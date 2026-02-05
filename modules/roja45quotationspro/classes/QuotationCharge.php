<?php
/**
 * QuotationCharge.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationCharge
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationCharge.
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

class QuotationCharge extends ObjectModel
{
    public static $CHARGE = 'CHARGE';
    public static $SHIPPING = 'SHIPPING';
    public static $HANDLING = 'HANDLING';
    public static $DISCOUNT = 'DISCOUNT';

    public static $PERCENTAGE = 'PERCENTAGE';
    public static $VALUE = 'VALUE';

    const FILTER_ACTION_ALL = 1;
    const FILTER_ACTION_SHIPPING = 2;
    const FILTER_ACTION_REDUCTION = 3;
    const FILTER_ACTION_GIFT = 4;
    const FILTER_ACTION_ALL_NOCAP = 5;

    public $id_roja45_quotation_charge;
    public $id_roja45_quotation;
    public $charge_name;
    public $charge_type;
    public $charge_method;
    public $charge_default;
    public $charge_value;
    public $charge_amount;
    public $charge_amount_wt;
    public $charge_handling;
    public $charge_handling_wt;
    public $specific_product;
    public $id_roja45_quotation_product;
    public $id_cart_rule;
    public $id_carrier;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_charge',
        'primary' => 'id_roja45_quotation_charge',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'charge_name' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_type' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_method' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_default' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'charge_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_amount_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_handling' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'charge_handling_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'specific_product' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_roja45_quotation_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_cart_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
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

    public static function getChargeValue($quotation, $charge, $products, $use_tax, $filter = null)
    {
        if (!$filter) {
            $filter = CartRule::FILTER_ACTION_ALL;
        }

        $reduction_value = 0;
        if (in_array($filter, array(self::FILTER_ACTION_ALL, self::FILTER_ACTION_REDUCTION))) {
            // Discount (%) on the whole order
            if (($charge['charge_method'] == self::$PERCENTAGE) && !$charge['specific_product']) {
                // Do not give a reduction on free products!
                $quotation_total = $quotation->getQuotationTotal($use_tax, Cart::ONLY_PRODUCTS);
                $reduction_value += $quotation_total * ($charge['charge_value'] / 100);
            }

            // Discount (%) on a specific product
            if (($charge['charge_method'] == self::$PERCENTAGE) && ($charge['specific_product'] > 0)) {
                foreach ($products as $product) {
                    if ($product['id_product'] == $charge['id_roja45_quotation_product']) {
                    }
                }
            }

            if ($charge['charge_method'] == self::$VALUE) {
                if ($use_tax) {
                    //$reduction_amount = $charge['charge_amount_wt'];
                } else {
                    //$reduction_amount = $charge['charge_amount'];
                }
            }
            //Cache::store($cache_id, $reduction_value);
            return $reduction_value;
        }
    }
}
