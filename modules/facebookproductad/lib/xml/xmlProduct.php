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

class xmlProduct extends baseXml
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
     *
     * @return mixed
     */
    public function hasCombination($iProductId)
    {
        return [$iProductId];
    }

    /**
     * buildDetailProductXml() method build product XML tags
     *
     * @return mixed
     */
    public function buildDetailProductXml()
    {
        $id_lang = !empty((int) \Tools::getValue('fpa_lang_id')) ? (int) \Tools::getValue('fpa_lang_id') : (int) \Tools::getValue('iLangId');
        $idProduct = (int) $this->data->p->id;
        $country = !empty(\Tools::getValue('country')) ? \Tools::getValue('fpa_lang_id') : \Tools::getValue('sCountryIso');

        if (!empty(exclusionProduct::isIdProductExcluded($idProduct))) {
            return false;
        }

        if (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-basic') {
            $this->data->step->id = ModuleTools::constructFeedIdsBasic($idProduct, $country, 'product');
        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-ean') {
            $this->data->step->id = ModuleTools::constructFeedIdsEan($idProduct, $id_lang, 'product', null, null, $this->data->p->ean13);
        } elseif (\FacebookProductAd::$conf['FPA_FEED_PREF_ID'] == 'tag-id-product-ref') {
            $this->data->step->id = ModuleTools::constructFeedIdsRef($idProduct, $id_lang, 'product', null, null, $this->data->p->reference);
        }

        // get weight
        $this->data->step->weight = (float) $this->data->p->weight;

        // handle different prices and shipping fees
        $this->data->step->price_default_currency_no_tax = \Tools::convertPrice((float) \Product::getPriceStatic((int) $this->data->p->id, false, null), $this->data->currency, false);
        $this->data->step->url = moduleTools::buildProductUrl($this->data->p, $id_lang, $this->data->currencyId, $this->aParams['iShopId'], null);

        // Exclude based on min price
        if (
            !empty(\FacebookProductAd::$conf['FPA_MIN_PRICE'])
            && ((float) $this->data->step->price_default_currency_no_tax < (float) \FacebookProductAd::$conf['FPA_MIN_PRICE'])
        ) {
            moduleReporting::create()->set(
                '_no_export_min_price',
                ['productId' => $this->data->step->id_reporting]
            );

            return false;
        }

        // Use tax according to the option
        $bUseTax = !empty(\FacebookProductAd::$conf['FPA_PROD_PRICE_TAX']) ? true : false;

        $this->data->step->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6);
        $this->data->step->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6, null, false, false);
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
        if ($this->data->p->available_date != '0000-00-00') {
            $this->data->step->availabilty_date = $this->data->p->available_date;
        }

        // shipping fees
        if (!empty(\FacebookProductAd::$conf['FPA_SHIPPING_USE'])) {
            $fPrice = 0;
            $product_price_default_tax = \Tools::convertPrice((float) $this->data->step->price_raw, $this->data->currency, false);
            $fPrice = number_format((float) $this->getProductShippingFees((float) moduleTools::round($product_price_default_tax)), 2, '.', '');
            if (!empty($this->data->step->carrier_tax) && !empty($this->data->currentCarrier->id)) {
                $carrier_tax = \Tax::getCarrierTaxRate((int) $this->data->currentCarrier->id);
                $this->data->p->additional_shipping_cost *= (1 + ($carrier_tax / 100));
            }
            $this->data->step->shipping_fees = number_format($fPrice + $this->data->p->additional_shipping_cost, 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        // get images
        $this->data->step->images = $this->getImages($this->data->p);

        // quantity
        // Do not export if the quantity is 0 for the combination and export out of stock setting is not On
        if (
            (int) $this->data->p->quantity < 1
            && (int) \FacebookProductAd::$conf['FPA_EXPORT_OOS'] == 0
        ) {
            moduleReporting::create()->set(
                '_no_export_no_stock',
                ['productId' => $this->data->step->id_reporting]
            );

            return false;
        }

        // quantity
        $this->data->step->quantity = (int) $this->data->p->quantity;

        // EAN13 or UPC
        $this->data->step->ean13 = trim($this->data->p->ean13);
        $this->data->step->upc = !empty($this->data->p->upc) ? trim($this->data->p->upc) : '';

        $this->data->step->gtin = moduleTools::getGtin(\FacebookProductAd::$conf['FPA_GTIN_PREF'], (array) $this->data->p);

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
        $this->data->step->mpn = $this->getSupplierReference($this->data->p->id, $this->data->p->id_supplier, $this->data->p->supplier_reference, $this->data->p->reference);

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
     * method format the product name
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
    public function formatProductName($iAdvancedProdName, $iAdvancedProdTitle, $sProdName, $sCatName, $sManufacturerName, $iLength, $iProdAttrId = null)
    {
        $sProdName = moduleTools::truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength);

        return moduleTools::formatProductTitle($sProdName, $iAdvancedProdTitle);
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

        // get cover
        $aImage = \Product::getCover($oProduct->id);

        // Additional images
        $aOtherImages = $oProduct->getImages(\FacebookProductAd::$iCurrentLang);
        foreach ($aOtherImages as $img) {
            if ((int) $img['id_image'] != (int) $aImage['id_image'] && $iCounter <= (int) moduleConfiguration::FPA_IMG_LIMIT && $img['cover'] != 1) {
                $aResultImages[] = ['id_image' => (int) $img['id_image']];
                ++$iCounter;
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
        if (empty(\FacebookProductAd::$bCompare1770)) {
            // detect the MPN type
            $sReturnRef = moduleDao::getProductSupplierReference($iProdId, $iSupplierId);
        } else {
            $oProduct = new \Product($iProdId);
            $sReturnRef = $oProduct->mpn;
        }

        return $sReturnRef;
    }
}
