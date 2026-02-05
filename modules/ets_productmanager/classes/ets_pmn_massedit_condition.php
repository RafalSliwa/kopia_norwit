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
class Ets_pmn_massedit_condition extends ObjectModel
{
    const FILTERED_FIELD_ALL =0;
    const FILTERED_FIELD_NAME =1;
    const FILTERED_FIELD_DESCRIPTION =2;
    const FILTERED_FIELD_SUMMARY =3;
    const FILTERED_FIELD_ATTRIBUTE =4;
    const FILTERED_FIELD_BRAND =5;
    const FILTERED_FIELD_SUPPLIER =6;
    const FILTERED_FIELD_COLOR =7;
    const FILTERED_FIELD_ID_PRODUCT =8;
    const FILTERED_FIELD_QUANTITY =9;
    const FILTERED_FIELD_PRICE =10;
    const FILTERED_FIELD_CATEGORIES = 11;
    const FILTERED_FIELD_FEATURES =12;
    const FILTERED_FIELD_REFERENCE = 13;
    public static $instance;
    public $id_ets_pmn_massedit;
    public $id_lang; 
    public $filtered_field;
    public $operator;
    public $compared_value;
    public $context;
    public static $definition = array(
		'table' => 'ets_pmn_massedit_condition',
		'primary' => 'id_ets_pmn_massedit_condition',
		'multilang' => false,
		'fields' => array(
			'id_ets_pmn_massedit' => array('type' => self::TYPE_INT),
            'id_lang' => array('type' => self::TYPE_INT),
            'filtered_field' => array('type'=>self::TYPE_INT),
            'operator' => array('type'=> self::TYPE_STRING),
            'compared_value' => array('type'=>self::TYPE_HTML),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        $this->context = Context::getContext();
	}
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_pmn_massedit_condition();
        }
        return self::$instance;
    }
    protected static $fields;
    public function getListFields()
    {
        if(!isset(self::$fields))
        {
            self::$fields =  array(
                Ets_pmn_massedit_condition::FILTERED_FIELD_NAME => array(
                    'id'=> Ets_pmn_massedit_condition::FILTERED_FIELD_NAME,
                    'name' => $this->l('Name')
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_REFERENCE => array(
                    'id'=> Ets_pmn_massedit_condition::FILTERED_FIELD_REFERENCE,
                    'name' => $this->l('Reference')
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES => array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES,
                    'name' => $this->l('Categories'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE => array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE,
                    'name' => $this->l('Price'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_SUMMARY =>array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_SUMMARY,
                    'name' => $this->l('Summary'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_DESCRIPTION => array(
                    'id'=> Ets_pmn_massedit_condition::FILTERED_FIELD_DESCRIPTION,
                    'name' => $this->l('Description'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_QUANTITY => array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_QUANTITY,
                    'name' => $this->l('Quantity'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE =>array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE,
                    'name' => $this->l('Attributes'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES =>array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES,
                    'name' => $this->l('Features'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR => array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR,
                    'name' => $this->l('Colors'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND => array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND,
                    'name' => $this->l('Brands'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER=> array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER,
                    'name' => $this->l('Suppliers'),
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_ID_PRODUCT=>array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_ID_PRODUCT,
                    'name' => $this->l('ID')
                ),
                Ets_pmn_massedit_condition::FILTERED_FIELD_ALL=>array(
                    'id' => Ets_pmn_massedit_condition::FILTERED_FIELD_ALL,
                    'name' => $this->l('Select all products'),
                )
            );
            if(!$this->getAttributes())
                unset(self::$fields[Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE]);
            if(!$this->getFeatures())
                unset(self::$fields[Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES]);
            if(!$this->getAttributeColors())
                unset(self::$fields[Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR]);
            if(!$this->getManufacturers())
                unset(self::$fields[Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND]);
            if(!$this->getSuppliers())
                unset(self::$fields[Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER]);
            return self::$fields;
        }
        return self::$fields;

    }
    protected static $operators;
    public function getOperators()
    {
        if(!self::$operators)
        {
            self::$operators =  array(
                'has_words' => array(
                    'id' => 'has_words',
                    'name' => $this->l('Has words'),
                    'class' => 'operator text'
                ),
                'not_has_words' => array(
                    'id' => 'not_has_words',
                    'name' => $this->l('Not has words'),
                    'class' => 'operator text'
                ),
                'equal_to' => array(
                    'id'=>'equal_to',
                    'name' => $this->l('Equal to'),
                    'class' => 'operator number'
                ),
                'greater_than' => array(
                    'id'=>'greater_than',
                    'name' => $this->l('Greater than'),
                    'class' => 'operator number',
                ),
                'smaller_to' => array(
                    'id'=>'smaller_to',
                    'name' => $this->l('Smaller than'),
                    'class' => 'operator number',
                ),
                'only_default' => array(
                    'id'=>'only_default',
                    'name' => $this->l('Only default'),
                    'class' => 'operator default',
                ),
                'in' => array(
                    'id'=>'in',
                    'name' => $this->l('In selected'),
                    'class' => 'operator default in',
                ),
                'not_in' => array(
                    'id'=>'not_in',
                    'name' => $this->l('Not in selected'),
                    'class' => 'operator default in',
                ),
            );
        }
        return self::$operators;
    }
    protected static $attributeColors;
    public function getAttributeColors($id_attributes = false)
    {
        if(!self::$attributeColors)
        {
            $sql = 'SELECT a.id_attribute,al.name,a.color FROM `'._DB_PREFIX_.'attribute` a
            INNER JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.id_attribute_group = a.id_attribute_group)
            INNER JOIN `'._DB_PREFIX_.'attribute_shop` a_shop ON (a.id_attribute = a_shop.id_attribute AND a_shop.id_shop="'.(int)Context::getContext()->shop->id.'")
            INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON (ag.id_attribute_group = ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute = al.id_attribute AND al.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE ag.is_color_group=1'.($id_attributes ? ' AND a.id_attribute in ('.implode(',',array_map('intval',explode(',',$id_attributes))).')':'');
            self::$attributeColors = Db::getInstance()->executeS($sql);
        }
        return self::$attributeColors;
    }
    protected static $attributes;
    public function getAttributes($id_attributes=false)
    {
        if(!self::$attributes)
        {
            $sql = 'SELECT a.id_attribute,CONCAT(agl.public_name,": ",al.name) as name FROM `'._DB_PREFIX_.'attribute` a
            INNER JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.id_attribute_group = a.id_attribute_group)
            INNER JOIN `'._DB_PREFIX_.'attribute_shop` a_shop ON (a.id_attribute = a_shop.id_attribute AND a_shop.id_shop="'.(int)Context::getContext()->shop->id.'")
            INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON (ag.id_attribute_group = ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute = al.id_attribute AND al.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE ag.is_color_group=0'.($id_attributes ? ' AND a.id_attribute in ('.implode(',',array_map('intval',explode(',',$id_attributes))).')':'');
            self::$attributes = Db::getInstance()->executeS($sql);
        }
        return self::$attributes;
    }
    protected static $features;
    public function getFeatures($id_feature_values = false)
    {
        if(!self::$features)
        {
            $sql = 'SELECT fv.id_feature_value,CONCAT(fl.name,": ",fvl.value) as name FROM `'._DB_PREFIX_.'feature_value` fv
            INNER JOIN `'._DB_PREFIX_.'feature` f ON (f.id_feature = fv.id_feature)
            INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (f.id_feature = fs.id_feature AND fs.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (fl.id_feature = f.id_feature AND fl.id_lang="'.(int)Context::getContext()->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE fv.custom=0'.($id_feature_values ? ' AND fv.id_feature IN ('.implode(',',array_map('intval',explode(',',$id_feature_values))).')':'');
            self::$features =  Db::getInstance()->executeS($sql);
        }
        return self::$features;
    }
    protected static $manufacturers;
    public function getManufacturers($id_manufacturers = false)
    {
        if(!self::$manufacturers)
        {
            $sql = 'SELECT m.id_manufacturer,m.name FROM `'._DB_PREFIX_.'manufacturer` m 
            INNER JOIN `'._DB_PREFIX_.'manufacturer_shop` ms ON (m.id_manufacturer = ms.id_manufacturer AND ms.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE 1'.($id_manufacturers ? ' AND m.id_manufacturer in ('.implode(',',array_map('intval',explode(',',$id_manufacturers))).')':'');
            self::$manufacturers = Db::getInstance()->executeS($sql);
        }
        return self::$manufacturers;
    }
    protected static $suppliers;
    public function getSuppliers($id_suppliers = false)
    {
        if(!self::$suppliers)
        {
            $sql = 'SELECT s.id_supplier,s.name FROM `'._DB_PREFIX_.'supplier` s
            INNER JOIN `'._DB_PREFIX_.'supplier_shop` ss ON (s.id_supplier = ss.id_supplier AND ss.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE 1'.($id_suppliers ? ' AND s.id_supplier in ('.implode(',',array_map('intval',explode(',',$id_suppliers))).')':'');;
            self::$suppliers=  Db::getInstance()->executeS($sql);
        }
        return self::$suppliers;
    }
    public function getCategories($id_categories=false)
    {
        $sql = 'SELECT c.id_category,cl.name FROM  `'._DB_PREFIX_.'category` c
        INNER JOIN  `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN  `'._DB_PREFIX_.'category_lang` cl ON (cl.id_category= c.id_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE 1 '.($id_categories ? '  AND c.id_category IN ('.implode(',',array_map('intval',explode(',',$id_categories))).')':'').' GROUP BY c.id_category';
        return Db::getInstance()->executeS($sql);
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}