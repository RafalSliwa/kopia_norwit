<?php
/**
 * upgrade_module_1_3_67
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_67
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_67.
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

function upgrade_module_1_3_67($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct_customization` (
            `id_roja45_quotation_requestproduct_customization` int(10) unsigned NOT NULL auto_increment,
            `id_roja45_quotation_requestproduct` int(10) unsigned NOT NULL,
            `id_customization` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_roja45_quotation_requestproduct_customization`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_product_customization` (
            `id_roja45_quotation_product_customization` int(10) unsigned NOT NULL auto_increment,
            `id_roja45_quotation_product` int(10) unsigned NOT NULL,
            `id_customization` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_roja45_quotation_product_customization`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    return $return;
}
