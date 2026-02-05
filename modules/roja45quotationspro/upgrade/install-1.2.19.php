<?php
/**
 * upgrade_module_1_2_17
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_2_17
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_2_17.
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

function upgrade_module_1_2_19($module)
{
    $return = true;
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'product_quotationspro`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='id_roja45_product_quotation') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `'._DB_PREFIX_.'product_quotationspro` ADD COLUMN `id_roja45_product_quotation` INT(10)';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_quotationspro';
        $counter = 1;
        foreach (Db::getInstance()->executeS($sql) as $row) {
            $sql = '
                UPDATE ' . _DB_PREFIX_ . 'product_quotationspro 
                SET id_roja45_product_quotation = '. (int) $counter. ' 
                WHERE id_product='. (int) $row['id_product'];
            Db::getInstance()->execute($sql);
            $counter++;
        }

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'product_quotationspro SET id_shop = '. (int) Shop::getContext();
        Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_quotationspro`  
            CHANGE `id_product` `id_product` INT(10) UNSIGNED NOT NULL';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_quotationspro` ADD COLUMN `id_shop`  INT(10)';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_quotationspro` 
            DROP PRIMARY KEY, ADD PRIMARY KEY (`id_roja45_product_quotation`) USING BTREE;';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'product_quotationspro` 
            CHANGE `id_roja45_product_quotation` `id_roja45_product_quotation`
            INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;';
        $return &= Db::getInstance()->execute($sql);
    }

    return $return;
}
