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

class ETS_CFU_Contact_Reply extends ObjectModel
{
    public static $definition = array(
        'table' => 'ets_cfu_message_reply',
        'primary' => 'id_ets_cfu_message_reply',
        'fields' => array(
            'id_contact_message' => array('type' => self::TYPE_INT),
            'id_employee' => array('type' => self::TYPE_INT),
            'content' => array('type' => self::TYPE_HTML),
            'reply_to' => array('type' => self::TYPE_HTML),
            'subject' => array('type' => self::TYPE_HTML),
            'attachment' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );
    public $id_contact_message;
    public $id_employee;
    public $content;
    public $reply_to;
    public $subject;
    public $attachment;
    public $attachment_file;
    public $date_add;
    public $date_upd;

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }

    public static function getCountReplies($args = array(), &$y_max_value = 0, Context $context = null)
    {
        if ($context == null)
            $context = Context::getContext();
        $sql = 'SELECT COUNT(r.id_ets_cfu_message_reply) FROM `' . _DB_PREFIX_ . 'ets_cfu_message_reply` r
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m ON (r.id_contact_message = m.id_contact_message)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_shop` cs ON (m.id_contact = cs.id_contact)
            WHERE m.id_contact_message is NOT NULL AND cs.id_contact is NOT NULL AND cs.id_shop=' . (int)$context->shop->id . ''
            . (isset($args['id_contact']) && $args['id_contact'] ? ' AND cs.id_contact=' . (int)$args['id_contact'] : '')
            . (isset($args['year']) && $args['year'] ? ' AND YEAR(r.date_add) ="' . pSQL($args['year']) . '"' : '')
            . (isset($args['month']) && $args['month'] ? ' AND MONTH(r.date_add) ="' . pSQL($args['month']) . '"' : '')
            . (isset($args['day']) && $args['day'] ? ' AND DAY(r.date_add) ="' . pSQL($args['day']) . '"' : '');
        $result = (int)Db::getInstance()->getValue($sql);
        if ($result > $y_max_value)
            $y_max_value = $result;
        return $result;
    }
}