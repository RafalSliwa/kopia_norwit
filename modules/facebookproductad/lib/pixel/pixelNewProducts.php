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

class pixelNewProducts extends basePixel
{
    /**
     * @var int : current object is a category
     */
    public $iManufacturerId = 0;

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;

        // use case - detect if we've got JS params
        $this->aJsParams = !empty($aParams['js']) && is_array($aParams['js']) ? $aParams['js'] : false;

        // handle the pagnitation
        $iPostPage = \Tools::getValue('p');
        $iPostProductPerPage = \Tools::getValue('n');

        $iPage = !empty($iPostPage) ? $iPostPage : 0;
        $iProductPerPage = !empty($iPostProductPerPage) ? $iPostProductPerPage : \Configuration::get('PS_PRODUCTS_PER_PAGE');

        // get new products
        $this->aProducts = \Product::getNewProducts(\FacebookProductAd::$iCurrentLang, $iPage, $iProductPerPage);
        $this->sCurrentLang = new \Language((int) \Context::getContext()->cookie->id_lang);

        if (!empty($this->aProducts)) {
            $this->bValid = true;
        }
    }

    /**
     * method set the content type
     */
    public function setTrackingType()
    {
        $this->sTrakingType = 'ViewCategory';
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
        $this->sContent_ids = ModuleTools::buildContentIds('product_listing', $this->sCurrentLang->iso_code, null, $this->aProducts);
    }

    /**
     * method set the content name
     */
    public function setContentName()
    {
        // get the current category name
        $this->sContent_name = 'New Products';
    }

    /**
     * method set total value
     */
    public function setValue()
    {
    }

    /**
     * method the currency
     */
    public function setCurrency()
    {
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
        $this->sContent_Category = 'New products';
    }
}
