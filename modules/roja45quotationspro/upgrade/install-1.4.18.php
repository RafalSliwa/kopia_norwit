<?php
/**
 * upgrade_module_1_4_18
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_4_18
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_4_18
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

function upgrade_module_1_4_18($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_customization` (
        `id_roja45_quotation_customization` int(10) unsigned NOT NULL auto_increment,
        `id_customization` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_roja45_quotation_customization`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = new DbQuery();
    $sql->select('id_customization');
    $sql->from('roja45_quotationspro_requestproduct_customization', 'rpc');
    if ($rows = Db::getInstance()->executeS($sql)) {
        foreach ($rows as $row) {
            Db::getInstance()->insert(
                'roja45_quotationspro_customization',
                array(
                    'id_roja45_quotation_customization' => $row['id_customization'],
                    'id_customization' => $row['id_customization'],
                )
            );
        }
    }

    Configuration::updateGlobalValue(
        'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT',
        1
    );

    return $return;
}
