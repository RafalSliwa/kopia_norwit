<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductXmlApi
{
    protected $settings = array();
    protected $attributeMapValues = array();
    protected $featureMapValues = array();
    protected $isFeatureActive = true;
    protected $PS_LABEL_OOS_PRODUCTS_BOA = ''; //Label of out-of-stock products with allowed backorders
    protected $langId = 1;
    protected $langIdAll = array();
    protected $langIdWitIso = [];
    protected $productTitleEditorValues = array();
    protected $productParam = array();
    protected $productAttributes = [];
    protected $productAttributesAllLanguages = [];
    protected $featuresKeyByName = array();
    protected $isExistsCategoryGetAllParents = true;
    protected $publicGrProducts = false;
    protected $extraFieldByName = [];
    protected $productFeatures = [];
    protected $productFeaturesWithValue = [];
    protected $productFeaturesWithId = [];
    protected $productLangValues = [];
    protected $taxRateList = [];
    protected $productAttributeAndFeatureName = [];
    protected $groupedAttributesByParent = [];
    protected $offerAdditionalFields = [];
    protected $variantParentGroupId = 0;
    protected $isTheSameAttribute = false;

    /**
     * @var AvailabilityLabel
     */
    protected $availabilityLabel;

    /**
     * @var ProductTitleEditor
     */
    protected $productTitleEditor;
    protected $attributesGroupsAll = [];

    public function getFeed(
        $permissions,
        $id,
        $prefS,
        $prefE,
        $html_tags_status,
        $extra_feed_row,
        $one_branch,
        $only_enabled,
        $split_feed_limit,
        $part,
        $categories,
        $cat_list,
        $multistoreString,
        $onlyInStock,
        $priceRange,
        $price_with_currency,
        $mode,
        $allImages,
        $affiliate,
        $currencyId,
        $feedGenerationTime,
        $feedGenerationTimeName,
        $splitByCombination,
        $productList,
        $productListStatus,
        $shippingCountry,
        $filterDiscount,
        $filterCategoryType,
        $productSettingsPackageId,
        $settings,
        $feedSettings,
        $context
    ) {
        $mode = $settings['feed_mode'];
        $settings['feed_mode_real'] = $mode;
        $this->settings = $feedSettings;
        $this->settings['pref_s'] = $prefS;
        $this->settings['pref_e'] = $prefE;
        $this->settings['is_enabled_field'] = [];
        $this->settings['field_status_offers'] = json_decode(htmlspecialchars_decode(Configuration::get('BLMOD_XML_FEED_STATUS_OFFERS')), true);
        $this->settings['feed_type_sql'] = ($this->settings['xml_type'] == 'offers') ? 'offer' : '';
        $this->settings['combination_id_separator'] = !empty($this->settings['combination_id_separator']) ? $this->settings['combination_id_separator'] : '-';
        $outOfStockStatus = 1;

        $this->settings['domain'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
        $domainParts = preg_split('/(?=\.[^.]+$)/', $this->settings['domain']);
        $this->settings['domain'] = $domainParts[0];

        if ($mode == 'cat' || $mode == 'publ' || $mode == 'dar' || $mode == 'ibs' || $mode == 'ven') {
            $mode = 'mir';
        }

        if ($mode == 'pb' || $mode == 'cri' || $mode == 'pm' || $mode == 'gei' || $mode == 'ski'
            || $mode == 'cew' || $mode == 'bi' || $mode == 'hb' || $mode == 'hb'|| $mode == 'fc' || $mode == 'pl'
            || $mode == 'f' || $mode == 'ins' || $mode == 'pint' || $mode == 'fav' || $mode == 'iem') {
            $mode = 'g';
        }

        if ($mode == 'twi') {
            $mode = 'pub';
        }

        if ($mode == 'apl') {
            $mode = 'ceo';
        }

        $this->settings['feed_mode_final'] = $mode;

        $toolsR = new ReflectionMethod('Tools', 'getPath');
        $this->settings['totalGetPathMethods'] = $toolsR->getNumberOfRequiredParameters();
        $this->settings['iSMinqcValid'] = XmlFeedsTools::iSMinqcValid();

        $productListClass = new ProductList($settings['product_list_exclude']);
        $productSettings = new ProductSettingsFront();
        $mergeAttributesByGroup = new MergeAttributesByGroup();
        $filterByAttribute = new FilterByAttribute();
        $productPropertyMap = new ProductPropertyMap();
        $this->productTitleEditor = new ProductTitleEditor();
        $feedPrice = new FeedPrice();
        $productCombination = new ProductCombinations();
        $filterByFeature = new FilterByFeature();
        $categoryTreeGenerator = new CategoryTreeGenerator();
        $databaseTableConnector = new DatabaseTableConnector();
        $this->availabilityLabel = new AvailabilityLabel();
        $feedShippingPrice = new FeedShippingPrice();
        $link_class = new Link();
        $cronManager = new CronManager();

        $this->productTitleEditor->feedSettings = $this->settings;
        $this->productTitleEditorValues = $this->productTitleEditor->getByFeedId($id);
        $this->attributeMapValues = $productPropertyMap->getMapValuesWithKey($this->settings['attribute_map_id']);
        $this->featureMapValues = $productPropertyMap->getMapValuesWithKey($this->settings['feature_map_id']);
        $this->isFeatureActive = Feature::isFeatureActive();
        $this->settings['title_elements'] = $this->productTitleEditor->getNewElementsByFeedId($id);
        $this->isExistsCategoryGetAllParents = method_exists('Category', 'getAllParents');
        $genderCategories = $categoryTreeGenerator->get($id);
        $customTableFields = $databaseTableConnector->get($id);
        $productListProductIdForXmlTags = [];
        $customXmlTagsByProductList = [];
        $customXmlTagByProductId = [];
        $this->settings['base_link'] = is_callable([$link_class, 'getBaseLink']) ? $link_class->getBaseLink() : _PS_BASE_URL_.__PS_BASE_URI__;

        if (!empty($this->settings['product_list_xml_tag_array'])) {
            $customXmlTagsByProductList = $productListClass->getProductListWithXmlTags($this->settings['product_list_xml_tag_array']);

            foreach ($this->settings['product_list_xml_tag_array'] as $lx) {
                $productListProductIdForXmlTags[$lx] = $productListClass->getProductsByProductList([$lx], []);

                foreach ($productListProductIdForXmlTags[$lx] as $i) {
                    $customXmlTagByProductId[$i][$lx] = $lx;
                }
            }
        }

        if (!empty($permissions['merge_attributes_by_group']) && empty($permissions['merge_attributes_parent_id'])) {
            $permissions['merge_attributes_by_group'] = 0;
        }

        if (!empty($permissions['merge_attributes_by_group'])) {
            $mergeAttributesByGroup->setParentGroup($permissions['merge_attributes_parent_id']);
            $mergeAttributesByGroup->setChildGroup($permissions['merge_attributes_child']);
        }

        $productSettingsList = $productSettings->getXmlByPackageId($productSettingsPackageId);

        $block_name = array();
        $block_status = array();
        $xml_name = array();
        $xml_name_l = array();
        $all_l_iso = array();
        $xml_cat_name = array();
        $xml_lf = array();
        $cover_i = array();
        $image_info = array();
        $priceFrom = false;
        $priceTo = false;
        $xml = '';
        $categoriesOfProductsUsed = array();
        $productId = htmlspecialchars(Tools::getValue('product_id'), ENT_QUOTES);

        if (!empty($priceRange)) {
            list($priceFrom, $priceTo) = explode(';', $priceRange);
        }

        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $url_type = getShopProtocol();
        $allImages = !empty($splitByCombination) ? true : $allImages;

        $block_n = Db::getInstance()->ExecuteS('SELECT `name`, `value`, `status`
            FROM '._DB_PREFIX_.'blmod_xml_block
            WHERE category = "'.(int)$id.'"');

        foreach ($block_n as $bn) {
            $block_name[$bn['name']] = $bn['value'];
            $block_status[$bn['name']] = $bn['status'];
        }

        $r = Db::getInstance()->ExecuteS('SELECT `name`, `status`, `title_xml`, `table`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` != "lang" AND `table` != "img_blmod" AND `table` != "category_lang"
            AND `table` != "product_lang" AND `table` != "bl_extra" AND `table` != "bl_extra_att" AND `status` = 1
            AND `table` != "bl_extra_feature" AND `table` != "bl_extra_attribute_group"
            ORDER BY `table` ASC');

        $field = '';

        foreach ($r as $f) {
            $field .= ' '._DB_PREFIX_.$f['table'].'.`'.$f['name'].'` AS '.$f['table'].'_'.$f['name'].' ,';
            $xml_name[$f['table'].'_'.$f['name']] = $f['title_xml'];
        }

        $extra_field = Db::getInstance()->ExecuteS('SELECT `name`, `title_xml`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "bl_extra" AND status = "1"');

        if (empty($field) && empty($extra_field)) {
            die('empty field list');
        }

        if (!empty($extra_field)) {
            foreach ($extra_field as $b_e) {
                if (empty($b_e['title_xml'])) {
                    continue;
                }

                $this->extraFieldByName[$b_e['name']] = $b_e['title_xml'];
            }
        }

        if (!empty($field)) {
            $field = ','.trim($field, ',');
        }

        $where_only_active = '';
        $order = !empty($settings['order_by_column']) ? ' ORDER BY '.htmlspecialchars($settings['order_by_column'], ENT_QUOTES) : '';
        $limit = '';

        if (!empty($only_enabled)) {
            $where_only_active = 'WHERE '._DB_PREFIX_.'product_shop.active = "1"';
        }

        if (!empty($split_feed_limit) && !empty($part)) {
            $order = !empty($order) ? $order : ' ORDER BY '._DB_PREFIX_.'product.id_product ASC';
            $limit = ' LIMIT '.((int)$split_feed_limit * (int)--$part).','.(int)$split_feed_limit;
        }

        if (!empty($this->settings['item_limit'])) {
            $limit = ' LIMIT '.(int)$this->settings['item_limit'];
        }

        if (!empty($this->settings['use_cron']) && !empty($this->settings['cron_chunks_limit'])) {
            $currentChunkNo = $cronManager->getCurrentChunkNo($id);

            $order = ' ORDER BY '._DB_PREFIX_.'product.id_product ASC';
            $limit = ' LIMIT '.($currentChunkNo * $this->settings['cron_chunks_limit']).', '.(int)$this->settings['cron_chunks_limit'];

            $currentChunkNo = $currentChunkNo + 1;
            $cronManager->updateChunkNo($id, $currentChunkNo);
        }

        $category_table = '';
        $categoryJoinMain = false;

        if (!empty($categories) && !empty($cat_list)) {
            if (empty($filterCategoryType)) {
                $categoryJoinMain = true;

                $category_table = '
                LEFT JOIN '._DB_PREFIX_.'category_product cp ON
                ('._DB_PREFIX_.'category_product.id_product = '._DB_PREFIX_.'product.id_product AND '._DB_PREFIX_.'product.id_category_default = '._DB_PREFIX_.'category_product.id_category)';

                $where_only_active .= $this->whereType($where_only_active) . _DB_PREFIX_ . 'product.id_category_default IN ('.pSQL($cat_list).')';
            } else {
                $category_table = 'INNER JOIN '._DB_PREFIX_.'category_product cp ON
                ('._DB_PREFIX_.'category_product.id_product = '._DB_PREFIX_.'product.id_product AND '._DB_PREFIX_.'category_product.id_category IN ('.pSQL($cat_list).'))';
            }
        }

        if (!empty($feedSettings['categories_without']) && !empty($feedSettings['cat_without_list'])) {
            if (empty($feedSettings['filter_category_without_type'])) {
                if (!$categoryJoinMain) {
                    $category_table .= '
                    LEFT JOIN '._DB_PREFIX_.'category_product cw ON
                    (cw.id_product = '._DB_PREFIX_.'product.id_product AND '._DB_PREFIX_.'product.id_category_default = cw.id_category)';
                }

                $where_only_active .= $this->whereType($where_only_active) . _DB_PREFIX_ . 'product.id_category_default NOT IN ('.pSQL($feedSettings['cat_without_list']).')';
            } else {
                $category_table .= 'LEFT JOIN '._DB_PREFIX_.'category_product cw ON
                (cw.id_product = '._DB_PREFIX_.'product.id_product AND cw.id_category IN ('.pSQL($feedSettings['cat_without_list']).'))';

                $where_only_active .= $this->whereType($where_only_active).'cw.id_product IS NULL';
            }
        }

        $multistoreJoin = '';
        $multistoreId = !empty($multistoreString) ? (int)$multistoreString : null;

        if (!empty($permissions['manufacturer']) && !empty($permissions['manufacturer_list'])) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.id_manufacturer IN ('.pSQL($permissions['manufacturer_list']).')';
        }

        if (!empty($permissions['supplier']) && !empty($permissions['supplier_list'])) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.id_supplier IN ('.pSQL($permissions['supplier_list']).')';
        }

        if (!empty($permissions['filter_visibility'])) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.visibility = "'.pSQL($permissions['filter_visibility']).'"';
        }

        if (!empty($productId)) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.id_product = "'.(int)$productId.'"';
        }

        if ((!empty($settings['product_list_exclude']) || !empty($productList)) && !empty($productListStatus)) {
            $productListExcludeActive = $productListClass->getExcludeProductsByProductList();

            $productListActive = $productListClass->getProductsByProductList($productList, $productListExcludeActive);
            $productListActive = !empty($productListActive) ? $productListActive : [0];

            $productListExcludeActive = $productListClass->getExcludeProductsByProductList();

            if (!empty($productList)) {
                $where_only_active .= $this->whereType($where_only_active) . _DB_PREFIX_ . 'product.id_product IN (' . pSQL(trim(implode(',', $productListActive), ',')) . ')';
            }

            if (!empty($productListExcludeActive)) {
                $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.id_product NOT IN ('.pSQL(trim(implode(',', $productListExcludeActive), ',')).')';
            }
        }

        if (!empty($permissions['only_on_sale'])) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product_shop.on_sale = 1';
        }

        if (!empty($permissions['only_available_for_order'])) {
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product_shop.available_for_order = 1';
        }

        if (!empty($settings['filter_created_before_days'])) {
            $dateCreatedBefore = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-'.(int)$settings['filter_created_before_days'].'days'));
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.date_add > "'.$dateCreatedBefore.'"';
        }

        if (!empty($settings['filter_updated_before_days'])) {
            $dateCreatedBefore = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'-'.(int)$settings['filter_updated_before_days'].'days'));
            $where_only_active .= $this->whereType($where_only_active)._DB_PREFIX_.'product.date_upd > "'.$dateCreatedBefore.'"';
        }

        $isbnSqlField = XmlFeedsTools::isIsbnExists() ? _DB_PREFIX_.'product.isbn AS blmod_isbn, ' : '';

        $customTableFieldsSql = '';

        if (!empty($customTableFields['column_value'][0])) {
            list($customColumn, $customTable) = explode('+', $customTableFields['column_connector'][0]);
            $field .= ', '.$customTable.'.'.$customTableFields['column_value'][0].' AS blmod_custom_table_0';

            $customTableFieldsSql = 'LEFT JOIN '.$customTable.' ON '.$customTable.'.'.$customColumn.' = '._DB_PREFIX_.'product.id_product ';
            $xml_name['blmod_custom_table_0'] = $customTableFields['name'][0];
        }

        $replaceTableName = [
            _DB_PREFIX_.'product.' => 'p.',
            _DB_PREFIX_.'manufacturer.' => 'm.',
            _DB_PREFIX_.'supplier.' => 's.',
            _DB_PREFIX_.'product_shop.' => 'ps.',
            _DB_PREFIX_.'category_product.' => 'cp.',
        ];

        $subQuery = '';

        if ($this->settings['iSMinqcValid']) {
            $subQuery = ', (
                SELECT mqc.quantity
                FROM ' . _DB_PREFIX_ . 'minqc mqc
                WHERE mqc.id_product = p.id_product AND mqc.id_shop = ' . (!empty($multistoreId) ? $multistoreId : 1) . '
                ORDER BY mqc.group ASC
                LIMIT 1
            ) as minqc_qty';
        }

        $sql = 'SELECT DISTINCT('._DB_PREFIX_.'product.id_product) AS pro_id, '._DB_PREFIX_.'product.id_category_default AS blmod_cat_id,
            '._DB_PREFIX_.'product.reference AS blmod_reference, '._DB_PREFIX_.'product.ean13 AS blmod_ean13,
            '._DB_PREFIX_.'product.upc AS blmod_upc, '.pSQL($isbnSqlField).
            _DB_PREFIX_.'manufacturer.name AS blmod_manufacturer, '._DB_PREFIX_.'product.price AS blmod_price'.pSQL($field).$subQuery.'
            FROM '._DB_PREFIX_.'product p
            LEFT JOIN '._DB_PREFIX_.'manufacturer m ON
            m.id_manufacturer = p.id_manufacturer
            LEFT JOIN '._DB_PREFIX_.'supplier s ON
            s.id_supplier = p.id_supplier
            LEFT JOIN '._DB_PREFIX_.'product_shop ps ON 
            (p.id_product = ps.id_product AND ps.id_shop = '.(!empty($multistoreId) ? $multistoreId : 1).')
            '.$multistoreJoin.$category_table.$customTableFieldsSql.$where_only_active.$order.$limit;

        foreach ($replaceTableName as $name => $prefix) {
            $sql = str_replace($name, $prefix, $sql);
        }

        $xmlWithoutKey = Db::getInstance()->ExecuteS($sql);

        if (empty($xmlWithoutKey) && !empty($currentChunkNo)) {
            $cronManager->updateChunkNo($id, 0);
            $isLastChunk = true;
        }

        $this->settings['is_enabled_field']['available_for_order'] = $this->isEnabledField($id, 'available_for_order', 'product');
        $this->settings['is_enabled_field']['quantity'] = $this->isEnabledField($id, 'quantity', 'product');
        $this->settings['is_enabled_field']['availability_label'] = $this->isEnabledField($id, 'availability_label', 'bl_extra');
        $this->settings['field_name']['price_sale_blmod'] = $this->getFieldName($id, 'price_sale_blmod', 'bl_extra');

        $xml_d = [];

        foreach ($xmlWithoutKey as $p) {
            $xml_d[$p['pro_id']] = $p;
        }

        //Language
        $l = Db::getInstance()->ExecuteS('SELECT `name`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "lang"');

        $googleCatMap = $this->getGoogleCatMap($mode, $settings);
        $count_lang = 0;
        $categoriesByKey = [];

        if (!empty($l)) {
            $count_lang = count($l);

            if ($count_lang < 2) {
                $id_lang = $l[0]['name'];
                $this->langId = $id_lang;
            }

            //Default category name
            $cat_name_status = Db::getInstance()->getRow('SELECT `name`, `status`, `title_xml`
                FROM '._DB_PREFIX_.'blmod_xml_fields
                WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "category_lang"');

            if ((!empty($cat_name_status['status']) && !empty($cat_name_status['title_xml'])) || !empty($this->extraFieldByName['main_category_name_2'])) {
                $cat_name = $this->getAllCategories($l, $multistoreId);
                $this->settings['categories'] = [];

                foreach ($cat_name as $c) {
                    $this->settings['categories'][$c['id_category']] = $c;
                }
            }

            if (!empty($cat_name_status['status']) && !empty($cat_name_status['title_xml'])) {
                if (!empty($cat_name)) {
                    $cat_old = false;

                    if ($count_lang < 2 || $mode == 'ep') {
                        foreach ($cat_name as $cn) {
                            $categoriesByKey[$cn['id_category']] = $cn['name'];

                            if ($cat_old == $cn['id_category']) {
                                $xml_cat_name[$cn['id_category']] .= $this->getDeepTagName($cat_name_status['title_xml']);
                            } else {
                                $xml_cat_name[$cn['id_category']] = $this->getDeepTagName($cat_name_status['title_xml']);
                            }

                            if (!empty($googleCatMap[$cn['id_category']])) {
                                $cn['name'] = $googleCatMap[$cn['id_category']]['name'];
                            }

                            $xml_cat_name[$cn['id_category']] .= $this->settings['pref_s'].$cn['name'].$this->settings['pref_e'];
                            $xml_cat_name[$cn['id_category']] .= $this->getDeepTagName($cat_name_status['title_xml'], true);

                            if ($mode == 'pub') {
                                $xml_cat_name[$cn['id_category']] = $this->settings['pref_s'].$cn['name'].$this->settings['pref_e'];
                            }

                            $cat_old = $cn['id_category'];
                        }
                    } else {
                        foreach ($cat_name as $cn) {
                            $langPrefix = '-'.$cn['iso_code'];

                            if ($cat_old == $cn['id_category']) {
                                $xml_cat_name[$cn['id_category']] .= $this->getDeepTagName($cat_name_status['title_xml'].$langPrefix);
                            } else {
                                $xml_cat_name[$cn['id_category']] = $this->getDeepTagName($cat_name_status['title_xml'].$langPrefix);
                            }

                            if (!empty($googleCatMap[$cn['id_category']])) {
                                $cn['name'] = $googleCatMap[$cn['id_category']]['name'];
                            }

                            $xml_cat_name[$cn['id_category']] .= $this->settings['pref_s'].$cn['name'].$this->settings['pref_e'];
                            $xml_cat_name[$cn['id_category']] .= $this->getDeepTagName($cat_name_status['title_xml'].$langPrefix, true);

                            $cat_old = $cn['id_category'];
                        }
                    }
                }
            } else {
                $xml_cat_name = [];
                $categoriesAll = $this->getAllCategories($l, $multistoreId);

                foreach ($categoriesAll as $c) {
                    $categoriesByKey[$c['id_category']] = $c['name'];
                }
            }

            //Description
            $l_where = '';
            $languagesFromDb = Language::getLanguages(false);

            foreach ($l as $ll) {
                foreach ($languagesFromDb as $lDb) {
                    if ($lDb['id_lang'] == $ll['name']) {
                        $this->langIdWitIso[$ll['name']] = $lDb['iso_code'];
                        break;
                    }
                }

                $l_where .= 'OR '._DB_PREFIX_.'product_lang.id_lang='.(int)$ll['name'].' ';
            }

            $l_where = trim($l_where, 'OR');

            if (_PS_VERSION_ >= '1.5') {
                $l_where .= ' AND '._DB_PREFIX_.'product_lang.id_shop = "'.(!empty($multistoreId) ? (int)$multistoreId : "1").'"';
            }

            $rl = Db::getInstance()->ExecuteS('SELECT `name`, `status`, `title_xml`
                FROM '._DB_PREFIX_.'blmod_xml_fields
                WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "product_lang" AND status = 1');

            $field = '';

            foreach ($rl as $fl) {
                $field .= ' `'._DB_PREFIX_.'product_lang`.`'.$fl['name'].'`,';
                $xml_name_l[$fl['name']] = $fl['title_xml'];
            }

            if (!empty($field)) {
                $field = ','.trim($field, ',');
            }

            $xml_l = Db::getInstance()->ExecuteS('SELECT '._DB_PREFIX_.'product_lang.id_product, 
                '._DB_PREFIX_.'product_lang.description_short AS description_short_blmod, 
                '._DB_PREFIX_.'lang.iso_code as blmodxml_l '.pSQL($field).'
                FROM '._DB_PREFIX_.'product_lang
                LEFT JOIN '._DB_PREFIX_.'lang ON
                '._DB_PREFIX_.'lang.id_lang = '._DB_PREFIX_.'product_lang.id_lang
                WHERE '.$l_where.'
                ORDER BY '._DB_PREFIX_.'product_lang.id_product ASC');

            $shortDescriptionList = [];

            if (!empty($xml_l) && !empty($field)) {
                $firstLang = !empty($languagesFromDb[0]) ? $languagesFromDb[0]['iso_code'] : '';

                foreach ($xml_l as $xll) {
                    $id_cat = $xll['id_product'];
                    $l_iso = $xll['blmodxml_l'];
                    $all_l_iso[] = $l_iso;
                    $lang_prefix = '-'.$l_iso;
                    $prefixOpen = '';

                    if ($count_lang < 2 && $mode != 'h') {
                        $lang_prefix = '';
                    }

                    if ($mode == 'h') {
                        $lang_prefix = $this->getLanguageCodeLong($l_iso);
                    }

                    if ($mode == 'ep') {
                        $lang_prefix = '';
                        $prefixOpen = ' lang="'.$l_iso.'"';
                    }

                    if ($mode == 'spa') {
                        $lang_prefix = '';
                    }

                    if ($mode == 'pub') {
                        $lang_prefix = Tools::strtoupper($this->getLanguageCodeLong($l_iso));
                    }

                    $xml_lf[$id_cat.$l_iso] = '';

                    foreach ($xll as $idl => $vall) {
                        if ($idl == 'id_product' || $idl == 'blmodxml_l' || ($mode != 'i' && $idl == 'description_short_blmod')) {
                            continue;
                        }

                        $vall = isset($vall) ? $vall : '';
                        $vall = !empty($this->settings['is_htmlspecialchars']) ? htmlspecialchars_decode($vall, ENT_QUOTES) : $vall;

                        if ($html_tags_status) {
                            $vall = strip_tags($vall);
                        }

                        if (!empty($this->settings['merge_descriptions'])) {
                            if ($idl == 'description') {
                                $xll['description_short_blmod'] = $html_tags_status ? strip_tags($xll['description_short_blmod']) : $xll['description_short_blmod'];
                                $xll['description_short_blmod'] = !empty($this->settings['is_htmlspecialchars']) ? htmlspecialchars_decode($xll['description_short_blmod'], ENT_QUOTES) : $xll['description_short_blmod'];
                                $vall = trim($xll['description_short_blmod'] . ' ' . $vall);
                            }

                            if ($idl == 'description_short') {
                                continue;
                            }
                        }

                        if ($idl == 'name') {
                            if ($mode == 'i' && !empty($xml_d[$xll['id_product']]['manufacturer_name'])) {
                                $vall = $xml_d[$xll['id_product']]['manufacturer_name'].' '.$vall;
                            }

                            $this->productParam['title'.'-'.$l_iso][$xll['id_product']] = $vall;
                            $vall = REPLACE_COMBINATION.$idl.'-'.$l_iso;
                        }

                        if (($idl == 'description_short' || $idl == 'description') && empty($vall)) {
                            $vall = REPLACE_COMBINATION.$idl.'-'.$l_iso;
                        }

                        if (($idl == 'description_short') && $mode == 'mal') {
                            $vall = Tools::substr($vall, 0, 300);
                        }

                        if (($idl == 'description') && $mode == 'mal') {
                            $vall = Tools::substr($vall, 0, 13000);
                        }

                        if ($mode == 'i' && $idl == 'description_short_blmod') {
                            $shortDescriptionList[$xll['id_product']] = htmlspecialchars($vall);
                            continue;
                        }

                        if ($mode == 'pub') {
                            if ($idl == 'name' && $l_iso != $firstLang) {
                                continue;
                            }

                            $xml_lf[$id_cat . $l_iso] .= '<attribute><code>'.$xml_name_l[$idl].(($count_lang > 1 && ($idl == 'description_short' || $idl == 'description')) ? '_'.$lang_prefix : '').'</code><value><![CDATA[' . $vall . ']]></value></attribute>';
                        } else {
                            $xml_lf[$id_cat . $l_iso] .= $this->getDeepTagName($xml_name_l[$idl] . $lang_prefix.$prefixOpen) . '<![CDATA[' . $vall . ']]>' . $this->getDeepTagName($xml_name_l[$idl] . $lang_prefix, true);
                        }
                    }

                    if ($mode == 'r') {
                        $xml_lf[$id_cat.$l_iso] = '<Description><Language>'.$l_iso.'</Language>'.$xml_lf[$id_cat.$l_iso].'</Description>';
                    }
                }

                $all_l_iso = array_unique($all_l_iso);
                $this->langIdAll = $all_l_iso;
            }
        }

        $this->attributesGroupsAll = AttributeGroupCore::getAttributesGroups($this->langId);

        //Images
        $image_class_name = 'Image';

        if (_PS_VERSION_ < '1.5') {
            $use_ps_images_class = false;
            $image_class_name = 'ImageCore';

            if (!class_exists($image_class_name, false)) {
                $image_class_name = 'Image';
            }

            $img_class = new $image_class_name();

            if (method_exists($img_class, 'getExistingImgPath')) {
                $use_ps_images_class = true;
            }
        } else {
            $use_ps_images_class = true;
        }

        $img_name_extra = false;

        if (_PS_VERSION_ >= '1.5.1' && _PS_VERSION_ < '1.3') {
            $img_name_extra = '_default';
        }

        $img = Db::getInstance()->ExecuteS('SELECT `name`, `title_xml`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "img_blmod" AND status = "1"');

        if (empty($allImages)) {
            $img_cover = Db::getInstance()->ExecuteS('SELECT `id_image`, `id_product`
                FROM '._DB_PREFIX_.'image
                WHERE cover = 1');

            foreach ($img_cover as $c) {
                $cover_i[$c['id_product']] = $c['id_image'];
            }
        }

        $base_dir_img = _PS_BASE_URL_.__PS_BASE_URI__.'img/p/';

        if ($mode == 'wum') {
            $features = $this->getAllAttributes($id_lang);
            $xml .= '<features>';

            foreach ($features as $f) {
                $this->featuresKeyByName[$f['name']] = $f['id_attribute'];
                $xml .= '<feature id="'.$f['id_attribute'].'">'.$this->settings['pref_s'].$f['name'].$this->settings['pref_e'].'</feature>';
            }

            $xml .= '</features>';
            $manufacturers = Manufacturer::getManufacturers(false, $id_lang);
            $xml .= '<brands>';

            foreach ($manufacturers as $m) {
                $xml .= '<brand id="'.$m['id_manufacturer'].'">'.$this->settings['pref_s'].$m['name'].$this->settings['pref_e'].'</brand>';
            }

            $xml .= '</brands>';
        }

        if ($mode == 'dm' || $mode == 'ppa') {
            $xml .= '<created_at>'.date('Y-m-d H:i').'</created_at>';
        }

        if ($mode == 'sfl') {
            $xml .= '<created_at>'.date('Y-m-d H:i:s').'</created_at>';
        }

        if ($mode == 'ro') {
            $categoriesAll = $this->getAllCategories($l, $multistoreId);
            $xml .= '<categories>';

            foreach ($categoriesAll as $cat) {
                $xml .= '<category id="'.$cat['id_category'].'">'.$this->settings['pref_s'].$cat['name'].$this->settings['pref_e'].'</category>';
            }

            $xml .= '</categories>';
        }

        if ($mode == 'ho') {
            $categoriesAll = $this->getAllCategories($l, $multistoreId);
            $xml .= '<categories>';

            foreach ($categoriesAll as $cat) {
                $xml .= '<category><id>'.$cat['id_category'].'</id><name>'.$this->settings['pref_s'].$cat['name'].$this->settings['pref_e'].'</name></category>';
            }

            $xml .= '</categories>';
        }

        if ($mode == 'cgr') {
            $xml .= ' <lastupdate>'.str_replace(' ', 'T', date('Y-m-d H:i:s')).'Z</lastupdate>';
        }

        $this->settings['currencyIso'] = '';
        $this->settings['currencyIdConvert'] = !empty($currencyId) ? $currencyId : false;
        $currencyId = !empty($currencyId) ? $currencyId : Configuration::get('PS_CURRENCY_DEFAULT');
        $feedCurrency = '';

        if (!empty($currencyId)) {
            $currencyClass = Currency::getCurrency($currencyId);
            $feedCurrency = ' '.$currencyClass['iso_code'];
        }

        if (!empty($price_with_currency) && !empty($feedCurrency)) {
            $this->settings['currencyIso'] = $feedCurrency;
        }

        if (!empty($block_status['file-name'])) {
            $fileNameParam = '';

            if ($mode == 'sez') {
                $fileNameParam = ' xmlns="http://www.zbozi.cz/ns/offer/1.0"';
            }

            if ($mode == 'ceo') {
                $fileNameParam = ' name="other"';
            }

            if ($mode == 'tt') {
                $fileNameParam = ' version="1.0" timestamp="'.date('Ymd:H:i:s').'"';
            }

            if ($mode == 'bee') {
                $fileNameParam = ' currency = "'.trim($feedCurrency).'"'.(!empty($l_iso) ? ' lang="'.$l_iso.'"' : '');
            }

            $xml .= '<' . $block_name['file-name'] .$fileNameParam. '>';
        }

        if (!empty($feedGenerationTime) && !empty($feedGenerationTimeName) && $settings['feed_mode_real'] != 'pub') {
            $xml .= '<'.$feedGenerationTimeName.'>'.date('Y-m-d H:i:s').'</'.$feedGenerationTimeName.'>';
        }

        $xml .= $extra_feed_row;

        if (!empty($currentChunkNo) && $currentChunkNo > 1) {
            $xml = '';
        }

        //Get attributes
        $extra_attributes = Db::getInstance()->ExecuteS('SELECT `name`, `title_xml`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND `table` = "bl_extra_att" AND status = "1"');

        //Feature
        $featureEnable = false;
        $fieldFeature = array();
        $fieldGroupedAttributes = array();

        $extraFieldFeature = Db::getInstance()->ExecuteS('SELECT `name`, `title_xml`, `table`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$id.'" AND `type` = "'.pSQL($this->settings['feed_type_sql']).'" AND (`table` = "bl_extra_feature" OR `table` = "bl_extra_attribute_group") 
            AND status = "1"');

        if (!empty($extraFieldFeature)) {
            foreach ($extraFieldFeature as $f) {
                if ($f['table'] == 'bl_extra_feature') {
                    $fieldFeature[$f['name']] = $f;
                } elseif ($f['table'] == 'bl_extra_attribute_group') {
                    $fieldGroupedAttributes[$f['name']] = $f;
                }
            }
        }

        if (method_exists('Product', 'getFrontFeaturesStatic')) {
            $featureEnable = true;
        }

        $configuration = Configuration::getMultiple(
            [
                'PS_LANG_DEFAULT',
                'PS_SHIPPING_FREE_PRICE',
                'PS_SHIPPING_HANDLING',
                'PS_SHIPPING_METHOD',
                'PS_SHIPPING_FREE_WEIGHT',
                'PS_CARRIER_DEFAULT',
                'PS_COUNTRY_DEFAULT',
                'PS_ORDER_OUT_OF_STOCK',
            ]
        );

        $this->settings['configuration'] = $configuration;

        $configurationLang = Configuration::getMultiple(
            [
                'PS_LABEL_DELIVERY_TIME_AVAILABLE',
                'PS_LABEL_DELIVERY_TIME_OOSBOA',
                'PS_LABEL_IN_STOCK_PRODUCTS',
                'PS_LABEL_OOS_PRODUCTS_BOA',
                'PS_LABEL_OOS_PRODUCTS_BOD',
            ],
            $this->langId
        );

        $carrierIdDefault = $configuration['PS_CARRIER_DEFAULT'];

        if (!empty($this->settings['shipping_price_mode']) && $this->settings['shipping_price_mode'] != 99999) {
            $carrierByReference = Carrier::getCarrierByReference($this->settings['shipping_price_mode']);
            $carrierIdDefault = !empty($carrierByReference->id) ? $carrierByReference->id : 0;
        }

        $this->settings['configurationLang'] = $configurationLang;

        $shippingCountry = !empty($this->settings['shipping_countries'][0]) ? $this->settings['shipping_countries'][0] : $configuration['PS_COUNTRY_DEFAULT'];

        $feedShippingPrice->setData($id_lang, $this->settings, $configuration, $multistoreId);
        $feedShippingPrice->loadCountries($carrierIdDefault);

        $defaultCountry = new Country($shippingCountry, $id_lang);

        $address = new Address();
        $address->id_country = $shippingCountry;
        $address->id_state = 0;
        $address->postcode = 0;
        //END Shipping parameter

        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');

        $xmlProductMruAll = '';
        $mruProductFields = array(
            'product_categories_tree',
            'id_category_all',
            'product_url_blmod',
        );

        if (class_exists('PrestaShop\PrestaShop\Adapter\Configuration', false)) {
            $configurationAdapter = new PrestaShop\PrestaShop\Adapter\Configuration;
            $PS_LABEL_OOS_PRODUCTS_BOA_LIST = $configurationAdapter->get('PS_LABEL_OOS_PRODUCTS_BOA');
            $this->PS_LABEL_OOS_PRODUCTS_BOA = !empty($PS_LABEL_OOS_PRODUCTS_BOA_LIST[$this->langId]) ? $PS_LABEL_OOS_PRODUCTS_BOA_LIST[$this->langId] : '';
        }

        $this->availabilityLabel->setSettings($this->settings);
        $mergeAttributesByGroup->setSettings($this->settings);

        foreach ($xml_d as $xdd) {
            if (!empty($allImages)) {
                $img_all_images = Db::getInstance()->ExecuteS('SELECT `id_image`, `id_product`
                    FROM '._DB_PREFIX_.'image
                    WHERE id_product = "'.(int)$xdd['pro_id'].'"
                    ORDER BY `cover` DESC, `position` ASC');
            } else {
                $img_all_images[0]['id_image'] = isset($cover_i[$xdd['pro_id']]) ? $cover_i[$xdd['pro_id']] : false;
            }

            if (!empty($settings['filter_image']) && empty($settings['split_by_combination'])) {
                if ($settings['filter_image'] == 1) {
                    if (empty($img_all_images[0]['id_image'])) {
                        continue;
                    }
                }

                if ($settings['filter_image'] == 2) {
                    if (!empty($img_all_images[0]['id_image'])) {
                        continue;
                    }
                }
            }

            $product_class = new Product($xdd['pro_id'], false, $id_lang);
            $productQty = (int)$product_class->getQuantity($xdd['pro_id']);
            $this->availabilityLabel->setIsAvailableWhenOutOfStock(StockAvailable::outOfStock($xdd['pro_id']));

            if (empty($this->settings['price_rounding_type'])) {
                $salePrice = Tools::ps_round($product_class->getPriceStatic($xdd['pro_id'], true, null), 2);
            } else {
                $salePrice = $product_class->getPriceStatic($xdd['pro_id'], true, null, 2);
            }

            $basePrice = $product_class->price;
            $wholesalePrice = $product_class->wholesale_price;
            $taxRate = $this->getTaxRation($product_class, $address, $context);
            $combinations = [];

            if (!empty($splitByCombination)) {
                $combinations = $productCombination->getCombinations($product_class, $id_lang);
            }

            if (!empty($settings['filter_image']) && empty($combinations)) {
                if ($settings['filter_image'] == 1) {
                    if (empty($img_all_images[0]['id_image'])) {
                        continue;
                    }
                }

                if ($settings['filter_image'] == 2) {
                    if (!empty($img_all_images[0]['id_image'])) {
                        continue;
                    }
                }
            }

            if (empty($combinations) && !empty($permissions['filter_exclude_empty_params'])) {
                foreach ($permissions['filter_exclude_empty_params'] as $emptyParamKEy) {
                    if (empty($xdd['blmod_'.$emptyParamKEy])) {
                        continue 2;
                    }
                }
            }

            $shippingPriceAll = $feedShippingPrice->getPrice($product_class, $salePrice);
            $shippingPrice = 0;

            foreach ($shippingPriceAll as $spa) {
                $shippingPrice = $spa;
                break;
            }

            $shippingPrice = $this->getPriceFormat($feedPrice->getEditedPrice($shippingPrice, 'shipping_price', $this->settings));
            $priceWithoutDiscount = $product_class->getPriceStatic($xdd['pro_id'], true, null, 2, null, false, false);
            $combinationDefault = [];
            $this->productParam['shipping_price'][$xdd['pro_id']] = $shippingPrice;
            $this->productParam['reference'][$xdd['pro_id']] = $xdd['blmod_reference'];
            $this->productParam['ean13'][$xdd['pro_id']] = $xdd['blmod_ean13'];
            $this->productParam['isbn'][$xdd['pro_id']] = isset($xdd['blmod_isbn']) ? $xdd['blmod_isbn'] : '';
            $this->productParam['manufacturer'][$xdd['pro_id']] = $xdd['blmod_manufacturer'];
            $this->productParam['sale_price'][$xdd['pro_id']] = $salePrice;
            $this->productParam['category'][$xdd['pro_id']] = !empty($categoriesByKey[$xdd['blmod_cat_id']]) ? $categoriesByKey[$xdd['blmod_cat_id']] : '';
            $this->productParam['category_id'][$xdd['pro_id']] = $xdd['blmod_cat_id'];
            $this->productParam['tax_rate'][$xdd['pro_id']] = $taxRate;
            $availabilityName = $this->getAvailabilityByMode($product_class, $feedSettings, $configurationLang);

            if ($filterDiscount == 1 && number_format($salePrice, 2, '.', '') >= number_format($priceWithoutDiscount, 2, '.', '')) {
                continue;
            }

            if ($filterDiscount == 2 && number_format($salePrice, 2, '.', '') != number_format($priceWithoutDiscount, 2, '.', '')) {
                continue;
            }

            if ((!empty($priceFrom) && $salePrice < $priceFrom) || (!empty($priceTo) && $salePrice > $priceTo)) {
                continue;
            }

            if (!empty($settings['filter_qty_status'])) {
                $onlyInStock = true;

                if ($settings['filter_qty_type'] == '>' && $productQty < $settings['filter_qty_value']) {
                    continue;
                } elseif ($settings['filter_qty_type'] == '<' && $productQty >= $settings['filter_qty_value']) {
                    continue;
                } elseif ($settings['filter_qty_type'] == '=' && $productQty != $settings['filter_qty_value']) {
                    continue;
                }
            }

            if (!empty($settings['only_available_for_order'])) {
                $outOfStockStatus = StockAvailable::outOfStock($xdd['pro_id']);

                if ($productQty < 1 && $outOfStockStatus == 0) {
                    continue;
                }

                if ($productQty < 1 && $outOfStockStatus == 2 && $configuration['PS_ORDER_OUT_OF_STOCK'] == 0) {
                    continue;
                }
            }

            $this->loadProductFeatures($id_lang, $xdd['pro_id'], $multistoreId);

            if (!$filterByFeature->isExists($this->settings, $this->productFeaturesWithId)) {
                continue;
            }

            if (!$filterByFeature->isNotExists($this->settings, $this->productFeaturesWithId)) {
                continue;
            }

            $catBlockParam = '';

            if ($mode == 'k24' || $mode == 'kos' || $mode == 'plt') {
                $catBlockParam = ' id="'.REPLACE_COMBINATION.'product_id_element"';
            }

            $xmlProductMru = '<product>';
            $xmlProduct = !empty($this->settings['item_starts_on_a_new_line']) ? PHP_EOL : '';
            $xmlProduct .= '<'.$block_name['cat-block-name'].$catBlockParam.(($mode == 'tt' || $mode == 'ep' || $mode == 'ro' || $mode == 'sfl' || $mode == 'ua') ? ' '.($mode == 'sfl' ? 'cnt' : 'id').'="'.REPLACE_COMBINATION.'id_product'.'"' : '').(($mode == 'ep' || $mode == 'ro') ? ' available="true"' : '').REPLACE_COMBINATION.'cat-block-name>';

            if (!empty($customXmlTagByProductId[$xdd['pro_id']])) {
                foreach ($customXmlTagByProductId[$xdd['pro_id']] as $i) {
                    $xmlProduct .= $customXmlTagsByProductList[$i];
                }
            }

            if (!empty($feedSettings['gender_field_category_status']) && !empty($feedSettings['gender_field_category_name'])) {
                $xmlProduct .= '<'.$feedSettings['gender_field_category_name'].'>'.(!empty($genderCategories[$xdd['blmod_cat_id']]) ? $genderCategories[$xdd['blmod_cat_id']] : $feedSettings['gender_field_category_prime_value']).'</'.$feedSettings['gender_field_category_name'].'>';
            }

            if ($mode == 'twi') {
                $xmlProduct .= '<sku>offre'.REPLACE_COMBINATION.'product_id_element'.'</sku>';
            }

            $settings['vivino_bottle_size'] = !empty($settings['vivino_bottle_size']) ? $settings['vivino_bottle_size'] : 'vi_default_'.$settings['vivino_bottle_size_default'];
            $settings['vivino_lot_size'] = !empty($settings['vivino_lot_size']) ? $settings['vivino_lot_size'] : 'vi_default_'.$settings['vivino_lot_size_default'];

            $this->productFeatures[$settings['vivino_bottle_size']] = !empty($this->productFeatures[$settings['vivino_bottle_size']]) ? $this->productFeatures[$settings['vivino_bottle_size']] : $settings['vivino_bottle_size_default'];
            $this->productFeatures[$settings['vivino_lot_size']] = !empty($this->productFeatures[$settings['vivino_lot_size']]) ? $this->productFeatures[$settings['vivino_lot_size']] : $settings['vivino_lot_size_default'];

            if (!empty($this->productFeatures[$settings['vivino_bottle_size']])) {
                $xmlProduct .= '<bottle_size>'.$this->productFeatures[$settings['vivino_bottle_size']].'</bottle_size>';
            }

            if (!empty($this->productFeatures[$settings['vivino_lot_size']])) {
                $xmlProduct .= '<bottle_quantity>'.$this->productFeatures[$settings['vivino_lot_size']].'</bottle_quantity>';
            }

            if ($mode == 'wum') {
                $categoriesOfProductsUsed[] = $xdd['blmod_cat_id'];
                $xmlProduct = '<'.$block_name['cat-block-name'].$catBlockParam.' id="'.REPLACE_COMBINATION.'product_id_element" brand_id="'.$product_class->id_manufacturer.'" category_id="'.$xdd['blmod_cat_id'].'">';
            }

            if ($mode == 'pub') {
                $xmlProduct .= '<product-id-type>SHOP_SKU</product-id-type>';
            }

            if (!empty($block_name['extra-product-rows'])) {
                if ($mode == 'pub') {
                    $xmlProductMru .= htmlspecialchars_decode($block_name['extra-product-rows'], ENT_QUOTES);
                    $xmlProduct .= htmlspecialchars_decode($block_name['extra-offer-rows'], ENT_QUOTES);
                } else {
                    $xmlProduct .= htmlspecialchars_decode($block_name['extra-product-rows'], ENT_QUOTES);
                }
            }

            $isAvailableWhenOutOfStock = (!empty($xdd['product_available_for_order']) || !empty($this->settings['is_enabled_field']['availability_label'])) ? Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($product_class->id)) : true;
            $this->settings['isAvailableWhenOutOfStock'] = $isAvailableWhenOutOfStock;
            $paramsCeneo = [];

            foreach ($xdd as $id => $val) {
                if ($id == 'pro_id' || $id == 'blmod_cat_id' || $id == 'blmod_price' || $id == 'bl_extra_att'
                    || $id == 'blmod_reference' || $id == 'blmod_ean13' || $id == 'blmod_isbn' || $id == 'blmod_manufacturer'
                    || $id == 'blmod_upc' || $id == 'minqc_qty') {
                    continue;
                }

                if ($id == 'product_quantity') {
                    $val = $productQty;
                }

                if ($id == 'product_id_category_default') {
                    if (!empty($googleCatMap[$val])) {
                        $val = $googleCatMap[$val]['id'];
                    }
                }

                $val = isset($val) ? $val : '';

                if ($id == 'product_condition' && $mode == 'cgr') {
                    $condition = [
                        'new' => 'Καινούριο',
                        'used' => 'Μεταχειρισμένο',
                        'refurbished' => 'Ανακατασκευή',
                    ];

                    $val = Tools::strtolower($val);
                    $val = !empty($condition[$val]) ? $condition[$val] : $val;
                }

                if ($id == 'product_available_for_order') {
                    $val = $availabilityName['out'];

                    if ($product_class->available_for_order == 1 || $product_class->online_only == 1) {
                        if ($productQty > 0) {
                            $val = $availabilityName['in'];
                        } else {
                            if ($isAvailableWhenOutOfStock) {
                                $val = !empty($availabilityName['on_demand']) ? $availabilityName['on_demand'] : $availabilityName['in'];
                            }
                        }
                    }
                }

                if ($id == 'product_price' || $id == 'product_wholesale_price' || $id == 'product_ecotax') {
                    $val = $feedPrice->getEditedPrice($val, $id, $this->settings);
                }

                if ($id == 'product_price' && $mode == 'h') {
                    $val = (int)($val * 100);
                }

                if ($id == 'product_weight' && $mode == 'r') {
                    $val = (int)($val * 1000);
                }

                if ($id == 'product_reference' || $id == 'product_supplier_reference' || $id == 'product_quantity' ||
                    $id == 'product_ean13' || $id == 'product_id_product' || $id == 'product_available_for_order' ||
                    $id == 'product_price' || $id == 'product_weight' || $id == 'product_isbn' || $id == 'product_wholesale_price' ||
                    $id == 'product_minimal_quantity' || $id == 'product_upc' || $id == 'product_mpn' || $id == 'product_location') {
                    $valDefault = $val;
                    $val = REPLACE_COMBINATION.str_replace('product_', '', $id);
                    $combinationDefault[str_replace('product_', '', $id)] = $valDefault;
                }

                if ($id == 'product_price' && $mode == 'r') {
                    $xmlProduct .= '<Price><Currency>'.trim($feedCurrency).'</Currency><VATRate>22</VATRate>';
                }

                if (($mode == 's' || $mode == 'g' || $mode == 'f' || $mode == 'y' || $mode == 't' || $mode == 'pint'
                    || $mode == 'lw' || $mode == 'cj' || $mode == 'fav') && $id == 'product_weight'
                    || $mode == 'tc' || $mode == 'lyst' || $mode == 'wb' || $mode == 'ikx') {
                    $val .= ' '.$weightUnit;
                }

                if ($id == 'product_date_upd' && $mode == 'm') {
                    $val = date('Y-m-d', strtotime($val));
                }

                if ($id == 'manufacturer_name' && $mode == 'mm') {
                    $xmlProduct .= '<manufacturer>'.$this->settings['pref_s'].$val.$this->settings['pref_e'].'</manufacturer>';
                }

                if ($mode == 'pub' && ($id == 'product_reference' || $id == 'product_ean13' || $id == 'manufacturer_name' || $id == 'product_id_product')) {
                    $xmlProductMru .= '<attribute><code>'.$xml_name[$id].'</code><value>'.$this->settings['pref_s'].$val.$this->settings['pref_e'].'</value></attribute>';
                }

                if ($mode == 'pub' && ($id == 'manufacturer_name' || $id == 'product_ean13')) {
                    continue;
                }

                if ($id == 'blmod_custom_table_0' && !empty($this->settings['connected_value_prefix'])) {
                    $val = !empty($val) ? $val : $this->settings['connected_value_prefix'].$xdd['pro_id'];
                }

                if ($mode == 'ceo' &&
                    in_array(
                        $id,
                        array(
                            'manufacturer_name',
                            'product_id_product',
                            'product_quantity',
                            'product_available_for_order',
                            'product_ean13',
                            'product_reference',
                            'product_isbn',
                            'product_weight',
                            'product_upc',
                        )
                    )) {
                    if (in_array($id, ['manufacturer_name', 'product_ean13', 'product_reference', 'product_isbn', 'product_weight', 'product_upc',])) {
                        $paramsCeneo[$xml_name[$id]] = $val;
                    }

                    continue;
                }

                $skipCdata = in_array($id, ['product_ean13',]);
                $cdataS = $this->settings['pref_s'];
                $cdataE = $this->settings['pref_e'];

                if (!empty($skipCdata)) {
                    $cdataS = '';
                    $cdataE = '';
                }

                $xmlProduct .= $this->getDeepTagName($xml_name[$id]).$cdataS.$val.$cdataE.$this->getDeepTagName($xml_name[$id], true);

                if ($id == 'product_price' && $mode == 'r') {
                    $xmlProduct .= '</Price>';
                }
            }

            /*
            if ($mode == 'pub' && !in_array($id, ['product_reference', 'product_ean13', 'manufacturer_name', 'product_id_product',])) {
                $xmlProductMru .= '<attribute><code>'.$xml_name[$id].'</code><value>'.$this->settings['pref_s'].$val.$this->settings['pref_e'].'</value></attribute>';
            }
            */

            if ($mode == 'g' || $mode == 'f' || $mode == 'y' || $mode == 't' || $mode == 'pint' || $mode == 'cj' || $mode == 'fav'
                || $mode == 'tc' || $mode == 'lyst' || $mode == 'wb' || $mode == 'ikx') {
                if ((empty($xdd['manufacturer_name']) && empty($xdd['product_ean13'])) || (empty($xdd['manufacturer_name']) && empty($xdd['product_reference']))) {
                    $xmlProduct .= '<g:identifier_exists>no</g:identifier_exists>';
                }
            }

            $id_cat = $xdd['pro_id'];
            $def_cat = isset($xdd['blmod_cat_id']) ? $xdd['blmod_cat_id'] : false;

            $this->productLangValues = $xml_lf;
            $allowLangByFeedType = !($mode == 'spa' && count($this->langIdWitIso) > 1);

            if (!empty($xml_lf) && $allowLangByFeedType) {
                foreach ($all_l_iso as $iso) {
                    $xml_lf[$id_cat.$iso] = isset($xml_lf[$id_cat.$iso]) ? $xml_lf[$id_cat.$iso] : false;

                    if ($mode == 'pub') {
                        $xmlProductMru .= $xml_lf[$id_cat . $iso];
                    } else {
                        $xmlProduct .= $xml_lf[$id_cat . $iso];
                    }
                }
            }

            $this->productAttributeAndFeatureName = [];
            $xmlImages = array();
            $xmlImagesUrl = array();
            $imageNumber = 0;
            $imageNumberReal = 0;

            if (!empty($img) && !empty($img_all_images[0]['id_image'])) {
                if (empty($one_branch)) {
                    $xmlProduct .= $this->getDeepTagName($block_name['img-block-name']);
                }

                if ($use_ps_images_class) {
                    if ($mode != 'pub') {
                        $xmlProduct .= REPLACE_COMBINATION . 'image';
                    }

                    foreach ($img as $i) {
                        foreach ($img_all_images as $all_img) {
                            $image_info['id_image'] = $all_img['id_image'];
                            $image_info['id_product'] = $xdd['pro_id'];

                            $link = new Link();
                            $img_dir_server = $link->getImageLink($product_class->link_rewrite, $image_info['id_product'].'-'.$image_info['id_image'], $i['name'].$img_name_extra);

                            if (!empty($img_dir_server) && Tools::substr($img_dir_server, 0, 4) != 'http') {
                                $img_dir_server = $url_type.$img_dir_server;
                            }

                            /**
                             * @var ImageCore
                             */
                            $img_class = new $image_class_name($image_info['id_image']);
                            $img_class->id = $image_info['id_image'];
                            $img_dir_file = _PS_PROD_IMG_DIR_.$img_class->getExistingImgPath().'-'.$i['name'].'.jpg';

                            if (file_exists($img_dir_file)) {
                                $imageNumberReal++;
                                if (empty($xmlImages[$image_info['id_image']])) {
                                    $xmlImages[$image_info['id_image']] = '';
                                }

                                $imageNumber = ($mode == 'h' || $mode == 'spa') ? $imageNumber+1 : '';

                                if ($imageNumberReal > 1 && ($mode == 'g' || $mode == 'f' || $mode == 'y' || $mode == 't'
                                    || $mode == 'pint' || $mode == 'cj' || $mode == 'fav'
                                    || $mode == 'tc' || $mode == 'lyst' || $mode == 'wb' || $mode == 'ikx')) {
                                    $i['title_xml'] = 'g:additional_image_link';
                                }

                                $imgDirServerClean = $img_dir_server;

                                if ($mode == 'a') {
                                    $img_dir_server = '<admarkt:image url="'.$img_dir_server.'"/>';
                                } else {
                                    $img_dir_server = $this->settings['pref_s'].$img_dir_server.$this->settings['pref_e'];
                                }

                                if (($mode == 'sn' || $mode == 'ma') && $imageNumberReal > 1) {
                                    $i['title_xml'] = 'additional_image_link';
                                }

                                if (($mode == 's' || $mode == 'bp' || $mode == 'dm') && $imageNumberReal > 1) {
                                    $i['title_xml'] = 'additional_imageurl';
                                }

                                if ($mode == 'mala' || $mode == 'kog' || $mode == 'mir') {
                                    $xmlImages[$image_info['id_image']] .= $imgDirServerClean;
                                } else {
                                    $xmlImages[$image_info['id_image']] .= $this->getDeepTagName($i['title_xml'] . $imageNumber) . $img_dir_server . $this->getDeepTagName($i['title_xml'] . $imageNumber, true);
                                }

                                $xmlImagesUrl[] = $imgDirServerClean;
                            }
                        }
                    }
                } else {
                    foreach ($img as $i) {
                        foreach ($img_all_images as $all_img) {
                            $img_dir_file = $xdd['pro_id'] . '-' . $all_img['id_image'] . '-' . $i['name'] . '.jpg';

                            if (file_exists('img/p/' . $img_dir_file)) {
                                $img_dir = $base_dir_img . $img_dir_file;
                                $xmlProduct .= $this->getDeepTagName($i['title_xml']) . $this->settings['pref_s'] . $img_dir . $this->settings['pref_e'] . $this->getDeepTagName($i['title_xml'], true);
                            }
                        }
                    }
                }

                if (empty($one_branch)) {
                    $xmlProduct .= $this->getDeepTagName($block_name['img-block-name'], true);
                }
            }

            if (!empty($xml_cat_name)) {
                if ((empty($one_branch) && $count_lang > 1) || $mode == 'x' || $mode == 'o') {
                    $xmlProduct .= '<'.$block_name['def_cat-block-name'].'>';
                }

                if ($mode == 'pub') {
                    $xmlProductMru .= '<attribute><code>product-category</code><value>'.(isset($xml_cat_name[$def_cat]) ? $xml_cat_name[$def_cat] : '').'</value></attribute>';
                } else {
                    $xmlProduct .= isset($xml_cat_name[$def_cat]) ? $xml_cat_name[$def_cat] : '';
                }

                if ((empty($one_branch) && $count_lang > 1) || $mode == 'x' || $mode == 'o') {
                    $xmlProduct .= '</'.$block_name['def_cat-block-name'].'>';
                }
            }

            $addMultipleShippingPrice = false;
            $addMultipleShippingCountriesCode = false;
            $shippingPriceFieldName = '';
            $shippingCountryFieldName = '';
            $ignoreExtraTagCdata = [
                'names_of_all_categories',
                'related_products',
                'attached_files',
                'virtual_products',
            ];

            if (!empty($extra_field)) {
                $unitPriceRatio = $product_class->unit_price_ratio;

                if (empty($product_class->unit_price_ratio) || $product_class->unit_price_ratio < 0.00001) {
                    $unitPriceRatio = 1;
                }

                foreach ($extra_field as $b_e) {
                    $extraTag = '';
                    $extraTagVal = '';

                    if ($b_e['name'] == 'product_url_utm_blmod') {
                        continue;
                    }

                    if (count($shippingPriceAll) > 1) {
                        if ($b_e['name'] == 'price_shipping_blmod') {
                            $shippingPriceFieldName = $b_e['title_xml'];
                            $addMultipleShippingPrice = true;
                            continue;
                        }

                        if ($b_e['name'] == 'shipping_country_code') {
                            $shippingCountryFieldName = $b_e['title_xml'];
                            $addMultipleShippingCountriesCode = true;
                            continue;
                        }
                    }

                    if ($mode == 'r' && $b_e['name'] == 'price_sale_blmod') {
                        $extraTag .= '<Price><Currency>'.trim($feedCurrency).'</Currency>';
                    }

                    $extraTag .= $this->getDeepTagName($b_e['title_xml']).(in_array($b_e['name'], $ignoreExtraTagCdata) ? '' : $this->settings['pref_s']);

                    if ($b_e['name'] == 'price_shipping_blmod') {
                        $extraTag .= $shippingPrice;
                    } elseif ($b_e['name'] == 'price_sale_blmod') {
                        $extraTag .= REPLACE_COMBINATION.'sale_blmod';
                        $combinationDefault['sale_blmod'] = $salePrice;
                    } elseif ($b_e['name'] == 'price_sale_with_min_qty_blmod') {
                        $extraTag .= REPLACE_COMBINATION.'sale_with_min_qty_blmod';
                        $combinationDefault['sale_with_min_qty_blmod'] = 1;
                    } elseif ($b_e['name'] == 'price_sale_tax_excl_blmod') {
                        $extraTag .= REPLACE_COMBINATION.'sale_tax_excl_blmod';
                        $combinationDefault['sale_tax_excl_blmod'] = $salePrice;
                    } elseif ($b_e['name'] == 'price_wt_discount_blmod') {
                        $extraTag .= REPLACE_COMBINATION.'price_wt_discount_blmod';
                        $combinationDefault['price_wt_discount_blmod'] = $priceWithoutDiscount;
                    } elseif ($b_e['name'] == 'only_discount_blmod') {
                        $extraTag .= $this->getPriceFormat($priceWithoutDiscount - $salePrice);
                    } elseif ($b_e['name'] == 'discount_rate_blmod') {
                        if (!empty($priceWithoutDiscount)) {
                            $extraTag .= round((($priceWithoutDiscount - $salePrice) / $priceWithoutDiscount * 100), 0);
                        } else {
                            $extraTag .= 0;
                        }
                    } elseif ($b_e['name'] == 'product_url_blmod') {
                        $extraTagVal = REPLACE_COMBINATION.'url';
                        $extraTag .= $extraTagVal;
                        $extraUrl = !empty($this->extraFieldByName['product_url_utm_blmod']) ? htmlspecialchars_decode($this->extraFieldByName['product_url_utm_blmod'], ENT_QUOTES) : '';
                        $combinationDefault['additional_url'] = $link_class->getProductLink($product_class, null, null, null, $id_lang);
                        $combinationDefault['url'] = $combinationDefault['additional_url'].$extraUrl;
                    } elseif ($b_e['name'] == 'additional_product_url_blmod') {
                        $extraTag .= REPLACE_COMBINATION.'additional_url';
                    } elseif ($b_e['name'] == 'names_of_all_categories') {
                        $categoriesNames = explode(',|||,', $this->getProductCategories($xdd['pro_id'], $id_lang, 0));
                        $extraTagVal = '';

                        foreach ($categoriesNames as $nameNo => $catName) {
                            $catNameFinal = ($mode == 'ppa') ? 'category' : 'sub_cat_'.($nameNo+1);
                            $extraTagVal .= '<'.$catNameFinal.'>'.$this->settings['pref_s'].$catName.$this->settings['pref_e'].'</'.$catNameFinal.'>';
                        }

                        $extraTag .= $extraTagVal;
                    } elseif ($b_e['name'] == 'product_categories_tree') {
                        $extraTagVal = $this->getProductCategories($xdd['pro_id'], $id_lang, $def_cat);
                        $extraTag .= $extraTagVal;
                    } elseif ($b_e['name'] == 'id_category_all') {
                        $extraTag .= $this->getProductCategories($xdd['pro_id'], $id_lang, $def_cat, true);
                    } elseif ($b_e['name'] == 'category_url') {
                        $extraTag .= !empty($def_cat) ? $link_class->getCategoryLink($def_cat, null, $id_lang) : '';
                    } elseif ($b_e['name'] == 'unit') {
                        $extraTag .= $product_class->unity;
                    } elseif ($b_e['name'] == 'unit_price') {
                        if (!empty($product_class->unity)) {
                            $extraTag .= $this->getPriceFormat($product_class->getPriceStatic($xdd['pro_id'], true, null) / $unitPriceRatio).(empty($this->settings['unit_price_without_unit']) ? ' / '.$product_class->unity : '');
                        } else {
                            $extraTag .= '';
                        }
                    } elseif ($b_e['name'] == 'unit_price_e_tax') {
                        if (!empty($product_class->unity)) {
                            $extraTag .= $this->getPriceFormat($product_class->price / $unitPriceRatio).(empty($this->settings['unit_price_without_unit']) ? ' / '.$product_class->unity : '');
                        } else {
                            $extraTag .= '';
                        }
                    } elseif ($b_e['name'] == 'tax_rate') {
                        $extraTag .= $taxRate;
                    } elseif ($b_e['name'] == 'parent_id_product') {
                        $extraTag .= $xdd['pro_id'];
                    } elseif ($b_e['name'] == 'additional_id_product') {
                        $extraTag .= $xdd['pro_id'];
                    } elseif ($b_e['name'] == 'additional_reference') {
                        $extraTag .= REPLACE_COMBINATION.'additional_reference';
                    } elseif ($b_e['name'] == 'parent_reference') {
                        $extraTag .= $xdd['blmod_reference'];
                    } elseif ($b_e['name'] == 'additional_id_combination') {
                        $extraTag .= REPLACE_COMBINATION.'additional_id_combination';
                    } elseif ($b_e['name'] == 'stock_status') {
                        $extraTag .= REPLACE_COMBINATION.'stock_status';
                        $combinationDefault['stock_status'] = $productQty > 0 ? 'Y' : 'N';
                    } elseif ($b_e['name'] == 'shipping_country_code') {
                        $extraTag .= !empty($defaultCountry->iso_code) ? $defaultCountry->iso_code : '';
                    } elseif ($b_e['name'] == 'shipping_country') {
                        $extraTag .= !empty($defaultCountry->name) ? $defaultCountry->name : '';
                    } elseif ($b_e['name'] == 'product_tags') {
                        $tag = new Tag();
                        $tagsByLanguage = $tag->getProductTags($xdd['pro_id']);
                        $extraTag .= '';

                        if (!empty($tagsByLanguage[$id_lang])) {
                            $extraTag .= implode(',', $tagsByLanguage[$id_lang]);
                        }
                    } elseif ($b_e['name'] == 'days_back_created') {
                        $dateFromInterval = new \DateTime(date('Y-m-d H:i:s'));
                        $dateToInterval = new \DateTime($product_class->date_add);
                        $interval = $dateFromInterval->diff($dateToInterval);
                        $extraTag .= $interval->format('%a');
                    } elseif ($b_e['name'] == 'additional_ean13_with_prefix') {
                        $extraTag .= REPLACE_COMBINATION.'additional_ean13_with_prefix';
                    } elseif ($b_e['name'] == 'related_products') {
                        $relatedProducts = $this->getRelatedProducts($xdd['pro_id']);

                        $extraTag .= '';

                        if (!empty($relatedProducts)) {
                            foreach ($relatedProducts as $r) {
                                $extraTag .= '<ITEM>'.(!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$r['id_product_2'].'</ITEM>';
                            }
                        }
                    } elseif ($b_e['name'] == 'available_date') {
                        $extraTag .= REPLACE_COMBINATION.'available_date';
                    } elseif ($b_e['name'] == 'availability_label') {
                        $extraTag .= REPLACE_COMBINATION.'availability_label';
                        $combinationDefault['availability_label'] = '';
                    } elseif ($b_e['name'] == 'attached_files') {
                        $productAttachments = $product_class->getAttachments($id_lang);
                        $extraTag .= '';

                        if (!empty($productAttachments)) {
                            foreach ($productAttachments as $a) {
                                $extraTag .= '<attachment>
                                    <id_attachment>' . $a['id_attachment'] . '</id_attachment>
                                    <file_name>' . $this->settings['pref_s'] . $a['file_name'] . $this->settings['pref_e'] . '</file_name>
                                    <file_key>' . $a['file'] . '</file_key>
                                    <name>' . $this->settings['pref_s'] . $a['name'] . $this->settings['pref_e'] . '</name>
                                    <description>' . $this->settings['pref_s'] . $a['description'] . $this->settings['pref_e'] . '</description>
                                    <url>' . $this->settings['pref_s'] . $this->settings['url_protocol_without_slash'] . Link::getUrlSmarty(['entity' => 'attachment', 'params' => ['id_attachment' => $a['id_attachment'],]]) . $this->settings['pref_e'] . '</url>
                                    <size>' . $a['file_size'] . '</size>
                                    <mime>' . $a['mime'] . '</mime>
                                </attachment>';
                            }
                        }
                    } elseif ($b_e['name'] == 'virtual_products') {
                        $virtualProducts = $this->getVirtualProductsByProductId($xdd['pro_id']);
                        $extraTag .= '';

                        if (!empty($virtualProducts)) {
                            foreach ($virtualProducts as $v) {
                                $extraTag .= '<virtual_product>
                                    <id_product_download>' . $v['id_product_download'] . '</id_product_download>
                                    <display_filename>' .$this->settings['pref_s'].$v['display_filename'] .$this->settings['pref_e']. '</display_filename>
                                    <filename>' .$this->settings['pref_s'].$v['filename'].$this->settings['pref_e']. '</filename>
                                    <url>' .$this->settings['pref_s'].$this->settings['base_link'].'download/'.$v['filename'].$this->settings['pref_e']. '</url>
                                    <date_add>' . $v['date_add'] . '</date_add>
                                    <date_expiration>' . $v['date_expiration'] . '</date_expiration>
                                    <nb_days_accessible>' . $v['nb_days_accessible'] . '</nb_days_accessible>
                                    <nb_downloadable>' . $v['nb_downloadable'] . '</nb_downloadable>
                                    <active>' . $v['active'] . '</active>
                                    <is_shareable>' . $v['is_shareable'] . '</is_shareable>
                                </virtual_product>';
                            }
                        }
                    } elseif ($b_e['name'] == 'main_category_name_2') {
                        $extraTag .= !empty($this->settings['categories'][$product_class->id_category_default]['name']) ? $this->settings['categories'][$product_class->id_category_default]['name'] : '' ;
                    } elseif ($b_e['name'] == 'all_features') {
                        $list = $this->productFeaturesWithValue;

                        if (!empty($list)) {
                            array_walk($list, function (&$value, $key) {
                                $value = $key . ':' . $value;
                            });
                        }

                        $extraTag .= implode(',', $list);
                    } elseif ($b_e['name'] == 'is_default_combination') {
                        $extraTag .= REPLACE_COMBINATION.'is_default_combination';
                    } elseif ($b_e['name'] == 'price_of_pallet_minqc_blmod') {
                        $extraTag .= $this->getPriceFormat($salePrice) * $xdd['minqc_qty'];
                    } elseif ($b_e['name'] == 'lot_size_minqc_blmod') {
                        $extraTag .= $xdd['minqc_qty'];
                    }

                    $extraTag .= (in_array($b_e['name'], $ignoreExtraTagCdata) ? '' : $this->settings['pref_e']). $this->getDeepTagName($b_e['title_xml'], true);

                    if ($mode == 'ceo' && in_array($b_e['name'], ['product_url_blmod', 'price_sale_blmod',])) {
                        $extraTag = '';
                    }

                    if ($mode == 'pub' && in_array($b_e['name'], $mruProductFields)) {
                        $xmlProductMru .= '<attribute><code>'.$b_e['title_xml'].'</code><value>'.$extraTagVal.'</value></attribute>';
                    } else {
                        $xmlProduct .= $extraTag;
                    }
                }
            }

            if ($addMultipleShippingPrice && $this->settings['feed_mode_final'] == 'g') {
                foreach ($shippingPriceAll as $countryCode => $shippingPrice) {
                    $xmlProduct .= '<g:shipping><g:price>'.$this->getPriceFormat($feedPrice->getEditedPrice($shippingPrice, 'shipping_price', $this->settings)).'</g:price><g:country>'.$countryCode.'</g:country></g:shipping>';
                }
            }

            if ($addMultipleShippingPrice && $this->settings['feed_mode_final'] != 'g') {
                foreach ($shippingPriceAll as $shippingPrice) {
                    $xmlProduct .= '<'.$shippingPriceFieldName.'>'.$this->getPriceFormat($feedPrice->getEditedPrice($shippingPrice, 'shipping_price', $this->settings)).'</'.$shippingPriceFieldName.'>';
                }
            }

            if ($addMultipleShippingCountriesCode && $this->settings['feed_mode_final'] != 'g') {
                foreach ($shippingPriceAll as $shippingCode => $shippingPrice) {
                    $xmlProduct .= '<'.$shippingCountryFieldName.'>'.$this->settings['pref_s'].$shippingCode.$this->settings['pref_e'].'</'.$shippingCountryFieldName.'>';
                }
            }

            $attributesList = [];
            $productAttributes = $product_class->getAttributesGroups($id_lang);
            $this->productAttributes = $productAttributes;

            if ($mode == 'spa') {
                $this->productAttributesAllLanguages = [];

                foreach ($this->langIdWitIso as $langIdFromList => $langIso) {
                    $this->productAttributesAllLanguages[$langIdFromList] = $product_class->getAttributesGroups($langIdFromList);
                }
            }

            if (!empty($extra_attributes) || !empty($fieldGroupedAttributes)) {
                $attributesList = $this->productAttributes;
            }

            //Product feature
            $this->offerAdditionalFields = [];

            if (!empty($featureEnable) && !empty($fieldFeature)) {
                $features = $this->getFrontFeatures($id_lang, $xdd['pro_id'], $multistoreId);

                if (!empty($features)) {
                    foreach ($features as $fKey => $fVal) {
                        $features[$fKey]['value'] = !empty($this->featureMapValues[$features[$fKey]['id_feature'].'-'.$features[$fKey]['id_feature_value']]) ? $this->featureMapValues[$features[$fKey]['id_feature'].'-'.$features[$fKey]['id_feature_value']] : $fVal['value'];
                    }

                    if ($mode == 'x') {
                        $xmlProduct .= '<TECHDATA>';
                    }

                    if ($mode == 'man') {
                        $xmlProduct .= '<params>';
                    }

                    if (!empty($this->settings['attribute_structure_id'])) {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= $this->attributeFeatureStructure($fieldFeature[$f['id_feature']]['title_xml'], $f['value']);
                        }

                    } elseif ($mode == 'i') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<s:attribute name="'.$this->attributeName($f['name']).'">'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</s:attribute>';
                        }
                    } elseif ($mode == 'x') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<PARAMETER name="'.$this->attributeName($f['name']).'">'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</PARAMETER>';
                        }
                    } elseif ($mode == 'gla' || $mode == 'u' || $mode == 'naj' || $mode == 'zbo' || $mode == 'tov' || $mode == 'alz') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<PARAM><PARAM_NAME>'.$fieldFeature[$f['id_feature']]['title_xml'].'</PARAM_NAME><VAL>'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</VAL></PARAM>';
                        }
                    } elseif ($mode == 'mal') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            //$this->productAttributeAndFeatureName[] = $fieldFeature[$f['id_feature']]['title_xml'];
                            $xmlProduct .= '<PARAM><NAME>'.$fieldFeature[$f['id_feature']]['title_xml'].'</NAME><VALUE>'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</VALUE></PARAM>';
                        }
                    } elseif ($mode == 'ceo') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $paramsCeneo[$fieldFeature[$f['id_feature']]['title_xml']] = $f['value'];
                        }
                    } elseif ($mode == 'man') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<param><param_name>'.$fieldFeature[$f['id_feature']]['title_xml'].'</param_name><param_value>'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</param_value></param>';
                        }
                    } elseif ($mode == 'ho') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<param name="'.$this->attributeName($f['name']).'">'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</param>';
                        }
                    } elseif ($mode == 'ro') {
                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            $xmlProduct .= '<param name="'.$fieldFeature[$f['id_feature']]['title_xml'].'">'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</param>';
                        }
                    } elseif ($mode == 'cgr') {
                        $makeModelBranch = [];
                        $makeModelBranchXml = '';
                        $carFeatureList = ['make', 'model', 'yearfrom', 'yearto',];

                        foreach ($features as $f) {
                            if (empty($fieldFeature[$f['id_feature']])) {
                                continue;
                            }

                            if ($fieldFeature[$f['id_feature']]['title_xml'] == 'make') {
                                $f['value'] = !empty($f['value']) ? $f['value'] : $this->productParam['manufacturer'][$xdd['pro_id']];
                            }

                            $fullRow = '<'.$fieldFeature[$f['id_feature']]['title_xml'].'>'.$this->settings['pref_s'].$f['value'].$this->settings['pref_e'].'</'.$fieldFeature[$f['id_feature']]['title_xml'].'>';

                            if (in_array($fieldFeature[$f['id_feature']]['title_xml'], $carFeatureList)) {
                                $makeModelBranch[$fieldFeature[$f['id_feature']]['title_xml']] = $fullRow;
                                continue;
                            }

                            $xmlProduct .= $fullRow;
                        }

                        if (!empty($makeModelBranch)) {
                            foreach ($carFeatureList as $c) {
                                $makeModelBranchXml .= !empty($makeModelBranch[$c]) ? $makeModelBranch[$c] : '';
                            }

                            $xmlProduct .= '<makemodels><makemodel>'.$makeModelBranchXml.'</makemodel></makemodels>';
                        }
                    } else {
                        foreach ($features as $f) {
                            if (!empty($fieldFeature[$f['id_feature']])) {
                                if ($mode == 'pub') {
                                    if ($permissions['xml_type'] == 'offers' && in_array($fieldFeature[$f['id_feature']]['title_xml'], ['quantity-mini', 'quantity-step',])) {
                                        $this->offerAdditionalFields[$fieldFeature[$f['id_feature']]['title_xml']] = $f['value'];
                                        continue;
                                    }

                                    $xmlProductMru .= '<attribute><code>'.$fieldFeature[$f['id_feature']]['title_xml'].'</code><value>'.$this->settings['pref_s'] . $f['value'] . $this->settings['pref_e'] .'</value></attribute>';
                                }

                                $xmlProduct .= $this->getDeepTagName($fieldFeature[$f['id_feature']]['title_xml']). $this->settings['pref_s'] . $f['value'] . $this->settings['pref_e'] . $this->getDeepTagName($fieldFeature[$f['id_feature']]['title_xml'], true);
                            }
                        }
                    }

                    if ($mode == 'x') {
                        $xmlProduct .= '</TECHDATA>';
                    }

                    if ($mode == 'man') {
                        $xmlProduct .= '</params>';
                    }
                }
            }

            $affiliate_prices = [];

            //Affiliate price
            if (!empty($affiliate)) {
                $affiliate_prices = Db::getInstance()->ExecuteS('SELECT `affiliate_name`, `affiliate_formula`, `xml_name`,
                    `category_type`, `category_id_list`
                    FROM '._DB_PREFIX_.'blmod_xml_affiliate_price
                    WHERE `affiliate_name` IN ("'. str_replace(',', '","', pSQL(implode(',', $affiliate))).'")
                    ORDER BY affiliate_name ASC');
            }

            if (!empty($affiliate_prices)) {
                $xmlProduct .= REPLACE_COMBINATION.'affiliate_price';
            }

            if ($mode == 'h') {
                $xmlProduct .= '<Min_shipping>1</Min_shipping><Max_shipping>4</Max_shipping>';
            }

            if (!empty($productSettingsPackageId)) {
                $xmlProduct .= !empty($productSettingsList[$xdd['pro_id']]) ? $productSettingsList[$xdd['pro_id']] : $productSettingsList[ProductSettingsFront::DEFAULT_SETTINGS_ID];
            }

            if (!empty($combinations)) {
                $usedParentGroup = [];
                $parentGroupName = '';
                $xmlProductMruBeforeCombination = $xmlProductMru;
                $xmlProductMru = '';

                if (!empty($settings['only_available_for_order'])) {
                    $outOfStockStatus = StockAvailable::outOfStock($xdd['pro_id']);
                }

                $attributesByParentGroup = !empty($permissions['merge_attributes_by_group']) ? $mergeAttributesByGroup->getByParentGroup($productAttributes, $onlyInStock, $fieldGroupedAttributes, $this->attributeMapValues, $outOfStockStatus) : array();

                if ($mode == 'gla' || $mode == 'naj') {
                    $xmlProduct .= '<ITEMGROUP_ID>'.$xdd['pro_id'].'</ITEMGROUP_ID>';
                }

                $isFirst = 0;
                $this->isTheSameAttribute = false;

                foreach ($combinations as $c) {
                    if (!empty($this->isTheSameAttribute)) {
                        continue;
                    }

                    if ($onlyInStock && $c['quantity'] < 1) {
                        continue;
                    }

                    $combinationCore = new CombinationCore();
                    $combinationCore->id = $c['id_product_attribute'];
                    $combinationImages = $combinationCore->getWsImages();

                    if (!empty($settings['filter_image'])) {
                        if ($settings['filter_image'] == 1) {
                            if (empty($combinationImages)) {
                                continue;
                            }
                        }

                        if ($settings['filter_image'] == 2) {
                            if (!empty($combinationImages)) {
                                continue;
                            }
                        }
                    }

                    if (!empty($permissions['filter_exclude_empty_params'])) {
                        foreach ($permissions['filter_exclude_empty_params'] as $emptyParamKEy) {
                            if (empty($c[$emptyParamKEy])) {
                                continue 2;
                            }
                        }
                    }

                    if (!empty($settings['only_available_for_order'])) {
                        if ($c['quantity'] < 1 && $outOfStockStatus == 0) {
                            continue;
                        }

                        if ($c['quantity'] < 1 && $outOfStockStatus == 2 && $configuration['PS_ORDER_OUT_OF_STOCK'] == 0) {
                            continue;
                        }
                    }

                    if (!$filterByAttribute->isRequiredAttributeExists($permissions['only_with_attributes_status'], $permissions['only_with_attributes'], $c['id_product_attribute'], $productAttributes)) {
                        continue;
                    }

                    if (!empty($permissions['only_without_attributes_status'])) {
                        if ($filterByAttribute->isRequiredAttributeExists($permissions['only_without_attributes_status'], $permissions['only_without_attributes'], $c['id_product_attribute'], $productAttributes)) {
                            continue;
                        }
                    }

                    if ($this->isExcludeByMinimumOrderQuantity($c['minimal_quantity'])) {
                        continue;
                    }

                    $this->groupedAttributesByParent = [
                        'parentName' => '',
                        'childValues' => [],
                    ];

                    if (!empty($permissions['merge_attributes_by_group'])) {
                        if (!empty($attributesByParentGroup)) {
                            $parentGroupName = $mergeAttributesByGroup->getCombinationParentGroupName($productAttributes, $c['id_product_attribute']);

                            if (in_array($parentGroupName, $usedParentGroup)) {
                                continue;
                            }

                            $c['attribute_designation'] = $mergeAttributesByGroup->getCombinationNameByMainGroup($parentGroupName);
                            $usedParentGroup[] = $parentGroupName;

                            $this->groupedAttributesByParent = [
                                'parentName' => $parentGroupName,
                                'childValues' => $attributesByParentGroup,
                            ];
                        }
                    }

                    $xmlProductCombination = '';

                    if (empty($isFirst) && $mode == 'bee') {
                        $xmlProductCombination .= $this->replaceCombinationToEmpty($xmlProduct, $combinationDefault, $xmlImages, $product_class, $mode, [], [], true).'</'.$block_name['cat-block-name'].'>';
                        $isFirst = true;
                    }

                    if ($mode == 'pub') {
                        $xmlProductCombination .= $this->replaceCombination($xmlProduct, $c, $xmlImages, $link_class, $product_class, $id_lang, $affiliate_prices, $mode, $isAvailableWhenOutOfStock, $availabilityName, array(), $paramsCeneo);
                        $this->publicGrProducts = true;
                        $xmlProductMru .= $this->replaceCombination($xmlProductMruBeforeCombination, $c, $xmlImages, $link_class, $product_class, $id_lang, $affiliate_prices, $mode, $isAvailableWhenOutOfStock, $availabilityName, $combinationImages, $paramsCeneo);
                        $this->publicGrProducts = false;
                        $xmlProductMru .= $this->getProductAttributeBranchMru($extra_attributes, $attributesList, $block_name, $one_branch, $c['id_product_attribute']);
                    } else {
                        $xmlProductCombination .= $this->replaceCombination($xmlProduct, $c, $xmlImages, $link_class, $product_class, $id_lang, $affiliate_prices, $mode, $isAvailableWhenOutOfStock, $availabilityName, $combinationImages, $paramsCeneo);
                        $xmlProductCombination .= $this->getProductAttributeBranch($extra_attributes, $attributesList, $block_name, $one_branch, $c['id_product_attribute']);
                        $xmlProductCombination = $this->addVariants($xmlProductCombination, $product_class, $isAvailableWhenOutOfStock, $availabilityName, $fieldGroupedAttributes);
                    }

                    if (empty($permissions['merge_attributes_by_group']) || empty($attributesByParentGroup)) {
                        $attributeBranch = $this->getProductGroupedAttributeBranch($fieldGroupedAttributes, $attributesList, $c['id_product_attribute'], $paramsCeneo);

                        if ($mode != 'pub') {
                            $xmlProductCombination .= $attributeBranch['xml'];
                        } else {
                            $xmlProductMru .= $attributeBranch['xml'];

                            $this->settings['feed_mode'] = '';
                            $attributeBranch = $this->getProductGroupedAttributeBranch($fieldGroupedAttributes, $attributesList, $c['id_product_attribute'], $paramsCeneo);
                            $this->settings['feed_mode'] = $mode;
                            $xmlProductCombination .= $attributeBranch['xml'];
                        }

                        $paramsCeneo = $attributeBranch['paramsCeneo'];
                    } else {
                        $xmlProductCombination .= $this->getProductGroupedAttributeBranchByParentGroup($fieldGroupedAttributes, $attributesByParentGroup, $parentGroupName, $mergeAttributesByGroup->getParentGroup());
                    }

                    if (!empty($this->productAttributeAndFeatureName)) {
                        $xmlProductCombination .= '<VARIABLE_PARAMS>';

                        $this->productAttributeAndFeatureName = array_unique($this->productAttributeAndFeatureName);

                        foreach ($this->productAttributeAndFeatureName as $v) {
                            $xmlProductCombination .= '<PARAM_replace>'.$v.'</PARAM_replace>';
                        }

                        $xmlProductCombination .= '</VARIABLE_PARAMS>';
                    }

                    if ($mode == 'mal' || $mode == 'cgr') {
                        $xmlProductCombination = $this->fieldSorting($xmlProductCombination . '</' . $block_name['cat-block-name'] . '>', $mode, '', true);
                    }

                    $xmlProductCombination = $this->replaceXmlTree($xmlProductCombination);

                    $xmlProductMru .= '</product>';
                    $xml .= $xmlProductCombination.'</'.$block_name['cat-block-name'].'>';
                }
            } else {
                if (!$filterByAttribute->isRequiredAttributeExists($permissions['only_with_attributes_status'], $permissions['only_with_attributes'], 0, $productAttributes)) {
                    continue;
                }

                if (!empty($permissions['only_without_attributes_status'])) {
                    if ($filterByAttribute->isRequiredAttributeExists($permissions['only_without_attributes_status'], $permissions['only_without_attributes'], 0, $productAttributes)) {
                        continue;
                    }
                }

                if ($this->isExcludeByMinimumOrderQuantity($product_class->minimal_quantity)) {
                    continue;
                }

                $affiliatePriceFinal = 0;

                if (!empty($affiliate_prices)) {
                    $affiliatePrice = '';

                    if ($mode == 'wor') {
                        $affiliatePrice = '<offer-additional-fields>';
                    }

                    foreach ($affiliate_prices as $a_price) {
                        $affiliatePriceFinal = $this->calculateAffiliatePrices(
                            $salePrice,
                            $basePrice,
                            $shippingPrice,
                            $priceWithoutDiscount,
                            $wholesalePrice,
                            $a_price,
                            $product_class
                        );

                        if ($mode == 'ceo') {
                            break;
                        }

                        if ($mode == 'wor') {
                            $affiliatePrice .= '<offer-additional-field>
                                    <code>price[channel='.$a_price['xml_name'].']</code>
                                    <value>'.$this->settings['pref_s'].$affiliatePriceFinal.$this->settings['pref_e'].'</value>
                                </offer-additional-field>';
                            continue;
                        }

                        $affiliatePrice .= $this->getDeepTagName($a_price['xml_name']).$this->settings['pref_s'].$affiliatePriceFinal.$this->settings['pref_e'].$this->getDeepTagName($a_price['xml_name'], true);
                    }

                    if ($mode == 'wor') {
                        if (!empty($this->settings['worten_ship_from_country'])) {
                            $affiliatePrice .= '<offer-additional-field>
                                    <code>ship-from-country-offer</code>
                                    <value>'.$this->settings['worten_ship_from_country'].'</value>
                                </offer-additional-field>';
                        }

                        $affiliatePrice .= '</offer-additional-fields>';
                    }

                    $xmlProduct = str_replace(REPLACE_COMBINATION.'affiliate_price', $affiliatePrice, $xmlProduct);
                }

                $attributeBranch = $this->getProductGroupedAttributeBranch($fieldGroupedAttributes, $attributesList, 0, $paramsCeneo);

                if ($mode != 'pub') {
                    $xmlProduct .= $attributeBranch['xml'];
                } else {
                    $xmlProductMru .= $attributeBranch['xml'];
                }

                $paramsCeneo = $attributeBranch['paramsCeneo'];

                $xmlProduct = $this->replaceCombinationToEmpty($xmlProduct, $combinationDefault, $xmlImages, $product_class, $mode, $paramsCeneo, $affiliatePriceFinal);
                $xmlProduct = $this->replaceXmlTree($xmlProduct);
                $xmlProduct = $this->addVariants($xmlProduct, $product_class, $isAvailableWhenOutOfStock, $availabilityName, $fieldGroupedAttributes);

                if ($mode == 'pub') {
                    $this->publicGrProducts = true;
                    $xmlProductMru = $this->replaceCombinationToEmpty($xmlProductMru, $combinationDefault, $xmlImages, $product_class, $mode, $paramsCeneo, $affiliatePriceFinal);
                    $this->publicGrProducts = false;
                    $xmlProductMru .= $this->getProductAttributeBranchMru($extra_attributes, $attributesList, $block_name, $one_branch);
                }

                if ($mode == 'h' && !empty($xmlImagesUrl)) {
                    $xmlProduct .= '<image_tree>'.implode('|', $xmlImagesUrl).'</image_tree>';
                }

                if ($mode == 'mala' && !empty($xmlImagesUrl)) {
                    $xmlProduct .= '<Image>'.implode(',', $xmlImagesUrl).'</Image>';
                }

                if ($mode == 'kog' && !empty($xmlImagesUrl)) {
                    $xmlProduct .= '<IMAGES>'.implode('|', $xmlImagesUrl).'</IMAGES>';
                }

                if ($mode == 'mir' && !empty($xmlImagesUrl)) {
                    $xmlProduct .= '<mainImage>'.implode(',', $xmlImagesUrl).'</mainImage>';
                    $xmlProduct .= '<mainImageThumb>'.implode(',', $xmlImagesUrl).'</mainImageThumb>';
                }

                if ($mode == 'pub' && !empty($xmlImagesUrl)) {
                    $uId = 0;

                    foreach ($xmlImagesUrl as $u) {
                        $xmlProductMru .= '<attribute><code>'.($uId < 1 ? 'mainImage' : 'varExtraImage'.$uId).'</code><value>'.$u.'</value></attribute>';
                        $xmlProductMru .= '<attribute><code>'.($uId < 1 ? 'mainImageThumb' : 'varExtraImageThumb'.$uId).'</code><value>'.$u.'</value></attribute>';
                        $uId++;
                    }
                }

                if (!empty($settings['spartoo_size'])) {
                    $xmlProduct .= $this->getSpartooSizeBlock($product_class->getAttributesResume($id_lang, ' ', ', '));
                }

                $xmlProductExtra = '';

                if (!empty($this->productAttributeAndFeatureName)) {
                    $xmlProductExtra .= '<VARIABLE_PARAMS>';

                    $this->productAttributeAndFeatureName = array_unique($this->productAttributeAndFeatureName);

                    foreach ($this->productAttributeAndFeatureName as $v) {
                        $xmlProductExtra .= '<PARAM_replace>'.$v.'</PARAM_replace>';
                    }

                    $xmlProductExtra .= '</VARIABLE_PARAMS>';
                }

                $xml .= $this->fieldSorting($xmlProduct.'</'.$block_name['cat-block-name'].'>', $mode, $xmlProductExtra);
                $xmlProductMru .= '</product>';
            }

            $xmlProductMruAll .= $xmlProductMru;
        }

        if (!empty($block_status['file-name']) && (empty($currentChunkNo) || !empty($isLastChunk))) {
            $xml .= '</' . $block_name['file-name'] . '>';
        }

        if ($mode == 'wum') {
            $categoriesAll = $this->getAllCategories($l, $multistoreId);
            $usedCategories = array();
            $usedParents = array();

            $xml .= '<categories>';

            foreach ($categoriesAll as $cat) {
                if (!in_array($cat['id_category'], $categoriesOfProductsUsed)) {
                    continue;
                }

                if ($cat['id_parent'] < 3) {
                    $cat['id_parent'] = 0;
                }

                if (!empty($cat['id_parent'])) {
                    $usedParents[] = $cat['id_parent'];
                }

                $usedCategories[] = $cat['id_category'];

                $xml .= '<category id="'.$cat['id_category'].'"'.(!empty($cat['id_parent']) ? ' parent_id="'.$cat['id_parent'].'"' : '').'>'.$this->settings['pref_s'].$cat['name'].$this->settings['pref_e'].'</category>';
            }

            $usedParents = array_unique($usedParents);

            foreach ($usedParents as $catId) {
                if (in_array($catId, $usedCategories)) {
                    continue;
                }

                $xml .= '<category id="'.$catId.'">'.$this->settings['pref_s'].$categoriesByKey[$catId].$this->settings['pref_e'].'</category>';
            }

            $xml .= '</categories>';
        }

        if ($mode == 'pub') {
            if ($permissions['xml_type'] == 'products') {
                $xml = '<products>'.$xmlProductMruAll.'</products>';
            } elseif ($permissions['xml_type'] == 'offers') {
            } else {
                $xmlHeaderPub = '';

                if (!empty($feedGenerationTime) && !empty($feedGenerationTimeName)) {
                    $xmlHeaderPub = '<'.$feedGenerationTimeName.'>'.date('Y-m-d H:i:s').'</'.$feedGenerationTimeName.'>';
                }

                $xml = $xmlHeaderPub.'<products>'.$xmlProductMruAll.'</products>'.$xml;
            }
        }

        return $xml;
    }

    public function replaceXmlTree($xml)
    {
        preg_match_all("'<sBLMOD>(.*?)</sBLMOD>'si", $xml, $categories);

        $levels = [];

        if (empty($categories[1])) {
            return $xml;
        }

        foreach ($categories[1] as $k => $c) {
            preg_match("'<nBLMOD>(.*?)</nBLMOD>'si", $c, $name);
            $names = explode('_lBLMOD_', $name[1]);

            preg_match("'<vBLMOD>(.*?)</vBLMOD>'si", $c, $value);

            $levels[$names[0]][] = [
                'full' => $categories[0][$k],
                'name' => $names[1],
                'value' => $value[1],
            ];
        }

        foreach ($levels as $branchName => $branch) {
            $xmlN = (($branchName == 'warehouse' && $this->settings['feed_mode'] == 'ppa') ? '<warehouses>' : '').'<'.$branchName.'>';
            $firstField = '';

            if ($branchName == 'warehouse' && $this->settings['feed_mode'] == 'ppa') {
                $xmlN .= '<warehouse_id>1</warehouse_id>';
            }

            foreach ($branch as $b) {
                $xmlN .= '<'.$b['name'].'>';
                $xmlN .= $b['value'];
                $xmlN .= '</'.$b['name'].'>';

                if (empty($firstField)) {
                    $firstField = $b['full'];
                } else {
                    $xml = str_replace($b['full'], '', $xml);
                }
            }

            $xmlN .= '</'.$branchName.'>'. (($branchName == 'warehouse' && $this->settings['feed_mode'] == 'ppa')  ? '</warehouses>' : '');

            $xml = str_replace($firstField, $xmlN, $xml);
        }

        return $xml;
    }

    public function getProductTax($idTaxRulesGroup)
    {
        if (isset($this->taxRateList[$idTaxRulesGroup])) {
            return $this->taxRateList[$idTaxRulesGroup];
        }

        $rate = 0;

        if (!empty($idTaxRulesGroup)) {
            $rate = Db::getInstance()->getValue('SELECT t.rate
                FROM ' . _DB_PREFIX_ . 'tax_rule tr
                LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON
                t.id_tax = tr.id_tax
                WHERE tr.id_tax_rules_group = ' . (int)$idTaxRulesGroup);
        }

        $rate = PriceFormat::convertByType($rate, $this->settings['price_format_id']);
        $this->taxRateList[$idTaxRulesGroup] = $rate;

        return $rate;
    }

    public function replaceCombination(
        $xml,
        $combination,
        $images,
        $link_class,
        $product_class,
        $id_lang,
        $affiliate_prices,
        $mode,
        $isAvailableWhenOutOfStock,
        $availabilityName,
        $combinationImages,
        $paramsCeneo
    ) {
        $feedPrice = new FeedPrice();

        $combinationSalePrice = $feedPrice->getEditedPrice($product_class->getPriceStatic($product_class->id, true, $combination['id_product_attribute'], (empty($this->settings['price_rounding_type']) ? 6 : 2)), 'sale_blmod', $this->settings);
        $priceWithoutDiscount = $feedPrice->getEditedPrice($product_class->getPriceStatic($product_class->id, true, $combination['id_product_attribute'], 2, null, false, false), 'price_wt_discount_blmod', $this->settings);
        $basePrice = $feedPrice->getEditedPrice($product_class->getPriceStatic($product_class->id, false, $combination['id_product_attribute'], 2), 'product_price', $this->settings);
        $combination['quantity'] = (int)$combination['quantity'];
        $combinationId = $product_class->id.$this->settings['combination_id_separator'].$combination['id_product_attribute'];
        $extraUrl = !empty($this->extraFieldByName['product_url_utm_blmod']) ? htmlspecialchars_decode($this->extraFieldByName['product_url_utm_blmod'], ENT_QUOTES) : '';
        $url = $link_class->getProductLink($product_class, null, null, null, $id_lang, null, $combination['id_product_attribute'], Configuration::get('PS_REWRITING_SETTINGS'), false, true);
        $priceSale = $this->getPriceFormat($combinationSalePrice);
        $weight = $product_class->weight+$combination['weight'];
        $combinationAttributes = array();
        $combination['isbn'] = isset($combination['isbn']) ? $combination['isbn'] : '';
        $combination['upc'] = !empty($combination['upc']) ? $combination['upc'] : '';
        $combination['mpn'] = !empty($combination['mpn']) ? $combination['mpn'] : '';
        $wholesalePrice = ($combination['wholesale_price'] < 0.01) ? $basePrice : $combination['wholesale_price'];
        $taxRate = $this->productParam['tax_rate'][$product_class->id];
        $salePriceOriginal = $combinationSalePrice;
        $saleTaxExcl = $this->getPriceFormat($feedPrice->getEditedPrice(Tools::ps_round(($salePriceOriginal / (1 + $taxRate / 100)), 2, 1), 'sale_tax_excl_blmod', $this->settings));
        $priceSaleWtDiscount = $this->getPriceFormat($priceWithoutDiscount);

        $priceSale = $this->addSpecificFixedPrice($priceSale, $product_class->id, $combination['id_product_attribute']);

        foreach ($this->productAttributes as $a) {
            if ($combination['id_product_attribute'] != $a['id_product_attribute']) {
                continue;
            }

            $combinationAttributes[] = $a;
        }

        if (!empty($this->settings['attribute_id_as_combination_id'])) {
            foreach ($this->productAttributes as $a) {
                if ($a['id_product_attribute'] != $combination['id_product_attribute']) {
                    continue;
                }

                if ($this->settings['merge_attributes_parent'] == $a['id_attribute_group']) {
                    $combinationId = $product_class->id . '-' . $a['id_attribute'];
                    break;
                }
            }
        }

        $elementsByKey = array(
            3 => (!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$combinationId,
            4 => !empty($combination['reference']) ? $combination['reference'] : '',
            5 => !empty($combination['ean13']) ? $combination['ean13'] : '',
            6 => isset($combination['isbn']) ? $combination['isbn'] : '',
            7 => !empty($this->productParam['category'][$product_class->id]) ? $this->productParam['category'][$product_class->id] : '',
            8 => !empty($this->productParam['manufacturer'][$product_class->id]) ? $this->productParam['manufacturer'][$product_class->id] : '',
            9 => !empty($this->productParam['reference'][$product_class->id]) ? $this->productParam['reference'][$product_class->id] : '',
        );

        $productTitleList = [];
        $name = '';

        foreach ($this->langIdWitIso as $langIdFromList => $iso) {
            if ($mode == 'spa' && count($this->langIdWitIso)) {
                $combinationAttributes = [];

                foreach ($this->productAttributesAllLanguages[$langIdFromList] as $a) {
                    if ($combination['id_product_attribute'] != $a['id_product_attribute']) {
                        continue;
                    }

                    $combinationAttributes[] = $a;
                }
            }

            $name = !empty($this->productParam['title-'.$iso][$product_class->id]) ? $this->productTitleEditor->replaceTitleByKey($this->productParam['title-'.$iso][$product_class->id], $this->productTitleEditorValues) : '';
            $name = $this->productTitleEditor->addElementsToTitle($name, $this->settings['title_elements'], $elementsByKey);
            $name = $this->productTitleEditor->addAttributesToTile($name, $this->settings['title_elements'], $combinationAttributes);
            $nameWithoutTransform = $name;
            $name = $this->productTitleEditor->titleTransformer($name, $this->settings);
            $name = $this->productTitleEditor->addText($name, $this->settings);
            $xml = str_replace(REPLACE_COMBINATION.'name-'.$iso, $name, $xml);
            $productTitleList[$iso] = $name;
            $replaceEmptyDescription = '';

            switch ($this->settings['empty_description']) {
                case 1:
                    $replaceEmptyDescription = $nameWithoutTransform;
                    break;
                case 2:
                    $replaceEmptyDescription = $this->settings['empty_description_text'];
                    break;
            }

            $xml = str_replace(REPLACE_COMBINATION . 'description-' . $iso, $replaceEmptyDescription, $xml);
            $xml = str_replace(REPLACE_COMBINATION . 'description_short-' . $iso, $replaceEmptyDescription, $xml);
        }

        if (empty($combination['ean13']) && $mode == 'sfl') {
            $combination['ean13'] = $this->settings['domain'].'-'.$combinationId;
        }

        $eanWithPrefix = '';

        if (!empty($combination['ean13'])) {
            $eanWithPrefix = (!empty($this->settings['ean_prefix']) ? $this->settings['ean_prefix'] : '').$combination['ean13'];
        }

        $referenceWithPrefix = '';

        if (!empty($combination['reference'])) {
            $referenceWithPrefix = (!empty($this->settings['reference_prefix']) ? $this->settings['reference_prefix'] : '').$combination['reference'];
        }

        $catBlockName = '';

        if ($mode == 'bee') {
            $catBlockName = ' idp="'.$product_class->id.'" idd="'.$combination['id_product_attribute'].'" type="child"';
        }

        if (!empty($this->settings['max_quantity']) && !empty($this->settings['max_quantity_status']) && $combination['quantity'] > $this->settings['max_quantity']) {
            $combination['quantity'] = $this->settings['max_quantity'];
        }

        $xml = $this->removeSalePriceTag($xml, $combinationSalePrice, $priceWithoutDiscount);

        $combination['mpn'] = $this->displayMpnFromAttributeValue($combination['mpn']);

        $xml = str_replace(REPLACE_COMBINATION.'quantity', $combination['quantity'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'minimal_quantity', $combination['minimal_quantity'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_with_min_qty_blmod', $this->getPriceFormat($feedPrice->getEditedPrice(($salePriceOriginal * $combination['minimal_quantity']), 'sale_with_min_qty_blmod', $this->settings)), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'ean13', $combination['ean13'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'isbn', $combination['isbn'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'mpn', $combination['mpn'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'supplier_reference', $combination['supplier_reference'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'reference', $combination['reference'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_reference', $referenceWithPrefix, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'url', $url.$extraUrl, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_url', $url, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_blmod', $priceSale, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_tax_excl_blmod', $saleTaxExcl, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'price_wt_discount_blmod', $priceSaleWtDiscount, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'wholesale_price', $this->getPriceFormat($feedPrice->getEditedPrice($wholesalePrice, 'product_wholesale_price', $this->settings)), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'id_product', (!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$combinationId, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_id_combination', (($mode == 'mir') ? 'PRODUCT' : '').$combinationId, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'price', $this->getPriceFormat($basePrice), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'stock_status', ($combination['quantity'] > 0 ? 'Y' : 'N'), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'product_id_element', $product_class->id.'-'.$combination['id_product_attribute'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'weight', $weight, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_ean13_with_prefix', $eanWithPrefix, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'cat-block-name', $catBlockName, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'location', !empty($combination['location']) ? $combination['location'] : '', $xml);
        $xml = str_replace(REPLACE_COMBINATION.'upc', $combination['upc'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'available_date', $combination['available_date'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'availability_label', $this->availabilityLabel->getStatus($product_class, $combination['quantity']), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'is_default_combination', (int)$combination['default_on'], $xml);

        if ($mode == 'mal') {
            $xml = str_replace('</TITLE>', '</TITLE><ITEMGROUP_TITLE>' . $name . '</ITEMGROUP_TITLE>', $xml);
        }

        if ($mode == 'pub') {
            if ($this->publicGrProducts) {
                $xml .= '<attribute><code>variant-group-id</code><value>' . $this->settings['pref_s'] . $product_class->id . $this->settings['pref_e'] . '</value></attribute>';
                $xml .= '<attribute><code>product-sku</code><value>' . $this->settings['pref_s'] . $combinationId . $this->settings['pref_e'] . '</value></attribute>';
                $xml .= '<attribute><code>unique-identifier</code><value>' . $this->settings['pref_s'] . 'PRODUCT' . $combinationId . $this->settings['pref_e'] . '</value></attribute>';
            } else {
                $images = array();
                $productTax = $this->getProductTax($product_class->id_tax_rules_group);

                $xml .= '<offer-additional-fields>';

                if (!empty($this->offerAdditionalFields)) {
                    foreach ($this->offerAdditionalFields as $oafKey => $oafVal) {
                        $xml .= '<offer-additional-field><code>' . $oafKey . '</code><value>' . $this->settings['pref_s'] . $oafVal . $this->settings['pref_e'] . '</value></offer-additional-field>';
                    }
                }

                $xml .= '<offer-additional-field><code>offervat</code><value>'.$this->settings['pref_s'].$productTax.$this->settings['pref_e'].'</value></offer-additional-field>';
                $xml .= '<offer-additional-field><code>shippingvat</code><value>'.$this->settings['pref_s'].$productTax.$this->settings['pref_e'].'</value></offer-additional-field>';
                $xml .= '</offer-additional-fields>';
            }
        }

        if (!empty($affiliate_prices)) {
            $affiliatePrice = '';

            if ($mode == 'wor') {
                $affiliatePrice = '<offer-additional-fields>';
            }

            foreach ($affiliate_prices as $a_price) {
                $affiliatePriceFinal = $this->calculateAffiliatePrices(
                    $combinationSalePrice,
                    $product_class->price,
                    $this->productParam['shipping_price'][$product_class->id],
                    $priceWithoutDiscount,
                    $combination['wholesale_price'],
                    $a_price,
                    $product_class
                );

                if ($mode == 'ceo') {
                    break;
                }

                if ($mode == 'wor') {
                    $affiliatePrice .= '<offer-additional-field>
                                    <code>price[channel='.$a_price['xml_name'].']</code>
                        <value>'.$this->settings['pref_s'].$affiliatePriceFinal.$this->settings['pref_e'].'</value>
                    </offer-additional-field>';
                    continue;
                }

                $affiliatePrice .= $this->getDeepTagName($a_price['xml_name']).$this->settings['pref_s'].$affiliatePriceFinal.$this->settings['pref_e'].$this->getDeepTagName($a_price['xml_name'], true);
            }

            if ($mode == 'wor') {
                if (!empty($this->settings['worten_ship_from_country'])) {
                    $affiliatePrice .= '<offer-additional-field>
                            <code>ship-from-country-offer</code>
                            <value>'.$this->settings['worten_ship_from_country'].'</value>
                        </offer-additional-field>';
                }

                $affiliatePrice .= '</offer-additional-fields>';
            }

            $xml = str_replace(REPLACE_COMBINATION.'affiliate_price', $affiliatePrice, $xml);
        }

        $combinationAvailability = $availabilityName['out'];

        if ($product_class->available_for_order == 1 || $product_class->online_only == 1) {
            if ($combination['quantity'] > 0) {
                $combinationAvailability = $availabilityName['in'];
            } else {
                if ($isAvailableWhenOutOfStock) {
                    $combinationAvailability = !empty($availabilityName['on_demand']) ? $availabilityName['on_demand'] : $availabilityName['in'];
                }
            }
        }

        $xml = str_replace(REPLACE_COMBINATION.'available_for_order', $combinationAvailability, $xml);

        $xml .= $this->getAvailabilityDate($combinationAvailability);

        if ($mode == 'ceo') {
            $extraParam = '';
            $combinationIdCeo = $combinationId;

            if ($this->settings['feed_mode'] == 'apl') {
                $combinationIdCeo = $combination['reference'];
            }

            if (!empty($weight) && $product_class->weight > 0) {
                $extraParam = ' weight="'.$weight.'"';
                unset($paramsCeneo['weight']);
            }

            $addAvailStatus = ' avail="'.$combinationAvailability.'"';
            $addStockStatus = ' stock="'.$combination['quantity'].'"';

            if (empty($this->settings['is_enabled_field']['available_for_order'])) {
                $addAvailStatus = '';
            }

            if (empty($this->settings['is_enabled_field']['quantity'])) {
                $addStockStatus = '';
            }

            $tagParams = 'id="'.$combinationIdCeo.'" url="'.$url.'" price="'.$priceSale.'"'.$addStockStatus.$addAvailStatus;
            $xml = str_replace('<o>', '<o '.$tagParams.$extraParam.'>', $xml);
        }

        $imagesXml = '';
        $combinationImagesList = array();
        $imageNo = 1;

        if (empty($combinationImages) && !empty($images)) {
            foreach ($images as $id => $i) {
                $combinationImages[] = array('id' => $id);
            }
        }

        if (!empty($images)) {
            foreach ($images as $id => $i) {
                $images[$id] = str_replace('additional_image_link>', 'image_link>', $i);
            }
        }

        if (!empty($combinationImages)) {
            $combinationImagesValues = [];

            foreach ($combinationImages as $v) {
                $combinationImagesValues[] = $v['id'];
            }

            foreach ($images as $imageId => $imageUrl) {
                if (!in_array($imageId, $combinationImagesValues)) {
                    continue;
                }

                $combinationImagesList[] = $images[$imageId];
            }

            if (!empty($combinationImagesList)) {
                if ($mode == 'spa') {
                    $imagesXml .= '<photos>';
                }

                if ($mode == 'ppa') {
                    $imagesXml .= '<images>';
                }

                if ($mode == 'lw') {
                    $imageKeys = array_keys($combinationImagesList);

                    if ($imageNo = 1) {
                        $imagesXml .= str_replace('1>', '>', $combinationImagesList[$imageKeys[0]]);
                        unset($combinationImagesList[$imageKeys[0]]);
                    }

                    if (empty($combinationImagesList)) {
                        return str_replace(REPLACE_COMBINATION.'image', $imagesXml, $xml);
                    }

                    $imagesXml .= '<additional_imageurl>';
                }

                if ($mode == 'ceo') {
                    $imagesXml .= '<imgs>';
                }

                if ($mode == 'mal') {
                    foreach ($combinationImagesList as $image) {
                        $isCover = !empty($imagesXml) ? 'false' : 'true';
                        $imagesXml .= '<MEDIA>'.$image.'<MAIN>'.$isCover.'</MAIN></MEDIA>';

                        if (empty($this->settings['all_images'])) {
                            break;
                        }
                    }
                } elseif ($mode == 'tro') {
                    foreach ($combinationImagesList as $image) {
                        $imagesXml .= str_replace('e>', 'e'.($imageNo == 1 ? '' : $imageNo).'>', $image);
                        $imageNo++;
                    }
                } elseif ($mode == 'ceo') {
                    $isCover = false;

                    foreach ($combinationImagesList as $image) {
                        $image = str_replace(['<![CDATA[', ']]>'],'', $image);

                        if (empty($isCover)) {
                            $imagesXml .= '<main url="' . strip_tags($image) . '"></main>';
                        } else {
                            $imagesXml .= '<i url="' . strip_tags($image) . '"></i>';
                        }

                        $isCover = true;

                        if (empty($this->settings['all_images'])) {
                            break;
                        }
                    }
                } elseif ($mode == 'pub') {
                    $uId = 0;

                    foreach ($combinationImagesList as $u) {
                        $u = str_replace(array('<mainImage>', '</mainImage>'), '', $u);
                        $xml .= '<attribute><code>' . ($uId < 1 ? 'mainImage1' : 'varExtraImage' . $uId) . '</code><value>' . $u . '</value></attribute>';
                        $xml .= '<attribute><code>' . ($uId < 1 ? 'mainImageThumb' : 'varExtraImageThumb' . $uId) . '</code><value>' . $u . '</value></attribute>';
                        $uId++;

                        if (empty($this->settings['all_images'])) {
                            break;
                        }
                    }
                } elseif ($mode == 'mala') {
                     $imagesXml = '<Image>'.implode(',', $combinationImagesList).'</Image>';
                } elseif ($mode == 'kog') {
                    $imagesXml = '<IMAGES>'.implode('|', $combinationImagesList).'</IMAGES>';
                } elseif ($mode == 'mir') {
                    $imagesXml = '<mainImage>'.implode(',', $combinationImagesList).'</mainImage>';
                    $imagesXml .= '<mainImageThumb>'.implode(',', $combinationImagesList).'</mainImageThumb>';
                } elseif ($mode == 'kos') {
                    foreach ($combinationImagesList as $image) {
                        if ($imageNo == 2) {
                            $imagesXml .= '<additional_images>';
                        }

                        if ($imageNo > 1) {
                            $imagesXml .= str_replace('image_url', 'image', $image);
                        } else {
                            $imagesXml .= $image;
                        }

                        $imageNo++;
                    }

                    if ($imageNo > 2) {
                        $imagesXml .= '</additional_images>';
                    }
                } elseif ($mode == 'gla') {
                    $isCover = true;

                    foreach ($combinationImagesList as $c) {
                        if ($isCover) {
                            $imagesXml .= $c;
                            $isCover = false;
                            continue;
                        }

                        $imagesXml .= str_replace('IMGURL>', 'IMGURL_ALTERNATIVE>', $c);
                    }
                } elseif ($mode == 'k') {
                    foreach ($combinationImagesList as $image) {
                        $imagesXml .= str_replace('l>', 'l'.($imageNo == 1 ? '' : '_'.$imageNo).'>', $image);
                        $imageNo++;
                    }
                } elseif ($mode == 'alz') {
                    foreach ($combinationImagesList as $image) {
                        $image = strip_tags(str_replace(['<![CDATA[', ']]>'],'', $image));
                        $imagesXml .= '<PARAM><PARAM_NAME>PICTURE_'.str_pad(($imageNo), 2, '0', STR_PAD_LEFT).'</PARAM_NAME><VAL>' . $image. '</VAL></PARAM>';
                        $imageNo++;
                    }
                } elseif ($mode == 'cen') {
                    $imagesAdditional = [];

                    foreach ($combinationImagesList as $image) {
                        if ($imageNo > 1) {
                            $imagesAdditional[] = strip_tags(str_replace(['<![CDATA[', ']]>'],'', $image));
                            continue;
                        }

                        $imagesXml .= $image;

                        if (empty($this->settings['all_images'])) {
                            break;
                        }

                        $imageNo++;
                    }

                    $imagesXml .= '<moreImages>'.$this->settings['pref_s'].implode(',', $imagesAdditional).$this->settings['pref_e'].'</moreImages>';
                } elseif ($mode == 'lh') {
                    foreach ($combinationImagesList as $image) {
                        $imagesXml .= str_replace('_1>', ($imageNo == 1 ? '' : '_'.$imageNo) . '>', $image);
                        $imageNo++;
                    }
                } elseif ($mode == 'ps') {
                    foreach ($combinationImagesList as $image) {
                        $imagesXml .= str_replace('_1', '_'.$imageNo, $image);
                        $imageNo++;
                    }
                } else {
                    if (!empty($this->settings['additional_image_name'])) {
                        foreach ($combinationImagesList as $image) {
                            if ($imageNo > 1) {
                                $imagesXml .= '<'.$this->settings['additional_image_name'].'>'.strip_tags(str_replace(['<![CDATA[', ']]>'],'', $image)).'</'.$this->settings['additional_image_name'].'>';
                            } else {
                                $imagesXml .= $image;
                            }

                            if (empty($this->settings['all_images'])) {
                                break;
                            }

                            $imageNo++;
                        }
                    } else {
                        foreach ($combinationImagesList as $c) {
                            if ($mode == 's' || $mode == 'bp' || $mode == 'dm') {
                                $c = empty($imagesXml) ? str_replace('additional_imageurl>', 'image>', $c) : str_replace('image>', 'additional_imageurl>', $c);
                            }

                            if (!empty($imagesXml)) {
                                $c = str_replace('image_link>', 'additional_image_link>', $c);
                            }

                            $imagesXml .= str_replace('1>', (($mode == 'bee' && $imageNo == 1) ? '' : $imageNo) . '>', $c);
                            $imageNo++;

                            if (empty($this->settings['all_images']) || ($mode == 'mm' && $imageNo > 5)) {
                                break;
                            }
                        }
                    }
                }

                if ($mode == 'ppa') {
                    $imagesXml .= '</images>';
                }

                if ($mode == 'ceo') {
                    $imagesXml .= '</imgs>';
                }

                if ($mode == 'spa') {
                    $imagesXml .= '</photos>';
                }

                if ($mode == 'lw') {
                    $imagesXml .= '</additional_imageurl>';
                }
            }
        }

        $xml = str_replace(REPLACE_COMBINATION.'image', $imagesXml, $xml);

        if (!empty($paramsCeneo) && $mode == 'ceo') {
            $xml .= '<attrs>';

            foreach ($paramsCeneo as $k => $v) {
                $k = str_replace('_', ' ', $k);
                $v = ($v == REPLACE_COMBINATION.'ean13') ? str_replace($v, $combination['ean13'], $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'reference') ? str_replace($v, $combination['reference'], $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'isbn') ? str_replace($v, $combination['isbn'], $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'upc') ? str_replace($v, $combination['upc'], $v) : $v;

                $xml .= '<a name="'.$k.'">'.$this->settings['pref_s'].$v.$this->settings['pref_e'].'</a>';
            }

            $xml .= '</attrs>';
        }

        if (!empty($this->settings['spartoo_size'])) {
            $xml .= $this->getSpartooSizeBlock($combinationAttributes, $combination['ean13']);
        }

        if ($mode == 'spa' && count($this->langIdWitIso) > 1) {
            $xml .= '<languages>';

            foreach ($this->langIdWitIso as $langIdFromList => $iso) {
                if (empty($this->productLangValues[$product_class->id.$iso])) {
                    continue;
                }

                $color = '';

                foreach ($this->productAttributesAllLanguages[$langIdFromList] as $a) {
                    if ($a['id_product_attribute'] == $combination['id_product_attribute'] && $a['id_attribute_group'] == 2) {
                        $color = $a['attribute_name'];
                    }
                }

                $xml .= '<language>';
                $xml .= '<code>'.Tools::strtoupper($iso).'</code>';
                $xml .= str_replace(REPLACE_COMBINATION.'name-'.$iso, $productTitleList[$iso], $this->productLangValues[$product_class->id.$iso]);
                $xml .= '<product_color>'.$color.'</product_color>';
                $xml .= '<product_price>'.$this->getPriceFormat($priceWithoutDiscount).'</product_price>';
                $xml .= '</language>';
            }

            $xml .= '</languages>';
        }

        return $xml;
    }

    public function replaceCombinationToEmpty($xml, $combination, $images, $product_class, $mode, $paramsCeneo, $affiliatePriceFinal, $isParent = false)
    {
        $feedPrice = new FeedPrice();

        $combination['sale_blmod'] = !empty($combination['sale_blmod']) ? $combination['sale_blmod'] : 0;
        $combination['price_wt_discount_blmod'] = !empty($combination['price_wt_discount_blmod']) ? $combination['price_wt_discount_blmod'] : 0;
        $quantity = !empty($combination['quantity']) ? (int)$combination['quantity'] : 0;
        $url = !empty($combination['url']) ? $combination['url'] : '';
        $urlAdditional = !empty($combination['additional_url']) ? $combination['additional_url'] : '';
        $price = $this->getPriceFormat(!empty($combination['price']) ? $combination['price'] : 0);
        //$price = $feedPrice->getEditedPrice($product_class->getPriceStatic($product_class->id, false, null, 2), 'product_price', $this->settings);
        $priceSale = $this->getPriceFormat(!empty($combination['sale_blmod']) ? $feedPrice->getEditedPrice($combination['sale_blmod'], 'sale_blmod', $this->settings) : 0);
        $priceWtDiscount = $this->getPriceFormat(!empty($combination['price_wt_discount_blmod']) ? $feedPrice->getEditedPrice($combination['price_wt_discount_blmod'], 'price_wt_discount_blmod', $this->settings) : 0);
        $availability = isset($combination['available_for_order']) ? $combination['available_for_order'] : '';
        $ean = !empty($combination['ean13']) ? $combination['ean13'] : '';
        $reference = !empty($combination['reference']) ? $combination['reference'] : '';
        $combination['isbn'] = isset($combination['isbn']) ? $combination['isbn'] : '';
        $combination['upc'] = !empty($combination['upc']) ? $combination['upc'] : '';
        $wholesalePrice = ($product_class->wholesale_price < 0.01) ? $product_class->price : $product_class->wholesale_price;
        $taxRate = $this->productParam['tax_rate'][$product_class->id];
        $salePriceOriginal = $this->productParam['sale_price'][$product_class->id];
        $saleTaxExcl = $this->getPriceFormat($feedPrice->getEditedPrice(Tools::ps_round(($salePriceOriginal / (1 + $taxRate / 100)), 2, 1), 'sale_tax_excl_blmod', $this->settings));

        $priceSale = $this->addSpecificFixedPrice($priceSale, $product_class->id);

        $elementsByKey = array(
            3 => (!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$product_class->id,
            4 => !empty($this->productParam['reference'][$product_class->id]) ? $this->productParam['reference'][$product_class->id] : '',
            5 => !empty($this->productParam['ean13'][$product_class->id]) ? $this->productParam['ean13'][$product_class->id] : '',
            6 => isset($this->productParam['isbn'][$product_class->id]) ? $this->productParam['isbn'][$product_class->id] : '',
            7 => !empty($this->productParam['category'][$product_class->id]) ? $this->productParam['category'][$product_class->id] : '',
            8 => !empty($this->productParam['manufacturer'][$product_class->id]) ? $this->productParam['manufacturer'][$product_class->id] : '',
            9 => !empty($this->productParam['reference'][$product_class->id]) ? $this->productParam['reference'][$product_class->id] : '',
        );

        $productTitleList = [];

        foreach ($this->langIdAll as $iso) {
            $name = !empty($this->productParam['title-'.$iso][$product_class->id]) ? $this->productTitleEditor->replaceTitleByKey($this->productParam['title-'.$iso][$product_class->id], $this->productTitleEditorValues) : '';
            $name = $this->productTitleEditor->addElementsToTitle($name, $this->settings['title_elements'], $elementsByKey);
            $name = $this->productTitleEditor->addAttributesToTile($name, $this->settings['title_elements'], $this->productAttributes);
            $nameWithoutTransform = $name;
            $name = $this->productTitleEditor->titleTransformer($name, $this->settings);
            $name = $this->productTitleEditor->addText($name, $this->settings);
            $xml = str_replace(REPLACE_COMBINATION.'name-'.$iso, $name, $xml);
            $productTitleList[$iso] = $name;
            $replaceEmptyDescription = '';

            switch ($this->settings['empty_description']) {
                case 1:
                    $replaceEmptyDescription = $nameWithoutTransform;
                    break;
                case 2:
                    $replaceEmptyDescription = $this->settings['empty_description_text'];
                    break;
            }

            $xml = str_replace(REPLACE_COMBINATION . 'description-' . $iso, $replaceEmptyDescription, $xml);
            $xml = str_replace(REPLACE_COMBINATION . 'description_short-' . $iso, $replaceEmptyDescription, $xml);
        }

        if (empty($ean) && $mode == 'sfl') {
            $ean = $this->settings['domain'].'-'.$product_class->id;
        }

        $eanWithPrefix = '';

        if (!empty($product_class->ean13)) {
            $eanWithPrefix = (!empty($this->settings['ean_prefix']) ? $this->settings['ean_prefix'] : '').$product_class->ean13;
        }

        $referenceWithPrefix = '';

        if (!empty($this->productParam['reference'][$product_class->id])) {
            $referenceWithPrefix = (!empty($this->settings['reference_prefix']) ? $this->settings['reference_prefix'] : '').$this->productParam['reference'][$product_class->id];
        }

        $catBlockName = '';

        if ($mode == 'bee') {
            $catBlockName = ' idp="' . $product_class->id . '" idd="0" type="'.($isParent ? 'parent' : 'simple').'"';
        }

        if (!empty($this->settings['max_quantity']) && !empty($this->settings['max_quantity_status']) && $quantity > $this->settings['max_quantity']) {
            $quantity = $this->settings['max_quantity'];
        }

        $xml = $this->removeSalePriceTag($xml, $combination['sale_blmod'], $combination['price_wt_discount_blmod']);

        $product_class->mpn = $this->displayMpnFromAttributeValue($product_class->mpn);

        $xml = str_replace(REPLACE_COMBINATION.'quantity', $quantity, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'minimal_quantity', $product_class->minimal_quantity, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_with_min_qty_blmod', $this->getPriceFormat($feedPrice->getEditedPrice(($salePriceOriginal * $product_class->minimal_quantity), 'sale_with_min_qty_blmod', $this->settings)), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'available_for_order', $availability, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'ean13', $ean, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'isbn', $combination['isbn'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'mpn', $product_class->mpn, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'supplier_reference', !empty($combination['supplier_reference']) ? $combination['supplier_reference'] : '', $xml);
        $xml = str_replace(REPLACE_COMBINATION.'reference', $reference, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_reference', $referenceWithPrefix, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'url', $url, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_url', $urlAdditional, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'wholesale_price', $this->getPriceFormat($feedPrice->getEditedPrice($wholesalePrice, 'product_wholesale_price', $this->settings)), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_blmod', $priceSale, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'sale_tax_excl_blmod', $saleTaxExcl, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'price_wt_discount_blmod', $priceWtDiscount, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'id_product', (!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$product_class->id.(!empty($this->settings['product_id_with_zero']) ? '-0' : ''), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_id_combination', ($mode == 'mir' ? 'PRODUCT' : '').$product_class->id, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'price', $price, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'stock_status', !empty($combination['stock_status']) ? $combination['stock_status'] : '', $xml);
        $xml = str_replace(REPLACE_COMBINATION.'product_id_element', $product_class->id, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'weight', $product_class->weight, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'additional_ean13_with_prefix', $eanWithPrefix, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'cat-block-name', $catBlockName, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'location', $product_class->location, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'upc', $combination['upc'], $xml);
        $xml = str_replace(REPLACE_COMBINATION.'available_date', $product_class->available_date, $xml);
        $xml = str_replace(REPLACE_COMBINATION.'availability_label', $this->availabilityLabel->getStatus($product_class, $quantity), $xml);
        $xml = str_replace(REPLACE_COMBINATION.'is_default_combination', 1, $xml);

        $xml .= $this->getAvailabilityDate($availability);

        if ($mode == 'pub') {
            if ($this->publicGrProducts) {
                $xml .= '<attribute><code>variant-group-id</code><value>' . $this->settings['pref_s'] . $product_class->id . $this->settings['pref_e'] . '</value></attribute>';
                $xml .= '<attribute><code>product-sku</code><value>' . $this->settings['pref_s'] . $product_class->id . $this->settings['pref_e'] . '</value></attribute>';
                $xml .= '<attribute><code>unique-identifier</code><value>' . $this->settings['pref_s'] . 'PRODUCT' . $product_class->id . $this->settings['pref_e'] . '</value></attribute>';
            } else {
                $productTax = $this->getProductTax($product_class->id_tax_rules_group);

                $xml .= '<offer-additional-fields>';

                if (!empty($this->offerAdditionalFields)) {
                    foreach ($this->offerAdditionalFields as $oafKey => $oafVal) {
                        $xml .= '<offer-additional-field><code>' . $oafKey . '</code><value>' . $this->settings['pref_s'] . $oafVal . $this->settings['pref_e'] . '</value></offer-additional-field>';
                    }
                }

                $xml .= '<offer-additional-field><code>offervat</code><value>' . $this->settings['pref_s'] . $productTax . $this->settings['pref_e'] . '</value></offer-additional-field>';
                $xml .= '<offer-additional-field><code>shippingvat</code><value>' . $this->settings['pref_s'] . $productTax . $this->settings['pref_e'] . '</value></offer-additional-field>';
                $xml .= '</offer-additional-fields>';
            }
        }

        if ($mode == 'ceo') {
            $extraParam = '';
            $combinationIdCeo = $product_class->id;

            if ($this->settings['feed_mode'] == 'apl') {
                $combinationIdCeo = $combination['reference'];
            }

            if (!empty($paramsCeneo['weight']) && $product_class->weight > 0) {
                $extraParam = ' weight="'.$product_class->weight.'"';
                unset($paramsCeneo['weight']);
            }

            $addAvailStatus = ' avail="'.$availability.'"';
            $addStockStatus = ' stock="'.$quantity.'"';

            if (empty($this->settings['is_enabled_field']['available_for_order'])) {
                $addAvailStatus = '';
            }

            if (empty($this->settings['is_enabled_field']['quantity'])) {
                $addStockStatus = '';
            }

            $tagParams = 'id="'.$combinationIdCeo.'" url="'.$url.'" price="'.(!empty($affiliatePriceFinal) ? $affiliatePriceFinal : $priceSale).'"'.$addStockStatus.$addAvailStatus;
            $xml = str_replace('<o>', '<o '.$tagParams.$extraParam.'>', $xml);
        }

        $imagesXml = '';
        $imageNo = 1;

        if ($mode == 'mala' || $mode == 'kog' || $mode == 'mir') {
            $images = [];
        }

        if (!empty($images)) {
            if ($mode == 'spa') {
                $imagesXml = '<photos>';
            }

            if ($mode == 'ppa') {
                $imagesXml = '<images>';
            }

            $imageKeys = array_keys($images);

            if ($mode == 'lw') {
                if ($imageNo = 1) {
                    $imagesXml .= str_replace('1>', '>', $images[$imageKeys[0]]);
                    unset($images[$imageKeys[0]]);
                }

                if (empty($images)) {
                    return str_replace(REPLACE_COMBINATION.'image', $imagesXml, $xml);
                }

                $imagesXml .= '<additional_imageurl>';
            }

            if ($mode == 'ceo') {
                $imagesXml .= '<imgs>';
            }

            if ($mode == 'mal') {
                foreach ($images as $image) {
                    $isCover = !empty($imagesXml) ? 'false' : 'true';
                    $imagesXml .= '<MEDIA>'.$image.'<MAIN>'.$isCover.'</MAIN></MEDIA>';

                    if (empty($this->settings['all_images'])) {
                        break;
                    }
                }
            } elseif ($mode == 'ceo') {
                $isCover = false;

                foreach ($images as $image) {
                    $image = str_replace(['<![CDATA[', ']]>'],'', $image);

                    if (empty($isCover)) {
                        $imagesXml .= '<main url="' . strip_tags($image) . '"></main>';
                    } else {
                        $imagesXml .= '<i url="' . strip_tags($image) . '"></i>';
                    }

                    $isCover = true;

                    if (empty($this->settings['all_images'])) {
                        break;
                    }
                }
            } elseif ($mode == 'gla') {
                $isCover = true;

                foreach ($images as $image) {
                    if ($isCover) {
                        $imagesXml .= $image;
                        $isCover = false;
                        continue;
                    }

                    $imagesXml .= str_replace('IMGURL>', 'IMGURL_ALTERNATIVE>', $image);
                }
            } elseif ($mode == 'tro') {
                foreach ($images as $image) {
                    $imagesXml .= str_replace('e>', 'e'.($imageNo == 1 ? '' : $imageNo).'>', $image);
                    $imageNo++;
                }
            } elseif ($mode == 'kos') {
                foreach ($images as $image) {
                    if ($imageNo == 2) {
                        $imagesXml .= '<additional_images>';
                    }

                    if ($imageNo > 1) {
                        $imagesXml .= str_replace('image_url', 'image', $image);
                    } else {
                        $imagesXml .= $image;
                    }

                    $imageNo++;
                }

                if ($imageNo > 2) {
                    $imagesXml .= '</additional_images>';
                }
            } elseif ($mode == 'k') {
                foreach ($images as $image) {
                    $imagesXml .= str_replace('l>', 'l'.($imageNo == 1 ? '' : '_'.$imageNo).'>', $image);
                    $imageNo++;
                }
            } elseif ($mode == 'alz') {
                foreach ($images as $image) {
                    $image = strip_tags(str_replace(['<![CDATA[', ']]>'],'', $image));
                    $imagesXml .= '<PARAM><PARAM_NAME>PICTURE_'.str_pad(($imageNo), 2, '0', STR_PAD_LEFT).'</PARAM_NAME><VAL>' . $image. '</VAL></PARAM>';
                    $imageNo++;
                }
            } elseif ($mode == 'cen') {
                $imagesAdditional = [];

                foreach ($images as $image) {
                    if ($imageNo > 1) {
                        $imagesAdditional[] = strip_tags(str_replace(['<![CDATA[', ']]>'],'', $image));
                        continue;
                    }

                    $imagesXml .= $image;

                    if (empty($this->settings['all_images'])) {
                        break;
                    }

                    $imageNo++;
                }

                $imagesXml .= '<moreImages>'.$this->settings['pref_s'].implode(',', $imagesAdditional).$this->settings['pref_e'].'</moreImages>';
            } elseif ($mode == 'lh') {
                foreach ($images as $image) {
                    $imagesXml .= str_replace('_1>', ($imageNo == 1 ? '' : '_'.$imageNo) . '>', $image);
                    $imageNo++;
                }
            } elseif ($mode == 'ps') {
                foreach ($images as $image) {
                    $imagesXml .= str_replace('_1', '_'.$imageNo, $image);
                    $imageNo++;
                }
            } else {
                if (!empty($this->settings['additional_image_name'])) {
                    foreach ($images as $image) {
                        if ($imageNo > 1) {
                            $imagesXml .= '<'.$this->settings['additional_image_name'].'>' . strip_tags(str_replace(['<![CDATA[', ']]>'], '', $image)) . '</'.$this->settings['additional_image_name'].'>';
                        } else {
                            $imagesXml .= $image;
                        }

                        $imageNo++;
                    }
                } else {
                    foreach ($images as $image) {
                        $imagesXml .= str_replace('1>', (($mode == 'bee' && $imageNo == 1) ? '' : $imageNo) . '>', $image);
                        $imageNo++;

                        if (empty($this->settings['all_images']) || ($mode == 'mm' && $imageNo > 5)) {
                            break;
                        }
                    }
                }
            }

            if ($mode == 'ppa') {
                $imagesXml .= '</images>';
            }

            if ($mode == 'ceo') {
                $imagesXml .= '</imgs>';
            }

            if ($mode == 'spa') {
                $imagesXml .= '</photos>';
            }

            if ($mode == 'lw') {
                $imagesXml .= '</additional_imageurl>';
            }
        }

        $xml = str_replace(REPLACE_COMBINATION.'image', $imagesXml, $xml);

        if (!empty($paramsCeneo) && $mode == 'ceo') {
            $xml .= '<attrs>';

            foreach ($paramsCeneo as $k => $v) {
                $k = str_replace('_', ' ', $k);
                $v = ($v == REPLACE_COMBINATION.'ean13') ? str_replace($v, $ean, $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'reference') ? str_replace($v, $reference, $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'isbn') ? str_replace($v, $combination['isbn'], $v) : $v;
                $v = ($v == REPLACE_COMBINATION.'upc') ? str_replace($v, $combination['upc'], $v) : $v;

                $xml .= '<a name="'.$k.'">'.$this->settings['pref_s'].$v.$this->settings['pref_e'].'</a>';
            }

            $xml .= '</attrs>';
        }

        if ($mode == 'spa' && count($this->langIdAll) > 1) {
            $xml .= '<languages>';

            foreach ($this->langIdAll as $iso) {
                if (empty($this->productLangValues[$product_class->id.$iso])) {
                    continue;
                }

                $xml .= '<language>';
                $xml .= '<code>'.Tools::strtoupper($iso).'</code>';
                $xml .= str_replace(REPLACE_COMBINATION.'name-'.$iso, $productTitleList[$iso], $this->productLangValues[$product_class->id.$iso]);
                $xml .= '<product_color></product_color>';
                $xml .= '<product_price>'.$priceWtDiscount.'</product_price>';
                $xml .= '</language>';
            }

            $xml .= '</languages>';
        }

        return $xml;
    }

    /**
     * Display product attributes
     *
     * @param $fieldGroupedAttributes
     * @param $attributesList
     * @param $id_product_attribute
     * @param $paramsCeneo
     * @return array
     */
    public function getProductGroupedAttributeBranch($fieldGroupedAttributes, $attributesList, $id_product_attribute = 0, $paramsCeneo = array(), $addAllAttributes = false)
    {
        $xmlProduct = '';

        $mode = $this->settings['feed_mode'];

        if ($addAllAttributes && !empty($this->attributesGroupsAll)) {
            $fieldGroupedAttributes = [];
            $attributesList = $this->productAttributes;

            foreach ($this->attributesGroupsAll as $a) {
                $fieldGroupedAttributes[] = [
                    'name' => $a['id_attribute_group'],
                    'title_xml' => $a['public_name'],
                ];
            }
        }

        if (!empty($fieldGroupedAttributes) && !empty($attributesList)) {
            $attributeByGroup = array();

            foreach ($attributesList as $a) {
                if (empty($a['quantity'])) {
                    //continue;
                }

                if (!empty($id_product_attribute)) {
                    if ($id_product_attribute != $a['id_product_attribute']) {
                        continue;
                    }
                }

                $attributeByGroup[$a['id_attribute_group']][] = !empty($this->attributeMapValues[$a['id_attribute_group'].'-'.$a['id_attribute']]) ? $this->attributeMapValues[$a['id_attribute_group'].'-'.$a['id_attribute']] : $a['attribute_name'];
            }

            $paramName = ($mode == 'mal') ? 'NAME' : 'PARAM_NAME';

            if ($mode == 'man') {
                $xmlProduct .= '<params>';
            }

            if ($addAllAttributes) {
                $xmlProduct .= '<attributes>';
            }

            foreach ($fieldGroupedAttributes as $ag) {
                if (!empty($this->settings['is_skip_empty_attribute'])) {
                    if (empty($attributeByGroup[$ag['name']]) && $attributeByGroup[$ag['name']] != '0') {
                        continue;
                    }
                }

                $attributeByGroup[$ag['name']] = !empty($attributeByGroup[$ag['name']]) ? $attributeByGroup[$ag['name']] : array();

                if (!empty($this->settings['attribute_structure_id'])) {
                    $xmlProduct .= $this->attributeFeatureStructure($ag['title_xml'], implode(',', array_unique($attributeByGroup[$ag['name']])));
                } elseif ($addAllAttributes) {
                    if (empty($attributeByGroup[$ag['name']])) {
                        continue;
                    }

                    $xmlProduct .= '<attribute><name>'.$this->settings['pref_s']. $ag['title_xml'].$this->settings['pref_e']. '</name><value>'.$this->settings['pref_s'].implode(',', array_unique($attributeByGroup[$ag['name']])).$this->settings['pref_e'].'</value></attribute>';
                } elseif ($mode == 'gla' || $mode == 'u' || $mode == 'mal' || $mode == 'naj' || $mode == 'zbo' || $mode == 'tov' || $mode == 'st' || $mode == 'alz') {
                    $fieldName = ($mode == 'mal') ? 'VALUE' : 'VAL';
                    $valueList = array_unique($attributeByGroup[$ag['name']]);

                    if (empty($valueList)) {
                        continue;
                    }

                    foreach ($valueList as $v) {
                        if ($mode == 'mal') {
                            $this->productAttributeAndFeatureName[] = $ag['title_xml'];
                        }

                        $xmlProduct .= '<PARAM><' . $paramName . '>' . $ag['title_xml'] . '</' . $paramName . '><' . $fieldName . '>' . $this->settings['pref_s'] . $v . $this->settings['pref_e'] . '</' . $fieldName . '></PARAM>';
                    }
                } elseif ($mode == 'ceo') {
                    $paramsCeneo[$ag['title_xml']] = implode(',', array_unique($attributeByGroup[$ag['name']]));
                } elseif ($mode == 'man') {
                    $xmlProduct .= '<param><param_name>' . $ag['title_xml'] . '</param_name><param_value>' . $this->settings['pref_s'] . implode(',', array_unique($attributeByGroup[$ag['name']])) . $this->settings['pref_e'] . '</param_value></param>';
                } elseif ($mode == 'pub' || ($this->settings['feed_mode_final'] == 'pub' && $this->settings['xml_type'] == 'products')) {
                    $xmlProduct .= '<attribute><code>' . $ag['title_xml'] . '</code><value>' . $this->settings['pref_s'] . implode(',', array_unique($attributeByGroup[$ag['name']])) . $this->settings['pref_e'] . '</value></attribute>';
                } elseif ($mode == 'wum') {
                    $attributesUniqueList = array_unique($attributeByGroup[$ag['name']]);

                    if (!empty($attributesUniqueList)) {
                        foreach ($attributesUniqueList as $n) {
                            $xmlProduct .= '<feature id="' . (!empty($this->featuresKeyByName[$n]) ? $this->featuresKeyByName[$n] : 0) . '">' . $this->settings['pref_s'] . $n . $this->settings['pref_e'] . '</feature>';
                        }
                    }
                } elseif ($mode == 'ho' || $mode == 'ro') {
                    $valueList = array_unique($attributeByGroup[$ag['name']]);

                    if (empty($valueList)) {
                        continue;
                    }

                    foreach ($valueList as $v) {
                        $xmlProduct .= '<param name="' . $ag['title_xml'] . '">' . $this->settings['pref_s'] . $v . $this->settings['pref_e'] . '</param>';
                    }
                } else {
                    if (!empty($this->settings['skroutz_variant_size']) && strtolower($ag['title_xml']) == 'size') {
                        continue;
                    }

                    $xmlProduct .= $this->getDeepTagName($ag['title_xml']) . $this->settings['pref_s'] . implode(',', array_unique($attributeByGroup[$ag['name']])) . $this->settings['pref_e'] . $this->getDeepTagName($ag['title_xml'], true);
                }
            }

            if ($addAllAttributes) {
                $xmlProduct .= '</attributes>';
            }

            if ($mode == 'man') {
                $xmlProduct .= '</params>';
            }
        }

        return array('xml' => $xmlProduct, 'paramsCeneo' => $paramsCeneo);
    }

    public function getProductGroupedAttributeBranchByParentGroup($fieldGroupedAttributes, $attributesByParentGroup, $parentGroupName, $parentGroupId)
    {
        $xmlProduct = '';

        if (!empty($parentGroupId[0]) && empty($this->variantParentGroupId)) {
            $this->variantParentGroupId = $parentGroupId[0];
        }

        $attributesByParentGroup[$parentGroupName][$this->variantParentGroupId][] = $parentGroupName;

        foreach ($attributesByParentGroup[$parentGroupName] as $id => $ag) {
            if (!empty($this->settings['spartoo_size']) && $id == $this->settings['spartoo_size']) {
                continue;
            }

            if (!empty($this->settings['skroutz_variant_size']) && strtolower($fieldGroupedAttributes[$id]['title_xml']) == 'size') {
                continue;
            }

            $xmlProduct .= $this->getDeepTagName($fieldGroupedAttributes[$id]['title_xml']) . $this->settings['pref_s'] .implode(',', array_unique($ag)) . $this->settings['pref_e'] . $this->getDeepTagName($fieldGroupedAttributes[$id]['title_xml'], true);
        }

        return $xmlProduct;
    }

    public function getProductAttributeBranchMru($extra_attributes, $attributesList, $block_name, $one_branch, $id_product_attribute = 0)
    {
        $xmlProduct = '';

        if (empty($extra_attributes) || empty($attributesList)) {
            return $xmlProduct;
        }

        $list = array();
        $row = 0;

        $extra_attributes = array_reverse($extra_attributes);

        foreach ($attributesList as $ag) {
            if (!empty($id_product_attribute)) {
                if ($id_product_attribute != $ag['id_product_attribute']) {
                    continue;
                }
            }

            foreach ($extra_attributes as $a) {
                if (isset($list[$row][$a['title_xml']])) {
                    $row++;
                }

                $list[$row][$a['title_xml']] = ($a['title_xml'] == 'code') ? $ag[$a['name']] : $this->settings['pref_s'].$ag[$a['name']].$this->settings['pref_e'];
            }
        }

        if (empty($list)) {
            return $xmlProduct;
        }

        foreach ($list as $element) {
            $xmlProduct .= '<attribute>';

            foreach ($element as $k => $e) {
                $xmlProduct .= '<'.$k.'>'.$e.'</'.$k.'>';
            }

            $xmlProduct .= '</attribute>';
        }

        return $xmlProduct;
    }

    public function getProductAttributeBranch($extra_attributes, $attributesList, $block_name, $one_branch, $id_product_attribute = 0)
    {
        $xmlProduct = '';

        if (!empty($extra_attributes) && !empty($attributesList)) {
            if (empty($one_branch)) {
                $xmlProduct .= '<'.$block_name['attributes-block-name'].'>';
            }

            $nr = 0;

            foreach ($attributesList as $ag) {
                if (!empty($id_product_attribute)) {
                    if ($id_product_attribute != $ag['id_product_attribute']) {
                        continue;
                    }
                }

                ++$nr;

                if (empty($one_branch)) {
                    $xmlProduct .= '<'.$block_name['attributes-block-name'].'-'.$nr.'>';
                }

                foreach ($extra_attributes as $a) {
                    $xmlProduct .= $this->getDeepTagName($a['title_xml']).$this->settings['pref_s'].$ag[$a['name']].$this->settings['pref_e'].$this->getDeepTagName($a['title_xml'], true);
                }

                if (empty($one_branch)) {
                    $xmlProduct .= '</'.$block_name['attributes-block-name'].'-'.$nr.'>';
                }
            }

            if (empty($one_branch)) {
                $xmlProduct .= '</'.$block_name['attributes-block-name'].'>';
            }
        }

        return $xmlProduct;
    }

    public function attributeName($n)
    {
        $n = trim($n, ':');

        return $n;
    }

    public function getPriceFormat($price = 0)
    {
        if (!empty($this->settings['currencyIdConvert'])) {
            $price = Tools::convertPrice($price, $this->settings['currencyIdConvert']);
        }

        return PriceFormat::convertByType($price, $this->settings['price_format_id']).$this->settings['currencyIso'];
    }

    public function whereType($type)
    {
        if (!empty($type)) {
            return ' AND ';
        }

        return ' WHERE ';
    }

    public function getProductCategories($productId, $langId = false, $defaultCatId = 0, $returnId = false)
    {
        $separator = !empty($this->settings['category_tree_separator']) ? $this->settings['category_tree_separator'] : ' > ';
        $list = array();
        $fieldName = 'name';

        if ($returnId) {
            $fieldName = 'id_category';
            $separator = ',';
        }

        if (!empty($this->settings['category_tree_type'])) {
            if (!$returnId && !empty($defaultCatId)) {
                $path = '';

                if ($this->settings['totalGetPathMethods'] == 2) {
                    $path = Tools::getPath('', $defaultCatId);
                } elseif ($this->settings['totalGetPathMethods'] == 1) {
                    $path = Tools::getPath($defaultCatId);
                }

                $path = trim(html_entity_decode(strip_tags($path), ENT_QUOTES, 'UTF-8'));

                if (!empty($path)) {
                    return str_replace('>', $separator, $path);
                }
            }
        }

        if (!empty($defaultCatId) && $this->isExistsCategoryGetAllParents && !$returnId) {
            $categoryDefault = new Category($defaultCatId, $langId);
            $list = [];
            $allParents = $categoryDefault->getAllParents($langId);

            foreach ($allParents as $category) {
                if ($category->id_parent != 0 && !$category->is_root_category) {
                    $list[] = $category->$fieldName;
                }
            }

            if (!$categoryDefault->is_root_category && !empty($category)) {
                if ($category->id_parent != 0 && !$category->is_root_category) {
                    $list[] = $categoryDefault->$fieldName;
                }
            }

            if (empty($list)) {
                $list[] = $categoryDefault->$fieldName;
            }
        }

        if (!empty($list)) {
            return implode($separator, $list);
        }

        if (empty($defaultCatId) && empty($returnId)) {
            $separator = ',|||,';
        }

        $categories = Db::getInstance()->executeS('SELECT DISTINCT(p.id_category), l.name
            FROM '._DB_PREFIX_.'category_product p
            LEFT JOIN '._DB_PREFIX_.'category c ON
            p.id_category = c.id_category
            LEFT JOIN '._DB_PREFIX_.'category_lang l ON
            (p.id_category = l.id_category AND l.id_lang = "'.(int)$langId.'")
            WHERE p.id_product = "'.(int)$productId.'" AND c.level_depth != "0"
            ORDER BY c.level_depth ASC');

        if (empty($categories)) {
            return false;
        }

        foreach ($categories as $c) {
            $list[] = $c[$fieldName];
        }

        return implode($separator, $list);
    }

    public function getGoogleCatMap($mode, $settings)
    {
        $categoryMap = new CategoryMap();
        $fileName = $categoryMap->getFileNameById($settings['category_map_id']);
        $googleCategory = new GoogleCategoryBlMod($fileName);
        $googleCategories = $googleCategory->getList();

        $categoriesMap = Db::getInstance()->ExecuteS('SELECT `category_id`, `g_category_id`
            FROM '._DB_PREFIX_.'blmod_xml_g_cat 
            WHERE type = "'.pSQL($settings['category_map_id']).'"');

        if (empty($categoriesMap)) {
            return [];
        }

        $useCategoryId = Db::getInstance()->getValue('SELECT c.use_category_id
			FROM '._DB_PREFIX_.'blmod_xml_category_map c
			WHERE c.id = '.pSQL($settings['category_map_id']));

        $googleCategoriesMap = [];

        foreach ($categoriesMap as $m) {
            if ($fileName == 'kogan_ebay-en-EN.txt') {
                $googleCategoriesMap[$m['category_id']] = array(
                    'id' => $m['g_category_id'],
                    'name' => 'ebay:'.$m['g_category_id'],
                );

                continue;
            }

            if ($mode == 'a' || $mode == 'spa') {
                $googleCategoriesMap[$m['category_id']] = array(
                    'id' => $m['g_category_id'],
                    'name' => $m['g_category_id'],
                );

                continue;
            }

            $nameFinal = isset($googleCategories[$m['g_category_id']]) ? $googleCategories[$m['g_category_id']] : '';

            if ($mode == 'mal') {
                $name = explode(' | ', $nameFinal);
                $nameFinal = $name[1];
            }

            if ($fileName == 'car_gr-gr-GR.txt' || !empty($useCategoryId)) {
                $nameFinal = $m['g_category_id'];
            }

            $googleCategoriesMap[$m['category_id']] = [
                'id' => $m['g_category_id'],
                'name' => $nameFinal,
            ];
        }

        return $googleCategoriesMap;
    }

    public function getAvailabilityByMode($product, $feedSettings, $configurationLang)
    {
        if ((!empty($feedSettings['in_stock_text']) || (string)$feedSettings['in_stock_text'] == '0') || !empty($feedSettings['out_of_stock_text'])) {
            return array(
                'in' => (!empty($feedSettings['in_stock_text']) || (string)$feedSettings['in_stock_text'] == '0') ? $feedSettings['in_stock_text'] : '',
                'out' => !empty($feedSettings['out_of_stock_text']) ? $feedSettings['out_of_stock_text'] : '',
                'on_demand' => !empty($feedSettings['on_demand_stock_text']) ? $feedSettings['on_demand_stock_text'] : (!empty($this->PS_LABEL_OOS_PRODUCTS_BOA) ? $this->PS_LABEL_OOS_PRODUCTS_BOA : ''),
            );
        }

        $id = !empty($feedSettings['in_stock_text']) ? $feedSettings['in_stock_text'] : 'in stock';
        $out = !empty($feedSettings['out_of_stock_text']) ? $feedSettings['out_of_stock_text'] : 'out of stock';
        $onDemand = !empty($feedSettings['on_demand_stock_text']) ? $feedSettings['on_demand_stock_text'] : 'on demand';
        $product->additional_delivery_times = !empty($product->additional_delivery_times) ? $product->additional_delivery_times : 0;

        if ($product->additional_delivery_times == 1) {
            return array(
                'in' => !empty($configurationLang['PS_LABEL_DELIVERY_TIME_AVAILABLE']) ? $configurationLang['PS_LABEL_DELIVERY_TIME_AVAILABLE'] : $id,
                'out' => !empty($configurationLang['PS_LABEL_DELIVERY_TIME_OOSBOA']) ? $configurationLang['PS_LABEL_DELIVERY_TIME_OOSBOA'] : $out,
                'on_demand' => !empty($this->PS_LABEL_OOS_PRODUCTS_BOA) ? $this->PS_LABEL_OOS_PRODUCTS_BOA : $onDemand,
            );
        }

        if ($product->additional_delivery_times == 2) {
            return array(
                'in' => !empty($product->delivery_in_stock) ? $product->delivery_in_stock : $id,
                'out' => !empty($product->delivery_out_stock) ? $product->delivery_out_stock : $out,
                'on_demand' => !empty($this->PS_LABEL_OOS_PRODUCTS_BOA) ? $this->PS_LABEL_OOS_PRODUCTS_BOA : $onDemand,
            );
        }

        return array(
            'in' => $id,
            'out' => $out,
            'on_demand' => $onDemand,
        );
    }

    public function getLanguageCodeLong($code = '')
    {
        $list = array(
            'lt' => 'lit',
            'en' => 'eng',
            'es' => 'spa',
            'ru' => 'rus',
            'fr' => 'fra',
            'lv' => 'lav',
            'it' => 'ita',
            'gr' => 'gre',
            'de' => 'deu',
        );

        return !empty($list[$code]) ? $list[$code] : $code;
    }

    public function getDeepTagName($tag = '', $close = false)
    {
        if (strpos($tag, '/') === false) {
            return '<'.($close ? '/' : '').$tag.'>';
        }

        if ($close) {
            return '</vBLMOD></sBLMOD>';
        }

        return '<sBLMOD><nBLMOD>'.str_replace('/', '_lBLMOD_', $tag).'</nBLMOD><vBLMOD>';
    }

    public function fieldSorting($xml, $mode, $xmlProductExtra = '', $isCombinations = false)
    {
        if ($mode == 'cgr') {
            return $this->fieldSortingCarGr($xml, $isCombinations);
        }

        if ($mode == 'vi' && !$isCombinations) {
            return $this->fieldSortingVivino($xml);
        }

        if ($mode != 'mal') {
            return $xml;
        }

        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $values, $index);
        xml_parser_free($p);

        $tagList = [];
        $mainTag = '';

        foreach ($values as $v) {
            if ($v['level'] == 1) {
                $mainTag = $v['tag'];
                continue;
            }

            if ($v['level'] != 2) {
                continue;
            }

            if (in_array($v['tag'], $tagList)) {
                continue;
            }

            $tagList[$v['tag']] = $v['tag'];
        }

        $newXml = '<'.$mainTag.'>';

        $fields = array_unique(array_merge([
            'ID',
            'STAGE',
            'ITEMGROUP_ID',
            'ITEMGROUP_TITLE',
            'CATEGORY_ID',
            'BRAND_ID',
            'TITLE',
            'SHORTDESC',
            'LONGDESC',
            'PRIORITY',
            'PACKAGE_SIZE',
            'BARCODE',
            'PRICE',
            'VAT',
            'RRP',
            'PARAM',
            'VARIABLE_PARAMS',
            'MEDIA',
            'PROMOTION',
            'DIMENSIONS',
            'LABEL',
            'DELIVERY_DELAY',
            'FREE_DELIVERY',
        ], $tagList));

        $xmlBottomDeliveryDelay = '';
        $xmlBottomFreeDelivery = '';

        foreach ($fields as $s) {
            if ($s == 'ITEMGROUP_ID' && !$isCombinations) {
                continue;
            }

            preg_match_all("'<".$s.">(.*?)</".$s.">'si", $xml, $rows);

            if ($s == 'DELIVERY_DELAY' && !empty($rows[0])) {
                $xmlBottomDeliveryDelay = $rows[0][0];
                continue;
            }

            if ($s == 'FREE_DELIVERY' && !empty($rows[0])) {
                $xmlBottomFreeDelivery = $rows[0][0];
                continue;
            }

            foreach ($rows[0] as $r) {
                $newXml .= $r;
            }
        }

        $closeTag = '</'.$mainTag.'>';

        if ($isCombinations && strpos($xml, $closeTag) !== false) {
            $closeTag = '';
        }

        if ($isCombinations && strpos($xml, REPLACE_COMBINATION.'image') !== false) {
            $newXml .= REPLACE_COMBINATION.'image';
        }

        $newXml = str_replace('PARAM_replace', 'PARAM', $newXml);

        return $newXml.$xmlProductExtra.$xmlBottomDeliveryDelay.$xmlBottomFreeDelivery.$closeTag;
    }

    public function fieldSortingVivino($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $values, $index);
        xml_parser_free($p);

        $tagList = [];
        $mainTag = '';

        foreach ($values as $v) {
            if ($v['level'] == 2 && $v['type'] == 'open') {
                $mainTag = Tools::strtolower($v['tag']);
                continue;
            }

            if ($v['level'] != 3) {
                continue;
            }

            $tagList[] = Tools::strtolower($v['tag']);
        }

        $fields = array_unique(array_merge([
            'producer',
            'wine-name',
            'appellation',
            'vintage',
            'country',
            'color',
            'description',
            'alcohol',
            'producer-address',
        ], $tagList));

        $newXml = '';

        foreach ($fields as $s) {
            preg_match_all("'<".$s.">(.*?)</".$s.">'si", $xml, $rows);

            if (empty($rows[0])) {
                continue;
            }

            foreach ($rows[0] as $r) {
                $newXml .= $r;
            }
        }

        preg_match("'<".$mainTag.">(.*?)</".$mainTag.">'si", $xml, $extrasBranch);

        $xml = str_replace($extrasBranch[0], '', $xml);
        $xml = str_replace('</product>', '<'.$mainTag.'>'.$newXml.'</'.$mainTag.'></product>', $xml);

        return $xml;
    }

    public function fieldSortingCarGr($xml, $isCombinations = false)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $values, $index);
        xml_parser_free($p);


        $tagList = [];
        $mainTag = '';

        foreach ($values as $v) {
            if ($v['level'] == 1 && $v['type'] == 'open') {
                $mainTag = Tools::strtolower($v['tag']);
                continue;
            }

            if ($v['level'] != 2) {
                continue;
            }

            $tagList[] = Tools::strtolower($v['tag']);
        }

        $fields = array_unique(array_merge([
            'unique_id',
            'manufacturer_number',
            'aftermarket_number',
            'title',
            'description',
            'category_id',
            'price',
            'makemodels',
            'photos',
            'sblmod',
            'condition',
            'external_link',
        ], $tagList));

        $newXml = '';

        foreach ($fields as $s) {
            preg_match_all("'<".$s.">(.*?)</".$s.">'si", $xml, $rows);

            if (empty($rows[0])) {
                continue;
            }

            foreach ($rows[0] as $r) {
                $newXml .= $r;
            }
        }

        $closeTag = '</'.$mainTag.'>';

        if ($isCombinations) {
            $closeTag = '';
        }

        return '<'.$mainTag.'>'.$newXml.$closeTag;
    }

    public function getFrontFeatures($langId, $productId, $multistoreId)
    {
        if (!$this->isFeatureActive) {
            return array();
        }

        $multistoreId = !empty($multistoreId) ? (int)$multistoreId : 1;

        return Db::getInstance()->executeS('SELECT fl.`name`, fvl.`value`, pf.id_feature, fvl.`id_feature_value`
            FROM '._DB_PREFIX_.'feature_product pf 
            LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON 
            (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$langId.') 
            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON 
            (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$langId.') 
            LEFT JOIN '._DB_PREFIX_.'feature f ON 
            (f.id_feature = pf.id_feature AND fl.id_lang = '.(int)$langId.') 
            INNER JOIN '._DB_PREFIX_.'feature_shop feature_shop ON 
            (feature_shop.id_feature = f.id_feature AND feature_shop.id_shop = '.(int)$multistoreId.') 
            WHERE pf.id_product = '.(int)$productId);
    }

    public function getAllCategories($languages, $multistoreId)
    {
        $l_where_cat = '';

        foreach ($languages as $ll) {
            $l_where_cat .= 'OR c.`id_lang`='.(int)$ll['name'].' ';

            if ($this->settings['feed_mode'] == 'ep') {
                break;
            }
        }

        $l_where_cat = '('.trim($l_where_cat, 'OR').')';

        if (_PS_VERSION_ >= '1.5') {
            $l_where_cat .= ' AND id_shop = "'.(!empty($multistoreId) ? (int)$multistoreId : "1").'"';
        }

        return Db::getInstance()->ExecuteS('SELECT c.`id_category`, c.`name`, c.`id_lang`, l.iso_code, cr.id_parent
            FROM '._DB_PREFIX_.'category_lang c
            LEFT JOIN '._DB_PREFIX_.'category cr ON
            cr.id_category = c.id_category
            INNER JOIN '._DB_PREFIX_.'lang l ON
            l.id_lang = c.id_lang
            WHERE '.$l_where_cat.'
            ORDER BY c.`id_category`');
    }

    public function getAllAttributes($langId)
    {
        return Db::getInstance()->ExecuteS('SELECT al.id_attribute, al.name FROM '._DB_PREFIX_.'attribute_lang al WHERE al.id_lang = '.(int)$langId);
    }

    public function loadProductFeatures($langId, $productId, $multistoreId)
    {
        $features = $this->getFrontFeatures($langId, $productId, $multistoreId);
        $this->productFeatures = [];
        $this->productFeaturesWithId = [];
        $this->productFeaturesWithValue = [];

        if (empty($features)) {
            return false;
        }

        foreach ($features as $f) {
            $this->productFeatures[$f['id_feature']] = !empty($this->featureMapValues[$f['id_feature'].'-'.$f['id_feature_value']]) ? $this->featureMapValues[$f['id_feature'].'-'.$f['id_feature_value']] : $f['value'];
            $this->productFeaturesWithId[$f['id_feature']] = $f['id_feature_value'];
            $this->productFeaturesWithValue[$f['name']] = $this->productFeatures[$f['id_feature']];
        }

        return $this->productFeatures;
    }

    public function getSpartooSizeBlock($combinations, $defaultEan13 = '')
    {
        $spartooSizeList = [];

        if (empty($combinations)) {
            return '';
        }

        if (!empty($this->groupedAttributesByParent['childValues']) && !empty($this->groupedAttributesByParent['parentName'])) {
            foreach ($this->groupedAttributesByParent['childValues'][$this->groupedAttributesByParent['parentName']] as $a) {
                $groupedAttributes = $a;
                break;
            }
        }

        if (empty($groupedAttributes)) {
            foreach ($combinations as $c) {
                if ($c['quantity'] < 1 && !empty($this->settings['only_in_stock'])) {
                    continue;
                }

                $sizeName = '';

                foreach ($this->productAttributes as $a) {
                    if ($c['id_product_attribute'] == $a['id_product_attribute'] && $a['id_attribute_group'] == $this->settings['spartoo_size']) {
                        $sizeName = !empty($this->attributeMapValues[$this->settings['spartoo_size'] . '-' . $a['id_attribute']]) ? $this->attributeMapValues[$this->settings['spartoo_size'] . '-' . $a['id_attribute']] : $a['attribute_name'];
                        break 1;
                    }
                }

                if (empty($spartooSizeList[$sizeName])) {
                    $spartooSizeList[$sizeName] = [
                        'size_name' => $sizeName,
                        'size_quantity' => $c['quantity'],
                        'size_reference' => $c['reference'],
                        'ean13' => !empty($c['ean13']) ? $c['ean13'] : $defaultEan13,
                    ];
                } else {
                    $spartooSizeList[$sizeName]['size_quantity'] += $c['quantity'];
                }
            }
        } else {
            foreach ($this->productAttributes as $aParent) {
                if ($aParent['id_attribute_group'] == $this->settings['merge_attributes_parent']) {
                    foreach ($this->productAttributes as $aChild) {
                        if ($aChild['quantity'] < 1 && !empty($this->settings['only_in_stock'])) {
                            continue;
                        }

                        if ($aParent['attribute_name'] != $this->groupedAttributesByParent['parentName']) {
                            continue;
                        }

                        if ($aParent['id_attribute_group'] == $aChild['id_attribute_group'] || !in_array($aChild['attribute_name'], $groupedAttributes)) {
                            continue;
                        }

                        if ($aParent['id_product_attribute'] == $aChild['id_product_attribute']) {
                            $aChild['size_name'] = $aChild['attribute_name'];
                            $aChild['size_quantity'] = $aChild['quantity'];
                            $aChild['size_reference'] = $aChild['reference'];
                            $spartooSizeList[] = $aChild;
                        }
                    }
                }
            }
        }

        $xmlProduct = '<size_list>';

        if (!empty($spartooSizeList)) {
            foreach ($spartooSizeList as $s) {
                $xmlProduct .= '<size>';
                $xmlProduct .= '<size_name>'.$s['size_name'].'</size_name>';
                $xmlProduct .= '<size_quantity>'.$s['size_quantity'].'</size_quantity>';
                $xmlProduct .= '<size_reference>'.$s['size_reference'].'</size_reference>';
                $xmlProduct .= '<ean>'.$s['ean13'].'</ean>';
                $xmlProduct .= '</size>';
            }
        }

        $xmlProduct .= '</size_list>';

        return $xmlProduct;
    }

    protected function calculateAffiliatePrices(
        $salePrice,
        $basePrice,
        $shippingPrice,
        $priceWithoutDiscount,
        $wholesalePrice,
        $affiliate,
        $product
    ) {
        if (empty($salePrice)) {
            return $this->getPriceFormat('0.00');
        }

        if (empty($affiliate['affiliate_formula'])) {
            return $this->getPriceFormat('0.00');
        }

        $formula = $affiliate['affiliate_formula'];

        if (!empty($affiliate['category_id_list'])) {
            $affiliateCategoryList = explode(',', $affiliate['category_id_list']);

            if (!empty($affiliate['category_type'])) {
                $productAllCategories = $this->getProductCategories($product->id, $this->langId, $product->id_category_default, true);
                $productAllCategories = !empty($productAllCategories) ? explode(',', $productAllCategories) : [];
                $hasCategory = array_intersect($affiliateCategoryList, $productAllCategories);

                if (empty($hasCategory)) {
                    return $this->getPriceFormat($salePrice);
                }
            } else {
                if (!in_array($product->id_category_default, $affiliateCategoryList)) {
                    return $this->getPriceFormat($salePrice);
                }
            }
        }

        list($shippingPrice) = explode(' ', $shippingPrice);

        $formula = str_replace('wholesale_price', $wholesalePrice, $formula);
        $formula = str_replace('price_without_discount', $priceWithoutDiscount, $formula);
        $formula = str_replace('base_price', $basePrice, $formula);
        $formula = str_replace('sale_price', $salePrice, $formula);
        $formula = str_replace('shipping_price', $shippingPrice, $formula);
        $formula = str_replace('tax_price', ($salePrice - $basePrice), $formula);
        $formula = str_replace('price_sale', $salePrice, $formula);
        $formula = str_replace('price', $salePrice, $formula);

        $parser = new FormulaParser($formula);

        return $this->getPriceFormat(number_format($parser->getResultValue(), 2, '.', ''));
    }

    public function isExcludeByMinimumOrderQuantity($quantity = 0)
    {
        $from = (int)$this->settings['exclude_minimum_order_qty_from'];
        $to = (int)$this->settings['exclude_minimum_order_qty_to'];

        if (empty($from) && empty($to)) {
            return false;
        }

        if ($quantity >= $from && $quantity <= $to) {
            return true;
        }

        if ($quantity >= $from && empty($to)) {
            return true;
        }

        if ($quantity <= $to && empty($from)) {
            return true;
        }

        return false;
    }

    public function isEnabledField($feedId, $name, $table)
    {
        return (int)Db::getInstance()->getValue('SELECT f.status
            FROM '._DB_PREFIX_.'blmod_xml_fields f
            WHERE f.category = '.(int)$feedId.' AND f.name = "'.htmlspecialchars($name, ENT_QUOTES).'" AND f.table = "'.htmlspecialchars($table, ENT_QUOTES).'"');
    }

    public function getFieldName($feedId, $name, $table)
    {
        return Db::getInstance()->getValue('SELECT f.title_xml
            FROM '._DB_PREFIX_.'blmod_xml_fields f
            WHERE f.category = '.(int)$feedId.' AND f.name = "'.htmlspecialchars($name, ENT_QUOTES).'" AND f.table = "'.htmlspecialchars($table, ENT_QUOTES).'"');
    }

    public function getTaxRation($product_class, $address, $context)
    {
        /*
        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int) $product_class->id, $context));
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        return $product_tax_calculator->getTotalRate();
        */

        return $this->getProductTax($product_class->id_tax_rules_group);
    }

    protected function getAvailabilityDate($availability)
    {
        if (empty($this->settings['is_enabled_field']['available_for_order']) ||
            $this->settings['feed_mode_final'] != 'g' ||
            !in_array($availability, ['preorder', 'backorder', 'on demand',])) {
            return '';
        }

        return '<g:availability_date>'.date('Y-m-d H:i:s', strtotime('+7days')).'T12:00-0800</g:availability_date>';
    }

    protected function getRelatedProducts($productId)
    {
        return Db::getInstance()->ExecuteS('SELECT a.id_product_2
            FROM '._DB_PREFIX_.'accessory a
            WHERE a.id_product_1 = '.(int)$productId);
    }

    protected function addVariants($xmlProduct, $product_class, $isAvailableWhenOutOfStock, $availabilityName, $fieldGroupedAttributes)
    {
        if (empty($this->settings['skroutz_variant_size'])) {
            return $xmlProduct;
        }

        $link = new Link();
        $feedPrice = new FeedPrice();
        $productCombination = new ProductCombinations();

        $combinations = $productCombination->getCombinations($product_class, $this->langId);

        $sizeList = [];

        if (empty($combinations)) {
            return $xmlProduct;
        }

        $childElementId = '';

        if (!empty($this->groupedAttributesByParent['childValues']) && !empty($this->groupedAttributesByParent['parentName'])) {
            foreach ($this->groupedAttributesByParent['childValues'][$this->groupedAttributesByParent['parentName']] as $k => $a) {
                $childElementId = $k;
                $groupedAttributes = $a;
                break;
            }
        }

        $uniqueAttributes = [];

        foreach ($this->productAttributes as $a) {
            $uniqueAttributes[] = $a['id_attribute_group'];
        }

        $uniqueAttributes = array_unique($uniqueAttributes);
        $this->isTheSameAttribute = (count($uniqueAttributes) == 1);

        if (empty($groupedAttributes)) {
            foreach ($combinations as $c) {
                if ($c['quantity'] < 1 && !empty($this->settings['only_in_stock'])) {
                    continue;
                }

                $sizeName = '';

                if (!empty($this->isTheSameAttribute)) {
                    foreach ($this->productAttributes as $a) {
                        if ($c['id_product_attribute'] == $a['id_product_attribute']) {
                            $sizeName = $a['attribute_name'];
                            break 1;
                        }
                    }
                } else {
                    foreach ($this->productAttributes as $a) {
                        if ($c['id_product_attribute'] == $a['id_product_attribute'] && $a['id_attribute_group'] == $this->settings['skroutz_variant_size']) {
                            $sizeName = !empty($this->attributeMapValues[$this->settings['skroutz_variant_size'] . '-' . $a['id_attribute']]) ? $this->attributeMapValues[$this->settings['skroutz_variant_size'] . '-' . $a['id_attribute']] : $a['attribute_name'];
                            break 1;
                        }
                    }
                }

                if (empty($sizeList[$sizeName])) {
                    $sizeList[$sizeName] = [
                        'id_product_attribute' => $c['id_product_attribute'],
                        'size_name' => $sizeName,
                        'size_quantity' => $c['quantity'],
                        'size_reference' => $c['reference'],
                        'mpn' => $c['mpn'],
                        'ean13' => !empty($c['ean13']) ? $c['ean13'] : '',
                    ];
                } else {
                    $sizeList[$sizeName]['size_quantity'] += $c['quantity'];
                }
            }
        } else {
            foreach ($this->productAttributes as $aParent) {
                if (in_array($aParent['id_attribute_group'], $this->settings['merge_attributes_parent_id'])) {
                    foreach ($this->productAttributes as $aChild) {
                        if ($aChild['quantity'] < 1 && !empty($this->settings['only_in_stock'])) {
                            continue;
                        }

                        if ($aParent['attribute_name'] != $this->groupedAttributesByParent['parentName']) {
                            continue;
                        }

                        if ($aParent['id_attribute_group'] == $aChild['id_attribute_group'] || !in_array($aChild['attribute_name'], $groupedAttributes)) {
                            continue;
                        }

                        if ($aParent['id_product_attribute'] == $aChild['id_product_attribute']) {
                            $aChild['size_name'] = $aChild['attribute_name'];
                            $aChild['size_quantity'] = $aChild['quantity'];
                            $aChild['size_reference'] = $aChild['reference'];
                            $sizeList[] = $aChild;
                        }

                        $this->variantParentGroupId = $aParent['id_attribute_group'];
                    }
                } else {
                    $childElementId = $aParent['id_attribute_group'];
                }
            }
        }

        $xmlProduct .= '<variations>';
        $allSizes = [];

        if (!empty($sizeList)) {
            $childElementName = 'size';

            foreach ($fieldGroupedAttributes as $a) {
                if ($a['name'] == $childElementId) {
                    $childElementName = $a['title_xml'];
                }
            }

            foreach ($sizeList as $s) {
                $extraUrl = !empty($this->extraFieldByName['product_url_utm_blmod']) ? htmlspecialchars_decode($this->extraFieldByName['product_url_utm_blmod'], ENT_QUOTES) : '';
                $url = $link->getProductLink($product_class, null, null, null, $this->langId, null, $s['id_product_attribute'], Configuration::get('PS_REWRITING_SETTINGS'), false, true).$extraUrl;
                $salePrice = $feedPrice->getEditedPrice($product_class->getPriceStatic($product_class->id, true, $s['id_product_attribute'], (empty($this->settings['price_rounding_type']) ? 6 : 2)), 'sale_blmod', $this->settings);

                $combinationAvailability = $availabilityName['out'];

                if ($product_class->available_for_order == 1 || $product_class->online_only == 1) {
                    if ($s['size_quantity'] > 0) {
                        $combinationAvailability = $availabilityName['in'];
                    } else {
                        if ($isAvailableWhenOutOfStock) {
                            $combinationAvailability = !empty($availabilityName['on_demand']) ? $availabilityName['on_demand'] : $availabilityName['in'];
                        }
                    }
                }

                $xmlProduct .= '<variation>';
                $xmlProduct .= '<variationid>'.(!empty($this->settings['product_id_prefix']) ? $this->settings['product_id_prefix'] : '').$product_class->id.'-'.$s['id_product_attribute'].'</variationid>';
                $xmlProduct .= '<link>'.$this->settings['pref_s'].$url.$this->settings['pref_e'].'</link>';
                $xmlProduct .= '<availability>'.$combinationAvailability.'</availability>';
                $xmlProduct .= '<manufacturersku>'.$s['mpn'].'</manufacturersku>';
                $xmlProduct .= '<ean>'.$s['ean13'].'</ean>';
                $xmlProduct .= '<price_with_vat>'.$this->getPriceFormat($salePrice).'</price_with_vat>';
                $xmlProduct .= '<'.$childElementName.'>'.$s['size_name'].'</'.$childElementName.'>';
                $xmlProduct .= '<quantity>'.$s['size_quantity'].'</quantity>';
                $xmlProduct .= '</variation>';
                $allSizes[] = $s['size_name'];
            }
        }

        $xmlProduct .= '</variations>';

        if (!empty($allSizes)) {
            $xmlProduct .= '<'.$childElementName.'>'.implode(',', $allSizes).'</'.$childElementName.'>';
        }

        return $xmlProduct;
    }

    /**
     * Remove sale price tag
     *
     * @param string $xml
     * @param float $combinationSalePrice
     * @param float $priceWithoutDiscount
     * @return string
     */
    protected function removeSalePriceTag($xml, $combinationSalePrice, $priceWithoutDiscount)
    {
        if (empty($this->settings['hide_sale_price'])) {
            return $xml;
        }

        if (abs($combinationSalePrice - $priceWithoutDiscount) < 0.02) {
            $xml = str_replace('<'.$this->settings['field_name']['price_sale_blmod'].'>' . $this->settings['pref_s'] . REPLACE_COMBINATION . 'sale_blmod' . $this->settings['pref_e'] . '</'.$this->settings['field_name']['price_sale_blmod'].'>', '', $xml);
        }

        return $xml;
    }

    protected function getVirtualProductsByProductId($productId)
    {
        return Db::getInstance()->ExecuteS('SELECT d.*
            FROM '._DB_PREFIX_.'product_download d
            WHERE d.id_product = "'.(int)$productId.'"
            ORDER BY d.id_product_download ASC');
    }

    protected function displayMpnFromAttributeValue($mpn)
    {
        if (empty($mpn)) {
            return '';
        }

        if (empty($this->settings['mpn_from_attribute'])) {
            return $mpn;
        }

        return Db::getInstance()->getValue('SELECT a.name
            FROM ' . _DB_PREFIX_ . 'attribute_lang a
            WHERE a.id_attribute = "'.(int)$mpn.'" AND a.`id_lang` = '.(int)$this->langId);
    }

    protected function addSpecificFixedPrice($salePrice, $productId, $productAttributeId = null)
    {
        if (empty($this->settings['is_use_specific_fixed_price'])) {
            return $salePrice;
        }

        $specificPrice = new SpecificPriceCore();
        $specificPriceAll = $specificPrice->getByProductId($productId, $productAttributeId);

        if (empty($specificPriceAll)) {
            return $salePrice;
        }

        $taxRate = $this->productParam['tax_rate'][$productId];

        if (empty($taxRate)) {
            return $salePrice;
        }

        foreach ($specificPriceAll as $s) {
            if ($s['price'] >= 0.0001) {
                return Tools::ps_round(($s['price'] * (1 + ($taxRate / 100))), 2).$this->settings['currencyIso'];
            }
        }

        return $salePrice;
    }

    protected function attributeFeatureStructure($name, $value)
    {
        switch ($this->settings['attribute_structure_id']) {
            case 1:
                return '<'.$name.'>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</'.$name.'>';
            case 2:
                return '<atribute_name>'.$name.'</atribute_name><attribute_value>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</attribute_value>';
            case 3:
                return '<param name="'.$name.'">'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</param>';
            case 4:
                return '<PARAM><PARAM_NAME>'.$name.'</PARAM_NAME><VAL>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</VAL></PARAM>';
            case 5:
                return '<PARAM><NAME>'.$name.'</NAME><VALUE>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</VALUE></PARAM>';
            case 6:
                return '<attribute><code>'.$name.'</code><value>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</value></attribute>';
        }

        return '<'.$name.'>'.$this->settings['pref_s'].$value.$this->settings['pref_e'].'</'.$name.'>';
    }
}
