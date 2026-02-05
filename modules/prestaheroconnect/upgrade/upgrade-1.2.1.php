<?php
/**
 * 2007-2021 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2021 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_1_2_1()
{
    try{
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ph_con_employee_token` ADD `token_social_expire_at` DATETIME NULL DEFAULT NULL AFTER `token_expire_at`');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ph_con_employee_token` ADD `token_social` varchar(255) DEFAULT NULL AFTER `token`');
    }
    catch (Exception $e)
    {
        if($e)
        {
            //
        }
    }
    return true;
}