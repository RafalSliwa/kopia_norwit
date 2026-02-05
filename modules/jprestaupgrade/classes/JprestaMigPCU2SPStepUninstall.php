<?php

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class JprestaMigPCU2SPStepUninstall extends JprestaMigPCU2SPStep
{
    var $module;

    var $moduleNameToUninstall;

    public function __construct($upgradeModule, $moduleNameToUninstall)
    {
        $this->module = $upgradeModule;
        $this->moduleNameToUninstall = $moduleNameToUninstall;
        parent::__construct(
            $moduleNameToUninstall . 'Uninstall',
            str_replace('_module_name_', $this->moduleNameToUninstall, $upgradeModule->l('Uninstall "_module_name_" but preserve its configuration', 'jprestamigpcu2spstepuninstall')),
            '../modules/'.$upgradeModule->name.'/views/img/logo-'.$moduleNameToUninstall.'.png',
            $upgradeModule->l('The module will be uninstalled and deleted, but its configuration will be preserved for import into the Speed Pack module.', 'jprestamigpcu2spstepuninstall')
        );
    }

    public function isRequired()
    {
        return JprestaUpgradeUtils::isModuleInstalled($this->moduleNameToUninstall);
    }

    public function init()
    {
        $mi = Module::getInstanceByName($this->moduleNameToUninstall);
        if ($mi) {
            $this->name = str_replace($this->moduleNameToUninstall, $mi->displayName, $this->name);
            $this->state = parent::STATE_TO_VALIDATE;
        }
        else {
            $this->state = parent::STATE_ERROR;
            $this->messages = $this->module->l('Cannot instanciate the module', 'jprestamigpcu2spstepuninstall') . ': ' . $this->moduleNameToUninstall;
        }
    }

    public function run()
    {
        try {
            $moduleManagerBuilder = \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();

            // Clearing the cache is really long so I disable it. If needed the user will do it manually.
            if (method_exists($moduleManager, 'setActionParams')) {
                // Does not exist in PS8
                $moduleManager->setActionParams(['cacheClearEnabled' => false]);
            } elseif (method_exists($moduleManager, 'disableSystemClearCache')) {
                // In PS8
                $moduleManager->disableSystemClearCache();
            }
            if (!defined('JprestaMigPCU2SP')) {
                define('JprestaMigPCU2SP', 'true');
            }
            $moduleManager->uninstall($this->moduleNameToUninstall);
            self::removeModuleFromDisk($this->moduleNameToUninstall);
            $this->state = parent::STATE_VALIDATED;
        }
        catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        } finally {
            if (count($this->errors) > 0) {
                $this->state = parent::STATE_ERROR;
            }
        }
        // Do not retry
        return false;
    }

    private static function removeModuleFromDisk($name)
    {
        $fs = new FileSystem();
        try {
            $fs->remove(_PS_MODULE_DIR_ . '/' . $name);
        } catch (IOException $e) {
            JprestaUpgradeUtils::addLog("Warning, module $name was not completly deleted from disk: " . $e->getMessage(), 2);
        }
    }
}
