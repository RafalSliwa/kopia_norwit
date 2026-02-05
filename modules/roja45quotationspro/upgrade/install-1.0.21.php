<?php
/**
 * upgrade_module_1_1_7
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_1_7
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_1_7.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Function
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_21($module)
{
    $return = true;
    if ((version_compare(_PS_VERSION_, '1.6.1.11', '>=') === true)) {
        if (file_exists(_PS_MODULE_DIR_ . $module->name . '/override')) {
            rename(_PS_MODULE_DIR_ . $module->name . '/override', _PS_MODULE_DIR_ . $module->name . '/override_old');
            unlink(_PS_OVERRIDE_DIR_.'/classes/SpecificPrice.php');
        }
    }
    return $return;
}
