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

class pixelCategory extends basePixel
{
    /**
     * @var int : current object is a category
     */
    public $iCategoryId = 0;

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;
        $this->sCurrentLang = new \Language(\FacebookProductAd::$iCurrentLang);

        // use case - detect if we've got JS params
        $this->aJsParams = !empty($aParams['js']) && is_array($aParams['js']) ? $aParams['js'] : false;

        // get the category id
        $this->iCategoryId = (int) $aParams['iCategoryId'];
        $this->oCategory = new \Category($this->iCategoryId, \FacebookProductAd::$iCurrentLang);

        if (!empty($this->oCategory)) {
            // handle the pagnitation
            $iPostPage = \Tools::getValue('p');
            $iPostProductPerPage = \Tools::getValue('n');
            $sPostOrderBy = \Tools::getValue('orderby');
            $sPostOrderWay = \Tools::getValue('orderby');

            $iPage = !empty($iPostPage) ? $iPostPage : 0;
            $iProductPerPage = !empty($iPostProductPerPage) ? $iPostProductPerPage : \Configuration::get('PS_PRODUCTS_PER_PAGE');
            $sOrderby = !empty($sPostOrderBy) ? $sPostOrderBy : null;
            $sOrderway = !empty($sPostOrderWay) ? $sPostOrderWay : null;

            $this->aProducts = $this->oCategory->getProducts(\FacebookProductAd::$iCurrentLang, $iPage, $iProductPerPage, $sOrderby, $sOrderway, false, true, false, 1, true, null);

            if (!empty($this->aProducts)) {
                $this->bValid = true;
            }
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
        $this->sContent_name = str_replace(['\'', '"'], ' ', $this->oCategory->name);
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
        $this->sContent_Category = str_replace(['\'', '"'], ' ', html_entity_decode(moduleTools::getProductPath($this->iCategoryId, \FacebookProductAd::$iCurrentLang)));
    }
}
