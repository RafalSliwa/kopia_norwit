<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_9_7($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // Media::clearCache(); // called in /install-2.9.8.php
    $module_obj->img()->optimizer('install');
    $module_obj->retro()->prepareOriginalImageFiles();
    $upd_rows = [];
    foreach ($module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'custombanners') as $row) {
        if ($module_obj->retro()->updateImgDataIfRequired($row['content'])) {
            $row['content'] = pSQL($row['content'], true);
            $upd_rows[] = '(\'' . implode('\', \'', $row) . '\')';
        }
    }
    if ($upd_rows) {
        $module_obj->db->execute('REPLACE INTO ' . _DB_PREFIX_ . 'custombanners VALUES ' . implode(', ', $upd_rows));
    }

    return true;
}
