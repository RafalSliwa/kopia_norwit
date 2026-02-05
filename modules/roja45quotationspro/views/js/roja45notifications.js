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
    $('body').on('click', function (e) {
        if (!$('#quotation_notification.dropdown').is(e.target)
            && $('#quotation_notification.dropdown').has(e.target).length === 0
            && $('.open').has(e.target).length === 0
        ) {
            $('#quotation_notification.dropdown').removeClass('open');
        }
    });

    if (typeof roja45quotationspro_expiry_enable !== 'undefined' && roja45quotationspro_expiry_enable) {
        $('table.roja45_quotationspro td.expiry_date_hidden').each(function() {
            let expiry_date_str = $(this).html().trim();
            let expiry_date = new Date(expiry_date_str);

            let diff = roja45quotationspro_notifications.date_diff(Date.now(), expiry_date);
            if (diff < roja45quotationspro_expiry_alert) {
                $(this).closest('tr').find('td').addClass('roja45quotationspro_expiry_alert');
            } else if (diff < roja45quotationspro_expiry_warning) {
                $(this).closest('tr').find('td').addClass('roja45quotationspro_expiry_warning');
            }
        });
        $('input[name=id_roja45_quotationFilter_expiry_date_hidden]').parent().remove();
    }

    if (typeof roja45_header_location !== 'undefined') {
        roja45quotationspro_notifications.initBackOfficeNotifications();
    }
});

var roja45quotationspro_notifications = {
    initBackOfficeNotifications: function () {
        $.ajax({
            type: "POST",
            url: roja45quotationspro_controller,
            data: {
                'action' : 'updateNotifications',
                'ajax' : 1
            },
            dataType: 'json',
            beforeSend: function() {
                $('#quotation-notifications .notification-elements').empty();
            },
            success: function(data) {
                if (data.result == 1) {
                    $('#quotation_notification').appendTo(roja45_header_location);
                    $('#quotation-notifications .notification-elements').append(data.quotations);
                    $('#total_quotation_notif_value').html(data.num_notifications);
                    if (data.num_notifications > 0) {
                        $('#total_quotation_notif_value').parent().removeClass('hide');
                    } else {
                        $('#total_quotation_notif_value').parent().addClass('hide');
                    }
                } else {
                    //roja45global.displayErrorMsg(json.response);
                }
            },
            error: function(data) {
                //roja45global.displayErrorMsg(data);
            },
            complete: function(data) {
            }
        });
    },

    date_diff: function(date1, date2) {
        dt1 = new Date(date1);
        dt2 = new Date(date2);
        return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
    }
}
