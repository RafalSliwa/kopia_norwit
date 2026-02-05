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
function upgrade_module_1_1_6($object)
{
	$tblLogRequest = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_trans_log_request` (
            `id_ets_trans_log_request` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `page_type` VARCHAR(20) DEFAULT NULL,
            `lang_source` VARCHAR(20) NOT NULL,
            `lang_target` VARCHAR(20) NOT NULL,
            `text_translated` TEXT DEFAULT NULL,
            `text_response` TEXT DEFAULT NULL,
            `status` TINYINT(1) DEFAULT NULL,
            `character_count` INT(10) UNSIGNED NOT NULL,
            `api_type` VARCHAR(20) NOT NULL,
            `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_upd` DATETIME DEFAULT NULL,
            `deleted_at` DATETIME DEFAULT NULL,
            `message` TEXT DEFAULT NULL,
            `id_shop` INT(10) NOT NULL,
            PRIMARY KEY (`id_ets_trans_log_request`),
            INDEX (`id_shop`, `page_type`, `status`,`api_type`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
	Db::getInstance()->execute($tblLogRequest);
	return true;
}