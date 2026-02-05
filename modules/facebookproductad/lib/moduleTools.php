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

namespace FacebookProductAd\ModuleLib;

if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\Feeds;
use FacebookProductAd\Models\Reporting;
use FacebookProductAd\Pixel\basePixel;

class moduleTools
{
    /**
     * Magic Method __construct
     */
    private function __construct() {}

    /**
     * method returns good translated errors
     */
    public static function translateJsMsg()
    {
        return moduleConfiguration::getJsMessage();
    }

    /**
     * method update new keys in new module version
     */
    public static function updateConfiguration()
    {
        // check to update new module version
        foreach (moduleConfiguration::getConfVar() as $sKey => $mVal) {
            // use case - not exists
            if (\Configuration::get($sKey) === false) {
                // update key/ value
                \Configuration::updateValue($sKey, $mVal);
            }
        }
    }

    /**
     * all details of the shop group or one required detail
     *
     * @param string $sDetail
     *
     * @return mixed : array or string
     */
    public static function getGroupShopDetail($sDetail = null)
    {
        // get the current group shop
        $oGroupShop = new \ShopGroup(\Context::getContext()->shop->id_shop_group);

        $aDetails = $oGroupShop->getFields();

        return ($sDetail !== null && isset($aDetails[$sDetail])) ? $aDetails[$sDetail] : $aDetails;
    }

    /**
     * method set all constant module in ps_configuration
     *
     * @param array $aOptionListToUnserialize
     * @param int $iShopId
     */
    public static function getConfiguration(array $aOptionListToUnserialize = null, $iShopId = null)
    {
        // get configuration options
        if (null !== $iShopId && is_numeric($iShopId)) {
            \FacebookProductAd::$conf = \Configuration::getMultiple(array_keys(moduleConfiguration::getConfVar()), null, null, $iShopId);
        } else {
            \FacebookProductAd::$conf = \Configuration::getMultiple(array_keys(moduleConfiguration::getConfVar()));
        }
        if (
            !empty($aOptionListToUnserialize)
            && is_array($aOptionListToUnserialize)
        ) {
            foreach ($aOptionListToUnserialize as $sOption) {
                if (!empty(\FacebookProductAd::$conf[strtoupper($sOption)]) && is_string(\FacebookProductAd::$conf[strtoupper($sOption)]) && !is_numeric(\FacebookProductAd::$conf[strtoupper($sOption)])) {
                    \FacebookProductAd::$conf[strtoupper($sOption)] = moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf[strtoupper($sOption)]);
                }
            }
        }
    }

    /**
     * method set good iso lang
     *
     * @return mixed
     */
    public static function getLangIso($iLangId = null)
    {
        if (null === $iLangId) {
            $iLangId = \FacebookProductAd::$iCurrentLang;
        }

        // get iso lang
        $sIsoLang = \Language::getIsoById($iLangId);

        if (false === $sIsoLang) {
            $sIsoLang = 'en';
        }

        return $sIsoLang;
    }

    /**
     * method return Lang id from iso code
     *
     * @param string $sIsoCode
     *
     * @return int
     */
    public static function getLangId($sIsoCode, $iDefaultId = null)
    {
        // get iso lang
        $iLangId = \Language::getIdByIso($sIsoCode);

        if (empty($iLangId) && $iDefaultId !== null) {
            $iLangId = $iDefaultId;
        }

        return $iLangId;
    }

    /**
     * method Performs a "UNION" of installed shop languages and languages
     * default supported values
     *
     * @param int $iShopId
     *
     * @return array
     */
    public static function getAvailableLanguages($iShopId)
    {
        // set
        $aAvailableLanguages = [];

        $aShopLanguages = \Language::getLanguages(false, (int) $iShopId);

        foreach ($aShopLanguages as $aLanguage) {
            if ($aLanguage['active']) {
                $aAvailableLanguages[] = $aLanguage;
            }
        }

        return $aAvailableLanguages;
    }

    /**
     * returns information about languages / countries and currencies available for Google
     *
     * @param array $available_languages
     *
     * @return array
     */
    public static function getLangCurrencyCountry(array $available_languages)
    {
        // Force database update to be sure we could make the migration
        moduleUpdate::create()->run('tables');
        moduleUpdate::create()->run('fields');
        $output_data = [];

        $hasData = Feeds::hasSavedData(\FacebookProductAd::$iShopId);
        if (!empty($hasData)) {
            $available_feeds = Feeds::getAvailableFeeds((int) \FacebookProductAd::$iShopId);
            if (!empty($available_feeds)) {
                foreach ($available_languages as $lang) {
                    $current_feed_shop = Feeds::getFeedLangData($lang['iso_code'], (int) \FacebookProductAd::$iShopId);
                    foreach ($current_feed_shop as $feed) {
                        $language = new \Language($lang['id_lang']);
                        $id_country = \Country::getByIso(\Tools::strtolower($feed['iso_country']));

                        if (!empty($id_country)) {
                            $country_name = \Country::getNameById(\FacebookProductAd::$iCurrentLang, $id_country);
                            $country = new \Country($id_country);

                            if (!empty($country->id)) {
                                if (!empty($country->active)) {
                                    $id_currency = \Currency::getIdByIsoCode($feed['iso_currency']);
                                    $currency = new \Currency($id_currency);
                                    if (!empty($currency->iso_code)) {
                                        $output_data[] = [
                                            'langId' => $language->id,
                                            'langIso' => $language->iso_code,
                                            'countryIso' => $country->iso_code,
                                            'currencyIso' => $currency->iso_code,
                                            'currencyId' => $currency->id,
                                            'currencyFirst' => 1,
                                            'langName' => $language->name,
                                            'countryName' => $country_name,
                                            'currencySign' => $currency->sign,
                                            'taxonomy' => $feed['taxonomy'],
                                            'is_default' => $feed['feed_is_default'],
                                            'id_feed' => $feed['id_feed'],
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $output_data;
    }

    /**
     * method returns current currency sign or id
     *
     * @param string $sField : field name has to be returned
     * @param int $iCurrencyId : currency id
     *
     * @return mixed : string or array
     */
    public static function getCurrency($sField = null, $iCurrencyId = 0)
    {
        // set
        $mCurrency = null;
        $aCurrency = \Currency::getCurrency($iCurrencyId);

        if ($sField !== null) {
            switch ($sField) {
                case 'id_currency':
                    $mCurrency = $aCurrency['id_currency'];

                    break;
                case 'name':
                    $mCurrency = $aCurrency['name'];

                    break;
                case 'iso_code':
                    $mCurrency = $aCurrency['iso_code'];

                    break;
                case 'iso_code_num':
                    $mCurrency = $aCurrency['iso_code_num'];

                    break;
                case 'sign':
                    $mCurrency = $aCurrency['sign'];

                    break;
                case 'conversion_rate':
                    $mCurrency = $aCurrency['conversion_rate'];

                    break;
                case 'format':
                    $mCurrency = $aCurrency['format'];

                    break;
                default:
                    $mCurrency = $aCurrency;

                    break;
            }
        }

        return $mCurrency;
    }

    /**
     * method returns template path
     *
     * @param string $sTemplate
     *
     * @return mixed
     */
    public static function getTemplatePath($sTemplate)
    {
        return \FacebookProductAd::$oModule->getTemplatePath($sTemplate);
    }

    /**
     * method returns link object
     *
     * @return mixed
     */
    public static function getLinkObj()
    {
        return \FacebookProductAd::$oContext->link;
    }

    /**
     * method returns product link
     *
     * @param \Product $oProduct
     * @param int $iLangId
     *
     * @return mixed
     */
    public static function getProductLink($oProduct, $iLangId)
    {
        $sProdUrl = '';

        if (\Configuration::get('PS_REWRITING_SETTINGS')) {
            $sProdUrl = self::getLinkObj()->getProductLink($oProduct, null, null, null, (int) $iLangId, null, 0, true);
        } else {
            $sProdUrl = self::getLinkObj()->getProductLink($oProduct, null, null, null, (int) $iLangId, null, 0, false);
        }

        return $sProdUrl;
    }

    /**
     * method returns the product condition
     *
     * @param string $sCondition
     *
     * @return mixed
     */
    public static function getProductCondition($sCondition = null)
    {
        $sResult = '';

        if (
            $sCondition !== null
            && in_array($sCondition, ['new', 'used', 'refurbished'])
        ) {
            $sResult = $sCondition;
        } else {
            $sResult = !empty(\FacebookProductAd::$conf['FPA_COND']) ? \FacebookProductAd::$conf['FPA_COND'] : 'new';
        }

        return $sResult;
    }

    /**
     * returns product image
     *
     * @param \Product $oProduct
     * @param string $sImageType
     * @param array $aForceImage
     *
     * @return string
     */
    public static function getProductImage(\Product &$oProduct, $sImageType = null, $aForceImage = [])
    {
        $sImgUrl = '';

        if (\Validate::isLoadedObject($oProduct)) {
            // use case - get Image
            $aImage = !empty($aForceImage) ? $aForceImage : $oProduct->getImages(\FacebookProductAd::$iCurrentLang);

            if (!empty($aImage)) {
                // get image url
                if ($sImageType !== null) {
                    $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image'], $sImageType);
                } else {
                    $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image']);
                }
            }
        }

        return $sImgUrl;
    }

    /**
     * truncate current request_uri in order to delete params : sAction and sType
     *
     * @param string $mNeedle
     *
     * @return mixed
     */
    public static function truncateUri($mNeedle = '&sAction')
    {
        // set tmp
        $aQuery = [$mNeedle];

        // get URI
        $sURI = $_SERVER['REQUEST_URI'];

        foreach ($aQuery as $sNeedle) {
            $sURI = strstr($sURI, $sNeedle) ? substr($sURI, 0, strpos($sURI, $sNeedle)) : $sURI;
        }

        return $sURI;
    }

    /**
     * method check if specific module and module's vars are available
     *
     * @param string $sModuleName
     * @param array $aCheckedVars
     * @param bool $bObjReturn
     * @param bool $bOnlyInstalled
     *
     * @return mixed : true or false or obj
     */
    public static function isInstalled($sModuleName, array $aCheckedVars = [], $bObjReturn = false, $bOnlyInstalled = false)
    {
        $mReturn = false;

        // use case - check module is installed in DB
        if (\Module::isInstalled($sModuleName)) {
            if (!$bOnlyInstalled) {
                $oModule = \Module::getInstanceByName($sModuleName);

                if (!empty($oModule)) {
                    // check if module is activated
                    $aActivated = \Db::getInstance()->ExecuteS('SELECT id_module as id, active FROM ' . _DB_PREFIX_ . 'module WHERE name = "' . pSQL($sModuleName) . '" AND active = 1');

                    if (!empty($aActivated[0]['active'])) {
                        $mReturn = true;

                        if (version_compare(_PS_VERSION_, '1.5', '>')) {
                            $aActivated = \Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . (int) $aActivated[0]['id'] . ' AND id_shop = ' . (int) \Context::getContext()->shop->id);

                            if (empty($aActivated)) {
                                $mReturn = false;
                            }
                        }

                        if ($mReturn) {
                            if (!empty($aCheckedVars)) {
                                foreach ($aCheckedVars as $sVarName) {
                                    $mVar = \Configuration::get($sVarName);

                                    if (empty($mVar)) {
                                        $mReturn = false;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($mReturn && $bObjReturn) {
                    $mReturn = $oModule;
                }
                unset($oModule);
            } else {
                $mReturn = true;
            }
        }

        return $mReturn;
    }

    /**
     * method write breadcrumbs of product for category
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param string $sPath
     * @param bool $bEncoding
     *
     * @return mixed
     */
    public static function getProductPath($iCatId, $iLangId, $sPath = '', $bEncoding = true)
    {
        $oCategory = new \Category($iCatId);

        return \Validate::isLoadedObject($oCategory) ? strip_tags(self::getPath((int) $oCategory->id, (int) $iLangId, $sPath, $bEncoding)) : '';
    }

    /**
     * method write breadcrumbs of product for category
     *
     * Forced to redo the function from Tools here as it works with cookie
     * for language, not a passed parameter in the function
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param string $sPath
     * @param bool $bEncoding
     *
     * @return mixed
     */
    public static function getPath($iCatId, $iLangId, $sPath = '', $bEncoding = true)
    {
        $mReturn = '';

        if ($iCatId == 1) {
            $mReturn = $sPath;
        } else {
            // get pipe
            $sPipe = ' > ';
            $sFullPath = '';

            $aInterval = \Category::getInterval($iCatId);
            $aIntervalRoot = \Category::getInterval(\Context::getContext()->shop->getCategory());

            if (!empty($aInterval) && !empty($aIntervalRoot)) {
                $sQuery = 'SELECT c.id_category, cl.name, cl.link_rewrite'
                    . ' FROM ' . _DB_PREFIX_ . 'category c'
                    . \Shop::addSqlAssociation('category', 'c', false)
                    . ' LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . \Shop::addSqlRestrictionOnLang('cl') . ')'
                    . 'WHERE c.nleft <= ' . (int) $aInterval['nleft']
                    . ' AND c.nright >= ' . (int) $aInterval['nright']
                    . ' AND c.nleft >= ' . (int) $aIntervalRoot['nleft']
                    . ' AND c.nright <= ' . (int) $aIntervalRoot['nright']
                    . ' AND cl.id_lang = ' . (int) $iLangId
                    . ' AND c.level_depth > ' . (int) $aIntervalRoot['level_depth']
                    . ' ORDER BY c.level_depth ASC';

                $aCategories = \Db::getInstance()->executeS($sQuery);

                $iCount = 1;
                $nCategories = count($aCategories);

                foreach ($aCategories as $aCategory) {
                    $sFullPath .= ($bEncoding ? htmlentities($aCategory['name'], ENT_NOQUOTES, 'UTF-8') : $aCategory['name']) . (($iCount++ != $nCategories || !empty($sPath)) ? $sPipe : '');
                }
                $mReturn = $sFullPath . $sPath;
            }
        }

        return $mReturn;
    }

    /**
     * method process categories to generate tree of them
     *
     * @param array $aCategories
     * @param array $aIndexedCat
     * @param array $aCurrentCat
     * @param int $iCurrentIndex
     * @param int $iDefaultId
     * @param bool $bFirstExec
     *
     * @return array
     */
    public static function recursiveCategoryTree(array $aCategories, array $aIndexedCat, $aCurrentCat, $iCurrentIndex = 1, $iDefaultId = null, $bFirstExec = false)
    {
        // set variables
        static $_aTmpCat;
        static $_aFormatCat;

        if ($bFirstExec) {
            $_aTmpCat = null;
            $_aFormatCat = null;
        }

        if (!isset($_aTmpCat[$aCurrentCat['infos']['id_parent']])) {
            $_aTmpCat[$aCurrentCat['infos']['id_parent']] = 0;
        }
        ++$_aTmpCat[$aCurrentCat['infos']['id_parent']];

        // calculate new level
        $aCurrentCat['infos']['iNewLevel'] = $aCurrentCat['infos']['level_depth'] + 1;

        // calculate type of gif to display - displays tree in good
        $aCurrentCat['infos']['sGifType'] = (count($aCategories[$aCurrentCat['infos']['id_parent']]) == $_aTmpCat[$aCurrentCat['infos']['id_parent']] ? 'f' : 'b');

        // calculate if checked
        if (in_array($iCurrentIndex, $aIndexedCat)) {
            $aCurrentCat['infos']['bCurrent'] = true;
        } else {
            $aCurrentCat['infos']['bCurrent'] = false;
        }

        // define classname with default cat id
        $aCurrentCat['infos']['mDefaultCat'] = ($iDefaultId === null) ? 'default' : $iCurrentIndex;

        $_aFormatCat[] = $aCurrentCat['infos'];

        if (isset($aCategories[$iCurrentIndex])) {
            foreach ($aCategories[$iCurrentIndex] as $iCatId => $aCat) {
                if ($iCatId != 'infos') {
                    self::recursiveCategoryTree($aCategories, $aIndexedCat, $aCategories[$iCurrentIndex][$iCatId], $iCatId);
                }
            }
        }

        return $_aFormatCat;
    }

    /**
     * method process brands to generate tree of them
     *
     * @param array $aBrands
     * @param array $aIndexedBrands
     *
     * @return array
     */
    public static function recursiveBrandTree(array $aBrands, array $aIndexedBrands)
    {
        // set
        $aFormatBrands = [];

        foreach ($aBrands as $iIndex => $aBrand) {
            $aFormatBrands[] = [
                'id' => $aBrand['id_manufacturer'],
                'name' => $aBrand['name'],
                'checked' => (in_array($aBrand['id_manufacturer'], $aIndexedBrands) ? true : false),
            ];
        }

        return $aFormatBrands;
    }

    /**
     * method process suppliers to generate tree of them
     *
     * @param array $aSuppliers
     * @param array $aIndexedSuppliers
     *
     * @return array
     */
    public static function recursiveSupplierTree(array $aSuppliers, array $aIndexedSuppliers)
    {
        // set
        $aFormatSuppliers = [];

        foreach ($aSuppliers as $iIndex => $aSupplier) {
            $aFormatSuppliers[] = [
                'id' => $aSupplier['id_supplier'],
                'name' => $aSupplier['name'],
                'checked' => (in_array($aSupplier['id_supplier'], $aIndexedSuppliers) ? true : false),
            ];
        }

        return $aFormatSuppliers;
    }

    /**
     * method round on numeric
     *
     * @param float $fVal
     * @param int $iPrecision
     *
     * @return float
     */
    public static function round($fVal, $iPrecision = 2)
    {
        if (method_exists('Tools', 'ps_round')) {
            $fVal = \Tools::ps_round((float) $fVal, $iPrecision);
        } else {
            $fVal = round((float) $fVal, $iPrecision);
        }

        return $fVal;
    }

    /**
     * method set host
     *
     * @return mixed
     */
    public static function setHost()
    {
        if (\Configuration::get('PS_SHOP_DOMAIN') != false) {
            $sURL = 'http://' . \Configuration::get('PS_SHOP_DOMAIN');
        } else {
            $sURL = 'http://' . $_SERVER['HTTP_HOST'];
        }

        return $sURL;
    }

    /**
     * method set the XML file's prefix
     *
     * @return mixed
     */
    public static function setXmlFilePrefix()
    {
        return 'facebookproductad' . \FacebookProductAd::$conf['FPA_FEED_TOKEN'];
    }

    /**
     * method clear all generated files
     *
     * @return mixed
     */
    public static function cleanUpFiles()
    {
        foreach (\FacebookProductAd::$aAvailableLanguages as $aLanguage) {
            // get each countries by language
            $aCountries = moduleConfiguration::FPA_AVAILABLE_COUNTRIES[$aLanguage['iso_code']];

            foreach ($aCountries as $sCountry => $aLocaleData) {
                // detect file's suffix and clear file
                $fileSuffix = self::buildFileSuffix($aLanguage['iso_code'], $sCountry);
                @unlink(moduleConfiguration::FPA_SHOP_PATH_ROOT . \FacebookProductAd::$sFilePrefix . '.' . $fileSuffix . '.xml');
            }
        }
    }

    /**
     * method Build file suffix based on language and country ISO code
     *
     * @param string $sLangIso
     * @param string $sCountryIso
     * @param int $iShopId
     *
     * @return mixed
     */
    public static function buildFileSuffix($sLangIso, $sCountryIso, $iShopId = 0)
    {
        if (\Tools::strtolower($sLangIso) == \Tools::strtolower($sCountryIso)) {
            $sSuffix = \Tools::strtolower($sLangIso);
        } else {
            $sSuffix = \Tools::strtolower($sLangIso) . '.' . \Tools::strtolower($sCountryIso);
        }

        $sSuffix .= ($iShopId ? '.shop' . $iShopId : '.shop' . \FacebookProductAd::$iShopId);

        return $sSuffix;
    }

    /**
     * method returns all available condition
     */
    public static function getConditionType()
    {
        return [
            'new' => \FacebookProductAd::$oModule->l('New', 'moduleTools'),
            'used' => \FacebookProductAd::$oModule->l('Used', 'moduleTools'),
            'refurbished' => \FacebookProductAd::$oModule->l('Refurbished', 'moduleTools'),
        ];
    }

    /**
     * method returns all available description
     */
    public static function getDescriptionType()
    {
        return [
            1 => \FacebookProductAd::$oModule->l('Short description', 'moduleTools'),
            2 => \FacebookProductAd::$oModule->l('Long description', 'moduleTools'),
            3 => \FacebookProductAd::$oModule->l('Both', 'moduleTools'),
            4 => \FacebookProductAd::$oModule->l('Meta-description', 'moduleTools'),
        ];
    }

    /**
     * method set all available attributes managed in facebook flux
     */
    public static function loadFacebookTags()
    {
        return [
            '_no_available_for_order' => [
                'label' => \FacebookProductAd::$oModule->l('Product not available for order', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because you don\'t allow them to be ordered when out of stock', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            '_no_product_name' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product name', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because the product names are missing', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
            '_no_required_data' => [
                'label' => \FacebookProductAd::$oModule->l('Missing mandatory information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because one of this mandatory product information is missing: name / description / link / image link', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            '_no_export_no_supplier_ref' => [
                'label' => \FacebookProductAd::$oModule->l('Product without MPN', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because they do not have a MPN reference', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_no_ean_upc' => [
                'label' => \FacebookProductAd::$oModule->l('Product without GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because they do not have a GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_no_stock' => [
                'label' => \FacebookProductAd::$oModule->l('No stock', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because they are out of stock', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_min_price' => [
                'label' => \FacebookProductAd::$oModule->l('Product under min price', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Products not exported because their price is lower than the minimum value defined in the configuration', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            // to be removed because this was only valid for gmcp old versions
            'excluded' => [
                'label' => \FacebookProductAd::$oModule->l('Excluded product list', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('this product or combination has been excluded from your feed as you defined it in the exclusion rules tab', 'moduleTools') . '.',
                'faq_id' => 0,
                'anchor' => '',
            ],
            'id' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product ID', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "ID" tag => This is the unique identifier of the item', 'moduleTools') . '.',
                'faq_id' => 261,
                'anchor' => '',
            ],
            'title' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product title', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "TITLE" tag => This is the title of the item', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
            'description' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product description', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "DESCRIPTION" tag => This is the description of the item', 'moduleTools') . '.',
                'faq_id' => 273,
                'anchor' => '',
            ],
            'google_product_category' => [
                'label' => \FacebookProductAd::$oModule->l('No Facebook category', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "FACEBOOK PRODUCT CATEGORY" tag => You have to associate each product default category with an official Facebook category', 'moduleTools') . '.',
                'faq_id' => 281,
                'anchor' => '',
            ],
            'product_type' => [
                'label' => \FacebookProductAd::$oModule->l('No product type', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "PRODUCT TYPE" tag => Unlike the "Facebook Product Category" tag, the "Product Type" tag contains the information about the category of the product according to your own classification', 'moduleTools') . '.',
                'faq_id' => 258,
                'anchor' => '',
            ],
            'link' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "LINK" tag => This is the link of the item', 'moduleTools') . '.',
                'faq_id' => 260,
                'anchor' => '',
            ],
            'image_link' => [
                'label' => \FacebookProductAd::$oModule->l('Missing image link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "IMAGE LINK" tag => This is the URL of the main image of the product', 'moduleTools') . '.',
                'faq_id' => 257,
                'anchor' => '',
            ],
            'condition' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product condition', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "CONDITION" tag => This is the condition of the item', 'moduleTools') . '.',
                'faq_id' => 259,
                'anchor' => '',
            ],
            'availability' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product availability', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "AVAILABILITY" tag => This indicates the availability of the item', 'moduleTools') . '.',
                'faq_id' => 262,
                'anchor' => '',
            ],
            'price' => [
                'label' => \FacebookProductAd::$oModule->l('Missing product price', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "PRICE" tag => This is the price of the item', 'moduleTools') . '.',
                'faq_id' => 279,
                'anchor' => '',
            ],
            'gtin' => [
                'label' => \FacebookProductAd::$oModule->l('No GTIN code', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "GTIN" tag => The "Global Trade Item Number" is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 263,
                'anchor' => '',
            ],
            'brand' => [
                'label' => \FacebookProductAd::$oModule->l('No product brand', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "BRAND" tag => The product brand is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 278,
                'anchor' => '',
            ],
            'mpn' => [
                'label' => \FacebookProductAd::$oModule->l('No MPN reference', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "MPN" tag => The "Manufacturer Part Number" is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 264,
                'anchor' => '',
            ],
            'adult' => [
                'label' => \FacebookProductAd::$oModule->l('No adult tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "ADULT" tag => This tag indicates that the item is for adults only', 'moduleTools') . '.',
                'faq_id' => 274,
                'anchor' => '',
            ],
            'gender' => [
                'label' => \FacebookProductAd::$oModule->l('No gender tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "GENDER" tag => This tag allows you specify the gender of the people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 270,
                'anchor' => '',
            ],
            'age_group' => [
                'label' => \FacebookProductAd::$oModule->l('No age group tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "AGE GROUP" tag => This tag allows you to specify the age group of people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 271,
                'anchor' => '',
            ],
            'color' => [
                'label' => \FacebookProductAd::$oModule->l('No color', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "COLOR" tag => This tag indicates the color of the item', 'moduleTools') . '.',
                'faq_id' => 276,
                'anchor' => '',
            ],
            'size' => [
                'label' => \FacebookProductAd::$oModule->l('No size', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "SIZE" tag => This tag indicates the size of the item', 'moduleTools') . '.',
                'faq_id' => 275,
                'anchor' => '',
            ],
            'material' => [
                'label' => \FacebookProductAd::$oModule->l('No material tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "MATERIAL" tag => This tag indicates the material the item is made from', 'moduleTools') . '.',
                'faq_id' => 268,
                'anchor' => '',
            ],
            'pattern' => [
                'label' => \FacebookProductAd::$oModule->l('No pattern tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "PATTERN" tag => This tag indicates the pattern or graphic print on the item', 'moduleTools') . '.',
                'faq_id' => 269,
                'anchor' => '',
            ],
            'item_group_id' => [
                'label' => \FacebookProductAd::$oModule->l('No item group id', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "ITEM GROUP ID" tag => All items that are variants of a same product must have the same item group id', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            'shipping_weight' => [
                'label' => \FacebookProductAd::$oModule->l('No information on package weight', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('The "SHIPPING WEIGHT" tag => This is the weight of the item used to calculate the shipping cost of the item', 'moduleTools') . '.',
                'faq_id' => 282,
                'anchor' => '',
            ],
            'shipping' => [
                'label' => \FacebookProductAd::$oModule->l('Missing shipping information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \FacebookProductAd::$oModule->l('The "SHIPPING" tag => The shipping tag lets you override shipping information for specific items', 'moduleTools') . '.',
                'faq_id' => 51,
                'anchor' => '',
            ],
            'title_length' => [
                'label' => \FacebookProductAd::$oModule->l('Too long title', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \FacebookProductAd::$oModule->l('Facebook requires your product titles to be no more than 150 characters long', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
        ];
    }

    /**
     * method returns the Facebook taxonomy file's content
     *
     * @param string $sUrl
     *
     * @return mixed
     */
    public static function getFacebookFile($sUrl)
    {
        $sContent = false;

        // Let's try first with file_get_contents
        if (ini_get('allow_url_fopen')) {
            $sContent = (method_exists('Tools', 'file_get_contents') ? \Tools::file_get_contents($sUrl) : file_get_contents($sUrl));
        }

        // Returns false ? Try with CURL if available
        if ($sContent === false && function_exists('curl_init')) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $sUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_VERBOSE => true,
            ]);

            $sContent = @curl_exec($ch);
            curl_close($ch);
        }

        // Will return false if no method is available, or if either fails
        // This will cause a JavaScript alert to be triggered by the AJAX call
        return $sContent;
    }

    /**
     * method returns the generated report files
     *
     * @return array
     */
    public static function getGeneratedReport()
    {
        $reporting_output = [];
        $reportingList = Reporting::getReportingList(\FacebookProductAd::$iShopId);

        if (!empty($reportingList)) {
            foreach ($reportingList as $list) {
                $reporting_data = explode('_', $list['iso_feed']);

                $id_lang = \Language::getIdByIso($reporting_data[0]);
                $language = new \Language((int) $id_lang);

                $id_currency = \Currency::getIdByIsoCode($reporting_data[2]);
                $currency = new \Currency($id_currency);

                $id_country = \Country::getByIso(\Tools::strtolower($reporting_data[1]));
                $country = new \Country((int) $id_country);

                $reporting_output[] = [
                    'full' => $reporting_data[0] . '_' . $reporting_data[1] . '_' . $reporting_data[2],
                    'lang_iso' => $language->name . ' - ' . \Tools::strtoupper($language->iso_code),
                    'currency' => $currency->sign . ' - ' . $currency->iso_code,
                    'country' => \Country::getNameById(\FacebookProductAd::$iCurrentLang, $country->id) . ' - ' . $country->iso_code,
                ];
            }
        }

        return $reporting_output;
    }

    /**
     * method format the product title by uncap or not or leave uppercase only first character of each word
     *
     * @param string $sTitle
     * @param int $iFormatMode
     *
     * @return mixed
     */
    public static function formatProductTitle($sTitle, $iFormatMode = 0)
    {
        $sResult = '';

        // format title
        if ($iFormatMode == 0) {
            $sResult = self::strToUtf8($sTitle);
        } else {
            $sResult = self::strToLowerUtf8($sTitle);

            if ($iFormatMode == 1) {
                $aResult = explode(' ', $sResult);

                foreach ($aResult as &$sWord) {
                    $sWord = \Tools::ucfirst(trim($sWord));
                }

                $sResult = implode(' ', $aResult);
            } else {
                $sResult = \Tools::ucfirst(trim($sResult));
            }
        }

        return $sResult;
    }

    /**
     * method format the product name with combination
     *
     * @param int $iAttrId
     * @param int $iCurrentLang
     * @param int $iShopId
     *
     * @return mixed
     */
    public static function getProductCombinationName($iAttrId, $iCurrentLang, $iShopId)
    {
        // set var
        $sProductName = '';

        $aCombinations = moduleDao::getProductComboAttributes($iAttrId, $iCurrentLang, $iShopId);

        if (!empty($aCombinations)) {
            $sExtraName = '';
            foreach ($aCombinations as $c) {
                $sExtraName .= ' ' . stripslashes($c['name']);
            }
            $sProductName .= $sExtraName;
            unset($sExtraName);
        }
        unset($aCombinations);

        return $sProductName;
    }

    /**
     * method uncap the product title
     *
     * @param int $iAdvancedProdName
     * @param string $sProdName
     * @param string $sCatName
     * @param string $sManufacturerName
     * @param int $iLength
     *
     * @return mixed
     */
    public static function truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength)
    {
        if (function_exists('mb_substr')) {
            switch ($iAdvancedProdName) {
                case 0:
                    $sProdName = mb_substr($sProdName, 0, $iLength);

                    break;
                case 1:
                    $sProdName = mb_substr($sCatName . ' - ' . $sProdName, 0, $iLength);

                    break;
                case 2:
                    $sProdName = mb_substr($sProdName . ' - ' . $sCatName, 0, $iLength);

                    break;
                case 3:
                    $sBrand = !empty($sManufacturerName) ? $sManufacturerName . ' - ' : '';
                    $sProdName = mb_substr($sBrand . $sProdName, 0, $iLength);

                    break;
                case 4:
                    $sBrand = !empty($sManufacturerName) ? ' - ' . $sManufacturerName : '';
                    $sProdName = mb_substr($sProdName . $sBrand, 0, $iLength);

                    break;
                default:
                    break;
            }
        }

        return stripslashes($sProdName);
    }

    /**
     * method Used by uncapProductTitle. strtolower doesn't work with UTF-8
     * The second solution if no mb_strtolower available is not perfect but will work
     * with most European languages. Worse comes to worse, the person may chose not to uncap
     *
     * @param $sString
     *
     * @return mixed
     */
    public static function strToLowerUtf8($sString)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($sString, 'utf-8') : utf8_encode(\Tools::strtolower(utf8_decode($sString)));
    }

    /**
     * method Used by uncapProductTitle. strToUtf8 doesn't work with UTF-8
     * The second solution if no mb_convert_encoding available is not perfect but will work
     * with most European languages. Worse comes to worse, the person may chose not to uncap
     *
     * @param $sString
     *
     * @return mixed
     */
    public static function strToUtf8($sString)
    {
        return function_exists('mb_convert_encoding') ? mb_convert_encoding($sString, 'utf-8') : utf8_encode(utf8_decode($sString));
    }

    /**
     * method Check file based on language and country ISO code
     *
     * @param string $sIsoLang
     * @param string $sIsoCountry
     * @param string $sCurrency
     *
     * @return mixed
     */
    public static function checkReportFile($sIsoLang, $sIsoCountry, $sCurrency)
    {
        $sFilename = moduleConfiguration::FPA_REPORTING_DIR . 'reporting-' . $sIsoLang . '-' . \Tools::strtolower($sIsoCountry) . '-' . $sCurrency . '.txt';

        return (file_exists($sFilename) && filesize($sFilename)) ? true : false;
    }

    /**
     * @param mixed $aParams
     * @param mixed $bUseTax
     * @param mixed $bUseShipping
     * @param mixed $bUseWrapping
     *
     * @return float
     */
    public static function getOrderPrice($aParams, $bUseTax, $bUseShipping, $bUseWrapping)
    {
        $fOderAmount = 0.0;

        if (!empty($aParams)) {
            // case with tax
            if (!empty($bUseTax)) {
                if (!empty($bUseShipping) && !empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_paid;
                } elseif (empty($bUseShipping) && !empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_paid - $aParams->total_shipping_tax_incl;
                } elseif (!empty($bUseShipping) && empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_paid - $aParams->total_wrapping_tax_incl;
                } elseif (empty($bUseShipping) && empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_paid - $aParams->total_wrapping_tax_incl - $aParams->total_shipping_tax_incl;
                }
            } // case without tax
            elseif (empty($bUseTax)) {
                if (!empty($bUseShipping) && !empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_paid_tax_excl;
                } elseif (empty($bUseShipping) && !empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_products + $aParams->total_wrapping_tax_excl;
                } elseif (!empty($bUseShipping) && empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_products + $aParams->total_shipping_tax_excl;
                } elseif (empty($bUseShipping) && empty($bUseWrapping)) {
                    $fOderAmount = $aParams->total_products;
                }
            }
        }

        return (float)number_format($fOderAmount, 2, '.', '');
    }

    /* Clean up MS Word style quotes and other characters Facebook does not like */
    /**
     *  method clean up MS Word style quotes and other characters Facebook does not like
     *
     * @param string $str
     *
     * @return mixed
     */
    public static function cleanUp($str)
    {
        $str = str_replace('<br>', "\n", $str);
        $str = str_replace('<br />', "\n", $str);
        $str = str_replace('</p>', "\n", $str);
        $str = str_replace('<p>', '', $str);
        $str = str_replace('&', '', $str);

        $quotes = [
            "\xC2\xAB" => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB" => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
            "\xE2\x80\x94" => '-', // —
        ];

        $str = strtr($str, $quotes);

        return trim(strip_tags($str));
    }

    /**
     * detectCurrentPage() method returns current page type
     */
    public static function detectCurrentPage()
    {
        $step_id = self::getStepId((int) \Context::getContext()->cart->id);
        $sCurrentTypePage = '';

        // use case - home page
        if (\Tools::getValue('controller') == 'index') {
            $sCurrentTypePage = 'other';
        } // use case - search results page
        elseif (\Tools::getValue('controller') == 'search' && empty(\Context::getContext()->controller->module)) {
            $sCurrentTypePage = 'search';
        } // use case - order page
        elseif ((\Tools::getValue('controller') == 'order'
                || \Tools::getValue('controller') == 'orderopc')
            && \Tools::getValue('step') == false
        ) {
            if (isset(\Context::getContext()->controller->page_name)) {
                if (\Context::getContext()->controller->page_name == 'checkout') {
                    $sCurrentTypePage = 'checkout';
                } else {
                    if ($step_id == 0 && \Context::getContext()->controller->page_name != 'checkout') {
                        $sCurrentTypePage = 'cart';
                    } else {
                        $sCurrentTypePage = 'other';
                    }
                }
            } else {
                if ($step_id == 0) {
                    $sCurrentTypePage = 'cart';
                }
            }
        } elseif ((\Tools::getValue('controller') == 'submit') || \Tools::getValue('controller') == 'orderconfirmation') {
            $sCurrentTypePage = 'purchase';
        } // use case - category page
        elseif (\Tools::getValue('id_category')) {
            $sCurrentTypePage = 'category';
        } // use case - product page
        elseif (\Tools::getValue('id_product')) {
            $sCurrentTypePage = 'product';
        } elseif (\Tools::getValue('controller') == 'manufacturer') {
            $sCurrentTypePage = 'manufacturer';
        } elseif (\Tools::getValue('controller') == 'pricesdrop') {
            $sCurrentTypePage = 'promotion';
        } elseif (\Tools::getValue('controller') == 'newproducts') {
            $sCurrentTypePage = 'newproducts';
        } elseif (\Tools::getValue('controller') == 'bestsales') {
            $sCurrentTypePage = 'bestsales';
        } elseif (\Tools::getValue('controller') == 'cart') {
            $sCurrentTypePage = 'cart';
        } elseif (\Tools::getValue('controller') == 'contact') {
            $sCurrentTypePage = 'contact';
        } elseif (\Tools::getValue('controller') == 'supercheckout') { // Handle for supercheckout module
            $sCurrentTypePage = 'checkout';
        } else {
            $sCurrentTypePage = 'other';
        }

        return $sCurrentTypePage;
    }

    /**
     * method returns all products displayed by home featured module
     *
     * @param string $sName
     * @param int $iLimit
     *
     * @return array $aProducts
     */
    public static function getHomeFeaturedProducts($sName, $iLimit)
    {
        $oCategory = new \Category(\Context::getContext()->shop->getCategory(), \FacebookProductAd::$iCurrentLang);

        return $oCategory->getProducts((int) \FacebookProductAd::$iCurrentLang, 1, $iLimit, 'position');
    }

    /**
     * getNewProducts() method returns all products displayed by block new products module
     *
     * @param string $sName
     * @param int $iLimit
     *
     * @return array $aProducts
     */
    public static function getNewProducts($sName, $iLimit)
    {
        // set var
        $aProducts = [];

        if (!\Configuration::get('PS_NB_DAYS_NEW_PRODUCT') || \Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) {
            $aProducts = \Product::getNewProducts((int) \FacebookProductAd::$iCurrentLang, 0, $iLimit);
        }

        return $aProducts;
    }

    /**
     * getBestSellersProducts() method returns all products displayed by block best sellers module
     *
     * @return array $aProducts
     */
    public static function getBestSellersProducts()
    {
        // set var
        $aProducts = [];

        if (!\Configuration::get('PS_CATALOG_MODE') || \Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) {
            $iProducPerPage = !empty(\Configuration::get('PS_BLOCK_BESTSELLERS_TO_DISPLAY')) ? \Configuration::get('PS_BLOCK_BESTSELLERS_TO_DISPLAY') : 10;
            $aProducts = \ProductSale::getBestSales((int) \FacebookProductAd::$iCurrentLang, 0, $iProducPerPage);
        }

        return $aProducts;
    }

    /**
     * method returns all products displayed by block specials module
     *
     * @param string $sName
     * @param int $iLimit
     *
     * @return array $aProducts
     */
    public static function getBlockSpecials($sName, $iLimit)
    {
        // set var
        $aProducts = [];

        if (!\Configuration::get('BLOCKSPECIALS_SPECIALS_NBR') || \Configuration::get('PS_BLOCK_SPECIALS_DISPLAY')) {
            $aProducts = \Product::getPricesDrop((int) \FacebookProductAd::$iCurrentLang, 0, $iLimit);
        }

        return $aProducts;
    }

    /**
     * Sanitize product properties formatted as array instead of a string matching to the current language
     *
     * @param $property
     * @param $iLangId
     *
     * @return mixed|string
     */
    public static function sanitizeProductProperty($property, $iLangId)
    {
        $content = '';

        // check if the product name is an array
        if (is_array($property)) {
            if (count($property) == 1) {
                $content = reset($property);
            } elseif (isset($property[$iLangId])) {
                $content = $property[$iLangId];
            }
        } else {
            $content = $property;
        }

        return $content;
    }

    /**
     * Handle the excluded word of the title
     *
     * @param $product_name
     *
     * @return mixed|string
     */
    public static function handleExcludedWords($product_name)
    {
        $excluded_words = json_decode(\FacebookProductAd::$conf['FPA_EXCLUDED_WORDS'], true);
        $product_name_clean = $product_name;

        if (!empty($excluded_words) && is_array($excluded_words)) {
            foreach ($excluded_words as $word) {
                $product_name_clean = str_replace($word, '', $product_name_clean);
                $product_name_clean = str_replace('  ', ' ', $product_name_clean);
            }
        }

        return $product_name_clean;
    }

    /**
     * build the display tag
     *
     * @param array $aDynTags
     * @param string $sPageType
     *
     * @return array $aAssign all tag information
     *
     */
    public static function buildDynDisplayTag($aDynTags, $sPageType)
    {
        try {
            // get the pixel information
            $oTagsCtrl = basePixel::get($sPageType, $aDynTags);

            // set Pixel code
            $aAssign['sPixel'] = \FacebookProductAd::$conf['FPA_PIXEL'];

            $oTagsCtrl->set();
            $aAssign['aDynTags'] = $oTagsCtrl->display();

            // use case - check if not empty and get the tracking type as we need it to open the fbq Facebook JS object
            if (!empty($aAssign['aDynTags'])) {
                $aAssign['sCR'] = "\n";

                $aAssign['aTrackingType'] = $aAssign['aDynTags']['tracking_type'];

                $aAssign['sJsObjName'] = moduleConfiguration::FPA_JS_NAME;
                unset($aAssign['aDynTags']['tracking_type']);
                unset($aAssign['aDynTags']['js_code']);
            }

            unset($oTagsCtrl);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $aAssign;
    }

    /**
     * get order if for Paypox
     *
     * @param int $id_cart
     */
    public static function getOrderIdForPaypox($id_cart)
    {
        $iOrderId = moduleDao::getOrderIdFromCart($id_cart);

        return (int)$iOrderId;
    }

    /**
     * check the gtin value
     *
     * @param string $sPriority the priority
     * @param array $aProduct the product information
     *
     * @return mixed
     */
    public static function getGtin($sPriority, $aProduct)
    {
        $sGtin = '';

        if ($sPriority == 'ean') {
            if (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            } elseif (
                !empty($aProduct['upc'])
                && (\Tools::strlen($aProduct['upc']) == 8
                    || \Tools::strlen($aProduct['upc']) == 12
                    || \Tools::strlen($aProduct['upc']) == 13)
            ) {
                $sGtin = $aProduct['upc'];
            }
        } elseif ($sPriority == 'upc') {
            if (
                !empty($aProduct['upc'])
                && (\Tools::strlen($aProduct['upc']) == 8
                    || \Tools::strlen($aProduct['upc']) == 12
                    || \Tools::strlen($aProduct['upc']) == 13)
            ) {
                $sGtin = $aProduct['upc'];
            } elseif (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            }
        } elseif ($sPriority == 'isbn') {
            if (!empty($aProduct['isbn']) && \Tools::strlen($aProduct['isbn']) == 13) {
                $sGtin = $aProduct['isbn'];
            } elseif (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            }
        }

        return $sGtin;
    }

    /**
     * get the exclusion rules names
     *
     * @param array $aExclusionRules the rules
     *
     * @return mixed
     */
    public static function getExclusionRulesName($aExclusionRules)
    {
        // Array to format th;e values with good value
        $aData = $aExclusionRules;

        foreach ($aExclusionRules as $sKey => $sValue) {
            $aTmpData = moduleTools::handleGetConfigurationData($sValue['exclusion_value']);

            if ($sValue['type'] !== null) {
                switch ($sValue['type']) {
                    case 'word':
                        if (isset($aTmpData['exclusionData'])) {
                            $aData[$sKey]['exclusion_value_text'] = $aTmpData['exclusionData'];
                        }

                        break;
                    case 'feature':
                        $aFeature = \FeatureValue::getFeatureValuesWithLang(
                            \FacebookProductAd::$iCurrentLang,
                            (int) $aTmpData['exclusionOn']
                        );
                        foreach ($aFeature as $sFeature) {
                            if (
                                $sFeature['id_feature_value'] == (int) $aTmpData['exclusionData']
                            ) {
                                $aData[$sKey]['exclusion_value_text'] = $sFeature['value'];
                            }
                        }

                        break;
                    case 'attribute':
                        $aAttribute = \AttributeGroup::getAttributes(
                            \FacebookProductAd::$iCurrentLang,
                            (int) $aTmpData['exclusionOn']
                        );

                        foreach ($aAttribute as $sAttribute) {
                            if (
                                $sAttribute['id_attribute'] == (int) $aTmpData['exclusionData']
                            ) {
                                $aData[$sKey]['exclusion_value_text'] = $sAttribute['name'];
                            }
                        }

                        break;
                    default:
                        $sType = '';

                        break;
                }
                unset($aTmpData);
                unset($aFeature);
                unset($aAttribute);
            }
        }

        return $aData;
    }

    /**
     * to compare date
     *
     * @param string $sDate1
     * @param string $sDate2
     *                       return int : difference entre les dates
     */
    public static function dateCompare($sDate1, $sDate2)
    {
        $dDate1 = date_create($sDate1);
        $dDate2 = date_create($sDate2);
        $iDiff = date_diff($dDate1, $dDate2);

        // if date2 > date1 return 0 else return 1
        return $iDiff->invert;
    }

    /**
     * Get the order id by its cart id.
     *
     * @param int $id_cart Cart id
     *
     * @return int $id_order
     */
    public static function getIdByCartId($id_cart)
    {
        $query = new \DbQuery();
        $query->select('o.id_order');
        $query->from('orders', 'o');
        $query->where('o.`id_cart` = ' . (int) $id_cart);

        return (int)\Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * format the date for Google prerequisistes
     *
     * @param string $sDate
     *
     * @return mixed
     */
    public static function formatDateISO8601($sDate)
    {
        $sDate = new \DateTime($sDate);

        return $sDate->format(\DateTime::ISO8601);
    }

    /**
     * method returns the consent status
     *
     * @return int
     */
    public static function getConsentStatus()
    {
        $iConsentLvl = 0;

        try {
            // Use case with ACB module
            if (!empty(moduleTools::isInstalled('pm_advancedcookiebanner'))) {
                $object_acb = moduleTools::isInstalled('pm_advancedcookiebanner', [], true);
                if (version_compare($object_acb->version, '4.0.0', '<')) {
                    $iConsentLvl = \AcbCookie::getConsentLevel();
                } else {
                    $consentData = $object_acb->getModuleAuthorization('facebookproductad');

                    if (empty($consentData)) {
                        exit;
                    }

                    if (!empty($consentData['allowed'])) {
                        if ($consentData['moduleCategory'] == 'analytics') {
                            $iConsentLvl = 1;
                        } elseif ($consentData['moduleCategory'] == 'marketing') {
                            $iConsentLvl = 2;
                        } else {
                            $iConsentLvl = 3;
                        }
                    }
                }
            } else {
                // Use case with the trigger click event on accept all button
                $iConsentLvl = isset(\Context::getContext()->cookie->bt_fbda_consent_lvl) ? \Context::getContext()->cookie->bt_fbda_consent_lvl : 0;
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 2, $e->getCode(), null, null, true);
        }

        return $iConsentLvl;
    }

    /**
     * Prepares and returns user data for Facebook API calls
     *
     * @param \Context $context PrestaShop context object
     * @param string $event Event name (e.g. 'Purchase', 'ViewContent')
     * @param string $type Content type
     * @param string $contentCategory Content category
     * @param string|array $contentIds Content ID(s)
     * @param string $contentName Content name
     * @param bool $onlyReturnEventId Whether to return only the event ID
     * @param array $priceData Price data for certain events
     * @param string $currentPage Current page identifier
     * @param int|null $clientTimestamp Client-side timestamp
     * @param int|null $orderId Order ID for Purchase events
     * @param string|null $clientUrl Client URL for event_source_url
     * @param string|null $sharedEventId Shared event_id from pixel for deduplication
     *
     * @return array|string Data for API call or event ID
     */
    public static function getApiUserData(\Context $context, $event, $type, $contentCategory = '', $contentIds = '', $contentName = '', $onlyReturnEventId = false, $priceData = [], $currentPage = '', $clientTimestamp = null, $orderId = null, $clientUrl = null, $sharedEventId = null)
    {
        $customer_data = [];
        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';

        if (empty($fbc) && isset($_GET['fbclid'])) {
            // Ensure fbclid is preserved exactly as received without any modification
            $fbclid = $_GET['fbclid'];
            // Validate fbclid format before using it
            if (self::isValidFbclid($fbclid)) {
                // Calculate subdomain index according to Meta specifications
                $subdomainIndex = self::calculateSubdomainIndex();
                // Use MILLISECONDS for the timestamp as required by Meta
                $timestamp = round(microtime(true) * 1000);
                $fbc = 'fb.' . $subdomainIndex . '.' . $timestamp . '.' . $fbclid;

                // Set cookie with proper domain and 90-day expiration as recommended by Meta
                $domain = self::getCookieDomain();
                setcookie('_fbc', $fbc, time() + (90 * 24 * 60 * 60), '/', $domain, true, false);
            }
        }

        // Validate existing fbc format to prevent truncation issues
        if (!empty($fbc) && !self::isValidFbcFormat($fbc)) {
            // If fbc is malformed, clear it to prevent Facebook API errors
            $fbc = '';
            setcookie('_fbc', '', time() - 3600, '/'); // Clear invalid cookie
        }

        // Use shared event_id from pixel if provided, otherwise generate one
        if (!empty($sharedEventId)) {
            $event_id = $sharedEventId;
        } else {
            $event_id = self::generateConsistentEventId($context, $event, $contentIds, $orderId);
        }

        // Prepare customer data
        $customer_data = self::prepareCustomerData($context, $event_id, $fbp, $fbc);

        // Prepare user data for API call
        $user_data = self::prepareUserData($customer_data);

        // Prepare custom data with enhanced validation
        $custom_data = self::prepareCustomData($contentCategory, $contentIds, $contentName, $type, $event, $priceData, $orderId);

        // Enhanced timestamp handling to prevent creationTime errors
        $now = time();

        // ALWAYS prioritize client timestamp to ensure consistency between pixel and API
        // This is critical for Facebook's deduplication system
        if (!empty($clientTimestamp) && $clientTimestamp > 0) {
            $event_time = $clientTimestamp;

            // Log for debugging but don't reject the timestamp
            if (abs($clientTimestamp - $now) > 3600) {
                \PrestaShopLogger::addLog(
                    'FacebookProductAd: Large time difference detected between client and server: ' . abs($clientTimestamp - $now) . ' seconds. Using client time.',
                    1, // Lower severity - informational only
                    null,
                    'FacebookProductAd',
                    1,
                    true
                );
            }
        } else {
            // Only use server time as fallback
            $event_time = $now;
        }

        // Enhanced fbc timestamp validation
        $fbc_timestamp = null;
        $fbc_timestamp_seconds = null;
        if (!empty($fbc)) {
            // Expected format: fb.<subdomainIndex>.<timestamp>.<fbclid>
            $parts = explode('.', $fbc);
            if (count($parts) >= 4 && is_numeric($parts[2])) {
                $fbc_timestamp = (int)$parts[2];

                // Convert milliseconds to seconds for comparison
                $fbc_timestamp_seconds = $fbc_timestamp > 9999999999 ? intval($fbc_timestamp / 1000) : $fbc_timestamp;

                // Validate fbc timestamp is reasonable (not in future, not too old)
                $maxFbcAge = 86400 * 90; // 90 days max age for fbc (Meta recommendation)
                if ($fbc_timestamp_seconds > $now || $fbc_timestamp_seconds < ($now - $maxFbcAge)) {
                    // Invalid fbc timestamp, clear it
                    $fbc = '';
                    $fbc_timestamp = null;
                    $domain = self::getCookieDomain();
                    setcookie('_fbc', '', time() - 3600, '/', $domain);
                }
            }
        }

        // Ensure event_time is never before fbc_timestamp (Facebook requirement)
        if ($fbc_timestamp && $fbc_timestamp_seconds <= $now && $event_time < $fbc_timestamp_seconds) {
            $event_time = $fbc_timestamp_seconds;
        }

        // Prepare final data for API call
        $dataForCall = [
            'data' => [
                [
                    'event_name' => $event,
                    'event_time' => $event_time,
                    'action_source' => 'website',
                    'event_id' => $event_id,
                    'event_source_url' => self::getEventSourceUrl($clientUrl),
                    'user_data' => $user_data,
                    'custom_data' => $custom_data,
                ],
            ],
        ];

        return $onlyReturnEventId ? $event_id : $dataForCall;
    }

    /**
     * Generate a unique event_id for each event occurrence
     * According to Meta documentation: "The event_id parameter is an identifier that can uniquely distinguish between similar events"
     * Each event occurrence must have a unique event_id for proper deduplication between browser and server
     *
     * @param \Context $context PrestaShop context object
     * @param string $event Event name
     * @param string|array $contentIds Content IDs
     * @param int|null $orderId Order ID for Purchase events
     *
     * @return string Unique event ID
     */
    private static function generateConsistentEventId(\Context $context, $event, $contentIds = '', $orderId = null)
    {
        // For Purchase events, use order reference (these are naturally unique per order)
        if ($event === 'Purchase' && !empty($orderId)) {
            $order = new \Order((int)$orderId);
            if (\Validate::isLoadedObject($order)) {
                if (!empty($order->reference)) {
                    return 'purchase_' . $order->reference;
                } else {
                    return 'purchase_order_' . $orderId;
                }
            }
        }

        // For other events, create a unique ID for each event occurrence
        // Meta documentation: "For other events without an intrinsic ID number, a random number can be used"
        $timestamp = time();
        $randomComponent = rand(1000, 9999);

        // Get user identifier (matches client logic)
        if (!empty($context->customer->id)) {
            $userId = hash('sha256', (string)$context->customer->id);
        } else {
            $userId = (string)$context->cart->id_guest;
        }

        // Each occurrence gets a unique ID combining timestamp and random component
        return strtolower($event) . '_' . $userId . '_' . $timestamp . '_' . $randomComponent;
    }

    /**
     * Calculate subdomain index according to Meta specifications
     * 'com' = 0, 'example.com' = 1, 'www.example.com' = 2
     *
     * @return int
     */
    private static function calculateSubdomainIndex()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        if (empty($host)) {
            // Fallback to PrestaShop configuration
            $host = \Configuration::get('PS_SHOP_DOMAIN');
            if (empty($host)) {
                $host = \Configuration::get('PS_SHOP_DOMAIN_SSL');
            }
        }

        if (empty($host)) {
            return 1; // Default fallback
        }

        // Count the number of dots to determine subdomain level
        $dotCount = substr_count($host, '.');

        // Meta specification: 'com' = 0, 'example.com' = 1, 'www.example.com' = 2
        if ($dotCount == 0) {
            return 0; // No dots (like 'localhost')
        } elseif ($dotCount == 1) {
            return 1; // One dot (like 'example.com')
        } else {
            return 2; // Two or more dots (like 'www.example.com' or 'shop.www.example.com')
        }
    }

    /**
     * Get cookie domain for _fbc cookie
     *
     * @return string
     */
    private static function getCookieDomain()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        if (empty($host)) {
            // Fallback to PrestaShop configuration
            $host = \Configuration::get('PS_SHOP_DOMAIN');
            if (empty($host)) {
                $host = \Configuration::get('PS_SHOP_DOMAIN_SSL');
            }
        }

        // Return empty string for localhost or IP addresses
        if (empty($host) || $host === 'localhost' || preg_match('/^\d+\.\d+\.\d+\.\d+$/', $host)) {
            return '';
        }

        return $host;
    }

    /**
     * Validates fbclid format according to Meta specifications
     * Meta fbclid is case-sensitive and should not be modified
     *
     * @param string $fbclid
     * @return bool
     */
    private static function isValidFbclid($fbclid)
    {
        // Basic validation: fbclid should be a non-empty string
        if (empty($fbclid) || !is_string($fbclid)) {
            return false;
        }

        $length = strlen($fbclid);
        // Meta fbclid typically ranges from 30 to 300 characters
        if ($length < 10 || $length > 500) {
            return false;
        }

        // More permissive validation - Meta fbclid can contain various characters
        // Allow alphanumeric, dots, dashes, underscores, and other URL-safe characters
        if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $fbclid)) {
            return false;
        }

        return true;
    }

    /**
     * Validates fbc format according to Meta specifications
     * Format: fb.<subdomainIndex>.<timestamp>.<fbclid>
     *
     * @param string $fbc
     * @return bool
     */
    private static function isValidFbcFormat($fbc)
    {
        if (empty($fbc) || !is_string($fbc)) {
            return false;
        }

        // Expected format: fb.<subdomainIndex>.<timestamp>.<fbclid>
        $parts = explode('.', $fbc);
        if (count($parts) < 4) {
            return false;
        }

        // Validate format components
        if ($parts[0] !== 'fb') {
            return false;
        }

        // Validate subdomain index (should be 0, 1, or 2 according to Meta specs)
        $subdomainIndex = (int)$parts[1];
        if (!is_numeric($parts[1]) || $subdomainIndex < 0 || $subdomainIndex > 2) {
            return false;
        }

        // Validate timestamp part (should be positive number)
        if (!is_numeric($parts[2]) || (int)$parts[2] <= 0) {
            return false;
        }

        // Validate fbclid part (reconstruct from remaining parts)
        $fbclid = implode('.', array_slice($parts, 3));
        if (!self::isValidFbclid($fbclid)) {
            return false;
        }

        return true;
    }

    /**
     * Prepares customer data
     *
     * @param \Context $context
     * @param string $event_id
     * @param string $fbp
     * @param string $fbc
     *
     * @return array
     */
    private static function prepareCustomerData(\Context $context, $event_id, $fbp, $fbc)
    {
        $customer_data = [
            'event_id' => $event_id,
            'fbp' => $fbp,
            'fbc' => $fbc,
            'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '',
            'event_source_url' => self::getEventSourceUrl(),
        ];

        if (!empty($context->customer->id)) {
            $customer_data = array_merge($customer_data, self::getLoggedInCustomerData($context));
        } else {
            $customer_data = array_merge($customer_data, self::getGuestCustomerData($context));
        }

        return $customer_data;
    }

    /**
     * Gets data for logged-in customers
     *
     * @param \Context $context
     *
     * @return array
     */
    private static function getLoggedInCustomerData(\Context $context)
    {
        $gender = new \Gender((int) $context->customer->id_gender);
        $id_address = \Address::getFirstCustomerAddressId((int) $context->customer->id);
        $customer_address = new \Address((int) $id_address);
        $state = new \State((int) $customer_address->id_state);
        $country = new \Country((int) $customer_address->id_country);

        $data = [
            'em' => hash('sha256', (string) $context->customer->email),
            'ph' => hash('sha256', $customer_address->phone),
            'fn' => hash('sha256', (string) $context->customer->firstname),
            'ln' => hash('sha256', (string) $context->customer->lastname),
            'db' => hash('sha256', (string) $context->customer->birthday),
            'ge' => hash('sha256', (string) ($gender->type == 0 ? 'm' : 'f')),
            'zp' => hash('sha256', (string) $customer_address->postcode),
            'country' => hash('sha256', (string) \Tools::strtolower($country->iso_code)),
            'external_id' => hash('sha256', (string) $context->customer->id),
        ];

        if (empty(\FacebookProductAd::$conf['FPA_HAS_WARNING'])) {
            $data['ct'] = hash('sha256', (string) $customer_address->city);
            $data['st'] = hash('sha256', (string) \Tools::strtolower($state->iso_code));
            if (!empty((string) self::getClientIpAddress())) {
                $data['client_ip_address'] = (string) self::getClientIpAddress();
            }
        }

        return $data;
    }

    /**
     * Gets data for guest customers
     *
     * @param \Context $context
     *
     * @return array
     */
    private static function getGuestCustomerData(\Context $context)
    {
        $data = [
            'em' => hash('sha256', ''),
            'ph' => hash('sha256', ''),
            'fn' => hash('sha256', ''),
            'ln' => hash('sha256', ''),
            'db' => hash('sha256', ''),
            'ge' => hash('sha256', ''),
            'zp' => hash('sha256', ''),
            'country' => hash('sha256', ''),
            'external_id' => hash('sha256', (string)$context->cart->id_guest),
        ];

        if (empty(\FacebookProductAd::$conf['FPA_HAS_WARNING'])) {
            $data['ct'] = hash('sha256', '');
            $data['st'] = hash('sha256', '');
            if (!empty((string) self::getClientIpAddress())) {
                $data['client_ip_address'] = (string) self::getClientIpAddress();
            }
        }

        return $data;
    }

    /**
     * Get the client IP address
     *
     * @return string
     */
    private static function getClientIpAddress()
    {
        // First try to use PrestaShop's native method if available
        if (method_exists('\\Tools', 'getRemoteAddr')) {
            return \Tools::getRemoteAddr();
        }

        // Fallback to manual detection with enhanced proxy support
        $ip = $_SERVER['REMOTE_ADDR'];

        // Check for various proxy headers in order of reliability
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // Standard proxy header
            'HTTP_X_REAL_IP',        // Nginx proxy
            'HTTP_CLIENT_IP',        // Some proxies
            'HTTP_X_FORWARDED',      // Some proxies
            'HTTP_X_CLUSTER_CLIENT_IP', // Load balancers
            'HTTP_FORWARDED_FOR',    // RFC 7239
            'HTTP_FORWARDED'         // RFC 7239
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $firstIp = trim($ips[0]);

                // Validate that it's a public IP
                if (filter_var($firstIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $ip = $firstIp;
                    break;
                }
            }
        }

        return $ip;
    }

    /**
     * Prepares user data for API call
     *
     * @param array $customer_data
     *
     * @return array
     */
    private static function prepareUserData($customer_data)
    {
        $user_data = [
            // Preserve the fbc value exactly as it is without any modification
            'fbc' => $customer_data['fbc'],
            'fbp' => $customer_data['fbp'],
            'em' => $customer_data['em'],
            'ph' => $customer_data['ph'],
            'fn' => $customer_data['fn'],
            'ln' => $customer_data['ln'],
            'db' => $customer_data['db'],
            'ge' => $customer_data['ge'],
            'ct' => isset($customer_data['ct']) ? $customer_data['ct'] : '',
            'st' => isset($customer_data['st']) ? $customer_data['st'] : '',
            'zp' => $customer_data['zp'],
            'country' => $customer_data['country'],
            'external_id' => $customer_data['external_id'],
            'client_ip_address' => !empty((string) self::getClientIpAddress()) ? (string) self::getClientIpAddress() : '',
            'client_user_agent' => $customer_data['client_user_agent'],
        ];

        return $user_data;
    }

    /**
     * Prepares custom data for API call
     *
     * @param string $contentCategory
     * @param string|array $contentIds
     * @param string $contentName
     * @param string $type
     * @param string $event
     * @param array $priceData
     * @param int|null $orderId Order ID for Purchase events
     *
     * @return array
     */
    private static function prepareCustomData($contentCategory, $contentIds, $contentName, $type, $event, $priceData, $orderId = null)
    {
        $custom_data = [
            'content_category' => $contentCategory,
            'content_ids' => $contentIds,
            'content_name' => $contentName,
            'content_type' => $type,
        ];

        if (($event == 'Purchase' || $event == 'ViewContent' || $event == 'AddToCart' || $event == 'AddPaymentInfo') && !empty($priceData)) {
            // Ensure currency is always uppercase and properly formatted
            $currency = isset($priceData['currency']) ? strtoupper(trim($priceData['currency'])) : '';
            $value = isset($priceData['value']) ? $priceData['value'] : '';

            // Validate currency format (must be 3 character ISO code)
            if (!empty($currency) && strlen($currency) === 3 && ctype_alpha($currency)) {
                $custom_data['currency'] = $currency;
            }

            // Ensure value is properly formatted as a float with 2 decimal places
            if (!empty($value) && is_numeric($value)) {
                $custom_data['value'] = number_format((float)$value, 2, '.', '');
            }
        }

        if ($event == 'ViewContent' || $event == 'PageView') {
            $custom_data['event_deduplication_key'] = self::generateDeduplicationKey($contentIds);
        }

        if ($event === 'Purchase' && !empty($orderId)) {
            $custom_data['order_id'] = $orderId;
        }

        return $custom_data;
    }

    /**
     * Generates a unique deduplication key for ViewContent event
     */
    private static function generateDeduplicationKey($contentIds)
    {
        $uniqueKey = '';

        // Utilise l'ID du contenu comme base
        if (is_array($contentIds)) {
            $uniqueKey = implode('_', $contentIds);
        } else {
            $uniqueKey = $contentIds;
        }

        // Ajoute un timestamp pour l'unicité
        $uniqueKey .= '_' . time();

        // Ajoute un identifiant de session si disponible
        if (isset($_COOKIE['PHPSESSID'])) {
            $uniqueKey .= '_' . $_COOKIE['PHPSESSID'];
        }

        return hash('sha256', $uniqueKey);
    }

    /**
     * method setup the customer data for API call
     *
     * @param \Context $context
     *
     * @return array
     */
    public static function getAdvancedMatchingData(\Context $context)
    {
        $customer_data = [];

        if (!empty($context->customer->id)) {
            $customer_data['em'] = (string) $context->customer->email;
            $customer_data['fn'] = (string) $context->customer->firstname;
            $customer_data['ln'] = (string) $context->customer->lastname;
        }

        return $customer_data;
    }

    /**
     * getStepId() method returns the good matching list name according to the current controller name
     *
     * @param int $iCartId
     *
     * @return int
     */
    public static function getStepId($iCartId = 0)
    {
        $iStepId = 0;

        if ($iCartId != 0) {
            $oCheckout = moduleDao::getCartSteps($iCartId);

            if (!empty($oCheckout)) {
                // detect the personal information - step 1
                if (
                    isset($oCheckout['checkout-personal-information-step'])
                    && (isset($oCheckout['checkout-personal-information-step']->step_is_reachable)
                        && $oCheckout['checkout-personal-information-step']->step_is_reachable == 1)
                    && (isset($oCheckout['checkout-personal-information-step']->step_is_complete)
                        && $oCheckout['checkout-personal-information-step']->step_is_complete == 0)
                ) {
                    $iStepId = 0;
                }

                // detect the address information - step 2
                if (
                    isset($oCheckout['checkout-addresses-step'])
                    && (isset($oCheckout['checkout-addresses-step']->step_is_reachable)
                        && $oCheckout['checkout-addresses-step']->step_is_reachable == true)
                    && (isset($oCheckout['checkout-addresses-step']->step_is_complete)
                        && $oCheckout['checkout-addresses-step']->step_is_complete == false)
                ) {
                    $iStepId = 1;
                }
                // detect the delivery information - step 3
                if (
                    isset($oCheckout['checkout-delivery-step'])
                    && (isset($oCheckout['checkout-delivery-step']->step_is_reachable)
                        && $oCheckout['checkout-delivery-step']->step_is_reachable == 1)
                    && (isset($oCheckout['checkout-delivery-step']->step_is_complete)
                        && $oCheckout['checkout-delivery-step']->step_is_complete == 0)
                ) {
                    $iStepId = 2;
                }
                // detect the payment information - step 4
                if (
                    isset($oCheckout['checkout-payment-step'])
                    && (isset($oCheckout['checkout-payment-step']->step_is_reachable)
                        && $oCheckout['checkout-payment-step']->step_is_reachable == 1)
                    && (isset($oCheckout['checkout-payment-step']->step_is_complete)
                        && $oCheckout['checkout-payment-step']->step_is_complete == 0)
                ) {
                    $iStepId = 3;
                }
            }
        }

        return $iStepId;
    }

    /**
     * method return available countries supported by Facebook
     *
     * @return array
     */
    public static function getAvailableTaxonomyCountries()
    {
        $saved_taxonomies = Feeds::getSavedTaxonomies((int) \FacebookProductAd::$iShopId);
        $shop_countries = \Country::getCountries((int) \FacebookProductAd::$oContext->cookie->id_lang, true);
        $taxonomies_output = [];

        if (!empty($saved_taxonomies)) {
            foreach ($saved_taxonomies as $data) {
                $id_country = \Country::getByIso(\Tools::strtolower($data['iso_country']));
                if (isset($shop_countries[$id_country])) {
                    $country = new \Country($id_country);
                    $taxonomies_output[$data['taxonomy']]['countries'][] = isset($country->name[\FacebookProductAd::$oContext->cookie->id_lang]) ? $country->name[\FacebookProductAd::$oContext->cookie->id_lang] : '';
                    $taxonomies_output[$data['taxonomy']]['id_lang'] = 1;
                }
            }
        }

        foreach ($taxonomies_output as $key => $data_output) {
            if (!empty($data_output['countries'])) {
                $taxonomies_output[$key]['countries'] = array_unique($data_output['countries']);
            }
        }

        return $taxonomies_output;
    }

    /**
     * method returns available carriers for one country zone
     *
     * @param int $iCountryZone
     *
     * @return array
     */
    public static function getAvailableCarriers($iCountryZone)
    {
        $aCarriers = \Carrier::getCarriers((int) \FacebookProductAd::$oContext->cookie->id_lang, true, false, (int) $iCountryZone, null, 5);

        return $aCarriers;
    }

    /**
     * method to build and factorize contentIds
     *
     * @param $pageType
     * @param $isoLang
     * @param $productId
     * @param $productsIds
     * @param $quote
     * @param $openTag
     * @param $closeTag
     *
     * @return mixed|array
     */
    public static function buildContentIds($pageType, $isoLang = null, $productId = null, $productsIds = [], $quote = '"', $openTag = '[', $closeTag = ']')
    {
        try {
            // init vars to create pixel
            $outpoutContentIds = '';
            $separator = \FacebookProductAd::$conf['FPA_COMBO_SEPARATOR'];
            $combo = \FacebookProductAd::$conf['FPA_P_COMBOS'];
            $id_lang = \Context::getContext()->language->id;

            if (!empty(\FacebookProductAd::$conf['FPA_ADD_LANG_ID']) && !empty($isoLang)) {
                $langPrefix = !empty($isoLang) ? \Tools::strtoupper($isoLang) : '';
            }
            if ($pageType == 'product') {
                // Init collection
                $productCollection = new \PrestaShopCollection('product');
                $productCollection->where('id_product', '=', (int) $productId);
                $product = $productCollection->getFirst();
                $xmlType = empty(\FacebookProductAd::$conf['FPA_P_COMBOS']) ? 'product' : 'combination';
                if (empty($combo)) {
                    if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                        $outpoutContentIds = moduleTools::constructFeedIdsRef($product->id, $id_lang, 'product', null, null, $product->reference);
                    } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                        $outpoutContentIds = moduleTools::constructFeedIdsEan($product->id, $id_lang, 'product', null, null, $product->ean13);
                    } else {
                        if (!empty($product->id)) {
                            $outpoutContentIds = moduleTools::constructFeedIdsBasic($product->id, $id_lang, 'product');
                        }
                    }
                } else {
                    $currentCombination = \Tools::getValue('id_product_attribute');

                    if (empty($currentCombination)) {
                        if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                            $outpoutContentIds = moduleTools::constructFeedIdsRef($product->id, $id_lang, 'product', $currentCombination, $separator, $product->reference);
                        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                            $outpoutContentIds = moduleTools::constructFeedIdsEan($product->id, $id_lang, 'product', $currentCombination, $separator, $product->ean13);
                        } else {
                            $outpoutContentIds = moduleTools::constructFeedIdsBasic($product->id, $id_lang, 'product', $currentCombination, $separator);
                        }
                    } else {
                        $combination = new \Combination((int) $currentCombination);
                        if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                            $outpoutContentIds = moduleTools::constructFeedIdsRef($product->id, $id_lang, 'combination', $currentCombination, $separator, $combination->reference);
                        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                            $outpoutContentIds = moduleTools::constructFeedIdsEan($product->id, $id_lang, 'combination', $currentCombination, $separator, $combination->ean13);
                        } else {
                            $outpoutContentIds = moduleTools::constructFeedIdsBasic($product->id, $id_lang, 'combination', $currentCombination, $separator);
                        }
                    }
                    $outpoutContentIds = '[' . $outpoutContentIds . ']';
                }
            } elseif ($pageType == 'product_listing') { // behaviour on page lists
                $productsData = [];
                if (!empty($productsIds)) {
                    // reset products ids to create new table
                    reset($productsIds);

                    foreach ($productsIds as $productsId) {
                        // Reforce check for default type according to the module option
                        $xmlType = empty(\FacebookProductAd::$conf['FPA_P_COMBOS']) ? 'product' : 'combination';
                        $idProduct = isset($productsId['id_product']) ? $productsId['id_product'] : $productsId['id'];
                        if (isset($idProduct)) {
                            if (empty($combo)) {
                                if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                                    $id = moduleTools::constructFeedIdsRef($idProduct, $id_lang, $xmlType, null, null, $productsId['reference']);
                                    $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                                    $id = moduleTools::constructFeedIdsEan($idProduct, $id_lang, $xmlType, null, null, $productsId['ean13']);
                                    $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                } else {
                                    $id = moduleTools::constructFeedIdsBasic($idProduct, $id_lang, $xmlType);
                                    $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                }
                            } else {
                                $defaultProductComboId = \Product::getDefaultAttribute($idProduct);

                                if (empty($defaultProductComboId)) {
                                    if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                                        $id = moduleTools::constructFeedIdsRef($idProduct, $id_lang, 'product', $defaultProductComboId, $separator, $productsId['reference']);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                                        $id = moduleTools::constructFeedIdsEan($idProduct, $id_lang, 'product', $defaultProductComboId, $separator, $productsId['ean13']);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    } else {
                                        $id = moduleTools::constructFeedIdsBasic($idProduct, $id_lang, 'product', $defaultProductComboId, $separator);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    }
                                } else {
                                    $combination = new \Combination((int) $defaultProductComboId);
                                    if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
                                        $id = moduleTools::constructFeedIdsRef($idProduct, $id_lang, 'combination', $defaultProductComboId, $separator, $combination->reference);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
                                        $id = moduleTools::constructFeedIdsEan($idProduct, $id_lang, 'combination', $defaultProductComboId, $separator, $combination->ean13);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    } else {
                                        $id = moduleTools::constructFeedIdsBasic($idProduct, $id_lang, 'combination', $defaultProductComboId, $separator);
                                        $productsData[] = strpos($id, '\'') !== false ? $id : $quote . $id . $quote;
                                    }
                                }
                            }
                        }
                    }
                    array_unique($productsData);
                    // init the string
                    $outpoutContentIds = $openTag;
                    $outpoutContentIds .= implode(',', $productsData);
                    $outpoutContentIds .= $closeTag;
                }
                array_unique($productsData);

                // init the string
                $outpoutContentIds = $openTag;
                $outpoutContentIds .= implode(',', $productsData);
                $outpoutContentIds .= $closeTag;
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
        }

        if (!empty($outpoutContentIds)) {
            // Check if string starts with [ and ends with ]
            if (substr($outpoutContentIds, 0, 1) !== '[' || substr($outpoutContentIds, -1) !== ']') {
                $outpoutContentIds = '[' . trim($outpoutContentIds, '[]') . ']';
            }
        }

        // send return to the method
        return $outpoutContentIds;
    }

    /**
     *  Method build the product url for data feed according to the module feed options
     *
     * @param object $product
     * @param int $langId
     * @param int $currencyId
     * @param int $idShop
     * @param int $ipa
     *
     * @return mixed
     */
    public static function buildProductUrl($product, $langId, $currencyId, $idShop, $ipa = null)
    {
        $url = '';
        $urlExtractPart = '';
        $product_category = new \Category((int) $product->getDefaultCategory(), (int) $langId);

        \Context::getContext()->language->id = (int)$langId;
        $addAnchor = \FacebookProductAd::$conf['FPA_INCL_ANCHOR'];
        $url = \Context::getContext()->link->getProductLink($product, null, \Tools::strtolower($product_category->link_rewrite), null, (int) $langId, (int) $idShop, (int) $ipa, false, false, false, [], $addAnchor);

        // handle the advanced parameters
        // format the current URL with currency or Google campaign parameters
        if (!empty(\FacebookProductAd::$conf['FPA_ADD_CURRENCY'])) {
            $urlExtractPart = substr($url, (strrpos($url, '#') ?: -1) + 1);
            $anchorPosition = strpos($url, '#');
            $url = str_replace('#' . $urlExtractPart, '', $url);
            $url .= (strpos($url, '?') !== false) ? '&SubmitCurrency=1&id_currency=' . (int) $currencyId : '?SubmitCurrency=1&id_currency=' . (int) $currencyId;
        }
        if (!empty(\FacebookProductAd::$conf['FPA_UTM_CAMPAIGN'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_campaign=' . \FacebookProductAd::$conf['FPA_UTM_CAMPAIGN'] : '?utm_campaign=' . \FacebookProductAd::$conf['FPA_UTM_CAMPAIGN'];
        }
        if (!empty(\FacebookProductAd::$conf['FPA_UTM_SOURCE'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_source=' . \FacebookProductAd::$conf['FPA_UTM_SOURCE'] : '?utm_source=' . \FacebookProductAd::$conf['FPA_UTM_SOURCE'];
        }
        if (!empty(\FacebookProductAd::$conf['FPA_UTM_CAMPAIGN'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_medium=' . \FacebookProductAd::$conf['FPA_UTM_MEDIUM'] : '?utm_medium=' . \FacebookProductAd::$conf['FPA_UTM_MEDIUM'];
        }

        if (!empty($addAnchor) && !empty($anchorPosition) && !empty($urlExtractPart)) {
            $url .= '#' . $urlExtractPart;
        }

        return $url;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param $typeId
     *
     * @return mixed
     */
    public static function constructFeedIdsBasic($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null)
    {
        $idOutput = '';
        $prefixId = '';

        if (!empty(\FacebookProductAd::$conf['FPA_ADD_LANG_ID'])) {
            $prefixId = \Tools::strtoupper(\FacebookProductAd::$conf['FPA_ID_PREFIX']) . \Tools::strtoupper(\Language::getIsoById((int) $idLang));
        } else {
            $prefixId = \Tools::strtoupper(\FacebookProductAd::$conf['FPA_ID_PREFIX']);
        }

        if ($xmlType == 'combination') {
            $combinedId = $prefixId . $idProduct . $separator . $idProductAttribute;
            $idOutput = strpos($combinedId, '\'') !== false ? $combinedId : '\'' . $combinedId . '\'';
        } elseif ($xmlType == 'product') {
            $productId = $prefixId . $idProduct;
            $idOutput = strpos($productId, '\'') !== false ? $productId : '\'' . $productId . '\'';
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param $typeId
     *
     * @return mixed
     */
    public static function constructFeedIdsEan($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $eanProduct = null)
    {
        $idOutput = '';

        if (
            !empty($eanProduct)
            && (\Tools::strlen($eanProduct) == 8
                || \Tools::strlen($eanProduct) == 12
                || \Tools::strlen($eanProduct) == 13)
        ) {
            $idOutput = $eanProduct;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $idLang, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param $typeId
     *
     * @return mixed
     */
    public static function constructFeedIdsRef($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $refProduct = null)
    {
        $idOutput = '';

        if (!empty($refProduct)) {
            $idOutput = $refProduct;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $idLang, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param $typeId
     *
     * @return mixed
     */
    public static function constructFeedIdsRefWhenHasSameValues($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $refProduct = null)
    {
        $idOutput = '';

        if (!empty($refProduct)) {
            $idOutput = $refProduct . $separator . $idProductAttribute;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $idLang, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param $typeId
     *
     * @return mixed
     */
    public static function constructFeedIdsEanWhenHasSameValues($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $eanProduct = null)
    {
        $idOutput = '';

        if (
            !empty($eanProduct)
            && (\Tools::strlen($eanProduct) == 8
                || \Tools::strlen($eanProduct) == 12
                || \Tools::strlen($eanProduct) == 13)
        ) {
            $idOutput = $eanProduct . $separator . $idProductAttribute;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $idLang, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * Check if there is an id shop set to 0 on stock available and can create export problem
     *
     * @return mixed
     */
    public static function checkStockAvailableShopId()
    {
        $query = new \DbQuery();
        $query->select('id_product');
        $query->from('stock_available', 'sa');
        $query->where('sa.id_shop = 0');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Method check the taxonomies from others modules feed
     *
     * @param string $isoLang
     *
     * @return array
     */
    public static function getTaxonomiesToImport($isoLang)
    {
        $gmcTaxonomies = [];
        $gmcpTaxonomies = [];
        $fbdaTaxonomies = [];
        $tkpTaxonomies = [];

        $checkGmcTable = ' show tables like "' . _DB_PREFIX_ . 'gmc_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkGmcTable))) {
            $gmcQuery = new \DbQuery();
            $gmcQuery->select('*');
            $gmcQuery->from('gmc_taxonomy_categories', 'gtc');
            $gmcQuery->where('gtc.id_shop=' . (int) \FacebookProductAd::$iShopId);
            $gmcQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $gmcTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($gmcQuery);
        }

        $checkGmcpTable = ' show tables like "' . _DB_PREFIX_ . 'gmcp_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkGmcpTable))) {
            $gmcpQuery = new \DbQuery();
            $gmcpQuery->select('*');
            $gmcpQuery->from('gmcp_taxonomy_categories', 'gtc');
            $gmcpQuery->where('gtc.id_shop=' . (int) \FacebookProductAd::$iShopId);
            $gmcpQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $gmcpTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($gmcpQuery);
        }

        $checkFbdaTable = ' show tables like "' . _DB_PREFIX_ . 'fpa_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkFbdaTable))) {
            $fbdaQuery = new \DbQuery();
            $fbdaQuery->select('*');
            $fbdaQuery->from('fpa_taxonomy_categories', 'gtc');
            $fbdaQuery->where('gtc.id_shop=' . (int) \FacebookProductAd::$iShopId);
            $fbdaQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $fbdaTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($fbdaQuery);
        }

        $checkTkpTable = ' show tables like "' . _DB_PREFIX_ . 'tkp_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkTkpTable))) {
            $tkpQuery = new \DbQuery();
            $tkpQuery->select('*');
            $tkpQuery->from('tkp_taxonomy_categories', 'gtc');
            $tkpQuery->where('gtc.id_shop=' . (int) \FacebookProductAd::$iShopId);
            $tkpQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $tkpTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tkpQuery);
        }

        return [
            'gmcTaxonomies' => $gmcTaxonomies,
            'gmcpTaxonomies' => $gmcpTaxonomies,
            'fbdaTaxonomies' => $fbdaTaxonomies,
            'tkpTaxonomies' => $tkpTaxonomies,
        ];
    }

    /**
     * Method set all available attributes managed in data feed
     *
     * @param mixed $module
     */
    public static function loadFeedTag($module)
    {
        return [
            '_no_available_for_order' => [
                'label' => $module->l('Product not available for order', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because you don\'t allow them to be ordered when out of stock', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            '_no_product_name' => [
                'label' => $module->l('Missing product name', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because the product names are missing', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
            '_no_required_data' => [
                'label' => $module->l('Missing mandatory information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because one of this mandatory product information is missing: name / description / link / image link', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            '_no_export_no_supplier_ref' => [
                'label' => $module->l('Product without MPN', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because they do not have a MPN reference', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_no_ean_upc' => [
                'label' => $module->l('Product without GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because they do not have a GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_no_stock' => [
                'label' => $module->l('No stock', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because they are out of stock', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            '_no_export_min_price' => [
                'label' => $module->l('Product under min price', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Products not exported because their price is lower than the minimum value defined in the configuration', 'moduleTools') . '.',
                'faq_id' => 131,
                'anchor' => '',
            ],
            // to be removed because this was only valid for gmcp old versions
            'excluded' => [
                'label' => $module->l('Excluded product list', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('this product or combination has been excluded from your feed as you defined it in the exclusion rules tab', 'moduleTools') . '.',
                'faq_id' => 0,
                'anchor' => '',
            ],
            'id' => [
                'label' => $module->l('Missing product ID', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "ID" tag => This is the unique identifier of the item', 'moduleTools') . '.',
                'faq_id' => 261,
                'anchor' => '',
            ],
            'title' => [
                'label' => $module->l('Missing product title', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "TITLE" tag => This is the title of the item', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
            'description' => [
                'label' => $module->l('Missing product description', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "DESCRIPTION" tag => This is the description of the item', 'moduleTools') . '.',
                'faq_id' => 273,
                'anchor' => '',
            ],
            'google_product_category' => [
                'label' => $module->l('No Facebook category', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "FACEBOOK PRODUCT CATEGORY" tag => You have to associate each product default category with an official Facebook category', 'moduleTools') . '.',
                'faq_id' => 281,
                'anchor' => '',
            ],
            'product_type' => [
                'label' => $module->l('No product type', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "PRODUCT TYPE" tag => Unlike the "Facebook Product Category" tag, the "Product Type" tag contains the information about the category of the product according to your own classification', 'moduleTools') . '.',
                'faq_id' => 258,
                'anchor' => '',
            ],
            'link' => [
                'label' => $module->l('Missing product link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "LINK" tag => This is the link of the item', 'moduleTools') . '.',
                'faq_id' => 260,
                'anchor' => '',
            ],
            'image_link' => [
                'label' => $module->l('Missing image link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "IMAGE LINK" tag => This is the URL of the main image of the product', 'moduleTools') . '.',
                'faq_id' => 257,
                'anchor' => '',
            ],
            'condition' => [
                'label' => $module->l('Missing product condition', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "CONDITION" tag => This is the condition of the item', 'moduleTools') . '.',
                'faq_id' => 259,
                'anchor' => '',
            ],
            'availability' => [
                'label' => $module->l('Missing product availability', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "AVAILABILITY" tag => This indicates the availability of the item', 'moduleTools') . '.',
                'faq_id' => 262,
                'anchor' => '',
            ],
            'price' => [
                'label' => $module->l('Missing product price', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "PRICE" tag => This is the price of the item', 'moduleTools') . '.',
                'faq_id' => 279,
                'anchor' => '',
            ],
            'gtin' => [
                'label' => $module->l('No GTIN code', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "GTIN" tag => The "Global Trade Item Number" is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 263,
                'anchor' => '',
            ],
            'brand' => [
                'label' => $module->l('No product brand', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "BRAND" tag => The product brand is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 278,
                'anchor' => '',
            ],
            'mpn' => [
                'label' => $module->l('No MPN reference', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "MPN" tag => The "Manufacturer Part Number" is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 264,
                'anchor' => '',
            ],
            'adult' => [
                'label' => $module->l('No adult tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "ADULT" tag => This tag indicates that the item is for adults only', 'moduleTools') . '.',
                'faq_id' => 274,
                'anchor' => '',
            ],
            'gender' => [
                'label' => $module->l('No gender tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "GENDER" tag => This tag allows you specify the gender of the people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 270,
                'anchor' => '',
            ],
            'age_group' => [
                'label' => $module->l('No age group tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "AGE GROUP" tag => This tag allows you to specify the age group of people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 271,
                'anchor' => '',
            ],
            'color' => [
                'label' => $module->l('No color', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "COLOR" tag => This tag indicates the color of the item', 'moduleTools') . '.',
                'faq_id' => 276,
                'anchor' => '',
            ],
            'size' => [
                'label' => $module->l('No size', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "SIZE" tag => This tag indicates the size of the item', 'moduleTools') . '.',
                'faq_id' => 275,
                'anchor' => '',
            ],
            'material' => [
                'label' => $module->l('No material tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "MATERIAL" tag => This tag indicates the material the item is made from', 'moduleTools') . '.',
                'faq_id' => 268,
                'anchor' => '',
            ],
            'pattern' => [
                'label' => $module->l('No pattern tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "PATTERN" tag => This tag indicates the pattern or graphic print on the item', 'moduleTools') . '.',
                'faq_id' => 269,
                'anchor' => '',
            ],
            'item_group_id' => [
                'label' => $module->l('No item group id', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "ITEM GROUP ID" tag => All items that are variants of a same product must have the same item group id', 'moduleTools') . '.',
                'faq_id' => 491,
                'anchor' => '',
            ],
            'shipping_weight' => [
                'label' => $module->l('No information on package weight', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => $module->l('The "SHIPPING WEIGHT" tag => This is the weight of the item used to calculate the shipping cost of the item', 'moduleTools') . '.',
                'faq_id' => 282,
                'anchor' => '',
            ],
            'shipping' => [
                'label' => $module->l('Missing shipping information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => $module->l('The "SHIPPING" tag => The shipping tag lets you override shipping information for specific items', 'moduleTools') . '.',
                'faq_id' => 51,
                'anchor' => '',
            ],
            'title_length' => [
                'label' => $module->l('Too long title', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => $module->l('Facebook requires your product titles to be no more than 150 characters long', 'moduleTools') . '.',
                'faq_id' => 280,
                'anchor' => '',
            ],
        ];
    }

    /**
     * @param object $errorMessage
     *
     * @return mixed
     */
    public static function formatApiErrorMessage($errorMessage)
    {
        $formatedMessages = [
            'message' => isset($errorMessage->error_user_msg) ? $errorMessage->error_user_msg : '',
            'title' => isset($errorMessage->error_user_title) ? $errorMessage->error_user_title : '',
            'code' => isset($errorMessage->code) ? $errorMessage->code : '',
        ];

        return json_encode($formatedMessages);
    }

    /**
     * get the event url for the API call
     *
     * @return mixed
     */
        /**
     * Get event source URL with priority to client URL for accurate Facebook Conversion API tracking
     *
     * @param string|null $clientUrl Client URL from JavaScript (already validated)
     * @return string URL-encoded event source URL
     */
    public static function getEventSourceUrl($clientUrl = null)
    {
        // Priority 1: Use validated client URL if provided
        if (!empty($clientUrl)) {
            // Client URL is already validated in the controller, use it directly
            \PrestaShopLogger::addLog(
                'Facebook API: Using client URL for event_source_url: ' . $clientUrl,
                1,
                null,
                'FacebookProductAd',
                1,
                true
            );
            return urlencode($clientUrl);
        }

        // Priority 2: Fallback to server-side URL detection
        \PrestaShopLogger::addLog(
            'Facebook API: Using server fallback for event_source_url',
            1,
            null,
            'FacebookProductAd',
            1,
            true
        );

        return self::getEventUrl();
    }

    /**
     * Get event URL from server-side detection (fallback method)
     *
     * @return string URL-encoded event URL
     */
    public static function getEventUrl()
    {
        // Fix for invalid.invalid domain issue - PHP 7.0+ compatible
        $host = '';
        $uri = '';

        // Try to get host from various sources
        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            // Fallback: try to get from PrestaShop configuration
            $shopDomain = \Configuration::get('PS_SHOP_DOMAIN');
            if (!empty($shopDomain)) {
                $host = $shopDomain;
            } else {
                // Final fallback: force correct domain
                $host = 'www.faubourg54.com';
            }
        }

        // Get URI
        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = '/';
        }

                // Validate host to prevent invalid.invalid and other invalid domains
        if ($host === 'invalid.invalid' ||
            strpos($host, 'invalid') !== false ||
            empty($host) ||
            $host === 'localhost' ||
            strpos($host, '127.0.0.1') !== false ||
            strpos($host, '::1') !== false) {

            // Try alternative PrestaShop domain configurations
            $shopDomainSSL = \Configuration::get('PS_SHOP_DOMAIN_SSL');
            if (!empty($shopDomainSSL)) {
                $host = $shopDomainSSL;
            } else {
                // Try to build from shop URL configuration
                $shopUrl = \Configuration::get('PS_SHOP_URL');
                if (!empty($shopUrl)) {
                    // Remove protocol and trailing slash
                    $host = preg_replace('#^https?://#', '', $shopUrl);
                    $host = rtrim($host, '/');
                } else {
                    // Final fallback: use current context shop domain
                    $context = \Context::getContext();
                    if (isset($context->shop) && !empty($context->shop->domain)) {
                        $host = $context->shop->domain;
                    } else {
                        // Last resort: generic placeholder that Facebook will accept
                        $host = 'shop.localhost';
                    }
                }
            }

            // Log for debugging
            \PrestaShopLogger::addLog(
                'Facebook API getEventUrl: Invalid host detected (' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'undefined') . '), forced to: ' . $host,
                2,
                null,
                'FacebookProductAd',
                1,
                true
            );
        }

        // Build protocol
        $protocol = 'https://'; // Force HTTPS for Facebook API
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocol = 'https://';
        } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            $protocol = 'https://';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $protocol = 'https://';
        }

        $url = $protocol . $host . $uri;

        return urlencode($url);
    }

    /**
     * method use for get saved data
     *
     * @param mixed $data the data information
     *
     * @return mixed
     */
    public static function handleGetConfigurationData($data)
    {
        $is_json = false;

        if (!empty($data)) {
            $is_json = is_string($data) && is_array(json_decode($data, true)) ? true : false;
        }

        if (empty($is_json)) {
            $handle = 'unserial';
            $handle .= 'ize';

            if ($data !== "false") {
                return call_user_func($handle, $data);
            }
        } else {
            return json_decode($data, true);
        }
    }

    /**
     * method use for set saved data
     *
     * @param array $data the data information
     *
     * @return mixed
     */
    public static function handleSetConfigurationData($data)
    {
        return json_encode($data);
    }

    /**
     * method if the description is HTML
     *
     * @param string $description
     *
     * @return mixed
     */
    public static function isHtml($description)
    {
        return $description != strip_tags($description) ? true : false;
    }

    /**
     * Standardize and format content_ids
     *
     * @param array $contentIdsToClean The content_ids to format
     *
     * @return array The formatted content_ids
     */
    public static function cleanContentIds($contentIdsToClean)
    {
        if (isset($contentIdsToClean['data'][0]['custom_data']['content_ids'])) {
            // Format content_ids
            $contentIds = $contentIdsToClean['data'][0]['custom_data']['content_ids'];

            // If not an array, convert to array
            if (!is_array($contentIds)) {
                $contentIds = array($contentIds);
            }

            // Clean and format each ID
            $formattedIds = array_map(function($id) {
                // Remove existing quotes if any
                $cleanId = trim($id, '"\'');
                // Add double quotes
                return '"' . $cleanId . '"';
            }, $contentIds);

            // Remove duplicates and reindex array
            return array_values(array_unique($formattedIds));
        }

        return [];
    }

    /**
     * Performs a secure HTTP request to the PrestaShop Addons/PM API
     *
     * This method handles communication with the PrestaShop Addons marketplace API
     * using proper security measures and error handling.
     *
     * @param array<string, mixed> $data Additional parameters to send with the request
     * @param string $c Domain component (default: prestashop)
     * @param string $s Subdomain component (default: api.addons)
     * @return mixed|false JSON decoded response or false on failure
     * @throws \InvalidArgumentException If domain parameters are invalid
     * @throws \RuntimeException If the request fails
     */
    private static function doHttpRequest(array $data = [], $c = 'prestashop', $s = 'api.addons')
    {
        // Input validation
        if (!is_string($c) || !is_string($s) || empty($c) || empty($s)) {
            throw new \InvalidArgumentException('Invalid domain parameters provided');
        }

        // Sanitize domain components
        $c = preg_replace('/[^a-z0-9\-]/', '', strtolower($c));
        $s = preg_replace('/[^a-z0-9\-]/', '', strtolower($s));

        // Merge default data with provided data
        $defaultData = [
            'version' => defined('_PS_VERSION_') ? _PS_VERSION_ : '',
            'iso_lang' => \Tools::strtolower(\FacebookProductAd::$sCurrentLang),
            'iso_code' => \Tools::strtolower(\Country::getIsoById((int)\Configuration::get('PS_COUNTRY_DEFAULT'))),
            'module_key' => isset(\FacebookProductAd::$oModule->module_key) ? \FacebookProductAd::$oModule->module_key : '',
            'method' => 'contributor',
            'action' => 'all_products',
        ];
        $data = array_merge($defaultData, $data);

        // Build request
        $postData = http_build_query($data);
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . strlen($postData),
                    'User-Agent: FacebookProductAd Module',
                    'Accept: application/json'
                ],
                'content' => $postData,
                'timeout' => 15,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false
            ]
        ];

        $context = stream_context_create($options);
        $url = 'https://api.addons.prestashop.com/';

        // Make request with error handling
        try {
            $response = \Tools::file_get_contents($url, false, $context);

            if ($response === false) {
                // Log error details
                $error = error_get_last();
                \PrestaShopLogger::addLog(
                    'HTTP request failed: ' . (isset($error['message']) ? $error['message'] : 'Unknown error'),
                    3
                );
                return false;
            }

            // Parse and validate response
            $decodedResponse = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \PrestaShopLogger::addLog(
                    'JSON decode error: ' . json_last_error_msg(),
                    3
                );
                return false;
            }

            if (empty($decodedResponse)) {
                \PrestaShopLogger::addLog(
                    'Empty response received from API',
                    3
                );
                return false;
            }

            return $decodedResponse;
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog(
                'Exception in HTTP request: ' . $e->getMessage(),
                3
            );
            return false;
        }
    }

    /**
     * Gets the list of installed PM modules from the Addons API with caching
     *
     * @return array<int|string, array<string|mixed>>
     */
    private static function getAddonsModulesFromApi()
    {
        // Check if cache exists and is valid
        $cacheKey = 'BT_MODULES_CS';
        $cacheDateKey = 'BT_MODULES_CS_LAST_UPDATE';
        $cacheData = \Configuration::get($cacheKey);
        $cacheDate = (int)\Configuration::get($cacheDateKey);
        $cacheExpiration = strtotime('+2 day', $cacheDate);

        // Return cache data if valid
        if (!empty($cacheData) && $cacheExpiration > time()) {
            $decodedCache = json_decode($cacheData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decodedCache;
            }
        }

        // Get fresh data from API
        $apiResponse = self::doHttpRequest();

        if (empty($apiResponse) || empty($apiResponse->products)) {
            return [];
        }

        // Transform and clean data
        $modulesData = [];
        foreach ($apiResponse->products as $module) {
            if (empty($module->id)) {
                continue;
            }

            // Get default shop currency ISO code
            $defaultCurrency = \Currency::getDefaultCurrency();
            if (!$defaultCurrency) {
                continue;
            }

            // Exclusion for FashionSeo
            if ($module->id != 53055) {
                $modulesData[(int) $module->id] = [
                    'name' => (string) $module->name,
                    'displayName' => (string) $module->displayName,
                    'url' => (string) $module->url,
                    'compatibility_from' => (string) $module->compatibility->from,
                    'compatibility_to' => (string) $module->compatibility->to,
                    'version' => (string) $module->version,
                    'description' => (string) $module->description,
                    'img' => (string) $module->img,
                    'nbRates' => (int) $module->nbRates,
                    'avgRate' => (float) $module->avgRate,
                    'cover' => isset($module->cover->big) ? (string) $module->cover->big : '',
                ];
            }
        }

        // Store in cache
        \Configuration::updateValue($cacheKey, json_encode($modulesData));
        \Configuration::updateValue($cacheDateKey, time());

        return $modulesData;
    }

    /**
     * Get filtered list of installed modules from cache
     *
     * @param string $technical_module_name Technical name of the current module
     * @return array<int|string, array<string|mixed>> List of filtered modules
     */
    public static function getModulesFromCache($technical_module_name)
    {
        if (empty($technical_module_name) || !is_string($technical_module_name)) {
            return [];
        }

        $modules = self::getAddonsModulesFromApi();
        if (empty($modules)) {
            return [];
        }

        $excludedTerms = ['bf', 'facebookads'];
        $filteredModules = [];

        foreach ($modules as $module) {
            if (!isset($module['name']) || !is_string($module['name'])) {
                continue;
            }

            $moduleName = strtolower($module['name']);
            $technicalName = strtolower($technical_module_name);

            // Skip current and related modules
            if (
                $moduleName === $technicalName
                || ($technicalName === 'facebookproductad' && stripos($moduleName, 'facebookproductad') !== false)
                || ($technicalName === 'facebookproductad' && stripos($moduleName, 'facebookproductadbf') !== false)
                || stripos($moduleName, $technicalName) !== false
            ) {
                continue;
            }

            // Skip excluded terms
            $excluded = false;
            foreach ($excludedTerms as $term) {
                if (stripos($moduleName, $term) !== false) {
                    $excluded = true;

                    break;
                }
            }

            if (!$excluded) {
                $filteredModules[] = $module;
            }
        }

        // Randomize filtered modules
        shuffle($filteredModules);

        return $filteredModules;
    }
}
