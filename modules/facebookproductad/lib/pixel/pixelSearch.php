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

class pixelSearch extends basePixel
{
    /**
     * @var string : the query to search
     */
    public $sQuery = '';

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;

        // get the search query
        $sQuery = \Tools::getValue('s');

        // Sometimes the param is q
        if (empty($sQuery)) {
            $sQuery = \Tools::getValue('q');
        }

        $this->sQuery = $sQuery;

        // handle the pagnitation
        $iPostPage = \Tools::getValue('p');
        $iPostProductPerPage = \Tools::getValue('n');

        $iPage = !empty($iPostPage) ? $iPostPage : 0;
        $iProductPerPage = !empty($iPostProductPerPage) ? $iPostProductPerPage : \Configuration::get('PS_PRODUCTS_PER_PAGE');

        // get the search results
        $this->aProducts = \Search::find(\FacebookProductAd::$iCurrentLang, $this->sQuery, $iPage, $iProductPerPage, 'position', 'desc');

        if (!empty($this->aProducts['result'])) {
            $this->bValid = true;
        }

        // get the context information
        $this->sCurrentLang = new \Language((int) \Context::getContext()->cookie->id_lang);
    }

    /**
     * method set the content type
     */
    public function setTrackingType()
    {
        $this->sTrakingType = 'Search';
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
        $productIds = [];

        // Handle case to extract product result from the search query
        if (!empty($this->aProducts['result'])) {
            foreach ($this->aProducts['result'] as $data) {
                $productIds[] = $data;
            }
        }

        $this->sContent_ids = ModuleTools::buildContentIds('product_listing', $this->sCurrentLang->iso_code, null, $productIds);
    }

    /**
     * method set the content name
     */
    public function setContentName()
    {
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
        // get the search query
        $this->sQuerySearch = $this->sQuery;
    }

    /**
     * method set the category values
     */
    public function setContentCategory()
    {
    }
}
