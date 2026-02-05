# ARCHITECTURE – Moduł customcarrier dla PrestaShop 8.x

## 1. Informacje Ogólne

**Nazwa modułu:** customcarrier
**Wersja PrestaShop:** 8.x (8.0, 8.1, 8.2)
**Wersja PHP:** 8.1+
**Architektura:** Symfony Components (Doctrine, Form, Twig)

---

## 2. Struktura Katalogów

```
customcarrier/
├── config/
│   ├── routes.yml                 # Routing Symfony dla backoffice
│   └── services.yml               # Dependency Injection
├── controllers/
│   └── admin/                     # Kontrolery administracyjne (legacy hooks)
├── src/
│   ├── Controller/
│   │   └── Admin/                 # Kontrolery Symfony backoffice
│   │       ├── ConfigurationController.php
│   │       ├── ProductSettingsController.php
│   │       ├── ReportsController.php
│   │       └── ImportExportController.php
│   ├── Entity/
│   │   ├── CustomCarrierProduct.php      # Ustawienia transportowe produktu
│   │   ├── CustomCarrierCombination.php  # Ustawienia dla kombinacji
│   │   ├── CustomCarrierZone.php         # Progi kwotowe per strefa
│   │   └── CustomCarrierLog.php          # Entity do raportów (opcjonalnie)
│   ├── Repository/
│   │   ├── CustomCarrierProductRepository.php
│   │   ├── CustomCarrierCombinationRepository.php
│   │   └── CustomCarrierZoneRepository.php
│   ├── Form/
│   │   ├── ConfigurationType.php         # Formularz ustawień globalnych
│   │   ├── ProductTransportType.php      # Formularz pól produktu
│   │   └── ZoneThresholdType.php         # Formularz progów per strefa
│   ├── Service/
│   │   ├── ShippingCalculator.php        # Główna logika obliczeń
│   │   ├── CartAnalyzer.php              # Analiza koszyka (grupowanie)
│   │   ├── ThresholdChecker.php          # Sprawdzanie progów
│   │   ├── CacheManager.php              # Zarządzanie cache
│   │   ├── ImportService.php             # Import CSV/XML
│   │   ├── ExportService.php             # Export CSV/XML
│   │   └── LoggerService.php             # Logowanie do plików
│   ├── EventSubscriber/
│   │   └── ProductFormSubscriber.php     # Hook do formularza produktu
│   └── Exception/
│       └── ShippingCalculationException.php
├── views/
│   ├── templates/
│   │   └── admin/
│   │       ├── configuration.html.twig   # Panel konfiguracji
│   │       ├── product_tab.html.twig     # Zakładka Transport na produkcie
│   │       ├── reports.html.twig         # Dashboard raportów
│   │       └── import_export.html.twig   # Panel import/export
│   └── js/
│       └── admin/
│           ├── product-transport.js      # JS dla zakładki produktu
│           └── configuration.js          # JS dla panelu konfiguracji
├── translations/
│   ├── pl.yml
│   └── en.yml
├── upgrade/
│   └── upgrade-x.x.x.php                 # Skrypty aktualizacji
├── sql/
│   ├── install.sql
│   └── uninstall.sql
├── var/
│   └── logs/                             # Logi obliczeń (gitignore)
├── customcarrier.php                     # Główny plik modułu
├── logo.png
└── composer.json
```

---

## 3. Schemat Bazy Danych

### 3.1 Tabela: ps_customcarrier_product

Przechowuje ustawienia transportowe dla produktów.

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_customcarrier_product` (
    `id_customcarrier_product` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_product` INT(11) UNSIGNED NOT NULL,
    `free_shipping` TINYINT(1) NOT NULL DEFAULT 0,
    `base_shipping_cost` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
    `multiply_by_quantity` TINYINT(1) NOT NULL DEFAULT 0,
    `free_shipping_quantity` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `apply_threshold` TINYINT(1) NOT NULL DEFAULT 0,
    `separate_package` TINYINT(1) NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_customcarrier_product`),
    UNIQUE KEY `id_product` (`id_product`),
    KEY `idx_free_shipping` (`free_shipping`),
    KEY `idx_separate_package` (`separate_package`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Tabela: ps_customcarrier_combination

Nadpisania ustawień dla kombinacji produktów.

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_customcarrier_combination` (
    `id_customcarrier_combination` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_product_attribute` INT(11) UNSIGNED NOT NULL,
    `id_product` INT(11) UNSIGNED NOT NULL,
    `override_settings` TINYINT(1) NOT NULL DEFAULT 0,
    `free_shipping` TINYINT(1) DEFAULT NULL,
    `base_shipping_cost` DECIMAL(20,6) DEFAULT NULL,
    `multiply_by_quantity` TINYINT(1) DEFAULT NULL,
    `free_shipping_quantity` INT(11) UNSIGNED DEFAULT NULL,
    `apply_threshold` TINYINT(1) DEFAULT NULL,
    `separate_package` TINYINT(1) DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_customcarrier_combination`),
    UNIQUE KEY `id_product_attribute` (`id_product_attribute`),
    KEY `id_product` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.3 Tabela: ps_customcarrier_zone

Progi kwotowe dla stref dostawy.

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_customcarrier_zone` (
    `id_customcarrier_zone` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_zone` INT(11) UNSIGNED NOT NULL,
    `threshold_amount` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_customcarrier_zone`),
    UNIQUE KEY `id_zone` (`id_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. Hooki PrestaShop

### 4.1 Hooki Instalacyjne
| Hook | Opis |
|------|------|
| `actionModuleInstallAfter` | Tworzenie tabel, rejestracja kuriera |
| `actionModuleUninstallBefore` | Usuwanie danych, wyrejestrowanie kuriera |

### 4.2 Hooki Backoffice
| Hook | Opis |
|------|------|
| `displayAdminProductsShippingStepBottom` | Zakładka Transport na karcie produktu |
| `actionProductFormBuilderModifier` | Modyfikacja formularza produktu (Symfony) |
| `actionAfterUpdateProductFormHandler` | Zapis ustawień transportowych |
| `actionAfterCreateProductFormHandler` | Zapis przy tworzeniu produktu |
| `displayBackOfficeHeader` | Ładowanie CSS/JS w backoffice |

### 4.3 Hooki Frontend (Koszyk)
| Hook | Opis |
|------|------|
| `actionCarrierProcess` | Główne obliczenie kosztu wysyłki |
| `displayCarrierExtraContent` | Dodatkowe info o wysyłce (opcjonalne) |

### 4.4 Hooki Carrier
| Hook | Opis |
|------|------|
| `actionValidateOrder` | Logowanie końcowego kosztu wysyłki |
| `actionOrderStatusPostUpdate` | Aktualizacja statystyk |

---

## 5. Serwisy (Dependency Injection)

### 5.1 ShippingCalculator

Główny serwis obliczający koszt wysyłki.

```php
namespace CustomCarrier\Service;

class ShippingCalculator
{
    public function calculateForCart(Cart $cart, int $idZone): float;
    public function calculateForProduct(int $idProduct, int $quantity, int $idZone): float;
    public function getBreakdown(Cart $cart, int $idZone): array;
}
```

### 5.2 CartAnalyzer

Analiza i grupowanie produktów w koszyku.

```php
namespace CustomCarrier\Service;

class CartAnalyzer
{
    public function groupProducts(Cart $cart): array;
    public function getSeparatePackageProducts(Cart $cart): array;
    public function getSharedShippingProducts(Cart $cart): array;
}
```

### 5.3 ThresholdChecker

Sprawdzanie progów darmowej wysyłki.

```php
namespace CustomCarrier\Service;

class ThresholdChecker
{
    public function checkQuantityThreshold(int $idProduct, int $quantity): bool;
    public function checkAmountThreshold(Cart $cart, int $idZone): bool;
    public function getApplicableThreshold(int $idZone): ?float;
}
```

---

## 6. Algorytm Obliczania Kosztu Wysyłki

```
1. POBIERZ produkty z koszyka
2. DLA KAŻDEGO produktu:
   a. POBIERZ ustawienia transportowe (produkt lub kombinacja)
   b. SPRAWDŹ darmową wysyłkę bezwarunkową → jeśli TAK → koszt = 0
   c. SPRAWDŹ próg ilościowy → jeśli spełniony → koszt = 0
   d. SPRAWDŹ próg kwotowy (jeśli apply_threshold = true) → jeśli spełniony → koszt = 0
   e. OBLICZ koszt bazowy:
      - jeśli multiply_by_quantity → koszt = base_cost × quantity
      - jeśli nie → koszt = base_cost
   f. DODAJ do grupy (separate_package lub shared)

3. SUMUJ koszty wszystkich grup
4. ZAPISZ do cache (klucz: id_cart + id_zone + hash produktów)
5. ZWRÓĆ sumę końcową
```

---

## 7. Cache

### 7.1 Strategia Cache

- **Klucz:** `customcarrier_{id_cart}_{id_zone}_{products_hash}`
- **TTL:** 300 sekund (5 minut)
- **Invalidacja:** przy zmianie koszyka, zmianie ustawień produktu

### 7.2 Implementacja

Wykorzystanie natywnego systemu cache PrestaShop (Symfony Cache Component).

```php
namespace CustomCarrier\Service;

class CacheManager
{
    public function get(string $key): ?float;
    public function set(string $key, float $value, int $ttl = 300): void;
    public function invalidateCart(int $idCart): void;
    public function invalidateProduct(int $idProduct): void;
}
```

---

## 8. Logowanie

### 8.1 Lokalizacja Logów

```
var/logs/customcarrier/
├── calculations_YYYY-MM-DD.log    # Logi obliczeń
├── errors_YYYY-MM-DD.log          # Błędy
└── import_YYYY-MM-DD.log          # Logi importu
```

### 8.2 Format Logu

```
[2024-01-26 10:30:45] CALC id_cart=123 id_zone=1 products=[{id:45,qty:2},{id:67,qty:1}] result=85.00 rules_applied=[quantity_threshold:45] duration_ms=12
```

### 8.3 Retencja

- Domyślnie: 30 dni
- Konfigurowalne w ustawieniach modułu
- Automatyczne czyszczenie przez CRON lub przy zapisie

---

## 9. Import/Export

### 9.1 Format CSV

```csv
id_product;free_shipping;base_shipping_cost;multiply_by_quantity;free_shipping_quantity;apply_threshold;separate_package
123;0;40.00;0;3;0;1
456;0;60.00;0;0;1;0
789;1;0.00;0;0;0;0
```

### 9.2 Format XML

```xml
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product id="123">
        <free_shipping>0</free_shipping>
        <base_shipping_cost>40.00</base_shipping_cost>
        <multiply_by_quantity>0</multiply_by_quantity>
        <free_shipping_quantity>3</free_shipping_quantity>
        <apply_threshold>0</apply_threshold>
        <separate_package>1</separate_package>
    </product>
</products>
```

### 9.3 Walidacja Importu

- Sprawdzenie istnienia produktu w bazie
- Walidacja typów danych
- Raport błędów z numerami linii
- Tryb "dry run" (podgląd bez zapisu)

---

## 10. Konfiguracja Modułu

### 10.1 Klucze Configuration

| Klucz | Typ | Domyślna wartość | Opis |
|-------|-----|------------------|------|
| `CUSTOMCARRIER_ACTIVE` | bool | 1 | Czy moduł aktywny |
| `CUSTOMCARRIER_NAME` | string | "Wysyłka kurierem" | Nazwa kuriera dla klienta |
| `CUSTOMCARRIER_THRESHOLD_MODE` | string | "cart" | Tryb progu: "cart" lub "products" |
| `CUSTOMCARRIER_LOGGING` | bool | 1 | Czy logować obliczenia |
| `CUSTOMCARRIER_LOG_RETENTION` | int | 30 | Dni retencji logów |
| `CUSTOMCARRIER_CACHE_ENABLED` | bool | 1 | Czy cache aktywny |
| `CUSTOMCARRIER_CACHE_TTL` | int | 300 | TTL cache w sekundach |

---

## 11. Wymagania Systemowe

### 11.1 Serwer
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Rozszerzenia PHP: json, mbstring, xml

### 11.2 PrestaShop
- Wersja 8.0, 8.1, 8.2
- Aktywny moduł Carrier (standardowy)

### 11.3 Uprawnienia
- Zapis do `var/logs/`
- Zapis do `var/cache/`

---

## 12. Bezpieczeństwo

### 12.1 Walidacja Danych
- Wszystkie dane wejściowe walidowane przez Symfony Forms
- Sanityzacja danych w repozytoriach
- Prepared statements (Doctrine)

### 12.2 Autoryzacja
- Panel konfiguracji: uprawnienia `CONFIGURE`
- Edycja produktu: uprawnienia `EDIT` dla produktów
- Import/Export: uprawnienia `CONFIGURE`

### 12.3 CSRF
- Tokeny CSRF dla wszystkich formularzy (standard Symfony)

---

## 13. Testowanie

### 13.1 Testy Jednostkowe
```
tests/
├── Unit/
│   ├── Service/
│   │   ├── ShippingCalculatorTest.php
│   │   ├── CartAnalyzerTest.php
│   │   └── ThresholdCheckerTest.php
│   └── Entity/
│       └── CustomCarrierProductTest.php
└── Integration/
    └── ShippingCalculationIntegrationTest.php
```

### 13.2 Scenariusze Testowe
- Produkt z darmową wysyłką bezwarunkową
- Produkt z progiem ilościowym (poniżej/powyżej)
- Produkt z progiem kwotowym (poniżej/powyżej)
- Mix produktów (osobna paczka + wspólna)
- Kombinacje produktów z nadpisanymi ustawieniami
- Cache hit/miss
- Import CSV z błędami

---

## 14. Migracje i Aktualizacje

### 14.1 Wersjonowanie
- Semantic Versioning (MAJOR.MINOR.PATCH)
- Skrypty upgrade w `upgrade/upgrade-x.x.x.php`

### 14.2 Kompatybilność Wsteczna
- Migracje bazy danych zachowują istniejące dane
- Deprecation warnings dla usuniętych funkcji
