<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-2024 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once("seoredirect.php");

$module = new seoredirect();

if (Tools::getValue('key') == $module->secure_key)
{
    $module->cronJob();
}
else
{
    echo "access denied";
}