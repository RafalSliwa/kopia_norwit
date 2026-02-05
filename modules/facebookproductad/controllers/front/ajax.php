<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    BusinessTech.fr
 * @copyright Business Tech
 * @license   Commercial
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\FacebookApi\FacebookClient;
use FacebookProductAd\ModuleLib\moduleTools;

/**
 * Controller to handle ajax function with Prestashop
 */
class facebookproductadAjaxModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    protected $jsonOutput = [];
    public $ajax = true;

    /**
     * init module front controller
     */
    public function init()
    {
        // exec parent
        parent::init();
        $this->ajax = true;
    }

    /**
     * Handle the check about what function to use based on action, if there no match the ajax can't be used
     *
     * @return mixed
     */
    public function displayAjax()
    {
        $sAction = Tools::getValue('action', 'undefined');

        if (!empty($sAction) && method_exists($this, 'ajaxProcess' . Tools::toCamelCase($sAction))) {
            $this->{'ajaxProcess' . Tools::toCamelCase($sAction)}();
        } else {
            $this->errors[] = $this->module->l('Undefined action', 'ajax');
        }
    }

    /**
     * Validate if the provided URL is from an allowed domain for security
     * This prevents potential security issues with client-provided URLs
     *
     * @param string $url The URL to validate
     * @return bool True if URL is valid and from allowed domain
     */
    private function isValidSourceUrl($url)
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }

        // Parse the URL to extract components
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return false;
        }

        // Get allowed domains from PrestaShop configuration
        $allowedDomains = [];

        // Add main shop domains
        $shopDomain = \Configuration::get('PS_SHOP_DOMAIN');
        $shopDomainSSL = \Configuration::get('PS_SHOP_DOMAIN_SSL');

        if (!empty($shopDomain)) {
            $allowedDomains[] = $shopDomain;
        }
        if (!empty($shopDomainSSL)) {
            $allowedDomains[] = $shopDomainSSL;
        }

        // Add current context shop domain if available
        $context = \Context::getContext();
        if (isset($context->shop) && !empty($context->shop->domain)) {
            $allowedDomains[] = $context->shop->domain;
            $allowedDomains[] = $context->shop->domain_ssl;
        }

        // Remove duplicates and empty values
        $allowedDomains = array_filter(array_unique($allowedDomains));

        // Check if the URL host matches any allowed domain
        $urlHost = strtolower($parsedUrl['host']);
        foreach ($allowedDomains as $domain) {
            if (strtolower($domain) === $urlHost) {
                return true;
            }
        }

        // Log invalid URL attempt for security monitoring
        \PrestaShopLogger::addLog(
            'Facebook API: Invalid client URL rejected: ' . $url . ' (host: ' . $urlHost . ')',
            2,
            null,
            'FacebookProductAd',
            1,
            true
        );

        return false;
    }

    /**
     * handle ajax for handle the select promotion clicked
     */
    protected function ajaxProcessUpdateConsent()
    {
        $sToken = Tools::getValue('token');
        $sModuleToken = Tools::getToken(false);
        // Do not execute if token is missing or false
        if (!empty($sToken) && $sModuleToken == $sToken) {
            // Set the user cookie
            Context::getContext()->cookie->bt_fbda_consent_lvl = 3;

            exit(\json_encode(true));
        }
    }

    /**
     * Handle the send API data fo Facebook API
     *
     * @return mixed
     */
    protected function ajaxProcessSendApiData()
    {
        $sToken = Tools::getValue('token');
        $sModuleToken = Tools::getToken(false);
        $tagContent = json_decode(Tools::getValue('tagContent'), true);
        $tagContentCheck = Tools::getValue('tagContentCheck');
        $useApiForPageView = Tools::getValue('useApiForPageView');
        $currentPage = Tools::getValue('pagetype');
        $sendPageViewViaApi = ($useApiForPageView == 1 && $currentPage != 'order-confirmation');
        $clientTimestamp = (int)Tools::getValue('clientTimestamp'); // Get client timestamp
        $sharedEventId = Tools::getValue('shared_event_id'); // Get shared event_id from pixel

        // Get client URL for better event_source_url accuracy
        $clientUrl = Tools::getValue('client_url');
        $validatedClientUrl = null;

        // Validate client URL if provided
        if (!empty($clientUrl) && $this->isValidSourceUrl($clientUrl)) {
            $validatedClientUrl = $clientUrl;
            // Log successful URL validation for debugging
            \PrestaShopLogger::addLog(
                'Facebook API: Using validated client URL for event_source_url: ' . $clientUrl,
                1,
                null,
                'FacebookProductAd',
                1,
                true
            );
        } elseif (!empty($clientUrl)) {
            // Log when client URL is rejected
            \PrestaShopLogger::addLog(
                'Facebook API: Client URL rejected, using server fallback. Rejected URL: ' . $clientUrl,
                2,
                null,
                'FacebookProductAd',
                1,
                true
            );
        }

        // Do not execute if token is missing or false
        if (!empty($sToken) && $sModuleToken == $sToken) {
            if (!empty($tagContent)) {
                // Use case for product type that needs handle some data
                if (isset($tagContent['aDynTags']['content_type'])) {
                    if ($tagContent['aDynTags']['content_type']['value'] == 'product') {
                        $priceData = [
                            'currency' => isset($tagContent['aDynTags']['currency']['value']) ? $tagContent['aDynTags']['currency']['value'] : '',
                            'value' => isset($tagContent['aDynTags']['value']['value']) ? $tagContent['aDynTags']['value']['value'] : '',
                        ];
                        $content_category = isset($tagContent['aDynTags']['content_category']) ? $tagContent['aDynTags']['content_category']['value'] : '';
                        $content_name = isset($tagContent['aDynTags']['content_name']) ? $tagContent['aDynTags']['content_name']['value'] : '';
                        $content_ids = isset($tagContent['aDynTags']['content_ids']) ? $tagContent['aDynTags']['content_ids']['value'] : '';
                        $tracking_type = isset($tagContent['aTrackingType']) ? $tagContent['aTrackingType']['value'] : '';

                        // Pass validated client URL to getApiUserData for accurate event_source_url
                        $user_data = moduleTools::getApiUserData(
                            \Context::getContext(),
                            $tracking_type,
                            'product',
                            $content_category,
                            $content_ids,
                            $content_name,
                            false,
                            $priceData,
                            $currentPage,
                            $clientTimestamp,
                            null,
                            $validatedClientUrl,
                            $sharedEventId
                        );
                        FacebookClient::send($user_data);
                    }
                }
                // Use case to handle initiate checkout with the conversion API
                if (isset($tagContent['aTrackingType']['value']) && $tagContent['aTrackingType']['value'] == 'InitiateCheckout') {
                    // Pass validated client URL for InitiateCheckout events
                    $user_data = moduleTools::getApiUserData(
                        \Context::getContext(),
                        'InitiateCheckout',
                        '',
                        '',
                        '',
                        '',
                        false,
                        [],
                        '',
                        $clientTimestamp,
                        null,
                        $validatedClientUrl,
                        $sharedEventId
                    );
                    FacebookClient::send($user_data);
                }

                if (!empty($sendPageViewViaApi)) {
                    // Pass validated client URL for PageView events
                    $user_data = moduleTools::getApiUserData(
                        \Context::getContext(),
                        'PageView',
                        '',
                        '',
                        '',
                        '',
                        false,
                        [],
                        $currentPage,
                        $clientTimestamp,
                        null,
                        $validatedClientUrl,
                        $sharedEventId
                    );
                    FacebookClient::send($user_data);
                }
            }

            exit('data has been sent to API');
        }
    }

    /**
     * Send the payment event with conversion API from Facebook
     *
     * @return mixed
     */
    protected function ajaxProcessSendPaymentInfoToApi()
    {
        $sToken = Tools::getValue('token');
        $sModuleToken = Tools::getToken(false);
        $evendId = Tools::getValue('eventId');
        // Do not execute if token is missing or false
        if (!empty($sToken) && $sModuleToken == $sToken) {
            if (!empty($evendId)) {
                // Get cart total with proper decimal formatting
                $cart = Context::getContext()->cart;
                $price_data = [];

                if (Validate::isLoadedObject($cart)) {
                    $price_data = [
                        'currency' => Context::getContext()->currency->iso_code,
                        'value' => number_format($cart->getOrderTotal(true), 2, '.', '')
                    ];
                }

                $user_data = moduleTools::getApiUserData(
                    \Context::getContext(),
                    'AddPaymentInfo',
                    '',
                    '',
                    '',
                    '',
                    false,
                    $price_data,
                    '',
                    time(),
                    null,
                    null,
                    $evendId
                );
                FacebookClient::send($user_data);
            }
        }
    }

    /**
     * Handle add to cart event when a click is made on shop
     *
     * @return mixed
     */
    protected function ajaxProcessAddToCart()
    {
        $token = Tools::getValue('token');
        $module_token = Tools::getToken(false);
        $sharedEventId = Tools::getValue('shared_event_id'); // Get shared event_id from pixel

        // Do not execute if token is missing or false
        if (!empty($token) && $module_token == $token) {
            $id_product = Tools::getValue('id_product');
            $ipa = \Tools::getValue('id_product_attribute');
            $product = new Product((int) $id_product, true, FacebookProductAd::$iCurrentLang);
            if (empty(\FacebookProductAd::$bAdvancedPack)) {
                if (empty($ipa)) {
                    $price = \Product::getPriceStatic($product->id, true, null, 2, null, false, true);
                } else {
                    $price = \Product::getPriceStatic($product->id, true, (int) $ipa, 2, null, false, true);
                }
            } else {
                if (\AdvancedPack::isValidPack($product->id)) {
                    $price = \AdvancedPack::getPackPrice($product->id);
                } else {
                    $price = \Product::getPriceStatic($product->id, true, null, 2, null, false, true);
                }
            }
            $lang_prefix = new \Language(\Context::getContext()->cookie->id_lang);

            if (!empty($id_product)) {
                $content_ids = moduleTools::buildContentIds('product', $lang_prefix->iso_code, $id_product);
                $price_data = [
                    'currency' => Context::getContext()->currency->iso_code,
                    'value' => number_format($price, 2, '.', ''),
                ];

                $this->jsonOutput['content_type'] = 'product';
                $this->jsonOutput['content_ids'] = $content_ids;
                $this->jsonOutput['value'] = $price_data['value'];
                $this->jsonOutput['currency'] = $price_data['currency'];

                // Handle the send data cart to API with shared event_id
                $user_data = moduleTools::getApiUserData(
                    \Context::getContext(),
                    'AddToCart',
                    'product',
                    '',
                    $content_ids,
                    '',
                    false,
                    $price_data,
                    '',
                    time(),
                    null,
                    null,
                    $sharedEventId
                );
                FacebookClient::send($user_data);
            }

            exit(json_encode($this->jsonOutput));
        }
    }

    /**
     * Handle update combination event (like select size and color on front office) when a click is made on shop
     *
     * @return mixed
     */
    protected function ajaxProcessUpdateCombination()
    {
        $sToken = \Tools::getValue('token');
        $sModuleToken = \Tools::getToken(false);

        // Do not execute if token is missing or false
        if (!empty($sToken) && $sModuleToken == $sToken) {
            $id_product_attribute = \Tools::getValue('id_product_attribute');
            $id_product = \Tools::getValue('id_product');
            $fPrice = 0;

            if (!empty($id_product)) {
                $oProduct = new \Product((int) $id_product, true, \FacebookProductAd::$iCurrentLang);
                $oCategory = new \Category((int) $oProduct->id_category_default, \FacebookProductAd::$iCurrentLang);
                $currentLang = new \Language(\Context::getContext()->cookie->id_lang);

                if (empty($id_product_attribute)) {
                    $fPrice = \Product::getPriceStatic((int) $oProduct->id, true, null, 2, null, false, true);
                } else {
                    $fPrice = \Product::getPriceStatic((int) $oProduct->id, true, (int) $id_product_attribute, 2, null, false, true);
                }

                // Use consistent currency and formatting approach
                $currency = \Context::getContext()->currency->iso_code;
                $formattedPrice = number_format($fPrice, 2, '.', '');

                $this->jsonOutput['content_id'] = moduleTools::buildContentIds('product', $currentLang->iso_code, $id_product);
                $this->jsonOutput['content_name'] = str_replace('\'', '', $oProduct->name[\FacebookProductAd::$iCurrentLang]);
                $this->jsonOutput['currency'] = $currency;
                $this->jsonOutput['content_category'] = $oCategory->name;
                $this->jsonOutput['value'] = $formattedPrice;

                // Send API event for combination update to maintain consistency
                if (\FacebookProductAd::$conf['FPA_USE_API']) {
                    $priceData = [
                        'currency' => $currency,
                        'value' => $formattedPrice,
                    ];
                    $user_data = moduleTools::getApiUserData(
                        \Context::getContext(),
                        'ViewContent',
                        'product',
                        $oCategory->name,
                        $this->jsonOutput['content_id'],
                        $this->jsonOutput['content_name'],
                        false,
                        $priceData,
                        '',
                        time(),
                        null,
                        null
                    );
                    FacebookClient::send($user_data);
                }
            }

            exit(json_encode($this->jsonOutput));
        }
    }
}
