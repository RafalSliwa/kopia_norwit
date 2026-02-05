<?php
/**
 * ValidateModuleRequest
 *
 * @category  ValidateModuleRequest
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
 * ValidateModuleRequest
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

class ValidateModuleRequest extends HttpRequest
{
    protected $auth_key;
    protected $module_name;
    protected $purchased_from;
    protected $customer_email;
    protected $domain;
    protected $status;
    protected $response_type;

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
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param mixed $auth_key
     */
    public function setAuthKey($auth_key)
    {
        $this->auth_key = $auth_key;
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
    public function getResponseType()
    {
        return $this->esponse_type;
    }

    /**
     * @param mixed $esponse_type
     */
    public function setResponseType($esponse_type)
    {
        $this->esponse_type = $esponse_type;
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

    public function getBody()
    {
        return json_encode(array(
            'api_key' => $this->getApiKey(),
            'auth_key' => $this->getAuthKey(),
            'module_name' => $this->getModuleName(),
            'purchased_from' => $this->getPurchasedFrom(),
            'customer_email' => $this->getCustomerEmail(),
            'domain' => $this->getDomain(),
            'status' => $this->getStatus(),
            'response_type' => $this->getResponseType(),
        ), false);
    }
}
