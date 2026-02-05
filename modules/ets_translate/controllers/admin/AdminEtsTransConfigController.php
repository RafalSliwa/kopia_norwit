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
 * @property Ets_Translate $module
 */
class AdminEtsTransConfigController extends ModuleAdminController
{
	protected $tab_active = 'setting';
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function renderOptions()
    {
        return $this->module->renderConfig(false, $this->tab_active);
    }

    public function postProcess()
    {
        parent::postProcess();
        if ((int)Tools::isSubmit('saveEtstransSettings')) {

            $languages = Language::getLanguages(false);
            $fields = $this->module->getFieldForm();
            $this->tab_active = $this->module->getActiveSettingTab();

            if((int)Tools::getValue('ETS_TRANS_AUTO_SETTING_ENABLED')){
                $fields['ETS_TRANS_LANG_SOURCE']['required'] = true;
                $fields['ETS_TRANS_LANG_TARGET']['required'] = true;
                $fields['ETS_TRANS_FILED_TRANS']['required'] = true;
            }
            $errors = $this->validateFields($fields, $languages);
            if($errors){
                $this->errors = $errors;
            }
            else{
            	$this->module->_clearCache('*');
                $langDefault = Configuration::get('PS_LANG_DEFAULT');
                foreach ($fields as $field){
                    if ($field['tab'] != Tools::getValue('ETS_TRANS_TAB_SETTING'))
                        continue;
                	if (isset($field['ignore']) && $field['ignore'])
                		continue;
                    $fieldVal = ($fieldVal = Tools::getValue($field['name'])) && (is_array($fieldVal) || Validate::isCleanHtml($fieldVal)) ? $fieldVal : '';
                    if (isset($field['lang']) && $field['lang']) {
                        $value = array();
                        foreach ($languages as $lang) {
                        	if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_TO') {
                        		if ($this->module->isEnablePhraseKey()) {
			                        $value[$lang['id_lang']] = ($langValue = Tools::getValue($field['name'] . '_' . $lang['id_lang'])) && is_array($langValue) ? $langValue : Tools::getValue($field['name'] . '_' . $langDefault);
			                        $value[$lang['id_lang']] = json_encode($value[$lang['id_lang']]);
		                        }
	                        } else
                                $value[$lang['id_lang']] = ($langValue = Tools::getValue($field['name'] . '_' . $lang['id_lang'])) && Validate::isCleanHtml($langValue) ? $langValue : Tools::getValue($field['name'] . '_' . $langDefault);
                        }
                        if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_TO') {
	                        if ($this->module->isEnablePhraseKey()) {
		                        Configuration::updateGlobalValue($field['name'], $value);
		                        Configuration::updateValue($field['name'], $value);
	                        }
                        } else {
	                        Configuration::updateGlobalValue($field['name'], $value);
	                        Configuration::updateValue($field['name'], $value);
                        }
                    }
                    else {
                    	$isTransAllLang = Tools::getIsset('ETS_TRANS_LANG_TARGET_ALL') && (int)Tools::getValue('ETS_TRANS_LANG_TARGET_ALL');

                    	if ($field['name'] == 'ETS_TRANS_LANG_TARGET' && $isTransAllLang) {
		                    $fieldVal = [];
	                    }
                        if(is_array($fieldVal)){
                        	if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_FROM') {
		                        $fieldVal = json_encode($fieldVal);
	                        } else
                                $fieldVal = implode(',', $fieldVal);
                        }
                        if (!$fieldVal && isset($field['is_suffix_rate']) && $field['is_suffix_rate']) {
	                        $fieldVal = $field['default'];
                        }
	                    if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_FROM') {
		                    if ($this->module->isEnablePhraseKey()) {
			                    Configuration::updateGlobalValue($field['name'], $fieldVal);
			                    Configuration::updateValue($field['name'], $fieldVal);
		                    }
	                    } else {
		                    Configuration::updateGlobalValue($field['name'], $fieldVal);
		                    Configuration::updateValue($field['name'], $fieldVal);
	                    }
                    }
                }
                $this->confirmations = array($this->l('Configuration saved'));

            }
        }

    }

    public function validateFields($fields, $languages) {
	    $errors = array();
	    $api = new EtsTransApi();
		$chapGpt = new EtsTransChatGPT();
	    $contextualWordsConfig = array('ETS_TRANS_CONTEXT_WORDS', 'ETS_TRANS_PAGE_APPEND_CONTEXT_WORD');
	    $apiType = $this->module->getApiType();
	    foreach ($fields as $field) {
            if ($field['tab'] != Tools::getValue('ETS_TRANS_TAB_SETTING'))
                continue;
		    $fieldVal = ($fieldVal = Tools::getValue($field['name'])) && (is_array($fieldVal) || Validate::isCleanHtml($fieldVal)) ? $fieldVal : '';
	    	if (
	    		($field['name'] == 'ETS_TRANS_DEEPL_API_KEY' && $apiType != EtsTransApi::$_DEEPL_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_BING_API_KEY' && $apiType != EtsTransApi::$_BING_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_LIBRE_API_KEY' && $apiType != EtsTransApi::$_LIBRE_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_GOOGLE_API_KEY' && $apiType != EtsTransApi::$_GOOGLE_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_LECTO_API_KEY' && $apiType != EtsTransApi::$_LECTO_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_YANDEX_API_KEY' && $apiType != EtsTransApi::$_YANDEX_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_GOOGLE' && $apiType != EtsTransApi::$_GOOGLE_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_BING' && $apiType != EtsTransApi::$_BING_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_DEEPL' && $apiType != EtsTransApi::$_DEEPL_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_LIBRE' && $apiType != EtsTransApi::$_LIBRE_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_LECTO' && $apiType != EtsTransApi::$_LECTO_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_SUFFIX_RATE_YANDEX' && $apiType != EtsTransApi::$_YANDEX_API_TYPE)
			    || ($field['name'] == 'ETS_TRANS_CHATGPT_API' && !$this->module->isEnableChatGPT())
			    || (isset($field['ignore']) && $field['ignore'])
		    ) {
				continue;
		    };
		    if(isset($field['lang']) && $field['lang'] && isset($field['required']) && $field['required'] && !in_array($field['name'], $contextualWordsConfig)){
		    	if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_TO') {
//
			    }
			    elseif(!($nameDefault = Tools::getValue($field['name'] . '_' . Configuration::get('PS_LANG_DEFAULT'))) || !Validate::isCleanHtml($nameDefault)) {
				    $errors[] = isset($field['error_message']['required']) ? $field['error_message']['required'] : $field['name'] . ' ' . $this->l('is required');
			    }
			    elseif(isset($field['validate']) && $field['validate']){
				    foreach ($languages as $lang){
					    if(($langVal = Tools::getValue($field['name'] . '_' . $lang['id_lang'])) && !Validate::{$field['validate']}($langVal)){
						    $errors[] = isset($field['error_message']['validate']) ? '"'.$lang['iso_code'].'" '.$field['error_message']['validate'] : '"'.$lang['iso_code'].'" '.$field['name'].' '.$this->l('is invalid');
					    }
				    }
			    }
		    }
		    elseif (!$fieldVal && isset($field['required']) && $field['required'] && (!isset($field['is_suffix_rate']) || !$field['is_suffix_rate'])) {
			    if(!in_array($field['name'], $contextualWordsConfig) || (in_array($field['name'], $contextualWordsConfig) && $this->module->isEnableAppendContextWord())) {
			    	if (in_array($field['name'], $contextualWordsConfig) && $this->module->isEnableAppendContextWord()) {
			    		if (isset($field['lang']) && $field['lang']) {
			    			$val = Tools::getValue($field['name'] . '_' . Configuration::get('PS_LANG_DEFAULT'));
			    			if (!$val)
							    $errors[] = $field['error_message']['required'];
					    }
				    } else
				        $errors[] = $field['error_message']['required'];
			    }

		    }
		    elseif ($fieldVal && isset($field['validate']) && $field['validate']) {
			    if (!Validate::{$field['validate']}($fieldVal)) {
				    $errors[] = $field['error_message']['validate'];
			    }
		    }
		    elseif($field['name'] == 'ETS_TRANS_MAX_WORD_APPEND_CONTEXT_WORD' && Tools::getValue('ETS_TRANS_MAX_WORD_APPEND_CONTEXT_WORD') == '0'){
			    $errors[] =$this->l('The "Append contextual words when original text to translate fewer than (or equals)" must be greater than zero');
		    }
		    if (isset($field['api_type']) && $field['api_type']) {
			    $validApi = $api->validateApiKey($fieldVal, $field['api_type']);
			    if($fieldVal && isset($field['isApiKey']) && $field['isApiKey'] && $validApi['status'] != 200){
				    $errors[] = $validApi['message'] ?: $field['error_message']['isApiKey'];
			    }
		    }
		    if ($fieldVal && isset($field['isChatGptAPI']) && $res = $chapGpt->validateApiKey($fieldVal)) {
		    	if ($res['errors']) {
				    $errors[] = is_array($res['errors']) ? $res['errors'][0] : $res['errors'];
			    }
		    }
		    if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_FROM' && $this->module->isEnablePhraseKey()) {
		    	if (is_array($fieldVal)) {
				    if(count(array_unique($fieldVal)) < count($fieldVal))
				    {
					    $errors[] = $this->l(sprintf('%s has same value!', $field['label']));
				    } else {
					    foreach ($fieldVal as $key => $value) {
						    $__index = $key + 1;
						    if (!$value) {
							    $errors[] = (isset($field['error_message']['required']) ? $field['error_message']['required'] : $field['label']) . ' (' . $__index . ')' . ' ' . $this->l('is required');
						    }
					    }
				    }
			    }
		    }
	    }
	    return $errors;
    }
}