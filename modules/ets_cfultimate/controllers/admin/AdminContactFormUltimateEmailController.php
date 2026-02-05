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

class AdminContactFormUltimateEmailController extends ModuleAdminController
{
    public $_html;
    public $_fields_form;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->_fields_form = Ets_cfudefines::getInstance($this->module)->getFields('email');
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $errors = array();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $inputs = $this->_fields_form['form']['input'];
        $languages = Language::getLanguages(false);
        if (Tools::isSubmit('etsCfuBtnSubmit')) {
            if ($inputs) {
                foreach ($inputs as $input) {
                    $key = $input['name'];
                    if (isset($input['lang']) && $input['lang']) {
                        if (isset($input['required']) && $input['required'] && !Tools::getValue($key . '_' . $id_lang_default))
                            $errors[] = $input['label'] . ' ' . $this->module->l('is required');
                        elseif (isset($input['validate']) && method_exists('Validate', $input['validate'])) {
                            $validate = $input['validate'];
                            if (!Validate::$validate(trim(Tools::getValue($key . '_' . $id_lang_default))))
                                $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                            else {
                                if ($languages) {
                                    foreach ($languages as $lang) {
                                        if (Tools::getValue($key . '_' . $lang['id_lang']) && !Validate::$validate(trim(Tools::getValue($key . '_' . $lang['id_lang']))))
                                            $errors[] = $input['label'] . ' ' . $lang['iso_code'] . ' ' . $this->module->l('is invalid');
                                    }
                                }
                            }
                            unset($validate);
                        }
                    } elseif (isset($input['required']) && $input['required'] && !Tools::getValue($key))
                        $errors[] = $input['label'] . ' ' . $this->module->l('is required');
                    elseif (isset($input['validate']) && method_exists('Validate', $input['validate'])) {
                        $validate = $input['validate'];
                        if (!Validate::$validate(trim(Tools::getValue($key))))
                            $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                        unset($validate);
                    }
                }
            }
            if (!$errors)
            {
                if ($inputs) {
                    foreach ($inputs as $input) {
                        $key = $input['name'];
                        if (isset($input['lang']) && $input['lang']) {
                            $vals = array();
                            foreach ($languages as $language) {
                                $vals[$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang']) ? Tools::getValue($key . '_' . $language['id_lang']) : Tools::getValue($key . '_' . $id_lang_default);
                            }
                            Configuration::updateValue($key, $vals, true);
                        } else {
                            Configuration::updateValue($key, Tools::getValue($key));
                        }
                    }
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateEmail') . '&conf=4');
            }
        }
        $this->context->smarty->assign(
            array(
                'form_config' => $this->module->renderFormEmail(),
                'errors' => $errors? $this->module->displayError($errors) : false,
            )
        );
        $this->_html .= $this->module->display($this->module->getLocalPath(), 'form.tpl');
        return $this->_html;
    }
}