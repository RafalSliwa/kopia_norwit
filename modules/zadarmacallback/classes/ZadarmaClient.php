<?php

class ZadarmaClient
{
    /**
     * Sends callback request through Zadarma API
     *
     * @param string $to   Customer number (target)
     * @param string|null $from FROM number (if null, will use from configuration)
     * @return bool
     * @throws Exception
     */
    public function sendCallback($to, $from = null)
    {
        $apiKey = Configuration::get('ZADARMA_API_KEY');
        $apiSecret = Configuration::get('ZADARMA_API_SECRET');

        if (!$from) {
            $from = Configuration::get('ZADARMA_FROM_NUMBER');
        }

        if (!$apiKey || !$apiSecret || !$from || !$to) {
            throw new Exception('Missing required data for API call.');
        }

        $params = [
            'from' => $from,
            'to' => $to,
            'voice_start' => '1'
        ];

        // Correct signature according to Zadarma documentation
        ksort($params);
        $paramsStr = http_build_query($params, null, '&', PHP_QUERY_RFC1738);
        $method = '/v1/request/callback/';
        $signString = $method . $paramsStr . md5($paramsStr);
        $sign = base64_encode(hash_hmac('sha1', $signString, $apiSecret, true));

        // Log request details (for debugging)
        /*
        PrestaShopLogger::addLog(
            '[Zadarma API][DEBUG] Callback request details: ' . json_encode([
                'endpoint' => 'https://api.zadarma.com' . $method,
                'apiKey' => $apiKey,
                'sign' => $sign,
                'signString' => $signString,
                'params' => $paramsStr,
                'headers' => [
                    'Authorization: ' . $apiKey . ':' . $sign,
                    'Content-Type: application/x-www-form-urlencoded'
                ]
            ]),
            1, // info
            null,
            'ZadarmaClient',
            null,
            true
        );
        */

        // Send request
        $ch = curl_init('https://api.zadarma.com' . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey . ':' . $sign,
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log response (for debugging)
        /*
        PrestaShopLogger::addLog(
            '[Zadarma API] Callback response: ' . $response,
            ($httpCode !== 200 ? 3 : 1), // 1 = info, 3 = error
            null,
            'ZadarmaClient',
            null,
            true
        );
        */

        $decoded = json_decode($response, true);

        if ($httpCode !== 200 || empty($decoded['status']) || $decoded['status'] !== 'success') {
            throw new Exception('Zadarma API error: ' . $response);
        }

        return true;
    }

    /**
     * Sends callback using official Zadarma library (same as testConnection)
     */
    public function sendCallbackOfficial($to, $from = null)
    {
        $apiKey = Configuration::get('ZADARMA_API_KEY');
        $apiSecret = Configuration::get('ZADARMA_API_SECRET');

        if (!$from) {
            $from = Configuration::get('ZADARMA_FROM_NUMBER');
        }

        if (!$apiKey || !$apiSecret || !$from || !$to) {
            throw new Exception('Missing required data for API call.');
        }

        // Use the same library as testConnection
        require_once dirname(__DIR__) . '/vendor/autoload.php';
        $api = new \Zadarma_API\Api($apiKey, $apiSecret);

        $params = [
            'from' => $from,
            'to' => $to,
            'voice_start' => '1'
        ];

        try {
            $response = $api->call('/v1/request/callback/', $params);
            $decoded = json_decode($response, true);

            // Log callback response (for debugging)
            // PrestaShopLogger::addLog('[Zadarma][DEBUG] Callback response: ' . $response, 1);

            if (!isset($decoded['status'])) {
                throw new Exception('No status in callback API response. Returned: ' . $response);
            }

            if ($decoded['status'] !== 'success') {
                throw new Exception('Zadarma API error: ' . $response);
            }

            return $decoded;

        } catch (Exception $e) {
            throw new Exception('Zadarma API error: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to Zadarma API (checks account balance) - uses official library
     *
     * @return bool
     * @throws Exception
     */
   public function testConnection()
    {
        $apiKey = Configuration::get('ZADARMA_API_KEY');
        $apiSecret = Configuration::get('ZADARMA_API_SECRET');

        if (!$apiKey || !$apiSecret) {
            throw new Exception('Missing API Key or Secret.');
        }

        require_once dirname(__DIR__) . '/vendor/autoload.php';
        $api = new \Zadarma_API\Api($apiKey, $apiSecret);

        try {
            $response = $api->call('/v1/info/balance');

            // Response is a string — we need to parse it
            $decoded = json_decode($response, true);

            // Log parsed response (for debugging)
            // PrestaShopLogger::addLog('[Zadarma][DEBUG] Parsed response: ' . print_r($decoded, true), 1);

            if (!isset($decoded['status'])) {
                throw new Exception('No status in API response. Returned: ' . $response);
            }

            if ($decoded['status'] !== 'success') {
                throw new Exception('Unsuccessful API response: ' . $response);
            }

            return true;

        } catch (Exception $e) {
            throw new Exception('Zadarma connection error: ' . $e->getMessage());
        }
    }
}

