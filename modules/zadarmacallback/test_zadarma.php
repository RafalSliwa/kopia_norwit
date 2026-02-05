<?php

require_once __DIR__ . '/vendor/autoload.php';

use Zadarma_API\Api;

// 🔐 Dane API
$apiKey    = 'defe01401c38ec874637';
$apiSecret = 'b63a971665601056e0b4';

$api = new Api($apiKey, $apiSecret);

// Stylizacja HTML
echo '<style>body{font-family:Arial,sans-serif;font-size:14px;}pre{white-space:pre-wrap;background:#f8f8f8;padding:10px;border-radius:5px;border:1px solid #ddd;}.success{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}.debug{background:#e3f2fd;padding:10px;margin:10px 0;border-left:4px solid #2196f3;}.test{background:#f0f8ff;padding:15px;margin:15px 0;border:2px solid #4CAF50;border-radius:8px;}</style>';

echo '<h2>🧪 Test Zadarma API - Callback z Prefiksami</h2>';

// ===============================
// 🔐 STAŁY NUMER FROM + TO
// ===============================
$from_base = '48573568477'; // ← TWÓJ STAŁY NUMER  
$to_number = '0048788954495'; // ← TWÓJ NUMER TESTOWY

// ===============================
// 📞 TESTY RÓŻNYCH FORMATÓW
// ===============================
$test_formats = [
    '1. Bez prefiksu'     => $from_base,
    '2. Z prefiksem +'    => '+' . $from_base,
    '3. Z prefiksem 00'   => '00' . $from_base,
    '4. Tylko lokalne'    => substr($from_base, 2), // usuń 48
];

echo '<div class="debug">';
echo '<h3>🎯 Stały numer FROM: ' . $from_base . '</h3>';
echo '<h3>🎯 Stały numer TO: ' . $to_number . '</h3>';
echo '<b>📋 Testowane formaty:</b><br>';
foreach ($test_formats as $label => $format) {
    echo "- $label: <code>$format</code><br>";
}
echo '</div>';

// ===============================
// 🔍 SPRAWDZENIE STATUSU KONTA
// ===============================
echo "<h3>💰 Status konta:</h3>";
try {
    $balance = $api->call('/v1/info/balance');
    echo "<div class='debug'>";
    if (is_array($balance) && isset($balance['balance'])) {
        echo "<b>Saldo:</b> " . $balance['balance'] . " " . $balance['currency'] . "<br>";
        if ($balance['balance'] < 5) {
            echo "<div class='error'>⚠️ UWAGA: Niskie saldo - może nie wystarczyć na testy!</div>";
        }
    } else {
        echo "<b>Saldo response:</b><pre>" . print_r($balance, true) . "</pre>";
    }
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Błąd sprawdzania salda: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// ===============================
// 🧪 TESTY CALLBACK Z RÓŻNYMI FORMATAMI
// ===============================

foreach ($test_formats as $test_label => $from_format) {
    echo "<div class='test'>";
    echo "<h3>🧪 $test_label</h3>";
    
    echo "<div class='debug'>";
    echo "<b>FROM:</b> <code>$from_format</code><br>";
    echo "<b>TO:</b> <code>$to_number</code><br>"; // ← NAPRAWIONE
    echo "<b>⏰ Czas testu:</b> " . date('Y-m-d H:i:s') . "<br>";
    echo "</div>";
    
    try {
        // 📞 WYKONAJ CALLBACK Z PRAWDZIWYM NUMEREM TO
        $response = $api->requestCallback($from_format, $to_number); // ← NAPRAWIONE
        
        echo "<h4>📡 Odpowiedź API:</h4>";
        echo "<div class='debug'><pre>";
        var_dump($response);
        echo "</pre></div>";
        
        // 🔍 ANALIZA ODPOWIEDZI
        if (is_object($response)) {
            $is_success = !empty($response->from) && !empty($response->to);
            
            if ($is_success) {
                echo "<div class='success'>";
                echo "✅ $test_label - SUCCESS!<br>";
                echo "📞 FROM: {$response->from}<br>";
                echo "📱 TO: {$response->to}<br>";
                echo "🕒 TIME: {$response->time} (" . date('Y-m-d H:i:s', $response->time) . ")<br>";
                echo "</div>";
                
                echo "<div style='background:#e8f5e8;padding:10px;margin:10px 0;border-radius:5px;'>";
                echo "🔔 <b>UWAGA:</b> Zadarma powinna dzwonić TERAZ!<br>";
                echo "📞 Najpierw na: <b>{$response->from}</b><br>";
                echo "📱 Potem na numer ustawiony w Zadarma<br>";
                echo "</div>";
                
                // ⏸️ PAUZA MIĘDZY TESTAMI
                echo "<p>⏸️ Czekam 10 sekund przed następnym testem...</p>";
                sleep(10);
                
            } else {
                echo "<div class='error'>";
                echo "❌ $test_label - FAILED<br>";
                echo "Odpowiedź nie zawiera prawidłowych danych FROM/TO<br>";
                echo "</div>";
            }
        } else {
            echo "<div class='error'>";
            echo "❌ $test_label - INVALID RESPONSE<br>";
            echo "API nie zwróciło obiektu odpowiedzi<br>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<b>❌ $test_label - ERROR:</b><br>";
        echo "<b>Message:</b> " . $e->getMessage() . "<br>";
        echo "<b>Code:</b> " . $e->getCode() . "<br>";
        echo "</div>";
    }
    
    echo "</div>"; // zamknij .test
    echo "<hr>";
}

// ===============================
// 📊 SPRAWDZENIE STATYSTYK
// ===============================

echo "<h3>📊 Sprawdzenie ostatnich połączeń:</h3>";
try {
    $stats = $api->call('/v1/statistics', [
        'start' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
        'end' => date('Y-m-d H:i:s')
    ]);
    
    if (!empty($stats['stats'])) {
        echo "<div class='debug'>";
        echo "<b>🔍 Ostatnie połączenia (ostatnie 10 minut):</b><br><br>";
        foreach (array_slice($stats['stats'], -10) as $call) {
            echo "<b>" . $call['callstart'] . "</b> - ";
            echo "FROM: <code>" . $call['from'] . "</code> → TO: <code>" . $call['to'] . "</code> ";
            echo "(<span style='color:" . ($call['disposition'] == 'answered' ? 'green' : 'red') . "'>" . $call['disposition'] . "</span>)<br>";
        }
        echo "</div>";
    } else {
        echo "<div class='debug'>Brak połączeń w ostatnich 10 minutach</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Błąd statystyk: " . $e->getMessage() . "</div>";
}

echo "<hr>";

// ===============================
// 📋 PODSUMOWANIE
// ===============================

echo '<div style="background:#fff3cd;padding:20px;border:2px solid #ffeaa7;border-radius:8px;margin:20px 0;">';
echo '<h3>📋 Instrukcje:</h3>';
echo '<ol>';
echo '<li><b>Test uruchomi 4 różne formaty</b> numeru FROM</li>';
echo '<li><b>Każdy test</b> wykona prawdziwy callback</li>';
echo '<li><b>Pauza 10 sekund</b> między testami</li>';
echo '<li><b>Sprawdź telefon</b> - powinien dzwonić na ' . $from_base . '</li>';
echo '<li><b>Po odebraniu</b> - zostaniesz połączony z numerem z Zadarma</li>';
echo '</ol>';
echo '<p><b>🎯 Cel:</b> Sprawdzić który format działa najlepiej</p>';
echo '<p><b>📊 Wyniki:</b> Sprawdź statystyki na końcu</p>';
echo '</div>';

echo '<div style="background:#ffe6e6;padding:15px;border:2px solid #ff9999;border-radius:8px;margin:20px 0;">';
echo '<h4>⚠️ UWAGA:</h4>';
echo '<p><b>Ten test wykona 4 prawdziwe callback!</b></p>';
echo '<p><b>Łączny koszt:</b> ~2-3 PLN</p>';
echo '<p><b>Czas trwania:</b> ~2 minuty</p>';
echo '</div>';
