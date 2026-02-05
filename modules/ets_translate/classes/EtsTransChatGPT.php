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

class EtsTransChatGPT extends ObjectModel {

    const GPT_40 = 'gpt-4o-mini';
    const GPT_35_TURBO = 'gpt-3.5-turbo';
	public static $instance = null;
	private static $_MODULE_NAME = 'ets_translate';
	/**
	 * @var Ets_Translate
	 */
	private $module;

	private $key = '';
	private $model = self::GPT_40;
	private $headers = [];
	private $prompt = '';
	private $maxToken = 2000;
	private $temperature = 0.2;
	private $frequency_penalty = 0;
	private $presence_penalty = 0;
	private $session_id  = '';
	public $position;
	public $label;
	public $content;
	public static $definition = array(
		'table' => 'ets_trans_chatgpt_template',
		'primary' => 'id_ets_trans_chatgpt_template',
		'multilang' => true,
		'fields' => array(
			'position' => array('type' => self::TYPE_INT),
			'label' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml'),
			'content' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml'),
		)
	);




	public function __construct($id_item = null, $id_lang = null, $id_shop = null, $key = '', $model = '')
	{
		parent::__construct($id_item, $id_lang, $id_shop);
		if ($key) {
			$this->setKey($key);
		}
		if ($model)
			$this->setModel($model);
		$this->setModule();
	}

	/**
	 * @param String $key
	 * @param String $model
	 * @return EtsTransChatGPT|null
	 */
	public static function getInstance($key = '', $model = '')
	{
		if(!isset(self::$instance)){
			self::$instance = new EtsTransChatGPT($key, $model);
		}
		return self::$instance;
	}

	public function l($string)
	{
		return Translate::getModuleTranslation(self::$_MODULE_NAME, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
	}

	/**
	 * @return array
	 */
	public function setHeaders() {
		$key = $this->getKey();
		if ($key) {
			$this->headers = [
				'Accept: application/json',
				'Content-Type: application/json',
				'Authorization: Bearer ' . $key . '',
			];
		}
        return [];
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param Ets_Translate|null $module
	 */
	public function setModule($module = null) {
		if ($module)
			$this->module = $module;
		else
			$this->module = new Ets_Translate();
	}

	/**
	 * @return Ets_Translate
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	public function setKey($key) {
		$this->key = $key;
		$this->setHeaders();
	}

	/**
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}

	public function setModel($model) {
		if ($model)
			$this->model = $model;
	}

	public function setPrompt($prompt) {
		$this->prompt = $prompt;
	}

	public function getPrompt() {
		return $this->prompt;
	}

	public function setMaxToken($token) {
		$this->maxToken = $token;
	}

	public function getMaxToken() {
		return $this->maxToken;
	}

	public function setTemperature($temperature) {
		$this->temperature = $temperature;
	}

	public function getTemperature() {
		return $this->temperature;
	}

	/**
	 * @param string $uri
	 * @param array $data
	 * @return array
	 */
	private function request( $uri, $data) {
		$headers = $this->getHeaders();
		if (!$headers)
			$this->setHeaders();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$result = curl_exec($ch);
		return $this->formatResponse($result);
	}

	public function setSessionId($session) {
		$this->session_id = $session;
	}

	public function getData() {
		$data = [
			'model' => $this->getModel(),
			'temperature' => $this->getTemperature(),
			'max_tokens' => $this->getMaxToken(),
			'frequency_penalty' => $this->frequency_penalty,
			'presence_penalty' => $this->presence_penalty,
			'stop' => '[" Human:", " AI:"]'
		];
		if ($this->getModel() == self::GPT_40) {
			$data['messages'] = array(
				array(
					"role" => "user",
					"content" => str_replace('"', '', urldecode($this->getPrompt()))
				)
			);
		} else {
			$data['prompt'] = str_replace('"', '', urldecode($this->getPrompt()));
		}
		return $data;
	}

	private function formatResponse($res) {
		if ($res) {
			$result = json_decode($res, true);
			if (isset($result['error'])) {
				$res = [
					'errors' => [$result['error']['message']],
					'data' => '',
					'message' => $this->l('ChatGPT API request failed')
				];
			} else {
				$res = [
					'errors' => false,
					'data' => nl2br($result['choices'][0]['message']['content']),
					'message' => $this->l('ChatGPT API request successfully')
				];
			}
		}
		return $res;
	}

	public function completions() {
		if ($this->getPrompt() && $this->getModel() && $this->getKey()) {
			$endPoint = $this->getEndpoint('completion');
			return $this->request($endPoint, $this->getData());
		}
		return '';
	}

	public function getEndpoint($type, $model_id = '') {
		$url = 'https://api.openai.com/v1/';
		switch ($type) {
			case 'model':
				$url .= 'models';
				break;
			case 'retrieve_model':
				$url .= 'models';
				if ($model_id) {
					$url .= '/{' . $model_id . '}';
				}
				break;
			case 'completion':
			default:
				if ($this->getModel() == 'gpt-4o-mini') {
					$url = 'https://api.openai.com/v1/chat/completions';
				} else {
					$url .= 'completions';
				}
			break;
		}
		return $url;
	}

	public function validateApiKey($key) {
		if (!$key) {
			return [
				'errors' => $this->l('API key not found!'),
				'data' => '',
				'message' => $this->l('ChatGPT API request failed')
			];
		}
		$this->setKey($key);
		$this->setPrompt('hi');
		return $this->completions();
	}
	public function formatAndSaveMessage($data, $id_lang = null, $applyFor = 'form_step1_description_') {
		if ($data && Validate::isCleanHtml($data)) {
			$message = new EtsTransChatGPTMessage();
			$message->is_chatgpt = 1;
			$message->message = $data;
			$message->apply_for = $applyFor;
			if ($message->add()) {
				return $this->displayMessage($message, $id_lang, $applyFor);
			}
			return $this->l('An error occurred while saving the message');
		}
		return $data;
	}
	public function displayMessage($message, $id_lang = null, $applyFor = 'form_step1_description_')
	{
		if(!is_object($message))
		{
			$message = new EtsTransChatGPTMessage($message);
		}
		if ($this->module->gte810 && !$this->module->is812) {
			if ($applyFor == 'form_step1_description_short_')
				$applyFor = 'product_description_description_short_';
			if ($applyFor == 'form_step1_description_')
				$applyFor = 'product_description_description_';
		}
		Context::getContext()->smarty->assign(
			array(
				'chatgpt_message' => $message,
				'languages' => Language::getLanguages(),
				'defaultFormLanguage' => $id_lang ?: Configuration::get('PS_LANG_DEFAULT'),
				'apply_for' => $applyFor,
				'gte810' => $this->module->gte810 && !$this->module->is812
			)
		);
		return $this->module->display($this->module->getLocalPath(),'chatgpt-message.tpl');
	}

	public static function countTemplatesWithFilter($filter = false)
	{
		$req = 'SELECT COUNT(t.id_ets_trans_chatgpt_template) 
            FROM `'._DB_PREFIX_.'ets_trans_chatgpt_template` t
            LEFT JOIN `'._DB_PREFIX_.'ets_trans_chatgpt_template_lang` tl ON (t.id_ets_trans_chatgpt_template =tl.id_ets_trans_chatgpt_template AND tl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE 1 '.($filter ? bqSQL($filter) : '');
		return (int)Db::getInstance()->getValue($req);
	}

	public static function getTemplatesWithFilter($filter = false, $sort = false, $start = false, $limit = false)
	{
		$req = 'SELECT t.id_ets_trans_chatgpt_template,tl.*,tl.label as title
            FROM `' . _DB_PREFIX_ . 'ets_trans_chatgpt_template` t
            LEFT JOIN `'._DB_PREFIX_.'ets_trans_chatgpt_template_lang` tl ON (t.id_ets_trans_chatgpt_template =tl.id_ets_trans_chatgpt_template AND id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE 1 ' . ($filter ? bqSQL($filter) : '')
			. ($start !== false && $limit ? " LIMIT " . (int)$start . ", " . (int)$limit : "");
		return Db::getInstance()->executeS($req);
	}

	public function renderFormTemplateChatGPT()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Free Translate and AI Content Generator'),
					'icon' => 'icon-AdminCatalog',
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Label'),
						'name' => 'label',
						'lang' => true,
						'required' => true,
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Content'),
						'name' => 'content',
						'lang' => true,
						'required' => true,
						'shortcodes' => array(
							'{product_name}', '{product_description}', '{product_summary}', '{product_tags}', '{product_reference}', '{product_brand}', '{current_language}'
						),
						'shortcode_label' => $this->l('Available shortcodes:'),
					),
					array(
						'type' => 'hidden',
						'name' => 'control'
					),
					array(
						'type' => 'hidden',
						'name' => 'id_ets_trans_chatgpt_template',
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		$tab_module = $this->module->tab ? '&tab_module='.$this->module->tab : '';
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'ets_trans_chatgpt_template';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->module = $this->module;
		$helper->identifier = 'id_ets_trans_chatgpt_template';
		$helper->submit_action = 'etsTransSaveTemplateGPT';
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminModules', true).'&configure='.$this->module->name . $tab_module;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$fields = array(
			array(
				'name' => 'id_ets_trans_chatgpt_template',
				'primary_key' => true
			),
			array(
				'name' => 'label',
				'multi_lang' => true
			),
			array(
				'name' => 'content',
				'multi_lang' => true,
			),
		);
		$helper->tpl_vars = array(
			'base_url' => Context::getContext()->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $lang->id,
				'iso_code' => $lang->iso_code
			),
			'fields_value' => $this->module->getFieldsValues($fields,'id_ets_trans_chatgpt_template','EtsTransChatGPT','etsTransSaveTemplateGPT'),
			'cancel_popup' => '#',
			'languages' => Context::getContext()->controller->getLanguages(),
			'id_language' => Context::getContext()->language->id,
			'link' => Context::getContext()->link,
			'post_key' => 'id_ets_trans_chatgpt_template',
			'isMultipleLanguage' => ($idsLang = Language::getIDs(true)) && count($idsLang),
			'isEnableModule' => $this->module->isEnabledEtsTranslateModule(),
			'wrap_tab' => false
		);
		return $helper->generateForm(array($fields_form));
	}
	public static function getAllTemplate()
	{
		$languages = Language::getLanguages(false);
		$templates = array();
		if($languages)
		{
			foreach($languages as $language)
			{
				$sql ='SELECT tl.label,tl.content FROM `'._DB_PREFIX_.'ets_trans_chatgpt_template` t
                INNER JOIN `'._DB_PREFIX_.'ets_trans_chatgpt_template_lang` tl ON (t.id_ets_trans_chatgpt_template = tl.id_ets_trans_chatgpt_template AND tl.id_lang="'.(int)$language['id_lang'].'")';
				$templates[$language['id_lang']] = Db::getInstance()->executeS($sql);
			}
		}
		return $templates;
	}

}