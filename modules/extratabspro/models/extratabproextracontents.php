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
class Extratabproextracontents extends ObjectModel
{

    public $id_extracontents;
    public $id_tab;
    public $id_product;
    public $body;

    public static $definition = array(
        'table' => 'extratabsproextracontents',
        'primary' => 'id_extracontents',
        'multilang' => true,
        'fields' => array(
            'id_extracontents' => array('type' => ObjectModel :: TYPE_INT),
            'id_tab' => array(
                'type' => ObjectModel :: TYPE_INT,
                'required' => true
            ),
            'id_product' => array(
                'type' => ObjectModel :: TYPE_INT,
                'required' => true
            ),
            'body' => array(
                'type' => ObjectModel :: TYPE_HTML,
                'lang' => true,
                'size' => 3999999999999
            )
        ),
    );

    public static function getByProductAndTabId($id_product, $id_tab)
    {
        $result = Db::getInstance()->ExecuteS('SELECT id_extracontents FROM `' . _DB_PREFIX_ . 'extratabsproextracontents` AS tab WHERE (tab.`id_product` = ' . (int)$id_product . ' AND tab.`id_tab` = ' . (int)$id_tab . ')');
        return $result;
    }

    public static function getBoolByProductAndTabId($id_product, $id_tab)
    {
        $result = Db::getInstance()->ExecuteS('SELECT id_extracontents FROM `' . _DB_PREFIX_ . 'extratabsproextracontents` AS tab WHERE (tab.`id_product` = ' . (int)$id_product . ' AND tab.`id_tab` = ' . (int)$id_tab . ')');
        if (isset($result[0]['id_extracontents'])) {
            return true;
        } else {
            false;
        }
    }

    public function __construct($id_extracontents = null)
    {
        parent::__construct($id_extracontents);
    }

}