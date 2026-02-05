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

namespace FacebookProductAd\Xml;

if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\exclusionProduct;
use FacebookProductAd\ModuleLib\moduleReporting;
use FacebookProductAd\ModuleLib\moduleTools;

class xmlCombination extends baseXml
{
    /**
     * Magic Method __construct
     *
     * @param array $aParams
     */
    public function __construct(array $aParams = null)
    {
        parent::__construct($aParams);
    }

    /**
     * Magic Method __destruct
     */
    public function __destruct()
    {
    }

    /**
     * hasCombination() method load products combination
     *
     * @param int $iProductId
     * @param bool $bExcludedProduct
     *
     * @return mixed
     */
    public function hasCombination($iProductId, $bExcludedProduct = false)
    {
        return moduleDao::getProductCombination($this->aParams['iShopId'], $iProductId, $bExcludedProduct);
    }

    /**
     * buildDetailProductXml() method build product XML tags
     *
     * @return mixed
     */
    public function buildDetailProductXml()
    {
        $separator = \FacebookProductAd::$conf['FPA_COMBO_SEPARATOR'];
        $idProduct = (int) $this->data->p->id;
        $id_lang = !empty((int) \Tools::getValue('fpa_lang_id')) ? (int) \Tools::getValue('fpa_lang_id') : (int) \Tools::getValue('iLangId');
        $id_shop = !empty((int) \Tools::getValue('id_shop')) ? (int) \Tools::getValue('id_shop') : (int) \Tools::getValue('iShopId');
        $idAttribute = (int) $this->data->c['id_product_attribute'];

        if (!empty(exclusionProduct::isIdProductExcluded($idProduct, $idAttribute))) {
            return false;
        }

        if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-basic') {
            $this->data->step->id = ModuleTools::constructFeedIdsBasic($idProduct, $id_lang, 'combination', $idAttribute, $separator);
        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
            if ($this->data->c['ean13'] != $this->data->p->ean13) {
                $this->data->step->id = ModuleTools::constructFeedIdsEan($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['ean13']);
            } else {
                $this->data->step->id = ModuleTools::constructFeedIdsEanWhenHasSameValues($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['reference']);
            }
        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
            if ($this->data->c['reference'] != $this->data->p->reference) {
                $this->data->step->id = ModuleTools::constructFeedIdsRef($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['reference']);
            } else {
                $this->data->step->id = ModuleTools::constructFeedIdsRefWhenHasSameValues($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['reference']);
            }
        }

        // Use case for product with no combo
        $this->data->step->id_no_combo = $this->data->p->id;
        $this->data->step->url = moduleTools::buildProductUrl($this->data->p, (int) $id_lang, $this->data->currencyId, $id_shop, $this->data->c['id_product_attribute']);

        // get weight
        $this->data->step->weight = (float) $this->data->p->weight + (float) $this->data->c['weight'];

        // Use tax according to the option
        $bUseTax = !empty(\FacebookProductAd::$conf['FPA_PROD_PRICE_TAX']) ? true : false;

        // handle different prices and shipping fees
        $this->data->step->price_default_currency_no_tax = \Tools::convertPrice((float) \Product::getPriceStatic((int) $this->data->p->id, false, (int) $this->data->c['id_product_attribute']), $this->data->currency, false);

        // Exclude based on min price
        if (
            !empty(\FacebookProductAd::$conf['FPA_MIN_PRICE'])
            && ((float) $this->data->step->price_default_currency_no_tax < (float) \FacebookProductAd::$conf['FPA_MIN_PRICE'])
        ) {
            moduleReporting::create()->set('_no_export_min_price', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        $this->data->step->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, (int) $this->data->c['id_product_attribute']);
        $this->data->step->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, (int) $this->data->c['id_product_attribute'], 6, null, false, false);
        $this->data->step->price = number_format(moduleTools::round($this->data->step->price_raw), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        $this->data->step->price_no_discount = number_format(moduleTools::round($this->data->step->price_raw_no_discount), 2, '.', '') . ' ' . $this->data->currency->iso_code;

        // Use case handle pack price accuratlly
        if (\FacebookProductAd::$bAdvancedPack && \AdvancedPack::isValidPack($this->data->p->id)) {
            $oPack = new \AdvancedPack($this->data->p->id);
            $this->data->step->price_raw_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_raw = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        $this->data->step->availabilty_date = '';

        if ($this->data->c['available_date'] != '0000-00-00') {
            $this->data->step->availabilty_date = $this->data->c['available_date'];
        }
        // shipping fees
        if (!empty(\FacebookProductAd::$conf['FPA_SHIPPING_USE'])) {
            $fPrice = 0;
            $product_price_default_tax = \Tools::convertPrice((float) $this->data->step->price_raw, $this->data->currency, false);
            $fPrice = number_format((float) $this->getProductShippingFees((float) moduleTools::round($product_price_default_tax)), 2, '.', '');
            if (!empty($this->data->step->carrier_tax->id) && !empty($this->data->currentCarrier)) {
                $carrier_tax = \Tax::getCarrierTaxRate((int) $this->data->currentCarrier->id);
                $this->data->p->additional_shipping_cost *= (1 + ($carrier_tax / 100));
            }
            $this->data->step->shipping_fees = number_format($fPrice + $this->data->p->additional_shipping_cost, 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        // get images
        $this->data->step->images = $this->getImages($this->data->p, $this->data->c['id_product_attribute']);

        // quantity
        // Do not export if the quantity is 0 for the combination and export out of stock setting is not On
        if (
            (int) $this->data->c['combo_quantity'] <= 0
            && (int) \FacebookProductAd::$conf['FPA_EXPORT_OOS'] == 0
        ) {
            moduleReporting::create()->set(
                '_no_export_no_stock',
                ['productId' => $this->data->step->id_reporting]
            );

            return false;
        }
        $this->data->step->quantity = (int) $this->data->c['combo_quantity'];

        // EAN13 or UPC
        $this->data->step->ean13 = $this->data->c['ean13'];
        $this->data->step->upc = !empty($this->data->c['upc']) ? $this->data->c['upc'] : '';

        if (!empty(moduleTools::getGtin(\FacebookProductAd::$conf['FPA_GTIN_PREF'], $this->data->c))) {
            $this->data->step->gtin = moduleTools::getGtin(\FacebookProductAd::$conf['FPA_GTIN_PREF'], $this->data->c);
        } else {
            $this->data->step->gtin = moduleTools::getGtin(\FacebookProductAd::$conf['FPA_GTIN_PREF'], (array) $this->data->p);
        }

        // Exclude without EAN
        if (
            \FacebookProductAd::$conf['FPA_EXC_NO_EAN']
            && (empty($this->data->step->ean13) || \Tools::strlen($this->data->step->ean13) < 10)
            && (empty($this->data->step->upc) || \Tools::strlen($this->data->step->upc) < 10)
        ) {
            moduleReporting::create()->set(
                '_no_export_no_ean_upc',
                ['productId' => $this->data->step->id_reporting]
            );

            return false;
        }

        // supplier reference
        $this->data->step->mpn = $this->getSupplierReference($this->data->p->id, $this->data->p->id_supplier, $this->data->p->supplier_reference, $this->data->p->reference, (int) $this->data->c['id_product_attribute'], $this->data->c['supplier_reference'], $this->data->c['reference']);

        // exclude if mpn is empty
        if (
            !empty(\FacebookProductAd::$conf['FPA_EXC_NO_MREF'])
            && !\FacebookProductAd::$conf['FPA_INC_ID_EXISTS']
            && empty($this->data->step->mpn)
        ) {
            moduleReporting::create()->set(
                '_no_export_no_supplier_ref',
                ['productId' => $this->data->step->id_reporting]
            );

            return false;
        }

        $this->data->step->visibility = $this->data->p->visibility;

        return true;
    }

    /**
     * format the product name
     *
     * @param int $iAdvancedProdName
     * @param int $iAdvancedProdTitle
     * @param string $sProdName
     * @param string $sCatName
     * @param string $sManufacturerName
     * @param int $iLength
     * @param int $iProdAttrId
     *
     * @return mixed
     */
    public function formatProductName($iAdvancedProdName, $iAdvancedProdTitle, $sProdName, $sCatName, $sManufacturerName, $iLength, $iProdAttrId = null, $iLangId = null, $sPrefix = null, $sSuffix = null)
    {
        // Use case to add or not combination data
        if (!empty(\FacebookProductAd::$conf['FPA_INCL_ATTR_VALUE'])) {
            // get the combination attributes to format the product name
            $aCombinationAttr = moduleDao::getProductComboAttributes($iProdAttrId, $this->aParams['iLangId'], $this->aParams['iShopId']);
            if (!empty($aCombinationAttr)) {
                $sExtraName = '';
                foreach ($aCombinationAttr as $c) {
                    $sExtraName .= ' ' . stripslashes($c['name']);
                }
                $sProdName .= $sExtraName;
            }
        }
        // encode
        $sProdName = moduleTools::truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength);
        $sProdName = moduleTools::formatProductTitle($sProdName, $iAdvancedProdTitle);

        return $sProdName;
    }

    /**
     * method get images of one product or one combination
     *
     * @param \Product $oProduct
     * @param int $iProdAttributeId
     *
     * @return array
     */
    public function getImages(\Product $oProduct, $iProdAttributeId = null)
    {
        // set vars
        $aResultImages = [];
        $iCounter = 1;

        // get images of combination
        $aAttributeImages = $oProduct->getCombinationImages(\FacebookProductAd::$iCurrentLang);

        if (!empty($aAttributeImages[$iProdAttributeId]) && is_array($aAttributeImages[$iProdAttributeId])) {
            $aImage = ['id_image' => $aAttributeImages[$iProdAttributeId][0]['id_image']];
        } else {
            $aImage = \Product::getCover($oProduct->id);
        }

        // Additional images
        unset($aAttributeImages['id_image']);

        if (!empty($aAttributeImages) && is_array($aAttributeImages)) {
            foreach ($aAttributeImages[$iProdAttributeId] as $sImg) {
                if ($iCounter <= (int) moduleConfiguration::FPA_IMG_LIMIT) {
                    $aResultImages[] = ['id_image' => $sImg['id_image']];
                    ++$iCounter;
                }
            }
        }

        return ['image' => $aImage, 'others' => $aResultImages];
    }

    /**
     * method get supplier reference
     *
     * @param int $iProdId
     * @param int $iSupplierId
     * @param string $sSupplierRef
     * @param string $sProductRef
     * @param int $iProdAttributeId
     * @param string $sCombiSupplierRef
     * @param string $sCombiRef
     *
     * @return mixed
     */
    public function getSupplierReference($iProdId, $iSupplierId, $sSupplierRef = null, $sProductRef = null, $iProdAttributeId = 0, $sCombiSupplierRef = null, $sCombiRef = null)
    {
        return moduleDao::getProductSupplierReference($iProdId, $iSupplierId, $iProdAttributeId);
    }
}
