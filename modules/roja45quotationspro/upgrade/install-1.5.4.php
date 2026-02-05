<?php
/**
 * upgrade_module_1_5_4
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_4
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_4
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

function upgrade_module_1_5_4($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_customizationdata` (
        `id_roja45_quotation_customizationdata` int(10) unsigned NOT NULL auto_increment,
        `id_roja45_quotation_customization` int(10) unsigned NOT NULL,
        `type` tinyint(1) NOT NULL,
        `index` int(10) NOT NULL,
        `value` varchar(255) NOT NULL,
        `price` decimal(20,6),
        `weight` decimal(20,6),
        PRIMARY KEY (`id_roja45_quotation_customizationdata`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_customization`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_product') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_customization` 
            ADD `id_product` INT(10) NOT NULL AFTER `id_roja45_quotation_customization`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_customization` 
            ADD `id_product_attribute` INT(10) NOT NULL AFTER `id_product`';
        $return &= Db::getInstance()->execute($sql);

        // TODO - get the original id_customization, and copy them here
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_customization') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct` 
            ADD `id_customization` INT(10) NOT NULL DEFAULT 0 AFTER `id_product_attribute`';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_customization') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` 
            ADD `id_customization` INT(10) NOT NULL DEFAULT 0 AFTER `id_product_attribute`';
        $return &= Db::getInstance()->execute($sql);
    }

    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('roja45_quotationspro_product_customization');
    if ($rows = Db::getInstance()->executeS($sql)) {
        foreach ($rows as $row) {
            $quotation_product = new QuotationProduct($row['id_roja45_quotation_product']);
            $quotation = new RojaQuotation($quotation_product->id_roja45_quotation);

            $customization = new Customization($row['id_customization']);
            $quotation_customization = new QuotationCustomization();
            $quotation_customization->id_product = $quotation_product->id_product;
            $quotation_customization->id_product_attribute = $quotation_product->id_product_attribute;
            $quotation_customization->id_customization = $customization->id;
            $quotation_customization->save();

            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('customized_data');
            $sql->where('id_customization=' . (int) $row['id_customization']);
            if ($datas = Db::getInstance()->executeS($sql)) {
                foreach ($datas as $data) {
                    $quotation_customization->addCustomizationData(
                        $quotation_customization->id,
                        $data['type'],
                        $data['index'],
                        $data['value'],
                        $data['price'],
                        $data['weight']
                    );
                }
            }

            $sql = 'UPDATE '._DB_PREFIX_.'roja45_quotationspro_requestproduct rp 
                SET rp.id_customization='.(int) $quotation_customization->id . '
                WHERE rp.id_roja45_quotation_request='.(int) $quotation->id_request . '
                AND rp.id_product=' . (int) $quotation_product->id_product . '
                AND rp.id_product_attribute=' . (int) $quotation_product->id_product_attribute;
            Db::getInstance()->execute($sql);

            $quotation_product->id_customization = $quotation_customization->id;
            $quotation_product->save();
        }
    }

    $custom_template_dir = _PS_MODULE_DIR_.$module->name.'/views/templates/admin/custom/';
    $language = new Language(Configuration::get('PS_LANG_DEFAULT'));
    if (!file_exists($custom_template_dir.'pdf_request_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'pdf_request_en.tpl',
            $custom_template_dir.'pdf_request_'.$language->iso_code.'.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'pdf_quotation_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'pdf_quotation_en.tpl',
            $custom_template_dir.'pdf_quotation_'.$language->iso_code.'.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_blank_template_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_blank_template_en.tpl',
            $custom_template_dir.'mail_blank_template_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_blank_template_en-txt.tpl',
            $custom_template_dir.'mail_blank_template_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_customer_request_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_customer_request_en.tpl',
            $custom_template_dir.'mail_customer_request_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_customer_request_en-txt.tpl',
            $custom_template_dir.'mail_customer_request_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_admin_request_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_admin_request_en.tpl',
            $custom_template_dir.'mail_admin_request_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_admin_request_en-txt.tpl',
            $custom_template_dir.'mail_admin_request_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_send_quote_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_send_quote_en.tpl',
            $custom_template_dir.'mail_send_quote_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_send_quote_en-txt.tpl',
            $custom_template_dir.'mail_send_quote_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_notify_admin_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_notify_admin_en.tpl',
            $custom_template_dir.'mail_notify_admin_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_notify_admin_en-txt.tpl',
            $custom_template_dir.'mail_notify_admin_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_thank_you_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_thank_you_en.tpl',
            $custom_template_dir.'mail_thank_you_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_thank_you_en-txt.tpl',
            $custom_template_dir.'mail_thank_you_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_message_received_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_message_received_en.tpl',
            $custom_template_dir.'mail_message_received_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_message_received_en-txt.tpl',
            $custom_template_dir.'mail_message_received_'.$language->iso_code.'-txt.tpl'
        );
    }
    if (!file_exists($custom_template_dir.'mail_customer_order_request_'.$language->iso_code.'.tpl')) {
        copy(
            $custom_template_dir.'mail_customer_order_request_en.tpl',
            $custom_template_dir.'mail_customer_order_request_'.$language->iso_code.'.tpl'
        );
        copy(
            $custom_template_dir.'mail_customer_order_request_en-txt.tpl',
            $custom_template_dir.'mail_customer_order_request_'.$language->iso_code.'-txt.tpl'
        );
    }
    return $return;
}
