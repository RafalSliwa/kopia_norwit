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

class Ets_pmn_defines
{
    public static $instance;
    public $name = 'ets_productmanager';
    protected $context;
    protected $smarty;
    public function __construct()
    {
        $this->context = Context::getContext();
        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty;
        }
    }
    public function _installDb(){
        $res =  Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_view` ( 
        `id_ets_pmn_view` INT(11) NOT NULL AUTO_INCREMENT , 
        `name` VARCHAR(1000) NOT NULL , 
        `fields` TEXT NOT NULL , 
        PRIMARY KEY (`id_ets_pmn_view`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci') ;
        
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_view_employee` (
        `id_ets_pmn_view` INT(11) NOT NULL , 
        `id_employee` INT(11) NOT NULL , 
        PRIMARY KEY (`id_ets_pmn_view`, `id_employee`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_filter_employee` (
        `id_employee` INT(11) NOT NULL , 
        `filters` text , 
        PRIMARY KEY (`id_employee`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit` ( 
        `id_ets_pmn_massedit` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_shop` INT(11),
        `name` VARCHAR(1000) NOT NULL , 
        `excluded` Text NOT NULL , 
        `status_edit` TINYINT(1) NOT NULL , 
        `type_combine_condition` VARCHAR(10) NOT NULL, 
        `deleted` TINYINT(1) NOT NULL , 
        `date_add` DATETIME NOT NULL , 
        PRIMARY KEY (`id_ets_pmn_massedit`,`id_shop`), INDEX (`status_edit`),INDEX(`deleted`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_condition` ( 
        `id_ets_pmn_massedit_condition` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_pmn_massedit` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `filtered_field` TINYINT(2) NOT NULL , 
        `operator` VARCHAR(100) NOT NULL , 
        `compared_value` TEXT NOT NULL , 
        PRIMARY KEY (`id_ets_pmn_massedit_condition`, `id_ets_pmn_massedit`, `id_lang`),
        INDEX (`filtered_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_history` ( 
        `id_ets_pmn_massedit_history` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_pmn_massedit` INT(11) NOT NULL , 
        `id_shop` INT(11) NOT NULL , 
        `fields` text NOT NULL , 
        `edited_field` TINYINT(2) NOT NULL ,
        `date_add` DATETIME NOT NULL ,
        PRIMARY KEY (`id_ets_pmn_massedit_history`), 
        INDEX (`id_ets_pmn_massedit`),INDEX(id_shop)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_log` ( 
        `id_ets_pmn_massedit_log` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_pmn_massedit_history` INT(11) NOT NULL , 
        `editlog` TEXT NOT NULL , PRIMARY KEY (`id_ets_pmn_massedit_log`), INDEX (`id_ets_pmn_massedit_history`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_condition_action` ( 
        `id_ets_pmn_massedit_condition_action` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_pmn_massedit` INT(11), 
        `id_ets_pmn_massedit_history` INT(11),
        `condition` VARCHAR(22) NOT NULL , 
        `field` VARCHAR(40) NOT NULL , 
        `value` text NOT NULL , 
        PRIMARY KEY (`id_ets_pmn_massedit_condition_action`),INDEX (`id_ets_pmn_massedit`),INDEX(`id_ets_pmn_massedit_history`), INDEX (`condition`), INDEX (`field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_condition_action_lang` ( 
        `id_ets_pmn_massedit_condition_action` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL , 
        `value_lang` TEXT NOT NULL , PRIMARY KEY (`id_ets_pmn_massedit_condition_action`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_massedit_history_product` ( 
        `id_ets_pmn_massedit_history_product` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_pmn_massedit_history` INT(11),
        `id_product` INT(11) NOT NULL ,
        `field_name` VARCHAR(28), 
        `id_lang` INT(11),
        `old_value` text,
        `new_value` text,
        `date_add` DATETIME,
         PRIMARY KEY (`id_ets_pmn_massedit_history_product`), INDEX (`id_product`), INDEX (`id_lang`), INDEX (`field_name`), INDEX (`id_ets_pmn_massedit_history`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_pmn_product_note` (`id_product` INT(11) NOT NULL , `note` TEXT NOT NULL , PRIMARY KEY (`id_product`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $res &= Db::getInstance()->insert('ets_pmn_massedit',array(
            'id_ets_pmn_massedit' =>1,
            'name' => '',
            'id_shop'=> 0,
            'status_edit' => 0,
            'deleted' => 1,
        )
        );
        return $res;
    }
    public function _unInstallDb()
    {
        $tables = array(
            'ets_pmn_view',
            'ets_pmn_view_employee',
            'ets_pmn_filter_employee',
            'ets_pmn_massedit',
            'ets_pmn_massedit_condition',
            'ets_pmn_massedit_history',
            'ets_pmn_massedit_log',
            'ets_pmn_massedit_condition_action_lang',
            'ets_pmn_massedit_condition_action',
            'ets_pmn_massedit_history_product',
        );
        if($tables)
        {
            foreach($tables as $table)
               Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($table).'`');
        }
        return true;
    }
    public static function getColumns($table)
    {
        return Db::getInstance()->ExecuteS('DESCRIBE '._DB_PREFIX_.bqSQL($table));
    }
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_pmn_defines();
        }
        return self::$instance;
    }
    protected static $inputs;
    public function getConfigInputs()
    {
        if(!self::$inputs)
        {
            self::$inputs = array(
                array(
                    'type'=>'switch',
                    'label' => $this->l('Set fixed position for column titles on product listing page'),
                    'desc' => $this->l('Easier to view product information when scrolling'),
                    'name'=>'ETS_PMN_FIXED_HEADER_PRODUCT',
                    'validate' => 'isInt',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type'=>'switch',
                    'label' => $this->l('Enable instant search on product listing page'),
                    'desc' => $this->l('Display search result immediately as you typing in product filter'),
                    'name'=>'ETS_PMN_ENABLE_INSTANT_FILTER',
                    'validate' => 'isInt',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'default' => 1,
                ),
                array(
                    'type'=>'switch',
                    'label' => $this->l('Save Mass Edit log'),
                    'desc' => Ets_pmn_defines::displayText($this->l('View Mass Edit log here'),'a',array('href'=> $this->context->link->getAdminLink('AdminProductManagerMassiveEditLog'))),
                    'name'=>'ETS_PMN_SAVE_EDIT_LOG',
                    'validate' => 'isInt',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                    'default' => 0,
                ),
                array(
                    'type'=>'text',
                    'label'=> $this->l('Number of products edited per Ajax request'),
                    'desc'=> $this->l('Leave this field blank to edit all products'),
                    'validate'=>'isUnsignedInt',
                    'name'=>'ETS_PMN_NUMBER_PRODUCT_EDIT_EACH_AJAX',
                    'default'=>100,
                    'col'=>3,
                ),
            );
        }
        return self::$inputs;
    }
    public function getProductFields()
    {
        $title_fields = array(
            'id_product' => array(
                'title' => $this->l('ID'),
                'group' => $this->l('Basic settings'),
                'beggin' => true,
                'all' => true,
                'filter' =>true,
                'type'=>'int'
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'sort'=>false,
            ),
            'name' => array(
                'title' => $this->l('Product name'),
                'filter' => $this->l('Search name'),
                'input' => array(
                    'name' => 'name',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'description_short' => array(
                'title' => $this->l('Summary'),
                'filter' => $this->l('Search summary'),
                'lang' => true,
                'sort'=>false,
                'input' => array(
                    'name' => 'description_short_full',
                    'type' => 'textarea',
                    'lang'=>true,
                    'autoload_rte' => true,
                    'popup' => true,
                ),
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'filter' => $this->l('Search description'),
                'lang' => true,
                'sort'=>false,
                'input' => array(
                    'name' => 'description_full',
                    'type' => 'textarea',
                    'lang'=>true,
                    'autoload_rte' => true,
                    'popup' => true,
                ),
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'filter' => $this->l('Search ref.'),
                'input' => array(
                    'name' => 'reference',
                    'type' => 'text',
                ),
            ),
            'name_category' => array(
                'title' => $this->l('Main category'),
                'filter' => $this->l('Search category')
            ),
            'categories'=>array(
                'title' => $this->l('Categories'),
                'sort' => false,
            ),
            'features' => array(
                'title' => $this->l('Features'),
                'sort' => false,
            ),
            'manufacturers' => array(
                'title'=> $this->l('Brands'),
                'sort' => false,
                'filter' => $this->l('Search brands'),
                'input' => array(
                    'name' => 'id_manufacturer',
                    'type' => 'select',
                    'values' => array(
                        'query' => array_merge(array(array('id_manufacturer'=>'','name'=> '--')), $this->getManufactures()),
                        'id' => 'id_manufacturer',
                        'name' => 'name'
                    )
                ),
            ),
            'combinations' => array(
                'title' => $this->l('Combinations'),
                'sort' => false,
            ),
            'active' => array(
                'title' => $this->l('Status'),
            ),
            'private_note' => array(
                'title' => $this->l('Private note'),
                'sort' => false,
                'filter' => $this->l('Search private note'),
                'input' => array(
                    'name' =>'private_note',
                    'type'=>'textarea',
                )
            ),
            'related_product'=>array(
                'title' => $this->l('Related product'),
                'sort' => false,
                'end'=>true,
            ),
            'sav_quantity' => array(
                'title' => $this->l('Quantity'),
                'group' => $this->l('Quantity'),
                'beggin' => true,
                'all' => true,
                'input'=>array(
                    'name' => 'sav_quantity',
                    'type' => 'text'
                ),
            ),
            'minimal_quantity' => array(
                'title' => $this->l('Min QTY for sale'),
                'sort' => false,
                'filter' => true,
                'type'=>'int',
                'input' => array(
                    'type' => 'text',
                    'name' => 'minimal_quantity'
                ),
            ),
            'location' => array(
                'title' => $this->l('Stock location'),
                'sort' => false,
                'filter' => $this->l('Search stock location'),
                'input' => array(
                    'name' => 'location',
                    'type' => 'text',
                ),
            ),
            'low_stock_threshold' => array(
                'title' => $this->l('Low stock level'),
                'sort' => false,
                'filter' => true,
                'type'=>'int',
                'input' => array(
                    'name' => 'low_stock_threshold',
                    'type' => 'text'
                )
            ),
            'associated_file' => array(
                'title' => $this->l('Associated file'),
                'sort' => false,
            ),
            'available_now' => array(
                'title' => $this->l('Label when in-stock'),
                'sort'=>false,
                'filter' => $this->l('Search label when in-stock'),
                'input' => array(
                    'name' =>'available_now',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'available_later' => array(
                'title' => $this->l('Label when out of stock'),
                'sort' => false,
                'filter' =>false,
                'input' => array(
                    'name' => 'available_later',
                    'type' => 'text',
                    'lang' => true,
                ),
            ),
            'available_date' => array(
                'title' => $this->l('Availability date'),
                'end' => true,
                'sort' => false,
                'input' => array(
                    'name' => 'available_date',
                    'type' => 'date',
                ),
            ),
            'price' => array(
                'title' => $this->l('Price (tax excl.)'),
                'group' => $this->l('Pricing'),
                'beggin' => true,
                'all' => true,
                'filter' =>true,
                'type'=>'int',
                'edit_inline' => true,
                'input' => array(
                    'name' => 'price_float',
                    'type' => 'text',
                    'suffix' => $this->context->currency->sign
                ),
            ),
            'price_final' => array(
                'title' => $this->l('Price (tax incl.)'),
                'sort'=>false,
                'input' => array(
                    'name' => 'price_final_float',
                    'type' => 'text',
                    'suffix' => $this->context->currency->sign
                ),
            ),
            'unit_price' => array(
                'title' => $this->l('Price per unit (tax excl.)'),
                'sort' => false,
                'input' => array(
                    'name' => 'unit_price_float',
                    'type' => 'text',
                    'suffix' => $this->context->currency->sign
                ),
            ),
            'wholesale_price' => array(
                'title' => $this->l('Cost price (tax excl.)'),
                'sort' => false,
                'filter' =>true,
                'type'=>'int',
                'input' => array(
                    'name' => 'wholesale_price_float',
                    'type' => 'text',
                    'suffix' => $this->context->currency->sign
                ),
            ),
            'tax_name'=>array(
                'title' => $this->l('Tax rule'),
                'sort' => false,
                'filter' => $this->l('Search tax rule'),
                'input' => array(
                    'name'=> 'id_tax_rules_group',
                    'type' => 'select',
                    'values' => array(
                        'query' => $this->getTaxRulesGroupsForOptions(),
                        'id'=> 'id_tax_rules_group',
                        'name' => 'name',
                    ),
                ),
            ),
            'on_sale'=>array(
                'title' => $this->l('Display \'On sale!\' flag'),
                'sort' => false,
            ),
            'specific_prices' => array(
                'title' => $this->l('Specific prices'),
                'end' => true,
                'sort'=>false,
            ),
            'meta_title' => array(
                'title' => $this->l('Meta title'),
                'group' => $this->l('SEO'),
                'beggin' => true,
                'all' => true,
                'sort' => false,
                'filter' =>  $this->l('Search meta title'),
                'input' => array(
                    'type'=>'text',
                    'name' => 'meta_title',
                    'lang'=>true,
                    'popup' => true,
                )
            ),
            'meta_description' => array(
                'title' => $this->l('Meta description'),
                'sort' => false,
                'filter' => $this->l('Search meta description'),
                'input' => array(
                    'name' =>'meta_description',
                    'type'=>'textarea',
                    'lang'=>true,
                    'popup' => true,
                )
            ),
            'link_rewrite' => array(
                'title' => $this->l('Friendly URL'),
                'sort' => false,
                'filter' =>$this->l('Search friendly url'),
                'input' => array(
                    'name' =>'link_rewrite',
                    'type'=>'text',
                    'lang'=>true,
                )
            ),
            'redirect_type' => array(
                'title' => $this->l('Redirection when offline'),
                'sort'=>false,
                'end'=> Module::isEnabled('ets_seo') ? false :true,
                'filter' =>$this->l('Search redirection when offline'),
                'input' => array(
                    'name' => 'redirect_type',
                    'type'=>'select',
                    'values'=>array(
                        'query' => array(
                            'default' => array(
                                'id' => 'default',
                                'name' => $this->l('Default behavior from configuration')
                            ),
                            '200-displayed' => array(
                                'id' => '200-displayed',
                                'name' => $this->l('No redirection (200), display product')
                            ),
                            '404-displayed' => array(
                                'id' => '200-displayed',
                                'name' => $this->l('No redirection (404), display product')
                            ),
                            '410-displayed' => array(
                                'id' => '200-displayed',
                                'name' => $this->l('No redirection (410), display product')
                            ),
                            '301-category' => array(
                                'id' =>'301-category',
                                'name' => $this->l('Permanent redirection to a category (301)')
                            ),
                            '302-category' => array(
                                'id' =>'302-category',
                                'name' => $this->l('Temporary redirection to a category (302)')
                            ),
                            '301-product' =>  array(
                                'id' =>'301-product',
                                'name' => $this->l('Permanent redirection to a product (301)')
                            ),
                            '302-product' =>  array(
                                'id' =>'302-product',
                                'name' => $this->l('Temporary redirection to a product (302)')
                            ),
                            '404' => array(
                                'id' =>'404',
                                'name' => $this->l('No redirection (404)')
                            ),
                        ),
                        'id'=>'id',
                        'name' => 'name',
                    )
                ),
            ),
            'seo_score'=> array(
                'title' => $this->l('SEO score'),
                'sort' => false,
            ),
            'readability_score' => array(
                'title' => $this->l('Readability score'),
                'sort' => false,
            ),
            'focus_keyphrase' => array(
                'title' => $this->l('Focus key phrase (keyword)'),
                'sort' => false,
                'filter' =>$this->l('Search focus key phrase'),
                'input' => array(
                    'name' => 'focus_keyphrase',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'related_keyphrases' => array(
                'title' => $this->l('Related key phrases'),
                'end'=>true,
                'sort' => false,
                'filter' =>$this->l('Search related key phrases'),
                'input' => array(
                    'name' => 'related_keyphrases',
                    'type' => 'tags',
                    'lang' => true,
                ),
            ),
            'visibility' => array(
                'title' => $this->l('Where to appear?'),
                'group' => $this->l('Options'),
                'beggin' => true,
                'all' => true,
                'sort' => false,
                'input'=> array(
                    'name' =>'product_visibility',
                    'type'=>'select',
                    'values' => array(
                        'query' => array(
                            'both'=> array(
                                'id' => 'both',
                                'name' => $this->l('Everywhere'),
                            ),
                            'catalog' => array(
                                'id' => 'catalog',
                                'name' => $this->l('Catalog only'),
                            ),
                            'search' => array(
                                'id' => 'search',
                                'name' => $this->l('Search only'),
                            ),
                            'none' => array(
                                'id' => 'none',
                                'name' => $this->l('Nowhere'),
                            )
                        ),
                        'id'=>'id',
                        'name' => 'name'
                    ),
                ),
            ),
            'tags' => array(
                'title' => $this->l('Tags'),
                'sort' => false,
                'input' => array(
                    'name' => 'tags_no_html',
                    'type'=>'tags',
                    'lang'=>true,
                ),
            ),
            'condition' => array(
                'title' => $this->l('Condition'),
                'sort' => false,
                'filter' => $this->l('Search condition'),
                'input'=>array(
                    'name' => 'condition',
                    'type' =>'select',
                    'values'=>array(
                        'query' => array(
                            'new'=>array(
                                'id' =>'new',
                                'name' => $this->l('New'),
                            ),
                            'used'=>array(
                                'id' =>'used',
                                'name' => $this->l('Used'),
                            ),
                            'refurbished'=>array(
                                'id' =>'refurbished',
                                'name' => $this->l('Refurbished'),
                            )
                        ),
                        'id'=>'id',
                        'name' =>'name'
                    )
                )
            ),
            'isbn' => array(
                'title' => $this->l('ISBN'),
                'sort' => false,
                'filter' => $this->l('Search ISBN'),
                'input' => array(
                    'name' => 'isbn',
                    'type'=>'text',
                ),
            ),
            'ean13' => array(
                'title' => $this->l('EAN-13 or JAN barcode'),
                'sort' => false,
                'filter' => $this->l('Search EAN-13 or JAN barcode'),
                'input' => array(
                    'name' => 'ean13',
                    'type'=>'text',
                ),
            ),
            'upc' => array(
                'title' => $this->l('UPC barcode'),
                'sort' => false,
                'filter' => $this->l('Search UPC barcode'),
                'input' => array(
                    'name' => 'upc',
                    'type'=>'text',
                ),
            ),
            'customization' => array(
                'title' => $this->l('Customization'),
                'sort' => false,
            ),
            'attached_files' => array(
                'title' => $this->l('Attached files'),
                'sort' => false,
            ),
            'suppliers' => array(
                'title' => $this->l('Suppliers'),
                'filter' => $this->l('Search supplier name'),
                'end' => true,
                'sort' => false,
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'group' => $this->l('Shipping'),
                'beggin' => true,
                'filter' => $this->l('Search width'),
                'type' =>'int',
                'sort' => false,
                'all' => true,
                'input' => array(
                    'name' => 'width_float',
                    'type' => 'text',
                    'suffix' => $this->l('cm'),
                ),
            ),
            'height' => array(
                'title' => $this->l('Height'),
                'sort' => false,
                'filter' => $this->l('Search height'),
                'type' =>'int',
                'input' => array(
                    'name' => 'height_float',
                    'type' => 'text',
                    'suffix' => $this->l('cm'),
                ),
            ),
            'depth' => array(
                'title' => $this->l('Depth'),
                'sort' => false,
                'filter' => $this->l('Search depth'),
                'type' =>'int',
                'input' => array(
                    'name' => 'depth_float',
                    'type' => 'text',
                    'suffix' => $this->l('cm'),
                ),
            ),
            'weight' => array(
                'title' => $this->l('Weight'),
                'sort' => false,
                'filter' => $this->l('Search weight'),
                'type' =>'int',
                'input' => array(
                    'name' => 'weight_float',
                    'type' => 'text',
                    'suffix' => $this->l('kg'),
                ),
            ),
            'additional_delivery_times' => array(
                'title' => $this->l('Delivery time'),
                'sort' => false,  
            ),
            'delivery_in_stock' => array(
                'title' => $this->l('Delivery time of in-stock products'),
                'sort' => false,
                'filter' => $this->l('Search delivery time of in-stock'),
                'input' => array(
                    'name' => 'delivery_in_stock',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'delivery_out_stock' => array(
                'title' => $this->l('Delivery time of out-of-stock'),
                'sort' => false,
                'filter' => $this->l('Search delivery time of out-of-stock'),
                'input' => array(
                    'name' => 'delivery_out_stock',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'additional_shipping_cost' => array(
                'title' => $this->l('Shipping fees'),
                'filter' => $this->l('Search depth'),
                'sort' => false,
                'type' =>'int',
                'input' => array(
                    'name' => 'additional_shipping_cost_float',
                    'type' => 'text',
                    'suffix' => $this->context->currency->sign
                ),
            ),
            'selectedCarriers' => array(
                'title' => $this->l('Available carriers'),
                'end' => true,
                'sort' => false,
            ),
        );
        if(!Module::isEnabled('ets_seo'))
        {
            unset($title_fields['seo_score']);
            unset($title_fields['readability_score']);
            unset($title_fields['focus_keyphrase']);
            unset($title_fields['related_keyphrases']);
        }
        if(Module::isEnabled('ets_customfields'))
        {
            $custom_fields = $this->getCustomFields();
            $title_fields = array_merge($title_fields,$custom_fields);
        }
        if(Module::isEnabled('ets_extraproducttabs'))
        {
            $extratabs = $this->getExtraProductTabs();
            $title_fields = array_merge($title_fields,$extratabs);
        }
        if(Module::isEnabled('ph_sortbytrending'))
        {
            $title_fields['priority_product'] = array(
                'title' => $this->l('Priority'),
                'group' => $this->l('Sort by trending'),
                'beggin' => true,
                'all' => true,
                'sort' => false,
                'filter' =>$this->l('Search priority'),
                'type' =>'int',
                'end'=>true,
                'input' => array(
                    'name' => 'priority_product',
                    'type' => 'text',
                ),
            );
        }
        if(version_compare(_PS_VERSION_, '1.7.2.5', '<='))
        {
            unset($title_fields['low_stock_threshold']);
            unset($title_fields['low_stock_alert']);
            unset($title_fields['additional_delivery_times']);
            unset($title_fields['delivery_in_stock']);
            unset($title_fields['delivery_out_stock']);
        }
        if(version_compare(_PS_VERSION_, '8.0.0', '>='))
        {
            $title_fields['redirect_type']['input']['values']['query']['410'] = array(
                'id' => '410',
                'name' => $this->l('No redirection (410)')
            );
        }
        if(version_compare(_PS_VERSION_, '8.1.0', '<'))
        {
            unset($title_fields['redirect_type']['input']['values']['query']['default']);
            unset($title_fields['redirect_type']['input']['values']['query']['200-displayed']);
            unset($title_fields['redirect_type']['input']['values']['query']['404-displayed']);
            unset($title_fields['redirect_type']['input']['values']['query']['410-displayed']);
        }
        return $title_fields;
    }
    public function getCustomFields()
    {
        $custom_fields = array(
            'version' => array(
                'title' => $this->l('Version'),
                'group' => $this->l('Custom field'),
                'beggin' => true,
                'all' => true,
                'sort' => false,
                'input' => array(
                    'name' => 'version',
                    'type' => 'text',
                ),
            ),
            'compatibility' => array(
                'title' => $this->l('Compatible with'),
                'sort' => false,
                'filter' => $this->l('Search compatible with'),
                'input' => array(
                    'name' => 'compatibility',
                    'type' => 'text',
                ),
            ),
            'min_ps_version' => array(
                'title' => $this->l('Min PS version'),
                'sort' => false,
                'filter' => $this->l('Search min version'),
                'input' => array(
                    'name' => 'min_ps_version',
                    'type' => 'text',
                ),
            ),
            'max_ps_version' => array(
                'title' => $this->l('Max PS version'),
                'sort' => false,
                'filter' => $this->l('Search max version'),
                'input' => array(
                    'name' => 'max_ps_version',
                    'type' => 'text',
                ),
            ),
            'module_logo' => array(
                'title' => $this->l('Logo'),
                'sort'=>false,
            ),
            'module_name' => array(
                'title' => $this->l('Display name'),
                'sort' => false,
                'filter' => $this->l('Search display name'),
                'input' => array(
                    'name' => 'module_name',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'module_description'=> array(
                'title' => $this->l('Module description'),
                'sort' => false,
                'filter' => $this->l('Search module description'),
                'input' => array(
                    'name' => 'module_description',
                    'type' => 'textarea',
                    'lang'=>true,
                    'popup' => true,
                ),
            ),
            'fo_link' => array(
                'title' => $this->l('FO Demo'),
                'sort'=>false,
                'filter' =>$this->l('Search FO demo'),
                'input' => array(
                    'name' => 'link_fo',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'bo_link' => array(
                'title' => $this->l('BO Demo'),
                'sort'=>false,
                'filter' =>$this->l('Search BO demo'),
                'input' => array(
                    'name' => 'link_bo',
                    'type' => 'text',
                    'lang'=>true,
                ),
            ),
            'is_must_have' => array(
                'title' => $this->l('Must-have'),
                'sort' => false,
            ),
            'doc_name' => array(
                'title' => $this->l('Documentation'),
                'sort'=>false,
                'filter' => $this->l('Search documentation'),
                'end'=>true,
            ),
        );
        return $custom_fields;
    }
    public function getExtraProductTabs()
    {
        $sql = 'SELECT t.id_ets_ept_tab,tl.name FROM `'._DB_PREFIX_.'ets_ept_tab` t
        INNER JOIN `'._DB_PREFIX_.'ets_ept_tab_lang` tl ON (t.id_ets_ept_tab = tl.id_ets_ept_tab AND tl.id_lang="'.(int)$this->context->language->id.'")
        WHERE t.enable=1';
        $tabs = Db::getInstance()->executeS($sql);
        $extratabs = array();
        if($tabs)
        {
            foreach($tabs as $key=> $tab)
            {
                $extratabs['extra_product_tab_'.$tab['id_ets_ept_tab']] = array(
                    'title' => $tab['name'],
                    'group' => $key==0? $this->l('Extra tabs') :'',
                    'beggin' =>$key==0 ? true:false,
                    'all' => $key==0 ?true:false,
                    'sort' => false,
                    'end' => $key==count($tabs)-1 ? true :false,
                );
            }
        }
        return $extratabs;
    }
    public function getListCategories($id_product)
    {
        $sql ='SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (c.id_category = cp.id_category)
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.id_category = c.id_category AND cl.id_lang="'.(int)$this->context->language->id.'")
        WHERE cp.id_product="'.(int)$id_product.'" AND c.active=1 GROUP BY c.id_category';
        $categories = Db::getInstance()->executeS($sql);
        if($categories)
        {
            $content = '';
            foreach($categories as $category)
            {
                $content .=self::displayText($category['name'],'span').self::displayText('','br');
            }
            return $content;
        }
        return '';
    }
    public function getListFeatures($id_product)
    {
        $sql ='SELECT fl.name,fvl.value FROM `'._DB_PREFIX_.'feature_product` fp
        INNER JOIN `'._DB_PREFIX_.'feature` f ON (fp.id_feature = f.id_feature)
        INNER JOIN `'._DB_PREFIX_.'feature_value` fv ON (fv.id_feature_value = fp.id_feature_value)
        LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (fl.id_feature = f.id_feature AND fl.id_lang="'.(int)$this->context->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang="'.(int)$this->context->language->id.'")
        WHERE fp.id_product="'.(int)$id_product.'"';
        $features  = Db::getInstance()->executeS($sql);
        if($features)
        {
            $content = '';
            foreach($features as $feature)
            {
                $content .=self::displayText($feature['name'].': '.$feature['value'],'span').self::displayText('','br');
            }
            return $content;
        }
    }
    public function getListCombinations($id_product)
    {
        $product_attributes = Db::getInstance()->executeS('
        SELECT pa.id_product_attribute,sa.quantity,pa.price FROM `'._DB_PREFIX_.'product_attribute` pa
        INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute=pas.id_product_attribute)
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (pa.id_product_attribute = sa.id_product_attribute AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0) )
        WHERE pas.id_shop="'.(int)Context::getContext()->shop->id.'" AND pa.id_product="'.(int)$id_product.'"');
        if($product_attributes)
        {
            $content = '';
            foreach($product_attributes as &$product_attribute)
            {
                $name = $this->getProductAttributeName($product_attribute['id_product_attribute']);
                $price = Tools::displayPrice($product_attribute['price']);
                $text= sprintf($this->l('%s (Qty: %s, Impact: %s)'),$name,$product_attribute['quantity'],$price);
                $content .=self::displayText($text,'span').self::displayText('','br');
            }
            return $content;
        }
        return '';
    }
    public function getProductAttributeName($id_product_attribute,$small=false)
    {
        $sql = 'SELECT a.id_attribute,al.name,agl.name as group_name FROM `'._DB_PREFIX_.'attribute` a
            INNER JOIN `'._DB_PREFIX_.'attribute_shop` attribute_shop ON (a.id_attribute= attribute_shop.id_attribute AND attribute_shop.id_shop="'.(int)$this->context->shop->id.'")
            INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (a.id_attribute=pac.id_attribute)
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (a.id_attribute_group= agl.id_attribute_group AND agl.id_lang="'.(int)$this->context->language->id.'")
            WHERE pac.id_product_attribute ="'.(int)$id_product_attribute.'"
        ';
        $attributes = Db::getInstance()->executeS($sql);
        $name_attribute ='';
        if($attributes)
        {
            foreach($attributes as $attribute)
            {
                if($small)
                   $name_attribute .= $attribute['name'].' - '; 
                else
                    $name_attribute .= $attribute['group_name'].' - '.$attribute['name'].', ';
            }
        }
        return $small ? trim($name_attribute,' - '): trim($name_attribute,', ');
    }
    public function getListSpecificPrices($id_product)
    {
        $specific_prices = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE id_product="'.(int)$id_product.'"');
        if($specific_prices)
        {
            $content ='';
            foreach($specific_prices as &$specific_price)
            {
                if($specific_price['reduction'] >0)
                {
                    if($specific_price['reduction_type'] =='amount')
                        $specific_price['reduction'] = Tools::displayPrice($specific_price['reduction']);
                    else
                        $specific_price['reduction'] = Tools::ps_round($specific_price['reduction']*100,2).'%';
                }
                else
                    $specific_price['reduction']= 0;
                if($specific_price['price']!=-1)
                {
                    $text = sprintf($this->l('Fixed: %s'),Tools::displayPrice($specific_price['price']));
                }
                else
                    $text =sprintf($this->l('Impact: %s'),$specific_price['reduction']);
                
                $content .= self::displayText($text,'span').self::displayText('','br');
            }
            return $content;
        }
        return '--';
    }
    public function getListtags($id_product,$html = true,$id_lang = false)
    {

        $sql = 'SELECT t.name FROM `'._DB_PREFIX_.'product_tag` pt
        INNER JOIN `'._DB_PREFIX_.'tag` t ON (pt.id_tag = t.id_tag AND pt.id_lang= pt.id_lang)
        WHERE pt.id_product="'.(int)$id_product.'"'.($id_lang ? ' AND pt.id_lang="'.(int)$id_lang.'"':' AND pt.id_lang="'.(int)$this->context->language->id.'"');
        $tags = Db::getInstance()->executeS($sql);
        if($tags)
        {
            if($html)
            {
                $content = '';
                foreach($tags as $tag)
                {
                    $content .= self::displayText($tag['name'],'span').self::displayText('','br');
                }
                return $content;
            }
            else
            {
                $text = '';
                foreach($tags as $tag)
                    $text .= $tag['name'].',';
                return trim($text,',');
                
            }
        }
        else 
            return $html ? '--':'';
    }
    public function getListAttachments($id_product)
    {
        $sql = 'SELECT a.file_name FROM `'._DB_PREFIX_.'product_attachment` pa 
        INNER JOIN `'._DB_PREFIX_.'attachment` a ON (pa.id_attachment = a.id_attachment)
        WHERE pa.id_product="'.(int)$id_product.'"';
        $attachments = Db::getInstance()->executeS($sql);
        if($attachments)
        {
            $content='';
            foreach($attachments as $attachment)
            {
                $content .= self::displayText($attachment['file_name'],'span').self::displayText('','br');
            }
            return $content;
        }
        return '--';
    }
    public function getListSuppliers($id_product)
    {
        $sql = 'SELECT s.name FROM `'._DB_PREFIX_.'product_supplier` ps
        INNER JOIN `'._DB_PREFIX_.'supplier` s ON (ps.id_supplier=s.id_supplier)
        INNER JOIN `'._DB_PREFIX_.'supplier_shop` ss ON (ss.id_supplier=s.id_supplier)
        WHERE ps.id_product="'.(int)$id_product.'" AND ss.id_shop="'.(int)$this->context->shop->id.'" GROUP BY s.id_supplier';
        $suppliers = Db::getInstance()->executeS($sql);
        if($suppliers){
            $content='';
            foreach($suppliers as $supplier)
            {
                $content .= self::displayText($supplier['name'],'span').self::displayText('','br');
            }
            return $content;
        }
        return '--';
    }
    public function getListCustomizations($id_product)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'customization_field` cf
        INNER JOIN `'._DB_PREFIX_.'customization_field_lang` cfl ON (cf.id_customization_field=cfl.id_customization_field AND cfl.id_lang="'.(int)$this->context->language->id.'")
        WHERE cf.id_product="'.(int)$id_product.'" AND cf.is_deleted=0';
        $customizations = Db::getInstance()->executeS($sql);
        if($customizations)
        {
            $content='';
            foreach($customizations as $customization)
            {
                $content .= self::displayText($customization['name'],'span').self::displayText('','br');
            }
            return $content;
        }
        return '--';
    }
    public function getExtraProductTabValue($id_product,$id_tab,$id_lang=0)
    {
        if(Module::isEnabled('ets_extraproducttabs'))
        {
            return Module::getInstanceByName('ets_extraproducttabs')->getExtraProductTabValue($id_product,$id_tab,$id_lang);
        }
        return '';
    }
    protected static $list_fields;
    public function getFieldsByIdEmployee($id_employee=0)
    {
        if(!isset(self::$list_fields))
        {
            if(!$id_employee)
                $id_employee = $this->context->employee->id;
            if (Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST')) {
                $list_fields = explode(',', Configuration::get('ETS_PRODUCTMANAGE_ARRANGE_LIST'));
            } else
                $list_fields = Module::getInstanceByName('ets_productmanager')->_list_product_default;
            if(($id_view = Ets_pmn_view::getViewByIdEmployee($id_employee)) && ($viewOjb = new Ets_pmn_view($id_view)))
            {
                $list_fields = explode(',', $viewOjb->fields);
            }
            self::$list_fields = $list_fields;
        }
        return self::$list_fields;
    }
    protected static $product_fields;
    public function getProductListFields()
    {
        if(!isset(self::$product_fields))
        {
            $list_fields = $this->getFieldsByIdEmployee();
            $product_fields = array();
            if($list_fields)
            {
                $title_fields = $this->getProductFields();
                foreach($list_fields as $field)
                {
                    if(isset($title_fields[$field]))
                        $product_fields[$field] = $title_fields[$field];
                }
            }
            self::$product_fields = $product_fields;
        }
        return self::$product_fields;
    }
    public static function updateFocusKeyphrase($id_product,$id_lang,$focus_keyphrase)
    {
        if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_seo_product` WHERE id_product='.(int)$id_product.' AND id_lang='.(int)$id_lang.' AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_seo_product` SET key_phrase ="'.pSQL($focus_keyphrase).'" WHERE id_product='.(int)$id_product.' AND id_lang='.(int)$id_lang.' AND id_shop="'.(int)Context::getContext()->shop->id.'"');
        else
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_seo_product`(id_product,id_shop,id_lang,key_phrase) VALUES("'.(int)$id_product.'","'.(int)Context::getContext()->shop->id.'","'.(int)$id_lang.'","'.pSQL($focus_keyphrase).'")');
    }
    public static function updateMinorKeyPhrase($id_product,$id_lang,$minor_key_phrase)
    {
        if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_seo_product` WHERE id_product='.(int)$id_product.' AND id_lang='.(int)$id_lang.' AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_seo_product` SET minor_key_phrase ="'.pSQL($minor_key_phrase).'" WHERE id_product='.(int)$id_product.' AND id_lang='.(int)$id_lang.' AND id_shop='.(int)Context::getContext()->shop->id);
        else
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_seo_product`(id_product,id_shop,id_lang,minor_key_phrase) VALUES("'.(int)$id_product.'","'.(int)Context::getContext()->shop->id.'","'.(int)$id_lang.'","'.pSQL($minor_key_phrase).'")');
    }
    public static function updatePriorityProduct($id_product,$priority_product)
    {
        if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ph_sbt_product_position` WHERE id_product='.(int)$id_product))
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ph_sbt_product_position` SET priority ="'.(float)$priority_product.'" WHERE id_product='.(int)$id_product);
        else
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ph_sbt_product_position`(id_product,position,priority) VALUES("'.(int)$id_product.'","0","'.(float)$priority_product.'")');
    }
    public function getListCategoryProduct($id_product)
    {
        $sql = 'SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category_product` cp
        INNER JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category and cl.id_lang="'.(int)$this->context->language->id.'")
        WHERE cp.id_product='.(int)$id_product.' group by c.id_category';
        return Db::getInstance()->executeS($sql);
    }
    protected static $categoriesTree=array();
    public function getCategoriesTree($id_root=0)
    {
        if(!isset(self::$categoriesTree[$id_root]))
        {
            if(!$id_root)
            {
                $id_root = Db::getInstance()->getValue('SELECT c.id_category FROM `'._DB_PREFIX_.'category` c
                INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)$this->context->shop->id.'")
                WHERE c.active=1 AND is_root_category=1');
            }
            $sql ='SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category=cl.id_category AND cl.id_lang ="'.(int)$this->context->language->id.'" AND cl.id_shop="'.(int)$this->context->shop->id.'")
            WHERE c.id_category = "'.(int)$id_root.'" GROUP BY c.id_category';
            self::$categoriesTree[$id_root]=array();
            if($category = Db::getInstance()->getRow($sql))
            {
                $cat = array(
                    'name' => $category['name'],
                    'id_category' => $category['id_category']
                );
                $temp = array();
                $Childrens = $this->getChildrenCategories($category['id_category']);
                if($Childrens)
                {
                    foreach($Childrens as $children)
                    {
                        $arg = $this->getCategoriesTree($children['id_category']);
                        if($arg && isset($arg['0']))
                        {
                            $temp[] = $arg[0];
                        }
                    }
                }
                $cat['children'] = $temp;
                self::$categoriesTree[$id_root][] = $cat;
            }
        }
        return self::$categoriesTree[$id_root];
    }
    public function getChildrenCategories($id_parent)
    {
        $sql = 'SELECT c.id_category,cl.name FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop="'.(int)$this->context->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang="'.(int)$this->context->language->id.'" AND cl.id_shop="'.(int)$this->context->shop->id.'")
        WHERE c.id_parent="'.(int)$id_parent.'"';
        return Db::getInstance()->executeS($sql);
    }
    public function _saveCategoryProduct($id_product,$id_categories)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE id_product='.(int)$id_product);
        if($id_categories)
        {
            foreach($id_categories as $id_category)
            {
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES("'.(int)$id_product.'","'.(int)$id_category.'","1")');
            }
        }
    }
    public function _saveCarriersProduct($id_product,$selectedCarriers)
    {
        Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product='.(int)$id_product.' AND id_shop="'.(int)$this->context->shop->id.'"');
        if($selectedCarriers)
        {
            foreach($selectedCarriers as $id_carrier)
            {
                if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_carrier` WHERE id_product="'.(int)$id_product.'" AND id_carrier_reference="'.(int)$id_carrier.'" AND id_shop="'.(int)$this->context->shop->id.'"'))
                {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_carrier`(id_product,id_carrier_reference,id_shop) VALUES("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)$this->context->shop->id.'")');
                }
            }
        }
    }
    public function getListImages($id_product)
    {
        return  Db::getInstance()->executeS('SELECT image.* FROM `'._DB_PREFIX_.'image` image
        INNER JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.id_image = image.id_image ) 
        WHERE image_shop.id_product= '.(int)$id_product.' AND image_shop.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY image.position ASC');
    }
    public function getFeaturesProduct($id_product)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product;
        $product_features = Db::getInstance()->executeS($sql);
        if($product_features)
        {
            foreach($product_features as &$product_feature)
            {
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'feature_value` fv
                    LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang="'.(int)$this->context->language->id.'")
                WHERE fv.id_feature = "'.(int)$product_feature['id_feature'].'" AND fv.custom=0';
                $product_feature['feature_values'] = Db::getInstance()->executeS($sql);
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'feature_value` fv
                    LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang="'.(int)$this->context->language->id.'")
                WHERE fv.id_feature = "'.(int)$product_feature['id_feature'].'" AND fv.id_feature_value="'.(int)$product_feature['id_feature_value'].'"';
                $product_feature['feature_value'] = Db::getInstance()->getRow($sql);
            }
        }
        return $product_features;
    }
    public function getFeatures()
    {
        $sql ='SELECT f.*,fl.name
        FROM `'._DB_PREFIX_.'feature` f
        INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (f.id_feature = fs.id_feature)
        LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.id_feature = fl.id_feature AND fl.id_lang="'.(int)$this->context->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON (fv.id_feature = f.id_feature)
        WHERE fs.id_shop="'.(int)$this->context->shop->id.'"
        GROUP BY f.id_feature';
        return Db::getInstance()->executeS($sql);
    }
    public function getFeatureValues()
    {
        $sql ='SELECT fv.*,fvl.value
        FROM `'._DB_PREFIX_.'feature_value` fv 
        INNER JOIN `'._DB_PREFIX_.'feature` f ON (fv.id_feature = f.id_feature)
        INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (f.id_feature AND fs.id_shop)
        LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.id_feature_value= fvl.id_feature_value AND fvl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE fv.custom=0 AND fs.id_shop="'.(int)$this->context->shop->id.'"
        GROUP BY fv.id_feature_value';
        return Db::getInstance()->executeS($sql);
    }
    public function _saveFeatureProduct($id_product,$id_features,$id_feature_values,$feature_value_custom){
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE id_product='.(int)$id_product);
        if($id_features)
        {
            $languages = Language::getLanguages(false);
            foreach($id_features as $key=> $id_feature)
            {
                if($id_feature)
                {
                    if(isset($id_feature_values[$key]) && $id_feature_values[$key])
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'feature_product` WHERE id_product="'.(int)$id_product.'" AND id_feature = "'.(int)$id_feature.'" AND id_feature_value="'.(int)$id_feature_values[$key].'"'))
                        {
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$id_feature_values[$key].'")');
                        }
                    }
                    elseif(isset($feature_value_custom[$key]) && $feature_value_custom[$key])
                    {
                        $feature_value = new FeatureValue();
                        $feature_value->id_feature = $id_feature;
                        $feature_value->custom=1;
                        foreach($languages as $language)
                            $feature_value->value[$language['id_lang']] = $feature_value_custom[$key];
                        if($feature_value->add())
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'feature_product`(id_product,id_feature,id_feature_value) VALUES("'.(int)$id_product.'","'.(int)$id_feature.'","'.(int)$feature_value->id.'")');
                    }
                }
            }
        }
    }
    public function getManufactures()
    {
        $sql = 'SELECT m.id_manufacturer,m.name FROM `'._DB_PREFIX_.'manufacturer` m 
        INNER JOIN `'._DB_PREFIX_.'manufacturer_shop` ms ON (m.id_manufacturer = ms.id_manufacturer AND ms.id_shop="'.(int)$this->context->shop->id.'")';
        return Db::getInstance()->executeS($sql);
    }
    public function displayListCombinations($id_product)
    {
        /** @var Ets_productmanager $module */
        $module = Module::getInstanceByName('ets_productmanager');
        $product = new Product($id_product);
        $productAttributes = Db::getInstance()->executeS('SELECT pa.*,sa.quantity FROM `'._DB_PREFIX_.'product_attribute` pa
        INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute=pa.id_product_attribute)
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (pa.id_product_attribute=sa.id_product_attribute AND (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
        WHERE pas.id_shop="'.(int)Context::getContext()->shop->id.'" AND pa.id_product='.(int)$id_product.' ORDER BY pa.id_product_attribute ASC');
        if($productAttributes)
        {
            foreach($productAttributes as &$productattribute)
            {

                $productattribute['name_attribute'] = $this->getProductAttributeName($productattribute['id_product_attribute']);
                $attribute_images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$productattribute['id_product_attribute']);
                $productattribute['images'] = array();
                if($attribute_images)
                {
                    foreach($attribute_images as $attribute_image)
                        $productattribute['images'][] = $attribute_image['id_image'];
                }
                if($product->id_tax_rules_group)
                {
                    $tax = Module::getInstanceByName('ets_productmanager')->getTaxValue($product->id_tax_rules_group);
                    $productattribute['price_tax_incl'] = Tools::ps_round($productattribute['price'] + ($productattribute['price']*$tax),6);
                }
                else
                    $productattribute['price_tax_incl']= $productattribute['price'];
            }
        }
        $product_images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$id_product);
        if($product_images)
        {
            $type_image= Ets_productmanager::getFormatedName('small');
            foreach($product_images as &$image)
            {
                $image['link'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id],$image['id_image'],$type_image);
            }
        }
        $this->context->smarty->assign(
            array(
                'product_images' => $product_images,
                'default_currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'product_class' => $product,
                'productAttributes' => $productAttributes,
            )
        );
        return $module->display($module->getLocalPath(),'product/list_combinations.tpl');
    }
    public static function getAttributeGroups()
    {
        $sql = 'SELECT ag.*,agl.name FROM `'._DB_PREFIX_.'attribute_group` ag
        INNER JOIN `'._DB_PREFIX_.'attribute_group_shop` ags ON (ag.id_attribute_group = ags.id_attribute_group AND ags.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.id_attribute_group = agl.id_attribute_group AND agl.id_lang="'.(int)Context::getContext()->language->id.'")';
        $attributeGroups = Db::getInstance()->executeS($sql);
        if($attributeGroups)
        {
            foreach($attributeGroups as &$attributeGroup)
            {
                $attributeGroup['attributes'] = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'attribute` a
                INNER JOIN `'._DB_PREFIX_.'attribute_shop` ash ON (a.id_attribute = ash.id_attribute AND ash.id_shop="'.(int)Context::getContext()->shop->id.'")
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang="'.(int)Context::getContext()->language->id.'")
                WHERE a.id_attribute_group = "'.(int)$attributeGroup['id_attribute_group'].'"
                GROUP BY a.id_attribute');
                if($attributeGroup['is_color_group'] && $attributeGroup['attributes'])
                {
                    foreach($attributeGroup['attributes'] as &$attribute)
                    {
                        if(file_exists(_PS_COL_IMG_DIR_.$attribute['id_attribute'].'.jpg'))
                            $attribute['image'] = Module::getInstanceByName('ets_productmanager')->getBaseLink().'/img/co/'.$attribute['id_attribute'].'.jpg';
                    }
                }
            }
        }
        return $attributeGroups;
    }
    public function submitDeletecombinations($attributes,$id_product)
    {
        if($attributes)
        {
            foreach($attributes as $id_attribute)
            {
                $productAttribute = new Combination($id_attribute);
                $productAttribute->delete();
            }
        }
        Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
        die(
            json_encode(
                array(
                    'success' => $this->l('Deleted successfully'),
                    'list_combinations' => $this->displayListCombinations($id_product),
                    'combinations' => $this->getListCombinations($id_product),
                    'sav_quantity' =>$this->getQuantityProduct($id_product),
                )
            )
        );
    }
    public static function updateQuantityProduct($id_product)
    {
        $quantity = Db::getInstance()->getValue('SELECT SUM(quantity) FROM `'._DB_PREFIX_.'stock_available` WHERE id_product='.(int)$id_product.' AND id_product_attribute!=0 AND id_shop='.(int)Context::getContext()->shop->id);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'stock_available` SET quantity ='.(int)$quantity.' WHERE id_product='.(int)$id_product.' AND id_product_attribute=0 AND id_shop='.(int)Context::getContext()->shop->id);
    }
    public static function getLocation($id_product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product = (int) $id_product;

        if (null === $id_product_attribute) {
            $id_product_attribute = 0;
        } else {
            $id_product_attribute = (int) $id_product_attribute;
        }

        $query = new DbQuery();
        $query->select('location');
        $query->from('stock_available');
        $query->where('id_product = ' . (int)$id_product);
        $query->where('id_product_attribute = ' . (int)$id_product_attribute);

        $query = StockAvailable::addSqlShopRestriction($query, $id_shop);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    public static function setLocation($id_product, $location, $id_shop = null, $id_product_attribute = 0)
    {
        if (
            false === Validate::isUnsignedId($id_product)
            || (((false === Validate::isUnsignedId($id_shop)) && (null !== $id_shop)))
            || (false === Validate::isUnsignedId($id_product_attribute))
            || (false === Validate::isString($location))
        ) {
            $serializedInputData = [
                'id_product' => $id_product,
                'id_shop' => $id_shop,
                'id_product_attribute' => $id_product_attribute,
                'location' => $location,
            ];

            throw new \InvalidArgumentException(sprintf(
                'Could not update location as input data is not valid: %s',
                json_encode($serializedInputData)
            ));
        }

        $existing_id = StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop);

        if ($existing_id > 0) {
            Db::getInstance()->update(
                'stock_available',
                array('location' => pSQL($location)),
                'id_product = ' . (int)$id_product .
                (($id_product_attribute) ? ' AND id_product_attribute = ' . (int)$id_product_attribute : '') .
                StockAvailable::addSqlShopRestriction(null, $id_shop)
            );
        } else {
            $params = array(
                'location' => pSQL($location),
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute,
            );

            StockAvailable::addSqlShopParams($params, $id_shop);
            Db::getInstance()->insert('stock_available', $params, false, true, Db::ON_DUPLICATE_KEY);
        }
    }
    public function getQuantityProduct($id_product)
    {
        $product = new Product($id_product);
        $has_attribute = $product->hasCombinations();
        $this->smarty->assign(
            array(
                'quantity' => Db::getInstance()->getValue('SELECT quantity FROM `'._DB_PREFIX_.'stock_available` WHERE id_product="'.(int)$id_product.'" AND id_product_attribute=0 AND id_shop='.(int)$this->context->shop->id),
                'has_attribute' => $has_attribute,
                'link_product' => $this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$id_product)),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/row_quantity.tpl');
    }
    public function getStockAvailables($id_product)
    {
        $sql = 'SELECT sa.quantity,pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
        INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute = pas.id_product_attribute)
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product_attribute = pa.id_product_attribute and (sa.id_shop="'.(int)Context::getContext()->shop->id.'" OR sa.id_shop=0))
        WHERE pa.id_product='.(int)$id_product.' AND pas.id_shop='.(int)$this->context->shop->id;
        $stock_availables = Db::getInstance()->executeS($sql);
        if($stock_availables)
        {
            foreach($stock_availables as &$stock_available)
            {
                $stock_available['name'] = $this->getProductAttributeName($stock_available['id_product_attribute']);
            }
        }
        return $stock_availables;
    }
    public function getCustomizationFields($id_product)
    {
        $sql = 'SELECT id_customization_field FROM `'._DB_PREFIX_.'customization_field` WHERE id_product='.(int)$id_product.' AND is_deleted=0';
        $customizationFields = Db::getInstance()->executeS($sql);
        $objects = array();
        if($customizationFields)
        {
            foreach($customizationFields as $customizationField)
            {
                $objects[] = new CustomizationField($customizationField['id_customization_field']);
            }
        }
        return $objects;
    }
    public function submitCustomizationProduct($id_product,$custom_fields)
    {
        $errors = array();
        $languages = Language::getLanguages(false);
        $id_lang_default  = (int)Configuration::get('PS_LANG_DEFAULT');
        if($custom_fields)
        {
            foreach($custom_fields as $custom_field)
            {
                if(!$custom_field['label'][$id_lang_default])
                {
                    $errors[] = $this->l('Customization label is required');
                    break;
                }
                if(!Validate::isUnsignedInt($custom_field['type']))
                {
                    $errors[] = $this->l('Customization type is not valid');
                    break;
                }
                foreach($languages as $language)
                {
                    if($custom_field['label'][$language['id_lang']] && !Validate::isCleanHtml($custom_field['label'][$language['id_lang']]))
                    {
                        $errors[] = $this->l('Customization label is not valid');
                        break;
                    }
                }
                if($id_customization_field = (int)$custom_field['id_customization_field'])
                {
                    $customizationField = new CustomizationField($id_customization_field);
                    if(!Validate::isLoadedObject($customizationField) || $customizationField->id_product != $id_product)
                    {
                        $errors[] = $this->l('Customization field is not valid');
                    }
                }
            }
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'errors' => Module::getInstanceByName('ets_productmanager')->displayError($errors),
                    )
                )
            );
        }
        else
        {
            if($custom_fields)
            {
                Configuration::updateValue('PS_CUSTOMIZATION_FEATURE_ACTIVE',1);
                $id_customization_fields = array();
                foreach($custom_fields as $custom_field)
                {
                    if($id_customization_field = $custom_field['id_customization_field'])
                        $customizationField = new CustomizationField($id_customization_field);
                    else
                    {
                        $customizationField = new CustomizationField();
                        $customizationField->id_product=  $id_product;
                    }
                    foreach($languages as $language)
                    {
                        $customizationField->name[$language['id_lang']] = $custom_field['label'][$language['id_lang']] ? : $custom_field['label'][$id_lang_default];
                    }
                    $customizationField->type = (int)$custom_field['type'];
                    if(isset($custom_field['required']))
                        $customizationField->required = $custom_field['required'];
                    else    
                        $customizationField->required = 0;
                    if($customizationField->id)
                    {
                        $id_customization_fields[] = $customizationField->id;
                        $customizationField->update();
                    }
                    elseif($customizationField->add())
                        $id_customization_fields[] = $customizationField->id;
                }
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product="'.(int)$id_product.'"'.($id_customization_fields ? ' AND id_customization_field NOT IN ('.implode(',',array_map('intval',$id_customization_fields)).')':''));
            }
            else
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET is_deleted=1 WHERE id_product="'.(int)$id_product.'"');
            Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
            die(
                json_encode(
                    array(
                        'row_name' => 'customization',
                        'row_value' =>$this->getListCustomizations($id_product),
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
            
        }
    }
    public function getSuppliers($id_product)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'supplier` s
        INNER JOIN `'._DB_PREFIX_.'supplier_shop` ss ON (s.id_supplier= ss.id_supplier and ss.id_shop="'.(int)$this->context->shop->id.'")
        WHERE s.active=1';
        $suppliers = Db::getInstance()->executeS($sql);
        if($suppliers)
        {
            foreach($suppliers as &$supplier)
            {
                $supplier['checked'] = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'" AND id_supplier="'.(int)$supplier['id_supplier'].'"') ? true:false;
                if($supplier['checked'])
                {
                    
                    $supplier['product_suppliers'] = $this->refreshProductSupplierCombinationForm($supplier['id_supplier'],$id_product);
                }
                else
                    $supplier['product_suppliers'] = '';
            }
        }
        return $suppliers;
    }
    public function refreshProductSupplierCombinationForm($id_supplier,$id_product)
    {
        $has_attribute = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute` WHERE id_product="'.(int)$id_product.'"');
        $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $product_suppliers = Db::getInstance()->executeS('SELECT p.id_product,'.($has_attribute ? ' pa.id_product_attribute':'0').' as id_product_attribute,pl.name as product_name,ps.product_supplier_reference,ps.product_supplier_price_te,IF(ps.id_currency,ps.id_currency,"'.(int)$currency_default->id.'") as id_currency,IF('.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'cl.symbol':'cl.sign').','.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'cl.symbol':'cl.sign').',"'.pSQL($currency_default->sign).'") as symbol FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = "'.(int)$this->context->language->id.'" AND pl.id_shop="'.(int)Context::getContext()->shop->id.'")
            '.($has_attribute ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON(p.id_product=pa.id_product)':'').'
            LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.id_product = p.id_product'.($has_attribute ? ' AND pa.id_product_attribute=ps.id_product_attribute':'').' AND ps.id_supplier="'.(int)$id_supplier.'")
            LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'currency_lang':'currency').' cl ON (cl.id_currency=ps.id_currency'.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ?' AND cl.id_lang="'.(int)$this->context->language->id.'"':'').')
            WHERE p.id_product="'.(int)$id_product.'"
            GROUP BY p.id_product'.($has_attribute ? ',pa.id_product_attribute':'').'
        ');
        if($product_suppliers)
        {
            foreach($product_suppliers as &$product_supplier)
            {
                if($product_supplier['id_product_attribute'])
                    $product_supplier['product_name'] = $this->getProductAttributeName($product_supplier['id_product_attribute']);
            }
        }
        $this->smarty->assign(
            array(
                'product_suppliers' => $product_suppliers,
                'supplier_class' => new Supplier($id_supplier),
                'currencies' => $this->getCurrencies(),
                'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))
                
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/product/product_supplier_combination_form.tpl');
    }
    public function getCurrencies()
    {
        return Db::getInstance()->executeS('SELECT c.*,cl.name,'.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'cl.symbol' :'cl.sign').' as symbol FROM `'._DB_PREFIX_.'currency` c
        INNER JOIN `'._DB_PREFIX_.'currency_shop` cs ON (c.id_currency=cs.id_currency AND cs.id_shop="'.(int)$this->context->shop->id.'")
        LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? 'currency_lang':'currency').' cl ON (cl.id_currency=c.id_currency'.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? ' AND cl.id_lang="'.(int)$this->context->language->id.'"':'').')
        ');
    }
    public static function updateSuppliersProduct($id_product,$id_suppliers,$product_supplier_reference,$product_supplier_price,$product_supplier_price_currency)
    {
        if($id_suppliers && Ets_productmanager::validateArray($id_suppliers,'isInt'))
        {
            foreach($id_suppliers as $id_supplier)
            {
                $references = isset($product_supplier_reference[$id_supplier]) ? $product_supplier_reference[$id_supplier] : array();
                $supplier_prices = isset($product_supplier_price[$id_supplier]) ? $product_supplier_price[$id_supplier] :array() ;
                $currencies = isset($product_supplier_price_currency[$id_supplier]) ? $product_supplier_price_currency[$id_supplier] : array();
                if($currencies)
                {
                    foreach($currencies as $id_product_attribute=> $id_currency)
                    {
                        if(isset($references[$id_product_attribute]))
                            $reference = $references[$id_product_attribute];
                        else
                            $reference ='';
                        if(isset($supplier_prices[$id_product_attribute]))
                            $supplier_price = (float)$supplier_prices[$id_product_attribute];
                        else
                            $supplier_price =0;
                        if($id_product_supplier = Db::getInstance()->getValue('SELECT id_product_supplier FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product_attribute="'.(int)$id_product_attribute.'" AND id_product="'.(int)$id_product.'" AND id_supplier="'.(int)$id_supplier.'"'))
                            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_supplier` SET product_supplier_reference="'.pSQL($reference).'",product_supplier_price_te="'.(float)$supplier_price.'",id_currency ="'.(int)$id_currency.'" WHERE id_product_supplier="'.(int)$id_product_supplier.'"');
                        else
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_supplier`(id_product,id_product_attribute,id_supplier,product_supplier_reference,product_supplier_price_te,id_currency) VALUES("'.(int)$id_product.'","'.(int)$id_product_attribute.'","'.(int)$id_supplier.'","'.pSQL($reference).'","'.(float)$supplier_price.'","'.(int)$id_currency.'")');
                    }
                }
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'" AND id_supplier NOT IN ('.implode(',',array_map('intval',$id_suppliers)).')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE id_product="'.(int)$id_product.'"');
        Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));

    }
    public function getListAttachmentFields($id_product)
    {
        $sql = 'SELECT a.file_name,a.file_size,a.mime,al.name,pa.id_product,a.id_attachment FROM  `'._DB_PREFIX_.'attachment` a 
        LEFT JOIN  `'._DB_PREFIX_.'product_attachment` pa  ON (pa.id_attachment = a.id_attachment AND pa.id_product="'.(int)$id_product.'")
        LEFT JOIN `'._DB_PREFIX_.'attachment_lang` al ON(a.id_attachment = al.id_attachment AND al.id_lang="'.(int)$this->context->language->id.'")';
        return Db::getInstance()->executeS($sql);
    }
    public function existsSpecificPrice($specific_price)
    {
        return Db::getInstance()->getRow('
            SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE 
            id_product="'.(int)$specific_price->id_product.'" 
            AND id_product_attribute="'.(int)$specific_price->id_product_attribute.'" 
            AND id_customer ="'.(int)$specific_price->id_customer.'" 
            AND `id_cart`="'.(int)$specific_price->id_cart.'" 
            AND `from`="'.pSQL($specific_price->from).'" 
            AND `to` ="'.pSQL($specific_price->to).'" 
            AND id_shop="'.(int)$specific_price->id_shop.'" 
            AND id_shop_group="'.(int)$specific_price->id_shop_group.'" 
            AND id_currency="'.(int)$specific_price->id_currency.'" 
            AND id_country="'.(int)$specific_price->id_country.'" 
            AND id_group = "'.(int)$specific_price->id_group.'" 
            AND from_quantity="'.(int)$specific_price->from_quantity.'" 
            AND id_specific_price_rule="'.(int)$specific_price->id_specific_price_rule.'"'.
            ($specific_price->id ?' AND id_specific_price !="'.(int)$specific_price->id.'"':'')
        );
    }
    public function getListProductSpecificPrices($id_product)
    {
        $specific_prices = Db::getInstance()->executeS('
            SELECT sp.*,cul.name as currency_name, col.name as country_name, gl.name as group_name,CONCAT(c.firstname," ",c.lastname) as customer_name FROM `'._DB_PREFIX_.'specific_price` sp
            LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cul ON (cul.id_currency= sp.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=') ? ' AND cul.id_lang ="'.(int)$this->context->language->id.'"':'').')
            LEFT JOIN `'._DB_PREFIX_.'country_lang` col ON (col.id_country= sp.id_country AND col.id_lang="'.(int)$this->context->language->id.'") 
            LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (gl.id_group=sp.id_group AND gl.id_lang="'.(int)$this->context->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=sp.id_customer)
            WHERE sp.id_product='.(int)$id_product.' ORDER BY sp.id_specific_price asc');
        if($specific_prices)
        {
            foreach($specific_prices as &$specific_price)
            {
                if($specific_price['id_product_attribute'])
                {
                    $specific_price['attribute_name'] = $this->getProductAttributeName($specific_price['id_product_attribute']);
                    
                }
                if($specific_price['price']>=0)
                {
                    $specific_price['price_text'] = Tools::displayPrice($specific_price['price'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT')));
                }
                else
                    $specific_price['price_text'] ='--';
                if($specific_price['reduction_type']=='amount')
                {
                    $specific_price['reduction'] = Tools::displayPrice($specific_price['reduction'],new Currency(Configuration::get('PS_CURRENCY_DEFAULT'))).($specific_price['reduction_tax'] ? ' ('.$this->l('Tax incl.').')':' ('.$this->l('Tax excl.').')');
                }
                else
                    $specific_price['reduction'] = Tools::ps_round($specific_price['reduction']*100,2).'%';
            }
        }
        return $specific_prices;
    }
    public function getProductDownload($id_product)
    {
        $productDownload = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_download` WHERE id_product='.(int)$id_product);
        if($productDownload)
        {
            $productDownload['date_expiration'] = Tools::substr($productDownload['date_expiration'],0,10);
        }
        return $productDownload;
    }
    public function getSpecificDetail($specific_price)
    {
        return Db::getInstance()->getRow('
        SELECT sp.*,cul.name as currency_name, col.name as country_name, gl.name as group_name,CONCAT(c.firstname," ",c.lastname) as customer_name FROM `'._DB_PREFIX_.'specific_price` sp
        LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cul ON (cul.id_currency= sp.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'AND cul.id_lang="'.(int)$this->context->language->id.'"':'').')
        LEFT JOIN `'._DB_PREFIX_.'country_lang` col ON (col.id_country= sp.id_country AND col.id_lang="'.(int)$this->context->language->id.'") 
        LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (gl.id_group=sp.id_group AND gl.id_lang="'.(int)$this->context->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer=sp.id_customer)
        WHERE sp.id_product='.(int)$specific_price->id_product.' AND sp.id_specific_price = "'.(int)$specific_price->id.'"');
    }
    public function renderSpecificPrice($id_product,$id_specific_price=0)
    {
        $currencies = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'currency` c
            INNER JOIN `'._DB_PREFIX_.'currency_shop` cs ON (c.id_currency = cs.id_currency AND cs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cl ON (c.id_currency = cl.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'AND cl.id_lang="'.(int)$this->context->language->id.'"':'').')
            WHERE c.active=1');
        $countries = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'country` c
            INNER JOIN `'._DB_PREFIX_.'country_shop` cs ON (c.id_country = cs.id_country AND cs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.id_country= cl.id_country AND cl.id_lang ="'.(int)$this->context->language->id.'")
            WHERE c.active=1    
        ');
        $groups = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'group` g
            INNER JOIN `'._DB_PREFIX_.'group_shop` gs ON (g.id_group = gs.id_group AND gs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group=gl.id_group AND gl.id_lang="'.(int)$this->context->language->id.'")
        ');
        $productAttributes = Db::getInstance()->executeS('SELECT pa.*,sa.quantity FROM `'._DB_PREFIX_.'product_attribute` pa
        LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (pa.id_product_attribute=sa.id_product_attribute)
        WHERE pa.id_product='.(int)$id_product.' ORDER BY pa.id_product_attribute ASC');
        if($productAttributes)
        {
            foreach($productAttributes as &$productattribute)
            {
                
                $productattribute['name_attribute'] = self::getInstance()->getProductAttributeName($productattribute['id_product_attribute']);
                $attribute_images = Db::getInstance()->executeS('SELECT id_image FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$productattribute['id_product_attribute']);
                $productattribute['images'] = array();
                if($attribute_images)
                {
                    foreach($attribute_images as $attribute_image)
                        $productattribute['images'][] = $attribute_image['id_image'];
                }
            }
        }
        $specific_price= new SpecificPrice($id_specific_price);
        $this->context->smarty->assign(
            array(
                'currencies' => $currencies,
                'countries' => $countries,
                'groups' => $groups,
                'productAttributes' => $productAttributes,
                'default_currency' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'specific_price' => $specific_price,
                'id_product' => $id_product,
                'specific_price_customer' => new Customer($specific_price->id_customer),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_productmanager/views/templates/hook/product/specific_price.tpl');
    }
    public function getRelatedProducts($id_product)
    {
        $products = $this->getListProductsRelated($id_product);
        if($products)
        {
            $content = '';
            foreach($products as &$product)
            {
                $product['link'] = $this->context->link->getAdminLink('AdminProducts',true,array('id_product'=>$product['id_product']));
                if($product['img'])
                {
                    $content .= self::displayText(self::displayText('','img',array('src' => $product['img'])),'a',array('href' => $product['link'])).self::displayText('','br');
                }
                else
                {
                    $content .= self::displayText($product['name'],'a',array('href' => $product['link'])).self::displayText('','br');
                }

            }
            return $content;
        }
        return '--';
    }
    public function getListProductsRelated($id_product)
    {
        $sql ='SELECT p.*,pl.name,pl.link_rewrite,image_shop.`id_image` id_image, il.`legend` FROM `'._DB_PREFIX_.'product` p
        INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product=ps.id_product AND ps.id_shop="'.(int)$this->context->shop->id.'")
        INNER JOIN `'._DB_PREFIX_.'accessory` a ON (a.id_product_2 = p.id_product AND a.id_product_1="'.(int)$id_product.'")
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang="'.(int)$this->context->language->id.'" AND pl.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $this->context->language->id . ')
        GROUP BY p.id_product';
        $related_products = Db::getInstance()->executeS($sql);
        if($related_products)
        {
            $type_image= Ets_productmanager::getFormatedName('small');
            foreach($related_products as &$related_product)
            {
                if(!$related_product['id_image'])
                    $related_product['id_image'] = self::getIdImageByIdProduct($related_product['id_product']);
                if($related_product['id_image'])
                    $related_product['img'] = $this->context->link->getImageLink($related_product['link_rewrite'], $related_product['id_image'], $type_image);
            }
        }
        return $related_products;
    }
    public static function getIdImageByIdProduct($id_product)
    {
        return Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$id_product);
    }
    public function displayAjaxProductsList($query,$excludeIds,$active)
    {
        $type_image= Ets_productmanager::getFormatedName('home');
        if ($pos = Tools::strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }
        $disableCombination = false;
        $excludeVirtuals = false;
        $exclude_packs = false;
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
                WHERE (p.id_product="'.(int)$query.'" OR pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
                (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
                ($active ? ' AND p.active=1':'').
                ($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                ($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                ' GROUP BY p.id_product';
        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination || $excludeIds)) {
            $results = [];
            foreach ($items as $item) {
                if(!$item['id_image'])
                    $item['id_image'] = Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$item['id_product']);
                echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.($item['id_image'] ? str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)):'')."\n";
            }
        }
        elseif ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination
                if(!$item['id_image'])
                    $item['id_image'] = Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE id_product='.(int)$item['id_product']);
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, pai.`id_image`, al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $combination) {
                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                            $results[$combination['id_product_attribute']]['name'] = $item['name'].' '.$this->getProductAttributeName($combination['id_product_attribute']);
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                if(!$combination['id_image'])
                                    $combination['id_image'] = $item['id_image'];
                                $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $combination['id_image'], $type_image));
                            }
                            echo $item['id_product'].'|'.(int)$combination['id_product_attribute'].'|'.trim(str_replace('|','',$results[$combination['id_product_attribute']]['name'])).'|'.$results[$combination['id_product_attribute']]['ref'].'|'.$results[$combination['id_product_attribute']]['image']."\n";
                        }
                    } else {
                        echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image))."\n";
                    }
                } else {
                    echo $item['id_product'].'|0|'.trim(str_replace('|','',$item['name'])).'|'.$item['reference'].'|'.str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image))."\n";
                }
            }
        }
        die();
    }
    public function _submitRelatedProduct($id_product,$related_products)
    {
        if($related_products)
        {
            foreach($related_products as $related_product)
            {
                if($related_product!=$id_product && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2="'.(int)$related_product.'"'))
                {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'accessory`(id_product_1,id_product_2) VALUES("'.(int)$id_product.'","'.(int)$related_product.'")');
                }    
            }
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'" AND id_product_2 NOT IN ('.implode(',',array_map('intval',$related_products)).')');
        }
        else
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'accessory` WHERE id_product_1="'.(int)$id_product.'"');
        Hook::exec('actionProductUpdate',array('product' => new Product($id_product),'id_product'=>$id_product));
        die(
            json_encode(
                array(
                    'success' => $this->l('Updated successfully'),
                    'row_name' => 'related_product',
                    'row_value' => $this->getRelatedProducts($id_product),
                )
            )
        );
    }
    public function displayAjaxCustomersList($query)
    {
        $sql  ='SELECT * FROM `'._DB_PREFIX_.'customer` WHERE id_shop="'.(int)$this->context->shop->id.'" AND (id_customer="'.(int)$query.'" OR email LIKE "%'.pSQL($query).'%" OR CONCAT(firstname," ",lastname) LIKE "%'.pSQL($query).'%")';
        $customers = Db::getInstance()->executeS($sql);
        if($customers)
        {
            foreach($customers as $customer)
            {
                echo $customer['id_customer'].'|'.$customer['firstname'].' '.$customer['lastname'].'|'.$customer['email']."\n";
            }
        }
        die();
    }
    public function getListCurrencies()
    {
       return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'currency` c
            INNER JOIN `'._DB_PREFIX_.'currency_shop` cs ON (c.id_currency = cs.id_currency AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN '._DB_PREFIX_.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'currency_lang':'currency').' cl ON (c.id_currency = cl.id_currency '.(version_compare(_PS_VERSION_, '1.7.6.0', '>=')? 'AND cl.id_lang="'.(int)Context::getContext()->language->id.'"':'').')
            WHERE c.active=1');
    }
    public function getListCountries()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'country` c
            INNER JOIN `'._DB_PREFIX_.'country_shop` cs ON (c.id_country = cs.id_country AND cs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.id_country= cl.id_country AND cl.id_lang ="'.(int)$this->context->language->id.'")
            WHERE c.active=1    
        ');
    }
    public function getListGroups()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'group` g
            INNER JOIN `'._DB_PREFIX_.'group_shop` gs ON (g.id_group = gs.id_group AND gs.id_shop="'.(int)$this->context->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group=gl.id_group AND gl.id_lang="'.(int)$this->context->language->id.'")
        ');
    }
    public function deleteProducttag($id_product,$id_lang)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE id_product="'.(int)$id_product.'" AND id_lang='.(int)$id_lang);
    }
    public function getSeoKey($key,$id_product,$id_lang)
    {
        return Db::getInstance()->getValue('SELECT '.($key=='focus_keyphrase' ? 'key_phrase':'minor_key_phrase').' FROM `'._DB_PREFIX_.'ets_seo_product` WHERE id_product="'.(int)$id_product.'" AND id_lang='.(int)$id_lang.' AND id_shop='.(int)Context::getContext()->shop->id);
    }
    public function getFileName($id_product)
    {
        return Db::getInstance()->getValue('SELECT display_filename FROM `'._DB_PREFIX_.'product_download` WHERE id_product='.(int)$id_product);
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_productmanager', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function getBaseLink()
    {
        $url =(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($url,'/');
    }
    public static function getProductCarriers($id_product)
    {
        $sql = 'SELECT c.name FROM  `'._DB_PREFIX_.'product_carrier` pc
        INNER JOIN  `'._DB_PREFIX_.'carrier` c ON (pc.id_carrier_reference =c.id_reference)
        WHERE c.deleted=0 AND pc.id_product='.(int)$id_product.' AND pc.id_shop='.(int)Context::getContext()->shop->id;
        return Db::getInstance()->executeS($sql);
    }
    public static function getIdImageLang($id_image,$id_lang)
    {
        return Db::getInstance()->getValue('SELECT id_image_lang FROM `'._DB_PREFIX_.'ets_image_lang` WHERE id_image="'.(int)$id_image.'" AND id_lang="'.(int)$id_lang.'"');
    }
    public static function addAttachmentProduct($id_product,$id_attachment)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'product_attachment` WHERE id_product="'.(int)$id_product.'" AND id_attachment="'.(int)$id_attachment.'"'))
            Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'product_attachment` (id_product,id_attachment) VALUES("'.(int)$id_product.'","'.(int)$id_attachment.'")');
    }
    public static function deleteAttachmentProduct($id_product,$id_attachment)
    {
        return Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'product_attachment` WHERE id_product="'.(int)$id_product.'" AND id_attachment="'.(int)$id_attachment.'"');
    }
    public static function getCarriers($id_product)
    {
        $sql = 'SELECT c.name,c.id_reference,pc.id_carrier_reference FROM  `'._DB_PREFIX_.'carrier` c
        INNER JOIN  `'._DB_PREFIX_.'carrier_shop` cs ON (cs.id_carrier = c.id_carrier)
        LEFT JOIN  `'._DB_PREFIX_.'product_carrier` pc ON (pc.id_carrier_reference = c.id_reference AND pc.id_product="'.(int)$id_product.'" AND pc.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE c.deleted=0 AND cs.id_shop='.(int)Context::getContext()->shop->id;
        return Db::getInstance()->executeS($sql);
    }
    public static function resetAttributeDefault($id_product,$id_product_attribute)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_attribute` SET default_on=0 WHERE default_on=1 AND id_product="'.(int)$id_product.'" AND id_product_attribute!="'.(int)$id_product_attribute.'"');
    }
    public static function deleteProductAttributeImage($id_product_attribute)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute='.(int)$id_product_attribute);
    }
    public static function addProductAttributeImage($id_product_attribute,$id_image)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute_image` WHERE id_product_attribute="'.(int)$id_product_attribute.'" AND id_image="'.(int)$id_image.'"'))
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'product_attribute_image`(id_product_attribute,id_image) VALUES("'.(int)$id_product_attribute.'","'.(int)$id_image.'")');
    }
    public static function getProductNote($id_product)
    {
        return Db::getInstance()->getValue('SELECT note FROM `'._DB_PREFIX_.'ets_pmn_product_note` WHERE id_product='.(int)$id_product);
    }
    public static function updateProductNote($id_product,$privateNote)
    {

        if(Db::getInstance()->getRow('SELECT id_product FROM `'._DB_PREFIX_.'ets_pmn_product_note` WHERE id_product ='.(int)$id_product))
        {
             $sql ='UPDATE `'._DB_PREFIX_.'ets_pmn_product_note` set note ="'.pSQL($privateNote).'" WHERE id_product='.(int)$id_product;
        }else
            $sql ='INSERT INTO `'._DB_PREFIX_.'ets_pmn_product_note`(id_product,note) VALUES("'.(int)$id_product.'","'.pSQL($privateNote).'")';
        return Db::getInstance()->execute($sql);
    }
    public static function displayText($content,$tag,$attr_datas= array())
    {
        $text = '<' . $tag . ' ';
        if ($attr_datas) {
            foreach ($attr_datas as $key => $value)
                $text .= $key . '="' . $value . '" ';
        }
        if ($tag == 'img' || $tag == 'br' || $tag == 'path' || $tag == 'input')
            $text .= ' /'.'>';
        else
            $text .= '>';
        if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
            $text .= $content;
        if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
            $text .= '<'.'/' . $tag . '>';
        return $text;
    }
    public static function updateOldFeatureFlag()
    {
        if($state = Db::getInstance()->getValue('SELECT state FROM `'._DB_PREFIX_.'feature_flag` WHERE name="product_page_v2"'))
        {
            Configuration::updateGlobalValue('ETS_PMN_PRODUCT_PAGE_V2',$state);
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'feature_flag` SET state=0 WHERE name ="product_page_v2"');
        }
    }
    public static function updateNewFeatureFlag()
    {
        if($sate = Configuration::getGlobalValue('ETS_PMN_PRODUCT_PAGE_V2'))
        {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'feature_flag` SET state=1 WHERE name ="product_page_v2"');
            Configuration::updateGlobalValue('ETS_PMN_PRODUCT_PAGE_V2',0);
        }
    }
    public static function getTaxRulesGroupDefaultStateId( $taxRulesGroupId,  $countryId)
    {
        $sql ='SELECT tr.id_state FROM `'._DB_PREFIX_.'tax_rules_group` trg
        INNER JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
        INNER JOIN `'._DB_PREFIX_.'tax` t ON t.id_tax = tr.id_tax
        WHERE trg.deleted =0 AND trg.id_tax_rules_group = "'.(int)$taxRulesGroupId.'" AND tr.id_country = "'.(int)$countryId.'"
        ';
        $rawData = Db::getInstance()->executeS($sql);
        if (empty($rawData)) {
            return 0;
        }
        $firstRow = reset($rawData);

        return (int) $firstRow['id_state'];
    }
    public function getTaxRulesGroupsForOptions()
    {
        $tax_rules[] = [
            'id_tax_rules_group' => 0,
            'name' => $this->l('No tax'),
        ];
        return array_merge($tax_rules, TaxRulesGroup::getTaxRulesGroups(true));
    }
    public static function updateNewProduct($id_product)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET state=1 WHERE id_product='.(int)$id_product);
    }
}