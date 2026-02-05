<?php
/**
 * RojaQuotationTemplateProduct.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaQuotationTemplateProduct
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaQuotationTemplateProduct.
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

class RojaQuotationTemplateProduct extends ObjectModel
{
    public $id_roja45_quotation_template_product;
    public $id_roja45_quotation_template;
    public $id_product;
    public $id_product_attribute;
    public $product_title;
    public $qty;
    public $comment;
    public $unit_price_tax_excl;
    public $unit_price_tax_incl;
    public $custom_price;
    public $deposit_amount;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_template_product',
        'primary' => 'id_roja45_quotation_template_product',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_template' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_title' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'comment' => array('type' => self::TYPE_STRING),
            'qty' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'deposit_amount' => array('type' => self::TYPE_FLOAT),
            'custom_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public static function getList($id_quotation)
    {
        return Db::getInstance()->executeS(
            'SELECT *
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`
            WHERE `id_roja45_quotation` = ' . (int) $id_quotation
        );
    }
}
