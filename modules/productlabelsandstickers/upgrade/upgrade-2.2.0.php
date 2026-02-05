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

function upgrade_module_2_2_0($module)
{
    if (!columnExist('sticker_opacity')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `sticker_opacity` varchar(255) default NULL');
    }
    if (!columnExistSe('excluded_p')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers_rules` ADD `excluded_p` varchar(255) default NULL');
    }

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

function columnExistSe($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fmm_stickers_rules"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}
