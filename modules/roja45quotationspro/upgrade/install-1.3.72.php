<?php
/**
 * upgrade_module_1_3_72
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_72
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_72.
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

function upgrade_module_1_3_72($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ICON_PACK',
            '2'
        );
    } else {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ICON_PACK',
            '1'
        );
    }

    return $return;
}
