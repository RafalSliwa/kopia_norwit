<?php
/**
 * upgrade_module_1_3_25
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_25
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_25.
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

function upgrade_module_1_3_25($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;

    $id_parent = Tab::getIdFromClassName('AdminParentAdminQuotationsPro');
    if (!$id_tab = Tab::getIdFromClassName('AdminQuotations')) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminQuotations';
        $tab->id_parent = $id_parent;
        $tab->module = $module->name;
        $tab->icon = 'list';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                $module,
                'AdminQuotationsPro',
                $lang
            );
        }
        $return &= $tab->add();
    }

    $id_quotation_parent = Tab::getIdFromClassName('AdminQuotations');

    if ($id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
        $tab = new Tab($id_tab);
        $tab->id_parent = $id_quotation_parent;
        $return &= $tab->save();
        $tab->updatePosition(0, 1);
    }

    if (!$id_tab = Tab::getIdFromClassName('QuotationCatalog')) {
        $tab = new Tab($id_tab);
        $tab->id_parent = $id_quotation_parent;
        $return &= $tab->save();
    }


    Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAILREQUEST', 0);
    return $return;
}
