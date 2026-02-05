<?php
/**
 * upgrade_module_1_4_0
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_4_0
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_4_0.
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

function upgrade_module_1_4_0($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_document` (
        `id_roja45_document` int(10) unsigned NOT NULL auto_increment,
        `enabled` tinyint(1) NOT NULL,
        PRIMARY KEY (`id_roja45_document`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_document_lang` (
        `id_roja45_document` int(10) unsigned NOT NULL auto_increment,
        `id_lang` int(10) unsigned NOT NULL,
        `display_name` varchar(255),
        `file_name` varchar(255),
        `file_type` varchar(255),
        `internal_name` varchar(255),
        PRIMARY KEY (`id_roja45_document`,`id_lang`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document` (
        `id_roja45_quotation_document` int(10) unsigned NOT NULL auto_increment,
        `id_roja45_quotation` int(10) unsigned NOT NULL,
        `display_name` varchar(255),
        `file_type` varchar(255),
        `file` varchar(255),
        `internal_name` varchar(255),
        PRIMARY KEY (`id_roja45_quotation_document`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        $id_tab = Tab::getIdFromClassName('QuotationDocuments');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationDocuments';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $module->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }
    } else {
        $id_tab = Tab::getIdFromClassName('QuotationDocuments');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationDocuments';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $module->tabClassName);
            $tab->module = $module->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $module,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }
    }

    $module->registerHook('displayReassurance');

    return $return;
}
