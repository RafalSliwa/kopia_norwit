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

class EtsTransCE
{
	/** @var Ets_Translate */
	private $module;

	public function __construct()
	{
		$this->setModule();
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
	 * @param $langSource
	 * @param array $langTarget
	 * @param $type
	 * @param $keyTemplate
	 * @param int $idTemplate
	 * @return int[]
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public static function analysisCE($langSource, $langTarget, $trans_option, $type, $keyTemplate, $idTemplate = 0) {
		$nbText = 0;
		$nbChar=0;
		$nbMoney=0;
		$context = Context::getContext();
		$api = EtsTransApi::getInstance();
		if ($idTemplate && $type) {
			if ($type == 'template') {
				$itemSource = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
                WHERE id_ce_theme=" . (int)$idTemplate . " AND id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			else {
				$itemSource = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
				WHERE id_ce_content=" . (int)$idTemplate . " AND id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			if ($itemSource) {
				if (!isset($itemSource['content']))
					$itemSource = $itemSource[0];
				if ($langTarget && is_array($langTarget)) {
					foreach ($langTarget as $lang) {
						if ($type == 'template') {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
				                WHERE id_ce_theme=" . (int)$idTemplate . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						}
						else {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
                                WHERE id_ce_content=" . (int)$idTemplate . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						}
						if (isset($itemTarget) && is_array($itemTarget) && !isset($itemTarget['content']))
							$itemTarget = $itemTarget[0];
						$compareData = self::compareDataWithTransOption($itemSource, $itemTarget, $trans_option);
						if ($compareData['isTranslatable'])
							foreach ($itemSource as $k => $v)
								if ($v && isset($compareData[$k]) && $compareData[$k]) {
									$nbText += 1;
									$nbChar += Tools::strlen(str_replace(array("\r", "\n"), array('', ''), strip_tags($v)));
									$nbMoney += $api->getTotalFeeTranslate($nbChar);
								}
					}
				}
			}
		} else {
			$templates = [];
			$contents = [];
			if ($keyTemplate == 'ce_trans' || $type == 'template') {
				$templates = Db::getInstance()->executeS("SELECT content, title, id_ce_theme FROM `"._DB_PREFIX_."ce_theme_lang`
                WHERE id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			if ($keyTemplate == 'ce_trans' || $type == 'content') {
				$contents = Db::getInstance()->executeS("SELECT content, title, id_ce_content FROM `"._DB_PREFIX_."ce_content_lang`
				WHERE id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			$items = array_merge($templates, $contents);
			if ($langTarget && is_array($langTarget)) {
				foreach ($items as $item) {
					foreach ($langTarget as $lang) {
						if (isset($item['id_ce_theme']) && $item['id_ce_theme']) {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
                        WHERE id_ce_theme=" . (int)$item['id_ce_theme'] . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						} elseif (isset($item['id_ce_content']) && $item['id_ce_content'])
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
                        WHERE id_ce_content=" . (int)$item['id_ce_content'] . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						if ( isset($itemTarget) && is_array($itemTarget) && !isset($itemTarget['content']))
							$itemTarget = $itemTarget[0];
						$compareData = self::compareDataWithTransOption($item, $itemTarget, $trans_option);
						if ($compareData['isTranslatable'])
							foreach ($item as $k => $v)
								if ($v && isset($compareData[$k]) && $compareData[$k]) {
									$nbText += 1;
									$nbChar += Tools::strlen(str_replace(array("\r", "\n"), array('', ''), strip_tags($v)));
									$nbMoney += $api->getTotalFeeTranslate($nbChar);
								}
					}
				}
			}
		}
		return array(
			'nb_text' => $nbText,
			'nb_char' => $nbChar,
			'nb_money' => $nbMoney,
			'stop' => 1
		);
	}

	public static function formatKeyPathCEInCache($filePath) {
		$idItem = 0;
		$arr = explode('.', $filePath);
		if (count($arr) >= 2) {
			$idItem = $arr[0];
			$keyItem = $arr[1];
		} else
			$keyItem = $filePath;
		$type = '';
		if (strpos($keyItem, 'ce_trans_templates_') !== false) {
			$type = 'template';
			$keyItem = str_replace('ce_trans_templates_', '', $keyItem);
		}
		elseif(strpos($keyItem, 'ce_trans_contents_') !== false) {
			$keyItem = str_replace('ce_trans_contents_', '', $keyItem);
			$type = 'content';
		}
		return ['idItem' => $idItem, 'type' => $type, 'keyItem' => $keyItem];
	}

	public static function compareDataWithTransOption($itemSource, $itemTarget, $transOption) {
		$result = array(
			'content' => 0,
			'title' => 0,
			'isTranslatable' => 0
		);
		$contentTarget = trim($itemTarget['content'],"\n\r ");
		$titleTarget = $itemTarget['title'];
		$contentSource = trim($itemSource['content'],"\n\r ");
		$titleSource = $itemSource['title'];
		switch ($transOption){
			case 'both':
				if(!$contentTarget || strcmp($contentSource, $contentTarget) === 0){
					$result['content'] = 1;
					$result['isTranslatable'] = 1;
				}
				if(!$titleTarget || strcmp($titleSource, $titleTarget) === 0){
					$result['title'] = 1;
					$result['isTranslatable'] = 1;
				}
				break;
			case 'only_empty':
				if(!$contentTarget){
					$result['content'] = 1;
					$result['isTranslatable'] = 1;
				}
				if(!$titleTarget){
					$result['title'] = 1;
					$result['isTranslatable'] = 1;
				}
				break;
			case 'same_source':
				if($contentSource == $contentTarget){
					$result['content'] = 1;
					$result['isTranslatable'] = 1;
				}
				if($titleSource == $titleTarget){
					$result['title'] = 1;
					$result['isTranslatable'] = 1;
				}
				break;
			case 'all':
				$result['content'] = 1;
				$result['title'] = 1;
				$result['isTranslatable'] = 1;
				break;
		}
		return $result;
	}


	public static function translateCE($langSource, $dataTarget) {
		$api = EtsTransApi::getInstance();
		$result = [
			'errors' => false,
			'message' => ''
		];
		if ($dataTarget) {
			try {
//				require_once _PS_MODULE_DIR_ . 'creativeelements/classes/wrappers/UId.php';
				$dataResult = [];
				$nbText = 0;
				foreach ($dataTarget as $index => $data) {
					$resultTrans = $api->translate($langSource, Language::getIsoById($data['langTarget']), [$data['data']], 'ce_trans');
					if(!isset($resultTrans['errors']) || !$resultTrans['errors']){
						$translated = $resultTrans['data'];
						if ($data['type'] == 'template') {
							$db_name = 'ce_theme_lang';
							$id_key = 'id_ce_theme';
//							$id_type = CE\UId::TEMPLATE;
						} else {
							$db_name = 'ce_content_lang';
							$id_key = 'id_ce_content';
//							$id_type = CE\UId::CONTENT;
						}
						$sql = "UPDATE `"._DB_PREFIX_. $db_name . "` SET ".pSQL($data['key'])."='".pSQL($translated[0], true)."' WHERE " . pSQL($id_key) . "=".(int)$data['id']." AND id_lang=".(int)$data['langTarget'];
						Db::getInstance()->execute($sql);
						$dataResult[] = $resultTrans;
						$nbText++;
						// update meta of ce
//						$uid = new CE\UId($data['id'], $id_type, $langSource);
					} else {
						$result = $resultTrans;
						break;
					}
				}
				$result['stop_translate'] = 1;
				$result['data'] = $dataResult;
				$result['nb_text'] = $nbText;
			} catch (\Exception $exception) {
				$result = [
					'errors' => false,
					'message' => $exception->getMessage(),
					'stop_translate' => 1
				];
			}
		} else {
			$result = [
				'errors' => false,
				'message' => 'No data to translate!',
				'stop_translate' => 1
			];
		}
		if (!isset($result['message']))
			$result['message'] = $result['errors'] ? 'Error when translate!' : 'Translate successfully!';
		return $result;

	}

	public static function translateOneClickCE($langSource, $langTarget, $trans_option, $type, $keyTemplate, $idTemplate = 0) {
		if (!$langSource || !$langTarget)
			return false;
		$context = Context::getContext();
		$dataTarget = array();
		if ($idTemplate && $type) {
			if ($type == 'template') {
				$itemSource = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
                WHERE id_ce_theme=" . (int)$idTemplate . " AND id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			else {
				$itemSource = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
				WHERE id_ce_content=" . (int)$idTemplate . " AND id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			if ($itemSource) {
				if (!isset($itemSource['content']))
					$itemSource = $itemSource[0];
				if (is_array($langTarget)) {
					foreach ($langTarget as $lang) {
						if ($type == 'template') {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
				                WHERE id_ce_theme=" . (int)$idTemplate . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						}
						else {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
                                WHERE id_ce_content=" . (int)$idTemplate . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						}
						if (isset($itemTarget) && is_array($itemTarget) && !isset($itemTarget['content']))
							$itemTarget = $itemTarget[0];
						$compareData = self::compareDataWithTransOption($itemSource, $itemTarget, $trans_option);
						if ($compareData['isTranslatable'])
							foreach ($itemSource as $k => $v)
								if ($v && isset($compareData[$k]) && $compareData[$k]) {
									$dataTarget[] = [
										'is_trans' => 1,
										'data' => $v,
										'type' => $type,
										'langTarget' => $lang,
										'id' => $idTemplate,
										'key' => $k
									];
								}
					}
				}
			}
		} else {
			$templates = [];
			$contents = [];
			if ($keyTemplate == 'ce_trans' || $type == 'template') {
				$templates = Db::getInstance()->executeS("SELECT content, title, id_ce_theme FROM `"._DB_PREFIX_."ce_theme_lang`
                WHERE id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			if ($keyTemplate == 'ce_trans' || $type == 'content') {
				$contents = Db::getInstance()->executeS("SELECT content, title, id_ce_content FROM `"._DB_PREFIX_."ce_content_lang`
				WHERE id_lang=" . (int)$langSource . " AND id_shop=" .(int)$context->shop->id);
			}
			$items = array_merge($templates, $contents);
			if (is_array($langTarget)) {
				foreach ($items as $item) {
					foreach ($langTarget as $lang) {
						if (isset($item['id_ce_theme']) && $item['id_ce_theme']) {
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_theme_lang`
                        WHERE id_ce_theme=" . (int)$item['id_ce_theme'] . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						} elseif (isset($item['id_ce_content']) && $item['id_ce_content'])
							$itemTarget = Db::getInstance()->executeS("SELECT content, title FROM `"._DB_PREFIX_."ce_content_lang`
                        WHERE id_ce_content=" . (int)$item['id_ce_content'] . " AND id_lang=" . (int)$lang . " AND id_shop=" .(int)$context->shop->id);
						if (isset($itemTarget) && is_array($itemTarget) && !isset($itemTarget['content']))
							$itemTarget = $itemTarget[0];
						$compareData = self::compareDataWithTransOption($item, $itemTarget, $trans_option);
						$id = isset($item['id_ce_content']) && $item['id_ce_content'] ? $item['id_ce_content'] : $item['id_ce_theme'];
						if ($compareData['isTranslatable'])
							foreach ($item as $k => $v)
								if ($v && isset($compareData[$k]) && $compareData[$k]) {
									$dataTarget[] = [
										'is_trans' => 1,
										'data' => $v,
										'type' => isset($item['id_ce_content']) && $item['id_ce_content'] ? 'content' : 'template',
										'langTarget' => $lang,
										'id' => $id,
										'key' => $k
									];
								}
					}
				}
			}
		}
		return self::translateCE($langSource, $dataTarget);
	}

}