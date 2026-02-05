<?php
// Load PrestaShop bootstrap (needed for Configuration and other classes)
if (file_exists(__DIR__ . '/../../config/config.inc.php')) {
    require_once __DIR__ . '/../../config/config.inc.php';
} elseif (file_exists(__DIR__ . '/../../../config/config.inc.php')) {
    require_once __DIR__ . '/../../../config/config.inc.php';
}

// STEP 1: Echo-check – Zadarma sends zd_echo for verification
if (isset($_GET['zd_echo'])) {
    exit($_GET['zd_echo']);
}

// STEP 2: Allow only POST method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'status' => 'Test Webhook accessible',
        'time' => date('Y-m-d H:i:s'),
        'domain' => $_SERVER['HTTP_HOST'],
        'vendor_exists' => file_exists(__DIR__ . '/vendor/autoload.php'),
        'path' => __DIR__
    ]);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// STEP 3: Get data (JSON or form-data)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$dataRaw = file_get_contents('php://input');

if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode($dataRaw, true);
} else {
    $data = $_POST;
}

// STEP 4: Data validation
if (empty($data)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No data provided']);
    exit;
}

// STEP 5: Send data to external webhook (if configured)
$externalWebhookUrl = Configuration::get('ZADARMA_WEBHOOK_URL');

if (!empty($externalWebhookUrl) && $externalWebhookUrl !== 'http://localhost/modules/zadarmacallback/webhook.php') {
    try {
        $ch = curl_init($externalWebhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: PrestaShop-Zadarma-Module/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log external webhook errors (optional)
        if ($httpCode < 200 || $httpCode >= 300) {
            // External webhook failed, but continue with Zadarma API
        }
    } catch (Exception $e) {
        // External webhook failed, but continue with Zadarma API
    }
}

// STEP 6: If this is a callback request from form - execute API call to Zadarma
if (isset($data['zadarma_callback']) && $data['zadarma_callback'] == '1') {
    try {
        require_once __DIR__ . '/classes/ZadarmaClient.php';
        
        $client = new ZadarmaClient();
        $fromNumber = $data['from'] ?? '';
        $toNumber = $data['phone'] ?? '';
        
        if (empty($fromNumber) || empty($toNumber)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Missing phone numbers']);
            exit;
        }
        
        $result = $client->sendCallbackOfficial($toNumber, $fromNumber);
        
        // ✅ SUCCESS - API call completed
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Callback request sent successfully']);
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'API call failed: ' . $e->getMessage()]);
        exit;
    }
}

// STEP 7: Respond to Zadarma/Frontend (fallback for other requests)
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Request processed']);
exit;
