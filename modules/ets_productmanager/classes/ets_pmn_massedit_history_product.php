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
class Ets_pmn_massedit_history_product extends ObjectModel
{
    public $id_ets_pmn_massedit_history;
    public $id_product;
    public $field_name;
    public $id_lang;
    public $old_value;
    public $new_value;
    public $date_add;
    public static $definition = array(
		'table' => 'ets_pmn_massedit_history_product',
		'primary' => 'id_ets_pmn_massedit_history_product',
		'multilang' => false,
		'fields' => array(
			'id_ets_pmn_massedit_history' => array('type' => self::TYPE_INT),
            'id_product' => array('type'=> self::TYPE_INT),
            'field_name' => array('type' => self::TYPE_STRING),
            'id_lang' => array('type'=>self::TYPE_INT),
            'old_value' => array('type'=>self::TYPE_HTML),
            'new_value' => array('type'=>self::TYPE_HTML),
            'date_add' => array('type' => self::TYPE_DATE),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public function restoreProduct()
    {
        switch($this->field_name){
            case 'features':
                $this->restoreFeatureProducts();
                break;
            case 'id_categories':
                $this->restoreCategories();
                break;
            case 'related_products':
                $this->restoreRelatedProducts();
                break;
            case 'selectedCarriers':
                $this->restoreCarriers();
                break;
            case 'combinations':
                $this->restoreCombinations();
                break;
            case 'customization':
                $this->restoreCustomizations();
                break;
            case 'stocks':
                $this->restoreStocks();
                break;
            case 'tags':
                $this->restoreTags();
                break;
            case 'specific_prices':
                $this->restoreSpecificPrices();
                break;
        }
    }
    public function restoreSpecificPrices()
    {
        /** @var \Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'specific_price` WHERE id_product='.(int)$this->id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
            $specific_prices = json_decode($this->old_value,true);
            if($specific_prices)
            {
                foreach($specific_prices as $specific_price)
                {
                    if($specific_price['id_product']==$this->id_product)
                    {
                        $specificPrice = new SpecificPrice($specific_price['id_specific_price']);
                        if(!Validate::isLoadedObject($specificPrice))
                        {
                            $specificPrice->id = (int)$specific_price['id_specific_price'];
                            $specificPrice->force_id = true;
                        }
                        foreach($specific_price as $key=>$val)
                        {
                            $specificPrice->{$key} = $val;
                        }
                        if($specificPrice->force_id)
                            $specificPrice->add();
                        else
                            $specificPrice->update();
                        if($specificPrice->id && $specificPrice->id_product_attribute && isset($specific_price['product_attributes']) && ($product_attributes = $specific_price['product_attributes']) && isset($specific_price['attributes']) && ($attributes = $specific_price['attributes']) && ($combination = new Combination($product_attributes['id_product_attribute'])) && !Validate::isLoadedObject($combination))
                        {
                            $combination->id = (int)$product_attributes['id_product_attribute'];
                            $combination->force_id=true;
                            foreach($product_attributes as $key=>$val)
                            {
                                $combination->{$key} = $val;
                            }
                            $combination->default_on =0;
                            if($combination->force_id)
                                $combination->add();
                            else
                                $combination->update();
                            if($combination->id)
                            {
                                foreach($attributes as $attribute)
                                {
                                    if(class_exists('AttributeCore'))
                                        $attributeObj = new Attribute($attribute['id_attribute']);
                                    else
                                        $attributeObj = new ProductAttribute($attribute['id_attribute']);
                                    if(Validate::isLoadedObject($attributeObj) && !Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_attribute_combination` WHERE id_product_attribute="'.(int)$combination->id.'" AND id_attribute='.(int)$attribute['id_attribute']))
                                    {
                                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (id_product_attribute,id_attribute) VALUES("'.(int)$combination->id.'","'.(int)$attribute['id_attribute'].'")');
                                    }
                                }
                            }
                        }
                    }
                    
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Specific price is not valid');
        }
        else
        {
            $specifics = Db::getInstance()->executeS('SELECT id_specific_price FROM  `'._DB_PREFIX_.'specific_price` WHERE id_product='.(int)$this->id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
            if($specifics)
            {
                foreach($specifics as $specific)
                {
                    $obj = new SpecificPrice($specific['id_specific_price']);
                    $obj->delete();
                }
            }
        }
    }
    public function restoreCombinations($del_stock= true)
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        $product = new Product($this->id_product);
        if($this->old_value)
        {
            $productAttributes = json_decode($this->old_value,true);
            if($productAttributes)
            {
                $product->deleteProductAttributes();
                foreach($productAttributes as $productAttribute)
                {
                    if(isset($productAttribute['id_product']) && $productAttribute['id_product']== $this->id_product)
                    {
                        $combination = new Combination($productAttribute['id_product_attribute']);
                        foreach($productAttribute as $key=>$val)
                        {
                            if($key=='id_product_attribute')
                            {
                                if(!$combination->id)
                                {
                                    $combination->id= $val;
                                    $combination->force_id = true;
                                }
                            }
                            elseif($key!='name' && $key!='images' && $key!='attributes')
                            {
                                $combination->{$key} = $val;
                            }
                        }
                        if(($combination->force_id &&  $combination->add()) || $combination->update())
                        {
                            if(isset($productAttribute['attributes']) && $productAttribute['attributes'])
                            {
                                foreach($productAttribute['attributes'] as $attribute)
                                {
                                    $id_attribute = $attribute['id_attribute'];
                                    if(class_exists('AttributeCore'))
                                        $attributeObj = new Attribute($id_attribute);
                                    else
                                        $attributeObj = new ProductAttribute($id_attribute);
                                    if(Validate::isLoadedObject($attributeObj))
                                    {
                                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_attribute_combination` WHERE id_product_attribute="'.(int)$combination->id.'" AND id_attribute="'.(int)$id_attribute.'"'))
                                        {
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_combination`(id_product_attribute,id_attribute) VALUES("'.(int)$combination->id.'","'.(int)$id_attribute.'")');
                                        }
                                    }
                                }
                            }
                            if(isset($attribute['images']) && $attribute['images'])
                            {
                                foreach($attribute['images'] as $image)
                                {
                                    $id_image = (int)$image['id_image'];
                                    $image = new Image($id_image);
                                    if(Validate::isLoadedObject($image) && $image->id_product == $this->id_product)
                                    {
                                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_attribute_image` WHERE id_image="'.(int)$id_image.'" AND id_product_attribute="'.(int)$combination->id.'"'))
                                        {
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_image`(id_product_attribute,id_image) VALUES("'.(int)$combination->id.'","'.(int)$id_image.'")');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Combinations is not valid');
        }
        else
        {
            $product->deleteProductAttributes();
        }
        if($del_stock)
        {
            $sql ='SELECT id_ets_pmn_massedit_history_product FROM `'._DB_PREFIX_.'ets_pmn_massedit_history_product`
            WHERE id_product="'.(int)$this->id_product.'" AND id_ets_pmn_massedit_history="'.(int)$this->id_ets_pmn_massedit_history.'" AND field_name="stocks"';
            if($id_ets_pmn_massedit_history_product = (int)Db::getInstance()->getValue($sql))
            {
                $history_product = new Ets_pmn_massedit_history_product($id_ets_pmn_massedit_history_product);
                $history_product->restoreStocks(false);
                if(!$module->_errors)
                    $history_product->delete();
            }
        }
        
    }
    public function restoreCustomizations()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $customizations = json_decode($this->old_value,true);
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product='.(int)$this->id_product);
            if($customizations)
            {
                foreach($customizations as $customization)
                {
                    if($customization['id_product'] == $this->id_product)
                    {
                        $customization_field = new CustomizationField((int)$customization['id_customization_field']);
                        $customization_field->id_product = $this->id_product;
                        $customization_field->is_deleted=0;
                        $customization_field->is_module = $customization['is_module'];
                        $customization_field->required = $customization['required'];
                        $customization_field->type = $customization['type'];
                        if($customization['name'])
                        {
                            foreach($customization['name'] as $name)
                            {
                                $customization_field->name[$name['id_lang']] = $name['name'];
                            }
                        }
                        $customization_field->save();
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Customizations is not valid');
        }
        else
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product='.(int)$this->id_product);
    }
    public function restoreStocks($del_combination = true)
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $stock_availables = json_decode($this->old_value,true);
            if($stock_availables)
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'stock_available` WHERE id_product='.(int)$this->id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
                foreach($stock_availables as $stock_available)
                {
                    if(isset($stock_available['id_product']) && $stock_available['id_product']== $this->id_product && !StockAvailable::getStockAvailableIdByProductId($stock_available['id_product'],$stock_available['id_product_attribute'],$stock_available['id_shop']))
                    {
                        $stockAvailable = new StockAvailable();
                        foreach($stock_available as $key=>$val)
                        {
                            if($key=='id_stock_available')
                            {
                                $stockAvailable->id= $val;
                                $stockAvailable->force_id = true;
                            }
                            else
                                $stockAvailable->{$key} = $val;
                        }
                        $stockAvailable->add();
                    }
                }
                if($del_combination)
                {
                    $sql ='SELECT id_ets_pmn_massedit_history_product FROM  `'._DB_PREFIX_.'ets_pmn_massedit_history_product`                     WHERE id_product="'.(int)$this->id_product.'" AND id_ets_pmn_massedit_history="'.(int)$this->id_ets_pmn_massedit_history.'" AND field_name="combinations"';
                    if($id_ets_pmn_massedit_history_product =(int)Db::getInstance()->getValue($sql))
                    {
                        $history_product = new Ets_pmn_massedit_history_product($id_ets_pmn_massedit_history_product);
                        $history_product->restoreCombinations(false);
                        if(!$module->_errors)
                            $history_product->delete();
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Customizations is not valid');
        }
    }
    public function restoreTags()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $tags = json_decode($this->old_value,true);
            if($tags)
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product='.(int)$this->id_product);
                foreach($tags as $tag)
                {
                    if($tag['id_product'] == $this->id_product && Validate::isLoadedObject(new Tag($tag['id_tag'])))
                    {
                        if(!Db::getInstance()->getValue('SELECT * FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product="'.(int)$tag['id_product'].'" AND id_tag="'.(int)$tag['id_tag'].'" AND id_lang="'.(int)$tag['id_lang'].'"'))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_tag`(id_product,id_tag,id_lang) VALUES("'.(int)$tag['id_product'].'","'.(int)$tag['id_tag'].'","'.(int)$tag['id_lang'].'")');
                        }
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Tags is not valid');
            
        }
        else
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_tag` WHERE id_product='.(int)$this->id_product);
    }
    public function restoreFeatureProducts()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $features = json_decode($this->old_value,true);
            if($features)
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$this->id_product);
                foreach($features as $feature)
                {
                    if($this->id_product == (int)$feature['id_product'] && Validate::isLoadedObject( new Feature((int)$feature['id_feature'])) && Validate::isLoadedObject(new FeatureValue((int)$feature['id_feature_value'])))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product="'.(int)$this->id_product.'" AND id_feature="'.(int)$feature['id_feature'].'" AND id_feature_value ="'.(int)$feature['id_feature_value'].'"'))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$this->id_product.'","'.(int)$feature['id_feature'].'","'.(int)$feature['id_feature_value'].'")');
                        }
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Feature is not valid');
        }
        else
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$this->id_product);
        }
    }
    public function restoreCategories()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $categories = json_decode($this->old_value,true);
            if($categories)
            {
                $product = new Product($this->id_product);
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$this->id_product.' AND id_category!='.(int)$product->id_category_default);
                foreach($categories as $category)
                {
                    if($this->id_product== $category['id_product'] && Validate::isLoadedObject(new Category((int)$category['id_category'])))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$category['id_product'].' AND id_category='.(int)$category['id_category']))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product`(id_product,id_category,position) VALUES("'.(int)$category['id_product'].'","'.(int)$category['id_category'].'","'.(int)$category['position'].'")');
                        }
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Categories is not valid');
        }
    }
    public function restoreRelatedProducts()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $products = json_decode($this->old_value,true);
            if($products)
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1='.(int)$this->id_product);
                foreach($products as $product)
                {
                    if($product['id_product_1']== $this->id_product && Validate::isLoadedObject(new Product((int)$product['id_product_2'])))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$this->id_product.'" AND id_product_2='.(int)$product['id_product_2']))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'accessory`(id_product_1,id_product_2) VALUES("'.(int)$product['id_product_1'].'","'.(int)$product['id_product_2'].'")');
                        }
                    }
                }
            }
            else
                $module->_errors[] = $this->l('Old value of Related product is not valid');
        }
        else
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'accessory` WHERE id_product_1='.(int)$this->id_product);
    }
    public function restoreCarriers()
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        if($this->old_value)
        {
            $carriers = json_decode($this->old_value,true);
            if($carriers)
            {
                Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$this->id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
                foreach($carriers as $carrier)
                {
                    if($this->id_product== $carrier['id_product'] && Validate::isLoadedObject(Carrier::getCarrierByReference($carrier['id_carrier_reference'])))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$carrier['id_product'].' AND id_carrier_reference='.(int)$carrier['id_carrier_reference'].' AND id_shop='.(int)$carrier['id_shop']))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_carrier`(id_product,id_carrier_reference,id_shop) VALUES("'.(int)$this->id_product.'","'.(int)$carrier['id_carrier_reference'].'","'.(int)$carrier['id_shop'].'")');
                        }
                    }
                }
            }
            else
            {
                $module->_errors[] = $this->l('Old value of Carriers is not valid');
            }
        }
        else
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$this->id_product.' AND id_shop='.(int)Context::getContext()->shop->id);
    }
    public function restoreCategoryDefault($id_category_default)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'category_product` WHERE id_category="'.(int)$id_category_default.'" AND id_product='.(int)$this->id_product))
        {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product`(id_product,id_category) VALUES("'.(int)$id_category_default.'","'.(int)$this->id_product.'")');
        }
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}