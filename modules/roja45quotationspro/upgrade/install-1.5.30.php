<?php
/**
 * upgrade_module_1_5_30
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_30
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_30
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

function upgrade_module_1_5_30($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_tax_rules_group') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `id_tax_rules_group` INT(10) NULL AFTER `id_specific_price`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `tax_rate` DOUBLE(20,6) NULL AFTER `id_tax_rules_group`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'position') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `position` int(10) AFTER `id_shop`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'customization_cost_exc') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `customization_cost_exc` double(20,6) NOT NULL DEFAULT \'0\' AFTER `discount_type`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `customization_cost_inc` double(20,6) NOT NULL DEFAULT \'0\' AFTER `customization_cost_exc`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `customization_cost_type` varchar(255) NOT NULL DEFAULT \'1\' AFTER `customization_cost_inc`';
        $return &= Db::getInstance()->execute($sql);
    }

    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('roja45_quotationspro_product', 'rp');
    if ($rows = Db::getInstance()->executeS($sql)) {
        foreach ($rows as $row) {
            $quotation_product = new QuotationProduct($row['id_roja45_quotation_product']);
            $quotation = new RojaQuotation($row['id_roja45_quotation']);

            if (Validate::isLoadedObject($quotation_product)) {
                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                    (int) $row['id_product'],
                    Context::getContext()
                );
                $product_tax_calculator = TaxManagerFactory::getManager(
                    $quotation->getTaxAddress(),
                    $id_tax_rules_group
                )->getTaxCalculator();
                $tax_rate = $product_tax_calculator->getTotalRate();
                $quotation_product->id_tax_rules_group = $id_tax_rules_group;
                $quotation_product->tax_rate = $tax_rate;
                $quotation_product->position = 0;
                $quotation_product->save();
            }
        }
    }

    if ($quotations = RojaQuotation::getQuotations()) {
        foreach ($quotations as $quotation) {
            $quotation = new RojaQuotation($quotation['id_roja45_quotation']);
            if (Validate::isLoadedObject($quotation)) {
                $counter = 1;
                $sql = new DbQuery();
                $sql->select('*');
                $sql->from('roja45_quotationspro_product', 'qp');
                $sql->where('qp.`id_roja45_quotation` = ' . (int) $quotation->id_roja45_quotation);
                $sql->orderBy('qp.`date_add` ASC');
                if ($quotation_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                    foreach ($quotation_products as $quotation_product) {
                        $quotation_product = new QuotationProduct($quotation_product['id_roja45_quotation_product']);
                        $quotation_product->position = $counter;
                        $quotation_product->save();
                        $counter++;
                    }
                }
            }
        }
    }

    return $return;
}
