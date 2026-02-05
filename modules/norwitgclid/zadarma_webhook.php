<?php
/**
 * Zadarma Webhook Endpoint
 *
 * URL: https://norwit.pl/modules/norwitgclid/zadarma_webhook.php
 * Obsługuje: gclid, wbraid (iOS), gbraid
 */

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/norwitgclid.php';
require_once dirname(__FILE__) . '/classes/ConversionQueue.php';

$CONFIG = [
    'conversion_long' => '7478977159',
    'conversion_short' => '7478988194',
    'min_duration_long' => 120,
    'value_long' => 50,
    'value_short' => 5,
];

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$event = $input['event'] ?? '';

logWebhook($input);

if ($event !== 'NOTIFY_END') {
    echo json_encode(['status' => 'ignored', 'reason' => 'not NOTIFY_END']);
    exit;
}

$calledDid = $input['called_did'] ?? '';
$callerId = $input['caller_id'] ?? '';
$duration = (int)($input['duration'] ?? 0);
$callStart = $input['call_start'] ?? date('Y-m-d H:i:s');

$zadarmaNumber = $calledDid ?: $callerId;

if (!$zadarmaNumber) {
    echo json_encode(['status' => 'error', 'reason' => 'no phone number']);
    exit;
}

$record = NorwitGclid::getGclidByZadarmaNumber($zadarmaNumber);

if (!$record) {
    echo json_encode(['status' => 'ok', 'reason' => 'no record for this number', 'number' => $zadarmaNumber]);
    exit;
}

// Sprawdź czy mamy jakikolwiek identyfikator
$hasId = !empty($record['gclid']) || !empty($record['wbraid']) || !empty($record['gbraid']);
if (!$hasId) {
    echo json_encode(['status' => 'ok', 'reason' => 'no tracking id', 'number' => $zadarmaNumber]);
    exit;
}

if ($record['conversion_sent']) {
    echo json_encode(['status' => 'ok', 'reason' => 'already sent']);
    exit;
}

$isLong = $duration >= $CONFIG['min_duration_long'];
$conversionAction = $isLong ? $CONFIG['conversion_long'] : $CONFIG['conversion_short'];
$conversionValue = $isLong ? $CONFIG['value_long'] : $CONFIG['value_short'];
$conversionType = $isLong ? 'long' : 'short';

$added = ConversionQueue::add(
    (int)$record['id'],
    $record['gclid'] ?? null,
    $record['wbraid'] ?? null,
    $record['gbraid'] ?? null,
    $conversionAction,
    $conversionValue,
    $callStart,
    $duration
);

if ($added) {
    $idType = $record['gclid'] ? 'gclid' : ($record['wbraid'] ? 'wbraid' : 'gbraid');
    echo json_encode([
        'status' => 'queued',
        'id_type' => $idType,
        'type' => $conversionType,
        'value' => $conversionValue
    ]);
} else {
    echo json_encode(['status' => 'error', 'reason' => 'queue failed']);
}

function logWebhook(array $data): void
{
    $logFile = dirname(__FILE__) . '/logs/webhook_' . date('Y-m-d') . '.log';
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($logFile, date('H:i:s') . ' ' . json_encode($data) . "\n", FILE_APPEND);
}
