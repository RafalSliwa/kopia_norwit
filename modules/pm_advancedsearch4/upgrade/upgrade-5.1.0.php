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
 * Performs the update to version 5.1.0
 *
 * @param \PM_AdvancedSearch4 $module
 * @return bool
 */
function upgrade_module_5_1_0($module)
{
    $module->updateSearchTable('5.1.0');
    return true;
}
