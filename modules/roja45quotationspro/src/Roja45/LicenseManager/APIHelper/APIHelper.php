<?php
/**
 * APIHelper
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

namespace Roja45\LicenseManager\APIHelper;

class APIHelper
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const LIVE_MODE = 1;
    const TEST_MODE = 0;
    const SUCCESS = 200;
    const ERROR = 300;
    const CONNECTION_ERROR = 301;
    const CONFIGURATION_ERROR = 302;
    const AUTH_ERROR = 401;

    private static $instance = null;

    /**
     * The http connection.
     * @var resource httpClient
     */
    protected $httpClient;

    /**
     * The REST API key.
     * @var string $api_username
     */
    protected $api_username;

    /**
     * The REST API key.
     * @var string $api_password
     */
    protected $api_password;

    /**
     * The REST API key.
     * @var string $api_password
     */
    protected $api_key;

    /**
     * The REST API key.
     * @var string $api_token
     */
    protected $api_token;

    /**
     * The REST API key.
     * @var string $api_token_expires
     */
    protected $api_token_expires;

    /**
     * The REST API URL.
     * @var string $api_url
     */
    protected $api_url;

    /**
     * Error code if an issue is present.
     * @var string $error_code
     */
    protected $error_code;

    /**
     * Error message if an issue is present.
     * @var string $error_msg
     */
    protected $error_msg;

    /**
     * APIHelper constructor.
     *
     * @param string $api_url
     * @param string $api_key
     * @param int    $mode     Test ir Live mode
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $api_url,
        $api_key,
        $mode = self::LIVE_MODE
    ) {
        $this->setApiUrl($api_url);
        $this->setApiKey($api_key);
        $this->setMode((int) $mode);
    }

    /**
     * HttpClient
     *
     * Automatically load Guzzle when available
     *
     * @return CurlClient
     */
    public function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = CurlClient::getInstance();
        }
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->error_code;
    }

    /**
     * @param string $api_url
     */
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;
    }

    /**
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    /**
     * @param string $api_url
     */
    public function setErrorMsg($error_msg)
    {
        $this->error_msg = $error_msg;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->api_url;
    }

    /**
     * @param string $api_url
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
    }

    /**
     * Get theAPI key
     *
     * @return int
     */
    public function getApiUsername()
    {
        return $this->api_username;
    }

    /**
     * Get theAPI key
     *
     * @return int
     */
    public function getApiPassword()
    {
        return $this->api_password;
    }

    /**
     * Get theAPI key
     *
     * @return int
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Get the API key
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->api_token;
    }

    /**
     * Get the API key
     *
     * @return int
     */
    public function getApiTokenExpires()
    {
        return $this->api_token_expires;
    }

    /**
     * Set the token
     *
     * @param string|UsernameToken $token
     *
     * @return APIHelper
     * @throws InvalidArgumentException
     */
    public function setApiUsername($api_username)
    {
        if (is_string($api_username)) {
            $this->api_username = $api_username;
            return $this;
        }
        throw new InvalidArgumentException('Invalid username/token');
    }

    /**
     * Set the token
     *
     * @param string|UsernameToken $token
     *
     * @return APIHelper
     * @throws InvalidArgumentException
     */
    public function setApiPassword($api_password)
    {
        if (is_string($api_password)) {
            $this->api_password = $api_password;
            return $this;
        }
        throw new InvalidArgumentException('Invalid username/token');
    }

    /**
     * Set the token
     *
     * @param string $api_key
     *
     * @return APIHelper
     * @throws InvalidArgumentException
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    /**
     * Set the token
     *
     * @param string $api_token
     *
     * @return APIHelper
     * @throws InvalidArgumentException
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
        return $this;
    }

    /**
     * Set the token
     *
     * @param string $api_token_expires
     *
     * @return APIHelper
     * @throws InvalidArgumentException
     */
    public function setApiTokenExpires($api_token_expires)
    {
        $this->api_token_expires = $api_token_expires;
        return $this;
    }

    /**
     * Get the current mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set current mode
     *
     * @param int $mode
     *
     * @return PostNL
     *
     * @throws InvalidArgumentException
     */
    public function setMode($mode)
    {
        if (!in_array($mode, [
            static::LIVE_MODE,
            static::TEST_MODE
        ])) {
            throw new InvalidArgumentException('Mode not supported', self::CONFIGURATION_ERROR);
        }
        $this->mode = (int) $mode;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $args
     *
     * @return mixed
     *
     * @throws InvalidMethodException
     */
    public function call($name, $args)
    {
        //$mode = $this->getMode() === self::LIVE_MODE ? 'Live' : 'Test';
        if (method_exists($this, "{$name}")) {
            return call_user_func_array([$this, "{$name}"], $args);
        } elseif (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $args);
        }
        $class = get_called_class();
        throw new InvalidMethodException("`$class::$name` is not a valid method", self::CONFIGURATION_ERROR);
    }

    public function isTokenValid($api_token, $api_token_expires)
    {
        if (!$api_token || !$api_token_expires) {
            return false;
        }
        $timestamp = strtotime("now");
        if ($timestamp < $api_token_expires) {
            $this->setApiToken($api_token);
            return true;
        } else {
            return false;
        }
    }
}
