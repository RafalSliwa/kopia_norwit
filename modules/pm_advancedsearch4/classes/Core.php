<?php
/**
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

namespace AdvancedSearch;
if (!defined('_PS_VERSION_')) {
    exit;
}
use Db;
use Meta;
use Shop;
use Tools;
use Module;
use Context;
use Country;
use Category;
use Currency;
use Language;
use Validate;
use Exception;
use ObjectModel;
use Configuration;
use AdvancedSearch\Traits\SupportsSeoPages;
use AdvancedSearch\Traits\SupportsImageCriterionGroup;
class Core extends Module
{
    use SupportsSeoPages;
    use SupportsImageCriterionGroup;
    const MODULE_NAMESPACE = '\\AdvancedSearch\\';
    protected $html;
    protected $baseConfigUrl;
    protected $defaultLanguage;
    protected $isoLang;
    protected $languages;
    protected $coreClassName;
    protected $registerOnHooks;
    public static $modulePrefix = 'as4';
    protected $copyright_link = false;
    protected $support_link = false;
    protected $getting_started = false;
    protected $initTinyMceAtEnd = false;
    protected $initColorPickerAtEnd = false;
    protected $tempUploadDir = '/uploads/temp/';
    protected $cacheIsInMaintenance = null;
    protected $infos = [];
    protected $success = [];
    protected $errors = [];
    protected $cssJsToLoad = [];
    protected $allowFileExtension;
    protected $defaultConfiguration;
    protected $fileToCheck = [];
    
    public function __construct()
    {
        $this->coreClassName = Tools::strtolower(get_class());
        parent::__construct();
        $this->initClassVar();
    }
    public function install()
    {
        if (parent::install() == false or $this->registerHooks() == false) {
            return false;
        }
        return true;
    }
    protected function registerHooks()
    {
        if (empty($this->registerOnHooks)) {
            return true;
        }
        foreach ($this->registerOnHooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }
    public static function Db_ExecuteS($q)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($q);
    }
    protected function getProductsOnLive($q, $limit, $start)
    {
        $result = self::Db_ExecuteS('
        SELECT p.`id_product`, CONCAT(p.`id_product`, \' - \', IFNULL(CONCAT(NULLIF(TRIM(p.reference), \'\'), \' - \'), \'\'), pl.`name`) AS name
        FROM `' . _DB_PREFIX_ . 'product` p, `' . _DB_PREFIX_ . 'product_lang` pl, `' . _DB_PREFIX_ . 'product_shop` ps
        WHERE p.`id_product`=pl.`id_product`
        AND p.`id_product`=ps.`id_product`
        ' . Shop::addSqlRestriction(false, 'ps') . '
        AND pl.`id_lang`=' . (int)$this->defaultLanguage . '
        AND ps.`active` = 1
        AND ((p.`id_product` LIKE \'%' . pSQL($q) . '%\') OR (pl.`name` LIKE \'%' . pSQL($q) . '%\') OR (p.`reference` LIKE \'%' . pSQL($q) . '%\') OR (pl.`description` LIKE \'%' . pSQL($q) . '%\') OR (pl.`description_short` LIKE \'%' . pSQL($q) . '%\'))
        GROUP BY p.`id_product`
        ORDER BY pl.`name` ASC ' . ($limit ? 'LIMIT ' . (int)$start . ', ' . (int)$limit : ''));
        return $result;
    }
    protected function getSuppliersOnLive($q, $limit, $start)
    {
        $result = self::Db_ExecuteS('
        SELECT s.`id_supplier`, s.`name`
        FROM `' . _DB_PREFIX_ . 'supplier` s
        WHERE (s.name LIKE \'%' . pSQL($q) . '%\')
        AND s.`active` = 1
        ORDER BY s.`name` ' . ($limit ? 'LIMIT ' . (int)$start . ', ' . (int)$limit : ''));
        return $result;
    }
    protected function getManufacturersOnLive($q, $limit, $start)
    {
        $result = self::Db_ExecuteS('
        SELECT m.`id_manufacturer`, m.`name`
        FROM `' . _DB_PREFIX_ . 'manufacturer` m
        WHERE (m.name LIKE \'%' . pSQL($q) . '%\')
        AND m.`active` = 1
        ORDER BY m.`name` ' . ($limit ? 'LIMIT ' . (int)$start . ', ' . (int)$limit : ''));
        return $result;
    }
    protected function getCMSPagesOnLive($q, $limit, $start)
    {
        $result = self::Db_ExecuteS('
        SELECT c.`id_cms`, cl.`meta_title`
        FROM `' . _DB_PREFIX_ . 'cms` c
        LEFT JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON c.id_cms=cl.id_cms
        WHERE (cl.meta_title LIKE \'%' . pSQL($q) . '%\')
        AND cl.`id_lang`=' . (int)$this->defaultLanguage . '
        AND c.`active` = 1
        ORDER BY cl.`meta_title` ' . ($limit ? 'LIMIT ' . (int)$start . ', ' . (int)$limit : ''));
        return $result;
    }
    public static function getCustomMetasByIdLang()
    {
        $finalList = [];
        $metas = Meta::getMetas();
        foreach ($metas as $meta) {
            $finalList[$meta['page']] = [
                'page' => $meta['page'],
                'title' => $meta['page'],
            ];
        }
        $pages_names = Meta::getMetasByIdLang((int)Context::getContext()->language->id);
        foreach ($pages_names as $pageName) {
            if (!empty($pageName['title'])) {
                $pageName['title'] .= ' (' . $pageName['page'] . ')';
            }
            $finalList[$pageName['page']] = $pageName;
        }
        unset($pages_names);
        $moduleInstance = Module::getInstanceByName(_PM_AS_MODULE_NAME_);
        $finalList['checkout'] = [
            'page' => 'checkout',
            'title' => $moduleInstance->l('Checkout', $moduleInstance->coreClassName) . ' (checkout)',
        ];
        $finalList['product'] = [
            'page' => 'product',
            'title' => $moduleInstance->l('Product', $moduleInstance->coreClassName) . ' (product)',
        ];
        $finalList['category'] = [
            'page' => 'category',
            'title' => $moduleInstance->l('Category', $moduleInstance->coreClassName) . ' (category)',
        ];
        $finalList['cms'] = [
            'page' => 'cms',
            'title' => $moduleInstance->l('CMS', $moduleInstance->coreClassName) . ' (cms)',
        ];
        $finalList['index'] = [
            'page' => 'index',
            'title' => $moduleInstance->l('Homepage', $moduleInstance->coreClassName) . ' (index)',
        ];
        return $finalList;
    }
    protected function getControllerNameOnLive($q)
    {
        $pages = Meta::getPages();
        $pages['product'] = 'product';
        $pages['category'] = 'category';
        $pages['cms'] = 'cms';
        $pages['index'] = 'index';
        $pages['checkout'] = 'checkout';
        $pages_names = self::getCustomMetasByIdLang();
        $ignoreList = [
            _PM_AS_MODULE_NAME_ . '-advancedsearch4',
            _PM_AS_MODULE_NAME_ . '-seositemap',
        ];
        $controllers_list = [];
        foreach ($pages_names as $page_name) {
            if (isset($page_name['page']) && ((isset($pages[$page_name['page']]) || in_array($page_name['page'], $pages)) || (isset($pages[str_replace('-', '', $page_name['page'])]) || in_array(str_replace('-', '', $page_name['page']), $pages)))) {
                $ignore = false;
                foreach ($ignoreList as $pageToIgnore) {
                    if (stripos($page_name['page'], $pageToIgnore) !== false) {
                        $ignore = true;
                        continue;
                    }
                }
                if (!$ignore && (stripos($page_name['page'], $q) !== false || stripos($page_name['title'], $q) !== false)) {
                    $controllers_list[] = $page_name;
                }
            }
        }
        return $controllers_list;
    }
    protected function pmClearCache()
    {
        $this->clearCompiledTpl();
        return $this->context->smarty->clearCache(null, self::$modulePrefix);
    }
    protected static function clearCompiledTplAlternative($tplFileName, $compileDir)
    {
        $result = false;
        $compileDir = rtrim($compileDir, '/');
        $files = scandir($compileDir);
        if ($files && count($files)) {
            foreach ($files as $filename) {
                if ($filename != '.' && $filename != '..' && is_dir($compileDir . '/' . $filename)) {
                    self::clearCompiledTplAlternative($tplFileName, $compileDir . '/' . $filename);
                } else {
                    $ext = self::getFileExtension($filename);
                    if ($filename == '.' && $filename == '..' || is_dir($compileDir . '/' . $filename) || $filename == 'index.php' || $ext != 'php' || !preg_match('/file\.' . preg_quote($tplFileName) . '\.php/', $filename)) {
                        continue;
                    }
                    if (Tools::file_exists_cache($compileDir . '/' . $filename) && @unlink($compileDir . '/' . $filename)) {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }
    protected function clearCompiledTpl()
    {
        $files = scandir(dirname(__FILE__));
        if ($files && count($files)) {
            foreach ($files as $filename) {
                $ext = self::getFileExtension($filename);
                if ($ext != 'tpl') {
                    continue;
                }
                if (!$this->context->smarty->clearCompiledTemplate($filename)) {
                    self::clearCompiledTplAlternative($filename, $this->context->smarty->getCompileDir());
                }
            }
        }
    }
    protected function checkPermissions()
    {
        if (empty($this->fileToCheck)) {
            return [];
        }
        $errors = [];
        foreach ($this->fileToCheck as $fileOrDir) {
            if (!is_writable(dirname(__FILE__) . '/../' . $fileOrDir)) {
                $errors[] = dirname(__FILE__) . '/../' . $fileOrDir;
            }
        }
        return $errors;
    }
    protected function getContent()
    {
        $return = '';
        return $return;
    }
    public static function getFileExtension($filename)
    {
        $split = explode('.', $filename);
        $extension = end($split);
        return Tools::strtolower($extension);
    }
    public function showWarning($text)
    {
        $vars = [
            'text' => $text,
        ];
        return $this->fetchTemplate('core/warning.tpl', $vars);
    }
    protected function showRating($show = false)
    {
        $dismiss = (int)Configuration::getGlobalValue('PM_' . self::$modulePrefix . '_DISMISS_RATING');
        if ($show && $dismiss != 1 && self::getNbDaysModuleUsage() >= 3) {
            return $this->fetchTemplate('core/rating.tpl');
        }
        return '';
    }
    public function showInfo($text)
    {
        $vars = [
            'text' => $text,
        ];
        return $this->fetchTemplate('core/info.tpl', $vars);
    }
    public function displayTitle($title)
    {
        $vars = [
            'text' => $title,
        ];
        return $this->fetchTemplate('core/title.tpl', $vars);
    }
    public function displaySubTitle($title)
    {
        $vars = [
            'text' => $title,
        ];
        return $this->fetchTemplate('core/sub_title.tpl', $vars);
    }
    protected function displayJsTags($type = '', $return = false)
    {
        $opener = false;
        $closer = false;
        if ($type == 'open') {
            $opener = true;
        } elseif ($type == 'close') {
            $closer = true;
        }
        $vars = [
            'opener' => $opener,
            'closer' => $closer,
        ];
        if ($return) {
            return $this->fetchTemplate('core/js_tags.tpl', $vars);
        }
        $this->html .= $this->fetchTemplate('core/js_tags.tpl', $vars);
    }
    protected function displayCloseDialogIframeJs($include_script_tag = false)
    {
        $vars = [
            'include_script_tag' => $include_script_tag,
        ];
        $this->html .= $this->fetchTemplate('core/js_closeDialogIframe.tpl', $vars);
    }
    protected function displayErrorsJs($include_script_tag = false)
    {
        $vars = [
            'include_script_tag' => $include_script_tag,
            'js_errors' => $this->errors,
        ];
        $this->html .= $this->fetchTemplate('core/js_errors.tpl', $vars);
    }
    protected function displaySuccessJs($include_script_tag = false)
    {
        $vars = [
            'include_script_tag' => $include_script_tag,
            'js_successes' => $this->success,
        ];
        $this->html .= $this->fetchTemplate('core/js_success.tpl', $vars);
    }
    protected function displayInfosJs($include_script_tag = false)
    {
        $vars = [
            'include_script_tag' => $include_script_tag,
            'js_infos' => $this->infos,
        ];
        $this->html .= $this->fetchTemplate('core/js_infos.tpl', $vars);
    }
    private function getPMdata()
    {
        $param = [];
        $param[] = 'ver-' . _PS_VERSION_;
        $param[] = 'current-' . $this->name;
        
        $result = $this->getPMAddons();
        if ($result && self::isFilledArray($result)) {
            foreach ($result as $moduleName => $moduleVersion) {
                $param[] = $moduleName . '-' . $moduleVersion;
            }
        }
        return self::getDataSerialized(implode('|', $param));
    }
    private function getPMAddons()
    {
        $pmAddons = [];
        $result = self::Db_ExecuteS('SELECT DISTINCT name FROM ' . _DB_PREFIX_ . 'module WHERE name LIKE "pm_%"');
        if ($result && self::isFilledArray($result)) {
            foreach ($result as $module) {
                $instance = Module::getInstanceByName($module['name']);
                if (!empty($instance) && !empty($instance->version)) {
                    $pmAddons[(string)$module['name']] = $instance->version;
                }
            }
        }
        return $pmAddons;
    }
    private function doHttpRequest($data = [], $c = 'prestashop', $s = 'api.addons')
    {
        $data = array_merge([
            'version' => _PS_VERSION_,
            'iso_lang' => Tools::strtolower($this->isoLang),
            'iso_code' => Tools::strtolower(Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'))),
            'module_key' => $this->module_key,
            'method' => 'contributor',
            'action' => 'all_products',
        ], $data);
        $postData = http_build_query($data);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'content' => $postData,
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 15,
            ],
        ]);
        $response = Tools::file_get_contents('https://' . $s . '.' . $c . '.com', false, $context);
        if (empty($response)) {
            return false;
        }
        $responseToJson = json_decode((string)$response);
        if (empty($responseToJson)) {
            return false;
        }
        return $responseToJson;
    }
    private function getAddonsModulesFromApi()
    {
        $modules = Configuration::get('PM_' . self::$modulePrefix . '_AM');
        $modules_date = Configuration::get('PM_' . self::$modulePrefix . '_AMD');
        if ($modules && strtotime('+2 day', (int)$modules_date) > time()) {
            return json_decode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest();
        if (empty($jsonResponse->products)) {
            return [];
        }
        $dataToStore = [];
        foreach ($jsonResponse->products as $addonsEntry) {
            $dataToStore[(int)$addonsEntry->id] = [
                'name' => $addonsEntry->name,
                'displayName' => $addonsEntry->displayName,
                'url' => $addonsEntry->url,
                'compatibility' => $addonsEntry->compatibility,
                'version' => $addonsEntry->version,
                'description' => $addonsEntry->description,
            ];
        }
        Configuration::updateValue('PM_' . self::$modulePrefix . '_AM', json_encode($dataToStore));
        Configuration::updateValue('PM_' . self::$modulePrefix . '_AMD', time());
        return json_decode((string)Configuration::get('PM_' . self::$modulePrefix . '_AM'), true);
    }
    private function getPMModulesFromApi()
    {
        $modules = Configuration::get('PM_' . self::$modulePrefix . '_PMM');
        $modules_date = Configuration::get('PM_' . self::$modulePrefix . '_PMMD');
        if ($modules && strtotime('+2 day', (int)$modules_date) > time()) {
            return json_decode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest(['list' => $this->getPMAddons()], 'presta-module', 'api-addons');
        if (empty($jsonResponse)) {
            return [];
        }
        Configuration::updateValue('PM_' . self::$modulePrefix . '_PMM', json_encode($jsonResponse));
        Configuration::updateValue('PM_' . self::$modulePrefix . '_PMMD', time());
        return json_decode(Configuration::get('PM_' . self::$modulePrefix . '_PMM'), true);
    }
    public function displaySupport()
    {
        $get_started_image_list = [];
        if (!empty($this->getting_started) && self::isFilledArray($this->getting_started)) {
            foreach ($this->getting_started as $get_started_image) {
                $get_started_image_list[] = "{ 'href': '" . $get_started_image['href'] . "', 'title': '" . htmlentities($get_started_image['title'], ENT_QUOTES, 'UTF-8') . "' }";
            }
        }
        $pm_addons_products = $this->getAddonsModulesFromApi();
        $pm_products = $this->getPMModulesFromApi();
        if (!is_array($pm_addons_products)) {
            $pm_addons_products = [];
        }
        if (!is_array($pm_products)) {
            $pm_products = [];
        }
        self::shuffleArray($pm_addons_products);
        if (self::isFilledArray($pm_addons_products)) {
            if (!empty($pm_products['ignoreList']) && self::isFilledArray($pm_products['ignoreList'])) {
                foreach ($pm_products['ignoreList'] as $ignoreId) {
                    if (isset($pm_addons_products[$ignoreId])) {
                        unset($pm_addons_products[$ignoreId]);
                    }
                }
            }
            $addonsList = $this->getPMAddons();
            if ($addonsList && self::isFilledArray($addonsList)) {
                foreach (array_keys($addonsList) as $moduleName) {
                    foreach ($pm_addons_products as $k => $pm_addons_product) {
                        if ($pm_addons_product['name'] == $moduleName) {
                            unset($pm_addons_products[$k]);
                            break;
                        }
                    }
                }
            }
        }
        $vars = [
            'support_links' => (self::isFilledArray($this->support_link) ? $this->support_link : []),
            'copyright_link' => (self::isFilledArray($this->copyright_link) ? $this->copyright_link : false),
            'get_started_image_list' => (!empty($this->getting_started) && self::isFilledArray($this->getting_started) ? $this->getting_started : []),
            'pm_module_version' => $this->version,
            'pm_data' => $this->getPMdata(),
            'pm_products' => $pm_products,
            'pm_addons_products' => $pm_addons_products,
            'html_at_end' => $this->includeHTMLAtEnd(),
        ];
        return $this->fetchTemplate('core/support.tpl', $vars);
    }
    protected static function getRawRequestValue($attributeName)
    {
        $values = Tools::getAllValues();
        if (isset($values[$attributeName])) {
            return $values[$attributeName];
        }
        return null;
    }
    protected function preProcess()
    {
        if (Tools::getIsset('dismissRating')) {
            $this->cleanOutput();
            Configuration::updateGlobalValue('PM_' . self::$modulePrefix . '_DISMISS_RATING', 1);
            die;
        } elseif (Tools::getIsset('pm_load_function')) {
            if (!self::supportsSeoPages() && Tools::getValue('pm_load_function') == 'displaySeoSearchPanelList') {
                $this->cleanOutput();
                $this->html .= $this->showInfo($this->l('You need to upgrade to the PRO version to use SEO Pages. Please contact us to upgrade.', 'Core'));
                $this->echoOutput(true);
            }
            if (method_exists($this, Tools::getValue('pm_load_function'))) {
                $this->cleanOutput();
                if (self::getRawRequestValue('class')) {
                    if (class_exists(self::MODULE_NAMESPACE . self::getRawRequestValue('class'))) {
                        $class = self::MODULE_NAMESPACE . self::getRawRequestValue('class');
                        $obj = new $class();
                        if (Tools::getValue($obj->identifier)) {
                            $obj = new $class(Tools::getValue($obj->identifier));
                        }
                        $pmLoadFunction = Tools::getValue('pm_load_function');
                        $params = [
                            'obj' => $obj,
                            'class' => $class,
                            'method' => $pmLoadFunction,
                            'reload_after' => Tools::getValue('pm_reload_after'),
                            'js_callback' => Tools::getValue('pm_js_callback'),
                        ];
                        $this->html .= $this->$pmLoadFunction($params);
                    } else {
                        $this->cleanOutput();
                        $this->html .= $this->showWarning($this->l('Class', 'Core') . ' ' . self::getRawRequestValue('class') . ' ' . $this->l('does not exists', 'Core'));
                        $this->echoOutput(true);
                    }
                } else {
                    $pmLoadFunction = Tools::getValue('pm_load_function');
                    $params = [
                        'method' => $pmLoadFunction,
                        'reload_after' => Tools::getValue('pm_reload_after'),
                        'js_callback' => Tools::getValue('pm_js_callback'),
                    ];
                    $this->html .= $this->$pmLoadFunction($params);
                }
                $this->echoOutput(true);
            } else {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Method unavailable', 'Core'));
                $this->echoOutput(true);
            }
        } elseif (Tools::getIsset('pm_delete_obj')) {
            if (self::getRawRequestValue('class')) {
                if (class_exists(self::MODULE_NAMESPACE . self::getRawRequestValue('class'))) {
                    $class = self::MODULE_NAMESPACE . self::getRawRequestValue('class');
                    $obj = new $class();
                    $obj = new $class(Tools::getValue($obj->identifier));
                    if ($obj->delete()) {
                        $this->cleanOutput();
                        $this->postDeleteProcess([
                            'class' => get_class($obj),
                        ]);
                        $this->echoOutput(true);
                    } else {
                        $this->cleanOutput();
                        $this->html .= $this->showWarning($this->l('Error while deleting object', 'Core'));
                        $this->echoOutput(true);
                    }
                } else {
                    $this->cleanOutput();
                    $this->html .= $this->showWarning($this->l('Class', 'Core') . ' ' . self::getRawRequestValue('class') . ' ' . $this->l('does not exists', 'Core'));
                    $this->echoOutput(true);
                }
            } else {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Please send class name into “class“ var', 'Core'));
                $this->echoOutput(true);
            }
        } elseif (Tools::getIsset('pm_save_order')) {
            if (!Tools::getValue('order')) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Not receive IDS', 'Core'));
                $this->echoOutput(true);
            } elseif (!Tools::getValue('destination_table')) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Please send destination table', 'Core'));
                $this->echoOutput(true);
            } elseif (!Tools::getValue('field_to_update')) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Please send name of position field', 'Core'));
                $this->echoOutput(true);
            } elseif (!Tools::getValue('identifier')) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Please send identifier', 'Core'));
                $this->echoOutput(true);
            } else {
                $order = Tools::getValue('order');
                $identifier = Tools::getValue('identifier');
                $field_to_update = Tools::getValue('field_to_update');
                $destination_table = Tools::getValue('destination_table');
                foreach ($order as $position => $id) {
                    $id = preg_replace("/^\w+_/", '', $id);
                    $data = [
                        $field_to_update => $position,
                    ];
                    Db::getInstance()->update($destination_table, $data, $identifier . ' = ' . (int) $id);
                }
                $this->cleanOutput();
                $this->echoOutput(true);
            }
        } elseif (Tools::getIsset('getPanel') && Tools::getValue('getPanel')) {
            $this->cleanBuffer();
            switch (Tools::getValue('getPanel')) {
                case 'getChildrenCategories':
                    if (Tools::getValue('id_category_parent')) {
                        $children_categories = self::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), $this->defaultLanguage);
                        die(json_encode($children_categories));
                    }
                    break;
            }
        } elseif (Tools::getIsset('pm_duplicate_obj')) {
            if (!self::getRawRequestValue('class')) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Please send class name into “class“ var', 'Core'));
                $this->echoOutput(true);
            }
            if (!class_exists(self::MODULE_NAMESPACE . self::getRawRequestValue('class'))) {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Class', 'Core') . ' ' . self::getRawRequestValue('class') . ' ' . $this->l('does not exists', 'Core'));
                $this->echoOutput(true);
            }
            $class = self::MODULE_NAMESPACE . self::getRawRequestValue('class');
            $obj = new $class();
            $obj = new $class((int)Tools::getValue($obj->identifier));
            $duplicated_obj = $obj->duplicate();
            if ($duplicated_obj instanceof $class) {
                $this->cleanOutput();
                $this->postDuplicateProcess([
                    'obj' => $duplicated_obj,
                    'class' => get_class($duplicated_obj),
                ]);
                $this->echoOutput(true);
            } else {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Error while duplicating object', 'Core'));
                $this->echoOutput(true);
            }
        }
    }
    protected function isInMaintenance()
    {
        if (isset($this->cacheIsInMaintenance)) {
            return $this->cacheIsInMaintenance;
        }
        $config = $this->getModuleConfiguration();
        if (!empty($config['maintenanceMode'])) {
            $ips = explode(',', (string)Configuration::get('PS_MAINTENANCE_IP'));
            if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                $this->cacheIsInMaintenance = false;
                return false;
            }
            $this->cacheIsInMaintenance = true;
            return true;
        }
        $this->cacheIsInMaintenance = false;
        return false;
    }
    protected function postDuplicateProcess($params)
    {
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('open');
        }
        if (Tools::getIsset('pm_reload_after') && Tools::getValue('pm_reload_after')) {
            $this->reloadPanels(Tools::getValue('pm_reload_after'));
        }
        if (Tools::getIsset('pm_js_callback') && Tools::getValue('pm_js_callback')) {
            $this->getJsCallback(Tools::getValue('pm_js_callback'));
        }
        $this->success[] = $this->l('Successfully duplicated', 'Core');
        $this->displaySuccessJs(false);
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('close');
        }
    }
    protected function postDeleteProcess($params)
    {
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('open');
        }
        if (Tools::getIsset('pm_reload_after') && Tools::getValue('pm_reload_after')) {
            $this->reloadPanels(Tools::getValue('pm_reload_after'));
        }
        if (Tools::getIsset('pm_js_callback') && Tools::getValue('pm_js_callback')) {
            $this->getJsCallback(Tools::getValue('pm_js_callback'));
        }
        $this->success[] = $this->l('Successfully deleted', 'Core');
        $this->displaySuccessJs(false);
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('close');
        }
    }
    protected function getJsCallback($js_callback)
    {
        $js_callbacks = explode('|', $js_callback);
        foreach ($js_callbacks as $js_callback) {
            $this->html .= 'parent.parent.' . $js_callback . '();';
        }
    }
    protected function reloadPanels($reload_after)
    {
        $reload_after = explode('|', $reload_after);
        foreach ($reload_after as $panel) {
            $this->html .= 'parent.parent.reloadPanel("' . $panel . '");';
        }
    }
    protected function postSaveProcess($params)
    {
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('open');
        }
        if (isset($params['reload_after']) && $params['reload_after']) {
            $this->reloadPanels($params['reload_after']);
        }
        if (isset($params['js_callback']) && $params['js_callback']) {
            $this->getJsCallback($params['js_callback']);
        }
        $this->success[] = $this->l('Successfully saved', 'Core');
        $this->displaySuccessJs(false);
        if (isset($params['include_script_tag']) && $params['include_script_tag']) {
            $this->displayJsTags('close');
        }
    }
    protected function postProcess()
    {
        if (self::getRawRequestValue('pm_save_obj')) {
            if (class_exists(self::getRawRequestValue('pm_save_obj'))) {
                $class = self::getRawRequestValue('pm_save_obj');
                $obj = new $class();
                if (Tools::getValue($obj->identifier)) {
                    $obj = new $class(Tools::getValue($obj->identifier));
                }
                $this->copyFromPost($obj);
                $this->errors = $this->retroValidateController($obj);
                if (!self::isFilledArray($this->errors)) {
                    if ($obj->save()) {
                        $this->cleanOutput();
                        $this->postSaveProcess([
                            'class' => get_class($obj),
                            'obj' => $obj,
                            'include_script_tag' => true,
                            'reload_after' => Tools::getValue('pm_reload_after'),
                            'js_callback' => Tools::getValue('pm_js_callback'),
                        ]);
                        $this->echoOutput(true);
                    } else {
                        $this->cleanOutput();
                        $this->html .= $this->showWarning($this->l('Error while saving object', 'Core'));
                        $this->echoOutput(true);
                    }
                } else {
                    $this->cleanOutput();
                    $this->displayErrorsJs(true);
                    $this->echoOutput(true);
                }
            } else {
                $this->cleanOutput();
                $this->html .= $this->showWarning($this->l('Class', 'Core') . ' ' . self::getRawRequestValue('class') . ' ' . $this->l('does not exists', 'Core'));
                $this->echoOutput(true);
            }
        } elseif (Tools::getValue('uploadTempFile')) {
            $this->postProcessUploadTempFile();
        } elseif (Tools::getValue('getItem')) {
            $this->cleanOutput();
            $item = Tools::getValue('itemType');
            $query = Tools::getValue('q', false);
            if (!$query || Tools::strlen($query) < 1) {
                $this->cleanBuffer();
                die;
            }
            $limit = Tools::getValue('limit', 100);
            $start = Tools::getValue('start', 0);
            $items = [];
            $item_id_column = null;
            $item_name_column = null;
            switch ($item) {
                case 'product':
                    $items = $this->getProductsOnLive($query, $limit, $start);
                    $item_id_column = 'id_product';
                    $item_name_column = 'name';
                    break;
                case 'supplier':
                    $items = $this->getSuppliersOnLive($query, $limit, $start);
                    $item_id_column = 'id_supplier';
                    $item_name_column = 'name';
                    break;
                case 'manufacturer':
                    $items = $this->getManufacturersOnLive($query, $limit, $start);
                    $item_id_column = 'id_manufacturer';
                    $item_name_column = 'name';
                    break;
                case 'cms':
                    $items = $this->getCMSPagesOnLive($query, $limit, $start);
                    $item_id_column = 'id_cms';
                    $item_name_column = 'meta_title';
                    break;
                case 'controller':
                    $items = $this->getControllerNameOnLive($query);
                    $item_id_column = 'page';
                    $item_name_column = 'title';
                    break;
                default:
                    break;
            }
            if (!empty($items)) {
                foreach ($items as $row) {
                    $this->html .= $row[$item_id_column] . '=' . $row[$item_name_column] . "\n";
                }
            }
            $this->echoOutput(true);
            die;
        }
    }
    protected function postProcessUploadTempFile()
    {
        $this->cleanOutput();
        if (isset($_REQUEST['name'])) {
            $fileName = basename($_REQUEST['name']);
        } elseif (!empty($_FILES)) {
            $fileName = basename($_FILES['fileUpload']['name']);
        } else {
            $fileName = uniqid('file_' . self::$modulePrefix . mt_rand());
        }
        $extension = self::getFileExtension($fileName);
        $filePath = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . uniqid(self::$modulePrefix . mt_rand()) . '.' . $extension;
        if (!$out = @fopen("{$filePath}.part", 'wb')) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES['fileUpload']['error'] || !is_uploaded_file($_FILES['fileUpload']['tmp_name'])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            if (!$in = @fopen($_FILES['fileUpload']['tmp_name'], 'rb')) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen('php://input', 'rb')) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}.part", $filePath);
        die('{"jsonrpc" : "2.0", "filename" : "' . basename($filePath) . '"}');
    }
    protected function initClassVar()
    {
        $this->defaultLanguage = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->isoLang = (string)Language::getIsoById((int)$this->context->language->id);
        $this->languages = Language::getLanguages(false);
    }
    public function startForm($configOptions)
    {
        $defaultOptions = [
            'action' => false,
            'target' => 'dialogIframePostForm',
            'iframetarget' => true,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $return = '';
        if ($configOptions['iframetarget']) {
            $return .= $this->headerIframe();
        }
        $vars = [
            'form_action' => ($configOptions['action'] ? $configOptions['action'] : $this->baseConfigUrl),
            'form_id' => $configOptions['id'],
            'form_target' => $configOptions['target'],
            'obj_id' => (isset($configOptions['obj']) && is_object($configOptions['obj']) && !empty($configOptions['obj']->id) ? $configOptions['obj']->id : false),
            'obj_identifier' => (isset($configOptions['obj']) && is_object($configOptions['obj']) && !empty($configOptions['obj']->identifier) ? $configOptions['obj']->identifier : false),
            'obj_class' => (isset($configOptions['obj']) && is_object($configOptions['obj']) ? get_class($configOptions['obj']) : false),
            'pm_reload_after' => (!empty($configOptions['params']['reload_after']) ? $configOptions['params']['reload_after'] : false),
            'pm_js_callback' => (!empty($configOptions['params']['js_callback']) ? $configOptions['params']['js_callback'] : false),
        ];
        $return .= $this->fetchTemplate('core/components/form/start_form.tpl', $vars, $configOptions);
        return $return;
    }
    public function endForm($configOptions)
    {
        $defaultOptions = [
            'id' => null,
            'iframetarget' => true,
            'jquerytoolsvalidatorfunction' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $vars = [
            'form_id' => $configOptions['id'],
            'has_jquerytools' => ($configOptions['id'] != null && in_array('jquerytools', $this->cssJsToLoad)),
            'jquerytools_validator_function' => $configOptions['jquerytoolsvalidatorfunction'],
        ];
        $return = $this->fetchTemplate('core/components/form/end_form.tpl', $vars, $configOptions);
        if ($configOptions['iframetarget']) {
            $return .= $this->footerIframe();
        }
        return $return;
    }
    protected function retrieveFormValue($type, $fieldName, $fieldDbName, $obj, $defaultValue = '', $compareValue = false, $key = false)
    {
        if (!$fieldDbName) {
            $fieldDbName = $fieldName;
        }
        switch ($type) {
            case 'text':
                if (is_array($obj)) {
                    if ($key) {
                        return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName][$key]) ? $obj[$fieldDbName][$key] : $defaultValue)), ENT_COMPAT, 'UTF-8');
                    } else {
                        return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)), ENT_COMPAT, 'UTF-8');
                    }
                }
                if ($key) {
                    return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}[$key]) ? $obj->{$fieldDbName}[$key] : $defaultValue)), ENT_COMPAT, 'UTF-8');
                } else {
                    return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue)), ENT_COMPAT, 'UTF-8');
                }
            case 'textpx':
                if (is_array($obj)) {
                    if ($key) {
                        return (int)preg_replace('#px#', '', Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName][$key] : $defaultValue));
                    } else {
                        return (int)preg_replace('#px#', '', Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue));
                    }
                }
                if ($key) {
                    return (int)preg_replace('#px#', '', Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName}[$key] : $defaultValue));
                } else {
                    return (int)preg_replace('#px#', '', Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue));
                }
            case 'select':
                if (is_array($obj)) {
                    return (Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue) == $compareValue) ? ' selected="selected"' : '';
                }
                return (Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue) == $compareValue) ? ' selected="selected"' : '';
            case 'radio':
            case 'checkbox':
                if (is_array($obj)) {
                    if (isset($obj[$fieldName]) && is_array($obj[$fieldName]) && count($obj[$fieldName]) && isset($obj[$fieldDbName])) {
                        return (in_array($compareValue, $obj[$fieldName])) ? ' checked="checked"' : '';
                    }
                    return (Tools::getValue($fieldName, self::isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue) == $compareValue) ? ' checked="checked"' : '';
                }
                if (isset($obj->$fieldName) && is_array($obj->$fieldName) && count($obj->$fieldName) && isset($obj->{$fieldDbName})) {
                    return (in_array($compareValue, $obj->$fieldName)) ? ' checked="checked"' : '';
                }
                return (Tools::getValue($fieldName, $obj && isset($obj->{$fieldDbName}) ? $obj->{$fieldDbName} : $defaultValue) == $compareValue) ? ' checked="checked"' : '';
        }
    }
    public function startFieldset($configOptions)
    {
        $defaultOptions = [
            'title' => false,
            'icon' => false,
            'hide' => true,
            'onclick' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        return $this->fetchTemplate('core/components/fieldset/start_fieldset.tpl', [], $configOptions);
    }
    public function endFieldset()
    {
        return $this->fetchTemplate('core/components/fieldset/end_fieldset.tpl');
    }
    public function displayAjaxSelectMultiple($configOptions)
    {
        $defaultOptions = [
            'remoteurl' => false,
            'limit' => 50,
            'limitincrement' => 20,
            'remoteparams' => false,
            'tips' => false,
            'triggeronliclick' => true,
            'displaymore' => true,
            'idcolumn' => '',
            'namecolumn' => '',
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $vars = [
            'index_column' => (isset($configOptions['namecolumn']) && isset($configOptions['idcolumn']) && !empty($configOptions['namecolumn']) && !empty($configOptions['idcolumn'])),
        ];
        return $this->fetchTemplate('core/components/ajax_select_multiple.tpl', $vars, $configOptions);
    }
    public function displayInputActive($configOptions)
    {
        $defaultOptions = [
            'defaultvalue' => false,
            'tips' => false,
            'onclick' => false,
            'on_label' => false,
            'off_label' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $vars = [
            'selected_on' => $this->retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 1),
            'selected_off' => $this->retrieveFormValue('radio', $configOptions['key_active'], $configOptions['key_db'], $configOptions['obj'], $configOptions['defaultvalue'], 0),
        ];
        return $this->fetchTemplate('core/components/input_active.tpl', $vars, $configOptions);
    }
    public function displayInputColor($configOptions)
    {
        $defaultOptions = [
            'size' => '90px',
            'defaultvalue' => false,
            'tips' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        if (empty($configOptions['id'])) {
            $configOptions['id'] = 'color_' . uniqid(self::$modulePrefix . mt_rand());
        }
        $vars = [
            'current_value' => $this->retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue']),
        ];
        $this->initColorPickerAtEnd = true;
        return $this->fetchTemplate('core/components/input_color.tpl', $vars, $configOptions);
    }
    protected function parseOptions($defaultOptions = [], $options = [])
    {
        if (self::isFilledArray($options)) {
            $options = array_change_key_case($options, CASE_LOWER);
        }
        if (isset($options['tips']) && !empty($options['tips'])) {
            $options['tips'] = htmlentities($options['tips'], ENT_QUOTES, 'UTF-8');
        }
        if (self::isFilledArray($defaultOptions)) {
            $defaultOptions = array_change_key_case($defaultOptions, CASE_LOWER);
            foreach (array_keys($defaultOptions) as $option_name) {
                if (!isset($options[$option_name])) {
                    $options[$option_name] = $defaultOptions[$option_name];
                }
            }
        }
        return $options;
    }
    public function displayInputText($configOptions)
    {
        $defaultOptions = [
            'type' => 'text',
            'size' => '150px',
            'defaultvalue' => false,
            'min' => false,
            'max' => false,
            'maxlength' => false,
            'onkeyup' => false,
            'onchange' => false,
            'required' => false,
            'tips' => false,
            'placeholder' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $vars = [
            'current_value' => $this->retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue']),
        ];
        return $this->fetchTemplate('core/components/input_text.tpl', $vars, $configOptions);
    }
    public function displayInputTextLang($configOptions)
    {
        $defaultOptions = [
            'size' => '150px',
            'type' => 'text',
            'min' => false,
            'max' => false,
            'maxlength' => false,
            'onkeyup' => false,
            'onchange' => false,
            'required' => false,
            'tips' => false,
            'placeholder' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $current_value = [];
        foreach ($this->languages as $language) {
            $current_value[$language['id_lang']] = $this->retrieveFormValue('text', $configOptions['key'] . '_' . $language['id_lang'], $configOptions['key'], $configOptions['obj'], false, false, $language['id_lang']);
        }
        $vars = [
            'current_value' => $current_value,
            'pm_flags' => $this->displayPMFlags(),
        ];
        return $this->fetchTemplate('core/components/input_text_lang.tpl', $vars, $configOptions);
    }
    public function displayRichTextareaLang($configOptions)
    {
        $defaultOptions = [
            'size' => '100%',
            'min' => false,
            'max' => false,
            'maxlength' => false,
            'onkeyup' => false,
            'onchange' => false,
            'required' => false,
            'tips' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $current_value = [];
        foreach ($this->languages as $language) {
            $current_value[$language['id_lang']] = $this->retrieveFormValue('text', $configOptions['key'] . '_' . $language['id_lang'], $configOptions['key'], $configOptions['obj'], false, false, $language['id_lang']);
        }
        $vars = [
            'current_value' => $current_value,
            'pm_flags' => $this->displayPMFlags(false, 'tinyMceFlags'),
        ];
        $this->initTinyMceAtEnd = true;
        return $this->fetchTemplate('core/components/rich_textarea_lang.tpl', $vars, $configOptions);
    }
    public function displaySelect($configOptions)
    {
        $defaultOptions = [
            'size' => '200px',
            'defaultvalue' => false,
            'options' => [],
            'onchange' => false,
            'tips' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $selected_attr = [];
        foreach (array_keys($configOptions['options']) as $value) {
            $selected_attr[$value] = $this->retrieveFormValue('select', $configOptions['key'], false, $configOptions['obj'], '0', $value);
        }
        $vars = [
            'selected_attr' => $selected_attr,
        ];
        return $this->fetchTemplate('core/components/select.tpl', $vars, $configOptions);
    }
    public function displayCategoryTree($configOptions)
    {
        $defaultOptions = [
            'input_name' => 'categoryBox',
            'selected_cat' => [0],
            'use_radio' => false,
            'category_root_id' => Category::getRootCategory()->id,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        $selectedCat = $this->getCategoryInformations(Tools::getValue('categoryBox', $configOptions['selected_cat']), $this->defaultLanguage);
        $vars = [
            'category_tree' => $this->renderAdminCategorieTree($selectedCat, $configOptions['input_name'], $configOptions['use_radio'], $configOptions['category_root_id']),
        ];
        return $this->fetchTemplate('core/components/category_tree/global.tpl', $vars, $configOptions);
    }
    private static function getCategoryInformations($ids_category, $id_lang = null)
    {
        if ($id_lang === null) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        if (!self::isFilledArray($ids_category)) {
            return [];
        }
        $categories = [];
        if (isset($ids_category[0]['id_category'])) {
            $ids_category_tmp = [];
            foreach ($ids_category as $cat) {
                $ids_category_tmp[] = $cat['id_category'];
            }
            $ids_category = $ids_category_tmp;
        } elseif (is_object($ids_category[0]) && isset($ids_category[0]->id_category)) {
            $ids_category_tmp = [];
            foreach ($ids_category as $cat) {
                $ids_category_tmp[] = $cat->id_category;
            }
            $ids_category = $ids_category_tmp;
        }
        foreach ($ids_category as $k => $idCat) {
            if (empty($idCat)) {
                unset($ids_category[$k]);
            }
        }
        if (self::isFilledArray($ids_category)) {
            $results = Db::getInstance()->ExecuteS('
                SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, cl.`id_lang`
                FROM `' . _DB_PREFIX_ . 'category` c
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
                ' . Shop::addSqlAssociation('category', 'cl') . '
                WHERE cl.`id_lang` = ' . (int)$id_lang . '
                AND c.`id_category` IN (' . implode(',', array_map('intval', $ids_category)) . ')');
            foreach ($results as $category) {
                $categories[$category['id_category']] = $category;
            }
        }
        return $categories;
    }
    protected function getCategoryTreeForSelect()
    {
        $categoryList = Category::getCategories((int)$this->context->language->id);
        $categorySelect = $categoryParentSelect = $alreadyAdd = [];
        $rootCategoryId = (int)Configuration::get('PS_ROOT_CATEGORY');
        foreach ($categoryList as $shopCategory) {
            foreach ($shopCategory as $idCategory => $categoryInformations) {
                if ($rootCategoryId == $idCategory) {
                    continue;
                }
                $categoryParentSelect[$categoryInformations['infos']['id_parent']][$idCategory] = str_repeat('&#150 ', $categoryInformations['infos']['level_depth'] - 1) . $categoryInformations['infos']['name'];
            }
        }
        foreach ($categoryList as $shopCategory) {
            foreach ($shopCategory as $idCategory => $categoryInformations) {
                if ($rootCategoryId == $idCategory || in_array($idCategory, $alreadyAdd)) {
                    continue;
                }
                $categorySelect[(int)$idCategory] = str_repeat('&#150 ', $categoryInformations['infos']['level_depth'] - 1) . $categoryInformations['infos']['name'];
                if (isset($categoryParentSelect[(int)$idCategory])) {
                    foreach ($categoryParentSelect[(int)$idCategory] as $idCategoryChild => $categoryLabel) {
                        $categorySelect[(int)$idCategoryChild] = $categoryLabel;
                        $alreadyAdd[] = $idCategoryChild;
                    }
                }
            }
        }
        return $categorySelect;
    }
    protected function renderAdminCategorieTree($selected_cat = [], $input_name = 'categoryBox', $use_radio = false, $category_root_id = 1)
    {
        if (!$use_radio) {
            $input_name = $input_name . '[]';
        }
        $hidden_selected_categories = [];
        $root_is_selected = false;
        foreach ($selected_cat as $cat) {
            if (self::isFilledArray($cat)) {
                if ($cat['id_category'] != $category_root_id) {
                    $hidden_selected_categories[] = $cat['id_category'];
                } elseif ($cat['id_category'] == $category_root_id) {
                    $root_is_selected = true;
                }
            } else {
                if ($cat != $category_root_id) {
                    $hidden_selected_categories[] = $cat;
                } else {
                    $root_is_selected = true;
                }
            }
        }
        $root_category = new Category($category_root_id, $this->defaultLanguage);
        $root_category_name = $root_category->name;
        if (self::isFilledArray($selected_cat)) {
            if (isset($selected_cat[0])) {
                $selected_cat_js = implode(',', $selected_cat);
            } else {
                $selected_cat_js = implode(',', array_keys($selected_cat));
            }
        } else {
            $selected_cat_js = '';
        }
        $input_selector_value = str_replace(']', '', str_replace('[', '', $input_name));
        $vars = [
            'input_name' => $input_name,
            'hidden_selected_categories' => $hidden_selected_categories,
            'selected_cat_js' => $selected_cat_js,
            'category_root_id' => (int)$category_root_id,
            'root_category_name' => $root_category_name,
            'input_selector_value' => $input_selector_value,
            'use_radio' => $use_radio,
            'root_is_selected' => $root_is_selected,
        ];
        return $this->fetchTemplate('core/components/category_tree/tree.tpl', $vars);
    }
    protected function uploadImageLang(&$obj, $key, $path, $add_to_filename = false)
    {
        $ext = false;
        $update = false;
        $errors = [];
        foreach ($this->languages as $language) {
            $file = false;
            if (Tools::getIsset('unlink_' . $key . '_' . $language['id_lang']) and Tools::getValue('unlink_' . $key . '_' . $language['id_lang']) and isset($obj->{$key}[$language['id_lang']]) and $obj->{$key}[$language['id_lang']]) {
                @unlink(_PS_ROOT_DIR_ . $path . $obj->{$key}[$language['id_lang']]);
                $obj->{$key}[$language['id_lang']] = '';
                $update = true;
            } else {
                if (isset($_FILES[$key . '_' . $language['id_lang']]['tmp_name']) and $_FILES[$key . '_' . $language['id_lang']]['tmp_name'] != null) {
                    $file = $_FILES[$key . '_' . $language['id_lang']];
                } elseif ((!isset($obj->{$key}[$language['id_lang']]) || (isset($obj->{$key}[$language['id_lang']]) && !$obj->{$key}[$language['id_lang']])) && isset($_FILES[$key . '_' . $this->defaultLanguage]['tmp_name']) and $_FILES[$key . '_' . $this->defaultLanguage]['tmp_name'] != null) {
                    $file = $_FILES[$key . '_' . $this->defaultLanguage];
                }
                if ($file) {
                    if (!is_dir(_PS_ROOT_DIR_ . $path)) {
                        mkdir(_PS_ROOT_DIR_ . $path, 0777, true);
                    }
                    if (!is_dir(_PS_ROOT_DIR_ . $path . $language['iso_code'] . '/')) {
                        mkdir(_PS_ROOT_DIR_ . $path . $language['iso_code'] . '/', 0777, true);
                    }
                    $ext = self::getFileExtension($file['name']);
                    if (isset($obj->{$key}[$language['id_lang']]) && $obj->{$key}[$language['id_lang']]) {
                        @unlink(_PS_ROOT_DIR_ . $path . $obj->{$key}[$language['id_lang']]);
                    }
                    if (!in_array($ext, $this->allowFileExtension) || !getimagesize($file['tmp_name']) || !copy($file['tmp_name'], _PS_ROOT_DIR_ . $path . $language['iso_code'] . '/' . $obj->id . ($add_to_filename ? $add_to_filename : '') . '.' . $ext)) {
                        $errors[] = Tools::displayError('An error occurred during the image upload');
                    }
                    if (!count($errors)) {
                        $obj->{$key}[$language['id_lang']] = $language['iso_code'] . '/' . $obj->id . ($add_to_filename ? $add_to_filename : '') . '.' . $ext;
                        $update = true;
                    }
                }
            }
        }
        if (count($errors)) {
            return $errors;
        }
        return $update;
    }
    private static function getAllSubCategories($id_cat, $id_lang, $all_sub_categories = [])
    {
        $category = new Category((int)$id_cat);
        $sub_cats = $category->getSubcategories($id_lang);
        if (count($sub_cats) > 0) {
            foreach ($sub_cats as $sub_cat) {
                $all_sub_categories[] = $sub_cat['id_category'];
                self::getAllSubCategories($sub_cat['id_category'], $id_lang, $all_sub_categories);
            }
        }
        return $all_sub_categories;
    }
    public static function getChildrenWithNbSelectedSubCat($id_parent, $selectedCat, $id_lang)
    {
        $selectedCat = explode(',', str_replace(' ', '', $selectedCat));
        if (!is_array($selectedCat)) {
            $selectedCat = [];
        }
        return Db::getInstance()->ExecuteS('
                SELECT c.`id_category`, c.`level_depth`, CONCAT(cl.`name`, " (ID: ", c.`id_category`, ")") as `name`, IF((
                SELECT COUNT(*)
                FROM `' . _DB_PREFIX_ . 'category` c2
                WHERE c2.`id_parent` = c.`id_category`
        ) > 0, 1, 0) AS has_children, (
                    SELECT count(c3.`id_category`)
                    FROM `' . _DB_PREFIX_ . 'category` c3
                    WHERE c3.`nleft` > c.`nleft`
                    AND c3.`nright` < c.`nright`
                    AND c3.`id_category`  IN (' . implode(',', array_map('intval', $selectedCat)) . ')
                ) AS nbSelectedSubCat
                FROM `' . _DB_PREFIX_ . 'category` c
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
                ' . Shop::addSqlAssociation('category', 'cl') . '
                WHERE `id_lang` = ' . (int)$id_lang . '
                AND c.`id_parent` = ' . (int)$id_parent . '
                ORDER BY category_shop.`position` ASC');
    }
    protected function loadCssJsLibrary($library)
    {
        $return = '';
        switch ($library) {
            case 'core':
                $vars = [
                    'baseConfigUrl' => $this->baseConfigUrl,
                    'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
                    'modulePath' => $this->_path,
                    'id_language' => $this->defaultLanguage,
                    'baseAdminDir' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/',
                    'baseDir' => __PS_BASE_URI__ . '/',
                    'iso_user' => $this->context->language->iso_code,
                    'lang_is_rtl' => (int)$this->context->language->is_rtl,
                ];
                $return .= $this->fetchTemplate('core/library.tpl', $vars);
                $this->context->controller->addJqueryUI([
                    'ui.draggable',
                    'ui.droppable',
                    'ui.sortable',
                    'ui.widget',
                    'ui.dialog',
                    'ui.tabs',
                    'ui.progressbar',
                ], '../../../../modules/' . $this->name . '/views/css/jquery-ui-theme');
                $this->context->controller->addJS($this->_path . 'views/js/adminCore.js');
                $this->context->controller->addJS($this->_path . 'views/js/admin.js');
                $this->context->controller->addCSS($this->_path . 'views/css/adminCore.css');
                $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
                break;
            case 'jgrowl':
                $this->context->controller->addJS($this->_path . 'views/js/jGrowl/jquery.jgrowl_minimized.js');
                $this->context->controller->addCSS($this->_path . 'views/css/jGrowl/jquery.jgrowl.css');
                break;
            case 'multiselect':
                $this->context->controller->addJS($this->_path . 'views/js/multiselect/jquery.tmpl.1.1.1.js');
                $this->context->controller->addJS($this->_path . 'views/js/multiselect/jquery.blockUI.js');
                $this->context->controller->addJS($this->_path . 'views/js/multiselect/ui.multiselect.js');
                $this->context->controller->addCSS($this->_path . 'views/css/multiselect/ui.multiselect.css');
                break;
            case 'colorpicker':
                $this->context->controller->addJqueryPlugin('colorpicker');
                break;
            case 'codemirrorcore':
                $this->context->controller->addJS($this->_path . 'views/js/codemirror/codemirror.js');
                $this->context->controller->addCSS($this->_path . 'views/css/codemirror/codemirror.css');
                $this->context->controller->addCSS($this->_path . 'views/css/codemirror/default.css');
                break;
            case 'codemirrorcss':
                $this->context->controller->addJS($this->_path . 'views/js/codemirror/css.js');
                break;
            case 'datatables':
                $this->context->controller->addJS($this->_path . 'views/js/datatables/jquery.dataTables.min.js');
                $this->context->controller->addCSS($this->_path . 'views/css/datatables/demo_table_jui.css');
                break;
            case 'tiny_mce':
                $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tinymce.inc.js');
                $this->context->controller->addJS(__PS_BASE_URI__ . 'js/admin/tinymce.inc.js');
                $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js');
                break;
            case 'chosen':
                $this->context->controller->addJqueryPlugin('chosen');
                break;
            case 'plupload':
                $this->context->controller->addJS($this->_path . 'views/js/plupload.full.min.js');
                break;
            case 'form':
                $this->context->controller->addJS($this->_path . 'views/js/jquery.form.js');
                break;
            case 'selectize':
                $this->context->controller->addJS($this->_path . 'views/js/selectize/selectize.min.js');
                $this->context->controller->addCSS($this->_path . 'views/css/selectize/selectize.css');
                break;
        }
        return $return;
    }
    protected function loadCssJsLibraries()
    {
        $return = '';
        if (self::isFilledArray($this->cssJsToLoad)) {
            foreach ($this->cssJsToLoad as $library) {
                $return .= $this->loadCssJsLibrary($library);
            }
        }
        return $return;
    }
    protected function includeHTMLAtEnd()
    {
        $return = '';
        if ($this->initTinyMceAtEnd) {
            $return .= $this->initTinyMce();
        }
        if ($this->initColorPickerAtEnd) {
            $return .= $this->initColorPicker();
        }
        $return .= $this->displayJsTags('open', true);
        $return .= '$(\'.hideAfterLoad\').hide();';
        $return .= $this->displayJsTags('close', true);
        return $return;
    }
    public function addButton($configOptions)
    {
        $defaultOptions = [
            'text' => '',
            'href' => '',
            'title' => '',
            'onclick' => false,
            'icon_class' => false,
            'class' => false,
            'rel' => false,
            'target' => false,
            'id' => false,
        ];
        $configOptions = $this->parseOptions($defaultOptions, $configOptions);
        if (!$configOptions['id']) {
            $curId = 'button_' . uniqid(self::$modulePrefix . mt_rand());
        } else {
            $curId = $configOptions['id'];
        }
        $vars = [
            'currentId' => $curId,
        ];
        return $this->fetchTemplate('core/components/button.tpl', $vars, $configOptions);
    }
    public function displaySubmit($value, $name)
    {
        $vars = [
            'value' => $value,
            'name' => $name,
        ];
        return $this->fetchTemplate('core/components/submit_button.tpl', $vars);
    }
    protected function headerIframe()
    {
        $return = '';
        $assets = ['css' => [], 'js' => []];
        $backupHtml = $this->html;
        $inline = $this->loadCssJsLibraries();
        foreach ($this->context->controller->css_files as $cssUri => $media) {
            if (!preg_match('/gamification/i', $cssUri)) {
                $assets['css'][] = ['uri' => $cssUri, 'media' => $media];
            }
        }
        foreach ($this->context->controller->js_files as $jsUri) {
            if (!preg_match('#gamification|notifications\.js|help\.js#i', $jsUri)) {
                $assets['js'][] = ['uri' => $jsUri];
            }
        }
        $return = $backupHtml;
        $vars = [
            'ps_version' => Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2),
            'ps_version_full' => str_replace('.', '', _PS_VERSION_),
            'assets' => $assets,
            'inline' => $inline,
        ];
        $return .= $this->fetchTemplate('core/iframe/header.tpl', $vars);
        $return .= $inline;
        return $return;
    }
    protected function footerIframe()
    {
        $vars = [
            'html_at_end' => $this->includeHTMLAtEnd(),
        ];
        return $this->fetchTemplate('core/iframe/footer.tpl', $vars);
    }
    protected function initTinyMce()
    {
        $vars = [
            'init_tinymce_iso' => (Tools::file_exists_cache(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $this->isoLang . '.js') ? $this->isoLang : 'en'),
            'init_tinymce_ad' => dirname($_SERVER['PHP_SELF']),
            'init_tinymce_css_dir' => _THEME_CSS_DIR_,
        ];
        return $this->fetchTemplate('core/init_tinymce.tpl', $vars);
    }
    protected function initColorPicker()
    {
        return $this->fetchTemplate('core/init_color_picker.tpl');
    }
    protected function copyFromPost(&$destination, $data = false)
    {
        $clearTempDirectory = false;
        if (!$data) {
            $data = Tools::getAllValues();
        }
        foreach ($data as $key => $value) {
            if (preg_match('/_temp_file$/', $key) && $value) {
                $final_destination = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $destination::UPLOAD_PATH;
                $final_file = $final_destination . basename($value);
                $temp_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir . basename($value);
                if (self::isRealFile($temp_file)) {
                    rename($temp_file, $final_file);
                }
                $key = preg_replace('/_temp_file$/', '', $key);
                if ($old_file = basename(Tools::getValue($key . '_old_file'))) {
                    if (self::isRealFile($final_destination . basename(Tools::getValue($key . '_old_file')))) {
                        @unlink($final_destination . basename(Tools::getValue($key . '_old_file')));
                    }
                }
                $clearTempDirectory = true;
            } elseif (preg_match('/_unlink$/', $key)) {
                $key = preg_replace('/_unlink$/', '', $key);
                $final_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $destination::UPLOAD_PATH . basename(Tools::getValue($key . '_temp_file'));
                $temp_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir . basename(Tools::getValue($key . '_temp_file'));
                if (self::isRealFile($final_file)) {
                    @unlink($final_file);
                }
                if (self::isRealFile($temp_file)) {
                    @unlink($temp_file);
                }
                $value = '';
                $clearTempDirectory = true;
            } elseif (preg_match('/activestatus/', $key)) {
                $key = 'active';
            }
            if (is_object($destination) && property_exists($destination, $key)) {
                $destination->{$key} = $value;
            }
        }
        $fields = ObjectModel::getDefinition($destination)['fields'];
        $fieldsLang = array_filter($fields, function ($value) {
            return !empty($value['lang']);
        });
        if (count($fieldsLang)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($fieldsLang) as $field) {
                    if ((isset($data[$field . '_' . (int)$language['id_lang'] . '_temp_file_lang'])
                    && $data[$field . '_' . (int)$language['id_lang'] . '_temp_file_lang'])
                    || (isset($data[$field . '_all_lang']) && !$destination->{$field}[(int)$language['id_lang']]
                    && $data[$field . '_all_lang']
                    && isset($data[$field . '_' . (int)$this->defaultLanguage . '_temp_file_lang'])
                    && $data[$field . '_' . (int)$this->defaultLanguage . '_temp_file_lang'])) {
                        $new_temp_file_lang = null;
                        $old_file = null;
                        if (isset($data[$field . '_all_lang'])
                        && $data[$field . '_all_lang']
                        && $language['id_lang'] != $this->defaultLanguage) {
                            $key_default_language = $field . '_' . (int)$this->defaultLanguage . '_temp_file_lang';
                            $old_file = $data[$key_default_language];
                            $new_temp_file_lang = uniqid(self::$modulePrefix . mt_rand()) . '.' . self::getFileExtension($data[$key_default_language]);
                        }
                        $key = $field . '_' . (int)$language['id_lang'] . '_temp_file_lang';
                        $final_destination = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $destination::UPLOAD_PATH;
                        if (isset($data[$field . '_all_lang']) && $data[$field . '_all_lang'] && $language['id_lang'] != $this->defaultLanguage) {
                            $final_file = $final_destination . $new_temp_file_lang;
                            $temp_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir . basename($old_file);
                        } else {
                            $final_file = $final_destination . basename(Tools::getValue($key));
                            $temp_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir . basename(Tools::getValue($key));
                        }
                        if (self::isRealFile($temp_file)) {
                            copy($temp_file, $final_file);
                        }
                        $key = preg_replace('/_temp_file_lang$/', '', $key);
                        if ($old_file = basename(Tools::getValue($key . '_old_file_lang'))) {
                            if (self::isRealFile($final_destination . basename(Tools::getValue($key . '_old_file_lang')))) {
                                @unlink($final_destination . basename(Tools::getValue($key . '_old_file_lang')));
                            }
                        }
                        if (isset($data[$field . '_all_lang'])
                        && $data[$field . '_all_lang']
                        && $language['id_lang'] != $this->defaultLanguage) {
                            $destination->{$field}[(int)$language['id_lang']] = $new_temp_file_lang;
                        } else {
                            $destination->{$field}[(int)$language['id_lang']] = basename(Tools::getValue($field . '_' . (int)$language['id_lang'] . '_temp_file_lang'));
                        }
                        $clearTempDirectory = true;
                    }
                    if (Tools::getIsset($field . '_' . (int)$language['id_lang'] . '_unlink_lang') && Tools::getValue($field . '_' . (int)$language['id_lang'] . '_unlink_lang')) {
                        $key = $field . '_' . (int)$language['id_lang'] . '_unlink_lang';
                        $key = preg_replace('/_unlink_lang$/', '', $key);
                        $final_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $destination::UPLOAD_PATH . basename(Tools::getValue($key . '_old_file_lang'));
                        $temp_file = _PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir . basename(Tools::getValue($key . '_old_file_lang'));
                        if (self::isRealFile($final_file)) {
                            @unlink($final_file);
                        }
                        if (self::isRealFile($temp_file)) {
                            @unlink($temp_file);
                        }
                        $destination->{$field}[(int)$language['id_lang']] = '';
                        $clearTempDirectory = true;
                    }
                    if (Tools::getIsset($field . '_' . (int)$language['id_lang'])) {
                        $destination->{$field}[(int)$language['id_lang']] = Tools::getValue($field . '_' . (int)$language['id_lang']);
                    }
                }
            }
        }
        if ($clearTempDirectory) {
            $this->clearDirectory(_PS_MODULE_DIR_ . DIRECTORY_SEPARATOR . $this->name . $this->tempUploadDir);
        }
    }
    public static function isFilledArray($array)
    {
        return $array && is_array($array) && count($array);
    }
    public static function shuffleArray(&$a)
    {
        if (is_array($a) && count($a)) {
            $ks = array_keys($a);
            shuffle($ks);
            $new = [];
            foreach ($ks as $k) {
                $new[$k] = $a[$k];
            }
            $a = $new;
            return true;
        }
        return false;
    }
    public static function getDataSerialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_encode', [$data]);
        }
        return current(array_map($type . '_encode', [$data]));
    }
    public static function getDataUnserialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_decode', [$data]);
        }
        return current(array_map($type . '_decode', [$data]));
    }
    protected function cleanOutput()
    {
        $this->html = '';
        $this->cleanBuffer();
    }
    protected function cleanBuffer()
    {
        if (ob_get_length() > 0) {
            ob_clean();
        }
    }
    protected function echoOutput($die = false)
    {
        echo $this->html;
        if ($die) {
            die;
        }
    }
    protected function clearDirectory($dir)
    {
        if (!$dh = @opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..' || $obj == 'index.php') {
                continue;
            }
            if (!@unlink($dir . '/' . $obj)) {
                $this->clearDirectory($dir . '/' . $obj);
            }
        }
        closedir($dh);
    }
    public static function isRealFile($filename)
    {
        return Tools::file_exists_cache($filename) && !is_dir($filename);
    }
    public function getTplPath($tpl_name, $view = 'hook')
    {
        return $this->getTemplatePath('views/templates/' . $view . '/' . $this->getPrestaShopTemplateVersion() . '/' . $tpl_name);
    }
    protected function getKeyForLanguageFlags()
    {
        return uniqid(self::$modulePrefix . mt_rand());
    }
    protected function displayPMFlags($key = false, $class = false)
    {
        if (!$key) {
            $key = $this->getKeyForLanguageFlags();
        }
        $vars = [
            'flag_key' => $key,
            'class' => $class,
        ];
        $return = $this->fetchTemplate('core/flags.tpl', $vars);
        return $return;
    }
    protected static function retroValidateController($obj)
    {
        $error_field = '';
        $error_field_lang = '';
        try {
            $error_field = $obj->validateFields(false, true);
        } catch (Exception $e) {
        }
        if ($error_field !== true) {
            return [
                $error_field,
            ];
        }
        try {
            $error_field_lang = $obj->validateFieldsLang(false, true);
        } catch (Exception $e) {
        }
        if ($error_field_lang !== true) {
            return [
                $error_field_lang,
            ];
        }
        return [];
    }
    public static function pregQuoteSql($str)
    {
        return preg_replace('#([.\+*?^$()\[\]{}=!<>|:-])#', '\\\\\\\\\\\${1}', $str);
    }
    public static function changeTimeLimit($time)
    {
        if (!ini_get('safe_mode')) {
            if (function_exists('set_time_limit') && (ini_get('max_execution_time') < $time || $time === 0)) {
                set_time_limit($time);
            }
        }
    }
    protected static function getNbDaysModuleUsage()
    {
        $sql = 'SELECT DATEDIFF(NOW(),date_add)
                FROM ' . _DB_PREFIX_ . 'configuration
                WHERE name = \'' . pSQL('PM_' . self::$modulePrefix . '_LAST_VERSION') . '\'
                ORDER BY date_add ASC';
        return (int)Db::getInstance()->getValue($sql);
    }
    protected function getModuleConfiguration()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $allShopConfig = Configuration::getMultiShopValues('PM_' . self::$modulePrefix . '_CONF');
            if (!isset($allShopConfig[(int)$this->context->shop->id])) {
                $oldConf = Configuration::get('PM_' . self::$modulePrefix . '_CONF');
                if (!empty($oldConf)) {
                    $oldConf = json_decode($oldConf, true);
                    if ($oldConf != false) {
                        Configuration::updateValue('PM_' . self::$modulePrefix . '_CONF', json_encode($oldConf));
                    }
                } else {
                    Configuration::updateValue('PM_' . self::$modulePrefix . '_CONF', json_encode($this->defaultConfiguration));
                }
            }
            $conf = Configuration::get('PM_' . self::$modulePrefix . '_CONF');
            return json_decode((string)$conf, true);
        }
        return $this->defaultConfiguration;
    }
    public static function getModuleConfigurationStatic($idShop = null)
    {
        $conf = Configuration::get('PM_' . self::$modulePrefix . '_CONF', null, null, $idShop);
        if (!empty($conf)) {
            return json_decode($conf, true);
        } else {
            return [];
        }
    }
    protected function setModuleConfiguration($newConf)
    {
        Configuration::updateValue('PM_' . self::$modulePrefix . '_CONF', json_encode($newConf));
    }
    protected function setDefaultConfiguration()
    {
        if (!is_array($this->getModuleConfiguration()) || !count($this->getModuleConfiguration())) {
            Configuration::updateValue('PM_' . self::$modulePrefix . '_CONF', json_encode($this->defaultConfiguration));
        }
        return true;
    }
    public function getCurrentCustomerGroupId()
    {
        $id_group = (int)Configuration::get('PS_UNIDENTIFIED_GROUP');
        if (Validate::isLoadedObject($this->context->customer)) {
            $id_group = (int)$this->context->customer->id_default_group;
        }
        return $id_group;
    }
    public static function getSmartyVarValue($varName)
    {
        $smarty = Context::getContext()->smarty;
        if (method_exists($smarty, 'getTemplateVars')) {
            return $smarty->getTemplateVars($varName);
        } elseif (method_exists($smarty, 'get_template_vars')) {
            return $smarty->get_template_vars($varName);
        }
        return false;
    }
    protected function onBackOffice()
    {
        if (isset($this->context->cookie->id_employee) && Validate::isUnsignedId($this->context->cookie->id_employee)) {
            return true;
        }
        return false;
    }
    public static function arrayMapRecursive($fn, $arr)
    {
        if (!is_array($arr)) {
            return [];
        }
        $rarr = [];
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $rarr[$k] = self::arrayMapRecursive($fn, $v);
                if (!count($rarr[$k])) {
                    unset($rarr[$k]);
                }
            } else {
                if (preg_match('#~#', $v)) {
                    $interval = explode('~', $v);
                } else {
                    $interval = null;
                }
                if ($interval != null && is_array($interval) && count($interval) == 2) {
                    $isValidInterval = true;
                    foreach ($interval as $kInterval => $intervalValue) {
                        if ($kInterval == 1 && $intervalValue == '' && is_numeric($interval[0])) {
                            continue;
                        } elseif (!is_numeric($intervalValue)) {
                            $isValidInterval = false;
                            break;
                        }
                    }
                    if ($isValidInterval) {
                        $rarr[$k] = $v;
                    }
                } else {
                    $rarr[$k] = call_user_func($fn, $v);
                    if ($rarr[$k] == 0) {
                        unset($rarr[$k]);
                    }
                }
            }
        }
        return $rarr;
    }
    protected static function getCustomModuleTranslation($name, $string, $language)
    {
        static $translationCache = [];
        $cacheKey = md5($name . $string . $language->id);
        if (isset($translationCache[$cacheKey])) {
            return $translationCache[$cacheKey];
        }
        $translationsStrings = [];
        $files_by_priority = [
            _PS_THEME_DIR_ . 'modules/' . $name . '/translations/' . $language->iso_code . '.php',
            _PS_THEME_DIR_ . 'modules/' . $name . '/' . $language->iso_code . '.php',
            _PS_MODULE_DIR_ . $name . '/translations/' . $language->iso_code . '.php',
            _PS_MODULE_DIR_ . $name . '/' . $language->iso_code . '.php',
        ];
        foreach ($files_by_priority as $file) {
            if (Tools::file_exists_cache($file)) {
                include $file;
                if (!empty($translationsStrings)) {
                    $translationsStrings = $translationsStrings;
                    if (isset($_MODULE)) {
                        $translationsStrings += $_MODULE;
                    }
                } else {
                    if (isset($_MODULE)) {
                        $translationsStrings = $_MODULE;
                    }
                }
            }
        }
        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5((string)$string);
        $currentKey = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $name . '_' . $key);
        $defaultKey = Tools::strtolower('<{' . $name . '}prestashop>' . $name . '_' . $key);
        if (isset($translationsStrings[$currentKey])) {
            $ret = Tools::stripslashes($translationsStrings[$currentKey]);
        } elseif (isset($translationsStrings[$defaultKey])) {
            $ret = Tools::stripslashes($translationsStrings[$defaultKey]);
        } else {
            $ret = $string;
        }
        $translationCache[$cacheKey] = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
        return $translationCache[$cacheKey];
    }
    public function smartyNoFilterModifier($s)
    {
        return $s;
    }
    protected function registerFrontSmartyObjects()
    {
        static $registeredFO = false;
        if (!$registeredFO && !empty($this->context->smarty)) {
            $this->context->smarty->unregisterPlugin('modifier', self::$modulePrefix . '_nofilter');
            $this->context->smarty->registerPlugin('modifier', self::$modulePrefix . '_nofilter', [$this, 'smartyNoFilterModifier']);
            $registeredFO = true;
        }
    }
    protected function registerSmartyObjects()
    {
        static $registered = false;
        if (!$registered && !empty($this->context->smarty)) {
            $this->registerFrontSmartyObjects();
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_startForm', [$this, 'startForm']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_endForm', [$this, 'endForm']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_startFieldset', [$this, 'startFieldset']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_endFieldset', [$this, 'endFieldset']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_button', [$this, 'addButton']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_ajaxSelectMultiple', [$this, 'displayAjaxSelectMultiple']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_categoryTree', [$this, 'displayCategoryTree']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputActive', [$this, 'displayInputActive']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputColor', [$this, 'displayInputColor']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputText', [$this, 'displayInputText']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputTextLang', [$this, 'displayInputTextLang']);
            if ($this->supportsImageCriterionGroup()) {
                $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputFileLang', [$this, 'displayInputFileLang']);
                $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inlineUploadFile', [$this, 'displayInlineUploadFile']);
            } else {
                $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inputFileLang', function () {
                });
                $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_inlineUploadFile', function () {
                });
            }
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_richTextareaLang', [$this, 'displayRichTextareaLang']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_select', [$this, 'displaySelect']);
            $this->context->smarty->registerPlugin('function', self::$modulePrefix . '_showWarning', [$this, 'showWarning']);
            $this->context->smarty->registerObject('module', $this, [
                'showWarning',
                'showInfo',
                'displayTitle',
                'displaySubTitle',
                'displaySubmit',
                'displaySupport',
            ], false);
            $registered = true;
        }
    }
    protected function fetchTemplate($tpl, $customVars = [], $configOptions = [])
    {
        $this->registerSmartyObjects();
        $this->context->smarty->assign([
            'ps_version_full' => str_replace('.', '', _PS_VERSION_),
            'ps_major_version' => Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2),
            'psVersionForTemplates' => $this->getPrestaShopTemplateVersion(),
            'module_name' => $this->name,
            'module_path' => $this->_path,
            'base_config_url' => $this->baseConfigUrl,
            'current_iso_lang' => $this->isoLang,
            'current_id_lang' => (int)$this->context->language->id,
            'default_language' => $this->defaultLanguage,
            'languages' => $this->languages,
            'options' => $configOptions,
        ]);
        if (count($customVars)) {
            $this->context->smarty->assign($customVars);
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . $tpl);
    }
    protected function formatPrice($price, $currency = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            return Tools::displayPrice($price, $currency);
        }
        $context = Context::getContext();
        if (empty($currency)) {
            $currency = $context->currency;
        } elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }
        $locale = Tools::getContextLocale($context);
        $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;
        return $locale->formatPrice($price, $currencyCode);
    }
    public static function decodeCriteria($inputToDecode)
    {
        $decoded = json_decode((string)$inputToDecode, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            try {
                return self::decodeAlternative((string)$inputToDecode);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        return $decoded;
    }
    public static function encodeCriteria($inputToEncode)
    {
        return json_encode($inputToEncode);
    }
    protected static function decodeAlternative($inputToDecode, $type = 'dW5zZXJpYWxpemU=')
    {
        return @call_user_func_array(self::getDataUnserialized($type), [
            $inputToDecode,
            [
                'allowed_classes' => false,
            ],
        ]);
    }
    public function getPrestaShopTemplateVersion()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            return Tools::substr(_PS_VERSION_, 0, 1);
        }
        return Tools::substr(_PS_VERSION_, 0, 3);
    }
}
