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

namespace FacebookProductAd\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\ModuleLib\moduleTools;

class adminController extends baseController
{
    /**
     * @param array $aParams
     */
    public function __construct(array $aParams = null)
    {
        // defines type to execute
        // use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
        // use case : key sAction sent in POST mode (form or ajax query posted ).
        $sAction = (!\Tools::getIsset('sAction') || (\Tools::getIsset('sAction') && 'display' == \Tools::getValue('sAction'))) ? (\Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'display') : \Tools::getValue('sAction');

        // set action
        $this->setAction($sAction);

        // set type
        $this->setType();
    }

    /**
     *  method execute abstract derived admin object
     *
     * @param array $aRequest : request
     *
     * @return array $aDisplay : empty => false / not empty => true
     */
    public function run($aRequest)
    {
        // set
        $aDisplay = [];
        $aParams = [];

        switch (self::$sAction) {
            case 'display':
                $oAdminType = adminDisplay::create();

                moduleTools::getConfiguration();

                // update new module keys
                moduleTools::updateConfiguration();

                // use case - type not define => first page requested
                if (empty(self::$sType)) {
                    // update module version
                    \Configuration::updateValue('FPA_VERSION', \FacebookProductAd::$oModule->version);

                    // update module if necessary
                    $aParams['aUpdateErrors'] = \FacebookProductAd::$oModule->updateModule();
                }

                // get configuration options
                moduleTools::getConfiguration(['FPA_HOME_CAT', 'FPA_COLOR_OPT', 'FPA_SIZE_OPT', 'FPA_SHIP_CARRIERS', 'FPA_CHECK_EXPORT', 'FPA_PROD_EXCL']);

                // set js msg translation
                moduleTools::translateJsMsg();

                // set params
                $aParams['oJsTranslatedMsg'] = \json_encode(moduleConfiguration::getJsMessage());

                break;
            case 'update':
                $oAdminType = adminUpdate::create();

                break;
            case 'delete':
                $oAdminType = adminDelete::create();

                break;
            case 'generate':
                $oAdminType = adminGenerate::create();

                break;
            default:
                $oAdminType = false;

                break;
        }

        // process data to use in view (tpl)
        if (!empty($oAdminType)) {
            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oAdminType->run(parent::$sType, $aRequest);

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], $aParams, ['bAddJsCss' => true]);
            }

            // destruct
            unset($oAdminType);
        }

        return $aDisplay;
    }
}
