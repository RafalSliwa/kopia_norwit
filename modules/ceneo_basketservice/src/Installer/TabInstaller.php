<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2024, Ceneo
 * @license   LICENSE.txt
 */
namespace CeneoBs\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration as Cfg;

class TabInstaller
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function install()
    {
        $this->addTab(
            'AdminCeneoClass',
            ['pl' => 'Ceneo', 'en' => 'Ceneo'],
            $this->module->name,
            'AdminTools',
            'settings'
        );
        $this->addTab(
            'AdminCeneoBasketservice',
            ['pl' => 'Marketplace Ceneo (Kup teraz)', 'en' => 'Marketplace Ceneo (Buy Now)'],
            $this->module->name,
            'AdminCeneoClass',
            ''
        );
        $this->addTab(
            'AdminCeneoBasketserviceAjax',
            'Ajax',
            $this->module->name,
            'AdminCeneoClass',
            '',
            false
        );
        $this->addTab(
            'AdminCeneoBasketserviceSettings',
            ['pl' => 'Mapowanie dostawców', 'en' => 'Shipping mapping'],
            $this->module->name,
            'AdminCeneoClass',
            ''
        );


        return true;
    }

    public function uninstall()
    {
        $this->removeTab('AdminCeneoBasketservice');
        $this->removeTab('AdminCeneoBasketserviceAjax');
        $this->removeTab('AdminCeneoBasketserviceSettings');

        return true;
    }

    public function addTab($className, $tabNames, $moduleName, $parentClassName, $icon, $visible = true)
    {
        if ($id_tab = \Tab::getIdFromClassName($className)) {
            return new \Tab($id_tab);
        }
        $tab = new \Tab();
        $tab->active = $visible ? 1 : 0;
        $tab->class_name = $className;
        $tab->name = [];
        if (isset($icon)) {
            if (!empty($icon)) {
                $tab->icon = $icon;
            }
        }

        foreach (\Language::getLanguages(true) as $lang) {
            $isoCode = $lang['iso_code'];
            $tab->name[$lang['id_lang']] = isset($tabNames[$isoCode]) ? $tabNames[$isoCode] : $tabNames;
        }

        $tab->id_parent = (int) \Tab::getIdFromClassName($parentClassName);
        $tab->module = $moduleName;
        $tab->add();
        return $tab;
    }

    public function removeTab($className)
    {
        $id_tab = (int) \Tab::getIdFromClassName($className);
        $tab = new \Tab($id_tab);
        if ($tab->name !== '' && !\Tab::getTabs(Cfg::get('PS_LANG_DEFAULT'), $id_tab)) {
            $tab->delete();
        }
    }
}
