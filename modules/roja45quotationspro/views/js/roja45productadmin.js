/**
 * roja45productadmin.js.
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

    if (typeof tabs_manager !== 'undefined') {
        // variable is undefined
        tabs_manager.product_tabs['ModuleRoja45quotations'] = new function () {
            this.onReady = function () {
                $('#submitProductConfig').click(function (e) {
                    e.preventDefault();
                    roja45quotationspro.saveForm($(this));
                });
            }
        }
    }
});

roja45quotationspro = {
    saveForm : function ( ele ) {

        var href = $(ele).attr('href');
        $.ajax({
            url: href,
            type: 'post',
            dataType: 'json',
            data: $('#roja45quotations_form').serialize(),
            beforeSend: function() {
                roja45global.toggleModal();
            },
            success: function(json) {
                if (json.result == 'success') {
                    roja45global.displaySuccessMsg(json.response);
                } else if (json.result == 'error') {
                    $.each(json.errors, function(index, value) {
                        roja45global.displayErrorMsg(value);
                    });
                } else {
                    roja45global.displayErrorMsg('An unknown error occurred.');
                }
            },
            error: function(data) {
                roja45global.displayErrorMsg(data);
            },
            complete: function(data) {
                roja45global.toggleModal();
            }
        });
    }
}
