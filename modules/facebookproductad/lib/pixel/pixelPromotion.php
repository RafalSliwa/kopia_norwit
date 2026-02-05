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

class pixelPromotion extends basePixel
{
    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;

        $iPostPage = \Tools::getValue('p');
        $iPostProductPerPage = \Tools::getValue('n');

        $iPage = !empty($iPostPage) ? $iPostPage : 0;
        $iProductPerPage = !empty($iPostProductPerPage) ? $iPostProductPerPage : \Configuration::get('PS_PRODUCTS_PER_PAGE');

        $this->aProducts = \Product::getPricesDrop(\FacebookProductAd::$iCurrentLang, $iPage, $iProductPerPage);

        if (!empty($this->aProducts)) {
            $this->bValid = true;
            $this->aJsParams = !empty($aParams['js']) && is_array($aParams['js']) ? $aParams['js'] : false;

            $this->sCurrentLang = \Context::getContext()->language->iso_code;
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
        $this->sContent_ids = ModuleTools::buildContentIds('product_listing', $this->sCurrentLang, null, $this->aProducts);
    }

    /**
     * method set the content name
     */
    public function setContentName()
    {
        // get the current category name
        $this->sContent_name = 'Promotion';
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
        $this->sContent_Category = 'Promotions';
    }
}
