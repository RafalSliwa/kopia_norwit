<?php
/**
 * OrderHelper
 *
 * @category  OrderHelper
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * PostCodeNLAPIHelper
 *
 * @category  Class
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

namespace VidaXLAPI\Orders;

use Roja45APIHelper\APIHelper;

class OrderHelper extends APIHelper
{
    const SUCCESS = 200;

    protected $endpoint = 'api_customer/orders';

    /**
     * OrderHelper constructor.
     *
     * @param string $api_key
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $api_url,
        $api_username,
        $api_password
    ) {
        return parent::__construct(
            $api_url,
            $api_username,
            $api_password,
            self::LIVE_MODE
        );
    }

    /**
     * Request constructor.
     *
     * @param GetOrdersRequest $request
     *
     * @return CreateOrderResponse
     *
     * @throws InvalidArgumentException
     */
    public function postCreateOrder($request)
    {
        $request
            ->setUri($this->api_url . $this->endpoint)
            ->setHeaders(
                array(
                    'username'       => $this->getApiUsername(),
                    'password'       => $this->getApiPassword(),
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                )
            )
            ->setUsername($this->getApiUsername())
            ->setPassword($this->getApiPassword());

        if ($response = self::validateResponse($this->getHttpClient()->doRequest($request))) {
            return new CreateOrderResponse(
                $response['status'],
                $response['body'],
                $response['msg']
            );
        } else {
            return false;
        }
    }

    /**
     * Request constructor.
     *
     * @param GetOrdersRequest $request
     *
     * @return GetOrdersResponse
     *
     * @throws InvalidArgumentException
     */
    public function getGetOrder($request)
    {
        $request
            ->setUri($this->api_url . $this->endpoint)
            ->setHeaders(
                array(
                    'username'       => $this->getApiUsername(),
                    'password'       => $this->getApiPassword(),
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                )
            )
            ->setMethod('GET')
            ->setUsername($this->getApiUsername())
            ->setPassword($this->getApiPassword());

        if ($response = self::validateResponse($this->getHttpClient()->doRequest($request))) {
            return new GetOrdersResponse(
                $response['status'],
                $response['body'],
                $response['msg']
            );
        } else {
            return false;
        }
    }

    public static function validateResponse($json_response)
    {
        if ($json = json_decode($json_response)) {
            if (is_array($json)) {
                $body = json_encode($json);
                $status = self::SUCCESS;
                $msg = 'SUCCESS';
            } elseif (isset($json->order)) {
                $body = json_encode($json->order);
                $status = self::SUCCESS;
                $msg = 'SUCCESS';
            } else {
                $body = '';
                $status = $json->status;
                $msg = $json->message;
            }

            return array(
                'status' => $status,
                'body' => $body,
                'msg' => $msg
            );
        }
        return false;
    }
}