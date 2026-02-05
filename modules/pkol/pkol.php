<?php
/**
 * @author    Pko Leasing
 * @copyright 2024 PKO leasing
 * @license   MIT License
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use GuzzleHttp\Exception\RequestException;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class Pkol extends PaymentModule
{
    public $client;
    public $shopID;
    public $secret;
    public $env;
    public $widget_style;
    public $widget_size;
    public $show_rates;
    public $prod_url;
    public $show_product;
    public $show_cart;
    public $show_order;
    public $test_url;
    public $endpoint;
    public $app_status;
    public $form;
    public $langID;
    public $lease_url;

    public function __construct()
    {
        $this->name = 'pkol';
        $this->tab = 'front_office_features';
        $this->version = '1.1.2';
        $this->author = 'PKO Leasing';
        $this->module_key = 'a53208bc37cfbd17f7f9dc5aa9f8cd48';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->shopID = Configuration::get('shopId');
        $this->secret = Configuration::get('secretkey');
        $this->env = Configuration::get('env');
        $this->controllers = ['validation', 'ajax'];
        $this->widget_style = Configuration::get('widget_style');
        $this->widget_size = Configuration::get('widget_size');
        $this->show_rates = Configuration::get('widget_rates') === '1' ? 'yes' : 'no';
        $this->show_product = Configuration::get('display_button_0');
        $this->show_cart = Configuration::get('display_button_1');
        $this->show_order = Configuration::get('display_button_2');
        $this->prod_url = 'https://pc.pkoleasing.pl';
        $this->test_url = 'https://pc.pkoleasing.pl';
        $this->lease_url = '/leasing/init';
        $this->endpoint = '/leasing/public/plugin/simulation';
        $this->app_status = true;
        $this->message = '';
        $this->form = null;
        $this->langID = (int) Configuration::get('PS_LANG_DEFAULT');

        if ($this->env === '1') {
            $this->test_url = $this->prod_url;
            $this->lease_url = $this->prod_url . $this->lease_url;
        } else {
            $this->lease_url = $this->test_url . $this->lease_url;
        }

        if (!$this->shopID || !$this->secret) {
            $this->app_status = false;
        }

        parent::__construct();

        $this->displayName = $this->l('PKO Leasing Online', 'pkol');
        $this->description = $this->l(
            'PKO lease financing - the ability to verify the installment and take a lease.',
            'pkol'
        );
        $this->confirmUninstall = $this->l('Uninstall the plug-in?', 'pkol');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pkol_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `shop_id` int(11) NOT NULL,
        `url` varchar(255) NOT NULL,
        `request` text NOT NULL,
        `response` text NOT NULL,
        `message` varchar(255) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return parent::install()
            && $this->registerHook('displayProductActions')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('displayShoppingCartFooter')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayCartExtraProductActions')
            && $this->registerHook('displayCrossSellingShoppingCart')
            && $this->registerHook('displayBackOfficeHeader')
            && Configuration::updateValue('pkol_shopid', '');
    }

    public function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pkol_logs`';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        if (
            !parent::uninstall()
            || !Configuration::deleteByName('shopId')
            || !Configuration::deleteByName('secretkey')
        ) {
            return false;
        }

        return true;
    }

    public function validateProduct($data, $type = 'item')
    {
        $settings_token = $this->shopID . ':' . $this->secret;
        $token = base64_encode($settings_token);
        $show_rates = $this->show_rates;
        $check_vat = json_decode($data);
        $taxRate = true;

        foreach ($check_vat->products as $v) {
            if ($v->vatRate === null) {
                $taxRate = false;
                break;
            }
        }

        $status = false;
        $price = null;

        if ($taxRate) {
            $this->client = new \GuzzleHttp\Client([
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $requestData = $data;

            try {
                $response = $this->client->post(
                    $this->test_url . $this->endpoint,
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ],
                        'body' => $data,
                        'timeout' => 3,
                        'connect_timeout' => 3,
                    ]
                );

                if ($response && $response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody());
                    if (!$data->errors && $data->validityResult === 'VALID') {
                        if ($show_rates === 'yes') {
                            $price = $data->firstInstallment->value;
                        }
                        $status = true;
                    } else {
                        $this->logRequest(
                            $this->shopID,
                            $this->test_url . $this->endpoint,
                            json_encode($requestData),
                            json_encode($data),
                            'validateProduct'
                        );
                    }
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->logRequest(
                    $this->shopID,
                    $this->test_url . $this->endpoint,
                    json_encode($requestData),
                    json_encode($e),
                    'validateProduct'
                );
            }
        }

        if ($type === 'BASKET') {
            $cart = Context::getContext()->cart;
            if ($cart->getPackageShippingCost() > 0) {
                $price = null;
                $status = false;
            }
        }

        $color_style = $this->widget_style === '0' ? 'blue' : 'white';
        $size_style = $this->widget_size === '0' ? 'xxl' : ($this->widget_size === '1' ? 'big' : 'medium');
        $file = 'pko_' . $size_style . '_' . $color_style . '_leasing.' . ($size_style === 'xxl' ? 'webp' : 'svg');

        if ($size_style === 'big' && $color_style !== 'blue' && $show_rates === 'no') {
            $file = 'pko_' . $size_style . '_' . $color_style . '_leasing.webp';
        }

        if ($status === false) {
            $show_rates = 'no';
            $file = 'pko_disabled_' . $size_style . '_leasing.' . ($size_style === 'xxl' ? 'webp' : 'svg');
        }

        $class = $color_style !== 'blue' ? 'white' : '';
        $styles = '';
        $textVisible = false;

        if ($show_rates === 'yes' && isset($price)) {
            $color = $color_style === 'blue' ? '#fff' : '#000';
            $border = '';

            if ($size_style === 'big') {
                $styles = 'position:absolute;right:4px;top:17px;line-height:17px;width:70px;display:inline-block;font-size:14px;font-family:pkobp;color:' . $color;
                $file = str_replace('.svg', '_raty.webp', $file);
            } elseif ($size_style === 'xxl') {
                $styles = 'position:absolute;right:5px;top:20px;line-height:17px;width:80px;display:inline-block;font-size:15px;font-family:pkobp;color:' . $color;
                $file = str_replace('.webp', '_raty.webp', $file);
            } elseif ($size_style === 'medium') {
                $styles = 'position: absolute;right: -9px;top: 13px;line-height: 14px;width: 70px;display: inline-block;font-size: 12px;font-family: pkobp;color:' . $color;
                $file = str_replace('.svg', '_raty.webp', $file);
            } else {
                $styles = 'position: absolute;right: -12px;top: 11px;line-height: 12px;width: 60px;display: inline-block;font-size: 11px;font-family: pkobp;color:' . $color;
                $file = str_replace('.svg', '_raty.webp', $file);
            }

            $textVisible = true;
        } else {
            $textVisible = false;
        }

        $this->context->smarty->assign([
            'status' => $status,
            'price' => $price,
            'textVisible' => $textVisible,
            'styles' => $styles,
            'class' => $class,
            'imgPath' => $this->_path . 'views/img/' . $file,
            'id' => 1,
        ]);

        $response = $this->context->smarty->fetch($this->local_path . 'views/templates/front/validate_product.tpl');

        return ['status' => $status, 'response' => $response];
    }

    private function getAttributeName($id_lang, $id_att)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT al.*
         FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
         JOIN ' . _DB_PREFIX_ . 'attribute_lang al
         ON (pac.id_attribute = al.id_attribute AND al.id_lang = ' . (int) $id_lang . ')
         WHERE pac.id_product_attribute = ' . (int) $id_att
        );
    }

    public function getProduct($pid, $att)
    {
        $p = new Product($pid, false, $this->langID);
        $link = new Link();
        $url = $link->getProductLink($pid);

        if ($att > 0) {
            $url = str_replace($pid . '-', $pid . '-' . $att . '-', $url);
        }

        $attrib = $p->getAttributeCombinationsById($att, $this->langID);
        $price_notax = str_replace(',', '', number_format($p->getPriceStatic($pid, false, $att, 2), 2));
        $price_wtax = str_replace(',', '', number_format($p->getPriceStatic($pid, true, $att, 2), 2));
        $name = $p->name;
        $att_data = null;

        foreach ($attrib as $a) {
            $id = $a['id_product_attribute'];
            $att_data = $this->getAttributeName($this->langID, $id);
        }

        if ($att_data) {
            foreach ($att_data as $s) {
                $name .= ' ' . $s['name'];
            }
        }

        return [
            'product_name' => $name,
            'price_with_tax' => $price_wtax,
            'price_without_tax' => $price_notax,
            'url' => $url,
        ];
    }

    public function previewCart()
    {
        $data = [];
        $products = Context::getContext()->cart->getProducts();
        $cart = Context::getContext()->cart;

        $product_ids = [];

        if ($this->show_cart !== 'on' || !$this->testConnection()) {
            return '';
        }

        if (empty($products)) {
            return '';
        }

        foreach ($products as $product) {
            $pid = $product['id_product'];

            $quantity = $product['cart_quantity'];
            $p = new Product($pid, false, $this->langID);

            $cat_id = $p->id_category_default;
            $price = $product['specific_prices'];

            $el = [
                'categoryId' => $cat_id,
                'quantity' => $quantity,
                'netValue' => str_replace(',', '', number_format($p->getPriceStatic($pid, false, null, 2), 2)),
                'vatRate' => $this->getRate($p->getTaxesRate()),
            ];

            array_push($data, $el);
        }

        $test = [
            'shopId' => $this->shopID,
            'widgetOption' => 1,
            'totalNetValue' => str_replace(',', '', $cart->getOrderTotal(false)),
            'uniqueItemQuantity' => count($products),
            'source' => 'BASKET',
            'products' => $data,
        ];

        $data = json_encode($test);

        $check_product = $this->validateProduct($data, 'BASKET');

        if ($product) {
            $link = new Link();
            $url = $link->getProductLink($product);
        }

        $this->smarty->assign('data', $check_product);

        if (!$check_product['status']) {
            return;
        } else {
            $this->checked_product = $check_product;
            $n = 1;
            $type = 'BASKET';
            $url = _PS_BASE_URL_ . __PS_BASE_URI__ . 'koszyk?action=show';
            $total = 0;
            $total_wt = 0;

            foreach ($products as $product) {
                $pid = $product['id_product'];
                $p = new Product($pid, false, $this->langID);
                $att_id = null;

                if ($product['id_product_attribute'] > 0) {
                    $att_id = $product['id_product_attribute'];
                }

                if ($att_id) {
                    $att = $att_id;
                    $product_data = $this->getProduct($pid, $att_id);
                    $p->name = $product_data['product_name'];
                    $price_without_tax = $product_data['price_without_tax'];
                    $price_with_tax = $product_data['price_with_tax'];
                } else {
                    $att = null;
                    $price_without_tax = str_replace(',', '', number_format($p->getPriceStatic($pid, false, $att, 2), 2));
                    $price_with_tax = str_replace(',', '', number_format($p->getPriceStatic($pid, true, $att, 2), 2));
                }

                $quantity = $product['cart_quantity'];
                $cat_id = $p->id_category_default;
                $price = $product['specific_prices'];
                $p_quant = $quantity;

                $productData[] = [
                    'productName' => $p->name,
                    'productPrice' => $price_with_tax,
                    'productNetPrice' => $price_without_tax,
                    'productQuantity' => $p_quant,
                    'productCategory' => $cat_id,
                    'productVatRate' => $this->getRate($p->getTaxesRate()),
                    'productAvatarUrl' => $this->getProductLink($p),
                ];

                $total = $total + $price_without_tax * $p_quant;
                $total_wt = $total_wt + $price_with_tax * $p_quant;
            }

            $link = Context::getContext()->link->getPageLink('cart', true, Context::getContext()->language->id);
            $link .= '?action=show';

            $this->context->smarty->assign([
                'totalValue' => str_replace(',', '', number_format((float) $total_wt, 2, '.', '')),
                'totalNetValue' => str_replace(',', '', number_format((float) $total, 2, '.', '')),
                'source' => $type,
                'shopId' => $this->shopID,
                'returnLink' => $link,
                'uniqueItemQuantity' => count($productData),
                'products' => $productData,
            ]);

            $this->form = $this->context->smarty->fetch($this->local_path . 'views/templates/front/preview_cart.tpl');

            return ['result' => $this->form, 'button' => $check_product['response']];
        }
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        $data = [];
        $products = Context::getContext()->cart->getProducts();
        $cart = Context::getContext()->cart;

        if ($this->show_cart !== 'on' || !$this->testConnection() || empty($products)) {
            return '';
        }

        foreach ($products as $product) {
            $pid = $product['id_product'];
            $p = new Product($pid, false, $this->langID);
            $att_id = $product['id_product_attribute'] > 0 ? $product['id_product_attribute'] : null;

            if ($att_id) {
                $product_data = $this->getProduct($pid, $att_id);
                $p->name = $product_data['product_name'];
                $price_without_tax = $product_data['price_without_tax'];
                $price_with_tax = $product_data['price_with_tax'];
            } else {
                $price_without_tax = str_replace(
                    ',',
                    '',
                    number_format($p->getPriceStatic($pid, false, $att_id, 2), 2)
                );
                $price_with_tax = str_replace(
                    ',',
                    '',
                    number_format($p->getPriceStatic($pid, true, $att_id, 2), 2)
                );
            }

            $data[] = [
                'productName' => $p->name,
                'productPrice' => $price_with_tax,
                'productAvatarUrl' => $this->getProductLink($p),
                'netValue' => $price_without_tax,
                'quantity' => $product['cart_quantity'],
                'categoryId' => $p->id_category_default,
                'vatRate' => $this->getRate($p->getTaxesRate()),
            ];
        }

        $test = [
            'shopId' => $this->shopID,
            'widgetOption' => 1,
            'totalNetValue' => str_replace(',', '', $cart->getOrderTotal(false)),
            'uniqueItemQuantity' => count($products),
            'source' => 'BASKET',
            'products' => $data,
        ];

        $checkProduct = $this->validateProduct(json_encode($test), 'BASKET');

        if (!$checkProduct['status']) {
            return false;
        }

        $total = array_sum(array_column($data, 'netValue'));
        $total_wt = array_sum(array_column($data, 'productPrice'));

        $link = new Link();

        $this->context->smarty->assign([
            'shopId' => $this->shopID,
            'returnLink' => _PS_BASE_URL_ . __PS_BASE_URI__ . 'koszyk?action=show',
            'uniqueItemQuantity' => count($products),
            'products' => $data,
            'totalValue' => number_format($total_wt, 2, '.', ''),
            'totalNetValue' => number_format($total, 2, '.', ''),
            'leaseUrl' => $this->lease_url,
            'checkProductResponse' => htmlspecialchars_decode($checkProduct['response'], ENT_QUOTES), // Dekoduj encje HTML
            'moduleDir' => $link->getModuleLink('pkol', 'ajax', []),
        ]);

        $html = $this->context->smarty->fetch($this->local_path . 'views/templates/front/shopping_cart_footer.tpl');

        return $html;
    }

    public function hookDisplayShoppingCart($params)
    {
        return $this->display(__FILE__, 'button.tpl');
    }

    public function hookDisplayCrossSellingShoppingCart($params)
    {
        return '';
    }

    public function getRate($tax)
    {
        $taxes = [
            '0%' => 1,
            '7%' => 2,
            '8%' => 3,
            '22%' => 4,
            '23%' => 5,
            '23' => 5,
            '22' => 4,
            '8' => 3,
            '7' => 2,
            '0' => 1,
        ];

        return $taxes[$tax] ?? null;
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        global $product;

        if (!$this->app_status || $this->show_product !== 'on' || !$this->testConnection()) {
            return '';
        }

        if (Configuration::get('display_button_0') !== 'on') {
            return '';
        }

        $pid = Tools::getValue('id_product');
        $att_id = Tools::getValue('id_product_attribute');
        $product = new Product($pid, false, $this->langID);

        if ($att_id) {
            $product_data = $this->getProduct($pid, $att_id);
            $product->name = $product_data['product_name'];
            $price_without_tax = $product_data['price_without_tax'];
            $price_with_tax = $product_data['price_with_tax'];
        } else {
            $price_without_tax = str_replace(
                ',',
                '',
                number_format($product->getPriceStatic($pid, false, null, 2), 2)
            );
            $price_with_tax = str_replace(
                ',',
                '',
                number_format($product->getPriceStatic($pid, true, null, 2), 2)
            );
        }

        if ($product->getQuantity($product->id) == 0) {
            return '';
        }

        $cat_id = $product->id_category_default;

        $test = [
            'shopId' => $this->shopID,
            'widgetOption' => 1,
            'totalNetValue' => $price_without_tax,
            'uniqueItemQuantity' => 1,
            'source' => 'ITEM',
            'products' => [
                [
                    'categoryId' => $cat_id,
                    'quantity' => 1,
                    'netValue' => $price_without_tax,
                    'vatRate' => $this->getRate($product->getTaxesRate()),
                ],
            ],
        ];

        $checkProduct = $this->validateProduct(json_encode($test));

        if (!$checkProduct['status']) {
            return '';
        }

        $url = (new Link())->getProductLink($product);
        if ($att_id) {
            $url = str_replace($pid . '-', $pid . '-' . $att_id . '-', $url);
        }

        $link = new Link();

        $this->context->smarty->assign([
            'shopId' => $this->shopID,
            'returnLink' => $url,
            'uniqueItemQuantity' => 1,
            'productName1' => $product->name,
            'productPrice1' => $price_with_tax,
            'productNetPrice1' => $price_without_tax,
            'productQuantity1' => 1,
            'productCategory1' => $cat_id,
            'productVatRate1' => $this->getRate($product->getTaxesRate()),
            'totalValue' => $price_with_tax,
            'totalNetValue' => $price_without_tax,
            'source' => 'ITEM',
            'leaseUrl' => $this->lease_url,
            'moduleDir' => $link->getModuleLink('pkol', 'ajax', []),
            'checkProductResponse' => htmlspecialchars_decode($checkProduct['response'], ENT_QUOTES),
        ]);

        $this->context->smarty->assign([
            'shopId' => $this->shopID,
            'leaseUrl' => $this->lease_url,
            'returnLink' => (new Link())->getProductLink($product),
            'uniqueItemQuantity' => 1,
            'productName1' => $product->name,
            'productPrice1' => $price_with_tax,
            'productNetPrice1' => $price_without_tax,
            'productQuantity1' => 1,
            'productCategory1' => $cat_id,
            'productVatRate1' => $this->getRate($product->getTaxesRate()),
            'productAvatarUrl1' => $this->getProductLink($product),
            'totalValue' => $price_with_tax,
            'totalNetValue' => $price_without_tax,
            'source' => 'ITEM',
            'moduleDir' => $link->getModuleLink('pkol', 'ajax', []),
            'productId' => $pid,
        ]);

        $this->form = $this->context->smarty->fetch($this->local_path . 'views/templates/front/product_additional_info_form.tpl');

        $this->context->smarty->assign([
            'moduleDir' => $link->getModuleLink('pkol', 'ajax', []),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/front/product_additional_info.tpl');
    }

    public function hookDisplayFooterProduct($params)
    {
        if ($this->form) {
            echo $this->form;
        }
    }

    public function hookDisplayProductActions($params)
    {
        // Intentionally left empty as no actions are defined
    }

    public function hookDisplayCartExtraProductActions($params)
    {
        // Intentionally left empty as no actions are defined
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') !== $this->name) {
            return;
        }
        $this->context->controller->addJquery();
        $this->context->controller->addJS($this->_path . 'views/js/custom.js');
        Media::addJsDef([
            'translations' => [
                'testConnection' => $this->l('Test connection'),
                'validConfig' => $this->l('Configuration is valid'),
                'invalidConfig' => $this->l('Enter valid configuration'),
            ],
        ]);
    }

    public function verifyConnection()
    {
        $settings_token = $this->shopID . ':' . $this->secret;
        $token = base64_encode($settings_token);

        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
            'exceptions' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $test = [
            'shopId' => 1,
            'widgetOption' => 2,
            'totalNetValue' => 1000,
            'uniqueItemQuantity' => 1,
            'source' => 'BASKET',
            'products' => [
                [
                    'categoryId' => '475',
                    'quantity' => 1,
                    'netValue' => 1000,
                    'vatRate' => 0,
                ],
            ],
        ];

        try {
            $response = $this->client->post(
                $this->test_url . $this->endpoint,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                        'verify' => false,
                    ],
                    'body' => json_encode($test),
                    'timeout' => 3,
                    'connect_timeout' => 3,
                ]
            );

            if ($response && $response->getStatusCode() === 200) {
                $this->message = 'Połączenie powiodło się';
                $this->app_status = true;
            } else {
                $this->logRequest(
                    $this->shopID,
                    $this->test_url . $this->endpoint,
                    json_encode($test),
                    json_decode($response->getBody()),
                    'verifyConnection'
                );
            }
        } catch (RequestException $e) {
            $this->message = $this->l('The connection failed, check the configuration again.');
            $this->app_status = false;
            $this->logRequest(
                $this->shopID,
                $this->test_url . $this->endpoint,
                json_encode($test),
                json_encode($e->getResponse()->getBody()->getContents()),
                'verifyConnection'
            );
        }

        return $this->app_status;
    }

    public function testConnection()
    {
        $settings_token = $this->shopID . ':' . $this->secret;
        $token = base64_encode($settings_token);

        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
            'exceptions' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
                'verify' => false,
            ],
        ]);

        $test = [
            'shopId' => 1,
            'widgetOption' => 2,
            'totalNetValue' => 1000,
            'uniqueItemQuantity' => 1,
            'source' => 'BASKET',
            'products' => [
                [
                    'categoryId' => '475',
                    'quantity' => 1,
                    'netValue' => 1000,
                    'vatRate' => 0,
                ],
            ],
        ];

        try {
            $response = $this->client->post(
                $this->test_url . $this->endpoint,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                        'verify' => false,
                    ],
                    'body' => json_encode($test),
                    'timeout' => 5,
                    'connect_timeout' => 5,
                ]
            );

            if ($response && $response->getStatusCode() === 200) {
                $this->message = $this->l('The connection succeeded.');
                $this->app_status = true;
            } else {
                $this->message = $this->l('Connection failed, check the configuration again.');
                $this->app_status = false;
            }
        } catch (RequestException $e) {
            $this->message = $this->l('Connection failed, check the configuration again.');
            $this->app_status = false;
        }

        return $this->app_status;
    }

    public function getContent()
    {
        if (Tools::isSubmit('download_logs')) {
            $this->downloadLogs();
        }

        $disableFieldsValue = null;
        $this->message = '';
        $test = $_GET['test'] ?? false;

        if ($test === '1') {
            $settings_token = $this->shopID . ':' . $this->secret;
            $token = base64_encode($settings_token);

            $this->client = new \GuzzleHttp\Client([
                'verify' => false,
                'exceptions' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            $testData = [
                'shopId' => 1,
                'widgetOption' => 2,
                'totalNetValue' => 1000,
                'uniqueItemQuantity' => 1,
                'source' => 'BASKET',
                'products' => [
                    [
                        'categoryId' => '475',
                        'quantity' => 1,
                        'netValue' => 1000,
                        'vatRate' => 0,
                    ],
                ],
            ];

            try {
                $response = $this->client->post(
                    $this->test_url . $this->endpoint,
                    [
                        'body' => json_encode($testData),
                        'connect_timeout' => 5,
                    ]
                );

                if ($response && $response->getStatusCode() === 200) {
                    $this->message = $this->l('Connection succeeded.');
                    $this->app_status = true;
                    $disableFieldsValue = 'ok';
                } else {
                    $this->logRequest(
                        $this->shopID,
                        $this->test_url . $this->endpoint,
                        json_encode($testData),
                        json_decode($response->getBody()),
                        'getContent'
                    );
                    $this->message = $this->l('Connection failed, check the configuration again.');
                    $this->app_status = false;
                    $disableFieldsValue = '1';
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->message = $this->l('A Guzzle error occurred. Please ensure PHP cURL is enabled and check the configuration or try again later.');
                $this->app_status = false;
                $disableFieldsValue = '1';
                $this->logRequest(
                    $this->shopID,
                    $this->test_url . $this->endpoint,
                    '',
                    '',
                    'getContent - RequestException'
                );
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $this->message = $this->l('Connection failed: Unable to connect to the server. Please check the URL or network.');
                $this->app_status = false;
                $disableFieldsValue = '1';

                $this->logRequest(
                    $this->shopID,
                    $this->test_url . $this->endpoint,
                    '',
                    '',
                    'getContent - ConnectException'
                );
            } catch (RequestException $e) {
                $this->message = $this->l('Connection failed, check the configuration again.');
                $this->app_status = false;
                $disableFieldsValue = '1';
            }
        }

        if (Tools::isSubmit('submit' . $this->name)) {
            $shopId = (string) Tools::getValue('shopId');
            $secretkey = Tools::getValue('secretkey');
            $option0 = Tools::getValue('display_button_0');
            $option1 = Tools::getValue('display_button_1');
            $option2 = Tools::getValue('display_button_2');
            $env = Tools::getValue('env');
            $w_style = Tools::getValue('widget_style');
            $w_size = Tools::getValue('widget_size');
            $w_rates = Tools::getValue('widget_rates');

            if (!$shopId || empty($secretkey) || !Validate::isGenericName($shopId)) {
                $this->message = $this->l('Fill in the required field');
            } else {
                Configuration::updateValue('shopId', $shopId);
                Configuration::updateValue('secretkey', $secretkey);

                if ($this->verifyConnection()) {
                    Configuration::updateValue('display_button_0', $option0);
                    Configuration::updateValue('display_button_1', $option1);
                    Configuration::updateValue('display_button_2', $option2);
                }

                Configuration::updateValue('env', $env);
                Configuration::updateValue('widget_style', $w_style);
                Configuration::updateValue('widget_size', $w_size);
                Configuration::updateValue('widget_rates', $w_rates);

                $this->message = $this->l('Changes saved successfully.');
            }
        }

        $this->context->smarty->assign([
            'disable_fields' => $disableFieldsValue,
            'message' => $this->message,
            'form' => $this->displayForm(),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }

    public function getOptions()
    {
        return [
            [
                'id_checkbox_options' => 0,
                'checkbox_options_name' => $this->l('Whether to display the button on the product card.'),
            ],
            [
                'id_checkbox_options' => 1,
                'checkbox_options_name' => $this->l('Whether to display the button in the shopping cart.'),
            ],
            [
                'id_checkbox_options' => 2,
                'checkbox_options_name' => $this->l('Do display the payment method in the shopping cart.'),
            ],
        ];
    }

    public function displayForm()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('ID shop'),
                    'name' => 'shopId',
                    'size' => 20,
                    'maxlength' => 10,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Secret key'),
                    'name' => 'secretkey',
                    'size' => 20,
                    'required' => true,
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Display payment method'),
                    'name' => 'display_button',
                    'disabled' => true,
                    'values' => [
                        'query' => $this->getOptions(),
                        'id' => 'id_checkbox_options',
                        'name' => 'checkbox_options_name',
                        'expand' => [
                            'print_total' => count($this->getOptions()),
                            'default' => 'show',
                            'show' => [
                                'text' => $this->l('show'),
                                'icon' => 'plus-sign-alt',
                            ],
                            'hide' => [
                                'text' => $this->l('hide'),
                                'icon' => 'minus-sign-alt',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Select environment'),
                    'name' => 'env',
                    'class' => 't',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'dev',
                            'value' => 0,
                            'label' => $this->l('Test'),
                        ],
                        [
                            'id' => 'prod',
                            'value' => 1,
                            'label' => $this->l('Production'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Select widget style'),
                    'name' => 'widget_style',
                    'class' => 't',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'blue',
                            'value' => 0,
                            'label' => $this->l('Dark color schema'),
                        ],
                        [
                            'id' => 'white',
                            'value' => 1,
                            'label' => $this->l('Light color schema'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Choose the size of the widghet'),
                    'name' => 'widget_size',
                    'class' => 't',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'big',
                            'value' => 0,
                            'label' => $this->l('Big size'),
                        ],
                        [
                            'id' => 'medium',
                            'value' => 1,
                            'label' => $this->l('Medium size'),
                        ],
                        [
                            'id' => 'small',
                            'value' => 2,
                            'label' => $this->l('Small size'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Display widget: "installment from"'),
                    'name' => 'widget_rates',
                    'class' => 't',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'v_yes',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'v_no',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
            'buttons' => [
                'download_logs' => [
                    'title' => $this->l('Download logs'),
                    'name' => 'submitDownloadLogs',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'process-icon-download',
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&download_logs=1&token='
                        . Tools::getAdminTokenLite('AdminModules'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save'
                    . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        $helper->fields_value['shopId'] = Configuration::get('shopId');
        $helper->fields_value['secretkey'] = Configuration::get('secretkey');
        $helper->fields_value['display_button_0'] = Configuration::get('display_button_0');
        $helper->fields_value['display_button_1'] = Configuration::get('display_button_1');
        $helper->fields_value['display_button_2'] = Configuration::get('display_button_2');
        $helper->fields_value['env'] = Configuration::get('env');
        $helper->fields_value['widget_style'] = Configuration::get('widget_style');
        $helper->fields_value['widget_size'] = Configuration::get('widget_size');
        $helper->fields_value['widget_rates'] = Configuration::get('widget_rates');

        return $helper->generateForm($fields_form);
    }

    public function hookPaymentOptions()
    {
        $products = Context::getContext()->cart->getProducts();

        if ($this->show_order !== 'on' || !$this->testConnection()) {
            return;
        }

        $data = [];
        $cart = Context::getContext()->cart;

        if ($cart->getPackageShippingCost() > 0) {
            return;
        }

        foreach ($products as $product) {
            $pid = $product['id_product'];
            $quantity = $product['cart_quantity'];
            $p = new Product($pid, false, $this->langID);
            $cat_id = $p->id_category_default;

            $el = [
                'categoryId' => $cat_id,
                'quantity' => $quantity,
                'netValue' => str_replace(',', '', number_format($p->getPriceStatic($pid, false, null, 2), 2)),
                'vatRate' => $this->getRate($p->getTaxesRate()),
            ];
            $data[] = $el;
        }

        $test = [
            'shopId' => $this->shopID,
            'widgetOption' => 1,
            'totalNetValue' => str_replace(',', '', $cart->getOrderTotal(false)),
            'uniqueItemQuantity' => count($products),
            'source' => 'BASKET',
            'products' => $data,
        ];

        $data = json_encode($test);
        $check_product = $this->validateProduct($data, 'BASKET');

        $link = new Link();
        $url = $link->getProductLink($product);

        $this->smarty->assign('data', $check_product);

        if (!$check_product['status']) {
            return;
        }

        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
            ->setCallToActionText($this->l('PKO Leasing Online'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true));

        return [$newOption];
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        $this->context->controller->registerJavascript(
            'module-' . $this->name . '-simple-lib',
            'modules/' . $this->name . '/views/js/pkol.js',
            [
                'priority' => 999,
                'attribute' => 'async',
            ]
        );
    }

    public function logRequest($shop_id, $url, $request, $response, $message)
    {
        $sql_delete = 'DELETE FROM `' . _DB_PREFIX_ . 'pkol_logs`
                   WHERE `created_at` < NOW() - INTERVAL 7 DAY';
        Db::getInstance()->execute($sql_delete);

        $log_count = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'pkol_logs`');

        if ($log_count > 1000) {
            $sql_delete_excess_logs = 'DELETE FROM `' . _DB_PREFIX_ . 'pkol_logs`
                                   WHERE `id` IN (
                                       SELECT `id` FROM (
                                           SELECT `id` FROM `' . _DB_PREFIX_ . 'pkol_logs`
                                           ORDER BY `created_at` ASC
                                           LIMIT ' . ($log_count - 1000) . '
                                       ) as tmp
                                   )';
            Db::getInstance()->execute($sql_delete_excess_logs);
        }

        $sql_insert = 'INSERT INTO `' . _DB_PREFIX_ . "pkol_logs`
                (`shop_id`, `url`, `request`, `response`, `message`, `created_at`)
                   VALUES ('" . pSQL($shop_id) . "', '" . pSQL($url) . "', '" . pSQL($request) . "', '"
            . pSQL($response) . "', '" . pSQL($message) . "', NOW())";

        Db::getInstance()->execute($sql_insert);
    }

    public function downloadLogs()
    {
        $logs = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'pkol_logs` ORDER BY `created_at` DESC');

        if (!$logs) {
            return false;
        }

        $filename = 'pkol_logs_' . $this->shopID . '_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['ID', 'Shop ID', 'URL', 'Request', 'Response', 'Message', 'Created At'], ';');

        foreach ($logs as $log) {
            fputcsv($output, $log, ';');
        }

        fclose($output);
        exit;
    }

    public function getProductLink($product)
    {
        $link = new Link();
        $protocol = Tools::getProtocol();
        $imageId = $product->getCover($product->id)['id_image'];
        return $protocol . $link->getImageLink($product->link_rewrite, $imageId, 'large_default');
    }
}
