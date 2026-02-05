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

class AdminStickersController extends ModuleAdminController
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
            'sticker_size' => [
                'title' => $this->module->l('Product Page Sticker Size'),
                'width' => 'auto',
            ],
            'sticker_opacity' => [
                'title' => $this->module->l('Sticker Opacity'),
                'width' => 'auto',
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
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
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
        $this->_where = 'AND a.text_status = 0';
        $this->_use_found_rows = false;

        return parent::renderList();
    }

    public function renderForm()
    {
        $languages = Language::getLanguages();
        $module = new ProductLabelsandStickers();
        $current_object = $this->loadObject(true);
        $id = (int) Tools::getValue('sticker_id');

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
        $sticker_link = '';
        $hints = '';
        $sticker_size = '';
        $sticker_opacity = '';
        $sticker_size_list = '';
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
        if ($id != 0) {
            $this->context->smarty->assign('sticker_id', (int) $id);
            $colors = $current_object->getColors($id);
            $sticker_name = $current_object->sticker_name;
            $sticker_size = $current_object->sticker_size;
            $sticker_opacity = $current_object->sticker_opacity;
            $sticker_size_list = $current_object->sticker_size_list;
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
            $product = $current_object->product;
            $listing = $current_object->listing;
        }
        $informations = _PS_MODULE_DIR_ . 'productlabelsandstickers/views/templates/admin/stickers/info.tpl';
        $this->context->smarty->assign([
            'show_toolbar' => true,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'title' => $this->l('Product labels and Stickers'),
            'currentToken' => $this->token,
            'currentIndex' => self::$currentIndex,
            'informations' => $informations,
            'currentTab' => $this,
            'sticker_name' => $sticker_name,
            'sticker_size' => $sticker_size,
            'sticker_opacity' => $sticker_opacity,
            'sticker_size_list' => $sticker_size_list,
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
            'product' => $product,
            'listing' => $listing,
        ]);

        return parent::renderForm();
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
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $id = (int) Tools::getValue('sticker_id');
            $align_x = Tools::getValue('x_align');
            if (empty($align_x)) {
                $this->errors[] = $this->l('Please select an alignment.');
            } elseif ((!isset($_FILES['sticker_image']) || !$_FILES['sticker_image']['tmp_name']) && $id <= 0) {
                $this->errors[] = $this->l('Please select an image icon.');
            }
            parent::postProcess();
            $id = (int) Tools::getValue('sticker_id');
            $current_object = $this->loadObject(true);

            $id_lang = (int) Context::getContext()->language->id;
            if (!$id_lang) {
                $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            }
            $shops = Tools::getValue('checkBoxShopAsso_' . $this->table);
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
            }
        }
        if (Tools::isSubmit('deletefmm_stickers')) {
            parent::postProcess();
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
}
