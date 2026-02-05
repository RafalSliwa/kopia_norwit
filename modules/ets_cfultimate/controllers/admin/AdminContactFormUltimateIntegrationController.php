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

class AdminContactFormUltimateIntegrationController extends ModuleAdminController
{
    public $_html;
    public $_fields_form;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->_fields_form = Ets_cfudefines::getInstance($this->module)->getFields('config');
    }

    public function initContent()
    {
        parent::initContent();
    }

    /**
     * @return false|string
     * @throws PrestaShopException
     */
    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $errors = array();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $inputs = $this->_fields_form['form']['input'];
        $languages = Language::getLanguages(false);
        if (Tools::isSubmit('etsCfuImportContactSubmit')) {
            $this->module->processImport();
            $errors = $this->module->getErrors();
        } else {
            if (Tools::isSubmit('etsCfuBtnSubmit')) {
                if ($inputs) {
                    foreach ($inputs as $input) {
                        $key = $input['name'];
                        if (isset($input['lang']) && $input['lang']) {
                            if (isset($input['required']) && $input['required'] && !Tools::getValue($key . '_' . $id_lang_default)) {
                                $errors[] = $input['label'] . ' ' . $this->module->l('is required');
                            } elseif (isset($input['validate']) && method_exists('Validate', $input['validate'])) {
                                $validate = $input['validate'];
                                if (!Validate::$validate(trim(Tools::getValue($key . '_' . $id_lang_default)))) {
                                    $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                                } elseif ($languages) {
                                    foreach ($languages as $lang) {
                                        if (Tools::getValue($key . '_' . $lang['id_lang']) && !Validate::$validate(trim(Tools::getValue($key . '_' . $lang['id_lang'])))) {
                                            $errors[] = $input['label'] . ' ' . $lang['iso_code'] . ' ' . $this->module->l('is invalid');
                                        }
                                    }
                                }
                                unset($validate);
                            }
                        } elseif (isset($input['required']) && $input['required'] && $this->fieldRequired($input)) {
                            $errors[] = $input['label'] . ' ' . $this->module->l('is required');
                        } elseif (isset($input['validate']) && method_exists('Validate', $input['validate'])) {
                            $validate = $input['validate'];
                            if (!is_array(Tools::getValue($key)) && trim(Tools::getValue($key)) !== '' && !Validate::$validate(trim(Tools::getValue($key))))
                                $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                            unset($validate);
                        } elseif($key == 'ETS_CFU_SCORE_CAPTCHA_V3' && Tools::getValue($key)) {
                        	$val = (float) Tools::getValue($key);
                        	if ($val <= 0 || $val >= 1)
		                        $errors[] = $input['label'] . ' ' . $this->module->l('must greater 0 and less than or equal 1');
                        }
                    }
                }
                if (!$errors) {
                    if ($inputs) {
                        foreach ($inputs as $input) {
                            $key = $input['name'];
                            if (isset($input['lang']) && $input['lang']) {
                                $values = array();
                                foreach ($languages as $language) {
                                    $values[$language['id_lang']] = Tools::getValue($key . '_' . $language['id_lang']) ? Tools::getValue($key . '_' . $language['id_lang']) : Tools::getValue($key . '_' . $id_lang_default);
                                }
                                Configuration::updateValue($key, $values, true);
                            } else {
                                Configuration::updateValue($key, Tools::getValue($key));
                                if (!(int)Configuration::get('ETS_CFU_CACHE_ENABLED')) {
                                    ETS_CFU_SmartyCache::clearCacheAllSmarty('*');
                                }
                            }
                        }
                    }
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateIntegration') . '&conf=4&current_tab=' . Tools::getValue('current_tab'));
                }
            }
        }
        $this->context->smarty->assign(array(
            'form_config' => $this->module->renderFormConfig(),
            'errors' => $errors ? $this->module->displayError($errors) : false,
        ));
        $this->_html .= $this->module->display($this->module->getLocalPath(), 'form.tpl');
        return $this->_html;
    }

    public function fieldRequired($input)
    {
        switch ($input['name']) {
            case 'ETS_CFU_SITE_KEY' :
                if (Tools::getValue('ETS_CFU_ENABLE_RECAPTCHA') && Tools::getValue('ETS_CFU_RECAPTCHA_TYPE') == 'v2' && !Tools::getValue('ETS_CFU_SITE_KEY'))
                    return true;
                break;
            case 'ETS_CFU_SECRET_KEY' :
                if (Tools::getValue('ETS_CFU_ENABLE_RECAPTCHA') && Tools::getValue('ETS_CFU_RECAPTCHA_TYPE') == 'v2' && !Tools::getValue('ETS_CFU_SECRET_KEY'))
                    return true;
                break;
            case 'ETS_CFU_SITE_KEY_V3' :
                if (Tools::getValue('ETS_CFU_ENABLE_RECAPTCHA') && Tools::getValue('ETS_CFU_RECAPTCHA_TYPE') == 'v3' && !Tools::getValue('ETS_CFU_SITE_KEY_V3'))
                    return true;
                break;
            case 'ETS_CFU_SECRET_KEY_V3' :
                if (Tools::getValue('ETS_CFU_ENABLE_RECAPTCHA') && Tools::getValue('ETS_CFU_RECAPTCHA_TYPE') == 'v3' && !Tools::getValue('ETS_CFU_SECRET_KEY_V3'))
                    return true;
                break;
            case 'ETS_CFU_SCORE_CAPTCHA_V3' :
                if (Tools::getValue('ETS_CFU_ENABLE_RECAPTCHA') && Tools::getValue('ETS_CFU_RECAPTCHA_TYPE') == 'v3' && !Tools::getValue('ETS_CFU_SCORE_CAPTCHA_V3'))
                    return true;
                break;
            case 'ETS_CFU_CACHE_LIFETIME' :
                if ((int)Tools::getValue('ETS_CFU_CACHE_ENABLED') > 0 && trim(Tools::getValue('ETS_CFU_CACHE_LIFETIME')) == '')
                    return true;
                break;
            default :
                if (!Tools::getValue($input['name']))
                    return true;
                break;
        }
        return false;
    }

    public function ajaxProcessClearCache()
    {
        if (!ETS_CFU_SmartyCache::clearCacheAllSmarty('*'))
            $this->errors[] = $this->l('Cache is empty.', 'AdminContactFormUltimateIntegrationController');
        die(json_encode([
            'errors' => count($this->errors) > 0 ? implode(PHP_EOL, $this->errors) : false,
            'msg' => $this->l('Clear cache successfully', 'AdminContactFormUltimateIntegrationController')
        ]));
    }
}