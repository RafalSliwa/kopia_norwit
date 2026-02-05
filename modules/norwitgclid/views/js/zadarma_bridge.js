/**
 * Zadarma Bridge - Łączy dynamiczny numer Zadarma z GCLID
 *
 * Ten skrypt:
 * 1. Czeka na podmianę numeru przez Zadarma
 * 2. Wysyła session_id + numer do backendu
 * 3. Backend zapisuje połączenie GCLID ↔ numer
 *
 * Wymaga: NorwitGCLID musi być załadowany przed tym skryptem
 */

(function() {
    'use strict';

    // Konfiguracja
    var CONFIG = {
        // Używamy wewnętrznego endpointu (bez API key - autoryzacja przez sesję)
        apiEndpoint: '/modules/norwitgclid/save_zadarma.php',
        checkInterval: 1000, // ms
        maxAttempts: 30, // max 30 sekund czekania
        debug: false
    };

    // Sprawdź czy mamy GCLID
    if (!window.NorwitGCLID || !window.NorwitGCLID.gclid) {
        if (CONFIG.debug) console.log('[ZadarmaBridge] No GCLID, skipping');
        return;
    }

    var sessionId = window.NorwitGCLID.sessionId;
    var gclid = window.NorwitGCLID.gclid;
    var attempts = 0;
    var savedNumbers = new Set();

    if (CONFIG.debug) {
        console.log('[ZadarmaBridge] Initialized with GCLID:', gclid);
    }

    /**
     * Znajdź wszystkie numery telefonów na stronie
     */
    function findPhoneNumbers() {
        var phones = [];

        // Szukaj linków tel:
        document.querySelectorAll('a[href^="tel:"]').forEach(function(el) {
            var number = el.href.replace('tel:', '').replace(/\s/g, '');
            if (number && !savedNumbers.has(number)) {
                phones.push({
                    element: el,
                    number: number,
                    displayed: el.textContent.trim()
                });
            }
        });

        // Szukaj elementów z data-phone (Zadarma często tak robi)
        document.querySelectorAll('[data-phone], [data-zadarma-number]').forEach(function(el) {
            var number = el.dataset.phone || el.dataset.zadarmaNumber;
            if (number && !savedNumbers.has(number)) {
                phones.push({
                    element: el,
                    number: number.replace(/\s/g, ''),
                    displayed: el.textContent.trim()
                });
            }
        });

        return phones;
    }

    /**
     * Wyślij numer do backendu
     */
    function saveNumberToBackend(zadarmaNumber, phoneDisplayed) {
        if (savedNumbers.has(zadarmaNumber)) return;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', CONFIG.apiEndpoint, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                savedNumbers.add(zadarmaNumber);
                if (CONFIG.debug) {
                    console.log('[ZadarmaBridge] Saved:', zadarmaNumber, '→', gclid);
                }
            } else {
                console.error('[ZadarmaBridge] Failed to save:', xhr.responseText);
            }
        };

        // Używamy formData zamiast JSON (prostsze, bez CORS)
        var formData = 'session_id=' + encodeURIComponent(sessionId) +
                       '&zadarma_number=' + encodeURIComponent(zadarmaNumber) +
                       '&phone_displayed=' + encodeURIComponent(phoneDisplayed);

        xhr.send(formData);
    }

    /**
     * Sprawdź czy Zadarma podmienił numery
     */
    function checkForPhoneNumbers() {
        var phones = findPhoneNumbers();

        phones.forEach(function(phone) {
            // Zadarma podmienia numery na unikalne
            // Sprawdź czy to nie jest oryginalny numer
            if (phone.number.length > 8) {
                saveNumberToBackend(phone.number, phone.displayed);
            }
        });

        attempts++;

        if (attempts < CONFIG.maxAttempts && phones.length === 0) {
            setTimeout(checkForPhoneNumbers, CONFIG.checkInterval);
        }
    }

    /**
     * Obserwuj zmiany DOM (Zadarma może podmienić numery po załadowaniu)
     */
    function observeDOM() {
        if (!window.MutationObserver) return;

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    checkForPhoneNumbers();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }

    // Inicjalizacja
    function init() {
        // Czekaj na załadowanie DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(checkForPhoneNumbers, 2000); // Daj Zadarmie 2s na podmianę
                observeDOM();
            });
        } else {
            setTimeout(checkForPhoneNumbers, 2000);
            observeDOM();
        }
    }

    init();
})();
