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

class EtsTransApi
{
    public static $instance = null;
    public static $_GOOGLE_API_TYPE = 'google_api';
    public static $_DEEPL_API_TYPE = 'deepl_api';
    public static $_BING_API_TYPE = 'bing_api';
    public static $_LIBRE_API_TYPE = 'libre_api';
    public static $_LECTO_API_TYPE = 'lecto_api';
    public static $_YANDEX_API_TYPE = 'yandex_api';
    public static $listApiSupportExclude = ['google_api', 'deepl_api', 'bing_api'];

    protected $apiKey = null;
    public $apiType = null;
	/**
	 * @var Ets_Translate
	 */
	public $module;
    public function __construct()
    {
	    $this->module = new Ets_Translate();
	    $this->setApiType(Configuration::get('ETS_TRANS_SELECT_API'));
	    $this->setApiKey();
    }

    public function setApiType($apiType = null) {
	    $this->apiType = $apiType ?: self::$_GOOGLE_API_TYPE;
    }

    public function setApiKey($apiKey = null) {
    	if ($apiKey) {
    		$this->apiKey = $apiKey;
	    } else if ($this->apiType) {
    		switch ($this->apiType) {
			    case self::$_LECTO_API_TYPE:
			    	$this->apiKey = Configuration::get('ETS_TRANS_LECTO_API_KEY');
			    	break;
			    case self::$_LIBRE_API_TYPE:
				    $this->apiKey = Configuration::get('ETS_TRANS_LIBRE_API_KEY');
				    break;
			    case self::$_BING_API_TYPE:
				    $this->apiKey = Configuration::get('ETS_TRANS_BING_API_KEY');
				    break;
			    case self::$_DEEPL_API_TYPE:
				    $this->apiKey = Configuration::get('ETS_TRANS_DEEPL_API_KEY');
				    break;
			    case self::$_YANDEX_API_TYPE:
				    $this->apiKey = Configuration::get('ETS_TRANS_YANDEX_API_KEY');
				    break;
			    case self::$_GOOGLE_API_TYPE:
			    default:
	                $this->apiKey = Configuration::get('ETS_TRANS_GOOGLE_API_KEY');
			    	break;
		    }
	    }
	    $this->apiKey = trim($this->apiKey, ' ');
    }

    public static function getInstance()
    {
        if(!isset(self::$instance)){
            self::$instance = new EtsTransApi();
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_translate', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

	/**
	 * @return array[]
	 */
    public static function getListApi() {
	    return array(
		    ['id' => self::$_GOOGLE_API_TYPE, 'name' => 'Google translate'],
		    ['id' => self::$_DEEPL_API_TYPE, 'name' => 'DeepL translate'],
		    ['id' => self::$_BING_API_TYPE, 'name' => 'Bing translate'],
		    ['id' => self::$_LIBRE_API_TYPE, 'name' => 'Libre translate'],
		    ['id' => self::$_LECTO_API_TYPE, 'name' => 'Lecto translate'],
		    ['id' => self::$_YANDEX_API_TYPE, 'name' => 'Yandex translate'],
	    );
    }

	/**
	 * @param $key
	 * @return array
	 */
    public static function getApiNameByKey($key) {
    	$arr = self::getListApi();
    	return $arr[$key];
    }

    public function request($type, $uri, $params = array(), $headers = array(), $request = array(), $isParamConvertJson = false, $getStatus = false, $useUrlEncodeWithParams = false, $apiType = null)
    {
        $ch = curl_init();
        if (!$apiType)
        	$apiType = $this->apiType;

	    if ($request) {
			$uri = $uri . '?' . http_build_query($request);
	    }
        $uri = rtrim($uri, ' ');
        curl_setopt($ch, CURLOPT_URL, $uri);

        if($headers){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

	    $type = Tools::strtoupper($type);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        if($params && $type === 'POST'){
	        if ($useUrlEncodeWithParams) {
		        $data = self::urlEncodeWithRepeatedParams($params);
	        }
        	elseif ($isParamConvertJson)
		        $data = json_encode($params);
            else
	            $data = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 60 * 1000);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36');

	    if ($getStatus) {
		    $responseHeaders = [];
		    // this function is called by curl for each header received
		    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			    function($curl, $header) use (&$responseHeaders)
			    {
				    $len = strlen($header);
				    $header = explode(':', $header, 2);
				    if (count($header) < 2) // ignore invalid headers
					    return $len;
				    $key = strtolower(trim($header[0]));
				    $val = trim($header[1]);
				    if (isset($responseHeaders[$key])) {
					    if (is_array($responseHeaders[$key])) {
						    $responseHeaders[$key][] = $val;
					    } else
						    $responseHeaders[$key] = [$responseHeaders[$key], $val];
				    } else
					    $responseHeaders[$key] = $val;

				    return $len;
			    }
		    );
	    }

        $response = curl_exec($ch);
        if ($getStatus)
            $info = curl_getinfo($ch);
        curl_close($ch);
	    if ($getStatus)
		    return ['status_code' => $info['http_code'], 'data' => $response, 'headers' => $responseHeaders];
        return $response;
    }


    private function validateLang($lang) {
	    return $lang && Language::isInstalled($lang) ? Language::getLanguageCodeByIso($lang) : '';
    }

	/**
	 * @param $source
	 * @return string
	 */
    private function getSourceIfNot($source) {
	    if(!$source && !(int)Configuration::get('ETS_TRANS_AUTO_DETECT_LANG')){
		    $source = 'en';
	    }
	    return $source;
    }

	/**
	 * @param $source
	 * @param $target
	 * @param $textContent
	 * @param false $validateApiKey
	 * @return array
	 */
    private function validateBeforeTrans($source, $target, $textContent, $validateApiKey = false) {
	    if(!$textContent){
		    $data = array(
			    'errors' => false,
			    'data' => array()
		    );
		    return [
		        'isError' => true,
			    'data' => $data
		    ];
	    }
	    switch ($this->apiType) {
		    case self::$_DEEPL_API_TYPE:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No DeepL translate API key was found.'),
						    'message' => $this->l('No DeepL translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
		    case self::$_BING_API_TYPE:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No Bing translate API key was found.'),
						    'message' => $this->l('No Bing translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
		    case self::$_LIBRE_API_TYPE:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No Libre translate API key was found.'),
						    'message' => $this->l('No Libre translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
		    case self::$_LECTO_API_TYPE:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No Lecto translate API key was found.'),
						    'message' => $this->l('No Lecto translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
		    case self::$_YANDEX_API_TYPE:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No Yandex translate API key was found.'),
						    'message' => $this->l('No Yandex translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
		    case self::$_GOOGLE_API_TYPE:
		    default:
		    	if (!$this->apiKey)
				    return [
					    'isError' => true,
					    'data' => array(
						    'errors' => $this->l('No Google translate API key was found.'),
						    'message' => $this->l('No Google translate API key was found.'),
						    'data' => array()
					    )
				    ];
		    	break;
	    }

	    $source = $this->getSourceIfNot($source);

	    if(!is_array($textContent))
	    {
		    $textContent = array($textContent);
	    }

	    $langCodeSource = $this->validateLang($source);
	    $langCodeTarget = $this->validateLang($target);
	    $errorLang = '';
	    if($source && !$langCodeSource)
		    $errorLang = $this->l('The source language is invalid');
	    if(!$langCodeTarget)
		    $errorLang = $this->l('The target language is invalid');
	    if($errorLang && !$validateApiKey)
		    return [
			    'isError' => true,
			    'data' => array(
				    'errors' => $errorLang,
				    'message' => $errorLang,
				    'data' => array()
			    )
		    ];
	    if($source && $source !== 'en')
		    $source = $langCodeSource ? explode('-',$langCodeSource)[0] : '';
	    if($target !== 'fr')
		    $target = explode('-',$langCodeTarget)[0];
	    $enLang = array('en', 'gb');
	    if($target == 'tw'){
		    $target = 'zh-TW';
	    }
	    if (in_array($source, $enLang) && in_array($target, $enLang)){
		    return [
			    'isError' => true,
			    'data' => array(
				    'errors' => false,
				    'data' => $textContent
			    )
		    ];
	    }
	    if ($source == $target){
		    return [
			    'isError' => true,
			    'data' => array(
				    'errors' => false,
				    'data' => $textContent
			    )
		    ];
	    }
	    return [
	    	'source' => $source,
	    	'target' => $target,
	    	'textContent' => $textContent,
	    ];
    }

    public function googleTrans($source, $target, $textContent, $validateApiKey=false, $pageType = null)
    {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent, $validateApiKey);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
	    	return $validateBeforeTrans['data'];
	    }
	    $source = $validateBeforeTrans['source'];
	    $target = $validateBeforeTrans['target'];
	    $textContent = $validateBeforeTrans['textContent'];
        $queryUrl = 'key='.$this->apiKey;
        $params = array('q' => $textContent, 'target' => $target,'format'=> 'html');
        if($source){
            $params['source'] = $source;
        }

        $res = $this->request(
            'POST',
            'https://www.googleapis.com/language/translate/v2?'.$queryUrl,
            $params,
            array("X-HTTP-Method-Override: GET")
        );

        if($res){
            $jsonResponse = json_decode($res, true);
            if(isset($jsonResponse['data']) && isset($jsonResponse['data']['translations'])){
                $translated = array();
                $detectSourceLang = array();
                foreach ($jsonResponse['data']['translations'] as $item){
                    $translated[] = htmlspecialchars_decode($item['translatedText'], ENT_QUOTES);
                    if(!$source){
                        $detectSourceLang[] = isset($item['detectedSourceLanguage']) ? $item['detectedSourceLanguage'] : null;
                    }
                }
                EtsTransLogRequest::saveLog($pageType, self::$_GOOGLE_API_TYPE, 1, $textContent, $translated, $source, $target);
                return array(
                    'errors' => false,
                    'data' => $translated,
                    'detectedSourceLanguage' => $detectSourceLang
                );
            }
            if(isset($jsonResponse['error']) && isset($jsonResponse['error']['message'])){
	            EtsTransLogRequest::saveLog($pageType, self::$_GOOGLE_API_TYPE, 0, $textContent, [], $source, $target, null, $jsonResponse['error']['message']);
                return array(
                    'errors' => true,
                    'message' => $jsonResponse['error']['message'],
                    'data' => array(),
                );
            }
        }
        return array(
            'errors' => true,
            'data' => array()
        );
    }

    public function bingTrans($source, $target, $textContent, $pageType = null) {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
		    return $validateBeforeTrans['data'];
	    }
	    $source = $validateBeforeTrans['source'];
	    $target = $validateBeforeTrans['target'];
	    $textContent = $validateBeforeTrans['textContent'];
	    $endpoint = $this->getEndpointBingApi();
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Ocp-Apim-Subscription-Key: ' . $this->apiKey;
	    $request = [
		    'api-version' => '3.0',
		    'to' => $target,
		    'textType' => 'html'
	    ];
	    if ($source)
	    	$request['from'] = $source;
	    $params = [];
	    foreach ($textContent as $index => $text) {
	    	$params[] = ['Text' => $text];
	    }
	    $res = $this->request('POST', $endpoint, $params, $header, $request,true,true);
	    if ($res) {
		    $responseData = json_decode($res['data'], true);
	    	if ($res['status_code'] == 200) {
			    $translated = array();
			    $detectSourceLang = array();
				foreach ($responseData as $data) {
					if (isset($data['translations']) && count($data['translations'])) {
						foreach ($data['translations'] as $trans) {
							if (isset($trans['text']) && $trans['text'])
								$translated[] = htmlspecialchars_decode($trans['text'], ENT_QUOTES);
						}
					}
					if (!$source && isset($data['detectedLanguage'])) {
						$detectSourceLang[] = $data['detectedLanguage']['language'];
					}
				}
				$characterCount = isset($res['headers']) && isset($res['headers']['x-metered-usage']) ? (int)$res['headers']['x-metered-usage'] : null;
			    EtsTransLogRequest::saveLog($pageType, self::$_BING_API_TYPE, 1, $textContent, $translated, $source, $target, $characterCount);
			    return array(
				    'errors' => false,
				    'data' => $translated,
				    'detectedSourceLanguage' => $detectSourceLang
			    );
		    } else {
			    EtsTransLogRequest::saveLog($pageType, self::$_BING_API_TYPE, 0, $textContent, [], $source, $target, null, $responseData['error']['message']);
			    return array(
				    'errors' => true,
				    'message' => $responseData['error']['message'],
				    'data' => array(),
				    'api_error' => true
			    );
		    }
	    }
	    return array(
		    'errors' => true,
		    'data' => array()
	    );

    }

	private function convertLangIsoForDeepLApi($iso_lang) {
		$arrLangIsoSupport = [
			'no' => 'nb'
		];
		if (isset($arrLangIsoSupport[$iso_lang]) && $arrLangIsoSupport[$iso_lang])
			return $arrLangIsoSupport[$iso_lang];
		return $iso_lang;
	}

    public function deepLTrans($source, $target, $textContent, $pageType = null) {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
		    return $validateBeforeTrans['data'];
	    }
	    $source = $this->convertLangIsoForDeepLApi($validateBeforeTrans['source']);
	    $target = $this->convertLangIsoForDeepLApi($validateBeforeTrans['target']);
	    if (!$this->checkSupportLangDeepL($target)) {
	    	return array(
			    'errors' => sprintf($this->l('DeepL translate not support this language (%s)'), $target),
			    'message' => sprintf($this->l('DeepL translate not support this language (%s)'), $target),
			    'data' => array()
		    );
	    }
	    $textContent = $validateBeforeTrans['textContent'];
	    $endpoint = $this->getEndpointDeepLApi('translate');
	    $header = array();
	    $header[] = 'Content-type: application/x-www-form-urlencoded';
	    $header[] = 'X-HTTP-Method-Override: POST';
	    $header[] = 'Authorization: DeepL-Auth-Key ' . $this->apiKey;
		$params = [];
		if ($source)
			$params['source_lang'] = strtoupper($source);
		$params['target_lang'] = strtoupper($target);
		$params['tag_handling'] = 'xml';
		$params['ignore_tags'] = ["code"];
		$params['outline_detection'] = false;
		if (is_array($textContent)) {
			$txt = '';
			foreach ($textContent as $text) {
				$txt .= $text;
			}
			$params['text'] = $txt;
		} else
			$params['text'] = $textContent;
	    $res = $this->request(
		    'POST',
		    $endpoint,
		    $params,
		    $header,
		    [],
		    false,
		    true,
		    true
	    );
	    if ($res && $res['status_code'] == 200 && $res['data']) {
		    $responseData = json_decode($res['data'], true);

		    $translated = array();
		    $detectSourceLang = array();
		    foreach ($responseData as $data) {
			    foreach ($data as $trans) {
				    if (isset($trans['text']) && $trans['text'])
					    $translated[] = htmlspecialchars_decode($trans['text'], ENT_QUOTES);
				    if (!$source && isset($trans['detected_source_language']))
					    $detectSourceLang[] = strtolower($trans['detected_source_language']);
			    }
		    }
		    EtsTransLogRequest::saveLog($pageType, self::$_DEEPL_API_TYPE, 1, $textContent, $translated, $source, $target);
		    return array(
			    'errors' => false,
			    'data' => $translated,
			    'detectedSourceLanguage' => $detectSourceLang
		    );
	    } elseif($res['data']) {
	    	$mess = json_decode($res['data'], true);
		    EtsTransLogRequest::saveLog($pageType, self::$_DEEPL_API_TYPE, 0, $textContent, [], $source, $target, null, $mess['message']);
		    return array(
			    'errors' => true,
			    'message' => $mess['message'],
			    'data' => array(),
			    'api_error' => true
		    );
	    }
	    return array(
		    'errors' => true,
		    'data' => array()
	    );
    }

    public function yandexTrans($source, $target, $textContent, $pageType = null) {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
		    return $validateBeforeTrans['data'];
	    }
	    $source = $validateBeforeTrans['source'];
	    $target = $validateBeforeTrans['target'];
	    $textContent = $validateBeforeTrans['textContent'];
	    if (!$this->checkSupportLangYandex($target)) {
		    return array(
			    'errors' => sprintf($this->l('Yandex translate not support this language (%s)'), $target),
			    'message' => sprintf($this->l('Yandex translate not support this language (%s)'), $target),
			    'data' => array()
		    );
	    }
	    $endpoint = $this->getEndpointYandexApi('translate');
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Accept: */*';
	    $header[] = 'Host: translate.yandex.net';
	    $request = [
		    'lang' => $source ? $source . '-' . $target : $target,
		    'key' => $this->apiKey,
		    'format' => 'html'
	    ];
        $res = null;
	    if (!is_array($textContent)) {
		    $request['text'] = $textContent;
		    $res = $this->formatResponseYandex($this->request('POST', $endpoint, [], $header, $request, false, true, true));

	    } else {
	    	foreach ($textContent as $text) {
			    $request['text'] = $text;
			    $res = $this->formatResponseYandex($this->request('POST', $endpoint, [], $header, $request, false, true, true));
		    }
	    }
	    if ($res) {
		    $translated =$res['translated'];
		    $detectSourceLang = array();
	    }
	    return array(
		    'errors' => true,
		    'data' => array()
	    );

    }
    private function formatResponseYandex($res) {
	    if ($res && $res['status_code'] == 200 && $res['data']) {
			return $res['data'];
	    }
	    return null;
    }

    public function lectoTrans($source, $target, $textContent, $pageType = null) {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
		    return $validateBeforeTrans['data'];
	    }
	    $source = $validateBeforeTrans['source'];
	    $target = $validateBeforeTrans['target'];
	    $checkLangTarget = $this->checkSupportLangLecto($target, 'target');
	    if (!$checkLangTarget['is_support']) {
		    return array(
			    'errors' => $checkLangTarget['message'],
			    'message' => $checkLangTarget['message'],
			    'data' => array(),
			    'api_error' => true
		    );
	    }
	    $checkLangSource = $this->checkSupportLangLecto($source, 'source');
	    if ($source && !$checkLangSource['is_support']) {
		    return array(
			    'errors' => $checkLangSource['message'],
			    'message' => $checkLangSource['message'],
			    'data' => array(),
			    'api_error' => true
		    );
	    }

	    $endpoint = $this->getEnpointLectoApi('text'); //https://api.lecto.ai/v1/translate/text
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Accept: application/json';
	    $header[] = 'X-API-Key: ' . $this->apiKey;
	    $params = [
	    	'to' => [$target]
	    ];
	    if ($source)
	    	$params['from'] = $source;
	    if (is_array($textContent)) {
		    $params['texts'] = $textContent;
	    } else
		    $params['texts'] = [$textContent];
	    $res = $this->request('POST', $endpoint, $params, $header, [], true, true);
	    if ($res && $res['status_code'] == 200 && $res['data']) {
		    $responseData = json_decode($res['data'], true);

		    $translated = array();
		    $detectSourceLang = array();
		    $responseTrans = $responseData['translations'];
		    foreach ($responseTrans as $data) {
			    foreach ($data['translated'] as $trans) {
				    $translated[] = htmlspecialchars_decode($trans, ENT_QUOTES);
			    }
		    }
		    if (!$source && isset($responseData['from']))
			    $detectSourceLang[] = strtolower($responseData['from']);
		    $character_count = isset($responseData['translated_characters']) && $responseData['translated_characters'] ? $responseData['translated_characters'] : null;
			    EtsTransLogRequest::saveLog($pageType, self::$_LECTO_API_TYPE, 1, $textContent, $translated, $source, $target, $character_count);
		    return array(
			    'errors' => false,
			    'data' => $translated,
			    'detectedSourceLanguage' => $detectSourceLang
		    );
	    } elseif($res['data']) {
		    $data = json_decode($res['data'], true);
		    $mess = $this->getErrorMessageLecto($data) ? : $this->l('Request API failed!');
		    EtsTransLogRequest::saveLog($pageType, self::$_LECTO_API_TYPE, 0, $textContent, [], $source, $target, null, $mess);
		    return array(
			    'errors' => true,
			    'message' => $mess,
			    'data' => array(),
			    'api_error' => true
		    );
	    }
	    return array(
		    'errors' => true,
		    'data' => array()
	    );
    }

    public function libreTrans($source, $target, $textContent, $pageType = null) {
	    $validateBeforeTrans = $this->validateBeforeTrans($source, $target, $textContent);
	    if (isset($validateBeforeTrans['isError']) && $validateBeforeTrans['isError']) {
		    return $validateBeforeTrans['data'];
	    }
	    $source = $validateBeforeTrans['source'];
	    $target = $validateBeforeTrans['target'];
	    $textContent = $validateBeforeTrans['textContent'];
	    $endpoint = 'https://libretranslate.com/translate';
	    $header = array();
	    $header[] = 'Content-type: application/x-www-form-urlencoded';
	    $header[] = 'accept: application/x-www-form-urlencoded';
	    $params = [
	    	'q' => $textContent,
			'target' => $target,
		    'source' => $source ?: 'auto',
		    'format' => 'html',
		    'api_key' => $this->apiKey
	    ];

	    $res = $this->request(
		    'POST',
		    $endpoint,
		    $params,
		    $header,
		    [],
		    false,
		    true,
		    true
	    );


	    if ($res && $res['status_code'] == 200 && $res['data']) {
		    $responseData = json_decode($res['data'], true);
		    $translated = array();
		    if (isset($responseData['translatedText']) && $responseData['translatedText']) {
			    $translated[] = htmlspecialchars_decode($responseData['translatedText'], ENT_QUOTES);
		    }
		    EtsTransLogRequest::saveLog($pageType, self::$_LIBRE_API_TYPE, 1, $textContent, $translated, $source, $target);
		    return array(
			    'errors' => false,
			    'data' => $translated,
			    'detectedSourceLanguage' => []
		    );
	    } elseif($res['data']) {
		    $mess = json_decode($res['data'], true);
		    EtsTransLogRequest::saveLog($pageType, self::$_LIBRE_API_TYPE, 0, $textContent, [], $source, $target, null, $mess['message']);
		    return array(
			    'errors' => true,
			    'message' => $mess['message'],
			    'data' => array(),
		    );
	    }
	    return array(
		    'errors' => true,
		    'data' => array()
	    );

    }

    public function translate($source, $target, $textContent, $pageType = null)
    {
        $isUseAppendWords = $this->isUseAppendContextualWords($pageType, $source);
        if (is_numeric($source))
            $source = Language::getIsoById($source);
        $dataBeforeTrans = $this->formatBeforeTranslate($textContent, $target, $isUseAppendWords);
        if ($this->apiType === self::$_DEEPL_API_TYPE)
        	$textChunk = $dataBeforeTrans['textTrans'];
        else
            $textChunk = array_chunk($dataBeforeTrans['textTrans'], 100);
        $resultTrans = array(
            'errors' => false,
            'message' => '',
            'data' => array(),
            'detectedSourceLanguage' => array(),
        );
        $arraySpecials = array('en', 'gb');
        foreach ($textChunk as $textItems){
            if (in_array($target, $arraySpecials) && in_array($source, $arraySpecials)){
	            $resultTrans['data'] = array_merge($resultTrans['data'], is_array($textItems) ? $textItems : [$textItems]);
            }
            else{
            	switch ($this->apiType) {
		            case self::$_BING_API_TYPE:
		            	$resultItem = $this->bingTrans($source, $target, $textItems, $pageType);
	                break;
		            case self::$_DEEPL_API_TYPE:
		            	$resultItem = $this->deepLTrans($source, $target, $textItems, $pageType);
	                break;
		            case self::$_LIBRE_API_TYPE:
		            	$resultItem = $this->libreTrans($source, $target, $textItems, $pageType);
	                break;
		            case self::$_LECTO_API_TYPE:
		            	$resultItem = $this->lectoTrans($source, $target, $textItems, $pageType);
	                break;
		            case self::$_YANDEX_API_TYPE:
		            	$resultItem = $this->yandexTrans($source, $target, $textItems, $pageType);
	                break;
		            case self::$_GOOGLE_API_TYPE:
		            default:
		                $resultItem = $this->googleTrans($source, $target, $textItems, false, $pageType);
	                break;
	            }
                if(isset($resultItem['errors']) && $resultItem['errors']){
                    $resultTrans['errors'] = true;
                    $resultTrans['message'] = isset($resultItem['message']) ? $resultItem['message'] : 'Error';
                    break;
                }
                else{
                    $resultTrans['data'] = array_merge($resultTrans['data'], $resultItem['data']);
                    if(!$source && isset($resultItem['detectedSourceLanguage'])){
                        $resultTrans['detectedSourceLanguage'] = array_merge($resultTrans['detectedSourceLanguage'], $resultItem['detectedSourceLanguage']);
                    }
                }
            }
        }
        $resultTrans['data'] = $this->formatAfterTranslate($resultTrans['data'], $source, $target, $isUseAppendWords, $dataBeforeTrans['textTrans'], $pageType, $dataBeforeTrans['embeds']);
        $resultTrans['api_type'] = $this->apiType;
        return $resultTrans;
    }

    public function validateApiKey($key, $apiType = '')
    {
    	if ($apiType)
    		$this->apiType = $apiType;
	    switch ($apiType) {
		    case self::$_GOOGLE_API_TYPE:
    		default:
			    $this->apiKey = $key;
			    $res = $this->googleTrans('en', 'fr', 'hi', true);
			    if($res && !$res['errors'] && $res['data']){
				    $result = [
					    'status' => 200,
					    'message' => ''
				    ];
			    } else {
				    $result = [
					    'status' => false,
					    'message' => $res['errors'] && isset($res['message']) ? $res['message'] : ''
				    ];
			    }
    		break;
		    case self::$_DEEPL_API_TYPE:
		    	$this->apiKey = $key;
		    	$res = $this->validateDeepLApi();
			    if ($res && isset($res['character_count']) && isset($res['character_limit']) && $res['character_limit'] > $res['character_count']) {
				    $result = [
				    	'status' => 200,
					    'message' => ''
				    ];
			    } else
				    $result = [
					    'status' => false,
					    'message' => ''
				    ];
		    break;
		    case self::$_LIBRE_API_TYPE:
		    	$this->apiKey = $key;
			    $result = $this->validateLibreApi();
		    break;
		    case self::$_LECTO_API_TYPE:
		    	$this->apiKey = $key;
			    $result = $this->validateLectoApi();
		    break;
		    case self::$_YANDEX_API_TYPE:
		    	$this->apiKey = $key;
			    $result = $this->validateYandexApi();
		    break;
		    case self::$_BING_API_TYPE:
		    	$this->apiKey = $key;
		    	$result = $this->validateBingApi();
		    break;
	    }
	    return $result;
    }

    public function validateYandexApi() {
	    $endpoint = $this->getEndpointYandexApi('detect');
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Accept: */*';
	    $header[] = 'Host: translate.yandex.net';
	    $request = [
	    	'text' => 'Hello',
		    'key' => $this->apiKey,
		    'hint' => 'en,de'
	    ];
	    $res = $this->request('POST', $endpoint, [], $header, $request, false, true);
	    if ($res && $res['status_code'] == 200) {
		    return [
			    'status' => $res['status_code'],
			    'message' => '',
		    ];
	    }
	    return [
		    'status' => $res['status_code'],
		    'message' => '',
	    ];
    }

    public function validateLectoApi() {
	    $endpoint = $this->getEnpointLectoApi('text'); //https://api.lecto.ai/v1/translate/text
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Accept: application/json';
	    $header[] = 'X-API-Key: ' . $this->apiKey;
	    $params = [
		    'from' => 'en',
		    'texts' => ['hi'],
		    'to' => ['de']
	    ];
	    $res = $this->request('POST', $endpoint, $params, $header, [], true, true);
	    if ($res && $res['status_code'] == 200) {
		    return [
		    	'status' => $res['status_code'],
			    'message' => '',
		    ];
	    }
	    $data = json_decode($res['data'], true);
	    return [
		    'status' => $res['status_code'],
		    'message' => $this->getErrorMessageLecto($data),
	    ];
    }

    private function getErrorMessageLecto($data) {
	    $mess = isset($data['details']) && isset($data['details']['message']) ? $data['details']['message'] : '';
	    if (isset($data['details']) && isset($data['details']['texts']))
		    $mess .= ', ' . $data['details']['texts'];
	    if (isset($data['details']) && isset($data['details']['from']))
		    $mess .= ', ' . $data['details']['from'];
	    if (isset($data['details']) && isset($data['details']['to']))
		    $mess .= ', ' . $data['details']['to'];
	    return $mess;
    }

    public function validateLibreApi() {
	    $endpoint = 'https://libretranslate.com/detect';
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'accept: application/x-www-form-urlencoded';
	    $params = [
	    	'q' => 'hi',
		    'api_key' => $this->apiKey
	    ];
	    $res = $this->request(
		    'POST',
		    $endpoint,
		    $params,
		    $header,
		    [],
		    true,
		    true
	    );
	    if ($res && $res['status_code'] == 200) {
		    return [
			    'status' => $res['status_code'],
			    'message' => '',
		    ];
	    }
	    return [
		    'status' => $res['status_code'],
		    'message' => '',
	    ];
    }

    public function validateBingApi() {
    	$endpoint = $this->getEndpointBingApi();
	    $header = array();
	    $header[] = 'Content-Type: application/json';
	    $header[] = 'Ocp-Apim-Subscription-Key: ' . $this->apiKey;
	    $params = [['Text' => 'hi']];
	    $request = [
	    	'api-version' => '3.0',
		    'to' => 'es'
	    ];
	    $res = $this->request(
		    'POST',
		    $endpoint,
		    $params,
		    $header,
		    $request,
		    true,
		    true
	    );
	    if ($res && $res['status_code'] == 200) {
		    return [
			    'status' => $res['status_code'],
			    'message' => '',
		    ];
	    }
	    $mess = isset($res['data']['error']) && isset($res['data']['error']['message']) ? $res['data']['error']['message'] : '';

	    return [
		    'status' => $res['status_code'],
		    'message' => $mess,
	    ];
    }

    public function getEndpointBingApi($region = '') {
	    $endpoint = 'https://api.cognitive.microsofttranslator.com/translate';
	    if ($region) {
		    // switch to endpoint api with region
		    // do after
	    }
	    return $endpoint;
    }

    public function validateDeepLApi() {
    	$endpoint = $this->getEndpointDeepLApi('usage');
	    $header = array();
	    $header[] = 'Content-type: application/json';
	    $header[] = 'X-HTTP-Method-Override: POST';
	    $header[] = 'Authorization: DeepL-Auth-Key ' . $this->apiKey;
	    $res = $this->request(
		    'POST',
		    $endpoint,
		    [],
		    $header
	    );
	    return json_decode($res, true);
    }

    private function getEndpointYandexApi($path) {
	    $uri = 'https://translate.yandex.net/api/v1.5/tr.json/';
	    if (strpos($path, '/') === 0) {
		    $path = substr($path, 1);
	    }
	    return $uri . $path;
    }

    private function getEnpointLectoApi($path) {
	    $uri = 'https://api.lecto.ai/v1/translate/';
	    if (strpos($path, '/') === 0) {
		    $path = substr($path, 1);
	    }
	    return $uri . $path;
    }

    private function getEndpointDeepLApi($path) {
    	$uri = 'https://api.deepl.com/v2/';
		if ($this->apiType == self::$_DEEPL_API_TYPE && $this->apiKey) {
			$pattern = "/:fx/i";
			if (preg_match($pattern, $this->apiKey)) {
				$uri = 'https://api-free.deepl.com/v2/';
			}
		}
		if (strpos($path, '/') === 0) {
			$path = substr($path, 1);
	    }
		return $uri . $path;
    }

	public static function notTranslateRegexFormat($regex)
	{
		return '(?<!<span\sclass="notranslate\sexclude">)'.$regex.'(?!<\/span>)?';
	}
	const arrIncludeRegex = ['(%[a-zA-Z0-9._-]+%)','%s', '%d'];
    public function formatBeforeTranslate($textTrans, $IsoTarget, $isAppendContextualWord = false){
    	$phraseKey = $this->getPhraseWord('from');
	    $listExcludes = [];
	    if (in_array($this->apiType, self::$listApiSupportExclude))
            $listExcludes = $this->getExcludeWords();
        $excludeRegex = array();
        $replacements = array();
        $arr_special_characters = ['%', '&', '#', '$', '@', '*'];
        foreach ($listExcludes as $word){
	        if (!$word) continue;

	        if (!in_array($word,self::arrIncludeRegex))
		        $word = preg_quote($word, '/');
	        if (in_array($word,self::arrIncludeRegex) || in_array($word[0], $arr_special_characters) || in_array($word[Tools::strlen($word) -1], $arr_special_characters)){
		        $excludeRegex[] = '/'.self::notTranslateRegexFormat($word).'/i';
		        $replacements[] = '<'.'span class="'.'notranslate exclude'.'"'.'>'.'$1'.'<'.'/'.'span'.'>';
	        }
	        else {
		        $excludeRegex[] = '/\b' . self::notTranslateRegexFormat($word) . '\b/i';
		        $replacements[] = '<'.'s'.'p'.'a'.'n class="'.'notranslate exclude"'.'>'.'$1'.'<'.'/span'.'>';
	        }
        }
        $isTransOnlyPhrase = Configuration::get('ETS_TRANS_PHRASE_ALONE');
        $embeds = [];
        foreach ($textTrans as $key=>$text){
        	if (is_string($text) && !empty($text) && $text)
        	{
		        $text = trim($text);
		        $text = str_replace("\n", '<'.'s'.'p'.'a'.'n class="trans_broken"'.'>'.'<'.'/'.'s'.'p'.'a'.'n'.'>', $text);
		        $_text = $text;

		        if ($phraseKey && count($phraseKey)) {
		        	$isValidOnlyPhrase = false;
		        	if ($isTransOnlyPhrase) {
				        $arrText = explode('<'.'s'.'p'.'a'.'n'.' class="'.'trans_broken"'.'>'.'<'.'/'.'s'.'p'.'a'.'n'.'>', $_text);
				        foreach ($arrText as $i => $t) {
					        $_t = preg_replace('/^<[^>]+>|<[^>]+>$/', '', trim($t));
					        foreach ($phraseKey as $k => $phrase) {
						        if (trim($t) == $phrase || $_t == $phrase) {
							        $isValidOnlyPhrase = true;
							        $arrText[$i] = '<'.'s'.'p'.'a'.'n data-index="' . $k . '" class="notranslate phrase"'.'>' . $t . '<'.'/'.'s'.'p'.'a'.'n'.'>';
						        }
					        }
				        }
				        $text = implode('<'.'s'.'p'.'a'.'n class="trans_broken"'.'>'.'<'.'/'.'s'.'p'.'a'.'n'.'>', $arrText);
			        } else {
				        $count = 0;
				        foreach ($phraseKey as $k => $phrase) {
					        $regex = '/('. preg_quote($phrase, '/') .')/';
					        $replace = '<'.'span data-index="' . $k . '" class="notranslate phrase"'.'>'.'$1'.'<'.'/'.'s'.'p'.'a'.'n'.'>';
					        $text = preg_replace($regex, $replace, $text, -1,$count);
				        }
				        if ($count)
					        $isValidOnlyPhrase = true;
			        }

			        if ($isValidOnlyPhrase) {
				        $textTrans[$key] = $text;
				        continue;
			        }
		        }
		        if($listExcludes){
			        $text = preg_replace($excludeRegex, $replacements, $text);
			        if($isAppendContextualWord){
				        if($maxLength = (int)Configuration::get('ETS_TRANS_MAX_WORD_APPEND_CONTEXT_WORD')){
					        if($this->countWords($text) <= $maxLength){
						        $contextualWords = Configuration::get('ETS_TRANS_CONTEXT_WORDS');
						        $text = $text.'<'.'s'.'p'.'a'.'n class="notranslate contextual"'.'>'.strip_tags($contextualWords).'<'.'/'.'s'.'p'.'a'.'n'.'>';
					        }
				        }
				        else{
					        $contextualWords = Configuration::get('ETS_TRANS_CONTEXT_WORDS');
					        $text = $text.'<'.'s'.'p'.'a'.'n class="notranslate contextual">'.strip_tags($contextualWords).'<'.'/'.'s'.'p'.'a'.'n'.'>';
				        }
			        }
		        }
                preg_match_all('/<iframe.*?>.*?<\/iframe>/is', $text, $embeds);
                $embeds[$key] = $embeds[0];
                foreach ($embeds as $k => $embed) {
                    $placeholder = '__ETS_EMBED_' . base64_encode($k) . '__';
                    $text = str_replace($embed, $placeholder, $text);
                }
		        $textTrans[$key] = $text;
	        }
        	else
        	{
		        $textTrans[$key] = '<'.'s'.'p'.'a'.'n class="trans_empty"'.'>' . $text . '<'.'/'.'s'.'p'.'a'.'n'.'>';
	        }
        }
        return ['textTrans' => $textTrans, 'embeds' => $embeds];
    }
    public function formatAfterTranslate($textTrans,$langSource, $langTarget, $isAppendContextualWord = false, $textOriginal = array(), $pageType = null, $embeds = []){
		$phrase = $this->getPhraseWord('', $langTarget);
        foreach ($textTrans as $key=>$text){
        	$text_result = $text;
	        $text_result = preg_replace('/<'.'span class="trans_broken">\s*'.'<'.'\/span'.'>/', "\n", $text_result);
	        $text_result = preg_replace('/<'.'span\s+class="trans_empty">(.*?)'.'<'.'\/span'.'>/', '$1', $text_result);
	        $text_result = $this->pregReplaceAll('/<'.'span class="notranslate exclude">'.'(.*?)'.'<'.'\/span'.'>/', '$1', $text_result);
            if (isset($phrase['from']) && count($phrase['from']) && isset($phrase['to']) && count($phrase['to'])) {
	            $isTransOnlyPhrase = Configuration::get('ETS_TRANS_PHRASE_ALONE');
            	foreach ($phrase['from'] as $k => $textPhraseFrom) {
            		if ($isTransOnlyPhrase) {
			            $_textPhraseFrom = '<'.'s'.'p'.'a'.'n data-index="' . $k . '" class="notranslate phrase"'.'>' . trim($textPhraseFrom) . '<'.'/'.'span'.'>';
			            $arrText = explode('<'.'s'.'p'.'a'.'n class="trans_broken"'.'>'.'<'.'/'.'span'.'>', $text);
			            if (count($arrText) > 1) {
				            $textReplace = isset($phrase['to'][$k]) && $phrase['to'][$k] ? $phrase['to'][$k] : '$1';
				            $text_result = $this->pregReplaceAll('/<'.'span data-index="' . $k . '" class="notranslate phrase"'.'>'.'(.*?)'.'<'.'\/span'.'>'.'/', $textReplace, $text_result);
			            } else {
				            if ($text == $_textPhraseFrom) {
					            $text_result = isset($phrase['to'][$k]) && $phrase['to'][$k] ? $phrase['to'][$k] : $textPhraseFrom;
				            }
			            }
		            } else {
            			$textReplace = isset($phrase['to'][$k]) && $phrase['to'][$k] ? $phrase['to'][$k] : '$1';
			            $text_result = $this->pregReplaceAll('/<'.'span data-index="' . $k . '" class="notranslate phrase"'.'>(.*?)'.'<'.'\/span'.'>/', $textReplace, $text_result);
		            }
	            }
            }
	        $textTrans[$key] = $text_result;
            if($isAppendContextualWord) {
                $textTrans[$key] = preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textTrans[$key]);
                //Re-translate if response data is empty
                if(!trim($textTrans[$key])){
	                switch ($this->apiType) {
		                case self::$_BING_API_TYPE:
			                $res = $this->bingTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), $pageType);
			                break;
		                case self::$_DEEPL_API_TYPE:
			                $res = $this->deepLTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), $pageType);
			                break;
		                case self::$_LIBRE_API_TYPE:
			                $res = $this->libreTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), $pageType);
			                break;
		                case self::$_LECTO_API_TYPE:
			                $res = $this->lectoTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), $pageType);
			                break;
		                case self::$_YANDEX_API_TYPE:
			                $res = $this->yandexTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), $pageType);
			                break;
		                case self::$_GOOGLE_API_TYPE:
		                default:
		                    $res = $this->googleTrans($langSource, $langTarget, array(preg_replace('/<'.'span class="notranslate contextual"'.'>'.'(.*?)'.'<'.'\/span'.'>/', '', $textOriginal[$key])), false, $pageType);
			                break;
	                }
                    if((!isset($res['errors']) || !$res['errors']) && isset($res['data']) && $res['data']){
                        $textTrans[$key] = $res['data'][0];
                    }
                }
                if($textOriginal[$key] && Tools::substr($textOriginal[$key], -1) !== ' '){
                    $textTrans[$key] = rtrim($textTrans[$key]);
                }
            }
            $embed = $embeds && isset($embeds[$key]) && $embeds[$key] ? $embeds[$key] : [];
            if ($embed) {
                foreach ($embed as $k => $e) {
                    $placeholder = '__ETS_EMBED_' . base64_encode($k) . '__';
                    $textTrans[$key] = str_replace($placeholder, $e, $textTrans[$key]);
                }
            }
        }
        return $textTrans;
    }

    private function pregReplaceAll($regex, $replacement, $string) {
	    while(preg_match($regex, $string)) {
		    $string = preg_replace($regex, $replacement, $string);
	    }
	    return $string;
	}

    public function getExcludeWords()
    {
        $excludeWords = Configuration::get('ETS_TRANS_EXCLUDE_WORDS');
        if($excludeWords) {
            $listExcludes = explode("\r\n", $excludeWords);
            foreach ($listExcludes as $k=>$w){
                $listExcludes[$k] = trim($w);
            }
        }
        else{
            $listExcludes = array();
        }
	    usort($listExcludes, [EtsTransApi::class, "compareStrings"]);
	    $listExcludes = array_reverse($listExcludes);
	    return array_unique(array_merge($listExcludes, self::arrIncludeRegex));
    }
	public static function compareStrings($a, $b) {
	    if (strpos($a, $b) !== false) {
		    return -1;
	    } else {
		    return strcmp($a, $b);
	    }
    }

    public function getPhraseWord($key = '', $isoTarget = '') {
    	if ($key == 'from') {
		    return json_decode(Configuration::get('ETS_TRANS_KEY_PHRASE_FROM'), true);
	    }
    	if ($key == 'to' && $isoTarget) {
    		return json_decode(Configuration::get('ETS_TRANS_KEY_PHRASE_TO', Language::getIdByIso($isoTarget)), true);
	    }
    	return [
    		'from' => json_decode(Configuration::get('ETS_TRANS_KEY_PHRASE_FROM'), true),
    		'to' => json_decode(Configuration::get('ETS_TRANS_KEY_PHRASE_TO', Language::getIdByIso($isoTarget)), true),
	    ];
    }

    public function getTotalFeeTranslate($nbChar)
    {
        switch ($this->apiType){
            case self::$_GOOGLE_API_TYPE:
	        default:
                if(($configRate = $this->module->getRateApi()) && Tools::strlen($configRate)){
                    $perChar = (float)$configRate / 1000000;
                    return (int)$nbChar * $perChar;
                }
                return (int)$nbChar * 0.00002;
            case self::$_BING_API_TYPE:
                if(($configRate = $this->module->getRateApi()) && Tools::strlen($configRate)){
                    $perChar = (float)$configRate / 1000000;
                    return (int)$nbChar * $perChar;
                }
                return (int)$nbChar * 0.00001;
        }
        return 0;
    }

    public static function getLangCodeFromIdLang($idLang)
    {
        $isoCode = Language::getIsoById($idLang);
        if(!$isoCode){
            return null;
        }
        return explode('-',Language::getLanguageCodeByIso($isoCode))[0];
    }

    public function isUseAppendContextualWords($pageType, $langSource)
    {
        if(!$langSource){
            $idLang = Language::getIdByIso('en') ?: (int) Configuration::get('PS_LANG_DEFAULT');
        }
        else if (!is_numeric($langSource)){
            $idLang = Language::getIdByIso($langSource);
        } else {
            $idLang = $langSource;
        }
        if(!(int)Configuration::get('ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD', $idLang)){
            return false;
        }
        $catalog = array('catalog', 'product', 'category', 'manufacturer', 'supplier');
        $page = array('cms', 'cms_category', 'page');
        $inter = array('theme', 'module', 'email', 'inter', 'sfmodule', 'back', 'others');
        $configPageToAppend = Configuration::get('ETS_TRANS_PAGE_APPEND_CONTEXT_WORD',  $idLang);
        $pageToAppend = $configPageToAppend ? explode(',', $configPageToAppend) : array();
        if(in_array('catalog', $pageToAppend) && in_array($pageType, $catalog))
            return true;
        if(in_array('page', $pageToAppend) && in_array($pageType, $page))
            return true;
        if(in_array('inter', $pageToAppend) && in_array($pageType, $inter))
            return true;
        if(in_array($pageType, $pageToAppend)){
            return true;
        }
        return false;
    }

    public function countWords($text)
    {
        $text = trim(strip_tags($text));
        preg_match_all('/\S+/', $text, $matches);
        return $matches ? count($matches) : 0;
    }

    private function checkSupportLangLecto($lang_code, $type) {
	    $list = $this->getListLangSupportLecto();
	    $is_support = false;
	    $_type = $type == 'target' ? 'support_target' : 'support_source';
	    if ($list && $list['success'] && $list['languages']) {
		    foreach ($list['languages'] as $l) {
			    if ($l['language_code'] && $lang_code == $l['language_code'] && $l[$_type]) {
				    $is_support = true;
				    break;
			    }
		    }
	    }
	    return [
	        'is_support' => $is_support,
		    'message' => !$list['success'] ? $list['message'] : ''
	    ];
	}

	private function checkSupportLangYandex($lang_code) {
    	$list = $this->getListLangSupportYandex();
		$is_support = false;
    	if ($list && isset($list['langs']) && $list['langs']) {
    		foreach ($list['langs'] as $iso => $lang) {
    			if ($iso == $lang_code) {
				    $is_support = true;
				    break;
			    }
		    }
	    }
		return $is_support;
	}

    private function checkSupportLangDeepL($lang_code) {
    	$list = $this->getListLangSupportDeepL();
    	$is_support = false;
    	if ($list) {
    		foreach ($list as $l) {
    			if ($l['language'] && strtoupper($lang_code) == $l['language']) {
    				$is_support = true;
    				break;
			    }
		    }
	    }
    	return $is_support;
    }

	private static function urlEncodeWithRepeatedParams($params)
	{
		$params = isset($params) ? $params : [];
		$fields = [];
		foreach ($params as $key => $value) {
			$name = \urlencode($key);
			if (is_array($value)) {
				$fields[] = implode(
					'&',
					array_map(
						function (string $textElement) use ($name) {
							return $name . '=' . \urlencode($textElement);
						},
						$value
					)
				);
			} elseif (is_null($value)) {
				// Parameters with null value are skipped
			} else {
				$fields[] = $name . '=' . \urlencode($value);
			}
		}

		return implode("&", $fields);
	}

	private function getListLangSupportLecto() {
		$list = Configuration::get('ETS_TRANS_LIST_LANG_TARGET_LECTO');
		if ($list) {
			$list = json_decode($list, true);
			if (isset($list['languages'])) {
				return [
					'success' => true,
					'languages' => $list['languages']
				];
			}
		}
		$endpoint = $this->getEnpointLectoApi('languages');
		$header = array();
		$header[] = 'Content-type: application/json';
		$header[] = 'accept: application/json';
		$header[] = 'X-API-Key: ' . $this->apiKey;
		$res = $this->request('GET', $endpoint, [], $header);
		if ($res) {
			$list = json_decode($res, true);

			if (isset($list['languages'])) {
				Configuration::updateValue('ETS_TRANS_LIST_LANG_TARGET_LECTO', $res);
				return [
					'success' => true,
					'languages' => $list['languages']
				];
			} else {
				return [
					'success' => false,
					'languages' => [],
					'message' => $this->getErrorMessageLecto($list)
				];
			}
		}
		return [
			'success' => false,
			'languages' => [],
			'message' => $this->l('Cannot load the list of support languages!')
		];
	}

	private function getListLangSupportDeepL() {
		$list = Configuration::get('ETS_TRANS_LIST_LANG_TARGET_DEEPL');
		if (!$list) {
			$endpoint = $this->getEndpointDeepLApi('languages');
			$header = array();
			$header[] = 'Content-type: application/json';
			$header[] = 'X-HTTP-Method-Override: GET';
			$header[] = 'Authorization: DeepL-Auth-Key ' . $this->apiKey;
			$request = [
				'type' => 'target'
			];
			$res = $this->request(
				'GET',
				$endpoint,
				[],
				$header,
				$request
			);
			if ($res) {
				$list = $res;
				Configuration::updateValue('ETS_TRANS_LIST_LANG_TARGET_DEEPL', $res);
			}
		}
		return json_decode($list, true);
	}

	private function getListLangSupportYandex() {
		$list = Configuration::get('ETS_TRANS_LIST_LANG_TARGET_YANDEX');
		if (!$list) {
			$endpoint = $this->getEndpointYandexApi('getLangs');
			$header = array();
			$header[] = 'Host: translate.yandex.net';
			$header[] = 'Accept: */*';
			$header[] = 'Content-Type: application/x-www-form-urlencoded';
			$request = [
				'key' => $this->apiKey,
				'ui' => 'en'
			];
			$res = $this->request(
				'POST',
				$endpoint,
				[],
				$header,
				$request
			);
			if ($res) {
				$list = $res;
				Configuration::updateValue('ETS_TRANS_LIST_LANG_TARGET_YANDEX', $res);
			}
		}
		return json_decode($list, true);
	}

}