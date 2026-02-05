<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class JprestaMigPCU2SPStepUpgrade extends JprestaMigPCU2SPStep
{
    var $module;

    var $moduleNameToUpgrade;

    var $downloadInfos;

    public function __construct($upgradeModule, $moduleNameToUpgrade, $downloadInfos)
    {
        $this->module = $upgradeModule;
        $this->moduleNameToUpgrade = $moduleNameToUpgrade;
        $this->downloadInfos = $downloadInfos;
        parent::__construct(
            $moduleNameToUpgrade . 'Upgrade',
            str_replace(['_module_name_', '_version_'], [$this->moduleNameToUpgrade, $downloadInfos['version']], $upgradeModule->l('Upgrade "_module_name_" to version _version_', 'jprestamigpcu2spstepupgrade')),
            '../modules/'.$moduleNameToUpgrade.'/logo.png',
            $upgradeModule->l('The module must be upgraded to the latest version to ensure all upgrade scripts have been executed', 'jprestamigpcu2spstepupgrade')
        );
    }

    public function isRequired()
    {
        return JprestaUpgradeUtils::isModuleInstalled($this->moduleNameToUpgrade);
    }

    public function init()
    {
        $mi = Module::getInstanceByName($this->moduleNameToUpgrade);
        if ($mi) {
            $this->name = str_replace($this->moduleNameToUpgrade, $mi->displayName, $this->name);
            $this->initState();
        }
        else {
            $this->state = parent::STATE_ERROR;
            $this->errors[] = $this->module->l('Cannot instanciate the module', 'jprestamigpcu2spstepupgrade') . ': ' . $this->moduleNameToUpgrade;
        }
    }

    private function initState()
    {
        $mi = Module::getInstanceByName($this->moduleNameToUpgrade);
        if ($mi) {
            $database_version = DB::getInstance()->getValue('SELECT version FROM `' . _DB_PREFIX_ . 'module` WHERE name=\'' . pSQL($mi->name) . '\'', false);
            $upgradeScriptOK = Tools::version_compare($mi->version, $database_version, '<=');
            if ($upgradeScriptOK && version_compare($mi->version, $this->downloadInfos['version'] , '>=')) {
                $this->state = parent::STATE_VALIDATED;
            }
            else if (version_compare($mi->version, $this->downloadInfos['version'] , '>=')) {
                $this->state = parent::STATE_TO_VALIDATE_AGAIN;
            }
            else {
                $this->state = parent::STATE_TO_VALIDATE;
            }
        }
    }

    public function run()
    {
        $retry = false;
        try {
            $upgrader = new JprestaModuleUpgrader();
            $confKey = $this->id . '-v' . str_replace('.', '-', $this->downloadInfos['version']);
            $previousRun = JprestaUpgradeUtils::getConfigurationAllShop($confKey);
            if (!$previousRun) {
                $fullyInstalled = $upgrader->upgradeModule($this->moduleNameToUpgrade, $this->downloadInfos['link'], $this->downloadInfos['version']);
                if (count($upgrader->errors) === 0) {
                    // Remember that files have been installed
                    JprestaUpgradeUtils::saveConfigurationAllShop($confKey, true);
                    // Will retry to finish the installation (upgrade scripts)
                    $retry = true;
                }
            } else {
                // This will run upgrade scripts
                $fullyInstalled = $upgrader->upgradeModule($this->moduleNameToUpgrade, null, null);
                if (count($upgrader->errors) === 0) {
                    // Clean
                    Configuration::deleteByName($confKey);
                }
            }
            $this->errors = array_merge($this->errors, $upgrader->errors);
            $this->confirmations = array_merge($this->confirmations, $upgrader->confirmations);
            if (count($upgrader->errors) === 0) {
                if ($retry) {
                    $this->state = parent::STATE_TO_VALIDATE_AGAIN;
                }
                else {
                    $this->state = parent::STATE_VALIDATED;
                }
            }
        }
        catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        } finally {
            if (count($this->errors) > 0) {
                $this->state = parent::STATE_ERROR;
            }
        }
        return $retry;
    }
}
