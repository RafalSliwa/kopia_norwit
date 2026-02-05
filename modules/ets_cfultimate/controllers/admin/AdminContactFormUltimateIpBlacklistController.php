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

class AdminContactFormUltimateIpBlacklistController extends ModuleAdminController
{
    /**
     * @var string
     */
    public $_html;
    /**
     * @var array
     */
    public $_fields_form;

    /**
     * AdminContactFormUltimateIpBlacklistController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->_fields_form = Ets_cfudefines::getInstance($this->module)->getFields('ip_black_list');
    }

    /**
     * @see parent::initContent();
     */
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
        $languages = Language::getLanguages(false);
        if (($inputs = $this->_fields_form['form']['input']) && Tools::isSubmit('etsCfuBtnSubmit')) {
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
                } elseif (isset($input['required']) && $input['required'] && !Tools::getValue($key)) {
                    $errors[] = $input['label'] . ' ' . $this->module->l('is required');
                } elseif (isset($input['validate']) && method_exists('Validate', $input['validate'])) {
                    $validate = $input['validate'];
                    if (!Validate::$validate(trim(Tools::getValue($key))))
                        $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                    unset($validate);
                } elseif ($key == 'ETS_CFU_IP_BLACK_LIST' && ($ip_blacklist = trim(Tools::getValue($key))) != '' && !preg_match('/^(([0-9A-Fa-f\.\*:])+(\n|(\r\n))*)+$/', $ip_blacklist)) {
                    $errors[] = $input['label'] . ' ' . $this->module->l('is invalid');
                } elseif ($key == 'ETS_CFU_EMAIL_BLACK_LIST' && ($email_blacklist = Tools::getValue($key)) != '') {
                    $email_blacklists = explode("\n", $email_blacklist);
                    $ik = 0;
                    foreach ($email_blacklists as $email) {
                        $ik++;
                        if (trim($email) == '') {
                            $errors[] = $input['label'] . ' ' . sprintf($this->module->l('is invalid. Line %d email is empty'), $ik);
                            break;
                        } else if (!preg_match('/^[0-9A-Za-z\.\*\:\@]+$/', trim($email))) {
                            $errors[] = $input['label'] . ' ' . sprintf($this->module->l('is invalid. Line %d email "%s" is invalid'), $ik, $email);
                            break;
                        }
                    }
                }
            }
            if (!$errors) {
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
                    }
                }
            }
            if (!$errors)
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateIpBlacklist') . '&conf=4');
        }
        $this->context->smarty->assign(array(
            'form_config' => $this->module->renderFormIpBlackList(),
            'errors' => $errors ? $this->module->displayError($errors) : '',
        ));
        $this->_html .= $this->module->display($this->module->getLocalPath(), 'form.tpl');
        return $this->_html;
    }
}
