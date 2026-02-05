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

namespace FacebookProductAd\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\ModuleLib\moduleTools;

class hookDisplay extends hookBase
{
    /**
     * @var bool : define if one hook is already executed
     */
    protected static $bAlreadyExecute = false;

    /**
     * Magic Method __construct assigns few information about hook
     */
    public function __construct($sHookAction)
    {
        // set hook action
        $this->sHook = $sHookAction;
    }

    /**
     * method execute hook
     *
     * @param array $aParams
     *
     * @return array
     */
    public function run(array $aParams = null)
    {
        // set variables
        $aDisplayHook = [];

        switch ($this->sHook) {
            case 'header':
            case 'orderConfirmation':
            case 'footer':
            case 'paymentByBinaries':
            case 'paymentTop':
                // use case - display in header
                $aDisplayHook = call_user_func_array([$this, 'display' . ucfirst($this->sHook)], [$aParams]);

                break;
            default:
                break;
        }

        return $aDisplayHook;
    }

    /**
     *  method display header
     *
     * @param array $aParams
     *
     * @return array
     */
    private function displayHeader(array $aParams = null)
    {
        // detect the page
        $sPageType = (!empty($aParams['sPageType']) ? $aParams['sPageType'] : moduleTools::detectCurrentPage());
        $iOrderId = 0;
        $jsDefs = [];
        // Remove old event_id generation - now handled by generateConsistentEventId() in JavaScript

        // get required values
        $iProductId = \Tools::getvalue('id_product');
        $iCatId = \Tools::getvalue('id_category');
        $iManufacturerId = \Tools::getvalue('id_manufacturer');
        $currentLang = new \Language(\Context::getContext()->cookie->id_lang);
        $prefixLang = '';
        $ipa = \Tools::getValue('id_product_attribute');

        if (!empty(\FacebookProductAd::$conf['FPA_ADD_LANG_ID']) && !empty($currentLang->iso_code)) {
            $prefixLang = !empty($currentLang->iso_code) ? \Tools::strtoupper($currentLang->iso_code) : '';
        }

        if (!empty(\Tools::getvalue('id_order'))) {
            $iOrderId = \Tools::getvalue('id_order');
        } elseif (!empty(\Context::getContext()->controller->id_order)) {
            $iOrderId = (int) \Context::getContext()->controller->id_order;
        }

        // With some payment method the context cart id is not set
        $iCartId = !empty(\Tools::getValue('id_cart')) ? \Tools::getValue('id_cart') : \Context::getContext()->cart->id;

        // Use case for the orderId for Paybox
        if (empty($iOrderId)) {
            $iOrderId = moduleDao::getOrderIdFromCart($iCartId);
        }

        $aDynTags = [
            'iProductId' => $iProductId,
            'iCategoryId' => $iCatId,
            'iManufacturerId' => $iManufacturerId,
            'iCartId' => $iCartId,
            'iOrderId' => $iOrderId,
            'js' => [
                'wishSelectorProd' => \FacebookProductAd::$conf['FPA_JS_WISH_SELECTOR_PROD'],
            ],
        ];

        $tagContent = json_encode(moduleTools::buildDynDisplayTag($aDynTags, $sPageType));

        $jsDefs['btnAddToWishlist'] = $aDynTags['js']['wishSelectorProd'];
        $jsDefs['tagContent'] = moduleTools::buildDynDisplayTag($aDynTags, $sPageType);
        $jsDefs['tagContentApi'] = $tagContent;
        $jsDefs['tagContentApiCheck'] = md5($tagContent);
        $jsDefs['ApiToken'] = hash('md5', \Tools::getToken(false));
        $jsDefs['pixel_id'] = \FacebookProductAd::$conf['FPA_PIXEL'];
        $jsDefs['activate_pixel'] = \FacebookProductAd::$conf['FPA_ACTIVATE_PIXEL'];
        $jsDefs['bUseConsent'] = \FacebookProductAd::$conf['FPA_USE_CONSENT'];
        $jsDefs['iConsentConsentLvl'] = moduleTools::getConsentStatus();
        $jsDefs['bConsentHtmlElement'] = !empty(\FacebookProductAd::$conf['FPA_ELEMENT_HTML_ID']) ? \FacebookProductAd::$conf['FPA_ELEMENT_HTML_ID'] : '';
        $jsDefs['bConsentHtmlElementSecond'] = !empty(\FacebookProductAd::$conf['FPA_ELEMENT_HTML_SECOND_ID']) ? \FacebookProductAd::$conf['FPA_ELEMENT_HTML_SECOND_ID'] : '';
        $jsDefs['bUseAxeption'] = \FacebookProductAd::$conf['FPA_USE_AXEPTIO'];
        $jsDefs['token'] = \Tools::getToken(false);
        $jsDefs['ajaxUrl'] = \Context::getContext()->link->getModuleLink('facebookproductad', 'ajax', []);
        $jsDefs['external_id'] = !empty(\Context::getContext()->customer->id) ? hash('sha256', (string) \Context::getContext()->customer->id) : (string) \Context::getContext()->cart->id_guest;
        $jsDefs['useAdvancedMatching'] = !empty(\FacebookProductAd::$conf['FPA_ADVANCED_MATCHING']) ? true : false;
        $jsDefs['advancedMatchingData'] = !empty(moduleTools::getAdvancedMatchingData(\Context::getContext())) ? moduleTools::getAdvancedMatchingData(\Context::getContext()) : false;
        // Remove old eventId - now using generateConsistentEventId() in JavaScript
        $jsDefs['fbdaSeparator'] = \FacebookProductAd::$conf['FPA_COMBO_SEPARATOR'];
        $jsDefs['pixelCurrency'] = \Context::getContext()->currency->iso_code;
        $jsDefs['comboExport'] = \FacebookProductAd::$conf['FPA_P_COMBOS'];

        // Add order reference for Purchase events deduplication
        if (!empty($iOrderId)) {
            $order = new \Order((int)$iOrderId);
            if (\Validate::isLoadedObject($order)) {
                $jsDefs['orderReference'] = $order->reference;
                $jsDefs['id_order'] = $order->id;
            }
        }
        $jsDefs['prefix'] = \FacebookProductAd::$conf['FPA_ID_PREFIX'];
        $jsDefs['prefixLang'] = $prefixLang;
        $jsDefs['useConversionApi'] = \FacebookProductAd::$conf['FPA_USE_API'];
        $jsDefs['useApiForPageView'] = \FacebookProductAd::$conf['FPA_API_PAGE_VIEW'];
        $jsDefs['currentPage'] = $sPageType;
        $jsDefs['id_order'] = $iOrderId;
        $jsDefs['id_product_attribute'] = $ipa;

        if (!empty($jsDefs['tagContent']['currency']['value'])) {
            $jsDefs['tagContent']['currency']['value'] = str_replace('\'', '', $jsDefs['tagContent']['currency']['value']);
        }

        \Media::addJsDef(['btPixel' => $jsDefs]);
        \Context::getContext()->controller->addJS(moduleConfiguration::FPA_URL_JS . 'pixel.js');

        return ['tpl' => moduleConfiguration::FPA_TPL_HOOK_PATH . 'header.tpl', 'assign' => []];
    }
}
