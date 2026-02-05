<?php
/**
 * upgrade_module_1_3_20
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_20
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_20.
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

function upgrade_module_1_3_20($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    if ($id_tab = Tab::getIdFromClassName($module->tabClassName)) {
        $tab = new Tab($id_tab);
        $tab->icon = 'list';
        $tab->save();
    }

    if ($id_tab = Tab::getIdFromClassName('QuotationCatalog')) {
        $tab = new Tab($id_tab);
        $tab->icon = 'store';
        $tab->save();
    }

    if ($id_tab = Tab::getIdFromClassName('AdminQuotationTemplates')) {
        $tab = new Tab($id_tab);
        $tab->icon = 'tab';
        $tab->save();
    }


    Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAILREQUEST', 0);
    return $return;
}
