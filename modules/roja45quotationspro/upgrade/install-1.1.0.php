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

function upgrade_module_1_1_0($module)
{
    $return = true;

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_request` (
        `id_roja45_quotation_request` int(10) unsigned NOT NULL auto_increment,
        `id_shop` int(10),
        `id_currency` int(10),
        `id_customer` int(10),
        `id_guest` int(10),
        `id_lang` int(10),
        `secure_key` varchar(32),
        `date_add` datetime,
        `date_upd` datetime,
        PRIMARY KEY (`id_roja45_quotation_request`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct` (
        `id_roja45_quotation_requestproduct` int(10) unsigned NOT NULL auto_increment,
        `id_roja45_quotation_request` int(10),
        `id_shop` int(10),
        `id_product` int(10),
        `id_product_attribute` int(10),
        `qty` int(10),
        `date_add` datetime,
        PRIMARY KEY (`id_roja45_quotation_requestproduct`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $def_states = array(
        array(
            'code' => QuotationStatus::$NWQT,
            'color' => '#FF0000',
            'unremovable' => 1,
            'send_email' => 0,
            'notify_admin' => 0,
            'name' => 'New Quotation',
            'answer_template' => 'customer_order_request'
        ),
    );

    foreach ($def_states as $state) {
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_status',
            array(
                'color' => pSQL($state['color']),
                'code' => pSQL($state['code']),
                'unremovable' => (int)$state['unremovable'],
                'send_email' => (int)$state['send_email'],
                'notify_admin' => (int)$state['notify_admin'],
                'answer_template' => pSQL($state['answer_template']),
            )
        );
        $id_status = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_status_lang',
                array(
                    'id_roja45_quotation_status' => (int)$id_status,
                    'id_lang' => (int)$language['id_lang'],
                    'status' => pSQL(RojaFortyFiveQuotationsProCore::getLocalTranslation(
                        $module,
                        $state['code'],
                        $language
                    )),
                )
            );
        }
    }

    Configuration::updateValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART', 1);
    Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_USEAJAX', 1);
    Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLE_INVISIBLECAPTCHA', 0);
    $module->registerHook('displayNav');
    $module->registerHook('displayRoja45ProductList');
    $module->unregisterHook('displayProductListReviews');
    $module->registerHook('displayProductPriceBlock');

    return $return;
}
