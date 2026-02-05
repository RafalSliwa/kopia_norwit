/**
 * roja45moduleadmin.js.
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
    $('input[name=ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA]').on('change', function() {
        if ($("input[name='ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA']:checked").val() == '1') {
            $('.recaptcha_hidden').fadeIn();
        } else {
            $('.recaptcha_hidden').fadeOut();
        }
    }).trigger('change');

    $('input[name=ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE]').on('change', function() {
        if ($("input[name='ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE']:checked").val() == '1') {
            $('.new_addresses_hidden').fadeIn();
        } else {
            $('.new_addresses_hidden').fadeOut();
        }
    }).trigger('change');

    $('input[name=ROJA45_QUOTATIONSPRO_DISPLAY_LABEL]').on('change', function() {
        if ($("input[name='ROJA45_QUOTATIONSPRO_DISPLAY_LABEL']:checked").val() == '1') {
            $('.label_position_hidden').fadeIn();
        } else {
            $('.label_position_hidden').fadeOut();
        }
    }).trigger('change');

    $('input[name=ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE]').on('change', function() {
        if ($("input[name='ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE']:checked").val() == '1') {
            $('.customreference_hidden').fadeIn();
        } else {
            $('.customreference_hidden').fadeOut();
        }
    }).trigger('change');
});

var roja45quotationspro = (function (my) {
    my.registerModule = function (account_email, account_order, account_domain, account_key)
    {
        $.ajax({
            url: $('#ROJA45_QUOTATIONSPRO_REGISTRATION_URL').val(),
            type: 'post',
            dataType: 'json',
            data: {
                'ajax' : 1,
                'action' : 'submitRegisterModule',
                'account_email' : account_email,
                'account_order' : account_order,
                'account_domain' : account_domain,
                'account_key' : account_key,
            },
            beforeSend: function() {
                roja45quotationspro.toggleModal();
            },
            success: function(data) {
                if (data.result) {
                    window.location = data.redirect;
                } else {
                    roja45quotationspro.displayErrorMsg(data.errors);
                    roja45quotationspro.toggleModal();
                }
            },
            error: function(data) {
                roja45quotationspro.displayErrorMsg(data);
                roja45quotationspro.toggleModal();
            },
            complete: function(data) {

            }
        });
    };

    /*
    my.moduleRegistered = function (account_key)
    {
        $.ajax({
            url: $('#ROJA45_QUOTATIONSPRO_REGISTRATION_URL').val(),
            type: 'post',
            dataType: 'json',
            data: {
                'ajax' : 1,
                'action' : 'submitModuleRegistered',
                'account_key' : account_key
            },
            beforeSend: function() {
                roja45quotationspro.toggleModal();
            },
            success: function(data) {
                if (data.result) {
                    $('#roja45quotationspro_form .tab-content').fadeOut('fast', function() {
                        $('#roja45quotationspro_registration_tab').remove();
                        $(this).append(data.view);
                        $(this).fadeIn();
                    });
                } else {
                    roja45quotationspro.displayErrorMsg(data.errors);
                }
            },
            error: function(data) {
                roja45quotationspro.displayErrorMsg(data);
            },
            complete: function(data) {
                roja45quotationspro.toggleModal();
            }
        });
    };*/

    my.toggleModal = function ()
    {
        $('#roja45_quotation_modal_dialog').toggle();
    };

    my.displaySuccessMsg = function (msg)
    {
        $.growl.notice({
            duration: 3000,
            location: 'immersive',
            title: 'Success',
            message: msg
        });
    };

    my.displayWarningMsg = function (msgs)
    {
        $.each(msgs, function(index, value) {
            $.growl.warning({
                duration: 6000,
                location: 'immersive',
                title: 'Warning',
                message: value
            });
        });
    };

    my.displayErrorMsg = function (msgs) {
        $.each(msgs, function(index, value) {
            $.growl.error({
                duration: 10000,
                location: "immersive",
                title: 'Error',
                message: value
            });
        });
    };

    return my;
}(roja45quotationspro || {}));

