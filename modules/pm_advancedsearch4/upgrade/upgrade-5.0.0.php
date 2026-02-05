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
 * Performs the update to version 5.0.0
 *
 * @param \PM_AdvancedSearch4 $module
 * @return bool
 */
function upgrade_module_5_0_0($module)
{
    $module->unregisterHook('header');
    $module->registerHook('displayHeader');
    $module->updateSearchTable('4.12.14');
    return true;
}
