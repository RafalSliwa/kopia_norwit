<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_7_8($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $carousels = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
    $multilang_rows = [];
    foreach ($carousels as $c) {
        $name_multilang = json_decode($c['name_multilang'], true);
        foreach ($name_multilang as $id_lang => $name) {
            $data = ['name' => $name, 'description' => ''];
            $data = json_encode($data);
            $row = (int) $c['id_carousel'] . ', ' . (int) $c['id_shop'] . ', ' . (int) $id_lang . ', \'' . pSQL($data) . '\'';
            $multilang_rows[] = '(' . $row . ')';
        }
    }
    $module_obj->prepareDatabaseTables();
    $module_obj->db->execute('REPLACE INTO ' . _DB_PREFIX_ . 'easycarousels_lang VALUES ' . implode(', ', $multilang_rows));
    $module_obj->db->execute('ALTER TABLE ' . _DB_PREFIX_ . 'easycarousels DROP COLUMN name_multilang');

    return true;
}
