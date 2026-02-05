$(document).ready(function() {
    $('#bulk_action_menu_roja45_quotationspro ~ .dropdown-menu a').each(function() {
        var $this = $(this);
        var action = $this.attr('onclick');
        if (action && action.includes('submitBulkupdateStatusroja45_quotationspro')) {
            $this.removeAttr('onclick');

            $this.click(function(e) {
                e.preventDefault();

                $('#quotationspro_update_status_alert_dialog').show();
                $('.quotationspro_request_dialog_overlay').addClass('roja45quotationspro-darken-background').css({
                    'width': '100%',
                    'height': '100%',
                    'display': 'block'
                });

                $('#cancelButton').click(function() {
                    $('#quotationspro_update_status_alert_dialog').hide();
                    $('.quotationspro_request_dialog_overlay').removeClass('roja45quotationspro-darken-background').css({
                        'width': '0',
                        'height': '0',
                        'display': 'none'
                    });
                });

                $('#confirmButton').click(function() {
                    var selectedStatus = $('#quotation_status').val();
                    $(this).attr('disabled', 'disabled');
                    /*$('#quotationspro_update_status_alert_dialog').hide();
                    $('.quotationspro_request_dialog_overlay').removeClass('roja45quotationspro-darken-background').css({
                        'width': '0',
                        'height': '0',
                        'display': 'none'
                    });*/

                    var form = $('#bulk_action_menu_roja45_quotationspro').closest('form');
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'selected_status',
                        value: selectedStatus
                    }).appendTo(form);

                    sendBulkAction(form.get(0), 'submitBulkupdateStatusroja45_quotationspro');
                });
            });
        }
    });
});
