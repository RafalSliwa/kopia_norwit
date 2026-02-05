<?php
/**
 * HeartbeatRequest
 *
 * @category  HeartbeatRequest
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
 * HeartbeatRequest
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

namespace Roja45\LicenseManager\Prestashop\Api\v1\Request;

use Roja45\LicenseManager\APIHelper\HttpRequest;
use Roja45\LicenseManager\APIHelper\InvalidArgumentException;

class HeartbeatRequest extends HttpRequest
{
    protected $module_name;
    protected $module_version;
    protected $purchased_from;
    protected $order_reference;
    protected $customer_email;
    protected $domain;
    protected $sending_host;
    protected $status;
    protected $connection;

    /**
     * Request constructor.
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array $body
     *
     * @throws InvalidArgumentException
     */
    public function __construct() {
        return parent::__construct(
            HttpRequest::GET
        );
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * @param mixed $module_name
     */
    public function setModuleName($module_name)
    {
        $this->module_name = $module_name;
    }

    /**
     * @return mixed
     */
    public function getModuleVersion()
    {
        return $this->module_version;
    }

    /**
     * @param mixed $module_version
     */
    public function setModuleVersion($module_version)
    {
        $this->module_version = $module_version;
    }

    /**
     * @return mixed
     */
    public function getPurchasedFrom()
    {
        return $this->purchased_from;
    }

    /**
     * @param mixed $purchased_from
     */
    public function setPurchasedFrom($purchased_from)
    {
        $this->purchased_from = $purchased_from;
    }

    /**
     * @return mixed
     */
    public function getOrderReference()
    {
        return $this->order_reference;
    }

    /**
     * @param mixed $order_reference
     */
    public function setOrderReference($order_reference)
    {
        $this->order_reference = $order_reference;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->customer_email;
    }

    /**
     * @param mixed $customer_email
     */
    public function setCustomerEmail($customer_email)
    {
        $this->customer_email = $customer_email;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getSendingHost()
    {
        return $this->sending_host;
    }

    /**
     * @param mixed $sending_host
     */
    public function setSendingHost($sending_host)
    {
        $this->sending_host = $sending_host;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getBody()
    {
        return json_encode(array(
            'api_key' => $this->getApiKey(),
            'module_name' => $this->getModuleName(),
            'module_version' => $this->getModuleVersion(),
            'order_reference' => $this->getOrderReference(),
            'purchased_from' => $this->getPurchasedFrom(),
            'customer_email' => $this->getCustomerEmail(),
            'domain' => $this->getDomain(),
            'sending_host' => $this->getSendingHost(),
            'status' => $this->getStatus(),
            'connection' => $this->getConnection()
        ), false);
    }
}
