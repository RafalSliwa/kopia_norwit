# Custom Carrier - Changelog

## v1.4.0 (2026-02-04)

### ✨ Internacjonalizacja

- **Usunięto wszystkie hardcoded teksty** - moduł w pełni zgodny z systemem tłumaczeń PrestaShop
- Naprawiono hardcoded polskie etykiety w `product_tab.tpl`:
  - "Bazowy koszt wysyłki (brutto)" → `{l s='Base shipping cost (gross)'}`
  - "brutto" → `{l s='gross'}`
  - Wszystkie opisy pól używają teraz tłumaczeń
- Naprawiono hardcoded ostrzeżenia JavaScript:
  - 7 komunikatów w `product_tab.tpl`
  - 8 komunikatów w `bulk_shipping.tpl`
  - Wszystkie używają `{l s='...' js=1}` dla właściwego escapowania
- Naprawiono hardcoded nazwę przewoźnika w `customcarrier.php`:
  - "Wysyłka kurierem" → `$this->trans('Courier shipping')`
- **Dodano 20 nowych tłumaczeń polskich** w `translations/pl.php`

### 🎨 Ulepszenia UX

- **Wybór liczby produktów na stronie** w masowych ustawieniach wysyłki:
  - Opcje: 50, 100, 200, 500, 1000, Wszystkie
  - Znacznie ułatwia konfigurację dużej liczby produktów (np. 4000+)
  - Stan wyboru jest zachowywany podczas nawigacji i filtrowania

### 🔒 Bezpieczeństwo

- Usunięto pliki debug z zagrożeniami bezpieczeństwa:
  - `debug_settings.php` - niezabezpieczony dostęp do danych produktów
  - `debug_cart.php` - niezabezpieczony dostęp do danych koszyka
- Usunięto debug logging do plików (zapobieganie wypełnieniu dysku)

### 🐛 Poprawki

- Naprawiono funkcjonalność "Osobna paczka" dla produktów z `separate_package = 1`
- Poprawiono obsługę produktów z wagą (max_weight_per_package)
- Dodano narzędzie debugowania (tymczasowe, usuwane po diagnozowaniu)

---

## v1.1.2 (2026-01-26)

### 🐛 Poprawki krytyczne

- **CRITICAL FIX**: `getOrderShippingCostExternal()` teraz deleguje do głównej metody zamiast zwracać `false`
  - **Problem**: Carrier był niewidoczny na liście przewoźników w PrestaShop 8.x
  - **Rozwiązanie**: Metoda external teraz wywołuje `getOrderShippingCost()` i zwraca prawidłowy koszt
  - **Efekt**: Carrier "Wysyłka kurierem" (ID 56) powinien pojawić się na liście

---

## v1.1.1 (2026-01-26)

### 🐛 Poprawki

- **FIX**: Carrier teraz pojawia się na liście również dla pustych koszyków
  - Poprzednio: `getOrderShippingCost()` zwracało `false` dla pustego koszyka → carrier niewidoczny
  - Teraz: Zwraca koszt domyślny (`CUSTOMCARRIER_DEFAULT_COST`) dla pustego koszyka → carrier widoczny

### 🔧 Zmiany

- Poprawiono logikę pól `max_packages` i `cost_above_max_packages`
- Carrier ID 56 "Wysyłka kurierem" powinien być teraz widoczny na liście przewoźników

---

## v1.1.0 (2026-01-26)

### ✨ Nowe funkcjonalności

#### Maksymalna ilość produktu w paczce

Dodano dwa nowe pola konfiguracyjne dla każdego produktu:

1. **Max quantity per package** (`max_quantity_per_package`)
   - Maksymalna ilość produktu, która mieści się w jednej paczce
   - Gdy ilość przekracza limit, koszt jest mnożony przez liczbę paczek
   - Wartość `0` lub puste pole = brak limitu (nieograniczona ilość w paczce)

2. **Package cost above max** (`package_cost_above_max`)
   - Alternatywny koszt dostawy dla dużych zamówień
   - Aktywuje się gdy ilość > `2 × max_quantity_per_package`
   - Pozwala na bardziej korzystną cenę dla większych zamówień (jedna paczka zamiast wielu)

### 🎯 Przykład użycia: Talerze do betonu

**Konfiguracja:**
- Base shipping cost: `60 zł`
- Max quantity per package: `2 szt`
- Package cost above max: `140 zł`

**Obliczanie kosztów:**
- **1-2 szt:** 1 paczka × 60 zł = **60 zł**
- **3-4 szt:** 2 paczki × 60 zł = **120 zł**
- **5+ szt:** przekroczenie progu (> 4) → jedna paczka = **140 zł**

**Integracja z progiem darmowej dostawy:**
- Gdy `apply_threshold = ON` i koszyk >= 3000 zł → **0 zł** (darmowa dostawa)
- Próg nadpisuje wszystkie inne reguły

### 🔧 Zmiany techniczne

#### Baza danych
- Dodano kolumnę `max_quantity_per_package` do tabeli `customcarrier_product`
- Dodano kolumnę `package_cost_above_max` do tabeli `customcarrier_product`

#### Logika obliczania kosztów

```php
// Pseudokod
if (max_quantity_per_package > 0) {
    packageCount = ceil(quantity / max_quantity_per_package);

    if (package_cost_above_max exists AND quantity > max_quantity_per_package × 2) {
        return package_cost_above_max; // Jeden pakiet z alternatywną ceną
    } else {
        return base_shipping_cost × packageCount; // Standardowe mnożenie
    }
}
```

#### Formularze Back Office

Nowe pola w sekcji "Custom Carrier Settings" na karcie produktu (Shipping):

- **Max quantity per package** - pole liczbowe (int), placeholder "0", jednostka "pcs"
- **Cost when exceeding max qty** - pole liczbowe (decimal), placeholder "0", waluta (zł/€)

### 📋 Migracja

Dla istniejących instalacji wykonaj:

```sql
ALTER TABLE `mvg2_customcarrier_product`
ADD COLUMN `max_quantity_per_package` INT(11) DEFAULT NULL
COMMENT 'Maksymalna ilość produktu w jednej paczce';

ALTER TABLE `mvg2_customcarrier_product`
ADD COLUMN `package_cost_above_max` DECIMAL(20,6) DEFAULT NULL
COMMENT 'Koszt paczki gdy ilość > max_quantity_per_package';
```

*(Zamień `mvg2_` na swój prefiks bazy danych)*

### 🧪 Testy

```bash
cd /Users/remac/norwit.pl/prestashop
php -r "require 'config/config.inc.php'; /* test code */"
```

Wszystkie testy przeszły pomyślnie ✅

---

## v1.0.2 (poprzednia wersja)

- Stabilna wersja z podstawowymi funkcjonalnościami
- Free shipping conditions
- Zone thresholds
- Multiply by quantity
- Separate package
- Apply threshold
