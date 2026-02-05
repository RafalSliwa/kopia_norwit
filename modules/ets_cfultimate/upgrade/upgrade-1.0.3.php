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

function upgrade_module_1_0_3()
{
    $res = ct_check_colum('ets_cfu_contact', 'group_access', 'varchar(255) CHARACTER SET utf8 NOT NULL AFTER `hook`');
    $res &= ct_check_colum('ets_cfu_contact', 'only_customer', 'INT(1) NOT NULL AFTER `group_access`');

    if ($res) {
        $group = Group::getGroups(Context::getContext()->language->id, true);
        $total_group = count($group);
        $group_temp = array();
        for ($i = 0; $i < $total_group; $i++) {
            if (isset($group[$i]['id_group']) && $group[$i]['id_group'] > 0)
                $group_temp[] = $group[$i]['id_group'];
        }
        $res &= DB::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_cfu_contact` SET group_access=\'' . implode(',', array_map('intval', $group_temp)) . '\'');
    }
    return $res;
}

if (!function_exists('ct_check_colum')) {
    function ct_check_colum($table, $column, $suffix)
    {
        return Db::getInstance()->execute('
            SET @dbname = DATABASE();
            SET @tablename = "' . _DB_PREFIX_ . pSQL($table) . '";
            SET @columnname = "' . pSQL($column) . '";
            SET @suffix = "' . pSQL($suffix) . '";
            SET @preparedStatement = (SELECT IF(
            (
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                  (table_name = @tablename)
                  AND (table_schema = @dbname)
                  AND (column_name = @columnname)
                ) > 0,
                "SELECT 1",
                CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname," ", @suffix)
            ));
            PREPARE alterIfNotExists FROM @preparedStatement;
            EXECUTE alterIfNotExists;
            DEALLOCATE PREPARE alterIfNotExists;
        ');
    }
}
