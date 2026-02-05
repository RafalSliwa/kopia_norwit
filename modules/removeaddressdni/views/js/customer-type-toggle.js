document.addEventListener('DOMContentLoaded', function() {
  setupCustomerTypeToggle();
  setupVatValidation();

  // Observe changes in the form (e.g. after AJAX)
  var target = document.getElementById('custom-address-form') || document.body;
  var observer = new MutationObserver(function(mutations) {
    // After each change, re-attach handlers
    setupCustomerTypeToggle();
    setupVatValidation();
  });
  observer.observe(target, { childList: true, subtree: true });
});

function setupCustomerTypeToggle() {
  var btns = document.querySelectorAll('.customer-type-btn');
  var form = document.getElementById('custom-address-form');
  var customerTypeInput = document.getElementById('customer_type');

  function toggleCompanyFields(show) {
    if (!form) return;
    var companyContainers = form.querySelectorAll('.form-group, .form-row, .form-field');
    companyContainers.forEach(function(container) {
      var hasCompany = container.querySelector('input[name="company"]');
      var hasVat = container.querySelector('input[name="vat_number"]');
      if (hasCompany || hasVat) {
        container.style.display = show ? '' : 'none';
      }
    });
  }

  // Set visibility of fields based on customer type
  function updateCompanyFieldsVisibility() {
    if (customerTypeInput && customerTypeInput.value === 'company') {
      toggleCompanyFields(true);
      btns.forEach(b => b.classList.remove('active'));
      btns.forEach(b => { if (b.dataset.type === 'company') b.classList.add('active'); });
    } else {
      toggleCompanyFields(false);
      btns.forEach(b => b.classList.remove('active'));
      btns.forEach(b => { if (b.dataset.type === 'private') b.classList.add('active'); });
    }
  }

  // Hide company/vat fields by default
  updateCompanyFieldsVisibility();

  // Hide/show pdgetdatabyvatnumberpro block based on customer type
  function updatePdgetdatabyvatnumberproVisibility() {
    var pdgetBlock = document.getElementById('pdgetdatabyvatnumberpro');
    if (!pdgetBlock) return;
    if (customerTypeInput && customerTypeInput.value === 'private') {
      pdgetBlock.style.display = 'none';
    } else {
      pdgetBlock.style.display = '';
    }
  }

  // Update on load
  updatePdgetdatabyvatnumberproVisibility();

  btns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (customerTypeInput) customerTypeInput.value = btn.dataset.type;
      updateCompanyFieldsVisibility();
      updatePdgetdatabyvatnumberproVisibility();
      setupVatValidation(); // refresh VAT validation after customer type change
    });
  });

  // Hide/show fields after country change
  var countrySelect = form ? form.querySelector('[name="id_country"]') : null;
  if (countrySelect) {
    countrySelect.addEventListener('change', function() {
      setTimeout(function() {
        updateCompanyFieldsVisibility();
        updatePdgetdatabyvatnumberproVisibility();
        setupVatValidation();
      }, 150);
    });
  }

  // Hide/show fields after language change (listen to all language selects if present)
  var langSelects = document.querySelectorAll('select[name="language"], .language-selector select');
  langSelects.forEach(function(langSelect) {
    langSelect.addEventListener('change', function() {
      setTimeout(function() {
        updateCompanyFieldsVisibility();
        updatePdgetdatabyvatnumberproVisibility();
        setupVatValidation();
      }, 150);
    });
  });
}

function setupVatValidation() {
  var vatInput = document.getElementById('field-vat_number');
  var companyInput = document.getElementById('field-company');
  var useSameAddressCheckbox = document.getElementById('use_same_address');
  // Nowe: sprawdzamy typ formularza po polu hidden
  var form = vatInput ? vatInput.form : null;
  var typeInput = form ? form.querySelector('input[name="type"]') : null;
  console.log('typeInput:', typeInput);
  console.log('typeInput.value:', typeInput ? typeInput.value : null);
  var isInvoice = typeInput ? typeInput.value === 'invoice' : true; // jeśli brak pola, domyślnie true
  if (!vatInput) return;

  // VAT validation message
  var msg = typeof window.vatValidationMsg !== 'undefined'
    ? window.vatValidationMsg
    : 'Enter exactly 10 digits after PL in the VAT field!';

  // Add error div if it does not exist
  var errorDiv = document.getElementById('vat-error');
  if (!errorDiv) {
    errorDiv = document.createElement('div');
    errorDiv.id = 'vat-error';
    errorDiv.style.color = 'red';
    errorDiv.style.display = 'none';
    vatInput.parentNode.appendChild(errorDiv);
  }

  function getIsPoland() {
    var form = vatInput.form;
    var countrySelect = form ? form.querySelector('[name="id_country"]') : null;
    if (!countrySelect) return false;
    var selectedOption = countrySelect.options[countrySelect.selectedIndex];
    return selectedOption && (
      selectedOption.text.toLowerCase().indexOf('polska') !== -1 ||
      selectedOption.value === '14'
    );
  }

  function isVisible(el) {
    return !!(el && el.offsetParent !== null);
  }

  function validateVat() {
    console.log('🧾 [VAT VALIDATION] ---');
    var isPoland = getIsPoland();
    var companyFilled = companyInput && companyInput.value.trim().length > 0;
    // Poprawka: jeśli nie ma checkboxa, VAT nie jest wymagany
    var useSameChecked = useSameAddressCheckbox ? useSameAddressCheckbox.checked : false;
    var value = vatInput.value;
    var vatVisible = isVisible(vatInput);
    var digits = vatInput.value.slice(2);

    // DEBUG: loguj wartości
    console.log('validateVat called');
    console.log('vatVisible:', vatVisible);
    console.log('companyFilled:', companyFilled);
    console.log('isPoland:', isPoland);
    console.log('useSameChecked:', useSameChecked);
    console.log('isInvoice:', isInvoice);
    console.log('vatInput.value:', vatInput.value);

    // Sprawdź stan checkboxa z emoji/logiem
    if (useSameAddressCheckbox) {
      if (useSameAddressCheckbox.checked) {
        console.log('✅ Checkbox use_same_address jest zaznaczony');
      } else {
        console.log('❌ Checkbox use_same_address jest odznaczony');
      }
    } else {
      console.log('⚠️ Checkbox use_same_address nie istnieje');
    }

    if (isPoland) {
      if (!value.startsWith('PL')) value = 'PL';
      value = 'PL' + value.replace(/^PL/, '').replace(/[^0-9]/g, '').slice(0, 10);
      vatInput.value = value;
      digits = vatInput.value.slice(2); // aktualizuj digits po zmianie value
    }

    // Jeśli nie ma checkboxa (useSameChecked === true), VAT wymagany dla faktury
    if (vatVisible && companyFilled && isPoland && useSameChecked && isInvoice) {
      vatInput.setAttribute('required', 'required');
      if (digits.length !== 10) {
        errorDiv.textContent = msg;
        errorDiv.style.display = 'block';
        console.log('VAT ERROR: digits.length !== 10');
        return false;
      } else {
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        console.log('VAT OK');
        return true;
      }
    } else {
      vatInput.removeAttribute('required');
      errorDiv.textContent = '';
      errorDiv.style.display = 'none';
      console.log('VAT not required');
      return true;
    }
  }

  // Remove old events before adding new ones (so MutationObserver does not duplicate them)
  vatInput.oninput = null;
  if (companyInput) companyInput.oninput = null;

  vatInput.addEventListener('input', validateVat);
  if (companyInput) companyInput.addEventListener('input', validateVat);

  var form = vatInput.form;
  if (form) {
    form.onsubmit = null;
    form.addEventListener('submit', function(e) {
      // Waliduj nawet jeśli pole jest puste lub ukryte
      if (!validateVat()) {
        e.preventDefault();
        vatInput.focus();
      }
    });
  }

  // After country change, force format and validation
  var countrySelect = form ? form.querySelector('[name="id_country"]') : null;
  if (countrySelect) {
    countrySelect.onchange = null;
    countrySelect.addEventListener('change', function() {
      setTimeout(validateVat, 100);
    });
  }

  if (useSameAddressCheckbox) {
    useSameAddressCheckbox.addEventListener('change', validateVat);
  }
}
