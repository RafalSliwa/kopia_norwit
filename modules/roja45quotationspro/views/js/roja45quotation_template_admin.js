/**
 * roja45quotationsadmin.js.
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
    $(document).on('click', '.createQuote', function (e) {
        e.preventDefault();
        var target = $(this);
        $.confirm({
            text: roja45quotationspro_txt_createquote,
            title: roja45quotationspro_txt_confirm,
            confirm: function () {
                roja45quotationspro.createQuote(target);
            },
            cancel: function () {
                // nothing to do
            },
            confirmButton: roja45quotationspro_confirmbutton,
            cancelButton: roja45quotationspro_cancelbutton,
            post: true,
            confirmButtonClass: "btn-danger",
            cancelButtonClass: "btn-default",
            dialogClass: "bootstrap modal-dialog" // Bootstrap classes for large modal
        });
    });

});

roja45quotationspro = {
    createQuote : function(ele)
    {
        roja45quotationspro.toggleModal();
        $('#roja45quotationspro_form').submit();
    },

    toggleModal : function ()
    {
        $('#roja45_quotation_modal_dialog').toggle();
    },

    toggleSavingIndicator : function ()
    {
        $('.disabled-while-saving').prop('disabled', function(i, v) { return !v; });
        $('#roja45quotation_form .badge.saving-indicator').fadeToggle('fast');
    },

    displaySuccessMsg : function (msg)
    {
        $.growl.notice({
            duration: 3000,
            location: 'immersive',
            title: 'Success',
            message: msg
        });
    },

    displayWarningMsg : function (msg)
    {
        $.growl.warning({
            duration: 6000,
            location: 'immersive',
            title: 'Warning',
            message: msg
        });
    },

    displayErrorMsg : function (msg) {
        $.growl.error({
            duration: 10000,
            location: "immersive",
            title: 'Error',
            message: msg
        });
    }
}
