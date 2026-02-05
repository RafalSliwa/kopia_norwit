<?php

require_once 'config/config.inc.php';
require_once 'init.php';

try {
    $db = Db::getInstance();
    $id_product = 2681;
    
    echo "=== SZCZEGÓŁOWE INFORMACJE O PRODUKCIE ID {$id_product} ===" . PHP_EOL;
    
    // 1. Podstawowe informacje o produkcie
    $productQuery = "
        SELECT p.id_product, pl.name, p.weight, p.price, p.active, p.quantity
        FROM " . _DB_PREFIX_ . "product p
        LEFT JOIN " . _DB_PREFIX_ . "product_lang pl ON p.id_product = pl.id_product AND pl.id_lang = 1
        WHERE p.id_product = {$id_product}
    ";
    
    $product = $db->getRow($productQuery);
    if ($product) {
        echo "Nazwa: " . $product['name'] . PHP_EOL;
        echo "Waga: " . $product['weight'] . " kg" . PHP_EOL;
        echo "Cena: " . $product['price'] . " zł" . PHP_EOL;
        echo "Aktywny: " . ($product['active'] ? 'TAK' : 'NIE') . PHP_EOL;
        echo "Ilość: " . $product['quantity'] . PHP_EOL;
    } else {
        echo "Produkt nie znaleziony!" . PHP_EOL;
        exit;
    }
    
    // 2. Przypisani przewoźnicy do produktu
    echo PHP_EOL . "=== PRZEWOŹNICY PRZYPISANI DO PRODUKTU ===" . PHP_EOL;
    $carriersQuery = "
        SELECT c.id_carrier, c.name, c.id_reference, pc.id_product
        FROM " . _DB_PREFIX_ . "product_carrier pc
        INNER JOIN " . _DB_PREFIX_ . "carrier c ON pc.id_carrier_reference = c.id_reference
        WHERE pc.id_product = {$id_product} AND c.active = 1 AND c.deleted = 0
        ORDER BY c.name
    ";
    
    $carriers = $db->executeS($carriersQuery);
    if ($carriers) {
        foreach ($carriers as $carrier) {
            echo "- ID: {$carrier['id_carrier']}, Nazwa: {$carrier['name']}, Reference: {$carrier['id_reference']}" . PHP_EOL;
        }
    } else {
        echo "Brak przypisanych przewoźników - używa wszystkich dostępnych" . PHP_EOL;
    }
    
    // 3. Dostępne opcje dostawy dla tego produktu
    echo PHP_EOL . "=== OPCJE DOSTAWY DLA TEGO PRODUKTU ===" . PHP_EOL;
    $deliveryQuery = "
        SELECT 
            c.id_carrier, c.name, d.price, d.id_zone,
            COALESCE(rw.delimiter1, rp.delimiter1) as range_from,
            COALESCE(rw.delimiter2, rp.delimiter2) as range_to,
            CASE
                WHEN rw.id_range_weight IS NOT NULL THEN 'weight'
                WHEN rp.id_range_price IS NOT NULL THEN 'price'
                ELSE 'unknown'
            END as range_type
        FROM " . _DB_PREFIX_ . "delivery d
        INNER JOIN " . _DB_PREFIX_ . "carrier c ON d.id_carrier = c.id_carrier
        LEFT JOIN " . _DB_PREFIX_ . "range_weight rw ON rw.id_range_weight = d.id_range_weight
        LEFT JOIN " . _DB_PREFIX_ . "range_price rp ON rp.id_range_price = d.id_range_price
        WHERE c.active = 1 AND c.deleted = 0 AND d.id_zone = 1
        AND (
            (rw.id_range_weight IS NOT NULL AND {$product['weight']} >= rw.delimiter1 AND {$product['weight']} < rw.delimiter2) OR
            (rp.id_range_price IS NOT NULL AND {$product['price']} >= rp.delimiter1 AND {$product['price']} < rp.delimiter2) OR
            (rw.id_range_weight IS NULL AND rp.id_range_price IS NULL)
        )
        ORDER BY d.price ASC
    ";
    
    $deliveryOptions = $db->executeS($deliveryQuery);
    if ($deliveryOptions) {
        foreach ($deliveryOptions as $option) {
            echo "- {$option['name']} (ID: {$option['id_carrier']}): {$option['price']} zł";
            echo " | Zakres {$option['range_type']}: {$option['range_from']} - {$option['range_to']}" . PHP_EOL;
        }
    } else {
        echo "Brak opcji dostawy dla tego produktu" . PHP_EOL;
    }
    
    // 4. Wszystkie dostępne przewoźnicy (dla porównania)
    echo PHP_EOL . "=== WSZYSCY AKTYWNI PRZEWOŹNICY ===" . PHP_EOL;
    $allCarriersQuery = "
        SELECT c.id_carrier, c.name, c.id_reference
        FROM " . _DB_PREFIX_ . "carrier c
        WHERE c.active = 1 AND c.deleted = 0
        ORDER BY c.name
    ";
    
    $allCarriers = $db->executeS($allCarriersQuery);
    foreach ($allCarriers as $carrier) {
        echo "- ID: {$carrier['id_carrier']}, Nazwa: {$carrier['name']}" . PHP_EOL;
    }
    
    // 5. Progi darmowej dostawy
    echo PHP_EOL . "=== PROGI DARMOWEJ DOSTAWY ===" . PHP_EOL;
    $freeShippingQuery = "
        SELECT 
            c.name, 
            COALESCE(rw.delimiter1, rp.delimiter1) as free_threshold,
            CASE
                WHEN rw.id_range_weight IS NOT NULL THEN 'weight'
                WHEN rp.id_range_price IS NOT NULL THEN 'price'
                ELSE 'unknown'
            END as range_type
        FROM " . _DB_PREFIX_ . "delivery d
        INNER JOIN " . _DB_PREFIX_ . "carrier c ON d.id_carrier = c.id_carrier
        LEFT JOIN " . _DB_PREFIX_ . "range_weight rw ON rw.id_range_weight = d.id_range_weight
        LEFT JOIN " . _DB_PREFIX_ . "range_price rp ON rp.id_range_price = d.id_range_price
        WHERE c.active = 1 AND c.deleted = 0 AND d.id_zone = 1 AND d.price = 0
        AND (rw.delimiter1 > 0 OR rp.delimiter1 > 0)
        ORDER BY COALESCE(rw.delimiter1, rp.delimiter1) ASC
    ";
    
    $freeShipping = $db->executeS($freeShippingQuery);
    if ($freeShipping) {
        foreach ($freeShipping as $free) {
            echo "- {$free['name']}: {$free['free_threshold']} ({$free['range_type']})" . PHP_EOL;
        }
    } else {
        echo "Brak progów darmowej dostawy w systemie przewoźników" . PHP_EOL;
        
        // Sprawdź ustawienie PrestaShop
        $psFreeSetting = Configuration::get('PS_SHIPPING_FREE_PRICE');
        echo "Ustawienie PrestaShop PS_SHIPPING_FREE_PRICE: " . ($psFreeSetting ?: 'nie ustawione') . PHP_EOL;
    }
    
    echo PHP_EOL . "=== PODSUMOWANIE ===" . PHP_EOL;
    
    // Użyj modułu do obliczenia ostatecznego kosztu
    require_once 'modules/relatedproducts/relatedproducts.php';
    $module = new RelatedProducts();
    $shippingInfo = $module->getLowestShippingPrice($id_product);
    
    echo "Najniższy koszt dostawy NETTO: " . $shippingInfo['price_without_vat'] . " zł" . PHP_EOL;
    echo "Najniższy koszt dostawy BRUTTO: " . $shippingInfo['price'] . " zł" . PHP_EOL;
    echo "Przewoźnik: " . $shippingInfo['carrier']['name'] . PHP_EOL;
    echo "Próg darmowej dostawy: " . $module->getFreeShippingThreshold($id_product) . " zł" . PHP_EOL;
    
} catch (Exception $e) {
    echo "BŁĄD: " . $e->getMessage() . PHP_EOL;
    echo "Plik: " . $e->getFile() . PHP_EOL;
    echo "Linia: " . $e->getLine() . PHP_EOL;
}