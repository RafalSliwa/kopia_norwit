/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 *
 *  @author    Peter Sliacky (Prestasmart)
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* 10.9.2025 - tested with ruch v2.2.14 (Orlen module) - by Marcin Bogdanski @ Opennet */

tc_confirmOrderValidations['ruch'] = function() {
  if (
      $('.delivery-option.ruch input[type=radio]').is(':checked') &&
      'undefined' !== typeof testPkt17() && !testPkt17()
  ) {
    var shippingErrorMsg = $('#thecheckout-shipping .inner-wrapper > .error-msg');
    $('.shipping-validation-details').remove();
    shippingErrorMsg.append('<span class="shipping-validation-details"> (ORLEN Paczka)</span>')
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false;
  } else {
    return true;
  }
}

checkoutShippingParser.ruch = {
  after_load_callback: function(deliveryOptionIds) {
    ruch_async_carrier_loaded = true;
    if ('undefined' !== typeof ruchSelectWidgetMode)
    {
      // ruch_widget_started = true;
      ruchSelectWidgetMode();
      $('.delivery-option input').on('click', function() {
        ruchSelectWidgetMode();
      });
    }
  },
}
