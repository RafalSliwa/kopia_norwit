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
/**
 * Class AdminProductManagerAjaxController
 * @property Ets_productmanager $module
 */
class AdminProductManagerAjaxController extends ModuleAdminController
{
    public $_errors = array();
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function initContent()
    {
        parent::initContent();
        if(Tools::isSubmit('submitAssociatedFilesProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->updateDownloadProduct($id_product);
        }
        if(Tools::isSubmit('searchCustomer'))
        {
            $query = trim(Tools::getValue('q', false));
            if(empty($query) || !Validate::isCleanHtml($query))
                die();
            Ets_pmn_defines::getInstance()->displayAjaxCustomersList($query);
        }
        if(Tools::isSubmit('searchRelatedProduct'))
        {
            $query = trim(Tools::getValue('q', false));
            if (empty($query) || !Ets_productmanager::validateArray($query)) {
                die();
            }
            $excludeIds = Tools::getValue('excludeIds', false);
            $active = (int)Tools::getValue('active');
            Ets_pmn_defines::getInstance()->displayAjaxProductsList($query,$excludeIds,$active);
        }
        if(Tools::isSubmit('refreshProductSupplierCombinationForm') && ($id_product = (int)Tools::getValue('id_product')) && ($id_supplier = (int)Tools::getValue('id_supplier')))
        {
            die(
                json_encode(
                    array(
                        'html_form' => Ets_pmn_defines::getInstance()->refreshProductSupplierCombinationForm($id_supplier,$id_product)
                    )
                )
            );
        }
        if(Tools::isSubmit('submitCarriersProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitCarriersProduct($id_product);
        }
        if(Tools::isSubmit('submitDeliveryTimesProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitDeliveryTimesProduct($id_product);
        }
        if(Tools::isSubmit('submitCustomizationProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            if( ($custom_fields = Tools::getValue('custom_fields')) && Ets_productmanager::validateArray($custom_fields))
                Ets_pmn_defines::getInstance()->submitCustomizationProduct($id_product,$custom_fields);
            else
                Ets_pmn_defines::getInstance()->submitCustomizationProduct($id_product,array());
        }
        if(Tools::isSubmit('submitQuanittyProduct') && $id_product = (int)Tools::getValue('id_product'))
        {
            $this->_submitQuanittyProduct($id_product);
        }
        if(Tools::isSubmit('submitCombinationsProduct') && $id_product = (int)Tools::getValue('id_product'))
        {
            $this->submitCombinationsProduct($id_product);
        }
        if(Tools::isSubmit('submitSavecombinations') && ($attributes= Tools::getValue('list_product_attributes')) && Ets_productmanager::validateArray($attributes))
        {
            $this->_submitSavecombinations($attributes);
        }
        if(Tools::isSubmit('submitDeletecombinations') && ($attributes= Tools::getValue('list_product_attributes')) && Ets_productmanager::validateArray($attributes))
        {
            $id_product = (int)Tools::getValue('id_product');
            Ets_pmn_defines::getInstance()->submitDeletecombinations($attributes,$id_product);
        }
        if(Tools::isSubmit('submitDeleteProductAttribute') && ($id_product_attribute = (int)Tools::getValue('id_product_attribute')))
        {
            $this->_submitDeleteProductAttribute($id_product_attribute);
        }
        if(Tools::isSubmit('submitCreateCombination') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitCreateCombination($id_product);
        }
        if(Tools::isSubmit('submitFeatureProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitFeatureProduct($id_product);
        }
        if(Tools::isSubmit('submitCategoryProduct') && ($id_product= (int)Tools::getValue('id_product')))
        {
            $this->_submitCategoryProduct($id_product);
        }
        if(Tools::isSubmit('deleteImageProduct') && $id_image = (int)Tools::getValue('id_image'))
        {
            $this->_submitdeleteImageProduct($id_image);
        }
        if(Tools::isSubmit('submitImageProduct') && $id_image= (int)Tools::getValue('id_image'))
        {
            $this->_submitSaveImageProduct($id_image);
        }
        if(Tools::isSubmit('getFromImageProduct') && $id_image= (int)Tools::getValue('id_image'))
        {
            die(
                json_encode(
                    array(
                        'form_image' => $this->module->_getFromImageProduct($id_image),
                    )
                )
            );
        }
        if(Tools::isSubmit('submitUploadImageSave'))
        {
            $id_product = (int)Tools::getValue('id_product');
            $this->_submitUploadImageSave($id_product);
            if($this->_errors)
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->_errors),
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('etsGetFormPopupProduct') && ($id_product=(int)Tools::getValue('id_product')) && ($field= Tools::getValue('field')) && Validate::isGenericName($field))
        {
            $this->_getFormPopupProduct($id_product,$field); 
        }
        if(Tools::isSubmit('unitProductAction') && ($id_product= (int)Tools::getValue('id_product')) && ($action_field = Tools::getValue('action_field')) && in_array($action_field,array('active','on_sale','is_must_have')))
        {
            if($action_field=='active' || $action_field=='on_sale')
            {
                $product_class = new Product($id_product);
                $product_class->{$action_field} = (int)Tools::getValue('value_field');
                $product_class->update();
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
            
        }
        if(Tools::isSubmit('submitProductChangeInLine') && ($id_product = (int)Tools::getValue('id_product')) && ($product = new Product($id_product)) && Validate::isLoadedObject($product))
        {
            if($this->_checkBeforSubmitProduct())
            {
                $this->_submitProductChangeInLine($product);
            }
        }
        if(Tools::isSubmit('getFormEditInlineProduct') && $id_product = (int)Tools::getValue('id_product'))
        {
            $this->module->getFormEditInlineProduct($id_product);
        }
        if (Tools::isSubmit('arrangeproduct')) {
            $this->module->getFormArrangeProduct();
        }
        if(Tools::isSubmit('submitSuppliersProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->submitSuppliersProduct($id_product);
        }
        if(Tools::isSubmit('submitProductAttachment') && $id_product = (int)Tools::getValue('id_product'))
        {
            $this->_submitProductAttachment($id_product);
        }
        if(Tools::isSubmit('submitAddRemoveAttachment'))
        {
            $this->_submitAddRemoveAttachment();
        }
        if(Tools::isSubmit('submitSavePecificPrice') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitSavePecificPrice($id_product);
        }
        if(Tools::isSubmit('getFormSpecificPrice') && ($id_specific_price = (int)Tools::getValue('id_specific_price')))
        {
            $specific_price = new SpecificPrice($id_specific_price);
            die(
                json_encode(
                    array(
                        'form_html' => Ets_pmn_defines::getInstance()->renderSpecificPrice($specific_price->id_product,$specific_price->id),
                    )
                )
            );
        }
        if(Tools::isSubmit('submitDeleteSpecificPrice') && $id_specific_price = (int)Tools::getValue('id_specific_price'))
        {
            $this->_submitDeleteSpecificPrice($id_specific_price);
        }
        if(Tools::isSubmit('deletefileproduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitDeleteFileDownloadProduct($id_product);
        }
        if(Tools::isSubmit('submitRelatedProduct') && ($id_product = (int)Tools::getValue('id_product')) && ($related_products = Tools::getValue('related_products')) && Ets_productmanager::validateArray($related_products,'isInt') )
        {
            Ets_pmn_defines::getInstance()->_submitRelatedProduct($id_product,$related_products);
        }
        if(Tools::isSubmit('action') && Tools::getValue('action')=='updateImageOrdering' && ($images = Tools::getValue('images')) && Ets_productmanager::validateArray($images,'isInt'))
        {
            $id_product=(int)Tools::getValue('id_product');
            foreach($images as $key=> $id_image)
            {
                $image = new Image($id_image);
                $image->position = $key+1;
                $image->update();
                if(!$id_product)
                    $id_product = $image->id_product;
            }
            if($id_product)
                Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )  
            );
        }
        if(Tools::isSubmit('submitExtraTabProduct') && ($id_product = (int)Tools::getValue('id_product')) && ($id_tab_extra = (int)Tools::getValue('id_tab_extra')))
        {
            $this->_submitExtraTabProduct($id_product,$id_tab_extra);
        }
        if(Tools::isSubmit('submitDocumentProduct') && ($id_product = (int)Tools::getValue('id_product')))
        {
            $this->_submitDocumentProduct($id_product);
        }
        if(Tools::isSubmit('changePrivateNoteProduct'))
            $this->changePrivateNoteProduct();
    }
    public function _getFormPopupProduct($id_product,$field)
    {
        $html = '';
        if(Tools::strpos($field,'extra_product_tab_')===0 && ($id_tab = (int)str_replace('extra_product_tab_','',$field)))
            $html = $this->renderFormExtratabProducts($id_tab,$id_product);
        else
        switch ($field) {
          case 'image':
              $html = $this->module->renderFormImageProduct($id_product);
              break;
          case 'name_category':
          case 'categories':
              $html = $this->module->renderFormCategoryProduct($id_product);
              break;
          case 'features':
              $html = $this->module->renderFormFeatureProduct($id_product);
              break;
          case 'combinations':
              $html = $this->module->renderFormCombinations($id_product);
              break;
          case 'sav_quantity':
             $html = $this->module->renderFormQuantityProduct($id_product);
             break;
          case 'customization':
              $html = $this->module->renderFormCustomizationProduct($id_product);
              break; 
          case 'suppliers':
              $html = $this->module->renderFormSupplierProduct($id_product);
              break;
          case 'attached_files':
              $html = $this->module->renderFormAttachedFiles($id_product);
              break; 
          case 'specific_prices':
              $html = $this->rederFormSpecificPrice($id_product);
              break;  
          case 'associated_file':
              $html = $this->renderAssociatedFile($id_product);
              break;   
          case 'related_product':
              $html = $this->renderFormRelatedProduct($id_product);
              break;  
          case 'module_logo':
            $html = $this->renderFormUploadLogoModule($id_product);
            break;
          case 'doc_name':
            $html = $this->renderFormUploadDocumentModule($id_product);
            break;
           case 'additional_delivery_times':
                $html = $this->renderFormDeliveryTimesProduct($id_product);
                break; 
           case 'selectedCarriers':
                $html = $this->renderFormCarriersProduct($id_product);
                break;
        } 
        die(
            json_encode(
                array(
                    'html_form'=>$html,
                )
            )
        );
    }
    
    public function _checkBeforSubmitProduct()
    {
        if(!$this->module->active)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->l('You must enable Product Manager module to configure its features')),
                    )
                )
            );
        }
        $id_lang_default =Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $list_fields = array_keys(Ets_pmn_defines::getInstance()->getProductListFields());
        if(in_array('name',$list_fields))
        {
            $name_default = Tools::getValue('name_'.$id_lang_default);
            if(!trim($name_default))
            {
                $this->_errors[] = $this->l('Product name is required');
            }
            else
            {
                foreach($languages as $language)
                {
                    if(($name = Tools::getValue('name_'.$language['id_lang'])) && !Validate::isCatalogName($name))
                        $this->_errors[] = $this->l('Product name is not valid in').' '.$language['iso_code'];
                }
            }
        }
        if(in_array('link_rewrite',$list_fields))
        {
            $link_rewrite_default = Tools::getValue('link_rewrite_'.$id_lang_default);
            if(!trim($link_rewrite_default))
                $this->_errors[] = $this->l('Product link rewrite is required');
            else{
                foreach($languages as $language)
                {
                    if(($link_rewrite = Tools::getValue('link_rewrite_'.$language['id_lang'])) && !Validate::isLinkRewrite($link_rewrite))
                        $this->_errors[] = $this->l('Product link rewrite is not valid in').' '.$language['iso_code'];
                }
            }
        }
        if(in_array('reference',$list_fields) && ($reference = Tools::getValue('reference')) && !Validate::isReference($reference) )
        {
            $this->_errors[] = $this->l('Reference is not valid');
        }
        if(in_array('minimal_quantity',$list_fields) && ($minimal_quantity = Tools::getValue('minimal_quantity')) && !Validate::isUnsignedInt($minimal_quantity))
        {
            $this->_errors[] = $this->l('Minimum quantity for sale is not valid');
        }
        if(in_array('location',$list_fields) && ($location = Tools::getValue('location')) && (!Validate::isReference($location) || Tools::strlen($location)>64))
            $this->_errors[] = $this->l('Location is not valid');
        if(in_array('low_stock_threshold',$list_fields) && ($low_stock_threshold = Tools::getValue('low_stock_threshold')) && !Validate::isInt($low_stock_threshold))
            $this->_errors[] = $this->l('Low stock level is not valid');
        if(in_array('available_date',$list_fields) && ($available_date = Tools::getValue('available_date')) && !Validate::isDateFormat($available_date))
        {
            $this->_errors[] = $this->l('Availability date is not valid');
        }
        if(in_array('price',$list_fields))
        {
            if(($price = Tools::getValue('price')) && !Validate::isPrice($price))
                $this->_errors[] = $this->l('Price (tax excl.) is not valid');
        }
        if(in_array('price_final',$list_fields))
        {
            if(($price_final = Tools::getValue('price_final')) && !Validate::isPrice($price_final))
                $this->_errors[] = $this->l('Price (tax incl.) is not valid');
        }
        if(in_array('unit_price',$list_fields))
        {
            if(($unit_price = Tools::getValue('unit_price')) && !Validate::isPrice($unit_price))
                $this->_errors[] = $this->l('Price per unit is not valid');
        }
        if(in_array('isbn',$list_fields))
        {
            if(($isbn = Tools::getValue('isbn')) && (!Validate::isIsbn($isbn) || Tools::strlen($isbn) >32))
                $this->_errors[] = $this->l('ISBN is not valid');
        }
        if(in_array('ean13',$list_fields))
        {
            if(($ean13 = Tools::getValue('ean13')) && (!Validate::isEan13($ean13) || Tools::strlen($ean13)>13))
                $this->_errors[] = $this->l('EAN-13 is not valid');
        }
        if(in_array('upc',$list_fields))
        {
            if(($upc = Tools::getValue('upc')) && (!Validate::isUpc($upc) || Tools::strlen($upc)>12))
                $this->_errors[] = $this->l('UPC barcode is not valid');
        }
        if(in_array('sav_quantity',$list_fields) && Tools::isSubmit('sav_quantity') && ($sav_quantity = Tools::getValue('sav_quantity')) && (!Validate::isInt($sav_quantity) || Tools::strlen($sav_quantity)>10))
        {
            $this->_errors[] = $this->l('Quantity is not valid');
        }
        if(in_array('version',$list_fields) && Tools::isSubmit('version') && ($version = Tools::getValue('version')) && !Validate::isString($version))
            $this->_errors[] = $this->l('Version is not valid');
        if(in_array('min_ps_version',$list_fields) && Tools::isSubmit('min_ps_version') && ($min_ps_version = Tools::getValue('min_ps_version')) && !preg_match('/^\d+\.\d+([\d\.]*)$/', $min_ps_version))
            $this->_errors[] = $this->l('Min PS version is not valid');
        if(in_array('max_ps_version',$list_fields) && Tools::isSubmit('max_ps_version') && ($max_ps_version = Tools::getValue('max_ps_version')) && !preg_match('/^\d+\.\d+([\d\.]*)$/', $max_ps_version))
            $this->_errors[] = $this->l('Max PS version is not valid');
        if(in_array('compatibility',$list_fields) && Tools::isSubmit('compatibility') && ($compatibility = Tools::getValue('compatibility')) && !Validate::isString($compatibility))
            $this->_errors[] = $this->l('Compatible with is not valid');
        if(in_array('width',$list_fields) && Tools::isSubmit('width') && ($width = Tools::getValue('width')) && !Validate::isUnsignedFloat($width))
            $this->_errors[] = $this->l('Width is not valid');
        if(in_array('height',$list_fields) && Tools::isSubmit('height') && ($height = Tools::getValue('height')) && !Validate::isUnsignedFloat($height))
            $this->_errors[] = $this->l('Height is not valid');
        if(in_array('depth',$list_fields) && Tools::isSubmit('depth') && ($depth = Tools::getValue('depth')) && !Validate::isUnsignedFloat($depth))
            $this->_errors[] = $this->l('Depth is not valid');
        if(in_array('weight',$list_fields) && Tools::isSubmit('weight') && ($weight = Tools::getValue('weight')) && !Validate::isUnsignedFloat($weight))
            $this->_errors[] = $this->l('Weight is not valid');
        if(in_array('additional_shipping_cost',$list_fields) && Tools::isSubmit('additional_shipping_cost') && ($additional_shipping_cost = Tools::getValue('additional_shipping_cost')) && !Validate::isPrice($additional_shipping_cost))
            $this->_errors[] = $this->l('Shipping fee is not valid');
        foreach($languages as $language)
        {
            if(in_array('description_short',$list_fields))
            {
                if(($description_short = Tools::getValue('description_short_'.$language['id_lang'])) && !Validate::isCleanHtml($description_short))
                    $this->_errors[] = sprintf($this->l('Summary is not valid in %s'),$language['iso_code']);
                $short_description_limit= Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? :800;
                if(Tools::strlen(strip_tags($description_short))> $short_description_limit)
                    $this->_errors[]= sprintf($this->l('[%s] Summary is too long. It should have %d characters or less.'),$language['iso_code'],$short_description_limit);
            }
            if(in_array('delivery_in_stock',$list_fields))
            {
                if(($delivery_in_stock = Tools::getValue('delivery_in_stock_'.$language['id_lang'])) && !Validate::isGenericName($delivery_in_stock))
                    $this->_errors[] = sprintf($this->l('Delivery time of in-stock is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($delivery_in_stock)> 255)
                    $this->_errors[]= sprintf($this->l('[%s] Delivery time of in-stock is too long. It should have 255 characters or less.'));
            }
            if(in_array('delivery_out_stock',$list_fields))
            {
                if(($delivery_out_stock = Tools::getValue('delivery_out_stock_'.$language['id_lang'])) && !Validate::isGenericName($delivery_out_stock))
                    $this->_errors[] = sprintf($this->l('Delivery time of out-of-stock is not valid in %d'),$language['iso_code']);
                elseif(Tools::strlen($delivery_out_stock)> 255)
                    $this->_errors[]= sprintf($this->l('[%s] Delivery time of out-of-stock is too long. It should have 255 characters or less.'));
            }
            if(in_array('description',$list_fields))
            {
                if(($description = Tools::getValue('description_'.$language['id_lang'])) && !Validate::isCleanHtml($description))
                    $this->_errors[] = sprintf($this->l('Description is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($description)>21844)
                    $this->_errors[]= sprintf($this->l('[%s] Description is too long. It should have 21844 characters or less.'),$language['iso_code']);
            }
            if(in_array('meta_title',$list_fields))
            {
                if(($meta_title = Tools::getValue('meta_title_'.$language['id_lang'])) && !Validate::isGenericName($meta_title))
                    $this->_errors[] = sprintf($this->l('Meta title is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($meta_title) >255)
                    $this->_errors[]= sprintf($this->l('[%s] Meta title is too long. It should have 255 characters or less.'),$language['iso_code']);
            }
            if(in_array('meta_description',$list_fields))
            {
                if(($meta_description = Tools::getValue('meta_description_'.$language['id_lang'])) && !Validate::isGenericName($meta_description))
                    $this->_errors[] = sprintf($this->l('Meta description is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($meta_description) >512)
                    $this->_errors[]= sprintf($this->l('[%s] Meta description is too long. It should have 512 characters or less.'),$language['iso_code']);
            }
            if(in_array('available_now',$list_fields))
            {
                if(($available_now = Tools::getValue('available_now_'.$language['id_lang'])) && !Validate::isGenericName($available_now))
                    $this->_errors[] = sprintf($this->l('Label when in-stock is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($available_now) >255)
                    $this->_errors[]= sprintf($this->l('[%s] Label when in-stock is too long. It should have 255 characters or less.'),$language['iso_code']);
                    
            }
            if(in_array('available_later',$list_fields))
            {
                if(($available_later = Tools::getValue('available_later_'.$language['id_lang'])) && !Validate::isGenericName($available_later))
                    $this->_errors[] = sprintf($this->l('Label when out of stock is not valid in %s'),$language['iso_code']);
                elseif(Tools::strlen($available_later)>255)
                    $this->_errors[]= sprintf($this->l('[%s] Label when out of stock is too long. It should have 255 characters or less.'),$language['iso_code']);
                    
            }
            if(in_array('module_name',$list_fields) && ($module_name= Tools::getValue('module_name_'.$language['id_lang'])) && !Validate::isString($module_name))
                $this->_errors[] = $this->l('Display name is not valid in').' '.$language['iso_code'];
            if(in_array('module_description',$list_fields) && ($module_description= Tools::getValue('module_description_'.$language['id_lang'])) && !Validate::isString($module_description))
                $this->_errors[] = $this->l('Module description is not valid in').' '.$language['iso_code'];
            if(in_array('fo_link',$list_fields) && ($fo_link= Tools::getValue('fo_link_'.$language['id_lang'])) && !$this->module->isDomain($fo_link))
                $this->_errors[] = $this->l('FO Demo is not valid in').' '.$language['iso_code'];
            if(in_array('bo_link',$list_fields) && ($bo_link= Tools::getValue('bo_link_'.$language['id_lang'])) && !$this->module->isDomain($bo_link))
                $this->_errors[] = $this->l('Max PS version is not valid in').' '.$language['iso_code'];
            if(in_array('priority_product',$list_fields) && ($priority_product = Tools::getValue('priority_product')) && !Validate::isFloat($priority_product)) 
                $this->_errors[] = $this->l('Priority is not valid');
            if(in_array('tags',$list_fields))
            {
                if($tags = Tools::getValue('tags_'.$language['id_lang']))
                {
                    foreach(explode(',',$tags) as $tag)
                    {
                        if($tag && !Validate::isGenericName($tag))
                        {
                            $this->_errors[] = $this->l('Tags are not valid in').' '.$language['iso_code'];
                            break;
                        }
                    }
                }
            }
            if(in_array('focus_keyphrase',$list_fields))
            {
                $focus_keyphrase = Tools::getValue('focus_keyphrase_'.$language['id_lang']);
                if($focus_keyphrase && !Validate::isString($focus_keyphrase))
                    $this->_errors[] = $this->l('Focus key phrase is not valid in').' '.$language['iso_code'];
            }
            if(in_array('related_keyphrases',$list_fields))
            {
                $related_keyphrases = Tools::getValue('related_keyphrases_'.$language['id_lang']);
                $focus_keyphrase = Tools::getValue('focus_keyphrase_'.$language['id_lang']);
                if($related_keyphrases && !Validate::isString($related_keyphrases))
                    $this->_errors[] = $this->l('Related key phrases are not valid in').' '.$language['iso_code'];
                elseif(in_array('focus_keyphrase',$list_fields) && $related_keyphrases && $related_keyphrases == $focus_keyphrase)
                    $this->_errors[] = $this->l('The related key phrase is the same as focus key phrase in').' '.$language['iso_code'];
            }
        }
        if(Module::isEnabled('ets_extraproducttabs'))
        {
            $fields = Ets_pmn_defines::getInstance()->getProductListFields();
            foreach($fields as $key=> $field)
            {
                if(Tools::strpos($key,'extra_product_tab')===0)
                {
                    foreach($languages as $language)
                    {
                        if (($extra= Tools::getValue($key.'_'.$language['id_lang'])) && !Validate::isString($extra))
                            $this->_errors[] = $field['title'].' '.$this->l('is not valid in').' '.$language['iso_code'];
                    }
                    
                }
            }
        }
        if(!$this->_errors)
            return true;
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->_errors),
                    )
                )
            );  
        }
    }
    public function _submitProductChangeInLine($product)
    {
        $id_lang_default =Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $list_fields = Ets_pmn_defines::getInstance()->getProductListFields();
        $columns = array();
        if($list_fields)
        {
            foreach($list_fields as $key => $field)
            {
                if($key !='tax_name' && property_exists($product,$key) && isset($field['input']) && ($input = $field['input']))
                {
                    
                    if(isset($input['lang']) && $input['lang'])
                    {
                        foreach($languages as $language)
                        {
                            $product->{$key}[$language['id_lang']] = Tools::getValue($key.'_'.$language['id_lang']) ?: Tools::getValue($key.'_'.$id_lang_default);
                        }
                        if($key == 'description_short')
                        {
                            $description_short = $product->description_short[$this->context->language->id];
                            if($this->module->str_length && Tools::strlen(strip_tags($description_short)) > $this->module->str_length)
                                $description_short = Tools::substr(strip_tags($description_short),0,$this->module->str_length).'...';
                            else
                                $description_short = strip_tags($description_short);
                            $columns[] = array(
                                'name' => $key,
                                'value' => $description_short,
                            );    
                        }
                        elseif($key=='description')
                        {
                            $description = $product->description[$this->context->language->id];
                            if($this->module->str_length && Tools::strlen(strip_tags($description)) > $this->module->str_length)
                                $description = Tools::substr(strip_tags($description),0,$this->module->str_length).'...';
                            else
                                $description = strip_tags($description);
                            $columns[] = array(
                                'name' => $key,
                                'value' => $description,
                            ); 
                        }
                        else
                        {
                            
                            $columns[] = array(
                                'name' => $key,
                                'value' =>$key=='name' ? Ets_pmn_defines::displayText($product->{$key}[$this->context->language->id],'a',array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product->id)).'#tab-step1')) : $product->{$key}[$this->context->language->id],
                            );
                        }
                    }
                    else
                    {
                        if($key=='price')
                        {
                            $product->price = (float)Tools::getValue('price');
                            $columns[] = array(
                                'name' => $key,
                                'value' => Ets_pmn_defines::displayText(
                                Tools::displayPrice($product->price),
                                'a',
                                array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product->id)).'#tab-step2')) ,
                            );
                        }
                        elseif($key =='wholesale_price')
                        {
                            $product->wholesale_price = (float)Tools::getValue('wholesale_price');
                            $columns[] = array(
                                'name' => $key,
                                'value' => Ets_pmn_defines::displayText(
                                Tools::displayPrice($product->wholesale_price),
                                'a',
                                array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product->id)).'#tab-step2')) ,
                            );
                        }
                        else
                        {
                            $product->{$key} = Tools::getValue($key);
                            $value_key =$product->{$key};
                            if( $input['type']=='select' && isset($input['values']['query']) && ($values = $input['values']) && ($queries = $values['query']))
                            {
                                foreach($queries as $query)
                                {
                                    if($query[$values['id']]==Tools::getValue($key))
                                    {
                                        $value_key =$query[$values['name']];
                                        break;
                                    }
                                }
                            }
                            if($key=='width' || $key=='height' || $key=='depth')
                            {
                                $columns[] = array(
                                'name' => $key,
                                    'value' => $product->{$key}!=0 ? $product->{$key}.'(cm)':'--',
                                );
                            }
                            elseif($key=='weight')
                                $columns[] = array(
                                    'name' => $key,
                                    'value' => $product->{$key}!=0 ? $product->{$key}.'(kg)':'--'
                                );
                            else
                                $columns[] = array(
                                    'name' => $key,
                                    'value' => $key=='additional_shipping_cost' ? Tools::displayPrice($product->{$key}) : $value_key,
                                );
                        }
                        
                    }
                    if($key=='available_now')
                    {
                        $product->low_stock_alert = (int)Tools::getValue('low_stock_alert');
                    }
                    if(Tools::isSubmit('low_stock_alert'))
                        $product->low_stock_alert = (int)Tools::getValue('low_stock_alert');
                    if($key=='visibility')
                    {
                        $product->available_for_order = (int)Tools::getValue('available_for_order');
                        $product->show_price = (int)Tools::getValue('show_price');
                        $product->online_only = (int)Tools::getValue('online_only');
                    }
                    if($key=='condition')
                    {
                        $product->show_condition = Tools::getValue('show_condition');
                    }
                }
                elseif($key=='tax_name')
                {
                    $product->id_tax_rules_group = (int)Tools::getValue('tax_name');
                    $columns[] = array(
                        'name' => $key,
                        'value' => $product->id_tax_rules_group ? (new TaxRulesGroup($product->id_tax_rules_group,$this->context->language->id))->name: $this->l('No tax'),
                    );
                }
                elseif($key=='manufacturers')
                {
                    $product->id_manufacturer = (int)Tools::getValue('manufacturers');
                    $columns[] = array(
                        'name' => $key,
                        'value' => (new Manufacturer($product->id_manufacturer,$this->context->language->id))->name,
                    );
                }
                elseif($key =='price_final')
                {
                    $id_tax_rules_group = (int)Tools::getValue('tax_name',$product->id_tax_rules_group);
                    if(!isset($list_fields['price']))
                        $product->price = (float)Tools::ps_round(Tools::getValue($key)/(1+$this->module->getTaxValue($id_tax_rules_group)),6);
                    $price_final = (float)Tools::getValue('price_final');
                    $columns[] = array(
                        'name' => $key,
                        'value' => Ets_pmn_defines::displayText(Tools::displayPrice($price_final),'a',array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product->id)).'#tab-step2')) ,
                    );
                }
                if($key=='tags')
                {
                    foreach($languages as $language)
                    {
                        if(($tags = Tools::getValue('tags_'.$language['id_lang'])) && Validate::isCleanHtml($tags))
                        {
                            Ets_pmn_defines::getInstance()->deleteProducttag($product->id,$language['id_lang']);
                            Tag::addTags($language['id_lang'],$product->id,$tags);
                        }
                        else
                            Ets_pmn_defines::getInstance()->deleteProducttag($product->id,$language['id_lang']);
                    }
                    $columns[] = array(
                        'name' => $key,
                        'value' => Ets_pmn_defines::getInstance()->getListtags($product->id),
                    );
                }
                if($key=='private_note')
                {
                    $private_note = Tools::getValue('private_note');
                    if(!$private_note || Validate::isCleanHtml($private_note))
                    {
                        Ets_pmn_defines::updateProductNote($product->id,$private_note);
                        $columns[] = array(
                            'name' => $key,
                            'value' =>$private_note,
                        );
                    }
                }
            }
            
            if(isset($list_fields['unit_price']))
            {
                $product->unit_price = (float)Tools::getValue('unit_price');
                $columns[] = array(
                    'name' => 'unit_price',
                    'value' => Tools::displayPrice($product->unit_price) ,
                );
            }
        }
        if($product->update())
        {
            if(Tools::isSubmit('sav_quantity') && ($sav_quantity = Tools::getValue('sav_quantity'))!='' && Validate::isInt($sav_quantity))
            {
                StockAvailable::setQuantity($product->id, 0, $sav_quantity);
                StockAvailable::setProductOutOfStock($product->id,StockAvailable::getStockAvailableIdByProductId($product->id) ? StockAvailable::outOfStock($product->id): $product->out_of_stock);
                $columns[] = array(
                    'name' => 'sav_quantity',
                    'value' => Ets_pmn_defines::displayText($sav_quantity,'a',array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product->id)).'#tab-step3')),
                );
            }
            if(Tools::isSubmit('location') && ($location = Tools::getValue('location'))!='' && Validate::isReference($location))
            {
                if(method_exists('StockAvailable','setLocation'))
                {
                    Ets_pmn_defines::setLocation($product->id,$location);
                    $columns[] = array(
                        'name' => 'location',
                        'value' => $location,
                    );
                }
            }
            $this->updateExtraField($product->id,$list_fields,$columns);
            if(Module::isEnabled('ets_seo') && (isset($list_fields['readability_score']) || isset($list_fields['seo_score'])))
            {
                $seoproducts = Module::getInstanceByName('ets_seo')->modifyResultList('product',array(array('id_product'=>$product->id)));
                if($seoproducts)
                {
                    if(isset($list_fields['readability_score']))
                    {
                        $columns[] = array(
                            'name' => 'readability_score',
                            'value' => $seoproducts[0]['readability_score']
                        );
                    }
                    if(isset($list_fields['seo_score']))
                    {
                        $columns[] = array(
                            'name' => 'seo_score',
                            'value' => $seoproducts[0]['seo_score']
                        );
                    }
                }
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Product updated successfully'),
                        'columns' => $columns,
                    )
                )
            );
        }
        
    }
    public function updateExtraField($id_product,$list_fields,&$columns)
    {
        $languages = Language::getLanguages(false);
        $id_lang = $this->context->language->id;
        foreach($list_fields as $key=> $field)
        {
            if($key=='focus_keyphrase')
            {
                foreach($languages as $language)
                {
                    $focus_keyphrase = Tools::getValue('focus_keyphrase_'.$language['id_lang']);
                    if($focus_keyphrase && Validate::isCleanHtml($focus_keyphrase))
                    {
                        Ets_pmn_defines::updateFocusKeyphrase($id_product,$language['id_lang'],$focus_keyphrase);
                    }
                }
                $focus_keyphrase = Tools::getValue('focus_keyphrase_'.$id_lang);
                $columns[] = array(
                    'name' => 'focus_keyphrase',
                    'value' =>$focus_keyphrase,
                );
            }
            if($key=='related_keyphrases')
            {
                foreach($languages as $language)
                {
                    $minor_key_phrase = Tools::getValue('related_keyphrases_'.$language['id_lang']);
                    if($minor_key_phrase && Validate::isCleanHtml($minor_key_phrase))
                    {
                        Ets_pmn_defines::updateMinorKeyPhrase($id_product,$language['id_lang'],$minor_key_phrase);
                    }
                }
                $minor_key_phrase = Tools::getValue('related_keyphrases_'.$id_lang);
                $columns[] = array(
                    'name' => 'related_keyphrases',
                    'value' =>$minor_key_phrase,
                );
            }
            if($key=='priority_product' &&  Module::isEnabled('ph_sortbytrending'))
            {
                $priority_product = (float)Tools::getValue('priority_product');
                Ets_pmn_defines::updatePriorityProduct($id_product,$priority_product);
                $columns[] = array(
                    'name' => $key,
                    'value' =>$priority_product,
                );
            }
        }
        if(Module::isEnabled('ets_customfields'))
        {
            Module::getInstanceByName('ets_customfields')->updateExtraField($id_product,$list_fields,$columns);
        }
    }
    public function _submitQuanittyProduct($id_product)
    {
        $quantities = Tools::getValue('quantities');
        $sav_quantity = 0;
        if($quantities && Ets_productmanager::validateArray($quantities,'isInt'))
        {
            foreach($quantities as $quantity)
            {
                if(!Validate::isUnsignedInt($quantity))
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($this->l('Quantity is not valid')),
                            )
                        )
                    );
                }
            }
            foreach($quantities as $id_product_attribute => $quantity)
            {
                $combination = new Combination($id_product_attribute);
                if(Validate::isLoadedObject($combination) && $combination->id_product = $id_product)
                {
                    $combination->quantity = $quantity;
                    if($combination->update())
                    {
                        $sav_quantity +=$quantity;
                        StockAvailable::setQuantity($id_product, (int)$id_product_attribute, $combination->quantity);
                    }
                }
            }
        }
        Ets_pmn_defines::updateQuantityProduct($id_product);
        Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
        die(
            json_encode(
                array(
                    'success' => $this->l('Updated successfully'),
                    'row_value' => Ets_pmn_defines::displayText($sav_quantity,'a',array('href'=>$this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$id_product)).'#tab-step3')),
                    'row_name' => 'sav_quantity',
                    'list_combinations' =>Ets_pmn_defines::getInstance()->getListCombinations($id_product),
                )
            )
        );
    }
    public function _submitSavecombinations($attributes)
    {
        $id_product = (int)Tools::getValue('id_product');
        $data = Tools::getValue('product_combination_bulk');
        if(Ets_productmanager::validateArray($data))
        {
            if($data['quantity'] && !Validate::isInt($data['quantity']))
                $this->_errors[] = $this->l('Quantity is not valid');
            if($data['cost_price'] && !Validate::isPrice($data['cost_price']))
                $this->_errors[] = $this->l('Cost price is not valid');
            if($data['impact_on_price_te'] && !Validate::isPrice($data['impact_on_price_te']))
                $this->_errors[] = $this->l('Impact on price is not valid');
            if($data['impact_on_weight'] && !Validate::isFloat($data['impact_on_weight']))
                $this->_errors[] = $this->l('Impact on weight is not valid');
            if($data['date_availability'] && !Validate::isDate($data['date_availability']))
                $this->_errors[] = $this->l('Availability date is not valid');
            if($data['reference'] && !Validate::isReference($data['reference']))
                $this->_errors[] = $this->l('Reference is not valid');
            if($data['minimal_quantity'] && !Validate::isUnsignedInt($data['minimal_quantity']))
                $this->_errors[] = $this->l('Minimum quantity is not valid');
            if($data['low_stock_threshold'] && !Validate::isInt($data['low_stock_threshold']))
                $this->_errors[] = $this->l('Low stock level is not valid');
        }
        else
            $this->_errors[] = $this->l('Data post is not valid');
        if(!$this->_errors)
        {
            foreach($attributes as  $id_product_attribute)
            {
                $combination = new Combination($id_product_attribute);
                $combination->quantity = (int)$data['quantity'];
                $combination->minimal_quantity = (int)$data['minimal_quantity'];
                $combination->cost_price = (float)$data['cost_price'];
                $combination->price= (float)$data['impact_on_price_te'];
                $combination->weight = (float)$data['impact_on_weight'];
                $combination->available_date = $data['date_availability'];
                $combination->reference = $data['reference'];
                $combination->low_stock_threshold = (int)$data['low_stock_threshold'];
                $combination->low_stock_alert = isset($data['low_stock_alert'])? (int)$data['low_stock_alert']:0;
                if($combination->update())
                    StockAvailable::setQuantity($combination->id_product, (int)$id_product_attribute, $combination->quantity);
            }
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'list_combinations' =>Ets_pmn_defines::getInstance()->displayListCombinations($id_product),
                        'combinations' => Ets_pmn_defines::getInstance()->getListCombinations($id_product),
                        'sav_quantity' => Ets_pmn_defines::getInstance()->getQuantityProduct($id_product),
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->_errors),
                    )
                )
            );
        }
    }
    public function _submitDeleteProductAttribute($id_product_attribute)
    {
        $productAttribute = new Combination($id_product_attribute);
        if($productAttribute->delete())
        {
            Hook::exec('actionProductUpdate',array('product' => new Product($productAttribute->id_product),'id_product'=>$productAttribute->id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Deleted successfully'),
                        'combinations' => Ets_pmn_defines::getInstance()->getListCombinations($productAttribute->id_product),
                        'sav_quantity'=>Ets_pmn_defines::getInstance()->getQuantityProduct($productAttribute->id_product),
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->l('An error occurred while deleting the attribute'),
                    )  
                )
            );
        }  
    }
    protected function addAttribute($attributes, $price = 0, $weight = 0)
    {
        $id_product = (int)Tools::getValue('id_product');
        if ($id_product) {
            return array(
                'id_product' => $id_product,
                'price' => (float)$price,
                'weight' => (float)$weight,
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
    public function _submitCreateCombination($id_product)
    {
        $attribute_options = Tools::getValue('attribute_options');
        $tab = Ets_productmanager::validateArray($attribute_options) ? array_values($attribute_options) : array();
        $product = new Product($id_product);
        $product_type = $product->getType();
        if($product_type== Product::PTYPE_SIMPLE)
        {
            $attributes = Product::getAttributesInformationsByProduct($product->id);
            if (count($tab) && Validate::isLoadedObject($product)) {
                $combinations = array_values(Ets_productmanager::createCombinations($tab));
                $values = array_values(array_map(array($this, 'addAttribute'), $combinations));
                SpecificPriceRule::disableAnyApplication();
                $product->generateMultipleCombinations($values, $combinations,false);
                Product::getDefaultAttribute($product->id, 0, true);
                Product::updateDefaultAttribute($product->id);
                StockAvailable::synchronize($product->id);
                SpecificPriceRule::enableAnyApplication();
                SpecificPriceRule::applyAllRules(array((int)$product->id));
                if(empty($attributes))
                    StockAvailable::setQuantity($id_product,0,0);
                Hook::exec('actionProductUpdate',array('product' => $product,'id_product'=>$product->id));
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Generated attribute successfully'),
                            'list_combinations' => Ets_pmn_defines::getInstance()->displayListCombinations($id_product),
                            'combinations' => Ets_pmn_defines::getInstance()->getListCombinations($id_product),
                            'sav_quantity' => Ets_pmn_defines::getInstance()->getQuantityProduct($id_product),
                        )
                    )
                );
            } else {
                $this->_errors[] = $this->l('Unable to initialize these parameters. A combination is missing or an object cannot be loaded.');
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->_errors)
                        )
                    )  
                );
            }
        }
        else
        {
            $this->_errors[] = $this->l('Product is not valid');
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->_errors)
                    )
                )  
            );
        }
    }
    public function _submitFeatureProduct($id_product)
    {
        $id_features = Tools::getValue('id_features');
        $id_feature_values = Tools::getValue('id_feature_values');
        $feature_value_custom = Tools::getValue('feature_value_custom');
        $errors = array();
        if($id_features && Ets_productmanager::validateArray($id_features) && Ets_productmanager::validateArray($id_feature_values) && Ets_productmanager::validateArray($feature_value_custom))
        {
            foreach($id_features as $index => $id_feature)
            {
                if($id_feature && (!isset($id_feature_values[$index]) || !$id_feature_values[$index]) && (!isset($feature_value_custom[$index]) || !$feature_value_custom[$index]))
                {
                    $errors[] = $this->l('Feature value is required');
                    break;
                }
                if($id_feature && (!isset($id_feature_values[$index]) || !$id_feature_values[$index]) && isset($feature_value_custom[$index]) && $feature_value_custom[$index] && !Validate::isGenericName($feature_value_custom[$index]))
                {
                    $errors[] = $this->l('Feature value is required');
                    break;
                }
                
            }
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
        else
        {
            Ets_pmn_defines::getInstance()->_saveFeatureProduct($id_product,$id_features,$id_feature_values,$feature_value_custom);
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'row_value'=> Ets_pmn_defines::getInstance()->getListFeatures($id_product),
                        'row_name' => 'features',
                        'id_product' => $id_product
                    )
                )
            );
        }
    }
    public function _submitDeliveryTimesProduct($id_product)
    {
        $errors = array();
        $product = new Product($id_product);
        if(!Validate::isLoadedObject($product))
            $errors[] = $this->l('Product is not valid');
        $additional_delivery_times = (int)Tools::getValue('additional_delivery_times');
        if(!$errors)
        {
            $product->additional_delivery_times = (int)$additional_delivery_times;
            if($product->update())
            {
                if($additional_delivery_times==1)
                    $value = $this->l('Default delivery time');
                elseif($additional_delivery_times==2)
                    $value = $this->l('Specific delivery time to this product');
                else
                    $value = $this->l('None');
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfully'),
                            'row_value'=> $value,
                            'row_name' => 'additional_delivery_times',
                            'id_product' => $id_product
                        )
                    )
                );
            }
            else
                $errors[] = $this->l('An error occurred while saving the product');
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
    }
    public function _submitCarriersProduct($id_product)
    {
        $product = new Product($id_product);
        $errors = array();
        if(!Validate::isLoadedObject($product))
            $errors[] = $this->l('Product is not valid');
        $selectedCarriers = Tools::getValue('selectedCarriers');
        if($selectedCarriers && !Ets_productmanager::validateArray($selectedCarriers,'isInt'))
            $errors[] = $this->l('Available carriers are not valid');
        if(!$errors)
        {
            Ets_pmn_defines::getInstance()->_saveCarriersProduct($id_product,$selectedCarriers);
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'row_value'=> $this->module->displayListCarriers($id_product),
                        'row_name' => 'selectedCarriers',
                        'id_product' => $id_product
                    )
                )
            );
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
    }
    public function _submitCategoryProduct($id_product)
    {
        $errors = array();
        if(!$id_categories = Tools::getValue('id_categories'))
            $errors[] = $this->l('Category is required');
        elseif(!is_array($id_categories) || !Ets_productmanager::validateArray($id_categories,'isInt'))
        {
            $errors[] = $this->l('Category is not valid');
        }
        elseif(!$id_category_default = (int)Tools::getValue('id_category_default'))
            $errors[] = $this->l('Default category is required');
        elseif(!in_array($id_category_default,$id_categories))
            $errors[] = $this->l('Default category is not valid');
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
        else
        {
            $product = new Product($id_product);
            $product->id_category_default = $id_category_default;
            if($product->update())
            {
                Ets_pmn_defines::getInstance()->_saveCategoryProduct($id_product,$id_categories);
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Updated successfully'),
                            'row_value' => (new Category($id_category_default,$this->context->language->id))->name,
                            'row_name' => 'name_category',
                            'categories'=> Ets_pmn_defines::getInstance()->getListCategories($id_product),
                            'id_product' => $id_product
                        )
                    )
                );
            }
        }
    }
    public function _submitdeleteImageProduct($id_image)
    {
        $image = new Image($id_image);
        if(Validate::isLoadedObject($image))
        {
            if($image->delete())
            {
                $is_cover =false;
                $product = new Product($image->id_product,false,$this->context->language->id);
                if($image->cover)
                {
                    $is_cover = true;
                    if($images = Ets_pmn_defines::getInstance()->getListImages($image->id_product))
                    {
                        $id_image_new = $images[0]['id_image'];
                        $newImage = new Image($id_image_new);
                        $newImage->cover=1;
                        $newImage->update();
                    }
                }
                Hook::exec('actionProductUpdate',array('product' => new Product($image->id_product),'id_product'=>$image->id_product));
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Deleted image successfully'),
                            'id_image' => $image->id,
                            'is_cover' => $is_cover,
                            'link' => isset($newImage) ? $this->context->link->getImageLink($product->link_rewrite,$newImage->id) :'',
                            'id_new_image' => isset($id_image_new) ? $id_image_new :''
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' => $this->module->displayError($this->l('An error occurred while deleting the image')),
                        )
                    )
                );
            }
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->l('Image is not valid')),
                    )
                )
            );
        }
    }
    public function _submitSaveImageProduct($id_image)
    {
        $image = new Image($id_image);
        $id_product = (int)Tools::getValue('id_product');
        if(Validate::isLoadedObject($image) && $image->id_product == $id_product)
        {
            $languages = Language::getLanguages(false);
            $errors = array();
            $product = new Product($id_product,false,$this->context->language->id);
            $image_cover = (int)Tools::getValue('image_cover');
            foreach($languages as $language)
            {
                $legend = strip_tags(Tools::getValue('legend_'.$language['id_lang']));
                if(Tools::strlen($legend)<128 && Validate::isCleanHtml($legend))
                    $image->legend[$language['id_lang']] = $legend;
                else
                    $errors[] = $this->l('Image caption is not valid in').' '.$language['iso_code'];
            }
            if(!$errors &&  $image_cover)
            {
                Image::deleteCover($image->id_product);
                $image->cover=1;
            }
            if(!$errors)
            {
                
                if($image->update())
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated image successfully'),
                                'id_image' => $image->id,
                                'cover'=> $image_cover ? 1: 0,
                                'link' => $this->context->link->getImageLink($product->link_rewrite,$image->id),
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($this->l('An error occurred while updating the image')),
                            )
                        )
                    );
                }
            }
            else
                die(
                        json_encode(
                            array(
                                'errors' => $this->module->displayError($errors),
                            )
                        )
                    );
            
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($this->l('Image is not valid')),
                    )
                )
            );
        }
    }
    public function _submitUploadImageSave($idProduct = null, $inputFileName = 'upload_image')
    {
        $idProduct = $idProduct ? $idProduct : (int)Tools::getValue('id_product');
        if(!$idProduct)
            $this->_errors[] = $this->l('Product is required');
        elseif(!Validate::isLoadedObject(new Product($idProduct)))
            $this->_errors[] = $this->l('Product is not valid');
        $product = new Product($idProduct);
        $image_uploader = new HelperImageUploader($inputFileName);
        $_FILES[$inputFileName]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES[$inputFileName]['name']);
        $this->module->validateFile($_FILES[$inputFileName]['name'],$_FILES[$inputFileName]['size'],$this->_errors,array('jpeg', 'gif', 'png', 'jpg'),Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE')*1024*1024);
        if($this->_errors)
            return false;
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize(null);
        $files = $image_uploader->process();
        foreach ($files as &$file) {
            $image = new Image();
            if($file['error'])
            {
                $this->errors[] = $file['error'];
                return false;
            }
            else
            {
                $image->id_product = (int) ($product->id);
                $image->position = Image::getHighestPosition($product->id) + 1;
                if (!Image::getCover($image->id_product)) {
                    $image->cover = 1;
                } else {
                    $image->cover = 0;
                }
                if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                    $this->errors[] = $validate;
                }
    
                if ($this->errors) {
                    continue;
                }
                if (!$image->add()) {
                    $this->_errors[] = $this->l('An error occurred while creating additional image');
                } else {
                    if (!$new_path = $image->getPathForCreation()) {
                        $this->_errors[] = $this->l('An error occurred while attempting to create a new folder.');
                        continue;
                    }
                    $error = 0;
                    if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                        switch ($error) {
                            case ImageManager::ERROR_FILE_NOT_EXIST:
                                $this->_errors[] = $this->l('An error occurred while copying image, the file does not exist anymore.');
                                break;
                            case ImageManager::ERROR_FILE_WIDTH:
                                $this->_errors[] = $this->l('An error occurred while copying image, the file width is 0px.');
                                break;
                            case ImageManager::ERROR_MEMORY_LIMIT:
                                $this->_errors[] = $this->l('An error occurred while copying image, check your memory limit.');
                                break;
                            default:
                                $this->errors[] = $this->l('An error occurred while copying the image.');
                                break;
                        }
                        continue;
                    } else {
                        $imagesTypes = ImageType::getImagesTypes('products');
                        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');
                        foreach ($imagesTypes as $imageType) {
                            if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                                $this->_errors[] =$this->l('An error occurred while copying this image:').' ' . Tools::stripslashes($imageType['name']);
                                continue;
                            }
    
                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($file['save_path'], $new_path . '-' . Tools::stripslashes($imageType['name']) . '2x.' . $image->image_format, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2, $image->image_format)) {
                                    $this->_errors[] = $this->l('An error occurred while copying this image:') . ' ' . Tools::stripslashes($imageType['name']);
                                    continue;
                                }
                            }
                        }
                    }
    
                    unlink($file['save_path']);
                    unset($file['save_path']);
                    Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));
                    if (!$image->update()) {
                        $this->_errors[] =  $this->l('An error occurred while updating the status.');
                        continue;
                    }
                    $shops = Shop::getContextListShopID();
                    $image->associateTo($shops);
                    $json_shops = array();
                    foreach ($shops as $id_shop) {
                        $json_shops[$id_shop] = true;
                    }
                    $file['status'] = 'ok';
                    $file['id'] = $image->id;
                    $file['position'] = $image->position;
                    $file['cover'] = $image->cover;
                    $file['legend'] = $image->legend;
                    $file['path'] = $image->getExistingImgPath();
                    $file['shops'] = $json_shops;
                    @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $product->id . '.jpg');
                    @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_' . $this->context->shop->id . '.jpg');
                    @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_0.jpg');
                    Hook::exec('actionProductUpdate',array('product' => $product,'id_product'=>$product->id));
                    die(
                        json_encode(
                            array(
                                'success' => true,
                                'id_image' => $image->id,
                                'iscover' => $image->cover,
                                'link' => $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id],$image->id),
                                'link_delete' => $this->module->getLinkAdminController('admin_product_image_delete',array('idImage'=>$image->id)),
                                'link_update' => $this->module->getLinkAdminController('admin_product_image_form',array('idImage'=>$image->id)),
                            )
                        )
                    );
                }
            }
            
        }
        return $files;
    }
    public function _submitProductAttachment($id_product)
    {
        $errors = array();
        $product = new Product($id_product);
        if(isset($_FILES['product_attachment_file']['name']) && $_FILES['product_attachment_file']['name'] && isset($_FILES['product_attachment_file']['tmp_name']) && $_FILES['product_attachment_file']['tmp_name'])
        {
            $_FILES['product_attachment_file']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES['product_attachment_file']['name']);
            $this->module->validateFile($_FILES['product_attachment_file']['name'],$_FILES['product_attachment_file']['size'],$errors);
        }
        else
            $errors[] = $this->l('File attachment is required');
        if(!($product_attachment_name = Tools::getValue('product_attachment_name')))
            $errors[] = $this->l('Title of attachment is required');
        elseif(!Validate::isGenericName($product_attachment_name))
            $errors[] = $this->l('Title of attachment is not valid');
        if(($product_attachment_description = Tools::getValue('product_attachment_description')) && !Validate::isCleanHtml($product_attachment_description))
            $errors[] = $this->l('Description of attachment is not valid');
        if(!($id_product = (int)Tools::getValue('id_product')))
        {
            $errors[] = $this->l('Product is required');
        }
        elseif(!Validate::isLoadedObject( $product))
            $errors[] = $this->l('Product is not valid');
        if(!$errors)
        {
            $file = Tools::passwdGen(40);
            $file_name = $_FILES['product_attachment_file']['name'];
            if(move_uploaded_file($_FILES['product_attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$file))
            {
                $attachment = new Attachment();
                $attachment->file = $file;
                $attachment->file_name = $file_name;
                $attachment->mime = $_FILES['product_attachment_file']['type'];
                $languages = Language::getLanguages(false);
                foreach($languages as $language)
                {
                    $attachment->name[$language['id_lang']] = $product_attachment_name;
                    $attachment->description[$language['id_lang']] = $product_attachment_description;
                }
                if($attachment->add())
                {
                    Hook::exec('actionProductUpdate',array('product' => $product,'id_product'=>$product->id));
                    if($attachment->attachProduct($id_product))
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->l('Added attachment successfully'),
                                    'real_name' => $product_attachment_name,
                                    'file_name' => $file_name,
                                    'mime' => $attachment->mime,
                                    'id'=>$attachment->id,
                                    'attached_files' => Ets_pmn_defines::getInstance()->getListAttachments($id_product),
                                )
                            )
                        );
                    }
                    else
                    {
                        $attachment->delete();
                        $errors[] = $this->l('An error occurred while saving the attachment');
                    }    
                }
                else
                    $errors[] = $this->l('An error occurred while saving the attachment');
            }
            else
                $errors[] = $this->l('An error occurred while uploading the attachment');
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors),
                    )
                )
            );
        }
    }
    public function _submitAddRemoveAttachment()
    {
        $id_product = (int)Tools::getValue('id_product');
        $id_attachment = (int)Tools::getValue('id_attachment');
        $added = (int)Tools::getValue('added');
        $error = '';
        if(!Validate::isLoadedObject( new Product($id_product)))
            $error = $this->l('Product is not valid');
        elseif(!Validate::isLoadedObject(new Attachment($id_attachment)))
            $error = $this->l('Attachment is not valid');
        else
        {
            if($added)
            {
                Ets_pmn_defines::addAttachmentProduct($id_product,$id_attachment);
            }
            else
                Ets_pmn_defines::deleteAttachmentProduct($id_product,$id_attachment);
            die(
                json_encode(
                    array(
                        'success' => $added ? $this->l('Added attachment successfully') : $this->l('Removed attachment successfully'),
                        'attached_files' => Ets_pmn_defines::getInstance()->getListAttachments($id_product),
                    )
                )
            );
        }
        if($error)
        {
            die(
                json_encode(
                    array(
                        'errors' => $error,
                    )
                )
            );
        }
    }
    public function rederFormSpecificPrice($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'id_product' => $id_product,
                'specific_prices' =>Ets_pmn_defines::getInstance()->getListProductSpecificPrices($id_product),
                'module_dir' => $this->module->module_dir,
                'specific_prices_from'=> Ets_pmn_defines::getInstance()->renderSpecificPrice($id_product),
                '_PS_JS_DIR_' => _PS_JS_DIR_,
                'product_name' =>$product->name
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/specific_prices.tpl');
    }
    public function _submitSavePecificPrice($id_product)
    {
        $errors = array();
        if($id_specific_price = (int)Tools::getValue('id_specific_price'))
        {
            $specific_price = new SpecificPrice($id_specific_price);
            if($specific_price->id_product!=$id_product || !Validate::isLoadedObject($specific_price))
                $errors[] = $this->l('Specific price is not valid');
        }
        else
        {
            $specific_price = new SpecificPrice();
            $specific_price->id_product = $id_product;
        }
        $specific_price->id_product_attribute = (int)Tools::getValue('specific_price_id_product_attribute');
        $specific_price->id_currency = (int)Tools::getValue('specific_price_id_currency');
        $specific_price->id_country = (int)Tools::getValue('specific_price_id_country');
        $specific_price->id_group = (int)Tools::getValue('specific_price_id_group');
        $specific_price->id_customer = (int)Tools::getValue('specific_price_id_customer');
        $specific_price->from_quantity = (int)Tools::getValue('specific_price_from_quantity');
        $specific_price_from = Tools::getValue('specific_price_from');
        $specific_price->from = $specific_price_from && Validate::isDate($specific_price_from) ? $specific_price_from:'0000-00-00 00:00:00';
        $specific_price_to = Tools::getValue('specific_price_to');
        $specific_price->to = $specific_price_to && Validate::isDate($specific_price_to) ? $specific_price_to:'0000-00-00 00:00:00';
        $specific_price->id_shop = $this->context->shop->id;
        $specific_price_product_price = (float)Tools::getValue('specific_price_product_price');
        $specific_price_leave_bprice = (int)Tools::getValue('specific_price_leave_bprice');
        if($specific_price_leave_bprice)
            $specific_price->price=-1;
        else
            $specific_price->price = (float)$specific_price_product_price;
        $specific_price->reduction_type= Tools::getValue('specific_price_sp_reduction_type');
        if(!($specific_price_sp_reduction = Tools::getValue('specific_price_sp_reduction')) && $specific_price_leave_bprice )
        {
            $errors[] = $this->l('Apply a discount is required');
        }
        if($specific_price->reduction_type=='amount')
        {
            if(trim($specific_price_sp_reduction) !='' && (((float)$specific_price_sp_reduction <=0 && $specific_price->price==-1) || !Validate::isPrice($specific_price_sp_reduction)))
                $errors[] = $this->l('Apply a discount is not valid');
            else
                $specific_price->reduction = (float)Tools::getValue('specific_price_sp_reduction');
        }
        else
        {
            if(trim($specific_price_sp_reduction) !='' && (((float)$specific_price_sp_reduction <=0 && $specific_price->price==-1) || !Validate::isFloat($specific_price_sp_reduction)))
                $errors[] = $this->l('Apply a discount is not valid');
            else
                $specific_price->reduction = (float)Tools::getValue('specific_price_sp_reduction')/100;
        }
        $specific_price->reduction_tax = (int)Tools::getValue('specific_price_sp_reduction_tax');
        if(Ets_pmn_defines::getInstance()->existsSpecificPrice($specific_price))
            $errors[] = $this->l('A specific price already exists for these parameters.');
        $success = false;
        if(!$errors)
        {
            if($specific_price->id)
            {
                if($specific_price->update())
                    $success = $this->l('Updated specific price successfully');
                else
                    $errors[] = $this->l('An error occurred while updating the specific price');
            }
            else
            {
                if($specific_price->add())
                    $success = $this->l('Added specific price successfully');
                else
                    $errors[] = $this->l('An error occurred while creating the specific price');
            }
        }
        if(!$errors)
        {
            $specific = Ets_pmn_defines::getInstance()->getSpecificDetail($specific_price);
            if($specific['id_product_attribute'])
            {
                $specific['attribute_name'] = Ets_pmn_defines::getInstance()->getProductAttributeName($specific['id_product_attribute']);
                
            }
            if($specific['price']>=0)
            {
                $specific['price_text'] = Tools::displayPrice($specific['price'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
            }
            else
                $specific['price_text'] = Ets_pmn_defines::displayText('--','span',array('class'=>'text-center'));

            if($specific['reduction_type']=='amount')
            {
                $specific['reduction'] = Tools::displayPrice($specific['reduction'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).($specific['reduction_tax'] ? ' ('.$this->module->l('Tax incl.','products').')':' ('.$this->module->l('Tax excl.','products').')');
            }
            else
                $specific['reduction'] = Tools::ps_round($specific['reduction']*100,2).'%';
            $specific['form'] = Tools::displayDate($specific_price->from,true);
            $specific['to'] = Tools::displayDate($specific_price->to,true);
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'success' => $success,
                        'specific' => $specific,
                        'specific_prices' => Ets_pmn_defines::getInstance()->getListSpecificPrices($id_product),
                        'id_product' => $id_product,
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
    }
    public function _submitDeleteSpecificPrice($id_specific_price){
        $specific_price = new SpecificPrice($id_specific_price);
        if($specific_price->delete())
        {
            Hook::exec('actionProductUpdate',array('product' => new Product($specific_price->id_product),'id_product'=>$specific_price->id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Deleted successfully'),
                        'specific_prices' => Ets_pmn_defines::getInstance()->getListSpecificPrices($specific_price->id_product),
                        'id_product' => $specific_price->id_product,
                    )
                )
            );
        }
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->l('An error occurred while saving the item')
                    )
                )
            );
        }
    }
    public function renderAssociatedFile($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'id_product' => $id_product,
                'link_download_file' => $this->module->getLinkAdminController('admin_product_virtual_download_file_action',array('idProduct'=>$id_product)),
                'link_delete_file' => $this->context->link->getAdminLink('AdminProductManagerAjax').'&id_product='.$id_product.'&deletefileproduct=1',
                'productDownload' => Ets_pmn_defines::getInstance()->getProductDownload($id_product),
                'module_dir' => $this->module->module_dir,
                '_PS_JS_DIR_' => _PS_JS_DIR_,
                'product_name' => $product->name
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/associated_file.tpl');
    }
    public function renderFormDeliveryTimesProduct($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'id_product' => $id_product,
                'product_name' => $product->name,
                'additional_delivery_times' => (int)$product->additional_delivery_times,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/delivery_times.tpl');
    }
    public function renderFormCarriersProduct($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'id_product' => $id_product,
                'product_name' => $product->name,
                'shop_name' => $this->context->shop->name,
                'carriers' => Ets_pmn_defines::getCarriers($id_product),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/carriers.tpl');
    }
    public function _submitDeleteFileDownloadProduct($id_product)
    {
        $download = Ets_pmn_defines::getInstance()->getProductDownload($id_product);
        if($download)
        {
            $obj = new ProductDownload($download['id_product_download']);
            $obj->delete(true);
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
        }
        die(
            json_encode(
                array(
                    'success' => $this->l('Deleted successfully'),
                    'id_product' => $id_product,
                )
            )
        );
    }
    public function updateDownloadProduct($id_product)
    {
        $product = new Product($id_product);
        $errors = array();
        $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');
        if ($virtual_product_expiration_date && !Validate::isDate($virtual_product_expiration_date)) {
            $errors[] = $this->l('Expiration date is not valid');
        }
        if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
            $_FILES['virtual_product_file_uploader']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'_',$_FILES['virtual_product_file_uploader']['name']);
            $this->module->validateFile($_FILES['virtual_product_file_uploader']['name'],$_FILES['virtual_product_file_uploader']['size'],$errors);
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
        $is_virtual_file = (int)Tools::getValue('is_virtual_file');
        if ($is_virtual_file == 1) {
            if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $display_name = $_FILES['virtual_product_file_uploader']['name'];
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            }
            $product->deleteProductAttributes();
            $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
            if (!$id_product_download) {
                $id_product_download = (int)Tools::getValue('virtual_product_id');
            }
            $is_shareable = (int)Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = (int)Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable = (int)Tools::getValue('virtual_product_nb_downloable');
            
            $download = new ProductDownload((int)$id_product_download);
            $download->id_product = (int)$product->id;
            if(($virtual_product_name && Validate::isCleanHtml($virtual_product_name)) || (isset($display_name) && Validate::isCleanHtml($display_name)))
                $download->display_filename = $virtual_product_name ?:$display_name;
            if(isset($virtual_product_filename))
                $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date && Validate::isDate($virtual_product_expiration_date) ? $virtual_product_expiration_date.' 23:59:59' : '';
            $download->nb_days_accessible = (int)$virtual_product_nb_days;
            $download->nb_downloadable = (int)$virtual_product_nb_downloable;
            $download->active = 1;
            $download->is_shareable = (int)$is_shareable;
            
            if ($download->save()) {
                Hook::exec('actionProductSave',array('id_product'=>$id_product));
                if(Module::isEnabled('ets_customfields'))
                {
                    die(
                        json_encode(
                            array(
                                'row_name' => 'associated_file',
                                'row_value' =>$download->display_filename ? Ets_pmn_defines::displayText($download->display_filename,'a',array('href'=>$this->module->getLinkAdminController('admin_product_virtual_download_file_action',array('idProduct'=>$id_product)))):'--',
                                'success' => $this->l('Updated successfully'),
                                'id_lang' => $this->context->language->id,
                                'czf_product' => Module::getInstanceByName('ets_customfields')->getCzfProductByProductId($id_product)
                            )
                        )
                    );
                }
                die(
                    json_encode(
                        array(
                            'row_name' => 'associated_file',
                            'row_value' => $download->display_filename ? Ets_pmn_defines::displayText($download->display_filename,'a',array('href'=>$this->module->getLinkAdminController('admin_product_virtual_download_file_action',array('idProduct'=>$id_product)))):'--',
                            'success' => $this->l('Updated successfully')
                        )
                    )
                );
            }
        } else {
            $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int)$id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = 0;
            }
            die(
                json_encode(
                    array(
                        'row_name' => 'associated_file',
                        'row_value' => '--',
                        'success' => $this->l('Updated successfully')
                    )
                )
            );
        }
        return false;
    }
    public function renderFormRelatedProduct($id_product)
    {
        $related_products = Ets_pmn_defines::getInstance()->getListProductsRelated($id_product);
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'related_products' => $related_products,
                '_PS_JS_DIR_' => _PS_JS_DIR_,
                'module_dir' => $this->module->module_dir,
                'id_product'  => $id_product,
                'product_name' => $product->name,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/product/form_related.tpl');
    }
    public function renderFormUploadLogoModule($id_product)
    {
        $logo = Module::isEnabled('ets_customfields') ?  Module::getInstanceByName('ets_customfields')->getLogoProduct($id_product): false;
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'id_product'  => $id_product,
                'module_logo' => $logo,
                'product_name' => $product->name,
            )
        );
        return  $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_logo.tpl');
    }
    public function renderFormUploadDocumentModule($id_product)
    {
        $listLangs = Language::getLanguages(false);
        $filedValues = Module::getInstanceByName('ets_customfields')->getCzfProductByProductId($id_product);
        $docType = array();
        foreach (Language::getLanguages(false) as $lang){
            if(isset($filedValues['doc_file'][$lang['id_lang']])){
                $docType[$lang['id_lang']] = pathinfo($filedValues['doc_file'][$lang['id_lang']], PATHINFO_EXTENSION);
            }
        }
        $product = new Product($id_product,false,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'idProduct'  => $id_product,
                'czfFields' => $filedValues,
                'czfLanguages' => $listLangs,
                'activeLang' => $listLangs[0],
                'baseUri' => __PS_BASE_URI__,
                'maxFileSize' => (float)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'baseLinkDownloadDoc' => $this->context->link->getModuleLink('ets_customfields', 'documentation', array('boDownload'=> 1,'idProduct'=>$id_product)),
                'docFields' => array(
                    'doc_file' => array(
                        'label' => $this->l('File'),
                        'type' => 'file',
                        'required' => false,
                        'value' => $filedValues ? $filedValues['doc_file'] : array(),
                        'doc_display_name' => $filedValues ? $filedValues['doc_display_name'] : array(),
                        'nbDownload'=> $filedValues ? $filedValues['doc_nb_download']:array(),
                    ),
                    'doc_name' => array(
                        'label' => $this->l('Name'),
                        'type' => 'text',
                        'required' => true,
                        'value' => $filedValues ? $filedValues['doc_name'] : array()
                    ),
                    'doc_desc' => array(
                        'label' => $this->l('Description'),
                        'type' => 'textarea',
                        'required' => false,
                        'value' => $filedValues ? $filedValues['doc_desc'] : array()
                    ),
                    'doc_size' => array(
                        'label' => '',
                        'type' => 'hidden',
                        'required' => false,
                        'value' => $filedValues ? $filedValues['doc_size'] : array()
                    ),
                    'doc_type' => array(
                        'label' => '',
                        'type' => 'hidden',
                        'required' => false,
                        'value' => $docType,
                    ),
                ),
                'product_name' => $product->name
            )
        );
        return  $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_document.tpl');
    }
    public function renderFormExtratabProducts($id_tab,$id_product)
    {
        if(Module::isEnabled('ets_extraproducttabs') && ($ets_extraproducttabs = Module::getInstanceByName('ets_extraproducttabs')))
        {
            $activeLanguages = Language::getLanguages(false);
            $listExtraTabs = $ets_extraproducttabs->getListTabs($id_product,null,null,false,null,$id_tab);
            $linkDownload = $this->context->link->getModuleLink('ets_extraproducttabs', 'download');
            $this->context->smarty->assign(array(
                'listExtraTabs' => $listExtraTabs,
                'tabTypes' => $ets_extraproducttabs->getTabTypes(),
                'currentLang' => Language::getLanguage($this->context->language->id),
                'listActiveLanguages' => $activeLanguages,
                'activeLang' => $activeLanguages[0],
                'linkToListTabs' => $this->context->link->getAdminLink('AdminModules').'&configure=ets_extraproducttabs',
                'uploadDir' => strpos($linkDownload, '?') === false ? $linkDownload.'?' : $linkDownload.'&',
                'imgDir' => $this->context->shop->getBaseURI().'img/ets_extraproducttabs/',
                'idProduct' => $id_product,
                'datetimepickerJs' => '',
                'baseUrl' => $this->context->shop->getBaseURI(),
                'path' => $this->context->shop->getBaseURI().'upload/ets_extraproducttabs/',
                'id_tab_extra'=>$id_tab,
                'listHooks' => $ets_extraproducttabs->getListHooks(false)
            ));
            $input_extra = $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_extraproducttabs/views/templates/hook/extra_tab_product.tpl');
            $this->context->smarty->assign('input_extra',$input_extra);
            return  $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_extratab.tpl');
        }
        return '';
    }
    public function _submitExtraTabProduct($id_product,$id_tab_extra)
    {
        if(Module::isEnabled('ets_extraproducttabs') && ($ets_extraproducttabs = Module::getInstanceByName('ets_extraproducttabs')) )
        {
            if(($tabContent = Tools::getValue('ets_ept_content')) && is_array($tabContent)){
                $errors = $ets_extraproducttabs->validateDataTab($tabContent);
                $ets_ept_use_global = Tools::getValue('ets_ept_use_global',array());
                if(!Ets_productmanager::validateArray($ets_ept_use_global))
                    $errors[] = $this->l('Use global is not valid');
                if($errors){
                    die(
                        json_encode(
                            array('errors' => $this->module->displayError($errors))
                        )
                    );
                }
                $ets_extraproducttabs->saveDataTab($id_product, $tabContent, $ets_ept_use_global);
            }
        }
        Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
        die(
            json_encode(
                array(
                    'row_name' => 'extra_product_tab_'.$id_tab_extra,
                    'row_value' => Ets_pmn_defines::getInstance()->getExtraProductTabValue($id_product,$id_tab_extra),
                    'success' => $this->l('Updated successfully')
                )
            )
        );
    }
    public function _submitDocumentProduct($id_product)
    {
        if(Module::isEnabled('ets_customfields'))
        {
            return Module::getInstanceByName('ets_customfields')->_submitDocumentProduct($id_product);
        }
        return false;
    }
    public function submitCombinationsProduct($id_product)
    {
        $product = new Product($id_product);
        if($product->getType()== Product::PTYPE_SIMPLE)
        {
            if(($combinations_id_product_attribute = Tools::getValue('combinations_id_product_attribute')) && Ets_productmanager::validateArray($combinations_id_product_attribute,'isInt'))
            {
                $combinations_attribute_default = Tools::getValue('combinations_attribute_default');
                $combinations_attribute_quantity = Tools::getValue('combinations_attribute_quantity');
                $combinations_attribute_available_date = Tools::getValue('combinations_attribute_available_date');
                $combinations_attribute_minimal_quantity= Tools::getValue('combinations_attribute_minimal_quantity');
                $combinations_attribute_reference = Tools::getValue('combinations_attribute_reference');
                $combinations_attribute_location = Tools::getValue('combinations_attribute_location');
                $combinations_attribute_low_stock_threshold = Tools::getValue('combinations_attribute_low_stock_threshold');
                $combinations_attribute_wholesale_price= Tools::getValue('combinations_attribute_wholesale_price');
                $combinations_attribute_price = Tools::getValue('combinations_attribute_price');
                $combinations_attribute_unity = Tools::getValue('combinations_attribute_unity');
                $combinations_attribute_weight = Tools::getValue('combinations_attribute_weight');
                $combinations_attribute_isbn = Tools::getValue('combinations_attribute_isbn');
                $combinations_attribute_ean13= Tools::getValue('combinations_attribute_ean13');
                $combinations_attribute_upc = Tools::getValue('combinations_attribute_upc');
                $combination_id_image_attr = Tools::getValue('combination_id_image_attr');
                $combination_attribute_low_stock_alert = Tools::getValue('combination_attribute_low_stock_alert');
                foreach($combinations_id_product_attribute as $id_product_attribute)
                {
                    $combination = new Combination($id_product_attribute);
                    if(Validate::isLoadedObject($combination) && $combination->id_product == $product->id)
                    {
                        if(isset($combinations_attribute_default[$id_product_attribute]) && Validate::isInt($combinations_attribute_default[$id_product_attribute]))
                        {
                            $combination->default_on = (int)$combinations_attribute_default[$id_product_attribute];
                            if($combination->default_on)
                                Ets_pmn_defines::resetAttributeDefault($product->id,$id_product_attribute);
                        }
                        else
                            $combination->default_on=0;
                        if(isset($combinations_attribute_quantity[$id_product_attribute]) && Validate::isUnsignedInt($combinations_attribute_quantity[$id_product_attribute]))
                            $combination->quantity = (int)$combinations_attribute_quantity[$id_product_attribute];
                        else
                            $combination->quantity=0;
                        if(isset($combinations_attribute_available_date[$id_product_attribute]) && Validate::isDate($combinations_attribute_available_date[$id_product_attribute]))
                            $combination->available_date = $combinations_attribute_available_date[$id_product_attribute];
                        else
                            $combination->available_date ='0000-00-00';
                        if(isset($combinations_attribute_minimal_quantity[$id_product_attribute]) && Validate::isUnsignedInt($combinations_attribute_minimal_quantity[$id_product_attribute]))
                            $combination->minimal_quantity = (int)$combinations_attribute_minimal_quantity[$id_product_attribute];
                        else
                            $combination->minimal_quantity=0;
                        if(isset($combinations_attribute_reference[$id_product_attribute]) && Validate::isReference($combinations_attribute_reference[$id_product_attribute]))
                            $combination->reference = $combinations_attribute_reference[$id_product_attribute];
                        else
                            $combination->reference='';
                        if(isset($combinations_attribute_location[$id_product_attribute]) && Validate::isGenericName($combinations_attribute_location[$id_product_attribute]))
                            $combination->location = $combinations_attribute_location[$id_product_attribute];
                        else
                            $combination->location='';
                        if(isset($combinations_attribute_low_stock_threshold[$id_product_attribute]) && Validate::isInt($combinations_attribute_low_stock_threshold[$id_product_attribute]))
                            $combination->low_stock_threshold = (int)$combinations_attribute_low_stock_threshold[$id_product_attribute];
                        else
                            $combination->low_stock_threshold =0;
                        if(isset($combination_attribute_low_stock_alert[$id_product_attribute]) && Validate::isInt($combination_attribute_low_stock_alert[$id_product_attribute]))
                            $combination->low_stock_alert = (int)$combination_attribute_low_stock_alert[$id_product_attribute];
                        else
                            $combination->low_stock_alert=0;
                        if(isset($combinations_attribute_wholesale_price[$id_product_attribute]) && Validate::isPrice($combinations_attribute_wholesale_price[$id_product_attribute]))
                            $combination->wholesale_price = (float)$combinations_attribute_wholesale_price[$id_product_attribute];
                        else
                            $combination->wholesale_price =0;
                        if(isset($combinations_attribute_price[$id_product_attribute]) && Validate::isNegativePrice($combinations_attribute_price[$id_product_attribute]))
                            $combination->price = (float)$combinations_attribute_price[$id_product_attribute];
                        else
                            $combination->price = 0;
                        if(isset($combinations_attribute_unity[$id_product_attribute]) && Validate::isNegativePrice($combinations_attribute_unity[$id_product_attribute]))
                            $combination->unit_price_impact = $combinations_attribute_unity[$id_product_attribute];
                        else
                            $combination->unit_price_impact = 0;
                        if(isset($combinations_attribute_weight[$id_product_attribute]) && Validate::isUnsignedFloat($combinations_attribute_weight[$id_product_attribute]))
                            $combination->weight = (float)$combinations_attribute_weight[$id_product_attribute];
                        else
                            $combination->weight =0;
                        if(isset($combination->isbn) && isset($combinations_attribute_isbn[$id_product_attribute]) && Validate::isIsbn($combinations_attribute_isbn[$id_product_attribute]))
                            $combination->isbn = $combinations_attribute_isbn[$id_product_attribute];
                        else
                            $combination->isbn='';
                        if(isset($combinations_attribute_ean13[$id_product_attribute]) && Validate::isEan13($combinations_attribute_ean13[$id_product_attribute]))
                            $combination->ean13 = $combinations_attribute_ean13[$id_product_attribute];
                        else
                            $combination->ean13='';
                        if(isset($combinations_attribute_upc[$id_product_attribute]) && Validate::isUpc($combinations_attribute_upc[$id_product_attribute]))
                            $combination->upc = $combinations_attribute_upc[$id_product_attribute];
                        else
                            $combination->upc = '';
                        if($combination->update())
                        {
                            StockAvailable::setQuantity($id_product, (int)$id_product_attribute, $combination->quantity);
                            if(method_exists('StockAvailable','setLocation'))
                                Ets_pmn_defines::setLocation($id_product,$combination->location,null,$combination->id);
                            Ets_pmn_defines::deleteProductAttributeImage($id_product_attribute);
                            if(isset($combination_id_image_attr[$id_product_attribute]) && $combination_id_image_attr[$id_product_attribute])
                            {
                                foreach($combination_id_image_attr[$id_product_attribute] as $id_image)
                                {
                                    if($id_image && Validate::isInt($id_image))
                                    {
                                        Ets_pmn_defines::addProductAttributeImage($id_product_attribute,$id_image);
                                    }

                                }
                            }
                        }
                    }
                }
            }
            else
                $product->deleteProductAttributes();
            Ets_pmn_defines::updateQuantityProduct($id_product);
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'row_value' => Ets_pmn_defines::getInstance()->getListCombinations($id_product),
                        'row_name' => 'combinations',
                        'sav_quantity' =>Ets_pmn_defines::getInstance()->getQuantityProduct($id_product),
                    )
                )
            );
        }
    }
    public function submitSuppliersProduct($id_product)
    {
        $errors = array();
        $id_suppliers = Tools::getValue('id_suppliers');
        $product_supplier_reference = Tools::getValue('product_supplier_reference');
        $product_supplier_price = Tools::getValue('product_supplier_price');
        $product_supplier_price_currency = Tools::getValue('product_supplier_price_currency');
        if($id_suppliers && Ets_productmanager::validateArray($id_suppliers,'isInt') && Ets_productmanager::validateArray($product_supplier_reference) && Ets_productmanager::validateArray($product_supplier_price) && Ets_productmanager::validateArray($product_supplier_price_currency))
        {
            foreach($id_suppliers as $id_supplier)
            {
                $supplier = new Supplier($id_supplier);
                if(!Validate::isLoadedObject($supplier))
                {
                    $errors[] = $this->l('Supplier is not valid');
                }
                else
                {
                    if(isset($product_supplier_reference[$id_supplier]) && ($references = $product_supplier_reference[$id_supplier]))
                    {
                        foreach($references as $reference)
                            if($reference && !Validate::isReference($reference))
                                $errors[] = $this->l('Supplier reference is not valid');
                    }
                    if(isset($product_supplier_price[$id_supplier]) && ($prices = $product_supplier_price[$id_supplier]))
                    {
                        foreach($prices as $price)
                        {
                            if($price && !Validate::isPrice($price))
                                $errors[] = $this->l('Product price is not valid');
                        }
                    }
                    if(isset($product_supplier_price_currency[$id_supplier]) && ($currencies = $product_supplier_price_currency[$id_supplier]))
                    {
                        foreach($currencies as $id_currency)
                        {
                            $currency_class = new Currency($id_currency);
                            if(!Validate::isLoadedObject($currency_class))
                                $errors[] = $this->l('Product price currency is not valid');
                        }
                    }
                }
            }
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
        else
        {
            Ets_pmn_defines::updateSuppliersProduct($id_product,$id_suppliers,$product_supplier_reference,$product_supplier_price,$product_supplier_price_currency);
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'row_name' => 'suppliers',
                        'row_value' => Ets_pmn_defines::getInstance()->getListSuppliers($id_product),
                    )
                )
            );
        }
    }
    public function changePrivateNoteProduct()
    {
        $id_product = (int)Tools::getValue('id_note_product');
        $private_note = Tools::getValue('private_note');
        $error = '';
        if(!$id_product || !Validate::isLoadedObject(new Product($id_product)))
            $error = $this->l('Product is not valid');
        elseif($private_note && !Validate::isCleanHtml($private_note))
            $error = $this->l('Private note is not valid');
        if($error)
        {
            die(
                json_encode(
                    array(
                        'error' => $error,
                    )
                )
            );
        }
        else{
            Ets_pmn_defines::updateProductNote($id_product,$private_note);
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
        }
    }
}