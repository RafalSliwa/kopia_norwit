<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_4($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // caching settings are adjusted in 2.7.7, so the code below is not required
    // $rows = $module_obj->db->executeS('
    //     SELECT hook_name, id_shop, caching
    //     FROM ' . _DB_PREFIX_ . 'ec_hook_settings WHERE caching <> \'\'
    // ');
    // $upd_rows = [];
    // foreach ($rows as $k => $row) {
    //     $upd_caching = json_decode($row['caching'], true);
    //     $upd_caching['category'] = $module_obj->cachingSettings('allowConsiderCat') . '';
    //     if ($module_obj->cachingSettings('isBlocked', ['hook_name' => $row['hook_name']])) {
    //         $upd_caching = $module_obj->cachingSettings('getDefault');
    //     }
    //     $upd_caching = json_encode($upd_caching);
    //     if ($upd_caching != $row['caching']) {
    //         $row['caching'] = $upd_caching;
    //         $upd_rows[$k] = '(\'' . implode('\', \'', array_map('pSQL', $row)) . '\')';
    //     }
    // }
    // if ($upd_rows) {
    //     $module_obj->db->execute('
    //         INSERT INTO ' . _DB_PREFIX_ . 'ec_hook_settings (hook_name, id_shop, caching)
    //         VALUES ' . implode(', ', $upd_rows) . ' ON DUPLICATE KEY UPDATE caching = VALUES(caching)
    //     ');
    // }
    // $module_obj->cache('clear', '');

    return true;
}
