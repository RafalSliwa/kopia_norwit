<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_1_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $files_to_remove = [
        _PS_MODULE_DIR_ . $module_obj->name . '/upgrade/upgrade-1.0.1.php',
    ];
    foreach ($files_to_remove as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    return true;
}
