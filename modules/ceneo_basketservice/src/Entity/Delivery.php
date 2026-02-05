<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2024, Ceneo
 * @license   LICENSE.txt
 */
namespace CeneoBs\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Delivery extends \ObjectModel
{
    public $id;

    public $name;

    public $carrier_id;

    public $countries;

    public $ceneo_carrier_id;

    public static $definition = [
        'table' => 'ceneo_bs_delivery',
        'primary' => 'id',
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'default' => null,
            ],
            'carrier_id' => [
                'type' => self::TYPE_INT,
                'required' => true,
                'validate' => 'isNullOrUnsignedId',
                'default' => null,
            ],
            'countries' => [
                'type' => self::TYPE_STRING,
                'default' => null,
            ],
            'ceneo_carrier_id' => [
                'type' => self::TYPE_STRING,
                'default' => null,
            ],
        ],
    ];
}
