<?php
/**
 * upgrade_module_1_3_74
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_74
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_74.
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

function upgrade_module_1_3_74($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $value = Configuration::get(
        'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR'
    );
    if ($value=='._desktop_quotecart') {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
            '.roja_desktop_quotecart'
        );
    }

    $module->registerHook('displayBackOfficeHeader');
    $module->registerHook('displayBackOfficeTop');

    return $return;
}
