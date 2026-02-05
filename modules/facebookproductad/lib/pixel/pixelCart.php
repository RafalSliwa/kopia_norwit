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

class pixelCart extends basePixel
{
    /**
     * @var int : current object is a cart
     */
    public $iCartId = 0;

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = false;
        $this->sCurrentLang = new \Language((int) \Context::getContext()->cookie->id_lang);

        // use case - detect if we've got JS params
        $this->aJsParams = !empty($aParams['js']) && is_array($aParams['js']) ? $aParams['js'] : false;

        // get the cart id
        $this->iCartId = $aParams['iCartId'];

        $this->oCart = new \Cart((int) $this->iCartId);

        if (!empty($this->oCart)) {
            $this->aProducts = $this->oCart->getProducts();

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
        if (!empty(\FacebookProductAd::$conf['FPA_TRACK_ADD_CART_PAGE'])) {
            $this->sTrakingType = 'AddToCart';
        } else {
            $this->sTrakingType = 'PageView';
        }
    }

    /**
     * method set the content type
     */
    public function setContentType()
    {
        if (!empty(\FacebookProductAd::$conf['FPA_TRACK_ADD_CART_PAGE'])) {
            $this->sContent_type = 'product';
        }
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
    }

    /**
     * method set total value
     */
    public function setValue()
    {
        // get the cart amount
        $this->fValue = $this->oCart->getOrderTotal();
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
    }
}
