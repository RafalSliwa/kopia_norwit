<?php

/**
 * Roja45QuotationsPro.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Roja45QuotationsPro extends Module
{
    const TOPIC = 'UNSET';
    const CODE_ERROR = 'ERROR';
    const CODE_SUCCESS = 'SUCCESS';
    const DEFAULT_PRECISION = 6;
    public $html;
    protected static $cache_enabled;

    public function __construct()
    {
        //RojaQuotationLogger::trace('Roja45QuotationsPro: __construct');
        $this->name = 'roja45quotationspro';
        $this->tab = 'front_office_features';
        $this->version = '1.5.57';
        $this->author = 'Inter-Soft';
        $this->source = 'prestashop';
        $this->is_eu_compatible = 1;
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->tabClassName = 'AdminQuotationsPro';

        parent::__construct();

        $this->controllers = array(
            'QuotationsProFront',
        );

        $this->displayName = $this->l('ToolE: Quotations Pro');
        $this->description = $this->l(
            'Enable product quotations for customers with custom forms and quotation response system.'
        );
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->confirmUninstall = $this->l('Are you sure you want to delete all saved details?');
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = 'd88ff1bf238941b1c79d71246d5264b6';
        $this->author_address = '0xF3D82474F7BE4238658Bac2a21e8aD338B86447f';
        $this->prestashop_product_id = '23609';
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHooks()
            || !$this->installDb()
            || !$this->installTabs()
            || !$this->setGlobalVars()
            || !$this->populateDefaultData()
        ) {
            $this->uninstallDb();
            parent::uninstall();
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!$this->uninstallDb()
            || !$this->uninstallTabs()
            || !$this->removeGlobalVars()
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    public static function clearAllCached()
    {
        $module = Module::getInstanceByName('roja45quotationspro');
        $module->clearCache('displayFooter', 'displayFooter');
        $module->clearCache('displayShoppingCartFooter', 'displayShoppingCartFooter');
        $module->clearCache('displayProductButtons', 'displayProductButtons');
        $module->clearCache('displayBackOfficeTop', 'displayBackOfficeTop');
        $module->clearCache('displayEnabledIndicator', 'displayEnabledIndicator');
        $module->clearCache('displayCustomerAccount', 'displayCustomerAccount');
        $module->clearCache('displayRoja45ProductList', 'displayRoja45ProductList');
        $module->clearCache('displayRoja45ProductListFlag', 'displayRoja45ProductListFlag');
        $module->clearCache('displayRoja45ProductListQuoteButton', 'displayRoja45ProductListQuoteButton');
        $module->clearCache('displayRoja45ProductListCartButton', 'displayRoja45ProductListCartButton');
        $module->clearCache('displayNav', 'displayNav');
        $module->clearCache('displayRoja45ProductPageIndicator', 'displayRoja45ProductPageIndicator');
        $module->clearCache('displayRoja45ProductPageIndicator', 'displayRoja45ProductPageIndicator');

        //$module->clearCache('tab', 'homefeatured-tab', false);
        //$module->clearCache('homefeatured', '', false);
        $module->clearCache('*', 'ps_featuredproducts', false);
    }

    public function clearCache($template, $cache_id, $this_module = true)
    {
        if ($this_module) {
            if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                $this->_clearCache(
                    'module:' . $this->name . '/views/templates/hook/PS17_' . $template . '.tpl',
                    $cache_id
                );
            } else {
                $this->_clearCache($template . '.tpl');
            }
        } else {
            $this->_clearCache(
                $template,
                $cache_id
            );
        }
    }

    public function getContent()
    {
        $this->processSubmit();
        $this->html .= $this->renderModuleForm();
        return $this->html;
    }

    public function renderWidget($hookName = null, array $configuration = array())
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $this->smarty->assign($variables);
            return $this->display(__FILE__, $hookName . '.tpl');
        } else {
            if (!$this->isCached(
                'module:' . $this->name . '/views/templates/hook/PS17_' . $hookName . '.tpl',
                $this->getCacheId($hookName)
            )) {
                $this->smarty->assign($variables);
            }
            return $this->fetch(
                'module:' . $this->name . '/views/templates/hook/PS17_' . $hookName . '.tpl',
                $configuration['cache_id']
            );
        }
    }

    public function getWidgetVariables($hookName = null, array $configuration = array())
    {
        if (in_array($hookName, array(
            'displayProductButtons',
            'displayRoja45AddToQuoteButton',
        ))) {
            $id_product_attribute = 0;
            if (is_array($configuration['product'])) {
                $configuration['product'] = (object) $configuration['product'];
            }
            $id_product = Tools::getValue('id_product');
            if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                $id_product_attribute = $configuration['product']->id_product_attribute;
            } else {
                $product = new Product($id_product);
                $attributes_groups = $product->getAttributesGroups($this->context->language->id);
                if (is_array($attributes_groups) && $attributes_groups) {
                    foreach ($attributes_groups as $row) {
                        if ($row['default_on']) {
                            $id_product_attribute = (int) $row['id_product_attribute'];
                            //$minimal_quantity = (int)$row['minimal_quantity'];
                        }
                    }
                }
            }

            $minimal_quantity = $configuration['product']->minimal_quantity;
            if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
                return array(
                    'roja45_quotation_enablequotecart' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
                    'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                    'roja45quotationspro_touchspin' => (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
                    ),
                    'product' => $configuration['product'],
                    'id_product_attribute' => $id_product_attribute,
                    'minimal_quantity' => $minimal_quantity,
                    'roja45quotationspro_iconpack' => (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_ICON_PACK'
                    ),
                );
            }
        } elseif (in_array($hookName, array(
            'displayNav',
            'displayNav1',
            'displayNav2',
            'displayTop',
            'displayRoja45QuoteCart',
            'displayRoja45MobileQuoteCart',
        ))) {
            $request = QuotationRequest::getInstance();
            if ($request && !$request->requested) {
                $products = $request ? $request->getProducts() : array();
                $number_products = $request ? QuotationRequest::getNumberOfProducts($request->id) : 0;
            } else {
                $products = array();
                $number_products = 0;
            }

            $request_link = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'quoteSummary',
                ),
                true
            );

            return array(
                'roja45quotationspro_enable_inquotenotify' => 1,
                'roja45_quotation_enablequotecart' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
                'roja45quotationspro_enable_quote_dropdown' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY'
                ),
                'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                'request_link' => $request_link,
                'nbr_products' => count($products),
                'requested_products' => $products,
                'request_qty' => $number_products,
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );
        } elseif (preg_match('/^displayProductListAddToQuoteButton\d*$/', $hookName)) {
            $min_quantity = 1;
            if (isset($configuration['product']->product_attribute_minimal_quantity) &&
                $configuration['product']->product_attribute_minimal_quantity >= 1) {
                $min_quantity = $configuration['product']->product_attribute_minimal_quantity;
            }
            return array(
                'min_quantity' => $min_quantity,
                'minimal_quantity' => $min_quantity,
                'product' => $configuration['product'],
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );
        } elseif (preg_match('/^displayEnabledIndicator\d*$/', $hookName)) {
            $minimum_quantity = 1;
            if (isset($configuration['product']['product_attribute_minimal_quantity']) &&
                $configuration['product']['product_attribute_minimal_quantity'] >= 1) {
                $minimum_quantity = (int) $configuration['product']['product_attribute_minimal_quantity'];
            }
            return array(
                'product' => $configuration['product'],
                'minimum_quantity' => $minimum_quantity,
            );
        } elseif (in_array($hookName, array(
            'displayReassurance',
        ))) {
            return array(
                'id_cart' => $this->context->cart->id,
                'controller' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'convertCartToQuote',
                    ),
                    true
                ),
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
            );
        } elseif (preg_match('/^displayRoja45ProductList\d*$/', $hookName)) {
            $minimum_quantity = 1;
            if (isset($configuration['product']['product_attribute_minimal_quantity']) &&
                $configuration['product']['product_attribute_minimal_quantity'] >= 1) {
                $minimum_quantity = (int) $configuration['product']['product_attribute_minimal_quantity'];
            }
            return array(
                'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                'id_language' => $this->context->language->id,
                'product' => $configuration['product'],
                'roja45_quotation_enablequotecart' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
                'id_product' => $configuration['product']['id_product'],
                'id_product_attribute' => $configuration['product']['id_product_attribute'],
                'minimum_quantity' => $minimum_quantity,
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );
        } elseif (preg_match('/^displayCustomerAccount\d*$/', $hookName)) {
            return array(
                'request_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'quoteSummary',
                    ),
                    true
                ),
                'id_language' => $this->context->language->id,
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );
        } elseif (preg_match('/^quotecart_modal\d*$/', $hookName)) {
            return array(
                'product' => $configuration['product'],
                'nbr_products' => count($configuration['products']),
                'roja45quotationspro_enable_inquotenotify' => 1,
                'roja45quotationspro_enablequotecart' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'
                ),
                'request_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'quoteSummary',
                    ),
                    true
                ),
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );
        } elseif (preg_match('/^displayFooter\d*$/', $hookName)) {
            return array(
                'roja45quotationspro_enable_inquotenotify' => 1,
                'roja45quotationspro_enablequotecart' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'
                ),
            );
        } elseif (preg_match('/^displayRoja45ProductListFlag\d*$/', $hookName)) {
            return array(
                'product' => $configuration['product'],
                'roja45quotationspro_label_position' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'
                ),
            );
        } elseif (preg_match('/^displayRoja45ProductListCartButton\d*$/', $hookName)) {
            $min_quantity = 1;
            if (isset($configuration['product']->product_attribute_minimal_quantity) &&
                $configuration['product']->product_attribute_minimal_quantity >= 1) {
                $min_quantity = $configuration['product']->product_attribute_minimal_quantity;
            }
            return array(
                'product' => $configuration['product'],
                'minimum_quantity' => $min_quantity,
            );
        } elseif (preg_match('/^displayBackOfficeHeader\d*$/', $hookName)) {
            return array(
                'roja45quotationspro_expiry_enable' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES'
                ),
                'roja45quotationspro_expiry_warning' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'
                ),
                'roja45quotationspro_expiry_alert' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'
                ),
            );
        } elseif (preg_match('/^displayRoja45ProductListQuoteButton\d*$/', $hookName)) {
            $controller_url = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'token' => Tools::getToken(false),
                ),
                true
            );
            $min_quantity = 1;
            if (isset($configuration['product']->product_attribute_minimal_quantity) &&
                $configuration['product']->product_attribute_minimal_quantity >= 1) {
                $min_quantity = $configuration['product']->product_attribute_minimal_quantity;
            }
            return array(
                'roja45quotationspro_controller' => $controller_url,
                'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                'roja45_quotation_enablequotecart' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
                'roja45quotationspro_touchspin' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
                ),
                'min_quantity' => $min_quantity,
                'minimal_quantity' => $min_quantity,
                'product' => $configuration['product'],
            );
        } elseif (preg_match('/^displayRoja45ProductPageCartButton\d*$/', $hookName)) {
            $controller_url = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'token' => Tools::getToken(false),
                ),
                true
            );
            $min_quantity = 1;
            if (isset($configuration['product']->product_attribute_minimal_quantity) &&
                $configuration['product']->product_attribute_minimal_quantity >= 1) {
                $min_quantity = $configuration['product']->product_attribute_minimal_quantity;
            }
            $params = array(
                'roja45quotationspro_controller' => $controller_url,
                'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                'roja45_quotation_enablequotecart' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
                'roja45_quotation_hideaddtocart' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_HIDEADDTOCART'),
                'roja45quotationspro_touchspin' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
                ),
                'min_quantity' => $min_quantity,
                'minimal_quantity' => $min_quantity,
                'id_product_attribute' => 0,
                'product' => $configuration['product'],
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            );

            if (isset($configuration['custom_params'])) {
                $params = array_merge($params, $configuration['custom_params']);
            }
            return $params;
        } elseif (preg_match('/^displayRoja45ProductPageQuoteButton\d*$/', $hookName)) {
            if (!is_array($configuration['product'])) {
                $id_product = $configuration['product']->id;
                $id_product_attribute = 0;
                $minimal_quantity = $configuration['product']->minimal_quantity;
                $product = new Product($id_product);
                $attributes_groups = $product->getAttributesGroups($this->context->language->id);
                if (is_array($attributes_groups) && $attributes_groups) {
                    foreach ($attributes_groups as $row) {
                        if ($row['default_on']) {
                            $id_product_attribute = (int) $row['id_product_attribute'];
                            $minimal_quantity = (int) $row['minimal_quantity'];
                        }
                    }
                }
            } else {
                $id_product = $configuration['product']['id_product'];
                $id_product_attribute = $configuration['product']['id_product_attribute'];
                $product = new Product($id_product);
                $minimal_quantity = $configuration['product']['minimal_quantity'];
            }
            if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
                $params = array(
                    'roja45_quotation_enablequotecart' => Configuration::get(
                        'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'
                    ),
                    'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
                    'roja45_quotation_hideaddtocart' => (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_HIDEADDTOCART'
                    ),
                    'roja45quotationspro_touchspin' => (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
                    ),
                    'product' => $configuration['product'],
                    'id_product_attribute' => $id_product_attribute,
                    'minimal_quantity' => $minimal_quantity,
                );
                if (isset($configuration['custom_params'])) {
                    $params = array_merge($params, $configuration['custom_params']);
                }
                return $params;
            }
        } elseif (in_array($hookName, array(
            'displayShoppingCart',
            'displayShoppingCartFooter',
        ))) {
            $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            $quotation = new RojaQuotation($id_roja45_quotation);
            $cart_products = $this->context->cart->getProducts();
            $params = array(
                'roja45_modify_quote_allowed' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION'
                ),
                'roja45_contains_quote' => 0,
                'roja45_convert_to_quote' => 0,
                'roja45_download_pdf' => 0,
                'roja45_email_pdf' => 0,
                'roja45_contains_products' => count($cart_products),
                'roja45_cartmodified' => (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_QUOTEMODIFIED'
                ),
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
            );

            if (Validate::isLoadedObject($quotation) &&
                (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART')
            ) {
                $params['roja45_contains_quote'] = $quotation->id;
            }

            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION')) {
                $params['roja45_convert_to_quote'] = 1;
                $params['id_cart'] = $this->context->cart->id;
                $params['request_quote_controller'] = $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'convertCartToQuote',
                    ),
                    true
                );
            }

            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION')) {
                $params['roja45_download_pdf'] = 1;
                $params['id_cart'] = $this->context->cart->id;
                $params['download_pdf_controller'] = $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'downloadRequestPDF',
                    ),
                    true
                );
            }
            return $params;
        } elseif (preg_match('/^displayBackOfficeFooter\d*$/', $hookName)) {
            if (version_compare(_PS_VERSION_, '1.6.0.9', '<=') === true) {
                return array(
                    'requires_multiselect' => 1,
                );
            }
        } elseif (preg_match('/^displayProductButtonsRegistration\d*$/', $hookName)) {
            return array(
                'login_url' => Context::getContext()->link->getPageLink(
                    'my-account',
                    true
                )
            );
        } else {
            return array();
        }
    }

    public static function cacheEnabled()
    {
        if (!isset(Roja45QuotationsPro::$cache_enabled)) {
            $product_ids = array();
            $sql = new DbQuery();
            $sql->select('qp.id_product');
            $sql->from('product_quotationspro', 'qp');
            $sql->where('qp.enabled=1');
            $sql->where('qp.id_shop=' . (int) Context::getContext()->shop->id);

            if ($results = Db::getInstance()->executeS($sql)) {
                foreach ($results as $row) {
                    array_push($product_ids, $row['id_product']);
                }
                Roja45QuotationsPro::$cache_enabled = $product_ids;
                return Roja45QuotationsPro::$cache_enabled;
            } else {
                return array();
            }
        }

        if (Roja45QuotationsPro::$cache_enabled === false || empty(Roja45QuotationsPro::$cache_enabled)) {
            return array();
        } else {
            return Roja45QuotationsPro::$cache_enabled;
        }
    }

    public function hookDisplayHeader($params)
    {
        $page = null;
        if (isset($this->context->controller->php_self) || isset($this->context->controller->page_name)) {
            $page = isset($this->context->controller->php_self) ?
            $this->context->controller->php_self : $this->context->controller->page_name;
        }
        if (!$page || $page == 'pagenotfound') {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')) {
                return;
            }
        }

        $this->context->controller->addJqueryPlugin('growl');
        $this->context->controller->addJqueryUI('ui.effect');
        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
            $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro17.js');
            $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro_cart17.js');
            $this->context->controller->addCSS($this->_path . 'views/css/roja45quotationspro17.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/validate.js');
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
                // $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro_cart17.js');
            }
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/roja45quotationspro.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro.js');
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addJS($this->_path . 'views/js/validate.js');
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
                $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro_cart.js');
            }
        }

        $this->smarty->assign(
            array(
                'roja45_use_quote_cart' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'),
            )
        );

        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION')) {
            $roja45_in_cart = (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_QUOTEINCART'
            );
            $roja45_quote_modified = (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_QUOTEMODIFIED'
            );

            if ($roja45_quote_modified) {
                $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_ID_QUOTATION'
                );
                $quotation = new RojaQuotation($id_roja45_quotation);
                $products = $quotation->getQuotationProductList();
                foreach ($products as $product) {
                    $this->context->cart->deleteProduct($product['id_product'], $product['id_product_attribute']);
                    $quotationProduct = new QuotationProduct($product['id_roja45_quotation_product']);
                    if ($quotationProduct->id_specific_price) {
                        $specific_price = new SpecificPrice($quotationProduct->id_specific_price);
                        $specific_price->delete();
                    }
                }
            }
            $allow_cart_mods = (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION'
            );
            if (!$allow_cart_mods) {
                if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                    $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro_preventcartmods17.js');
                } else {
                    $this->context->controller->addJS($this->_path . 'views/js/roja45quotationspro_preventcartmods.js');
                }
            }

            Media::addJsDef(array(
                'roja45quotationspro_allow_modifications' => $allow_cart_mods,
                'roja45quotationspro_cart_modified' => $roja45_quote_modified,
                'roja45quotationspro_in_cart' => $roja45_in_cart,
            ));
        }

        if ($page == 'order' || $page == 'order-opc') {
        } else {
            if (in_array($this->context->controller->php_self, array('index', 'category'))) {
                $cart = $this->context->cart;
                if ($id = RojaQuotation::getQuotationForCart($cart->id)) {
                    $quotation = new RojaQuotation($id);
                    if (Validate::isLoadedObject($quotation)) {
                        if ($quotation->modified) {
                            $products = $quotation->getQuotationProductList();
                            foreach ($products as $product) {
                                if ($product['id_specific_price']) {
                                    $specific_price = new SpecificPrice($product['id_specific_price']);
                                    $specific_price->delete();
                                    $quotation_product = new QuotationProduct($product['id_roja45_quotation_product']);
                                    $quotation_product->id_specific_price = 0;
                                    $quotation_product->save();
                                }
                            }
                            $discounts = $quotation->getQuotationChargeList(QuotationCharge::$DISCOUNT);
                            foreach ($discounts as $discount) {
                                if ($discount['id_cart_rule']) {
                                    $cart_rule = new CartRule($discount['id_cart_rule']);
                                    $cart_rule->delete();
                                    $quotation_discount = new QuotationCharge($discount['id_roja45_quotation_charge']);
                                    $quotation_discount->id_cart_rule = 0;
                                    $quotation_discount->save();
                                }
                            }
                        }
                    }
                }
            } else {
                if (in_array($this->context->controller->php_self, array('product'))) {
                    $id_product = Tools::getValue('id_product');
                    Media::addJsDef(array(
                        'roja45quotationspro_id_product' => $id_product,
                    ));
                }
            }
        }

        $controller_url = $this->context->link->getModuleLink(
            'roja45quotationspro',
            'QuotationsProFront',
            array(
                'token' => Tools::getToken(false),
            ),
            true
        );

        $usejs = Configuration::get('ROJA45_QUOTATIONSPRO_USEJS');
        if (!is_numeric(Configuration::get('ROJA45_QUOTATIONSPRO_USEJS'))) {
            $usejs = 1;
        }

        Media::addJsDef(array(
            'roja45quotationspro_controller' => $controller_url,
            //'roja45quotationspro_enabled' => Roja45QuotationsPro::cacheEnabled(),
            'roja45_quotation_useajax' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX'),
            'roja45_hide_add_to_cart' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_HIDEADDTOCART'),
            'roja45_hide_price' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_HIDEPRICE'),
            'roja45quotationspro_change_qty' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS'),
            'roja45quotationspro_delete_products' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS'
            ),
            'roja45quotationspro_enable_captcha' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'),
            'roja45quotationspro_enable_quote_dropdown' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY'
            ),
            'roja45quotationspro_enable_captchatype' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_CAPTCHATYPE'
            ),
            'roja45quotationspro_recaptcha_site_key' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE'
            ),
            'roja45quotationspro_quotation_address_invoice' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE'
            ),
            'roja45quotationspro_quotation_address_delivery' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY'
            ),
            'roja45quotationspro_usejs' => (int) $usejs,
            'roja45quotationspro_enable_inquotenotify' => 1,
            'roja45quotationspro_enablequotecart' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'
            ),
            'roja45quotationspro_enablequotecartpopup' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP'
            ),
            'roja45quotationspro_show_label' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL'),
            'roja45quotationspro_catalog_mode' => (int) Configuration::get('PS_CATALOG_MODE'),
            'roja45quotationspro_request_buttons' => Configuration::get('ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS'),
            'roja45quotationspro_label_position' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'
            ),
            'roja45quotationspro_productselector_addtocart' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR'
            ),
            'roja45quotationspro_productselector_price' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR'
            ),
            'roja45quotationspro_productselector_qty' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR'
            ),
            'roja45quotationspro_productlistitemselector' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR'
            ),
            'roja45quotationspro_productlistselector_addtocart' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR'
            ),
            'roja45quotationspro_productlistselector_buttons' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR'
            ),
            'roja45quotationspro_productlistselector_price' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR'
            ),
            'roja45quotationspro_productlistselector_flag' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR'
            ),
            'roja45quotationspro_responsivecartselector' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR'
            ),
            'roja45quotationspro_responsivecartnavselector' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR'
            ),
            //'roja45quotationspro_fontpack' => (int) $font_pack,
            'roja45quotationspro_instantresponse' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_INSTANTRESPONSE'
            ),
            'roja45quotationspro_touchspin' => (int) Configuration::get(
                'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
            ),
        ));

        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return $this->display(__FILE__, 'displayHeader.tpl');
        } else {
            Media::addJsDefL(
                'roja45quotationspro_quote_link_text',
                $this->l('Get A Quote')
            );
            Media::addJsDefL(
                'roja45quotationspro_button_addquote',
                $this->l('Add To Quote')
            );
            Media::addJsDefL(
                'roja45quotationspro_button_text',
                $this->l('Request Quote')
            );
            Media::addJsDefL(
                'roja45quotationspro_cartbutton_text',
                $this->l('Add To Quote')
            );
            Media::addJsDefL(
                'roja45quotationspro_success_title',
                $this->l('Success')
            );
            Media::addJsDefL(
                'roja45quotationspro_warning_title',
                $this->l('Warning')
            );
            Media::addJsDefL(
                'roja45quotationspro_error_title',
                $this->l('Error')
            );
            Media::addJsDefL(
                'roja45quotationspro_quote_modified',
                $this->l(
                    'Your cart has changed, you can request a new quote or reload an existing quote by clicking the link below.'
                )
            );
            Media::addJsDefL(
                'roja45quotationspro_new_quote_available',
                $this->l('A new quotation is available in your account.')
            );
            Media::addJsDefL(
                'roja45quotationspro_button_text_2',
                $this->l('Request New Quote')
            );
            Media::addJsDefL(
                'roja45quotationspro_sent_success',
                $this->l('Request received, we will be in touch shortly. Thank You.')
            );
            Media::addJsDefL(
                'roja45quotationspro_added_success',
                $this->l('Product added to your request successfully.')
            );
            Media::addJsDefL(
                'roja45quotationspro_added_failed',
                $this->l('Unable to add product to your request.')
            );
            Media::addJsDefL(
                'roja45quotationspro_deleted_success',
                $this->l('Product removed from your request successfully.')
            );
            Media::addJsDefL(
                'roja45quotationspro_deleted_failed',
                $this->l('Unable to remove product from your request.')
            );
            Media::addJsDefL(
                'roja45quotationspro_sent_failed',
                $this->l('Unable to send request. Please try again later.')
            );
            Media::addJsDefL(
                'roja45quotationspro_unknown_error',
                $this->l('An unexpected error has occurred, please raise this with your support provider.')
            );
        }
    }

    public function hookDisplayNav($params)
    {
        $page = $this->context->controller->php_self;
        if (!$page) {
            $page = $this->context->controller->page_name;
        }

        if (!$page) {
            return;
        } elseif ($page == 'pagenotfound') {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = null;
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
            return $this->renderWidget('displayNav', $params);
        }
    }

    public function hookDisplayNav1($params)
    {
        return $this->hookDisplayNav($params);
    }

    public function hookDisplayNav2($params)
    {
        return $this->hookDisplayNav($params);
    }

    public function hookDisplayTop($params)
    {
        $page = $this->context->controller->php_self;
        if (!$page) {
            $page = $this->context->controller->page_name;
        }

        if (!$page) {
            return;
        } elseif ($page == 'pagenotfound') {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = null;
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
            return $this->renderWidget('displayTop', $params);
        }
    }

    public function hookDisplayFooter($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        $params['cache_id'] = 'displayFooter';
        return $this->renderWidget('displayFooter', $params);
    }

    public function hookDisplayProductButtons($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')) {
                $params['cache_id'] = null;
                return $this->renderWidget('displayProductButtonsRegistration', $params);
            } else {
                return;
            }
        }
        if (!is_array($params['product'])) {
            $id_product = $params['product']->id;
        } else {
            $id_product = $params['product']['id_product'];
        }

        if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
            if (Configuration::get('ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS')) {
                $qty = StockAvailable::getQuantityAvailableByProduct(
                    $id_product,
                    0,
                    (int) $params['cart']->id_shop
                );
                if ($qty <= 0) {
                    return;
                }
            }

            $params['cache_id'] = null;
            return $this->renderWidget('displayProductButtons', $params);
        }
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->hookDisplayProductButtons($params);
    }

    public function hookDisplayLeftColumnProduct($params)
    {
        return $this->hookDisplayProductButtons($params);
    }

    public function hookDisplayRightColumnProduct($params)
    {
        return $this->hookDisplayProductButtons($params);
    }

    public function hookDisplayProductListReviews($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        $id_product = $params['product']['id_product'];
        $params['cache_id'] = null;
        if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
            return $this->renderWidget('displayEnabledIndicator', $params);
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        if (Module::isEnabled($this->name) && !Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = $this->getCacheId('displayCustomerAccount');
        return $this->renderWidget('displayCustomerAccount', $params);
    }

    public function hookDisplayShoppingCartFooter($params)
    {
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')) {
                $params['cache_id'] = null;
                return $this->renderWidget('displayShoppingCartFooter_register', $params);
            } else {
                return;
            }
        }
        $params['cache_id'] = null;
        return $this->renderWidget('displayShoppingCartFooter', $params);
    }

    public function hookDisplayShoppingCart($params)
    {
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')) {
                $params['cache_id'] = null;
                return $this->renderWidget('displayShoppingCart_register', $params);
            } else {
                return;
            }
        }
        $params['cache_id'] = null;
        return $this->renderWidget('displayShoppingCart', $params);
    }

    public function hookDisplayReassurance($params)
    {
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION')) {
            if (!isset($this->context->controller->php_self)) {
                return;
            }

            if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
                return;
            }

            if (RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            )) {
                return;
            }
            $params['cache_id'] = null;
            return $this->renderWidget('displayReassurance', $params);
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if (Module::isEnabled($this->name) &&
            $this->context->controller->controller_name == 'AdminModules' &&
            Tools::getValue('configure') == $this->name
        ) {
            $this->context->controller->addJqueryPlugin('validate');
            $this->context->controller->addJS($this->_path . 'views/js/roja45moduleadmin.js');
            $this->context->controller->addCSS($this->_path . 'views/css/roja45moduleadmin.css');
            $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/libraries/riotjs/riot+compiler.min.js');
            $this->context->controller->addJqueryPlugin('growl');
            $this->context->controller->addJS($this->_path . 'views/js/validate.js');
            $this->context->controller->addJqueryUi('ui.dialog');
            if (version_compare(_PS_VERSION_, '1.6.0.3', '>=') === true) {
                $this->context->controller->addjqueryPlugin('sortable');
            } elseif (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/jquery.sortable.js');
            }
        }
        $this->context->controller->addJS($this->_path . 'libraries/favico/favico.js');

        Media::addJsDef(array(
            'roja45quotationspro_controller' => $this->context->link->getAdminLink(
                'AdminQuotationsPro',
                true
            ),
        ));
        $this->context->controller->addJS($this->_path . 'views/js/roja45notifications.js');
        $this->context->controller->addCSS($this->_path . 'views/css/roja45quotationspro_notifications.css');
    }

    public function hookActionCustomerAccountAdd($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        $id_roja45_quotation_request = 0;
        if (isset($params['customer']->id)) {
            $id_roja45_quotation_request = Context::getContext()->cookie->__get(
                'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . $params['newCustomer']->id
            );
        }

        if (!$id_roja45_quotation_request) {
            if ($id_roja45_quotation_guest_request = Context::getContext()->cookie->__get(
                'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_guest
            )) {
                $_instance = new QuotationRequest($id_roja45_quotation_guest_request);
                $_instance->id_customer = $params['newCustomer']->id;
                $_instance->save();
                Context::getContext()->cookie->__set(
                    'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . $params['newCustomer']->id,
                    $id_roja45_quotation_guest_request
                );
            }
        }
    }

    public function hookActionAuthentication($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        $id_roja45_quotation_request = 0;
        if (isset($params['customer']->id)) {
            $id_roja45_quotation_request = Context::getContext()->cookie->__get(
                'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . $params['customer']->id
            );
        }

        if (!$id_roja45_quotation_request) {
            if ($id_roja45_quotation_guest_request = Context::getContext()->cookie->__get(
                'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_guest
            )) {
                $_instance = new QuotationRequest($id_roja45_quotation_guest_request);
                $_instance->id_customer = $params['customer']->id;
                $_instance->save();
                Context::getContext()->cookie->__set(
                    'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . $params['customer']->id,
                    $id_roja45_quotation_guest_request
                );
            }
        }

        $cart_products = Context::getContext()->cart->getProducts();
        if (Configuration::get('ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE') && count($cart_products)) {
            if (!isset($_instance) || !Validate::isLoadedObject($_instance)) {
                $_instance = QuotationRequest::getInstance(true);
                Context::getContext()->cookie->__set(
                    'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . $params['customer']->id,
                    $_instance->id
                );
            }

            foreach ($cart_products as $cart_product) {
                if ($_instance->updateQty(
                    $cart_product['cart_quantity'],
                    $cart_product['id_product'],
                    $cart_product['id_product_attribute'],
                    $cart_product['id_customization'],
                    'up'
                )) {
                    Context::getContext()->cart->deleteProduct(
                        $cart_product['id_product'],
                        $cart_product['id_product_attribute']
                    );
                } else {
                    throw new Exception($this->l('Unable to add requested product to quotation'));
                }
            }
            $request_link = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'quoteSummary',
                ),
                true
            );
            Tools::redirect($request_link);
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (Module::isEnabled($this->name)) {
            $params['cache_id'] = null;
            return $this->renderWidget('displayBackOfficeHeader', $params);
        }
    }

    public function hookDisplayBackOfficeTop($params)
    {
        if (Module::isEnabled($this->name)) {
            $params['cache_id'] = null;
            return $this->renderWidget('displayBackOfficeTop', $params);
        }
    }

    public function hookDisplayBackOfficeFooter($params)
    {
        if (Module::isEnabled($this->name)) {
            $params['cache_id'] = null;
            return $this->renderWidget('displayBackOfficeFooter', $params);
        }
    }

    public function hookDisplayAdminStatsModules($params)
    {
        return '';
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = null;
        return '';
    }

    public function hookDisplayRoja45EnabledIndicator($params)
    {
        return $this->hookDisplayProductListReviews($params);
    }

    public function hookDisplayRoja45Footer($params)
    {
        return $this->hookDisplayFooter($params);
    }

    public function hookDisplayRoja45ProductListFlag($params)
    {
        $html = '';
        if (!isset($this->context->controller->php_self)) {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        $id_product = $params['product']['id_product'];
        if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
            $params['cache_id'] = $this->getCacheId('displayRoja45ProductListFlag');
            return $this->renderWidget('displayRoja45ProductListFlag', $params);
        }
        return $html;
    }

    public function hookDisplayRoja45ProductPageIndicator($params)
    {
        $html = '';
        if (!isset($this->context->controller->php_self)) {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }

        if (in_array($params['product']->id, Roja45QuotationsPro::cacheEnabled())) {
            $params['cache_id'] = $this->getCacheId('displayRoja45ProductPageIndicator');
            return $this->renderWidget('displayRoja45ProductPageIndicator', $params);
        }
        return $html;
    }

    public function hookDisplayRoja45ProductListButton($params)
    {
        $params['cache_id'] = null;
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return $this->renderWidget('displayRoja45ProductListCartButton', $params);
        }

        if (in_array($params['product']['id_product'], Roja45QuotationsPro::cacheEnabled())) {
            return $this->renderWidget('displayRoja45ProductListQuoteButton', $params);
        } else {
            return $this->renderWidget('displayRoja45ProductListCartButton', $params);
        }
    }

    public function hookDisplayRoja45ProductPageButton($params)
    {
        $add_to_quote = false;
        if (!isset($this->context->controller->php_self)) {
            $add_to_quote = false;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            $add_to_quote = false;
        } elseif (in_array($params['product']->id, Roja45QuotationsPro::cacheEnabled())) {
            $add_to_quote = true;
        }

        $custom_params = array();
        $keys = RojaFortyFiveQuotationsProCore::pregGrepKey('/^custom_/', $params);
        foreach ($keys as $key) {
            $value = $params[$key];
            $key = Tools::substr($key, 7, Tools::strlen($key));
            $custom_params[$key] = $value;
        }

        if (isset($params['id_product'])) {
            $custom_params['id_product'] = $params['id_product'];
        }

        if (isset($params['id_product_attribute'])) {
            $custom_params['id_product_attribute'] = $params['id_product_attribute'];
        }

        $params['custom_params'] = $custom_params;
        if ($add_to_quote) {
            $params['cache_id'] = null;
            return $this->renderWidget('displayRoja45ProductPageQuoteButton', $params);
        } else {
            $params['cache_id'] = null;
            return $this->renderWidget('displayRoja45ProductPageCartButton', $params);
        }
    }

    public function hookDisplayRoja45QuoteCart($params)
    {
        $page = $this->context->controller->php_self;
        if (!$page) {
            $page = $this->context->controller->page_name;
        }

        if (!$page) {
            return;
        } elseif ($page == 'pagenotfound') {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = null;
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
            return $this->renderWidget('displayRoja45QuoteCart', $params);
        }
    }

    public function hookDisplayRoja45MobileQuoteCart($params)
    {
        $page = $this->context->controller->php_self;
        if (!$page) {
            $page = $this->context->controller->page_name;
        }

        if (!$page) {
            return;
        } elseif ($page == 'pagenotfound') {
            return;
        }
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $params['cache_id'] = null;
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
            return $this->renderWidget('displayRoja45MobileQuoteCart', $params);
        }
    }

    public function hookDisplayRoja45AddToQuoteButton($params)
    {
        if (!isset($this->context->controller->php_self)) {
            return;
        }

        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        if (!is_array($params['product'])) {
            $id_product = $params['product']->id;
        } else {
            $id_product = $params['product']['id_product'];
        }

        if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
            $params['cache_id'] = null;
            return $this->renderWidget('displayRoja45AddToQuoteButton', $params);
        }
    }

    public function hookDisplayRoja45ModuleManager($params)
    {
        $return = $this->name;
        return $return;
    }

    public function hookDisplayRoja45ProductList($params)
    {
        if (!Roja45QuotationsPro::groupEnabled($this->context->customer->id)) {
            return;
        }
        $id_product = $params['product']['id_product'];
        $params['cache_id'] = null;
        if (in_array($id_product, Roja45QuotationsPro::cacheEnabled())) {
            return $this->renderWidget('displayRoja45ProductList', $params);
        }
    }

    public function hookDisplayAdminListAfter($params)
    {
        if (isset($this->context->controller->controller_name) && ($this->context->controller->controller_name == 'AdminQuotationsPro')) {
            $options = array();
            foreach (QuotationStatus::getQuotationStates($this->context->language->id) as $status) {
                $options[] = array(
                    'id' => $status['code'],
                    'name' => $status['status'],
                );
            }
            $this->smarty->assign(
                array(
                    'options' => $options,
                )
            );
            return $this->display(__FILE__, 'views/templates/admin/_popup_quotation_status.tpl');
        }
    }

    public function hookActionCarrierProcess($params)
    {
        /*if ($params['cart'] != null) {
            if ($id = (int) RojaQuotation::getQuotationForCart($params['cart']->id)) {
                $quotation = new RojaQuotation($id);
                if (Validate::isLoadedObject($quotation) && $params['cookie']->isLogged()) {
                    // TODO - Comparison between cart and quote (method on quotation object)
                }
            }
        }*/
    }

    public function hookActionValidateOrder($params)
    {
        if ($id_roja45_quotation = RojaQuotation::getQuotationForCart($params['cart']->id)) {
            $quotation = new RojaQuotation(
                $id_roja45_quotation
            );
            if (Validate::isLoadedObject($quotation)) {
                $quotation->setStatus(QuotationStatus::$ORDR);
                $mysql_date_now = date('Y-m-d H:i:s');
                $quotation->purchase_date = $mysql_date_now;
                $quotation->id_cart = 0;
                //$quotation->resetCartPrices();
                $quotation->save();

                $quotation_order = new QuotationOrder();
                $quotation_order->id_roja45_quotation = $quotation->id;
                $quotation_order->id_order = $params['order']->id;
                $quotation_order->add();

                $params['order']->note = sprintf($this->l('Ordered from quote #[%s]'),  $quotation->reference);

                RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_ID_QUOTATION');
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART');
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEMODIFIED');
            }
        }
    }

    public function hookActionCartSave($params)
    {
        if (Module::isEnabled($this->name)) {
            $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            if ($id_roja45_quotation &&
                (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART') &&
                ((isset($this->context->controller->php_self) && ($this->context->controller->php_self != 'order'))
                    || ((isset($this->context->controller->controller_name)) && ($this->context->controller->controller_name != 'AdminQuotationsPro')))
            ) {
                /*if ($id_roja45_quotation &&
                (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART') &&
                ($this->context->controller->php_self != 'order')
                ) {*/
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (Validate::isLoadedObject($quotation) &&
                    isset($params['cart']) &&
                    $params['cart']->id_customer &&
                    !(int) Configuration::get('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION') &&
                    $quotation->isModified($params['cart']->id, $params['cart']->id_currency)
                ) {
                    $this->context->cart = $params['cart'];
                    $this->context->currency = new Currency($params['cart']->id_currency);
                    //$quotation->resetCartPrices();
                    $quotation->resetCart($params['cart'], false);
                    $quotation->modified = 1;
                    $quotation->save();
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEMODIFIED', 1);
                    RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_ID_QUOTATION');
                    RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART');
                }
            }
        }
    }

    public function hookActionAfterDeleteProductInCart($params)
    {
        if (!Module::isEnabled($this->name)) {
            return false;
        }

        $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
            'ROJA45QUOTATIONSPRO_ID_QUOTATION'
        );
        if ($id_roja45_quotation &&
            (int) RojaFortyFiveQuotationsProCore::getCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART')) {
            $quotation = new RojaQuotation($id_roja45_quotation);
            if (Validate::isLoadedObject($quotation) &&
                isset($params['cart']) &&
                $params['cart']->id_customer &&
                !(int) Configuration::get('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION') &&
                $quotation->isModified($params['cart']->id, $params['cart']->id_currency)
            ) {
                //$products = $this->context->cart->getProducts(true);
            } else {
                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_QUOTEINCART',
                    0
                );
            }
        }
        return true;
    }

    public function hookActionObjectProductInCartDeleteAfter($params)
    {
        $this->hookActionAfterDeleteProductInCart($params);
    }

    public function hookActionProductSave($params)
    {
        if (Module::isEnabled($this->name) && (int) Configuration::get('ROJA45_QUOTATIONSPRO_AUTOENABLENEW')) {
            $sql = new DbQuery();
            $sql->select('pq.id_roja45_product_quotation');
            $sql->from('product_quotationspro', 'pq');
            $sql->where('pq.id_product = ' . (int) $params['product']->id);
            $sql->where('pq.id_shop = ' . (int) Shop::getContext());
            $id_roja45_product_quotation = (int) Db::getInstance()->getValue($sql);
            if (!$id_roja45_product_quotation) {
                $product_quotation = new RojaProductQuotation();
                $product_quotation->id_product = $params['id_product'];
                $product_quotation->id_shop = Shop::getContext();
                $product_quotation->enabled = 1;
                $product_quotation->save();
            }
        }
    }

    public function hookActionProductDelete($params)
    {
        if (Module::isEnabled($this->name)) {
            $sql = new DbQuery();
            $sql->select('pq.id_roja45_product_quotation');
            $sql->from('product_quotationspro', 'pq');
            $sql->where('pq.id_product = ' . (int) $params['product']->id);
            $sql->where('pq.id_shop = ' . (int) Shop::getContext());

            $id_roja45_product_quotation = (int) Db::getInstance()->getValue($sql);
            if ($id_roja45_product_quotation) {
                $product_quotation = new RojaProductQuotation($id_roja45_product_quotation);
                $product_quotation->delete();
            }
        }
    }

    public function hookActionMailAlterMessageBeforeSend($params)
    {
        if (Module::isEnabled($this->name) && (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
        )) {
            $reference = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC'
            );
            $thread_id = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT'
            );
            if ($reference && $thread_id) {
                $unique_id = uniqid(rand());
                $params['message']->getHeaders()->addTextHeader(
                    'In-Reply-To',
                    $reference . ':' . $thread_id . ':' . $unique_id
                );
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC'
                );
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT'
                );
            }
        }
    }

    public function hookActionObjectCustomerMessageAddAfter($params)
    {
        $customer_thread = new CustomerThread($params['object']->id_customer_thread);
        if (Validate::isLoadedObject($customer_thread)) {
            $quotation_reference = $customer_thread->token;
            if ($id_roja45_quotation = RojaQuotation::getQuotationForReference($quotation_reference)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (Validate::isLoadedObject($quotation)) {
                    if (!$quotation->isStatus(QuotationStatus::$RCVD) &&
                        !$quotation->isStatus(QuotationStatus::$OPEN) &&
                        !$quotation->isStatus(QuotationStatus::$SENT)) {
                        if (isset($params['object']->id_employee)) {
                            $quotation->setStatus(
                                QuotationStatus::$CUSR,
                                $quotation->getSummaryDetails()
                            );
                        } else {
                            $quotation->setStatus(
                                QuotationStatus::$MESG,
                                $quotation->getSummaryDetails()
                            );
                        }
                    }
                }
            }
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if (RojaQuotation::deleteCustomerData($customer['email'])) {
                return json_encode(true);
            } else {
                return json_encode($this->l('Unable to delete customer data.'));
            }
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            if ($json = RojaQuotation::exportCustomerData($customer['email'])) {
                return $json;
            } else {
                return json_encode($this->l('Unable to export customer data.'));
            }
        }
    }

    public function hookActionGetAdditionalQuotationSummaryData($params)
    {
        if (Module::isEnabled($this->name)) {
            return [
                'test' => 'test',
                'test_two' => 1
            ];
        }
        return [];
    }

    public function hookActionDeliveryPriceByWeight($params)
    {
        if (Module::isEnabled($this->name)) {
            $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            $quotation = new RojaQuotation($id_roja45_quotation);
            if (Validate::isLoadedObject($quotation)) {
                if ($params['id_carrier'] == $quotation->id_carrier) {
                    //$shipping = $quotation->getTotalShippingCost(false);
                    $shipping = $quotation->getQuotationTotal(false, RojaQuotation::ONLY_SHIPPING);
                    if (is_numeric($shipping)) {
                        return $shipping;
                    }
                }
            }
        }
        return false;
    }

    public function hookActionDeliveryPriceByPrice($params)
    {
        if (Module::isEnabled($this->name)) {
            $id_roja45_quotation = RojaFortyFiveQuotationsProCore::getCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            $quotation = new RojaQuotation($id_roja45_quotation);
            if (Validate::isLoadedObject($quotation)) {
                if ($params['id_carrier'] == $quotation->id_carrier) {
                    //$shipping = $quotation->getTotalShippingCost(false);
                    $shipping = $quotation->getQuotationTotal(false, RojaQuotation::ONLY_SHIPPING);
                    if (is_numeric($shipping)) {
                        return $shipping;
                    }
                }
            }
        }
        return false;
    }

    public function hookActionGetProductPropertiesAfter($params)
    {
        if (Module::isEnabled($this->name)) {
            $page = $this->context->controller->php_self;
            if ($page == 'cart') {
                $usetax = !Tax::excludeTaxeOption();
                if (isset($params['product']['quantity_wanted'])) {
                    // 'quantity_wanted' may very well be zero even if set
                    $quantity = max((int) $params['product']['minimal_quantity'], (int) $params['product']['quantity_wanted']);
                } elseif (isset($params['product']['cart_quantity'])) {
                    $quantity = max((int) $params['product']['minimal_quantity'], (int) $params['product']['cart_quantity']);
                } else {
                    $quantity = (int) $params['product']['minimal_quantity'];
                }

                $specific_prices = null;
                $params['product']['reduction'] = Product::getPriceStatic(
                    (int) $params['product']['id_product'],
                    (bool) $usetax,
                    $params['product']['id_product_attribute'],
                    6,
                    null,
                    true,
                    true,
                    $quantity,
                    true,
                    null,
                    (isset($params['context']->cart) ? $params['context']->cart->id : null),
                    null,
                    $specific_prices
                );

                $params['product']['reduction_without_tax'] = Product::getPriceStatic(
                    (int) $params['product']['id_product'],
                    false,
                    $params['product']['id_product_attribute'],
                    6,
                    null,
                    true,
                    true,
                    $quantity,
                    true,
                    null,
                    (isset($params['context']->cart) ? $params['context']->cart->id : null),
                    null,
                    $specific_prices
                );
                $params['product']['specific_prices'] = $specific_prices;
            }

            return true;
        }
        return false;
    }

    public function hookAddWebserviceResources($extra_resources)
    {
        return array(
            'quotations' => array(
                'description' => 'Roja45: Quotations',
                'class' => 'RojaQuotation',
                'forbidden_method' => array('PUT', 'POST', 'DELETE')),
        );
    }

    public function getForm()
    {
        $form = array();
        $sql = '
            SELECT * FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_form`
            WHERE id_shop =' . (int) $this->context->shop->id;

        if ($row = Db::getInstance()->getRow($sql)) {
            $form['id'] = $row['id_quotation_form'];
            $form['cols'] = $row['form_columns'];
            // explode titles.
            $titles = array();
            parse_str($row['form_column_titles'], $titles);

            $form['titles'] = $titles;
            $sql = '
            	SELECT form_element_id as id,
            	form_element_name as name,
            	form_element_type as type,
            	form_element_column as col,
            	form_element_deletable as deletable,
            	form_element_config as configuration
                FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_form_element`
                WHERE id_quotation_form = ' . (int) $form['id'];
            // TODO add orderby
            if ($results = Db::getInstance()->executeS($sql)) {
                foreach ($results as $row) {
                    $form['fields'][$row['col']][] = $row;
                }
            }
        } else {
            $form['cols'] = 2;
            $form['id'] = '';
        }

        return $form;
    }

    protected function processSubmit()
    {
        if (Tools::isSubmit('sent_register')) {
            if (Tools::getIsset('email') && Tools::getIsset('number_order')) {
                $params = array();
                $params['email'] = Tools::getValue('email');
                $params['number_order'] = Tools::getValue('number_order');
                $params['is_domain_test'] = (
                    (Tools::getIsset('is_domain_test') && Tools::getValue('is_domain_test', 'off') == 'on') ? 1 : 0
                );

                if (!empty($params['email']) && !empty($params['number_order'])) {
                    $response = $this->jsonDecode($this->sendRequest($params));

                    if (is_object($response)) {
                        if ($response->code == self::CODE_ERROR) {
                            $this->errors[] = $response->message;
                        } elseif ($response->code == self::CODE_SUCCESS) {
                            $this->html .= $this->displayConfirmation($response->message);
                        }
                    }
                } else {
                    $this->errors[] = 'Please enter the information marked as mandatory for registration module.';
                }
            }
        } else if (Tools::isSubmit('submitConfiguration')) {
            $returned = $this->processSubmitConfiguration();
            if (is_array($returned)) {
                if (count($returned) > 0) {
                    foreach ($returned as $err) {
                        $this->html .= $this->displayError($err);
                    }
                } else {
                    $this->html .= $this->displayError($this->l('Unable to save configuration.'));
                }
            } elseif ($returned == true) {
                $this->html .= $this->displayConfirmation($this->l('Settings updated'));
                Tools::redirect($this->context->link->getAdminLink(
                        'AdminModules',
                        true
                    ) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            } else {
                $this->html .= $this->displayError($this->l('Unable to save configuration.'));
            }
            Roja45QuotationsPro::clearAllCached();
        } else if (Tools::isSubmit('submitRegistration')) {
            RojaFortyFiveQuotationsProLicense::registerModule(
                $this
            );
        }
    }

    private function registerHooks()
    {
        if (!$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('actionCarrierProcess')
            || !$this->registerHook('actionCartSave')
            || !$this->registerHook('actionProductSave')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('actionAuthentication')
            || !$this->registerHook('actionCustomerAccountAdd')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionDeleteGDPRCustomer')
            || !$this->registerHook('actionExportGDPRData')
            || !$this->registerHook('actionMailAlterMessageBeforeSend')
            || !$this->registerHook('actionObjectCustomerMessageAddAfter')
            || !$this->registerHook('actionDeliveryPriceByWeight')
            || !$this->registerHook('actionDeliveryPriceByPrice')
            || !$this->registerHook('actionGetProductPropertiesAfter')
            || !$this->registerHook('actionGetAdditionalQuotationSummaryData')
            // || !$this->registerHook('registerGDPRConsent')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('displayBackOfficeTop')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('displayNav')
            || !$this->registerHook('displayNav2')
            || !$this->registerHook('displayRoja45ModuleManager')
            || !$this->registerHook('displayCarrierExtraContent')
            || !$this->registerHook('displayProductListReviews')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('displayBackOfficeTop')
            || !$this->registerHook('displayAdminStatsModules')
            || !$this->registerHook('displayShoppingCart')
            || !$this->registerHook('displayShoppingCartFooter')
            //|| !$this->registerHook('displayOverrideTemplate')
            || !$this->registerHook('displayRoja45EnabledIndicator')
            || !$this->registerHook('displayRoja45QuoteCart')
            || !$this->registerHook('displayRoja45MobileQuoteCart')
            || !$this->registerHook('displayRoja45ProductList')
            || !$this->registerHook('displayRoja45AddToQuoteButton')
            || !$this->registerHook('displayRoja45ProductListFlag')
            || !$this->registerHook('displayRoja45ProductListButton')
            || !$this->registerHook('displayRoja45ProductPageButton')
            || !$this->registerHook('addWebserviceResources')
            || !$this->registerHook('displayAdminListAfter')
        ) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            if (!$this->registerHook('displayProductButtons')
                || !$this->registerHook('displayRoja45ProductPageIndicator')
                || !$this->registerHook('actionAfterDeleteProductInCart')
            ) {
                return false;
            }
        }

        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
            if (!$this->registerHook('displayProductAdditionalInfo')
                || !$this->registerHook('actionObjectProductInCartDeleteAfter')
            ) {
                return false;
            }
        }
        return true;
    }

    private function installDb()
    {
        $return = true;
        try {
            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_quotationspro` (
                `id_roja45_product_quotation` int(10) unsigned NOT NULL auto_increment,
                `id_product` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `enabled` tinyint(1) NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id_roja45_product_quotation`),
                INDEX id_product (id_product)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_form` (
                `id_quotation_form` int(10) unsigned NOT NULL auto_increment,
                `id_shop` int(10) unsigned NOT NULL,
                `form_columns` int(10) NOT NULL,
                `form_name` varchar(255) NOT NULL,
                `form_column_titles` varchar(255) NULL,
                `default_form` tinyint(1),
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_quotation_form`, `id_shop`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_form_element` (
                `id_quotation_form_element` int(10) unsigned NOT NULL auto_increment,
                `id_quotation_form` int(10) unsigned NOT NULL,
                `form_element_id` varchar(255) NOT NULL,
                `form_element_name` varchar(255) NOT NULL,
                `form_element_type` varchar(255),
                `form_element_column` int(1) NOT NULL,
                `form_element_position` int(10) NOT NULL,
                `form_element_deletable` tinyint(1) NOT NULL,
                `form_element_config` text NOT NULL,
                PRIMARY KEY (`id_quotation_form_element`, `id_quotation_form`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_formconditiongroup` (
                  `id_roja45_quotation_formconditiongroup` int(10) unsigned NOT NULL auto_increment,
                  `id_roja45_quotation_form` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_roja45_quotation_formconditiongroup`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_formcondition` (
                  `id_roja45_quotation_formcondition` int(10) unsigned NOT NULL auto_increment,
                  `id_roja45_quotation_formconditiongroup` int(10) unsigned NOT NULL,
                  `type` varchar(255) NOT NULL,
                  `value` varchar(255),
                  PRIMARY KEY (`id_roja45_quotation_formcondition`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_form_product` (
                  `id_roja45_quotation_form_product` int(10) unsigned NOT NULL auto_increment,
                  `id_product` int(10) unsigned NOT NULL,
                  `id_roja45_quotation_form` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id_roja45_quotation_form_product`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_status` (
                `id_roja45_quotation_status` int(10) unsigned NOT NULL auto_increment,
                `color`       varchar(255) NOT NULL,
                `code`        varchar(255) NOT NULL,
                `send_email`  tinyint(1) NOT NULL DEFAULT \'0\',
                `notify_admin`  tinyint(1) NOT NULL DEFAULT \'0\',
                `unremovable`     tinyint(1) NOT NULL DEFAULT \'0\',
                `answer_template`   varchar(255) NOT NULL,
                `customer_pdf_ids`   varchar(255) NULL,
                `admin_pdf_ids`   varchar(255) NULL,
                `id_roja45_quotation_answer`   int(10) unsigned NULL,
                `id_roja45_quotation_answer_admin`   int(10) unsigned NULL,
                PRIMARY KEY (`id_roja45_quotation_status`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_status_lang` (
                `id_roja45_quotation_status` int(10) unsigned NOT NULL auto_increment,
                `id_lang` varchar(255) NOT NULL,
                `status` varchar(255) NOT NULL,
                `display_code` varchar(255) NOT NULL,
                PRIMARY KEY (`id_roja45_quotation_status`,`id_lang`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_answer` (
                `id_roja45_quotation_answer` int(10) unsigned NOT NULL auto_increment,
                `type` int(10) NOT NULL,
                `custom_css` TEXT NOT NULL,
                `enabled` tinyint(1) NOT NULL DEFAULT \'1\',
                PRIMARY KEY (`id_roja45_quotation_answer`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang` (
                `id_roja45_quotation_answer` int(10) unsigned NOT NULL auto_increment,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                `subject` varchar(255) NULL,
                `template` varchar(255) NULL,
                PRIMARY KEY (`id_roja45_quotation_answer`, `id_lang`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
              CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_customization` (
              `id_roja45_quotation_customization` int(10) unsigned NOT NULL auto_increment,
              `id_product` int(10) unsigned NOT NULL,
              `id_product_attribute` int(10) unsigned NOT NULL,
              `id_customization` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id_roja45_quotation_customization`)
            ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
              CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_customizationdata` (
              `id_roja45_quotation_customizationdata` int(10) unsigned NOT NULL auto_increment,
              `id_roja45_quotation_customization` int(10) unsigned NOT NULL,
              `type` tinyint(1) NOT NULL,
              `index` int(10) NOT NULL,
              `value` varchar(255) NOT NULL,
              `price` decimal(20,6),
              `weight` decimal(20,6),
              PRIMARY KEY (`id_roja45_quotation_customizationdata`)
            ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_request` (
                `id_roja45_quotation_request` int(10) unsigned NOT NULL auto_increment,
                `id_shop` int(10),
                `id_currency` int(10),
                `id_customer` int(10),
                `id_guest` int(10),
                `id_lang` int(10),
                `secure_key` varchar(32),
                `form_data` text,
                `abandoned` tinyint(1),
                `requested` tinyint(1),
                `reference` varchar(255) NOT NULL,
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_roja45_quotation_request`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct` (
                `id_roja45_quotation_requestproduct` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation_request` int(10),
                `id_shop` int(10),
                `id_product` int(10),
                `id_product_attribute` int(10),
                `id_customization` INT(10) NOT NULL DEFAULT 0,
                `qty` int(10),
                `date_add` datetime,
                PRIMARY KEY (`id_roja45_quotation_requestproduct`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro` (
                `id_roja45_quotation` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation_status` int(10) unsigned,
                `id_lang` int(10),
                `id_shop` int(10),
                `id_currency` int(10),
                `id_country` int(10),
                `id_state` int(10),
                `id_address_invoice` int(10),
                `id_address_delivery` int(10),
                `id_address_tax` int(10) DEFAULT \'21\',
                `id_carrier` int(10),
                `id_request` int(10),
                `expiry_date` DATETIME NULL,
                `valid_days` int(10),
                `email` varchar(255) NOT NULL,
                `firstname` varchar(255) NOT NULL,
                `lastname` varchar(255) NOT NULL,
                `form_data` text,
                `reference` varchar(255) NOT NULL,
                `filename` varchar(255),
                `calculate_taxes` tinyint(1) NOT NULL DEFAULT \'0\',
                `modified` tinyint(1) NOT NULL DEFAULT \'0\',
                `quote_sent` tinyint(1) NOT NULL DEFAULT \'0\',
                `id_customer` int(10),
                `tmp_password` varchar(255),
                `id_cart` int(10),
                `id_order` int(10),
                `purchase_date` DATETIME NULL DEFAULT NULL,
                `id_employee` int(10) unsigned,
                `id_profile` int(10) unsigned,
                `total_to_pay` decimal(20,6),
                `total_to_pay_wt` decimal(20,6),
                `total_products` decimal(20,6),
                `total_products_wt` decimal(20,6),
                `total_discount` decimal(20,6),
                `total_discount_wt` decimal(20,6),
                `total_shipping_exc` decimal(20,6),
                `total_shipping_inc` decimal(20,6),
                `total_handling` decimal(20,6),
                `total_handling_wt` decimal(20,6),
                `total_wrapping` decimal(20,6),
                `total_charges` decimal(20,6),
                `total_charges_wt` decimal(20,6),
                `is_template` TINYINT(1) NOT NULL DEFAULT \'0\',
                `quote_name` varchar(255),
                `template_name` varchar(255),
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_roja45_quotation`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
              CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_product` (
              `id_roja45_quotation_product` int(10) unsigned NOT NULL auto_increment,
              `id_roja45_quotation` int(10) unsigned NOT NULL,
              `id_product` int(10),
              `id_product_attribute` int(10),
              `id_customization` INT(10) NOT NULL DEFAULT 0,
              `id_shop` int(10),
              `position` int(10),
              `product_title` varchar(255),
              `comment` varchar(1000),
              `qty` int(10),
              `unit_price_tax_excl` decimal(20,6),
              `unit_price_tax_incl` decimal(20,6),
              `deposit_amount` double(20,6) NOT NULL DEFAULT \'100.0\',
              `discount` double(20,6) NOT NULL DEFAULT \'0\',
              `discount_type` varchar(255) NOT NULL DEFAULT \'percentage\',
              `customization_cost_exc` double(20,6) NOT NULL DEFAULT \'0\',
              `customization_cost_inc` double(20,6) NOT NULL DEFAULT \'0\',
              `customization_cost_type` varchar(255) NOT NULL DEFAULT \'1\',
              `custom_price` tinyint(1) NOT NULL DEFAULT \'0\',
              `custom_image` varchar(255) NULL,
              `id_specific_price` int(10),
              `id_tax_rules_group` int(10),
              `tax_rate` double(20,6),
              `date_add` DATETIME NOT NULL,
              `date_upd` DATETIME NOT NULL,
              PRIMARY KEY (`id_roja45_quotation_product`)
            ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_charge` (
                `id_roja45_quotation_charge` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation` int(10),
                `charge_name` varchar(255),
                `charge_type` varchar(255),
                `charge_method` varchar(255),
                `charge_default` tinyint(1),
                `charge_value` decimal(20,6),
                `charge_amount` decimal(20,6),
                `charge_amount_wt` decimal(20,6),
                `charge_handling` decimal(20,6),
                `charge_handling_wt` decimal(20,6),
                `specific_product` tinyint(1) NOT NULL DEFAULT \'0\',
                `id_roja45_quotation_product` int(10) unsigned,
                `id_cart_rule` int(10) unsigned,
                `id_carrier` int(10) unsigned,
                PRIMARY KEY (`id_roja45_quotation_charge`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_message` (
                `id_roja45_quotation_message` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation` int(10),
                `id_customer_thread` int(10),
                PRIMARY KEY (`id_roja45_quotation_message`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_note` (
                `id_roja45_quotation_note` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation` int(10),
                `note` text,
                `added` DATETIME NOT NULL,
                PRIMARY KEY (`id_roja45_quotation_note`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_order` (
                `id_roja45_quotation_order` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation` int(10) unsigned NOT NULL,
                `id_order` int(10) unsigned NOT NULL,
                `date_add` datetime,
                `date_upd` datetime,
                PRIMARY KEY (`id_roja45_quotation_order`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_document` (
                `id_roja45_document` int(10) unsigned NOT NULL auto_increment,
                `id_shop` int(10) unsigned NOT NULL,
                `enabled` tinyint(1) NOT NULL,
                PRIMARY KEY (`id_roja45_document`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_document_lang` (
                `id_roja45_document` int(10) unsigned NOT NULL auto_increment,
                `id_lang` int(10) unsigned NOT NULL,
                `display_name` varchar(255),
                `file_name` varchar(255),
                `file_type` varchar(255),
                `internal_name` varchar(255),
                PRIMARY KEY (`id_roja45_document`,`id_lang`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document` (
                `id_roja45_quotation_document` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation` int(10) unsigned NOT NULL,
                `id_roja45_document` int(10) unsigned NULL,
                `display_name` varchar(255),
                `file_type` varchar(255),
                `file` varchar(255),
                `internal_name` varchar(255),
                PRIMARY KEY (`id_roja45_quotation_document`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template` (
                `id_roja45_quotation_template` int(10) unsigned NOT NULL auto_increment,
                `id_roja45_quotation_status` int(10),
                `id_lang` int(10),
                `id_shop` int(10),
                `id_currency` int(10),
                `id_carrier` int(10),
                `calculate_taxes` tinyint(1) NOT NULL DEFAULT \'0\',
                `template_name` varchar(255),
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_roja45_quotation_template`)
                ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = '
              CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template_product` (
              `id_roja45_quotation_template_product` int(10) unsigned NOT NULL auto_increment,
              `id_roja45_quotation_template` int(10) unsigned NOT NULL,
              `id_product` int(10),
              `id_product_attribute` int(10),
              `product_title` varchar(255),
              `comment` varchar(1000),
              `qty` int(10),
              `unit_price_tax_excl` decimal(20,6),
              `unit_price_tax_incl` decimal(20,6),
              `deposit_amount` double(20,6) NOT NULL DEFAULT \'100.0\',
              `custom_price` tinyint(1) NOT NULL DEFAULT \'0\',
              `date_add` DATETIME NOT NULL,
              `date_upd` DATETIME NOT NULL,
              PRIMARY KEY (`id_roja45_quotation_template_product`)
            ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'roja45_quotationspro_template_charge` (
            `id_roja45_quotation_template_charge` int(10) unsigned NOT NULL auto_increment,
            `id_roja45_quotation_template` int(10),
            `charge_name` varchar(255),
            `charge_type` varchar(255),
            `charge_method` varchar(255),
            `charge_value` decimal(20,6),
            `charge_amount` decimal(20,6),
            `charge_amount_wt` decimal(20,6),
            `specific_product` tinyint(1) NOT NULL DEFAULT \'0\',
            `id_roja45_quotation_product` int(10),
            `id_cart_rule` int(10),
            PRIMARY KEY (`id_roja45_quotation_template_charge`)
            ) ENGINE=`' . _MYSQL_ENGINE_ . '` DEFAULT CHARSET=UTF8;';
            $return &= (bool) Db::getInstance()->execute($sql);

            return $return;
        } catch (Exception $e) {
            return false;
        }
    }

    private function setGlobalVars()
    {
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLED', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES', 'module');
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAIL', '');
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_CAPTCHATYPE', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_COLUMNS', 2);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_CALCULATION_ORDER', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_REPLACE_CART', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_USE_CS', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_REQUEST_TYPE', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS', 30);
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION',
            'quote-box-bottom-left'
        );
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_USEAJAX', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_AUTOENABLENEW', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_HIDEADDTOCART', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_HIDEPRICE', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAILREQUEST', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS', 0);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_USEJS', 1);
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ENABLELOG', false);
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_INCLUDEHANDLING',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_INSTANTRESPONSE',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING',
            5
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT',
            2
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR',
            'DESC'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE',
            0
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER',
            0
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT',
            0
        );

        //$profiles = Profile::getProfiles($this->context->language->id);
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER',
            _PS_ADMIN_PROFILE_
        );

        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_USE_PS_PDF',
            1
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT',
            'QUOTE-[random 6]-[month][year]'
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE',
            0
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER',
            'sortAttributeById'
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES',
            0
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER',
            0
        );

        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
                '.product-add-to-cart'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
                'div.product-prices'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
                '.quote_quantity_wanted'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
                'article.product-miniature'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
                ''
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
                ''
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
                '.product-price-and-shipping'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR',
                '#header .header-nav div.hidden-md-up'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
                '._desktop_quotecart'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_ICON_PACK',
                '2'
            );
            Configuration::updateGlobalValue(
                'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT',
                1
            );
            Configuration::updateGlobalValue(
                'ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS',
                0
            );
            Configuration::updateGlobalValue(
                'ROJA45_QUOTATIONSPRO_DISABLECARTRULES',
                0
            );
            Configuration::updateGlobalValue(
                'ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION',
                0
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR',
                '.product-flags'
            );
        } else {
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
                '#add_to_cart'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
                '#our_price_display'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
                '#quantity_wanted'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
                'ul.product_list li.ajax_block_product'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
                '.button.ajax_add_to_cart_button'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
                '.button-container'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
                '.content_price'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY',
                0
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE',
                0
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR',
                '#header .header-nav .hidden-md-up #_mobile_cart'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
                '._desktop_quotecart'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_ICON_PACK',
                '1'
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION',
                0
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE',
                1
            );
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION',
                0
            );
        }

        return true;
    }

    private function installTabs()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
            return $this->installTabs17();
        } else {
            return $this->installTabs16();
        }
    }

    private function installTabs16()
    {
        $return = true;
        $id_tab = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminParent' . $this->tabClassName;
            $tab->id_parent = 0;
            $tab->module = $this->name;

            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();

            $tab->updatePosition(0, 3);

            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $this->tabClassName;
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'list';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationCatalog');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCatalog';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'store';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
            $tab->updatePosition(0, 1);
        }

        $id_tab = Tab::getIdFromClassName('QuotationForms');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationForms';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;

            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('AdminQuotationTemplates');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminQuotationTemplates';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationStatuses');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationStatuses';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'check_circle';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationAnswers');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationAnswers';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'question_answer';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationCarts');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCarts';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationDocuments');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationDocuments';
            $tab->id_parent = Tab::getIdFromClassName('AdminParent' . $this->tabClassName);
            $tab->module = $this->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }

        return $return;
    }

    private function installTabs17()
    {
        // Setup PS 1.7 tabs
        $return = true;

        if (!$id_tab = Tab::getIdFromClassName('AdminParentAdminQuotationsPro')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminParentAdminQuotationsPro';
            $tab->id_parent = 0;
            $tab->module = $this->name;

            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();

            $tab->updatePosition(0, 2);

            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminQuotations';
            $tab->id_parent = Tab::getIdFromClassName('AdminParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'list';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    'AdminQuotationsPro',
                    $lang
                );
            }
            $return &= $tab->add();
            $tab->updatePosition(0, 1);
        }

        if (!$id_tab = Tab::getIdFromClassName('QuotationCatalog')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCatalog';
            $tab->id_parent = Tab::getIdFromClassName('AdminParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'store';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
            $tab->updatePosition(0, 1);
        }

        if (!$id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'ParentAdminQuotationsPro';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $this->name;
            $tab->icon = 'list';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    'AdminQuotationsPro',
                    $lang
                );
            }
            $return &= $tab->add();
        }

        if (!$id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminQuotationsPro';
            $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'list';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationForms');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationForms';
            $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
            $tab->icon = 'file';
            $tab->module = $this->name;

            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        if (!$id_tab = Tab::getIdFromClassName('AdminQuotationTemplates')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminQuotationTemplates';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $this->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }

        if (!$id_tab = Tab::getIdFromClassName('QuotationAnswers')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationAnswers';
            $tab->id_parent = Tab::getIdFromClassName('AdminQuotations');
            $tab->module = $this->name;
            $tab->icon = 'question_answer';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationDocuments');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationDocuments';
            $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'tab';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }

            $return &= $tab->add();
        }

        if (!$id_tab = Tab::getIdFromClassName('QuotationStatuses')) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationStatuses';
            $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'check_circle';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        $id_tab = Tab::getIdFromClassName('QuotationCarts');
        if (!$id_tab) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'QuotationCarts';
            $tab->id_parent = Tab::getIdFromClassName('ParentAdminQuotationsPro');
            $tab->module = $this->name;
            $tab->icon = 'question_answer';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = RojaFortyFiveQuotationsProCore::getLocalTranslation(
                    $this,
                    $tab->class_name,
                    $lang
                );
            }
            $return &= $tab->add();
        }

        return $return;
    }

    private function populateDefaultData()
    {
        $return = true;
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'product_quotationspro (id_product,id_shop,enabled)
                SELECT id_product, ' . (int) $shop['id_shop'] . ', 0
                FROM ' . _DB_PREFIX_ . 'product';
            $return &= Db::getInstance()->execute($sql);
        }
        $this->l('Quotation');
        $this->l('Your quotation [%1$s] : [#ct%2$s] : [#tc%3$s]');
        $this->l('Your quotation [%1$s] : [#tc%2$s]');
        $this->l('inc.');
        $this->l('(inc.)');
        $this->l('exc.');
        $this->l('(exc.)');
        $this->l('Product unavailable for ordering: [%s].');
        $this->l('You are ordering less than the minimum quantity for item [%s].');
        $this->l('An item in your quotation is unavailable for order, please contact support.');
        $this->l('Unable to save quotation product, please try again.');
        $this->l('Unable to save cart rule. If the problem persists, please contact your system administrator.');
        $this->l('Unable to save quotation discount, please try again.');
        $this->l('Unable to create cart price for product %s : %s');

        $languages = Language::getLanguages(true);
        $contact_firstname_config = 'form_element_name=ROJA45QUOTATIONSPRO_FIRSTNAME&form_element_size=&' .
            'form_element_required=1&form_element_validation=isName&form_element_validation_custom=&';
        $contact_lastname_config = 'form_element_name=ROJA45QUOTATIONSPRO_LASTNAME&form_element_size=&' .
            'form_element_required=1&form_element_validation=isName&form_element_validation_custom=&';
        $contact_email_config = 'form_element_name=ROJA45QUOTATIONSPRO_EMAIL&form_element_size=&' .
            'form_element_required=1&form_element_validation=isEmail&form_element_validation_custom=&';
        foreach ($languages as $language) {
            $contact_firstname_config .= 'form_element_label_' .
            $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation(
                $this,
                'FormFieldFirstName',
                $language
            ) . '&form_element_description_' . $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation($this, 'FormFieldFirstNameDesc', $language) . '&';
            $contact_lastname_config .= 'form_element_label_' .
            $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation(
                $this,
                'FormFieldLastName',
                $language
            ) . '&form_element_description_' . $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation($this, 'FormFieldLastNameDesc', $language) . '&';
            $contact_email_config .= 'form_element_label_' . $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation(
                $this,
                'FormFieldEmail',
                $language
            ) . '&form_element_description_' . $language['id_lang'] . '=' .
            RojaFortyFiveQuotationsProCore::getLocalTranslation($this, 'FormFieldEmailDesc', $language) . '&';
        }
        $contact_firstname_config = Tools::substr(
            $contact_firstname_config,
            0,
            Tools::strlen($contact_firstname_config) - 1
        );
        $contact_lastname_config = Tools::substr(
            $contact_lastname_config,
            0,
            Tools::strlen($contact_lastname_config) - 1
        );
        $contact_email_config = Tools::substr(
            $contact_email_config,
            0,
            Tools::strlen($contact_email_config) - 1
        );

        foreach ($shops as $shop) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_form',
                array(
                    'id_shop' => (int) $shop['id_shop'],
                    'form_columns' => 1,
                    'form_name' => 'Default',
                    'default_form' => 1,
                    'date_add' => date("Y-m-d H:i:s"),
                    'date_upd' => date("Y-m-d H:i:s"),
                )
            );

            $id_form = Db::getInstance()->Insert_ID();

            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_form_element',
                array(
                    'id_quotation_form' => (int) $id_form,
                    'form_element_id' => 'ROJA45QUOTATIONSPRO_FIRSTNAME',
                    'form_element_name' => 'ROJA45QUOTATIONSPRO_FIRSTNAME',
                    'form_element_type' => 'TEXT',
                    'form_element_column' => 1,
                    'form_element_position' => 0,
                    'form_element_deletable' => 0,
                    'form_element_config' => pSQL($contact_firstname_config),
                )
            );

            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_form_element',
                array(
                    'id_quotation_form' => (int) $id_form,
                    'form_element_id' => 'ROJA45QUOTATIONSPRO_LASTNAME',
                    'form_element_name' => 'ROJA45QUOTATIONSPRO_LASTNAME',
                    'form_element_type' => 'TEXT',
                    'form_element_column' => 1,
                    'form_element_position' => 0,
                    'form_element_deletable' => 0,
                    'form_element_config' => pSQL($contact_lastname_config),
                )
            );

            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_form_element',
                array(
                    'id_quotation_form' => (int) $id_form,
                    'form_element_id' => 'ROJA45QUOTATIONSPRO_EMAIL',
                    'form_element_name' => 'ROJA45QUOTATIONSPRO_EMAIL',
                    'form_element_type' => 'TEXT',
                    'form_element_column' => 1,
                    'form_element_position' => 1,
                    'form_element_deletable' => 0,
                    'form_element_config' => pSQL($contact_email_config),
                )
            );
        }

        $custom_template_dir = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/custom/';
        $css = Tools::file_get_contents(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/views/css/pdf-styles.css');
        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$PDF,
                'custom_css' => $css,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Quotation Request PDF'),
                    'template' => pSQL('pdf_request_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'pdf_request_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'pdf_request_en.tpl',
                    $custom_template_dir . 'pdf_request_' . $language['iso_code'] . '.tpl'
                );
            }
        }
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF', $id_roja45_quotation_answer);

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$PDF,
                'custom_css' => $css,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Customer Quotation PDF'),
                    'template' => pSQL('pdf_quotation_' . $language['iso_code']),
                )
            );

            if (!file_exists($custom_template_dir . 'pdf_quotation_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'pdf_quotation_en.tpl',
                    $custom_template_dir . 'pdf_quotation_' . $language['iso_code'] . '.tpl'
                );
            }
        }
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_QUOTATION_PDF', $id_roja45_quotation_answer);

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Template Email'),
                    'template' => pSQL('mail_blank_template_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_blank_template_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_blank_template_en.tpl',
                    $custom_template_dir . 'mail_blank_template_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_blank_template_en-txt.tpl',
                    $custom_template_dir . 'mail_blank_template_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_customer_request_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_customer_request_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Customer Request Received Email'),
                    'subject' => pSQL('We have received your request.'),
                    'template' => pSQL('mail_customer_request_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_customer_request_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_customer_request_en.tpl',
                    $custom_template_dir . 'mail_customer_request_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_customer_request_en-txt.tpl',
                    $custom_template_dir . 'mail_customer_request_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_admin_request_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_admin_request_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Admin Request Received Email'),
                    'subject' => pSQL('Quotation Request Received'),
                    'template' => pSQL('mail_admin_request_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_admin_request_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_admin_request_en.tpl',
                    $custom_template_dir . 'mail_admin_request_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_admin_request_en-txt.tpl',
                    $custom_template_dir . 'mail_admin_request_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_quotation_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Send Customer Quote Email'),
                    'subject' => pSQL('Quotation [%1$s]'),
                    'template' => pSQL('mail_send_quote_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_send_quote_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_send_quote_en.tpl',
                    $custom_template_dir . 'mail_send_quote_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_send_quote_en-txt.tpl',
                    $custom_template_dir . 'mail_send_quote_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_notifyadmin = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer_notifyadmin,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Notify Admin Email'),
                    'subject' => pSQL('Quotation status has changed.'),
                    'template' => pSQL('mail_notify_admin_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_notify_admin_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_notify_admin_en.tpl',
                    $custom_template_dir . 'mail_notify_admin_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_notify_admin_en-txt.tpl',
                    $custom_template_dir . 'mail_notify_admin_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Thank You Email'),
                    'subject' => pSQL('Thank you'),
                    'template' => pSQL('mail_thank_you_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_thank_you_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_thank_you_en.tpl',
                    $custom_template_dir . 'mail_thank_you_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_thank_you_en-txt.tpl',
                    $custom_template_dir . 'mail_thank_you_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_messagereceived = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer_messagereceived,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Message Received Email'),
                    'subject' => pSQL('Thank you for your message'),
                    'template' => pSQL('mail_message_received_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_message_received_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_message_received_en.tpl',
                    $custom_template_dir . 'mail_message_received_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_message_received_en-txt.tpl',
                    $custom_template_dir . 'mail_message_received_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $return &= Db::getInstance()->insert(
            'roja45_quotationspro_answer',
            array(
                'type' => (int) QuotationAnswer::$MAIL,
                'enabled' => 1,
            )
        );
        $id_roja45_quotation_answer_orderrequest = Db::getInstance()->Insert_ID();
        foreach (Language::getLanguages(true) as $language) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_answer_lang',
                array(
                    'id_roja45_quotation_answer' => (int) $id_roja45_quotation_answer_orderrequest,
                    'id_lang' => (int) $language['id_lang'],
                    'name' => pSQL('Order Request Email'),
                    'subject' => pSQL('We have received your order request'),
                    'template' => pSQL('mail_customer_order_request_' . $language['iso_code']),
                )
            );
            if (!file_exists($custom_template_dir . 'mail_customer_order_request_' . $language['iso_code'] . '.tpl')) {
                copy(
                    $custom_template_dir . 'mail_customer_order_request_en.tpl',
                    $custom_template_dir . 'mail_customer_order_request_' . $language['iso_code'] . '.tpl'
                );
                copy(
                    $custom_template_dir . 'mail_customer_order_request_en-txt.tpl',
                    $custom_template_dir . 'mail_customer_order_request_' . $language['iso_code'] . '-txt.tpl'
                );
            }
        }

        $def_states = array(
            array(
                'code' => QuotationStatus::$RCVD,
                'display_code' => QuotationStatus::$RCVD,
                'color' => '#FF8C00',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Quotation Request Received',
                'answer_template' => 'quotation_request_received',
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => 0,
                'id_roja45_quotation_answer_admin' => 0,
            ),
            array(
                'code' => QuotationStatus::$OPEN,
                'display_code' => QuotationStatus::$OPEN,
                'color' => '#108510',
                'unremovable' => 1,
                'send_email' => 1,
                'notify_admin' => 1,
                'name' => 'Quotation Open',
                'answer_template' => null,
                'customer_pdf_ids' => '1',
                'admin_pdf_ids' => '1',
                'id_roja45_quotation_answer' => $id_roja45_quotation_customer_request_answer,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_admin_request_answer,
            ),
            array(
                'code' => QuotationStatus::$SENT,
                'display_code' => QuotationStatus::$SENT,
                'color' => '#32CD32',
                'unremovable' => 1,
                'send_email' => 1,
                'notify_admin' => 0,
                'name' => 'Customer Quotation Sent',
                'answer_template' => null,
                'customer_pdf_ids' => '2',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => $id_roja45_quotation_quotation_answer,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_answer_notifyadmin,
            ),
            array(
                'code' => QuotationStatus::$CART,
                'display_code' => QuotationStatus::$CART,
                'color' => '#4169E1',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'In Customer Cart',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
            array(
                'code' => QuotationStatus::$MESG,
                'display_code' => QuotationStatus::$MESG,
                'color' => '#FF8C00',
                'unremovable' => 1,
                'send_email' => 1,
                'notify_admin' => 1,
                'name' => 'Customer Message Received',
                'answer_template' => 'customer_message_received',
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => $id_roja45_quotation_answer_messagereceived,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_answer_notifyadmin,
            ),
            array(
                'code' => QuotationStatus::$CUSR,
                'display_code' => QuotationStatus::$CUSR,
                'color' => '#4169E1',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Customer Response Sent',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
            array(
                'code' => QuotationStatus::$ORDR,
                'display_code' => QuotationStatus::$ORDR,
                'color' => '#4169E1',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Customer Order Raised',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_answer_notifyadmin,
            ),
            array(
                'code' => QuotationStatus::$CLSD,
                'display_code' => QuotationStatus::$CLSD,
                'color' => '#b3b3b3',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Closed - Completed',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
            array(
                'code' => QuotationStatus::$INCP,
                'display_code' => QuotationStatus::$INCP,
                'color' => '#DC143C',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Closed - Incomplete',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
            array(
                'code' => QuotationStatus::$DLTD,
                'display_code' => QuotationStatus::$DLTD,
                'color' => '#FF0000',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'Quotation Deleted',
                'answer_template' => null,
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
            array(
                'code' => QuotationStatus::$CCLD,
                'display_code' => QuotationStatus::$CCLD,
                'color' => '#FF0000',
                'unremovable' => 1,
                'send_email' => 1,
                'notify_admin' => 0,
                'name' => 'Quotation Request Cancelled',
                'answer_template' => 'quotation_request_cancelled',
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_answer_notifyadmin,
            ),
            array(
                'code' => QuotationStatus::$CORD,
                'display_code' => QuotationStatus::$CORD,
                'color' => '#FF0000',
                'unremovable' => 1,
                'send_email' => 1,
                'notify_admin' => 1,
                'name' => 'Customer Order Request',
                'answer_template' => 'customer_order_request',
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => $id_roja45_quotation_answer_orderrequest,
                'id_roja45_quotation_answer_admin' => $id_roja45_quotation_answer_notifyadmin,
            ),
            array(
                'code' => QuotationStatus::$NWQT,
                'display_code' => QuotationStatus::$NWQT,
                'color' => '#FF8C00',
                'unremovable' => 1,
                'send_email' => 0,
                'notify_admin' => 0,
                'name' => 'New Quotation',
                'answer_template' => 'customer_order_request',
                'customer_pdf_ids' => '0',
                'admin_pdf_ids' => '0',
                'id_roja45_quotation_answer' => null,
                'id_roja45_quotation_answer_admin' => null,
            ),
        );

        foreach ($def_states as $state) {
            $return &= Db::getInstance()->insert(
                'roja45_quotationspro_status',
                array(
                    'color' => pSQL($state['color']),
                    'code' => pSQL($state['code']),
                    'unremovable' => (int) $state['unremovable'],
                    'send_email' => (int) $state['send_email'],
                    'notify_admin' => (int) $state['notify_admin'],
                    'answer_template' => pSQL($state['answer_template']),
                    'customer_pdf_ids' => pSQL($state['customer_pdf_ids']),
                    'admin_pdf_ids' => pSQL($state['admin_pdf_ids']),
                    'id_roja45_quotation_answer' => (int) $state['id_roja45_quotation_answer'],
                    'id_roja45_quotation_answer_admin' => (int) $state['id_roja45_quotation_answer_admin'],
                )
            );
            $id_status = Db::getInstance()->Insert_ID();
            Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_' . $state['code'], $id_status);
            foreach (Language::getLanguages(true) as $language) {
                $return &= Db::getInstance()->insert(
                    'roja45_quotationspro_status_lang',
                    array(
                        'id_roja45_quotation_status' => (int) $id_status,
                        'id_lang' => (int) $language['id_lang'],
                        'status' => pSQL(RojaFortyFiveQuotationsProCore::getLocalTranslation(
                            $this,
                            $state['code'],
                            $language
                        )),
                        'display_code' => $state['display_code'],
                    )
                );
            }
        }

        $groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        $group_ids = '';
        foreach ($groups as $group) {
            $group_ids .= $group['id_group'] . ',';
        }
        $group_ids = Tools::substr($group_ids, 0, Tools::strlen($group_ids) - 1);
        Configuration::updateValue('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS', $group_ids);

        $sql =
        'SELECT c.id_contact, c.email, cl.name
            FROM `' . _DB_PREFIX_ . 'contact` c
            LEFT JOIN `' . _DB_PREFIX_ . 'contact_lang` cl on (c.id_contact = cl.id_contact)
            WHERE cl.id_lang = ' . (int) $this->context->language->id . '
            ORDER BY c.id_contact';

        if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
            Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_EMAIL', $row['email']);
            Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_CONTACT_NAME', $row['name']);
        }
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_CS_ACCOUNT',
            $row['id_contact']
        );
        Configuration::updateGlobalValue(
            'ROJA45_QUOTATIONSPRO_USE_CS',
            0
        );

        return $return;
    }

    private function uninstallDb()
    {
        try {
            return Db::getInstance()->execute('DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'product_quotationspro`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_form`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_form_element`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_form_product`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_customization`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_customizationdata`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_formcondition`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_formconditiongroup`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_status`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_status_lang`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_answer`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_answer_lang`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_product`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_product_customization`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_charge`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_message`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_document`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_document_lang`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_request`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct_customization`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_order`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_conversation`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_conversationitem`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_template`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_template_charge`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_template_product`,
            `' . _DB_PREFIX_ . 'roja45_quotationspro_note`');
        } catch (Exception $e) {
            return false;
        }
    }

    private function uninstallTabs()
    {
        if ($id_tab = Tab::getIdFromClassName('QuotationDocuments')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('QuotationForms')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('QuotationCarts')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('QuotationStatuses')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('QuotationAnswers')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('QuotationCatalog')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('AdminQuotationTemplates')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('AdminQuotationsPro')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('AdminQuotations')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('ParentAdminQuotationsPro')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        if ($id_tab = Tab::getIdFromClassName('AdminParentAdminQuotationsPro')) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return true;
    }

    private function removeGlobalVars()
    {
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLED');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_EMAIL');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_COLUMNS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CAPTCHATYPE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CALCULATION_ORDER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_REPLACE_CART');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_REQUEST_TYPE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_USE_CS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_USEAJAX');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CONTACT_NAME');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CS_ACCOUNT');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ADD_CART_PRODUCTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CONTACT_BCC');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_AUTOENABLENEW');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_HIDEADDTOCART');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_HIDEPRICE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_EMAILREQUEST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTRSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCTLISTRADDTOCARTSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_INCLUDEHANDLING');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_INSTANTRESPONSE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ICON_PACK');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_INVISIBLECAPTCHA');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_USEJS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DISABLECARTRULES');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_USE_PS_PDF');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_PDF');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_BROWSERNOTIFICATION_TIMESTAMP');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES');
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER');

        $def_states = array(
            array(
                'code' => QuotationStatus::$RCVD,
            ),
            array(
                'code' => QuotationStatus::$SENT,
            ),
            array(
                'code' => QuotationStatus::$CART,
            ),
            array(
                'code' => QuotationStatus::$MESG,
            ),
            array(
                'code' => QuotationStatus::$CUSR,
            ),
            array(
                'code' => QuotationStatus::$ORDR,
            ),
            array(
                'code' => QuotationStatus::$CLSD,
            ),
            array(
                'code' => QuotationStatus::$INCP,
            ),
            array(
                'code' => QuotationStatus::$DLTD,
            ),
            array(
                'code' => QuotationStatus::$CCLD,
            ),
            array(
                'code' => QuotationStatus::$CORD,
            ),
            array(
                'code' => QuotationStatus::$NWQT,
            ),
            array(
                'code' => QuotationStatus::$OPEN,
            ),
        );

        foreach ($def_states as $state) {
            Configuration::deleteByName('ROJA45_QUOTATIONSPRO_STATUS_' . $state['code']);
        }
        return true;
    }

    protected function renderModuleForm()
    {
        $sql = '
            SELECT pl.`id_product`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'product_lang` pl
            WHERE pl.id_lang=' . $this->context->language->id;
        $options = Db::getInstance()->executeS($sql);
        $forms = QuotationForm::getForms();
        $params = array(
            'url' => $this->context->link->getAdminLink(
                'AdminModules',
                true
            ) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            'languages' => $this->context->controller->getLanguages(),
            'id_default_language' => Configuration::get('PS_LANG_DEFAULT'),
            'is_17' => (version_compare(_PS_VERSION_, '1.7', '>=') == true) ?
            true : false,
            'contacts' => Contact::getContacts($this->context->language->id),
            'carriers' => Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                Carrier::ALL_CARRIERS
            ),
            'col_width' => 12,
            'defaultFormLanguage' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'customer_groups' => Group::getGroups($this->context->language->id, $this->context->shop->id),
            'products' => array(
                'label' => $this->l('Enabled Product List'),
                'hint' => $this->l('Select the products for which you would like enable quotations.'),
                'name' => 'ROJA45_QUOTATIONSPRO_ENABLED_PRODUCTS',
                'required' => true,
                'size' => '20',
                'options' => array(
                    'query' => $options,
                    'id' => 'id_product',
                    'name' => 'name',
                ),
            ),
            'text_input' => array(
                'name' => 'TEXT_FIELD_LABEL',
                'id' => 'TEST_ID',
                'type' => 'text',
                'size' => '20',
                'maxlength' => '10',
                'readonly' => '0',
                'class' => 'test-class',
                'required' => '1',
            ),
            'fields_value' => $this->getModuleConfigFieldsValues(),
            'roja45quotationspro_addnew' => $this->context->link->getAdminLink(
                'QuotationForms',
                true
            ),
            'roja45quotationspro_enable' => $this->context->link->getAdminLink(
                'QuotationCatalog',
                true
            ),
            'quotation_request_url' => $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'quoteSummary',
                ),
                true
            ),
            'forms' => $forms,
            'pdf_templates' => QuotationAnswer::getPDFTemplates($this->context->language->id),
            'email_templates' => QuotationAnswer::getMailTemplates($this->context->language->id),
            'employees' => Employee::getEmployees(true),
            'profiles' => Profile::getProfiles($this->context->language->id),
            'shop_url' => $this->context->shop->getBaseURL(true),
        );
        $this->params = $params;
        RojaFortyFiveQuotationsProLicense::renderModuleForm($this);
    }

    private function postValidation()
    {
        if (Tools::isSubmit('submitConfiguration')) {
            if (!Tools::getValue('ROJA45_QUOTATION_ENABLED')) {
                $this->postErrors[] = $this->l('Account Name is required.');
            }
        }
    }

    public function getModuleConfigFieldsValues()
    {
        return array(
            'ROJA45_QUOTATIONSPRO_USE_CS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USE_CS',
                Configuration::get('ROJA45_QUOTATIONSPRO_USE_CS')
            ),
            'ROJA45_QUOTATIONSPRO_CS_ACCOUNT' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CS_ACCOUNT',
                Configuration::get('ROJA45_QUOTATIONSPRO_CS_ACCOUNT')
            ),
            'ROJA45_QUOTATIONSPRO_EMAIL' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_EMAIL',
                Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL')
            ),
            'ROJA45_QUOTATIONSPRO_CONTACT_NAME' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CONTACT_NAME',
                Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_NAME')
            ),
            'ROJA45_QUOTATIONSPRO_CONTACT_BCC' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CONTACT_BCC',
                Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_BCC')
            ),
            'ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES',
                Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA')
            ),
            'ROJA45_QUOTATIONSPRO_CAPTCHATYPE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CAPTCHATYPE',
                Configuration::get('ROJA45_QUOTATIONSPRO_CAPTCHATYPE')
            ),
            'ROJA45_QUOTATIONSPRO_CALCULATION_ORDER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CALCULATION_ORDER',
                Configuration::get('ROJA45_QUOTATIONSPRO_CALCULATION_ORDER')
            ),
            'ROJA45_QUOTATIONSPRO_REPLACE_CART' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_REPLACE_CART',
                Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_CART')
            ),
            'ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS',
                Configuration::get('ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS')
            ),
            'ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS',
                Configuration::get('ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER')
            ),
            'ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET',
                Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET')
            ),
            'ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE',
                Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE')
            ),
            'ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS',
                Configuration::get('ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS')
            ),
            'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL',
                Configuration::get('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL')
            ),
            'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION',
                Configuration::get('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS')
            ),
            'ROJA45_QUOTATIONSPRO_USEAJAX' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USEAJAX',
                Configuration::get('ROJA45_QUOTATIONSPRO_USEAJAX')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')
            ),
            'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION',
                Configuration::get('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD')
            ),
            'ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS',
                Configuration::get('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS')
            ),
            'ROJA45_QUOTATIONSPRO_HIDEADDTOCART' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIDEADDTOCART',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIDEADDTOCART')
            ),
            'ROJA45_QUOTATIONSPRO_HIDEPRICE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIDEPRICE',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIDEPRICE')
            ),
            'ROJA45_QUOTATIONSPRO_EMAILREQUEST' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_EMAILREQUEST',
                Configuration::get('ROJA45_QUOTATIONSPRO_EMAILREQUEST')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS')
            ),
            'ROJA45_QUOTATIONSPRO_AUTOENABLENEW' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_AUTOENABLENEW',
                Configuration::get('ROJA45_QUOTATIONSPRO_AUTOENABLENEW')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP')
            ),
            'ROJA45_QUOTATIONSPRO_INCLUDEHANDLING' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_INCLUDEHANDLING',
                Configuration::get('ROJA45_QUOTATIONSPRO_INCLUDEHANDLING')
            ),
            'ROJA45_QUOTATIONSPRO_INSTANTRESPONSE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_INSTANTRESPONSE',
                Configuration::get('ROJA45_QUOTATIONSPRO_INSTANTRESPONSE')
            ),
            'ROJA45_QUOTATIONSPRO_ICON_PACK' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ICON_PACK',
                Configuration::get('ROJA45_QUOTATIONSPRO_ICON_PACK')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE')
            ),
            'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
                Configuration::get('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR')
            ),
            'ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION',
                Configuration::get('ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION')
            ),
            'ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE',
                Configuration::get('ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE')
            ),
            'ROJA45_QUOTATIONSPRO_USEJS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USEJS',
                Configuration::get('ROJA45_QUOTATIONSPRO_USEJS')
            ),
            'ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER',
                Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER')
            ),
            'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY',
                Configuration::get('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY')
            ),
            'ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS',
                Configuration::get('ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS')
            ),
            'ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM',
                Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM')
            ),
            'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT',
                Configuration::get('ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT')
            ),
            'ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS',
                Configuration::get('ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS')
            ),
            'ROJA45_QUOTATIONSPRO_DISABLECARTRULES' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DISABLECARTRULES',
                Configuration::get('ROJA45_QUOTATIONSPRO_DISABLECARTRULES')
            ),
            'ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION',
                Configuration::get('ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE')
            ),
            'ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT',
                Configuration::get('ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT')
            ),
            'ROJA45_QUOTATIONSPRO_USE_PS_PDF' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USE_PS_PDF',
                Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_PDF' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_PDF',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_PDF')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL')
            ),
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES')
            ),
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING')
            ),
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR')
            ),
            'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER',
                Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER')
            ),
            'ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE',
                Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE')
            ),
            'ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS',
                Configuration::get('ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS')
            ),
            'ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER',
                Configuration::get('ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN')
            ),
            'ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS',
                Configuration::get('ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD')
            ),
            'ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION',
                Configuration::get('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')
            ),
            'ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE',
                Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE')
            ),
            'ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT',
                Configuration::get('ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT')
            ),
            'ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR',
                Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR')
            ),
            'ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS',
                Configuration::get('ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS')
            ),
            'ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING',
                Configuration::get('ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE')
            ),
            'ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER',
                Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER')
            ),
            'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER',
                Configuration::get('ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER')
            ),
            'ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES',
                Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE')
            ),
            'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY',
                Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY')
            ),
            'ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP',
                Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')
            ),
            'ROJA45_QUOTATIONSPRO_ENABLELOG' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_ENABLELOG',
                Configuration::get('ROJA45_QUOTATIONSPRO_ENABLELOG', null, null, null, false)
            ),
            'enabled_groups' => explode(',', Configuration::get('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS')),
        );
    }

    public function buildFormComponents($form_config)
    {
        $id_language = $this->context->language->id;
        $id_language_default = Configuration::get('PS_LANG_DEFAULT');

        $form = array();
        // for columns
        foreach ($form_config['fields'] as $col => $column) {
            $pos = 1;
            $form[$col] = array();
            $form[$col]['settings'] = array();
            $form[$col]['fields'] = array();

            if (isset($form_config['titles']['form_element_column_title_' . $col])) {
                $form[$col]['settings']['column_heading'] = $form_config['titles']['form_element_column_title_' . $col];
            }

            foreach ($column as $field) {
                // TODO - loop through the fields and extract each component
                //parse_str($field['configuration'], $config);
                $form_components = [];
                $components = explode('&', $field['configuration']);
                $current_key = '';
                foreach ($components as $component) {
                    $component = explode('=', $component);
                    if ($component[0] == 'form_element_name') {
                        $form_components[$component[1]] = [];
                        $current_key = $component[1];
                    }
                    $form_components[$current_key][$component[0]] = $component[1];
                }

                foreach ($form_components as $config) {
                    $form_field = array();
                    $form_field['id'] = $config['form_element_name'];
                    $form_field['name'] = $config['form_element_name'];
                    $form_field['enabled'] = isset($config['form_element_enabled']) ? $config['form_element_enabled'] : 1;
                    $form_field['type'] = isset($config['form_element_type']) ? $config['form_element_type'] : $field['type'];
                    $form_field['validation'] = '';
                    $form_field['custom_regex'] = '';
                    $form_field['disabled'] = false;
                    $form_field['readonly'] = false;

                    $form_field['collapse'] = isset($config['form_element_collapse']) ? $config['form_element_collapse'] : 0;

                    if (isset($field['pos'])) {
                        $form_field['position'] = $field['pos'];
                    }

                    if (Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES') &&
                        Context::getContext()->customer->isLogged()
                    ) {
                        if (in_array($field['id'], [
                            'ROJA45QUOTATIONSPRO_FIRSTNAME',
                            'ROJA45QUOTATIONSPRO_LASTNAME',
                            'ROJA45QUOTATIONSPRO_EMAIL'
                        ])) {
                            $form_field['readonly'] = true;
                        }
                    }

                    $form_field['required'] = isset($config['form_element_required']) ? $config['form_element_required'] : 0;

                    if (isset($config['form_element_validation'])) {
                        $form_field['validation'] = $config['form_element_validation'];

                        // TODO - need to convert back to string here?
                        if ($form_field['validation'] == 'isCustom') {
                            $form_field['custom_regex'] = $config['form_element_validation_custom'];
                        }
                    }
                    $form_field['field_type'] = $form_field['type'];
                    if ($form_field['type'] == 'SELECT') {
                        $form_field['display_as'] = (int)(isset($config['form_element_displayas'])) ? $config['form_element_displayas'] : 0;
                        if ($config['form_element_contents'] == 1) {
                            if (!isset($config['form_element_select_options_' . $id_language])) {
                                $exploded = explode(
                                    "\n",
                                    urldecode($config['form_element_select_options_' . $id_language_default])
                                );
                            } else {
                                $exploded = explode(
                                    "\n",
                                    urldecode($config['form_element_select_options_' . $id_language])
                                );
                            }
                            $options = array();
                            foreach ($exploded as $key => $option) {
                                $options[$key]['id'] = Tools::strtoupper(trim($option));
                                $options[$key]['name'] = $option;
                            }
                            $form_field['options'] = $options;
                            $form_field['key_options'] = 'id';
                            $form_field['value_options'] = 'name';
                            $form_field['field_type'] = 'CUSTOM_SELECT';
                        } elseif ($config['form_element_contents'] == 2) {
                            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                                $countries = Carrier::getDeliveredCountries(
                                    $this->context->language->id,
                                    true,
                                    true
                                );
                            } else {
                                $countries = Country::getCountries($this->context->language->id, true);
                            }
                            $form_field['options'] = $countries;
                            $form_field['key_options'] = 'id_country';
                            $form_field['value_options'] = 'name';
                            $form_field['field_type'] = 'COUNTRY';
                        } elseif ($config['form_element_contents'] == 3) {
                            $shipping_methods = Carrier::getCarriers(
                                $this->context->language->id,
                                true,
                                false,
                                false,
                                null,
                                Carrier::ALL_CARRIERS
                            );
                            $form_field['options'] = $shipping_methods;
                            $form_field['key_options'] = 'id_carrier';
                            $form_field['value_options'] = 'name';
                            $form_field['field_type'] = 'SHIPPING_METHOD';
                        } elseif ($config['form_element_contents'] == 4) {
                            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                                $countries = Carrier::getDeliveredCountries(
                                    $this->context->language->id,
                                    true,
                                    true
                                );
                            } else {
                                $countries = Country::getCountries($this->context->language->id, true);
                            }
                            $country = reset($countries);
                            $states = State::getStatesByIdCountry($country['id_country'], true);
                            $form_field['options'] = $states;
                            $form_field['key_options'] = 'id_state';
                            $form_field['value_options'] = 'name';
                            $form_field['field_type'] = 'STATE';
                        } elseif ($config['form_element_contents'] == 5) {
                            $is_logged = false;
                            if (Context::getContext()->customer->isLogged()) {
                                $is_logged = true;
                            }

                            if ($is_logged) {
                                $addresses =  Context::getContext()->customer->getAddresses($this->context->language->id);
                            }

                            $options = [
                                0 => [
                                    'id_address' => 0,
                                    'name' => $this->l('Select')
                                ]
                            ];
                            
                            if (!empty($addresses) && $is_logged) {
                                foreach ($addresses as $address) {
                                    $options[] = [
                                        'id_address' => $address['id_address'],
                                        'name' => $address['alias'],
                                    ];
                                }
                            } else if (empty($addresses)) {
                                $config['form_element_class'] = "invisible";
                            }
                            
                            $form_field['options'] = $options;
                            $form_field['key_options'] = 'id_address';
                            $form_field['value_options'] = 'name';
                            $form_field['field_type'] = 'ADDRESS_SELECTOR';
                        }
                    }
                    if ($form_field['type'] == 'TEXTAREA') {
                        if (!isset($config['form_element_rows_' . $id_language])) {
                            $form_field['rows'] = $config['form_element_rows_' . $id_language_default];
                        } else {
                            $form_field['rows'] = $config['form_element_rows_' . $id_language];
                        }
                        $form_field['rows'] = $config['form_element_rows_' . $id_language];
                    }
                    if ($form_field['type'] == 'DATE') {
                        $form_field['format'] = $config['form_element_date_format'];
                    }
                    if ($form_field['type'] == 'DATEPERIOD') {
                        $form_field['format'] = $config['form_element_date_format'];
                        $form_field['start_label'] = $config['form_element_start_label_' . $id_language_default];
                        $form_field['end_label'] = $config['form_element_end_label_' . $id_language_default];
                    }

                    if (!isset($config['form_element_label_' . $id_language])) {
                        if (isset($config['form_element_label_' . $id_language_default])) {
                            $form_field['label'] = urldecode($config['form_element_label_' . $id_language_default]);
                        } else {
                            $form_field['label'] = '';
                        }
                    } else {
                        $form_field['label'] = urldecode($config['form_element_label_' . $id_language]);
                    }

                    if (!isset($config['form_element_description_' . $id_language])) {
                        if (isset($config['form_element_label_' . $id_language_default])) {
                            $form_field['description'] = urldecode($config['form_element_label_' . $id_language_default]);
                        } else {
                            $form_field['description'] = '';
                        }
                    } else {
                        $form_field['description'] = urldecode($config['form_element_description_' . $id_language_default]);
                    }

                    if (isset($config['form_element_size'])) {
                        $form_field['size'] = $config['form_element_size'];
                    } else {
                        $form_field['size'] = '';
                    }

                    if (isset($config['form_element_class'])) {
                        $form_field['class'] = $config['form_element_class'];
                    } else {
                        $form_field['class'] = '';
                    }
                    if (isset($config['form_element_length'])) {
                        $form_field['maxlength'] = $config['form_element_length'];
                    } else {
                        $form_field['maxlength'] = 255;
                    }

                    if (!isset($config['form_element_prefix_' . $id_language])) {
                        if (isset($config['form_element_prefix_' . $id_language_default]) &&
                            Tools::strlen($config['form_element_prefix_' . $id_language_default]) > 0
                        ) {
                            $form_field['prefix'] = $config['form_element_prefix_' . $id_language_default];
                        } else {
                            $form_field['prefix'] = null;
                        }
                    } else {
                        if (isset($config['form_element_prefix_' . $id_language]) &&
                            Tools::strlen($config['form_element_prefix_' . $id_language]) > 0
                        ) {
                            $form_field['prefix'] = $config['form_element_prefix_' . $id_language];
                        } else {
                            $form_field['prefix'] = null;
                        }
                    }

                    if (!isset($config['form_element_suffix_' . $id_language])) {
                        if (isset($config['form_element_suffix_' . $id_language_default]) &&
                            Tools::strlen($config['form_element_suffix_' . $id_language_default]) > 0
                        ) {
                            $form_field['suffix'] = $config['form_element_suffix_' . $id_language_default];
                        } else {
                            $form_field['suffix'] = null;
                        }
                    } else {
                        if (isset($config['form_element_suffix_' . $id_language]) &&
                            Tools::strlen($config['form_element_suffix_' . $id_language]) > 0
                        ) {
                            $form_field['suffix'] = $config['form_element_suffix_' . $id_language];
                        } else {
                            $form_field['suffix'] = null;
                        }
                    }

                    $form[$col]['fields'][$field['name']][$pos] = $form_field;
                    $pos++;
                }
            }
        }
        return $form;
    }

    private static function getCountry($address = null)
    {
        if ($id_country = Tools::getValue('id_country')) {
            return (int) $id_country;
        } elseif (isset($address) && isset($address->id_country) && $address->id_country) {
            $id_country = $address->id_country;
        } elseif (Configuration::get('PS_DETECT_COUNTRY') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match('#(?<=-)\w\w|\w\w(?!-)#', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $array);
            if (is_array($array) && isset($array[0]) && Validate::isLanguageIsoCode($array[0])) {
                $id_country = (int) Country::getByIso($array[0], true);
            }
        }
        if (!isset($id_country) || !$id_country) {
            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        return (int) $id_country;
    }

    public function processSubmitConfiguration()
    {
        $validationErrors = array();
        $html = '';
        try {
            // FORM VALIDATION
            Configuration::updateValue(
                'ROJA45_QUOTATIONSPRO_USE_CS',
                Tools::getValue('ROJA45_QUOTATIONSPRO_USE_CS')
            );

            if (Tools::getValue('ROJA45_QUOTATIONSPRO_USE_CS') == 1) {
                if (!Tools::strlen(trim(Tools::getValue('ROJA45_QUOTATIONSPRO_EMAIL'))) > 0) {
                    $validationErrors[] = $this->l('Email Address Required');
                }
                if (!Tools::strlen(trim(Tools::getValue('ROJA45_QUOTATIONSPRO_CONTACT_NAME'))) > 0) {
                    $validationErrors[] = $this->l('Contact Name Required');
                }
            } else {
                if (!Tools::strlen(trim(Tools::getValue('ROJA45_QUOTATIONSPRO_CS_ACCOUNT'))) > 0) {
                    $validationErrors[] = $this->l('Customer Service Account Required');
                }
            }
            if (!RojaFortyFiveQuotationsProLicense::validateUpdate($this)) {
                return;
            }
            if (!count($validationErrors)) {
                if (Tools::getValue('ROJA45_QUOTATIONSPRO_USE_CS') == 0) {
                    $id_account = Tools::getValue('ROJA45_QUOTATIONSPRO_CS_ACCOUNT');
                    $sql = 'SELECT c.id_contact, c.email, cl.name
                    FROM `' . _DB_PREFIX_ . 'contact` c
                    LEFT JOIN `' . _DB_PREFIX_ . 'contact_lang` cl on (c.id_contact = cl.id_contact)
                    WHERE c.id_contact = ' . (int) $id_account . '
                    AND cl.id_lang = ' . (int) $this->context->language->id;

                    if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)) {
                        Configuration::updateValue('ROJA45_QUOTATIONSPRO_EMAIL', $row['email']);
                        Configuration::updateValue('ROJA45_QUOTATIONSPRO_CONTACT_NAME', $row['name']);
                    }
                    Configuration::updateValue(
                        'ROJA45_QUOTATIONSPRO_CS_ACCOUNT',
                        Tools::getValue('ROJA45_QUOTATIONSPRO_CS_ACCOUNT')
                    );
                } else {
                    Configuration::updateValue(
                        'ROJA45_QUOTATIONSPRO_EMAIL',
                        Tools::getValue('ROJA45_QUOTATIONSPRO_EMAIL')
                    );
                    Configuration::updateValue(
                        'ROJA45_QUOTATIONSPRO_CONTACT_NAME',
                        Tools::getValue('ROJA45_QUOTATIONSPRO_CONTACT_NAME')
                    );
                }
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CONTACT_BCC',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CONTACT_BCC')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_EMAIL_TEMPLATES')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA')
                );

                if ((int) Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA') &&
                    (empty(Tools::getValue('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE')) || empty(Tools::getValue('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET')))
                ) {
                    $validationErrors[] = $this->l('You need to provide your Google reCaptcha site and secret keys');
                }

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CAPTCHATYPE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CAPTCHATYPE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET')
                );

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE')
                );

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE')
                );

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY')
                );

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CALCULATION_ORDER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CALCULATION_ORDER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_REPLACE_CART',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_REPLACE_CART')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_REQUEST_TYPE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_REQUEST_TYPE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DELETE_CART_PRODUCTS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QTY_CART_PRODUCTS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_REQUEST_BUTTONS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION')
                );
                if (!is_int((int) Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS'))) {
                    $validationErrors[] = $this->l('Your valid days value should be an number.');
                }
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_USEAJAX',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_USEAJAX')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECART',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_AUTOENABLENEW',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_AUTOENABLENEW')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIDEADDTOCART',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIDEADDTOCART')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLELOG',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLELOG')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIDEPRICE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIDEPRICE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_EMAILREQUEST',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_EMAILREQUEST')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_INCLUDEHANDLING',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_INCLUDEHANDLING')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_USEJS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_USEJS')
                );

                if (Tools::getValue('ROJA45_QUOTATIONSPRO_INSTANTRESPONSE') == 1 &&
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA') == 1 &&
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CAPTCHATYPE') != 2
                ) {
                    $validationErrors[] = $this->l(
                        'The fast response option can only be used with Google reCAPTCHA v3.'
                    );
                }
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_INSTANTRESPONSE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_INSTANTRESPONSE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ICON_PACK')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DISABLECARTRULES',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DISABLECARTRULES')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_USE_PS_PDF',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_USE_PS_PDF')
                );
                if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
                    if ($id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType(QuotationStatus::$OPEN)) {
                        $status = new QuotationStatus($id_roja45_quotation_status);
                        $status->send_email = 1;
                        $status->save();
                    }

                    if ($id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType(QuotationStatus::$SENT)) {
                        $status = new QuotationStatus($id_roja45_quotation_status);
                        $status->send_email = 1;
                        $status->notify_admin = 0;
                        $status->save();
                    }
                }
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_PDF',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_PDF')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_CUSTOMER_EMAIL')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS')
                );
                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')
                );

                if ($groups = Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS')) {
                    $selected_groups = implode(',', $groups);
                    Configuration::updateValue(
                        'ROJA45_QUOTATIONSPRO_ENABLED_GROUPS',
                        $selected_groups
                    );
                }

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER')
                );
                if ((int) Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER')) {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override_classes/classes/Carrier.php') &&
                        !file_exists(_PS_MODULE_DIR_ . $this->name . '/override/classes/Carrier.php')) {
                        copy(
                            _PS_MODULE_DIR_ . $this->name . '/override_classes/classes/Carrier.php',
                            _PS_MODULE_DIR_ . $this->name . '/override/classes/Carrier.php'
                        );
                       // if ($this->getOverrides() != null) {
                            try {
                                $this->installOverrides();
                            } catch (Exception $e) {
                                $this->uninstallOverrides();
                                return false;
                            }
                     //   }
                    }
                } else {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override/classes/Carrier.php')) {
                        $this->uninstallOverrides();
                        unlink(_PS_MODULE_DIR_ . $this->name . '/override/classes/Carrier.php');
                        Tools::generateIndex();
                    }
                }

                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT')
                );
                if ((int) Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT')) {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override_classes/classes/Product.php') &&
                        !file_exists(_PS_MODULE_DIR_ . $this->name . '/override/classes/Product.php')) {
                        copy(
                            _PS_MODULE_DIR_ . $this->name . '/override_classes/classes/Product.php',
                            _PS_MODULE_DIR_ . $this->name . '/override/classes/Product.php'
                        );
                      //  if ($this->getOverrides() != null) {
                            try {
                                $this->installOverrides();
                            } catch (Exception $e) {
                                $this->uninstallOverrides();
                                return false;
                            }
                      //  }
                    }
                } else {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override/classes/Product.php')) {
                        $this->uninstallOverrides();
                        unlink(_PS_MODULE_DIR_ . $this->name . '/override/classes/Product.php');
                        Tools::generateIndex();
                    }
                }

                Configuration::updateValue(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER')
                );
                if ((int) Tools::getValue('ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER')) {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override_classes/controllers/admin/AdminCustomerThreadsController.php') &&
                        !file_exists(_PS_MODULE_DIR_ . $this->name . '/override/controllers/admin/AdminCustomerThreadsController.php')) {
                        copy(
                            _PS_MODULE_DIR_ . $this->name . '/override_classes/controllers/admin/AdminCustomerThreadsController.php',
                            _PS_MODULE_DIR_ . $this->name . '/override/controllers/admin/AdminCustomerThreadsController.php'
                        );
                       // if ($this->getOverrides() != null) {
                            try {
                                $this->installOverrides();
                            } catch (Exception $e) {
                                $this->uninstallOverrides();
                                return false;
                            }
                       // }
                    }
                } else {
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . '/override/controllers/admin/AdminCustomerThreadsController.php')) {
                        $this->uninstallOverrides();
                        unlink(_PS_MODULE_DIR_ . $this->name . '/override/controllers/admin/AdminCustomerThreadsController.php');
                        Tools::generateIndex();
                    }
                }
                self::clearAllCached();
            }

            if (count($validationErrors)) {
                return $validationErrors;
            } else {
                self::clearAllCached();
                $html .= $this->displayConfirmation($this->l('Configuration Updated'));
            }

            return $html;
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            return $validationErrors;
        }
    }

    public function getLiveConfiguratorToken()
    {
        return Tools::getAdminToken('themeconfigurator' . (int) Tab::getIdFromClassName('themeconfigurator')
            . (is_object(Context::getContext()->employee) ? (int) Context::getContext()->employee->id :
                Tools::getValue('id_employee')));
    }

    public function getTemplatePaths16()
    {
        $tpl_paths = array();
        if (file_exists(_PS_THEME_DIR_ . 'modules/roja45quotationspro/request-summary-product-line.tpl')) {
            $tpl_paths['request-summary-product-line'] =
                _PS_THEME_DIR_ . 'modules/roja45quotationspro/request-summary-product-line.tpl';
        } else {
            $tpl_paths['request-summary-product-line'] =
                _PS_MODULE_DIR_ . 'roja45quotationspro/request-summary-product-line.tpl';
        }
    }

    public static function groupEnabled($id_customer)
    {
        $enabled_groups = explode(',', Configuration::get('ROJA45_QUOTATIONSPRO_ENABLED_GROUPS'));
        $members = Customer::getGroupsStatic($id_customer);
        $results = array_intersect($members, $enabled_groups);
        if (count($results) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
