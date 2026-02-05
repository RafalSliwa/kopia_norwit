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
declare(strict_types=1);

namespace CeneoBs\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RepositoryBasketservice
{
    /**
     * Get ceneo order id
     *
     * @param $id_order
     *
     * @return mixed
     */
    public function getCeneoOrderId($id_order)
    {
        return \Db::getInstance()->getValue('SELECT ceneo_order_id FROM ' . _DB_PREFIX_ .
            'ceneo_bs WHERE shop_order_id = ' . $id_order);
    }

    public function getAllCeneoOrderId($id_order)
    {
        return \Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ .
            'ceneo_bs WHERE shop_order_id = ' . $id_order);
    }

    public function getAllCarriers()
    {
        return \Db::getInstance()->executeS('
            SELECT `c`.*
            FROM `' . _DB_PREFIX_ . 'carrier` `c`
    
            WHERE `c`.`deleted` = 0
            AND `c`.`active` = 1
        ');
    }

    public function getCeneoCarrierById($idCarrier)
    {
        return \Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ .
            'ceneo_bs_delivery WHERE carrier_id = ' . $idCarrier);
    }

    public function getAllCeneoCarriers()
    {
        return \Db::getInstance()->executeS('
            SELECT * FROM ' . _DB_PREFIX_ . 'ceneo_bs_delivery cbs
        ');
    }
}
