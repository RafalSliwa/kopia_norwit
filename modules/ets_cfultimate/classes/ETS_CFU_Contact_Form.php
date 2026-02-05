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

class ETS_CFU_Contact_Form extends ETS_CFU_Translate
{
    const post_type = 'ets_cfu_contact_form';
    public static $found_items = 0;
    public static $current = null;
    public $id;
    public $name;
    public $title;
    public $locale;
    public $properties = array();
    public $unit_tag;
    public $responses_count = 0;
    public $scanned_form_tags;
    public $shortcode_atts = array();
    public $save_message;
    public $open_form_by_button;
    public $button_popup_enabled;
    public $id_product;
    public $user_agent;
    public $container_post_id;
    public $condition;
    public $active;
    public $mailchimp_enabled;

    private function __construct($contact = null)
    {
        if (is_numeric($contact))
            $contact = new ETS_CFU_Contact($contact, Context::getContext()->language->id);
        if ($contact->id) {
            $this->id = $contact->id;
            $this->name = $contact->title;
            $this->title = $contact->title;
            $this->locale = '';
            $this->save_message = $contact->save_message;
            $this->open_form_by_button = $contact->open_form_by_button;
            $this->button_popup_enabled = $contact->button_popup_enabled;
            $this->button_label = $contact->button_label ? $contact->button_label : 'Contact Form Ultimate';
            $this->title_alias = $contact->title_alias;
            $this->meta_title = $contact->meta_title;
            $this->meta_keyword = $contact->meta_keyword;
            $this->meta_description = $contact->meta_description;
            $this->save_attachments = $contact->save_attachments;
            $this->star_message = $contact->star_message;

            $this->thank_you_active = $contact->thank_you_active;
            $this->thank_you_page = $contact->thank_you_page;
            $this->thank_you_alias = $contact->thank_you_alias;
            $this->thank_you_page_title = $contact->thank_you_page_title;
            $this->thank_you_message = $contact->thank_you_message;
            $dataProvider = ETS_CFU_Data_Provider::getInstance();
            $properties = array(
                'form' => $contact->short_code,
                'mail' => array(
                    'active' => 1,
                    'subject' => $contact->subject,
                    'sender' => $contact->email_from,
                    'bcc' => $contact->bcc,
                    'recipient' => $contact->email_to,
                    'body' => $contact->message_body,
                    'additional_headers' => $contact->additional_headers,
                    'attachments' => $contact->file_attachments,
                    'use_html' => true,
                    'exclude_blank' => true,
                ),
                'mail_2' => array(
                    'active' => $contact->use_email2,
                    'subject' => $contact->subject2,
                    'sender' => $contact->email_from2,
                    'bcc' => $contact->bcc2,
                    'recipient' => $contact->email_to2,
                    'body' => $contact->message_body2,
                    'additional_headers' => $contact->additional_headers2,
                    'attachments' => $contact->file_attachments2,
                    'use_html' => true,
                    'exclude_blank' => true,
                ),
                'messages' => array(
                    'mail_sent_ok' => $contact->message_mail_sent_ok ?: 'Thank you for your message. It has been sent.',
                    'mail_sent_ng' => $contact->message_mail_sent_ng ?: 'There was an error while trying to send your message. Please try again later.',
                    'validation_error' => $contact->message_validation_error ?: '',
                    'spam' => $contact->message_spam ?: 'One or more fields have an error. Please check and try again.',
                    'accept_terms' => $contact->message_accept_terms ?: 'You must accept the terms and conditions before sending your message.',
                    'invalid_required' => $contact->message_invalid_required ?: 'The field is required.',
                    'invalid_too_long' => $contact->message_invalid_too_long ?: 'The field is too long.',
                    'invalid_too_short' => $contact->message_invalid_too_short ?: 'The field is too short.',
                    'invalid_date' => $contact->message_invalid_date ?: 'The date format is incorrect.',
                    'date_too_early' => $contact->message_date_too_early ?: 'The date is before the earliest one allowed.',
                    'date_too_late' => $contact->message_date_too_late ?: 'The date is after the latest one allowed.',
                    'upload_failed' => $contact->message_upload_failed ?: 'There was an unknown error while uploading the file.',
                    'upload_file_type_invalid' => $contact->message_upload_file_type_invalid ?: 'You are not allowed to upload files of this type.',
                    'upload_file_too_large' => $contact->message_upload_file_too_large ?: 'The file is too big.',
                    'upload_failed_php_error' => $contact->message_upload_failed_php_error ?: 'There was an error while uploading the file.',
                    'invalid_number' => $contact->message_invalid_number ?: 'The number format is invalid.',
                    'number_too_small' => $contact->message_number_too_small ?: 'The number is smaller than the minimum allowed.',
                    'number_too_large' => $contact->message_number_too_large ?: 'The number is larger than the maximum allowed',
                    'quiz_answer_not_correct' => $contact->message_quiz_answer_not_correct ?: 'The answer to the quiz is incorrect.',
                    'captcha_not_match' => $contact->message_captcha_not_match ?: 'Your entered code is incorrect.',
                    'ip_black_list' => $contact->message_ip_black_list ?: 'You are not allowed to submit this form. Please contact webmaster for more information.',
                    'email_black_list' => $contact->message_email_black_list ?: 'Your email is blocked. Contact webmaster for more info.',
                    'invalid_email' => $contact->message_invalid_email ?: 'The e-mail address entered is invalid.',
                    'invalid_url' => $contact->message_invalid_url ?: 'The URL is invalid.',
                    'invalid_tel' => $contact->message_invalid_tel ?: 'The telephone number is invalid.',
                    'thank_you_mes' => $contact->thank_you_message ?: 'Thank you for contacting us. This message is to confirm that you have successfully submitted the contact form. We\'ll get back to you shortly.',
                    'thank_you_url' => $contact->thank_you_url ?: 'Customer will be redirected to this URL after submitting the form successfully',
                    'filter_spam_content' => $this->l('Your message was detected as spam. Please contact the webmaster for more information.', 'ETS_CFU_Contact_Form'),
                    'filter_spam_email' => $this->l('Your email was detected as spam. Please contact the webmaster for more information.', 'ETS_CFU_Contact_Form'),
                ),
                'additional_settings' => $contact->additional_settings,
                'button' => array(
                    'label' => $contact->button_label ?: 'Open contact form',
                    'background_color' => $contact->button_background_color,
                    'hover_color' => $contact->button_hover_color,
                    'text_color' => $contact->button_text_color,
                    'background_hover_color' => $contact->button_background_hover_color,
                    'icon_enabled' => $contact->button_icon_enabled,
                    'icon_custom' => $dataProvider->getIcons($contact->button_icon_custom),
                    'icon_custom_file' => $dataProvider->getMediaLink($contact->button_icon_custom_file),
                ),
                'floating' => array(
                    'label' => $contact->floating_label ?: 'Open contact form',
                    'background_color' => $contact->floating_background_color,
                    'hover_color' => $contact->floating_hover_color,
                    'text_color' => $contact->floating_text_color,
                    'background_hover_color' => $contact->floating_background_hover_color,
                    'icon_enabled' => $contact->floating_icon_enabled,
                    'icon_custom' => $dataProvider->getIcons($contact->floating_icon_custom),
                    'icon_custom_file' => $dataProvider->getMediaLink($contact->floating_icon_custom_file),
                    'popup_position' => $contact->button_popup_position,
                    'popup_left' => $contact->button_popup_left,
                    'popup_right' => $contact->button_popup_right,
                    'popup_top' => $contact->button_popup_top,
                    'popup_bottom' => $contact->button_popup_bottom,
                )
            );
            $this->properties = $properties;
            $this->active = $contact->active;
            $this->condition = $contact->condition;
            $this->mailchimp_enabled = $contact->mailchimp_enabled;
        }
    }

    public static function count()
    {
        return self::$found_items;
    }

    public static function get_current()
    {
        return self::$current;
    }

    public static function get_instance($id_contact)
    {
        $contact = new ETS_CFU_Contact($id_contact, Context::getContext()->language->id);
        if (!$contact->id) {
            return false;
        }
        return self::$current = new ETS_CFU_Contact_Form($contact);
    }

    public static function get_unit_tag($id = 0)
    {
        static $global_count = 0;
        $global_count += 1;
        $unit_tag = sprintf('wpcfu-f%1$d-o%2$d',
            ets_cfu_absint($id), $global_count);
        return $unit_tag;
    }

    public function initial()
    {
        return empty($this->id);
    }

    public function name()
    {
        return $this->name;
    }

    public function title()
    {
        return $this->title;
    }

    public function locale()
    {
        if (ets_cfu_is_valid_locale($this->locale)) {
            return $this->locale;
        } else {
            return '';
        }
    }

    public function set_locale($locale)
    {
        $locale = trim($locale);

        if (ets_cfu_is_valid_locale($locale)) {
            $this->locale = $locale;
        } else {
            $this->locale = 'en_US';
        }
    }

    public function shortcode_attr($name)
    {
        if (isset($this->shortcode_atts[$name])) {
            return (string)$this->shortcode_atts[$name];
        }
    }

    public function is_posted()
    {
        return true;
    }

    public function scan_form_tags($cond = null)
    {
        $manager = ETS_CFU_Form_Tag_Manager::get_instance();

        $manager->set_instance();
        if (empty($this->scanned_form_tags)) {
            $this->scanned_form_tags = $manager->scan($this->prop('form'));
        }
        $tags = $this->scanned_form_tags;

        return $manager->filter($tags, $cond);
    }

    public function prop($name)
    {
        $props = $this->get_properties();
        return isset($props[$name]) ? $props[$name] : null;
    }

    public function get_properties()
    {
        $properties = (array)$this->properties;
        return $properties;
    }

    public function form_elements()
    {
        return $this->replace_all_form_tags();
    }

    public function replace_all_form_tags()
    {
        $manager = ETS_CFU_Form_Tag_Manager::get_instance();
        $manager->set_instance();
        $form = $this->prop('form');

        if (ets_cfu_autop_or_not()) {
            $form = $manager->normalize($form);
            $form = ets_cfu_autop($form);
        }

        $form = $manager->replace_all($form);
        $this->scanned_form_tags = $manager->get_scanned_tags();
        return $form;
    }

    public function submit($args = '')
    {
        $args = ets_cfu_parse_args($args, array(
            'skip_mail' => false,
        ));
        $submission = ETS_CFU_Submission::get_instance($this, array(
            'skip_mail' => $args['skip_mail'],
        ));

        $result = array(
            'contact_form_id' => $this->id(),
            'status' => $submission->get_status(),
            'message' => $submission->get_response(),
            'demo_mode' => false,
        );

        if ($submission->is('validation_failed')) {
            $result['invalid_fields'] = $submission->get_invalid_fields();
        }

        return $result;
    }

    public function id()
    {
        return $this->id;
    }

    public function message($status, $filter = true)
    {
        $messages = $this->prop('messages');
        $message = isset($messages[$status]) ? $messages[$status] : '';
        if ($filter) {
            $message = $this->filter_message($message, $status);
        }
        return $message;
    }

    public function filter_message($message, $status = '')
    {
        $message = ets_cfu_strip_all_tags($message);
        $message = ets_cfu_mail_replace_tags($message, array('html' => true));
        unset($status);
        return $message;
    }
}
