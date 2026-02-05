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
namespace CeneoBs\Handler;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoBs\Api\Client;
use CeneoBs\Repository\RepositoryBasketservice;
use Configuration as Cfg;

class OrderHandler
{
    private $repository;
    private $api;

    public function __construct(
        RepositoryBasketservice $repository,
        Client $api
    ) {
        $this->api = $api;
        $this->repository = $repository;
    }

    public function orderChangeState($newOrderStatusId, $id_order)
    {
        if ($newOrderStatusId == (int) Cfg::get('PS_OS_CANCELED')) {
            $this->handleCeneoOrder($id_order, 'cancel');
        } elseif ($newOrderStatusId == (int) Cfg::get('PS_OS_PREPARATION')) {
            $this->handleCeneoOrder($id_order, 'confirm');
        } elseif ($newOrderStatusId == (int) Cfg::get('PS_OS_SHIPPING')) {
            $this->handleCeneoOrder($id_order, 'send');
        }
    }

    /**
     * Order handler
     *
     * @param $id_order
     * @param $action
     */
    private function handleCeneoOrder($id_order, $action): void
    {
        $ceneo_order_id = $this->repository->getCeneoOrderId($id_order);
        if ($ceneo_order_id) {
            $this->api->performApiAction($ceneo_order_id, $action);
        }
    }
}
