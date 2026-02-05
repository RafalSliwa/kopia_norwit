<?php
/**
 * QuotationStatusesController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationStatusesController
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationStatusesController.
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

class QuotationStatusesController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotations_statuses';
        $this->tpl_folder = 'quotations_statuses';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_status';
        $this->identifier = 'id_roja45_quotation_status';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'QuotationStatus';
        $this->action = 'QuotationStatus';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );

        $this->_defaultOrderBy = $this->identifier = 'id_roja45_quotation_status';
        $this->list_id = 'id_roja45_quotation_status';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', range(1, 13));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationStatuses'),
                'confirm' => $this->l('Delete selected items?', 'QuotationStatuses'),
                'icon' => 'icon-trash',
            ),
        );

        $fields_list = array(
            'id_roja45_quotation_status' => array(
                'title' => $this->l('ID', 'QuotationStatuses'),
                'align' => 'text-center',
                'class' => 'fixed-width-sm',
            ),
            'status' => array(
                'title' => $this->l('Name', 'QuotationStatuses'),
                'width' => 'auto',
            ),
            'display_code' => array(
                'title' => $this->l('Code', 'QuotationStatuses'),
                'color' => 'color',
                'align' => 'text-center',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-md',
            ),
            'send_email' => array(
                'title' => $this->l('Send email to customer', 'QuotationStatuses'),
                'align' => 'text-center',
                'active' => 'sendEmail',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
            'notify_admin' => array(
                'title' => $this->l('Notify admin', 'QuotationStatuses'),
                'align' => 'text-center',
                'active' => 'notifyAdmin',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),

        );

        $answer_array = array();
        $answers = QuotationAnswer::getMailTemplates($this->context->language->id);
        foreach ($answers as $answer) {
            $answer_array[$answer['id_roja45_quotation_answer']] = $answer['name'];
        }
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $fields_list = array_merge(
                $fields_list,
                array(
                    'answer_name' => array(
                        'title' => $this->l('Customer Email', 'QuotationStatuses'),
                        'type' => 'select',
                        'list' => $answer_array,
                        'width' => 'auto',
                        'filter_key' => 'a!id_roja45_quotation_answer',
                        'filter_type' => 'int',
                        'order_key' => 'id_roja45_quotation_answer',
                    ),
                    'admin_answer_name' => array(
                        'title' => $this->l('Admin Email', 'QuotationStatuses'),
                        'type' => 'select',
                        'list' => $answer_array,
                        'width' => 'auto',
                        'filter_key' => 'a!id_roja45_quotation_answer_admin',
                        'filter_type' => 'int',
                        'order_key' => 'id_roja45_quotation_answer_admin',
                    ),
                )
            );
        } else {
            $fields_list = array_merge(
                $fields_list,
                array(
                    'answer_template' => array(
                        'title' => $this->l('Answer', 'QuotationStatuses'),
                        'class' => 'fixed-width-sm',
                    ),
                )
            );
        }
        $this->fields_list = $fields_list;
        $this->toolbar_title = $this->l('Quotation States', 'QuotationStatuses');
        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationStatuses')
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

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach ($this->_list as &$list_item) {
                $quotation_answer = new QuotationAnswer(
                    $list_item['id_roja45_quotation_answer'],
                    $this->context->language->id
                );
                $list_item['answer_name'] = $quotation_answer->name;
                $quotation_answer = new QuotationAnswer(
                    $list_item['id_roja45_quotation_answer_admin'],
                    $this->context->language->id
                );
                $list_item['admin_answer_name'] = $quotation_answer->name;
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $this->deleted = false; // Disabling saving historisation
            if (isset($_POST['customer_pdf_ids'])) {
                $_POST['customer_pdf_ids'] = implode(',', $_POST['customer_pdf_ids']);
            }
            if (isset($_POST['admin_pdf_ids'])) {
                $_POST['admin_pdf_ids'] = implode(',', $_POST['admin_pdf_ids']);
            }
            if (!isset($_POST['code'])) {
                $_POST['code'] = $_POST['display_code_' . Configuration::get('PS_LANG_DEFAULT')];
            }
            return parent::postProcess();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $quotation_status = new QuotationStatus(
                Tools::getValue('id_quotation_status'),
                $this->context->language->id
            );
            if (!$quotation_status->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete default quotation statuses.', 'QuotationStatuses');
            } else {
                return parent::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->table . 'Box') as $selection) {
                $quotation_status = new QuotationStatus((int) $selection, $this->context->language->id);
                if (!$quotation_status->isRemovable()) {
                    $this->errors[] = $this->l('For security reasons, you cannot delete default quotation statuses.', 'QuotationStatuses');
                    break;
                }
            }

            if (!count($this->errors)) {
                return parent::postProcess();
            }
        } else {
            return parent::postProcess();
        }
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'roja45_quotation_status') {
            $this->initQuotationStatusList();
        }

        return parent::filterToField($key, $filter);
    }

    public function renderForm()
    {
        $fields = array(
            array(
                'type' => 'text',
                'label' => $this->l('Status name', 'QuotationStatuses'),
                'name' => 'status',
                'lang' => true,
                'required' => true,
                'hint' => array(
                    $this->l('Quotation status (e.g. \'Pending\').', 'QuotationStatuses'),
                    $this->l('Invalid characters: numbers and', 'QuotationStatuses') . ' !<>,;?=+()@#"{}_$%:',
                ),
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Color', 'QuotationStatuses'),
                'name' => 'color',
                'hint' => $this->l(
                    'Status will be highlighted in this color. HTML colors only.'
                ) . ' "lightblue", "#CC6600")',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Code', 'QuotationStatuses'),
                'name' => 'display_code',
                'lang' => true,
                'hint' => $this->l('Status Abbreviation Code.', 'QuotationStatuses'),
            ),
            array(
                'type' => 'switch',
                'name' => 'send_email',
                'label' => $this->l('Send Email', 'QuotationStatuses'),
                'hint' => $this->l('Select to automatically send an email.', 'QuotationStatuses'),
                'required' => true,
                'values' => array(
                    array(
                        'id' => 'send_email_on',
                        'value' => 1,
                        'label' => $this->l('Enabled', 'QuotationStatuses'),
                    ),
                    array(
                        'id' => 'send_email_off',
                        'value' => 0,
                        'label' => $this->l('Disabled', 'QuotationStatuses'),
                    ),
                ),
            ),
        );

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $templates = QuotationAnswer::getMailTemplates($this->context->language->id);
            array_unshift($templates, array(
                'id_roja45_quotation_answer' => 0,
                'name' => '',
            ));

            $pdfs = QuotationAnswer::getPDFTemplates($this->context->language->id);
            $pdfs = array_merge(
                array(
                    0 => array(
                        'id_roja45_quotation_answer' => 0,
                        'name' => $this->l('None', 'QuotationStatuses'),
                    ),
                ),
                $pdfs
            );
            $fields = array_merge(
                $fields,
                array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customer Email', 'QuotationStatuses'),
                        'name' => 'id_roja45_quotation_answer',
                        'lang' => false,
                        'options' => array(
                            'query' => $templates,
                            'id' => 'id_roja45_quotation_answer',
                            'name' => 'name',
                            'folder' => 'folder',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Admin Email', 'QuotationStatuses'),
                        'name' => 'id_roja45_quotation_answer_admin',
                        'lang' => false,
                        'options' => array(
                            'query' => $templates,
                            'id' => 'id_roja45_quotation_answer',
                            'name' => 'name',
                            'folder' => 'folder',
                        ),
                    ),
                )
            );

            $fields = array_merge(
                $fields,
                array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customer Email PDFs', 'QuotationStatuses'),
                        'name' => 'customer_pdf_ids',
                        'multiple' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => $pdfs,
                            'id' => 'id_roja45_quotation_answer',
                            'name' => 'name',
                            'folder' => 'folder',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Admin Email PDFs', 'QuotationStatuses'),
                        'name' => 'admin_pdf_ids',
                        'multiple' => true,
                        'lang' => false,
                        'options' => array(
                            'query' => $pdfs,
                            'id' => 'id_roja45_quotation_answer',
                            'name' => 'name',
                            'folder' => 'folder',
                        ),
                    ),
                )
            );
        } else {
            $templates = QuotationAnswer::getTemplates();
            $fields = array_merge(
                $fields,
                array(
                    array(
                        'type' => 'select_template',
                        'label' => $this->l('Template', 'QuotationStatuses'),
                        'name' => 'answer_template',
                        'lang' => false,
                        'options' => array(
                            'query' => $templates,
                            'id' => 'id',
                            'name' => 'name',
                            'folder' => 'folder',
                        ),
                    ),
                )
            );
        }
        $fields = array_merge(
            $fields,
            array(
                array(
                    'type' => 'switch',
                    'name' => 'notify_admin',
                    'label' => $this->l('Notify Admin', 'QuotationStatuses'),
                    'hint' => $this->l(
                        'Select to automatically send an email to your administration address when this status is assigned.'
                    ),
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'notify_admin_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'QuotationStatuses'),
                        ),
                        array(
                            'id' => 'notify_admin_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'QuotationStatuses'),
                        ),
                    ),
                ),
            )
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Quotation status', 'QuotationStatuses'),
                'icon' => 'icon-time',
            ),
            'input' => $fields,
            'submit' => array(
                'title' => $this->l('Save', 'QuotationStatuses'),
            ),
        );

        if (Tools::isSubmit('updateroja45_quotation_status') || Tools::isSubmit('addroja45_quotation_status')) {
            return $this->renderQuotationStatusForm();
        } else {
            return parent::renderForm();
        }
    }

    public function getFieldsValue($obj)
    {
        $fields_value = parent::getFieldsValue($obj);
        $fields_value['customer_pdf_ids[]'] = explode(',', $fields_value['customer_pdf_ids']);
        $fields_value['admin_pdf_ids[]'] = explode(',', $fields_value['admin_pdf_ids']);
        return $fields_value;
    }

    protected function renderQuotationStatusForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'send_email_on' => $this->getFieldValue($obj, 'send_email'),
        );

        if ($this->getFieldValue($obj, 'color') !== false) {
            $this->fields_value['color'] = $this->getFieldValue($obj, 'color');
        } else {
            $this->fields_value['color'] = '#ffffff';
        }

        $this->fields_value['customer_pdf_ids[]'] = array();
        $this->fields_value['admin_pdf_ids[]'] = array();

        return parent::renderForm();
    }

    public function sendEmail()
    {
    }

    protected function getTemplates()
    {
        $theme = new Theme($this->context->shop->id_theme);
        $default_path = '../mails/';
        $theme_path = '../themes/' . $theme->directory . '/mails/';

        $array = array();
        foreach (Language::getLanguages(true) as $language) {
            $iso_code = $language['iso_code'];
            if (!@filemtime(_PS_ADMIN_DIR_ . '/' . $default_path . $iso_code) &&
                !@filemtime(_PS_ADMIN_DIR_ . '/' . $theme_path . $iso_code)) {
                continue;
            }

            $theme_templates_dir = _PS_ADMIN_DIR_ . '/' . $theme_path . $iso_code;
            $theme_templates = is_dir($theme_templates_dir) ? scandir($theme_templates_dir) : array();
            $templates = array_unique(
                array_merge(scandir(_PS_ADMIN_DIR_ . '/' . $default_path . $iso_code), $theme_templates)
            );
            foreach ($templates as $template) {
                if (!strncmp(strrev($template), 'lmth.', 5)) {
                    $search_result = array_search($template, $theme_templates);
                    $array[$iso_code][] = array(
                        'id' => Tools::substr($template, 0, -5),
                        'name' => Tools::substr($template, 0, -5),
                        'folder' => ((!empty($search_result) ? $theme_path : $default_path)),
                    );
                }
            }
        }

        return $array;
    }

    public function ajaxProcessSendemailroja45QuotationsproStatus()
    {
        $id = (int) Tools::getValue($this->identifier);
        $sql =
        'UPDATE ' . _DB_PREFIX_ . 'roja45_quotationspro_status
            SET `send_email`= NOT `send_email`
            WHERE ' . $this->identifier . '=' . $id;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            $json = json_encode(array(
                'success' => 1,
                'text' => $this->l('Updated successfully.', 'QuotationStatuses'),
            ));
            die($json);
        } else {
            $json = json_encode(array(
                'success' => 0,
                'text' => $this->l('An error occurred while updating this value.', 'QuotationStatuses'),
            ));
            die($json);
        }
    }

    public function ajaxProcessNotifyadminroja45QuotationsproStatus()
    {
        $id = (int) Tools::getValue($this->identifier);
        $sql =
        'UPDATE ' . _DB_PREFIX_ . 'roja45_quotationspro_status
            SET `notify_admin`= NOT `notify_admin`
            WHERE ' . $this->identifier . '=' . $id;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            $json = json_encode(array(
                'success' => 1,
                'text' => $this->l('Updated successfully.', 'QuotationStatuses'),
            ));
            die($json);
        } else {
            $json = json_encode(array(
                'success' => 0,
                'text' => $this->l('An error occurred while updating this value.', 'QuotationStatuses'),
            ));
            die($json);
        }
    }
}
