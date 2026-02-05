document.addEventListener('DOMContentLoaded', function () {
  const callUsLink = document.querySelector('.call-us');
  const modal = document.getElementById('zadarma-modal');
  const close = document.querySelector('.zadarma-close');
  const form = document.getElementById('zadarma-form');
  const success = document.getElementById('zadarma-success');
  const errorBox = document.getElementById('zadarma-error');
  const fromField = document.getElementById('zadarma-from-number');
  const phoneField = document.getElementById('zadarma-phone');

  // ✅ Format phone number - prefix +48 with formatting
  if (phoneField) {
    // Get language/locale from PrestaShop
    const currentLanguage = window.prestashop?.language?.iso_code || 
                         document.documentElement.lang || 
                         'pl'; // fallback
  
    // console.log('[Zadarma] Detected language:', currentLanguage);
    
    // Add prefix +48 only for Polish store
    const shouldAddPrefix = currentLanguage === 'pl';
    const phonePrefix = '+48 ';
    
    // Add prefix on first focus if field is empty (only for PL)
    phoneField.addEventListener('focus', function(e) {
      if (!e.target.value.trim() && shouldAddPrefix) {
        e.target.value = phonePrefix;
        setTimeout(() => {
          e.target.setSelectionRange(4, 4); // cursor after prefix
        }, 0);
      }
    });

    phoneField.addEventListener('input', function(e) {
      let value = e.target.value;
      
      if (shouldAddPrefix) {
        // Logic for Polish store (with +48 prefix)
        if (!value || value.length === 0) {
          e.target.value = phonePrefix;
          setTimeout(() => {
            e.target.setSelectionRange(4, 4);
          }, 0);
          return;
        }
        
        // Remove everything except digits
        let numbers = value.replace(/\D/g, '');
        
        // Make sure it starts with 48
        if (!numbers.startsWith('48')) {
          if (numbers.startsWith('0')) {
            numbers = '48' + numbers.substring(1);
          } else if (numbers.length > 0) {
            numbers = '48' + numbers;
          } else {
            numbers = '48';
          }
        }
        
        // ✅ DODAJ PLUS OD RAZU
        numbers = '+' + numbers;  // "48700123456" → "+48700123456"
        
        // Limit to 12 characters (+48 + 9 digits)
        if (numbers.length > 12) {
          numbers = numbers.substring(0, 12);
        }
        
        // Format: +48 XXX XXX XXX
        let formatted = numbers.substring(0, 3);  // "+48"
        if (numbers.length > 3) {
          const rest = numbers.substring(3);
          if (rest.length > 0) formatted += ' ' + rest.substring(0, 3);
          if (rest.length > 3) formatted += ' ' + rest.substring(3, 6);
          if (rest.length > 6) formatted += ' ' + rest.substring(6, 9);
        }
        
        e.target.value = formatted;
        
        // Check if number is valid and hide error if so
        const errorBox = document.getElementById('zadarma-error');
        const cleanNumbers = formatted.replace(/\D/g, '');
        if (cleanNumbers.length === 11 && cleanNumbers.startsWith('48')) {
          if (errorBox) errorBox.style.display = 'none';
        }
        
        // Set cursor at the end
        setTimeout(() => {
          e.target.setSelectionRange(formatted.length, formatted.length);
        }, 0);
        
      } else {
        // Logic for other languages (without prefix)
        // Remove everything except digits, spaces and characters +, -, ()
        let cleaned = value.replace(/[^\d\s\+\-\(\)]/g, '');
        e.target.value = cleaned;
        
        // Hide validation error for other languages
        const errorBox = document.getElementById('zadarma-error');
        if (errorBox) errorBox.style.display = 'none';
      }
    });

    // Don't allow deleting entire content - keep at least prefix (only for PL)
    phoneField.addEventListener('keydown', function(e) {
      if (shouldAddPrefix) {
        const cursorPos = e.target.selectionStart;
        if ((e.key === 'Backspace' || e.key === 'Delete') && cursorPos <= 3 && e.target.value.length <= 4) {
          e.preventDefault();
          e.target.value = phonePrefix;
          e.target.setSelectionRange(4, 4);
        }
      }
    });

    // Don't allow leaving field with empty value (only for PL)
    phoneField.addEventListener('blur', function(e) {
      if (shouldAddPrefix && (!e.target.value.trim() || e.target.value.trim() === '+48')) {
        e.target.value = '';
      }
    });

    // Format on paste
    phoneField.addEventListener('paste', function(e) {
      setTimeout(() => {
        phoneField.dispatchEvent(new Event('input'));
      }, 10);
    });
  }

  // ✅ Mobile device detection
  function isMobileDevice() {
    return /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
  }

  // ✅ Set FROM number - priority order:
  // 1. Admin panel configuration (window.zadarma_from_number)
  // 2. Fallback static number
  if (fromField) {
    if (typeof window.zadarma_from_number !== 'undefined' && window.zadarma_from_number) {
      fromField.value = window.zadarma_from_number;
      //console.log('[Zadarma] FROM set from admin config:', window.zadarma_from_number);
    } else {
      fromField.value = '+48573568477';
      //console.log('[Zadarma] FROM set to fallback number: +48573568477');
    }
  }

  // ✅ Open modal on desktop
  if (callUsLink && modal && !isMobileDevice()) {
    callUsLink.addEventListener('click', function (e) {
      e.preventDefault();
      modal.style.display = 'block';
    });
  }

  // ❌ Close modal on "X" click
  if (close) {
    close.addEventListener('click', function () {
      modal.style.display = 'none';
    });
  }

  // ❌ Close modal on outside click
  window.addEventListener('click', function (e) {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });

  // 📤 AJAX form handling
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      if (errorBox) errorBox.style.display = 'none';
      if (success) success.style.display = 'none';

      const formData = new FormData(form);
      formData.append('zadarma_callback', 1);

      // Get language for validation
      const currentLanguage = window.prestashop?.language?.iso_code || 
                           document.documentElement.lang || 
                           'pl';

      // Format phone number for API
      const phoneInput = document.getElementById('zadarma-phone');
      if (phoneInput && phoneInput.value) {
        // ✅ ZACHOWAJ + JEŚLI JEST NA POCZĄTKU
        let phoneNumber = phoneInput.value.trim();
        
        // Jeśli numer zaczyna się od +, zachowaj go
        if (phoneNumber.startsWith('+')) {
          // Usuń wszystko oprócz cyfr i + na początku
          phoneNumber = '+' + phoneNumber.substring(1).replace(/\D/g, '');
        } else {
          // Jeśli nie ma +, usuń wszystkie znaki niebędące cyframi
          phoneNumber = phoneNumber.replace(/\D/g, '');
        }
        
        // console.log('[DEBUG] Phone with +:', phoneNumber, 'length:', phoneNumber.length, 'language:', currentLanguage);
        
        if (currentLanguage === 'pl') {
          // Validation for Polish numbers
          const digitsOnly = phoneNumber.replace(/\D/g, ''); // Dla walidacji tylko cyfry
          
          if (digitsOnly.length === 11 && digitsOnly.startsWith('48')) {
            formData.set('phone', phoneNumber); // ✅ WYŚLIJ Z + jeśli był
          } else {
            if (errorBox) {
              errorBox.textContent = window.zadarmaTranslations?.phoneError || '❌ Enter a valid phone number (9 digits).';
              errorBox.style.display = 'block';
            } else {
              alert(window.zadarmaTranslations?.phoneError || '❌ Enter a valid Polish phone number (9 digits).');
            }
            return;
          }
        } else {
          // Validation for other countries - more liberal
          const digitsOnly = phoneNumber.replace(/\D/g, '');
          
          if (digitsOnly.length >= 8 && digitsOnly.length <= 15) {
            formData.set('phone', phoneNumber); // ✅ WYŚLIJ Z + jeśli był
          } else {
            if (errorBox) {
              errorBox.textContent = window.zadarmaTranslations?.phoneError || '❌ Enter a valid phone number.';
              errorBox.style.display = 'block';
            } else {
              alert(window.zadarmaTranslations?.phoneError || '❌ Enter a valid phone number.');
            }
            return;
          }
        }
      }

      // Add page URL (referer)
      formData.append('referer_url', window.location.href);

      // ✅ TYMCZASOWO - TYLKO LOCAL WEBHOOK (bez n8n)
      const localWebhookUrl = '/modules/zadarmacallback/webhook.php';

      console.log('[Zadarma] TESTING - sending ONLY to local webhook:', localWebhookUrl);

      // ✅ TYLKO JEDEN REQUEST do local webhook
      fetch(localWebhookUrl, {
        method: 'POST',
        body: new URLSearchParams(formData)
      })
      .then(response => {
        response.clone().text().then(txt => {
          // console.log('RAW response:', txt);
          // console.log('[DEBUG] Response length:', txt.length);
        });
        
        // console.log('[DEBUG] Content-Type:', response.headers.get('content-type'));
        
        // Check if response contains JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
          // console.log('[DEBUG] Parsing JSON...');
          
          // Check if response is not empty
          return response.text().then(text => {
            // console.log('[DEBUG] Response text:', text);
            if (text.trim() === '') {
              // console.log('[DEBUG] Empty JSON response, using fallback');
              return { success: true };
            }
            return JSON.parse(text);
          });
        } else {
          // Fallback - assume success
          // console.log('[Zadarma] Endpoint did not return JSON, assuming success');
          return { success: true };
        }
      })
      .then(res => {
        if (res.success) {
          const description = document.getElementById('zadarma-description');
          const headerTitle = document.getElementById('zadarma-header-title');
          
          if (form) form.style.display = 'none';
          if (description) description.style.display = 'none';
          if (headerTitle) headerTitle.style.display = 'none';
          if (success) success.style.display = 'block';

          setTimeout(() => {
            if (modal) modal.style.display = 'none';
            if (form) {
               form.style.display = 'block';
              form.reset();
             }
             if (description) description.style.display = 'block';
            if (headerTitle) headerTitle.style.display = 'block';
           if (success) success.style.display = 'none';
         }, 4000);
        } else {
          if (errorBox) {
            errorBox.textContent = res.error || window.zadarmaTranslations?.generalError || '❌ An error occurred.';
            errorBox.style.display = 'block';
          } else {
            alert(res.error || window.zadarmaTranslations?.generalError || '❌ An error occurred.');
          }
        }
      })
      .catch((err) => {
        console.error('AJAX error:', err);
        if (errorBox) {
          errorBox.textContent = window.zadarmaTranslations?.connectionError || '❌ Server connection error.';
          errorBox.style.display = 'block';
        } else {
          alert(window.zadarmaTranslations?.connectionError || '❌ Server connection error.');
        }
      });

      /* ❌ ZAKOMENTOWANE - n8n logic
      // Get webhook URL from module configuration or use default
      const webhookUrl = window.zadarma_webhook_url || '/modules/zadarmacallback/webhook.php';
      const localWebhookUrl = '/modules/zadarmacallback/webhook.php';

      // ✅ NORMALIZUJ URLs - usuń domenę z main webhook
      let normalizedWebhookUrl = webhookUrl;
      if (webhookUrl.includes('://')) {
        // Usuń https://www.norwit.pl część
        const url = new URL(webhookUrl);
        normalizedWebhookUrl = url.pathname; // Zostanie: /modules/zadarmacallback/webhook.php
      }

      console.log('[Zadarma] Normalized webhook URL:', normalizedWebhookUrl);
      console.log('[Zadarma] Local webhook URL:', localWebhookUrl);

      // Send to main webhook (could be n8n or local)
      const mainRequest = fetch(webhookUrl, {
        method: 'POST',
        body: new URLSearchParams(formData)
      });

      // ✅ Porównaj znormalizowane URLs
      const requests = [mainRequest];
      if (normalizedWebhookUrl !== localWebhookUrl) {
        console.log('[Zadarma] Also sending to local webhook.php for API call');
        const localRequest = fetch(localWebhookUrl, {
          method: 'POST',
          body: new URLSearchParams(formData)
        });
        requests.push(localRequest);
      } else {
        console.log('[Zadarma] URLs are same - sending to ONE webhook only');
      }

      Promise.all(requests)
        .then(responses => {
          // Use first response (main webhook) for UI
          const mainResponse = responses[0];
          
          mainResponse.clone().text().then(txt => {
            // console.log('RAW response:', txt);
            // console.log('[DEBUG] Response length:', txt.length);
          });
          
          // console.log('[DEBUG] Content-Type:', mainResponse.headers.get('content-type'));
          
          // Check if response contains JSON
          const contentType = mainResponse.headers.get('content-type');
          if (contentType && contentType.includes('application/json')) {
            // console.log('[DEBUG] Parsing JSON...');
            
            // Check if response is not empty
            return mainResponse.text().then(text => {
              // console.log('[DEBUG] Response text:', text);
              if (text.trim() === '') {
                // console.log('[DEBUG] Empty JSON response, using fallback');
                return { success: true };
              }
              return JSON.parse(text);
            });
          } else {
            // Fallback for n8n and other endpoints that don't return JSON
            // console.log('[Zadarma] Endpoint did not return JSON, assuming success');
            return { success: true };
          }
        })
        .then(res => {
          if (res.success) {
            const description = document.getElementById('zadarma-description');
            const headerTitle = document.getElementById('zadarma-header-title');
            
            if (form) form.style.display = 'none';
            if (description) description.style.display = 'none';
            if (headerTitle) headerTitle.style.display = 'none';
            if (success) success.style.display = 'block';

            setTimeout(() => {
              if (modal) modal.style.display = 'none';
              if (form) {
                 form.style.display = 'block';
                form.reset();
               }
               if (description) description.style.display = 'block';
              if (headerTitle) headerTitle.style.display = 'block';
             if (success) success.style.display = 'none';
           }, 4000);
          } else {
            if (errorBox) {
              errorBox.textContent = res.error || window.zadarmaTranslations?.generalError || '❌ An error occurred.';
              errorBox.style.display = 'block';
            } else {
              alert(res.error || window.zadarmaTranslations?.generalError || '❌ An error occurred.');
            }
          }
        })
        .catch((err) => {
          console.error('AJAX error:', err);
          if (errorBox) {
            errorBox.textContent = window.zadarmaTranslations?.connectionError || '❌ Server connection error.';
            errorBox.style.display = 'block';
          } else {
            alert(window.zadarmaTranslations?.connectionError || '❌ Server connection error.');
          }
        });
      */
    });
  }
});
