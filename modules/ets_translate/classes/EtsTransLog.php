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

class EtsTransLog extends ObjectModel
{
    public $id_ets_trans_log;
    public $page_type;
    public $id_session;
    public $lang_source;
    public $lang_target;
    public $date_add;
    public $timeout;
    public $ids_translated;
    public $text_translated;
    public $status;
    public $res_message;
    public $id_shop;

    public static $definition = array(
        'table' => 'ets_trans_log',
        'primary' => 'id_ets_trans_log',
        'fields' => array(
            'page_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'id_session' => array(
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
            'date_add' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'timeout' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'ids_translated' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'allow_null' => true
            ),
            'text_translated' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'allow_null' => true
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'res_message' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'allow_null' => true
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
        )
    );

    public static function getSubTextFromDataSource($dataSource)
    {
        $text = "";
        $overflow = 0;
        foreach ($dataSource as $item) {
            $text .= $item . ';';
            if (Tools::strlen('text') > 50) {
                $overflow = 1;
                break;
            }
        }
        $text = rtrim($text, ';');
        if ($overflow) {
            $text .= '...';
        }
        return $text;
    }

    public static function getLogLangTargetFromIds($langTarget)
    {
        if (is_array($langTarget)) {
            $text = '';
            foreach ($langTarget as $lang) {
                $text .= Language::getIsoById((int)$lang) . ',';
            }

            return rtrim($text, ',');
        }
        return Language::getIsoById((int)$langTarget);
    }

    public static function generateIdCode($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function getLogs($limit = 50, $page = 1)
    {
        $idShop = Context::getContext()->shop->id;
        $totalLog = Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."ets_trans_log` WHERE id_shop=".(int)$idShop);
        $totalPage = ceil($totalLog / $limit);
        $nextPage = $page + 1;
        if($totalPage < (int)$page){
            $page = $totalPage;
            $nextPage = $page;
        }
        $offset = $limit * ((int)$page - 1);
        if($offset < 0){
            $offset = 0;
        }
        $logs = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."ets_trans_log` WHERE id_shop=".(int)$idShop." ORDER BY id_ets_trans_log DESC LIMIT ".(int)$offset.",".(int)$limit);
        return array(
            'total_page' => $totalPage,
            'current_page' => $page,
            'next_page' => $nextPage,
            'prev_page' => $page - 1,
            'total' => $totalLog,
            'data' => $logs
        );
    }

    public static function clearAllLog($context = null)
    {
        if(!$context){
            $context = Context::getContext();
        }
        return Db::getInstance()->execute("TRUNCATE TABLE `"._DB_PREFIX_."ets_trans_log`");
    }
    public static function logTranslate($pageType, $status, $timeStartTrans, $idLangSource, $idLangTarget, $text = null, $ids = null, $idSession= null, $errorMessage=null, $character_count = null)
    {
    	/** @var Ets_Translate $module */
    	$module = Module::getInstanceByName('ets_translate');
	    $module->_clearCache('list_translate_log.tpl', $module->_getCacheId());
        if(!(int)Configuration::get('ETS_TRANS_ENABLE_LOG')){
            return false;
        }
        if(!$ids && !$text){
            return false;
        }
        $log = new EtsTransLog();
        $log->id_session = $idSession;
        $log->page_type = $pageType;
        $log->timeout = round(microtime(true) * 1000) - round($timeStartTrans * 1000);
        $log->date_add = date('Y-m-d H:i:s');
        $log->lang_source = Language::getIsoById((int)$idLangSource);
        $log->text_translated = $text;
        $log->ids_translated = $ids;
        $log->lang_target = EtsTransLog::getLogLangTargetFromIds($idLangTarget);
        $log->status = $status;
        $log->res_message =$errorMessage;
        $log->id_shop = Context::getContext()->shop->id;
        $log->save();

        return true;
    }

}