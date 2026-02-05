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

function upgrade_module_1_1_0($module)
{
    if (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
        $module->registerHook('displayBackOfficeHeader');

        return true;
    } else {
        $module->registerHook('backOfficeHeader');

        return true;
    }

    return true;
}
