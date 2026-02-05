<?php
/**
 * upgrade_module_1_5_14
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_14
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_14
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

function upgrade_module_1_5_14($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_profile') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` 
            ADD `id_profile` INT(10) NULL AFTER `id_employee`';
        $return &= Db::getInstance()->execute($sql);
    }

    $sql = new DbQuery();
    $sql->select('*');
    $sql->from('roja45_quotationspro', 'q');
    if ($rows = Db::getInstance()->executeS($sql)) {
        foreach ($rows as $row) {
            $quotation = new RojaQuotation($row['id_roja45_quotation']);
            if (Validate::isLoadedObject($quotation)) {
                $employee = new Employee($row['id_employee']);
                if (Validate::isLoadedObject($employee)) {
                    $quotation->id_profile = $employee->id_profile;
                } else {
                    $quotation->id_profile = _PS_ADMIN_PROFILE_;
                }
                $quotation->save();
            }
        }
    }

    if ($id_roja45_status = (int) Configuration::get(
        'ROJA45_QUOTATIONSPRO_STATUS_SENT'
    )) {
        $status = new QuotationStatus($id_roja45_status);
        if (isset($status->id_roja45_quotation_answer)) {
            $quotation_answer = new QuotationAnswer(
                $status->id_roja45_quotation_answer
            );
            foreach (Language::getLanguages(false) as $language) {
                $quotation_answer->subject[$language['id_lang']] = 'Quotation [%1$s] : [#ct%2$s] : [#tc%3$s]';
                $quotation_answer->name[$language['id_lang']] = 'Send Customer Quote Email';
            }
            $quotation_answer->save();
        }
    }

    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG',
        0
    );

    return $return;
}
