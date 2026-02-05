<?php
/**
 * 2007-2018 PrestaShop
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace CeneoXml\Utils;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TabManager
{
    public static function addTab($className, array $tabName, $moduleName, $parentClassName, $icon = null)
    {
        if ($id_tab = \Tab::getIdFromClassName($className)) {
            return new \Tab($id_tab);
        }
        $tab = new \Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = [];
        if (!empty($icon)) {
            $tab->icon = $icon;
        }

        foreach (\Language::getLanguages(true) as $lang) {
            if (isset($tabName[$lang['iso_code']])) {
                $tab->name[$lang['id_lang']] = $tabName[$lang['iso_code']];
            }
        }

        $id_parent = \Tab::getIdFromClassName($parentClassName);
        if ($id_parent === false) {
            PrestaShopLogger::addLog('Nie udało się znaleźć ID dla rodzica o nazwie klasy: ' . $parentClassName, 3);
            return null;
        }
        $tab->id_parent = (int) $id_parent;
        $tab->module = $moduleName;

        if (!$tab->save()) {
            PrestaShopLogger::addLog('Nie udało się dodać zakładki o nazwie klasy: ' . $className, 3);
            return null;
        }

        return $tab;
    }

    public static function removeTab($className)
    {
        $id_tab = (int) \Tab::getIdFromClassName($className);
        $tab = new \Tab($id_tab);
        if ($tab->name !== '' && !\Tab::getTabs(\Configuration::get('PS_LANG_DEFAULT'), $id_tab)) {
            $tab->delete();
        }
    }
}
