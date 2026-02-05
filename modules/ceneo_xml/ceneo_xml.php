<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2023, Ceneo
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use CeneoXml\Helper\Html;
use CeneoXml\LegacyCeneoAttributeRepository;
use CeneoXml\LegacyCeneoCategoryRepository;
use CeneoXml\LegacyProductSettingsRepository;
use CeneoXml\Utils\Helper;
use CeneoXml\Utils\TabManager;

class Ceneo_Xml extends Module
{
    public $displayName;
    public $description;
    private $legacyCeneoCategoryRepository;
    private $legacyCeneoAttributeRepository;
    private $legacyProductSettingsRepository;
    public $exclude_inactive;
    public $exclude_oos;
    public $exclude_by_price_max;
    public $merge_combinations;
    public $ips;
    public $regenerate;
    public $stock_management;
    public $order_out_of_stock;
    public $advanced_stock_management;
    public $avail;
    public $basket;
    public $last;
    private $_html = '';
    private $_postErrors = [];
    private $js_path;
    private $css_path;

    public function __construct()
    {
        $this->name = 'ceneo_xml';
        $this->tab = 'front_office_features';
        $this->version = '1.0.8';
        $this->author = 'Ceneo';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->is_configurable = true;

        $this->module_key = '8920184e8dcd1b7af367640e2ff8c115';

        parent::__construct();

        $this->displayName = $this->l('Ceneo XML feed');
        $this->description = $this->l('Generates XML feed for Ceneo');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->legacyCeneoAttributeRepository = new LegacyCeneoAttributeRepository(
            Db::getInstance(),
            $this->context->shop
        );
        $this->legacyCeneoCategoryRepository = new LegacyCeneoCategoryRepository(
            Db::getInstance(),
            $this->context->shop
        );
        $this->legacyProductSettingsRepository = new LegacyProductSettingsRepository(
            Db::getInstance(),
            $this->context->shop
        );

        $this->exclude_inactive = Configuration::get('CENEO_XML_EXCLUDE_INACTIVE');
        $this->exclude_oos = Configuration::get('CENEO_XML_EXCLUDE_OOS');
        $this->exclude_by_price_max = Configuration::get('CENEO_XML_EXCLUDE_BY_PRICE_MAX');
        $this->merge_combinations = Configuration::get('CENEO_XML_MERGE_COMBINATIONS');
        $this->ips = Configuration::get('CENEO_XML_IPS');
        $this->regenerate = Configuration::get('CENEO_XML_REGENERATE');
        $this->basket = Configuration::get('CENEO_XML_BASKET');
        $this->avail = Configuration::get('CENEO_XML_AVAIL');
        $this->last = Configuration::get('CENEO_XML_LAST');

        $this->order_out_of_stock = (int) Configuration::get('PS_ORDER_OUT_OF_STOCK');
        $this->stock_management = (int) Configuration::get('PS_STOCK_MANAGEMENT');
        $this->advanced_stock_management = (int) Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

        $this->js_path = $this->_path . 'views/js/';
        $this->css_path = $this->_path . 'views/css/';
    }

    private function _postProcess()
    {
        $avail_before = Configuration::get('CENEO_XML_AVAIL');
        $basket_before = Configuration::get('CENEO_XML_BASKET');
        if (Configuration::updateValue('CENEO_XML_EXCLUDE_INACTIVE', Tools::getValue('CENEO_XML_EXCLUDE_INACTIVE'))
            && Configuration::updateValue('CENEO_XML_EXCLUDE_OOS', Tools::getValue('CENEO_XML_EXCLUDE_OOS'))
            && Configuration::updateValue('CENEO_XML_EXCLUDE_BY_PRICE_MAX', Tools::getValue('CENEO_XML_EXCLUDE_BY_PRICE_MAX'))
            && Configuration::updateValue('CENEO_XML_MERGE_COMBINATIONS', Tools::getValue('CENEO_XML_MERGE_COMBINATIONS'))
            && Configuration::updateValue('CENEO_XML_AVAIL', Tools::getValue('CENEO_XML_AVAIL'))
            && Configuration::updateValue('CENEO_XML_BASKET', Tools::getValue('CENEO_XML_BASKET'))
            && Configuration::updateValue('CENEO_XML_REGENERATE', Tools::getValue('CENEO_XML_REGENERATE'))) {
            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            if ($basket_before != Configuration::get('CENEO_XML_BASKET')) {
                Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'ceneo_xml_product_settings set basket = 2');
            }
            if ($avail_before != Configuration::get('CENEO_XML_AVAIL')) {
                Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'ceneo_xml_product_settings set avail = ' .
                    Configuration::get('CENEO_XML_AVAIL'));
            }
        } else {
            $this->_html .= $this->displayError($this->l('Settings failed'));
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $html_helper = new Html();

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $this->_html .= $html_helper->displayInfoHeader();
                $this->_html .= $this->renderForm();
            } else {
                $this->_html .= $html_helper->displayShopHeader();
            }
        } else {
            $this->_html .= $html_helper->displayInfoHeader();
            $this->_html .= $this->renderForm();
        }

        $this->_html .= '<br />';

        return $this->_html;
    }

    public function hookActionAdminControllerSetMedia()
    {
        $controller = Dispatcher::getInstance()->getController();

        if ($controller !== 'CeneoXmlCategoryMapping' && !(Tools::getIsset('configure')
                && Tools::getValue('configure') == 'ceneo_xml')) {
            return;
        }

        $all_attributes = AttributeGroup::getAttributesGroups(1);
        $shop_attributes = [];
        foreach ($all_attributes as $group) {
            $shop_attributes[] = ['id' => 'attribute-' . $group['id_attribute_group'], 'name' => $group['name']];
        }

        $all_features = Feature::getFeatures(1);
        foreach ($all_features as $feature) {
            $shop_attributes[] = ['id' => 'feature-' . $feature['id_feature'], 'name' => $feature['name']];
        }

        Media::addJsDef(
            [
                'shop_attributes' => json_encode($shop_attributes),
                'ceneo_xml_ajax' => Context::getContext()->link->getModuleLink('ceneo_xml', 'ajax'),
            ]
        );

        $this->context->controller->addCSS($this->css_path . 'select2.min.css');
        $this->context->controller->addCSS($this->css_path . 'style.css');
        $this->context->controller->addJS($this->js_path . 'select2.min.js');
        $this->context->controller->addJS($this->js_path . 'file-form.js');
    }

    public function renderForm()
    {
        $days_range_select = [];
        $days_range = range(0, 21);
        foreach ($days_range as $d) {
            $days_range_select[] = ['id' => $d, 'name' => $d];
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Ceneo.pl Feed XML'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Exclude inactive'),
                        'desc' => $this->l('Exclude inactive products'),
                        'name' => 'CENEO_XML_EXCLUDE_INACTIVE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'inactive_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'inactive_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Exclude out of stock'),
                        'desc' => $this->l('Exclude out of stock products. Only when stock management enabled.'),
                        'name' => 'CENEO_XML_EXCLUDE_OOS',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'oos_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'oos_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Exclude products cheaper than'),
                        'desc' => $this->l('Exclude products cheaper than'),
                        'name' => 'CENEO_XML_EXCLUDE_BY_PRICE_MAX',
                        'size' => 225,
                        'required' => false,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Combine variants into one product'),
                        'desc' => $this->l('Combine variants into one product'),
                        'name' => 'CENEO_XML_MERGE_COMBINATIONS',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'merge_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'merge_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Visibility of the offer in the Ceneo Buy Now service'),
                        'desc' => $this->l('Changing this field will reset the settings on the product card'),
                        'name' => 'CENEO_XML_BASKET',
                        'required' => false,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'basket_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'basket_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Default products availability'),
                        'desc' => $this->l('Changing this field will reset the settings on the product card'),
                        'name' => 'CENEO_XML_AVAIL',
                        'required' => false,
                        'options' => [
                            'query' => Helper::getAvailabilitiesLabels($this->stock_management, $this),
                            'id' => 'key',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save settings'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function installContext()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return true;
    }

    public function getConfigFieldsValues(): array
    {
        $settings = [
            'CENEO_XML_EXCLUDE_INACTIVE',
            'CENEO_XML_EXCLUDE_OOS',
            'CENEO_XML_EXCLUDE_BY_PRICE_MAX',
            'CENEO_XML_IPS',
            'CENEO_XML_REGENERATE',
            'CENEO_XML_MERGE_COMBINATIONS',
            'CENEO_XML_AVAIL',
            'CENEO_XML_BASKET',
        ];

        $return = [];

        foreach ($settings as $setting) {
            $return[$setting] = Tools::getValue($setting, Configuration::get($setting));
        }

        return $return;
    }

    public function installTabs(): bool
    {
        $moduleName = $this->name;

        TabManager::addTab('AdminCeneoClass',
            [
                'pl' => 'Ceneo',
                'en' => 'Ceneo',
            ],
            $moduleName,
            'AdminTools',
            'settings'
        );
        TabManager::addTab('CeneoXmlConfig',
            [
                'pl' => 'Feed Xml',
                'en' => 'Feed Xml',
            ],
            $moduleName,
            'AdminCeneoClass'
        );
        TabManager::addTab('CeneoXmlEditProducts',
            [
                'pl' => 'Masowa edycja produktów',
                'en' => 'Mass edit products',
            ],
            $moduleName,
            'AdminCeneoClass'
        );
        TabManager::addTab('CeneoXmlCategoryMapping',
            [
                'pl' => 'Mapowanie kategorii',
                'en' => 'Category Mapping',
            ],
            $moduleName,
            'AdminCeneoClass'
        );

        return true;
    }

    public function uninstallTabs(): bool
    {
        TabManager::removeTab('CeneoXmlConfig');
        TabManager::removeTab('CeneoXmlEditProducts');
        TabManager::removeTab('CeneoXmlCategoryMapping');
        TabManager::removeTab('AdminCeneoClass');

        return true;
    }

    public function translateStrings()
    {
        $this->l('Products');
        $this->l('ID');
        $this->l('Name');
        $this->l('Price');
        $this->l('Reference');
        $this->l('Category');
        $this->l('Yes');
        $this->l('No');
        $this->l('Settings updated');
        $this->l('Settings failed');
        $this->l('Exclude inactive');
        $this->l('Exclude inactive products');
        $this->l('Exclude out of stock');
        $this->l('Exclude out of stock products. Only when stock management enabled.');
        $this->l('Exclude products cheaper than');
        $this->l('Combine variants into one product');
        $this->l('Default products availability');
        $this->l('Save settings');
        $this->l('Ceneo.pl Feed XML');
        $this->l('Settings');
        $this->l('Ceneo');
        $this->l('Mass edit products');
        $this->l('Category Mapping');
        $this->l('Ceneo XML feed');
        $this->l('Generates XML feed for Ceneo');
        $this->l('Are you sure you want to uninstall?');
        $this->l('Ceneo XML feed');
        $this->l('Visibility');
        $this->l('Default');
        $this->l('Status');
        $this->l('Active');
        $this->l('Inactive');
        $this->l('Enabled');
        $this->l('Yes');
        $this->l('No');
        $this->l('Save');
        $this->l('Enable in feed Ceneo');
        $this->l('Disable in feed Ceneo');
        $this->l('Change the visibility on Ceneo Buy Now');
        $this->l('Change product availability on Ceneo');
        $this->l('Set default values');
        $this->l('Mapping could not be edited.');
        $this->l('Products have been enabled successfully.');
        $this->l('Products have been disabled successfully.');
        $this->l('Default values have been set.');
        $this->l('Default values not set.');
        $this->l('Products have been visible successfully.');
        $this->l('Products have been hidden successfully.');
        $this->l('Products have been availability status successfully.');
        $this->l('Product listing');

        $this->l('Visibility of the offer in the Ceneo Buy Now service');
        $this->l('Changing this field will reset the settings on the product card');

        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "check availability".');
        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to 3 days" (the store will send the product up to 3 days).');
        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to a week" (the store will send the product within a week).');
        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to 14 days" (the store will send the product up to 14 days).');
        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "on order".');
        $this->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "on pre-order".');
        $this->l('Set the availability for all products to: "available" (the store will ship the product within 24 hours).');
        $this->l('Set the availability for all products to: "available up to 3 days" (the store will ship the product up to 3 days).');
        $this->l('Set the availability for all products to: "available available up to a week" (the store will ship the product within a week).');
        $this->l('Set the availability for all products to: "available up to 14 days" (the store will ship the product up to 14 days).');
        $this->l('Set the availability for all products to: "on demand".');
        $this->l('Set the availability for all products to: "on pre-order".');
        $this->l('Set the availability for all products to: "check availability".');

        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "check availability".');
        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to 3 days" (the store will send the product up to 3 days).');
        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to a week" (the store will send the product within a week).');
        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to 14 days" (the store will send the product up to 14 days).');
        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "on order".');
        $this->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "on pre-order".');
        $this->l('Set availability to: "available" (the store will ship the product within 24 hours).');
        $this->l('Set availability to: "available up to 3 days" (the store will ship the product up to 3 days).');
        $this->l('Set availability to: "available up to a week" (the store will ship the product within a week).');
        $this->l('Set availability to: "available up to 14 days" (the store will ship the product up to 14 days).');
        $this->l('Set availability to: "on demand".');
        $this->l('Set availability to: "on pre-sale".');
        $this->l('Set availability to: "check availability".');
        $this->l('Set the availability for all products to: "available up to a week" (the store will ship the product within a week).');
        $this->l('Set the availability for all products to: "on order".');
        $this->l('Set availability to: "on order".');
        $this->l('Mapping saved.');
        $this->l('offers');
        $this->l('-- Choose --');
        $this->l('Visibility in the shop');
    }

    public function install()
    {
        return parent::install()
            && $this->installTabs()
            && $this->installContext()
            && $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
            && $this->registerHook('displayAdminProductsCombinationBottom')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->installDB()
            && $this->initDefaultConfiguration();
    }

    public function initDefaultConfiguration()
    {
        $configValues = [
            'CENEO_XML_EXCLUDE_INACTIVE' => '1',
            'CENEO_XML_EXCLUDE_OOS' => '1',
            'CENEO_XML_COUNT' => '0',
            'CENEO_XML_EXCLUDE_BY_PRICE_MAX' => '0',
            'CENEO_XML_REGENERATE' => '0',
            'CENEO_XML_IPS' => '',
            'CENEO_XML_AVAIL' => '1',
            'CENEO_XML_BASKET' => '1',
            'CENEO_XML_LAST' => '0000-00-00 00:00:00',
            'CENEO_XML_MERGE_COMBINATIONS' => '1',
        ];

        foreach ($configValues as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        return true;
    }

    public function removeConfigurationSettings()
    {
        $configNames = [
            'CENEO_XML_EXCLUDE_INACTIVE',
            'CENEO_XML_EXCLUDE_OOS',
            'CENEO_XML_EXCLUDE_BY_PRICE_MAX',
            'CENEO_XML_REGENERATE',
            'CENEO_XML_COUNT',
            'CENEO_XML_IPS',
            'CENEO_XML_AVAIL',
            'CENEO_XML_BASKET',
            'CENEO_XML_LAST',
            'CENEO_XML_MERGE_COMBINATIONS',
        ];

        foreach ($configNames as $configName) {
            Configuration::deleteByName($configName);
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallDB()
            && $this->uninstallTabs()
            && $this->removeConfigurationSettings();
    }

    public function installDB()
    {
        return $this->legacyProductSettingsRepository->createTables()
            && $this->legacyCeneoAttributeRepository->createTables()
            && $this->legacyCeneoAttributeRepository->installFixtures()
            && $this->legacyCeneoCategoryRepository->createTables()
            && $this->legacyCeneoCategoryRepository->installFixtures();
    }

    public function uninstallDB()
    {
        return $this->legacyCeneoAttributeRepository->dropTables()
            && $this->legacyCeneoCategoryRepository->dropTables()
            && $this->legacyProductSettingsRepository->dropTables();
    }

    public function hookActionProductUpdate(array $params)
    {
        $id = $params['id_product'];
        $return = true;

        $this->legacyProductSettingsRepository->getByIdProduct($id);
        if (!Tools::getIsset('exclude')) {
            $return = $this->legacyProductSettingsRepository->setExcludeByIdProduct($id, null, 0);
        } else {
            $return = $this->legacyProductSettingsRepository->setExcludeByIdProduct($id, null, 1);
        }

        if (Tools::getIsset('basket')) {
            $return = $this->legacyProductSettingsRepository->setBasketByIdProduct($id, Tools::getValue('basket'));
        }

        if (Tools::getIsset('avail') && $avail = Tools::getValue('avail')) {
            $return = $this->legacyProductSettingsRepository->setAvailByIdProduct($id, $avail);
        }

        return $return;
    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $info = [];
        if (isset($params['id_product']) && $product = new Product($params['id_product'])) {
            $id_default_category = $product->id_category_default;
            $mapping = Db::getInstance()->getValue('select categories from ' . _DB_PREFIX_
                . 'ceneo_xml_category_mapping where id_mapping = 1');
            if ($mapping) {
                $categories = json_decode($mapping, 1);
                if (isset($categories[$id_default_category]) && $categories[$id_default_category]) {
                    $mandatory_attributes = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_
                        . 'ceneo_xml_attribute where is_key_attribute = "True" and id_ceneo_category = ' .
                        (int) $categories[$id_default_category]);
                    if ($mandatory_attributes) {
                        foreach ($mandatory_attributes as $attr) {
                            $info[] = $attr['name'] . ' ' . $this->l('is mandatory for this Ceneo category');
                        }
                    }
                    $other_attributes = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_
                        . 'ceneo_xml_attribute where is_key_attribute = "False" and id_ceneo_category = ' .
                        (int) $categories[$id_default_category]);
                    if ($other_attributes) {
                        foreach ($other_attributes as $attr) {
                            $info[] = $attr['name'] . ' ' . $this->l('is suggested for this Ceneo category');
                        }
                    }
                }
            }
        }

        $product_settings = $this->legacyProductSettingsRepository->getByIdProduct($params['id_product']);
        $checkAvail = $product_settings['avail'] === Configuration::get('CENEO_XML_AVAIL') ? 0 : $product_settings['avail'];
        $this->context->smarty->assign(
            [
                'exclude_from_ceneo' => $product_settings['exclude'] ? $product_settings['exclude'] : 0,
                'ceneo_basket' => isset($product_settings['basket']) ?
                    $product_settings['basket'] :
                    Configuration::get('CENEO_XML_BASKET'),
                'ceneo_avail' => isset($product_settings['avail']) ?
                    $product_settings['avail'] :
                    Configuration::get('CENEO_XML_AVAIL'),
                'defaultValue' => $checkAvail,
                'info' => join('<br/>', $info),
                'availabilities' => Helper::getSingleAvailabilitiesLabels($this->stock_management, $this),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/productupdate.tpl');
    }

    public function hookDisplayAdminProductsCombinationBottom($params)
    {
        $this->context->smarty->assign(
            [
                'exclude_from_ceneo' => $this->legacyProductSettingsRepository->getByIdProduct(
                    $params['id_product'],
                    $params['id_product_attribute']
                ),
                'id_product_attribute' => $params['id_product_attribute'],
            ]
        );

        return $this->display(__FILE__, 'views/templates/hook/combinationupdate.tpl');
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return false;
    }

    public function getFriendlyInfo()
    {
        return $this->display($this->name, 'views/templates/admin/friendlyurl.tpl');
    }

    public function debug($context, $file)
    {
        $filename = ($file ?? 'test');
        $logDir = __DIR__ . '/log';

        $log = PHP_EOL . 'User: ' . $_SERVER['REMOTE_ADDR'] . ' - ' . date('F j, Y, g:i a') . PHP_EOL
            . print_r($context, true) . PHP_EOL . '-------------------------';
        file_put_contents($logDir . '/' . $filename . '_' . date('j.n.Y') . '.log', $log, FILE_APPEND);
    }
}
