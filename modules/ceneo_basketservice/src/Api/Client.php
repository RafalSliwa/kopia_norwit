<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Ceneo
 * @copyright 2024, Ceneo
 * @license   LICENSE.txt
 */
namespace CeneoBs\Api;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration as Cfg;

class Client
{
    /**
     * Get token to api
     *
     * @return string|null
     */
    public function getToken()
    {
        $token = Cfg::get('CENEO_BS_TOKEN');
        $expires = Cfg::get('CENEO_BS_TOKEN_EXPIRES');
        $now = time();

        if ($now > $expires) {
            $api = new \RestClient([
                'base_url' => 'https://developers.ceneo.pl/',
                'headers' => [
                    'Authorization' => 'Basic ' . Cfg::get('CENEO_BS_KEY'),
                ],
            ]);

            $result = $api->get('AuthorizationService.svc/GetToken?grantType=client_credentials', []);

            if ($result->info->http_code == 204) {
                $token = $result->headers->access_token;
                $expires = strtotime('+14 minutes');
                Cfg::updateValue('CENEO_BS_TOKEN', $token);
                Cfg::updateValue('CENEO_BS_TOKEN_EXPIRES', $expires);
            }
        }

        return $token;
    }

    public function getShippingData($order)
    {
        $url = str_replace('https://developers.ceneo.pl/', '', $order['ShippingData']['__deferred']['uri'])
            . '?$format=json';
        return $this->executeApiCall($url);
    }

    public function getInvoiceData($order)
    {
        $url = str_replace('https://developers.ceneo.pl/', '', $order['InvoiceData']['__deferred']['uri'])
            . '?$format=json';
        return $this->executeApiCall($url);
    }

    public function getPaymentData($order)
    {
        $url = str_replace('https://developers.ceneo.pl/', '', $order['PaymentTypes']['__deferred']['uri'])
            . '?$format=json';
        return $this->executeApiCall($url);
    }

    public function getOrderDetails($order)
    {
        $url = str_replace('https://developers.ceneo.pl/', '', $order['OrderItems']['__deferred']['uri'])
            . '?$format=json';
        return $this->executeApiCall($url);
    }

    public function setOrders($params)
    {
        return $this->executeApiCall('BasketService.svc/SetOrders?orders=' . $params);
    }

    public function getAllOrders()
    {
        return $this->executeApiCall('BasketService.svc/Orders?$filter=OrderStateId%20eq%2030&$orderby=CreatedDate%20desc&$format=json');
    }

    public function getParcelCarriers()
    {
        return $this->executeApiCall('BasketService.svc/ParcelCarriers?$format=json');
    }

    public function getParcelCarrierById(string $id)
    {
        $url = 'BasketService.svc/ParcelCarriers(' . (int) $id . ')?$format=json';

        return $this->executeApiCall($url);
    }

    public function getOrderAdditionalServices($order)
    {
        $url = str_replace('https://developers.ceneo.pl/', '', $order['OrderAdditionalServices']['__deferred']['uri'])
            . '?$format=json';
        return $this->executeApiCall($url);
    }

    public function setOrderShipment(string $orderId, string $trackingNumber, string $carrierId)
    {
        $url = 'BasketService.svc/SetOrderShipment?orderId=' . $orderId . '&trackingNumber=' .
            $trackingNumber . '&carrierId=' . $carrierId;
        return $this->executeApiCall($url);
    }

    public function removeOrderShipment(string $orderId, string $trackingNumber)
    {
        $url = 'BasketService.svc/RemoveOrderShipment?orderId=' . $orderId . '&trackingNumber=' . $trackingNumber;
        return $this->executeApiCall($url);
    }

    public function executeApiCall(string $url)
    {
        $client = $this->createAuthenticatedClient();
        $response = $client->get($url);

        return json_decode($response->response, true);
    }

    private function createAuthenticatedClient(): \RestClient
    {
        return new \RestClient([
            'base_url' => 'https://developers.ceneo.pl/',
            'headers' => ['Authorization' => 'Bearer ' . $this->getToken()],
        ]);
    }

    /**
     * Actions api
     *
     * @param string $ceneo_order_id
     * @param string $action
     *
     * @return void
     */
    public function performApiAction(string $ceneo_order_id, string $action)
    {
        $endpointMap = [
            'cancel' => 'BasketService.svc/CancelOrder?id=' . $ceneo_order_id . '&reason={\'Id\':1}',
            'confirm' => 'BasketService.svc/ConfirmOrder?id=' . $ceneo_order_id,
            'send' => 'BasketService.svc/SendOrder?id=' . $ceneo_order_id,
        ];

        if (!array_key_exists($action, $endpointMap)) {
            throw new \InvalidArgumentException('Invalid action specified.');
        }

        $client = $this->createAuthenticatedClient();
        $client->get($endpointMap[$action]);
    }
}
