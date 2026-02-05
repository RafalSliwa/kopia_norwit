<?php
/**
 * HttpRequest
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

class HttpRequest
{
    const POST = 'POST';
    const GET = 'GET';

    /** @var string $uri */
    protected $uri;
    /** @var string $method */
    private $method;
    /** @var string $body */
    protected $body;
    /** @var array $headers */
    protected $headers;
    /** @var array $username */
    protected $username;
    /** @var array $password */
    protected $password;
    /** @var array $api_key */
    protected $api_key;
    /** @var array $token */
    protected $token;
    /**
     * @return array
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param array $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return array
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param array $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param string $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    /**
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param array $token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * HttpRequest constructor.
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array $body
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $method = null,
        $uri = null,
        $headers = array(),
        $body = array()
    ) {
        if ($method) {
            $this->setMethod($method);
        }
        if ($uri) {
            $this->setUri($uri);
        }

        $this->setHeaders($headers);
        $this->setBody($body);
        return $this;
    }

    /**
     * Get URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set timeout
     *
     * @param string $uri
     *
     * @return HttpRequest
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return HttpRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get request body as JSON
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return HttpRequest
     */
    public function setBody($body)
    {
        if (is_array($body) && count($body)) {
            $body = json_encode($body);
            $this->body = $body;
        }

        return $this;
    }

    /**
     * Get request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set headers
     *
     * @param array $headers
     *
     * @return HttpRequest
     */
    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $this->headers = $headers;
            return $this;
        }
        throw new InvalidArgumentException('Headers should be an array');
    }
}
