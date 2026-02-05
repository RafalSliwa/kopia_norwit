<?php

require_once 'config/config.inc.php';
require_once 'init.php';
require_once 'modules/relatedproducts/relatedproducts.php';

try {
    $module = new RelatedProducts();
    $shippingInfo = $module->getLowestShippingPrice(2681);
    
    echo "=== KOSZT WYSYŁKI DLA PRODUKTU ID 2681 ===" . PHP_EOL;
    echo "Cena NETTO: " . $shippingInfo['price_without_vat'] . " zł" . PHP_EOL;
    echo "Cena BRUTTO: " . $shippingInfo['price'] . " zł" . PHP_EOL;
    echo "Sformatowana cena: " . $shippingInfo['formatted_price'] . PHP_EOL;
    echo "Przewoźnik: " . $shippingInfo['carrier']['name'] . PHP_EOL;
    echo "Waga produktu: " . $shippingInfo['product_weight'] . " kg" . PHP_EOL;
    echo "VAT zastosowany: " . ($shippingInfo['vat_applied'] ? 'TAK' : 'NIE') . PHP_EOL;
    
    if (isset($shippingInfo['debug_carriers']) && !empty($shippingInfo['debug_carriers'])) {
        echo PHP_EOL . "=== DOSTĘPNI PRZEWOŹNICY ===" . PHP_EOL;
        foreach ($shippingInfo['debug_carriers'] as $carrier) {
            if (isset($carrier['skipped_reason'])) {
                echo "- " . $carrier['carrier_name'] . " (POMINIĘTY: " . $carrier['skipped_reason'] . ")" . PHP_EOL;
            } else {
                echo "- " . $carrier['carrier_name'] . ": " . ($carrier['price'] ?? 'brak ceny') . " zł";
                if (isset($carrier['product_weight'])) {
                    echo " (waga: " . $carrier['product_weight'] . " kg)";
                }
                echo PHP_EOL;
            }
        }
    }
    
    // Sprawdź też próg darmowej dostawy
    $threshold = $module->getFreeShippingThreshold(2681);
    echo PHP_EOL . "=== PRÓG DARMOWEJ DOSTAWY ===" . PHP_EOL;
    echo "Próg dla tego produktu: " . $threshold . " zł" . PHP_EOL;
    
} catch (Exception $e) {
    echo "BŁĄD: " . $e->getMessage() . PHP_EOL;
    echo "Plik: " . $e->getFile() . PHP_EOL;
    echo "Linia: " . $e->getLine() . PHP_EOL;
}