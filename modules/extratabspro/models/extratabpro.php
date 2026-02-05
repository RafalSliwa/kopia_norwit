<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */
class Extratabpro extends ObjectModel
{
    public $id_tab;
    public $id_shop;
    public $id_product;
    public $active;
    public $position;
    public $body;
    public $name;
    public $internal_name;
    public $block_type;
    public $categories;
    public $block_type2;
    public $manufacturers;
    public $block_type3;
    public $products;
    public $block_type4;
    public $suppliers;
    public $cms;
    public $cms_body;
    public $geoip;
    public $selected_geoip;
    public $everywhere;
    public $feature;
    public $feature_v;
    public $for_groups;
    public $groups;
    public $df;
    public $date_from;
    public $tf;
    public $time_from;
    public $dt;
    public $date_to;
    public $tt;
    public $time_to;
    public $allConditions;
    public $allshops;
    public $stock;

    public static $definition = array(
        'table' => 'extratabspro',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'id_tab' => array('type' => ObjectModel :: TYPE_INT),
            'id_shop' => array('type' => ObjectModel :: TYPE_INT),
            'id_product' => array(
                'type' => ObjectModel :: TYPE_INT,
                'required' => true
            ),
            'active' => array('type' => ObjectModel :: TYPE_BOOL),
            'position' => array('type' => ObjectModel :: TYPE_INT),
            'body' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'lang' => true,
                'size' => 3999999999999
            ),
            'name' => array(
                'type' => ObjectModel :: TYPE_STRING,
                'lang' => true,
                'size' => 254
            ),
            'internal_name' => array(
                'type' => ObjectModel :: TYPE_STRING,
                'lang' => true,
                'size' => 254
            ),
            'block_type' => array('type' => ObjectModel :: TYPE_INT),
            'block_type2' => array('type' => ObjectModel :: TYPE_INT),
            'block_type3' => array('type' => ObjectModel :: TYPE_INT),
            'block_type4' => array('type' => ObjectModel :: TYPE_INT),
            'categories' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'manufacturers' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'products' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'suppliers' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'cms' => array(
                'type' => ObjectModel :: TYPE_INT,
                'validate' => 'isAnything'
            ),
            'cms_body' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'geoip' => array(
                'type' => ObjectModel :: TYPE_INT,
                'validate' => 'isInt'
            ),
            'selected_geoip' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'everywhere' => array(
                'type' => ObjectModel :: TYPE_INT,
                'validate' => 'isInt'
            ),
            'feature' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'feature_v' => array(
                'type' => ObjectModel::TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'for_groups' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'groups' => array(
                'type' => ObjectModel::TYPE_HTML,
                'validate' => 'isAnything'
            ),
            'df' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'dt' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'tf' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'tt' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'date_from' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isAnything'
            ),
            'date_to' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isAnything'
            ),
            'time_from' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isAnything'
            ),
            'time_to' => array(
                'type' => ObjectModel::TYPE_STRING,
                'validate' => 'isAnything'
            ),
            'allConditions' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'allshops' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
            'stock' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isAnything'
            ),
        ),
    );

    public static function getAllTabs()
    {
        $query = "SELECT * FROM `" . _DB_PREFIX_ . "extratabspro` AS a LEFT JOIN `" . _DB_PREFIX_ . "extratabspro_lang` AS b ON a.id_tab = b.id_tab WHERE a.id_shop IN (0,".Context::getContext()->shop->id.") AND b.id_lang=" . Context::getContext()->language->id;
        return Db::getInstance()->ExecuteS($query);
    }

    public static function loadByIdProduct($id_product)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'extratabspro` AS tab WHERE tab.id_shop IN (0,'.Context::getContext()->shop->id.') AND (tab.`id_product` = ' . (int)$id_product . ' OR tab.`block_type`=2 OR tab.`block_type3`=1 OR tab.`block_type2`=1 OR tab.`block_type4`=1 OR tab.`feature`=1 OR tab.`everywhere`=1) ORDER BY tab.position');
        global $array;
        foreach ($result as $k => $v)
        {
            $array[] = new Extratabpro($v['id_tab']);
        }
        return $array;
    }

    public static function loadByIdProductActive($id_product)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'extratabspro` AS tab WHERE tab.id_shop IN (0,'.Context::getContext()->shop->id.') AND (tab.`id_product` = ' . (int)$id_product . ' OR tab.`block_type`=2 OR tab.`block_type2`=1 OR tab.`block_type3`=1 OR tab.`block_type2`=1 OR tab.`feature`=1 OR tab.`everywhere`=1 OR tab.`block_type4`=1) AND tab.active="1" ORDER BY tab.position');
        global $array;
        foreach ($result as $k => $v)
        {
            $array[] = new Extratabpro($v['id_tab']);
        }
        return $array;
    }

    public static function lastObject($id_product)
    {
        $result = Db::getInstance()->ExecuteS('SELECT tab.id_tab FROM `' . _DB_PREFIX_ . 'extratabspro` AS tab WHERE tab.id_shop IN (0,'.Context::getContext()->shop->id.') AND (tab.`id_product` = ' . (int)$id_product . ') ORDER BY id_tab DESC LIMIT 1');
        return new Extratabpro ($result[0]['id_tab']);
    }
}