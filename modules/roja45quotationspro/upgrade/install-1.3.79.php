<?php
/**
 * install-1.3.79
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
 * install-1.3.79.
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

function upgrade_module_1_3_79($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = 'SHOW INDEX FROM '._DB_PREFIX_.'product_quotationspro;';
    $results = Db::getInstance()->executeS($sql);
    if (is_array($results)) {
        $sql = 'DROP INDEX id_product ON '._DB_PREFIX_.'product_quotationspro;';
        Db::getInstance()->execute($sql);
        $sql = 'CREATE INDEX id_product ON '._DB_PREFIX_.'product_quotationspro (id_product);';
        Db::getInstance()->execute($sql);
    }
    if (!is_array($results)) {
        $sql = 'CREATE INDEX id_product ON '._DB_PREFIX_.'product_quotationspro (id_product);';
        Db::getInstance()->execute($sql);
    }
    return $return;
}
