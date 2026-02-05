<?php

namespace Roja45\LicenseManager\APIHelper;

/**
 * Class HttpResponse
 * @package APIHelper
 *
 * Object that holds your response details
 */
class HttpResponse
{
    const SUCCESS = 200;
    const ERROR = 300;

    /**
     * @var integer
     */
    public $statusCode;

    /**
     * @var array | string
     */
    public $response;

    /**
     * @var array
     */
    public $message;

    public function __construct($statusCode = null, $response = null, $message = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->response = $response;
    }

    public function isSuccess()
    {
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return array|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array|string $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public static function validateResponse($json_response)
    {
        if ($json = json_decode($json_response)) {
            return $json;
        }

        return false;
    }
}
