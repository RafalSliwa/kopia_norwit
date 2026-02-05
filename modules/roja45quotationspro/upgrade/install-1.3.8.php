<?php
/**
 * upgrade_module_1_3_8
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_8
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_8.
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

function upgrade_module_1_3_8($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;
    $select = Db::getInstance()->getValue(
        'SELECT id_meta 
        FROM `' . _DB_PREFIX_ . 'meta` 
        WHERE page ="module-roja45quotationspro-QuotationsProFront"'
    );
    if (!$select) {
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'meta` (`id_meta`, `page`, `configurable`) 
            VALUES (NULL, "module-roja45quotationspro-QuotationsProFront", 1)'
        );
        $id_meta = Db::getInstance()->Insert_ID();
        $id_shop = Context::getContext()->shop->id;
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'meta_lang',
                array(
                    'id_meta' => (int) $id_meta,
                    'id_shop' => (int) $id_shop,
                    'id_lang' => (int) $language['id_lang'],
                    'title' => pSQL(''),
                    'description' => pSQL(''),
                    'keywords' => pSQL(''),
                    'url_rewrite' => pSQL('')
                )
            );
        }
    }
    return $return;
}
