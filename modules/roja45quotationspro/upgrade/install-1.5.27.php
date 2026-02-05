<?php
/**
 * upgrade_module_1_5_27
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_27
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_27
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

function upgrade_module_1_5_27($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_status_lang`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'display_code') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status_lang` 
            ADD `display_code` varchar(255) AFTER `status`';
        $return &= Db::getInstance()->execute($sql);
    }
    $statuses = QuotationStatus::getQuotationStates(Context::getCOntext()->language->id);
    foreach ($statuses as $status) {
        $status = new QuotationStatus($status['id_roja45_quotation_status']);
        foreach (Language::getLanguages(false) as $language) {
            $status->display_code[$language['id_lang']] = $status->code;
        }
        $status->save();
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_status`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'customer_pdf_ids') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status` 
            ADD `customer_pdf_ids` VARCHAR(255) AFTER `id_roja45_quotation_answer_admin`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_status` 
            ADD `admin_pdf_ids` VARCHAR(255) AFTER `customer_pdf_ids`';
        $return &= Db::getInstance()->execute($sql);

        $id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType(QuotationStatus::$OPEN);
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->customer_pdf_ids = '1';
        $status->admin_pdf_ids = '1';
        foreach (Language::getLanguages(false) as $language) {
            $status->display_code[$language['id_lang']] = $status->code;
        }
        $status->save();
        $id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType(QuotationStatus::$SENT);
        $status = new QuotationStatus($id_roja45_quotation_status);
        $status->customer_pdf_ids = '2';
        $status->admin_pdf_ids = '';
        foreach (Language::getLanguages(false) as $language) {
            $status->display_code[$language['id_lang']] = $status->code;
        }
        $status->save();
    }
    return $return;
}