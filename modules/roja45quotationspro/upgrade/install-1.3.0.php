<?php
/**
 * upgrade_module_1_3_0
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_0
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_0.
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

function upgrade_module_1_3_0($module)
{
    $return = true;

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'roja45_quotationspro`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='is_template') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `is_template` TINYINT(1)';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `quote_name` VARCHAR(255)';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `template_name` VARCHAR(255)';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `date_add` DATETIME';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` ADD COLUMN `date_upd` DATETIME';
        $return &= Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro` SET `date_add` = `received`;';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro` SET `date_upd` = `last_update`;';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` DROP `received`;';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro` DROP `last_update`;';
        $return &= Db::getInstance()->execute($sql);
    }

    $list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'roja45_quotationspro_product`');
    $alter_column=true;
    if (is_array($list_fields)) {
        foreach ($list_fields as $field) {
            if ($field['Field']=='date_add') {
                $alter_column=false;
            }
        }
    }
    if ($alter_column) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` ADD COLUMN `date_add` DATETIME';
        $return &= Db::getInstance()->execute($sql);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'roja45_quotationspro_product` ADD COLUMN `date_upd` DATETIME';
        $return &= Db::getInstance()->execute($sql);
    }

    $id_tab = Tab::getIdFromClassName('AdminQuotationTemplates');
    if (!$id_tab) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminQuotationTemplates';
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

    $groups = Group::getGroups(Context::getContext()->language->id, Context::getContext()->shop->id);
    $group_ids = '';
    foreach ($groups as $group) {
        $group_ids .= $group['id_group'] . ',';
    }
    $group_ids = Tools::substr($group_ids, 0, Tools::strlen($group_ids)-1);

    Configuration::updateValue('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS', $group_ids);
    Configuration::updateValue('ROJA45_QUOTATIONSPRO_AUTOENABLENEW', 0);

    $module->registerHook('actionProductSave');
    $module->registerHook('actionProductDelete');
    return $return;
}
