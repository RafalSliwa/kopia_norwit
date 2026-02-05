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

class EtsTransCore
{

    protected $langSource;
    protected $langTarget;
    protected $transOption;
    protected $type = null;
    protected $selectedName;
    protected $isDetectLanguage = false;
    protected $isOneClick = false;
    public $adminFD = '';

    /** @var Ets_Translate */
    protected $module;

	public static $_STOP_BY = [
		'merchant', 'api'
	];

	public static $ignorefile = array(
		'.',
		'..',
		'.git',
		'.htaccess',
		'.git',
		'.idea',
		'README.md',
		'index.php'
	);

    public function __construct($type = 'module')
    {
        $this->type = $type;
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

    public function setLangSource($langSource)
    {
        $this->langSource = $langSource;
    }
    public function setIsDetectLanguage($isDetectLanguage)
    {
        $this->isDetectLanguage = $isDetectLanguage;
    }

    public function setLangTarget($langTarget)
    {
        $this->langTarget = $langTarget;
    }

    public function setTransOption($transOption)
    {
        $this->transOption = $transOption;
    }

    public function setAdminFD($adminFD)
    {
        $this->adminFD = $adminFD;
    }
    public function setIsOneClick($isOneClick)
    {
        $this->isOneClick = $isOneClick;
    }

    public function listFiles($dir, $list = array(), $file_ext = 'php')
    {
        $dir = rtrim($dir, '/') . DIRECTORY_SEPARATOR;
        if(!@is_dir(rtrim($dir, '/'))){
            return array();
        }
        $to_parse = scandir($dir);
        // copied (and kind of) adapted from AdminImages.php
        foreach ($to_parse as $file) {
            if (in_array($file, self::$ignorefile)) {
                continue;
            }
            if (preg_match('#' . preg_quote($file_ext, '#') . '$#i', $file)) {
                $list[] = $dir . $file;
            } elseif (is_dir($dir . $file)) {
                $list = $this->listFiles($dir . $file, $list, $file_ext);
            }
        }
        return $list;
    }

    public function getTextTranslateFormFilePath($filePath, $fileType = null, $isSf = false)
    {
        if (!$fileType) {
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        }
        if($fileType == 'xlf'){
            return $this->getTextSourceInXlfFile($filePath);
        }
        switch ($fileType) {
            case 'php':
                $regex = array(
                    'classic' => '/->l\((\')' . _PS_TRANS_PATTERN_ . '\'(, ?\'(.+)\')?(, ?(.+))?\)/U',
                    'sf' => '/\->trans\(\s*(\')'._PS_TRANS_PATTERN_.'\'(\s*, ?\'(.+)\')?(\s*, ?(.+))?[\)|\]]{1}(\s*, ?(.+))?\)/sU',
                );
                break;
            case 'twig':
                $regex = array(
                    'sf' => '/\{\{\s*([\'\"])'._PS_TRANS_PATTERN_.'\1\|trans(?:\((?:.+)?\s*\1(.+)?\1\)|\|raw)?\s*\}\}/sU',
                );
                break;
            case 'tpl':
                if ($this->type == 'theme' || $this->type =='back') {
                    $regex = array(
                        'sf' => '/\{l\s*s=([\'\"])' . _PS_TRANS_PATTERN_ . '\1.*\s+d=\1([\w\.]+)\1.*\}/sU',
                    );
                }
                else if($this->type == 'module' || $this->type == 'sfmodule') {
                    $regex = array(
                        'classic' => '/\{l\s*s=([\'\"])' . _PS_TRANS_PATTERN_ . '\1.*\s+mod=\1' . $this->selectedName . '\1.*\}/sU',
                        'sf' => '/\{l\s*s=([\'\"])' . _PS_TRANS_PATTERN_ . '\1.*\s+d=\1([\w\.]+)\1.*\}/sU',
                    );
                }
                break;
        }

        if (!isset($regex)) {
            return array();
        }
        if (!@file_exists($filePath)) {
            return array();
        }
        $content = Tools::file_get_contents($filePath);
        $strings = array();
        foreach ($regex as $key=>$reg){
            $n = preg_match_all($reg, $content, $matches);
            for ($i = 0; $i < $n; $i++) {
                $quote = isset($matches[1][$i]) ? $matches[1][$i] : '';
                $string = isset($matches[2][$i]) ? $matches[2][$i] : '';
                $domain = isset($matches[3][$i]) ? $matches[3][$i] : '';
                if ($quote === '"') {
                    // Escape single quotes because the core will do it when looking for the translation of this string
                    $string = str_replace('\'', '\\\'', $string);
                    // Unescape double quotes
                    $string = preg_replace('/\\\\+"/', '"', $string);
                }
                if ($this->type == 'theme' || $isSf) {
                    if($this->type == 'sfmodule' && $key == 'classic'){
                        $domain = str_replace('_', '', $this->selectedName);
                        $domain = Tools::ucfirst(Tools::strtolower($domain));
                        $domain = 'Modules'.$domain.$domain;
                    }
                    elseif(($this->type == 'sfmodule' || $this->type == 'back') && $key == 'sf' && $fileType == 'php'){
                        $domain = isset($matches[8][$i]) ? $matches[8][$i] : '';
                        if($quote == '"'){
                            $domain = str_replace('"', '', $domain);
                        }
                        else{
                            $domain = str_replace("'", '', $domain);
                        }
                    }
                    if(!trim($domain) || !preg_match('/^[a-zA-Z\.]+$/', trim($domain))){
                        continue;
                    }
                    if ($quote == "'"){
                        $quotePos = strpos($string,"'");
                        if($quotePos !== false && $quotePos == 0){
                            continue;
                        }
                        if($quotePos !== false && Tools::substr($string, $quotePos-1, 1) !== "\\"){
                            continue;
                        }
                    }
                    elseif($quote == '"'){
                        $quotePos = strpos($string,'"');
                        if($quotePos !== false && $quotePos == 0){
                            continue;
                        }
                        if($quotePos !== false && Tools::substr($string, $quotePos-1, 1) !== "\\"){
                            continue;
                        }
                    }
                    $strings[] = array(
                        'text' => $string,
                        'domain' => trim($domain)
                    );
                } else
                    $strings[] = $string;
            }
        }

        return $strings;
    }

    public function formatTextArray($textTrans)
    {
        $result = array();
        foreach ($textTrans as $item) {
            $exists = false;
            foreach ($result as $x){
                if($x['text'] == $item['text'] && $x['domain'] == $item['domain'] && $x['path'] == $item['path']){
                    $exists = true;
                    break;
                }
            }
            if(!$exists){
                $result[] = $item;
            }
        }

        usort($result, array($this, 'cmp'));
        return $result;
    }

    protected function cmp($a, $b)
    {
        return strcmp($a["domain"], $b["domain"]);
    }

    public function removeDuplicateItem($textTrans, $renewKey = true)
    {
        $textTrans = array_unique($textTrans);
        if (!$renewKey) {
            return $textTrans;
        }
        $result = array();
        foreach ($textTrans as $text) {
            $result[] = $text;
        }
        return $result;
    }

    public function modifyTextTranslating($textTrans)
    {
        foreach ($textTrans as &$text) {
            if ($this->type == 'theme') {
                $text = preg_replace('/(\%\w+\%)/', '<'.'span class="'.'notranslate"'.'>'.' $1 '.'<'.'/'.'span'.'>', $text);
            } else
                $text = preg_replace('/(\[\w+\])/', '<'.'span class="'.'notranslate"'.'>'.' $1 '.'<'.'/'.'span'.'>', $text);
        }
        return $textTrans;
    }

    public function modifyTextTranslated($textTranslated, $textDefaultArray = array())
    {
        foreach ($textTranslated as $k => $text) {
            if ($this->type == 'theme') {
                $textTranslated[$k] = preg_replace('/(<'.'span class="notranslate"'.'>)(\%\w+\%)(<\/span>)/', '$2', $text);
            } else
                $textTranslated[$k] = preg_replace('/(<'.'span class="notranslate"'.'>)(\[\w+\])(<\/span>)/', '$2', $text);
            if ($textDefaultArray && isset($textDefaultArray[$k])) {
                $textDefaultArray[$k]['text_translated'] = $textTranslated[$k];
            }
        }
        if ($textDefaultArray) {
            return $textDefaultArray;
        }
        return $textTranslated;
    }
    public function deleteCacheItem($id)
    {
        $cache = new EtsTransCache((int)$id);
        if ($cache->id_ets_trans_cache) {
            return $cache->delete();
        }
        return false;
    }

    public function filterTextTranslate($textTransArray, $target)
    {
        $textModified = array();
        foreach ($textTransArray as $item) {
            $textModified[$item['domain']][] = $item;
        }
        
        $result = array();
        foreach ($textModified as $domain => $item) {
            if($this->isClassicTrans($domain)){
                $itemChecked = $this->filterTextTranslateItemClassic($item, $target);

            }
            else
                $itemChecked = $this->filterTextTranslateItem($domain, $item, $target);
            if (!$itemChecked) {
                continue;
            }
            foreach ($itemChecked as $i) {
                $result[] = $i;
            }
        }
        return $result;
    }

    public function isClassicTrans($domain)
    {
        $domain = str_replace('.', '', $domain);
        $parts = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);
        if($this->type == 'sfmodule' && $this->selectedName && $parts && is_array($parts) && count($parts) == 3){
            $moduleName = Tools::ucfirst(str_replace('_', '', $this->selectedName));
            if($parts[1] == $parts[2] && $moduleName == $parts[1]){
                return true;
            }
        }

        return false;
    }

    public function filterTextTranslateItem($domain, $items, $isoCode)
    {
        $domain = str_replace('.', '', $domain);
        $idLang= Language::getIdByIso($isoCode);
        $locale = Language::getLocaleByIso($isoCode);
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $xmlStr = Tools::file_get_contents(_PS_ROOT_DIR_ . ( $this->module->isGte816 ? '/translations/' : '/app/Resources/translations/') . $locale . '/' . $domain . '.' . $locale . '.xlf');
        if(!$xmlStr){
            return $items;
        }
        $dom->loadXML($xmlStr);
        $nodeFiles = $dom->getElementsByTagName('file');
        foreach ($items as $k => $item) {
            $path = str_replace(_PS_ROOT_DIR_ . '/', '', $item['path']);
            $path = str_replace('\\', '/', $path);

            $textTranslated = '';
            foreach ($nodeFiles as $nodeFile) {
                if ((string)$nodeFile->getAttribute('original') == $path) {
                    $transUnits = $nodeFile->getElementsByTagName('trans-unit');
                    foreach ($transUnits as $transUnit) {
                        if ((string)$transUnit->getAttribute('id') == md5($item['text'])) {
                            $textTranslated = $transUnit->getElementsByTagName('target')->item(0)->nodeValue;
                            break;
                        }
                    }
                    break;
                }
            }
            $textDb = $this->getTranslationOnDb(str_replace("\'", "'", $item['text']), $idLang,$domain);

            if($textDb){
                $textTranslated = $textDb;
            }
            if ($textTranslated && !$textDb){
                if($this->type == 'theme'){
                    Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."translation` (`id_lang`, `key`, `translation`, `domain`, `theme`) VALUES(".(int)$idLang.", '".pSQL(str_replace("\'", "'", $item['text']))."', '".pSQL($textTranslated)."', '".pSQL($domain)."', '".pSQL($this->selectedName)."')");
                }
                else{
                    Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."translation` (`id_lang`, `key`, `translation`, `domain`) VALUES(".(int)$idLang.", '".pSQL(str_replace("\'", "'", $item['text']))."', '".pSQL($textTranslated)."', '".pSQL($domain)."')");
                }
            }
            if (!self::checkTransOption($this->transOption, $item['text'], $textTranslated)) {
                unset($items[$k]);
            }
        }

        return $items;
    }

    public function filterTextTranslateItemClassic($items, $isoCode)
    {
        foreach ($items as $k=>$item){
            $filePath = _PS_MODULE_DIR_.$this->selectedName.'/translations/'.$isoCode.'.php';
            if(!@file_exists($filePath)){
                return array();
            }
            $content = Tools::file_get_contents($filePath);
            $text = preg_replace("/\\\*'/", "\'", $item['text']);
            $strMd5 = md5($text);
            $ext = pathinfo($item['path'], PATHINFO_EXTENSION);
            $fileName = basename($item['path'], '.'.$ext);
            $keyMd5 = '<{' . $this->selectedName . '}prestashop>' .$fileName.'_'. $strMd5;
            $textTranslated = null;
            preg_match('/\$_MODULE\[\''.preg_quote($keyMd5).'\'\]\s*=\s*\'(.*)\';/',$content, $matches);
            if($matches){
                $textTranslated = isset($matches[1]) ? $matches[1] : '';
            }
            if (!self::checkTransOption($this->transOption, $item['text'], $textTranslated)) {
                unset($items[$k]);
            }
        }

        return $items;
    }

    public static function checkTransOption($transOption, $textDefault, $textTranslated)
    {
        switch ($transOption) {
            case 'both':

                if (!$textTranslated || $textTranslated == $textDefault) {
                    return true;
                }
                break;
            case 'only_empty':
                if (!$textTranslated) {
                    return true;
                }
                break;
            case 'same_source':
                if ($textTranslated == $textDefault) {
                    return true;
                }
                break;
            default:
                return true;
        }
        return false;
    }

    public function executeTextTranslate($textTransArray, $source, $target)
    {
        $textModified = array();
        foreach ($textTransArray as $item) {
            $textModified[$item['domain']][] = $item;
        }

        foreach ($textModified as $domain => $item) {
            $this->saveTranslatedToXlfFile($domain, $item, $source, $target);
            foreach ($item as $trans){
                $idLang=Language::getIdByIso($target);
                $this->translateOnDb($trans['text'], $trans['text_translated'], $idLang, $domain);
            }
            if($this->isClassicTrans($domain)){
                $this->saveToClassicTransFile($item, $target);
            }
        }
    }

    public function saveToClassicTransFile($items, $isoCode)
    {
        $moduleDir = _PS_MODULE_DIR_.$this->selectedName;
        if(!@is_dir($moduleDir . '/translations')){
            @mkdir($moduleDir . '/translations');
        }

        if (!@file_exists(($trans_file = $moduleDir . '/translations/' . $isoCode . '.php'))) {
            $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
            @file_put_contents($trans_file, $content);
        }
        if (!is_writable($trans_file)) {
            return false;
        }
        $content = Tools::file_get_contents($trans_file);
        if(!$content || strpos($content,'<?php') === false){
            $content = "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
            @file_put_contents($trans_file, $content);
        }
        foreach ($items as $item){
            if(!isset($item['text_translated']) || !$item['text_translated']){
                continue;
            }
            $text = preg_replace("/\\\*'/", "\'", $item['text']);
            $strMd5 = md5($text);
            $ext = pathinfo($item['path'], PATHINFO_EXTENSION);
            $fileName = basename($item['path'], '.'.$ext);
            $keyMd5 = '<{' . $this->selectedName . '}prestashop>' .$fileName.'_'. $strMd5;
            preg_match('/\$_MODULE\[\''.preg_quote($keyMd5).'\'\]/',$content, $matches);
            if($matches){
                $content = preg_replace('/(\$_MODULE\[\''.preg_quote($keyMd5).'\'\]\s*=\s*\')(.*)(\';)/', '${1}'.pSQL($item['text_translated']).'${3}', $content);
            }
            else
                $content .= "\n\$_MODULE['".$keyMd5."']='".pSQL($item['text_translated'])."';";
        }
        @file_put_contents($trans_file, $content);
        return true;
    }

    public function saveTranslatedToXlfFile($domain, $textTransArray, $source, $target)
    {
        $textTransData = array();
        foreach ($textTransArray as $item) {
            $path = str_replace(_PS_ROOT_DIR_ . '/', '', $item['path']);
            $path = str_replace('\\', '/', $path);
            $textTransData[$path][] = $item;
        }
        $locale = Language::getLocaleByIso($target);
        $fileSysTranslatedDir = _PS_ROOT_DIR_ . ($this->module->isGte816 ? '/translations/' : '/app/Resources/translations/') . $locale;
        if (!@is_dir($fileSysTranslatedDir)) {
            mkdir($fileSysTranslatedDir, 0755, true);
        }
        $fileSysTransName = str_replace('.', '', $domain);
        $fileSysTranslatedPath = $fileSysTranslatedDir . '/' . $fileSysTransName . '.' . $locale . '.xlf';
        if (@!file_exists($fileSysTranslatedPath)) {
            @file_put_contents($fileSysTranslatedPath, '<'.'?'.'xml version="1.0" encoding="UTF-8"?>' . "\n" . '<'.'xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2"'.'>'.'<'.'/xliff'.'>');
        }
        $xmlStr = Tools::file_get_contents($fileSysTranslatedPath);
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->loadXML($xmlStr);
        $xliff = $dom->getElementsByTagName('xliff')->item(0);
        $nodeFiles = $dom->getElementsByTagName('file');
        try{
            foreach ($textTransData as $path => $itemPath) {
                $hasFile = false;
                foreach ($nodeFiles as $nodeFile) {
                    foreach ($itemPath as $itemTrans){
                        if ($nodeFile->getAttribute('original') == $path) {
                            $hasFile = true;
                            $transUnits = $nodeFile->getElementsByTagName('trans-unit');
                            $hasTarget = false;
                            foreach ($transUnits as $transUnit) {
                                if ($transUnit->getAttribute('id') == md5($itemTrans['text'])) {
                                    $transUnit->getElementsByTagName('target')->item(0)->nodeValue = htmlspecialchars($itemTrans['text_translated']);
                                    
                                    $hasTarget = true;
                                    break;
                                }
                            }

                            if (!$hasTarget) {
                                $newTransUnit = $this->addTransUnit($dom, $itemTrans['text'], $itemTrans['text_translated']);
                                $_body = $nodeFile->getElementsByTagName('body')->item(0);
                                if (!$_body) {
                                    $_body = $dom->createElement('body');
                                    $_body->appendChild($newTransUnit);
                                    $nodeFile->appendChild($_body);
                                } else {
                                    $_body->appendChild($newTransUnit);
                                }
                            }
                        }
                    }
                }
                if (!$hasFile) {
                    $newFileNode = $this->addFile($dom, $path, $source, $target);
                    $body = $dom->createElement('body');
                    foreach ($itemPath as $itemTrans){
                        $body->appendChild($this->addTransUnit($dom, $itemTrans['text'], $itemTrans['text_translated']));
                        $newFileNode->appendChild($body);
                        $xliff->appendChild($newFileNode);
                    }
                }
            }
            $dom->save($fileSysTranslatedPath);
        }
        catch (Exception $ex){
            if($ex){
                return false;
            }
        }

    }

    protected function addTransUnit(&$dom, $source, $target, $note = '', $approved = 'yes')
    {
        $id = md5($source);
        $translation = $dom->createElement('trans-unit');
        $translation->setAttribute('id', $id);
        $translation->setAttribute('approved', $approved);

        // Does the target contain characters requiring a CDATA section?
        $source_value = 1 === preg_match('/[&<>]/', $source) ? $dom->createCDATASection($source) : $dom->createTextNode($source);
        $target_value = 1 === preg_match('/[&<>]/', $target) ? $dom->createCDATASection($target) : $dom->createTextNode($target);
        $note_value = 1 === preg_match('/[&<>]/', $note) ? $dom->createCDATASection($note) : $dom->createTextNode($note);

        $s = $dom->createElement('source');
        $s->appendChild($source_value);
        $translation->appendChild($s);
        // Skip metadata
        $z = $dom->createElement('target');
        $z->appendChild($target_value);
        $translation->appendChild($z);

        $n = $dom->createElement('note');
        $n->appendChild($note_value);
        $translation->appendChild($n);
        return $translation;
    }

    protected function addFile(&$dom, $filename, $sourceLanguage, $targetLanguage)
    {
        $xliffFile = $dom->createElement('file');
        $xliffFile->setAttribute('original', $filename);
        $xliffFile->setAttribute('source-language', $sourceLanguage ?: 'en');
        $xliffFile->setAttribute('target-language', $targetLanguage);
        $xliffFile->setAttribute('datatype', 'plaintext');
        return $xliffFile;
    }

    public function getTotalCharTrans($textTrans){
        if(!is_array($textTrans)){
            $textTrans = array($textTrans);
        }
        $count = 0;
        foreach ($textTrans as $text){
            $count += Tools::strlen($text);
        }
        return $count;
    }

    public function getTotalRemainFile($type, $name)
    {
        $idShop = Context::getContext()->shop->id;
        return (int)Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."ets_trans_cache` WHERE `cache_type`='".pSQL($type)."' AND `name`='".pSQL($name)."' AND id_shop=".(int)$idShop);
    }

    public function translateOnDb($text,$translated, $idLang, $domain)
    {
        $domain = str_replace('.', '', $domain);
        $existsSql = "SELECT `id_translation` FROM `"._DB_PREFIX_."translation` WHERE `key`='".pSQL($text)."' AND `domain`='".pSQL($domain)."' AND `id_lang`='".(int)$idLang."'";
        if($this->type == 'theme'){
            $existsSql .= " AND `theme`='".pSQL($this->selectedName)."'";
        }
        $idTranslation = (int)Db::getInstance()->getValue($existsSql);
        if($idTranslation){
            Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."translation` SET `translation`='".pSQL($translated)."' WHERE `id_translation`=". $idTranslation);
        }
        else{
            if($this->type == 'theme'){
                Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."translation` (id_lang, `key`, `translation`, `domain`, `theme`) VALUES(".(int)$idLang.",'".pSQL($text)."','".pSQL($translated)."','".pSQL($domain)."','".pSQL($this->selectedName)."')");
            }
            else{
                Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."translation` (id_lang, `key`, `translation`, `domain`) VALUES(".(int)$idLang.",'".pSQL($text)."','".pSQL($translated)."','".pSQL($domain)."')");
            }
        }
        return true;
    }

    public function getTranslationOnDb($text, $idLang, $domain)
    {

        $existsSql = "SELECT `translation` FROM `"._DB_PREFIX_."translation` WHERE `key`='".pSQL($text)."' AND `domain`='".pSQL($domain)."' AND `id_lang`='".(int)$idLang."'";
        if($this->type == 'theme'){
            $existsSql .= " AND `theme`='".pSQL($this->selectedName)."'";
        }
        return (string)Db::getInstance()->getValue($existsSql);
    }

    public function getTextSourceInXlfFile($filePath)
    {
        if(!@is_file($filePath)){
            return array();
        }
        $fileName = basename($filePath, '.xlf');
        $dom = new DOMDocument();
        $xmlStr = Tools::file_get_contents($filePath);
        $dom->loadXML($xmlStr);
        $nodeFiles = $dom->getElementsByTagName('file');
        $data = array();
        if(!$nodeFiles){
            return $data;
        }
        foreach ($nodeFiles as $nodeFile){
            $original = $nodeFile->getAttribute('original');
            $transUnits = $nodeFile->getElementsByTagName('trans-unit');
            foreach ($transUnits as $transUnit) {
                $data[] = array(
                    'text' => $transUnit->getElementsByTagName('source')->item(0)->nodeValue,
                    'path' => $original,
                    'domain' => $fileName
                );
            }
        }
        return $data;
    }

    public function updateStatusCacheItem($idCache, $status)
    {
        return Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."ets_trans_cache` SET `status`=".(int)$status." WHERE id_ets_trans_cache=".(int)$idCache);
    }

    public static function getAllThemes()
    {
        $lists = scandir(_PS_ROOT_DIR_.'/themes');
        $themes = array();
        foreach ($lists as $item){
            if(@is_file(_PS_ROOT_DIR_.'/themes/'.$item) || $item =='_libraries' || $item == '.' || $item == '..'){
                continue;
            }
            $themes[] = array(
                'title' => $item,
                'path' => _PS_ROOT_DIR_.'/themes/'.$item,
            );
        }
        return $themes;
    }

    public static function getAllModules($active = false)
    {
        $filterWhere = "";
        if($active !== false){
            $filterWhere .= " AND active=".(int)$active;
        }
        $lists = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."module` WHERE 1". pSQL($filterWhere));
        $modules = array();
        foreach ($lists as $item) {
            $modules[] = array(
                'title' => $item['name'],
                'path' => _PS_MODULE_DIR_ . $item['name'],
            );
        }
        return $modules;
    }

    public static function getAllHooksContentCE() {
		return Db::getInstance()->executeS("SELECT hook FROM `"._DB_PREFIX_."ce_content` 
	    WHERE 1 GROUP BY hook");
    }

    public static function getAllContentsCEModule($hook,$active = false) {
	    $context = Context::getContext();
	    $filterWhere = "";
	    if($active !== false){
		    $filterWhere .= " AND t.active=".(int)$active;
	    }
	    $lists = Db::getInstance()->executeS("SELECT cl.title,c.id_ce_content FROM `"._DB_PREFIX_."ce_content` AS c 
	    LEFT JOIN `"._DB_PREFIX_."ce_content_lang` AS cl ON cl.id_ce_content=c.id_ce_content AND cl.id_lang=" . (int)$context->language->id . " AND cl.id_shop=" . (int)$context->shop->id . " 
	    WHERE c.hook LIKE '" . pSQL($hook) . "'" . pSQL($filterWhere));
	    $templates = [];
	    foreach ($lists as $item) {
		    $name = $item['id_ce_content'] . '.ce_trans_contents_' . $hook;
		    $templates[$name] = [
			    'title' => $item['title'] ?: 'No title',
			    'name' => $name
		    ];
	    }
	    return $templates;
    }

    public static function getAllTemplatesCEModule($type, $active = false) {
		$context = Context::getContext();
	    $filterWhere = "";
	    if($active !== false){
		    $filterWhere .= " AND t.active=".(int)$active;
	    }
	    $lists = Db::getInstance()->executeS("SELECT t.type,tl.title,t.id_ce_theme FROM `"._DB_PREFIX_."ce_theme` AS t 
	    LEFT JOIN `"._DB_PREFIX_."ce_theme_lang` AS tl ON tl.id_ce_theme=t.id_ce_theme AND tl.id_lang=" . pSQL($context->language->id) . " AND tl.id_shop=" . pSQL($context->shop->id) . " 
	    WHERE t.type LIKE '" . pSQL($type) . "'" . pSQL($filterWhere));
	    $templates = [];
	    //ce_templates_footer
	    foreach ($lists as $item) {
	    	$name = $item['id_ce_theme'] . '.ce_trans_templates_' . $type;
	    	$templates[$name] = [
	    		'title' => $item['title'] ?: 'No title',
			    'name' => $name
		    ];
	    }
	    return $templates;
    }

    public static function setNameForAllThemes($themes, $prefix)
    {
        foreach ($themes as &$theme)
        {
            $theme['name'] = $prefix.$theme['title'];
        }
        return $themes;
    }
    public static function setNameForAllModules($modules, $prefix)
    {
        foreach ($modules as &$module){
            $module['name'] = $prefix.$module['title'];
        }
        return $modules;
    }

    public static function setEmailForAllThemes($themes, $emails)
    {
        foreach ($themes as &$theme)
        {
            $theme['emails'] = EtsTransCore::setValForEmail($emails, 'inter_email_body_theme_'.$theme['title'].'_');
        }
        return $themes;
    }

    public static function setValForEmail($emails, $prefix)
    {
        foreach ($emails as &$email)
        {
            $email['val'] = $prefix.$email['key'];
        }
        return $emails;
    }
}