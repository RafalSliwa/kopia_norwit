<?php
/**
 * Custom Carrier Module - Upgrade to 1.2.0
 * Adds bulk shipping settings admin tab
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    // Install the bulk settings tab
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminCustomCarrierBulk';
    $tab->name = [];

    foreach (Language::getLanguages(true) as $lang) {
        $tab->name[$lang['id_lang']] = 'Custom Carrier Bulk';
    }

    $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
    $tab->module = $module->name;

    return $tab->add();
}
