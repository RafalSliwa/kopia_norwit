<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_0_1($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $hook_files_to_delete = ['product-details.tpl', 'manufacturer-details.tpl', 'supplier-details.tpl'];
    foreach ($hook_files_to_delete as $filename) {
        if (file_exists(_PS_MODULE_DIR_ . $module_obj->name . '/views/templates/hook/' . $filename)) {
            unlink(_PS_MODULE_DIR_ . $module_obj->name . '/views/templates/hook/' . $filename);
        }
    }

    // in new version some files moved to 'views', basing on validator requirements
    $folders_to_delete = ['css', 'js', 'img'];
    foreach ($folders_to_delete as $folder_name) {
        recursiveRemove(_PS_MODULE_DIR_ . $module_obj->name . '/' . $folder_name);
    }

    return true;
}

function recursiveRemove($dir, $top_level = false)
{
    $files_to_keep = [];
    if ($top_level) {
        $files_to_keep = ['index.php'];
    }
    $structure = glob(rtrim($dir, '/') . '/*');
    if (is_array($structure)) {
        foreach ($structure as $file) {
            if (is_dir($file)) {
                recursiveRemove($file);
            } elseif (is_file($file) && !in_array(basename($file), $files_to_keep)) {
                unlink($file);
            }
        }
    }
    if (!$top_level && is_dir($dir)) {
        rmdir($dir);
    }
}
