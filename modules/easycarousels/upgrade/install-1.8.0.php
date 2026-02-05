<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_8_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $all = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
    $rows = [];
    $required_fields = $module_obj->getRequiredFields();
    foreach ($all as $a) {
        $settings = json_decode($a['settings'], true);
        foreach ($required_fields as $key => $fields) {
            foreach ($fields as $name => $f) {
                if (!isset($settings[$key][$name])) {
                    $settings[$key][$name] = $f['value'];
                }
            }
        }
        $settings = json_encode($settings);
        $rows[] = '(' . (int) $a['id_carousel'] . ', ' . (int) $a['id_shop'] . ', \'' . pSQL($settings) . '\')';
    }
    $module_obj->db->execute('
        INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, settings)
        VALUES ' . implode(', ', $rows) . '
        ON DUPLICATE KEY UPDATE
        settings = VALUES(settings)
    ');

    $files_to_remove = [
        _PS_MODULE_DIR_ . $module_obj->name . '/documentation_en.txt',
    ];
    foreach ($files_to_remove as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    return true;
}
