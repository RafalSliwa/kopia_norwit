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
class Ets_pmn_massedit_condition_action extends ObjectModel
{
    public static $instance;
    public $id_ets_pmn_massedit;
    public $id_ets_pmn_massedit_history;
    public $condition;
    public $field;
    public $value;
    public $value_lang;
    public static $definition = array(
		'table' => 'ets_pmn_massedit_condition_action',
		'primary' => 'id_ets_pmn_massedit_condition_action',
		'multilang' => true,
		'fields' => array(
			'id_ets_pmn_massedit' => array('type' => self::TYPE_INT),
            'id_ets_pmn_massedit_history' => array('type'=>self::TYPE_INT),
            'condition' => array('type'=>self::TYPE_STRING),
            'field' => array('type'=> self::TYPE_STRING),
            'value' => array('type'=> self::TYPE_STRING),
            'value_lang' => array('type'=>self::TYPE_HTML,'lang'=>true),
        )
	);
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_pmn_massedit_condition_action();
        }
        return self::$instance;
    }
    public static function deleteConditioField($id_massedit)
    {
        $condisionFields = Db::getInstance()->executeS('SELECT id_ets_pmn_massedit_condition_action FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` WHERE id_ets_pmn_massedit='.(int)$id_massedit);
        if($condisionFields)
        {
            foreach($condisionFields as $condisionField)
            {
                $condisionFieldObj = new Ets_pmn_massedit_condition_action($condisionField['id_ets_pmn_massedit_condition_action']);
                $condisionFieldObj->delete();
            }
        }
        return true;
    }
    protected static $conditionFields;
    public function getConditionFields()
    {
        if(!self::$conditionFields)
        {
            self::$conditionFields = array(
                'name' => $this->l('Name'),
                'description_short' => $this->l('Summary'),
                'description' => $this->l('Description'),
                'reference' => $this->l('Reference'),
                'active' => $this->l('Enabled'),
                'id_category_default' => $this->l('Default category'),
                'id_categories' => $this->l('Category'),
                'id_manufacturer' => $this->l('Brand'),
                'features' => $this->l('Features'),
                'related_products' => $this->l('Related product'),
                'quantity' => $this->l('Quantity'),
                'minimal_quantity' => $this->l('Minimal quantity for sale'),
                'location' => $this->l('Stock location'),
                'low_stock_threshold' => $this->l('Low stock level'),
                'low_stock_alert' => $this->l('Send me an email when the quantity is below or equals this level'),
                'out_of_stock' => $this->l('Availability preferences'),
                'available_now' => $this->l('Label when in stock'),
                'available_later' => $this->l('Label when out of stock (and back order allowed)'),
                'combinations' => $this->l('Combinations'),
                'width' => $this->l('Width'),
                'height' => $this->l('Height'),
                'depth' => $this->l('Depth'),
                'weight' => $this->l('Weight'),
                'additional_delivery_times' => $this->l('Delivery time'),
                'delivery_in_stock' => $this->l('Delivery time of in-stock products'),
                'delivery_out_stock' => $this->l('Delivery time of out-of-stock products with allowed orders'),
                'additional_shipping_cost' => $this->l('Shipping fees'),
                'selectedCarriers' => $this->l('Available carriers'),
                'price' => $this->l('Price (tax excl.)'),
                'unit_price' => $this->l('Price per unit (tax excl.)'),
                'id_tax_rules_group' => $this->l('Tax rule'),
                'on_sale' => sprintf($this->l('Display the %sOn sale!%s flag on the product page, and on product listings.'),'"','"'),
                'wholesale_price' => $this->l('Cost price'),
                'meta_title' => $this->l('Meta title'),
                'meta_description' => $this->l('Meta description'),
                'link_rewrite' => $this->l('Friendly URL'),
                'visibility' => $this->l('Visibility'),
                'available_for_order' => $this->l('Available for order'),
                'online_only' => $this->l('Web only (not sold in your retail store)'),
                'tags' => $this->l('Tags'),
                'condition' => $this->l('Condition'),
                'show_condition' => $this->l('Show condition'),
                'isbn' => $this->l('ISBN'),
                'ean13' => $this->l('EAN-13 or JAN barcode'),
                'upc' => $this->l('UPC barcode'),
                'customization' => $this->l('Customization'),
                'specific_prices' => $this->l('Specific prices'),
            );
        }
        return self::$conditionFields;

    }
    protected static $actions;
    public function getConditionActions()
    {
        if(!self::$actions)
        {
            self::$actions = array(
                'off' => $this->l('Off'),
                'append_before' => $this->l('Append before'),
                'append_after' => $this->l('Append after'),
                'replace' => $this->l('Replace'),
                'active_all' => $this->l('Active all'),
                'disable_all' => $this->l('Disable all'),
                'remove' => $this->l('Remove'),
                'remove_all' => $this->l('Remove all'),
                'add' => $this->l('Add'),
                'minus_amount' => $this->l('Minus amount'),
                'plus_percent'=> $this->l('Plus percent'),
                'minus_percent' => $this->l('Minus percent'),
                'plus_amount'=> $this->l('Plus amount'),
            );
        }
        return self::$actions;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getActions($id_massedit)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` a
        LEFT JOIN `'._DB_PREFIX_.'ets_pmn_massedit_condition_action_lang` al ON (a.id_ets_pmn_massedit_condition_action = al.id_ets_pmn_massedit_condition_action AND al.value_lang!="false")
        WHERE a.id_ets_pmn_massedit='.(int)$id_massedit;
        return Db::getInstance()->executeS($sql);
    }
}