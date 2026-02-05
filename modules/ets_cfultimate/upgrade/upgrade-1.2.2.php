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

function upgrade_module_1_2_2($object)
{
    if (!@is_dir(_PS_IMG_DIR_ . $object->name)) {
        @mkdir(_PS_IMG_DIR_ . $object->name, 0755, true);
    }
    if (@file_exists(_PS_IMG_DIR_ . 'index.php') && !@file_exists($object->name . DIRECTORY_SEPARATOR . 'index.php')) {
        @copy(_PS_IMG_DIR_ . 'index.php', _PS_IMG_DIR_ . $object->name . DIRECTORY_SEPARATOR . 'index.php');
    }
    ETS_CFU_Tools::recursiveCopy($object->getLocalPath() . 'views/img/upload', _PS_IMG_DIR_ . $object->name);

    if (!@is_dir(_PS_DOWNLOAD_DIR_ . $object->name)) {
        @mkdir(_PS_DOWNLOAD_DIR_ . $object->name, 0755, true);
    }
    if (@file_exists(_PS_DOWNLOAD_DIR_ . 'index.php') && !@file_exists(_PS_DOWNLOAD_DIR_ . $object->name . DIRECTORY_SEPARATOR . 'index.php')) {
        @copy(_PS_DOWNLOAD_DIR_ . 'index.php', _PS_DOWNLOAD_DIR_ . $object->name . DIRECTORY_SEPARATOR . 'index.php');
    }

    ETS_CFU_Tools::recursiveCopy($object->getLocalPath() . 'views/img/etscfu_upload', _PS_DOWNLOAD_DIR_ . $object->name);

    if ((int)Db::getInstance()->getValue('SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name`=\'AdminContactFormUltimateDownload\'') <= 0) {
        $addTab = [
            [
                'class_name' => 'AdminContactFormUltimateDownload',
                'tab_name' => 'File download',
                'icon' => 'icon icon-download',
                'active' => 0
            ]
        ];
        $parentId = (int)Db::getInstance()->getValue('SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name`=\'AdminContactFormUltimate\'');
        $object->addTabs($addTab, Language::getLanguages(false), $parentId);
    }

    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_lang` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_shop` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_message` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_message_reply` CONVERT TO CHARACTER SET utf8mb4');
    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_cfu_log` CONVERT TO CHARACTER SET utf8mb4');

    return true;
}