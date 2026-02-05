<?php
require_once dirname(__FILE__).'/../../../../../config/config.inc.php';
include_once dirname(__FILE__).'/../../../config/ehpcfg.php';
include_once dirname(__FILE__).'/../HybridClient/PSHClient.php';
include_once dirname(__FILE__).'/../../../sql/ScbDbUtil.php';
    /**
     * Sprawdzanie co 5 minut, wysyłany request max 20 wniosków w kolejności check_date
    */

$pshLocation = Configuration::get('SANTANDERCREDIT_SVC_LOCATION');
$pshLogin = Configuration::get('SANTANDERCREDIT_PSH_LOGIN');
$pshPass = Configuration::get('SANTANDERCREDIT_PSH_PASS');
$pshCrt = Configuration::get('SANTANDERCREDIT_CRT_FILE');
$shopNumber = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));	
$pshCrt = dirname(__FILE__).'/../../../CERT/' . trim($pshCrt);
// select top 20 wniosków (wszystkie niezakończone w kolejności check_date)
$ordersArray = ScbDbUtil::getOrders2check();
$client = new PSHClient($pshLogin, $pshPass, $pshLocation, $pshCrt, $shopNumber);
if(count($ordersArray) > 0 and $client->isActive()) {   
    // buduj request
    // getApplicationState    
    $client->checkAppStatus($ordersArray);
    // oznaczenie daty synchronizacji statusu na sprawdzanych wnioskach
    ScbDbUtil::updateCheckDate($ordersArray);    
    // przetwarzanie odpowiedzi w pętli
    ScbDbUtil::updateAppStates($client->applications, $client->operationStatus);
}