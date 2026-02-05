<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_5_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $files_to_remove = [
        '/views/templates/admin/exceptions-settings-form.tpl',
        '/views/templates/admin/carousel-settings-form.tpl',
        '/views/templates/admin/positions-settings-form.tpl',
    ];
    foreach ($files_to_remove as $file_path) {
        if (file_exists(_PS_MODULE_DIR_ . $module_obj->name . $file_path)) {
            unlink(_PS_MODULE_DIR_ . $module_obj->name . $file_path);
        }
    }
    if ($module_obj->db->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'cb_carousel_settings\'')
        && !$module_obj->db->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'cb_hook_settings\'')) {
        $module_obj->db->execute('
            RENAME TABLE ' . _DB_PREFIX_ . 'cb_carousel_settings TO ' . _DB_PREFIX_ . 'cb_hook_settings
        ');
        $columns = $module_obj->db->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'cb_hook_settings');
        foreach ($columns as $c) {
            if ($c['Field'] == 'settings') {
                $module_obj->db->execute('
                    ALTER TABLE ' . _DB_PREFIX_ . 'cb_hook_settings CHANGE settings carousel text NOT NULL
                ');
            }
        }
    }

    return true;
}
