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

class EtsTransPage
{
    public static $instance;

    public static $_FIELDS_TRANS_CATEGORIES = ['name', 'description', 'additional_description', 'meta_title', 'meta_description', 'meta_keywords'];
    public static $_FIELDS_TRANS_CMS = ['meta_title', 'head_seo_title', 'meta_description', 'meta_keywords', 'content'];

    public static function translate($langSource, $dataTrans, $pageType = null, $isNewBlockreassurance = false, $colData = array(), $pageId = 0, $extraOptions = array(), $transFields = array(), $image_id = null, $transOption = null, $autoDetectLanguage = null)
    {
        if(!isset($dataTrans['source']) || !isset($dataTrans['target'])){
            return false;
        }
        $sources = $dataTrans['source'];
        $resultTrans = array();
        $resultTextTrans = array();
        $api = EtsTransApi::getInstance();
        $textLog = '';
        $addTextLog = true;
        $extraOptions['lang_source'] = $langSource;
        if ($pageType == 'product'){
            $product = new Product($pageId);
        }
        if (!count($transFields) && $pageType == 'product') {
        	$transFields = EtsTransDefine::getListFieldsProductTrans(true);
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        foreach ($dataTrans['target'] as $idLang => $targetItem)
        {
	        $timStartTrans = microtime(true);
	        $textTrans = array();
	        $keyTrans = array();
	        $langCodeSource = $langSource && !$autoDetectLanguage ? Language::getIsoById($langSource) : null;
	        $langCodeTarget = Language::getIsoById($idLang);
	        foreach ($targetItem as $key => $isTrans) {
		        if ((int)$isTrans && ((isset($sources[$key]) && $sources[$key]) || (isset($colData[$key]) && $colData[$key] == 'legend'))) {
			        if ($pageType == 'product' && isset($product) && $product->id && in_array($colData[$key], $transFields)) {
				        if (($colData[$key] == 'legend' && $image_id) || $colData[$key] != 'legend') {
					        $textTrans[] = $sources[$key];
					        $keyTrans[] = $key;
				        }
				        if ($colData[$key] == 'legend' && !$image_id && $transOption) {
					        $textTransFormat = self::getTextTrans([$product], [$colData[$key]], $idLang, $langSource, $transOption, 'product');
					        $_keyTrans = $textTransFormat['keyTrans'];
					        $_textTrans = $textTransFormat['textTrans'];
					        foreach ($_textTrans as $kt => $_text) {
						        $textTrans[] = $_text;
						        $keyTrans[] = $_keyTrans[$kt];
						        $colData[$_keyTrans[$kt]] = $colData[$key];
					        }
				        }
			        } else if ($pageType != 'product') {
				        $textTrans[] = $sources[$key];
				        $keyTrans[] = $key;
			        }
			        if ($addTextLog) {
				        $textLog .= preg_replace('/[\s]{2,}/', ' ', str_replace("\r\n", " ", $sources[$key]));
				        if (Tools::strlen($textLog) > 50) {
					        $addTextLog = false;
				        }
			        }
		        }
	        }
	        if ($langCodeTarget) {
		        $res = $api->translate($langCodeSource, $langCodeTarget, $textTrans, $pageType);
		        if (!$res['errors']) {
			        foreach ($textTrans as $key => $item) {
				        $resultTrans[$idLang][$keyTrans[$key]] = isset($res['data'][$key]) ? $res['data'][$key] : '';
				        $resultTextTrans[$idLang][$item] = isset($res['data'][$key]) ? $res['data'][$key] : '';
			        }
                    EtsTransLog::logTranslate($pageType, true, $timStartTrans, $langSource, $idLang, EtsTransModule::getTextLog($textTrans), null,null, null);
                }
                else{
                    EtsTransLog::logTranslate($pageType, false, $timStartTrans, $langSource, $idLang, EtsTransModule::getTextLog($textTrans), null,null, isset($res['message']) ? $res['message'] : null);

                    return array(
                        'text_log' => strip_tags(rtrim($textLog, ';')),
                        'result' => $res
                    );
                }
            }
        }
	    if ($langSource && in_array($pageType, ['product', 'cms', 'ets_extraproducttabs'])) {
	    	$has_source_data_in_result_trans = false;
	    	foreach ($resultTrans as $key => $resultTran) {
				if ($key == $langSource) {
					$has_source_data_in_result_trans = true;
					break;
				}
		    }
	    	if (!$has_source_data_in_result_trans) {
			    $resultTrans[$langSource] = $dataTrans['source'];
		    }
	    }
        $savePsPageSuccess = false;
        if($pageType && $colData && $pageId){
            $savePsPageSuccess = self::updateOnDB($pageType, $pageId, $colData, $resultTrans, $isNewBlockreassurance, $extraOptions, $image_id);
        }

        return array(
            'text_log' => strip_tags(rtrim($textLog, ';')),
            'result' => $resultTrans,
            'resultText' => $resultTextTrans,
            'dataSaved' => $savePsPageSuccess
        );
    }

    public static function updateOnDB($pageType, $pageId, $colData, $transData, $isNewBlockreassurance, $extraOptions = array(), $image_id = null)
    {
        $context = Context::getContext();
	    $fieldName = '';
        switch ($pageType){
            case 'product':
                $obj = new Product($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'link_rewrite';
                if (isset($extraOptions['ignore_product_name']) && $extraOptions['ignore_product_name']){
                    foreach ($transData as $idLang=>$textTrans){
                        if (isset($extraOptions['lang_source']) && isset($obj->name[$extraOptions['lang_source']]) && $obj->name[$extraOptions['lang_source']])
                            $obj->name[$idLang] = $obj->name[$extraOptions['lang_source']];
                    }
                }
                break;
            case 'category':
                $obj = new Category($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'link_rewrite';
                break;
            case 'cms':
                $obj = new CMS($pageId);
                $fieldName = 'meta_title';
                $fieldRewrite = 'link_rewrite';
                break;
            case 'cms_category':
                $obj = new CMSCategory($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'link_rewrite';
                break;
            case 'manufacturer':
                $obj = new Manufacturer($pageId);
                break;
            case 'supplier':
                $obj = new Supplier($pageId);
                break;
            case 'attribute_group':
                $obj = new AttributeGroup($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'url_name';
                break;
            case 'attribute':
                $obj = new Attribute($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'url_name';
                break;
            case 'feature':
                $obj = new Feature($pageId);
                $fieldName = 'name';
                $fieldRewrite = 'url_name';
                break;
            case 'feature_value':
                $obj = new FeatureValue($pageId);
                $fieldName = 'value';
                $fieldRewrite = 'url_name';
                break;
            default:
                $obj = null;
                break;
        }
        if(!in_array($pageType, array('blockreassurance', 'ps_linklist', 'ps_mainmenu', 'ps_customtext', 'ps_imageslider', 'ets_extraproducttabs', 'combination_product')) && (!isset($obj) || !$obj || !$obj->id)){
            return false;
        }

        $transLinkRewrite = isset($extraOptions['auto_generate_link_rewrite']) ? (int)$extraOptions['auto_generate_link_rewrite'] : (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');

        if($pageType == 'attribute' && ($attrLangData = self::getAttributeLangData($pageId))){
            foreach ($attrLangData as $attrLangDataItem)
            {
                foreach ($attrLangDataItem as $ka=>$va){
                    if($ka !== 'id_lang'){
                        ${'_GET'}[$ka.'_'.$attrLangDataItem['id_lang']] = $va;
                    }
                }
            }
        }
        if($pageType == 'feature_value' && ($featureLangData = self::getFeatureValueLang($pageId))){
            foreach ($featureLangData as $featureLangDataItem)
            {
                foreach ($featureLangDataItem as $ka=>$va){
                    if($ka !== 'id_lang'){
                        ${'_GET'}[$ka.'_'.$featureLangDataItem['id_lang']] = $va;
                    }
                }
            }
        }


        foreach ($transData as $idLang=>$textTrans){
            foreach ($textTrans as $key=>$text){
                if($pageType == 'blockreassurance'){
                    if($isNewBlockreassurance){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."psreassurance_lang` SET `".pSQL($colData[$key])."`='".pSQL($text)."' WHERE id_psreassurance=".(int)$pageId." AND id_lang=".(int)$idLang);
                    }
                    else
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."reassurance_lang` SET `text`='".pSQL($text)."' WHERE id_reassurance=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                elseif($pageType == 'ps_linklist'){
                    if($key == 'form_link_block_block_name_'){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."link_block_lang` SET `name`='".pSQL($text)."' WHERE id_link_block=".(int)$pageId." AND id_lang=".(int)$idLang);
                    }
                    elseif(strpos($key, 'form_link_block_custom_') !== false){

                        $linkBlockItem = Db::getInstance()->getRow("SELECT `custom_content` FROM `"._DB_PREFIX_."link_block_lang` WHERE id_link_block=".(int)$pageId." AND id_lang=".(int)$idLang);
                        if($linkBlockItem){
                            $customContent  = $linkBlockItem['custom_content'];
                            if($customContent){
                                $customContent = json_decode($customContent, true);
                            }
                            else{
                                $customContent = array();
                            }
                            preg_match('/^form_link_block_custom_([0-9]+)_([0-9]+)_title$/', 'form_link_block_custom_0_1_title', $matchesLinkBlock);
                            $indexLb = isset($matchesLinkBlock[1]) ? $matchesLinkBlock[1] : null;
                            $idLangLb  = isset($matchesLinkBlock[2]) ? $matchesLinkBlock[1] : null;
                            if($indexLb !== null && $idLangLb !== null){
                                if(isset($customContent[(int)$indexLb])){
                                    $customContent[(int)$indexLb] = array(
                                        'title' =>$text,
                                        'url' => $transLinkRewrite && isset($extraOptions['lang_source']) && $extraOptions['lang_source'] != $idLang ? Tools::str2url($text) : $customContent[(int)$indexLb]['url']
                                    );
                                }
                                else{
                                    $customContent[] = array(
                                        'title' =>$text,
                                        'url' => $transLinkRewrite && isset($extraOptions['lang_source']) && $extraOptions['lang_source'] != $idLang ? Tools::str2url($text) : ''
                                    );
                                }
                            }

                            Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."link_block_lang` SET `custom_content`='".pSQL(json_encode($customContent), true)."' WHERE id_link_block=".(int)$pageId." AND id_lang=".(int)$idLang);
                        }
                    }
                    continue;
                }
                elseif ($pageType == 'ps_mainmenu'){
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."linksmenutop_lang` SET `label`='".pSQL($text)."' WHERE id_linksmenutop=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                elseif ($pageType == 'ps_customtext'){
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."info_lang` SET `text`='".pSQL($text, true)."' WHERE id_lang=".(int)$idLang)." AND id_shop=".(int)$context->shop->id;
                    continue;
                }
                elseif ($pageType == 'ps_imageslider'){
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."homeslider_slides_lang` SET `".pSQL($colData[$key])."`='".pSQL($text, true)."' WHERE id_homeslider_slides=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                elseif ($pageType == 'ets_extraproducttabs'){
                    if(strpos($key, 'content') !== false){
                        $key = 'content_';
                    }
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_ept_tab_lang` SET `".pSQL($colData[$key])."`='".pSQL($text, true)."' WHERE id_ets_ept_tab=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                elseif ($pageType == 'combination_product' && $pageId) {
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."product_attribute_lang` SET `".pSQL($colData[$key])."`='".pSQL($text, true)."' WHERE id_product_attribute=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                if($pageType == 'attribute_group' && $key == 'meta_title_'){
                    self::createAttributeGroupIndexable($pageId);
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` SET meta_title='".pSQL($text)."' WHERE id_attribute_group=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                elseif($pageType == 'feature' && $key == 'meta_title_'){
                    self::createFeatureIndexable($pageId);
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_feature_lang_value` SET meta_title='".pSQL($text)."' WHERE id_feature=".(int)$pageId." AND id_lang=".(int)$idLang);
                    continue;
                }
                else if($pageType == 'attribute' && $key == 'meta_title_'){
                    ${'_GET'}['meta_title_'.(int)$idLang] = $text;
                    continue;
                }
                else if($pageType == 'feature_value' && $key == 'meta_title_'){
                    ${'_GET'}['meta_title_'.(int)$idLang] = $text;
                    continue;
                }
                if(isset($colData[$key]) && $colData[$key]){
                    if(strpos($key, 'keywords') !== false){
                        $obj->{$colData[$key]}[$idLang] = str_replace('|', ',', $text);
                    }
                    else {
                        if ($pageType == 'product' && strpos($key, 'ets_ept_content') !== false) {
                            $tabId = str_replace('ets_ept_content_', '', $key);
                            $tabId = str_replace('_', '', $tabId);
                            EtsTransModule::updateContentProductTab((int)$tabId, $pageId, $transData, 'content', $idLang, $text);
                        }
                        else if ($pageType == 'product' && strpos($key, 'ets_ept_file_desc') !== false) {
                            $tabId = str_replace('ets_ept_file_desc_', '', $key);
                            $tabId = str_replace('_', '', $tabId);
                            EtsTransModule::updateContentProductTab((int)$tabId, $pageId, $transData, 'file_desc', $idLang, $text);
                        }
                        else if ($pageType == 'product' && strpos($key, 'legend') !== false) {
                        	if ($image_id) {
		                        Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "image_lang` SET `" . pSQL($colData[$key]) . "`='" . pSQL($text) . "' WHERE id_image=" . (int)$image_id . " AND id_lang=" . (int)$idLang);
	                        } else {
		                        $keySplit = explode('.', $key);
		                        if (count($keySplit) == 2) {
		                        	$_image_field = $keySplit[1];
		                        	$_image_id = $keySplit[0];
			                        Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "image_lang` SET `" . pSQL($_image_field) . "`='" . pSQL($text) . "' WHERE id_image=" . (int)$_image_id . " AND id_lang=" . (int)$idLang);
		                        }
	                        }
                        } else if ($pageType == 'product' && in_array($key, ['product_combinations_availability_available_now_label_', 'product_combinations_availability_available_later_label_'])) {
                            $arr = [
                                'product_combinations_availability_available_now_label_' => 'available_now',
                                'product_combinations_availability_available_later_label_' => 'available_later',
                            ];
                            Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "product_lang` SET `" . pSQL($arr[$key]) . "`='" . pSQL($text) . "' WHERE id_product=" . (int)$obj->id . " AND id_lang=" . (int)$idLang . " AND id_shop=" . (int)$context->shop->id);
                        }
                        else if ($pageType == 'product' && $key == 'product_seo_tags_') {
                            $idProductTag = Db::getInstance()->getValue("SELECT GROUP_CONCAT(id_tag) FROM `" . _DB_PREFIX_ . "product_tag` WHERE id_product=" . (int)$obj->id . " AND id_lang=" . (int)$idLang);
                            if ($idProductTag) {
                                Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "product_tag`  WHERE id_product=" . (int)$obj->id . " AND id_lang=" . (int)$idLang);
                                Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "tag` WHERE id_tag IN (" . pSQL($idProductTag) . ")");
                            }
                            $arrayValues = explode(',', $text);
                            foreach ($arrayValues as $value) {
                                $data = [$colData[$key] => pSQL(trim($value)), 'id_lang' => (int)$idLang];
                                if (Db::getInstance()->insert('tag', $data)) {
                                    $data = ['id_product' => (int)$obj->id, 'id_lang' => (int)$idLang, 'id_tag' => (int)Db::getInstance()->Insert_ID()];
                                    Db::getInstance()->insert('product_tag', $data);
                                }
                            }
                        }
                        else {
                            $obj->{$colData[$key]}[$idLang] = $text;
                        }
                    }
                    if($pageType == 'attribute' || $pageType == 'feature_value'){
                        ${'_GET'}[$colData[$key].'_'.$idLang] = $obj->{$colData[$key]}[$idLang];
                        if($transLinkRewrite && isset($extraOptions['lang_source']) && $extraOptions['lang_source'] != $idLang && isset($fieldRewrite) && $colData[$key] == $fieldName){
                            ${'_GET'}[$fieldRewrite.'_'.$idLang] = Tools::str2url($obj->{$colData[$key]}[$idLang]);
                        }
                    }
                    elseif($transLinkRewrite && isset($fieldRewrite) && $colData[$key] == $fieldName){
                        if($pageType == 'attribute_group'){
                            Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` SET url_name='".pSQL(Tools::str2url($obj->{$colData[$key]}[$idLang]))."' WHERE id_attribute_group=".(int)$pageId." AND id_lang=".(int)$idLang);
                        }
                        elseif($pageType == 'attribute_group'){
                            Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_feature_lang_value` SET url_name='".pSQL(Tools::str2url($obj->{$colData[$key]}[$idLang]))."' WHERE id_feature=".(int)$pageId." AND id_lang=".(int)$idLang);
                        }
                        else{
                            $obj->{$fieldRewrite}[$idLang] = Tools::str2url($obj->{$colData[$key]}[$idLang]);
                        }
                    }

                }
            }
        }
        return isset($obj) && $obj->update() ? true : (!isset($obj) ? true : false);
    }

    public static function transProduct($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $transFields = array(), $autoDetectLanguage = null){
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!$ids){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $products = array();
        foreach ($ids as $id)
        {
            $products[] = new Product((int)$id);
        }
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        if (!count($transFields))
	        $transFields = EtsTransDefine::getListFieldsProductTrans(true);
        if (isset($extraOptions['ignore_product_name']) && $extraOptions['ignore_product_name']){
	        if (($key = array_search('name', $transFields)) !== false) {
		        unset($transFields[$key]);
	        }
	        $extraOptions['field_ignore_name'] = $transFields;
        }
        $strTranslatedLength = 0;
        if(!is_array($targetLang)){
            $targetLang = explode(',', $targetLang);
        }
        $nbTextTranslated = 0;
        foreach ($targetLang as $index => $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($products, $transFields,$idLang,$sourceLang,$transOption, 'product', $extraOptions);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'product');
            if(!$res['errors'] && $res['data']){
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $translatedText = $res['data'];
                $productUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $productId = $keySplit[0];
                    $productField = $keySplit[1];
	                if(!Validate::isCleanHtml($translatedText[$key],true))
		                continue;
					if ($productField == 'legend') {
						$image = new Image($productId);
						$image->{$productField}[$idLang] = $translatedText[$key];
						if (!$image->validateFieldsLang(false))
							continue;
						$image->ets_trans = 1;
						$image->save();
						continue;
					}
	                try {
		                if(!isset($productUpdate[$productId])){
			                $p = new Product($productId);
			                if (isset($extraOptions) && $extraOptions['ignore_product_name']){
				                $p->name[$idLang] = $p->name[$sourceLang];
				                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($p->name[$idLang])){
					                $p->link_rewrite[$idLang] = $urlRewrite;
				                }
			                }
			                $productUpdate[$productId] = $p;
			                if(strpos($productField, 'keywords') !== false){
				                $p->{$productField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$productField}[$idLang] = $translatedText[$key];
				                if($productField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$productUpdate[$productId];
			                if(strpos($productField, 'keywords') !== false){
				                $p->{$productField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$productField}[$idLang] = $translatedText[$key];
				                if($productField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if (isset($keyTrans[$key + 1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if ($keySplit2[0] != $productId) {
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                } else {
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }

                        EtsTransLog::logTranslate('product', true, $timStartTrans, $sourceLang, $idLang, $translatedText[$key], EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
	                } catch (\PrestaShopException $exception) {
		                EtsTransLog::logTranslate('product', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($productUpdate);
            }
            else{
                EtsTransLog::logTranslate('product', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                return $res;
            }
        }
        $transDataExtra = self::transExtraTabProduct($ids, $sourceLang, $targetLang, $transOption);
        if ($transDataExtra){
            $strTranslatedLength += (int)$transDataExtra['nb_str'];
            $nbTextTranslated += (int)$transDataExtra['nb_text'];
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function transExtraTabProduct($productIds, $sourceLangId, $targetLangIds, $transOption)
    {
        if (!Module::isInstalled('ets_extraproducttabs')){
            return 0;
        }
        $productTabs = Db::getInstance()->executeS("
            SELECT ep.*, et.is_global FROM `"._DB_PREFIX_."ets_ept_product` ep 
            JOIN `"._DB_PREFIX_."ets_ept_tab` et ON ep.id_ets_ept_tab = et.id_ets_ept_tab
            WHERE ep.id_product IN (".implode(',', array_map('intval', $productIds)).") AND et.enable = 1
        ");

        if (!$productTabs) return 0;

        $texts = [];
        $tabLang = Db::getInstance()->executeS("SELECT `id_ets_ept_tab`, `content`, `file_desc` FROM `"._DB_PREFIX_."ets_ept_tab_lang` WHERE id_lang=".(int)$sourceLangId);
        $dataTabText = [];
        foreach ($tabLang as $item){
            $dataTabText[$item['id_ets_ept_tab']] = array(
                'content' => $item['content'],
                'file_desc' => $item['file_desc'],
            );
        }
        foreach ($productTabs as $item){
            if (!isset($dataTabText[$item['id_ets_ept_tab']])){
                continue;
            }
            if ($item['id_lang'] == $sourceLangId){
                $texts[$item['id_ets_ept_tab'].".".$item['id_product'].".content"] = (int)$item['is_global'] && (int)$item['use_global_content'] && isset($dataTabText[$item['id_ets_ept_tab']]['content']) ? trim($dataTabText[$item['id_ets_ept_tab']]['content']) : trim($item['content']);
                $texts[$item['id_ets_ept_tab'].".".$item['id_product'].".file_desc"] = (int)$item['is_global'] && (int)$item['use_global_content'] && isset($dataTabText[$item['id_ets_ept_tab']]['file_desc']) ? trim($dataTabText[$item['id_ets_ept_tab']]['file_desc']) :  trim($item['file_desc']);
            }
        }
        if(!is_array($targetLangIds)){
            $targetLangIds = explode(',', $targetLangIds);
        }

        if ($texts){
            $strTranslatedLength = 0;
            $nbTextTranslated = 0;

            foreach ($targetLangIds as $langId) {
                $textTrans = [];
                $keyTrans = [];
                foreach ($productTabs as $item) {
                    if ($item['id_lang'] == $langId){
                        if ((int)$item['is_global'] && (int)$item['use_global_content']){
                            continue;
                        }
                        $kContent = $item['id_ets_ept_tab'].".".$item['id_product'].".content";
                        $kFileDesc = $item['id_ets_ept_tab'].".".$item['id_product'].".file_desc";

                        if (!isset($texts[$kContent]))
                            continue;
                        switch ($transOption){
                            case 'both':
                                if ((isset($texts[$kContent]) && $texts[$kContent] && $texts[$kContent] == trim($item['content'])) || !trim($item['content']) ){
                                    $textTrans[] = $texts[$kContent];
                                    $keyTrans[] = $kContent;
                                }
                                if ((isset($texts[$kFileDesc]) && $texts[$kFileDesc] && $texts[$kFileDesc] == trim($item['file_desc'])) || !trim($item['file_desc']) ){
                                    $textTrans[] = $texts[$kFileDesc];
                                    $keyTrans[] = $kFileDesc;
                                }
                                break;
                            case 'only_empty':
                                if (isset($texts[$kContent]) && $texts[$kContent] && (!$item['content'] || !trim($item['content'])) ){
                                    $textTrans[] = $texts[$kContent];
                                    $keyTrans[] = $kContent;
                                }
                                if (isset($texts[$kFileDesc]) &&$texts[$kFileDesc] && (!$item['file_desc'] || !trim($item['file_desc'])) ){
                                    $textTrans[] = $texts[$kFileDesc];
                                    $keyTrans[] = $kFileDesc;
                                }
                                break;
                            case 'same_source':
                                if ((isset($texts[$kContent]) && $texts[$kContent] && $texts[$kContent] && $texts[$kContent] == trim($item['content']))){
                                    $textTrans[] = $texts[$kContent];
                                    $keyTrans[] = $kContent;
                                }
                                if ((isset($texts[$kFileDesc]) && $texts[$kFileDesc]&& $texts[$kContent] == trim($item['file_desc']))){
                                    $textTrans[] = $texts[$kFileDesc];
                                    $keyTrans[] = $kFileDesc;
                                }
                                break;
                            default:
                                if ($texts[$kContent]) {
                                    $textTrans[] = $texts[$kContent];
                                    $keyTrans[] = $kContent;
                                }
                                if ($texts[$kFileDesc]) {
                                    $textTrans[] = $texts[$kFileDesc];
                                    $keyTrans[] = $kFileDesc;
                                }
                                break;
                        }

                    }
                }
                if(!$nbTextTranslated){
                    $nbTextTranslated = count($textTrans);
                }

                if (!$textTrans) continue;

                $res = EtsTransApi::getInstance()->translate(Language::getIsoById((int)$sourceLangId), Language::getIsoById((int)$langId),$textTrans);
                if (!$res['errors'] && $res['data']) {
                    $strTranslatedLength+= self::countTextTranslated($textTrans);
                    $translatedText = $res['data'];
                    foreach ($keyTrans as $k => $item){
                        $dataTab = explode('.', $item);
                        if (!isset($translatedText[$k]) || count($dataTab) < 2)
                            continue;
                        $idTab = $dataTab[0];
                        $idProduct = $dataTab[1];
                        $field = $dataTab[2];
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_ept_product` SET `".pSQL($field)."` = '".pSQL($translatedText[$k], true)."' WHERE `id_ets_ept_tab`=".(int)$idTab." AND `id_product`=".(int)$idProduct." AND id_lang=".(int)$langId);
                    }
                }
            }
            return ['nb_text' => $nbTextTranslated, 'nb_str' => $strTranslatedLength];
        }
        return 0;
    }

    public static function transAllProduct($sourceLang, $targetLang, $transOption, $offset = 0, $extraOptions = array(), $transFields = array(), $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_product` FROM `"._DB_PREFIX_."product_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_product'];
        }
        $result = self::transProduct($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $transFields, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllCategory($sourceLang, $targetLang, $transOption, $offset = 0, $extraOptions = array(), $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_category` FROM `"._DB_PREFIX_."category_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_category'];
        }
        $result = self::translateCategory($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllCMS($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_cms` FROM `"._DB_PREFIX_."cms_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_cms'];
        }
        $result = self::translateCMS($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllCMSCategory($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_cms_category` FROM `"._DB_PREFIX_."cms_category_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);

        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_cms_category'];
        }
        $result = self::translateCMSCategory($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllManufacturer($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_manufacturer` FROM `"._DB_PREFIX_."manufacturer_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);

        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_manufacturer'];
        }
        $result = self::translateManufacturer($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllSupplier($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_supplier` FROM `"._DB_PREFIX_."supplier_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_supplier'];
        }
        $result = self::translateSupplier($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllBlockReassurance($sourceLang, $targetLang, $transOption, $offset=0, $isNewBlockreassurance = false, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 10;
        $moduleObj = Module::getInstanceByName('blockreassurance');
        if($isNewBlockreassurance) {
	        $check = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."psreassurance` LIKE 'id_shop'");
	        if ($check && count($check)) {
		        $idsData = Db::getInstance()->executeS("SELECT `id_psreassurance` FROM `" . _DB_PREFIX_ . "psreassurance` WHERE `id_shop`=" . (int)$context->shop->id . " LIMIT " . (int)$offset . ", " . (int)$limit);
	        } else {
		        $idsData = Db::getInstance()->executeS("SELECT `id_psreassurance` FROM `" . _DB_PREFIX_ . "psreassurance` LIMIT " . (int)$offset . ", " . (int)$limit);
	        }
            $idCol = 'id_psreassurance';
        }
        else {
            $idsData = Db::getInstance()->executeS("SELECT `id_reassurance` FROM `" . _DB_PREFIX_ . "reassurance` WHERE `id_shop`=" . (int)$context->shop->id . " LIMIT " . (int)$offset . ", " . (int)$limit);
            $idCol = 'id_reassurance';
        }
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item[$idCol];
        }
        $result = self::translateBlockReassurance($ids, $sourceLang, $targetLang, $transOption, $isNewBlockreassurance, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllLinkList($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $autoDetectLanguage = null)
    {
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_link_block` FROM `"._DB_PREFIX_."link_block` LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_link_block'];
        }
        $result = self::translateLinkList($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }
    public static function translateAllMainMenu($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_linksmenutop` FROM `"._DB_PREFIX_."linksmenutop` WHERE id_shop=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_linksmenutop'];
        }
        $result = self::translateMainMenu($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllImageSliders($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_homeslider_slides` FROM `"._DB_PREFIX_."homeslider` WHERE id_shop=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_homeslider_slides'];
        }
        $result = self::translateImageSliders($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }
    public static function translateAllExtraProductTabs($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_ets_ept_tab` FROM `"._DB_PREFIX_."ets_ept_tab` WHERE id_shop=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_ets_ept_tab'];
        }
        $result = self::translateExtraProductTabs($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllAttributeGroup($sourceLang, $targetLang, $transOption, $offset=0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 2;
        $idsData = Db::getInstance()->executeS("SELECT `id_attribute_group` FROM `"._DB_PREFIX_."attribute_group_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_attribute_group'];
        }
        $result = self::translateAttributeGroup($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
	    $result['trans_page_type_value'] = false;
	    $stopTranslate = false;
	    if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
		    if (isset($result['errors']) && $result['errors'])
			    $stopTranslate = true;
		    $result['trans_page_type_value'] = true;
	    }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }
    public static function translateAllAttribute($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $idAttributeGroup = 0, $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 10;
        $idsData = Db::getInstance()->executeS("
            SELECT attrs.`id_attribute` FROM `"._DB_PREFIX_."attribute_shop` attrs 
            LEFT JOIN `"._DB_PREFIX_."attribute` a ON attrs.id_attribute=a.id_attribute
            WHERE attrs.`id_shop`=".(int)$context->shop->id.($idAttributeGroup ? " AND a.id_attribute_group=".(int)$idAttributeGroup : "")." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_attribute'];
        }
        $result = self::translateAttribute($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllFeature($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $autoDetectLanguage = null)
    {
        $context = Context::getContext();
        $limit = 10;
        $idsData = Db::getInstance()->executeS("SELECT `id_feature` FROM `"._DB_PREFIX_."feature_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_feature'];
        }
        $result = self::translateFeature($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $result['trans_page_type_value'] = false;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
        	if (isset($result['errors']) && $result['errors'])
                $stopTranslate = true;
	        $result['trans_page_type_value'] = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateAllFeatureValue($sourceLang, $targetLang, $transOption, $offset=0, $extraOptions = array(), $autoDetectLanguage = null, $idFeature = null)
    {
        $context = Context::getContext();
        $limit = 10;
        $idsData = Db::getInstance()->executeS("
            SELECT fv.`id_feature_value` FROM `"._DB_PREFIX_."feature_value` fv
            LEFT JOIN `"._DB_PREFIX_."feature_shop` fs ON fv.id_feature=fs.id_feature
            WHERE fs.`id_shop`=".(int)$context->shop->id.($idFeature ? " AND fv.id_feature=".(int)$idFeature : "")." LIMIT ".(int)$offset.", ".(int)$limit);
        if(!$idsData || !count($idsData)){
            return array(
                'nb_translated' => $offset,
                'stop_translate' => true
            );
        }
        $ids = array();
        foreach ($idsData as $item){
            $ids[] = $item['id_feature_value'];
        }
        $result = self::translateFeatureValue($ids, $sourceLang, $targetLang, $transOption, $extraOptions, $autoDetectLanguage);
        if(!is_array($result)){
            $result = array();
        }

        $result['nb_translated'] = $offset + count($idsData);
        $result['ids_translated'] = $ids;
        $stopTranslate = false;
        if(count($idsData) < $limit || (isset($result['errors']) && $result['errors'])){
            $stopTranslate = true;
        }
        $result['stop_translate'] = $stopTranslate;
        return $result;
    }

    public static function translateCategory($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!$ids){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $categories = array();
        foreach ($ids as $id)
        {
            $categories[] = new Category((int)$id);
        }
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = self::$_FIELDS_TRANS_CATEGORIES;
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($categories,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'category');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new Category($itemId);
			                if (!Validate::isLoadedObject(new Category($p->id_parent)))
				                continue;
			                $p->groupBox = $p->getGroups();
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }
			                $linkRr = '';
			                foreach($p->link_rewrite as $ii){
				                if($ii){
					                $linkRr = $ii;
					                break;
				                }
			                }
			                foreach($p->link_rewrite as $ki=> $ii){
				                if(!$ii && $linkRr){
					                $p->link_rewrite[$ki] = $linkRr;
				                }
			                }
			                if (!$p->validateFieldsLang(false))
			                	continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if (!Validate::isLoadedObject(new Category($p->id_parent)))
				                continue;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }

			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $linkRr = '';
					                foreach($p->link_rewrite as $ii){
						                if($ii){
							                $linkRr = $ii;
							                break;
						                }
					                }
					                foreach($p->link_rewrite as $ki=> $ii){
						                if(!$ii && $linkRr){
							                $p->link_rewrite[$ki] = $linkRr;
						                }
					                }
					                if (!$p->validateFieldsLang(false))
					                	continue;
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $linkRr = '';
				                foreach($p->link_rewrite as $ii){
					                if($ii){
						                $linkRr = $ii;
						                break;
					                }
				                }
				                foreach($p->link_rewrite as $ki=> $ii){
					                if(!$ii && $linkRr){
						                $p->link_rewrite[$ki] = $linkRr;
					                }
				                }
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (\PrestaShopException $exception) {
		                EtsTransLog::logTranslate('category', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('category', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('category', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    private static function getAutoDetectLanguage($autoDetectLanguage = null) {
	    if ($autoDetectLanguage === null) {
		    /** @var Ets_Translate $module */
		    $module = Module::getInstanceByName('ets_translate');
		    return $module->isAutoDetectLanguage();
	    }
	    return $autoDetectLanguage;
    }

    public static function translateCMS($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
    	$autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!$ids){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $cms = array();
        foreach ($ids as $id)
        {
            $cms[] = new CMS((int)$id);
        }
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = self::$_FIELDS_TRANS_CMS;
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($cms,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'cms');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new CMS($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'meta_title'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'meta_title'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (\PrestaShopException $exception) {
		                EtsTransLog::logTranslate('cms', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);

                EtsTransLog::logTranslate('cms', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('cms', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateCMSCategory($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!$ids){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $cmsCategory = array();
        foreach ($ids as $id)
        {
            $cmsCategory[] = new CMSCategory((int)$id);
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = ['name', 'meta_title', 'description', 'meta_description', 'meta_keywords'];
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($cmsCategory,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'cms_category');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new CMSCategory($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
				                if($itemField == 'name'){
					                if($transLinkRewrite && $sourceLang != $idLang && $urlRewrite = self::slugify($translatedText[$key])){
						                $p->link_rewrite[$idLang] = $urlRewrite;
					                }
				                }
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (\PrestaShopException $exception) {
		                EtsTransLog::logTranslate('cms_category', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('cms_category', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('cms_category', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateManufacturer($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
    	$autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!$ids){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $manufacturer = array();
        foreach ($ids as $id)
        {
            $manufacturer[] = new Manufacturer((int)$id);
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = ['description', 'short_description', 'meta_title', 'meta_description', 'meta_keywords'];
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($manufacturer,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'manufacturer');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new Manufacturer($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('manufacturer', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('manufacturer', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('manufacturer', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateSupplier($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $manufacturer = array();
        foreach ($ids as $id)
        {
            $manufacturer[] = new Supplier((int)$id);
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = ['description', 'meta_title', 'meta_description', 'meta_keywords'];
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($manufacturer,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'supplier');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new Supplier($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('supplier', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('supplier', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('supplier', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateImageSliders($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $imageSliderObj = self::getImageSliderObjects($ids);
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = ['title', 'description', 'legend'];
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($imageSliderObj,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'ps_imageslider');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if((int)$itemId){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."homeslider_slides_lang` SET `".pSQL($itemField)."` = '".pSQL($translatedText[$key], $itemField == 'description')."' WHERE `id_homeslider_slides`=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }
                }
                EtsTransLog::logTranslate('ps_imageslider', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('ps_imageslider', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }
    public static function translateExtraProductTabs($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $extraProductTabs = self::getExtraProductTabObjects($ids);
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = ['name', 'content', 'placeholder', 'description', 'file_desc'];
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($extraProductTabs,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }

            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'ets_extraproducttabs');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if((int)$itemId){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_ept_tab_lang` SET `".pSQL($itemField)."` = '".pSQL($translatedText[$key], $itemField == 'description')."' WHERE `id_ets_ept_tab`=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }
                }
                EtsTransLog::logTranslate('ets_extraproducttabs', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('ets_extraproducttabs', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateLinkList($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $linkListObj = self::getLinkListBlockObject($ids);
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('name', 'custom_content');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($linkListObj,$transFields,$idLang,$sourceLang,$transOption, 'ps_linklist');
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'ps_linklist');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit)  < 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if($itemField == 'name'){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."link_block_lang` SET `name`='".pSQL($translatedText[$key])."' WHERE id_link_block=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }
                    elseif($itemField == 'custom_content'){
                        $indexCustomContent = $keySplit[2];
                        $rowLinkBlock = Db::getInstance()->getRow("SELECT `custom_content` FROM `"._DB_PREFIX_."link_block_lang` WHERE id_link_block=".(int)$itemId." AND id_lang=".(int)$idLang);
                        if(!$rowLinkBlock){
                            continue;
                        }
                        $customContentDb = $rowLinkBlock['custom_content'] ? json_decode($rowLinkBlock['custom_content'], true) : array();

                        if(!isset($customContentDb[(int)$indexCustomContent])){
                            $customContentDb[] = array(
                                'title' => $translatedText[$key],
                                'url' => $transLinkRewrite && $sourceLang != $idLang ? Tools::str2url($translatedText[$key]) : ''
                            );
                        }
                        else{
                            $customContentDb[(int)$indexCustomContent] = array(
                                'title' => $translatedText[$key],
                                'url' => $transLinkRewrite && $sourceLang != $idLang ? Tools::str2url($translatedText[$key]) : $customContentDb[(int)$indexCustomContent]['url'],
                            );
                        }
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."link_block_lang` SET `custom_content`='".json_encode($customContentDb)."' WHERE id_link_block=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }

                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('ps_linklist', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('ps_linklist', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateMainMenu($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $linkListObj = self::getMainMenuObject($ids);
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('label');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;

        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($linkListObj,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'ps_mainmenu');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit)  < 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if($itemField == 'label'){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."linksmenutop_lang` SET `label`='".pSQL($translatedText[$key])."' WHERE id_linksmenutop=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }
                }
                EtsTransLog::logTranslate('ps_mainmenu', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('ps_mainmenu', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateBlockReassurance($ids, $sourceLang, $targetLang, $transOption, $isNewBlockreassurance, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $blockReassurance = self::getReassuranceObject($ids, $isNewBlockreassurance);
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        if($isNewBlockreassurance){
            $transFields = array('title', 'description');
        }
        else{
            $transFields = array('text');
        }

        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($blockReassurance,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'blockreassurance');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if($isNewBlockreassurance){
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."psreassurance_lang` SET `".pSQL($itemField)."`='".pSQL($translatedText[$key])."' WHERE id_psreassurance=".(int)$itemId." AND id_lang=".(int)$idLang);
                    }
                    else
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."reassurance_lang` SET `text`='".pSQL($translatedText[$key])."' WHERE id_reassurance=".(int)$itemId." AND id_lang=".(int)$idLang);
                }
                EtsTransLog::logTranslate('blockreassurance', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('blockreassurance', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function getReassuranceObject($ids, $isNewVersion = false)
    {
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $blockReassurance = array();
        if(!$ids)
            return $blockReassurance;
        $implode_ids = implode(',', array_map('intval', $ids));
        if($isNewVersion) {
            $dataDb = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "psreassurance_lang` WHERE `id_psreassurance` IN (" . $implode_ids . ")");
            $idCol = 'id_psreassurance';
        }
        else {
            $dataDb = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "reassurance_lang` WHERE `id_reassurance` IN (" . $implode_ids . ")");
            $idCol = 'id_reassurance';
        }
        $objectData = array();
        foreach ($dataDb as $item)
        {
            if(!isset($objectData[$item[$idCol]])){
                if($isNewVersion){
                    $objectData[$item[$idCol]] = array(
                        $item['id_lang'] => array(
                            'title' => $item['title'],
                            'description' => $item['description'],
                        )
                    );
                }
                else{
                    $objectData[$item[$idCol]] = array(
                        $item['id_lang'] => $item['text']
                    );
                }

            }
            else{
                if($isNewVersion){
                    $objectData[$item[$idCol]][$item['id_lang']] = array(
                        'title' => $item['title'],
                        'description' => $item['description'],
                    );
                }
                else {
                    $objectData[$item[$idCol]][$item['id_lang']] = $item['text'];
                }
            }
        }
        foreach ($objectData as $id=>$itemObj){
            $objBlock = new stdClass();
            if($isNewVersion){
                $objBlock->title = array();
                $objBlock->description = array();
            }
            else
                $objBlock->text = array();
            $objBlock->id = $id;
            foreach ($itemObj as $idLangItem=>$textVal){
                if($isNewVersion) {
                    $objBlock->title[$idLangItem] = $textVal['title'];
                    $objBlock->description[$idLangItem] = $textVal['description'];
                }
                else
                    $objBlock->text[$idLangItem] = $textVal;
            }
            $blockReassurance[$id] = $objBlock;
        }
        return $blockReassurance;
    }
    public static function getLinkListBlockObject($ids)
    {
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $blockReassurance = array();
        if(!$ids)
            return $blockReassurance;
        $dataDb = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."link_block_lang` WHERE `id_link_block` IN (".implode(',', array_map('intval', $ids)).")");
        $objectData = array();
        foreach ($dataDb as $item)
        {
            if(!isset($objectData[$item['id_link_block']])){
                $customContent = $item['custom_content'] ? json_decode($item['custom_content'], true) : array();
                $customContentData = array();
                foreach ($customContent as $icc){
                    $customContentData[] = array('title'=>$icc['title']);
                }
                $objectData[$item['id_link_block']] = array(
                    $item['id_lang'] => array(
                        'name' => $item['name'],
                        'custom_content' => $customContentData,
                    )
                );
            }
            else{
                $customContent = $item['custom_content'] ? json_decode($item['custom_content'], true) : array();
                $customContentData = array();
                foreach ($customContent as $icc){
                    $customContentData[] = array('title'=>$icc['title']);
                }
                $objectData[$item['id_link_block']][$item['id_lang']] = array(
                    'name' => $item['name'],
                    'custom_content' => $customContentData,
                );
            }
        }
        foreach ($objectData as $id=>$itemObj){
            $objBlock = new stdClass();
            $objBlock->name = array();
            $objBlock->custom_content = array();
            $objBlock->id = $id;
            foreach ($itemObj as $idLangItem=>$textVal){
                $objBlock->name[$idLangItem] = $textVal['name'];
                $objBlock->custom_content[$idLangItem] = $textVal['custom_content'];
            }
            $blockReassurance[$id] = $objBlock;
        }
        return $blockReassurance;
    }

    public static function getMainMenuObject($ids)
    {
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $mainMenuObjects = array();
        if(!$ids)
            return $mainMenuObjects;
        $dataDb = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."linksmenutop_lang` WHERE `id_linksmenutop` IN (".implode(',', array_map('intval', $ids)).")");

        $objectData = array();
        foreach ($dataDb as $item)
        {
            if(!isset($objectData[$item['id_linksmenutop']])){
                $objectData[$item['id_linksmenutop']] = array(
                    $item['id_lang'] => array(
                        'label' => $item['label']
                    )
                );
            }
            else{
                $objectData[$item['id_linksmenutop']][$item['id_lang']] = array(
                    'label' => $item['label']
                );
            }
        }
        foreach ($objectData as $id=>$itemObj){
            $objBlock = new stdClass();
            $objBlock->label = array();
            $objBlock->id = $id;
            foreach ($itemObj as $idLangItem=>$textVal){
                $objBlock->label[$idLangItem] = $textVal['label'];
            }
            $mainMenuObjects[$id] = $objBlock;
        }
        return $mainMenuObjects;
    }
    public static function getImageSliderObjects($ids)
    {
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $mainMenuObjects = array();
        if(!$ids)
            return $mainMenuObjects;
        $dataDb = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."homeslider_slides_lang` WHERE `id_homeslider_slides` IN (".implode(',', array_map('intval', $ids)).")");

        $objectData = array();
        foreach ($dataDb as $item)
        {
            if(!isset($objectData[$item['id_homeslider_slides']])){
                $objectData[$item['id_homeslider_slides']] = array(
                    $item['id_lang'] => array(
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'legend' => $item['legend'],
                    )
                );
            }
            else{
                $objectData[$item['id_homeslider_slides']][$item['id_lang']] = array(
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'legend' => $item['legend'],
                );
            }
        }
        foreach ($objectData as $id=>$itemObj){
            $objBlock = new stdClass();
            $objBlock->title = array();
            $objBlock->description = array();
            $objBlock->legend = array();
            $objBlock->id = $id;
            foreach ($itemObj as $idLangItem=>$textVal){
                $objBlock->title[$idLangItem] = $textVal['title'];
                $objBlock->description[$idLangItem] = $textVal['description'];
                $objBlock->legend[$idLangItem] = $textVal['legend'];
            }
            $mainMenuObjects[$id] = $objBlock;
        }
        return $mainMenuObjects;
    }

    public static function getExtraProductTabObjects($ids)
    {
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $extraTabObj = array();
        if(!$ids)
            return $extraTabObj;
	    try {
		    $dataDb = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."ets_ept_tab_lang` WHERE `id_ets_ept_tab` IN (".implode(',', array_map('intval', $ids)).")");

		    $objectData = array();
		    foreach ($dataDb as $item)
		    {
			    if(!isset($objectData[$item['id_ets_ept_tab']])){
				    $objectData[$item['id_ets_ept_tab']] = array(
					    $item['id_lang'] => array(
						    'name' => $item['name'],
						    'content' => $item['content'],
						    'placeholder' => $item['placeholder'],
						    'description' => $item['description'],
						    'file_desc' => $item['file_desc'],
					    )
				    );
			    }
			    else{
				    $objectData[$item['id_ets_ept_tab']][$item['id_lang']] = array(
					    'name' => $item['name'],
					    'content' => $item['content'],
					    'placeholder' => $item['placeholder'],
					    'description' => $item['description'],
					    'file_desc' => $item['file_desc'],
				    );
			    }
		    }
		    foreach ($objectData as $id=>$itemObj){
			    $objBlock = new stdClass();
			    $objBlock->name = array();
			    $objBlock->content = array();
			    $objBlock->placeholder = array();
			    $objBlock->description = array();
			    $objBlock->id = $id;
			    foreach ($itemObj as $idLangItem=>$textVal){
				    $objBlock->name[$idLangItem] = $textVal['name'];
				    $objBlock->content[$idLangItem] = $textVal['content'];
				    $objBlock->placeholder[$idLangItem] = $textVal['placeholder'];
				    $objBlock->description[$idLangItem] = $textVal['description'];
				    $objBlock->file_desc[$idLangItem] = $textVal['file_desc'];
			    }
			    $extraTabObj[$id] = $objBlock;
		    }
		    return $extraTabObj;
	    } catch (\PrestaShopDatabaseException $exception) {
		    return $extraTabObj;
	    }
    }

    public static function translateAttributeGroup($ids, $sourceLang, $targetLang, $transOption, $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $attributeGroups = array();
        foreach ($ids as $id)
        {
            $attributeGroupsItem = new AttributeGroup((int)$id);
            $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` WHERE id_attribute_group=".(int)$id);

            $attributeGroupsItem->meta_title = array();
            if($agls){
                foreach ($agls as $agl){
                    $attributeGroupsItem->meta_title[$agl['id_lang']] = $agl['meta_title'];
                }
            }
            else{
                foreach (Language::getLanguages(false) as $lang){
                    $attributeGroupsItem->meta_title[$lang['id_lang']] = '';
                }
            }
            $attributeGroups[] = $attributeGroupsItem;
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('name', 'public_name', 'meta_title');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($attributeGroups,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'attribute_group');
            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if($itemField == 'meta_title'){
                        self::createAttributeGroupIndexable($itemId);
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` SET meta_title='".pSQL($translatedText[$key])."' WHERE id_attribute_group=".(int)$itemId." AND id_lang=".(int)$idLang);
                        continue;
                    }

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new AttributeGroup($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('attribute_group', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                EtsTransLog::logTranslate('attribute_group', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
                unset($itemUpdate);
            }
            else{

                EtsTransLog::logTranslate('attribute_group', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateAttribute($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $attributes = array();
        foreach ($ids as $id)
        {
            $attributeItem = new Attribute((int)$id);
            $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_attribute_lang_value` WHERE id_attribute=".(int)$id);

            $attributeItem->meta_title = array();
            if($agls){
                foreach ($agls as $agl){
                    $attributeItem->meta_title[$agl['id_lang']] = $agl['meta_title'];
                }
            }
            else{
                foreach (Language::getLanguages(false) as $lang){
                    $attributeItem->meta_title[$lang['id_lang']] = '';
                }
            }
            $attributes[] = $attributeItem;
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('name', 'meta_title');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($attributes,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'attribute');

            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();

                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];

                    if(!isset($itemUpdate[$itemId]) && ($attrLangData = self::getAttributeLangData($itemId))){
                        foreach ($attrLangData as $itemLangData)
                        {
                            foreach ($itemLangData as $kid=>$vid){
                                if($kid !== 'id_lang')
                                    ${'_GET'}[$kid.'_'.$itemLangData['id_lang']] = $vid;
                            }
                        }
                    }

                    $itemField = $keySplit[1];
                    if($itemField == 'meta_title'){
                       ${'_GET'}['meta_title_'.(int)$idLang] = $translatedText[$key];
                        $p = new Attribute($itemId);
	                    if (!$p->validateFieldsLang(false))
		                    continue;
                        $p->ets_trans = 1;
                        $p->save();
                        continue;
                    }

	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new Attribute($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                ${'_GET'}[$itemField.'_'.$idLang] = $p->{$itemField}[$idLang];
			                if($itemField == 'name' && $transLinkRewrite && $sourceLang != $idLang){
				                ${'_GET'}['url_name_'.$idLang] = Tools::str2url($p->{$itemField}[$idLang]);
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                ${'_GET'}[$itemField.'_'.$idLang] = $p->{$itemField}[$idLang];
			                if($itemField == 'value' && $transLinkRewrite && $sourceLang != $idLang){
				                ${'_GET'}['url_name_'.$idLang] = Tools::str2url($p->{$itemField}[$idLang]);
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }

		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('attribute', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }

                unset($itemUpdate);
                EtsTransLog::logTranslate('attribute', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);

            }
            else{
                EtsTransLog::logTranslate('attribute', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);

                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }

        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateFeature($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $features = array();
        foreach ($ids as $id)
        {
            $featureItem = new Feature((int)$id);
            $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_feature_lang_value` WHERE id_feature=".(int)$id);

            $featureItem->meta_title = array();
            if($agls){
                foreach ($agls as $agl){
                    $featureItem->meta_title[$agl['id_lang']] = $agl['meta_title'];
                }
            }
            else{
                foreach (Language::getLanguages(false) as $lang){
                    $featureItem->meta_title[$lang['id_lang']] = '';
                }
            }
            $features[] = $featureItem;
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('name', 'meta_title');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($features,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'feature');

            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];
                    if($itemField == 'meta_title'){
                        self::createFeatureIndexable($itemId);
                        Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_feature_lang_value` SET meta_title='".pSQL($translatedText[$key])."' WHERE id_feature=".(int)$itemId." AND id_lang=".(int)$idLang);
                        continue;
                    }
	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new Feature($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                if($itemField == 'name' && $transLinkRewrite && $sourceLang != $idLang){
				                Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."layered_indexable_feature_lang_value` SET url_name='".pSQL(Tools::str2url($p->{$itemField}[$idLang]))."' WHERE id_feature=".(int)$itemId." AND id_lang=".(int)$idLang);
			                }
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }

			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('feature', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('feature', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('feature', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function translateFeatureValue($ids, $sourceLang, $targetLang, $transOption, $extraOptions = array(), $autoDetectLanguage = null)
    {
        if(!$ids){
            return false;
        }
	    $autoDetectLanguage = self::getAutoDetectLanguage($autoDetectLanguage);
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $features = array();
        foreach ($ids as $id)
        {
            $featureItem = new FeatureValue((int)$id);
            $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_feature_value_lang_value` WHERE id_feature_value=".(int)$id);

            $featureItem->meta_title = array();
            if($agls){
                foreach ($agls as $agl){
                    $featureItem->meta_title[$agl['id_lang']] = $agl['meta_title'];
                }
            }
            else{
                foreach (Language::getLanguages(false) as $lang){
                    $featureItem->meta_title[$lang['id_lang']] = '';
                }
            }
            $features[] = $featureItem;
        }
        $sourceIsoCode = $autoDetectLanguage ? null : Language::getIsoById((int)$sourceLang);
        $api = EtsTransApi::getInstance();
        $transFields = array('value', 'meta_title');
        $strTranslatedLength = 0;
        $nbTextTranslated = 0;
        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($extraOptions['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$extraOptions['auto_generate_link_rewrite'];
        }
        foreach ($targetLang as $idLang)
        {
            $timStartTrans = microtime(true);
            $textTransFormat = self::getTextTrans($features,$transFields,$idLang,$sourceLang,$transOption);
            $textTrans = $textTransFormat['textTrans'];
            $keyTrans = $textTransFormat['keyTrans'];
            if(!$nbTextTranslated){
                $nbTextTranslated = count($textTrans);
            }
            $res = $api->translate($sourceIsoCode, Language::getIsoById($idLang), $textTrans, 'feature_value');

            if(!$res['errors'] && $res['data']){
                $translatedText = $res['data'];
                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $itemUpdate = array();
                foreach ($keyTrans as $key => $item){
                    $keySplit = explode('.', $item);
                    if(count($keySplit) !== 2){
                        continue;
                    }
                    $itemId = $keySplit[0];
                    $itemField = $keySplit[1];

                    if(!isset($itemUpdate[$itemId]) && ($featureLangData = self::getFeatureValueLang($itemId))){
                        foreach ($featureLangData as $itemLangData)
                        {
                            foreach ($itemLangData as $kid=>$vid){
                                if($kid !== 'id_lang')
                                    ${'_GET'}[$kid.'_'.$itemLangData['id_lang']] = $vid;
                            }
                        }
                    }
                    if($itemField == 'meta_title'){
                        ${'_GET'}['meta_title_'.(int)$idLang] = $translatedText[$key];
                        $p = new FeatureValue($itemId);
	                    if (!$p->validateFieldsLang(false))
		                    continue;
                        $p->ets_trans = 1;
                        $p->save();
                        continue;
                    }
	                try {
		                if(!isset($itemUpdate[$itemId])){
			                $p = new FeatureValue($itemId);
			                $itemUpdate[$itemId] = $p;
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                ${'_GET'}[$itemField.'_'.$idLang] = $p->{$itemField}[$idLang];
			                if($itemField == 'value' && $transLinkRewrite && $sourceLang != $idLang){
				                ${'_GET'}['url_name_'.$idLang] = Tools::str2url($p->{$itemField}[$idLang]);
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                $p->ets_trans = 1;
			                $p->save();
		                }
		                else{
			                $p = &$itemUpdate[$itemId];
			                if(strpos($itemField, 'keywords') !== false){
				                $p->{$itemField}[$idLang] = str_replace('|', ',', $translatedText[$key]);
			                }
			                else{
				                $p->{$itemField}[$idLang] = $translatedText[$key];
			                }
			                ${'_GET'}[$itemField.'_'.$idLang] = $p->{$itemField}[$idLang];
			                if($itemField == 'value' && $transLinkRewrite && $sourceLang != $idLang){
				                ${'_GET'}['url_name_'.$idLang] = Tools::str2url($p->{$itemField}[$idLang]);
			                }
			                if (!$p->validateFieldsLang(false))
				                continue;
			                if(isset($keyTrans[$key+1])) {
				                $keySplit2 = explode('.', $keyTrans[$key + 1]);
				                if($keySplit2[0] != $itemId){
					                $p->ets_trans = 1;
					                $p->save();
				                }
			                }
			                else{
				                $p->ets_trans = 1;
				                $p->save();
			                }
		                }
	                } catch (PrestaShopException $exception) {
		                EtsTransLog::logTranslate('feature_value', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, $exception->getMessage());
	                }
                }
                unset($itemUpdate);
                EtsTransLog::logTranslate('feature_value', true, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null);
            }
            else{
                EtsTransLog::logTranslate('feature_value', false, $timStartTrans, $sourceLang, $idLang, null, EtsTransModule::getLogIdTrans($keyTrans, '.'),null, isset($res['message']) ? $res['message'] : null);

                $strTranslatedLength+= self::countTextTranslated($textTrans);
                $res['translated_length'] = $strTranslatedLength;
                return $res;
            }
        }
        return array(
            'translated_length' => $strTranslatedLength,
            'nb_text' => $nbTextTranslated,
        );
    }

    public static function ignoreTextTranslate($text, $content)
    {
        return preg_replace('/\b('.$text.')\b/', '<'.'s'.'p'.'a'.'n class="'.'notranslate exclude'.'"'.'>'.'$1'.'<'.'/'.'s'.'p'.'a'.'n'.'>', $content);
    }

    public static function getTextTrans($objects, $transFields, $idLang, $sourceLang, $transOption, $pageType = null, $extraOptions = array()){
        $textTrans = array();
        $keyTrans = array();
        foreach ($objects as $obj)
        {
            switch ($transOption){
                case 'both': // same content with source lang or empty
                    foreach ($transFields as $field) {

                    	if ($field == 'legend' && $pageType == 'product') {
		                    $images = Image::getImages($sourceLang, $obj->id, null, Context::getContext()->shop->id);
		                    foreach ($images as $image) {
		                    	$_image = new Image($image['id_image'], $idLang);
		                    	if (!$_image->id)
		                    		continue;
		                    	if ($_image->legend == '' || $_image->legend == $image['legend']) {
				                    $textTrans[] = $image[$field];
				                    $keyTrans[] = $image['id_image'] . '.' . $field;
			                    }
		                    }

	                    }

                    	if (isset($obj->{$field}[$sourceLang]) && $obj->{$field}[$sourceLang]) {
		                    if($pageType == 'ps_linklist' && is_array($obj->{$field}[$sourceLang]) && is_array($obj->{$field}[$idLang])){
			                    foreach ($obj->{$field}[$sourceLang] as $subkey=>$subtext){
				                    if(!$subtext || !is_array($subtext)){
					                    continue;
				                    }
				                    foreach ($subtext as $bk=>$bv){
					                    if(!isset($obj->{$field}[$idLang][$subkey][$bk]) || !trim($obj->{$field}[$idLang][$subkey][$bk]) || trim($obj->{$field}[$idLang][$subkey][$bk]) == trim($bv)){
						                    $textTrans[] = $bv;
						                    $keyTrans[] = $obj->id.'.'.$field.'.'.$subkey.'.'.$bk;
					                    }
				                    }
			                    }
		                    }
		                    elseif(!trim($obj->{$field}[$idLang]) || trim($obj->{$field}[$sourceLang]) == trim($obj->{$field}[$idLang])){
			                    if(strpos($field, 'keywords') !== false){
				                    $textTrans[] = str_replace(',', '|', $obj->{$field}[$sourceLang]);
			                    }
			                    else{
				                    $textTrans[] = $obj->{$field}[$sourceLang];
			                    }
			                    $keyTrans[] = $obj->id.'.'.$field;
		                    }
	                    }
                    }
                    break;
                case 'only_empty': // only translate empty data of field
                    foreach ($transFields as $field) {
	                    if ($field == 'legend' && $pageType == 'product') {
		                    $images = Image::getImages($sourceLang, $obj->id, null, Context::getContext()->shop->id);
		                    foreach ($images as $image) {
			                    $_image = new Image($image['id_image'], $idLang);
			                    if (!$_image->id)
				                    continue;
			                    if ($_image->legend == '') {
				                    $textTrans[] = $image[$field];
				                    $keyTrans[] = $image['id_image'] . '.' . $field;
			                    }
		                    }

	                    }
	                    if (isset($obj->{$field}[$sourceLang]) && $obj->{$field}[$sourceLang]) {
		                    if($pageType == 'ps_linklist' && is_array($obj->{$field}[$sourceLang]) && is_array($obj->{$field}[$idLang])){
			                    foreach ($obj->{$field}[$sourceLang] as $subkey=>$subtext){
				                    if(!$subtext || !is_array($subtext)){
					                    continue;
				                    }
				                    foreach ($subtext as $bk=>$bv){
					                    if(!isset($obj->{$field}[$idLang][$subkey][$bk]) || !trim($obj->{$field}[$idLang][$subkey][$bk])){
						                    $textTrans[] = $bv;
						                    $keyTrans[] = $obj->id.'.'.$field.'.'.$subkey.'.'.$bk;
					                    }
				                    }
			                    }
		                    }
		                    elseif(!trim($obj->{$field}[$idLang])){
			                    if(strpos($field, 'keywords') !== false){
				                    $textTrans[] = str_replace(',', '|', $obj->{$field}[$sourceLang]);
			                    }
			                    else{
				                    $textTrans[] = $obj->{$field}[$sourceLang];
			                    }
			                    $keyTrans[] = $obj->id.'.'.$field;
		                    }
	                    }
                    }
                    break;
                case 'same_source': // only same data with source lang
                    foreach ($transFields as $field) {

	                    if ($field == 'legend' && $pageType == 'product') {
		                    $images = Image::getImages($sourceLang, $obj->id, null, Context::getContext()->shop->id);
		                    foreach ($images as $image) {
			                    $_image = new Image($image['id_image'], $idLang);
			                    if (!$_image->id)
				                    continue;
			                    if ($_image->legend == $image['legend']) {
				                    $textTrans[] = $image[$field];
				                    $keyTrans[] = $image['id_image'] . '.' . $field;
			                    }
		                    }

	                    }
	                    if (isset($obj->{$field}[$sourceLang]) && $obj->{$field}[$sourceLang]) {
		                    if($pageType == 'ps_linklist' && is_array($obj->{$field}[$sourceLang]) && is_array($obj->{$field}[$idLang])){
			                    foreach ($obj->{$field}[$sourceLang] as $subkey=>$subtext){
				                    if(!$subtext || !is_array($subtext)){
					                    continue;
				                    }
				                    foreach ($subtext as $bk=>$bv){
					                    if(isset($obj->{$field}[$idLang][$subkey][$bk]) && trim($obj->{$field}[$idLang][$subkey][$bk]) == trim($bv)){
						                    $textTrans[] = $bv;
						                    $keyTrans[] = $obj->id.'.'.$field.'.'.$subkey.'.'.$bk;
					                    }
				                    }
			                    }
		                    }
		                    elseif(trim($obj->{$field}[$sourceLang]) == trim($obj->{$field}[$idLang])){
			                    if(strpos($field, 'keywords') !== false){
				                    $textTrans[] = str_replace(',', '|', $obj->{$field}[$sourceLang]);
			                    }
			                    else{
				                    $textTrans[] = $obj->{$field}[$sourceLang];
			                    }
			                    $keyTrans[] = $obj->id.'.'.$field;
		                    }
	                    }
                    }
                    break;
                default: // don't care conditions, translate all
                    foreach ($transFields as $field) {

	                    if ($field == 'legend' && $pageType == 'product') {
		                    $images = Image::getImages($sourceLang, $obj->id, null, Context::getContext()->shop->id);
		                    foreach ($images as $image) {
			                    $textTrans[] = $image[$field];
			                    $keyTrans[] = $image['id_image'] . '.' . $field;
		                    }
	                    }
	                    if (isset($obj->{$field}[$sourceLang]) && $obj->{$field}[$sourceLang]) {
		                    if($pageType == 'ps_linklist' && is_array($obj->{$field}[$sourceLang]) && is_array($obj->{$field}[$idLang])){
			                    foreach ($obj->{$field}[$sourceLang] as $subkey=>$subtext){
				                    if(!$subtext || !is_array($subtext)){
					                    continue;
				                    }
				                    foreach ($subtext as $bk=>$bv){
					                    $textTrans[] = $bv;
					                    $keyTrans[] = $obj->id.'.'.$field.'.'.$subkey.'.'.$bk;
				                    }
			                    }
		                    }
		                    else{
			                    if(strpos($field, 'keywords') !== false){
				                    $textTrans[] = str_replace(',', '|', $obj->{$field}[$sourceLang]);
			                    }
			                    else{
				                    $textTrans[] = $obj->{$field}[$sourceLang];
			                    }
			                    $keyTrans[] = $obj->id.'.'.$field;
		                    }
	                    }
                    }
                    break;
            }
        }

        return array(
            'textTrans' => $textTrans,
            'keyTrans' => $keyTrans,
        );
    }

    public static function translatePage($type,$formData, $extraOptions = array(), $transFields = array(), $isNewBlockreassurance = 0, $autoDetectLanguage = null)
    {
        $limit = 2;
        $ids = $formData['page_id'];
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        $idsTrans = array_splice($ids, 0,$limit);
        $nbTrans = isset($formData['nb_translated']) ? (int)$formData['nb_translated'] + count($idsTrans) : count($idsTrans);
        switch ($type){
            case 'product':
                $result = self::transProduct($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions, $transFields, $autoDetectLanguage);
                break;
            case 'category':
                $result = self::translateCategory($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'cms':
                $result = self::translateCMS($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'cms_category':
                $result = self::translateCMSCategory($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'manufacturer':
                $result = self::translateManufacturer($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
                break;
            case 'supplier':
                $result = self::translateSupplier($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
                break;
            case 'attribute_group':
                $result = self::translateAttributeGroup($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
                break;
            case 'attribute':
                $result = self::translateAttribute($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'feature':
                $result = self::translateFeature($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'feature_value':
                $result = self::translateFeatureValue($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'blockreassurance':
                $result = self::translateBlockReassurance($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $isNewBlockreassurance);
                break;
            case 'ps_linklist':
                $result = self::translateLinkList($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option'], $extraOptions);
                break;
            case 'ps_mainmenu':
                $result = self::translateMainMenu($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
            case 'ps_imageslider':
                $result = self::translateImageSliders($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
            case 'ets_extraproducttabs':
                $result = self::translateExtraProductTabs($idsTrans, $formData['trans_source'], $formData['trans_target'], $formData['trans_option']);
                break;

        }

        if(isset($result) && $result){
            if(!is_array($result)){
                $result = array();
            }
            $result['page_id'] = implode(',', $ids);
            $result['ids_translated'] = $idsTrans;
            $result['nb_translated'] = $nbTrans;
            $result['stop_translate'] = $ids ? false : true;
            if(isset($result['errors']) && $result['errors']){
                $result['stop_translate'] = true;
            }
            return $result;
        }
        return array();
    }

    public static function slugify($text)
    {
       return Tools::str2url($text);
    }

    public static function countTextTranslated($arrText)
    {
        $count = 0;
        if (is_array($arrText)) {
	        foreach ($arrText as $item){
		        $count += Tools::strlen($item);
	        }
        } else {
        	$count = Tools::strlen($arrText);
        }
        return $count;
    }

    public static function analysisTranslate($pageType, $formData, $offset=0, $idAttributeGroup = 0, $idFeature = 0, $isNewBlockreassurance = false)
    {
        if(!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option'])){
            return false;
        }
        $limit = 10;
        $langSource = $formData['trans_source'];
        $langTarget = $formData['trans_target'];
        $transOption = $formData['trans_option'];
        $result = array(
            'nb_text' => 0,
            'nb_char' => 0,
            'nb_money' => 0,
            'stop' => 0,
            'offset' => $offset + $limit,
        );
        $context = Context::getContext();
	    $idField = '';
	    $objClass = '';
	    $transFields = [];
        switch ($pageType){
            case 'product':
                $transFields = EtsTransDefine::getListFieldsProductTrans(true);
                if (isset($formData['etsTransFields']) && count($formData['etsTransFields'])) {
                	$transFields = $formData['etsTransFields'];
                }
                $idItems = Db::getInstance()->executeS("SELECT `id_product` FROM `"._DB_PREFIX_."product_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_product';
                $objClass = 'Product';
                break;
            case 'category':
                $transFields = self::$_FIELDS_TRANS_CATEGORIES;
                $idItems = Db::getInstance()->executeS("SELECT `id_category` FROM `"._DB_PREFIX_."category_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_category';
                $objClass = 'Category';
                break;
            case 'cms':
                $transFields = self::$_FIELDS_TRANS_CMS;
                $idItems = Db::getInstance()->executeS("SELECT `id_cms` FROM `"._DB_PREFIX_."cms_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_cms';
                $objClass = 'CMS';
                break;
            case 'cms_category':
                $transFields = ['name', 'meta_title', 'description', 'meta_description', 'meta_keywords'];
                $idItems = Db::getInstance()->executeS("SELECT `id_cms_category` FROM `"._DB_PREFIX_."cms_category_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_cms_category';
                $objClass = 'CMSCategory';
                break;
            case 'manufacturer':
                $transFields = ['description', 'short_description', 'meta_title', 'meta_description', 'meta_keywords'];
                $idItems = Db::getInstance()->executeS("SELECT `id_manufacturer` FROM `"._DB_PREFIX_."manufacturer_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_manufacturer';
                $objClass = 'Manufacturer';
                break;
            case 'supplier':
                $transFields = ['description', 'meta_title', 'meta_description', 'meta_keywords'];
                $idItems = Db::getInstance()->executeS("SELECT `id_supplier` FROM `"._DB_PREFIX_."supplier_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_supplier';
                $objClass = 'Supplier';
	            break;
            case 'attribute_group':
                $transFields = ['name', 'public_name', 'meta_title'];
                $idItems = Db::getInstance()->executeS("SELECT `id_attribute_group` FROM `"._DB_PREFIX_."attribute_group_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_attribute_group';
                $objClass = 'AttributeGroup';
                break;
            case 'attribute':
                $transFields = ['name', 'meta_title'];
                $idItems = Db::getInstance()->executeS("
                    SELECT a.id_attribute FROM `"._DB_PREFIX_."attribute_shop` attrs 
                    LEFT JOIN `"._DB_PREFIX_."attribute` a ON attrs.id_attribute=a.id_attribute 
                    WHERE attrs.`id_shop`=".(int)$context->shop->id.($idAttributeGroup ? " AND a.id_attribute_group=".(int)$idAttributeGroup : "")." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_attribute';
                $objClass = 'Attribute';
                break;
            case 'feature':
                $transFields = ['name', 'meta_title'];
                $idItems = Db::getInstance()->executeS("SELECT `id_feature` FROM `"._DB_PREFIX_."feature_shop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_feature';
                $objClass = 'Feature';
                break;
            case 'feature_value':
                $transFields = ['value', 'meta_title'];
                $idItems = Db::getInstance()->executeS("
                    SELECT fv.id_feature_value FROM `"._DB_PREFIX_."feature_value` fv 
                    LEFT JOIN `"._DB_PREFIX_."feature_shop` fs ON fv.id_feature=fs.id_feature WHERE fs.`id_shop`=".(int)$context->shop->id.($idFeature ? " AND fv.id_feature=".(int)$idFeature : "")." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_feature_value';
                $objClass = 'FeatureValue';
                break;
            case 'blockreassurance':
                $moduleObj = Module::getInstanceByName('blockreassurance');
                if($isNewBlockreassurance){
                	$check = Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."psreassurance` LIKE 'id_shop'");
                	if ($check && count($check)) {
		                $idItems = Db::getInstance()->executeS("SELECT `id_psreassurance` FROM `"._DB_PREFIX_."psreassurance` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
	                } else {
		                $idItems = Db::getInstance()->executeS("SELECT `id_psreassurance` FROM `"._DB_PREFIX_."psreassurance` LIMIT ".(int)$offset.", ".(int)$limit);
	                }
                    $idField = 'id_psreassurance';
                    $transFields = ['title', 'description'];
                }
                else{
                    $idItems = Db::getInstance()->executeS("SELECT `id_reassurance` FROM `"._DB_PREFIX_."reassurance` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                    $idField = 'id_reassurance';
                    $transFields = ['text'];
                }
                $objClass = '';
                break;
            case 'ps_linklist':
                $transFields = ['name', 'custom_content'];
                $idItems = Db::getInstance()->executeS("SELECT `id_link_block` FROM `"._DB_PREFIX_."link_block` LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_link_block';
                $objClass = '';
                break;
            case 'ps_mainmenu':
                $transFields = ['label'];
                $idItems = Db::getInstance()->executeS("SELECT `id_linksmenutop` FROM `"._DB_PREFIX_."linksmenutop` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_linksmenutop';
                $objClass = '';
                break;
            case 'ps_imageslider':
                $transFields = ['title', 'description', 'legend'];
                $idItems = Db::getInstance()->executeS("SELECT `id_homeslider_slides` FROM `"._DB_PREFIX_."homeslider` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_homeslider_slides';
                $objClass = '';
                break;
            case 'ets_extraproducttabs':
                $transFields = ['name', 'content'];
                $idItems = Db::getInstance()->executeS("SELECT `id_ets_ept_tab` FROM `"._DB_PREFIX_."ets_ept_tab` WHERE `id_shop`=".(int)$context->shop->id." LIMIT ".(int)$offset.", ".(int)$limit);
                $idField = 'id_ets_ept_tab';
                $objClass = '';
                break;
        }

        if(!isset($idItems)){
            $result['stop'] = 1;
            return $result;
        }
        $ids = array();
        foreach ($idItems as $item){
            $ids[] = $item[$idField];
        }
        $objs = array();
        if($pageType == 'blockreassurance'){
            $moduleObj = Module::getInstanceByName('blockreassurance');
            $blockReassuranceObject = self::getReassuranceObject($ids, $isNewBlockreassurance);
        }
        elseif($pageType == 'ps_linklist'){
            $linkListObject = self::getLinkListBlockObject($ids);
        }
        elseif($pageType == 'ps_mainmenu'){
            $mainMenuObject = self::getMainMenuObject($ids);
        }
        elseif($pageType == 'ps_imageslider'){
            $imageSliderObject = self::getImageSliderObjects($ids);
        }
        elseif($pageType == 'ets_extraproducttabs'){
            $extraTabsObject = self::getExtraProductTabObjects($ids);
        }

        foreach ($ids as $i){
            if($pageType == 'ps_linklist' && isset($linkListObject)){
                $itemObj = $linkListObject[$i];
            }
            elseif($pageType == 'blockreassurance' && isset($blockReassuranceObject)){
                $itemObj = $blockReassuranceObject[$i];
            }
            elseif($pageType == 'ps_mainmenu' && isset($mainMenuObject)){
                $itemObj = $mainMenuObject[$i];
            }
            elseif($pageType == 'ps_imageslider' && isset($imageSliderObject)){
                $itemObj =  $imageSliderObject[$i];
            }
            elseif($pageType == 'ets_extraproducttabs' && isset($extraTabsObject)){
                $itemObj = $extraTabsObject[$i];
            }
            else if ($objClass){
                $itemObj = new $objClass($i);
            }
            if($pageType == 'attribute_group' || $pageType =='attribute'){
                if($pageType == 'attribute_group')
                    $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` WHERE id_attribute_group=".(int)$i);
                else
                    $agls = Db::getInstance()->executeS("SELECT `meta_title`, `id_lang` FROM `"._DB_PREFIX_."layered_indexable_attribute_lang_value` WHERE id_attribute=".(int)$i);
                if (isset($itemObj)) {
	                $itemObj->meta_title = array();
	                if($agls){
		                foreach ($agls as $agl){
			                $itemObj->meta_title[$agl['id_lang']] = $agl['meta_title'];
		                }
	                }
	                else{
		                foreach (Language::getLanguages(false) as $lang){
			                $itemObj->meta_title[$lang['id_lang']] = '';
		                }
	                }
                }

            }
            if (isset($itemObj))
                $objs[] = $itemObj;
        }
        foreach ($langTarget as $idLang){
            $dataTrans = self::getTextTrans($objs, $transFields, $idLang, $langSource, $transOption, $pageType);
            $textTrans = $dataTrans['textTrans'];
            $result['nb_text'] = count($textTrans);
            $result['nb_char'] += self::countTextTranslated($textTrans);
        }
        $api = EtsTransApi::getInstance();
        $result['nb_money'] = $api->getTotalFeeTranslate($result['nb_char']);
        if(count($ids) < $limit){
            $result['stop'] = 1;
        }
        if(!$result['nb_char']){
            $result['nb_text'] = 0;
        }
        return $result;
    }

    public static function getAttributeLangData($idAttribute, $id_shop = null)
    {
        if(!$id_shop){
            $id_shop = Context::getContext()->shop->id;
        }

        return Db::getInstance()->executeS("
            SELECT al.name, lial.url_name, lial.meta_title, al.id_lang FROM `"._DB_PREFIX_."attribute_lang` al 
            LEFT JOIN `"._DB_PREFIX_."layered_indexable_attribute_lang_value` lial ON al.id_attribute=lial.id_attribute AND al.id_lang=lial.id_lang 
            JOIN `"._DB_PREFIX_."attribute_shop` attrs ON al.id_attribute=attrs.id_attribute AND attrs.id_shop=".(int)$id_shop."
            WHERE al.id_attribute=".(int)$idAttribute);
    }

    public static function getFeatureValueLang($idFeatureValue, $id_shop=null)
    {
        if(!$id_shop){
            $id_shop = Context::getContext()->shop->id;
        }
        return Db::getInstance()->executeS("
            SELECT fvl.value, lifvl.url_name, lifvl.meta_title, fvl.id_lang FROM `"._DB_PREFIX_."feature_value_lang` fvl 
            LEFT JOIN `"._DB_PREFIX_."layered_indexable_feature_value_lang_value` lifvl ON fvl.id_feature_value = lifvl.id_feature_value AND fvl.id_lang=lifvl.id_lang 
            JOIN `"._DB_PREFIX_."feature_value` fv ON fvl.id_feature_value=fv.id_feature_value 
            JOIN `"._DB_PREFIX_."feature_shop` fs ON fv.id_feature=fs.id_feature AND fs.id_shop=".(int)$id_shop."
            WHERE fvl.id_feature_value=".(int)$idFeatureValue);
    }

    public static function createAttributeGroupIndexable($idAttributeGroup)
    {
        foreach(Language::getLanguages(true) as $lang){
            if(!Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` WHERE id_attribute_group=".(int)$idAttributeGroup." AND id_lang=".(int)$lang['id_lang'])){
                Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."layered_indexable_attribute_group_lang_value` (id_attribute_group, id_lang,url_name, meta_title) VALUES(".(int)$idAttributeGroup.",".(int)$lang['id_lang'].", '', '')");
            }
        }
    }
    public static function createFeatureIndexable($idFeature)
    {
        foreach(Language::getLanguages(true) as $lang){
            if(!Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."layered_indexable_feature_lang_value` WHERE id_feature=".(int)$idFeature." AND id_lang=".(int)$lang['id_lang'])){
                Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."layered_indexable_feature_lang_value` (id_feature, id_lang,url_name, meta_title) VALUES(".(int)$idFeature.",".(int)$lang['id_lang'].", '', '')");
            }
        }
    }
}