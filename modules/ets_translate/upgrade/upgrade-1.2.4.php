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
/**
 * @param Ets_Translate $object
 * @return bool
 */
function upgrade_module_1_2_4($object)
{

	$tblChatGPTMessage = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_message` (
             `id_ets_trans_chatgpt_message` INT(11) NOT NULL AUTO_INCREMENT , 
             `is_chatgpt` INT(1) NOT NULL ,
             `message` text,
             `date_add` datetime,
          PRIMARY KEY (`id_ets_trans_chatgpt_message`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

	$tblChatGPTTemp ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_template` (
         `id_ets_trans_chatgpt_template` INT(11) NOT NULL AUTO_INCREMENT , 
         `position` INT(11) NOT NULL ,
        PRIMARY KEY (`id_ets_trans_chatgpt_template`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

	$tblChatGPTTemplateLang ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_template_lang` (
         `id_ets_trans_chatgpt_template` INT(11) NOT NULL , 
         `id_lang` INT(11) NOT NULL , 
         `label` text ,
         `content` text ,
        PRIMARY KEY (`id_ets_trans_chatgpt_template`,`id_lang`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

	return Db::getInstance()->execute($tblChatGPTMessage)
		&& Db::getInstance()->execute($tblChatGPTTemp)
		&& Db::getInstance()->execute($tblChatGPTTemplateLang);
}