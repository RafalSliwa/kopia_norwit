<?php
/**
 * upgrade_module_1_2_23
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_2_23
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_2_23.
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

function upgrade_module_1_2_23($module)
{
    $return = true;
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_order` (
        `id_roja45_quotation_order` int(10) unsigned NOT NULL auto_increment,
        `id_roja45_quotation` int(10) unsigned NOT NULL,
        `id_order` int(10) unsigned NOT NULL,
        `date_add` datetime,
        `date_upd` datetime,
        PRIMARY KEY (`id_roja45_quotation_order`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        SELECT *
        FROM `' . _DB_PREFIX_ . 'roja45_quotationspro` q';
    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    foreach ($results as $row) {
        if (!empty($row['id_order']) && !QuotationOrder::exists($row['id_roja45_quotation'], $row['id_order'])) {
            $quotation_order = new QuotationOrder();
            $quotation_order->id_roja45_quotation = $row['id_roja45_quotation'];
            $quotation_order->id_order = $row['id_order'];
            $quotation_order->add();
        }
    }
    return $return;
}
