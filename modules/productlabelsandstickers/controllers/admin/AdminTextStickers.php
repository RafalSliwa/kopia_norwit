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

class AdminTextStickersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'fmm_stickers';
        $this->className = 'Stickers';
        $this->identifier = 'sticker_id';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;

        parent::__construct();

        $this->context = Context::getContext();

        $this->fields_list = [
            'sticker_id' => [
                'title' => '#',
                'width' => 25,
            ],
            'sticker_name' => [
                'title' => $this->module->l('Sticker Name'),
                'width' => 'auto',
            ],
            'title' => [
                'title' => $this->module->l('Sticker Text'),
                'width' => 'auto',
                'lang' => true,
            ],
            'sticker_size' => [
                'title' => $this->module->l('Product Page Sticker Size'),
                'width' => 'auto',
            ],
            'sticker_opacity' => [
                'title' => $this->module->l('Sticker Opacity'),
                'width' => 'auto',
            ],
            'status' => [
                'title' => $this->module->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'width' => 'auto',
                'align' => 'center',
                'orderby' => false,
            ],
            'sticker_size_list' => [
                'title' => $this->module->l('Listing Page Sticker Size'),
                'width' => 'auto',
            ],
            'sticker_image' => [
                'title' => $this->l('Image'),
                'align' => 'center',
                'callback' => 'getStickerImage',
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

    public function getStickerImage($echo, $row)
    {
        $image_path = $echo;
        $image_path = $row['sticker_image'];
        $base_img = __PS_BASE_URI__;
        if ($image_path != '') {
            return '<img src="' . $base_img . 'img/' . $image_path . '" style="width:100px" />';
        } else {
            return $this->l('No');
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('duplicate');
        $this->_where = 'AND a.text_status > 0';
        $this->_use_found_rows = false;
        // $this->textstickerlist = $this->getStickerList(parent::renderList());
        return parent::renderList();
    }

    public function getStickerList($list)
    {
        return $list;
    }

    public function renderForm()
    {
        $languages = Language::getLanguages();
        $module = new ProductLabelsandStickers();
        $current_object = $this->loadObject(true);
        $id = (int) Tools::getValue('sticker_id');
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $id_lang = (int) $this->context->language->id;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }

        $shops = '';
        $selected_shops = '';
        if (Shop::isFeatureActive() && $id) {
            $shops = $this->renderShops();
            $assoc_shops = Stickers::getShopStickers($id);
            $selected_shops = ($current_object && $assoc_shops = Stickers::getShopStickers($id)) ? implode(',', $assoc_shops) : '';
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
        $this->context->smarty->assign('id_sticker', (int) $id);

        $sticker_name = '';
        $sticker_type = '';
        $hints = '';
        $sticker_size = '';
        $sticker_opacity = '';
        $sticker_link = '';
        $sticker_size_list = '';
        $sticker_size_home = '';
        $sticker_image = '';
        $x_align = '';
        $y_align = '';
        $medium_width = 20;
        $medium_height = 20;
        $medium_x = 0;
        $medium_y = 0;
        $small_width = 20;
        $small_height = 20;
        $small_x = 0;
        $small_y = 0;
        $thickbox_width = 30;
        $thickbox_height = 30;
        $thickbox_x = 0;
        $thickbox_y = 0;
        $large_width = 30;
        $large_height = 30;
        $large_x = 0;
        $large_y = 0;
        $home_width = 20;
        $home_height = 20;
        $home_x = 0;
        $home_y = 0;
        $cart_width = 20;
        $cart_height = 20;
        $cart_x = 0;
        $cart_y = 0;
        $color = '';
        $bg_color = '';
        $font = '';
        $font_size = 0;
        $font_size_listing = 0;
        $font_size_product = 0;
        $text_status = 0;
        $expiry_date = '';
        $start_date = '';
        $y_coordinate_listing = 0;
        $y_coordinate_product = 0;
        $tip_bg = '';
        $tip_color = '';
        $tip_pos = 0;
        $tip_width = 180;
        $product = 0;
        $listing = 0;

        /* getting products */
        $products = [];
        if ($id > 0) {
            $obj = new Rules();
            $edit_data = $obj->getAllEditData($id);
            $edit_data = array_shift($edit_data);
            $edit_data['value_array'] = [];

            if (isset($edit_data['value'])) {
                $edit_data['value_array'] = explode(',', $edit_data['value']);
            }

            $shop_data = $obj->getAllEditDataShop($edit_data['fmm_stickers_rules_id']);

            $products = $edit_data['value_array'];

            if (!empty($products) && is_array($products)) {
                foreach ($products as &$product) {
                    $product = new Product((int) $product, true, (int) $id_lang);
                    $product->id_product_attribute = (int) Product::getDefaultAttribute($product->id) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                    $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById((int) $product->id_product_attribute, $id_lang) : Product::getCover($product->id);
                    if (!is_array($_cover)) {
                        $_cover = Product::getCover($product->id);
                    }

                    $product->id_image = $_cover ? $_cover['id_image'] : 0;
                }
            }
        }
        /* end getting products */

        /* get excluded products */
        $ex_products = [];
        if ($id > 0) {
            $obj = new Rules();
            $abc = [];

            if (isset($abc)) {
                if (!empty($edit_data['excluded_p'])) {
                    $abc = explode(',', $edit_data['excluded_p']);
                }
            }

            $shop_data = $obj->getAllEditDataShop($id);
            $ex_products = $abc;

            if (!empty($products) && ($abc[0] != null) && is_array($ex_products)) {
                foreach ($ex_products as &$product) {
                    $product = new Product((int) $product, true, (int) $id_lang);
                    $product->id_product_attribute = (int) Product::getDefaultAttribute($product->id) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                    $_cover = ((int) $product->id_product_attribute > 0) ? Product::getCombinationImageById((int) $product->id_product_attribute, $id_lang) : Product::getCover($product->id);
                    if (!is_array($_cover)) {
                        $_cover = Product::getCover($product->id);
                    }

                    if (is_array($_cover)) {
                        // Check if $_cover is an array before accessing its offset
                        $product->id_image = $_cover['id_image'];
                    } else {
                        $product->id_image = null;
                    }
                }
            }
        }

        /* end get excluded products */
        if ($id != 0) {
            $this->context->smarty->assign('sticker_id', (int) $id);
            $colors = $current_object->getColors($id);
            $sticker_name = $current_object->sticker_name;

            $fstickerrule = Rules::getRuleByStickerId($current_object->sticker_id, $this->context->shop->id);

            $fstickershop = new Stickers();
            $fstickershop = $fstickershop->getShopStickers((int) $current_object->sticker_id);

            $sticker_type = $current_object->sticker_type;
            $sticker_size = $current_object->sticker_size;
            $sticker_opacity = $current_object->sticker_opacity;
            $sticker_size_list = $current_object->sticker_size_list;

            $sticker_size_home = $current_object->sticker_size_home;

            $sticker_image = $current_object->sticker_image;
            $x_align = $current_object->x_align;
            $y_align = $current_object->y_align;
            $medium_width = $current_object->medium_width;
            $medium_height = $current_object->medium_height;
            $medium_x = $current_object->medium_x;
            $medium_y = $current_object->medium_y;
            $small_width = $current_object->small_width;
            $small_height = $current_object->small_height;
            $small_x = $current_object->small_x;
            $small_y = $current_object->small_y;
            $thickbox_width = $current_object->thickbox_width;
            $thickbox_height = $current_object->thickbox_height;
            $thickbox_x = $current_object->thickbox_x;
            $thickbox_y = $current_object->thickbox_y;
            $large_width = $current_object->large_width;
            $large_height = $current_object->large_height;
            $large_x = $current_object->large_x;
            $large_y = $current_object->large_y;
            $home_width = $current_object->home_width;
            $home_height = $current_object->home_height;
            $home_x = $current_object->home_x;
            $home_y = $current_object->home_y;
            $cart_width = $current_object->cart_width;
            $cart_height = $current_object->cart_height;
            $cart_x = $current_object->cart_x;
            $cart_y = $current_object->cart_y;
            $color = $colors['color'];
            $bg_color = $colors['bg_color'];
            $font = $current_object->font;
            $font_size = $current_object->font_size;
            $font_size_listing = $current_object->font_size_listing;
            $font_size_product = $current_object->font_size_product;
            $text_status = $current_object->text_status;
            $expiry_date = $current_object->expiry_date;
            $start_date = $current_object->start_date;
            $y_coordinate_listing = $current_object->y_coordinate_listing;
            $y_coordinate_product = $current_object->y_coordinate_product;
            $sticker_link = $current_object->url;
            $hints = $current_object->tip;
            $tip_bg = $current_object->tip_bg;
            $tip_color = $current_object->tip_color;
            $tip_pos = $current_object->tip_pos;
            $tip_width = $current_object->tip_width;

            /*
             * this line is commented because it override the 'products'
                $product = $current_object->product;
            */

            $listing = $current_object->listing;
            $home = $current_object->home;
        }

        /* =================================================== */
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

        $groups = Group::getGroups($this->context->language->id);
        $categories = Category::getSimpleCategories($id_lang);
        $features = Feature::getFeatures($id_lang);
        $allfeatures = [];

        foreach ($features as $key => $value) {
            $fdetail = FeatureValue::getFeatureValuesWithLang($id_lang, $value['id_feature']);

            foreach ($fdetail as $kk => $vval) {
                $fdetail[$kk]['name'] = $value['name'];
            }
            foreach ($fdetail as $newk => $newval) {
                $allfeatures[] = $newval;
            }
        }

        $myshops = Shop::getShops(true, null, false);

        /* =================================================== */
        $informations = _PS_MODULE_DIR_ . 'productlabelsandstickers/views/templates/admin/stickers/info_textbase.tpl';

        // $url = $this->context->link->getAdminLink('AdminStickersRules', true);
        $url = $this->context->link->getAdminLink('AdminTextStickers', true);

        $this->context->smarty->assign([
            'customers' => $groups,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'allfeatures' => $allfeatures,
            'brands' => $brands,
            'myshops' => $myshops,
            'fstickerrule' => isset($fstickerrule) ? $fstickerrule : [],
            'fstickershop' => isset($fstickershop) ? $fstickershop : [],
            'action_url' => $url,
            'show_toolbar' => true,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'title' => $this->l('Product labels and Stickers'),
            'currentToken' => $this->token,
            'currentIndex' => self::$currentIndex,
            'informations' => $informations,
            'currentTab' => $this,
            'sticker_name' => $sticker_name,
            'sticker_type' => $sticker_type,
            'sticker_size' => $sticker_size,
            'sticker_opacity' => $sticker_opacity,
            'sticker_size_list' => $sticker_size_list,
            'sticker_size_home' => $sticker_size_home,
            'sticker_image' => $sticker_image,
            'x_align' => $x_align,
            'y_align' => $y_align,
            'medium_width' => $medium_width,
            'medium_width' => $medium_width,
            'medium_width' => $medium_width,
            'medium_height' => $medium_height,
            'medium_x' => $medium_x,
            'medium_y' => $medium_y,
            'small_width' => $small_width,
            'small_height' => $small_height,
            'small_x' => $small_x,
            'small_y' => $small_y,
            'thickbox_width' => $thickbox_width,
            'thickbox_height' => $thickbox_height,
            'thickbox_x' => $thickbox_x,
            'thickbox_y' => $thickbox_y,
            'large_width' => $large_width,
            'large_height' => $large_height,
            'large_x' => $large_x,
            'large_y' => $large_y,
            'home_width' => $home_width,
            'home_height' => $home_height,
            'home_x' => $home_x,
            'home_y' => $home_y,
            'cart_width' => $cart_width,
            'cart_height' => $cart_height,
            'cart_x' => $cart_x,
            'cart_y' => $cart_y,
            '_PS_MODULE_DIR_' => _PS_MODULE_DIR_,
            'base_uri' => __PS_BASE_URI__,
            'color' => $color,
            'bg_color' => $bg_color,
            'font' => $font,
            'font_size' => (int) $font_size,
            'font_size_listing' => (int) $font_size_listing,
            'font_size_product' => (int) $font_size_product,
            'text_status' => (int) $text_status,
            'expiry_date' => $expiry_date,
            'start_date' => $start_date,
            'y_coordinate_listing' => $y_coordinate_listing,
            'y_coordinate_product' => $y_coordinate_product,
            'sticker_link' => $sticker_link,
            'hints' => $hints,
            'tip_bg' => $tip_bg,
            'tip_color' => $tip_color,
            'tip_pos' => $tip_pos,
            'tip_width' => $tip_width,
            'product' => $current_object->product,
            'products' => $products,
            'ex_products' => $ex_products,
            'listing' => $listing,
            'home' => isset($home) && $home,
        ]);
        // parent::renderForm();
        return $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/stickers/helpers/form/text.tpl');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(['colorpicker_assets_path' => $this->module->getPathUri() . 'views/img/']);
        $this->addJqueryUI(['ui.datepicker']);
        $this->addJs([
            __PS_BASE_URI__ . 'js/jquery/plugins/jquery.colorpicker.js',
            $this->module->getPathUri() . 'views/js/color-picker-assests.js',
        ]);
    }

    public function postProcess()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            // $class = new Rules();
            $sticker_type = Tools::getValue('sticker_type');

            $align_x = Tools::getValue('x_align');
            $color = Tools::getValue('color');
            $bg_color = Tools::getValue('bg_color');
            $sticker_text = Tools::getValue('sticker_text' . $default_lang);

            /* =========================================================== */
            $fmm_stickers_rules_id = (int) Tools::getValue('fmm_stickers_rules_id');
            // $shops = Tools::getValue('shops');
            $status = (int) Tools::getValue('status');
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
            if ($sticker_type == 'text') {
                if (empty($align_x)) {
                    $this->errors[] = $this->l('Please select an alignment.');
                } elseif (empty($color)) {
                    $this->errors[] = $this->l('Please select a color.');
                } elseif (empty($bg_color)) {
                    $this->errors[] = $this->l('Please select a background color.');
                } elseif (empty($sticker_text)) {
                    $this->errors[] = $this->l('You must enter the text in default language.');
                }

                parent::postProcess();
                $current_object = $this->loadObject(true);

                $id = (int) Tools::getValue('sticker_id');
                $id_lang = (int) Context::getContext()->language->id;
                if (!$id_lang) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                }

                if (isset($_FILES['sticker_image']) && $_FILES['sticker_image']['tmp_name']) {
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers`
                    SET `sticker_image` = "" WHERE `sticker_id` = ' . (int) $id);

                    $isFileExist = _PS_IMG_DIR_ . $current_object->sticker_image;

                    if (file_exists($isFileExist)) {
                        $current_object->sticker_image = null;
                        unlink($isFileExist);
                        $directory_path = _PS_IMG_DIR_ . 'stickers/' . $current_object->sticker_id;
                    }
                }

                if ($current_object->sticker_image) {
                    $test = _PS_IMG_DIR_ . $current_object->sticker_image;
                    if (file_exists($test)) {
                        $current_object->sticker_image = null;
                        unlink($test);
                        $directory_path = _PS_IMG_DIR_ . 'stickers/' . $current_object->sticker_id;
                        rmdir($directory_path);

                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers`
                        SET `sticker_image` = "" WHERE `sticker_id` = ' . (int) $id);
                    }
                }

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
                }

                // $shops = Tools::getValue('checkBoxShopAsso_' . $this->table);
                // adding shop data
                if (Shop::isFeatureActive() && $current_object->id) {
                    Stickers::removeShopStickers($current_object->id);
                    if (isset($shops) && $shops) {
                        foreach ($shops as $id_shop) {
                            Stickers::insertShopStickers($current_object->id, $id_shop);
                        }
                    }
                } else {
                    if ($current_object->id) {
                        Stickers::removeShopStickers($current_object->id);
                        Stickers::insertShopStickers($current_object->id, $this->context->shop->id);
                    }
                }

                /* fmeupdate 4.0.0 */
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

                    // $shops = Tools::getValue('shops');
                    $class->resetShops($fmm_stickers_rules_id);

                    // $id is refer to $id_sticker
                    if ($excluded_products) {
                        $excluded_products = implode(',', $excluded_products);
                    }

                    $class->changeAll($fmm_stickers_rules_id, $id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                    $class->saveShops($fmm_stickers_rules_id, $shops);
                } else {
                    if (empty($rule)) {
                        $this->errors[] = $this->l('Please select a Rule.');
                    } else {
                        /* fmeupdate 4.0.0 */
                        if ($rule == 'brand') {
                            if (empty($brands)) {
                                $this->errors[] = $this->l('Please select a brand.');
                            } else {
                                $rule_value = implode(',', $brands);

                                if ($excluded_products) {
                                    $excluded_products = implode(',', $excluded_products);
                                }

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);

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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'product') {
                            if (empty($products)) {
                                $this->errors[] = $this->l('Please select at least one product.');
                            } else {
                                $rule_value = implode(',', $products);
                                $excluded_products = implode(',', $excluded_products);

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);

                                $class->saveShops($_id, $shops);
                            }
                        } elseif ($rule == 'customer') {
                            if (empty($groups)) {
                                $this->errors[] = $this->l('Please select at least one customer group.');
                            } else {
                                $rule_value = implode(',', $groups);

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } else {
                            if ($excluded_products) {
                                $excluded_products = implode(',', $excluded_products);
                            }

                            $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                            $class->saveShops($_id, $shops);
                        }
                        /* end fmeupdate 4.0.0 */
                    }
                } /* end fmeupdate 4.0.0 */
            } elseif ($sticker_type == 'image') {
                if (empty($align_x)) {
                    $this->errors[] = $this->l('Please select an alignment.');
                } elseif ((!isset($_FILES['sticker_image']) || !$_FILES['sticker_image']['tmp_name']) && (int) Tools::getValue('sticker_id') <= 0) {
                    $this->errors[] = $this->l('Please upload a sticker.');
                }

                parent::postProcess();

                $current_object = $this->loadObject(true);
                Stickers::overrideTextSticker((int) $current_object->sticker_id);

                $id = (int) Tools::getValue('sticker_id');
                $id_lang = (int) Context::getContext()->language->id;

                if (!$id_lang) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                }

                $sticker_text = Tools::getValue('sticker_text' . $id_lang);
                if (!empty($sticker_text)) {
                    $languages = Language::getLanguages(true);
                    $exists = (int) $current_object->getStickerIdStatic($id);
                    if ($exists > 0) {
                        foreach ($languages as $language) {
                            $current_object->updateLabelText($id, (int) $language['id_lang'], '');
                        }
                    } else {
                        foreach ($languages as $language) {
                            $current_object->insertLabelText($id, (int) $language['id_lang'], '');
                        }
                    }
                }

                // adding shop data
                if (Shop::isFeatureActive() && $current_object->id) {
                    Stickers::removeShopStickers($current_object->id);
                    if (isset($shops) && $shops) {
                        foreach ($shops as $id_shop) {
                            Stickers::insertShopStickers($current_object->id, $id_shop);
                        }
                    }
                } else {
                    if ($current_object->id) {
                        Stickers::removeShopStickers($current_object->id);
                        Stickers::insertShopStickers($current_object->id, $this->context->shop->id);
                    }
                }

                if (isset($_FILES['sticker_image']) && $_FILES['sticker_image']['tmp_name']) {
                    $image_sticker = $_FILES['sticker_image']['tmp_name'];
                    $image_name_sticker = $_FILES['sticker_image']['name'];
                    $path = _PS_IMG_DIR_ . 'stickers/' . $id . '/';
                    $dir = $path . $image_name_sticker;
                    if (!is_dir(_PS_IMG_DIR_ . 'stickers/' . $id)) {
                        @mkdir(_PS_IMG_DIR_ . 'stickers/' . $id, 0777, true);
                    }
                    move_uploaded_file($image_sticker, $dir);
                    $imgPath = 'stickers/' . $id . '/' . $image_name_sticker;

                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers`
                    SET `sticker_image` = "' . pSQL($imgPath) . '"
                    WHERE `sticker_id` = ' . (int) $id);
                } else {
                    $this->errors[] = $this->l('Please upload a sticker.');
                }

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

                    // $id = $id_sticker
                    $class->resetShops($fmm_stickers_rules_id);

                    if ($excluded_products) {
                        $excluded_products = implode(',', $excluded_products);
                    }

                    $class->changeAll($fmm_stickers_rules_id, $id, 0, $status, '', $rule, $rule_value, $status, $start_date, $expiry_date, $excluded_products);
                    $class->saveShops($fmm_stickers_rules_id, $shops);
                } else {
                    if (empty($rule)) {
                        $this->errors[] = $this->l('Please select a Rule.');
                    } else {
                        $shops = Tools::getValue('shops');

                        if ($rule == 'customer') {
                            if (empty($groups)) {
                                $this->errors[] = $this->l('Please select at least one customer group.');
                            } else {
                                $rule_value = implode(',', $groups);
                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
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

                                $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                                $class->saveShops($_id, $shops);
                            }
                        } else {
                            if ($excluded_products) {
                                $excluded_products = implode(',', $excluded_products);
                            }

                            $_id = $class->saveAll($id, 0, $status, '', $rule, $rule_value, $start_date, $expiry_date, $excluded_products);
                            $class->saveShops($_id, $shops);
                        }
                    }
                }
            }
        }

        if (Tools::isSubmit('delete' . $this->table)) {
            $id_sticker = (int) Tools::getValue('sticker_id');
            $deleted_result = Stickers::deleteStickerById($id_sticker);
            if ($deleted_result) {
                $this->confirmations[] = $this->l('Sticker successfully deleted');
            } else {
                $this->errors[] = $this->l('Sticker deletion failed');
            }
        }

        // duplicate table row
        if (Tools::isSubmit('duplicatefmm_stickers')) {
            $id_sticker = (int) Tools::getValue('sticker_id');
            $sticker = Stickers::getStickerById($id_sticker);
            $dupli_sticker = Stickers::DuplicateRow($sticker);
        }

        if (Tools::isSubmit('submitBulkenableSelection' . $this->table)) {
            $this->bulkUpdateStatus(true);
        } elseif (Tools::isSubmit('submitBulkdisableSelection' . $this->table)) {
            $this->bulkUpdateStatus(false);
        } elseif (Tools::isSubmit('status' . $this->table) && Tools::getValue('sticker_id')) {
            $sticker = new Stickers((int) Tools::getValue('sticker_id'));
            $status = !$sticker->status;
            $rule = new Rules((int) $sticker->getStickerRule($sticker->sticker_id)['fmm_stickers_rules_id']);

            if (Validate::isLoadedObject($sticker) && Validate::isLoadedObject($rule)) {
                $sticker->status = (bool) $status;
                $rule->status = (bool) $status;
                $rule->update();
                $sticker->update();
            }
        } elseif (Tools::isSubmit('submitBulkdeleteSelection' . $this->table)) {
            $this->processBulkDeleteSelection();
        }
    }

    public function init()
    {
        parent::init();
        if (isset($_FILES['sticker_image'])) {
            $tmpName = $_FILES['sticker_image']['tmp_name'];
            if (isset($tmpName) && $tmpName) {
                list($width, $height) = getimagesize($tmpName);
                if ($width > 200 || $height > 200) {
                    $this->errors[] = $this->l('Image size must be less than 200px X 200px');

                    return;
                }
            }
        }

        if (Tools::getValue('action') == 'getSearchProducts') {
            $this->getSearchProducts();
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

    public function getSearchProducts()
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

    public static function getStatus($id)
    {
        if ((int) $id <= 0) {
            $return = 'No';
        } elseif ((int) $id > 0) {
            $return = 'Yes';
        }

        return $return;
    }

    protected function bulkUpdateStatus($status)
    {
        $selectedItems = Tools::getValue($this->table . 'Box');

        if (is_array($selectedItems) && !empty($selectedItems)) {
            foreach ($selectedItems as $id) {
                $sticker = new Stickers((int) $id);
                $rule = new Rules((int) $sticker->getStickerRule($sticker->sticker_id)['fmm_stickers_rules_id']);

                if (Validate::isLoadedObject($sticker) && Validate::isLoadedObject($rule)) {
                    $sticker->status = (bool) $status;
                    $rule->status = (bool) $status;

                    if ($rule->update() && $sticker->update()) {
                        $this->confirmations[] = $this->l('Sticker(s) successfully updated.');
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
                $sticker = new Stickers((int) $id);

                if (Validate::isLoadedObject($sticker)) {
                    $result = Stickers::deleteStickerById((int) $sticker->sticker_id);

                    if ($result) {
                        $this->confirmations[] = $this->l('Sticker(s) successfully deleted.');
                    }
                }
            }
        }
    }
}
