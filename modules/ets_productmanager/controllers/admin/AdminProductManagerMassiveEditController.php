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
 * Class AdminProductManagerMassiveEditController
 * @property Ets_productmanager $module
 */
class AdminProductManagerMassiveEditController extends ModuleAdminController
{
    public $_errors = array();
    public $condition_text_fields = array();
    public $condition_select_fields = array();
    public $condition_radio_fields = array();
    public $condition_active_fields = array();
    public $condition_quantity_fields = array();
    public $condition_price_fields = array();
    public $condition_multi_fields = array();
    public $condition_form_fields = array();
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function initContent()
    {
        parent::initContent();
        if(Tools::isSubmit('getFormTemplatesMassive'))
        {
            if(!$this->module->active)
            {
                die(
                    json_encode(
                        array(
                            'error' => $this->l('You must enable Product Manager module to configure its features'),
                        )
                    )
                );
            }
            if(($id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit')) && ($massive = new Ets_pmn_massedit($id_ets_pmn_massedit)) && Validate::isLoadedObject($massive) && ($massive->deleted==0 || $massive->id==1) )
            {
                die(
                    json_encode(
                        array(
                            'html_form' => $this->renderFormMassedit($massive,true),
                            'list_template_massedit' => $massive->renderTemplateMassedit(),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'html_form' => $this->renderFormMassedit(false,true),
                            'list_template_massedit' => Ets_pmn_massedit::getInstance()->renderTemplateMassedit(),
                        )
                    )
                );
            }
              
        }
        if(Tools::isSubmit('submitSearchProductEdit'))
        {
            if(!$this->module->active)
            {
                die(
                    json_encode(
                        array(
                            'error' => $this->l('You must enable Product Manager module to configure its features'),
                        )
                    )
                );
            }
            $type_combine_condition = Tools::getValue('type_combine_condition','and');

            if(!in_array($type_combine_condition,array('and','or')))
                $type_combine_condition =  'and';
            if(Tools::isSubmit('getPreviewProducts') && ($id_massedit = (int)Tools::getValue('id_massedit')) && ($massedit = new Ets_pmn_massedit($id_massedit)) && Validate::isLoadedObject($massedit))
            {
                $conditions = $massedit->getConditions();
                $product_excluded = $massedit->excluded;
                die(
                    json_encode(
                        array(
                            'product_list' => $massedit->renderListProductsByConditions($conditions,$product_excluded,false,(int)Tools::getValue('page'),(int)Tools::getValue('paginator_matching_products_select_limit',10),(int)Tools::getValue('stepNumber'),$type_combine_condition),
                        )
                    )
                );
            }
            else
            {
                $conditions = $this->getConditions();
                $product_excluded = Tools::getValue('product_excluded');
                $product_excluded = Validate::isCleanHtml($product_excluded) ? trim($product_excluded,','):'';
                die(
                    json_encode(
                        array(
                            'product_list' => Ets_pmn_massedit::getInstance()->renderListProductsByConditions($conditions,Validate::isCleanHtml($product_excluded) ? $product_excluded :'',true,(int)Tools::getValue('page'),(int)Tools::getValue('paginator_matching_products_select_limit',10),(int)Tools::getValue('stepNumber'),$type_combine_condition),
                        )
                    )
                );
            }
            
        }
        if(Tools::isSubmit('btnSubmitMassedit'))
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
            $this->_submitSaveMassedit();
        }
        if(Tools::isSubmit('submitSaveMasseEditProduct'))
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
            $this->_submitSaveMasseEditProduct();
        }
        if(Tools::isSubmit('submitSaveMasseActionEdit'))
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
            $this->_submitSaveMasseActionEdit();
        }
        if(Tools::isSubmit('submitSaveNameMassedit'))
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
            $this->processSubmitSaveNameMassedit();
        }
    }
    public function _submitSaveMassedit()
    {
        $errors = array();
        $massedit_name = Tools::getValue('massedit_name');
        $id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit');
        $type_combine_condition = Tools::getValue('type_combine_condition','and');
        if(!in_array($type_combine_condition,array('or','and')))
            $type_combine_condition = 'and';
        if($id_ets_pmn_massedit)
            $massedit = new Ets_pmn_massedit($id_ets_pmn_massedit);
        else
        {
            $massedit = new Ets_pmn_massedit();
            $massedit->id_shop = $this->context->shop->id;
        }
        $massedit->type_combine_condition = $type_combine_condition;
        if((!$massedit_name && $id_ets_pmn_massedit && !$massedit->deleted) || (!$massedit_name && Tools::isSubmit('submitSaveMassiveTemplate')) || (!$massedit_name && Tools::isSubmit('saveMassedit')))
            $errors[] = $this->l('Mass Edit template name is required');
        if($massedit_name && !Validate::isCleanHtml($massedit_name))
            $errors[] = $this->l('Mass Edit template name is not valid');
        $product_excluded = trim(Tools::getValue('product_excluded'),',');
        if($product_excluded && !Validate::isCleanHtml($product_excluded))
            $errors[] = $this->l('Excluded product is not valid');
        if(!$errors)
        {
            $conditions = $this->getConditions();
            $product_excluded = Tools::getValue('product_excluded');
            $product_excluded = Validate::isCleanHtml($product_excluded) ? trim($product_excluded,','):'';
            $nbProducts = Ets_pmn_massedit::getInstance()->getProducts($conditions,true,0,false,Validate::isCleanHtml($product_excluded) ? $product_excluded :'',$type_combine_condition);
            if(!$nbProducts)
                $errors[] = $this->l('Product selected not found');
        }
        if(!$errors)
        {
            $massedit->name = $massedit_name;
            $massedit->excluded = $product_excluded;
            if(!$massedit_name)
                $massedit->deleted=1;
            else
            {
                $massedit->deleted=0;
                if($massedit->id==1)
                {
                    unset($massedit->id);
                    $massedit->id_shop = $this->context->shop->id;
                }
                
            }
            if(isset($massedit->id) && $massedit->id)
            {
                if(!$massedit->update())
                    $errors[] = $this->l('An error occurred while saving the Mass Edit');
            }elseif(!$massedit->add())
            {
                $errors[] = $this->l('An error occurred while saving the Mass Edit');
            }
            if(!$errors && $massedit->id)
            {
                $conditions = $this->getConditions();
                $massedit->deleteCondition();
                if($conditions)
                {
                    foreach($conditions as $condition)
                    {
                        $condition_class = new Ets_pmn_massedit_condition();
                        $condition_class->id_ets_pmn_massedit = $massedit->id;
                        $condition_class->id_lang = $condition['id_lang'];
                        $condition_class->filtered_field = $condition['filtered_field'];
                        $condition_class->operator = $condition['operator'];
                        $condition_class->compared_value = is_array($condition['compared_value']) ? implode(',',$condition['compared_value']) : $condition['compared_value'];
                        $condition_class->add();
                    }
                }
                else
                {
                    $condition_class = new Ets_pmn_massedit_condition();
                    $condition_class->id_ets_pmn_massedit = $massedit->id;
                    $condition_class->filtered_field = Ets_pmn_massedit_condition::FILTERED_FIELD_ALL;
                    $condition_class->add();
                }
                $this->context->smarty->assign(
                    array(
                        'languages' => Language::getLanguages(false),
                        'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                        'default_currency' => $this->context->currency
                    )
                );
                die(
                    json_encode(
                        array(
                            'success' => $this->l('Submitted successfully'),
                            'html_form' => $this->renderFormStartMassedit($massedit),
                            'id_ets_pmn_massedit' => $massedit->id,
                            'list_template_massedit' => $massedit->renderTemplateMassedit(),
                            'url_reload' => $this->context->link->getAdminLink('AdminProductManagerMassiveEdit').'&id_ets_pmn_massedit='.$massedit->id.'&startmassedit=1',
                        )
                    )
                );
            }
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
    public function getConditions()
    {
        $condition_fields = Tools::getValue('condition_field');
        $condition_operators = Tools::getValue('condition_operator');
        $operator_value_texts = Tools::getValue('operator_value_text');
        $operator_value_text_langs = Tools::getValue('operator_value_text_lang');
        $operator_value_attributes = Tools::getValue('operator_value_attribute');
        $operator_value_features = Tools::getValue('operator_value_features');
        $operator_value_manufacturers = Tools::getValue('operator_value_manufacturer');
        $operator_value_suppliers = Tools::getValue('operator_value_supplier');
        $operator_value_colors = Tools::getValue('operator_value_color');
        $countLanguages = count(Language::getLanguages(false));
        $operator_value_categories = Tools::getValue('operator_value_categories');
        $conditions = array();
        if($condition_fields && Ets_productmanager::validateArray($operator_value_texts) && Ets_productmanager::validateArray($operator_value_text_langs) && Ets_productmanager::validateArray($operator_value_attributes) && Ets_productmanager::validateArray($operator_value_manufacturers) && Ets_productmanager::validateArray($operator_value_suppliers) && Ets_productmanager::validateArray($operator_value_colors) && Ets_productmanager::validateArray($condition_fields) && Ets_productmanager::validateArray($condition_operators))
        {
            foreach($condition_fields as $index => $field) {
                $condition = array();
                $condition['filtered_field'] = $field;
                if (in_array($field, array(Ets_pmn_massedit_condition::FILTERED_FIELD_NAME, Ets_pmn_massedit_condition::FILTERED_FIELD_DESCRIPTION, Ets_pmn_massedit_condition::FILTERED_FIELD_SUMMARY))) {
                    if (isset($condition_operators[$index]) && $condition_operators[$index] && isset($operator_value_texts[$index]) && $operator_value_texts[$index] != '' && ((isset($operator_value_text_langs[$index]) && $operator_value_text_langs[$index] != '') || $countLanguages == 1)) {
                        $condition['id_lang'] = $countLanguages == 1 ? $this->context->language->id : $operator_value_text_langs[$index];
                        $condition['compared_value'] = $operator_value_texts[$index];
                        $condition['operator'] = $condition_operators[$index];
                        $conditions[] = $condition;
                    }
                }
                if (in_array($field, array(Ets_pmn_massedit_condition::FILTERED_FIELD_REFERENCE)))
                {
                    if(isset($condition_operators[$index]) && $condition_operators[$index] && isset($operator_value_texts[$index]) && $operator_value_texts[$index]!=='' && Validate::isReference($operator_value_texts[$index]))
                    {
                        $condition['id_lang'] = 0;
                        $condition['compared_value'] = $operator_value_texts[$index];
                        $condition['operator'] = $condition_operators[$index];
                        $conditions[] = $condition;
                    }
                }
                if(in_array($field,array(Ets_pmn_massedit_condition::FILTERED_FIELD_ID_PRODUCT,Ets_pmn_massedit_condition::FILTERED_FIELD_QUANTITY,Ets_pmn_massedit_condition::FILTERED_FIELD_PRICE)))
                {
                    if(isset($condition_operators[$index]) && $condition_operators[$index] && isset($operator_value_texts[$index]) && $operator_value_texts[$index]!=='' && Validate::isFloat($operator_value_texts[$index]))
                    {
                        $condition['id_lang'] = 0;
                        $condition['compared_value'] = $operator_value_texts[$index];
                        $condition['operator'] = $condition_operators[$index];
                        $conditions[] = $condition;
                    }
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE && isset($operator_value_attributes[$index]) && $operator_value_attributes[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_attributes[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES && isset($operator_value_features[$index]) && $operator_value_features[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_features[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR && isset($operator_value_colors[$index]) && $operator_value_colors[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_colors[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND && isset($operator_value_manufacturers[$index]) && $operator_value_manufacturers[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_manufacturers[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER && isset($operator_value_suppliers[$index]) && $operator_value_suppliers[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_suppliers[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES && isset($operator_value_categories[$index]) && $operator_value_categories[$index])
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = array_map('intval',explode(',',trim($operator_value_categories[$index],',')));
                    $condition['operator']= $condition_operators[$index];
                    $conditions[] = $condition;
                }
                if($field == Ets_pmn_massedit_condition::FILTERED_FIELD_ALL)
                {
                    $condition['id_lang'] = 0;
                    $condition['compared_value'] = '';
                    $condition['operator']= false;
                    $conditions[] = $condition;
                }
                
            }
        }
        return $conditions;
    }
    public function renderList()
    {
        if(!$this->module->active)
            return $this->module->displayWarning($this->l('You must enable Product Manager module to configure its features'));
        $html ='';
        if(Tools::isSubmit('delMassedit') && ($id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit')) && ($massedit = new Ets_pmn_massedit($id_ets_pmn_massedit)) && (Validate::isLoadedObject($massedit)) )
        {
            
            $massedit->deleted =1;
            if($massedit->update())
            {
                $this->context->cookie->ets_pmn_success = $this->l('Deleted mass edit successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEdit').'&addnewmassedit=1');
            }
            else
                $this->context->controller->errors[] = $this->l('An error occurred while deleting the mass edit');
        }
        if(Tools::isSubmit('startmassedit'))
        {
            $id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit');
            $massedit = new Ets_pmn_massedit($id_ets_pmn_massedit);
            if($massedit && Validate::isLoadedObject($massedit))
            {
                $this->context->smarty->assign(
                    array(
                        'languages' => Language::getLanguages(false),
                        'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                        'default_currency' => $this->context->currency
                    )
                );
                $html = $this->renderFormStartMassedit($massedit);
            }
        }elseif(Tools::isSubmit('previewmassedit'))
        {
            $id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit');
            $massedit = new Ets_pmn_massedit($id_ets_pmn_massedit);

            if($massedit && Validate::isLoadedObject($massedit))
            {
                $this->context->smarty->assign(
                    array(
                        'languages' => Language::getLanguages(false),
                        'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                        'default_currency' => $this->context->currency
                    )
                );
                $product_exclued = Tools::getValue('product_exclued',$massedit->excluded);
                $html = $massedit->renderFormPreviewMassedit($product_exclued);
            }
        }
        elseif(Tools::isSubmit('list'))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditTemplates'));
        else
        {
            $id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit');
            if($id_ets_pmn_massedit && Validate::isUnsignedId($id_ets_pmn_massedit))
            {
                $massedit = new Ets_pmn_massedit($id_ets_pmn_massedit);
                if($id_ets_pmn_massedit==1)
                    $massedit->deleteCondition();
            }
            else
            {
                $massedit = new Ets_pmn_massedit(1);
                $massedit->deleteCondition();
            }
            $html = $this->renderFormMassedit($massedit);
        }    
        $this->context->smarty->assign(
            array(
                'ets_pmn_body_html' => $html,
            )
        );
        if($this->context->cookie->ets_pmn_success)
        {
            $success = $this->context->cookie->ets_pmn_success;
            $this->context->cookie->ets_pmn_success = false;
            
        }
        return (isset($success) && $success ? $this->module->displayConfirmation($success):'').$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'massive.tpl');
    }

    /**
     * @param Ets_pmn_massedit $massedit
     * @return string
     */
    public function renderFormStartMassedit($massedit)
    {
        $product_tabs = array(
            array(
                'tab' => 'BaseSettings',
                'name' => $this->l('Base settings'),
            ),
            array(
                'tab' => 'Categories',
                'name' => $this->l('Categories'),
            ),
            array(
                'tab' => 'BrandAndFeatures',
                'name' => $this->l('Brand and Features'),
            ),
            array(
                'tab' => 'RelatedProducts',
                'name' =>$this->l('Related products')
            ),
            array(
                'tab'=> 'Quantities',
                'name' => $this->l('Quantities'),
            ),
            array(
                'tab'=>'Combinations',
                'name' => $this->l('Combinations'),
            ),
            array(
                'tab'=>'Shipping',
                'name' => $this->l('Shipping'),
            )
        );
        $product_tabs[] = array(
            'tab'=>'Price',
            'name' => $this->l('Pricing'),
        );
        $product_tabs[] = array(
            'tab'=>'Seo',
            'name' => $this->l('SEO'),
        );
        $product_tabs[] = array(
            'tab'=>'Options',
            'name' => $this->l('Options'),
        );
        $this->condition_text_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'append_before',
                'name' => $this->l('Append before'),
            ),
            array(
                'id' => 'append_after',
                'name' => $this->l('Append after'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $this->condition_price_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'plus_percent',
                'name' => $this->l('+ %'),
            ),
            array(
                'id' => 'minus_percent',
                'name' => $this->l('- %'),
            ),
            array(
                'id' => 'plus_amount',
                'name' => $this->l('+ amount'),
            ),
            array(
                'id' => 'minus_amount',
                'name' => $this->l('- amount'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $this->condition_select_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'remove',
                'name' => $this->l('Remove'),
            ),
            array(
                'id' => 'remove_all',
                'name' => $this->l('Remove all'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $this->condition_radio_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $this->condition_multi_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'add',
                'name' => $this->l('Add'),
            ),
            array(
                'id' => 'remove',
                'name' => $this->l('Remove'),
            ),
            array(
                'id' => 'remove_all',
                'name' => $this->l('Remove all'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace all'),
            ),
        );
        $this->condition_form_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'add',
                'name' => $this->l('Add'),
            ),
            array(
                'id' => 'remove_all',
                'name' => $this->l('Remove all'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $this->condition_active_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'active_all',
                'name' => $this->l('Activate all'),
            ),
            array(
                'id' => 'disable_all',
                'name' => $this->l('Disable all'),
            ),
        );
        $this->condition_quantity_fields = array(
            array(
                'id' => 'off',
                'name' => $this->l('Off'),
            ),
            array(
                'id' => 'plus_amount',
                'name' => $this->l('+ amount'),
            ),
            array(
                'id' => 'minus_amount',
                'name' => $this->l('- amount'),
            ),
            array(
                'id' => 'replace',
                'name' => $this->l('Replace'),
            ),
        );
        $valueFieldPost = array();
        if($massedit->id!=1)
        {
            if($actions = Ets_pmn_massedit_condition_action::getActions($massedit->id))
            {
                foreach($actions as $action)
                {
                    if($action['id_lang'])
                    {
                        if(!isset($valueFieldPost[$action['field']]))
                        {
                            $valueFieldPost[$action['field']] = array();
                        }
                        $valueFieldPost[$action['field']][$action['id_lang']] = $action['value_lang'];
                    }
                    else
                        $valueFieldPost[$action['field']] = Validate::isJson($action['value']) ? json_decode($action['value']):$action['value'] ;
                    if(!isset($valueFieldPost['condition_fields']))
                        $valueFieldPost['condition_fields'] = array();
                    $valueFieldPost['condition_fields'][$action['field']] = $action['condition'];
                }

            }
            $this->context->smarty->assign(
                array(
                    'valueFieldPost' => $valueFieldPost,
                )
            );
        }
        if($product_tabs)
        {
            foreach($product_tabs as &$tab)
            {
                if (method_exists($this, 'renderForm' . $tab['tab'])) {
                    $tab['content_html'] = $this->{'renderForm' . $tab['tab']}($massedit->id);
                }
                else
                    $tab['content_html'] = 'renderForm' . $tab['tab'];
            }
        }
        $this->context->smarty->assign(
            array(
                'product_tabs' => $product_tabs,
                'current_tab' => 'BaseSettings',
                'id_ets_pmn_massedit' => $massedit->id,
                'massedit' => $massedit,
                'step_massedit_html' => $massedit->renderStepMassedit(2),
                'product_list' => $massedit->renderListProductsByConditions($massedit->getConditions(),$massedit->excluded,false,(int)Tools::getValue('page'),(int)Tools::getValue('paginator_matching_products_select_limit',10),(int)Tools::getValue('stepNumber')),
                'massedit_name' => $massedit->name
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php','product/form.tpl');
    }
    public function renderFormBaseSettings()
    {
        $show_variations = false;
        $fields = array(
            array(
                'type' => 'radio',
                'name' => 'show_variations',
                'label' => $this->l('Combinations'),
                'form_group_class' => 'ets_pmn_show_variations'.(!$show_variations ?' hide':''),
                'values' => array(
                    array(
                        'id'=>0,
                        'name' => $this->l('Simple product'),
                    ),
                    array(
                        'id'=>1,
                        'name' => $this->l('Product with combinations'),
                    ),
                )
            )
        );
        $fields[]= array(
            'type' => 'text',
            'name' => 'name',
            'label' => $this->l('Name'),
            'lang'=> true,
            'condition_fields' => $this->condition_text_fields,
            'condition_fields_selected' => 'off',
            'short_codes' => array('name'=>'{name}','price'=>'{price}'),
        );
        $fields[]= array(
            'type' => 'textarea',
            'name' => 'description_short',
            'label' => $this->l('Summary'),
            'autoload_rte' => true,
            'lang'=> true,
            'placeholder' => $this->l('Fill in a striking short description of the product (displayed on product page and product list as abstract for customers and search engines). For detailed informations use the description tab.'),
            'max_text' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ?: 800,
            'small_text' => $this->l('characters allowed'),
            'condition_fields' => $this->condition_text_fields,
            'condition_fields_selected' => 'off',
            'short_codes' => array('summary:50'=>'{summary:50}','features'=>'{features}','combinations'=>'{combinations}','name'=>'{name}','price'=>'{price}','url'=>'{url}'),
        );
        $fields[] = array(
            'type' => 'textarea',
            'name' => 'description',
            'label' => $this->l('Description'),
            'autoload_rte' => true,
            'lang'=> true,
            'max_text' => 21844,
            'small_text' => $this->l('characters allowed'),
            'condition_fields' => $this->condition_text_fields,
            'condition_fields_selected' => 'off',
            'short_codes' => array('description:50'=>'{description:50}','summary:50' =>'{summary:50}','features' =>'{features}','combinations'=>'{combinations}','name'=>'{name}','price'=>'{price}','url'=>'{url}'),
        );
        $fields[] = array(
            'type' => 'text',
            'name' => 'reference',
            'label' => $this->l('Reference'),
            'condition_fields' => $this->condition_text_fields,
            'condition_fields_selected' => 'off'
        );
        $fields[] = array(
            'type' => 'switch',
            'name'=> 'active',
            'label' => $this->l('Enabled'),
            'condition_fields' => $this->condition_active_fields,
            'condition_fields_selected' => 'off'
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormCategories($id_massedit=false)
    {
        if($id_massedit >1)
        {
            if($categories = Ets_pmn_massedit::getValueFieldByID($id_massedit,'id_categories'))
            {
                $selected_categories = json_decode($categories,true);
            }
            else
                $selected_categories = array();
            if($id_category = (int)Ets_pmn_massedit::getValueFieldByID($id_massedit,'id_category_default'))
                $selected_default_category = array($id_category);
            else
                $selected_default_category = array();
            $disabled_categories = array();
        }
        else
        {
            $selected_categories = array();
            $selected_default_category = array();
            $disabled_categories = array();

        }
        $fields = array(
            array(
                'type' => 'categories',
                'name'=>'id_category_default',
                'label' => $this->l('Default category'),
                'categories_tree'=> $this->module->displayProductCategoryTre(Ets_pmn_defines::getInstance()->getCategoriesTree(),$selected_default_category,'id_category_default',$disabled_categories,0,array(),true,true,'radio'),
                'condition_fields' => $this->condition_radio_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'categories',
                'name'=>'id_categories',
                'label' => $this->l('Categories'),
                'categories_tree'=> $this->module->displayProductCategoryTre(Ets_pmn_defines::getInstance()->getCategoriesTree(),$selected_categories,'',$disabled_categories,0,array(),true),
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormBrandAndFeatures($id_massedit= false)
    {
        $manufacturers = Ets_pmn_massedit::getManufacturers();
        $fields = array();
        if($manufacturers)
            $fields[] = array(
                'type' => 'select',
                'name' => 'id_manufacturer',
                'label' => $this->l('Brand','products'),
                'values' => array_merge(array(array('id'=>'','name'=> $this->l('--'))), $manufacturers),
                'condition_fields' => $this->condition_select_fields,
                'condition_fields_selected' => 'off'
            );
        if(($product_features = $this->displayProductFeatures($id_massedit)))
        {
            $fields[] = array(
                'type' => 'product_features',
                'name'=> 'features',
                'label' => $this->l('Features','products'),
                'list_features' => $product_features,
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            );
        }
        if($fields)
        {
            $this->context->smarty->assign(
                array(
                    'fields' => $fields,
                )
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
        }
    }
    public function renderFormRelatedProducts($id_massedit= false)
    {
        if($id_massedit >1 && ($related_products =  Ets_pmn_massedit::getListProductsRelated($id_massedit)))
        {
            $this->context->smarty->assign(
                array(
                    'related_products' => $related_products,
                )
            );
        }
        $fields = array(
            array(
                'type' => 'custom_form',
                'label' => $this->l('Related product'),
                'name' => 'related_products',
                'form_group_class'=> 'ets_pmn_form_related_product',
                'html_form' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/related.tpl'),
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function displayProductFeatures($id_massedit)
    {
        if($id_massedit > 1 && ($selected_features = Ets_pmn_massedit::getValueFieldByID($id_massedit,'features')))
        {
            $selected_features = json_decode($selected_features,true);
            $product_features = array();
            if(isset($selected_features['id_features']) && $selected_features['id_features'] && ($id_features = $selected_features['id_features']))
            {
                $id_feature_values = isset($selected_features['id_feature_values']) ? $selected_features['id_feature_values']:array();
                $feature_value_custom = isset($selected_features['feature_value_custom']) ? $selected_features['feature_value_custom']:array();
                foreach($id_features as $key=>$id_feature)
                {
                    if($id_feature)
                    {
                        $product_features[] = array(
                            'id_feature' => $id_feature,
                            'id_feature_value' => isset($id_feature_values[$key]) ? $id_feature_values[$key] : 0,
                            'feature_value' => array(
                                'custom' => isset($feature_value_custom[$key]) && $feature_value_custom[$key] ? 1 : 0,
                                'value' => isset($feature_value_custom[$key]) && $feature_value_custom[$key] ? $feature_value_custom[$key] :'',
                            )
                        );
                    }
                }
            }
        }
        else
            $product_features = array();
        $features= Ets_pmn_massedit::getFeatures();
        $features_values = Ets_pmn_massedit::getFeatureValues();
        $this->context->smarty->assign(
            array(
                'product_features' => $product_features,
                'features' =>$features ,
                'features_values' => $features_values,
            )
        );
        if($features || $product_features)
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/features.tpl');
        else
            return false;
    }
    public function renderFormQuantities(){
        $fields = array(
            array(
                'type' => 'text',
                'name' => 'quantity',
                'label' => $this->l('Quantity'),
                'condition_fields' => $this->condition_quantity_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'minimal_quantity',
                'label' => $this->l('Minimum quantity for sale'),
                'condition_fields' => $this->condition_quantity_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'location',
                'label' => $this->l('Stock location'),
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'low_stock_threshold',
                'label' => $this->l('Low stock level'),
                'condition_fields' => $this->condition_quantity_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'switch',
                'name'=> 'low_stock_alert',
                'label' => $this->l('Send me an email when the quantity is below or equals this level'),
                'condition_fields' => $this->condition_active_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'radio',
                'name' => 'out_of_stock',
                'label' => $this->l('Availability preferences'),
                'values' => array(
                    array(
                        'id' => 0,
                        'name' => $this->l('Deny orders'),
                    ),
                    array(
                        'id' => 1,
                        'name' => $this->l('Allow orders'),
                    ),
                    array(
                        'id' => 2,
                        'name' => $this->l('Use default behavior (Deny orders)'),
                    ),
                ),
                'condition_fields' => $this->condition_radio_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'available_now',
                'label' => $this->l('Label when in stock'),
                'lang' => true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'available_later',
                'label' => $this->l('Label when out of stock (and back order allowed)'),
                'lang' => true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormShipping()
    {
        if(($carriers = Ets_pmn_massedit::getListCarriers()))
        {
            foreach($carriers as &$carrier)
            {
                if(!$carrier['name'])
                    $carrier['name'] = Configuration::get('PS_SHOP_NAME');
                if($carrier['delay'])
                    $carrier['name'] .=' ('.$carrier['delay'].')';
            }
        }
        $this->context->smarty->assign(
            array(
                'carriers' => $carriers,
            )
        );
        $fields = array(
            array(
                'type' =>'text',
                'label' =>$this->l('Width'),
                'name' => 'width',
                'suffix' => $this->l('cm'),
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Height'),
                'name' => 'height',
                'suffix' => $this->l('cm'),
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Depth'),
                'name' => 'depth',
                'suffix' => $this->l('cm'),
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Weight'),
                'name' => 'weight',
                'suffix' => $this->l('kg'),
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Delivery time'),
                'name' => 'additional_delivery_times',
                'values' => array(
                    array(
                        'id' => 0,
                        'name' => $this->l('None'),
                    ),
                    array(
                        'id' => 1,
                        'name' => $this->l('Default delivery time'),
                    ),
                    array(
                        'id' => 2,
                        'name' => $this->l('Specific delivery time to this product'),
                    )
                ),
                'condition_fields' => $this->condition_radio_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delivery time of in-stock products'),
                'name' => 'delivery_in_stock',
                'lang' => true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delivery time of out-of-stock products with allowed orders'),
                'name' => 'delivery_out_stock',
                'lang' => true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Shipping fees'),
                'name' => 'additional_shipping_cost',
                'suffix' => $this->context->currency->sign,
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'checkbox',
                'name' => 'selectedCarriers',
                'label' => $this->l('Available carriers'),
                'values' => $carriers,
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            ),
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormSeo()
    {
        $fields=array(
            array(
                'type' => 'text',
                'name' => 'meta_title',
                'label' => $this->l('Meta title'),
                'lang'=> true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off',
                'short_codes' => array('name'=>'{name}','price'=>'{price}'),
            ),
            array(
                'type' => 'textarea',
                'name' => 'meta_description',
                'label' => $this->l('Meta description'),
                'lang'=> true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off',
                'short_codes' => array('name'=>'{name}','price'=>'{price}'),
            ),
            array(
                'type' => 'text',
                'name' => 'link_rewrite',
                'label' => $this->l('Friendly URL'),
                'lang'=> true,
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormOptions($id_massedit= false)
    {
        $fields = array(
            array(
                'name' => 'visibility',
                'type' => 'select',
                'label' => $this->l('Visibility'),
                'values' => array(
                    array(
                        'id'=>'both',
                        'name'=> $this->l('Everywhere')
                    ),
                    array(
                        'id'=>'catalog',
                        'name'=> $this->l('Catalog only')
                    ),
                    array(
                        'id'=>'search',
                        'name'=> $this->l('Search only')
                    ),
                    array(
                        'id'=>'none',
                        'name'=> $this->l('Nowhere')
                    ),
                ),
                'condition_fields' => $this->condition_radio_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'switch',
                'name'=> 'available_for_order',
                'label' => $this->l('Available for order'),
                'condition_fields' => $this->condition_active_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'switch',
                'name'=> 'online_only',
                'label' => $this->l('Web only (not sold in your retail store)'),
                'condition_fields' => $this->condition_active_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'tags',
                'name' => 'tags',
                'label' => $this->l('Tags'),
                'lang' => true,
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off',
                'desc' => $this->l('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.'),
            ),
            array(
                'name' => 'condition',
                'type' => 'select',
                'label' => $this->l('Condition'),
                'values' => array(
                    array(
                        'id'=>'new',
                        'name'=> $this->l('New')
                    ),
                    array(
                        'id'=>'used',
                        'name'=> $this->l('Used')
                    ),
                    array(
                        'id'=>'refurbished',
                        'name'=> $this->l('Refurbished')
                    ),
                    array(
                        'id'=>'none',
                        'name'=> $this->l('Nowhere')
                    ),
                ),
                'condition_fields' => $this->condition_radio_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'switch',
                'name'=> 'show_condition',
                'label' => $this->l('Display condition on product page'),
                'condition_fields' => $this->condition_active_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'isbn',
                'label' => $this->l('ISBN'),
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'ean13',
                'label' => $this->l('EAN-13 or JAN barcode'),
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'text',
                'name' => 'upc',
                'label' => $this->l('UPC barcode'),
                'condition_fields' => $this->condition_text_fields,
                'condition_fields_selected' => 'off',
            ),
            array(
                'type' => 'custom_form',
                'label' => $this->l('Customization'),
                'name' => 'customization',
                'html_form' => $this->renderFormCustomizationProduct($id_massedit),
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormCustomizationProduct($id_massedit)
    {
        if($id_massedit >1 && ($customizations = Ets_pmn_massedit::getValueFieldByID($id_massedit,'customization'))) {
            $customizations = json_decode($customizations,true);
            $this->context->smarty->assign(
                array(
                    'customizationFields' => $customizations,
                )
            );
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/customization.tpl');
    }
    public function renderFormPrice($id_massedit=false)
    {
        $fields = array(
            array(
                'type' =>'text',
                'label' =>$this->l('Price (tax excl.)'),
                'name' => 'price',
                'suffix' => $this->context->currency->sign,
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off',
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Price per unit (tax excl.)'),
                'name' => 'unit_price',
                'suffix' => $this->context->currency->sign,
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off',
            ),
            array(
                'type' => 'select',
                'name' => 'id_tax_rules_group',
                'label' => $this->l('Tax rule'),
                'values' => array_merge(array(array('id'=>'','name'=> $this->l('No tax'))), Ets_pmn_massedit::getTaxRulesGroups()),
                'condition_fields' => $this->condition_select_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' => 'switch',
                'name'=> 'on_sale',
                'label' => sprintf($this->l('Display the %sOn sale!%s flag on the product page, and on product listings.'),'"','"'),
                'condition_fields' => $this->condition_active_fields,
                'condition_fields_selected' => 'off'
            ),
            array(
                'type' =>'text',
                'label' =>$this->l('Cost price'),
                'name' => 'wholesale_price',
                'suffix' => $this->context->currency->sign,
                'condition_fields' => $this->condition_price_fields,
                'condition_fields_selected' => 'off',
            ),
            array(
                'type' => 'custom_form',
                'label' => $this->l('Specific prices'),
                'name' => 'specific_prices',
                'html_form' => $this->renderFormSpecificPrice($id_massedit),
                'condition_fields' => $this->condition_form_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function renderFormSpecificPrice($id_massedit=false)
    {
        $currencies = Ets_pmn_defines::getInstance()->getListCurrencies();
        $countries = Ets_pmn_defines::getInstance()->getListCountries();
        $groups = Ets_pmn_defines::getInstance()->getListGroups();
        if($id_massedit >1 && ($specific_price = Ets_pmn_massedit::getValueFieldByID($id_massedit,"specific_prices"))) {
            $specific_price = json_decode($specific_price);
        }
        else
            $specific_price= new SpecificPrice(0);
        $this->context->smarty->assign(
            array(
                'currencies' => $currencies,
                'countries' => $countries,
                'groups' => $groups,
                'default_currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'specific_price' => $specific_price,
                'specific_price_customer' => new Customer($specific_price->id_customer),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/specific_prices.tpl');
    }
    public function renderFormCombinations($id_massedit)
    {
        $attributeGroups = Ets_pmn_defines::getAttributeGroups();
        $selected_attributeGroups = array();
        if($id_massedit >1) {
            if ($selected_attributes = Ets_pmn_massedit::getValueFieldByID($id_massedit,'combinations')) {
                $selected_attributes = json_decode($selected_attributes, true);
                $selected_attributeGroups = array();
                if($selected_attributes)
                {
                    foreach($selected_attributes as $attributes)
                    {
                        $selected_attributeGroups = array_merge($selected_attributeGroups,$attributes);
                    }
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'attributeGroups'=>$attributeGroups,
                'selected_attributeGroups' => $selected_attributeGroups,
            )
        );

        $fields = array(
            array(
                'type' => 'custom_form',
                'label' => $this->l('Combinations'),
                'name' => 'combinations',
                'html_form' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/combinations.tpl'),
                'condition_fields' => $this->condition_multi_fields,
                'condition_fields_selected' => 'off'
            )
        );
        $this->context->smarty->assign(
            array(
                'fields' => $fields,
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_massedit/form.tpl');
    }
    public function _submitSaveMasseActionEdit()
    {
        $errors = array();
        $condition_field = Tools::getValue('condition_field');
        $id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit');
        $massedit = new Ets_pmn_massedit($id_ets_pmn_massedit);
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang_required = $id_lang_default;
        if(Validate::isLoadedObject($massedit))
        {
            if($condition_field && Ets_productmanager::validateArray($condition_field))
            {
                $product_language_fields = Ets_pmn_massedit::getInstance()->getProductLanguageFields();
                $product_fields = Ets_pmn_massedit::getInstance()->getProductFields();
                $edit = false;
                foreach($condition_field as $field=> $condition)
                {
                    
                    if($condition!='off')
                    {
                        $edit = true;
                        if($condition=='remove_all')
                            continue;
                        if(isset($product_language_fields[$field]) || $field=='tags')
                        {
                            $errors_required = $field;
                            $id_lang_required = $id_lang_default;
                            foreach($languages as $language)
                            {
                                $value_lang = Tools::getValue($field.'_'.(int)$language['id_lang']);
                                if($value_lang!='')
                                {
                                    $errors_required = false;
                                }
                            }
                            if($errors_required)
                                break;
                        }
                        else
                        {
                            $value = Tools::getValue($field);
                            if(!in_array($field,array('active','low_stock_alert','on_sale','online_only','show_condition','available_for_order')))
                            {
                                if($field=='features')
                                {
                                    $id_features  = Tools::getValue('id_features');
                                    $id_feature_values = Tools::getValue('id_feature_values');
                                    $feature_value_custom = Tools::getValue('feature_value_custom');
                                    $ok = false;
                                    if(($id_features && Ets_productmanager::validateArray($id_features) && Ets_productmanager::validateArray($id_feature_values) && Ets_productmanager::validateArray($feature_value_custom)) || $condition=='remove_all')
                                    {
                                        foreach($id_features as $index=>$id_feature)
                                        {
                                            if($id_feature && ((isset($id_feature_values[$index]) && $id_feature_values[$index]) || (isset($feature_value_custom[$index]) && $feature_value_custom[$index])))
                                                $ok = true;
                                        }
                                    }
                                    if(!$ok)
                                    {
                                        $errors_required = $field;
                                        $id_lang_required =0;
                                        break;
                                    }
                                }elseif($field=='combinations')
                                {
                                    $attribute_options = Tools::getValue('attribute_options');
                                    if(!$attribute_options)
                                    {
                                        $errors_required = $field;
                                        $id_lang_required =0;
                                        break;
                                    }
                                }
                                elseif($field=='out_of_stock' || $field=='additional_delivery_times')
                                {
                                    if(trim($value)==='')
                                    {
                                        $errors_required = $field;
                                        $id_lang_required =0;
                                        break;
                                    }
                                    
                                }elseif($field=='customization')
                                {
                                    $custom_fields = Tools::getValue('custom_fields');
                                    $ok = false;
                                    if(($custom_fields && Ets_productmanager::validateArray($custom_fields)))
                                    {
                                        foreach($custom_fields as $custom_field)
                                        {
                                            if(isset($custom_field['label']) && $custom_field['label'])
                                            {
                                                foreach($custom_field['label'] as $label)
                                                    if($label)
                                                        $ok = true;
                                            }
                                        }
                                    }
                                    if(!$ok)
                                    {
                                        $errors_required = $field;
                                        $id_lang_required = 0;
                                        break;
                                    }
                                }
                                elseif($field=='specific_prices')
                                {
                                    $specific_prices = Tools::getValue('specific_prices');
                                    if(!Ets_productmanager::validateArray($specific_prices) || $specific_prices['sp_reduction']=='')
                                    {
                                        $errors_required = $field;
                                        $id_lang_required = 0;
                                        break;
                                    }
                                }
                                elseif($value=='' && $field!='id_tax_rules_group')
                                {
                                    $errors_required = $field;
                                    $id_lang_required = 0;
                                    break;
                                }
                            }
                        }
                        
                    }
                }
                if(isset($errors_required) && $errors_required)
                {
                    die(
                        json_encode(
                            array(
                                'error_required' => $this->l('Enter value for all selected actions'),
                                'id_lang_required' => $id_lang_required,
                                'field' => $errors_required
                            )
                        )
                    );
                }
                if($edit)
                {
                    $fields_error = array();
                    foreach($condition_field as $field =>$condition)
                    {
                        if($condition!='off' && $condition!='remove_all')
                        {
                            if(isset($product_language_fields[$field]))
                            {
                                $validate = $product_language_fields[$field]['validate'];
                                foreach($languages as $language)
                                {
                                    $value_lang = Tools::getValue($field.'_'.$language['id_lang']);
                                    if($field=='name' || $field=='meta_title' || $field=='meta_description')
                                    {
                                        $value_lang = str_replace(array('{name}','{price}','{url}'),'',$value_lang);
                                    }
                                    if($value_lang && method_exists('Validate',$validate)&& !Validate::{$validate}($value_lang,true))
                                    {
                                        $errors[] = sprintf($this->l('%s%s%s  is not valid in language %s'),'"',$product_language_fields[$field]['title'],'"',$language['iso_code']);
                                        $fields_error[] = array(
                                            'field_error' => $field,
                                            'id_lang' => (int)$language['id_lang'],
                                        );
                                    }
                                    elseif($condition=='replace')
                                    {
                                        $size = isset($product_language_fields[$field]['size']) ? (int)$product_language_fields[$field]['size']:0;
                                        if($value_lang && $size && Tools::strlen(strip_tags($value_lang)) > $size)
                                        {
                                            $errors[] = sprintf($this->l('%s%s%s is too long in language %s. It should have %s characters or less'),'"',$product_language_fields[$field]['title'],'"',$language['iso_code'],$size);
                                            $fields_error[] = array(
                                                'field_error' => $field,
                                                'id_lang' => (int)$language['id_lang'],
                                            );
                                        }
                                    }
                                }
                            }
                            elseif(isset($product_fields[$field]))
                            {
                                $validate = $product_fields[$field]['validate'];
                                $value = Tools::getValue($field);
                                if($field=='price' || $field=='unit_price' || $field=='wholesale_price')
                                        $value = str_replace(array('{price}'),'',$value);
                                if($value && method_exists('Validate',$validate)&& !Validate::{$validate}($value) )
                                {
                                    $errors[] = sprintf($this->l('%s%s%s is not valid'),'"',$product_fields[$field]['title'],'"');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                elseif($condition=='replace')
                                {
                                    $size = isset($product_fields[$field]['size']) ? (int)$product_fields[$field]['size']:0;    
                                    if($size && $size < Tools::strlen($value))
                                    {
                                        $errors[] = sprintf($this->l('%s%s%s is too long. It should have %s characters or less'),'"',$product_fields[$field]['title'],'"',$size);
                                        $fields_error[] = array(
                                            'field_error' => $field,
                                            'id_lang' => 0,
                                        );
                                    }
                                        
                                }    
                            }elseif($field=='specific_prices')
                            {
                                $specific_prices = Tools::getValue('specific_prices');
                                if(!Ets_productmanager::validateArray($specific_prices))
                                {
                                    $errors[] = $this->l('Data specific price is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                elseif(!$specific_prices['sp_reduction'])
                                {
                                    $errors[] = $this->l('Apply a discount of is required');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                elseif(!Validate::isUnsignedFloat($specific_prices['sp_reduction']))
                                {
                                    $errors[] = $this->l('Apply a discount of is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                elseif(isset($specific_prices['sp_reduction_type']) && $specific_prices['sp_reduction_type']=='percentage' && $specific_prices['sp_reduction']<=0 || $specific_prices['sp_reduction']>100)
                                {
                                    $errors[] = $this->l('Apply a discount of is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                if(isset($specific_prices['from']) && ($from= $specific_prices['from']) && !Validate::isDate($from))
                                {
                                    $errors[] = $this->l('Available from is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                if(isset($specific_prices['to']) && ($to= $specific_prices['to']) && !Validate::isDate($to))
                                {
                                    $errors[] = $this->l('Available to is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                if(isset($specific_prices['from']) && ($from= $specific_prices['from']) && Validate::isDate($from) && isset($specific_prices['to']) && ($to= $specific_prices['to']) && Validate::isDate($to) && strtotime($from) >strtotime($to))
                                {
                                    $errors[] = $this->l('Available to smaller than available from');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                if(isset($specific_prices['product_price']) && ($product_price = Tools::getValue('product_price')) && !Validate::isNegativePrice($product_price))
                                {
                                    $errors[] = $this->l('Product price is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                if(isset($specific_prices['from_quantity']) && ($from_quantity = $specific_prices['from_quantity']) && !Validate::isUnsignedId($from_quantity))
                                {
                                    $errors[] = $this->l('Starting at is not valid');
                                    $fields_error[] = array(
                                        'field_error' => $field,
                                        'id_lang' => 0,
                                    );
                                }
                                
                            }
                        }
                    }
                    if(isset($condition_field['name']) && $condition_field['name']=='replace')
                    {
                        $name_default = Tools::getValue('name_'.$id_lang_default);
                        if(!$name_default)
                        {
                            $errors[] = $this->l('Product name is required.');
                            $fields_error[] = array(
                                'field_error' => 'name',
                                'id_lang' => $id_lang_default,
                            );
                        }
                    }
                    if(isset($condition_field['id_category_default']) && $condition_field['id_category_default']!='off')
                    {
                        $id_category_default = (int)Tools::getValue('id_category_default');
                        if(!$id_category_default)
                        {
                            $errors[] = $this->l('Default category is required.');
                            $fields_error[] = array(
                                'field_error' => 'id_category_default',
                                'id_lang' => 0,
                            );
                        }
                    }
                }
                else
                {
                    $errors[] = $this->l('There is not any selected field');
                }
                    
            }
            else
                $errors[] = $this->l('Condition field is not valid');
        }
        else
            $errors[] = $this->l('Mass Edit is not valid');
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors),
                        'fields_error' => isset($fields_error) && $fields_error ? $fields_error : false,
                    )
                )
            );
        }
        else
        {
            Ets_pmn_massedit_condition_action::deleteConditioField($id_ets_pmn_massedit);
            foreach($condition_field as $field =>$condition)
            {
                if($condition!='off')
                {
                    $conditionFieldObj = new Ets_pmn_massedit_condition_action();
                    $conditionFieldObj->id_ets_pmn_massedit = $id_ets_pmn_massedit;
                    $conditionFieldObj->condition = $condition;
                    $conditionFieldObj->field = $field;
                    $conditionFieldObj->id_ets_pmn_massedit_history = 0;
                    if(isset($product_language_fields[$field]) || $field=='tags')
                    {
                        foreach($languages as $language)
                        {
                            $value_lang = Tools::getValue($field.'_'.$language['id_lang']);
                            $conditionFieldObj->value_lang[$language['id_lang']] = Validate::isCleanHtml($value_lang,true) ? $value_lang :'';
                        }
                        $conditionFieldObj->add();
                    }
                    elseif(isset($product_fields[$field]))
                    {
                        $value = Tools::getValue($field);
                        $conditionFieldObj->value = Validate::isCleanHtml($value,true) ? $value :'';  
                        foreach($languages as $language)
                        {
                            $conditionFieldObj->value_lang[$language['id_lang']] = 'false';
                        }
                        $conditionFieldObj->add();
                    }
                    elseif($field=='features')
                    {   
                        $id_features  = Tools::getValue('id_features');
                        $id_feature_values = Tools::getValue('id_feature_values');
                        $feature_value_custom = Tools::getValue('feature_value_custom');
                        if(($id_features && Ets_productmanager::validateArray($id_features) && Ets_productmanager::validateArray($id_feature_values) && Ets_productmanager::validateArray($feature_value_custom)) || $condition=='remove_all')
                        {
                            if($condition!='remove_all')
                            {
                                $value = array(
                                    'id_features' => $id_features,
                                    'id_feature_values' => $id_feature_values,
                                    'feature_value_custom' => $feature_value_custom,
                                );
                                $conditionFieldObj->value = json_encode($value); 
                            }
                            else
                                $conditionFieldObj->value ='' ;
                            foreach($languages as $language)
                            {
                                $conditionFieldObj->value_lang[$language['id_lang']] = 'false';
                            }
                            $conditionFieldObj->add();
                        }
                        
                    }
                    elseif($field=='customization')
                    {
                        $custom_fields = Tools::getValue('custom_fields');
                        if(($custom_fields && Ets_productmanager::validateArray($custom_fields)) || $condition=='remove_all')
                        {
                            if($condition=='remove_all')
                                $conditionFieldObj->value='';
                            else
                                $conditionFieldObj->value = json_encode($custom_fields);  
                            foreach($languages as $language)
                            {
                                $conditionFieldObj->value_lang[$language['id_lang']] = 'false';
                            }
                            $conditionFieldObj->add();
                        }
                    }elseif($field=='combinations')
                    {
                        $attribute_options = Tools::getValue('attribute_options');
                        if(($attribute_options && Ets_productmanager::validateArray($attribute_options)) || $condition=='remove_all')
                        {
                            $conditionFieldObj->value = $condition=='remove_all' ?  '': json_encode($attribute_options);  
                            foreach($languages as $language)
                            {
                                $conditionFieldObj->value_lang[$language['id_lang']] = 'false';
                            }
                            $conditionFieldObj->add();
                        }
                    }
                    else
                    {
                        $value = Tools::getValue($field);
                        if($condition=='remove_all')
                            $conditionFieldObj->value ='';
                        else
                            $conditionFieldObj->value =  is_array($value) && Ets_productmanager::validateArray($value) ? json_encode($value):$value;  
                        foreach($languages as $language)
                        {
                            $conditionFieldObj->value_lang[$language['id_lang']] = 'false';
                        }
                        $conditionFieldObj->add();
                    }
                    
                }
            }
            $this->context->smarty->assign(
                array(
                    'languages' => Language::getLanguages(false),
                    'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                    'default_currency' => $this->context->currency
                )
            );
            $product_excluded = Tools::getValue('product_exclued',$massedit->excluded);
            $product_excluded = $product_excluded && Validate::isCleanHtml($product_excluded) ? $product_excluded:'';
            die(
                json_encode(
                    array(
                        'success' => $this->l('Submitted successfully'),
                        'html_form' => $massedit->renderFormPreviewMassedit($product_excluded),
                        'url_reload' => $this->context->link->getAdminLink('AdminProductManagerMassiveEdit').'&id_ets_pmn_massedit='.$massedit->id.'&previewmassedit=1', 
                    )
                )
            );
        }
    }
    public function _submitSaveMasseEditProduct()
    {
        
        if(($id_ets_pmn_massedit = (int)Tools::getValue('id_ets_pmn_massedit')) && ($massedit = new Ets_pmn_massedit($id_ets_pmn_massedit)) && Validate::isLoadedObject($massedit))
        {
            
            if($id_ets_massedit_history = (int)Tools::getValue('id_ets_massedit_history'))
            {
                $condition_actions = $massedit->getEditActions($id_ets_massedit_history);
                $masseditHistory = new Ets_pmn_massedit_history($id_ets_massedit_history);
                $total_products = (int)Tools::getValue('total_products');
                if($this->context->cookie->ets_pmn_log_errors)
                {
                   $log_errros = json_decode($this->context->cookie->ets_pmn_log_errors,true);
                }
                else
                    $log_errros = array();
                $current_page = (int)Tools::getValue('current_page');
            }
            else
            {
                $condition_actions = $massedit->getEditActions();
                $log_errros = array();
                $this->context->cookie->ets_pmn_log_errors= '';
                $total_products  =0;
                $current_page =1;
                if($condition_actions)
                {
                    $masseditHistory = new Ets_pmn_massedit_history();
                    $masseditHistory->id_ets_pmn_massedit = $massedit->id;
                    $masseditHistory->id_shop = $this->context->shop->id;
                    $masseditHistory->edited_field=0;
                    $fields = '';
                    foreach($condition_actions as $action)
                    {
                        $fields .= $action['field'].',';
                    }
                    $masseditHistory->fields = trim($fields,',');
                    $masseditHistory->add();
                }
            }
            
            if($condition_actions && isset($masseditHistory) && Validate::isLoadedObject($masseditHistory))
            {
                $limit = (int)Configuration::get('ETS_PMN_NUMBER_PRODUCT_EDIT_EACH_AJAX') ? :false;
                $conditions = $massedit->getConditions();
                $start = ($current_page-1)*$limit;
                $products = $masseditHistory->getProducts($conditions,false,$start,$limit,$massedit->excluded,$massedit->type_combine_condition);
                if(!$id_ets_massedit_history && !$products)
                {
                    $log_errros[] = $this->l('Cannot find any products that satisfy your condition');
                }
                if($products)
                {
                    $masseditHistory->saveMasseEditProduct($products,$condition_actions,$log_errros,$total_products);
                    $this->context->cookie->ets_pmn_log_errors = json_encode($log_errros);
                    if($limit)
                    {
                        die(
                            json_encode(
                                array(
                                    'edit_continue' => true,
                                    'id_ets_massedit_history' => $masseditHistory->id,
                                    'total_products' => $total_products,
                                    'current_page' => $current_page+1,
                                )
                            )
                        );
                    }
                    else
                    {
                        if($log_errros)
                        {
                            die(
                                json_encode(
                                    array(
                                        'errors' => $this->module->displayError($log_errros),
                                    )
                                )
                            );
                        }
                        else
                        {
                            die(
                                json_encode(
                                    array(
                                        'success' => $this->module->displayConfirmation(sprintf($this->l('%d product(s) updated successfully'),$total_products)),
                                        'product_list' =>Configuration::get('ETS_PMN_SAVE_EDIT_LOG') ? $this->module->renderListLogs($masseditHistory) : $masseditHistory->displayLogNotAvalible() ,
                                    )
                                )
                            );
                        }
                    }
                }
                else
                {
                    if($log_errros)
                    {
                        die(
                            json_encode(
                                array(
                                    'errors' => $this->module->displayError($log_errros),
                                )
                            )
                        );
                    }
                    else
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->displayConfirmation(sprintf($this->l('%d product(s) updated successfully'),$total_products)),
                                    'product_list' =>Configuration::get('ETS_PMN_SAVE_EDIT_LOG') ? $this->module->renderListLogs($masseditHistory) : $masseditHistory->displayLogNotAvalible() ,
                                )
                            )
                        );
                    }
                }
            }
        }
    }
    public function processSubmitSaveNameMassedit()
    {
        $id_massedit = (int)Tools::getValue('id_massedit');
        $name = Tools::getValue('massedit_name');
        $errors ='';
        if(!$name)
        {
            $errors = $this->l('Mass Edit name is required');
        }
        elseif(!Validate::isCleanHtml($name))
            $errors = $this->l('Mass Edit name is not valid');
        
        if(!$errors)
        {
            if($id_massedit==1)
            {
                $massedit = new Ets_pmn_massedit();
                $massedit->id_shop = $this->context->shop->id;
            }
            else
                $massedit = new Ets_pmn_massedit($id_massedit);
            $massedit->name = $name;
            $massedit->deleted =0;
            if($massedit->save())
            {
                die(json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                        'massedits_list' => $massedit->renderTemplateMassedit(),
                        'id_ets_pmn_massedit' => $massedit->id,
                    )
                ));
            }
            else
                $errors = $this->l('An error occurred while saving the mass edit name');
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => $errors,
                    )
                )
            );
        }
    }
    public function renderFormMassedit($massedit=false,$edit_template = false)
    {
        if($massedit==false)
            $massedit = Ets_pmn_massedit::getInstance();
        $conditions = $massedit->getConditions();
        $product_excluded = Tools::getValue('product_exclued',$massedit->excluded);
        $product_excluded = $product_excluded && Validate::isCleanHtml($product_excluded) ?$product_excluded:'';
        $massedit_name = Tools::getValue('massedit_name',$massedit->name);
        Context::getContext()->smarty->assign(
            array(
                'row_form_massedit' => $massedit->renderFormRowMassedit(),
                'conditions' => $conditions,
                'product_excluded' => ','.trim($product_excluded,',').',',
                'massedit_name' => $massedit_name,
                'id_ets_pmn_massedit' => $massedit->id,
                'deleted' => $massedit->deleted,
                'type_combine_condition' => $massedit->type_combine_condition,
                'step_massedit_html' => $massedit->renderStepMassedit(1),
                'product_list' => $massedit->renderListProductsByConditions($conditions,$product_excluded,true,(int)Tools::getValue('page'),(int)Tools::getValue('paginator_matching_products_select_limit',10),(int)Tools::getValue('stepNumber')),
                'edit_template' => $edit_template,
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/form_massedit.tpl');
    }
}