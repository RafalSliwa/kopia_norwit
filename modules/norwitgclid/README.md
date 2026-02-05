# Norwit GCLID Server-Side Tracker v1.1.0

## Problem
Standardowe trackery używają cookies, które są blokowane przez:
- Cookie consent banners (x13eucookies)
- Ad blockery
- Safari ITP (iOS)

**Wynik: 60% utraconych konwersji**

## Rozwiązanie
Ten moduł zapisuje identyfikatory po stronie serwera:
- PHP session (first-party, omija consent)
- Baza danych (trwałe przechowywanie)
- Mapowanie identyfikator ↔ numer Zadarma

### Obsługiwane identyfikatory:
- **gclid** - standard Google Ads Click ID
- **wbraid** - iOS Web-to-App (po iOS 14.5)
- **gbraid** - iOS App-to-Web
- **ga_client_id** - Google Analytics Client ID (dla SEO)

## Instalacja przez Menedżer Modułów

### Opcja 1: Upload ZIP (zalecane)
```
1. Spakuj folder: zip -r norwitgclid.zip norwitgclid/
2. Panel admina → Moduły → Menedżer modułów → Prześlij moduł
3. Wybierz norwitgclid.zip
4. Kliknij "Instaluj"
```

### Opcja 2: FTP + Panel
```
1. Prześlij folder norwitgclid/ do /modules/
2. Panel admina → Moduły → Menedżer modułów
3. Wyszukaj "Norwit Conversion"
4. Kliknij "Instaluj"
```

### Opcja 3: CLI
```bash
php bin/console prestashop:module install norwitgclid
```

## Po instalacji

### 1. Wyłącz stary gclidtracker (jeśli istnieje)
```bash
php bin/console prestashop:module disable gclidtracker
```

### 2. Skonfiguruj webhook Zadarma
W Zadarma ustaw webhook na n8n, który przekazuje do:
```
POST https://norwit.pl/modules/norwitgclid/zadarma_webhook.php
```

### 3. Skonfiguruj cron (serwer)
```bash
* * * * * /usr/bin/python3 /path/to/scripts/process_conversions.py >> /var/log/gads_conversions.log 2>&1
```

## Tabele w bazie danych

Moduł automatycznie tworzy 2 tabele:

### {PREFIX}_norwit_gclid
Główna tabela tracking - przechowuje identyfikatory użytkowników:
- gclid, wbraid, gbraid, ga_client_id
- session_id, zadarma_number, phone_displayed
- ip_address, user_agent, landing_page, referrer
- conversion_sent, conversion_type, conversion_value

### {PREFIX}_norwit_conversion_queue
Kolejka konwersji do wysłania do Google Ads:
- gclid_record_id, gclid, wbraid, gbraid
- conversion_action, conversion_value
- call_datetime, call_duration
- status (pending/sent/error), error_message, retry_count

## Flow danych

```
1. User → ?gclid=XXX → PHP hook → SESSION + DB (norwit_gclid)
2. Zadarma GTM → podmiana numeru → JS Bridge → DB update
3. Telefon → Zadarma webhook → PHP → DB (conversion_queue)
4. Cron (Python) → Google Ads API → konwersja offline
```

## Wartościowanie konwersji

- **Długa rozmowa (≥2min)**: 50 PLN - conversion_action: 7478977159
- **Krótka rozmowa (<2min)**: 5 PLN - conversion_action: 7478988194

## API Endpoints

### GET: Health check
```bash
curl "https://norwit.pl/modules/norwitgclid/api.php?action=health"
```

### GET: Pobierz rekord dla numeru Zadarma
```bash
curl -H "X-API-KEY: xxx" \
  "https://norwit.pl/modules/norwitgclid/api.php?action=get_gclid&zadarma_number=48123456789"
```

### POST: Zadarma webhook
```bash
curl -X POST "https://norwit.pl/modules/norwitgclid/zadarma_webhook.php" \
  -H "Content-Type: application/json" \
  -d '{"event":"NOTIFY_END","caller_id":"48123456789","duration":150}'
```

## Testowanie

### Test 1: Sprawdź zapis identyfikatorów
```
1. Otwórz: https://norwit.pl/?gclid=TEST123
2. Sprawdź bazę: SELECT * FROM {PREFIX}_norwit_gclid ORDER BY id DESC LIMIT 1;
```

### Test 2: Sprawdź kolejkę konwersji
```sql
SELECT * FROM {PREFIX}_norwit_conversion_queue WHERE status = 'pending';
```

### Test 3: Health check API
```bash
curl "https://norwit.pl/modules/norwitgclid/api.php?action=health"
```

## Wymagania

- PrestaShop 1.7.x / 8.x
- PHP 7.4+
- MySQL/MariaDB
- Python 3.8+ (dla skryptu konwersji)
- google-ads Python library
