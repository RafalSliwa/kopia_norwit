<?php
/**
 * QuotationFormsController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationFormsController
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationFormsController.
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

class QuotationFormsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotation_forms/';
        $this->tpl_folder = 'quotation_forms/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_form';
        $this->identifier = 'id_quotation_form';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'QuotationForm';
        $this->action = 'QuotationForm';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;

        $this->explicitSelect = false;
        $this->addRowAction('setDefault');
        if (!Tools::getValue($this->identifier)) {
            $this->multishop_context_group = false;
        }
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );
        $this->_defaultOrderBy = $this->identifier;
        $this->deleted = false;
        $this->_orderBy = null;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', range(1, 1));

        $this->shopLinkType = 'shop';
        $this->fields_list = array(
            'id_quotation_form' => array(
                'title' => $this->l('Id', 'QuotationForms'),
                'align' => 'text-center',
                'class' => 'fixed-width-sm',
            ),
            'form_name' => array(
                'title' => $this->l('Form', 'QuotationForms'),
                'width' => 'auto',
                'havingFilter' => false,
            ),
            'num_products' => array(
                'title' => $this->l('# Products', 'QuotationForms'),
                'align' => 'text-center',
                'havingFilter' => false,
            ),
            'default_form' => array(
                'title' => $this->l('Default Form', 'QuotationForms'),
                'align' => 'text-center',
                'type' => 'bool',
                'ajax' => false,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
        );

        Shop::setContext(Shop::CONTEXT_ALL);


        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationForms')
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        if ($this->action != null || ($this->display != null && $this->tabAccess[$this->display])) {
            $this->addJqueryPlugin('autocomplete');
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationadmin_form.js'
            );
            $this->context->controller->addCss(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.css'
            );
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/jquery-confirm/jquery-confirm.min.js'
            );
            $this->context->controller->addJqueryUI('ui.dialog');
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/css/roja45quotationadmin_form.css'
            );
            if (version_compare(_PS_VERSION_, '1.6.0.3', '>=') === true) {
                $this->context->controller->addjqueryPlugin('sortable');
            } elseif (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/jquery.sortable.js');
            }
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_quotation_form'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new quotation form', 'QuotationForms'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ?
        array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        /** @var QuotationRequest $quotation_request */
        $quotation_form = $this->loadObject(true);

        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->l('Edit Quotation Form: ', 'QuotationForms') . (isset($quotation_form->form_name) ?
                    $quotation_form->form_name : $quotation_form->id);
                break;
            case 'view':
                $this->toolbar_title[] = $this->l('View Quotation Form: ', 'QuotationForms') . (isset($quotation_form->form_name) ?
                    $quotation_form->form_name : $quotation_form->id);
                break;
            default:
                $this->toolbar_title[] = $this->l('Quotation Forms', 'QuotationForms');
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            return $this->processSaveForm();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            return parent::postProcess();
        } else {
            return parent::postProcess();
        }
    }

    public function processSaveForm()
    {
        /** @var QuotationForm $quotation_request */
        try {
            $config = json_decode(Tools::getValue('ROJA45_QUOTATIONSPRO_FORM'));
            $id_quotation_form = Tools::getValue('ROJA45_QUOTATIONSPRO_FORM_ID');
            /** @var QuotationForm $object */
            $quotation_form = new QuotationForm($id_quotation_form);

            $quotation_form->id_shop = (int) Tools::getValue('ROJA45_QUOTATIONSPRO_FORM_SHOP_ID');
            if (!$quotation_form->id_shop) {
                $quotation_form->id_shop = Configuration::get('PS_SHOP_DEFAULT');
            }
            $quotation_form->form_columns = (int) $config->num_columns;
            $quotation_form->form_name = $config->form_name;
            $quotation_form->form_column_titles = $config->titles;
            $quotation_form->default_form = (int) Tools::getValue('ROJA45_QUOTATIONSPRO_FORM_DEFAULT');

            if ((int) Tools::getValue('ROJA45_QUOTATIONSPRO_FORM_DEFAULT')) {
                $quotation_form->default_form = 1;
            } else {
                $shop_forms = QuotationForm::getForms($quotation_form->id_shop, true);
                if ((!$shop_forms) || !count($shop_forms)) {
                    $quotation_form->default_form = 1;
                }
            }
            $id_quotation_form = $quotation_form->save();

            $sql = '
                DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_form_element`
                WHERE id_quotation_form ="' . (int) $id_quotation_form . '"';
            if (Db::getInstance()->execute($sql)) {
                foreach ($config->columns as $col => $column) {
                    if ($column) {
                        foreach ($column->fields as &$field) {
                            $configuration = $field->configuration;
                            $id = $field->id;
                            $name = $field->name;
                            $pos = $field->pos;
                            $type = $field->type;

                            if (($id == 'ROJA45QUOTATIONSPRO_FIRSTNAME') ||
                                ($id == 'ROJA45QUOTATIONSPRO_LASTNAME') ||
                                ($id == 'ROJA45QUOTATIONSPRO_EMAIL')) {
                                $deletable = 0;
                            } else {
                                $deletable = 1;
                            }

                            Db::getInstance()->insert(
                                'roja45_quotationspro_form_element',
                                array(
                                    'id_quotation_form' => (int) $id_quotation_form,
                                    'form_element_id' => pSQL($id),
                                    'form_element_name' => pSQL($name),
                                    'form_element_type' => pSQL($type),
                                    'form_element_column' => (int) $col,
                                    'form_element_position' => (int) $pos,
                                    'form_element_deletable' => (int) $deletable,
                                    'form_element_config' => pSQL($configuration),
                                )
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            $this->errors = $validationErrors;
        }

        if (Validate::isLoadedObject($quotation_form)) {
            $quotation_form->deleteConditions();
            foreach ($_POST as $key => $values) {
                if (preg_match('/^condition_group_([0-9]+)$/Ui', $key, $condition_group)) {
                    $conditions = array();
                    foreach ($values as $value) {
                        $condition = explode('_', $value);
                        $conditions[] = array('type' => $condition[0], 'value' => $condition[1]);
                    }
                    $quotation_form->addConditions($conditions);
                }
            }
            $quotation_form->applyConditions();
        }

        Roja45QuotationsPro::clearAllCached();
        return $quotation_form;
    }

    public function renderForm()
    {
        /** @var QuotationForm $quotation_form */
        $quotation_form = $this->loadObject(true);
        $tpl = $this->context->smarty->createTemplate(
            $this->getTemplatePath('quotationform_view.tpl') . 'quotationform_view.tpl'
        );

        $form = null;
        if (Validate::isLoadedObject($quotation_form)) {
            $form = $quotation_form->getFormData();
        } else {
            $form = $quotation_form->getDefaultForm();
        }
        $tpl->assign(
            array(
                'controller' => $this->context->link->getAdminLink(
                    'QuotationForms',
                    true
                ),
                'languages' => $this->context->controller->getLanguages(),
                'id_lang' => $this->context->language->id,
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                'id_quotation_form' => $quotation_form->id_quotation_form,
                'id_quotation_shop_id' => $quotation_form->id_shop,
                'is_17' => (version_compare(_PS_VERSION_, '1.7', '>=') == true) ? true : false,
                'contacts' => Contact::getContacts($this->context->language->id),
                'form' => $form,
                'shops' => Shop::getShops(),
                'columns' => $form['cols'],
                'col_width' => 12,
                'defaultFormLanguage' => (int) Configuration::get('PS_LANG_DEFAULT'),
                'customer_groups' => Group::getGroups($this->context->language->id, $this->context->shop->id),
                'categories' => Category::getSimpleCategories((int) $this->context->language->id),
                'conditions' => $quotation_form->getConditions(),
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
            )
        );
        return $tpl->fetch();
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        $id_lang_shop = true;
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

        foreach ($this->_list as &$list_item) {
            $quotation_form = new QuotationForm($list_item['id_quotation_form']);
            $list_item['num_products'] = count($quotation_form->getAffectedProducts());
            if ($list_item['num_products'] == 0) {
                $sql = 'SELECT COUNT(id_product) FROM `' . _DB_PREFIX_ . 'product_shop` WHERE id_shop = ' . (int) $list_item['id_shop'];
                $list_item['num_products'] = (int) Db::getInstance()->getValue($sql);
            }
        }
    }

    protected function getShopContextError()
    {
        return '
            <p class="alert alert-danger">' .
        $this->l('Please enable products from the shop context.', 'QuotationForms') .
            '</p>';
    }
}
