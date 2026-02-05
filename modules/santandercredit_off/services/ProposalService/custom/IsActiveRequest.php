<?php
// parametr wywołania: id_order
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

$client = new PSHClient($pshLogin, $pshPass, $pshLocation, $pshCrt, $shopNumber);
if($client->isActive()) {   
    echo 'OK';
} else {
    echo 'Błąd';
}
