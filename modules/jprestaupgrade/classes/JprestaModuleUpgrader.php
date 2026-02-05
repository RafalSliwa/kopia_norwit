<?php

class JprestaModuleUpgrader
{
    var $errors = [];
    var $confirmations = [];

    /**
     * @throws PrestaShopException
     */
    public function upgradeModule($moduleName, $downloadLink, $newVersion)
    {
        $fullyInstalled = false;
        try {
            $moduleFile = $tempFile = null;

            if ($downloadLink) {
                // Get the ZIP file
                $moduleFile = $tempFile = _PS_CACHE_DIR_ . 'jprestaupgrade-' . time() . '.zip';
                JprestaUpgradeUtils::downloadFile($downloadLink, $tempFile);

                if (strpos($downloadLink, '-multiple') !== false) {
                    // This is an archive with multiple version depending on Prestashop instances
                    $moduleFile = JprestaUpgradeUtils::extractModuleNeededVersion($tempFile);
                }
            }

            // Upgrade the module
            if (Tools::version_compare(_PS_VERSION_,'1.7','>=')) {
                $moduleManagerBuilder = \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();

                // Disable ps_mbo or it will not work (don't know if it will fail for PS<8.0.2)
                if (method_exists('Hook', 'disableHooksForModule') && Module::isInstalled('ps_mbo')) {
                    Hook::disableHooksForModule(Module::getModuleIdByName('ps_mbo'));
                }

                // Clearing the cache is really long so I disable it. If needed the user will do it manually.
                if (method_exists($moduleManager, 'setActionParams')) {
                    // Does not exist in PS8
                    $moduleManager->setActionParams(['cacheClearEnabled' => false]);
                } elseif (method_exists($moduleManager, 'disableSystemClearCache')) {
                    // In PS8
                    $moduleManager->disableSystemClearCache();
                }

                if (Tools::version_compare(_PS_VERSION_, '8', '<')) {
                    $moduleManager->upgrade($moduleName, $newVersion, $moduleFile);
                } else {
                    $moduleManager->upgrade($moduleName, $moduleFile);
                }
            }
            else {
                // Extract module
                if ($moduleFile) {
                    JprestaUpgradeUtils::extractZipToDir($moduleFile, _PS_MODULE_DIR_);
                }
                $module = Module::getInstanceByName($moduleName);
                if (!$module) {
                    throw new PrestaShopException($moduleName . ' cannot be instanciated');
                }
                // Install or upgrade module
                if (!Module::isInstalled($moduleName)) {
                    $resulat = $module->install();
                }
                else {
                    $moduleOnDisk = null;
                    $modulesOnDisk = Module::getModulesOnDisk(true);
                    foreach($modulesOnDisk as $curModuleOnDisk) {
                        if ($curModuleOnDisk->name === $module->name) {
                            $moduleOnDisk = $curModuleOnDisk;
                            break;
                        }
                    }
                    if (!$moduleOnDisk) {
                        throw new PrestaShopException('Cannot find module on disk');
                    }
                    // Upgrade Module process, init check if a module could be upgraded
                    $moduleMainFile = _PS_MODULE_DIR_ . $module->name . '/' . $module->name . '.php';
                    if (Module::initUpgradeModule($moduleOnDisk)) {
                        // When the XML cache file is up-to-date, the module may not be loaded yet
                        if (!class_exists($module->name)) {
                            if (!file_exists($moduleMainFile)) {
                                throw new PrestaShopException($moduleMainFile . ' does not exist');
                            }
                            require_once($moduleMainFile);
                        }

                        if ($object = Adapter_ServiceLocator::get($module->name)) {
                            /** @var Module $object */
                            $object->runUpgradeModule();
                            if ((count($errors_module_list = $object->getErrors()))) {
                                $this->errors[] = $this->module->l('Some errors occurred during the upgrade of the module', 'jprestamoduleupgrader');
                                $this->errors = array_merge($this->errors, $errors_module_list);
                            } elseif ((count($conf_module_list = $object->getConfirmations()))) {
                                $this->confirmations[] = $this->module->l('The module has been upgraded to version', 'jprestamoduleupgrader') . ' ' . $object->version;
                                $this->confirmations = array_merge($this->confirmations, $conf_module_list);
                            }
                            unset($object);
                        }
                    }
                    // Module can't be upgraded if not file exist but can change the database version...
                    // User has to be prevented
                    elseif (Module::getUpgradeStatus($module->name)) {
                        // When the XML cache file is up-to-date, the module may not be loaded yet
                        if (!class_exists($module->name)) {
                            if (file_exists($moduleMainFile)) {
                                require_once($moduleMainFile);
                                $object = Adapter_ServiceLocator::get($module->name);
                                $this->confirmations[] = $this->module->l('The module has been upgraded to version', 'jprestamoduleupgrader') . ' ' . $object->version;
                            } else {
                                throw new PrestaShopException($moduleMainFile . ' does not exist');
                            }
                        }
                        unset($object);
                    }
                }
            }

            // It will still be old version if there is a PHP code cache so we will need to run the process again
            $moduleInstance = Module::getInstanceByName($moduleName);
            $fullyInstalled = $newVersion === null || $moduleInstance->version === $newVersion;

        } catch (Exception $e) {
            $this->errors[] = 'An error occured during the upgrade of module ' . $moduleName . ' v'.$newVersion.': ' . $e->getMessage() . ' (more details in logs)';
            JprestaUpgradeUtils::addLog('An error occured during the upgrade of module ' . $moduleName . ' v'.$newVersion.': '. $e->getMessage() . ". ". JprestaUpgradeUtils::jTraceEx($e), 2);
        } finally {
            JprestaUpgradeUtils::deleteFile($tempFile);
            JprestaUpgradeUtils::deleteFile($moduleFile);
        }
        return $fullyInstalled;
    }

}
