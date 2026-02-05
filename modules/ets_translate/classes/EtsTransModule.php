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

class EtsTransModule extends EtsTransCore
{

    public function __construct($moduleName = null)
    {
        parent::__construct('module');
        $this->selectedName = rtrim($moduleName, '/ ');
    }

    public function setModuleName($moduleName)
    {
        $this->selectedName = $moduleName;
    }

    public function getModuleName()
    {
        if (isset($this->selectedName))
            return $this->selectedName;
        return null;
    }

    public function loadModuleFiles()
    {
        $context = Context::getContext();
        $this->deleteModuleCache();
        $moduleDir = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->selectedName;
        $moduleFiles = scandir($moduleDir);
        $rootDirFiles = array();
        foreach ($moduleFiles as $file) {
            if (in_array($file, self::$ignorefile)) {
                continue;
            }
            $filePath = $moduleDir . DIRECTORY_SEPARATOR . $file;

            if ($file == '.' || $file == '..' || !@is_file($filePath)) {
                continue;
            }
            if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }
            $rootDirFiles[] = $moduleDir . DIRECTORY_SEPARATOR . $file;
        }
        unset($file);
        $phpFiles = array_merge($rootDirFiles, $this->listFiles($moduleDir . '/classes/', array(), 'php'));
        $phpFiles = array_merge($phpFiles, $this->listFiles($moduleDir . '/controllers/', array(), 'php'));
        $directories = array(
            'php' => $phpFiles,
            'twig' => $this->listFiles($moduleDir . '/views/templates/', array(), 'twig'),
            'tpl' => $this->listFiles($moduleDir . '/views/templates/', array(), 'tpl'),
        );
        foreach ($directories as $type => $files) {
            foreach ($files as $file) {
                $cache = new EtsTransCache();
                $cache->cache_type = 'module';
                $cache->name = $this->selectedName;
                $cache->file_path = $file;
                $cache->file_type = $type;
                $cache->id_shop = $context->shop->id;
                $cache->save();
            }
        }
    }

    public function translateModule()
    {
        $result = array(
            'errors' => true,
            'nb_translated' => 0,
            'nb_char_translated' => 0,
            'stop_translate' => 1,
            'file_name' => '',
            'text_translated' => array()
        );
        if (!isset($this->langTarget) || (!isset($this->langTarget) && !$this->isDetectLanguage))
        	return $result;

        $context = Context::getContext();
        $cacheTrans = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_trans_cache` WHERE `cache_type`='module' AND `name`='" . pSQL($this->selectedName) . "' AND id_shop=" . (int)$context->shop->id);
        if (!$cacheTrans) {
            $config = EtsTransConfig::getInstance();
            $result['errors'] = false;
            $config->deletePauseData('module', '', $this->selectedName, '', $this->langTarget);
            return $result;
        }
        $textTrans = $this->getTextTranslateFormFilePath($cacheTrans['file_path'], $cacheTrans['file_type']);
        if (!$textTrans) {
            $this->deleteCacheItem($cacheTrans['id_ets_trans_cache']);
            return $this->translateModule();
        }
        $textTrans = $this->removeDuplicateItem($textTrans);
        $nbWillTranslate = count($textTrans);
        $moduleDir = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->selectedName;
        $textData = array();
        $textWillTranslate = array();
        $api = new EtsTransApi();
        $source = Language::getIsoById($this->langSource);
        $fileName = basename($cacheTrans['file_path'], '.' . $cacheTrans['file_type']);
	    $originData = array();
        foreach ($this->langTarget as $idLang) {
            $target = Language::getIsoById($idLang);
            $textTrans = $this->filterTransOption($textTrans, $moduleDir . '/translations/' . $target . '.php', $fileName);

            if (!$textWillTranslate)
                $textWillTranslate = $textTrans;
            else
                $textWillTranslate = array_merge($textWillTranslate, $textTrans);
            $originalText = $textTrans;
            $textTrans = $this->modifyTextTranslating($textTrans);
            $nbWillTranslate = count($textTrans);
            $resultTrans = $api->translate($source, $target, $textTrans, 'module');
            if (isset($resultTrans['errors']) && $resultTrans['errors']) {
                $result['errors'] = true;
                $result['message'] = isset($resultTrans['message']) && $resultTrans['message'] ? $resultTrans['message'] : '';
                $result['api_type'] = isset($resultTrans['api_type']) && $resultTrans['api_type'] ? $resultTrans['api_type'] : '';
                return $result;
            }
            $textTranslated = $resultTrans['data'];
            $textTranslated = $this->modifyTextTranslated($textTranslated);
            if (!@is_dir($moduleDir . '/translations')) {
                @mkdir($moduleDir . '/translations');
            }

            if (!@file_exists(($trans_file = $moduleDir . '/translations/' . $target . '.php'))) {
                $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
                @file_put_contents($trans_file, $content);
            }
            if (!is_writable($trans_file)) {
                continue;
            }
            $content = Tools::file_get_contents($trans_file);
            if (!$content || strpos($content, '<?php') === false) {
                $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
                @file_put_contents($trans_file, $content);
            }
            foreach ($originalText as $key => $text) {
                if (!isset($textTranslated[$key]) || !$textTranslated[$key]) {
                    continue;
                }
                $originData[$key] = $text;
                $textData[$key][$idLang] = $textTranslated[$key];
                $text = preg_replace("/\\\*'/", "\'", $text);
                $strMd5 = md5($text);
                $keyMd5 = '<{' . $this->selectedName . '}prestashop>' . $fileName . '_' . $strMd5;
                preg_match('/\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]/', $content, $matches);
                if ($matches) {
                    $content = preg_replace('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', '${1}' . pSQL($textTranslated[$key]) . '${3}', $content);
                } else
                    $content .= "\n\$_MODULE['" . $keyMd5 . "']='" . pSQL($textTranslated[$key]) . "';";
            }

            @file_put_contents($trans_file, $content);
        }
        $result['errors'] = false;
        $result['nb_translated'] = $nbWillTranslate;
        $nbChar = 0;
        foreach ($textWillTranslate as $text) {
            $nbChar += Tools::strlen($text);
        }
        $result['nb_char_translated'] = $nbChar;
        $result['stop_translate'] = false;
        $result['text_translated'] = $textData;
        $result['text_key'] = $originData;
        $result['file_name'] = $fileName;
        if (!connection_aborted())
            $this->deleteCacheItem($cacheTrans['id_ets_trans_cache']);
        return $result;
    }

    protected function filterTransOption($textTrans, $filePath, $fileName)
    {
        if (!@file_exists($filePath)) {
            return $textTrans;
        }
        $content = Tools::file_get_contents($filePath);
        $result = array();
        foreach ($textTrans as $text) {

            $strMd5 = md5($text);
            $keyMd5 = '<{' . $this->selectedName . '}prestashop>' . $fileName . '_' . $strMd5;
            $transSaved = null;
            preg_match('/\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\'(.*)\';/', $content, $matches);

            if ($matches) {
                $transSaved = isset($matches[1]) && $matches[1] ? $matches[1] : '';
            }

            $textTrans = addslashes($text);
            switch ($this->transOption) {
                case 'both':
                    if (!$transSaved || $transSaved == $textTrans) {
                        $result[] = $text;
                    }
                    break;
                case 'only_empty':
                    if (!$transSaved) {
                        $result[] = $text;
                    }
                    break;
                case 'same_source':
                    if ($transSaved == $textTrans) {
                        $result[] = $text;
                    }
                    break;
                case 'all':
                    $result[] = $text;
                    break;
            }

        }

        return $result;
    }

    public function deleteModuleCache()
    {
        $context = Context::getContext();
        return Db::getInstance()->execute("DELETE FROM `" . _DB_PREFIX_ . "ets_trans_cache` WHERE `cache_type`='module' AND `name`='" . pSQL($this->selectedName) . "' AND id_shop=" . (int)$context->shop->id);
    }

    public function analysisModule()
    {
        $context = Context::getContext();
        $cacheTrans = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_trans_cache` WHERE `cache_type`='module' AND `name`='" . pSQL($this->selectedName) . "' AND status=0 AND id_shop=" . (int)$context->shop->id);
        if (!$cacheTrans) {
            return array(
                'nb_text' => 0,
                'nb_char' => 0,
                'nb_money' => 0,
                'stop' => 1
            );
        }
        $textTrans = $this->getTextTranslateFormFilePath($cacheTrans['file_path'], $cacheTrans['file_type']);
        if (!$textTrans) {
            $this->updateStatusCacheItem($cacheTrans['id_ets_trans_cache'], 1);
            return $this->analysisModule();
        }

        $textTrans = $this->removeDuplicateItem($textTrans);
        $moduleDir = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->selectedName;
        $textWillTranslate = array();
	    $nbWillTranslate = 0;
        $fileName = basename($cacheTrans['file_path'], '.' . $cacheTrans['file_type']);
        foreach ($this->langTarget as $idLang) {
            $target = Language::getIsoById($idLang);
            $textTrans = $this->filterTransOption($textTrans, $moduleDir . '/translations/' . $target . '.php', $fileName);
            $nbWillTranslate = count($textTrans);
            if (!$textWillTranslate)
                $textWillTranslate = $textTrans;
            else
                $textWillTranslate = array_merge($textWillTranslate, $textTrans);
            $textTrans = $this->modifyTextTranslating($textTrans);
        }
        $nbChar = 0;
        foreach ($textWillTranslate as $text) {
            $nbChar += Tools::strlen($text);
        }
        $api = EtsTransApi::getInstance();
        $totalItem = (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_trans_cache` WHERE `cache_type`='module' AND `name`='" . pSQL($this->selectedName) . "' AND status=0 AND id_shop=" . (int)$context->shop->id);
        $this->updateStatusCacheItem($cacheTrans['id_ets_trans_cache'], 1);
        return array(
            'nb_text' => $nbWillTranslate,
            'nb_char' => $nbChar,
            'nb_money' => $api->getTotalFeeTranslate($nbChar),
            'stop' => $totalItem > 0 ? 0 : 1
        );
    }

    public static function analysisModuleMegamenu($formData)
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option'])) {
            return false;
        }
        $source = $formData['trans_source'];
        $target = is_array($formData['trans_target']) ? $formData['trans_target'] : explode(',', $formData['trans_target']);
        $transOption = $formData['trans_option'];
        $tableTrans = array(
            'ets_mm_menu_lang' => array('title', 'bubble_text'),
            'ets_mm_tab_lang' => array('title', 'bubble_text'),
            'ets_mm_block_lang' => array('title', 'content'),
        );
        $result = array(
            'nb_text' => 0,
            'nb_char' => 0,
            'nb_money' => 0,
            'stop' => 1
        );
        $api = EtsTransApi::getInstance();
        foreach ($tableTrans as $tblName => $fields) {
            $menus = self::getMegamenuData($tblName);
            $dataSource = array();
            $dataTarget = array();
            $tt = array();
            foreach ($menus as $menu) {
                if ($menu['id_lang'] == $source) {
                    foreach ($fields as $field) {
                        $dataSource[$field] = $menu[$field];
                    }
                } else {
                    foreach ($target as $idLang) {
                        if ($menu['id_lang'] == $idLang) {
                            foreach ($fields as $field) {
                                if ($dataSource[$field] && EtsTransCore::checkTransOption($transOption, $dataSource[$field], $menu[$field])) {
                                    $dataTarget[$idLang][$field] = 1;
                                    $result['nb_char'] += Tools::strlen($dataSource[$field]);
                                    if(!in_array($dataSource[$field], $tt)){
                                        $tt[] = $dataSource[$field];
                                        $result['nb_text']++;
                                    }
                                } else {
                                    $dataTarget[$idLang][$field] = 0;
                                }
                            }
                        }
                    }
                }
            }
        }
        $result['nb_money'] = $api->getTotalFeeTranslate($result['nb_char']);
        if(!$result['nb_char']){
            $result['nb_text'] = 0;
        }
        return $result;
    }

    public static function transAllMegamenu($formData)
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option'])) {
            return false;
        }
        $source = $formData['trans_source'];
        $target = is_array($formData['trans_target']) ? $formData['trans_target'] : explode(',', $formData['trans_target']);
        $transOption = $formData['trans_option'];
        $tableTrans = array(
            'ets_mm_menu_lang' => array('id_menu', 'title', 'bubble_text'),
            'ets_mm_tab_lang' => array('id_tab', 'title', 'bubble_text'),
            'ets_mm_block_lang' => array('id_block', 'title', 'content'),
        );
        $result = array(
            'nb_text' => 0,
            'nb_char' => 0,
            'nb_money' => 0,
            'stop' => 1
        );
        $api = EtsTransApi::getInstance();
        foreach ($tableTrans as $tblName => $fields) {
            $menus = self::getMegamenuData($tblName);
            $dataSource = array();
            $dataTarget = array();
            foreach ($menus as $menu) {
                if ($menu['id_lang'] == $source) {
                    foreach ($fields as $k => $field) {
                        if ($k == 0) {
                            continue;
                        }
                        $dataSource[$menu[$fields[0]] . '|' . $field] = $menu[$field];
                    }
                }
            }
            foreach ($target as $idLang) {
                foreach ($menus as $menu) {
                    if ($menu['id_lang'] == $idLang) {
                        foreach ($fields as $k => $field) {
                            if ($k == 0) {
                                continue;
                            }
                            $keyData = $menu[$fields[0]] . '|' . $field;
                            if (isset($dataSource[$keyData]) && $dataSource[$keyData] && EtsTransCore::checkTransOption($transOption, $dataSource[$keyData], $menu[$field])) {
                                $dataTarget[$idLang][$keyData] = 1;
                            } else {
                                $dataTarget[$idLang][$keyData] = 0;
                            }
                        }
                    }
                }
            }

            $tt = array();
            foreach ($target as $ldLang) {
                $nbChar = 0;
                $textTrans = array();
                $listKey = array();
                foreach ($dataSource as $key => $text) {
                    if ($text && isset($dataTarget[$ldLang][$key]) && $dataTarget[$ldLang][$key]) {
                        $textTrans[] = $text;
                        $nbChar += Tools::strlen($text);
                        if(!in_array($text, $tt)){
                            $tt[] = $text;
                            $result['nb_text']++;
                        }
                        $listKey[] = $key;
                    }
                }
                $result['nb_char'] += $nbChar;
                if ($textTrans) {
                    $timStartTrans = microtime(true);
                    $sourceLang = self::getAutoDetectLanguage() ? null : Language::getIsoById($formData['trans_source']);
                    $resTrans = $api->translate($sourceLang, Language::getIsoById($ldLang), $textTrans, 'megamenu');
                    if ($resTrans && (!isset($resTrans['errors']) || !$resTrans['errors'])) {
                        $translated = $resTrans['data'];
                        $idTrans = array();
                        foreach ($listKey as $k => $key) {
                            $keyData = explode('|', $key);
                            $idTrans[] = $keyData[0];
                            Db::getInstance()->execute("UPDATE " . _DB_PREFIX_ . pSQL($tblName) . " SET " . pSQL($keyData[1]) . "='" . pSQL($translated[$k], true) . "' WHERE id_lang=" . (int)$ldLang . " AND `" . pSQL($fields[0]) . "`=" . (int)$keyData[0]);
                        }
                        $result['nb_money'] += $api->getTotalFeeTranslate($nbChar);
                        EtsTransLog::logTranslate('megamenu', true, $timStartTrans, $source, $ldLang, null, self::getLogIdTrans($listKey), null);
                    }
                    else{
                        EtsTransLog::logTranslate('megamenu', false, $timStartTrans, $source, $ldLang, null, self::getLogIdTrans($listKey), null, isset($resTrans['message']) && $resTrans['message'] ? $resTrans['message'] : null);

                        return $resTrans;
                    }
                }
            }

        }
        return $result;
    }

    public static function getMegamenuData($tblName, $idShop = null)
    {
        if (!$idShop) {
            $idShop = Context::getContext()->shop->id;
        }
	    try {
		    switch ($tblName) {
			    case 'ets_mm_menu_lang':
				    $menus = Db::getInstance()->executeS("SELECT a.title, a.bubble_text, a.id_lang, a.id_menu FROM " . _DB_PREFIX_ . pSQL($tblName) . " as a 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_menu` m ON m.id_menu = a.id_menu 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_menu_shop` ms ON ms.id_menu = m.id_menu
                WHERE ms.id_shop=" . (int)$idShop);
				    break;
			    case 'ets_mm_tab_lang':
				    $menus = Db::getInstance()->executeS("SELECT a.title, a.bubble_text, a.id_lang, a.id_tab FROM " . _DB_PREFIX_ . pSQL($tblName) . " as a 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_tab` t ON t.id_tab = a.id_tab 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_menu` m ON m.id_menu = t.id_menu 
                LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_menu_shop` ms ON ms.id_menu = m.id_menu
                WHERE ms.id_shop=" . (int)$idShop);
				    break;
			    case 'ets_mm_block_lang':
				    $menus = Db::getInstance()->executeS("SELECT a.title, a.content, a.id_block, a.id_lang FROM `" . _DB_PREFIX_ . "ets_mm_block_lang` a
                            LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_block` b ON b.id_block = a.id_block
                            LEFT JOIN (SELECT c1.id_column,m1.id_menu FROM `" . _DB_PREFIX_ . "ets_mm_column` c1
                            INNER JOIN `" . _DB_PREFIX_ . "ets_mm_tab` t1 ON (t1.id_tab= c1.id_tab)
                            INNER JOIN `" . _DB_PREFIX_ . "ets_mm_menu` m1 ON(t1.id_menu=m1.id_menu)
                            ) as column1 ON (b.id_column = column1.id_column)
                            LEFT JOIN (SELECT c2.id_column,m2.id_menu FROM `" . _DB_PREFIX_ . "ets_mm_column` c2
                            INNER JOIN `" . _DB_PREFIX_ . "ets_mm_menu` m2 ON(m2.id_menu=c2.id_menu)
                            ) as column2 ON (b.id_column = column2.id_column)
                            LEFT JOIN `" . _DB_PREFIX_ . "ets_mm_menu_shop` ms ON (ms.id_menu = column1.id_menu or ms.id_menu = column2.id_menu)
                            WHERE ms.id_shop = " . (int)$idShop);
				    break;
			    default:
				    $menus = array();
				    break;
		    }
		    return $menus;
	    } catch (\Exception $exception) {
        	return [];
	    }
    }


	public static function checkUrlAliasBlogPostExists($url_alias, $id_post)
	{
		return Db::getInstance()->getValue('SELECT ps.id_post FROM `' . _DB_PREFIX_ . 'ybc_blog_post_lang` pl
        INNER JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_shop` ps ON ps.id_post= pl.id_post AND ps.id_shop="' . (int)Context::getContext()->shop->id . '"
        WHERE pl.url_alias ="' . pSQL($url_alias) . '" AND ps.id_post!="' . (int)$id_post . '"');
	}

	public static function checkUrlAliasBlogCategoryExists($url_alias,$id_category)
	{
		return Db::getInstance()->getValue('SELECT cs.id_category FROM `'._DB_PREFIX_.'ybc_blog_category_lang` cl
        INNER JOIN `'._DB_PREFIX_.'ybc_blog_category_shop` cs ON cs.id_category= cl.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'"
        WHERE cl.url_alias ="'.pSQL($url_alias).'" AND cs.id_category!="'.(int)$id_category.'"');
	}
    public static function translateAllBlog($formData, $analysis = false)
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option']) || !isset($formData['blog_type'])) {
            return false;
        }

        $transLinkRewrite = (int)Configuration::get('ETS_TRANS_AUTO_GENERATE_LINK_REWRITE');
        if (isset($formData['auto_generate_link_rewrite'])){
            $transLinkRewrite = (int)$formData['auto_generate_link_rewrite'];
        }
        $blogType = $formData['blog_type'];
        $id_shop = Context::getContext()->shop->id;
        $offset = isset($formData['offset']) ? (int)$formData['offset'] : 0;
        $limit = 5;
        $pageId = isset($formData['page_id']) && $formData['page_id'] ? $formData['page_id'] : '';
        $ids = array();
	    $idCol = 'id_post';
        if($blogType == 'post'){
            $tblName = 'ybc_blog_post_lang';
            $idCol = 'id_post';
            $fields = array('title', 'meta_title', 'description', 'short_description', 'meta_keywords', 'meta_description');
            if($pageId){
                $ids = explode(',', $pageId);
            }
            else{
                $idData  = Db::getInstance()->executeS("SELECT p.id_post FROM `"._DB_PREFIX_."ybc_blog_post` p 
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_post_shop` ps ON p.id_post = ps.id_post
            WHERE ps.id_shop=".(int)$id_shop." LIMIT ".(int)$offset.",".(int)$limit);
                $ids = array();
                foreach ($idData as $item){
                    $ids[] = $item['id_post'];
                }
            }
            if(!$ids){
                $posts = array();
            }
            else
                $posts = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."ybc_blog_post_lang` WHERE id_post IN(".implode(',', array_map('intval', $ids)).")");
        }
        elseif($blogType == 'category'){
            $tblName = 'ybc_blog_category_lang';
            $idCol = 'id_category';
            $fields = array('title', 'meta_title', 'description', 'meta_keywords', 'meta_description');
            if($pageId){
                $ids = explode(',', $pageId);
            }
            else{
                $idData = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."ybc_blog_category` c 
            LEFT JOIN `"._DB_PREFIX_."ybc_blog_category_shop` cs ON c.id_category = cs.id_category 
            WHERE cs.id_shop=".(int)$id_shop." LIMIT ".(int)$offset.",".(int)$limit);
                $ids = array();
                foreach ($idData as $item){
                    $ids[] = $item['id_category'];
                }
            }

            if(!$ids){
                $posts = array();
            }
            else
                $posts = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."ybc_blog_category_lang` WHERE `id_category` IN(".implode(',', array_map('intval', $ids)).")");
        }
        if(!isset($tblName) || !isset($fields) || !isset($posts)){
            return array(
                'errors' => true
            );
        }

        $result = array(
            'nb_text' => 0,
            'nb_char' => 0,
            'nb_money' => 0,
            'stop' => 0,
            'total_item' => 0,
            'blog_type' => $blogType,
            'offset' => $offset+$limit,
        );

        if(!$posts){
            $result['stop'] = 1;
            if(!$analysis){
                $config = EtsTransConfig::getInstance();
                $config->deletePauseData('blog_'.$blogType);
            }
            if($analysis){
                $result['total_item'] = self::getTotalBlogItem($blogType);
            }
            return $result;
        }

        $dataSource = array();
        $dataSourceFromRequest = isset($formData['trans_data']) && isset($formData['trans_data']['source']) ? $formData['trans_data']['source'] : [];
        foreach ($posts as $post){
            if((int)$post['id_lang'] == (int)$formData['trans_source']){
                foreach ($fields as $field){
                    $keyTrans = $post[$idCol].'|'.$field;
                    if (isset($dataSourceFromRequest[$field . '_'])) {
	                    $dataSource[$keyTrans] = $dataSourceFromRequest[$field . '_'];
                    	// update new data source to database
	                    Db::getInstance()->execute("UPDATE "._DB_PREFIX_.pSQL($tblName)." SET ".pSQL($field)."='".pSQL($dataSource[$keyTrans], true)."' WHERE id_lang=".(int)$formData['trans_data']['source']." AND ".pSQL($idCol)."=".(int)$post[$idCol]);
	                    if($field == 'title' && $transLinkRewrite){
		                    if($urlRewrite = EtsTransPage::slugify($dataSource[$keyTrans])){
		                    	if ($blogType == 'post' && self::checkUrlAliasBlogPostExists($urlRewrite, $post[$idCol]))
				                    $urlRewrite .= '-1';
		                    	if ($blogType == 'category' && self::checkUrlAliasBlogCategoryExists($urlRewrite, $post[$idCol]))
				                    $urlRewrite .= '-1';
			                    Db::getInstance()->execute("UPDATE "._DB_PREFIX_.pSQL($tblName)." SET `url_alias`='".pSQL($urlRewrite)."' WHERE id_lang=".(int)$formData['trans_data']['source']." AND ".pSQL($idCol)."=".(int)$post[$idCol]);
		                    }
	                    }
                    } else {
	                    $dataSource[$keyTrans] = $post[$field];
                    }
                }
            }

        }
        if(!$dataSource){
            return $result;
        }
        $dataTarget = array();
        $langTarget = is_array($formData['trans_target']) ? $formData['trans_target'] : explode(',', $formData['trans_target']);
        $tt = array();
        foreach ($langTarget as $idLang){
            foreach ($posts as $post){
                if($post['id_lang'] != $idLang){
                    continue;
                }
                foreach ($fields as $field){
                    $keyTrans = $post[$idCol].'|'.$field;
                    if(isset($dataSource[$keyTrans]) && $dataSource[$keyTrans] && (EtsTransCore::checkTransOption($formData['trans_option'], $dataSource[$keyTrans], $post[$field]) || $formData['trans_data']['target'][$idLang][$field . '_'] == 1)){
                        $dataTarget[$idLang][$keyTrans] = 1;
                        if(!in_array($dataSource[$keyTrans], $tt)){
                            $tt[] = $dataSource[$keyTrans];
                            $result['nb_text']++;
                        }
                        $result['nb_char'] += Tools::strlen($dataSource[$keyTrans]);
                    }
                    else{

                        $dataTarget[$idLang][$keyTrans] = 0;
                    }

                }
            }
        }
        $api = EtsTransApi::getInstance();
        if(!$analysis){
            foreach ($dataTarget as $idLang=>$item){
                $textTrans = array();
                $listKey = array();
                foreach ($item as $ik=>$status){
                    if($status){
                        $textTrans[] = $dataSource[$ik];
                        $listKey[] = $ik;
                    }
                }
                if($textTrans){
                    $timStartTrans = microtime(true);
                    $source = self::getAutoDetectLanguage() ? null : Language::getIsoById($formData['trans_source']);
                    $resultTrans = $api->translate($source, Language::getIsoById($idLang), $textTrans, 'blog');
                    $pageType = $blogType == 'post' ? 'blog_post' : 'blog_category';
                    if(!isset($resultTrans['errors']) || !$resultTrans['errors']){
                        $translated = $resultTrans['data'];
                        foreach ($listKey as $i=>$key){
                            $keyData = explode('|', $key);
                            Db::getInstance()->execute("UPDATE "._DB_PREFIX_.pSQL($tblName)." SET ".pSQL($keyData[1])."='".pSQL($translated[$i], true)."' WHERE id_lang=".(int)$idLang." AND ".pSQL($idCol)."=".(int)$keyData[0]);
                            if($keyData[1] == 'title' && $transLinkRewrite){

                                if($urlRewrite = EtsTransPage::slugify($translated[$i])){
	                                if ($blogType == 'post' && self::checkUrlAliasBlogPostExists($urlRewrite, $keyData[0]))
		                                $urlRewrite .= '-1';
	                                if ($blogType == 'category' && self::checkUrlAliasBlogCategoryExists($urlRewrite, $keyData[0]))
		                                $urlRewrite .= '-1';
                                    Db::getInstance()->execute("UPDATE "._DB_PREFIX_.pSQL($tblName)." SET `url_alias`='".pSQL($urlRewrite)."' WHERE id_lang=".(int)$idLang." AND ".pSQL($idCol)."=".(int)$keyData[0]);
                                }
                            }
                        }
                        if(!$source){
                            $source = isset($resultTrans['detectedSourceLanguage']) && $resultTrans['detectedSourceLanguage'] ? $resultTrans['detectedSourceLanguage'] :  null;
                        }
                        EtsTransLog::logTranslate($pageType, true, $timStartTrans, $source, $idLang, null, self::getLogIdTrans($listKey), null);
                    }
                    else{
                        EtsTransLog::logTranslate($pageType, false, $timStartTrans, $source, $idLang, null, self::getLogIdTrans($listKey), null, isset($resultTrans['message']) && $resultTrans['message'] ? $resultTrans['message'] : null);

                        return $resultTrans;
                    }
                }
            }
        }

        $result['nb_money'] = $api->getTotalFeeTranslate($result['nb_char']);
        if(!$posts || count($ids) < $limit || (isset($formData['page_id']) && $formData['page_id'])){
            $result['stop'] = 1;
            if(!$analysis){
                $config = EtsTransConfig::getInstance();
                $config->deletePauseData('blog_'.$blogType);
            }
            if($analysis)
                $result['total_item'] = self::getTotalBlogItem($blogType);
        }
        return $result;
    }

    public static function getTotalBlogItem($blogType, $id_shop = null){
        if($id_shop){
            $id_shop = Context::getContext()->shop->id;
        }
        if($blogType == 'post'){
            return (int)Db::getInstance()->getValue("SELECT * FROM `"._DB_PREFIX_."ybc_blog_post_shop` WHERE `id_shop`=".(int)$id_shop);
        }
        elseif($blogType == 'category'){
            return (int)Db::getInstance()->getValue("SELECT * FROM `"._DB_PREFIX_."ybc_blog_category_shop` WHERE `id_shop`=".(int)$id_shop);
        }
    }


    public static function analysisModuleBlog($formData)
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option']) || !isset($formData['blog_type'])) {
            return false;
        }

        return self::translateAllBlog($formData, true);
    }

	private static function getAutoDetectLanguage($autoDetectLanguage = null) {
		if ($autoDetectLanguage === null) {
			/** @var Ets_Translate $module */
			$module = Module::getInstanceByName('ets_translate');
			return $module->isAutoDetectLanguage();
		}
		return $autoDetectLanguage;
	}

    public static function translateAllModulePc($formData, $analysis = false, $pcType = null, $noLimit = false)
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option'])) {
            return false;
        }
        $offset = isset($formData['offset']) ? (int)$formData['offset'] : 0;
        $limit = 20;
        $pageId = isset($formData['page_id']) && $formData['page_id'] ? $formData['page_id'] : '';
        $ids = array();
        $result = array(
            'nb_text' => 0,
            'nb_char' => 0,
            'nb_money' => 0,
            'stop' => 0,
            'total_item' => 0,
            'offset' => $offset+$limit,
        );

        $tpmType = 0;
        if($pageId){
            $ids = is_array($pageId) ? $pageId : explode(',', $pageId);
        }
        else
        {
            if($pcType == 'review' || $pcType == 'question'){
                $comments = Db::getInstance()->executeS("SELECT id_ets_rv_product_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_product_comment` 
                                        WHERE 1".($pcType ? " AND `question`=".($pcType == 'question' ? 1 : 0) : "").($noLimit ? "" : " 
                                        LIMIT ".(int)$offset.",".(int)$limit));
            }
            elseif($pcType == 'comment' || $pcType == 'question_comment' || $pcType == 'answer'){
                $filterWhere = "";
                if($pcType == 'comment'){
                    $filterWhere .= " AND `question`=0 AND answer=0";
                }
                elseif($pcType == 'question_comment'){
                    $filterWhere .= " AND `question`=1 AND answer=0";
                }
                elseif($pcType == 'answer'){
                    $filterWhere .= " AND `question`=1 AND answer=1";
                }
	            if (isset($formData['page_product_comment_id']) && $formData['page_product_comment_id'])
		            $filterWhere .= " AND `id_ets_rv_product_comment`=" . (int)$formData['page_product_comment_id'];
                $comments = Db::getInstance()->executeS("SELECT id_ets_rv_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_comment` 
                                        WHERE 1 ".pSQL($filterWhere).($noLimit ? "" : "
                                        LIMIT ".(int)$offset.",".(int)$limit));
            }
            elseif($pcType == 'reply' || $pcType == 'answer_comment'){
                $comments = Db::getInstance()->executeS("SELECT id_ets_rv_reply_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_reply_comment` 
                                        WHERE 1".($pcType ? " AND `question`=".($pcType == 'reply' ? 0 : 1) : "").($noLimit ? "" : " 
                                        LIMIT ".(int)$offset.",".(int)$limit));
            }
            else{
                $productCommentTotal = (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."ets_rv_product_comment`");

                if($productCommentTotal <= $offset){
                    $commentTotal = (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."ets_rv_comment`");
                    if($commentTotal+$productCommentTotal <= $offset){
                        $replyTotal = (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."ets_rv_reply_comment`");
                        if($replyTotal+$commentTotal+$productCommentTotal <= $offset){
                            $comments = array();
                        }
                        else{
                            $tpmType=3;
                            $offset2 = $offset - ($productCommentTotal+$commentTotal);
                            $comments = Db::getInstance()->executeS("SELECT id_ets_rv_reply_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_reply_comment` 
                                        LIMIT ".(int)$offset2.",".(int)$limit);
                        }
                    }
                    else{
                        $tpmType=2;
                        $offset2 = $offset- $productCommentTotal;
                        $comments = Db::getInstance()->executeS("SELECT id_ets_rv_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_comment` 
                                        LIMIT ".(int)$offset2.",".(int)$limit);
                        $result['offset'] = count($comments)+$productCommentTotal;
                    }
                }
                else{
                    $comments = Db::getInstance()->executeS("SELECT id_ets_rv_product_comment as id_pc FROM `"._DB_PREFIX_."ets_rv_product_comment` 
                                        LIMIT ".(int)$offset.",".(int)$limit);
                    $tpmType=1;
                }
            }
            $result['offset'] = count($comments) < $limit ? $offset+ count($comments) : $offset+$limit;
            if(!$comments){
                $result['stop'] = 1;
                if(!$analysis){
                    $config = EtsTransConfig::getInstance();
                    $config->deletePauseData('pc', $pcType);
                }
                if($analysis){
                    $result['total_item'] = self::getTotalProductComments($pcType);
                }
                return $result;
            }
            foreach ($comments as $cmt){
                $ids[] = $cmt['id_pc'];
            }
        }

        if($pcType == 'review' || $pcType == 'question' || $tpmType==1) {
        	$sql = "SELECT cl.id_ets_rv_product_comment as id_pc, cl.title, cl.content, cl.id_lang, col.id_lang as origin_lang 
                                                    FROM `" . _DB_PREFIX_ . "ets_rv_product_comment_lang` cl
                                                    JOIN `" . _DB_PREFIX_ . "ets_rv_product_comment` c ON cl.id_ets_rv_product_comment = c.id_ets_rv_product_comment
                                                    LEFT JOIN `" . _DB_PREFIX_ . "ets_rv_product_comment_origin_lang` col ON c.id_ets_rv_product_comment=col.id_ets_rv_product_comment
                                                    WHERE c.id_ets_rv_product_comment IN (" . implode(',', array_map('intval', $ids)) . ") GROUP BY cl.id_ets_rv_product_comment, cl.id_lang, col.id_lang";
            $cmtLang = Db::getInstance()->executeS($sql);
            $fields = array('title', 'content');
        }
        elseif($pcType == 'comment' || $pcType == 'question_comment' || $pcType == 'answer' || $tpmType==2){
            $cmtLang = Db::getInstance()->executeS("SELECT cl.id_ets_rv_comment as id_pc, cl.content, cl.id_lang, col.id_lang as origin_lang 
                                                    FROM `" . _DB_PREFIX_ . "ets_rv_comment_lang` cl
                                                    JOIN `" . _DB_PREFIX_ . "ets_rv_comment` c ON cl.id_ets_rv_comment = c.id_ets_rv_comment
                                                    LEFT JOIN `" . _DB_PREFIX_ . "ets_rv_comment_origin_lang` col ON c.id_ets_rv_comment=col.id_ets_rv_comment
                                                    WHERE c.id_ets_rv_comment IN (" . implode(',', array_map('intval', $ids)) . ") GROUP BY cl.id_ets_rv_comment, cl.id_lang, col.id_lang");
            $fields = array('content');
        }
        elseif($pcType == 'reply' || $pcType == 'answer_comment' || $tpmType==3){
            $cmtLang = Db::getInstance()->executeS("SELECT cl.id_ets_rv_reply_comment as id_pc, cl.content, cl.id_lang, col.id_lang as origin_lang 
                                                    FROM `" . _DB_PREFIX_ . "ets_rv_reply_comment_lang` cl
                                                    JOIN `" . _DB_PREFIX_ . "ets_rv_reply_comment` c ON cl.id_ets_rv_reply_comment = c.id_ets_rv_reply_comment
                                                    LEFT JOIN `" . _DB_PREFIX_ . "ets_rv_reply_comment_origin_lang` col ON c.id_ets_rv_reply_comment=col.id_ets_rv_reply_comment
                                                    WHERE c.id_ets_rv_reply_comment IN (" . implode(',', array_map('intval', $ids)) . ") GROUP BY cl.id_ets_rv_reply_comment, cl.id_lang, col.id_lang");
            $fields = array('content');
        }
        else{
            $cmtLang = array();
            $fields = array();
        }

        if(!$cmtLang){
            $result['stop'] = 1;
            if(!$analysis){
                $config = EtsTransConfig::getInstance();
                $config->deletePauseData('pc', $pcType);
            }
            $config = EtsTransConfig::getInstance();
            $config->deletePauseData('pc', $pcType);
            if($analysis){
                $result['total_item'] = self::getTotalProductComments($pcType);
            }
            return $result;
        }

        $dataSource = array();
        foreach ($cmtLang as $cmtItem){
            if((int)$cmtItem['id_lang'] == (int)$cmtItem['origin_lang']){
	            foreach ($fields as $field){
		            $keyTrans = $cmtItem['id_pc'].'|'.$field;
		            $dataSource[$keyTrans] = $cmtItem[$field];
	            }
            }
        }
        if(!$dataSource){
            return $result;
        }
        $dataTarget = array();
        $langTarget = is_array($formData['trans_target']) ? $formData['trans_target'] : explode(',', $formData['trans_target']);
        //Get all languages excerpt source language;
        foreach ($langTarget as $idLang){
            if(isset($dataTarget[$idLang]) && $dataTarget[$idLang]){
                continue;
            }
            $dataTarget[$idLang] = array();
            foreach ($cmtLang as $cmtItem){
                $tt = array();
                foreach ($fields as $field){
                    $keyTrans = $cmtItem['id_pc'].'|'.$field;
                    if(isset($dataSource[$keyTrans]) && $dataSource[$keyTrans] && EtsTransCore::checkTransOption($formData['trans_option'], $dataSource[$keyTrans], $cmtItem[$field])){
                        $dataTarget[$idLang][$keyTrans] = array(
                            'is_trans' => 1,
                            'lang_source' => (int)$formData['trans_source'] == 0 ? $cmtItem['origin_lang'] : (int)$formData['trans_source']
                        );
                        if(!in_array($dataSource[$keyTrans],$tt)){
                            $tt[] = $dataSource[$keyTrans];
                            $result['nb_text']++;
                        }
                        $result['nb_char'] += Tools::strlen($dataSource[$keyTrans]);
                    }
                    else{
                        $dataTarget[$idLang][$keyTrans] = array(
                            'is_trans' => 0,
                            'lang_source' => (int)$formData['trans_source'] == 0 ? $cmtItem['origin_lang'] : (int)$formData['trans_source']
                        );;
                    }
                }
            }
        }
        $transAllType = null;
        if(!$pcType){
            if($tpmType == 1){
                $transAllType = 'review';
            }
            elseif($tpmType == 2){
                $transAllType = 'comment';
            }
            elseif($tpmType == 3){
                $transAllType = 'reply';
            }
        }
        $api = EtsTransApi::getInstance();
        if(!$analysis){
            foreach ($dataTarget as $idLang=>$item){
                $textTrans = array();
                $listKey = array();
                foreach ($item as $ik=>$statusData){
                    if($statusData['is_trans']){
	                      $id_lang_source = $statusData['lang_source'];
                        $textTrans[] = $dataSource[$ik];
                        $listKey[] = array(
                            'key' => $ik,
                            'lang_source' => $statusData['lang_source']
                        );
                    }
                }
                if($textTrans){
                    $timStartTrans = microtime(true);
                    //Auto detect source language if config is ON
	                $detectedSourceLanguage = self::getAutoDetectLanguage();
	                if (isset($formData['auto_detect_language'])) {
		                $detectedSourceLanguage = $formData['auto_detect_language'];
	                }

					$source = $detectedSourceLanguage ? null : (isset($id_lang_source) && $id_lang_source ? $id_lang_source: Language::getIsoById($formData['trans_source'])) ;
                    $langSource =$formData['trans_source'];
                    $resultTrans = $api->translate($source, Language::getIsoById($idLang), $textTrans, 'pc');
                    if(!isset($resultTrans['errors']) || !$resultTrans['errors']){
                        $translated = $resultTrans['data'];
                        foreach ($listKey as $i=>$key){
                            $keyData = explode('|', $key['key']);
                            if($key['lang_source'] !== $idLang) {
                                self::updateTransModulePcItem($keyData[1], $translated[$i], (int)$keyData[0], (int)$idLang, ($pcType ?: $transAllType));
                            }
                        }
                        //Check data in source language if auto detect is ON
//                        if(!$source){
//                            $detectedSourceLanguage = isset($resultTrans['detectedSourceLanguage']) ? $resultTrans['detectedSourceLanguage'] : null;
//
//                            if($detectedSourceLanguage){
//                                $langSource = $detectedSourceLanguage;
//                                foreach ($listKey as $i=>$key){
//                                    if(isset($detectedSourceLanguage[$i]) && $detectedSourceLanguage[$i] !== EtsTransApi::getLangCodeFromIdLang($key['lang_source'])){
//                                        $resultSourceTrans = $api->translate(null, Language::getIsoById($key['lang_source']), array($textTrans[$i]), 'pc');
//                                        if(!isset($resultSourceTrans['errors']) || !$resultSourceTrans['errors']){
//                                            $sourceTranslated = $resultSourceTrans['data'];
//                                            $keyData = explode('|', $key['key']);
//                                            self::updateTransModulePcItem($keyData[1],$sourceTranslated[0],(int)$keyData[0],(int)$key['lang_source'], ($pcType ?: $transAllType));
//                                        }
//                                    }
//                                }
//                            }
//                        }
                        EtsTransLog::logTranslate('pc', true, $timStartTrans, $langSource, $idLang, null, self::getLogIdTrans(array_column($listKey, 'key')),null, null);
                    }
                    else{
                        EtsTransLog::logTranslate('pc', false, $timStartTrans, $langSource, $idLang, null, self::getLogIdTrans(array_column($listKey, 'key')), null, isset($resultTrans['message']) ? $resultTrans['message'] : null);
                        $resultTrans['stop'] = true;
                        return $resultTrans;
                    }
                }
            }
        }
        $result['nb_money'] = $api->getTotalFeeTranslate($result['nb_char']);
        if(!$cmtLang || (isset($formData['page_id']) && $formData['page_id'])){
            $result['stop'] = 1;
            if(!$analysis){
                $config = EtsTransConfig::getInstance();
                $config->deletePauseData('pc', $pcType);
            }
            if($analysis)
                $result['total_item'] = self::getTotalProductComments($pcType);
        }
        if(!$result['nb_char']){
            $result['nb_text'] = 0;
        }

        return $result;
    }

    public static function updateTransModulePcItem($colName, $value, $id, $idLang, $pcType)
    {
        if($pcType == 'review' || $pcType == 'question') {
        	$sql = "UPDATE `"._DB_PREFIX_."ets_rv_product_comment_lang` SET ".pSQL($colName)."='".pSQL($value, true)."' WHERE id_lang=".(int)$idLang." AND id_ets_rv_product_comment=".(int)$id;
            return Db::getInstance()->execute($sql);
        }

        if($pcType == 'comment' || $pcType == 'question_comment' || $pcType == 'answer'){
            return Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_rv_comment_lang` SET ".pSQL($colName)."='".pSQL($value, true)."' WHERE id_lang=".(int)$idLang." AND id_ets_rv_comment=".(int)$id);
        }

        if($pcType == 'reply' || $pcType == 'answer_comment'){
            return Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_rv_reply_comment_lang` SET ".pSQL($colName)."='".pSQL($value, true)."' WHERE id_lang=".(int)$idLang." AND id_ets_rv_reply_comment=".(int)$id);
        }
        return false;
    }

    public static function translateViewModulePc($formData, $pcType)
    {
        $reviewTypes = array('review', 'reply', 'comment');
        $questionTypes = array('question', 'question_comment', 'answer', 'answer_comment');
        $formDataCustom = $formData;
	    $result = [];
        if (in_array($pcType, $reviewTypes)){
	        $result = self::translateAllModulePc($formDataCustom, false, 'review', true);
            $formDataCustom['page_product_comment_id'] = $formData['page_id'];
            $formDataCustom['page_id'] = 0;
            self::translateAllModulePc($formDataCustom, false, 'comment', true);
            self::translateAllModulePc($formDataCustom, false, 'reply', true);
        }
        else if (in_array($pcType, $questionTypes)){
	        $result = self::translateAllModulePc($formDataCustom, false, 'question', true);
            $formDataCustom['page_id'] = 0;
	        $formDataCustom['page_product_comment_id'] = $formData['page_id'];
	        $formDataCustom['page_id'] = 0;
            self::translateAllModulePc($formDataCustom, false, 'answer', true);
            self::translateAllModulePc($formDataCustom, false, 'question_comment', true);
            self::translateAllModulePc($formDataCustom, false, 'answer_comment', true);
        }
        return $result;
    }

    public static function analysisModulePc($formData, $pcType = '')
    {
        if (!isset($formData['trans_source']) || !isset($formData['trans_target']) || !isset($formData['trans_option'])) {
            return false;
        }
        $pcType = ($pcType && Validate::isCleanHtml($pcType)) ? $pcType : '';
        return self::translateAllModulePc($formData, true, $pcType);
    }

    public static function getTotalProductComments($pcType = null, $idShop = null)
    {
        if (!$idShop) {
            $idShop = Context::getContext()->shop->id;
        }
        if ($pcType == 'review' || $pcType == 'question') {
            return (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_product_comment` pc 
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
        }

        if ($pcType == 'comment' || $pcType == 'question_comment' || $pcType == 'answer') {
            return (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_comment` c 
                LEFT JOIN `"._DB_PREFIX_."ets_rv_product_comment` pc ON c.id_ets_rv_product_comment=pc.id_ets_rv_product_comment
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
        }

        if ($pcType == 'reply' || $pcType == 'answer_comment') {
            return (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_reply_comment` rc
                LEFT JOIN `"._DB_PREFIX_."ets_rv_comment` c ON rc.id_ets_rv_comment=c.id_ets_rv_comment 
                LEFT JOIN `"._DB_PREFIX_."ets_rv_product_comment` pc ON c.id_ets_rv_product_comment=pc.id_ets_rv_product_comment 
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
        }
        $productComment =  (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_product_comment` pc 
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
         $comments = (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_comment` c 
                LEFT JOIN `"._DB_PREFIX_."ets_rv_product_comment` pc ON c.id_ets_rv_product_comment=pc.id_ets_rv_product_comment
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
         $reply = (int)Db::getInstance()->getValue("
                SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_rv_reply_comment` rc
                LEFT JOIN `"._DB_PREFIX_."ets_rv_comment` c ON rc.id_ets_rv_comment=c.id_ets_rv_comment 
                LEFT JOIN `"._DB_PREFIX_."ets_rv_product_comment` pc ON c.id_ets_rv_product_comment=pc.id_ets_rv_product_comment
                LEFT JOIN `" . _DB_PREFIX_ . "product_shop` ps ON pc.id_product = ps.id_product WHERE ps.id_shop=" . (int)$idShop);
        return $productComment + $comments + $reply;
    }


    public static  function updateTextModule($dataTrans, $moduleName, $fileName)
    {
        if(!is_array($dataTrans)){
            return false;
        }

        foreach ($dataTrans as $idLang=> $textTrans){
            $target = Language::getIsoById($idLang);
            if (!@file_exists(($trans_file = _PS_MODULE_DIR_.$moduleName . '/translations/' . $target . '.php'))) {
                $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
                @file_put_contents($trans_file, $content);
            }
            if (!is_writable($trans_file)) {
                return false;
            }
            $content = Tools::file_get_contents($trans_file);
            if (!$content || strpos($content, '<?php') === false) {
                $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
                @file_put_contents($trans_file, $content);
            }
            foreach ($textTrans as $textOrigin=>$translated){
                $text = preg_replace("/\\\*'/", "\'", $textOrigin);
                if(!$text || !$translated){
                    continue;
                }
                $strMd5 = md5($text);
                $keyMd5 = '<{' . $moduleName . '}prestashop>' . $fileName . '_' . $strMd5;
                preg_match('/\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]/', $content, $matches);
                if ($matches) {
                    $content = preg_replace('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', '${1}' . pSQL($translated) . '${3}', $content);
                } else{
                    $content .= "\n\$_MODULE['" . $keyMd5 . "']='" . pSQL($translated) . "';";
                }
            }
            @file_put_contents($trans_file, $content);
        }

    }

    public static function updateTransMegamenuItem($id, $menuType, $colData, $transData)
    {
        $tableName = '';
        $idItem = '';
        switch ($menuType){
            case 'menu':
                $tableName = 'ets_mm_menu_lang';
                $idItem = 'id_menu';
                break;
            case 'tab':
                $tableName = 'ets_mm_tab_lang';
                $idItem = 'id_tab';
                break;
            case 'block':
                $tableName = 'ets_mm_block_lang';
                $idItem = 'id_block';
                break;
        }
        if(!$tableName || !$idItem || !$id){
            return false;
        }
        foreach ($transData as $idLang => $itemTrans){
            $dataUpdate = array();
            foreach ($itemTrans as $key=>$text){
                if(isset($colData[$key])){
                    $dataUpdate[$colData[$key]] = pSQL($text);
                }
            }
            if($dataUpdate)
            	Db::getInstance()->update(pSQL($tableName), $dataUpdate, pSQL($idItem) . '=' . (int)$id . ' AND `id_lang`=' .(int)$idLang);
        }
        return true;
    }

    public static function updateTransBlogItem($id, $blogType, $colData, $transData, $extraOptions = array())
    {
        $tableName = '';
        $idItem = '';
        switch ($blogType){
            case 'post':
                $tableName = 'ybc_blog_post_lang';
                $idItem = 'id_post';
                break;
            case 'category':
                $tableName = 'ybc_blog_category_lang';
                $idItem = 'id_category';
                break;
        }

        if(!$tableName || !$idItem || !$id){
            return false;
        }
	    foreach ($transData as $idLang => $itemTrans){
		    $dataUpdate = array();
		    foreach ($itemTrans as $key=>$text){
			    if(isset($colData[$key])){
				    $dataUpdate[$colData[$key]] = pSQL($text);
				    if (isset($extraOptions['auto_generate_link_rewrite']) && $extraOptions['auto_generate_link_rewrite'] && $colData[$key] == 'title'){
					    $dataUpdate['url_alias'] = pSQL(Tools::str2url($text));
				    }
			    }
		    }
		    if($dataUpdate) {
			    Db::getInstance()->update($tableName, $dataUpdate, pSQL($idItem)."=".(int)$id." AND `id_lang`=".(int)$idLang);
		    }

	    }
        return true;
    }

    public static function updateTransPCItem($id, $pcType, $colData, $transData)
    {
        if(!$id){
            return false;
        }
        foreach ($transData as $idLang => $itemTrans){
            $dataUpdate = array();
            foreach ($itemTrans as $key=>$text){
                if(isset($colData[$key])){
                    $dataUpdate[$colData[$key]] = pSQL($text);
                }
            }
            if($dataUpdate){
                if($pcType == 'review' || $pcType == 'question'){

                	Db::getInstance()->update('ets_rv_product_comment_lang', $dataUpdate, "id_ets_rv_product_comment=" . (int)$id . " AND `id_lang`=" . (int)$idLang);
                }
                elseif($pcType == 'comment' || $pcType == 'question_comment' || $pcType == 'answer'){
	                Db::getInstance()->update('ets_rv_comment_lang', $dataUpdate, "id_ets_rv_comment=" . (int)$id . " AND `id_lang`=" . (int)$idLang);
                }
                elseif($pcType == 'reply' || $pcType == 'answer_comment'){
	                Db::getInstance()->update('ets_rv_reply_comment_lang', $dataUpdate, "id_ets_rv_reply_comment=" . (int)$id . " AND `id_lang`=" . (int)$idLang);
                }
            }
         }
        return true;
    }

    public static function getTextLog($textSource)
    {
        $textLog = "";
        foreach ($textSource as $text){
            if(Tools::strlen($textLog) >= 50){
                break;
            }
            $textLog .= $text.";";
        }
        return Tools::substr(strip_tags(rtrim($textLog, ';')), 0, 50);
    }

    public static function getLogIdTrans($listData, $delimiter = '|')
    {
        $ids = array();
        foreach ($listData as $keyData){
            $id = explode($delimiter, $keyData)[0];
            if(!in_array($id, $ids))
                $ids[] = $id;
        }
        return implode(',', $ids);
    }

    public static function updateContentProductTab($id_tab, $id_product, $content, $col = 'content', $id_lang = null, $textTrans = null)
    {
        if ($id_lang){
            return Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_ept_product` SET `" . pSQL($col) . "`='" . pSQL($textTrans) . "' WHERE id_ets_ept_tab=" . (int)$id_tab . " AND id_product=" . (int)$id_product . " AND id_lang=" . (int)$id_lang);
        }
        foreach ($content as $idLang => $textArr){
            if(!is_array($textArr)){
                continue;
            }
            foreach ($textArr as $text)
                if($text && Validate::isCleanHtml($text)) {
                    Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_ept_product` SET `" . pSQL($col) . "`='" . pSQL($text) . "' WHERE id_ets_ept_tab=" . (int)$id_tab . " AND id_product=" . (int)$id_product . " AND id_lang=" . (int)$idLang);
                }
        }
    }

    public static function translateLivechatTicket($idTicket, $sourceLang, $targetLang, $text)
    {
        $langTarget = $targetLang;
        if (!$langTarget){
            $idLangTarget = self::getLcTicketCustomerLanguage($idTicket);
            if ($idLangTarget && ($lang = Language::getLanguage($idLangTarget)))
                $langTarget = $lang['iso_code'];

        }

        if ($langTarget) {
            $results = EtsTransApi::getInstance()->translate($sourceLang ?: null, $langTarget, [$text]);
            $itemLangSource = array();
            if (isset($results['detectedSourceLanguage']) && $results['detectedSourceLanguage']){
                $itemLangSource = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `language_code` LIKE '".pSQL($results['detectedSourceLanguage'][0])."-%'");
            }
            elseif ($sourceLang)
                $itemLangSource = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `iso_code` = '".pSQL($sourceLang)."'");

            $itemTargetLang = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `iso_code` ='".pSQL($langTarget)."'");
            $results['lang_source'] = $itemLangSource;
            $results['lang_target'] = $itemTargetLang;
            return $results;
        }
        else{
            return false;
        }
    }

    public static function translateHelpdeskTicket($idTicket, $sourceLang, $targetLang, $text)
    {
        $langTarget = $targetLang;
        if (!$langTarget){
            $idLangTarget = self::getHdTicketCustomerLanguage($idTicket);
            if ($idLangTarget && ($lang = Language::getLanguage($idLangTarget)))
                $langTarget = $lang['iso_code'];

        }

        if ($langTarget) {
            $results = EtsTransApi::getInstance()->translate($sourceLang ?: null, $langTarget, [$text]);
            $itemLangSource = array();
            if (isset($results['detectedSourceLanguage']) && $results['detectedSourceLanguage']){
                $itemLangSource = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `language_code` LIKE '".pSQL($results['detectedSourceLanguage'][0])."-%'");
            }
            elseif ($sourceLang)
                $itemLangSource = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `iso_code` = '".pSQL($sourceLang)."'");

            $itemTargetLang = Db::getInstance()->getRow("SELECT `name`, `iso_code` FROM `"._DB_PREFIX_."lang` WHERE `iso_code` ='".pSQL($langTarget)."'");
            $results['lang_source'] = $itemLangSource;
            $results['lang_target'] = $itemTargetLang;
            return $results;
        }
        else{
            return false;
        }
    }

    public static function getLcTicketCustomerLanguage($idTicket)
    {
        $ticket = Db::getInstance()->getRow("SELECT id_lang, id_customer FROM `"._DB_PREFIX_."ets_livechat_ticket_form_message` WHERE id_message=".(int)$idTicket);
        if ($ticket){
            $idLangTarget = 0;
            if ((int)$ticket['id_lang']){
                $idLangTarget = (int)$ticket['id_lang'];

            }
            elseif($idCustomer = (int)$ticket['id_customer']){
                $customer = new Customer($idCustomer);
                $idLangTarget = $customer->id_lang;
            }

            return $idLangTarget;
        }

        return 0;

    }
    public static function getHdTicketCustomerLanguage($idTicket)
    {
        $ticket = Db::getInstance()->getRow("SELECT id_lang, id_customer FROM `"._DB_PREFIX_."ets_hd_ticket` WHERE id_ets_hd_ticket=".(int)$idTicket);
        if ($ticket){
            $idLangTarget = 0;
            if ((int)$ticket['id_lang']){
                $idLangTarget = (int)$ticket['id_lang'];

            }
            elseif($idCustomer = (int)$ticket['id_customer']){
                $customer = new Customer($idCustomer);
                $idLangTarget = $customer->id_lang;
            }

            return $idLangTarget;
        }

        return 0;

    }
}