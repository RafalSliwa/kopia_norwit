<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class CustomBanners extends Module
{
    public static $errors = [];

    public function __construct()
    {
        if (!defined('_PS_VERSION_')) {
            exit;
        }
        $this->name = 'custombanners';
        $this->tab = 'front_office_features';
        $this->version = '3.0.1';
        $this->ps_versions_compliancy = ['min' => '1.6.0.4', 'max' => _PS_VERSION_];
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '89d38a87bea7e9c6b04e6c77b9cef1cf';
        parent::__construct();
        $this->displayName = $this->l('Custom HTML, Banners and Sliders');
        $this->description = $this->l('Images, Sliders/Carousels, Videos, custom HTML/CSS/JS anywhere on your shop');
        $this->img_dir = $this->_path . 'views/img/uploads/';
        $this->img_dir_local = $this->local_path . 'views/img/uploads/';
        $this->db = Db::getInstance();
        $this->is_16 = Tools::substr(_PS_VERSION_, 0, 3) === '1.6';
        $this->empty_date = '0000-00-00 00:00:00';
        $this->img_fields = ['img', 'img_hover'];
    }

    public function install()
    {
        return parent::install()
            && $this->prepareDatabase()
            && $this->sliderLibrary('install')
            && $this->img()->optimizer('install')
            && $this->data()->prepareDemoContent($this->id)
            && $this->registerHook('displayHeader');
    }

    public function prepareDatabase()
    {
        $sql = [];
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . $this->sqlTable() . ' (
            id_banner int(10) unsigned NOT NULL AUTO_INCREMENT,
            hook_name varchar(64) NOT NULL,
            id_wrapper int(10) unsigned NOT NULL,
            position int(10) NOT NULL,
            active tinyint(1) NOT NULL,
            active_tablet tinyint(1) NOT NULL,
            active_mobile tinyint(1) NOT NULL,
            publish_from datetime NOT NULL,
            publish_to datetime NOT NULL,
            exceptions text NOT NULL,
            css_class varchar(64) NOT NULL,
            label varchar(64) NOT NULL,
            PRIMARY KEY (id_banner),
            KEY hook_name (hook_name),
            KEY active (active),
            KEY active_tablet (active_tablet),
            KEY active_mobile (active_mobile),
            KEY publish_from (publish_from),
            KEY publish_to (publish_to)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
        ';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . $this->sqlTable('_lang') . ' (
            id_banner int(10) unsigned NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            id_lang int(10) unsigned NOT NULL,
            content text NOT NULL,
            PRIMARY KEY (id_banner, id_shop, id_lang)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
        ';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . $this->sqlTable('_wrapper_settings') . ' (
            id_wrapper int(10) unsigned NOT NULL AUTO_INCREMENT,
            general text NOT NULL,
            carousel text NOT NULL,
            PRIMARY KEY (id_wrapper)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
        ';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . $this->sqlTable('_hook_settings') . ' (
            hook_name varchar(64) NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            exc_type tinyint(1) NOT NULL DEFAULT 1 ,
            exc_controllers text NOT NULL,
            PRIMARY KEY (hook_name, id_shop)
          ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
        ';

        return $this->runSql($sql);
    }

    public function uninstall()
    {
        $sql = [
            'DROP TABLE IF EXISTS ' . $this->sqlTable(),
            'DROP TABLE IF EXISTS ' . $this->sqlTable('_lang'),
            'DROP TABLE IF EXISTS ' . $this->sqlTable('_wrapper_settings'),
            'DROP TABLE IF EXISTS ' . $this->sqlTable('_hook_settings'),
        ];

        return $this->runSql($sql)
            && $this->img()->deleteAll()
            && $this->img()->optimizer('uninstall')
            && $this->sliderLibrary('uninstall')
            && parent::uninstall();
    }

    public function runSql($sql)
    {
        foreach ($sql as $s) {
            if (!$this->db->execute($s)) {
                return false;
            }
        }

        return true;
    }

    public function getColumns($type)
    {
        $columns = [];
        switch ($type) {
            case 'main':
                $columns = ['id_banner', 'hook_name', 'id_wrapper', 'position', 'active', 'active_tablet',
                    'active_mobile', 'publish_from', 'publish_to', 'exceptions', 'css_class', 'label'];
                break;
            case 'lang':
                $columns = ['id_banner', 'id_shop', 'id_lang', 'content'];
        }

        return $columns;
    }

    public function getAvailableHooks()
    {
        $methods = get_class_methods(__CLASS__);
        $methods_to_exclude = ['hookDisplayHeader' => 0];
        if (!$this->is_16) {
            $methods_to_exclude['hookDisplayMyAccountBlockFooter'] = 0;
            $methods_to_exclude['hookDisplayNav'] = 0;
            $methods_to_exclude['hookDisplayPayment'] = 0;
            $methods_to_exclude['hookDisplayProductButtons'] = 0; // alias for displayProductAdditionalInfo
            $methods_to_exclude['hookDisplayProductComparison'] = 0;
            $methods_to_exclude['hookDisplayProductTab'] = 0;
            $methods_to_exclude['hookDisplayProductTabContent'] = 0;
            $methods_to_exclude['hookDisplayTopColumn'] = 0;
        } else {
            $methods_to_exclude['hookDisplayAfterBodyOpeningTag'] = 0;
            $methods_to_exclude['hookDisplayAfterProductThumbs'] = 0;
            $methods_to_exclude['hookDisplayBeforeBodyClosingTag'] = 0;
            $methods_to_exclude['hookDisplayCustomerLoginFormAfter'] = 0;
            $methods_to_exclude['hookDisplayFooterAfter'] = 0;
            $methods_to_exclude['hookDisplayFooterBefore'] = 0;
            $methods_to_exclude['hookDisplayNav1'] = 0;
            $methods_to_exclude['hookDisplayNav2'] = 0;
            $methods_to_exclude['hookDisplayNavFullWidth'] = 0;
            $methods_to_exclude['hookDisplayProductAdditionalInfo'] = 0;
            $methods_to_exclude['hookDisplayReassurance'] = 0;
            $methods_to_exclude['hookDisplayWrapperBottom'] = 0;
            $methods_to_exclude['hookDisplayWrapperTop'] = 0;
        }
        $available_hooks = [];
        foreach ($methods as $m) {
            if (Tools::substr($m, 0, 11) === 'hookDisplay' && !isset($methods_to_exclude[$m])) {
                $available_hooks[str_replace('hookDisplay', 'display', $m)] = 0;
            }
        }
        ksort($available_hooks);

        return $available_hooks;
    }

    public function prepaceMCEContentCSS()
    {
        $mce_content_css = $this->is_16 ? _THEME_CSS_DIR_ . 'global.css' : _THEME_CSS_DIR_ . 'theme.css';
        $mce_content_css .= ', ' . $this->_path . 'views/css/mce.css';
        $extra_css_files = [
            _PS_THEME_DIR_ . 'assets/css/custom.css',
            $this->customCode('getFilePath', ['type' => 'css']),
        ];
        foreach ($extra_css_files as $file) {
            if (file_exists($file)) {
                $mce_content_css .= ', ' . strstr($file, $this->_path);
            }
        }

        return $mce_content_css;
    }

    public function getContent()
    {
        $this->failed_txt = $this->l('Failed');
        $this->saved_txt = $this->l('Saved');
        if ($action = Tools::getValue('action')) {
            if (Tools::getValue('ajax')) {
                $action_method = 'ajax' . $action;
                if (method_exists($this, $action_method) && is_callable([$this, $action_method])) {
                    $this->$action_method();
                }

                return;
            }
            if ($action == 'exportBannersData') {
                $this->data()->export();
            }
        }
        $v = '?v=' . $this->version;
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJQueryUI('ui.datetimepicker');
        $this->context->controller->js_files[] = $this->_path . 'views/js/back.js' . $v;
        $this->context->controller->css_files[$this->_path . 'views/css/back.css' . $v] = 'all';
        $this->context->controller->css_files[$this->_path . 'views/css/common-classes.css' . $v] = 'all';
        if (Module::isEnabled('ps_edition_basic')) {
            $this->context->controller->css_files[$this->_path . 'views/css/ps-edition-basic.css' . $v] = 'all';
        }
        $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js');
        if (file_exists(_PS_ROOT_DIR_ . '/js/admin/tinymce.inc.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/admin/tinymce.inc.js');
        } else { // retro-compatibility
            $this->context->controller->addJS(__PS_BASE_URI__ . 'js/tinymce.inc.js');
        }

        return $this->displayForm();
    }

    private function displayForm()
    {
        $sorted_hooks = $this->getAvailableHooks();
        $sorted_banners = [];
        foreach ($this->getBannersDataMultilang() as $b) {
            if (isset($sorted_hooks[$b['hook_name']])) {
                ++$sorted_hooks[$b['hook_name']];
                $sorted_banners[$b['hook_name']][$b['id_wrapper']][$b['id_banner']] = $b;
            }
        }
        $used_hooks = array_filter($sorted_hooks);
        arsort($used_hooks);
        $sorted_hooks = $used_hooks + $sorted_hooks;
        $import_success_param = 'import_success';
        $iso = $this->context->language->iso_code;
        $this->retro()->checkMagicQuotes();
        $this->context->smarty->assign([
            'js_vars' => [
                'hooks_by_name' => array_keys($sorted_hooks),
                'iso' => file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en',
                'mce_content_css' => $this->prepaceMCEContentCSS(),
                'ad' => dirname($_SERVER['PHP_SELF']),
                'importSuccessParam' => $import_success_param,
                'importConfirmationHTML' => !Tools::getValue($import_success_param) ? '' :
                $this->displayConfirmation(htmlspecialchars_decode($this->l('Data was successfully imported'))),
                'cb_txt' => array_map('htmlspecialchars_decode', [
                    'failed' => $this->failed_txt,
                    'saved' => $this->saved_txt,
                    'areYouSure' => $this->l('Are you sure?'),
                ]),
                'cb_is_16' => $this->is_16,
            ],
            'banners' => $sorted_banners,
            'hooks' => $sorted_hooks,
            'iso_lang_current' => $iso,
            'files_update_warnings' => $this->getFilesUpdadeWarnings(),
            'optimization_data' => $this->img()->optimizer('getAllData'),
            'custom_code' => $this->customCode('get'),
            'slider_library' => [
                'data' => $this->sliderLibrary('getData'),
                'options' => $this->sliderLibrary('getOptions'),
            ],
            'info_links' => [
                'changelog' => $this->_path . 'Readme.md?v=' . $this->version,
                'documentation' => $this->_path . 'readme_en.pdf?v=' . $this->version,
                'contact' => 'https://addons.prestashop.com/en/contact-us?id_product=19404',
                'modules' => 'https://addons.prestashop.com/en/2_community-developer?contributor=64815',
            ],
            'cb_errors' => self::$errors,
            'cb' => $this,
        ] + $this->commonVarsForBannerForm());

        return $this->display($this->local_path, 'views/templates/admin/configure.tpl');
    }

    public function ajaxGetOriginalCustomCode()
    {
        $code = $this->customCode('getDefault', ['type' => Tools::getValue('type')]);
        exit(json_encode(['original_code' => $code]));
    }

    public function ajaxSaveCustomCode()
    {
        $params = ['type' => Tools::getValue('type'), 'code' => Tools::getValue('code')];
        if ($this->customCode('save', $params)) {
            exit(json_encode(['successText' => $this->saved_txt]));
        }
    }

    public function customCode($action, $params = [])
    {
        $ret = true;
        switch ($action) {
            case 'get':
                $ret = array_fill_keys($this->customCode('getTypes'), '');
                foreach ($ret as $type => &$code) {
                    $path = $this->customCode('getFilePath', ['type' => $type]);
                    if (file_exists($path)) {
                        $code = Tools::file_get_contents($path);
                    }
                }
                if (isset($params['type'])) {
                    $ret = isset($ret[$params['type']]) ? $ret[$params['type']] : '';
                }
                break;
            case 'getDefault':
                $ret = '';
                if ($demo_file_path = $this->data()->getDemoFilePath()) {
                    $type = $params['type'];
                    if ($dir = $this->data()->extractZipToTemporaryDirectory($demo_file_path)) {
                        $possible_files = ['custom.' . $type, $type . '/shopid_shop_default.' . $type]; // retro
                        foreach ($possible_files as $file_path) {
                            $file_path = $dir . $file_path;
                            if (file_exists($file_path)) {
                                $ret = Tools::file_get_contents($file_path);
                                break;
                            }
                        }
                    }
                    $this->recursiveRemove($dir);
                }
                break;
            case 'save':
                if (in_array($params['type'], $this->customCode('getTypes'))) {
                    $file_path = $this->customCode('getFilePath', $params);
                    if ($code = rtrim($params['code'])) {
                        $ret = file_put_contents($file_path, $code . PHP_EOL);  // add last empty line to r-trimmed code
                    } elseif (file_exists($file_path)) {
                        $ret = unlink($file_path);
                    }
                    Media::clearCache();
                }
                break;
            case 'getFilePath':
                $ret = $this->local_path . 'views/' . $params['type'] . '/custom.' . $params['type'];
                break;
            case 'getTypes':
                $ret = ['css', 'js'];
                break;
        }

        return $ret;
    }

    public function ajaxUpdateSliderLibrary()
    {
        $params = ['type' => Tools::getValue('type'), 'load' => Tools::getValue('load')];
        if ($this->sliderLibrary('updateData', $params)) {
            exit(json_encode(['successText' => $this->saved_txt]));
        }
    }

    public function sliderLibrary($action, $params = [])
    {
        $ret = true;
        switch ($action) {
            case 'load':
                if (!isset($this->context->slider_lib_loaded)) {
                    $this->context->slider_lib_loaded = [];
                }
                $lib = $this->sliderLibrary('getData');
                if ($lib['load'] && !isset($this->context->slider_lib_loaded[$lib['type']])) {
                    if ($lib['type'] == 'bx') {
                        $this->context->controller->addJqueryPlugin('bxslider');
                    } else {
                        $this->addJS('lib/' . $lib['type'] . '.js');
                        $this->addCSS('lib/' . $lib['type'] . '.css');
                    }
                    $this->context->slider_lib_loaded[$lib['type']] = 1;
                }
                $this->addJS('slider.js');
                $this->sliderLibrary('loadAdapters', $lib);
                break;
            case 'loadAdapters':
                if ($params['type'] != 'swiper11') {
                    $this->addCSS('adapter/' . $params['type'] . '-adapter.css');
                    $this->addJS('adapter/' . $params['type'] . '-adapter.js');
                }
                break;
            case 'getData':
                if (!isset($this->slider_lib_data)) {
                    $this->slider_lib_data = json_decode(Configuration::get('CB_SLIDER_LIB'), true);
                }
                $ret = $this->slider_lib_data;
                break;
            case 'detectExternal':
                $ret = [];
                foreach (['easycarousels', 'amazzingblog'] as $m) {
                    if (Module::isEnabled($m) && $m = Module::getInstanceByName($m)) {
                        if (method_exists($m, 'sliderLibrary')) {
                            $ret = $m->sliderLibrary('getData');
                            $ret['load'] = !$ret['load'];
                            break;
                        }
                    }
                }
                break;
            case 'install':
                $ret = $this->sliderLibrary('updateData', $this->sliderLibrary('detectExternal'));
                break;
            case 'updateData':
                $available_options = $this->sliderLibrary('getOptions');
                $data = [
                    'type' => isset($params['type']) && isset($available_options[$params['type']])
                        ? $params['type'] : current(array_keys($available_options)),
                    'load' => isset($params['load']) ? (int) $params['load'] : 1,
                ];
                $ret = Configuration::updateGlobalValue('CB_SLIDER_LIB', json_encode($data));
                break;
            case 'uninstall':
                $ret = Configuration::deleteByName('CB_SLIDER_LIB');
                break;
            case 'getOptions':
                $ret = [
                    'swiper11' => 'Swiper 11 (' . $this->l('recommended') . ')',
                    'swiper5' => 'Swiper 5',
                    'swiper4' => 'Swiper 4',
                    'swiper3' => 'Swiper 3',
                    'bx' => 'BxSlider',
                ];
                break;
        }

        return $ret;
    }

    public function ajaxSaveOptimizer()
    {
        exit(json_encode($this->img()->optimizer('save', [
            'id' => Tools::getValue('o_identifier'),
            'settings' => Tools::getValue('o_settings', []),
        ])));
    }

    public function ajaxRegenerateThumbs()
    {
        $ret = ['complete' => false];
        if (!$ret['params'] = Tools::getValue('params')) { // first call
            $ret['params'] = [
                'to_process' => $this->img()->getAllImages(true),
                'processed' => [],
                'o' => ['b_orig' => 0, 'b_optm' => 0],
            ];
        }
        if ($ret['params']['to_process']) {
            $img_name = basename(array_shift($ret['params']['to_process']));
            $ret['params']['processed'][] = (int) $this->img()->optimizer('process', ['img_name' => $img_name]);
            $ret['params']['o']['b_orig'] += (int) filesize($this->img()->getPath($img_name, true));
            $ret['params']['o']['b_optm'] += (int) filesize($this->img()->getPath($img_name));
            $ret['diff_formatted'] = $this->img()->formatBytes(
                max([0, $ret['params']['o']['b_orig'] - $ret['params']['o']['b_optm']])
            );
            $ret['diff_formatted'] .= ' (' . $this->img()->getCompressionRate(
                $ret['params']['o']['b_orig'],
                $ret['params']['o']['b_optm']
            ) . '%)';
        } else {
            $ret['complete'] = true;
            $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
        }
        exit(json_encode($ret));
    }

    public function getFilesUpdadeWarnings()
    {
        $warnings = $customizable_layout_files = [];
        $locations = [
            '/css/' => 'css',
            '/js/' => 'js',
            '/templates/admin/' => 'tpl',
            '/templates/hook/' => 'tpl',
            '/templates/front/' => 'tpl',
        ];
        foreach ($locations as $loc => $ext) {
            $loc = 'views' . $loc;
            $files = glob($this->local_path . $loc . '*.' . $ext);
            foreach ($files as $file) {
                $customizable_layout_files[] = '/' . $loc . basename($file);
            }
        }
        foreach ($customizable_layout_files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (!$this->is_16) {
                $customized_file_path = _PS_THEME_DIR_ . 'modules/' . $this->name . $file;
            } else {
                $customized_file_path = _PS_THEME_DIR_ . ($ext != 'tpl' ? $ext . '/' : '') .
                'modules/' . $this->name . $file;
            }
            if (file_exists($customized_file_path)) {
                $original_file_path = $this->local_path . $file;
                $original_rows = file($original_file_path);
                $original_identifier = trim(array_pop($original_rows));
                $customized_rows = file($customized_file_path);
                $customized_identifier = trim(array_pop($customized_rows));
                if ($original_identifier != $customized_identifier) {
                    $warnings[$file] = $original_identifier;
                }
            }
        }

        return $warnings;
    }

    public function getBannerFields()
    {
        $fields = [
            'ac_title' => [
                'label' => $this->l('Accordion title'),
                'type' => 'text',
                'group_class' => 'w-4-element',
                'multilang' => 1,
            ],
            'img' => [
                'label' => $this->l('Image'),
                'type' => 'img',
                'subfields' => $this->img()->getConfigurableFields(),
                'multilang' => 1,
            ],
            'img_hover' => [
                'label' => $this->l('Hover image'),
                'tooltip' => $this->l('Displayed on hover'),
                'type' => 'img',
                'group_class' => 'img-field',
                'multilang' => 1,
            ],
            'link' => [
                'label' => $this->l('Image link'),
                'type' => 'link',
                'selector' => [
                    'custom' => $this->l('Custom link'),
                    'Product' => $this->l('Link to product'),
                    'Category' => $this->l('Link to Category'),
                    'Manufacturer' => $this->l('Link to Manufacturer'),
                    'Supplier' => $this->l('Link to Supplier'),
                    'CMS' => $this->l('Link to CMS page'),
                    'CMSCategory' => $this->l('Link to CMS category'),
                ],
                'group_class' => 'img-field',
                'multilang' => 1,
            ],
            'html' => [
                'label' => $this->l('HTML'),
                'type' => 'html',
                'multilang' => 1,
            ],
            'css_class' => [
                'label' => $this->l('CSS class'),
                'type' => 'text',
            ],
            'exceptions' => [
                'label' => $this->l('Display banner'),
                'type' => 'exceptions',
                'selectors' => [
                    'page' => $this->getPageExceptionsOptions(),
                    'customer' => [
                        '0' => $this->l('For all customers'),
                        'group' => $this->l('Only for selected customer groups'),
                        'customer' => $this->l('Only for selected customers'),
                    ],
                ],
            ],
            'publish_from' => [
                'label' => $this->l('Start publication'),
                'type' => 'date',
            ],
            'publish_to' => [
                'label' => $this->l('End publication'),
                'type' => 'date',
            ],
        ];

        return $fields;
    }

    public function getPageExceptionsOptions()
    {
        $pages = [
            'product' => $this->l('product'),
            'category' => $this->l('category'),
            'manufacturer' => $this->l('manufacturer'),
            'supplier' => $this->l('supplier'),
            'cms' => $this->l('cms'),
        ];
        $options = ['0' => $this->l('On all available pages')];
        foreach ($pages as $k => $page) {
            $options[$k . '_all'] = sprintf($this->l('On all %s pages'), $page);
            $options[$k] = sprintf($this->l('On selected %s pages'), $page);
            if ($k == 'product') {
                $options['product_category'] = $this->l('On product pages inside selected categories');
                $options['product_manufacturer'] = $this->l('On product pages of selected manufacturers');
            } elseif ($k == 'category') {
                $options['subcategory'] = $this->l('On all subcategories of selected categories');
            }
        }

        return $options;
    }

    public function deviceType($action, $params = [])
    {
        $result = [];
        switch ($action) {
            case 'getAvailable':
                $result = ['desktop' => 'active', 'tablet' => 'active_tablet', 'mobile' => 'active_mobile'];
                break;
            case 'getActiveKey':
                if (!isset($this->context->cookie->cb_device_key)) {
                    $device = $this->context->getDevice();
                    if ($device == Context::DEVICE_MOBILE) {
                        $key = '_mobile';
                    } elseif ($device == Context::DEVICE_TABLET) {
                        $key = '_tablet';
                    } else {
                        $key = '';
                    }
                    $this->context->cookie->__set('cb_device_key', $key);
                }
                $result = $this->context->cookie->cb_device_key;
                break;
        }

        return $result;
    }

    public function getBSClasses()
    {
        return ['lg' => '1199', 'md' => '991', 'sm' => '767', 'xs' => '479', 'xxs' => '480'];
    }

    public function ajaxImportBannersData()
    {
        if ($this->data()->import()) {
            $ret = ['success' => true];
        } else {
            $ret = ['errors' => $this->l('An error occured while importing data')];
        }
        exit(json_encode($ret));
    }

    public function updateAllWrappersSettings()
    {
        $saved_wrapper_settings = $this->db->executeS('
            SELECT * FROM ' . $this->sqlTable('_wrapper_settings') . '
        ');
        $standard_settings_fields = [
            'general' => $this->getWrapperSettingsFields(false, 'general'),
            'carousel' => $this->getWrapperSettingsFields(false, 'carousel'),
        ];
        $updated_rows = [];
        foreach ($saved_wrapper_settings as $row) {
            foreach ($standard_settings_fields as $type => $standard_fields) {
                $row[$type] = json_decode($row[$type], true);
                foreach ($standard_fields as $name => $field) {
                    if (!isset($row[$type][$name])) {
                        $row[$type][$name] = $field['value'];
                    }
                }
                $row[$type] = json_encode($row[$type]);
            }
            $updated_rows[] = '(\'' . implode('\', \'', array_map('pSQL', $row)) . '\')';
        }

        return $this->db->execute('
            REPLACE INTO ' . $this->sqlTable('_wrapper_settings') . '
            VALUES ' . implode(', ', $updated_rows) . '
        ');
    }

    public function clearFilesAndSetError($error)
    {
        $this->recursiveRemove($this->local_path . 'tmp/', true);
        if (Tools::isSubmit('ajax')) {
            $this->throwError($error);
        }
        $this->context->controller->errors[] = $error;

        return false;
    }

    public function recursiveRemove($dir, $top_level = false, $files_to_keep = [])
    {
        $removed = true;
        $files_to_keep_ = $files_to_keep;
        if ($top_level) {
            $files_to_keep[] = 'index.php';
        }
        $structure = glob(rtrim($dir, '/') . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                if (!in_array(basename($file), $files_to_keep)) {
                    if (is_dir($file)) {
                        $top_level_ = $file . '/' == $this->img()->getPath('', true); // /img/uploads/orig/
                        $removed &= $this->recursiveRemove($file, $top_level_, $files_to_keep_);
                    } elseif (is_file($file)) {
                        $removed &= unlink($file);
                    }
                }
            }
        }
        if (!$top_level) {
            $removed &= rmdir($dir);
        }

        return $removed;
    }

    public function formatIDs($ids, $return_string = true)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        $ids = array_map('intval', $ids);
        $ids = array_combine($ids, $ids);
        unset($ids[0]);

        return $return_string ? implode(',', $ids) : $ids;
    }

    public function shopIDs($return_string = false)
    {
        if (!isset($this->shop_ids)) {
            $this->shop_ids = Shop::getContextListShopID();
        }

        return $this->formatIDs($this->shop_ids, $return_string);
    }

    public function sqlIDs($ids)
    {
        return $this->formatIDs($ids, true);
    }

    public function sqlTable($suffix = '')
    {
        return '`' . _DB_PREFIX_ . 'cb' . bqSQL($suffix) . '`';
    }

    public function getBannersRowsFromDB($id_banner = false, $shop_ids = [])
    {
        $now = date('Y-m-d H:i:s');
        $shop_ids_ = $shop_ids ? $this->sqlIDs($shop_ids) : $this->shopIDs(true);
        $data = $this->db->executeS('
            SELECT main.*, lang.id_lang, lang.id_shop, lang.content,
            DATEDIFF(main.publish_from, \'' . pSQL($now) . '\') AS days_before_publish,
            DATEDIFF(\'' . pSQL($now) . '\', main.publish_to) AS days_expired
            FROM ' . $this->sqlTable() . ' main
            LEFT JOIN ' . $this->sqlTable('_lang') . ' lang
                ON lang.id_banner = main.id_banner
            WHERE 1 ' . ($id_banner ? ' AND main.id_banner = ' . (int) $id_banner : '') . '
            ' . ($shop_ids_ ? ' AND lang.id_shop IN (' . $shop_ids_ . ')' : '') . '
            ORDER BY lang.id_shop = ' . (int) $this->context->shop->id . ' DESC, position, id_wrapper
        ');

        return $data;
    }

    public function getBannersDataMultilang($id_banner = false, $full = false)
    {
        $banners = $already_included = [];
        foreach ($this->getBannersRowsFromDB($id_banner) as $b) {
            $id = $b['id_banner'];
            if (!$b['content'] || isset($already_included[$id][$b['id_lang']])) {
                continue;
            }
            $already_included[$id][$b['id_lang']] = 1;
            foreach (['publish_from', 'publish_to'] as $key) {
                $b[$key] = $b[$key] == $this->empty_date ? '' : $b[$key];
            }
            $content = json_decode($b['content'], true);
            if ($full) {
                $this->img()->includeOptimizationInfo($content);
                foreach ($content as $name => $value) {
                    $banners[$id]['content'][$name][$b['id_lang']] = $value;
                }
            }
            if ($b['id_lang'] == $this->context->language->id) {
                $b['label'] = $b['label'] ?: sprintf($this->l('Banner %d'), $id);
                if (!empty($content['img']['name'])) {
                    $b['img_preview'] = $this->getBannerImgSrc($content['img']['name']);
                } elseif (!empty($content['html'])) {
                    $b['html_preview'] = 1;
                }
                foreach ($b as $name => $value) {
                    if ($name != 'content') {
                        if ($name == 'exceptions') {
                            $value = $value ? json_decode($value, true) : [];
                            $banners[$id]['exc_note'] = $this->getExcNote($value);
                        }
                        $banners[$id][$name] = $value;
                    }
                }
            }
        }

        return $banners;
    }

    public function getExcNote($exceptions_data)
    {
        $exceptions = [];
        if (!empty($exceptions_data['page']['type'])) {
            $exceptions[] = $this->l('on selected pages');
        }
        if (!empty($exceptions_data['customer']['type'])) {
            $exceptions[] = $this->l('for selected customers');
        }

        return $exceptions ? sprintf($this->l('Displayed %s'), implode('/', $exceptions)) : '';
    }

    public function getBannerImgSrc($img_name)
    {
        $src = '';
        if ($img_name != '' && file_exists($this->img_dir_local . $img_name)) {
            $src = $this->img_dir . $img_name;
        }

        return $src;
    }

    public function callBannerForm($id_banner, $full = false)
    {
        if (!$banner_data = $id_banner ? current($this->getBannersDataMultilang($id_banner, $full)) : []) {
            $banner_data = $this->getEmptyBannerData();
        }
        $this->context->smarty->assign(['banner' => $banner_data] + $this->commonVarsForBannerForm($full));

        return $this->display($this->local_path, 'views/templates/admin/banner-form.tpl');
    }

    public function getEmptyBannerData()
    {
        return array_merge(array_fill_keys($this->getColumns('main'), ''), [
            'hook_name' => Tools::getValue('hook_name'),
            'id_wrapper' => Tools::getValue('id_wrapper'),
            'active' => 1,
            'active_tablet' => 1,
            'active_mobile' => 1,
            'days_before_publish' => 0,
            'days_expired' => 0,
            'exceptions' => [],
            'content' => [],
        ]);
    }

    public function commonVarsForBannerForm($full = false)
    {
        $vars = [
            'device_types' => array_reverse($this->deviceType('getAvailable')), // mobile first
            'bs_classes' => $this->getBSClasses(),
        ];
        if ($vars['full'] = $full) {
            $vars += [
                'input_fields' => $this->getBannerFields(),
                'languages' => Language::getLanguages(false),
                'id_lang_current' => $this->context->language->id,
                'multishop_note' => count($this->shopIDs()) > 1,
                'cb' => $this,
            ];
        }

        return $vars;
    }

    public function ajaxCallSettingsForm()
    {
        $hook_name = Tools::getValue('hook_name');
        $settings_type = Tools::getValue('settings_type');
        $method = 'getHook' . Tools::ucfirst($settings_type) . 'Settings';
        if (!is_callable([$this, $method])) {
            $this->throwError($this->l('This type of settings is not supported'));
        }
        $this->context->smarty->assign([
            'settings' => $this->$method($hook_name),
            'settings_type' => $settings_type,
            'hook_name' => $hook_name,
        ]);
        $form_html = $this->display($this->local_path, 'views/templates/admin/hook-' . $settings_type . '-form.tpl');
        $ret = ['form_html' => $form_html];
        exit(json_encode($ret));
    }

    public function getHookExceptionsSettings($hook_name)
    {
        $exc_data = $this->db->executeS('
            SELECT exc_type, exc_controllers
            FROM ' . $this->sqlTable('_hook_settings') . '
            WHERE hook_name = \'' . pSQL($hook_name) . '\'
            AND id_shop IN (' . $this->shopIDs(true) . ')
        ');
        $type = 0;
        $current_exceptions = [];
        foreach ($exc_data as $row) {
            if (!$type || $row['id_shop'] == $this->context->controller->id_shop) {
                $type = $row['exc_type'];
            }
            $current_exceptions += explode(',', $row['exc_controllers']);
        }
        $current_exceptions = array_flip($current_exceptions);
        $sorted_exceptions = [
            'core' => ['group_name' => $this->l('Core pages'), 'values' => []],
            'modules' => ['group_name' => $this->l('Module pages'), 'values' => []],
        ];
        $front_controllers = array_keys(Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_));
        $retro_compatibility = ['auth' => 'authentication', 'compare' => 'productscomparison'];
        foreach ($front_controllers as $fc) {
            $fc = isset($retro_compatibility[$fc]) ? $retro_compatibility[$fc] : $fc;
            $sorted_exceptions['core']['values'][$fc] = (int) isset($current_exceptions[$fc]);
        }
        $module_front_controllers = Dispatcher::getModuleControllers('front');
        foreach ($module_front_controllers as $module_name => $controllers) {
            foreach ($controllers as $controller_name) {
                $key = 'module-' . $module_name . '-' . $controller_name;
                $sorted_exceptions['modules']['values'][$key] = (int) isset($current_exceptions[$key]);
            }
        }

        return ['type' => $type, 'exceptions' => $sorted_exceptions];
    }

    public function getHookPositionsSettings($hook_name)
    {
        $hook_modules = Hook::getModulesFromHook(Hook::getIdByName($hook_name));
        $sorted = [];
        foreach ($hook_modules as $m) {
            if ($instance = Module::getInstanceByName($m['name'])) {
                $logo_src = false;
                if (file_exists(_PS_MODULE_DIR_ . $instance->name . '/logo.png')) {
                    $logo_src = _MODULE_DIR_ . $instance->name . '/logo.png';
                }
                $sorted[$m['id_module']] = [
                    'name' => $instance->name,
                    'position' => $m['m.position'],
                    'enabled' => $instance->isEnabledForShopContext(),
                    'display_name' => $instance->displayName,
                    'description' => $instance->description,
                    'logo_src' => $logo_src,
                ];
                if ($m['id_module'] == $this->id) {
                    $sorted[$m['id_module']]['current'] = 1;
                }
            }
        }

        return $sorted;
    }

    public function ajaxSaveHookSettings()
    {
        $hook_name = Tools::getValue('hook_name');
        $id_hook = Hook::getIdByName($hook_name);
        $settings_type = Tools::getValue('settings_type');
        $saved = false;
        if ($settings_type == 'exceptions') {
            $exc_type = Tools::getValue('exceptions_type');
            $exc_controllers = Tools::getValue('exceptions', []);
            foreach ($exc_controllers as $c) {
                if (!Validate::isControllerName($c)) {
                    $this->throwError('Incorrect controller name');
                }
            }
            $rows = [];
            foreach ($this->shopIDs() as $id_shop) {
                $rows[] = '(\'' . pSQL($hook_name) . '\', ' . (int) $id_shop . ', '
                    . (int) $exc_type . ', \'' . implode(',', array_map('pSQL', $exc_controllers)) . '\')';
            }
            $saved = $this->db->execute('
                INSERT INTO ' . $this->sqlTable('_hook_settings') . '
                (hook_name, id_shop, exc_type, exc_controllers)
                VALUES ' . implode(', ', $rows) . '
                ON DUPLICATE KEY UPDATE
                exc_type = VALUES(exc_type),
                exc_controllers = VALUES(exc_controllers)
            ');
        } elseif ($settings_type == 'position') {
            $id_module = Tools::getValue('id_module');
            $new_position = Tools::getValue('new_position');
            $way = Tools::getValue('way');
            if ($module = Module::getInstanceById($id_module)) {
                $saved = $module->updatePosition($id_hook, $way, $new_position);
            }
        }
        $ret = ['saved' => $saved];
        exit(json_encode($ret));
    }

    public function ajaxProcessModule()
    {
        $id_module = Tools::getValue('id_module');
        $hook_name = Tools::getValue('hook_name');
        $act = Tools::getValue('act');
        $module = Module::getInstanceById($id_module);
        $saved = false;
        if (Validate::isLoadedObject($module)) {
            switch ($act) {
                case 'disable':
                    $module->disable();
                    $saved = !$module->isEnabledForShopContext();
                    break;
                case 'unhook':
                    $saved = $module->unregisterHook(Hook::getIdByName($hook_name), $this->shopIDs());
                    break;
                case 'uninstall':
                    if ($id_module != $this->id) {
                        $saved = $module->uninstall();
                    }
                    break;
                case 'enable':
                    $saved = $module->enable();
                    break;
            }
        }
        $ret = ['saved' => $saved];
        exit(json_encode($ret));
    }

    public function ajaxAddWrapper()
    {
        $this->deleteUnusedWrappers();
        $id_wrapper = $this->addWrapper();
        $ret = ['wrapper_html' => $this->callWrapperForm($id_wrapper)];
        exit(json_encode($ret));
    }

    public function callWrapperForm($id_wrapper)
    {
        $this->context->smarty->assign([
            'id_wrapper' => $id_wrapper,
            'banners' => [],
            'cb' => $this,
        ]);

        return $this->display($this->local_path, 'views/templates/admin/wrapper-form.tpl');
    }

    public function addWrapper()
    {
        $settings = [];
        $fields = [
            'general' => $this->getWrapperSettingsFields(),
            'carousel' => $this->getWrapperSettingsFields(false, 'carousel'),
        ];
        foreach ($fields as $k => $f) {
            foreach ($f as $name => $field) {
                $settings[$k][$name] = $field['value'];
            }
            $settings[$k] = json_encode($settings[$k]);
        }
        $added = $this->db->execute('
            INSERT INTO ' . $this->sqlTable('_wrapper_settings') . '
            VALUES (0, \'' . pSQL($settings['general']) . '\', \'' . pSQL($settings['carousel']) . '\')
        ');

        return $added ? $this->db->insert_ID() : false;
    }

    public function ajaxDeleteWrapper()
    {
        $id_wrapper = Tools::getValue('id_wrapper');
        $ret = ['deleted' => $this->deleteWrapper($id_wrapper)];
        exit(json_encode($ret));
    }

    public function deleteWrapper($id_wrapper)
    {
        $deleted = $this->db->execute('
            DELETE FROM ' . $this->sqlTable('_wrapper_settings') . ' WHERE id_wrapper = ' . (int) $id_wrapper
        );

        return $deleted;
    }

    public function deleteUnusedWrappers()
    {
        $wrappers_data = $this->db->executeS('
            SELECT cb.id_banner, w.id_wrapper
            FROM ' . $this->sqlTable('_wrapper_settings') . ' w
            LEFT JOIN ' . $this->sqlTable() . ' cb
                ON cb.id_wrapper = w.id_wrapper
        ');
        $to_delete = [];
        foreach ($wrappers_data as $w) {
            if (!$w['id_banner']) {
                $to_delete[] = $w['id_wrapper'];
            }
        }
        if ($to_delete) {
            $this->db->execute('
                DELETE FROM ' . $this->sqlTable('_wrapper_settings') . '
                WHERE id_wrapper IN (' . implode(', ', array_map('intval', $to_delete)) . ')
            ');
        }
    }

    public function getWrapperSettingsFields($id_wrapper = false, $settings_type = 'general')
    {
        $fields = [];
        switch ($settings_type) {
            case 'general':
                $fields = [
                    'custom_class' => [
                        'label' => $this->l('Wrapper class'),
                        'value' => '',
                        'validate' => 'isLabel',
                        'type' => 'text',
                    ],
                    'display_type' => [
                        'label' => $this->l('Display type'),
                        'value' => 1,
                        'validate' => 'isInt',
                        'type' => 'select',
                        'options' => [
                            1 => $this->l('Regular'),
                            2 => $this->l('Carousel'),
                            4 => $this->l('Accordion'),
                            3 => $this->l('Random banner'),
                        ],
                        'input_class' => 'display-type',
                    ],
                ];
                break;
            case 'carousel':
                $pn_options = [
                    0 => $this->l('Hide'),
                    1 => $this->l('Show'),
                    2 => $this->l('Show on hover'),
                ];
                $fields = [
                    'p' => [
                        'label' => $this->l('Pagination'),
                        'value' => 0,
                        'type' => 'select',
                        'options' => $pn_options,
                    ],
                    'n' => [
                        'label' => $this->l('Navigation arrows'),
                        'value' => 1,
                        'type' => 'select',
                        'options' => $pn_options,
                    ],
                    'a' => [
                        'label' => $this->l('Autoplay'),
                        'value' => 1,
                        'type' => 'switcher',
                    ],
                    'ah' => [
                        'label' => $this->l('Pause autoplay on hover'),
                        'value' => 1,
                        'type' => 'switcher',
                    ],
                    'ps' => [
                        'label' => $this->l('Autoplay interval'),
                        'tooltip' => $this->l('Time between each auto transition'),
                        'value' => 4000,
                        'type' => 'text',
                        'input_suffix' => 'ms',
                    ],
                    's' => [
                        'label' => $this->l('Animation speed'),
                        'value' => 250,
                        'type' => 'text',
                    ],
                    'l' => [
                        'label' => $this->l('Loop'),
                        'value' => 1,
                        'type' => 'switcher',
                    ],
                    't' => [
                        'label' => $this->l('Ticker mode'),
                        'value' => 0,
                        'type' => 'switcher',
                    ],
                    'm' => [
                        'label' => $this->l('Items moved per transition'),
                        'tooltip' => $this->l('Set 0 to move all visible items'),
                        'value' => 1,
                        'type' => 'text',
                    ],
                    'sb' => [
                        'label' => $this->l('Space between items'),
                        'value' => 0,
                        'type' => 'text',
                        'input_suffix' => 'px',
                    ],
                ];
                foreach ($this->getResolutions() as $res => $r) {
                    $fields[$r[0]] = [
                        'label' => !$res ? $this->l('Visible items (default)')
                            : sprintf($this->l('Visible items on displays < %spx'), $res),
                        'value' => $r[1],
                        'type' => 'text',
                        'min' => 1,
                    ];
                }
                foreach ($fields as &$f) {
                    $f['validate'] = 'isInt';
                }
                if ($this->sliderLibrary('getData')['type'] != 'bx') {
                    $fields['t']['class'] = 'hidden'; // ticker mode available only in bxslider
                }
                break;
        }
        if ($id_wrapper) {
            $saved_data = $this->db->getValue('
                SELECT `' . bqSQL($settings_type) . '` FROM ' . $this->sqlTable('_wrapper_settings') . '
                WHERE id_wrapper = ' . (int) $id_wrapper . '
            ');
            $saved_data = json_decode($saved_data, true);
            foreach (array_keys($fields) as $name) {
                if (isset($saved_data[$name])) {
                    $fields[$name]['value'] = $saved_data[$name];
                }
            }
        }

        return $fields;
    }

    public function getResolutions()
    {
        return [0 => ['i', 5], 1200 => ['i_1200', 4], 992 => ['i_992', 3], 768 => ['i_768', 2], 480 => ['i_480', 1]];
    }

    public function ajaxSaveWrapperSettings()
    {
        $id_wrapper = Tools::getValue('id_wrapper');
        $type = Tools::getValue('settings_type');
        $settings = Tools::getValue('settings');
        exit(json_encode(['saved' => $this->saveWrapperSettings($id_wrapper, $type, $settings, true)]));
    }

    public function saveWrapperSettings($id, $type, $settings, $throw_error = false)
    {
        $settings_to_save = [];
        $fields = $this->getWrapperSettingsFields($id, $type);
        foreach ($fields as $name => $field) {
            $settings_to_save[$name] = isset($settings[$name]) ? $settings[$name] : $field['value'];
        }
        $errors = $this->validateSettings($settings_to_save, $fields); // incorrect values are updated to default
        if ($errors && $throw_error) {
            foreach (array_keys($errors) as $field_name) {
                $errors[$field_name] = sprintf($this->l('Incorrect value for "%s"'), $fields[$field_name]['label']);
            }
            $this->throwError($errors);
        }

        return $this->db->execute('
            INSERT INTO ' . $this->sqlTable('_wrapper_settings') . ' (`id_wrapper`, `' . bqSQL($type) . '`)
            VALUES (' . (int) $id . ', \'' . pSQL(json_encode($settings_to_save)) . '\')
            ON DUPLICATE KEY UPDATE `' . bqSQL($type) . '` = VALUES(`' . bqSQL($type) . '`)
        ');
    }

    public function ajaxCallBannerForm()
    {
        $id_banner = (int) Tools::getValue('id_banner');
        $full = Tools::getValue('full');
        $ret = ['banner_form_html' => $this->callBannerForm($id_banner, $full)];
        exit(json_encode($ret));
    }

    public function ajaxCopyToAnotherHook()
    {
        $id_banner = Tools::getValue('id_banner');
        $to_hook = Tools::getValue('to_hook');
        if (!$id_banner || !$to_hook) {
            $this->throwError($this->l('Error'));
        }
        $delete_original = Tools::getValue('delete_original');
        $append_to_wrapper_id = $this->db->getValue('
            SELECT id_wrapper FROM ' . $this->sqlTable() . '
            WHERE hook_name = \'' . pSQL($to_hook) . '\'
            ORDER BY position DESC
        ');
        if (!$append_to_wrapper_id) {
            $append_to_wrapper_id = $this->addWrapper();
        }
        $new_banner_id = $this->copyToAnotherHook($id_banner, $to_hook, $append_to_wrapper_id, $delete_original);
        $ret = [
            'append_to_wrapper_id' => $append_to_wrapper_id,
            'new_wrapper_form' => $this->callWrapperForm($append_to_wrapper_id),
            'new_banner_form' => $new_banner_id ? $this->callBannerForm($new_banner_id) : false,
            'reponseText' => isset($this->response_text) ? $this->response_text : $this->l('Failed'),
        ];
        if (!$delete_original) {
            $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
        }
        exit(json_encode($ret));
    }

    public function copyToAnotherHook($id_banner, $to_hook, $new_wrapper_id, $delete_original = false)
    {
        $main_row = $this->db->getRow('
            SELECT * FROM ' . $this->sqlTable() . ' WHERE id_banner = ' . (int) $id_banner . '
        ');
        $main_row = $this->fillArray($main_row, [
            'id_banner' => 0,
            'hook_name' => $to_hook,
            'id_wrapper' => $new_wrapper_id,
            'position' => $this->getNextPosition($to_hook),
        ]);
        $copied = $this->db->execute('
            REPLACE INTO ' . $this->sqlTable() . '
            VALUES (\'' . implode('\', \'', array_map('pSQL', $main_row)) . '\')
        ');
        if ($copied && $new_id = $this->db->Insert_ID()) {
            $lang_rows = $this->db->executeS('
                SELECT * FROM ' . $this->sqlTable('_lang') . ' WHERE id_banner = ' . (int) $id_banner . '
            ');
            foreach ($lang_rows as $k => $row) {
                $row['id_banner'] = $new_id;
                $row['content'] = $this->img()->prepareContentForDuplication($row['content']);
                foreach ($row as $key => $value) {
                    $row[$key] = $key == 'content' ? '\'' . pSQL($value, true) . '\'' : (int) $value;
                }
                $lang_rows[$k] = '(' . implode(', ', $row) . ')';
            }
            $copied &= $lang_rows && $this->db->execute(
                'REPLACE INTO ' . $this->sqlTable('_lang') . ' VALUES ' . implode(', ', $lang_rows) . '
            ');
            if ($copied) {
                $this->response_text = sprintf($this->l('Copied to %s'), $to_hook);
                foreach ($this->shopIDs() as $id_shop) {
                    if (!$this->isRegisteredInHookConsideringShop($to_hook, $id_shop)) {
                        $this->registerHook($to_hook, [$id_shop]);
                    }
                }
                if ($delete_original) {
                    $copied &= $this->deleteBanner($id_banner);
                    $this->response_text = sprintf($this->l('Moved to %s'), $to_hook);
                }
            }
        }

        return $copied ? $new_id : false;
    }

    public function ajaxBulkUpdate()
    {
        $banner_ids = Tools::getValue('ids', []);
        $bulk_action = Tools::getValue('bulk_action');
        $bulk_value = Tools::getValue('bulk_value');
        if (!$banner_ids && !Tools::getValue('no_selection_required')) {
            $this->throwError($this->l('Please make a selection'));
        }
        $ret = ['success' => true];
        if ($bulk_action == 'deleteAll') {
            $banner_ids = array_column($this->db->executeS('
                SELECT id_banner FROM ' . $this->sqlTable() . '
            '), 'id_banner');
            $bulk_action = 'delete';
        }
        switch ($bulk_action) {
            case 'active':
            case 'active_tablet':
            case 'active_mobile':
                $ret['success'] &= $this->db->execute('
                    UPDATE ' . $this->sqlTable() . '
                    SET `' . bqSQL($bulk_action) . '` = ' . (int) $bulk_value . '
                    WHERE id_banner IN (' . $this->sqlIDs($banner_ids) . ')
                ');
                break;
            case 'move':
            case 'copy':
                if ($to_hook = Tools::getValue('to_hook')) {
                    $delete_original = $bulk_action == 'move';
                    $wrapper_id = (int) $this->db->getValue('
                        SELECT id_wrapper FROM ' . $this->sqlTable() . '
                        WHERE hook_name = \'' . pSQL($to_hook) . '\'
                        ORDER BY position DESC
                    ') ?: $this->addWrapper();
                    foreach ($banner_ids as $id_banner) {
                        if ($new_id = $this->copyToAnotherHook($id_banner, $to_hook, $wrapper_id, $delete_original)) {
                            $ret['responseHTML'] .= $this->callBannerForm($new_id);
                        } else {
                            $this->response_text = $this->failed_txt;
                            $ret['success'] = false;
                            break;
                        }
                    }
                    $ret['append_to_wrapper_id'] = $wrapper_id;
                    $ret['new_wrapper_form'] = $this->callWrapperForm($wrapper_id);
                    if (!$delete_original) {
                        $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
                    }
                }
                break;
            case 'delete':
                foreach ($banner_ids as $id_banner) {
                    $ret['success'] &= $this->deleteBanner($id_banner);
                }
                if ($ret['success']) {
                    $ret['successText'] = $this->l('Deleted');
                }
                $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
                break;
            case 'deleteUnusedImages':
                $ret['successText'] = 'Deleted ' . count($this->img()->deleteUnused()) . ' image(s)';
                $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
                break;
        }
        exit(json_encode($ret));
    }

    public function ajaxSaveBannerData()
    {
        $data = Tools::getValue('data');
        if (!$data || empty($data['content'])) {
            $this->throwError($this->l('Please fill in at least one field'));
        }
        $saved_id = $this->saveBannerData($data);
        $ret = ['banner_form_html' => $saved_id ? $this->callBannerForm($saved_id) : false];
        if (!empty($this->upd_image_data)) {
            $ret['upd_img_data'] = $this->img()->optimizer('getAvailableImagesData');
        }
        exit(json_encode($ret));
    }

    public function validateSettings(&$settings, $fields, $upd = true)
    {
        $errors = [];
        foreach ($settings as $key => $value) {
            if (!isset($fields[$key])) {
                unset($settings[$key]);
            } elseif (isset($fields[$key]['required']) && $value == '') {
                $errors[$key] = 'empty';
            } elseif (isset($fields[$key]['validate'])) {
                $validate = $fields[$key]['validate'];
                if (!Validate::$validate($value)) {
                    $errors[$key] = 'incorrect';
                }
                if ($validate == 'isInt') {
                    if ((isset($fields[$key]['max']) && $value > $fields[$key]['max'])
                        || (isset($fields[$key]['min']) && $value < $fields[$key]['min'])) {
                        $errors[$key] = 'out_of_range';
                    }
                }
            }
            if ($upd && isset($errors[$key]) && isset($fields[$key]['value'])) {
                $settings[$key] = $fields[$key]['value'];
            }
        }

        return $errors;
    }

    public function getNextPosition($hook_name)
    {
        return (int) $this->db->getValue('
            SELECT MAX(position) FROM ' . $this->sqlTable() . ' WHERE hook_name = \'' . pSQL($hook_name) . '\'
        ') + 1;
    }

    public function prepareDataForSaving($data, $throw_error = false)
    {
        // main data
        foreach (['publish_from', 'publish_to'] as $key) {
            if (empty($data[$key]) || !Validate::isDate($data[$key])) {
                $data[$key] = $this->empty_date;
            }
        }
        if (!empty($data['exceptions'])) {
            foreach ($data['exceptions'] as $group => $exc) {
                if (empty($exc['type'])) {
                    unset($data['exceptions'][$group]);
                } else {
                    $data['exceptions'][$group]['ids'] = $this->formatIDs($exc['ids']);
                }
            }
            $data['exceptions'] = $data['exceptions'] ? json_encode($data['exceptions']) : '';
        }
        // lang data
        if (!empty($data['content'])) {
            $data['processed_images'] = $this->img()->prepareImgDataForSaving($data['content']);
            $lang_source_content = [];
            foreach (Tools::getValue('lang_source', []) as $name => $id_lang_source) {
                if (isset($data['content'][$id_lang_source][$name])) {
                    $lang_source_content[$name] = $data['content'][$id_lang_source][$name];
                }
            }
            foreach ($data['content'] as $id_lang => $content) {
                $content = array_merge($content, $lang_source_content);
                if (isset($content['link'])) {
                    if ($content['link']['type'] == 'custom') {
                        if (strpos($content['link']['href'], '.') !== false) {
                            $str_before_first_slash = current(explode('/', $content['link']['href']));
                            if (!in_array($str_before_first_slash, ['http:', 'https:', ''])) {
                                $content['link']['href'] = '//' . $content['link']['href'];
                            }
                        } elseif (!$content['link']['href']) {
                            $content['link']['href'] = '#';
                        }
                    } elseif (!Validate::isInt($content['link']['href'])) {
                        $content['link']['href'] = '';
                        if ($throw_error) {
                            $error = $this->l('Please specify a proper ID for the link field (%s)');
                            $this->throwError(sprintf($error, Language::getIsoById($id_lang)));
                        }
                    }
                }
                $data['content'][$id_lang] = json_encode($content);
            }
        }

        return $data;
    }

    public function saveBannerData($data)
    {
        $data = $this->prepareDataForSaving($data, Tools::getValue('ajax'));
        $id_banner = $data['id_banner'];
        $main_row = $lang_rows = [];
        foreach ($this->getColumns('main') as $c_name) {
            $value = isset($data[$c_name]) ? $data[$c_name] : '';
            $main_row[] = '\'' . pSQL($value) . '\'';
        }
        $saved = $this->db->execute('
            REPLACE INTO ' . $this->sqlTable() . ' VALUES (' . implode(', ', $main_row) . ')
        ');
        if ($saved) {
            $lang_array = array_fill_keys($this->getColumns('lang'), '');
            $id_banner = $id_banner ?: $this->db->Insert_ID();
            foreach ($this->shopIDs() as $id_shop) {
                foreach ($data['content'] as $id_lang => $encoded_content) {
                    $row = $this->fillArray($lang_array, [
                        'id_banner' => (int) $id_banner,
                        'id_shop' => (int) $id_shop,
                        'id_lang' => (int) $id_lang,
                        'content' => '\'' . pSQL($encoded_content, true) . '\'',
                    ]);
                    $lang_rows[] = '(' . implode(', ', $row) . ')';
                }
            }
            $saved &= $lang_rows && $this->db->execute('
                REPLACE INTO ' . $this->sqlTable('_lang') . ' VALUES ' . implode(', ', $lang_rows) . '
            ');
            if ($saved) {
                if (!empty($data['processed_images'])) {
                    if ($data['processed_images']['delete_if_unused']) {
                        $this->img()->deleteIfUnused($data['processed_images']['delete_if_unused']);
                    }
                    $this->upd_image_data = array_filter($data['processed_images']);
                }
                foreach ($this->shopIDs() as $id_shop) {
                    if (!$this->isRegisteredInHookConsideringShop($data['hook_name'], $id_shop)) {
                        $this->registerHook($data['hook_name'], [$id_shop]);
                    }
                }
                if (!$data['id_banner']) {
                    $first_position_in_wrapper = $this->db->getValue('
                        SELECT MIN(position) FROM ' . $this->sqlTable() . '
                        WHERE hook_name = \'' . pSQL($data['hook_name']) . '\'
                        AND id_wrapper = ' . (int) $data['id_wrapper'] . '
                        AND id_banner <> ' . (int) $id_banner . '
                    ');
                    $this->db->execute('
                        UPDATE ' . $this->sqlTable() . ' SET position = position + 1
                        WHERE hook_name = \'' . pSQL($data['hook_name']) . '\'
                        AND position >= ' . (int) $first_position_in_wrapper . '
                    ');
                    $this->db->execute('
                        UPDATE ' . $this->sqlTable() . ' SET position = ' . (int) $first_position_in_wrapper . '
                        WHERE id_banner = ' . (int) $id_banner . '
                    ');
                }
            }
        }

        return $saved ? $id_banner : false;
    }

    public function fillArray($main_array, $data)
    {
        return array_merge($main_array, array_intersect_key($data, $main_array));
    }

    public function isRegisteredInHookConsideringShop($hook_name, $id_shop)
    {
        $sql = 'SELECT COUNT(*)
            FROM ' . _DB_PREFIX_ . 'hook_module hm
            LEFT JOIN ' . _DB_PREFIX_ . 'hook h ON (h.id_hook = hm.id_hook)
            WHERE h.name = \'' . pSQL($hook_name) . '\'
            AND hm.id_shop = ' . (int) $id_shop . ' AND hm.id_module = ' . (int) $this->id;

        return $this->db->getValue($sql);
    }

    public function ajaxToggleParam()
    {
        $result = [];
        $id_banner = Tools::getValue('id_banner');
        $param_name = Tools::getValue('param_name');
        if ($id_banner && $param_name) {
            $result['success'] = $this->db->execute('
                UPDATE ' . $this->sqlTable() . '
                SET `' . bqSQL($param_name) . '` = ' . (int) Tools::getValue('param_value') . '
                WHERE `id_banner` = ' . (int) $id_banner . '
            ');
        }
        exit(json_encode($result));
    }

    public function ajaxDeleteBanner()
    {
        $id_banner = Tools::getValue('id_banner');
        $deleted = $this->deleteBanner($id_banner);
        $result = [
            'deleted' => $deleted,
            'successText' => $deleted && isset($this->response_text) ? $this->response_text : '',
            'upd_img_data' => $this->img()->optimizer('getAvailableImagesData'),
        ];
        exit(json_encode($result));
    }

    public function deleteBanner($id_banner)
    {
        $banner_data = $this->db->executeS('
            SELECT cbl.content, cb.hook_name FROM ' . $this->sqlTable() . ' cb
            LEFT JOIN ' . $this->sqlTable('_lang') . ' cbl ON cbl.id_banner = cb.id_banner
            WHERE cbl.id_banner = ' . (int) $id_banner . ' AND cbl.id_shop IN (' . $this->shopIDs(true) . ')
        ');
        $banner_images = $this->img()->extractImageNames($banner_data);
        $hook_name = current(array_column($banner_data, 'hook_name'));
        // first delete data for shops in current context
        if ($deleted = $this->db->execute('
                DELETE FROM ' . $this->sqlTable('_lang') . '
                WHERE id_banner = ' . (int) $id_banner . ' AND id_shop IN (' . $this->shopIDs(true) . ')
        ')) {
            $this->img()->deleteIfUnused($banner_images);
            if (!$this->db->getValue('
                    SELECT id_banner FROM ' . $this->sqlTable('_lang') . '
                    WHERE id_banner = ' . (int) $id_banner
            )) { // if data is not available in any other shop, banner can be completely deleted
                $deleted &= $this->db->execute(
                    'DELETE FROM ' . $this->sqlTable() . ' WHERE id_banner = ' . (int) $id_banner
                );
                $this->response_text = $this->l('Deleted');
            } else {
                $this->response_text = $this->l('Deleted for current shops');
            }
            $shop_ids_where_hook_is_used = array_column($this->db->executeS('
                SELECT DISTINCT(cbl.id_shop) FROM ' . $this->sqlTable('_lang') . ' cbl
                INNER JOIN ' . $this->sqlTable() . ' cb
                    ON cb.id_banner = cbl.id_banner
                    AND cb.hook_name = \'' . pSQL($hook_name) . '\'
            '), 'id_shop', 'id_shop');
            $all_shop_ids = Shop::getShops(false, null, true);
            if ($unhook_for_shop_ids = array_diff($all_shop_ids, $shop_ids_where_hook_is_used)) {
                $this->unregisterHook(Hook::getIdByName($hook_name), $unhook_for_shop_ids);
            }
        }

        return $deleted;
    }

    public function ajaxUpdatePositionsInHook()
    {
        $ordered_ids = Tools::getValue('ordered_ids');
        if (!$ordered_ids) {
            $this->throwError($this->failed_txt);
        }
        if (Tools::getValue('moved_element_is_banner')) {
            $id_wrapper = Tools::getValue('moved_element_wrapper_id');
            $id_banner = Tools::getValue('moved_element_id');
            if ($id_wrapper && $id_banner) {
                $this->db->execute('
                    UPDATE ' . $this->sqlTable() . '
                    SET id_wrapper = ' . (int) $id_wrapper . ' WHERE id_banner = ' . (int) $id_banner
                );
            }
        }
        $upd_rows = [];
        foreach ($ordered_ids as $position => $id_banner) {
            $upd_rows[] = '(' . (int) $id_banner . ', ' . (int) ++$position . ')';
        }
        $sql = '
            INSERT INTO ' . $this->sqlTable() . ' (id_banner, position)
            VALUES ' . implode(', ', $upd_rows) . ' ON DUPLICATE KEY UPDATE position = VALUES(position)
        ';
        $ret = $this->db->execute($sql) ? ['successText' => $this->saved_txt] : [];
        exit(json_encode($ret));
    }

    public function throwError($errors)
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }
        $ret = ['errors' => $this->displayError(implode('<br>', $errors))];
        exit(json_encode($ret));
    }

    public function getFullControllerName()
    {
        if (!isset($this->full_controller_name)) {
            $controller = Tools::getValue('controller');
            if (Tools::getValue('fc') == 'module' && Tools::isSubmit('module')) {
                $controller = 'module-' . Tools::getValue('module') . '-' . $controller;
            }
            $this->full_controller_name = $controller;
        }

        return $this->full_controller_name;
    }

    public function getBannersInHook($hook_name)
    {
        $current_controller = $this->getFullControllerName();
        $current_id = Tools::getValue('id_' . $current_controller);
        $hook_settings = $this->db->getRow('
            SELECT * FROM ' . $this->sqlTable('_hook_settings') . '
            WHERE hook_name = \'' . pSQL($hook_name) . '\'
        ');
        if (!empty($hook_settings['exc_type'])) {
            $type = $hook_settings['exc_type'];
            $controllers = array_flip(explode(',', $hook_settings['exc_controllers']));
            if (($type == 1 && isset($controllers[$current_controller]))
                || ($type == 2 && !isset($controllers[$current_controller]))) {
                return;
            }
        }
        $now = date('Y-m-d H:i:s');
        $banners_db = $this->db->executeS('
            SELECT cb.id_banner, cb.id_wrapper, cb.exceptions, cb.css_class, cbl.content
            FROM ' . $this->sqlTable() . ' cb
            LEFT JOIN ' . $this->sqlTable('_lang') . ' cbl
                ON cbl.id_banner = cb.id_banner
                AND cbl.id_shop = ' . (int) $this->context->shop->id . '
                AND cbl.id_lang = ' . (int) $this->context->language->id . '
            WHERE cb.hook_name = \'' . pSQL($hook_name) . '\'
            AND cb.`active' . bqSQL($this->deviceType('getActiveKey')) . '` = 1
            AND cb.publish_from <= \'' . pSQL($now) . '\'
            AND (cb.publish_to = \'' . pSQL($this->empty_date) . '\' OR cb.publish_to >= \'' . pSQL($now) . '\')
            ORDER BY cb.position ASC
        ');
        $sorted = [];
        foreach ($banners_db as $b) {
            $content = json_decode($b['content'], true);
            if (!$content || (!empty($b['exceptions'])
                && !$this->allowedForDisplay($b['exceptions'], $current_controller, $current_id))) {
                continue;
            }
            foreach ($this->img_fields as $img_field) {
                if (!empty($content[$img_field]['name'])) {
                    $content[$img_field]['src'] = $this->getBannerImgSrc($content[$img_field]['name']);
                }
            }
            if (isset($content['link']['type']) && $content['link']['type'] != 'custom') {
                $get_link_method = 'get' . $content['link']['type'] . 'Link';
                $id_resource = $content['link']['href'];
                if ((int) $id_resource) {
                    $content['link']['href'] = $this->context->link->$get_link_method($id_resource);
                } else {
                    unset($content['link']);
                }
            }
            if (!empty($content['link']) && !empty($content['html'])) {
                $content['html'] = str_replace('{link}', $content['link']['href'], $content['html']);
            }
            unset($b['content']);
            $sorted[$b['id_wrapper']]['banners'][$b['id_banner']] = $b + $content;
            $sorted[$b['id_wrapper']]['settings'] = [];
        }
        if ($wrapper_ids_ = $this->sqlIDs(array_keys($sorted))) {
            $w_settings = $this->db->executeS('
                SELECT * FROM ' . $this->sqlTable('_wrapper_settings') . '
                WHERE id_wrapper IN (' . $wrapper_ids_ . ')
            ');
            foreach ($w_settings as $s) {
                $id_wrapper = $s['id_wrapper'];
                $settings = json_decode($s['general'], true);
                if ($settings['is_carousel'] = $settings['display_type'] == 2) {
                    $settings['carousel'] = json_decode($s['carousel'], true);
                    $settings['bx'] = $this->sliderLibrary('getData')['type'] == 'bx';
                    $prev_num = 1;
                    foreach ($this->getResolutions() as $res => $r) {
                        if ($settings['carousel'][$r[0]] != $prev_num) {
                            $settings['item_w'][$res] = round(100 / $settings['carousel'][$r[0]], 2);
                        }
                        $prev_num = $settings['carousel'][$r[0]];
                    }
                } elseif ($settings['display_type'] == 3) {
                    $banners = $sorted[$id_wrapper]['banners'];
                    $random_id = array_rand($banners);
                    $sorted[$id_wrapper]['banners'] = [$random_id => $banners[$random_id]];
                } elseif ($settings['display_type'] == 4) {
                    $active_id = key($sorted[$id_wrapper]['banners']);
                    $sorted[$id_wrapper]['banners'][$active_id]['active_item'] = 1;
                }
                $sorted[$id_wrapper]['settings'] = $settings;
            }
        }

        return $sorted;
    }

    public function allowedForDisplay($encoded_exceptions, $current_controller, $current_id)
    {
        $exceptions = json_decode($encoded_exceptions, true);
        $allowed = true;
        if (!empty($exceptions['page']['type'])) {
            $ids = $this->formatIDs($exceptions['page']['ids'], false);
            switch ($exceptions['page']['type']) {
                case 'product_category':
                    $allowed = $current_controller == 'product' && $ids && $this->isInCategory($current_id, $ids);
                    break;
                case 'product_manufacturer':
                    $allowed = $current_controller == 'product'
                        && isset($ids[$this->context->controller->getProduct()->id_manufacturer]);
                    break;
                case 'subcategory':
                    $allowed = $current_controller == 'category' && $ids
                        && $this->isSubcategory($this->context->controller->getCategory(), $ids);
                    break;
                default:
                    if ($allowed_controller = str_replace('_all', '', $exceptions['page']['type'])) {
                        if ($allowed = $allowed_controller == $current_controller) {
                            if ($allowed_controller == $exceptions['page']['type']) {
                                $allowed = isset($ids[$current_id]); // for example: 'category' 3,5
                            } else {
                                $allowed = !isset($ids[$current_id]); // for example: 'category_all' except 3,5
                            }
                        }
                    }
                    break;
            }
        }
        if ($allowed && !empty($exceptions['customer']['type'])) {
            $ids = $this->formatIDs($exceptions['customer']['ids'], false);
            if ($exceptions['customer']['type'] == 'customer') {
                $allowed = isset($ids[$this->context->customer->id]);
            } elseif ($exceptions['customer']['type'] == 'group') {
                $allowed = array_intersect($this->context->customer->getGroups(), $ids);
            }
        }

        return $allowed;
    }

    public function isInCategory($id_product, $cat_ids)
    {
        // detect only direct associations
        return (bool) $this->db->getValue('
            SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product
            WHERE id_product = ' . (int) $id_product . ' AND id_category IN (' . $this->sqlIDs($cat_ids) . ')
        ');
    }

    public function isSubCategory($category_obj, $parent_ids)
    {
        return (bool) $this->db->getValue('
            SELECT id_category FROM ' . _DB_PREFIX_ . 'category
            WHERE nleft < ' . (int) $category_obj->nleft . ' AND nright > ' . $category_obj->nright . '
            AND id_category IN (' . $this->sqlIDs($parent_ids) . ')
        ');
    }

    public function addJS($file, $custom_path = '')
    {
        $path = ($custom_path ? $custom_path : 'modules/' . $this->name . '/views/js/') . $file;
        if (!$this->is_16) {
            $params = ['server' => $custom_path ? 'remote' : 'local'];
            $this->context->controller->registerJavascript(sha1($path), $path, $params);
        } else {
            $path = $custom_path ? $path : __PS_BASE_URI__ . $path;
            $this->context->controller->addJS($path);
        }
    }

    public function addCSS($file, $custom_path = '', $media = 'all')
    {
        $path = ($custom_path ? $custom_path : 'modules/' . $this->name . '/views/css/') . $file;
        if (!$this->is_16) {
            $params = ['media' => $media, 'server' => $custom_path ? 'remote' : 'local'];
            $this->context->controller->registerStylesheet(sha1($path), $path, $params);
        } else {
            $path = $custom_path ? $path : __PS_BASE_URI__ . $path;
            $this->context->controller->addCSS($path, $media);
        }
    }

    public function hookDisplayHeader()
    {
        $this->addJS('front.js');
        $this->sliderLibrary('load');
        $this->addJS('custom.js');
        $this->addCSS('front.css');
        $this->addCSS('custom.css');
        Media::addJsDef([
            'cb_isDesktop' => (int) !$this->deviceType('getActiveKey'),
        ]);
    }

    public function displayNativeHook($hook_name)
    {
        $this->context->smarty->assign([
            'banners' => $this->getBannersInHook($hook_name),
            'hook_name' => $hook_name,
        ]);

        return $this->display($this->local_path, 'banners.tpl');
    }

    public function hookDisplayBanner()
    {
        return $this->displayNativeHook('displayBanner');
    }

    public function hookDisplayBeforeCarrier()
    {
        return $this->displayNativeHook('displayBeforeCarrier');
    }

    public function hookDisplayBeforePayment()
    {
        return $this->displayNativeHook('displayBeforePayment');
    }

    public function hookDisplayCarrierList()
    {
        return $this->displayNativeHook('displayCarrierList');
    }

    public function hookDisplayCompareExtraInformation()
    {
        return $this->displayNativeHook('displayCompareExtraInformation');
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->displayNativeHook('displayCustomerAccount');
    }

    public function hookDisplayCustomerAccountForm()
    {
        return $this->displayNativeHook('displayCustomerAccountForm');
    }

    public function hookDisplayCustomerAccountFormTop()
    {
        return $this->displayNativeHook('displayCustomerAccountFormTop');
    }

    public function hookDisplayCustomerLoginFormAfter()
    {
        return $this->displayNativeHook('displayCustomerLoginFormAfter');
    }

    public function hookDisplayFooter()
    {
        return $this->displayNativeHook('displayFooter');
    }

    public function hookDisplayFooterProduct()
    {
        return $this->displayNativeHook('displayFooterProduct');
    }

    public function hookDisplayHeaderCategory()
    {
        return $this->displayNativeHook('displayHeaderCategory');
    }

    public function hookDisplayHome()
    {
        return $this->displayNativeHook('displayHome');
    }

    public function hookDisplayHomeTab()
    {
        return $this->displayNativeHook('displayHomeTab');
    }

    public function hookDisplayHomeTabContent()
    {
        return $this->displayNativeHook('displayHomeTabContent');
    }

    public function hookDisplayInvoice()
    {
        return $this->displayNativeHook('displayInvoice');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->displayNativeHook('displayLeftColumn');
    }

    public function hookDisplayLeftColumnProduct()
    {
        return $this->displayNativeHook('displayLeftColumnProduct');
    }

    public function hookDisplayMaintenance()
    {
        return $this->displayNativeHook('displayMaintenance');
    }

    public function hookDisplayMobileTopSiteMap()
    {
        return $this->displayNativeHook('displayMobileTopSiteMap');
    }

    public function hookDisplayMyAccountBlock()
    {
        return $this->displayNativeHook('displayMyAccountBlock');
    }

    public function hookDisplayMyAccountBlockfooter()
    {
        return $this->displayNativeHook('displayMyAccountBlockfooter');
    }

    public function hookDisplayNav()
    {
        return $this->displayNativeHook('displayNav');
    }

    public function hookDisplayOrderConfirmation()
    {
        return $this->displayNativeHook('displayOrderConfirmation');
    }

    public function hookDisplayOrderDetail()
    {
        return $this->displayNativeHook('displayOrderDetail');
    }

    public function hookDisplayPayment()
    {
        return $this->displayNativeHook('displayPayment');
    }

    public function hookDisplayPaymentReturn()
    {
        return $this->displayNativeHook('displayPaymentReturn');
    }

    public function hookDisplayPaymentTop()
    {
        return $this->displayNativeHook('displayPaymentTop');
    }

    public function hookDisplayPDFInvoice()
    {
        return $this->displayNativeHook('displayPDFInvoice');
    }

    public function hookDisplayProductAdditionalInfo()
    {
        return $this->displayNativeHook('displayProductAdditionalInfo');
    }

    public function hookDisplayProductButtons()
    {
        return $this->displayNativeHook('displayProductButtons');
    }

    public function hookDisplayProductComparison()
    {
        return $this->displayNativeHook('displayProductComparison');
    }

    public function hookDisplayProductListReviews()
    {
        return $this->displayNativeHook('displayProductListReviews');
    }

    public function hookDisplayProductTab()
    {
        return $this->displayNativeHook('displayProductTab');
    }

    public function hookDisplayProductTabContent()
    {
        return $this->displayNativeHook('displayProductTabContent');
    }

    public function hookDisplayRightColumn()
    {
        return $this->displayNativeHook('displayRightColumn');
    }

    public function hookDisplayRightColumnProduct()
    {
        return $this->displayNativeHook('displayRightColumnProduct');
    }

    public function hookDisplayShoppingCart()
    {
        return $this->displayNativeHook('displayShoppingCart');
    }

    public function hookDisplayShoppingCartFooter()
    {
        return $this->displayNativeHook('displayShoppingCartFooter');
    }

    public function hookDisplayTop()
    {
        return $this->displayNativeHook('displayTop');
    }

    public function hookDisplayTopColumn()
    {
        return $this->displayNativeHook('displayTopColumn');
    }

    public function hookDisplayCustomBanners1()
    {
        return $this->displayNativeHook('displayCustomBanners1');
    }

    public function hookDisplayCustomBanners2()
    {
        return $this->displayNativeHook('displayCustomBanners2');
    }

    public function hookDisplayCustomBanners3()
    {
        return $this->displayNativeHook('displayCustomBanners3');
    }

    public function hookDisplayCustomBanners4()
    {
        return $this->displayNativeHook('displayCustomBanners4');
    }

    public function hookDisplayCustomBanners5()
    {
        return $this->displayNativeHook('displayCustomBanners5');
    }

    public function hookDisplayCustomBanners6()
    {
        return $this->displayNativeHook('displayCustomBanners6');
    }

    public function hookDisplayCustomBanners7()
    {
        return $this->displayNativeHook('displayCustomBanners7');
    }

    /*
    * since PS 1.7
    */
    public function hookDisplayAfterBodyOpeningTag()
    {
        return $this->displayNativeHook('displayAfterBodyOpeningTag');
    }

    public function hookDisplayAfterProductThumbs()
    {
        return $this->displayNativeHook('displayAfterProductThumbs');
    }

    public function hookDisplayWrapperTop()
    {
        return $this->displayNativeHook('displayWrapperTop');
    }

    public function hookDisplayWrapperBottom()
    {
        return $this->displayNativeHook('displayWrapperBottom');
    }

    public function hookDisplayNav1()
    {
        return $this->displayNativeHook('displayNav1');
    }

    public function hookDisplayNav2()
    {
        return $this->displayNativeHook('displayNav2');
    }

    public function hookDisplayNavFullWidth()
    {
        return $this->displayNativeHook('displayNavFullWidth');
    }

    public function hookDisplayFooterBefore()
    {
        return $this->displayNativeHook('displayFooterBefore');
    }

    public function hookDisplayFooterAfter()
    {
        return $this->displayNativeHook('displayFooterAfter');
    }

    public function hookDisplayReassurance()
    {
        return $this->displayNativeHook('displayReassurance');
    }

    public function hookDisplayBeforeBodyClosingTag()
    {
        return $this->displayNativeHook('displayBeforeBodyClosingTag');
    }

    public function img()
    {
        if (!isset($this->cb_img)) {
            require_once $this->local_path . 'classes/BannerImg.php';
            $this->cb_img = new BannerImg();
        }

        return $this->cb_img;
    }

    public function data()
    {
        if (!isset($this->banners_data)) {
            require_once $this->local_path . 'classes/BannersData.php';
            $this->banners_data = new BannersData();
        }

        return $this->banners_data;
    }

    public function retro()
    {
        if (!isset($this->retro_obj)) {
            require_once $this->local_path . 'classes/retro.php';
            $this->retro_obj = new Retro();
        }

        return $this->retro_obj;
    }

    // public function __call($method, $arguments)
    // {
    //     $available_hooks = $this->getAvailableHooks();
    //     $hook_name = ltrim($method, 'hook');
    //     if (isset($available_hooks[$hook_name])) {
    //         $this->context->smarty->assign([
    //             'banners' => $this->getBannersInHook($hook_name),
    //             'hook_name' => $hook_name,
    //         ]);
    //         return $this->display($this->local_path, 'banners.tpl');
    //     }
    // }
}
