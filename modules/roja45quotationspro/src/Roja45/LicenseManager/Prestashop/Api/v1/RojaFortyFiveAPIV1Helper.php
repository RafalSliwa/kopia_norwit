<?php
/**
 * PrestashopSellerAPIV1Helper
 *
 * @category  PrestashopSellerAPIV1Helper
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
 * PrestashopSellerAPIV1Helper
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

namespace Roja45\LicenseManager\Prestashop\Api\v1;

use Roja45\LicenseManager\APIHelper\APIHelper;
use Roja45\LicenseManager\APIHelper\CurlClient;
use Roja45\LicenseManager\APIHelper\HttpException;
use Roja45\LicenseManager\APIHelper\InvalidArgumentException;
use Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiHelper;
use Roja45\LicenseManager\Prestashop\Api\v1\Request\ValidateModuleRequest;
use Roja45\LicenseManager\Prestashop\Api\v1\Response\ValidateModuleResponse;
use Roja45\LicenseManager\Prestashop\Api\v1\Request\RegisterModuleRequest;
use Roja45\LicenseManager\Prestashop\Api\v1\Response\RegisterModuleResponse;
use Roja45\LicenseManager\Prestashop\Api\v1\Request\HeartbeatRequest;
use Roja45\LicenseManager\Prestashop\Api\v1\Response\HeartbeatResponse;

class RojaFortyFiveAPIV1Helper extends APIHelper implements RojaFortyFiveApiHelper
{
    protected $api_version;
    protected $auth_token;
    protected $register_endpoint = 'register';
    protected $validate_endpoint = 'validate';
    protected $heartbeat_endpoint = 'heartbeat';
    protected $last_response = null;

        /**
     * OrderHelper constructor.
     *
     * @param string $api_key
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $api_url,
        $api_key,
        $mode = APIHelper::LIVE_MODE
    ) {
        return parent::__construct($api_url, $mode);
    }

    public function registerModule(
        $module_source,
        $module_name,
        $customer_email,
        $order_reference,
        $domain,
        $test_domain,
        $account_key = null
    ) {
        try {
            $request = new RegisterModuleRequest();
            $request
                ->setUri($this->getApiUrl() . $this->register_endpoint)
                ->setApiKey($this->getApiKey())
                ->setMethod(APIHelper::GET);

            $request->setHeaders(
                array(
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                )
            );
            $request->setModuleName($module_name);
            $request->setPurchasedFrom($module_source);
            $request->setCustomerEmail($customer_email);
            $request->setOrderReference($order_reference);
            $request->setRegisteredDomain($domain);
            $request->setTestDomain($test_domain);
            $request->setAccountKey($account_key);

            $client = $this->getHttpClient();
            $client->setAuthType(CurlClient::NO_AUTH);
            $this->last_response = RegisterModuleResponse::validateResponse($client->doRequest($request));
            if ($this->last_response->isSuccess()) {
                return $this->last_response->getAuthorizationKey();
            } else {
                return false;
            }
        } catch (HttpException $e) {
            $this->setErrorCode(APIHelper::CONNECTION_ERROR);
            $this->setErrorMsg($e->getMessage());
            throw new \Exception(
                $this->getErrorMsg(),
                $this->getErrorCode()
            );
        }
    }

    public function validateModule($auth_key, $module_source, $module_name, $customer_email, $domain, $status, $as_html) {
        try {
            $request = new ValidateModuleRequest();
            $request
                ->setUri($this->getApiUrl() . $this->validate_endpoint)
                ->setApiKey($this->getApiKey())
                ->setMethod(APIHelper::GET);

            $request->setHeaders(
                array(
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                )
            );
            $request->setAuthKey($auth_key);
            $request->setModuleName($module_name);
            $request->setPurchasedFrom($module_source);
            $request->setCustomerEmail($customer_email);
            $request->setStatus($status);
            $request->setDomain($domain);
            if ($as_html) {
                $request->setResponseType('html');
            } else {
                $request->setResponseType('json');
            }

            $client = $this->getHttpClient();
            $client->setAuthType(CurlClient::NO_AUTH);
            $this->last_response = ValidateModuleResponse::validateResponse($client->doRequest($request));
            if ($this->last_response->isSuccess()) {
                return true;
            } else {
                return false;
            }
        } catch (HttpException $e) {
            $this->setErrorCode(APIHelper::CONNECTION_ERROR);
            $this->setErrorMsg($e->getMessage());
            throw new \Exception(
                $this->getErrorMsg(),
                $this->getErrorCode()
            );
        }
    }

    public function heartbeat(
        $module_name,
        $module_version,
        $module_source,
        $order_reference,
        $sending_host,
        $account_domain,
        $customer_email,
        $status,
        $connection
    ) {
        try {
            $request = new HeartbeatRequest();
            $request
                ->setUri($this->getApiUrl() . $this->heartbeat_endpoint)
                ->setApiKey($this->getApiKey())
                ->setMethod(APIHelper::GET);

            $request->setHeaders(
                array(
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                )
            );
            $request->setModuleName($module_name);
            $request->setModuleVersion($module_version);
            $request->setPurchasedFrom($module_source);
            $request->setOrderReference($order_reference);
            $request->setSendingHost($sending_host);
            $request->setStatus($status);
            $request->setDomain($account_domain);
            $request->setCustomerEmail($customer_email);
            $request->setStatus($status);
            $request->setConnection($connection);

            $client = $this->getHttpClient();
            $client->setAuthType(CurlClient::NO_AUTH);
            $this->last_response = HeartbeatResponse::validateResponse($client->doRequest($request));
            if ($this->last_response->isSuccess()) {
                return true;
            } else {
                return false;
            }
        } catch (HttpException $e) {
            $this->setErrorCode(APIHelper::CONNECTION_ERROR);
            $this->setErrorMsg($e->getMessage());
            throw new \Exception(
                $this->getErrorMsg(),
                $this->getErrorCode()
            );
        }
    }

    public function getLastError()
    {
        if ($this->last_response) {
            return $this->last_response->getMessage();
        }
        return 'Unknown Error';
    }
}
