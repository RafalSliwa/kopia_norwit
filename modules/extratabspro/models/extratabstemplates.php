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
class extratabstemplates extends ObjectModel
{

    public $id_tab_template;
    public $body;
    public $name;

    public static $definition = array(
        'table' => 'extratabspro_temp',
        'primary' => 'id_tab_template',
        'multilang' => true,
        'fields' => array(
            'id_tab_template' => array('type' => ObjectModel :: TYPE_INT),
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
        ),
    );
}