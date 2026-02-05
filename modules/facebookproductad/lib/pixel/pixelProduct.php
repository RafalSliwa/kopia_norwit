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

namespace FacebookProductAd\Pixel;

if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\ModuleLib\moduleTools;

class pixelProduct extends basePixel
{
    /**
     * @var int : current object is a Product
     */
    public $iProductId = 0;

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;

        // get the product id
        $this->iProductId = isset($aParams['iProductId']) ? $aParams['iProductId'] : false;

        if (!empty($this->iProductId)) {
            $this->bValid = true;

            // use case - detect if we've got JS params
            $this->aJsParams = !empty($aParams['js']) && is_array($aParams['js']) ? $aParams['js'] : false;

            // get the current object
            $this->oProduct = new \Product($this->iProductId, true);
            // get context information
            $this->sCurrentLang = new \Language(\Context::getContext()->cookie->id_lang);
        }
    }

    /**
     * method set the content type
     */
    public function setTrackingType()
    {
        $this->sTrakingType = 'ViewContent';
    }

    /**
     * method set the content type
     */
    public function setContentType()
    {
        $this->sContent_type = 'product';
    }

    /**
     * method set the content ids
     */
    public function setContentIds()
    {
        $this->sContent_ids = ModuleTools::buildContentIds('product', $this->sCurrentLang->iso_code, $this->iProductId);
    }

    /**
     * method set the content name
     */
    public function setContentName()
    {
        if (!empty($this->oProduct) && is_object($this->oProduct)) {
            if (!empty($this->oProduct->name[\FacebookProductAd::$iCurrentLang])) {
                $this->sContent_name = str_replace(['\'', '"'], ' ', $this->oProduct->name[\FacebookProductAd::$iCurrentLang]);
            }
        }
    }

    /**
     * method set total value
     */
    public function setValue()
    {
        // Use tax according to the option
        $bUseTax = !empty(\FacebookProductAd::$conf['FPA_PROD_PRICE_TAX']) ? true : false;

        // get the static Price with the default function from PS
        if (!empty($this->oProduct) && is_object($this->oProduct)) {
            if (empty(\FacebookProductAd::$bAdvancedPack)) {
                $ipa = \Tools::getValue('id_product_attribute');
                if (empty($ipa)) {
                    $price = \Product::getPriceStatic($this->iProductId, true, null, 2, null, false, true);
                } else {
                    $price = \Product::getPriceStatic($this->iProductId, true, (int) $ipa, 2, null, false, true);
                }
            } else {
                if (\AdvancedPack::isValidPack($this->iProductId)) {
                    $price = \AdvancedPack::getPackPrice($this->iProductId);
                } else {
                    $price = \Product::getPriceStatic($this->iProductId, true, null, 2, null, false, true);
                }
            }

            // Ensure consistent formatting: convert to float and round to 2 decimal places
            $this->fValue = round((float)$price, 2);
        }
    }

    /**
     * method the currency
     */
    public function setCurrency()
    {
        // get the currency code and ensure it's uppercase for consistency
        $currency = \Context::getContext()->currency->iso_code;
        $this->sCurrency = strtoupper(trim($currency));
    }

    /**
     * method the query search
     */
    public function setQuerySearch()
    {
    }

    /**
     * method set the category values
     */
    public function setContentCategory()
    {
        if (!empty($this->oProduct) && is_object($this->oProduct)) {
            $oCategory = new \Category($this->oProduct->id_category_default, \FacebookProductAd::$iCurrentLang);
            if (!empty($oCategory) && is_object($oCategory)) {
                $this->sContent_Category = str_replace(['\'', '"'], ' ', $oCategory->name);
            }
        }
    }
}
