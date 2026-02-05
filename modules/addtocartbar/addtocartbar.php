<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Addtocartbar extends Module
{
    protected $author_address;

    public function __construct()
    {
        $this->name = 'addtocartbar';
        $this->tab = 'front_office_features';
        $this->version = '1.3.0';
        $this->author = 'FMM Modules';
        $this->need_instance = 0;
        $this->module_key = 'd7099c244a524edb7e7c4d49194983e1';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Add to Cart bar');
        $this->description = $this->l('This module Shows an add to cart button all the time on a product page when user scroll down.Also, customize the appearance of the sticky add to cart bar & help to increase the users convenience.');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->upgradeModule('1.3.0'); // Ensure this calls the upgrade file
    }

    public function upgradeModule($version)
    {
        if (version_compare($this->version, $version, '<')) {
            include dirname(__FILE__) . '/upgrade/upgrade-' . str_replace('.', '_', $version) . '.php';
            call_user_func('upgrade_module_' . str_replace('.', '_', $version), $this);
        }
    }

    public function install()
    {
        if (parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayHeader')
            && $this->installConfiguration()) {
            if (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
                $this->registerHook('displayBackOfficeHeader');

                return true;
            } else {
                $this->registerHook('backOfficeHeader');

                return true;
            }
        } else {
            return false;
        }

        return true;
    }

    public function installConfiguration()
    {
        return (bool) Configuration::updateValue('STICKY_PRO_TYPES', 'simple,pack,virtual,combinations')
        && (bool) Configuration::updateValue('STICKY_POS', 'bottom')
        && (bool) Configuration::updateValue('STICKY_BODY_PAD', '130')
        && (bool) Configuration::updateValue('STICKY_BACK_COLOR', '#ddffe6')
        && (bool) Configuration::updateValue('STICKY_PRO_TEXT_COLOR', '#000000')
        && (bool) Configuration::updateValue('STICKY_CART_BTN_BG_COLOR', '#24b9d7')
        && (bool) Configuration::updateValue('STICKY_CART_BTN_TXT_COLOR', '#FFFFFF')
        && (bool) Configuration::updateValue('STICKY_PRICE_TXT_COLOR', '#000000')
        && (bool) Configuration::updateValue('STICKY_ATR_LABEL_COLOR', '#000000')
        && (bool) Configuration::updateValue('STICKY_BORDER_COLOR', '#00ffff')
        && (bool) Configuration::updateValue('STICKY_BORDER_RADIUS', '5')
        && (bool) Configuration::updateValue('STICKY_BAR_HEIGHT', '150')
        && (bool) Configuration::updateValue('STICKY_EXC_PRODUCTS', '')
        && (bool) Configuration::updateValue('DEVICE_CHECK_SWITCH', 0)
        && Configuration::updateValue('STICK_DEVICES_SHOW', '')
        && (bool) Configuration::updateValue('STICKY_ON_OFF', 1)
        && (bool) Configuration::updateValue('STICKY_DATE_FIELD', date('Y-m-d'))
        && (bool) Configuration::updateValue('STICKY_PRO_TYPE_TOGGLE', 1)
        && (bool) Configuration::updateValue('STICKY_CATEGORY', 1)
        && (bool) Configuration::updateValue('STICKY_EXC_PRODUCTS_ON_OFF', 1);
    }

    public function uninstall()
    {
        return parent::uninstall()
        && $this->uninstallConfiguration();
    }

    /**
     * @return bool
     */
    public function uninstallConfiguration()
    {
        return (bool) Configuration::deleteByName('STICKY_PRO_TYPES')
        && (bool) Configuration::deleteByName('STICKY_POS')
        && (bool) Configuration::deleteByName('STICKY_BODY_PAD')
        && (bool) Configuration::deleteByName('STICKY_BACK_COLOR')
        && (bool) Configuration::deleteByName('STICKY_PRO_TEXT_COLOR')
        && (bool) Configuration::deleteByName('STICKY_CART_BTN_BG_COLOR')
        && (bool) Configuration::deleteByName('STICKY_CART_BTN_TXT_COLOR')
        && (bool) Configuration::deleteByName('STICKY_PRICE_TXT_COLOR')
        && (bool) Configuration::deleteByName('STICKY_ATR_LABEL_COLOR')
        && (bool) Configuration::deleteByName('STICKY_BORDER_COLOR')
        && (bool) Configuration::deleteByName('STICKY_BORDER_RADIUS')
        && (bool) Configuration::deleteByName('STICKY_BAR_HEIGHT')
        && (bool) Configuration::deleteByName('STICKY_EXC_CATEGORIES')
        && (bool) Configuration::deleteByName('STICK_DEVICES_SHOW')
        && (bool) Configuration::deleteByName('STICKY_ON_OFF')
        && (bool) Configuration::deleteByName('DEVICE_CHECK_SWITCH')
        && (bool) Configuration::deleteByName('STARTING_RESOLUTION')
        && (bool) Configuration::deleteByName('RESOLUTION_ENDING')
        && (bool) Configuration::deleteByName('STICKY_EXC_PRODUCTS')
        && (bool) Configuration::deleteByName('STICKY_DATE_FIELD')
        && (bool) Configuration::deleteByName('STICKY_PRO_TYPE_TOGGLE')
        && (bool) Configuration::deleteByName('STICKY_CATEGORY')
        && (bool) Configuration::deleteByName('STICKY_EXC_PRODUCTS_ON_OFF');
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        $action = Tools::getValue('action');
        if ($action == 'getSearchProducts') {
            $this->getSearchProducts();
            exit;
        }
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitAddtocartbarModule')) == true) {
            $output .= $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $output . $this->renderForm();
    }

    /**
     * Names of devices function
     *
     * @return $options
     */
    protected function deviceOptions()
    {
        $options = [
            [
                'id' => 'desktop',
                'name' => $this->l('Desktop view'),
            ],

            [
                'id' => 'tablet',
                'name' => $this->l('Tablet view'),
            ],

            [
                'id' => 'phone',
                'name' => $this->l('Phone view'),
            ],
        ];

        return $options;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAddtocartbarModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $products = [];
        $id_lang = (int) $this->context->language->id;
        $criterea_value = Configuration::get('STICKY_EXC_PRODUCTS');
        if (!empty($criterea_value)) {
            $products = explode(',', $criterea_value);
            if ($products[0] != null && is_array($products)) {
                foreach ($products as &$product) {
                    $product = new Product((int) $product, true, (int) $id_lang);
                    $product->id_product_attribute = (int) Product::getDefaultAttribute(
                        $product->id
                    ) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                    $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById(
                        (int) $product->id_product_attribute,
                        $id_lang
                    ) : Product::getCover($product->id);
                    if (!is_array($_cover)) {
                        $_cover = Product::getCover($product->id);
                    }
                    $product->id_image = $_cover['id_image'];
                }
            }
        }

        $getConfigFormValues = $this->getConfigFormValues(); // getting config form values

        $devices_types_selected = Configuration::get('STICK_DEVICES_SHOW');
        $devices_types_selected = explode(',', $devices_types_selected);
        foreach ($devices_types_selected as $select) {
            $getConfigFormValues = array_merge($getConfigFormValues, ['STICK_DEVICES_SHOW_' . $select => 1]);
        }
        $id_lang = (int) $this->context->language->id;
        $categories = Category::getSimpleCategories($id_lang);
        $selected_cat = (Configuration::get('STICKY_EXC_CATEGORIES')) ? explode(',', Configuration::get('STICKY_EXC_CATEGORIES')) : [];

        $helper->tpl_vars = [
            'fields_value' => $getConfigFormValues,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'products' => $products,
            'categories' => $categories,
            'selected_cat' => $selected_cat,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Sticky bar On/Off '),
                        'name' => 'STICKY_ON_OFF',
                        'desc' => $this->l('Enable or disable Sticky Bar'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('On'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Off'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'date',
                        'label' => $this->l('Select Date'),
                        'name' => 'STICKY_DATE_FIELD',
                        'desc' => $this->l('Choose the date when the sticky bar will be visible.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Device recognization'),
                        'name' => 'DEVICE_CHECK_SWITCH',
                        'desc' => $this->l('Chose way to recognize user device as per your requirement left for (user agent) and right For (screen Resolution size)'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Resolution'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('User Agent'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'number',
                        'name' => 'STARTING_RESOLUTION',
                        'label' => $this->l('Set Resolution:'),
                        'desc' => $this->l('The sticky cart bar will not be displayed within the specified resolution range.'),
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => $this->l('Select Devices to disable view'),
                        'desc' => $this->l('Select devices to hide the Sticky Bar on them'),
                        'name' => 'STICK_DEVICES_SHOW',
                        'class' => 'deviceshowclass',
                        'values' => [
                            'query' => $this->deviceOptions(),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Select Position'),
                        'name' => 'STICKY_POS',
                        'desc' => $this->l('Set the position of sticky add to cart bar either to the top or bottom.'),
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => 'top',
                                    'name' => 'top',
                                ],
                                [
                                    'id_option' => 'bottom',
                                    'name' => 'bottom',
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'range',
                        'label' => $this->l('Body bottom padding:'),
                        'name' => 'STICKY_BODY_PAD',
                        'desc' => $this->l('choose the body bottom padding'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Sticky bar background Color:'),
                        'name' => 'STICKY_BACK_COLOR',
                        'desc' => $this->l('Choose the background colour for the sticky add to cart bar'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Sticky bar border shadow '),
                        'name' => 'STICKY_SHADOW',
                        'desc' => $this->l('Enable or disable border shadow'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Product name text color:'),
                        'name' => 'STICKY_PRO_TEXT_COLOR',
                        'desc' => $this->l('Select the colour for the product name text.'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Add to cart button background Color:'),
                        'name' => 'STICKY_CART_BTN_BG_COLOR',
                        'desc' => $this->l('Choose the background colour of the add to cart button.'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Add to cart button Text Color:'),
                        'name' => 'STICKY_CART_BTN_TXT_COLOR',
                        'desc' => $this->l('Select the colour of the “Add to Cart” text shown on the add to cart button.'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Price text color:'),
                        'name' => 'STICKY_PRICE_TXT_COLOR',
                        'desc' => $this->l('Choose the colour of the product price.'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Attributes label text Color:'),
                        'name' => 'STICKY_ATR_LABEL_COLOR',
                        'desc' => $this->l('Select the attribute label color.'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Border Color:'),
                        'name' => 'STICKY_BORDER_COLOR',
                        'desc' => $this->l('Select the border color'),
                        'size' => 20,
                    ],
                    [
                        'type' => 'range',
                        'label' => $this->l('Border Width:'),
                        'name' => 'STICKY_BORDER_RADIUS',
                        'desc' => $this->l('choose the border width to show around the sticky add to cart bar'),
                    ],
                    [
                        'type' => 'range',
                        'label' => $this->l('Sticky bar height:'),
                        'name' => 'STICKY_BAR_HEIGHT',
                        'desc' => $this->l('choose the height of sticky bar in px'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Find Products'),
                        'name' => 'STICKY_EXC_PRODUCTS_ON_OFF',
                        'desc' => $this->l('Enable or disable Search Product Field'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enable'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disable'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'name' => 'STICKY_EXC_PRODUCTS',
                        'values' => [],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Categories'),
                        'name' => 'STICKY_CATEGORY',
                        'desc' => $this->l('Enable or disable SCategories'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enable'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disable'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'categories_tree',
                        'label' => $this->l('Select categories'),
                        'name' => 'categories_tree',
                        'desc' => $this->l('You can select one or more categories.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Product Types'),
                        'name' => 'STICKY_PRO_TYPE_TOGGLE',
                        'desc' => $this->l('Enable or disable Product Types'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enable'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disable'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Select specific product type to display sticky Add to cart'),
                        'name' => 'STICKY_PRO_TYPES[]',
                        'desc' => 'Choose the product types (simple, pack, virtual, combinations)on whose product pages you want to show only work on categories selected',
                        'multiple' => true,
                        'class' => 'chosen',
                        'options' => [
                            'query' => [
                                [
                                    'id_option' => 'simple',
                                    'name' => 'simple',
                                ],
                                [
                                    'id_option' => 'pack',
                                    'name' => 'pack',
                                ],
                                [
                                    'id_option' => 'virtual',
                                    'name' => 'virtual',
                                ],
                                [
                                    'id_option' => 'combinations',
                                    'name' => 'combinations',
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Getting Selected Categories for Categories Tree
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        $selectedCategories = [];

        // Retrieve the saved selected category IDs from your module's configuration or database
        $selectedCategoryIds = Configuration::get('STICKY_EXC_CATEGORIES');

        // Convert the comma-separated string of category IDs to an array
        $selectedCategories = explode(',', Configuration::get('STICKY_EXC_CATEGORIES'));

        return $selectedCategories;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $product_type = [];
        $product_types_selected = Configuration::get('STICKY_PRO_TYPES');
        if (!empty($product_types_selected)) {
            $product_type = explode(',', $product_types_selected);
        }

        return [
            'STICKY_PRO_TYPES[]' => $product_type,
            'STICKY_POS' => Configuration::get('STICKY_POS'),
            'STICKY_BODY_PAD' => Configuration::get('STICKY_BODY_PAD'),
            'STICKY_BACK_COLOR' => Configuration::get('STICKY_BACK_COLOR'),
            'STICKY_SHADOW' => Configuration::get('STICKY_SHADOW'),
            'STICKY_PRO_TEXT_COLOR' => Configuration::get('STICKY_PRO_TEXT_COLOR'),
            'STICKY_CART_BTN_BG_COLOR' => Configuration::get('STICKY_CART_BTN_BG_COLOR'),
            'STICKY_CART_BTN_TXT_COLOR' => Configuration::get('STICKY_CART_BTN_TXT_COLOR'),
            'STICKY_PRICE_TXT_COLOR' => Configuration::get('STICKY_PRICE_TXT_COLOR'),
            'STICKY_ATR_LABEL_COLOR' => Configuration::get('STICKY_ATR_LABEL_COLOR'),
            'STICKY_BORDER_COLOR' => Configuration::get('STICKY_BORDER_COLOR'),
            'STICKY_BORDER_RADIUS' => Configuration::get('STICKY_BORDER_RADIUS'),
            'STICKY_BAR_HEIGHT' => Configuration::get('STICKY_BAR_HEIGHT'),
            'STICKY_ON_OFF' => Configuration::get('STICKY_ON_OFF'),
            'STARTING_RESOLUTION' => Configuration::get('STARTING_RESOLUTION'),
            'RESOLUTION_ENDING' => Configuration::get('RESOLUTION_ENDING'),
            'DEVICE_CHECK_SWITCH' => Configuration::get('DEVICE_CHECK_SWITCH'),
            // new Fields
            'STICKY_DATE_FIELD' => Configuration::get('STICKY_DATE_FIELD'),
            'STICKY_PRO_TYPE_TOGGLE' => Configuration::get('STICKY_PRO_TYPE_TOGGLE'),
            'STICKY_CATEGORY' => Configuration::get('STICKY_CATEGORY'),
            'STICKY_EXC_PRODUCTS_ON_OFF' => Configuration::get('STICKY_EXC_PRODUCTS_ON_OFF'),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        // updated
        $excluded_pro_value = '';
        if (Tools::getValue('STICKY_EXC_PRODUCTS_ON_OFF') == 1) {
            $excluded_pro_value = Tools::getValue('STICKY_EXC_PRODUCTS');
            if (!empty($excluded_pro_value)) {
                $excluded_pro_value = implode(',', $excluded_pro_value);
            }
        } else {
            $excluded_pro_value = Configuration::get('STICKY_EXC_PRODUCTS');
        }

        $included_pro_type = '';
        if (Tools::getValue('STICKY_PRO_TYPE_TOGGLE') == 1) {
            $included_pro_type = Tools::getValue('STICKY_PRO_TYPES');
            if (!empty($included_pro_type)) {
                $included_pro_type = implode(',', $included_pro_type);
            }
        } else {
            $included_pro_type = Configuration::get('STICKY_PRO_TYPES');
        }
        $categories = '';
        if (Tools::getValue('STICKY_CATEGORY') == 1) {
            $categories = (Tools::getValue('categoryBox')) ? implode(',', Tools::getValue('categoryBox')) : '';
        } else {
            $categories = Configuration::get('STICKY_EXC_CATEGORIES');
        }

        $devices = $this->deviceOptions();
        $selected_devices = [];
        foreach ($devices as $device) {
            if (Tools::getValue('STICK_DEVICES_SHOW_' . $device['id']) == 'on') {
                $selected_devices[] = $device['id'];
            }
        }
        $selected_devices = implode(',', $selected_devices);

        // setting resolution min and max limit
        $start_resolution = min(max(Tools::getValue('STARTING_RESOLUTION'), 0), 5000);
        $resolution_end = min(max(Tools::getValue('RESOLUTION_ENDING'), 0), 5000);

        Configuration::updateValue('DEVICE_CHECK_SWITCH', Tools::getValue('DEVICE_CHECK_SWITCH'));
        Configuration::updateValue('STICKY_PRO_TYPES', $included_pro_type);
        Configuration::updateValue('STARTING_RESOLUTION', $start_resolution);
        Configuration::updateValue('RESOLUTION_ENDING', $resolution_end);
        Configuration::updateValue('STICK_DEVICES_SHOW', $selected_devices);
        Configuration::updateValue('STICKY_POS', Tools::getValue('STICKY_POS'));
        Configuration::updateValue('STICKY_BODY_PAD', Tools::getValue('STICKY_BODY_PAD'));
        Configuration::updateValue('STICKY_BACK_COLOR', Tools::getValue('STICKY_BACK_COLOR'));
        Configuration::updateValue('STICKY_SHADOW', Tools::getValue('STICKY_SHADOW'));
        Configuration::updateValue('STICKY_ON_OFF', Tools::getValue('STICKY_ON_OFF'));
        Configuration::updateValue('STICKY_PRO_TEXT_COLOR', Tools::getValue('STICKY_PRO_TEXT_COLOR'));
        Configuration::updateValue('STICKY_CART_BTN_BG_COLOR', Tools::getValue('STICKY_CART_BTN_BG_COLOR'));
        Configuration::updateValue('STICKY_CART_BTN_TXT_COLOR', Tools::getValue('STICKY_CART_BTN_TXT_COLOR'));
        Configuration::updateValue('STICKY_PRICE_TXT_COLOR', Tools::getValue('STICKY_PRICE_TXT_COLOR'));
        Configuration::updateValue('STICKY_ATR_LABEL_COLOR', Tools::getValue('STICKY_ATR_LABEL_COLOR'));
        Configuration::updateValue('STICKY_BORDER_COLOR', Tools::getValue('STICKY_BORDER_COLOR'));
        Configuration::updateValue('STICKY_BORDER_RADIUS', Tools::getValue('STICKY_BORDER_RADIUS'));
        Configuration::updateValue('STICKY_BAR_HEIGHT', Tools::getValue('STICKY_BAR_HEIGHT'));
        Configuration::updateValue('STICKY_EXC_CATEGORIES', $categories);
        Configuration::updateValue('STICKY_EXC_PRODUCTS', $excluded_pro_value);
        // New Fields
        $date_value = '';
        if (Tools::getValue('STICKY_ON_OFF') == 1) {
            $date_value = Tools::getValue('STICKY_DATE_FIELD');
            if (empty($date_value)) {
                return $this->displayError($this->l('Please Select a Valid Date'));
            }
        } else {
            $date_value = Configuration::get('STICKY_DATE_FIELD');
        }
        Configuration::updateValue('STICKY_DATE_FIELD', $date_value);
        Configuration::updateValue('STICKY_PRO_TYPE_TOGGLE', Tools::getValue('STICKY_PRO_TYPE_TOGGLE'));
        Configuration::updateValue('STICKY_CATEGORY', Tools::getValue('STICKY_CATEGORY'));
        Configuration::updateValue('STICKY_EXC_PRODUCTS_ON_OFF', Tools::getValue('STICKY_EXC_PRODUCTS_ON_OFF'));

        return $this->displayConfirmation($this->l('Settings Updated'));
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if ($this->context->controller instanceof AdminModulesController) {
            $this->context->controller->addJS($this->_path . 'views/js/stickycartback.js');
            $this->context->controller->addCSS($this->_path . 'views/css/stickycartback.css');
        }
        if (Tools::getValue('id_product')) {
            $this->context->controller->addJS($this->_path . '/views/js/stickycart.js');
            $this->context->controller->addCSS($this->_path . '/views/css/stickycart.css');
        }
        $url = $this->context->link->getAdminLink('AdminModules', true);
        Media::addJsDef([
            'sticky_action_url' => $url . '&configure=addtocartbar&action=getSearchProducts&forceJson=1' .
            '&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20',
            'DEVICE_CHECK_SWITCH' => Configuration::get('DEVICE_CHECK_SWITCH'),
            'STICKY_ON_OFF' => Configuration::get('STICKY_ON_OFF'),
            'STICKY_PRO_TYPE_TOGGLE' => Configuration::get('STICKY_PRO_TYPE_TOGGLE'),
            'STICKY_CATEGORY' => Configuration::get('STICKY_CATEGORY'),
            'STICKY_EXC_PRODUCTS_ON_OFF' => Configuration::get('STICKY_EXC_PRODUCTS_ON_OFF'),

            '_ps_version' => _PS_VERSION_,
        ]);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/stickycart.js');
        }
        Media::addJsDef([
            'product_id' => (int) Tools::getValue('id_product'),
            'STICKY_POS' => Configuration::get('STICKY_POS'),
            'STICKY_BODY_PAD' => Configuration::get('STICKY_BODY_PAD'),
            'STICKY_BACK_COLOR' => Configuration::get('STICKY_BACK_COLOR'),
            'STICKY_PRO_TEXT_COLOR' => Configuration::get('STICKY_PRO_TEXT_COLOR'),
            'STICKY_CART_BTN_BG_COLOR' => Configuration::get('STICKY_CART_BTN_BG_COLOR'),
            'STICKY_CART_BTN_TXT_COLOR' => Configuration::get('STICKY_CART_BTN_TXT_COLOR'),
            'STICKY_PRICE_TXT_COLOR' => Configuration::get('STICKY_PRICE_TXT_COLOR'),
            'STICKY_ATR_LABEL_COLOR' => Configuration::get('STICKY_ATR_LABEL_COLOR'),
            'STICKY_BORDER_COLOR' => Configuration::get('STICKY_BORDER_COLOR'),
            'STICKY_BORDER_RADIUS' => Configuration::get('STICKY_BORDER_RADIUS'),
            'STICKY_BAR_HEIGHT' => Configuration::get('STICKY_BAR_HEIGHT'),
            'STICKY_ON_OFF' => Configuration::get('STICKY_ON_OFF'),
            'DEVICE_CHECK_SWITCH' => Configuration::get('DEVICE_CHECK_SWITCH'),
            'STICKY_DATE_FIELD' => Configuration::get('STICKY_DATE_FIELD'),
            'STARTING_RESOLUTION' => Configuration::get('STARTING_RESOLUTION'),
            'RESOLUTION_ENDING' => Configuration::get('RESOLUTION_ENDING'),
            'sticky_add_to_cart' => $this->l('Add to Cart'),
        ]);

        return $this->displayStickyCart();
    }

    public function displayStickyCart()
    {
        $flag = false;
        $pageName = Dispatcher::getInstance()->getController();
        $productName = '';
        $id_product = 0;
        $productCat = [];
        $id_customization = 0;
        $productsType = '';
        $is_stickycarton = Configuration::get('STICKY_ON_OFF');
        $date_to_show_cart = Configuration::get('STICKY_DATE_FIELD');
        $product_exe_toggle = Configuration::get('STICKY_EXC_PRODUCTS_ON_OFF');
        $category_toggle = Configuration::get('STICKY_CATEGORY');
        $specific_product_toggle = Configuration::get('STICKY_PRO_TYPE_TOGGLE');

        $selected_devices = explode(',', Configuration::get('STICK_DEVICES_SHOW'));

        if (Tools::getValue('id_product')) {
            $id_product = (int) Tools::getValue('id_product');
            $productCat = Product::getProductCategories($id_product);
            $products = new Product($id_product);
            if ($products->customizable) {
                $already_customized = $this->context->cart->getProductCustomization(
                    $id_product,
                    null,
                    true
                );
                foreach ($already_customized as $customization) {
                    $id_customization = $customization['id_customization'];
                }
            }
            $productName = $products->getProductName($id_product, null, $this->context->language->id);
            $productsType = $products->getWsType();
            if ($productsType == 'simple') {
                if ($products->hasAttributes()) {
                    $productsType = 'combinations';
                }
            }
        }

        $exclusiveCategories = [];
        if ($category_toggle == 1) {
            if (Configuration::get('STICKY_EXC_CATEGORIES')) {
                $exclusiveCategories = explode(',', Configuration::get('STICKY_EXC_CATEGORIES'));
            }
        }

        $exclusiveProducts = [];
        if ($product_exe_toggle == 1) {
            if (Configuration::get('STICKY_EXC_PRODUCTS')) {
                $exclusiveProducts = explode(',', Configuration::get('STICKY_EXC_PRODUCTS'));
            }
        }

        $includedProductTypes = [];
        if ($specific_product_toggle == 1) {
            if (Configuration::get('STICKY_PRO_TYPES')) {
                $includedProductTypes = explode(',', Configuration::get('STICKY_PRO_TYPES'));
            }
        }
        // checking is it resolution to disable or useragent
        $devicechecktype = Configuration::get('DEVICE_CHECK_SWITCH');

        // 0 mean it is user agent base checking so
        if ($devicechecktype == 0) {
            // getting current useragent
            $currentuseragent = $this->userAgentDevice();

            // Checking is it allowed
            $is_alloweduseragent = array_intersect($currentuseragent, $selected_devices);
        } else {
            $is_alloweduseragent = null;
        }

        $catResult = array_intersect($productCat, $exclusiveCategories);
        $productResult = in_array($id_product, $exclusiveProducts);
        $typesProduct = in_array($productsType, $includedProductTypes);

        if ($pageName == 'product' && $is_stickycarton == 1 && $date_to_show_cart <= date('Y-m-d') && $is_alloweduseragent == null) {
            $flag = !(empty($productResult));

            if (!$flag && !(empty($catResult))) {
                if (empty($includedProductTypes) || !(empty($typesProduct))) {
                    $flag = true;
                }
            } elseif (!empty($typesProduct) && empty($exclusiveCategories)) {
                $flag = true;
            }
        }

        if ($flag == true) {
            if (Tools::getValue('id_product')) {
                $this->context->controller->addJS($this->_path . '/views/js/stickycart.js');
                $this->context->controller->addCSS($this->_path . '/views/css/stickycart.css');
            }
            $this->context->smarty->assign([
                'productName' => $productName,
                'id_product' => $id_product,
                'id_customization' => $id_customization,
                'shadow' => Configuration::get('STICKY_SHADOW'),
                'stickybar_pos' => Configuration::get('STICKY_POS'),
                'STICKY_BODY_PAD' => Configuration::get('STICKY_BODY_PAD'),
            ]);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/stickycart.tpl');
        }
    }

    /**
     * This function is getting the user agent
     *
     * @return array
     */
    protected function userAgentDevice()
    {
        // Getting user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        // Initialize variables to track device type
        $isPhone = false;
        $isDesktop = false;
        $isTablet = false;

        // Check for phone
        if (preg_match('/(android|iphone|ipod|opera mini|blackberry|windows (phone|ce)|mobile)/i', $userAgent)) {
            $isPhone = true;
        }

        // Check for tablet
        if (preg_match('/(ipad|android(?!.*mobile))/i', $userAgent)) {
            $isTablet = true;
        }

        // If not a phone or tablet, consider it a desktop
        if (!$isPhone && !$isTablet) {
            $isDesktop = true;
        }

        // Create an array to store the device types
        $deviceTypes = [];

        // Add device types to the array based on the results
        if ($isPhone) {
            $deviceTypes[] = 'phone';
        }

        if ($isTablet) {
            $deviceTypes[] = 'tablet';
        }

        if ($isDesktop) {
            $deviceTypes[] = 'desktop';
        }

        // Return the array of device types
        return $deviceTypes;
    }

    protected function getSearchProducts()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            exit(json_encode($this->l('Found Nothing.')));
        }

        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = '
        SELECT p.`id_product`,
        pl.`link_rewrite`,
        p.`reference`,
        pl.`name`,
        image_shop.`id_image` id_image,
        il.`legend`,
        p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' .
        (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' .
        (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' .
        (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ .
            'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination || $excludeIds)) {
            $results = [];
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']) . (
                        !empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''
                    ) . '|' . (int) $item['id_product'];
                } else {
                    $cover = Product::getCover($item['id_product']);
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'] . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''),
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],

                                ($item['id_image']) ? $item['id_image'] : $cover['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    ];
                }
            }

            if (!$forceJson) {
                echo implode("\n", $results);
            } else {
                echo json_encode($results);
            }
        } elseif ($items) {
            // packs
            $results = [];
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = '
                    SELECT pa.`id_product_attribute`,
                    pa.`reference`,
                    ag.`id_attribute_group`,
                    pai.`id_image`,
                    agl.`name` AS group_name,
                    al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_combination` pac ON pac.`id_product_attribute` =
                            pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group_lang` agl ON (ag.`id_attribute_group` =
                                agl.`id_attribute_group` AND agl.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
                            $cover = Product::getCover($item['id_product']);
                            $results[$k['id_product_attribute']]['id'] = $item['id_product'];

                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] =
                                $combination['id_product_attribute'];
                            !empty(
                                $results[$combination['id_product_attribute']]['name']
                            ) ? $results[$combination['id_product_attribute']]['name'] .=
                            ' ' . $combination['group_name'] . '-' .
                            $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] =
                                $item['name'] . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] =
                                !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $context->link->getImageLink(
                                        $item['link_rewrite'],
                                        ($combination['id_image']) ? $combination['id_image'] : $cover['id_image'],
                                        $this->getFormatedName('home')
                                    )
                                );
                            }
                        }
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    $this->getFormatedName('home')
                                )
                            ),
                        ];
                    }
                } else {
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    ];
                }
            }
            echo json_encode(array_values($results));
        } else {
            echo json_encode([]);
        }
    }

    public function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(['_' . $theme_name, $theme_name . '_'], '', $name);
        // check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name, 'products')) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' . $theme_name, 'products')) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' . $name_without_theme_name, 'products')) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }
}
