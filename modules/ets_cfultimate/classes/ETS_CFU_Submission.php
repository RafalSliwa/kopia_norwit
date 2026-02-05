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

class ETS_CFU_Submission
{
    const ETS_CFU_DO_SHOW = 1;
    const ETS_CFU_DO_HIDE = 2;
    const ETS_CFU_DO_SHOW_MULTIPLE = 3;
    const ETS_CFU_DO_HIDE_MULTIPLE = 4;

    const ETS_CFU_OPERATOR_CONTAINER = 1;
    const ETS_CFU_OPERATOR_DO_NOT_CONTAINER = 2;
    const ETS_CFU_OPERATOR_EMPTY = 3;
    const ETS_CFU_OPERATOR_FILLED = 4;
    const ETS_CFU_OPERATOR_EQUAL = 5;
    const ETS_CFU_OPERATOR_NOT_EQUAL = 6;
    const ETS_CFU_OPERATOR_LESS_THAN = 7;
    const ETS_CFU_OPERATOR_GREATER_THAN = 8;
    const ETS_CFU_OPERATOR_BEFORE = 9;
    const ETS_CFU_OPERATOR_AFTER = 10;
    private static $instance;
    /* @var $contact_form ETS_CFU_Contact_Form */
    private $contact_form;
    private $status = 'init';
    private $posted_data = array();
    private $uploaded_files = array();
    private $skip_mail = false;
    private $response = '';
    private $invalid_fields = array();
    private $meta = array();
    private $consent = array();
    private $attachments = array();

    private function __construct()
    {
    }

    public static function get_instance(ETS_CFU_Contact_Form $contact_form = null, $args = array())
    {
        $args = array_merge($args, array(
            'skip_mail' => false,
        ));
        if (empty(self::$instance)) {
            if (null == $contact_form) {
                return null;
            }
            self::$instance = new self;
            self::$instance->contact_form = $contact_form;
            self::$instance->skip_mail = (bool)$args['skip_mail'];
            self::$instance->setup_posted_data();
            self::$instance->submit();
        } elseif (null != $contact_form) {
            return null;
        }
        return self::$instance;
    }

    private function setup_posted_data()
    {
        $posted_data = (array)$_POST;
        $posted_data = array_diff_key($posted_data, array('_wpnonce' => ''));
        $posted_data = $this->sanitize_posted_data($posted_data);
        $tags = $this->contact_form->scan_form_tags();
        foreach ((array)$tags as $tag) {
            if (empty($tag->name)) {
                continue;
            }
            $name = $tag->name;
            $pipes = $tag->pipes;
            $value_orig = $value = '';

            if (isset($posted_data[$name])) {
                $value_orig = $value = $posted_data[$name];
            }
            if ($pipes instanceof ETS_CFU_Pipes && !$pipes->zero()) {
                if (is_array($value_orig)) {
                    $value = array();
                    foreach ($value_orig as $v) {
                        $value[] = $pipes->do_pipe(ets_cfu_unslash($v));
                    }
                } else {
                    $value = $pipes->do_pipe(ets_cfu_unslash($value_orig));
                }
            }

            $posted_data[$name] = $value;
        }
        $this->posted_data = $posted_data;
        return $this->posted_data;
    }

    private function sanitize_posted_data($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, 'sanitize_posted_data'), $value);
        } elseif (is_string($value)) {
            $value = ets_cfu_check_invalid_utf8($value);
            $value = ets_cfu_kses_no_null($value);
        }
        return $value;
    }

    public function ipBlackList($ip_blacklist)
    {
        if (!$ip_blacklist)
            return false;
        $remote_addr = Tools::getRemoteAddr();
        $ips = explode("\n", $ip_blacklist);
        if ($ips) {
            foreach ($ips as $ip) {
                if (preg_match('/^' . $this->formatPattern($ip) . '$/', $remote_addr)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isContentSpam($regexPattern)
    {
        if (!$regexPattern) {
            return false;
        }
        $regexPatterns = explode("\n", $regexPattern);
        $form_tags = $this->contact_form->scan_form_tags();
        foreach ($form_tags as $tag) {
            if ($tag->basetype === 'text' || $tag->basetype === 'textarea') {
                $input_var = Tools::getValue($tag->name);
                foreach ($regexPatterns as $pattern) {
                    if (preg_match($pattern, $input_var)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isEmailSpam($regexPattern)
    {
        if (!$regexPattern) {
            return false;
        }
        $regexPatterns = explode("\n", $regexPattern);
        $form_tags = $this->contact_form->scan_form_tags();
        foreach ($form_tags as $tag) {
            if ($tag->basetype === 'email') {
                $input_var = Tools::getValue($tag->name);
                foreach ($regexPatterns as $pattern) {
                    if (preg_match($pattern, $input_var)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function formatPattern($pattern)
    {
        return str_replace('*', '(.*)', trim($pattern));
    }

    private function submit()
    {
        if (!$this->is('init')) {
            return $this->status;
        }
        $this->meta = array(
            'remote_ip' => $this->get_remote_ip_addr(),
            'user_agent' => $this->contact_form->user_agent,
            'url' => $this->get_request_url(),
            'timestamp' => ets_cfu_current_time('timestamp'),
            'unit_tag' => $this->contact_form->unit_tag,
            'container_post_id' => $this->contact_form->container_post_id,
            'current_user_id' => (int)Context::getContext()->customer->id,
        );
        $contact_form = $this->contact_form;

        if ($this->ipBlackList(Configuration::get('ETS_CFU_IP_BLACK_LIST'))) {
            $this->set_status('validation_failed');
            $this->set_response($contact_form->message('ip_black_list'));
        } elseif ($this->isEmailSpam(Configuration::get('ETS_CFU_REGEX_FILTER_SPAM_EMAIL'))) {
            $this->set_status('validation_failed');
            $this->set_response($contact_form->message('filter_spam_email'));
        } elseif ($this->isContentSpam(Configuration::get('ETS_CFU_REGEX_FILTER_SPAM_CONTENT'))) {
            $this->set_status('validation_failed');
            $this->set_response($contact_form->message('filter_spam_content'));
        } elseif (!$this->validate()) {
            $this->set_status('validation_failed');
            $this->set_response($contact_form->message('validation_error'));
        } elseif (!$this->accepted()) {
            $this->set_status('acceptance_missing');
            $this->set_response($contact_form->message('accept_terms'));
        } elseif ($this->spam()) {
            $this->set_status('spam');
            $this->set_response($contact_form->message('spam'));
        } elseif (($send_mail = $this->mail()) === true) {
            $this->synMailchimp();
            if ($contact_form->thank_you_active) {
                if (trim($contact_form->thank_you_page) == 'thank_page_url') {
                    $this->set_status('mail_redirect');
                    $this->set_response($contact_form->message('thank_you_url'));
                } else {
                    $this->set_status('load_thank_page');
                    $base_url = Ets_CfUltimate::getLinkContactForm($contact_form->id, (int)Context::getContext()->language->id, 'thank');
                    $this->set_response($base_url);
                }
            } else {
                $this->set_status('mail_sent');
                $this->set_response($contact_form->message('mail_sent_ok'));
            }
        } else {
            $this->set_status('mail_failed');
            if ($send_mail === false)
                $this->set_response($contact_form->message('mail_sent_ng'));
            elseif ($send_mail == -1)
                $this->set_response('Invalid mail to');
            elseif ($send_mail == -2)
                $this->set_response('Invalid e-mail subject');
        }
        if (!$contact_form->save_message)
            $this->remove_uploaded_files();

        return $this->status;
    }

    public function synMailchimp()
    {
        if ($this->contact_form instanceof ETS_CFU_Contact_Form && $this->contact_form->mailchimp_enabled) {
            $contact = new ETS_CFU_Contact($this->contact_form->id, Context::getContext()->language->id);
            if ($contact->mailchimp_audience && $contact->mailchimp_api_key && $contact->mailchimp_mapping_data) {
                $form_tags = $this->contact_form->scan_form_tags();
                $email_address = null;
                $merge_fields = [];
                $mapping_data = json_decode($contact->mailchimp_mapping_data, true);
                if ($form_tags) {
                    foreach ($form_tags as $tag) {
                        if (isset($mapping_data[$tag->name]) && is_array($mapping_data[$tag->name])) {
                            $tagName = isset($mapping_data[$tag->name]['tag']) && $mapping_data[$tag->name]['tag'] !== '' ? $mapping_data[$tag->name]['tag'] : null;
                            if ($tagName === null)
                                continue;
                            if (strpos($tagName, 'ADDRESS.') !== false) {
                                $tagNames = explode('.', $tagName);
                                if (count($tagNames) > 1)
                                    $merge_fields[$tagNames[0]][$tagNames[1]] = Tools::getValue($tag->name);
                            } else
                                $merge_fields[$tagName] = Tools::getValue($tag->name);
                        }
                        if ($tag->basetype === 'email') {
                            $email_address = Tools::getValue($tag->name);
                        }
                    }
                    if (isset($merge_fields['ADDRESS']) && (
                            empty($merge_fields['ADDRESS']) ||
                            empty($merge_fields['ADDRESS']['addr1']) ||
                            empty($merge_fields['ADDRESS']['city']) ||
                            empty($merge_fields['ADDRESS']['zip']) ||
                            empty($merge_fields['ADDRESS']['state']) ||
                            empty($merge_fields['ADDRESS']['country'])
                        )) {
                        unset($merge_fields['ADDRESS']);
                    }
                }
                if ($email_address !== null) {
                    EtsCfuMailChimp::getInstance()->addMergeFields($contact->mailchimp_api_key, $contact->mailchimp_audience, ['email_address' => $email_address, 'merge_fields' => $merge_fields]);
                }
            }
        }
    }

    public function is($status)
    {
        return $this->status == $status;
    }

    private function get_remote_ip_addr()
    {
        $ip_addr = '';

        if (isset($_SERVER['REMOTE_ADDR'])
            && $_SERVER['REMOTE_ADDR']) {
            $ip_addr = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_addr;
    }

    private function get_request_url()
    {
        $home_url = ets_cfu_untrailingslashit(Context::getContext()->link->getPageLink('index'));
        if (self::is_restful()) {
            $referer = isset($_SERVER['HTTP_REFERER'])
                ? trim($_SERVER['HTTP_REFERER']) : '';
            if ($referer && 0 === strpos($referer, $home_url)) {
                return ets_cfu_esc_url_raw($referer);
            }
        }
        $url = preg_replace('%(?<!:|/)/.*$%', '', $home_url)
            . ets_cfu_get_request_uri();
        return $url;
    }

    public static function is_restful()
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }

    /* @var $tag ETS_CFU_Form_Tag */
    private function doCondition($tag, $key, $operators, $values, $dos, $fields, &$hidden_fields = [])
    {
        $logic = false;
        $field_value = Tools::getValue($tag->name);
        switch ($operators[$key]) {
            case self::ETS_CFU_OPERATOR_CONTAINER:
                if (Tools::strpos($field_value, $values[$key]) !== false) {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_DO_NOT_CONTAINER:
                if (Tools::strpos($field_value, $values[$key]) === false) {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_EMPTY:
                if (trim($field_value) == '') {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_FILLED:
                if (trim($field_value) != '') {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_EQUAL:
                $eq = $this->doConvertValue($tag, $values[$key], $key);
                if ($eq[0] == $eq[1]) {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_NOT_EQUAL:
                $not_eq = $this->doConvertValue($tag, $values[$key], $key);
                if ($not_eq[0] != $not_eq[1]) {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_BEFORE:
            case self::ETS_CFU_OPERATOR_LESS_THAN:
                $le = $this->doConvertValue($tag, $values[$key], $key);
                if ($le[0] < $le[1]) {
                    $logic = true;
                }
                break;
            case self::ETS_CFU_OPERATOR_AFTER:
            case self::ETS_CFU_OPERATOR_GREATER_THAN:
                $gt = $this->doConvertValue($tag, $values[$key], $key);
                if ($gt[0] > $gt[1]) {
                    $logic = true;
                }
                break;
        }
        $this->doField($dos[$key], $fields[$key], $logic, $hidden_fields);
    }

    /* @var $tag ETS_CFU_Form_Tag */
    private function doConvertValue($tag, $value2, $key = null)
    {
        $field_value = Tools::getValue($tag->name);
        switch ($tag->basetype) {
            case 'number':
                $convert1 = $field_value;
                $convert2 = $value2;
                break;
            case 'date':
                $convert1 = strtotime($field_value);
                $convert2 = strtotime($value2);
                break;
            case 'checkbox':
                $convert1 = 0;
                $convert2 = 1;
                $values = $field_value;
                if (count($value2) > 0 && count($values) > 0 && count($value2) === count($values)) {
                    $convert1 = 1;
                    foreach ($values as $value) {
                        if (!in_array($value, $value2)) {
                            $convert1 = 0;
                            return [$convert1, $convert2];
                        }
                    }
                }
                break;
            case 'radio':
                $convert1 = $field_value;
                $convert2 = $value2[0];
                break;
            case 'select':
            case 'menu':
                $convert1 = $field_value;
                $convert2 = $value2;
                if ($tag->has_option('multiple')) {
                    $convert1 = Tools::getValue($tag->name, []);
                    if (is_array($convert1) && count($convert1) > 0) {
                        foreach ($convert1 as $item) {
                            if ($item == $convert2) {
                                $convert1 = $item;
                                return [$convert1, $convert2];
                            }
                        }
                    }
                }
                break;
            default:
                $convert1 = $field_value;
                $convert2 = $value2;
                break;
        }
        return [$convert1, $convert2];
    }

    private function doField($_do, $fields, $logic, &$hidden_fields)
    {
        if (!$_do || !$fields)
            return;
        switch ($_do) {
            case self::ETS_CFU_DO_HIDE:
            case self::ETS_CFU_DO_HIDE_MULTIPLE:
                if ($logic) {
                    foreach ($fields as $field) {
                        $hidden_fields[] = $field;
                    }
                }
                break;
            case self::ETS_CFU_DO_SHOW:
            case self::ETS_CFU_DO_SHOW_MULTIPLE:
                if (!$logic) {
                    foreach ($fields as $field) {
                        $hidden_fields[] = $field;
                    }
                }
                break;
        }
    }

    /* @var $tag ETS_CFU_Form_Tag */
    private function validateCondition($tags)
    {
        $hidden_fields = [];
        $condition = json_decode($this->contact_form->condition, true);
        if (!$tags || !$condition) {
            return $hidden_fields;
        }
        foreach ($condition['if'] as $key => $item) {
            $tag = $this->getFieldForm($tags, $item);
            $this->doCondition($tag, $key, $condition['operator'], $condition['value'], $condition['do'], $condition['fields'], $hidden_fields);
        }
        return $hidden_fields;
    }

    private function getFieldForm($tags, $key)
    {
        foreach ($tags as $tag) {
            if ($tag->name == $key)
                return $tag;
        }
    }

    private function validate()
    {
        if ($this->invalid_fields) {
            return false;
        }
        require_once(_PS_MODULE_DIR_ . 'ets_cfultimate/classes/ETS_CFU_Validation.php');
        $result = new ETS_CFU_Validation();
        $tags = $this->contact_form->scan_form_tags();
        $hidden_fields = $this->validateCondition($tags);
        foreach ($tags as $tag) {
            if (in_array($tag->name, $hidden_fields) || $tag->has_option('mod_reference') && (!Context::getContext()->customer->logged || !getOrderReferrence())) {
                continue;
            }
            $type = str_replace('*', '', $tag->type);

            if ($type == 'radio')
                $type = 'checkbox';
            if ($type == 'range')
                $type = 'number';
            $func = 'ets_cfu_' . $type . '_validation_filter';
            if (function_exists($func))
                $result = $func($result, $tag);
            else
                $result = ets_cfu_text_validation_filter($result, $tag);
        }

        $this->invalid_fields = $result->get_invalid_fields();
        return $result->is_valid();
    }

    private function accepted()
    {
        return true;
    }

    private function spam()
    {
        return ets_cfu_recaptcha_check_with_google(false);
    }

    private function mail()
    {
        $contact_form = $this->contact_form;

        $result = ETS_CFU_Mail::send($contact_form->prop('mail'), 'mail', true);
        if ($result === true) {
            $additional_mail = array();
            if (($mail_2 = $contact_form->prop('mail_2')) && $mail_2['active']) {
                $additional_mail['mail_2'] = $mail_2;
            }
            foreach ($additional_mail as $name => $template) {
                ETS_CFU_Mail::send($template, $name, false);
            }
            ETS_CFU_Mail::deleteFileNotUse($contact_form->prop('mail'), 'mail');
            return true;
        }
        ETS_CFU_Mail::deleteFileNotUse($contact_form->prop('mail'), 'mail');
        return $result;
    }

    public function remove_uploaded_files()
    {
        foreach ((array)$this->uploaded_files as $path) {
            ets_cfu_rmdir_p($path);
            if (($dir = dirname($path))
                && false !== ($files = scandir($dir))
                && !array_diff($files, array('.', '..'))) {
                rmdir($dir);
            }
        }
    }

    public function get_status()
    {
        return $this->status;
    }

    public function set_status($status)
    {
        if (preg_match('/^[a-z][0-9a-z_]+$/', $status)) {
            $this->status = $status;
            return true;
        }
        return false;
    }

    public function get_response()
    {
        return $this->response;
    }

    public function set_response($response)
    {
        $this->response = $response;
        return true;
    }

    public function get_contact_form()
    {
        return $this->contact_form;
    }

    public function get_invalid_field($name)
    {
        if (isset($this->invalid_fields[$name])) {
            return $this->invalid_fields[$name];
        } else {
            return false;
        }
    }

    public function get_invalid_fields()
    {
        return $this->invalid_fields;
    }

    public function get_posted_data($name = '')
    {
        if (!empty($name)) {
            if (isset($this->posted_data[$name])) {
                return $this->posted_data[$name];
            } else {
                return null;
            }
        }
        return $this->posted_data;
    }

    public function add_consent($name, $conditions)
    {
        $this->consent[$name] = $conditions;
        return true;
    }

    public function collect_consent()
    {
        return (array)$this->consent;
    }

    public function uploaded_files()
    {
        return $this->uploaded_files;
    }

    public function attachments()
    {
        return $this->attachments;
    }

    public function add_uploaded_file($name, $file_path, $attachment)
    {
        $this->uploaded_files[$name] = $file_path;
        $this->attachments[$name] = $attachment;
        if (empty($this->posted_data[$name])) {
            $this->posted_data[$name] = basename($file_path);
        }
    }

    private function is_blacklisted()
    {
        $target = ets_cfu_array_flatten($this->posted_data);
        $target[] = $this->get_meta('remote_ip');
        $target[] = $this->get_meta('user_agent');
        $target = implode("\n", $target);
        return (bool)ets_cfu_blacklist_check($target);
    }

    public function get_meta($name)
    {
        if (isset($this->meta[$name])) {
            return $this->meta[$name];
        }
    }
}