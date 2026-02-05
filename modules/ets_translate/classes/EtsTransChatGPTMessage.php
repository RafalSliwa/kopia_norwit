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
class EtsTransChatGPTMessage extends ObjectModel
{
	public static $instance;
	public $is_chatgpt;
	public $message;
	public $apply_for;
	public $date_add;
	public static $definition = array(
		'table' => 'ets_trans_chatgpt_message',
		'primary' => 'id_ets_trans_chatgpt_message',
		'fields' => array(
			'is_chatgpt' => array('type' => self::TYPE_INT),
			'message' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'apply_for' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
		)
	);

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new EtsTransChatGPTMessage();
		}
		return self::$instance;
	}

	public function l($string)
	{
		return Translate::getModuleTranslation('ets_translate', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
	}

	public static function getMessages($lastID = false, $applyFor = 'form_step1_description_', $id_lang = null)
	{
		$messages = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ets_trans_chatgpt_message` '.($lastID ? ' WHERE id_ets_trans_chatgpt_message <'.(int)$lastID:'').' ORDER BY id_ets_trans_chatgpt_message DESC LIMIT 0,10');
		if($messages)
		{
			foreach($messages as &$message)
			{
				$message['content'] = EtsTransChatGPT::getInstance()->displayMessage($message['id_ets_trans_chatgpt_message'], $id_lang, $message['apply_for'] ?: $applyFor);
			}
		}
		return $messages;
	}


	public function clearDataChatGpt() {
		if (Db::getInstance()->execute("TRUNCATE TABLE `"._DB_PREFIX_."ets_trans_chatgpt_message`")) {
			return [
				'errors' => false,
				'message' => $this->l('Clear all data successfully!')
			];
		}
		return [
			'errors' => true,
			'message' => $this->l('Cannot clear ChatGPT data!')
		];
	}
}