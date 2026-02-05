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

Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_contact` (
          `id_contact` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `email_to` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `bcc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `email_from` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `exclude_lines` int(11) NOT NULL,
          `use_html_content` int(11) NOT NULL,
          `use_email2` int(11) NOT NULL,
          `email_to2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `bcc2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `email_from2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `additional_headers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `additional_headers2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `exclude_lines2` int(11) NOT NULL,
          `use_html_content2` int(11) NOT NULL,
          `id_employee` int(1) NOT NULL,
          `save_message` int(1) NOT NULL,
          `save_attachments` INT(1) NOT NULL,
          `star_message` INT(1) NOT NULL,
          `open_form_by_button` INT (1),
          `file_attachments2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `file_attachments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `render_form` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
           `condition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
          `button_popup_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
          `button_popup_position` VARCHAR(64) NOT NULL,
          `button_popup_left` INT(11) NOT NULL,
          `button_popup_right` INT(11) NOT NULL,
          `button_popup_top` INT(11) NOT NULL,
          `button_popup_bottom` INT(11) NOT NULL,
          `floating_text_color` VARCHAR(16) NOT NULL,
          `floating_background_color` VARCHAR(16) NOT NULL,
          `floating_hover_color` VARCHAR(16) NOT NULL,
          `floating_background_hover_color` VARCHAR(16) NOT NULL,
          `floating_icon_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
          `floating_icon_custom` VARCHAR(255) NOT NULL,
          `floating_icon_custom_file` VARCHAR(255) NOT NULL,
          `button_text_color` VARCHAR(16) NOT NULL,
          `button_background_color` VARCHAR(16) NOT NULL,
          `button_hover_color` VARCHAR(16) NOT NULL,
          `button_background_hover_color` VARCHAR(16) NOT NULL,
          `button_icon_enabled` TINYINT(1) UNSIGNED DEFAULT 0,
          `button_icon_custom` VARCHAR(255) NOT NULL,
          `button_icon_custom_file` VARCHAR(255) NOT NULL,
          `hook` VARCHAR(222),
          `group_access` VARCHAR(255),
          `thank_you_active` INT(1) NOT NULL,
          `thank_you_page` VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL,
          `active` INT(1),
          `enable_form_page` INT(1),
          `position` INT(11),
          `mailchimp_enabled` TINYINT(1) UNSIGNED DEFAULT 0,
          `mailchimp_api_key` VARCHAR(255) NOT NULL,
          `mailchimp_audience` VARCHAR(128) NOT NULL,
          `mailchimp_mapping_data` text DEFAULT NULL,
          `date_add` date NOT NULL,
          `date_upd` date NOT NULL,
          PRIMARY KEY (`id_contact`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_contact_lang` (
          `id_contact` int(11) NOT NULL,
          `id_lang` int(11) NOT NULL,
          `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `title_alias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `meta_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `meta_keyword` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `button_label` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `floating_label` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `short_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `template_mail` text NOT NULL,
          `subject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	,
          `subject2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_body2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	,
          `message_mail_sent_ok` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_mail_sent_ng` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_validation_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_spam` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_accept_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_required` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_too_long` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_too_short` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_date_too_early` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL, 
          `message_invalid_date` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_date_too_late` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_upload_failed` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_upload_file_type_invalid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_upload_file_too_large` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_quiz_answer_not_correct` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_tel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `additional_settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_upload_failed_php_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_invalid_number` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_number_too_small` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_number_too_large` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_captcha_not_match` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_ip_black_list` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `message_email_black_list` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `thank_you_page_title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
          `thank_you_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `thank_you_alias` VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL,
          `thank_you_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL
        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_contact_shop` (
      `id_contact` int(11) NOT NULL,
      `id_shop` int(11) NOT NULL
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_contact_message`(
          `id_contact_message` int(11) unsigned NOT NULL AUTO_INCREMENT ,
          `id_contact` int(11) NOT NULL,
          `id_customer` INT (11) NOT NULL,
          `id_product` INT (11) NOT NULL DEFAULT 0,
          `ip` VARCHAR(255) NOT NULL,
          `replied` INT(1) NOT NULL,
          `readed` INT(1) NOT NULL,
          `special` INT(1) NOT NULL,
          `subject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `sender` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `recipient` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `attachments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `reply_to` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
          `date_add` DATETIME NOT NULL,
          `date_upd` DATETIME NOT NULL,
          PRIMARY KEY (`id_contact_message`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` (
      `id_contact_message` int(11) NOT NULL,
      `id_shop` int(11) NOT NULL
    )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_message_reply`(
    `id_ets_cfu_message_reply` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
    `id_contact_message` INT(11) NOT NULL,
    `id_employee` INT(11) NOT NULL,
    `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
    `reply_to` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
    `subject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci	 NOT NULL,
    `attachment` varchar(500) NOT NULL,
    `date_add` date NOT NULL,
    `date_upd` date NOT NULL,
    PRIMARY KEY (`id_ets_cfu_message_reply`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_cfu_log`(
    `id_ets_cfu_log` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `ip` varchar(50) DEFAULT NULL,
    `id_contact` INT(11) NOT NULL,
    `browser` varchar(70) DEFAULT NULL,
    `id_customer` INT (11) DEFAULT NULL,
    `id_guest` INT (11) DEFAULT NULL,
    `datetime_added` datetime NOT NULL,
    PRIMARY KEY (`id_ets_cfu_log`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
return true;