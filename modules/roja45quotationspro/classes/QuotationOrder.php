<?php
/**
 * QuotationProduct.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationProduct
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationProduct.
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

class QuotationOrder extends ObjectModel
{
    public $id_roja45_quotation_order;
    public $id_roja45_quotation;
    public $id_order;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_order',
        'primary' => 'id_roja45_quotation_order',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public static function getList($id_quotation)
    {
        return Db::getInstance()->executeS(
            'SELECT * 
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_order` 
            WHERE `id_roja45_quotation` = ' . (int)$id_quotation
        );
    }

    public static function hasOrders($id_quotation)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(id_roja45_quotation_order) 
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_order` 
            WHERE `id_roja45_quotation` = ' . (int)$id_quotation
        );
    }

    public static function getLastOrder($id_quotation)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_order', 'qo');
        $sql->where('id_roja45_quotation = ' . (int) $id_quotation);
        $sql->orderBy('date_add ASC');
        $sql->limit(1);

        return Db::getInstance()->executeS($sql);
    }

    public static function exists($id_roja45_quotation, $id_order)
    {
        $sql = new DbQuery();
        $sql->select('qo.id_roja45_quotation_product');
        $sql->from('roja45_quotationspro_product', 'qo');
        $sql->where('qo.id_roja45_quotation='.(int) $id_roja45_quotation);
        $sql->where('qo.id_order='.(int) $id_order);
        if (Db::getInstance()->getValue($sql)) {
            return true;
        } else {
            return false;
        }
    }
}
