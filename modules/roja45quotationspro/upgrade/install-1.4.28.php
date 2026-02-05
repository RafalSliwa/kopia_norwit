<?php
/**
 * upgrade_module_1_4_28
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_4_28
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_4_28
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Function
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_28($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
        CHANGE `filename` `filename` VARCHAR(255) NULL;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
        CHANGE `tmp_password` `tmp_password` VARCHAR(255) NULL;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
        CHANGE `purchase_date` `purchase_date` DATETIME NULL;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
        CHANGE `quote_name` `quote_name` VARCHAR(255) NULL;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
        CHANGE `template_name` `template_name` VARCHAR(255) NULL;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro`
        CHANGE `is_template` `is_template` TINYINT(1) NULL DEFAULT \'0\';';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
        CHANGE `deposit_amount` `deposit_amount` DOUBLE(20,6) UNSIGNED NOT NULL DEFAULT \'100.00\';';
    $return &= (bool)Db::getInstance()->execute($sql);

    return $return;
}
