<?php
/**
 * QuotationMessage.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationMessage
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationMessage.
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

class QuotationMessage extends ObjectModel
{
    public $id_roja45_quotation_message;
    public $id_roja45_quotation;
    public $id_customer_thread;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_message',
        'primary' => 'id_roja45_quotation_message',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer_thread' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        ),
    );

    public static function getList($id_quotation)
    {
        $sql = '
            SELECT * 
            FROM `'._DB_PREFIX_.'roja45_quotationspro_message` 
            WHERE `id_roja45_quotation` = '.(int) $id_quotation;

        return Db::getInstance()->executeS($sql);
    }

    public static function deleteQuotationMessages($id_roja45_quotation)
    {
        $sql = '
            DELETE FROM `'._DB_PREFIX_.'roja45_quotationspro_message` 
            WHERE `id_roja45_quotation` = '.(int) $id_roja45_quotation;
        return Db::getInstance()->execute($sql);
    }

    public static function getQuotationForThread($id_customer_thread)
    {
        $sql = '
            SELECT id_roja45_quotation 
            FROM `'._DB_PREFIX_.'roja45_quotationspro_message`
            WHERE `id_customer_thread` = '.(int) $id_customer_thread;
        return Db::getInstance()->executeS($sql);
    }

    public static function getCustomerThread($id_quotation)
    {
        $sql = new DbQuery();
        $sql->select('id_customer_thread');
        $sql->from('roja45_quotationspro_message', 'qm');
        $sql->where('qm.id_roja45_quotation='.(int) $id_quotation);
        $sql->orderBy('qm.id_roja45_quotation DESC');
        return Db::getInstance()->getValue($sql);
    }
}
