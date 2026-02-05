<?php
// parametr wywołania: id_order musi, w przypadku tego skryptu, zawierać POSApplicationNumber (nie ma konwersji z id)
require_once dirname(__FILE__).'/../../../../../config/config.inc.php';
include_once dirname(__FILE__).'/../../../config/ehpcfg.php';
include_once dirname(__FILE__).'/../HybridClient/PSHClient.php';
include_once dirname(__FILE__).'/../../../sql/ScbDbUtil.php';

$pshLocation = Configuration::get('SANTANDERCREDIT_SVC_LOCATION');
$pshLogin = Configuration::get('SANTANDERCREDIT_PSH_LOGIN');
$pshPass = Configuration::get('SANTANDERCREDIT_PSH_PASS');
$pshCrt = Configuration::get('SANTANDERCREDIT_CRT_FILE');
$shopNumber = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));	
$pshCrt = dirname(__FILE__).'/../../../CERT/' . trim($pshCrt);
$oid = Tools::getValue('id_order');
$shopAppNr = $oid;
$responseJson = '{"isOk":"false"}';

$client = new PSHClient($pshLogin, $pshPass, $pshLocation, $pshCrt, $shopNumber);
if($client->isActive()) {   
    $client->checkAppStatus([$shopAppNr]);
    if($client->isCorrect and is_object($client->applications) and $client->applications->count() == 1){
        //log request, response and perform notification        
        if(isset($client->applications[0]->ApplicationNumber)) {
            ScbDbUtil::RegisterSuccessfullPshResponse($shopAppNr, $client->operationStatus, $client->applications[0]);                    
            $responseJson = '{"isOk":"true"}';
        } else {
            // response formalnie poprawny ale brak wniosku
            // ScbDbUtil::LogIncorrectPshResponse($shopAppNr, $client->operationStatus, $client->applications[0]->CreditState);
            ScbDbUtil::RegisterSuccessfullPshResponse($shopAppNr, $client->operationStatus, $client->applications[0]);
            $responseJson = '{"isOk":"true"}';
        }
    } else{
        // bad request - log it
        ScbDbUtil::LogIncorrectPshResponse($shopAppNr, $client->operationStatus, $client->lastException);
    }
}
echo $responseJson;