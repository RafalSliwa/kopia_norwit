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

function upgrade_module_1_1_2()
{
    return
        Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_message` 
            ADD `id_product` INT(11) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `id_customer`
        ')
        && Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` 
                ADD `button_popup_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `render_form`
                , ADD `button_popup_position` VARCHAR(64) NOT NULL AFTER `button_popup_enabled`
                , ADD `button_popup_left` INT(11) NOT NULL AFTER `button_popup_position`
                , ADD `button_popup_right` INT(11) NOT NULL AFTER `button_popup_left`
                , ADD `button_popup_top` INT(11) NOT NULL AFTER `button_popup_right`
                , ADD `button_popup_bottom` INT(11) NOT NULL AFTER `button_popup_top`
                , ADD `button_text_color` VARCHAR(16) NOT NULL AFTER `button_popup_bottom`
                , ADD `button_background_color` VARCHAR(16) NOT NULL AFTER `button_text_color`
                , ADD `button_hover_color` VARCHAR(16) NOT NULL AFTER `button_background_color`
                , ADD `button_background_hover_color` VARCHAR(16) NOT NULL AFTER `button_hover_color`
                , ADD `button_icon_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `button_hover_color`
                , ADD `button_icon_custom` VARCHAR(255) NOT NULL AFTER `button_icon_enabled`
                , ADD `button_icon_custom_file` VARCHAR(255) NOT NULL AFTER `button_icon_custom`;
        ')
        && Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact`
                ADD `floating_text_color` VARCHAR(16) NOT NULL AFTER `button_popup_bottom`
                , ADD `floating_background_color` VARCHAR(16) NOT NULL AFTER `floating_text_color`
                , ADD `floating_hover_color` VARCHAR(16) NOT NULL AFTER `floating_background_color`
                , ADD `floating_background_hover_color` VARCHAR(16) NOT NULL AFTER `floating_hover_color`
                , ADD `floating_icon_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `floating_background_hover_color`
                , ADD `floating_icon_custom` VARCHAR(255) NOT NULL AFTER `floating_icon_enabled`
                , ADD `floating_icon_custom_file` VARCHAR(255) NOT NULL AFTER `floating_icon_custom`
        ')
        && Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_lang`
                ADD `floating_label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `button_label`
        ');
}