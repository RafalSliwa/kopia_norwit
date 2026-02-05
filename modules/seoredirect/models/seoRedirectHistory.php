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
class seoRedirectHistory extends ObjectModel
{
    public $id_seor_history;
    public $url;
    public $new;
    public $date_add;
    public $id_shop;
    public static $definition = array(
        'table' => 'seor_history',
        'primary' => 'id_seor_history',
        'multilang' => false,
        'fields' => array(
            'id_seor_history' => array('type' => ObjectModel :: TYPE_INT),
            'url' => array('type' => ObjectModel :: TYPE_STRING),
            'new' => array('type' => ObjectModel :: TYPE_STRING),
            'date_add' => array('type' => ObjectModel :: TYPE_DATE),
            'id_shop' => array('type' => ObjectModel :: TYPE_INT),
        ),
    );
}