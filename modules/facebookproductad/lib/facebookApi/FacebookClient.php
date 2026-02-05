<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\FacebookApi;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Models\apiLog;
use FacebookProductAd\ModuleLib\moduleTools;

class FacebookClient
{
    const API_VERSION = 'v20.0'; // Facebook Graph API version used for all requests

    /**
     * The API url
     *
     * @var string
     */
    public static $api_url;

    /**
     * Store processed event IDs to prevent duplicates
     *
     * @var array
     */
    private static $processedEventIds = [];

    /**
     * Store processed event IDs in context to prevent duplicates across page loads
     *
     * @var string
     */
    private static $contextKey = 'FPA_PROCESSED_EVENT_IDS';

    public function __construct()
    {
        // Initialize the processed events from context if available
        $context = \Context::getContext();
        if (isset($context->cookie->{self::$contextKey})) {
            $savedIds = json_decode($context->cookie->{self::$contextKey}, true);
            if (is_array($savedIds)) {
                self::$processedEventIds = $savedIds;
            }
        }
    }

    /**
     * Send formatted data to Facebook API
     *
     * @param mixed $data The data to be sent to Facebook API
     *
     * @return mixed|null The API response or null if the call was not made
     */
    public static function send($data)
    {
        try {
            $response = null;
            // Only handle the API call if we have pixel token and if the feature is activated
            if (!empty(\FacebookProductAd::$conf['FPA_ACTIVATE_PIXEL']) && !empty(\FacebookProductAd::$conf['FPA_PIXEL']) && !empty(\FacebookProductAd::$conf['FPA_TOKEN_API']) && !empty(\FacebookProductAd::$conf['FPA_USE_API'])) {
                // Validate and format the data before sending
                // If data is not in the expected format with a 'data' array, wrap it
                if (!isset($data['data'][0])) {
                    $data = ['data' => [$data]];
                }

                // Add required fields and deduplicate events
                $filteredEvents = [];
                foreach ($data['data'] as $event) {
                    // event_id should always be provided by moduleTools::getApiUserData()
                    // FacebookClient is only a transporter and should not generate event_id


                    // Create a more specific deduplication key that includes the event name
                    // This prevents different event types with the same ID from being filtered
                    $deduplicationKey = $event['event_name'] . '_' . $event['event_id'];

                    // Check if this event has already been processed to avoid duplicates
                    if (in_array($deduplicationKey, self::$processedEventIds)) {
                        // Skip this event as it's a duplicate
                        \PrestaShopLogger::addLog(
                            'Facebook API: Skipping duplicate event: ' . $event['event_name'] . ' with ID: ' . $event['event_id'],
                            1,
                            null,
                            null,
                            null,
                            true
                        );
                        continue;
                    }

                    // Add this event_id to the processed list for future deduplication
                    self::$processedEventIds[] = $deduplicationKey;

                    // Limit the size of the processed events array to prevent memory issues
                    // Only keep the most recent 200 events
                    if (count(self::$processedEventIds) > 200) {
                        array_shift(self::$processedEventIds);
                    }

                    // Store in cookie for cross-request deduplication
                    // This ensures events aren't duplicated even across page loads
                    $context = \Context::getContext();
                    $context->cookie->{self::$contextKey} = json_encode(self::$processedEventIds);
                    $context->cookie->write();

                    // Add required fields for server-side API if not already set
                    if (!isset($event['event_time'])) {
                        $event['event_time'] = time();
                    }
                                        if (!isset($event['event_source_url'])) {
                        // Fix for invalid.invalid domain issue - PHP 7.0+ compatible
                        // Universal solution for all clients
                        $host = '';
                        $uri = '';

                        // Try to get host from various sources
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $host = $_SERVER['HTTP_HOST'];
                        } else {
                            // Fallback: try to get from PrestaShop configuration
                            $shopDomain = \Configuration::get('PS_SHOP_DOMAIN');
                            if (!empty($shopDomain)) {
                                $host = $shopDomain;
                            } else {
                                // Try alternative PrestaShop domain configurations
                                $shopDomainSSL = \Configuration::get('PS_SHOP_DOMAIN_SSL');
                                if (!empty($shopDomainSSL)) {
                                    $host = $shopDomainSSL;
                                } else {
                                    // Try to build from shop URL configuration
                                    $shopUrl = \Configuration::get('PS_SHOP_URL');
                                    if (!empty($shopUrl)) {
                                        // Remove protocol and trailing slash
                                        $host = preg_replace('#^https?://#', '', $shopUrl);
                                        $host = rtrim($host, '/');
                                    } else {
                                        // Final fallback: use current context shop domain
                                        $context = \Context::getContext();
                                        if (isset($context->shop) && !empty($context->shop->domain)) {
                                            $host = $context->shop->domain;
                                        } else {
                                            // Last resort: generic placeholder that Facebook will accept
                                            $host = 'shop.localhost';
                                        }
                                    }
                                }
                            }
                        }

                        // Get URI
                        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
                            $uri = $_SERVER['REQUEST_URI'];
                        } else {
                            $uri = '/';
                        }

                        // Validate host to prevent invalid.invalid and other invalid domains
                        if ($host === 'invalid.invalid' ||
                            strpos($host, 'invalid') !== false ||
                            empty($host) ||
                            $host === 'localhost' ||
                            strpos($host, '127.0.0.1') !== false ||
                            strpos($host, '::1') !== false) {

                            // Use a generic but valid domain for Facebook API
                            $host = 'shop.localhost';

                            // Log for debugging
                            \PrestaShopLogger::addLog(
                                'Facebook API: Invalid host detected (' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'undefined') . '), forced to: ' . $host,
                                2,
                                null,
                                'FacebookProductAd',
                                1,
                                true
                            );
                        }

                        // Build protocol - prefer HTTPS for Facebook API
                        $protocol = 'https://';
                        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                            $protocol = 'https://';
                        } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
                            $protocol = 'https://';
                        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                            $protocol = 'https://';
                        } elseif (\Configuration::get('PS_SSL_ENABLED')) {
                            $protocol = 'https://';
                        }

                        $event['event_source_url'] = $protocol . $host . $uri;
                    }

                    // Add action_source for all events - required by Facebook API
                    if (!isset($event['action_source'])) {
                        $event['action_source'] = 'website';
                    }

                    // Ensure user_data is properly formatted for matching
                    // Handle legacy format where user_properties was used instead of user_data
                    if (!isset($event['user_data']) && isset($event['user_properties'])) {
                        $event['user_data'] = $event['user_properties'];
                        unset($event['user_properties']);
                    }

                    // Ensure user_data contains hashed values for matching
                    // Facebook requires all PII to be hashed with SHA-256
                    if (isset($event['user_data'])) {
                        // Hash user data if not already hashed (hashed values are 64 chars long)
                        if (isset($event['user_data']['em']) && strlen($event['user_data']['em']) < 64) {
                            $event['user_data']['em'] = hash('sha256', strtolower(trim($event['user_data']['em'])));
                        }
                        if (isset($event['user_data']['fn']) && strlen($event['user_data']['fn']) < 64) {
                            $event['user_data']['fn'] = hash('sha256', strtolower(trim($event['user_data']['fn'])));
                        }
                        if (isset($event['user_data']['ln']) && strlen($event['user_data']['ln']) < 64) {
                            $event['user_data']['ln'] = hash('sha256', strtolower(trim($event['user_data']['ln'])));
                        }
                        if (isset($event['user_data']['ph']) && strlen($event['user_data']['ph']) < 64) {
                            // Remove non-numeric characters and hash
                            $phone = preg_replace('/[^0-9]/', '', $event['user_data']['ph']);
                            $event['user_data']['ph'] = hash('sha256', $phone);
                        }
                    }

                    // Special handling for Purchase event - Facebook has specific requirements
                    if (isset($event['event_name']) && $event['event_name'] === 'Purchase') {
                        // Ensure action_source is set
                        if (!isset($event['action_source'])) {
                            $event['action_source'] = 'website';
                        }

                        // Format content_ids properly if they exist
                        // content_ids must be an array of strings for Facebook API
                        if (isset($event['custom_data']['content_ids'])) {
                            $contentIds = $event['custom_data']['content_ids'];
                            if (!is_array($contentIds)) {
                                $contentIds = array($contentIds);
                            }

                            // Clean and format each ID - ensure proper format for Facebook API
                            $formattedIds = array_map(function($id) {
                                // Remove any brackets and trim whitespace
                                $cleanId = trim(str_replace(['[', ']'], '', $id));
                                // Remove any quotes (Facebook API will add proper JSON quotes during encoding)
                                $cleanId = str_replace(['"', "'"], '', $cleanId);
                                return $cleanId;
                            }, $contentIds);

                            // Update content_ids with formatted values - must be a flat array of strings
                            $event['custom_data']['content_ids'] = array_values(array_unique($formattedIds));
                        }

                        // Ensure content_type is set for Purchase events - required by Facebook
                        if (!isset($event['custom_data']['content_type'])) {
                            $event['custom_data']['content_type'] = 'product';
                        }

                        // Ensure currency is uppercase - Facebook API requirement
                        if (isset($event['custom_data']['currency'])) {
                            $event['custom_data']['currency'] = strtoupper($event['custom_data']['currency']);
                        }
                    }

                    // Add the processed event to our filtered list
                    $filteredEvents[] = $event;
                }

                // If we have no events after filtering duplicates, return early
                if (empty($filteredEvents)) {
                    return null;
                }

                // Update data with filtered events
                $data['data'] = $filteredEvents;
                // Construct the API URL with proper parameters
                $test_event_code = null;
                if (!empty(\FacebookProductAd::$conf['FPA_TEST_EVENT_CODE']) &&
                    !empty(\FacebookProductAd::$conf['FPA_USE_TEST_EVENT_CODE'])) {
                    $test_event_code = htmlspecialchars(trim(\FacebookProductAd::$conf['FPA_TEST_EVENT_CODE']), ENT_QUOTES, 'UTF-8');
                }

                // Build the Facebook Graph API URL with pixel ID and access token
                $url = sprintf(
                    'https://graph.facebook.com/%s/%s/events?access_token=%s',
                    self::API_VERSION,
                    urlencode((string) \FacebookProductAd::$conf['FPA_PIXEL']),
                    urlencode((string) \FacebookProductAd::$conf['FPA_TOKEN_API'])
                );

                // Only append test_event_code if it's not null
                // Test event code is used for debugging in Facebook Events Manager
                if ($test_event_code !== null) {
                    $url .= '&test_event_code=' . urlencode($test_event_code);
                }

                // Initialize cURL session for API request
                $curl = curl_init();

                // Set cURL options with improved error handling and security
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                    CURLOPT_TIMEOUT => 10, // 10 second timeout for the request
                    CURLOPT_SSL_VERIFYPEER => true, // Verify SSL certificate
                    CURLOPT_SSL_VERIFYHOST => 2, // Verify hostname in SSL certificate
                    CURLOPT_CONNECTTIMEOUT => 5, // 5 second timeout for connection
                    CURLOPT_USERAGENT => 'PrestaShop-FacebookProductAd/' . _PS_VERSION_, // Identify the client
                    CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects for security
                ));

                // Execute cURL request and get the response
                $response = curl_exec($curl);

                // Check for cURL errors
                if (curl_errno($curl)) {
                    throw new \Exception('cURL Error: ' . curl_error($curl));
                }

                // Decode JSON response from Facebook
                $decodedResponse = json_decode($response);

                // Handle API errors by logging them
                if (isset($decodedResponse->error)) {
                    $apiLog = new apiLog();
                    $apiLog->error_message = moduleTools::formatApiErrorMessage($decodedResponse->error);
                    $apiLog->page_event = (string) moduleTools::detectCurrentPage();
                    $apiLog->id_shop = \FacebookProductAd::$iShopId;
                    $apiLog->add();

                    // Log the request data for debugging Purchase events specifically
                    if (isset($data['data'][0]['event_name']) && $data['data'][0]['event_name'] === 'Purchase') {
                        \PrestaShopLogger::addLog(
                            'Facebook API Purchase Event Error: ' . json_encode([
                                'request' => $data,
                                'response' => $decodedResponse
                            ]),
                            2,
                            null,
                            null,
                            null,
                            true
                        );
                    }
                }

                // Close cURL session to free resources
                curl_close($curl);
            }

            return $response;
        } catch (\Exception $e) {
            // Log any exceptions that occur during processing
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
            return null;
        }
    }
}
