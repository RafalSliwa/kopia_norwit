<?php

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Upgrade module powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class AdminJprestaUpgradeController extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
        // Some modules (like MyPresta.eu) add some advert messages
        $this->warnings = [];
        $this->errors = [];
        $this->confirmations = [];

        // If we try to update the settings
        if (Tools::getIsset('submitModuleJak')) {
            $JprestaUpgradeApi = new JprestaUpgradeApi(Tools::getValue('jprestaAccountKey'), JprestaUpgradeApi::getPrestashopToken());
            $psType = Tools::getValue('prestashopType') === 'test' ? 'test' : 'prod';
            $res = $JprestaUpgradeApi->attach($psType === 'test');
            if ($res === true) {
                JprestaUpgradeApi::setPrestashopType($psType);
                JprestaUpgradeApi::setJPrestaAccountKey(Tools::getValue('jprestaAccountKey'));
                $this->confirmations[] = $this->module->l('Your JPresta Account Key has been saved', 'jprestaupgrade');
            }
            else {
                $this->errors[] = $this->module->l('Cannot attach JPresta Account Key', 'jprestaupgrade') . ' ' . Tools::getValue('jprestaAccountKey') . ': ' . $res;
            }
        }
        elseif (Tools::getIsset('submitModuleJakDetach')) {
            $JprestaUpgradeApi = new JprestaUpgradeApi(JprestaUpgradeApi::getJPrestaAccountKey(), JprestaUpgradeApi::getPrestashopToken());
            $res = $JprestaUpgradeApi->detach();
            if ($res === true) {
                JprestaUpgradeApi::setPrestashopType(null);
                JprestaUpgradeApi::setJPrestaAccountKey(null);
                $this->confirmations[] = $this->module->l('Your JPresta Account has been detached', 'jprestaupgrade');
            }
            else {
                $this->errors[] = $this->module->l('Cannot detach your JPresta Account', 'jprestaupgrade') . ' ' . Tools::getValue('jprestaAccountKey') . ': ' . $res;
            }
        }
        elseif (Tools::getIsset('submitModuleConfirmClone') || Tools::getIsset('submitModuleJakReset')) {
            JprestaUpgradeApi::setPrestashopIsClone(true);
            $this->confirmations[] = $this->module->l('Thank you, you can now attach this Prestashop instance to your JPresta account', 'jprestaupgrade');
        }
        elseif (Tools::getIsset('submitModuleNotAClone')) {
            JprestaUpgradeApi::setPrestashopIsClone(false);
            $this->confirmations[] = $this->module->l('Thank you, this Prestashop instance is considered the same', 'jprestaupgrade');
        }
        elseif (Tools::getIsset('submitModuleUpgrade')) {
            $this->json = true;
            $moduleOrThemeName = Tools::getValue('submitModuleUpgrade');

            $infos = JprestaUpgradeApi::getModuleOrThemeInfosByName($moduleOrThemeName);
            if ($infos['type'] === 'module') {
                $moduleInstance = Module::getInstanceByName($moduleOrThemeName);
                if ($this->upgradeModule($moduleOrThemeName)) {
                    $this->status = 'ok';
                    $this->confirmations = array_merge($this->confirmations, $moduleInstance->getConfirmations());
                    if (count($this->confirmations) === 0) {
                        $this->confirmations[] = $this->module->l('The module has been upgraded to version', 'jprestaupgrade') . ' ' . $moduleInstance->version;
                    }
                    $this->content = $this->renderModule($moduleOrThemeName);
                }
                elseif (count($this->errors) === 0) {
                    // The upgrade must be re-lauched
                    $this->status = 'restart';
                }
                else {
                    $this->errors = array_merge($this->errors, $moduleInstance->getErrors());
                    if (count($this->errors) === 0) {
                        $this->errors[] = $this->module->l('An unknown error occured during the upgrade', 'jprestaupgrade');
                    }
                    $this->status = 'error';
                }
            }
            else {
                $this->upgradeTheme($moduleOrThemeName);
                $this->content = $this->renderModule($moduleOrThemeName);
            }
        }
        return true;
    }

    private function renderModule($moduleName)
    {
        // Variable for smarty
        $infos = array();
        $infos['jpresta_account_key'] = JprestaUpgradeApi::getJPrestaAccountKey();
        $infos['jpresta_ps_token'] = JprestaUpgradeApi::getPrestashopToken();
        $infos['jpresta_ps_type'] = JprestaUpgradeApi::getPrestashopType();
        $infos['jpresta_clone_detected'] = JprestaUpgradeApi::getPrestashopIsClone();
        $infos['module_name'] = $this->module->name;
        $infos['request_uri'] = $_SERVER['REQUEST_URI'];
        $infos['migration_pcu2sp_link'] = $this->context->link->getAdminLink('AdminJprestaMigPCU2SP');
        $infos['can_migrate'] = version_compare(_PS_VERSION_, "1.7.1.0", ">=");
        $infos['module'] = JprestaUpgradeApi::getModuleOrThemeInfosByName($moduleName);

        $this->context->smarty->assign($infos);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_row.tpl');
    }

    public function renderList()
    {
        // Refresh the list (no cache)
        try {
            JprestaUpgradeApi::getLicenses(false);
        }
        catch(Exception $e) {
            $this->errors[] = $this->module->l('Cannot retrieve your licenses. If the problem persists, detach your JPresta Account Key and attach it again.', 'jprestaupgrade') . ': ' . $e->getMessage();
            JprestaUpgradeUtils::addLog('Cannot retrieve your licenses : '. $e->getMessage() . ". ". JprestaUpgradeUtils::jTraceEx($e), 2);
        }

        // Variable for smarty
        $infos = array();
        $infos['jpresta_account_key'] = JprestaUpgradeApi::getJPrestaAccountKey();
        $infos['jpresta_ps_token'] = JprestaUpgradeApi::getPrestashopToken();
        $infos['jpresta_ps_type'] = JprestaUpgradeApi::getPrestashopType();
        $infos['jpresta_clone_detected'] = JprestaUpgradeApi::getPrestashopIsClone();
        $infos['module_name'] = $this->module->name;
        $infos['request_uri'] = $_SERVER['REQUEST_URI'];
        $infos['migration_pcu2sp_link'] = $this->context->link->getAdminLink('AdminJprestaMigPCU2SP');
        $infos['can_migrate'] = version_compare(_PS_VERSION_, "1.7.1.0", ">=");
        $infos['jpresta_modules'] = JprestaUpgradeApi::getModulesOrThemesInfos();

        $this->context->smarty->assign($infos);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/content.tpl');
    }

    /**
     * @throws PrestaShopException
     */
    private function upgradeModule($moduleName)
    {
        $downloadLink = null;
        $newVersion = null;
        $infos = JprestaUpgradeApi::getModuleOrThemeInfosByName($moduleName);
        if (isset($infos['license'])
            && isset($infos['license']['download'])
            && isset($infos['license']['download']['link'])
            && $infos['license']['download']['can_upgrade']) {
            $downloadLink = $infos['license']['download']['link'];
            $newVersion = $infos['license']['download']['version'];
        }
        $upgrader = new JprestaModuleUpgrader();
        $fullyInstalled = $upgrader->upgradeModule($moduleName, $downloadLink, $newVersion);
        $this->errors = array_merge($this->errors, $upgrader->errors);
        $this->confirmations = array_merge($this->confirmations, $upgrader->confirmations);
        return $fullyInstalled;
    }

    private function upgradeTheme($themeName)
    {
        $themeInfos = JprestaUpgradeApi::getModuleOrThemeInfosByName($themeName);
        if (!$themeInfos
            || !isset($themeInfos['license'])
            || !isset($themeInfos['license']['download'])
            || !isset($themeInfos['license']['download']['link'])
            || !$themeInfos['license']['download']['can_upgrade']) {
            $this->jsonError("$themeName cannot be upgraded");
            return;
        }
        $downloadLink = $themeInfos['license']['download']['link'];
        $newVersion = $themeInfos['license']['download']['version'];

        $fileSystem = new Filesystem();
        $themeFile = $fileSystem->tempnam(_PS_CACHE_DIR_, 'jup');
        $themeDir = _PS_ALL_THEMES_DIR_ . $themeName;
        $themeDirTmp = _PS_CACHE_DIR_ . $themeName . '-v' . $newVersion . '-' . (new DateTime())->format('Y-m-d_H-i-s');
        $themeDirBackup = _PS_ALL_THEMES_DIR_ . 'backups/' . $themeName . '-backup-' . (new DateTime())->format('Y-m-d_H-i-s');

        try {
            // 1) Download the ZIP file
            JprestaUpgradeUtils::downloadFile($downloadLink, $themeFile);

            // 2) Extract files in temporary directory under all theme directory <theme_name>_vx.x.x_YYYYMMDD_HHmmss
            $fileSystem->mkdir($themeDirTmp);
            $this->extractThemeFiles($themeFile, $themeDirTmp);

            // 3) If no error, copy files of the current theme to <theme_name>_backup_YYYYMMDD_HHmmss
            $fileSystem->mirror($themeDir, $themeDirBackup, null, ['override' => true, 'copy_on_windows' => true]);

            // 4) If no error, copy files from <theme_name>_vx.x.x_YYYYMMDD_HHmmss to <theme_name>
            $fileSystem->mirror($themeDirTmp, $themeDir, null, ['override' => true, 'copy_on_windows' => true]);

            // 5) Update cached informations about the theme
            $serviceLocator = new ServiceLocator();
            $configuration = $serviceLocator::get('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');
            foreach (Shop::getCompleteListOfShopsID() as $id_shop) {
                $confFile = $configuration->get('_PS_CONFIG_DIR_') . 'themes/' . $themeName . '/shop' . $id_shop . '.json';
                if ($fileSystem->exists($confFile)) {
                    try {
                        $fileSystem->remove($confFile);
                    } catch (Exception $e) {
                        $this->warnings[] = "Cannot refresh the cache for shop #$id_shop, try to delete the file $confFile manually";
                    }
                }
            }

            $this->jsonConfirmation("$themeName has been upgraded to $newVersion");
            $this->informations[] = "A backup of current version has been made in $themeDirBackup";

        } catch (Exception $e) {
            // In case of error, restore previous files and delete temp files

            $this->jsonError('An error occured during the upgrade of theme ' . $themeName . ' v'.$newVersion.': ' . $e->getMessage() . ' (more details in logs)');
            JprestaUpgradeUtils::addLog('An error occured during the upgrade of theme ' . $themeName . ' v'.$newVersion.': '. $e->getMessage() . ". ". JprestaUpgradeUtils::jTraceEx($e), 2);

            if (file_exists($themeDirBackup)) {
                try {
                    if (file_exists($themeDir)) {
                        $fileSystem->remove($themeDir, 30);
                    }
                    $fileSystem->rename($themeDirBackup, $themeDir);
                    $this->informations[] = "The backup of the theme $themeName has been restored";
                } catch (Exception $e) {
                    $this->errors[] = "Sorry, I was not able to restore the backup $themeDirBackup into $themeDir, try to do it manually.";
                }
            }
        } finally {
            try {
                $fileSystem->remove($themeDirTmp);
                $fileSystem->remove($themeFile);
            } catch (Exception $e) {
                // Ignore
            }
        }
    }

    private function extractThemeFiles($zipFile, $toDir)
    {
        $zip = new ZipArchive();
        $res = $zip->open($zipFile);
        if ($res !== true) {
            throw new PrestaShopException('Cannot open zip file: ' . $res);
        }
        try {
            $filesToExtract = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (!JprestaUpgradeUtils::startsWith($filename, 'dependencies')
                    && $filename !== 'assets/css/custom.css'
                    && $filename !== 'assets/js/custom.js'
                ) {
                    $filesToExtract[] = $filename;
                }
            }
            if (!$zip->extractTo($toDir, $filesToExtract)) {
                throw new PrestaShopException('Cannot extract zip file: ' . $zipFile . ' / ' . $toDir);
            }
        } finally {
            $zip->close();
        }
    }
}
