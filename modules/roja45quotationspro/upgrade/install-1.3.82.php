<?php
/**
 * upgrade_module_1_3_82
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  install-1.3.79
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_82.
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

function upgrade_module_1_3_82($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'product_quotationspro`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'id_shop') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'product_quotationspro` 
            ADD `id_shop` int(10) AFTER `id_product`';
        $return &= Db::getInstance()->execute($sql);
    }

    Db::getInstance()->execute(
        'UPDATE ' . _DB_PREFIX_ . 'product_quotationspro SET id_shop = ' . (int) Configuration::get('PS_SHOP_DEFAULT')
    );

    return $return;
}
