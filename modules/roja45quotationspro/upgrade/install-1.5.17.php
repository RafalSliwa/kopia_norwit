<?php
/**
 * upgrade_module_1_5_17
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_17
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_17
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

function upgrade_module_1_5_17($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_charge`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'charge_default') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_charge` 
            ADD `charge_default` TINYINT(1) AFTER `charge_method`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_charge` 
            ADD `charge_handling` DOUBLE(20,6) NULL AFTER `charge_amount_wt`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_charge` 
            ADD `charge_handling_wt` DOUBLE(20,6) NULL AFTER `charge_handling`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_charge` 
            ADD `id_carrier` INT(10) UNSIGNED NULL AFTER `id_cart_rule`';
        $return &= Db::getInstance()->execute($sql);
    }

    if ($quotations = RojaQuotation::getQuotations()) {
        foreach ($quotations as $quotation) {
            $quotation = new RojaQuotation($quotation['id_roja45_quotation']);
            $charges = $quotation->getQuotationChargeList(QuotationCharge::$SHIPPING);
            foreach ($charges as $charge) {
                $carrier = new Carrier($quotation->id_carrier);
                $address = $quotation->getTaxAddress();
                $carrier_tax = $carrier->getTaxesRate($address);

                $charge = new QuotationCharge($charge['id_roja45_quotation_charge']);
                $charge->charge_default = 1;
                $charge->id_carrier = $quotation->id_carrier;
                if ($carrier->shipping_handling) {
                    $charge->charge_handling = Configuration::get('PS_SHIPPING_HANDLING');
                    $charge->charge_handling_wt = $charge->charge_handling * (1 + ($carrier_tax/100));
                }
                $charge->save();
            }

            $handling = $quotation->getQuotationChargeList(QuotationCharge::$HANDLING);
            foreach ($handling as $charge) {
                $charge = new QuotationCharge($charge['id_roja45_quotation_charge']);
                $charge->delete();
            }
        }
    }

    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION',
        0
    );
    return $return;
}
