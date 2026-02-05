<?php
/**
 * upgrade_module_1_5_1
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_1
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_1
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

function upgrade_module_1_5_1($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template` (
        `id_roja45_quotation_template` int(10) unsigned NOT NULL auto_increment,
        `id_lang` int(10),
        `id_shop` int(10),
        `id_currency` int(10),
        `id_carrier` int(10),
        `calculate_taxes` tinyint(1) NOT NULL DEFAULT \'0\',
        `template_name` varchar(255),
        `date_add` DATETIME NOT NULL,
        `date_upd` DATETIME NOT NULL,
        PRIMARY KEY (`id_roja45_quotation_template`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
      CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template_product` (
      `id_roja45_quotation_template_product` int(10) unsigned NOT NULL auto_increment,
      `id_roja45_quotation_template` int(10) unsigned NOT NULL,
      `id_product` int(10),
      `id_product_attribute` int(10),
      `product_title` varchar(255),
      `comment` varchar(1000),
      `qty` int(10),
      `unit_price_tax_excl` decimal(20,6),
      `unit_price_tax_incl` decimal(20,6),
      `deposit_amount` double(20,6) NOT NULL DEFAULT \'100.0\',
      `custom_price` tinyint(1) NOT NULL DEFAULT \'0\',
      `date_add` DATETIME NOT NULL,
      `date_upd` DATETIME NOT NULL,
      PRIMARY KEY (`id_roja45_quotation_template_product`)
    ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template_charge` (
        `id_roja45_quotation_template_charge` int(10) unsigned NOT NULL auto_increment,
        `id_roja45_quotation_template` int(10),
        `charge_name` varchar(255),
        `charge_type` varchar(255),
        `charge_method` varchar(255),
        `charge_value` decimal(20,6),
        `charge_amount` decimal(20,6),
        `charge_amount_wt` decimal(20,6),
        `specific_product` tinyint(1) NOT NULL DEFAULT \'0\',
        `id_roja45_quotation_product` int(10),
        `id_cart_rule` int(10),
        PRIMARY KEY (`id_roja45_quotation_template_charge`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = new DbQuery();
    $sql->select('q.id_roja45_quotation');
    $sql->from('roja45_quotationspro', 'q');
    $sql->where('q.is_template=1');
    if ($templates = Db::getInstance()->executeS($sql)) {
        foreach ($templates as $template) {
            $quotation = new RojaQuotation($template['id_roja45_quotation']);
            if (Validate::isLoadedObject($quotation)) {
                $template = new RojaQuotationTemplate();
                $template->id_lang = $quotation->id_lang;
                $template->id_shop = $quotation->id_shop;
                $template->id_currency = $quotation->id_currency;
                $template->id_employee = 0;
                $template->calculate_taxes = $quotation->calculate_taxes;
                $template->template_name = $quotation->template_name;
                if ($template->add()) {
                    foreach ($quotation->getProducts() as $quotation_product) {
                        $quotation_product = new QuotationProduct($quotation_product['id_roja45_quotation_product']);

                        $quotation_template_product = new RojaQuotationTemplateProduct();
                        $quotation_template_product->id_roja45_quotation_template = $template->id;
                        $quotation_template_product->id_product = $quotation_product->id_product;
                        $quotation_template_product->id_product_attribute = $quotation_product->id_product_attribute;
                        $quotation_template_product->product_title = $quotation_product->product_title;
                        $quotation_template_product->qty = $quotation_product->qty;
                        $quotation_template_product->comment = $quotation_product->comment;
                        $quotation_template_product->unit_price_tax_excl = $quotation_product->unit_price_tax_excl;
                        $quotation_template_product->unit_price_tax_incl = $quotation_product->unit_price_tax_incl;
                        $quotation_template_product->deposit_amount = $quotation_product->deposit_amount;
                        $quotation_template_product->custom_price = $quotation_product->custom_price;
                        $quotation_template_product->add();
                        $quotation_product->delete();
                    }

                    foreach ($quotation->getQuotationAllCharges() as $charge) {
                        $charge = new QuotationCharge($charge['id_roja45_quotation_charge']);

                        $template_charge = new RojaQuotationTemplateCharge();
                        $template_charge->id_roja45_quotation_template = $template->id;
                        $template_charge->charge_name = $charge->charge_name;
                        $template_charge->charge_type = $charge->charge_type;
                        $template_charge->charge_method = $charge->charge_method;
                        $template_charge->charge_value = $charge->charge_value;
                        $template_charge->charge_amount = $charge->charge_amount;
                        $template_charge->charge_amount_wt = $charge->charge_amount_wt;
                        $template_charge->specific_product = $charge->specific_product;
                        $template_charge->id_roja45_quotation_product = $charge->id_roja45_quotation_product;
                        $template_charge->id_cart_rule = $charge->id_cart_rule;
                        $template_charge->add();
                        $charge->delete();
                    }
                    foreach ($quotation->getQuotationAllDiscounts() as $discount) {
                        $discount = new QuotationCharge($discount['id_roja45_quotation_charge']);

                        $template_discount = new RojaQuotationTemplateCharge();
                        $template_discount->id_roja45_quotation_template = $template->id;
                        $template_discount->charge_name = $discount->charge_name;
                        $template_discount->charge_type = $discount->charge_type;
                        $template_discount->charge_method = $discount->charge_method;
                        $template_discount->charge_value = $discount->charge_value;
                        $template_discount->charge_amount = $discount->charge_amount;
                        $template_discount->charge_amount_wt = $discount->charge_amount_wt;
                        $template_discount->specific_product = $discount->specific_product;
                        $template_discount->id_roja45_quotation_product = $discount->id_roja45_quotation_product;
                        $template_discount->id_cart_rule = $discount->id_cart_rule;
                        $template_discount->add();
                        $discount->delete();
                    }
                }
                $quotation->delete();
            }
        }
    }

    return $return;
}
