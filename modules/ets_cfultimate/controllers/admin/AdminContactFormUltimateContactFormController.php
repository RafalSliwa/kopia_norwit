<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_ . 'ets_cfultimate/classes/ETS_CFU_Pagination.php');

class AdminContactFormUltimateContactFormController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        if (($conf = Tools::getValue('conf')) && !(isset($this->context->controller->_conf[$conf])) && $conf == 99) {
            $this->context->controller->_conf[$conf] = $this->module->l('Imported successfully');
        }
    }

    public function initContent()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        parent::initContent();

        if (Tools::getValue('check_apikey')) {
            $id_contact = (int)Tools::getValue('id_contact', null);
            $contact = new ETS_CFU_Contact($id_contact);
            $api_key_mailchimp = Tools::getValue('api_key', $contact->mailchimp_api_key);
            if (!$api_key_mailchimp) {
                $status_error = true;
                $statusMes = $this->l('API key is required');
            } else {
                $api_connect = EtsCfuMailchimpApi::getInstance($api_key_mailchimp);
                $status_connect = $api_connect->request('GET');
                $status_error = (!$status_connect || (isset($status_connect->status) && $status_connect->status == '401')) ? true : false;
                $statusMes = $status_error ? $this->l('Your API key is invalid. Please check your key again.') : $this->l('Your API key is correct');
            }
            die(json_encode(
                array(
                    'error' => $status_error ? true : false,
                    'messageType' => $status_error ? 'error' : 'success',
                    'message' => $statusMes,
                )
            ));
        }
        if (Tools::getValue('setup_mailchimp')) {
            $api_key_mailchimp = Tools::getValue('api_key');
            $list_mailchimp = [];
            if (!$api_key_mailchimp) {
                $status_error = true;
                $statusMes = $this->l('API key is required');
            } else {
                $res = EtsCfuMailchimp::getInstance()->getAudiences($api_key_mailchimp);
                if ($res && isset($res['connect_status']) && $res['connect_status'] === true && !empty($res['data_list']) && is_array($res['data_list'])) {
                    foreach ($res['data_list'] as $key => $label) {
                        $list_mailchimp[] = [
                            'id' => $key,
                            'label' => $label,
                        ];
                    }
                }
                $status_error = !$res['connect_status'];
                $statusMes = $status_error ? $this->l('Your API key is invalid. Please check your key again.') : $this->l('Your API key is correct');
            }
            die(json_encode(
                array(
                    'error' => $status_error ? true : false,
                    'messageType' => $status_error ? 'error' : 'success',
                    'message' => $statusMes,
                    'mailchimp_audience' => $list_mailchimp
                )
            ));
        }
        if (Tools::getValue('get_mailchimp_audience')) {
            $api_key = Tools::getValue('api_key');
            $list_id = Tools::getValue('list_id');
            $merge_fields = EtsCfuMailChimp::getInstance()->getMergeFields($api_key, $list_id);
            die(json_encode(array(
                    'merge_fields' => $merge_fields
                )
            ));
        }
        if (Tools::getValue('action') == 'etsCfuUpdateContactFormOrdering' && $formcontact = Tools::getValue('formcontact')) {
            $page = Tools::getValue('page', 1);
            foreach ($formcontact as $key => $form) {
                $position = $key + ($page - 1) * 20;
                ETS_CFU_Contact::updatePosition($position, $form);
            }
            die(
            json_encode(
                array(
                    'page' => $page,
                )
            )
            );
        }
    }

    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $filter = '';
        $values_submit = array();
        $id_contact = trim(Tools::getValue('id_contact'));
        if ($id_contact !== '' && Validate::isUnsignedInt($id_contact) && (int)$id_contact > 0) {
            $filter .= ' AND c.`id_contact`="' . (int)$id_contact . '"';
            $values_submit['id_contact'] = (int)$id_contact;
        }
        $contact_title = trim(Tools::getValue('contact_title'));
        if ($contact_title !== '' && Validate::isCleanHtml($contact_title)) {
            $filter .= ' AND cl.title LIKE "%' . pSQL($contact_title) . '%"';
            $values_submit['contact_title'] = $contact_title;
        }
        $hook = trim(Tools::getValue('hook'));
        if ($hook !== '' && Validate::isHookName($hook)) {
            $filter .= ' AND c.hook LIKE "%' . pSQL($hook) . '%"';
            $values_submit['hook'] = $hook;
        }
        $dateAddFrom = trim(Tools::getValue('messageFilter_dateadd_from'));
        if ($dateAddFrom !== '' && Validate::isDate($dateAddFrom)) {
            $filter .= ' AND c.date_add >="' . pSQL($dateAddFrom) . '"';
            $values_submit['messageFilter_dateadd_from'] = $dateAddFrom;
        }
        $dateAddTo = trim(Tools::getValue('messageFilter_dateadd_to'));
        if ($dateAddTo !== '' && Validate::isDate($dateAddTo)) {
            $filter .= ' AND c.date_add <= "' . pSQL($dateAddTo) . '"';
            $values_submit['messageFilter_dateadd_to'] = $dateAddTo;
        }
        $saveMessage = trim(Tools::getValue('save_message'));
        if ($saveMessage != '' && Validate::isUnsignedInt($saveMessage)) {
            $filter .= ' AND c.save_message = "' . (int)$saveMessage . '"';
            $values_submit['save_message'] = (int)$saveMessage;
        }
        $activeContact = trim(Tools::getValue('active_contact'));
        if ($activeContact != '' && Validate::isUnsignedInt($activeContact)) {
            $filter .= ' AND c.active = "' . (int)$activeContact . '"';
            $values_submit['active_contact'] = (int)$activeContact;
        }
        $sort = trim(Tools::getValue('sort'));
        if ($sort === 'id_contact') {
            $sort = 'c.id_contact';
        } elseif ($sort == '' || !Validate::isOrderBy($sort))
            $sort = 'position';
        $sort_type = trim(Tools::getValue('sort_type'));
        if ($sort_type == '' || !Validate::isOrderWay($sort_type))
            $sort_type = 'ASC';
        $total = ETS_CFU_Contact::getContactList(false, $filter, 0, 0, true);
        $limit = 20;
        $page = Tools::getValue('page', 1);
        $start = ($page - 1) * $limit;
        $pagination = new ETS_CFU_Pagination();
        $pagination->url = $this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true) . http_build_query($values_submit) . '&page=_page_';
        $pagination->limit = $limit;
        $pagination->page = $page;
        $pagination->total = $total;
        $pagination->controller = Tools::getValue('controller');
        $contacts = ETS_CFU_Contact::getContactList(false, $filter, $start, $limit, false, $sort, $sort_type);
        if ($contacts) {
            foreach ($contacts as &$contact) {
                $contact['hooks'] = explode(',', $contact['hook']);
                if ($contact['enable_form_page'])
                    $contact['link'] = Ets_CfUltimate::getLinkContactForm($contact['id_contact']);
                $contact['count_views'] = ETS_CFU_Contact::countLog($contact['id_contact']);
                $contact['count_message'] = ETS_CFU_Contact_Message::countMessage($contact['id_contact']);
            }
        }
        $hooks = array(
            'nav_top' => $this->module->l('Header - top navigation'),
            'header' => $this->module->l('Header - main header'),
            'displayTop' => $this->module->l('Top'),
            'home' => $this->module->l('Home'),
            'left_column' => $this->module->l('Left column'),
            'footer' => $this->module->l('Footer'),
            'right_column' => $this->module->l('Right column'),
            'product_thumbs' => $this->module->l('Product page - below product image'),
            'product_right' => $this->module->l('Product page - right column'),
            'product_left' => $this->module->l('Product page - left column'),
            'checkout_page' => $this->module->l('Checkout page'),
            'register_page' => $this->module->l('Register page'),
            'login_page' => $this->module->l('Login page'),
        );
        $this->context->smarty->assign(
            array(
                'ets_cfu_contacts' => $contacts,
                'url_module' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->module->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->module->name,
                'ets_cfu_pagination_text' => $pagination->render(),
                'filter' => $filter,
                'filter_params' => http_build_query($values_submit),
                'is_ps15' => version_compare(_PS_VERSION_, '1.6', '<') ? true : false,
                'etsCfuOkImport' => Tools::getValue('etsCfuOkImport'),
                'hooks' => $hooks,
                'sort' => Tools::getValue('sort', 'position'),
                'sort_type' => Tools::getValue('sort_type', 'asc'),
                '_PS_JS_DIR_' => _PS_JS_DIR_,
                'ETS_CFU_ENABLE_TMCE' => Configuration::get('ETS_CFU_ENABLE_TMCE'),
                'showShortcodeHook' => Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE')
            )
        );
        return $this->module->display($this->module->getLocalPath(), 'list-contact.tpl');
    }
}