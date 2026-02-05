<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2019
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_0($module)
{
    if (!columnExist('product')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `product` int(11) DEFAULT 0');
    }
    if (!columnExist('listing')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `listing` int(11) DEFAULT 0');
    }
    if (!columnExist('url')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `url` varchar(255) NOT NULL');
    }
    if (!columnExist('tip')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `tip` int(11) DEFAULT 0');
    }
    if (!columnExist('tip_color')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `tip_color` varchar(255) NOT NULL');
    }
    if (!columnExist('tip_bg')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `tip_bg` varchar(255) NOT NULL');
    }
    if (!columnExist('tip_pos')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `tip_pos` int(11) DEFAULT 0');
    }
    if (!columnExist('tip_width')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `tip_width` int(11) DEFAULT 180');
    }
    if (!columnExistLng('tip_txt')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers_lang` ADD `tip_txt` text');
    }
    $module->registerHook('header');

    return true;
}

function columnExist($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fmm_stickers"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}
function columnExistLng($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fmm_stickers_lang"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}
