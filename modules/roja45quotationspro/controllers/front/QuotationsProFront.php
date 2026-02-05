<?php
/**
 * roja45quotationsproQuotationsProFrontModuleFrontController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  roja45quotationsproQuotationsProFrontModuleFrontController
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * roja45quotationsproQuotationsProFrontModuleFrontController.
 *
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class roja45quotationsproQuotationsProFrontModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function setMedia()
    {
        parent::setMedia();
        RojaFortyFiveQuotationsProCore::addJs(
            $this,
            _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationspro_summary'
        );

        if (Context::getContext()->language->iso_code != 'en') {
            if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
                $this->context->controller->addJs(
                    'js/jquery/ui/i18n/jquery.ui.datepicker-' . Context::getContext()->language->iso_code . '.js'
                );
            } else {
                $this->context->controller->registerJavascript(
                    'jquery_ui_localizations',
                    'js/jquery/ui/i18n/jquery.ui.datepicker-' .
                    Context::getContext()->language->iso_code . '.js',
                    null
                );
            }
        }
    }

    public function postProcess()
    {
        if ($id_quotation = Tools::getValue('p')) {
            $this->processPurchaseRequest((int) $id_quotation);
        } else if ($reference = Tools::getValue('r')) {
            $hash = Tools::getValue('h');
            $this->processPurchaseRequest($reference, $hash);
        }

        $action = Tools::toCamelCase(Tools::getValue('action'), true);
        if (!$this->ajax && !empty($action) && method_exists($this, 'process' . $action)) {
            $this->{'process' . $action}();
        } elseif (Tools::getValue('submitRefreshCaptcha')) {
            $this->ajaxProcessSubmitRefreshCaptcha();
        } elseif (Tools::getValue('submitUpdateSummaryButtons')) {
            $this->ajaxProcessSubmitUpdateSummaryButtons();
        } else {
            parent::postProcess();
        }
    }

    public function processAddToQuote()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen($id_product = (int) Tools::getValue('id_product')) > 0) {
                $validationErrors[] = $this->module->l('Product Id Required', 'QuotationsProFront');
            }
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');
            if (!$quantity = (int) Tools::getValue('quote_quantity_wanted')) {
                if (!$quantity = (int) Tools::getValue('quantity')) {
                    $quantity = (int) Tools::getValue('minimal_quantity');
                }
            }
            if (!$quantity) {
                $quantity = 1;
            }
            $mode = trim(Tools::getValue('mode'));
            if (!count($validationErrors)) {
                $this->addProduct($id_product, $id_product_attribute, $quantity, $mode);
            } else {
                $this->errors = $validationErrors;
            }
            $this->processQuoteSummary();
        } catch (Exception $e) {
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'request-summary.tpl');
            $validationErrors[] = $e->getMessage();
            $this->errors = $validationErrors;
        }
    }

    public function processGetCustomerQuotes()
    {
        if (!Context::getContext()->customer->isLogged()) {
            $back = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'getCustomerQuotes',
                ),
                true
            );
            Tools::redirect('index.php?controller=authentication&back=' . $back);
        }

        $this->page_name = 'module-roja45quotationspro-QuotationsProFront-GetCustomerQuotes';
        if (Context::getContext()->customer->id) {
            $ids = RojaQuotation::getQuotationsForCustomer($this->context->customer->id);
            $customerquotes = array();

            foreach ($ids as $id) {
                $quotation = new RojaQuotation($id['id_roja45_quotation']);
                $products = $quotation->getProducts();
                if (count($products)) {
                    $quotation->expired = 0;
                    if ($quotation->hasExpired()) {
                        $quotation->expired = 1;
                    }
                    $quotation->ordered = 0;
                    if ($quotation->id_order != 0) {
                        $quotation->ordered = 1;
                        $order = new Order($quotation->id_order);
                        $quotation->last_ordered = $order->date_add;
                    } elseif (QuotationOrder::hasOrders($id['id_roja45_quotation'])) {
                        $quotation->ordered = 1;
                        $order = QuotationOrder::getLastOrder($id['id_roja45_quotation']);
                        $quotation->last_ordered = $order[0]['date_add'];
                    }
                    $totals_exc = $quotation->getQuotationTotals(false);
                    $totals_inc = $quotation->getQuotationTotals(true);
                    $quotation->total_exc = $totals_exc['quotation_total_products'];
                    $quotation->total_exc_formatted = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::convertPrice($totals_exc['quotation_total_products']),
                        $this->context->currency
                    );
                    $quotation->total_shipping_exc = $totals_exc['quotation_total_shipping'];
                    $quotation->total_shipping_exc_formatted = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::convertPrice($totals_exc['quotation_total_shipping']),
                        $this->context->currency
                    );
                    $quotation->total_inc = $totals_inc['quotation_total_products'];
                    $quotation->total_inc_formatted = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::convertPrice($totals_inc['quotation_total_products']),
                        $this->context->currency
                    );
                    $quotation->total_shipping_inc = $totals_inc['quotation_total_shipping'];
                    $quotation->total_shipping_inc_formatted = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::convertPrice($totals_inc['quotation_total_shipping'] + $totals_inc['quotation_total_handling']),
                        $this->context->currency
                    );
                    $customerquotes[] = $quotation;
                }
            }
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
            $this->context->smarty->assign(array(
                'num_address' => count($addresses),
                'customerquotes' => $customerquotes,
                'catalog_mode' => (int) Configuration::get('PS_CATALOG_MODE'),
                'roja45_multiple_customer_orders' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS'
                ),
                'tax_enabled' => Configuration::get('PS_TAX'),
                'customer_group_without_tax' => Group::getPriceDisplayMethod(
                    $this->context->customer->id_default_group
                ),
                'roja45quotationspro_iconpack' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
            ));
            Media::addJsDef(array(
                'roja45_quotationspro_controller' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(),
                    true
                ),
                'roja45_order_page_url' => $this->context->link->getPageLink('order', true),
            ));

            $false = false;
            Media::addJsDefL(
                array('roja45quotationspro_sent_failed'),
                $this->module->l('Unable to send request. Please try again later.', 'QuotationsProFront'),
                null,
                $false
            );
            Media::addJsDefL(
                array('roja45_quotationspro_unknown_error'),
                $this->module->l(
                    'An unexpected error has occurred, please raise this with your support provider.',
                    'QuotationsProFront'
                ),
                null,
                $false
            );
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'customer-quotes.tpl');
        }
    }

    public function processGetQuotationDetails()
    {
        if (!Context::getContext()->customer->isLogged()) {
            $back = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'getQuotationDetails',
                    'id_roja45_quotation' => Tools::getValue('id_roja45_quotation'),
                ),
                true
            );
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($back));
        }
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                throw new Exception($this->module->l(
                    'Quotation Id required.'
                ));
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                throw new Exception($this->module->l(
                    'The quotation could not be loaded.',
                    'QuotationsProFront'
                ));
            }

            if ((int) $quotation->id_customer != (int) Context::getContext()->customer->id) {
                throw new Exception($this->module->l(
                    'The quotation is not yours.',
                    'QuotationsProFront'
                ));
            }

            $quotation->expired = 0;
            if ($quotation->expiry_date != '0000-00-00 00:00:00') {
                $date = new DateTime($quotation->expiry_date);
                if (new DateTime() > $date) {
                    $quotation->expired = 1;
                }
            }
            $quotation->ordered = 0;
            if ($quotation->id_order != 0) {
                $quotation->ordered = 1;
            }
            $quotation->total_exc = $quotation->getQuotationTotal(false);
            $quotation->total_exc_formatted = Tools::displayPrice(Tools::convertPrice($quotation->total_exc));
            $quotation->total_inc = $quotation->getQuotationTotal(true);
            $quotation->total_inc_formatted = Tools::displayPrice(Tools::convertPrice($quotation->total_inc));

            if (!count($validationErrors)) {
                $id_customer_thread = QuotationMessage::getCustomerThread(
                    $quotation->id
                );

                $message_thread = new CustomerThread($id_customer_thread);
                $messages = array();
                if ($message_ids = $message_thread->getWsCustomerMessages()) {
                    foreach ($message_ids as $message_id) {
                        $customer_message = new CustomerMessage($message_id['id']);
                        $name = 'you';
                        if ($customer_message->id_employee) {
                            $employee = new Employee($customer_message->id_employee);
                            $name = $employee->firstname;
                        }
                        $messages[] = array(
                            'id_employee' => (int) $customer_message->id_employee,
                            'name' => $name,
                            'message' => $customer_message->message,
                            'file_name' => $customer_message->file_name,
                            'private' => (int) $customer_message->private,
                            'date_add' => $customer_message->date_add,
                            'date_upd' => $customer_message->date_upd,
                        );
                    }
                }
                usort($messages, array($this, "dateCompare"));

                $products = $quotation->getProducts();
                $discounts = $quotation->getQuotationChargeList(QuotationCharge::$DISCOUNT);
                $documents = $quotation->getDocuments();
                $charges = $quotation->getQuotationAllCharges();
                $shipping = $quotation->getQuotationShippingCharges($quotation->id_lang);
                $display_tax = !Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $summary = $quotation->getSummaryDetails(
                    $quotation->id_lang,
                    Context::getContext()->currency->id,
                    $display_tax
                );
                $addresses = $this->context->customer->getAddresses($this->context->language->id);
                $this->context->smarty->assign($summary);
                $this->context->smarty->assign(array(
                    'languages' => $this->context->language->getLanguages(true),
                    // 'link' => $this->context->link,
                    'products' => $products,
                    'num_address' => count($addresses),
                    'id_customer_thread' => $id_customer_thread,
                    'charges' => $charges,
                    'shipping' => $shipping,
                    'documents' => $documents,
                    'messages' => $messages,
                    'discounts' => $discounts,
                    'id_roja45_quotation' => $quotation->id_roja45_quotation,
                    'quotation' => $quotation,
                    'show_taxes' => $display_tax,
                    'catalog_mode' => Configuration::get('PS_CATALOG_MODE'),
                    'back' => Tools::getValue('back'),
                    'roja45quotationspro_iconpack' => (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_ICON_PACK'
                    ),
                ));
                RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'customer-quote-details.tpl');
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors = $validationErrors;
        }
    }

    public function dateCompare($a, $b)
    {
        $t2 = strtotime($a['date_add']);
        $t1 = strtotime($b['date_add']);
        return $t1 - $t2;
    }

    public function processGetCustomerQuoteHistory()
    {
        if (!Context::getContext()->customer->isLogged()) {
            $back = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'getCustomerQuoteHistory',
                ),
                true
            );
            Tools::redirect('index.php?controller=authentication&back=' . $back);
        }
        $this->page_name = 'module-roja45quotationspro-QuotationsProFront-GetCustomerQuoteHistory';
        if (Context::getContext()->customer->id) {
            $ids = RojaQuotation::getQuotationsForCustomer($this->context->customer->id);
            $historicalquotes = array();
            foreach ($ids as $id) {
                $quotation = new RojaQuotation($id['id_roja45_quotation']);
                $has_orders = QuotationOrder::hasOrders($id['id_roja45_quotation']);
                $has_expired = $quotation->hasExpired();
                if ($has_orders || $has_expired) {
                    $quotation->total = $quotation->getQuotationTotal((int) $quotation->calculate_taxes);
                    $quotation->total_formatted = Tools::displayPrice(Tools::convertPrice($quotation->total));
                    $historicalquotes[] = $quotation;
                }
            }
            $addresses = $this->context->customer->getAddresses($this->context->language->id);

            $this->context->smarty->assign(array(
                'num_address' => count($addresses),
                'quotes' => $historicalquotes,
            ));
            Media::addJsDef(array(
                'roja45_quotationspro_controller' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(),
                    true
                ),
            ));

            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'customer-quote-history.tpl');
        }
    }

    public function processDeleteFromQuote()
    {
        $validationErrors = array();
        if (!Tools::strlen($id_roja45_quotation_requestproduct = trim(Tools::getValue('id_roja45_quotation_requestproduct'))) > 0) {
            $validationErrors[] = $this->module->l('Product Id Required', 'QuotationsProFront');
        }

        if (!count($validationErrors)) {
            $this->deleteProduct($id_roja45_quotation_requestproduct);
        } else {
            $this->errors = $validationErrors;
        }
        $this->processQuoteSummary();
    }

    public function processQuoteSummary()
    {
        try {
            $controller_url = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(),
                true
            );
            $request = QuotationRequest::getInstance(true);
            $id_quotation_form = null;
            if ($request) {
                $summary = $request->getSummaryDetails();
                $this->context->smarty->assign($summary);
                $num_products = count($summary['quotation_products']);
                $this->context->smarty->assign(array(
                    'numberProducts' => $num_products,
                    'empty' => ($num_products == 0),
                ));
                $id_quotation_form = false;
                if (!$num_products && (int) Configuration::get('ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS')) {
                    $id_quotation_form = (int) Configuration::get(
                        'ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM'
                    );
                } elseif ($num_products) {
                    $id_quotation_form = QuotationForm::getFormIdForProduct(
                        $summary['quotation_products'][0]['id_product'],
                        $this->context->shop->id
                    );
                }

                if (!$id_quotation_form) {
                    $id_quotation_form = QuotationForm::getDefaultFormId($this->context->shop->id);
                }
                $quotation_form = new QuotationForm($id_quotation_form);
                
                $field_address_invoice = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE');
                $field_address_delivery = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY');
                $is_show_checkbox_reuse_address = $request->address_enable_auto_create && $field_address_invoice && $field_address_delivery;
                $form_config = $quotation_form->getFormData();
                $form = $this->module->buildFormComponents($form_config);
                $this->context->smarty->assign(array(
                    'form' => $form,
                    'columns' => $form_config['cols'],
                    'col_width' => 12 / $form_config['cols'],
                ));
            } else {
                $this->context->smarty->assign(array(
                    'empty' => 1,
                ));
            }

            $display_tax = Configuration::get('PS_TAX') ?
            !(Group::getPriceDisplayMethod(Group::getCurrent()->id)) :
            0;
            $lastProductAdded = null;
            $this->context->smarty->assign(array(
                'roja45_quoationspro_controller' => $controller_url,
                'id_language' => $this->context->language->id,
                'home_url' => $this->context->link->getPageLink('index', true, null),
                'roja45quotationspro_enable_captcha' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'
                ),
                'roja45quotationspro_replace_zero_price' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE'
                ),
                'roja45quotationspro_replace_zero_price_text' => $this->module->l(
                    'Price Requested',
                    'QuotationsProFront'
                ),
                'roja45quotationspro_enable_captchatype' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_CAPTCHATYPE'
                ),
                'is_show_checkbox_reuse_address' => $is_show_checkbox_reuse_address,
                'field_address_delivery' => $field_address_delivery,
                'last_item_can_append_checkbox_address' => 'ROJA45QUOTATIONSPRO_CUSTOMER_DNI',
                'in_cart' => true,
                'lastProductAdded' => $lastProductAdded,
                'sl_country' => (int) $this->context->country->id,
                'roja45quotationspro_recaptcha_site_key' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE'
                ),
                'roja45quotationspro_usepspdf' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_USE_PS_PDF'
                ),
                'roja45quotationspro_enable_fileupload' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD'
                ),
                'roja45quotationspro_enable_multiplefileupload' => Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD'
                ),
                'roja45quotationspro_touchspin' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'
                ),
                'token_cart' => Tools::getToken(false),
                'isLogged' => $this->context->customer->isLogged(),
                'smallSize' => Image::getSize(RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small')),
                'cannotModify' => 0,
                'id_employee' => Tools::getValue('id_employee'),
                'live_configurator_token' => Tools::getValue('live_configurator_token'),
                'displayQuantity' => 1,
                'errors' => array(),
                'is_admin' => 0,
                'file_size' => RojaFortyFiveQuotationsProCore::getAsBytes(ini_get('upload_max_filesize')),
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
                'roja45quotationspro_showpriceinsummary' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'
                ),
                'roja45quotationspro_noproductrequest' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS'
                ),
                'display_tax' => $display_tax,
            ));

            Media::addJsDef(array(
                'roja45_quoationspro_controller' => $controller_url,
            ));

            if ($this->context->customer->isLogged()) {
                $this->context->smarty->assign(array(
                    'field_values' => array(
                        'ROJA45QUOTATIONSPRO_FIRSTNAME' => $this->context->customer->firstname,
                        'ROJA45QUOTATIONSPRO_LASTNAME' => $this->context->customer->lastname,
                        'ROJA45QUOTATIONSPRO_EMAIL' => $this->context->customer->email,
                    ),
                ));
            }
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'request-summary.tpl');
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Roja45: Error displaying quotation summary: ' . $e->getMessage(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }

    public function processConvertCartToQuote()
    {
        try {
            $controller_url = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(),
                true
            );
            $cart = new Cart(Tools::getValue('id_cart'));
            $this->context->smarty->assign(array(
                'empty' => 0,
            ));
            if (Validate::isLoadedObject($cart)) {
                $request = QuotationRequest::getInstance(true);

                if ((bool)Configuration::get(
                    'ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id,
                    1
                )) {
                    $request->deleteProducts();
                }
                $products = $cart->getProducts(true);
                foreach ($products as $product) {
                    if ($request->containsProduct(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        $product['id_customization']
                    )) {
                        $request->deleteProduct(
                            $product['id_product'],
                            $product['id_product_attribute'],
                            $product['id_customization']
                        );
                    }

                    if (!$request->updateQty(
                        $product['quantity'],
                        $product['id_product'],
                        $product['id_product_attribute'],
                        $product['id_customization'],
                        'up'
                    )) {
                        throw new Exception($this->module->l(
                            'Unable to add requested product to quotation',
                            'QuotationsProFront'
                        ));
                    }

                    $cart->deleteProduct(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        $product['id_customization']
                    );

                    // TODO - remove product from shopping cart?  Deletes the customization, check if creates a problem
                    // when creating an order.  May need to create new prestashop customization.
                }

                $summary = $request->getSummaryDetails();
                $this->context->smarty->assign($summary);
            } else {
                $this->context->smarty->assign(array(
                    'empty' => 1,
                ));
            }

            $form_config = $this->module->getForm();
            $form = $this->module->buildFormComponents($form_config);

            $display_tax = Configuration::get('PS_TAX') ?
                !(Group::getPriceDisplayMethod(Group::getCurrent()->id)) :
                0;

            $lastProductAdded = null;
            $this->context->smarty->assign(array(
                'roja45_quoationspro_controller' => $controller_url,
                'id_language' => $this->context->language->id,
                'home_url' => $this->context->link->getPageLink('index', true, null),
                'form' => $form,
                'columns' => $form_config['cols'],
                'roja45quotationspro_enable_captcha' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'),
                'roja45quotationspro_enable_captchatype' => (int)Configuration::get(
                    'ROJA45_QUOTATIONSPRO_CAPTCHATYPE'
                ),
                'in_cart' => true,
                'lastProductAdded' => $lastProductAdded,
                'col_width' => 12 / $form_config['cols'],
                'sl_country' => (int)$this->context->country->id,
                'roja45quotationspro_recaptcha_site_key' => Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE'),
                'roja45quotationspro_enable_fileupload' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD'),
                'token_cart' => Tools::getToken(false),
                'isLogged' => $this->context->customer->isLogged(),
                'smallSize' => Image::getSize(RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small')),
                'cannotModify' => 0,
                'id_employee' => Tools::getValue('id_employee'),
                'live_configurator_token' => Tools::getValue('live_configurator_token'),
                'displayQuantity' => 1,
                'errors' => array(),
                'is_admin' => 0,
                'file_size' => RojaFortyFiveQuotationsProCore::getAsBytes(ini_get('upload_max_filesize')),
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
                'roja45quotationspro_iconpack' => (int)Configuration::get(
                    'ROJA45_QUOTATIONSPRO_ICON_PACK'
                ),
                'roja45quotationspro_showpriceinsummary' => (int)Configuration::get(
                    'ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'
                ),
                'display_tax' => $display_tax,
            ));

            if (Tools::getValue('live_configurator_token') &&
                Tools::getValue('live_configurator_token') == $this->module->getLiveConfiguratorToken() &&
                Tools::getIsset('id_employee')
            ) {
                $this->context->smarty->assign(array(
                    'roja45quotationspro_enable_captcha' => 0,
                    'is_admin' => 1,
                ));
            }

            Media::addJsDef(array(
                'roja45_quoationspro_controller' => $controller_url,
            ));

            if ($this->context->customer->isLogged()) {
                $this->context->smarty->assign(array(
                    'field_values' => array(
                        'ROJA45QUOTATIONSPRO_FIRSTNAME' => $this->context->customer->firstname,
                        'ROJA45QUOTATIONSPRO_LASTNAME' => $this->context->customer->lastname,
                        'ROJA45QUOTATIONSPRO_EMAIL' => $this->context->customer->email,
                    ),
                ));
            }
            $this->redirect_after = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'quoteSummary',
                ),
                true
            );
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Roja45: Error converting cart to quote: ' . $e->getMessage(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }

    public function processConvertToQuote()
    {
        $validationErrors = array();

        if (!count($validationErrors)) {
            $controller_url = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(),
                true
            );
            $request = QuotationRequest::getInstance(true);
            $product_ids = Tools::getValue('product_ids');
            if (count($product_ids)) {
                foreach ($product_ids as $key => $product_id) {
                    $productObj = new Product($product_id);
                    $id_product_attribute = 0;
                    if ($product_id_attributes = Tools::getValue('product_id_attributes')) {
                        $id_product_attribute = $product_id_attributes[$key];
                    }

                    $sql = new DbQuery();
                    $sql->select('id_customization');
                    $sql->from('customization');
                    $sql->where('id_cart=' . (int) $this->context->cart->id);
                    $sql->where('id_product=' . (int) $product_id);
                    $sql->where('id_product_attribute=' . (int) $id_product_attribute);
                    $id_customization = (int) Db::getInstance()->getValue($sql);

                    if (!$request->updateQty(
                        $productObj->minimal_quantity,
                        $product_id,
                        $id_product_attribute,
                        $id_customization,
                        'up'
                    )) {
                        throw new Exception($this->module->l(
                            'Unable to add requested product to quotation',
                            'QuotationsProFront'
                        ));
                    }
                }

                $summary = $request->getSummaryDetails();
                $this->context->smarty->assign($summary);
                $this->context->smarty->assign(array(
                    'numberProducts' => count($summary['quotation_products']),
                    'empty' => (count($summary['quotation_products']) == 0),
                ));
            } else {
                $this->context->smarty->assign(array(
                    'empty' => 1,
                ));
            }

            $form_config = $this->module->getForm();
            $form = $this->module->buildFormComponents($form_config);

            $lastProductAdded = null;
            $this->context->smarty->assign(array(
                'roja45_quoationspro_controller' => $controller_url,
                'id_language' => $this->context->language->id,
                'home_url' => $this->context->link->getPageLink('index', true, null),
                'form' => $form,
                'columns' => $form_config['cols'],
                'roja45quotationspro_enable_captcha' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'),
                'roja45quotationspro_enable_captchatype' => (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_CAPTCHATYPE'
                ),
                'in_cart' => true,
                'lastProductAdded' => $lastProductAdded,
                'col_width' => 12 / $form_config['cols'],
                'sl_country' => (int) $this->context->country->id,
                'roja45quotationspro_recaptcha_site_key' => Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE'),
                'roja45quotationspro_enable_fileupload' => Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD'),
                'token_cart' => Tools::getToken(false),
                'isLogged' => $this->context->customer->isLogged(),
                'smallSize' => Image::getSize(RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small')),
                'cannotModify' => 0,
                'id_employee' => Tools::getValue('id_employee'),
                'live_configurator_token' => Tools::getValue('live_configurator_token'),
                'displayQuantity' => 1,
                'errors' => array(),
                'is_admin' => 0,
                'file_size' => RojaFortyFiveQuotationsProCore::getAsBytes(ini_get('upload_max_filesize')),
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
            ));

            if (Tools::getValue('live_configurator_token') &&
                Tools::getValue('live_configurator_token') == $this->module->getLiveConfiguratorToken() &&
                Tools::getIsset('id_employee')
            ) {
                $this->context->smarty->assign(array(
                    'roja45quotationspro_enable_captcha' => 0,
                    'is_admin' => 1,
                ));
            }

            Media::addJsDef(array(
                'roja45_quoationspro_controller' => $controller_url,
            ));

            if ($this->context->customer->isLogged()) {
                $this->context->smarty->assign(array(
                    'field_values' => array(
                        'ROJA45QUOTATIONSPRO_FIRSTNAME' => $this->context->customer->firstname,
                        'ROJA45QUOTATIONSPRO_LASTNAME' => $this->context->customer->lastname,
                        'ROJA45QUOTATIONSPRO_EMAIL' => $this->context->customer->email,
                    ),
                ));
            }
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'request-summary.tpl');
        }
    }

    public function processSubmitRequest()
    {
        RojaQuotationLogger::debug('QuotationsProFrontModuleFrontController: processSubmitRequest');

        if ((int) Configuration::getGlobalValue('RJ45DISMOD')) {
            return false;
        }

        try {
            RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA')) {
                if (Tools::strlen(trim(Tools::getValue('g-recaptcha-response'))) > 0) {
                    $secret = Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET');
                    if (!Tools::strlen(trim($secret))) {
                        throw new Exception($this->module->l(
                            'No reCaptcha secret key available',
                            'QuotationsProFront'
                        ));
                    }
                    $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\CurlPost());
                    $resp = $recaptcha->verify(Tools::getValue('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);
                    if (!$resp->isSuccess()) {
                        throw new Exception('[' .
                            implode("|", $resp->getErrorCodes()) .
                            '] ' . $this->module->l(
                                'Your reCAPTCHA challenge has failed, are you a robot?',
                                'QuotationsProFront'
                            ));
                    }
                } else {
                    throw new Exception($this->module->l(
                        'No reCaptcha challenge provided',
                        'QuotationsProFront'
                    ));
                }
            }

            RojaQuotationLogger::debug('QuotationsProFrontModuleFrontController: processSubmitRequest: submitRequest');
            $quotation_request = $this->submitRequest();
            $request_received = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'requestReceived',
                    'request_id' => $quotation_request->id
                ),
                true
            );
            Tools::redirect($request_received);

            /*if ($this->submitRequest()) {

            } else {
                throw new Exception($this->module->l(
                    'Unable to submit request.',
                    'QuotationsProFront'
                ));
            }*/
        } catch (Exception $e) {
            RojaQuotationLogger::error('QuotationsProFrontModuleFrontController: processSubmitRequest: submitRequest', [
                'exception' => $e
            ]);
            $this->errors[] = $e->getMessage();
            $this->processQuoteSummary();
        }
    }

    public function processRequestReceived()
    {
        if ((int) Configuration::getGlobalValue('RJ45DISMOD')) {
            return false;
        }

        try {
            RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
            );
            $id_quotations_request = Tools::getValue('request_id');
            $request = new QuotationRequest($id_quotations_request, $this->context->language->id);
            $this->context->smarty->assign(array(
                'home_url' => $this->context->link->getPageLink('index', true, null),
                'account_link' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
                'quotation_request' => $request->getSummaryDetails($this->context->language->id),
                'isLogged' => $this->context->customer->isLogged(),
            ));
            QuotationRequest::reset();
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'request-received.tpl');
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->processQuoteSummary();
        }
    }

    public function processSubmitRequestOrder()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->module->l('No quotation id provided.', 'QuotationsProFront');
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }
            if ((int) $quotation->id_customer != (int) Context::getContext()->customer->id) {
                $validationErrors[] = $this->module->l('The quotation is not yours.', 'QuotationsProFront');
            }
            if (!count($validationErrors)) {
                $template_vars = array();

                $hide_prices = (bool) Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE');
                $quotation_details = $quotation->getSummaryDetails(null, null, true, $hide_prices);
                $template_vars = array_merge(
                    $template_vars,
                    $quotation_details
                );

                $quotation->setStatus(
                    QuotationStatus::$CORD,
                    $template_vars
                );
                $quotation->save();
            }
            Tools::redirect($this->context->link->getPageLink('my-account', true));
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = Tools::displayError($e->getMessage());
            if (_PS_MODE_DEV_ === true) {
                $this->errors[] = Tools::displayError($e->getTraceAsString);
            }
            $this->processGetCustomerQuotes();
        }
    }

    public function processSubmitAddToCart()
    {
        $validationErrors = array();
        try {
            RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                'ROJA45QUOTATIONSPRO_QUOTEINCART',
                0
            );
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->module->l(
                    'No quotation id provided.',
                    'QuotationsProFront'
                );
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }

            if ((int) $quotation->id_customer != (int) Context::getContext()->customer->id) {
                $validationErrors[] = $this->module->l(
                    'The quotation is not yours.',
                    'QuotationsProFront'
                );
            }

            if ($quotation->hasExpired()) {
                $validationErrors[] = $this->module->l(
                    'This quote has expired, please request a new quote.',
                    'QuotationsProFront'
                );
            }

            if (!count($validationErrors)) {
                $ids = RojaQuotation::getQuotationsForCustomer($this->context->customer->id);
                foreach ($ids as $id) {
                    $reset_quotation = new RojaQuotation($id['id_roja45_quotation']);
                    if ($reset_quotation->id_order == 0) {
                        $reset_quotation->id_cart = 0;
                        $reset_quotation->tmp_password = '';
                        $reset_quotation->save();
                    }
                }
                $products = $quotation->getProducts();
                if (!Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY')) {
                    Tools::redirect('index.php?controller=myaccount');
                }
                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_QUOTEINCART',
                    $quotation->id
                );
                if ($quotation->populateCart($products, $this->context->currency->id)) {
                    $quotation->setStatus(QuotationStatus::$CART);
                    $quotation->id_cart = $this->context->cart->id;
                    $quotation->update();

                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_ID_QUOTATION',
                        $quotation->id
                    );
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_QUOTEMODIFIED',
                        0
                    );
                    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                        Tools::redirect('index.php?controller=cart&action=show');
                    } else {
                        Tools::redirect('index.php?controller=order');
                    }
                } else {
                    $this->errors[] = Tools::displayError(
                        'Unable to populate customer cart.',
                        !Tools::getValue('ajax')
                    );
                }
            }
            Tools::redirect('index.php?controller=myaccount');
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = $e->getMessage();
            $this->processGetCustomerQuotes();
        }
    }

    public function processDownloadFile()
    {
        if (!Context::getContext()->customer->isLogged()) {
            $back = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'getCustomerQuotes',
                ),
                true
            );
            Tools::redirect('index.php?controller=authentication&back=' . $back);
        }

        $validationErrors = array();
        try {
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->module->l(
                    'No quotation id provided.',
                    'QuotationsProFront'
                );
            }
            if (!$id_roja45_quotation_document = Tools::getValue('file')) {
                $validationErrors[] = $this->module->l(
                    'No document id provided.',
                    'QuotationsProFront'
                );
            }

            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                $document = $quotation->getDocument($id_roja45_quotation_document);

                if ($document['id_roja45_document']) {
                    $subdir = '';
                } else {
                    $subdir = $quotation->reference . DIRECTORY_SEPARATOR;
                }
                $file = _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' .
                    $subdir . DIRECTORY_SEPARATOR . $document['internal_name'];
                if (!Validate::isFileName($document['internal_name']) ||
                    !file_exists($file)) {
                    throw new Exception($this->module->l(
                        'This file no longer exists',
                        'QuotationsProFront'
                    ));
                }

                $filename = $document['display_name'];

                /* Detect mime content type */
                $mimeType = false;
                if (function_exists('finfo_open')) {
                    $finfo = @finfo_open(FILEINFO_MIME);
                    $mimeType = @finfo_file($finfo, $file);
                    @finfo_close($finfo);
                } elseif (function_exists('mime_content_type')) {
                    $mimeType = @mime_content_type($file);
                }
                if (empty($mimeType)) {
                    $bName = basename($filename);
                    $bName = explode('.', $bName);
                    $bName = strtolower($bName[count($bName) - 1]);

                    $mimeTypes = [
                        'ez' => 'application/andrew-inset',
                        'hqx' => 'application/mac-binhex40',
                        'cpt' => 'application/mac-compactpro',
                        'doc' => 'application/msword',
                        'oda' => 'application/oda',
                        'pdf' => 'application/pdf',
                        'ai' => 'application/postscript',
                        'eps' => 'application/postscript',
                        'ps' => 'application/postscript',
                        'smi' => 'application/smil',
                        'smil' => 'application/smil',
                        'wbxml' => 'application/vnd.wap.wbxml',
                        'wmlc' => 'application/vnd.wap.wmlc',
                        'wmlsc' => 'application/vnd.wap.wmlscriptc',
                        'bcpio' => 'application/x-bcpio',
                        'vcd' => 'application/x-cdlink',
                        'pgn' => 'application/x-chess-pgn',
                        'cpio' => 'application/x-cpio',
                        'csh' => 'application/x-csh',
                        'dcr' => 'application/x-director',
                        'dir' => 'application/x-director',
                        'dxr' => 'application/x-director',
                        'dvi' => 'application/x-dvi',
                        'spl' => 'application/x-futuresplash',
                        'gtar' => 'application/x-gtar',
                        'hdf' => 'application/x-hdf',
                        'js' => 'application/x-javascript',
                        'skp' => 'application/x-koan',
                        'skd' => 'application/x-koan',
                        'skt' => 'application/x-koan',
                        'skm' => 'application/x-koan',
                        'latex' => 'application/x-latex',
                        'nc' => 'application/x-netcdf',
                        'cdf' => 'application/x-netcdf',
                        'sh' => 'application/x-sh',
                        'shar' => 'application/x-shar',
                        'swf' => 'application/x-shockwave-flash',
                        'sit' => 'application/x-stuffit',
                        'sv4cpio' => 'application/x-sv4cpio',
                        'sv4crc' => 'application/x-sv4crc',
                        'tar' => 'application/x-tar',
                        'tcl' => 'application/x-tcl',
                        'tex' => 'application/x-tex',
                        'texinfo' => 'application/x-texinfo',
                        'texi' => 'application/x-texinfo',
                        't' => 'application/x-troff',
                        'tr' => 'application/x-troff',
                        'roff' => 'application/x-troff',
                        'man' => 'application/x-troff-man',
                        'me' => 'application/x-troff-me',
                        'ms' => 'application/x-troff-ms',
                        'ustar' => 'application/x-ustar',
                        'src' => 'application/x-wais-source',
                        'xhtml' => 'application/xhtml+xml',
                        'xht' => 'application/xhtml+xml',
                        'zip' => 'application/zip',
                        'au' => 'audio/basic',
                        'snd' => 'audio/basic',
                        'mid' => 'audio/midi',
                        'midi' => 'audio/midi',
                        'kar' => 'audio/midi',
                        'mpga' => 'audio/mpeg',
                        'mp2' => 'audio/mpeg',
                        'mp3' => 'audio/mpeg',
                        'aif' => 'audio/x-aiff',
                        'aiff' => 'audio/x-aiff',
                        'aifc' => 'audio/x-aiff',
                        'm3u' => 'audio/x-mpegurl',
                        'ram' => 'audio/x-pn-realaudio',
                        'rm' => 'audio/x-pn-realaudio',
                        'rpm' => 'audio/x-pn-realaudio-plugin',
                        'ra' => 'audio/x-realaudio',
                        'wav' => 'audio/x-wav',
                        'pdb' => 'chemical/x-pdb',
                        'xyz' => 'chemical/x-xyz',
                        'bmp' => 'image/bmp',
                        'gif' => 'image/gif',
                        'ief' => 'image/ief',
                        'jpeg' => 'image/jpeg',
                        'jpg' => 'image/jpeg',
                        'jpe' => 'image/jpeg',
                        'png' => 'image/png',
                        'tiff' => 'image/tiff',
                        'tif' => 'image/tif',
                        'djvu' => 'image/vnd.djvu',
                        'djv' => 'image/vnd.djvu',
                        'wbmp' => 'image/vnd.wap.wbmp',
                        'ras' => 'image/x-cmu-raster',
                        'pnm' => 'image/x-portable-anymap',
                        'pbm' => 'image/x-portable-bitmap',
                        'pgm' => 'image/x-portable-graymap',
                        'ppm' => 'image/x-portable-pixmap',
                        'rgb' => 'image/x-rgb',
                        'xbm' => 'image/x-xbitmap',
                        'xpm' => 'image/x-xpixmap',
                        'xwd' => 'image/x-windowdump',
                        'igs' => 'model/iges',
                        'iges' => 'model/iges',
                        'msh' => 'model/mesh',
                        'mesh' => 'model/mesh',
                        'silo' => 'model/mesh',
                        'wrl' => 'model/vrml',
                        'vrml' => 'model/vrml',
                        'css' => 'text/css',
                        'html' => 'text/html',
                        'htm' => 'text/html',
                        'asc' => 'text/plain',
                        'txt' => 'text/plain',
                        'rtx' => 'text/richtext',
                        'rtf' => 'text/rtf',
                        'sgml' => 'text/sgml',
                        'sgm' => 'text/sgml',
                        'tsv' => 'text/tab-seperated-values',
                        'wml' => 'text/vnd.wap.wml',
                        'wmls' => 'text/vnd.wap.wmlscript',
                        'etx' => 'text/x-setext',
                        'xml' => 'text/xml',
                        'xsl' => 'text/xml',
                        'mpeg' => 'video/mpeg',
                        'mpg' => 'video/mpeg',
                        'mpe' => 'video/mpeg',
                        'qt' => 'video/quicktime',
                        'mov' => 'video/quicktime',
                        'mxu' => 'video/vnd.mpegurl',
                        'avi' => 'video/x-msvideo',
                        'movie' => 'video/x-sgi-movie',
                        'ice' => 'x-conference-xcooltalk',
                    ];

                    if (isset($mimeTypes[$bName])) {
                        $mimeType = $mimeTypes[$bName];
                    } else {
                        $mimeType = 'application/octet-stream';
                    }
                }

                if (ob_get_level() && ob_get_length() > 0) {
                    ob_end_clean();
                }

                /* Set headers for download */
                header('Content-Transfer-Encoding: binary');
                header('Content-Type: ' . $mimeType);
                header('Content-Length: ' . filesize($file));
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                //prevents max execution timeout, when reading large files
                @set_time_limit(0);
                $fp = fopen($file, 'rb');

                if ($fp && is_resource($fp)) {
                    while (!feof($fp)) {
                        echo fgets($fp, 16384);
                    }
                }

                exit;
            }
            Tools::redirect('index.php?controller=myaccount');
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = Tools::displayError($e->getMessage());
        }
    }

    public function processDownloadPDF()
    {
        $validationErrors = array();
        try {
            RojaFortyFiveQuotationsProCore::saveCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART', 0);
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->module->l('No quotation id provided.', 'QuotationsProFront');
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'error' => Tools::displayError('The quotation could not be loaded.', 'QuotationsProFront'),
                    )
                ));
            }

            $quotation->generateQuotationPDF(true, !Group::getPriceDisplayMethod(Group::getCurrent()->id));
            exit;
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = Tools::displayError($e->getMessage());
            $this->processGetCustomerQuotes();
        }
    }

    public function processDownloadRequestPDF()
    {
        $validationErrors = array();
        try {
            $cart = new Cart(Tools::getValue('id_cart'));
            if (Validate::isLoadedObject($cart)) {
                $request = QuotationRequest::getInstance(true);
                $request->deleteProducts();
                $products = $cart->getProducts(true);
                foreach ($products as $product) {
                    if (!$request->updateQty(
                        $product['quantity'],
                        $product['id_product'],
                        $product['id_product_attribute'],
                        $product['id_customization'],
                        'up'
                    )) {
                        throw new Exception($this->module->l(
                            'Unable to add requested product to quotation',
                            'QuotationsProFront'
                        ));
                    }
                }
                if ($request) {
                    if (empty($request->reference)) {
                        $request->reference = RojaQuotation::generateReference();
                        $request->save();
                    }
                    $request->generatePDF(true);
                    exit;
                }
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = Tools::displayError($e->getMessage());
            $this->processGetCustomerQuotes();
        }
    }

    public function processCustomerDelete()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (Validate::isLoadedObject($quotation)) {
                $quotation->setStatus(QuotationStatus::$DLTD);
            }
            $this->processGetCustomerQuotes();
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors[] = Tools::displayError($e->getMessage());
            $this->processGetCustomerQuotes();
        }
    }

    public function processPurchaseRequest($quotation_id, $hash = false)
    {
        if (!Context::getContext()->customer->isLogged()) {
            if ($hash) {
                $params = [
                    'r' => $quotation_id,
                    'h' => $hash
                ];
            } else {
                $params = [
                    'p' => $quotation_id
                ];
            }
            $back = $this->context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                $params,
                true
            );
            $url = $this->context->link->getPageLink(
                'authentication',
                true,
                null,
                'back=' . urlencode($back)
            );
            Tools::redirect($url);
        }
        if (!RojaFortyFiveQuotationsProLicense::validateUpdate($this->module)) {
            return;
        }
        $validationErrors = array();
        try {
            if (is_int($quotation_id) && $quotation_id) {
                $quotation = new RojaQuotation($quotation_id);
                if (!Validate::isLoadedObject($quotation)) {
                    $this->errors[] = $this->module->l(
                        'Unable to load requested quotation [CODE=404]',
                        'QuotationsProFront'
                    );
                }
                if (Context::getContext()->customer->email != $quotation->email) {
                    $this->errors[] = $this->module->l(
                        'Unable to load requested quotation [CODE=503]',
                        'QuotationsProFront'
                    );
                }
            } else if ($hash) {
                /** @var RojaQuotation $quotation */
                if (!$id_quotation = RojaQuotation::getQuotationForReference($quotation_id)) {
                    $this->errors[] = $this->module->l(
                        'Unable to load requested quotation [CODE=404]',
                        'QuotationsProFront'
                    );
                }
                $quotation = new RojaQuotation($id_quotation);
                if (!Validate::isLoadedObject($quotation)) {
                    $this->errors[] = $this->module->l(
                        'Unable to load requested quotation [CODE=404]',
                        'QuotationsProFront'
                    );
                }

                if ($hash != hash('md5', $quotation->email)) {
                    $this->errors[] = $this->module->l(
                        'Unable to load requested quotation [CODE=503]',
                        'QuotationsProFront'
                    );
                }
            } else {
                $this->errors[] = $this->module->l(
                    'Unable to load requested quotation [CODE=404]',
                    'QuotationsProFront'
                );
                RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'error-page.tpl');
                return;
            }

            $closed_status = [
                Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_DLTD'),
                Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_CLSD'),
                Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_INCP'),
            ];

            if (in_array($quotation->id_roja45_quotation_status, $closed_status)) {
                $this->errors[] = $this->module->l(
                    'Unable to load requested quotation [CODE=504]',
                    'QuotationsProFront'
                );
                RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'error-page.tpl');
                return;
            }

            if (new DateTime() > new DateTime($quotation->expiry_date)) {
                $this->errors[] = $this->module->l(
                    'Expired [CODE=410]',
                    'QuotationsProFront'
                );
                RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'error-page.tpl');
                return;
            }
            if ((int) Configuration::get('PS_CATALOG_MODE')) {
                $_GET['id_roja45_quotation'] = $quotation->id;
                $this->processGetQuotationDetails();
            } else {
                $products = $quotation->getProducts();
                if ($quotation->populateCart($products, $this->context->currency->id)) {
                    $quotation->setStatus(QuotationStatus::$CART);
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_QUOTEINCART',
                        $quotation->id
                    );
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_ID_QUOTATION',
                        $quotation->id_roja45_quotation
                    );
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEMODIFIED', 0);
                    Tools::redirect('index.php?controller=order');
                } else {
                    $this->errors[] = Tools::displayError(
                        'Unable to populate customer cart.',
                        !Tools::getValue('ajax')
                    );
                }
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $this->errors = $validationErrors;
            $this->context->smarty->assign(array(
                'errors' => $validationErrors,
                'back' => $this->context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getCustomerQuotes',
                    ),
                    true
                ),
            ));
            RojaFortyFiveQuotationsProCore::setFrontControllerTemplate($this, 'error-page.tpl');
        }
    }

    public function displayAjaxValidateRecaptcha()
    {
        if ((int) Configuration::getGlobalValue('RJ45DISMOD')) {
            return false;
        }

        try {
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA')) {
                if (Tools::strlen(trim(Tools::getValue('g-recaptcha-response'))) > 0) {
                    $secret = Configuration::get('ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET');
                    if (!Tools::strlen(trim($secret))) {
                        throw new Exception($this->module->l(
                            'No reCaptcha secret key available',
                            'QuotationsProFront'
                        ));
                    }
                    $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\CurlPost());
                    $resp = $recaptcha->verify(Tools::getValue('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);
                    if (!$resp->isSuccess()) {
                        throw new Exception('[' .
                            implode("|", $resp->getErrorCodes()) .
                            '] ' . $this->module->l(
                                'Your reCAPTCHA challenge has failed, are you a robot?',
                                'QuotationsProFront'
                            ));
                    }
                } else {
                    throw new Exception($this->module->l(
                        'No reCaptcha challenge provided',
                        'QuotationsProFront'
                    ));
                }
            }
            $json = json_encode(array(
                'result' => 1,
                'response' => $this->module->l('Recaptcha validated.', 'QuotationsProFront'),
            ));
            die($json);
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxSubmitInstantRequest()
    {
        if ((int) Configuration::getGlobalValue('RJ45DISMOD')) {
            return false;
        }
        try {
            $this->submitRequest();
            $json = json_encode(array(
                'result' => 1,
            ));
            die($json);
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 'error',
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxSubmitCustomerMessage()
    {
        if ((int) Configuration::getGlobalValue('RJ45DISMOD')) {
            return false;
        }
        $validationErrors = array();
        try {
            $id_roja45_quotation = Tools::getValue('id_roja45_quotation');
            if (!$id_roja45_quotation || !Tools::strlen($id_roja45_quotation)) {
                throw new Exception($this->module->l(
                    'Quotation Id required.',
                    'QuotationsProFront'
                ));
            }

            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                throw new Exception($this->module->l(
                    'Quotation could not be loaded.',
                    'QuotationsProFront'
                ));
            }

            $message = Tools::getValue('message');
            if (!$message || empty($message)) {
                throw new Exception($this->module->l(
                    'Message emtpy.',
                    'QuotationsProFront'
                ));
            }
            $date_add = new DateTime();
            $filename = '';
            $customer_file = false;
            if (isset($_FILES['uploadedfile'])) {
                if (is_array($_FILES['uploadedfile'])) {
                    if (!empty($_FILES['uploadedfile']['tmp_name'])) {
                        $customer_file = array(
                            'name' => $_FILES['uploadedfile']['name'],
                            'type' => $_FILES['uploadedfile']['type'],
                            'tmp_name' => $_FILES['uploadedfile']['tmp_name'],
                            'error' => $_FILES['uploadedfile']['error'],
                            'size' => $_FILES['uploadedfile']['size'],
                        );
                    }
                } else {
                    $customer_file = Tools::strlen($_FILES['uploadedfile']) ? $_FILES['uploadedfile'] : false;
                }

                if ($customer_file) {
                    if (!empty($customer_file['tmp_name'])
                        && $customer_file['tmp_name'] != 'none'
                        && (!isset($customer_file['error']) || !$customer_file['error'])) {
                        $file = $customer_file['tmp_name'];
                        $ext = Tools::substr(
                            $customer_file['name'],
                            strrpos($customer_file['name'], '.') + 1
                        );
                        $filename = substr(md5($quotation->email . $customer_file['name'] . $quotation->id), 0, 17);
                        //$filename = sha1($quotation->email.$customer_file['name'].$quotation->id);
                        $customer_file['filename'] = $filename;

                        if (!move_uploaded_file(
                            $file,
                            _PS_ROOT_DIR_ . _THEME_PROD_PIC_DIR_ . $filename
                        )) {
                            $validationErrors[] = $this->module->l(
                                'Cannot save file to download directory, an error occurred while moving.',
                                'QuotationsProFront'
                            );
                        }

                        $save_dir = _PS_IMG_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference;
                        if (!file_exists($save_dir)) {
                            mkdir(
                                $save_dir,
                                0755,
                                true
                            );
                        }
                        if (!is_dir($save_dir) || !is_writable($save_dir)) {
                            $validationErrors[] = $this->module->l(
                                'Cannot save file to download directory, not writable.',
                                'QuotationsProFront'
                            );
                        }
                        if (!copy(
                            _PS_ROOT_DIR_ . _THEME_PROD_PIC_DIR_ . $filename,
                            $save_dir . DIRECTORY_SEPARATOR . $filename . '.' . $ext
                        )) {
                            $validationErrors[] = sprintf($this->module->l(
                                'Cannot save file to download directory, an error occurred while moving [%s]',
                                'QuotationsProFront'
                            ), $_FILES['uploadedfile']['error']);
                        }

                        if (is_file($file)) {
                            @unlink($file);
                        }
                    } else {
                        $validationErrors[] = sprintf(
                            $this->module->l('Cannot upload file [%s] : Error: %s'),
                            $customer_file['tmp_name'],
                            $customer_file['error']
                        );
                    }
                }
            }

            if (count($validationErrors)) {
                $json = json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                ));
                die($json);
            }
            $customer_message = new CustomerMessage();
            $customer_message->id_customer_thread = Tools::getValue('id_customer_thread');
            $customer_message->id_employee = 0;
            $customer_message->file_name = $filename;
            $customer_message->message = $message;
            $customer_message->add();

            $json = json_encode(array(
                'result' => 1,
                'name' => 'you',
                'message' => $message,
                'date_add' => $date_add->format($this->context->language->date_format_full),
            ));
            die($json);
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxSubmitQuantity()
    {
        $validationErrors = array();
        try {
            $id_roja45_quotation_requestproduct = Tools::getValue('id_roja45_quotation_requestproduct');
            if (!$id_roja45_quotation_requestproduct || !Tools::strlen($id_roja45_quotation_requestproduct)) {
                throw new Exception($this->module->l(
                    'Product Id required.',
                    'QuotationsProFront'
                ));
            }
            $quantity = Tools::getValue('quantity');
            if (!$quantity || !Tools::strlen($quantity)) {
                throw new Exception($this->module->l(
                    'Product quantity required.'
                ));
            }

            if (!count($validationErrors)) {
                $request_product = new QuotationRequestProduct($id_roja45_quotation_requestproduct);
                if (!$request_product->updateQty($quantity)) {
                    $validationErrors[] = $this->module->l(
                        'Unable to update quantity.',
                        'QuotationsProFront'
                    );
                    $arr = array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    );
                    $json = json_encode($arr);
                    die($json);
                } else {
                    QuotationRequest::reset();
                    $json = json_encode(array(
                        'result' => 1,
                        'response' => $this->module->l('Quantity updated.', 'QuotationsProFront'),
                    ));
                    die($json);
                }
            } else {
                $arr = array(
                    'result' => 0,
                    'errors' => $validationErrors,
                );
                $json = json_encode($arr);
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxGetStates()
    {
        $validationErrors = array();
        try {
            $id_country = Tools::getValue('id_country');
            if (!$id_country) {
                throw new Exception($this->module->l(
                    'Country Id required.',
                    'QuotationsProFront'
                ));
            }

            if (!count($validationErrors)) {
                $states = State::getStatesByIdCountry($id_country);
                $json = json_encode(array(
                    'result' => 1,
                    'states' => $states,
                    'response' => $this->module->l('Success', 'QuotationsProFront'),
                ));
                die($json);
            } else {
                $arr = array(
                    'result' => 0,
                    'errors' => $validationErrors,
                );
                $json = json_encode($arr);
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxClearQuoteFromCart()
    {
        $validationErrors = array();
        try {
            if (!count($validationErrors)) {
                $disable_discount = (int) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLECARTRULES');

                if ($cart_products = $this->context->cart->getProducts()) {
                    foreach ($cart_products as $cart_product) {
                        $this->context->cart->deleteProduct(
                            $cart_product['id_product'],
                            $cart_product['id_product_attribute'],
                            $cart_product['id_customization']
                        );

                        RojaFortyFiveQuotationsProCore::deleteCustomerCartProductSpecificPrice(
                            $this->context->customer->id,
                            $this->context->cart->id,
                            $cart_product['id_product'],
                            $cart_product['id_product_attribute']
                        );
                    }
                }
                $cart_rules = CartRule::getCustomerCartRules(
                    (int) $this->context->language->id,
                    $this->context->customer->id,
                    true
                );
                foreach ($cart_rules as $cart_rule) {
                    if ($disable_discount || $cart_rule['id_customer']) {
                        $cart_rule = new CartRule($cart_rule['id_cart_rule']);
                        $cart_rule->delete();
                    }
                }
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_QUOTEINCART');
                RojaFortyFiveQuotationsProCore::clearCustomerRequirement('ROJA45QUOTATIONSPRO_ID_QUOTATION');

                $json = json_encode(array(
                    'result' => 1,
                    'redirect' => 'index.php',
                    'response' => $this->module->l('Success', 'QuotationsProFront'),
                ));
                die($json);
            } else {
                $arr = array(
                    'result' => 0,
                    'errors' => $validationErrors,
                );
                $json = json_encode($arr);
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function displayAjaxAddProductToRequest()
    {
        try {
            $validationErrors = array();
            if (!$id_product = (int) trim(Tools::getValue('id_product'))) {
                $validationErrors[] = $this->module->l('Product Id Required', 'QuotationsProFront');
            }
            $quantity = (int) trim(Tools::getValue('quantity'));
            if (!$quantity) {
                $quantity = (int) Tools::getValue('minimal_quantity');
            }

            $id_product_attribute = (int) trim(Tools::getValue('id_product_attribute'));
            $mode = trim(Tools::getValue('mode'));
            $position = trim(Tools::getValue('position'));
            $mobile = trim(Tools::getValue('mobile'));

            if (!count($validationErrors)) {
                if ($this->addProduct($id_product, $id_product_attribute, $quantity, $mode)) {
                    $quotation_request = QuotationRequest::getInstance(true);
                    $summary = $quotation_request->getSummaryDetails();
                    $product = new Product($id_product);

                    $template = 'displayNav';
                    if ($position == 'top') {
                        $template = 'displayTop';
                    } elseif ($position == 'custom') {
                        $template = ($mobile == 'true') ? 'displayRoja45MobileQuoteCart' : 'displayRoja45QuoteCart';
                    }

                    $quote_cart = RojaFortyFiveQuotationsProCore::displayTemplate(
                        $this->module,
                        $template,
                        array(
                            'product' => $product,
                        ),
                        $template,
                        null
                    );

                    $products = $quotation_request->getProducts();
                    $last_added = null;
                    foreach ($products as $product) {
                        if ($product['id_product'] == $id_product) {
                            $last_added = $product;
                        }
                    }
                    if ($last_added) {
                        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                            $this->context->smarty->assign(array(
                                'product' => $last_added,
                                'nbr_products' => count($products),
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
                            ));
                            $modal = $this->module->fetch(
                                'module:' . $this->module->name . '/views/templates/hook/PS17_quotecart_modal.tpl'
                            );
                            $summary = array_merge($summary, array(
                                'modal' => $modal,
                            ));
                        }
                    }
                    $response = array_merge($summary, array(
                        'template' => $quote_cart,
                        'result' => 1,
                        'response' => $this->module->l('Product Added.', 'QuotationsProFront'),
                    ));
                    $json = json_encode($response);
                    die($json);
                } else {
                    throw new Exception($this->module->l(
                        'Unable to add product to quotation request.',
                        'QuotationsProFront'
                    ));
                }
            } else {
                throw new Exception($this->module->l(
                    'Validation errors',
                    'QuotationsProFront'
                ));
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $arr = array(
                'result' => 0,
                'errors' => $validationErrors,
            );
            $json = json_encode($arr);
            die($json);
        }
    }

    public function displayAjaxLoadTemplate()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen($id_template = trim(Tools::getValue('template'))) > 0) {
                $validationErrors[] = $this->module->l('Template Required', 'QuotationsProFront');
            }

            if (!count($validationErrors)) {
                $template = new RojaQuotation($id_template);

                $quotation_request = QuotationRequest::getInstance();
                foreach ($quotation_request->getProducts() as $product) {
                    $quotation_request->deleteProduct($product['id_product'], $product['id_product_attribute']);
                }

                $products = $template->getProducts();
                foreach ($products as $product) {
                    $this->addProduct($product['id_product'], $product['id_product_attribute'], $product['qty'], 'up');
                }
                // Load products from template.

                $json = json_encode(array(
                    'result' => 1,
                    'response' => $this->module->l('Loading template', 'QuotationsProFront'),
                    'redirect' => $this->context->link->getPageLink(
                        'index',
                        true,
                        null,
                        array(
                            'live_configurator_token' => Tools::getValue('live_configurator_token'),
                            'id_employee' => Tools::getValue('id_employee'),
                        )
                    ),
                ));
                die($json);
            } else {
                $arr = array(
                    'result' => 0,
                    'errors' => $validationErrors,
                );
                $json = json_encode($arr);
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
            ));
            die($json);
        }
    }

    public function displayAjaxDeleteProductFromRequest()
    {
        try {
            $validationErrors = array();
            if (!$id_roja45_quotation_requestproduct = (int) trim(Tools::getValue('id_roja45_quotation_requestproduct'))) {
                $validationErrors[] = $this->module->l('Product Id Required', 'QuotationsProFront');
            }

            if (!count($validationErrors)) {
                if ($this->deleteProduct($id_roja45_quotation_requestproduct)) {
                    $quotation_request = QuotationRequest::getInstance();
                    $number_products = QuotationRequest::getNumberOfProducts($quotation_request->id);
                    $json = json_encode(array_merge($quotation_request->getSummaryDetails(), array(
                        'result' => 'success',
                        'number_products' => $number_products,
                        'response' => $this->module->l('Product Removed.', 'QuotationsProFront'),
                    )));
                    die($json);
                }
            } else {
                $arr = array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                );
                $json = json_encode($arr);
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            $arr = array(
                'result' => 'error',
                'errors' => $validationErrors,
            );
            $json = json_encode($arr);
            die($json);
        }
    }

    public function ajaxProcessSubmitUpdateSummaryButtons()
    {
        $validationErrors = array();
        if (!count($validationErrors)) {
            // Get JSON
            $exploded = explode('_', Tools::getValue('id_product'));
            $id_removed = $exploded[0];
            $products = $this->context->cart->getProducts();
            $enabled_products = $this->module->getEnabledProducts();
            $enable_quotation = false;
            foreach ($products as $product) {
                if (in_array($product['id_product'], $enabled_products) && ($id_removed != $product['id_product'])) {
                    $enable_quotation = true;
                }
            }
            $json = json_encode(array(
                'result' => 'success',
                'enable' => $enable_quotation,
                'response' => $this->module->l('Success', 'QuotationsProFront'),
            ));
            die($json);
        } else {
            $arr = array(
                'result' => 'error',
                'errors' => $validationErrors,
            );
            $json = json_encode($arr);
            die($json);
        }
    }

    private function submitRequest()
    {
        $validationErrors = array();
        if (!Tools::strlen($email = trim(Tools::getValue('ROJA45QUOTATIONSPRO_EMAIL'))) > 0) {
            $validationErrors[] = $this->module->l('Email Address Required', 'QuotationsProFront');
        }
        if (!Tools::strlen($firstname = trim(Tools::getValue('ROJA45QUOTATIONSPRO_FIRSTNAME'))) > 0) {
            $validationErrors[] = $this->module->l('First Name Required', 'QuotationsProFront');
        }
        if (!Tools::strlen($lastname = trim(Tools::getValue('ROJA45QUOTATIONSPRO_LASTNAME'))) > 0) {
            $validationErrors[] = $this->module->l('Last Name Required', 'QuotationsProFront');
        }

        if (!count($validationErrors)) {
            if (!RojaFortyFiveQuotationsProLicense::validateUpdate($this->module)) {
                throw new Exception('Unauthorized request.');
            }
            $request = Tools::getValue('ROJA45QUOTATIONSPRO_FORMDATA');
            if ($quotation_request = QuotationRequest::getInstance()) {
                $quotation_request->form_data = $request;
                $quotation_request->save();
                $filename = null;
                $customer_files = array();

                if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD')) {
                    if (isset($_FILES['uploadedfile'])) {
                        if (is_array($_FILES['uploadedfile'])) {
                            if (is_array($_FILES['uploadedfile']['name'])) {
                                foreach ($_FILES['uploadedfile']['name'] as $key => $filename) {
                                    if (!empty($_FILES['uploadedfile']['tmp_name'][$key])) {
                                        $customer_files[] = array(
                                            'name' => $filename,
                                            'type' => $_FILES['uploadedfile']['type'][$key],
                                            'tmp_name' => $_FILES['uploadedfile']['tmp_name'][$key],
                                            'error' => $_FILES['uploadedfile']['error'][$key],
                                            'size' => $_FILES['uploadedfile']['size'][$key],
                                        );
                                    }
                                }
                            } else {
                                if (!empty($_FILES['uploadedfile']['tmp_name'])) {
                                    $customer_files[] = array(
                                        'name' => $_FILES['uploadedfile']['name'],
                                        'type' => $_FILES['uploadedfile']['type'],
                                        'tmp_name' => $_FILES['uploadedfile']['tmp_name'],
                                        'error' => $_FILES['uploadedfile']['error'],
                                        'size' => $_FILES['uploadedfile']['size'],
                                    );
                                }
                            }
                        } else {
                            $customer_files = Tools::strlen($_FILES['uploadedfile']) ? $_FILES['uploadedfile'] : false;
                        }
                    }

                    if ($customer_files) {
                        foreach ($customer_files as &$customer_file) {
                            if (!empty($customer_file['tmp_name'])
                                && $customer_file['tmp_name'] != 'none'
                                && (!isset($customer_file['error']) || !$customer_file['error'])) {
                                $file = $customer_file['tmp_name'];
                                $filename = sha1($email . $customer_file['name'] . $quotation_request->id);
                                $customer_file['filename'] = $filename;
                                if (!file_exists(_PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation_request->reference)) {
                                    mkdir(
                                        _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation_request->reference,
                                        0755,
                                        true
                                    );
                                }
                                if (!file_exists(
                                    _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation_request->reference . DIRECTORY_SEPARATOR . $filename
                                )) {
                                    if (!move_uploaded_file($file, _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation_request->reference . DIRECTORY_SEPARATOR . $filename)) {
                                        $validationErrors[] = $this->module->l(
                                            'Cannot save file to download directory, an error occurred while moving.',
                                            'QuotationsProFront'
                                        );
                                    }
                                } else {
                                    $validationErrors[] = $this->module->l(
                                        'Cannot save file to download directory, filename already exists.',
                                        'QuotationsProFront'
                                    );
                                }
                                if (is_file($file)) {
                                    @unlink($file);
                                }
                            } else {
                                $validationErrors[] = sprintf(
                                    $this->module->l('Cannot upload file [%s] : Error: %s'),
                                    $customer_file['tmp_name'],
                                    $customer_file['error']
                                );
                            }
                        }
                    }
                }

                if (count($validationErrors)) {
                    $this->errors = $validationErrors;
                    throw new Exception($this->module->l(
                        'Validation errors',
                        'QuotationsProFront'
                    ));
                }

                RojaQuotationLogger::error('QuotationsProFrontModuleFrontController: processSubmitRequest: create quotation', [
                    'email' => $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'quotation_request' => $quotation_request,
                    'customer_files' => $customer_files
                ]);
                if ($this->createQuotation(
                    $email,
                    $firstname,
                    $lastname,
                    $quotation_request,
                    $customer_files
                )) {
                    $quotation_request->requested = 1;
                    $quotation_request->save();
                    Context::getContext()->cookie->__unset(
                        QuotationRequest::getCustomerCookieKey()
                    );
                    Context::getContext()->cookie->__unset(
                        QuotationRequest::getGuestCookieKey()
                    );
                    $this->context->smarty->assign(array(
                        'home_url' => $this->context->link->getPageLink('index', true, null),
                        'account_link' => $this->context->link->getModuleLink(
                            'roja45quotationspro',
                            'QuotationsProFront',
                            array(
                                'action' => 'getCustomerQuotes',
                            ),
                            true
                        ),
                    ));
                    return $quotation_request;
                } else {
                    throw new Exception($this->module->l(
                        'Unable to create quotation',
                        'QuotationsProFront'
                    ));
                }
            } else {
                throw new Exception($this->module->l(
                    'No request to process',
                    'QuotationsProFront'
                ));
            }
        }
        throw new Exception('Validation errors.');
    }

    private function addProduct($id_product, $id_product_attribute, $quantity, $mode)
    {
        $quotation_request = QuotationRequest::getInstance(true);
        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEQUOTECART')) {
            foreach ($quotation_request->getProducts() as $product) {
                if ($product['id_product'] != $id_product) {
                    $this->deleteProduct($product['id_roja45_quotation_requestproduct']);
                }
            }
        }

        if (isset($id_product_attribute) && $id_product_attribute) {
            $combination = new Combination($id_product_attribute);
            if (!Validate::isLoadedObject($combination)) {
                throw new Exception($this->module->l(
                    'The combination object cannot be loaded.',
                    'QuotationsProFront'
                ));
            }
        }

        $id_customization = QuotationCustomization::getProductCustomization(
            $this->context->cart->id,
            $quotation_request->id,
            $id_product,
            $id_product_attribute
        );

        if ($quotation_request->updateQty($quantity, $id_product, $id_product_attribute, $id_customization, $mode)) {
            return true;
        } else {
            throw new Exception($this->module->l(
                'Unable to add update quantity for this product in request.',
                'QuotationsProFront'
            ));
        }
    }

    private function deleteProduct($id_roja45_quotation_requestproduct)
    {
        if ($quotation_request = QuotationRequest::getInstance()) {
            if ($quotation_request->deleteRequestProduct($id_roja45_quotation_requestproduct)) {
                return true;
            } else {
                throw new Exception($this->module->l(
                    'Unable to delete requested product from quotation',
                    'QuotationsProFront'
                ));
            }
        }
    }

    /**
     * @param string $email
     * @param string $firstname
     * @param string $lastname
     * @param QuotationRequest $request
     * @param $products
     * @param $files
     * @return bool
     */
    private function createQuotation($email, $firstname, $lastname, $request, $files)
    {
        if (!$id_country = Tools::getValue('country')) {
            $id_country = (int) $this->context->country->id;
        }

        /** @var RojaQuotation $quotation */
        $quotation = new RojaQuotation();
        $quotation->id_lang = (int) $this->context->language->id;
        $quotation->id_shop = (int) $this->context->shop->id;
        $quotation->id_currency = (int) $this->context->currency->id;
        $quotation->id_country = $id_country;
        $quotation->email = trim($email);
        $quotation->firstname = $firstname;
        $quotation->lastname = $lastname;
        $quotation->id_request = $request->id;
        $quotation->reference = $request->reference;
        //$quotation->reference = RojaQuotation::generateReference();
        //$quotation->filename = $filename;
        $quotation->date_add = date('Y-m-d H:i:s');
        $quotation->date_upd = date('Y-m-d H:i:s');
        $quotation->purchase_date = null;
        $quotation->id_profile = Configuration::get(
            'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER'
        );

        //if (Configuration::get('ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS')) {
        //    $quotation->id_employee = Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER');
        //}
        $quotation->form_data = json_encode($request->getFormData());
        $valid_for = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
        $date = new DateTime($quotation->date_add);
        $date->add(new DateInterval('P' . $valid_for . 'D'));
        $quotation->expiry_date = $date->format('Y-m-d H:i:s');
        $customer = new Customer();
        $customer = $customer->getByEmail($email);

        if (Validate::isLoadedObject($customer)) {
            $id_customer = $quotation->id_customer = $customer->id;
            $addresses = $customer->getAddresses($this->context->language->id);

            if (count($addresses) > 0) {
                $default_address_id = $addresses[0]['id_address'];
                
                if (!$quotation->id_address_delivery) {
                    $quotation->id_address_delivery = !empty($request->address_delivery_id) ? $request->address_delivery_id : $default_address_id;
                }
                
                if (!$quotation->id_address_invoice) {
                    $quotation->id_address_invoice = !empty($request->address_invoice_id) ? $request->address_invoice_id : $default_address_id;
                }

                $quotation->id_address_tax = Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery' ? RojaQuotation::TAX_DELIVERY_ADDRESS : RojaQuotation::TAX_INVOICE_ADDRESS;
            }
            
            $request->secure_key = $customer->secure_key;
            $request->id_customer = $id_customer;
            $request->save();
        }

        if (isset($id_customer) && $id_customer) {
            $price_display = Group::getPriceDisplayMethod($id_customer);
            $id_group = (int) Customer::getDefaultGroupId($id_customer);
            $quotation->id_customer = $id_customer;
        } else {
            $price_display = Group::getDefaultPriceDisplayMethod();
            $id_group = (int) $this->context->customer->id_default_group;
        }

        if (!$price_display) {
            $quotation->calculate_taxes = 1;
        } else {
            $quotation->calculate_taxes = 0;
        }

        if (!Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY')) {
            return true;
        }
        RojaQuotationLogger::error('QuotationsProFrontModuleFrontController: processSubmitRequest: save quotation', []);
        if (!$quotation->save()) {
            throw new Exception('Unable to save quotation.');
        }

        foreach ($files as $file) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $quotation->addDocument(
                $file['name'],
                $file['filename'],
                $file['filename'],
                $extension
            );
        }

        $result = true;
        $products = $request->getProducts();

        foreach ($products as &$product) {
            $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                (int) $product['id_product'],
                Context::getContext()
            );
            $product_tax_calculator = TaxManagerFactory::getManager(
                $quotation->getTaxAddress(),
                $id_tax_rules_group
            )->getTaxCalculator();
            $tax_rate = $product_tax_calculator->getTotalRate();

            $quotation->addProduct(
                $product['id_product'],
                $product['id_product_attribute'],
                $product['id_customization'],
                false,
                $product['quote_quantity'],
                null,
                $id_group,
                $product['customizations'],
                $id_tax_rules_group,
                $tax_rate
            );
        }

        if ($id_carrier = (int) $request->hasShipping()) {
            $quotation->addCarrierCharge($id_carrier, $this->context->cart);
        }

        if (!$result) {
            $quotation->delete();
            throw new Exception('Unable to delete quotation.');
        }

        $customer_copy = (Tools::getValue('ROJA45QUOTATIONSPRO_CUSTOMER_COPY') == 'on');
        $template_vars = array(
            'show_summary' => (int) $customer_copy,
        );

        $hide_prices = (bool) Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE');
        $quotation_details = $quotation->getSummaryDetails(null, null, true, $hide_prices);
        $template_vars = array_merge(
            $template_vars,
            $quotation_details
        );

        $file_attachments = null;
        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            if ($customer_copy) {
                // TODO - This should come from status config
                $file_attachments['request']['content'] = RojaPDF::generatePDF(
                    'RequestPdf',
                    $quotation,
                    false
                );
                $file_attachments['request']['name'] = $quotation->reference . '.pdf';
                $file_attachments['request']['mime'] = 'application/pdf';
                $template_vars = array(
                    'show_summary' => 1,
                );
            }

            if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
                $tpl = Context::getContext()->smarty->createTemplate(
                    $this->getTemplatePath('emailQuotationRequestSummary.tpl')
                );
            } else {
                $tpl = Context::getContext()->smarty->createTemplate(
                    'module:' . $this->module->name . '/views/templates/front/PS17_emailQuotationRequestSummary.tpl'
                );
            }

            $tpl->assign($request->getSummaryDetails());
            $tpl->assign(
                array(
                    'link' => $this->context->link,
                    'show_summary' => (int) $customer_copy,
                )
            );
            $summary = $tpl->fetch();
            $summary_txt = \Soundasleep\Html2Text::convert(
                $summary,
                [
                    'ignore_errors' => true
                ]
            );
            $template_vars = array(
                '{id_product}' => Tools::getValue('ID_PRODUCT'),
                '{customer_firstname}' => $firstname,
                '{customer_lastname}' => $lastname,
                '{content}' => ($customer_copy) ? $summary : '',
                '{content_html}' => ($customer_copy) ? $summary : '',
                '{content_text}' => ($customer_copy) ? $summary_txt : '',
                '{content_txt}' => ($customer_copy) ? $summary_txt : '',
            );

            if (Configuration::get('ROJA45_QUOTATIONSPRO_EMAILREQUEST')) {
                $params = array(
                    '{customer_firstname}' => $firstname,
                    '{customer_lastname}' => $lastname,
                    '{content}' => ($customer_copy) ? $summary : '',
                    '{content_html}' => ($customer_copy) ? $summary : '',
                    '{content_text}' => ($customer_copy) ? $summary_txt : '',
                    '{content_txt}' => ($customer_copy) ? $summary_txt : '',
                );

                $contact = new Contact(
                    Configuration::get('ROJA45_QUOTATIONSPRO_CS_ACCOUNT'),
                    $this->context->language->id
                );
                if (Validate::isLoadedObject($contact)) {
                    $contact_name = $contact->name;
                    $contact_email = $contact->email;
                } else {
                    $contact_name = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_NAME');
                    $contact_email = Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL');
                }

                $bcc = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_BCC');
                if (Tools::strlen($bcc) == 0) {
                    $bcc = null;
                }

                $sent = Mail::Send(
                    (int) $this->context->language->id,
                    'roja45quotationrequestadmin',
                    Mail::l('Quotation Request Received', (int) $this->context->language->id),
                    $params,
                    $contact_email,
                    $contact_name,
                    $contact_email,
                    //$contact_name,
                    null,
                    $file_attachments,
                    null,
                    _PS_MODULE_DIR_ . 'roja45quotationspro/mails/',
                    false,
                    null,
                    $bcc,
                    Tools::getValue('ROJA45QUOTATIONSPRO_EMAIL')
                );
                if (!$sent) {
                    PrestaShopLogger::addLog(
                        'Roja45: Quotations Pro::submitRequest - Unable to send admin email',
                        1,
                        null
                    );
                }
            }
        }
        $sent = $quotation->setStatus(
            QuotationStatus::$RCVD,
            $template_vars,
            $file_attachments
        );

        if (!$sent) {
            PrestaShopLogger::addLog(
                'Roja45: Quotations Pro::submitRequest - There was a problem updating the quotation status.',
                1,
                null
            );
            throw new Exception('There was a problem updating the quotation status.');
        }

        // TODO - Probably want to do this only when the admin wants to open a quotation, button in quotation? but
        // automatic when the quotation is changed.
        $sent = $quotation->setStatus(
            QuotationStatus::$OPEN,
            $template_vars
        );
        if (!$sent) {
            PrestaShopLogger::addLog(
                'Roja45: Quotations Pro::submitRequest - There was a problem updating the quotation status.',
                1,
                null
            );
            throw new Exception('There was a problem updating the quotation status.');
        }

        return $quotation;
    }

    public function processEnable()
    {
        RojaFortyFiveQuotationsProCore::enableModule();
        die(0);
    }

    public function processDisable()
    {
        RojaFortyFiveQuotationsProCore::disableModule();
        die(0);
    }

    public function processResetauth()
    {
        RojaFortyFiveQuotationsProCore::resetModule();
        die(0);
    }

    public function processStatus()
    {
        $response = new stdClass();
        $response->module_name = $this->module->name;
        $response->module_version = $this->module->version;
        $response->module_source = $this->module->source;
        $response->account_email = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS');
        $response->account_order = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTORDER');
        $response->account_domain = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN');
        $response->host = $_SERVER['HTTP_HOST'];
        $response->status = (int) Configuration::getGlobalValue('RJ45DISMOD');
        echo Tools::jsonEncode($response);
        die(0);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        if (Tools::getValue('action') == 'getQuotationDetails') {
            $breadcrumb['links'][] = array(
                'title' => $this->l('Quotation Details'),
                'url' => '',
            );
        }

        return $breadcrumb;
    }
}
