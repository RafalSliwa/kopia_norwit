<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    // Add new configuration value for the sticky bar visibility date
    if (!Configuration::hasKey('STICKY_DATE_FIELD')) {
        Configuration::updateValue('STICKY_DATE_FIELD', date('Y-m-d'));
    }
    if (!Configuration::hasKey('STICKY_PRO_TYPE_TOGGLE')) {
        Configuration::updateValue('STICKY_PRO_TYPE_TOGGLE', 1);
    }
    if (!Configuration::hasKey('STICKY_CATEGORY')) {
        Configuration::updateValue('STICKY_CATEGORY', 1);
    }
    if (!Configuration::hasKey('STICKY_EXC_PRODUCTS_ON_OFF')) {
        Configuration::updateValue('STICKY_EXC_PRODUCTS_ON_OFF', 1);
    }

    return true;
}
