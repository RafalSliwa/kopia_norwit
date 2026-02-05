<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class EasyCarousels extends Module
{
    public $memo = [];

    public function __construct()
    {
        $this->name = 'easycarousels';
        $this->tab = 'front_office_features';
        $this->version = '2.7.7';
        $this->ps_versions_compliancy = ['min' => '1.6.0.4', 'max' => _PS_VERSION_];
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'b277f11ccef2f6ec16aaac88af76573e';
        parent::__construct();
        $this->displayName = $this->l('Easy carousels');
        $this->description = $this->l('Create custom carousels in just a few clicks');
        $this->db = Db::getInstance();
        $this->image_sizes = [];
        $this->is_16 = Tools::substr(_PS_VERSION_, 0, 3) === '1.6';
        $this->img_src_cache = [];
    }

    public function isMobile()
    {
        if (!isset($this->context->cookie->is_mobile)) {
            if (is_callable([$this->context, 'isMobile'])) {
                $is_mobile = $this->context->isMobile();
            } else {
                $is_mobile = $this->context->getMobileDetect()->isMobile();
            }
            $this->context->cookie->__set('is_mobile', (int) $is_mobile);
        }

        return $this->context->cookie->is_mobile;
    }

    public function getTypeNames($grouped = true)
    {
        $type_names = [
            $this->l('Carousels for any page') => [
                'newproducts' => $this->l('New products'),
                'bestsellers' => $this->l('Bestsellers'),
                'featuredproducts' => $this->l('Featured products'),
                'pricesdrop' => $this->l('On sale'),
                'catproducts' => $this->l('Products from selected categories'),
                'products' => $this->l('Selected products'),
                'viewedproducts' => $this->l('Viewed products'),
                'bymanufacturer' => $this->l('Products by manufacturers'),
                'bysupplier' => $this->l('Products by suppliers'),
                'bysupplier_' => $this->l('Products by default supplier'),
                'bytag' => $this->l('Products by tags'),
                'categories' => $this->l('Selected categories'),
                'subcategories' => $this->l('Subcategories'),
                'manufacturers' => $this->l('Manufacturers'),
                'suppliers' => $this->l('Suppliers'),
            ],
            $this->l('Carousels for product page / checkout page') => [
                'samecategory' => $this->l('Other products from same category'),
                'samefeature' => $this->l('Other products with same features'),
                'samemanufacturer' => $this->l('Other products of same brand (manufacturer)'),
                'sametag' => $this->l('Other products with same tags'),
                'accessories' => !$this->is_16 ? $this->l('Related products (defined on product sheet)')
                    : $this->l('Product accessories'),
                'boughttogether' => $this->l('Frequently bought together'),
            ],
        ];

        return $grouped ? $type_names : call_user_func_array('array_merge', array_values($type_names));
    }

    public function getFields($type, $advanced_options = [])
    {
        $fields = [];
        $int_options = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10];
        $hover_options = [
            0 => $this->l('Don\'t display'),
            1 => $this->l('Display'),
            2 => $this->l('Display on hover'),
        ];
        switch ($type) {
            case 'carousel':
                $fields = [
                    'type' => [
                        'name' => $this->l('Display type'),
                        'value' => 1,
                        'type' => 'select',
                        'select' => [
                            0 => $this->l('Simple grid'),
                            1 => $this->l('Carousel'),
                            2 => $this->l('Native horizontal scroll'),
                        ],
                    ],
                    'p' => [
                        'name' => $this->l('Pagination'),
                        'value' => 0,
                        'type' => 'select',
                        'select' => $hover_options,
                        'class' => 'c-opt',
                    ],
                    'n' => [
                        'name' => $this->l('Navigation arrows'),
                        'value' => 2,
                        'type' => 'select',
                        'select' => $hover_options,
                        'class' => 'c-opt',
                    ],
                    'a' => [
                        'name' => $this->l('Enable autoplay'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'c-opt',
                    ],
                    'ah' => [
                        'name' => $this->l('Stop autoplay on hover'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'c-opt',
                    ],
                    'ps' => [
                        'name' => $this->l('Autoplay interval'),
                        'tooltip' => $this->l('Time interval between each auto transition (in ms)'),
                        'value' => 4000,
                        'type' => 'text',
                        'class' => 'c-opt',
                    ],
                    'l' => [
                        'name' => $this->l('Loop'),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'c-opt',
                    ],
                    's' => [
                        'name' => $this->l('Animation speed (ms)'),
                        'value' => 100,
                        'type' => 'text',
                        'class' => 'c-opt',
                    ],
                    'm' => [
                        'name' => $this->l('Slides to move'),
                        'tooltip' => $this->l('Number of slides moved per transition')
                        . '. ' . $this->l('Set 0 to move all visible slides'),
                        'value' => 1,
                        'type' => 'text',
                        'class' => 'c-opt',
                    ],
                    'min_width' => [
                        'name' => $this->l('Min item width (px)'),
                        'tooltip' => $this->l('If item width is too small, number of visible columns will be adjusted')
                        . '. ' . $this->l('Check documentation for additional info'),
                        'value' => 150,
                        'type' => 'text',
                        'class' => 'c-opt',
                    ],
                    'normalize_h' => [
                        'name' => $this->l('Normalize heights'),
                        'tooltip' => $this->l('Force same height for all elements'),
                        'value' => 0,
                        'type' => 'switcher',
                    ],
                    'total' => [
                        'name' => $this->l('Total items'),
                        'value' => 10,
                        'type' => 'text',
                    ],
                    'r' => [
                        'name' => $this->l('Visible rows'),
                        'tooltip' => $this->l('You can rotate several rows at once'),
                        'value' => 1,
                        'type' => 'select',
                        'select' => $int_options,
                        'class' => 'c-opt',
                    ],
                    'i' => [
                        'name' => $this->l('Visible columns'),
                        'tooltip' => $this->l('Number of visible items in a row'),
                        'value' => 4,
                        'type' => 'select',
                        'select' => $int_options,
                    ],
                    'i_1200' => [
                        'name' => $this->l('Visible columns on displays < 1200px'),
                        'tooltip' => $this->l('Number of visible items in a row for displays smaller than 1200px.'),
                        'value' => 4,
                        'type' => 'select',
                        'select' => $int_options,
                    ],
                    'i_992' => [
                        'name' => $this->l('Visible columns on displays < 992px'),
                        'value' => 3,
                        'type' => 'select',
                        'select' => $int_options,
                    ],
                    'i_768' => [
                        'name' => $this->l('Visible columns on displays < 768px'),
                        'value' => 2,
                        'type' => 'select',
                        'select' => $int_options,
                    ],
                    'i_480' => [
                        'name' => $this->l('Visible columns on displays < 480px'),
                        'value' => 1,
                        'type' => 'select',
                        'select' => $int_options,
                    ],
                ];
                break;
            case 'exceptions':
                $fields = [
                    'display' => [
                        'name' => $this->l('Display carousel'),
                        'type' => 'custom',
                        'value' => [],
                        'selectors' => [
                            'page' => $this->getPageExceptionsOptions(),
                            'customer' => [
                                '0' => $this->l('For all customers'),
                                'group' => $this->l('Only for selected customer groups'),
                                'customer' => $this->l('Only for selected customers'),
                            ],
                        ],
                    ],
                ];
                break;
            case 'special':
                $fields = [
                    'id_feature' => [
                        'name' => $this->l('Feature group IDs'),
                        'tooltip' => $this->l('Leave it empty to display products, matching by all feature groups'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option samefeature',
                    ],
                    'last_visited' => [
                        'name' => $this->l('Consider last visited category'),
                        'tooltip' => $this->l('If not available, default category will be used'),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'special_option samecategory',
                    ],
                    'min_matches' => [
                        'name' => $this->l('Minimum matches'),
                        'tooltip' => $this->l('Display products, having at least this number of matching properties'),
                        'value' => '0',
                        'type' => 'select',
                        'select' => $int_options + ['0' => $this->l('All available')],
                        'class' => 'special_option samefeature sametag',
                    ],
                    'product_ids' => [
                        'name' => $this->l('Product ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option products',
                    ],
                    'cat_ids' => [
                        'name' => $this->l('Category ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option catproducts categories',
                    ],
                    'same_man' => [
                        'name' => $this->l('Same manufacturer'),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'special_option samecategory',
                    ],
                    'parent_ids' => [
                        'name' => $this->l('Category parents'),
                        'tooltip' => $this->l('Leave empty if you want to display subcategories of current category'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option subcategories',
                    ],
                    'id_manufacturer' => [
                        'name' => $this->l('Manufacturer ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option bymanufacturer manufacturers',
                    ],
                    'tags' => [
                        'name' => $this->l('Tags'),
                        'tooltip' => $this->l('Separated by comma (tag1, tag2, tag3 ...)'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option bytag',
                    ],
                    'id_supplier' => [
                        'name' => $this->l('Supplier ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'special_option bysupplier bysupplier_ suppliers',
                    ],
                ];
                break;
            case 'php':
                $fields = [
                    'order_by' => [
                        'name' => $this->l('Order by'),
                        'value' => 'default',
                        'type' => 'select',
                        'select' => [
                            'default' => $this->l('Default way for this carousel'),
                            'date_add' => $this->l('Date added'),
                            'date_upd' => $this->l('Date updated'),
                            'name' => $this->l('Name'),
                            'id' => $this->l('ID'),
                            'sales' => $this->l('Best sales'),
                            'random' => $this->l('Randomly'),
                            // 'reference' => $this->l('Product reference'),
                            // 'position' => $this->l('Position in category'),
                            // 'price' => $this->l('Price'),
                        ],
                        'option_classes' => array_fill_keys(['date_add', 'date_upd', 'sales'], 'p-option'),
                    ],
                    'f_cat' => [
                        'name' => $this->l('Filter by categories'),
                        'tooltip' => $this->l('Specify IDs. For example: 7, 35, 18'),
                        'type' => 'text',
                        'value' => '',
                        'class' => 'p-option not-for-some not-for-catproducts',
                    ],
                    'sales_days' => [
                        'name' => $this->l('Count sales for the last'),
                        'suffix' => $this->l('days'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'sales-days-option',
                    ],
                    'oos' => [
                        'name' => $this->l('Exclude non-available'),
                        'type' => 'select',
                        'value' => 0,
                        'select' => [
                            0 => $this->l('No'),
                            1 => $this->l('Yes'),
                            2 => $this->l('Yes, except those allowed for ordering'),
                        ],
                        'class' => 'p-option',
                    ],
                    'f_man' => [
                        'name' => $this->l('Filter by manufacturers'),
                        'tooltip' => $this->l('Specify IDs. For example: 7, 35, 18'),
                        'type' => 'text',
                        'value' => '',
                        'class' => 'p-option not-for-some not-for-bymanufacturer not-for-samemanufacturer',
                    ],
                    'consider_cat' => [
                        'name' => $this->l('Consider category'),
                        'tooltip' => $this->l('Show products only from current category')
                            . ' (' . $this->l('if carousel is displayed on category page') . ')',
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option not-for-some not-for-accessories not-for-samefeature '
                            . 'not-for-samecategory not-for-sametag not-for-samemanufacturer not-for-boughttogether',
                    ],
                ];
                break;
            case 'tpl':
                $product_manufacturer_options = [
                    '0' => $this->l('Don\'t display'),
                    '1' => $this->l('Title'),
                ];
                $img_type_options = ['--' => $this->l('None')];
                $img_type_options_classes = [];
                $required_types = ['products', 'categories', 'manufacturers', 'suppliers'];
                foreach (ImageType::getImagesTypes() as $t) {
                    $img_type = $t['name'];
                    $cls = [];
                    $include = false;
                    foreach ($required_types as $rt) {
                        if (!$t[$rt]) {
                            $cls[$rt] = 'not-for-' . Tools::substr($rt, 0, 1);
                        } else {
                            $include = true;
                            if ($rt == 'manufacturers') {
                                $product_manufacturer_options[$img_type] = $this->l('Logo') . ': ' . $img_type;
                            }
                        }
                    }
                    if ($include) {
                        $img_type_options[$img_type] = $img_type;
                        if ($cls) {
                            $img_type_options_classes[$img_type] = 'not-for-some-types ' . implode(' ', $cls);
                        }
                    }
                }
                $img_type_options['original'] = 'original';
                $product_manufacturer_options['original'] = $this->l('Logo') . ': original';
                $fields = [
                    'custom_class' => [
                        'name' => $this->l('Container class'),
                        'tooltip' => $this->l('It will be applied to container of items'),
                        'value' => '',
                        'type' => 'text',
                        'class' => 'custom-class',
                    ],
                    'external_tpl' => [
                        'name' => $this->l('Product item template'),
                        'value' => 0,
                        'type' => 'select',
                        'select' => [
                            0 => $this->l('Built-in (configurable)'),
                            1 => $this->l('External template file'),
                        ],
                        'class' => 'p-option external-tpl',
                    ],
                    'image_type' => [
                        'name' => $this->l('Image'),
                        'value' => $this->getHomeImgType(),
                        'type' => 'select',
                        'select' => $img_type_options,
                        'option_classes' => $img_type_options_classes,
                    ],
                    'second_image' => [
                        'name' => $this->l('Second image on hover'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'title_one_line' => [
                        'name' => $this->l('Title in one line'),
                        'tooltip' => $this->l('Truncate title if its length overlaps first line'),
                        'value' => 1,
                        'type' => 'switcher',
                    ],
                    'title' => [
                        'name' => $this->l('Title length (symbols)'),
                        'tooltip' => $this->l('Set 0 if you don\'t want to display title'),
                        'value' => 45,
                        'type' => 'text',
                    ],
                    'reference' => [
                        'name' => $this->l('Product reference'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'description' => [
                        'name' => $this->l('Description length'),
                        'value' => 0,
                        'type' => 'text',
                    ],
                    'product_cat' => [
                        'name' => $this->l('Product category'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'product_man' => [
                        'name' => $this->l('Product manufacturer'),
                        'value' => 0,
                        'class' => 'p-option',
                        'type' => 'select',
                        'select' => $product_manufacturer_options,
                    ],
                    'price' => [
                        'name' => $this->l('Price'),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'add_to_cart' => [
                        'name' => $this->l('Add to cart button'),
                        'value' => 1,
                        'type' => 'switcher',
                        // 'type'  => 'select',
                        // 'select' => array(
                        //     0 => 'Hide',
                        //     1 => 'Show',
                        //     2 => 'Show, with attribute selectors',
                        // ),
                        'class' => 'p-option',
                    ],
                    // 'att_links' => array(
                    //     'name'  => 'Available attribute links',
                    //     'value' => 0,
                    //     'type'  => 'select',
                    //     'select' => array(0 => '- '.'None'.' -') + $this->att('getGroups'),
                    //     'class' => 'p-option',
                    // ),
                    'view_more' => [
                        'name' => $this->l('View more'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'quick_view' => [
                        'name' => $this->l('Quick view'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'stock' => [
                        'name' => $this->l('Stock data'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'stickers' => [
                        'name' => $this->l('Stickers'),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ],
                    'view_all' => [
                        'name' => $this->l('Link to all items'),
                        'value' => 0,
                        'type' => 'select',
                        'select' => [
                            0 => $this->l('None'),
                            1 => $this->l('Displayed below carousel'),
                            2 => $this->l('Bound to carousel title'),
                        ],
                        'class' => 'special_option newproducts bestsellers ' .
                        'pricesdrop bymanufacturer bysupplier bysupplier_ catproducts view-all-link',
                    ],
                    'matches' => [
                        'name' => $this->l('Number of matches'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'special_option manufacturers suppliers',
                    ],
                    'c_matches' => [
                        'name' => $this->l('Show number of matches'),
                        'value' => 0,
                        'type' => 'select',
                        'select' => [
                            0 => $this->l('None'),
                            1 => $this->l('Products associated directly'),
                            2 => $this->l('All products from subcategories'),
                        ],
                        'class' => 'special_option categories subcategories',
                    ],
                    'external_tpl_path' => [
                        'name' => $this->l('External template path'),
                        'tooltip' => $this->l('Relative path to existing tpl file in theme directory'),
                        'value' => !$this->is_16 ? 'templates/catalog/_partials/miniatures/product.tpl' : '',
                        'type' => 'text',
                        'class' => 'p-option external-tpl-path',
                    ],
                ];
                if (Module::isInstalled('productlistthumbnails')) {
                    $fields['thumbnails'] = [
                        'name' => $this->l('Product thumbnails'),
                        'value' => 0,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ];
                }
                $available_hooks = ['ProductPriceBlock', 'ProductListReviews'];
                if (!$this->is_16) {
                    $available_hooks[] = 'ProductListFunctionalButtons';
                } else {
                    $available_hooks[] = 'ProductDeliveryTime';
                    // $fields['add_to_cart']['type'] = 'switcher';
                    // unset($fields['add_to_cart']['select']);
                }
                foreach ($available_hooks as $k => $hook) {
                    $full_hook_name = 'display' . $hook;
                    $fields[$full_hook_name] = [
                        'name' => $hook,
                        'tooltip' => sprintf($this->l('All data hooked to %s'), $full_hook_name),
                        'value' => 1,
                        'type' => 'switcher',
                        'class' => 'p-option',
                    ];
                    if (!$k) {
                        $fields[$full_hook_name]['separator'] = $this->l('Displayed hooks');
                    }
                }
                break;
        }
        if (isset($advanced_options[$type])) {
            foreach ($advanced_options[$type] as $a_name => $visible) {
                if (isset($fields[$a_name])) {
                    $fields[$a_name]['class'] = isset($fields[$a_name]['class']) ? $fields[$a_name]['class'] . ' ' : '';
                    $fields[$a_name]['class'] .= 'advanced-option';
                    if (!$visible) {
                        $opt = $fields[$a_name];
                        $opt['class'] .= ' force-hidden';
                        unset($fields[$a_name]); // move to the end
                        $fields[$a_name] = $opt;
                    }
                }
            }
        }

        return $fields;
    }

    public function getHomeImgType()
    {
        if (!isset($this->home_img_type)) {
            $this->home_img_type = !$this->is_16 ? ImageType::getFormattedName('home') :
            ImageType::getFormatedName('home');
        }

        return $this->home_img_type;
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
            $options[$k . '_all'] = sprintf($this->l('Only on %s pages'), $page);
            $options[$k] = sprintf($this->l('Only on selected %s pages'), $page);
            if ($k == 'product') {
                $options['product_category'] = $this->l('On product pages inside selected categories');
                $options['product_manufacturer'] = $this->l('On product pages of selected manufacturers');
            } elseif ($k == 'category') {
                $options['category_sub'] = $this->l('On all subcategories of selected categories');
            }
        }

        return $options;
    }

    public function install()
    {
        return parent::install()
            && $this->prepareDatabaseTables()
            && $this->prepareDemoContent()
            && $this->sliderLibrary('install')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function prepareDatabaseTables()
    {
        $sql = [];
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'easycarousels (
            id_carousel int(10) unsigned NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            hook_name varchar(128) NOT NULL,
            id_wrapper int(10) unsigned NOT NULL,
            in_tabs tinyint(1) NOT NULL DEFAULT 1,
            active tinyint(1) NOT NULL DEFAULT 1,
            position int(10) NOT NULL,
            type varchar(128) NOT NULL,
            settings text NOT NULL,
            PRIMARY KEY (id_carousel, id_shop),
            KEY hook_name (hook_name),
            KEY position (position),
            KEY active (active),
            KEY in_tabs (in_tabs)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'easycarousels_lang (
            id_carousel int(10) unsigned NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            id_lang int(10) unsigned NOT NULL,
            data text NOT NULL,
            PRIMARY KEY (id_carousel, id_shop, id_lang)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'ec_wrapper (
            id_wrapper int(10) unsigned NOT NULL AUTO_INCREMENT,
            settings text NOT NULL,
            PRIMARY KEY (id_wrapper)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'ec_hook_settings (
            hook_name varchar(64) NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            display text NOT NULL,
            caching text NOT NULL,
            exc_type tinyint(1) NOT NULL DEFAULT 1,
            exc_controllers text NOT NULL,
            PRIMARY KEY (hook_name, id_shop)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return $this->runSql($sql);
    }

    public function prepareDemoContent()
    {
        if ($demo_file_path = $this->getDemoFilePath()) {
            $this->importCarousels($demo_file_path);
        }

        return true;
    }

    public function getDemoFilePath()
    {
        $demo_file_path = $this->local_path . 'democontent/carousels-custom.txt';
        if (!file_exists($demo_file_path)) {
            $demo_file_path = $this->local_path . 'democontent/carousels' . ($this->is_16 ? '-16' : '') . '.txt';
        }

        return file_exists($demo_file_path) ? $demo_file_path : false;
    }

    public function uninstall()
    {
        $sql = [
            'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'easycarousels',
            'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'easycarousels_lang',
            'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ec_wrapper',
            'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ec_hook_settings',
        ];
        if (!$this->runSql($sql) || !parent::uninstall() || !$this->sliderLibrary('uninstall')) {
            return false;
        }
        $this->cache('clear', '');
        $this->relatedOverrides()->process('removeOverride', 'Product');

        return true;
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

    /**
     * easycarousels table has a composite KEY that cannot be autoincremented.
     **/
    public function getNewCarouselId()
    {
        $max_id = $this->db->getValue('SELECT MAX(id_carousel) FROM ' . _DB_PREFIX_ . 'easycarousels');

        return (int) $max_id + 1;
    }

    public function getNextCarouselPosition($hook_name)
    {
        $max_position = $this->db->getValue('
            SELECT MAX(position) FROM ' . _DB_PREFIX_ . 'easycarousels WHERE hook_name = \'' . pSQL($hook_name) . '\'
        ');

        return (int) $max_position + 1;
    }

    public function prepareMCEContentCSS()
    {
        $mce_content_css = $this->is_16 ? _THEME_CSS_DIR_ . 'global.css'
            : _THEME_CSS_DIR_ . 'theme.css, ' . _THEME_CSS_DIR_ . 'custom.css';
        $mce_content_css .= ', ' . $this->_path . 'views/css/mce_custom.css';

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
            } elseif ($action == 'exportCarousels') {
                return $this->exportCarousels();
            }

            return;
        }
        if (Tools::getValue('normalize_all')) {
            $this->normalizeAllCarouselSettings();
            $this->cachingSettings('adjustAll');
            $this->cache('clear', '');
            $this->context->controller->confirmations[] = 'Carousel settings normalized'
                . ', caching settings adjusted, cache cleared';
        }
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->css_files[$this->_path . 'views/css/back.css?' . $this->version] = 'all';
        $this->context->controller->js_files[] = $this->_path . 'views/js/back.js?' . $this->version;
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
        $carousels = $this->getAllCarousels();
        $hooks = $this->getAvailableHooks();

        $sorted_hooks = [];
        foreach (array_keys($carousels) as $hook_name) {
            if (!$hook_name) {
                continue;
            }
            $total = 0;
            foreach ($carousels[$hook_name] as $carousels_in_wrapper) {
                $total += count($carousels_in_wrapper);
            }
            $sorted_hooks[$hook_name] = $total;
        }
        arsort($sorted_hooks);

        foreach ($hooks as $hook_name => $count) {
            if (!isset($sorted_hooks[$hook_name])) {
                $sorted_hooks[$hook_name] = $count;
            }
        }

        $iso = $this->context->language->iso_code;
        $this->context->smarty->assign([
            'js_vars' => [
                'iso' => file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en',
                'mce_content_css' => $this->prepareMCEContentCSS(),
                'ad' => dirname($_SERVER['PHP_SELF']),
                'failedTxt' => htmlspecialchars_decode($this->failed_txt),
                'savedTxt' => htmlspecialchars_decode($this->saved_txt),
                'areYouSureTxt' => htmlspecialchars_decode($this->l('Are you sure?')),
            ],
            'hooks' => $sorted_hooks,
            'carousels' => $carousels,
            'custom_code' => $this->customCode('get'),
            'slider_library' => [
                'data' => $this->sliderLibrary('getData'),
                'options' => $this->sliderLibrary('getOptions'),
            ],
            'type_names' => $this->getTypeNames(),
            'id_lang_current' => $this->context->language->id,
            'iso_lang_current' => $iso,
            'overrides_data' => $this->relatedOverrides()->getData(),
            'ec' => $this,
            'howto_tpl_path' => $this->getTemplatePath('views/templates/admin/importer-how-to.tpl'),
            'info_links' => [
                'changelog' => $this->_path . 'Readme.md?v=' . $this->version,
                'documentation' => $this->_path . 'readme_en.pdf?v=' . $this->version,
                'contact' => 'https://addons.prestashop.com/contact-form.php?id_product=18853',
                'modules' => 'https://addons.prestashop.com/en/2_community-developer?contributor=64815',
            ],
            'is_16' => $this->is_16,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function ajaxProcessOverride()
    {
        $override_action = Tools::getValue('override_action');
        $class_name = Tools::getValue('class_name');
        $ret = ['processed' => $this->relatedOverrides()->process($override_action, $class_name)];
        if (is_string($ret['processed'])) {
            $this->throwError($ret['processed']);
        }
        exit(json_encode($ret));
    }

    public function accessoriesDisplayed($id_shop = null, $active = null)
    {
        $cache_key = 'accs_' . (int) $id_shop . '_' . (int) $active;
        $existing_id = $this->cache('get', $cache_key);
        if ($existing_id === false) {
            $existing_id = (int) $this->db->getValue('
                SELECT id_carousel FROM ' . _DB_PREFIX_ . 'easycarousels WHERE type = \'accessories\''
                . ($id_shop ? ' AND id_shop = ' . (int) $id_shop : '') . ($active ? ' AND active = 1' : '') . '
            ');
            $this->cache('save', $cache_key, $existing_id);
        }

        return $existing_id;
    }

    public function renderPossibleWarnings()
    {
        $html = '';
        $file_warnings = $customizable_layout_files = [];
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
            $customized_file_path = _PS_THEME_DIR_;
            if ($this->is_16 && $ext != 'tpl') {
                $customized_file_path .= $ext . '/';
            }
            $customized_file_path .= 'modules/' . $this->name . $file;
            if (file_exists($customized_file_path)) {
                $original_file_path = $this->local_path . $file;
                $original_rows = file($original_file_path);
                $original_identifier = trim(array_pop($original_rows));
                $customized_rows = file($customized_file_path);
                $customized_identifier = trim(array_pop($customized_rows));
                if (Tools::substr($original_identifier, 1, 7) === '* since'
                    && $original_identifier != $customized_identifier) {
                    $path = explode('/themes/', $customized_file_path);
                    $path = isset($path[1]) ? '/themes/' . $path[1] : $file;
                    $file_warnings[$path] = $original_identifier;
                }
            }
        }
        $this->context->smarty->assign([
            'file_warnings' => $file_warnings,
        ]);
        $html .= $this->display(__FILE__, 'views/templates/admin/warnings.tpl');

        return $html;
    }

    public function getAvailableHooks()
    {
        $available_hooks = [];
        foreach (get_class_methods(__CLASS__) as $m) {
            if (Tools::substr($m, 0, 11) === 'hookDisplay') {
                $available_hooks[str_replace('hookDisplay', 'display', $m)] = 0;
            }
        }
        if ($this->is_16) {
            $to_exclude = array_fill_keys(['displayFooterAfter', 'displayFooterBefore', 'displayOrderConfirmation2',
                'displayCrossSellingShoppingCart', 'displayReassurance', 'displayNavFullWidth',
                'displayNav1', 'displayNav2', 'displaySearch', 'displayProductAdditionalInfo'], 0);
            $available_hooks = array_diff_key($available_hooks, $to_exclude);
        } else {
            unset($available_hooks['displayProductButtons']); // alias for displayProductAdditionalInfo
        }
        unset($available_hooks['displayHeader']); // deprecated
        ksort($available_hooks);

        return $available_hooks;
    }

    public function exportCarousels()
    {
        $languages = Language::getLanguages(false);
        $lang_id_iso = [];
        foreach ($languages as $lang) {
            $lang_id_iso[$lang['id_lang']] = $lang['iso_code'];
        }
        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $tables_to_export = [
            'easycarousels',
            'easycarousels_lang',
            'ec_wrapper',
            'ec_hook_settings',
            'hook_module',
        ];
        $export_data = [];
        foreach ($tables_to_export as $table_name) {
            $data_from_db = $this->db->executeS('SELECT * FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`');
            $ret = [];
            switch ($table_name) {
                case 'easycarousels':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $ret[$id_shop][$d['id_carousel']] = $d;
                    }
                    break;
                case 'easycarousels_lang':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $l_iso = $d['id_lang'] == $id_lang_default ? 'LANG_ISO_DEFAULT' : $lang_id_iso[$d['id_lang']];
                        $ret[$id_shop][$l_iso][$d['id_carousel']] = $d;
                    }
                    break;
                case 'ec_hook_settings':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $ret[$id_shop][$d['hook_name']] = $d;
                    }
                    break;
                case 'hook_module':
                    foreach ($data_from_db as $d) {
                        if ($d['id_module'] == $this->id) {
                            $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                            $hook_name = Hook::getNameByid($d['id_hook']);
                            $ret[$id_shop][$hook_name] = $d['position'];
                        }
                    }
                    break;
                default:
                    $ret = $data_from_db;
                    break;
            }
            $export_data[$table_name] = $ret;
        }
        foreach (['css', 'js'] as $type) {
            $custom_file_path = $this->customCode('getFilePath', ['type' => $type]);
            if (file_exists($custom_file_path)) {
                $export_data[$type] = Tools::file_get_contents($custom_file_path);
            }
        }
        $export_data = json_encode($export_data);
        $file_name = 'carousels-' . date('d-m-Y') . '.txt';
        header('Content-disposition: attachment; filename=' . $file_name);
        header('Content-type: text/plain');
        echo $export_data;
        exit;
    }

    public function ajaxImportCarousels()
    {
        if ($this->importCarousels()) {
            $ret = ['upd_html' => $this->import_response . $this->displayForm()];
        } else {
            $ret = ['errors' => $this->import_response];
        }
        exit(json_encode($ret));
    }

    public function getRequiredFields($advanced_options = [])
    {
        $keys = ['php', 'special', 'tpl', 'carousel'];
        $required_fields = [];
        foreach ($keys as $key) {
            $required_fields[$key] = $this->getFields($key, $advanced_options);
        }

        return $required_fields;
    }

    public function normalizeCarouselSettings($settings, $required_fields = [], $retro = false)
    {
        if ($json = is_string($settings)) {
            $settings = json_decode($settings, true);
        }
        if ($retro) {
            if (isset($settings['php']['randomize'])) {
                if (!isset($settings['php']['order_by'])) {
                    $settings['php']['order_by'] = 'random';
                }
                unset($settings['php']['randomize']);
            }
            if (!empty($settings['exceptions']['page']['type'])
                && $settings['exceptions']['page']['type'] == 'subcategory') {
                $settings['exceptions']['page']['type'] = 'category_sub';
            }
        }
        $required_fields = $required_fields ?: $this->getRequiredFields();
        foreach ($required_fields as $type => $fields) {
            foreach ($fields as $name => $field) {
                if (!isset($settings[$type][$name])) {
                    $settings[$type][$name] = $field['value'];
                }
            }
        }

        return $json ? json_encode($settings) : $settings;
    }

    public function normalizeAllCarouselSettings()
    {
        $all_carousels = $this->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
        $required_fields = $this->getRequiredFields();
        $rows = [];
        foreach ($all_carousels as $c) {
            $settings = $this->normalizeCarouselSettings($c['settings'], $required_fields, true);
            if ($settings != $c['settings']) {
                $rows[] = '(' . (int) $c['id_carousel'] . ', ' . (int) $c['id_shop'] . ', \'' . pSQL($settings) . '\')';
            }
        }
        if ($rows) {
            $this->db->execute('
                INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, settings)
                VALUES ' . implode(', ', $rows) . ' ON DUPLICATE KEY UPDATE settings = VALUES(settings)
            ');
        }
    }

    public function importCarousels($file_path = false)
    {
        if (!$file_path) {
            if (!isset($_FILES['carousels_data_file'])
            || !is_uploaded_file($_FILES['carousels_data_file']['tmp_name'])) {
                return $this->displayError($this->l('File not uploaded'));
            }
            $file_path = $_FILES['carousels_data_file']['tmp_name'];
        }
        $imported_data = json_decode(Tools::file_get_contents($file_path), true);
        $shop_ids = Shop::getShops(false, null, true);
        $lang_iso_id = array_column(Language::getLanguages(false), 'id_lang', 'iso_code');
        $tables_to_fill = $hooks_data = [];
        $required_fields = $this->getRequiredFields();
        foreach ($shop_ids as $id_shop) {
            $shop_key = isset($imported_data['easycarousels'][$id_shop]) ? $id_shop : 'ID_SHOP_DEFAULT';
            $carousels = $imported_data['easycarousels'][$shop_key];
            foreach ($carousels as $c) {
                $c['id_shop'] = $id_shop;
                $c['settings'] = $this->normalizeCarouselSettings($c['settings'], $required_fields, true);
                if (!isset($c['id_wrapper'])) {
                    $c['id_wrapper'] = 0; // retro compatibility
                }
                $tables_to_fill['easycarousels'][] = $c;
                $hooks_data[$id_shop][$c['hook_name']] = isset($imported_data['hook_module'][$shop_key][$c['hook_name']])
                    ? $imported_data['hook_module'][$shop_key][$c['hook_name']] : 1;
            }
            $l_shop_key = isset($imported_data['easycarousels_lang'][$id_shop]) ? $id_shop : 'ID_SHOP_DEFAULT';
            $carousels_lang = $imported_data['easycarousels_lang'][$l_shop_key];
            foreach ($lang_iso_id as $iso => $id_lang) {
                $iso_key = isset($carousels_lang[$iso]) ? $iso : 'LANG_ISO_DEFAULT';
                foreach ($carousels_lang[$iso_key] as $row) {
                    $row['id_shop'] = $id_shop;
                    $row['id_lang'] = $id_lang;
                    $tables_to_fill['easycarousels_lang'][] = $row;
                }
            }
            if (!empty($imported_data['ec_wrapper'])) {
                $tables_to_fill['ec_wrapper'] = $imported_data['ec_wrapper'];
            }
            // ec_hook_settings
            if ($imported_data['ec_hook_settings']) {
                if (isset($imported_data['ec_hook_settings'][$id_shop])) {
                    $settings_rows = $imported_data['ec_hook_settings'][$id_shop];
                } else {
                    $settings_rows = $imported_data['ec_hook_settings']['ID_SHOP_DEFAULT'];
                }
                $required_columns = ['hook_name', 'id_shop', 'display', 'caching', 'exc_type', 'exc_controllers'];
                foreach ($settings_rows as $row) {
                    $compatible_row = [];
                    foreach ($required_columns as $key) {
                        $compatible_row[$key] = isset($row[$key]) ? $row[$key] : '';
                    }
                    $compatible_row['id_shop'] = $id_shop;
                    $tables_to_fill['ec_hook_settings'][] = $compatible_row;
                }
            }
        }
        $sql = [];
        foreach ($tables_to_fill as $table_name => $rows_to_insert) {
            $current_db_columns = array_column($this->db->executeS('
                SHOW COLUMNS FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`
            '), 'Field');
            $imported_db_columns = array_keys(current($rows_to_insert));
            if ($current_db_columns != $imported_db_columns) {
                $err = $this->l('This file can not be used for import. Reason: Database tables don\'t match (%s).');

                return $this->throwError(sprintf($err, _DB_PREFIX_ . $table_name));
            }
            $sql[] = 'DELETE FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`';
            $rows = [];
            foreach ($rows_to_insert as $row) {
                foreach ($row as $name => &$r) {
                    $allow_html = $name == 'data' ? true : false;
                    $r = pSQL($r, $allow_html);
                }
                $rows[] = '(\'' . implode('\', \'', $row) . '\')';
            }
            if ($rows && $current_db_columns) {
                $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . bqSQL($table_name) . '`
                (`' . implode('`, `', array_map('bqSQL', $current_db_columns)) . '`)
                VALUES ' . implode(', ', $rows);
            }
        }
        if (!$sql) {
            $this->throwError($this->l('Nothing to import'));
        }
        if ($imported = $this->runSql($sql)) {
            foreach ($hooks_data as $id_shop => $hook_list) {
                if ($id_shop != $this->context->shop->id) {
                    Cache::clean('hook_module_list');
                    $this->setCustomShopContext($id_shop);
                }
                foreach ($hook_list as $hook_name => $ec_position) {
                    if (Validate::isHookName($hook_name)) {
                        $this->registerHook($hook_name, [$id_shop]);
                        $this->updatePosition(Hook::getIdByName($hook_name), 0, $ec_position);
                    }
                }
            }
            $this->restoreOriginalContext();
            foreach (['css', 'js'] as $type) {
                if (!empty($imported_data[$type])) {
                    file_put_contents($this->customCode('getFilePath', ['type' => $type]), $imported_data[$type]);
                }
            }
            $this->cache('clear', '');
            $this->cachingSettings('adjustAll');
            $this->import_response = $this->displayConfirmation($this->l('Data was successfully  imported'));
        } else {
            $this->import_response = $this->displayError($this->l('An error occured while importing data'));
        }

        return $imported;
    }

    public function setCustomShopContext($id_shop)
    {
        if (!isset($this->memo['context'])) {
            $this->memo['context'] = ['type' => Shop::getContext(), 'id' => null];
            if ($this->memo['context']['type'] == Shop::CONTEXT_GROUP) {
                $this->memo['context']['id'] = $this->context->shop->id_shop_group;
            } elseif ($this->memo['context']['type'] == Shop::CONTEXT_SHOP) {
                $this->memo['context']['id'] = $this->context->shop->id;
            }
        }
        Shop::setContext(Shop::CONTEXT_SHOP, $id_shop);
        unset($this->memo['shop_ids']);
    }

    public function restoreOriginalContext()
    {
        if (!empty($this->memo['context'])) {
            Shop::setContext($this->memo['context']['type'], $this->memo['context']['id']);
        }
        unset($this->memo['shop_ids']);
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

    /*
    * @deprecated since 2.7.5
    */
    public function hookDisplayHeader()
    {
        return '';
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->sliderLibrary('load'); // front.js is included together with other libraries if required
        $this->addJS('custom.js');
        $this->addCSS('front.css');
        $this->addCSS('custom.css');
        Media::addJsDef([
            'is_16' => $this->is_16,
            'ec_ajax_path' => $this->getAjaxPath(),
        ]);
        $this->context->smarty->assign(['ec_is_mobile' => $this->isMobile()]);
        if ($this->is_16) {
            $this->context->controller->addjqueryPlugin('fancybox');
        }
        switch ($this->getFullControllerName()) {
            case 'category':
                $this->lastVisitedCategory('set', ['id_cat' => Tools::getValue('id_category')]);
                break;
            case 'product':
                if ($id_product = $this->context->controller->getProduct()->id) {
                    $this->addViewedProduct($id_product);
                    $this->context->ec_accessories = $this->accessoriesDisplayed($this->context->shop->id, true);
                }
                break;
        }
    }

    public function lastVisitedCategory($action, $params = [])
    {
        $ret = 0;
        switch ($action) {
            case 'get':
                if (Tools::isSubmit('ec_last_visited')) {
                    $ret = (int) Tools::getValue('ec_last_visited'); // for dynamic carousels
                } elseif (!empty($this->context->cookie->ec_last_visited)
                    && $this->getFullControllerName() == 'product' && $id_product = Tools::getValue('id_product')) {
                    $data = explode('#', $this->context->cookie->ec_last_visited);
                    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                    if ($id_product == $data[1] || strstr($referer, $data[2]) == $data[2]) {
                        $ret = $id_cat = (int) $data[0];
                        // upate cookie value in case if user reloads product-page or clicks on a related product
                        $this->lastVisitedCategory('set', [
                            'id_cat' => $id_cat,
                            'id_product' => $id_product,
                        ]);
                    }
                }
                break;
            case 'set':
                if (isset($_SERVER['REQUEST_URI'])) {
                    $params += ['id_cat' => 0, 'id_product' => 0];
                    $cookie_value = $params['id_cat'] . '#' . $params['id_product'] . '#' . $_SERVER['REQUEST_URI'];
                    $ret = $this->context->cookie->__set('ec_last_visited', $cookie_value);
                }
                break;
        }

        return $ret;
    }

    public function addViewedProduct($id_product)
    {
        $viewed = !empty($this->context->cookie->ec_viewed) ?
            $this->formatIDs($this->context->cookie->ec_viewed, false) : [];
        $viewed = [$id_product => $id_product] + $viewed; // last viewed comes first
        $viewed = array_slice($viewed, 0, 10, true);
        $this->context->cookie->__set('ec_viewed', implode(',', $viewed));
    }

    public function ajaxAction($action)
    {
        $ret = [];
        switch ($action) {
            case 'getCarouselsInHook':
                // $time_start = microtime(true);
                if (!$this->is_16) {
                    $this->context->smarty->assign(['urls' => $this->context->controller->getTemplateVarUrls()]);
                }
                $hook_name = Tools::getValue('hook_name');
                $html = $this->displayCarouselsInHook(
                    $hook_name,
                    $this->formatIDs(Tools::getValue('id_product'), false),
                    Tools::getValue('id_category'),
                    Tools::getValue('current_id'),
                    Tools::getValue('current_controller'),
                    $this->getHookDisplaySettings($hook_name),
                    $this->getHookCachingSettings($hook_name)
                );
                $ret = [
                    'carousels_html' => $html,
                    // 'time_'.$hook_name => microtime(true) - $time_start,
                ];
                break;
            case 'updateCombination':
                $ret = $this->att(
                    'getCombinationDataByAtts',
                    ['att_ids' => Tools::getValue('att_ids'), 'id_product' => Tools::getValue('id_product')]
                );
                break;
        }
        exit(json_encode($ret));
    }

    public function getHookCacheKey($hook_name, $current_controller, $current_id, $caching_settings)
    {
        $cache_key = $hook_name . '_' . $current_controller . '_' . (int) $this->isMobile() . '_'
            . (int) $this->context->shop->id . '_' . (int) $this->context->language->id . '_'
            . (int) $this->context->currency->id;
        if (!empty($caching_settings['check_ids'][$current_controller])) {
            $cache_key .= '_' . (int) $current_id;
        }
        if ($caching_settings['country']) {
            $cache_key .= '_' . (int) $this->context->country->id;
        }
        if ($caching_settings['group'] || !empty($caching_settings['check_ids']['group'])) {
            $cache_key .= '_' . (int) $this->context->customer->id_default_group;
        }

        return $cache_key;
    }

    public function displayCarouselsInHook(
        $hook_name,
        $id_product,
        $id_category,
        $current_id,
        $current_controller,
        $display_settings,
        $caching_settings
    ) {
        $cache_key = '';
        if ($cache_time = $caching_settings['time']) {
            $cache_key = $this->getHookCacheKey($hook_name, $current_controller, $current_id, $caching_settings);
            $cached_html = $this->cache('get', $cache_key, $cache_time);
            if ($cached_html !== false) {
                return $cached_html;
            }
        }
        if ($carousels = $this->getAllCarousels(
            'in_tabs',
            $hook_name,
            true,
            $id_product,
            $id_category,
            $current_id,
            $current_controller
        )) {
            // get all wrappers settings in one request
            $wrappers_settings = [];
            $wrapper_settings_data = $this->db->executeS('
                SELECT * FROM ' . _DB_PREFIX_ . 'ec_wrapper
                WHERE id_wrapper IN (' . $this->sqlIDs(array_keys($carousels)) . ')
            ');
            foreach ($wrapper_settings_data as $s) {
                $wrappers_settings[$s['id_wrapper']] = json_decode($s['settings'], true);
            }
            $smarty_array = [
                'carousels_in_hook' => $carousels,
                'wrappers_settings' => $wrappers_settings,
                'hook_name' => $hook_name,
                'display_settings' => $display_settings,
                'image_sizes' => $this->image_sizes,
                'carousel_tpl' => $this->getTemplatePath('carousel.tpl'),
                'currency_iso_code' => $this->context->currency->iso_code,
                'static_token' => Tools::getToken(false),
                'is_16' => $this->is_16,
            ];
            if ($this->is_16) {
                $smarty_array['add_to_cart_url'] = $this->context->link->getPageLink('cart', true, null, 'add=1');
            } elseif (empty($this->context->smarty->tpl_vars['urls'])) {
                $smarty_array['urls'] = [ // simplified version of $this->context->controller->getTemplateVarUrls()
                    'pages' => ['cart' => $this->context->link->getPageLink('cart', true)],
                ];
                $smarty_array['page'] = ['page_name' => 'category']; // may be used in external templates
            }
            $this->context->smarty->assign($smarty_array);
            $html = $this->display(__FILE__, 'views/templates/hook/layout.tpl');
        } else {
            $html = '';
        }
        if ($cache_key) {
            $this->cache('save', $cache_key, $html);
        }

        return $html;
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

    public function getCurrentProductIds($current_controller)
    {
        $ids = [];
        $cart_controllers = ['cart' => 1, 'order' => 1, 'orderopc' => 1];
        if ($id_product = Tools::getValue('id_product')) {
            $ids[$id_product] = $id_product;
        } elseif (isset($cart_controllers[$current_controller])) {
            foreach ($this->context->cart->getProducts(false, false, null, false) as $p) {
                $ids[$p['id_product']] = $p['id_product'];
            }
        }

        return $ids;
    }

    public function displayNativeHook($hook_name)
    {
        $current_controller = $this->getFullControllerName();
        $current_id = Tools::getValue('id_' . $current_controller);
        $hook_settings = $this->db->getRow('
            SELECT * FROM ' . _DB_PREFIX_ . 'ec_hook_settings
            WHERE hook_name = \'' . pSQL($hook_name) . '\'
            AND id_shop = ' . (int) $this->context->shop->id . '
        ');
        if ($hook_settings) {
            if (!empty($hook_settings['exc_type'])) {
                $type = $hook_settings['exc_type'];
                $controllers = array_flip(explode(',', $hook_settings['exc_controllers']));
                if (($type == 1 && isset($controllers[$current_controller]))
                    || ($type == 2 && !isset($controllers[$current_controller]))) {
                    return;
                }
            }
            $display_settings = json_decode($hook_settings['display'], true)
                ?: $this->getHookDisplaySettings('');
            $caching_settings = json_decode($hook_settings['caching'], true)
                ?: $this->cachingSettings('getDefault');
        } else {
            $display_settings = $this->getHookDisplaySettings('');
            $caching_settings = $this->cachingSettings('getDefault');
        }
        $id_product = $this->getCurrentProductIds($current_controller);
        $id_category = Tools::getValue('id_category');
        if (empty($display_settings['instant_load']) && !Tools::getValue('ajax')) {
            $params = [
                'ajax' => 1,
                'action' => 'getCarouselsInHook',
                'hook_name' => $hook_name,
                'id_product' => $this->formatIDs($id_product),
                'id_category' => $id_category,
                'current_id' => $current_id,
                'current_controller' => $current_controller,
            ];
            if ($current_controller == 'product') {
                $params['ec_last_visited'] = $this->lastVisitedCategory('get');
            }
            $this->context->smarty->assign(['ec_dynamic_ajax_path' => $this->getAjaxPath('ajax', $params)]);
            $ret = $this->display(__FILE__, 'views/templates/hook/dynamic.tpl');
        } else {
            $ret = $this->displayCarouselsInHook(
                $hook_name,
                $id_product,
                $id_category,
                $current_id,
                $current_controller,
                $display_settings,
                $caching_settings
            );
        }

        return $ret;
    }

    public function getAjaxPath($controller_name = 'ajax', $params = [])
    {
        $ssl = !empty($this->context->controller->ssl);
        $params['token'] = $this->getAjaxToken();

        return $this->context->link->getModuleLink($this->name, $controller_name, $params, $ssl);
    }

    public function getAjaxToken()
    {
        return Tools::encrypt($this->name);
    }

    public function hookDisplayHome()
    {
        return $this->displayNativeHook('displayHome');
    }

    public function hookDisplayTop()
    {
        return $this->displayNativeHook('displayTop');
    }

    public function hookDisplayTopColumn()
    {
        return $this->displayNativeHook('displayTopColumn');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->displayNativeHook('displayLeftColumn');
    }

    public function hookDisplayLeftColumnProduct()
    {
        return $this->displayNativeHook('displayLeftColumnProduct');
    }

    public function hookDisplayRightColumn()
    {
        return $this->displayNativeHook('displayRightColumn');
    }

    public function hookDisplayRightColumnProduct()
    {
        return $this->displayNativeHook('displayRightColumnProduct');
    }

    public function hookDisplayProductButtons()
    {
        return $this->displayNativeHook('displayProductButtons');
    }

    public function hookDisplayProductAdditionalInfo()
    {
        return $this->displayNativeHook('displayProductAdditionalInfo');
    }

    public function hookDisplayAfterProductThumbs()
    {
        return $this->displayNativeHook('displayAfterProductThumbs');
    }

    public function hookDisplayFooterProduct()
    {
        return $this->displayNativeHook('displayFooterProduct');
    }

    public function hookDisplayFooter()
    {
        return $this->displayNativeHook('displayFooter');
    }

    public function hookDisplayFooterAfter()
    {
        return $this->displayNativeHook('displayFooterAfter');
    }

    public function hookDisplayFooterBefore()
    {
        return $this->displayNativeHook('displayFooterBefore');
    }

    public function hookDisplayShoppingCart()
    {
        return $this->displayNativeHook('displayShoppingCart');
    }

    public function hookDisplayShoppingCartFooter()
    {
        return $this->displayNativeHook('displayShoppingCartFooter');
    }

    public function hookDisplayOrderConfirmation2()
    {
        return $this->displayNativeHook('displayOrderConfirmation2');
    }

    public function hookDisplayCrossSellingShoppingCart()
    {
        return $this->displayNativeHook('displayCrossSellingShoppingCart');
    }

    public function hookDisplaySearch()
    {
        return $this->displayNativeHook('displaySearch'); // displayed in /not-found.tpl
    }

    public function hookDisplayNavFullWidth()
    {
        return $this->displayNativeHook('displayNavFullWidth');
    }

    public function hookDisplayNav()
    {
        return $this->displayNativeHook('displayNav');
    }

    public function hookDisplayNav1()
    {
        return $this->displayNativeHook('displayNav1');
    }

    public function hookDisplayNav2()
    {
        return $this->displayNativeHook('displayNav2');
    }

    public function hookDisplayReassurance()
    {
        return $this->displayNativeHook('displayReassurance');
    }

    public function hookDisplayNotFound()
    {
        return $this->displayNativeHook('displayNotFound');
    }

    public function hookDisplayEasyCarousel1()
    {
        return $this->displayNativeHook('displayEasyCarousel1');
    }

    public function hookDisplayEasyCarousel2()
    {
        return $this->displayNativeHook('displayEasyCarousel2');
    }

    public function hookDisplayEasyCarousel3()
    {
        return $this->displayNativeHook('displayEasyCarousel3');
    }

    public function hookDisplayEasyCarousel4()
    {
        return $this->displayNativeHook('displayEasyCarousel4');
    }

    public function hookDisplayEasyCarousel5()
    {
        return $this->displayNativeHook('displayEasyCarousel5');
    }

    public function shopAssoc($type = 'where', $alias = '')
    {
        if (!isset($this->memo['shop_assoc'][$alias])) {
            $prefix = $alias ? '`' . bqSQL($alias) . '`.' : '';
            $id_shop = $this->context->shop->id;
            $shop_ids_ = $this->sqlIDs([$id_shop => $id_shop] + $this->shopIDs()); // current shop first
            $this->memo['shop_assoc'][$alias] = [
                'where' => $prefix . '`id_shop` IN (' . $shop_ids_ . ')',
                'order_by' => 'FIELD(' . $prefix . '`id_shop`, ' . $shop_ids_ . ')',
            ];
        }

        return isset($this->memo['shop_assoc'][$alias][$type]) ? $this->memo['shop_assoc'][$alias][$type] : '';
    }

    public function getOldestDate($days_back)
    {
        return date('Y-m-d H:i:s', strtotime('-' . (int) $days_back . ' days'));
    }

    public function sqlIsNew($alias = 'product_shop')
    {
        $nb_days_new = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        return '`' . bqSQL($alias) . '`.`date_add` > \'' . pSQL($this->getOldestDate($nb_days_new)) . '\'';
    }

    public function queryAddSalesData(&$query, $nb_days, $inner_join = false)
    {
        $join = $inner_join ? 'innerJoin' : 'leftJoin';
        if ($nb_days) {
            $query->select('SUM(od.product_quantity) AS sales_num');
            $query->$join('order_detail', 'od', 'od.product_id = p.id_product');
            $query->$join('orders', 'o', 'o.id_order = od.id_order
                AND o.date_add > \'' . pSQL($this->getOldestDate($nb_days)) . '\'
                AND o.id_shop = product_shop.id_shop AND o.valid = 1
            ');
            $query->groupBy('p.id_product');
        } else {
            $query->select('ps.quantity AS sales_num');
            $query->$join('product_sale', 'ps', 'ps.id_product = p.id_product');
        }
    }

    public function querySetOrder(&$query, $order_by, $default_value = 'product_shop.date_add DESC')
    {
        $queries = [
            'date_add' => 'product_shop.date_add DESC',
            'date_upd' => 'product_shop.date_upd DESC',
            'name' => 'pl.name ASC',
            'sales' => 'sales_num DESC',
            'random' => 'RAND()',
        ];
        $order_by = isset($queries[$order_by]) ? $queries[$order_by] : $default_value;
        $query->orderBy($order_by . ', product_shop.id_product DESC');
    }

    public function getProductFeatures($id_product, $f_group_ids, $implode = false)
    {
        $f_group_ids_ = $this->sqlIDs($f_group_ids);

        return $this->formatIDs(array_column($this->db->executeS('
            SELECT id_feature_value as id FROM ' . _DB_PREFIX_ . 'feature_product
            WHERE id_product = ' . (int) $id_product
            . ($f_group_ids_ ? ' AND id_feature IN (' . $f_group_ids_ . ')' : '') . '
        '), 'id', 'id'), $implode);
    }

    public function getProductTags($id_product, $id_lang, $implode = false)
    {
        return $this->formatIDs(array_column($this->db->executeS('
            SELECT id_tag as id FROM ' . _DB_PREFIX_ . 'product_tag
            WHERE id_product = ' . (int) $id_product . ' AND id_lang = ' . (int) $id_lang . '
        '), 'id', 'id'), $implode);
    }

    public function getMatchingManufacturers($product_ids, $implode = false)
    {
        $ids = [];
        if ($this->getFullControllerName() == 'product') {
            $ids = [$this->context->controller->getProduct()->id_manufacturer];
        } elseif ($product_ids_ = $this->sqlIDs($product_ids)) {
            $ids = array_column($this->db->executeS('
                SELECT DISTINCT(id_manufacturer) as id FROM ' . _DB_PREFIX_ . 'product
                WHERE id_product IN (' . $product_ids_ . ')
            '), 'id', 'id');
        }

        return $this->formatIDs($ids, $implode);
    }

    public function getMatchingCategories($product_ids, $id_shop, $check_last_visited, $implode = false)
    {
        $ids = [];
        if ($product_ids_ = $this->sqlIDs($product_ids)) {
            if ($check_last_visited && $id_cat = $this->lastVisitedCategory('get')) {
                $ids = [$id_cat => $id_cat];
            } else {
                $ids = array_column($this->db->executeS('
                    SELECT id_category_default as id FROM ' . _DB_PREFIX_ . 'product_shop
                    WHERE id_product IN (' . $product_ids_ . ') AND id_shop = ' . (int) $id_shop . '
                '), 'id', 'id');
            }
        }

        return $this->formatIDs($ids, $implode);
    }

    public function getMatchingProducts($all_matches, $available_min, $settings_min, $limit)
    {
        $sorted_matches = $matching_products = [];
        foreach ($all_matches as $p) {
            $sorted_matches[$p['id_product']][$p['id_match']] = $p['id_match'];
        }
        $min_matches = $settings_min && $settings_min < $available_min ? $settings_min : $available_min;
        foreach ($sorted_matches as $id => $matching_values) {
            if (count($matching_values) >= $min_matches) {
                $matching_products[$id] = ['id_product' => $id]; // same format as in other carousels
            }
            if (count($matching_products) >= $limit) {
                break;
            }
        }

        return $matching_products;
    }

    public function getCarouselProducts($type, $settings, $current_category, $p_ids, $details = true)
    {
        $products = [];
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $order_by = !empty($settings['php']['order_by']) ? $settings['php']['order_by'] : 'default';
        $default_order_by = 'product_shop.date_add DESC';
        $query = new DbQuery();
        $query->select('DISTINCT(p.id_product)');
        $query->from('product', 'p');
        $query->join(Shop::addSqlAssociation('product', 'p'));
        $query->where('product_shop.active = 1 AND product_shop.visibility IN ("both", "catalog")');
        if ($current_category && $settings['php']['consider_cat']) {
            $query->innerJoin('category_product', 'cat_p', 'cat_p.id_product = p.id_product
                AND cat_p.id_category = ' . (int) $current_category);
        }
        if ($order_by == 'name') {
            $query->leftJoin('product_lang', 'pl', 'pl.id_product = p.id_product
                AND pl.id_lang = ' . (int) $id_lang . ' AND pl.id_shop = product_shop.id_shop');
        } elseif ($order_by == 'sales' && $type != 'bestsellers' && $type != 'boughttogether') {
            $this->queryAddSalesData($query, $settings['php']['sales_days']);
        }
        if (!empty($settings['php']['oos'])) {
            $allow_ordering_oos = '-1';
            if ($settings['php']['oos'] == 2) {
                $allow_ordering_oos = '1' . (Configuration::get('PS_ORDER_OUT_OF_STOCK') ? ',2' : '');
            }
            $on = 'sa.id_product = p.id_product AND sa.id_product_attribute = 0';
            $on .= ' AND (sa.quantity > 0 OR sa.out_of_stock IN (' . $allow_ordering_oos . '))';
            $on .= StockAvailable::addSqlShopRestriction(null, null, 'sa');
            $query->innerJoin('stock_available', 'sa', $on);
        }
        if ($f_cat_ids_ = $this->sqlIDs($settings['php']['f_cat'])) {
            $query->innerJoin('category_product', 'f_cat', 'f_cat.id_product = p.id_product
                AND f_cat.id_category IN (' . $f_cat_ids_ . ')');
        }
        if ($type != 'bymanufacturer' && $f_man_ids_ = $this->sqlIDs($settings['php']['f_man'])) {
            $query->where('p.id_manufacturer IN (' . $f_man_ids_ . ')');
        }
        switch ($type) {
            case 'newproducts':
                $query->where($this->sqlIsNew());
                break;
            case 'featuredproducts':
                $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product
                    AND cp.id_category = ' . (int) $this->context->shop->getCategory());
                $default_order_by = 'cp.position ASC';
                break;
            case 'catproducts':
                if ($cat_ids_ = $this->sqlIDs($settings['special']['cat_ids'])) {
                    $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product
                        AND cp.id_category IN (' . $cat_ids_ . ')');
                    $default_order_by = 'cp.position ASC';
                } else {
                    $query = false;
                }
                break;
            case 'pricesdrop':
                $today = date('Y-m-d H:i:s');
                $query->innerJoin('specific_price', 'sp', 'sp.id_product = p.id_product
                    AND sp.id_customer IN (0,' . (int) $this->context->customer->id . ')
                    AND sp.id_group IN (0,' . $this->customerGroups(true) . ')
                    AND sp.id_shop IN (0,' . (int) $id_shop . ')
                    AND (sp.from = "0000-00-00 00:00:00" OR sp.from <= "' . pSQL($today) . '")
                    AND (sp.to = "0000-00-00 00:00:00" OR sp.to >= "' . pSQL($today) . '")
                    AND sp.reduction > 0');
                break;
            case 'bestsellers':
                $this->queryAddSalesData($query, $settings['php']['sales_days'], true);
                $default_order_by = 'sales_num DESC';
                break;
            case 'viewedproducts':
            case 'products':
                $ids = [];
                if ($type == 'viewedproducts' && !empty($this->context->cookie->ec_viewed)) {
                    $ids = $this->context->cookie->ec_viewed;
                } elseif ($type == 'products' && !empty($settings['special']['product_ids'])) {
                    $ids = $settings['special']['product_ids'];
                }
                if ($ids_ = $this->sqlIDS($ids)) {
                    $query->where('p.id_product IN (' . $ids_ . ')');
                    if ($p_ids_ = $this->sqlIDs($p_ids)) {
                        $query->where('p.id_product NOT IN (' . $p_ids_ . ')');
                    }
                    $default_order_by = 'FIELD(product_shop.id_product, ' . $ids_ . ')';
                } else {
                    $query = false;
                }
                break;
            case 'bymanufacturer':
                if ($m_ids_ = $this->sqlIDs($settings['special']['id_manufacturer'])) {
                    $query->where('p.id_manufacturer IN (' . $m_ids_ . ')');
                } else {
                    $query = false;
                }
                break;
            case 'bysupplier':
            case 'bysupplier_':
                if ($s_ids_ = $this->sqlIDs($settings['special']['id_supplier'])) {
                    if ($type == 'bysupplier_') {
                        $query->where('p.id_supplier IN (' . $s_ids_ . ')');
                    } else {
                        $query->innerJoin('product_supplier', 'psup', 'psup.id_product = p.id_product
                            AND psup.id_product_attribute = 0 AND psup.id_supplier IN (' . $s_ids_ . ')');
                    }
                } else {
                    $query = false;
                }
                break;
            case 'bytag':
                if (!empty($settings['special']['tags'])) {
                    $tags = array_map('trim', explode(',', $settings['special']['tags']));
                    $query->innerJoin('product_tag', 'ptag', 'ptag.id_product = p.id_product');
                    $query->innerJoin('tag', 'tag', 'tag.id_tag = ptag.id_tag
                        AND tag.name IN(\'' . implode('\', \'', array_map('pSQL', $tags)) . '\')');
                } else {
                    $query = false;
                }
                break;
            case 'accessories':
                if ($p_ids_ = $this->sqlIDs($p_ids)) {
                    $query->innerJoin('accessory', 'a', 'a.id_product_2 = p.id_product
                        AND a.id_product_1 IN (' . $p_ids_ . ') AND a.id_product_2 NOT IN (' . $p_ids_ . ')');
                    $default_order_by = '1';
                } else {
                    $query = false;
                }
                break;
            case 'boughttogether':
                if ($p_ids_ = $this->sqlIDs($p_ids)) {
                    $subquery = new DbQuery();
                    $subquery->select('od_sub.id_order')->from('order_detail', 'od_sub');
                    $subquery->innerJoin('orders', 'o', 'o.id_order = od_sub.id_order
                        AND o.id_shop = ' . (int) $id_shop . ' AND o.valid = 1');
                    $subquery->where('od_sub.product_id IN (' . $p_ids_ . ')');
                    if (!empty($settings['php']['sales_days'])) {
                        $oldest_date = $this->getOldestDate($settings['php']['sales_days']);
                        $subquery->where('o.date_add > \'' . pSQL($oldest_date) . '\'');
                    }
                    $query->innerJoin('order_detail', 'od', 'od.product_id = p.id_product
                        AND od.product_id NOT IN (' . $p_ids_ . ')
                        AND od.id_order IN (' . $subquery->build() . ')');
                    $query->groupBy('p.id_product');
                    if ($order_by == 'sales') {
                        $query->select('SUM(od.product_quantity) AS sales_num');
                    } else {
                        $default_order_by = 'COUNT(DISTINCT od.id_order) DESC'; // how many times bought together
                    }
                } else {
                    $query = false;
                }
                break;
            case 'samecategory':
                if ($settings['special']['same_man'] && $m_ids = $this->getMatchingManufacturers($p_ids)) {
                    $query->where('p.id_manufacturer IN (' . $this->sqlIDs($m_ids) . ')');
                }
                if ($c_ids = $this->getMatchingCategories($p_ids, $id_shop, $settings['special']['last_visited'])) {
                    $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product
                        AND cp.id_category IN (' . $this->sqlIDs($c_ids) . ')');
                    $query->where('p.id_product NOT IN (' . $this->sqlIDs($p_ids) . ')');
                    $default_order_by = 'cp.position ASC';
                } else {
                    $query = false;
                }
                break;
            case 'samemanufacturer':
                if ($m_ids = $this->getMatchingManufacturers($p_ids)) {
                    $query->where('p.id_manufacturer IN (' . $this->sqlIDs($m_ids) . ')');
                } else {
                    $query = false;
                }
                break;
            case 'samefeature':
            case 'sametag':
                $limit = $settings['carousel']['total'];
                foreach ($p_ids as $id_product) { // define min matches for each product individually
                    if ($type == 'samefeature') {
                        $m_table = 'feature_product';
                        $m_column = 'id_feature_value';
                        $ids_to_match = $this->getProductFeatures($id_product, $settings['special']['id_feature']);
                    } else {
                        $m_table = 'product_tag';
                        $m_column = 'id_tag';
                        $ids_to_match = $this->getProductTags($id_product, $id_lang);
                    }
                    if ($ids_to_match) {
                        $m_query = clone $query;
                        $m_query->select('m_table.`' . bqSQL($m_column) . '` AS id_match');
                        $m_query->innerJoin($m_table, 'm_table', 'm_table.id_product = p.id_product
                            AND m_table.`' . bqSQL($m_column) . '` IN (' . $this->sqlIDs($ids_to_match) . ')
                            AND m_table.id_product NOT IN (' . $this->sqlIDs($p_ids) . ')');
                        $this->querySetOrder($m_query, $order_by, $default_order_by);
                        $all_matches = $this->db->executeS($m_query);
                        $available_min = count($ids_to_match);
                        $settings_min = $settings['special']['min_matches'];
                        // each matching product will have key as id_product, so it is OK to use +=
                        $products += $this->getMatchingProducts($all_matches, $available_min, $settings_min, $limit);
                    }
                    $limit -= count($products);
                    if ($limit < 1) {
                        break;
                    }
                }
                $query = false;
                break;
        }
        if ($query) {
            $this->querySetOrder($query, $order_by, $default_order_by);
            $query->limit((int) $settings['carousel']['total']);
            $products = $this->db->executeS($query);
        }
        $ids = array_column($products, 'id_product');

        return $details ? $this->getProductsInfos($ids, $id_lang, $id_shop, $settings['tpl']) : $ids;
    }

    public function getCarouselItems($type, $item_type, $settings, $current_category)
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $items = [];
        $default_order_by = 'name';
        $identifier = 'id_' . $item_type;
        $query = new DbQuery();
        if ($type == 'subcategories') {
            if ($parent_ids_ = $this->sqlIDs($settings['special']['parent_ids']) ?: (int) $current_category) {
                $query->where('main.`id_parent` IN (' . $parent_ids_ . ')');
                $default_order_by = 'category_shop.`position` ASC';
            } else {
                $query = false;
            }
        } elseif ($type == 'categories') {
            if ($cat_ids_ = $this->sqlIDs($settings['special']['cat_ids'])) {
                $query->where('main.`id_category` IN (' . $cat_ids_ . ')');
                $default_order_by = 'FIELD(main.`id_category`, ' . $cat_ids_ . ')';
            } else {
                $query = false;
            }
        }
        if ($query) {
            $query->select('DISTINCT(main.`' . bqSQL($identifier) . '`) AS id, `name`, `description`');
            $query->from($item_type, 'main');
            $query->join(Shop::addSqlAssociation($item_type, 'main'));
            $query->innerJoin(
                $item_type . '_lang',
                'lang',
                'lang.`' . bqSQL($identifier) . '` = main.`' . bqSQL($identifier) . '`'
            );
            $query->where('main.`active` = 1 AND lang.`id_lang` = ' . (int) $id_lang);
            if (!empty($settings['special'][$identifier])
                    && $filter_by_ids_ = $this->sqlIDs($settings['special'][$identifier])) {
                $query->where('main.`' . bqSQL($identifier) . '` IN (' . $filter_by_ids_ . ')');
            }
            if ($item_type == 'category') {
                $query->select('`link_rewrite`');
                $query->innerJoin('category_group', 'cg', 'cg.`id_category` = main.`id_category`');
                $query->where('cg.`id_group` IN (' . $this->customerGroups(true) . ')');
                $query->where('main.`id_parent` > 0 AND main.`id_category` <> ' . (int) $this->context->shop->getCategory());
                $query->where('lang.`id_shop` = ' . (int) $id_shop);
            }
            $order_by = $settings['php']['order_by'];
            $accepted_order_by = ['name' => 'name', 'id' => 'id', 'random' => 'RAND()'];
            $query->orderBy(isset($accepted_order_by[$order_by]) ? $accepted_order_by[$order_by] : $default_order_by);
            $query->limit((int) $settings['carousel']['total']);
            $items = $this->db->ExecuteS($query);
        }

        return $items;
    }

    public function getStructuredCarouselItems($type, $item_type, $settings, $current_category, $p_ids)
    {
        if (empty($settings['carousel']['total'])) {
            return [];
        }
        if ($item_type == 'product') {
            $items = $this->getCarouselProducts($type, $settings, $current_category, $p_ids);
        } else {
            $items = $this->getCarouselItems($type, $item_type, $settings, $current_category);
            $m_key = $item_type == 'category' ? 'c_matches' : 'matches';
            $show_matches = !empty($settings['tpl'][$m_key]) ? $settings['tpl'][$m_key] : 0;
            $consider_subcategories = $show_matches == 2;
        }

        if ($settings['carousel']['type'] != 1) {
            $settings['carousel']['r'] = 1;
        }

        $structured_items = [];
        $current_row = 1;
        $current_col = 0;
        foreach ($items as $item) {
            if ($current_col >= ceil(count($items) / $settings['carousel']['r'])) {
                ++$current_row;
                $current_col = 0;
            }
            ++$current_col;
            if ($item_type != 'product') {
                $item['img_src'] = $this->getImageUrl($item_type, $item['id'], $settings['tpl']['image_type']);
                $alias = isset($item['link_rewrite']) ? $item['link_rewrite'] : Tools::str2url($item['name']);
                $item['url'] = $this->getItemUrl($item_type, $item['id'], $alias);
                if ($show_matches) {
                    $item['matches'] = $this->getMatchesNum($item_type, $item['id'], $consider_subcategories);
                }
            }
            $structured_items[$current_col][$current_row] = $item;
        }

        return $structured_items;
    }

    public function getCarouselItemType($type)
    {
        $other_types = [
            'manufacturers' => 'manufacturer',
            'suppliers' => 'supplier',
            'categories' => 'category',
            'subcategories' => 'category',
        ];

        return isset($other_types[$type]) ? $other_types[$type] : 'product';
    }

    public function getFullExternalTplPath($tpl_path)
    {
        $external_tpl_path = _PS_THEME_DIR_ . $tpl_path;

        return is_file($external_tpl_path) ? $external_tpl_path : '';
    }

    public function getCarouselItemTpl($item_type, $settings)
    {
        if ($item_type == 'product') {
            if (empty($settings['external_tpl'])
                || !$tpl_path = $this->getFullExternalTplPath($settings['external_tpl_path'])) {
                $tpl_path = $this->getTemplatePath('product-item' . ($this->is_16 ? '-16' : '') . '.tpl');
            }
        } else {
            $tpl_path = $this->getTemplatePath('item.tpl');
        }

        return $tpl_path;
    }

    public function getImageUrl($resource_type, $id, $image_type)
    {
        $cache_key = $resource_type . '-' . $id . '-' . $image_type;
        if (!isset($this->img_src_cache[$cache_key])) {
            $dirs = [
                'supplier' => [_PS_SUPP_IMG_DIR_, _THEME_SUP_DIR_],
                'manufacturer' => [_PS_MANU_IMG_DIR_, _THEME_MANU_DIR_],
                'category' => [_PS_CAT_IMG_DIR_, _THEME_CAT_DIR_],
            ];
            if ($image_type == '--' || !isset($dirs[$resource_type])) {
                $this->img_src_cache[$cache_key] = false;
            } else {
                $basename = $id . ($image_type != 'original' ? '-' . $image_type : '') . '.jpg';
                if (!file_exists($dirs[$resource_type][0] . $basename)) {
                    $type_ext = $image_type != 'original' ? '-default-' . $image_type : '';
                    $basename = $this->context->language->iso_code . $type_ext . '.jpg';
                }
                $this->img_src_cache[$cache_key] = $dirs[$resource_type][1] . $basename;
            }
        }

        return $this->img_src_cache[$cache_key];
    }

    public function getItemUrl($item_type, $id, $alias = null)
    {
        $url = '#';
        $method = 'get' . Tools::ucfirst($item_type) . 'Link';
        if (is_callable([$this->context->link, $method])) {
            $url = $this->context->link->$method($id, $alias);
        }

        return $url;
    }

    public function getMatchesNum($item_type, $id, $all = false)
    {
        $num = 0;
        $sql_data = [
            'category' => ['category_product', ''],
            'manufacturer' => ['product', ''],
            'supplier' => ['product_supplier', ' AND main.`id_product_attribute` = 0'],
        ];
        $ids_ = $item_type == 'category' && $all ? $this->getSubcategoryIDs($id) : (int) $id;
        if ($ids_ && isset($sql_data[$item_type])) {
            $num = $this->db->getValue('
                SELECT COUNT(DISTINCT(main.id_product))
                FROM `' . _DB_PREFIX_ . bqSQL($sql_data[$item_type][0]) . '` main
                ' . Shop::addSqlAssociation('product', 'main') . '
                WHERE product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")
                AND main.`id_' . bqSQL($item_type) . '` IN (' . $ids_ . ')' . $sql_data[$item_type][1] . '
            ');
        }

        return $num;
    }

    public function getSubcategoryIDs($id_parent, $include_self = true, $implode = true)
    {
        $ids = [];
        if ($include_self) {
            $ids[$id_parent] = $id_parent;
        }
        $parent = $this->db->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'category WHERE id_category = ' . (int) $id_parent);
        $subcategories = $this->db->executeS('
            SELECT DISTINCT(c.id_category) AS id FROM ' . _DB_PREFIX_ . 'category c
            ' . Shop::addSqlAssociation('category', 'c') . ' WHERE c.active = 1
            AND c.nleft > ' . (int) $parent['nleft'] . ' AND c.nright < ' . (int) $parent['nright'] . '
        ');
        foreach ($subcategories as $row) {
            $ids[$row['id']] = $row['id'];
        }

        return $this->formatIDs($ids, $implode);
    }

    public function getSecondProductImages($ids)
    {
        return array_column($this->db->executeS('
            SELECT i.id_product, i.id_image
            FROM ' . _DB_PREFIX_ . 'image i ' . Shop::addSqlAssociation('image', 'i') . '
            WHERE i.id_product IN (' . $this->sqlIDs($ids) . ') AND i.cover IS NULL
            GROUP BY i.id_product
        '), 'id_image', 'id_product');
    }

    public function att($action, $params = [])
    {
        $ret = [];
        $params['id_lang'] = isset($params['id_lang']) ? $params['id_lang'] : $this->context->language->id;
        $params['id_shop'] = isset($params['id_shop']) ? $params['id_shop'] : $this->context->shop->id;
        switch ($action) {
            case 'stockQuery':
                $allow_oos = (bool) Configuration::get('PS_ORDER_OUT_OF_STOCK');
                $ret = 'INNER JOIN ' . _DB_PREFIX_ . 'stock_available sa
                    ON sa.id_product_attribute = pas.id_product_attribute AND sa.id_product = pas.id_product
                    AND sa.id_shop = pas.id_shop AND sa.id_product_attribute > 0
                    AND (sa.quantity > 0 OR sa.out_of_stock IN (' . ($allow_oos ? '1,2' : '1') . '))';
                break;
            case 'getAvailable':
                $c_data = $this->db->executeS('
                    SELECT
                        pas.id_product,
                        pac.id_attribute AS id_att,
                        al.name AS att_name,
                        agl.public_name AS group_name,
                        pac.id_product_attribute AS id_comb
                    FROM ' . _DB_PREFIX_ . 'product_attribute_shop pas
                    ' . $this->att('stockQuery') . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac
                        ON pac.id_product_attribute = pas.id_product_attribute
                    LEFT JOIN ' . _DB_PREFIX_ . 'attribute a
                        ON a.id_attribute = pac.id_attribute
                    LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al
                        ON al.id_attribute = a.id_attribute AND al.id_lang = ' . (int) $params['id_lang'] . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl
                        ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = ' . (int) $params['id_lang'] . '
                    WHERE pas.id_shop = ' . (int) $params['id_shop'] . '
                        AND pas.id_product IN (' . $this->sqlIDs(array_keys($params['ids'])) . ')
                    ORDER BY a.position ASC, a.id_attribute_group ASC
                ');
                foreach ($c_data as $row) {
                    if ($row['id_comb'] == $params['ids'][$row['id_product']]) {
                        $ret[$row['id_product']][$row['group_name']][0] = $row['id_att']; // selected att ID
                    }
                    $ret[$row['id_product']][$row['group_name']][$row['id_att']] = $row['att_name'];
                }
                break;
            case 'getCombinationDataByAtts':
                asort($params['att_ids']);
                $data = $this->db->getRow('
                    SELECT pas.id_product_attribute AS id_comb, pas.id_product,
                    (GROUP_CONCAT(pac.id_attribute) = \'' . $this->sqlIDs($params['att_ids']) . '\') AS exact_match
                    FROM ' . _DB_PREFIX_ . 'product_attribute_shop pas
                    INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac
                        ON pac.id_product_attribute = pas.id_product_attribute
                    ' . $this->att('stockQuery') . '
                    WHERE pas.id_product = ' . (int) $params['id_product'] . ' AND pas.id_shop = ' . (int) $params['id_shop'] . '
                    GROUP BY pas.id_product_attribute
                    ORDER BY exact_match DESC
                ');
                if ($data['exact_match']) {
                    $ret = [
                        'id_comb' => $data['id_comb'],
                        'img_src' => $this->att('getImgSrc', $data),
                    ];
                }
                break;
            case 'getImgSrc':
                if ($ret = Product::getCombinationImageById($params['id_comb'], $params['id_lang'])) {
                    $ret = $this->context->link->getImageLink(
                        $ret['id_image'],
                        $params['id_product'] . '-' . $ret['id_image'],
                        $this->getHomeImgType()
                    );
                }
                break;
            case 'get':
                $ret = array_column($this->db->executeS('
                    SELECT name, id_attribute FROM ' . _DB_PREFIX_ . 'attribute_lang
                    WHERE id_lang = ' . (int) $params['id_lang'] . '
                '), 'name', 'id_attribute');
                break;
            case 'getGroups':
                $ret = array_column($this->db->executeS('
                    SELECT public_name, id_attribute_group FROM ' . _DB_PREFIX_ . 'attribute_group_lang
                    WHERE id_lang = ' . (int) $params['id_lang'] . '
                '), 'public_name', 'id_attribute_group');
                break;
        }

        return $ret;
    }

    public function setAvailableCombinations(&$products_data, $id_shop)
    {
        $product_ids = array_column($products_data, 'id_product');
        $c_data = $this->db->executeS('
            SELECT pas.id_product, pas.id_product_attribute, i.id_image
            FROM ' . _DB_PREFIX_ . 'product_attribute_shop pas
            ' . $this->att('stockQuery') . '
            LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_image pai
                ON pai.id_product_attribute = pas.id_product_attribute
            LEFT JOIN ' . _DB_PREFIX_ . 'image i
                ON i.id_image = pai.id_image
            WHERE pas.id_shop = ' . (int) $id_shop . ' AND pas.id_product IN (' . $this->sqlIDs($product_ids) . ')
            ORDER BY pas.default_on DESC, i.cover DESC, i.position ASC
        ');
        $upd_c_data = [];
        foreach ($c_data as $row) {
            if (!isset($upd_c_data[$row['id_product']])) {
                $row['cover_image_id'] = $row['id_image'];
                $upd_c_data[$row['id_product']] = $row;
            }
        }
        foreach ($products_data as &$pd) {
            if (isset($upd_c_data[$pd['id_product']])) {
                foreach ($upd_c_data[$pd['id_product']] as $key => $value) {
                    if ($value) {
                        $pd[$key] = $value;
                    }
                }
            }
        }
    }

    public function getProductsInfos($ids, $id_lang, $id_shop, $settings)
    {
        if (!$ids_ = $this->sqlIDs($ids)) {
            return [];
        }
        $show_cat = $settings['product_cat'];
        $show_man = $settings['product_man'];
        $products_infos = [];
        $products_data = $this->db->executeS('
            SELECT p.*, product_shop.*, pl.*, image.id_image, il.legend,
                pas.id_product_attribute, pas.minimal_quantity AS pa_minimal_quantity, '
                . ($show_cat ? 'cl.name AS cat_name, cl.link_rewrite as cat_link_rewrite, ' : '')
                . ($show_man ? 'm.name AS man_name, ' : '')
                . $this->sqlIsNew() . ' AS new
            FROM ' . _DB_PREFIX_ . 'product p
            ' . Shop::addSqlAssociation('product', 'p') . '
            INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
                ON (pl.id_product = p.id_product
                AND pl.id_shop = ' . (int) $id_shop . ' AND pl.id_lang = ' . (int) $id_lang . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas
                ON pas.id_product_attribute = product_shop.cache_default_attribute
                AND pas.id_shop = product_shop.id_shop'
            . ($show_cat ? '
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
                ON (cl.id_category = product_shop.id_category_default
                AND cl.id_shop = ' . (int) $id_shop . ' AND cl.id_lang = ' . (int) $id_lang . ')' : '')
            . ($show_man ? '
            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m
                ON m.id_manufacturer = p.id_manufacturer AND m.active = 1' : '') . '
            LEFT JOIN ' . _DB_PREFIX_ . 'image image
                ON (image.id_product = p.id_product AND image.cover = 1)
            LEFT JOIN ' . _DB_PREFIX_ . 'image_lang il
                ON (il.id_image = image.id_image AND il.id_lang = ' . (int) $id_lang . ')
            WHERE p.id_product IN (' . $ids_ . ')
        ');
        $second_images = $available_atts = [];
        $image_type = $settings['image_type'] != 'original' ? $settings['image_type'] : null;
        if ($settings['add_to_cart']) {
            $this->setAvailableCombinations($products_data, $id_shop);
            if ($settings['add_to_cart'] == 2) {
                $product_combinations = array_column($products_data, 'id_product_attribute', 'id_product');
                $available_atts = $this->att('getAvailable', ['ids' => $product_combinations]);
            }
        }
        if (!empty($settings['second_image'])) {
            $second_images = $this->getSecondProductImages($ids);
        }
        $positions = array_flip($ids);
        foreach ($products_data as $pd) {
            $id = $pd['id_product'];
            if (!empty($pd['pa_minimal_quantity'])) {
                $pd['minimal_quantity'] = $pd['pa_minimal_quantity'];
            }
            if (!empty($available_atts[$id])) {
                $pd['available_atts'] = $available_atts[$id];
            }
            // out_of_stock is required to avoid extra queries in getProductProperties
            $pd['out_of_stock'] = StockAvailable::outOfStock($id, $id_shop);
            $pd = Product::getProductProperties($id_lang, $pd);
            $link_rewrite = $pd['link_rewrite'];
            if (!$this->is_16) {
                $pd = $this->presentProduct($pd);
                if (!$image_type) {
                    $this->setOriginalCoverData($pd);
                }
            } else {
                $pd['img_src'] = $this->context->link->getImageLink($link_rewrite, $pd['id_image'], $image_type);
            }
            if ($settings['stock'] && $pd['available_for_order'] && !Configuration::get('PS_CATALOG_MODE')
                && Configuration::get('PS_STOCK_MANAGEMENT')) {
                $this->addAvailabilityMessageIfRequired($pd);
            }
            if (!empty($second_images[$id])) {
                $src = $this->context->link->getImageLink($link_rewrite, $second_images[$id], $image_type);
                $pd['second_img_src'] = $src;
            }
            if ($show_man && !empty($pd['id_manufacturer'])) {
                $alias = Tools::str2url($pd['man_name']);
                $pd['man_url'] = $this->getItemUrl('manufacturer', $pd['id_manufacturer'], $alias);
                if ($show_man != 1) {
                    $pd['man_img_src'] = $this->getImageUrl('manufacturer', $pd['id_manufacturer'], $show_man);
                }
            }
            if ($show_cat && !empty($pd['id_category_default'])) {
                $pd['cat_url'] = $this->getItemUrl('category', $pd['id_category_default'], $pd['cat_link_rewrite']);
            }
            $products_infos[$positions[$id]] = $pd;
        }
        ksort($products_infos);

        return $products_infos;
    }

    public function addAvailabilityMessageIfRequired(&$product)
    {
        if (empty($product['availability_message'])) {
            $txt = [
                'available_now' => $this->l('In stock'),
                'available_later' => $this->l('Pre-order'),
                'available_different' => $this->l('Available with different options'),
                'not_available' => $this->l('Out of stock'),
            ];
            if ($product['quantity'] > 0) {
                $status = 'available_now';
            } elseif ($product['allow_oosp']) {
                $status = 'available_later';
            } elseif (isset($product['quantity_all_versions']) && $product['quantity_all_versions'] > 0) {
                $status = 'available_different';
            } else {
                $status = 'not_available';
            }
            $product['availability_message'] = $txt[$status];
            if ($this->is_16) {
                $product['stock_status'] = $status;
            }
        }
    }

    public function presentProduct($product_data)
    {
        if (!isset($this->factory_presenter)) {
            $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
            $this->factory_presenter = $factory->getPresenter();
            $this->factory_settings = $factory->getPresentationSettings();
        }

        return $this->factory_presenter->present($this->factory_settings, $product_data, $this->context->language);
    }

    public function setOriginalCoverData(&$p_data)
    {
        $cover_src = $this->context->link->getImageLink($p_data['link_rewrite'], $p_data['cover']['id_image']);
        if (is_array($p_data)) {
            $p_data['cover']['bySize']['original']['url'] = $cover_src;
        } else { // LazyArray since 1.7.5
            $cover = $p_data['cover'];
            $cover['bySize']['original']['url'] = $cover_src;
            $p_data->offsetSet('cover', $cover, true);
        }
    }

    public function ajaxCallCarouselForm()
    {
        $id_carousel = (int) Tools::getValue('id_carousel');
        $hook_name = Tools::getValue('hook_name');
        $id_wrapper = Tools::getValue('id_wrapper');
        exit(json_encode(['html' => $this->renderCarouselForm($id_carousel, $hook_name, $id_wrapper)]));
    }

    public function ajaxCallSettingsForm()
    {
        $hook_name = Tools::getValue('hook_name');
        $settings_type = Tools::getValue('settings_type');
        $method = 'getHook' . Tools::ucfirst($settings_type) . 'Settings';
        if (!is_callable([$this, $method])) {
            $this->throwError('error');
        }
        $smarty_vars = [
            'settings' => $this->$method($hook_name),
            'settings_type' => $settings_type,
            'hook_name' => $hook_name,
        ];
        if ($settings_type == 'caching') {
            $smarty_vars['caching_info'] = $this->cache('getInfo', $hook_name . '_');
        }
        $this->context->smarty->assign($smarty_vars);
        $form_html = $this->display($this->local_path, 'views/templates/admin/hook-' . $settings_type . '-form.tpl');
        exit(json_encode(['form_html' => $form_html]));
    }

    public function ajaxClearHookCache()
    {
        $ret = [];
        if ($cleared = $this->cache('clear', Tools::getValue('hook_name') . '_')) {
            $ret['successText'] = $this->l('Cleared');
        }
        exit(json_encode($ret));
    }

    public function getWrapperFields($id_wrapper = false)
    {
        $fields = [
            'custom_class' => [
                'display_name' => $this->l('Wrapper class'),
                'value' => '',
                'type' => 'text',
                'validate' => 'isLabel',
            ],
        ];
        if ($id_wrapper) {
            $saved_data = $this->db->getValue('
                SELECT settings FROM ' . _DB_PREFIX_ . 'ec_wrapper
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

    public function ajaxSaveWrapperSettings()
    {
        $fields = $this->getWrapperFields();
        $form_data = Tools::getValue('form_data');
        parse_str($form_data, $form_data);
        $data_to_save = [];
        foreach ($fields as $name => $field) {
            if (isset($form_data[$name])) {
                $validate = $field['validate'];
                if (Validate::$validate($form_data[$name])) {
                    $field['value'] = $form_data[$name];
                } else {
                    $txt = sprintf($this->l('Incorrect value for "%s"'), $field['display_name']);
                    $this->throwError($txt);
                }
            }
            $data_to_save[$name] = $field['value'];
        }
        $id_wrapper = $form_data['id_wrapper'];
        $id_wrapper_new = false;
        if (!$id_wrapper) {
            $id_wrapper_new = $id_wrapper = $this->addWrapper();
            if ($ids_in_wrapper = Tools::getValue('ids_in_wrapper')) {
                $id_carousel_first = current($ids_in_wrapper);
                $this->updateCarouselWrapper($id_carousel_first, $id_wrapper_new, $ids_in_wrapper);
            }
        }
        $ret = [
            'saved' => $this->saveWrapperSettings($id_wrapper, $data_to_save),
            'id_wrapper_new' => $id_wrapper_new,
        ];
        exit(json_encode($ret));
    }

    public function saveWrapperSettings($id_wrapper, $settings)
    {
        if (!Validate::isString($settings)) {
            $settings = json_encode($settings);
        }
        $saved = $this->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'ec_wrapper (id_wrapper, settings)
            VALUES (' . (int) $id_wrapper . ', \'' . pSQL($settings) . '\')
            ON DUPLICATE KEY UPDATE settings = VALUES(settings)
        ');

        return $saved;
    }

    public function getHookExceptionsSettings($hook_name)
    {
        $exc_data = $this->db->executeS('
            SELECT exc_type, exc_controllers, id_shop
            FROM ' . _DB_PREFIX_ . 'ec_hook_settings
            WHERE hook_name = \'' . pSQL($hook_name) . '\' AND id_shop IN (' . $this->shopIDs(true) . ')
        ');
        $type = 0;
        $current_exceptions = [];
        foreach ($exc_data as $row) {
            if (!$type || $row['id_shop'] == $this->context->shop->id) {
                $type = $row['exc_type'];
            }
            if ($row['exc_controllers']) {
                $exceptions = explode(',', $row['exc_controllers']);
                $current_exceptions += array_combine($exceptions, $exceptions);
            }
        }
        $sorted_exceptions = [
            'core' => [
                'group_name' => $this->l('Core pages'),
                'values' => [],
            ],
            'modules' => [
                'group_name' => $this->l('Module pages'),
                'values' => [],
            ],
        ];
        $front_controllers = array_keys(Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_));
        $retro_compatibility = [
            'auth' => 'authentication',
            'compare' => 'productscomparison',
        ];
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
        $settings = [
            'type' => $type,
            'exceptions' => $sorted_exceptions,
        ];

        return $settings;
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

    public function getHookDisplaySettings($hook_name = '')
    {
        $settings = ['custom_class' => 'row', 'compact_tabs' => 1, 'instant_load' => 1];
        if ($hook_name) {
            $saved_settings = $this->db->getValue('
                SELECT display FROM ' . _DB_PREFIX_ . 'ec_hook_settings
                WHERE hook_name = \'' . pSQL($hook_name) . '\' AND id_shop = ' . (int) $this->context->shop->id . '
            ');
            if ($saved_settings = json_decode($saved_settings, true)) {
                foreach ($settings as $name => $val) {
                    $settings[$name] = isset($saved_settings[$name]) ? $saved_settings[$name] : $val;
                }
            }
        }

        return $settings;
    }

    public function getHookCachingSettings($hook_name = '')
    {
        return $this->cachingSettings('get', ['hook_name' => $hook_name]);
    }

    public function ajaxSaveHookSettings()
    {
        $hook_name = Tools::getValue('hook_name');
        $id_hook = Hook::getIdByName($hook_name);
        $settings_type = Tools::getValue('settings_type');
        $saved = false;
        if (in_array($settings_type, ['display', 'caching'])) {
            $saved = $this->saveHookSettings($hook_name, $settings_type, Tools::getValue('settings'));
        } elseif ($settings_type == 'exceptions') {
            $exc_type = Tools::getValue('exceptions_type');
            $exc_controllers = Tools::getValue('exceptions');
            $saved = $this->saveExceptions($hook_name, $exc_type, $exc_controllers, $this->shopIDs());
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

    public function saveHookSettings($hook_name, $settings_type, $settings)
    {
        if ($settings_type == 'caching' && isset($settings['adjust_required'])) {
            unset($settings['adjust_required']);
            $settings = $this->cachingSettings('adjust', ['hook_name' => $hook_name, 'settings' => $settings]);
        }
        $settings = !is_string($settings) ? json_encode($settings) : $settings;
        $rows = [];
        foreach ($this->shopIDs() as $id_shop) {
            $rows[] = '(\'' . pSQL($hook_name) . '\', ' . (int) $id_shop . ', \'' . pSQL($settings) . '\')';
        }

        return $rows && $this->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'ec_hook_settings
            (`hook_name`, `id_shop`, `' . bqSQL($settings_type) . '`) VALUES ' . implode(', ', $rows) . '
            ON DUPLICATE KEY UPDATE `' . bqSQL($settings_type) . '` = VALUES(`' . bqSQL($settings_type) . '`)
        ') && $this->cache('clear', $hook_name . '_');
    }

    public function saveExceptions($hook_name, $exc_type, $exc_controllers, $shop_ids)
    {
        $exc_controllers = is_array($exc_controllers) ? implode(',', $exc_controllers) : $exc_controllers;
        $rows = [];
        foreach ($shop_ids as $id_shop) {
            $rows[] = '(\'' . pSQL($hook_name) . '\', ' . (int) $id_shop . ', ' . (int) $exc_type
                . ', \'' . pSQL($exc_controllers) . '\')';
        }
        $saved = true;
        if ($rows) {
            $saved &= $this->db->execute('
                INSERT INTO ' . _DB_PREFIX_ . 'ec_hook_settings
                (hook_name, id_shop, exc_type, exc_controllers)
                VALUES ' . implode(', ', $rows) . '
                ON DUPLICATE KEY UPDATE
                exc_type = VALUES(exc_type),
                exc_controllers = VALUES(exc_controllers)
            ');
        }
        // make sure native exceptions are not used
        $saved &= $this->unregisterExceptions(Hook::getIdByName($hook_name), $shop_ids);

        return $saved;
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
                    $saved = $module->unregisterHook(Hook::getIdByName($hook_name));
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

    public function renderCarouselForm($id_carousel, $hook_name, $id_wrapper, $full = true)
    {
        $carousel_info = $this->db->getRow('
            SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels
            WHERE `id_carousel` = ' . (int) $id_carousel . ' AND ' . $this->shopAssoc('where') . '
            ORDER BY ' . $this->shopAssoc('order_by') . '
        ');
        if ($carousel_info) {
            $carousel_info['multilang'] = array_column($this->db->executeS('
                SELECT `data`, `id_lang` FROM ' . _DB_PREFIX_ . 'easycarousels_lang
                WHERE `id_carousel` = ' . (int) $id_carousel . ' AND ' . $this->shopAssoc('where') . '
                GROUP BY `id_lang`
                ORDER BY ' . $this->shopAssoc('order_by') . '
            '), 'data', 'id_lang');
            foreach ($carousel_info['multilang'] as $id_lang => $data) {
                $carousel_info['multilang'][$id_lang] = json_decode($data, true);
                if (!isset($carousel_info['name']) || $id_lang == $this->context->language->id) {
                    $carousel_info['name'] = $carousel_info['multilang'][$id_lang]['name'];
                }
            }
            $carousel_info['settings'] = json_decode($carousel_info['settings'], true);
            if ($exc_note = $this->getExceptionsNote($carousel_info['settings'])) {
                $carousel_info['exc_note'] = $exc_note;
            }
        } else {
            $carousel_info = [ // default carousel data
                'id_carousel' => (int) $id_carousel,
                'active' => 1,
                'type' => 'newproducts',
                'in_tabs' => (int) $this->db->getValue('
                    SELECT in_tabs FROM ' . _DB_PREFIX_ . 'easycarousels
                    WHERE id_wrapper = ' . (int) $id_wrapper . ' AND in_tabs = 1
                '),
                'hook_name' => $hook_name,
                'id_wrapper' => $id_wrapper,
                'settings' => [],
                'multilang' => [],
            ];
        }
        $fields = $this->getRequiredFields($this->prepareAdvancedOptions($carousel_info['settings']));
        $fields['exceptions'] = $this->getFields('exceptions');
        if ($carousel_info['hook_name'] == 'displayHome') {
            $fields['php']['consider_cat']['class'] = 'hidden';
            $fields['exceptions']['display']['selectors']['page'] = [
                '0' => $this->l('Only on homepage (hook displayHome)'),
            ];
        }
        $this->context->smarty->assign([
            'carousel' => $carousel_info,
            'multilang_fields' => $this->getCarouselMultilangFields($carousel_info['multilang']),
            'type_names' => $this->getTypeNames(),
            'fields' => $fields,
            'languages' => Language::getLanguages(false),
            'id_lang_current' => $this->context->language->id,
            'device_types' => $this->getDeviceTypes(),
            'multidevice_settings' => $this->getMultiDeviceSettings(),
            'full' => $full,
            'ec' => $this,
            'multishop_warning' => count($this->shopIDs()) > 1,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/carousel-form.tpl');
    }

    public function prepareAdvancedOptions($saved_settings)
    {
        $show_advanced = Tools::getValue('advanced');
        $advanced_options = [
            'tpl' => ['external_tpl', 'external_tpl_path'],
        ];
        foreach ($advanced_options as $type => $fields) {
            $advanced_options[$type] = array_fill_keys($fields, $show_advanced);
        }
        if (!$advanced_options['tpl']['external_tpl']) { // external_tpl should be visible if it was activated before
            foreach (array_keys($this->getDeviceTypes()) as $d_key) {
                if (!empty($saved_settings['tpl' . ($d_key == 'desktop' ? '' : '_' . $d_key)]['external_tpl'])) {
                    $advanced_options['tpl']['external_tpl'] = $advanced_options['tpl']['external_tpl_path'] = true;
                    break;
                }
            }
        }

        return $advanced_options;
    }

    public function getCarouselMultilangFields($saved_values = [])
    {
        $fields = [
            'name' => [
                'display_name' => $this->l('Carousel title'),
                'tooltip' => $this->l('You can leave it empty for carousels that are not in tabs'),
                'value' => [],
                'type' => 'text',
                'cols' => ['wrapper' => '8', 'label' => '3', 'input' => '9'],
            ],
            'description' => [
                'display_name' => $this->l('Carousel description'),
                'tooltip' => $this->l('You can leave it empty'),
                'value' => [],
                'type' => 'mce',
                'cols' => ['wrapper' => '12', 'label' => '2', 'input' => '10'],
            ],
        ];
        if ($saved_values) {
            foreach ($saved_values as $id_lang => $lang_values) {
                foreach ($lang_values as $name => $value) {
                    if (isset($fields[$name])) {
                        $fields[$name]['value'][$id_lang] = $value;
                    }
                }
            }
        }

        return $fields;
    }

    public function getDeviceTypes()
    {
        return ['desktop' => $this->l('Desktop version'), 'mobile' => $this->l('Mobile version')];
    }

    public function getMultiDeviceSettings()
    {
        return ['carousel' => 'carousel', 'tpl' => 'tpl'];
    }

    public function getExceptionsNote($settings)
    {
        $exc_note = '';
        if (isset($settings['exceptions'])) {
            $exceptions = [];
            if (!empty($settings['exceptions']['page']['type'])) {
                $exceptions[] = $this->l('on selected pages');
            }
            if (!empty($settings['exceptions']['customer']['type'])) {
                $exceptions[] = $this->l('for selected customers');
            }
            if ($exceptions) {
                $exc_note = sprintf($this->l('Displayed %s'), implode('/', $exceptions));
            }
        }

        return $exc_note;
    }

    public function ajaxBulkUpdate()
    {
        if (!$carousel_ids = Tools::getValue('ids')) {
            $this->throwError($this->l('Please select at least one carousel'));
        }
        $success = true;
        $bulk_action = Tools::getValue('bulk_action');
        $toggle_actions = [
            'enable' => ['active', 1],
            'disable' => ['active', 0],
            'group_in_tabs' => ['in_tabs', 1],
            'ungroup' => ['in_tabs', 0],
        ];
        if (isset($toggle_actions[$bulk_action])) {
            $success &= $this->db->execute('
                UPDATE ' . _DB_PREFIX_ . 'easycarousels
                SET `' . bqSQL($toggle_actions[$bulk_action][0]) . '` = ' . (int) $toggle_actions[$bulk_action][1] . '
                WHERE `id_carousel` IN (' . $this->sqlIDs($carousel_ids) . ')
                AND `id_shop` IN (' . $this->shopIDs(true) . ')
            ');
        } elseif ($bulk_action == 'delete') {
            foreach ($carousel_ids as $id_carousel) {
                $success &= $this->deleteCarousel($id_carousel);
            }
        }
        $this->afterCarouselUpdate(Tools::getValue('clear_cache_for_hook'));
        exit(json_encode(['success' => $success]));
    }

    public function afterCarouselUpdate($hook_name = '', $upd_caching_settings = false)
    {
        if ($hook_name) {
            $this->cache('clear', $hook_name . '_');
            if ($upd_caching_settings) {
                $this->cachingSettings('updateIfRequired', ['hook_name' => $hook_name]);
            }
        }
        $this->cache('clear', 'accs_');
    }

    public function ajaxSaveCarousel()
    {
        $id_carousel = $keep_positions = Tools::getValue('id_carousel');
        if ($id_carousel == 0) {
            $id_carousel = $this->getNewCarouselId();
        }
        $params_string = Tools::getValue('carousel_data');
        parse_str($params_string, $params);
        if (isset($params['in_tabs'])) {
            $empty_name_lang_ids = [];
            foreach ($params['multilang'] as $id_lang => $lang_data) {
                if (!$lang_data['name']) {
                    $empty_name_lang_ids[$id_lang] = $id_lang;
                }
            }
            if ($empty_name_lang_ids) {
                $this->errors[] = $this->l('Please fill carousel name in the following languages: ') .
                implode(', ', array_column($this->db->executeS('
                    SELECT iso_code FROM ' . _DB_PREFIX_ . 'lang
                    WHERE id_lang IN (' . $this->sqlIDs($empty_name_lang_ids) . ')
                '), 'iso_code'));
            }
        }
        if (empty($this->errors) && !$this->saveCarousel($id_carousel, $params)) {
            $this->errors[] = $this->l('Carousel not saved');
        }
        if (!$keep_positions && $ordered_ids_in_hook = Tools::getValue('ids_in_hook')) {
            foreach ($ordered_ids_in_hook as $k => $id) {
                if ($id == 0) {
                    // back.js is responsible for having only one carousel with id=0
                    $ordered_ids_in_hook[$k] = $id_carousel;
                }
            }
            $this->updatePositionsInHook($ordered_ids_in_hook);
        }
        // save wrapper if it was not saved before
        $id_wrapper_new = false;
        if (empty($params['id_wrapper'])) {
            $ids_in_wrapper = Tools::getValue('ids_in_wrapper');
            $id_wrapper_new = $this->updateCarouselWrapper($id_carousel, $id_wrapper_new, $ids_in_wrapper);
        }
        if (!empty($this->errors)) {
            $this->throwError($this->errors);
        }
        $result = [
            'updated_form_header' => $this->renderCarouselForm($id_carousel, false, false),
            'responseText' => $this->l('Saved'),
            'id_wrapper_new' => $id_wrapper_new,
        ];
        exit(json_encode($result));
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
            exit(json_encode(['successText' => $this->l('Saved')]));
        }
    }

    public function customCode($action, $params = [])
    {
        $ret = true;
        switch ($action) {
            case 'get':
                $ret = $this->customCode('getTypes');
                foreach ($ret as $type => &$code) {
                    $path = $this->customCode('getFilePath', ['type' => $type]);
                    if (file_exists($path)) {
                        $code = Tools::file_get_contents($path);
                    }
                }
                if (isset($params['type']) && isset($ret[$params['type']])) {
                    $ret = $ret[$params['type']];
                }
                break;
            case 'getDefault':
                $ret = '';
                if ($demo_file_path = $this->getDemoFilePath()) {
                    $demo_data = json_decode(Tools::file_get_contents($demo_file_path), true);
                    if (isset($demo_data[$params['type']])) {
                        $ret = $demo_data[$params['type']];
                    }
                }
                break;
            case 'save':
                $available_types = $this->customCode('getTypes');
                $type = $params['type'];
                if (isset($available_types[$type]) && $file_path = $this->customCode('getFilePath', $params)) {
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
                $ret = ['css' => '', 'js' => ''];
                break;
        }

        return $ret;
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
                $this->addJS('front.js'); // standard behavor for swiper11 is included here
                $this->sliderLibrary('loadAdapters', $lib);
                break;
            case 'loadAdapters':
                if ($params['type'] != 'swiper11') {
                    if ($params['type'] == 'bx') {
                        $this->addCSS('adapter/bx-adapter.css');
                    }
                    $this->addJS('adapter/' . $params['type'] . '-adapter.js');
                }
                break;
            case 'getData':
                if (!isset($this->lib_data)) {
                    $this->lib_data = json_decode(Configuration::get('EC_SLIDER_LIB'), true);
                }
                $ret = $this->lib_data;
                break;
            case 'detectExternal':
                $ret = [];
                foreach (['custombanners', 'amazzingblog'] as $m) {
                    if (Module::isEnabled($m) && $m = Module::getInstanceByName($m)) {
                        if (method_exists($m, 'sliderLibrary')) {
                            $ret = $m->sliderLibrary('getData');
                            break;
                        }
                    }
                }
                break;
            case 'install':
                if ($lib_data = $this->sliderLibrary('detectExternal')) {
                    $lib_data['load'] = !$lib_data['load'];
                }
                $ret = $this->sliderLibrary('updateData', $lib_data);
                break;
            case 'updateData':
                $available_options = $this->sliderLibrary('getOptions');
                $data = [
                    'type' => isset($params['type']) && isset($available_options[$params['type']]) ?
                        $params['type'] : current(array_keys($available_options)),
                    'load' => isset($params['load']) ? (int) $params['load'] : 1,
                ];
                $ret = Configuration::updateGlobalValue('EC_SLIDER_LIB', json_encode($data));
                break;
            case 'uninstall':
                $ret = Configuration::deleteByName('EC_SLIDER_LIB');
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

    public function ajaxUpdateSliderLibrary()
    {
        $params = ['type' => Tools::getValue('type'), 'load' => Tools::getValue('load')];
        if ($this->sliderLibrary('updateData', $params)) {
            exit(json_encode(['successText' => $this->l('Saved')]));
        }
    }

    public function validateSettings($settings, $carousel_type)
    {
        $errors = [];
        if (isset($settings['special'])) {
            foreach ($settings['special'] as $name => $value) {
                if ($name == 'tags') {
                    $settings['special'][$name] = Tools::getDescriptionClean($value);
                } elseif ($name == 'min_matches') {
                    $settings['special'][$name] = (int) $value;
                } else {
                    $settings['special'][$name] = $this->formatIDs($value);
                }
            }
        }
        if (isset($settings['exceptions'])) {
            foreach ($settings['exceptions'] as $key => &$exc) {
                if ($exc['type'] && Tools::substr($exc['type'], -4) != '_all') {
                    if (!$exc['ids'] = $this->formatIDs($exc['ids'])) {
                        $exc['type'] = ($key == 'page') ? $exc['type'] . '_all' : '0';
                    }
                } else {
                    $exc['ids'] = '';
                }
            }
        }
        switch ($carousel_type) {
            case 'products':
                if (!$settings['special']['product_ids']) {
                    $errors[] = $this->l('Please specify at least one Product ID');
                }
                break;
            case 'catproducts':
            case 'categories':
                if (!$settings['special']['cat_ids']) {
                    $errors[] = $this->l('Please specify at least one Category ID');
                }
                break;
            case 'bymanufacturer':
                if (!$settings['special']['id_manufacturer']) {
                    $errors[] = $this->l('Please specify at least one Manufacturer ID');
                }
                break;
            case 'bysupplier':
            case 'bysupplier_':
                if (!$settings['special']['id_supplier']) {
                    $errors[] = $this->l('Please specify at least one Supplier ID');
                }
                break;
            case 'bytag':
                if (!$settings['special']['tags']) {
                    $errors[] = $this->l('Please specify at least one Tag');
                }
                break;
        }
        foreach ($this->getDeviceTypes() as $d_type => $d_name) {
            $is_desktop = $d_type == 'desktop';
            $d_key = $is_desktop ? '' : '_' . $d_type;
            $d_txt = $is_desktop ? '' : ' | ' . $d_name;
            if (isset($settings['tpl' . $d_key])) {
                if (isset($settings['tpl' . $d_key]['external_tpl_path'])) {
                    $path = trim($settings['tpl' . $d_key]['external_tpl_path'], '/');
                    $settings['tpl' . $d_key]['external_tpl_path'] = $this->getFullExternalTplPath($path) ? $path : '';
                }
                $t = array_merge($settings['tpl'], $settings['tpl' . $d_key]);
                if ($t['external_tpl'] && !$t['external_tpl_path']) {
                    $errors[] = $this->l('Specified tpl file does not exist in your theme directory') . $d_txt;
                }
            }
            if (isset($settings['carousel' . $d_key])) {
                $c = array_merge($settings['carousel'], $settings['carousel' . $d_key]);
                if ($c['type'] == 1 && $c['r'] > 1 && $c['total'] % $c['r'] !== 0) {
                    $txt = $this->l('Make sure that number of items (%1$d) is divisible by number of rows (%2$d)');
                    $errors[] = sprintf($txt, $c['total'], $c['r']) . $d_txt;
                }
            }
        }
        if ($errors && Tools::getValue('ajax')) {
            $this->throwError($errors);
        }

        return $settings;
    }

    /**
     * @return bool result
     **/
    public function saveCarousel($id_carousel, $params)
    {
        $settings = $this->normalizeCarouselSettings($params['settings']);
        $settings = $this->validateSettings($params['settings'], $params['type']);
        $hook_name = $params['hook_name'];
        $language_ids = array_column(Language::getLanguages(false), 'id_lang', 'id_lang');
        $rows = $lang_rows = [];
        $position = !empty($params['position']) ? $params['position'] : $this->getNextCarouselPosition($hook_name);
        foreach ($this->shopIDs() as $id_shop) {
            $rows[$id_shop] = '(' . (int) $id_carousel;
            $rows[$id_shop] .= ', ' . (int) $id_shop;
            $rows[$id_shop] .= ', \'' . pSQL($hook_name) . '\'';
            $rows[$id_shop] .= ', ' . (int) $params['id_wrapper'];
            $rows[$id_shop] .= ', ' . (int) !empty($params['in_tabs']);
            $rows[$id_shop] .= ', ' . (int) !empty($params['active']);
            $rows[$id_shop] .= ', ' . (int) $position;
            $rows[$id_shop] .= ', \'' . pSQL($params['type']) . '\'';
            $rows[$id_shop] .= ', \'' . pSQL(json_encode($settings)) . '\')';
            foreach ($language_ids as $id_lang) {
                $lang_data = isset($params['multilang'][$id_lang]) ? $params['multilang'][$id_lang] : [];
                $lang_row = (int) $id_carousel . ', ' . (int) $id_shop . ', ' . (int) $id_lang;
                $lang_rows[] = '(' . $lang_row . ', \'' . pSQL(json_encode($lang_data), true) . '\')';
            }
        }
        if ($result = $rows && $lang_rows) {
            $result = $this->runSql([
                'REPLACE INTO ' . _DB_PREFIX_ . 'easycarousels VALUES ' . implode(', ', $rows),
                'REPLACE INTO ' . _DB_PREFIX_ . 'easycarousels_lang VALUES ' . implode(', ', $lang_rows),
            ]);
            if ($params['type'] == 'accessories') {
                $this->relatedOverrides()->process('addOverride', 'Product');
            }
            $this->registerHook($hook_name); // possibe duplicate hookRegistrations are handled in registerHook()
            $this->afterCarouselUpdate($hook_name, true);
        }

        return $result;
    }

    /**
     * @return int $id_wrapper
     **/
    public function updateCarouselWrapper($id_carousel, $id_wrapper = false, $ids_in_wrapper = [])
    {
        if (!is_array($ids_in_wrapper)) {
            return false;
        }
        if (!$id_wrapper) {
            $id_wrapper = $this->addWrapper();
        }
        // make sure id_carousel is included in wrapper
        $ids_in_wrapper = array_combine($ids_in_wrapper, $ids_in_wrapper);
        $ids_in_wrapper[$id_carousel] = $id_carousel;
        unset($ids_in_wrapper[0]);
        if ($ids_in_wrapper) {
            $updated = $this->db->execute('
                UPDATE ' . _DB_PREFIX_ . 'easycarousels
                SET id_wrapper = ' . (int) $id_wrapper . '
                WHERE id_carousel IN (' . $this->sqlIDs($ids_in_wrapper) . ')
            ');
            if ($updated) {
                $this->removeUnusedWrappers();
            }
        }

        return $id_wrapper;
    }

    public function removeUnusedWrappers()
    {
        $unused_wrappers = array_column($this->db->executeS('
            SELECT w.id_wrapper FROM ' . _DB_PREFIX_ . 'ec_wrapper w
            LEFT JOIN ' . _DB_PREFIX_ . 'easycarousels c ON c.id_wrapper = w.id_wrapper
            WHERE c.id_carousel IS NULL
        '), 'id_wrapper');
        if ($unused_wrappers_ = $this->sqlIDs($unused_wrappers)) {
            $this->db->execute('
                DELETE FROM ' . _DB_PREFIX_ . 'ec_wrapper WHERE id_wrapper IN (' . $unused_wrappers_ . ')
            ');
        }
    }

    public function addWrapper()
    {
        $added = $this->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'ec_wrapper VALUES (0, "[]")
        ');

        return $added ? $this->db->insert_ID() : false;
    }

    public function ajaxToggleParam()
    {
        $result = [];
        $id_carousel = Tools::getValue('id_carousel');
        $param_name = Tools::getValue('param_name');
        $param_value = Tools::getValue('param_value');
        if ($id_carousel && $param_name) {
            $result['success'] = $this->db->execute('
                UPDATE ' . _DB_PREFIX_ . 'easycarousels
                SET `' . bqSQL($param_name) . '` = ' . (int) $param_value . '
                WHERE `id_carousel` = ' . (int) $id_carousel . '
                AND `id_shop` IN (' . $this->shopIDs(true) . ')
            ');
            $this->afterCarouselUpdate(Tools::getValue('clear_cache_for_hook'));
        }
        exit(json_encode($result));
    }

    public function ajaxDeleteCarousel()
    {
        exit(json_encode(['deleted' => $this->deleteCarousel(Tools::getValue('id_carousel'))]));
    }

    public function deleteCarousel($id_carousel)
    {
        $ret = true;
        foreach (['easycarousels', 'easycarousels_lang'] as $table_name) {
            $ret &= $this->db->execute('
                DELETE FROM `' . _DB_PREFIX_ . bqSQL($table_name) . '`
                WHERE `id_carousel` = ' . (int) $id_carousel . '
                AND `id_shop` IN (' . $this->shopIDs(true) . ')
            ');
        }
        $this->afterCarouselUpdate(Tools::getValue('clear_cache_for_hook'), true);

        return $ret;
    }

    public function ajaxUpdatePositionsInHook()
    {
        $ordered_ids = Tools::getValue('ordered_ids');
        if (!$ordered_ids) {
            $this->throwError($this->l('Ordering failed'));
        }
        $moved_element_wrapper_id = $moved_element_id = $id_wrapper_new = false;
        if (Tools::getValue('moved_element_is_carousel')) {
            $moved_element_wrapper_id = Tools::getValue('moved_element_wrapper_id');
            $moved_element_id = Tools::getValue('moved_element_id');
            if (!$moved_element_wrapper_id) {
                $id_wrapper_new = $this->updateCarouselWrapper($moved_element_id);
                $moved_element_wrapper_id = $id_wrapper_new;
            }
        }
        if ($this->updatePositionsInHook($ordered_ids, $moved_element_wrapper_id, $moved_element_id)) {
            $this->afterCarouselUpdate(Tools::getValue('hook_name'));
            exit(json_encode([
                'id_wrapper_new' => $id_wrapper_new,
                'successText' => $this->l('Saved'),
            ]));
        } else {
            $this->throwError($this->l('Ordering failed'));
        }
    }

    public function updatePositionsInHook($ordered_ids, $moved_element_wrapper_id = false, $moved_element_id = false)
    {
        if (!$ordered_ids) {
            return true;
        }
        $update_rows = [];
        foreach ($this->shopIDs() as $id_shop) {
            foreach ($ordered_ids as $k => $id_carousel) {
                if ($id_carousel > 0) {
                    $pos = $k + 1;
                    $update_rows[] = '(' . (int) $id_carousel . ', ' . (int) $id_shop . ', ' . (int) $pos . ')';
                }
            }
        }
        $sql = [];
        $sql[] = '
            INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, position)
            VALUES ' . implode(', ', $update_rows) . '
            ON DUPLICATE KEY UPDATE position = VALUES(position)
        ';
        if ($moved_element_wrapper_id) {
            $sql[] = '
                UPDATE ' . _DB_PREFIX_ . 'easycarousels
                SET id_wrapper = ' . (int) $moved_element_wrapper_id . '
                WHERE id_carousel = ' . (int) $moved_element_id . '
            ';
        }

        return $this->runSql($sql);
    }

    public function getCarouselName($type)
    {
        $type_names = $this->getTypeNames(false);
        $name = isset($type_names[$type]) ? $type_names[$type] : $type;

        return $name;
    }

    public function getCarouselRows($hook_name = false, $front = false, $multilang = false)
    {
        $q = new DbQuery();
        $q->select('c.*')->from('easycarousels', 'c');
        if ($multilang) {
            $q->select('cl.`data` AS multilang_data');
            $q->leftJoin('easycarousels_lang', 'cl', 'c.`id_carousel` = cl.`id_carousel`
                AND cl.`id_lang` = ' . (int) $this->context->language->id . '
                AND cl.`id_shop` = c.`id_shop`');
        }
        if ($front) {
            $q->where('c.`active` = 1 AND c.`id_shop` = ' . (int) $this->context->shop->id);
            $q->orderBy('c.`position`');
        } else {
            $q->where($this->shopAssoc('where', 'c'));
            $q->groupBy('`id_carousel`');
            $q->orderBy($this->shopAssoc('order_by', 'c') . ', c.`position`');
        }
        if ($hook_name) {
            $q->where('c.`hook_name` = \'' . pSQL($hook_name) . '\'');
        }

        return $this->db->executeS($q);
    }

    public function getAllCarousels(
        $group_by = 'hook_name',
        $hook_name = false,
        $front = false,
        $id_product = 0,
        $id_category = 0,
        $current_id = 0,
        $current_controller = ''
    ) {
        $carousels = $this->getCarouselRows($hook_name, $front, true);
        if ($group_by) {
            $grouped_carousels = [];
            foreach ($carousels as $k => $carousel) {
                $settings = json_decode($carousel['settings'], true);
                $multilang = json_decode($carousel['multilang_data'], true) ?:
                array_fill_keys(array_keys($this->getCarouselMultilangFields()), '');
                $carousel['name'] = $multilang['name'];
                if (!$front) {
                    if ($exc_note = $this->getExceptionsNote($settings)) {
                        $carousel['exc_note'] = $exc_note;
                    }
                } elseif ($this->isAllowedForDisplay($settings, $current_controller, $current_id)) {
                    if ($this->isMobile()) {
                        foreach ($this->getMultiDeviceSettings() as $settings_type) {
                            if (isset($settings[$settings_type . '_mobile'])) {
                                $settings[$settings_type] = array_merge(
                                    $settings[$settings_type],
                                    $settings[$settings_type . '_mobile']
                                );
                            }
                        }
                    }
                    $carousel['item_type'] = $this->getCarouselItemType($carousel['type']);
                    $carousel['items'] = $this->getStructuredCarouselItems(
                        $carousel['type'],
                        $carousel['item_type'],
                        $settings,
                        $id_category,
                        $id_product
                    );
                    if (!$carousel['items']) {
                        continue;
                    }
                    $carousel['identifier'] = $carousel['type'] . '_' . $carousel['id_carousel'];
                    $carousel['description'] = $multilang['description'];
                    $carousel['is_swiper'] = $settings['carousel']['type'] == 1
                        && $this->sliderLibrary('getData')['type'] != 'bx';
                    $carousel['item_tpl'] = $this->getCarouselItemTpl($carousel['item_type'], $settings['tpl']);
                    $carousel['settings'] = $settings;
                    if (!empty($settings['tpl']['view_all'])) {
                        $link = $this->getLinkToAllItems($carousel['type'], $settings);
                        $carousel['view_all_link'] = $link;
                    }
                    if (!$carousel['name'] && $carousel['in_tabs']) {
                        $carousel['name'] = $this->getCarouselName($carousel['type']);
                    }
                    // prepare image sizes to be used later in tpls
                    foreach (['image_type', 'product_man'] as $i) {
                        if (!empty($settings['tpl'][$i])) {
                            $img_type = $settings['tpl'][$i];
                            if ($img_type && $img_type != 1 && empty($this->image_sizes[$img_type])) {
                                $this->image_sizes[$img_type] = Image::getSize($img_type);
                            }
                        }
                    }
                } else {
                    continue;
                }
                if (isset($carousel[$group_by])) {
                    $group_key = $carousel[$group_by];
                    if ($group_by == 'hook_name') {
                        $grouped_carousels[$group_key][$carousel['id_wrapper']][$k] = $carousel;
                    } else {
                        if ($group_by == 'in_tabs') {
                            $group_key = $group_key ? 'in_tabs' : 'one_by_one';
                        }
                        $grouped_carousels[$carousel['id_wrapper']][$group_key][$k] = $carousel;
                    }
                }
            }
            $carousels = $grouped_carousels;
        }

        return $carousels;
    }

    public function isAllowedForDisplay($settings, $current_controller, $current_id)
    {
        $allowed = true;
        if (!empty($settings['exceptions']['page']['type'])) {
            $allowed_ids = $this->formatIDs($settings['exceptions']['page']['ids'], false);
            switch ($settings['exceptions']['page']['type']) {
                case 'product_category':
                    $allowed = $current_controller == 'product' && $allowed_ids
                        && $this->isInCategory($current_id, $allowed_ids);
                    break;
                case 'product_manufacturer':
                    $allowed = $current_controller == 'product' && $allowed_ids
                        && isset($allowed_ids[$this->context->controller->getProduct()->id_manufacturer]);
                    break;
                case 'category_sub':
                    $allowed = $current_controller == 'category' && $allowed_ids
                        && $this->isSubcategory($this->context->controller->getCategory(), $allowed_ids);
                    break;
                default:
                    $allowed_controller = str_replace('_all', '', $settings['exceptions']['page']['type']);
                    $allowed = $current_controller == $allowed_controller
                        && (!$allowed_ids || isset($allowed_ids[$current_id]));
                    break;
            }
        }
        if ($allowed && !empty($settings['exceptions']['customer']['type'])) {
            $allowed_ids = $this->formatIDs($settings['exceptions']['customer']['ids'], false);
            if ($settings['exceptions']['customer']['type'] == 'group') {
                $allowed = (bool) array_intersect_key($this->customerGroups(), $allowed_ids);
            } else {
                $allowed = isset($allowed_ids[$this->context->customer->id]);
            }
        }

        return $allowed;
    }

    public function isInCategory($id_product, $cat_ids)
    {
        return (bool) $this->db->getValue('
            SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product
            WHERE id_product = ' . (int) $id_product . ' AND id_category IN (' . $this->sqlIDs($cat_ids) . ')
        '); // only direct associations
    }

    public function isSubcategory($category_obj, $parent_ids)
    {
        return (bool) $this->db->getValue('
            SELECT id_category FROM ' . _DB_PREFIX_ . 'category
            WHERE nleft < ' . (int) $category_obj->nleft . ' AND nright > ' . (int) $category_obj->nright . '
            AND id_category IN (' . $this->sqlIDs($parent_ids) . ')
        '); // detect parents of all levels
    }

    public function customerGroups($implode = false)
    {
        if (!isset($this->memo['c_groups'])) {
            $this->memo['c_groups'] = $this->context->customer->getGroups();
        }

        return $this->formatIDs($this->memo['c_groups'], $implode);
    }

    public function getLinkToAllItems($carousel_type, $settings)
    {
        $link = '';
        switch ($carousel_type) {
            case 'newproducts':
                $link = $this->context->link->getPageLink('new-products');
                break;
            case 'bestsellers':
                $link = $this->context->link->getPageLink('best-sales');
                break;
            case 'pricesdrop':
                $link = $this->context->link->getPageLink('prices-drop');
                break;
            case 'bymanufacturer':
                $link = $this->context->link->getManufacturerLink((int) $settings['special']['id_manufacturer']);
                break;
            case 'bysupplier':
            case 'bysupplier_':
                $link = $this->context->link->getSupplierLink((int) $settings['special']['id_supplier']);
                break;
            case 'catproducts':
                $link = $this->context->link->getCategoryLink((int) $settings['special']['cat_ids']);
                break;
        }

        return $link;
    }

    public function cachingSettings($action, $params = [])
    {
        $ret = [];
        switch ($action) {
            case 'get':
                $saved_settings = json_decode($this->db->getValue('
                    SELECT caching FROM ' . _DB_PREFIX_ . 'ec_hook_settings
                    WHERE hook_name = \'' . pSQL($params['hook_name']) . '\'
                    AND id_shop = ' . (int) $this->context->shop->id . '
                '), true) ?: [];
                $ret = $saved_settings + $this->cachingSettings('getDefault');
                break;
            case 'getDefault':
                $ret = ['time' => 0, 'country' => 0, 'group' => 0, 'check_ids' => [], 'blocked' => 0];
                break;
            case 'updateIfRequired':
                $settings = $this->cachingSettings('get', $params);
                $upd_settings = $this->cachingSettings('adjust', $params + ['settings' => $settings]);
                if ($upd_settings != $settings) {
                    $this->saveHookSettings($params['hook_name'], 'caching', $upd_settings);
                }
                break;
            case 'adjust':
                $upd_settings = ['check_ids' => [], 'blocked' => 0];
                foreach ($this->getCarouselRows($params['hook_name']) as $carousel) {
                    if (!$this->cachingSettings('isCacheable', $carousel)) {
                        $upd_settings['blocked'] = 1;
                        break;
                    }
                    $c_settings = json_decode($carousel['settings'], true);
                    if (!empty($c_settings['php']['consider_cat'])) {
                        $upd_settings['check_ids']['category'] = 1;
                    }
                    if (!empty($c_settings['exceptions'])) {
                        foreach ($c_settings['exceptions'] as $exc) {
                            if ($exc['type'] && $exc['ids']) {
                                $obj_type = current(explode('_', $exc['type']));
                                $upd_settings['check_ids'][$obj_type] = 1;
                            }
                        }
                    }
                }
                foreach (array_keys($upd_settings['check_ids']) as $obj_type) {
                    if (!$this->cachingSettings('allowIDs', [$obj_type])) {
                        $upd_settings['blocked'] = 1;
                        unset($upd_settings['check_ids'][$obj_type]);
                    }
                }
                if ($upd_settings['blocked']) {
                    $upd_settings['time'] = 0;
                }
                $ret = array_merge($params['settings'], $upd_settings);
                break;
            case 'adjustAll':
                $data = $this->db->executeS('
                    SELECT id_shop, hook_name FROM ' . _DB_PREFIX_ . 'ec_hook_settings WHERE caching <> \'\'
                ');
                $sorted = [];
                foreach ($data as $row) {
                    $sorted[$row['id_shop']][$row['hook_name']] = $row['hook_name'];
                }
                foreach ($sorted as $id_shop => $hooks) {
                    $this->setCustomShopContext($id_shop);
                    foreach ($hooks as $hook_name) {
                        $this->cachingSettings('updateIfRequired', ['hook_name' => $hook_name]);
                    }
                }
                $this->restoreOriginalContext();
                break;
            case 'isCacheable':
                $not_cacheable = ['viewedproducts', 'samecategory', 'samefeature',
                    'samemanufacturer', 'sametag', 'accessories'];
                $ret = !in_array($params['type'], $not_cacheable);
                break;
            case 'allowIDs':
                $allowed_types = ['product' => 1, 'category' => 1, 'manufacturer' => 1,
                    'supplier' => 1, 'cms' => 1, 'group' => 1]; // don't allow customer ids
                $obj_type = $params[0];
                $ret = isset($allowed_types[$obj_type]) && (int) $this->db->getValue('
                    SELECT COUNT(`id_' . bqSQL($obj_type) . '`) FROM `' . _DB_PREFIX_ . bqSQL($obj_type) . '`
                ') < 50;
                break;
        }

        return $ret;
    }

    public function cache($action, $cache_id, $data = '', $cache_time = 3600, $decode = false)
    {
        $ret = true;
        $full_path = $this->local_path . 'cache/' . $cache_id;
        switch ($action) {
            case 'get':
                if ($ret = file_exists($full_path) && (time() - filemtime($full_path) < $cache_time)) {
                    $ret = Tools::file_get_contents($full_path);
                    if ($decode) {
                        $ret = json_decode($ret, true);
                    }
                }
                break;
            case 'save':
                if (!is_string($data)) {
                    $data = json_encode($data);
                }
                $ret = file_put_contents($full_path, $data) !== false;
                break;
            case 'clear':
                foreach (glob($full_path . '*') as $path) {
                    $ret &= unlink($path);
                }
                break;
            case 'getInfo':
                if ($ret = glob($full_path . '*') ?: '') {
                    $ret = sprintf(
                        $this->l('Cache size: %1$s | last updated: %2$s'),
                        Tools::formatBytes(array_sum(array_map('filesize', $ret))) . 'b',
                        date('Y-m-d H:i:s', max(array_map('filemtime', $ret)))
                    );
                }
                break;
        }

        return $ret;
    }

    public function formatIDs($ids, $return_string = true)
    {
        if ($ids = is_array($ids) ? $ids : explode(',', $ids)) {
            $ids = array_map('intval', $ids);
            $ids = array_combine($ids, $ids);
            unset($ids[0]);
        }

        return $return_string ? implode(',', $ids) : $ids;
    }

    public function sqlIDs($ids)
    {
        return $this->formatIDs($ids, true);
    }

    public function shopIDs($return_string = false)
    {
        if (!isset($this->memo['shop_ids'])) {
            $this->memo['shop_ids'] = Shop::getContextListShopID();
        }

        return $this->formatIDs($this->memo['shop_ids'], $return_string);
    }

    public function throwError($errors)
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }
        $errors_html = $this->displayError(implode('<br>', $errors));
        if (Tools::isSubmit('ajax')) {
            exit(json_encode(['errors' => $errors_html]));
        }

        return $errors_html;
    }

    public function relatedOverrides()
    {
        if (!isset($this->related_overrides)) {
            require_once $this->local_path . 'classes/RelatedOverrides.php';
            $this->related_overrides = new RelatedOverrides($this);
        }

        return $this->related_overrides;
    }
}
