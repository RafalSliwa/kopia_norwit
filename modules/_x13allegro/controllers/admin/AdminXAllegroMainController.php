<?php

require_once (dirname(__FILE__) . '/../../x13allegro.php');

use x13allegro\Adapter\Module\x13gpsrAdapter;
use x13allegro\Api\Adapter\Category\ExcludedFromGPSR;
use x13allegro\Api\DataProvider\ResponsibleProducersProvider;
use x13allegro\Api\Model\Attachment as AttachmentModel;
use x13allegro\Api\Model\DateTime;
use x13allegro\Api\Model\Image;
use x13allegro\Api\Model\Marketplace\Enum\Marketplace;
use x13allegro\Api\Model\Offers\AdditionalMarketplaces\Marketplace as AdditionalMarketplace;
use x13allegro\Api\Model\Offers\Attachments\AttachmentType;
use x13allegro\Api\Model\Offers\Enum\SellingModeType;
use x13allegro\Api\Model\Offers\OfferProduct;
use x13allegro\Api\Model\Offers\ProductSet as AllegroProductSet;
use x13allegro\Api\Model\Offers\ProductSet\Product as AllegroProduct;
use x13allegro\Api\Model\Offers\ProductSet\SafetyInformationType;
use x13allegro\Api\Model\Offers\Promotion;
use x13allegro\Api\Model\PromotionPackages\Enum\PackageType;
use x13allegro\Api\Model\PromotionPackages\Enum\PackageModificationType;
use x13allegro\Api\Model\PromotionPackages\PromotionPackages;
use x13allegro\Api\DataFactory\CategoriesParametersFactory;
use x13allegro\Api\DataProvider\AdditionalServicesProvider;
use x13allegro\Api\DataProvider\AfterSaleServicesProvider;
use x13allegro\Api\DataProvider\CategoriesProvider;
use x13allegro\Api\DataProvider\CategoriesParametersProvider;
use x13allegro\Api\DataProvider\DeliveryMethodsProvider;
use x13allegro\Api\DataProvider\LoyaltyProvider;
use x13allegro\Api\DataProvider\MarketplacesProvider;
use x13allegro\Api\DataProvider\OfferFeesProvider;
use x13allegro\Api\DataProvider\ProductSearchProvider;
use x13allegro\Api\DataProvider\ResponsiblePersonsProvider;
use x13allegro\Api\DataProvider\SizeTablesProvider;
use x13allegro\Api\DataProvider\TaxesProvider;
use x13allegro\Api\XAllegroApi;
use x13allegro\Component\Logger\LogType;
use x13allegro\Exception\ModuleException;
use x13allegro\Form\CategoryParameters\ParametersForm;
use x13allegro\Json\JsonMapBuilder;
use x13allegro\SyncManager\Offer\Enum\ProcessOperation;
use x13allegro\SyncManager\Offer\OfferProcessManager;

final class AdminXAllegroMainController extends XAllegroController
{
    protected $allegroAutoLogin = true;
    protected $allegroAccountSwitch = true;

    private $allegroPromotionPackages;
    private $allegroTemplates;
    private $deliveryOptionsList;
    private $shippingRatesList;

    /** @var XAllegroPas */
    private $allegroPasDefault;

    /** @var CategoriesProvider */
    private $categoriesProvider;

    /** @var CategoriesParametersProvider */
    private $categoriesParametersProvider;

    /** @var TaxesProvider */
    private $taxesProvider;

    public function __construct()
    {
        parent::__construct();

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminXAllegroPerform'));

        $this->tpl_folder = 'x_allegro_main/';
    }

    public function init()
    {
        parent::init();

        if (!$this->allegroApi) {
            if ($this->ajax) {
                die(json_encode(array('apiError' => true, 'messages' => $this->errors)));
            }

            return;
        }

        $this->categoriesProvider = new CategoriesProvider($this->allegroApi);
        $this->categoriesParametersProvider = new CategoriesParametersProvider($this->allegroApi);
        $this->taxesProvider = new TaxesProvider($this->allegroApi);

        if (!$this->ajax) {
            $this->allegroTemplates = XAllegroTemplate::getList();
            $this->deliveryOptionsList = XAllegroPas::getList();
            $this->allegroPasDefault = XAllegroPas::getDefault(true);

            try {
                $this->allegroPromotionPackages = $this->allegroApi->sale()->promotionPackages()->getPromotionPackages();

                $result = $this->allegroApi->sale()->shippingRates()->getAll()->shippingRates;

                foreach ($result as $shippingRate) {
                    $shippingRateMarketplaces = [];
                    foreach ($shippingRate->marketplaces as $marketplace) {
                        $shippingRateMarketplaces[] = $marketplace->id;
                    }

                    $this->shippingRatesList[$shippingRate->id] = array(
                        'id' => $shippingRate->id,
                        'name' => $shippingRate->name,
                        'marketplaces' => $shippingRateMarketplaces
                    );
                }
            }
            catch (Exception $ex) {
                $this->errors[] = (string)$ex;
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryUI(array(
            'ui.datepicker',
            'ui.slider',
            'ui.sortable'
        ));
        $this->addjQueryPlugin('date');

        $this->addJS($this->module->getPathUri() . 'views/js/tinymce/tinymce.min.js');
        $this->addJS($this->module->getPathUri() . 'views/js/tinymce/jquery.tinymce.min.js');

        $this->addCSS($this->module->getPathUri() . 'views/css/allegro-description.css');
    }

    /**
     * @see AdminController::checkAccess()
     * @param bool $disable
     * @return bool
     */
    public function viewAccess($disable = false)
    {
        if (!parent::viewAccess($disable)) {
            return false;
        }

        if ($this->tabAccess['edit'] !== '1') {
            $this->errors[] = $this->l('Nie masz uprawnień do wystawiania nowych ofert.');
            return false;
        }

        return true;
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        $this->toolbar_title[] = $this->l('Wystaw nowe oferty');
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['allegro_sold'] = array(
            'href' => '#',
            'desc' => $this->l('Wystaw oferty'),
            'icon' => 'process-icon-gavel icon-gavel',
            'class' => 'xallegro-perform'
        );

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        $this->toolbar_btn['allegro_sold'] = array(
            'href' => '#',
            'desc' => $this->l('Wystaw oferty'),
            'class' => 'xallegro-perform fa fa-gavel'
        );

        parent::initToolbar();

        unset($this->toolbar_btn['new']);
    }

    public function initContent()
    {
        if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('Wybierz konkretny kontekst sklepu aby wystawić nową ofertę');
            return;
        }

        parent::initContent();

        if ($this->allegroAuthorization !== true) {
            return;
        }

        $accountName = $this->allegroApi->getAccount()->username;
        if ($this->allegroApi->getAccount()->sandbox) {
            $accountName .= ' (sandbox)';
        }

        $this->context->smarty->assign([
            'current_tab_level' => 2,
            'toolbar_scroll' => true,
            'toolbar_title' => $this->toolbar_title,
            'toolbar_subtitle' => sprintf('na koncie: %s, %s', $accountName, $this->context->shop->name),
            'toolbar_btn' =>  $this->page_header_toolbar_btn,
        ]);

        if (empty($this->allegroTemplates)) {
            $this->errors[] = $this->l('Brak utworzonych/aktywnych szablonów -').' <a href="'.$this->context->link->getAdminLink('AdminXAllegroTemplates').'">'.$this->l('dodaj/aktywuj szablon').'</a>.';
        }
        if (empty($this->deliveryOptionsList)) {
            $this->errors[] = $this->l('Brak utworzonych profili dostawy -').' <a href="'.$this->context->link->getAdminLink('AdminXAllegroPas').'&addxallegro_delivery">'.$this->l('dodaj profil dostawy').'</a>.';
        }
        if (empty($this->shippingRatesList)) {
            $this->errors[] = $this->l('Brak utworzonych cenników dostawy, lub wystąpił problem z API Allegro -').' <a href="'.$this->context->link->getAdminLink('AdminXAllegroPas').'&addxallegro_delivery_rate">'.$this->l('dodaj cennik dostawy').'</a>.';
        }
        if (!Tools::getValue('id_product')) {
            $this->errors[] = $this->l('Brak wybranych produktów do wystawienia');
        }

        if (!empty($this->errors)) {
            return;
        }

        $products = XAllegroProduct::getProducts(
            array_map('intval', explode(',', Tools::getValue('id_product'))),
            $this->context->shop->id,
            $this->allegroApi->getAccount(),
            $this->context
        );

        Hook::exec('filterX13AllegroAuctionProducts', array('products' => &$products));

        /** @todo Refactoring */
        $image_type = array('name' => null);
        foreach (array_reverse(ImageType::getImagesTypes(null, true)) as $imgType) {
            if ($imgType['products']) {
                $image_type = $imgType;
                break;
            }
        }

        $accountConfiguration = new XAllegroConfigurationAccount($this->allegroApi->getAccount()->id);
        $deliveryFields = $this->_getPasFields();
        $marketplaces = [];

        foreach (Marketplace::values() as $marketplace) {
            $marketplacesProvider = new MarketplacesProvider($marketplace->getValue());
            $marketplaceCountry = $marketplacesProvider->getMarketplaceCountry();
            $marketplaceCurrency = $marketplacesProvider->getMarketplaceCurrency();

            $marketplaces[$marketplace->getValue()] = [
                'id' => $marketplace->getValue(),
                'name' => $marketplace->getValueTranslated(),
                'countryName' => $marketplaceCountry->name,
                'countryCode' => $marketplaceCountry->iso_code,
                'currencySign' => $marketplaceCurrency->sign,
                'currencyIsoCode' => $marketplaceCurrency->iso_code,
                'currencyPrecision' => $marketplaceCurrency->precision,
                'currencyConversionRate' => $marketplaceCurrency->conversion_rate
            ];
        }

        $shippingRateMarketplaces = [];
        $shippingRateDefaultId = $accountConfiguration->get('SHIPPING_RATE_DEFAULT_ID');
        $shippingRateId = ($shippingRateDefaultId ?: current($this->shippingRatesList)['id']);

        foreach ($this->shippingRatesList as $shippingRate) {
            $shippingRateMarketplaces[$shippingRate['id']] = $shippingRate['marketplaces'];
        }

        $this->context->smarty->assign(array(
            'account' => $this->allegroApi->getAccount(),
            'products' => $products,
            'durations' => XAllegroAuction::getPublicationDurationOptions(),
            'durations_auction' => XAllegroAuction::getPublicationDurationOptions('AUCTION'),
            'templates' => $this->allegroTemplates,
            'shippingRateMarketplaces' => $shippingRateMarketplaces,
            'shippingRateSelectedId' => $shippingRateId,
            'shipping_rates' => $this->shippingRatesList,
            'shipments' => $this->provideDeliveryMethods($this->shippingRatesList[$shippingRateId]['id']),
            'afterSales' => (new AfterSaleServicesProvider($this->allegroApi))->getAllServices(),
            'additionalServices' => (new AdditionalServicesProvider($this->allegroApi))->getAdditionalServices(),
            'sizeTables' => (new SizeTablesProvider($this->allegroApi))->getSizeTables(),
            'wholesalePriceList' => (new LoyaltyProvider($this->allegroApi))->getWholesalePriceList(),
            'promotionPackages' => $this->allegroPromotionPackages,
            'responsibleProducers' => (new ResponsibleProducersProvider($this->allegroApi))->getResponsibleProducers(),
            'responsiblePersons' => (new ResponsiblePersonsProvider($this->allegroApi))->getResponsiblePersons(),
            'safetyInformationTypes' => SafetyInformationType::toChoseList(),
            'safetyInformationTextMax' => XAllegroApi::PRODUCT_SAFETY_TEXT_MAX,
            'safetyInformationAttachmentMax' => XAllegroApi::PRODUCT_SAFETY_ATTACHMENT_MAX,
            'safetyInformationAttachmentMaxFilesize' => XAllegroApi::ATTACHMENT_MAX_FILESIZE,
            'safetyInformationAttachmentExtensions' => XAllegroAttachment::getAllowedExtensions(AttachmentType::SAFETY_INFORMATION_MANUAL()),
            'safetyInformationAttachmentMimeTypes' => XAllegroAttachment::getAllowedMimeTypes(AttachmentType::SAFETY_INFORMATION_MANUAL()),
            'categories' => $this->categoriesProvider->getCategoriesList(),
            'category_path' => array(null),
            'select_images' => XAllegroConfiguration::get('SELECT_IMAGES'),
            'select_images_max' => XAllegroApi::PHOTO_COMPANY_MAX,
            'account_sandbox' => (int)$this->allegroApi->getAccount()->sandbox,
            'image_legacy' => $this->checkLegacyImages(),
            'images_preview_type' => $image_type,
            'product_select_mode' => XAllegroConfiguration::get('SELECT_ALL'),
            'productization_mode' => XAllegroConfiguration::get('PRODUCTIZATION_MODE'),
            'message_to_seller' => !(int)$accountConfiguration->get('AUCTION_DISABLE_ORDER_MESSAGE', true),
            'b2b_only' => (int)$accountConfiguration->get('AUCTION_B2B_ONLY', true),
            'titleMinWords' => XAllegroApi::TITLE_MIN_WORDS,
            'titleMinSize' => XAllegroApi::TITLE_MIN_SIZE,
            'titleMaxSize' => XAllegroApi::TITLE_MAX_SIZE,
            'marketplaces' => $marketplaces,

            // bulk default options
            'bulk_send_tax' => (int)XAllegroConfiguration::get('PRICE_TAX'),

            // forms
            'pas_fields' => $this->renderCustomForm($deliveryFields, $this->_getFieldsValuesFrom($deliveryFields), 0),

            // tinymce
            'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
            'path_css' => _THEME_CSS_DIR_,
            'iso' => (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $this->context->language->iso_code . '.js') ? $this->context->language->iso_code : 'en'),

            // productization
            'productization_name' => XAllegroConfiguration::get('PRODUCTIZATION_NAME'),
            'productization_description' => XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION'),
            'productization_gpsr' => XAllegroConfiguration::get('PRODUCTIZATION_GPSR'),
            'productization_show_reference' => XAllegroConfiguration::get('PRODUCTIZATION_SHOW_REFERENCE'),
            'productization_show_gtin' => XAllegroConfiguration::get('PRODUCTIZATION_SHOW_GTIN'),
            'productization_show_mpn' => XAllegroConfiguration::get('PRODUCTIZATION_SHOW_MPN'),

            'fees_enabled' => $accountConfiguration->get('AUCTION_CALCULATE_FEES', true),
            'x13gpsrInstalled' => (bool)(new x13gpsrAdapter())->getInstance(),
            'x13gpsrInfoHide' => XAllegroConfiguration::get('X13GPSR_INFO_HIDE')
        ));

        // fix HelperForm tinymce autoload
        $removeJS = [
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'admin/tinymce.inc.js'
        ];

        if (defined('_TB_VERSION_')) {
            foreach ($removeJS as $js) {
                $jsCheck = preg_grep("#$js#", $this->js_files);
                if (!empty($jsCheck)) {
                    unset($this->js_files[key($jsCheck)]);
                }
            }
        } else {
            $this->context->controller->removeJS($removeJS);
        }
    }

    /**
     * @param string $shippingRateId
     * @param bool $group
     * @return array
     */
    private function provideDeliveryMethods($shippingRateId, $group = true)
    {
        $deliveryMethods = array();

        try {
            $dataProvider = new DeliveryMethodsProvider(
                $this->allegroApi->sale()->deliveryMethods()->getAll()->deliveryMethods
            );

            $deliveryMethods = $dataProvider->getDeliveryMethodsWithShippingRates(
                $this->allegroApi->sale()->shippingRates()->getDetails($shippingRateId)
            );

            if ($group) {
                $deliveryMethods = $dataProvider->groupDeliveryMethods($deliveryMethods);
            }
        }
        catch (Exception $ex) {
            $this->errors[] = $ex;
        }

        return $deliveryMethods;
    }

    /**
     * @return bool
     */
    private function checkLegacyImages()
    {
        if (!defined('_PS_PROD_IMG_DIR_') || version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            return false;
        }

        $image_legacy = false;
        $dir = _PS_PROD_IMG_DIR_;

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false && $image_legacy == false) {
                    if (!is_dir($dir.DIRECTORY_SEPARATOR.$file) && $file[0] != '.' && is_numeric($file[0])) {
                        $image_legacy = true;
                    }
                }
                closedir($dh);
            }
        }

        return $image_legacy;
    }

    private function _getFieldsValuesFrom(array $fields)
    {
        $fieldsValues = array();

        foreach ($fields[0]['form']['input'] as $input)
        {
            $fieldValue = Tools::getValue($input['name'], null);

            if ($fieldValue === null && isset($input['default_value'])) {
                $fieldValue = $input['default_value'];
            }

            $fieldsValues[$input['name']] = $fieldValue;
        }

        return $fieldsValues;
    }

    private function _getPasFields()
    {
        $profiles = array();
        foreach ($this->deliveryOptionsList as $pas) {
            $profiles[] = array(
                'id_xallegro_pas' => $pas['id'],
                'name' => $pas['name']
            );
        }

        $deliveryOptionsFields[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Płatność i dostawa')
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Profil'),
                    'name' => 'pas',
                    'class' => 'fixed-width-xxl',
                    'options' => array(
                        'query' => array_merge(
                            array(
                                array(
                                    'id_xallegro_pas' => '0',
                                    'name' => $this->l('-- Wybierz --')
                                )
                            ), $profiles),
                        'id' => 'id_xallegro_pas',
                        'name' => 'name'
                    ),
                    'default_value' => $this->allegroPasDefault->id
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Czas wysyłki'),
                    'name' => 'handling_time',
                    'required' => true,
                    'class' => 'fixed-width-xxl',
                    'options' => array(
                        'query' => XAllegroPas::getHandlingTimeOptions(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default_value' => $this->allegroPasDefault->handling_time
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Dodatkowe informacje o dostawie'),
                    'name' => 'additional_info',
                    'class' => 'fixed-width-xxl',
                    'rows' => 5,
                    'cols' => 35,
                    'default_value' => $this->allegroPasDefault->additional_info
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Kraj'),
                    'name' => 'country_code',
                    'class' => 'fixed-width-xxl',
                    'required' => true,
                    'options' => array(
                        'query' => XAllegroPas::getCountryCodeOptions(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default_value' => $this->allegroPasDefault->country_code
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Województwo'),
                    'name' => 'province',
                    'class' => 'fixed-width-xxl',
                    'options' => array(
                        'query' => XAllegroPas::getProvinceOptions(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default_value' => $this->allegroPasDefault->province
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Kod pocztowy'),
                    'name' => 'post_code',
                    'class' => 'fixed-width-xxl',
                    'size' => 12,
                    'default_value' => $this->allegroPasDefault->post_code
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Miasto'),
                    'name' => 'city',
                    'class' => 'fixed-width-xxl',
                    'size' => 35,
                    'required' => true,
                    'default_value' => $this->allegroPasDefault->city
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Opcje faktury'),
                    'name' => 'invoice',
                    'class' => 'fixed-width-xxl',
                    'required' => true,
                    'options' => array(
                        'query' => XAllegroPas::getInvoiceOptions(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'default_value' => $this->allegroPasDefault->invoice
                ),
            )
        );

        return $deliveryOptionsFields;
    }

    /**
     * @param array $item
     * @param array|null $errors
     * @return void
     */
    private function validateAuction(array $item, array &$errors = null)
    {
        static $ps_order_out_of_stock = null;
        if ($ps_order_out_of_stock === null) {
            $ps_order_out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
        }

        if (!$item['category_id']) {
            $errors[] = $this->l('Nie wybrano kategorii.');
        }
        else if (!$item['category_is_leaf']) {
            $errors[] = $this->l('Wybrana kategoria nie jest kategorią najniższego rzędu.');
        }

        if ($item['selling_mode'] == SellingModeType::AUCTION && $item['quantity'] > 1) {
            $errors[] = $this->l('Ilość przedmiotów w ofercie z ceną wywoławczą nie może być większa niż 1.');
        }

        if (!isset($item['quantity']) || !$item['quantity']) {
            $errors[] = $this->l('Nie podano ilości.');
        }
        else if ((int)$item['quantity'] < 1) {
            $errors[] = $this->l('Podana ilość musi być większa od zera.');
        }
        else if (XAllegroConfiguration::get('QUANITY_CHECK')) {
            $productOOS = StockAvailable::outOfStock($item['id_product'], $this->context->shop->id);
            $oos = $productOOS == 2 ? (int)$ps_order_out_of_stock : $productOOS;

            if (!$oos && (int)$item['quantity'] > StockAvailable::getQuantityAvailableByProduct($item['id_product'], $item['id_product_attribute'], $this->context->shop->id)) {
                $errors[] = $this->l('Podana ilość jest większą niż dostępna.');
            }
        }

        // if ($item['category_gpsr']) {
        //     if (!$item['responsible_producer']) {
        //         $errors[] = $this->l('Nie podano danych teleadresowych producenta.');
        //     }
        //     if (!$item['safety_information_type']) {
        //         $errors[] = $this->l('Nie wybrano rodzaju informacji o bezpieczeństwie produktu.');
        //     }

        //     if ($item['safety_information_type'] === SafetyInformationType::ATTACHMENTS) {
        //         $attachmentsCount = count($item['safety_information_attachment_product']) + count($item['safety_information_attachment_offer']);

        //         if (!$attachmentsCount) {
        //             $errors[] = $this->l('Nie wybrano załączników z informacją o bezpieczeństwie produktu.');
        //         }
        //     }
        //     else if ($item['safety_information_type'] === SafetyInformationType::TEXT) {
        //         $textLength = mb_strlen($item['safety_information_text']);

        //         if (!$textLength) {
        //             $errors[] = $this->l('Nie uzupełniono tekstu informacji o bezpieczeństwie produktu.');
        //         }
        //         else if ($textLength > XAllegroApi::PRODUCT_SAFETY_TEXT_MAX) {
        //             $errors[] = $this->l('Tekst informacji o bezpieczeństwie produktu jest za długi.');
        //         }
        //     }
        // }
    }

    /**
     * @return array
     */
    private function _getAuctionsFromPost()
    {
        $auctionsData = [
            //'tags' => Tools::getValue('xallegro_tag', array()),
            'starting_at' => (Tools::getValue('start') ? Tools::getValue('start_time') : null),
            'warranty' => Tools::getValue('warranty', null),
            'implied_warranty' => Tools::getValue('implied_warranty', null),
            'return_policy' => Tools::getValue('return_policy', null),
            'message_to_seller' => Tools::getValue('message_to_seller'),
            'offer_b2b_only' => (bool)Tools::getValue('offer_b2b_only'),
            'additional_services' => Tools::getValue('additional_services', null),
            'pas' => [
                'country_code' => Tools::getValue('country_code'),
                'province' => Tools::getValue('province', null),
                'post_code' => Tools::getValue('post_code', null),
                'city' => Tools::getValue('city'),
                'invoice' => Tools::getValue('invoice'),
                'handling_time' => Tools::getValue('handling_time'),
                'additional_info' => Tools::getValue('additional_info', null),
                'shipping_rate' => Tools::getValue('shipping_rate')
            ]
        ];

        // ajax data limit is set by hand to 1
        // but current index is dynamic
        $itemPOST = Tools::getValue('item');
        $currentIndex = key($itemPOST);
        $item = current($itemPOST);

        $images = [];
        if ($item['image_main']) {
            $images[] = $item['image_main'];
        }

        if (isset($item['images']) && !empty($item['images'])) {
            foreach ($item['images'] as $image) {
                if ($image != $item['image_main']) {
                    $images[] = $image;
                }
            }
        }

        $auctionsData['item'] = [
            'enabled' => (isset($item['enabled']) ? $item['enabled'] : false),
            'x_id' => $currentIndex,
            'category_id' => (int)(!empty($item['category_id']) ? $item['category_id'] : false),
            'category_is_leaf' => (int)(!empty($item['category_is_leaf']) ? $item['category_is_leaf'] : false),
            'productization_mode' => (isset($item['productization_mode']) ? $item['productization_mode'] : false), // when its null then auction is disabled to issue
            'allegro_product_id' => !empty($item['allegro_product_id']) ? $item['allegro_product_id'] : null,
            'allegro_product_images' => !empty($item['allegro_product_images']) ? json_decode($item['allegro_product_images'], true) : null,
            'allegro_product_description' => !empty($item['allegro_product_description']) ? json_decode($item['allegro_product_description']) : null,
            'allegro_product_trusted_content_name' => (int)(!empty($item['allegro_product_trusted_content_name']) ? $item['allegro_product_trusted_content_name'] : false),
            'allegro_product_trusted_content_images' => (int)(!empty($item['allegro_product_trusted_content_images']) ? $item['allegro_product_trusted_content_images'] : false),
            'allegro_product_trusted_content_description' => (int)(!empty($item['allegro_product_trusted_content_description']) ? $item['allegro_product_trusted_content_description'] : false),
            'allegro_product_trusted_content_category' => (int)(!empty($item['allegro_product_trusted_content_category']) ? $item['allegro_product_trusted_content_category'] : false),
            'id_product' => (int)$item['id_product'],
            'id_product_attribute' => (int)$item['id_product_attribute'],
            'id_auction' => (float)$item['id_auction'],
            'id_template' => (isset($item['template']) ? (int)$item['template'] : null),
            'images' => $images,
            'title' => $item['title'],
            'description' => $item['description'],
            'ean' => $item['ean13'],
            'upc' => $item['upc'],
            'reference' => $item['reference'],
            'selling_mode' => $item['selling_mode'],
            'duration' => (!$item['duration'] ? null : $item['duration']),
            'auto_renew' => (isset($item['auto_renew']) ? $item['auto_renew'] : null),
            'price_buy_now' => $item['price_buy_now'],
            'price_starting' => (isset($item['price_asking']) ? $item['price_asking'] : 0),
            'price_minimal' => (isset($item['price_minimal']) ? $item['price_minimal'] : 0),
            'price_calculate_fees' => $item['price_calculate_fees'],
            'tax_rate' => (isset($item['tax_rate']) ? $item['tax_rate'] : null),
            'send_tax' => (isset($item['send_tax']) ? $item['send_tax'] : 0),
            'quantity' => (isset($item['quantity']) ? $item['quantity'] : 1),
            'unit' => $item['quantity_type'],
            'category_parameters' => (isset($item['category_fields']) ? $item['category_fields'] : []),
            'category_ambiguous_parameters' => (isset($item['category_ambiguous_fields']) ? $item['category_ambiguous_fields'] : []),
            'category_gpsr' => (int)$item['category_gpsr'],
            'responsible_producer' => !empty($item['responsible_producer']) ? $item['responsible_producer'] : null,
            'responsible_person' => !empty($item['responsible_person']) ? $item['responsible_person'] : null,
            'safety_information_type' => !empty($item['safety_information_type']) ? $item['safety_information_type'] : null,
            'safety_information_text' => !empty($item['safety_information_text']) ? trim(str_replace("\r\n", "\n", $item['safety_information_text'])) : '',
            'safety_information_attachment_product' => !empty($item['safety_information_attachment_product']) ? $item['safety_information_attachment_product'] : [],
            'safety_information_attachment_offer' => !empty($item['safety_information_attachment_offer']) ? $item['safety_information_attachment_offer'] : [],
            'safety_information_attachment_x13gpsr' => !empty($item['safety_information_attachment_x13gpsr']) ? $item['safety_information_attachment_x13gpsr'] : [],
            //'tags_individual' => (isset($item['product_tags']) ? true : false),
            //'tags' => (isset($item['product_tags']) && isset($item['tags']) ? $item['tags'] : []),
            'marketplaces' => (isset($item['marketplace']) ? $item['marketplace'] : []),
            'promotionPackages' => [
                PackageType::BASE => (isset($item['basePackages']) && $item['basePackages'] ? $item['basePackages'] : false),
                PackageType::EXTRA => (isset($item['extraPackages']) ? $item['extraPackages'] : [])
            ],
            'preorder' => (isset($item['preorder'])),
            'preorder_date' => (isset($item['preorder_date']) ? $item['preorder_date'] : false),
            'size_table' => (isset($item['size_table']) ? $item['size_table'] : false),
            'wholesale_price' => (isset($item['wholesale_price']) ? $item['wholesale_price'] : false)
        ];

        return $auctionsData;
    }

    public function ajaxProcessSaveDesc()
    {
        $collection = new Collection('XAllegroProduct');
        $collection->where('id_product', '=', Tools::getValue('id_product'));
        $collection->where('id_product_attribute', '=', Tools::getValue('id_product_attribute'));

        if (Validate::isLoadedObject($collection->getFirst()))
        {
            $collection->getFirst()->description = Tools::getValue('description');
            $collection->getFirst()->save();
        }
        else {
            $product = new XAllegroProduct();
            $product->id_product = Tools::getValue('id_product');
            $product->id_product_attribute = Tools::getValue('id_product_attribute');
            $product->save();
        }

        die(true);
    }

    public function ajaxProcessUpdateImagesPositions()
    {
        XAllegroProduct::updateImagesPositions(
            Tools::getValue('id_product'),
            Tools::getValue('id_product_attribute'),
            Tools::getValue('images')
        );

        die(true);
    }

    public function ajaxProcessPreview()
    {
        $item = array();
        foreach (Tools::getValue('data') as $data)
        {
            $name = explode(']', $data['name']);
            if (!isset($name[1])) {
                continue;
            }

            $name = trim($name[1], '[');

            if ($name == 'images' || $name == 'additional') {
                $item[$name][] = $data['value'];
            } else {
                $item[$name] = $data['value'];
            }
        }

        if ((int)Tools::getValue('allegroDescription')) {
            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'product-allegro-description-preview.tpl');
            $tpl->assign([
                'allegroDescriptionStyle' => file_get_contents(_PS_MODULE_DIR_ . 'x13allegro/views/css/allegro-description.css'),
                'allegroDescription' => json_decode($item['allegro_product_description'], false)
            ]);

            die(json_encode([
                'preview' => $tpl->fetch()
            ]));
        }

        $templateOverride = false;
        $templateModifierExec = Hook::exec(
            'actionX13AllegroTemplatePreviewModifier',
            [
                'item' => $item,
                'template' => &$templateOverride
            ],
            null,
            true
        );
        if ($templateModifierExec && $templateOverride !== false) {
            die(json_encode(array(
                'preview' => $templateOverride['preview']
            )));
        }

        if (!isset($item['description'])) {
            $item['description'] = '';
        }

        $images = array_values(array_unique(array_merge(
            (isset($item['image_main']) && $item['image_main'] ? array($item['image_main']) : array()),
            (isset($item['images']) && is_array($item['images']) ? $item['images'] : array())
        )));

        $template = new XAllegroTemplate($item['template']);
        $product = new Product($item['id_product'], false, $this->allegroApi->getAccount()->id_language, $this->context->shop->id);
        $xProduct = new XAllegroProduct(null, $product->id);

        $template->setProduct($product, $xProduct);

        if ($item['id_product_attribute']) {
            $combination = $product->getAttributeCombinationsById($item['id_product_attribute'], $this->allegroApi->getAccount()->id_language);
            $template->setProductAttribute($combination);
        }

        $template->prepareVariables($item, $product, $images);
        $template->render();

        die(json_encode(array(
            'preview' => $template->getHTML()
        )));
    }

    public function ajaxProcessSearchInProductization()
    {
        $searchOptions = json_decode(XAllegroConfiguration::get('PRODUCTIZATION_SEARCH'), true);
        $searchPhrase = Tools::getValue('searchPhrase');
        $searchProduct = [];

        $productName = Tools::getValue('productName');
        $productReference = Tools::getValue('productReference');
        $productEAN13 = Tools::getValue('productEAN13');
        $productISBN = Tools::getValue('productISBN');
        $productUPC = Tools::getValue('productUPC');
        $productMPN = Tools::getValue('productMPN');

        $productsFromAllegroSearch = [];
        $productsFoundOption =
        $productsFoundMode =
        $productChosen = null;

        // manual search
        if (!empty($searchPhrase)) {
            $searchPhrase = trim($searchPhrase);
            // temporary disabled
            // manual search always as everywhere
            //$searchProduct['manual']['mode'][ProductSearchProvider::SEARCH_GTIN][] = $searchPhrase;
            //$searchProduct['manual']['mode'][ProductSearchProvider::SEARCH_MPN][] = $searchPhrase;
            $searchProduct['manual']['mode'][ProductSearchProvider::SEARCH_EVERYWHERE][] = $searchPhrase;
        }
        // search on init
        // manual search with empty "searchPhrase"
        else {
            if (isset($searchOptions['GTIN']['search']) && $searchOptions['GTIN']['search']) {
                if (!empty($productEAN13) && strlen($productEAN13) >= 8) {
                    $searchProduct['GTIN']['mode'][ProductSearchProvider::SEARCH_GTIN][] = $productEAN13;
                    $searchProduct['GTIN']['mode'][ProductSearchProvider::SEARCH_GTIN][] = str_pad($productEAN13, 13, '0', STR_PAD_LEFT);
                }
                if (!empty($productISBN)) {
                    $searchProduct['GTIN']['mode'][ProductSearchProvider::SEARCH_GTIN][] = $productISBN;
                }
                if (!empty($productUPC)) {
                    $searchProduct['GTIN']['mode'][ProductSearchProvider::SEARCH_GTIN][] = $productUPC;
                }

                if (isset($searchProduct['GTIN'])) {
                    $searchProduct['GTIN']['select'] = $searchOptions['GTIN']['select'];
                }
            }

            if (isset($searchOptions['MPN']['search']) && $searchOptions['MPN']['search'] && !empty($productMPN)) {
                $searchProduct['MPN']['mode'][ProductSearchProvider::SEARCH_MPN][] = $productMPN;
                $searchProduct['MPN']['select'] = $searchOptions['MPN']['select'];
            }

            if (isset($searchOptions['reference']['search']) && $searchOptions['reference']['search'] && !empty($productReference)) {
                // Product reference could be everything, and be everywhere
                // it depends on how client use this feature
                $searchProduct['reference']['mode'][ProductSearchProvider::SEARCH_GTIN][] = $productReference;
                $searchProduct['reference']['mode'][ProductSearchProvider::SEARCH_MPN][] = $productReference;
                $searchProduct['reference']['mode'][ProductSearchProvider::SEARCH_EVERYWHERE][] = $productReference;
                $searchProduct['reference']['select'] = $searchOptions['reference']['select'];
            }

            if (isset($searchOptions['product_name']['search']) && $searchOptions['product_name']['search']) {
                $searchProduct['product_name']['mode'][ProductSearchProvider::SEARCH_EVERYWHERE][] = $productName;
                $searchProduct['product_name']['select'] = $searchOptions['product_name']['select'];
            }
        }

        try {
            foreach ($searchProduct as $searchOption => $searchModeList) {
                foreach ($searchModeList['mode'] as $searchMode => $phraseList) {
                    foreach ($phraseList as $phrase) {
                        $productsFromAllegroSearch = (new ProductSearchProvider($this->allegroApi))->search($phrase, $searchMode);

                        if (!empty($productsFromAllegroSearch)) {
                            if ($searchOption != 'manual' && isset($searchOptions[$searchOption])) {
                                $productsFoundOption = $searchOptions[$searchOption]['select'];
                                $productsFoundMode = $searchOption;
                            }

                            break 3;
                        }
                    }
                }
            }

            if (empty($productsFromAllegroSearch)) {
                exit(json_encode([
                    'result' => false,
                    'message' => '<div class="alert medium-alert alert-danger" role="alert"><p class="alert-text">'.$this->l('Nie znaleziono powiązania z Katalogiem Allegro').'</p></div>',
                ]));
            }

            $productsCategories = [];
            foreach ($productsFromAllegroSearch as $product) {
                $productsCategories[$product->id] = $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($product->category->id));
            }

            $productSearchModalContent = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'product-selection-modal-content.tpl');
            $productSearchModalContent->assign([
                'products_from_allegro' => $productsFromAllegroSearch,
                'products_categories' => $productsCategories,
                'products_url' => 'https://allegro.pl' . ($this->allegroApi->getAccount()->sandbox ? '.allegrosandbox.pl' : '')
            ]);

            $countProductsFromAllegroSearch = count($productsFromAllegroSearch);
            if ($searchPhrase === false // not manual search
                && (($productsFoundOption == 'always_first' && $countProductsFromAllegroSearch >= 1)
                    || ($productsFoundOption == 'only_single' && $countProductsFromAllegroSearch == 1))
            ) {
                $productFromAllegro = $productsFromAllegroSearch[0];

                $parametersForm = (new ParametersForm())
                    ->setController($this)
                    ->setLanguage($this->allegroApi->getAccount()->id_language)
                    ->setCategory((new XAllegroCategory(XAllegroCategory::getIdByAllegroCategory($productFromAllegro->category->id))))
                    ->setParameters($this->categoriesParametersProvider->getParameters($productFromAllegro->category->id))
                    ->mapProductizationToHelperForm(
                        $productFromAllegro->parameters,
                        Tools::getValue('productId'),
                        Tools::getValue('productAttributeId'),
                        $productEAN13
                    );

                $allegroProductResponsibleProducers =
                $allegroProductSafetyInformation = '';

                if (!empty($productFromAllegro->productSafety)) {
                    if (!empty($productFromAllegro->productSafety->responsibleProducers)) {
                        $allegroProductResponsibleProducers = [];

                        foreach ($productFromAllegro->productSafety->responsibleProducers as $responsibleProducer) {
                            $allegroResponsibleProducer = new \stdClass();
                            $allegroResponsibleProducer->id = $responsibleProducer->id;
                            $allegroResponsibleProducer->name = '(Katalog Allegro) ' . $responsibleProducer->name;
                            $allegroProductResponsibleProducers[] = $allegroResponsibleProducer;
                        }

                        $allegroProductResponsibleProducers = json_encode($allegroProductResponsibleProducers);
                    }

                    if (!empty($productFromAllegro->productSafety->safetyInformation)
                        && $productFromAllegro->productSafety->safetyInformation->type === SafetyInformationType::TEXT
                    ) {
                        $allegroProductSafetyInformation = json_encode($productFromAllegro->productSafety->safetyInformation);
                    }
                }

                // trusted content paths are postponed by Allegro
                $allegroProductTrustedContentPaths = '';
                /*if (!empty($productFromAllegro->trustedContent->paths)) {
                    if (!in_array('name', $productFromAllegro->trustedContent->paths)
                        && in_array('category', $productFromAllegro->trustedContent->paths)
                        && in_array('images', $productFromAllegro->trustedContent->paths)
                        && in_array('description', $productFromAllegro->trustedContent->paths)
                    ) {
                        $productFromAllegro->trustedContent->paths[] = 'name';
                    }

                    $allegroProductTrustedContentPaths = json_encode($productFromAllegro->trustedContent->paths);
                }*/

                $productChosen = [
                    'categoryId' => $productFromAllegro->category->id,
                    'categoryIsLeaf' => 1, // this is always to be true
                    'categoryGPSR' => !ExcludedFromGPSR::isExcluded($productFromAllegro->category->id),
                    'categoryPath' => $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($productFromAllegro->category->id)),
                    'taxes' => $this->taxesProvider->getTaxesForCategory($productFromAllegro->category->id),
                    'allegroProductId' => $productFromAllegro->id,
                    'allegroProductUrl' => XAllegroApi::generateProductUrl($productFromAllegro->id, $this->allegroApi->getAccount()->sandbox),
                    'allegroProductName' => $productFromAllegro->name,
                    'allegroProductImages' => json_encode($productFromAllegro->images),
                    'allegroProductDescription' => (isset($productFromAllegro->description) ? json_encode($productFromAllegro->description) : ''),
                    'allegroProductCategoryDefault' => $productFromAllegro->category->id,
                    'allegroProductCategorySimilar' => (!empty($productFromAllegro->category->similar) ? json_encode($productFromAllegro->category->similar) : false),
                    'allegroProductCategorySimilarCount' => (!empty($productFromAllegro->category->similar) ? count($productFromAllegro->category->similar) +1 : 0), // +1 add default category
                    'allegroProductResponsibleProducers' => $allegroProductResponsibleProducers,
                    'allegroProductSafetyInformation' => $allegroProductSafetyInformation,
                    'allegroProductTrustedContentPaths' => $allegroProductTrustedContentPaths,
                    'parameters' => str_replace('class="category-field', 'class="category-field ', $parametersForm->buildForm()),
                    'parametersDepending' => $parametersForm->getDependsOnValueIds(),
                    'parametersRequiredIf' => $parametersForm->getRequiredIfValuesIds()
                ];
            }

            exit(json_encode([
                'result' => true,
                'nbProducts' => count($productsFromAllegroSearch),
                'productSelectionModal' => $productSearchModalContent->fetch(),
                'productsFoundOption' => $productsFoundOption,
                'productsFoundMode' => $productsFoundMode,
                'productChosen' => $productChosen
            ]));
        }
        catch (Exception $e) {
            exit(json_encode([
                'result' => false,
                'message' => '<div class="alert medium-alert alert-danger" role="alert"><p class="alert-text">' . $e . '</div>',
            ]));
        }
    }

    public function ajaxProcessSelectFromProductization()
    {
        $categoryCurrentId = Tools::getValue('categoryCurrent');
        $allegroProductId = Tools::getValue('allegroProductId');
        $productEAN13 = Tools::getValue('productEAN13');
        $productId = (int)Tools::getValue('productId');
        $productAttributeId = (int)Tools::getValue('productAttributeId');

        try {
            $productDetails = $this->allegroApi
                ->sale()
                ->products()
                ->getProduct($allegroProductId);

            $categoryId = ($categoryCurrentId ?: $productDetails->category->id);

            $parametersForm = (new ParametersForm())
                ->setController($this)
                ->setLanguage($this->allegroApi->getAccount()->id_language)
                ->setCategory((new XAllegroCategory(XAllegroCategory::getIdByAllegroCategory($categoryId))))
                ->setParameters($this->categoriesParametersProvider->getParameters($categoryId))
                ->mapProductizationToHelperForm(
                    $productDetails->parameters,
                    $productId,
                    $productAttributeId,
                    $productEAN13
                );

            $allegroProductResponsibleProducers =
            $allegroProductSafetyInformation = '';

            if (!empty($productDetails->productSafety)) {
                if (!empty($productDetails->productSafety->responsibleProducers)) {
                    $allegroProductResponsibleProducers = [];

                    foreach ($productDetails->productSafety->responsibleProducers as $responsibleProducer) {
                        $allegroResponsibleProducer = new \stdClass();
                        $allegroResponsibleProducer->id = $responsibleProducer->id;
                        $allegroResponsibleProducer->name = '(Katalog Allegro) ' . $responsibleProducer->name;
                        $allegroProductResponsibleProducers[] = $allegroResponsibleProducer;
                    }

                    $allegroProductResponsibleProducers = json_encode($allegroProductResponsibleProducers);
                }

                if (!empty($productDetails->productSafety->safetyInformation)
                    && $productDetails->productSafety->safetyInformation->type === SafetyInformationType::TEXT
                ) {
                    $allegroProductSafetyInformation = json_encode($productDetails->productSafety->safetyInformation);
                }
            }

            // trusted content paths are postponed by Allegro
            $allegroProductTrustedContentPaths = '';
            /*if (!empty($productDetails->trustedContent->paths)) {
                if (!in_array('name', $productDetails->trustedContent->paths)
                    && in_array('category', $productDetails->trustedContent->paths)
                    && in_array('images', $productDetails->trustedContent->paths)
                    && in_array('description', $productDetails->trustedContent->paths)
                ) {
                    $productDetails->trustedContent->paths[] = 'name';
                }

                $allegroProductTrustedContentPaths = json_encode($productDetails->trustedContent->paths);
            }*/

            exit(json_encode([
                'result' => true,
                'parameters' => str_replace('class="category-field', 'class="category-field ', $parametersForm->buildForm()),
                'parametersDepending' => $parametersForm->getDependsOnValueIds(),
                'parametersRequiredIf' => $parametersForm->getRequiredIfValuesIds(),
                'categoryId' => $categoryId,
                'categoryIsLeaf' => 1, // this is always to be true
                'categoryGPSR' => !ExcludedFromGPSR::isExcluded($categoryId),
                'categoryPath' => $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($categoryId)),
                'taxes' => $this->taxesProvider->getTaxesForCategory($categoryId),
                'allegroProductUrl' => XAllegroApi::generateProductUrl($productDetails->id, $this->allegroApi->getAccount()->sandbox),
                'allegroProductName' => $productDetails->name,
                'allegroProductId' => $productDetails->id,
                'allegroProductImages' => json_encode($productDetails->images),
                'allegroProductDescription' => (isset($productDetails->description) ? json_encode($productDetails->description) : ''),
                'allegroProductCategoryDefault' => $productDetails->category->id,
                'allegroProductCategorySimilar' => (!empty($productDetails->category->similar) ? json_encode($productDetails->category->similar) : false),
                'allegroProductCategorySimilarCount' => (!empty($productDetails->category->similar) ? count($productDetails->category->similar) +1 : 0), // +1 add default category
                'allegroProductResponsibleProducers' => $allegroProductResponsibleProducers,
                'allegroProductSafetyInformation' => $allegroProductSafetyInformation,
                'allegroProductTrustedContentPaths' => $allegroProductTrustedContentPaths
            ]));
        }
        catch (Exception $e) {
            exit(json_encode([
                'result' => false,
                'message' => (string)$e,
            ]));
        }
    }

    public function ajaxProcessGetCategories()
    {
        $categories =
        $categoriesPath =
        $parameters =
        $parametersDepending =
        $parametersRequiredIf =
        $taxes = [];
        $isLeaf =
        $categoriesFieldsProduct = false;

        $productIds = Tools::getValue('productsIds', []);
        $categoryId = Tools::getValue('id_allegro_category');
        $category = $this->categoriesProvider->getCategoryDetails($categoryId);

        if (Tools::getValue('full_path')) {
            foreach ($this->categoriesProvider->getCategoriesPath($categoryId) as $id => $list) {
                $categoriesPath[] = [
                    'id' => $id,
                    'list' => $list
                ];
            }
        } else {
            $categories = $this->categoriesProvider->getCategoriesList($categoryId);
        }

        if ($category && $category->leaf) {
            $isLeaf = true;
        }

        if ($isLeaf && !empty($productIds)) {
            try {
                $parameters = $this->categoriesParametersProvider->getParameters($categoryId);
                $taxes = $this->taxesProvider->getTaxesForCategory($categoryId);
            }
            catch (Exception $ex) {}

            if (!empty($parameters)) {
                list($x_id, $productId, $productAttributeId, $productCategoryId) = explode('_', $productIds[0]);

                $defaultCategoryMapId = XAllegroCategory::getIdByAllegroCategory($categoryId);
                $categoryMapId = XAllegroCategory::getPreciseMappingCategory($categoryId, $productCategoryId);

                $parametersFormProduct = new ParametersForm();
                $categoriesFieldsProduct = $parametersFormProduct
                    ->setController($this)
                    ->setLanguage($this->allegroApi->getAccount()->id_language)
                    ->setCategory(new XAllegroCategory($categoryMapId ?: $defaultCategoryMapId))
                    ->setParameters($parameters)
                    ->setProductFieldsValues($productId, $productAttributeId)
                    ->buildForm();

                $parametersDepending = $parametersFormProduct->getDependsOnValueIds();
                $parametersRequiredIf = $parametersFormProduct->getRequiredIfValuesIds();
            }
        }

        die(json_encode(array(
            'last_node' => (int)$isLeaf,
            'gpsr' => $isLeaf && !ExcludedFromGPSR::isExcluded($categoryId),
            'fields_product' => $categoriesFieldsProduct,
            'fields_product_depending' => $parametersDepending,
            'fields_product_required_if' => $parametersRequiredIf,
            'categories' => $categories,
            'categories_array' => $categoriesPath,
            'category_path' => $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($categoryId)),
            'taxes' => $taxes
        )));
    }

    public function ajaxProcessGetCategoriesSimilar()
    {
        $categoryDefaultId = Tools::getValue('categoryDefault');
        $categoryList[$categoryDefaultId] = $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($categoryDefaultId));

        foreach (json_decode(Tools::getValue('categorySimilar')) as $categorySimilar) {
            $categoryList[$categorySimilar->id] = $this->formatCategoryPath($this->categoriesProvider->getCategoriesPathLite($categorySimilar->id));
        }

        $categorySimilarModalContent = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'product-category-similar-modal-content.tpl');
        $categorySimilarModalContent->assign([
            'index' => Tools::getValue('index'),
            'categoryList' => $categoryList,
            'categoryCurrent' => Tools::getValue('categoryCurrent')
        ]);

        die(json_encode([
            'success' => true,
            'html' => $categorySimilarModalContent->fetch()
        ]));
    }

    public function ajaxProcessGetCategoriesParameters()
    {
        $categoryList = json_decode(Tools::getValue('categoryList', '[]'), true);
        $categoryPaths =
        $categoryParameters = [];

        try {
            foreach (array_keys($categoryList) as $categoryId) {
                $categoryPaths[$categoryId] = $this->categoriesProvider->getCategoriesPathLite($categoryId);

                $categoryParameters[$categoryId] = (new ParametersForm())
                    ->setController($this)
                    ->setParameters($this->categoriesParametersProvider->getParameters($categoryId))
                    ->setFieldsValues()
                    ->buildForm();
            }
        }
        catch (Exception $ex) {
            die(json_encode([
                'success' => false,
                'message' => (string)$ex
            ]));
        }

        $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'bulk-category-parameters-modal-content.tpl');
        $tpl->assign([
            'categoryList' => $categoryList,
            'categoryPaths' => $categoryPaths,
            'categoryParameters' => $categoryParameters
        ]);

        die(json_encode([
            'success' => true,
            'modalContent' => $tpl->fetch()
        ]));
    }

    public function ajaxProcessUploadSafetyInformationAttachment()
    {
        if (!is_dir(X13_ALLEGRO_ATTACHMENT_DIR)) {
            mkdir(X13_ALLEGRO_ATTACHMENT_DIR, 0775, true);
        }

        try {
            if (empty($_FILES['file']['tmp_name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new RuntimeException($this->l('Wystąpił błąd podczas przesyłania załącznika'));
            }

            if ($_FILES['file']['size'] > XAllegroAttachment::getMaxFilesizeBytes()) {
                throw new RuntimeException(sprintf($this->l('Przesłany załącznik (%dMB) jest większy niż maksymalne %dMB'), $_FILES['file']['size'] / 1024 / 1024, XAllegroApi::ATTACHMENT_MAX_FILESIZE));
            }

            $fileInfo = new SplFileInfo($_FILES['file']['name']);
            $fileExtension = $fileInfo->getExtension();
            $fileMimeType = mime_content_type($_FILES['file']['tmp_name']);

            if (!XAllegroAttachment::isAllowedMimeTypeForExtension($fileExtension, $fileMimeType)
                || !in_array($fileExtension, XAllegroAttachment::getAllowedExtensions(AttachmentType::SAFETY_INFORMATION_MANUAL()), true)
                || !in_array($fileMimeType, XAllegroAttachment::getAllowedMimeTypes(AttachmentType::SAFETY_INFORMATION_MANUAL()), true)
            ) {
                throw new RuntimeException($this->l('Nieobsługiwany typ załącznika'));
            }

            $fileName = md5(implode('_', [
                $_FILES['file']['name'],
                microtime()
            ]));

            if (!move_uploaded_file($_FILES['file']['tmp_name'], X13_ALLEGRO_ATTACHMENT_DIR . $fileName)) {
                throw new RuntimeException($this->trans('Wystąpił błąd podczas zapisu załącznika', [], 'Modules.X13manager.Support'));
            }

            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'gpsr-safety-information-attachment-row.tpl');
            $tpl->assign([
                'file' => $fileName,
                'name' => $fileInfo->getBasename(".$fileExtension"),
                'mimetype' => $fileMimeType,
                'type' => $fileExtension
            ]);

            die(json_encode([
                'success' => true,
                'attachmentsRow' => $tpl->fetch()
            ]));
        }
        catch (Exception $ex) {
            die(json_encode([
                'success' => false,
                'message' => $ex->getMessage()
            ]));
        }
    }

    public function ajaxProcessX13GPSRInfoHide()
    {
        XAllegroConfiguration::updateValue('X13GPSR_INFO_HIDE', 1);

        die(json_encode([
            'success' => true
        ]));
    }

    public function ajaxProcessGetTags()
    {
        $tags_product =
        $tags_category =
        $tags = false;

        $xCategory = new XAllegroCategory(XAllegroCategory::getIdByAllegroCategory(Tools::getValue('id_allegro_category')));
        $ids = Tools::getValue('productsIds');
        $tags = (new XAllegroHelperTagManager())
            ->setEditable(false)
            ->renderTagsTable($this->allegroApi);

        if ($tags && $ids) {
            foreach ($ids as $id)
            {
                list($x_id, $id_product) = explode('_', $id);

                $tagManger = new XAllegroHelperTagManager();
                $tag_map = $tagManger->getTagMap(
                    $xCategory,
                    new XAllegroProduct(null, $id_product),
                    $this->allegroApi->getAccount()->id
                );

                if (!empty($tag_map)) {
                    $tags_product[$x_id] = $tagManger
                        ->setEditable(false)
                        ->renderTagsTable($this->allegroApi, $tag_map);
                }
            }
        }

        if (!Tools::getValue('onlyProducts')) {
            $tags_category = (new XAllegroHelperTagManager())
                ->setMapType(XAllegroTagManager::MAP_AUCTION)
                ->setContainer('tags')
                ->renderAuctionManager($this->allegroApi, $xCategory->tags);
        }

        die(json_encode(array(
            'tags' => $tags,
            'tags_product' => $tags_product,
            'tags_category' => $tags_category,
            'tag_manager_auction_limit' => XAllegroApi::TAG_AUCTION_LIMIT
        )));
    }

    public function ajaxProcessGetPas()
    {
        $pas = new XAllegroPas(Tools::getValue('id'));

        die(json_encode(array(
            'id' => $pas->id,
            'name' => $pas->name,
            'city' => $pas->city,
            'country_code' => $pas->country_code,
            'post_code' => $pas->post_code,
            'province' => $pas->province,
            'invoice' => $pas->invoice,
            'handling_time' => $pas->handling_time,
            'additional_info' => $pas->additional_info
        )));
    }

    public function ajaxProcessChangeShippingRate()
    {
        $deliveryMethods = $this->provideDeliveryMethods(Tools::getValue('shipping_rate'), false);

        if (!empty($this->errors)) {
            die(json_encode(array(
                'result' => false,
                'message' => $this->errors
            )));
        }

        die(json_encode(array(
            'result' => true,
            'deliveryMethods' => $deliveryMethods
        )));
    }

    public function ajaxProcessPerformAuctions()
    {
        $auctionsData = $this->_getAuctionsFromPost();
        $item = &$auctionsData['item'];

        // exit;
        if (!$item['enabled']) {
            die(json_encode([]));
        }

        $offerId = 0;
        $calculatedFees = 0;
        $description = null;
        $imagesUploaded = [];
        $errors = [];

        $this->validateAuction($item, $errors);

        // exit;
        if (!empty($errors)) {
            $this->returnOfferErrorResponse($errors, $item['x_id'], $offerId, $item['title']);
        }

        if ($item['id_template']) {
            $templateOverride = false;
            $templateModifierExec = Hook::exec(
                'actionX13AllegroTemplateModifier',
                [
                    'item' => $item,
                    'template' => &$templateOverride
                ],
                null,
                true
            );

            if ($templateModifierExec && $templateOverride !== false) {
                $imagesUploaded = $this->uploadOfferImages($templateOverride['usedImages'], $errors);
                foreach($imagesUploaded as $image) {
                    $templateOverride['content'] = preg_replace('/' . $image['field'] . '/', $image['url'], $templateOverride['content']);
                }

                $description = json_decode($templateOverride['content']);
            } else {
                $template = $this->prepareOfferTemplate($item);
                $imagesUploaded = $this->uploadOfferImages($template->getUsedImages(), $errors);
                $description = $template->encodeTemplate($imagesUploaded);
            }
        }

        // exit;
        if (!empty($errors)) {
            $this->returnOfferErrorResponse($errors, $item['x_id'], $offerId, $item['title']);
        }

        // TODO: bring back tags
        $tags = [];
        /*if ($item['tags_individual'] && !empty($item['tags'])) {
            $tags = array_keys($item['tags']);
        }
        if (isset($auctionsData['tags'][$this->allegroApi->getAccount()->id]) && ! empty($auctionsData['tags'][$this->allegroApi->getAccount()->id])) {
            $tags = array_merge($tags, array_keys($auctionsData['tags'][$this->allegroApi->getAccount()->id]));
        }
        $tags = array_slice($tags, 0, XAllegroApi::TAG_AUCTION_LIMIT);*/

        // @todo move this logic outside of this controller
        try {
            $parametersProvider = new CategoriesParametersFactory($this->categoriesParametersProvider->getParameters($item['category_id']));
            $offerParameters = $parametersProvider->prepareParametersForAuction($item['category_parameters'], $item['category_ambiguous_parameters']);
            $productizationDescriptionImages = [];
            $productizationImages = [];
            $psImages = [];

            foreach ($imagesUploaded as $psImage) {
                $psImages[] = $psImage['url'];
            }

            if ($item['productization_mode'] == XAllegroAuction::PRODUCTIZATION_ASSIGN) {
                if ($item['allegro_product_trusted_content_images'] && $item['allegro_product_trusted_content_description']) {
                    $description = $item['allegro_product_description'];

                    $psImages = [];
                    foreach ($item['allegro_product_images'] as $catalogImage) {
                        $productizationImages[] = $catalogImage['url'];
                    }
                }
                else {
                    // If we don't want to use productization description, we need to remove images coming from it to avoid possible duplicates
                    $imagesFromCatalogToIgnore = [];
                    if ('prestashop' == XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION')) {
                        if (!empty($item['allegro_product_description'])) {
                            foreach ($item['allegro_product_description'] as $sections) {
                                foreach ($sections as $section) {
                                    if (empty($section->items)) {
                                        continue;
                                    }

                                    foreach ($section->items as $sectionItem) {
                                        if ('IMAGE' == $sectionItem->type && !empty($sectionItem->url)) {
                                            $imagesFromCatalogToIgnore[] = $sectionItem->url;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // If we want all data from the Allegro Catalog, and we have them, we need to reset images from the PrestaShop
                    if ('allegro' == XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION')
                        && !empty($item['allegro_product_description'])
                        && 'allegro' == XAllegroConfiguration::get('PRODUCTIZATION_IMAGES')
                        && !empty($item['allegro_product_images'])
                    ) {
                        $psImages = [];
                    }

                    if ('allegro' == XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION') && !empty($item['allegro_product_description'])) {
                        $description = $item['allegro_product_description'];

                        // TODO: refacto, duplication with ignored images
                        $descriptionImages = [];
                        foreach ($description as $sections) {
                            foreach ($sections as $section) {
                                if (empty($section->items)) {
                                    continue;
                                }

                                foreach ($section->items as $sectionItem) {
                                    if ($sectionItem->type == 'IMAGE' && !empty($sectionItem->url)) {
                                        $descriptionImages[] = $sectionItem->url;
                                    }
                                }
                            }
                        }

                        foreach ($descriptionImages as $url) {
                            $productizationDescriptionImages[] = $url;
                        }
                    }

                    if ('allegro' == XAllegroConfiguration::get('PRODUCTIZATION_IMAGES') && !empty($item['allegro_product_images'])
                        && empty($productizationDescriptionImages)
                    ) {
                        if (count($item['allegro_product_images']) == 1) {
                            $imagesFromCatalogToIgnore = [];
                        }
                        foreach ($item['allegro_product_images'] as $catalogImage) {
                            if (in_array($catalogImage['url'], $imagesFromCatalogToIgnore)) {
                                continue;
                            }
                            $productizationImages[] = $catalogImage['url'];
                        }
                    }

                    if ('merge' == XAllegroConfiguration::get('PRODUCTIZATION_IMAGES')
                        && !empty($item['allegro_product_images'])
                        && empty($productizationDescriptionImages)
                    ) {
                        if (1 == count($item['allegro_product_images'])) {
                            $imagesFromCatalogToIgnore = [];
                        }

                        foreach ($item['allegro_product_images'] as $catalogImage) {
                            if (in_array($catalogImage['url'], $imagesFromCatalogToIgnore)) {
                                continue;
                            }
                            $productizationImages[] = $catalogImage['url'];
                        }
                    }

                    // If description is coming from PrestaShop, we need to make sure that we have a place for them
                    if ('prestashop' == XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION')
                        || 'allegro' == XAllegroConfiguration::get('PRODUCTIZATION_DESCRIPTION') && empty($item['allegro_product_description'])
                    ) {
                        $productizationImages = array_slice($productizationImages, 0, XAllegroApi::PHOTO_COMPANY_MAX - count($psImages));
                    }
                }
            }

            /** @var OfferProduct $offer */
            $offer = (new JsonMapBuilder('OfferProduct'))->map(new OfferProduct());
            $offer = $this->prepareOfferBaseData($offer, $auctionsData, $description);

            // Due to the fact that Allegro can return us a lot of images, we need to make sure that we have a place for them
            $imagesFromCatalog = array_merge($productizationDescriptionImages, $productizationImages);
            $psImages = array_slice($psImages, 0, XAllegroApi::PHOTO_COMPANY_MAX - count($imagesFromCatalog));
            $images = array_slice(array_merge($psImages, $imagesFromCatalog), 0, XAllegroApi::PHOTO_COMPANY_MAX);

            // we need to split the parameters between those for the product, and those for the auction itself
            $parametersDescribingProduct = $parametersProvider->getProductParametersIDs();
            $parametersForProduct = [];

            foreach ($offerParameters as $key => $parameter) {
                if (in_array($parameter->id, $parametersDescribingProduct)) {
                    $parametersForProduct[] = $parameter;
                    unset($offerParameters[$key]);
                }
            }

            $offer->parameters = array_values($offerParameters);

            /** @var AllegroProduct $allegroProduct */
            $allegroProduct = (new JsonMapBuilder('Product'))->map(new AllegroProduct());
            $allegroProduct->id = $item['allegro_product_id'];
            $allegroProduct->name = $item['title'];
            $allegroProduct->category->id = $item['category_id'];
            $allegroProduct->parameters = $parametersForProduct;

            foreach ($images as $image) {
                $allegroProduct->image($image);
            }

            /** @var AllegroProductSet $allegroProductSet */
            $allegroProductSet = (new JsonMapBuilder('ProductSet'))->map(new AllegroProductSet());
            $allegroProductSet->product = $allegroProduct;

            //if ($item['category_gpsr']) {
                if ($item['responsible_producer']) {
                    $allegroProductSet->responsibleProducer->id = $item['responsible_producer'];
                }
                if ($item['responsible_person']) {
                    $allegroProductSet->responsiblePerson->id = $item['responsible_person'];
                }

                $allegroProductSet->safetyInformation->type = $item['safety_information_type'];

                if ($item['safety_information_type'] === SafetyInformationType::ATTACHMENTS) {
                    $attachmentsToUpload = [];

                    if (!empty($item['safety_information_attachment_x13gpsr']['checked'])) {
                        foreach (array_keys($item['safety_information_attachment_x13gpsr']['checked']) as $x13gpsrAttachmentKey) {
                            $attachmentsToUpload[] = [
                                'filePath' => $item['safety_information_attachment_x13gpsr']['file'][$x13gpsrAttachmentKey],
                                'fileName' => $item['safety_information_attachment_x13gpsr']['name'][$x13gpsrAttachmentKey],
                                'fileMimeType' => $item['safety_information_attachment_x13gpsr']['mimetype'][$x13gpsrAttachmentKey]
                            ];
                        }
                    }

                    foreach ($item['safety_information_attachment_product'] as $productAttachmentId) {
                        $productAttachment = new Attachment((int)$productAttachmentId, $this->allegroApi->getAccount()->id_language);

                        if (Validate::isLoadedObject($productAttachment)) {
                            $attachmentsToUpload[] = [
                                'filePath' => _PS_DOWNLOAD_DIR_ . $productAttachment->file,
                                'fileName' => $productAttachment->name,
                                'fileMimeType' => $productAttachment->mime
                            ];
                        }
                    }

                    if (!empty($item['safety_information_attachment_offer'])) {
                        foreach ($item['safety_information_attachment_offer']['file'] as $offerAttachmentId => $offerAttachment) {
                            $attachmentsToUpload[] = [
                                'filePath' => X13_ALLEGRO_ATTACHMENT_DIR . $offerAttachment,
                                'fileName' => $item['safety_information_attachment_offer']['name'][$offerAttachmentId],
                                'fileMimeType' => $item['safety_information_attachment_offer']['mimetype'][$offerAttachmentId],
                                'deleteAfter' => true
                            ];
                        }
                    }

                    $attachmentsToUpload = array_slice($attachmentsToUpload, 0, XAllegroApi::PRODUCT_SAFETY_ATTACHMENT_MAX);

                    try {
                        foreach ($attachmentsToUpload as $attachmentToUpload) {
                            $allegroProductSet->safetyInformation->attachment(
                                $this->uploadOfferAttachment(
                                    $attachmentToUpload['filePath'],
                                    $attachmentToUpload['fileName'],
                                    $attachmentToUpload['fileMimeType']
                                )
                            );
                        }
                    }
                    catch (ModuleException $ex) {
                        // exit;
                        $this->returnOfferErrorResponse([(string)$ex], $item['x_id'], $offerId, $item['title']);
                    }
                    catch (Exception $ex) {
                        // exit;
                        $this->returnOfferErrorResponse([$ex->getMessage()], $item['x_id'], $offerId, $item['title']);
                    }
                }
                else if ($item['safety_information_type'] === SafetyInformationType::TEXT) {
                    $allegroProductSet->safetyInformation->description = $item['safety_information_text'];
                }
            //}

            $offer->productSet($allegroProductSet);

            foreach ($images as $image) {
                $offer->image($image);
            }

            $offer->publication->status = 'ACTIVE';

            if ($auctionsData['starting_at']) {
                $offer->publication->startingAt = new DateTime($auctionsData['starting_at']);
            }

            if ($item['price_calculate_fees'] && (float)$item['price_buy_now'] != 0) {
                $promotion = new Promotion();

                foreach ($item['promotionPackages'] as $packageOptions) {
                    // extraPackages [checkbox]
                    if (is_array($packageOptions) && !empty($packageOptions)) {
                        foreach ($packageOptions as $packageId) {
                            $promotion->{$packageId} = true;
                        }
                    }
                    // basePackages [radio]
                    else if ($packageOptions) {
                        $promotion->{$packageOptions} = true;
                    }
                }

                $calculatedFees = (new OfferFeesProvider($this->allegroApi))->getOfferFees($offer, $promotion);
                $offer->sellingMode->price->amount += $calculatedFees;
            }

            $offerProcessManager = new OfferProcessManager();
            $resource = $this->allegroApi->sale()->productOffers();

            /** @var OfferProduct $result */
            $result = $resource->create($offer);
            $offerId = $result->id;

            //if (!empty($result->validation->errors)) {
            //    foreach ($result->validation->errors as $error) {
            //        $errors[] = ($error->userMessage ?: $error->message);
            //    }
            //}

            // exit;
            //if (!empty($errors)) {
            //    $this->returnOfferErrorResponse($errors, $item['x_id'], $offerId, $item['title']);
            //}

            $auctionObj = new XAllegroAuction();
            $auctionObj->id_xallegro_account = $this->allegroApi->getAccount()->id;
            $auctionObj->id_shop = $this->context->shop->id;
            $auctionObj->id_shop_group = $this->context->shop->id_shop_group;
            $auctionObj->id_auction = $offerId;
            $auctionObj->id_product = $item['id_product'];
            $auctionObj->id_product_attribute = $item['id_product_attribute'];
            $auctionObj->selling_mode = $item['selling_mode'];
            $auctionObj->quantity = $item['quantity'];
            $auctionObj->price_buy_now = $item['price_buy_now'] + $calculatedFees;
            $auctionObj->fees = $calculatedFees;
            $auctionObj->start_time = ($auctionsData['starting_at'] ? date('Y-m-d H:i:s', strtotime($auctionsData['starting_at'])) : '0000-00-00 00:00:00');
            $auctionObj->start = 1;
            $auctionObj->closed = 1;
            $auctionObj->auto_renew = (!is_numeric($item['auto_renew']) ? null : (int)$item['auto_renew']);
            $auctionObj->save();

            foreach ($item['marketplaces'] as $marketplaceId => $marketplaceItem) {
                if ($marketplaceId === $this->allegroApi->getAccount()->base_marketplace) {
                    continue;
                }

                $auctionObj->addAuctionMarketplace($marketplaceId, $marketplaceItem['price_buy_now']);
            }

            $operationId = (isset($resource->getHeaders()->location) ? basename($resource->getHeaders()->location) : null);

            if ($operationId && $resource->getCode() === 202) {
                $offerProcessManager->createProcess(
                    $this->allegroApi->getAccount()->id,
                    $offerId,
                    $operationId,
                    [ProcessOperation::STATUS_ACTIVE_CREATE]
                );
            }

            $this->log
                ->account($this->allegroApi->getAccount()->id)
                ->product($this->context->shop->id, $item['id_product'], $item['id_product_attribute'])
                ->offer($offerId)
                ->logDatabase()
                ->info(LogType::OFFER_CREATE(), ($operationId ? ['operationId' => $operationId] : null));

            $this->assignOfferPromotionPackages($offerId, $item);
        }
        catch (Exception $ex) {
            // exit;
            $this->returnOfferErrorResponse([(string)$ex], $item['x_id'], $offerId, $item['title']);
        }

        // delete manually uploaded safety information attachments
        /*if (isset($attachmentsToUpload)) {
            foreach ($attachmentsToUpload as $attachmentToUpload) {
                if (isset($attachmentToUpload['deleteAfter'])
                    && $attachmentToUpload['deleteAfter']
                    && file_exists($attachmentToUpload['filePath'])
                ) {
                    unlink($attachmentToUpload['filePath']);
                }
            }
        }*/

        // exit;
        $this->returnOfferSuccessResponse($item['x_id'], $offerId, $item['title']);
    }

    /**
     * @param array $item
     * @return XAllegroTemplate
     */
    private function prepareOfferTemplate(array $item)
    {
        $product = new Product($item['id_product'], true, $this->allegroApi->getAccount()->id_language, $this->context->shop->id);
        $xProduct = new XAllegroProduct(null, $product->id);
        $xTemplate = new XAllegroTemplate($item['id_template']);

        $xTemplate->setProduct($product, $xProduct);

        if ($item['id_product_attribute']) {
            $combination = $product->getAttributeCombinationsById($item['id_product_attribute'], $this->allegroApi->getAccount()->id_language);
            $xTemplate->setProductAttribute($combination);
        }

        $templateVariables = [
            'id_product' => $item['id_product'],
            'id_product_attribute' => $item['id_product_attribute'],
            'price_buy_now' => $item['price_buy_now'],
            'title' => $item['title'],
            'description' => $item['description']
        ];

        $xTemplate->prepareVariables($templateVariables, $product, $item['images'])->render();

        return $xTemplate;
    }

    /**
     * @param array $images
     * @param array|null $errors
     * @return array
     */
    private function uploadOfferImages(array $images, array &$errors = null)
    {
        $validImagesCount = 0;

        for ($uploadErrors = 0; $uploadErrors < 3; $uploadErrors++) {
            if ($validImagesCount < count($images)) {
                foreach ($images as &$image) {
                    if (isset($image['valid_upload']) && $image['valid_upload']) {
                        continue;
                    }

                    try {
                        /** @var Image $imageObject */
                        $imageObject = (new JsonMapBuilder('Image'))->map(new Image());
                        $imageObject->url = $image['url'];

                        $originalUrl = $image['url'];

                        if (XAllegroConfiguration::get('IMAGES_UPLOAD_TYPE') == 'BINARY') {
                            $imageUrl = $this->allegroApi->sale()->images()->uploadBinary($imageObject);
                        } else {
                            $imageUrl = $this->allegroApi->sale()->images()->upload($imageObject);
                        }

                        $image['url'] = $imageUrl->location;
                        $image['valid_upload'] = 1;
                        $validImagesCount++;

                        if (isset($errors[$originalUrl])) {
                            unset($errors[$originalUrl]);
                        }
                    }
                    catch (Exception $ex) {
                        $image['valid_upload'] = 0;
                        $errors[$originalUrl] = $ex . ' - ' . $originalUrl;
                    }
                }
                unset($image);

                if (!empty($errors)) {
                    $uploadErrors++;
                }
            }
        }

        return $images;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $fileMimeType
     * @return string
     * @throws ModuleException|RuntimeException
     */
    private function uploadOfferAttachment($filePath, $fileName, $fileMimeType)
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException(sprintf('Nie znaleziono pliku załącznika "%s"', $fileName));
        }

        /** @var AttachmentModel $attachment */
        $attachment = (new JsonMapBuilder('Attachment'))->map(new AttachmentModel());
        $attachment->type = AttachmentType::SAFETY_INFORMATION_MANUAL;
        $attachment->file->name = $fileName . '.' . XAllegroAttachment::getMimeTypeExtension($fileMimeType);

        $responseCreate = $this->allegroApi->sale()->attachments()->createAttachmentFile($attachment);
        $responseUpload = $this->allegroApi->sale()->attachments()->uploadAttachmentFile($responseCreate->id, file_get_contents($filePath), $fileMimeType);

        return $responseUpload->id;
    }

    /**
     * @param OfferProduct $offer
     * @param array $auctionsData
     * @param stdClass $description
     * @return OfferProduct
     */
    private function prepareOfferBaseData($offer, array $auctionsData, $description)
    {
        $item = &$auctionsData['item'];

        $offer->name = $item['title'];
        $offer->description = $description;
        $offer->category->id = $item['category_id'];
        $offer->publication->duration = $item['duration'];
        $offer->sellingMode->format = $item['selling_mode'];
        $offer->sellingMode->price->amount = $item['price_buy_now'];
        $offer->sellingMode->price->currency = $this->allegroApi->getCurrency()->iso_code;
        $offer->sellingMode->startingPrice->amount = $item['price_starting'];
        $offer->sellingMode->startingPrice->currency = $this->allegroApi->getCurrency()->iso_code;
        $offer->sellingMode->minimalPrice->amount = $item['price_minimal'];
        $offer->sellingMode->minimalPrice->currency = $this->allegroApi->getCurrency()->iso_code;
        $offer->stock->available = $item['quantity'];
        $offer->stock->unit = $item['unit'];
        $offer->delivery->handlingTime = $auctionsData['pas']['handling_time'];
        $offer->delivery->additionalInfo = $auctionsData['pas']['additional_info'];
        $offer->delivery->shippingRates->id = $auctionsData['pas']['shipping_rate'];
        $offer->delivery->shipmentDate = ($item['preorder'] ? new DateTime($item['preorder_date']) : null);
        $offer->location->countryCode = $auctionsData['pas']['country_code'];
        $offer->location->province = $auctionsData['pas']['province'];
        $offer->location->postCode = $auctionsData['pas']['post_code'];
        $offer->location->city = $auctionsData['pas']['city'];
        $offer->payments->invoice = $auctionsData['pas']['invoice'];
        $offer->afterSalesServices->warranty->id = $auctionsData['warranty'];
        $offer->afterSalesServices->impliedWarranty->id = $auctionsData['implied_warranty'];
        $offer->afterSalesServices->returnPolicy->id = $auctionsData['return_policy'];
        $offer->additionalServices->id = $auctionsData['additional_services'];
        $offer->messageToSellerSettings->mode = $auctionsData['message_to_seller'];
        $offer->b2b->buyableOnlyByBusiness = $auctionsData['offer_b2b_only'];

        foreach ($item['marketplaces'] as $marketplaceId => $marketplaceItem) {
            if ($marketplaceId === $this->allegroApi->getAccount()->base_marketplace) {
                continue;
            }

            $marketplacesProvider = new MarketplacesProvider($marketplaceId);

            /** @var AdditionalMarketplace $additionalMarketplace */
            $additionalMarketplace = (new JsonMapBuilder('AdditionalMarketplace'))->map(new AdditionalMarketplace());
            $additionalMarketplace->sellingMode->price->amount = $marketplaceItem['price_buy_now'];
            $additionalMarketplace->sellingMode->price->currency = $marketplacesProvider->getMarketplaceCurrency()->iso_code;

            $offer->additionalMarketplaces->{$marketplaceId} = $additionalMarketplace;

            if ($item['send_tax'] && $marketplaceItem['tax'] !== '') {
                $offer->taxSettings->addTaxRate($marketplaceItem['tax'], $marketplacesProvider->getMarketplaceCountry()->iso_code);
            }
        }

        if ($item['send_tax']) {
            if (isset($item['marketplaces'][$this->allegroApi->getAccount()->base_marketplace]['tax'])
                && $item['marketplaces'][$this->allegroApi->getAccount()->base_marketplace]['tax'] != ''
            ) {
                $offer->taxSettings->addTaxRate($item['marketplaces'][$this->allegroApi->getAccount()->base_marketplace]['tax'], $this->context->country->iso_code);
            }
        } else {
            $offer->taxSettings = null;
        }

        if ($item['size_table']) {
            $offer->sizeTable->id = $item['size_table'];
        } else {
            $offer->sizeTable = null;
        }

        if ($item['wholesale_price']) {
            $offer->discounts->wholesalePriceList->id = $item['wholesale_price'];
        } else {
            $offer->discounts = null;
        }

        $externalId = null;
        switch (XAllegroConfiguration::get('AUCTION_EXTERNAL')) {
            case XAllegroAuction::EXTERNAL_ID:
                $externalId = trim($item['id_product'] . ($item['id_product_attribute'] ? '_' . $item['id_product_attribute'] : ''));
                break;

            case XAllegroAuction::EXTERNAL_REFERENCE:
                $externalId = trim($item['reference']);
                break;

            case XAllegroAuction::EXTERNAL_EAN:
                $externalId = trim($item['ean']);
                break;

            case XAllegroAuction::EXTERNAL_UPC:
                $externalId = trim($item['upc']);
                break;

            case XAllegroAuction::EXTERNAL_ISBN:
                $externalId = trim($item['isbn']);
                break;

            case XAllegroAuction::EXTERNAL_MPN:
                $externalId = trim($item['mpn']);
                break;
        }

        if (!empty($externalId)) {
            $offer->external->id = $externalId;
        } else {
            $offer->external = null;
        }

        return $offer;
    }

    /**
     * @param $offerId
     * @param array $auctionsData
     * @return void
     */
    private function assignOfferPromotionPackages($offerId, array $auctionsData)
    {
        $promotionPackages = new PromotionPackages();

        foreach ($auctionsData['promotionPackages'] as $packageType => $packageOptions) {
            // extraPackages [checkbox]
            if (is_array($packageOptions) && !empty($packageOptions)) {
                foreach ($packageOptions as $packageId) {
                    $promotionPackages->addModification(PackageModificationType::CHANGE, $packageType, $packageId);
                }
            }
            // basePackages [radio]
            else if ($packageOptions) {
                $promotionPackages->addModification(PackageModificationType::CHANGE, $packageType, $packageOptions);
            }
        }

        if (!empty($promotionPackages->modifications)) {
            $this->allegroApi->sale()->promotionPackages()->modifyOfferPromotionPackages($offerId, $promotionPackages);

            // @todo add Log
        }
    }

    /**
     * @param array $errors
     * @param int $currentIndex
     * @param float $offerId
     * @param string $offerTitle
     * @return void
     */
    private function returnOfferErrorResponse(array $errors, $currentIndex, $offerId, $offerTitle)
    {
        $error_content = 'Wystapiły nastepujące błędy:<ul>';
        foreach ($errors as $error) {
            foreach (explode(';', $error) as $errorLine) {
                $error_content .= '<li>' . $errorLine . '</li>';
            }
        }
        $error_content .= '</ul>';

        $response[] = [
            'success' => false,
            'x_id' => $currentIndex,
            'id_auction' => $offerId,
            'message' => '<strong>' . $offerTitle . ': </strong>' . $error_content
        ];

        die(json_encode($response));
    }

    /**
     * @param $currentIndex
     * @param $offerId
     * @param $offerTitle
     * @return void
     */
    private function returnOfferSuccessResponse($currentIndex, $offerId, $offerTitle)
    {
        $url = XAllegroApi::generateOfferUrl($offerId, $this->allegroApi->getAccount()->sandbox);

        $response[] = [
            'success' => true,
            'x_id' => $currentIndex,
            'id_auction' => $offerId,
            'message' => '<strong>' . $offerTitle . ': </strong>&nbsp;<a href="' . $url . '" target="_blank" rel="nofollow">' . $url . '</a>'
        ];

        die(json_encode($response));
    }

    /**
     * @param array $categoryPath
     * @return string
     */
    private function formatCategoryPath(array $categoryPath)
    {
        return implode('/', array_map(function($value) {
            return "<span>$value</span>";
        }, $categoryPath));
    }
}
