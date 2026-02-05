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
use FacebookProductAd\Dao\customLabelDao;
use FacebookProductAd\Models\customLabelDynamicBestSales;
use FacebookProductAd\Models\customLabelDynamicCategories;
use FacebookProductAd\Models\customLabelDynamicFeature;
use FacebookProductAd\Models\customLabelDynamicLastProductOrder;
use FacebookProductAd\Models\customLabelDynamicNewProduct;
use FacebookProductAd\Models\customLabelDynamicPriceRange;
use FacebookProductAd\Models\customLabelDynamicProducts;
use FacebookProductAd\Models\customLabelDynamicPromotion;
use FacebookProductAd\Models\customLabelTags;

class labelTools
{
    /**
     * Handle the check and insert for basics and dynamic product custom label
     *
     * @param int $iTagId
     * @param string $sLabelType
     * @param array $aSpecificProducts
     */
    public static function handleDefautTag($iTagId, $sLabelType, $aSpecificProducts = [])
    {
        foreach (moduleConfiguration::FPA_LABEL_LIST as $sTableName => $sFieldType) {
            if (\Tools::getIsset('bt_' . $sFieldType . '-box')) {
                $aSelectedIds = \Tools::getValue('bt_' . $sFieldType . '-box');
                foreach ($aSelectedIds as $iSelectedId) {
                    customLabelTags::inserCatTag($iTagId, $iSelectedId, $sTableName, $sFieldType);
                }
            }
        }
        if (!empty($aSpecificProducts)) {
            foreach ($aSpecificProducts as $key => $aProduct) {
                $oProduct = new \Product((int) $aProduct, true, \FacebookProductAd::$iCurrentLang);

                if (\Validate::isLoadedObject($oProduct)) {
                    $sProductName = $oProduct->name;
                    customLabelDynamicProducts::insertProductTag($iTagId, (int) $aProduct, $sProductName);
                }
            }
        }
    }

    /**
     * Handle the check and insert custom label based on feature
     *
     * @param int $iTagId
     * @param int $iFeatureId
     */
    public static function handleFeatureTag($iTagId, $iFeatureId)
    {
        customLabelDynamicFeature::addTag($iTagId, $iFeatureId);
    }

    /**
     * Handle the check and insert custom label based on dynmamic cat
     *
     * @param int $iTagId
     * @param array $aCategories
     */
    public static function handleCatDynamicTag($iTagId, $aCategories)
    {
        foreach ($aCategories as $iSelectedId) {
            customLabelDynamicCategories::insertDynamicCat($iTagId, $iSelectedId);
        }
    }

    /**
     * Handle the check and insert of new product for custom label dynamic
     *
     * @param int $iTagId
     * @param string $sNewProductDate
     */
    public static function handleDynamicNewProduct($iTagId, $sNewProductDate)
    {
        $aProductIds = customLabelDao::getNewProducts($sNewProductDate);

        if (!empty($aProductIds)) {
            foreach ($aProductIds as $aProduct) {
                customLabelDynamicNewProduct::insertDynamicNew($iTagId, $sNewProductDate, $aProduct['id_product']);
            }
        } else {
            customLabelDynamicNewProduct::insertDynamicNew($iTagId, $sNewProductDate, 0);
        }
    }

    /**
     * Handle the check and insert best sales for the custom label
     *
     * @param int $iTagId
     * @param string $sBestSaleType
     * @param float $fBestSaleAmount
     * @param string $sBestSaleStartDate
     * @param string $sBestSalesEndDate
     */
    public static function handleDynamicBestSales($iTagId, $sBestSaleType, $fBestSaleAmount, $sBestSaleStartDate, $sBestSalesEndDate)
    {
        // getProductIds for selected parameters in best sales form
        $aProductIds = customLabelDao::getProductBestSales($sBestSaleType, (float)$fBestSaleAmount, $sBestSaleStartDate, $sBestSalesEndDate);
        if (!empty($aProductIds)) {
            foreach ($aProductIds as $aProduct) {
                if (!empty($aProduct['product_id'])) {
                    customLabelDynamicBestSales::insertDynamicBestSales($iTagId, (float)$fBestSaleAmount, $sBestSaleType, $sBestSaleStartDate, $sBestSalesEndDate, $aProduct['product_id']);
                } elseif (!empty($aProduct['id_product'])) {
                    customLabelDynamicBestSales::insertDynamicBestSales($iTagId, (float)$fBestSaleAmount, $sBestSaleType, $sBestSaleStartDate, $sBestSalesEndDate, $aProduct['id_product']);
                }
            }
        } else {
            $aAssign['aErrors'][] = ['msg' => '', 'code' => ''];
        }
    }

    /**
     * Handle the check and insert best sales for the custom label
     *
     * @param int $iTagId
     * @param string $fPriceMin
     * @param string $fPriceMax
     */
    public static function handleDynamicPriceRange($iTagId, $fPriceMin, $fPriceMax)
    {
        // Get product according to the re
        $aProductIds = customLabelDao::getPriceRangeProduct($fPriceMin, $fPriceMax);

        if (!empty($aProductIds)) {
            foreach ($aProductIds as $aProduct) {
                customLabelDynamicPriceRange::insertDynamicPriceRange($iTagId, (int)$fPriceMin, (int)$fPriceMax, $aProduct['id_product']);
            }
        } else {
            $aAssign['aErrors'][] = ['msg' => '', 'code' => ''];
        }
    }

    /**
     * Handle the check and insert last ordered product label
     *
     * @param int $iTagId
     * @param string $sLastOrderedStart
     * @param string $sLastOrderedEnd
     */
    public static function handleDynamicLastOrdered($iTagId, $sLastOrderedStart, $sLastOrderedEnd)
    {
        $aOrders = \Order::getOrdersIdByDate($sLastOrderedStart, $sLastOrderedEnd);
        // Loop on orders for the available period
        foreach ($aOrders as $iOrderId) {
            $oOrder = new \Order((int) $iOrderId);
            $aOrderDetails = $oOrder->getProducts(false, false, false, false);

            foreach ($aOrderDetails as $aDetails) {
                $aProductIds[] = $aDetails['product_id'];
            }
        }

        if (!empty($aProductIds)) {
            // Removed duplicate values
            $aProductIds = array_unique($aProductIds);

            foreach ($aProductIds as $iProductId) {
                customLabelDynamicLastProductOrder::insertDynamicLastProductOrdered($iTagId, $sLastOrderedStart, $sLastOrderedEnd, $iProductId);
            }
        } else {
            $aAssign['aErrors'][] = ['msg' => '', 'code' => ''];
        }
    }

    /**
     * Handle the check and insert of promotion for the custom label
     *
     * @param int $iTagId
     * @param string $sLastOrderedStart
     * @param string $sLastOrderedEnd
     */
    public static function handleDynamicPromotion($iTagId, $sLastOrderedStart, $sLastOrderedEnd)
    {
        // Get products in promotions
        $aProducts = \Product::getPricesDrop(\FacebookProductAd::$sCurrentLang, 0, 100000, false, null, null);
        $aProductIds = [];

        foreach ($aProducts as $aDetail) {
            $aProductIds[] = $aDetail['id_product'];
        }

        // Removed duplicate values
        $aProductIds = array_unique($aProductIds);

        if (!empty($aProductIds)) {
            foreach ($aProductIds as $iProductId) {
                // customLabelDynamicPromotion::insertDynamicPromotion($iTagId, $sLastOrderedStart, $sLastOrderedEnd, $iProductId);
            }
        } else {
            $aAssign['aErrors'][] = ['msg' => '', 'code' => ''];
        }
    }

    /**
     * Clean tag on table before insert again the value
     *
     * @param int $iTagId
     * @param string $sLabelType
     */
    public static function cleanTag($iTagId, $sLabelType)
    {
        if ($sLabelType == 'custom_label') {
            foreach (moduleConfiguration::FPA_LABEL_LIST as $sTableName => $sFieldType) {
                // delete related tables
                customLabelTags::deleteCatTag($iTagId, $sTableName);
            }
            customLabelDynamicProducts::deleteProductTag($iTagId);
        }

        if ($sLabelType == 'dynamic_features_list') {
            customLabelDynamicFeature::deleteFeatureSave($iTagId);
        }

        if ($sLabelType == 'dynamic_categorie') {
            customLabelDynamicCategories::deleteDynamicCat($iTagId);
        }

        if ($sLabelType == 'dynamic_new_product') {
            customLabelDynamicNewProduct::deleteDynamicNew($iTagId);
        }

        if ($sLabelType == 'dynamic_best_sale') {
            customLabelDynamicBestSales::deleteDynamicBestSales($iTagId);
        }

        if ($sLabelType == 'dynamic_price_range') {
            customLabelDynamicPriceRange::deleteDynamicPriceRange($iTagId);
        }

        if ($sLabelType == 'dynamic_last_order') {
            customLabelDynamicLastProductOrder::deleteDynamicLastProductOrdered($iTagId);
        }

        if ($sLabelType == 'dynamic_promotion') {
            customLabelDynamicPromotion::deleteDynamicPromotion($iTagId);
        }
    }

    /**
     * Check and assign again custom label to product during data feed process.
     *
     * @return mixed
     */
    public static function updateCustomLabelFeedProcess()
    {
        // Get active tag ready for data feed process
        $aActiveTags = customLabelTags::getActive(\FacebookProductAd::$iShopId);
        $sDateFrom = '0000-00-00 00:00:00';
        $sDateTo = '0000-00-00 00:00:00';

        if (!empty($aActiveTags)) {
            foreach ($aActiveTags as $aTag) {
                // Use case to reforce assign if a prodcut is added in category after custom label creation
                if ($aTag['type'] == 'dynamic_categorie') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicCategories::getDynamicCat((int) $aTag['id_tag']);
                        self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                        self::handleCatDynamicTag((int) $aTag['id_tag'], (array) $aTagDataSaved);
                    }
                }

                if ($aTag['type'] == 'dynamic_features_list') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicFeature::getFeatureSave((int) $aTag['id_tag']);
                        self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                        self::handleFeatureTag((int) $aTag['id_tag'], (int) $aTagDataSaved['id_feature']);
                    }
                }

                if ($aTag['type'] == 'dynamic_new_product') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicNewProduct::getDynamicNew((int) $aTag['id_tag']);
                        // Use case to edit an existing tag
                        if (!empty($aTagDataSaved)) {
                            $sDateFrom = $aTagDataSaved['from_date'] == '0000-00-00 00:00:00' ? '' : $aTagDataSaved['from_date'];

                            self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                            self::handleDynamicNewProduct((int) $aTag['id_tag'], (string) $sDateFrom);
                        }
                    }
                }

                if ($aTag['type'] == 'dynamic_best_sale') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicBestSales::getDynamicBestSales((int) $aTag['id_tag']);
                        if (!empty($aTagDataSaved)) {
                            $sDateFrom = $aTagDataSaved['start_date'];
                            $sDateTo = $aTagDataSaved['end_date'];
                            self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                            self::handleDynamicBestSales((int) $aTag['id_tag'], $aTagDataSaved['unit'], (float) $aTagDataSaved['amount'], (string) $sDateFrom, (string) $sDateTo);
                        }
                    }
                }

                if ($aTag['type'] == 'dynamic_price_range') {
                    if (!empty($aTag['id_tag'])) {
                        if (!empty($aTagDataSaved)) {
                            $aTagDataSaved = customLabelDynamicPriceRange::getDynamicPriceRange((int) $aTag['id_tag']);
                            self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                            self::handleDynamicPriceRange((int) $aTag['id_tag'], (string) $aTagDataSaved['price_min'], (string) $aTagDataSaved['price_max']);
                        }
                    }
                }

                if ($aTag['type'] == 'dynamic_last_order') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicLastProductOrder::getDynamicLastProductOrdered((int) $aTag['id_tag']);
                        if (!empty($aTagDataSaved)) {
                            $sDateFrom = $aTagDataSaved['start_date'] == '0000-00-00 00:00:00' ? '' : $aTagDataSaved['start_date'];
                            $sDateTo = $aTagDataSaved['end_date'] == '0000-00-00 00:00:00' ? '' : $aTagDataSaved['end_date'];
                            self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                            self::handleDynamicLastOrdered((int) $aTag['id_tag'], (string) $sDateFrom, (string) $sDateTo);
                        }
                    }
                }

                if ($aTag['type'] == 'dynamic_promotion') {
                    if (!empty($aTag['id_tag'])) {
                        $aTagDataSaved = customLabelDynamicPromotion::getDynamicLastDynamicPromotion((int) $aTag['id_tag']);
                        if (!empty($aTagDataSaved)) {
                            $sDateFrom = $aTagDataSaved['start_date'] == '0000-00-00 00:00:00' ? '' : $aTagDataSaved['start_date'];
                            $sDateTo = $aTagDataSaved['end_date'] == '0000-00-00 00:00:00' ? '' : $aTagDataSaved['end_date'];
                            self::cleanTag((int) $aTag['id_tag'], $aTag['type']);
                            self::handleDynamicPromotion((int) $aTag['id_tag'], (string) $sDateFrom, (string) $sDateTo);
                        }
                    }
                }
            }
        }
    }
}
