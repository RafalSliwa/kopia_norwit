<?php
/**
 * upgrade_module_1_1_7
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_1_7
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_1_7.
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

function upgrade_module_1_2_5($module)
{
    $return = true;
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'roja45_quotationspro`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='filename') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `id_request`  INT(10)';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `filename` VARCHAR(255)';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'roja45_quotationspro_request`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='form_data') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_request` ADD COLUMN `form_data` text';
        $return &= Db::getInstance()->execute($sql);
    }

    return $return;
}
