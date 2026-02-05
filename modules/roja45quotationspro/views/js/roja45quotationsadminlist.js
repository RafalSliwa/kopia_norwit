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
    $(document).on('click', '.btn.btn-assign-user', function (e) {
        e.preventDefault();
        $(this).closest('.btn-group').find('.quotationspro_assign_user_dialog').show();
    });
    $(document).on('click', '#quotationspro_assign_user_dialog .btn-close', function (e) {
        e.preventDefault();
        $(this).closest('#quotationspro_assign_user_dialog').hide();
    });
    $(document).on('click', '#quotationspro_assign_user_dialog .btn-assign', function (e) {
        e.preventDefault();
        var id_roja45_quotation = $(this).attr("data-quotation-id");
        var id_employee = $(this).val();
        $.ajax({
            type:"POST",
            url : $('#quotationspro_assign_user_dialog').attr('data-controller'),
            async: true,
            dataType: "json",
            data : {
                'ajax': "1",
                'action': 'submitAssignUserToQuotation',
                'id_roja45_quotation' : id_roja45_quotation,
                'id_employee' : id_employee
            },
            beforeSend : function() {
                $('#roja45_quotation_modal_overlay').show();
            },
            success : function(data) {
                if (data.result) {
                    window.location = data.redirect;
                } else {
                    // roja45quotationspro.displayErrorMsg(data.errors);
                    $('#roja45_quotation_modal_overlay').hide();
                }
            },
            error : function(data) {
                //roja45quotationspro.displayErrorMsg(roja45_quotationspro_error_unexpected);
                $('#quotationspro_addproduct_modal').modal('toggle');
                $('#roja45_quotation_modal_overlay').hide();
            },
            complete: function() {
                $('#quotationspro_addproduct_form').find('input[name^="selected_product_ids"]').remove();
                $('#quotationspro_addproduct_form').find('input.add-product-checkbox').prop('checked', false);
            }
        });
    });
});