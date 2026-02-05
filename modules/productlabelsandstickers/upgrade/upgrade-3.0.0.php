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

function upgrade_module_3_0_0($module)
{
    if (!columnExist('stickerbanner_id')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers_rules` ADD `stickerbanner_id` varchar(255) default NULL AFTER `sticker_id`');
    }

    return true;
}

function columnExist($column_name)
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
