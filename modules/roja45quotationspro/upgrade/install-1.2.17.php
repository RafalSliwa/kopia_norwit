<?php
/**
 * upgrade_module_1_2_17
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_2_17
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_2_17.
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

function upgrade_module_1_2_17($module)
{
    $return = true;
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $id_tab = Tab::getIdFromClassName('QuotationCatalog');
    if (!$id_tab) {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'QuotationCatalog';
        $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $module->tabClassName);
        $tab->module = $module->name;

        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                $module,
                $tab->class_name,
                $lang
            );
        }
        $return &= $tab->add();
    }

    return $return;
}
