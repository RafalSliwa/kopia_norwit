<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminStickersBannersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'ProductLabel';
        $this->table = 'fmm_stickersbanners';
        $this->identifier = 'stickersbanners_id';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();

        $this->fields_list = [
            'stickersbanners_id' => [
                'title' => '#',
                'width' => 25,
            ],
            'title' => [
                'title' => $this->module->l('Title'),
                'width' => 'auto',
            ],
            'start_date' => [
                'title' => $this->module->l('Start'),
                'width' => 'auto',
                'type' => 'date',
            ],
            'expiry_date' => [
                'title' => $this->module->l('End'),
                'width' => 'auto',
                'type' => 'date',
            ],
            'banner_status' => [
                'title' => $this->module->l('Status'),
                'type' => 'bool',
                'active' => 'banner_status',
                'width' => 'auto',
                'align' => 'center',
                'orderby' => false,
            ],
        ];

        $this->bulk_actions = [
            'enableSelection' => [
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off',
            ],
            'disableSelection' => [
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off',
            ],
            'deleteSelection' => [
                'text' => $this->l('Delete selection'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Are you sure you want to delete the selected items?'),
            ],
        ];
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initContent()
    {
        if (Tools::getValue('action') == 'getSearchProducts') {
            $this->getSearchProducts();
        }

        parent::initContent();
    }

    public function renderForm()
    {
        $languages = Language::getLanguages();

        $module = new ProductLabelsandStickers();
        $current_object = $this->loadObject(true);
        $id = (int) Tools::getValue('stickersbanners_id');

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }

        $shops = '';
        $selected_shops = '';
        if (Shop::isFeatureActive() && $id) {
            $shops = $this->renderShops();

            $assoc_shops = ProductLabel::getShopLabels($id);
            $selected_shops = ($current_object && $assoc_shops = ProductLabel::getShopLabels($id)) ? implode(',', $assoc_shops) : '';
        }

        $this->context->smarty->assign(['shops' => $shops, 'selected_shops' => $selected_shops]);
        $this->fields_form['submit'] = [
            'title' => $this->l('Save'),
            'class' => 'button',
        ];

        $this->context->smarty->assign('mode', $this->display);
        $this->context->smarty->assign('current_lang', $this->context->language->id);
        $this->context->smarty->assign('languages', $languages);
        $this->context->smarty->assign('module', $module);
        $this->context->smarty->assign('current_object', $current_object);
        $this->context->smarty->assign('stickersbanners_id', (int) $id);

        $color = '';
        $bg_color = '';
        $font = '';
        $font_size = 0;
        $border_color = '';
        $font_weight = '';
        $start_date = '';
        $expiry_date = '';
        if ($id != 0) {
            $this->context->smarty->assign('stickersbanners_id', $id);

            /* ==== */
            $fbannerstickerrule = Rules::getRuleByBannerStickerId($current_object->stickersbanners_id, $this->context->shop->id);
            $fbannerstickershop = ProductLabel::getShopBannerStickers((int) $current_object->stickersbanners_id);
            /* ==== */

            /* ==== */
            $products = [];
            if ($id > 0) {
                $obj = new Rules();
                $edit_data = $obj->getAllEditDataBanner($fbannerstickerrule['fmm_stickers_rules_id']);

                $edit_data = array_shift($edit_data);

                $edit_data['value_array'] = [];

                if (isset($edit_data['value'])) {
                    $edit_data['value_array'] = explode(',', $edit_data['value']);
                }

                $shop_data = $obj->getAllEditDataShop($id);
                $products = $edit_data['value_array'];

                if (!empty($products) && ($products[0] != null) && is_array($products)) {
                    foreach ($products as &$product) {
                        $product = new Product((int) $product, true, (int) $this->context->shop->id);
                        $product->id_product_attribute = (int) Product::getDefaultAttribute($product->id) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                        $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById((int) $product->id_product_attribute, $this->context->shop->id) : Product::getCover($product->id);
                        if (!is_array($_cover)) {
                            $_cover = Product::getCover($product->id);
                        }
                        if (is_array($_cover)) { // Check if $_cover is an array before accessing its offset
                            $product->id_image = $_cover['id_image'];
                        } else {
                            $product->id_image = null;
                        }
                    }
                }
            }

            /* ==== */

            /* ==== */
            $ex_products = [];
            if ($id > 0) {
                $obj = new Rules();
                $abc = [];

                if (isset($abc)) {
                    $abc = explode(',', $edit_data['excluded_p']);
                }

                $shop_data = $obj->getAllEditDataShop($fbannerstickerrule['fmm_stickers_rules_id']);

                $ex_products = $abc;

                if (!empty($products) && ($abc[0] != null) && is_array($ex_products)) {
                    foreach ($ex_products as &$product) {
                        $product = new Product((int) $product, true, (int) $this->context->shop->id);
                        $product->id_product_attribute = (int) Product::getDefaultAttribute($product->id) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                        $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById((int) $product->id_product_attribute, $this->context->shop->id) : Product::getCover($product->id);
                        if (!is_array($_cover)) {
                            $_cover = Product::getCover($product->id);
                        }
                        if (is_array($_cover)) { // Check if $_cover is an array before accessing its offset
                            $product->id_image = $_cover['id_image'];
                        } else {
                            $product->id_image = null;
                        }
                    }
                }
            }
            /* ==== */

            $colors = $current_object->getColors($id);
            $color = $colors['color'];
            $bg_color = $colors['bg_color'];
            $border_color = $colors['border_color'];
            $font = $current_object->font;
            $font_size = $current_object->font_size;
            $font_weight = $current_object->font_weight;
            $start_date = $current_object->start_date;
            $expiry_date = $current_object->expiry_date;
        }

        /* =========================================================== */
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $groups = Group::getGroups($this->context->language->id);
        $categories = Category::getSimpleCategories($this->context->language->id);
        $features = Feature::getFeatures($this->context->language->id);
        $allfeatures = [];

        foreach ($features as $key => $value) {
            $fdetail = FeatureValue::getFeatureValuesWithLang($this->context->language->id, $value['id_feature']);

            foreach ($fdetail as $kk => $vval) {
                $fdetail[$kk]['name'] = $value['name'];
            }
            foreach ($fdetail as $newk => $newval) {
                $allfeatures[] = $newval;
            }
        }

        $brands = ($ps_17 >= 1) ? Manufacturer::getLiteManufacturersList() : Manufacturer::getManufacturers();
        $suppliers = ($ps_17 >= 1) ? Supplier::getLiteSuppliersList() : Supplier::getSuppliers();

        if ($ps_17 <= 0 && !empty($brands)) {
            foreach ($brands as &$brand) {
                $brand['id'] = $brand['id_manufacturer'];
            }
        }

        if ($ps_17 <= 0 && !empty($suppliers)) {
            foreach ($suppliers as &$supplier) {
                $supplier['id'] = $supplier['id_supplier'];
            }
        }

        $myshops = Shop::getShops(true, null, false);

        /* =========================================================== */
        $informations = _PS_MODULE_DIR_ . 'productlabelsandstickers/views/templates/admin/stickers_banners/info.tpl';

        $url = $this->context->link->getAdminLink('AdminStickersBanners', true);

        $this->context->smarty->assign(
            [
                'customers' => $groups,
                'suppliers' => $suppliers,
                'categories' => $categories,
                'allfeatures' => $allfeatures,
                'brands' => $brands,
                'myshops' => $myshops,

                'fstickerrule' => isset($fbannerstickerrule) ? $fbannerstickerrule : [],
                'fstickershop' => isset($fbannerstickershop) ? $fbannerstickershop : [],
                'action_url' => $url,
                'show_toolbar' => true,
                'toolbar_btn' => $this->toolbar_btn,
                'toolbar_scroll' => $this->toolbar_scroll,
                'title' => $this->l('Product labels and Stickers'),
                'currentToken' => $this->token,
                'currentIndex' => self::$currentIndex,
                'informations' => $informations,
                'currentTab' => $this,
                '_PS_MODULE_DIR_' => _PS_MODULE_DIR_,
                'base_uri' => __PS_BASE_URI__,
                'color' => $color,
                'bg_color' => $bg_color,
                'font' => $font,
                'font_size' => (int) $font_size,
                'border_color' => $border_color,
                'font_weight' => $font_weight,
                'start_date' => $start_date,
                'expiry_date' => $expiry_date,
                'products' => isset($products) ? $products : [],
                'ex_products' => isset($ex_products) ? $ex_products : [],
            ]
        );

        return parent::renderForm();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(['colorpicker_assets_path' => $this->module->getPathUri() . 'views/img/']);
        $this->addJs([
            __PS_BASE_URI__ . 'js/jquery/plugins/jquery.colorpicker.js',
            $this->module->getPathUri() . 'views/js/color-picker-assests.js',
        ]);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddfmm_stickersbanners')) {
            parent::postProcess();

            $current_object = $this->loadObject(true);
            $id = (int) Tools::getValue('stickersbanners_id');
            $id_lang = Context::getContext()->language->id;
            if (!$id_lang) {
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            }

            /* =========================================================== */
            $fmm_stickers_rules_id = (int) Tools::getValue('fmm_stickers_rules_id');
            $banner_status = Tools::getValue('banner_status');
            $status = $banner_status;
            // $shops = Tools::getValue('shops');
            $rule = Tools::getValue('rule');
            $brands = Tools::getValue('brands');
            $categories = Tools::getValue('category');
            $features = Tools::getValue('feature');
            $conditions = Tools::getValue('conditions');
            $p_types = Tools::getValue('p_types');
            $suppliers = Tools::getValue('suppliers');
            $rule_value = Tools::getValue('rule_value');
            $start_date = Tools::getValue('start_date');
            $expiry_date = Tools::getValue('expiry_date');
            $products = Tools::getValue('related_products');
            $excluded_products = Tools::getValue('excluded_products');

            $groups = Tools::getValue('customers');
            $shops = Tools::getValue('shops');

            if (!$shops) {
                $shops = explode(',', $this->context->shop->id);
            }

            /* =========================================================== */

            $sticker_text = Tools::getValue('sticker_text' . $id_lang);
            if (!empty($sticker_text)) {
                $languages = Language::getLanguages(true);
                $exists = (int) $current_object->getStickerIdStatic($id);

                if ($exists > 0) {
                    foreach ($languages as $language) {
                        $current_object->updateLabelText($id, (int) $language['id_lang'], Tools::getValue('sticker_text' . (int) $language['id_lang']));
                    }
                } else {
                    foreach ($languages as $language) {
                        $current_object->insertLabelText($id, (int) $language['id_lang'], Tools::getValue('sticker_text' . (int) $language['id_lang']));
                    }
                }

                /* ============================================================= */
                $class = new Rules();
                if ($fmm_stickers_rules_id > 0) {
                    if ($rule == 'brand' && !empty($brands)) {
                        $rule_value = implode(',', $brands);
                    } elseif ($rule == 'supplier' && !empty($suppliers)) {
                        $rule_value = implode(',', $suppliers);
                    } elseif ($rule == 'condition' && !empty($conditions)) {
                        $rule_value = implode(',', $conditions);
                    } elseif ($rule == 'p_type' && !empty($p_types)) {
                        $rule_value = implode(',', $p_types);
                    } elseif ($rule == 'category' && !empty($categories)) {
                        $rule_value = implode(',', $categories);
                    } elseif ($rule == 'p_feature' && !empty($features)) {
                        $rule_value = implode(',', $features);
                    } elseif ($rule == 'product' && !empty($products)) {
                        $rule_value = implode(',', $products);
                    } elseif ($rule == 'customer' && !empty($groups)) {
                        $rule_value = implode(',', $groups);
                    }

                    $shops = Tools::getValue('shops');
                    // $id = $id_sticker

                    if (!$shops) {
                        $shops = explode(',', $this->context->shop->id);
                    }

                    $class->resetShops($fmm_stickers_rules_id);

                    if ($excluded_products) {
                        $excluded_products = implode(',', $excluded_products);
                    }

                    $class->changeAll($fmm_stickers_rules_id, 0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);

                    $class->saveShops($fmm_stickers_rules_id, $shops);
                } else {
                    if (empty($rule)) {
                        $this->errors[] = $this->l('Please select a Rule.');
                    } else {
                        if ($rule == 'customer') {
                            if (empty($groups)) {
                                $this->errors[] = $this->l('Please select at least one customer group.');
                            } else {
                                $rule_value = implode(',', $groups);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'brand') {
                            if (empty($brands)) {
                                $this->errors[] = $this->l('Please select a brand.');
                            } else {
                                $rule_value = implode(',', $brands);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'supplier') {
                            if (empty($suppliers)) {
                                $this->errors[] = $this->l('Please select a supplier.');
                            } else {
                                $rule_value = implode(',', $suppliers);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'category') {
                            if (empty($categories)) {
                                $this->errors[] = $this->l('Please select a category.');
                            } else {
                                $rule_value = implode(',', $categories);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'product') {
                            if (empty($products)) {
                                $this->errors[] = $this->l('Please select at least one product.');
                            } else {
                                $rule_value = implode(',', $products);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } else {
                            if ($excluded_products) {
                                $excluded_products = implode(',', $excluded_products);
                            }

                            $_id = $class->saveAll(0, $id, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                            $class->saveShops($_id, $shops);
                        }
                    }
                }
                /* ============================================================= */
            }

            // $shops = Tools::getValue('checkBoxShopAsso_' . $this->table);
            // adding shop data
            if (Shop::isFeatureActive() && $current_object->id) {
                ProductLabel::removeShopLabels($current_object->id);
                if (isset($shops) && $shops) {
                    foreach ($shops as $id_shop) {
                        if ($id_shop != 0) {
                            ProductLabel::insertShopLabels($current_object->id, $id_shop);
                        }
                    }
                }
            } else {
                if ($current_object->id) {
                    ProductLabel::removeShopLabels($current_object->id);
                    ProductLabel::insertShopLabels($current_object->id, $this->context->shop->id);
                }
            }
        }

        /* delete sticker banner with all related data */
        if (Tools::isSubmit('deletefmm_stickersbanners')) {
            parent::postProcess();
            $id_sticker = (int) Tools::getValue('stickersbanners_id');
            $sticker_banner = new ProductLabel($id_sticker, $this->context->language->id);
            $res = ProductLabel::deleteBannerStickerById($id_sticker, $this->context->shop->id);
        }
        /* end delete sticker banner */

        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->bulkUpdateStatus(true);
        } elseif (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->bulkUpdateStatus(false);
        } elseif (Tools::isSubmit('banner_status' . $this->table) && Tools::getValue('stickersbanners_id')) {
            $banner_sticker = new ProductLabel((int) Tools::getValue('stickersbanners_id'));
            $status = !$banner_sticker->banner_status;
            $rule = new Rules((int) Rules::getRuleByBannerStickerId((int) $banner_sticker->stickersbanners_id, $this->context->shop->id)['fmm_stickers_rules_id']);

            if (Validate::isLoadedObject($banner_sticker) && Validate::isLoadedObject($rule)) {
                $banner_sticker->banner_status = (bool) $status;
                $rule->status = (bool) $status;

                if ($rule->update() && $banner_sticker->update()) {
                    $this->confirmations[] = $this->l('Sticker(s) successfully updated.');
                }
            }
        } elseif (Tools::isSubmit('submitBulkdeleteSelection' . $this->table)) {
            $this->processBulkDeleteSelection();
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['save']);
        unset($this->toolbar_btn['cancel']);
    }

    public function renderShops()
    {
        $this->fields_form = [
            'form' => [
                'id_form' => 'field_shops',
                'input' => [
                    [
                        'type' => 'shop',
                        'label' => $this->l('Shop association:'),
                        'name' => 'checkBoxShopAsso',
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];
        $helper->id = (int) Tools::getValue('sticker_id');
        $helper->identifier = $this->identifier;
        $helper->tpl_vars = array_merge([
            'languages' => $this->getLanguages(),
            'id_language' => $this->context->language->id,
        ]);

        return $helper->renderAssoShop();
    }

    public function getShopValues($object)
    {
        return ['shop' => $this->getFieldValue($object, 'shop')];
    }

    protected function getSearchProducts()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            exit(json_encode($this->l('Found Nothing.')));
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ($items && ($disableCombination || $excludeIds)) {
            $results = [];
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int) $item['id_product'];
                } else {
                    $cover = Product::getCover($item['id_product']);
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'] . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''),
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], ($item['id_image']) ? $item['id_image'] : $cover['id_image'], $this->getFormatedName('home'))),
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
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
                            $k = $k;
                            $cover = Product::getCover($item['id_product']);
                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                            !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' ' . $combination['group_name'] . '-' . $combination['attribute_name']
                                : $results[$combination['id_product_attribute']]['name'] = $item['name'] . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], ($combination['id_image']) ? $combination['id_image'] : $cover['id_image'], $this->getFormatedName('home')));
                            }
                        }
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $this->getFormatedName('home'))),
                        ];
                    }
                } else {
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $this->getFormatedName('home'))),
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

    protected function bulkUpdateStatus($status)
    {
        $selectedItems = Tools::getValue($this->table . 'Box');

        if (is_array($selectedItems) && !empty($selectedItems)) {
            foreach ($selectedItems as $id) {
                $banner_sticker = new ProductLabel((int) $id);
                $rule = new Rules((int) Rules::getRuleByBannerStickerId((int) $banner_sticker->stickersbanners_id, $this->context->shop->id)['fmm_stickers_rules_id']);

                if (Validate::isLoadedObject($banner_sticker) && Validate::isLoadedObject($rule)) {
                    $banner_sticker->banner_status = (bool) $status;
                    $rule->status = (bool) $status;

                    if ($rule->update() && $banner_sticker->update()) {
                        $this->confirmations[] = $this->l('Banner Sticker(s) successfully updated.');
                    }
                }
            }
        }
    }

    public function processBulkDeleteSelection()
    {
        $selectedItems = Tools::getValue($this->table . 'Box');

        if (is_array($selectedItems) && !empty($selectedItems)) {
            foreach ($selectedItems as $id) {
                $banner_sticker = new ProductLabel((int) $id);

                if (Validate::isLoadedObject($banner_sticker)) {
                    $result = ProductLabel::deleteBannerStickerById((int) $banner_sticker->stickersbanners_id, $this->context->shop->id);

                    if ($result) {
                        $this->confirmations[] = $this->l('Banner Sticker(s) successfully deleted.');
                    }
                }
            }
        }
    }
}
