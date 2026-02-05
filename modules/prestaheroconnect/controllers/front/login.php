<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
class PrestaheroConnectLoginModuleFrontController extends ModuleFrontController
{
    public function init(){
        parent::init();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $token_social = Tools::getValue('token_social');
        $token = Tools::getValue('token');
        $id_customer = (int)Tools::getValue('id_customer');
        $id_employee = (int)Tools::getValue('id_employee');
        $tokenEmployee = PhConEmployeeToken::getToken($id_employee);
        $firstname = Tools::getValue('firstname');
        $lastname = Tools::getValue('lastname');
        $email = Tools::getValue('email');
        $remaining_lifetime = (int)Tools::getValue('remaining_lifetime');
        if($tokenEmployee && $token_social ==$tokenEmployee->token_social && strtotime($tokenEmployee->token_social_expire_at) > time() && $id_customer)
        {
            $tokenEmployee->token = $token;
            $tokenEmployee->token_social = null;
            $tokenEmployee->id_user = $id_customer;
            $tokenEmployee->firstname = $firstname && Validate::isCustomerName($firstname) ? $firstname :null;
            $tokenEmployee->lastname = $lastname && Validate::isCustomerName($lastname) ? $lastname : null;
            $tokenEmployee->email = $email && Validate::isEmail($email) ? $email :null;
            $tokenEmployee->token_expire_at = $remaining_lifetime > 0 ? date('Y-m-d H:i:s', $remaining_lifetime + time()) : null;
            $tokenEmployee->save(true);
            die(
                json_encode(
                    ['success' => true]
                )
            );
        }
        Tools::redirect($this->context->link->getPageLink('index'));
    }
}