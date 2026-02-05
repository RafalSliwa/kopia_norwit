<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_9_9($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // Media::clearCache(); // cleared in /install-3.0.0
    $module_obj->unregisterHook(Hook::getIdByName('displayBackOfficeHeader'));
    $module_obj->prepareDatabase();
    $modern_rows = $sql = [];
    foreach ($module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'custombanners') as $retro_row) {
        $module_obj->retro()->toModernRows($retro_row, $modern_rows);
    }
    foreach ($modern_rows as $key => $rows) {
        foreach ($rows as $k => $row) {
            foreach ($row as $c_name => $value) {
                $allow_html = $c_name == 'content';
                $row[$c_name] = pSQL($value, $allow_html);
            }
            $rows[$k] = '(\'' . implode('\', \'', $row) . '\')';
        }
        $modern_table_name = _DB_PREFIX_ . 'cb' . ($key == 'lang' ? '_lang' : '');
        $sql[] = 'REPLACE INTO `' . bqSQL($modern_table_name) . '` VALUES ' . implode(', ', $rows);
    }
    if ($module_obj->runSql($sql)) {
        $module_obj->db->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'custombanners');
    }
    $outdated_data_file = _PS_MODULE_DIR_ . $module_obj->name . '/defaults/data-17.zip';
    if (file_exists($outdated_data_file)) {
        unlink($outdated_data_file);
    }

    return true;
}
