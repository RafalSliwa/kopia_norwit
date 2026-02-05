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
class Ets_pmn_massedit_log extends ObjectModel
{
    public $name;
    public $fields;  
    public static $definition = array(
		'table' => 'ets_pmn_massedit_log',
		'primary' => 'id_ets_pmn_massedit_log',
		'multilang' => false,
		'fields' => array(
			'id_ets_pmn_massedit_history' => array('type' => self::TYPE_INT),
            'editlog' => array('type' => self::TYPE_STRING),
        )
	);
    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
	public static function deleteAllLog()
    {
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_pmn_massedit_history');
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ets_pmn_massedit_history_product');
        return true;
    }
 }