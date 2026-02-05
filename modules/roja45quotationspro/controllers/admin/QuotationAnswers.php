<?php
/**
 * QuotationAnswersController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationAnswersController
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationAnswersController.
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

class QuotationAnswersController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotations_answers/';
        $this->tpl_folder = 'quotations_answers/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_answer';
        $this->identifier = 'id_roja45_quotation_answer';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'QuotationAnswer';
        $this->action = 'QuotationAnswer';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationAnswers'),
                'confirm' => $this->l('Delete selected items?', 'QuotationAnswers'),
            ),
        );
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->_defaultOrderBy = $this->identifier = 'id_roja45_quotation_answer';
        $this->list_id = 'id_roja45_quotation_answer';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->addRowAction('edit');
        $this->addRowAction('duplicateAnswer');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', range(1, 12));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationAnswers'),
                'confirm' => $this->l('Delete selected items?', 'QuotationAnswers'),
                'icon' => 'icon-trash',
            ),
        );

        $languages = array();
        foreach (Language::getLanguages(false) as $lang) {
            $languages[$lang['id_lang']] = trim($lang['name']);
        }

        $this->fields_list = array(
            'id_roja45_quotation_answer' => array(
                'title' => $this->l('ID', 'QuotationAnswers'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name', 'QuotationAnswers'),
                'width' => 'auto',
            ),
            'type_text' => array(
                'title' => $this->l('Template Type', 'QuotationAnswers'),
                'type' => 'select',
                'orderby' => false,
                'list' => array(
                    1 => 'PDF',
                    2 => 'Email',
                    3 => 'Old Email Template',
                ),
                'filter_type' => 'int',
                'filter_key' => 'type',
            ),
        );

        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationAnswers')
        );
    }

    public function init()
    {
        if (Tools::isSubmit('add' . $this->table)) {
            $this->display = 'add';
        } elseif (Tools::isSubmit('update' . $this->table)) {
            $this->display = 'edit';
        }

        return parent::init();
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitReset' . $this->table)) {
            /** @var QuotationAnswer $answer */
            $answer = $this->loadObject(true);
            $template_path = _PS_THEME_DIR_ . 'modules/roja45quotationspro/views/templates/admin/custom/';
            foreach (Language::getLanguages(false) as $language) {
                $template = $answer->template[$language['id_lang']];
                if (file_exists($template_path . $template . '.tpl')) {
                    unlink($template_path . $template . '.tpl');
                }

                if (file_exists($template_path . $template . '-txt.tpl')) {
                    unlink($template_path . $template . '-txt.tpl');
                }
            }
            Tools::redirectAdmin($this->context->link->getAdminLink(
                'QuotationAnswers',
                true
            ) . '&update' . $this->table . '&' . $this->identifier . '=' . $answer->id);
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            /** @var QuotationAnswer $answer */
            $answer = $this->loadObject(true);
            $this->deleted = false;

            $template_path = _PS_THEME_DIR_ . 'modules/roja45quotationspro/views/templates/admin/custom/';
            if ($template_path && !file_exists($template_path)) {
                mkdir($template_path, 0777, true);
            }

            $template_type = (int) Tools::getValue('type');

            foreach (Language::getLanguages(false) as $language) {
                $template = Tools::getValue('template_' . $language['id_lang']);
                $html_content = Tools::getValue('html_template_' . $language['id_lang']);
                if (!empty($html_content)) {
                    if (empty($html_content)) {
                        $html_content = Tools::getValue('html_template_' . Configuration::get('PS_LANG_DEFAULT'));
                    }

                    if ($pos_product_list = strpos($html_content, 'data-do-not-remove="true"', 0)) {
                        if (!strpos($html_content, '<!-- foreach quotation_products -->', 0)) {
                            $html_content_search = substr($html_content, 0, $pos_product_list);
                            $line_start_pos = strrpos($html_content_search, '<tr');
                            $dom = \voku\helper\HtmlDomParser::str_get_html($html_content);
                            if ($product_element = $dom->find('tr[data-do-not-remove="true"]')) {
                                if ($element = $product_element[0]) {
                                    $replacement = $element->outertext();
                                    $str = '<!-- foreach quotation_products -->' .
                                        $replacement .
                                        '<!-- end foreach quotation_products -->';
                                    $html_content = substr_replace($html_content, $str, $line_start_pos, strlen($replacement));
                                }
                            }
                        }
                    }

                    $html_template = fopen($template_path . $template . '.tpl', 'w');
                    fwrite($html_template, $html_content);
                    fclose($html_template);

                    if ($template_type == QuotationAnswer::$MAIL) {
                        $text_content = Tools::getValue('text_template_' . $language['id_lang']);
                        if (empty($text_content)) {
                            $text_content = Tools::getValue('text_template_' . Configuration::get('PS_LANG_DEFAULT'));
                        }
                        $text_template = fopen($template_path . $template . '-txt.tpl', 'w');
                        fwrite($text_template, $text_content);
                        fclose($text_template);
                    }

                    if (isset($answer->template[$language['id_lang']]) && ($answer->template[$language['id_lang']] != $template)) {
                        $current_html_template = $template_path . $answer->template[$language['id_lang']] . '.tpl';
                        if (file_exists($current_html_template)) {
                            unlink($current_html_template);
                        }
                        $current_txt_template = $template_path . $answer->template[$language['id_lang']] . '-txt.tpl';
                        if (file_exists($current_txt_template)) {
                            unlink($current_txt_template);
                        }
                    }
                }
            }

            $custom_css = Tools::getValue('custom_css');
            if (Tools::strlen($custom_css)) {
                $custom_css_file = _PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/views/css/pdf-styles.css';
                //$custom_css_file = $template_path . 'pdf-styles.css';
                if (file_exists($custom_css_file)) {
                    $custom_css_file = fopen($custom_css_file, 'wa+');
                    fwrite($custom_css_file, $custom_css);
                    fclose($custom_css_file);
                }
            }
            return parent::postProcess();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $object = new QuotationAnswer(Tools::getValue('id_roja45_quotation_answer'), $this->context->language->id);
            if (!$object->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete default answers.', 'QuotationAnswers');
            } else {
                return $object->delete();
            }
        } elseif (Tools::isSubmit('duplicateAnswer' . $this->table)) {
            $answer = new QuotationAnswer(Tools::getValue('id_roja45_quotation_answer'), $this->context->language->id);
            if (Validate::isLoadedObject($answer)) {
                $answer->duplicate();
            }
            Tools::redirectAdmin($this->context->link->getAdminLink(
                'QuotationAnswers',
                true
            ));
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->identifier . 'Box') as $selection) {
                $object = new QuotationAnswer((int) $selection, $this->context->language->id);
                if (!$object->isRemovable()) {
                    $this->errors[] = $this->l('For security reasons, you cannot delete default answers.', 'QuotationAnswers');
                    break;
                }
                $object->delete();
            }
            return true;
        } else {
            return parent::postProcess();
        }
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ?
        array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        /** @var QuotationAnswers $answer */
        $answer = $this->loadObject(true);
        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = sprintf(
                    $this->l('Edit Answer: [%s]', 'QuotationAnswers'),
                    $answer->name[$this->context->language->id]
                );
                $this->addMetaTitle(sprintf(
                    $this->l('Edit Answer: [%s]', 'QuotationAnswers'),
                    $answer->name[$this->context->language->id]
                ));
                break;
            case 'add':
                $this->toolbar_title[] = $this->l('Add new Answer', 'QuotationAnswers');
                $this->addMetaTitle($this->l('Add new Answer', 'QuotationAnswers'));
                break;
            case 'view':
                $this->toolbar_title[] = sprintf(
                    $this->l('View Answer: [%s]', 'QuotationAnswers'),
                    $answer->name[$this->context->language->id]
                );
                $this->addMetaTitle(sprintf(
                    $this->l('View Answer: [%s]', 'QuotationAnswers'),
                    $answer->name[$this->context->language->id]
                ));
                break;
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }
    }

    public function renderForm()
    {
        $quotation_answer = $this->loadObject(true);

        $types = array(
            array(
                'id_type' => 1,
                'name' => $this->l('PDF', 'QuotationAnswers'),
            ),
            array(
                'id_type' => 2,
                'name' => $this->l('Email', 'QuotationAnswers'),
            ),
            array(
                'id_type' => 3,
                'name' => $this->l('Old Email Template', 'QuotationAnswers'),
            ),
        );
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Quotation Answers', 'QuotationAnswers'),
                'icon' => 'icon-time',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Display Name', 'QuotationAnswers'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'default_value' => 'TBC',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Subject', 'QuotationAnswers'),
                    'name' => 'subject',
                    'required' => false,
                    'lang' => false,
                    'default_value' => 'TBC',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Template Name', 'QuotationAnswers'),
                    'name' => 'template',
                    'required' => true,
                    'lang' => false,
                    'hint' => array(
                        $this->l('Name Identifier', 'QuotationAnswers'),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type', 'QuotationAnswers'),
                    'name' => 'type',
                    'options' => array(
                        'query' => $types,
                        'id' => 'id_type',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Language', 'QuotationAnswers'),
                    'name' => 'id_lang',
                    'options' => array(
                        'query' => $this->context->controller->getLanguages(),
                        'id' => 'id_lang',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Template', 'QuotationAnswers'),
                    'name' => 'html_template',
                    'lang' => true,
                    'class' => 'html_template',
                    'form_group_class' => 'html_template_group',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Template', 'QuotationAnswers'),
                    'name' => 'custom_css',
                    'lang' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Text Template', 'QuotationAnswers'),
                    'name' => 'text_template',
                    'lang' => true,
                    'form_group_class' => 'text_template_group',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled', 'QuotationAnswers'),
                    'name' => 'enabled',
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'enabled_on',
                            'value' => 1,
                            'label' => 'Yes',
                        ),
                        array(
                            'id' => 'enabled_off',
                            'value' => 0,
                            'label' => 'No',
                        ),
                    ),
                ),
            ),
            'buttons' => array(
                'preview_template' => array(
                    'title' => $this->l('Preview', 'QuotationAnswers'),
                    'class' => 'btn-preview-template pull-right',
                    'icon' => 'process-icon-edit',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save', 'QuotationAnswers'),
            ),
        );

        //$html = parent::renderForm();

        $html = $this->buildForm($quotation_answer);

        $tpl = $this->context->smarty->createTemplate($this->getTemplatePath('quotationanswers_tokens.tpl') . 'quotationanswers_tokens.tpl');
        $html .= $tpl->fetch();

        return $html;
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
            if ($list_item['enabled'] == 0) {
                $list_item['enabled_text'] = 'NO';
                $list_item['color_enabled'] = '#FF0000';
            } else {
                $list_item['enabled_text'] = 'YES';
                $list_item['color_enabled'] = '#32CD32';
            }

            if ($list_item['type'] == QuotationAnswer::$PDF) {
                $list_item['type_text'] = 'PDF';
            } elseif ($list_item['type'] == QuotationAnswer::$MAIL) {
                $list_item['type_text'] = 'Email';
            } elseif ($list_item['type'] == QuotationAnswer::$OLD) {
                $list_item['type_text'] = 'Old';
            }
        }
    }

    public function getFieldsValue($obj)
    {
        /** @var QuotationAnswer $obj */
        if ($this->action == 'new') {
            return parent::getFieldsValue($obj);
        }
        $fields_value = parent::getFieldsValue($obj);

        foreach (Language::getLanguages(false) as $language) {
            $id_lang = $language['id_lang'];
            if (empty($fields_value['template'][$language['id_lang']])) {
                if (!$id_lang_def = Language::getIdByIso('en')) {
                    $id_lang_def = Configuration::get('PS_LANG_DEFAULT');
                }
                $fields_value['template'][$language['id_lang']] = substr(
                        $fields_value['template'][$id_lang_def],
                        0,
                        -2
                    ) . $language['iso_code'];
            }

            if ($template_path = $this->getTemplate($fields_value['template'][$id_lang])) {
                $template_path .= $fields_value['template'][$id_lang];
            } else {
                $en_template = substr(
                    $fields_value['template'][$id_lang],
                    0,
                    -2
                ) . 'en';
                $template_path = $this->getTemplate($en_template) . $en_template;
            }

            $template = Tools::file_get_contents(
                $template_path . '.tpl'
            );
            $template = str_replace('{custom_css}', $obj->custom_css, $template);
            $fields_value['html_template'][$language['id_lang']] = $template;

            if (file_exists($template_path . '-txt.tpl')) {
                $num_lines = count(file($template_path . '-txt.tpl'));
                $fields_value['text_template'][$language['id_lang']] = Tools::file_get_contents(
                    $template_path . '-txt.tpl'
                );
                $fields_value['text_template_lines'][$language['id_lang']] = $num_lines;
            } else {
                $fields_value['text_template'][$language['id_lang']] = '';
                $fields_value['text_template_lines'][$language['id_lang']] = 5;
            }
        }
        return $fields_value;
    }

    public function getTemplate($template)
    {
        $template_path = false;
        if (file_exists(_PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/admin/custom/' . $template . '.tpl')) {
            $template_path = _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/admin/custom/';
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/views/templates/admin/custom/' . $template . '.tpl')) {
            $template_path = _PS_ROOT_DIR_ . '/modules/' . $this->module->name . '/views/templates/admin/custom/';
        }

        return $template_path;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        if ($this->display &&
            isset($this->tabAccess[$this->display]) &&
            $this->tabAccess[$this->display] == 1 &&
            in_array($this->display, array('add', 'edit'))
        ) {
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/js/roja45quotationanswers.js'
            );
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/css/roja45quotationsproadmin.css'
            );
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/tinymce/tinymce.min.js'
            );
            $this->context->controller->addJS(
                _PS_MODULE_DIR_ . $this->module->name . '/libraries/tinymce/jquery.tinymce.min.js'
            );
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_ . $this->module->name . '/views/css/pdf-styles.css'
            );
        }
    }

    public function displayDuplicateAnswerLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_duplicateAnswer.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getAdminLink(
                'QuotationAnswers',
                true
            ) . '&token=' . $token . '&' . $this->identifier . '=' . $id . '&duplicateAnswer' . $this->table,
            'action' => ' Duplicate',
            'id_roja45_quotation_answer' => $id,
        ));
        return $tpl->fetch();
    }

    public function ajaxProcessPreviewMessage()
    {
        $validationErrors = array();
        try {
            $template_type = (int) Tools::getValue('type');
            $quotation_answer = new QuotationAnswer(
                Tools::getValue('id_roja45_quotation_answer'),
                Tools::getValue('id_language')
            );

            $template = Tools::getValue('content');
            if (!$template) {
                throw new Exception('No content to preview');
            }
            ob_start();

            $preview_type = Tools::getValue('preview_type');

            $date = new DateTime();
            $pdf_data = array(
                'title' => 'QUOTE_000004_30-12-20.pdf',
                'header' => 'Quotation',
                'tax_text' => '(inc.)',
                'date' => $date->format(Context::getContext()->language->date_format_lite),
                'date_full' => $date->format(Context::getContext()->language->date_format_full),
            );

            if (Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()) != false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()))) {
                $logo = Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID());
            } elseif (Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()) != false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()))) {
                $logo = Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID());
            } else {
                $logo = '';
            }

            $width = 0;
            $height = 0;
            if (!empty($logo)) {
                list($width, $height) = getimagesize(_PS_IMG_DIR_ . $logo);
            }
            $logo = Tools::getHttpHost(true) . _PS_IMG_ . $logo;

            // Limit the height of the logo for the PDF render
            $maximum_height = 100;
            if ($height > $maximum_height) {
                $ratio = $maximum_height / $height;
                $height *= $ratio;
                $width *= $ratio;
            }

            $addressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
            $shop_address = '';
            $shop_state = '';
            $shop_country = '';
            $shop_address_obj = Context::getContext()->shop->getAddress();
            if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
                $shop_address_line = AddressFormat::generateAddress(
                    $shop_address_obj,
                    array(),
                    '-',
                    ' '
                );
                $shop_address = AddressFormat::generateAddress(
                    $shop_address_obj,
                    $addressPatternRules,
                    '<br/>',
                    ' '
                );
                if ($shop_address_obj->id_state) {
                    $shop_state = new State($shop_address_obj->id_state, Context::getContext()->language->id);
                    $shop_state = $shop_state->name;
                }
                if ($shop_address_obj->id_country) {
                    $shop_country = new Country($shop_address_obj->id_country, Context::getContext()->language->id);
                    $shop_country = $shop_country->name;
                }
            }

            $shop_data = array(
                'logo_path' => $logo,
                'img_ps_dir' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
                'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
                'shop_name' => Configuration::get('PS_SHOP_NAME', null, null, Context::getContext()->shop->id),
                'shop_logo' => $logo,
                'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, Context::getContext()->shop->id),
                'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, Context::getContext()->shop->id),
                'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, Context::getContext()->shop->id),
                'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, (int) Context::getContext()->shop->id),
                'shop_address1' => $shop_address_obj->address1,
                'shop_address2' => $shop_address_obj->address2,
                'shop_city' => $shop_address_obj->city,
                'shop_postcode' => $shop_address_obj->postcode,
                'shop_state' => $shop_state,
                'shop_country' => $shop_country,
                'shop_address' => $shop_address,
                'shop_address_line' => $shop_address_line,
                'shop_url' => Context::getContext()->link->getPageLink(
                    'index',
                    true,
                    Context::getContext()->language->id,
                    null,
                    false,
                    Context::getContext()->shop->id
                ),
                'customer_account_quotation_link' => Context::getContext()->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getQuotationDetails',
                        'id_roja45_quotation' => $this->id,
                    ),
                    true
                ),
                'customer_account_link' => Context::getContext()->link->getPageLink(
                    'my-account',
                    true,
                    Context::getContext()->language->id,
                    null,
                    false,
                    Context::getContext()->shop->id
                ),
                'shop_logo_width' => $width,
                'shop_logo_height' => $height,
            );
            $pdf_data = array_merge(
                $pdf_data,
                $shop_data
            );

            $id_image = Context::getContext()->language->iso_code . '-default';
            $imageObj = new Image($id_image);
            $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('cart');
            $url = Context::getContext()->link->getImageLink(
                'no-image',
                $id_image,
                $format
            );
            $image_loc = _PS_PROD_IMG_DIR_ . $imageObj->getImgPath() . '.' . $imageObj->image_format;
            if (!file_exists($image_loc)) {
            }
            //$image_tag = '<img class="img-responsive" src="'.$url.'"/>';

            $address = new Address();
            $address->address1 = 'Calle 23 #5b-221';
            $address->address2 = 'Taganga';
            $address->city = 'Santa Marta';
            $address->country = 'Colombia';
            $address->postcode = '470001';
            //$addressPatternRules['avoid'][] = 'firstname';
            //$addressPatternRules['avoid'][] = 'lastname';
            $customer_address = AddressFormat::generateAddress(
                $address,
                $addressPatternRules,
                '<br/>',
                ' ',
                array(
                    'firstname' => '<p>%s',
                    'lastname' => '%s</p>',
                )
            );

            $form_data = new stdClass();
            $firstname = new stdClass();
            $firstname->pos = 0;
            $firstname->name = 'ROJA45QUOTATIONSPRO_FIRSTNAME';
            $firstname->type = 'TEXT';
            $firstname->label = 'First Name';
            $firstname->value = 'Roja';
            $lastname = new stdClass();
            $lastname->pos = 1;
            $lastname->name = 'ROJA45QUOTATIONSPRO_LASTNAME';
            $lastname->type = 'TEXT';
            $lastname->label = 'Last Name';
            $lastname->value = 'FortyFive';
            $email = new stdClass();
            $email->pos = 2;
            $email->name = 'ROJA45QUOTATIONSPRO_EMAIL';
            $email->type = 'TEXT';
            $email->label = 'Email Address';
            $email->value = 'test_user@roja45.com';
            $form_fields = new stdClass();
            $form_fields->num = 1;
            $form_fields->fields = array(
                0 => $firstname,
                1 => $lastname,
                2 => $email,
            );

            $form_data->columns = array(
                0 => $form_fields,
            );

            $quotation_data = array(
                'quotation_id' => 1,
                'quotation_status_id' => 1,
                'quotation_status_status' => 'Quotation Received',
                'quotation_status_code' => 'RCVD',
                'quotation_reference' => 'QUOTE_000004_30-12-20',
                'quotation_date_created' => '12/31/2020',
                'quotation_date_updated' => '12/31/2020',
                'quotation_sales_person' => 'Employee #1',
                'quotation_has_customizations' => 0,
                'quotation_expiry_date' => '01/31/2021',
                'quotation_expiry_time' => '12:00:00',
                'quotation_products' => array(
                    1 => array(
                        'image_url' => $url,
                        'image_loc' => $image_loc,
                        'legend' => 'Hummingbird printed t-shirt',
                        'product_title' => 'Hummingbird printed t-shirt',
                        'reference' => 'demo_1',
                        'comment' => 'TBC',
                        'quantity' => '1',
                        'product_customization_total' => '£0.00',
                        'product_list_price' => '£20.00',
                        'product_list_price_exc' => '£12.00',
                        'product_list_price_inc' => '£20.00',
                        'product_subtotal_exc' => '£12.00',
                        'product_subtotal_inc' => '£20.00',
                        'product_subtotal' => '£20.00',
                        'product_tax' => '£4.00',
                        'tax_rate' => '20',
                    ),
                    2 => array(
                        'image_url' => $url,
                        'image_loc' => $image_loc,
                        'legend' => 'Hummingbird printed sweater',
                        'product_title' => 'Hummingbird printed sweater',
                        'reference' => 'demo_3',
                        'comment' => 'TBC',
                        'quantity' => '3',
                        'product_customization_total' => '£5.00',
                        'product_list_price' => '£10.00',
                        'product_list_price_exc' => '£8.00',
                        'product_list_price_inc' => '£10.00',
                        'product_subtotal_exc' => '£35.00',
                        'product_subtotal_inc' => '£30.00',
                        'product_subtotal' => '£30.00',
                        'product_tax' => '£5.00',
                        'tax_rate' => '20',
                        'customizations' => array(
                            1 => array(
                                'name' => 'Customization 1',
                                'value' => 'Customization Value',
                            ),
                            2 => array(
                                'name' => 'Customization 2',
                                'value' => 'Customization Value',
                            ),
                        ),
                    ),
                ),
                'quotation_totals_by_tax' => array(
                    '0' => array(
                        'tax_summary_name' => 'No Tax',
                        'tax_summary_total_exc' => '£29.00',
                        'tax_summary_total_inc' => '£29.00',
                        'tax_summary_total_tax' => '£0',
                    ),
                    '4' => array(
                        'tax_summary_name' => 'UK Reduced Rate (5%)',
                        'tax_summary_total_exc' => '£28.72',
                        'tax_summary_total_inc' => '£30.156',
                        'tax_summary_total_tax' => '£1.436',
                    ),
                    '7' => array(
                        'tax_summary_name' => 'UK Standard Rate (20%)',
                        'tax_summary_total_exc' => '£411.40',
                        'tax_summary_total_inc' => '£493.68',
                        'tax_summary_total_tax' => '£82.28',
                    ),
                ),
                'charges' => array(
                    0 => array(
                        'charge_id' => '1',
                        'charge_name' => 'PS 1.7.7',
                        'charge_type' => 'SHIPPING',
                        'charge_method' => 'VALUE',
                        'charge_value' => '0.000000',
                        'charge_amount' => '£5.00',
                    ),
                    1 => array(
                        'charge_id' => '2',
                        'charge_name' => 'Handling',
                        'charge_type' => 'HANDLING',
                        'charge_method' => 'VALUE',
                        'charge_value' => '0.000000',
                        'charge_amount' => '£2.40',
                    ),
                ),
                'discounts' => array(
                    0 => array(
                        'discount_id' => '1',
                        'discount_name' => '10% Discount',
                        'discount_type' => 'DISCOUNT',
                        'discount_method' => 'PERCENTAGE',
                        'discount_value' => '10.000000',
                        'discount_amount' => '£1.864',
                    ),
                ),
                'language_id' => 1,
                'language_name' => 'English',
                'language_iso_code' => 'en',
                'customer_id' => 1,
                'customer_title' => 'Mr',
                'customer_firstname' => 'John',
                'customer_lastname' => 'Smith',
                'customer_email' => 'jsmith@email.com',
                'customer_address_id' => 1,
                'customer_address_address1' => 'Calle 23 #5b-221',
                'customer_address_address2' => 'Taganga',
                'customer_address_city' => 'Santa Marta',
                'customer_address_postcode' => '470001',
                'customer_address_country' => 'Colombia',
                'customer_phone' => '320 123 4567',
                'customer_mobile' => '320 123 4567',
                'customer_address' => $customer_address,
                'currency_id' => 1,
                'currency_name' => 'British Pound',
                'currency_iso_code' => 'GBP',
                'currency_symbol' => '£',
                'show_taxes' => 1,
                'request_data' => $form_data,
            );

            $pdf_data = array_merge(
                $pdf_data,
                $quotation_data
            );

            $totals_data = array(
                'tax_text' => '(.inc)',
                'quotation_products_before_discount' => '£50.00',
                'quotation_customizations' => '£2.00',
                'quotation_ecotax' => '£1.25',
                'quotation_shipping' => '£5.00',
                'quotation_handling' => '£2.00',
                'quotation_discounts' => '£0.00',
                'quotation_charges' => '£0.00',
                'quotation_subtotal' => '£50.00',
                'quotation_subtotal_exc' => '£45.00',
                'quotation_subtotal_inc' => '£50.00',
                'quotation_tax' => '£11.00',
                'quotation_total' => '£57.00',
                'quotation_total_inc' => '£57.00',
                'quotation_total_exc' => '£50.00',
            );

            $pdf_data = array_merge(
                $pdf_data,
                $totals_data
            );

            $template_path = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('customer_request_form');
            $tpl = $this->context->smarty->createTemplate(
                $template_path
            );

            $tpl->assign($pdf_data);
            $customer_form = $tpl->fetch();

            $customer_form_txt = \Soundasleep\Html2Text::convert(
                $customer_form,
                [
                    'ignore_errors' => true
                ]
            );

            $pdf_data['customer_form'] = $customer_form;
            $pdf_data['customer_form_text'] = $customer_form_txt;

            if ($template_type == QuotationAnswer::$PDF) {
                $content_template = RojaFortyFiveQuotationsProCore::getEditorTemplatePath($quotation_answer->template);
                //$content_template = $template_path.$quotation_answer->template.'.tpl';
                file_put_contents($content_template, $template);
                $tpl = $this->context->smarty->createTemplate(
                    $content_template
                );
                $tpl->assign(
                    array(
                        'show_customizations' => 1,
                        'show_discounts' => 1,
                        'show_account' => 1,
                        'show_summary' => 1,
                        'show_product_customizations' => 1,
                        'show_customization_cost' => 1,
                        'show_ecotax' => 1,
                        'show_prices' => 1,
                        'show_product_discounts' => 1,
                        'show_product_comments' => 1,
                        'show_additional_shipping' => 1,
                    )
                );
                //$message_content = $tpl->fetch();
                $template = $tpl->fetch();

                $pdf_renderer = new RojaPDFGenerator(false);
                // $pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
                $pdf_renderer->createCSS($quotation_answer->custom_css);

                // extract header
                $header_start = strpos($template, '<!-- BEGIN HEADER -->');
                if ($header_start >= 0) {
                    $end = strpos($template, '<!-- END HEADER -->') + 19;
                    $header = Tools::substr($template, $header_start, $end - $header_start);
                    $header = QuotationAnswer::processTemplate($header, $pdf_data);
                    $pdf_renderer->createHeader($header);
                }

                // extract content
                if ($start = strpos($template, '<!-- END HEADER -->') + 19) {
                    $end = strpos($template, '<!-- BEGIN FOOTER -->');
                    $content = Tools::substr($template, $start, $end - $start);
                    $content = QuotationAnswer::processRecursiveTemplate($content, $pdf_data);
                    $pdf_renderer->createContent($content);
                }

                // extract footer
                if ($start = strpos($template, '<!-- BEGIN FOOTER -->')) {
                    $end = strpos($template, '<!-- END FOOTER -->') + 19;
                    $footer = Tools::substr($template, $start, $end - $start);
                    $footer = QuotationAnswer::processTemplate($footer, $pdf_data);
                    $pdf_renderer->createFooter($footer);
                }
                $pdf_renderer->writePage();

                if (ob_get_level() && ob_get_length() > 0) {
                    ob_clean();
                }
                if (file_exists(_PS_ROOT_DIR_ . '/ROJA45_PREVIEW.pdf')) {
                    unlink(_PS_ROOT_DIR_ . '/ROJA45_PREVIEW.pdf');
                }

                try {
                    $content = $pdf_renderer->render(_PS_ROOT_DIR_ . '/ROJA45_PREVIEW.pdf', 'S');
                } catch (Exception $e) {
                    die(var_dump($e));
                }

                die(base64_encode($content));
                //@chmod(_PS_ROOT_DIR_.'/ROJA45_PREVIEW.pdf', 0775);

                $url = Tools::getShopProtocol() . Tools::getHttpHost() . __PS_BASE_URI__ . 'ROJA45_PREVIEW.pdf';
                die(json_encode(array(
                    'content' => $content,
                    'url' => $url,
                    'result' => 2,
                    'response' => $this->l('Updated', 'QuotationAnswers'),
                )));
            } elseif ($template_type == QuotationAnswer::$MAIL) {
                $email_data = array(
                    'customer_username' => 'jsmith',
                    'customer_temporary_password' => 'Asyj$eWE12',
                    'customer_quotes_link' => $this->context->link->getModuleLink(
                        'roja45quotationspro',
                        'QuotationsProFront',
                        array(
                            'action' => 'getCustomerQuotes',
                        ),
                        true
                    ),
                    'quotation_purchase_link' => $this->context->link->getModuleLink(
                        'roja45quotationspro',
                        'QuotationsProFront',
                        array(
                            'p' => $quotation_data['quotation_id'],
                        ),
                        true
                    ),
                );
                $pdf_data = array_merge(
                    $pdf_data,
                    $email_data
                );

                if ($preview_type == 'html') {
                    //$content_template = $template_path.$quotation_answer->template.'.tpl';
                    $content_template = RojaFortyFiveQuotationsProCore::getEditorTemplatePath($quotation_answer->template);
                    //$mail_template = $template_path.'mail_template.tpl';
                    $mail_template = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('mail_template');
                } else {
                    //$content_template = $template_path.$quotation_answer->template.'-txt.tpl';
                    $content_template = RojaFortyFiveQuotationsProCore::getEditorTemplatePath($quotation_answer->template . '-txt');
                    //$mail_template = $template_path.'mail_template_txt.tpl';
                    $mail_template = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('mail_template_txt');
                }

                file_put_contents($content_template, $template);
                $tpl = $this->context->smarty->createTemplate(
                    $content_template
                );
                $tpl->assign(
                    array(
                        'show_customizations' => 1,
                        'show_discounts' => 1,
                        'show_account' => 1,
                        'show_summary' => 1,
                        'show_product_customizations' => 1,
                        'show_customization_cost' => 1,
                        'show_ecotax' => 1,
                        'show_prices' => 1,
                        'show_product_discounts' => 1,
                        'show_product_comments' => 1,
                        'show_additional_shipping' => 1,
                    )
                );
                $message_content = $tpl->fetch();

                $contents = Tools::file_get_contents($mail_template);
                $contents = str_replace('{email_subject}', 'Preview Message', $contents);
                $contents = str_replace(
                    '{email_css}',
                    Tools::file_get_contents(_PS_ROOT_DIR_ . '/modules/roja45quotationspro/views/css/email-styles.css'),
                    $contents
                );
                $contents = str_replace(
                    '{email_body}',
                    $message_content,
                    $contents
                );

                $content = QuotationAnswer::processRecursiveTemplate($contents, $pdf_data);

                $content_check = json_decode($content);
                if (isset($content_check)) {
                }
                if ($preview_type == 'text') {
                    $content = Tools::nl2br($content);
                }
                die(json_encode(array(
                    'content' => $content,
                    'result' => 2,
                    'response' => $this->l('Success', 'QuotationAnswers'),
                )));
            }
        } catch (Exception $e) {
            $validationErrors = array();
            $validationErrors[] = $e->getMessage();
            die(json_encode(array(
                'result' => 0,
                'errors' => $validationErrors,
            )));
        }
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'roja45_quotationspro_answers') {
            $this->initList();
        }

        return parent::filterToField($key, $filter);
    }

    private function buildForm($quotation_answer)
    {
        $tpl = $this->createModuleTemplate(
            'quotationanswers_form.tpl'
        );

        $this->fields_form = [['form' => $this->fields_form]];
        $fields_value = $this->getFieldsValue($quotation_answer);
        $tpl->assign(
            array(
                'controller_url' => $this->context->link->getAdminLink(
                    'QuotationAnswers',
                    true
                ),
                'languages' => $this->context->controller->getLanguages(),
                'link' => $this->context->link,
                'defaultFormLanguage' => Configuration::get('PS_LANG_DEFAULT'),
                'id_roja45_quotation_answer' => $quotation_answer->id,
                'roja45_template_type' => $quotation_answer->type,
                'email_template' => ($quotation_answer->type == QuotationAnswer::$PDF) ?
                0 :
                1,
                'roja45_template_css' => ($quotation_answer->type == QuotationAnswer::$PDF) ?
                '/modules/roja45quotationspro/views/css/pdf-styles.css' :
                '/modules/roja45quotationspro/views/css/email-styles.css',
                'fields_value' => $fields_value,
            )
        );

        return $tpl->fetch();
    }

    private function createModuleTemplate($tpl_name)
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

    private function getTemplateVars()
    {
        $shop_address = '';
        $shop_address_obj = $this->context->shop->getAddress();
        if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
            $shop_address = AddressFormat::generateAddress(
                $shop_address_obj,
                array(),
                '<br/>',
                ' ',
                array(
                    'company' => '<p>%s</p>',
                )
            );
        }
        $date = new DateTime();
        $vars = array(
            '{shop_logo}' => $this->getImageTag(_PS_IMG_ . Configuration::get(
                'PS_LOGO',
                null,
                null,
                $this->context->shop->id
            )),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_email}' => Configuration::get('PS_SHOP_EMAIL'),
            '{shop_address}' => $shop_address,
            '{shop_url}' => $this->context->link->getPageLink(
                'index',
                true,
                $this->context->language->id,
                null,
                false,
                $this->context->shop->id
            ),
            '{shop_fax}' => Configuration::get('PS_SHOP_FAX', null, null, $this->context->shop->id),
            '{shop_phone}' => Configuration::get('PS_SHOP_PHONE', null, null, $this->context->shop->id),
            '{date}' => $date->format($this->context->language->date_format_lite),
        );

        return $vars;
    }
}
