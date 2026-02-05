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

if (!defined('_PS_VERSION_')) {
    exit;
}

header('Access-Control-Allow-Origin: *');

if (!defined('_ETS_MODULE_')) {
    define('_ETS_MODULE_', 'ets_cfultimate');
}

require_once(dirname(__FILE__) . '/classes/function.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_SmartyCache.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Tools.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Translate.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Data_Provider.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Form_Tag.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Contact_Reply.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Contact.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Contact_Form.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Form_Tag_Manager.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Contact_Message.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Submission.php');
require_once(dirname(__FILE__) . '/classes/mail.php');
require_once(dirname(__FILE__) . '/classes/pipe.php');
require_once(dirname(__FILE__) . '/classes/integration.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Recaptcha.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Validation.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Link.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Browser.php');
require_once(dirname(__FILE__) . '/defines.php');
require_once(dirname(__FILE__) . '/classes/ETS_CFU_Presenter.php');
require_once(dirname(__FILE__) . '/classes/EtsCfuMailchimpApi.php');
require_once(dirname(__FILE__) . '/classes/EtsCfuMailChimp.php');

class Ets_cfultimate extends Module
{
    const DEFAULT_MAX_SIZE = 104857600;
    public $_html;
    public $_ps17;

    public $rmHookShortcode = false;

    public static $translation;
    public $secure_key;

    public function __construct()
    {
        $this->name = 'ets_cfultimate';
        $this->tab = 'front_office_features';
        $this->version = '1.4.5';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->module_key = '4bfac04a2e06bdebd1fb9c03ce95409b';
        if (version_compare(_PS_VERSION_, '1.7', '>='))
            $this->_ps17 = true;
        parent::__construct();
        $this->displayName = $this->l('Contact Form Ultimate');
        $this->description = $this->l('Visual drag and drop contact form builder for Prestashop. Create any kind of contact form you want.');
$this->refs = 'https://prestahero.com/';
        if (Tools::getValue('action') == 'etsCfuGetCountMessageContactForm') {
            die(json_encode(array(
                'count' => ETS_CFU_Contact_Message::getCountUnreadMessage(),
            )));
        }
        self::$translation = array(
            'email_is_black_list' => $this->l('This email address is in blacklist'),
        );
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return $this->fixOverrideConflict() && parent::install()
            && $this->_registerHook()
            && $this->_installDbConfig()
            && $this->_installTabs()
            && $this->initContact()
            && $this->createTemplateMail();
    }

    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $this->context->shop->domain . $this->context->shop->getBaseURI();
    }

    public static function getEmailToString($string)
    {
        preg_match_all('/(?:[a-zA-Z0-9\._\-]+@[a-zA-Z0-9\._\-]+\.[a-zA-Z0-9_\-]+)/i', $string, $matches);
        return isset($matches[0][0]) ? $matches[0][0] : '';
    }

    public function smartyBackOffice($args = array())
    {
        $this->smarty->assign($args);
        return $this->display(__FILE__, 'defines.tpl');
    }
    public function fixOverrideConflict(){
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class= 'Ets_Cfultimate_overrideUtil';
        $method = 'resolveConflict';
        call_user_func_array(array($class, $method),array($this));
        return true;
    }
    public function uninstallOverrides(){
        if(parent::uninstallOverrides())
        {
            require_once(dirname(__FILE__) . '/classes/OverrideUtil');
            $class= 'Ets_Cfultimate_overrideUtil';
            $method = 'restoreReplacedMethod';
            call_user_func_array(array($class, $method),array($this));
            return true;
        }
        return false;
    }
    public function installOverrides()
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class= 'Ets_Cfultimate_overrideUtil';
        if(parent::installOverrides())
        {
            call_user_func_array(array($class, 'onModuleEnabled'),array($this));
            return true;
        }
        return false;
    }
    public static function checkEmailBlackLists($email)
    {
        $listipemails = explode("\n", Configuration::get('ETS_CFU_IP_BLACK_LIST'));
        if (count($listipemails)) {
            for ($i = 0; $i < count($listipemails); $i++) {
                if (($emailCheck = trim($listipemails[$i])) && trim($email) == $emailCheck) {
                    return self::$translation['email_is_black_list'];
                }
            }
        }
        return false;
    }

    public function getContent()
    {
        if (!$this->active) {
            $this->_html .= $this->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
            return $this->_html;
        }
        if (version_compare(_PS_VERSION_, '1.6', '<'))
            $this->context->controller->addJqueryUI('ui.widget');
        else
            $this->context->controller->addJqueryPlugin('widget');
        $this->context->controller->addJqueryPlugin('tagify');

        if (Tools::isSubmit('etsCfuExportContactForm'))
            $this->generateArchive();
        if (Tools::isSubmit('etsCfuGetFormElementAjax')) {
            die(json_encode(
                array(
                    'form_html' => $this->replace_all_form_tags(Tools::getValue('short_code')),
                )
            ));
        }
        if (Tools::isSubmit('contactFormUltimateDefault')) {
            if ((int)Tools::getValue('contactFormUltimateDefault') == 1 && Tools::getValue('id_contact')) {
                Configuration::updateValue('ETS_CONTACT_FORM_ULTIMATE_DEFAULT', Tools::getValue('id_contact'));
            } elseif ((int)Tools::getValue('contactFormUltimateDefault') == 0 && Configuration::get('ETS_CONTACT_FORM_ULTIMATE_DEFAULT') == Tools::getValue('id_contact')) {
                Configuration::updateValue('ETS_CONTACT_FORM_ULTIMATE_DEFAULT', 0);
            }
        } elseif (Tools::isSubmit('etsCfuSaveMessageUpdate') && $id_contact = Tools::getValue('id_contact')) {
            ETS_CFU_Contact::updateSaveMessage($id_contact, (int)Tools::getValue('etsCfuSaveMessageUpdate'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true));
        } elseif (Tools::isSubmit('etsCfuSubmitSaveContact') || Tools::isSubmit('etsCfuSubmitSaveAndStayContact')) {
            $this->_html .= $this->saveContactFrom();
        } elseif (Tools::isSubmit('etsCfuDuplicateContact') && $id_contact = Tools::getValue('id_contact')) {
            $contact = new ETS_CFU_Contact($id_contact);
            $languages = Language::getLanguages(false);
            $identity = Tools::passwdGen(2, 'NUMERIC');
            foreach ($languages as $language) {
                $contact->title[$language['id_lang']] = $contact->title[$language['id_lang']] . ' [' . $this->l('Duplicated') . ']';
                $contact->title_alias[$language['id_lang']] = $contact->title_alias[$language['id_lang']] . '-' . $identity;
            }
            $contact->position = ETS_CFU_Contact::getPosition($this->context->shop->id);
            if ($contact->add()) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true) . '&conf=19');
            }
        } elseif (Tools::isSubmit('etsCfuDeleteContact') && $id_contact = Tools::getValue('id_contact')) {
            $contact = new ETS_CFU_Contact($id_contact);
            if ($contact->delete()) {
                ETS_CFU_SmartyCache::clearCacheAllSmarty('*', 'contact|' . $contact->id);
            }
            ETS_CFU_Contact::deleteContactShop($id_contact);
            $contacts = ETS_CFU_Contact::getContacts($this->context->shop->id, '*', 'c.position');
            $messages = ETS_CFU_Contact_Message::getAttachmentsMessages($id_contact);
            if ($messages) {
                foreach ($messages as $message) {
                    if ($message['attachments']) {
                        foreach (explode(',', $message['attachments']) as $attachment) {
                            $filename = _PS_DOWNLOAD_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $attachment;
                            if (file_exists($filename))
                                @unlink($filename);
                        }
                    }
                }
            }
            if ($contacts) {
                foreach ($contacts as $key => $contact) {
                    ETS_CFU_Contact::updatePosition($key, (int)$contact['id_contact']);
                }
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true) . '&conf=1');
        } elseif (Tools::isSubmit('etsCfuPreview') && $id_contact = Tools::getValue('id_contact')) {
            $contact = new ETS_CFU_Contact($id_contact, $this->context->language->id);
            die(json_encode(array(
                'form_html' => $this->replace_all_form_tags($contact->short_code),
                'contact' => $contact,
            )));
        } elseif (Tools::isSubmit('etsCfuActiveUpdate') && $id_contact = Tools::getValue('id_contact')) {
            ETS_CFU_Contact::setActive($id_contact, (int)Tools::getValue('etsCfuActiveUpdate'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true));
        }
        if (Tools::isSubmit('etsCfuEditContact') || Tools::isSubmit('etsCfuAddContact')) {
            Media::addJsDef([
                'ERROR_MAX_UPLOAD_FILE' => $this->l('The size of upload file must be less than or equal to %s MB', ETS_CFU_Tools::formatBytes(ETS_CFU_Tools::getPostMaxSizeBytes()))
            ]);
            $assign = array(
                'link' => $this->context->link,
                'link_basic' => $this->getBaseLink(),
                'mod_dr_ctf' => $this->_path,
                'ETS_CFU_ENABLE_TMCE' => Configuration::get('ETS_CFU_ENABLE_TMCE'),
                'languages' => $this->context->controller->getLanguages(),
                'defaultFormLanguage' => Configuration::get('PS_LANG_DEFAULT'),
                'img_dir' => $this->_path . 'views/img/',
                'max_upload_file' => ETS_CFU_Tools::formatBytes(ETS_CFU_Tools::getPostMaxSizeBytes()),
            );

            if (Configuration::get('ETS_CFU_ENABLE_RECAPTCHA')) {
                $assign['re_captcha_v3'] = Configuration::get('ETS_CFU_RECAPTCHA_TYPE') != 'v2';
            }

            $this->context->smarty->assign(
                array(
                    'showShortcodeHook' => Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE')
                )
            );

            $this->smarty->assign($assign);
            $this->_html .= $this->renderAddContactForm();
            $this->_html .= $this->display(__FILE__, 'url.tpl');
            $this->_html .= $this->display(__FILE__, 'textarea.tpl');
            $this->_html .= $this->display(__FILE__, 'text.tpl');
            $this->_html .= $this->display(__FILE__, 'telephone.tpl');
            $this->_html .= $this->display(__FILE__, 'submit.tpl');
            $this->_html .= $this->display(__FILE__, 'select.tpl');
            $this->_html .= $this->display(__FILE__, 'radio.tpl');
            $this->_html .= $this->display(__FILE__, 'quiz.tpl');
            $this->_html .= $this->display(__FILE__, 'number.tpl');
            $this->_html .= $this->display(__FILE__, 'email.tpl');
            $this->_html .= $this->display(__FILE__, 'checkbox.tpl');
            $this->_html .= $this->display(__FILE__, 'captcha.tpl');
            $this->_html .= $this->display(__FILE__, 'recaptcha.tpl');
            $this->_html .= $this->display(__FILE__, 'acceptance.tpl');
            $this->_html .= $this->display(__FILE__, 'date.tpl');
            $this->_html .= $this->display(__FILE__, 'file.tpl');
            $this->_html .= $this->display(__FILE__, 'html.tpl');
            $this->_html .= $this->display(__FILE__, 'password.tpl');
            $this->_html .= $this->display(__FILE__, 'referrence.tpl');
        } else {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateDashboard', true));
        }
        $this->smarty->assign(array(
            'html_content' => $this->_html,
            'etsCfuOkImport' => Tools::getValue('etsCfuOkImport'),
            '_PS_JS_DIR_' => _PS_JS_DIR_,
            'ETS_CFU_ENABLE_TMCE' => Configuration::get('ETS_CFU_ENABLE_TMCE'),
        ));
        return $this->display(__FILE__, 'admin.tpl');
    }

    private function generateArchive()
    {
        $zip = new ZipArchive();
        $cacheDir = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
        if (!@is_dir($cacheDir))
            @mkdir($cacheDir, 0755);
        $zip_file_name = 'contact_form_ultimate_' . date('dmYHis') . '.zip';
        if ($zip->open($cacheDir . $zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE) === true) {
            if (!$zip->addFromString('Data-Info.xml', $this->renderDataInfo())) {
                $this->_errors[] = $this->l('Cannot create Contact-Info.xml');
            }
            if (!$zip->addFromString('Contact-Info.xml', $this->renderContactFormData())) {
                $this->_errors[] = $this->l('Cannot create Contact-Info.xml');
            }
            $zip->close();
            if (!is_file($cacheDir . $zip_file_name)) {
                $this->_errors[] = $this->l(sprintf('Could not create %1s', $cacheDir . $zip_file_name));
            }
            if (!$this->_errors) {
                if (ob_get_length() > 0) {
                    ob_end_clean();
                }
                ob_start();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: public');
                header('Content-Description: File Transfer');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
                header('Content-Transfer-Encoding: binary');
                ob_end_flush();
                readfile($cacheDir . $zip_file_name);
                if (file_exists($cacheDir . $zip_file_name))
                    @unlink($cacheDir . $zip_file_name);
                exit;
            }
        }
        {
            echo $this->l('An error occurred during the archive generation');
            die;
        }
    }

    private function renderDataInfo()
    {
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml_output .= ETS_CFU_Tools::displayText("\n" . ETS_CFU_Tools::displayText($this->version, 'version') . "\n", 'entity_profile') . "\n";
        return $xml_output;
    }

    private function renderContactFormData()
    {
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $contacts = ETS_CFU_Contact::getContacts($this->context->shop->id, 'c.*');
        $builder_xml_output = '';
        if ($contacts) {
            foreach ($contacts as $contact) {
                $builder_xml_output_contact = '';
                foreach ($contact as $key => $value) {
                    if ($key != 'id_contact') {
                        $builder_xml_output_contact .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>' . "\n";
                    }
                }
                $contactLanguages = ETS_CFU_Contact::getContactsLang($contact['id_contact']);
                if ($contactLanguages) {
                    foreach ($contactLanguages as $datalanguage) {
                        $language_attrs = ['iso_code' => $datalanguage['iso_code']];
                        if ($datalanguage['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
                            $language_attrs['default'] = 1;
                        }
                        $builder_xml_output_language = '';
                        foreach ($datalanguage as $key => $value) {
                            if ($key != 'id_contact' && $key != 'id_lang' && $key != 'iso_code') {
                                $builder_xml_output_language .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>' . "\n";
                            }
                        }
                        $builder_xml_output_contact .= ETS_CFU_Tools::displayText($builder_xml_output_language, 'datalanguage', $language_attrs) . "\n";
                    }
                }
                $builder_xml_output .= ETS_CFU_Tools::displayText($builder_xml_output_contact, 'contactfrom', ['id' => (int)$contact['id_contact']]) . "\n";
            }
        }
        $xml_output .= ETS_CFU_Tools::displayText($builder_xml_output, 'entity_profile') . "\n";
        return $xml_output;
    }

    public function replace_all_form_tags($form)
    {
        $manager = ETS_CFU_Form_Tag_Manager::get_instance();
        $manager->set_instance();
        if (ets_cfu_autop_or_not()) {
            $form = $manager->normalize($form);
            $form = ets_cfu_autop($form);
        }
        $form = $manager->replace_all($form);
        $this->scanned_form_tags = $manager->get_scanned_tags();
        return $form;
    }

    public function saveContactFrom()
    {
        $errors = array();
        $post_content_size = ETS_CFU_Tools::getServerVars('CONTENT_LENGTH');
        $post_max_size = ETS_CFU_Tools::getPostMaxSizeBytes();
        if ($post_content_size > $post_max_size) {
            return $this->displayError([sprintf($this->l('The uploaded file(s) exceeds the post_max_size directive in php.ini (%s > %s)'), ETS_CFU_Tools::formatBytes($post_content_size), ETS_CFU_Tools::formatBytes($post_max_size))]);
        }
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $configs = Ets_cfudefines::getInstance($this)->getFields('contact')['form']['input'];
        $current_tab = trim(Tools::getValue('current_tab'));
        if ($configs) {
            foreach ($configs as $config) {
                $key = $config['name'];
                if (trim(Tools::getValue('thank_you_page')) == 'thank_page_url' && ($key == 'thank_you_alias' || $key == 'thank_you_message' || $key == 'thank_you_page_title')) {
                    continue;
                }
                if ((int)Tools::getValue('mailchimp_enabled') == 0 && $key == 'mailchimp_api_key') {
                    continue;
                }
                if ((int)Tools::getValue('mailchimp_enabled') == 1 && $key == 'mailchimp_api_key' && ($api_key_mailchimp = trim(Tools::getValue($key))) !== '') {
                    $api_connect = EtsCfuMailchimpApi::getInstance($api_key_mailchimp);
                    $status_connect = $api_connect->request('GET');
                    if (!$status_connect || (isset($status_connect->status) && $status_connect->status == '401')) {
                        $errors[] = $config['label'] . ' ' . $this->l('is invalid. Please check your key again.');
                    }
                    continue;
                }
                if (trim($key) == 'condition') {
                    if (($if = Tools::getValue('if', [])) && is_array($if) && count($if) > 0) {
                        $operator = Tools::getValue('operator', []);
                        $value = Tools::getValue('value', []);
                        $do = Tools::getValue('do', []);
                        $fields = Tools::getValue('fields', []);
                        if (count(array_diff_key($if, $operator, $value, $do, $fields)) > 0) {
                            $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                        }
                    }
                }
                if (isset($config['type']) && $config['type'] == 'file') {
                    if (isset($config['required']) && $config['required'] && !isset($_FILES[$key]['size'])) {
                        $errors[] = $config['label'] . ' ' . $this->l('is required');
                    } elseif (isset($_FILES[$key]) && isset($_FILES[$key]['size'])) {
                        $this->validateUpload($key, $config, (isset($config['base_url']) ? $config['base_url'] : null), $errors);
                    }
                } else {
                    if (isset($config['lang']) && $config['lang']) {
                        $languages = Language::getLanguages(false);
                        if ($key == 'thank_you_url') {
                            if (trim(Tools::getValue('thank_you_page')) == 'thank_page_url') {
                                if (!Tools::getValue($key . '_' . $id_lang_default)) {
                                    $errors[] = $config['label'] . ' ' . $this->l('is required');
                                } elseif (!Validate::isAbsoluteUrl(trim(Tools::getValue($key . '_' . $id_lang_default)))) {
                                    $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                                }
                            }
                            continue;
                        }
                        if (trim(Tools::getValue('thank_you_page')) == 'thank_page_default' && ($key == 'thank_you_message' || $key == 'thank_you_page_title')) {
                            if (!trim(Tools::getValue($key . '_' . $id_lang_default))) {
                                $errors[] = $config['label'] . ' ' . $this->l('thank you page') . ' ' . $this->l('is required');
                            }
                            continue;
                        }
                        if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key . '_' . $id_lang_default) == '')) {
                            $errors[] = $config['label'] . ' ' . $this->l('is required');
                        } elseif (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
                            $validate = $config['validate'];
                            if (!Validate::$validate(trim(Tools::getValue($key . '_' . $id_lang_default))))
                                $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                            else {
                                if ($languages) {
                                    foreach ($languages as $lang) {
                                        if (Tools::getValue($key . '_' . $lang['id_lang']) && !Validate::$validate(trim(Tools::getValue($key . '_' . $lang['id_lang']))))
                                            $errors[] = $config['label'] . ' ' . $lang['iso_code'] . ' ' . $this->l('is invalid');
                                    }
                                }
                            }
                            unset($validate);
                        }
                    } elseif (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim(Tools::getValue($key) == '')) {
                        $errors[] = $config['label'] . ' ' . $this->l('is required');
                    } elseif (!is_array(Tools::getValue($key)) && trim(Tools::getValue($key)) !== '' && isset($config['validate']) && method_exists('Validate', $config['validate'])) {
                        $validate = $config['validate'];
                        if (!isset($config['ref']) || (trim($config['ref']) == 'button_popup_enabled' && (int)Tools::getValue('button_popup_enabled') == 1 || trim($config['ref']) == 'open_form_by_button' && (int)Tools::getValue('open_form_by_button') == 1)) {
                            if (method_exists('ETS_CFU_Tools', $validate) && !ETS_CFU_Tools::$validate(trim(Tools::getValue($key)))) {
                                $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                            }
                        } else {
                            if (!Validate::$validate(trim(Tools::getValue($key))))
                                $errors[] = $config['label'] . ' ' . $this->l('is invalid');
                        }
                        unset($validate);
                    }
                }
            }
        }

        if (Tools::getValue('enable_form_page')) {
            foreach ($languages as $language) {
                if (($title_alias = Tools::getValue('title_alias_' . $language['id_lang'])) && !Validate::isLinkRewrite($title_alias)) {
                    $errors[] = sprintf($this->l('Contact form alias (%s) is invalid'), $language['iso_code']);
                } elseif ($title_alias && ($alias = ETS_CFU_Contact::getIdContactByAlias($title_alias, (int)$language['id_lang'])) && $alias != Tools::getValue('id_contact')) {
                    $errors[] = sprintf($this->l('Contact form alias (%s) is exists.'), $language['iso_code']);
                }
            }
        }

        if (trim(Tools::getValue('thank_you_page')) == 'thank_page_default') {
            foreach ($languages as $language) {
                $thank_you_alias = Tools::getValue('thank_you_alias_' . $language['id_lang']);
                if ($thank_you_alias && !Validate::isLinkRewrite($thank_you_alias)) {
                    $errors[] = sprintf($this->l('Thank page alias (%s) is invalid.'), $language['iso_code']);
                }
            }
        }

        if (!$errors) {
            if ($id_contact = Tools::getValue('id_contact')) {
                $contact = new ETS_CFU_Contact($id_contact);
            } else {
                $contact = new ETS_CFU_Contact();
                $contact->position = ETS_CFU_Contact::getPosition($this->context->shop->id);
            }
            $contact->id_employee = (int)$this->context->employee->id;
            if ($configs) {
                foreach ($configs as $config) {
                    $key = $config['name'];
                    if (trim($key) == 'condition') {
                        $operator = Tools::getValue('operator', []);
                        $if = Tools::getValue('if', []);
                        $value = Tools::getValue('value', []);
                        $do = Tools::getValue('do', []);
                        $fields = Tools::getValue('fields', []);
                        $fields_form = Tools::getValue('condition_fields_form', '{}');
                        $condition = [
                            'fields_form' => $fields_form,
                            'if' => $if,
                            'operator' => $operator,
                            'value' => $value,
                            'do' => $do,
                            'fields' => $fields,
                        ];
                        $contact->condition = json_encode($condition);
                        continue;
                    }
                    if (trim($key) === 'mailchimp_mapping_data' && ($mailchimp_merge_field = Tools::getValue('mailchimp_merge_field', []))) {
                        $mailchimp_api_key = Tools::getValue('mailchimp_api_key');
                        $mailchimp_audience = Tools::getValue('mailchimp_audience');
                        $mailchimp_merge_fields = [];
                        if ($mailchimp_api_key !== '' && $mailchimp_audience != '') {
                            $merge_fields = EtsCfuMailChimp::getInstance()->getMergeFields($contact->mailchimp_api_key, $contact->mailchimp_audience);
                            foreach ($mailchimp_merge_field as $tagName => $field) {
                                if (isset($merge_fields[$field]) && $merge_fields[$field]) {
                                    $mailchimp_merge_fields[$tagName] = $merge_fields[$field];
                                }
                            }
                        }
                        $contact->mailchimp_mapping_data = json_encode($mailchimp_merge_fields);
                        continue;
                    }
                    if (trim(Tools::getValue('thank_you_page')) == 'thank_page_url' && ($key == 'thank_you_page_title' || $key == 'thank_you_message')) {
                        continue;
                    }
                    if (trim(Tools::getValue('thank_you_page')) == 'thank_page_default' && $key == 'thank_you_url') {
                        continue;
                    }
                    if (isset($config['ref']) && (trim($config['ref']) == 'button_popup_enabled' && (int)Tools::getValue('button_popup_enabled') == 0 || trim($config['ref']) == 'open_form_by_button' && (int)Tools::getValue('open_form_by_button') == 0)) {
                        continue;
                    }
                    if (isset($config['lang']) && $config['lang']) {
                        $values = array();
                        foreach ($languages as $lang) {
                            if ($config['type'] == 'switch')
                                $values[$lang['id_lang']] = (int)trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? 1 : 0;
                            else
                                $values[$lang['id_lang']] = trim(Tools::getValue($key . '_' . $lang['id_lang'])) ? trim(Tools::getValue($key . '_' . $lang['id_lang'])) : trim(Tools::getValue($key . '_' . $id_lang_default));
                        }
                        $contact->$key = $values;
                    } elseif ($config['type'] == 'switch') {
                        $contact->$key = (int)Tools::getValue($key) ? 1 : 0;
                    } elseif ($config['type'] == 'file') {
                        if (!is_dir(_PS_IMG_DIR_ . $this->name)) {
                            mkdir(_PS_IMG_DIR_ . $this->name, 0755);
                        }
                        if (isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                            $salt = Tools::substr(sha1(microtime()), 0, 10);
                            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                            $imageName = @file_exists(_PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . Tools::strtolower($_FILES[$key]['name'])) || Tools::strtolower($_FILES[$key]['name']) == $contact->$key ? $salt . '-' . Tools::strtolower($_FILES[$key]['name']) : Tools::strtolower($_FILES[$key]['name']);
                            $fileName = _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $imageName;
                            if (file_exists($fileName)) {
                                $errors[] = $config['label'] . ' ' . $this->l('File name already exists. Try to rename the file and upload again');
                            } else {
                                $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                if (!$errors && isset($_FILES[$key]) &&
                                    !empty($_FILES[$key]['tmp_name']) &&
                                    !empty($imagesize) &&
                                    in_array($type, array('jpg', 'gif', 'jpeg', 'png', 'webp'))
                                ) {
                                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                    if ($error = ImageManager::validateUpload($_FILES[$key]))
                                        $errors[] = $error;
                                    elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                        $errors[] = $this->l('Cannot upload file');
                                    elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                                        $errors[] = $this->l('An error occurred during the image upload process.');
                                    if (file_exists($temp_name))
                                        @unlink($temp_name);
                                    if (!$errors) {
                                        if ($contact->$key != '') {
                                            $oldImage = _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $contact->$key;
                                            if (file_exists($oldImage))
                                                @unlink($oldImage);
                                        }
                                        $contact->$key = $imageName;
                                    }
                                }
                            }
                        } elseif ((int)Tools::getValue($key . '_delete') > 0) {
                            if ($contact->$key !== '' && @file_exists(($file = _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR . $contact->$key))) {
                                @unlink($file);
                            }
                            $contact->$key = '';
                        }
                    } elseif ($config['type'] == 'categories' && isset($config['tree']['use_checkbox']) && $config['tree']['use_checkbox'])
                        $contact->$key = implode(',', Tools::getValue($key));
                    elseif ($config['type'] == 'group') {
                        $contact->$key = implode(',', Tools::getValue($key));
                    } elseif ($config['type'] == 'checkbox') {
                        $values = array();
                        foreach ($config['values']['query'] as $value) {
                            if (Tools::getValue($key . '_' . $value['id'])) {
                                $values[] = Tools::getValue($key . '_' . $value['id']);
                            }
                        }
                        $contact->$key = implode(',', $values);
                    } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                        $contact->$key = implode(',', Tools::getValue($key));
                    } else
                        $contact->$key = trim(Tools::getValue($key));
                }
                $values_title_alias = array();
                $values_title_thank = array();
                foreach ($languages as $lang) {
                    if (!Tools::getValue('title_alias_' . $lang['id_lang']) && !Tools::getValue('title_alias_' . $id_lang_default)) {
                        $values_title_alias[$lang['id_lang']] = trim(Tools::getValue('title_' . $lang['id_lang'])) ? Tools::link_rewrite(trim(Tools::getValue('title_' . $lang['id_lang']))) : Tools::link_rewrite(trim(Tools::getValue('title_' . $id_lang_default)), true);
                    } else
                        $values_title_alias[$lang['id_lang']] = trim(Tools::getValue('title_alias_' . $lang['id_lang'])) ? trim(Tools::getValue('title_alias_' . $lang['id_lang'])) : trim(Tools::getValue('title_alias_' . $id_lang_default));

                    if (!Tools::getValue('thank_you_alias_' . $lang['id_lang']) && !Tools::getValue('thank_you_alias_' . $id_lang_default)) {
                        $values_title_thank[$lang['id_lang']] = trim(Tools::getValue('thank_you_page_title_' . $lang['id_lang'])) ? Tools::link_rewrite(trim(Tools::getValue('thank_you_page_title_' . $lang['id_lang']))) : Tools::link_rewrite(trim(Tools::getValue('thank_you_page_title_' . $id_lang_default)), true);
                        $checkAliasExit = ETS_CFU_Contact::checkAliasExit($values_title_thank[$lang['id_lang']], (int)$lang['id_lang'], $contact->id ? $contact->id : false, $this->context);
                        if ($checkAliasExit) {
                            $values_title_thank[$lang['id_lang']] = $values_title_thank[$lang['id_lang']] . '-' . ($contact->id ? $contact->id : (int)ETS_CFU_Contact::getMaxId() + 1);
                        }
                    } else {

                        $values_title_thank[$lang['id_lang']] = trim(Tools::getValue('thank_you_alias_' . $lang['id_lang'])) ? trim(Tools::getValue('thank_you_alias_' . $lang['id_lang'])) : trim(Tools::getValue('thank_you_alias_' . $id_lang_default));
                        $checkAliasExit = ETS_CFU_Contact::checkAliasExit($values_title_thank[$lang['id_lang']], (int)$lang['id_lang'], $contact->id ? $contact->id : false, $this->context);
                        if ($checkAliasExit) {
                            $values_title_thank[$lang['id_lang']] = $values_title_thank[$lang['id_lang']] . '-' . ($contact->id ? $contact->id : (int)ETS_CFU_Contact::getMaxId() + 1);
                        }
                    }
                }
                $contact->title_alias = $values_title_alias;
                $contact->thank_you_alias = $values_title_thank;
            }
        }

        if (!count($errors)) {
            if ($contact->id && $contact->update()) {
                ETS_CFU_SmartyCache::clearCacheAllSmarty('*', 'contact|' . $contact->id);
                Tools::redirectAdmin($this->getRedirect(array('id_contact' => $contact->id, 'conf' => 4)));
            } elseif (!$contact->id && $contact->add()) {
                Tools::redirectAdmin($this->getRedirect(array('id_contact' => $contact->id, 'conf' => 3)));
            } else
                $errors[] = $this->l('Unknown error happens');
        }
        if ($errors)
            return $this->displayError($errors);
    }

    public function validateUpload($key, $config, $file_dest = null, &$errors = [], $allow_files = ['jpg', 'jpeg', 'png', 'gif', 'webp'])
    {
        if ($file_dest == null)
            $file_dest = _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR;
        if (!@is_dir($file_dest))
            @mkdir($file_dest, 0755, true);
        $post_max_size = ETS_CFU_Tools::getPostMaxSizeBytes();
        if (
            !@is_writable($file_dest)
            && !empty($_FILES[$key]['name'])) {
            $errors[] = $config['label'] . '. ' . sprintf($this->l('The directory "%s" is not able to write.'), $file_dest);
        } elseif (isset($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
            if ($uploadError = ETS_CFU_Data_Provider::getInstance()->checkUploadError($_FILES[$key]['error'], $_FILES[$key]['name'])) {
                $errors[] = $uploadError;
            } elseif ($_FILES[$key]['size'] > $post_max_size) {
                $errors[] = $config['label'] . '. ' . sprintf($this->l('File is too large. Maximum size allowed: %sMb'), ETS_CFU_Tools::formatBytes($post_max_size));
            } elseif ($_FILES[$key]['size'] > Ets_cfultimate::DEFAULT_MAX_SIZE) {
                $errors[] = $config['label'] . '. ' . sprintf($this->l('File is too large. Current size is %1s, maximum size is %2s.'), $_FILES[$key]['size'], Ets_cfultimate::DEFAULT_MAX_SIZE);
            } elseif (isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                if (!Validate::isFileName(ETS_CFU_Tools::formatFileName($_FILES[$key]['name']))) {
                    $errors[] = $config['label'] . '. ' . sprintf($this->l('File name "%s" is invalid'), $_FILES[$key]['name']);
                } else {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                    if (!in_array($type, $allow_files)) {
                        $errors[] = $config['label'] . '. ' . sprintf($this->l('File "%s" type is not allowed'), $_FILES[$key]['name']);
                    }
                }
            }
        }

        return !(count($errors) > 0);
    }

    public function getRedirect($args = array())
    {
        return $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . (isset($args['conf']) ? '&conf=' . $args['conf'] : '') . '&etsCfuEditContact=1' . (!empty($args['id_contact']) ? '&id_contact=' . (int)$args['id_contact'] : '') . '&current_tab=' . (!empty($args['current_tab']) ? $args['current_tab'] : Tools::getValue('current_tab')) . '&current_tab_email=' . Tools::getValue('current_tab_email');
    }

    public function getAdminLink($token = true)
    {
        return $this->context->link->getAdminLink('AdminModules', $token) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
    }

    public function renderAddContactForm()
    {
        Media::addJsDef([
            'ETS_CFU_DO_NOT_IMPORT_LABEL' => $this->l('-- Do not import --'),
            'ETS_CFU_SELECT_AN_ITEM_LABEL' => $this->l('-- Select an item --'),
            'ETS_CFU_ADMIN_CONTACT_FORM_LINK' => $this->context->link->getAdminLink('AdminContactFormUltimateContactForm')
        ]);
        $id_contact = (int)Tools::getValue('id_contact') ?: null;
        $contact = new ETS_CFU_Contact($id_contact);
        $res = $contact->mailchimp_api_key ? EtsCfuMailChimp::getInstance()->getAudiences($contact->mailchimp_api_key) : [];
        $list_mailchimp = [];
        if ($res && isset($res['connect_status']) && $res['connect_status'] === true && !empty($res['data_list']) && is_array($res['data_list'])) {
            foreach ($res['data_list'] as $key => $label) {
                $list_mailchimp[] = [
                    'id' => $key,
                    'label' => $label,
                ];
            }
        }
        $contact_fields = Ets_cfudefines::getInstance($this)->getContactFields($list_mailchimp);
        if (Tools::isSubmit('id_contact')) {
            $contact_fields['form']['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_contact'
            );
            $contact_fields['form']['legend']['new'] = $this->getAdminLink() . '&etsCfuAddContact=1';
        }
        if ($this->rmHookShortcode()) {
            foreach ($contact_fields['form']['input'] as $key => $value) {
                if ($value['name'] == 'hook') {
                    unset($contact_fields['form']['input'][$key]);
                }
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'etsCfuSubmitSaveContact';
        $helper->currentIndex = $this->getAdminLink(false) . (Tools::isSubmit('etsCfuEditContact') && Tools::getValue('id_contact') ? '&etsCfuEditContact=1&id_contact=' . (int)Tools::getValue('id_contact') : '&etsCfuAddContact=1');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $assign = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $this->getAddContactFieldsValues($contact, $contact_fields),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => '../modules/' . $this->name . '/views/img/',
            'page' => 'contact',
            'link_basic' => $this->getBaseLink(),
            'name_controller' => 'edit_contact_form',
            'ps15' => (bool)version_compare(_PS_VERSION_, '1.6', '<'),
            'google_captcha' => $this->context->link->getAdminLink('AdminContactFormUltimateIntegration') . '&current_tab=google',
            'inputs' => Ets_cfudefines::getInstance($this)->getFields('inputs'),
            'admin_integration_link' => $this->context->link->getAdminLink('AdminContactFormUltimateIntegration'),
            'enable_hook_shortcode' => (int)Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE'),
            'enable_hook_shortcode_link' => $this->context->link->getAdminLink('AdminContactFormUltimateIntegration'),
            'condition_operator' => [
                '1' => $this->l('Contains'),
                '2' => $this->l('Do not contain'),
                '3' => $this->l('Empty'),
                '4' => $this->l('Filled'),
                '5' => $this->l('Equal to'),
                '6' => $this->l('Not equal to'),
                '7' => $this->l('Less than'),
                '8' => $this->l('Greater than'),
                '9' => $this->l('Before'),
                '10' => $this->l('After'),
            ],
            'condition_do' => [
                '1' => $this->l('Show'),
                '2' => $this->l('Hide'),
                '3' => $this->l('Show multiple'),
                '4' => $this->l('Hide multiple'),
            ],
            'check_apikey_link' => $this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true, [], ['check_apikey' => 1]),
            'mailchimp_doc_link' => $this->getPathUri() . 'Get_Mailchimp_API_key.pdf',
            'setup_mailchimp_link' => $this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true, [], ['setup_mailchimp' => 1]),
        );
        if ($contact->id > 0 && $contact->mailchimp_api_key !== '' && $contact->mailchimp_audience != '') {
            $assign['mailchimp_merge_fields'] = EtsCfuMailChimp::getInstance()->getMergeFields($contact->mailchimp_api_key, $contact->mailchimp_audience);
        }
        $helper->tpl_vars = $assign;
        $helper->override_folder = '/';

        return $helper->generateForm(array($contact_fields));
    }

    public function getAddContactFieldsValues(ETS_CFU_Contact $contact, $contact_fields)
    {
        $fields = array();
        $languages = $this->context->controller->getLanguages();
        $inputs = $contact_fields['form']['input'];
        if (!$inputs)
            return $fields;
        if ($contact->id > 0) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                if (isset($input['lang']) && $input['lang']) {
                    foreach ($languages as $l) {
                        $lang_values = $contact->{$key};
                        $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($lang_values[$l['id_lang']]) ? $lang_values[$l['id_lang']] : '');
                    }
                } elseif (trim($key) == 'condition') {
                    $condition = json_decode($contact->{$key}, true);
                    if (isset($condition['fields_form']))
                        $condition['fields_form'] = json_decode($condition['fields_form'], true);
                    $fields[$key] = Tools::getValue($key, $condition);
                } elseif ($input['name'] == 'id_contact') {
                    $fields['id_contact'] = Tools::getValue('id_contact');
                    $fields['link_contact'] = $contact->enable_form_page ? Ets_CfUltimate::getLinkContactForm(Tools::getValue('id_contact')) : '';
                } elseif ($input['type'] == 'checkbox') {
                    if (($values = Tools::getValue($key, explode(',', $contact->{$key})))) {
                        foreach ($values as $value) {
                            $fields[$key . '_' . $value] = 1;
                        }
                    }
                } elseif ($input['type'] == 'select' && isset($input['multiple']) && $input['multiple']) {
                    $fields[$key . '[]'] = Tools::getValue($key, explode(',', $contact->{$key}));
                } elseif (!isset($input['tree']) && $input['type'] != 'checkbox')
                    $fields[$key] = Tools::getValue($key, $contact->{$key});
                else
                    $fields[$key] = Tools::getValue($key, $contact->{$key});
                $this->multiValue($fields, $key, $input);
            }
        } else {
            foreach ($inputs as $input) {
                $key = $input['name'];
                if (trim($key) == 'condition') {
                    $_operator = Tools::getValue('operator', []);
                    $_if = Tools::getValue('if', []);
                    $_value = Tools::getValue('value', []);
                    $_do = Tools::getValue('do', []);
                    $_fields = Tools::getValue('fields', []);
                    $_fields_form = Tools::getValue('condition_fields_form', []);
                    $_condition = [
                        'fields_form' => $_fields_form,
                        'if' => $_if,
                        'operator' => $_operator,
                        'value' => $_value,
                        'do' => $_do,
                        'fields' => $_fields,
                    ];
                    $fields[$key] = $_condition;
                } else {
                    if (isset($input['lang']) && $input['lang']) {
                        foreach ($languages as $l) {
                            $default_lang = null;
                            if (isset($input['default_origin']) && $input['default_origin'])
                                $default_lang = ETS_CFU_Translate::trans($input['default_origin'], $l['id_lang'], 'defines');
                            elseif (isset($input['default']) && $input['default'])
                                $default_lang = $input['default'];
                            $fields[$key][$l['id_lang']] = $default_lang !== null ? Tools::getValue($key . '_' . $l['id_lang'], $default_lang) : Tools::getValue($key . '_' . $l['id_lang']);
                        }
                    } elseif ($input['name'] == 'id_contact') {
                        $fields['id_contact'] = Tools::getValue('id_contact');
                    } elseif ($input['type'] == 'checkbox') {
                        if (($values = isset($input['default']) && $input['default'] ? Tools::getValue($key, explode(',', $input['default'])) : Tools::getValue($key))) {
                            foreach ($values as $value) {
                                $fields[$key . '_' . $value] = 1;
                            }
                        }
                    } elseif ($input['type'] == 'select' && isset($input['multiple']) && $input['multiple']) {
                        $fields[$key . '[]'] = Tools::getValue($key);
                    } elseif (!isset($input['tree']) && $input['type'] != 'checkbox')
                        $fields[$key] = isset($input['default']) && $input['default'] ? Tools::getValue($key, $input['default']) : Tools::getValue($key);
                    else
                        $fields[$key] = Tools::getValue($key, (isset($input['default']) && $input['default'] ? $input['default'] : false));
                    $this->multiValue($fields, $key, $input);
                }
            }
        }
        return $fields;
    }

    public function multiValue(&$fields, $key, $input)
    {
        if (!(isset($fields[$key])))
            return false;
        if (isset($input['multi']) && $input['multi']) {
            $fields['multi_' . $key] = $fields[$key] ? ets_cfu_mailbox_list($fields[$key]) : array();
        }
    }

    public static function getLinkContactForm($id_contact_form, $id_lang = 0, $controller = 'contact')
    {
        $context = Context::getContext();
        $id_lang = $id_lang ?: $context->language->id;
        $contact_form = new ETS_CFU_Contact($id_contact_form, $id_lang);
        $blogLink = new ETS_CFU_Link();
        $params = array();
        $params['id_contact'] = $id_contact_form;
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $url = $blogLink->getBaseLinkFriendly(null, null) . $blogLink->getLangLinkFriendly($id_lang, null, null);
            if ($controller == 'contact' && $contact_form->id && $contact_form->title_alias) {
                $url .= (($subAlias = Configuration::get('ETS_CFU_CONTACT_ALIAS', $id_lang)) ? $subAlias : 'contact-form') . '/' . (Configuration::get('ETS_CFU_REMOVE_ID') ? '' : (int)$contact_form->id . '-') . $contact_form->title_alias . (Configuration::get('ETS_CFU_URL_SUFFIX') ? '.html' : '');
            } elseif ($controller == 'thank') {
                $url .= (($subAlias = Configuration::get('ETS_CFU_CONTACT_ALIAS', $id_lang)) ? $subAlias : 'contact-form') . '/thank/' . (Configuration::get('ETS_CFU_REMOVE_ID') ? '' : (int)$contact_form->id . '-') . $contact_form->thank_you_alias . (Configuration::get('ETS_CFU_URL_SUFFIX') ? '.html' : '');
            }
            return $url;
        }
        return $context->link->getModuleLink('ets_cfultimate', $controller, $params);
    }

    public function initContact()
    {
        if (class_exists('ETS_CFU_Contact')) {
            $group = Group::getGroups($this->context->language->id, true);
            $total_group = count($group);
            $group_temp = array();
            for ($i = 0; $i < $total_group; $i++) {
                $group_temp[] = $group[$i]['id_group'];
            }
            $str_update = implode(',', $group_temp);

            $contact = new ETS_CFU_Contact();
            $languages = Language::getLanguages(false);
            $contact_fields = Ets_cfudefines::getInstance($this)->getFields('contact');
            if (($inputs = $contact_fields['form']['input'])) {
                foreach ($inputs as $input) {
                    if (isset($input['lang']) && $input['lang']) {
                        $values = array();
                        foreach ($languages as $l) {
                            $default_lang = '';
                            if (isset($input['default_origin']) && $input['default_origin'])
                                $default_lang = ETS_CFU_Translate::trans($input['default_origin'], $l['id_lang'], 'defines');
                            elseif (isset($input['default']) && $input['default'])
                                $default_lang = $input['default'];
                            $values[$l['id_lang']] = $default_lang;
                        }
                        $contact->{$input['name']} = $values;
                    } else {
                        $contact->{$input['name']} = isset($input['default']) ? $input['default'] : '';
                    }
                }
            }
            $html_sc = $this->getHTML(array('type' => 'sc'));
            $html_msg = $this->getHTML(array('type' => 'msg'));
            foreach ($languages as $l) {
                $contact->short_code[$l['id_lang']] = $html_sc;
                $contact->message_body[$l['id_lang']] = $html_msg;
                $contact->message_body2[$l['id_lang']] = $html_msg;
                $contact->title[$l['id_lang']] = 'Sample Form';
            }
            $contact->render_form = $this->getHTML(array('type' => 'rf'));
            $contact->condition = @json_decode($contact->condition);
            $contact->email_to = Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>';
            $contact->email_to2 = '[text-652]<[email-668]>';
            $contact->email_from = '[text-652]<[email-668]>';
            $contact->email_from2 = Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>';
            $contact->additional_headers = '[text-652]<[email-668]>';
            $contact->additional_headers2 = Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>';
            $contact->id_employee = Context::getContext()->employee->id;
            $contact->group_access = $str_update;
            return $contact->add();
        }
        return true;
    }

    public function getHTML($args = array())
    {
        $args['languages'] = Language::getLanguages(false);
        $args['id_lang_default'] = $this->context->language->id;
        $args['icon_link'] = '../modules/ets_cfultimate/views/img/';
        $this->smarty->assign($args);
        return $this->display(__FILE__, 'init_contact.tpl');
    }

    public function _registerHook()
    {
        foreach (Ets_cfudefines::$_hooks as $hook) {
            $this->registerHook($hook);
        }
        return true;
    }

    public function _installDbConfig()
    {
        $fields_config = Ets_cfudefines::getInstance($this)->getFields('config');
        $inputs = $fields_config['form']['input'];
        $languages = Language::getLanguages(false);
        if ($inputs) {
            foreach ($inputs as $input) {
                if (isset($input['default'])) {
                    $key = $input['name'];
                    if (isset($input['lang']) && $input['lang']) {
                        $vals = array();
                        foreach ($languages as $language) {
                            $vals[$language['id_lang']] = $input['default'];
                        }
                        Configuration::updateValue($key, $vals, true);
                    } else {
                        Configuration::updateValue($key, $input['default']);
                    }
                }
            }
        }
        $fields_config = Ets_cfudefines::getInstance($this)->getFields('email');
        $inputs = $fields_config['form']['input'];
        $languages = Language::getLanguages(false);
        if ($inputs) {
            foreach ($inputs as $input) {
                if (isset($input['default'])) {
                    $key = $input['name'];
                    if (isset($input['lang']) && $input['lang']) {
                        $vals = array();
                        foreach ($languages as $language) {
                            $vals[$language['id_lang']] = $input['default'];
                        }
                        Configuration::updateValue($key, $vals, true);
                    } else {
                        Configuration::updateValue($key, $input['default']);
                    }
                }
            }
        }
        return true;
    }

    public function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminContactFormUltimate';
        $tab->module = $this->name;
        $tab->id_parent = 0;
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Contact');
        }
        $tab->save();
        if ($tab->id) {
            $this->addTabs(Ets_cfudefines::getInstance($this)->getFields('tabs'), $languages, $tab->id);
        }
        return true;
    }

    public function addTabs($tabs, $languages, $parent_id)
    {
        if (!is_array($tabs) || !count($tabs)) {
            return;
        }
        foreach ($tabs as $t) {
            if (!isset($t['class_name']) || !$t['class_name'])
                continue;
            $tab = new Tab();
            $tab->class_name = $t['class_name'];
            $tab->module = $this->name;
            $tab->id_parent = $parent_id;
            $tab->icon = $t['icon'];
            if ($languages) {
                foreach ($languages as $l) {
                    $tab->name[$l['id_lang']] = $t['tab_name'];
                }
            }
            $tab->active = isset($t['active']) ? (int)$t['active'] : 1;
            $tab->save();
            if ($tab->id && isset($t['children']) && $t['children']) {
                $this->addTabs($t['children'], $languages, $tab->id);
            }
        }
    }

    public function _uninstallTabs()
    {
        if (($tabId = (int)Tab::getIdFromClassName('AdminContactFormUltimate'))) {
            $tab = new Tab($tabId);
            if ($tab) {
                $tab->delete();
            }
            $this->deleteTabs(Ets_cfudefines::getInstance($this)->getFields('tabs'));
        }
        return true;
    }

    public function deleteTabs($tabs)
    {
        if (!$tabs) {
            return;
        }
        foreach ($tabs as $t) {
            if (($tabId = (int)Tab::getIdFromClassName($t['class_name']))) {
                $tab = new Tab($tabId);
                if ($tab) {
                    $tab->delete();
                }
                if (isset($t['children']) && $t['children']) {
                    $this->deleteTabs($t['children']);
                }
            }
        }
    }

    public function createTemplateMail()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            if (!file_exists(dirname(__FILE__) . '/mails/' . $language['iso_code'])) {
                mkdir(dirname(__FILE__) . '/mails/' . $language['iso_code'], 0755, true);
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_form_ultimate.html', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_form_ultimate.html');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_form_ultimate.txt', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_form_ultimate.txt');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_reply_form_ultimate.html', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_reply_form_ultimate.html');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_reply_form_ultimate.txt', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_reply_form_ultimate.txt');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_form_ultimate_plain.txt', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_form_ultimate_plain.txt');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_form_ultimate_plain.html', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_form_ultimate_plain.html');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_reply_form_ultimate_plain.txt', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_reply_form_ultimate_plain.txt');
                Tools::copy(dirname(__FILE__) . '/mails/en/contact_reply_form_ultimate_plain.html', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/contact_reply_form_ultimate_plain.html');
                Tools::copy(dirname(__FILE__) . '/mails/en/index.php', dirname(__FILE__) . '/mails/' . $language['iso_code'] . '/index.php');
            }
        }
        return true;
    }

    public function uninstall()
    {
        $dirs = array(
            _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR,
        );
        foreach ($dirs as $dir) {
            ETS_CFU_Tools::recursiveUnlink($dir);
        }

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall() && $this->_unInstallDbConfig() && $this->_uninstallTabs();
    }

    public function _unInstallDbConfig()
    {
        if (($fields_config = Ets_cfudefines::getInstance($this)->getFields('config')) && ($inputs = $fields_config['form']['input'])) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                Configuration::deleteByName($key);
            }
        }
        if (($fields_config = Ets_cfudefines::getInstance($this)->getFields('email')) && ($inputs = $fields_config['form']['input'])) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                Configuration::deleteByName($key);
            }
        }
        if (($fields_config = Ets_cfudefines::getInstance($this)->getFields('ip_black_list')) && ($inputs = $fields_config['form']['input'])) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                Configuration::deleteByName($key);
            }
        }
        foreach (glob(_PS_DOWNLOAD_DIR_ . $this->name . DIRECTORY_SEPARATOR . '*.*') as $filename) {
            if ($filename != _PS_DOWNLOAD_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'index.php' && file_exists($filename))
                @unlink($filename);
        }
        return true;
    }

    public function rmHookShortcode()
    {
        return !(int)Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE') && ($controller = Dispatcher::getInstance()->getController($this->context->shop->id)) && $controller != 'contact';
    }

    public function hookDisplayHeader()
    {
        if (!ETS_CFU_Contact::getContactFloating() && $this->rmHookShortcode()) {
            return '';
        }
        $this->context->controller->addJS($this->_path . 'views/js/date.js');
        $this->context->controller->addCSS($this->_path . 'views/css/date.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/scripts.js');
        $this->context->controller->addCSS($this->_path . 'views/css/style.css', 'all');
        if (version_compare(_PS_VERSION_, '1.6', '<'))
            $this->context->controller->addCSS($this->_path . 'views/css/style15.css', 'all');
        if (version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.5', '>'))
            $this->context->controller->addCSS($this->_path . 'views/css/style16.css', 'all');
        if (Configuration::get('ETS_CFU_ENABLE_TMCE')) {
            $this->context->controller->addCSS($this->_path . 'views/css/skin.min.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/tinymce/tinymce.min.js');
        }
        $assign = array(
            'url_basic' => $this->getBaseLink(),
            'link_contact_ets' => $this->context->link->getModuleLink('ets_cfultimate', 'contact'),
        );
        if (Configuration::get('ETS_CFU_ENABLE_RECAPTCHA')) {
            $assign['rc_enabled'] = true;
            $assign['rc_v3'] = Configuration::get('ETS_CFU_RECAPTCHA_TYPE') != 'v2';
            $assign['rc_key'] = Configuration::get('ETS_CFU_RECAPTCHA_TYPE') == 'v2' ? Configuration::get('ETS_CFU_SITE_KEY') : Configuration::get('ETS_CFU_SITE_KEY_V3');
            $this->context->controller->addJS($this->_path . 'views/js/recaptcha.js');
        }
        $assign['iso_code'] = $this->context->language->iso_code;
        $locale = explode('-', $this->context->language->language_code);
        $locale = !empty($locale[0]) ? $locale[0] : $locale;
        $assign['locale'] = $locale;
        if (!$this->context->customer->logged || !getOrderReferrence()) {
            $assign['hidden_reference'] = true;
        }
        $this->smarty->assign($assign);
        return $this->display(__FILE__, 'header.tpl') . $this->getContactFormByHook('header');
    }

    public function getContactFormByHook($hook)
    {
        if (trim($hook) !== '' && (int)Configuration::get('ETS_CFU_ENABLE_HOOK_SHORTCODE')) {
            $contacts = ETS_CFU_Contact::getContactsByHook($hook, $this->context);
            if ($contacts) {
                $form_html = '';
                foreach ($contacts as $contact) {
                    $form_html .= $this->hookDisplayContactFormUltimate($contact, $hook);
                }
                return $form_html;
            }
        }
        return '';
    }

    public function getContactFormInPage()
    {
        $id_contact = 0;
        $alias = Tools::getValue('url_alias');
        if ($this->context->controller instanceof Ets_CfUltimateContactModuleFrontController && trim($alias) !== '' && Validate::isCleanHtml(trim($alias))) {
            $id_contact = ETS_CFU_Contact::getIdContactByAlias(trim($alias));
        }
        $contacts = ETS_CFU_Contact::getContactsInPage($id_contact, $this->context);

        if ($contacts) {
            $form_html = '';
            foreach ($contacts as $contact) {
                $form_html .= $this->hookDisplayContactFormUltimate($contact, null, true);
            }
            return $form_html;
        }
        return '';
    }

    public function hookDisplayContactFormUltimate($params, $hook = null, $floating = false)
    {
        if (!$floating && $this->rmHookShortcode()) {
            return '';
        }
        $id = isset($params['id']) ? $params['id'] : $params['id_contact'];
        $contact = new ETS_CFU_Contact($id);

        $idCustomer = isset($this->context->customer) ? $this->context->customer->id : 0;
        $customer_group = Customer::getGroupsStatic($idCustomer);
        $group_access = $contact->id ? explode(',', $contact->group_access) : array();

        if ($contact->id && $contact->active && array_intersect($customer_group, $group_access)) {
            $contact_form = $this->ets_cfu_contact_form($id);
            return $this->form_html($contact_form, $hook, $floating ? 'floating' : true);
        }

        return '';
    }

    public function ets_cfu_contact_form($id)
    {
        return ETS_CFU_Contact_Form::get_instance($id);
    }

    /* @var $contact_form ETS_CFU_Contact_Form */
    public function form_html($contact_form, $hook = null, $displayHook = false)
    {
        $id_product = (int)Tools::getValue('
        
        ');
        $before = [$contact_form->id, (int)$displayHook];
        if ($hook !== null) {
            $before[] = $hook;
        }
        $cache_id = $id_product ? null : $this->getCacheId('contact', $before);
        if ($cache_id == null || !$this->isCached('contact-form.tpl', $cache_id)) {
            $contact_form->unit_tag = ETS_CFU_Contact_Form::get_unit_tag($contact_form->id);
            $contact_form->form_unit_tag = str_replace('wpcfu-f' . (int)$contact_form->id . '-o', '', $contact_form->unit_tag);
            if ($id_product > 0) {
                $product = new Product($id_product, false, $this->context->language->id);
                $cover = Product::getCover($product->id, $this->context);
                $this->smarty->assign(array(
                    'ets_cfu_product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'link' => $this->context->link->getProductLink($product),
                        'cover' => isset($cover['id_image']) && (int)$cover['id_image'] > 0 ? $this->context->link->getImageLink($product->link_rewrite, (int)$cover['id_image'], ETS_CFU_Tools::getFormattedName('home')) : '',
                    ]
                ));
            }
            $this->smarty->assign(array(
                'contact_form' => $contact_form,
                'link' => $this->context->link,
                'open_form_by_button' => $contact_form->open_form_by_button && $displayHook,
                'button_popup_enabled' => $contact_form->button_popup_enabled && (!$this->context->controller instanceof Ets_CfUltimateContactModuleFrontController || !($id_contact = ETS_CFU_Contact::getIdContactByAlias(trim(Tools::getValue('url_alias')))) || $id_contact != $contact_form->id),
                'form_elements' => $contact_form->form_elements(),
                'displayHook' => $displayHook,
            ));
        }

        return $this->display(__FILE__, 'contact-form.tpl', $cache_id);
    }

    public function renderFormConfig()
    {
        $config_fields = Ets_cfudefines::getInstance($this)->getFields('config');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'etsCfuBtnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminContactFormUltimateIntegration', false);
        $helper->token = Tools::getAdminTokenLite('AdminContactFormUltimateIntegration');
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'fields_value' => $this->getConfigFieldsValues($config_fields),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path . 'views/img/',
            'page' => 'integration',
            'name_controller' => 'integration',
            'link_basic' => $this->getBaseLink(),
            'ps15' => (bool)version_compare(_PS_VERSION_, '1.6', '<'),
        );
        $helper->module = $this;
        return $helper->generateForm(array($config_fields));
    }

    public function getConfigFieldsValues($config_fields)
    {
        if (!$config_fields) {
            $config_fields = Ets_cfudefines::getInstance($this)->getFields('config');
        }
        $inputs = $config_fields['form']['input'];
        $languages = Language::getLanguages(false);
        $fields = array();
        if ($inputs) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                if (isset($input['lang']) && $input['lang']) {
                    foreach ($languages as $language) {
                        $fields[$key][$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang'], Configuration::get($key, $language['id_lang']));
                    }
                } else
                    $fields[$key] = Tools::getValue($key, Configuration::get($key));
            }
        }
        return $fields;
    }

    public function renderFormEmail()
    {
        $email_fields = Ets_cfudefines::getInstance($this)->getFields('email');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'etsCfuBtnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminContactFormUltimateEmail', false);
        $helper->token = Tools::getAdminTokenLite('AdminContactFormUltimateEmail');
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'fields_value' => $this->getEmailFieldsValues($email_fields),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path . 'views/img/',
            'page' => 'email',
            'name_controller' => 'email',
            'link_basic' => $this->getBaseLink(),
            'ps15' => version_compare(_PS_VERSION_, '1.6', '<') ? true : false,
        );
        $helper->module = $this;
        return $helper->generateForm(array($email_fields));
    }

    public function getEmailFieldsValues($email_fields)
    {
        if (!$email_fields) {
            $email_fields = Ets_cfudefines::getInstance($this)->getFields('email');
        }
        $inputs = $email_fields['form']['input'];
        $languages = Language::getLanguages(false);
        $fields = array();
        if ($inputs) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                if (isset($input['lang']) && $input['lang']) {
                    foreach ($languages as $language) {
                        $fields[$key][$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang'], Configuration::get($key, $language['id_lang']));
                    }
                } else
                    $fields[$key] = Tools::getValue($key, Configuration::get($key));
            }
        }
        return $fields;
    }

    public function renderFormIpBlackList()
    {
        $ip_black_list = Ets_cfudefines::getInstance($this)->getFields('ip_black_list');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'etsCfuBtnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminContactFormUltimateIpBlacklist', false);
        $helper->token = Tools::getValue('token', Tools::getAdminTokenLite('AdminContactFormUltimateIpBlacklist'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'fields_value' => $this->getIpBlackListFieldsValues($ip_black_list),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path . 'views/img/',
            'page' => 'ip_black_list',
            'name_controller' => 'ip_black_list',
            'link_basic' => $this->getBaseLink(),
            'ps15' => version_compare(_PS_VERSION_, '1.6', '<') ? true : false,
        );
        $helper->module = $this;
        return $helper->generateForm(array($ip_black_list));
    }

    public function getIpBlackListFieldsValues($ip_black_list)
    {
        if (!$ip_black_list) {
            $ip_black_list = Ets_cfudefines::getInstance($this)->getFields('ip_black_list');
        }
        $inputs = $ip_black_list['form']['input'];
        $languages = Language::getLanguages(false);
        $fields = array();
        if ($inputs) {
            foreach ($inputs as $input) {
                $key = $input['name'];
                if (isset($input['lang']) && $input['lang']) {
                    foreach ($languages as $language) {
                        $fields[$key][$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang'], Configuration::get($key, $language['id_lang']));
                    }
                } else
                    $fields[$key] = Tools::getValue($key, Configuration::get($key));
            }
        }
        return $fields;
    }

    public function hookDisplayBackOfficeHeader()
    {
        Media::addJsDef([
            'ETS_CFU_LANGUAGES' => LanguageCore::getIDs(false),
        ]);
        $this->context->controller->addCSS(array(
            $this->_path . '/views/css/contact_form7_admin_all.css',
        ), 'all');
        $this->context->controller->addJS($this->_path . 'views/js/cfu_admin_all.js');
        $controller = Tools::getValue('controller');
        $is_loaded = (strpos($controller, 'AdminContactFormUltimate') !== FALSE);
        if ((Tools::getValue('configure') == $this->name && $controller == 'AdminModules') || $is_loaded) {
            Media::addJsDef([
                'ETS_CFU_FIELD_LABEL' => $this->l('field'),
                'ETS_CFU_FIELDS_VALID' => $this->l('Contact form needs to have at least 2 or more input fields'),
                'ETS_CFU_MAX_CONDITION_MSG' => $this->l('The maximum number of logical conditions that can be added has been reached'),
                'ETS_CFU_FIELDS_REQUIRED' => $this->l('Fields are required'),
                'ETS_CFU_VALUE_REQUIRED' => $this->l('Value is required'),
                'ETS_CFU_VALUE_INVALID' => $this->l('Value is invalid'),
                'ETS_CFU_IF_REQUIRED' => $this->l('"If" condition is required'),
                'ETS_CFU_IS_REQUIRED' => $this->l('is required'),
                'ETS_CFU_IS_INVALID' => $this->l('is invalid'),
            ]);
            $this->context->controller->addCSS($this->_path . '/views/css/contact_form7_admin.css', 'all');
            if (version_compare(_PS_VERSION_, '1.7', '<') && version_compare(_PS_VERSION_, '1.5', '>')) {
                $this->context->controller->addCSS($this->_path . '/views/css/contact_form7_admin16.css', 'all');
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path . '/views/css/contact_form7_admin15.css', 'all');
            }
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
                $this->context->controller->addJqueryUI('ui.sortable');
            }
        }
    }

    public function hookContactFormUltimateTopBlock()
    {
        $controller = Tools::getValue('controller');
        $cacheLifeTimeBefore = (int)Configuration::get('ETS_CFU_CACHE_BLOCK_TOP');
        $cacheLifeTime = (int)Configuration::get('ETS_CFU_CACHE_LIFETIME') * 3600;
        if ((time() - $cacheLifeTimeBefore) >= $cacheLifeTime) {
            $cacheLifeTimeBefore = time();
        }
        $cache_id = ETS_CFU_SmartyCache::getCachedId('block_top', $controller, $cacheLifeTimeBefore);
        if ($cache_id == null || !$this->isCached('block-top.tpl', $cache_id)) {
            if ($cache_id !== null) {
                Configuration::updateValue('ETS_CFU_CACHE_BLOCK_TOP', $cacheLifeTimeBefore);
                ETS_CFU_SmartyCache::clearCacheBoSmarty('block-top.tpl', 'block_top', $controller);
            }
            $this->smarty->assign(array(
                'controller' => $controller,
                'link' => $this->context->link,
                'ets_cfu_js_dir_path' => $this->_path . 'views/js/',
                'ets_cfu_default_lang' => Configuration::get('PS_LANG_DEFAULT'),
                'ets_cfu_is_updating' => Tools::getValue('id_contact') ? 1 : 0,
                'count_messages' => ETS_CFU_Contact_Message::getCountUnreadMessage(),
                'languages' => Language::getLanguages(false),
                'refsLink' => isset($this->refs) ? $this->refs . $this->context->language->iso_code : false,
            ));
        }
        return $this->display(__FILE__, 'block-top.tpl', $cache_id);
    }

    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $context->shop->domain . $context->shop->getBaseURI();
    }

    public function hookModuleRoutes($params)
    {
        $contactAlias = (Configuration::get('ETS_CFU_CONTACT_ALIAS', $this->context->language->id) ? Configuration::get('ETS_CFU_CONTACT_ALIAS', $this->context->language->id) : 'contact-form');
        if (!$contactAlias)
            return array();
        $removeId = Configuration::get('ETS_CFU_REMOVE_ID');
        $keywords = array(
            'url_alias' => array('regexp' => '[_a-zA-Z0-9-]+', 'param' => 'url_alias'),
        );

        if (!$removeId) {
            $keywords['id_contact'] = array('regexp' => '[0-9]+', 'param' => 'id_contact');
        }

        $routes = array(
            'ets_cfultimate_contact' => array(
                'controller' => 'contact',
                'rule' => $contactAlias,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_cfultimate',
                ),
            ),
            'ets_cfultimate_contact_single' => array(
                'controller' => 'contact',
                'rule' => $contactAlias . '/' . (!$removeId ? '{id_contact}-' : '') . '{url_alias}' . (Configuration::get('ETS_CFU_URL_SUFFIX') ? '.html' : ''),
                'keywords' => $keywords,
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_cfultimate',
                ),
            ),
            'ets_cfultimate_contact_thank' => array(
                'controller' => 'thank',
                'rule' => $contactAlias . '/thank/' . (!$removeId ? '{id_contact}-' : '') . '{url_alias}' . (Configuration::get('ETS_CFU_URL_SUFFIX') ? '.html' : ''),
                'keywords' => $keywords,
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ets_cfultimate',
                ),
            ),
        );

        return $routes;
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl" => array(
                    "allow_self_signed" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }

    public function hookActionOutputHTMLBefore($params)
    {
        if (isset($params['html']) && $params['html']) {
            $params['html'] = $this->doShortCode($params['html']);
        }
    }

    public function doShortCode($str)
    {
        return preg_replace_callback('~\[contact\-form\-7 id="(\d+)"\]~', array($this, 'replace'), $str);
    }

    public function hookDisplayHome()
    {
        return $this->getContactFormByHook('home');
    }

    public function hookDisplayNav2()
    {
        return $this->getContactFormByHook('nav_top');
    }

    public function hookDisplayProductAdditionalInfo()
    {
        return $this->getContactFormByHook('product_info');
    }

    public function hookDisplayFooterProduct()
    {
        return $this->getContactFormByHook('product_footer');
    }

    public function hookDisplayNav()
    {
        return $this->getContactFormByHook('nav_top');
    }

    public function hookDisplayTop()
    {
        return $this->getContactFormByHook('displayTop');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->getContactFormByHook('left_column');
    }

    public function hookDisplayFooter()
    {
        return $this->getContactFormByHook('footer_page') . $this->getContactFormInPage();
    }

    public function hookDisplayRightColumn()
    {
        return $this->getContactFormByHook('right_column');
    }

    public function hookDisplayAfterProductThumbs()
    {
        return $this->getContactFormByHook('product_thumbs');
    }

    public function hookDisplayRightColumnProduct()
    {
        return $this->getContactFormByHook('product_right');
    }

    public function hookDisplayLeftColumnProduct()
    {
        return $this->getContactFormByHook('product_left');
    }

    public function hookDisplayShoppingCartFooter()
    {
        return $this->getContactFormByHook('checkout_page');
    }

    public function hookDisplayCustomerAccountForm()
    {
        return $this->getContactFormByHook('register_page');
    }

    public function hookDisplayCustomerLoginFormAfter()
    {
        return $this->getContactFormByHook('login_page');
    }

    public function replaceDefaultContactForm($str)
    {
        return preg_replace('~' . ETS_CFU_Tools::displayText('.*', 'section', ['class' => 'contact-form']) . '~', 'abc', $str);
    }

    public function replace($matches)
    {
        if (is_array($matches) && count($matches) == 2) {
            if ($this->rmHookShortcode()) {
                return $matches[0];
            }
            $form = $this->hookDisplayContactFormUltimate(array(
                'id' => (int)$matches[1]
            ));
            if ($form)
                return $form;
            else
                return $this->display(__FILE__, 'no-form-contact.tpl');
        }
    }

    public function setMetas($id_contact, $thank_page = false)
    {
        if (!$id_contact || trim(Tools::getValue('module')) != $this->name) {
            return;
        }
        $meta = array();
        $contact = new ETS_CFU_Contact($id_contact, $this->context->language->id);;
        $meta['meta_title'] = $contact->meta_title ? $contact->meta_title : ($thank_page ? $contact->thank_you_page_title : $contact->title);
        $meta['meta_description'] = $contact->meta_description;
        $meta['meta_keywords'] = $contact->meta_keyword;
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $body_classes = array(
                'lang-' . $this->context->language->iso_code => true,
                'lang-rtl' => (bool)$this->context->language->is_rtl,
                'country-' . $this->context->country->iso_code => true,
            );
            $page = array(
                'title' => $meta['meta_title'],
                'canonical' => '',
                'meta' => array(
                    'title' => $meta['meta_title'],
                    'description' => $meta['meta_description'],
                    'keywords' => $meta['meta_keywords'],
                    'robots' => 'index',
                ),
                'page_name' => 'ets_cft_page',
                'body_classes' => $body_classes,
                'admin_notifications' => array(),
            );
            $this->context->smarty->assign(array('page' => $page));
        } else {
            $this->context->smarty->assign($meta);
        }
    }

    public function ets_cfu_text_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type, 'wpcfu-text');
        if (in_array($tag->basetype, array('email', 'url', 'tel', 'password'))) {
            $class .= ' wpcfu-validates-as-' . $tag->basetype;
        }
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control';
        $attrs = array();

        $attrs['size'] = $tag->get_size_option('40');
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
        if ($attrs['maxlength'] && $attrs['minlength'] && $attrs['maxlength'] < $attrs['minlength']) {
            unset($attrs['maxlength'], $attrs['minlength']);
        }
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['autocomplete'] = $tag->get_option('autocomplete', '[-0-9a-zA-Z]+', true);
        if ($tag->has_option('readonly')) {
            $attrs['readonly'] = 'readonly';
        }
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $value = (string)reset($tag->values);
        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $attrs['placeholder'] = $value;
            $value = '';
        }

        if ($tag->basetype != 'password') {
            if ($tag->has_option('default')) {
                $option = $tag->get_first_match_option('/^default:user_(.+)+$/');
                if (is_array($option) && isset($option[0]))
                    $value = $option[0];
            }

            $value = $tag->get_default_option($value);
            $value = ets_cfu_get_hangover($tag->name, $value);
            if ($tag->has_option('use_current_url'))
                $value = $this->getFileCacheByUrl();

            if ($tag->has_option('read_only'))
                $attrs['readonly'] = 'true';
        }

        $attrs['value'] = $value;
        if (ets_cfu_support_html5()) {
            $attrs['type'] = $tag->basetype;
        } else {
            $attrs['type'] = 'text';
        }
        $attrs['name'] = $tag->name;

        $this->smarty->assign(array(
            'html_class' => ets_cfu_sanitize_html_class($tag->name),
            'atts' => $attrs,
            'validation_error' => $validation_error,
            'show_hide_password' => ($tag->basetype == 'password')
        ));
        return $this->display(__FILE__, 'form_text.tpl');
    }

    public function getFileCacheByUrl()
    {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $url = 'https://' . $url;
        } else
            $url = 'http://' . $url;
        if (strpos($url, '#') !== FALSE) {
            $url = Tools::substr($url, 0, strpos($url, '#'));
        }
        return $url;
    }

    public function ets_cfu_textarea_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control' . ($tag->has_option('rte') && Configuration::get('ETS_CFU_ENABLE_TMCE') ? ' autoload_rte_cfu' : '');
        $attrs = array();
        $attrs['cols'] = $tag->get_cols_option('40');
        $attrs['rows'] = $tag->get_rows_option('10');
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
        if ($attrs['maxlength'] && $attrs['minlength'] && $attrs['maxlength'] < $attrs['minlength'] || $tag->has_option('rte')) {
            unset($attrs['maxlength'], $attrs['minlength']);
        }
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['autocomplete'] = $tag->get_option('autocomplete', '[-0-9a-zA-Z]+', true);
        if ($tag->has_option('readonly')) {
            $attrs['readonly'] = 'readonly';
        }
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $value = empty($tag->content)
            ? (string)reset($tag->values)
            : $tag->content;
        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $attrs['placeholder'] = $value;
            $value = '';
        }
        $value = $tag->get_default_option($value);
        $value = ets_cfu_get_hangover($tag->name, $value);
        $attrs['name'] = $tag->name;
        $this->smarty->assign(array(
            'html_class' => ets_cfu_sanitize_html_class($tag->name),
            'atts' => $attrs,
            'value' => esc_cfu_textarea($value),
            'tiny_mce_enabled' => (int)Configuration::get('ETS_CFU_ENABLE_TMCE'),
            'validation_error' => $validation_error,
        ));
        return $this->display(__FILE__, 'form_textarea.tpl');
    }

    public function ets_cfu_captcha_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type, 'wpcfu-text');
        if (in_array($tag->basetype, array('email', 'url', 'tel'))) {
            $class .= ' wpcfu-validates-as-' . $tag->basetype;
        }
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control';
        $attrs = array();
        $attrs['size'] = $tag->get_size_option('40');
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
        if ($attrs['maxlength'] && $attrs['minlength'] && $attrs['maxlength'] < $attrs['minlength']) {
            unset($attrs['maxlength'], $attrs['minlength']);
        }
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['autocomplete'] = $tag->get_option('autocomplete', '[-0-9a-zA-Z]+', true);
        if ($tag->has_option('readonly')) {
            $attrs['readonly'] = 'readonly';
        }
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $attrs['type'] = 'captcha';
        $attrs['name'] = $tag->name;
        $rand = md5(rand());
        $theme = $tag->get_option('theme', '(basic|complex|colorful)', true);
        $this->smarty->assign(array(
            'link_captcha_image' => Context::getContext()->link->getModuleLink('ets_cfultimate', 'captcha', array('captcha_name' => $tag->name, 'rand' => $rand, 'theme' => $theme), true),
            'html_class' => ets_cfu_sanitize_html_class($tag->name),
            'atts' => $attrs,
            'url_base' => $this->getBaseLink(),
            'rand' => $rand,
            'validation_error' => $validation_error
        ));
        return $this->display(__FILE__, 'form_captcha.tpl');
    }

    public function ets_cfu_quiz_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control';
        $attrs = array();
        $attrs['size'] = $tag->get_size_option('40');
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
        if ($attrs['maxlength'] && $attrs['minlength'] && $attrs['maxlength'] < $attrs['minlength']) {
            unset($attrs['maxlength'], $attrs['minlength']);
        }
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['autocomplete'] = 'off';
        $attrs['aria-required'] = 'true';
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $pipes = $tag->pipes;
        if ($pipes instanceof ETS_CFU_Pipes && !$pipes->zero()) {
            $pipe = $pipes->random_pipe();
            $question = $pipe->before;
            $answer = $pipe->after;
        } else {
            $question = '1+1=?';
            $answer = '2';
        }
        $answer = ets_cfu_canonicalize($answer);
        $attrs['type'] = 'text';
        $attrs['name'] = $tag->name;
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'question' => $question,
                'atts' => $attrs,
                'tag_name' => $tag->name,
                'answer' => ets_cfu_hash($answer, 'ets_cfu_quiz'),
                'validation_error' => $validation_error,
            )
        );
        return $this->display(__FILE__, 'form_quiz.tpl');
    }

    public function ets_cfu_number_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        $class .= ' wpcfu-validates-as-number';
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control';
        $attrs = array();
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['min'] = $tag->get_option('min', 'signed_int', true);
        $attrs['max'] = $tag->get_option('max', 'signed_int', true);
        $attrs['step'] = $tag->get_option('step', 'int', true);
        $attrs['tagtype'] = $tag->get_option('tagtype');

        if ($tag->has_option('readonly')) {
            $attrs['readonly'] = 'readonly';
        }
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $value = (string)reset($tag->values);
        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $attrs['placeholder'] = $value;
            $value = '';
        }
        $value = $tag->get_default_option($value);
        $value = ets_cfu_get_hangover($tag->name, $value);
        $attrs['value'] = $value;

        if (ets_cfu_support_html5()) {
            $attrs['type'] = $tag->basetype;
        } else {
            $attrs['type'] = 'text';
        }
        $attrs['name'] = $tag->name;
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'validation_error' => $validation_error,
            )
        );
        return $this->display(__FILE__, 'form_number.tpl');
    }

    public function ets_cfu_hidden_form_tag_handler($tag)
    {
        $attrs = array();
        $class = ets_cfu_form_controls_class($tag->type);
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $value = (string)reset($tag->values);
        $value = $tag->get_default_option($value);
        $attrs['value'] = $value;
        $attrs['type'] = 'hidden';
        $attrs['name'] = $tag->name;
        $this->smarty->assign(
            array(
                'atts' => $attrs,
            )
        );
        return $this->display(__FILE__, 'form_hidden.tpl');
    }

    public function ets_cfu_file_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control filestyle';
        $attrs = array();
        $attrs['size'] = $tag->get_size_option('40');
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['accept'] = ets_cfu_acceptable_filetypes(
            $tag->get_option('filetypes'), 'attr');

        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $attrs['type'] = 'file';
        $attrs['name'] = $tag->name;
        $attrs['data-icon'] = 'false';
        $attrs['data-placeholder'] = $this->l('No file chosen');
        $attrs['data-buttontext'] = $this->l('Choose file');
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'validation_error' => $validation_error,
                'type_file' => $tag->get_option('filetypes') ? (implode(' | ', explode('|', implode(',', $tag->get_option('filetypes'))))) : implode(', .', ETS_CFU_Tools::getDefaultFileType()),
                'limit_zie' => $tag->get_option('limit') ? implode(',', $tag->get_option('limit')) : ETS_CFU_Tools::formatBytes(ETS_CFU_Tools::getPostMaxSizeBytes()),
            )
        );

        return $this->display(__FILE__, 'form_file.tpl');
    }

    public function ets_cfu_select_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' wpcfu-not-valid';
        }
        $class .= ' form-control';
        $attrs = array();
        $mod_reference = $tag->has_option('mod_reference');
        if ($mod_reference && (!$this->context->customer->logged || !getOrderReferrence())) {
            return '';
        }
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $multiple = $tag->has_option('multiple');
        $include_blank = $tag->has_option('include_blank');
        $first_as_label = $tag->has_option('first_as_label');
        if ($tag->has_option('size')) {
            $size = $tag->get_option('size', 'int', true);

            if ($size) {
                $attrs['size'] = $size;
            } elseif ($multiple) {
                $attrs['size'] = 4;
            } else {
                $attrs['size'] = 1;
            }
        }
        $values = $tag->values;
        $labels = $tag->labels;
        if ($data = (array)$tag->get_data_option()) {
            $values = array_merge($values, array_values($data));
            $labels = array_merge($labels, array_values($data));
        }
        $defaults = isset($tag->defaults) ? $tag->defaults : [];
        $default_choice = $tag->get_default_option(null, 'multiple=1');
        foreach ($default_choice as $value) {
            $key = array_search($value, $values, true);

            if (false !== $key) {
                $defaults[] = (int)$key + 1;
            }
        }
        if ($matches = $tag->get_first_match_option('/^default:([0-9_]+)$/')) {
            $defaults = array_merge($defaults, explode('_', $matches[1]));
        }
        $defaults = array_unique($defaults);
        $shifted = false;
        if (!$multiple) {
            if ($include_blank || empty($values)) {
                array_unshift($labels, $this->l('-- Select an item --'));
                array_unshift($values, '');
                $shifted = true;
            } elseif ($first_as_label) {
                $values[0] = '';
            }
        }
        $html = '';
        $choice_value = array_shift($defaults);
        if ($choice_value == null)
            $choice_value = $values[0];
        $hangover = ets_cfu_get_hangover($tag->name, $multiple ? $defaults : $choice_value);
        if ($mod_reference && $this->context->customer->logged && $orders = getOrderReferrence()) {
            $labels = array();
            $orders = array_merge(array('select_reference' => $this->l('Select reference')), $orders);
            $values = $orders;
        }
        foreach ($values as $key => $value) {
            $selected = false;
            if ($hangover !== null) {
                if ($multiple) {
                    $selected = in_array($value, (array)$hangover, true);
                } else {
                    $selected = (trim($hangover) === trim($value));
                }
            } else {
                if (!$shifted && in_array((int)$key + 1, (array)$defaults)) {
                    $selected = true;
                } elseif ($shifted && in_array((int)$key, (array)$defaults)) {
                    $selected = true;
                }
            }
            $item_attrs = array(
                'value' => ($mod_reference && trim($key) === 'select_reference') ? ' ' : $value,
            );
            if ($selected) {
                $item_attrs['selected'] = 'selected';
            }

            $label = isset($labels[$key]) && $labels[$key] ? $labels[$key] : $value;
            $this->smarty->assign(
                array(
                    'item_attrs' => $item_attrs,
                    'label' => $label,
                )
            );
            $html .= $this->display(__FILE__, 'option.tpl');
        }
        if ($multiple) {
            $attrs['multiple'] = 'multiple';
        }
        $attrs['name'] = $tag->name . ($multiple ? '[]' : '');
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'html' => $html,
                'validation_error' => $validation_error,
            )
        );
        return $this->display(__FILE__, 'form_select.tpl');
    }

    public function ets_cfu_submit_form_tag_handler($tag)
    {
        $class = ets_cfu_form_controls_class($tag->type);
        $attrs = array();
        $attrs['class'] = $tag->get_class_option($class) . ' btn btn-primary ';
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $value = isset($tag->values[0]) ? $tag->values[0] : '';
        if (empty($value)) {
            $value = 'Send';
        }
        $attrs['type'] = 'submit';
        $attrs['value'] = $value;
        $this->smarty->assign(
            array(
                'atts' => $attrs
            )
        );
        return $this->display(__FILE__, 'form_submit.tpl');
    }

    public function ets_cfu_html_form_tag_handler($tag)
    {
        $class = ets_cfu_form_controls_class($tag->type);
        $attrs = array();
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['type'] = 'html';
        $attrs['name'] = $tag->name;
        $content = empty($tag->content) ? (string)reset($tag->values) : $tag->content;
        $content = trim($content);
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'content' => $content,
            )
        );

        return $this->display(__FILE__, 'form_html.tpl');
    }

    public function ets_cfu_recaptcha_form_tag_handler($tag)
    {
        $attrs = array();
        $recaptcha = ETS_CFU_Recaptcha::get_instance();
        $attrs['data-sitekey'] = $recaptcha->get_sitekey();
        $attrs['data-type'] = $tag->get_option('type', '(audio|image)', true);
        $attrs['data-size'] = $tag->get_option(
            'size', '(compact|normal|invisible)', true);
        $attrs['data-theme'] = $tag->get_option('theme', '(dark|light)', true);
        $attrs['data-badge'] = $tag->get_option(
            'badge', '(bottomright|bottomleft|inline)', true);
        $attrs['data-tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['data-callback'] = $tag->get_option('callback', '', true);
        $attrs['data-expired-callback'] =
            $tag->get_option('expired_callback', '', true);
        $attrs['class'] = $tag->get_class_option(
            ets_cfu_form_controls_class($tag->type, 'g-recaptcha'));
        $attrs['id'] = $tag->get_id_option();
        $this->smarty->assign(
            array(
                'atts' => $attrs,
                'html' => ets_cfu_recaptcha_noscript(array('sitekey' => $attrs['data-sitekey'])),
                'v3' => Configuration::get('ETS_CFU_RECAPTCHA_TYPE') != 'v2',
            )
        );
        return $this->display(__FILE__, 'form_recaptcha.tpl');
    }

    public function ets_cfu_date_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        $class .= ' ets_cfu-validates-as-date';
        if ($validation_error) {
            $class .= ' ets_cfu-not-valid';
        }
        $class .= ' form-control';
        if ($tag->has_option('time')) {
            $class .= ' datetimepicker';
        } else {
            $class .= ' datepicker';
        }
        $attrs = array();
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $attrs['min'] = $tag->get_date_option('min');
        $attrs['max'] = $tag->get_date_option('max');
        $attrs['step'] = $tag->get_option('step', 'int', true);
        if ($tag->has_option('readonly')) {
            $attrs['readonly'] = 'readonly';
        }
        if ($tag->is_required()) {
            $attrs['aria-required'] = 'true';
        }
        $attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        $value = (string)reset($tag->values);
        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $attrs['placeholder'] = $value;
            $value = '';
        }
        $value = $tag->get_default_option($value);
        $value = ets_cfu_get_hangover($tag->name, $value);
        $attrs['value'] = $value;
        $attrs['type'] = 'text';
        $attrs['name'] = $tag->name;
        $attrs['autocomplete'] = 'off';
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'validation_error' => $validation_error,
            )
        );
        return $this->display(__FILE__, 'form_date.tpl');
    }

    public function ets_cfu_count_form_tag_handler($tag)
    {
        $targets = ets_cfu_scan_form_tags(array('name' => $tag->name));
        $maxlength = $minlength = null;
        while ($targets) {
            $target = array_shift($targets);

            if ('count' != $target->type) {
                $maxlength = $target->get_maxlength_option();
                $minlength = $target->get_minlength_option();
                break;
            }
        }
        if ($maxlength && $minlength && $maxlength < $minlength) {
            $maxlength = $minlength = null;
        }
        if ($tag->has_option('down')) {
            $value = (int)$maxlength;
            $class = 'ets_cfu-character-count down';
        } else {
            $value = '0';
            $class = 'ets_cfu-character-count up';
        }
        $attrs = array();
        $attrs['id'] = $tag->get_id_option();
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['data-target-name'] = $tag->name;
        $attrs['data-starting-value'] = $value;
        $attrs['data-current-value'] = $value;
        $attrs['data-maximum-value'] = $maxlength;
        $attrs['data-minimum-value'] = $minlength;
        $this->smarty->assign(
            array(
                'atts' => $attrs,
                'value' => $value,
            )
        );
        return $this->display(__FILE__, 'form_count.tpl');
    }

    public function ets_cfu_checkbox_form_tag_handler($tag)
    {
        $validation_error = false;
        $isRadio = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' ets_cfu-not-valid';
        }
        $label_first = $tag->has_option('label_first');
        $each_a_line = $tag->has_option('each_a_line');

        $use_label_element = $tag->has_option('use_label_element');
        $exclusive = $tag->has_option('exclusive');
        $multiple = false;
        if ('checkbox' == $tag->basetype) {
            $multiple = !$exclusive;
        } else {
            $isRadio = true;
            $exclusive = false;
        }
        if ($exclusive) {
            $class .= ' ets_cfu-exclusive-checkbox';
        }
        $attrs = array();
        $attrs['class'] = $tag->get_class_option($class);
        $attrs['id'] = $tag->get_id_option();
        $tabindex = $tag->get_option('tabindex', 'signed_int', true);
        if (false !== $tabindex) {
            $tabindex = (int)$tabindex;
        }
        $html = '';
        $count = 0;
        $values = (array)$tag->values;
        $labels = (array)$tag->labels;
        $defaults = isset($tag->defaults) ? $tag->defaults : [];
        $default_choice = $tag->get_default_option(null, 'multiple=' . (int)$multiple);
        if ($default_choice) {
            foreach ($default_choice as $value) {
                $key = array_search($value, $values, true);

                if (false !== $key) {
                    $defaults[] = (int)$key + 1;
                }
            }
        }
        if (!$isRadio && $matches = $tag->get_first_match_option('/^default:([0-9_]+)$/')) {
            $defaults = array_merge($defaults, explode('_', $matches[1]));
        }
        $defaults = array_unique($defaults);
        $hangover = ets_cfu_get_hangover($tag->name, $multiple ? $defaults : array_shift($defaults));
        foreach ($values as $key => $value) {
            $class = 'ets_cfu-list-item';
            $checked = false;
            if ($hangover) {
                if ($multiple) {
                    $checked = in_array($value, (array)$hangover, true);
                } else {
                    $checked = ($hangover === $value);
                }
            } else {
                $checked = in_array($key + 1, (array)$defaults);
            }

            if (isset($labels[$key])) {
                $label = $labels[$key];
            } else {
                $label = $value;
            }
            $item_attrs = array(
                'type' => $tag->basetype,
                'name' => $tag->name . ($multiple ? '[]' : ''),
                'value' => $value,
                'checked' => $checked ? 'checked' : '',
                'tabindex' => false !== $tabindex ? $tabindex : '',
                'id' => $tag->name . '_' . $value,
            );
            if (false !== $tabindex && 0 < $tabindex) {
                $tabindex += 1;
            }
            $count += 1;
            if (1 == $count) {
                $class .= ' first';
            }
            $this->smarty->assign(
                array(
                    'class' => $class,
                    'label' => $label,
                    'label_first' => $label_first,
                    'label_for' => $tag->name . '_' . $value,
                    'use_label_element' => $use_label_element,
                    'item_attrs' => $item_attrs,
                    'values' => $values,
                    'count' => $count,
                    'each_a_line' => $each_a_line
                )
            );
            $html .= $this->display(__FILE__, 'item_checkbox.tpl');
        }
        $this->smarty->assign(
            array(
                'html_class' => ets_cfu_sanitize_html_class($tag->name),
                'atts' => $attrs,
                'html' => $html,
                'validation_error' => $validation_error,
            )
        );
        return $this->display(__FILE__, 'form_checkbox.tpl');
    }

    public function ets_cfu_acceptance_form_tag_handler($tag)
    {
        $validation_error = false;
        $class = ets_cfu_form_controls_class($tag->type);
        if ($validation_error) {
            $class .= ' ets_cfu-not-valid';
        }
        if ($tag->has_option('invert')) {
            $class .= ' invert';
        }
        if ($tag->has_option('optional')) {
            $class .= ' optional';
        }
        $attrs = array(
            'class' => trim($class),
        );
        $item_attrs = array();
        $item_attrs['type'] = 'checkbox';
        $item_attrs['name'] = $tag->name;
        $item_attrs['value'] = '1';
        $item_attrs['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
        $item_attrs['aria-invalid'] = $validation_error ? 'true' : 'false';
        if ($tag->has_option('default:on')) {
            $item_attrs['checked'] = 'checked';
        }
        $item_attrs['class'] = $tag->get_class_option();
        $item_attrs['id'] = $tag->get_id_option();
        $content = empty($tag->content)
            ? (string)reset($tag->values)
            : $tag->content;
        $content = trim($content);
        $this->smarty->assign(array(
            'html_class' => ets_cfu_sanitize_html_class($tag->name),
            'atts' => $attrs,
            'item_attrs' => $item_attrs,
            'content' => $content,
            'validation_error' => $validation_error,
        ));
        return $this->display(__FILE__, 'form_acceptance.tpl');
    }

    public function displayReplyMessage($reply)
    {
        $this->smarty->assign(
            array(
                'reply' => $reply,
                'countReply' => (int)ETS_CFU_Contact_Message::nbMessageReply($reply->id_contact_message),
            )
        );
        return $this->display(__FILE__, 'reply.tpl');
    }

    public function processImport($zipfile = false)
    {
        $pathCached = _PS_CACHE_DIR_ . $this->name . DIRECTORY_SEPARATOR;
        if (!is_dir(_PS_CACHE_DIR_ . $this->name))
            mkdir(_PS_CACHE_DIR_ . $this->name, 0755, true);
        if (!$zipfile) {
            $savePath = $pathCached;
            if (@file_exists($savePath . 'contactformultimate.data.zip'))
                @unlink($savePath . 'contactformultimate.data.zip');
            $uploader = new Uploader('contactformdata');
            $uploader->setCheckFileSize(false);
            $uploader->setAcceptTypes(array('zip'));
            $uploader->setSavePath($savePath);
            $file = $uploader->process('contactformultimate.data.zip');
            if ($file[0]['error'] === 0) {
                if (!Tools::ZipTest($savePath . 'contactformultimate.data.zip'))
                    $this->_errors[] = $this->l('Zip file seems to be broken');
            } else {
                $this->_errors[] = $file[0]['error'];
            }
            $extractUrl = $savePath . 'contactformultimate.data.zip';
        } else
            $extractUrl = $zipfile;
        if (!@file_exists($extractUrl))
            $this->_errors[] = $this->l('Zip file doesn\'t exist');
        if (!$this->_errors) {
            $zip = new ZipArchive();
            if ($zip->open($extractUrl) === true) {
                if ($zip->locateName('Contact-Info.xml') === false) {
                    $this->_errors[] = $this->l('Import file is invalid');
                    if (@file_exists($extractUrl))
                        @unlink($extractUrl);
                }
                $zip->close();
            } else
                $this->_errors[] = $this->l('Cannot open zip file. It might be broken or damaged');
        }
        if (!$this->_errors) {
            if (!Tools::ZipExtract($extractUrl, $pathCached))
                $this->_errors[] = $this->l('Cannot extract zip data');
            if (!@file_exists($pathCached . 'Contact-Info.xml'))
                $this->_errors[] = $this->l('Import file is invalid');
        }
        if (!$this->_errors) {

            if (@file_exists($pathCached . 'Contact-Info.xml')) {
                $this->importXmlTbl(@simplexml_load_file($pathCached . 'Contact-Info.xml'));
                @unlink($pathCached . 'Contact-Info.xml');
                if (file_exists($pathCached . 'Data-Info.xml'))
                    @unlink($pathCached . 'Data-Info.xml');
            }
            if (@file_exists($extractUrl)) {
                @unlink($extractUrl);
            }
        }
        if (!$this->_errors) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateContactForm') . '&conf=99');
        }

    }

    public function importXmlTbl($xml)
    {
        $languages = Language::getLanguages(false);
        if ($xml && isset($xml->contactfrom)) {
            if (Tools::getValue('importdeletebefore')) {
                ETS_CFU_Contact::deleteAll($this->context->shop->id);
            }
            foreach ($xml->contactfrom as $dataContact) {
                $id_contact = (int)$dataContact['id'];
                if (Tools::getValue('importoverride') && ETS_CFU_Contact::getContactById($id_contact, $this->context->shop->id))
                    $contact = new ETS_CFU_Contact($id_contact);
                else {
                    $contact = new ETS_CFU_Contact();
                    $contact->position = ETS_CFU_Contact::getPosition($this->context->shop->id);
                }
                $contact_fields = Ets_cfudefines::getInstance($this)->getFields('contact');
                $configs = $contact_fields['form']['input'];
                if ($configs) {
                    foreach ($configs as $config) {
                        $key = $config['name'];
                        if (!isset($config['lang']) || !$config['lang'] && $key != 'postion')
                            $contact->$key = $dataContact->$key;
                        if ($key == 'id_employee')
                            $contact->id_employee = (int)$this->context->employee->id;
                    }
                }
                if (isset($dataContact->datalanguage) && $dataContact->datalanguage) {
                    $language_xml_default = null;
                    foreach ($dataContact->datalanguage as $language_xml) {
                        if (isset($language_xml['default']) && (int)$language_xml['default']) {
                            $language_xml_default = $language_xml;
                            break;
                        }
                    }
                    $list_language_xml = array();
                    foreach ($dataContact->datalanguage as $language_xml) {
                        $iso_code = (string)$language_xml['iso_code'];
                        $id_lang = Language::getIdByIso($iso_code);
                        $list_language_xml[] = $id_lang;
                        if ($id_lang) {
                            foreach ($configs as $config) {
                                $key = $config['name'];
                                if (isset($config['lang']) && $config['lang']) {
                                    $temp = $contact->$key;
                                    $temp[$id_lang] = (string)$language_xml->$key;
                                    if (!$temp[$id_lang]) {
                                        if (isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key) && $language_xml_default->$key) {
                                            $temp[$id_lang] = (string)$language_xml_default->$key;
                                        }
                                    }
                                    $contact->$key = $temp;
                                }
                            }
                        }
                    }
                    foreach ($languages as $language) {
                        if (!in_array($language['id_lang'], $list_language_xml)) {
                            foreach ($configs as $config) {
                                $key = $config['name'];
                                if (isset($config['lang']) && $config['lang']) {
                                    $temp = $contact->$key;
                                    if (isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key) && $language_xml_default->$key) {
                                        $temp[$language['id_lang']] = $language_xml_default->$key;
                                    }
                                    $contact->$key = $temp;
                                }
                            }
                        }
                    }
                }
                $contact->save();
            }
        }
    }

    public function hookDisplayBackOfficeFooter()
    {
        if (version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        if (($cache_id = ETS_CFU_SmartyCache::getCachedId('admin_footer')) == null || !$this->isCached('admin_footer.tpl', $cache_id)) {
            $this->smarty->assign(array(
                'link_ajax' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            ));
        }
        return $this->display(__FILE__, 'admin_footer.tpl', $cache_id);
    }

    public function getDevice()
    {
        return ($userAgent = new ETS_CFU_Browser()) ? $userAgent->getBrowser() . ' ' . $userAgent->getVersion() . ' ' . $userAgent->getPlatform() : $this->l('Unknown');
    }

    public function overrideSmartyOutputContent($classname)
    {
        return trim($classname) === 'FrontController' && version_compare(_PS_VERSION_, '1.7.0.0', '<=');
    }

    public function addOverride($classname)
    {
        if (!$this->overrideSmartyOutputContent($classname))
            return true;
        return parent::addOverride($classname);
    }

    public function removeOverride($classname)
    {
        if (!$this->overrideSmartyOutputContent($classname))
            return true;
        return parent::addOverride($classname);
    }

    public function getOrderReferrence()
    {
        $orders = [];

        if (!isset($this->customer_thread['id_order'])) {
            $customer_orders = Order::getCustomerOrders($this->context->customer->id);

            foreach ($customer_orders as $customer_order) {
                $myOrder = new Order((int)$customer_order['id_order']);

                if (Validate::isLoadedObject($myOrder)) {
                    $orders[$customer_order['id_order']] = $customer_order;
                    $orders[$customer_order['id_order']]['products'] = $myOrder->getProducts();
                }
            }
        } elseif ((int)$this->customer_thread['id_order'] > 0) {
            $myOrder = new Order($this->customer_thread['id_order']);

            if (Validate::isLoadedObject($myOrder)) {
                $orders[$myOrder->id] = $this->context->controller->objectPresenter->present($myOrder);
                $orders[$myOrder->id]['id_order'] = $myOrder->id;
                $orders[$myOrder->id]['products'] = $myOrder->getProducts();
            }
        }

        if ($this->customer_thread['id_product']) {
            $id_order = isset($this->customer_thread['id_order']) ?
                (int)$this->customer_thread['id_order'] :
                0;

            $orders[$id_order]['products'][(int)$this->customer_thread['id_product']] = $this->context->controller->objectPresenter->present(
                new Product((int)$this->customer_thread['id_product'])
            );
        }

        $res = array();
        if ($orders) {
            foreach ($orders as $order) {
                $res[] = $order['reference'];
            }
        }

        return $res;
    }

    public static function checkVersionPs($ver, $compare = '>=')
    {
        if (version_compare(_PS_VERSION_, $ver, $compare)) {
            return true;
        }
        return false;
    }

    public function getDownloadLink()
    {
        $token = Tools::getAdminTokenLite('AdminETSEMDownload');
        return version_compare(_PS_VERSION_, '1.5.0.0', '<') ? 'index.php?tab=AdminContactFormUltimateDownload&token=' . (trim($token) !== '' ? $token : trim(Tools::getValue('token'))) : $this->context->link->getAdminLink('AdminContactFormUltimateDownload');
    }

    public function getCacheId($name = null, $before = null, $after = null)
    {
        if (!(int)Configuration::get('ETS_CFU_CACHE_ENABLED'))
            return null;
        $cache_id = $this->name . (trim(Tools::strtolower($name)) ? '|' . trim(Tools::strtolower($name)) : '') . (is_array($before) ? '|' . implode('|', $before) : ($before ? '|' . trim($before, '|') : ''));
        $cache_id = parent::getCacheId($cache_id);

        return $cache_id . (is_array($after) ? '|' . implode('|', $after) : ($after ? '|' . trim($after, '|') : ''));
    }

    public function getDefaultCompileId()
    {
        return Context::getContext()->shop->theme->getName();
    }

    public function clearCache($template, $cache_id = null, $compile_id = null)
    {
        if ($compile_id === null) {
            $compile_id = $this->getDefaultCompileId();
        }

        if (static::$_batch_mode) {
            if ($cache_id === null) {
                $cache_id = $this->name;
            }

            $key = $template . '-' . $cache_id . '-' . $compile_id;
            if (!isset(static::$_defered_clearCache[$key])) {
                static::$_defered_clearCache[$key] = [$this->getTemplatePath($template), $cache_id, $compile_id];
            }
        } else {
            if ($cache_id === null) {
                $cache_id = $this->name;
            }

            Tools::enableCache();
            $number_of_template_cleared = Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
            Tools::restoreCacheSettings();

            return $number_of_template_cleared;
        }
    }

    private function safeMkDir($path, $permission = 0755)
    {
        if (!@mkdir($concurrentDirectory = $path, $permission) && !is_dir($concurrentDirectory)) {
            throw new \PrestaShopException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
    }

    private function checkOverrideDir()
    {
        if (defined('_PS_OVERRIDE_DIR_')) {
            $psOverride = @realpath(_PS_OVERRIDE_DIR_) . DIRECTORY_SEPARATOR;
            if (!is_dir($psOverride)) {
                $this->safeMkDir($psOverride);
            }
            $base = str_replace('/', DIRECTORY_SEPARATOR, $this->getLocalPath() . 'override');
            $iterator = new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS);
            /** @var RecursiveIteratorIterator|\SplFileInfo[] $iterator */
            $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            $iterator->setMaxDepth(4);
            foreach ($iterator as $k => $item) {
                if (!$item->isDir()) {
                    continue;
                }
                $path = str_replace($base . DIRECTORY_SEPARATOR, '', $item->getPathname());
                if (!@file_exists($psOverride . $path)) {
                    $this->safeMkDir($psOverride . $path);
                    @touch($psOverride . $path . DIRECTORY_SEPARATOR . '_do_not_remove');
                }
            }
            if (!file_exists($psOverride . 'index.php')) {
                Tools::copy($this->getLocalPath() . 'index.php', $psOverride . 'index.php');
            }
        }
    }

    public function enable($force_all = false)
    {
        if (!$force_all && ETS_CFU_Tools::checkEnableOtherShop($this->id) && $this->getOverrides() != null) {
            try {
                $this->uninstallOverrides();
            } catch (Exception $e) {
                if ($e) {
                    //
                }
            }
        }
        $this->checkOverrideDir();
        return $this->fixOverrideConflict() && parent::enable($force_all);
    }

    public function disable($force_all = false)
    {
        if (parent::disable($force_all)) {
            if (!$force_all && ETS_CFU_Tools::checkEnableOtherShop($this->id)) {
                if (property_exists('Tab', 'enabled') && method_exists($this, 'get') && $dispatcher = $this->get('event_dispatcher')) {
                    /** @var \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
                    $dispatcher->addListener(\PrestaShopBundle\Event\ModuleManagementEvent::DISABLE, function (\PrestaShopBundle\Event\ModuleManagementEvent $event) {
                        ETS_CFU_Tools::activeTab($this->name);
                    });
                }
                if ($this->getOverrides() != null) {
                    try {
                        $this->installOverrides();
                    } catch (Exception $e) {
                        if ($e) {
                            //
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}    
