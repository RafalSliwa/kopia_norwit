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

require_once dirname(__FILE__).'/classes/EtsTransApi.php';
require_once dirname(__FILE__).'/classes/EtsTransDefine.php';
require_once dirname(__FILE__).'/classes/EtsTransPage.php';
require_once dirname(__FILE__).'/classes/EtsTransLogRequest.php';
require_once dirname(__FILE__).'/classes/EtsTransInternational.php';
require_once dirname(__FILE__).'/classes/EtsTransConfig.php';
require_once dirname(__FILE__).'/classes/EtsTransLog.php';
require_once dirname(__FILE__).'/classes/EtsTransCache.php';
require_once dirname(__FILE__).'/classes/EtsTransCore.php';
require_once dirname(__FILE__).'/classes/EtsTransModule.php';
require_once dirname(__FILE__).'/classes/EtsTransNewSystem.php';
require_once dirname(__FILE__).'/classes/EtsTransAll.php';
require_once dirname(__FILE__).'/classes/EtsTransChatGPT.php';
require_once dirname(__FILE__).'/classes/EtsTransChatGPTMessage.php';
require_once dirname(__FILE__).'/classes/EtsTransCE.php';

class Ets_Translate extends Module
{
    public $listControllerAllowed = array();
    public $listNoLinkRewriteItems = array();
    public $is178;
    public $is801;
    public $is810;
    public $gte810;
    public $is812;
    public $is813;
    public $isGte814;
    public $isGte816;

    public $_html;
    public function __construct()
    {
        $this->name = 'ets_translate';
        $this->tab = 'front_office_features';
        $this->version = '1.4.5';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '72a1ed11a4df4c137e09b44930c85329';
        parent::__construct();
        $this->displayName = $this->l('Free Translate & AI Content Generator');
        $this->description = $this->l('Free PrestaShop translation & AI content generator module based on Google Translate API, DeepL, ChatGPT, Bing/Azure, Libre, Lecto & Yandex. Translate entire store into 110+ languages and generate content with ChatGPT (2 features in 1 module)');
$this->refs = 'https://prestahero.com/';
        $this->listControllerAllowed = array(
            'AdminProducts',
            'AdminCategories',
            'AdminCmsContent',
            'AdminManufacturers',
            'AdminSuppliers',
        );
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->is178 = version_compare('1.7.8', _PS_VERSION_, '<=') && version_compare('8.0.1', _PS_VERSION_, '>=');
        $this->is801 = version_compare('8.0.1', _PS_VERSION_, '==');
	    $this->is810 = version_compare(_PS_VERSION_, '8.1.0', '>=') && version_compare(_PS_VERSION_, '8.1.1', '<');
	    $this->gte810 = version_compare(_PS_VERSION_, '8.1.0', '>=');
	    $this->is812 = version_compare(_PS_VERSION_, '8.1.2', '>=') && version_compare(_PS_VERSION_, '8.1.3', '<');
	    $this->is813 = version_compare(_PS_VERSION_, '8.1.3', '>=') && version_compare(_PS_VERSION_, '8.1.4', '<');
	    $this->isGte814 = version_compare(_PS_VERSION_, '8.1.4', '>=');
	    $this->isGte816 = version_compare(_PS_VERSION_, '8.1.6', '>=');
        $this->listNoLinkRewriteItems = array('manufacturer','supplier', 'attribute_group', 'blockreassurance', 'ps_customtext', 'ps_linklist', 'ps_mainmenu', 'ps_imageslider', 'ets_extraproducttabs', 'pc','megamenu', 'inter');
    }

    public function install()
    {
        $etsDef = EtsTransDefine::getInstance();
        $res = parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('etsDisplayCombinationProduct')
            && $this->setDefaultConfig()
            && $etsDef->installDb()
            && $this->installTabs();
        if ($res) {
        	$this->installDefaultTemplates();
        }
        return $res;
    }

    public function uninstall()
    {
        $etsDef = EtsTransDefine::getInstance();
        return parent::uninstall()
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('etsDisplayCombinationProduct')
            && $this->deleteKeyConfig()
            && $etsDef->uninstallDb()
            && $this->uninstallTabs();
    }

    protected function setDefaultConfig()
    {
        $languages = Language::getLanguages(false);
        foreach ($this->getFieldForm() as $field) {
            if (isset($field['default']) && $field['default']) {
                if (isset($field['lang']) && $field['lang']) {
                    $value = array();
                    foreach ($languages as $lang) {
                        $value[$lang['id_lang']] = $field['default'];
                    }
                    Configuration::updateGlobalValue($field['name'], $value);
                } else {
                    Configuration::updateGlobalValue($field['name'], $field['default']);
                }
            }
        }

        return true;
    }

    protected function deleteKeyConfig()
    {
        foreach ($this->getFieldForm() as $field) {
            Configuration::deleteByName($field['name']);
        }
        EtsTransConfig::resetKeyConfig();
        return true;
    }

    public function installTabs()
    {
        $parentTabId = Tab::getIdFromClassName('AdminInternational');
        $languages = Language::getLanguages(false);

        if($parentTabId){
            $tab = new Tab();
            $tab->id_parent = $parentTabId;
            $tab->module = $this->name;
            $tab->class_name = 'AdminEtsTransConfig';
            foreach ($languages as $lang){
                $tab->name[$lang['id_lang']] = EtsTransDefine::getTextLang('Free Translate', $lang) ?: $this->l('Free Translate');
            }
            $tab->icon = 'translate';
            $tab->save();
        }

        return true;

    }
    public function uninstallTabs()
    {
        $tabConfigId = Tab::getIdFromClassName('AdminEtsTransConfig');
        if($tabConfigId){
            $tabConfig = new Tab((int)$tabConfigId);
            $tabConfig->delete();
        }

        return true;
    }

    public function getContent()
    {
        $this->actionAjax();
        if(($viewTransLog = Tools::getValue('viewTranslateLog')) && Validate::isCleanHtml($viewTransLog)){
            return $this->renderListLog();
        }
        $inConfigTransWD = Tools::getIsset('configTransWebsite');
        if(!$inConfigTransWD){
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsTransConfig'));
        }

        return $this->renderConfig($inConfigTransWD);
    }

    public function renderConfig($inConfigTransWD = false, $tab_active = 'setting')
    {
        $this->saveFormConfig();
        $etsDef = EtsTransDefine::getInstance();
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $inConfigTransWD ? $this->l('1-Click translate') : $this->l('Global settings'),
	                'tabs' => $inConfigTransWD ? [] : $etsDef->getTabsSetting($tab_active)
                ),
                'input' => $inConfigTransWD ? $etsDef->configTransAllWensite() : $this->getFieldForm(true),
                'submit' => array(
                    'name' => $inConfigTransWD ? 'saveConfigTransAllWD' : 'saveEtstransSettings',
                    'title' => $inConfigTransWD ? $this->l('Translate') : $this->l('Save'),
                    'class' => $inConfigTransWD ? 'btn btn-default pull-right saveConfigAll js-ets-trans-btn-trans-all-website' : 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                ),
            ),
        );
        if($inConfigTransWD){
            $fieldsForm['form']['buttons'] = array(
                array(
                    'title' => $this->l('Back'),
                    'class' => 'ets-trans-btn-back',
                    'icon' => 'process-icon-arrow-left',
                    'href' => $this->context->link->getAdminLink('AdminEtsTransConfig')
                )
            );
        }

        $fieldsValue = array();
        if($inConfigTransWD)
        {
            $configs = $etsDef->configTransAllWensite();
        }
        else{
            $configs = $this->getFieldForm();
        }
        foreach ($configs as $field) {
            $isTab = Tools::getIsset('ETS_TRANS_TAB_SETTING') && $field['tab'] == Tools::getValue('ETS_TRANS_TAB_SETTING');
	        if (isset($field['ignore']) && $field['ignore'])
		        continue;
            if(isset($field['lang']) && $field['lang']){
                $fieldsValue[$field['name']] = array();
                foreach (Language::getLanguages(false) as $lang){
                	if ($field['name'] == 'ETS_TRANS_KEY_PHRASE_TO') {
		                $fieldsValue[$field['name']][$lang['id_lang']] = Tools::getIsset($field['name'].'_'.$lang['id_lang']) && $isTab ? (($configName = Tools::getValue($field['name'].'_'.$lang['id_lang'])) && is_array($configName) ? $configName : '') : json_decode(Configuration::get($field['name'], $lang['id_lang']), true);
	                }else
                        $fieldsValue[$field['name']][$lang['id_lang']] = Tools::getIsset($field['name'].'_'.$lang['id_lang']) && $isTab ? (($configName = Tools::getValue($field['name'].'_'.$lang['id_lang'])) && Validate::isCleanHtml($configName) ? $configName : '') : Configuration::get($field['name'], $lang['id_lang']);
                }
            }
            else{
                $fieldsValue[$field['name']] = Tools::getIsset($field['name']) && $isTab ? (($configName = Tools::getValue($field['name'])) && (is_array($configName) || Validate::isCleanHtml($configName)) ? $configName : '') : ($field['name'] == 'ETS_TRANS_KEY_PHRASE_FROM' ? json_decode(Configuration::get($field['name'])) : Configuration::get($field['name']));
            }
            if(isset($field['is_array']) && $field['is_array']  && !is_array($fieldsValue[$field['name']])){
                $fieldsValue[$field['name']] = explode(',', $fieldsValue[$field['name']]);
            }
            if ($field['name'] == 'ETS_TRANS_SELECT_API') {
            	if (Tools::getValue('ETS_TRANS_SELECT_API') && $isTab) {
		            $fieldsValue[$field['name']] = Tools::getValue('ETS_TRANS_SELECT_API');
	            } else if (Configuration::get($field['name'])) {
	                $fieldsValue[$field['name']] = Configuration::get($field['name']);
	            } else {
		            $fieldsValue[$field['name']] = $field['default'];
	            }
            }
            if ($field['type'] == 'switch' && !$fieldsValue[$field['name']])
                $fieldsValue[$field['name']] = 0;
        }
        return $this->renderForm($fieldsForm, $fieldsValue);
    }

    // ---cache with id page log
    protected function renderListLog()
    {
        $this->smarty->assign(array(
            'logData' => EtsTransLog::getLogs(50, (int)Tools::getValue('page', 1)),
            'linkPaginate' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&viewTranslateLog=1',
            'linkConfig' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
        ));
        return $this->display(__FILE__, 'list_translate_log.tpl');
    }

    protected function renderForm($fields_form, $fields_value)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEts_testModule';
        $helper->currentIndex = Tools::getIsset('configTransWebsite') ? $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name.'&configTransWebsite=1' : '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $etsDef = EtsTransDefine::getInstance();
        $helper->tpl_vars = array(
            'fields_value' => $fields_value,
            'ets_translate' => $this,
            'form_id' => Tools::getIsset('configTransWebsite') ? 'ets_translate_wd' : 'ets_translate_settings',
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'langTarget' => Language::getLanguages(true),
            'langWithFlag' => $this->getLangWithFlagImage(true),
            'listApi' => $this->getListApi(),
            'listRegion' => EtsTransDefine::getListRegion(),
            'treeWebPageOption' => $etsDef->treeWebPageSelection(),
            'isMultipleLanguage' => ($idsLang = Language::getIDs(true)) && count($idsLang),
            'isEnableModule' => $this->isEnabledEtsTranslateModule(),
            'linkToConfigLang' => $this->getLocalizationLink(),
            'pageAppendContextWords' => $this->getPageAppendContextWords(),
            'linkToConfigWd' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&configTransWebsite=1',
            'ETS_TRANS_LANG_TARGET' => $this->getListIdLangTarget(),
            'ETS_TRANS_WD_CONFIG' => (int)Tools::getValue('saveEtstransSettings') ? (($transWdConfig = Tools::getValue('ETS_TRANS_WD_CONFIG', array())) && is_array($transWdConfig) ? $transWdConfig : array()) : explode(',', Configuration::get('ETS_TRANS_WD_CONFIG')),
            'ETS_TRANS_PAGE_APPEND_CONTEXT_WORD' => Tools::getValue('saveEtstransSettings') ? (($transContextWord = Tools::getValue('ETS_TRANS_PAGE_APPEND_CONTEXT_WORD', array())) && is_array($transContextWord) ? $transContextWord : array()) : explode(',', Configuration::get('ETS_TRANS_PAGE_APPEND_CONTEXT_WORD')),
	        'ets_trans_box_add_template_chatgpt' => $this->renderFormTemplateChatGPT()
        );
        return $helper->generateForm(array($fields_form));
    }

    public function renderFormTemplateChatGPT() {
    	$html = '';
    	$etsTransChatGpt = new EtsTransChatGPT();
    	if (Tools::isSubmit('addnewTempChatGpt') || Tools::isSubmit('editTempChatGpt')) {
		    if(Tools::isSubmit('ajax'))
		    {
			    die(
			    json_encode(
				    array(
					    'form' =>$etsTransChatGpt->renderFormTemplateChatGPT()
				    )
			    )
			    );
		    }
		    else
			    $this->_html .= $etsTransChatGpt->renderFormTemplateChatGPT();

	    } else
		    $html = $this->displayText($etsTransChatGpt->renderFormTemplateChatGPT(),'div','ets-trans-box-form-chatgpt js-ets-trans-box-form-chatgpt');

    	return $html;
    }

    public function getPageAppendContextWords()
    {
    	return EtsTransDefine::getInstance()->getPageAppendContextWords();
    }

    public function getListIdLangTarget($explode = true) {
	    if (Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab()) {
	    	$langSource = Tools::getValue('ETS_TRANS_LANG_SOURCE');
		    $isTransAllLang = Tools::getIsset('ETS_TRANS_LANG_TARGET_ALL') && (int)Tools::getValue('ETS_TRANS_LANG_TARGET_ALL');
		    if ($isTransAllLang) {
			    $transLangTarget = [];
		    } else {
			    $transLangTarget = is_array(Tools::getValue('ETS_TRANS_LANG_TARGET')) ? Tools::getValue('ETS_TRANS_LANG_TARGET') : [];
		    }
	    } else {
		    $langSource = Configuration::get('ETS_TRANS_LANG_SOURCE');
		    $transLangTarget = Configuration::get('ETS_TRANS_LANG_TARGET');
	    }
	    if (!$transLangTarget || (is_array($transLangTarget) && !count($transLangTarget))) {
		    $languages = Language::getLanguages(true, false, true);
		    $transLangTarget = [];
		    foreach ($languages as $lang) {
			    if ($lang != $langSource)
			    	$transLangTarget[] = $lang;
		    }
	    }
	    if ($explode && !is_array($transLangTarget)) {
		    $transLangTarget = explode(',', $transLangTarget);
	    }
	    if (!$explode && is_array($transLangTarget)) {
		    $transLangTarget = implode(',', $transLangTarget);
	    }
    	return $transLangTarget ?: [];
    }

    public function getIdLangSource() {
	    if (Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab()) {
	    	return (int)Tools::getValue('ETS_TRANS_LANG_SOURCE');
	    }
	    return (int)Configuration::get('ETS_TRANS_LANG_SOURCE');
    }

    public function getLangDefault($onlyId = true) {
    	return EtsTransDefine::getLangDefault($onlyId);
    }

    public function getLocalizationLink()
    {
	    try{
            $res = $this->context->link->getAdminLink('AdminLocalization', true, array('route' => 'admin_localization_index'), array());
        }
        catch(Exception $ex){
            $res = $this->context->link->getAdminLink('AdminLocalization', true);
        }
        return $res;
    }

    public function getSuffixRateApi($apiType = '') {
    	$apiType = $apiType ?: $this->getApiType();
    	switch ($apiType) {
		    case EtsTransApi::$_DEEPL_API_TYPE:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateDeepL = Tools::getValue('ETS_TRANS_SUFFIX_RATE_DEEPL')) && Validate::isCleanHtml($rateDeepL) ? $rateDeepL : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_DEEPL') ?: 'USD');
		    case EtsTransApi::$_BING_API_TYPE:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateBing = Tools::getValue('ETS_TRANS_SUFFIX_RATE_BING')) && Validate::isCleanHtml($rateBing) ? $rateBing : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_BING') ?: 'USD');
		    case EtsTransApi::$_LIBRE_API_TYPE:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateLibre = Tools::getValue('ETS_TRANS_SUFFIX_RATE_LIBRE')) && Validate::isCleanHtml($rateLibre) ? $rateLibre : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_LIBRE') ?: 'USD');
		    case EtsTransApi::$_LECTO_API_TYPE:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateLibre = Tools::getValue('ETS_TRANS_SUFFIX_RATE_LECTO')) && Validate::isCleanHtml($rateLibre) ? $rateLibre : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_LECTO') ?: 'USD');
		    case EtsTransApi::$_YANDEX_API_TYPE:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateYandex = Tools::getValue('ETS_TRANS_SUFFIX_RATE_YANDEX')) && Validate::isCleanHtml($rateYandex) ? $rateYandex : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_YANDEX') ?: 'USD');
		    case EtsTransApi::$_GOOGLE_API_TYPE:
		    default:
		    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_SUFFIX_RATE_GOOGLE')) && Validate::isCleanHtml($rateGoogle) ? $rateGoogle : '') : (Configuration::get('ETS_TRANS_SUFFIX_RATE_GOOGLE') ?: 'USD');
	    }
    }

    public function getRateApi($apiType = '') {
	    $apiType = $apiType ?: $this->getApiType();
	    switch ($apiType) {
		    case EtsTransApi::$_DEEPL_API_TYPE:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_DEEPL')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_DEEPL');
		    case EtsTransApi::$_BING_API_TYPE:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_BING')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_BING');
		    case EtsTransApi::$_LIBRE_API_TYPE:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_LIBRE')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_LIBRE');
		    case EtsTransApi::$_LECTO_API_TYPE:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_LECTO')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_LECTO');
		    case EtsTransApi::$_YANDEX_API_TYPE:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_YANDEX')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_YANDEX');
		    case EtsTransApi::$_GOOGLE_API_TYPE:
		    default:
			    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (($rateGoogle = Tools::getValue('ETS_TRANS_RATE_GOOGLE')) ? $rateGoogle : '') : Configuration::get('ETS_TRANS_RATE_GOOGLE');
	    }
    }

    public function getLinkDescription($apiType = '') {
    	return EtsTransDefine::getInstance()->getLinkDescription($apiType);
    }

    public function getFieldForm($isInForm = false)
    {
	    $transOption = array();
	    $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD = $this->isEnableAppendContextWord();
        if ($isInForm) {
            $this->smarty->assign(array(
                'apiType' => $this->getApiType(),
            ));
            foreach ($this->renderTransOptions() as $key=>$option){
                $transOption[] = array(
                    'label' => $option['title'],
                    'value' => $key,
                    'id' => 'ETS_TRANS_FILED_TRANS_'.$key
                );
            }
        }
        $defaultLangTarget = Language::getIDs(true);
        $defaultLangSource = $this->getLangDefault();
        if (($key = array_search($defaultLangSource, $defaultLangTarget)) !== false) {
            unset($defaultLangTarget[$key]);
        }
        $enableAutoSetting = $this->getEnableAutoSetting();
        $apiType = $this->getApiType();
        $config = EtsTransDefine::getInstance()->getConfigs($isInForm, $apiType, $defaultLangSource, $defaultLangTarget, $enableAutoSetting, $transOption, $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD);
        if (!Module::isInstalled('ets_livechat') && !Module::isInstalled('ets_helpdesk')){
            unset($config['ETS_TRANS_ENABLE_TRANSLATE_TICKET']);
        }
        return $config;
    }

    public function saveFormConfig()
    {
        if(Tools::isSubmit('saveConfigTransAllWD')){
            $wdConfig = Tools::getValue('ETS_TRANS_WD_CONFIG');
            $wdValue =  is_array($wdConfig) ? implode(',', $wdConfig) : $wdConfig;
            Configuration::updateValue('ETS_TRANS_WD_CONFIG', $wdValue);
        }
    }

    public function addJqueryUi($component, $theme = 'base') {

	    if (!is_array($component)) {
		    $component = [$component];
	    }

	    foreach ($component as $ui) {
		    $ui_path = $this->getJqueryUIPath($ui, $theme);
		    $this->context->controller->addCSS($ui_path['css'], 'all', false);
		    $this->context->controller->addJS($ui_path['js'], false);
	    }
    }

	public function getJqueryUIPath($component, $theme)
	{
		$uiPath = ['js' => [], 'css' => []];
		$folder = _PS_JS_DIR_ . 'jquery/ui/';
		$file = 'jquery.' . $component . '.min.js';
		$urlData = parse_url($folder . $file);
		$fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
		$fileUriHostMode = _PS_CORE_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
		$uiTmp = [];
		if (isset(Media::$jquery_ui_dependencies[$component]) && Media::$jquery_ui_dependencies[$component]['theme']) {
			$themeCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.ui.theme.css');
			$compCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.' . $component . '.css');
			if (!empty($themeCss) || $themeCss) {
				$uiPath['css'] = array_merge($uiPath['css'], $themeCss);
			}
			if (!empty($compCss) || $compCss) {
				$uiPath['css'] = array_merge($uiPath['css'], $compCss);
			}
		}
		if (@filemtime($fileUri) || (defined('_PS_HOST_MODE_') && @filemtime($fileUriHostMode))) {
//			if (!empty($uiTmp)) {
//				foreach ($uiTmp as $ui) {
//					if (!empty($ui['js'])) {
//						$uiPath['js'][] = $ui['js'];
//					}
//
//					if (!empty($ui['css'])) {
//						$uiPath['css'][] = $ui['css'];
//					}
//				}
//				$uiPath['js'][] = Media::getJSPath($folder . $file);
//			} else {
//				$uiPath['js'] = Media::getJSPath($folder . $file);
//			}
            $uiPath['js'] = Media::getJSPath($folder . $file);
		}

		return $uiPath;
	}

    public function getAssignVariables($hook = 'displayBackOfficeHeader')
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isCleanHtml($controller) ? $controller : '';
        $request = $this->getRequestContainer();
        $arrayAdminPath = explode('/', str_replace('\\', '/', _PS_ADMIN_DIR_));
        $assigns = array(
            'controller' => $controller,
            'transJs' => $this->transJs(),
            'languages' => $this->context->controller->getLanguages(),
            'current_language' => $this->context->language,
            'linkAjaxModule' =>$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
            'linkAjaxBo' => _MODULE_DIR_ . $this->name . '/ajax.bo.php?token=' . Tools::getAdminTokenLite('AdminModules'),
            'langSourceDefault' => Language::getIdByIso('en'),
            'langTargetInterTrans' => ($langCode = Tools::getValue('lang')) && Validate::isCleanHtml($langCode) ? Language::getIdByIso($langCode) : '',
            'isAutoConfigEnabled' => (int)Configuration::get('ETS_TRANS_AUTO_SETTING_ENABLED'),
            'defaultTransConfig' => $this->getDefaultTransConfig(),
            'linkPsAdmin' => $arrayAdminPath[count($arrayAdminPath)-1],
            'enableAnalysis' => (int)Configuration::get('ETS_TRANS_ENABLE_ANALYSIS'),
            'rateVal' => $this->getRateApi(),
            'rateSuffix' => $this->getSuffixRateApi(),
            'enableAutoGenerateLinkRewrite' => (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE'),
            'ETS_TRANS_ENABLE_TRANS_FIELD' => (int)Configuration::get('ETS_TRANS_ENABLE_TRANS_FIELD'),
            'ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME'),
            'ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME'),
            'ETS_TRANS_ENABLE_TRANSLATE_TICKET' => (int)Configuration::get('ETS_TRANS_ENABLE_TRANSLATE_TICKET'),
            'isNewTemplate' => $request ? 1 : 0,
            'is1780' => $this->is178,
            'is801' => $this->is801,
            'is810' => $this->is810,
            'gte810' => $this->gte810,
            'is812' => $this->is812,
            'is813' => $this->is813,
            'isGte814' => $this->isGte814,
            'use_product_page_v2' => EtsTransDefine::getFeatureFlag('product_page_v2', 'state'),
            'ETS_TRANS_ENABLE_CHATGPT' => $this->isEnableChatGPT(),
        );
        if ($hook == 'etsDisplayCombinationProduct') {
            $assigns['tool_all_value'] = json_encode(Tools::getAllValues());
            $assigns['id_combination'] = $this->getIdProductCombinationOnUrl();
            $assigns['linkScriptBo'] = _MODULE_DIR_ . $this->name . '/views/js/admin_product_combination.js';
        }
        return $assigns;
    }

    public function hookEtsDisplayCombinationProduct()
    {
        $this->smarty->assign($this->getAssignVariables('etsDisplayCombinationProduct'));
        return $this->display(__FILE__, 'ets_admin_combination_product.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isCleanHtml($controller) ? $controller : '';
        $request = $this->getRequestContainer();
        $this->smarty->assign($this->getAssignVariables());
        if ($this->isEnabledEtsTranslateModule() || (!$this->isEnabledEtsTranslateModule() && $this->currentPageIsModuleConfigPage())) {
	        $this->context->controller->addCSS($this->_path . 'views/css/admin_pages.css');
        }
        if ($controller == 'AdminEtsTransConfig' || $controller == 'AdminProducts') {
	        $this->context->controller->addCSS($this->_path . 'views/css/admin_chatgpt.css');
	        $this->smarty->assign(array(
		        'linkJsChatGPT' => $this->_path . 'views/js/trans_chatgpt.js',
	        ));
        }
        if ($controller === 'AdminProducts') {
	        $this->addJqueryUi(['ui.core','ui.mouse' ,'ui.draggable', 'ui.resizable']);
        }
        if($this->isModuleInstalled('ets_megamenu') && $this->isEnabledEtsTranslateModule()){
            $this->context->controller->addCSS($this->_path . 'views/css/admin_megamenu.css');
            $this->smarty->assign(array(
                'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
                'jsTransMegamenu' => $this->_path . 'views/js/trans_megamenu.js',
            ));
            return $this->display(__FILE__, 'admin_head.tpl');
        }

        if($this->isModuleInstalled('ybc_blog') && $this->isEnabledEtsTranslateModule()){
            $this->context->controller->addCSS($this->_path . 'views/css/admin_blog.css');
            $this->smarty->assign(array(
                'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
                'jsTransMegamenu' => $this->_path . 'views/js/trans_blog.js',
            ));
	        $this->smarty->assign([
		        'blogType' => Tools::getValue('controller') == 'AdminYbcBlogCategory' ? 'category' : (Tools::getValue('controller') == 'AdminYbcBlogPost' ? 'post' : ''),
		        'is_list_post' => !Tools::getIsset('editybc_post') && !Tools::getIsset('addNew') && Tools::getValue('controller') == 'AdminYbcBlogPost',
		        'is_list_category' => !Tools::getIsset('editybc_post') && !Tools::getIsset('addNew') && Tools::getValue('controller') == 'AdminYbcBlogCategory',
	        ]);
            return $this->display(__FILE__, 'admin_head.tpl');
        }
        if($this->isModuleInstalled('ets_reviews') && $this->isEnabledEtsTranslateModule()){
            $this->context->controller->addCSS($this->_path . 'views/css/admin_productcomments.css');
            $this->smarty->assign(array(
                'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
                'jsTransMegamenu' => $this->_path . 'views/js/trans_productcomments.js',
                'autoDetectLanguage' => $this->isAutoDetectLanguage()
            ));
            return $this->display(__FILE__, 'admin_head.tpl');
        }

        if (Module::isInstalled('ets_livechat') && Module::isEnabled('ets_livechat') && $controller == 'AdminLiveChatTickets' && $this->isEnabledEtsTranslateModule()){
            $lcLangSource = Language::getLanguage($this->context->employee->id_lang);
            $idTicket = (int)Tools::getValue('id_ticket');
            $lcLangTarget = null;
            if($idTicket && ($idLangTicket = EtsTransModule::getLcTicketCustomerLanguage($idTicket))) {
                $lcLangTarget = Language::getLanguage($idLangTicket);
            }
            $this->smarty->assign(array(
                'linkJsLivechat' => $this->_path . 'views/js/trans_livechat.js',
                'allLangWithFlag' => $this->getLangWithFlagImage(false),
                'lcLangSource' => $lcLangSource ? $lcLangSource['iso_code']: null,
                'lcLangTarget' => $lcLangTarget ? $lcLangTarget['iso_code']: null,
            ));
        }
        if (Module::isInstalled('ets_helpdesk') && Module::isEnabled('ets_helpdesk') && $controller == 'AdminEtsHDTickets' && $this->isEnabledEtsTranslateModule()){
            $hdLangSource = Language::getLanguage($this->context->employee->id_lang);
            $idTicket = (int)Tools::getValue('id_ets_hd_ticket');
            $hdLangTarget = null;
            if($idTicket && ($idLangTicket = EtsTransModule::getHdTicketCustomerLanguage($idTicket))) {
                $hdLangTarget = Language::getLanguage($idLangTicket);
            }
            $this->smarty->assign(array(
                'linkJsHelpdesk' => $this->_path . 'views/js/trans_helpdesk.js',
                'allLangWithFlag' => $this->getLangWithFlagImage(false),
                'hdLangSource' => $hdLangSource ? $hdLangSource['iso_code']: null,
                'hdLangTarget' => $hdLangTarget ? $hdLangTarget['iso_code']: null,
            ));
        }
        if (!in_array($controller, $this->listControllerAllowed)) {
            if($this->currentPageIsModuleConfigPage()){
                $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
                $this->smarty->assign(array(
                    'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
                    'linkJsBo' => $this->_path.'views/js/admin.js',
                ));
                return $this->display(__FILE__, 'admin_head.tpl');
            }
            elseif(($request && $request->get('_route') == 'admin_international_translation_overview')
                || (!$request && $controller == 'AdminTranslations' && ((int)Tools::getIsset('locale') || (($langCode = Tools::getValue('lang')) && Validate::isCleanHtml($langCode))))
                    || (!$request && isset($_SERVER['PHP_SELF']) && Tools::strpos($_SERVER['PHP_SELF'],'/international/translations'))
                ){
            	if ($this->isEnabledEtsTranslateModule()) {
		            $this->context->controller->addCSS($this->_path . 'views/css/admin_inter_trans.css');
		            $this->smarty->assign(array(
			            'linkJsInterTrans' => $this->_path.'views/js/admin_inter_trans.js',
			            'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
			            'linkJsSimulate' => $this->_path . 'views/js/jquery.simulate.js',
		            ));
		            return $this->display(__FILE__, 'admin_head.tpl');
	            }
            }
            elseif(($request || (!$request && !Tools::getIsset('locale'))) && $controller == 'AdminTranslations'){
            	if ($this->isEnabledEtsTranslateModule()) {
		            $this->smarty->assign(array(
			            'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
			            'linkJsBo' => $this->_path.'views/js/admin.js',
		            ));
		            $this->context->controller->addCSS($this->_path . 'views/css/admin_inter_trans.css');
		            return $this->display(__FILE__, 'admin_head.tpl');
	            }
            }
        }
		if ($this->isEnabledEtsTranslateModule() || (!$this->isEnabledEtsTranslateModule() && $this->currentPageIsModuleConfigPage())) {
			$this->smarty->assign(array(
				'linkJsConfig' => $this->_path . 'views/js/admin_defines.js',
				'linkJsPages' => $this->_path . 'views/js/admin_pages.js',
				'linkJsCommon' => $this->_path . 'views/js/admin_common_trans.js',
			));
		}
        $this->getAssignment();
        return $this->display(__FILE__, 'admin_head.tpl');
    }
    public function currentPageIsModuleConfigPage() {
    	$controller = Tools::getIsset('controller') ? Tools::getValue('controller') : '';
    	if ($controller == 'AdminEtsTransConfig' || ($controller == 'AdminModules' && Tools::getValue('configure') === $this->name))
    		return true;
    	return false;
    }
	public function disable($force_all = false)
	{
		$res = parent::disable($force_all);
		if($res && !$force_all && EtsTransDefine::checkEnableOtherShop($this->id))
		{
            if(property_exists('Tab','enabled') && method_exists($this, 'get') && $dispatcher = $this->get('event_dispatcher')){
                /** @var \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
                $dispatcher->addListener(\PrestaShopBundle\Event\ModuleManagementEvent::DISABLE, function (\PrestaShopBundle\Event\ModuleManagementEvent $event) {
                    EtsTransDefine::activeTab($this->name);
                });
            }
			if($this->getOverrides() != null)
			{
				try {
					$this->installOverrides();
				}
				catch (Exception $e)
				{
					if($e)
					{
						//
					}
				}
			}
		}
		return $res;
	}
    private function safeMkDir($path, $permission = 0755)
    {
        if (!@mkdir($concurrentDirectory = $path, $permission) && !is_dir($concurrentDirectory)) {
            throw new \PrestaShopException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
    }
    private function checkOverrideDir()
    {
        if (defined('_PS_OVERRIDE_DIR_')) {
            $psOverride = @realpath(_PS_OVERRIDE_DIR_) . DIRECTORY_SEPARATOR;
            if (!is_dir($psOverride)) {
                $this->safeMkDir($psOverride);
            }
            $base = str_replace('/', DIRECTORY_SEPARATOR, $this->getLocalPath() . 'override');
            $iterator = new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS);
            /** @var RecursiveIteratorIterator|\SplFileInfo[] $iterator */
            $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            $iterator->setMaxDepth(4);
            foreach ($iterator as $k => $item) {
                if (!$item->isDir()) {
                    continue;
                }
                $path = str_replace($base . DIRECTORY_SEPARATOR, '', $item->getPathname());
                if (!@file_exists($psOverride . $path)) {
                    $this->safeMkDir($psOverride . $path);
                    @touch($psOverride . $path . DIRECTORY_SEPARATOR . '_do_not_remove');
                }
            }
            if (!file_exists($psOverride . 'index.php')) {
                Tools::copy($this->getLocalPath() . 'index.php', $psOverride . 'index.php');
            }
        }
    }
	public function enable($force_all = false)
	{
		if(!$force_all && EtsTransDefine::checkEnableOtherShop($this->id) && $this->getOverrides() != null)
		{
			try {
				$this->uninstallOverrides();
			}
			catch (Exception $e)
			{
				if($e)
				{
					//
				}
			}
		}
		$this->checkOverrideDir();
		return parent::enable($force_all);
	}

	public function isEnabledEtsTranslateModule() {
    	return Module::isInstalled($this->name) && Module::isEnabled($this->name);
    }

    public function getAssignment()
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isCleanHtml($controller) ? $controller : '';
        $request = $this->getRequestContainer();
        switch ($controller) {
            case 'AdminProducts':
                $pageType = 'product';
                $pageId = $request && $request->get('id') ? $request->get('id') : ($this->getIdProductOnUrl() ?: (int)Tools::getValue('id_product'));
                $isDetailPage = ($request && $request->get('id')) || ((!$request || !$request->get('id')) && ($this->getIdProductOnUrl() || (int)Tools::getValue('id_product')));
                break;
            case 'AdminCategories':
                $pageType = 'category';
                $pageId = $request ? $request->get('categoryId') : (int)Tools::getValue('id_category');
                $isDetailPage = $request ? in_array($request->get('_route'), array('admin_categories_create', 'admin_categories_edit')) : (Tools::getIsset('updatecategory') || Tools::getIsset('addcategory'));
                break;
            case 'AdminCmsContent':

                if ($request) {
                    $cmsRoutes = array('admin_cms_pages_edit', 'admin_cms_pages_create', 'admin_cms_pages_index');
                    if(in_array($request->get('_route'), $cmsRoutes))
                    {
                        $pageType = 'cms';
                        $pageId = $request ? $request->get('cmsPageId') : (int)Tools::getValue('id_cms');
                        $isDetailPage = $request && in_array($request->get('_route'), array('admin_cms_pages_edit', 'admin_cms_pages_create'));
                    }
                    else {
                        $pageType = 'cms_category';
                        $pageId = $request ? $request->get('cmsCategoryId') : (int)Tools::getValue('id_category');
                        $isDetailPage = $request && in_array($request->get('_route'), array('admin_cms_pages_category_edit', 'admin_cms_pages_category_create'));
                    }
                }
                else{
                    $pageType = 'cms';
                    $isDetailPage = false;
                    $pageId = 0;
                    if(Tools::getIsset('updatecms') || Tools::getIsset('addcms')){
                        $pageType = 'cms';
                        $pageId = (int)Tools::getValue('id_cms');
                        $isDetailPage = true;
                    }
                    else if(Tools::getIsset('updatecms_category') || Tools::getIsset('addcms_category'))
                    {
                        $pageType = 'cms_category';
                        $pageId = (int)Tools::getValue('id_cms_category');
                        $isDetailPage = true;
                    }
                }
                break;
            case 'AdminManufacturers':
                $pageType = 'manufacturer';
                $pageId = $request ? $request->get('manufacturerId') : (int)Tools::getValue('id_manufacturer');
                $isDetailPage = $request ? in_array($request->get('_route'), array('admin_manufacturers_create', 'admin_manufacturers_edit')) : (Tools::getIsset('addmanufacturer') || Tools::getIsset('updatemanufacturer'));
                break;
            case 'AdminSuppliers':
                $pageType = 'supplier';
                $pageId = $request ? $request->get('supplierId') : (int)Tools::getValue('id_supplier');
                $isDetailPage = $request ? in_array($request->get('_route'), array('admin_suppliers_create', 'admin_suppliers_edit')) : (Tools::getIsset('addsupplier') || Tools::getIsset('updatesupplier'));
                break;
            case 'AdminAttributesGroups':
                $pageType = 'attribute_group';
                $pageId = (int)Tools::getValue('id_attribute_group');
                $isDetailPage = Tools::getIsset('updateattribute_group') || Tools::getIsset('addattribute_group');
                if(Tools::getIsset('viewattribute_group') || Tools::getIsset('updateattribute') || Tools::getIsset('addattribute')){
                    $pageType = 'attribute';
                    $pageId = (int)Tools::getValue('id_attribute');
                    $isDetailPage = Tools::getIsset('updateattribute') || Tools::getIsset('addattribute');
                }
                break;
            case 'AdminFeatures':
                $pageType = 'feature';
                $pageId = (int)Tools::getValue('id_feature');
                $isDetailPage = Tools::getIsset('updatefeature') || Tools::getIsset('addfeature');
                if(Tools::getIsset('viewfeature') || Tools::getIsset('updatefeature_value') || Tools::getIsset('addfeature_value')){
                    $pageType = 'feature_value';
                    $pageId = (int)Tools::getValue('id_feature_value');
                    $isDetailPage = Tools::getIsset('updatefeature_value') || Tools::getIsset('addfeature_value');
                }
                break;
            case 'AdminModules':
                if(Tools::getValue('configure') === 'blockreassurance'){
                    $pageType = 'blockreassurance';
                    $pageId = (int)Tools::getValue('id_reassurance');
                    $isDetailPage = Tools::getIsset('updateblockreassurance') || Tools::getIsset('addblockreassurance');
                }
                elseif(Tools::getValue('configure') === 'ps_mainmenu'){
                    $pageType = 'ps_mainmenu';
                    $pageId = (int)Tools::getValue('id_linksmenutop');
                    $isDetailPage = 1;
                }
                elseif(Tools::getValue('configure') === 'ps_customtext'){
                    $pageType = 'ps_customtext';
                    $pageId = 1;
                    $isDetailPage = 1;
                }
                elseif(Tools::getValue('configure') === 'ps_imageslider'){
                    $pageType = 'ps_imageslider';
                    $pageId = (int)Tools::getValue('id_slide');
                    $isDetailPage = (int)Tools::getIsset('addSlide') || (int)Tools::getValue('id_slide');
                }
                elseif(Tools::getValue('configure') === 'ets_extraproducttabs'){
                    $pageType = 'ets_extraproducttabs';
                    $pageId = 0;
                    $isDetailPage = 1;
                }
                else{
                    $pageType = null;
                    $pageId = null;
                    $isDetailPage = false;
                }
                break;
            case 'AdminLinkWidget':
                $pageType = 'ps_linklist';
                $pageId = $request ? $request->get('linkBlockId') : '';
                $isDetailPage = $request && in_array($request->get('_route'), array('admin_link_block_edit', 'admin_link_block_create'));
                break;
            default:
                $pageType = null;
                $pageId = null;
                $isDetailPage = false;
                break;
        }
        if (!$pageType) {
            return;
        }
        $this->smarty->assign(array(
            'pageType' => $pageType,
            'pageId' => $pageId,
            'isDetailPage' => $isDetailPage
        ));
    }

    public function getIdProductOnUrl()
    {
    	$uri = '';
    	$find = $this->is810 ? '/products-v2' : '/product/form';
        if( isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] && Tools::strpos($_SERVER['PHP_SELF'],'/product/form')) {
	        $uri = $_SERVER['PHP_SELF'];
        } else if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] && Tools::strpos($_SERVER['REQUEST_URI'],$find)) {
	        $uri = $_SERVER['REQUEST_URI'];
        }
        if ($uri) {
        	$pattern = $this->is810 ? '/\/products-v2\/(\d+)/' : '/\/product\/form\/(\d+)/';
	        preg_match($pattern, $uri, $matches);
	        if($matches && isset($matches[1])){
		        return (int)$matches[1];
	        }
        }
        return null;
    }
    public function getIdProductCombinationOnUrl()
    {
    	$uri = '';
    	$find = $this->is810 ? '/products-v2/combinations' : '/product/form';
        if( isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] && Tools::strpos($_SERVER['PHP_SELF'],'/product/form')) {
	        $uri = $_SERVER['PHP_SELF'];
        } else if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] && Tools::strpos($_SERVER['REQUEST_URI'],$find)) {
	        $uri = $_SERVER['REQUEST_URI'];
        }
        if ($uri) {
        	$pattern = $this->is810 ? '/\/products-v2\/combinations\/(\d+)/' : '/\/product\/form\/(\d+)/';
	        preg_match($pattern, $uri, $matches);
	        if($matches && isset($matches[1])){
		        return (int)$matches[1];
	        }
        }
        return null;
    }

    public function getDefaultTransConfig()
    {
        if((int)Configuration::get('ETS_TRANS_AUTO_SETTING_ENABLED'))
        {
            return array(
                'lang_source' => $this->getIdLangSource(),
                'lang_target' => $this->getListIdLangTarget(false),
                'field_option' => Configuration::get('ETS_TRANS_FILED_TRANS'),
                'wd_data' => Configuration::get('ETS_TRANS_WD_CONFIG'),
                'current_api' => Configuration::get('ETS_TRANS_SELECT_API'),
            );
        }
        return array(
            'wd_data' => Configuration::get('ETS_TRANS_WD_CONFIG'),
        );
    }

    public function transJs()
    {
    	return EtsTransDefine::getInstance()->transJs();
    }

    public function getSfContainer()
    {
        if (!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer')) {
            $kernel = null;
            try {
                if (!class_exists('AppKernel')) {
                    return null;
                }
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            } catch (Exception $ex) {
                return null;
            }
        }
        return call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
    }

    public function getRequestContainer()
    {
        if ($sfContainer = $this->getSfContainer()) {
            return $sfContainer->get('request_stack')->getCurrentRequest();
        }
        return null;
    }

    public function getFormTrans($pageId, $pageType, $isTransAll = false, $fieldTrans = null, $resetTrans = 0)
    {
	    $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
	    $idFeature = Tools::getIsset('idFeature') ? Tools::getValue('idFeature') : 0;
	    $isNewBlockreassurance = $this->isNewBlockreassurance();
	    $pcType = $this->getPcTypeValue();
	    $blogType = Tools::getIsset('blogType') && Validate::isCleanHtml(Tools::getValue('blogType')) ? Tools::getValue('blogType') : '';
        if((int)$isTransAll){
            $etsConfig = EtsTransConfig::getInstance();
            $selectedTheme = 0;
            $langCodeTarget = ($langCodeTarget = Tools::getValue('langCodeTarget')) && Validate::isCleanHtml($langCodeTarget) ? Language::getIdByIso($langCodeTarget) : '';
            if($pageType == 'email' || $pageType == 'theme'){
                $selectedTheme = ($selectedTheme = Tools::getValue('selectedTheme')) && Validate::isCleanHtml($selectedTheme) ? $selectedTheme : '';
            }
            else if($pageType == 'module'){
                $selectedTheme = ($selectedTheme = Tools::getValue('moduleName')) && Validate::isCleanHtml($selectedTheme) ? $selectedTheme : '' ;
            }
            $typeDataResume = $pageType;
            if($pageType == 'blog'){
                $typeDataResume = $pageType.'_'.$blogType;
            }
            $sfType = ($sfType = Tools::getValue('sfType')) && Validate::isCleanHtml($sfType) ? $sfType : '';

            if($etsConfig->hasResumeData($typeDataResume, $pcType, $selectedTheme, $sfType, $langCodeTarget))
            {
                if(!(int)$resetTrans){
	                if(!$this->isCached('parts/popup_alert_resume.tpl',$this->_getCacheId()))
	                {
		                $resumeData = $etsConfig->getResumeData($typeDataResume, $pcType, $selectedTheme, $sfType, $langCodeTarget);
		                $totalItems = ($totalItems = Tools::getValue('totalItems')) && Validate::isCleanHtml($totalItems) ? $totalItems : 0;
		                $resumeData['total_translate'] = $pageType == 'email' || $pageType == 'module' ? $totalItems : EtsTransDefine::getInstance()->getTotalTranslate($pageType, $idAttributeGroup, $idFeature, $isNewBlockreassurance, $pcType);
		                $resumeData['page_type'] = $pageType;
		                if($pageType == 'blog'){
			                $resumeData['blog_type'] = $blogType;
		                }
		                $this->smarty->assign($resumeData);

	                }
                    return $this->display(__FILE__, 'parts/popup_alert_resume.tpl', $this->_getCacheId());
                }
                else{
                    $etsConfig->deletePauseData($typeDataResume, $pcType, $selectedTheme, $sfType, $langCodeTarget);
                }
            }
        }

	    $totalTranslate = 0;
	    if($isTransAll){
		    $totalTranslate = ($pageType == 'email' || $pageType == 'module') && ($totalEmail = (int)Tools::getValue('totalItems')) ? $totalEmail : EtsTransDefine::getInstance()->getTotalTranslate($pageType, $idAttributeGroup, $idFeature, $isNewBlockreassurance, $pcType);
	    }
	    else if($pageId && is_array($pageId)){
		    $totalTranslate = count($pageId);
	    }
	    $langCodeTarget = ($langCodeTarget = Tools::getValue('langCodeTarget')) && Validate::isCleanHtml($langCodeTarget) ? $langCodeTarget : '';
        $langTargetDefault = [];
	    if($langCodeTarget && ($idLangTarget = Language::getIdByIso($langCodeTarget))){
		    $langTargetDefault = Language::getLanguage($idLangTarget);
	    }

	    $autoDetectLang = Tools::getIsset('autoDetectLang') ? Validate::isCleanHtml(Tools::getValue('autoDetectLang')) : '';
	    $rvCommentId = Tools::getIsset('rv_comment_id') ? (int)Tools::getValue('rv_comment_id') : 0;
	    $pageId = $pageId && is_array($pageId) ? implode(',', $pageId) : $pageId;
	    $hideDataToTrans = Tools::getIsset('hideDataToTrans') ? Validate::isCleanHtml(Tools::getValue('hideDataToTrans')) : '';
	    $isNewTemplate = (int)Tools::getValue('isNewTemplate');
	    $moduleName = Tools::getIsset('moduleName') ? Validate::isCleanHtml(Tools::getValue('moduleName')) : '';
	    $selectedTheme = Tools::getIsset('selectedTheme') ? Validate::isCleanHtml(Tools::getValue('selectedTheme')) : '';
	    $sfType = Tools::getIsset('sfType') ? Validate::isCleanHtml(Tools::getValue('sfType')) : '';
	    $originalLangId = 0;
	    if ($rvCommentId) {
		    $sql = "SELECT col.id_lang as origin_lang FROM `" . _DB_PREFIX_ . "ets_rv_product_comment_origin_lang` col WHERE col.id_ets_rv_product_comment=" . $rvCommentId;
		    $cmtLang = Db::getInstance()->executeS($sql);
		    if ($cmtLang && isset($cmtLang[0]['origin_lang'])) {
			    $originalLangId = (int)$cmtLang[0]['origin_lang'];
		    }
	    }

	    // cache id params [$pageType, $pageId, $blogType, $sfType, $hideDataToTrans, $isNewTemplate, $moduleName, $selectedTheme, $autoDetectLang, $totalTranslate, $langCodeTarget]
	    $file_path_tempalte = ($pageId && is_array($pageId)) || $isTransAll == 1 ? 'parts/popup_trans_all.tpl' : 'parts/popup_trans.tpl';


	    if($pageType == 'email' && $isTransAll)
	    {
		    $emailTrans = EtsTransInternational::getEmailTemplate(null, true);
	    }
	    $configAutoEnable = Configuration::get('ETS_TRANS_AUTO_SETTING_ENABLED');
	    $langTargetIds = $this->getListIdLangTarget();
	    if($configAutoEnable && $langTargetIds){
		    $langTarget = array();
		    foreach ($langTargetIds as $item){
			    $langTarget[] = Language::getLanguage($item);
		    }
	    }
	    $listFieldsTransProduct = [];
	    if ($list = Configuration::get('ETS_TRANS_LIST_FIELDS_PRODUCT')) {
		    $listFieldsTransProduct = json_decode($list, true);
	    }
	    $this->smarty->assign(array(
		    'allLanguages' => $this->getLangWithFlagImage(true),
		    'transOptions' => $this->renderTransOptions(),
		    'idLangDefault' => $this->getLangDefault(),
		    'pageId' => $pageId,
		    'rv_comment_id' => $rvCommentId,
		    'origin_lang_id' => $originalLangId,
		    'pageType' => $pageType,
		    'blogType' => $blogType,
		    'hasGoogleApiKey' => Configuration::get('ETS_TRANS_GOOGLE_API_KEY') ? true : false,
		    'hasApiKey' => $this->getCurrentApiKey() ? true : false,
		    'apiType' => $this->getApiType(),
		    'linkConfigApi' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
		    'isTransAll' => $isTransAll,
		    'optionMailTrans' => isset($emailTrans) && $emailTrans ? $emailTrans : array(),
		    'fieldTrans' => $fieldTrans,
		    'totalTranslate' => $totalTranslate,
		    'configAutoEnable' => $configAutoEnable,
		    'enableAnalysis' => (int)Configuration::get('ETS_TRANS_ENABLE_ANALYSIS'),
		    'langSource' => $configAutoEnable ? Language::getLanguage($this->getIdLangSource()) : '',
		    'langTarget' => isset($langTarget) ? $langTarget : '',
		    'langTargetIds' => $configAutoEnable ? $langTargetIds : array(),
		    'fieldTranslate' => $configAutoEnable ? Configuration::get('ETS_TRANS_FILED_TRANS') : '',
		    'langSourceDefault' => Language::getLanguage(Configuration::get('PS_LANG_DEFAULT') ?: Language::getIdByIso('en')),
		    'langTargetDefault' => $langTargetDefault ?: array(),
		    'selectedTheme' => $selectedTheme,
		    'moduleName' => $moduleName,
		    'sfType' => $sfType,
		    'imgDir' => _PS_IMG_,
		    'autoDetectLang' => $pageType === 'pc' && $autoDetectLang,
		    'isConfigGoogleRate' => Configuration::get('ETS_TRANS_RATE_GOOGLE') !== false && Tools::strlen(Configuration::get('ETS_TRANS_RATE_GOOGLE')),
		    'isLocalize' => in_array($pageType, array('theme', 'email', 'module', 'subject')),
		    'pcType' => $pcType,
		    'hideDataToTrans' => $hideDataToTrans,
		    'ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME'),
		    'ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME'),
		    'ETS_TRANS_AUTO_GENERATE_LINK_REWRITE' => (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE'),
		    'isNewTemplate' => $isNewTemplate,
		    'listNoLinkRewriteItems' => $this->listNoLinkRewriteItems,
		    'listFieldsTransProduct' => EtsTransDefine::getListFieldsProductTrans(),
		    'ETS_TRANS_LIST_FIELDS_PRODUCT' => $listFieldsTransProduct,
		    'autoDetectLanguage' => $this->isAutoDetectLanguage(),
		    'gte810' => $this->gte810
	    ));

	    return $this->display(__FILE__, $file_path_tempalte);
    }

	/**
	 * @return int
	 */
    public function isAutoDetectLanguage() {
	    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (int)Tools::getValue('ETS_TRANS_AUTO_DETECT_LANG') : (int)Configuration::get('ETS_TRANS_AUTO_DETECT_LANG');
    }

    public function renderTransOptions()
    {
    	return EtsTransDefine::getInstance()->renderTransOptions();
    }

    public function getLangWithFlagImage($active = false, $ignoreLangSource = false)
    {
        $languages = Language::getLanguages($active);
        foreach ($languages as $index => &$lang) {
        	if ($ignoreLangSource && $lang['id_lang'] == $this->getIdLangSource())
        		unset($languages[$index]);
            $lang['flag'] = _PS_IMG_ . 'l/' . $lang['id_lang'] . '.jpg';
        }
        return $languages;
    }

    public function getListApi() {
    	return EtsTransApi::getListApi();
    }

    public function getCurrentApiKey() {
    	$apiType = $this->getApiType();
    	$apiKey = null;
    	switch ($apiType) {
		    case EtsTransApi::$_GOOGLE_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_GOOGLE_API_KEY') : Configuration::get('ETS_TRANS_GOOGLE_API_KEY');
		    	break;
		    case EtsTransApi::$_BING_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_BING_API_KEY') : Configuration::get('ETS_TRANS_BING_API_KEY');
		    	break;
		    case EtsTransApi::$_LIBRE_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_LIBRE_API_KEY') : Configuration::get('ETS_TRANS_LIBRE_API_KEY');
		    	break;
		    case EtsTransApi::$_LECTO_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_LECTO_API_KEY') : Configuration::get('ETS_TRANS_LECTO_API_KEY');
		    	break;
		    case EtsTransApi::$_YANDEX_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_YANDEX_API_KEY') : Configuration::get('ETS_TRANS_YANDEX_API_KEY');
		    	break;
		    case EtsTransApi::$_DEEPL_API_TYPE:
			    $apiKey = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_DEEPL_API_KEY') : Configuration::get('ETS_TRANS_DEEPL_API_KEY');
		    	break;
	    }
	    return $apiKey;
    }

    public function getApiType() {

	    $apiType = Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? Tools::getValue('ETS_TRANS_SELECT_API') : Configuration::get('ETS_TRANS_SELECT_API');
	    if (!$apiType) $apiType = EtsTransApi::$_GOOGLE_API_TYPE;

	    return $apiType;
    }
    public function isSettingTab($tab = 'setting')
    {
        return Tools::getIsset('ETS_TRANS_TAB_SETTING') && Tools::getValue('ETS_TRANS_TAB_SETTING') == $tab;
    }
    public function getPcTypeValue() {
	    $pcType = '';
    	if (Tools::getIsset('pcType') && Tools::getValue('pcType') != $pcType) {
		    $pcType = Tools::getValue('pcType');
		    $pcType = Validate::isCleanHtml($pcType) ? $pcType : '';
	    }
    	return $pcType;
    }

    public function isEnableAppendContextWord() {
	    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab('exception') ? (int)Tools::getValue('ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD') : (int)Configuration::get('ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD');
    }

    public function isEnablePhraseKey() {
	    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab('exception') ? Tools::getValue('ETS_TRANS_ENABLE_KEY_PHRASE') : Configuration::get('ETS_TRANS_ENABLE_KEY_PHRASE');
    }

    public function isEnableChatGPT() {
    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab('chatgpt') ? Tools::getValue('ETS_TRANS_ENABLE_CHATGPT') : Configuration::get('ETS_TRANS_ENABLE_CHATGPT');
    }

    public function getKeyChatGPT() {
    	return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab('chatgpt') ? Tools::getValue('ETS_TRANS_CHATGPT_API') : Configuration::get('ETS_TRANS_CHATGPT_API');
    }

    public function getEnableAutoSetting() {
	    return Tools::isSubmit('saveEtstransSettings') && $this->isSettingTab() ? (int)Tools::getValue('ETS_TRANS_AUTO_SETTING_ENABLED') : (int)Configuration::get('ETS_TRANS_AUTO_SETTING_ENABLED');
    }

    public function getActiveSettingTab() {
	    return Tools::isSubmit('saveEtstransSettings') ? Tools::getValue('ETS_TRANS_TAB_SETTING') : 'setting';
    }

    public function isModuleInstalled($moduleName)
    {
        $controller = ($controller = Tools::getValue('controller')) && Validate::isCleanHtml($controller) ? $controller : '';

        if($moduleName == 'ets_reviews' || $moduleName == 'ybc_blog'){
            $listControllers = $moduleName == 'ets_reviews' ? array('AdminEtsRVReviews','AdminEtsRVReviewsRatings', 'AdminEtsRVComments', 'AdminEtsRVReplies', 'AdminEtsRVQuestions', 'AdminEtsRVQuestionComments', 'AdminEtsRVAnswers', 'AdminEtsRVAnswerComments', 'AdminEtsRVQuestionsAnswers') : array('AdminYbcBlogCategory','AdminYbcBlogPost');

            return Module::isInstalled($moduleName)
                && Module::isEnabled($moduleName) && in_array($controller, $listControllers);
        }
        return Module::isInstalled($moduleName)
            && Module::isEnabled($moduleName)
            && $controller == 'AdminModules'
            && Tools::getValue('configure') === $moduleName;
    }


	public function getFieldsValues($formFields, $primaryKey, $objClass, $saveBtnName)
	{
		$fields = array();
		if (Tools::isSubmit($primaryKey))
		{
			$obj = new $objClass((int)Tools::getValue($primaryKey));
			$fields[$primaryKey] = (int)Tools::getValue($primaryKey, $obj->{$primaryKey});
		}
		else
		{
			$obj = new $objClass();
			$fields[$primaryKey] = 0;
		}

		foreach($formFields as $field)
		{
			if (isset($field['ignore']) && $field['ignore'])
				continue;
			if(!isset($field['primary_key']) && !isset($field['multi_lang']) && !isset($field['connection']))
			{
				$fieldName = $field['name'];
				$fields[$field['name']] = Tools::getValue($field['name'], $obj->{$fieldName});
			}

		}
		$languages = Language::getLanguages(false);

		/**
		 *  Default
		 */

		if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
		{
			foreach($formFields as $field)
			{
				if (isset($field['ignore']) && $field['ignore'])
					continue;
				if(isset($field['default']) && !isset($field['multi_lang']))
				{
					if(isset($field['default_submit']))
						$fields[$field['name']] = Tools::getValue($field['name']) ? : $field['default'];
					else
						$fields[$field['name']] = $field['default'];
				}
			}
		}


		/**
		 * Multiple language
		 */
		foreach ($languages as $lang)
		{
			foreach($formFields as $field)
			{
				if (isset($field['ignore']) && $field['ignore'])
					continue;
				if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
				{
					if(isset($field['multi_lang']))
					{
						if(isset($field['default']))
							$fields[$field['name']][$lang['id_lang']] = $field['default'];
						else
							$fields[$field['name']][$lang['id_lang']] = '';
					}
				}
				elseif(Tools::isSubmit($saveBtnName))
				{
					if(isset($field['multi_lang']))
						$fields[$field['name']][$lang['id_lang']] = Tools::getValue($field['name'].'_'.(int)$lang['id_lang']);

				}
				else{
					if(isset($field['multi_lang']))
					{
						$fieldName = $field['name'];
						$field_langs = $obj->{$fieldName};
						$fields[$field['name']][$lang['id_lang']] = isset($field_langs[$lang['id_lang']]) ? $field_langs[$lang['id_lang']]:'';
					}
				}
			}
		}
		$fields['control'] = trim(Tools::getValue('control')) ? : '';

		return $fields;

	}

	// -- cache form chat gpt
    public function getFormChatGPT($formData) {
	    $currentLang = isset($formData['id_language']) && $formData['id_language'] ? $formData['id_language'] : ($this->context->language->id ?: Configuration::get('PS_LANG_DEFAULT'));
	    $this->smarty->assign(
		    array(
			    'languages' => Language::getLanguages(true),
			    'defaultFormLanguage' => $currentLang,
			    'gpt_templates' =>  EtsTransChatGPT::getAllTemplate(),
			    'chatgpt_messages' => ($messages = EtsTransChatGPTMessage::getMessages(false, $formData['apply_for'], $currentLang)) ? array_reverse($messages) : array(),
		    )
	    );
	    return $this->display(__FILE__,'form_chatgpt.tpl');
    }

    public function clearDataChatGpt() {
    	$this->_clearCache('form_chatgpt.tpl', $this->_getCacheId());
    	return EtsTransChatGPTMessage::getInstance()->clearDataChatGpt();
    }

    public function submitChatGPT($formData) {
	    $errors = array();
	    if (!$formData || !is_array($formData)) {
		    $errors[] = $this->l('The data is invalid');
	    }
	    if (!isset($formData['prompt']) && !$formData['prompt'])
		    $errors[] = $this->l('The prompt data is required');
	    if (!Validate::isCleanHtml($formData['prompt'],true))
		    $errors[] = $this->l('The prompt is invalid');

	    if(!$errors)
	    {
		    $message = new EtsTransChatGPTMessage();
		    $message->is_chatgpt = 0;
		    $message->message = $formData['prompt'];
		    if(!$message->add())
			    $errors[] = $this->l('An error occurred while saving the message');
	    }

	    if ($errors) {
		    return array(
			    'errors' => $errors,
			    'data' => '',
			    'message' => $this->l('Form validate is failed')
		    );
	    }
	    $this->_clearCache('form_chatgpt.tpl', $this->_getCacheId());
	    //do chatgpt
	    $chatGPT = new EtsTransChatGPT();
	    $chatGPT->setKey($this->getKeyChatGPT());
	    $chatGPT->setModel(isset($formData['model']) && $formData['model'] ? $formData['model'] : '');
	    $chatGPT->setPrompt($formData['prompt']);
	    $res = $chatGPT->completions();
	    if (!$res['errors']) {
	    	$res['data'] = $chatGPT->formatAndSaveMessage($res['data'], $formData['id_language'], $formData['apply_for']);
	    }
	    return $res;
    }

    /**
     * @param $nbTranslated
     * @param $translateType
     * @param $translateType1
     * @return array|string|string[]
     */
    public function renderMessageResponse($nbTranslated, $translateType = 'products', $translateType1 = 'product')
    {

        if(!$nbTranslated){
            $message = $this->l(sprintf('Translated %s successfully', $translateType));
        }
        else if ($nbTranslated > 1) {
            $message = str_replace('[number]', $nbTranslated, $this->l(sprintf('Translated [number] %s successfully', $translateType)));
        }
        else {
            $message = str_replace('[number]', $nbTranslated, $this->l(sprintf('Translated [number] %s successfully', $translateType1)));
        }

        return $message;
    }

    public function translateDataPage($formData, $pageType, $isDetailPage = false)
    {
        $errors = array();
        if (!$formData || !is_array($formData)) {
            $errors[] = $this->l('The translate data is invalid');
        }

	    $autoDetectLanguage = $this->isAutoDetectLanguage();
        if (isset($formData['auto_detect_language']))
            $autoDetectLanguage = $formData['auto_detect_language'];
        if (!isset($formData['trans_source']) || !(int)$formData['trans_source']) {
        	if ($autoDetectLanguage) {
		        $formData['trans_source'] = 0;
	        } else {
		        if(!in_array($pageType, array('theme', 'email', 'module'))){
			        $errors[] = $this->l('The translate source language is required');
		        }
		        else{
			        $formData['trans_source'] = 0;
		        }
	        }
        }

        if (!isset($formData['trans_target']) || !$formData['trans_target']) {
            $errors[] = $this->l('The translate target language is required');
        }

        if (!isset($formData['trans_option']) || !(string)$formData['trans_option']) {
            $errors[] = $this->l('The translate field is required');
        }

        if ($errors) {
            return array(
                'errors' => $errors,
                'data' => array(),
                'message' => $this->l('Form validate is failed')
            );
        }
        if(!is_array($formData['trans_target'])){
            $formData['trans_target'] = explode(',', $formData['trans_target']);
        }
        $message = "";
        if(!$this->context->cookie->__get('ets_trans_translate')){
            $this->context->cookie->__set('ets_trans_translate', EtsTransLog::generateIdCode());
        }
        $extraOptions = array(
            'auto_generate_link_rewrite' => isset($formData['auto_generate_link_rewrite']) ? (int)$formData['auto_generate_link_rewrite'] : (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE') ,
        );
	    $pcType = $this->getPcTypeValue();
        if ($isDetailPage)
        {
            $savedSuccess = false;
            if($pageType == 'email'){
                $emailData = EtsTransInternational::getEmailSource($formData);
                $resTrans = EtsTransPage::translate((int)$formData['trans_source'], $emailData, $pageType, false, [], 0, [], [], null, null, $autoDetectLanguage);
                $results = isset($resTrans['result']) ? $resTrans['result'] : array();
                $results = EtsTransInternational::modifyResultTranslated($results, $emailData);
                $textTranslatedLength = EtsTransInternational::getTextLength($emailData['source']);
                $results['nb_translated'] = EtsTransInternational::getTotalEmailTranslate($emailData['target']);
                $results['nb_char_translated'] = $textTranslatedLength;

                $emailSelectedTheme = isset($formData['selected_theme']) ? $formData['selected_theme'] : null;
                if($results && (!isset($results['errors']) || !$results['errors'])){
                    EtsTransInternational::saveEmailTranslated($results, $emailSelectedTheme);
                    $savedSuccess = (bool)$results['nb_translated'];
                }
            }
            else if($pageType == 'module' && $formData['trans_all'] == 1 && $formData['module_name'])
            {
                $etsTransModule = new EtsTransModule($formData['module_name']);
	            $etsTransModule->setIsDetectLanguage($autoDetectLanguage);
                if((int)Tools::getValue('initTranslate')){
                    $etsTransModule->loadModuleFiles();
                    $results = array(
                        'errors' => false,
                        'data' => array(),
                        'after_init' => 1,
                        'done' => 1,
                        'step' => 1
                    );
                }
                else{
	                $etsTransModule->setLangSource($autoDetectLanguage ? null : $formData['trans_source']);
                    $etsTransModule->setLangTarget($formData['trans_target']);
                    $etsTransModule->setTransOption($formData['trans_option']);
                    $results = $etsTransModule->translateModule();
                    $savedSuccess = true;
                }
            }
            else if($pageType == 'theme' && $formData['trans_all'] == 1)
            {
                $sfType = ($sfType = Tools::getValue('sfType')) && Validate::isCleanHtml($sfType) ? $sfType : '';
                $sfTransType = 'theme';
                switch ($sfType)
                {
                    case 'themes':
                        $sfTransType = 'theme';
                        break;
                    case 'modules':
                        $sfTransType = 'sfmodule';
                        break;
                    case 'back':
                        $sfTransType = 'back';
                        break;
                    case 'others':
                        $sfTransType = 'others';
                        break;
                    case 'mails':
                        $sfTransType = 'mail';
                        break;
                }
                $etsTransTheme = new EtsTransNewSystem($sfTransType);
                $etsTransTheme->setAdminFD(($adminFd = Tools::getValue('adminFD')) && Validate::isCleanHtml($adminFd) ? $adminFd : '');
                $etsTransTheme->setSelectedName($formData['selected_theme']);
	            $etsTransTheme->setIsDetectLanguage($autoDetectLanguage);
                $initStep = isset($formData['init_step']) ? (int)$formData['init_step'] : 1;
                if((int)Tools::getValue('initTranslate')){
                    $initDone = $etsTransTheme->loadFileSystem($initStep);
                    $results = array(
                        'errors' => false,
                        'data' => array(),
                        'done' => $initDone === true ? 1 : 0,
                        'step' => $initStep,
                        'after_init' => 1
                    );
                }
                else{
                    $etsTransTheme->setLangSource($autoDetectLanguage ? null : $formData['trans_source']);
                    $etsTransTheme->setLangTarget($formData['trans_target']);
                    $etsTransTheme->setTransOption($formData['trans_option']);
                    $results = $etsTransTheme->translateData();
                    if(isset($results['stop_translate']) && $results['stop_translate']){
                        $etsConfig = EtsTransConfig::getInstance();
                        $etsConfig->deletePauseData('theme', $pcType, $formData['selected_theme'], $sfType, $formData['trans_target']);
                    }
                    $savedSuccess = true;
                }

            }
            else
            {
				$langSource = $pageType == 'pc' && $autoDetectLanguage ? null : (int)$formData['trans_source'];
				if(isset($formData['trans_data']) && $formData['trans_data']){
				  $formData['trans_data']['lang_source'] = (int)$formData['trans_source'];
				}
				$pageId = isset($formData['page_id']) && $formData['page_id'] ? (int)$formData['page_id'] : 0;
	            $transFields = isset($formData['etsTransFields']) && count($formData['etsTransFields']) ? $formData['etsTransFields'] : [];
	            if($pageType == 'product'){
                  $extraOptions['ignore_product_name'] = $formData['ignore_product_name'];
                  $extraOptions['ignore_content_has_product_name'] = $formData['ignore_content_has_product_name'];
                }
	            if (!$langSource && !$autoDetectLanguage && isset($formData['trans_data']['lang_source']) && $formData['trans_data']['lang_source'] && $pageType == 'pc' )
	            	$langSource = (int)$formData['trans_data']['lang_source'];
	            if (isset($formData['isTransView']) && $pageType == 'pc') {
		            $results = EtsTransModule::translateViewModulePc($formData, $pcType);
		            $savedSuccess = true;
	            } else {
		            $resTrans = EtsTransPage::translate($langSource, isset($formData['trans_data']) ? $formData['trans_data'] : array(), $pageType, $this->isNewBlockreassurance(), isset($formData['col_data']) ? $formData['col_data'] : array(), $pageId, $extraOptions, $transFields, isset($formData['image_id']) ? $formData['image_id'] : null, isset($formData['trans_option']) ? $formData['trans_option'] : null, $autoDetectLanguage);
		            $results = isset($resTrans['result']) ? $resTrans['result'] : array();
		            if(isset($resTrans['dataSaved']) && $resTrans['dataSaved']){
			            $savedSuccess = true;
		            }
		            if(isset($resTrans['resultText']) && $resTrans['resultText']){
			            if($pageType == 'module' && isset($formData['module_name']) && $formData['module_name'] && isset($formData['file_trans']) && $formData['file_trans']){
				            EtsTransModule::updateTextModule($resTrans['resultText'], $formData['module_name'], $formData['file_trans']);
				            $savedSuccess = true;
			            }
		            }
		            if($results && (!isset($results['errors']) || !$results['errors']) ){
			            if(isset($formData['ept_tab_id']) && (int)$formData['ept_tab_id']){
				            $colTab = 'content';
				            if (isset($formData['col_data'])){
					            foreach ($formData['col_data'] as $colDef){
						            if ($colDef == 'file_desc'){
							            $colTab = 'file_desc';
							            break;
						            }
					            }
				            }
				            EtsTransModule::updateContentProductTab((int)$formData['ept_tab_id'], $pageId,$results, $colTab);
			            }
			            if($pageType == 'megamenu' && isset($formData['col_data']) && isset($formData['menu_type'])){
				            $savedSuccess = EtsTransModule::updateTransMegamenuItem((int)$formData['page_id'], $formData['menu_type'], $formData['col_data'], $results);
			            }
			            elseif($pageType == 'blog' && isset($formData['col_data'])){
				            $savedSuccess = EtsTransModule::updateTransBlogItem((int)$formData['page_id'], $formData['blog_type'], $formData['col_data'], $results, $extraOptions);
			            }
			            elseif($pageType == 'pc' && isset($formData['col_data'])){
				            $savedSuccess = EtsTransModule::updateTransPCItem((int)$formData['page_id'],$pcType, $formData['col_data'], $results);
			            }
			            elseif($pageType == 'theme'){
				            $savedSuccess = true;
			            }
		            }
	            }
            }

            if ($results && isset($results['errors']) && $results['errors']) {
                $errors[] = isset($results['message']) ? $results['message'] : (is_string($results['errors']) ? $results['errors'] : $this->l('Translate failed'));
            } else{
                if(
                	(isset($formData['trans_all']) && $formData['trans_all'])
	                || (isset($results['nb_translated']) && $results['nb_translated'])
	                || ((!isset($formData['trans_all']) || !$formData['trans_all']) && $results)
                ){
	                $message = $this->l('Translated successfully, however translations HAVE NOT been saved. Please manually save the translations');
	                if ($savedSuccess) {
	                	if ($pageType == 'theme' && (!isset($formData['trans_all']) || !$formData['trans_all'])) {
			                $message = $this->l('Translated successfully');
		                } else {
	                		if ($pageType == 'product' && isset($formData['etsTransFields']) && in_array('legend', $formData['etsTransFields'])) {
				                $message = $this->l('Translated successfully, however some fields cannot display data. Please reload page to display new data!');
			                }else
				                $message = $this->l('Translated successfully, translations saved. However some fields cannot display data. Please reload page to display new data!');
		                }
	                }
                }
                else{
//                    $message = $this->l('All content has been translated, nothing to do!');
                    $message = $this->l('All content has been translated or there is no data to translate, nothing to do!');
                }
            }
            if($this->context->cookie->__get('ets_trans_translate')){
                $this->context->cookie->__unset('ets_trans_translate');
            }
        }
        else
        {
            $etsConfig = EtsTransConfig::getInstance();
            $isTransAll = isset($formData['trans_all']) && $formData['trans_all'] == 1;
            switch ($pageType) {
                case 'product':
                    $extraOptions = array(
                        'ignore_product_name' => isset($formData['ignore_product_name']) ? (int)$formData['ignore_product_name'] : (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME'),
                        'ignore_content_has_product_name' => isset($formData['ignore_content_has_product_name']) ? (int)$formData['ignore_content_has_product_name'] : (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME'),
                    );
                    $transFields = isset($formData['etsTransFields']) && count($formData['etsTransFields']) ? $formData['etsTransFields'] : [];
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::transAllProduct($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $transFields, $autoDetectLanguage);

                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('product');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('product', $formData, $extraOptions, $transFields, 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated);
                    break;
                case 'category':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllCategory($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, [], $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('category');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('category', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'categories', 'category');

                    break;
                case 'cms':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllCMS($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('cms');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('cms', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'CMS', 'CMS');
                    break;
                case 'cms_category':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllCMSCategory($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('cms_category');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('cms_category', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'categories', 'category');
                    break;
                case 'manufacturer':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllManufacturer($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('manufacturer');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('manufacturer', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'manufacturers', 'manufacturer');
                    break;
                case 'supplier':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllSupplier($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('supplier');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('supplier', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'suppliers', 'supplier');
                    break;
                case 'attribute_group':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllAttributeGroup($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('attribute_group');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('attribute_group', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'groups', 'group');
                    break;
                case 'attribute':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
                        $results = EtsTransPage::translateAllAttribute($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, [], $idAttributeGroup, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('attribute');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('attribute', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'attributes', 'attribute');
                    break;
                case 'feature':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllFeature($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('feature');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('feature', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'features', 'feature');
                    break;
                case 'feature_value':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllFeatureValue($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('feature_value');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('feature_value', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'feature values', 'feature value');
                    break;
                case 'blockreassurance':
	                $isNewBlockreassurance = $this->isNewBlockreassurance();
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;

                        $results = EtsTransPage::translateAllBlockReassurance($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $isNewBlockreassurance, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('blockreassurance');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('blockreassurance', $formData, $extraOptions, [], $isNewBlockreassurance, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'reassurance blocks', 'reassurance block');
                    break;
                case 'ps_linklist':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllLinkList($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $extraOptions, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('ps_linklist');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('ps_linklist', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'link widget', 'link widget');
                    break;
                case 'ps_mainmenu':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllMainMenu($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('ps_mainmenu');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('ps_mainmenu', $formData, $extraOptions, [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'menu items', 'menu item');
                    break;
                case 'ps_imageslider':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllImageSliders($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('ps_imageslider');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('ps_imageslider', $formData, [], [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'sliders', 'slider');
                    break;
                case 'ets_extraproducttabs':
                    if($isTransAll)
                    {
                        $offset = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0;
                        $results = EtsTransPage::translateAllExtraProductTabs($formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $offset, $autoDetectLanguage);
                        if($results['stop_translate']){
                            $etsConfig->deletePauseData('ets_extraproducttabs');
                        }
                    }
                    else {
                        $results = EtsTransPage::translatePage('ets_extraproducttabs', $formData, [], [], 0, $autoDetectLanguage);
                    }
                    $nbTranslated = isset($results['nb_translated']) ? $results['nb_translated'] : 0;
                    $message = $this->renderMessageResponse($nbTranslated, 'tabs', 'tab');
                    break;
                default:
                    $results = array();
                    break;
            }

            if ($results && isset($results['errors']) && $results['errors']) {
                if($this->context->cookie->__get('ets_trans_translate')){
                    $this->context->cookie->__unset('ets_trans_translate');
                }
                if($isTransAll){
                    $etsConfig->updatePauseData(array(
                        'pageType' => $pageType,
                        'nbTranslated' => isset($formData['nb_translated']) ? (int)$formData['nb_translated'] : 0,
                        'nbCharTranslated' => isset($formData['nb_char_translated']) ? (int)$formData['nb_char_translated'] : 0,
                        'langSource' => isset($formData['trans_source']) ? $formData['trans_source'] : 0,
                        'langTarget' => isset($formData['trans_target']) ? $formData['trans_target'] : 0,
                        'fieldOption' => isset($formData['trans_option']) ? $formData['trans_option'] : '',
                    ));
                }
                $errors[] = isset($results['message']) ? $results['message'] : $this->l('Translate failed');
            }
            if(isset($results['stop_translate']) && $results['stop_translate']){
                if($this->context->cookie->__get('ets_trans_translate')){
                    $this->context->cookie->__unset('ets_trans_translate');
                }
            }
        }
        $noTrans = false;
        if($isDetailPage && (!isset($formData['trans_all']) ||$formData['trans_all'] != 1)){
            if(!isset($results) || !$results){
//                $message = $this->l('All content has been translated, nothing to do!');
	            $message = $this->l('All content has been translated or there is no data to translate, nothing to do!');
                $noTrans = true;
            }
        }
        else if((!isset($formData['trans_all']) ||$formData['trans_all'] != 1) && !isset($results['translated_length'])){
//            $message = $this->l('All content has been translated, nothing to do!');
	        $message = $this->l('All content has been translated or there is no data to translate, nothing to do!');
            $noTrans = true;
        }
        if(!$this->getCurrentApiKey()){
        	$apiKey = $this->getApiType();
        	switch ($apiKey) {
		        case EtsTransApi::$_BING_API_TYPE:
			        $message = $this->l('No Bing translate API key was found.');
			        break;
		        case EtsTransApi::$_DEEPL_API_TYPE:
			        $message = $this->l('No DeepL translate API key was found.');
			        break;
		        case EtsTransApi::$_YANDEX_API_TYPE:
			        $message = $this->l('No Yandex translate API key was found.');
			        break;
		        case EtsTransApi::$_LECTO_API_TYPE:
			        $message = $this->l('No Lecto translate API key was found.');
			        break;
		        case EtsTransApi::$_LIBRE_API_TYPE:
			        $message = $this->l('No Libre translate API key was found.');
			        break;
		        case EtsTransApi::$_GOOGLE_API_TYPE:
		        default:
			        $message = $this->l('No Google translate API key was found.');
			        break;
	        }
            $errors = [$message];
        }
        return array(
            'errors' => $errors,
            'data' => $results,
            'noTrans' => $noTrans ? 1 : 0,
            'message' => $errors ? '' : $message // Message has a short code : [number]
        );
    }

    public function actionAjax()
    {
        if(Tools::isSubmit('etsTransGetFormTranslate')){
            $form = $this->getFormTrans(
                Tools::getValue('pageId'),
                Tools::getValue('pageType'),
                Tools::getValue('isTransAll'),
                Tools::getValue('fieldTrans'),
                Tools::getValue('resetTrans')
            );
            if($form){
                die(json_encode(array(
                    'success' => true,
                    'form' => $form,
                )));
            }
            die(json_encode(array(
                'success' => false,
                'form' => '',
            )));
        }

        if(Tools::isSubmit('etsTransGetFormInterTrans'))
        {
            $form = $this->getFormTrans(null,
                Tools::getValue('pageType'),
                (int)Tools::getValue('isTransAll'),
                Tools::getValue('fieldTrans'),
                Tools::getValue('resetTrans')
            );
            if($form){
                die(json_encode(array(
                    'success' => true,
                    'form' => $form,
                )));
            }
            die(json_encode(array(
                'success' => false,
                'form' => '',
            )));
        }

        if(Tools::isSubmit('etsTransCheckApiKey'))
        {
            if($apiKey = Tools::getValue('apiKey')){
            	if (Tools::getIsset('isCheckChatgpt') && Tools::getValue('isCheckChatgpt')) {
		            $chatGPT = new EtsTransChatGPT();
		            if ($res = $chatGPT->validateApiKey($apiKey)) {
		            	if ($res['errors']) {
				            die(json_encode(array(
					            'success' => false,
					            'message' => $this->l( is_array($res['errors']) ? $res['errors'][0] : $res['errors'])
				            )));
			            } else {
				            die(json_encode(array(
					            'success' => true,
					            'message' => $this->l('The chatGPT API key is good')
				            )));
			            }
 		            } else {
			            die(json_encode(array(
				            'success' => false,
				            'message' => $this->l('The chatGPT API key is invalid')
			            )));
		            }
	            } else {
		            $api = new EtsTransApi();
		            $apiType = Tools::getIsset('apiType') ? Tools::getValue('apiType') : $api::$_GOOGLE_API_TYPE;
		            $validApi = $api->validateApiKey($apiKey, $apiType);
		            if($validApi['status'] == 200)
		            {
			            die(json_encode(array(
				            'success' => true,
				            'message' => $this->l('The translate API key is good')
			            )));
		            }
		            die(json_encode(array(
			            'success' => false,
			            'message' => $validApi['message'] ?: $this->l('The translate API key is invalid')
		            )));
	            }
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('The translate API key is required')
            )));
        }

        if(Tools::isSubmit('etsTransClearLogItem')){
	        $this->_clearCache('list_translate_log.tpl', $this->_getCacheId());
            $idLog = (int)Tools::getValue('idLog');
            if($idLog){
                $log = new EtsTransLog($idLog);
                if($log && $log->id){
                    $log->delete();
                    die(json_encode(array(
                        'success' => true,
                        'message' => $this->l('Deleted log item successfully')
                    )));
                }
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Failed to delete log item')
            )));
        }

        if(Tools::isSubmit('etsTransClearAllLogs')){
	        $this->_clearCache('list_translate_log.tpl', $this->_getCacheId());
            if(EtsTransLog::clearAllLog()){
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Clear log successfully')
                )));
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Clear log failed')
            )));
        }
        if(Tools::isSubmit('etsTransGetFormConfigTransAll')){
            if($transWd = Tools::getValue('transWd')){
                if(is_array($transWd)){
                    $transWd = implode(',', $transWd);
                }
            }
            else{
	            $transWd = '';
            }

	        Configuration::updateValue('ETS_TRANS_WD_CONFIG', $transWd);
            $formHtml = $this->getFormConfigTransAll((int)Tools::getValue('interTrans'), (int)Tools::getValue('resetTrans'));
            die(json_encode(array(
                'success' => true,
                'form' => $formHtml
            )));
        }

        if(Tools::isSubmit('etsTransGetFormAnalysis')){
            die(json_encode(array(
                'success' => true,
                'form_html' => $this->display(__FILE__, 'parts/popup_analysis.tpl'),
            )));
        }
        if(Tools::isSubmit('etsTransGetFormAnalysisCompleted')){
        	$apiType = Configuration::get('ETS_TRANS_SELECT_API') ?: EtsTransApi::$_GOOGLE_API_TYPE;
            $this->smarty->assign(array(
                'isConfigGoogleRate' => Configuration::get('ETS_TRANS_RATE_GOOGLE') !== false && Tools::strlen(Configuration::get('ETS_TRANS_RATE_GOOGLE')),
				'api_name' => EtsTransApi::getApiNameByKey($apiType),
	            'total_character' => EtsTransLogRequest::getTotalTextTrans(date('Y-m-01'), date('Y-m-t'), $apiType)
            ));
            die(json_encode(array(
                'success' => true,
                'form_html' => $this->display(__FILE__, 'parts/popup_analysis_completed.tpl'),
            )));
        }
        if(Tools::isSubmit('etsTransGetFormTranslatingAll')){
            die(json_encode(array(
                'success' => true,
                'form_html' => $this->display(__FILE__, 'parts/popup_translating.tpl'),
            )));
        }
        if (Tools::isSubmit('etsTransLivechatTicket')){
            $sourceLang = Tools::getValue('sourceLang');
            if (!Validate::isString($sourceLang))
                $sourceLang = '';
            $targetLang = Tools::getValue('targetLang');
            if (!Validate::isString($targetLang))
                $targetLang = '';
            $ticketText = Tools::getValue('ticketText');
            if (!Validate::isString($ticketText))
                $ticketText = '';
            $idTicket = (int)Tools::getValue('idTicket');
            $resultTrans = EtsTransModule::translateLivechatTicket($idTicket, $sourceLang,$targetLang, $ticketText);

            if ($resultTrans){
                if (isset($resultTrans['lang_source']) && $resultTrans['lang_source'] && isset($resultTrans['lang_target']) && $resultTrans['lang_target']) {
                    $resultTrans['lang_detected_message'] = sprintf($this->l('Translate from [b]%s[/b] to [b]%s[/b]'), $resultTrans['lang_source']['name'], $resultTrans['lang_target']['name']);
                }
                elseif(isset($resultTrans['detectedSourceLanguage']) && isset($resultTrans['detectedSourceLanguage'][0])){
                    $resultTrans['lang_detected_message'] = sprintf($this->l('Translate from [b]%s[/b] to [b]%s[/b]'), $resultTrans['detectedSourceLanguage'][0], $resultTrans['lang_target']['name']);;
                }
                else{
                    $resultTrans['lang_detected_message'] = '';
                }
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Translated successfully'),
                    'data' => $resultTrans
                )));
            }
            else{
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->l('Cannot find target language to translate'),
                    'data' => $resultTrans
                )));
            }
        }

        if (Tools::isSubmit('etsTransHelpdeskTicket')){
            $sourceLang = Tools::getValue('sourceLang');
            if (!Validate::isString($sourceLang))
                $sourceLang = '';
            $targetLang = Tools::getValue('targetLang');
            if (!Validate::isString($targetLang))
                $targetLang = '';
            $ticketText = Tools::getValue('ticketText');
            if (!Validate::isString($ticketText))
                $ticketText = '';
            $idTicket = (int)Tools::getValue('idTicket');
            $resultTrans = EtsTransModule::translateLivechatTicket($idTicket, $sourceLang,$targetLang, $ticketText);

            if ($resultTrans){
                if (isset($resultTrans['lang_source']) && $resultTrans['lang_source'] && isset($resultTrans['lang_target']) && $resultTrans['lang_target']) {
                    $resultTrans['lang_detected_message'] = sprintf($this->l('Translate from [b]%s[/b] to [b]%s[/b]'), $resultTrans['lang_source']['name'], $resultTrans['lang_target']['name']);
                }
                elseif(isset($resultTrans['detectedSourceLanguage']) && isset($resultTrans['detectedSourceLanguage'][0])){
                    $resultTrans['lang_detected_message'] = sprintf($this->l('Translate from [b]%s[/b] to [b]%s[/b]'), $resultTrans['detectedSourceLanguage'][0], $resultTrans['lang_target']['name']);;
                }
                else{
                    $resultTrans['lang_detected_message'] = '';
                }
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Translated successfully'),
                    'data' => $resultTrans
                )));
            }
            else{
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->l('Cannot find target language to translate'),
                    'data' => $resultTrans
                )));
            }
        }

	    if (Tools::isSubmit('etsTransSaveTemplateGPT')) {
		    $this->saveTemplateGPT();
	    }
	    if (Tools::isSubmit('delGPT') && ($id_ets_trans_chatgpt_template = (int)Tools::getValue('id_ets_trans_chatgpt_template'))) {
			$this->deleteTemplateGPT($id_ets_trans_chatgpt_template);
	    }
	    if (Tools::isSubmit('editTempChatGpt') && ($id_ets_trans_chatgpt_template = (int)Tools::getValue('id_ets_trans_chatgpt_template'))) {
			$this->renderFormTemplateChatGPT();
	    }
    }
	private function deleteTemplateGPT($id_ets_trans_chatgpt_template)
	{
		$templateGpt = new EtsTransChatGPT($id_ets_trans_chatgpt_template);
		$templateGpt->delete();
		if(Tools::isSubmit('ajax'))
		{
			die(
			json_encode(
				array(
					'success' => $this->l('Deleted successfully'),
				)
			)
			);
		}
	}

	private function saveTemplateGPT(){
		$errors = array();
		if(($id_ets_trans_chatgpt_template = (int)Tools::getValue('id_ets_trans_chatgpt_template')))
		{
			$template = new EtsTransChatGPT($id_ets_trans_chatgpt_template);
		}
		else
		{
			$template = new EtsTransChatGPT();
		}
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

		$label = Tools::getValue('label_'.$id_lang_default);
		$content = Tools::getValue('content_'.$id_lang_default);
		if(!$label)
			$errors[] = $this->l('Label is required');
		elseif($label && !Validate::isCleanHtml($label))
			$errors[] = $this->l('Label is required');
		if(!$content)
			$errors[] = $this->l('Content is required');
		elseif($content && !Validate::isCleanHtml($content))
			$errors[] = $this->l('Content is required');
		if(!$errors)
		{
			$languages = Language::getLanguages(false);
			foreach($languages as $language)
			{
				$label_lang = Tools::getValue('label_'.$language['id_lang']);
				$content_lang = Tools::getValue('content_'.$language['id_lang']);
				if($label_lang && !Validate::isCleanHtml($label_lang))
					$errors[] = sprintf($this->l('Label in %s is not valid'), $language['iso_code']);
				else
					$template->label[$language['id_lang']] = $label_lang ? : $label;
				if($content_lang && !Validate::isCleanHtml($content_lang))
					$errors[] = sprintf($this->l('Content in %s is not valid'), $language['iso_code']);
				else
					$template->content[$language['id_lang']] = $content_lang ? : $content;
			}
		}
		if($errors)
		{
			if(Tools::isSubmit('ajax'))
			{
				die(
				json_encode(
					array(
						'errors' => $this->displayError($errors)
					)
				)
				);
			}
			else
				$this->_html += $this->displayError($errors);

		}
		else{
			$success = '';
			if($template->id)
			{
				if($template->update())
					$success = $this->l('Updated successfully');
			}
			elseif($template->add())
				$success = $this->l('Added successfully');
			if($success)
			{
				$this->_clearCache('list_helper.tpl', $this->_getCacheId('ets_trans_chatgpt'));
				$this->_clearCache('form_chatgpt.tpl', $this->_getCacheId());
				if(Tools::isSubmit('ajax'))
				{
					die(
					json_encode(
						array(
							'success' => $success,
							'list' => $this->displayListTemplateChatGPT(),
						)
					)
					);
				}
				else
					$this->_html = $this->displayConfirmation($success);
			}
			else
			{
				$errors[] = $this->l('An error occurred while saving the template');
				if(Tools::isSubmit('ajax'))
				{
					die(
					json_encode(
						array(
							'errors' => $this->displayError($errors)
						)
					)
					);
				}
				else
					$this->_html += $this->displayError($errors);
			}
		}
	}

    public function saveDataAfterPause($transInfo)
    {
        $etsConfig = EtsTransConfig::getInstance();
        return $etsConfig->updatePauseData($transInfo);
    }

    public function deleteDataPause($pageType, $selectedTheme = 0)
    {
        $etsConfig = EtsTransConfig::getInstance();
        return $etsConfig->deletePauseData($pageType, '', $selectedTheme);
    }

    public function analyzeBeforeTranslate($pageType, $formData, $offset)
    {
	    $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
	    $idFeature = Tools::getIsset('idFeature') ? Tools::getValue('idFeature') : 0;
	    $isNewBlockreassurance = $this->isNewBlockreassurance();
	    $pcType = $this->getPcTypeValue();
        if($pageType == 'megamenu'){
            $result = EtsTransModule::analysisModuleMegamenu($formData);
        }
        else if($pageType == 'blog'){
            $result = EtsTransModule::analysisModuleBlog($formData);
        }
        else if($pageType == 'pc'){
            $result = EtsTransModule::analysisModulePc($formData, $pcType);
        }
        else
            $result = EtsTransPage::analysisTranslate($pageType, $formData, $offset, $idAttributeGroup, $idFeature, $isNewBlockreassurance);
        if($result){
            $result['total_item'] = EtsTransDefine::getInstance()->getTotalTranslate($pageType, $idAttributeGroup, $idFeature, $isNewBlockreassurance, $pcType);
        }
        return $result;
    }

    public function analyzeBeforeTranslateLz($pageType, $formData, $step, $selected, $sfType, $isLoadFile, $resetData)
    {
	    $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
	    $idFeature = Tools::getIsset('idFeature') ? (int)Tools::getValue('idFeature') : 0;
	    $pcType = $this->getPcTypeValue();
        $transSource = isset($formData['trans_source']) ? $formData['trans_source'] : '';
        $transTarget = isset($formData['trans_target']) ? $formData['trans_target'] : array();

        $errors = array();
        if(!$transTarget){
            $errors[] =  $this->l('The target language is required');
        }
        else if(in_array($transSource, $transTarget)){
            $errors = $this->l('The target language cannot contain the source language');
        }
        if($errors){
            return array(
                'errors' => $errors
            );
        }
        $result = EtsTransInternational::analysisBeforeTranslate($pageType, $formData, $step, $selected, $sfType, $isLoadFile, $resetData);
        if($result){
            $result['total_item'] = EtsTransDefine::getInstance()->getTotalTranslate($pageType, $idAttributeGroup, $idFeature, $this->isNewBlockreassurance(), $pcType);
        }
        return $result;
    }

    public function getFormConfigTransAll($isInterTrans = false, $resetTrans = false)
    {
        $ec = EtsTransConfig::getInstance();
        $pcType = $this->getPcTypeValue();
        $pageType = $isInterTrans ? 'inter' : 'all';
        if($ec->hasResumeData($pageType, $pcType)){
            if($resetTrans){
                $ec->deletePauseData($pageType, $pcType);
            }
            else{
	            if(!$this->isCached('parts/popup_alert_resume.tpl',$this->_getCacheId()))
	            {
		            $resumeData = $ec->getResumeData($pageType, $pcType);
		            $resumeData['total_translate'] = '';
		            $resumeData['page_type'] = $pageType;
		            if(!$resumeData['nb_path'])
			            $resumeData['nb_path'] = EtsTransAll::getTotalItemTransAll(null, $pageType);
		            $this->smarty->assign($resumeData);
	            }
                return $this->display(__FILE__, 'parts/popup_alert_resume.tpl', $this->_getCacheId());
            }

        }
	    $transWd = Configuration::get('ETS_TRANS_WD_CONFIG');
	    $isNewTemplate = (int)Tools::getValue('isNewTemplate');
        $paramsCache = [$pageType, $pcType, $transWd, $isNewTemplate];
        $cache_id = $this->_getCacheId($paramsCache);
        if (!$this->isCached('parts/popup_trans_all.tpl', $cache_id)) {
	        $configAutoEnable = Configuration::get('ETS_TRANS_AUTO_SETTING_ENABLED');
	        $target = $this->getListIdLangTarget();
	        if($configAutoEnable && $target){
		        $langTarget = array();
		        foreach ($target as $item){
			        $langTarget[] = Language::getLanguage($item);
		        }
	        }
	        $assigns = array(
		        'allLanguages' => $this->getLangWithFlagImage(true),
		        'transOptions' => $this->renderTransOptions(),
		        'idLangDefault' => $this->getLangDefault(),
		        'pageType' => $pageType,
		        'pcType' => $pcType,
		        'fieldTrans' => '',
		        'isTransAll' => 1,
		        'autoDetectLanguage' => $this->isAutoDetectLanguage(),
		        'configAutoEnable' => $configAutoEnable,
		        'hasGoogleApiKey' => Configuration::get('ETS_TRANS_GOOGLE_API_KEY') ? true : false,
		        'hasApiKey' => $this->getCurrentApiKey() ? true : false,
		        'apiType' => $this->getApiType(),
		        'totalTranslate' => $transWd ? 1 : 0,
		        'langSource' => Language::getLanguage($this->getIdLangSource()),
		        'langTarget' => isset($langTarget) ? $langTarget : array(),
		        'linkConfigApi' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
		        'enableAnalysis' => (int)Configuration::get('ETS_TRANS_ENABLE_ANALYSIS'),
		        'fieldTranslate' => Configuration::get('ETS_TRANS_FILED_TRANS'),
		        'pageId' => '',
		        'imgDir' => _PS_IMG_,
		        'ETS_TRANS_WD_CONFIG' => array(),
		        'isInterTrans' => $isInterTrans,
		        'treeWebTranslations' => $isInterTrans ? null : EtsTransDefine::getInstance()->treeWebPageSelection(),
		        'treeWebPageOption' => $isInterTrans ? $this->getConfigInterTrans() : array(),
		        'langTargetIds' => $configAutoEnable ? $target : array(),
		        'wdConfig' => $transWd,
		        'isLocalize' => in_array($pageType, array('theme', 'email', 'module', 'subject')),
		        'ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME'),
		        'ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME' => (int)Configuration::get('ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME'),
		        'ETS_TRANS_AUTO_GENERATE_LINK_REWRITE' => (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE'),
		        'isNewTemplate' => $isNewTemplate,
	        );
	        $this->smarty->assign($assigns);
        }
        return $this->display(__FILE__, 'parts/popup_trans_all.tpl', $cache_id);
    }

    public function getConfigInterTrans()
    {
        $treeData = EtsTransDefine::getInstance()->treeWebPageSelection();
        return array($treeData['inter']);
    }

    public function loadFileTranslateAll($formData, $pageType = 'all')
    {
        $errors = array();
        if(!isset($formData['trans_wd']) || $formData['trans_wd']){
            $errors[] = $this->l('The web data is required');
        }
        if(!isset($formData['trans_source']) || !$formData['trans_source']){
            $errors[] = $this->l('The source language is required');
        }
        if(!isset($formData['trans_target']) || !$formData['trans_target']){
            $errors[] = $this->l('The target language is required');
        }
        if(!isset($formData['trans_option']) || !$formData['trans_option']){
            $errors[] = $this->l('The field options are required');
        }
        if(!$errors){
            return array(
                'errors' => $errors,
            );
        }
        $etsTransAll = new EtsTransAll($pageType);
        if($result = $etsTransAll->loadFileTranslateAll($formData, $pageType)){
            return array(
                'errors' => false,
                'total_item' => isset($result['total_item']) ? $result['total_item'] : 0
            );
        }

    }

    public function translateAllWebData($formData, $pageType)
    {
        $errors = array();
        if(!isset($formData['trans_wd']) || $formData['trans_wd']){
             $errors[] = $this->l('The web data is required');
        }
        if(!isset($formData['trans_source']) || !$formData['trans_source']){
             $errors[] = $this->l('The source language is required');
        }
        if(!isset($formData['trans_target']) || !$formData['trans_target']){
             $errors[] = $this->l('The target language is required');
        }
        if(!isset($formData['trans_option']) || !$formData['trans_option']){
             $errors[] = $this->l('The field options are required');
        }
        if(!$errors){
            return array(
                'errors' => $errors,
            );
        }
        $etsTransAll = new EtsTransAll($pageType);
	    $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
        if($result = $etsTransAll->translateAllWebData($formData, $this->isNewBlockreassurance(), $idAttributeGroup)){
            if(isset($result['stop_translate']) && $result['stop_translate'] && !isset($result['stop_by'])){
	            $result['message'] = isset($result['message']) ? $result['message'] : $this->l('Translated successfully');
            }
            return $result;
        }
        return array(
            'errors' => $this->l('Cannot load files to translate'),
        );
    }

    public function isNewBlockreassurance() {
	    $moduleObj = Module::getInstanceByName('blockreassurance');
	    return (int)Tools::getValue('isNewBlockreassurance') || ($moduleObj && version_compare('4.0.0', $moduleObj->version, '<='));
    }

    public function analyzingAllPage($pageType, $formData, $offset, $isInit= false)
    {
	    $eta = new EtsTransAll($pageType);
        if($isInit){
            return $eta->indexDataTranslate($formData['trans_wd']);
        }
        else{
	        $pcType = $this->getPcTypeValue();
	        $idAttributeGroup = Tools::getIsset('idAttributeGroup') ? (int)Tools::getValue('idAttributeGroup') : 0;
	        $idFeature = Tools::getIsset('idFeature') ? (int)Tools::getValue('idFeature') : 0;

            return $eta->analysisBeforeTranslate($formData, $pcType, $offset, $idAttributeGroup, $idFeature, $this->isNewBlockreassurance());
        }
    }

    public function translateAllMegamenu($formData)
    {
        $result =  EtsTransModule::transAllMegamenu($formData);
        if(isset($result['stop']) && $result['stop']){
            $result['message'] = $this->l('Translate successfully');
        }
        return $result;
    }

    public function translateAllBlog($formData)
    {
        $result =  EtsTransModule::translateAllBlog($formData);
        if(isset($result['stop']) && $result['stop']){
            $result['message'] = $this->l('Translate successfully');
        }
        return $result;
    }

    public function translateAllModulePc($formData, $pcType)
    {
        $result = EtsTransModule::translateAllModulePc($formData, false, $pcType);

        if(isset($result['stop']) && $result['stop']){
            $result['message'] = isset($result['errors']) && $result['errors'] ? $result['message'] : $this->l('Translate successfully');
        }
        return $result;
    }

	public function displayListTemplateChatGPT()
	{
		$fields_list = array(
			'title' => array(
				'title' => $this->l('Label'),
				'type' => 'text',
				'sort' => false,
				'filter' => false,
			),
			'content' => array(
				'title' => $this->l('Content'),
				'type' => 'text',
				'sort' => false,
				'filter' => false,
			),
		);
		$totalRecords = (int)EtsTransChatGPT::countTemplatesWithFilter(false);
		$templates = EtsTransChatGPT::getTemplatesWithFilter(false, null, 0, false);

		$listData = array(
			'name' => 'ets_trans_chatgpt',
			'actions' => array('edit' ,'delete_gpt'),
			'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&module_name='.$this->name,
			'identifier' => 'id_ets_trans_chatgpt_template',
			'show_toolbar' => false,
			'show_action' => true,
			'title' => $this->l('Free Translate & AI Content Generator'),
			'fields_list' => $fields_list,
			'field_values' => $templates,
			'paggination' => '',
			'filter_params' => $this->getFilterParams($fields_list),
			'show_reset' => false,
			'totalRecords' => $totalRecords,
			'show_add_new' => true,
			'sort'=> '',
			'sort_type'=> '',
		);
		return $this->renderList($listData, $listData['name']);
	}

	public function getFilterParams($field_list)
	{
		$params = '';
		if($field_list)
		{
			foreach($field_list as $key => $val)
			{
				if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
				{
					$params .= '&'.$key.'='.urlencode($value);
				}
			}
			unset($val);
		}
		return $params;
	}

	public function renderList($listData, $cache_id = null)
	{
		if (!$cache_id && isset($listData['name']) && $listData['name'])
			$cache_id = $listData['name'];
		if(!$this->isCached('list_helper.tpl',$this->_getCacheId($cache_id)))
		{
			if(isset($listData['fields_list']) && $listData['fields_list'])
			{
				foreach($listData['fields_list'] as $key => &$val)
				{
					if(isset($val['filter']) && $val['filter'] && $val['type']=='int')
					{
						$val['active']['max'] =  trim(Tools::getValue($key.'_max'));
						$val['active']['min'] =  trim(Tools::getValue($key.'_min'));
					}
					elseif($key=='has_post' && !Tools::isSubmit('has_post'))
						$val['active']=1;
					else
						$val['active'] = trim(Tools::getValue($key));
				}
			}
			if (isset($listData['field_values']) && $listData['field_values']) {
				foreach ($listData['field_values'] as &$field_value) {
					$field_value['content'] = htmlentities($field_value['content']);
					$field_value['label'] = htmlentities($field_value['label']);
				}
			}
			$this->context->smarty->assign($listData);

		}
		return $this->display(__FILE__, 'list_helper.tpl', $this->_getCacheId($cache_id));
	}

	public function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$alt=null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
	{
		return EtsTransDefine::displayText($content, $tag, $class, $id, $href, $blank, $src, $alt, $name, $value, $type, $data_id_product, $rel, $attr_datas);
	}

	public function _getCacheId($params = null)
	{
		$cacheId = $this->getCacheId($this->name);
		$cacheId = str_replace($this->name, '', $cacheId);
		$suffix ='';
		if($params)
		{
			if(is_array($params))
				$suffix .= '|'.implode('|',$params);
			else
				$suffix .= '|'.$params;
		}
		return $this->name . $suffix . $cacheId;
	}

	public function _clearCache($template,$cache_id = null, $compile_id = null)
	{
		if ($cache_id === null) {
			$cache_id = $this->name;
		}
		if($template=='*')
		{
			return Tools::clearCache(Context::getContext()->smarty, null, $cache_id, $compile_id);
		}
		else
		{
            return Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
		}
	}

	public function installDefaultTemplates() {
		$data = [
			[
				'label' => $this->l('Write product description 1'),
				'content' => $this->l('I need an effective product description for our {product_name} that will convince customers it\'s the best choice for their needs.'),
			],
			[
				'label' => $this->l('Write product description 2'),
				'content' => $this->l('Write a 50-word product description for {product_name}. Write in an upbeat, informative tone.'),
			],
			[
				'label' => $this->l('Write product description 3'),
				'content' => $this->l('I must craft a compelling product description for our {product_name} highlighting its key features and benefits.'),
			],
			[
				'label' => $this->l('Translate product summary'),
				'content' => $this->l("Translate {product_summary} into {current_language}"),
			],
			[
				'label' => $this->l('Translate product description'),
				'content' => $this->l('What is the translation of {product_description} in {current_language}?'),
			]
		];
		$template = new EtsTransChatGPT();
		foreach ($data as $item) {
			$languages = Language::getLanguages(false);
			foreach($languages as $language)
			{
				$template->label[$language['id_lang']] = $item['label'];
				$template->content[$language['id_lang']] = $item['content'];
			}
			$template->add();
		}
	}
}