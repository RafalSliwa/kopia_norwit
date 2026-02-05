<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class JprestaMigPCU2SPStepInstall extends JprestaMigPCU2SPStep
{
    var $module;

    var $moduleNameToInstall;

    var $downloadInfos;

    public function __construct($upgradeModule, $moduleNameToInstall, $downloadInfos)
    {
        $this->module = $upgradeModule;
        $this->moduleNameToInstall = $moduleNameToInstall;
        $this->downloadInfos = $downloadInfos;
        parent::__construct(
            $moduleNameToInstall . 'Install',
            str_replace(['_module_name_', '_version_'], [$this->moduleNameToInstall, $downloadInfos['version']], $upgradeModule->l('Install "_module_name_" and restore configuration', 'jprestamigpcu2spstepinstall')),
            '../modules/'.$upgradeModule->name.'/views/img/logo-'.$moduleNameToInstall.'.png',
            $upgradeModule->l('Install the Speed Pack module and restore the configuration of the replaced modules', 'jprestamigpcu2spstepinstall')
        );
    }

    public function isRequired()
    {
        return true;
    }

    public function init()
    {
        if (JprestaUpgradeUtils::isModuleInstalled($this->moduleNameToInstall)) {
            $this->state = parent::STATE_VALIDATED;
        }
        else {
            $this->state = parent::STATE_TO_VALIDATE;
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
            // Get the ZIP file
            $moduleFile = _PS_CACHE_DIR_ . 'jprestaupgrade-' . time() . '.zip';
            JprestaUpgradeUtils::downloadFile($this->downloadInfos['link'], $moduleFile);
            $moduleManager->install($moduleFile);
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
}
