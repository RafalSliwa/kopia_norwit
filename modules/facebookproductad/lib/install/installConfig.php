<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Install;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Configuration\moduleConfiguration;

class installConfig implements installInterface
{
    /**
     * method install of module
     *
     * @param mixed $mParam
     *
     * @return mixed $bReturn : true => validate install, false => invalidate install
     */
    public static function install($mParam = null)
    {
        // declare return
        $bReturn = true;

        // log jam to debug appli
        if (moduleConfiguration::FPA_LOG_JAM_CONFIG) {
            $bReturn = moduleConfiguration::FPA_LOG_JAM_CONFIG;
        } else {
            if (empty($mParam['bHookOnly'])) {
                // update each constant used in module admin & display
                foreach (moduleConfiguration::getConfVar() as $sKeyName => $mVal) {
                    if (!\Configuration::updateValue($sKeyName, $mVal)) {
                        $bReturn = false;
                    }
                }
            }
            if (empty($mParam['bConfigOnly'])) {
                // register each hooks
                foreach (moduleConfiguration::FPA_HOOKS as $aHook) {
                    if (!self::isHookInstalled($aHook['name'], \FacebookProductAd::$oModule->id)) {
                        if (!\FacebookProductAd::$oModule->registerHook($aHook['name'])) {
                            $bReturn = false;
                        }
                    }
                }
            }
        }
        unset($mParam);

        return $bReturn;
    }

    /**
     * method uninstall of module
     *
     * @param mixed $mParam
     *
     * @return mixed $bReturn : true => validate uninstall, false => invalidate uninstall / uninstall admin tab
     */
    public static function uninstall($mParam = null)
    {
        // set return execution
        $bReturn = true;

        // log jam to debug appli
        if (moduleConfiguration::FPA_LOG_JAM_CONFIG) {
            $bReturn = moduleConfiguration::FPA_LOG_JAM_CONFIG;
        } else {
            // delete global config
            foreach (moduleConfiguration::getConfVar() as $sKeyName => $mVal) {
                if (!\Configuration::deleteByName($sKeyName)) {
                    $bReturn = false;
                }
            }
        }
        unset($mParam);

        return $bReturn;
    }

    /**
     * method check if specific module is hooked to a specific hook
     *
     * @category admin / hook collection
     *
     * @uses
     *
     * @param string $sHookName
     * @param int $iModuleId
     *
     * @return bool
     */
    public static function isHookInstalled($sHookName, $iModuleId)
    {
        $bReturn = \FacebookProductAd::$oModule->isRegisteredInHook($sHookName);

        return $bReturn;
    }
}
