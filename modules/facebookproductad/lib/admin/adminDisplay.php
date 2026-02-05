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
use FacebookProductAd\Dao\customLabelDao;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Exclusion\exclusionRender;
use FacebookProductAd\Models\advancedExclusion;
use FacebookProductAd\Models\customLabelDynamicBestSales;
use FacebookProductAd\Models\customLabelDynamicCategories;
use FacebookProductAd\Models\customLabelDynamicFeature;
use FacebookProductAd\Models\customLabelDynamicLastProductOrder;
use FacebookProductAd\Models\customLabelDynamicNewProduct;
use FacebookProductAd\Models\customLabelDynamicPriceRange;
use FacebookProductAd\Models\customLabelTags;
use FacebookProductAd\Models\exclusionProduct;
use FacebookProductAd\Models\exportBrands;
use FacebookProductAd\Models\exportCategories;
use FacebookProductAd\Models\Feeds;
use FacebookProductAd\Models\googleTaxonomy;
use FacebookProductAd\Models\Reporting;
use FacebookProductAd\Models\tmpRules;
use FacebookProductAd\ModuleLib\moduleTools;
use FacebookProductAd\ModuleLib\moduleWarning;

class adminDisplay implements adminInterface
{
    /**
     * @var array : array for all flag ids used in option translation
     */
    protected $aFlagIds = [];

    /**
     * method display all configured data admin tabs
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return array
     */
    public function run($sType, $aParam = [])
    {
        // set variables
        $aDisplayData = [];

        if (empty($sType)) {
            $sType = 'tabs';
        }

        switch ($sType) {
            case 'tabs': // use case - display first page with all tabs
            case 'basics': // use case - display basics settings page
            case 'feed': // use case - display feed settings page
            case 'facebook': // use case - display facebook settings page
            case 'customLabel': // use case - display facebook custom label settings popup
            case 'customLabelProduct': // use case - display google custom label settings popup
            case 'feedList': // use case - display feed list settings page
            case 'reporting': // use case - display reporting settings page
            case 'reportingBox': // use case - display reporting fancybox
            case 'searchProduct': // use case - handle products autocomplete
            case 'advice': // use case - handle advice form
            case 'pixel': // use case - handle pixel form
            case 'shop': // use case - handle pixel form
            case 'newCustomFeed': // use case - handle new custom field
            case 'exclusionRule': // use case - handle the rules exclusion
            case 'excludeValue': // use case - the exclusion rules values
            case 'rulesSummary': // use case - exclusion rules summary
            case 'exclusionRuleProducts': // use case - the product concerned by an exclusion rules
            case 'consent': // use case - the consent tab
            case 'chats': // use case - the chats tab
            case 'log': // use case - the log form
            case 'crossSelling': // use case - the cross selling tab
                // execute match function
                $aDisplayData = call_user_func_array([$this, 'display' . ucfirst($sType)], [$aParam]);

                break;
            case 'taxonomy': // use case - display taxonomies
                // execute match function
                $aDisplayData = call_user_func_array([$this, 'displayFacebook'], [$aParam]);

                break;
            default:
                break;
        }
        // use case - generic assign
        if (!empty($aDisplayData)) {
            $aDisplayData['assign'] = array_merge($aDisplayData['assign'], $this->assign());
        }

        return $aDisplayData;
    }

    /**
     *  method assigns transverse data
     *
     * @return array
     */
    private function assign()
    {
        // set smarty variables
        $aAssign = [
            'sURI' => moduleTools::truncateUri('&sAction'),
            'sCtrlParamName' => moduleConfiguration::FPA_PARAM_CTRL_NAME,
            'sController' => moduleConfiguration::FPA_ADMIN_CTRL,
            'aQueryParams' => moduleConfiguration::getRequestParams(),
            'sDisplay' => \Tools::getValue('sDisplay'),
            'iCurrentLang' => intval(\FacebookProductAd::$iCurrentLang),
            'sCurrentLang' => \FacebookProductAd::$sCurrentLang,
            'sCurrentIso' => \Language::getIsoById(\FacebookProductAd::$iCurrentLang),
            'bDisplayAdvice' => \FacebookProductAd::$conf['FPA_DISP_ADVICE'],
            'faqLink' => 'http://faq.businesstech.fr',
            'sTs' => time(),
            'bAjaxMode' => (\FacebookProductAd::$sQueryMode == 'xhr' ? true : false),
            'sLoadingImg' => moduleConfiguration::FPA_URL_IMG . 'admin/bx_loader.gif',
            'sBigLoadingImg' => moduleConfiguration::FPA_URL_IMG . 'admin/ajax-loader.gif',
            'sHeaderInclude' => moduleTools::getTemplatePath('views/templates/admin/header.tpl'),
            'sErrorInclude' => moduleTools::getTemplatePath('views/templates/admin/error.tpl'),
            'sConfirmInclude' => moduleTools::getTemplatePath('views/templates/admin/confirm.tpl'),
            'bConfigureStep1' => \FacebookProductAd::$conf['FPA_CONF_STEP_1'],
            'bConfigureStep2' => \FacebookProductAd::$conf['FPA_CONF_STEP_2'],
            'bConfigureStep3' => \FacebookProductAd::$conf['FPA_CONF_STEP_3'],
            'bConfigureStep4' => \FacebookProductAd::$conf['FPA_CONF_STEP_4'],
            'sBusinessId' => \FacebookProductAd::$conf['FPA_BUSINESS_ID'],
            'hasActivatePixel' => \FacebookProductAd::$conf['FPA_ACTIVATE_PIXEL'],
            'useApi' => \FacebookProductAd::$conf['FPA_USE_API'],
            'hasWarning' => \FacebookProductAd::$conf['FPA_HAS_WARNING'],
            'hasAdvancedMatching' => \FacebookProductAd::$conf['FPA_ADVANCED_MATCHING'],
            'tokenApi' => \FacebookProductAd::$conf['FPA_TOKEN_API'],
            'pageViewApi' => \FacebookProductAd::$conf['FPA_API_PAGE_VIEW'],
            'moduleJsPath' => moduleConfiguration::FPA_URL_JS,
            'moduleCssPath' => moduleConfiguration::FPA_URL_CSS,
            'imagePath' => moduleConfiguration::FPA_URL_IMG,
            'useJs' => moduleConfiguration::FPA_USE_JS,
            'version' => \FacebookProductAd::$oModule->version,
            'moduleName' => \FacebookProductAd::$oModule->displayName,
            'number_of_feeds' => count(\FacebookProductAd::$aAvailableLangCurrencyCountry),
        ];

        return $aAssign;
    }

    /**
     * method displays admin's first page with all tabs
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayTabs(array $aPost = null)
    {
        // get support to use
        $iSupportToUse = moduleConfiguration::FPA_SUPPORT_BT;

        // set smarty variables
        $aAssign = [
            'sDocUri' => _MODULE_DIR_ . moduleConfiguration::FPA_MODULE_SET_NAME . '/',
            'sDocName' => 'readme_' . ((\FacebookProductAd::$sCurrentLang == 'fr') ? 'fr' : 'en') . '.pdf',
            'sContactUs' => !empty($iSupportToUse) ? moduleConfiguration::FPA_SUPPORT_URL . ((\FacebookProductAd::$sCurrentLang == 'fr') ? 'fr/contactez-nous' : 'en/contact-us') : moduleConfiguration::FPA_SUPPORT_URL . ((\FacebookProductAd::$sCurrentLang == 'fr') ? 'fr/ecrire-au-developpeur?id_product=' . moduleConfiguration::FPA_SUPPORT_ID : 'en/write-to-developper?id_product=' . moduleConfiguration::FPA_SUPPORT_ID),
            'sRateUrl' => !empty($iSupportToUse) ? moduleConfiguration::FPA_SUPPORT_URL . ((\FacebookProductAd::$sCurrentLang == 'fr') ? 'fr/modules-prestashop-reseaux-sociaux-facebook/50-module-prestashop-publicites-de-produits-facebook-pixel-facebook-0656272916497.html' : 'en/prestashop-modules-social-networks-facebook/50-prestashop-addon-facebook-product-ads-facebook-pixel-0656272916497.html') : moduleConfiguration::FPA_SUPPORT_URL . ((\FacebookProductAd::$sCurrentLang == 'fr') ? '/fr/ratings.php' : '/en/ratings.php'),
            'sCrossSellingUrl' => !empty($iSupportToUse) ? moduleConfiguration::FPA_SUPPORT_URL . '?utm_campaign=internal-module-ad&utm_source=banniere&utm_medium=' . moduleConfiguration::FPA_MODULE_SET_NAME : moduleConfiguration::FPA_SUPPORT_URL . \FacebookProductAd::$sCurrentLang . '/6_business-tech',
            'sCurrentIso' => \Language::getIsoById(\FacebookProductAd::$iCurrentLang),
        ];

        // check curl_init and file_get_contents to get the distant Facebook taxonomy file
        moduleWarning::create()->run('directive', 'allow_url_fopen', [], true);
        $bTmpStopExec = moduleWarning::create()->bStopExecution;
        moduleWarning::create()->bStopExecution = false;
        moduleWarning::create()->run('function', 'curl_init', [], true);
        if ($bTmpStopExec && moduleWarning::create()->bStopExecution) {
            $aAssign['bCurlAndContentStopExec'] = true;
        }

        // check if multi-shop configuration
        if (
            version_compare(_PS_VERSION_, '1.5', '>')
            && \Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
            && strpos(\Context::getContext()->cookie->shopContext, 'g-') !== false
        ) {
            $aAssign['bMultishopGroupStopExec'] = true;
        }

        // check if we hide the config
        if (
            !empty($aAssign['bFileStopExec'])
            || !empty($aAssign['bCurlAndContentStopExec'])
            || !empty($aAssign['bMultishopGroupStopExec'])
        ) {
            $aAssign['bHideConfiguration'] = true;
        }
        $aAssign['autocmp_js'] = __PS_BASE_URI__ . 'js/jquery/plugins/autocomplete/jquery.autocomplete.js';
        $aAssign['autocmp_css'] = __PS_BASE_URI__ . 'js/jquery/plugins/autocomplete/jquery.autocomplete.css';

        $aData = $this->displayBasics($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayFacebook($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayFeedList($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayReporting($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayPixel($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayNewCustomFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayConsent($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayShop($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayChats($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayLog($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        $aData = $this->displayCrossSelling($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        // assign all included templates files
        $aAssign['sHeaderBar'] = moduleTools::getTemplatePath('views/templates/admin/top.tpl');
        $aAssign['sBasicsInclude'] = moduleTools::getTemplatePath('views/templates/admin/basics.tpl');
        $aAssign['sFeedInclude'] = moduleTools::getTemplatePath('views/templates/admin/feed-settings.tpl');
        $aAssign['sFacebookInclude'] = moduleTools::getTemplatePath('views/templates/admin/settings.tpl');
        $aAssign['sFeedListInclude'] = moduleTools::getTemplatePath('views/templates/admin/feed-list.tpl');
        $aAssign['sReportingInclude'] = moduleTools::getTemplatePath('views/templates/admin/reporting-settings.tpl');
        $aAssign['sConsentInclude'] = moduleTools::getTemplatePath('views/templates/admin/consent-settings.tpl');
        $aAssign['sPixelInclude'] = moduleTools::getTemplatePath('views/templates/admin/pixel-settings.tpl');
        $aAssign['sTestingTools'] = moduleTools::getTemplatePath('views/templates/admin/testing-tools.tpl');
        $aAssign['sShopManagement'] = moduleTools::getTemplatePath('views/templates/admin/shop-management.tpl');
        $aAssign['sApiLog'] = moduleTools::getTemplatePath('views/templates/admin/log.tpl');
        $aAssign['sCrossSelling'] = moduleTools::getTemplatePath('views/templates/admin/cross-selling.tpl');
        $aAssign['sChatsConfig'] = moduleTools::getTemplatePath('views/templates/admin/chats-settings.tpl');
        $aAssign['sCustomFeed'] = moduleTools::getTemplatePath('views/templates/admin/new-custom-feed.tpl');
        $aAssign['sModuleVersion'] = \FacebookProductAd::$oModule->version;

        return [
            'tpl' => 'admin/body.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     *  method displays basic settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayBasics(array $aPost = null)
    {
        $aAssign = [
            'sDocUri' => _MODULE_DIR_ . moduleConfiguration::FPA_MODULE_SET_NAME . '/',
            'sDocName' => 'readme_' . ((\FacebookProductAd::$sCurrentLang == 'fr') ? 'fr' : 'en') . '.pdf',
            'sLink' => (!empty(\FacebookProductAd::$conf['FPA_LINK']) ? \FacebookProductAd::$conf['FPA_LINK'] : \FacebookProductAd::$sHost),
            'sPrefixId' => \FacebookProductAd::$conf['FPA_ID_PREFIX'],
            'iProductPerCycle' => \FacebookProductAd::$conf['FPA_AJAX_CYCLE'],
            'sImgSize' => \FacebookProductAd::$conf['FPA_IMG_SIZE'],
            'aHomeCatLanguages' => \FacebookProductAd::$conf['FPA_HOME_CAT'],
            'iHomeCatId' => \FacebookProductAd::$conf['FPA_HOME_CAT_ID'],
            'bAddCurrency' => \FacebookProductAd::$conf['FPA_ADD_CURRENCY'],
            'iAdvancedProductName' => \FacebookProductAd::$conf['FPA_ADV_PRODUCT_NAME'],
            'iAdvancedProductTitle' => \FacebookProductAd::$conf['FPA_ADV_PROD_TITLE'],
            'bIncludeLangId' => \FacebookProductAd::$conf['FPA_ADD_LANG_ID'],
            'sFeedToken' => \FacebookProductAd::$conf['FPA_FEED_TOKEN'],
            'aImageTypes' => \ImageType::getImagesTypes('products'),
            'sCondition' => \FacebookProductAd::$conf['FPA_COND'],
            'aAvailableCondition' => moduleTools::getConditionType(),
            'bAddImages' => \FacebookProductAd::$conf['FPA_ADD_IMAGES'],
            'bIncludeProductPriceTax' => \FacebookProductAd::$conf['FPA_PROD_PRICE_TAX'],
            'excludedWords' => '',
            'feedTagId' => \FacebookProductAd::$conf['FPA_FEED_PREF_ID'],
        ];

        $excluded_words = json_decode(\FacebookProductAd::$conf['FPA_EXCLUDED_WORDS'], true);
        if (!empty($excluded_words)) {
            if (is_array($excluded_words)) {
                foreach ($excluded_words as $word) {
                    $aAssign['excludedWords'] .= $word;

                    if ($word != end($excluded_words)) {
                        $aAssign['excludedWords'] .= ',';
                    }
                }
            }
        }

        $aCategories = \Category::getCategories(intval(\FacebookProductAd::$iCurrentLang), false);
        $aAssign['aHomeCat'] = moduleTools::recursiveCategoryTree($aCategories, [], current(current($aCategories)), (int) \Configuration::get('PS_HOME_CATEGORY'));
        unset($aCategories);

        // get all active languages in order to loop on field form which need to manage translation
        $aAssign['aLangs'] = \Language::getLanguages();

        // use case - detect if home category name has been filled
        $aAssign['aHomeCatLanguages'] = $this->getDefaultTranslations('FPA_HOME_CAT', 'HOME_CAT_NAME');

        if (is_array($aAssign['aHomeCatLanguages'])) {
            foreach ($aAssign['aLangs'] as $aLang) {
                if (!isset($aAssign['aHomeCatLanguages'][$aLang['id_lang']])) {
                    $aAssign['aHomeCatLanguages'][$aLang['id_lang']] = moduleConfiguration::FPA_HOME_CAT_NAME['en'];
                }
            }
        }

        return [
            'tpl' => 'admin/basics.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays feeds settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayFeed(array $aPost = null)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'bExportMode' => \FacebookProductAd::$conf['FPA_EXPORT_MODE'],
            'bExportOOS' => \FacebookProductAd::$conf['FPA_EXPORT_OOS'],
            'bExcludeNoEan' => \FacebookProductAd::$conf['FPA_EXC_NO_EAN'],
            'bExcludeNoMref' => \FacebookProductAd::$conf['FPA_EXC_NO_MREF'],
            'iMinPrice' => \FacebookProductAd::$conf['FPA_MIN_PRICE'],
            'bProductCombos' => \FacebookProductAd::$conf['FPA_P_COMBOS'],
            'iDescType' => \FacebookProductAd::$conf['FPA_P_DESCR_TYPE'],
            'aDescriptionType' => moduleTools::getDescriptionType(),
            'iIncludeStock' => \FacebookProductAd::$conf['FPA_INC_STOCK'],
            'bIncludeTagAdult' => \FacebookProductAd::$conf['FPA_INC_TAG_ADULT'],
            'handleTagAdultLink' => \Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=adult',
            'sIncludeSize' => \FacebookProductAd::$conf['FPA_INC_SIZE'],
            'aAttributeGroups' => \AttributeGroup::getAttributesGroups((int) \FacebookProductAd::$oContext->cookie->id_lang),
            'aFeatures' => \Feature::getFeatures((int) \FacebookProductAd::$oContext->cookie->id_lang),
            'aSizeOptions' => \FacebookProductAd::$conf['FPA_SIZE_OPT'],
            'aColorOptions' => \FacebookProductAd::$conf['FPA_COLOR_OPT'],
            'sIncludeColor' => \FacebookProductAd::$conf['FPA_INC_COLOR'],
            'aExcludedProducts' => \FacebookProductAd::$conf['FPA_PROD_EXCL'],
            'bIncludeMaterial' => \FacebookProductAd::$conf['FPA_INC_MATER'],
            'handleTagMaterialLink' => \Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=material',
            'bIncludePattern' => \FacebookProductAd::$conf['FPA_INC_PATT'],
            'handleTagPatternLink' => \Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=pattern',
            'bIncludeGender' => \FacebookProductAd::$conf['FPA_INC_GEND'],
            'handleTagGenderLink' => \Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=gender',
            'bIncludeAge' => \FacebookProductAd::$conf['FPA_INC_AGE'],
            'handleTagAgeGroupeLink' => \Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=agegroup',
            'bShippingUse' => \FacebookProductAd::$conf['FPA_SHIPPING_USE'],
            'sGtinPreference' => \FacebookProductAd::$conf['FPA_GTIN_PREF'],
            'aShippingCarriers' => [],
            'bRewriteNumAttrValues' => \FacebookProductAd::$conf['FPA_URL_NUM_ATTR_REWRITE'],
            'bUrlInclAttrId' => \FacebookProductAd::$conf['FPA_URL_ATTR_ID_INCL'],
            'bUrlError' => \FacebookProductAd::$conf['FPA_URL_PROD_ERROR'],
            'sComboSeparator' => \FacebookProductAd::$conf['FPA_COMBO_SEPARATOR'],
            'bClAutoUpdate' => \FacebookProductAd::$conf['FPA_CL_AUTO_UPDATE'],
            'aTags' => customLabelTags::getTags(\FacebookProductAd::$iShopId, null, null, null),
            'bIncludeAttributeValue' => \FacebookProductAd::$conf['FPA_INCL_ATTR_VALUE'],
            'bIncludeAnchor' => \FacebookProductAd::$conf['FPA_INCL_ANCHOR'],
        ];

        // handle product IDs and Names list to format them for the autocomplete feature
        if (!empty($aAssign['aExcludedProducts'])) {
            $sProdIds = '';
            $sProdNames = '';

            foreach ($aAssign['aExcludedProducts'] as $iKey => $sProdId) {
                $aProdIds = explode('¤', $sProdId);
                $oProduct = new \Product((int)$aProdIds[0], false, \FacebookProductAd::$iCurrentLang);

                // check if we export with combinations
                if (!empty($aProdIds[1])) {
                    $oProduct->name .= moduleTools::getProductCombinationName((int)$aProdIds[1], \FacebookProductAd::$iCurrentLang, \FacebookProductAd::$iShopId);
                }

                $sProdIds .= $sProdId . '-';
                $sProdNames .= $oProduct->name . '||';

                $aAssign['aProducts'][] = [
                    'id' => $sProdId,
                    'name' => $oProduct->name,
                    'attrId' => $aProdIds[1],
                    'stringIds' => $sProdId,
                ];
                unset($oProduct);
            }
            $aAssign['sProductIds'] = $sProdIds;
            $aAssign['sProductNames'] = str_replace('"', '', $sProdNames);
            unset($sProdIds);
            unset($sProdNames);
        }

        if (isset(\FacebookProductAd::$conf['aColorOptions']['attribute'])) {
            $aAssign['aColorOptions']['attribute'] = !empty(\FacebookProductAd::$conf['FPA_COLOR_OPT']['attribute']) ? \FacebookProductAd::$conf['FPA_COLOR_OPT']['attribute'] : [0];
        }

        if (isset(\FacebookProductAd::$conf['aColorOptions']['feature'])) {
            $aAssign['aColorOptions']['feature'] = !empty(\FacebookProductAd::$conf['FPA_COLOR_OPT']['feature']) ? \FacebookProductAd::$conf['FPA_COLOR_OPT']['feature'] : [0];
        }

        if (isset(\FacebookProductAd::$conf['aSizeOptions']['attribute'])) {
            $aAssign['aSizeOptions']['attribute'] = !empty(\FacebookProductAd::$conf['FPA_SIZE_OPT']['attribute']) ? \FacebookProductAd::$conf['FPA_SIZE_OPT']['attribute'] : [0];
        }

        if (isset(\FacebookProductAd::$conf['aSizeOptions']['feature'])) {
            $aAssign['aSizeOptions']['feature'] = !empty(\FacebookProductAd::$conf['FPA_SIZE_OPT']['feature']) ? \FacebookProductAd::$conf['FPA_SIZE_OPT']['feature'] : [0];
        }

        // get available categories and manufacturers
        $aCategories = \Category::getCategories(intval(\FacebookProductAd::$iCurrentLang), false);
        $aBrands = \Manufacturer::getManufacturers();

        $aStartCategories = current($aCategories);
        $aFirst = current($aStartCategories);
        $iStart = (int) \Category::getRootCategory()->id;

        // get registered categories and brands
        $aIndexedCategories = [];
        $aIndexedBrands = [];

        // use case - get categories or brands according to the export mode
        if (\FacebookProductAd::$conf['FPA_EXPORT_MODE'] == 1) {
            $aIndexedBrands = exportBrands::getFpaBrands(\FacebookProductAd::$iShopId);
        } else {
            $aIndexedCategories = exportCategories::getFpaCategories(\FacebookProductAd::$iShopId);
        }

        // format categories and brands
        $aAssign['aFormatCat'] = moduleTools::recursiveCategoryTree($aCategories, $aIndexedCategories, $aFirst, $iStart, null, true);
        $aAssign['aFormatBrands'] = moduleTools::recursiveBrandTree($aBrands, $aIndexedBrands);
        $aAssign['iShopCatCount'] = count($aAssign['aFormatCat']);
        $aAssign['iMaxPostVars'] = ini_get('max_input_vars');

        unset($aIndexedCategories);
        unset($aIndexedBrands);
        unset($aCategories);
        unset($aBrands);

        $availableLanguage = moduleTools::getAvailableLanguages((int) \FacebookProductAd::$iShopId);
        $hasData = Feeds::hasSavedData(\FacebookProductAd::$iShopId);

        $freeProductShippingPrice = is_string(\FacebookProductAd::$conf['FPA_FREE_PROD_PRICE_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf['FPA_FREE_PROD_PRICE_SHIP_CARRIERS']) : [];
        $carrierNoTax = is_string(\FacebookProductAd::$conf['FPA_NO_TAX_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf['FPA_NO_TAX_SHIP_CARRIERS']) : [];
        $isFreeCarrier = is_string(\FacebookProductAd::$conf['FPA_FREE_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf['FPA_FREE_SHIP_CARRIERS']) : [];

        if (!empty($hasData)) {
            $availableFeed = Feeds::getAvailableFeeds((int) \FacebookProductAd::$iShopId);
            if (!empty($availableFeed)) {
                foreach ($availableLanguage as $lang) {
                    $current_feed_shop = Feeds::getFeedLangData($lang['iso_code'], (int) \FacebookProductAd::$iShopId);
                    foreach ($current_feed_shop as $feed) {
                        $iCountryId = \Country::getByIso(\Tools::strtolower($feed['iso_country']));
                        if (!empty($iCountryId)) {
                            $country = new \Country($iCountryId);
                            if (!empty($country->active)) {
                                $iCountryZone = \Country::getIdZone($iCountryId);
                                if (!empty($iCountryZone)) {
                                    $aCarriers = moduleTools::getAvailableCarriers((int) $iCountryZone);
                                    if (!empty($aCarriers)) {
                                        $id_currency = \Currency::getIdByIsoCode($feed['iso_currency']);
                                        $currency = new \Currency($id_currency);
                                        if (!empty($currency->iso_code)) {
                                            if (!array_key_exists($feed['iso_country'], $aAssign['aShippingCarriers'])) {
                                                $aAssign['aShippingCarriers'][$feed['iso_country']] = [
                                                    'name' => $country->name,
                                                    'carriers' => $aCarriers,
                                                    'shippingCarrierId' => (!empty(\FacebookProductAd::$conf['FPA_SHIP_CARRIERS'][$feed['iso_country']]) ? \FacebookProductAd::$conf['FPA_SHIP_CARRIERS'][$feed['iso_country']] : 0),
                                                    'noTaxCarrier' => (!empty($carrierNoTax) ? (isset($carrierNoTax[$feed['iso_country']]) ? $carrierNoTax[$feed['iso_country']] : 0) : 0),
                                                    'free' => (!empty($isFreeCarrier) ? (isset($isFreeCarrier[$feed['iso_country']]) ? $isFreeCarrier[$feed['iso_country']] : 0) : 0),
                                                    'productFree' => (!empty($freeProductShippingPrice) ? (isset($freeProductShippingPrice[$feed['iso_country']]) ? $freeProductShippingPrice[$feed['iso_country']] : 0) : 0),
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // use case for the exclusion rules value
        $aExclusionRules = advancedExclusion::getRules();
        $aAssign['aExclusionRules'] = moduleTools::getExclusionRulesName($aExclusionRules);

        return [
            'tpl' => 'admin/feed-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays Facebook settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayFacebook(array $aPost = null)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'aCountryTaxonomies' => moduleTools::getAvailableTaxonomyCountries(),
            'taxonomyController' => \Context::getContext()->link->getAdminLink('AdminTaxonomy'),
            'sFacebookCatListInclude' => moduleTools::getTemplatePath('views/templates/admin/category-list.tpl'),
            'aTags' => customLabelTags::getTags(\FacebookProductAd::$iShopId, null, null, null),
            'sUtmCampaign' => \FacebookProductAd::$conf['FPA_UTM_CAMPAIGN'],
            'sUtmSource' => \FacebookProductAd::$conf['FPA_UTM_SOURCE'],
            'sUtmMedium' => \FacebookProductAd::$conf['FPA_UTM_MEDIUM'],
        ];

        foreach ($aAssign['aCountryTaxonomies'] as $sIsoCode => &$aTaxonomy) {
            $aTaxonomy['countryList'] = implode(', ', $aTaxonomy['countries']);
            $aTaxonomy['updated'] = googleTaxonomy::checkTaxonomyUpdate($sIsoCode);
        }

        return [
            'tpl' => 'admin/settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom labels
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayCustomLabel(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';
        $aAssign = [
            'aCustomLabelType' => moduleConfiguration::FPA_CUSTOM_LABEL_TYPE,
            'aCustomBestType' => moduleConfiguration::FPA_CUSTOM_LABEL_BEST_TYPE,
            'aCustomBestPeriodType' => moduleConfiguration::FPA_CUSTOM_LABEL_BEST_PERIOD_TYPE,
            'aFeatureAvailable' => moduleDao::getFeature(),
            'sCurrency' => \Currency::getDefaultCurrency()->sign,
            'sUriAutoComplete' => !empty(\FacebookProductAd::$bCompare80) ? 'index.php?controller=AdminProducts&ajax=1&action=productsList' : 'ajax_products_list.php',
        ];

        // get available categories and manufacturers
        $aCategories = \Category::getCategories(intval(\FacebookProductAd::$iCurrentLang), false);
        $aBrands = \Manufacturer::getManufacturers();
        $aSuppliers = \Supplier::getSuppliers();

        $aStartCategories = current($aCategories);
        $aFirst = current($aStartCategories);
        $iStart = (int) \Category::getRootCategory()->id;

        // get registered categories and brands and suppliers
        $aIndexedCategories = [];
        $aIndexedBrands = [];
        $aIndexedSuppliers = [];

        // use case - get categories or brands or suppliers according to the id tag
        $iTagId = \Tools::getValue('iTagId');
        $aTag = [];

        if (!empty($iTagId)) {
            $aTag = customLabelTags::getTags(\FacebookProductAd::$iShopId, $iTagId, null, null);
            // manage categories association for each type tag using categories
            $aClManualIndexedCategories = customLabelTags::getTags(null, $iTagId, 'cats', 'category');
            $aClDynamicIndexedCategories = customLabelDynamicCategories::getDynamicCat($iTagId);

            // merge result for return good check box for each categories
            $aIndexedCategories = array_merge($aClManualIndexedCategories, $aClDynamicIndexedCategories);

            $aIndexedBrands = customLabelTags::getTags(null, $iTagId, 'brands', 'brand');
            $aIndexedSuppliers = customLabelTags::getTags(null, $iTagId, 'suppliers', 'supplier');
            $aIndexedProducts = customLabelTags::getTags(null, $iTagId, 'products', null);

            // handle product IDs and Names list to format them for the autocomplete feature
            if (!empty($aIndexedProducts)) {
                $sProdIds = '';
                $sProdNames = '';
                foreach ($aIndexedProducts as $iKey => $iProdId) {
                    if (!empty($iProdId)) {
                        $sProdIds .= $iProdId['id_product'] . '-';
                        $sProdNames .= $iProdId['product_name'] . '||';

                        $aAssign['aProducts'][] = [
                            'id' => $iProdId['id_product'],
                            'name' => $iProdId['product_name'],
                        ];
                    }
                }
                $aAssign['sProductIds'] = $sProdIds;
                $aAssign['sProductNames'] = $sProdNames;
            }

            $aFeatureSelected = customLabelDynamicFeature::getFeatureSave($iTagId);
            $sDateNewProduct = customLabelDynamicNewProduct::getDynamicNew($iTagId);
            $aBestSales = customLabelDynamicBestSales::getDynamicBestSales($iTagId);
            $aLastOrdered = customLabelDynamicLastProductOrder::getDynamicLastProductOrdered($iTagId);
            $aPriceRange = customLabelDynamicPriceRange::getDynamicPriceRange($iTagId);

            $aAssign['bActive'] = $aTag[0]['active'];
            $aAssign['customLabelSetPosition'] = $aTag[0]['custom_label_set_postion'];
            $aAssign['sDate'] = $aTag[0]['end_date'];
            $aAssign['iFeatureId'] = $aFeatureSelected['id_feature'];
            $aAssign['aProductIds'] = $aIndexedProducts;
            $aAssign['sDateNewPoduct'] = $sDateNewProduct['from_date'];

            // Use case for best sale
            $aAssign['fAmount'] = $aBestSales['amount'];
            $aAssign['sUnit'] = $aBestSales['unit'];

            if ($aBestSales['start_date'] != '0000-00-00 00:00:00') {
                $aAssign['sStartDate'] = $aBestSales['start_date'];
            }

            if ($aBestSales['end_date'] != '0000-00-00 00:00:00') {
                $aAssign['sEndDate'] = $aBestSales['end_date'];
            }

            if ($aLastOrdered['start_date'] != '0000-00-00 00:00:00') {
                $aAssign['sStartDateLastOrdered'] = $aLastOrdered['start_date'];
            }

            if ($aLastOrdered['end_date'] != '0000-00-00 00:00:00') {
                $aAssign['sEndDateLastOrdered'] = $aLastOrdered['end_date'];
            }

            // Use case for price range CL
            $aAssign['fPriceMin'] = $aPriceRange['price_min'];
            $aAssign['fPriceMax'] = $aPriceRange['price_max'];
        }

        // format categories and brands and suppliers
        $aAssign['aTag'] = (count($aTag) == 1 && isset($aTag[0])) ? $aTag[0] : $aTag;
        $aAssign['aFormatCat'] = moduleTools::recursiveCategoryTree($aCategories, $aIndexedCategories, $aFirst, $iStart);
        $aAssign['aFormatBrands'] = moduleTools::recursiveBrandTree($aBrands, $aIndexedBrands);
        $aAssign['aFormatSuppliers'] = moduleTools::recursiveSupplierTree($aSuppliers, $aIndexedSuppliers);
        $aAssign['iShopCatCount'] = count($aAssign['aFormatCat']);
        $aAssign['iMaxPostVars'] = ini_get('max_input_vars');
        $aAssign['labelPosition'] = moduleConfiguration::getCustomLabelPosition();
        $sProduct = '';

        // manage autocomplete
        $aProduct = \Product::getSimpleProducts(\FacebookProductAd::$iShopId);

        foreach ($aProduct as $key => $value) {
            // set the string for autocomplete
            $sProduct[$key] = $value;
        }

        $aAssign['sProduct'] = $sProduct;

        return [
            'tpl' => 'admin/custom-label.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays products are associated to the CL
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayCustomLabelProduct(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];

        $iTagId = \Tools::getValue('iTagId');

        foreach (moduleConfiguration::FPA_CUSTOM_LABEL_PRODUCT_FILTER as $aFilter) {
            $aProductIds = customLabelDao::getCustomLabelProductIds($iTagId, $aFilter);
            if (!empty($aProductIds)) {
                foreach ($aProductIds as $aProductId) {
                    if (is_array($aProductId)) {
                        $oProduct = new \Product((int) $aProductId['id_product'], true, \FacebookProductAd::$iCurrentLang);
                        $aAssign['aProduct'][(int) $aProductId['id_product']] = ['id' => $oProduct->id, 'name' => $oProduct->name];
                    }
                }
            }
        }

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/custom-label-products.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displayFeedList() method displays feed list
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayFeedList(array $aPost = null)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'iShopId' => \FacebookProductAd::$iShopId,
            'sFpaLink' => \FacebookProductAd::$conf['FPA_LINK'],
            'bReporting' => \FacebookProductAd::$conf['FPA_REPORTING'],
            'iTotalProductToExport' => moduleDao::getProductIds(\FacebookProductAd::$iShopId, (bool) \FacebookProductAd::$conf['FPA_EXPORT_MODE'], true, null, null, false, false),
            'iTotalProduct' => moduleDao::countProducts(\FacebookProductAd::$iShopId, (bool) \FacebookProductAd::$conf['FPA_P_COMBOS']),
            'aFeedFileList' => [],
            'aFlyFileList' => [],
            'bExcludedProduct' => exclusionProduct::isExcludedProduct(),
        ];

        $aAssign['aCronLang'] = (!empty(\FacebookProductAd::$conf['FPA_CHECK_EXPORT']) ? \FacebookProductAd::$conf['FPA_CHECK_EXPORT'] : []);

        // handle data feed file name
        if (!empty($aAssign['sFpaLink'])) {
            if (!empty(\FacebookProductAd::$aAvailableLangCurrencyCountry)) {
                foreach (\FacebookProductAd::$aAvailableLangCurrencyCountry as $aData) {
                    // check if file exist
                    $sFileSuffix = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso']);
                    $sFilePath = \FacebookProductAd::$sFilePrefix . '.' . $sFileSuffix . '.xml';

                    if (is_file(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath)) {
                        $aAssign['aFeedFileList'][] = [
                            'link' => $aAssign['sFpaLink'] . __PS_BASE_URI__ . $sFilePath,
                            'filename' => $sFilePath,
                            'filemtime' => date('d-m-Y H:i:s', filemtime(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath)),
                            'checked' => (in_array($aData['langIso'] . '_' . $aData['countryIso'] . '_' . $aData['currencyIso'], $aAssign['aCronLang']) ? true : false),
                            'country' => $aData['countryIso'],
                            'lang' => $aData['langIso'],
                            'langId' => $aData['langId'],
                            'currencyIso' => $aData['currencyIso'],
                            'currencySign' => $aData['currencySign'],
                            'countryName' => $aData['countryName'],
                            'langName' => $aData['langName'],
                            'taxonomy' => $aData['taxonomy'],
                            'full' => strtoupper($aData['langIso']) . '_' . strtoupper($aData['countryIso']) . '_' . strtoupper($aData['currencyIso']),
                            'is_default' => $aData['is_default'],
                            'id_feed' => $aData['id_feed'],
                        ];
                    }

                    $aAssign['aCronList'][] = [
                        'link' => \Context::getContext()->link->getModuleLink(moduleConfiguration::FPA_MODULE_SET_NAME, moduleConfiguration::FPA_CTRL_CRON, ['id_shop' => \FacebookProductAd::$iShopId, 'fpa_lang_id' => $aData['langId'], 'country' => $aData['countryIso'], 'currency_iso' => $aData['currencyIso'], 'token' => \FacebookProductAd::$conf['FPA_FEED_TOKEN'], 'sType' => 'cron']),
                        'lang' => $aData['langIso'],
                        'country' => $aData['countryIso'],
                        'currencyIso' => $aData['currencyIso'],
                        'currencySign' => $aData['currencySign'],
                        'countryName' => $aData['countryName'],
                        'langName' => $aData['langName'],
                        'taxonomy' => $aData['taxonomy'],
                    ];
                }
            }

            // handle on-the-fly output
            if (!empty(\FacebookProductAd::$aAvailableLangCurrencyCountry)) {
                foreach (\FacebookProductAd::$aAvailableLangCurrencyCountry as $aData) {
                    $aAssign['aFlyFileList'][] = [
                        'link' => \Context::getContext()->link->getModuleLink(moduleConfiguration::FPA_MODULE_SET_NAME, moduleConfiguration::FPA_CTRL_FLY, ['id_shop' => \FacebookProductAd::$iShopId, 'fpa_lang_id' => $aData['langId'], 'country' => $aData['countryIso'], 'currency_iso' => $aData['currencyIso'], 'token' => \FacebookProductAd::$conf['FPA_FEED_TOKEN'], 'sType' => 'flyOutput']),
                        'country' => $aData['countryIso'],
                        'iso_code' => $aData['langIso'],
                        'currencyIso' => $aData['currencyIso'],
                        'currencySign' => $aData['currencySign'],
                        'countryName' => $aData['countryName'],
                        'langName' => $aData['langName'],
                        'taxonomy' => $aData['taxonomy'],
                        'is_default' => $aData['is_default'],
                        'id_feed' => $aData['id_feed'],
                    ];
                }
            }

            // handle the cron URL
            $aAssign['sCronUrl'] = \Context::getContext()->link->getModuleLink(moduleConfiguration::FPA_MODULE_SET_NAME, moduleConfiguration::FPA_CTRL_CRON, ['id_shop' => \FacebookProductAd::$iShopId, 'token' => \FacebookProductAd::$conf['FPA_FEED_TOKEN']]);

            // check if the feed protection is activated
            if (!empty(\FacebookProductAd::$conf['FPA_FEED_TOKEN'])) {
                $aAssign['sCronUrl'] .= '&token=' . \FacebookProductAd::$conf['FPA_FEED_TOKEN'];
            }
        }

        return [
            'tpl' => 'admin/feed-list.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays reporting settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayReporting(array $aPost = null)
    {
        $aAssign = [
            'aLangCurrencies' => moduleTools::getGeneratedReport(),
            'bReporting' => \FacebookProductAd::$conf['FPA_REPORTING'],
        ];

        return [
            'tpl' => 'admin/reporting-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     *  method displays advice form
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayAdvice(array $aPost = null)
    {
        $aAssign = [];

        // clean headers
        @ob_end_clean();

        // force xhr mode activated
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/advice.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays the new custom feed form
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayNewCustomFeed(array $aPost = null)
    {
        $aAssign = [
            'shop_lang' => \Language::getLanguages(false, (int) \FacebookProductAd::$iShopId),
            'country_shop' => \Country::getCountries(\FacebookProductAd::$iCurrentLang, true, false, false),
            'currency_shop' => \Currency::getCurrenciesByIdShop((int) \FacebookProductAd::$iShopId),
            'taxonomies' => moduleConfiguration::getTaxonomies(),
        ];

        return [
            'tpl' => 'admin/new-custom-feed.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays pixel config
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayPixel(array $aPost = null)
    {
        $aAssign = [
            'sPixel' => \FacebookProductAd::$conf['FPA_PIXEL'],
            'is_curl' => extension_loaded('curl'),
            'bUseTax' => \FacebookProductAd::$conf['FPA_USE_TAX'],
            'bUseTestEventCode' => \FacebookProductAd::$conf['FPA_USE_TEST_EVENT_CODE'],
            'testEventCode' => \FacebookProductAd::$conf['FPA_TEST_EVENT_CODE'],
            'bUseShipping' => \FacebookProductAd::$conf['FPA_USE_SHIPPING'],
            'bUseWrapping' => \FacebookProductAd::$conf['FPA_USE_WRAPPING'],
            'bDisplayCustomDomCode' => \FacebookProductAd::$conf['FPA_CUSTOM_DOM_ELEM'],
            'wishSelectorProd' => \FacebookProductAd::$conf['FPA_JS_WISH_SELECTOR_PROD'],
            'wishSelectorCat' => \FacebookProductAd::$conf['FPA_JS_WISH_SELECTOR_CAT'],
            'bTrackCartPage' => \FacebookProductAd::$conf['FPA_TRACK_ADD_CART_PAGE'],
            'bPmCookieBanner' => moduleTools::isInstalled('pm_advancedcookiebanner'),
            'aSelectorDefault' => [
                'add_to_cart' => '.add-to-cart',
                'wishlist' => 'a[id="wishlist_button"]',
                'add_to_cart_list' => 'a[rel=ajax_id_product__PRODUCT_ID_].ajax_add_to_cart_button',
                'wishlist_list' => 'button.wishlist-button-add',
                'order_selector' => '.btn-primary',
                'sub_selector' => '.pm_subscription_display_product_buttons',
            ],
        ];

        return [
            'tpl' => 'admin/pixel-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays shop tab
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayShop(array $aPost = null)
    {
        $sLang = 'en';

        if (\FacebookProductAd::$sCurrentLang == 'fr') {
            $sLang = 'fr';
        }

        $aAssign = [
            'sOverviewImg' => moduleConfiguration::FPA_URL_IMG . 'facebook-shops/overview-' . $sLang . '-shops.png',
            'sUrlShopLink' => 'https://www.facebook.com/business/shops',
        ];

        return [
            'tpl' => 'admin/shop-management.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displayReporting() method displays reporting fancybox
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayReportingBox(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];
        $aTmp = [];

        // get the current lang ID
        $sLang = \Tools::getValue('lang');
        $iProductCount = \Tools::getValue('count');
        $currency_iso = \Tools::getValue('sCurrencyIso');

        if (!empty($currency_iso)) {
            $sLang = $sLang . '_' . $currency_iso;
        }

        if (!empty($sLang)) {
            $reporting = Reporting::getReportingData($sLang, \FacebookProductAd::$iShopId);
            $reporting_details = json_decode($reporting, true);
            $details_iso_data = explode('_', $sLang);

            if (!empty($reporting_details)) {
                static $products = [];

                $id_lang = \Language::getIdByIso($details_iso_data[0]);
                $language = new \Language((int) $id_lang);
                $id_currency = \Currency::getIdByIsoCode($details_iso_data[2]);
                $currency = new \Currency($id_currency);
                $id_country = \Country::getByIso(\Tools::strtolower($details_iso_data[1]));
                $country = new \Country((int) $id_country);

                // check if exists counter key in the reporting
                if (!empty($reporting_details['counter'][0])) {
                    if (empty($iProductCount)) {
                        $iProductCount = $reporting_details['counter'][0]['products'];
                    }
                    unset($reporting_details['counter']);
                }

                // load facebook tags
                $aGoogleTags = moduleTools::loadFeedTag(\FacebookProductAd::$oModule);

                foreach ($reporting_details as $sTagName => &$aGTag) {
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['count'] = count($aGTag);
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['label'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['label'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['msg'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['msg'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['faq_id'] = (isset($aGoogleTags[$sTagName]) ? (int) ($aGoogleTags[$sTagName]['faq_id']) : 0);
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['anchor'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['anchor'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['mandatory'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['mandatory'] : false);

                    // detect the old format system and the new format
                    if (
                        isset($aGTag[0]['productId'])
                        && strstr($aGTag[0]['productId'], '_')
                    ) {
                        foreach ($aGTag as $iKey => &$aProdValue) {
                            list($iProdId, $iAttributeId) = explode('_', $aProdValue['productId']);
                            if (empty($products[$aProdValue['productId']])) {
                                // get the product obj
                                $oProduct = new \Product((int) $iProdId, true, (int) $id_lang);
                                $oCategory = new \Category((int) $oProduct->id_category_default, (int) $id_lang);

                                // set the product URL
                                $aProdValue['productUrl'] = moduleTools::getProductLink($oProduct, $id_lang);
                                // set the product name
                                $aProdValue['productName'] = $oProduct->name;

                                // if combination
                                if (!empty($iAttributeId)) {
                                    $product_category = new \Category((int) $oProduct->getDefaultCategory(), (int) $id_lang);
                                    $aProdValue['productUrl'] = \Context::getContext()->link->getProductLink($oProduct, null, \Tools::strtolower($product_category->link_rewrite), null, (int) $id_lang, (int) \FacebookProductAd::$iShopId, (int) $iAttributeId, true);

                                    // get the combination attributes to format the product name
                                    $aCombinationAttr = moduleDao::getProductComboAttributes((int)$iAttributeId, $id_lang, \FacebookProductAd::$iShopId);

                                    if (!empty($aCombinationAttr)) {
                                        $sExtraName = '';
                                        foreach ($aCombinationAttr as $c) {
                                            $sExtraName .= ' ' . stripslashes($c['name']);
                                        }
                                        $aProdValue['productName'] .= $sExtraName;
                                    }
                                }
                                unset($oProduct);
                                unset($oCategory);

                                $products[$aProdValue['productId']] = [
                                    'productId' => $iProdId,
                                    'productAttrId' => $iAttributeId,
                                    'productUrl' => $aProdValue['productUrl'],
                                    'productName' => $aProdValue['productName'],
                                ];
                            }
                            $aProdValue = $products[$aProdValue['productId']];
                        }
                    }
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['data'] = $aGTag;
                }
                $products = [];
                ksort($aTmp);
                unset($reporting_details);
                unset($aGoogleTags);

                $aAssign = [
                    'sLangName' => $language->name,
                    'sCountryName' => $country->name[$id_lang],
                    'aReport' => $aTmp,
                    'iProductCount' => (int) $iProductCount,
                    'sPath' => moduleConfiguration::FPA_SHOP_PATH_ROOT,
                    'faqLink' => 'http://faq.businesstech.fr/',
                    'sToken' => \Tools::getAdminTokenLite('AdminProducts'),
                    'sProductLinkController' => $_SERVER['SCRIPT_URI'] . '?controller=AdminProducts',
                    'sProductAction' => '&updateproduct',
                ];
            } else {
                $aAssign['aErrors'][] = [
                    'msg' => \FacebookProductAd::$oModule->l('There isn\'t any report for this language and country', 'adminDisplay.php') . ' : ' . $details_iso_data[0] . ' - ' . $details_iso_data[1],
                    'code' => 190,
                ];
            }
        } else {
            $aAssign['aErrors'][] = [
                'msg' => \FacebookProductAd::$oModule->l('Language ISO and country ISO aren\'t well formatted', 'adminDisplay.php'),
                'code' => 191,
            ];
        }

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/reporting-box.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     *method displays search product name for autocomplete
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displaySearchProduct(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // set
        $sOutput = '';

        // get the query to search
        $sSearch = \Tools::getValue('q');
        $sExcludedList = \Tools::getValue('excludeIds');

        if (!empty($sSearch)) {
            $aMatchingProducts = moduleDao::searchProducts($sSearch, (bool) \FacebookProductAd::$conf['FPA_P_COMBOS'], $sExcludedList);

            if (!empty($aMatchingProducts)) {
                foreach ($aMatchingProducts as $aProduct) {
                    // check if we export with combinations
                    if (!empty($aProduct['id_product_attribute'])) {
                        $aProduct['name'] .= moduleTools::getProductCombinationName($aProduct['id_product_attribute'], \FacebookProductAd::$iCurrentLang, \FacebookProductAd::$iShopId);
                    }
                    $sOutput .= trim($aProduct['name']) . '|' . (int) $aProduct['id_product'] . '|' . (!empty($aProduct['id_product_attribute']) ? $aProduct['id_product_attribute'] : '0') . "\n";
                }
            }
        }

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/product-search.tpl',
            'assign' => ['json' => $sOutput],
        ];
    }

    /**
     * method returns the matching requested translations
     *
     * @param string $sSerializedVar
     * @param string $sGlobalVar
     *
     * @return array
     */
    private function getDefaultTranslations($sSerializedVar, $sGlobalVar)
    {
        $aTranslations = [];

        if (!empty(\FacebookProductAd::$conf[strtoupper($sSerializedVar)])) {
            $aTranslations = is_string(\FacebookProductAd::$conf[strtoupper($sSerializedVar)]) ? moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf[strtoupper($sSerializedVar)]) : \FacebookProductAd::$conf[strtoupper($sSerializedVar)];
        } else {
            foreach (moduleConfiguration::FPA_HOME_CAT_NAME as $sIsoCode => $sTranslation) {
                $iLangId = moduleTools::getLangId($sIsoCode);

                if ($iLangId) {
                    // get Id by iso
                    $aTranslations[$iLangId] = $sTranslation;
                }
            }
        }

        return $aTranslations;
    }

    /**
     *  method displays the affected products by the rule
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayExclusionRuleProducts(array $aPost = null)
    {
        @ob_end_clean();
        $aAssign = [];
        $aExcludedProducts = [];

        $iRuleId = \Tools::getValue('iRuleId');

        if (!empty($iRuleId)) {
            $aProducts = ExclusionProduct::getExcludedProductById($iRuleId);

            foreach ($aProducts as $aProduct) {
                $oProduct = new \Product($aProduct['id_product'], true, \FacebookProductAd::$iCurrentLang);

                if (is_object($oProduct)) {
                    $sProductName = $oProduct->name;

                    // Use case manage the name with Combo value
                    if (!empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                        $sComboName = moduleTools::getProductCombinationName($aProduct['id_product_attribute'], \FacebookProductAd::$iCurrentLang, \FacebookProductAd::$iShopId);
                        $sProductName .= ' ' . $sComboName;
                    }

                    $aExcludedProducts[] = ['id' => $oProduct->id, 'name' => $sProductName];
                }
            }
        }

        unset($oProduct);

        $aAssign['aProductsData'] = $aExcludedProducts;

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/excluded-products.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules
     *
     * @param array $aPost
     *
     * @return array
     *
     */
    private function displayExcludeValue(array $aPost)
    {
        @ob_end_clean();
        $iRuleId = \Tools::getValue('iRuleId');
        $bAddTmpRules = \Tools::getValue('bUpdate');
        $aData = [];

        $aAssign = [];

        // Init the render object
        $oRender = new exclusionRender();

        $RenderData = [
            'idLang' => \FacebookProductAd::$iCurrentLang,
            'modulePrefix' => moduleConfiguration::FPA_TABLE_PREFIX,
            'typeWord' => moduleConfiguration::FPA_EXCLUSION_TYPE_WORD,
        ];

        $aAssign = $oRender->render($aPost['sExclusionType'], $aPost, $RenderData);
        // Use case for update rule
        if (!empty($iRuleId) && !empty($bAddTmpRules)) {
            $aData = AdvancedExclusion::getRulesById((int) $iRuleId);
            $aAssign['aDataRule'] = moduleTools::handleGetConfigurationData($aData['exclusion_value']);
            $aAssign['sType'] = $aData['type'];
            $aAssign['iRuleId'] = $aData['id'];

            // Use case for to add on the tmp rules for update display
            $aTmpData = moduleTools::handleGetConfigurationData($aData['exclusion_value']);
            foreach ($aTmpData as $sKey => $aRuleDetailData) {
                // Use case for a rules detail
                if ($sKey == 'aRulesDetail') {
                    foreach ($aRuleDetailData as $aRuleDetailFilter) {
                        tmpRules::addTmpRules(\FacebookProductAd::$iShopId, $aData['type'], $aRuleDetailFilter);
                    }
                }
            }
        }

        // Use case for feature values
        if (!empty($aPost['iFeatureId']) || $aPost['sExclusionType'] == 'feature') {
            $aAssign = $oRender->render('feature', $aPost, $RenderData);
        }

        // Use case for attribute values on ajax request
        if ($aPost['sExclusionType'] == 'attribute' || !empty($aPost['iAttributeId'])) {
            $aAssign = $oRender->render('attribute', $aPost, $RenderData);
        }
        // Use case for words values
        if ($aPost['sExclusionType'] == 'word') {
            $aAssign = $oRender->render('word', $aPost, $RenderData);
        }

        // Use case for category values
        if ($aPost['sExclusionType'] == 'category') {
            $aAssign = $oRender->render('category', $aPost, $RenderData);
        }

        // Use case for manufacturer values
        if ($aPost['sExclusionType'] == 'manufacturer') {
            $aAssign = $oRender->render('manufacturer', $aPost, $RenderData);
        }

        // Use case for supplier values
        if ($aPost['sExclusionType'] == 'supplier') {
            $aAssign = $oRender->render('supplier', $aPost, $aData);
        }

        // Force XHR
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/exclusion-values.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayRulesSummary(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];

        if (!empty($aPost['sTmpRules'])) {
            // Init the render object
            $oRender = new ExclusionRender();

            // Use case for the delete
            if (!empty($aPost['sDelete']) && !empty($aPost['iRuleId'])) {
                tmpRules::deleteTmpRules($aPost['iRuleId']);
            }

            $data = [
                'idShop' => \FacebookProductAd::$iShopId,
                'TmpRuleTable' => moduleConfiguration::FPA_TMP_RULE,
                'currentLang' => \FacebookProductAd::$sCurrentLang,
                'currentLangId' => \FacebookProductAd::$iCurrentLang,
                'rulesType' => moduleConfiguration::FPA_RULES_LABEL_TYPE,
                'wordType' => moduleConfiguration::FPA_RULES_WORD_TYPE,
                'combo' => \FacebookProductAd::$conf['FPA_P_COMBOS'],
                'comparePs8' => \FacebookProductAd::$bCompare80,
                'includeAttributeId' => \FacebookProductAd::$conf['FPA_INCL_ATTR_VALUE'],
                'modulePrefix' => moduleConfiguration::FPA_TABLE_PREFIX,
            ];

            $dataForRender = array_merge($aPost, $data);
            $aRulesData = $oRender->render('Rules', $dataForRender);

            if (!empty($aRulesData)) {
                $aAssign['aTmpRules'] = $aRulesData;
                $aAssign['aProducts'] = $oRender->render('Products', $dataForRender, $aRulesData);
            }
        }

        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/rules-summary.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules form configuration
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayExclusionRule(array $aPost = null)
    {
        $aAssign = [];
        // clean headers
        @ob_end_clean();
        $iRuleId = \Tools::getValue('iRuleId');

        $aAssign['bRefreshRules '] = false;

        // Use case for the refresh rules
        $aAssign = [
            'aExclusionType' => moduleConfiguration::FPA_RULES_LABEL_TYPE,
            'aExclusionWordType' => moduleConfiguration::FPA_RULES_WORD_TYPE,
            'aFeatures' => \Feature::getFeatures(\FacebookProductAd::$iCurrentLang),
            'aAttributes' => \AttributeGroup::getAttributesGroups(\FacebookProductAd::$iCurrentLang),
            'iRuleId' => !empty($iRuleId) ? $iRuleId : '',
        ];

        // Clean the database with tmp rules
        tmpRules::cleanTmpRules(\FacebookProductAd::$iShopId);
        tmpRules::resetIncrement();

        // Use case for update rule
        if (!empty($iRuleId)) {
            $aAssign['aDataRule'] = AdvancedExclusion::getRulesById((int) $iRuleId);
        }

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/exclusion-rules.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays consent config
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayConsent(array $aPost = null)
    {
        $aAssign = [
            'bPmCookieBanner' => moduleTools::isInstalled('pm_advancedcookiebanner'),
            'bActivateConsent' => \FacebookProductAd::$conf['FPA_USE_CONSENT'],
            'sAcceptElement' => \FacebookProductAd::$conf['FPA_ELEMENT_HTML_ID'],
            'sAcceptElementSecond' => \FacebookProductAd::$conf['FPA_ELEMENT_HTML_SECOND_ID'],
            'bActivateAxeptio' => \FacebookProductAd::$conf['FPA_USE_AXEPTIO'],
        ];

        return [
            'tpl' => 'admin/consent-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays chats config
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayChats(array $aPost = null)
    {
        $aAssign = [
            'bChatsIsInstalled' => \FacebookProductAd::$bFacebookChats,
            'sModuleChatsUrl' => \Context::getContext()->link->getAdminLink('AdminModules') . '&configure=btfacebookchats',
            'sWebsiteDiscover' => 'https://addons.prestashop.com/support-chat-online/51958-facebook-chats-network-chats-messenger-whatsapp.html',
        ];

        return [
            'tpl' => 'admin/chats-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     *  method displays basic settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayLog(array $aPost = null)
    {
        $aAssign = [
            'aEventType' => moduleConfiguration::getEventType(),
            'useApi' => \FacebookProductAd::$conf['FPA_USE_API'],
        ];

        return [
            'tpl' => 'admin/log.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * create() method set singleton
     *
     * @return adminDisplay
     */
    public static function create()
    {
        static $oDisplay;

        if (null === $oDisplay) {
            $oDisplay = new adminDisplay();
        }

        return $oDisplay;
    }

    /**
     * displays cross selling
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayCrossSelling(array $aPost = null)
    {
        $aAssign = [
            'modules' => moduleTools::getModulesFromCache(\FacebookProductAd::$oModule->name),
        ];

        return [
            'tpl' => 'admin/cross-selling.tpl',
            'assign' => $aAssign,
        ];
    }
}
