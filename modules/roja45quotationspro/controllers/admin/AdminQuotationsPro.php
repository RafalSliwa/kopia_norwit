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

class AdminQuotationsProController extends ModuleAdminController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->override_folder = 'roja45quotationspro/';
        $this->tpl_folder = 'roja45quotationspro/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro';
        $this->identifier = 'id_roja45_quotation';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'RojaQuotation';
        $this->action = 'RojaQuotation';
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->allow_export = true;
        $this->bulk_actions = array(
            'updateStatus' => array(
                'text' => $this->l('Update Status', 'AdminQuotationsPro'),
                'confirm' => $this->l('Update selected items?', 'AdminQuotationsPro'),
            ),
            'delete' => array(
                'text' => $this->l('Delete selected', 'AdminQuotationsPro'),
                'confirm' => $this->l('Delete selected items?', 'AdminQuotationsPro'),
            ),
            'delete_permanently' => array(
                'text' => $this->l('Delete permanently', 'AdminQuotationsPro'),
                'confirm' => $this->l('Delete selected items permanently?', 'AdminQuotationsPro'),
            ),
        );
        //$this->multishop_context = Shop::CONTEXT_ALL;

        $status = QuotationStatus::getQuotationStates($this->context->language->id);
        $states_array = array();
        foreach ($status as $row) {
            $states_array[$row['id_roja45_quotation_status']] = $row['status'];
        }

        $this->_defaultOrderBy = $this->identifier = 'id_roja45_quotation';
        $this->list_id = 'id_roja45_quotation';
        $this->deleted = false;

        $order_by = Configuration::get(
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER'
        );
        $this->_orderBy = 'date_add';
        if ($order_by == 1) {
            $this->_orderBy = 'date_add';
        } elseif ($order_by == 2) {
            $this->_orderBy = 'expiry_date';
        }

        $order_dir = Configuration::get(
            'ROJA45_QUOTATIONSPRO_QUOTATION_LIST_ORDER_DIR'
        );
        $this->_orderWay = 'DESC';
        if ($order_dir == 'ASC') {
            $this->_orderWay = 'ASC';
        } elseif ($order_dir == 'DESC') {
            $this->_orderWay = 'DESC';
        }

        $this->_select .= 'employee.`email` as owner, profile_lang.name as owner_group, address.company as company';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'employee` employee ON (employee.id_employee = a.id_employee)
            LEFT JOIN `' . _DB_PREFIX_ . 'profile_lang` profile_lang ON (profile_lang.id_profile = a.id_profile) AND profile_lang.id_lang=' . (int) $this->context->language->id . '
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` customer ON a.id_customer=customer.id_customer
            LEFT JOIN `' . _DB_PREFIX_ . 'address` address ON a.id_address_invoice=address.id_address';
        $this->_where = 'AND a.is_template=0';

        $enable_assign = (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS'
        );
        $assign_new = (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS'
        );
        $employee_assign = (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN'
        );
        $default_profile = (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_DEFAULT_OWNER'
        );

        if ($enable_assign) {
            if (($this->context->employee->id_profile == $default_profile) ||
                $this->context->employee->id_profile == _PS_ADMIN_PROFILE_
            ) {
                $this->addRowAction('assign');
            } else {
                if ($assign_new) {
                    $this->_where .= ' AND a.id_employee=' . $this->context->employee->id;
                } else {
                    $this->_where .= ' AND ((a.id_roja45_quotation_status IN (1,2,13) AND (a.id_employee=NULL OR a.id_employee=0)) OR (a.id_employee=' . $this->context->employee->id . '))';
                }

                if ($employee_assign) {
                    $this->addRowAction('assign');
                }
            }
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('deletePermanently');

        $this->shopLinkType = 'shop';

        $fields_list = array(
            /*'id_roja45_quotation' => array(
            'title' => $this->l('#'),
            'class' => 'fixed-width-xs',
            ),*/
            'display_code' => array(
                'title' => $this->l('Status', 'AdminQuotationsPro'),
                'class' => 'fixed-width-sm',
                'color' => 'color',
                'type' => 'select',
                'list' => $states_array,
                'filter_key' => 'a!id_roja45_quotation_status',
                'filter_type' => 'int',
                'order_key' => 'id_roja45_quotation_status',
            ),
            'reference' => array(
                'title' => $this->l('Reference #', 'AdminQuotationsPro'),
                'width' => 'auto',
                'class' => 'center fixed-width-md',
            ),
           /* 'quote_name' => array(
                'title' => $this->l('Quote Name', 'AdminQuotationsPro'),
                'width' => 'auto',
                'filter_key' => 'a!quote_name',
                'filter_type' => 'string',
            ),*/
            'firstname' => array(
                'title' => $this->l('Name', 'AdminQuotationsPro'),
                'width' => 'auto',
                'filter_key' => 'a!firstname',
                'class' => 'center fixed-width-lg',
            ),
            'lastname' => array(
                'title' => $this->l('Surname', 'AdminQuotationsPro'),
                'width' => 'auto',
                'filter_key' => 'a!lastname',
                'class' => 'center fixed-width-lg',
            ),
            'email' => array(
                'title' => $this->l('Email', 'AdminQuotationsPro'),
                'width' => 'auto',
                'filter_key' => 'a!email',
                'filter_type' => 'string',
                'class' => 'center fixed-width-lg',
            ),
            'company' => array(
                'title' => $this->l('Company', 'AdminQuotationsPro'),
                'width' => 'auto',
                'filter_key' => 'address!company',
                'filter_type' => 'string',
                'class' => 'center fixed-width-lg',
            ),
            'total' => array(
                'title' => $this->l('Total', 'AdminQuotationsPro'),
                'width' => 'auto',
                'type' => 'price',
                'havingFilter' => false,
                'orderby' => false,
                'search' => false,
            ),
            'date_add' => array(
                'title' => $this->l('Received', 'AdminQuotationsPro'),
                'width' => 'auto',
                'orderby' => true,
                'havingFilter' => true,
                'type' => 'datetime',
            ),
            'date_upd' => array(
                'title' => $this->l('Updated', 'AdminQuotationsPro'),
                'width' => 'auto',
                'orderby' => true,
                'havingFilter' => true,
                'type' => 'datetime',
            ),
            'expiry_date' => array(
                'title' => $this->l('Expires', 'AdminQuotationsPro'),
                'width' => 'auto',
                'orderby' => true,
                'havingFilter' => true,
                'type' => 'datetime',
                'class' => 'expiry_date',
            ),
        );

        if ($highlight_expiring = (int) Configuration::get(
            'ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES'
        )) {
            $fields_list = array_merge(
                $fields_list,
                array(
                    'expiry_date_hidden' => array(
                        'title' => $this->l('None', 'AdminQuotationsPro'),
                        'width' => 'auto',
                        'orderby' => false,
                        'havingFilter' => false,
                        'class' => 'expiry_date_hidden',
                    ),
                )
            );
        }

        $employee_array = [];
        if ($employees = Employee::getEmployees()) {
            foreach ($employees as $employee) {
                $obj = new Employee($employee['id_employee']);
                $employee_array[$obj->email] = $obj->email;
            }
        }

        $employee_groups = [];
        if ($profiles = Profile::getProfiles($this->context->language->id)) {
            foreach ($profiles as $profile) {
                $obj = new Profile($profile['id_profile'], $this->context->language->id);
                $employee_groups[$obj->name] = $obj->name;
            }
        }

        $fields_list = array_merge(
            $fields_list,
            array(
                'owner' => array(
                    'title' => $this->l('Owner', 'AdminQuotationsPro'),
                    'width' => 'auto',
                    'type' => 'select',
                    'list' => $employee_array,
                    'filter_key' => 'employee!email',
                    'filter_type' => 'string',
                ),
                'owner_group' => array(
                    'title' => $this->l('Owner Group', 'AdminQuotationsPro'),
                    'width' => 'auto',
                    'type' => 'select',
                    'list' => $employee_groups,
                    'filter_key' => 'profile_lang!name',
                    'filter_type' => 'string',
                ),
                'quotesent_text' => array(
                    'title' => $this->l('Sent', 'AdminQuotationsPro'),
                    'width' => 'auto',
                    'color' => 'color_sent',
                    'tmpTableFilter' => true,
                    'orderby' => false,
                    'class' => 'fixed-width-xs',
                    'havingFilter' => false,
                    'search' => false,
                ),
            )
        );
        $this->fields_list = $fields_list;

        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('AdminQuotationsPro')
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->context->controller->addJS(
            _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationsadmin_popup.js'
        );
        $this->context->controller->addCSS(
            _PS_MODULE_DIR_ . $this->module->name . '/views/css/roja45quotationsproadmin.css'
        );

        if ($this->action != null || ($this->display != null && $this->tabAccess[$this->display])) {
            $this->addJqueryPlugin('autocomplete');
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationsadmin.js'
            );
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/admin/tinymce.inc.js');
            /*$this->context->controller->addCss(
            _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.css'
            );*/
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.js'
            );
            $this->context->controller->addJqueryUI('ui.dialog');

            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/timepicker/jquery.ui-timepicker-addon.js'
            );
            if (Context::getContext()->language->iso_code != 'en') {
                $this->context->controller->addJS(
                    _PS_MODULE_DIR_ .
                    $this->module->name .
                    '/libraries/timepicker/localization/jquery-ui-timepicker-' .
                    Context::getContext()->language->iso_code . '.js'
                );
            }

            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/timepicker/jquery.ui-timepicker-addon.css',
                'all'
            );

            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-sortable/jquery-sortable.js'
            );

            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/select2/js/select2.js'
            );
            if (Context::getContext()->language->iso_code != 'en') {
                $this->context->controller->addJS(
                    _PS_MODULE_DIR_ . $this->module->name . '/libraries/select2/js/i18n/' . Context::getContext()->language->iso_code . '.js'
                );
            }
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/select2/css/select2.min.css',
                'all'
            );

            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationsadminlist.js'
            );
        } else {
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationsadminlist.js'
            );
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitedit' . $this->table)) {;
            $quotation = $this->processSubmitEditQuotation($this->loadObject(true));
            $link = 'index.php?controller=AdminQuotationsPro&viewroja45_quotationspro&id_roja45_quotation=' .
                $quotation->id .
                '&token=' .
                $this->token;
            Tools::redirectAdmin($link);
        } elseif (Tools::isSubmit('add' . $this->table)) {
            /*$quotation = $this->loadObject(true);
        $quotation->save();
        $link = 'index.php?controller=AdminQuotationsPro&viewroja45_quotationspro&id_roja45_quotation=' .
        $quotation->id .
        '&token=' .
        $this->token;
        Tools::redirectAdmin($link);*/
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $this->markDeleted(Tools::getValue('id_roja45_quotation'));
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink(
                'AdminQuotationsPro',
                true
            ));
        } elseif (Tools::isSubmit('deletePermanently' . $this->table)) {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (Validate::isLoadedObject($quotation)) {
                $quotation->delete();
            }
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->identifier . 'Box') as $selection) {
                $this->markDeleted((int) $selection);
            }
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink(
                'AdminQuotationsPro',
                true
            ));
        } elseif (Tools::isSubmit('submitBulkdelete_permanently' . $this->table)) {
            foreach (Tools::getValue($this->identifier . 'Box') as $selection) {
                $quotation = new RojaQuotation((int) $selection);
                if (Validate::isLoadedObject($quotation)) {
                    $quotation->delete();
                }
            }
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink(
                'AdminQuotationsPro',
                true
            ));
        } elseif (Tools::isSubmit('raiseOrder')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders') . '&addorder');
        } elseif (Tools::isSubmit('submitBulkupdateStatus' . $this->table)) {
            $this->processBulkUpdateStatus();
        }
        return parent::postProcess();
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

        foreach ($this->_list as &$list_item) {
            $quotation = new RojaQuotation($list_item['id_roja45_quotation']);
            $currency = new Currency($quotation->id_currency);
            $list_item['currency'] = $currency->iso_code;

            $display_tax = 0;
            if (!empty($quotation->id_customer)) {
                $customer = new Customer($quotation->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $priceDisplay = Product::getTaxCalculationMethod($quotation->id_customer);
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $display_tax = 1;
                    }
                }
            }
            //$list_item['email'] = $list_item['email'];
            //$list_item['total'] = Tools::displayPrice($quotation->getQuotationTotal((int)$display_tax));
            $totals = $quotation->getQuotationTotals((int) $display_tax);
            $list_item['total'] = Tools::convertPrice(
                $totals['quotation_total'],
                $currency,
                true
            );
            if ($list_item['id_employee'] > 0) {
                $employee = new Employee($list_item['id_employee']);
                //$list_item['owner'] = $employee->firstname . ' ' . $employee->lastname;
                $list_item['owner'] = $employee->email;
            }

            if ($list_item['id_lang'] > 0) {
                $language = new Language($list_item['id_lang'], $id_lang);
                $list_item['lang'] = $language->name;
            }

            $id_status = $list_item['id_roja45_quotation_status'];
            $status = new QuotationStatus($id_status, $this->context->language->id);

            if ($status->id == (int) Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_DLTD')) {
                $list_item['class'] = 'deleted';
            }

            $list_item['expiry_date_hidden'] = $list_item['expiry_date'];
            $list_item['color'] = $status->color;
            $list_item['code'] = $status->code;
            $list_item['display_code'] = $status->display_code;

            if ($list_item['quote_sent'] > 0) {
                $list_item['quotesent_text'] = 'YES';
                $list_item['color_sent'] = '#32CD32';
            } else {
                $list_item['quotesent_text'] = 'NO';
                $list_item['color_sent'] = '#FF0000';
            }

            if ($list_item['id_cart'] > 0) {
                $list_item['incart_text'] = 'YES';
                $list_item['color_cart'] = '#32CD32';
            } else {
                $list_item['incart_text'] = 'NO';
                $list_item['color_cart'] = '#FF0000';
            }

            if ($list_item['id_order'] > 0) {
                $list_item['hasorder_text'] = 'YES';
                $list_item['color_order'] = '#32CD32';
            } else {
                $list_item['hasorder_text'] = 'NO';
                $list_item['color_order'] = '#FF0000';
            }
        }
    }

    public function renderList()
    {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_BROWSERNOTIFICATION_TIMESTAMP',
            time()
        );
        return parent::renderList();
    }

    public function renderView()
    {
        if (!($quotation = $this->loadObject(true))) {
            return;
        }
        return $this->buildForm($quotation);
    }

    public function renderForm()
    {
        $quotation = $this->loadObject(true);
        if (!Validate::isLoadedObject($quotation)) {
            $id_country = (int) $this->context->country->id;
            $quotation = new RojaQuotation();
            $quotation->id_lang = (int) $this->context->language->id;
            $quotation->id_shop = (int) $this->context->shop->id;
            $quotation->id_currency = (int) $this->context->currency->id;
            $quotation->id_country = $id_country;
            $quotation->id_employee = $this->context->employee->id;
            $quotation->id_customer = 0;
            $quotation->id_address_tax = Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery' ? RojaQuotation::TAX_DELIVERY_ADDRESS : RojaQuotation::TAX_INVOICE_ADDRESS;
            $valid_for = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
            $date = new DateTime($quotation->date_add);
            $date->add(new DateInterval('P' . $valid_for . 'D'));
            $quotation->expiry_date = $date->format('Y-m-d H:i:s');
            $quotation->form_data = '';

            $priceDisplay = Product::getTaxCalculationMethod((int) $quotation->id_customer);
            $quotation->calculate_taxes = 0;
            if (!$priceDisplay || $priceDisplay == 2) {
                $quotation->calculate_taxes = 1;
            }
            if (!$quotation->reference) {
                $quotation->reference = RojaQuotation::generateReference();
            }

            $id_quotation_status = Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_' . QuotationStatus::$NWQT);
            $status = new QuotationStatus($id_quotation_status);
            $quotation->id_roja45_quotation_status = $status->id;
            $quotation->add();
            //$quotation->setStatus(QuotationStatus::$NWQT);

            $url = $this->context->link->getAdminLink(
                    'AdminQuotationsPro',
                    true
                ) . '&viewroja45_quotationspro&id_roja45_quotation=' . $quotation->id;
            Tools::redirectAdmin($url);
        }
        return $this->buildForm($quotation);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_quotation'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('New Quotation', 'AdminQuotationsPro'),
                'icon' => 'process-icon-new',
            );
        }
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ?
            array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        /** @var RojaQuotation $quotation */
        $quotation = $this->loadObject(true);

        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->l('Edit Quotation: ', 'AdminQuotationsPro') . $quotation->reference;
                break;
            case 'add':
                $this->toolbar_title[] = $this->l('Add new quote', 'AdminQuotationsPro');
                break;
            case 'view':
                $this->toolbar_title[] = $this->l('View Quotation: ', 'AdminQuotationsPro') . $quotation->reference;
                break;
            default:
                $this->toolbar_title[] = $this->l('Quotations', 'AdminQuotationsPro');
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }
    }

    public function initTabModuleList()
    {
        parent::initTabModuleList();
    }

    public function addToolBarModulesListButton()
    {
        parent::addToolBarModulesListButton();
    }

    protected function renderNewQuotationForm()
    {
        if (!($this->loadObject(true))) {
            return;
        }

        $form_config = $this->module->getForm();
        $form = $this->buildFormComponents($form_config);
        // TODO only do this in screen where there is an add to cart button.
        //$this->id_country = (int)Tools::getCountry();

        $this->smarty->assign(
            array(
                'id_language' => $this->context->language->id,
                'form' => $form,
                'columns' => $form_config['cols'],
                'enable_captcha' => 0,
                'col_width' => 12 / $form_config['cols'],
                // 'sl_country' => (int)$this->id_country,
                'enabled_products' => 0,
            )
        );
        $html = parent::renderForm();
        $html .= $this->display(__FILE__, 'displayFooter.tpl', 'roja45quotationspro-footer');

        return $html;
    }

    protected function renderQuotationForm()
    {
        if (!($this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'roja45_quotation') {
            $this->initList();
        }

        return parent::filterToField($key, $filter);
    }

    protected function getFieldsValues()
    {
        return array(
            'ROJA45_QUOTATIONSPRO_USE_CS' => Tools::getValue(
                'ROJA45_QUOTATIONSPRO_USE_CS',
                Configuration::get('ROJA45_QUOTATIONSPRO_USE_CS')
            ),
        );
    }

    public function displayAssignLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_assign.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getAdminLink(
                    'AdminQuotationsPro',
                    true
                ) . '&token=' . $token . '&' . $this->identifier . '=' . $id . '&postProcess' . $this->table,
            'action' => ' Assign',
            'id_roja45_quotation' => $id,
            'controller_url' => $this->context->link->getAdminLink(
                'AdminQuotationsPro',
                true
            ),
            'employees' => Employee::getEmployees(true),
        ));
        return $tpl->fetch();
    }

    public function displayDeletePermanentlyLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_deletePermanently.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getAdminLink(
                    'AdminQuotationsPro',
                    true
                ) . '&token=' . $token . '&' . $this->identifier . '=' . $id . '&deletePermanently' . $this->table,
            'action' => ' Delete Permanently',
            'id_roja45_quotation' => $id,
        ));
        return $tpl->fetch();
    }

    public function processDownloadPDFQuotation()
    {
        $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
        $quotation->generateQuotationPDF(true, $quotation->calculate_taxes);
        //exit;
    }

    public function processDownloadFile()
    {
        $validationErrors = array();
        try {
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->l('No quotation id provided.', 'AdminQuotationsPro');
            }
            if (!$id_roja45_quotation_document = Tools::getValue('id_roja45_quotation_document')) {
                $validationErrors[] = $this->l('No document id provided.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                $document = $quotation->getDocument($id_roja45_quotation_document);

                if ($document['id_roja45_document']) {
                    $subdir = '';
                } else {
                    $subdir = DIRECTORY_SEPARATOR . $quotation->reference;
                }
                $file = _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' .
                    $subdir . DIRECTORY_SEPARATOR . $document['internal_name'];
                if (!Validate::isFileName($document['internal_name']) ||
                    !file_exists($file)) {
                    throw new Exception($this->l('This file no longer exists', 'AdminQuotationsPro'));
                }

                $filename = $document['file'];

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
                header('Content-Disposition: attachment; filename="' . $document['display_name'] . '"');
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
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->errors = $validationErrors;
        }
    }

    /**
     * processSubmitEditQuotation - Save and update the quotation
     *
     * @param RojaQuotation $quotation The quotation to be saved
     *
     * @return RojaQuotation The updated quotation.
     *
     */
    public function processSubmitEditQuotation($quotation)
    {
        $id_country = (int) Tools::getValue('tax_country');
        $quotation->id_lang = (int) Tools::getValue('quote_language');
        $quotation->id_shop = (int) $this->context->shop->id;
        $quotation->id_currency = (int) Tools::getValue('quote_currency');
        $quotation->id_country = $id_country;
        $quotation->id_state = (int) Tools::getValue('tax_state');
        $quotation->id_employee = $this->context->employee->id;

        if ($expires = Tools::getValue('expires')) {
            $date = DateTime::createFromFormat(
                $this->context->language->date_format_full,
                trim($expires)
            );
            $quotation->expiry_date = $date->format('Y-m-d H:i:s');
        } else {
            $valid_for = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
            $date = new DateTime($quotation->date_add);
            $date->add(new DateInterval('P' . $valid_for . 'D'));
            $quotation->expiry_date = $date->format('Y-m-d H:i:s');
        }

        if (!$quotation->id_customer) {
            $customer = new Customer();
            if ($customer_email = Tools::getValue('email')) {
                $customer->getByEmail($customer_email);
                if (!Validate::isLoadedObject($customer)) {
                    $shop = new Shop($this->context->shop->id);
                    $password = RojaFortyFiveQuotationsProCore::passwdGen(8);
                    $id_customer = $this->createCustomerAccount(
                        $this->context->shop->id,
                        $shop->id_shop_group,
                        Tools::getValue('firstname'),
                        Tools::getValue('lastname'),
                        $customer_email,
                        $password
                    );
                    $customer = new Customer($id_customer);
                    //$customer->firstname = Tools::getValue('firstname');
                    //$customer->lastname = Tools::getValue('lastname');
                    //$customer->email = $customer_email;

                    $quotation->tmp_password = $password;
                    //$customer->passwd = Tools::encrypt($password);
                    //$customer->save();
                }
                $quotation->id_customer = $customer->id;
                $quotation->firstname = $customer->firstname;
                $quotation->lastname = $customer->lastname;
                $quotation->email = $customer->email;
            }
        }

        $quotation->id_address_invoice = (int) Tools::getValue('customer_main_address');
        $quotation->id_address_delivery = (int) Tools::getValue('customer_delivery_address');
        $quotation->id_address_tax = (int) Tools::getValue('customer_tax_address');
        //$quotation->updateAllPrices();

        $quotation->quote_name = Tools::getValue('quote_name');
        if (!$quotation->reference) {
            $quotation->reference = RojaQuotation::generateReference();
        }

        $quotation->calculate_taxes = Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_TAXES');

        foreach ($quotation->getProducts() as $quotation_product) {
            $quotation_product = new QuotationProduct($quotation_product['id_roja45_quotation_product']);
            
            $price_and_rate = $quotation->getRateAndPriceWithTax(
                $quotation_product->id_product,
                $quotation_product->unit_price_tax_excl,
                $this->context
            );

            $price_inc = Tools::ps_round($price_and_rate['price'], 6);

            $quotation_product->unit_price_tax_incl = Tools::ps_round(
                $price_inc,
                6
            );

            $quotation_product->tax_rate = $price_and_rate['rate'];

            if (!$quotation_product->save()) {
                throw new Exception(sprintf($this->l('Unable to save product update [%s]', 'AdminQuotationsPro'), Db::getInstance()->getMsgError()));
            }
        }

        if (!$quotation->save()) {
            throw new Exception(Db::getInstance()->getMsgError());
        }
        /* if ($quotation->id_roja45_quotation_status != $id_status) {
        $status = new QuotationStatus($id_status);
        $quotation->setStatus($status->code);
        }*/

        return $quotation;
    }

    public function processBulkUpdateStatus()
    {
        $order_state = Tools::getValue('selected_status');

        if ($order_state) {
            $quotation_ids = Tools::getValue($this->identifier . 'Box');
            foreach ($quotation_ids as $id_quotation) {
                $id_quotation_status = Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_' . $order_state);
                $quotation = new RojaQuotation($id_quotation);
                if ($id_quotation_status && ($quotation->id_roja45_quotation_status != $id_quotation_status)) {
                    $hide_prices = (bool) Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE');
                    $quotation_details = $quotation->getSummaryDetails(null, null, true, $hide_prices);
                    $quotation->setStatus($order_state, $quotation_details);
                }
            }
            $link = $this->context->link->getAdminLink(
                'AdminQuotationsPro',
                true
            );
            Tools::redirectAdmin($link);
        }
    }

    /**
     * @param $id_carrier
     * @param RojaQuotation $quotation
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getCarrierCharge($id_carrier, $quotation)
    {
        $currency = Currency::getDefaultCurrency();
        $this->preProcessCart($quotation, $currency->id);
        $products = $quotation->getProducts();
        $quotation->populateCart($products, $currency->id);

        $address = $quotation->getTaxAddress();
        $country = new Country($address->id_country);
        if (!$id_zone = (int) $country->id_zone) {
            $id_zone = $this->context->country->id_zone;
        }
        if ($address->id_state) {
            $state = new State($address->id_state);
            $id_zone = $state->id_zone;
        }
        $carrier = new Carrier($id_carrier);
        if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
            $shipping_cost = $carrier->getDeliveryPriceByWeight(
                $this->context->cart->getTotalWeight($products),
                $id_zone
            );
        } else {
            $order_total = $this->context->cart->getOrderTotal(
                true,
                Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
                $products
            );
            $shipping_cost = $carrier->getDeliveryPriceByPrice(
                $order_total,
                $id_zone,
                $quotation->id_currency
            );
        }

        $shipping_cost = $this->getPackageShippingCostFromModule(
            $this->context->cart,
            $carrier,
            $shipping_cost,
            $products
        );
        $currency = new Currency($quotation->id_currency);

        $handling = 0;
        if ($carrier->shipping_handling) {
            $handling = Configuration::get('PS_SHIPPING_HANDLING');
        }
        $quotation->resetCart($this->context->cart);
        $this->context->cart->delete();
        return array(
            'shipping' => Tools::convertPrice(
                $shipping_cost,
                $currency,
                true
            ),
            'handling' => Tools::convertPrice(
                $handling,
                $currency,
                true
            ),
        );
    }

    public function processSubmitNewCustomerOrder()
    {
        $validationErrors = array();
        $this->display = 'edit';
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $this->errors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }

            $payment_method = Tools::getValue('payment_method');
            if (!Tools::strlen($payment_method) > 0) {
                $this->errors[] = $this->l('You must provide a payment method.', 'AdminQuotationsPro');
            }

            $id_order_state = Tools::getValue('order_state');
            if (!Tools::strlen($id_order_state) > 0) {
                $this->errors[] = $this->l('You must provide an order status.', 'AdminQuotationsPro');
            }

            if (!$quotation->id_carrier) {
                $this->errors[] = $this->l('No carrier asssigned to this quotation. You need to add a shipping charge to the quotation.', 'AdminQuotationsPro');
            }

            if (!$quotation->id_address_delivery && !$quotation->id_address_invoice) {
                $this->errors[] = $this->l('No address provided.', 'AdminQuotationsPro');
            }

            if (!count($this->errors)) {
                $return = $this->preProcessCart($quotation, $quotation->id_currency);
                Context::getContext()->currency = new Currency((int) $quotation->id_currency);
                Context::getContext()->customer = new Customer((int) $quotation->id_customer);

                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_ID_QUOTATION',
                    $quotation->id_roja45_quotation
                );
                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_QUOTEINCART',
                    $quotation->id_roja45_quotation
                );

                $products = $quotation->getProducts();
                if ($return && $quotation->populateCart($products, $quotation->id_currency)) {
                    $quotation->setStatus(QuotationStatus::$CART);

                    $cart_total_paid = 0;
                    if ($id_order_state == Configuration::get('PS_OS_PAYMENT')) {
                        $cart_total_paid = (float) Tools::ps_round(
                            (float) $this->context->cart->getOrderTotal(
                                true,
                                Cart::BOTH,
                                null,
                                $quotation->id_carrier
                            ),
                            6
                        );
                    }

                    if (!Configuration::get('PS_CATALOG_MODE')) {
                        $payment_module = Module::getInstanceByName($payment_method);
                    } else {
                        $payment_module = new BoOrder();
                    }


                    $bad_delivery_address = (bool) !Address::isCountryActiveById(
                        (int) $this->context->cart->id_address_delivery
                    );
                    $bad_invoice_address = (bool) !Address::isCountryActiveById(
                        (int) $this->context->cart->id_address_invoice
                    );
                    if ($bad_delivery_address || $bad_invoice_address) {
                        if ($bad_delivery_address) {
                            $this->errors[] = $this->l('This delivery address country is not active.', 'AdminQuotationsPro');
                        } else {
                            $this->errors[] = $this->l('This invoice address country is not active.', 'AdminQuotationsPro');
                        }
                    } else {
                        if ($quotation->id_employee) {
                            $employee = new Employee($quotation->id_employee);
                        } else {
                            $employee = new Employee((int) Context::getContext()->cookie->id_employee);
                            $quotation->id_employee = $employee->id;
                            $quotation->save();
                        }

                        $subject = $this->module->l(
                                'Manual Order - Quotation'
                            ) . '[' . $quotation->reference . '] :' . ' ' . Tools::substr(
                                $employee->firstname,
                                0,
                                1
                            ) . '. ' . $employee->lastname;

                        $payment_module->validateOrder(
                            (int) $this->context->cart->id,
                            (int) $id_order_state,
                            $cart_total_paid,
                            $payment_module->displayName,
                            $subject,
                            array(),
                            null,
                            false,
                            $this->context->cart->secure_key,
                            $this->context->shop
                        );
                        if ($payment_module->currentOrder) {
                            RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                                'ROJA45QUOTATIONSPRO_ID_QUOTATION'
                            );
                            RojaFortyFiveQuotationsProCore::clearCustomerRequirement(
                                'ROJA45QUOTATIONSPRO_QUOTEINCART'
                            );

                            $link = $this->context->link->getAdminLink(
                                    'AdminQuotationsPro',
                                    true
                                ) . '&viewroja45_quotationspro&id_roja45_quotation=' . $quotation->id;
                            Tools::redirectAdmin($link);
                        }
                    }
                }
                $this->errors[] = $this->l('Unable to create order.', 'AdminQuotationsPro');
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->errors = $validationErrors;
        }
    }

    /**
     * ajaxProcessCreateQuote - Create an account for this customer
     *
     * @return json
     *
     */
    public function processCreateQuote()
    {
        $validationErrors = array();
        try {
            ob_start();
            $template = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($template)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation();
                $quotation->id_lang = (int) $template->id_lang;
                $quotation->id_shop = (int) $this->context->shop->id;
                $quotation->id_currency = (int) $template->id_currency;
                $quotation->id_country = (int) $template->id_country;
                $quotation->id_employee = $this->context->employee->id;
                $quotation->id_customer = 0;
                $quotation->valid_days = (int) $template->valid_days;
                $valid_for = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
                $date = new DateTime($quotation->date_add);
                $date->add(new DateInterval('P' . $valid_for . 'D'));
                $quotation->expiry_date = $date->format('Y-m-d H:i:s');
                $quotation->form_data = '';
                $quotation->calculate_taxes = (int) $template->calculate_taxes;
                if (!$quotation->add()) {
                    throw new Exception($this->l('Unable to save quotation.', 'AdminQuotationsPro'));
                }
                if (!$quotation->reference) {
                    $quotation->reference = RojaQuotation::generateReference();
                }

                foreach ($template->getProducts() as $template_product) {
                    $template_product = new QuotationProduct($template_product['id_roja45_quotation_product']);
                    $template_product->duplicateObject();
                    $template_product->id_roja45_quotation = $quotation->id;
                    $template_product->save();
                }

                foreach ($template->getQuotationAllCharges() as $charge) {
                    $charge = new QuotationCharge($charge['id_roja45_quotation_charge']);
                    $charge->duplicateObject();
                    $charge->id_roja45_quotation = $quotation->id;
                    $charge->save();
                }

                foreach ($template->getQuotationAllDiscounts() as $discount) {
                    $discount = new QuotationCharge($discount['id_roja45_quotation_charge']);
                    $discount->duplicateObject();
                    $discount->id_roja45_quotation = $quotation->id;
                    $discount->save();
                }
                $quotation->setStatus(QuotationStatus::$NWQT);
                ob_end_clean();

                Tools::redirect($this->context->link->getAdminLink(
                        'AdminQuotationsPro',
                        true
                    ) . '&id_roja45_quotation=' . $quotation->id . '&viewroja45_quotationspro');
            } else {
                throw new Exception($this->l('Validation errors', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->display = 'edit';
            $this->errors = $validationErrors;
        }
    }

    /**
     * ajaxProcessSaveAsTemplate - Create an account for this customer
     *
     * @return json
     *
     */
    public function processSaveAsTemplate()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $template = new RojaQuotationTemplate();
                $template->id_lang = $quotation->id_lang;
                $template->id_shop = $quotation->id_shop;
                $template->id_currency = $quotation->id_currency;
                $template->calculate_taxes = $quotation->calculate_taxes;
                $template->template_name = Tools::getValue('template_name');
                if (!$template->add()) {
                    throw new Exception($this->l('Unable to save quotation.', 'AdminQuotationsPro'));
                }

                foreach ($quotation->getProducts() as $quotation_product) {
                    $quotation_product = new QuotationProduct($quotation_product['id_roja45_quotation_product']);

                    $quotation_template_product = new RojaQuotationTemplateProduct();
                    $quotation_template_product->id_roja45_quotation_template = $template->id;
                    $quotation_template_product->id_product = $quotation_product->id_product;
                    $quotation_template_product->id_product_attribute = $quotation_product->id_product_attribute;
                    $quotation_template_product->product_title = $quotation_product->product_title;
                    $quotation_template_product->qty = $quotation_product->qty;
                    $quotation_template_product->comment = $quotation_product->comment;
                    $quotation_template_product->unit_price_tax_excl = $quotation_product->unit_price_tax_excl;
                    $quotation_template_product->unit_price_tax_incl = $quotation_product->unit_price_tax_incl;
                    $quotation_template_product->deposit_amount = $quotation_product->deposit_amount;
                    $quotation_template_product->custom_price = $quotation_product->custom_price;
                    $quotation_template_product->add();
                }

                foreach ($quotation->getQuotationAllCharges() as $charge) {
                    $charge = new QuotationCharge($charge['id_roja45_quotation_charge']);

                    $template_charge = new RojaQuotationTemplateCharge();
                    $template_charge->id_roja45_quotation_template = $template->id;
                    $template_charge->charge_name = $charge->charge_name;
                    $template_charge->charge_type = $charge->charge_type;
                    $template_charge->charge_method = $charge->charge_method;
                    $template_charge->charge_value = $charge->charge_value;
                    $template_charge->charge_amount = $charge->charge_amount;
                    $template_charge->charge_amount_wt = $charge->charge_amount_wt;
                    $template_charge->specific_product = $charge->specific_product;
                    $template_charge->id_roja45_quotation_product = $charge->id_roja45_quotation_product;
                    $template_charge->id_cart_rule = $charge->id_cart_rule;
                    $template_charge->add();
                }
                foreach ($quotation->getQuotationAllDiscounts() as $discount) {
                    $discount = new QuotationCharge($discount['id_roja45_quotation_charge']);

                    $template_discount = new RojaQuotationTemplateCharge();
                    $template_discount->id_roja45_quotation_template = $template->id;
                    $template_discount->charge_name = $discount->charge_name;
                    $template_discount->charge_type = $discount->charge_type;
                    $template_discount->charge_method = $discount->charge_method;
                    $template_discount->charge_value = $discount->charge_value;
                    $template_discount->charge_amount = $discount->charge_amount;
                    $template_discount->charge_amount_wt = $discount->charge_amount_wt;
                    $template_discount->specific_product = $discount->specific_product;
                    $template_discount->id_roja45_quotation_product = $discount->id_roja45_quotation_product;
                    $template_discount->id_cart_rule = $discount->id_cart_rule;
                    $template_discount->add();
                }

                $link = $this->context->link->getAdminLink(
                        'AdminQuotationsPro',
                        true
                    ) . '&view' . $this->table . '&' . $this->identifier . '=' . $quotation->id . '&token=' . $this->token;
                Tools::redirectAdmin($link);
            } else {
                throw new Exception($this->l('Quotation could not be loaded.', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->display = 'edit';
            $this->errors = $validationErrors;
        }
    }

    public function processAddDocument()
    {
        $validationErrors = array();
        if ($this->tabAccess['edit'] === '0') {
            $validationErrors[] = $this->l('You do not have the right permission', 'AdminQuotationsPro');
        }
        try {
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->l('Quotation Id missing', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (Validate::isLoadedObject($quotation)) {
                    $max_file_size = RojaFortyFiveQuotationsProCore::getBytesValue(
                        ini_get('upload_max_filesize')
                    );
                    if ($id_document = Tools::getValue('available_document')) {
                        $document = new QuotationDocument($id_document, $quotation->id_lang);
                        if (Validate::isLoadedObject($document)) {
                            $quotation->addDocument(
                                $document->display_name,
                                $document->file_name,
                                $document->internal_name,
                                $document->file_type,
                                $id_document
                            );
                        }
                    } elseif (isset($_FILES['document'])
                        && isset($_FILES['document']['tmp_name'])
                        && !empty($_FILES['document']['tmp_name'])) {
                        if ($_FILES['document']['size']
                            > $max_file_size) {
                            throw new Exception('File too large: ' . $_FILES['document']['size']);
                        } else {
                            $ext = Tools::substr(
                                $_FILES['document']['name'],
                                strrpos($_FILES['document']['name'], '.') + 1
                            );
                            $file_name = md5($_FILES['document']['name']) . '.' . $ext;

                            if (!file_exists(_PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference)) {
                                mkdir(
                                    _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference,
                                    0777,
                                    true
                                );
                            }
                            if (!move_uploaded_file(
                                $_FILES['document']['tmp_name'],
                                _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' .
                                DIRECTORY_SEPARATOR . $quotation->reference .
                                DIRECTORY_SEPARATOR . $file_name
                            )) {
                                return Tools::displayError(
                                    $this->l('An error occurred while attempting to upload the file.', 'AdminQuotationsPro')
                                );
                            } else {
                                $quotation->addDocument(
                                    $_FILES['document']['name'],
                                    $_FILES['document']['name'],
                                    $file_name,
                                    $ext
                                );
                            }
                        }
                    }
                    $link = $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&view' . $this->table . '&' . $this->identifier . '=' .
                        $id_roja45_quotation . '&token=' . $this->token;
                    Tools::redirectAdmin($link);
                } else {
                    throw new Exception($this->l('The quotation could not be loaded.', 'AdminQuotationsPro'));
                }
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->display = 'edit';
            $this->errors = $validationErrors;
        }
    }

    public function processDeleteDocument()
    {
        $validationErrors = array();
        if ($this->tabAccess['edit'] === '0') {
            $validationErrors[] = $this->l('You do not have the right permission', 'AdminQuotationsPro');
        }
        try {
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->l('Quotation Id missing', 'AdminQuotationsPro');
            }
            if (!$id_roja45_quotation_document = Tools::getValue('id_roja45_quotation_document')) {
                $validationErrors[] = $this->l('Quotation Document Id missing', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (Validate::isLoadedObject($quotation)) {
                    $quotation->deleteDocument($id_roja45_quotation_document);
                    $link = $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&view' . $this->table . '&' . $this->identifier . '=' .
                        $id_roja45_quotation . '&token=' . $this->token;
                    Tools::redirectAdmin($link);
                } else {
                    throw new Exception($this->l('The quotation could not be loaded.', 'AdminQuotationsPro'));
                }
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $this->display = 'view';
            $this->errors = $validationErrors;
        }
    }

    /**
     * ajaxProcessImageUpload - Upload a custom image for a quotation product
     *
     * @return json
     *
     */
    public function ajaxProcessUploadProductImage()
    {
        $validationErrors = array();

        try {
            ob_start();
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->l('Quotation Id missing', 'AdminQuotationsPro');
            }
            if (!$id_roja45_quotation_product = Tools::getValue('id_roja45_quotation_product')) {
                $validationErrors[] = $this->l('Quotation Product Id missing', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                $max_file_size = RojaFortyFiveQuotationsProCore::getBytesValue(
                    ini_get('upload_max_filesize')
                );
                if (isset($_FILES['uploadImage'])
                    && isset($_FILES['uploadImage']['tmp_name'])
                    && !empty($_FILES['uploadImage']['tmp_name'])) {
                    if ($_FILES['uploadImage']['size']
                        > $max_file_size) {
                        throw new Exception('File too large: ' . $_FILES['uploadImage']['size']);
                    } else {
                        $ext = Tools::substr(
                            $_FILES['uploadImage']['name'],
                            strrpos($_FILES['uploadImage']['name'], '.') + 1
                        );
                        $file_name = md5($_FILES['uploadImage']['name']) . '.' . $ext;

                        if (!file_exists(_PS_ROOT_DIR_ . '/img/modules/roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference)) {
                            mkdir(_PS_ROOT_DIR_ . '/img/modules/roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference, 0777, true);
                        }
                        $file = '/img/modules/roja45quotationspro' .
                            DIRECTORY_SEPARATOR . $quotation->reference .
                            DIRECTORY_SEPARATOR . $file_name;

                        if (!move_uploaded_file(
                            $_FILES['uploadImage']['tmp_name'],
                            _PS_ROOT_DIR_ . $file
                        )) {
                            return Tools::displayError(
                                $this->l('An error occurred while attempting to upload the file.', 'AdminQuotationsPro')
                            );
                        } else {
                            $quotation->addProductImage(
                                $id_roja45_quotation_product,
                                $file
                            );
                        }
                    }
                }

                ob_end_clean();
                die(json_encode(array(
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $id_roja45_quotation . '&viewroja45_quotationspro',
                    'result' => 1,
                    'response' => $this->l('Updated', 'AdminQuotationsPro'),
                )));
            } else {
                throw new Exception($this->l('Quotation could not be loaded.', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }

    /**
     * ajaxProcessImageUpload - Upload a custom image for a quotation product
     *
     * @return json
     *
     */
    public function ajaxProcessDeleteProductImage()
    {
        $validationErrors = array();

        try {
            ob_start();
            if (!$id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $validationErrors[] = $this->l('Quotation Id missing', 'AdminQuotationsPro');
            }
            if (!$id_roja45_quotation_product = Tools::getValue('id_roja45_quotation_product')) {
                $validationErrors[] = $this->l('Quotation Product Id missing', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                $quotation->deleteProductImage(
                    $id_roja45_quotation_product
                );
                ob_end_clean();
                die(json_encode(array(
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $id_roja45_quotation . '&viewroja45_quotationspro',
                    'result' => 1,
                    'response' => $this->l('Deleted', 'AdminQuotationsPro'),
                )));
            } else {
                throw new Exception($this->l('Quotation could not be loaded.', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }

    /**
     * ajaxProcessSaveAsTemplate - Create an account for this customer
     *
     * @return json
     *
     */
    public function ajaxProcessUpdateTemplate()
    {
        $validationErrors = array();
        try {
            ob_start();
            $template = new RojaQuotation((int) Tools::getValue('id_roja45_template'));
            if (!Validate::isLoadedObject($template)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $template->valid_days = Tools::getValue('valid_for');
                $template->id_country = Tools::getValue('tax_country');
                $template->id_state = Tools::getValue('tax_state');
                $template->calculate_taxes = Tools::getValue('ROJA45_QUOTATIONSPRO_ENABLE_TAXES');

                if (!$template->save()) {
                    throw new Exception($this->l('Unable to save quotation.', 'AdminQuotationsPro'));
                }

                ob_end_clean();
                die(json_encode(array(
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationTemplates',
                            true
                        ) . '&id_roja45_quotation=' . $template->id_roja45_quotation . '&viewroja45_quotationspro',
                    'result' => 1,
                    'response' => $this->l('Updated', 'AdminQuotationsPro'),
                )));
            } else {
                throw new Exception($this->l('Quotation could not be loaded.', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }
    /**
     * ajaxProcessSubmitUpdateProductPosition -
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitUpdateProductPosition()
    {
        $validationErrors = array();
        try {
            ob_start();
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $quotation_product_ids = Tools::getValue('quotation_product_ids');
                foreach ($quotation_product_ids as $key => $quotation_product_id) {
                    $quotation_product = new QuotationProduct($quotation_product_id);
                    $quotation_product->position = ++$key;
                    $quotation_product->save();
                }

                ob_end_clean();
                die(json_encode(array(
                    'result' => 1,
                    'response' => $this->l('Updated', 'AdminQuotationsPro'),
                )));
            } else {
                throw new Exception($this->l('Quotation could not be loaded.', 'AdminQuotationsPro'));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
        }
    }

    /**
     * ajaxProcessSubmitCreateCustomerAccount - Create an account for this customer
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitCreateCustomerAccount()
    {
        $validationErrors = array();
        try {
            $firstname = Tools::getValue('firstname');
            if (!Tools::strlen(trim($firstname)) > 0) {
                $validationErrors[] = $this->l('You must provide a firstname', 'AdminQuotationsPro');
            }
            $lastname = Tools::getValue('lastname');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a lastname', 'AdminQuotationsPro');
            }
            $customer_email = Tools::getValue('email');
            if (!Tools::strlen(trim($customer_email)) > 0) {
                $validationErrors[] = $this->l('You must provide an email address', 'AdminQuotationsPro');
            }

            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }
            if (!count($validationErrors)) {
                ob_start();
                $shop = new Shop($quotation->id_shop);
                $quotation->firstname = $firstname;
                $quotation->lastname = $lastname;
                $quotation->email = trim($customer_email);
                $quotation->tmp_password = RojaFortyFiveQuotationsProCore::passwdGen(8);
                if (!$quotation->id_customer = $this->createCustomerAccount(
                    $shop->id,
                    $shop->id_shop_group,
                    $quotation->firstname,
                    $quotation->lastname,
                    $quotation->email,
                    $quotation->tmp_password
                )) {
                    die(json_encode(
                        array(
                            'result' => false,
                            'error' => Tools::displayError('Unable to create customer account.'),
                        )
                    ));
                }

                $form_data = $quotation->getFormData();
                if ($form_data && array_key_exists('ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS', $form_data)) {
                    $id_address = $this->createCustomerAddress(
                        $quotation->id_customer,
                        $this->l('My Address', 'AdminQuotationsPro'),
                        $firstname,
                        $lastname,
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS'],
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS2'],
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_CITY'],
                        !empty($form_data['ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY']) ?
                            $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY'] :
                            0,
                        !empty($form_data['ROJA45QUOTATIONSPRO_CUSTOMER_STATE']) ?
                            $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_STATE'] :
                            0,
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_ZIP'],
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_PHONE'],
                        $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_COMPANY'],
                        !empty($form_data['ROJA45QUOTATIONSPRO_CUSTOMER_DNI']) ?
                            $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_DNI'] :
                            0,
                        !empty($form_data['ROJA45QUOTATIONSPRO_CUSTOMER_VAT_NUMBER']) ?
                            $form_data['ROJA45QUOTATIONSPRO_CUSTOMER_VAT_NUMBER'] :
                            0
                    );

                    if (!isset($quotation->id_address_invoice)) {
                        $quotation->id_address_invoice = $id_address;
                    }

                    if (!isset($quotation->id_address_delivery)) {
                        $quotation->id_address_delivery = $id_address;
                    }
                }

                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                ob_end_clean();
                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id . '&viewroja45_quotationspro',
                        'result' => 1,
                        'response' => $this->l('Account created.', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessDeleteQuotation - Create an account for this customer
     *
     * @return json
     *
     */
    public function ajaxProcessDeleteQuotation()
    {
        $validationErrors = array();
        try {
            ob_start();
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }
            if (!count($validationErrors)) {
                if (!$quotation->delete()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                ob_end_clean();
                die(json_encode(array(
                    'redirect' => $this->context->link->getAdminLink('AdminQuotationsPro', true),
                    'result' => 1,
                    'response' => $this->l('Deleted', 'AdminQuotationsPro'),
                )));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessDeleteQuotation - Create an account for this customer
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitUpdateMessageReadFlag()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = Tools::displayError('The quotation could not be loaded.');
            }
            if (!$id_customer_message = (int) Tools::getValue('id_customer_message')) {
                $validationErrors[] = Tools::displayError('No customer message id provided.');
            }
            if (!count($validationErrors)) {
                $message = new CustomerMessage($id_customer_message);
                $message->read = !$message->read;
                $message->save();

                die(json_encode(array(
                    'read' => (int) $message->read,
                    'result' => 1,
                    'response' => $this->l('Success', 'AdminQuotationsPro'),
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                )));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitSetCustomerAddress - Create an account for this customer
     *
     * @return json
     *

    public function ajaxProcessSubmitSetCustomerAddress()
    {
    $validationErrors = array();
    try {
    $quotation = new RojaQuotation((int)Tools::getValue('id_roja45_quotation'));
    if (!Validate::isLoadedObject($quotation)) {
    die(json_encode(
    array(
    'result' => false,
    'error' => Tools::displayError('The quotation could not be loaded.'),
    )
    ));
    }
    if (!count($validationErrors)) {
    $quotation->id_address = (int)Tools::getValue('id_address');
    if (!$quotation->save()) {
    $validationErrors[] = $this->l('Unable to save quotation.');
    die(json_encode(
    array(
    'result' => 0,
    'errors' => $validationErrors,
    )
    ));
    }

    die(json_encode(
    array(
    'result' => 1,
    'message' => $this->l('Customer address saved.'),
    )
    ));
    } else {
    die(json_encode(
    array(
    'result' => 0,
    'errors' => $validationErrors,
    )
    ));
    }
    } catch (Exception $e) {
    $validationErrors = array();
    $validationErrors[] = $e->getMessage();
    $json = json_encode(
    array(
    'result' => 0,
    'errors' => $validationErrors,
    'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
    'exception' => $e,
    )
    );
    die($json);
    }
    }*/

    /**
     * ajaxProcessSubmitCreateCustomerAddress - Create an account for this customer
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitCreateCustomerAddress()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }

            $alias = Tools::getValue('address_alias');
            if (!Tools::strlen(trim($alias)) > 0) {
                $validationErrors[] = $this->l('You must provide an address alias', 'AdminQuotationsPro');
            }
            $firstname = Tools::getValue('address_firstname');
            if (!Tools::strlen(trim($firstname)) > 0) {
                $validationErrors[] = $this->l('You must provide a customer name for this address', 'AdminQuotationsPro');
            }
            $lastname = Tools::getValue('address_lastname');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a customer lastname for this address', 'AdminQuotationsPro');
            }
            $address_line1 = Tools::getValue('address_line1');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide the address first line', 'AdminQuotationsPro');
            }
            $address_city = Tools::getValue('address_city');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a city', 'AdminQuotationsPro');
            }
            $address_zip = Tools::getValue('address_zip');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a zip/postal code', 'AdminQuotationsPro');
            }
            $address_country_id = Tools::getValue('address_country_id');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a country', 'AdminQuotationsPro');
            }
            $address_telephone = Tools::getValue('address_telephone');
            if (!Tools::strlen(trim($lastname)) > 0) {
                $validationErrors[] = $this->l('You must provide a telephone number', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $id_address = $this->createCustomerAddress(
                    $quotation->id_customer,
                    $alias,
                    $firstname,
                    $lastname,
                    $address_line1,
                    Tools::getValue('address_line2'),
                    $address_city,
                    $address_country_id,
                    Tools::getValue('address_state_id'),
                    $address_zip,
                    $address_telephone,
                    Tools::getValue('company'),
                    Tools::getValue('dni'),
                    Tools::getValue('vat_number')
                );

                if (!isset($quotation->id_address_invoice)) {
                    $quotation->id_address_invoice = $id_address;
                }

                if (!isset($quotation->id_address_delivery)) {
                    $quotation->id_address_delivery = $id_address;
                }

                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                die(json_encode(
                    array(
                        'result' => 1,
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'message' => $this->l('Customer address saved.', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessLoadCustomerQuotation - Retrieve a customer's request
     *
     * @return json
     *
     */
    public function ajaxProcessLoadCustomerQuotation()
    {
        $validationErrors = array();
        try {
            ob_start();
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => 0,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }

            $customer = new Customer($quotation->id_customer);
            if (!(int) $quotation->quote_sent && !Validate::isLoadedObject($customer)) {
                $shop = new Shop($quotation->id_shop);
                $tmp_password = RojaFortyFiveQuotationsProCore::passwdGen(8);
                $quotation->id_customer = $this->createCustomerAccount(
                    $shop->id,
                    $shop->id_shop_group,
                    $quotation->firstname,
                    $quotation->lastname,
                    $quotation->emailname,
                    $tmp_password
                );
                $quotation->tmp_password = $tmp_password;
                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $customer = new Customer($quotation->id_customer);
            }
            $language = new Language($quotation->id_lang);
            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
                $custom_object = $quotation->getSummaryDetails(
                    $quotation->id_lang,
                    $quotation->id_currency,
                    $quotation->calculate_taxes
                );

                $custom_object['quotation_products'] = array_reverse($custom_object['quotation_products']);
                $id_roja45_quotation_answer = (int) Tools::getValue('id_roja45_quotation_answer');
                $id_roja45_status = (int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_STATUS_SENT'
                );

                $sent_status = new QuotationStatus($id_roja45_status);

                $id_roja45_quotation_answer = ($id_roja45_quotation_answer) ?
                    $id_roja45_quotation_answer : $sent_status->id_roja45_quotation_answer;

                $quotation_answer = new QuotationAnswer($id_roja45_quotation_answer, $quotation->id_lang);
                if (!Validate::isLoadedObject($quotation_answer)) {
                    $validationErrors[] = $this->l('Unable to load email template.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $default_data = $quotation_answer->getDefaultVars(
                    $this->context->language->id,
                    (int) $this->context->shop->id
                );
                $custom_object = array_merge(
                    $custom_object,
                    $default_data
                );

                $language = new Language($quotation->id_lang);
                $custom_object['tax_text'] = ((int) $custom_object['show_taxes']) ?
                    Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)) :
                    Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language));

                $html_template = $quotation_answer->getTemplatePath(QuotationAnswer::$HTML_TEMPLATE);
                $tpl = $this->context->smarty->createTemplate(
                    $html_template
                );

                $tpl->assign(
                    array(
                        'show_account' => 0,
                        'show_customization_cost' => isset($custom_object['quotation_has_customization_cost']) ?
                            $custom_object['quotation_has_customization_cost'] :
                            0,
                        'show_product_customizations' => isset($custom_object['quotation_has_customizations']) ?
                            $custom_object['quotation_has_customizations'] :
                            0,
                        'show_product_discounts' => isset($custom_object['quotation_has_discounts']) ?
                            $custom_object['quotation_has_discounts'] :
                            0,
                        'show_additional_shipping' => isset($custom_object['quotation_has_additional_shipping']) ?
                            $custom_object['quotation_has_additional_shipping'] :
                            0,
                        'show_product_comments' => isset($custom_object['quotation_has_comments']) ?
                            $custom_object['quotation_has_comments'] :
                            0,
                        'show_ecotax' => isset($custom_object['quotation_has_ecotax']),
                        'show_prices' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'),
                        'show_summary' => 1,
                    )
                );
                if (Validate::isLoadedObject($customer)) {
                    if (!empty($quotation->tmp_password)) {
                        $tpl->assign(
                            array(
                                'show_account' => 1,
                            )
                        );
                        $account_data = array(
                            'customer_username' => $quotation->email,
                            'customer_temporary_password' => $quotation->tmp_password,
                            'customer_quotes_link' => $this->context->link->getPageLink('my-account', true),
                        );
                        $custom_object = array_merge(
                            $custom_object,
                            $account_data
                        );
                    }
                }

                $email_data = array(
                    'quotation_purchase_link' => $this->context->link->getModuleLink(
                        'roja45quotationspro',
                        'QuotationsProFront',
                        array(
                            'r' => $quotation->reference,
                            'h' => hash('md5', $quotation->email),
                        ),
                        true,
                        $quotation->id_lang,
                        $quotation->id_shop
                    ),
                );
                $custom_object = array_merge(
                    $custom_object,
                    $email_data
                );

                $content = $tpl->fetch();
                $content = QuotationAnswer::processRecursiveTemplate($content, $custom_object);

                $quotation_email_templates = QuotationAnswer::getMailTemplates($quotation->id_lang);
                $default_answer = $id_roja45_quotation_answer;
                if ((int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                )) {
                    $message_subject = $quotation_answer->subject;
                } else {
                    $message_subject = $quotation_answer->subject . ' : [#ct%2$s] : [#tc%3$s]';
                }
            } else {
                $template_vars = array();
                if (Validate::isLoadedObject($customer)) {
                    if ($quotation->tmp_password) {
                        $tmp_vars = array(
                            'include_account' => 1,
                            'username' => $quotation->email,
                            'password' => $quotation->tmp_password,
                            'my_account_link' => $this->context->link->getPageLink('my-account', true),
                        );
                        $template_vars = array_merge($template_vars, $tmp_vars);
                    }
                }
                if (Configuration::get('PS_MAIL_TYPE') == Mail::TYPE_BOTH ||
                    Configuration::get('PS_MAIL_TYPE') == Mail::TYPE_HTML) {
                    $tpl = $this->createModuleTemplate(
                        'send_quote.tpl'
                    );
                } else {
                    $tpl = $this->createModuleTemplate(
                        'send_quote_txt.tpl'
                    );
                }

                $smarty_vars = $quotation->getSmartyVars();
                $template_vars = array_merge($template_vars, $smarty_vars);
                $tpl->assign($template_vars);
                $content = $tpl->fetch();
                $quotation_email_templates = array();
                $default_answer = 0;
                if ((int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                )) {
                    $message_subject = Module::getInstanceByName('roja45quotationspro')->l('Quotation [%1$s]', false, RojaFortyFiveQuotationsProCore::getLocale($language));
                } else {
                    $message_subject = Module::getInstanceByName('roja45quotationspro')->l('Quotation [%1$s] : [#ct%2$s] : [#tc%3$s]', false, RojaFortyFiveQuotationsProCore::getLocale($language));
                }
            }

            $iso = Context::getContext()->language->iso_code;
            Context::getContext()->language->iso_code = $language->iso_code;

            $quotation_documents = $quotation->getDocuments();

            ob_end_clean();

            $tpl = $this->context->smarty->createTemplate(
                $this->getTemplatePath(
                    'quotationview_loadmessage_modal.tpl'
                ) . 'quotationview_loadmessage_modal.tpl'
            );
            $tpl->assign(
                array(
                    'id_roja45_quotation' => $quotation->id,
                    'quotation_documents' => $quotation_documents,
                    'quotation_email_templates' => $quotation_email_templates,
                    'default_email_template' => $default_answer,
                    'message_subject' => $message_subject,
                )
            );
            $view = $tpl->fetch();

            die(json_encode(
                array(
                    'result' => 1,
                    'view' => $view,
                    'can_edit' => 1,
                    //'can_edit' => ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) ? 0 : 1,
                    'content' => $content,
                )
            ));
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessLoadMessageTemplate - Load a message template
     *
     * @return json
     *
     */
    public function ajaxProcessLoadMessageTemplate()
    {
        $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
        if (!Validate::isLoadedObject($quotation)) {
            die(json_encode(
                array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )
            ));
        }

        try {
            ob_start();
            $template = Tools::getValue('template');
            $result = '';
            $type = (int) Tools::getValue('type');
            if ($type == QuotationAnswer::$MAIL) {
                $custom_object = $quotation->getSummaryDetails(
                    $quotation->id_lang,
                    $quotation->id_currency,
                    $quotation->calculate_taxes
                );
                $id_roja45_quotation_answer = Tools::getValue('id_roja45_quotation_answer');
                $quotation_answer = new QuotationAnswer($id_roja45_quotation_answer, $quotation->id_lang);
                if (!Validate::isLoadedObject($quotation_answer)) {
                    $validationErrors = array();
                    $validationErrors[] = $this->l('Unable to load email template.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $default_data = $quotation_answer->getDefaultVars(
                    $this->context->language->id,
                    (int) $this->context->shop->id
                );
                $custom_object = array_merge(
                    $custom_object,
                    $default_data
                );

                $language = new Language($quotation->id_lang);
                $custom_object['tax_text'] = ((int) $quotation->calculate_taxes) ?
                    Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)) :
                    Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language));

                $html_template = $quotation_answer->getTemplatePath(QuotationAnswer::$HTML_TEMPLATE);

                $tpl = $this->context->smarty->createTemplate(
                    $html_template
                );

                $tpl->assign(
                    array(
                        'show_account' => 0,
                    )
                );

                $email_data = array(
                    'quotation_purchase_link' => $this->context->link->getModuleLink(
                        'roja45quotationspro',
                        'QuotationsProFront',
                        array(
                            'p' => $quotation->id,
                        ),
                        true
                    ),
                );
                $custom_object = array_merge(
                    $custom_object,
                    $email_data
                );

                $content = $tpl->fetch();
                $result = QuotationAnswer::processRecursiveTemplate($content, $custom_object);
            } elseif ($type == QuotationAnswer::$OLD) {
                $answer = new QuotationAnswer(
                    Tools::getValue('id_roja45_quotation_answer'),
                    $this->context->language->id
                );
                if (!Validate::isLoadedObject($quotation)) {
                    die(json_encode(array(
                        'result' => 0,
                        'error' => Tools::displayError('The quotation answer could not be loaded.'),
                    )));
                }

                $template_path =
                    _PS_ROOT_DIR_ .
                    '/modules/roja45quotationspro/views/templates/admin/custom/' .
                    $answer->template .
                    '.tpl';

                $template_vars = $quotation->getTemplateVars();
                if (!file_exists($template_path)) {
                    $validationErrors = array();
                    $validationErrors[] = 'Template does not exist: ' . $template_path;
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                $template_body = Tools::file_get_contents($template_path);
                if (!$template_body || !Tools::strlen($template_body)) {
                    $validationErrors = array();
                    $validationErrors[] = 'Template has no content for the selected quotation language.';
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                $result = str_replace(array_keys($template_vars), array_values($template_vars), $template_body);
                if (!$result) {
                    $validationErrors = array();
                    $validationErrors[] = 'Unable to populate template variables: ' . $template_path;
                    $validationErrors[] = 'Template variables: ' . print_r($template_vars, true);
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
            }

            $message_subject = $this->l('New Message for Quotation [%1$s] : [#ct%2$s] : [#tc%3$s]', 'AdminQuotationsPro');

            ob_end_clean();
            die(json_encode(
                array(
                    'result' => 1,
                    'message_subject' => $message_subject,
                    'content' => $result,
                )
            ));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessSearchProducts()
    {
        try {
            $currency = new Currency((int) Tools::getValue('id_currency'));
            $id_customer = null;
            if ($id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (isset($quotation->id_customer)) {
                    $id_customer = $quotation->id_customer;
                }
            }
            if ($products = Product::searchByName(
                (int) $this->context->language->id,
                pSQL(Tools::getValue('product_search'))
            )) {
                foreach ($products as &$product) {
                    $specific_price = null;
                    $product['price_tax_incl'] = Product::getPriceStatic(
                        (int) $product['id_product'],
                        true,
                        null,
                        Roja45QuotationsPro::DEFAULT_PRECISION,
                        null,
                        false,
                        true,
                        1,
                        false,
                        $id_customer,
                        null,
                        true,
                        $specific_price
                    );

                    $product['price_tax_excl'] = Product::getPriceStatic(
                        (int) $product['id_product'],
                        false,
                        null,
                        Roja45QuotationsPro::DEFAULT_PRECISION,
                        null,
                        false,
                        true,
                        1,
                        false,
                        $id_customer,
                        null,
                        true,
                        $specific_price
                    );

                    $product['formatted_price'] = Tools::displayPrice(
                        Tools::convertPrice($product['price_tax_incl'], $currency),
                        $currency
                    );
                    $product['price_tax_incl'] = Tools::ps_round(
                        Tools::convertPrice($product['price_tax_incl'], $currency),
                        6
                    );
                    $product['price_tax_excl'] = Tools::ps_round(
                        Tools::convertPrice($product['price_tax_excl'], $currency),
                        6
                    );
                    $productObj = new Product(
                        (int) $product['id_product'],
                        false,
                        (int) $this->context->language->id
                    );
                    $supplier = new Supplier($productObj->id_supplier, (int) $this->context->language->id);
                    $product['supplier'] = $supplier->name;
                    $product['wholesale_price'] = Tools::displayPrice(
                        Tools::convertPrice($productObj->wholesale_price, $currency),
                        $currency
                    );
                    $combinations = array();
                    $attributes = $productObj->getAttributesGroups((int) $this->context->language->id);
                    if (Tools::isSubmit('id_address')) {
                        $product['tax_rate'] = $productObj->getTaxesRate(
                            new Address(Tools::getValue('id_address'))
                        );
                    }

                    $product['warehouse_list'] = array();

                    $product['specific_price'] = 0;
                    if ($specific_price) {
                        $product['specific_price'] = 1;
                    }

                    foreach ($attributes as $attribute) {
                        $combination = new Combination($attribute['id_product_attribute']);
                        if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                            $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                        }
                        $combinations[$attribute['id_product_attribute']]['attributes'] .=
                            $attribute['attribute_name'] . ' - ';
                        $combinations[$attribute['id_product_attribute']]['id_product_attribute'] =
                            $attribute['id_product_attribute'];
                        $combinations[$attribute['id_product_attribute']]['default_on'] =
                            $attribute['default_on'];
                        $combinations[$attribute['id_product_attribute']]
                        ['wholesale_price'] = $combination->wholesale_price;
                        $combinations[$attribute['id_product_attribute']]
                        ['wholesale_price_formatted'] = Tools::ps_round(
                            Tools::convertPrice($combination->wholesale_price, $currency),
                            6
                        );
                        if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                            $price_tax_incl = Product::getPriceStatic(
                                (int) $product['id_product'],
                                true,
                                $attribute['id_product_attribute'],
                                Roja45QuotationsPro::DEFAULT_PRECISION,
                                null,
                                false,
                                true,
                                1,
                                false,
                                $id_customer
                            );
                            $price_tax_excl = Product::getPriceStatic(
                                (int) $product['id_product'],
                                false,
                                $attribute['id_product_attribute'],
                                Roja45QuotationsPro::DEFAULT_PRECISION,
                                null,
                                false,
                                true,
                                1,
                                false,
                                $id_customer
                            );
                            $combinations[$attribute['id_product_attribute']]
                            ['price_tax_incl'] = Tools::ps_round(
                                Tools::convertPrice($price_tax_incl, $currency),
                                6
                            );
                            $combinations[$attribute['id_product_attribute']]
                            ['price_tax_incl_formatted'] = Tools::displayPrice(
                                Tools::convertPrice($price_tax_incl, $currency),
                                $currency
                            );
                            $combinations[$attribute['id_product_attribute']]
                            ['price_tax_excl'] = Tools::ps_round(
                                Tools::convertPrice($price_tax_excl, $currency),
                                6
                            );
                            $combinations[$attribute['id_product_attribute']]
                            ['price_tax_excl_formatted'] = Tools::displayPrice(
                                Tools::convertPrice($price_tax_excl, $currency),
                                $currency
                            );
                            $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(
                                Tools::convertPrice($price_tax_excl, $currency),
                                $currency
                            );
                        }
                        if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                            $combinations[
                            $attribute['id_product_attribute']
                            ]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                                (int) $product['id_product'],
                                $attribute['id_product_attribute'],
                                (int) $this->context->shop->id
                            );
                        }

                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                            (int) $product['advanced_stock_management'] == 1) {
                            $product['warehouse_list'][
                            $attribute['id_product_attribute']
                            ] = Warehouse::getProductWarehouseList(
                                $product['id_product'],
                                $attribute['id_product_attribute']
                            );
                        } else {
                            $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                        }

                        $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity(
                            $product['id_product'],
                            $attribute['id_product_attribute']
                        );
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                        (int) $product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                    } else {
                        $product['warehouse_list'][0] = array();
                    }

                    $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct(
                        (int) $product['id_product'],
                        0,
                        (int) $this->context->shop->id
                    );

                    foreach ($combinations as &$combination) {
                        $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                    }
                    $product['combinations'] = $combinations;

                    if ($product['customizable']) {
                        $product_instance = new Product((int) $product['id_product']);
                        $product['customization_fields'] = $product_instance->getCustomizationFields(
                            $this->context->language->id
                        );
                    }
                }
                die(json_encode(array(
                    'result' => true,
                    'products' => $products,
                )));
            } else {
                die(json_encode(array(
                    'result' => true,
                    'products' => array(),
                )));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => false,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessGetCatalogPriceRules()
    {
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )));
            }

            if ($id_product = Tools::getValue('id_product')) {
                $id_product_attribute = Tools::getValue('id_product_attribute');
                $qty = Tools::getValue('qty');
                $id_customer = null;
                $id_group = null;
                $id_country = null;
                $id_state = null;
                if ($quotation->id_customer) {
                    $id_customer = $quotation->id_customer;
                    $customer = new Customer($quotation->id_customer);
                    $id_group = $customer->id_default_group;
                    $address = $quotation->getTaxAddress();
                    $id_country = $address->id_country;
                    $id_state = $address->id_state;
                }
                if ($specific_price = SpecificPrice::getSpecificPrice(
                    (int) $id_product,
                    $quotation->id_shop,
                    $quotation->id_currency,
                    $id_country,
                    $id_group,
                    $qty,
                    $id_product_attribute,
                    $id_customer,
                    0,
                    0
                )) {
                    $specific_price_return = null;
                    $price = Product::priceCalculation(
                        $quotation->id_shop,
                        $id_product,
                        $id_product_attribute,
                        $id_country,
                        $id_state,
                        null,
                        $quotation->id_currency,
                        $id_group,
                        $qty,
                        $quotation->calculate_taxes,
                        6,
                        false,
                        true,
                        true,
                        $specific_price_return,
                        true,
                        $id_customer,
                        true,
                        0,
                        0,
                        0
                    );
                    die(json_encode(array(
                        'price' => $price,
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )));
                }
            }
            die(json_encode(array(
                'result' => 0,
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessGetPriceVolumeDiscount()
    {
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )));
            }

            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');
            $product_quantity = (int) Tools::getValue('product_quantity');

            $address = $quotation->getTaxAddress();
            $currency = new Currency($quotation->id_currency);

            $orig_price_exc = Product::getPriceStatic(
                $id_product,
                false,  // usetax
                $id_product_attribute,
                6, //$decimals
                null, //$divisor
                false,  //$only_reduc
                true,  //$usereduc
                //1,  //$quantity
                $product_quantity,  //$quantity
                false,  //$force_associated_tax
                $quotation->id_customer,  //$id_customer
                0,  //$id_cart
                //null,  //$id_address
                $address->id,  //$id_address
                $specific_price_output,
                true, // $with_ecotax
                true, // $use_group_reduction
                Context::getContext(),
                true, //$use_customer_price
                null //$id_customization
            );

            $orig_price_exc_currency = Tools::ps_round(
                Tools::convertPrice($orig_price_exc, $currency),
                6
            );
            die(json_encode(array(
                'offer_price' => $orig_price_exc,
                'offer_price_currency' => $orig_price_exc_currency,
                'result' => 1,
                'response' => $this->l('Success', 'AdminQuotationsPro'),
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessAddSelectedProducts()
    {
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )));
            }

            if ($selected_products = Tools::getValue('selected_product_ids')) {
                foreach ($selected_products as $selected_product) {
                    $id_product_attribute = null;
                    if (isset($selected_product['id_product_attribute']) && $selected_product['id_product_attribute']) {
                        $id_product_attribute = $selected_product['id_product_attribute'];
                        $combinationObj = new Combination($selected_product['id_product_attribute']);
                        if (!Validate::isLoadedObject($combinationObj)) {
                            continue;
                        }
                    }

                    if ($id_roja45_quotation_product = QuotationProduct::getQuotationProduct(
                        $quotation->id,
                        $selected_product['id_product'],
                        $id_product_attribute
                    )) {
                        $quotation_product = new QuotationProduct($id_roja45_quotation_product);
                        $quotation->deleteProduct($quotation_product);
                    }
                    $productObj = new Product($selected_product['id_product'], false, $quotation->id_lang);
                    if (!Validate::isLoadedObject($productObj)) {
                        continue;
                    }

                    if (isset($quotation->id_customer) && $quotation->id_customer) {
                        $id_group = (int) Customer::getDefaultGroupId($quotation->id_customer);
                    } else {
                        $id_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
                    }

                    $offer_price = RojaFortyFiveQuotationsProCore::getCurrencyValue(
                        $selected_product['product_price_tax_excl']
                    );

                    $address = $quotation->getTaxAddress();

                    $specific_price = SpecificPrice::getSpecificPrice(
                        $productObj->id,
                        $quotation->id_shop,
                        $quotation->id_currency,
                        $quotation->id_country,
                        $id_group,
                        1,
                        $id_product_attribute,
                        $quotation->id_customer
                    );

                    if ($selected_product['product_discount']) {
                        if ($selected_product['product_discount_type'] == 'percentage') {
                            $offer_price = $offer_price * ((100 - $selected_product['product_discount']) / 100);
                        } else {
                            $offer_price = $offer_price - $selected_product['product_discount'];
                        }
                        if ($offer_price < 0) {
                            $offer_price = 0;
                        }
                    } else if ($specific_price) {
                        $selected_product['product_discount'] = $specific_price['reduction'];
                        $selected_product['product_discount_type'] = $specific_price['reduction_type'];
                    } else if ($discount_by_group = Group::getReductionByIdGroup($id_group)) {
                        $selected_product['product_discount'] = $discount_by_group;
                        $selected_product['product_discount_type'] = 'percentage';
                        $offer_price = $offer_price * ((100 - $selected_product['product_discount']) / 100);
                    }

                    $currency = new Currency($quotation->id_currency);
                    if ((int) Configuration::get('PS_CURRENCY_DEFAULT') == $quotation->id_currency) {
                        $c_rate = 1.0;
                    } else {
                        $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);
                    }
                    $offer_price /= $c_rate;
                    $offer_price = Tools::ps_round(
                        $offer_price,
                        6
                    );

                    $orig_price_exc = Product::getPriceStatic(
                        $selected_product['id_product'],
                        false,  // usetax
                        $id_product_attribute,
                        6, //$decimals
                        null, //$divisor
                        false,  //$only_reduc
                        true,  //$usereduc
                        //1,  //$quantity
                        $selected_product['product_quantity'],  //$quantity
                        false,  //$force_associated_tax
                        $quotation->id_customer,  //$id_customer
                        0,  //$id_cart
                        //null,  //$id_address
                        ($address) ? $address->id : null,  //$id_address
                        $specific_price_output,
                        true, // $with_ecotax
                        true, // $use_group_reduction
                        Context::getContext(),
                        true, //$use_customer_price
                        null //$id_customization
                    );

                    $orig_price_exc_no_ecotax = Product::getPriceStatic(
                        $selected_product['id_product'],
                        false, // usetax
                        $id_product_attribute,
                        6, //$decimals
                        null, //$divisor
                        false,  //$only_reduc
                        true,  //$usereduc
                        //1,  //$quantity
                        $selected_product['product_quantity'],  //$quantity
                        false,  //$force_associated_tax
                        $quotation->id_customer,  //$id_customer
                        0,  //$id_cart
                        //null,  //$id_address
                        ($address) ? $address->id : null,  //$id_address
                        $specific_price_output,
                        false, // $with_ecotax
                        true, // $use_group_reduction
                        Context::getContext(),
                        true, //$use_customer_price
                        null //$id_customization
                    );

                    if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')) {
                        /*$offer_price = Product::priceCalculation(
                            $quotation->id_shop,
                            $productObj->id,
                            0,
                            $quotation->id_country,
                            $quotation->id_state,
                            ($address) ? $address->postcode : null,
                            $quotation->id_currency,
                            $id_group,
                            1,
                            false,
                            6,
                            false,
                            false,
                            true,
                            $specific_price,
                            false,
                            0,
                            true,
                            0,
                            1
                        );*/

                        $orig_price_exc = Product::getPriceStatic(
                            $selected_product['id_product'],
                            false,  // usetax
                            $id_product_attribute,
                            6, //$decimals
                            null, //$divisor
                            false,  //$only_reduc
                            true,  //$usereduc
                            //1,  //$quantity
                            $selected_product['product_quantity'],  //$quantity
                            false,  //$force_associated_tax
                            $quotation->id_customer,  //$id_customer
                            0,  //$id_cart
                            //null,  //$id_address
                            ($address) ? $address->id : null,  //$id_address
                            $specific_price_output,
                            true, // $with_ecotax
                            false, // $use_group_reduction
                            Context::getContext(),
                            true, //$use_customer_price
                            null //$id_customization
                        );

                        $orig_price_exc_no_ecotax = Product::getPriceStatic(
                            $selected_product['id_product'],
                            false, // usetax
                            $id_product_attribute,
                            6, //$decimals
                            null, //$divisor
                            false,  //$only_reduc
                            true,  //$usereduc
                            //1,  //$quantity
                            $selected_product['product_quantity'],  //$quantity
                            false,  //$force_associated_tax
                            $quotation->id_customer,  //$id_customer
                            0,  //$id_cart
                            //null,  //$id_address
                            ($address) ? $address->id : null,  //$id_address
                            $specific_price_output,
                            false, // $with_ecotax
                            false, // $use_group_reduction
                            Context::getContext(),
                            true, //$use_customer_price
                            null //$id_customization
                        );
                    }

                    $ecotax = $orig_price_exc - $orig_price_exc_no_ecotax;

                    $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                        (int) $selected_product['id_product'],
                        Context::getContext()
                    );
                    $product_tax_calculator = TaxManagerFactory::getManager(
                        $quotation->getTaxAddress(),
                        $id_tax_rules_group
                    )->getTaxCalculator();
                    $tax_rate = $product_tax_calculator->getTotalRate();

                    $quotation->addProduct(
                        $selected_product['id_product'],
                        $id_product_attribute,
                        0,
                        $offer_price,
                        $selected_product['product_quantity'],
                        $selected_product['comment'],
                        $id_group,
                        array(),
                        $id_tax_rules_group,
                        $tax_rate,
                        $selected_product['product_discount'],
                        $selected_product['product_discount_type'],
                        $ecotax
                    );
                }

                die(json_encode(array(
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    'result' => 1,
                    'response' => $this->l('Product Line Added', 'AdminQuotationsPro'),
                )));
            } else {
                die(json_encode(array(
                    'result' => 0,
                    'response' => $this->l('No products selected.', 'AdminQuotationsPro'),
                )));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessGetProducts()
    {
        try {
            $currency = new Currency((int) Tools::getValue('id_currency'));
            $id_customer = 0;
            if ($id_roja45_quotation = Tools::getValue('id_roja45_quotation')) {
                $quotation = new RojaQuotation($id_roja45_quotation);
                if (isset($quotation->id_customer)) {
                    $id_customer = $quotation->id_customer;
                }
            }

            if (empty($page_number = Tools::getValue('page_number', 1))) {
                $page_number = 1;
            }
            if (empty($results_per_page = Tools::getValue('results_per_page', 10))) {
                $results_per_page = 10;
            }

            $results = RojaQuotation::searchProducts(
                Tools::getValue('multiple_search'),
                (int) Tools::getValue('product_category'),
                (int) $this->context->language->id,
                $page_number,
                $results_per_page,
                'id_product',
                'ASC'
            );

            $address = $quotation->getTaxAddress();
            if (isset($quotation->id_customer) && $quotation->id_customer) {
                $id_group = (int) Customer::getDefaultGroupId($quotation->id_customer);
            } else {
                $id_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
            }

            if (isset($results['products']) && count($results['products'])) {
                foreach ($results['products'] as &$product) {
                    if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                        $id_image = $id_images['id_image'];
                    } else {
                        $id_image = Context::getContext()->language->iso_code . '-default';
                    }
                    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                        $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('cart');
                    } else {
                        $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('medium');
                    }

                    $image = new Image($id_image, Context::getContext()->language->id);
                    $product['image'] = $image;
                    $product['id_image'] = $id_image;

                    $product['image_url'] = Context::getContext()->link->getImageLink(
                        $product['link_rewrite'],
                        $id_image,
                        $format
                    );

                    $product['admin_link'] = $this->context->link->getAdminLink(
                            'AdminProducts',
                            true
                        ) . '&id_product=' . $product['id_product'] . '&updateproduct';

                    $specific_price = null;
                    $product['price_tax_incl_reduction_amount'] = 0;
                    $product['price_tax_incl'] = Product::priceCalculation(
                        $quotation->id_shop,
                        $product['id_product'],
                        0,
                        $address->id_country,
                        $address->id_state,
                        $address->postcode,
                        $quotation->id_currency,
                        $id_group,
                        1,
                        true,
                        6,
                        false, //only_reduc
                        false, // use_reduc
                        true,
                        $specific_price,
                        !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                        $id_customer,
                        true,
                        null,
                        1
                    );

                    $product['price_tax_incl_reduction'] = Product::priceCalculation(
                        $quotation->id_shop,
                        $product['id_product'],
                        0,
                        $address->id_country,
                        $address->id_state,
                        $address->postcode,
                        $quotation->id_currency,
                        $id_group,
                        1,
                        true,
                        6,
                        false, //only_reduc
                        true, // use_reduc
                        true, //with_ecotax
                        $specific_price,
                        !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                        $id_customer,
                        true,
                        null,
                        1
                    );

                    $product['specific_price'] = 0;
                    if ($specific_price) {
                        $product['specific_price'] = 1;
                        $product['reduction_type'] = $specific_price['reduction_type'];
                        if ($specific_price['reduction_type'] == 'percentage') {
                            $product['price_tax_incl_reduction_amount'] = $specific_price['reduction'] * 100;
                        } else {
                            $product['price_tax_incl_reduction_amount'] = $specific_price['reduction'];
                        }
                    }

                    $product['price_tax_excl'] = Product::priceCalculation(
                        $quotation->id_shop,
                        $product['id_product'],
                        0,
                        $address->id_country,
                        $address->id_state,
                        $address->postcode,
                        $quotation->id_currency,
                        $id_group,
                        1,
                        false,
                        6,
                        false, //only_reduc
                        false, // use_reduc
                        true, //with_ecotax
                        $specific_price,
                        !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                        $id_customer,
                        true,
                        null,
                        1
                    );
                    $product['price_tax_excl_reduction'] = Product::priceCalculation(
                        $quotation->id_shop,
                        $product['id_product'],
                        0,
                        $address->id_country,
                        $address->id_state,
                        $address->postcode,
                        $quotation->id_currency,
                        $id_group,
                        1,
                        false,
                        6,
                        false, //only_reduc
                        true, // use_reduc
                        true, //with_ecotax
                        $specific_price,
                        !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                        $id_customer,
                        true,
                        null,
                        1
                    );
                    if ($specific_price) {
                        if ($specific_price['reduction_type'] == 'percentage') {
                            $product['price_tax_excl_reduction_amount'] = $specific_price['reduction'] * 100;
                        } else {
                            $product['price_tax_excl_reduction_amount'] = $specific_price['reduction'];
                        }
                    }

                    $product['wholesale_price_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        $product['wholesale_price'],
                        $currency
                    );
                    $product['formatted_price'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        $product['price_tax_incl'],
                        $currency
                    );

                    $product['price_tax_excl'] = number_format(
                        $product['price_tax_excl'],
                        2
                    );
                    $product['price_tax_incl'] = number_format(
                        $product['price_tax_incl'],
                        2
                    );

                    $productObj = new Product(
                        (int) $product['id_product'],
                        false,
                        (int) $this->context->language->id
                    );
                    $supplier = new Supplier($productObj->id_supplier, (int) $this->context->language->id);
                    $product['supplier'] = $supplier->name;

                    $combinations = array();
                    if (Tools::isSubmit('id_address')) {
                        $product['tax_rate'] = $productObj->getTaxesRate(
                            new Address(Tools::getValue('id_address'))
                        );
                    }
                    $has_group_discount = false;
                    /*if ($group_reduction = (float) Group::getReductionByIdGroup($id_group)) {
                        $has_group_discount = true;
                        $product['price_tax_incl_reduction_amount'] = $group_reduction;
                        $product['reduction_type'] = 'percentage';
                    }*/
                    $product['has_group_discount'] = $has_group_discount;

                    $has_volume_discount = false;
                    $attributes = $productObj->getAttributesGroups((int) $this->context->language->id);

                    if (!count($attributes)) {
                        $quantity_discounts = SpecificPrice::getQuantityDiscounts(
                            $product['id_product'],
                            $quotation->id_shop,
                            $quotation->id_currency,
                            $address->id_country,
                            $id_group,
                            0,
                            false,
                            (int)
                            $quotation->id_customer
                        );
                        if (count($quantity_discounts)) {
                            $has_volume_discount = true;
                        }
                    }
                    if (!$sort_method = Configuration::get('ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER')) {
                        $sort_method = 'sortAttributeById';
                    }
                    usort($attributes, 'RojaFortyFiveQuotationsProCore::'.$sort_method);

                    $product['warehouse_list'] = array();
                    $first_combination = null;

                    $multiple_search = $results['search'];
                    foreach ($attributes as $key => $attribute) {
                        $combinationObj = new Combination($attribute['id_product_attribute']);
                        
                        if (!Validate::isLoadedObject($combinationObj)) {
                            continue;
                        }

                        if (!empty($multiple_search) && !empty($product['match_source']) && $product['match_source'] == 'combination') {
                            if (stripos($attribute['attribute_name'], $multiple_search) === false 
                            && stripos($combinationObj->reference, $multiple_search) === false 
                            && stripos($combinationObj->ean13, $multiple_search) === false 
                            && stripos($combinationObj->upc, $multiple_search) === false) {
                                continue;
                            }
                        }

                        $quantity_discounts = SpecificPrice::getQuantityDiscounts(
                            $product['id_product'],
                            $quotation->id_shop,
                            $quotation->id_currency,
                            $address->id_country,
                            $id_group,
                            $attribute['id_product_attribute'],
                            false,
                            (int) $quotation->id_customer
                        );
                        
                        if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                            $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                        }
                        $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'] . ' - ';
                        $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                        $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                        $combinations[$attribute['id_product_attribute']]['wholesale_price'] = $combinationObj->wholesale_price;
                        $combinations[$attribute['id_product_attribute']]['minimal_quantity'] = $attribute['minimal_quantity'];
                        $combinations[$attribute['id_product_attribute']]['wholesale_price_formatted'] = Tools::ps_round(
                            Tools::convertPrice($combinationObj->wholesale_price, $currency),
                            6
                        );
                        $combinations[$attribute['id_product_attribute']]['has_volume_discount'] = count($quantity_discounts);
                        if (count($quantity_discounts)) {
                            $has_volume_discount = true;
                            $combinations[$attribute['id_product_attribute']]['volume_discount_from'] = $quantity_discounts[0]['from_quantity'];
                        }

                        if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                            $price_tax_incl = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                $attribute['id_product_attribute'],
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                true,
                                6,
                                false,
                                false,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );
                            $price_tax_incl_with_reduction = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                0,
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                true,
                                6,
                                false,
                                true,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );
                            $price_tax_incl_reduction = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                0,
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                true,
                                6,
                                true,
                                true,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );

                            $price_tax_excl = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                $attribute['id_product_attribute'],
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                false,
                                6,
                                false,
                                false,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );
                            $price_tax_excl_with_reduction = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                0,
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                false,
                                6,
                                false,
                                true,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );
                            $price_tax_excl_reduction = Product::priceCalculation(
                                $quotation->id_shop,
                                $product['id_product'],
                                0,
                                $address->id_country,
                                $address->id_state,
                                $address->postcode,
                                $quotation->id_currency,
                                $id_group,
                                1,
                                false,
                                6,
                                true,
                                true,
                                true,
                                $specific_price,
                                !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                                $id_customer,
                                true,
                                null,
                                1
                            );

                            $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = $price_tax_incl;
                            $combinations[$attribute['id_product_attribute']]['price_tax_incl_formatted'] = $price_tax_incl;
                            $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = $price_tax_excl;
                            $combinations[$attribute['id_product_attribute']]['price_tax_excl_formatted'] = $price_tax_excl;
                        }
                        if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                            $combinations[
                            $attribute['id_product_attribute']
                            ]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                                (int) $product['id_product'],
                                $attribute['id_product_attribute'],
                                (int) $this->context->shop->id
                            );
                        }

                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                            (int) $product['advanced_stock_management'] == 1) {
                            $product['warehouse_list'][
                            $attribute['id_product_attribute']
                            ] = Warehouse::getProductWarehouseList(
                                $product['id_product'],
                                $attribute['id_product_attribute']
                            );
                        } else {
                            $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                        }

                        $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity(
                            $product['id_product'],
                            $attribute['id_product_attribute']
                        );
                        if ($key == 0) {
                            $first_combination = $combinations[$attribute['id_product_attribute']];
                        }
                    }

                    $product['has_volume_discount'] = $has_volume_discount;
                    if (count($quantity_discounts)) {
                        $product['volume_discount_from'] = $quantity_discounts[0]['from_quantity'];
                    }

                    if (!$first_combination && 1 === count($combinations) && $product['match_source'] == 'combination') {
                        $first_combination = reset($combinations);
                    }

                    if ($first_combination) {
                        $product['price_tax_excl'] = $first_combination['price_tax_excl'];
                        $product['price_tax_incl'] = $first_combination['price_tax_incl'];
                        $product['minimal_quantity'] = $first_combination['minimal_quantity'];
                        $product['quantity'] = $first_combination['qty_in_stock'];
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                        (int) $product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                    } else {
                        $product['warehouse_list'][0] = array();
                    }

                    $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct(
                        (int) $product['id_product'],
                        0,
                        (int) $this->context->shop->id
                    );

                    foreach ($combinations as &$combination) {
                        $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                    }
                    $product['combinations'] = $combinations;

                    if ($product['customizable']) {
                        $product_instance = new Product((int) $product['id_product']);
                        $product['customization_fields'] = $product_instance->getCustomizationFields(
                            $this->context->language->id
                        );
                    }
                }

                $tpl = $this->context->smarty->createTemplate(
                    $this->getTemplatePath(
                        'quotationview_addproducts_modal.tpl'
                    ) . 'quotationview_addproducts_modal.tpl'
                );
                $tpl->assign(
                    array(
                        'currency' => new Currency($quotation->id_currency),
                        'display_tax' => (int) $quotation->calculate_taxes,
                        'products' => $results['products'],
                        'total_results' => $results['total_results'],
                    )
                );
                $view = $tpl->fetch();
                die(json_encode(array(
                    'result' => 1,
                    'view' => $view,
                    'pages' => $results['pages'],
                    'page_number' => (int) $page_number,
                    'total_results' => $results['total_results'],
                )));
            } else {
                die(json_encode(array(
                    'result' => true,
                    'products' => array(),
                )));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => false,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessAddProductToQuotation()
    {
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )));
            }
            $id_product = Tools::getValue('id_product');
            $product = new Product($id_product, false, $quotation->id_lang);
            if (!Validate::isLoadedObject($product)) {
                die(json_encode(array(
                    'result' => 0,
                    'error' => Tools::displayError('The product object cannot be loaded.'),
                )));
            }
            $id_product_attribute = Tools::getValue('id_product_attribute');
            $combination = null;
            if (isset($id_product_attribute) && $id_product_attribute) {
                $combination = new Combination($id_product_attribute);
                if (!Validate::isLoadedObject($combination)) {
                    die(json_encode(array(
                        'result' => 0,
                        'error' => Tools::displayError('The combination object cannot be loaded.'),
                    )));
                }
            }

            $retail_price = Tools::getValue('retail_price');
            if (isset($this->id_customer) && $this->id_customer) {
                $id_group = (int) Customer::getDefaultGroupId($this->id_customer);
            } else {
                $id_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
            }

            $qty = Tools::getValue('product_quantity');
            $comment = Tools::getValue('product_comment');

            $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                (int) $id_product,
                Context::getContext()
            );
            $product_tax_calculator = TaxManagerFactory::getManager(
                $quotation->getTaxAddress(),
                $id_tax_rules_group
            )->getTaxCalculator();
            $tax_rate = $product_tax_calculator->getTotalRate();

            try {
                $quotation->addProduct(
                    $id_product,
                    $id_product_attribute,
                    $retail_price,
                    $qty,
                    $comment,
                    $id_group,
                    array(),
                    $id_tax_rules_group,
                    $tax_rate
                );
            } catch (Exception $e) {
                die(json_encode(
                    array(
                        'result' => 0,
                        'view' => $this->_getQuotationHTML($quotation),
                        'error' => Tools::displayError('Unable to add product to quotation'),
                    )
                ));
            }

            if (!$quotation->save()) {
                die(json_encode(
                    array(
                        'result' => 0,
                        'view' => $this->_getQuotationHTML($quotation),
                        'error' => Tools::displayError('Unable to save quotation'),
                    )
                ));
            }
            die(json_encode(array(
                'redirect' => $this->context->link->getAdminLink('AdminQuotationsPro', true) .
                    '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                'result' => 1,
                'response' => $this->l('Product Line Added', 'AdminQuotationsPro'),
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );

            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessDeleteProductOnQuotation - Delete a product line on a quotation.
     *
     * @return json
     *
     */
    public function ajaxProcessDeleteProductOnQuotation()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->l('No quotation id provided.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation_product'))) > 0) {
                $validationErrors[] = $this->l('No quotation product id provided.', 'AdminQuotationsPro');
            }

            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            $quotation_product = new QuotationProduct((int) Tools::getValue('id_roja45_quotation_product'));
            if (!Validate::isLoadedObject($quotation_product)) {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'error' => $this->l('The quotation could not be loaded.', 'AdminQuotationsPro'),
                    )
                ));
            }

            if (!$quotation->deleteProduct($quotation_product)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'view' => $this->_getQuotationHTML($quotation),
                        'error' => Tools::displayError('Unable to delete product from quotation'),
                    )
                ));
            }

            if (!$quotation->save()) {
                die(json_encode(
                    array(
                        'result' => false,
                        'view' => $this->_getQuotationHTML($quotation),
                        'error' => Tools::displayError('Unable to save quotation'),
                    )
                ));
            }

            die(json_encode(array(
                'redirect' => $this->context->link->getAdminLink(
                        'AdminQuotationsPro',
                        true
                    ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                'result' => 'success',
                'response' => $this->l('Success', 'AdminQuotationsPro'),
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );

            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessDeleteProductOnQuotation - Delete a product line on a quotation.
     *
     * @return json
     *
     */
    public function ajaxProcessDeleteProductsOnQuotation()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->l('No quotation id.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation_product'))) > 0) {
                $validationErrors[] = $this->l('No quotation product id.', 'AdminQuotationsPro');
            }

            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));

            foreach (Tools::getValue('product_ids') as $id_roja45_quotation_product) {
                $quotation_product = new QuotationProduct($id_roja45_quotation_product);
                if (!Validate::isLoadedObject($quotation_product)) {
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'error' => $this->l('The quotation could not be loaded.', 'AdminQuotationsPro'),
                        )
                    ));
                }
                if (!$quotation->deleteProduct($quotation_product)) {
                    die(json_encode(
                        array(
                            'result' => false,
                            'view' => $this->_getQuotationHTML($quotation),
                            'error' => Tools::displayError('Unable to delete product from quotation'),
                        )
                    ));
                }
            }
            if (!$quotation->save()) {
                die(json_encode(
                    array(
                        'result' => false,
                        'view' => $this->_getQuotationHTML($quotation),
                        'error' => Tools::displayError('Unable to save quotation'),
                    )
                ));
            }

            die(json_encode(array(
                'redirect' => $this->context->link->getAdminLink(
                        'AdminQuotationsPro',
                        true
                    ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                'result' => 'success',
                'response' => $this->l('Success', 'AdminQuotationsPro'),
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessLoadQuotationProduct - Retrieve a product line on a quotation.
     *
     * @return json
     *
     */
    public function ajaxProcessLoadQuotationProduct()
    {
        $quotation_product = new QuotationProduct(Tools::getValue('id_roja45_quotation_product'));
        if (!Validate::isLoadedObject($quotation_product)) {
            die(json_encode(
                array(
                    'result' => 'error',
                    'error' => $this->l('The quotation product object could not be loaded.', 'AdminQuotationsPro'),
                )
            ));
        }

        $product = new Product($quotation_product->id_product);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(
                array(
                    'result' => 'error',
                    'error' => $this->l('The product object cannot be loaded.', 'AdminQuotationsPro'),
                )
            ));
        }

        die(json_encode(
            array(
                'result' => 'success',
                'product' => $product,
                'price_tax_incl' => Product::getPriceStatic(
                    $product->id,
                    true,
                    $quotation_product->id_product_attribute,
                    Roja45QuotationsPro::DEFAULT_PRECISION
                ),
                'price_tax_excl' => Product::getPriceStatic(
                    $product->id,
                    false,
                    $quotation_product->id_product_attribute,
                    Roja45QuotationsPro::DEFAULT_PRECISION
                ),
            )
        ));
    }

    /**
     * ajaxProcesUpdateQuotationProduct - Retrieve a product line on a quotation.
     *
     * @return json
     *
     */
    public function ajaxProcessUpdateQuotationProducts()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                throw new Exception($this->l('The quotation could not be loaded.', 'AdminQuotationsPro'));
            }

            $currency = new Currency($quotation->id_currency);
            if ($products = Tools::getValue('products')) {
                foreach ($products as &$product) {
                    //if ($product['product_changed']) {
                    $quotation_product = new QuotationProduct($product['id_roja45_quotation_product']);
                    if (!Validate::isLoadedObject($quotation_product)) {
                        throw new Exception($this->l('The quotation product could not be loaded.', 'AdminQuotationsPro'));
                    }

                    $product_quantity = 0;
                    if (is_array($product['product_quotation_quantity'])) {
                        foreach ($product['product_quotation_quantity'] as $id_customization => $qty) {
                            // Update quantity of each customization
                            Db::getInstance()->update(
                                'customization',
                                array('quantity' => (int) $qty),
                                'id_customization = ' . (int) $id_customization
                            );
                            $product_quantity += $qty;
                        }
                    } else {
                        $product_quantity = (int) $product['product_quotation_quantity'];
                    }

                    //$current_price = ($quotation->calculate_taxes) ?
                    //$quotation_product->unit_price_tax_incl : $quotation_product->unit_price_tax_excl;

                    $quote_price_changed = false;
                    $discount_changed = false;
                    $customization_cost_changed = false;
                    $comment_changed = false;
                    $qty_changed = false;
                    $subtotal_changed = false;

                    if (is_numeric($product['product_price']) && ($product['product_price'] != $quotation_product->unit_price_tax_excl)) {
                        $quote_price_changed = true;
                    }
                    if (is_numeric($product['product_discount']) && ($product['product_discount'] != $quotation_product->discount)) {
                        $discount_changed = true;
                    }
                    if (is_numeric($product['product_quotation_quantity']) != $quotation_product->qty) {
                        $qty_changed = true;
                    }
                    if ($product['product_comment'] != $quotation_product->comment) {
                        $comment_changed = true;
                    }
                    if (isset($product['product_quotation_customization_cost']) && ($product['product_quotation_customization_cost'] != $quotation_product->customization_cost_exc)) {
                        $customization_cost_changed = true;
                    } else {
                        $product['product_quotation_customization_cost'] = 0;
                    }

                    if (is_numeric($product['product_price_subtotal_excl']) && ($product['product_price_subtotal_excl'] != $product['product_price_subtotal'])) {
                        $subtotal_changed = true;
                    }

                    if ($quotation->id_currency !== $this->context->currency->id) {
                        $product['list_price'] = Tools::ps_round(
                            Tools::convertPrice(
                                $product['list_price'],
                                $currency,
                                true
                            ),
                            6
                        );
                    }

                    $product['product_quotation_customization_cost_total_exc'] = 0;
                    $product['product_quotation_customization_cost_total_inc'] = 0;
                    if (!$quote_price_changed && !$discount_changed && !$qty_changed && !$comment_changed && !$customization_cost_changed && !$subtotal_changed) {
                        $product['product_discount'] = $quotation_product->discount;
                        $product['product_price'] = $quotation_product->unit_price_tax_excl;
                        $product['product_quotation_quantity'] = $quotation_product->qty;
                        $product['product_comment'] = $quotation_product->comment;
                        $product['product_quotation_customization_cost'] = $quotation_product->customization_cost_exc;
                    } else {
                        if ($subtotal_changed) {
                            $discounted_price = ($product['product_price_subtotal_excl'] - ($product['product_quotation_customization_cost'] * $product_quantity)) / $product_quantity;
                        } else {
                            $discounted_price = RojaFortyFiveQuotationsProCore::getCurrencyValue(
                                $product['product_price']
                            );
                            if ($discount_changed) {
                                if ($product['product_quotation_discount_type'] == 'percentage') {
                                    $discounted_price = $product['list_price'] * ((100 - $product['product_discount']) / 100);
                                } else {
                                    $discounted_price = $product['list_price'] - $product['product_discount'];
                                }
                                if ($discounted_price < 0) {
                                    $discounted_price = 0;
                                }
                                $discount_changed = true;
                            }
                        }

                        if ($comment_changed) {
                            $quotation_product->comment = $product['product_comment'];
                        }
                        if ((int) Configuration::get('PS_CURRENCY_DEFAULT') == $quotation->id_currency) {
                            $c_rate = 1.0;
                        } else {
                            $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);
                        }

                        $price_exc = $discounted_price;
                        $price_exc /= $c_rate;
                        $price_inc = Tools::ps_round(
                            $quotation->getPriceWithTax(
                                $quotation_product->id_product,
                                $price_exc,
                                $this->context
                            ),
                            6
                        );
                        $product['product_price'] = $price_exc;

                        $product['product_quotation_customization_cost_total_exc'] = 0;
                        $product['product_quotation_customization_cost_total_inc'] = 0;
                        if (isset($product['product_quotation_customization_cost']) && $product['product_quotation_customization_cost']) {
                            $customization_cost = $product['product_quotation_customization_cost'];

                            $customization_cost_exc = $customization_cost;
                            $customization_cost_exc /= $c_rate;
                            $customization_cost_inc = $quotation->getPriceWithTax(
                                $quotation_product->id_product,
                                $customization_cost_exc,
                                $this->context
                            );
                            $product['product_quotation_customization_cost_inc'] = $customization_cost_inc;
                            $product['product_quotation_customization_cost_exc'] = $customization_cost_exc;

                            if ($product['product_quotation_customization_cost_type'] == 1) {
                                $product['product_quotation_customization_cost_total_inc'] = $customization_cost_inc;
                                $product['product_quotation_customization_cost_total_exc'] = $customization_cost_exc;
                            } else {
                                $product['product_quotation_customization_cost_total_inc'] = $customization_cost_inc * $product_quantity;
                                $product['product_quotation_customization_cost_total_exc'] = $customization_cost_exc * $product_quantity;
                            }

                            $quotation_product->customization_cost_exc = Tools::ps_round(
                                $product['product_quotation_customization_cost_exc'],
                                6
                            );
                            $quotation_product->customization_cost_inc = Tools::ps_round(
                                $product['product_quotation_customization_cost_inc'],
                                6
                            );
                            $quotation_product->customization_cost_type = $product['product_quotation_customization_cost_type'];
                        }

                        if ($quote_price_changed) {
                            $product['product_discount'] = 0;
                            if ($product['list_price'] && ($product['product_price'] <= $product['list_price'])) {
                                if ($product['product_quotation_discount_type'] == 'percentage') {
                                    $product['product_discount'] = Tools::ps_round(
                                        (($product['list_price'] - $product['product_price']) * 100) / $product['list_price'],
                                        6
                                    );
                                } else {
                                    $product['product_discount'] = Tools::ps_round(
                                        $product['list_price'] - $product['product_price'],
                                        6
                                    );
                                }
                            }
                        }

                        if ($price_exc != (float) $quotation_product->unit_price_tax_excl) {
                            $quotation_product->custom_price = true;
                        }

                        $quotation_product->unit_price_tax_excl = Tools::ps_round(
                            $price_exc,
                            6
                        );
                        $quotation_product->unit_price_tax_incl = Tools::ps_round(
                            $price_inc,
                            6
                        );
                    }

                    $product['product_price_currency'] = Tools::ps_round(
                        Tools::convertPrice(
                            $product['product_price'],
                            $currency,
                            true
                        ),
                        6
                    );

                    $product['unit_price_tax_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $quotation_product->unit_price_tax_excl,
                                $currency,
                                true
                            ),
                            6
                        ),
                        $currency
                    );
                    $product['unit_price_tax_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $quotation_product->unit_price_tax_incl,
                                $currency,
                                true
                            ),
                            6
                        ),
                        $currency
                    );
                    $quotation_product->deposit_amount = $product['product_quotation_deposit_amount'];

                    $total_to_pay_exc = $quotation_product->unit_price_tax_excl * $product_quantity;
                    $total_to_pay_inc = $quotation_product->unit_price_tax_incl * $product_quantity;

                    $total_to_pay_exc += $product['product_quotation_customization_cost_total_exc'];
                    $total_to_pay_inc += $product['product_quotation_customization_cost_total_inc'];

                    $wholesale_price = (float) $product['wholesale_price'];
                    $wholesale_total = $wholesale_price * $product_quantity;
                    $total_profit_exc = $total_to_pay_exc - $wholesale_total;
                    //$total_profit_inc = $total_to_pay_inc - $wholesale_total;

                    $currency = new Currency($quotation->id_currency);
                    $product['profit_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $total_profit_exc,
                                $currency,
                                true
                            ),
                            6
                        ),
                        $currency
                    );

                    $product['total_to_pay_exc'] = Tools::ps_round(
                        Tools::convertPrice(
                            $total_to_pay_exc,
                            $currency,
                            true
                        ),
                        6
                    );

                    $product['total_to_pay_exc_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $total_to_pay_exc,
                                $currency,
                                true
                            ),
                            2
                        ),
                        $currency
                    );
                    $product['total_to_pay_inc'] = Tools::ps_round(
                        Tools::convertPrice(
                            $total_to_pay_inc,
                            $currency,
                            true
                        ),
                        6
                    );
                    $product['total_to_pay_inc_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $total_to_pay_inc,
                                $currency,
                                true
                            ),
                            2
                        ),
                        $currency
                    );

                    $product['total_to_pay_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                ($quotation->calculate_taxes) ?
                                    $total_to_pay_inc :
                                    $total_to_pay_exc,
                                $currency,
                                true
                            ),
                            6
                        ),
                        $currency
                    );
                    $product['total_tax_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::ps_round(
                            Tools::convertPrice(
                                $total_to_pay_inc - $total_to_pay_exc,
                                $currency,
                                true
                            ),
                            6
                        ),
                        $currency
                    );

                    $comment = $product['product_comment'];
                    $quotation_product->comment = $comment;
                    $quotation_product->qty = $product_quantity;
                    $quotation_product->discount = $product['product_discount'];
                    $quotation_product->discount_type = $product['product_quotation_discount_type'];
                    if (!$quotation_product->save()) {
                        throw new Exception(sprintf($this->l('Unable to save product update [%s]', 'AdminQuotationsPro'), Db::getInstance()->getMsgError()));
                    }
                    //  }
                }
            }

            $tpl = $this->context->smarty->createTemplate(
                $this->getTemplatePath('_quotation_totals.tpl') . '_quotation_totals.tpl'
            );
            $summary = $quotation->getSummaryDetails();
            $tpl->assign($summary);
            $tpl->assign(array(
                'deposit_enabled' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS'),
            ));
            $totals = $tpl->fetch();

            die(json_encode(
                array(
                    'result' => 1,
                    'products' => $products,
                    //'products' => $summary['quotation_products'],
                    'totals_html' => $totals,
                    'response' => $this->l('Success', 'AdminQuotationsPro'),
                )
            ));
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessSubmitResetProductPrice()
    {
        // Return value
        $res = true;
        $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
        //$quotation_product = new QuotationProduct((int)Tools::getValue('id_roja45_quotation_product'));

        $quotation->resetPrice((int) Tools::getValue('id_roja45_quotation_product'));
        $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(
            array(
                'quotation' => $quotation,
                'currency' => new Currency($quotation->id_currency),
                'current_id_lang' => Context::getContext()->language->id,
                'link' => Context::getContext()->link,
                'current_index' => self::$currentIndex,
                'display_warehouse' => (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            )
        );

        $view = $this->_getQuotationHTML($quotation);

        die(json_encode(
            array(
                'result' => $res,
                'view' => $view,
                'quotation' => $quotation,
            )
        ));
    }

    /**
     * ajaxProcessSubmitClaimQuotation - Employee claim a quote request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitClaimQuotation()
    {
        $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
        if (!Validate::isLoadedObject($quotation)) {
            die(json_encode(
                array(
                    'result' => false,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )
            ));
        }
        try {
            $context = Context::getContext();
            $quotation->id_employee = $context->employee->id;
            if ($quotation->update()) {
                die(json_encode(
                    array(
                        'result' => 1,
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    )
                ));
            } else {
                $validationErrors = array();
                $validationErrors[] = $this->l('An error occurred claiming the request.', 'AdminQuotationsPro');
                $json = json_encode(
                    array(
                        'result' => false,
                        'errors' => $validationErrors,
                    )
                );
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => false,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitReleaseQuotation - Release a claimed quote request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitReleaseQuotation()
    {
        $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
        if (!Validate::isLoadedObject($quotation)) {
            die(json_encode(
                array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )
            ));
        }
        try {
            $quotation->id_employee = '';
            $result = $quotation->save();
            if ($result) {
                die(json_encode(
                    array(
                        'result' => 1,
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    )
                ));
            } else {
                $validationErrors = array();
                $validationErrors[] = $this->l('An error occurred releasing the request.', 'AdminQuotationsPro');
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                );
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitResetCart - Release a claimed quote request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitResetCart()
    {
        $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
        if (!Validate::isLoadedObject($quotation)) {
            die(json_encode(
                array(
                    'result' => 0,
                    'error' => Tools::displayError('The quotation could not be loaded.'),
                )
            ));
        }
        try {
            $quotation->id_cart = 0;
            $result = $quotation->save();
            if ($result) {
                die(json_encode(
                    array(
                        'result' => 1,
                    )
                ));
            } else {
                $validationErrors = array();
                $validationErrors[] = $this->l('An error occurred releasing the request.', 'AdminQuotationsPro');
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                );
                die($json);
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSetCurrency - Sets the currency on the request
     *
     * @return json
     *
     */
    public function ajaxProcessSetCurrency()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_currency'))) > 0) {
                $validationErrors[] = $this->l('You must provide a currency in order to calculate the quote.', 'AdminQuotationsPro');
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => 0,
                        'error' => Tools::displayError('The quotation could not be loaded.', 'AdminQuotationsPro'),
                    )
                ));
            }

            if (!count($validationErrors)) {
                $quotation->id_currency = Tools::getValue('id_currency');
                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    $json = json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    );
                    die($json);
                }

                $view = $this->_getQuotationHTML($quotation);
                die(json_encode(
                    array(
                        'result' => 1,
                        'view' => $view,
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitSearchAccount - Sets the quotation status
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitSearchAccount()
    {
        $validationErrors = array();
        try {
            $customer_email = Tools::getValue('customer_email');
            if (!Tools::strlen(trim($customer_email)) > 0) {
                $validationErrors[] = $this->l('You must provide an email address', 'AdminQuotationsPro');
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }

            if (!count($validationErrors)) {
                $customers = Customer::getCustomersByEmail($customer_email);
                if (!$customers) {
                    $json = json_encode(
                        array(
                            'result' => 1,
                            'email' => false,
                            'response' => $this->l('No account found', 'AdminQuotationsPro'),
                        )
                    );
                    die($json);
                } elseif ($customers > 1) {
                }
                $customer = new Customer($customers[0]['id_customer']);
                $quotation->id_customer = $customer->id;
                $quotation->firstname = $customer->firstname;
                $quotation->lastname = $customer->lastname;
                $quotation->email = trim($customer->email);
                $quotation->save();
                $json = json_encode(
                    array(
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'email' => $customer->email,
                        'firstname' => $customer->firstname,
                        'lastname' => $customer->lastname,
                    )
                );
                die($json);
            } else {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitSetQuotationStatus - Sets the quotation status
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitSetQuotationStatus()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_status'))) > 0) {
                $validationErrors[] = $this->l('You must provide a status.', 'AdminQuotationsPro');
            }
            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation could not be loaded.'),
                    )
                ));
            }
            $status = new QuotationStatus(Tools::getValue('id_status'));
            if (!Validate::isLoadedObject($status)) {
                die(json_encode(
                    array(
                        'result' => false,
                        'error' => Tools::displayError('The quotation status could not be loaded.'),
                    )
                ));
            }
            if (!count($validationErrors)) {
                $template_vars = array();

                $hide_prices = (bool) Configuration::get('ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE');
                $quotation_details = $quotation->getSummaryDetails(null, null, true, $hide_prices);
                $template_vars = array_merge(
                    $template_vars,
                    $quotation_details
                );
                if (!$quotation->setStatus($status->code, $template_vars)) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    $json = json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    );
                    die($json);
                }

                //$status = new QuotationStatus(Tools::getValue('id_status'), $this->context->language->id);
                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'result' => 'success',
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSubmitSelectCustomer - Sets the quotation status
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitSelectCustomer()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_customer'))) > 0) {
                $validationErrors[] = $this->l('Invalid customer id', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
                if (!Validate::isLoadedObject($quotation)) {
                    $quotation = new RojaQuotation();
                    $quotation->id_lang = (int) $this->context->language->id;
                    $quotation->id_shop = (int) $this->context->shop->id;
                    $quotation->id_currency = (int) $this->context->currency->id;
                    $quotation->id_country = $this->context->country->id;
                    $quotation->id_employee = $this->context->employee->id;
                    $quotation->valid_days = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
                    $date = new DateTime($quotation->date_add);
                    $date->add(new DateInterval('P' . $quotation->valid_days . 'D'));
                    $quotation->expiry_date = $date->format('Y-m-d H:i:s');
                    $quotation->form_data = '';
                    $quotation->calculate_taxes = !Group::getPriceDisplayMethod(
                        Customer::getDefaultGroupId($quotation->id_customer)
                    );

                    /*$quotation->calculate_taxes = 0;
                    if (!$priceDisplay || $priceDisplay == 2) {
                    $quotation->calculate_taxes = 1;
                    }*/
                    if (!$quotation->reference) {
                        $quotation->reference = RojaQuotation::generateReference();
                    }
                    //$quotation->add();
                    $id_quotation_status = Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_' . QuotationStatus::$NWQT);
                    $status = new QuotationStatus($id_quotation_status);
                    $quotation->id_roja45_quotation_status = $status->id;
                }

                $customer = new Customer(Tools::getValue('id_customer'));
                $quotation->id_customer = Tools::getValue('id_customer');
                $quotation->id_lang = (int) $customer->id_lang;
                $quotation->email = trim($customer->email);
                $quotation->firstname = $customer->firstname;
                $quotation->lastname = $customer->lastname;

                if ($id_address = Address::getFirstCustomerAddressId($quotation->id_customer)) {
                    $quotation->id_address_invoice = $id_address;
                    $quotation->id_address_delivery = $id_address;
                    $quotation->id_address_tax = Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery' ? RojaQuotation::TAX_DELIVERY_ADDRESS : RojaQuotation::TAX_INVOICE_ADDRESS;
                    $address = new Address($id_address);
                    if (Validate::isLoadedObject($address)) {
                        $quotation->id_state = $address->id_state;
                        $quotation->id_country = $address->id_country;
                    }
                }

                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    $json = json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    );
                    die($json);
                }

                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id . '&viewroja45_quotationspro',
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessGetStates - Update country
     *
     * @return json
     *
     */
    public function ajaxProcessGetStates()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_country'))) > 0) {
                $validationErrors[] = $this->l('No country provided.', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $states = State::getStatesByIdCountry(Tools::getValue('id_country'));
                if (!$states) {
                    $states = array();
                }
                die(json_encode(
                    array(
                        'result' => 1,
                        'states' => $states,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSetCountry - Update country
     *
     * @return json
     *
     */
    public function ajaxProcessSetCountry()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_country'))) > 0) {
                $validationErrors[] = $this->l('No country provided.', 'AdminQuotationsPro');
            }

            $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
            if (!count($validationErrors)) {
                //$reload = false;
                $states = State::getStatesByIdCountry(Tools::getValue('id_country'));
                $quotation->id_country = Tools::getValue('id_country');
                $quotation->updateAllPrices();
                $quotation->save();

                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'result' => 1,
                        'states' => $states,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSetCountry - Update country
     *
     * @return json
     *
     */
    public function ajaxProcessSetField()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('name'))) > 0) {
                $validationErrors[] = $this->l('No field name provided', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('value'))) > 0) {
                $validationErrors[] = $this->l('No value provided', 'AdminQuotationsPro');
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

            if (!count($validationErrors)) {
                $name = trim(Tools::getValue('name'));
                $value = trim(Tools::getValue('value'));
                $quotation->$name = $value;
                if (!$quotation->save()) {
                    $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                    $json = json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    );
                    die($json);
                }
                $view = $this->_getQuotationHTML($quotation);
                die(json_encode(
                    array(
                        'result' => 'success',
                        'view' => $view,
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    /**
     * ajaxProcessSetState - Update request status
     *
     * @return json
     *
     */
    public function ajaxProcessSetState()
    {
        $validationErrors = array();
        if (!Tools::strlen(trim(Tools::getValue('id_state'))) > 0) {
            $validationErrors[] = $this->l('You must provide a state.', 'AdminQuotationsPro');
        }
        $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
        if (!count($validationErrors)) {
            try {
                if (Validate::isLoadedObject($quotation)) {
                    $quotation->id_state = Tools::getValue('id_state');
                    if (!$quotation->save()) {
                        $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                        $json = json_encode(
                            array(
                                'result' => 'error',
                                'errors' => $validationErrors,
                            )
                        );
                        die($json);
                    }
                }

                die(json_encode(
                    array(
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    /**
     * ajaxProcessSetLanguage- Update request status
     *
     * @return json
     *
     */
    public function ajaxProcessSetLanguage()
    {
        $validationErrors = array();
        if (!Tools::strlen(trim(Tools::getValue('id_lang'))) > 0) {
            $validationErrors[] = $this->l('You must provide a language id.', 'AdminQuotationsPro');
        }
        $quotation = new RojaQuotation(Tools::getValue('id_roja45_quotation'));
        if (!count($validationErrors)) {
            try {
                $reload = false;
                if (Validate::isLoadedObject($quotation)) {
                    $quotation->id_lang = Tools::getValue('id_lang');
                    if (!$quotation->save()) {
                        $validationErrors[] = $this->l('Unable to save quotation.', 'AdminQuotationsPro');
                        $json = json_encode(
                            array(
                                'result' => 0,
                                'errors' => $validationErrors,
                            )
                        );
                        die($json);
                    }
                    $reload = $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro';
                }

                die(json_encode(
                    array(
                        'result' => 1,
                        'reload' => $reload,
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    /**
     * ajaxProcessSubmitNewVoucher - Add a voucher to the request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitAddDiscount()
    {
        $validationErrors = array();
        if (!Tools::strlen(trim(Tools::getValue('discount_name'))) > 0) {
            $validationErrors[] = $this->l('You must specify a name in order to create a new discount.', 'AdminQuotationsPro');
        }
        if (!Tools::strlen(trim(Tools::getValue('discount_type'))) > 0) {
            $validationErrors[] = $this->l('You must specify a discount type.', 'AdminQuotationsPro');
        }
        if (!Tools::strlen(trim(Tools::getValue('discount_value'))) > 0) {
            $validationErrors[] = $this->l('The discount value must be greater than 0.', 'AdminQuotationsPro');
        }
        if (!count($validationErrors)) {
            try {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
                if (!Validate::isLoadedObject($quotation)) {
                    $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $charge = new QuotationCharge();
                $charge->id_roja45_quotation = $quotation->id;
                $charge->charge_name = trim(Tools::getValue('discount_name'));
                $charge->charge_type = QuotationCharge::$DISCOUNT;
                $charge->charge_default = 0;
                $discount_value = (float) str_replace(',', '.', Tools::getValue('discount_value'));
                $charge->charge_value = $discount_value;
                if (Tools::getValue('apply_discount') == 1) {
                    $charge->specific_product = false;
                } else {
                    $charge->specific_product = true;
                    $charge->id_roja45_quotation_product = Tools::getValue('select_product');
                }

                switch (Tools::getValue('discount_type')) {
                    case 1:
                        $charge->charge_method = QuotationCharge::$PERCENTAGE;
                        if ($discount_value < 0 || $discount_value > 100) {
                            $validationErrors[] = Tools::displayError('The discount value is invalid.');
                        } else {
                            $charge->charge_amount = $discount_value;
                            $charge->charge_amount_wt = $discount_value;
                        }
                        break;
                    case 2:
                        $discount_tax = Tools::getValue('discount_tax');
                        //$discount_tax_group = Tools::getValue('discount_tax_group');

                        $quotation_total_exc = $quotation->getQuotationTotal(false, Cart::ONLY_PRODUCTS);
                        $quotation_total_inc = $quotation->getQuotationTotal(true, Cart::ONLY_PRODUCTS);


                        $currency = new Currency($quotation->id_currency);
                        if ((int) Configuration::get('PS_CURRENCY_DEFAULT') == $quotation->id_currency) {
                            $c_rate = 1.0;
                        } else {
                            $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);
                        }
                        $discount_value /= $c_rate;
                        $discount_value = Tools::ps_round(
                            $discount_value,
                            6
                        );

                        if ($discount_value > $quotation_total_exc) {
                            $validationErrors[] = Tools::displayError('The discount value is greater than the order total.');
                        }

                        $products = $quotation->getProducts();
                        $charge->charge_method = QuotationCharge::$VALUE;

                        $discount_value = min($discount_value, $discount_tax ? $quotation_total_inc : $quotation_total_exc);

                        $discountAmountTaxExcl = 0;
                        $discountAmountTaxIncl = 0;
                        foreach ($products as $product) {
                            $taxRate = 0;
                            if ($product['product_price_subtotal_excl'] != 0) {
                                $taxRate = ($product['product_price_subtotal_incl'] - $product['product_price_subtotal_excl']) / $product['product_price_subtotal_excl'];
                            }
                            $weightFactor = 0;
                            if ($discount_tax==1) {
                                if ($quotation_total_inc != 0) {
                                    $weightFactor = $product['product_price_subtotal_incl'] / $quotation_total_inc;
                                }

                                if ($weightFactor > 0) {
                                    $discountAmountTaxIncl += $discount_value * $weightFactor;
                                    // recalculate tax included
                                    $discountAmountTaxExcl += ($discount_value * $weightFactor) / (1 + $taxRate);
                                }
                            } else {
                                if ($quotation_total_exc != 0) {
                                    $weightFactor = $product['product_price_subtotal_excl'] / $quotation_total_exc;
                                }
                                $discountAmountTaxExcl += $discount_value * $weightFactor;
                                // recalculate tax excluded
                                $discountAmountTaxIncl += ($discount_value * $weightFactor) * (1 + $taxRate);
                            }
                        }

                        $charge->charge_amount_wt = Tools::ps_round(
                            $discountAmountTaxIncl,
                            6
                        );
                        // tax inc
                        $charge->charge_amount = Tools::ps_round(
                            $discountAmountTaxExcl,
                            6
                        );

                        /*$tax_manager = TaxManagerFactory::getManager($quotation->getTaxAddress(), $discount_tax_group);
                        $tax_calculator = $tax_manager->getTaxCalculator();
                        if ($discount_tax==1) {
                            $charge->charge_amount_wt = $discount_value;
                            // tax inc
                            $charge->charge_amount = Tools::ps_round(
                                $discount_value / (1 + ($tax_calculator->getTotalRate() / 100)),
                                6
                            );
                        } else {
                            $charge->charge_amount = $discount_value;
                            $charge->charge_amount = Tools::ps_round(
                                $discount_value * (1 + ($tax_calculator->getTotalRate() / 100)),
                                6
                            );
                        }*/
                        break;
                    case 3:
                        $charge->charge_method = QuotationCharge::$VALUE;
                        break;
                    default:
                        $this->errors[] = Tools::displayError('The discount type is invalid.');
                        break;
                }

                if (count($validationErrors)) {
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                // TODO - Create new QuotationCharge object and save
                if (!$charge->save()) {
                    $validationErrors[] = Tools::displayError('Unable to create discount.');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                if (!$quotation->save()) {
                    $validationErrors[] = Tools::displayError('Unable to save quotation.');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    /**
     * ajaxProcessSubmitAssignUserToQuotation - Assign a user to a quotation
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitAssignUserToQuotation()
    {
        $validationErrors = array();
        if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
            $validationErrors[] = $this->l('No quotation id provided', 'AdminQuotationsPro');
        }
        if (!Tools::strlen(trim(Tools::getValue('id_employee'))) > 0) {
            $validationErrors[] = $this->l('No employee id provided.', 'AdminQuotationsPro');
        }

        if (!count($validationErrors)) {
            try {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
                if (!Validate::isLoadedObject($quotation)) {
                    $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $quotation->id_employee = (int) Tools::getValue('id_employee');
                $quotation->save();
                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ),
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    /**
     * ajaxProcessSubmitDeleteVoucher - Delete voucher on a request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitDeleteVoucher()
    {
        $validationErrors = array();
        $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
        if (!Validate::isLoadedObject($quotation)) {
            $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }

        $quotation_charge = new QuotationCharge((int) Tools::getValue('id_roja45_quotation_charge'));
        if (!Validate::isLoadedObject($quotation_charge)) {
            $validationErrors[] = $this->l('The quotation charge cannot be loaded.', 'AdminQuotationsPro');
            die(json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )
            ));
        }

        if ($quotation_charge->delete()) {
            // TODO - check for any vouchers for this quote and delete them
            $sql = '
                SELECT id_cart_rule
                FROM ' . _DB_PREFIX_ . 'cart_rule
                WHERE code like "%' . $quotation->reference . '%"';
            $results = Db::getInstance()->executeS($sql);
            foreach ($results as $row) {
                $cart_rule = new CartRule($row['id_cart_rule']);
                $cart_rule->delete();
            }
            die(json_encode(
                array(
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    'result' => 1,
                    'response' => $this->l('Success', 'AdminQuotationsPro'),
                )
            ));
        }
    }

    /**
     * ajaxProcessSubmitEnableTaxes - Enable taxes on a request
     *
     * @return json
     *
     */
    public function ajaxProcessSubmitEnableTaxes()
    {
        $validationErrors = array();

        if (!Tools::strlen(trim(Tools::getValue('enable_taxes'))) > 0) {
            $validationErrors[] = $this->l('You must specify whether to enable taxes.', 'AdminQuotationsPro');
        }

        if (!count($validationErrors)) {
            $res = true;

            try {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
                $quotation->calculate_taxes = (int) Tools::getValue('enable_taxes');
                $quotation->update();

                die(json_encode(
                    array(
                        'result' => $res,
                        'view' => $this->_getQuotationHTML($quotation),
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                PrestaShopLogger::addLog(
                    'Roja45: ' .
                    $e->getMessage() .
                    ' : ' .
                    $e->getTraceAsString(),
                    3,
                    null,
                    'AdminQuotationsPro'
                );
                $json = json_encode(
                    array(
                        'result' => false,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => false,
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    public function ajaxProcessSubmitShippingCharge()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('id_roja45_quotation'))) > 0) {
                $validationErrors[] = $this->l('No quotation id available.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('carriers'))) > 0) {
                $validationErrors[] = $this->l('No carrier id is available.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('charge_name'))) > 0) {
                $validationErrors[] = $this->l('No carrier name is available.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('charge_value'))) > 0) {
                $validationErrors[] = $this->l('No carrier value is available.', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
                if (!Validate::isLoadedObject($quotation)) {
                    $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }

                if ($default_shipping = (int) Tools::getValue('charge_default')) {
                    if ($results = QuotationCharge::getAllCharges($quotation->id)) {
                        foreach ($results as $row) {
                            $shipping_charge = new QuotationCharge($row['id_roja45_quotation_charge']);
                            $shipping_charge->charge_default = 0;
                            $shipping_charge->save();
                        }
                    }
                }
                $carrier = new Carrier(Tools::getValue('carriers'));
                //$charges = $this->getCarrierCharge($carrier->id, $quotation);
                $charge_value = (float) trim(Tools::getValue('charge_value'));
                $charge_handling = (float) trim(Tools::getValue('charge_handling'));

                $currency = new Currency($quotation->id_currency);
                $address = $quotation->getTaxAddress();
                $carrier_tax = $carrier->getTaxesRate($address);

                // save shipping cost as new charge
                $charge = new QuotationCharge();
                $charge->id_roja45_quotation = $quotation->id;
                $charge->charge_name = trim(Tools::getValue('charge_name'));
                //if ($charge_value = trim(Tools::getValue('charge_value'))) {
                //    $charges['shipping'] = $charge_value;
                //}

                $charge->charge_type = QuotationCharge::$SHIPPING;
                $charge->charge_method = QuotationCharge::$VALUE;
                $charge->charge_default = $default_shipping;
                $charge->id_carrier = (int) $carrier->id;

                if ((int) Configuration::get('PS_CURRENCY_DEFAULT') == $quotation->id_currency) {
                    $c_rate = 1.0;
                } else {
                    $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);
                }

                //$charges['shipping'] /= $c_rate;
                $charge_value /= $c_rate;
                $charge->charge_amount = Tools::ps_round(
                    $charge_value,
                    6
                );
                $charge->charge_amount_wt = Tools::ps_round(
                    $charge_value * (1 + ($carrier_tax / 100)),
                    6
                );

                $charge_handling /= $c_rate;
                $charge_handling = Tools::ps_round(
                    $charge_handling,
                    6
                );

                if ($carrier->shipping_handling && Configuration::get('PS_SHIPPING_HANDLING') > 0) {
                    $charge->charge_handling = $charge_handling;
                    $charge->charge_handling_wt = $charge_handling * (1 + ($carrier_tax / 100));
                }

                if (!$charge->save()) {
                    $validationErrors[] = Tools::displayError('Unable to save charge.');
                    $validationErrors[] = Db::getInstance()->getMsgError();
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }

                if ($charge->charge_default) {
                    $quotation->id_carrier = (int) Tools::getValue('carriers');
                }

                if (!$quotation->save()) {
                    $validationErrors[] = Tools::displayError('Unable to save quotation.');
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }

                die(json_encode(
                    array(
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                        'result' => 'success',
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                    )
                ));
            } else {
                die(json_encode(array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                )));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 'error',
                'errors' => $validationErrors,
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function ajaxProcessSubmitNewCharge()
    {
        $validationErrors = array();
        try {
            if (!Tools::strlen(trim(Tools::getValue('charge_name'))) > 0) {
                $validationErrors[] = $this->l('No name provided for this charge.', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('charge_value'))) > 0) {
                $validationErrors[] = $this->l('No value provided for this charge', 'AdminQuotationsPro');
            }
            if (!Tools::strlen(trim(Tools::getValue('charge_method'))) > 0) {
                $validationErrors[] = $this->l('No method provided for this charge.', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
                if (!Validate::isLoadedObject($quotation)) {
                    $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                    die(json_encode(array(
                        'result' => false,
                        'errors' => $validationErrors,
                    )));
                }

                $charge = new QuotationCharge();
                $charge->id_roja45_quotation = $quotation->id;
                $charge->charge_name = trim(Tools::getValue('charge_name'));
                switch (Tools::getValue('charge_type')) {
                    case 'general':
                        $charge->charge_type = QuotationCharge::$CHARGE;
                        break;
                    case 'shipping':
                        $charge->charge_type = QuotationCharge::$SHIPPING;
                        break;
                    case 'handling':
                        $charge->charge_type = QuotationCharge::$HANDLING;
                        break;
                }

                $charge_value = (float) str_replace(',', '.', Tools::getValue('charge_value'));
                switch (Tools::getValue('charge_method')) {
                    case 1:
                        $charge->charge_method = QuotationCharge::$PERCENTAGE;
                        if ($charge_value < 100) {
                            $charge->charge_amount = Tools::ps_round(
                                $quotation->total_to_pay * $charge_value / 100,
                                6
                            );
                            $charge->charge_amount_wt = Tools::ps_round(
                                $quotation->total_to_pay_wt * $charge_value / 100,
                                6
                            );
                        } else {
                            $validationErrors[] = Tools::displayError('The discount value is invalid.');
                        }
                        break;
                    // Amount type
                    case 2:
                        $charge->charge_method = QuotationCharge::$VALUE;
                        if ($charge_value < 0) {
                            $validationErrors[] = Tools::displayError('Your charge should not be negative');
                        } else {
                            $charge->charge_amount = Tools::ps_round($charge_value, 6);
                            $charge->charge_amount_wt = Tools::ps_round(
                                $charge_value * (1 + ($quotation->getTaxesAverage() / 100)),
                                6
                            );
                        }
                        break;
                    default:
                        $validationErrors[] = Tools::displayError('The charge type is invalid.');
                        break;
                }

                if (count($validationErrors)) {
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }

                // TODO - Create new QuotationCharge object and save
                if (!$charge->save()) {
                    $validationErrors[] = Tools::displayError('Unable to create charge. ');
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }
                $quotation->total_charges += $charge->charge_amount;
                $quotation->total_charges_wt += $charge->charge_amount_wt;

                if (!$quotation->save()) {
                    $validationErrors[] = Tools::displayError('Unable to save quotation.');
                    die(json_encode(
                        array(
                            'result' => 'error',
                            'errors' => $validationErrors,
                        )
                    ));
                }

                die(json_encode(
                    array(
                        'result' => 'success',
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessSubmitDeleteCharge()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }

            $charge = new QuotationCharge((int) Tools::getValue('id_roja45_quotation_charge'));
            if (!Validate::isLoadedObject($charge)) {
                $validationErrors[] = $this->l('The quotation charge cannot be loaded.', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }

            if (!$charge->delete()) {
                $validationErrors[] = $this->l('The quotation charge could not be deleted', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }

            die(json_encode(
                array(
                    'result' => 1,
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                )
            ));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessSubmitSaveCharge()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }

            $charge = new QuotationCharge((int) Tools::getValue('id_roja45_quotation_charge'));
            if (!Validate::isLoadedObject($charge)) {
                $validationErrors[] = $this->l('The quotation charge cannot be loaded.', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }
            $carrier = new Carrier((int) Tools::getValue('id_carrier'));

            $currency = new Currency($quotation->id_currency);
            $charge_amount = Tools::getValue('charge_amount');
            if ((int) Configuration::get('PS_CURRENCY_DEFAULT') == $quotation->id_currency) {
                $c_rate = 1.0;
            } else {
                $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);
            }
            $charge_amount /= $c_rate;

            $tax_rate = $carrier->getTaxesRate($quotation->getTaxAddress());
            if ($quotation->calculate_taxes) {
                $charge_amount_wt = Tools::ps_round(
                    (float) $charge_amount,
                    6
                );
                $charge_amount = Tools::ps_round(
                    (float) $charge_amount_wt / (1 + ($tax_rate / 100)),
                    6
                );
            } else {
                $charge_amount = Tools::ps_round(
                    (float) $charge_amount,
                    6
                );
                $charge_amount_wt = Tools::ps_round(
                    (float) $charge_amount * (1 + ($tax_rate / 100)),
                    6
                );
            }
            $charge->charge_amount = $charge_amount;
            $charge->charge_amount_wt = $charge_amount_wt;

            if (!$charge->save()) {
                $validationErrors[] = $this->l('The quotation charge could not be saved', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }

            die(json_encode(
                array(
                    'result' => 1,
                    'redirect' => $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                )
            ));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(
                array(
                    'result' => 0,
                    'errors' => $validationErrors,
                    'exception' => $e,
                )
            );
            die($json);
        }
    }

    public function ajaxProcessSubmitQuotationNote()
    {
        $validationErrors = array();
        if (!Tools::strlen(trim(Tools::getValue('note'))) > 0) {
            $validationErrors[] = $this->l('You must provide a note.', 'AdminQuotationsPro');
        }

        if (!count($validationErrors)) {
            try {
                $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));

                if (!Validate::isLoadedObject($quotation)) {
                    $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                $note = new QuotationNote();
                $note->id_roja45_quotation = $quotation->id;
                $note->note = trim(Tools::getValue('note'));
                $note->added = date('Y-m-d H:i:s');

                if (count($validationErrors)) {
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                if (!$note->add()) {
                    $validationErrors[] = Tools::displayError('Unable to create charge. ');
                    die(json_encode(
                        array(
                            'result' => 0,
                            'errors' => $validationErrors,
                        )
                    ));
                }

                die(json_encode(
                    array(
                        'result' => 1,
                        'response' => $this->l('Success', 'AdminQuotationsPro'),
                        'redirect' => $this->context->link->getAdminLink(
                                'AdminQuotationsPro',
                                true
                            ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                    )
                ));
            } catch (Exception $e) {
                $validationErrors = array();
                $validationErrors[] = $e->getMessage();
                $json = json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                        'exception' => $e,
                    )
                );
                die($json);
            }
        } else {
            die(json_encode(
                array(
                    'result' => 'error',
                    'errors' => $validationErrors,
                )
            ));
        }
    }

    public function ajaxProcessDeleteQuotationNote()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }

            $note = new QuotationNote((int) Tools::getValue('id_roja45_quotation_note'));
            if (!Validate::isLoadedObject($note)) {
                $validationErrors[] = $this->l('The quotation note cannot be loaded.', 'AdminQuotationsPro');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }
            // TODO - Create new QuotationCharge object and save
            if (!$note->delete()) {
                $validationErrors[] = Tools::displayError('Unable to delete note. ');
                die(json_encode(array(
                    'result' => 0,
                    'errors' => $validationErrors,
                )));
            }

            die(json_encode(array(
                'result' => 1,
                'response' => $this->l('Success', 'AdminQuotationsPro'),
                'redirect' => $this->context->link->getAdminLink(
                        'AdminQuotationsPro',
                        true
                    ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
            )));
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function ajaxProcessSubmitSendQuotationForm()
    {
        $validationErrors = array();
        try {
            ob_start();
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }
            if (!count($validationErrors)) {
                $contacts = Contact::getContacts($this->context->language->id);
                if (!count($contacts)) {
                    throw new Exception('No customer service accounts available.');
                }

                foreach ($contacts as $contact) {
                    if (strpos(Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL'), $contact['email']) !== false) {
                        $id_contact = $contact['id_contact'];
                    }
                }

                if (!isset($id_contact)) { // if not use the default contact category
                    $id_contact = $contacts[0]['id_contact'];
                }

                if (!$id_contact) {
                    throw new Exception('Unable to find a customer service account for the message thread.');
                }
                if ($quotation_message = $quotation->getQuotationMessageList()) {
                    if (count($quotation_message)) {
                        $ct = new CustomerThread($quotation_message[0]['id_customer_thread']);
                    }
                }

                $id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType(QuotationStatus::$SENT);
                $status = new QuotationStatus($id_roja45_quotation_status);

                if ($status->send_email) {
                    $id_thread = '';
                    if (!isset($ct) || !$ct->id) {
                        $ct = new CustomerThread();
                        if (isset($quotation->id_customer)) { //if mail is owned by a customer assign to him
                            $ct->id_customer = $quotation->id_customer;
                        }
                        $ct->email = $quotation->email;
                        $ct->id_contact = $id_contact;
                        $ct->id_lang = (int)$quotation->id_lang;
                        $ct->id_shop = $quotation->id_shop;
                        $ct->status = 'open';
                        $ct->token = $quotation->reference;
                        $ct->save();

                        $quotation_message = new QuotationMessage();
                        $quotation_message->id_roja45_quotation = (int)$quotation->id_roja45_quotation;
                        $quotation_message->id_customer_thread = (int)$ct->id;
                        if (!($quotation_message->add())) {
                            throw new Exception($this->l('Unable to create quotation message entry.', 'AdminQuotationsPro'));
                        }
                        $id_thread = $ct->id;
                    }

                    $file_attachments = array();
                    // get customer pdfs for status.
                    if ((int)Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
                        if ($status->customer_pdf_ids && ($customer_pdf_ids = explode(',', $status->customer_pdf_ids))) {
                            foreach ($customer_pdf_ids as $customer_pdf_id) {
                                $pdf = new QuotationAnswer($customer_pdf_id, $quotation->id_lang);
                                $name_clean = str_replace(' ', '_', $pdf->name);
                                $name_clean = strtolower($name_clean) . '_' . $customer_pdf_id;

                                $file_attachments[$name_clean]['content'] = RojaPDF::generatePDF(
                                    'CustomPdf',
                                    $quotation,
                                    false,
                                    array(
                                        'id_roja45_quotation_answer' => $customer_pdf_id,
                                    )
                                );
                                $file_attachments[$name_clean]['name'] = $pdf->name . '.pdf';
                                $file_attachments[$name_clean]['mime'] = 'application/pdf';
                            }
                        }
                    } else {
                        $file_attachments['quote']['content'] = RojaPDF::generatePDF(
                            'QuotationPdf',
                            $quotation,
                            false
                        );
                    }
                    $file_attachments['quote']['name'] = $quotation->reference . '.pdf';
                    $file_attachments['quote']['mime'] = 'application/pdf';
                    if ($document_ids = Tools::getValue('select_quotation_documents')) {
                        foreach ($document_ids as $document_id) {
                            $sql = new DbQuery();
                            $sql->select('*');
                            $sql->from('roja45_quotationspro_quotation_document', 'qd');
                            $sql->where('qd.id_roja45_quotation_document=' . (int)$document_id);
                            if ($document = Db::getInstance()->executeS($sql)) {
                                $file_attachments[$document[0]['display_name']]['content'] = Tools::file_get_contents(
                                    _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference . DIRECTORY_SEPARATOR . $document[0]['internal_name']
                                );

                                $file_attachments[$document[0]['display_name']]['name'] = $document[0]['file'];
                                $mimetype = 'application/pdf';
                                switch ($document[0]['file_type']) {
                                    case 'pdf':
                                        $mimetype = 'application/pdf';
                                        break;
                                    case 'jpg':
                                        $mimetype = 'image/jpeg';
                                        break;
                                    case 'png':
                                        $mimetype = 'image/png';
                                        break;
                                }
                                $file_attachments[$document[0]['display_name']]['mime'] = $mimetype;
                            }
                        }
                    }

                    $subject = sprintf(
                        Tools::getValue('message_subject'),
                        $quotation->reference,
                        $id_thread,
                        $quotation->reference
                    );

                    $message_html = Tools::getValue('response_content');
                    $message_txt = \Soundasleep\Html2Text::convert(
                        $message_html,
                        [
                            'ignore_errors' => true
                        ]
                    );
                    $params = array(
                        '{content_txt}' => $message_txt,
                        '{content_html}' => $message_html,
                    );

                    if (!$quotation->id_employee) {
                        if ($id_default_employee = Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE')) {
                            $quotation->id_employee = $id_default_employee;
                        } else {
                            $admin_user = Employee::getEmployeesByProfile(Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_OWNER'))[0];
                            $quotation->id_employee = $admin_user['id_employee'];
                        }

                    }
                    $cm = new CustomerMessage();
                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $message_txt;
                    $cm->id_employee = $quotation->id_employee;
                    $cm->add();

                    $bcc = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_BCC');
                    if (Tools::strlen($bcc) == 0) {
                        $bcc = null;
                    }

                    if ((int)Configuration::get(
                        'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                    )) {
                        $subject = sprintf(
                            Tools::getValue('message_subject'),
                            $quotation->reference
                        );
                        RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                            'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC',
                            $quotation->reference
                        );
                        RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                            'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT',
                            $ct->id
                        );
                    }

                    $contact = false;
                    if (Tools::getValue('ROJA45_QUOTATIONSPRO_USE_CS') == 1) {
                        $contact = new Contact(Configuration::get('ROJA45_QUOTATIONSPRO_CS_ACCOUNT'), $this->context->language->id);
                        $contact_name = $contact->name;
                        $contact_email = $contact->email;
                    } else {
                        $contacts = Contact::getContacts($this->context->language->id);
                        if (!count($contacts)) {
                            throw new Exception('No customer service account available for message thread');
                        }
                        $contact = new Contact($contacts[0]['id_contact']);
                        if (!Validate::isLoadedObject($contact)) {
                            throw new Exception('Unable to find the default customer service account.');
                        }
                        $contact_name = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_NAME');
                        $contact_email = Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL');
                    }

                    $replyTo = $contact_email;
                    $replyToName = $contact_name;
                    if (Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER')) {
                        $employee = new Employee($quotation->id_employee);
                        $replyTo = $employee->email;
                        $replyToName = $employee->firstname . ' ' . $employee->lastname;
                    }

                    $sent = $quotation->setStatus(
                        QuotationStatus::$SENT,
                        $quotation->getSummaryDetails(),
                        $file_attachments,
                        false,
                        $params,
                        $subject
                    );

                    if ($sent) {
                        $quotation->tmp_password = '';
                        $quotation->quote_sent = true;
                        $quotation->save();
                        $customer = new Customer($quotation->id_customer);
                        if (Validate::isLoadedObject($customer)) {
                            $hasAccount = 1;
                        } else {
                            $hasAccount = 0;
                        }

                        $tpl = $this->context->smarty->createTemplate(
                            $this->getTemplatePath('quotationview_buttons.tpl') . 'quotationview_buttons.tpl'
                        );
                        $tpl->assign(
                            array(
                                'languages' => $this->context->controller->getLanguages(),
                                'link' => $this->context->link,
                                'quotation' => $quotation,
                                'has_account' => $hasAccount,
                                'deleted' => 0,
                            )
                        );
                        $buttons = $tpl->fetch();

                        ob_end_clean();

                        die(json_encode(
                            array(
                                'result' => 1,
                                'buttons' => $buttons,
                                'status' => new QuotationStatus(
                                    $quotation->id_roja45_quotation_status,
                                    $this->context->language->id
                                ),
                                'redirect' => $this->context->link->getAdminLink(
                                        'AdminQuotationsPro',
                                        true
                                    ) . '&id_roja45_quotation=' . $quotation->id_roja45_quotation . '&viewroja45_quotationspro',
                                'message' => $this->l('Quotation Sent', 'AdminQuotationsPro'),
                            )
                        ));
                    } else {
                        $customer_thread_messages = $this->getMessageCustomerThreads($ct->id);
                        if (count($customer_thread_messages) == 1) {
                            $ct->delete();
                        }
                        $cm->delete();
                        PrestaShopLogger::addLog(
                            'Unable to send email to customer, please try again. If the problem persists, please contact your system administrator.',
                            1,
                            null,
                            'roja45quotationspro',
                            null,
                            true
                        );
                        throw new Exception($this->l('Unable to send email to customer, please try again. If the problem persists, please contact your system administrator.', 'AdminQuotationsPro'));
                    }
                }
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'message' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ));
            die($json);
        }
    }

    public function ajaxProcessSubmitSendMessageForm()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }

            $message_content = trim(Tools::getValue('response_content'));
            if (!Tools::strlen($message_content) > 0) {
                $validationErrors[] = $this->l('You must provide a message to send.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $contacts = Contact::getContacts($this->context->language->id);
                if (!count($contacts)) {
                    throw new Exception('No customer service accounts available');
                }

                foreach ($contacts as $contact) {
                    if (strpos(Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL'), $contact['email']) !== false) {
                        $id_contact = $contact['id_contact'];
                    }
                }

                if (!isset($id_contact)) { // if not use the default contact category
                    $id_contact = $contacts[0]['id_contact'];
                }

                if (!$id_contact) {
                    throw new Exception('Unable to find a customer service account for the message thread.');
                }

                if ($quotation_message = $quotation->getQuotationMessageList()) {
                    if (count($quotation_message)) {
                        $ct = new CustomerThread($quotation_message[0]['id_customer_thread']);
                    }
                }

                if (!isset($ct) || !$ct->id) {
                    $ct = new CustomerThread();
                    if (isset($quotation->id_customer)) {
                        $ct->id_customer = $quotation->id_customer;
                    }
                    $ct->email = $quotation->email;
                    $ct->id_contact = $id_contact;
                    $ct->id_lang = (int) $quotation->id_lang;
                    $ct->id_shop = $quotation->id_shop;
                    $ct->status = 'open';
                    $ct->token = $quotation->reference;
                    $ct->save();

                    $quotation_message = new QuotationMessage();
                    $quotation_message->id_roja45_quotation = (int) $quotation->id_roja45_quotation;
                    $quotation_message->id_customer_thread = (int) $ct->id;
                    if (!($quotation_message->add())) {
                        throw new Exception($this->l('Unable to create quotation message entry.', 'AdminQuotationsPro'));
                    }
                }

                $message_txt = \Soundasleep\Html2Text::convert(
                    $message_content,
                    [
                        'ignore_errors' => true
                    ]
                );
                $params = array(
                    '{content_txt}' => $message_txt,
                    '{content_html}' => $message_content,
                );

                if (!$quotation->id_employee) {
                    $admin_user = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_)[0];
                    $quotation->id_employee = $admin_user['id_employee'];
                }

                $cm = new CustomerMessage();
                $cm->id_customer_thread = $ct->id;
                $cm->id_employee = $quotation->id_employee;
                $cm->message = $message_txt;
                $cm->add();

                $file_attachment = null;
                if (Tools::getValue('message_template') == 'roja45_customer_quote') {
                    $file_attachment['invoice']['content'] = $quotation->generateQuotationPDF(
                        false,
                        $quotation->calculate_taxes
                    );
                    $file_attachment['invoice']['name'] = $quotation->reference;
                    $file_attachment['invoice']['mime'] = 'application/pdf';
                }

                if ($document_ids = Tools::getValue('select_quotation_documents')) {
                    foreach ($document_ids as $document_id) {
                        $sql = new DbQuery();
                        $sql->select('*');
                        $sql->from('roja45_quotationspro_quotation_document', 'qd');
                        $sql->where('qd.id_roja45_quotation_document=' . (int) $document_id);
                        if ($document = Db::getInstance()->executeS($sql)) {
                            $file_attachment[$document[0]['display_name']]['content'] = Tools::file_get_contents(
                                _PS_DOWNLOAD_DIR_ . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $document[0]['internal_name']
                            );

                            $file_attachment[$document[0]['display_name']]['name'] = $document[0]['file'];
                            switch ($document[0]['file_type']) {
                                case 'pdf':
                                    $file_attachment[$document[0]['display_name']]['mime'] = 'application/pdf';
                                default:
                                    $file_attachment[$document[0]['display_name']]['mime'] = 'application/pdf';
                            }
                        }
                    }
                }

                $bcc = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_BCC');
                if (Tools::strlen($bcc) == 0) {
                    $bcc = null;
                }

                $subject = sprintf(
                    Tools::getValue('message_subject'),
                    $quotation->reference,
                    $ct->id,
                    $quotation->reference
                );

                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC',
                    $quotation->reference
                );
                RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                    'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT',
                    $ct->id
                );
                $sent = Mail::Send(
                    (int) $this->context->language->id,
                    'message_wrapper',
                    $subject,
                    $params,
                    $quotation->email,
                    $quotation->firstname . ' ' . $quotation->lastname,
                    Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL'),
                    Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_NAME'),
                    $file_attachment,
                    null,
                    _PS_MODULE_DIR_ . 'roja45quotationspro/mails/',
                    false,
                    null,
                    $bcc,
                    null
                );

                if ($sent) {
                    $quotation->save();
                    $hasAccount = 0;
                    $customer = new Customer($quotation->id_customer);
                    if (Validate::isLoadedObject($customer)) {
                        $hasAccount = 1;
                    }
                    $tpl = $this->context->smarty->createTemplate(
                        $this->getTemplatePath('quotationview_buttons.tpl') . 'quotationview_buttons.tpl'
                    );
                    $tpl->assign(
                        array(
                            'languages' => $this->context->controller->getLanguages(),
                            'link' => $this->context->link,
                            'has_account' => $hasAccount,
                            'quotation' => $quotation,
                            'deleted' => 0,
                        )
                    );
                    $link = $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&viewroja45_quotationspro&id_roja45_quotation=' . $quotation->id;

                    $buttons = $tpl->fetch();
                    die(json_encode(
                        array(
                            'result' => 1,
                            'redirect' => $link,
                            'buttons' => $buttons,
                            'status' => new QuotationStatus(
                                $quotation->id_roja45_quotation_status,
                                $this->context->language->id
                            ),
                            'message' => $this->l('Quotation Sent.', 'AdminQuotationsPro'),
                        )
                    ));
                } else {
                    $customer_thread_messages = $this->getMessageCustomerThreads($ct->id);
                    if (count($customer_thread_messages) == 1) {
                        $ct->delete();
                    }
                    $cm->delete();
                    PrestaShopLogger::addLog(
                        'Unable to send email to customer, please try again. If the problem persists, please contact your system administrator.',
                        1,
                        null,
                        'roja45quotationspro',
                        null,
                        true
                    );
                    throw new Exception($this->l('Unable to send email to customer, please try again. If the problem persists, please contact your system administrator.', 'AdminQuotationsPro'));
                }
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'message' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function ajaxProcessUpdateQuotationRequestCounter()
    {
        $validationErrors = array();
        try {
            if (!count($validationErrors)) {
            } else {
                die(json_encode(
                    array(
                        'result' => 'error',
                        'errors' => $validationErrors,
                    )
                ));
            }
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

    public function ajaxProcessSubmitGetCarrierCharge()
    {
        $validationErrors = array();
        try {
            $quotation = new RojaQuotation((int) Tools::getValue('id_roja45_quotation'));
            if (!Validate::isLoadedObject($quotation)) {
                $validationErrors[] = $this->l('The quotation could not be loaded.', 'AdminQuotationsPro');
            }

            $id_carrier = (int) Tools::getValue('id_carrier');
            if (!$id_carrier) {
                $validationErrors[] = $this->l('No carrier id provided.', 'AdminQuotationsPro');
            }

            if (!count($validationErrors)) {
                $charges = $this->getCarrierCharge($id_carrier, $quotation);
                $currency = new Currency($quotation->id_currency);
                $shipping_cost = Tools::ps_round(
                    $charges['shipping'],
                    2
                );
                $handling_cost = Tools::ps_round(
                    $charges['handling'],
                    2
                );
                die(json_encode(
                    array(
                        'result' => 1,
                        'shipping_cost' => $shipping_cost,
                        'include_handling' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_INCLUDEHANDLING'),
                        'shipping_handling' => $handling_cost,
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function ajaxProcessUpdateNotifications()
    {
        $validationErrors = array();
        try {
            if (!count($validationErrors)) {
                $quotations = RojaQuotation::getQuotationsForStatus(QuotationStatus::$OPEN);
                foreach ($quotations as &$quotation) {
                    $quotation['difference'] = $this->dateDifference(
                        strtotime('now'),
                        strtotime($quotation['date_add'])
                    );
                    $quotation['link'] = $this->context->link->getAdminLink(
                            'AdminQuotationsPro',
                            true
                        ) . '&viewroja45_quotationspro&id_roja45_quotation=' . $quotation['id_roja45_quotation'];
                }
                $num_quotations = count($quotations);

                // TODO - Messages
                $messages = array();
                $num_messages = count($messages);
                $num_notifications = $num_quotations + $num_messages;

                $quotations_tpl = $this->context->smarty->createTemplate(
                    $this->getTemplatePath('notification_quotations.tpl') . 'notification_quotations.tpl'
                );
                $quotations_tpl->assign(
                    array(
                        'quotations' => $quotations,
                    )
                );

                die(json_encode(
                    array(
                        'result' => 1,
                        'quotations' => $quotations_tpl->fetch(),
                        'num_quotations' => $num_quotations,
                        'num_messages' => $num_messages,
                        'num_notifications' => $num_notifications,
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    public function ajaxProcessGetBrowserNotifications()
    {
        $validationErrors = array();
        try {
            if (!count($validationErrors)) {
                $lastcheck = Configuration::get(
                    'ROJA45_QUOTATIONSPRO_BROWSERNOTIFICATION_TIMESTAMP',
                    time()
                );
                $quotations = RojaQuotation::getQuotationsSince($lastcheck);
                $num_quotations = count($quotations);

                // TODO - Messages
                $messages = array();
                $num_messages = count($messages);
                $num_notifications = $num_quotations + $num_messages;
                die(json_encode(
                    array(
                        'result' => 1,
                        'num_notifications' => $num_notifications,
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        'result' => 0,
                        'errors' => $validationErrors,
                    )
                ));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            PrestaShopLogger::addLog(
                'Roja45: ' .
                $e->getMessage() .
                ' : ' .
                $e->getTraceAsString(),
                3,
                null,
                'AdminQuotationsPro'
            );
            $json = json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
                'msg' => 'Caught exception: ' . $e->getMessage() . "\n",
                'exception' => $e,
            ));
            die($json);
        }
    }

    /**
     * @param $quotation RojaQuotation
     * @return string
     */
    private function buildForm($quotation)
    {
        $status = new QuotationStatus($quotation->id_roja45_quotation_status, $this->context->language->id);

        $discounts = $quotation->getQuotationAllDiscounts();
        $charges = $quotation->getQuotationAllCharges();
        $shipping = $quotation->getQuotationShippingCharges($quotation->id_lang);
        $quotation_orders = QuotationOrder::getList($quotation->id);
        foreach ($quotation_orders as &$quotation_order) {
            $order = new Order($quotation_order['id_order']);
            $quotation_order['reference'] = $order->reference;
            $quotation_order['order_url'] = $this->context->link->getAdminLink(
                'AdminOrders',
                true,
                array(
                    'vieworder' => 1,
                    'id_order' => (int) $order->id,
                ),
                array(
                    'vieworder' => 1,
                    'id_order' => (int) $order->id,
                )
            );
        }

        $notes = $quotation->getQuotationNotesList();
        $documents = QuotationDocument::getDocuments(
            $quotation->id_lang,
            $quotation->id_shop
        );
        $quotation_documents = $quotation->getDocuments();
        $customer = new Customer($quotation->id_customer);
    
        $customer_group = new Group(
            $customer->id_default_group,
            $quotation->id_lang
        );
        $customer_group_name = $customer_group->name;
        $customer_group_discount = Group::getReductionByIdGroup($customer->id_default_group);
        /*if (is_array($customer_group_name)) {
            $customer_group_name = implode(" ", $customer_group_name);
        } else {
            $customer_group_name = null;
        }*/
        
        if (Validate::isLoadedObject($customer)) {
            $hasAccount = 1;
            //$quotation->calculate_taxes = !Group::getPriceDisplayMethod(
            //    Customer::getDefaultGroupId($quotation->id_customer)
            //);
            $addresses = $customer->getAddresses($this->context->language->id);
            if (count($addresses) > 0 && !$quotation->id_address_invoice) {
                $quotation->id_address_invoice = $addresses[0]['id_address'];
                $quotation->id_address_delivery = $addresses[0]['id_address'];
                $quotation->id_address_tax = Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery' ? RojaQuotation::TAX_DELIVERY_ADDRESS : RojaQuotation::TAX_INVOICE_ADDRESS;
                $quotation->save();
            }
        } else {
            $hasAccount = 0;
            $addresses = array();
        }

        $invoice_address = 0;
        if ((int) $quotation->id_address_invoice) {
            $invoice_address = new Address($quotation->id_address_invoice);
        }

        $delivery_address = 0;
        if ((int) $quotation->id_address_delivery) {
            $delivery_address = new Address($quotation->id_address_delivery);
        }

        $tax_address_id = $quotation->id_address_tax;

        $currencies = Currency::getCurrenciesByIdShop($this->context->shop->id);
        $employee = new Employee($quotation->id_employee);
        $lang = new Language($quotation->id_lang);
        $templates = QuotationAnswer::getTemplates();
        $templates_lang = array();
        if (array_key_exists($lang->iso_code, $templates)) {
            $templates_lang = $templates[$lang->iso_code];
        }
        $quotation_statuses = QuotationStatus::getQuotationStates($this->context->language->id);
        $carrierData = $quotation->getCarriers();

        $customer_thread_messages = array();
        if ($id_customer_thread = QuotationMessage::getCustomerThread($quotation->id)) {
            $customer_thread_messages = $this->getMessageCustomerThreads($id_customer_thread);
            foreach ($customer_thread_messages as &$message) {
                $message['file_ext'] = 'txt';
                $image_dir = '/img/' . 'roja45quotationspro' . DIRECTORY_SEPARATOR . $quotation->reference;
                if (file_exists(_PS_ROOT_DIR_ . $image_dir)) {
                    $cdir = scandir(_PS_ROOT_DIR_ . $image_dir);
                    foreach ($cdir as $key => $value) {
                        if (strpos($message['file_name'], $value) !== false) {
                            $file = $image_dir . DIRECTORY_SEPARATOR . $value;
                            if (is_file(_PS_ROOT_DIR_ . $file)) {
                                $file_details = getimagesize(_PS_ROOT_DIR_ . $file);
                                $message['file_loc'] = $file;
                                $message['file_type'] = $file_details['mime'];
                                if ($file_details['mime'] == 'image/jpeg') {
                                    $message['file_ext'] = 'jpg';
                                } elseif ($file_details['mime'] == 'image/png') {
                                    $message['file_ext'] = 'png';
                                } else {
                                    $message['file_ext'] = 'txt';
                                }
                            }
                        }
                    }
                }
            }
        }
        $unread = (int) $this->getUnreadMessageCount($id_customer_thread);

        $currency = new Currency((int) $quotation->id_currency);
        // Process request json obj into array of name value pairs, with field type
        $quotation_request = new QuotationRequest($quotation->id_request);
        $request = array();
        $old_form_data = false;
        if ($quotation_request->form_data) {
            $requestJSON = json_decode($quotation_request->form_data);
            if ($requestJSON) {
                $counter = 0;
                foreach ($requestJSON->columns as $column) {
                    $request_column = array();
                    foreach ($column->fields as $field) {
                        if (($field->name != 'FIRSTNAME') &&
                            ($field->name != 'LASTNAME') &&
                            ($field->name != 'CONTACT_EMAIL')
                        ) {
                            $request_column[$counter]['name'] = $field->name;
                            $request_column[$counter]['value'] = !empty($field->value) ? $field->value : '';
                            $request_column[$counter]['label'] = $field->label;
                            if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                            } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD')) {
                                if (isset($field->id)) {
                                    $carrier = new Carrier($field->id, $this->context->language->id);
                                } else if (isset($field->value)) {
                                    $carrier = new Carrier($field->value, $this->context->language->id);
                                }
                                $request_column[$counter]['value'] = $carrier->name;
                            } elseif (isset($field->type) && ($field->type == 'COUNTRY')) {
                                $country = null;
                                if (isset($field->id)) {
                                    $country = new Country($field->id, $this->context->language->id);
                                } else if (isset($field->value)) {
                                    $country = new Country($field->value, $this->context->language->id);
                                }
                                if ($country) {
                                    $request_column[$counter]['value'] = $country->name;
                                }
                            } elseif (isset($field->type) && ($field->type == 'STATE')) {
                                $state = null;
                                if (isset($field->id)) {
                                    $state = new State($field->id, $this->context->language->id);
                                } else if (isset($field->value)) {
                                    $state = new State($field->value, $this->context->language->id);
                                }
                                if ($state) {
                                    $request_column[$counter]['value'] = $state->name;
                                }
                            } elseif (isset($field->type) && ($field->type == 'ADDRESS_SELECTOR')) {
                                $address = new Address($field->id);
                                $address = AddressFormat::generateAddress(
                                    $address,
                                    array(),
                                    ', ',
                                    ' ',
                                    array()
                                );
                                $request_column[$counter]['value'] = $address;
                            }
                            ++$counter;
                        }
                    }
                    $request[] = $request_column;
                }
            }
        } else if ($quotation->form_data) {
            $old_form_data = true;
            if ($requestJSON = json_decode($quotation->form_data)) {
                foreach ($requestJSON as $key => $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')
                    ) {
                        $request[$key]['name'] = $field->name;
                        $request[$key]['value'] = $field->value;
                        $request[$key]['label'] = $field->label;
                        if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                        } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD')) {
                            $carrier = new Carrier($field->value, $this->context->language->id);
                            $request[$key]['value'] = $carrier->name;
                            $shipping_method = $carrier->name;
                        } elseif (isset($field->type) && ($field->type == 'COUNTRY')) {
                            $country = new Country($field->value, $this->context->language->id);
                            $request[$key]['value'] = $country->name;
                        } elseif (isset($field->type) && ($field->type == 'STATE')) {
                            $state = new State($field->value, $this->context->language->id);
                            $request[$key]['value'] = $state->name;
                        }
                    }
                }
            }
        }

        $countries = Country::getCountries($this->context->language->id, true);
        $id_country = !empty($quotation->id_country) ?
            $quotation->id_country :
            Configuration::get('PS_COUNTRY_DEFAULT');
        $states = State::getStatesByIdCountry($id_country);

        $categories = array_slice(Category::getCategories((int) $this->context->language->id, true, false), 1);
        $total_paid = 0;
        if ($quotation->id_order) {
            $order = new Order($quotation->id_order);
            $total_paid = $order->getOrdersTotalPaid();
        }
        $view = '';
        $tpl = $this->createModuleTemplate(
            '_adminHeader.tpl'
        );
        $view .= $tpl->fetch();

        $last_message = null;
        if (count($customer_thread_messages) > 0) {
            $last_message = $customer_thread_messages[0];
        }

        $tpl = $this->createModuleTemplate(
            'quotationview.tpl'
        );
        $summary = $quotation->getSummaryDetails(Context::getContext()->language->id);
        $edit_customer_url = $this->context->link->getAdminLink('AdminAddresses') . '&addaddress';

        $filename = sha1($quotation->email . $quotation->filename . $quotation->id_request);
        if (Shop::isFeatureActive()) {
            $tpl->assign(
                array(
                    'multistore_active' => 1,
                )
            );
        }

        $expiry_date_formatted = new DateTime($quotation->expiry_date);
        $expiry_date_formatted = $expiry_date_formatted->format(
            $this->context->language->date_format_full
        );
        $shop = new Shop($quotation->id_shop);
        $quotation->shop_name = $shop->name;

        $time_str = trim(substr(
            $this->context->language->date_format_full,
            strlen($this->context->language->date_format_lite),
            strlen($this->context->language->date_format_full)
        ));

        if ($payment_methods = PaymentModule::getInstalledPaymentModules()) {
            foreach ($payment_methods as &$method) {
                if ($module = Module::getInstanceById($method['id_module'])) {
                    $method['displayName'] = $module->displayName;
                } else {
                    $method['displayName'] = '';
                }
            }
        }

        $in_shop_context = (Shop::getContext() == 1) ? true : false;
        $tpl->assign($summary);
        $tpl->assign(
            array(
                'quotationspro_link' => $this->context->link->getAdminLink(
                    'AdminQuotationsPro',
                    true
                ),
                'languages' => $this->context->controller->getLanguages(),
                'link' => $this->context->link,
                'id_roja45_quotation' => isset($quotation->id) ? $quotation->id : 0,
                'payment_methods' => $payment_methods,
                'order_states' => OrderState::getOrderStates(Context::getContext()->language->id),
                'quotation' => $quotation,
                'expiry_date_formatted' => $expiry_date_formatted,
                'filename' => $filename,
                'employee' => $employee,
                'customer' => $customer,
                'addresses' => $addresses,
                'invoice_address' => $invoice_address,
                'delivery_address' => $delivery_address,
                'tax_address_id' => $tax_address_id,
                'has_account' => $hasAccount,
                'currency' => $currency,
                'notes' => $notes,
                'quotation_documents' => $quotation_documents,
                'documents' => $documents,
                'total_paid' => $total_paid,
                'carriers' => $carrierData['carriers'],
                'templates' => $templates_lang,
                'discounts' => $discounts,
                'upload_dir' => _THEME_PROD_PIC_DIR_,
                'in_shop_context' => $in_shop_context,
                'messages' => $customer_thread_messages,
                'unread' => (int) $unread,
                'last_message' => $last_message,
                'customer_message_link' => $this->context->link->getAdminLink(
                    'AdminCustomerThreads',
                    true,
                    array(
                        'viewcustomer_thread' => 1,
                        'customerThreadId' => $id_customer_thread
                    ),
                    array(
                        'viewcustomer_thread' => 1,
                        'customerThreadId' => $id_customer_thread
                    )
                ),
                'customer_group_name' => $customer_group_name,
                'customer_group_discount' => $customer_group_discount,
                'quotation_statuses' => $quotation_statuses,
                'quotation_orders' => $quotation_orders,
                'charges' => $charges,
                'shipping' => $shipping,
                'request' => $request,
                'old_form_data' => $old_form_data,
                'status' => $status,
                'currencies' => $currencies,
                'countries' => $countries,
                'categories' => $categories,
                'states' => $states,
                'lang' => $lang,
                'ROJA45_QUOTATIONSPRO_ENABLE_TAXES' => $quotation->calculate_taxes,
                'current_id_lang' => $this->context->language->id,
                'id_shop' => $quotation->id_shop,
                'current_index' => self::$currentIndex,
                'edit_customer_url' => $edit_customer_url,
                'fields_value' => $this->getFieldsValues(),
                'deleted' => ($status->id == (int) Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_DLTD')),
                //'shipping_handling' => Configuration::get('PS_SHIPPING_HANDLING'),
                'id_currency' => $quotation->id_currency,
                'roja45_quote_sent' => ($quotation->quote_sent) ? 1 : 0,
                'currency_sign' => $currency->sign,
                'currency_format' => $currency->format,
                'currency_blank' => $currency->blank,
                'has_voucher' => (count($discounts) > 0) ? 1 : 0,
                'has_charges' => (count($charges) > 0) ? 1 : 0,
                'has_shipping' => (count($charges) > 0) ? 1 : 0,
                'use_taxes' => (int) $quotation->calculate_taxes,
                'tax_rule_groups' => TaxRulesGroup::getTaxRulesGroups(),
                'deposit_enabled' => (int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS'),
                'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
                'roja45_quotations_dateformat' => RojaFortyFiveQuotationsProCore::convertDateFormat(
                    $this->context->language->date_format_lite
                ),
                'roja45_quotations_timeformat' => RojaFortyFiveQuotationsProCore::convertDateFormat(
                    $time_str
                ),
            )
        );

        $view .= $tpl->fetch();
        return $view;
    }

    private function getEmailTemplatePath($template, $id_lang)
    {
        $mail_type = Configuration::get('PS_MAIL_TYPE');
        $filetype = '';
        if ($mail_type == Mail::TYPE_BOTH || $mail_type == Mail::TYPE_TEXT) {
            $filetype = '.txt';
        }
        if ($mail_type == Mail::TYPE_BOTH || $mail_type == Mail::TYPE_HTML) {
            $filetype = '.html';
        }

        $module_name = 'roja45quotationspro';

        $iso = Language::getIsoById($id_lang);
        if (!$iso) {
            return false;
        }
        $iso_template = $iso . '/' . $template;

        $theme_path = _PS_THEME_DIR_;

        if (file_exists($theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . $filetype)) {
            $template_path = $theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . $filetype;
        } elseif (file_exists($theme_path . 'mails/' . $iso_template . $filetype)) {
            $template_path = $theme_path . 'mails/' . $iso_template . $filetype;
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/' . $module_name . '/mails/' . $iso_template . $filetype)) {
            $template_path = _PS_ROOT_DIR_ . '/modules/' . $module_name . '/mails/' . $iso_template . $filetype;
        } elseif (file_exists(_PS_ROOT_DIR_ . '/mails/' . $iso_template . $filetype)) {
            $template_path = _PS_ROOT_DIR_ . '/mails/' . $iso_template . $filetype;
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/' . $module_name . '/mails/en/' . $template . $filetype)) {
            $template_path = _PS_ROOT_DIR_ . '/modules/' . $module_name . '/mails/en/' . $template . $filetype;
        } else {
            return false;
        }

        return $template_path;
    }

    private function populateTemplate($template_vars, $template_path)
    {
        $mail_type = Configuration::get('PS_MAIL_TYPE');

        if ($mail_type == Mail::TYPE_BOTH || $mail_type == Mail::TYPE_TEXT) {
            //$template_body = strip_tags(html_entity_decode(Tools::file_get_contents($template_path), null, 'utf-8'));
            $template_body = Tools::file_get_contents($template_path);
        }

        if ($mail_type == Mail::TYPE_BOTH || $mail_type == Mail::TYPE_HTML) {
            $template_body = Tools::file_get_contents($template_path);
        }
        $body = str_replace(array_keys($template_vars), array_values($template_vars), $template_body);

        return $body;
    }

    public function getMessageCustomerThreads($id_customer_thread)
    {
        $sql = new DbQuery();
        $sql->select(
            'ct.*, cm.*, cl.name, CONCAT(e.firstname, \' \', e.lastname) employee_name,
            CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname'
        );
        $sql->from('customer_thread', 'ct');
        $sql->leftJoin(
            'customer_message',
            'cm',
            'ct.id_customer_thread = cm.id_customer_thread'
        );
        $sql->leftJoin(
            'contact_lang',
            'cl',
            'cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) Context::getContext()->language->id
        );
        $sql->leftJoin(
            'employee',
            'e',
            'e.id_employee = cm.id_employee'
        );
        $sql->leftJoin(
            'customer',
            'c',
            'IFNULL(ct.id_customer, ct.email) = IFNULL(c.id_customer, c.email)'
        );
        $sql->where('ct.id_customer_thread = ' . (int) $id_customer_thread);
        $sql->orderBy('cm.date_add DESC');
        return Db::getInstance()->executeS($sql);
    }

    public function getUnreadMessageCount($id_customer_thread)
    {
        $sql = new DbQuery();
        $sql->select(
            'cm.id_customer_message'
        );
        $sql->from('customer_message', 'cm');
        $sql->where('cm.id_customer_thread = ' . (int) $id_customer_thread);
        $sql->where('cm.read = 0');
        $sql->where('cm.private = 0');
        if ($rows = Db::getInstance()->executeS($sql)) {
            return count($rows);
        }
        return 0;
    }

    private function createCustomerAccount(
        $id_shop,
        $id_shop_group,
        $firstname,
        $lastname,
        $email,
        $tmp_password
    ) {
        $customer = new Customer();
        $customer->id_shop = $id_shop;
        $customer->id_shop_group = $id_shop_group;
        $customer->firstname = trim($firstname);
        $customer->lastname = trim($lastname);
        $customer->email = trim($email);
        $customer->passwd = RojaFortyFiveQuotationsProCore::encryptPassword($tmp_password);
        if (!$customer->save()) {
            return false;
        }
        return $customer->id;
    }

    private function createCustomerAddress(
        $id_customer,
        $alias,
        $firstname,
        $lastname,
        $address_line1,
        $address_line2,
        $address_city,
        $address_country_id,
        $address_state_id,
        $address_zip,
        $address_telephone,
        $company,
        $dni,
        $vat_number
    ) {
        $address = new Address();
        $address->id_customer = $id_customer;
        $address->alias = $alias;
        $address->firstname = $firstname;
        $address->lastname = $lastname;
        $address->address1 = $address_line1;
        $address->address2 = $address_line2;
        $address->city = $address_city;
        $address->id_country = $address_country_id;
        $address->id_state = ($address_state_id) ? $address_state_id : 0;
        $address->postcode = $address_zip;
        $address->phone = $address_telephone;
        $address->company = $company;
        $address->dni = $dni;
        $address->vat_number = $vat_number;
        if ($address->save()) {
            return $address->id;
        }
        return 0;
    }

    private function preProcessCart($quotation, $id_currency)
    {
        $id_customer = (int) $quotation->id_customer;
        $customer = new Customer((int) $id_customer);
        $this->context->customer = $customer;
        $this->context->cart = new Cart();
        $this->context->cart->recyclable = 0;
        $this->context->cart->gift = 0;

        if (!$this->context->cart->id_customer) {
            $this->context->cart->id_customer = $id_customer;
        }

        if (!$this->context->cart->secure_key) {
            $this->context->cart->secure_key = $this->context->customer->secure_key;
        }
        if (!$this->context->cart->id_shop) {
            $this->context->cart->id_shop = (int) $this->context->shop->id;
        }
        if (!$this->context->cart->id_lang) {
            $this->context->cart->id_lang = (($id_lang = (int) Tools::getValue('id_lang')) ?
                $id_lang : Configuration::get('PS_LANG_DEFAULT'));
        }
        $this->context->cart->id_currency = $id_currency;
        $this->context->cart->id_address_invoice = (int) $quotation->id_address_invoice;
        $this->context->cart->id_address_delivery = isset($quotation->id_address_delivery) ? $quotation->id_address_delivery : $quotation->id_address_invoice;

        $this->context->cart->setNoMultishipping();
        $this->context->cart->save();
        $quotation->id_cart = $this->context->cart->id;
        $quotation->save();
        $currency = new Currency((int) $this->context->cart->id_currency);
        $this->context->currency = $currency;
        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
        return 1;
    }

    private function getPackageShippingCostFromModule($cart, Carrier $carrier, $shipping_cost, $products)
    {
        if (!$carrier->shipping_external) {
            return $shipping_cost;
        }

        /** @var CarrierModule $module */
        $module = Module::getInstanceByName($carrier->external_module_name);

        if (!Validate::isLoadedObject($module)) {
            return false;
        }

        if (property_exists($module, 'id_carrier')) {
            $module->id_carrier = $carrier->id;
        }

        if (!$carrier->need_range) {
            return $module->getOrderShippingCostExternal($cart);
        }

        if (method_exists($module, 'getPackageShippingCost')) {
            $shipping_cost = $module->getPackageShippingCost($cart, $shipping_cost, $products);
        } else {
            $shipping_cost = $module->getOrderShippingCost($cart, $shipping_cost);
        }

        return $shipping_cost;
    }

    private function markDeleted($id_roja45_quotation)
    {
        $object = new RojaQuotation($id_roja45_quotation);
        if (!$object->isRemovable()) {
            $this->errors[] = $this->l('For security reasons, you cannot delete this quotation.', 'AdminQuotationsPro');
        }

        if ($object->id_cart) {
            SpecificPrice::deleteByIdCart($object->id_cart);
        }

        $charges = $object->getQuotationChargeList();
        foreach ($charges as $charge) {
            if ($charge['id_cart_rule']) {
                $rule = new CartRule($charge['id_cart_rule']);
                $rule->delete();
            }
            $chargeObj = new QuotationCharge($charge['id_roja45_quotation_charge']);
            $chargeObj->delete();
        }

        $object->setStatus(QuotationStatus::$DLTD);
        $object->save();
    }

    public function createModuleTemplate($tpl_name)
    {
        if (file_exists(_PS_THEME_DIR_ .
                'modules/' .
                $this->module->name .
                '/views/templates/admin/' .
                $tpl_name) &&
            $this->viewAccess()
        ) {
            return $this->context->smarty->createTemplate(
                _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/admin/' . $tpl_name,
                $this->context->smarty
            );
        } elseif (file_exists($this->getTemplatePath() . $this->override_folder . $tpl_name) &&
            $this->viewAccess()
        ) {
            return $this->context->smarty->createTemplate(
                $this->getTemplatePath() . $this->override_folder . $tpl_name,
                $this->context->smarty
            );
        }

        return $this->context->smarty->createTemplate($this->getTemplatePath() . $tpl_name);
    }

    private function dateDifference($date1timestamp, $date2timestamp)
    {
        $all = round(($date1timestamp - $date2timestamp) / 60);
        $d = floor($all / 1440);
        $h = floor(($all - $d * 1440) / 60);
        $m = $all - ($d * 1440) - ($h * 60);
        //Since you need just hours and mins
        $str = '';

        if ($d > 7) {
            $str .= $this->l('over a week ago', 'AdminQuotationsPro');
        } elseif ($d <= 7 && $d > 1) {
            $str .= $d . ' ' . $this->l('days ago', 'AdminQuotationsPro');
        } else {
            if ($h > 1) {
                $str .= $h . ' ' . $this->l('hours ago', 'AdminQuotationsPro');
            } else {
                if ($h == 1) {
                    $str .= $h . ' ' . $this->l('hour', 'AdminQuotationsPro') . ' ';
                }
                $str .= $m . ' ' . $this->l('minutes ago', 'AdminQuotationsPro');
            }
        }
        return $str;
    }
}
