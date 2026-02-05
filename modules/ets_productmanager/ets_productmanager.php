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
require_once(dirname(__FILE__) . '/classes/ets_pmn_defines.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_view.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_filter.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit_condition.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit_condition_action.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit_history.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit_history_product.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_massedit_log.php');
require_once(dirname(__FILE__) . '/classes/ets_pmn_paggination_class.php');
class Ets_productmanager extends Module
{
    public $str_length;
    public $_errors = array();
    public $file_types = array('jpg', 'gif', 'jpeg', 'png', 'doc', 'docs', 'docx', 'pdf', 'zip', 'txt');
    public $module_dir;
    public static $products = array();
    public static $carriers = array();
    public static $categories = array();
    public static $features = array();
    public static $manufacturers = array();
    public static $tax_rules = array();
    public static $tags = array();
    public $is8e = false;
    public $_list_product_default = array();
    public $_html;
    protected $fields_form = array();
    public function __construct()
    {
        $this->name = 'ets_productmanager';
        $this->tab = 'administration';
        $this->version = '1.5.0';
        $this->author = 'PrestaHero';
        $this->module_key = '49c8448b3f4d1c5a4e6e214c15ee56e7';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->module_dir = $this->_path;
        $this->displayName = $this->l('Product Manager');
        $this->description = $this->l('Advanced product management tool: quick edit, mass edit, customizable product listing, instant product filter and more!');
$this->refs = 'https://prestahero.com/';
        $this->ps_versions_compliancy = array('min' => '1.7.4.0', 'max' => _PS_VERSION_);
        $this->_list_product_default = array('id_product', 'image', 'name', 'reference', 'name_category', 'price', 'price_final', 'sav_quantity', 'active');
        $this->str_length = false;
        $this->is8e = version_compare(_PS_VERSION_, '8.0', '>=') && Module::isEnabled('ps_edition_basic');
    }

    public function replace($matches, $value)
    {
        if (isset($matches[1]) && $matches[1])
            return Tools::substr(strip_tags($value), 0, $matches[1]);
        else
            return $value;
    }

    public function unInstallDefaultConfig()
    {
        $inputs = Ets_pmn_defines::getInstance()->getConfigInputs();
        if ($inputs) {
            foreach ($inputs as $input) {
                Configuration::deleteByName($input['name']);
            }
        }
        Configuration::deleteByName('ETS_PRODUCTMANAGE_ARRANGE_LIST');
        return true;
    }

    public function installDefaultConfig()
    {
        $inputs = Ets_pmn_defines::getInstance()->getConfigInputs();
        $languages = Language::getLanguages(false);
        if ($inputs) {
            foreach ($inputs as $input) {
                if (isset($input['default']) && $input['default']) {
                    if (isset($input['lang']) && $input['lang']) {
                        $values = array();
                        foreach ($languages as $language) {
                            $values[$language['id_lang']] = isset($input['default_lang']) && $input['default_lang'] ? $this->getTextLang($input['default_lang'], $language) : $input['default'];
                        }
                        Configuration::updateGlobalValue($input['name'], $values);
                    } else
                        Configuration::updateGlobalValue($input['name'], $input['default']); 
                }
            }
        }
        return true;
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionDispatcherBefore')
            && $this->registerHook('actionAdminProductsListingFieldsModifier')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionAdminProductsListingResultsModifier') && $this->_installTab() && Ets_pmn_defines::getInstance()->_installDb() && $this->installDefaultConfig();
    }

    public function _installTab()
    {

        $tabs = array(
            array(
                'class_name' => 'AdminProductManagerAjax',
                'active' => 0,
                'name' => $this->l('Product Manager ajax'),
                'namelang' => 'Product manager ajax',
            ),
            array(
                'class_name' => 'AdminProductManagerMassiveEdit',
                'active' => 1,
                'name' => $this->l('Mass Edit'),
                'namelang' => 'Mass Edit',
            ),
            array(
                'class_name' => 'AdminProductManagerMassiveEditLog',
                'active' => 0,
                'name' => $this->l('Mass Edit log'),
                'namelang' => 'Mass Edit log',
            ),
        );
        if ($id_parent = (int)Tab::getIdFromClassName('AdminCatalog')) {
            $languages = Language::getLanguages(false);
            foreach ($tabs as $tab) {
                if (!Tab::getIdFromClassName($tab['class_name'])) {
                    $tab_class = new Tab();
                    $tab_class->class_name = $tab['class_name'];
                    $tab_class->module = $this->name;
                    $tab_class->id_parent = $id_parent;
                    $tab_class->active = $tab['active'];
                    foreach ($languages as $lang)
                        $tab_class->name[$lang['id_lang']] = $this->getTextLang($tab['namelang'], $lang) ?: $tab['name'];
                    $tab_class->save();
                }
            }
        }
        return true;
    }

    public function unInstall()
    {
        return parent::uninstall()
            && $this->unregisterHook('displayBackOfficeHeader')
            && $this->unregisterHook('actionDispatcherBefore')
            && $this->unregisterHook('actionAdminProductsListingFieldsModifier')
            && $this->unregisterHook('actionProductUpdate')
            && $this->unregisterHook('actionAdminProductsListingResultsModifier') && $this->_unInstallTab() && Ets_pmn_defines::getInstance()->_unInstallDb() && $this->unInstallDefaultConfig();
    }

    public function _unInstallTab()
    {
        $tabs = array('AdminProductManagerAjax', 'AdminProductManagerMassiveEdit', 'AdminProductManagerMassiveEditTemplates');
        foreach ($tabs as $tab) {
            if ($tabId = Tab::getIdFromClassName($tab)) {
                $tab_class = new Tab($tabId);
                $tab_class->delete();
            }
        }
        return true;
    }

    public function addJquery()
    {
        if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
            $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-' . _PS_JQUERY_VERSION_ . '.min.js');
        else
            $this->context->controller->addJquery();
    }

    public function hookActionDispatcherBefore($params)
    {
            if (isset($params['controller_type']) && $params['controller_type'] == Dispatcher::FC_ADMIN) {
                $context = $this->context;

                if (isset($context->employee->id) && $context->employee->id && $context->employee->isLoggedBack()) {
                    if(Tools::isSubmit('getFormListView'))
                    {
                        if(version_compare(_PS_VERSION_, '8.0.0', '>='))
                            Ets_pmn_defines::updateNewFeatureFlag();
                        $this->getFormListView();
                    }
                    if(version_compare(_PS_VERSION_, '8.0.0', '>='))
                    {
                        $this->addKeyTwig();
                    }
                }
            }
    }
    public function getFormListView()
    {
        $view = array(
            array(
                'id_ets_pmn_view' => 0,
                'fields' => '',
                'name' => $this->l('--'),
            ),
        );
        $list_views = Ets_pmn_view::getListViews();
        $list_views = array_merge($view,$list_views);
        $id_view_selected = (int)Ets_pmn_view::getViewByIdEmployee($this->context->employee->id);
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'ets_pmg_list_views'=>$list_views,
                'ets_pmg_id_view_selected'=>$id_view_selected,
            )
        );
        if(Tools::isSubmit('ajax'))
        {
            die(
                json_encode(
                    array(
                        'html' => $this->display(__FILE__,'list_views.tpl'),
                    )
                )
            );
        }
        return $this->display(__FILE__,'list_views.tpl');
    }
    public function getTwigs()
    {
        $product_fields = Ets_pmn_defines::getInstance()->getProductListFields();
        $ets_filter_products = Ets_pmn_filter::getFilter($this->context->employee->id);
        return array(
            'ets_filter_products'=>$ets_filter_products,
            'ETS_PMN_ENABLE_INSTANT_FILTER'=>(int)Configuration::get('ETS_PMN_ENABLE_INSTANT_FILTER'),
            'ets_pmn_id_lang'=>$this->context->language->id,
            'ets_pmg_product_fileds'=>$product_fields,
            'orderBy' => Tools::isSubmit('orderBy') ? Tools::getValue('orderBy'):'',
            'sortOrder' => Tools::isSubmit('orderBy') ? Tools::getValue('sortOrder'):'',
        );
    }
    public function addKeyTwig()
    {
        $request = $this->getRequestContainer();
        if($request)
        {
            $route = $request->get('_route');
            if($route=='admin_product_catalog' || $route=='admin_products_index')
            {
                $id_product = $request->get('id') ? : $request->get('productId');
                if(!$id_product && $this->active)
                {
                    $this->assignTwigVar(
                        $this->getTwigs()
                    );
                    if(!Tools::isSubmit('getFormListView') && !Tools::isSubmit('updateNewFeatureFlag'))
                        return Ets_pmn_defines::updateOldFeatureFlag();
                }
            }
        }
        Ets_pmn_defines::updateNewFeatureFlag();
    }
    public function addTwigVar($key, $value)
    {
        if ($sfContainer = $this->getSfContainer()) {
            $sfContainer->get('twig')->addGlobal($key, $value);
        }
    }
    public function assignTwigVar($params)
    {
        /** @var \Twig\Environment $tw */
        if(!class_exists('Ets_productmanager_twig'))
            require_once(dirname(__FILE__).'/classes/Ets_productmanager_twig.php');
        if($sfContainer = $this->getSfContainer())
        {
            try {
                $tw = $sfContainer->get('twig');
                $tw->addExtension(new Ets_productmanager_twig($params));
            } catch (\Twig\Error\RuntimeError $e) {
                // do no thing
            }
        }
    }
    public function hookDisplayBackOfficeHeader()
    {
        $html ='';
        if($request = $this->getRequestContainer())
        {
            $id_product = $request->get('id') ? : $request->get('productId');
        }
        else
            $id_product = (int)Tools::getValue('id_product');
        $configure = Tools::getValue('configure');
        $controller = Tools::getValue('controller');    
        if($controller == 'AdminProductManagerMassiveEdit' || $controller=='AdminProductManagerMassiveEditLog' || $controller=='AdminProductManagerMassiveEditTemplates' || $controller=='AdminProducts')
        {
            $this->addJquery();
        }  
        if($controller=='AdminProducts' && $this->active)
        {
            if(!$id_product)
            {
                Media::addJsDef(
                    array(
                        'ets_pmg_link_product_arrange'=>$this->context->link->getAdminLink('AdminProductManagerAjax').'&arrangeproduct=1',
                        'Customize_product_list_text'=>$this->l('Custom columns'),
                        'ets_pmg_link_productmanager_setting'=>$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                        'Product_manager_settings'=>$this->l('Product manager settings'),
                        'ets_pmn_edit_text'=>$this->l('Edit'),
                        'ets_pmn_update_text'=>$this->l('Update'),
                        'ets_pmn_cancel_text'=>$this->l('Cancel'),
                        'viewmore_text'=>$this->l('View more'),
                        'viewless_text'=>$this->l('View less'),
                        'ETS_PMN_FIXED_HEADER_PRODUCT'=>Configuration::get('ETS_PMN_FIXED_HEADER_PRODUCT'),
                        'ETS_PMN_ENABLE_INSTANT_FILTER'=>(int)Configuration::get('ETS_PMN_ENABLE_INSTANT_FILTER'),
                    )
                );

                if(version_compare(_PS_VERSION_, '8.0.0', '<')) {
                    $twigs = $this->getTwigs();
                    foreach ($twigs as $key => $value)
                        $this->addTwigVar($key, $value);
                }
                $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
                $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
                $this->context->controller->addJqueryPlugin('fancybox');
                $this->context->controller->addJS($this->_path.'view');
                $this->context->controller->addCSS($this->_path . 'views/css/product.css');
                $this->context->controller->addCSS($this->_path . 'views/css/product_popup.css');
                $this->context->controller->addJS($this->_path . 'views/js/validate.js');
                $this->context->controller->addJS($this->_path . 'views/js/product.js');
                $this->context->controller->addJS($this->_path . 'views/js/list_product.js');
                $languages = Language::getLanguages(false);
                $tax_rule_groups = TaxRulesGroup::getTaxRulesGroupsForOptions();
                if($tax_rule_groups)
                {
                    foreach($tax_rule_groups as &$tax_rule_group)
                    {
                        $tax_rule_group['value_tax'] = $this->getTaxValue($tax_rule_group['id_tax_rules_group']);
                    }
                }
                $this->smarty->assign(
                    array(
                        'tax_rule_groups' => $tax_rule_groups,
                        '_PS_JS_DIR_' => _PS_JS_DIR_,
                        'ets_max_lang_text' => (int)$this->str_length,
                        'ets_pmn_lang_current' => $this->context->language->id,
                    )
                );
                if(Module::isEnabled('ets_seo') && Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE'))
                {
                    $ets_pmn_seo_meta_titles = array();
                    $ets_pmn_seo_meta_descriptions = array();
                    foreach($languages as $language)
                    {
                        $ets_pmn_seo_meta_titles[$language['id_lang']] = Configuration::get('ETS_SEO_PROD_META_TILE',$language['id_lang']);
                        $ets_pmn_seo_meta_descriptions[$language['id_lang']] = Configuration::get('ETS_SEO_PROD_META_DESC',$language['id_lang']);
                    }
                    $this->context->smarty->assign(
                        array(
                            'ets_pmn_seo_meta_titles' => $ets_pmn_seo_meta_titles,
                            'ets_pmn_seo_meta_descriptions' => $ets_pmn_seo_meta_descriptions,
                        )
                    );
                }
                $html .= $this->display(__FILE__,'admin_head.tpl');
                if(Module::isEnabled('ets_multilangimages'))
                    $html .= Module::getInstanceByName('ets_multilangimages')->hookDisplayAdminProductsMainStepRightColumnBottom();
            }
            else
            {
                Media::addJsDef(
                    array(
                        'ets_pmn_product_note' => Ets_pmn_defines::getProductNote($id_product) ? : '',
                        'pmn_is17' => version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? true:false,
                        'pmn_id_product' => $id_product,
                        'pmn_private_note_text' => $this->l('Private note'),
                        'pmn_link_ajax' => $this->context->link->getAdminLink('AdminProductManagerAjax'),
                    )
                );
                $this->context->controller->addJS($this->_path . 'views/js/product_note.js');
            }
        }

        if($controller=='AdminProductManagerMassiveEdit')
        {
            $this->context->controller->addJqueryUI('ui.widget');
            $this->context->controller->addJqueryPlugin('tagify');
            $this->context->controller->addJqueryPlugin('autocomplete');
            $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            $this->context->controller->addJS($this->_path . 'views/js/massedit.js');
            $this->context->controller->addCSS($this->_path . 'views/css/product_popup.css');
            $this->context->controller->addCSS($this->_path . 'views/css/masseditedit.css');
            $this->context->controller->addJS($this->_path . 'views/js/product.js');
        }
        if($controller=='AdminProductManagerMassiveEditTemplates')
        {

            $this->context->controller->addJqueryUI('ui.widget');
            $this->context->controller->addJqueryPlugin('tagify');
            $this->context->controller->addJqueryPlugin('autocomplete');
            $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            $this->context->controller->addJS($this->_path . 'views/js/massedit.js');
            $this->context->controller->addCSS($this->_path . 'views/css/massedit.css');
            $this->context->controller->addCSS($this->_path . 'views/css/product.css');
            $this->context->controller->addJS($this->_path . 'views/js/product.js');
        }
        if($controller=='AdminProductManagerMassiveEditLog')
        {
            $this->context->controller->addJS($this->_path . 'views/js/masseditlog.js');
            $this->context->controller->addCSS($this->_path . 'views/css/masseditlog.css');
            $this->context->controller->addJS($this->_path . 'views/js/product.js');
        }
        if ($configure == $this->name && $controller=='AdminModules') {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            if ($this->is8e) {
                $this->context->controller->addCSS($this->_path . 'views/css/admin8e.css');
            }
        }
        return $html;
    }
    public function getRequestContainer()
    {
        if($sfContainer = $this->getSfContainer())
        {
            return $sfContainer->get('request_stack')->getCurrentRequest();
        }
        return null;
    }
    public function getSfContainer()
    {
        if(!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer'))
        {
            $kernel = null;
            try{
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            }
            catch (Exception $ex){
                return null;
            }
        }
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
        return $sfContainer;
    }
    public static function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(array('_'.$theme_name, $theme_name.'_'), '', $name);

        //check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name)) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name.'_'.$theme_name)) {
            return $name_without_theme_name.'_'.$theme_name;
        } elseif (ImageType::getByNameNType($theme_name.'_'.$name_without_theme_name)) {
            return $theme_name.'_'.$name_without_theme_name;
        } else {
            return $name_without_theme_name.'_default';
        }
    }
    public function renderFormImageProduct($id_product)
    {
        $type_image= self::getFormatedName('home');
        $product = new Product($id_product);
        $images = Ets_pmn_defines::getInstance()->getListImages($id_product);
        if($images)
        {
            foreach($images as &$image)
            {
                $image['link'] = str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $image['id_image'], $type_image));
                $image['link_delete'] = $this->getLinkAdminController('admin_product_image_delete',array('idImage'=>$image['id_image']));
                $image['link_update'] = $this->getLinkAdminController('admin_product_image_form',array('idImage'=>$image['id_image']));
                $image['format'] = 'jpg';
            }
        }
        $lang_default = new Language(Configuration::get('PS_LANG_DEFAULT'));
        $this->context->smarty->assign(
            array(
                'images' => $images,
                'product_class' => $product,
                '_PS_JS_DIR_' => _PS_JS_DIR_,
                'lang_default' => $lang_default,
                'product_name' => isset($product->name[$this->context->language->id]) ? $product->name[$this->context->language->id] : $product->name[Configuration::get('PS_LANG_DEFAULT')],
            )
        );
        return $this->display(__FILE__,'product/images.tpl');
    }
    public function _getFromImageProduct($id_image)
    {
        $image_class = new Image($id_image);
        $languages = Language::getLanguages(false);
        $legends = array();
        foreach($languages as $language)
        {
            $legends[$language['id_lang']] = $image_class->legend[$language['id_lang']];
        }
        $folders = str_split((string)$image_class->id);
        $path = implode('/', $folders) . '/';
        $url_image = $this->getBaseLink() . '/img/p/' . $path . $image_class->id . '.jpg';
        $isoLang = Tools::getValue('isoLang');
        if($isoLang && Validate::isLangIsoCode($isoLang))
            $id_lang_default = (int)Language::getIdByIso(Tools::getValue('isoLang'));
        else
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->context->smarty->assign(
            array(
                'image_class' => $image_class,
                'legends' => $legends,
                'languages' => $languages,
                'url_image'=> $url_image,
                'module_dir' => $this->_path,
                'id_lang_default' => $id_lang_default,
            )
        );
        if(Module::isEnabled('ets_multilangimages'))
        {
            $lang_images = array();
            if($languages)
            {
                foreach($languages as $language)
                {
                    if($language['id_lang'] == $id_lang_default || Ets_pmn_defines::getIdImageLang($id_image,$language['id_lang']))
                        $lang_images[$language['id_lang']] = Module::getInstanceByName('ets_multilangimages')->getLinkImageBase($id_image,$language['id_lang'],self::getFormatedName('home')).'?time='.time();
                    else
                        $lang_images[$language['id_lang']] ='';
                }
            }
            $this->context->smarty->assign(
                array(
                    'lang_images' => $lang_images
                )
            );
            return $this->display(__FILE__,'product/form_image_lang.tpl');
        }
        else
            return $this->display(__FILE__,'product/form_image.tpl');
    }
    public function displayProductCategoryTre($blockCategTree,$selected_categories=array(),$name='',$disabled_categories=array(),$id_category_default=0,$categories=null,$backend=false,$displayInput=true,$type_input='checkbox')
    {
        $this->smarty->assign(
            array(
                'blockCategTree'=> $blockCategTree,
                'branche_tpl_path_input'=> _PS_MODULE_DIR_.$this->name.'/views/templates/hook/category-tree.tpl',
                'selected_categories'=>$selected_categories,
                'disabled_categories' => $disabled_categories,
                'id_category_default' => $id_category_default,
                'name'=>$name ? $name :'id_categories',
                'backend' => $backend,
                'displayInput' => $displayInput,
                'categories' =>$categories,
                'type_input' => $type_input,
            )
        );
        return $this->display(__FILE__, 'categories.tpl');
    }
    public function renderFormCategoryProduct($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $categories = Ets_pmn_defines::getInstance()->getListCategoryProduct($id_product); 
        $selected_categories =array();
        if($categories)
        {
            foreach($categories as $category)
                $selected_categories[]=$category['id_category'];
        }
        $this->context->smarty->assign(
            array(
                'product_name' => $product->name,
                'id_product' => $id_product,
                'tree_categories' =>$this->displayProductCategoryTre(Ets_pmn_defines::getInstance()->getCategoriesTree(),$selected_categories,'',array(),$product->id_category_default,$categories),
            )
        );
        return $this->display(__FILE__,'form_category.tpl');
    }

    public function renderFormFeatureProduct($id_product)
    {
        $product_features = Ets_pmn_defines::getInstance()->getFeaturesProduct($id_product);
        $features= Ets_pmn_defines::getInstance()->getFeatures();
        $features_values = Ets_pmn_defines::getInstance()->getFeatureValues();
        $product = new Product($id_product,false,$this->context->language->id);
        $this->smarty->assign(
            array(
                'product_features' => $product_features,
                'features' =>$features ,
                'features_values' => $features_values,
                'id_product' => $id_product,
                'product_name' =>$product->name,
            )
        );
        return $this->display(__FILE__,'product/features.tpl');
    }
    
    public function renderFormCombinations($id_product)
    {
        $product = new Product($id_product);
        $product_type = $product->getType();
        if($product_type == Product::PTYPE_SIMPLE)
        {
            $attributeGroups = Ets_pmn_defines::getAttributeGroups();
            $this->smarty->assign(
                array(
                    'attributeGroups'=>$attributeGroups,
                    'list_product_attributes'=>Ets_pmn_defines::getInstance()->displayListCombinations($id_product),
                    'id_product' => $id_product,
                    'link' => $this->context->link,
                    '_PS_JS_DIR_' => _PS_JS_DIR_,
                    'product_name' => isset($product->name[$this->context->language->id]) ? $product->name[$this->context->language->id] : $product->name[Configuration::get('PS_LANG_DEFAULT')],
                )
            );
            return $this->display(__FILE__,'product/combinations.tpl');
        }
    }

    public function renderFormQuantityProduct($id_product)
    {
        $stock_availables = Ets_pmn_defines::getInstance()->getStockAvailables($id_product);
        $product = new Product($id_product,false,$this->context->language->id);
        $this->smarty->assign(
            array(
                'id_product' => $id_product,
                'stock_availables' => $stock_availables,
                'product_name' =>$product->name,
            )
        );
        return $this->display(__FILE__,'product/quantity.tpl');
    }
    
    public function renderFormCustomizationProduct($id_product)
    {
        $product = new Product($id_product,false,$this->context->language->id);
        $this->smarty->assign(
            array(
                'id_product' => $id_product,
                'languages' => Language::getLanguages(false),
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                'customizationFields' => Ets_pmn_defines::getInstance()->getCustomizationFields($id_product),
                'product_name' => $product->name
            )
        );
        return $this->display(__FILE__,'product/customization.tpl');
    }
    public function renderFormSupplierProduct($id_product)
    {
        $suppliers = Ets_pmn_defines::getInstance()->getSuppliers($id_product);
        $product = new Product($id_product,false,$this->context->language->id);
        $this->smarty->assign(
            array(
                'id_product' => $id_product,
                'suppliers' => $suppliers,
                'product_name' => $product->name,
                'id_supplier_default' => $product->id_supplier,
                'currencies' => Ets_pmn_defines::getInstance()->getCurrencies(),
                'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))
            )
        );
        return $this->display(__FILE__,'product/suppliers.tpl');
    }
    public function renderFormAttachedFiles($id_product)
    {
        $attachments = Ets_pmn_defines::getInstance()->getListAttachmentFields($id_product);
        $product = new Product($id_product,false,$this->context->language->id);
        $this->smarty->assign(
            array(
                'id_product' => $id_product,
                'attachments' => $attachments,
                'product_name' => $product->name,
            )
        );
        return $this->display(__FILE__,'product/attached_files.tpl');
    }
    public function getContent()
    {
        if(Tools::isSubmit('submitChangeView'))
        {
            $id_view_selected = (int)Tools::getValue('id_view_selected');
            if(Ets_pmn_view::submitChangeView($id_view_selected))
            {
                die(json_encode(
                    array(
                        'success'=> $this->l('Changed view successfully'),
                    )
                ));
            }
        }
        if(Tools::isSubmit('btnSubmitSaveView') || Tools::isSubmit('btnSubmitSaveAsView'))
        {
            $errors = '';
            $listFieldProducts = Tools::getValue('listFieldProducts');
            $name = Tools::getValue('view_name');
            $id_view = (int)Tools::getValue('id_view_selected');
            if(!$name)
                $errors = $this->l('Name is required');
            elseif(!Validate::isCleanHtml($name))
                $errors = $this->l('Name is not valid');
            elseif(Ets_pmn_view::checkExistName($name, Tools::isSubmit('btnSubmitSaveView') ? $id_view :0))
                $errors = $this->l('Name already exists');
            elseif(!$listFieldProducts)
            {
                $errors = $this->l('Custom column is required');
            }
            elseif(!is_array($listFieldProducts) || !Ets_productmanager::validateArray($listFieldProducts))
                $errors = $this->l('Custom column is not valid');
            if(!$errors)
            {
                if(Tools::isSubmit('btnSubmitSaveAsView') || !$id_view)
                    $viewOjb = new Ets_pmn_view();
                else
                    $viewOjb = new Ets_pmn_view($id_view);
                $viewOjb->name = $name;
                $viewOjb->fields = implode(',', array_map('pSQL',$listFieldProducts));
                $success ='';
                if($viewOjb->id)
                {
                    if($viewOjb->update())
                        $success = $this->l('Updated view successfully');
                    else
                        $errors = $this->l('An error occurred while saving the view');
                        
                }elseif($viewOjb->add())
                    $success = $this->l('Added view successfully');
                else
                    $errors = $this->l('An error occurred while saving the view');
                if($success)
                {
                    if($viewOjb->id!=$id_view)
                    {
                        if (Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST')) {
                            $list_fields = explode(',', Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST'));
                        } else
                            $list_fields = $this->_list_product_default;
                        $view = array(
                            array(
                                'id_ets_pmn_view' => 0,
                                'fields' => implode(',',$list_fields),
                                'name' => $this->l('--'),
                            ),
                        );
                        $list_views = Ets_pmn_view::getListViews();
                        $list_views = array_merge($view,$list_views);
                        Ets_pmn_view::updateView($viewOjb->id,$listFieldProducts);
                        $this->context->smarty->assign(
                            array(
                                'list_views' =>$list_views,
                                'id_view_selected' => $viewOjb->id,
                            )
                        );
                    }
                    Ets_pmn_view::updateView($viewOjb->id,$listFieldProducts);
                    die(json_encode(
                        array(
                            'success'=> $success,
                            'id_view_selected' => $viewOjb->id,
                            'list_sellect_view' => $viewOjb->id!=$id_view ? $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/select_view.tpl'):''
                        )
                    ));
                }
            }
            if ($errors)
            {
                die(json_encode(
                    array(
                        'errors'=> $errors,
                    )
                ));
            }
        }
        if(Tools::isSubmit('btnSubmitDeleteView') && ($id_view_selected = (int)Tools::getValue('id_view_selected')))
        {
            $viewOjb = new Ets_pmn_view($id_view_selected);
            if($viewOjb->delete())
            {
                if (Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST')) {
                    $list_fields = explode(',', Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST'));
                } else
                    $list_fields = $this->_list_product_default;
                $view = array(
                    array(
                        'id_ets_pmn_view' => 0,
                        'fields' => implode(',',$list_fields),
                        'name' => $this->l('--'),
                    ),
                );
                $list_views = Ets_pmn_view::getListViews();
                $list_views = array_merge($view,$list_views);
                $this->context->smarty->assign(
                    array(
                        'list_views' =>$list_views,
                        'id_view_selected' => 0,
                    )
                );
                die(
                    json_encode(
                        array(
                            'success'=> $this->l('Deleted successfully'),
                            'list_sellect_view' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/select_view.tpl'),
                        )
                    )
                );
            }
        }
        if (Tools::isSubmit('submitArrangeListProduct')) {
            $listFieldProducts =  Tools::getValue('listFieldProducts');
            $id_view_selected = (int)Tools::getValue('id_view_selected');
            $errors ='';
            if(!$listFieldProducts)
            {
                $errors = $this->l('Custom column is required');
            }
            elseif(!is_array($listFieldProducts) || !Ets_productmanager::validateArray($listFieldProducts))
                $errors = $this->l('Custom column is not valid');
            if($errors)
            {
                die(json_encode(
                    array(
                        'errors'=> $errors,
                    )
                ));
            }
            else
            {
                Ets_pmn_view::updateView($id_view_selected,$listFieldProducts);
                die(json_encode(
                    array(
                        'message' => $this->l('Settings updated!'),
                        'success' => true,
                    )
                ));
            }
            
        }
        if (Tools::isSubmit('submitRessetToDefaultList'))
        {
            Ets_pmn_view::updateView(0,'');
            die(json_encode(
                array(
                    'success'=> 3,
                )
            ));
        }
        $this->_html = '';
        if(!$this->active)
            $this->_html .= $this->displayWarning($this->l('You must enable Product Manager module to configure its features'));
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_errors)) {
                $inputs = Ets_pmn_defines::getInstance()->getConfigInputs();
                $languages = Language::getLanguages(false);
                $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
                foreach($inputs as $input)
                {
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $values = array();
                        foreach($languages as $language)
                        {
                            $value_default = Tools::getValue($input['name'].'_'.$id_lang_default);
                            $value = Tools::getValue($input['name'].'_'.$language['id_lang']);
                            $values[$language['id_lang']] = ($value && Validate::isCleanHtml($value)) || !isset($input['required']) ? $value : (Validate::isCleanHtml($value_default) ? $value_default :'');
                        }
                        Configuration::updateValue($input['name'],$values);
                    }
                    else
                    {
                        $val = Tools::getValue($input['name']);
                        if(Validate::isCleanHtml($val))
                            Configuration::updateValue($input['name'],$val);
                    }
                }
                $this->_html .= $this->displayConfirmation($this->l('Settings have been updated'));
            } else {
                $this->_html .= $this->displayError($this->_errors);
            }
        }
        $this->_html .= $this->renderForm();

        return $this->_html;
    }
    public function _postValidation()
    {
        $languages = Language::getLanguages(false);
        $inputs = Ets_pmn_defines::getInstance()->getConfigInputs();
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        foreach($inputs as $input)
        {
            if(isset($input['lang']) && $input['lang'])
            {
                if(isset($input['required']) && $input['required'])
                {
                    $val_default = Tools::getValue($input['name'].'_'.$id_lang_default);
                    if(!$val_default)
                    {
                        $this->_errors[] = sprintf($this->l('%s is required'),$input['label']);
                    }
                    elseif($val_default && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val_default))
                        $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    elseif($val_default && !Validate::isCleanHtml($val_default))
                        $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    else
                    {
                        foreach($languages as $language)
                        {
                            if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value))
                                $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                            elseif($value && !Validate::isCleanHtml($value))
                                $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        }
                    }
                }
                else
                {
                    foreach($languages as $language)
                    {
                        if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value))
                            $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        elseif($value && !Validate::isCleanHtml($value))
                            $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                    }
                }
            }
            else
            {
                $val = Tools::getValue($input['name']);
                if($val===''&& isset($input['required']))
                {
                    $this->_errors[] = sprintf($this->l('%s is required'),$input['label']);
                }
                if($val!=='' && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val))
                {
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                }
                elseif($val!=='' && isset($input['validate']) && ($validate = $input['validate']) && $validate=='isUnsignedInt' && $val <=0)
                {
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                }
                elseif($val!==''&& !Validate::isCleanHtml($val))
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
            }
        }
    }
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'id_form' => 'ets-pm-config-form',
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cog'
                ),
                'input' => Ets_pmn_defines::getInstance()->getConfigInputs(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = $this->id;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'link' => $this->context->link,
        );
        $helper->override_folder = '/';
        $this->fields_form = array();
        return $helper->generateForm(array('form' => $fields_form));
    }
    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();
        $inputs = Ets_pmn_defines::getInstance()->getConfigInputs();
        if($inputs)
        {
            foreach($inputs as $input)
            {
                if(!isset($input['lang']))
                {
                    $fields[$input['name']] = Tools::getValue($input['name'],Configuration::get($input['name']));
                }
                else
                {
                    foreach($languages as $language)
                    {
                        $fields[$input['name']][$language['id_lang']] = Tools::getValue($input['name'].'_'.$language['id_lang'],Configuration::get($input['name'],$language['id_lang']));
                    }
                }
            }
        }
        return $fields;
    }
    public function getFormEditInlineProduct($id_product)
    {
        if(!$this->active)
        {
            die(
                json_encode(
                    array(
                        'error' => $this->l('You must enable Product Manager module to configure its features'),
                    )
                )
            );
        }
        $product_class = new Product($id_product);
        $languages = Language::getLanguages(false);
        if(Module::isEnabled('ets_seo'))
        {
            foreach($languages as $language)
            {
                if(!isset($product_class->meta_title[$language['id_lang']]) || !$product_class->meta_title[$language['id_lang']] || (Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE') && Configuration::get('ETS_SEO_PROD_META_TILE',$language['id_lang'] )) )
                    $product_class->meta_title[$language['id_lang']] = Configuration::get('ETS_SEO_PROD_META_TILE',$language['id_lang']);
                if(!isset($product_class->meta_description[$language['id_lang']]) || !$product_class->meta_description[$language['id_lang']] || (Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE') && Configuration::get('ETS_SEO_PROD_META_DESC',$language['id_lang'] )))
                    $product_class->meta_description[$language['id_lang']] = Configuration::get('ETS_SEO_PROD_META_DESC',$language['id_lang']);
            }
        }
        $fields = Ets_pmn_defines::getInstance()->getProductListFields();
        $valueFieldPost = array();
        if($fields)
        {
            foreach(array_keys($fields) as $key)
            {
                if(property_exists($product_class,$key) && $key!='tax_name')
                {
                    $valueFieldPost[$key] = $product_class->{$key};
                }
                elseif($key=='price_final')
                    $valueFieldPost['price_final'] = Tools::ps_round($product_class->price +$product_class->price*$this->getTaxValue($product_class->id_tax_rules_group),6);
                elseif($key=='tax_name')
                {
                    $valueFieldPost['tax_name'] = $valueFieldPost['id_tax_rules_group']= $product_class->id_tax_rules_group;

                }
                if(!isset($valueFieldPost[$key]) && Module::isEnabled('ets_customfields') &&  ($value = Module::getInstanceByName('ets_customfields')->getCustomField($product_class->id,$key)))
                    $valueFieldPost[$key] = $value;
                elseif($key=='sav_quantity')
                {
                    $valueFieldPost['sav_quantity'] = StockAvailable::getQuantityAvailableByProduct($product_class->id);
                }
                elseif(($key=='focus_keyphrase' || $key=='related_keyphrases') && Module::isEnabled('ets_seo')) 
                {
                    foreach($languages as $language)
                        $valueFieldPost[$key][$language['id_lang']] = Ets_pmn_defines::getInstance()->getSeoKey($key,$id_product,$language['id_lang']);
                }
                elseif($key=='tags')
                {
                    foreach($languages as $language)
                        $valueFieldPost['tags'][$language['id_lang']] = Ets_pmn_defines::getInstance()->getListtags($id_product,false,$language['id_lang']);
                }
                elseif($key=='private_note')
                    $valueFieldPost['private_note'] = Ets_pmn_defines::getProductNote($id_product);
                if($key=='manufacturers')
                {
                    $valueFieldPost['manufacturers'] = $product_class->id_manufacturer;
                }
                if(Module::isEnabled('ets_extraproducttabs') && Tools::strpos($key,'extra_product_tab_')===0)
                {
                    $id_tab = (int)str_replace('extra_product_tab_','',$key);
                    foreach($languages as $language)
                    {
                        $valueFieldPost[$key][$language['id_lang']] = Ets_pmn_defines::getInstance()->getExtraProductTabValue($product_class->id,$id_tab,$language['id_lang']);
                    }
                }
                   
            }
            $this->context->smarty->assign(
                array(
                    'valueFieldPost' => $valueFieldPost,
                    'languages' => $languages,
                    'current_product' => $product_class,
                    'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                    'has_attribute' => false,
                )
            );
            $form_inputs = array();
            foreach($fields as $key=>$field)
            {
                if(isset($field['input']) && $field['input'])
                {
                    $field['input']['name'] = $key;
                    $form_inputs[] = array(
                        'name' => $key,
                        'popup' => isset($field['input']['popup']) ? $field['input']['popup'] : false,
                        'form_html' => $this->displayInputForm($field),
                    );
                }
            }
            die(
                json_encode(
                    array(
                        'inputs' => $form_inputs
                    )
                )
            );
        }
    }
    protected static $taxValue =array();
    public function getTaxValue($id_tax_group)
    {
        if (!isset(self::$taxValue[$id_tax_group]))
        {
            if($id_tax_group)
            {
                $price = 10;
                $context = $this->context;
                if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                    $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                    $address = new Address($id_address);
                } else {
                    $address = new Address();
                }
                $address = Address::initialize($address->id,true);
                if(!$address->id_state && $address->id_country)
                {
                    $address->id_state = Ets_pmn_defines::getTaxRulesGroupDefaultStateId($id_tax_group, $address->id_country);
                }
                $tax_manager = TaxManagerFactory::getManager($address, $id_tax_group);

                $product_tax_calculator = $tax_manager->getTaxCalculator();
                $priceTax = $product_tax_calculator->addTaxes($price);
                if($priceTax >  $price)
                    self::$taxValue[$id_tax_group] =  ($priceTax-$price)/$price;
                else
                    self::$taxValue[$id_tax_group] = 0;
            }
            else
                self::$taxValue[$id_tax_group] = 0;
        }
        return self::$taxValue[$id_tax_group];
    }
    public function displayInputForm($row)
    {
       $this->smarty->assign(
            array(
                'field' => $row['input'],
                'row' => $row,
                'field_title' => $row['title'],
                'id_lang_current' => $this->context->language->id,
                'id_product' => (int)Tools::getValue('id_product'),
            )
       ); 
       $content = $this->display(__FILE__,'form.tpl');
       $this->context->smarty->force_compile = false;
       return $content;
    }
    public function getFormArrangeProduct()
    {
        if (Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST')) {
            $list_fields = explode(',', Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST'));
        } else
            $list_fields = $this->_list_product_default;
        $view = array(
            array(
                'id_ets_pmn_view' => 0,
                'fields' => implode(',',$list_fields),
                'name' => $this->l('--'),
            ),
        );
        $list_views = Ets_pmn_view::getListViews();
        $list_views = array_merge($view,$list_views);
        $id_view_selected = (int)Ets_pmn_view::getViewByIdEmployee($this->context->employee->id);
        if($id_view_selected && ($viewObj = new Ets_pmn_view($id_view_selected)) && Validate::isLoadedObject($viewObj))
            $list_fields = explode(',',$viewObj->fields);
        $this->context->smarty->assign(
            array(
                'list_fields' => $list_fields,
                'title_fields' => Ets_pmn_defines::getInstance()->getProductFields(),
                'link_module' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
                'list_views' => $list_views,
                'id_view_selected' =>$id_view_selected,
            )
        );
        $display = $this->display(__FILE__, 'form_arrange.tpl');
        if (Tools::isSubmit('ajax')) {
            die(
                json_encode(
                    array(
                        'block_html' => $display,
                    )
                )
            );
        }
        return $display;
    }
    public function getFilters()
    {
        if(version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $adminFilter = $this->get('prestashop.core.admin.admin_filter.repository');
            $filter_products =  $adminFilter->findByEmployeeAndRouteParams($this->context->employee->id,$this->context->shop->id,'ProductController','catalogAction')->getFilter();
            if($filter_products)
                return json_decode($filter_products,true);
            else
                return array();
        }
        return array();
    }
    public function deleteFilters($id_employee)
    {
        if(version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $adminFilter = $this->get('prestashop.core.admin.admin_filter.repository');
            $adminFilter->removeByEmployeeAndRouteParams($id_employee,$this->context->shop->id,'ProductController','catalogAction');
        }
        Ets_pmn_filter::deleteFilter($id_employee);
    }
    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        if($posts = Tools::getAllValues())
        {
            if(version_compare(_PS_VERSION_, '1.7.5.0', '>='))
                $adminFilter = $this->get('prestashop.core.admin.admin_filter.repository');
            $filter_params =array();
            if($posts)
            {
                foreach(array_keys($posts) as $key)
                {
                    if(Tools::strpos($key,'filter_column_')===0)
                    {
                        $val =Tools::getValue($key);
                        $filter_params[$key] = Validate::isCleanHtml($val) ? $val:'';
                    }
                }
            }
            if(Tools::isSubmit('filter_category'))
                $filter_params['filter_category'] = (int)Tools::getValue('filter_category');
            if($filter_params)
            {
                if(version_compare(_PS_VERSION_, '1.7.5.0', '>='))
                    $adminFilter->createOrUpdateByEmployeeAndRouteParams($this->context->employee->id,$this->context->shop->id,$filter_params,'ProductController','catalogAction');
                Ets_pmn_filter::updateFilter($this->context->employee->id,$filter_params);
            }
        }
        $list_fields = Ets_pmn_defines::getInstance()->getFieldsByIdEmployee();
        $is_customfield= false;
        if(Module::isEnabled('ets_customfields'))
        {
            $custom_fields = Ets_pmn_defines::getInstance()->getCustomFields();
            foreach(array_keys($custom_fields) as $field)
            {
                if(in_array($field,$list_fields))
                {
                    $is_customfield = true;
                    break;
                }
            }
        }
        $sql_table = array();
        if($filter_products = Ets_pmn_filter::getFilter($this->context->employee->id))
        {
            if(in_array('description_short',$list_fields) && isset($filter_products['filter_column_description_short']) && $filter_products['filter_column_description_short'])
                $params['sql_where'][]= ' pl.description_short LIKE "%'.pSQL($filter_products['filter_column_description_short']).'%"';
            if(in_array('description',$list_fields) && isset($filter_products['filter_column_description']) && $filter_products['filter_column_description'])
                $params['sql_where'][]= ' pl.description LIKE "%'.pSQL($filter_products['filter_column_description']).'%"';
            if(in_array('manufacturers',$list_fields) && isset($filter_products['filter_column_manufacturers']) && $filter_products['filter_column_manufacturers'])
            {
                $params['sql_where'][] ='mu.name like "%'.pSQL($filter_products['filter_column_manufacturers']).'%"';
            }
            if(in_array('suppliers',$list_fields) && isset($filter_products['filter_column_suppliers']) && $filter_products['filter_column_suppliers'])
            {
                $params['sql_where'][] ='su.name like "%'.pSQL($filter_products['filter_column_suppliers']).'%"';
                $sql_table['sup'] = array(
                    'table' => 'product_supplier',
                    'join' => 'LEFT JOIN',
                    'on' => 'sup.`id_product` = p.`id_product`',
                );
                $sql_table['su'] = array(
                    'table' => 'supplier',
                    'join' => 'LEFT JOIN',
                    'on' => 'su.`id_supplier` = sup.`id_supplier`',
                );
                $params['sql_group_by'][] ='p.id_product';
            }
            if(in_array('minimal_quantity',$list_fields) && isset($filter_products['filter_column_minimal_quantity']) && $filter_products['filter_column_minimal_quantity'])
                $params['sql_where'][] = 'p.minimal_quantity '.pSQL($filter_products['filter_column_minimal_quantity'],true);
            if (in_array('location', $list_fields) && isset($filter_products['filter_column_location']) && $filter_products['filter_column_location']) {
                if(version_compare(_PS_VERSION_, '1.7.5', '>=')) {
                    $params['sql_where'][] = 'sax.`location` LIKE "%' . pSQL($filter_products['filter_column_location']) . '%"';
                }
                else
                {
                    $params['sql_where'][] = 'p.`location` LIKE "%' . pSQL($filter_products['filter_column_location']) . '%"';
                }
            }
            if(in_array('associated_file',$list_fields) && isset($filter_products['filter_column_associated_file']) && $filter_products['filter_column_associated_file'])
                $params['sql_where'][] ='pd.display_filename LIKE "%'.pSQL($filter_products['filter_column_associated_file']).'%"';
            if(in_array('available_now',$list_fields) && isset($filter_products['filter_column_available_now']) && $filter_products['filter_column_available_now'])
                $params['sql_where'][] = 'pl.available_now LIKE "%'.pSQL($filter_products['filter_column_available_now']).'%"';
            if(in_array('available_later',$list_fields) && isset($filter_products['filter_column_available_later']) && $filter_products['filter_column_available_later'])
                $params['sql_where'][] ='pl.available_later LIKE "%'.pSQL($filter_products['filter_column_available_later']).'%"';
            if(in_array('tax_name',$list_fields) && isset($filter_products['filter_column_tax_name']) && $filter_products['filter_column_tax_name'])
                $params['sql_where'][] = 'tax.name LIKE "%'.pSQL($filter_products['filter_column_tax_name']).'%"';
            if(in_array('on_sale',$list_fields) && isset($filter_products['filter_column_on_sale']) && $filter_products['filter_column_on_sale']!=='')
            {
                $params['sql_where'][] = 'sa.on_sale ="'.(int)$filter_products['filter_column_on_sale'].'"';
            }
            if(in_array('meta_title',$list_fields) && isset($filter_products['filter_column_meta_title']) && $filter_products['filter_column_meta_title'])
                $params['sql_where'][] = 'pl.meta_title LIKE "%'.pSQL($filter_products['filter_column_meta_title']).'%"';
            if(in_array('meta_description',$list_fields) && isset($filter_products['filter_column_meta_description']) && $filter_products['filter_column_meta_description'])
                $params['sql_where'][] = 'pl.meta_description LIKE "%'.pSQL($filter_products['filter_column_meta_description']).'%"';
            if(in_array('link_rewrite',$list_fields) && isset($filter_products['filter_column_link_rewrite']) && $filter_products['filter_column_link_rewrite'])
                $params['sql_where'][] = 'pl.link_rewrite LIKE "%'.pSQL($filter_products['filter_column_link_rewrite']).'%"';
            if(in_array('redirect_type',$list_fields) && isset($filter_products['filter_column_redirect_type']) && $filter_products['filter_column_redirect_type'])
                $params['sql_where'][] = 'p.redirect_type LIKE "%'.pSQL($filter_products['filter_column_redirect_type']).'%"';
            if(Module::isEnabled('ets_seo'))
            {
                if(in_array('focus_keyphrase',$list_fields) && isset($filter_products['filter_column_focus_keyphrase']) && $filter_products['filter_column_focus_keyphrase'])
                    $params['sql_where'][] = 'esp.key_phrase LIKE "%'.pSQL($filter_products['filter_column_focus_keyphrase']).'%"';
                if(in_array('related_keyphrases',$list_fields) && isset($filter_products['filter_column_related_keyphrases']) && $filter_products['filter_column_related_keyphrases'])
                    $params['sql_where'][] ='esp.minor_key_phrase LIKE "%'.pSQL($filter_products['filter_column_related_keyphrases']).'%"';    
            }
            if(in_array('condition',$list_fields) && isset($filter_products['filter_column_condition']) && $filter_products['filter_column_condition'])
                $params['sql_where'][]  = 'sa.condition LIKE "%'.pSQL($filter_products['filter_column_condition']).'%"';
            if(in_array('isbn',$list_fields) && isset($filter_products['filter_column_isbn']) && $filter_products['filter_column_isbn'])
                $params['sql_where'][] = 'p.isbn LIKE "%'.pSQL($filter_products['filter_column_isbn']).'%"';
            if(in_array('ean13',$list_fields) && isset($filter_products['filter_column_ean13']) && $filter_products['filter_column_ean13'])
                $params['sql_where'][] = 'p.ean13 LIKE "%'.pSQL($filter_products['filter_column_ean13']).'%"';
            if(in_array('upc',$list_fields) && isset($filter_products['filter_column_upc']) && $filter_products['filter_column_upc'])
                $params['sql_where'][] ='p.upc LIKE "%'.pSQL($filter_products['filter_column_upc']).'%"';
            if(in_array('low_stock_threshold',$list_fields) && isset($filter_products['filter_column_low_stock_threshold']) && $filter_products['filter_column_low_stock_threshold'])
                $params['sql_where'][] = 'p.low_stock_threshold LIKE "%'.pSQL($filter_products['filter_column_low_stock_threshold']).'%"';
            if(Module::isEnabled('ph_sortbytrending') && in_array('priority_product',$list_fields) && isset($filter_products['filter_column_priority_product']) && $filter_products['filter_column_priority_product'])
                $params['sql_where'][] = 'sbtp.priority LIKE "%'.pSQL($filter_products['filter_column_priority_product'].'%"');
            if(in_array('width',$list_fields) && isset($filter_products['filter_column_width']) && $filter_products['filter_column_width'])
                $params['sql_where'][] = 'p.width '.pSQL($filter_products['filter_column_width'],true);
            if(in_array('wholesale_price',$list_fields) && isset($filter_products['filter_column_wholesale_price']) && $filter_products['filter_column_wholesale_price'])
                $params['sql_where'][] = 'sa.wholesale_price '.pSQL($filter_products['filter_column_wholesale_price'],true);
            if(in_array('height',$list_fields) && isset($filter_products['filter_column_height']) && $filter_products['filter_column_height'])
                $params['sql_where'][] = 'p.heigth '.pSQL($filter_products['filter_column_height'],true);
            if(in_array('depth',$list_fields) && isset($filter_products['filter_column_depth']) && $filter_products['filter_column_depth'])
                $params['sql_where'][] = 'p.depth '.pSQL($filter_products['filter_column_depth'],true);
            if(in_array('weight',$list_fields) && isset($filter_products['filter_column_weight']) && $filter_products['filter_column_weight'])
                $params['sql_where'][] = 'p.weight '.pSQL($filter_products['filter_column_weight'],true);
            if(in_array('additional_shipping_cost',$list_fields) && isset($filter_products['filter_column_additional_shipping_cost']) && $filter_products['filter_column_additional_shipping_cost'])
                $params['sql_where'][] = 'p.additional_shipping_cost '.pSQL($filter_products['filter_column_additional_shipping_cost'],true);
            if(in_array('delivery_in_stock',$list_fields) && isset($filter_products['filter_column_delivery_in_stock']) && $filter_products['filter_column_delivery_in_stock'])
                $params['sql_where'][] = 'pl.delivery_in_stock LIKE "%'.pSQL($filter_products['filter_column_delivery_in_stock']).'%"';
            if(in_array('delivery_out_stock',$list_fields) && isset($filter_products['filter_column_delivery_out_stock']) && $filter_products['filter_column_delivery_out_stock'])
                $params['sql_where'][] = 'pl.delivery_out_stock LIKE "%'.pSQL($filter_products['filter_column_delivery_out_stock']).'%"';
            if(in_array('additional_delivery_times',$list_fields) && isset($filter_products['filter_column_additional_delivery_times']) && $filter_products['filter_column_additional_delivery_times']!=='')
                $params['sql_where'][] ='p.additional_delivery_times='.(int)$filter_products['filter_column_additional_delivery_times'];
            if(in_array('private_note',$list_fields) && isset($filter_products['filter_column_private_note']) && $filter_products['filter_column_private_note']!=='')
                $params['sql_where'][] ='pn.note LIKE "%'.pSQL($filter_products['filter_column_private_note']).'%"';
            if($is_customfield)
            {
                if(in_array('compatibility',$list_fields) && isset($filter_products['filter_column_compatibility']) && $filter_products['filter_column_compatibility']!=='')
                    $params['sql_where'][] = 'czfp.compatibility LIKE "%'.pSQL($filter_products['filter_column_compatibility']).'%"';
                if(in_array('version',$list_fields) && isset($filter_products['filter_column_version']) && $filter_products['filter_column_version'])
                    $params['sql_where'][] = 'czfp.version LIKE "%'.pSQL($filter_products['filter_column_version']).'%"';
                if(in_array('min_ps_version',$list_fields) && isset($filter_products['filter_column_min_ps_version']) && $filter_products['filter_column_min_ps_version'])
                    $params['sql_where'][] = 'czfp.min_ps_version LIKE "%'.pSQL($filter_products['filter_column_min_ps_version']).'%"';
                if(in_array('max_ps_version',$list_fields) && isset($filter_products['filter_column_max_ps_version']) && $filter_products['filter_column_max_ps_version'])
                    $params['sql_where'][] = 'czfp.max_ps_version LIKE "%'.pSQL($filter_products['filter_column_max_ps_version']).'%"';
                if(in_array('module_name',$list_fields) && isset($filter_products['filter_column_module_name']) && $filter_products['filter_column_module_name'])
                    $params['sql_where'][] = 'czfpl.display_name LIKE "%'.pSQL($filter_products['filter_column_module_name']).'%"';
                if(in_array('module_description',$list_fields) && isset($filter_products['filter_column_module_description']) && $filter_products['filter_column_module_description'])
                    $params['sql_where'][] = 'czfpl.description LIKE "%'.pSQL($filter_products['filter_column_module_description']).'%"';
                if(in_array('fo_link',$list_fields) && isset($filter_products['filter_column_fo_link']) && $filter_products['filter_column_fo_link'])
                    $params['sql_where'][] = 'czfpl.fo_link LIKE "%'.pSQL($filter_products['filter_column_fo_link']).'%"';
                if(in_array('bo_link',$list_fields) && isset($filter_products['filter_column_bo_link']) && $filter_products['filter_column_bo_link'])
                    $params['sql_where'][] = 'czfpl.bo_link LIKE "%'.pSQL($filter_products['filter_column_bo_link']).'%"';
                if(in_array('is_must_have',$list_fields) && isset($filter_products['filter_column_is_must_have']) && $filter_products['filter_column_is_must_have']!=='')
                    $params['sql_where'][] = 'IF(czfp.is_must_have, 1, 0) = "'.(int)$filter_products['filter_column_is_must_have'].'"';
                if(in_array('doc_name',$list_fields) && isset($filter_products['filter_column_doc_name']) && $filter_products['filter_column_doc_name'])
                    $params['sql_where'][] = 'czfpl.doc_display_name LIKE "%'.pSQL($filter_products['filter_column_doc_name']).'%"';
                if(in_array('doc_file',$list_fields) && isset($filter_products['filter_column_doc_file']) && $filter_products['filter_column_doc_file'])
                    $params['sql_where'][] = 'czfpl.doc_file LIKE "%'.pSQL($filter_products['filter_column_doc_file']).'%"'; 
            }
        }
        $sql_select = array(
            'description_short' => array('table' => 'pl', 'field' => 'description_short', 'filtering' => ' %s '),
            'description' => array('table' => 'pl', 'field' => 'description', 'filtering' => ' %s '),
            'minimal_quantity' => array('table'=>'p','field'=>'minimal_quantity', 'filtering' => ' %s '),
            'id_tax_rules_group' => array('table'=>'p','field' => 'id_tax_rules_group'),
            'id_manufacturer' => array('table'=>'p','field' => 'id_manufacturer'),
            'low_stock_threshold' => array('table'=>'p','field'=>'low_stock_threshold', 'filtering' => ' %s '),
            'associated_file' => array('table'=>'pd','field'=>'display_filename', 'filtering' => ' %s '),
            'available_now' => array('table'=>'pl','field'=>'available_now', 'filtering' => ' %s '),
            'available_later' => array('table'=>'pl','field'=>'available_later', 'filtering' => ' %s '),
            'available_date' => array('table'=>'p','field'=>'available_date', 'filtering' => ' %s '),
            'unit_price_ratio' => array('table'=>'sa','field'=>'unit_price_ratio', 'filtering' => ' %s '),
            'price_float' => array('table'=>'sa','field'=>'price', 'filtering' => ' %s '),
            'on_sale' => array('table'=>'sa','field'=>'on_sale', 'filtering' => ' %s '),
            'meta_title' => array('table'=>'pl','field'=>'meta_title', 'filtering' => ' %s '),
            'meta_description' => array('table'=>'pl','field'=>'meta_description', 'filtering' => ' %s '),
            'redirect_type' => array('table'=>'p','field'=>'redirect_type', 'filtering' => ' %s '),
            'visibility' => array('table'=>'sa','field'=>'visibility', 'filtering' => ' %s '),
            'condition' => array('table'=>'sa','field'=>'condition', 'filtering' => ' %s '),
            'isbn' => array('table'=>'p','field'=>'isbn', 'filtering' => ' %s '),
            'ean13' => array('table'=>'p','field'=>'ean13', 'filtering' => ' %s '),
            'upc' => array('table'=>'p','field'=>'upc', 'filtering' => ' %s '),
            'low_stock_alert' => array('table'=>'p','field'=>'low_stock_alert','filtering' => ' %s '),
            'show_price' => array('table'=>'p','field'=>'show_price','filtering' => ' %s '),
            'online_only' => array('table'=>'p','field'=>'online_only','filtering' => ' %s '),
            'show_condition' => array('table'=>'p','field'=>'show_condition','filtering' => ' %s '),
            'width' => array('table'=>'p','field'=>'width', 'filtering' => ' %s '),
            'height' => array('table'=>'p','field'=>'height', 'filtering' => ' %s '),
            'depth' => array('table'=>'p','field'=>'depth', 'filtering' => ' %s '),
            'weight' => array('table'=>'p','field'=>'weight', 'filtering' => ' %s '),
            'available_for_order' => array('table'=>'p','field'=>'available_for_order', 'filtering' => ' %s '),
            'wholesale_price' => array('table'=>'sa','field'=>'wholesale_price', 'filtering' => ' %s '),
            'additional_delivery_times' => array('table'=>'p','field'=>'additional_delivery_times', 'filtering' => ' %s '),
            'delivery_in_stock' => array('table'=>'pl','field'=>'delivery_in_stock', 'filtering' => ' %s '),
            'delivery_out_stock' => array('table'=>'pl','field'=>'delivery_out_stock', 'filtering' => ' %s '),
            'additional_shipping_cost' => array('table'=>'p','field'=>'additional_shipping_cost', 'filtering' => ' %s '),
        );
        if(in_array('location', $list_fields, true) || in_array('sav_quantity', $list_fields, true)){
            if(version_compare(_PS_VERSION_, '1.7.5', '>='))
            {
                $sql_table['sax'] = [
                    'table' => 'stock_available',
                    'join' => 'JOIN',
                    'on' => 'sax.`id_product` = p.`id_product` AND ( sax.`id_product_attribute` = p.`cache_default_attribute` )' .
                        StockAvailable::addSqlShopRestriction(null, $this->context->shop->id, 'sax'),
                ];
                $sql_select['location'] = ['table' => 'sax', 'field' => 'location', 'filtering' => ' LIKE %%%s%% '];

            }
            else
            {
                $sql_select['location'] = ['table' => 'p', 'field' => 'location', 'filtering' => ' LIKE %%%s%% '];
            }
            $sql_table['sax2'] = [
                'table' => 'stock_available',
                'join' => 'JOIN',
                'on' => 'sax2.`id_product` = p.`id_product` AND ( sax2.`id_product_attribute` = 0 )' .
                    StockAvailable::addSqlShopRestriction(null, $this->context->shop->id, 'sax2'),
            ];
            $sql_select['sav_quantity'] = ['table' => 'sax2', 'field' => 'quantity', 'filtering' => ' %s '];
        }
        if(in_array('manufacturers',$list_fields))
        {
            $sql_select['manufacturers'] = array('table'=>'mu','field'=>'name');
            $sql_table['mu'] = array(
                'table' => 'manufacturer',
                'join' => 'LEFT JOIN',
                'on' => 'mu.`id_manufacturer` = p.`id_manufacturer`',
            );
        }
        if(in_array('private_note',$list_fields))
        {
            $sql_select['private_note'] = array('table'=>'pn','field'=>'note');
            $sql_table['pn'] = array(
                'table' => 'ets_pmn_product_note',
                'join' => 'LEFT JOIN',
                'on' => 'pn.`id_product` = p.`id_product`',
            );
        }
        if(in_array('tax_name',$list_fields))
        {
            $sql_select['tax_name'] = array('table'=>'tax','field'=>'name');
            $sql_table['tax'] =  array(
                'table' => 'tax_rules_group',
                'join' => 'LEFT JOIN',
                'on' => 'tax.`id_tax_rules_group` = sa.`id_tax_rules_group`',
            );
        }
        if($is_customfield)
        {
            if(in_array('version',$list_fields))
                $sql_select['version'] = array('table'=>'czfp','field'=>'version','filtering' => ' %s ');
            if(in_array('compatibility',$list_fields))
                $sql_select['compatibility'] = array('table'=>'czfp','field'=>'compatibility','filtering' => ' %s ');
            if(in_array('min_ps_version',$list_fields))
                $sql_select['min_ps_version'] = array('table'=>'czfp','field'=>'min_ps_version','filtering' => ' %s ');
            if(in_array('max_ps_version',$list_fields))
                $sql_select['max_ps_version'] = array('table'=>'czfp','field'=>'max_ps_version','filtering' => ' %s ');
            if(in_array('module_logo',$list_fields))
                $sql_select['module_logo'] = array('table'=>'czfp','field'=>'logo','filtering' => ' %s ');
            if(in_array('module_name',$list_fields))
                $sql_select['module_name'] = array('table'=>'czfpl','field'=>'display_name','filtering' => ' %s ');
            if(in_array('module_description',$list_fields))
                $sql_select['module_description'] = array('table'=>'czfpl','field'=>'description','filtering' => ' %s ');
            if(in_array('fo_link',$list_fields))
                $sql_select['fo_link'] = array('table'=>'czfpl','field'=>'fo_link','filtering' => ' %s ');
            if(in_array('bo_link',$list_fields))
                $sql_select['bo_link'] = array('table'=>'czfpl','field'=>'bo_link','filtering' => ' %s ');
            if(in_array('is_must_have',$list_fields))
                $sql_select['is_must_have'] = array('table'=>'czfp','field'=>'is_must_have','filtering' => ' %s ');
            if(in_array('doc_name',$list_fields))
                $sql_select['doc_name'] = array('table'=>'czfpl','field'=>'doc_name','filtering' => ' %s ');
            if(in_array('doc_name',$list_fields))
            {
                $sql_select['doc_file'] = array('table'=>'czfpl','field'=>'doc_file','filtering' => ' %s ');
                $sql_select['doc_display_name'] = array('table'=>'czfpl','field'=>'doc_display_name','filtering' => ' %s ');  
            }      
            $sql_table['czfp'] =  array(
                'table' => 'ets_czf_product',
                'join' => 'LEFT JOIN',
                'on' => 'czfp.`id_product` = sa.`id_product`',
            );
            $sql_table['czfpl'] =  array(
                'table' => 'ets_czf_product_lang',
                'join' => 'LEFT JOIN',
                'on' => 'czfpl.`id_ets_czf_product` = czfp.`id_ets_czf_product` AND czfpl.id_lang="'.(int)$this->context->language->id.'"',
            );
        }
        if(Module::isEnabled('ets_seo') && ($ets_seo = Module::getInstanceByName('ets_seo')))
        {
            $join = false;
            if(in_array('focus_keyphrase',$list_fields))
            {
                $sql_select['focus_keyphrase'] = array('table' => 'esp','field' => 'key_phrase', 'filtering' => ' %s ');
                $join = true;
            }
            if(in_array('related_keyphrases',$list_fields))
            {
                $sql_select['related_keyphrases'] = array('table' => 'esp','field' => 'minor_key_phrase', 'filtering' => ' %s ');
                $join = true;
            }
            $filter_seo_score = ($filter_seo_score = Tools::getValue('filter_ets_seo_score', '')) && Validate::isCleanHtml($filter_seo_score) ? $filter_seo_score : '';
            $filter_readability_score = ($filter_readability_score = Tools::getValue('filter_ets_seo_readability', '')) && Validate::isCleanHtml($filter_readability_score) ? $filter_readability_score : '';
            if ($join && version_compare($ets_seo->version,'2.6.4','>=') && !$filter_seo_score && !$filter_readability_score) {
                $sql_table['esp'] =  [
                    'table' => 'ets_seo_product',
                    'join' => 'LEFT JOIN',
                    'on' => 'esp.`id_product` = p.`id_product` AND esp.`id_shop` = ' . (int) $this->context->shop->id . ' AND esp.`id_lang` = ' . (int) $this->context->language->id,
                ];
            }
        }
        if(Module::isEnabled('ph_sortbytrending') && in_array('priority_product',$list_fields))
        {
            $sql_select['priority_product'] = array('table'=>'sbtp','field'=>'priority');
            $sql_table['sbtp'] =  array(
                'table' => 'ph_sbt_product_position',
                'join' => 'LEFT JOIN',
                'on' => 'sbtp.`id_product` = sa.`id_product`',
            );
        }
        if($sql_table)
            $params['sql_table'] = array_merge($params['sql_table'],$sql_table);
        $params['sql_select'] = array_merge($params['sql_select'],$sql_select);
        
    }
    public static function getNoImageDefault($type_image)
    {
        $context = Context::getContext();
        if(file_exists(_PS_PROD_IMG_DIR_.$context->language->iso_code.'-default-'.$type_image.'.jpg'))
            return $context->link->getMediaLink(_PS_PROD_IMG_.$context->language->iso_code.'-default-'.$type_image.'.jpg');
        else
        {
            $langDefault = new Language(Configuration::get('PS_LANG_DEFAULT'));
            if(file_exists(_PS_PROD_IMG_DIR_.$langDefault->iso_code.'-default-'.$type_image.'.jpg'))
                return $context->link->getMediaLink(_PS_PROD_IMG_.$langDefault->iso_code.'-default-'.$type_image.'.jpg');
        }
    }
    public function hookActionAdminProductsListingResultsModifier($params)
    {
        if(!$params['products'])
        {
            return '';
        }
//        die(dump($params['products']));
        $products =&$params['products'];
        if($products )
        {
            $list_fields = Ets_pmn_defines::getInstance()->getFieldsByIdEmployee();
            $PS_WEIGHT_UNIT  = Configuration::get('PS_WEIGHT_UNIT');
            foreach($products as $key=> &$product)
            {
                if(!isset($product['id_product']) || !$product['id_product'])
                {
                    unset($products[$key]);
                    continue;
                }
                if(in_array('sav_quantity',$list_fields))
                {
                    $attributes = Product::getAttributesInformationsByProduct($product['id_product']);
                    $product['has_attribute'] = !empty($attributes);
                }

                if(in_array('description_short',$list_fields))
                {
                    $product['description_short_full'] = $product['description_short'];
                    if($this->str_length && Tools::strlen(strip_tags($product['description_short'])) > $this->str_length)
                        $product['description_short'] = Tools::substr(strip_tags($product['description_short']),0,$this->str_length).'...';
                    else
                        $product['description_short'] = strip_tags($product['description_short']);
                }
                if(in_array('description',$list_fields))
                {
                    $product['description_full'] = $product['description'];
                    if($this->str_length && Tools::strlen(strip_tags($product['description'])) > $this->str_length)
                        $product['description'] = Tools::substr(strip_tags($product['description']),0,$this->str_length).'...';
                    else
                        $product['description'] = strip_tags($product['description']);
                }
                if(in_array('categories',$list_fields))
                    $product['categories'] = Ets_pmn_defines::getInstance()->getListCategories($product['id_product']);
                if(in_array('features',$list_fields))
                    $product['features'] = Ets_pmn_defines::getInstance()->getListFeatures($product['id_product']);
                if(in_array('combinations',$list_fields))
                {
                    $product['combinations'] = Ets_pmn_defines::getInstance()->getListCombinations($product['id_product']);
                    if(Pack::isPack($product['id_product']) || $product['is_virtual'])
                        $product['add_combination'] = false;
                    else
                        $product['add_combination'] = true;
                }
                if(in_array('unit_price',$list_fields))
                {
                    $unit_price = $product['unit_price_ratio']!=0 && isset($product['price_float']) ? $product['price_float']/$product['unit_price_ratio'] :0;
                    $product['unit_price_float'] = $unit_price;
                    if($unit_price)
                        $product['unit_price'] = Tools::displayPrice($unit_price);
                    else
                        $product['unit_price'] = Ets_pmn_defines::displayText('--','span',array('class'=>'text-center'));
                }
                if(in_array('price_final',$list_fields))
                {
                    if(!isset($product['price_float']) && isset($product['final_price_tax_excluded']))
                        $product['price_float'] = $product['final_price_tax_excluded'];
                    $product['price_final_float'] = $product['price_final'] = $product['price_float'] + $product['price_float']*$this->getTaxValue($product['id_tax_rules_group']);
                    $product['price_final'] = Tools::displayPrice($product['price_final']);
                }
                if(in_array('wholesale_price',$list_fields))
                {
                    $product['wholesale_price_float'] = $product['wholesale_price'];
                    $product['wholesale_price'] = Tools::displayPrice($product['wholesale_price']);
                }
                if(in_array('additional_shipping_cost',$list_fields))
                {
                    $product['additional_shipping_cost_float'] = $product['additional_shipping_cost'];
                    $product['additional_shipping_cost'] = Tools::displayPrice($product['additional_shipping_cost']);
                }
                if(in_array('width',$list_fields))
                {
                    if($product['width']!=0)
                    {
                        $product['width_float'] = $product['width'];
                        $product['width'] = Tools::ps_round($product['width'],2).'(cm)';
                    }
                    else
                    {
                        $product['width_float'] = '';
                        $product['width'] = '--';
                    }
                }
                if(in_array('height',$list_fields))
                {
                    if($product['height']!=0)
                    {
                        $product['height_float'] = $product['height'];
                        $product['height'] = Tools::ps_round($product['height'],2).'(cm)';
                    }
                    else
                    {
                        $product['height_float'] = '';
                        $product['height'] = '--';
                    }
                }
                if(in_array('depth',$list_fields))
                {
                    if($product['depth']!=0)
                    {
                        $product['depth_float'] = $product['depth'];
                        $product['depth'] = Tools::ps_round($product['depth'],2).'(cm)';
                    }
                    else
                    {
                        $product['depth_float'] = '';
                        $product['depth'] = '--';
                    }
                }
                if(in_array('weight',$list_fields))
                {
                    if($product['weight']!=0)
                    {
                        $product['weight_float'] = $product['weight'];
                        $product['weight'] = Tools::ps_round($product['weight'],2).'('.$PS_WEIGHT_UNIT.')';
                    }
                    else
                    {
                        $product['weight_float'] = '';
                        $product['weight'] = '--';
                    }
                }
                if(in_array('specific_prices',$list_fields))
                    $product['specific_prices'] = Ets_pmn_defines::getInstance()->getListSpecificPrices($product['id_product']);
                if(in_array('tags',$list_fields))
                {
                    $product['tags'] = Ets_pmn_defines::getInstance()->getListtags($product['id_product']);
                }
                if(in_array('attached_files',$list_fields))
                    $product['attached_files'] = Ets_pmn_defines::getInstance()->getListAttachments($product['id_product']);
                if($id_product_dowload = (int)ProductDownload::getIdFromIdProduct($product['id_product']))
                {
                    $download = new ProductDownload($id_product_dowload);
                    if(method_exists($this->context,'getAdminBaseUrl'))
                        $product['link_associated_file'] = $this->context->getAdminBaseUrl().$download->getTextLink(true);
                    else
                        $this->getLinkAdminController('admin_product_virtual_download_file_action',array('idProduct'=>$product['id_product']));
                }
                else
                    $product['link_associated_file']='';
                if(in_array('associated_file',$list_fields) && (!$product['associated_file'] || !$product['is_virtual']))
                    $product['associated_file']='';
                if(in_array('suppliers',$list_fields))
                    $product['suppliers'] = Ets_pmn_defines::getInstance()->getListSuppliers($product['id_product']);
                if(in_array('customization',$list_fields))
                {
                    $product['customization'] = Ets_pmn_defines::getInstance()->getListCustomizations($product['id_product']);
                }
                if(in_array('additional_delivery_times',$list_fields))
                {
                    if($product['additional_delivery_times']==0)
                        $product['additional_delivery_times'] = $this->l('None');
                    elseif($product['additional_delivery_times']==1)
                        $product['additional_delivery_times'] = $this->l('Default delivery time');
                    elseif($product['additional_delivery_times']==2)
                        $product['additional_delivery_times'] = $this->l('Specific delivery time to this product');
                }
                if(isset($product['module_logo']) && $product['module_logo'])
                    $product['module_logo'] = Ets_pmn_defines::displayText('','img',array('href'=>$this->getBaseLink().'/'.$product['module_logo']));

                if(in_array('related_product',$list_fields))
                {
                    $product['related_product'] = Ets_pmn_defines::getInstance()->getRelatedProducts($product['id_product']);
                }
                if(isset($product['fo_link']))
                {
                    $product['link_fo'] = $product['fo_link'];
                    if($product['fo_link'])
                        $product['fo_link'] = Ets_pmn_defines::displayText($product['fo_link'],'a',array('href'=>$product['fo_link'],'target'=>'_blank'));

                }
                else
                    $product['link_fo'] = '';
                if(isset($product['bo_link']))
                {
                    $product['link_bo'] = $product['bo_link'];
                    if($product['bo_link'])
                        $product['bo_link'] = Ets_pmn_defines::displayText($product['bo_link'],'a',array('href'=>$product['bo_link'],'target'=>'_blank'));

                }
                else
                    $product['link_bo'] ='';
                if(isset($product['doc_name']) && isset($product['doc_file']))
                {

                    $product['doc_name'] = $product['doc_file'];
                    if($product['doc_file'])
                        $product['doc_name'] = Ets_pmn_defines::displayText(
                            Ets_pmn_defines::displayText(
                                Ets_pmn_defines::displayText(
                                    Ets_pmn_defines::displayText(
                                    '','path',array('d'=>'M768 384v-128h-128v128h128zm128 128v-128h-128v128h128zm-128 128v-128h-128v128h128zm128 128v-128h-128v128h128zm700-388q28 28 48 76t20 88v1152q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528v-1024h-416q-40 0-68-28t-28-68v-416h-128v128h-128v-128h-512v1536h1280zm-627-721l107 349q8 27 8 52 0 83-72.5 137.5t-183.5 54.5-183.5-54.5-72.5-137.5q0-25 8-52 21-63 120-396v-128h128v128h79q22 0 39 13t23 34zm-141 465q53 0 90.5-19t37.5-45-37.5-45-90.5-19-90.5 19-37.5 45 37.5 45 90.5 19z')),
                                'svg',array('viewBox'=>'0 0 1792 1792','xmlns'=>'http://www.w3.org/2000/svg')),
                                'i',array('class'=>'ets_svg_icon doc-icon doc-icon-zip')).$product['doc_display_name'],
                            'a',array('href'=>$this->context->link->getModuleLink('ets_customfields', 'documentation', array('boDownload'=> 1,'idLang'=>$this->context->language->id,'idProduct'=>$product['id_product']))));

                }
                if(Module::isEnabled('ets_seo') && (in_array('meta_title',$list_fields) || in_array('meta_description',$list_fields)))
                {
                    if(!$product['meta_title'] || (Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE') && Configuration::get('ETS_SEO_PROD_META_TILE',$this->context->language->id)))
                        $product['meta_title'] = Configuration::get('ETS_SEO_PROD_META_TILE',$this->context->language->id);
                    if(!$product['meta_description'] || (Configuration::get('ETS_SEO_PROD_FORCE_USE_META_TEMPLATE') && Configuration::get('ETS_SEO_PROD_META_DESC',$this->context->language->id) ))
                        $product['meta_description'] = Configuration::get('ETS_SEO_PROD_META_DESC',$this->context->language->id);
                }
                elseif(in_array('meta_title',$list_fields) || in_array('meta_description',$list_fields))
                {
                    if(in_array('meta_title',$list_fields))
                    {
                        $product['meta_title_full'] = $product['meta_title'];
                    }
                    if(in_array('meta_description',$list_fields))
                    {
                        $product['meta_description_full'] = $product['meta_description'];
                    }
                }
                if(in_array('selectedCarriers',$list_fields))
                {
                    $product['selectedCarriers'] = $this->displayListCarriers($product['id_product']);
                }
                if(Module::isEnabled('ets_extraproducttabs') && $list_fields)
                {
                    foreach($list_fields as $field)
                    {
                        if(Tools::strpos($field,'extra_product_tab_')===0)
                        {
                            if($id_tab = (int)str_replace('extra_product_tab_','',$field))
                            {
                                $product[$field] = Ets_pmn_defines::getInstance()->getExtraProductTabValue($product['id_product'],$id_tab);
                            }
                            else
                                $product[$field] = '';
                        }
                    }
                }
                if(in_array('tax_name',$list_fields) && !$product['tax_name'])
                {
                    $product['tax_name']= $this->l('No tax');
                }
                if(in_array('redirect_type',$list_fields))
                {
                    switch ($product['redirect_type'])
                    {
                        case '301-category':
                            $product['redirect_type'] = $this->l('Permanent redirection to a category (301)');
                            break;
                        case '302-category':
                            $product['redirect_type'] = $this->l('Temporary redirection to a category (302)');
                            break;
                        case '301-product':
                            $product['redirect_type'] = $this->l('Permanent redirection to a product (301)');
                            break;
                        case '302-product':
                            $product['redirect_type'] = $this->l('Temporary redirection to a product (302)');
                            break;
                        case '404':
                            $product['redirect_type'] = $this->l('No redirection (404)');
                            break;
                        case '410':
                            $product['redirect_type'] = $this->l('No redirection (410)');
                            break;
                        case 'default':
                            $product['redirect_type'] = $this->l('Default behavior from configuration');
                            break;
                        case '200-displayed':
                            $product['redirect_type'] = $this->l('No redirection (200), display product');
                            break;
                        case '404-displayed':
                            $product['redirect_type'] = $this->l('No redirection (404), display product');
                            break;
                        case '410-displayed':
                            $product['redirect_type'] = $this->l('No redirection (410), display product');
                            break;
                    }
                }
                if(in_array('visibility',$list_fields)){
                    switch ($product['visibility'])
                    {
                        case 'both':
                            $product['visibility'] = $this->l('Everywhere');
                            break;
                        case 'catalog':
                            $product['visibility'] = $this->l('Catalog only');
                            break;
                        case 'search':
                            $product['visibility'] = $this->l('Search only');
                            break;
                        case 'none':
                            $product['visibility'] = $this->l('Nowhere');
                            break;
                    }
                }
                if(in_array('condition',$list_fields)){
                    switch ($product['condition'])
                    {
                        case 'new':
                            $product['condition'] = $this->l('New');
                            break;
                        case 'used':
                            $product['condition'] = $this->l('Used');
                            break;
                        case 'refurbished':
                            $product['condition'] = $this->l('Refurbished');
                            break;
                    }
                }
                if(in_array('image',$list_fields) && !$product['image'])
                {
                    $product['image'] = Ets_pmn_defines::displayText('','img',['src' => self::getNoImageDefault(self::getFormatedName('small'))]);
                }
                if(in_array('name',$list_fields) && !$product['name'])
                {
                    $product['name'] = $this->l('N/A');
                }
            }
        }
    }
    public function displayListCarriers($id_product)
    {
        if($carriers = Ets_pmn_defines::getProductCarriers($id_product))
        {
            $text = '';
            foreach($carriers as $carrier)
                $text .= Ets_pmn_defines::displayText($carrier['name'] ? : $this->context->shop->name,'p');
            return $text;
        }
        return '--';
    }
    public function validateFile($file_name,$file_size,&$errors,$file_types=array(),$max_file_size= false)
    {
        if($file_name)
        {
            if(!Validate::isFileName($file_name))
            {
                $errors[] = sprintf($this->l('The file name "%s" is invalid'),$file_name);
            }
            else
            {
                $type = Tools::strtolower(Tools::substr(strrchr($file_name, '.'), 1));
                if(!$file_types)
                    $file_types = $this->file_types;
                if(!in_array($type,$file_types))
                    $errors[] = sprintf($this->l('The file "%s" is not in the correct format, accepted formats: %s'),$file_name,'.'.trim(implode(', .',$file_types),', .'));
                $max_file_size = $max_file_size ? : Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                if($file_size > $max_file_size)
                    $errors[] = sprintf($this->l('The file size of "%s" is too large. Limit: %s'),$file_name,Tools::ps_round($max_file_size/1048576,2).'Mb');
            }
        }
        
    }
    public function getBaseLink()
    {
        $url =(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($url,'/');
    }
    public function isDomain($inputDomainName)
    {
        if (Tools::strpos($inputDomainName, 'http') === 0)
            $domain_validation = '/(http|https)\:\/\/[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
        else
            $domain_validation = '/^[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
        if (preg_match("$domain_validation", $inputDomainName)) {
            return true;
        }
        return false;
    }
    public function getLinkAdminController($entiny,$params=array())
    {
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
        if (null !== $sfContainer) {
            $sfRouter = $sfContainer->get('router');
            return $sfRouter->generate(
                $entiny,
                $params
            );
        }
    }
    public function getTextLang($text, $lang,$file_name='')
    {
        if(is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif(is_object($lang))
            $iso_code = $lang->iso_code;
        else
        {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
		$modulePath = rtrim(_PS_MODULE_DIR_, '/').'/'.$this->name;
        $fileTransDir = $modulePath.'/translations/'.$iso_code.'.'.'php';
        if(!@file_exists($fileTransDir)){
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $text_tras = preg_replace("/\\\*'/", "\'", $text);
        $strMd5 = md5($text_tras);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ? : $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if($matches && isset($matches[2])){
           return  $matches[2];
        }
        return $text;
    }
    public function renderList($listData)
    { 
        if(isset($listData['fields_list']) && $listData['fields_list'])
        {
            foreach($listData['fields_list'] as $key => &$val)
            {
                $value_key = (string)Tools::getValue($key);
                $value_key_max = (string)Tools::getValue($key.'_max');
                $value_key_min = (string)Tools::getValue($key.'_min');
                if(isset($val['filter']) && $val['filter'] && ($val['type']=='int' || $val['type']=='date'))
                {
                    if(Tools::isSubmit('ets_pmn_submit_'.$listData['name']))
                    {
                        $val['active']['max'] =  trim($value_key_max);   
                        $val['active']['min'] =  trim($value_key_min); 
                    }
                    else
                    {
                        $val['active']['max']='';
                        $val['active']['min']='';
                    }  
                }  
                elseif(!Tools::isSubmit('del') && Tools::isSubmit('ets_pmn_submit_'.$listData['name']))               
                    $val['active'] = trim($value_key);
                else
                    $val['active']='';
            }
        }    
        $this->smarty->assign($listData);
        return $this->display(__FILE__, 'list_helper.tpl');
    }
    public function getFilterParams($field_list,$table='')
    {
        $params = '';        
        if($field_list)
        {
            if(Tools::isSubmit('ets_pmn_submit_'.$table))
                $params .='&ets_pmn_submit_'.$table.='=1';
            foreach($field_list as $key => $val)
            {
                $value_key = (string)Tools::getValue($key);
                $value_key_max = (string)Tools::getValue($key.'_max');
                $value_key_min = (string)Tools::getValue($key.'_min');
                if($value_key!='')
                {
                    $params .= '&'.$key.'='.urlencode($value_key);
                }
                if($value_key_max!='')
                {
                    $params .= '&'.$key.'_max='.urlencode($value_key_max);
                }
                if($value_key_min!='')
                {
                    $params .= '&'.$key.'_min='.urlencode($value_key_min);
                } 
            }
            unset($val);
        }
        return $params;
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array) || !$array)
            return true;
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
    public static function createCombinations($list)
    {
        if (count($list) <= 1) {
            return count($list) ? array_map(function ($v) { return array($v); }, $list[0]) : $list;
        }
        $res = array();
        $first = array_pop($list);
        foreach ($first as $attribute) {
            $tab = self::createCombinations($list);
            foreach ($tab as $to_add) {
                $res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
            }
        }
        return $res;
    }
    public static function getProductInfo($product, $id_lang = false)
    {
        $context = Context::getContext();
        if(!$id_lang)
            $id_lang = $context->language->id;
        $id_customer = isset($context->customer->id) && $context->customer->id ? (int)($context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        if($group->price_display_method)
            $tax=false;
        else
            $tax=true;
        if(!is_object($product))
            $product = new Product($product, true, $id_lang, $context->shop->id);
        $pinfo = array();   
        $price = $product->getPrice($tax,null);
        $oldPrice = $product->getPriceWithoutReduct(!$tax,null);
        $pinfo['price'] = Tools::displayPrice($price);       
        $pinfo['old_price'] = Tools::displayPrice($oldPrice); 
        return $pinfo;
    }
    public function displayPaggination($limit,$name)
    {
        if(!$this->isCached('limit.tpl',$this->_getCacheId($limit.'|'.$name)))
        {
            $this->context->smarty->assign(
                array(
                    'limit' => $limit,
                    'pageName' => $name,
                )
            );
        }
        return $this->display(__FILE__,'limit.tpl',$this->_getCacheId($limit.'|'.$name));
    }
    /**
     * @param Ets_pmn_massedit_history $history
     */
    public function renderListLogs($history=false)
    {
        if(!$history)
            $history = Ets_pmn_massedit_history::getInstance();
        $fields_list = array(
            'input_box' => array(
                'title' => '',
                'type' => 'checkbox',
            ),
            'name' => array(
                'title' => $this->l('Mass Edit template'),
                'type' => 'select',
                'sort' => $history->id ? false: true,
                'filter' =>$history->id ? false: true,
                'filter_list' =>$history->id ? false: array(
                    'id_option' => 'id_ets_pmn_massedit',
                    'value' => 'name',
                    'list' => Ets_pmn_massedit::getAllMassedit(),
                )
            ),
            'product_name' => array(
                'title' => $this->l('Product'),
                'type' => 'text',
                'sort' =>$history->id ? false: true,
                'filter' =>$history->id ? false: true,
                'strip_tag' => false,
            ),
            'field_name' => array(
                'title' => $this->l('Field name'),
                'type' => 'text',
                'sort' =>$history->id ? false: true,
                'filter' =>$history->id ? false: true,
            ),
            'lang_name' => array(
                'title' => $this->l('Language'),
                'type' => 'select',
                'sort' =>$history->id ? false: true,
                'filter' =>$history->id ? false: true,
                'filter_list' => array(
                    'id_option' => 'id_lang',
                    'value' => 'name',
                    'list' => array_merge(array(array('id_lang'=>0,'name'=>$this->l('No language'))),Language::getLanguages(false)),
                )
            ),
            'old_value' => array(
                'title' => $this->l('Old value'),
                'type' => 'text',
                'strip_tag'=>false,
            ),
            'new_value' => array(
                'title' => $this->l('New value'),
                'type' => 'text',
                'strip_tag'=>false,
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'date',
                'sort' => $history->id ? false: true,
                'filter' =>$history->id ? false: true,
            ),
        );
        $show_resset = false;
        if($history->id)
        {
            $filter = '  AND h.id_ets_pmn_massedit_history ='.(int)$history->id;
            unset($fields_list['input_box']);
        }
        else
            $filter = "";
        if(Tools::isSubmit('ets_pmn_submit_pmn_logmassedit'))
        {
            if(($id_ets_pmn_massedit_history_max = Tools::getValue('id_ets_pmn_massedit_history_max')) || trim($id_ets_pmn_massedit_history_max)!='')
            {
                if(Validate::isInt($id_ets_pmn_massedit_history_max))
                    $filter .=' AND h.id_ets_pmn_massedit_history <='.(int)$id_ets_pmn_massedit_history_max;
                $show_resset = true;
            }
            if(($name = Tools::getValue('name')) || $name!='')
            {
                if(Validate::isUnsignedInt($name))
                    $filter .=' AND m.id_ets_pmn_massedit = "'.(int)$name.'"';
                $show_resset = true;
            }
            if(($product_name = Tools::getValue('product_name')) || $product_name!='')
            {
                if(Validate::isCleanHtml($product_name))
                    $filter .= ' AND pl.name LIKE "%'.pSQL($product_name).'%"';
                $show_resset = true;
            }
            if(($field_name = Tools::getValue('field_name')) || $field_name!='')
            {
                if(Validate::isCleanHtml($field_name))
                    $filter .= ' AND hp.field_name LIKE "%'.pSQL($field_name).'%"';
                $show_resset = true;
            }
            if(($lang_name = Tools::getValue('lang_name')) || $lang_name!='')
            {
                if(Validate::isUnsignedInt($lang_name))
                    $filter .= ' AND hp.id_lang = "'.(int)$lang_name.'"';
                $show_resset = true;
            }
            if(($date_add_min = Tools::getValue('date_add_min')) || $date_add_min!='')
            {
                if(Validate::isDate($date_add_min))
                    $filter .= ' AND hp.date_add >= "'.pSQL($date_add_min).'"';
                $show_resset = true;
            }
            if(($date_add_max = Tools::getValue('date_add_max')) || $date_add_max!='')
            {
                if(Validate::isDate($date_add_max))
                    $filter .= ' AND hp.date_add <= "'.pSQL($date_add_max).'"';
                $show_resset = true;
            }
        }
        $sort = "";
        $sort_type = Tools::getValue('sort_type','desc');
        $sort_value = Tools::getValue('sort','date_add');
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ets_pmn_massedit_history':
                    $sort .='h.id_ets_pmn_massedit_history';
                    break;
                case 'name':
                    $sort .='m.name';
                    break;
                case 'product_name':
                    $sort .='pl.name';
                    break;
                case 'lang_name':
                    $sort .='l.name';
                    break;
                case 'field_name':
                    $sort .='hp.field_name';
                    break;
                case 'date_add':
                    $sort .='hp.date_add';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);
        }
        $page = (int)Tools::getValue('page');
        if($page<=0)
            $page = 1;
        $totalRecords = (int) $history->getListLogMassedit($filter,0,0,'',true);
        $paggination = new Ets_pmn_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEditLog').'&id_ets_pmn_massedit_history='.(int)$history->id.'&page=_page_'.$this->getFilterParams($fields_list,'pmn_logmassedit');
        if(!$history->id)
        {
            $paggination->limit =  (int)Tools::getValue('paginator_log_select_limit',20);
            $paggination->name ='log';
            $paggination->select_limit = true;
        }
        else
            $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $logmassedits = $history->getListLogMassedit($filter,$start,$paggination->limit,$sort);
        if($logmassedits)
        {
            $fields = Ets_pmn_massedit_condition_action::getInstance()->getConditionFields();
            foreach($logmassedits as &$logmassedit)
            {
                $logmassedit['product_name'] = Ets_pmn_defines::displayText($logmassedit['product_name'],'a',array('href' => Context::getContext()->link->getAdminLink('AdminProducts',true,array('id_product'=>$logmassedit['id_product']))));
                switch($logmassedit['field_name']){
                    case 'features':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListFeatures(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListFeatures(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'id_categories':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListCategories(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListCategories(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'related_products':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListProducts(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListProducts(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'selectedCarriers':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListCarriers(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListCarriers(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'combinations':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListCombinations(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListCombinations(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'customization':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListCustomization(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListCustomization(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'id_category_default':
                        $id_new = (int)$logmassedit['new_value'];
                        $id_old = (int)$logmassedit['old_value'];
                        if($id_new && !isset(self::$categories[$id_new]))
                            self::$categories[$id_new] = new Category($id_new,Context::getContext()->language->id);
                        if($id_old && !isset(self::$categories[$id_old]))
                            self::$categories[$id_old] = new Category($id_old,Context::getContext()->language->id);
                        $logmassedit['new_value'] = $id_new ? self::$categories[$id_new]->name:'';
                        $logmassedit['old_value'] = $id_old ? self::$categories[$id_old]->name:'';
                        break;
                    case 'id_manufacturer':
                        $id_new = (int)$logmassedit['new_value'];
                        $id_old = (int)$logmassedit['old_value'];
                        if($id_new && !isset(self::$manufacturers[$id_new]))
                            self::$manufacturers[$id_new] = new Manufacturer($id_new);
                        if($id_old && !isset(self::$manufacturers[$id_old]))
                            self::$manufacturers[$id_old] = new Manufacturer($id_old);
                        $logmassedit['new_value'] = $id_new ? self::$manufacturers[$id_new]->name:'';
                        $logmassedit['old_value'] = $id_old ? self::$manufacturers[$id_old]->name :'';
                        break;
                    case 'stocks':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListStocks(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListStocks(json_decode($logmassedit['new_value'],true)):'';
                        $logmassedit['field_name'] = $this->l('Stocks');
                        break;
                    case 'specific_prices':
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListSpecificPrices(json_decode($logmassedit['old_value'],true)):'';
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListSpecificPrices(json_decode($logmassedit['new_value'],true)):'';
                        break;
                    case 'additional_delivery_times':
                        if(!$logmassedit['old_value'])
                            $logmassedit['old_value'] = $this->l('None');
                        elseif($logmassedit['old_value']==1)
                            $logmassedit['old_value'] = $this->l('Default delivery time');
                        elseif($logmassedit['old_value']==2)
                            $logmassedit['old_value'] = $this->l('Specific delivery time to this product');
                        if(!$logmassedit['new_value'])
                            $logmassedit['new_value'] = $this->l('None');
                        elseif($logmassedit['new_value']==1)
                            $logmassedit['new_value'] = $this->l('Default delivery time');
                        elseif($logmassedit['new_value']==2)
                            $logmassedit['new_value'] = $this->l('Specific delivery time to this product');
                        break;
                    case 'id_tax_rules_group':
                        $id_new = (int)$logmassedit['new_value'];
                        $id_old = (int)$logmassedit['old_value'];
                        if($id_new && !isset(self::$tax_rules[$id_new]))
                            self::$tax_rules[$id_new] = new TaxRulesGroup($id_new,Context::getContext()->language->id);
                        if($id_old &&!isset(self::$tax_rules[$id_old]))
                            self::$tax_rules[$id_old] = new TaxRulesGroup($id_old,Context::getContext()->language->id);
                        $logmassedit['new_value'] = $id_new ? self::$tax_rules[$id_new]->name :$this->l('No tax');
                        $logmassedit['old_value'] = $id_old ? self::$tax_rules[$id_old]->name : $this->l('No tax');
                        break;
                    case 'price':
                    case 'unit_price':
                    case 'wholesale_price':
                    case 'additional_shipping_cost':
                        $logmassedit['old_value'] = Tools::displayPrice($logmassedit['old_value']);
                        $logmassedit['new_value'] = Tools::displayPrice($logmassedit['new_value']);
                        break;
                    case 'width':
                    case 'height':
                    case 'depth':
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $logmassedit['new_value'].$this->l('(cm)') :'';
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $logmassedit['old_value'].$this->l('(cm)'):'';
                        break;
                    case 'weight':
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $logmassedit['new_value'].$this->l('(kg)') :'';
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $logmassedit['old_value'].$this->l('(kg)'):'';
                        break;
                    case 'on_sale':
                    case 'online_only':
                    case 'low_stock_alert':
                    case 'active':
                    case 'available_for_order':
                    case 'show_condition':
                    case 'show_price':
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $this->l('Yes') : $this->l('No');
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $this->l('Yes') : $this->l('No');
                        break;
                    case 'tags':
                        $logmassedit['new_value'] = $logmassedit['new_value'] ? $history->displayListTags(json_decode($logmassedit['new_value'],true)):'';
                        $logmassedit['old_value'] = $logmassedit['old_value'] ? $history->displayListTags(json_decode($logmassedit['old_value'],true)):'';
                        break;
                    case 'visibility':
                        if($logmassedit['old_value']=='both')
                            $logmassedit['old_value'] = $this->l('Everywhere');
                        elseif($logmassedit['old_value']=='catalog')
                            $logmassedit['old_value'] = $this->l('Catalog only');
                        elseif($logmassedit['old_value']=='search')
                            $logmassedit['old_value'] = $this->l('Search only');
                        elseif($logmassedit['old_value']=='none')
                            $logmassedit['old_value'] = $this->l('Nowhere');
                        if($logmassedit['new_value']=='both')
                            $logmassedit['new_value'] = $this->l('Everywhere');
                        elseif($logmassedit['new_value']=='catalog')
                            $logmassedit['new_value'] = $this->l('Catalog only');
                        elseif($logmassedit['new_value']=='search')
                            $logmassedit['new_value'] = $this->l('Search only');
                        elseif($logmassedit['new_value']=='none')
                            $logmassedit['new_value'] = $this->l('Nowhere');
                        break;
                    case 'condition':
                        if($logmassedit['old_value']=='new')
                            $logmassedit['old_value'] = $this->l('New');
                        elseif($logmassedit['old_value']=='used')
                            $logmassedit['old_value'] = $this->l('Used');
                        elseif($logmassedit['old_value']=='refurbished')
                            $logmassedit['old_value'] = $this->l('Refurbished');
                        elseif($logmassedit['old_value']=='none')
                            $logmassedit['old_value'] = $this->l('Nowhere');
                        if($logmassedit['new_value']=='new')
                            $logmassedit['new_value'] = $this->l('New');
                        elseif($logmassedit['new_value']=='used')
                            $logmassedit['new_value'] = $this->l('Used');
                        elseif($logmassedit['new_value']=='refurbished')
                            $logmassedit['new_value'] = $this->l('Refurbished');
                        elseif($logmassedit['new_value']=='none')
                            $logmassedit['new_value'] = $this->l('Nowhere');
                        break;
                    default:
                        $new_value = strip_tags($logmassedit['new_value']);
                        $old_value = strip_tags($logmassedit['old_value']);
                        $logmassedit['new_value'] = $new_value;
                        $logmassedit['old_value'] = $old_value;
                }
                $logmassedit['field_name'] = isset($fields[$logmassedit['field_name']]) ? $fields[$logmassedit['field_name']] : $logmassedit['field_name'];
                if(!$logmassedit['name'])
                {
                    $logmassedit['name'] = $this->l('--');
                }
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        if($history->id)
        {
            $massedit = new Ets_pmn_massedit($history->id_ets_pmn_massedit);
        }
        $listData = array(
            'name' => $history->id ? 'logs' : 'pmn_logmassedit',
            'icon' => 'fa fa-logmassedits',
            'actions' => array('restore','delete'),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEditLog').'&id_ets_pmn_massedit_history='.(int)$history->id.($paggination->limit!=20 ? '&paginator_log_select_limit='.$paggination->limit:''),
            'identifier' => 'id_ets_pmn_massedit_history_product',
            'show_toolbar' => $history->id ? false: true,
            'show_action' =>$history->id ? false: true,
            'title' => ($history->id ? $this->l('View edit log').($massedit->name ? ' #'.$massedit->name :'') : $this->l('Mass Edit log')),
            'fields_list' => $fields_list,
            'field_values' => $logmassedits,
            'paggination' => $paggination->render(),
            'filter_params' => $this->getFilterParams($fields_list,'pmn_logmassedit'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_value,
            'show_add_new'=> false,
            'link_new' => false,
            'link_delete_all' => $history->id ? false : Context::getContext()->link->getAdminLink('AdminProductManagerMassiveEditLog').'&clearAllLog=1',
            'sort_type' => $sort_type,
            'view_more_content' => true,
        );
        return $this->renderList($listData);
    }
    public function hookActionProductUpdate($params)
    {
        if(version_compare(_PS_VERSION_,'8.0.0','>=') && isset($params['product']) && ($product = $params['product']) && $product->state==0)
        {
            Ets_pmn_defines::updateNewProduct($product->id);
        }
    }
    public function _getCacheId($params = null)
    {
        $cacheId = $this->getCacheId($this->name);
        $cacheId = str_replace($this->name, '', $cacheId);
        $suffix ='';
        if($params)
        {
            if(is_array($params))
                $suffix .= '|'.implode('|',$params);
            else
                $suffix .= '|'.$params;
        }
        return $this->name . $suffix . $cacheId.'|'.date('ymd');
    }
    public function _clearCache($template,$cache_id= null, $compile_id = null)
    {
        if($cache_id===null)
            $cache_id = $this->name;
        if($template=='*')
        {
            return Tools::clearCache(Context::getContext()->smarty, null, $cache_id, $compile_id);
        }
        else
        {
            return Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
        }
    }
    public function checkCreatedColumn($table,$column)
    {
        $fieldsCustomers = Ets_pmn_defines::getColumns($table);
        $check_add=false;
        foreach($fieldsCustomers as $field)
        {
            if($field['Field']==$column)
            {
                $check_add=true;
                break;
            }
        }
        return $check_add;
    }
}
        