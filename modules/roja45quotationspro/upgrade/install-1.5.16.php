<?php
/**
 * upgrade_module_1_5_16
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_5_16
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_5_16
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

function upgrade_module_1_5_16($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_roja45_document') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document` 
            ADD `id_roja45_document` INT(10) NULL AFTER `id_roja45_quotation`';
        $return &= Db::getInstance()->execute($sql);
    }

    if ($documents = QuotationDocument::getDocuments()) {
        foreach ($documents as $document) {
            $source_file = _PS_MODULE_DIR_.'roja45quotationspro'.
                DIRECTORY_SEPARATOR.'documents'.
                DIRECTORY_SEPARATOR.$document['internal_name'];
            $target_file = _PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$document['internal_name'];
            if (file_exists($source_file)) {
                rename($source_file, $target_file);
            }
        }
    }

    if ($quotations = RojaQuotation::getQuotations()) {
        foreach ($quotations as $quotation) {
            $quotation = new RojaQuotation($quotation['id_roja45_quotation']);
            if (isset($quotation->filename)) {
                $filename = sha1($quotation->email . $quotation->filename . $quotation->id_request);
                if (!file_exists(_PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$quotation->reference)) {
                    mkdir(_PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$quotation->reference, 0777, true);
                }
                $source_file = _PS_DOWNLOAD_DIR_.$filename;
                $target_file = _PS_DOWNLOAD_DIR_.'roja45quotationspro'.
                    DIRECTORY_SEPARATOR.$quotation->reference.DIRECTORY_SEPARATOR.$filename;

                $type = pathinfo($quotation->filename, PATHINFO_EXTENSION);
                if (file_exists($source_file)) {
                    rename($source_file, $target_file);
                    $quotation->addDocument(
                        $quotation->filename,
                        $filename,
                        $filename,
                        $type
                    );
                }
            }

            if ($quotation_documents = $quotation->getDocuments()) {
                if (!file_exists(_PS_MODULE_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$quotation->reference)) {
                    mkdir(_PS_MODULE_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$quotation->reference, 0777, true);
                }
                foreach ($quotation_documents as $document) {
                    $source_file = _PS_MODULE_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$document['internal_name'];
                    $target_file = _PS_DOWNLOAD_DIR_.'roja45quotationspro'.
                        DIRECTORY_SEPARATOR.$quotation->reference.DIRECTORY_SEPARATOR.$document['internal_name'];
                    if (file_exists($source_file)) {
                        rename($source_file, $target_file);
                    }
                }
            }
        }
    }

    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD',
        0
    );

    return $return;
}
