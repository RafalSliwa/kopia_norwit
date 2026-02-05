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

class EtsTransDefine{
    public $context;
	/**
	 * @var Ets_Translate
	 */
	public $module;
    public static $_region = 'us-east-2';
    public static $instance = null;

    public function __construct($module = null)
    {
        if (!(is_object($module)) || !$module) {
            $module = Module::getInstanceByName('ets_translate');
        }
        $this->module = $module;
        $context = Context::getContext();
        $this->context = $context;
    }

    public function l($string)
    {
        return Translate::getModuleTranslation('ets_translate', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

	/**
	 * @param null|string $content
	 * @param null|string $tag
	 * @param null|string $class
	 * @param null|string|int $id
	 * @param null|string $href
	 * @param bool|string $blank
	 * @param null|string $src
	 * @param null|string $alt
	 * @param null|string $name
	 * @param null|string|int $value
	 * @param null|string $type
	 * @param null|string|int $data_id_product
	 * @param null|string $rel
	 * @param null|array $attr_datas
	 * @return string
	 */
	public static function displayText($content=null,$tag=null,$class=null,$id=null,$href=null,$blank=false,$src = null,$alt = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null) {
		$text ='';
		if($tag)
		{
			$text .= '<'.$tag.($class ? ' class="'.$class.'"':'').($id ? ' id="'.$id.'"':'');
			if($href)
				$text .=' href="'.$href.'"';
			if($blank && $tag ='a')
				$text .=' target="_blank"';
			if($src)
				$text .=' src ="'.$src.'"';
			if($name)
				$text .=' name="'.$name.'"';
			if($value)
				$text .=' value ="'.$value.'"';
			if($type)
				$text .= ' type="'.$type.'"';
			if($data_id_product)
				$text .=' data-id_product="'.(int)$data_id_product.'"';
			if($rel)
				$text .=' rel="'.$rel.'"';
			if($alt)
				$text .=' alt="'.$alt.'"';
			if($attr_datas)
			{
				foreach($attr_datas as $data)
				{
					$text .=' '.$data['name'].'='.'"'.$data['value'].'"';
				}
			}
			if($tag=='img' || $tag=='br' || $tag=='input')
				$text .='/>';
			else
				$text .='>';
			if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
				$text .= $content;
			if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
				$text .= '<'.'/' . $tag . '>';
		}
		return $text;
	}

	/**
	 * @return array[]
	 */
    public function getPageAppendContextWords() {
	    $pages = array(
		    array(
			    'title' => $this->l('Catalog (products, categories, features and attributes, etc.)'),
			    'value' => 'catalog',
		    ),
		    array(
			    'title' => $this->l('CMS Pages and categories'),
			    'value' => 'page',
		    ),
		    array(
			    'title' => $this->l('International / Translations (texts in tpl or php files and email templates)'),
			    'value' => 'inter',
		    ),
	    );
	    if(Module::isInstalled('ybc_blog')){
		    $pages[] = array(
			    'title' => $this->l('Blog'),
			    'value' => 'blog'
		    );
	    }
	    if(Module::isInstalled('ets_megamenu')){
		    $pages[] = array(
			    'title' => $this->l('Mega Menu Pro'),
			    'value' => 'megamenu'
		    );
	    }
	    if(Module::isInstalled('ets_reviews')){
		    $pages[] = array(
			    'title' => $this->l('Trusted Reviews'),
			    'value' => 'pc'
		    );
	    }
	    if(Module::isInstalled('blockreassurance')){
		    $pages[] = array(
			    'title' => $this->l('Customer Reassurance'),
			    'value' => 'blockreassurance'
		    );
	    }
	    if(Module::isInstalled('ps_linklist')){
		    $pages[] = array(
			    'title' => $this->l('Link widget (footer menu)'),
			    'value' => 'ps_linklist'
		    );
	    }
	    if(Module::isInstalled('ps_mainmenu')){
		    $pages[] = array(
			    'title' => $this->l('Main menu (top menu)'),
			    'value' => 'ps_mainmenu'
		    );
	    }
	    if(Module::isInstalled('ps_customtext')){
		    $pages[] = array(
			    'title' => $this->l('Custom text blocks'),
			    'value' => 'ps_customtext'
		    );
	    }
	    if(Module::isInstalled('ps_imageslider')){
		    $pages[] = array(
			    'title' => $this->l('Image slider on home page'),
			    'value' => 'ps_imageslider'
		    );
	    }
	    if(Module::isInstalled('ets_extraproducttabs')){
		    $pages[] = array(
			    'title' => str_replace('&amp;', '&', $this->l('Custom Fields & Tabs On Product Page')),
			    'value' => 'ets_extraproducttabs'
		    );
	    }
	    return $pages;
    }

    public function renderBtnCheckApi($label = 'Check API', $class = '') {
		return self::displayText($this->l($label), 'button', 'btn btn-default btn-sm ets-trans-btn-check-api js-ets-trans-check-api ' . $class);
    }

    public function getConfigs($isInForm, $apiType, $defaultLangSource, $defaultLangTarget, $enableAutoSetting, $transOption, $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD) {
	    $linkText = $this->l('How to get API key?');
		$linkDescGoogle = $linkText;
		$linkDescBing = $linkText;
		$linkDescDeepL = $linkText;
		$linkDescLecto = $linkText;
		$linkDescLibre = $linkText;
		$linkDescYandex = $linkText;
		if ($isInForm) {
			$btnCheckApi = $this->renderBtnCheckApi();
			$linkDescGoogle = $btnCheckApi;
			$linkDescGoogle .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_GOOGLE_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescGoogle .= ' ' . $this->l('Make sure you have enabled "Cloud Translation API" for the project associated with this key');

			$linkDescBing = $btnCheckApi;
			$linkDescBing .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_BING_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescBing .= ' ' . $this->l('Make sure you have enabled "Bing Translation API" for the project associated with this key');

			$linkDescDeepL = $btnCheckApi;
			$linkDescDeepL .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_DEEPL_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescDeepL .= ' ' . $this->l('Make sure you have enabled "DeepL Translation API" for the project associated with this key');

			$linkDescLecto = $btnCheckApi;
			$linkDescLecto .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_LECTO_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescLecto .= ' ' . $this->l('Make sure you have enabled "Lecto Translation API" for the project associated with this key');

			$linkDescLibre = $btnCheckApi;
			$linkDescLibre .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_LIBRE_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescLibre .= ' ' . $this->l('Make sure you have enabled "Libre Translation API" for the project associated with this key');

			$linkDescYandex = $btnCheckApi;
			$linkDescYandex .= self::displayText($linkText, 'a', '', '', $this->getLinkDescription(EtsTransApi::$_YANDEX_API_TYPE), '_blank', '', '', '', '', '', '', 'noreferrer');
			$linkDescYandex .= ' ' . $this->l('Make sure you have enabled "Yandex Translation API" for the project associated with this key');
		}
    	return array(
		    'ETS_TRANS_SELECT_API' => array(
			    'name' => 'ETS_TRANS_SELECT_API',
			    'label' => $this->l('Select translation provider'),
			    'type' => 'text',
			    'default' => EtsTransApi::$_GOOGLE_API_TYPE,
			    'desc' => $this->l(''),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_DEEPL_API_KEY' => array(
			    'name' => 'ETS_TRANS_DEEPL_API_KEY',
			    'label' => $this->l('DeepL API key'),
			    'desc' => $isInForm ? $linkDescDeepL : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'type' => 'text',
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_DEEPL_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_DEEPL_API_TYPE ? 'hide' : ''),
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_DEEPL_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The DeepL translate API key is required'),
				    'validate' => $this->l('The DeepL translate API key must be a string'),
				    'isApiKey' => $this->l('The DeepL translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_BING_API_KEY' => array(
			    'name' => 'ETS_TRANS_BING_API_KEY',
			    'label' => $this->l('Bing API key for "Azure Translation API"'),
			    'desc' => $isInForm ? $linkDescBing : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_BING_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_BING_API_TYPE ? 'hide' : ''),
			    'type' => 'text',
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_BING_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The Bing translate API key is required'),
				    'validate' => $this->l('The Bing translate API key must be a string'),
				    'isApiKey' => $this->l('The Bing translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_GOOGLE_API_KEY' => array(
			    'name' => 'ETS_TRANS_GOOGLE_API_KEY',
			    'label' => $this->l('Google API key for "Cloud Translation API"'),
			    'desc' => $isInForm ? $linkDescGoogle : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_GOOGLE_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_GOOGLE_API_TYPE ? 'hide' : ''),
			    'type' => 'text',
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_GOOGLE_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The Google translate API key is required'),
				    'validate' => $this->l('The Google translate API key must be a string'),
				    'isApiKey' => $this->l('The Google translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_LIBRE_API_KEY' => array(
			    'name' => 'ETS_TRANS_LIBRE_API_KEY',
			    'label' => $this->l('Libre API key for "Cloud Translation API"'),
			    'desc' => $isInForm ? $linkDescLibre : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_LIBRE_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_LIBRE_API_TYPE ? 'hide' : ''),
			    'type' => 'text',
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_LIBRE_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The Libre translate API key is required'),
				    'validate' => $this->l('The Libre translate API key must be a string'),
				    'isApiKey' => $this->l('The Libre translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_LECTO_API_KEY' => array(
			    'name' => 'ETS_TRANS_LECTO_API_KEY',
			    'label' => $this->l('Lecto API key for "Cloud Translation API"'),
			    'desc' => $isInForm ? $linkDescLecto : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_LECTO_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_LECTO_API_TYPE ? 'hide' : ''),
			    'type' => 'text',
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_LECTO_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The Lecto translate API key is required'),
				    'validate' => $this->l('The Lecto translate API key must be a string'),
				    'isApiKey' => $this->l('The Lecto translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_YANDEX_API_KEY' => array(
			    'name' => 'ETS_TRANS_YANDEX_API_KEY',
			    'label' => $this->l('Yandex API key for "Translation API"'),
			    'desc' => $isInForm ? $linkDescYandex : $linkText,
			    'validate' => 'isString',
			    'required' => true,
			    'form_group_class' => 'ets-trans-api-group ets-trans-api-group_' . EtsTransApi::$_YANDEX_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_YANDEX_API_TYPE ? 'hide' : ''),
			    'type' => 'text',
			    'isApiKey' => true,
			    'api_type' => EtsTransApi::$_YANDEX_API_TYPE,
			    'error_message' => array(
				    'required' => $this->l('The Yandex translate API key is required'),
				    'validate' => $this->l('The Yandex translate API key must be a string'),
				    'isApiKey' => $this->l('The Yandex translate API key is invalid'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_AUTO_SETTING_ENABLED' => array(
			    'name' => 'ETS_TRANS_AUTO_SETTING_ENABLED',
			    'label' => $this->l('Auto apply global settings when translating'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('If this option is turned off, you will be required to specify your preferred translation options every time you translate'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_LANG_SOURCE' => array(
			    'name' => 'ETS_TRANS_LANG_SOURCE',
			    'label' => $this->l('Translate from'),
			    'type' => 'text',
			    'form_group_class' => 'ets-trans-auto-setting-group '.(!$enableAutoSetting ? 'hide' : ''),
			    'default' => $defaultLangSource,
			    'error_message' => array(
				    'required' => $this->l('The "Translate from" field is required'),
			    ),
			    'desc' => $this->l('This is source language that will be used to translate into destination languages selected below'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_LANG_TARGET' => array(
			    'name' => 'ETS_TRANS_LANG_TARGET',
			    'label' => $this->l('Translate into'),
			    'type' => 'text',
			    'form_group_class' => 'ets-trans-auto-setting-group ets-trans-target-lang-config '.(!$enableAutoSetting ? 'hide' : ''),
			    'multiple' => true,
			    'default' => implode(',', $defaultLangTarget),
			    'error_message' => array(
				    'required' => $this->l('The "Translate into" field is required'),
			    ),
			    'desc' => $this->l('These are destination languages to apply translation, they will be translated from the source language. Translation will only be performed for languages selected here'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_AUTO_DETECT_LANG' => array(
			    'name' => 'ETS_TRANS_AUTO_DETECT_LANG',
			    'label' => $this->l('Auto detect language when translating'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('The detected language of customer comments.'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_FILED_TRANS' => array(
			    'name' => 'ETS_TRANS_FILED_TRANS',
			    'label' => $this->l('How to translate?'),
			    'type' => 'radio',
			    'form_group_class' => 'ets-trans-auto-setting-group '.(!$enableAutoSetting ? 'hide' : ''),
			    'values' => isset($transOption) ? $transOption : array(),
			    'default' => 'both',
			    'error_message' => array(
				    'required' => $this->l('The "How to translate?" field is required'),
			    ),
			    'tab' => 'setting'
		    ),

		    'ETS_TRANS_AUTO_GENERATE_LINK_REWRITE' => array(
			    'name' => 'ETS_TRANS_AUTO_GENERATE_LINK_REWRITE',
			    'label' => $this->l('Regenerate friendly URL when translating titles'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('Friendly URL will be generated based on the translated text when titles such as product name, category name, CMS page title, etc. are translated'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_ANALYSIS' => array(
			    'name' => 'ETS_TRANS_ENABLE_ANALYSIS',
			    'label' => $this->l('Analyze the translation before translating'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('You will be noticed how many characters you are going to translate and an estimated cost you will pay Google for the translation'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_IGNORE_OLD_CONTROLLER' => array(
			    'name' => 'ETS_TRANS_IGNORE_OLD_CONTROLLER',
			    'label' => $this->l('Ignore old controller files when translating'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('Old controller files are not used in PrestaShop 1.7 however lot of them still exists, we do not need to translate text in those files.'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_TRANS_FIELD' => array(
			    'name' => 'ETS_TRANS_ENABLE_TRANS_FIELD',
			    'label' => $this->l('Enable field translation'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('You will see a "Translate" icon besides every input field that allows you translate the content inside the field'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_KEY_PHRASE' => array(
			    'name' => 'ETS_TRANS_ENABLE_KEY_PHRASE',
			    'label' => $this->l('Fixed translation for some special words'),
			    'type' => 'switch',
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'tab' => 'exception',
			    'desc' => $this->l('The option to translate with fixed meaning only applies if the phrase to be translated stands alone'),
		    ),
		    'ETS_TRANS_KEY_PHRASE_FROM' => array(
			    'name' => 'ETS_TRANS_KEY_PHRASE_FROM',
			    'id' => 'ETS_TRANS_KEY_PHRASE_FROM',
			    'label' => $this->transJs()['label_phrase_key'],
			    'required' => true,
			    'type' => 'text',
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_KEY_PHRASE_TO' => array(
			    'name' => 'ETS_TRANS_KEY_PHRASE_TO',
			    'label' => $this->transJs()['label_translate_to'],
			    'id' => 'ETS_TRANS_KEY_PHRASE_TO',
			    'type' => 'text',
			    'lang' => true,
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_PHRASE_ALONE' => array(
			    'name' => 'ETS_TRANS_PHRASE_ALONE',
			    'label' => $this->l('The fixed translation is applied if the phrase to be translated stands alone.'),
			    'type' => 'switch',
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'default' => 1,
			    'tab' => 'exception',
			    'form_group_class' => 'ets_trans_phrase_alone  js-ets_trans_phrase_alone js-ets_trans_key_phrase ' .( !$this->module->isEnablePhraseKey() ? 'hide' : '')
		    ),
		    'ETS_TRANS_EXCLUDE_WORDS' => array(
			    'name' => 'ETS_TRANS_EXCLUDE_WORDS',
			    'label' => $this->l('Excluded words (or phrases)'),
			    'default' => '',
			    'type' => 'textarea',
			    'col' => 4,
			    'rows'=> 5,
			    'desc' => $this->l('These words or phrases will not be translated. Each word or phrase on a line. However, this option does not support Lecto, Libre and Yandex translate'),
			    'tab' => 'exception'
		    ),

		    'ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD' => array(
			    'name' => 'ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD',
			    'label' => $this->l('Append contextual words when translating'),
			    'type' => 'switch',
			    'default' => 0,
			    'values' => array(
				    array(
					    'id' => 'ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD_1',
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'id' => 'ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD_0',
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_CONTEXT_WORDS' => array(
			    'name' => 'ETS_TRANS_CONTEXT_WORDS',
			    'label' => $this->l('Append contextual words to improve translations'),
			    'type' => 'textarea',
			    'lang' => true,
			    'required' => true,
			    'col' => 6,
			    'rows' => 5,
			    'default' => 'data, database, programming, computer, technology',
			    'form_group_class' => 'ets-trans-append-context-word'.(isset($ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD) && $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD ? '': ' hide'),
			    'error_message' => array(
				    'required' => $this->l('The context words are required'),
			    ),
			    'desc' => $this->l('These words help Google understands the context of the translation better, especially when the text to translate is too short (has only one or a few words). These words will be removed from the translation result'),
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_PAGE_APPEND_CONTEXT_WORD' => array(
			    'name' => 'ETS_TRANS_PAGE_APPEND_CONTEXT_WORD',
			    'label' => $this->l('Pages to append contextual words when translating'),
			    'type' => 'text',
			    'required' => true,
			    'form_group_class' => 'ets-trans-append-context-word'.(isset($ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD) && $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD ? '': ' hide'),
			    'default' => 'inter',
			    'error_message' => array(
				    'required' => $this->l('The pages to append context words are required'),
			    ),
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_MAX_WORD_APPEND_CONTEXT_WORD' => array(
			    'name' => 'ETS_TRANS_MAX_WORD_APPEND_CONTEXT_WORD',
			    'label' => $this->l('Append contextual words when original text to translate fewer than (or equals)'),
			    'type' => 'text',
			    'col' => 2,
			    'suffix' => $this->l('words'),
			    'validate' => 'isUnsignedInt',
			    'default' => 10,
			    'form_group_class' => 'ets-trans-append-context-word'.(isset($ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD) && $ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD ? '': ' hide'),
			    'error_message' => array(
				    'validate' => $this->l('The "Append contextual words when original text to translate fewer than (or equals)" is invalid'),
			    ),
			    'desc' => $this->l('Leave blank to append contextual words to every text when translating'),
			    'tab' => 'exception'
		    ),
		    'ETS_TRANS_RATE_GOOGLE' => array(
			    'name' => 'ETS_TRANS_RATE_GOOGLE',
			    'label' => $this->l('Google translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_GOOGLE', $this->module->getSuffixRateApi(EtsTransApi::$_GOOGLE_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_GOOGLE_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_GOOGLE_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_GOOGLE_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 500.000 free characters every month and Google also offers 12-month and $300 free trial! For more see').' '.self::displayText($this->l('Google Translation API pricing'), 'a', '', '', 'https://cloud.google.com/translate/pricing?hl=en_US', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The Google translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_RATE_BING' => array(
			    'name' => 'ETS_TRANS_RATE_BING',
			    'label' => $this->l('Bing translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_BING', $this->module->getSuffixRateApi(EtsTransApi::$_BING_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_BING_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_BING_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_BING_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 2.000.000 free characters every month! For more see').' '.self::displayText($this->l('Bing Translation API pricing'), 'a', '', '', 'https://azure.microsoft.com/en-us/pricing/details/cognitive-services/translator', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The Bing translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_RATE_DEEPL' => array(
			    'name' => 'ETS_TRANS_RATE_DEEPL',
			    'label' => $this->l('DeepL translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_DEEPL', $this->module->getSuffixRateApi(EtsTransApi::$_DEEPL_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_DEEPL_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_DEEPL_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_DEEPL_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 500.000 free characters every month! For more see').' '.self::displayText($this->l('DeepL Translation API pricing'), 'a', '', '', 'https://www.deepl.com/pro?cta=header-prices#team', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The DeepL translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_RATE_LIBRE' => array(
			    'name' => 'ETS_TRANS_RATE_LIBRE',
			    'label' => $this->l('Libre translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_LIBRE', $this->module->getSuffixRateApi(EtsTransApi::$_LIBRE_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_LIBRE_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_LIBRE_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_LIBRE_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 500.000 free characters every month! For more see').' '.self::displayText($this->l('Libre Translation API pricing'), 'a', '', '', 'https://portal.libretranslate.com/', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The Libre translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_RATE_LECTO' => array(
			    'name' => 'ETS_TRANS_RATE_LECTO',
			    'label' => $this->l('Lecto translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_LECTO', $this->module->getSuffixRateApi(EtsTransApi::$_LECTO_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_LECTO_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_LECTO_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_LECTO_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 500.000 free characters every month! For more see').' '.self::displayText($this->l('Lecto Translation API pricing'), 'a', '', '', 'https://dashboard.lecto.ai/pricing', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The Lecto translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_RATE_YANDEX' => array(
			    'name' => 'ETS_TRANS_RATE_YANDEX',
			    'label' => $this->l('Yandex translation pricing (per one million characters)'),
			    'suffix' => self::displayText('', 'input', 'form-control input-suffix', '', '', '', '', '', 'ETS_TRANS_SUFFIX_RATE_YANDEX', $this->module->getSuffixRateApi(EtsTransApi::$_YANDEX_API_TYPE), 'text'),
			    'form_group_class' => 'ets-trans-rate-setting ets-trans-api-group_' . EtsTransApi::$_YANDEX_API_TYPE . ' ets-trans-rate-setting_' . EtsTransApi::$_YANDEX_API_TYPE . ' js-ets-trans-api-group ' . ($apiType != EtsTransApi::$_YANDEX_API_TYPE ? 'hide' : ''),
			    'validate' => 'isUnSignedFloat',
			    'type' => 'text',
			    'desc' => $this->l('Don\'t panic! You have 500.000 free characters every month! For more see').' '.self::displayText($this->l('Yandex Translation API pricing'), 'a', '', '', 'https://translate.yandex.com/developers/offer/prices', '_blank', '', '', '', '', '', '', 'noreferrer').'. '.$this->l('This value is used to estimate your expense when translating your website content. Leave blank if you do not want to see estimated cost for your translation. '),
			    'error_message' => array(
				    'validate' => $this->l('The Yandex translation pricing must be a decimal number'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_GOOGLE' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_GOOGLE',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The Google translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_BING' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_BING',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The Bing translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_DEEPL' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_DEEPL',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The DeepL translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_LIBRE' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_LIBRE',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The Libre translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_LECTO' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_LECTO',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The Lecto translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_SUFFIX_RATE_YANDEX' => array(
			    'name' => 'ETS_TRANS_SUFFIX_RATE_YANDEX',
			    'label' => '',
			    'default' => 'USD',
			    'type' => 'text',
			    'is_suffix_rate' => true,
			    'required' => true,
			    'error_message' => array(
				    'required' => $this->l('The Yandex translation rate unit is required'),
			    ),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_LOG' => array(
			    'name' => 'ETS_TRANS_ENABLE_LOG',
			    'label' => $this->l('Enable translation log'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'id' => 'ETS_TRANS_ENABLE_LOG_1',
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'id' => 'ETS_TRANS_ENABLE_LOG_0',
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('Log all API requests to Google, recommended for development purpose only.') . ' ' . self::displayText($this->l('View translation log here'), 'a', '', '', $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name.'&viewTranslateLog=1'),
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_TRANSLATE_TICKET' => array(
			    'name' => 'ETS_TRANS_ENABLE_TRANSLATE_TICKET',
			    'label' => $this->l('Translate tickets'),
			    'type' => 'switch',
			    'default' => 1,
			    'values' => array(
				    array(
					    'id' => 'ETS_TRANS_ENABLE_TRANSLATE_TICKET_1',
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'id' => 'ETS_TRANS_ENABLE_TRANSLATE_TICKET_0',
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => '',
			    'tab' => 'setting'
		    ),
		    'ETS_TRANS_ENABLE_CHATGPT' => array(
			    'name' => 'ETS_TRANS_ENABLE_CHATGPT',
			    'label' => $this->l('Enable ChatGPT'),
			    'type' => 'switch',
			    'default' => 0,
			    'values' => array(
				    array(
					    'value' => 1,
					    'label' => $this->l('Yes'),
				    ),
				    array(
					    'value' => 0,
					    'label' => $this->l('No'),
				    ),
			    ),
			    'desc' => $this->l('Enable ChatGPT to create or edit content.'),
			    'tab' => 'chatgpt'
		    ),
		    'ETS_TRANS_CHATGPT_API' => array(
			    'name' => 'ETS_TRANS_CHATGPT_API',
			    'label' => $this->l('ChatGPT API'),
			    'type' => 'text',
			    'validate' => 'isString',
			    'required' => true,
			    'isChatGptAPI' => true,
			    'form_group_class' => 'ets-trans-chatgpt-api ets-trans-toggle-parent-enable-chatgpt'.($this->module->isEnableChatGPT() ? '': ' hide'),
			    'error_message' => array(
				    'required' => $this->l('The ChatGPT API key is required'),
				    'validate' => $this->l('The ChatGPT API key must be a string'),
				    'isApiKey' => $this->l('The ChatGPT API key is invalid'),
			    ),
			    'desc' => $isInForm ? $this->renderBtnCheckApi('Check ChatGPT API', 'btn-check-gpt-api js-btn-check-gpt-api') . self::displayText($linkText, 'a', '', '', 'https://platform.openai.com/account/api-keys', '_blank', '', '', '', '', '', '', 'noreferrer') . ' ' . $this->l('Make sure you have enabled "ChatGPT API" for the project associated with this key') : $this->l('How to get API key?'),
			    'tab' => 'chatgpt'
		    ),
		    'ETS_TRANS_CHATGPT_TEMPLATES' =>array(
			    'name' => 'ETS_TRANS_CHATGPT_TEMPLATES',
			    'label'=> '',
			    'type'=>'text',
			    'tab' => 'chatgpt',
			    'required' => false,
			    'form_group_class' => 'ets-trans-chatgpt-templates chatgpt-templates-list ets-trans-toggle-parent-enable-chatgpt' . ($this->module->isEnableChatGPT() ? '': ' hide'),
			    'ignore' => true
		    ),
	    );
    }


    public function display($template)
    {
        if (!$this->module)
            return;
        return $this->module->display($this->module->getLocalPath(), $template);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EtsTransDefine();
        }
        return self::$instance;
    }

    public function installDb()
    {
        $tblLog = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_trans_log` (
            `id_ets_trans_log` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_session` VARCHAR(20) DEFAULT NULL,
            `page_type` VARCHAR(20) NOT NULL,
            `lang_source` VARCHAR(20) NOT NULL,
            `lang_target` VARCHAR(191) NOT NULL,
            `ids_translated` VARCHAR(191) DEFAULT NULL,
            `text_translated` TEXT DEFAULT NULL,
            `status` TINYINT(1) DEFAULT NULL,
            `res_message` TEXT DEFAULT NULL,
            `timeout` INT(10) DEFAULT NULL,
            `date_add` DATETIME DEFAULT NULL,
            `id_shop` INT(10) NOT NULL,
            PRIMARY KEY (`id_ets_trans_log`),
            INDEX (`id_shop`, `status`, `page_type`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";

        $tblCache = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_trans_cache` (
            `id_ets_trans_cache` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cache_type` VARCHAR(50) NOT NULL,
            `name` VARCHAR(50) DEFAULT NULL,
            `file_path` TEXT DEFAULT NULL,
            `file_type` VARCHAR(20) DEFAULT NULL,
            `nb_translated` INT(10) DEFAULT NULL,
            `status` TINYINT(1) DEFAULT NULL,
            `is_oneclick` TINYINT(1) DEFAULT 0,
            `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_upd` DATETIME DEFAULT NULL,
            `id_shop` INT(10) NOT NULL,
            PRIMARY KEY (`id_ets_trans_cache`),
            INDEX (`cache_type`, `name`, `status`,`id_shop`,`is_oneclick`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";

        $tblLogRequest = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ets_trans_log_request` (
            `id_ets_trans_log_request` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `page_type` VARCHAR(20) DEFAULT NULL,
            `lang_source` VARCHAR(20) NOT NULL,
            `lang_target` VARCHAR(20) NOT NULL,
            `text_translated` TEXT DEFAULT NULL,
            `text_response` TEXT DEFAULT NULL,
            `status` TINYINT(1) DEFAULT NULL,
            `character_count` INT(10) UNSIGNED NOT NULL,
            `api_type` VARCHAR(20) NOT NULL,
            `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_upd` DATETIME DEFAULT NULL,
            `deleted_at` DATETIME DEFAULT NULL,
            `message` TEXT DEFAULT NULL,
            `id_shop` INT(10) NOT NULL,
            PRIMARY KEY (`id_ets_trans_log_request`),
            INDEX (`id_shop`, `page_type`, `status`,`api_type`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";

        $tblChatGPTMessage = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_message` (
			`id_ets_trans_chatgpt_message` INT(11) NOT NULL AUTO_INCREMENT , 
			`is_chatgpt` INT(1) NOT NULL ,
			`message` text,
			`apply_for` VARCHAR(222) DEFAULT NULL,
			`date_add` datetime,
            PRIMARY KEY (`id_ets_trans_chatgpt_message`),
            INDEX(`is_chatgpt`)
        ) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

	    $tblChatGPTTemp ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_template` (
         `id_ets_trans_chatgpt_template` INT(11) NOT NULL AUTO_INCREMENT , 
         `position` INT(11) NOT NULL ,
        PRIMARY KEY (`id_ets_trans_chatgpt_template`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';

	    $tblChatGPTTemplateLang ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_trans_chatgpt_template_lang` (
         `id_ets_trans_chatgpt_template` INT(11) NOT NULL , 
         `id_lang` INT(11) NOT NULL , 
         `label` text ,
         `content` text ,
        PRIMARY KEY (`id_ets_trans_chatgpt_template`,`id_lang`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';


        return Db::getInstance()->execute($tblLog)
            && Db::getInstance()->execute($tblCache)
            && Db::getInstance()->execute($tblChatGPTMessage)
            && Db::getInstance()->execute($tblChatGPTTemp)
            && Db::getInstance()->execute($tblChatGPTTemplateLang)
            && Db::getInstance()->execute($tblLogRequest);
    }

    public function uninstallDb()
    {
        return Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_log`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_log_request`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_chatgpt_message`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_chatgpt_template`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_chatgpt_template_lang`")
            && Db::getInstance()->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ets_trans_cache`");
    }

    public function configTransAllWensite()
    {
        return array(
            'ETS_TRANS_WD_CONFIG' => array(
                'name' => 'ETS_TRANS_WD_CONFIG',
                'label' => '',
                'is_array' => true,
                'type' => 'text',
            )
        );
    }

    public function getTabsSetting($tab_active = 'setting') {
    	return [
    	    'setting' => [
    	    	'label' => $this->l('Translate settings'),
		        'active' => $tab_active == 'setting'
	        ],
    	    'exception' => [
    	    	'label' => $this->l('Exceptions'),
		        'active' => $tab_active == 'exception'
	        ],
    	    'chatgpt' => [
    	    	'label' => $this->l('ChatGPT'),
		        'active' => $tab_active == 'chatgpt'
	        ],
	    ];
    }

    public function treeWebPageSelection()
    {
        $modules = EtsTransCore::getAllModules();
        $themes = EtsTransCore::getAllThemes();
        $emails = EtsTransInternational::getEmailTemplate(null, true);
        $pagesTrans = array(
            'all' => array(
                'title' => $this->l('All (translate everything)'),
                'name' => 'wd_all',
            ),
            'catalog' => array(
                'title' => $this->l('Catalog'),
                'name' => 'catalog_all',
                'items' => array(
                    'product' => array(
                        'title' => $this->l('Products'),
                        'name' => 'catalog_product',
                    ),
                    'category' => array(
                        'title' =>$this->l('Categories'),
                        'name' => 'catalog_category',
                    ),
                    'manufacturer' => array(
                        'title' => $this->l('Brands'),
                        'name' => 'catalog_manufacturer',
                    ),
                    'supplier' => array(
                        'title' => $this->l('Suppliers'),
                        'name' => 'catalog_supplier',
                    ),
                    'attribute' => array(
                        'title' => $this->l('Attributes'),
                        'name' => 'catalog_attribute',
                    ),
                    'attribute_group' => array(
                        'title' => $this->l('Attribute groups'),
                        'name' => 'catalog_attribute_group',
                    ),
                    'feature' => array(
                        'title' => $this->l('Features'),
                        'name' => 'catalog_feature',
                    ),
                    'feature_value' => array(
                        'title' => $this->l('Feature values'),
                        'name' => 'catalog_feature_value',
                    ),
                )
            ),
            'page' => array(
                'title' => $this->l('Pages'),
                'name' => 'page_all',
                'items' => array(
                    'cms_category' => array(
                        'title' => $this->l('CMS categories'),
                        'name' => 'page_cms_category',
                    ),
                    'cms' => array(
                        'title' => $this->l('CMS pages'),
                        'name' => 'page_cms',
                    ),
                )
            ),
            'inter' => array(
                'title' => $this->l('International / Translations'),
                'name' => 'inter_all',
                'items' => array(
                    'back' => array(
                        'title' => $this->l('Back office'),
                        'name' => 'inter_back',
                    ),
                    'theme' => array(
                        'title' => $this->l('Themes'),
                        'name' => 'inter_theme',
                        'items' => EtsTransCore::setNameForAllThemes($themes, 'inter_theme_'),
                    ),
                    'module' => array(
                        'title' => $this->l('Installed modules'),
                        'name' => 'inter_module',
                        'items' => EtsTransCore::setNameForAllModules($modules, 'inter_module_'),
                    ),
                    'email' => array(
                        'title' => $this->l('Email'),
                        'name' => 'inter_email',
                        'items' => array(
                            'subject' => array(
                                'title' => $this->l('Subjects'),
                                'name' => 'inter_email_subject',
                            ),
                            'body' => array(
                                'title' => $this->l('Body'),
                                'name' => 'inter_email_body',
                                'items' => array(
                                    'core' =>array(
                                        'title' => $this->l('Core'),
                                        'name' => 'inter_email_body_core',
                                        'emails' => EtsTransCore::setValForEmail($emails, 'inter_email_body_core_'),
                                    ),
                                    'theme' =>array(
                                        'title' => $this->l('Theme'),
                                        'name' => 'inter_email_body_theme',
                                        'items' => EtsTransCore::setEmailForAllThemes(EtsTransCore::setNameForAllThemes($themes, 'inter_email_body_theme_'), $emails),
                                    ),
                                )
                            ),
                        )
                    ),
                    'others' => array(
                        'title' => $this->l('Static pages (pages in SEO settings tab such as homepage, login, my account, etc.)'),
                        'name' => 'inter_other',
                    ),
                )
            ),
        );
        if(Module::isInstalled('ybc_blog')){
            $pagesTrans['blog'] = array(
                'title' => $this->l('Blog'),
                'name' => 'blog_all',
                'items' => array(
                    'blog_post' => array(
                        'title' => $this->l('Blog posts'),
                        'name' => 'blog_post'
                    ),
                    'blog_category' => array(
                        'title' => $this->l('Blog categories'),
                        'name' => 'blog_category'
                    ),
                )
            );
        }
        if(Module::isInstalled('ets_megamenu')){
            $pagesTrans['megamenu'] = array(
                'title' => $this->l('Mega Menu Pro'),
                'name' => 'megamenu'
            );
        }
        if(Module::isInstalled('ets_reviews')){
            $pagesTrans['pc'] = array(
                'title' => $this->l('Trusted Reviews'),
                'name' => 'pc'
            );
        }
        if(Module::isInstalled('blockreassurance')){
            $pagesTrans['blockreassurance'] = array(
                'title' => $this->l('Customer Reassurance'),
                'name' => 'blockreassurance'
            );
        }
        if(Module::isInstalled('ps_linklist')){
            $pagesTrans['ps_linklist'] = array(
                'title' => $this->l('Link widget (footer menu)'),
                'name' => 'ps_linklist'
            );
        }
        if(Module::isInstalled('ps_mainmenu')){
            $pagesTrans['ps_mainmenu'] = array(
                'title' => $this->l('Main menu (top menu)'),
                'name' => 'ps_mainmenu'
            );
        }
        if(Module::isInstalled('ps_customtext')){
            $pagesTrans['ps_customtext'] = array(
                'title' => $this->l('Custom text blocks'),
                'name' => 'ps_customtext'
            );
        }
        if(Module::isInstalled('ps_imageslider')){
            $pagesTrans['ps_imageslider'] = array(
                'title' => $this->l('Image slider'),
                'name' => 'ps_imageslider'
            );
        }
        if(Module::isInstalled('ets_extraproducttabs')){
            $pagesTrans['ets_extraproducttabs'] = array(
                'title' => str_replace('&amp;','&', $this->l('Custom Fields & Tabs On Product Page')),
                'name' => 'ets_extraproducttabs'
            );
        }
//        if (Module::isEnabled('creativeelements')) {
//        	$hooks = EtsTransCore::getAllHooksContentCE();
//        	$hooks_items = [];
//        	if ($hooks) {
//		        foreach ($hooks as $hook) {
//        			$key = 'ce_trans_contents_' . $hook['hook'];
//        			$hooks_items[$key] = [
//        				'title' => $hook['hook'],
//				        'name' => $key,
//				        'items' => EtsTransCore::getAllContentsCEModule($hook['hook'])
//			        ];
//		        }
//	        }
//	        $pagesTrans['ce_trans'] = array(
//		        'title' => str_replace('&amp;','&', $this->l('Creative Elements - live Theme & Page Builder')),
//		        'name' => 'ce_trans',
//		        'items' => array(
//			        'ce_trans_templates' => array(
//				        'title' => $this->l('Templates'),
//				        'name' => 'ce_trans_templates',
//				        'items' => [
//							'ce_trans_templates_header' => [
//								'title' => $this->l('Header templates'),
//								'name' => 'ce_trans_templates_header',
//								'items' => EtsTransCore::getAllTemplatesCEModule('header')
//							],
//					        'ce_trans_templates_footer' => [
//						        'title' => $this->l('Footer templates'),
//						        'name' => 'ce_trans_templates_footer',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('footer')
//					        ],
//					        'ce_trans_templates_page-index' => [
//						        'title' => $this->l('Home templates'),
//						        'name' => 'ce_trans_templates_page-index',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('page-index')
//					        ],
//					        'ce_trans_templates_page-contact' => [
//						        'title' => $this->l('Contact page templates'),
//						        'name' => 'ce_trans_templates_page-contact',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('page-contact')
//					        ],
//					        'ce_trans_templates_product' => [
//						        'title' => $this->l('Product page templates'),
//						        'name' => 'ce_trans_templates_product',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('product')
//					        ],
//					        'ce_trans_templates_product-quick-view' => [
//						        'title' => $this->l('Quick view templates'),
//						        'name' => 'ce_trans_templates_product-quick-view',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('product-quick-view')
//					        ],
//					        'ce_trans_templates_product-miniature' => [
//						        'title' => $this->l('Product miniature templates'),
//						        'name' => 'ce_trans_templates_product-miniature',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('product-miniature')
//					        ],
//					        'ce_trans_templates_page-not-found' => [
//						        'title' => $this->l('404 page templates'),
//						        'name' => 'ce_trans_templates_page-not-found',
//						        'items' => EtsTransCore::getAllTemplatesCEModule('page-not-found')
//					        ],
//				        ]
//			        ),
//			        'ce_trans_contents' => array(
//				        'title' => $this->l('Contents'),
//				        'name' => 'ce_trans_contents',
//				        'items' => $hooks_items
//			        ),
//		        )
//	        );
//        }
        return $pagesTrans;
    }

    public static function getTextLang($text, $lang,$file_name='')
    {
        $moduleName = 'ets_translate';
        $text2 = preg_replace("/\\\*'/", "\'", $text);
        if(is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif(is_object($lang))
            $iso_code = $lang->iso_code;
        else
        {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
        $modulePath = rtrim(_PS_MODULE_DIR_, '/').'/'.$moduleName;
        $fileTransDir = $modulePath.'/translations/'.$iso_code.'.'.'php';
        if(!@file_exists($fileTransDir)){
            return '';
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $strMd5 = md5($text2);
        $keyMd5 = '<{' . $moduleName . '}prestashop>' .($file_name ? Tools::strtolower($file_name) : $moduleName). '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if($matches && isset($matches[2])){
            return  $matches[2];
        }
        return '';
    }

    public static function getListFieldsProductTrans($onlyKeys = false) {
    	if ($onlyKeys) {
    		return ['name', 'description_short', 'description', 'available_now', 'available_later', 'delivery_in_stock', 'delivery_out_stock','meta_title', 'meta_description', 'legend', 'product_combinations_available_now', 'product_combinations_available_later'];
	    }
    	return [
    	    'name' => 'Product name',
    	    'description' => 'Description',
    	    'description_short' => 'Sumary',
    	    'available_now' => 'Label when in stock',
    	    'available_later' => 'Label when out of stock (and back order allowed)',
    	    'delivery_in_stock' => 'Delivery time of in-stock products',
    	    'delivery_out_stock' => 'Delivery time of out-of-stock products with allowed orders',
    	    'meta_title' => 'Meta title',
    	    'meta_description' => 'Meta description',
    	    'product_combinations_available_now' => 'Product combinations label when in stock',
    	    'product_combinations_available_later' => 'Product combinations label when out of stock',
    	    'legend' => 'Alt image',
	    ];
    }

    public static function getListRegion() {
	    return [
		    ["label"=> "US East (Ohio)", "value"=> "us-east-2"],
		    ["label"=> "US East (N. Virginia)", "value"=> "us-east-1"],
		    ["label"=> "US West (N. California)", "value"=> "us-west-1"],
		    ["label"=> "US West (Oregon)", "value"=> "us-west-2"],
		    ["label"=> "Africa (Cape Town)", "value"=> "af-south-1"],
		    ["label"=> "Asia Pacific (Hong Kong)", "value"=> "ap-east-1"],
		    ["label"=> "Asia Pacific (Jakarta)", "value"=> "ap-southeast-3"],
		    ["label"=> "Asia Pacific (Mumbai)", "value"=> "ap-south-1"],
		    ["label"=> "Asia Pacific (Osaka)", "value"=> "ap-northeast-3"],
		    ["label"=> "Asia Pacific (Seoul)", "value"=> "ap-northeast-2"],
		    ["label"=> "Asia Pacific (Singapore)", "value"=> "ap-southeast-1"],
		    ["label"=> "Asia Pacific (Sydney)", "value"=> "ap-southeast-2"],
		    ["label"=> "Asia Pacific (Tokyo)", "value"=> "ap-northeast-1"],
		    ["label"=> "Canada (Central)", "value"=> "ca-central-1"],
		    ["label"=> "China (Beijing)", "value"=> "cn-north-1"],
		    ["label"=> "China (Ningxia)", "value"=> "cn-northwest-1"],
		    ["label"=> "Europe (Frankfurt)", "value"=> "eu-central-1"],
		    ["label"=> "Europe (Ireland)", "value"=> "eu-west-1"],
		    ["label"=> "Europe (London)", "value"=> "eu-west-2"],
		    ["label"=> "Europe (Milan)", "value"=> "eu-south-1"],
		    ["label"=> "Europe (Paris)", "value"=> "eu-west-3"],
		    ["label"=> "Europe (Stockholm)", "value"=> "eu-north-1"],
		    ["label"=> "Middle East (Bahrain)", "value"=> "me-south-1"],
		    ["label"=> "South America (São Paulo)", "value"=> "sa-east-1"]
	    ];
	}

	public function getTotalTranslate($pageType, $idAttributeGroup = 0, $idFeature = 0, $isNewBlockreassurance = 0, $pcType = '')
	{
		switch ($pageType){
			case 'product':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."product_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'category':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."category_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'cms':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."cms_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'cms_category':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."cms_category_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'manufacturer':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."manufacturer_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'supplier':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."supplier_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'attribute_group':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."attribute_group_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'attribute':
				$total = Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `"._DB_PREFIX_."attribute_shop` attrs 
                LEFT JOIN `"._DB_PREFIX_."attribute` a ON attrs.id_attribute=a.id_attribute 
                WHERE attrs.`id_shop`=".(int)$this->context->shop->id.($idAttributeGroup ? " AND a.id_attribute_group=".(int)$idAttributeGroup : ""));
				break;
			case 'feature':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."feature_shop` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'feature_value':
				$total = Db::getInstance()->getValue("
                        SELECT COUNT(*) as total FROM `"._DB_PREFIX_."feature_value` fv 
                         LEFT JOIN `"._DB_PREFIX_."feature_shop` fs ON fv.id_feature=fs.id_feature WHERE fs.`id_shop`=".(int)$this->context->shop->id.($idFeature ? " AND fv.id_feature=".(int)$idFeature : ""));
                break;
			case 'blockreassurance':
				if($isNewBlockreassurance){
					$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."psreassurance` WHERE `id_shop`=".(int)$this->context->shop->id);
				}
				else
					$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."reassurance` WHERE `id_shop`=".(int)$this->context->shop->id);
				break;
			case 'ps_linklist':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."link_block`");
				break;
			case 'ps_mainmenu':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."linksmenutop` WHERE id_shop=".(int)$this->context->shop->id);
				break;
			case 'ps_imageslider':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."homeslider` WHERE id_shop=".(int)$this->context->shop->id);
				break;
			case 'ps_customtext':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."info_shop` WHERE id_shop=".(int)$this->context->shop->id);
				break;
			case 'ets_extraproducttabs':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."ets_ept_tab` WHERE id_shop=".(int)$this->context->shop->id);
				break;
			case 'pc':
				$total = Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."ets_rv_product_comment` epc
                                                            LEFT JOIN `"._DB_PREFIX_."product_shop` ps ON epc.id_product=ps.id_product AND ps.id_shop=".(int)$this->context->shop->id."
                                                             WHERE 1 ".($pcType && Validate::isCleanHtml($pcType) ? " AND epc.question=".($pcType == 'question' ? 1 : 0) : ""));
				break;
			case 'email':
				$total = 0;
				break;
			default:
				$total = 0;
				break;
		}

		return $total;
	}


	public function transJs()
	{
		return array(
			'translate' => $this->l('Translate'),
			'chatgpt' => $this->l('ChatGPT'),
			'label_chatgpt' => $this->l('Open ChatGPT'),
			'submit_chatgpt' => $this->l('Submit ChatGPT'),
			'cancel_chatgpt' => $this->l('Cancel'),
			'ChatGPT_API_request_error_text' => $this->l('ChatGPT API request failed.'),
			'empty_prompt_chatgpt' => $this->l('Prompt is required'),
			'apply_content_from_chatgpt_success' => $this->l('Apply content successfully!'),
			'g-translate' => $this->l('Translate'),
			'trans_all_comments' => $this->l('Translate all comments'),
			'trans_review' => $this->l('Translate review'),
			'translate_all_posts' => $this->l('Translate all posts'),
			'translate_all_categories' => $this->l('Translate all categories'),
			'bulk_translate' => $this->l('Translate selected items'),
			'translate_all' => $this->l('Translate all'),
			'checking' => $this->l('Checking'),
			'stop' => $this->l('Stop'),
			'delete' => $this->l('Delete'),
			'translating' => $this->l('Translating'),
			'target_lang_required' => $this->l('The target language is required'),
			'can_not_trans_item' => $this->l('Cannot translate this item'),
			'reset_all_trans' => $this->l('Reset all translation'),
			'reset_trans' => $this->l('Reset translation'),
			'reset_all_trans_success' => $this->l('Reset translation successfully'),
			'confirm_form_trans_not_save' => $this->l('The new translations are not saved. Do you want to leave this page?'),
			'translate_updated' => $this->l('Translation successfully updated'),
			'product' => $this->l('product'),
			'products' => $this->l('products'),
			'category' => $this->l('category'),
			'categories' => $this->l('categories'),
			'CMS' => $this->l('CMS'),
			'CMSs' => $this->l('CMSs'),
			'CMS_category' => $this->l('CMS category'),
			'CMS_categories' => $this->l('CMS categories'),
			'manufacturer' => $this->l('manufacturer'),
			'manufacturers' => $this->l('manufacturers'),
			'supplier' => $this->l('supplier'),
			'suppliers' => $this->l('suppliers'),
			'item' => $this->l('item'),
			'items' => $this->l('items'),
			'pause_success' => $this->l('Translation paused'),
			'resume' => $this->l('Resume'),
			'close' => $this->l('Close'),
			'pause' => $this->l('Pause'),
			'confirm_clear_all_logs' => $this->l('Are you sure you want to clear all logs?'),
			'confirm_delete_log_item' => $this->l('Do you want to delete this log item?'),
			'sentence' => $this->l('text'),
			'sentences' => $this->l('texts'),
			'not_need_translate' => $this->l('This page does not need to translate'),
			'web_data_required' => $this->l('Data to translate is required'),
			'initializing' => $this->l('Initializing'),
			'no_text_trans' => $this->l('All content has been translated or there is no data to translate, nothing to do!'),
			'blog_post' => $this->l('Blog post'),
			'blog_posts' => $this->l('Blog posts'),
			'blog_category' => $this->l('Blog category'),
			'blog_categories' => $this->l('Blog categories'),
			'megamenu' => $this->l('Mega menu'),
			'confirm_translate' => $this->l('Are you sure you want to translate this content?'),
			'translate_fields' => $this->l('Translate fields'),
			'translate_pages' => $this->l('Translate pages'),
			'translate_category_pages' => $this->l('Translate category pages'),
			'file_emails' => $this->l('email files'),
			'attribute' => $this->l('attribute'),
			'attributes' => $this->l('attributes'),
			'attribute_group' => $this->l('attribute group'),
			'attribute_groups' => $this->l('attribute groups'),
			'feature' => $this->l('feature'),
			'features' => $this->l('features'),
			'feature_value' => $this->l('feature value'),
			'feature_values' => $this->l('feature values'),
			'tab' => $this->l('tab'),
			'tabs' => $this->l('tabs'),
			'no_item_to_trans' => $this->l('No items to translate'),
			'enter_msg_reply' => $this->l('Enter a message to reply'),
			'translate_from' => $this->l('Translate from'),
			'translate_to' => $this->l('Translate into'),
			'lang_customer' => $this->l('Current customer language'),
			'auto_detect_language' => $this->l('Auto detect language'),
			'original_content' => $this->l('Original content'),
			'translated_content' => $this->l('Translated content'),
			'blank_to_use_origin' => $this->l('Leave this field blank to submit original content'),
			'swap_language' => $this->l('Swap language'),
			'languages' => $this->l('languages'),
			'translate_all_reviews' => $this->l('Translate all reviews'),
			'translate_all_comments' => $this->l('Translate all comments'),
			'translate_all_replies' => $this->l('Translate all replies'),
			'translate_all_questions' => $this->l('Translate all questions'),
			'translate_all_comments_for_questions' => $this->l('Translate all comments for questions'),
			'translate_all_answers' => $this->l('Translate all answers'),
			'translate_all_comments_for_answers' => $this->l('Translate all comments for answers'),
			'keep_original_content' => $this->l('Keep original content below translated content'),
			'invalid_phrase_key' => $this->l('Please enter all input phrase keys'),
			'label_phrase_key' => $this->l('Phrase key'),
			'label_translate_to' => $this->l('Translate into'),
			'loading_text' => $this->l('Loading...'),
			'you' => $this->l('You'),
		);
	}

	public function renderTransOptions() {
		return array(
			'both' => array(
				'title' => $this->l('Translate all missing translation fields (empty fields and fields which have the same content of source language)'),
				'default' => true
			),
			'only_empty' => array(
				'title' => $this->l('Translate empty fields only')
			),
			'same_source' => array(
				'title' => $this->l('Translate fields which have the same content of source language only')
			),
			'all' => array(
				'title' => $this->l('Translate all fields (replace all old translations)'),
			)
		);
	}

	public static function getLangDefault($onlyId = true) {
		$id = (int)Configuration::get('PS_LANG_DEFAULT');
		if ($onlyId)
			return $id;
		return Language::getLanguage($id);
	}

	public function getLinkDescription($apiType = '') {
		$url_description = 'https://demo2.presta-demos.com/docs/translate/';
		$default_url_description = $url_description . 'HOW_TO_GET_GOOGLE_API_KEY.pdf';
		if (!$apiType)
			return $default_url_description;
		switch ($apiType) {
			case EtsTransApi::$_BING_API_TYPE:
				return 'https://learn.microsoft.com/en-us/azure/cognitive-services/translator/how-to-create-translator-resource#authentication-keys-and-endpoint-url';
			case EtsTransApi::$_LIBRE_API_TYPE:
				return 'https://portal.libretranslate.com/';
			case EtsTransApi::$_DEEPL_API_TYPE:
                $currentLange = $this->context->language;
                $iso_code_upper = $currentLange && $currentLange->iso_code ? strtoupper($currentLange->iso_code) : 'EN';
				$fileNameDes = 'HOW_TO_GET_DEEPL_API_KEY_' . $iso_code_upper . '.pdf';
				if (in_array($iso_code_upper, ['ES', 'FR', 'IT', 'DE', 'PL', 'NL', 'PT', 'RU', 'CS'])) {
					return $url_description . $fileNameDes;
				}
				return $url_description . 'HOW_TO_GET_DEEPL_API_KEY.pdf';
			case EtsTransApi::$_LECTO_API_TYPE:
				return 'https://dashboard.lecto.ai/';
			case EtsTransApi::$_YANDEX_API_TYPE:
				return 'https://translate.yandex.com/developers/keys';
			case EtsTransApi::$_GOOGLE_API_TYPE:
			default:
				return $default_url_description;
		}
	}
	public static function checkEnableOtherShop($id_module)
	{
		$sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
		return Db::getInstance()->executeS($sql);
	}
	public static function activeTab($module_name)
	{
		if(property_exists('Tab','enabled'))
			return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'tab` SET enabled=1 where module ="'.pSQL($module_name).'"');
	}

	public static function getFeatureFlag($name, $select_field = '*')
	{
		try {
			if ($select_field == '*') {
				return Db::getInstance()->getRow('SELECT ' . pSQL($select_field) . ' FROM `'._DB_PREFIX_.'feature_flag` WHERE name="'.pSQL($name).'"');
			}
			return Db::getInstance()->getValue('SELECT ' . pSQL($select_field) . ' FROM `'._DB_PREFIX_.'feature_flag` WHERE name="'.pSQL($name).'"');
		} catch (\Exception $exception) {
			return false;
		}
	}
}