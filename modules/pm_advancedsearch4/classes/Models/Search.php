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

namespace AdvancedSearch\Models;
if (!defined('_PS_VERSION_')) {
    exit;
}
use Db;
use Hook;
use Shop;
use Tools;
use Module;
use Context;
use Validate;
use ObjectModel;
use AdvancedSearch\Core;
use AdvancedSearch\SearchEngineDb;
use AdvancedSearch\SearchEngineUtils;
use AdvancedSearch\Traits\SupportsSeoPages;
class Search extends ObjectModel
{
    use SupportsSeoPages;
    public $id;
    public $id_hook;
    public $active = 1;
    public $internal_name;
    public $description;
    public $title;
    public $css_classes;
    public $search_results_selector_css;
    public $display_nb_result_on_blc = 0;
    public $display_nb_result_criterion = 1;
    public $remind_selection;
    public $show_hide_crit_method;
    public $filter_by_emplacement = 1;
    public $search_on_stock = 0;
    public $hide_empty_crit_group;
    public $search_method;
    public $step_search = 0;
    public $step_search_next_in_disabled;
    public $position;
    public $products_per_page;
    public $products_order_by;
    public $products_order_way;
    public $keep_category_information;
    public $display_empty_criteria = 0;
    public $recursing_indexing = 1;
    public $search_results_selector;
    public $smarty_var_name;
    public $insert_in_center_column;
    public $unique_search;
    public $reset_group;
    public $scrolltop_active = 1;
    public $id_category_root = 0;
    public $redirect_one_product = 1;
    public $priority_on_combination_image = 1;
    public $add_anchor_to_url = 1;
    public $hide_criterions_group_with_no_effect;
    protected $tables = [
        'pm_advancedsearch',
        'pm_advancedsearch_lang',
    ];
    protected $table = 'pm_advancedsearch';
    public $identifier = 'id_search';
    public static $definition = [
        'table' => 'pm_advancedsearch',
        'primary' => 'id_search',
        'multishop' => true,
        'multilang' => true,
        'fields' => [
            'id_hook' => ['type' => self::TYPE_INT, 'required' => true, 'validate' => 'isInt'],
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'internal_name' => ['type' => self::TYPE_STRING, 'size' => 255],
            'css_classes' => ['type' => self::TYPE_STRING, 'size' => 255],
            'search_results_selector' => ['type' => self::TYPE_STRING, 'size' => 255],
            'display_nb_result_on_blc' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'display_nb_result_criterion' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'remind_selection' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'show_hide_crit_method' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'filter_by_emplacement' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'search_on_stock' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'hide_empty_crit_group' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'search_method' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'priority_on_combination_image' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'products_per_page' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'products_order_by' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'products_order_way' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'step_search' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'step_search_next_in_disabled' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'keep_category_information' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'display_empty_criteria' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'recursing_indexing' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'smarty_var_name' => ['type' => self::TYPE_STRING, 'size' => 64],
            'insert_in_center_column' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'reset_group' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'unique_search' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'scrolltop_active' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_category_root' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'redirect_one_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'add_anchor_to_url' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'hide_criterions_group_with_no_effect' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true],
        ],
    ];
    public $categories_association;
    public $cms_association;
    public $products_association;
    public $product_categories_association;
    public $manufacturers_association;
    public $suppliers_association;
    public $special_pages_association;
    public function __construct($idSearch = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], ['type' => 'shop']);
        parent::__construct($idSearch, $idLang, $idShop);
    }
    public function save($nullValues = false, $autoDate = false)
    {
        SearchEngineUtils::setLocalStorageCacheKey();
        if ($this->id_hook != -1) {
            if ($this->id_hook == Hook::getIdByName('displayHome')) {
                $this->insert_in_center_column = 1;
            } else {
                $this->insert_in_center_column = 0;
            }
        }
        if (!empty($this->id) && !$this->filter_by_emplacement) {
            $this->id_category_root = 0;
            SearchEngineDb::execute('
                UPDATE `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$this->id . '`
                SET `context_type`="2"
                WHERE `criterion_group_type`="category"
            ');
        }
        $result = parent::save($nullValues, $autoDate);
        $add_associations = true;
        if ((int)$this->id_hook == (int)Hook::getIdByName('displayAdvancedSearch4')) {
            $add_associations = false;
        }
        if (Tools::getIsset('categories_association') && $add_associations) {
            $this->addAssociations($this->categories_association, 'pm_advancedsearch_category', 'id_category', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_category');
        }
        if (Tools::getIsset('cms_association') && $add_associations) {
            $this->addAssociations($this->cms_association, 'pm_advancedsearch_cms', 'id_cms', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_cms');
        }
        if (Tools::getIsset('products_association') && $add_associations) {
            $this->addAssociations($this->products_association, 'pm_advancedsearch_products', 'id_product', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_products');
        }
        if (Tools::getIsset('product_categories_association') && $add_associations) {
            $this->addAssociations($this->product_categories_association, 'pm_advancedsearch_products_cat', 'id_category', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_products_cat');
        }
        if (Tools::getIsset('manufacturers_association') && $add_associations) {
            $this->addAssociations($this->manufacturers_association, 'pm_advancedsearch_manufacturers', 'id_manufacturer', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_manufacturers');
        }
        if (Tools::getIsset('suppliers_association') && $add_associations) {
            $this->addAssociations($this->suppliers_association, 'pm_advancedsearch_suppliers', 'id_supplier', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_suppliers');
        }
        if (Tools::getIsset('special_pages_association') && $add_associations) {
            $this->addAssociations($this->special_pages_association, 'pm_advancedsearch_special_pages', 'page', true);
        } elseif (Tools::isSubmit('submitSearchVisibility')) {
            $this->cleanAssociation('pm_advancedsearch_special_pages');
        }
        return $result;
    }
    public function duplicate(int $idShop = null, array $importData = [])
    {
        SearchEngineUtils::setLocalStorageCacheKey();
        $obj = parent::duplicateObject();
        if (!Validate::isLoadedObject($obj)) {
            return false;
        }
        $moduleInstance = Module::getInstanceByName(_PM_AS_MODULE_NAME_);
        if ((int)$idShop) {
            $obj->internal_name = $this->internal_name;
            $obj->active = $this->active;
        } else {
            $translated_string = $moduleInstance->translateMultiple('duplicated_from');
            $obj->internal_name = sprintf($translated_string[Context::getContext()->language->id], $this->internal_name);
            $obj->active = 0;
        }
        $obj->title = $this->title;
        $obj->description = $this->description;
        $obj->update();
        $ret = $moduleInstance->installDBCache((int)$obj->id);
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$obj->id . '` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$obj->id . '_lang` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_lang`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$obj->id . '_link` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_link`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$obj->id . '_list` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_list`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$obj->id . '` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$this->id . '`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$obj->id . '_lang` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$this->id . '_lang`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_' . (int)$obj->id . '` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_' . (int)$this->id . '`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_criterion_' . (int)$obj->id . '` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_criterion_' . (int)$this->id . '`');
        $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_product_price_' . (int)$obj->id . '` SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_product_price_' . (int)$this->id . '`');
        SearchEngineDb::execute('UPDATE `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$obj->id . '` SET `id_search` = ' . (int)$obj->id);
        $criterionsGroupsImages = SearchEngineDb::query('SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$obj->id . '_lang` WHERE `icon`!=""');
        if ($criterionsGroupsImages && Core::isFilledArray($criterionsGroupsImages)) {
            foreach ($criterionsGroupsImages as $criterionGroupImage) {
                if ($criterionGroupImage['icon'] && Tools::file_exists_cache(_PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions_group/' . $criterionGroupImage['icon'])) {
                    $newImageName = uniqid(Core::$modulePrefix . mt_rand()) . '.' . Core::getFileExtension($criterionGroupImage['icon']);
                    if (copy(_PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions_group/' . $criterionGroupImage['icon'], _PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions_group/' . $newImageName)) {
                        Db::getInstance()->update(
                            'pm_advancedsearch_criterion_group_' . (int)$obj->id . '_lang',
                            [
                                'icon' => $newImageName,
                            ],
                            'id_criterion_group = ' . (int)$criterionGroupImage['id_criterion_group'] . ' AND id_lang = ' . (int)$criterionGroupImage['id_lang']
                        );
                    }
                }
            }
        }
        $criterionsImages = SearchEngineDb::query('SELECT * FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$obj->id . '_lang` WHERE `icon`!=""');
        if ($criterionsImages && Core::isFilledArray($criterionsImages)) {
            foreach ($criterionsImages as $criterionsImage) {
                if ($criterionsImage['icon'] && Tools::file_exists_cache(_PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions/' . $criterionsImage['icon'])) {
                    $newImageName = uniqid(Core::$modulePrefix . mt_rand()) . '.' . Core::getFileExtension($criterionsImage['icon']);
                    if (copy(_PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions/' . $criterionsImage['icon'], _PS_ROOT_DIR_ . '/modules/' . _PM_AS_MODULE_NAME_ . '/search_files/criterions/' . $newImageName)) {
                        Db::getInstance()->update(
                            'pm_advancedsearch_criterion_' . (int)$obj->id . '_lang',
                            [
                                'icon' => $newImageName,
                            ],
                            'id_criterion = ' . (int)$criterionsImage['id_criterion'] . ' AND id_lang = ' . (int)$criterionsImage['id_lang']
                        );
                    }
                }
            }
        }
        if ((int)$idShop) {
            $fromBackOfficeUserAction = Tools::getValue('submitSearchDuplicate') || Tools::getValue('pm_duplicate_obj');
            $categoryListCondition = '';
            if (isset($importData['categoryList']) && is_array($importData['categoryList']) && count($importData['categoryList'])) {
                $categoryListCondition = ' AND `id_category` IN (' . implode(',', array_map('intval', $importData['categoryList'])) . ')';
            }
            $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_category` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_category` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_category` WHERE `id_search` = ' . (int)$this->id . $categoryListCondition . ')');
            $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_products_cat` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_category` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_products_cat` WHERE `id_search` = ' . (int)$this->id . $categoryListCondition . ')');
            $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_products` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_product` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_products` WHERE `id_search` = ' . (int)$this->id . ')');
            $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_special_pages` (SELECT "' . (int)$obj->id . '" AS `id_search`, `page` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_special_pages` WHERE `id_search` = ' . (int)$this->id . ')');
            if ($fromBackOfficeUserAction || isset($importData['cms'])) {
                $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_cms` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_cms` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_cms` WHERE `id_search` = ' . (int)$this->id . ')');
            }
            if ($fromBackOfficeUserAction || isset($importData['manufacturer'])) {
                $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_manufacturers` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_manufacturer` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_manufacturers` WHERE `id_search` = ' . (int)$this->id . ')');
            }
            if ($fromBackOfficeUserAction || isset($importData['supplier'])) {
                $ret &= SearchEngineDb::execute('INSERT INTO `' . _DB_PREFIX_ . 'pm_advancedsearch_suppliers` (SELECT "' . (int)$obj->id . '" AS `id_search`, `id_supplier` FROM `' . _DB_PREFIX_ . 'pm_advancedsearch_suppliers` WHERE `id_search` = ' . (int)$this->id . ')');
            }
            Db::getInstance()->delete(
                'pm_advancedsearch_shop',
                'id_shop != ' . (int)$idShop . ' AND id_search = ' . (int)$obj->id
            );
            if ($fromBackOfficeUserAction && Tools::getValue('submitSearchDuplicate') && $idShop != Context::getContext()->shop->id) {
                Db::getInstance()->insert(
                    'pm_advancedsearch_shop',
                    [
                        'id_shop' => (int)$idShop,
                        'id_search' => (int)$obj->id,
                    ]
                );
            }
            Db::getInstance()->update(
                'pm_advancedsearch_product_price_' . (int)$obj->id,
                [
                    'id_shop' => (int)$idShop,
                ]
            );
        }
        if ($ret) {
            $ret = $obj;
        }
        return $ret;
    }
    public function delete()
    {
        SearchEngineUtils::setLocalStorageCacheKey();
        $result = parent::delete();
        $this->cleanAllAssociations();
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_shop`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_lang`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_link`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_' . (int)$this->id . '_list`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$this->id . '`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_criterion_group_' . (int)$this->id . '_lang`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_' . (int)$this->id . '`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_cache_product_criterion_' . (int)$this->id . '`');
        SearchEngineDb::execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'pm_advancedsearch_product_price_' . (int)$this->id . '`');
        if (self::supportsSeoPages()) {
            Seo::deleteByIdSearch($this->id);
        }
        return $result;
    }
    public function addAssociations(array $associations, string $associationTable, string $associationIdentifier, bool $cleanBefore)
    {
        $result = true;
        if ($cleanBefore) {
            $result &= $this->cleanAssociation($associationTable);
        }
        $formattedAssociations = [];
        foreach ($associations as $value) {
            $value = trim((string)$value);
            if (!$value) {
                continue;
            }
            $formattedAssociations[] = [
                $this->identifier => (int)$this->id,
                $associationIdentifier => $value,
            ];
        }
        foreach (array_chunk($formattedAssociations, 5000) as $rows) {
            $result &= Db::getInstance()->insert($associationTable, $rows);
        }
        return (bool)$result;
    }
    public function cleanAssociation(string $associationTable)
    {
        return SearchEngineDb::execute('DELETE FROM `' . bqSQL(_DB_PREFIX_ . $associationTable) . '` WHERE `' . bqSQL($this->identifier) . '` = ' . (int)$this->id);
    }
    public function cleanAllAssociations()
    {
        $result = $this->cleanAssociation('pm_advancedsearch_category');
        $result &= $this->cleanAssociation('pm_advancedsearch_cms');
        $result &= $this->cleanAssociation('pm_advancedsearch_products');
        $result &= $this->cleanAssociation('pm_advancedsearch_products_cat');
        $result &= $this->cleanAssociation('pm_advancedsearch_manufacturers');
        $result &= $this->cleanAssociation('pm_advancedsearch_suppliers');
        $result &= $this->cleanAssociation('pm_advancedsearch_special_pages');
        return (bool)$result;
    }
}
