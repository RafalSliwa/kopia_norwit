<?php

require_once 'config/config.inc.php';
require_once 'init.php';

try {
    $db = Db::getInstance();
    $id_product = 2681;
    
    echo "=== BEZPOŚREDNIE ZAPYTANIA SQL DO BAZY ===" . PHP_EOL;
    echo "Produkt ID: {$id_product}" . PHP_EOL;
    echo "Baza danych: " . _DB_NAME_ . PHP_EOL;
    echo "Prefiks tabel: " . _DB_PREFIX_ . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    
    // 1. Tabela ps_product - podstawowe dane
    echo PHP_EOL . "1. TABELA ps_product:" . PHP_EOL;
    $query1 = "SELECT * FROM " . _DB_PREFIX_ . "product WHERE id_product = {$id_product}";
    echo "SQL: {$query1}" . PHP_EOL;
    $product = $db->getRow($query1);
    
    if ($product) {
        foreach ($product as $key => $value) {
            echo "  {$key}: {$value}" . PHP_EOL;
        }
    } else {
        echo "  Produkt nie znaleziony!" . PHP_EOL;
    }
    
    // 2. Tabela ps_product_lang - nazwy w różnych językach
    echo PHP_EOL . "2. TABELA ps_product_lang:" . PHP_EOL;
    $query2 = "SELECT * FROM " . _DB_PREFIX_ . "product_lang WHERE id_product = {$id_product}";
    echo "SQL: {$query2}" . PHP_EOL;
    $productLang = $db->executeS($query2);
    
    foreach ($productLang as $lang) {
        echo "  Język {$lang['id_lang']}: {$lang['name']}" . PHP_EOL;
        echo "    link_rewrite: {$lang['link_rewrite']}" . PHP_EOL;
        if (!empty($lang['description_short'])) {
            echo "    opis_krótki: " . substr($lang['description_short'], 0, 100) . "..." . PHP_EOL;
        }
    }
    
    // 3. Tabela ps_product_carrier - przypisani przewoźnicy
    echo PHP_EOL . "3. TABELA ps_product_carrier:" . PHP_EOL;
    $query3 = "SELECT * FROM " . _DB_PREFIX_ . "product_carrier WHERE id_product = {$id_product}";
    echo "SQL: {$query3}" . PHP_EOL;
    $productCarriers = $db->executeS($query3);
    
    if ($productCarriers) {
        foreach ($productCarriers as $pc) {
            echo "  id_carrier_reference: {$pc['id_carrier_reference']}" . PHP_EOL;
        }
    } else {
        echo "  Brak przypisanych przewoźników (używa domyślnych)" . PHP_EOL;
    }
    
    // 4. Tabela ps_carrier - szczegóły przewoźników
    echo PHP_EOL . "4. TABELA ps_carrier (wszystkie aktywne):" . PHP_EOL;
    $query4 = "SELECT * FROM " . _DB_PREFIX_ . "carrier WHERE active = 1 AND deleted = 0 ORDER BY position";
    echo "SQL: {$query4}" . PHP_EOL;
    $carriers = $db->executeS($query4);
    
    foreach ($carriers as $carrier) {
        echo "  ID: {$carrier['id_carrier']}, Ref: {$carrier['id_reference']}, Nazwa: {$carrier['name']}" . PHP_EOL;
        echo "    external_module_name: {$carrier['external_module_name']}" . PHP_EOL;
        echo "    shipping_handling: {$carrier['shipping_handling']}, is_free: {$carrier['is_free']}" . PHP_EOL;
        echo "    max_weight: {$carrier['max_weight']}, max_width: {$carrier['max_width']}" . PHP_EOL;
    }
    
    // 5. Tabela ps_delivery - ceny dostaw
    echo PHP_EOL . "5. TABELA ps_delivery (strefa 1 - Polska):" . PHP_EOL;
    $query5 = "SELECT d.*, c.name as carrier_name FROM " . _DB_PREFIX_ . "delivery d 
               INNER JOIN " . _DB_PREFIX_ . "carrier c ON d.id_carrier = c.id_carrier 
               WHERE d.id_zone = 1 AND c.active = 1 AND c.deleted = 0 
               ORDER BY c.name, d.price";
    echo "SQL: {$query5}" . PHP_EOL;
    $deliveries = $db->executeS($query5);
    
    foreach ($deliveries as $delivery) {
        echo "  {$delivery['carrier_name']} (ID: {$delivery['id_carrier']}): {$delivery['price']} zł" . PHP_EOL;
        echo "    id_range_weight: {$delivery['id_range_weight']}, id_range_price: {$delivery['id_range_price']}" . PHP_EOL;
        echo "    id_zone: {$delivery['id_zone']}" . PHP_EOL;
    }
    
    // 6. Tabela ps_range_price - zakresy cenowe
    echo PHP_EOL . "6. TABELA ps_range_price:" . PHP_EOL;
    $query6 = "SELECT * FROM " . _DB_PREFIX_ . "range_price ORDER BY delimiter1";
    echo "SQL: {$query6}" . PHP_EOL;
    $rangePrices = $db->executeS($query6);
    
    foreach ($rangePrices as $range) {
        echo "  ID: {$range['id_range_price']}, Zakres: {$range['delimiter1']} - {$range['delimiter2']}" . PHP_EOL;
    }
    
    // 7. Tabela ps_range_weight - zakresy wagowe
    echo PHP_EOL . "7. TABELA ps_range_weight:" . PHP_EOL;
    $query7 = "SELECT * FROM " . _DB_PREFIX_ . "range_weight ORDER BY delimiter1";
    echo "SQL: {$query7}" . PHP_EOL;
    $rangeWeights = $db->executeS($query7);
    
    foreach ($rangeWeights as $range) {
        echo "  ID: {$range['id_range_weight']}, Zakres: {$range['delimiter1']} - {$range['delimiter2']}" . PHP_EOL;
    }
    
    // 8. Stock Available
    echo PHP_EOL . "8. TABELA ps_stock_available:" . PHP_EOL;
    $query8 = "SELECT * FROM " . _DB_PREFIX_ . "stock_available WHERE id_product = {$id_product}";
    echo "SQL: {$query8}" . PHP_EOL;
    $stock = $db->executeS($query8);
    
    foreach ($stock as $s) {
        echo "  id_product_attribute: {$s['id_product_attribute']}, quantity: {$s['quantity']}" . PHP_EOL;
        echo "    depends_on_stock: {$s['depends_on_stock']}, out_of_stock: {$s['out_of_stock']}" . PHP_EOL;
    }
    
    // 9. Kategorie produktu
    echo PHP_EOL . "9. TABELA ps_category_product:" . PHP_EOL;
    $query9 = "SELECT cp.*, cl.name as category_name FROM " . _DB_PREFIX_ . "category_product cp 
               LEFT JOIN " . _DB_PREFIX_ . "category_lang cl ON cp.id_category = cl.id_category AND cl.id_lang = 1
               WHERE cp.id_product = {$id_product}";
    echo "SQL: {$query9}" . PHP_EOL;
    $categories = $db->executeS($query9);
    
    foreach ($categories as $cat) {
        echo "  Kategoria ID: {$cat['id_category']}, Nazwa: {$cat['category_name']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "BŁĄD: " . $e->getMessage() . PHP_EOL;
    echo "Plik: " . $e->getFile() . PHP_EOL;
    echo "Linia: " . $e->getLine() . PHP_EOL;
}