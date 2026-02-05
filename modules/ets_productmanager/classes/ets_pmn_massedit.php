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
class Ets_pmn_massedit extends ObjectModel
{
    public static $instance;
    public $id_shop;
    public $name;
    public $excluded; 
    public $status_edit;
    public $type_combine_condition;
    public $deleted;
    public $date_add;
    public static $definition = array(
		'table' => 'ets_pmn_massedit',
		'primary' => 'id_ets_pmn_massedit',
		'multilang' => false,
		'fields' => array(
            'id_shop' => array('type'=>self::TYPE_STRING),
			'name' => array('type' => self::TYPE_STRING),
            'excluded' => array('type' => self::TYPE_STRING),
            'type_combine_condition' => array('type' => self::TYPE_STRING),
            'status_edit' => array('type'=>self::TYPE_INT),
            'deleted' => array('type'=>self::TYPE_INT),
            'date_add' => array('type'=> self::TYPE_DATE),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_pmn_massedit();
        }
        return self::$instance;
    }
    public function save($null_values = false, $auto_date = true)
    {
        if(parent::save($null_values,$auto_date))
        {
             Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_pmn_massedit_condition` SET id_ets_pmn_massedit ="'.(int)$this->id.'" WHERE id_ets_pmn_massedit=1');
             Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` SET id_ets_pmn_massedit ="'.(int)$this->id.'" WHERE id_ets_pmn_massedit=1');
             return true;
        }
        return false;
    }
    public function delete()
    {
        if(parent::delete())
        {
            Db::getInstance()->delete('ets_pmn_massedit_condition','id_ets_pmn_massedit='.(int)$this->id);
            $condition_actions = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` WHERE id_ets_pmn_massedit='.(int)$this->id);
            if($condition_actions)
            {
                foreach($condition_actions as $condition_action)
                {
                    $id = $condition_action['id_ets_pmn_massedit_condition_action'];
                    $conditionActionOjb = new Ets_pmn_massedit_condition_action($id);
                    $conditionActionOjb->delete();
                }
            }
            return true;
        }
        return false;
    }
    public function getListMassedit($filter='',$start=0,$limit=12,$order_by='',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(DISTINCT m.id_ets_pmn_massedit)';
        else
            $sql ='SELECT *';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_pmn_massedit` m
        WHERE m.deleted=0 AND m.id_shop="'.(int)Context::getContext()->shop->id.'" '.($filter ? (string)$filter:'');
        if(!$total)
        {
            $sql .= ($order_by ? ' ORDER By '.bqSQL($order_by) :'');
            if($limit!==false)
                $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
        }
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            return Db::getInstance()->executeS($sql);
        }
    }
    public function getConditions($html=true)
    {
        if($this->id)
        {
            $conditions = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition` WHERE id_ets_pmn_massedit ='.(int)$this->id);
            if($conditions)
            {
                $filtered_field_array = array(
                    Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE,
                    Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES,
                    Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND,
                    Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES,
                    Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR,
                    Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER
                );
                foreach($conditions as &$condition)
                {
                    if(in_array($condition['filtered_field'],$filtered_field_array))
                    {
                        $condition['compared_value'] = $condition['compared_value'] ? array_map('intval',explode(',',$condition['compared_value'])) :array();
                    }
                    if($html)
                    {
                        $condition['row_html'] = $this->renderFormRowMassedit($condition);
                    }
                }
                
            }
            return $conditions;
        }
        return array();
    }
    public function renderFormPreviewMassedit($product_excluded)
    {
        $context = Context::getContext();
        $conditions = $this->getConditions();
        $filter_products = Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition` WHERE id_ets_pmn_massedit='.(int)$this->id);
        $condition_fields = Ets_pmn_massedit_condition::getInstance()->getListFields();
        $condition_operators = Ets_pmn_massedit_condition::getInstance()->getOperators();
        if($filter_products)
        {
            foreach($filter_products as &$filter_product)
            {
                $filtered_field = (int)$filter_product['filtered_field'];
                $operator = $filter_product['operator'];
                $filter_product['filter'] = isset($condition_fields[$filtered_field]) ? $condition_fields[$filtered_field]['name']:'';
                $filter_product['action'] = isset($condition_operators[$operator]) ? $condition_operators[$operator]['name']:'';
                if($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE)
                    $filter_product['compared_value'] = $this->displayListAttributes($filter_product['compared_value']);
                if($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES)
                    $filter_product['compared_value'] = $this->displayFeaturesList($filter_product['compared_value']);
                elseif($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR)
                    $filter_product['compared_value'] = $this->displayListColors($filter_product['compared_value']);
                elseif($filtered_field==Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND)
                    $filter_product['compared_value'] = $this->displayListBrands($filter_product['compared_value']);
                elseif($filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES)
                    $filter_product['compared_value'] = $this->displayListCategories($filter_product['compared_value']);
                elseif($filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER)
                    $filter_product['compared_value'] = $this->displayListSuppliers($filter_product['compared_value']);
            }
            unset($filter_product);
        }
        $edit_actions = Db::getInstance()->executeS('SELECT cf.* FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` cf
        WHERE cf.id_ets_pmn_massedit='.(int)$this->id.' AND id_ets_pmn_massedit_history=0');
        $fields = Ets_pmn_massedit_condition_action::getInstance()->getConditionFields();
        $actions = Ets_pmn_massedit_condition_action::getInstance()->getConditionActions();
        $id_lang = Context::getContext()->language->id;
        if($edit_actions)
        {
            foreach($edit_actions as $key=> &$edit_action)
            {
                if($edit_action['condition']!='off')
                {
                    switch($edit_action['field']){
                        case 'features':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListFeatures(json_decode($edit_action['value'],true)):'';
                            break;
                        case 'id_categories':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListCategories(implode(',',json_decode($edit_action['value'],true))):'';
                            break;
                        case 'related_products':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListProducts(json_decode($edit_action['value'],true)):'';
                            break;
                        case 'selectedCarriers':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListCarriers(json_decode($edit_action['value'],true)):'';
                            break;
                        case 'combinations':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListCombinations(json_decode($edit_action['value'],true)):'';
                            break;
                        case 'customization':
                            $edit_action['value'] = $edit_action['value'] ? $this->displayListCustomization(json_decode($edit_action['value'],true)):'';
                            break;
                        case 'specific_prices':
                            if($edit_action['value'])
                            {
                                $specific_prices = json_decode($edit_action['value'],true);
                                if($specific_prices)
                                {
                                    $text ='';
                                    if(isset($specific_prices['id_currency']) && ($id_currency= $specific_prices['id_currency']) && ($currency = new Currency($id_currency,$id_lang)) && Validate::isLoadedObject($currency) )
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Currency: %s'),$currency->name),'p');
                                    else
                                        $text .= Ets_pmn_defines::displayText($this->l('Currency: All currencies'),'p');
                                    if(isset($specific_prices['id_country']) && ($id_country= $specific_prices['id_country']) && ($country = new Country($id_country,$id_lang)) && Validate::isLoadedObject($country) )
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Country: %s'),$country->name),'p');
                                    else
                                        $text .= Ets_pmn_defines::displayText($this->l('Country: All countries'),'p');
                                    if(isset($specific_prices['id_group']) && ($id_group= $specific_prices['id_group']) && ($group = new Group($id_group,$id_lang)) && Validate::isLoadedObject($group) )
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Customer group : %s'),$group->name),'p');
                                    else
                                        $text .= Ets_pmn_defines::displayText($this->l('Customer group: All groups'),'p');
                                    if(isset($specific_prices['id_customer']) && ($id_customer= $specific_prices['id_customer']) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Customer: %s'),$customer->firstname.' '.$customer->lastname.' ('.$customer->email.')'),'p');
                                    else
                                        $text .= Ets_pmn_defines::displayText($this->l('Customer: All customers'),'p');
                                    if(isset($specific_prices['from']) && ($from = $specific_prices['from']) && Validate::isDate($from))
                                        $text .=  Ets_pmn_defines::displayText(sprintf($this->l('Available from: %s'),Tools::displayDate($from,false)),'p');
                                    if(isset($specific_prices['to']) && ($to = $specific_prices['to']) && Validate::isDate($to))
                                        $text .=  Ets_pmn_defines::displayText(sprintf($this->l('Available to: %s'),Tools::displayDate($to,false)),'p');
                                    if(isset($specific_prices['from_quantity']) && ($from_quantity = $specific_prices['from_quantity']))
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Starting at: %d'),$from_quantity),'p');
                                    if(isset($specific_prices['sp_reduction']) && ($sp_reduction = $specific_prices['sp_reduction']))
                                    {
                                        if(isset($specific_prices['sp_reduction_type']) && $specific_prices['sp_reduction_type']=='percentage')
                                            $text .= Ets_pmn_defines::displayText(sprintf($this->l('Apply a discount of: %s%s'),$sp_reduction,'%'),'p');
                                        else
                                        {
                                            if(isset($specific_prices['sp_reduction_tax']) && $specific_prices['sp_reduction_tax']==1)
                                                $text .= Ets_pmn_defines::displayText(sprintf($this->l('Apply a discount of: %s (Tax included.)'),Tools::displayPrice($sp_reduction)),'p');
                                            else
                                                $text .= Ets_pmn_defines::displayText(sprintf($this->l('Apply a discount of: %s (Tax excluded.)'),Tools::displayPrice($sp_reduction)),'p');
                                        }
                                    }
                                    if(isset($specific_prices['product_price']) && $specific_prices['product_price'])
                                        $text .= Ets_pmn_defines::displayText(sprintf($this->l('Product price (tax excl.): %s'),Tools::displayPrice($specific_prices['product_price'])),'p');
                                    $edit_action['value'] = $text;
                                }
                                else
                                    $edit_action['value']='';
                            }
                            else
                                $edit_action['value']='';
                            break;
                        case 'id_category_default':
                            $edit_action['value'] = (new Category($edit_action['value'],$id_lang))->name;
                            break;
                        case 'id_manufacturer':
                            $edit_action['value'] = (new Manufacturer($edit_action['value']))->name;
                            break;
                        case 'out_of_stock':
                            if(!$edit_action['value'])
                                $edit_action['value'] = $this->l('Deny orders');
                            elseif($edit_action['value']==1)
                                $edit_action['value'] = $this->l('Allow orders');
                            elseif($edit_action['value']==2)
                                $edit_action['value'] = $this->l('Use default behavior (Deny orders)');
                            break;
                        case 'additional_delivery_times':
                            if(!$edit_action['value'])
                                $edit_action['value'] = $this->l('None');
                            elseif($edit_action['value']==1)
                                $edit_action['value'] = $this->l('Default delivery time');
                            elseif($edit_action['value']==2)
                                $edit_action['value'] = $this->l('Specific delivery time to this product');
                            break;
                       case 'id_tax_rules_group':
                            if(!$edit_action['value'])
                                $edit_action['value'] = $this->l('No tax');
                            else
                            {
                                $edit_action['value'] = (new TaxRulesGroup($edit_action['value'],$id_lang))->name;
                            } 
                            break;
                       case 'price':
                       case 'unit_price':
                       case 'wholesale_price':
                       case 'additional_shipping_cost':
                            if($edit_action['condition']!='plus_percent' && $edit_action['condition']!='minus_percent')
                            {
                                if(Validate::isPrice($edit_action['value']))
                                    $edit_action['value'] = Tools::displayPrice($edit_action['value']);
                            }   
                            else
                                $edit_action['value'] .='%';
                            break;
                       case 'width':
                       case 'height':
                       case 'depth':
                            $edit_action['value'] = $edit_action['value'].$this->l('(cm)');
                            break;
                       case 'weight':
                            $edit_action['value'] = $edit_action['value'].$this->l('(Kg)');
                            break;
                       case 'visibility':
                            if($edit_action['value']=='both')
                                $edit_action['value'] = $this->l('Everywhere');
                            elseif($edit_action['value']=='catalog')
                                $edit_action['value'] = $this->l('Catalog only');
                            elseif($edit_action['value']=='search')
                                $edit_action['value'] = $this->l('Search only');
                            elseif($edit_action['value']=='none')
                                $edit_action['value'] = $this->l('Nowhere');
                            break;
                       case 'condition':
                            if($edit_action['value']=='new')
                                $edit_action['value'] = $this->l('New');
                            elseif($edit_action['value']=='used')
                                $edit_action['value'] = $this->l('Used');
                            elseif($edit_action['value']=='refurbished')
                                $edit_action['value'] = $this->l('Refurbished');
                            elseif($edit_action['value']=='none')
                                $edit_action['value'] = $this->l('Nowhere');
                       break;
                    }
                }
                $edit_action['languages'] = Db::getInstance()->executeS('SELECT cfl.value_lang,l.name as lang_name FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action_lang` cfl
                INNER JOIN  `'._DB_PREFIX_.'lang` l ON (l.id_lang=cfl.id_lang AND l.active=1)
                WHERE cfl.id_ets_pmn_massedit_condition_action='.(int)$edit_action['id_ets_pmn_massedit_condition_action'].' AND cfl.value_lang!="false"');
                if($edit_action['field']=='tags' && $edit_action['languages'])
                {
                    foreach($edit_action['languages'] as &$language)
                    {
                        if($language['value_lang'])
                        {
                            $value_lang = explode(',',$language['value_lang']);
                            $text ='';
                            foreach($value_lang as $val)
                                $text .= Ets_pmn_defines::displayText($val,'p');
                            $language['value_lang'] = $text;
                        }
                    }
                    unset($language);
                }
                if($edit_action['languages'])
                {
                    foreach($edit_action['languages'] as $key_lang=> &$language)
                    {
                        if(!$language['value_lang'])
                            unset($edit_action['languages'][$key_lang]);
                    }
                    unset($language);
                    if(!$edit_action['languages'])
                        unset($edit_actions[$key]);
                }
                $edit_action['field'] = isset($fields[$edit_action['field']]) ? $fields[$edit_action['field']]:$edit_action['field'];
                $edit_action['condition'] = isset($actions[$edit_action['condition']]) ? $actions[$edit_action['condition']] : $edit_action['condition'];
                
            }
            unset($edit_action);
        }
        $context->smarty->assign(
            array(
                'step_massedit_html' => $this->renderStepMassedit(3),
                'filter_products' => $filter_products,
                'product_list' => $this->renderListProductsByConditions($conditions,Validate::isCleanHtml($product_excluded) ? $product_excluded :'',false),
                'edit_actions' => $edit_actions,
                'massedit_name' => $this->name,
                'id_ets_pmn_massedit' => $this->id,
            )
        );
        return $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/preview_massedit.tpl');
    }
    public function displayListCustomization($customizations)
    {
        if($customizations)
        {
            $text = '';
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            foreach($customizations as $customization)
            {
                if($customization['label'][$id_lang_default]!='')
                {
                    if(isset($customization['require']) && $customization['require'])
                        $content = sprintf($this->l('Label: %s, Type: %s, Required: Yes'),$customization['label'][$id_lang_default],$customization['type']==1 ? 'text':'file');
                    else
                        $content = sprintf($this->l('Label: %s, Type: %s, Required: No'),$customization['label'][$id_lang_default],$customization['type']==1 ? 'text':'file');
                    $text .= Ets_pmn_defines::displayText($content,'p');
                }
                
            }
            return $text;
        }
    }
    public function displayListCombinations($combinations)
    {
        if($combinations)
        {
            $text = '';
            $id_lang = Context::getContext()->language->id;
            foreach($combinations as $id_attribute_group => $attributes)
            {
                $attributeGroup = new AttributeGroup($id_attribute_group,$id_lang);
                if(Validate::isLoadedObject($attributeGroup) && $attributes)
                {
                    $attributeName ='';
                    foreach($attributes as $id_attribute)
                    {
                        $attributeObj = new ProductAttribute($id_attribute,$id_lang);
                        if(Validate::isLoadedObject($attributeObj))
                            $attributeName .=$attributeObj->name.', ';
                    }
                    $text .= Ets_pmn_defines::displayText($attributeGroup->name.': '.trim($attributeName,', '),'p');
                }
            }
            return $text;
        }
    }
    public function displayListFeatures($features)
    {
         if(is_array($features) && Ets_productmanager::validateArray($features) && isset($features['id_features']) && isset($features['id_feature_values']) && isset($features['feature_value_custom']))
         {
            $id_features = $features['id_features'];
            $id_feature_values = $features['id_feature_values'];
            $feature_value_custom = $features['feature_value_custom'];
            $text ='';
            $id_lang = Context::getContext()->language->id;
            foreach($id_features as $key=>$id_feature)
            {
                $featureObj = new Feature($id_feature,$id_lang);
                if(Validate::isLoadedObject($featureObj))
                {
                    if(isset($id_feature_values[$key]) && ($id_feature_value = (int)$id_feature_values[$key]) && ($featureValue = new FeatureValue($id_feature_value,$id_lang)) && Validate::isLoadedObject($featureValue))
                        $text .= Ets_pmn_defines::displayText($featureObj->name.': '.$featureValue->value,'p');
                    elseif(isset($feature_value_custom[$key]) && ($value = $feature_value_custom[$key]))
                        $text .= Ets_pmn_defines::displayText($featureObj->name.': '.$value,'p');
                }
            }
            return $text;
         }   
    }
    public function displayListCarriers($id_carriers)
    {
        if(!is_array($id_carriers))
            $id_carriers = explode(',',$id_carriers);
        $sql = 'SELECT c.name,c.id_carrier FROM  `'._DB_PREFIX_.'carrier` c
        INNER JOIN  `'._DB_PREFIX_.'carrier_shop` cs ON (c.id_carrier=cs.id_carrier AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE c.id_carrier IN ('.implode(',',array_map('intval',$id_carriers)).')';
        $carriers = Db::getInstance()->executeS($sql);
        if($carriers)
        {
            $text ='';
            foreach($carriers as $carrier)
            {
                $text .= Ets_pmn_defines::displayText($carrier['name'] ? : Context::getContext()->shop->name,'p');
            }
            return $text;
        }
    }
    public function displayListProducts($id_products)
    {
        if(!is_array($id_products))
            $id_products = explode(',',$id_products);
        $sql = 'SELECT p.id_product,pl.name FROM  `'._DB_PREFIX_.'product` p 
            INNER JOIN  `'._DB_PREFIX_.'product_shop` ps ON (p.id_product=ps.id_product AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN  `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'" AND pl.id_shop="'.(int)Context::getContext()->shop->id.'")
            WHERE p.id_product IN ('.implode(',',array_map('intval',$id_products)).')
        ';
        if($products = Db::getInstance()->executeS($sql))
        {
            $text = '';
            foreach($products as $product)
            {
                $text .= Ets_pmn_defines::displayText($product['name'],'p');
            }
            return $text;
        }
    }
    public function displayListSuppliers($id_suppliers,$html= true)
    {
        $suppliers = Ets_pmn_massedit_condition::getInstance()->getSuppliers($id_suppliers);
        if($suppliers)
        {
            $text = '';
            foreach($suppliers as $supplier)
                $text .= $html ? Ets_pmn_defines::displayText($supplier['name'],'p') : $supplier['name'].', ' ;
            return $html ? $text : trim($text,', ');
        }
    }
    public function displayListCategories($id_categories,$html = true)
    {
        if($id_categories)
        {
            $categories = Ets_pmn_massedit_condition::getInstance()->getCategories($id_categories);
            if($categories)
            {
                $text ='';
                foreach($categories as $category)
                    $text .= $html ? Ets_pmn_defines::displayText($category['name'],'p') : $category['name'].', ';
                return $html ? $text : trim($text,', ');
            }
        }
        
    }
    public function displayListBrands($id_manaufacturers,$html = true)
    {
        $brands = Ets_pmn_massedit_condition::getInstance()->getManufacturers($id_manaufacturers);
        if($brands)
        {
            $text = '';
            foreach($brands as $brand)
                $text .= $html ? Ets_pmn_defines::displayText($brand['name'],'p') : $brand['name'].', ';
            return $html ? $text : trim($text,', ');
        }
    }
    public function displayListAttributes($id_attributes,$html=true)
    {
        $attributes = Ets_pmn_massedit_condition::getInstance()->getAttributes($id_attributes);
        if($attributes)
        {
            $text ='';
            foreach($attributes as $attribute)
                $text .= $html ? Ets_pmn_defines::displayText($attribute['name'],'p') :$attribute['name'].', ';
            return $html ? $text : trim($text,', ');
        }
    }
    public function displayFeaturesList($id_features,$html = true)
    {
        $features = Ets_pmn_massedit_condition::getInstance()->getFeatures($id_features);
        if($features)
        {
            $text ='';
            foreach($features as $feature)
                $text .= $html ? Ets_pmn_defines::displayText($feature['name'],'p') :$feature['name'].', ';
            return $html ? $text : trim($text,', ');
        }
    }
    public function displayListColors($id_attributes,$html= true)
    {
        $context = Context::getContext();
        $colors = Ets_pmn_massedit_condition::getInstance()->getAttributeColors($id_attributes);
        if($html)
        {
            $context->smarty->assign(
                array(
                    'colors' => $colors,
                )
            );
            return $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/colors.tpl');
        }
        else
        {
            if($colors)
            {
                $text = '';
                foreach($colors as $color)
                {
                    $context->smarty->assign(
                        array(
                            'color' => $color,
                        )
                    );
                    $text .= $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/color.tpl').', ';
                }
                return trim($text,', ');
            }
        }
    }
    public function renderFormRowMassedit($condition=null)
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($condition && $condition['filtered_field'] == Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES)
            $selected_categories = $condition['compared_value'];
        else
            $selected_categories= array();
        $categories = array();
        $context = Context::getContext();
        $context->smarty->assign(
            array(
                'condition_fields' => Ets_pmn_massedit_condition::getInstance()->getListFields(),
                'condition_operators' => Ets_pmn_massedit_condition::getInstance()->getOperators(),
                'languages' => Language::getLanguages(false),
                'condition' => $condition,
                'attributes' => Ets_pmn_massedit_condition::getInstance()->getAttributes(),
                'features' => Ets_pmn_massedit_condition::getInstance()->getFeatures(),
                'manufacturers' => Ets_pmn_massedit_condition::getInstance()->getManufacturers(),
                'suppliers' => Ets_pmn_massedit_condition::getInstance()->getSuppliers(),
                'colors' => Ets_pmn_massedit_condition::getInstance()->getAttributeColors(),
                'tree_categories' =>$module->displayProductCategoryTre(Ets_pmn_defines::getInstance()->getCategoriesTree(),$selected_categories,'',array(),0,$categories,true),
            )
        );
        return $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/row_form_massedit.tpl');
    }
    public function renderStepMassedit($step)
    {
        $context = Context::getContext();
        $context->smarty->assign(
            array(
                'step' => $step,
                'template_massedits' => $this->renderTemplateMassedit(),
            )
        );
        return $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/step_massedit.tpl');
    }
    public function renderTemplateMassedit()
    {
        $context = Context::getContext();
        $context->smarty->assign(
            array(
                'id_ets_pmn_massedit' => $this->id,
                'massedits' => Ets_pmn_massedit::getListMassedit('',0,false),
            )
        );
        return $context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/massive_templates.tpl');
    }
    public function getProducts($conditions,$total=false,$start=0,$limit=20,$excludeProducts='',$type_combine_condition='')
    {
        if(!$type_combine_condition)
            $type_combine_condition = $this->type_combine_condition ?: "and";
        if(!in_array($type_combine_condition,array('and','or')))
            $type_combine_condition = 'and';
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
                               SELECT pa.id_product FROM `'._DB_PREFIX_.'product_attribute` pa
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
        else
        {
            return $total ? 0 : array();
        }

        if($total)
        {
            $sql ='SELECT COUNT(DISTINCT p.id_product) FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product= p.id_product)
            INNER JOIN  `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product=ps.id_product AND sa.id_product_attribute=0 AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
            '.(string)$leftJoin.'
            WHERE p.state=1 AND ps.id_shop = "'.(int)Context::getContext()->shop->id.'" AND ( '.($type_combine_condition=='and' ? '1':'0').' '.($filters ? (string)$filters:'').' ) '.($excludeProducts ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',explode(',',$excludeProducts))).')':'');
            return Db::getInstance()->getValue($sql);
        }
        else
        {
            $sql ='SELECT DISTINCT p.id_product,product_lang.name,p.price,product_lang.link_rewrite FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product= p.id_product AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
            INNER JOIN  `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product=ps.id_product AND sa.id_product_attribute=0 AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
            LEFT JOIN `'._DB_PREFIX_.'product_lang` product_lang ON (product_lang.id_product=ps.id_product AND product_lang.id_lang="'.(int)Context::getContext()->language->id.'" AND product_lang.id_shop="'.(int)Context::getContext()->shop->id.'")
            '.(string)$leftJoin.'
            WHERE p.state=1 AND ps.id_shop = "'.(int)Context::getContext()->shop->id.'" AND ('.($type_combine_condition=='and' ? '1':'0').' '.($filters ? (string)$filters:'').' )'.($excludeProducts ? ' AND p.id_product NOT IN ('.implode(',',array_map('intval',explode(',',$excludeProducts))).')':'').' ORDER BY p.id_product'.($limit ? ' LIMIT '.(int)$start.','.(int)$limit:'');
            $products = Db::getInstance()->executeS($sql);
            if($products)
            {
                $type_image= Ets_productmanager::getFormatedName('small');
                foreach($products as &$product)
                {
                    if($id_image = (int)Db::getInstance()->getValue('SELECT id_image FROM  `'._DB_PREFIX_.'image` WHERE id_product="'.(int)$product['id_product'].'" AND cover=1'))
                    {
                        $product['image'] = Context::getContext()->link->getImageLink($product['link_rewrite'],$id_image,$type_image);
                    }elseif($id_image = (int)Db::getInstance()->getValue('SELECT id_image FROM  `'._DB_PREFIX_.'image` WHERE id_product='.(int)$product['id_product']))
                        $product['image'] = Context::getContext()->link->getImageLink($product['link_rewrite'],$id_image,$type_image);
                    else
                        $product['image'] ='';
                    $product['link'] = Context::getContext()->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product']));
                }
            }
            
            return $products;
        }
    }
    public function renderListProductsByConditions($conditions,$product_excluded,$excluded = true,$page=1,$limit=10,$stepNumber=0,$type_combine_condition='')
    {
        if(!$type_combine_condition)
            $type_combine_condition = $this->type_combine_condition;
        if($limit<=0)
            $limit =10;
        $fields_list = array(
            'id_product' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => false,
                'filter' => false,
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type' => 'text',
                'sort' => false,
                'filter' => false,
                'strip_tag'=>false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 40,
                'type' => 'text',
                'sort' => false,
                'filter' => false,
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'type' => 'text',
                'strip_tag' => false,                
            ),
        );
        if($excluded)
            $fields_list['edit_action'] = array(
                'title' => $this->l('Selected for edit'),
                'type' => 'text',
                'strip_tag' => false,
            );
        $module = Module::getInstanceByName('ets_productmanager');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) $this->getProducts($conditions,true,0,false,!$excluded ? $product_excluded:'',$type_combine_condition);
        Context::getContext()->smarty->assign('totalEditProducts',$totalRecords);
        $paggination = new Ets_pmn_paggination_class();
        $paggination->name ='matching_products';
        $paggination->text=false;
        $paggination->total = $totalRecords;
        $paggination->select_limit = true;
        if($excluded)
            $paggination->url = Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEdit').'&getProducts=1&page=_page_'.$module->getFilterParams($fields_list,'pmn_products');
        else
            $paggination->url = Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEdit').'&getPreviewProducts=1&id_massedit='.(int)$this->id.'&page=_page_'.$module->getFilterParams($fields_list,'pmn_products');
        $paggination->limit = (int)$limit;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $products = $this->getProducts($conditions,false,$start,$paggination->limit,!$excluded ? $product_excluded:'',$type_combine_condition);
        if($excluded)
        {
            if($product_excluded)
            {
                $product_excluded = array_map('intval',explode(',',$product_excluded));
            }
        }
        if($products)
        {
            foreach($products as &$product)
            {
                $product['price'] =Tools::displayPrice($product['price']);
                $product['image'] = Ets_pmn_defines::displayText(Ets_pmn_defines::displayText('','img',array('src'=>$product['image'])),'a',array('href'=>$product['link']));
                if($excluded)
                {
                    $attr_datas = array(
                        'type' => 'checkbox',
                        'name' => 'exclued_massive_id_products',
                        'class' => 'exclued_massive_id_products',
                        'value' => $product['id_product'],
                    );
                    if($product_excluded)
                    {
                        if(in_array($product['id_product'],$product_excluded))
                        {
                            $attr_datas['checked'] = 'checked';
                            $attr_datas['title'] = $this->l('Click to select');
                        }
                        else
                        {
                            $attr_datas['title'] = $this->l('Click to unselect');
                        }
                    }
                    else
                        $attr_datas['title'] = $this->l('Click to unselect');
                    $product['edit_action'] = Ets_pmn_defines::displayText(Ets_pmn_defines::displayText('','input',$attr_datas).Ets_pmn_defines::displayText('','span',array('class'=>'check_input_color')),'div',array('class'=>'ets_mg_edit_action'));
                }
            }
        }
        $listData = array(
            'name' => 'pmn_products',
            'icon' => 'fa fa-product',
            'actions' => array(),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEdit'),
            'identifier' => 'id_product',
            'show_toolbar' => false,
            'show_action' => false,
            'title' => Tools::isSubmit('submitSaveMasseEditProduct') || $stepNumber==4 ? $this->l('Products') : $this->l('Matching products'),
            'text_no_item' => $this->l('No matching product'),
            'fields_list' => $fields_list,
            'field_values' => $products,
            'paggination' => $paggination->render(),
            'filter_params' => $module->getFilterParams($fields_list,'pmn_products'),
            'show_reset' =>false,
            'totalRecords' => $totalRecords,
            'sort'=> 'asc',
            'show_add_new'=> false,
            'link_new' => '', 
            'sort_type' => 'p.id_product',
        ); 
        return $module->renderList($listData);
    }
    public static function getManufacturers()
    {
        $sql = 'SELECT id_manufacturer as id,name FROM  `'._DB_PREFIX_.'manufacturer` where active=1';
        return Db::getInstance()->executeS($sql);
    }
    public static function getFeatures()
    {
        $sql = 'SELECT f.id_feature,fl.name FROM  `'._DB_PREFIX_.'feature` f
        INNER JOIN  `'._DB_PREFIX_.'feature_shop` fs ON (f.id_feature=fs.id_feature AND fs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN  `'._DB_PREFIX_.'feature_lang` fl ON (f.id_feature=fl.id_feature AND fl.id_lang="'.(int)Context::getContext()->language->id.'")
        ';
        return Db::getInstance()->executeS($sql);
    }
    public static function getFeatureValues()
    {
        $sql ='SELECT fv.id_feature_value,fv.custom,fvl.value,fv.id_feature FROM  `'._DB_PREFIX_.'feature_value` fv
        INNER JOIN  `'._DB_PREFIX_.'feature` f ON (fv.id_feature = f.id_feature)
        INNER JOIN  `'._DB_PREFIX_.'feature_shop` fs ON (f.id_feature = fs.id_feature AND fs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN  `'._DB_PREFIX_.'feature_value_lang` fvl ON (fvl.id_feature_value= fv.id_feature_value AND fvl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE fv.custom=0 GROUP BY fv.id_feature_value';
        return Db::getInstance()->executeS($sql);
    }
    public static function getListCarriers()
    {
        $sql ='SELECT c.id_reference id,c.name,cl.delay FROM  `'._DB_PREFIX_.'carrier` c
        INNER JOIN  `'._DB_PREFIX_.'carrier_shop` cs ON (c.id_carrier=cs.id_carrier AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN  `'._DB_PREFIX_.'carrier_lang` cl ON (c.id_carrier = cl.id_carrier AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE c.deleted=0 GROUP BY c.id_reference';
        return Db::getInstance()->executeS($sql);
    }
    
    public static function getTaxRulesGroups()
    {
        return Db::getInstance()->executeS('
			SELECT DISTINCT g.id_tax_rules_group as id, g.name, g.active
			FROM `' . _DB_PREFIX_ . 'tax_rules_group` g'
            . Shop::addSqlAssociation('tax_rules_group', 'g') . ' WHERE deleted = 0 AND g.`active` = 1
			ORDER BY name ASC');
    }
    public function getProductLanguageFields()
    {
        return array(
            'meta_description' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Meta description'),
                'size' => 512,
            ),
            'meta_keywords' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Meta keywords'),
                'size' => 255,
            ),
            'meta_title' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Meta title'),
                'size' => 255,
            ),
            'link_rewrite' => array(
                'validate' => 'isLinkRewrite',
                'title' => $this->l('Friendly URL'),
                'size' => 128,
            ),
            'name' => array(
                'validate' => 'isCatalogName',
                'title' => $this->l('Name'),
                'size' => 128,
            ),
            'description' => array(
                'validate' => 'isCleanHtml',
                'title' => $this->l('Description'),
            ),
            'description_short' => array(
                'validate' => 'isCleanHtml',
                'title' => $this->l('Summary'),
                'size' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? :800,
            ),
            'available_now' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Label when in stock '),
                'size' => 255,
            ),
            'available_later' =>array(
                'validate' =>'isGenericName',
                'title' => $this->l('Label when out of stock (and back order allowed) '),
                'size' => 255,
            ), 
            'delivery_in_stock' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Delivery time of in-stock products'),
                'size' => 255,
            ),
            'delivery_out_stock' => array(
                'validate' => 'isGenericName',
                'title' => $this->l('Delivery time of out-of-stock products with allowed orders'),
                'size' => 255,
            )
        );
    }
    public function getProductFields(){
        return array(
            'id_manufacturer'=> array(
                'validate'=> 'isUnsignedId',
                'title'=> $this->l('Brand'),
            ),
            'id_supplier'=> array(
                'validate'=> 'isUnsignedId',
                'title'=> $this->l('Default supplier'),
            ),
            'reference' => array(
                'validate' => 'isReference',
                'title' => $this->l('Reference'),
                'size' => 64
            ),
            'location' => array(
                'validate' => 'isReference',
                'title' => $this->l('Location'),
                'size' => 64
            ),
            'width' => array(
                'validate' => 'isUnsignedFloat',
                'title' => $this->l('Width'),
            ),
            'height'=> array(
                'validate'=> 'isUnsignedFloat',
                'title' => $this->l('Height'),
            ),
            'depth' => array(
                'validate' => 'isUnsignedFloat',
                'title' => $this->l('Depth'),
            ),
            'weight' => array(
                'validate' => 'isUnsignedFloat',
                'title' => $this->l('Weight'),
            ),
            'ean13' => array(
                'title' => $this->l('EAN-13 or JAN barcode '),
                'validate' => 'isEan13',
            ),
            'isbn' => array(
                'title' => $this->l('ISBN'),
                'validate' => 'isIsbn',
            ),
            'upc' => array(
                'title' => $this->l('UPC barcode'),
                'validate' => 'isUpc'
            ),
            'additional_delivery_times' => array(
                'title' => $this->l('Delivery time'),
                'validate' =>'isUnsignedId',
            ),
            'id_category_default' => array(
                'title' => $this->l('Default category'),
                'validate' => 'isUnsignedId',
            ),
            'id_tax_rules_group' => array(
                'tilte' => $this->l('Tax rules'),
                'validate' => 'isUnsignedId',
            ),
            'on_sale' => array(
                'title' => $this->l('On sale'),
                'validate' => 'isBool'
            ),
            'online_only' => array(
                'title' => $this->l('Online only'),
                'validate' => 'isBool',
            ),
            'minimal_quantity' => array(
                'title' => $this->l('Minimal quantity'),
                'validate' => 'isUnsignedInt',
            ),  
            'low_stock_threshold' => array(
                'title' => $this->l('Low stock level'),
                'validate' => 'isInt',
            ),
            'quantity' => array(
                'title' => $this->l('Quantity'),
                'validate' => 'isInt',
            ),
            'out_of_stock' => array(
                'title' => $this->l('Availability preferences'),
                'validate' => 'isInt',
            ),
            'low_stock_alert' => array(
                'title' => $this->l('Send me an email when the quantity is below or equals this level'),
                'validate' => 'isBool',
            ),
            'price' => array(
                'title' => $this->l('Price (tax excl.)'),
                'validate' => 'isPrice',
            ),
            'unit_price' => array(
                'title' => $this->l('Price per unit (tax excl.)'),
                'validate' => 'isPrice',
            ),
            'wholesale_price' => array(
                'title' => $this->l('Cost price'),
                'validate' => 'isPrice',
            ),
            'additional_shipping_cost' => array(
                'title' => $this->l('Shipping fees'),
                'validate' => 'isPrice',
            ),
            'active' => array(
                'title' => $this->l('Enable'),
                'validate' =>'isBool',
            ),
            'available_for_order' => array(
                'title' => $this->l('Available for order'),
                'validate' =>'isBool',
            ),
            'show_condition' => array(
                'title' => $this->l('Display condition on product page'),
                'validate' =>'isBool',
            ),
            'condition' => array(
                'title' => $this->l('Condition'),
                'validate' =>'isGenericName',
            ),
            'visibility' => array(
                'title' => $this->l('Visibility'),
                'validate' =>'isProductVisibility',
            ),
        );
    }
    public function deleteCondition()
    {
        return Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition` WHERE id_ets_pmn_massedit='.(int)$this->id);
    }
    public function getEditActions($id_ets_pmn_massedit_history=0)
    {
        return Db::getInstance()->executeS('SELECT `id_ets_pmn_massedit_condition_action`,`field`,`condition` FROM  `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` WHERE id_ets_pmn_massedit='.(int)$this->id.($id_ets_pmn_massedit_history  ? ' AND id_ets_pmn_massedit_history='.(int)$id_ets_pmn_massedit_history: ' AND id_ets_pmn_massedit_history=0').'   GROUP BY id_ets_pmn_massedit_condition_action');
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getAllMassedit()
    {
        return Db::getInstance()->executeS('
            SELECT m.id_ets_pmn_massedit,if(m.name!="",m.name,"--") as name FROM  `'._DB_PREFIX_.'ets_pmn_massedit` m
            INNER JOIN  `'._DB_PREFIX_.'ets_pmn_massedit_history` mh ON (m.id_ets_pmn_massedit = mh.id_ets_pmn_massedit)
            INNER JOIN  `'._DB_PREFIX_.'ets_pmn_massedit_history_product` mhp ON (mhp.id_ets_pmn_massedit_history =mh.id_ets_pmn_massedit_history)
            WHERE mh.id_shop = "'.(int)Context::getContext()->shop->id.'" GROUP BY m.id_ets_pmn_massedit
        ');
    }
    public static function getListProductsRelated($id_massedit)
    {
        if($id_massedit >1 && ($selected_products = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` WHERE `field`="related_products" AND id_ets_pmn_massedit='.(int)$id_massedit)) && ($selected_products = json_decode($selected_products,true)))
        {
            $sql ='SELECT p.*,pl.name,pl.link_rewrite,image_shop.`id_image` id_image, il.`legend` FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product=ps.id_product AND ps.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'" AND pl.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) Context::getContext()->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            WHERE p.id_product IN ('.implode(',',array_map('intval',$selected_products)).')
            GROUP BY p.id_product';
            $related_products = Db::getInstance()->executeS($sql);
            if($related_products)
            {
                $type_image= Ets_productmanager::getFormatedName('small');
                foreach($related_products as &$related_product)
                {
                    if(!$related_product['id_image'])
                        $related_product['id_image'] = Ets_pmn_defines::getIdImageByIdProduct($related_product['id_product']);
                    if($related_product['id_image'])
                        $related_product['img'] = Context::getContext()->link->getImageLink($related_product['link_rewrite'], $related_product['id_image'], $type_image);
                }
            }
            return $related_products;
        }
        return false;
    }
    public static function getValueFieldByID($id_massedit,$field)
    {
        return Db::getInstance()->getValue('SELECT `value` FROM `' . _DB_PREFIX_ . 'ets_pmn_massedit_condition_action` WHERE `field`="'.pSQL($field).'" AND id_ets_pmn_massedit=' . (int)$id_massedit);
    }
}