<?php
/**
 * Upgrade module powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once 'classes/JprestaUpgradeUtils.php';
require_once 'classes/JprestaUpgradeApi.php';
require_once 'classes/JprestaModuleUpgrader.php';
require_once 'classes/JprestaMigPCU2SPStep.php';
require_once 'classes/JprestaMigPCU2SPStepUpgrade.php';
require_once 'classes/JprestaMigPCU2SPStepUninstall.php';
require_once 'classes/JprestaMigPCU2SPStepInstall.php';
require_once 'classes/JprestaMigPCU2SPStepTest.php';

class Jprestaupgrade extends Module
{

    public function __construct()
    {
        $this->name = 'jprestaupgrade';
        $this->tab = 'administration';
        $this->version = '2.0.4';
        $this->author = 'JPresta';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('JPresta - Easy upgrade');
        $this->description = $this->l('Easily upgrade your theme and modules developped by JPresta.com');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $ret = parent::install()
                && $this->registerHook('displayAdminAfterHeader');

        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->installTab('AdminJprestaUpgrade', $this->displayName, (int)Tab::getIdFromClassName('AdminParentModules'));
        }
        else {
            $this->installTab('AdminJprestaUpgrade', $this->displayName, (int)Tab::getIdFromClassName('AdminParentModulesSf'));
        }
        $this->installTab('AdminJprestaMigPCU2SP', $this->l('Migration to Speed Pack module'));
        return $ret;
    }

    public function installTab($adminController, $name = false, $id_parent = -1)
    {
        $isUpdate = true;
        $tab = Tab::getInstanceFromClassName($adminController);
        if (!$tab || !$tab->id) {
            $tab = new Tab();
            $tab->class_name = $adminController;
            $isUpdate = false;
        }
        $tab->active = 1;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            // Translation for modules are cached in a global variable but the local is ignored >:(
            if (is_array($name)) {
                if (array_key_exists($lang['iso_code'], $name)) {
                    $trans = $name[$lang['iso_code']];
                }
                elseif (array_key_exists('en', $name)) {
                    $trans = $name['en'];
                }
            }
            else {
                $trans = $name;
            }
            $tab->name[$lang['id_lang']] = !$trans ? $this->name : $trans;
        }
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        if ($isUpdate) {
            return $tab->update();
        }
        else {
            return $tab->add();
        }
    }

    public function uninstallTab($adminController)
    {
        $id_tab = (int)Tab::getIdFromClassName($adminController);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                return ($tab->delete());
            } else {
                $return = false;
            }
        } else {
            $return = true;
        }
        return $return;
    }

    /**
     * Override Module::updateModuleTranslations()
     */
    public function updateModuleTranslations()
    {
        // Speeds up installation: do nothing because translations are not in Prestashop language pack
    }

    public function getContent()
    {
        $link = $this->context->link->getAdminLink('AdminJprestaUpgrade');
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>')) {
            Tools::redirect($link);
        } else {
            // There is a bug in redirect and getAdminLink in PS1.5 and PS1.6 so we do it ourselves
            $path = parse_url($_SERVER['REQUEST_URI'])['path'];
            header('Loc'.'ation: //' . $_SERVER['HTTP_HOST'] . dirname($path) . '/' . $link);
            exit;
        }
    }

    public function hookDisplayAdminAfterHeader()
    {
        if (in_array(Tools::getValue('controller'), ['AdminModules', 'AdminModulesManage', 'AdminModulesNotifications', 'AdminModulesUpdates'])
            && !Tools::getIsset('configure')) {
            if (JprestaUpgradeApi::getUpdatableCount() > 0) {
                $infos = [];
                $infos['jpresta_modules'] = JprestaUpgradeApi::getModulesOrThemesInfos();
                $infos['jpresta_upgrade_link'] = $this->context->link->getAdminLink('AdminJprestaUpgrade');

                $this->context->smarty->assign($infos);
                return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/notification.tpl');
            }
        }
    }

}
