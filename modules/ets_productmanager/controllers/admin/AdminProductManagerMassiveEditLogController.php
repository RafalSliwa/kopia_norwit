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
 * Class AdminProductManagerMassiveEditLogController
 * @property Ets_productmanager $module
 */
class AdminProductManagerMassiveEditLogController extends ModuleAdminController
{
    public $_errors = array();
    public function __construct()
    {
       parent::__construct();
       $this->context= Context::getContext();
       $this->bootstrap = true;
    }
    public function init()
    {
        parent::init();
        if(Tools::isSubmit('submitSearchProductEdit') && ($id_ets_pmn_massedit_history = (int)Tools::getValue('id_ets_pmn_massedit_history'))&& ($masedit = new Ets_pmn_massedit_history($id_ets_pmn_massedit_history)))
        {
            die(
                json_encode(
                    array(
                        'product_list' => $this->module->renderListLogs($masedit),
                    )
                )
            );
        }
    }
    public function renderList()
    {
        if(!$this->module->active)
            return $this->module->displayWarning($this->l('You must enable Product Manager module to configure its features'));
        if(Tools::isSubmit('del') && ($id_ets_pmn_massedit_history_product = (int)Tools::getValue('id_ets_pmn_massedit_history_product')))
        {
            $history_product = new Ets_pmn_massedit_history_product($id_ets_pmn_massedit_history_product);
            if(Validate::isLoadedObject($history_product))
                $history_product->delete();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditLog').'&conf=2');
        }
        if(Tools::isSubmit('restorepmn_logmassedit') && ($id_ets_pmn_massedit_history_product = (int)Tools::getValue('id_ets_pmn_massedit_history_product')) && ($history_product = new Ets_pmn_massedit_history_product($id_ets_pmn_massedit_history_product)) && Validate::isLoadedObject($history_product))
        {
            if($this->_submitRestore($history_product))
            {
                $this->context->cookie->_success = $this->l('Restored successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditLog'));
            }
        }
        if(Tools::isSubmit('btnDeleteSelection'))
        {
            $pmn_logmassedit_boxs = Tools::getValue('pmn_logmassedit_boxs');
            if($pmn_logmassedit_boxs && Ets_productmanager::validateArray($pmn_logmassedit_boxs,'isInt'))
            {
                foreach($pmn_logmassedit_boxs as $id_logmassedit)
                {
                    if(($history_product = new Ets_pmn_massedit_history_product($id_logmassedit)) && Validate::isLoadedObject($history_product))
                        $history_product->delete();
                }
                $this->context->cookie->_success = $this->l('Deleted selection successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditLog'));
            }
        }
        if(Tools::isSubmit('btnRestoreSelection'))
        {
            $pmn_logmassedit_boxs = Tools::getValue('pmn_logmassedit_boxs');
            if($pmn_logmassedit_boxs && Ets_productmanager::validateArray($pmn_logmassedit_boxs,'isInt'))
            {
                $ok = true;
                foreach($pmn_logmassedit_boxs as $id_logmassedit)
                {
                    if(($history_product = new Ets_pmn_massedit_history_product($id_logmassedit)) && Validate::isLoadedObject($history_product))
                    {
                        if(!$this->_submitRestore($history_product))
                        {
                            $ok = false;
                            break;
                        }
                    }
                }
                if($ok)
                {
                    $this->context->cookie->_success = $this->l('Restore selection successfully');
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditLog'));
                }
            }
        }
        if(Tools::isSubmit('clearAllLog'))
        {
            if(Ets_pmn_massedit_log::deleteAllLog())
            {
                $this->context->cookie->_success = $this->l('Clear log successfully');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductManagerMassiveEditLog'));
            }
        }
        $html ='';
        if($this->_errors)
            $html .= $this->module->displayError($this->_errors);
        elseif($this->context->cookie->_success)
        {
            $html .= $this->module->displayConfirmation($this->context->cookie->_success);
            $this->context->cookie->_success='';
        }
        $html .= $this->module->renderListLogs();
        $this->context->smarty->assign(
            array(
                'ets_pmn_body_html' => $html,
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'massive.tpl');
    }
    public function _submitRestore($history_product)
    {
        $id_product = (int)$history_product->id_product;
        $field = $history_product->field_name;
        if(($product = new Product($id_product)) && Validate::isLoadedObject($product))
        {
            $product_language_fields = Ets_pmn_massedit::getInstance()->getProductLanguageFields();
            $product_fields = Ets_pmn_massedit::getInstance()->getProductFields();
            if(isset($product_language_fields[$field]))
            {
                $validate = $product_language_fields[$field]['validate'];
                if(!$history_product->old_value || Validate::{$validate}($history_product->old_value))
                {
                    $id_lang = (int)$history_product->id_lang;
                    $product->{$field}[$id_lang] = $history_product->old_value;
                    
                    if($product->update())
                    {
                        $history_product->delete();
                        return true;
                    }
                    else
                        $this->_errors[] = $this->l('An error occurred while restoring'); 
                }
                else
                    $this->_errors[] = $this->l('Old value is not valid');
                
            }
            elseif(isset($product_fields[$field]) && $field!='quantity' && $field!='location' && $field!='out_of_stock')
            {
                
                $validate = $product_fields[$field]['validate'];
                if(!$history_product->old_value || Validate::{$validate}($history_product->old_value))
                {
                    $product->{$field} = $history_product->old_value;
                    if($product->update())
                    {
                        if($field=='id_category_default')
                            $history_product->restoreCategoryDefault($product->id_category_default);
                        $history_product->delete();
                        return true;
                    }
                }
                else
                    $this->_errors[] = $this->l('Old value is not valid');
            }
            else
            {
                $history_product->restoreProduct($product);
                if(!$this->module->_errors)
                {
                    $history_product->delete();
                    return true;
                }
                else
                    $this->_errors = $this->module->_errors;
            }
        }
        else
            $this->_errors[] = $this->l('Product to restore is not valid');
    }
}