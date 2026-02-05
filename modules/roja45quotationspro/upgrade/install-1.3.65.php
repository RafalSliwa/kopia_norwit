<?php
/**
 * upgrade_module_1_3_65
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_65
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_65.
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

function upgrade_module_1_3_65($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_formconditiongroup` (
          `id_roja45_quotation_formconditiongroup` int(10) unsigned NOT NULL auto_increment,
          `id_roja45_quotation_form` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id_roja45_quotation_formconditiongroup`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_formcondition` (
          `id_roja45_quotation_formcondition` int(10) unsigned NOT NULL auto_increment,
          `id_roja45_quotation_formconditiongroup` int(10) unsigned NOT NULL,
          `type` varchar(255) NOT NULL,
          `value` varchar(255),
          PRIMARY KEY (`id_roja45_quotation_formcondition`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $sql = '
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_form_product` (
          `id_roja45_quotation_form_product` int(10) unsigned NOT NULL auto_increment,
          `id_product` int(10) unsigned NOT NULL,
          `id_roja45_quotation_form` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id_roja45_quotation_form_product`)
        ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
    $return &= (bool)Db::getInstance()->execute($sql);

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_form`');
    $add_tables = true;
    foreach ($list_fields as $field) {
        if ($field['Field'] == 'form_name') {
            $add_tables = false;
        }
    }
    if ($add_tables) {
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_form` 
            ADD `form_name` VARCHAR(255) AFTER `form_columns`';
        $return &= Db::getInstance()->execute($sql);
        $sql =
            'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_form` 
            ADD `default_form` VARCHAR(255) AFTER `form_name`';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_form` ADD COLUMN `date_add` DATETIME NULL';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_form` ADD COLUMN `date_upd` DATETIME NULL';
        $return &= Db::getInstance()->execute($sql);
    }

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        $id_tab = Tab::getIdFromClassName('QuotationForms');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationForms';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->icon = 'file';
            $tab->module = $module->name;

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
        $id_tab = Tab::getIdFromClassName('QuotationForms');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationForms';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $module->tabClassName);
            $tab->module = $module->name;

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

    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
        '.quote_quantity_wanted'
    );
    Configuration::updateValue(
        'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
        '._desktop_quotecart'
    );
    return $return;
}
