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

function ets_cfu_kses_no_null($string, $options = null)
{
    if (!isset($options['slash_zero'])) {
        $options = array('slash_zero' => 'remove');
    }

    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
    if ('remove' == $options['slash_zero']) {
        $string = preg_replace('/\\\\+0+/', '', $string);
    }
    return $string;
}

function ets_cfu_absint($maybeint)
{
    return abs((int)$maybeint);
}

function ets_cfu_specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false)
{
    $string = (string)$string;

    if (0 === Tools::strlen($string))
        return '';

    if (!preg_match('/[&<>"\']/', $string))
        return $string;

    if (empty($quote_style))
        $quote_style = ENT_NOQUOTES;
    elseif (!in_array($quote_style, array(0, 2, 3, 'single', 'double'), true))
        $quote_style = ENT_QUOTES;


    if (in_array($charset, array('utf8', 'utf-8', 'UTF8')))
        $charset = 'UTF-8';

    $_quote_style = $quote_style;

    if ($quote_style === 'double') {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ($quote_style === 'single') {
        $quote_style = ENT_NOQUOTES;
    }

    if (!$double_encode) {
        $string = ets_cfu_kses_normalize_entities($string);
    }

    $string = @htmlspecialchars($string, $quote_style, $charset, $double_encode);

    if ('single' === $_quote_style)
        $string = str_replace("'", '&#039;', $string);

    return $string;
}

function ets_cfu_check_invalid_utf8($string, $strip = false)
{
    $string = (string)$string;

    if (0 === Tools::strlen($string)) {
        return '';
    }

    static $is_utf8 = null;
    if (!isset($is_utf8)) {
        $is_utf8 = true;
    }
    if (!$is_utf8) {
        return $string;
    }

    static $utf8_pcre = null;
    if (!isset($utf8_pcre)) {
        $utf8_pcre = @preg_match('/^./u', 'a');
    }
    if (!$utf8_pcre) {
        return $string;
    }

    if (1 === @preg_match('/^./us', $string)) {
        return $string;
    }

    if ($strip && function_exists('iconv')) {
        return iconv('utf-8', 'utf-8', $string);
    }

    return '';
}

function ets_cfu_esc_html($text)
{
    $safe_text = ets_cfu_check_invalid_utf8($text);
    $safe_text = ets_cfu_specialchars($safe_text, ENT_QUOTES);
    return $safe_text;
}

function ets_cfu_autop_preserve_newline_callback($matches)
{
    return str_replace("\n", displayText('', 'WPPreserveNewline', [], true), $matches[0]);
}

function ets_cfu_sanitize_query_var($text)
{
    $text = ets_cfu_unslash($text);
    $text = ets_cfu_check_invalid_utf8($text);
    if (false !== strpos($text, '<')) {
        $text = ets_cfu_pre_kses_less_than($text);
        $text = ets_cfu_strip_all_tags($text);
    }

    $text = preg_replace('/%[a-f0-9]{2}/i', '', $text);
    $text = preg_replace('/ +/', ' ', $text);
    $text = trim($text, ' ');

    return $text;
}

function ets_cfu_pre_kses_less_than($text)
{
    return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'ets_cfu_pre_kses_less_than_callback', $text);
}

function ets_cfu_pre_kses_less_than_callback($matches)
{
    if (false === strpos($matches[0], '>'))
        return ets_cfu_esc_html($matches[0]);
    return $matches[0];
}

function ets_cfu_strip_quote($text)
{
    $text = trim($text);

    if (preg_match('/^"(.*)"$/s', $text, $matches)) {
        $text = $matches[1];
    } elseif (preg_match("/^'(.*)'$/s", $text, $matches)) {
        $text = $matches[1];
    }
    return $text;
}

function ets_cfu_strip_quote_deep($arr)
{
    if (is_string($arr)) {
        return ets_cfu_strip_quote($arr);
    }

    if (is_array($arr)) {
        $result = array();

        foreach ($arr as $key => $text) {
            $result[$key] = ets_cfu_strip_quote_deep($text);
        }

        return $result;
    }
}

function ets_cfu_normalize_newline($text, $to = "\n")
{
    if (!is_string($text)) {
        return $text;
    }

    $nls = array("\r\n", "\r", "\n");

    if (!in_array($to, $nls)) {
        return $text;
    }

    return str_replace($nls, $to, $text);
}

function ets_cfu_normalize_newline_deep($arr, $to = "\n")
{
    if (is_array($arr)) {
        $result = array();

        foreach ($arr as $key => $text) {
            $result[$key] = ets_cfu_normalize_newline_deep($text, $to);
        }

        return $result;
    }

    return ets_cfu_normalize_newline($arr, $to);
}

function ets_cfu_strip_newline($str)
{
    $str = (string)$str;
    $str = str_replace(array("\r", "\n"), '', $str);
    return trim($str);
}

function ets_cfu_canonicalize($text, $strto = 'lower')
{
    $text = mb_convert_kana($text, 'asKV', 'UTF-8');
    if ('lower' == $strto) {
        $text = Tools::strtolower($text);
    } elseif ('upper' == $strto) {
        $text = Tools::strtoupper($text);
    }

    $text = trim($text);
    return $text;
}

function ets_cfu_is_name($string)
{
    return preg_match('/^[A-Za-z][-A-Za-z0-9_:.]*$/', $string);
}

function ets_cfu_sanitize_unit_tag($tag)
{
    $tag = preg_replace('/[^A-Za-z0-9_-]/', '', $tag);
    return $tag;
}

function ets_cfu_is_email($email)
{
    return Validate::isEmail($email);
}

function ets_cfu_is_blacklist_email($email)
{
    $email_blacklist = Configuration::get('ETS_CFU_EMAIL_BLACK_LIST');
    if (!$email_blacklist || !($email))
        return false;
    $emails = explode("\n", $email_blacklist);
    if ($emails) {
        foreach ($emails as $pattern) {
            if (preg_match('/^' . str_replace('*', '(.*)', trim($pattern)) . '$/', $email)) {
                return true;
            }
        }
    }
    return false;
}

function ets_cfu_is_url($url)
{
    $result = (false !== filter_var($url, FILTER_VALIDATE_URL));
    return $result;
}

function ets_cfu_is_tel($tel)
{
    $result = preg_match('%^[+]?[0-9()/ -]*$%', $tel);
    return $result;
}

function ets_cfu_is_number($number)
{
    return is_numeric($number);
}

function ets_cfu_is_date($date)
{
    $result = preg_match('/^([0-9]{4,})-([0-9]{2})-([0-9]{2})( [0-9]{2}:[0-9]{2})$/', $date, $matches);

    if ($result) {
        $result = checkdate($matches[2], $matches[3], $matches[1]);
    }
    if ($result)
        return $result;
    else {
        $result = preg_match('/^([0-9]{4,})-([0-9]{2})-([0-9]{2})$/', $date, $matches);
        if ($result) {
            $result = checkdate($matches[2], $matches[3], $matches[1]);
        }
        return $result;
    }
}

function ets_cfu_is_mailbox_list($mailbox_list)
{
    if (!is_array($mailbox_list)) {
        $mailbox_text = (string)$mailbox_list;
        $mailbox_text = ets_cfu_unslash($mailbox_text);
        $mailbox_text = preg_replace('/\\\\(?:\"|\')/', 'esc-quote', $mailbox_text);
        $mailbox_text = preg_replace('/(?:\".*?\"|\'.*?\')/', 'quoted-string', $mailbox_text);
        $mailbox_list = explode(',', $mailbox_text);
    }
    $addresses = array();
    foreach ($mailbox_list as $mailbox) {
        if (!is_string($mailbox)) {
            return false;
        }
        $mailbox = trim($mailbox);
        if (preg_match('/<(.+)>$/u', $mailbox, $matches)) {
            $addr_spec = $matches[1];
        } else {
            $addr_spec = $mailbox;
        }
        if (!ets_cfu_is_email($addr_spec)) {
            return false;
        }
        $addresses[] = $addr_spec;
    }
    return $addresses;
}

function ets_cfu_mailbox_list($mailbox_list)
{
    if (!is_array($mailbox_list)) {
        $mailbox_text = (string)$mailbox_list;
        $mailbox_text = ets_cfu_unslash($mailbox_text);
        $mailbox_text = preg_replace('/\\\\(?:\"|\')/', 'esc-quote', $mailbox_text);
        $mailbox_text = preg_replace('/(?:\".*?\"|\'.*?\')/', 'quoted-string', $mailbox_text);
        $mailbox_list = explode(',', $mailbox_text);
    }
    $emails = array();
    foreach ($mailbox_list as $mailbox) {
        if (!is_string($mailbox))
            return false;
        $mailbox = trim($mailbox);
        $address = array();
        if (preg_match('/<(.+)>$/', $mailbox, $matches) && !empty($matches[1])) {
            $address['email'] = $matches[1];
            $address['name'] = preg_replace('/<(.+)>$/', '', $mailbox);
        }
        $emails[] = $address;
    }
    return $emails;
}

function ets_cfu_antiscript_file_name($filename)
{
    $filename = basename($filename);
    $parts = explode('.', $filename);

    if (count($parts) < 2) {
        return $filename;
    }

    $script_pattern = '/^(php|phtml|pl|py|rb|cgi|asp|aspx)\d?$/i';

    $filename = array_shift($parts);
    $extension = array_pop($parts);

    foreach ((array)$parts as $part) {
        if (preg_match($script_pattern, $part)) {
            $filename .= '.' . $part . '_';
        } else {
            $filename .= '.' . $part;
        }
    }

    if (preg_match($script_pattern, $extension)) {
        $filename .= '.' . $extension . '_.txt';
    } else {
        $filename .= '.' . $extension;
    }

    return $filename;
}

function ets_cfu_mask_password($text, $length_unmasked = 0)
{
    $length = Tools::strlen($text);
    $length_unmasked = ets_cfu_absint($length_unmasked);

    if (0 == $length_unmasked) {
        if (9 < $length) {
            $length_unmasked = 4;
        } elseif (3 < $length) {
            $length_unmasked = 2;
        } else {
            $length_unmasked = $length;
        }
    }

    $text = Tools::substr($text, 0 - $length_unmasked);
    $text = str_pad($text, $length, '*', STR_PAD_LEFT);
    return $text;
}

function ets_cfu_sanitize_html_class($class, $fallback = '')
{
    $sanitized = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $class);

    $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $sanitized);

    if ('' == $sanitized && $fallback) {
        return ets_cfu_sanitize_html_class($fallback);
    }
    return $sanitized;
}

function esc_cfu_textarea($text)
{
    $safe_text = htmlspecialchars($text, ENT_QUOTES);
    return $safe_text;
}

function ets_cfu_blacklist_check($target)
{
    $mod_keys = trim(Configuration::get('ets_cfu_blacklist_keys'));

    if (empty($mod_keys)) {
        return false;
    }

    $words = explode("\n", $mod_keys);

    foreach ((array)$words as $word) {
        $word = trim($word);

        if (empty($word) || 256 < Tools::strlen($word)) {
            continue;
        }

        $pattern = sprintf('#%s#i', preg_quote($word, '#'));

        if (preg_match($pattern, $target)) {
            return true;
        }
    }

    return false;
}

function ets_cfu_array_flatten($input)
{
    if (!is_array($input)) {
        return array($input);
    }

    $output = array();

    foreach ($input as $value) {
        $output = array_merge($output, ets_cfu_array_flatten($value));
    }

    return $output;
}

function ets_cfu_flat_join($input)
{
    $input = ets_cfu_array_flatten($input);
    $output = array();

    foreach ((array)$input as $value) {
        $output[] = trim((string)$value);
    }

    return implode(', ', $output);
}

function ets_cfu_support_html5()
{
    return true;
}

function ets_cfu_support_html5_fallback()
{
    return true;
}

function ets_cfu_use_really_simple_captcha()
{
    return true;
}

function ets_cfu_validate_configuration()
{
    return true;
}

function ets_cfu_load_js()
{
    return true;
}

function ets_cfu_load_css()
{
    return true;
}

function ets_cfu_format_atts($atts)
{
    $html = '';
    $prioritized_atts = array('type', 'name', 'value');
    foreach ($prioritized_atts as $att) {
        if (isset($atts[$att])) {
            $value = trim($atts[$att]);
            $html .= sprintf(' %s="%s"', $att, $value);
            unset($atts[$att]);
        }
    }
    foreach ($atts as $key => $value) {
        $key = Tools::strtolower(trim($key));
        if (!preg_match('/^[a-z_:][a-z_:.0-9-]*$/', $key)) {
            continue;
        }
        $value = trim($value);
        if ('' !== $value) {
            $html .= sprintf(' %s="%s"', $key, $value);
        }
    }
    $html = trim($html);
    return $html;
}

function ets_cfu_link($url, $anchor_text, $args = '')
{
    $defaults = array(
        'id' => '',
        'class' => '',
    );

    $args = ets_cfu_parse_args($args, $defaults);
    $args = array_intersect_key($args, $defaults);
    $atts = ets_cfu_format_atts($args);

    $link = sprintf(displayText('%2$s', 'a', ['href' => '%1$s', '%3$s' => null]),
        ets_cfu_esc_url($url),
        ets_cfu_esc_html($anchor_text),
        $atts ? (' ' . $atts) : '');

    return $link;
}

function ets_cfu_get_request_uri()
{
    static $request_uri = '';

    if (empty($request_uri)) {
        $request_uri = ets_cfu_add_query_arg(array());
    }

    return ets_cfu_esc_url_raw($request_uri);
}

function ets_cfu_version($args = '')
{
    $defaults = array(
        'limit' => -1,
        'only_major' => false,
    );

    $args = ets_cfu_parse_args($args, $defaults);

    if ($args['only_major']) {
        $args['limit'] = 2;
    }

    $args['limit'] = (int)$args['limit'];

    $ver = WPCF7_VERSION;
    $ver = strtr($ver, '_-+', '...');
    $ver = preg_replace('/[^0-9.]+/', ".$0.", $ver);
    $ver = preg_replace('/[.]+/', ".", $ver);
    $ver = trim($ver, '.');
    $ver = explode('.', $ver);

    if (-1 < $args['limit']) {
        $ver = array_slice($ver, 0, $args['limit']);
    }

    $ver = implode('.', $ver);

    return $ver;
}

function ets_cfu_version_grep($version, array $input)
{
    $pattern = '/^' . preg_quote((string)$version, '/') . '(?:\.|$)/';

    return preg_grep($pattern, $input);
}

function ets_cfu_enctype_value($enctype)
{
    $enctype = trim($enctype);

    if (empty($enctype)) {
        return '';
    }

    $valid_enctypes = array(
        'application/x-www-form-urlencoded',
        'multipart/form-data',
        'text/plain',
    );

    if (in_array($enctype, $valid_enctypes)) {
        return $enctype;
    }

    $pattern = '%^enctype="(' . implode('|', $valid_enctypes) . ')"$%';

    if (preg_match($pattern, $enctype, $matches)) {
        return $matches[1];
    }

    return '';
}

function ets_cfu_rmdir_p($dir)
{
    if (is_file($dir)) {
        if (!$result = @unlink($dir)) {
            $stat = stat($dir);
            $perms = $stat['mode'];
            chmod($dir, $perms | 0200);

            if (!$result = @unlink($dir)) {
                chmod($dir, $perms);
            }
        }

        return $result;
    }

    if (!is_dir($dir)) {
        return false;
    }

    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..") {
                continue;
            }

            ets_cfu_rmdir_p(ets_cfu_path_join($dir, $file));
        }

        closedir($handle);
    }

    if (false !== ($files = scandir($dir))
        && !array_diff($files, array('.', '..'))) {
        return rmdir($dir);
    }

    return false;
}

function ets_cfu_build_query($args, $key = '')
{
    $sep = '&';
    $ret = array();

    foreach ((array)$args as $k => $v) {
        $k = urlencode($k);

        if (!empty($key)) {
            $k = $key . '%5B' . $k . '%5D';
        }

        if (null === $v) {
            continue;
        } elseif (false === $v) {
            $v = '0';
        }

        if (is_array($v) || is_object($v)) {
            array_push($ret, ets_cfu_build_query($v, $k));
        } else {
            array_push($ret, $k . '=' . urlencode($v));
        }
    }

    return implode($sep, $ret);
}

function ets_cfu_count_code_units($string)
{
    static $use_mb = null;

    if (is_null($use_mb)) {
        $use_mb = function_exists('mb_convert_encoding');
    }

    if (!$use_mb) {
        return false;
    }

    $string = (string)$string;
    $string = str_replace("\r\n", "\n", $string);

    $encoding = mb_detect_encoding($string, mb_detect_order(), true);

    if ($encoding) {
        $string = mb_convert_encoding($string, 'UTF-16', $encoding);
    } else {
        $string = mb_convert_encoding($string, 'UTF-16', 'UTF-8');
    }

    $byte_count = mb_strlen($string, '8bit');

    return floor($byte_count / 2);
}

function ets_cfu_is_localhost()
{
    $server_name = Tools::strtolower($_SERVER['SERVER_NAME']);
    return in_array($server_name, array('localhost', '127.0.0.1'));
}

function ets_cfu_sanitize_key($key)
{
    $key = Tools::strtolower($key);
    $key = preg_replace('/[^a-z0-9_\-]/', '', $key);
}

function ets_cfu_contact_form($id)
{
    return ETS_CFU_Contact_Form::get_instance($id);
}

function ets_cfu_get_current_contact_form()
{
    if ($current = ETS_CFU_Contact_Form::get_current()) {
        return $current;
    }
}

function ets_cfu_is_posted()
{
    if (!$contact_form = ets_cfu_get_current_contact_form()) {
        return false;
    }

    return $contact_form->is_posted();
}

function ets_cfu_get_hangover($name, $default = null)
{
    if (!ets_cfu_is_posted()) {
        return $default;
    }

    $submission = ETS_CFU_Submission::get_instance();

    if (!$submission || $submission->is('mail_sent')) {
        return $default;
    }

    return Tools::isSubmit($name) ? ets_cfu_unslash(Tools::getValue($name)) : $default;
}

function ets_cfu_get_message($status)
{
    if (!$contact_form = ets_cfu_get_current_contact_form()) {
        return '';
    }

    return $contact_form->message($status);
}

function ets_cfu_form_controls_class($type, $default = '')
{
    $type = trim($type);
    $default = array_filter(explode(' ', $default));

    $classes = array_merge(array('ets_cfu_form-control'), $default);

    $typebase = rtrim($type, '*');
    $required = ('*' == Tools::substr($type, -1));

    $classes[] = 'ets_cfu-' . $typebase;

    if ($required) {
        $classes[] = 'ets_cfu-validates-as-required';
    }

    $classes = array_unique($classes);

    return implode(' ', $classes);
}

function ets_cfu_sanitize_form($input, $default = '')
{
    if (null === $input) {
        return $default;
    }

    $output = trim($input);
    return $output;
}

function ets_cfu_sanitize_mail($input, $defaults = array())
{
    $defaults = ets_cfu_parse_args($defaults, array(
        'active' => false,
        'subject' => '',
        'sender' => '',
        'recipient' => '',
        'body' => '',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => false,
        'exclude_blank' => false,
    ));

    $input = ets_cfu_parse_args($input, $defaults);

    $output = array();
    $output['active'] = (bool)$input['active'];
    $output['subject'] = trim($input['subject']);
    $output['sender'] = trim($input['sender']);
    $output['recipient'] = trim($input['recipient']);
    $output['body'] = trim($input['body']);
    $output['additional_headers'] = '';

    $headers = str_replace("\r\n", "\n", $input['additional_headers']);
    $headers = explode("\n", $headers);

    foreach ($headers as $header) {
        $header = trim($header);

        if ('' !== $header) {
            $output['additional_headers'] .= $header . "\n";
        }
    }

    $output['additional_headers'] = trim($output['additional_headers']);
    $output['attachments'] = trim($input['attachments']);
    $output['use_html'] = (bool)$input['use_html'];
    $output['exclude_blank'] = (bool)$input['exclude_blank'];

    return $output;
}

function ets_cfu_sanitize_additional_settings($input, $default = '')
{
    if (null === $input) {
        return $default;
    }

    $output = trim($input);
    return $output;
}

function ets_cfu_parse_str($string, &$array)
{
    parse_str($string, $array);
}

function ets_cfu_text_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_text_form_tag_handler($tag);
}

function ets_cfu_textarea_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_textarea_form_tag_handler($tag);
}

function ets_cfu_select_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_select_form_tag_handler($tag);
}

function ets_cfu_submit_form_tag_handler($tag)
{
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_submit_form_tag_handler($tag);
}

function ets_cfu_html_form_tag_handler($tag)
{
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_html_form_tag_handler($tag);
}

function ets_cfu_response_form_tag_handler($tag)
{
    unset($tag);
    if ($contact_form = ets_cfu_get_current_contact_form()) {
        return $contact_form->form_response_output();
    }
}

function ets_cfu_recaptcha_form_tag_handler($tag)
{
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_recaptcha_form_tag_handler($tag);
}

function ets_cfu_captcha_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_captcha_form_tag_handler($tag);
}

function ets_cfu_quiz_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_quiz_form_tag_handler($tag);
}

function ets_cfu_number_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_number_form_tag_handler($tag);
}

function ets_cfu_hidden_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_hidden_form_tag_handler($tag);
}

function ets_cfu_file_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_file_form_tag_handler($tag);
}

function ets_cfu_date_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_date_form_tag_handler($tag);
}

function ets_cfu_count_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_count_form_tag_handler($tag);
}

function ets_cfu_checkbox_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_checkbox_form_tag_handler($tag);
}

function ets_cfu_parse_args($args, $defaults = '')
{
    if (is_object($args))
        $r = get_object_vars($args);
    elseif (is_array($args))
        $r =& $args;
    else
        ets_cfu_parse_str($args, $r);

    if (is_array($defaults))
        return array_merge($defaults, $r);
    return $r;
}

function ets_cfu_acceptance_form_tag_handler($tag)
{
    if (empty($tag->name)) {
        return '';
    }
    $ets_cfultimate = Module::getInstanceByName('ets_cfultimate');
    return $ets_cfultimate->ets_cfu_acceptance_form_tag_handler($tag);
}

function ets_cfu_autop_or_not()
{
    return true;
}

function ets_cfu_autop($pee, $br = 1)
{
    if (trim($pee) === '') {
        return '';
    }

    $pee = $pee . "\n";
    $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);

    $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
    $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
    $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);


    $form_tags_manager = ETS_CFU_Form_Tag_Manager::get_instance();
    $block_hidden_form_tags = $form_tags_manager->collect_tag_types(
        array('display-block', 'display-hidden'));
    $block_hidden_form_tags = sprintf('(?:%s)',
        implode('|', $block_hidden_form_tags));

    $pee = preg_replace('!(\[' . $block_hidden_form_tags . '[^]]*\])!',
        "\n$1\n\n", $pee);

    $pee = str_replace(array("\r\n", "\r"), "\n", $pee);

    if (strpos($pee, '<object') !== false) {
        $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee);
        $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
    }

    $pee = preg_replace("/\n\n+/", "\n\n", $pee);

    $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
    $pee = '';

    foreach ($pees as $tinkle) {
        $pee .= trim($tinkle, "\n") . "\n";
    }
    $pee = preg_replace('|<p>\s*</p>|', '', $pee);
    $pee = preg_replace('!<p>([^<]+)</(div|address|form|fieldset)>!', "<p>$1</p></$2>", $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

    $pee = preg_replace('!<p>\s*(\[' . $block_hidden_form_tags . '[^]]*\])!',
        "$1", $pee);
    $pee = preg_replace('!(\[' . $block_hidden_form_tags . '[^]]*\])\s*</p>!',
        "$1", $pee);

    if ($br) {

        $pee = preg_replace_callback(
            '/<(script|style|textarea).*?<\/\\1>/s',
            'ets_cfu_autop_preserve_newline_callback', $pee);
        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
        $pee = str_replace(displayText('', 'WPPreserveNewline', [], true), "\n", $pee);

        $pee = preg_replace('!<br />\n(\[' . $block_hidden_form_tags . '[^]]*\])!',
            "\n$1", $pee);
    }

    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);

    if (strpos($pee, '<pre') !== false) {
        $pee = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is',
            'clean_pre', $pee);
    }

    $pee = preg_replace("|\n</p>$|", '</p>', $pee);

    return $pee;
}

function ets_cfu_add_form_tag($tag, $func, $features = '')
{
    $manager = ETS_CFU_Form_Tag_Manager::get_instance();

    return $manager->add($tag, $func, $features);
}

function ets_cfu_remove_form_tag($tag)
{
    $manager = ETS_CFU_Form_Tag_Manager::get_instance();

    return $manager->remove($tag);
}

function ets_cfu_replace_all_form_tags($content)
{
    $manager = ETS_CFU_Form_Tag_Manager::get_instance();

    return $manager->replace_all($content);
}

function ets_cfu_scan_form_tags($cond = null)
{
    $contact_form = ETS_CFU_Contact_Form::get_current();

    if ($contact_form) {
        return $contact_form->scan_form_tags($cond);
    }

    return array();
}

function ets_cfu_form_tag_supports($tag, $feature)
{
    $manager = ETS_CFU_Form_Tag_Manager::get_instance();

    return $manager->tag_type_supports($tag, $feature);
}

function ets_cfu_file_form_enctype_filter($enctype)
{
    $multipart = (bool)ets_cfu_scan_form_tags(
        array('type' => array('file', 'file*')));

    if ($multipart) {
        $enctype = 'multipart/form-data';
    }

    return $enctype;
}

function ets_cfu_quiz_validation_filter($result, $tag)
{
    $name = $tag->name;

    $answer = Tools::isSubmit($name) ? ets_cfu_canonicalize(Tools::getValue($name)) : '';
    $answer = ets_cfu_unslash($answer);

    $answer_hash = ets_cfu_hash($answer, 'ets_cfu_quiz');

    $expected_hash = Tools::isSubmit('_ets_cfu_quiz_answer_' . $name)
        ? (string)Tools::getValue('_ets_cfu_quiz_answer_' . $name)
        : '';
    if ($answer_hash != $expected_hash && $expected_hash) {
        $result->invalidate($tag, ets_cfu_get_message('quiz_answer_not_correct'));
    }

    return $result;
}

function ets_cfu_file_validation_filter($result, $tag)
{
    $name = $tag->name;

    $file = isset($_FILES[$name]) ? $_FILES[$name] : null;

    if ($file['error'] && UPLOAD_ERR_NO_FILE != $file['error']) {
        $result->invalidate($tag, ets_cfu_get_message('upload_failed_php_error'));
        return $result;
    }
    if (empty($file['tmp_name']) && $tag->is_required()) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
        return $result;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return $result;
    }
    $file_type_pattern = ets_cfu_acceptable_filetypes($tag->get_option('filetypes'), 'regex');

    $file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';

    if (!preg_match($file_type_pattern, $file['name'])) {
        $result->invalidate($tag,
            ets_cfu_get_message('upload_file_type_invalid'));
        return $result;
    }
    $allowed_size = ETS_CFU_Tools::getPostMaxSizeBytes();

    if ($file_size_a = $tag->get_option('limit')) {
        $limit_pattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';
        foreach ($file_size_a as $file_size) {
            if (preg_match($limit_pattern, $file_size, $matches)) {
                $allowed_size = (int)$matches[1];

                if (!empty($matches[2])) {
                    $kbmb = Tools::strtolower($matches[2]);

                    if ('kb' == $kbmb) {
                        $allowed_size *= 1024;
                    } elseif ('mb' == $kbmb) {
                        $allowed_size *= 1024 * 1024;
                    }
                }

                break;
            }
        }
    }
    if ($file['size'] > $allowed_size) {
        $result->invalidate($tag, ets_cfu_get_message('upload_file_too_large'));
        return $result;
    }
    $uploads_dir = ets_cfu_upload_tmp_dir();
    $filename = $file['name'];
    $filename = ets_cfu_canonicalize($filename, 'as-is');
    $filename = ets_cfu_antiscript_file_name($filename);
    $filename = ets_cfu_generateRandomString(7) . '-' . ets_cfu_unique_filename($uploads_dir, str_replace([' ', ',', '|'], '-', $filename));
    $new_file = ets_cfu_path_join($uploads_dir, $filename);
    $attachment = Tools::fileAttachment($name);
    if (false === move_uploaded_file($file['tmp_name'], $new_file)) {
        $result->invalidate($tag, ets_cfu_get_message('upload_failed'));
        return $result;
    }

    if ($submission = ETS_CFU_Submission::get_instance()) {
        $submission->add_uploaded_file($name, $new_file, $attachment);
    }

    return $result;
}

function ets_cfu_generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = Tools::strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function ets_cfu_acceptable_filetypes($types = 'default', $format = 'regex')
{
    if ('default' === $types || empty($types)) {
        $types = ETS_CFU_Tools::getDefaultFileType();
    } else {
        $types_tmp = (array)$types;
        $types = array();

        foreach ($types_tmp as $val) {
            if (is_string($val)) {
                $val = preg_split('/[\s|,]+/', $val);
            }

            $types = array_merge($types, (array)$val);
        }
    }

    $types = array_unique(array_filter($types));

    $output = '';

    foreach ($types as $type) {
        $type = trim($type, ' ,.|');
        $type = str_replace(
            array('.', '+', '*', '?'),
            array('\.', '\+', '\*', '\?'),
            $type);

        if ('' === $type) {
            continue;
        }

        if ('attr' === $format || 'attribute' === $format) {
            $output .= sprintf('.%s', $type);
            $output .= ',';
        } else {
            $output .= $type;
            $output .= '|';
        }
    }

    return trim($output, ' ,|');
}

function ets_cfu_upload_tmp_dir()
{
    $dir = _PS_DOWNLOAD_DIR_ . 'ets_cfultimate';
    ets_cfu_mkdir_p($dir);
    if (file_exists(_PS_DOWNLOAD_DIR_ . 'index.php') && !file_exists($dir . DIRECTORY_SEPARATOR . 'index.php'))
        @copy(_PS_DOWNLOAD_DIR_ . 'index.php', $dir . DIRECTORY_SEPARATOR . 'index.php');
    return $dir;
}

function ets_cfu_textarea_validation_filter($result, $tag)
{
    $name = $tag->name;

    $value = Tools::isSubmit($name) ? (string)Tools::getValue($name) : '';
    if ($tag->is_required() && '' == $value) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
    }
    if ('' !== $value) {
        $maxlength = $tag->get_maxlength_option();
        $minlength = $tag->get_minlength_option();

        if ($maxlength && $minlength && $maxlength < $minlength) {
            $maxlength = $minlength = null;
        }
        $code_units = ets_cfu_count_code_units(Tools::stripslashes($value));

        if (false !== $code_units) {
            if ($maxlength && $maxlength < $code_units) {
                $result->invalidate($tag, ets_cfu_get_message('invalid_too_long'));
            } elseif ($minlength && $code_units < $minlength) {
                $result->invalidate($tag, ets_cfu_get_message('invalid_too_short'));
            }
        }
    }

    return $result;
}

function ets_cfu_text_validation_filter($result, $tag)
{
    $name = $tag->name;

    $value = Tools::getValue($name) && !is_array(Tools::getValue($name))
        ? trim(ets_cfu_unslash(strtr((string)Tools::getValue($name), "\n", " ")))
        : Tools::getValue($name);

    if ('text' == $tag->basetype) {
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
        }
    }
    if ('email' == $tag->basetype) {
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
        } elseif ('' != $value && !ets_cfu_is_email($value)) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_email'));
        } elseif (($msg = Ets_cfultimate::checkEmailBlackLists($value)) && $msg !== true) {
            $result->invalidate($tag, $msg);
        } elseif ('' != $value && ets_cfu_is_blacklist_email($value)) {
            $result->invalidate($tag, ets_cfu_get_message('email_black_list'));
        }
    }

    if ('url' == $tag->basetype) {
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
        } elseif ('' != $value && !ets_cfu_is_url($value)) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_url'));
        }
    }

    if ('tel' == $tag->basetype) {
        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
        } elseif ('' != $value && !ets_cfu_is_tel($value)) {
            $result->invalidate($tag, ets_cfu_get_message('invalid_tel'));
        }
    }
    if ('' !== $value && !is_array($value)) {
        $maxlength = $tag->get_maxlength_option();
        $minlength = $tag->get_minlength_option();

        if ($maxlength && $minlength && $maxlength < $minlength) {
            $maxlength = $minlength = null;
        }
        $code_units = ets_cfu_count_code_units(Tools::stripslashes($value));
        if (false !== $code_units) {
            if ($maxlength && $maxlength < $code_units) {
                $result->invalidate($tag, ets_cfu_get_message('invalid_too_long'));
            } elseif ($minlength && $code_units < $minlength) {
                $result->invalidate($tag, ets_cfu_get_message('invalid_too_short'));
            }
        }
    }

    return $result;
}

function ets_cfu_checkbox_validation_filter($result, $tag)
{
    $name = $tag->name;
    $is_required = $tag->is_required();
    $value = Tools::isSubmit($name) ? (array)Tools::getValue($name) : array();

    if ($is_required && empty($value)) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
    }

    return $result;
}

function ets_cfu_date_validation_filter($result, $tag)
{
    $name = $tag->name;

    $min = $tag->get_date_option('min');
    $max = $tag->get_date_option('max');

    $value = Tools::isSubmit($name)
        ? trim(strtr((string)Tools::getValue($name), "\n", " "))
        : '';

    if ($tag->is_required() && '' == $value) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
    } elseif ('' != $value && !ets_cfu_is_date($value)) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_date'));
    } elseif ('' != $value && !empty($min) && $value < $min) {
        $result->invalidate($tag, ets_cfu_get_message('date_too_early'));
    } elseif ('' != $value && !empty($max) && $max < $value) {
        $result->invalidate($tag, ets_cfu_get_message('date_too_late'));
    }

    return $result;
}

function ets_cfu_number_validation_filter($result, $tag)
{
    $name = $tag->name;

    $value = Tools::isSubmit($name)
        ? trim(strtr((string)Tools::getValue($name), "\n", " "))
        : '';

    $min = $tag->get_option('min', 'signed_int', true);
    $max = $tag->get_option('max', 'signed_int', true);

    if ($tag->is_required() && '' == $value) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
    } elseif ('' != $value && !ets_cfu_is_number($value)) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_number'));
    } elseif ('' != $value && '' != $min && (float)$value < (float)$min) {
        $result->invalidate($tag, ets_cfu_get_message('number_too_small'));
    } elseif ('' != $value && '' != $max && (float)$max < (float)$value) {
        $result->invalidate($tag, ets_cfu_get_message('number_too_large'));
    }

    return $result;
}

function ets_cfu_captcha_validation_filter($result, $tag)
{
    $name = $tag->name;
    $prefix = isset(Context::getContext()->cookie->$name) ? (string)Context::getContext()->cookie->$name : '';
    $response = Tools::isSubmit($name) ? (string)Tools::getValue($name) : '';
    if (0 == Tools::strlen($prefix) || trim($prefix) != trim($response)) {
        $result->invalidate($tag, ets_cfu_get_message('captcha_not_match'));
    }
    Context::getContext()->cookie->$name = '';
    Context::getContext()->cookie->write();
    return $result;
}

function ets_cfu_select_validation_filter($result, $tag)
{
    $name = $tag->name;
    $values = Tools::getValue($name);
    if (is_array($values)) {
        foreach ($values as $key => $value) {
            if ('' === $value) {
                unset($values[$key]);
            }
        }
    }
    $empty = empty($values);

    if ($tag->is_required() && $empty) {
        $result->invalidate($tag, ets_cfu_get_message('invalid_required'));
    }

    return $result;
}

function ets_cfu_acceptance_validation_filter($result, $tag)
{
    if ($tag->has_option('optional')) {
        return $result;
    }
    $name = $tag->name;
    $value = (Tools::getValue($name) ? 1 : 0);
    $invert = $tag->has_option('invert');
    if ($invert && $value || !$invert && !$value) {
        $result->invalidate($tag, ets_cfu_get_message('accept_terms'));
    }
    return $result;
}

function ets_cfu_esc_url($url, $protocols = null, $_context = 'display')
{

    if ('' == $url)
        return $url;

    $url = str_replace(' ', '%20', $url);
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);

    if ('' === $url) {
        return $url;
    }

    if (0 !== stripos($url, 'mailto:')) {
        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = _ets_cfu_deep_replace($strip, $url);
    }

    $url = str_replace(';//', '://', $url);

    if (strpos($url, ':') === false && !in_array($url[0], array('/', '#', '?')) &&
        !preg_match('/^[a-z0-9-]+?\.php/i', $url))
        $url = 'http://' . $url;

    if ('display' == $_context) {
        $url = ets_cfu_kses_normalize_entities($url);
        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);
    }
    if ('/' === $url[0]) {
        $good_protocol_url = $url;
    } else {
        if (!is_array($protocols))
            $protocols = ets_cfu_allowed_protocols();
        $good_protocol_url = ets_cfu_kses_bad_protocol($url, $protocols);
        if (Tools::strtolower($good_protocol_url) != Tools::strtolower($url))
            return '';
    }
    return $good_protocol_url;
}

function ets_cfu_allowed_protocols()
{
    static $protocols = array();

    if (empty($protocols)) {
        $protocols = array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', 'urn');
    }
    return $protocols;
}

function ets_cfu_kses_normalize_entities($string)
{
    $string = str_replace('&', '&amp;', $string);
    $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'ets_cfu_kses_normalize_entities2', $string);
    return $string;
}

function ets_cfu_kses_normalize_entities2($matches)
{
    if (empty($matches[1]))
        return '';

    $i = $matches[1];
    $i = "&amp;#$i;";
    return $i;
}

function ets_cfu_kses_bad_protocol($string, $allowed_protocols)
{
    $string = ets_cfu_kses_no_null($string);
    $iterations = 0;

    do {
        $original_string = $string;
        $string = ets_cfu_kses_bad_protocol_once($string, $allowed_protocols);
    } while ($original_string != $string && ++$iterations < 6);

    if ($original_string != $string)
        return '';

    return $string;
}

function ets_cfu_kses_bad_protocol_once($string, $allowed_protocols, $count = 1)
{
    $string2 = preg_split('/:|&#0*58;|&#x0*3a;/i', $string, 2);
    if (isset($string2[1]) && !preg_match('%/\?%', $string2[0])) {
        $string = trim($string2[1]);
        $protocol = ets_cfu_kses_bad_protocol_once2($string2[0], $allowed_protocols);
        if ('feed:' == $protocol) {
            if ($count > 2)
                return '';
            $string = ets_cfu_kses_bad_protocol_once($string, $allowed_protocols, ++$count);
            if (empty($string))
                return $string;
        }
        $string = $protocol . $string;
    }

    return $string;
}

function ets_cfu_kses_bad_protocol_once2($string, $allowed_protocols)
{
    $string2 = ets_cfu_kses_decode_entities($string);
    $string2 = preg_replace('/\s/', '', $string2);
    $string2 = ets_cfu_kses_no_null($string2);
    $string2 = Tools::strtolower($string2);
    $allowed = false;
    foreach ((array)$allowed_protocols as $one_protocol)
        if (Tools::strtolower($one_protocol) == $string2) {
            $allowed = true;
            break;
        }
    if ($allowed)
        return "$string2:";
    else
        return '';
}

function ets_cfu_kses_decode_entities($string)
{
    $string = preg_replace_callback('/&#([0-9]+);/', '_ets_cfu_kses_decode_entities_chr', $string);
    $string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', '_ets_cfu_kses_decode_entities_chr_hexdec', $string);
    return $string;
}

function _ets_cfu_kses_decode_entities_chr_hexdec($match)
{
    return chr(hexdec($match[1]));
}

function _ets_cfu_kses_decode_entities_chr($match)
{
    return chr($match[1]);
}

function ets_cfu_untrailingslashit($string)
{
    return rtrim($string, '/\\');
}

function ets_cfu_esc_url_raw($url, $protocols = null)
{
    return ets_cfu_esc_url($url, $protocols, 'db');
}

function ets_cfu_add_query_arg()
{
    $args = func_get_args();
    if (is_array($args[0])) {
        if (count($args) < 2 || false === $args[1])
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[1];
    } else {
        if (count($args) < 3 || false === $args[2])
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[2];
    }

    if ($frag = strstr($uri, '#'))
        $uri = Tools::substr($uri, 0, -Tools::strlen($frag));
    else
        $frag = '';

    if (0 === stripos($uri, 'http://')) {
        $protocol = 'http://';
        $uri = Tools::substr($uri, 7);
    } elseif (0 === stripos($uri, 'https://')) {
        $protocol = 'https://';
        $uri = Tools::substr($uri, 8);
    } else {
        $protocol = '';
    }

    if (strpos($uri, '?') !== false) {
        list($base, $query) = explode('?', $uri, 2);
        $base .= '?';
    } elseif ($protocol || strpos($uri, '=') === false) {
        $base = $uri . '?';
        $query = '';
    } else {
        $base = '';
        $query = $uri;
    }
    $qs = array();
    ets_cfu_parse_str($query, $qs);
    $qs = ets_cfu_urlencode_deep($qs);
    if (is_array($args[0])) {
        foreach ($args[0] as $k => $v) {
            $qs[$k] = $v;
        }
    } else {
        $qs[$args[0]] = $args[1];
    }

    foreach ($qs as $k => $v) {
        if ($v === false)
            unset($qs[$k]);
    }

    $ret = ets_cfu__build_query($qs);
    $ret = trim($ret, '?');
    $ret = preg_replace('#=(&|$)#', '$1', $ret);
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim($ret, '?');
    return $ret;
}

function ets_cfu_urlencode_deep($value)
{
    return ets_cfu_map_deep($value, 'urlencode');
}

function ets_cfu_map_deep($value, $callback)
{
    if (is_array($value)) {
        foreach ($value as $index => $item) {
            $value[$index] = ets_cfu_map_deep($item, $callback);
        }
    } elseif (is_object($value)) {
        $object_vars = get_object_vars($value);
        foreach ($object_vars as $property_name => $property_value) {
            $value->$property_name = ets_cfu_map_deep($property_value, $callback);
        }
    } else {
        $value = call_user_func($callback, $value);
    }

    return $value;
}

function ets_cfu__build_query($data)
{
    return ets_cfu_http_build_query($data, null, '&', '', false);
}

function ets_cfu_http_build_query($data, $prefix = null, $sep = null, $key = '', $urlencode = true)
{
    $ret = array();

    foreach ((array)$data as $k => $v) {
        if ($urlencode)
            $k = urlencode($k);
        if (is_int($k) && $prefix != null)
            $k = $prefix . $k;
        if (!empty($key))
            $k = $key . '%5B' . $k . '%5D';
        if ($v === null)
            continue;
        elseif ($v === false)
            $v = '0';

        if (is_array($v) || is_object($v))
            array_push($ret, ets_cfu_http_build_query($v, '', $sep, $k, $urlencode));
        elseif ($urlencode)
            array_push($ret, $k . '=' . urlencode($v));
        else
            array_push($ret, $k . '=' . $v);
    }

    if (null === $sep)
        $sep = ini_get('arg_separator.output');

    return implode($sep, $ret);
}

function _ets_cfu_deep_replace($search, $subject)
{
    $subject = (string)$subject;

    $count = 1;
    while ($count) {
        $subject = str_replace($search, '', $subject, $count);
    }

    return $subject;
}

function ets_cfu_current_time($type, $gmt = 0)
{
    switch ($type) {
        case 'mysql':
            return ($gmt) ? gmdate('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s', time());
        case 'timestamp':
            return ($gmt) ? time() : time();
        default:
            return ($gmt) ? date($type) : date($type, time());
    }
}

function ets_cfu_strip_all_tags($string, $remove_breaks = false)
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    $string = strip_tags($string);

    if ($remove_breaks)
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);

    return trim($string);
}

function ets_cfu_mail_replace_tags($content, $args = '', $body = false)
{
    if ($args) {
        $args = array(
            'html' => false,
            'exclude_blank' => false,
        );
    }

    if (is_array($content)) {
        foreach ($content as $key => $value) {
            $content[$key] = ets_cfu_mail_replace_tags($value, $args);
        }

        return $content;
    }
    $content = explode("\n", $content);
    foreach ($content as $num => $line) {
        $line = new ETS_CFU_MailTaggedText($line, $args);
        $replaced = $line->replace_tags();
        if ($args['exclude_blank']) {
            $replaced_tags = $line->get_replaced_tags();

            if (empty($replaced_tags) || array_filter($replaced_tags)) {
                $content[$num] = $replaced;
            } else {
                unset($content[$num]);
            }
        } else {
            $content[$num] = $replaced;
        }
    }

    $content = implode("\n", $content);
    unset($body);
    return $content;
}

function ets_cfu_unslash($value)
{
    return ets_cfu_stripslashes_deep($value);
}

function ets_cfu_stripslashes_deep($value)
{
    return ets_cfu_map_deep($value, 'ets_cfu_stripslashes_from_strings_only');
}

function ets_cfu_stripslashes_from_strings_only($value)
{
    return is_string($value) ? Tools::stripslashes($value) : $value;
}

function ets_cfu_is_valid_locale($locale)
{
    $pattern = '/^[a-z]{2,3}(?:_[a-zA-Z_]{2,})?$/';
    return (bool)preg_match($pattern, $locale);
}

function ets_cfu_path_join($base, $path)
{
    if (ets_cfu_path_is_absolute($path))
        return $path;

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function ets_cfu_mkdir_p($target)
{
    $wrapper = null;

    if (ets_cfu_is_stream($target)) {
        list($wrapper, $target) = explode('://', $target, 2);
    }

    $target = str_replace('//', '/', $target);

    if ($wrapper !== null) {
        $target = $wrapper . '://' . $target;
    }

    $target = rtrim($target, '/');
    if (empty($target))
        $target = '/';

    if (file_exists($target))
        return @is_dir($target);

    $target_parent = dirname($target);
    while ('.' != $target_parent && !is_dir($target_parent)) {
        $target_parent = dirname($target_parent);
    }

    if ($stat = @stat($target_parent)) {
        $dir_perms = $stat['mode'] & 0007777;
    } else {
        $dir_perms = 0777;
    }

    if (@mkdir($target, $dir_perms, true)) {

        if ($dir_perms != ($dir_perms & ~umask())) {
            $folder_parts = explode('/', Tools::substr($target, Tools::strlen($target_parent) + 1));
            for ($i = 1, $c = count($folder_parts); $i <= $c; $i++) {
                @chmod($target_parent . '/' . implode('/', array_slice($folder_parts, 0, $i)), $dir_perms);
            }
        }

        return true;
    }

    return false;
}

function ets_cfu_is_stream($path)
{
    $wrappers = stream_get_wrappers();
    $wrappers_re = '(' . join('|', $wrappers) . ')';

    return preg_match("!^$wrappers_re://!", $path) === 1;
}

function ets_cfu_path_is_absolute($path)
{

    if (realpath($path) == $path)
        return true;

    if (Tools::strlen($path) == 0 || $path[0] == '.')
        return false;

    if (preg_match('#^[a-zA-Z]:\\\\#', $path))
        return true;

    return ($path[0] == '/' || $path[0] == '\\');
}

function ets_cfu_zeroise($number, $threshold)
{
    return sprintf('%0' . $threshold . 's', $number);
}

function ets_cfu_unique_filename($dir, $filename, $unique_filename_callback = null)
{
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $name = pathinfo($filename, PATHINFO_BASENAME);
    if ($ext) {
        $ext = '.' . $ext;
    }

    if ($name === $ext) {
        $name = '';
    }

    if ($unique_filename_callback && is_callable($unique_filename_callback)) {
        $filename = call_user_func($unique_filename_callback, $dir, $name, $ext);
    } else {
        $number = '';

        if ($ext && Tools::strtolower($ext) != $ext) {
            $ext2 = Tools::strtolower($ext);
            $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);

            while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
                $new_number = (int)$number + 1;
                $filename = str_replace(array("-$number$ext", "$number$ext"), "-$new_number$ext", $filename);
                $filename2 = str_replace(array("-$number$ext2", "$number$ext2"), "-$new_number$ext2", $filename2);
                $number = $new_number;
            }
            return $filename2;
        }

        while (file_exists($dir . "/$filename")) {
            $new_number = (int)$number + 1;
            if ('' == "$number$ext") {
                $filename = "$filename-" . $new_number;
            } else {
                $filename = str_replace(array("-$number$ext", "$number$ext"), "-" . $new_number . $ext, $filename);
            }
            $number = $new_number;
        }
    }

    return $filename;
}

function ets_cfu_recaptcha_noscript($args = '')
{
    $args = ets_cfu_parse_args($args, array(
        'sitekey' => '',
    ));

    if (empty($args['sitekey'])) {
        return;
    }

}

function ets_cfu_recaptcha_check_with_google($spam)
{

    $contact_form = ets_cfu_get_current_contact_form();

    if (!$contact_form) {
        return $spam;
    }

    $tags = $contact_form->scan_form_tags(array('type' => 'recaptcha'));

    if (empty($tags)) {
        return $spam;
    }

    $recaptcha = ETS_CFU_Recaptcha::get_instance();

    if (!$recaptcha->is_active()) {
        return $spam;
    }

    $response_token = ets_cfu_recaptcha_response();
    $spam = !$recaptcha->verify($response_token);

    return $spam;
}

function ets_cfu_recaptcha_response()
{
    if (Tools::isSubmit('g-recaptcha-response')) {
        return Tools::getValue('g-recaptcha-response');
    }
    return false;
}

function ets_cfu_wpautop($pee, $br = true)
{
    $pre_tags = array();

    if (trim($pee) === '')
        return '';

    $pee = $pee . "\n";

    if (strpos($pee, '<pre') !== false) {
        $pee_parts = explode('</pre>', $pee);
        $last_pee = array_pop($pee_parts);
        $pee = '';
        $i = 0;

        foreach ($pee_parts as $pee_part) {
            $start = strpos($pee_part, '<pre');

            if ($start === false) {
                $pee .= $pee_part;
                continue;
            }

            $name = displayText('', 'pre', ['wp-pre-tag-' . $i => null]);
            $pre_tags[$name] = Tools::substr($pee_part, $start) . '</pre>';

            $pee .= Tools::substr($pee_part, 0, $start) . $name;
            $i++;
        }

        $pee .= $last_pee;
    }
    $pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);

    $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

    $pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee);

    $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

    $pee = str_replace(array("\r\n", "\r"), "\n", $pee);

    $pee = ets_cfu_replace_in_html_tags($pee, array("\n" => " <!-- wpnl --> "));

    if (strpos($pee, '<option') !== false) {
        $pee = preg_replace('|\s*<option|', '<option', $pee);
        $pee = preg_replace('|</option>\s*|', '</option>', $pee);
    }

    if (strpos($pee, '</object>') !== false) {
        $pee = preg_replace('|(<object[^>]*>)\s*|', '$1', $pee);
        $pee = preg_replace('|\s*</object>|', '</object>', $pee);
        $pee = preg_replace('%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee);
    }

    if (strpos($pee, '<source') !== false || strpos($pee, '<track') !== false) {
        $pee = preg_replace('%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee);
        $pee = preg_replace('%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee);
        $pee = preg_replace('%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee);
    }

    if (strpos($pee, '<figcaption') !== false) {
        $pee = preg_replace('|\s*(<figcaption[^>]*>)|', '$1', $pee);
        $pee = preg_replace('|</figcaption>\s*|', '</figcaption>', $pee);
    }

    $pee = preg_replace("/\n\n+/", "\n\n", $pee);

    $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);

    $pee = '';

    foreach ($pees as $tinkle) {
        $pee .= displayText(trim($tinkle, "\n"), 'p') . "\n";
    }

    $pee = preg_replace('|<p>\s*</p>|', '', $pee);

    $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);

    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);

    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);

    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);

    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

    if ($br) {
        $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_ets_cfu_autop_newline_preservation_helper', $pee);

        $pee = str_replace(array('<br>', '<br/>'), '<br />', $pee);

        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);

        $pee = str_replace(displayText('', 'WPPreserveNewline', [], true), "\n", $pee);
    }

    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);

    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
    $pee = preg_replace("|\n</p>$|", '</p>', $pee);

    if (!empty($pre_tags))
        $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

    if (false !== strpos($pee, '<!-- wpnl -->')) {
        $pee = str_replace(array(' <!-- wpnl --> ', '<!-- wpnl -->'), "\n", $pee);
    }

    return $pee;
}

function ets_cfu_replace_in_html_tags($haystack, $replace_pairs)
{
    $textarr = ets_cfu_html_split($haystack);
    $changed = false;

    if (1 === count($replace_pairs)) {
        foreach ($replace_pairs as $needle => $replace) ;

        for ($i = 1, $c = count($textarr); $i < $c; $i += 2) {
            if (false !== strpos($textarr[$i], $needle)) {
                $textarr[$i] = str_replace($needle, $replace, $textarr[$i]);
                $changed = true;
            }
        }
    } else {
        $needles = array_keys($replace_pairs);

        for ($i = 1, $c = count($textarr); $i < $c; $i += 2) {
            foreach ($needles as $needle) {
                if (false !== strpos($textarr[$i], $needle)) {
                    $textarr[$i] = strtr($textarr[$i], $replace_pairs);
                    $changed = true;
                    break;
                }
            }
        }
    }

    if ($changed) {
        $haystack = implode($textarr);
    }

    return $haystack;
}

function ets_cfu_html_split($input)
{
    return preg_split(ets_cfu_get_html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE);
}

function ets_cfu_get_html_split_regex()
{
    static $regex;

    if (!isset($regex)) {
        $comments =
            '!'
            . '(?:'
            . '-(?!->)'
            . '[^\-]*+'
            . ')*+'
            . '(?:-->)?';

        $cdata =
            '!\[CDATA\['
            . '[^\]]*+'
            . '(?:'
            . '](?!]>)'
            . '[^\]]*+'
            . ')*+'
            . '(?:]]>)?';

        $escaped =
            '(?='
            . '!--'
            . '|'
            . '!\[CDATA\['
            . ')'
            . '(?(?=!-)'
            . $comments
            . '|'
            . $cdata
            . ')';

        $regex =
            '/('
            . '<'
            . '(?'
            . $escaped
            . '|'
            . '[^>]*>?'
            . ')'
            . ')/';
    }
    return $regex;
}

function _ets_cfu_autop_newline_preservation_helper($matches)
{
    return str_replace("\n", displayText('', 'WPPreserveNewline', [], true), $matches[0]);
}

function ets_cfu_hash($data, $scheme = 'auth')
{
    $salt = _COOKIE_KEY_;
    unset($scheme);
    return $data ? md5($salt . $data) : '';
}

function ets_cfu_mysql2date($format, $date, $translate = true)
{
    if (empty($date))
        return false;
    unset($translate);
    if ('G' == $format)
        return strtotime($date . ' +0000');

    $i = strtotime($date);

    if ('U' == $format)
        return $i;
    return date($format, $i);
}

function ets_cfu_acceptance_mail_tag($replaced, $submitted, $html, $mail_tag)
{
    $form_tag = $mail_tag->corresponding_form_tag();

    if (!$form_tag) {
        return $replaced;
    }

    if (!empty($submitted)) {
        $replaced = 'Consented';
    } else {
        $replaced = 'Not consented';
    }

    $content = empty($form_tag->content)
        ? (string)reset($form_tag->values)
        : $form_tag->content;

    if (!$html) {
        $content = ets_cfu_strip_all_tags($content);
    }

    $content = trim($content);


    return $content;
}

function getOrderReferrence()
{
    $orders = [];
    $customer_thread = array();
    if (!isset($customer_thread['id_order'])) {
        $customer_orders = Order::getCustomerOrders(Context::getContext()->customer->id);
        if ($customer_orders)
            foreach ($customer_orders as $customer_order) {
                $myOrder = new Order((int)$customer_order['id_order']);

                if (Validate::isLoadedObject($myOrder)) {
                    $orders[$customer_order['id_order']] = $customer_order;
                    $orders[$customer_order['id_order']]['products'] = $myOrder->getProducts();
                }
            }
    } elseif ((int)$customer_thread['id_order'] > 0) {
        $myOrder = new Order($customer_thread['id_order']);

        if (Validate::isLoadedObject($myOrder)) {
            $orders[$myOrder->id] = Context::getContext()->controller->objectPresenter->present($myOrder);
            $orders[$myOrder->id]['id_order'] = $myOrder->id;
            $orders[$myOrder->id]['products'] = $myOrder->getProducts();
        }
    }

    if (isset($customer_thread['id_product']) && $customer_thread['id_product']) {
        $id_order = isset($customer_thread['id_order']) ?
            (int)$customer_thread['id_order'] :
            0;

        $orders[$id_order]['products'][(int)$customer_thread['id_product']] = Context::getContext()->controller->objectPresenter->present(
            new Product((int)$customer_thread['id_product'])
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

function displayText($content, $tag, $attr_datas = array(), $short_tag = false)
{
    $text = '<' . $tag . ' ';
    if ($attr_datas) {
        foreach ($attr_datas as $key => $value) {
            if ($value == null)
                $text .= $key;
            else
                $text .= $key . '="' . $value . '" ';
        }
    }
    if ($short_tag || $tag == 'img' || $tag == 'br' || $tag == 'path' || $tag == 'input') {
        $text .= ' /' . '>';
    } else {
        $text .= '>';
    }

    if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
        $text .= $content;
    if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
        $text .= '<' . '/' . $tag . '>';
    return $text;
}