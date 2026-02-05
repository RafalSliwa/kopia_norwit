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

$(document).ready(function() {
    $('#customerquotes_block_account .ajax-view-quote').off('click').on('click', function(e) {
        e.preventDefault();
        getQuoteDetails($(this));
    });
    $('.close-button').off('click').on('click', function(e) {
        e.preventDefault();
        $('#view_quotation_details').slideUp('normal', function(e) {
            $('#view_quotation_details #quotation_details').empty();
        });
    });

    $('#customerquotes_block_account .ajax-add-to-cart').off('click').on('click', function(e) {
        toggleWaitDialog();
    });
});

function getQuoteDetails(target) {
    var id_roja45_quotation = target.closest('tr').attr('data-id');
    var url = target.attr('href')+'&id_roja45_quotation='+ id_roja45_quotation;

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        beforeSend: function() {
            toggleWaitDialog();
        },
        success: function(data) {
            if (data.result == 'success') {
                displaySuccessMsg(data.message);
                $('#view_quotation_details #quotation_details').empty();
                $('#view_quotation_details #quotation_details').html(data.view);
                $('#view_quotation_details').slideDown();
            } else if (data.result == 'error') {
                displayErrorMsg(roja45quotationspro_sent_failed);
            } else {
                displayErrorMsg(roja45_quotationspro_unknown_error);
            }
        },
        error: function(data) {
            displayErrorMsg(roja45quotationspro_sent_failed);
        },
        complete: function(data) {
            toggleWaitDialog();
        }
    });
}

function addToCart(target) {
    var id_roja45_quotation = target.closest('tr').attr('data-id');
    var id_cart= target.closest('tr').attr('data-id');
    var url = target.attr('href')+'&id_roja45_quotation='+ id_roja45_quotation;

    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        beforeSend: function() {
            toggleWaitDialog();
        },
        success: function(data) {
            if (data.result == 'success') {
                //ajaxCart.refresh();
                displaySuccessMsg(data.message);
                window.location = roja45_order_page_url;
            } else if (data.result == 'error') {
                displayErrorMsg(roja45quotationspro_sent_failed);
                toggleWaitDialog();
            } else {
                displayErrorMsg(roja45_quotationspro_unknown_error);
                toggleWaitDialog();
            }
        },
        error: function(data) {
            displayErrorMsg(roja45quotationspro_sent_failed);
            toggleWaitDialog();
        },
        complete: function(data) {
            //toggleWaitDialog();
        }
    });
}

function toggleWaitDialog() {
    if (typeof toggleRoja45GlobalWaitDialog === "function") {
        toggleRoja45GlobalWaitDialog();
    } else {
        if ( $('#customerquotes_block_account_modal').hasClass('invisible') ) {
            $('#customerquotes_block_account_modal').removeClass('invisible');
            $('#customerquotes_block_account_modal').fadeIn();
        } else {
            $('#customerquotes_block_account_modal').fadeOut();
            $('#customerquotes_block_account_modal').addClass('invisible');
        }
    }
}

function displaySuccessMsg( msg ) {
    $.growl.notice({
        duration: 3000,
        location: 'immersive',
        title: 'Success',
        message: msg
    });
}
function displayWarningMsg( msg ) {
    $.growl.warning({
        duration: 6000,
        location: 'immersive',
        title: 'Warning',
        message: msg
    });
}
function displayErrorMsg( msg ) {
    $.growl.error({
        duration: 10000,
        location: "immersive",
        title: 'Error',
        message: msg
    });
}