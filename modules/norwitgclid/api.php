<?php
/**
 * NorwitGCLID API Endpoint
 *
 * Endpoint dla n8n do pobierania GCLID na podstawie numeru Zadarma
 *
 * Endpoints:
 * - GET /api.php?action=get_gclid&zadarma_number=123456
 * - POST /api.php?action=save_zadarma_number
 * - POST /api.php?action=mark_conversion
 */

// Załaduj PrestaShop
require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/norwitgclid.php';

// API Key do autoryzacji (ustaw własny!)
define('NORWIT_API_KEY', 'CHANGE_THIS_TO_YOUR_SECRET_KEY_123');

// Nagłówki CORS i JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Obsługa preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Sprawdź autoryzację
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
$apiKey = str_replace('Bearer ', '', $authHeader);

if (empty($apiKey)) {
    $apiKey = Tools::getValue('api_key');
}

if ($apiKey !== NORWIT_API_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid API key']);
    exit;
}

// Pobierz akcję
$action = Tools::getValue('action');

switch ($action) {
    case 'get_gclid':
        handleGetGclid();
        break;

    case 'save_zadarma_number':
        handleSaveZadarmaNumber();
        break;

    case 'mark_conversion':
        handleMarkConversion();
        break;

    case 'get_pending_conversions':
        handleGetPendingConversions();
        break;

    case 'health':
        echo json_encode(['status' => 'ok', 'timestamp' => date('c')]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request', 'message' => 'Unknown action']);
}

/**
 * GET: Pobierz GCLID dla numeru Zadarma
 *
 * Parametry:
 * - zadarma_number: numer z Zadarma (wymagany jeśli brak phone)
 * - phone: wyświetlony numer telefonu
 */
function handleGetGclid()
{
    $zadarmaNumber = Tools::getValue('zadarma_number');
    $phone = Tools::getValue('phone');

    if (empty($zadarmaNumber) && empty($phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameter', 'message' => 'zadarma_number or phone required']);
        return;
    }

    $result = null;

    if (!empty($zadarmaNumber)) {
        $result = NorwitGclid::getGclidByZadarmaNumber($zadarmaNumber);
    }

    if (!$result && !empty($phone)) {
        $result = NorwitGclid::getGclidByPhoneDisplayed($phone);
    }

    if ($result) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => (int)$result['id'],
                'gclid' => $result['gclid'],
                'session_id' => $result['session_id'],
                'zadarma_number' => $result['zadarma_number'],
                'ip_address' => $result['ip_address'],
                'landing_page' => $result['landing_page'],
                'created_at' => $result['created_at'],
                'conversion_sent' => (bool)$result['conversion_sent'],
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'GCLID not found for this number',
            'data' => null
        ]);
    }
}

/**
 * POST: Zapisz numer Zadarma dla sesji
 *
 * Body JSON:
 * {
 *   "session_id": "abc123",
 *   "zadarma_number": "48123456789",
 *   "phone_displayed": "+48 123 456 789"
 * }
 */
function handleSaveZadarmaNumber()
{
    $input = json_decode(file_get_contents('php://input'), true);

    $sessionId = $input['session_id'] ?? '';
    $zadarmaNumber = $input['zadarma_number'] ?? '';
    $phoneDisplayed = $input['phone_displayed'] ?? '';

    if (empty($sessionId) || empty($zadarmaNumber)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $success = NorwitGclid::saveZadarmaNumber($sessionId, $zadarmaNumber, $phoneDisplayed);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Zadarma number saved' : 'Failed to save'
    ]);
}

/**
 * POST: Oznacz konwersję jako wysłaną
 *
 * Body JSON:
 * {
 *   "id": 123,
 *   "conversion_type": "PURCHASE_INTENT",
 *   "conversion_value": 100.00
 * }
 */
function handleMarkConversion()
{
    $input = json_decode(file_get_contents('php://input'), true);

    $id = (int)($input['id'] ?? 0);
    $conversionType = $input['conversion_type'] ?? '';
    $conversionValue = (float)($input['conversion_value'] ?? 0);

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        return;
    }

    $success = NorwitGclid::markConversionSent($id, $conversionType, $conversionValue);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Conversion marked' : 'Failed to mark'
    ]);
}

/**
 * GET: Pobierz konwersje oczekujące na wysłanie
 *
 * Parametry:
 * - limit: max ilość (default 100)
 * - days: z ostatnich X dni (default 7)
 */
function handleGetPendingConversions()
{
    $limit = (int)Tools::getValue('limit', 100);
    $days = (int)Tools::getValue('days', 7);

    $results = Db::getInstance()->executeS(
        "SELECT * FROM `" . _DB_PREFIX_ . "norwit_gclid`
         WHERE conversion_sent = 0
         AND gclid IS NOT NULL
         AND gclid != ''
         AND zadarma_number IS NOT NULL
         AND created_at > DATE_SUB(NOW(), INTERVAL " . $days . " DAY)
         ORDER BY created_at DESC
         LIMIT " . $limit
    );

    echo json_encode([
        'success' => true,
        'count' => count($results),
        'data' => $results
    ]);
}
