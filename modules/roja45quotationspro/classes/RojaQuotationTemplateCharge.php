<?php
/**
 * RojaQuotationTemplateCharge.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaQuotationTemplateCharge
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaQuotationTemplateCharge.
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

class RojaQuotationTemplateCharge extends ObjectModel
{
    public static $CHARGE = 'CHARGE';
    public static $SHIPPING = 'SHIPPING';
    public static $HANDLING = 'HANDLING';
    public static $DISCOUNT = 'DISCOUNT';

    public static $PERCENTAGE = 'PERCENTAGE';
    public static $VALUE = 'VALUE';

    public $id_roja45_quotation_template_charge;
    public $id_roja45_quotation_template;
    public $charge_name;
    public $charge_type;
    public $charge_method;
    public $charge_value;
    public $charge_amount;
    public $charge_amount_wt;
    public $specific_product;
    public $id_roja45_quotation_product;
    public $id_cart_rule;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_template_charge',
        'primary' => 'id_roja45_quotation_template_charge',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_template' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'charge_name' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_type' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_method' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_amount_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'specific_product' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_roja45_quotation_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_cart_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public static function getList($id_quotation, $type = null)
    {
        $sql = '
            SELECT *
            FROM `'._DB_PREFIX_.'roja45_quotationspro_charge`
            WHERE `id_roja45_quotation` = '.(int) $id_quotation;
        if ($type) {
            $sql .= ' AND `charge_type` = "'.pSQL($type).'"';
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getAllCharges($id_quotation)
    {
        $sql = '
          SELECT * 
          FROM `'._DB_PREFIX_.'roja45_quotationspro_charge` 
          WHERE `id_roja45_quotation` = '.(int) $id_quotation . '
          AND  `charge_type` in ("'.self::$CHARGE.'", "'.self::$SHIPPING.'", "'.self::$HANDLING.'")';
        return Db::getInstance()->executeS($sql);
    }

    public static function getAllDiscounts($id_quotation)
    {
        $sql = '
          SELECT * 
          FROM `'._DB_PREFIX_.'roja45_quotationspro_charge` 
          WHERE `id_roja45_quotation` = '.(int) $id_quotation . '
          AND  `charge_type` in ("'.self::$DISCOUNT.'")';
        return Db::getInstance()->executeS($sql);
    }
}
