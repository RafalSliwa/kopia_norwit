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

class pixelPurchase extends basePixel
{
    /**
     * @var int : current object is a Product
     */
    public $iOrderId = 0;

    /**
     * @var int : current object is a Product
     */
    public $iCartId = 0;

    /**
     * @var bool : current object is a Product
     */
    public $bUseSmarty = false;

    /**
     * __construct magic method assign
     *
     * @param array $aParams
     */
    public function __construct(array $aParams)
    {
        $this->bValid = true;

        $iCartId = \Tools::getValue('id_cart');
        $iOrderId = $aParams['iOrderId'];
        $products = [];
        $aProductInfo = [];
        $content = '';
        // Set bValid
        $this->bValid = false;

        if (!empty($iOrderId)) {
            $this->oOrder = new \Order((int) $iOrderId);
            if (\Validate::isLoadedObject($this->oOrder)) {
                $this->iOrderId = $iOrderId;
                $aProductInfo['order_information'] = $this->oOrder;
                $aProductInfo['products'] = $this->oOrder->getCartProducts();
                $this->bValid = true;
            } else {
                if (!empty($iCartId)) {
                    $this->iOrderId = \Order::getIdByCartId($iCartId);
                    $this->bValid = true;
                }
            }
        } elseif (!empty($iCartId)) {
            $this->iOrderId = \Order::getIdByCartId($iCartId);
            $this->bValid = true;
        }

        if (empty(\FacebookProductAd::$bAdvancedPack)) {
            if (!empty($aProductInfo['products'])) {
                $products = $aProductInfo['products'];
            }
        } else {
            // Handle check pack
            if (!empty($iCartId)) {
                $packIds = \AdvancedPack::getIdsPacks(true);
                $cart = new \Cart((int) $iCartId);
                $productInPack = [];
                $packOrdered = [];
                foreach ($cart->getProducts() as $product) {
                    if (in_array($product['id_product'], $packIds)) {
                        foreach ($packIds as $packId) {
                            $packContent = \AdvancedPack::getPackContent((int) $packId);

                            foreach ($packContent as $content) {
                                $productInPack[] = $content['id_product'];
                            }
                            $packOrdered[] = $content['id_pack'];
                        }
                    }
                }

                foreach ($aProductInfo['products'] as $productOrdered) {
                    // Removed splitted product from pack
                    if (!in_array($productOrdered['id_product'], $productInPack)) {
                        $products[] = $productOrdered;
                    }
                }

                // Handle set of the pack data if there is pack
                if (!empty($packOrdered)) {
                    // Build the data for pack
                    $packOrdered = array_unique($packOrdered);
                    foreach ($packOrdered as $idPack) {
                        $product = new \Product($idPack);
                        $products[] = (array) $product;
                    }
                }
            }
        }

        // get context information
        $this->sCurrentLang = new \Language((int) \Context::getContext()->cookie->id_lang);

        // build the tag
        $this->aProducts = $products;
        $this->oOrder = !empty($aProductInfo['order_information']) ? $aProductInfo['order_information'] : [];
    }

    /**
     * method set the content type
     */
    public function setTrackingType()
    {
        $this->sTrakingType = 'Purchase';
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
    }

    /**
     * method set total value
     */
    public function setValue()
    {
        // check case on the order confirmation page
        $this->fValue = moduleTools::getOrderPrice($this->oOrder, \FacebookProductAd::$conf['FPA_USE_TAX'], \FacebookProductAd::$conf['FPA_USE_SHIPPING'], \FacebookProductAd::$conf['FPA_USE_WRAPPING']);
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

    /**
     * Get API user data for Purchase event with order ID
     *
     * @return array
     */
    public function getApiUserData()
    {
        if (!$this->bValid || empty($this->oOrder)) {
            return [];
        }

        // Prepare price data for the Purchase event
        $priceData = [
            'currency' => $this->sCurrency,
            'value' => $this->fValue,
        ];

        // Get API user data with order ID for consistent event_id generation
        return moduleTools::getApiUserData(
            \Context::getContext(),
            'Purchase',
            'product',
            '', // content category
            $this->sContent_ids,
            '', // content name
            false,
            $priceData,
            'purchase', // current page
            null, // client timestamp
            $this->oOrder->id, // order ID for event_id generation
            null // client URL (not available in server-side Purchase events)
        );
    }
}
