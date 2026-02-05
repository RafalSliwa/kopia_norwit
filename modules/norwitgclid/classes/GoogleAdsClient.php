<?php
/**
 * Google Ads API Client - Offline Conversions
 */

class GoogleAdsClient
{
    private $config;
    private $accessToken;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Upload click conversion to Google Ads
     */
    public function uploadConversion(string $gclid, string $conversionAction, float $value, string $dateTime): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to get access token'];
        }

        $customerId = $this->config['customer_id'];
        $url = "https://googleads.googleapis.com/v18/customers/{$customerId}:uploadClickConversions";

        $body = [
            'conversions' => [[
                'gclid' => $gclid,
                'conversionAction' => "customers/{$customerId}/conversionActions/{$conversionAction}",
                'conversionDateTime' => $this->formatDateTime($dateTime),
                'conversionValue' => $value,
                'currencyCode' => 'PLN'
            ]],
            'partialFailure' => true
        ];

        return $this->request('POST', $url, $body, $token);
    }

    /**
     * Get OAuth2 access token using refresh token
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = $this->request('POST', 'https://oauth2.googleapis.com/token', [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'refresh_token' => $this->config['refresh_token'],
            'grant_type' => 'refresh_token'
        ], null, true);

        if ($response['success'] && isset($response['data']['access_token'])) {
            $this->accessToken = $response['data']['access_token'];
            return $this->accessToken;
        }

        return null;
    }

    /**
     * HTTP request helper
     */
    private function request(string $method, string $url, array $data, ?string $token = null, bool $formEncoded = false): array
    {
        $ch = curl_init($url);

        $headers = ['Accept: application/json'];

        if ($token) {
            $headers[] = "Authorization: Bearer {$token}";
            $headers[] = "developer-token: {$this->config['developer_token']}";
        }

        if ($formEncoded) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $body = http_build_query($data);
        } else {
            $headers[] = 'Content-Type: application/json';
            $body = json_encode($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        $decoded = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => $decoded
        ];
    }

    /**
     * Format datetime for Google Ads API (yyyy-mm-dd hh:mm:ss+|-hh:mm)
     */
    private function formatDateTime(string $dateTime): string
    {
        $dt = new DateTime($dateTime, new DateTimeZone('Europe/Warsaw'));
        return $dt->format('Y-m-d H:i:sP');
    }
}
