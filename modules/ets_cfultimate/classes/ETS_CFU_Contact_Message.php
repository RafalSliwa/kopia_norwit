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

class ETS_CFU_Contact_Message extends ObjectModel
{
    public static $definition = array(
        'table' => 'ets_cfu_contact_message',
        'primary' => 'id_contact_message',
        'fields' => array(
            'id_contact' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'id_product' => array('type' => self::TYPE_INT),
            'ip' => array('type' => self::TYPE_STRING),
            'subject' => array('type' => self::TYPE_HTML),
            'sender' => array('type' => self::TYPE_HTML),
            'readed' => array('type' => self::TYPE_INT),
            'special' => array('type' => self::TYPE_INT),
            'body' => array('type' => self::TYPE_HTML),
            'recipient' => array('type' => self::TYPE_HTML),
            'attachments' => array('type' => self::TYPE_HTML),
            'reply_to' => array('type' => self::TYPE_HTML),
            'replied' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );
    public $id_contact;
    public $id_customer;
    public $id_product;
    public $ip;
    public $subject;
    public $sender;
    public $body;
    public $recipient;
    public $attachments;
    public $replied;
    public $reply_to;
    public $special;
    public $readed;
    public $date_add;
    public $date_upd;

    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` (`id_shop`, `id_contact_message`)
			VALUES(' . (int)$id_shop . ', ' . (int)$this->id . ')'
        );
        return $res;
    }

    public static function getCountUnreadMessage()
    {
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(DISTINCT  m.id_contact_message)
            FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m
            INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact` c on (m.id_contact=c.id_contact)
            WHERE m.readed = 0 AND ms.id_shop=' . (int)Context::getContext()->shop->id
        );
    }

    public static function getAttachmentsMessages($id_contact)
    {
        if (trim($id_contact) == '' || !Validate::isUnsignedInt($id_contact))
            return false;
        return Db::getInstance()->getValue('SELECT attachments FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_contact="' . (int)$id_contact . '"');
    }

    public static function getAttachmentsMessagesByIdContactMessage($id_contact_message)
    {
        if (trim($id_contact_message) == '' || !Validate::isUnsignedInt($id_contact_message))
            return false;
        return Db::getInstance()->getValue('SELECT attachments FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_contact_message="' . (int)$id_contact_message . '"');
    }

    public static function deleteByIdContactMessage($id_contact_message)
    {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_contact_message ="' . (int)$id_contact_message . '"');
    }

    public static function getMessageList($filters = '', $start = 0, $limit = 0, $count = false, $order_by = '')
    {
        $context = Context::getContext();
        $query = '
            SELECT m.*,cl.title,IF(r.message_reply_id IS NULL,0,1) AS replied 
            FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m
            INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_lang` cl on (m.id_contact=cl.id_contact AND cl.id_lang="' . (int)$context->language->id . '")
            LEFT JOIN (
                SELECT id_contact_message, MAX(id_ets_cfu_message_reply) AS `message_reply_id` 
                FROM `' . _DB_PREFIX_ . 'ets_cfu_message_reply`
            )  r ON (r.id_contact_message=m.id_contact_message)
            WHERE ms.id_shop="' . (int)$context->shop->id . '"' . (string)$filters . ' 
            ' . ($order_by ? 'ORDER BY ' . pSQL($order_by) : '') . ',replied' . ($limit ? ' LIMIT ' . (int)$start . ',' . (int)$limit : '');
        $messages = Db::getInstance()->executeS($query);
        if ($count)
            return count($messages);
        if ($messages) {
            foreach ($messages as &$message) {
                $message['attachments'] = $message['attachments'] ? explode(',', $message['attachments']) : array();
            }
        }
        return $messages;
    }

    public static function nbMessageReply($id_contact_message)
    {
        if (trim($id_contact_message) == '' || !Validate::isUnsignedInt($id_contact_message))
            return 0;
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_cfu_message_reply` WHERE id_contact_message="' . (int)$id_contact_message . '"');
    }

    public static function countMessage($id_contact)
    {
        if (trim($id_contact) == '' || !Validate::isUnsignedInt($id_contact))
            return 0;
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_contact=' . (int)$id_contact);
    }

    public static function nbMessageReplied($id_shop)
    {
        if (trim($id_shop) == '' || !Validate::isUnsignedInt($id_shop))
            return 0;
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(DISTINCT m.id_contact_message)
            FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m
            INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (ms.id_contact_message = m.id_contact_message AND ms.id_shop = ' . (int)$id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_message_reply` r ON (r.id_contact_message = m.id_contact_message)
            WHERE r.id_ets_cfu_message_reply is NOT NULL AND r.id_ets_cfu_message_reply != 0
        ');
    }

    public static function nbMessageContactByCustomer($id_shop)
    {
        if (trim($id_shop) == '' || !Validate::isUnsignedInt($id_shop))
            return 0;
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(DISTINCT m.id_customer) 
            FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m
            INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (m.id_contact_message = ms.id_contact_message AND ms.id_shop = ' . (int)$id_shop . ')
            WHERE id_customer is NOT NULL AND id_customer != 0
        ');
    }

    public static function nbMessageContact($id_shop)
    {
        if (trim($id_shop) == '' || !Validate::isUnsignedInt($id_shop))
            return 0;
        return (int)Db::getInstance()->getValue('
            SELECT COUNT(m.id_customer) FROM (
              SELECT * FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_customer is NOT NULL AND id_customer != 0 GROUP BY id_customer
              UNION
              SELECT * FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_customer is NULL OR id_customer = 0) as `m` 
            INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (m.id_contact_message = ms.id_contact_message)
            WHERE ms.id_shop = ' . (int)$id_shop . '
        ');
    }

    public static function updateSpecial($id_message, $special)
    {
        if (trim($id_message) == '' || !Validate::isUnsignedInt($id_message))
            return false;
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_cfu_contact_message` SET `special`=' . (int)$special . ' WHERE `id_contact_message` =' . (int)$id_message);
    }

    public static function updateIsRead($id_contact_message, $read = 1)
    {
        if (trim($id_contact_message) == '' || !Validate::isUnsignedInt($id_contact_message))
            return false;
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_cfu_contact_message` SET readed=' . (int)$read . ' WHERE id_contact_message ="' . (int)$id_contact_message . '"');
    }

    public static function deleteAllByIdContactMessage($id_contact_message)
    {
        if (trim($id_contact_message) == '' || !Validate::isUnsignedInt($id_contact_message))
            return false;
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` WHERE id_contact_message="' . (int)$id_contact_message . '"');
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` where id_contact_message=' . (int)$id_contact_message);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_cfu_message_reply` WHERE id_contact_message=' . (int)$id_contact_message);

        return true;
    }

    public static function getRepliesByIdContactMessage($id_message, Context $context = null)
    {
        if (trim($id_message) == '' || !Validate::isUnsignedInt($id_message))
            return false;
        if ($context == null)
            $context = Context::getContext();
        $replies = Db::getInstance()->executeS('
           SELECT * FROM `' . _DB_PREFIX_ . 'ets_cfu_message_reply` 
           WHERE id_contact_message="' . (int)$id_message . '"'
        );
        if ($replies) {
            foreach ($replies as &$reply) {
                if (isset($reply['attachment']) && trim($reply['attachment']) !== '' && @file_exists(_PS_DOWNLOAD_DIR_ . 'ets_cfultimate' . DIRECTORY_SEPARATOR . $reply['attachment'])) {
                    $reply['attachment_file'] = isset($context->employee) ? $context->link->getAdminLink('AdminContactFormUltimateDownload', true, [], ['file' => $reply['attachment']]) : $context->link->getModuleLink('ets_cfultimate', 'download', ['file' => $reply['attachment']]);
                }
            }
        }
        return $replies;
    }

    public static function getMessageByIdContactMessage($id_message, $context = null)
    {
        if (trim($id_message) == '' || !Validate::isUnsignedInt($id_message))
            return false;
        if ($context == null)
            $context = Context::getContext();

        $message = Db::getInstance()->getRow('
        SELECT m.*,cl.title,c.save_attachments ,c.email_to, CONCAT(cu.firstname," ",cu.lastname) as customer_name
        FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m
        INNER JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_message_shop` ms ON (m.id_contact_message=ms.id_contact_message)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact` c ON (c.id_contact=m.id_contact)
        lEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_lang` cl on (c.id_contact=cl.id_contact AND cl.id_lang="' . (int)$context->language->id . '")
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` cu ON (m.id_customer=cu.id_customer)
        WHERE ms.id_shop="' . (int)Context::getContext()->shop->id . '" AND m.id_contact_message=' . (int)$id_message);

        if (trim($message['attachments']))
            $message['attachments'] = explode(',', trim($message['attachments']));
        else
            $message['attachments'] = '';
        $message['replies'] = ETS_CFU_Contact_Message::getRepliesByIdContactMessage($message['id_contact_message']);
        $message['from_reply'] = Configuration::get('PS_SHOP_NAME') . ' <' . (Configuration::get('PS_MAIL_METHOD') == 2 ? Configuration::get('PS_MAIL_USER') : Configuration::get('PS_SHOP_EMAIL')) . '>';
        $message['reply'] = Configuration::get('PS_SHOP_NAME') . ' <' . Configuration::get('PS_SHOP_EMAIL') . '>';
        $message['email_to'] = explode(',', trim($message['email_to']))[0];
        if (isset($message['id_product']) && (int)$message['id_product'] > 0) {
            $product = new Product((int)$message['id_product'], false, $context->language->id);
            $cover = Product::getCover($product->id, $context);
            $message['product'] = array(
                'link' => $context->link->getProductLink($product),
                'image' => isset($cover['id_image']) && (int)$cover['id_image'] > 0 ? $context->link->getImageLink($product->link_rewrite, $cover['id_image'], ETS_CFU_Tools::getFormattedName('home')) : '',
                'name' => $product->name,
            );
        }
        return $message;
    }

    public static function getCountMessage($args = array(), &$y_max_value = 0, Context $context = null)
    {
        if ($context == null)
            $context = Context::getContext();
        $sql = '
            SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'ets_cfu_contact_message` m 
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_cfu_contact_shop` cs ON (m.id_contact = cs.id_contact)
            WHERE cs.id_contact is NOT NULL AND  cs.id_shop = ' . (int)$context->shop->id
            . (isset($args['id_contact']) && $args['id_contact'] ? ' AND cs.id_contact=' . (int)$args['id_contact'] : '')
            . (isset($args['year']) && $args['year'] ? ' AND YEAR(m.date_add) ="' . pSQL($args['year']) . '"' : '')
            . (isset($args['month']) && $args['month'] ? ' AND MONTH(m.date_add) ="' . pSQL($args['month']) . '"' : '')
            . (isset($args['day']) && $args['day'] ? ' AND DAY(m.date_add) ="' . pSQL($args['day']) . '"' : '')
            . (isset($args['read']) ? ' AND m.readed ="' . pSQL($args['read']) . '"' : '');
        $result = (int)Db::getInstance()->getValue($sql);
        if ($result > $y_max_value)
            $y_max_value = $result;
        return $result;
    }
}