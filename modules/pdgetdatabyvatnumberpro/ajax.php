<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - Pd Get data by vat number Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   Pd Get data by vat number Pro for - PrestaShop 1.5.x and 1.6.x and 1.7.x
* @version   1.0.2
* @date      7-06-2018
*/

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once(dirname(__FILE__).'/pdgetdatabyvatnumberpro.php');

$module = new pdgetdatabyvatnumberpro();

if (Tools::getValue('secure_key') == $module->secure_key) {
    $nip = Tools::getValue('nip');
    $nip = preg_replace('/[^a-zA-z0-9]/', '', $nip);
    $nip_country_iso = Tools::getValue('nip_country_iso');
    $data = array(
       'data' => $module->callApiByNipAndCountryIso($nip, $nip_country_iso),
    );

    die(json_encode($data));
}
