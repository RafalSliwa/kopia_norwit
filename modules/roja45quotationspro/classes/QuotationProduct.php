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

class QuotationProduct extends ObjectModel
{
    public $id_roja45_quotation_product;
    public $id_roja45_quotation;
    public $id_product;
    public $id_product_attribute;
    public $id_customization;
    public $id_shop;
    public $position;
    public $product_title;
    public $qty;
    public $comment;
    public $unit_price_tax_excl;
    public $unit_price_tax_incl;
    public $custom_price;
    public $custom_image;
    public $deposit_amount;
    public $discount;
    public $discount_type;
    public $customization_cost_exc;
    public $customization_cost_inc;
    public $customization_cost_type;
    public $id_specific_price;
    public $id_tax_rules_group;
    public $tax_rate;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_product',
        'primary' => 'id_roja45_quotation_product',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customization' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_title' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => false),
            'comment' => array('type' => self::TYPE_STRING),
            'qty' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'deposit_amount' => array('type' => self::TYPE_FLOAT),
            'discount' => array('type' => self::TYPE_FLOAT),
            'discount_type' => array('type' => self::TYPE_STRING),
            'customization_cost_exc' => array('type' => self::TYPE_FLOAT),
            'customization_cost_inc' => array('type' => self::TYPE_FLOAT),
            'customization_cost_type' => array('type' => self::TYPE_STRING),
            'custom_price' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'custom_image' => array('type' => self::TYPE_STRING),
            'id_specific_price' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'tax_rate' => array('type' => self::TYPE_FLOAT, 'required' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public function delete()
    {
        // get all products, delete them
        if ($this->id_customization) {
            $quotation_product_customizations = QuotationCustomization::getCustomizations(
                $this->id_product,
                $this->id_product_attribute,
                $this->id_customization,
                $this->id_lang
            );
            foreach ($quotation_product_customizations as $quotation_product_customization) {
                $customization = new QuotationCustomization(
                    $quotation_product_customization['id_roja45_quotation_customization']
                );
                $customization->delete();
            }
        }

        return parent::delete();
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this->update();
    }

    public function duplicate($id_roja45_quotation)
    {
        $this->id = null;
        $this->id_roja45_quotation = $id_roja45_quotation;
        $this->add();
        return $this->id;
    }

    public static function getIdQuotationProduct($id_roja45_quotation, $id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT qp.id_roja45_quotation_product
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product` qp
            WHERE qp.`id_roja45_quotation` = ' . (int) $id_roja45_quotation . ' AND qp.`id_product` = ' . (int) $id_product);
    }

    public static function getList($id_quotation)
    {
        return Db::getInstance()->executeS('SELECT *
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`
            WHERE `id_roja45_quotation` = ' . (int) $id_quotation);
    }

    public static function getQuotationProduct(
        $id_roja45_quotation,
        $id_product,
        $id_product_attribute,
        $id_customization = 0
    ) {
        return Db::getInstance()->getValue('SELECT id_roja45_quotation_product
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_product`
            WHERE `id_roja45_quotation` = ' . (int) $id_roja45_quotation . '
            AND `id_product` = ' . (int) $id_product . '
            AND `id_product_attribute` = ' . (int) $id_product_attribute . '
            AND `id_customization` = ' . (int) $id_customization);
    }
}
