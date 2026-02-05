<?php
/**
 * AdminQuotationsProController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  AdminQuotationsProController
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * AdminQuotationsProController.
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

class AdminQuotationTemplatesController extends ModuleAdminController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->override_folder = 'quotation_templates/';
        $this->tpl_folder = 'quotation_templates/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_template';
        $this->identifier = 'id_roja45_quotation_template';
        $this->submit_action = 'submitAdd'.$this->table;
        $this->show_cancel_button = true;
        $this->className = 'RojaQuotationTemplate';
        $this->action = 'RojaQuotationTemplate';
        $this->deleted = false;
        $this->colorOnBackground = false;
        //$this->multishop_context = Shop::CONTEXT_ALL;
        $this->_defaultOrderBy = $this->identifier = 'id_roja45_quotation_template';
        $this->list_id = 'id_roja45_quotation_template';
        $this->deleted = false;
        $this->_orderBy = 'date_upd';
        $this->_orderWay = 'DESC';
        $this->shopLinkType = 'shop';

        $this->addRowAction('edit');
        $this->addRowAction('createQuote');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->fields_list = array(
            'template_name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
            ),
        );

        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('AdminQuotationTemplates')
        );
    }


    /**
     * ajaxProcessCreateQuote - Create an account for this customer
     *
     * @return json
     *
     */
    public function processSubmitCreateQuote()
    {
        $validationErrors = array();
        $this->display = 'edit';
        try {
            ob_start();
            $template = new RojaQuotationTemplate((int)Tools::getValue('id_roja45_quotation_template'));
            if (!Validate::isLoadedObject($template)) {
                throw new Exception($this->l('The template could not be loaded.'));
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation();
                $quotation->id_lang = (int) $template->id_lang;
                $quotation->id_shop = (int) $this->context->shop->id;
                $quotation->id_currency = (int) $template->id_currency;
                $quotation->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
                $quotation->id_employee = $this->context->employee->id;
                $quotation->id_customer = 0;
                $quotation->reference = RojaQuotation::generateReference();
                $quotation->valid_days = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
                $date = new DateTime($quotation->date_add);
                $date->add(new DateInterval('P'.$quotation->valid_days.'D'));
                $quotation->expiry_date = $date->format('Y-m-d H:i:s');
                $quotation->form_data = '';
                //$quotation->email = $this->l('Pending..');
                //$quotation->firstname = $this->l('Pending..');
                //$quotation->lastname = $this->l('Pending..');
                $quotation->calculate_taxes = (int) $template->calculate_taxes;
                if (!$quotation->add()) {
                    throw new Exception($this->l('Unable to create the quote.'));
                }

                $position = 1;
                foreach ($template->getProducts() as $template_product) {
                    $template_product = new RojaQuotationTemplateProduct($template_product['id_roja45_quotation_template_product']);
                    $quotation_product = new QuotationProduct();
                    $quotation_product->id_roja45_quotation = $quotation->id;
                    $quotation_product->id_shop = (int) $this->context->shop->id;
                    $quotation_product->id_product = $template_product->id_product;
                    $quotation_product->id_product_attribute = $template_product->id_product_attribute;
                    $quotation_product->product_title = $template_product->product_title;
                    $quotation_product->qty = $template_product->qty;
                    $quotation_product->comment = $template_product->comment;
                    $quotation_product->unit_price_tax_excl = $template_product->unit_price_tax_excl;
                    $quotation_product->unit_price_tax_incl = $template_product->unit_price_tax_incl;
                    $quotation_product->deposit_amount = $template_product->deposit_amount;
                    $quotation_product->custom_price = $template_product->custom_price;
                    $quotation_product->position = $position;
                    $position++;
                    if (!$quotation_product->add()) {
                        $quotation->delete();
                        throw new Exception($this->l('Unable to add a product to the quote.'));
                    }
                }

                foreach ($template->getCharges() as $charge) {
                    $template_charge = new RojaQuotationTemplateCharge($charge['id_roja45_quotation_template_charge']);
                    $quotation_charge = new QuotationCharge();
                    $quotation_charge->id_roja45_quotation = $quotation->id;
                    $quotation_charge->charge_name = $template_charge->charge_name;
                    $quotation_charge->charge_type = $template_charge->charge_type;
                    $quotation_charge->charge_method = $template_charge->charge_method;
                    $quotation_charge->charge_value = $template_charge->charge_value;
                    $quotation_charge->charge_default = 1;
                    $quotation_charge->charge_amount = $template_charge->charge_amount;
                    $quotation_charge->charge_amount_wt = $template_charge->charge_amount_wt;
                    $quotation_charge->specific_product = $template_charge->specific_product;
                    $quotation_charge->id_roja45_quotation_product = $template_charge->id_roja45_quotation_product;
                    $quotation_charge->id_cart_rule = $template_charge->id_cart_rule;
                    if (!$quotation_charge->add()) {
                        $quotation->delete();
                        throw new Exception($this->l('Unable to add a charge to the quote.'));
                    }
                }

                foreach ($template->getDiscounts() as $discount) {
                    $template_discount = new RojaQuotationTemplateCharge($discount['id_roja45_quotation_template_charge']);
                    $quotation_discount = new QuotationCharge();
                    $quotation_discount->id_roja45_quotation = $quotation->id;
                    $quotation_discount->charge_name = $template_discount->charge_name;
                    $quotation_discount->charge_type = $template_discount->charge_type;
                    $quotation_discount->charge_default = 1;
                    $quotation_discount->charge_method = $template_discount->charge_method;
                    $quotation_discount->charge_value = $template_discount->charge_value;
                    $quotation_discount->charge_amount = $template_discount->charge_amount;
                    $quotation_discount->charge_amount_wt = $template_discount->charge_amount_wt;
                    $quotation_discount->specific_product = $template_discount->specific_product;
                    $quotation_discount->id_roja45_quotation_product = $template_discount->id_roja45_quotation_product;
                    $quotation_discount->id_cart_rule = $template_discount->id_cart_rule;
                    if (!$quotation_discount->add()) {
                        $quotation->delete();
                        throw new Exception($this->l('Unable to add a discount to the quote.'));
                    }
                }
                $quotation->setStatus(QuotationStatus::$NWQT);

                ob_end_clean();

                $link = 'index.php?controller=AdminQuotationsPro&viewroja45_quotationspro&id_roja45_quotation=' .
                    $quotation->id .
                    '&token=' . Tools::getAdminTokenLite('AdminQuotationsPro');
                Tools::redirectAdmin($link);
            } else {
                throw new Exception($this->l('Validation errors'));
            }
        } catch (Exception $e) {
            if (isset($quotation)) {
                $quotation->delete();
            }

            $validationErrors[] = $e->getMessage();
            error_log('Caught exception: ' . $e->getMessage());
            $this->errors = $validationErrors;
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        if ($this->action!=null || ($this->display!=null && $this->tabAccess[$this->display])) {
            $this->addJqueryPlugin('autocomplete');
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotation_template_admin.js'
            );
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/admin/tinymce.inc.js');
            $this->context->controller->addCss(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.css'
            );
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.js'
            );
            $this->context->controller->addJqueryUI('ui.dialog');
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name .  '/views/css/roja45quotationsproadmin.css'
            );
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->identifier . 'Box') as $id_roja45_quotation) {
                $quotation = new RojaQuotationTemplate($id_roja45_quotation);
                $quotation->delete();
            }
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink(
                'AdminQuotationTemplates',
                true
            ));
        } else {
            return parent::postProcess();
        }
    }

    public function renderForm()
    {
        if (!($quotation_template = $this->loadObject(true))) {
            return;
        }
        return $this->buildForm($quotation_template);
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ?
            array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        /** @var RojaQuotation $quotation */
        $quotation = $this->loadObject(true);
        switch ($this->display) {
            case 'view':
                $this->toolbar_title[] = $this->l('View Template: ') . $quotation->template_name;
                break;
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function displayCreateQuoteLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_create_quote.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getAdminLink(
                'AdminQuotationTemplates',
                true
            ) .'&'.$this->identifier.'='.$id.'&action=submitCreateQuote',
            'action' => ' Create Quote',
            'id_roja45_quotation_template' => $id,
            'token' => $token
        ));
        return $tpl->fetch();
    }

    public function getCarriers()
    {
        $_carriers = array();
        if ($this->id_country) {
            $country = new Country($this->id_country);
        } else {
            $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        }

        $products = $this->getProducts();

        if ((int)$this->id_customer) {
            $customer = new Customer((int)$this->id_customer);
            $result = Carrier::getCarriers(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                true,
                false,
                (int)$country->id_zone,
                $customer->getGroups(),
                Carrier::ALL_CARRIERS
            );
            unset($customer);
        } else {
            $result = Carrier::getCarriers(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                true,
                false,
                (int) $country->id_zone,
                null,
                Carrier::ALL_CARRIERS
            );
        }

        $total = $this->getQuotationTotal(true, Cart::ONLY_PRODUCTS);
        foreach ($result as $k => $row) {
            /** @var Carrier $carrier */
            $_carriers[$row['id_carrier']]['carrier'] = new Carrier((int)$row['id_carrier']);
            $carrier = $_carriers[$row['id_carrier']]['carrier'];
            $shipping_method = $carrier->getShippingMethod();
            // Get only carriers that are compliant with shipping method
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT &&
                    $carrier->getMaxDeliveryPriceByWeight((int)$country->id_zone) === false
                ) || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE &&
                    $carrier->getMaxDeliveryPriceByPrice((int)$country->id_zone) === false
                )
            ) {
                unset($result[$k]);
                continue;
            }

            // If out-of-range behavior carrier is set on "Desactivate carrier"
            if ($row['range_behavior']) {
                $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                    $row['id_carrier'],
                    $this->getTotalWeight(),
                    (int)$country->id_zone
                );
                $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                    $row['id_carrier'],
                    $total,
                    (int)$country->id_zone,
                    (int)$this->id_currency
                );
                // Get only carriers that have a range compatible with cart
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)
                ) {
                    unset($result[$k]);
                    continue;
                }
            }

            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping = $carrier->getDeliveryPriceByWeight(
                    $this->getTotalWeight($products),
                    (int)$country->id_zone
                );
            } else {
                $shipping = $carrier->getDeliveryPriceByPrice($total, (int)$country->id_zone, (int)$this->id_currency);
            }

            $_carriers[$row['id_carrier']]['shipping'] = $shipping;
        }
        $return = array();
        $return['carriers'] = $_carriers;

        return $return;
    }

    /**
     * @param $quotation RojaQuotationTemplate
     * @return string
     */
    private function buildForm($quotation_template)
    {
        $products = $quotation_template->getProducts();
        $discounts = $quotation_template->getDiscounts();
        $charges = $quotation_template->getCharges();

        $currencies = Currency::getCurrencies(false, true);
        $employee = new Employee($quotation_template->id_employee);
        $lang = new Language($quotation_template->id_lang);
        $currency = new Currency((int)$quotation_template->id_currency);
        $shop = new Shop($quotation_template->id_shop);
        $categories = array_slice(Category::getCategories((int)$this->context->language->id, true, false), 1);

        $carrierData = $quotation_template->getCarriers();
        $documents = QuotationDocument::getDocuments(
            $quotation_template->id_lang,
            $quotation_template->id_shop
        );

        $view = '';
        $tpl = $this->context->smarty->createTemplate($this->getTemplatePath('_adminHeader.tpl') . '_adminHeader.tpl');
        $view .= $tpl->fetch();

        $tpl = $this->context->smarty->createTemplate(
            $this->getTemplatePath('quotationview_template.tpl') . 'quotationview_template.tpl'
        );

        //$quotation_documents = $quotation_template->getDocuments();
        $quotation_documents = array();

        $tpl->assign(
            array(
                'quotationspro_link' => $this->context->link->getAdminLink(
                    'AdminQuotationTemplates'
                ) . '&update' . $this->table,
                'languages' => $this->context->controller->getLanguages(),
                'link' => $this->context->link,
                'id_roja45_quotation_template' => $quotation_template->id_roja45_quotation_template,
                'payment_methods' => PaymentModule::getInstalledPaymentModules(),
                'order_states' => OrderState::getOrderStates(Context::getContext()->language->id),
                'quotation_template' => $quotation_template,
                'template_name' => $quotation_template->template_name,
                'employee' => $employee,
                'currency' => $currency,
                'discounts' => $discounts,
                'quotation_products' => $products,
                'documents' => $documents,
                'quotation_documents' => $quotation_documents,
                'carriers' => $carrierData['carriers'],
                'charges' => $charges,
                'currencies' => $currencies,
                'lang' => $lang,
                'current_id_lang' => $this->context->language->id,
                'id_shop' => $shop->id,
                'shop_name' => $shop->name,
                'categories' => $categories,
                'id_currency' => $currency->id,
                'id_lang' => $quotation_template->id_lang,
                'currency_sign' => $currency->sign,
                'currency_format' => $currency->format,
                'currency_blank' => $currency->blank,
                'has_voucher' => (count($discounts) > 0) ? 1 : 0,
                'has_charges' => (count($charges) > 0) ? 1 : 0,
                'show_taxes' => $quotation_template->calculate_taxes,
                'use_taxes' => $quotation_template->calculate_taxes,
                'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
                'roja45_quotations_dateformat' => RojaFortyFiveQuotationsProCore::convertDateFormat(
                    $this->context->language->date_format_lite
                ),
                'allow_edit' => 0
            )
        );

        $view .= $tpl->fetch();
        return $view;
    }
}
