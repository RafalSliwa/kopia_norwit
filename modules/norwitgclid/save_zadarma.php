<?php
/**
 * Wewnętrzny endpoint do zapisywania numeru Zadarma
 * Używany przez JavaScript bridge (bez API key - walidacja przez sesję)
 */

// Załaduj PrestaShop
require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/norwitgclid.php';

// Tylko POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Nagłówki
header('Content-Type: application/json');

// Pobierz dane
$sessionId = Tools::getValue('session_id');
$zadarmaNumber = Tools::getValue('zadarma_number');
$phoneDisplayed = Tools::getValue('phone_displayed');

// Walidacja
if (empty($sessionId) || empty($zadarmaNumber)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Walidacja sesji - session_id musi pasować do aktualnej sesji
// (zabezpieczenie przed nadużyciem)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sprawdź czy session_id pochodzi z prawdziwej sesji z GCLID
$existingRecord = Db::getInstance()->getValue(
    "SELECT id FROM `" . _DB_PREFIX_ . "norwit_gclid`
     WHERE session_id = '" . pSQL($sessionId) . "'
     AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"
);

if (!$existingRecord) {
    // Nie ma rekordu dla tej sesji - odmów
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid session']);
    exit;
}

// Zapisz numer Zadarma
$success = NorwitGclid::saveZadarmaNumber($sessionId, $zadarmaNumber, $phoneDisplayed);

echo json_encode([
    'success' => $success,
    'message' => $success ? 'Number saved' : 'Failed to save'
]);
