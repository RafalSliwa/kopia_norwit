<?php
/**
 * upgrade_module_1_2_21
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_2_21
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_2_21.
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

function upgrade_module_1_2_21($module)
{
    $return = true;
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'roja45_quotationspro_product`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='comment') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` ADD COLUMN `comment`  VARCHAR(1000)';
        $return &= Db::getInstance()->execute($sql);
    }

    return $return;
}
