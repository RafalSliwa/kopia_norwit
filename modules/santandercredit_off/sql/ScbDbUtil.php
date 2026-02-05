<?php

use PhpParser\Node\Stmt\TryCatch;

require_once dirname(__FILE__) . '/../../../config/config.inc.php';
include_once dirname(__FILE__) . '/../services/ProposalService/HybridClient/Application.php';
include_once dirname(__FILE__) . '/../services/ProposalService/HybridClient/OperationStatus.php';
/**
 * Source Application Number to id_order converter
 * 
 */
class ScbDbUtil {

    public static function getIdOrder($ShopApplicationNumber) {
        $query = 'SELECT id_order FROM ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping where pos_app_number = \''.$ShopApplicationNumber. '\'';
        $idOrder = Db::getInstance()->getValue($query);
        if($idOrder){
            return $idOrder;
        }
        return -1;
    }
    
    public static function getShopApplicationNumber($IdOrder){
        $query = 'SELECT pos_app_number FROM ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping where id_order = '.$IdOrder;
        $san = Db::getInstance()->getValue($query);
        return $san;
    }

    public static function RegisterSuccessfullPshResponse($shopAppNr, $operationStatus, $application) {         
        $shop = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));
        $numbers2check = [];
        $cnt = count(CHECK_THIS_STATES);
        for($i = 0; $i < $cnt; $i++) {
            array_push($numbers2check, Configuration::get(EHP_ORDER_STATE_PREFIX . CHECK_THIS_STATES[$i]));
        }
        $token = '';        
        $orderObj = new Order(ScbDbUtil::getIdOrder($shopAppNr));
        $currentOrderState = $orderObj->getCurrentOrderState();
        $newOrderState = ScbDbUtil::mapCreditStateToOrderState($application->CreditState, $currentOrderState); 
        $checkIt = '0';
        $cnt = count($numbers2check);
        for($i = 0; $i < $cnt; $i++){
            if($newOrderState == $numbers2check[$i]){
                $checkIt = '1';
                break;
            }
        }
        $request_date = date("Y-m-d H:i:s");
        $agreementDate = null;
        $appStatusChgDate = null;
        if(isset($application->AgreementDate)){
            $agreementDate = date("Y-m-d H:i:s",strtotime($application->AgreementDate));            
        }
        if(isset($application->ChangeDate)){
            $appStatusChgDate = date("Y-m-d H:i:s",strtotime($application->ChangeDate));
        }
        $sql = [
            'INSERT INTO ' . _DB_PREFIX_ . 'scb_ehp_log (
                id_order,
                pos_app_number,
                application_number,
                agreement_number,
                agreement_date,
                shop_number,
                ehp_token,
                request_date,
                success,
                application_status,
                app_status_chg_date,
                downpayment,
                total_price
            ) VALUES ('
            .ScbDbUtil::getIdOrder($shopAppNr) . ',\''
            .$shopAppNr . '\',\''
            .$application->ApplicationNumber . '\',\''
            .$application->AgreementNumber . '\',\''
            .$agreementDate . '\',\''
            .$shop . '\',\''
            .$token . '\',\''
            .$request_date . '\','
            .'1,\''
            .$application->CreditState . '\',\''
            .$appStatusChgDate . '\','
            .$application->Downpayment . ','
            .$application->TotalPrice . ');'
            ,'UPDATE ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping SET '
            .'application_number = \'' . $application->ApplicationNumber . '\', '
            .'agreement_number = \'' . $application->AgreementNumber . '\', '
            .'agreement_date = \'' . $agreementDate . '\', '
            .'ehp_token = \'' . $token . '\', '
            .'order_status = \'' . $newOrderState . '\', ' 
            .'application_status = \'' . $application->CreditState . '\', ' 
            .'app_status_chg_date = \'' . $appStatusChgDate . '\', ' 
            .'downpayment = ' . $application->Downpayment . ','
            .'total_price = ' . $application->TotalPrice . ', '
            .'check_date = \'' . $request_date . '\', '
            .'check_it = ' . $checkIt
            .' WHERE pos_app_number = \'' . $shopAppNr .'\''
        ];    
        
        foreach ($sql as $query) {	
            Db::getInstance()->execute($query);
        }

        if ($currentOrderState->id <> $newOrderState) {
            $orderObj->setCurrentState($newOrderState);
            $orderObj->save();
        };        
    }

    public static function LogIncorrectPshResponse($shopAppNr, $operationStatus, $errorInfo) {
        if(isset($shopAppNr) and is_object($operationStatus) and is_string($errorInfo)) {            
            $shop = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));
            $request_date = date("Y-m-d H:i:s");
            $token = '';
            $sql = [
                'INSERT INTO ' . _DB_PREFIX_ . 'scb_ehp_log (
                    id_order,
                    pos_app_number,
                    shop_number,
                    ehp_token,
                    request_date,
                    success,
                    message
                ) VALUES ('
                .ScbDbUtil::getIdOrder($shopAppNr) . ',\''
                .$shopAppNr . '\',\''
                .$shop . '\',\''
                .$token . '\',\''
                .$request_date . '\','
                .'0,\'' . $errorInfo . '\')'                
            ]; 
            
            foreach ($sql as $query) {	
                Db::getInstance()->execute($query);
            }
                
        }
    }

    public static function mapCreditStateToOrderState($CreditState, $currentOrderState){        
        $currentOrderStateId = $currentOrderState->id;
        $newOrderState = $currentOrderStateId; 
        $luckyNumbers = [];
        foreach(EHP_APP_STATES as $key => $value) {
            $statNameNoPrefix = $key;
            $orderStateName = EHP_ORDER_STATE_PREFIX . $statNameNoPrefix;    
            $StateId = Configuration::get($orderStateName);
            array_push($luckyNumbers, $StateId);
        }
        $editable = false;
        $cnt = count($luckyNumbers);
        for($i = 0; $i < $cnt; $i++) {
            if($currentOrderStateId == $luckyNumbers[$i]) {
                $editable = true;
            }
        }
        if($editable){         
            if(array_key_exists($CreditState, BANK2SHOP_STATE_MAP)) {                
                $statNameNoPrefix = BANK2SHOP_STATE_MAP[$CreditState];
                $orderStateName = EHP_ORDER_STATE_PREFIX . $statNameNoPrefix;        
                $newOrderState = (int) Configuration::get($orderStateName);                    
            }        
        }
        return $newOrderState;
    }

    public static function getFullApplicationInfo($id_order){
        $app = new Application();
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping where id_order = '.$id_order;
        $result = Db::getInstance()->executeS($query);
        foreach($result as $row){
            $app->AgreementDate = $row['agreement_date'];
            $app->AgreementNumber = $row['agreement_number'];
            $app->ApplicationNumber = $row['application_number'];
            $app->ChangeDate = $row['app_status_chg_date'];
            $app->CreditState = $row['application_status'];
            $app->Downpayment = $row['downpayment'];
            $app->ShopApplicationNumber = $row['pos_app_number'];
            $app->ShopNumber = $row['shop_number'];
            $app->TotalPrice = $row['total_price'];
            $app->check_date = $row['check_date'];
        }
        return $app;
    }
   
    public static function getOrders2check(){
        $orderArray = [];
        $query = 'SELECT pos_app_number FROM ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping WHERE `date_add` > DATE_ADD(NOW(), INTERVAL -14 DAY) AND check_it = 1 order by check_date limit 20';
        $result = Db::getInstance()->executeS($query);
        foreach($result as $row){
            array_push($orderArray, $row['pos_app_number']);
        }
        return $orderArray;
    }

    public static function updateCheckDate($arrayOfOrders){
        $request_date = date("Y-m-d H:i:s");
        $query = 'UPDATE ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping SET ';
        $query = $query . ' check_date = \'' . $request_date . '\' WHERE pos_app_number in (';        
        $notEmpty = 0;
        foreach ($arrayOfOrders as $shopAppNumber) {
            $query = $query . '\'' . $shopAppNumber . '\',';
            $notEmpty = 1;
        }
        if($notEmpty > 0){
            $query = substr($query, 0, strlen($query) - 1);
            $query = $query . ')';
            Db::getInstance()->execute($query);
        }
    }

    public static function updateAppStates($apps, $opState){
        foreach ($apps as $key => $a) {
            ScbDbUtil::RegisterSuccessfullPshResponse($a->ShopApplicationNumber, $opState, $a);                    
        }
    }
}
