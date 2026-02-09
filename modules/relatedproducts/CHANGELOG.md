# Related Products - Changelog

## v1.2.1 (2026-02-09)

### 🐛 Poprawki błędów

- **Naprawiono efekt "flash" w koszyku przy zwiększaniu ilości**
  - Problem: Tekst "Dostawa: Za darmo!" migał/drżał przy zwiększaniu ilości produktu w modalnym koszyku
  - Przyczyna: Funkcja JavaScript `applyDeliveryText()` była wywoływana wielokrotnie i aktualizowała DOM nawet gdy status wysyłki się nie zmienił
  - Rozwiązanie: Dodano sprawdzenie `statusChanged` i `lastUpdateTime` w `relatedproducts-cart.js` (linie 439-448, 100ms throttle)

### 🔧 Zmiany techniczne

- Zoptymalizowano funkcję `applyDeliveryText()` w JavaScript aby zapobiec redundantnym aktualizacjom DOM
- Dodano time-based throttling (100ms) dla aktualizacji statusu wysyłki

---

## v1.1.0 (2026-02-05)

### ✨ Internacjonalizacja

- **Usunięto wszystkie hardcoded teksty** - moduł w pełni zgodny z systemem tłumaczeń PrestaShop
- Naprawiono hardcoded polskie komunikaty w `relatedproducts.php`:
  - 13+ wystąpień `"Za darmo!"` → `$this->l('Free!')`
  - `"Kurier Norwit 01"` → `$this->l('Courier Norwit 01')`
  - `"Standard delivery"` → `$this->l('Standard delivery')`
  - `"Przesyłka kurierska"` → `$this->l('Courier shipment')`
  - `"Wysyłka"` → `$this->l('Shipping')`
  - Symbol waluty `" zł"` → `Tools::displayPrice()`
- Naprawiono hardcoded komunikaty JavaScript w `relatedproducts-cart.js`:
  - 5 komunikatów błędów i etykiet
  - Wszystkie przekazane przez `Media::addJsDef()` z tłumaczeniami
  - Fallback na angielskie teksty gdy tłumaczenia niedostępne
- **Dodano 10 nowych tłumaczeń polskich** w `translations/pl.php`

### 🔧 Zmiany techniczne

- Dodano obiekt `relatedproducts_translations` w JavaScript (przekazywany przez PHP)
- Używa PrestaShop `Media::addJsDef()` do przekazywania tłumaczeń do JS
- Wszystkie komunikaty użytkownika teraz tłumaczalne przez system PrestaShop

---

## v1.0.7 (poprzednia wersja)

- Stabilna wersja z podstawowymi funkcjonalnościami
- Integracja z customcarrier module
- Wyświetlanie powiązanych produktów w modal
- Kalkulacja kosztów wysyłki
- Obsługa progów darmowej dostawy
