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

//if (!defined('_PS_VERSION_')) { exit; }

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/ajax.init.php');


$context = Context::getContext();

/** @var Ets_Translate|null $et */
$et = Module::getInstanceByName('ets_translate');

if(!$context->employee || !$context->employee->id || strip_tags(Tools::getValue('token')) !== Tools::getAdminTokenLite('AdminModules'))
{
    return false;
}

if (Tools::isSubmit('etsTransOpenChatGPT')) {
	$formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : '';
	$result = $et->getFormChatGPT($formData);
	if ($result) {
		die(json_encode(array(
			'success' => true,
			'form' => $result,
		)));
	}
	die(json_encode(array(
		'success' => false,
		'form' => '',
		'message' => $et->l('Cannot load chat form!')
	)));
}

if (Tools::isSubmit('etsTransClearDataChat')) {
	$result = $et->clearDataChatGpt();
	die(json_encode(array(
		'success' => !$result['errors'],
		'errors' => $result['errors'],
		'message' => isset($result['message']) ? $result['message'] : 'Unknown error!'
	)));
}

if (Tools::isSubmit('etsTransSubmitChatGPT')) {
	$formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : '';
	$result = $et->submitChatGPT($formData);
	if ($result) {
		die(json_encode($result));
	}
	die(json_encode(array(
		'success' => !$result['errors'],
		'errors' => $result['errors'],
		'data'=> $result['data'],
		'message' => isset($result['message']) ? $result['message'] : 'Error unknown!'
	)));
}

if(Tools::isSubmit('etsTransGetFormTranslate')){
    $pageId = ($pageId = Tools::getValue('pageId')) && (is_array($pageId) || Validate::isCleanHtml($pageId)) ? $pageId : array();
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType);
    $form = $et->getFormTrans($pageId, $pageType);
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
if(Tools::isSubmit('etsTransTranslatePage')){
    $formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : '';
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : '';
    $isDetailPage = (int)Tools::getValue('isDetailPage');
    $result = $et->translateDataPage($formData, $pageType, $isDetailPage);

    die(json_encode(array(
        'success' => !$result['errors'],
        'errors' => $result['errors'],
        'trans_data'=> $result['data'],
        'no_trans' => isset($result['noTrans']) && $result['noTrans'],
        'message' => isset($result['message']) ? $result['message'] : 'Error unknown!'
    )));
}
if(Tools::isSubmit('etsTransPauseTranslate')){
    $transInfo = ($transInfo = Tools::getValue('transInfo')) && is_array($transInfo) ? $transInfo : array();
    if($et->saveDataAfterPause($transInfo)){
        die(json_encode(array(
            'success' => true,
        )));
    }
    die(json_encode(array(
        'success' => false,
    )));
}

if(Tools::isSubmit('etsTransDeleteDataPause')){
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : '';
    $selectedTheme = ($selectedTheme = Tools::getValue('selected_theme')) && Validate::isCleanHtml($selectedTheme) ? $selectedTheme : '';
    if($pageType && Validate::isCleanHtml($pageType) && $selectedTheme && Validate::isCleanHtml($selectedTheme
        )){
        if($et->deleteDataPause($pageType, $selectedTheme)){
            die(json_encode(array(
                'success' => true,
            )));
        }
    }

    die(json_encode(array(
        'success' => false,
    )));
}
if(Tools::isSubmit('etsTransAnalyzing')){
    $isLocalization = (int)Tools::getValue('isLocalization');
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : null;
    $formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : array();
    $offset = ($offset = Tools::getValue('offset')) && Validate::isCleanHtml($offset) ? $offset : null;
    if($isLocalization){
        $resultAnalysis = $et->analyzeBeforeTranslateLz(
            $pageType,
            $formData,
            ($step = Tools::getValue('step')) && Validate::isCleanHtml($step) ? $step : 0,
            ($selected = Tools::getValue('selected')) && Validate::isCleanHtml($selected) ? $selected : 'classic',
            ($sfType = Tools::getValue('sfType')) && Validate::isCleanHtml($sfType) ? $sfType : '',
            (int)Tools::getValue('isLoadFile'),
            (int)Tools::getValue('resetData')
        );
    }
    else
        $resultAnalysis = $et->analyzeBeforeTranslate($pageType,$formData, $offset);
    if($resultAnalysis && (!isset($resultAnalysis['errors']) || !$resultAnalysis['errors'])){
        die(json_encode(array(
            'success' => true,
            'data' => $resultAnalysis
        )));
    }
    die(json_encode(array(
        'success' => false,
        'errors' => isset($resultAnalysis['errors']) ? $resultAnalysis['errors'] : 'Has an error',
        'data' => array()
    )));
}

if(Tools::isSubmit('etsTransAllLoadFile')){
    $forData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : array();
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : array();
    $result = $et->loadFileTranslateAll($formData, $pageType);
    if(isset($result['errors']) && $result['errors']){
        die(json_encode(array(
            'success' => false,
            'errors' => isset($result['message']) ? $result['message'] : 'Error'
        )));
    }
    die(json_encode(array(
        'success' => true,
        'total_item' => isset($result['total_item']) ? $result['total_item'] : 0,
    )));

}
if(Tools::isSubmit('etsTransTransAll')){
    $formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : array();
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : 'all';
    $result = $et->translateAllWebData($formData, $pageType);
    if(!isset($result['errors']) || !$result['errors']){
        if(isset($result['stop_translate']) && $result['stop_translate']){
            $ec = EtsTransConfig::getInstance();
            $ec->deletePauseData($pageType);
        }
        die(json_encode(array(
            'success' => true,
            'data' => $result
        )));
    }
    die(json_encode(array(
        'success' => false,
        'errors' => isset($result['message']) ? $result['message'] : 'Error',
	    'result' => $result
    )));
}

if(Tools::isSubmit('etsTransAnalyzingAllPage')){
    $formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : array();
    $pageType = ($pageType = Tools::getValue('pageType')) && Validate::isCleanHtml($pageType) ? $pageType : 'all';
    $offset = ($offset = Tools::getValue('offset')) && Validate::isCleanHtml($offset) ? $offset : 0;
    if($isInit = Tools::getValue('init'))
    {
        $et->analyzingAllPage($pageType, $formData, $offset, true);
        die(json_encode(array(
            'success' => true,
            'after_init' => 1
        )));
    }
    else{
        $resultAnalysis = $et->analyzingAllPage($pageType, $formData, $offset, false);
        die(json_encode(array(
            'success' => true,
            'data' => $resultAnalysis
        )));
    }
}

if(Tools::isSubmit('etsTransMegamenu') || Tools::isSubmit('etsTransBlog') || Tools::isSubmit('etsTransModulePc')){
    $formData = ($formData = Tools::getValue('formData')) && is_array($formData) ? $formData : array();
    $isDetailPage = 1;
    $pageType = Tools::isSubmit('etsTransMegamenu') ? 'megamenu' : 'blog';
    if(Tools::isSubmit('etsTransModulePc')){
        $pageType = 'pc';
    }

    if((int)Tools::getValue('isTransAll') || (isset($formData['page_id']) && $formData['page_id'] && !isset($formData['col_data']) && in_array($pageType, array('megamenu', 'blog', 'pc')))){
	    $result = [];
        if($pageType == 'megamenu'){
            $result = $et->translateAllMegamenu($formData);
        }
        elseif($pageType == 'blog'){
            $result = $et->translateAllBlog($formData);
        }
        elseif($pageType == 'pc'){
            $pcType = ($pcType = Tools::getValue('pcType')) && Validate::isCleanHtml($pcType) ? $pcType : '';
            $result = $et->translateAllModulePc($formData, $pcType);
        }
        die(json_encode(array(
            'success' => !(isset($result['errors']) && $result['errors']),
            'errors' => isset($result['errors']) && $result['errors'],
            'data'=> $result,
            'no_trans' => false,
            'message' => isset($result['message']) ? $result['message'] : ''
        )));
    }
    else{
        $result = $et->translateDataPage($formData, $pageType, $isDetailPage);
        die(json_encode(array(
            'success' => !$result['errors'],
            'errors' => $result['errors'],
            'trans_data'=> $result['data'],
            'no_trans' => isset($result['noTrans']) && $result['noTrans'],
            'message' => isset($result['message']) ? $result['message'] : ''
        )));
    }
}

