<?php
/**
 * SearchUserResponse
 *
 * @category  SearchUserResponse
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
 * SearchUserResponse
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

namespace Roja45\LicenseManager\Prestashop\Api\v1\Response;

use Roja45\LicenseManager\APIHelper\HttpResponse;

class ValidateModuleResponse extends HttpResponse
{
    const SUCCESS = 200;
    const ERROR = 422;

    protected $authorization_key;

    public function __construct($statusCode = null, $response = null, $message = null)
    {
        parent::__construct($statusCode, $response, $message);
        return $this;
    }

    /**
     * @return array
     */
    public function getAuthorizationKey()
    {
        return $this->authorization_key;
    }

    public function setAuthorizationKey($authorization_key)
    {
        $this->authorization_key = $authorization_key;
    }

    public function isSuccess()
    {
        if ($this->statusCode==self::SUCCESS) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateResponse($json_response)
    {
        $response = new ValidateModuleResponse();
        if ($json = parent::validateResponse($json_response)) {
            if (isset($json->error)) {
                $status = self::ERROR;
                $msg = $json->error;
            } else if ($json->registered == 0) {
                $status = self::ERROR;
                $msg = 'UNREGISTERED';
            } else if ($json->registered == 1) {
                $status = self::SUCCESS;
                $msg = 'SUCCESS';
            }
        } else {
            $status = self::ERROR;
            $msg = 'ERROR';
        }
        $response->setStatusCode($status);
        $response->setResponse($json_response);
        $response->setMessage($msg);
        return $response;
    }
}
