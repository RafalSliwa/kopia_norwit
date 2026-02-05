<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-2024 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class seoRedirectList extends ObjectModel
{
    public $id_seor;
    public $old;
    public $position;
    public $new;
    public $code;
    public $regexp;
    public $wildcard;
    public $active;
    public $redirect_type;
    public $date_add;
    public $date_update;
    public $id_shop;
    public static $definition = array(
        'table' => 'seor',
        'primary' => 'id_seor',
        'multilang' => false,
        'fields' => array(
            'id_seor' => array('type' => ObjectModel :: TYPE_INT),
            'position' => array('type' => ObjectModel :: TYPE_INT),
            'old' => array('type' => ObjectModel :: TYPE_STRING),
            'new' => array('type' => ObjectModel :: TYPE_STRING),
            'regexp' => array('type' => ObjectModel :: TYPE_STRING),
            'wildcard' => array('type' => ObjectModel :: TYPE_STRING),
            'redirect_type' => array('type' => ObjectModel :: TYPE_INT),
            'active' => array('type' => ObjectModel :: TYPE_INT),
            'date_add' => array('type' => ObjectModel :: TYPE_DATE),
            'date_update' => array('type' => ObjectModel :: TYPE_DATE),
            'id_shop' => array('type' => ObjectModel :: TYPE_INT),
        ),
    );

    public static function getOld($old)
    {
        $shop_base_url = Context::getContext()->shop->getBaseURL(false,false);
        $record = Db::getInstance(_PS_USE_SQL_SLAVE_)->executes('SELECT * FROM `' . _DB_PREFIX_ . 'seor` where old="' . $old . '" OR old="' . $shop_base_url.$old . '"  OR old="' . str_replace($shop_base_url, '', $old) . '"');
        return $record;
    }

    public function __construct($id_seor = null)
    {
        parent::__construct($id_seor);
    }
}