<?php
/**
 * upgrade_module_1_5_2
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_2
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_2
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

function upgrade_module_1_5_2($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_address_invoice') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_invoice` INT(10) NULL AFTER `id_address`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_delivery` INT(10) NULL AFTER `id_address_invoice`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_address_tax` INT(10) NULL DEFAULT \'21\' AFTER `id_address_delivery`';
        $return &= Db::getInstance()->execute($sql);

        if ($quotations = RojaQuotation::getQuotations()) {
            foreach ($quotations as $quotation) {
                $quotationObj = new RojaQuotation($quotation['id_roja45_quotation']);
                $quotationObj->id_address_delivery = $quotation['id_address'];
                $quotationObj->id_address_invoice = $quotation['id_address'];
                $quotationObj->id_address_tax = RojaQuotation::TAX_INVOICE_ADDRESS;
                $quotationObj->save();
            }
        }

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` DROP `id_address`';
        $return &= Db::getInstance()->execute($sql);
    }

    return $return;
}
