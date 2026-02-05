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
            $('.cart-overview .js-cart-line-product-quantity').attr('disabled', true);
            $('.cart-overview .js-cart-line-product-quantity .js-increase-product-quantity').hide();
            $('.cart-overview .js-cart-line-product-quantity .js-decrease-product-quantity').hide();
            $('.cart-overview a.remove-from-cart').hide();
        }
    }

    $('.btn-clear-quote').on('click', function(e) {
        $.ajax({
            url: roja45quotationspro_controller,
            type: 'post',
            dataType: 'json',
            data: {
                'action' : 'clearQuoteFromCart',
                'ajax' : 1,
            },
            beforeSend: function () {
                //roja45quotationspro.toggleWaitDialog();
            },
            success: function (data) {
                if (data.result) {
                    //roja45quotationspro.displaySuccessMsg([roja45quotationspro_deleted_success]);
                    window.location = data.redirect;
                } else {
                    //roja45quotationspro.displayErrorMsg([roja45quotationspro_unknown_error]);
                }
            },
            error: function (data) {
                //roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            complete: function (data) {
                //roja45quotationspro.toggleWaitDialog();
            }
        });
    });
});
