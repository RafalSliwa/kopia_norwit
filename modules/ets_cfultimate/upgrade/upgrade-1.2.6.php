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

function upgrade_module_1_2_6()
{
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` ADD `mailchimp_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `position`; ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` ADD `mailchimp_api_key` VARCHAR(255) NOT NULL AFTER `mailchimp_enabled`; ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` ADD `mailchimp_audience` VARCHAR(128) NOT NULL AFTER `mailchimp_api_key`;  ');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` ADD `mailchimp_mapping_data` text DEFAULT NULL AFTER `mailchimp_audience`;  ');

    return true;
}