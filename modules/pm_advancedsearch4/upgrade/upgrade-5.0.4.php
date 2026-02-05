<?php
/**
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Performs the update to version 5.0.4
 *
 * @param \PM_AdvancedSearch4 $module
 * @return bool
 */
function upgrade_module_5_0_4($module)
{
    $module->unregisterHook('updateProduct');
    $module->registerHook('actionUpdateProduct');
    $module->unregisterHook('addProduct');
    $module->registerHook('actionProductAdd');
    $module->unregisterHook('deleteProduct');
    $module->registerHook('actionProductDelete');
    return true;
}
