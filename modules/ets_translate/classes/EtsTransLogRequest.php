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

class EtsTransLogRequest extends ObjectModel
{
	public static $definition = array(
		'table' => 'ets_trans_log_request',
		'primary' => 'id_ets_trans_log_request',
		'fields' => array(
			'page_type' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'lang_source' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'lang_target' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'text_translated' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'allow_null' => true
			),
			'text_response' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'allow_null' => true
			),
			'status' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isInt'
			),
			'character_count' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isInt'
			),
			'api_type' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'date_add' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'date_upd' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'deleted_at' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString'
			),
			'message' => array(
				'type' => self::TYPE_HTML,
				'validate' => 'isCleanHtml'
			),
			'id_shop' => array(
				'type' => self::TYPE_INT,
				'validate' => 'isInt'
			),
		)
	);
	public $id_ets_trans_log_request;
	public $page_type;
	public $lang_source;
	public $lang_target;
	public $text_translated;
	public $text_response;
	public $status;
	public $character_count;
	public $api_type;
	public $date_add;
	public $date_upd;
	public $deleted_at;
	public $message;
	public $id_shop;

	public static function saveLog($pageType, $apiType, $status, $arrTextTranslate, $arrTextResponse, $langSource, $langTarget, $character_count = null, $message = '')
	{

		$log = new EtsTransLogRequest();
		$log->page_type = $pageType ?: '';
		$log->lang_source = $langSource;
		$log->lang_target = $langTarget;
		$log->text_translated = json_encode($arrTextTranslate);
		$log->text_response = json_encode($arrTextResponse);
		$log->status = $status;
		$log->character_count = $character_count ?: EtsTransPage::countTextTranslated($arrTextTranslate);
		$log->date_add = date('Y-m-d H:i:s');
		$log->api_type = $apiType;
		$log->message = $message;
		$log->id_shop = Context::getContext()->shop->id;
		$log->save();
	}

	public static function getTotalTextTrans($from, $to, $type = 'all')
	{
		$dq = new DbQuery();
		$dq->select('SUM(character_count) as count')
			->from('ets_trans_log_request')
			->where('(date_add BETWEEN \'' . pSQL($from) . '\' AND \'' . pSQL($to) . '\')')
			->where('status=1');
		if ($type != 'all') {
			$dq->where('api_type=\'' . pSQL($type) . '\'');
		}
		$res = Db::getInstance()->executeS($dq);
		if ($res && count($res) && isset($res[0]['count']))
			return $res[0]['count'];
		return null;
	}
}