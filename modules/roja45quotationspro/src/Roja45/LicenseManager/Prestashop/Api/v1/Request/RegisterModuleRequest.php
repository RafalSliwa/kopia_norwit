<?php
/**
 * SearchUserRequest
 *
 * @category  SearchUserRequest
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
 * SearchUserRequest
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

class RegisterModuleRequest extends HttpRequest
{
    protected $module_name;
    protected $purchased_from;
    protected $customer_email;
    protected $order_reference;
    protected $registered_domain;
    protected $test_domain;
    protected $account_key;

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
     * @return String
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * @param String $module_name
     */
    public function setModuleName($module_name)
    {
        $this->module_name = $module_name;
    }

    /**
     * @return String
     */
    public function getCustomerEmail()
    {
        return $this->customer_email;
    }

    /**
     * @param String $customer_email
     */
    public function setCustomerEmail($customer_email)
    {
        $this->customer_email = $customer_email;
    }

    /**
     * @return String
     */
    public function getOrderReference()
    {
        return $this->order_reference;
    }

    /**
     * @param String $order_reference
     */
    public function setOrderReference($order_reference)
    {
        $this->order_reference = $order_reference;
    }

    /**
     * @return String
     */
    public function getRegisteredDomain()
    {
        return $this->registered_domain;
    }

    /**
     * @param String $registered_domain
     */
    public function setRegisteredDomain($registered_domain)
    {
        $this->registered_domain = $registered_domain;
    }

    /**
     * @return String
     */
    public function getTestDomain()
    {
        return $this->test_domain;
    }

    /**
     * @param String $test_domain
     */
    public function setTestDomain($test_domain)
    {
        $this->test_domain = $test_domain;
    }

    /**
     * @return String
     */
    public function getAccountKey()
    {
        return $this->account_key;
    }

    /**
     * @param String $account_key
     */
    public function setAccountKey($account_key)
    {
        $this->account_key = $account_key;
    }

    public function getBody()
    {
        return json_encode(array(
            'api_key' => $this->getApiKey(),
            'module_name' => $this->getModuleName(),
            'purchased_from' => $this->getPurchasedFrom(),
            'customer_email' => $this->getCustomerEmail(),
            'order_reference' => $this->getOrderReference(),
            'registered_domain' => $this->getRegisteredDomain(),
            'test_domain' => $this->getTestDomain(),
            'account_key' => $this->getAccountKey()
        ), false);
    }
}
