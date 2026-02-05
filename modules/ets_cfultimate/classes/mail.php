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

class ETS_CFU_Mail
{
    private static $current = null;
    public $contact_form;
    private $name = '';
    private $locale = '';
    private $template = array();
    private $use_html = false;
    private $exclude_blank = false;

    private function __construct($name, $template)
    {
        $this->name = trim($name);
        $this->use_html = !empty($template['use_html']);
        $this->exclude_blank = !empty($template['exclude_blank']);
        $this->template = ets_cfu_parse_args($template, array(
            'subject' => '',
            'sender' => '',
            'bcc' => '',
            'body' => '',
            'recipient' => '',
            'additional_headers' => '',
            'attachments' => '',
        ));
        if ($submission = ETS_CFU_Submission::get_instance()) {
            $contact_form = $submission->get_contact_form();
            $this->contact_form = $contact_form;
            $this->locale = $contact_form->locale();
            $this->save_message = $contact_form->save_message;
        }
    }

    public static function get_current()
    {
        return self::$current;
    }

    public static function send($template, $name = '', $save = false)
    {
        self::$current = new self($name, $template);
        return self::$current->compose(true, $save);
    }

    private function compose($send = true, $save = false)
    {
        $context = Context::getContext();
        $components = array(
            'subject' => $this->get('subject', true),
            'sender' => $this->get('sender', true),
            'bcc' => $this->get('bcc', true),
            'body' => $this->get('body', true),
            'recipient' => $this->get('recipient', true),
            'additional_headers' => $this->get('additional_headers', true),
            'attachments' => $this->attachments(),
        );
        if (!$send) {
            return $components;
        }
        $subject = ets_cfu_strip_newline($components['subject']);
        $from = Ets_CfUltimate::getEmailToString($components['sender']);
        $nameFrom = trim(str_replace(array('<', '>', $from), '', $components['sender']));
        $body = $components['body'];
        $additional_headers = trim($components['additional_headers']);
        $replyTo = Ets_CfUltimate::getEmailToString($additional_headers);
        $replyToName = trim(str_replace(array('<', '>', $replyTo), '', $additional_headers));
        $attachments = $components['attachments'];

        $template_email = Configuration::get(($this->name != 'mail' ? 'ETS_CFU_EMAIL_TEMPLATE_CUSTOMER' : 'ETS_CFU_EMAIL_TEMPLATE_ADMIN'), $context->language->id);
        $id_product = $this->contact_form->id_product;
        if (trim($id_product) == '' || !Validate::isUnsignedInt($id_product)) {
            $id_product = 0;
        }
        if ($id_product > 0) {
            $product = new Product((int)$id_product, false, $context->language->id);
            $cover = Product::getCover($product->id, $context);
            $product_smarty = [
                'product' => [
                    'link' => $context->link->getProductLink($product),
                    'image' => isset($cover['id_image']) && $cover['id_image'] > 0 ? $context->link->getImageLink($product->link_rewrite, $cover['id_image'], ETS_CFU_Tools::getFormattedName('small')) : '',
                    'name' => $product->name
                ]
            ];
            $body .= ETS_CFU_Data_Provider::getInstance()->fetch('product.tpl', $product_smarty);
        }
        $template_vars = array(
            '{message_content}' => Configuration::get('ETS_CFU_ENABLE_TEMPLATE') ? str_replace(
                array('{message_content}', '%7Bshop_url%7D', '%7Bshop_logo%7D', '{shop_logo}'),
                array($body, '{shop_url}', '{shop_logo}', preg_replace('/<!--(.*)-->/Uis', '', $context->smarty->fetch(dirname(__FILE__) . '/../views/templates/hook/shop_logo.tpl'))),
                $template_email
            ) : $body,
        );

        $recipients = explode(',', $components['recipient']);
        $ok = false;
        if ($recipients) {
            $mails_to = array();
            $names_to = array();
            foreach ($recipients as $recipient) {
                $to = Ets_CfUltimate::getEmailToString($recipient);
                $nameTo = trim(str_replace(array('<', '>', $to), '', $recipient));
                if (Validate::isEmail($to) && !ets_cfu_is_blacklist_email($to)) {
                    $mails_to[] = $to;
                    $names_to[] = $nameTo ? $nameTo : '';
                } else
                    return -1;
            }
            if (!$subject || !Validate::isMailSubject($subject))
                return -2;
            elseif ($mails_to) {
                $mails_bcc = array();
                $bcc = explode(',', $components['bcc']);
                if ($bcc) {
                    foreach ($bcc as $bc) {
                        $email = Ets_CfUltimate::getEmailToString($bc);
                        if (Validate::isEmail($email))
                            $mails_bcc[] = $email;
                    }
                }
                if (Mail::Send(
                    Context::getContext()->language->id,
                    Configuration::get('ETS_CFU_ENABLE_TEMPLATE') ? 'contact_form_ultimate' : 'contact_form_7_plain',
                    $subject,
                    $template_vars,
                    $mails_to,
                    $names_to ? $names_to : null,
                    ($from ? $from : null),
                    $nameFrom ? $nameFrom : Configuration::get('PS_SHOP_NAME'),
                    $attachments,
                    null,
                    dirname(__FILE__) . '/../mails/',
                    false,
                    Context::getContext()->shop->id,
                    $mails_bcc,
                    $replyTo ? $replyTo : null,
                    $replyToName ? $replyToName : null
                )) {
	                $ok = true;
                } elseif(Mail::Send(
	                Context::getContext()->language->id,
	                Configuration::get('ETS_CFU_ENABLE_TEMPLATE') ? 'contact_form_ultimate' : 'contact_form_7_plain',
	                $subject,
	                $template_vars,
	                $mails_to,
	                $names_to ? $names_to : null,
	                null,
	                $nameFrom ? $nameFrom : Configuration::get('PS_SHOP_NAME'),
	                $attachments,
	                null,
	                dirname(__FILE__) . '/../mails/',
	                false,
	                Context::getContext()->shop->id,
	                $mails_bcc,
	                $replyTo ? $replyTo : null,
	                $replyToName ? $replyToName : null
                )) {
	                $ok = true;
                }
            }
        }
        if ($ok == true) {
            if ($this->contact_form->save_message && $save) {
                $file_uploads = $this->file_uploads();
                $files_list = '';
                foreach ($file_uploads as $file)
                    $files_list .= ',' . basename($file);
                $contact_message = new ETS_CFU_Contact_Message();
                $contact_message->id_contact = $this->contact_form->id;
                if (Context::getContext()->customer->logged)
                    $contact_message->id_customer = (int)Context::getContext()->customer->id;
                else
                    $contact_message->id_customer = 0;
                $contact_message->id_product = (int)$id_product;
                $contact_message->subject = $subject;
                $contact_message->recipient = $components['recipient'];
                $contact_message->sender = $components['sender'] ? $components['sender'] : (Configuration::get('PS_MAIL_METHOD') == 2 ? Configuration::get('PS_MAIL_USER') : Configuration::get('PS_SHOP_MAIL'));
                $contact_message->reply_to = $components['additional_headers'];
                $contact_message->body = $body;
                $contact_message->attachments = trim($files_list, ',');
                $contact_message->ip = Tools::getRemoteAddr();
                if ($this->contact_form->star_message)
                    $contact_message->special = 1;
                $contact_message->add();
                if (!$this->contact_form->save_attachments) {
                    $this->delete_file_uploads();
                }
            } elseif (!$this->contact_form->save_message && !$this->contact_form->save_attachments)
                $this->delete_file_uploads();
            return true;
        }
        return false;
    }

    public function get($component, $replace_tags = false)
    {
        $use_html = ($this->use_html && 'body' == $component);
        $exclude_blank = ($this->exclude_blank && 'body' == $component);

        $template = $this->template;
        $body = false;
        if ($component == 'body')
            $body = true;
        $component = isset($template[$component]) ? $template[$component] : '';

        if ($replace_tags) {
            $component = $this->replace_tags($component, array(
                'html' => $use_html,
                'exclude_blank' => $exclude_blank,
            ), $body);
        }
        if ($use_html)
            $component = ets_cfu_wpautop($component);
        return $component;
    }

    public function replace_tags($content, $args = '', $body = false)
    {
        return ets_cfu_mail_replace_tags($content, $args, $body);
    }

    private function attachments($template = null)
    {
        if (!$template) {
            $template = $this->get('attachments');
        }
        $attachments = array();
        if ($submission = ETS_CFU_Submission::get_instance()) {
            $uploaded_files = $submission->attachments();
            foreach ((array)$uploaded_files as $name => $path) {
                if (false !== strpos($template, "[{$name}]")
                    && !empty($path)) {
                    $attachments[] = $path;
                }
            }
        }
        return $attachments;
    }

    private function file_uploads($template = null)
    {
        if (!$template) {
            $template = $this->get('attachments');
        }
        $attachments = array();
        if ($submission = ETS_CFU_Submission::get_instance()) {
            $uploaded_files = $submission->uploaded_files();
            foreach ((array)$uploaded_files as $name => $path) {
                if (false !== strpos($template, "[{$name}]")
                    && !empty($path)) {
                    $attachments[] = $path;
                }
            }
        }
        return $attachments;
    }

    private function delete_file_uploads($template = null)
    {
        if (!$template) {
            $template = $this->get('attachments');
        }

        if ($submission = ETS_CFU_Submission::get_instance()) {
            $uploaded_files = $submission->uploaded_files();
            foreach ((array)$uploaded_files as $name => $path) {
                if (false !== strpos($template, "[{$name}]") && !empty($path) && file_exists($path)) {
                    @unlink($path);
                }
            }
        }
    }

    public static function deleteFileNotUse($template, $name = '')
    {
        self::$current = new self($name, $template);
        return self::$current->deleteFile();
    }

    private function deleteFile($template = null)
    {
        if (!$template) {
            $template = $this->get('attachments');
        }
        if ($submission = ETS_CFU_Submission::get_instance()) {
            $uploaded_files = $submission->uploaded_files();
            foreach ((array)$uploaded_files as $name => $path) {
                if (false === strpos($template, "[{$name}]") && file_exists($path)) {
                    @unlink($path);
                }
            }
        }
    }

    public function name()
    {
        return $this->name;
    }
}

function ets_cfu_phpmailer_init($phpmailer)
{
    $custom_headers = $phpmailer->getCustomHeaders();
    $phpmailer->clearCustomHeaders();
    $ets_cfu_content_type = false;

    foreach ((array)$custom_headers as $custom_header) {
        $name = $custom_header[0];
        $value = $custom_header[1];

        if ('X-WPCF7-Content-Type' === $name) {
            $ets_cfu_content_type = trim($value);
        } else {
            $phpmailer->addCustomHeader($name, $value);
        }
    }
    if ('text/html' === $ets_cfu_content_type) {
        $phpmailer->msgHTML($phpmailer->Body);
    } elseif ('text/plain' === $ets_cfu_content_type) {
        $phpmailer->AltBody = '';
    }
}

class ETS_CFU_MailTaggedText
{
    private $html = false;
    private $callback = null;
    private $content = '';
    private $replaced_tags = array();

    public function __construct($content, $args = '')
    {
        $args = ets_cfu_parse_args($args, array(
            'html' => false,
            'callback' => null,
        ));
        $this->html = (bool)$args['html'];
        if (null !== $args['callback'] && is_callable($args['callback'])) {
            $this->callback = $args['callback'];
        } elseif ($this->html) {
            $this->callback = array($this, 'replace_tags_callback_html');
        } else {
            $this->callback = array($this, 'replace_tags_callback');
        }
        $this->content = $content;
    }

    public function get_replaced_tags()
    {
        return $this->replaced_tags;
    }

    public function replace_tags()
    {
        $regex = '/(\[?)\[[\t ]*'
            . '([a-zA-Z_][0-9a-zA-Z:._-]*)'
            . '((?:[\t ]+"[^"]*"|[\t ]+\'[^\']*\')*)'
            . '[\t ]*\](\]?)/';
        return preg_replace_callback($regex, $this->callback, $this->content);
    }

    private function replace_tags_callback_html($matches)
    {
        return $this->replace_tags_callback($matches, true);
    }

    private function replace_tags_callback($matches, $html = false)
    {
        if ($matches[1] == '[' && $matches[4] == ']') {
            return Tools::substr($matches[0], 1, -1);
        }
        $tag = $matches[0];
        $tagname = $matches[2];
        $values = $matches[3];
        $mail_tag = new ETS_CFU_MailTag($tag, $tagname, $values);

        $field_name = $mail_tag->field_name();
        $submission = ETS_CFU_Submission::get_instance();
        $submitted = $submission
            ? $submission->get_posted_data($field_name)
            : null;
        if (null !== $submitted) {

            if ($mail_tag->get_option('do_not_heat')) {
                $submitted = Tools::isSubmit($field_name) ? Tools::getValue($field_name) : '';
            }
            $replaced = $submitted;
            if ($format = $mail_tag->get_option('format')) {
                $replaced = $this->format($replaced, $format);
            }
            $replaced = ets_cfu_flat_join($replaced);
            if ($html) {
                $replaced = ets_cfu_esc_html($replaced);
            }
            if ($form_tag = $mail_tag->corresponding_form_tag()) {
                $type = $form_tag->type;
                if ($type == 'acceptance' || $type == 'acceptance*')
                    $replaced = ets_cfu_acceptance_mail_tag($replaced, $submitted, $html, $mail_tag);
            }
            $replaced = ets_cfu_unslash(trim($replaced));
            $this->replaced_tags[$tag] = $replaced;
            return $replaced;
        }
        $special = null;
        if (null !== $special) {
            $this->replaced_tags[$tag] = $special;
            return $special;
        }
        return $tag;
    }

    public function format($original, $format)
    {
        $original = (array)$original;
        foreach ($original as $key => $value) {
            if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)) {
                $original[$key] = ets_cfu_mysql2date($format, $value);
            }
        }
        return $original;
    }
}

class ETS_CFU_MailTag
{
    private $tag;
    private $tagname = '';
    private $name = '';
    private $options = array();
    private $values = array();
    private $form_tag = null;

    public function __construct($tag, $tagname, $values)
    {
        $this->tag = $tag;
        $this->name = $this->tagname = $tagname;
        $this->options = array(
            'do_not_heat' => false,
            'format' => '',
        );
        if (!empty($values)) {
            preg_match_all('/"[^"]*"|\'[^\']*\'/', $values, $matches);
            $this->values = ets_cfu_strip_quote_deep($matches[0]);
        }
        if (preg_match('/^_raw_(.+)$/', $tagname, $matches)) {
            $this->name = trim($matches[1]);
            $this->options['do_not_heat'] = true;
        }
        if (preg_match('/^_format_(.+)$/', $tagname, $matches)) {
            $this->name = trim($matches[1]);
            $this->options['format'] = $this->values[0];
        }
    }

    public function tag_name()
    {
        return $this->tagname;
    }

    public function field_name()
    {
        return $this->name;
    }

    public function get_option($option)
    {
        return $this->options[$option];
    }

    public function values()
    {
        return $this->values;
    }

    public function corresponding_form_tag()
    {
        if ($this->form_tag instanceof ETS_CFU_Form_Tag) {
            return $this->form_tag;
        }
        if ($submission = ETS_CFU_Submission::get_instance()) {
            $contact_form = $submission->get_contact_form();
            $tags = $contact_form->scan_form_tags(array(
                'name' => $this->name,
                'feature' => '! zero-controls-container',
            ));
            if ($tags) {
                $this->form_tag = $tags[0];
            }
        }
        return $this->form_tag;
    }
}
