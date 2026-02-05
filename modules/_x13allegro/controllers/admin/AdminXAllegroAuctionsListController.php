<?php

require_once (dirname(__FILE__) . '/../../x13allegro.php');

use x13allegro\Api\XAllegroApi;
use x13allegro\Api\XAllegroApiTools;
use x13allegro\Api\DataProvider\MarketplacesProvider;
use x13allegro\Api\DataProvider\OfferProvider;
use x13allegro\Api\DataUpdater\Updater;
use x13allegro\Api\DataUpdater\EntityUpdaterFinder;
use x13allegro\Api\Model\Marketplace\Enum\Marketplace;
use x13allegro\Api\Model\Marketplace\Enum\MarketplacePublicationStatus;
use x13allegro\Api\Model\Offers\OfferUpdate;
use x13allegro\Api\Model\Offers\Publication;
use x13allegro\Api\Model\Offers\Stock;
use x13allegro\Api\Model\Offers\Enum\PublicationStatus;
use x13allegro\Component\Logger\LogType;

final class AdminXAllegroAuctionsListController extends XAllegroController
{
    protected $allegroAutoLogin = true;
    protected $allegroAccountSwitch = true;

    public $multiple_fieldsets = true;

    /** @var XAllegroAuction */
    protected $object;

    protected $_default_pagination = 50;

    /** @var array */
    private $auctions = [];

    public function __construct()
    {
        $this->table = 'xallegro_auction';
        $this->identifier = 'id_xallegro_auction';
        $this->className = 'XAllegroAuction';
        $this->list_no_link = true;

        parent::__construct();

        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminXAllegroAuctionsList'));
        $this->tpl_folder = 'x_allegro_auctions/';

        $this->_conf[101] = $this->l('Usunięto powiązanie oferty.');
        $this->_conf[102] = $this->l('Utworzono powiązanie oferty z produktem.');
    }

    public function init()
    {
        // offer format
        if (Tools::getIsset('offerType') || !isset($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')})) {
            $this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} = Tools::getValue('offerType', 'buy_now');
        }

        // offer status
        if (Tools::getIsset('xallegroFilterStatus') || !isset($this->allegroCookie->{$this->getAllegroCookieFilter('offerStatus')})) {
            $this->allegroCookie->{$this->getAllegroCookieFilter('offerStatus')} = Tools::getValue('xallegroFilterStatus', 'active');
        }

        // offer marketplace
        if (Tools::getIsset('xallegroFilterMarketplace') || !isset($this->allegroCookie->{$this->getAllegroCookieFilter('offerMarketplace')})) {
            $this->allegroCookie->{$this->getAllegroCookieFilter('offerMarketplace')} = Tools::getValue('xallegroFilterMarketplace', 'all');
        }

        $_GET['offerType'] = $this->allegroCookie->{$this->getAllegroCookieFilter('offerType')};

        if ($this->tabAccess['edit'] === '1') {
            $this->bulk_actions['update'] = array(
                'text' => $this->l('Aktualizuj wybrane'),
                'icon' => 'icon-cogs bulkUpdate',
            );

            if ($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} === 'buy_now') {
                $this->bulk_actions['auto_renew'] = array(
                    'text' => $this->l('Ustaw auto wznawianie'),
                    'icon' => 'icon-cogs bulkAutoRenew',
                );

                $this->bulk_actions['redo'] = array(
                    'text' => $this->l('Wznów wybrane'),
                    'icon' => 'icon-repeat bulkRedo',
                );
            }

            $this->bulk_actions['finish'] = array(
                'text' => $this->l('Zakończ wybrane'),
                'icon' => 'icon-flag-checkered bulkFinish',
            );

            $this->bulk_actions['unbind'] = array(
                'text' => $this->l('Usuń powiązania'),
                'icon' => 'icon-unlink bulkUnbind'
            );
        }

        parent::init();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('autocomplete');
        $this->addJqueryUI('ui.sortable');

        $this->addJS($this->module->getPathUri() . 'views/js/tinymce/tinymce.min.js');
        $this->addJS($this->module->getPathUri() . 'views/js/tinymce/jquery.tinymce.min.js');
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        if ($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} === 'auction') {
            $title = 'Licytacje';
        }
        else {
            $title = 'Kup teraz';
        }

        if (!$this->display == 'edit' && method_exists($this, 'addMetaTitle')) {
            $this->addMetaTitle($title);
            $this->toolbar_title = $title;
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display))
        {
            $this->page_header_toolbar_btn['allegro_buy_now'] = array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . (Tools::getIsset('id_xallegro_account') ? '&id_xallegro_account=' . Tools::getValue('id_xallegro_account') : '') . '&offerType=buy_now',
                'desc' => $this->l('Kup teraz'),
                'icon' => 'process-icon-cart-arrow-down icon-cart-arrow-down',
                'class' => 'x-allegro_buy_now'
            );

            $this->page_header_toolbar_btn['allegro_auction'] = array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . (Tools::getIsset('id_xallegro_account') ? '&id_xallegro_account=' . Tools::getValue('id_xallegro_account') : '') . '&offerType=auction',
                'desc' => $this->l('Licytacje'),
                'icon' => 'process-icon-gavel icon-gavel',
                'class' => 'x-allegro_auction'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        parent::initProcess();

        if ((Tools::isSubmit('edit' . $this->table) || Tools::isSubmit('delete_link')) && Tools::getValue('id_auction')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->display = 'edit';
            } else {
                $this->errors[] = $this->l('Nie masz uprawnień do edycji w tym miejscu.');
            }
        }
    }

    public function renderList()
    {
        $this->fields_list = $this->getFieldsList('default');

        if ($this->tabAccess['edit'] === '1') {
            $this->addRowAction('xAuctionBind');
            $this->addRowAction('xAuctionUpdate');
            $this->addRowAction('xAuctionRedo');
        }

        $this->addRowAction('xAuctionUrl');

        if ($this->tabAccess['edit'] === '1') {
            $this->addRowAction('xAuctionEditBind');
            $this->addRowAction('xAuctionUnbind');
            $this->addRowAction('xAuctionEditProduct');
            $this->addRowAction('xAuctionFinish');
        }

        $helper = new HelperList();
        $this->setHelperDisplay($helper);

        //$helper->title = $this->toolbar_title;
        $helper->simple_header = false;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->orderBy = $this->context->cookie->xallegroauctionslistxallegro_auctionOrderby;
        $helper->orderWay = strtoupper($this->context->cookie->xallegroauctionslistxallegro_auctionOrderway);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&id_xallegro_account=' . $this->allegroApi->getAccount()->id . '&offerType=' . Tools::getValue('offerType');

        // override default action attribute
        $helper->tpl_vars['override_action'] = $helper->currentIndex;

        // filters
        $helper->tpl_vars['xallegroFilterStatus'] = $this->allegroCookie->{$this->getAllegroCookieFilter('offerStatus')};
        $helper->tpl_vars['xallegroFilterMarketplace'] = $this->allegroCookie->{$this->getAllegroCookieFilter('offerMarketplace')};
        $helper->tpl_vars['filterByProductization'] = (isset($this->context->cookie->submitFilterxallegro_productizationNeeded) && $this->context->cookie->submitFilterxallegro_productizationNeeded);
        $helper->tpl_vars['marketplaceFilters'] = Marketplace::toChoseList();

        $auctionFieldsList = $this->getFieldsList();
        $auctionFieldsListSettings = json_decode(XAllegroConfiguration::get('AUCTION_FIELDS_LIST_SETTINGS'), true);

        if (isset($auctionFieldsListSettings['default'])) {
            $auctionFieldsListSettingsMissing = array_diff(
                array_keys($auctionFieldsList),
                array_keys($auctionFieldsListSettings['default'])
            );

            foreach ($auctionFieldsListSettingsMissing as $field) {
                $auctionFieldsListSettings['default'] = $this->arrayInsertAfter(
                    $auctionFieldsListSettings['default'],
                    substr($field, 0, -3),
                    [$field => '0']
                );
            }
        }

        $helper->tpl_vars['auctionFieldsList'] = $auctionFieldsList;
        $helper->tpl_vars['auctionFieldsListSettings'] = $auctionFieldsListSettings;

        $this->getAuctionList();

        $helper->listTotal = $this->_listTotal;

        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }

        return $helper->generateList($this->_list, $this->fields_list);
    }

    public function renderForm()
    {
        if (!Validate::isLoadedObject($this->object) && (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP)) {
            $this->fields_form[]['form'] = [
                'legend' => [
                    'title' => $this->l('Powiąż ofertę z produktem')
                ],
                'warning' => $this->l('Wybierz konkretny kontekst sklepu aby powiązać ofertę z PrestaShop')
            ];
        }
        else {
            $this->fields_form[]['form'] = [
                'legend' => [
                    'title' => Validate::isLoadedObject($this->object) ? $this->l('Pogląd powiązania oferty z produktem') : $this->l('Powiąż ofertę z produktem')
                ],
                'description' => ((Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) || $this->context->shop->id !== $this->object->id_shop
                    ? $this->l('Oferta powiązana z produktem w sklepie') . ': ' . (new Shop($this->object->id_shop))->name
                    : null
                ),
                'submit' => [
                    'title' => $this->l('Zapisz'),
                    'class' => (Validate::isLoadedObject($this->object) ? 'hidden' : 'btn btn-default pull-right')
                ],
                'input' => [
                    ['type' => 'hidden', 'name' => 'id_auction'],
                    ['type' => 'hidden', 'name' => 'closed'],
                    ['type' => 'hidden', 'name' => 'closedDb'],
                    ['type' => 'hidden', 'name' => 'start'],
                    ['type' => 'hidden', 'name' => 'startDb'],
                    ['type' => 'hidden', 'name' => 'id_xallegro_account'],
                    ['type' => 'hidden', 'name' => 'id_product'],
                    ['type' => 'hidden', 'name' => 'id_shop'],
                    ['type' => 'hidden', 'name' => 'offerType'],
                    [
                        'type' => 'text',
                        'label' => $this->l('Nazwa produktu'),
                        'name' => 'name',
                        'size' => 70,
                        'class' => 'custom_ac_input',
                        'desc' => (Validate::isLoadedObject($this->object) ? false : $this->l('Zacznij wpisywać pierwsze litery nazwy produktu, kodu referencyjnego lub jego ID, następnie wybierz produkt z listy rozwijalnej')),
                        'disabled' => (Validate::isLoadedObject($this->object) ? true : null)
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Nazwa atrybutu'),
                        'name' => 'id_product_attribute',
                        'options' => [
                            'query' => [
                                ['id' => 0, 'name' => (Validate::isLoadedObject($this->object) ? $this->object->name_attribute : 'Brak')]
                            ],
                            'id' => 'id',
                            'name' => 'name'
                        ],
                        'disabled' => (Validate::isLoadedObject($this->object) ? true : null)
                    ]
                ],
                'buttons' => [
                    [
                        'href' => $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&offerType=' . Tools::getValue('offerType'),
                        'title' => $this->l('Wróć'),
                        'class' => 'pull-left',
                        'icon' => 'process-icon-back'
                    ]
                ]
            ];
        }

        $this->fields_form[]['form'] = [
            'legend' => [
                'title' => $this->l('Informacje o ofercie pobrane z Allegro')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Tytuł oferty'),
                    'name' => 'title',
                    'size' => 70,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Cena Kup Teraz'),
                    'name' => 'price_buy_now',
                    'size' => 10,
                    'class' => 'fixed-width-sm',
                    'disabled' => true,
                    'suffix' => ' zł',
                    'callback' => 'priceFormat',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Pozostała ilość przedmiotów'),
                    'name' => 'quantity',
                    'size' => 10,
                    'class' => 'fixed-width-sm',
                    'disabled' => true
                ],
                [
                    'type' => $this->bootstrap ? 'switch' : 'radio',
                    'label' => $this->l('Zaplanowana'),
                    'name' => 'start',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'start_on', 'value' => 1, 'label' => $this->l('Tak')],
                        ['id' => 'start_off', 'value' => 0, 'label' => $this->l('Nie')]
                    ],
                    'disabled' => true
                ],
                [
                    'type' => $this->bootstrap ? 'switch' : 'radio',
                    'label' => $this->l('Zakończona'),
                    'name' => 'closed',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'closed_on', 'value' => 1, 'label' => $this->l('Tak')],
                        ['id' => 'closed_off', 'value' => 0, 'label' => $this->l('Nie')]
                    ],
                    'disabled' => true
                ]
            ]
        ];

        if (Validate::isLoadedObject($this->object)) {
            $this->fields_form[]['form'] = [
                'legend' => [
                    'title' => $this->l('Informacje o ofercie przechowywane przez moduł')
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Cena Kup Teraz'),
                        'name' => 'priceBuyNowDb',
                        'size' => 10,
                        'class' => 'fixed-width-sm',
                        'disabled' => true,
                        'suffix' => ' zł',
                        'callback' => 'priceFormat',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Pozostała ilość przedmiotów'),
                        'name' => 'quantityDb',
                        'size' => 10,
                        'class' => 'fixed-width-sm',
                        'disabled' => true
                    ],
                    [
                        'type' => $this->bootstrap ? 'switch' : 'radio',
                        'label' => $this->l('Zaplanowana'),
                        'name' => 'startDb',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'start_on', 'value' => 1, 'label' => $this->l('Tak')],
                            ['id' => 'start_off', 'value' => 0, 'label' => $this->l('Nie')]
                        ],
                        'disabled' => true
                    ],
                    [
                        'type' => $this->bootstrap ? 'switch' : 'radio',
                        'label' => $this->l('Zakończona'),
                        'name' => 'closedDb',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'closed_on', 'value' => 1, 'label' => $this->l('Tak')],
                            ['id' => 'closed_off', 'value' => 0, 'label' => $this->l('Nie')]
                        ],
                        'disabled' => true,
                        'auctionDbInfo' => (Validate::isLoadedObject($this->object) && $this->object->closedDb && !$this->object->startDb
                            ? 'Ta oferta została zamknięta w bazie danych.<br>'
                                . 'Jeśli nie zgadza się to ze stanem faktycznym, należy wymusić stan oferty.<br>'
                                . 'Możesz to zrobić <a href="' . $this->context->link->getAdminLink('AdminXAllegroConfiguration') . '#xallegro_configuration_fieldset_cron" target="_blank">TUTAJ</a>, klikając na przycisk "Wymuś stan ofert według Allegro".'
                            : null)
                    ],
                    [
                        'type' => $this->bootstrap ? 'switch' : 'radio',
                        'label' => $this->l('Zarchiwizowana'),
                        'name' => 'archived',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'archived_on', 'value' => 1, 'label' => $this->l('Tak')],
                            ['id' => 'archived_off', 'value' => 0, 'label' => $this->l('Nie')]
                        ],
                        'disabled' => true,
                        'auctionDbInfo' => (Validate::isLoadedObject($this->object) && $this->object->archived
                            ? 'Ta oferta została zarchiwizowana w bazie danych, dnia <strong>' . (new DateTime($this->object->archived_date))->format('d.m.Y H:i') . '</strong>.<br>'
                                . 'Jeśli nie zgadza się to ze stanem faktycznym, należy wymusić stan oferty.<br>'
                                . 'Możesz to zrobić <a href="' . $this->context->link->getAdminLink('AdminXAllegroConfiguration') . '#xallegro_configuration_fieldset_cron" target="_blank">TUTAJ</a>, klikając na przycisk "Wymuś stan ofert według Allegro".'
                            : null)
                    ]
                ]
            ];
        }

        $this->show_form_cancel_button = false;

        return parent::renderForm();
    }

    /**
     * @param bool|false $opt
     * @return XAllegroAuction|false
     */
    protected function loadObject($opt = false)
    {
        if (!$this->allegroApi instanceof XAllegroApi) {
            $this->object = null;
            return false;
        }

        if (Validate::isLoadedObject($this->object)) {
            return $this->object;
        }

        $this->object = (new PrestaShopCollection(XAllegroAuction::class))
            ->where('id_auction', '=', Tools::getValue('id_auction'))
            ->getFirst();

        if ($this->object) {
            $productObj = new Product($this->object->id_product, true, $this->allegroApi->getAccount()->id_language, $this->object->id_shop);
            $productObjAttr = $productObj->getAttributeCombinationsById($this->object->id_product_attribute, $this->allegroApi->getAccount()->id_language);

            $this->object->name = Product::getProductName($this->object->id_product, $this->object->id_product_attribute, $this->allegroApi->getAccount()->id_language);
            $this->object->name_attribute = (!empty($productObjAttr) ? $productObjAttr[0]['group_name'] . ' - ' . $productObjAttr[0]['attribute_name'] : '');
            $this->object->priceBuyNowDb = $this->object->price_buy_now;
            $this->object->quantityDb = (int)$this->object->quantity;
            $this->object->closedDb = (int)$this->object->closed;
            $this->object->startDb = (int)$this->object->start;

            if (!Validate::isLoadedObject($productObj)) {
                $this->errors[] = $this->l('Powiązanie odnosi się do nieistniejącego produktu.');
            }
        }
        else {
            $this->object = new XAllegroAuction();
            $this->object->id_xallegro_account = $this->allegroApi->getAccount()->id;
            $this->object->id_auction = Tools::getValue('id_auction');
            $this->object->id_shop = $this->context->shop->id;
            $this->object->id_shop_group = $this->context->shop->id_shop_group;
            $this->object->priceBuyNowDb = '0.00';
            $this->object->quantityDb = 0;
            $this->object->closedDb = 0;
            $this->object->startDb = 0;
        }

        try {
            $offer = (new OfferProvider($this->allegroApi, true))->getOfferProductDetails($this->object->id_auction);
            $priceBuyNow = ($offer->sellingMode->price ? $offer->sellingMode->price->amount : 0);

            $this->object->title = $offer->name;
            $this->object->price_buy_now = number_format($priceBuyNow, 2, '.', '');
            $this->object->quantity = $offer->stock->available;
            $this->object->closed = (in_array($offer->publication->status, [PublicationStatus::INACTIVE, PublicationStatus::ENDED]) ? 1 : 0);
            $this->object->start = (in_array($offer->publication->status, [PublicationStatus::INACTIVE, PublicationStatus::ACTIVATING]) ? 1 : 0);

            $marketplaceProvider = new MarketplacesProvider($offer->publication->marketplaces->base->id);
            $marketplaces[] = [
                'id' => $offer->publication->marketplaces->base->id,
                'name' => $marketplaceProvider->getMarketplaceName(),
                'offerUrl' => $marketplaceProvider->getMarketplaceOfferUrl($offer->id, $this->allegroApi->getAccount()->sandbox)
            ];

            foreach ($offer->publication->marketplaces->additional as $marketplace) {
                if (!Marketplace::isValid($marketplace->id)) {
                    continue;
                }

                $marketplaceProvider = new MarketplacesProvider($marketplace->id);
                $marketplaces[] = [
                    'id' => $marketplace->id,
                    'name' => $marketplaceProvider->getMarketplaceName(),
                    'offerUrl' => $marketplaceProvider->getMarketplaceOfferUrl($offer->id, $this->allegroApi->getAccount()->sandbox)
                ];
            }

            // @todo fix when refactoring offer association preview
            $this->tpl_form_vars['offerMarketplaces'] = $marketplaces;
        }
        catch (Exception $ex) {
            $this->errors[] = (string)$ex;
            $this->object = null;
            return false;
        }

        $this->object->offerType = Tools::getValue('offerType');

        return $this->object;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('delete_link') && $this->tabAccess['edit'] === '1') {
            XAllegroAuction::deleteAuctions([Tools::getValue('id_auction')]);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&conf=101' . '&offerType=' . Tools::getValue('offerType'));
        }
        else if (Tools::getValue('action')) {
            $method = 'process' . Tools::toCamelCase(Tools::getValue('action'), true);
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return parent::postProcess();
    }

    public function processSave()
    {
        $this->loadObject();

        // jesli relacja istnieje w bazie, pomijamy
        if (Validate::isLoadedObject($this->object)) {
            return false;
        }

        $this->validateRules('XAllegroAuction');

        if (!Tools::getValue('id_product')) {
            $this->errors[] = $this->l('Brak wybranego produktu do powiązania');
        }

        if (!empty($this->errors)) {
            $this->display = 'edit';
            return false;
        }

        // at this point all the Offer's data should be loaded by loadObject() method
        $offer = (new OfferProvider($this->allegroApi))->getOfferProductDetails($this->object->id_auction);

        if ($offer->publication->status == PublicationStatus::ENDED) {
            $offerList = $this->allegroApi->sale()->offers()->getList(
                ['offer.id' => $this->object->id_auction]
            );

            if (isset($offerList->offers[0])) {
                $endDate = (new \DateTime($offerList->offers[0]->publication->endedAt))
                    ->setTimezone(new \DateTimeZone(date_default_timezone_get()))
                    ->format('Y-m-d H:i:s');
            }
        }

        $this->object->id_product = (int)Tools::getValue('id_product');
        $this->object->id_product_attribute = (int)Tools::getValue('id_product_attribute');
        $this->object->id_shop = $this->context->shop->id;
        $this->object->id_shop_group = $this->context->shop->id_shop_group;
        $this->object->selling_mode = strtoupper(Tools::getValue('offerType'));
        $this->object->start = (int)Tools::getValue('start');
        $this->object->closed = (int)Tools::getValue('closed');
        $this->object->end_date = (isset($endDate) ? $endDate : null);
        $this->object->add();

        foreach ($offer->publication->marketplaces->additional as $marketplace) {
            $marketplacePriceBuyNow = '0.00';
            if (is_object($offer->additionalMarketplaces->{$marketplace->id}->sellingMode)
                && is_object($offer->additionalMarketplaces->{$marketplace->id}->sellingMode->price)
            ) {
                $marketplacePriceBuyNow = $offer->additionalMarketplaces->{$marketplace->id}->sellingMode->price->amount;
            }

            $this->object->addAuctionMarketplace($marketplace->id, $marketplacePriceBuyNow);
        }

        $this->redirect_after = $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&conf=102&offerType=' . Tools::getValue('offerType');

        return $this->object;
    }

    public function processBulkUnbind()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            XAllegroAuction::deleteAuctions($this->boxes);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&conf=101&offerType=' . Tools::getValue('offerType'));
        }
    }

    public function displayXAuctionUrlLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $row = $this->findElementByKeyValue($this->_list, 'id_auction', $ids[0]);
        $linkHTML = [];

        foreach ($row['marketplaces'] as $marketplaceId => $marketplace) {
            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_url.tpl');
            $tpl->assign([
                'href' => (new MarketplacesProvider($marketplaceId))->getMarketplaceOfferUrl($row['id_auction'], $this->allegroApi->getAccount()->sandbox),
                'title' => $this->l('Zobacz na Allegro') . ' ' . $marketplace['name'],
                'action' => $marketplace['name']
            ]);

            $linkHTML[] = $tpl->fetch();
        }

        return implode('<br>', $linkHTML);
    }

    public function displayXAuctionBindLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $row = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);

        if ($row) {
            return null;
        }

        $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_bind.tpl');
        $tpl->assign('href', $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&edit' . $this->table . '&id_xallegro_account=' . $ids[1] . '&id_auction=' . $ids[0] . '&offerType=' . Tools::getValue('offerType'));

        $tpl->assign(array(
            'title' => $this->l('Powiąż ofertę z produktem'),
            'action' => $this->l('Powiąż'),
            'icon' => 'icon-link',
            'img' => 'themes/default/img/tree-multishop-url.png'
        ));

        return $tpl->fetch();
    }

    public function displayXAuctionEditBindLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $row = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);

        if (!$row) {
            return null;
        }

        $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_bind.tpl');
        $tpl->assign('href', $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&edit' . $this->table . '&id_xallegro_account=' . $ids[1] . '&id_auction=' . $ids[0] . '&offerType=' . Tools::getValue('offerType'));

        $tpl->assign(array(
            'title' => $this->l('Pogląd powiązania oferty z produktem'),
            'action' => $this->l('Zobacz powiązanie'),
            'icon' => 'icon-search',
            'img' => '../img/admin/subdomain.gif'
        ));

        return $tpl->fetch();
    }

    public function displayXAuctionUnbindLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $rowAuctions = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);
        $rowList = $this->findElementByKeyValue($this->_list, 'id_auction', current($ids));

        if ($rowAuctions && $rowList['binded']) {
            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_unbind.tpl');
            $tpl->assign(array(
                'href' => $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&id_auction=' . $rowList['id_auction'] . '&delete_link' . '&offerType=' . Tools::getValue('offerType'),
                'title' => $this->l('Usuń powiązanie produktu'),
                'action' => $this->l('Usuń powiązanie'),
                'data_id' => $rowList['id_auction'],
                'data_title' => htmlspecialchars($rowList['name'])
            ));

            return $tpl->fetch();
        }

        return null;
    }

    public function displayXAuctionEditProductLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $rowAuctions = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);
        $rowList = $this->findElementByKeyValue($this->_list, 'id_auction', current($ids));

        if ($rowAuctions && $rowList['binded']) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $href = $this->context->link->getAdminLink('AdminProducts').'&updateproduct&id_product='.$rowList['id_product'];
            } else if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
                $href = $this->context->link->getAdminLink('AdminProducts', true, ['route' => 'admin_product_form', 'id' => $rowList['id_product']]);
            } else {
                $href = $this->context->link->getAdminLink('AdminProducts', true, ['id_product' => $rowList['id_product']]);
            }

            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_edit_product.tpl');
            $tpl->assign(array(
                'href' => $href,
                'title' => $this->l('Edytuj produkt'),
                'action' => $this->l('Edytuj produkt'),
            ));

            return $tpl->fetch();
        }

        return null;
    }

    public function displayXAuctionFinishLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $rowAuctions = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);
        $rowList = $this->findElementByKeyValue($this->_list, 'id_auction', current($ids));

        if ($rowAuctions
            && $rowList['status'] == PublicationStatus::ACTIVE
            && !$rowAuctions['start']
            && !$rowAuctions['closed']
            && !$rowAuctions['archived']
        ) {
            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_finish.tpl');
            $tpl->assign(array(
                'href' => '#finish',
                'title' => $this->l('Zakończ ofertę'),
                'action' => $this->l('Zakończ'),
                'data_id' => $rowList['id_auction'],
                'data_title' =>  htmlspecialchars($rowList['name'])
            ));

            return $tpl->fetch();
        }

        return null;
    }

    public function displayXAuctionRedoLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $rowAuctions = $this->findElementByKeyValue($this->auctions, 'id_auction', $ids[0]);
        $rowList = $this->findElementByKeyValue($this->_list, 'id_auction', current($ids));

        if ($rowAuctions
            && ($rowList['status'] == PublicationStatus::ENDED || $rowAuctions['closed'])
            && !$rowAuctions['start']
            && !$rowAuctions['archived']
            && $this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} === 'buy_now'
        ) {
            $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_redo.tpl');
            $tpl->assign(array(
                'href' => '#redo',
                'title' => $this->l('Wznów ponownie wybraną ofertę'),
                'action' => $this->l('Wznów'),
                'data_id' => $rowList['id_auction'],
                'data_title' =>  htmlspecialchars($rowList['name'])
            ));

            return $tpl->fetch();
        }

        return null;
    }

    public function displayXAuctionUpdateLink($token = null, $id, $name = null)
    {
        $ids = explode('|', $id);
        $rowList = $this->findElementByKeyValue($this->_list, 'id_auction', current($ids));

        $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/action_auction_update.tpl');
        $tpl->assign(array(
            'href' => '#update',
            'title' => $this->l('Aktualizuj ofertę'),
            'action' => $this->l('Aktualizuj'),
            'data_id' => $rowList['id_auction'],
            'data_title' =>  htmlspecialchars($rowList['name'])
        ));

        return $tpl->fetch();
    }

    private function getAuctionList()
    {
        $offerFilters = array();
        $offerStatus = ($this->allegroCookie->{$this->getAllegroCookieFilter('offerStatus')} === 'all'
            ? 'inactive,active,activating,ended'
            : $this->allegroCookie->{$this->getAllegroCookieFilter('offerStatus')});

        if ($this->allegroCookie->{$this->getAllegroCookieFilter('offerMarketplace')} !== 'all') {
            $offerFilters['publication.marketplace'] = $this->allegroCookie->{$this->getAllegroCookieFilter('offerMarketplace')};
        }

        if (isset($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_id_auction) && !empty($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_id_auction)) {
            $offerFilters['offer.id'] = trim($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_id_auction);
        }

        if (isset($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_name) && !empty($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_name)) {
            $offerFilters['name'] = urlencode($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_name);
        }

        if (isset($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_external) && !empty($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_external)) {
            $offerFilters['external.id'] = urlencode($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_external);
        }

        $filterReference = false;
        if (isset($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_reference) && !empty($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_reference)) {
            $filterReference = $this->context->cookie->xallegroauctionslistxallegro_auctionFilter_reference;
        }

        $filterEan13 = false;
        if (isset($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_ean13) && !empty($this->context->cookie->xallegroauctionslistxallegro_auctionFilter_ean13)) {
            $filterEan13 = $this->context->cookie->xallegroauctionslistxallegro_auctionFilter_ean13;
        }

        switch ($this->context->cookie->xallegroauctionslistxallegro_auctionOrderby) {
            case 'quantity':
                $sort = 'stock.available';
                break;

            case 'sold':
                $sort = 'stock.sold';
                break;

            case 'price':
                $sort = 'sellingMode.price.amount';
                break;

            default:
                $sort = false;
        }

        if ($sort) {
            if ($this->context->cookie->xallegroauctionslistxallegro_auctionOrderway == 'desc') {
                $sort = '-' . $sort;
            }

            $offerFilters['sort'] = $sort;
        }

        // pagination offset
        if (Tools::getIsset('xallegro_auction_pagination') || !isset($this->context->cookie->xallegro_auction_pagination) || !$this->context->cookie->xallegro_auction_pagination) {
            $this->context->cookie->xallegro_auction_pagination = (int)Tools::getValue('xallegro_auction_pagination', $this->_default_pagination);
        }

        // check for max_input_vars
        // finish, redo & update needs 2*offer inputs for ID & name variables (+ some standard vars)
        $maxInputVars = (int)ini_get('max_input_vars');
        if ($maxInputVars <= ($this->context->cookie->xallegro_auction_pagination * 2) -10) {
            $this->warnings[] = $this->module->renderAdminMessage(sprintf($this->l('Uwaga! Twoja maksymalna liczba pól w formularzu (max_input_vars) %s może uniemożliwić poprawną obsługę listy ofert.'), '<b>' . $maxInputVars . '</b>'));
        }

        // pagination page
        if (Tools::getIsset('submitFilterxallegro_auction') || !isset($this->context->cookie->submitFilterxallegro_auction) || !$this->context->cookie->submitFilterxallegro_auction) {
            $this->context->cookie->submitFilterxallegro_auction = max((int)Tools::getValue('submitFilterxallegro_auction', 1), 1);
        }

        // products where productization is needed and product is not mapped with Allegro Catalog
        if (Tools::getValue('filterByProductization')) {
            $this->context->cookie->submitFilterxallegro_productizationNeeded = 1;
        }

        if (Tools::getValue('resetFilterByProductization')) {
            $this->context->cookie->submitFilterxallegro_productizationNeeded = 0;
        }

        if (isset($this->context->cookie->submitFilterxallegro_productizationNeeded) && $this->context->cookie->submitFilterxallegro_productizationNeeded) {
            $offerFilters['productizationRequired'] = 'true';
            $offerFilters['product.id.empty'] = 'true';
        }

        try {
            $result = $this->allegroApi->sale()->offers()->getList(
                array_merge($offerFilters, array(
                    'publication.status' => strtoupper($offerStatus),
                    'sellingMode.format' => strtoupper($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')})
                )),
                $this->context->cookie->xallegro_auction_pagination,
                ($this->context->cookie->submitFilterxallegro_auction - 1) * $this->context->cookie->xallegro_auction_pagination
            );

            // @todo backport for PHP 5, change this to array_column()
            $offersIds = array_map(function ($object) {
                return $object->id;
            }, $result->offers);

            $this->auctions = XAllegroAuction::getAuctionAssociationsForList($offersIds);
            $accountConfiguration = new XAllegroConfigurationAccount($this->allegroApi->getAccount()->id);

            foreach ($result->offers as $offer) {
                $priceBuyNow =
                $priceStarting =
                $priceMinimal =
                $priceCurrent = 0;
                $priceShop = null;

                $start =
                $end = false;

                $marketplaces = [];

                if ($offer->sellingMode) {
                    if (is_object($offer->sellingMode->price)) {
                        $priceBuyNow = (float)$offer->sellingMode->price->amount;
                    }

                    if (is_object($offer->sellingMode->startingPrice)) {
                        $priceStarting = (float)$offer->sellingMode->startingPrice->amount;
                    }

                    if (is_object($offer->sellingMode->minimalPrice)) {
                        $priceMinimal = (float)$offer->sellingMode->minimalPrice->amount;
                    }
                }

                if (is_object($offer->saleInfo->currentPrice)) {
                    $priceCurrent = (float)$offer->saleInfo->currentPrice->amount;
                }

                if ($offer->publication->startedAt) {
                    $start = $offer->publication->startedAt;
                }
                else if ($offer->publication->startingAt) {
                    $start = $offer->publication->startingAt;
                }

                if ($offer->publication->endedAt) {
                    $end = $offer->publication->endedAt;
                }
                else if ($offer->publication->endingAt) {
                    $end = $offer->publication->endingAt;
                }

                $binded = $this->findElementByKeyValue($this->auctions, 'id_auction', $offer->id);
                $bindedDetails = [];

                if ($binded) {
                    $bindedDetails = [
                        'current_context' => (!Shop::isFeatureActive() || Shop::getContext() === Shop::CONTEXT_SHOP ? $this->context->shop->id : null),
                        'id_shop' => $binded['id_shop'],
                        'shop_name' => $binded['shop_name']
                    ];

                    $productCustom = (new XAllegroProductCustom($this->allegroApi->getAccount()->id, $binded['id_product']))
                        ->useGlobalAccountsSettings(true)
                        ->setProductAttributeId($binded['id_product_attribute'])
                        ->setOriginalProductPrice(
                            XAllegroProduct::convertPrice(
                                XAllegroProduct::getProductStaticPrice(
                                    $binded['id_product'],
                                    $binded['id_product_attribute'],
                                    $accountConfiguration->get('AUCTION_PRICE_CUSTOMER_GROUP', true),
                                    $this->context
                                ),
                                $this->context->currency,
                                $this->allegroApi->getCurrency()
                            )
                        )
                        ->calculatePrice();

                    $priceShop = XAllegroProduct::calculatePrice($productCustom, $accountConfiguration, $this->context->currency);
                }

                $marketplaceProvider = new MarketplacesProvider($offer->publication->marketplaces->base->id);
                $marketplaceCurrency = $marketplaceProvider->getMarketplaceCurrency();

                $marketplaces[$offer->publication->marketplaces->base->id] = [
                    'name' => $marketplaceProvider->getMarketplaceName(),
                    'currencySign' => $marketplaceCurrency->sign,
                    'currencyIso' => $marketplaceCurrency->iso_code,
                    'priceBuyNow' => XAllegroProduct::formatPrice($priceBuyNow, $marketplaceCurrency),
                    'sold' => $offer->stock->sold,
                    'visits' => $offer->stats->visitsCount,
                    'status' => $offer->publication->status,
                    'statusTranslated' => $this->formatOfferStatus($offer->publication->status),
                    'statusDetails' => []
                ];

                // prepare marketplaces array in order by Marketplace Enum
                foreach (Marketplace::toArray() as $marketplace) {
                    if ($marketplace !== $offer->publication->marketplaces->base->id) {
                        $marketplaces[$marketplace] = null;
                    }
                }

                foreach ($offer->publication->marketplaces->additional as $marketplace) {
                    if (!Marketplace::isValid($marketplace->id)) {
                        continue;
                    }

                    $marketplaceProvider = new MarketplacesProvider($marketplace->id);
                    $marketplaceCurrency = $marketplaceProvider->getMarketplaceCurrency();
                    $offerMarketplace = $offer->additionalMarketplaces->{$marketplace->id};
                    $offerMarketplacePrice = null;

                    if (is_object($offerMarketplace->sellingMode) && is_object($offerMarketplace->sellingMode->price)) {
                        $offerMarketplacePrice = XAllegroProduct::formatPrice($offerMarketplace->sellingMode->price->amount, $marketplaceCurrency);
                    }

                    $statusDetails = [];
                    if ($binded
                        && isset($binded['marketplace'][$marketplace->id])
                        && $binded['marketplace'][$marketplace->id]['last_status']
                    ) {
                        $marketplaceStatus = $binded['marketplace'][$marketplace->id];
                        $statusDetails = [
                            'status' => MarketplacePublicationStatus::from($marketplaceStatus['last_status'])->getValueTranslated(),
                            'statusDate' => ($marketplaceStatus['last_status_date'] ? (new DateTime($marketplaceStatus['last_status_date']))->format('d.m.Y H:i') : null),
                            'statusRefusalReasons' => $marketplaceStatus['last_status_refusal_reasons']
                        ];
                    }

                    $marketplaces[$marketplace->id] = [
                        'name' => $marketplaceProvider->getMarketplaceName(),
                        'currencySign' => $marketplaceCurrency->sign,
                        'currencyIso' => $marketplaceCurrency->iso_code,
                        'priceBuyNow' => $offerMarketplacePrice,
                        'sold' => $offerMarketplace->stock->sold,
                        'visits' => $offerMarketplace->stats->visitsCount,
                        'status' => $offerMarketplace->publication->state,
                        'statusTranslated' => MarketplacePublicationStatus::from($offerMarketplace->publication->state)->getValueTranslated(),
                        'statusDetails' => $statusDetails
                    ];
                }

                // clear empty marketplaces
                $marketplaces = array_filter($marketplaces);

                $this->_list[] = array(
                    'id_xallegro_auction' => (float)$offer->id . '|' .  $this->allegroApi->getAccount()->id,
                    'image' => ($offer->primaryImage->url ? str_replace('original', 's64', $offer->primaryImage->url) : null),
                    'image_large' => ($offer->primaryImage->url ? str_replace('original', 's192', $offer->primaryImage->url) : null),
                    'id_auction' => (float)$offer->id,
                    'name' => $offer->name,
                    'external' => (is_object($offer->external) ? $offer->external->id : ''),
                    'quantity' => (int)$offer->stock->available,
                    'id_currency' => $this->allegroApi->getCurrency()->id,
                    'price_shop' => $priceShop,
                    'price' => $priceBuyNow,
                    'price_pl' => $marketplaces[XAllegroApi::MARKETPLACE_PL]['priceBuyNow'] . ' ' . $marketplaces[XAllegroApi::MARKETPLACE_PL]['currencySign'],
                    'price_cz' => (isset($marketplaces[XAllegroApi::MARKETPLACE_CZ]) ? $marketplaces[XAllegroApi::MARKETPLACE_CZ]['priceBuyNow'] . ' ' . $marketplaces[XAllegroApi::MARKETPLACE_CZ]['currencySign'] : null),
                    'price_sk' => (isset($marketplaces[XAllegroApi::MARKETPLACE_SK]) ? $marketplaces[XAllegroApi::MARKETPLACE_SK]['priceBuyNow'] . ' ' . $marketplaces[XAllegroApi::MARKETPLACE_SK]['currencySign'] : null),
                    'price_hu' => (isset($marketplaces[XAllegroApi::MARKETPLACE_HU]) ? $marketplaces[XAllegroApi::MARKETPLACE_HU]['priceBuyNow'] . ' ' . $marketplaces[XAllegroApi::MARKETPLACE_HU]['currencySign'] : null),
                    'price_starting' => $priceStarting,
                    'price_minimal' => $priceMinimal,
                    'price_current' => $priceCurrent,
                    'offers' => (int)$offer->saleInfo->biddersCount,
                    'sold_pl' => $marketplaces[XAllegroApi::MARKETPLACE_PL]['sold'],
                    'sold_cz' => (isset($marketplaces[XAllegroApi::MARKETPLACE_CZ]) ? $marketplaces[XAllegroApi::MARKETPLACE_CZ]['sold'] : null),
                    'sold_sk' => (isset($marketplaces[XAllegroApi::MARKETPLACE_SK]) ? $marketplaces[XAllegroApi::MARKETPLACE_SK]['sold'] : null),
                    'sold_hu' => (isset($marketplaces[XAllegroApi::MARKETPLACE_HU]) ? $marketplaces[XAllegroApi::MARKETPLACE_HU]['sold'] : null),
                    'visits_pl' => $marketplaces[XAllegroApi::MARKETPLACE_PL]['visits'],
                    'visits_cz' => (isset($marketplaces[XAllegroApi::MARKETPLACE_CZ]) ? $marketplaces[XAllegroApi::MARKETPLACE_CZ]['visits'] : null),
                    'visits_sk' => (isset($marketplaces[XAllegroApi::MARKETPLACE_SK]) ? $marketplaces[XAllegroApi::MARKETPLACE_SK]['visits'] : null),
                    'visits_hu' => (isset($marketplaces[XAllegroApi::MARKETPLACE_HU]) ? $marketplaces[XAllegroApi::MARKETPLACE_HU]['visits'] : null),
                    'start' => ($start ? (new DateTime($start))->setTimezone(new DateTimeZone(date_default_timezone_get()))->format('d.m.Y H:i') : null),
                    'end' => ($end ? (new DateTime($end))->setTimezone(new DateTimeZone(date_default_timezone_get()))->format('d.m.Y H:i') : null),
                    'format' => ($offer->sellingMode && $offer->sellingMode->format ? $offer->sellingMode->format : ''),
                    'status' => $offer->publication->status,
                    'status_pl' => $marketplaces[XAllegroApi::MARKETPLACE_PL]['statusTranslated'],
                    'status_cz' => (isset($marketplaces[XAllegroApi::MARKETPLACE_CZ]) ? $marketplaces[XAllegroApi::MARKETPLACE_CZ]['statusTranslated'] : null),
                    'status_sk' => (isset($marketplaces[XAllegroApi::MARKETPLACE_SK]) ? $marketplaces[XAllegroApi::MARKETPLACE_SK]['statusTranslated'] : null),
                    'status_hu' => (isset($marketplaces[XAllegroApi::MARKETPLACE_HU]) ? $marketplaces[XAllegroApi::MARKETPLACE_HU]['statusTranslated'] : null),
                    'base_marketplace' => $offer->publication->marketplaces->base->id,
                    'marketplaces' => $marketplaces,
                    'binded' => (int)$binded,
                    'binded_details' => $binded ? $bindedDetails : false,
                    'archived' => $binded && isset($binded['archived']) ? (int)$binded['archived'] : 0,
                    'id_product' => $binded && isset($binded['id_product']) ? $binded['id_product'] : false,
                    'id_product_attribute' => $binded && isset($binded['id_product_attribute']) ? $binded['id_product_attribute'] : false,
                    'reference' => ($binded ? $binded['reference'] : false),
                    'ean13' => ($binded ? $binded['ean13'] : false),
                    'quantity_shop' => ($binded ? $binded['quantity_shop'] : false),
                    'auto_renew' => ($binded ? $binded['auto_renew'] : null),
                    'id_shop' => ($binded ? $binded['id_shop'] : null),
                    'shop_name' => ($binded ? $binded['shop_name'] : null)
                );
            }

            if (isset($this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_binded'})) {
                $this->_list = array_filter($this->_list, function ($listItem) {
                    return $listItem['binded'] == (int)$this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_binded'};
                });
            }

            if (isset($this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_archived'})) {
                $this->_list = array_filter($this->_list, function ($listItem) {
                    return $listItem['archived'] == (int)$this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_archived'};
                });
            }

            if (isset($this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_auto_renew'})) {
                $this->_list = array_filter($this->_list, function ($listItem) {
                    $filter = $this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_auto_renew'};

                    if ($filter == 'default') {
                        return $listItem['auto_renew'] === null;
                    } else {
                        return is_numeric($listItem['auto_renew']) && (int)$listItem['auto_renew'] === (int)$filter;
                    }
                });
            }

            if (isset($this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_id_shop'})) {
                $this->_list = array_filter($this->_list, function ($listItem) {
                    return $listItem['id_shop'] == (int)$this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_id_shop'};
                });
            }

            if ($filterReference) {
                $this->_list = array_filter($this->_list, function ($listItem) use ($filterReference) {
                    return strpos($listItem['reference'], $filterReference) !== false;
                });
            }

            if ($filterEan13) {
                $this->_list = array_filter($this->_list, function ($listItem) use ($filterEan13) {
                    return false !== strpos($listItem['ean13'], $filterEan13);
                });
            }

            $this->_listTotal = (int)$result->totalCount;
        }
        catch (Exception $ex) {
            $this->errors[] = (string)$ex;
        }
    }

    /**
     * @param array $list
     * @param string $key
     * @param int $value
     * @return mixed
     */
    private function findElementByKeyValue(array $list, $key, $value)
    {
        foreach ($list as $item)
        {
            if (is_array($item) && isset($item[$key]) && $item[$key] == $value) {
                return $item;
            }
            else if (is_object($item) && property_exists($item, $key) && $item->{$key} == $value) {
                return $item;
            }
        }

        return false;
    }

    /**
     * @todo Refactor to Enum translation
     * @deprecated
     */
    private function formatOfferStatus($status)
    {
        switch ($status) {
            case PublicationStatus::INACTIVE:
                return $this->l('szkic');

            case PublicationStatus::ACTIVATING:
                return $this->l('zaplanowana');

            case PublicationStatus::ACTIVE:
                return $this->l('aktywna');

            case PublicationStatus::ENDED:
                return $this->l('zakończona');
        }

        return null;
    }

    /**
     * @param string|null $profile
     * @return array[]
     */
    private function getFieldsList($profile = null)
    {
        $shopList = [];
        foreach (Shop::getShops() as $shop) {
            $shopList[$shop['id_shop']] = $shop['name'];
        }

        $fieldsList = [
            'image' => [
                'title' => '',
                'width' => 'auto',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'title' => $this->l('Zdjęcie'),
                    'default' => true
                ]
            ],
            'id_auction' => [
                'title' => $this->l('ID'),
                'width' => 'auto',
                'class' => 'fixed-width-md',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'readonly' => true,
                    'default' => true
                ]
            ],
            'name' => [
                'title' => $this->l('Tytuł oferty'),
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-name' : ''),
                'width' => 'auto',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'readonly' => true,
                    'default' => true
                ]
            ],
            'external' => [
                'title' => $this->l('Sygnatura'),
                'class' => 'fixed-width-md',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'quantity' => [
                'title' => $this->l('Ilość'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz'),
                    'default' => true
                ]
            ],
            'quantity_shop' => [
                'title' => $this->l('Ilość w sklepie'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz'),
                    'default' => true
                ]
            ],
            'shop_name' => [
                'title' => $this->l('Sklep'),
                'class' => 'fixed-width-md',
                'type' => 'select',
                'list' => $shopList,
                'filter_key' => 'id_shop',
                'orderby' => false,
                'settings' => [
                    'default' => Shop::isFeatureActive(),
                    'desc' => $this->l('tylko w opcji multistore')
                ]
            ],
            'reference' => [
                'title' => $this->l('Indeks'),
                'class' => 'fixed-width-md',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'ean13' => [
                'title' => $this->l('Ean'),
                'class' => 'fixed-width-md',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'price' => [
                'title' => $this->l('Cena'),
                'class' => 'text-right' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-price' : ''),
                'align' => 'right',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'price_pl' => [
                'title' => $this->l('Cena (PL)'),
                'marketplace' => XAllegroApi::MARKETPLACE_PL,
                'class' => 'text-right' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-price_pl' : ''),
                'align' => 'right',
                'search' => false,
                'orderby' => false
            ],
            'price_cz' => [
                'title' => $this->l('Cena (CZ)'),
                'marketplace' => XAllegroApi::MARKETPLACE_CZ,
                'class' => 'text-right' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-price_cz' : ''),
                'align' => 'right',
                'search' => false,
                'orderby' => false
            ],
            'price_sk' => [
                'title' => $this->l('Cena (SK)'),
                'marketplace' => XAllegroApi::MARKETPLACE_SK,
                'class' => 'text-right' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-price_sk' : ''),
                'align' => 'right',
                'search' => false,
                'orderby' => false
            ],
            'price_hu' => [
                'title' => $this->l('Cena (HU)'),
                'marketplace' => XAllegroApi::MARKETPLACE_HU,
                'class' => 'text-right' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-price_hu' : ''),
                'align' => 'right',
                'search' => false,
                'orderby' => false
            ],
            'price_shop' => [
                'title' => $this->l('Cena ost.'),
                'hint' => $this->l('Cena produktu na podstawie konfiguracji modułu'),
                'type' => 'price',
                'align' => 'text-right',
                'class' => 'fixed-width-sm',
                'havingFilter' => false,
                'search' => false,
                'orderby' => false
            ],
            'offers' => [
                'title' => $this->l('Ofert'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko licytacje'),
                    'default' => true
                ]
            ],
            'sold' => [
                'title' => $this->l('Sprzedano'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => true,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz'),
                    'default' => true
                ]
            ],
            'sold_pl' => [
                'title' => $this->l('Sprzedano (PL)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_PL,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz')
                ]
            ],
            'sold_cz' => [
                'title' => $this->l('Sprzedano (CZ)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_CZ,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz')
                ]
            ],
            'sold_sk' => [
                'title' => $this->l('Sprzedano (SK)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_SK,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz')
                ]
            ],
            'sold_hu' => [
                'title' => $this->l('Sprzedano (HU)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_HU,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz')
                ]
            ],
            'visits' => [
                'title' => $this->l('Wizyt'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false
            ],
            'visits_pl' => [
                'title' => $this->l('Wizyt (PL)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_PL,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false
            ],
            'visits_cz' => [
                'title' => $this->l('Wizyt (CZ)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_CZ,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false
            ],
            'visits_sk' => [
                'title' => $this->l('Wizyt (SK)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_SK,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false
            ],
            'visits_hu' => [
                'title' => $this->l('Wizyt (HU)'),
                'hint' => $this->l('Ostatnie 30 dni'),
                'marketplace' => XAllegroApi::MARKETPLACE_HU,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'search' => false,
                'orderby' => false
            ],
            'start' => [
                'title' => $this->l('Data rozpoczęcia'),
                'class' => 'fixed-width-md',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'end' => [
                'title' => $this->l('Data zakończenia'),
                'class' => 'fixed-width-md',
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'status' => [
                'title' => $this->l('Status'),
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-status' : ''),
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'status_pl' => [
                'title' => $this->l('Status (PL)'),
                'marketplace' => XAllegroApi::MARKETPLACE_PL,
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-status_pl' : ''),
                'search' => false,
                'orderby' => false
            ],
            'status_cz' => [
                'title' => $this->l('Status (CZ)'),
                'marketplace' => XAllegroApi::MARKETPLACE_CZ,
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-status_cz' : ''),
                'search' => false,
                'orderby' => false
            ],
            'status_sk' => [
                'title' => $this->l('Status (SK)'),
                'marketplace' => XAllegroApi::MARKETPLACE_SK,
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-status_sk' : ''),
                'search' => false,
                'orderby' => false
            ],
            'status_hu' => [
                'title' => $this->l('Status (HU)'),
                'marketplace' => XAllegroApi::MARKETPLACE_HU,
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-status_hu' : ''),
                'search' => false,
                'orderby' => false
            ],
            'marketplace' => [
                'title' => $this->l('Rynek'),
                'class' => (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-marketplace' : ''),
                'search' => false,
                'orderby' => false,
                'settings' => [
                    'desc' => $this->l('tylko Kup teraz'),
                    'default' => true
                ]
            ],
            'binded' => [
                'title' => $this->l('Powiązana'),
                'hint' => $this->l('Powiązana z produktem'),
                'align' => 'center',
                'class' => 'fixed-width-xs' . (version_compare(_PS_VERSION_, '1.7.8.0', '<') ? ' column-binded' : ''),
                'type' => 'bool',
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'archived' => [
                'title' => $this->l('Zarchiwizowana'),
                'hint' => $this->l('Powiązanie zarchiwizowane w bazie danych'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'bool',
                'icon' => [
                    '0' => ['class' => 'icon-minus'],
                    '1' => ['class' => 'icon-archive']
                ],
                'search' => true,
                'orderby' => false,
                'settings' => [
                    'default' => true
                ]
            ],
            'auto_renew' => [
                'title' => $this->l('Wznawianie'),
                'hint' => $this->l('Opcja auto wznawiania'),
                'class' => 'fixed-width-md x-auction-list-auto_renew',
                'search' => true,
                'orderby' => false,
                'filter_key' => 'auto_renew',
                'type' => 'select',
                'list' => [
                    'default' => $this->l('domyślnie'),
                    '1' => $this->l('tak'),
                    '0' => $this->l('nie'),
                    '-1' => $this->l('błąd wznawiania')
                ],
                'settings' => [
                    'default' => true,
                    'desc' => $this->l('tylko Kup teraz')
                ]
            ]
        ];

        if (!$profile) {
            return $fieldsList;
        }

        if ($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} === 'buy_now') {
            unset($fieldsList['offers']);
        }
        else if ($this->allegroCookie->{$this->getAllegroCookieFilter('offerType')} === 'auction') {
            unset(
                $fieldsList['quantity'],
                $fieldsList['quantity_shop'],
                $fieldsList['price_cz'],
                $fieldsList['price_sk'],
                $fieldsList['price_hu'],
                $fieldsList['visits_cz'],
                $fieldsList['visits_sk'],
                $fieldsList['visits_hu'],
                $fieldsList['sold'],
                $fieldsList['sold_cz'],
                $fieldsList['sold_pl'],
                $fieldsList['sold_sk'],
                $fieldsList['sold_hu'],
                $fieldsList['marketplace'],
                $fieldsList['status_cz'],
                $fieldsList['status_sk'],
                $fieldsList['status_hu'],
                $fieldsList['auto_renew']
            );
        }

        if (!Shop::isFeatureActive()) {
            unset($fieldsList['shop_name']);
        }

        $auctionFieldsListSettings = json_decode(XAllegroConfiguration::get('AUCTION_FIELDS_LIST_SETTINGS'), true);
        $fieldsListProfile = [];

        if (isset($auctionFieldsListSettings[$profile])) {
            foreach ($fieldsList as $fieldId => $field) {
                // new field added in module update -> we need to show it always
                // next find saved fields in selected profile
                if (!isset($auctionFieldsListSettings[$profile][$fieldId])
                    || (int)$auctionFieldsListSettings[$profile][$fieldId]
                ) {
                    $fieldsListProfile[$fieldId] = $field;
                }
            }
        }
        // list without any profile
        else {
            foreach ($fieldsList as $fieldId => $field) {
                if (isset($field['settings']['default']) && $field['settings']['default']) {
                    $fieldsListProfile[$fieldId] = $field;
                }
            }
        }

        return $fieldsListProfile;
    }

    public function ajaxProcessGetAuctionFormModal()
    {
        if ($this->tabAccess['edit'] !== '1') {
            die(json_encode([
                'success' => false,
                'message' => $this->l('Nie masz uprawnień do edycji w tym miejscu.')
            ]));
        }

        $formAction = Tools::getValue('formAction');

        if ($formAction == 'update') {
            $auctions = [];

            foreach (Tools::getValue('auctions', []) as $item) {
                $auctions[$item['id']] = [
                    'id_auction' => $item['id'],
                    'title' => $item['title'],
                    'href' => XAllegroApi::generateOfferUrl($item['id'], $this->allegroApi->getAccount()->sandbox)
                ];
            }
        } else {
            $auctionsPOST = [];

            foreach (Tools::getValue('auctions', []) as $item) {
                $auctionsPOST[$item['id']] = $item['title'];
            }

            switch ($formAction) {
                case 'finish': $closed = 0; break;
                case 'redo': $closed = 1; break;
                default: $closed = false;
            }

            $auctions = XAllegroAuction::getAuctionsByAllegroId(array_keys($auctionsPOST), $closed);

            if (empty($auctions)) {
                die(json_encode([
                    'success' => false,
                    'message' => $this->l('Nie znaleziono żadnej z wybranych ofert.')
                ]));
            }

            foreach ($auctions as &$auction) {
                $auction['title'] = $auctionsPOST[$auction['id_auction']];
                $auction['href'] = XAllegroApi::generateOfferUrl($auction['id_auction'], $this->allegroApi->getAccount()->sandbox);

                if ($formAction == 'redo') {
                    $productOOS = XAllegroProduct::setOOS(StockAvailable::outOfStock($auction['id_product']));
                    $productQuantity = StockAvailable::getQuantityAvailableByProduct($auction['id_product'], $auction['id_product_attribute'], $auction['id_shop']);
                    $productDisabledByQuantity = XAllegroProduct::setDisabledByQuantity($productQuantity, $productOOS, $this->allegroApi->getAccount()->id);
                    $productDisabledByActive = XAllegroProduct::setDisabledByActive((int)$auction['shop_active']);

                    // when Allegro always max is disabled get last auction quantity
                    // this is the last value in our database before auction was closed
                    if (!XAllegroConfiguration::get('QUANITY_ALLEGRO_ALWAYS_MAX') && $auction['quantity'] < $productQuantity) {
                        $auctionQuantity = XAllegroProduct::calculateQuantity($auction['quantity'], $productOOS, $this->allegroApi->getAccount()->id);
                    } else {
                        $auctionQuantity = XAllegroProduct::calculateQuantity($productQuantity, $productOOS, $this->allegroApi->getAccount()->id);
                    }

                    $auction['redoData'] = [
                        'status' => XAllegroAuction::getAuctionsStatus($auction['id_product'], $auction['id_product_attribute'], $auction['id_xallegro_account'], $auction['id_shop']),
                        'productOOS' => $productOOS,
                        'productQuantity' => $productQuantity,
                        'auctionQuantityMax' => XAllegroApiTools::calculateMaxQuantity($productQuantity),
                        'auctionQuantity' => $auctionQuantity,
                        'auctionDisabled' => (int)($productDisabledByQuantity || $productDisabledByActive)
                    ];
                }
            }
        }

        $tpl = $this->context->smarty->createTemplate($this->module->getLocalPath() . 'views/templates/admin/' . $this->tpl_folder . 'helpers/list/auction-form-modal.tpl');
        $tpl->assign([
            'allegroAccountId' => $this->allegroApi->getAccount()->id,
            'formAction' => $formAction,
            'auctions' => $auctions,
            'availableUpdateEntities' => ($formAction == 'update' ? (new EntityUpdaterFinder($this->allegroApi))->getUpdatersForView() : null)
        ]);

        die(json_encode([
            'success' => true,
            'html' => $tpl->fetch()
        ]));
    }

    public function	ajaxProcessGetProductList()
    {
        $query = Tools::getValue('q', false);

        if (!$query || strlen($query) < 1) {
            die();
        }

        if ($pos = strpos($query, ' (ref:')) {
            $query = substr($query, 0, $pos);
        }

        $items = Db::getInstance()->executeS('
            SELECT p.`id_product`, p.`reference`, pl.`name`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' . (int)$this->allegroApi->getAccount()->id_language . Shop::addSqlRestrictionOnLang('pl') . ')
            WHERE (pl.`name` LIKE "%' . pSQL($query).  '%" 
                OR p.`reference` LIKE "%' . pSQL($query) . '%"' .
            (is_numeric($query) ? ' OR p.`id_product` = ' . (int)$query : '') . ')' . '
            GROUP BY p.`id_product`
        ');

        foreach ($items AS $item) {
            $item['reference'] = str_replace('|', '', $item['reference']);
            $item['name'] = str_replace('|', '', $item['name']);

            echo 'id: ' . $item['id_product'] . ' - '.trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int)($item['id_product']) . "\n";
        }

        die();
    }

    public function	ajaxProcessGetAttributes()
    {
        $product = new Product(Tools::getValue('id_product'));
        die(json_encode($product->getAttributesResume($this->allegroApi->getAccount()->id_language)));
    }

    public function ajaxProcessAuctionUpdate()
    {
        $entity = Tools::getValue('entity');
        $auctionProcessedIndex = (int)Tools::getValue('auctionIndex');

        try {
            $updater = new Updater($entity, $this->allegroApi);
        }
        catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'continue' => false,
                'message' => (string)$e,
                'processed' => $auctionProcessedIndex
            ]));
        }

        $result = $updater->handle();

        if (!$result['success']) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'asWarning' => (isset($result['as_warning']) && $result['as_warning']),
                'message' => $result['message'],
                'messageOnFinish' => $updater->getMessageOnFinish(),
                'processed' => ++$auctionProcessedIndex
            ]));
        }
        
        die(json_encode([
            'success' => true,
            'continue' => true,
            'message' => $result['message'],
            'messageOnFinish' => $updater->getMessageOnFinish(),
            'processed' => ++$auctionProcessedIndex
        ]));
    }

    public function ajaxProcessAuctionFinish()
    {
        $auctionId = Tools::getValue('auction');
        $auction = XAllegroAuction::getAuctionByAllegroId($auctionId);
        $auctionProcessedIndex = (int)Tools::getValue('auctionIndex');
        $auctionHref = $this->generateAuctionHref($auctionId);

        if (!$auction) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd zamykania oferty %s: <em>Nie znaleziono powiązania w bazie danych.</em>', $auctionHref),
                'processed' => ++$auctionProcessedIndex
            ]));
        }

        $resource = $this->allegroApi->sale()->productOffers();

        try {
            $offerUpdate = new OfferUpdate($auction->id_auction);
            $offerUpdate->publication = new StdClass();
            $offerUpdate->publication->status = PublicationStatus::ENDED;

            $resource->update($offerUpdate);

            $this->log
                ->account($this->allegroApi->getAccount()->id)
                ->offer($offerUpdate->id)
                ->info(LogType::OFFER_PUBLICATION_STATUS_ENDED());

            if ($resource->getCode() == 202) {
                $replayData = new StdClass();
                $replayData->operationId = basename($resource->getHeaders()->location);

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => sprintf('Oferta %s: <em>Trwa zamykanie...</em>', $auctionHref),
                    'asPlaceholder' => true,
                    'processed' => $auctionProcessedIndex,
                    'replayAction' => 'auctionFinishReplay',
                    'replayData' => $replayData
                ]));
            }
        }
        catch (Exception $exception) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd zamykania oferty %s: <em>%s.</em>', $auctionHref, $exception),
                'processed' => ++$auctionProcessedIndex
            ]));
        }

        XAllegroAuction::closeAuction($auctionId, new DateTime());
        XAllegroAuction::updateAuctionAutoRenew($auctionId, 0);

        die(json_encode([
            'success' => true,
            'continue' => true,
            'message' => sprintf('Oferta %s: <em>Poprawnie zamknięta.</em>', $auctionHref),
            'messageOnFinish' => 'Zamknięto wybrane oferty, zamknij aby kontynuować.',
            'processed' => ++$auctionProcessedIndex
        ]));
    }

    public function ajaxProcessAuctionFinishReplay()
    {
        // wait for API to process operation
        // default time is passed in "retry-after" header (default: 5)
        sleep(2);

        $auctionId = Tools::getValue('auction');
        $auctionProcessedIndex = (int)Tools::getValue('auctionIndex');
        $auctionHref = $this->generateAuctionHref($auctionId);
        $operationId = Tools::getValue('replayData')['operationId'];

        $resource = $this->allegroApi->sale()->productOffers();

        try {
            $resource->updateOperationCheck($auctionId, $operationId);

            $this->log
                ->account($this->allegroApi->getAccount()->id)
                ->offer($auctionId)
                ->info(LogType::OFFER_PROCESS_OPERATION_CHECK(), [
                    'operationId' => $operationId
                ]);

            if ($resource->getCode() == 202) {
                $replayData = new StdClass();
                $replayData->operationId = basename($resource->getHeaders()->location);

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => '',
                    'processed' => $auctionProcessedIndex,
                    'replayAction' => 'auctionFinishReplay',
                    'replayData' => $replayData
                ]));
            }
            else if ($resource->getCode() == 303) {
                XAllegroAuction::closeAuction($auctionId, new DateTime());
                XAllegroAuction::updateAuctionAutoRenew($auctionId, 0);

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => sprintf('Oferta %s: <em>Poprawnie zamknięta.</em>', $auctionHref),
                    'messageOnFinish' => 'Zamknięto wybrane oferty, zamknij aby kontynuować.',
                    'processed' => ++$auctionProcessedIndex
                ]));
            }
        }
        catch (Exception $exception) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd zamykania oferty %s: <em>%s.</em>', $auctionHref, $exception),
                'processed' => ++$auctionProcessedIndex
            ]));
        }
    }

    public function ajaxProcessAuctionRedo()
    {
        $error = false;
        $auctionId = Tools::getValue('auction');

        $auction = XAllegroAuction::getAuctionByAllegroId($auctionId);
        $auctionQuantity = (int)Tools::getValue('auctionQuantity');
        $auctionProcessedIndex = (int)Tools::getValue('auctionIndex');
        $auctionHref = $this->generateAuctionHref($auctionId);

        if (!$auction) {
            $error = $this->l('Nie znaleziono powiązania w bazie danych.');
        }
        else if ($auctionQuantity <= 0 || $auctionQuantity > XAllegroApi::QUANTITY_MAX) {
            $error = $this->l('Podano błędną ilość.');
        }
        else if (XAllegroConfiguration::get('QUANITY_CHECK')) {
            $productOOS = XAllegroProduct::setOOS(StockAvailable::outOfStock($auction->id_product));
            $productQuantity = StockAvailable::getQuantityAvailableByProduct($auction->id_product, $auction->id_product_attribute, $auction->id_shop);

            if (XAllegroProduct::setDisabledByQuantity($productQuantity, $productOOS, $this->allegroApi->getAccount()->id)) {
                $error = $this->l('Brak odpowiedniej ilości produktu w sklepie.');
            }
        }
        else if (XAllegroProduct::setDisabledByActive(XAllegroHelper::getActiveByProductId($auction->id_product, $auction->id_shop))) {
            $error = $this->l('Produkt jest nieaktywny w sklepie.');
        }

        if ($error !== false) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd wznowienia oferty %s: <em>%s.</em>', $auctionHref, $error),
                'processed' => ++$auctionProcessedIndex
            ]));
        }

        $resource = $this->allegroApi->sale()->productOffers();

        try {
            $offerUpdate = new OfferUpdate($auction->id_auction);
            $offerUpdate->stock = new Stock();
            $offerUpdate->stock->available = $auctionQuantity;
            $offerUpdate->publication = new Publication();
            $offerUpdate->publication->status = PublicationStatus::ACTIVE;

            $resource->update($offerUpdate);

            $this->log
                ->account($this->allegroApi->getAccount()->id)
                ->offer($offerUpdate->id)
                ->info(LogType::OFFER_PUBLICATION_STATUS_ACTIVE(), [
                    'quantity' => $auctionQuantity
                ]);

            if ($resource->getCode() == 202) {
                $replayData = new StdClass();
                $replayData->operationId = basename($resource->getHeaders()->location);

                XAllegroAuction::startAuction($auctionId);

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => sprintf('Oferta %s: <em>Trwa wznawianie...</em>', $auctionHref),
                    'asPlaceholder' => true,
                    'processed' => $auctionProcessedIndex,
                    'replayAction' => 'auctionRedoReplay',
                    'replayData' => $replayData
                ]));
            }
        }
        catch (Exception $exception) {
            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd wznowienia oferty %s: <em>%s.</em>', $auctionHref, $exception),
                'processed' => ++$auctionProcessedIndex
            ]));
        }

        XAllegroAuction::updateAuctionQuantity($auctionQuantity, $auctionId);
        XAllegroAuction::activeAuction($auctionId);
        XAllegroAuction::updateAuctionAutoRenew($auctionId, Tools::getValue('auctionAutoRenew', null));

        die(json_encode([
            'success' => true,
            'continue' => true,
            'message' => sprintf('Oferta %s: <em>Wznowiona z ilością: %d.</em>', $auctionHref, $auctionQuantity),
            'messageOnFinish' => 'Wznowiono wybrane oferty, zamknij aby kontynuować.',
            'processed' => ++$auctionProcessedIndex
        ]));
    }

    public function ajaxProcessAuctionRedoReplay()
    {
        // wait for API to process operation
        // default time is passed in "retry-after" header (default: 5)
        sleep(2);

        $auctionId = Tools::getValue('auction');
        $auctionQuantity = (int)Tools::getValue('auctionQuantity');
        $auctionProcessedIndex = (int)Tools::getValue('auctionIndex');
        $auctionHref = $this->generateAuctionHref($auctionId);
        $operationId = Tools::getValue('replayData')['operationId'];

        $resource = $this->allegroApi->sale()->productOffers();

        try {
            $resource->updateOperationCheck($auctionId, $operationId);

            $this->log
                ->account($this->allegroApi->getAccount()->id)
                ->offer($auctionId)
                ->info(LogType::OFFER_PROCESS_OPERATION_CHECK(), [
                    'operationId' => $operationId
                ]);

            if ($resource->getCode() == 202) {
                $replayData = new StdClass();
                $replayData->operationId = basename($resource->getHeaders()->location);

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => '',
                    'processed' => $auctionProcessedIndex,
                    'replayAction' => 'auctionRedoReplay',
                    'replayData' => $replayData
                ]));
            }
            else if ($resource->getCode() == 303) {
                XAllegroAuction::updateAuctionQuantity($auctionQuantity, $auctionId);
                XAllegroAuction::activeAuction($auctionId);
                XAllegroAuction::updateAuctionAutoRenew($auctionId, Tools::getValue('auctionAutoRenew', null));

                die(json_encode([
                    'success' => true,
                    'continue' => true,
                    'message' => sprintf('Oferta %s: <em>Wznowiona z ilością: %d.</em>', $auctionHref, $auctionQuantity),
                    'messageOnFinish' => 'Wznowiono wybrane oferty, zamknij aby kontynuować.',
                    'processed' => ++$auctionProcessedIndex
                ]));
            }
        }
        catch (Exception $exception) {
            XAllegroAuction::closeAuction($auctionId);

            die(json_encode([
                'success' => false,
                'continue' => true,
                'message' => sprintf('Błąd wznowienia oferty %s: <em>%s.</em>', $auctionHref, $exception),
                'processed' => ++$auctionProcessedIndex
            ]));
        }
    }

    public function ajaxProcessChangeAutoRenew()
    {
        $offerId = Tools::getValue('offerId');
        $autoRenew = Tools::getValue('autoRenew', null);
        $success = true;

        if (is_array($offerId)) {
            foreach ($offerId as $id) {
                $success &= XAllegroAuction::updateAuctionAutoRenew($id, $autoRenew);
            }
        } else {
            $success = XAllegroAuction::updateAuctionAutoRenew($offerId, $autoRenew);
        }

        die(json_encode([
            'success' => $success
        ]));
    }

    public function ajaxProcessSaveAuctionListSettings()
    {
        $listSettings = [
            'default' => Tools::getValue('fields')
        ];

        XAllegroConfiguration::updateValue('AUCTION_FIELDS_LIST_SETTINGS', json_encode($listSettings));
        $this->processResetFilters();

        die(json_encode([
            'url' => $this->context->link->getAdminLink('AdminXAllegroAuctionsList') . '&offerType=' . Tools::getValue('offerType')
        ]));
    }

    /**
     * @param float|string $auctionId
     * @return string
     */
    private function generateAuctionHref($auctionId)
    {
        return sprintf('<a href="%s" target="_blank" rel="nofollow"><b>%s</b></a>',
            XAllegroApi::generateOfferUrl($auctionId, $this->allegroApi->getAccount()->sandbox),
            $auctionId
        );
    }

    /**
     * @param array $array
     * @param string $key
     * @param array $new
     * @return array
     */
    private function arrayInsertAfter(array $array, $key, array $new)
    {
        $index = array_search($key, array_keys($array));
        $pos = false === $index ? count($array) : $index + 1;

        return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }
}
