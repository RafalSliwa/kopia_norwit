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
use FacebookProductAd\Exclusion\exclusionTools;
use FacebookProductAd\Models\advancedExclusion;
use FacebookProductAd\Models\apiLog;
use FacebookProductAd\Models\customLabelTags;
use FacebookProductAd\Models\exclusionProduct;
use FacebookProductAd\Models\exportBrands;
use FacebookProductAd\Models\exportCategories;
use FacebookProductAd\Models\featureCategoryTag;
use FacebookProductAd\Models\Feeds;
use FacebookProductAd\Models\googleTaxonomy;
use FacebookProductAd\Models\tmpRules;
use FacebookProductAd\ModuleLib\labelTools;
use FacebookProductAd\ModuleLib\moduleTools;
use FacebookProductAd\ModuleLib\moduleUpdate;

class adminUpdate implements adminInterface
{
    /**
     * run() method update all tabs content of admin page
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

        switch ($sType) {
            case 'advice': // use case - update the advice form
            case 'basic': // use case - update basic settings
            case 'feed': // use case - update feed settings
            case 'feedList': // use case - update feed list settings
            case 'tag': // use case - update advanced tag settings
            case 'label': // use case - update custom label settings
            case 'labelState': // use case - update custom label statut active or not
            case 'customLabelList': // use case - update customlabelList with bulk action
            case 'position': // use case - update position with bulk action
            case 'customLabelDate': // use case - update custome label date with bulk action
            case 'customCheck': // use case - associate product to custom label during the data feed udpdate
            case 'facebook': // use case - update facebook campaign settings
            case 'reporting': // use case - update reporting settings
            case 'facebookCategoriesSync': // use case - update facebook categories sync action
            case 'xml': // use case - update the xml file
            case 'pixel': // use case - update the xml file
            case 'newFeed': // use case - update the xml file
            case 'exclusionRule': // use case - update exclusion rules
            case 'rulesList': // use case - update exclusion rules from list
            case 'consent': // use case - update consent
            case 'feedListSynch': // use case - update feed list from the serveur
            case 'log':
                // execute match function
                $aDisplayData = call_user_func_array([$this, 'update' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aDisplayData;
    }

    /**
     * method update advice settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateAdvice(array $aPost)
    {
        @ob_end_clean();

        // set
        $aAssign = [];
        \Configuration::updateValue('FPA_CONF_STEP_4', 1);

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/body.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method update basic settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateBasic(array $aPost)
    {
        @ob_end_clean();

        // set
        $aData = [];

        try {
            // register title
            $sShopLink = \Tools::getValue('bt_link');

            // clean the end slash if exists
            if (substr($sShopLink, -1) == '/') {
                $sShopLink = substr($sShopLink, 0, strlen($sShopLink) - 1);
            }

            \Configuration::updateValue('FPA_LINK', $sShopLink);
            \Configuration::updateValue('FPA_ADD_LANG_ID', \Tools::getValue('bt_add_lang_id'));
            \Configuration::updateValue('FPA_ID_PREFIX', \Tools::getValue('bt_prefix-id'));
            $this->updateLang($aPost, 'bt_home-cat-name', 'FPA_HOME_CAT', false, \FacebookProductAd::$oModule->l('type of product sold', 'adminUpdate'));
            \Configuration::updateValue('FPA_AJAX_CYCLE', \Tools::getValue('bt_ajax-cycle'));
            \Configuration::updateValue('FPA_IMG_SIZE', \Tools::getValue('bt_image-size'));
            \Configuration::updateValue('FPA_ADD_IMAGES', \Tools::getValue('bt_add_images'));
            \Configuration::updateValue('FPA_PROD_PRICE_TAX', \Tools::getValue('bt_add_product_price_tax'));
            \Configuration::updateValue('FPA_HOME_CAT_ID', \Tools::getValue('bt_home-cat-id'));
            \Configuration::updateValue('FPA_ADD_CURRENCY', \Tools::getValue('bt_add-currency'));
            \Configuration::updateValue('FPA_COND', \Tools::getValue('bt_product-condition'));
            \Configuration::updateValue('FPA_ADV_PRODUCT_NAME', \Tools::getValue('bt_advanced-prod-name'));
            \Configuration::updateValue('FPA_FEED_TOKEN', \Tools::getValue('bt_feed-token'));
            \Configuration::updateValue('FPA_ADV_PROD_TITLE', \Tools::getValue('bt_advanced-prod-title'));
            \Configuration::updateValue('FPA_FEED_PREF_ID', \Tools::getValue('bt_feed-tag-id'));
            \Configuration::updateValue('FPA_CONF_STEP_2', 1);

            $words = \Tools::getValue('bt_excluded_words');
            $excluded_words = [];

            if (!empty($words)) {
                // Use case if we have 2 expression or more
                $strPos = strpos($words, ',');
                if (!empty($strPos)) {
                    $excluded_words = explode(',', $words);
                } else {
                    $excluded_words[0] = $words;
                }
            }

            \Configuration::updateValue('FPA_EXCLUDED_WORDS', json_encode($excluded_words));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        moduleTools::getConfiguration();
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => \FacebookProductAd::$aAvailableLangCurrencyCountry]);
        moduleUpdate::create()->run('configuration', 'feed');

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('basics');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update feed management settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateFeed(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            if (\Tools::getIsset('bt_export')) {
                $bExportMode = \Tools::getValue('bt_export');
                \Configuration::updateValue('FPA_EXPORT_MODE', $bExportMode);

                // Use case export by categories
                if ($bExportMode == 0) {
                    $aCategoryBox = \Tools::getValue('bt_category-box');

                    if (!empty($aCategoryBox)) {
                        if (exportCategories::cleanTable(\FacebookProductAd::$iShopId)) {
                            foreach ($aCategoryBox as $iCatId) {
                                $export_category = new exportCategories();
                                $export_category->id_category = (int) $iCatId;
                                $export_category->id_shop = \FacebookProductAd::$iShopId;
                                $export_category->add();
                            }
                        }
                    }
                } else {
                    $aBrandBox = \Tools::getValue('bt_brand-box');
                    if (!empty($aBrandBox)) {
                        if (exportBrands::cleanTable(\FacebookProductAd::$iShopId)) {
                            foreach ($aBrandBox as $iBrandId) {
                                $export_brand = new exportBrands();
                                $export_brand->id_brands = (int) $iBrandId;
                                $export_brand->id_shop = \FacebookProductAd::$iShopId;
                                $export_brand->add();
                            }
                        }
                    }
                }
            }

            if (\Tools::getIsset('bt_url-error')) {
                \Configuration::updateValue('FPA_URL_PROD_ERROR', \Tools::getValue('bt_url-error'));
            }
            if (\Tools::getIsset('bt_cl_check')) {
                \Configuration::updateValue('FPA_CL_AUTO_UPDATE', \Tools::getValue('bt_cl_check'));
            }
            if (\Tools::getIsset('bt_prod-desc-type')) {
                \Configuration::updateValue('FPA_P_DESCR_TYPE', \Tools::getValue('bt_prod-desc-type'));
            }
            if (\Tools::getIsset('bt_incl-stock')) {
                \Configuration::updateValue('FPA_INC_STOCK', \Tools::getValue('bt_incl-stock'));
            }
            if (\Tools::getIsset('bt_incl-tag-adult')) {
                \Configuration::updateValue('FPA_INC_TAG_ADULT', \Tools::getValue('bt_incl-tag-adult'));
            }
            if (\Tools::getIsset('bt_incl-size')) {
                \Configuration::updateValue('FPA_INC_SIZE', \Tools::getValue('bt_incl-size'));
            }
            if (\Tools::getIsset('bt_size-opt')) {
                \Configuration::updateValue('FPA_SIZE_OPT', moduleTools::handleSetConfigurationData(\Tools::getValue('bt_size-opt')));
            }
            if (\Tools::getIsset('bt_incl-color')) {
                \Configuration::updateValue('FPA_INC_COLOR', \Tools::getValue('bt_incl-color'));
            }
            if (\Tools::getIsset('bt_color-opt')) {
                \Configuration::updateValue('FPA_COLOR_OPT', moduleTools::handleSetConfigurationData(\Tools::getValue('bt_color-opt')));
            }
            if (\Tools::getIsset('bt_incl-material')) {
                \Configuration::updateValue('FPA_INC_MATER', \Tools::getValue('bt_incl-material'));
            }
            if (\Tools::getIsset('bt_incl-pattern')) {
                \Configuration::updateValue('FPA_INC_PATT', \Tools::getValue('bt_incl-pattern'));
            }
            if (\Tools::getIsset('bt_incl-gender')) {
                \Configuration::updateValue('FPA_INC_GEND', \Tools::getValue('bt_incl-gender'));
            }
            if (\Tools::getIsset('bt_incl-age')) {
                \Configuration::updateValue('FPA_INC_AGE', \Tools::getValue('bt_incl-age'));
            }
            if (\Tools::getIsset('bt_manage-shipping')) {
                \Configuration::updateValue('FPA_SHIPPING_USE', \Tools::getValue('bt_manage-shipping'));
            }
            if (\Tools::getIsset('bt_gtin-pref')) {
                \Configuration::updateValue('FPA_GTIN_PREF', \Tools::getValue('bt_gtin-pref'));
            }

            \Configuration::updateValue('FPA_CONF_STEP_3', 1);

            // Handle the export out of stock
            if (\Tools::getIsset('bt_export-oos')) {
                \Configuration::updateValue('FPA_EXPORT_OOS', \Tools::getValue('bt_export-oos'));
            }

            // handle if we export or not products without EAN code
            if (\Tools::getIsset('bt_excl-no-ean')) {
                \Configuration::updateValue('FPA_EXC_NO_EAN', \Tools::getValue('bt_excl-no-ean'));
            }

            // handle if we export or not products without manufacturer code
            if (\Tools::getIsset('bt_excl-no-mref')) {
                \Configuration::updateValue('FPA_EXC_NO_MREF', \Tools::getValue('bt_excl-no-mref'));
            }

            // handle if we export products over a min price
            if (\Tools::getIsset('bt_min-price')) {
                \Configuration::updateValue('FPA_MIN_PRICE', !empty(\Tools::getValue('bt_min-price')) ? number_format((int)str_replace(',', '.', \Tools::getValue('bt_min-price')), 2) : 0.00);
            }

            /* USE CASE - update feed data options */
            if (\Tools::getIsset('bt_prod-combos')) {
                $bProductCombos = \Tools::getValue('bt_prod-combos');
                \Configuration::updateValue('FPA_P_COMBOS', $bProductCombos);

                // Use case for option only if we export each combination as a product
                if (!empty($bProductCombos)) {
                    // use case - options around the combination URLs for the export each combination as a single product
                    \Configuration::updateValue('FPA_URL_NUM_ATTR_REWRITE', \Tools::getValue('bt_rewrite-num-attr'));
                    \Configuration::updateValue('FPA_URL_ATTR_ID_INCL', \Tools::getValue('bt_incl-attr-id'));
                    \Configuration::updateValue('FPA_COMBO_SEPARATOR', \Tools::getValue('bt_combo-separator'));
                    \Configuration::updateValue('FPA_INCL_ANCHOR', \Tools::getValue('bt_include_anchor'));
                    \Configuration::updateValue('FPA_INCL_ATTR_VALUE', \Tools::getValue('bt_include_attribute_values'));
                }
            }

            if (\Tools::getIsset('bt_ship-carriers')) {
                $aShippingCarriers = [];
                $aPostShippingCarriers = \Tools::getValue('bt_ship-carriers');

                if (
                    !empty($aPostShippingCarriers) && is_array($aPostShippingCarriers)
                ) {
                    foreach ($aPostShippingCarriers as $iKey => $mVal) {
                        $aShippingCarriers[$iKey] = $mVal;
                    }
                    $sShippingCarriers = moduleTools::handleSetConfigurationData($aShippingCarriers);
                } else {
                    $sShippingCarriers = '';
                }
                \Configuration::updateValue('FPA_SHIP_CARRIERS', $sShippingCarriers);
            }

            if (\Tools::getIsset('bt_ship-carriers_free_product_price')) {
                $shippingFreeCarrier = [];
                $aPostShippingCarriers = \Tools::getValue('bt_ship-carriers_free_product_price');

                if (!empty($aPostShippingCarriers) && is_array($aPostShippingCarriers)) {
                    foreach ($aPostShippingCarriers as $iKey => $mVal) {
                        $shippingFreeCarrier[$iKey] = $mVal;
                    }
                    $sShippingCarriers = moduleTools::handleSetConfigurationData($shippingFreeCarrier);
                } else {
                    $sShippingCarriers = '';
                }
                \Configuration::updateValue('FPA_FREE_PROD_PRICE_SHIP_CARRIERS', $sShippingCarriers);
            }

            if (\Tools::getIsset('bt_ship-carriers_no_tax')) {
                $shippingNoTax = [];
                $carrierNoTax = \Tools::getValue('bt_ship-carriers_no_tax');

                if (!empty($carrierNoTax) && is_array($carrierNoTax)) {
                    foreach ($carrierNoTax as $iKey => $mVal) {
                        $shippingNoTax[$iKey] = $mVal;
                    }
                    $shippingNoTaxSaved = moduleTools::handleSetConfigurationData($shippingNoTax);
                } else {
                    $shippingNoTaxSaved = '';
                }

                \Configuration::updateValue('FPA_NO_TAX_SHIP_CARRIERS', $shippingNoTaxSaved);
            } else {
                \Configuration::updateValue('FPA_NO_TAX_SHIP_CARRIERS', '');
            }

            if (\Tools::getIsset('bt_ship-carriers_free')) {
                $freeCarrierData = [];
                $freeCarrier = \Tools::getValue('bt_ship-carriers_free');

                if (!empty($freeCarrier) && is_array($freeCarrier)) {
                    foreach ($freeCarrier as $iKey => $mVal) {
                        $freeCarrierData[$iKey] = $mVal;
                    }
                    $freeCarrierSaved = moduleTools::handleSetConfigurationData($freeCarrierData);
                } else {
                    $freeCarrierSaved = '';
                }
                \Configuration::updateValue('FPA_FREE_SHIP_CARRIERS', $freeCarrierSaved);
            } else {
                \Configuration::updateValue('FPA_FREE_SHIP_CARRIERS', '');
            }

            // update attributes and the feature for size tag
            if (\Tools::getIsset('hiddenProductIds')) {
                $sExcludedIds = \Tools::getValue('hiddenProductIds');
                $aExcludedIds = !empty($sExcludedIds) ? explode('-', $sExcludedIds) : [];

                if (!empty($aExcludedIds)) {
                    array_pop($aExcludedIds);
                }
                \Configuration::updateValue('FPA_PROD_EXCL', moduleTools::handleSetConfigurationData($aExcludedIds));
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration(['FPA_COLOR_OPT', 'FPA_SIZE_OPT', 'FPA_SHIP_CARRIERS', 'FPA_PROD_EXCL']);

        // In many case we need to generate all the XML according to some options updated like categories / brands or basics options
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => \FacebookProductAd::$aAvailableLangCurrencyCountry]);
        moduleUpdate::create()->run('configuration', 'feed');

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update feed list settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateFeedList(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            // update cron export
            \Configuration::updateValue('FPA_CHECK_EXPORT', moduleTools::handleSetConfigurationData(\Tools::getValue('bt_cron-export')));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration(['FPA_CHECK_EXPORT']);

        // In many case we need to generate all the XML according to some options updated like categories / brands or basics options
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => \FacebookProductAd::$aAvailableLangCurrencyCountry]);
        moduleUpdate::create()->run('configuration', 'feed');

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('feedList');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update advanced tag settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateTag(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];
        $categories = [];

        try {
            /* USE CASE - handle all tags configured */
            foreach (moduleConfiguration::FPA_TAG_LIST as $sTagType) {
                if (!empty($aPost[$sTagType]) && is_array($aPost[$sTagType])) {
                    foreach ($aPost[$sTagType] as $iCatId => $mVal) {
                        $categories[$iCatId][$sTagType] = strip_tags($mVal);
                    }
                }
            }

            // Clean the table for the new insert
            featureCategoryTag::cleanTable(\FacebookProductAd::$iShopId);

            if (!empty($categories)) {
                foreach ($categories as $id_category => $value) {
                    $feature_category = new featureCategoryTag();
                    $feature_category->id_cat = (int) $id_category;
                    $feature_category->values = moduleTools::handleSetConfigurationData($value);
                    $feature_category->id_shop = (int) \FacebookProductAd::$iShopId;
                    $feature_category->add();
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/advanced-tag-update.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method update custom label settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateLabel(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            $sLabelName = \Tools::getValue('bt_label-name');
            $iTagId = \Tools::getValue('bt_tag-id');
            $sLabelType = \Tools::getValue('bt_cl-type');
            $bActivateTag = \Tools::getValue('bt_cl-statut');
            $sDateEnd = \Tools::getValue('bt_cl_date_end');
            $sDateNewProduct = \Tools::getValue('bt_cl_dyn_date_start');
            $sProductSpecific = \Tools::getValue('hiddenProductIds-cl');
            $aProductSpecific = !empty($sProductSpecific) ? explode('-', $sProductSpecific) : [];
            $sBestSaleType = \Tools::getValue('dynamic_best_sales_unit');
            $fBestSaleAmount = \Tools::getValue('bt_cl_dyn_amount');
            $sBestSaleStartDate = \Tools::getValue('bt_dyn_best_sale_start');
            $sBestSalesEndDate = \Tools::getValue('bt_dyn_best_sale_end');
            $fPriceMin = \Tools::getValue('bt_dyn_min_price');
            $fPriceMax = \Tools::getValue('bt_dyn_max_price');
            $sLastOrderedStart = \Tools::getValue('bt_dyn_last_order_start');
            $sLastOrderedEnd = \Tools::getValue('bt_dyn_last_order_end');
            $iLastId = (int) customLabelTags::getLastId();
            $iNextId = $iLastId + 1;
            $customSetPosition = \Tools::getValue('bt_cl_association');

            if (empty($sLabelName)) {
                throw new \Exception(\FacebookProductAd::$oModule->l('You haven\'t filled out the label name', 'adminUpdate') . '.', 560);
            } else {
                // USE CASE - The tag is already saved
                if (!empty($iTagId)) {
                    // get the postion save for the tag
                    $iPositionTag = customLabelTags::getTagPosition($iTagId);
                    customLabelTags::updateTag($iTagId, $sLabelName, $sLabelType, $bActivateTag, $customSetPosition, $iPositionTag, $sDateEnd);

                    // Clean the tag
                    labelTools::cleanTag($iTagId, $sLabelType);
                } // use case - create tag
                else {
                    $iTagId = customLabelTags::addTag(\FacebookProductAd::$iShopId, $sLabelName, $sLabelType, $customSetPosition, $bActivateTag, $iNextId, $sDateEnd);
                }

                if ($sLabelType == 'custom_label' || $sLabelType == 'dynamic_new_product') {
                    labelTools::handleDefautTag($iTagId, $sLabelType, $aProductSpecific);
                }

                if ($sLabelType == 'dynamic_features_list') {
                    labelTools::handleFeatureTag($iTagId, (int) \Tools::getValue('dynamic_features_list'));
                }

                if ($sLabelType == 'dynamic_categorie') {
                    labelTools::handleCatDynamicTag($iTagId, \Tools::getValue('bt_category-box'));
                }

                // USE CASE - Dynamic new product
                if ($sLabelType == 'dynamic_new_product') {
                    labelTools::handleDynamicNewProduct($iTagId, $sDateNewProduct);
                }

                // USE CASE - Dynamic best sales
                if ($sLabelType == 'dynamic_best_sale') {
                    labelTools::handleDynamicBestSales($iTagId, $sBestSaleType, $fBestSaleAmount, $sBestSaleStartDate, $sBestSalesEndDate);
                }

                // Use case dynamic price range
                if ($sLabelType == 'dynamic_price_range') {
                    labelTools::handleDynamicPriceRange($iTagId, $fPriceMin, $fPriceMax);
                }

                // Use case handle last ordered products
                if ($sLabelType == 'dynamic_last_order') {
                    labelTools::handleDynamicLastOrdered($iTagId, $sLastOrderedStart, $sLastOrderedEnd);
                }

                // Use case handle product in promotion
                if ($sLabelType == 'dynamic_promotion') {
                    labelTools::handleDynamicPromotion($iTagId, $sLastOrderedStart, $sLastOrderedEnd);
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/custom-label-update.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * update custom label activation from list
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateLabelState(array $aPost)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            @ob_end_clean();
        }

        // set
        $aData = [];
        $sDeleteType = \Tools::getValue('sDeleteType');
        $iTagId = \Tools::getValue('iTagId');
        $aTagIds = \Tools::getValue('iTagIds');

        try {
            if (in_array($sDeleteType, ['one', 'bulk'])) {
                if ($sDeleteType == 'one' && !empty($iTagId)) {
                    customLabelTags::updateTagStatus($iTagId, (int) \Tools::getValue('bActive'));
                } elseif ($sDeleteType == 'bulk' && !empty($aTagIds)) {
                    $aIdsDelete = explode(',', $aTagIds);
                    foreach ($aIdsDelete as $aCurrentClId) {
                        customLabelTags::updateTagStatus((int)$aCurrentClId, (int) \Tools::getValue('bActive'));
                    }
                }
            } else {
                throw new \Exception(\FacebookProductAd::$oModule->l('Your custom label ID is not valid or some parameters are wrong', 'adminUpdate') . '.', 900);
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('facebook');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     * update custom label activation from list
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updatePosition(array $aPost)
    {
        \FacebookProductAd::$sQueryMode = 'xhr';
        $iTagIdMoveToNewPos = \Tools::getValue('iTagIdMoveToNewPos');
        $iNewPosition = \Tools::getValue('iNewPosition');
        $iTagIdMoveToOldPos = \Tools::getValue('iTagIdMoveToOldPos');
        $iOldPosition = \Tools::getValue('iOldPosition');
        $aData = [];

        customLabelTags::updatePositionTag($iTagIdMoveToNewPos, $iNewPosition, (int)\FacebookProductAd::$iShopId);
        customLabelTags::updatePositionTag($iTagIdMoveToOldPos, $iOldPosition, (int)\FacebookProductAd::$iShopId);

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('facebook');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     * update custom label date when check or data feed is generated
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateCustomLabelDate(array $aPost = null)
    {
        @ob_end_clean();
        $aData = [];
        $sDateToday = date('Y-m-d');

        // get all tag information id and date
        $aTags = customLabelTags::getTagDate(\FacebookProductAd::$iShopId);

        // make the process for each tag with date
        foreach ($aTags as $aTag) {
            $iDateCompare = moduleTools::dateCompare($sDateToday, (string) $aTag['end_date']);
            $iPositionTag = customLabelTags::getTagPosition((int) $aTag['id_tag']);
            // made update tag statut if date is over
            if ($iDateCompare == 1) {
                // update tag statut
                if (!empty($iPositionTag)) {
                    customLabelTags::updateProcessDate((int) $aTag['id_tag'], 0, $iPositionTag['position']);
                }
            }
        }

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('facebook');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     * update custom label product association durong the data feed update
     *
     * @param array $aPost
     *
     * @return mixed
     */
    private function updateCustomCheck(array $aPost = null)
    {
        labelTools::updateCustomLabelFeedProcess();
    }

    /**
     *  method update facebook settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateFacebook(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        \Configuration::updateValue('FPA_UTM_CAMPAIGN', \Tools::getValue('bt_utm-campaign'));
        \Configuration::updateValue('FPA_UTM_SOURCE', \Tools::getValue('bt_utm-source'));
        \Configuration::updateValue('FPA_UTM_MEDIUM', \Tools::getValue('bt_utm-medium'));

        // get configuration options
        moduleTools::getConfiguration(['FPA_COLOR_OPT', 'FPA_SIZE_OPT', 'FPA_SHIP_CARRIERS']);

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('facebook');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update pixel config settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updatePixel(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            \Configuration::updateValue('FPA_PIXEL', \Tools::getValue('bt_pixel'));
            \Configuration::updateValue('FPA_BUSINESS_ID', \Tools::getValue('bt_business_id'));
            \Configuration::updateValue('FPA_ACTIVATE_PIXEL', \Tools::getValue('bt_activate_pixel'));
            \Configuration::updateValue('FPA_USE_API', \Tools::getValue('bt_api_conversion'));
            \Configuration::updateValue('FPA_ADVANCED_MATCHING', \Tools::getValue('bt_advanced_matching'));
            \Configuration::updateValue('FPA_HAS_WARNING', \Tools::getValue('bt_api_warning'));
            \Configuration::updateValue('FPA_TOKEN_API', \Tools::getValue('bt_api_token'));
            \Configuration::updateValue('FPA_API_PAGE_VIEW', \Tools::getValue('bt_api_pageview'));
            \Configuration::updateValue('FPA_USE_TAX', \Tools::getValue('bt_use-tax'));
            \Configuration::updateValue('FPA_USE_TEST_EVENT_CODE', \Tools::getValue('bt_use_test_event_code'));
            \Configuration::updateValue('FPA_TEST_EVENT_CODE', \Tools::getValue('bt_test_event_code'));
            \Configuration::updateValue('FPA_USE_SHIPPING', \Tools::getValue('bt_use-shipping'));
            \Configuration::updateValue('FPA_USE_WRAPPING', \Tools::getValue('bt_use-wrapping'));
            \Configuration::updateValue('FPA_TRACK_HOME', \Tools::getValue('bt_track_home'));
            \Configuration::updateValue('FPA_JS_CART_SELECTOR_PROD', \Tools::getValue('bt_code_addtocart_product'));
            \Configuration::updateValue('FPA_TRACK_ADD_CART_PAGE', \Tools::getValue('bt_track_cart_page'));
            \Configuration::updateValue('FPA_JS_WISH_SELECTOR_PROD', \Tools::getValue('bt_code_addtowishlist_product'));
            \Configuration::updateValue('FPA_JS_CART_SELECTOR_CAT', \Tools::getValue('bt_code_addtocart_category'));
            \Configuration::updateValue('FPA_JS_WISH_SELECTOR_CAT', \Tools::getValue('bt_code_addtowishlist_list'));

            if (!empty(\Tools::getValue('bt_pixel'))) {
                \Configuration::updateValue('FPA_CONF_STEP_1', 1);
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('pixel');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update new feed config settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateNewFeed(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        $lang_code = \Tools::getValue('bt-new-feed-lang');
        $country_code = \Tools::getValue('bt-new-feed-country');
        $currency = \Tools::getValue('bt-new-feed-currency');
        $taxonomy = \Tools::getValue('bt-new-feed-taxonomy');

        // Check if the data feed combination exist
        $feedExist = Feeds::feedExist($lang_code, $country_code, $currency, $taxonomy, (int) \FacebookProductAd::$iShopId);

        // Use case error message if the data feed already exist or add the data on the data base
        if (!empty($feedExist)) {
            $aData['aErrors'] = true;
        } else {
            // Make the insert
            $feed = new Feeds();
            $feed->iso_lang = $lang_code;
            $feed->iso_country = $country_code;
            $feed->iso_currency = $currency;
            $feed->taxonomy = $taxonomy;
            $feed->id_shop = \FacebookProductAd::$iShopId;
            $feed->feed_is_default = 0;
            $feed->add();
        }

        try {
            $aAssign = [
                'saved' => true,
                'feed_exist' => !empty($feedExist) ? true : false,
            ];
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('newCustomFeed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update reporting settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateReporting(array $aPost)
    {
        @ob_end_clean();

        // set
        $aData = [];

        try {
            \Configuration::updateValue('FPA_REPORTING', \Tools::getValue('bt_reporting'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('reporting');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update the facebook categories by sync action
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateFacebookCategoriesSync(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            $sLangIso = \Tools::getValue('sLangIso');

            if ($sLangIso != false) {
                // Get and check content is here
                $sContent = moduleTools::getFacebookFile('http://www.google.com/basepages/producttype/taxonomy.' . basename($sLangIso) . '.txt');
                // use case - the Facebook file content is KO
                if (!$sContent || \Tools::strlen($sContent) == 0) {
                    throw new \Exception(\FacebookProductAd::$oModule->l('An error occurred during the Facebook file get content', 'adminUpdate') . '.', 591);
                } else {
                    // Convert to array and check all is still OK
                    $aLines = explode("\n", trim($sContent));

                    // use case - wrong format
                    if (!$aLines || !is_array($aLines)) {
                        throw new \Exception(\FacebookProductAd::$oModule->l('The Facebook taxonomy file content is not formatted well', 'adminUpdate') . '.', 592);
                    } else {
                        // Delete past data
                        googleTaxonomy::clean($sLangIso);

                        // Re-insert
                        foreach ($aLines as $index => $sLine) {
                            // First line is the version number, so skip it
                            if ($index > 0) {
                                googleTaxonomy::addTaxonomy($sLine, $sLangIso);
                            }
                        }
                    }
                }

                $aAssign['aCountryTaxonomies'] = moduleTools::getAvailableTaxonomyCountries();

                foreach ($aAssign['aCountryTaxonomies'] as $sIsoCode => &$aTaxonomy) {
                    $aTaxonomy['countryList'] = implode(', ', $aTaxonomy['countries']);
                    $aTaxonomy['currentUpdated'] = $sLangIso == $sIsoCode ? true : false;
                    $aTaxonomy['updated'] = googleTaxonomy::checkTaxonomyUpdate($sIsoCode);
                }
            } else {
                throw new \Exception(\FacebookProductAd::$oModule->l('The server has returned an unsecure request error (wrong parameters)!', 'adminUpdate') . '.', 593);
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sURI'] = moduleTools::truncateUri('&sAction');
        $aAssign['sCtrlParamName'] = moduleConfiguration::FPA_PARAM_CTRL_NAME;
        $aAssign['sController'] = moduleConfiguration::FPA_ADMIN_CTRL;
        $aAssign['aQueryParams'] = moduleConfiguration::getRequestParams();
        $aAssign['iCurrentLang'] = intval(\FacebookProductAd::$iCurrentLang);
        $aAssign['sCurrentLang'] = \FacebookProductAd::$sCurrentLang;
        $aAssign['taxonomyController'] = \Context::getContext()->link->getAdminLink('AdminTaxonomy');

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/category-list.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method update the XML file
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateXml(array $aPost)
    {
        @ob_end_clean();

        // set
        $aAssign = [];

        try {
            $iShopId = \Tools::getValue('iShopId');
            $sFilename = \Tools::getValue('sFilename');
            $iLangId = \Tools::getValue('iLangId');
            $sLangIso = \Tools::getValue('sLangIso');
            $sCountryIso = \Tools::getValue('sCountryIso');
            $sCurrencyIso = \Tools::getValue('sCurrencyIso');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');

            if (($iShopId != false && is_numeric($iShopId))
                && ($sFilename != false && is_string($sFilename))
                && ($iLangId != false && is_numeric($iLangId))
                && ($sLangIso != false && is_string($sLangIso))
                && ($sCountryIso != false && is_string($sCountryIso))
                && ($sCurrencyIso != false && is_string($sCurrencyIso))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
            ) {
                $_POST['iShopId'] = $iShopId;
                $_POST['sFilename'] = $sFilename;
                $_POST['iLangId'] = $iLangId;
                $_POST['sLangIso'] = $sLangIso;
                $_POST['sCountryIso'] = \Tools::strtoupper($sCountryIso);
                $_POST['sCurrencyIso'] = \Tools::strtoupper($sCurrencyIso);
                $_POST['iFloor'] = $iFloor;
                $_POST['iStep'] = \FacebookProductAd::$conf['FPA_AJAX_CYCLE'];
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;

                // exec the generate class to generate the XML files
                $aGenerate = adminGenerate::create()->run('xml', ['reporting' => \FacebookProductAd::$conf['FPA_REPORTING']]);

                if (empty($aGenerate['assign']['aErrors'])) {
                    $aAssign['status'] = 'ok';
                    $aAssign['counter'] = $iFloor + $_POST['iStep'];
                    $aAssign['process'] = $aGenerate['assign']['process'];
                } else {
                    $aAssign['status'] = 'ko';
                    $aAssign['error'] = $aGenerate['assign']['aErrors'];
                }
            } else {
                $sMsg = \FacebookProductAd::$oModule->l(
                    'The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!',
                    'adminUpdate'
                ) . '.<br/>';
                $sMsg .= \FacebookProductAd::$oModule->l('Shop ID', 'adminUpdate') . ': ' . $iShopId . '<br/>'
                    . \FacebookProductAd::$oModule->l('File name', 'adminUpdate') . ': ' . $sFilename . '<br/>'
                    . \FacebookProductAd::$oModule->l('\Language ID', 'adminUpdate') . ': ' . $iLangId . '<br/>'
                    . \FacebookProductAd::$oModule->l('\Language ISO', 'adminUpdate') . ': ' . $sLangIso . '<br/>'
                    . \FacebookProductAd::$oModule->l('country ISO', 'adminUpdate') . ': ' . $sCountryIso . '<br/>'
                    . \FacebookProductAd::$oModule->l('Currency ISO', 'adminUpdate') . ': ' . $sCurrencyIso . '<br/>'
                    . \FacebookProductAd::$oModule->l('Step', 'adminUpdate') . ': ' . $iFloor . '<br/>'
                    . \FacebookProductAd::$oModule->l('Total products to process', 'adminUpdate') . ': ' . $iTotal . '<br/>'
                    . \FacebookProductAd::$oModule->l('Total products to process (without counting combinations)', 'adminUpdate') . ': ' . $iTotal . '<br/>'
                    . \FacebookProductAd::$oModule->l('Stock the real number of products to process', 'adminUpdate') . ': ' . $iProcess . '<br/>';

                throw new \Exception($sMsg, 594);
            }
        } catch (\Exception $e) {
            $aAssign['status'] = 'ko';
            $aAssign['error'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/feed-generate.tpl',
            'assign' => ['json' => \json_encode($aAssign)],
        ];
    }

    /**
     * updateLang() method check and update lang of multi-language fields
     *
     * @param array $aPost : params
     * @param string $sFieldName : field name linked to the translation value
     * @param string $sGlobalName : name of GLOBAL variable to get value
     * @param bool $bCheckOnly
     * @param string $sErrorDisplayName
     *
     * @return array
     */
    private function updateLang(array $aPost, $sFieldName, $sGlobalName, $bCheckOnly = false, $sErrorDisplayName = '')
    {
        // check title in each active language
        $aLangs = [];
        $aLanguages = \Language::getLanguages();

        foreach ($aLanguages as $nKey => $aLang) {
            if (empty($aPost[$sFieldName . '_' . $aLang['id_lang']])) {
                $aLangs[$aLang['id_lang']] = strip_tags($aPost[$sFieldName . '_' . $aLanguages[0]['id_lang']]);
            } else {
                $aLangs[$aLang['id_lang']] = strip_tags($aPost[$sFieldName . '_' . $aLang['id_lang']]);
            }
        }
        if (!$bCheckOnly) {
            \Configuration::updateValue($sGlobalName, moduleTools::handleSetConfigurationData($aLangs));
        }

        return $aLangs;
    }

    /**
     * method update the exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     *
     */
    private function updateExclusionRule(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            $bActive = \Tools::getValue('bt_excl-rule-active');
            $sExclusionName = \Tools::getValue('bt-exclusion-name');
            $sExclusionType = \Tools::getValue('bt-exclusion-type');
            $iExclusionId = \Tools::getValue('bt-exclusion-id');
            $sExclusionWordType = \Tools::getValue('bt-exclusion-word-type');
            $sExclusionWord = \Tools::getValue('word-exclusion-value');
            $sExclusionFeature = \Tools::getValue('bt-exclusion-feature');
            $sExclusionFeatureValue = \Tools::getValue('bt-feature-value');
            $sExclusionAttribute = \Tools::getValue('bt-exclusion-attribute');
            $sExclusionAttributeValue = \Tools::getValue('bt-attribute-value');
            $sExclusionSupplier = \Tools::getValue('bt_supplier-box');
            $sProductSpecificExclusion = \Tools::getValue('hiddenProductIds');

            // Use case to build the exlusion rule when it is a word type
            if ($sExclusionType == 'word') {
                if (!empty($sExclusionWordType) && !empty($sExclusionWord)) {
                    $aRulevalue = [
                        'exclusionOn' => $sExclusionWordType,
                        'exclusionData' => $sExclusionWord,
                    ];
                }
            } elseif ($sExclusionType == 'feature') {
                $aRulevalue = [
                    'exclusionOn' => $sExclusionFeature,
                    'exclusionData' => $sExclusionFeatureValue,
                ];
            } elseif ($sExclusionType == 'attribute') {
                $aRulevalue = [
                    'exclusionOn' => $sExclusionAttribute,
                    'exclusionData' => $sExclusionAttributeValue,
                ];
            } elseif ($sExclusionType == 'specificProduct') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sProductSpecificExclusion,
                ];
            } elseif ($sExclusionType == 'supplier') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sExclusionSupplier,
                ];
            }

            // Use case to manage the product ids according to the rules values
            $aRulevalue['aProductIds'] = exclusionTools::getProductFromRules();
            $aRuleDetails = tmpRules::getTmpRules();

            // Stock the rules preferences
            foreach ($aRuleDetails as $aRuleDetail) {
                $aRulevalue['aRulesDetail'][] = $aRuleDetail['exclusion_values'];
            }
            $sExclusionValue = moduleTools::handleSetConfigurationData($aRulevalue);

            // Use case insert
            if (empty($iExclusionId)) {
                if (advancedExclusion::addRule($bActive, \FacebookProductAd::$iShopId, $sExclusionName, $sExclusionType, $sExclusionValue)) {
                    $aLastRule = advancedExclusion::getLastRuleId();
                    foreach ($aRulevalue['aProductIds'] as $aProductData) {
                        // Use case export each combination as a product
                        if (!empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                            exclusionProduct::addRule($aLastRule['last_id'], $aProductData['id_product'], $aProductData['id_product_attribute']);
                        } else {
                            exclusionProduct::addRule($aLastRule['last_id'], $aProductData);
                        }
                    }
                }
            } else { // use case update
                if (advancedExclusion::updateRule($bActive, \FacebookProductAd::$iShopId, $sExclusionName, $sExclusionType, $sExclusionValue, $iExclusionId)) {
                    if (empty($bActive)) {
                        exclusionProduct::deleteRule($iExclusionId);
                        foreach ($aRulevalue['aProductIds'] as $aProductData) {
                            if (!empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                exclusionProduct::addRule($iExclusionId, $aProductData['id_product'], $aProductData['id_product_attribute']);
                            } else {
                                exclusionProduct::addRule($iExclusionId, $aProductData);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/advanced-tag-update.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method update the rule list
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateRulesList(array $aPost)
    {
        @ob_end_clean();

        // set
        $aData = [];

        try {
            $iRuleId = \Tools::getValue('iRuleId');
            $sType = \Tools::getValue('sUpdateType');
            $bActivate = \Tools::getValue('bActivate');

            if (empty($iRuleId) || empty($sType)) {
                throw new \Exception(\FacebookProductAd::$oModule->l('Your rule id isn\'t valid or update of type is not valid or some activated parameters are forgotten', 'adminUpdate') . '.', 700);
            } else {
                // include
                if (advancedExclusion::updateRuleStatus($iRuleId, $sType, $bActivate)) {
                    if (!empty($bActivate)) {
                        $aProducts = exclusionTools::getProductFromRules($iRuleId, true);
                        foreach ($aProducts as $aProductData) {
                            exclusionProduct::addRule($iRuleId, $aProductData['id_product'], $aProductData['id_product_attribute']);
                        }
                    } else {
                        if (!exclusionProduct::deleteRule($iRuleId)) {
                            throw new \Exception(\FacebookProductAd::$oModule->l('Error while deleting product exclusions', 'adminUpdate') . '.', 700);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update consent settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateConsent(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            \Configuration::updateValue('FPA_USE_CONSENT', \Tools::getValue('bt_activate_consent'));
            \Configuration::updateValue('FPA_ELEMENT_HTML_ID', \Tools::getValue('bt_accept_element-id'));
            \Configuration::updateValue('FPA_ELEMENT_HTML_SECOND_ID', \Tools::getValue('bt_accept_element-id_second'));
            \Configuration::updateValue('FPA_USE_AXEPTIO', \Tools::getValue('bt_activate_axeptio'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // In many case we need to generate all the XML according to some options updated like categories / brands or basics options
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => \FacebookProductAd::$aAvailableLangCurrencyCountry]);
        moduleUpdate::create()->run('configuration', 'feed');

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('consent');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * method update advice settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateLog(array $aPost)
    {
        @ob_end_clean();

        $eventPageType = \Tools::getValue('bt_event_type');
        $dateStart = \Tools::getValue('bt_event_date_start');

        // set
        $aData = [
            'dataLog' => apiLog::getApiLogErrorMessage($eventPageType, $dateStart),
        ];

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // get run of admin display in order to display first page of admin with feed management settings updated
        $aDisplay = adminDisplay::create()->run('log', $aData);

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        // destruct
        unset($aData);

        return $aDisplay;
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
            $oUpdate = new adminUpdate();
        }

        return $oUpdate;
    }
}
