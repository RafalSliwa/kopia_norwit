<?php
/**
 * CurlClient
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

class CurlClient
{
    const DEFAULT_TIMEOUT = 80;
    const DEFAULT_CONNECT_TIMEOUT = 30;
    const NO_AUTH = 'NONE';
    const BASIC_AUTH = 'BASIC';
    const HEADER_AUTH = 'HEADER';

    /** @var int $timeout */
    private $timeout = self::DEFAULT_TIMEOUT;
    /** @var int $connectTimeout */
    private $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;
    /**
     * Verify the server SSL certificate
     *
     * @var bool|string $verify
     */
    private $verify = true;
    /** @var static $instance */
    private static $instance;
    /** @var array|callable|null $defaultOptions */
    protected $defaultOptions;
    /** @var array $userAgentInfo */
    protected $userAgentInfo;
    /** @var array $pendingRequests */
    protected $pendingRequests = [];
    /** @var array $authType */
    protected $authType = CurlClient::BASIC_AUTH;

    /**
     * @return array
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param array $authType
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;
    }

    /**
     * CurlClient Singleton
     *
     * @return CurlClient
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Set timeout
     *
     * @param int $seconds
     *
     * @return CurlClient
     */
    public function setTimeout($seconds)
    {
        $this->timeout = (int) max($seconds, 0);
        return $this;
    }

    /**
     * Set connection timeout
     *
     * @param int $seconds
     *
     * @return CurlClient
     */
    public function setConnectTimeout($seconds)
    {
        $this->connectTimeout = (int) max($seconds, 0);
        return $this;
    }

    /**
     * Set the verify setting
     *
     * @param bool|string $verify
     *
     * @return CurlClient
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
        return $this;
    }

    /**
     * Get timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get connection timeout
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * Return verify setting
     *
     * @return bool|string
     */
    public function getVerify()
    {
        return $this->verify;
    }

    /**
     * Adds a request to the list of pending requests
     * Using the ID you can replace a request
     *
     * @param string $id      Request ID
     * @param string $request PSR-7 request
     *
     * @return int|string
     */
    public function addOrUpdateRequest($id, $request)
    {
        if (is_null($id)) {
            return array_push($this->pendingRequests, $request);
        }

        $this->pendingRequests[$id] = $request;

        return $id;
    }

    /**
     * Remove a request from the list of pending requests
     *
     * @param string $id
     */
    public function removeRequest($id)
    {
        unset($this->pendingRequests[$id]);
    }

    /**
     * Clear all pending requests
     */
    public function clearRequests()
    {
        $this->pendingRequests = [];
    }

    /**
     * Do a single request
     *
     * Exceptions are captured into the result array
     *
     * @param HttpRequest $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function doRequest(HttpRequest $request)
    {
        $curl = curl_init();
        // Create a callback to capture HTTP headers for the response
        $this->prepareRequest($curl, $request);
        $rbody = curl_exec($curl);

        if(!curl_errno($curl)) {
            $info = curl_getinfo($curl);
            $request_header_info = curl_getinfo($curl, CURLINFO_HEADER_OUT);
            //throw new \Exception('Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']);
        }

        if ($rbody === false) {
            $errno = curl_errno($curl);
            $message = curl_error($curl);
            curl_close($curl);
            $this->handleCurlError($request->getUri(), $errno, $message);
        }

        // Todo - add debug option.
        curl_close($curl);

        return $rbody;
    }

    /**
     * @param resource $curl
     * @param HttpRequest $request
     *
     * @throws ApiException
     */
    protected function prepareRequest($curl, HttpRequest $request)
    {
        $method = strtolower($request->getMethod());
        $body = (string) $request->getBody();
        $headers = [];
        foreach ($request->getHeaders() as $key=>$value) {
            $headers[] = "$key: $value";
        }
        $headers[] = 'Expect:';
        $opts = [];
        if (is_callable($this->defaultOptions)) { // call defaultOptions callback, set options to return value
            $opts = call_user_func_array($this->defaultOptions, func_get_args());
            if (!is_array($opts)) {
                throw new ApiException("Non-array value returned by defaultOptions CurlClient callback");
            }
        } elseif (is_array($this->defaultOptions)) { // set default curlopts from array
            $opts = $this->defaultOptions;
        }
        $opts[CURLOPT_URL] = $request->getUri();
        if ($method == 'get') {
            $opts[CURLOPT_HTTPGET] = 1;
            if ($body) {
                $params = http_build_query(json_decode($body));
                $opts[CURLOPT_URL] = $request->getUri() . '?';
                if (strlen($params)) {
                    $opts[CURLOPT_URL] .= $params;
                }
            }
        } elseif ($method == 'post') {
            $opts[CURLOPT_POST] = 1;
            if ($body) {
                //$params = http_build_query(json_decode($body));
                $opts[CURLOPT_POSTFIELDS] = $body;
                //$opts[CURLOPT_URL] = $request->getUri() . '?' . $params;
                //if (strlen($params)) {
                //    $opts[CURLOPT_URL] .= $params;
                //}
            }
        } elseif ($method == 'put') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($body) {
                $opts[CURLOPT_POSTFIELDS] = $body;
            }
        } elseif ($method == 'delete') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        } else {
            throw new ApiException("Unrecognized method $method");
        }

        $opts[CURLOPT_RETURNTRANSFER] = 1;
        $opts[CURLOPT_HEADER] = 0;
        if ($this->authType == CurlClient::BASIC_AUTH) {
            $opts[CURLOPT_USERPWD] = $request->getUsername() . ":" . $request->getPassword();
        }
        $opts[CURLOPT_USERAGENT] = 'curl/7.76.1';
        $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[CURLOPT_TIMEOUT] = $this->timeout;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        $opts[CURLOPT_FAILONERROR] = 0;
        $opts[CURLOPT_FRESH_CONNECT] = 1;
        $opts[CURLOPT_AUTOREFERER] = 1;

        if ($this->verify) {
            $opts[CURLOPT_VERBOSE] = 1;
            $opts[CURLINFO_HEADER_OUT] = 1;
            $opts[CURLOPT_SSL_VERIFYPEER] = 0;
            $opts[CURLOPT_SSL_VERIFYHOST] = 2;
            if (is_string($this->verify)) {
                $opts[CURLOPT_CAINFO] = $this->verify;
            }
        } else {
            $opts[CURLOPT_SSL_VERIFYPEER] = 0;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        curl_setopt_array($curl, $opts);
    }
    /**
     * @param number $errno
     * @param string $message
     *
     * @throws HttpException
     */
    private function handleCurlError($url, $errno, $message)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to ($url).  Please check your "
                    ."internet connection and try again.";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify SSL certificate.  Please make sure "
                    ."that your network is not intercepting certificates.  "
                    ."(Try going to $url in your browser.)  "
                    ."If this problem persists,";
                break;
            default:
                $msg = "Unexpected error communicating with API.  "
                    ."If this problem persists,";
        }
        $msg .= " contact administrator";
        $msg .= "\n\n(Network error [errno $errno]: $message)";
        throw new HttpException($msg, $errno);
    }
}
