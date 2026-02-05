<?php
/**
 * QuotationConversation.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationConversation
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationConversation.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationConversationItem extends ObjectModel
{
    public $id_roja45_quotation_conversationitem;
    public $id_roja45_quotation_conversation;
    public $message;
    public $filename;
    public $id_employee;
    public $is_read;
    public $is_sent;
    public $is_private;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_conversationitem',
        'primary' => 'id_roja45_quotation_conversationitem',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_conversation' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'message' => array(
                'type' => self::TYPE_STRING,
                'required' => true
            ),
            'filename' => array(
                'type' => self::TYPE_STRING
            ),
            'id_employee' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'is_read' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_sent' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_private' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );
}
