<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
    exit;

/*
module version part in PosApplicationNUmber, Change it always when version is changing.
*/
define('MODULE_NUMBER_PREFIX', '_P1922_');

include_once 'config/ehpcfg.php';
include_once 'sql/ScbDbUtil.php';

class SantanderCredit extends PaymentModule
{

    private $_errorsArray = array();
    private $shopTestId = '99995';
    private $ssl = false;

    public function __construct()
    {

        $this->name = 'santandercredit';
        $this->tab = 'payments_gateways';
        $this->version = '1.9.22'; //remember to change MODULE_NUMBER_PREFIX
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Santander Consumer Bank';

        $this->bootstrap = true;
        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Santander - System ratalny');
        $this->description = $this->l('Santander - Zakupy na raty w internecie');
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == "on") {
            $this->ssl = true;
        }
    }

    /**
     * Odczyt z ostatniego rekordu tabeli scb_espl_phist lub 0
     */
    public static function getEhpDbVersion()
    {
        $dbVer = 0;
        $tableExists = 0;
        $apos = '\'';
        $pfx = Db::getInstance()->getPrefix();
        $queryTableExists = 'SHOW TABLES LIKE ' . $apos . $pfx . 'scb_espl_phist' . $apos;
        Db::getInstance()->execute($queryTableExists);
        $tableExists = Db::getInstance()->numRows();
        if ($tableExists > 0) {
            $queryMax = ' (SELECT MAX(id) from ' . _DB_PREFIX_ . 'scb_ehp_phist' . ') ';
            $queryDbVer = 'SELECT db_ver FROM ' . _DB_PREFIX_ . 'scb_ehp_phist where id = ' . $queryMax;
            $dbVer = Db::getInstance()->getValue($queryDbVer);
        }
        return $dbVer;
    }

    public function install()
    {

        // $dbVer = $this->getEhpDbVersion();

        include_once 'sql/installDB.php';

        //nowe statusy
        foreach (EHP_APP_STATES as $key => $value) {
            if (!$this->createOrderState($key, $value, $this->getStatusColor($key))) {
                return false;
            }
        }

        return parent::install() &&
            $this->createOrUpdateParam('SANTANDERCREDIT_SHOP_ID', $this->shopTestId) &&
            $this->createOrUpdateParam('SANTANDERCREDIT_BLOCK', 'left') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_BLOCK_TITLE', 'eRaty Santander Consumer Bank') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_SYMULATOR', 'true') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_URL_SYMULATOR', 'https://wniosek.eraty.pl/symulator/oblicz/') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_URL_WNIOSEK', 'https://wniosek.eraty.pl/formularz/') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_QTY_SELECTOR', '#quantity_wanted') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_PRICE_SELECTOR', 'div.current-price > span[itemprop="price"],div.current-price > span.current-price-value') &&

            $this->createOrUpdateParam('SANTANDERCREDIT_QTY_QUERY', "$('#quantity_wanted').val();") &&
            $this->createOrUpdateParam('SANTANDERCREDIT_PRICE_QUERY', "$('div.current-price > span[itemprop=\"price\"],div.current-price > span.current-price-value').attr(\"content\");") &&
            $this->createOrUpdateParam('SANTANDERCREDIT_SIM_DATA_FORM', null) &&

            $this->createOrUpdateParam('SANTANDERCREDIT_USE_ORDER_STATE', 'SCB_EHP_ST_PAYMENT_DECLARED') &&

            $this->createOrUpdateParam('SANTANDERCREDIT_PSH_LOGIN', '') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_PSH_PASS', '') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_SVC_LOCATION', 'https://api.santanderconsumer.pl/ProposalServiceHybrid') &&
            $this->createOrUpdateParam('SANTANDERCREDIT_CRT_FILE', 'newfile.crt.pem') &&

            $this->registerHook('paymentOptions') &&
            // $this->registerHook('displayPaymentReturn') &&                
            $this->registerHook('displayOrderConfirmation') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayOrderDetail') &&
            $this->registerHook('actionGetAdminOrderButtons');
    }

    function createOrUpdateParam($pName, $pValue)
    {
        $result = true;
        if (empty(Configuration::get($pName))) {
            $result = Configuration::updateValue($pName, $pValue);
        }
        return $result;
    }


    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        return parent::uninstall();
    }

    private function scbQueryDecode($query)
    {
        // $query = base64_decode($query);
        $query = str_replace('_nawiasL_', '(', $query);
        $query = str_replace('_nawiasP_', ')', $query);
        return $query;
    }
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $scbShopID = strval(Tools::getValue('SANTANDERCREDIT_SHOP_ID'));
            if (!$scbShopID || empty($scbShopID) || !Validate::isGenericName($scbShopID))
                $output .= $this->displayError($this->l('Nieprawidłowy numer Sklepu'));
            else {
                Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', '1234');
                Configuration::updateValue('SANTANDERCREDIT_SHOP_ID', trim(Tools::getValue('SANTANDERCREDIT_SHOP_ID')));
                Configuration::updateValue('SANTANDERCREDIT_SYMULATOR', strval(Tools::getValue('SANTANDERCREDIT_SYMULATOR')));
                Configuration::updateValue('SANTANDERCREDIT_URL_SYMULATOR', Tools::getValue('SANTANDERCREDIT_URL_SYMULATOR'));
                Configuration::updateValue('SANTANDERCREDIT_URL_WNIOSEK', Tools::getValue('SANTANDERCREDIT_URL_WNIOSEK'));
                // Configuration::updateValue('SANTANDERCREDIT_QTY_QUERY', Tools::getValue('SANTANDERCREDIT_QTY_QUERY'));
                Configuration::updateValue('SANTANDERCREDIT_QTY_QUERY', $this->scbQueryDecode(Tools::getValue('SANTANDERCREDIT_QTY_QUERY')));
                // Configuration::updateValue('SANTANDERCREDIT_PRICE_QUERY', Tools::getValue('SANTANDERCREDIT_PRICE_QUERY'));
                Configuration::updateValue('SANTANDERCREDIT_PRICE_QUERY', $this->scbQueryDecode(Tools::getValue('SANTANDERCREDIT_PRICE_QUERY')));
                Configuration::updateValue('SANTANDERCREDIT_SIM_DATA_FORM', strval(Tools::getValue('SANTANDERCREDIT_SIM_DATA_FORM')));
                Configuration::updateValue('SANTANDERCREDIT_PSH_LOGIN', Tools::getValue('SANTANDERCREDIT_PSH_LOGIN'));
                Configuration::updateValue('SANTANDERCREDIT_PSH_PASS', Tools::getValue('SANTANDERCREDIT_PSH_PASS'));
                Configuration::updateValue('SANTANDERCREDIT_SVC_LOCATION', Tools::getValue('SANTANDERCREDIT_SVC_LOCATION'));
                Configuration::updateValue('SANTANDERCREDIT_CRT_FILE', Tools::getValue('SANTANDERCREDIT_CRT_FILE'));
                $output .= $this->displayConfirmation($this->l('Zmiany zostały zapisane'));
            }
        }
        // $this->context->smarty->assign(array_merge($definedurls, $otherValues, $apiValues));

        // $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        $proto = 'http://';
        if ($this->ssl) {
            $proto = 'https://';
        }
        $defaultUrls = EHP_DEF_URLS;
        $defaultQueries = EHP_DEF_QUERIES;
        $ehpCurrentValues = [
            'EHP_CURRENT_URL_SYMULATOR' => Configuration::get('SANTANDERCREDIT_URL_SYMULATOR'),
            'EHP_CURRENT_URL_WNIOSEK' => Configuration::get('SANTANDERCREDIT_URL_WNIOSEK'),
            'EHP_CURRENT_SVC_LOCATION' => Configuration::get('SANTANDERCREDIT_SVC_LOCATION'),
            'EHP_CURRENT_QTY_QUERY' => str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('"', '&quot;', str_replace("'", "&#039;", Configuration::get('SANTANDERCREDIT_QTY_QUERY'))))),
            'EHP_CURRENT_PRICE_QUERY' => str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('"', '&quot;', str_replace("'", "&#039;", Configuration::get('SANTANDERCREDIT_PRICE_QUERY')))))
        ];
        $otherCfgVars = [
            'OrderMapCmd' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/sql/repair_oa_mapping.php'
            , 'scbEhpDocUrl' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/doc/scb_ehp_payment_UserDoc.pdf'
            , 'scbEhpSlsDocUrl' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/doc/scb_ehp_payment_SalesmanDoc.pdf'
            , 'scbEhpAdmDocUrl' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/doc/scb_ehp_payment_AdminDoc.pdf'
            , 'pshIsActiveCommand' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/services/ProposalService/custom/IsActiveRequest.php'
            , 'pshLoginChckCommand' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/services/ProposalService/custom/FreeAppStateRequest.php'
        ];
        $qFunctions = [
            'quantityFn' => $this->quantityFnJs(),
            'priceFn' => $this->priceFnJs()
        ];
        $this->context->smarty->assign(array_merge($otherCfgVars, $defaultUrls, $defaultQueries, $ehpCurrentValues, $qFunctions));
        // $output = $output . $this->display(__FILE__, 'infos.tpl');
        $output = $output . $this->context->smarty->fetch($this->local_path . 'views/templates/hook/infos.tpl');
        $output = $output . $this->displayForm() . $this->context->smarty->fetch($this->local_path . 'views/templates/hook/infos2.tpl');
        return $output;
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $defaultUrls = array(
            array(
                'id_option' => 1,       // The value of the 'value' attribute of the <option> tag.
                'name' => 'Aktualne'    // The value of the text content of the  <option> tag.
            ),
            array(
                'id_option' => 2,
                'name' => 'Domyślne'
            )
        );

        $defaulQtyPriceQueries = array(
            array(
                'id_option' => 1,       // The value of the 'value' attribute of the <option> tag.
                'name' => 'Aktualne'    // The value of the text content of the  <option> tag.
            ),
            array(
                'id_option' => 2,
                'name' => 'Domyślne'
            )
        );

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Parametry bramki płatniczej eRaty Santander:'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Numer Sklepu'),
                    'name' => 'SANTANDERCREDIT_SHOP_ID',
                    'size' => 20,
                    'required' => true
                ),
                // wstawka start
                array(
                    'type' => 'select',
                    'name' => 'SCB_EHP_defaultUrls',
                    'label' => 'RESET adresów url',
                    'options' => array(
                        'query' => $defaultUrls,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                // wstawka stop
                array(
                    'type' => 'text',
                    'label' => $this->l('Adres symulatora'),
                    'name' => 'SANTANDERCREDIT_URL_SYMULATOR',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adres rejestratora wniosków'),
                    'name' => 'SANTANDERCREDIT_URL_WNIOSEK',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adres usługi ProposalServiceHybrid'),
                    'name' => 'SANTANDERCREDIT_SVC_LOCATION',
                    'hint' => 'URL do Web Service',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Login do ProposalServiceHybrid'),
                    'name' => 'SANTANDERCREDIT_PSH_LOGIN',
                    'hint' => 'Login do usługi sieciowej. To oraz hasło powinien dostarczyć Opiekun Sklepu.',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Hasło do ProposalServiceHybrid'),
                    'name' => 'SANTANDERCREDIT_PSH_PASS',
                    'hint' => 'Hasło do usługi sieciowej. Dostarcza Opiekun Sklepu z ramienia SCB.',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Plik certyfikatu'),
                    'name' => 'SANTANDERCREDIT_CRT_FILE',
                    'hint' => 'Nazwa pliku zawierającego certyfikat klienta ProposalServiceHybrid',
                    'size' => 128,
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Zapisz'),
                'class' => 'btn btn-default pull-right'
            ),
            'buttons' => [
                [
                    //'href' => '//url',          // If this is set, the button will be an <a> tag
                    // 'js'   => 'pshPassChange();', // Javascript to execute on click
                    'class' => 'btn',              // CSS class to add
                    'type' => 'button',         // Button type
                    'id' => 'pshPassButton',
                    'name' => 'pshPassButton',       // If not defined, this will take the value of "submitOptions{$table}"
                    'icon' => 'icon-foo',       // Icon to show, if any
                    'title' => 'Hasło do ProposalServiceHybrid'      // Button label
                ],
                [
                    //'href' => '//url',          // If this is set, the button will be an <a> tag
                    // 'js'   => 'pshPassChange();', // Javascript to execute on click
                    'class' => 'btn',              // CSS class to add
                    'type' => 'button',         // Button type
                    'id' => 'pshTestButton',
                    'name' => 'pshTestButton',       // If not defined, this will take the value of "submitOptions{$table}"
                    'icon' => 'icon-foo',       // Icon to show, if any
                    'title' => 'Test połączenia z serwisem'      // Button label
                ]
           ]
        );

        $fields_form[1]['form'] = [
            'legend' => [
                'title' => 'Osadzenie klawisza "Oblicz ratę" na stronie Sklepu:',
                'icon' => 'icon-cogs'
            ],
            'input' => [
                array(
                    'type' => 'switch',
                    'label' => $this->l('Kalkulator na stronie produktu'),
                    'name' => 'SANTANDERCREDIT_SYMULATOR',
                    'is_bool' => true,
                    'hint' => $this->l('Umożliwia obliczanie wysokości raty na stronie produktu'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('TAK'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('NIE'),
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Potwierdzaj dane do symulacji'),
                    'name' => 'SANTANDERCREDIT_SIM_DATA_FORM',
                    'is_bool' => true,
                    'hint' => $this->l('Tak - wyświetla dodatkowy dialog przed wywołaniem kalkulatora rat.'),
                    'values' => array(
                        array(
                            'id' => 'active_sim_data_on',
                            'value' => true,
                            'label' => $this->l('TAK'),
                        ),
                        array(
                            'id' => 'active_sim_data_off',
                            'value' => false,
                            'label' => $this->l('NIE'),
                        )
                    ),
                ),
                // wstawka start
                array(
                    'type' => 'select',
                    'name' => 'SCB_EHP_defaultQUeries',
                    'label' => 'RESET QTY_QUERY i PRICE_QUERY',
                    'options' => array(
                        'query' => $defaulQtyPriceQueries,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                // wstawka stop
                array(
                    'type' => 'text',
                    'label' => $this->l('Odczyt ilości towaru ze strony Sklepu (QTY_QUERY)'),
                    'name' => 'SANTANDERCREDIT_QTY_QUERY',
                    'hint' => 'Funkcja jQuery odczytująca ilość jednostek produktu (dane dla symulatora rat).',
                    'size' => 128,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Odczyt ceny towaru ze strony Sklepu (PRICE_QUERY)'),
                    'name' => 'SANTANDERCREDIT_PRICE_QUERY',
                    'hint' => 'Funkcja jQuery) odczytująca cenę jednostkową (dane dla symulatora rat).',
                    'size' => 128,
                    'required' => true
                ),
            ],
            'submit' => [
                'title' => 'Zapisz',
                'class' => 'btn btn-default pull-right'
            ]
/*            
            , 'buttons' => [
                [
                    //'href' => '//url',          // If this is set, the button will be an <a> tag
                    // 'js'   => 'pshPassChange();', // Javascript to execute on click
                    'class' => 'btn',              // CSS class to add
                    'type' => 'button',         // Button type
                    'id' => 'qCheckButton',
                    'name' => 'qCheckButton',       // If not defined, this will take the value of "submitOptions{$table}"
                    'icon' => 'icon-foo',       // Icon to show, if any
                    'title' => 'Check Queries'      // Button label
                ]
            ]
*/
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['SANTANDERCREDIT_SHOP_ID'] = Configuration::get('SANTANDERCREDIT_SHOP_ID');
        $helper->fields_value['SANTANDERCREDIT_SYMULATOR'] = Configuration::get('SANTANDERCREDIT_SYMULATOR');
        $helper->fields_value['SANTANDERCREDIT_URL_SYMULATOR'] = Configuration::get('SANTANDERCREDIT_URL_SYMULATOR');
        $helper->fields_value['SANTANDERCREDIT_URL_WNIOSEK'] = Configuration::get('SANTANDERCREDIT_URL_WNIOSEK');
        $helper->fields_value['SANTANDERCREDIT_QTY_QUERY'] = Configuration::get('SANTANDERCREDIT_QTY_QUERY');
        $helper->fields_value['SANTANDERCREDIT_PRICE_QUERY'] = Configuration::get('SANTANDERCREDIT_PRICE_QUERY');
        $helper->fields_value['SANTANDERCREDIT_SIM_DATA_FORM'] = Configuration::get('SANTANDERCREDIT_SIM_DATA_FORM');
        $helper->fields_value['SANTANDERCREDIT_SVC_LOCATION'] = Configuration::get('SANTANDERCREDIT_SVC_LOCATION');
        $helper->fields_value['SANTANDERCREDIT_PSH_LOGIN'] = Configuration::get('SANTANDERCREDIT_PSH_LOGIN');
        $helper->fields_value['SANTANDERCREDIT_PSH_PASS'] = Configuration::get('SANTANDERCREDIT_PSH_PASS');
        $helper->fields_value['SANTANDERCREDIT_CRT_FILE'] = Configuration::get('SANTANDERCREDIT_CRT_FILE');

        $helper->fields_value['OrderMapCmd'] = 'OrderMapCmd';
        return $helper->generateForm($fields_form);
    }

    public function hookPaymentOptions($params)
    {
        //        Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
//            'imgDir' => $this->context->link->getModuleLink('santandercredit','images')        
        if ($params['cart']->getOrderTotal() < 100)
            return;
        $this->smarty->assign(array(
            'totalOrderC' => Tools::displayPrice($params['cart']->getOrderTotal(true, Cart::BOTH)),
            'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
            'symulatorURL' => Configuration::get('SANTANDERCREDIT_URL_SYMULATOR'),
            'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
            'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
            'totalOrder' => $params['cart']->getOrderTotal(),
            'imgDir' => $this->_path . 'views/img'
        ));
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
            ->setCallToActionText('eRaty Santander Consumer Bank')
            ->setAction($this->context->link->getModuleLink('santandercredit', 'santanderCreditValidate', array(), true))
            ->setAdditionalInformation($this->fetch('module:santandercredit/views/templates/hook/santanderCreditInfo.tpl'));
        $payment_options = [
            $newOption,
        ];
        return $payment_options;
    }

    function hookDisplayOrderConfirmation($params)
    {        
        if ($params['order']->payment == $this->displayName) {
            // global $cart, $cookie, $currency;
            $cart = new Cart(intval($params['order']->id_cart));            
            $cookie = $this->context->cookie;
            $address = new Address(intval($cart->id_address_invoice));
            $customer = new Customer(intval($cart->id_customer));
            $total = floatval(number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', ''));
            $santanderCreditShopId = trim(Configuration::get('SANTANDERCREDIT_SHOP_ID'));
            $ehpToken = $this->genEhpToken();

            if (!Validate::isLoadedObject($address) || !Validate::isLoadedObject($customer)) {
                return $this->l('Błąd płatności: nieprawidłowy adres lub dane klienta.');
            }

            $summaryDetails = $cart->getSummaryDetails();
            $proto = 'http://';
            if ($this->ssl) {
                $proto = 'https://';
            }
            $POSApplicationNumber = $this->generatePOSAppNr($params['order']);
            $smartTable = array(
                'applicationURL' => Configuration::get('SANTANDERCREDIT_URL_WNIOSEK'),
                'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
                'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
                'orderId' => $POSApplicationNumber, //$params['order']->id, 
                'shopId' => $santanderCreditShopId,
                'shopName' => Configuration::get('PS_SHOP_NAME'),
                'shopMailAdress' => Configuration::get('PS_SHOP_EMAIL'),
                'shopPhone' => Configuration::get('PS_SHOP_PHONE'),
                'shopHttp' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__,

                'returnTrue' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=true&orderId=',                
                'returnFalse' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=false&orderId=',                

                'email' => $customer->email,
                'imie' => ($cookie->logged ? $cookie->customer_firstname : false),
                'nazwisko' => ($cookie->logged ? $cookie->customer_lastname : false),
                'telKontakt' => $address->phone,
                'ulica' => $address->address1,
                'ulica2' => $address->address2,
                'miasto' => $address->city,
                'kodPocz' => $address->postcode,
                'shipping' => round($summaryDetails['total_shipping'], 2),
                'products' => $cart->getProducts(true),
                'totalOrder' => $total,
                'ehpToken' => $ehpToken,
                'modDir' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name
            );
            $this->smarty->assign($smartTable);

            $this->initEhpApplication(
                $params['order']->id,
                $params['order']->date_add,
                $POSApplicationNumber,
                $params['order']->reference,
                $this->getNewOrderStartState(),
                $santanderCreditShopId,
                $smartTable
            );

            return $this->fetch('module:santandercredit/views/templates/hook/santanderCreditPayment.tpl');
        }
    }

    function genEhpToken()
    {
        $orderDate = date("Y_m_d_H_i_s");
        $token = md5(rand()) . '_' . $orderDate;
        return $token;
    }

    function genTimeToken()
    {
        return date("Y_m_d_H_i_s_");
    }

    public static function getNewOrderStartState()
    {
        return Configuration::get('SCB_EHP_ST_PAYMENT_DECLARED');
    }

    /**
     * adding record in scb_espl_order_app_mapping and scb_espl_log
     * 
     */
    public function initEhpApplication($id_order, $date_add, $pos_app_number, $reference, $order_status, $shop_number, $post_data)
    {
        $updateOk = true;
        $orderID = trim($id_order);
        $jsonDataSent = '{}'; //$this->collectPostData($post_data); //TEST IT!!!
        $sql = array();
        $posApplicationNumber = ScbDbUtil::getShopApplicationNumber($id_order);
        if (is_string($posApplicationNumber) and strlen($posApplicationNumber) > 4) {
            /* mapping exists. Probably the best option is do nothing in this place.
                it could happen when order confirmation page was refreshed or when custmoer is startting to send application
                from customer panel.
            */

        } else {
            $sql = [
                'INSERT INTO ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping (id_order, date_add, pos_app_number, reference, order_status, shop_number, check_date, post_data, check_it) VALUES ('
                . $orderID . ', \''
                . $date_add . '\', \''
                . $pos_app_number . '\', \''
                . $reference . '\', \''
                . $order_status . '\', \''
                . $shop_number . '\', \''
                . $date_add . '\', \''
                . $jsonDataSent
                . '\', 1);'
            ];
        }
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                $updateOk = false;
                break;
            }
        }
        return $updateOk;
    }

    public function displaySymulator($params)
    {
        global $smarty;

        $id_product = Tools::getValue('id_product');
        $product = new Product($id_product, true);
        $simDlg = 0;
        if (Configuration::get('SANTANDERCREDIT_SIM_DATA_FORM') == 1) {
            $simDlg = 1;
        }
        $smarty->assign(array(
            'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
            'santanderCreditProductPrice' => round($product->getPrice(true), 2),
            'jq_qtySelector' => Configuration::get('SANTANDERCREDIT_QTY_SELECTOR'),
            'jq_priceSelector' => Configuration::get('SANTANDERCREDIT_PRICE_SELECTOR'),
            'symulatorURL' => Configuration::get('SANTANDERCREDIT_URL_SYMULATOR'),
            'scb_quantity' => Tools::getValue('quantity_wanted', $product->minimal_quantity),
            'quantityFn' => $this->quantityFnJs(),
            'priceFn' => $this->priceFnJs(),
            'displaySimInputModal' => $simDlg
        ));

        if (Configuration::get('SANTANDERCREDIT_SYMULATOR') <> null) {
            return $this->display(__FILE__, 'santanderCreditProduct.tpl');
        }
    }


    private function priceFnJs()
    {
        $fn = '';
        //docelowo selectory zamienić na priceQuery i quantityQuery. Mają zwracać wartość.        
        $priceSelector = Configuration::get('SANTANDERCREDIT_PRICE_QUERY');
        // funkcje zmienić aby korzystały z query
        $price = "
        function ehpGetPprice(){
            let q = " . $priceSelector . ";
            return q;
        }
        ";
        $fn = $price;
        return $fn;
    }

    private function quantityFnJs()
    {
        $fn = '';
        //docelowo selectory zamienić na priceQuery i quantityQuery. Mają zwracać wartość.
        $qtySelector = Configuration::get('SANTANDERCREDIT_QTY_QUERY');
        // funkcje zmienić aby korzystały z query
        $qty = "
        function ehpGetPquantity(){
            let q = " . $qtySelector . ";
            return q;
        }
        ";
        $fn = $qty;
        return $fn;
    }

    /*
        protected function getProductMinimalQuantity($product)
        {
            $minimal_quantity = 1;
            return $minimal_quantity;
        }
    */

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->displaySymulator($params);
    }



    public function hookActionGetAdminOrderButtons(array $params)
    {
    }

    public function hookDisplayAdminOrder($params)
    {

        $order = new Order($params['id_order']);
        if ($order->payment == $this->displayName) {
            try {
                $sql = 'SELECT * from ' . _DB_PREFIX_ . 'scb_ehp_log where id_order = ' . trim($order->id);
                $result = Db::getInstance()->executeS($sql);
                $fullAppInfo = ScbDbUtil::getFullApplicationInfo($order->id);
                $proto = 'http://';
                if ($this->ssl) {
                    $proto = 'https://';
                }
                $smartTab = array(
                    'refreshCommand' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/services/ProposalService/custom/AppStateRequest.php',
                    'id_order' => $params['id_order']
                    , 'log' => $result
                    , 'fullAppInfo' => $fullAppInfo
                    , 'instrukcja' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/doc/scb_ehp_payment_SalesmanDoc.pdf'
                );
                $this->smarty->assign($smartTab);
                return $this->display(__FILE__, 'ehpDisplayAdminOrder.tpl');
            } catch (Exception $exc) {
                // put something into log
            }
        }

    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    // public function hookBackOfficeHeader()
    public function hookDisplayBackOfficeHeader()
    {
        // addJS is NOT deprecated in Back Office!!!!!
        $this->context->controller->addJS($this->_path . 'views/js/santanderCreditBO.js');
        $this->context->controller->addCSS($this->_path . 'views/css/scbEhpStyle.css');

    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerJavascript(
            $this->name . '-scb_js',
            'modules/' . $this->name . '/views/js/santanderCredit.js',
            [
                'priority' => 200,
                'attribute' => 'async',
            ]
        );
        $this->context->controller->registerStylesheet(
            $this->name . '-scb_css',
            'modules/' . $this->name . '/views/css/scbEhpStyle.css',
            [
                'media' => 'all'
            ]
        );
    }

    public function hookDisplayOrderDetail($params)
    {

        $order = $params['order'];
        if (!Validate::isLoadedObject($order)) {
            return '';
        }
        $ehpMessage = 'Poniżej znajduje się informacja pobrana z systemu bankowego. 
         Aby ją zaktualizować - kliknij "Odśwież informację z Banku"';
        $application = new Application();
        $sendApplicationDisabled = 'disabled';
        $backToApp_sendApp = 'Złóż wniosek';

        $customer = $order->getCustomer();
        $address = new Address();
        $addresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
        foreach ($addresses as $a) {
            if ($a['active'] == 1 and !($a['deleted'] == 1)) {
                $address = $a;
                break;
            }
        }
        $proto = 'http://';
        if ($this->ssl) {
            $proto = 'https://';
        }
        if ($order->payment == $this->displayName) {
            // check if there is a record in scb_ehp_order_app_mapping (ehp payment was started)
            $posApplicationNumber = ScbDbUtil::getShopApplicationNumber($order->id);
            if (is_string($posApplicationNumber) and strlen($posApplicationNumber) > 4) {
                // ehp payment was started
                // now check if application was registered in the bank
                $application = ScbDbUtil::getFullApplicationInfo($order->id);
                if (!(is_string($application->ApplicationNumber) and strlen($application->ApplicationNumber) > 4)) {
                    //nie ma numeru wniosku
                    //You must check application state 
                    $ehpMessage = 'Brak informacji o statusie wniosku kredytowego. 
                    Zwykle wniosek jest rejestrowany przy składaniu zamówienia ale możesz to zrobić również teraz.
                    Kliknij "Odśwież informację z Banku". Jeśli w Banku nie będzie Twojego wniosku a zamówienie zostało złożone 
                    nie wcześniej niż 7 dni temu, uaktywni się klawisz "Złóż wbiosek".';
                    if ($application->CreditState == 'Brak wniosku' and $this->validateCheckDate($application->check_date) == 1
                        and $this->getTimestamp($order->date_add) >= $this->getTimestamp(date_add(date_create("now"),
                            date_interval_create_from_date_string("-7 days"))->format("Y-m-d"))
                    ) {
                        //złóż wniosek tylko wtedy gdy 'Brak wniosku' i status nie starszy niż 5 minut i zamówienie nie starsze niż 7 dni
                        $ehpMessage = 'Możesz teraz złożyć wniosek kredytowy w Banku. Kliknij "Złóż wniosek".';
                        $sendApplicationDisabled = '';
                    }
                } else {

                    //jest numer wniosku kredytowego - sprawdz edytowalne statusy
                    if ($this->editableAppState($application, $order->date_add) == 1) {
                        //ten warunek powoduje że musiało być wykonane sprawdzenie przynajmniej raz
                        //maybe plus: check_date not older than 5 minutes
                        $ehpMessage = 'Wniosek kredytowy jest nie dokończony. Możesz teraz do niego powrócić klikając "Wróć do wniosku".';
                        $sendApplicationDisabled = '';
                        $backToApp_sendApp = 'Wróć do wniosku';
                    }

                }
            } else {
                $ehpMessage = 'Brak informacji o statusie wniosku kredytowego. 
                Zwykle wniosek jest rejestrowany przy składaniu zamówienia ale możesz to zrobić również teraz.
                Kliknij "Odśwież informację z Banku". Jeśli w Banku nie będzie Twojego wniosku a zamówienie zostało złożone 
                nie wcześniej niż 7 dni temu, uaktywni się klawisz "Złóż wbiosek".';
                // add ehp application order mapping
                $posApplicationNumber = $this->generatePOSAppNr($order);

                $this->initEhpApplication(
                    $order->id,
                    $order->date_add,
                    $posApplicationNumber,
                    $order->reference,
                    $this->getNewOrderStartState(),
                    trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
                    [
                        'applicationURL' => Configuration::get('SANTANDERCREDIT_URL_WNIOSEK'),
                        'orderId' => $posApplicationNumber,
                        'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
                        'shopName' => Configuration::get('PS_SHOP_NAME'),
                        'shopMailAdress' => Configuration::get('PS_SHOP_EMAIL'),
                        'shopPhone' => Configuration::get('PS_SHOP_PHONE'),
                        'shopHttp' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__,
                        'returnTrue' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=true&orderId=',
                        'returnFalse' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=false&orderId=',
                        'email' => $customer->email,
                        'imie' => $customer->firstname,
                        'nazwisko' => $customer->lastname,
                        'telKontakt' => $address['phone'],
                        'ulica' => $address['address1'],
                        'ulica2' => $address['address2'],
                        'miasto' => $address['city'],
                        'kodPocz' => $address['postcode'],
                        'shipping' => round($order->total_shipping, 2),
                        'products' => $order->getOrderDetailList(),
                        'totalOrder' => round($order->getOrdersTotalPaid(), 2),
                        'ehpToken' => $this->genEhpToken()
                    ]
                );
                $application = ScbDbUtil::getFullApplicationInfo($order->id);
            }

            $smartTab = [
                'ehpMessage' => $ehpMessage,
                'application' => $application,
                'sendApplicationDisabled' => $sendApplicationDisabled,
                'backToApp_sendApp' => $backToApp_sendApp,
                'refreshCommand' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/services/ProposalService/custom/AppStateRequest.php',
                'id_order' => $order->id,

                'applicationURL' => Configuration::get('SANTANDERCREDIT_URL_WNIOSEK'),

                'orderId' => $posApplicationNumber,
                'shopId' => trim(Configuration::get('SANTANDERCREDIT_SHOP_ID')),
                'shopName' => Configuration::get('PS_SHOP_NAME'),
                'shopMailAdress' => Configuration::get('PS_SHOP_EMAIL'),
                'shopPhone' => Configuration::get('PS_SHOP_PHONE'),
                'shopHttp' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__,
                //            better to go back to customer panel
                'returnTrue' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=true&orderId=',
                'returnFalse' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'module/' . $this->name . '/santanderCreditReturn?status=false&orderId=',
                'email' => $customer->email,
                'imie' => $customer->firstname,
                'nazwisko' => $customer->lastname,
                'telKontakt' => $address['phone'],
                'ulica' => $address['address1'],
                'ulica2' => $address['address2'],
                'miasto' => $address['city'],
                'kodPocz' => $address['postcode'],
                'shipping' => round($order->total_shipping, 2),
                'products' => $order->getOrderDetailList(),
                'totalOrder' => floatval(number_format($order->getOrdersTotalPaid(), 2, '.', '')),
                'ehpToken' => $this->genEhpToken(),
                'userDoc' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name . '/doc/scb_ehp_payment_UserDoc.pdf',
                'modDir' => $proto . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/' . $this->name
            ];

            $this->smarty->assign($smartTab);
            return $this->display(__FILE__, 'views/templates/hook/displayOrderDetail.tpl');
        }
    }

    private function editableAppState($application, $order_date)
    {
        $editable = 0;
        $orderDate = date_create((new DateTimeImmutable($order_date))->format("Y-m-d"));
        $oDt = date_timestamp_get($orderDate);
        $minDate = date_create("now");
        date_add($minDate, date_interval_create_from_date_string("-7 days"));
        $mDt = date_timestamp_get($minDate);
        if ($oDt >= $mDt) {
            if ($this->validateCheckDate($application->check_date) == 1) {
                $eStates = [
                    'Brak wniosku',
                    'Bank',
                    'Bank (-101)',
                    'Bank (-105)',
                    'Klient',
                    'Klient (-100)',
                    'Klient (-104)',
                    'Klient(-127)'
                ];
                if (array_search(trim($application->CreditState), $eStates)) {
                    $editable = 1;
                }
            }
        }
        return $editable;
    }

    private function validateCheckDate($cd)
    {
        $dateIsValid = 0;
        $checkDate = date_create($cd);
        $cDt = date_timestamp_get($checkDate);
        $mincDate = date_create("now");
        date_add($mincDate, date_interval_create_from_date_string("-5 minute"));
        $mcDt = date_timestamp_get($mincDate);
        if ($cDt >= $mcDt) {
            $dateIsValid = 1;
        }
        return $dateIsValid;
    }

    private function getTimestamp($date1)
    {
        $d1 = date_create($date1);
        $oDt1 = date_timestamp_get($d1);
        return $oDt1;
    }

    /**
     * Creates new order state for eRaty espl payment system and return its id. 
     * If state already exists - simply return its id.
     * 
     * @int order state id
     */
    function createOrderState($appState, $oStatusName, $color)
    {
        $result = true;
        $orderStateName = EHP_ORDER_STATE_PREFIX . $appState;
        $pValue = Configuration::get($orderStateName);
        // if (empty($pValue)) {
        try {
            if ($this->isNewState($pValue)) {
                $order_state = new OrderState();
                $order_state->name = array();

                foreach (Language::getLanguages() as $language) {
                    if (Tools::strtolower($language['iso_code']) == 'pl') {
                        $order_state->name[$language['id_lang']] = $oStatusName;
                    } else {
                        $order_state->name[$language['id_lang']] = 'EHP: ' . $appState;
                    }
                }
            } else {
                $order_state = new OrderState((int) $pValue);
            }
            $order_state->send_email = false;
            $order_state->color = $color; //'#DDEEFF';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = true;
            $order_state->invoice = $this->invoiceOnState($appState);
            $order_state->paid = $this->orderPaidOnState($appState);
            $order_state->module_name = 'santandercredit';
            $order_state->deleted = false;

            $order_state->save();

            $result = Configuration::updateValue($orderStateName, (int) $order_state->id);
        } catch (Exception $exc) {
            $result = false;
        }
        return $result;
    }

    function isNewState($pValue)
    {
        $newState = false;
        $table = OrderState::$definition['table'];
        // $pValue = Configuration::get($orderStateName);
        if (empty($pValue)) {
            $newState = true;
        } else {
            if (version_compare(_PS_VERSION_, '8.1.1', '>=') == true) {
                $newState = !OrderState::existsInDatabase((int) $pValue);
            } else {
                $newState = !OrderState::existsInDatabase((int) $pValue, $table);
            }
        }
        return $newState;
    }

    private function invoiceOnState($appState)
    {
        $invoice = false;
        if ($appState == 'EHP_CREDIT_GRANTED') {
            $invoice = true;
        }
        return $invoice;
    }

    private function orderPaidOnState($appState)
    {
        $paid = false;
        if ($appState == 'EHP_CREDIT_GRANTED') {
            $paid = true;
        }
        return $paid;
    }

    function getStatusColor($key)
    {
        $color = '#DDEEFF';
        switch ($key) {
            case 'APPLICATION_PROCEDING':
                $color = '#ffdf30'; //yellow
                break;
            case 'CREDIT_REJECTED':
                $color = '#db0600'; //red
                break;
            case 'ABANDONED':
                $color = '#db0600'; //red
                break;
            case 'CREDIT_GRANTED':
                $color = '#458000'; //green
                break;
            case 'CANCELLED':
                $color = '#db0600'; //red
                break;
            default:
                $color = '#DDEEFF'; //blue
                break;
        }
        return $color;
    }


    /**
     * Generates POSApplicationNumber based on order id and order date
     */
    function generatePOSAppNr(Order $order)
    {
        $id_order = $order->id;
        $POSAppNr = (string) $id_order;
        $suff = '_sd_' . date("Y_m_d_H_i_s");
        try {
            $orderAddDate = new DateTimeImmutable($order->date_add);
            $suff = MODULE_NUMBER_PREFIX . $orderAddDate->format("Y_m_d_H_i_s");
        } catch (Exception $e) {
            $suff = '_ed_' . date("Y_m_d_H_i_s");
        }
        $POSAppNr = $POSAppNr . $suff;
        return $POSAppNr;
    }

    function collectPostData($smartTab)
    {
        $postData = '';
        $ao = ['action' => $smartTab['applicationURL']];
        $nr = 0;
        foreach ($smartTab['products'] as $product) {
            $nr = $nr + 1;
            $ao += ['idTowaru' . $nr => $product['product_id']];
            $ao += ['nazwaTowaru' . $nr => $product['product_name']];
            $ao += ['wartoscTowaru' . $nr => round($product['unit_price_tax_incl'], 2)];
            $ao += ['liczbaSztukTowaru' . $nr => $product['product_quantity']];
            $ao += ['jednostkaTowaru' . $nr => 'szt.'];
        }

        if ($smartTab['shipping'] > 0) {
            $nr = $nr + 1;
            $ao += ['idTowaru' . $nr => 'KosztPrzesylki'];
            $ao += ['nazwaTowaru' . $nr => 'Koszt przesyłki'];
            $ao += ['wartoscTowaru' . $nr => round($smartTab['shipping'], 2)];
            $ao += ['liczbaSztukTowaru' . $nr => '1'];
            $ao += ['jednostkaTowaru' . $nr => 'szt.'];
        }

        $ao += ["liczbaSztukTowarow" => $nr];
        $ao += ["typProduktu" => "0"];
        $ao += ["wariantSklepu" => "1"];
        $ao += ["nrZamowieniaSklep" => $smartTab['orderId']];
        $ao += ["wartoscTowarow" => $smartTab['totalOrder']];
        $ao += ["imie" => $smartTab['imie']];
        $ao += ["nazwisko" => $smartTab['nazwisko']];
        $ao += ["email" => $smartTab['email']];
        $ao += ["telKontakt" => $smartTab['telKontakt']];
        $ao += ["ulica" => $smartTab['ulica']];
        $ao += ["nrDomu" => $smartTab['ulica2']];
        $ao += ["nrMieszkania" => ''];
        $ao += ["miasto" => $smartTab['miasto']];
        $ao += ["kodPocz" => $smartTab['kodPocz']];
        $ao += ["char" => "UTF"];
        $ao += ["numerSklepu" => $smartTab['shopId']];
        $ao += ["shopName" => $smartTab['shopName']];
        $ao += ["shopHttp" => $smartTab['shopHttp']];
        $ao += ["wniosekZapisany" => $smartTab['returnTrue']];
        $ao += ["wniosekAnulowany" => $smartTab['returnFalse']];
        $postData = json_encode($ao);
        return $postData;
    }

}
