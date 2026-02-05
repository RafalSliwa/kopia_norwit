{if isset($cod_warning_text)}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    function findCodInput() {
      let input = document.querySelector('input[data-module-name="ps_cashondelivery"]');
      if (input) {
        return input;
      }
      input = Array.from(document.querySelectorAll('input[data-module-name]')).find(function (candidate) {
        var moduleName = (candidate.getAttribute('data-module-name') || '').toLowerCase();
        return moduleName.indexOf('cashondelivery') !== -1;
      });
      if (input) {
        return input;
      }
      var container = Array.from(document.querySelectorAll('[data-payment-module]')).find(function (node) {
        var paymentModule = (node.getAttribute('data-payment-module') || '').toLowerCase();
        return paymentModule.indexOf('cashondelivery') !== -1;
      });
      if (container) {
        return container.querySelector('input[type="radio"]');
      }
      return null;
    }

    var codWarningLogged = false;

    function disableCodPayment() {
      var codInput = findCodInput();
      if (!codInput) {
        if (!codWarningLogged) {
          console.warn('Nie znaleziono pola płatności Cash on Delivery.');
          codWarningLogged = true;
        }
        return;
      }
      codWarningLogged = false;
      codInput.disabled = true;

      var container = codInput.closest('.payment-option');
      if (container) {
        container.classList.add('cod-disabled');
        if (!container.querySelector('.cod-limit-warning')) {
          var warning = document.createElement('div');
          warning.className = 'cod-limit-warning alert alert-warning';
          warning.textContent = "{$cod_warning_text|escape:'js'}";
          container.appendChild(warning);
        }
      }
    }

    disableCodPayment();

    if (window.prestashop && typeof window.prestashop.on === 'function') {
      window.prestashop.on('updatedPaymentOptions', disableCodPayment);
    }

    var paymentStep = document.querySelector('#checkout-payment-step');
    if (paymentStep && typeof MutationObserver !== 'undefined') {
      var observer = new MutationObserver(function (mutations) {
        if (mutations.some(function (mutation) {
          return mutation.addedNodes && mutation.addedNodes.length;
        })) {
          disableCodPayment();
        }
      });
      observer.observe(paymentStep, {childList: true, subtree: true});
    }
  });
</script>
{/if}
