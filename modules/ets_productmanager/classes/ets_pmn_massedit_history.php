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
class Ets_pmn_massedit_history extends ObjectModel
{
    public static $instance;
    public static $products = array();
    public static $carriers = array();
    public static $categories = array();
    public static $feature_values = array();
    public static $features = array();
    public static $manufacturers = array();
    public static $tax_rules = array();
    public static $tags = array();
    public $id_shop;
    public $id_ets_pmn_massedit;
    public $fields;
    public $edited_field;
    public $date_add;
    public static $definition = array(
		'table' => 'ets_pmn_massedit_history',
		'primary' => 'id_ets_pmn_massedit_history',
		'multilang' => false,
		'fields' => array(
			'id_ets_pmn_massedit' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT),
            'fields' => array('type'=> self::TYPE_STRING),
            'edited_field' => array('type'=>self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_pmn_massedit_history();
        }
        return self::$instance;
    }
    public function add($auto_date= true,$null_values=false)
    {
        if(parent::add($auto_date,$null_values))
        {
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` SET id_ets_pmn_massedit_history="'.(int)$this->id.'" WHERE id_ets_pmn_massedit_history=0 AND id_ets_pmn_massedit='.(int)$this->id_ets_pmn_massedit);
            return true;
        }
        return false;
    }
    public function getProducts($conditions,$total=false,$start=0,$limit=20,$excludeProducts='',$type_combine_condition='')
    {
        if(!in_array($type_combine_condition,array('and','or')))
            $type_combine_condition ='and';
        $filters = '';
        $leftJoin = '';
        $search_text_lang = array();
        $search_category = false;
        $search_attribute = false;
        $search_features = false;
        $search_supplier = false;
        if($conditions)
        {
            foreach($conditions as $condition)
            {
                $compared_value = is_array($condition['compared_value']) ? $condition['compared_value'] : trim($condition['compared_value']);
                $filtered_field = $condition['filtered_field'];
                if($compared_value!='')
                {
                    if(($id_lang = (int)$condition['id_lang']) && in_array($filtered_field,array(Ets_pmn_massedit_condition::FILTERED_FIELD_NAME,Ets_pmn_massedit_condition::FILTERED_FIELD_DESCRIPTION,Ets_pmn_massedit_condition::FILTERED_FIELD_SUMMARY)))
                    {
                        if(!isset($search_text_lang[$id_lang]))
                        {
                            $search_text_lang[$id_lang] = true;
                            $leftJoin .=' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl'.(int)$id_lang.' ON (pl'.(int)$id_lang.'.id_product= p.id_product AND pl'.(int)$id_lang.'.id_lang ="'.(int)$id_lang.'" AND pl'.(int)$id_lang.'.id_shop="'.(int)Context::getContext()->shop->id.'")';
                        }
                        $field ='';
                        $operator ='';
                        switch ($filtered_field) {
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_NAME:
                                $field = 'pl'.(int)$id_lang.'.name';
                            break;
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_DESCRIPTION:
                                $field = 'pl'.(int)$id_lang.'.description';
                            break;
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_SUMMARY :
                                $field ='pl'.(int)$id_lang.'.description_short';
                            break;
                        } 
                        switch($condition['operator'])
                        {
                            case 'has_words':
                                $operator = 'LIKE "%'.pSQL($compared_value).'%"';
                            break;
                            case 'not_has_words':
                                $operator = 'NOT LIKE "%'.pSQL($compared_value).'%"';
                            break;
                        }
                        if($field && $operator)
                        {
                            $filters .= ' '.pSQL($type_combine_condition).' '.bqSQL($field).' '.(string)$operator;
                        }
                                
                    }
                    if(in_array($filtered_field,array(Ets_pmn_massedit_condition::FILTERED_FIELD_REFERENCE)))
                    {
                        $field = 'p.reference';
                        $operator='';
                        switch($condition['operator'])
                        {
                            case 'has_words':
                                $operator = 'LIKE "%'.pSQL($compared_value).'%"';
                                break;
                            case 'not_has_words':
                                $operator = 'NOT LIKE "%'.pSQL($compared_value).'%"';
                                break;
                        }
                        if($field && $operator)
                        {
                            $filters .= ' '.pSQL($type_combine_condition).' '.bqSQL($field).' '.(string)$operator;
                        }
                    }
                    if(in_array($filtered_field,array(Ets_pmn_massedit_condition::FILTERED_FIELD_ID_PRODUCT,Ets_pmn_massedit_condition::FILTERED_FIELD_QUANTITY,Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE)))
                    {
                        $field ='';
                        $operator ='';
                        switch ($filtered_field) {
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_ID_PRODUCT:
                                $field = 'p.id_product';
                            break;
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_QUANTITY:
                                $field = 'sa.quantity';
                            break;
                          case Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE :
                                $field ='p.price';
                            break;
                        } 
                        switch($condition['operator'])
                        {
                            case 'equal_to':
                                $operator = '= "'.($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE ? (float)$compared_value: (int)$compared_value ).'"';
                            break;
                            case 'greater_than':
                                $operator = '> "'.($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE ? (float)$compared_value: (int)$compared_value ).'"';
                            break;
                            case 'smaller_to':
                                $operator = '< "'.($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE ? (float)$compared_value: (int)$compared_value ).'"';
                            break;
                        }
                        if($field && $operator)
                        {
                            $filters .= ' '.pSQL($type_combine_condition).' '.bqSQL($field).' '.(string)$operator;
                        }
                    }
                    if($filtered_field == Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND)
                    {
                        if($condition['operator']=='in')
                            $filters .=' '.pSQL($type_combine_condition).' p.id_manufacturer IN ('.implode(',',array_map('intval',$compared_value)).')';
                        else
                            $filters .=' '.pSQL($type_combine_condition).' p.id_manufacturer NOT IN ('.implode(',',array_map('intval',$compared_value)).')';
                    }
                    if($filtered_field == Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER)
                    {
                        if($condition['operator']=='only_default')
                        {
                            $filters .=' '.pSQL($type_combine_condition).' p.id_supplier IN ('.implode(',',array_map('intval',$compared_value)).')';
                        }
                        else
                        {
                            if(!$search_supplier)
                            {
                                $search_supplier = true;
                                $leftJoin .=' LEFT JOIN `'._DB_PREFIX_.'product_supplier` psu ON (psu.id_product=p.id_product)';
                            }
                            if($condition['operator']=='in')
                                $filters .=' '.pSQL($type_combine_condition).' psu.id_supplier IN ('.implode(',',array_map('intval',$compared_value)).')';
                            else
                                $filters .=' '.pSQL($type_combine_condition).' (psu.id_supplier NOT IN ('.implode(',',array_map('intval',$compared_value)).') OR psu.id_supplier is null)';
                        }
                    }
                    if($filtered_field == Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES)
                    {
                        if($condition['operator']=='only_default')
                        {
                            $filters .=' '.pSQL($type_combine_condition).' p.id_category_default IN ('.implode(',',array_map('intval',$compared_value)).')';
                        }
                        else
                        {
                            if(!$search_category)
                            {
                                $search_category = true;
                                $leftJoin .=' LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_product = p.id_product)
                                LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)';
                            }
                            if($condition['operator']=='in')
                                $filters .=' '.pSQL($type_combine_condition).' cp.id_category IN ('.implode(',',array_map('intval',$compared_value)).')';
                            else
                                $filters .=' '.pSQL($type_combine_condition).' cp.id_category NOT IN ('.implode(',',array_map('intval',$compared_value)).')';
                        }
                    }
                    if($filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE || $filtered_field == Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR)
                    {
                        if(!$search_attribute)
                        {
                            $search_attribute = true;
                            $leftJoin .= ' LEFT JOIN (
                               SELECT DISTINCT pa.id_product FROM `'._DB_PREFIX_.'product_attribute` pa
                               INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = pa.id_product_attribute AND pac.id_attribute IN ('.implode(',',array_map('intval',$compared_value)).'))
                            ) product_attribute ON (product_attribute.id_product=p.id_product)';
                        }
                        if($condition['operator']=='in')
                            $filters .=' '.pSQL($type_combine_condition).' product_attribute.id_product is not null';
                        else
                            $filters .=' '.pSQL($type_combine_condition).' product_attribute.id_product is null';
                    }
                    if($filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES)
                    {
                        if(!$search_features)
                        {
                            $search_features = true;
                            $leftJoin .= ' LEFT JOIN `'._DB_PREFIX_.'feature_product` fp ON (fp.id_product=p.id_product AND fp.id_feature_value IN ('.implode(',',array_map('intval',$compared_value)).'))';
                        }
                        if($condition['operator']=='in')
                            $filters .=' '.pSQL($type_combine_condition).' fp.id_feature_value is NOT NULL';
                        else
                            $filters .=' '.pSQL($type_combine_condition).' fp.id_feature_value is null';
                    }
                }
                
            }
        }
        if($total)
        {
            $sql ='SELECT COUNT(DISTINCT p.id_product) FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product= p.id_product)
            INNER JOIN  `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product=ps.id_product AND sa.id_product_attribute=0 AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
            '.(string)$leftJoin.'
            WHERE p.state=1 AND ps.id_shop = "'.(int)Context::getContext()->shop->id.'" AND ( '.($type_combine_condition =='and'? '1':'0').' '.($filters ? (string)$filters:'').' ) '.($excludeProducts ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',explode(',',$excludeProducts))).')':'');
            return Db::getInstance()->getValue($sql);
        }
        else
        {
            $sql ='SELECT p.id_product,product_lang.name,p.price FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product= p.id_product)
            INNER JOIN  `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product=ps.id_product AND sa.id_product_attribute=0 AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
            LEFT JOIN `'._DB_PREFIX_.'product_lang` product_lang ON (product_lang.id_product=p.id_product AND product_lang.id_lang="'.(int)Context::getContext()->language->id.'" AND product_lang.id_shop="'.(int)Context::getContext()->shop->id.'")
            '.(string)$leftJoin.'
            WHERE p.state=1 AND ps.id_shop = "'.(int)Context::getContext()->shop->id.'" AND ( '.($type_combine_condition =='and'? '1':'0').' '.($filters ? (string)$filters:'').' ) '.($excludeProducts ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',explode(',',$excludeProducts))).')':'').' GROUP BY p.id_product ORDER BY p.id_product'.($limit ? ' LIMIT '.(int)$start.','.(int)$limit:'');
            return Db::getInstance()->executeS($sql);
        }
    }
    public function saveMasseEditProduct($products,$condition_actions,&$log_errros,&$total_products)
    {            
        $conditionActionObjs = array();
        if($products && $condition_actions)
        {
            $product_language_fields = Ets_pmn_massedit::getInstance()->getProductLanguageFields();
            $product_fields = Ets_pmn_massedit::getInstance()->getProductFields();
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $extra_conditions =  array();
            if($condition_actions)
            {
                foreach($condition_actions as $key=> $condition_action)
                {
                    if(!isset($conditionActionObjs[$key]))
                        $conditionActionObjs[$key] = new Ets_pmn_massedit_condition_action($condition_action['id_ets_pmn_massedit_condition_action']);
                    $condition = $conditionActionObjs[$key]->condition;
                    $field = $conditionActionObjs[$key]->field;
                    if(!isset($product_language_fields[$field]) && !isset($product_fields[$field]))
                    {
                        if($field=='tags')
                        {
                            $extra_conditions[$field] = array(
                                'value' => $conditionActionObjs[$key]->value_lang,
                                'condition' => $condition,
                            );
                        }
                        else
                        {
                            $extra_conditions[$field] = array(
                                'value' => json_decode($conditionActionObjs[$key]->value,true),
                                'condition' => $condition,
                            );
                        }
                    }
                    elseif($field=='quantity' || $field=='minimal_quantity' || $field=='location' || $field=='out_of_stock')
                    {
                        $extra_conditions[$field] = array(
                            'value' => $conditionActionObjs[$key]->value,
                            'condition' => $condition,
                        );
                    }    
                }
            }
            foreach($products as $product)
            {
                $id_product = (int)$product['id_product'];
                $errors = array();
                $product_class = new Product($id_product);
                $product_class->unit_price = ($product_class->unit_price_ratio != 0 ? $product_class->price / $product_class->unit_price_ratio : 0);
                $product_old = new Product($id_product);
                $product_class->unit_price = ($product_class->unit_price_ratio != 0 ? $product_class->price / $product_class->unit_price_ratio : 0);
                $save_log = (int)Configuration::get('ETS_PMN_SAVE_EDIT_LOG');
                if(isset($extra_conditions['features']) && $extra_conditions['features']['condition']!='off')
                {
                    if($save_log)
                    {
                        $features = Ets_pmn_massedit_history::getProductFeatures($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name= 'features';
                        $product_history->old_value = $features ? json_encode($features):'';  
                        if($product_history->add())
                        {
                            $this->updateProductFeature($id_product,$extra_conditions['features']);
                            $new_features = Ets_pmn_massedit_history::getProductFeatures($id_product);
                            $product_history->new_value = $new_features ? json_encode($new_features):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup features for product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                        $this->updateProductFeature($id_product,$extra_conditions['features']);
                    
                }
                if(isset($extra_conditions['related_products']) && $extra_conditions['related_products']['condition']!='off')
                {
                    if($save_log)
                    {
                        $related_products = Ets_pmn_massedit_history::getProductRelateds($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='related_products';
                        $product_history->old_value = $related_products ? json_encode($related_products):'';
                        if($product_history->add())
                        {
                            $this->updateProductRelated($id_product,$extra_conditions['related_products']);
                            $new_related_products = Ets_pmn_massedit_history::getProductRelateds($id_product);
                            $product_history->new_value = $new_related_products ? json_encode($new_related_products):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup for related product of product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                        $this->updateProductRelated($id_product,$extra_conditions['related_products']);
                    
                }
                if(isset($extra_conditions['selectedCarriers']) && $extra_conditions['selectedCarriers']['condition']!='off')
                {
                    if($save_log)
                    {
                        $carriers = Ets_pmn_massedit_history::getProductCarriers($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='selectedCarriers';
                        $product_history->old_value = $carriers ? json_encode($carriers):'';
                        if($product_history->add())
                        {
                            $this->updateProductCarrier($id_product,$extra_conditions['selectedCarriers']);
                            $new_carriers = Ets_pmn_massedit_history::getProductCarriers($id_product);
                            $product_history->new_value = $new_carriers ? json_encode($new_carriers):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup carrier for product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                    {
                        $this->updateProductCarrier($id_product,$extra_conditions['selectedCarriers']);
                    }
                    
                }
                if(isset($extra_conditions['tags']) && $extra_conditions['tags']['condition']!='off')
                {
                    if($save_log)
                    {
                        $tags = Ets_pmn_massedit_history::getProductTags($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='tags';
                        $product_history->old_value = $tags ? json_encode($tags):'';
                        if($product_history->add())
                        {
                            $this->updateProducttags($id_product,$extra_conditions['tags']);
                            $new_tags = Ets_pmn_massedit_history::getProductTags($id_product);
                            $product_history->new_value = $new_tags ? json_encode($new_tags):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup tags for product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                        $this->updateProducttags($id_product,$extra_conditions['tags']);
                }
                if(isset($extra_conditions['customization']) && $extra_conditions['customization']['condition']!='off')
                {
                    if($save_log)
                    {
                        $custommizations = Ets_pmn_massedit_history::getProductCustomizationField($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='customization';
                        $product_history->old_value = $custommizations ? json_encode($custommizations):'';
                        if($product_history->add())
                        {
                            $this->updateCustomization($id_product,$extra_conditions['customization']);
                            $new_custommizations = Ets_pmn_massedit_history::getProductCustomizationField($id_product);
                            $product_history->new_value = $new_custommizations ? json_encode($new_custommizations):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup for customization of product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                        $this->updateCustomization($id_product,$extra_conditions['customization']);
                    
                }
                if(isset($extra_conditions['specific_prices']) && $extra_conditions['specific_prices']['condition']!='off')
                {
                    if($save_log)
                    {
                        $specific_prices = Ets_pmn_massedit_history::getProductSpecificPrices($id_product);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $id_product;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='specific_prices';
                        $product_history->old_value = $specific_prices ? json_encode($specific_prices):'';
                        if($product_history->add())
                        {
                            $this->updateSpecificPrices($id_product,$extra_conditions['specific_prices']);
                            $new_specific_prices = Ets_pmn_massedit_history::getProductSpecificPrices($id_product);
                            $product_history->new_value = $new_specific_prices ? json_encode($new_specific_prices):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                            $errors[] = sprintf($this->l('Cannot create backup for specific price of product (%d)'),$id_product);
                        unset($product_history);
                    }
                    else
                        $this->updateSpecificPrices($id_product,$extra_conditions['specific_prices']);
                    
                }
                if(isset($extra_conditions['combinations']) || isset($extra_conditions['quantity']) || isset($extra_conditions['minimal_quantity']) || isset($extra_conditions['location']) || isset($extra_conditions['out_of_stock']))
                {
                    if($save_log)
                    {
                        $stocks = Ets_pmn_massedit_history::getProductStockAvailables($product_class->id);
                        $product_history = new Ets_pmn_massedit_history_product();
                        $product_history->id_product = $product_class->id;
                        $product_history->id_ets_pmn_massedit_history = $this->id;
                        $product_history->field_name='stocks';
                        $product_history->old_value = $stocks ? json_encode($stocks):'';
                        if($product_history->add())
                        {
                            if(isset($extra_conditions['combinations']) && $extra_conditions['combinations']['condition']!='off')
                            {
                                $productAttributes = Ets_pmn_massedit_history::getProductAttributes($product_class->id);
                                $attribute_history = new Ets_pmn_massedit_history_product();
                                $attribute_history->id_product = $product_class->id;
                                $attribute_history->id_ets_pmn_massedit_history = $this->id;
                                $attribute_history->field_name='combinations';
                                $attribute_history->old_value = $productAttributes ? json_encode($productAttributes):'';
                                if($attribute_history->add())
                                {
                                    $this->updateCombinations($product_class,$extra_conditions['combinations']);
                                    $new_productAttributes = Ets_pmn_massedit_history::getProductAttributes($product_class->id);
                                    $attribute_history->new_value = $new_productAttributes ? json_encode($new_productAttributes):'';
                                    if($attribute_history->new_value!=$attribute_history->old_value)
                                        $attribute_history->update();
                                    else
                                        $attribute_history->delete();
                                }
                                else
                                    $errors[] = sprintf($this->l('Cannot create combinations for product (%d)'),$id_product);
                                unset($attribute_history);
                            }
                            if(isset($extra_conditions['quantity']) && $extra_conditions['quantity']['condition']!='off')
                            {
                                $this->updateProductquantity($product_class,$extra_conditions['quantity']['condition'],$extra_conditions['quantity']['value']);
                            }
                            if(isset($extra_conditions['minimal_quantity']) && $extra_conditions['minimal_quantity']['condition']!='off')
                            {
                                $this->updateProductMinimalQuantity($product_class,$extra_conditions['minimal_quantity']['condition'],$extra_conditions['minimal_quantity']['value']);
                            }
                            if(isset($extra_conditions['location']) && $extra_conditions['location']['condition']!='off')
                            {
                                $this->updateProductLocations($product_class,$extra_conditions['location']['condition'],$extra_conditions['location']['value']);
                            }
                            if(isset($extra_conditions['out_of_stock']) && $extra_conditions['out_of_stock']['condition']!='off')
                            {
                                $this->updateProductOutOfStocks($product_class,$extra_conditions['out_of_stock']['condition'],$extra_conditions['out_of_stock']['value']);
                            }
                            $new_stocks = Ets_pmn_massedit_history::getProductStockAvailables($product_class->id);
                            $product_history->new_value = $new_stocks ? json_encode($new_stocks):'';
                            if($product_history->new_value!=$product_history->old_value)
                                $product_history->update();
                            else
                                $product_history->delete();
                        }
                        else
                        {
                            $errors[] = sprintf($this->l('Cannot create backup for available stock of product (#%d)'),$id_product);
                        }
                        unset($product_history);
                    }
                    else
                    {
                        if(isset($extra_conditions['combinations']) && $extra_conditions['combinations']['condition']!='off')
                        {
                            $this->updateCombinations($product_class,$extra_conditions['combinations']);
                        }
                        if(isset($extra_conditions['quantity']) && $extra_conditions['quantity']['condition']!='off')
                        {
                            $this->updateProductquantity($product_class,$extra_conditions['quantity']['condition'],$extra_conditions['quantity']['value']);
                        }
                        if(isset($extra_conditions['minimal_quantity']) && $extra_conditions['minimal_quantity']['condition']!='off')
                        {
                            $this->updateProductMinimalQuantity($product_class,$extra_conditions['minimal_quantity']['condition'],$extra_conditions['minimal_quantity']['value']);
                        }
                        if(isset($extra_conditions['location']) && $extra_conditions['location']['condition']!='off')
                        {
                            $this->updateProductLocations($product_class,$extra_conditions['location']['condition'],$extra_conditions['location']['value']);
                        }
                        if(isset($extra_conditions['out_of_stock']) && $extra_conditions['out_of_stock']['condition']!='off')
                        {
                            $this->updateProductOutOfStocks($product_class,$extra_conditions['out_of_stock']['condition'],$extra_conditions['out_of_stock']['value']);
                        }
                    }
                    
                }
                if($condition_actions)
                {
                    foreach($condition_actions as $key=> $condition_action)
                    {
                        if(!isset($conditionActionObjs[$key]))
                            $conditionActionObjs[$key] = new Ets_pmn_massedit_condition_action($condition_action['id_ets_pmn_massedit_condition_action']);
                        $condition = $conditionActionObjs[$key]->condition;
                        $field = $conditionActionObjs[$key]->field;
                        if($condition!='off')
                        {
                            if(isset($product_language_fields[$field]))
                            {
                                $validate = $product_language_fields[$field]['validate'];
                                foreach($languages as $language)
                                {
                                    $id_lang = $language['id_lang'];
                                    if($conditionActionObjs[$key]->value_lang[$id_lang])
                                    {
                                        if($condition=='append_before')
                                            $product_class->{$field}[$id_lang] = $conditionActionObjs[$key]->value_lang[$id_lang].$product_class->{$field}[$id_lang];
                                        elseif($condition=='append_after')
                                            $product_class->{$field}[$id_lang] .= $conditionActionObjs[$key]->value_lang[$id_lang];
                                        elseif($condition=='replace')
                                            $product_class->{$field}[$id_lang] = $conditionActionObjs[$key]->value_lang[$id_lang] ? :$conditionActionObjs[$key]->value_lang[$id_lang_default];
                                        $size = isset($product_language_fields[$field]['size']) ? (int)$product_language_fields[$field]['size']:0;
                                        if($field=='name')
                                            $product_class->name[$id_lang] = $this->replaceNameShortCode($product_class->name[$id_lang],$product_old,$id_lang);
                                        elseif($field=='meta_title')
                                            $product_class->meta_title[$id_lang] = $this->replaceNameShortCode($product_class->meta_title[$id_lang],$product_old,$id_lang);
                                        elseif($field=='meta_description')
                                            $product_class->meta_description[$id_lang] = $this->replaceNameShortCode($product_class->meta_description[$id_lang],$product_old,$id_lang);
                                        elseif($field=='description')
                                            $product_class->description[$id_lang] = $this->replaceDescriptionShortCode($product_class->description[$id_lang],$product_old,$id_lang);
                                        elseif($field=='description_short')
                                            $product_class->description_short[$id_lang] = $this->replaceSummaryShortCode($product_class->description_short[$id_lang],$product_old,$id_lang);
                                        if(($val = $product_class->{$field}[$id_lang]))
                                        {
                                            if(method_exists('Validate',$validate) && !Validate::{$validate}($val,true))
                                                $errors[] = sprintf($this->l('%s%s%s of product (#%d) is not valid in language %s'),'"',$product_language_fields[$field]['title'],'"',$id_product,$language['iso_code']);
                                            elseif($size && Tools::strlen(strip_tags($val)) > $size)
                                            {
                                                $errors[] = sprintf($this->l('%s%s%s of product (#%d) is too long in language %s. It should have %s characters or less'),'"',$product_language_fields[$field]['title'],'"',$id_product,$language['iso_code'],$size);
                                            }
                                        }
                                    }
                                    
                                }
                            }
                            elseif(isset($product_fields[$field]) && $field!='minimal_quantity' && $field!='quantity' && $field!='location' && $field!='out_of_stock')
                            {
                                $validate = $product_fields[$field]['validate'];
                                $value = $conditionActionObjs[$key]->value;
                                if($field=='price' || $field=='unit_price' || $field=='wholesale_price')
                                    $value = $this->replacePriceShortCode($value,$product_old);
                                if($condition=='append_before')
                                    $product_class->{$field} = $value . $product_class->{$field};
                                elseif($condition =='append_after')
                                    $product_class->{$field} .= $value;
                                elseif($condition=='replace')
                                    $product_class->{$field} = $validate=='isPrice' || $validate=='isUnsignedFloat' ? (float) $value : ($validate=='isUnsignedId' || $validate=='isUnsignedInt' ? (int)$value :$value);
                                elseif($condition=='plus_percent')
                                {
                                    if(Validate::isPrice($value))
                                        $product_class->{$field} = Tools::ps_round((($value ? :0)*$product_class->{$field})/100 + $product_class->{$field},6);
                                    else
                                        $errors[] = sprintf($this->l('%s%s%s is not valid'),'"',$product_fields[$field]['title'],'"');
                                }
                                     
                                elseif($condition=='minus_percent')
                                {
                                    if(Validate::isPrice($value))
                                        $product_class->{$field} =   Tools::ps_round($product_class->{$field} -(($value ? :0)*$product_class->{$field})/100,6);
                                    else
                                        $errors[] = sprintf($this->l('%s%s%s is not valid'),'"',$product_fields[$field]['title'],'"');
                                }
                                elseif($condition=='plus_amount')
                                {
                                    if(Validate::isPrice($value))
                                        $product_class->{$field} = $product_class->{$field} + ($value ? :0);
                                    else
                                        $errors[] = sprintf($this->l('%s%s%s is not valid'),'"',$product_fields[$field]['title'],'"');
                                }
                                elseif($condition=='minus_amount')
                                {
                                    if(Validate::isPrice($value))
                                        $product_class->{$field} = $product_class->{$field} - ($value ? :0);
                                    else
                                        $errors[] = sprintf($this->l('%s%s%s is not valid'),'"',$product_fields[$field]['title'],'"');
                                }  
                                elseif($condition=='active_all')
                                    $product_class->{$field} =1;
                                elseif($condition=='disable_all')
                                    $product_class->{$field} =0;
                                elseif($condition=='remove' && ($field=='id_manufacturer' || $field=='id_tax_rules_group') && $product_class->{$field}==(int)$value)
                                {
                                    $product_class->{$field} = 0;
                                }
                                elseif($condition =='remove_all' && ($field=='id_manufacturer' || $field=='id_tax_rules_group'))
                                {
                                    $product_class->{$field} = 0;
                                }
                                $size = isset($product_fields[$field]['size']) ? (int)$product_fields[$field]['size']:0;  
                                  
                                if(($val = $product_class->{$field}))
                                {
                                    if(method_exists('Validate',$validate) && !Validate::{$validate}($val))
                                        $errors[] = sprintf($this->l('%s%s%s of product (#%d) is not valid'),'"',$product_fields[$field]['title'],'"',$id_product);
                                    elseif($size && $size < Tools::strlen($val))
                                        $errors[] = sprintf($this->l('%s%s%s of product (#%d) is too long. It should have %s characters or less'),'"',$product_fields[$field]['title'],'"',$id_product,$size);
                                }
                            }
                        }
                    }
                }
                if(!$product_class->name[$id_lang_default])
                {
                    $errors[] = sprintf($this->l('Name of product (#%d) is required.'),$product_class->id);
                }
                if(!$product_class->id_category_default)
                {
                    $errors[] = sprintf($this->l('Default category of product (#%d) is required.'),$product_class->id);
                }
                if(!$errors)
                {
                    if($condition_actions && $save_log)
                    {
                        foreach($condition_actions as $key=> $condition_action)
                        {
                            if(!isset($conditionActionObjs[$key]))
                                $conditionActionObjs[$key] = new Ets_pmn_massedit_condition_action($condition_action['id_ets_pmn_massedit_condition_action']);
                            $condition = $conditionActionObjs[$key]->condition;
                            $field = $conditionActionObjs[$key]->field;
                            if($condition!='off' && $condition!='remove_all')
                            {
                                if(isset($product_language_fields[$field]))
                                {
                                    foreach($languages as $language)
                                    {
                                        $id_lang = $language['id_lang'];
                                        if($conditionActionObjs[$key]->value_lang[$id_lang] )
                                        {
                                            $product_history = new Ets_pmn_massedit_history_product();
                                            $product_history->id_product = $product_class->id;
                                            $product_history->id_ets_pmn_massedit_history = $this->id;
                                            $product_history->field_name=$field;
                                            $product_history->old_value = $product_old->{$field}[$id_lang];
                                            $product_history->new_value = $product_class->{$field}[$id_lang];
                                            $product_history->id_lang = $id_lang;
                                            if($product_history->old_value != $product_history->new_value && !$product_history->add())
                                                $errors[] = sprintf($this->l('Cannot create backup %s for product (#%d)'),$product_language_fields[$field]['title'],$id_product);
                                            unset($product_history);
                                        }
                                        
                                    }
                                }
                                elseif(isset($product_fields[$field]) && $field!='quantity' && $field!='location' && $field!='out_of_stock')
                                {
                                    $product_history = new Ets_pmn_massedit_history_product();
                                    $product_history->id_product = $product_class->id;
                                    $product_history->id_ets_pmn_massedit_history = $this->id;
                                    $product_history->field_name=$field;
                                    $product_history->old_value = $product_old->{$field};
                                    $product_history->new_value = $product_class->{$field};
                                    if($product_history->old_value != $product_history->new_value && !$product_history->add())
                                        $errors[] = sprintf($this->l('Cannot create backup %s for product (#%d)'),$product_fields[$field]['title'],$id_product);
                                    unset($product_history);
                                }
                            }
                        }
                    }
                    if(!$errors)
                    {
                        $product_class->unit_price_ratio = $product_class->unit_price ? $product_class->price/$product_class->unit_price:0;
                        if(!$product_class->update())
                        {
                            $log_errros[] = sprintf($this->l('An error occurred while saving the product ($%d)'),$product_class->id);
                        }
                        else
                        {
                            if(isset($extra_conditions['id_categories']) && $extra_conditions['id_categories']['condition']!='off')
                            {
                                if($save_log)
                                {
                                    $categories = Ets_pmn_massedit_history::getProductCategories($id_product);
                                    $product_history = new Ets_pmn_massedit_history_product();
                                    $product_history->id_product = $id_product;
                                    $product_history->id_ets_pmn_massedit_history = $this->id;
                                    $product_history->field_name='id_categories';
                                    $product_history->old_value = $categories ? json_encode($categories):'';
                                    if($product_history->add())
                                    {
                                        $this->updateProductCategories($product_class,$extra_conditions['id_categories']);
                                        $new_categories = Ets_pmn_massedit_history::getProductCategories($id_product);
                                        $product_history->new_value = $new_categories ? json_encode($new_categories):'';
                                        if($product_history->new_value!=$product_history->old_value)
                                            $product_history->update();
                                        else
                                            $product_history->delete();
                                        
                                    }
                                    else
                                        $errors[] = sprintf($this->l('Cannot create backup category for product (%d)'),$id_product);
                                    unset($product_history);
                                }
                                else
                                    $this->updateProductCategories($product_class,$extra_conditions['id_categories']);
                            }
                            $total_products++;                                                    
                            if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'category_product` WHERE id_category="'.(int)$product_class->id_category_default.'" AND id_product="'.(int)$product_class->id.'"'))
                            {
                                $position = 1+ (int)Db::getInstance()->getValue('SELECT MAX(position) FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$product_class->id);
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product`(id_product,id_category,position) VALUES("'.(int)$product_class->id.'","'.(int)$product_class->id_category_default.'","'.(int)$position.'")');
                            }
                        }
                    }
                    else
                        $log_errros = array_merge($log_errros,$errors);
                    
                }
                else
                {
                    $log_errros = array_merge($log_errros,$errors);
                }
            }
        }
        elseif(!$products)
            $log_errros[] = $this->l('Cannot find any products that satisfy your condition');
        elseif(!$condition_actions)
            $log_errros[] = $this->l('You have not selected any product fields');
        
    }
    public function displayLogNotAvalible()
    {
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/no_avalible.tpl');
    }
    public function updateProductMinimalQuantity($product,$condition,$quantity)
    {
        Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'product_shop` SET minimal_quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'minimal_quantity + '.(int)$quantity: 'minimal_quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'" AND id_shop="'.(int)Context::getContext()->shop->id.'"');
        Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'product` SET minimal_quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'minimal_quantity + '.(int)$quantity: 'minimal_quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'"');
        Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'product_attribute` SET minimal_quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'minimal_quantity + '.(int)$quantity: 'minimal_quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'"');
        Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'product_attribute_shop` SET minimal_quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'minimal_quantity + '.(int)$quantity: 'minimal_quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'" AND id_shop="'.(int)Context::getContext()->shop->id.'"');
        return true;
    }
    public function updateProductquantity($product,$condition,$quantity)
    {
        $id_shop = Context::getContext()->shop->id;
        if(Product::getAttributesInformationsByProduct($product->id))
        {
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'stock_available` SET quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'quantity + '.(int)$quantity: 'quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'" AND id_product_attribute<>0 AND id_shop="'.(int)$id_shop.'"');
            $total_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
    			SELECT SUM(quantity) as quantity
    			FROM  `' . _DB_PREFIX_ . 'stock_available` WHERE id_product = ' . (int) $product->id . '
    			AND id_product_attribute <> 0 ' .
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'stock_available` SET quantity = '.(int)$total_quantity.' WHERE id_product="'.(int)$product->id.'" AND id_product_attribute=0 AND id_shop="'.(int)$id_shop.'"');
        }
        else
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'stock_available` SET quantity = '.($condition=='replace' ? (int)$quantity :($condition=='plus_amount' ? 'quantity + '.(int)$quantity: 'quantity-'.(int)$quantity)).' WHERE id_product="'.(int)$product->id.'" AND id_shop="'.(int)$id_shop.'"');
        return true;
    }
    public function updateProductLocations($product,$condition,$location)
    {
        $id_shop=Context::getContext()->shop->id;
        if(Product::getAttributesInformationsByProduct($product->id))
        {
            $product_attributes = Db::getInstance()->executeS('SELECT id_product_attribute FROM  `'._DB_PREFIX_.'product_attribute` WHERE id_product="'.(int)$product->id.'" AND id_product_attribute<>0');
            if($product_attributes)
            {
                foreach($product_attributes as $product_attribute)
                {
                    $id_product_attribute = (int)$product_attribute['id_product_attribute'];
                    $location_old = Ets_pmn_defines::getLocation($product->id,$id_product_attribute,$id_shop);
                    if(!$location_old || $condition=='replace')
                        Ets_pmn_defines::setLocation($product->id,$location,$id_shop,$id_product_attribute);
                    else
                    {
                        if($condition=='append_before')
                            $location = $location.$location_old;
                        else
                            $condition =$location_old.$location;
                        if(Validate::isString($location))
                        {
                            Ets_pmn_defines::setLocation($product->id,$location,$id_shop,$id_product_attribute);
                        }
                    }
                }
            }
        }
        else
        {
            $location_old = Ets_pmn_defines::getLocation($product->id,0,$id_shop);
            if(!$location_old || $condition=='replace')
                Ets_pmn_defines::setLocation($product->id,$location,$id_shop,0);
            else
            {
                if($condition=='append_before')
                    $location = $location.$location_old;
                else
                    $condition =$location_old.$location;
                if(Validate::isString($location))
                {
                    Ets_pmn_defines::setLocation($product->id,$location,$id_shop,0);
                }
            }
        }
        return true;
    }
    public function updateProductOutOfStocks($product,$condition,$out_of_stock)
    {
        if($condition=='replace')
            StockAvailable::setProductOutOfStock($product->id,$out_of_stock);
        return true;
    }
    public function updateProductCategories($product,$condition_field)
    {
        $condition = $condition_field['condition'];
        $id_categories = $condition_field['value'];
        if($condition=='remove_all' || $condition=='replace')
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'category_product` WHERE id_product="'.(int)$product->id.'" AND id_category !='.(int)$product->id_category_default);
        }
        if($condition=='remove')
        {
            if($id_categories && Ets_productmanager::validateArray($id_categories,'isInt'))
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$product->id.' AND id_category!='.(int)$product->id_category_default.' AND id_category IN ('.implode(',',array_map('intval',$id_categories)).')');
            }
        }
        if($condition=='add' || $condition=='replace')
        {
            if($id_categories && Ets_productmanager::validateArray($id_categories,'isInt'))
            {
                $position = (int)Db::getInstance()->getValue('SELECT MAX(position) FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$product->id);
                foreach($id_categories as $id_category)
                {
                    $position++;
                    if($id_category && Validate::isLoadedObject( new Category($id_category)) && !Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'category_product` WHERE id_category="'.(int)$id_category.'" AND id_product='.(int)$product->id))
                    {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product`(id_product,id_category,position) VALUES("'.(int)$product->id.'","'.(int)$id_category.'","'.(int)$position.'")');
                    }
                }
            }
        }
    }
    public function updateProductCarrier($id_product,$condition_field)
    {
        $condition = $condition_field['condition'];
        $selectedCarriers = $condition_field['value'];
        $id_shop = Context::getContext()->shop->id;
        if($condition=='remove_all' || $condition=='replace')
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$id_product.' AND id_shop='.(int)$id_shop);
        }
        if($condition=='remove')
        {
            if($selectedCarriers && Ets_productmanager::validateArray($selectedCarriers,'isInt'))
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$id_product.' AND id_carrier_reference IN ('.implode(',',array_map('intval',$selectedCarriers)).') AND id_shop='.(int)$id_shop);
            }
        }
        if($condition=='add' || $condition=='replace')
        {
            if($selectedCarriers && Ets_productmanager::validateArray($selectedCarriers,'isInt'))
            {
                foreach($selectedCarriers as $id_reference)
                {
                    if($id_reference && Carrier::getCarrierByReference($id_reference) && !Db::getInstance()->getValue('SELECT id_carrier_reference FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product="'.(int)$id_product.'" AND id_carrier_reference='.(int)$id_reference.' AND id_shop='.(int)$id_shop))
                    {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_carrier`(id_product,id_carrier_reference,id_shop) VALUES("'.(int)$id_product.'","'.(int)$id_reference.'","'.(int)$id_shop.'")');
                    }
                }
            }
        }
    }
    public function updateProducttags($id_product,$condition_field)
    {
        $condition = $condition_field['condition'];
        $value = $condition_field['value'];
        $languages = Language::getLanguages(false);
        if($languages)
        {
            foreach($languages as $language)
            {
                $id_lang = (int)$language['id_lang'];
                $tags = isset($value[$id_lang]) ? $value[$id_lang]:'';
                if($condition=='remove_all' || $condition=='replace')
                {
                    Ets_pmn_defines::getInstance()->deleteProducttag($id_product,$id_lang);
                }
                if($tags && $condition!='remove_all')
                {
                    $tagList = explode(',',$tags);
                    $list = array();
                    foreach ($tagList as $tag) {
                        if (!Validate::isGenericName($tag)) {
                            return false;
                        }
                        $tag = trim(Tools::substr($tag, 0, Tag::$definition['fields']['name']['size']));
                        $tagObj = new Tag(null, $tag, (int) $id_lang);
                        if(Validate::isLoadedObject($tagObj) && $condition=='remove')
                        {
                            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product="'.(int)$id_product.'" AND id_tag="'.(int)$tagObj->id.'" AND id_lang="'.(int)$id_lang.'"');
                        }
                        if($condition=='replace' || $condition=='add')
                        {
                            /* Tag does not exist in database */
                            if (!Validate::isLoadedObject($tagObj)) {
                                $tagObj->name = $tag;
                                $tagObj->id_lang = (int) $id_lang;
                                $tagObj->add();
                            }
                            if (!in_array($tagObj->id, $list)) {
                                $list[] = $tagObj->id;
                            }
                        }
                    }
                    if($list)
                    {
                        foreach($list as $id_tag)
                        {
                            if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product="'.(int)$id_product.'" AND id_tag="'.(int)$id_tag.'" AND id_lang="'.(int)$id_lang.'"'))
                            {
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_tag` (id_product,id_tag,id_lang) VALUES("'.(int)$id_product.'","'.(int)$id_tag.'","'.(int)$id_lang.'")');
                            }
                        }
                    }
                }
            }
        }
    }
    public function updateSpecificPrices($id_product,$condition_field)
    {
        $condition = $condition_field['condition'];
        if($condition=='remove_all' || $condition=='replace')
        {
            $specifics = Db::getInstance()->executeS('SELECT id_specific_price FROM  `'._DB_PREFIX_.'specific_price` WHERE id_product='.(int)$id_product.' AND (id_shop='.(int)Context::getContext()->shop->id.' OR id_shop=0)');
            if($specifics)
            {
                foreach($specifics as $specific)
                {
                    $obj = new SpecificPrice($specific['id_specific_price']);
                    $obj->delete();
                }
            }
        }
        if($condition=='replace' || $condition=='add')
        {
            if(($specific_prices = $condition_field['value']) && Ets_productmanager::validateArray($specific_prices))
            {
                $sql ='SELECT id_specific_price FROM `'._DB_PREFIX_.'specific_price` 
                WHERE `id_product`="'.(int)$id_product.'" 
                AND `id_currency`="'.(int)$specific_prices['id_currency'].'" 
                AND `id_group`="'.(int)$specific_prices['id_group'].'" 
                AND `id_country`="'.(int)$specific_prices['id_country'].'" 
                AND `id_product_attribute`= "0"
                AND `id_customer`= "'.(int)$specific_prices['id_customer'].'" 
                AND `from` = "'.($specific_prices['from'] ? pSQL($specific_prices['from']):'0000-00-00 00:00:00' ).'"
                AND `to`= "'.($specific_prices['to'] ? pSQL($specific_prices['to']):'0000-00-00 00:00:00' ).'"
                AND `from_quantity`="'.(int)$specific_prices['from_quantity'].'"
                AND `id_shop` = "'.(int)Context::getContext()->shop->id.'"';
                if($id_specific_price = (int)Db::getInstance()->getValue($sql))
                {
                    $specificPrice = new SpecificPrice($id_specific_price);
                }
                else
                {
                    $specificPrice = new SpecificPrice();
                }
                $specificPrice->id_product = $id_product;
                $specificPrice->id_product_attribute = 0;
                $specificPrice->id_currency = (int)$specific_prices['id_currency'];
                $specificPrice->id_country = (int)$specific_prices['id_country'];
                $specificPrice->id_group = (int)$specific_prices['id_group'];
                $specificPrice->id_customer = (int)$specific_prices['id_customer'];
                $specificPrice->from_quantity = (int)$specific_prices['from_quantity'];
                $specificPrice->from = $specific_prices['from'] ? : '0000-00-00 00:00:00';
                $specificPrice->to = $specific_prices['to'] ? : '0000-00-00 00:00:00';
                $specificPrice->id_shop = Context::getContext()->shop->id;
                if(isset($specific_prices['leave_bprice']) && $specific_prices['leave_bprice'])
                    $specificPrice->price=-1;
                else
                    $specificPrice->price = isset($specific_prices['product_price']) ? (float)$specific_prices['product_price']:0;
                $specificPrice->reduction_type= $specific_prices['sp_reduction_type'];
                $specific_price_sp_reduction = $specific_prices['sp_reduction'];
                if($specificPrice->reduction_type=='amount')
                    $specificPrice->reduction = (float)$specific_price_sp_reduction;
                else
                    $specificPrice->reduction = (float)$specific_price_sp_reduction/100;
                $specificPrice->reduction_tax = (int)$specific_prices['sp_reduction_tax'];
                if($specificPrice->id)
                {
                    $specificPrice->update();
                }
                else
                {
                    $specificPrice->add();
                }
            }
        }
    }
    public function updateCustomization($id_product,$condition_field)
    {
        $condition = $condition_field['condition'];
        if($condition=='remove_all' || $condition=='replace')
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product="'.(int)$id_product.'"');
        if($condition=='add' || $condition=='replace')
        {
            if(($custom_fields = $condition_field['value']) && Ets_productmanager::validateArray($custom_fields))
            {
                Configuration::updateValue('PS_CUSTOMIZATION_FEATURE_ACTIVE',1);
                $languages = Language::getLanguages(false);
                $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                foreach($custom_fields as $custom_field)
                {
                    $errors = array();
                    if(!$custom_field['label'][$id_lang_default])
                    {
                        $errors[] = $this->l('Customization label is required');
                        break;
                    }
                    if(isset($custom_field['type']) && !Validate::isUnsignedInt($custom_field['type']))
                    {
                        $errors[] = $this->l('Customization type is not valid');
                        break;
                    }
                    foreach($languages as $language)
                    {
                        if($custom_field['label'][$language['id_lang']] && !Validate::isCleanHtml($custom_field['label'][$language['id_lang']]))
                            $errors[] = sprintf($this->l('Customization label is not valid in %s'),$language['iso_code']);
                    }
                    if(!$errors)
                    {
                        $customizationField = new CustomizationField();
                        $customizationField->id_product=  $id_product;
                        foreach($languages as $language)
                        {
                            $customizationField->name[$language['id_lang']] = $custom_field['label'][$language['id_lang']] ? : $custom_field['label'][$id_lang_default];
                        }
                        $customizationField->type = (int)$custom_field['type'];
                        if(isset($custom_field['require']))
                        {
                            $customizationField->required = (int)$custom_field['require'];
                        }
                        else    
                        {
                            $customizationField->required = 0;
                        }
                        $customizationField->add();
                    }
                }
            }
        }
    }
    public function updateCombinations($product,$condition_field)
    {
        $condition = $condition_field['condition'];
        $product_type = $product->getType();
        if($product_type== Product::PTYPE_SIMPLE)
        {
            if($condition=='remove_all' || $condition=='replace')
                $product->deleteProductAttributes();
            $attribute_options = $condition_field['value'];
            if($condition!='remove_all' && $attribute_options && Ets_productmanager::validateArray($attribute_options,'isInt'))
            {
                $tab = array_values($attribute_options);
                $attributes = Product::getAttributesInformationsByProduct($product->id);
                if (count($tab) && Validate::isLoadedObject($product)) {
                    $combinations = array_values(Ets_productmanager::createCombinations($tab));
                    $id_products = array();
                    if($combinations)
                    {
                        for($i=0;$i<count($combinations);$i++)
                        {
                            $id_products[] = $product->id;
                        }
                    }
                    $values = array_values(array_map(array($this, 'addAttribute'), $combinations,$id_products));
                    if($condition=='add' || $condition=='replace')
                    {
                        SpecificPriceRule::disableAnyApplication();
                        $product->generateMultipleCombinations($values, $combinations,false);
                        Product::getDefaultAttribute($product->id, 0, true);
                        Product::updateDefaultAttribute($product->id);
                        StockAvailable::synchronize($product->id);
                        SpecificPriceRule::enableAnyApplication();
                        SpecificPriceRule::applyAllRules(array((int)$product->id));
                        if(empty($attributes))
                            StockAvailable::setQuantity($product->id,0,0);
                    }
                    elseif($condition=='remove')
                    {
                        foreach(array_keys($values) as $key)
                        {
                            if(isset($combinations[$key]))
                            {
                                $id_combination = (int) $product->productAttributeExists($combinations[$key], false, null, true, true);
                                if($id_combination)
                                {
                                    $combination_obj = new Combination($id_combination);
                                    $combination_obj->delete();
                                }
                            }
                            
                        }
                    }
                    Hook::exec('actionProductUpdate',array('product' => $product,'id_product'=>$product->id));
                }
            }
            elseif($condition=='remove_all')
            {
                $product->cache_default_attribute=0;
                $product->update();
            }
        }
        
    }
    protected function addAttribute($attributes, $id_product)
    {
        if ($id_product) {
            return array(
                'id_product' => (int)$id_product,
                'price' => 0,
                'weight' => 0,
                'ecotax' => 0,
                'quantity' => 0,
                'reference' => '',
                'default_on' => 0,
                'available_date' => '0000-00-00'
            );
        }
        unset($attributes);
        return array();
    }
    public function updateProductRelated($id_product,$condition_field)
    {
        $condition= $condition_field['condition'];
        $related_products = $condition_field['value'];
        if($condition=='remove_all' || $condition=='replace')
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1='.(int)$id_product);
        }
        if($condition=='remove')
        {
            if($related_products && Ets_productmanager::validateArray($related_products,'isInt'))
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2 IN ('.implode(',',array_map('intval',$related_products)).')');
            }
        }
        if($condition=='add' || $condition=='replace')
        {
            if($related_products && Ets_productmanager::validateArray($related_products,'isInt'))
            {
                foreach($related_products as $related_product)
                {
                    if( $related_product!=$id_product && Validate::isLoadedObject(new Product($related_product)) &&  !Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2='.(int)$related_product))
                    {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'accessory`(id_product_1,id_product_2) VALUES("'.(int)$id_product.'","'.(int)$related_product.'")');
                    }
                }
            }
        }
    }
    public function updateProductFeature($id_product,$condition_field)
    {
        $condition = $condition_field['condition'];
        $value = $condition_field['value'];
        $id_features  = $value['id_features'];
        $id_feature_values = $value['id_feature_values'];
        $feature_value_custom = $value['feature_value_custom'];
        if($condition=='remove_all' || $condition=='replace')
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product);
        }
        if($condition=='remove')
        {
            if($id_features && Ets_productmanager::validateArray($id_features,'isInt'))
            {
                foreach($id_features as $key=>$id_feature)
                {
                    if($id_feature)
                    {
                        $id_feature_value = isset($id_feature_values[$key]) ? (int)$id_feature_values[$key]:0;
                        Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product.' AND id_feature ="'.(int)$id_feature.'"'.($id_feature_value ? ' AND id_feature_value ='.(int)$id_feature_value:''));

                    }

                }
            } 
        }
        if($condition=='add' || $condition=='replace')
        {
            $languages = Language::getLanguages(false);
            if($id_features && Ets_productmanager::validateArray($id_features,'isInt') && Ets_productmanager::validateArray($id_feature_values,'isInt') && Ets_productmanager::validateArray($feature_value_custom))
            {
                foreach($id_features as $key=> $id_feature)
                {
                    if($id_feature)
                    {
                        if(isset($id_feature_values[$key]) && $id_feature_values[$key])
                        {
                            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'feature_product` WHERE id_product="'.(int)$id_product.'" AND id_feature = "'.(int)$id_feature.'" AND id_feature_value="'.(int)$id_feature_values[$key].'"'))
                            {
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$id_feature_values[$key].'")');
                            }
                        }
                        elseif(isset($feature_value_custom[$key]) && $feature_value_custom[$key])
                        {
                            $feature_value = new FeatureValue();
                            $feature_value->id_feature = $id_feature;
                            $feature_value->custom=1;
                            foreach($languages as $language)
                                $feature_value->value[$language['id_lang']] = $feature_value_custom[$key];
                            if($feature_value->add())
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$feature_value->id.'")');
                        }
                    }
                }
            }
        }
    }
    public static function getProductStockAvailables($id_product)
    {
        $stocks = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'stock_available` WHERE id_product ='.(int)$id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
        if($stocks)
        {
            foreach($stocks as &$stock)
            {
                if($stock['id_product_attribute'])
                    $stock['attribute_name'] = Ets_pmn_defines::getInstance()->getProductAttributeName($stock['id_product_attribute']);
            }
        }
        return $stocks;
    }
    public static function getProductCategories($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$id_product);
    }
    public static function getProductFeatures($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product);
    }
    public static function getProductSupplier($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'product_supplier` WHERE id_product='.(int)$id_product);
    }
    public static function getProductCarriers($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
    } 
    public static function getProductRelateds($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1='.(int)$id_product);
    }
    public static function getProductTags($id_product)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product='.(int)$id_product);
    }
    public static function getCombinationAttributes($id_product_attribute)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'product_attribute_combination` WHERE id_product_attribute='.(int)$id_product_attribute);
    }
    public static function getCombinationImage($id_product_attribute)
    {
        return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$id_product_attribute);
    }
    public static function getProductAttributes($id_product)
    {
        $sql = 'SELECT pa.* FROM  `'._DB_PREFIX_.'product_attribute` pa
        INNER JOIN  `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute=pas.id_product_attribute AND pas.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE pa.id_product ='.(int)$id_product;
        $product_attributes = Db::getInstance()->executeS($sql);
        if($product_attributes)
        {
            foreach($product_attributes as &$product_attribute)
            {
                $product_attribute['attributes'] = self::getCombinationAttributes((int)$product_attribute['id_product_attribute']);
                $product_attribute['images'] = self::getCombinationImage((int)$product_attribute['id_product_attribute']);
                $product_attribute['name'] = Ets_pmn_defines::getInstance()->getProductAttributeName($product_attribute['id_product_attribute']);
            }
        }
        return $product_attributes;
    }
    public static function getProductCustomizationField($id_product)
    {
        $sql = 'SELECT * FROM  `'._DB_PREFIX_.'customization_field` WHERE id_product='.(int)$id_product.' AND is_deleted=0';
        if($customization_fields = Db::getInstance()->executeS($sql))
        {
            foreach($customization_fields as &$customization_field)
            {
                $customization_field['name'] = Db::getInstance()->executeS('SELECT name,id_lang FROM  `'._DB_PREFIX_.'customization_field_lang` WHERE id_shop='.(int)Context::getContext()->shop->id.' AND id_customization_field='.(int)$customization_field['id_customization_field']);
            }
            return $customization_fields;
        }
    }
    public static function getProductSpecificPrices($id_product)
    {
        $sql ='SELECT * FROM  `'._DB_PREFIX_.'specific_price` WHERE id_product ='.(int)$id_product.' AND (id_shop='.(int)Context::getContext()->shop->id.' OR id_shop=0)';
        if($specific_prices = Db::getInstance()->executeS($sql))
        {
            foreach($specific_prices as $specific_price)
            {
                if($specific_price['id_product_attribute'])
                {
                    $specific_price['product_attributes'] = Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_attribute` WHERE id_product_attribute='.(int)$specific_price['id_product_attribute']);
                    $specific_price['attributes'] = self::getCombinationAttributes((int)$specific_price['id_product_attribute']);
                    $specific_price['image_attribute'] = self::getCombinationImage((int)$specific_price['id_product_attribute']);
                    $specific_price['name_attributes'] = Ets_pmn_defines::getInstance()-> getProductAttributeName($specific_price['id_product_attribute']);
                }
            }
            return $specific_prices;
        }
    }
    public function displayProductFeatures($product)
    {
        $id_lang = Context::getContext()->language->id;
        $sql ='SELECT fl.name,fvl.value FROM  `'._DB_PREFIX_.'feature_product` fp
        INNER JOIN  `'._DB_PREFIX_.'feature` f ON (fp.id_feature = f.id_feature)
        INNER JOIN  `'._DB_PREFIX_.'feature_value` fv ON (fv.id_feature_value = fp.id_feature_value)
        LEFT JOIN  `'._DB_PREFIX_.'feature_lang` fl ON (f.id_feature = fl.id_feature AND fl.id_lang="'.(int)$id_lang.'")
        LEFT JOIN  `'._DB_PREFIX_.'feature_value_lang` fvl ON (fvl.id_feature_value=fv.id_feature_value AND fvl.id_lang="'.(int)$id_lang.'")
        WHERE fp.id_product='.(int)$product->id;
        $features = Db::getInstance()->executeS($sql);
        if($features)
        {
            $text ='';
            foreach($features as $feature)
            {
                $text .= Ets_pmn_defines::displayText($feature['name'].': '.$feature['value'],'p');
            }
            return $text;
        }
        
    }
    public function displayProductCombinations($product)
    {
        $sql = 'SELECT * FROM  `'._DB_PREFIX_.'product_attribute` pa
        INNER JOIN  `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute=pas.id_product_attribute AND pas.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE pa.id_product='.(int)$product->id;
        $product_attributes = Db::getInstance()->executeS($sql);
        if($product_attributes)
        {
            $text ='';
            foreach($product_attributes as $product_attribute)
            {
                $text .= Ets_pmn_defines::displayText(Ets_pmn_defines::getInstance()->getProductAttributeName($product_attribute['id_product_attribute']),'p');
            }
            return $text;
        }
    }
    
    public function replaceNameShortCode($text,$product,$id_lang=0)
    {
        if(!$id_lang)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $productInfo = Ets_productmanager::getProductInfo($product->id);
        return str_replace(array('{name}','{price}'),array($product->name[$id_lang],$productInfo['price']),$text);
    }
    public function replaceDescriptionShortCode($text,$product,$id_lang=0)
    {
        if(!$id_lang)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $match=array();
        if(preg_match('#\{summary(?:\:(\d+?))?\}#',$text, $match))
            $text = preg_replace('#\{summary(?:\:(\d+?))?\}#', $this->replace($match,$product->description_short[$id_lang]), $text);
        if(preg_match('#\{description(?:\:(\d+?))?\}#',$text, $match))
            $text = preg_replace('#\{description(?:\:(\d+?))?\}#', $this->replace($match,$product->description[$id_lang]), $text);
        $productInfo = Ets_productmanager::getProductInfo($product->id);
        return str_replace(array('{name}','{price}','{url}','{features}','{combinations}'),array($product->name[$id_lang],$productInfo['price'],Context::getContext()->link->getProductLink($product),$this->displayProductFeatures($product),$this->displayProductCombinations($product)),$text);
        
    }
    public function replaceSummaryShortCode($text,$product,$id_lang=0)
    {
        if(!$id_lang)
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        preg_match('#\{summary(?:\:(\d+?))?\}#',$text, $match);
        $text = preg_replace('#\{summary(?:\:(\d+?))?\}#', $this->replace($match,$product->description_short[$id_lang]), $text);
        $productInfo = Ets_productmanager::getProductInfo($product->id);
        return str_replace(array('{name}','{price}','{url}','{features}','{combinations}'),array($product->name[$id_lang],$productInfo['price'],Context::getContext()->link->getProductLink($product),$this->displayProductFeatures($product),$this->displayProductCombinations($product)),$text);
    }
    public function replacePriceShortCode($text,$product)
    {
        $productInfo = Ets_productmanager::getProductInfo($product->id);
        return str_replace('{price}',$productInfo['price'],$text);
    }
    public function replace($matches,$value)
    {
        if(isset($matches[1]) && $matches[1])
            return Tools::substr($value ? strip_tags($value):'',0,$matches[1]);
        else
            return $value;
    }
    public function getListLogMassedit($filter='',$start=0,$limit=12,$order_by='',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(DISTINCT hp.id_ets_pmn_massedit_history_product)';
        else
            $sql ='SELECT h.id_ets_pmn_massedit_history,m.name,pl.name as product_name,l.name as lang_name,hp.old_value,hp.new_value,hp.field_name,hp.id_ets_pmn_massedit_history_product,hp.date_add,hp.id_product';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_pmn_massedit_history` h
        INNER JOIN  `'._DB_PREFIX_.'ets_pmn_massedit_history_product` hp ON (hp.id_ets_pmn_massedit_history = h.id_ets_pmn_massedit_history)
        LEFT JOIN  `'._DB_PREFIX_.'ets_pmn_massedit` m on (m.id_ets_pmn_massedit=h.id_ets_pmn_massedit)
        LEFT JOIN  `'._DB_PREFIX_.'lang` l ON (hp.id_lang=l.id_lang)
        LEFT JOIN  `'._DB_PREFIX_.'product` p ON (p.id_product=hp.id_product)
        LEFT JOIN  `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product=p.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'" AND pl.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE h.id_shop="'.(int)Context::getContext()->shop->id.'" '.($filter ? (string)$filter:'');
        if(!$total)
        {
            $sql .= ($order_by ? ' ORDER By '.bqSQL($order_by) :'');
            $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
        }
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            return Db::getInstance()->executeS($sql);
        }
    }
    public function displayListCombinations($combinations)
    {
        if($combinations)
        {
            $text ='';
            foreach($combinations as $combination)
            {
                $text .= Ets_pmn_defines::displayText($combination['name'],'span');
            }
            return $text;
        }
    }
    public function displayListStocks($stocks)
    {
        if($stocks)
        {
            $text ='';
            foreach($stocks as $stock)
            {
                if($stock['id_product_attribute'])
                    $val = sprintf($this->l('%s. Quantity: %d'),$stock['attribute_name'],$stock['quantity']);
                else
                    $val = sprintf($this->l('Quantity: %d'),$stock['quantity']);
                $text .= Ets_pmn_defines::displayText($val,'span');
            }
            return $text;
        }
    }
    public function displayListFeatures($features)
    {
        if($features)
        {
            $text ='';
            foreach($features as $feature)
            {
                $id_feature = (int)$feature['id_feature'];
                if(!isset(self::$features[$id_feature]))
                    self::$features[$id_feature] = new Feature($id_feature,Context::getContext()->language->id);
                $id_feature_value = (int)$feature['id_feature_value'];
                if(!isset(self::$feature_values[$id_feature_value]))
                    self::$feature_values[$id_feature_value] = new FeatureValue($id_feature_value,Context::getContext()->language->id);
                if(Validate::isLoadedObject(self::$features[$id_feature]) && Validate::isLoadedObject(self::$feature_values[$id_feature_value]))
                    $text.= Ets_pmn_defines::displayText(self::$features[$id_feature]->name.': '.self::$feature_values[$id_feature_value]->value,'span');
            }
            return $text;
        }
    }
    public function displayListCategories($categories)
    {
        if($categories)
        {
            $text ='';
            foreach($categories as $category)
            {
                if(!isset(self::$categories[$category['id_category']]))
                    self::$categories[$category['id_category']] = new Category($category['id_category'],Context::getContext()->language->id);
                if(Validate::isLoadedObject(self::$categories[$category['id_category']]))
                    $text .= Ets_pmn_defines::displayText(self::$categories[$category['id_category']]->name,'span');
            }
            return $text;
        }
    }
    public function displayListCarriers($carriers)
    {
        if($carriers)
        {
            $text ='';
            foreach($carriers as $carrier)
            {
                $id_carrier_reference = (int)$carrier['id_carrier_reference'];
                if(!isset(self::$carriers[$id_carrier_reference]))
                    self::$carriers[$id_carrier_reference] = Carrier::getCarrierByReference($id_carrier_reference,Context::getContext()->language->id); 
                if(Validate::isLoadedObject(self::$carriers[$id_carrier_reference]))
                    $text .= Ets_pmn_defines::displayText(self::$carriers[$id_carrier_reference]->name,'span');
            }
            return $text;
        }
    }
    public function displayListProducts($products)
    {
        if($products)
        {
            $text ='';
            foreach($products as $product)
            {
                $id_product = (int)$product['id_product_2'];
                if(!isset(self::$products[$id_product]))
                    self::$products[$id_product] = new Product($id_product,false,Context::getContext()->language->id);
                if(Validate::isLoadedObject(self::$products[$id_product]))
                    $text .= Ets_pmn_defines::displayText(self::$products[$id_product]->name,'span');
            }
            return $text;
        }
    }
    public function displayListCustomization($customizations)
    {
        if($customizations)
        {
            $text = '';
            foreach($customizations as $customization)
            {
                $val = sprintf($this->l('Label: %s, Type: %s, Required: %s'),$customization['name'][0]['name'],$customization['type'] ? $this->l('Text'):$this->l('File'),$customization['required'] ? $this->l('Yes'):$this->l('No') );
                $text .= Ets_pmn_defines::displayText($val,'span');
            }
            return $text;
        }
    }
    public function displayListTags($tags)
    {
        if($tags)
        {
            $text ='';
            foreach($tags as $tag)
            {
                $id_tag = (int)$tag['id_tag'];
                if($id_tag && !isset(self::$tags[$id_tag]))
                    self::$tags[$id_tag] = new Tag($id_tag);
                $text .=Ets_pmn_defines::displayText(self::$tags[$id_tag]->name,'span');
            }
            return $text;
        }
    }
    public function displayListSpecificPrices($specific_prices)
    {
        if($specific_prices)
        {
            $text ='';
            foreach($specific_prices as $specific_price)
            {
                if($specific_price['reduction_type']=='amount')
                    $text .= Ets_pmn_defines::displayText('-'.Tools::displayPrice($specific_price['reduction']),'span');
                else
                    $text .= Ets_pmn_defines::displayText('-'.Tools::ps_round($specific_price['reduction']*100,2).'%','span');
            }
            return $text;
        }
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}