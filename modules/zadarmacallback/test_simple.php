<?php

require_once __DIR__ . '/vendor/autoload.php';
use Zadarma_API\Api;

// Zwiększ limit czasu
ini_set('max_execution_time', 0);
set_time_limit(0);

$apiKey    = 'defe01401c38ec874637';
$apiSecret = 'b63a971665601056e0b4';
$api = new Api($apiKey, $apiSecret);

echo '<h2>🧪 Simple Callback Test</h2>';

// TEST 1: Bez prefiksu
echo '<h3>Test 1: Bez prefiksu</h3>';
$from = '48573568477';
$to = '48535484671';

echo "<p><b>FROM:</b> $from | <b>TO:</b> $to</p>";

try {
    $response = $api->requestCallback($from, $to);
    echo "<div style='color:green;'><b>✅ SUCCESS:</b></div>";
    if (is_object($response)) {
        echo "FROM: " . $response->from . "<br>";
        echo "TO: " . $response->to . "<br>";
        echo "TIME: " . $response->time . " (" . date('Y-m-d H:i:s', $response->time) . ")<br>";
    } else {
        echo "<pre>" . print_r($response, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<div style='color:red;'><b>❌ ERROR:</b> " . $e->getMessage() . "</div>";
}

echo "<hr>";

// TEST 2: Z prefiksem +
echo '<h3>Test 2: Z prefiksem +</h3>';
$from = '+48573568477';
$to = '+48535484671';

echo "<p><b>FROM:</b> $from | <b>TO:</b> $to</p>";

try {
    $response = $api->requestCallback($from, $to);
    echo "<div style='color:green;'><b>✅ SUCCESS:</b></div>";
    if (is_object($response)) {
        echo "FROM: " . $response->from . "<br>";
        echo "TO: " . $response->to . "<br>";
        echo "TIME: " . $response->time . " (" . date('Y-m-d H:i:s', $response->time) . ")<br>";
    } else {
        echo "<pre>" . print_r($response, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<div style='color:red;'><b>❌ ERROR:</b> " . $e->getMessage() . "</div>";
}

echo "<hr>";

// SPRAWDŹ SALDO
echo '<h3>💰 Saldo</h3>';
try {
    $balance_response = $api->call('/v1/info/balance');
    if (is_string($balance_response)) {
        $balance = json_decode($balance_response, true);
    } else {
        $balance = $balance_response;
    }
    
    if (isset($balance['balance'])) {
        echo "<p><b>Saldo:</b> " . $balance['balance'] . " " . $balance['currency'] . "</p>";
    } else {
        echo "<pre>" . print_r($balance_response, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<div style='color:red;'>❌ Błąd salda: " . $e->getMessage() . "</div>";
}

echo '<hr>';
echo '<p><b>🎯 Który format zadziałał?</b></p>';
echo '<p><b>📞 Czy telefon dzwonił?</b></p>';