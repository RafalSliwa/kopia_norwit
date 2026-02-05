<?php

namespace Roja45\LicenseManager\APIHelper;

class HttpException extends \Exception
{
    /**
     * @var statusCode
     */
    public $statusCode;

    /**
     * @param string $response
     */
    public function __construct($message, $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }
}
