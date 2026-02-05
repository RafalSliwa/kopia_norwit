<?php
/**
* 2012-2020 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Opineo Opinie Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
*  @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
*  @copyright 2012-2020 Patryk Marek - PrestaDev.pl
*  @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it)
*  @link      http://prestadev.pl
*  @package   PD Opineo Opinie Pro - PrestaShop 1.6.x & 1.7.x Module
*  @version   1.4.6
*  @date      06-06-2017
*/

class PdGetDataByVatnumberProAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        $module = new pdgetdatabyvatnumberpro();
        if (Tools::getValue('secure_key') == $module->secure_key) {
            $nip = Tools::getValue('nip');
            $nip = preg_replace('/[^a-zA-z0-9]/', '', $nip);
            $id_country = Tools::getValue('id_country');
            $data = array(
               'data' => $module->callApiByNip($nip, $id_country)
            );

            die(json_encode($data));
        }
    }
}
