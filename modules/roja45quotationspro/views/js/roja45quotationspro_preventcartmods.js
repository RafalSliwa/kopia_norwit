/**
 * roja45quotationspro_order.js.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

$(document).ready(function () {
    if (!(typeof roja45quotationspro_cart_modified !== "undefined") && roja45quotationspro_cart_modified) {
        var here = '';
    }
    if (!parseInt(roja45quotationspro_in_cart)) {
        var quote_button = $('#btn_request_quote');
        quote_button.css('margin-right', '10px');
        $('.cart_navigation').append(quote_button);
        quote_button.fadeIn();
    } else {
        if (!roja45quotationspro_allow_modifications) {
            $('.cart_quantity_input').attr('disabled', true);
            $('.cart_quantity_down').hide();
            $('.cart_quantity_up').hide();
            $('.cart_quantity_delete').hide();
            $('.price_discount_delete').hide();
        }
        $('.cart_navigation a.standard-checkout').show();
    }
});
