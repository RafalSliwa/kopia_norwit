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
use FacebookProductAd\Common\fileClass;
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\exclusionProduct;
use FacebookProductAd\Models\Reporting;
use FacebookProductAd\ModuleLib\moduleReporting;
use FacebookProductAd\ModuleLib\moduleTools;
use FacebookProductAd\Xml\xmlStrategy;

class adminGenerate implements adminInterface
{
    /**
     * @var array : array for all parameters provided to generate XMl files
     */
    protected static $aParamsForXml = [];

    /**
     * method generate data feed content
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return array
     */
    public function run($sType, $aParam = [])
    {
        // set variables
        $aData = [];

        switch ($sType) {
            case 'xml': // use case - generate XML file
            case 'flyOutput': // use case - generate XML file on fly output
            case 'cron': // use case - generate XML file via the cron execution
                // execute match function
                $aData = call_user_func_array([$this, 'generate' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aData;
    }

    /**
     * method generate an XML file
     *
     * @param array $aPost
     *
     * @return array
     */
    private function generateXml(array $aPost = null)
    {
        // set
        $aAssign = [];
        $aProducts = [];
        $aCombinations = [];
        $iShopId = 0;
        $iLangId = 0;
        $sLangIso = '';
        $sCountryIso = '';
        $sCurrencyIso = '';
        $sFilename = '';
        $bExcludedProduct = false;
        $iFloor = 0;
        $iStep = 0;
        $iTotal = 0;
        $iProcess = 0;

        if (empty(self::$aParamsForXml)) {
            self::$aParamsForXml = moduleConfiguration::FPA_PARAM_FOR_XML;
        }

        try {
            foreach (self::$aParamsForXml as $sParamName) {
                $mValue = \Tools::getValue($sParamName);
                if (\Tools::getValue($sParamName) !== false) {
                    $$sParamName = $mValue;
                } else {
                    throw new \Exception(\FacebookProductAd::$oModule->l('One or more of the required parameters are not provided, please check the list in the current class', 'adminGenerate') . '.', 800);
                }
            }
            // detect if we force the reporting or not
            $bForceReporting = \Tools::getValue('bReporting');
            $bForceReporting = ($bForceReporting !== false) ? $bForceReporting : \FacebookProductAd::$conf['FPA_REPORTING'];

            // set params
            $aParams = [
                'bExport' => \FacebookProductAd::$conf['FPA_EXPORT_MODE'],
                'iShopId' => (int) $iShopId,
                'iLangId' => (int) $iLangId,
                'sLangIso' => $sLangIso,
                'sCountryIso' => $sCountryIso,
                'sCurrencyIso' => $sCurrencyIso,
                'sFpaLink' => \FacebookProductAd::$conf['FPA_LINK'],
                'iFloor' => (int) $iFloor,
                'iStep' => (int) $iStep,
                'iTotal' => (int) $iTotal,
                'iProcess' => (int) $iProcess,
                'bOutput' => \Tools::getValue('bOutput'),
                'bExcludedProduct' => exclusionProduct::isExcludedProduct(),
            ];

            if ($iFloor == 0) {
                $oUpdate = adminUpdate::create();
                $oUpdate->run('customLabelDate');

                // Reforce the custom label product assocation according to the option
                if (!empty(\FacebookProductAd::$conf['FPA_CL_AUTO_UPDATE'])) {
                    $oUpdate->run('customCheck');
                }
            }

            // get the XMl strategy
            $oXmlStrategy = new xmlStrategy($aParams);

            // composition of File Obj into XMlStrategy
            $oXmlStrategy->setFile(fileClass::create());

            Reporting::cleanTable(\Tools::strtoupper($sLangIso) . '_' . $sCountryIso . '_' . $sCurrencyIso, \FacebookProductAd::$iShopId);
            // detect if this is the first step
            if ((int) $iFloor == 0) {
                // reset the XMl file
                $oXmlStrategy->write(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilename, '');

                // create header
                $oXmlStrategy->header();
            }

            // load products
            $aProducts = $oXmlStrategy->loadProduct(\FacebookProductAd::$conf['FPA_P_COMBOS'], $bExcludedProduct);

            foreach ($aProducts as $aProduct) {
                // get the instance of the product
                $oProduct = new \Product((int) $aProduct['id'], true, (int) $iLangId);

                // check if validate product
                if (\Validate::isLoadedObject($oProduct) && $oProduct->active && (($oProduct->available_for_order)|| empty($oProduct->available_for_order))) {
                    // define the strategy
                    $sXmlProductType = $oProduct->hasAttributes() && !empty(\FacebookProductAd::$conf['FPA_P_COMBOS']) ? 'Combination' : 'Product';

                    // set the matching object
                    if (\FacebookProductAd::$bAdvancedPack && class_exists('AdvancedPack') 
                    && \AdvancedPack::isValidPack($oProduct->id)) {
                        $oXmlStrategy->get('product', $aParams);
                    } else {
                        $oXmlStrategy->get($sXmlProductType, $aParams);
                    }

                    // check if combinations
                    $aCombinations = $oXmlStrategy->hasCombination($oProduct->id, $bExcludedProduct);

                    foreach ($aCombinations as $aCombination) {
                        $oXmlStrategy->buildProductXml($oXmlStrategy->data, $oProduct, $aCombination);
                    }
                }
            }

            // get the number of products really processed
            $aAssign['process'] = (int) ($iProcess + $oXmlStrategy->getProcessedProduct());

            // detect if the last step
            if (((int) $iFloor + (int) $iStep) >= $iTotal) {
                $oXmlStrategy->footer();

                // store the nb of products really processed by the export action
                moduleReporting::create()->set('counter', ['products' => $aAssign['process']]);

                // define the status of the feed generation
                $aAssign['bContinueStatus'] = false;
                $aAssign['bFinishStatus'] = true;
            } else {
                // define the status of the feed generation
                $aAssign['bContinueStatus'] = true;
                $aAssign['bFinishStatus'] = false;
            }

            // write
            $oXmlStrategy->write(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilename, $oXmlStrategy->sContent, false, true);

            // merge reporting file's content + current reporting
            $aReporting = moduleReporting::create()->mergeData();

            // write reporting file by country and currency
            if (!empty($aReporting)) {
                Reporting::addReporting(\Tools::strtoupper($sLangIso) . '_' . $sCountryIso . '_' . $sCurrencyIso, $aReporting, \FacebookProductAd::$iShopId);
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
            $aErrorParam = ['msg' => $e->getMessage(), 'code' => $e->getCode()];

            if (moduleConfiguration::FPA_DEBUG) {
                $aErrorParam['file'] = $e->getFile();
                $aErrorParam['trace'] = $e->getTraceAsString();
            }
            $aAssign['aErrors'][] = $aErrorParam;
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method generate the XML feed by the fly output
     *
     * @param array $aPost
     *
     * @return array
     */
    private function generateFlyOutput(array $aPost = null)
    {
        $aAssign = [];

        try {
            // get the token
            $sToken = \Tools::getValue('token');
            if (
                !empty(\FacebookProductAd::$conf['FPA_FEED_TOKEN'])
                && $sToken != \FacebookProductAd::$conf['FPA_FEED_TOKEN']
            ) {
                throw new \Exception(\FacebookProductAd::$oModule->l('Invalid security token', 'adminGenerate') . '.', 810);
            }
            // get data feed params
            $_POST['iShopId'] = \Tools::getValue('id_shop');
            $_POST['iLangId'] = !empty(\Tools::getValue('fpa_lang_id')) ? \Tools::getValue('fpa_lang_id') : \Tools::getValue('id_lang');
            $_POST['sLangIso'] = moduleTools::getLangIso($_POST['iLangId']);
            $_POST['sCountryIso'] = \Tools::getValue('country');
            $_POST['sCurrencyIso'] = \Tools::getValue('currency_iso');
            $_POST['iFloor'] = 0;
            $_POST['iTotal'] = 0;
            $_POST['iStep'] = 0;
            $_POST['iProcess'] = 0;
            $_POST['bOutput'] = 1;
            $_POST['bExcludedProduct'] = exclusionProduct::isExcludedProduct();

            // set the filename
            $sFileSuffix = moduleTools::buildFileSuffix($_POST['sLangIso'], $_POST['sCountryIso']);
            $_POST['sFilename'] = \FacebookProductAd::$sFilePrefix . '.' . $sFileSuffix . '.xml';

            unset($sFileSuffix);

            // execute the generate XML function
            $this->generateXml();
        } catch (\Exception $e) {
            $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method generate the XML feed by the cron execution
     *
     * @param array $aPost
     *
     * @return array
     */
    private function generateCron(array $aPost = null)
    {
        $aAssign = [];
        $aLang = [];

        try {
            // get the token
            $sToken = \Tools::getValue('token');
            $sCountry = \Tools::getValue('country');
            $iLang = \Tools::getValue('fpa_lang_id');
            $sCurrency = \Tools::getValue('currency_iso');

            // get the token if necessary
            if (
                !empty(\FacebookProductAd::$conf['FPA_FEED_TOKEN'])
                && $sToken != \FacebookProductAd::$conf['FPA_FEED_TOKEN']
            ) {
                throw new \Exception(\FacebookProductAd::$oModule->l('Invalid security token', 'adminGenerate') . '.', 820);
            }

            // check if this is the first time execution of the CRON
            $_POST['aLangIds'] = \Tools::getValue('aLangIds');
            $_POST['iShopId'] = \Tools::getValue('id_shop');
            $_POST['sCurrencyIso'] = \Tools::getValue('currency_iso');

            // first execution
            if (empty($_POST['aLangIds'])) {
                if (
                    !empty($sCountry)
                    && !empty($iLang)
                ) {
                    $aDataFeedCron[] = moduleTools::getLangIso($iLang) . '_' . $sCountry . '_' . $sCurrency;
                } // use case - the general data feed cron URL
                else {
                    // get selected data feed
                    $aDataFeedCron = \FacebookProductAd::$conf['FPA_CHECK_EXPORT'];
                }

                foreach ($aDataFeedCron as $iKey => &$sLangIso) {
                    $sLangIso = \Tools::strtolower($sLangIso);
                }

                // set the available data feed
                foreach (\FacebookProductAd::$aAvailableLanguages as $aLanguage) {
                    // set the cookie id lang to get the good language
                    \Context::getContext()->cookie->id_lang = $aLanguage['id_lang'];

                    // get the matching languages
                    foreach (moduleConfiguration::FPA_AVAILABLE_COUNTRIES[$aLanguage['iso_code']] as $sCountryIso => $aLocaleData) {
                        // Only if currency is installed
                        foreach ($aLocaleData['currency'] as $sCurrency) {
                            if (
                                in_array(\Tools::strtolower($aLanguage['iso_code'] . '_' . $sCountryIso . '_' . $sCurrency), $aDataFeedCron)
                                && \Currency::getIdByIsoCode($sCurrency)
                            ) {
                                $aLang[] = $aLanguage['iso_code'] . '_' . $sCountryIso . '_' . $sCurrency;
                            }
                        }
                    }
                }

                list($sLangIso, $sCountryIso, $sCurrency) = explode('_', $aLang[0]);
                $_POST['iLangId'] = moduleTools::getLangId($sLangIso);
                $_POST['iCurrentLang'] = 0;
                $_POST['sLangIso'] = $sLangIso;
                $_POST['sCountryIso'] = $sCountryIso;
                $_POST['sCurrency'] = $sCurrency;
                $_POST['iStep'] = \FacebookProductAd::$conf['FPA_AJAX_CYCLE'];
                $_POST['iFloor'] = 0;
                $_POST['iProcess'] = 0;
                $_POST['bExcludedProduct'] = exclusionProduct::isExcludedProduct();

                // get the total products to export
                $_POST['iTotal'] = moduleDao::getProductIds($_POST['iShopId'], (bool) \FacebookProductAd::$conf['FPA_EXPORT_MODE'], true);

                // set the filename
                $sFileSuffix = moduleTools::buildFileSuffix($_POST['sLangIso'], $_POST['sCountryIso'], $_POST['iShopId']);
                $_POST['sFilename'] = \FacebookProductAd::$sFilePrefix . '.' . $sFileSuffix . '.xml';
                unset($sFileSuffix);

                // get lang
                $_POST['aLangIds'] = $aLang;
            } else {
                $_POST['iCurrentLang'] = \Tools::getValue('iCurrentLang');
                $_POST['aLangIds'] = \Tools::getValue('aLangIds');

                list($sLangIso, $sCountryIso, $sCurrencyIso) = explode('_', $_POST['aLangIds'][$_POST['iCurrentLang']]);

                // get data feed params
                $_POST['iLangId'] = moduleTools::getLangId($sLangIso);
                $_POST['sLangIso'] = $sLangIso;
                $_POST['sCountryIso'] = $sCountryIso;
                $_POST['sCurrencyIso'] = $sCurrencyIso;
                $_POST['iFloor'] = \Tools::getValue('iFloor');
                $_POST['iTotal'] = \Tools::getValue('iTotal');
                $_POST['iStep'] = \Tools::getValue('iStep');
                $_POST['iProcess'] = \Tools::getValue('iProcess');
                $_POST['bExcludedProduct'] = exclusionProduct::isExcludedProduct();

                // set the filename
                $sFileSuffix = moduleTools::buildFileSuffix(
                    $_POST['sLangIso'],
                    $_POST['sCountryIso'],
                    $_POST['iShopId']
                );
                $_POST['sFilename'] = \FacebookProductAd::$sFilePrefix . '.' . $sFileSuffix . '.xml';
                unset($sFileSuffix);
            }

            // execute the generate XML function
            $aContent = $this->generateXml();

            if (empty($aContent['assign']['aErrors'])) {
                // handle the cron URL
                $sCronUrl = \Context::getContext()->link->getModuleLink(moduleConfiguration::FPA_MODULE_SET_NAME, moduleConfiguration::FPA_CTRL_CRON, ['id_shop' => \FacebookProductAd::$iShopId]);

                // check if the feed protection is activated
                if (!empty($sToken)) {
                    $sCronUrl .= '&token=' . $sToken;
                }

                // set the base cron URL
                $sCronUrl .= '&aLangIds[]=' . implode('&aLangIds[]=', $_POST['aLangIds'])
                    . '&iTotal=' . (int) $_POST['iTotal']
                    . '&iStep=' . (int) $_POST['iStep']
                    . '&bExcludedProduct=' . $_POST['bExcludedProduct'];

                if (
                    !empty($aContent['assign']['bContinueStatus'])
                    && empty($aContent['assign']['bFinishStatus'])
                ) {
                    $_POST['iFloor'] += $_POST['iStep'];
                    $_POST['iProcess'] = $aContent['assign']['process'];
                    // header location
                    header('Location: ' . $sCronUrl . '&iCurrentLang=' . $_POST['iCurrentLang'] . '&iFloor=' . $_POST['iFloor'] . '&iProcess=' . $_POST['iProcess']);
                    exit;
                } elseif (
                    empty($aContent['assign']['bContinueStatus'])
                    && !empty($aContent['assign']['bFinishStatus'])
                    && isset($_POST['aLangIds'][$_POST['iCurrentLang'] + 1])
                ) {
                    // header location
                    header('Location: ' . $sCronUrl . '&iCurrentLang=' . ($_POST['iCurrentLang'] + 1) . '&iFloor=0&iProcess=0');
                    exit;
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);

            $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * create() method set singleton
     *
     * @category admin collection
     *
     * @return mixed
     */
    public static function create()
    {
        static $oUpdate;

        if (null === $oUpdate) {
            $oUpdate = new adminGenerate();
        }

        return $oUpdate;
    }
}
