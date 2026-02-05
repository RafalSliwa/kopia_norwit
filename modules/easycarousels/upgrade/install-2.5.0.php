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
    // add columns for new exceptions mechanism
    $module_obj->db->execute('
        ALTER TABLE ' . _DB_PREFIX_ . 'ec_hook_settings
        ADD exc_type tinyint(1) NOT NULL DEFAULT 1 AFTER display,
        ADD exc_controllers text NOT NULL AFTER exc_type
    ');
    // prepare exceptions data
    $exc_data = $module_obj->db->executeS('
        SELECT hme.*, h.name AS hook_name
        FROM ' . _DB_PREFIX_ . 'hook_module_exceptions hme
        LEFT JOIN ' . _DB_PREFIX_ . 'hook h ON h.id_hook = hme.id_hook
        WHERE id_module = ' . (int) $module_obj->id . '
    ');
    $hook_exceptions = [];
    foreach ($exc_data as $d) {
        $id_shop = $d['id_shop'];
        $hook_name = $d['hook_name'];
        $hook_exceptions[$id_shop][$hook_name][] = $d['file_name'];
    }
    foreach ($hook_exceptions as $id_shop => $exceptions) {
        foreach ($exceptions as $hook_name => $exc_controllers) {
            $module_obj->saveExceptions($hook_name, 1, $exc_controllers, [$id_shop]);
        }
    }

    return true;
}
