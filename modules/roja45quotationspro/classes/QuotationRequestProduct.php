<?php
/**
 * QuotationRequestProduct.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationRequestProduct
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationRequestProduct.
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

class QuotationRequestProduct extends ObjectModel
{
    public $id_roja45_quotation_requestproduct;
    public $id_roja45_quotation_request;
    public $id_shop;
    public $id_product;
    public $id_product_attribute;
    public $id_customization;
    public $qty;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_requestproduct',
        'primary' => 'id_roja45_quotation_requestproduct',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_request' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => true,
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => true,
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => true,
            ),
            'id_product_attribute' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => false,
            ),
            'id_customization' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => false,
            ),
            'qty' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => false,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'lang' => false,
                'required' => false,
            ),
        ),
    );

    public function delete()
    {
        $customization = new QuotationCustomization($this->id_customization);
        $customization->delete();
        return parent::delete();
    }

    /*
    public function addCustomization($id_customization)
    {
    if ($id_roja45_quotation_requestproduct_customization = QuotationRequestProductCustomization::exists(
    $this->id_roja45_quotation_requestproduct,
    $id_customization
    )) {
    $customization = new QuotationRequestProductCustomization(
    $id_roja45_quotation_requestproduct_customization
    );
    $customization->save();
    } else {
    $customization = new QuotationRequestProductCustomization();
    }
    $customization->id_roja45_quotation_requestproduct = $this->id;
    $customization->id_customization = $id_customization;
    return $customization->save();
    }
     */

    public function updateQty($qty)
    {
        $result = Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`
			SET `qty` = ' . $qty . ', `date_add` = NOW()
			WHERE `id_product` = ' . (int) $this->id_product .
            (!empty($this->id_product_attribute) ?
                ' AND `id_product_attribute` = ' . (int) $this->id_product_attribute : '') .
            (!empty($this->id_customization) ?
                ' AND `id_customization` = ' . (int) $this->id_customization : '') . '
			AND `id_roja45_quotation_requestproduct` = ' . (int) $this->id_roja45_quotation_requestproduct . '
			LIMIT 1'
        );
        return $result;
    }

    public static function exists($id_roja45_quotation_request, $id_product, $id_product_attribute, $id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }
        $sql = new DbQuery();
        $sql->select('id_roja45_quotation_requestproduct');
        $sql->from('roja45_quotationspro_requestproduct', 'rp');
        $sql->where('rp.id_roja45_quotation_request=' . (int) $id_roja45_quotation_request);
        $sql->where('rp.id_product=' . (int) $id_product);
        $sql->where('rp.id_product_attribute=' . (int) $id_product_attribute);
        $sql->where('rp.id_shop=' . (int) $id_shop);
        return Db::getInstance()->getValue($sql);
    }

    public static function updateQtyStatic($id_roja45_quotation_request, $id_product, $id_product_attribute, $qty)
    {
        $result = Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`
			SET `qty` = ' . $qty . ', `date_add` = NOW()
			WHERE `id_product` = ' . (int) $id_product .
            (!empty($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
			AND `id_roja45_quotation_request` = ' . (int) $id_roja45_quotation_request . '
			LIMIT 1'
        );
        return $result;
    }
}
