{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<style>
    .icon-AdminParentAdminQuotationsPro:before {
        content: "\f0ca";
    }
</style>

{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}


<script type="text/javascript">
    let roja45_header_location = "#customer_messages_notif";
    let roja45_bg_color = "#DF0067";
    let roja45_txt_color = "#ffffff";
    let roja45quotationspro_expiry_enable = parseInt({$roja45quotationspro_expiry_enable});
    let roja45quotationspro_expiry_warning = parseInt({$roja45quotationspro_expiry_warning});
    let roja45quotationspro_expiry_alert = parseInt({$roja45quotationspro_expiry_alert});
    function loadQuotationNotification(adminController) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: adminController,
            data: {
                ajax: 1,
                action: 'getBrowserNotifications',
            },

            success: function(data) {
                if (data.result) {
                    if (data.num_notifications) {
                        window.favicon.badge(data.num_notifications);
                    }
                } else {
                    $.growl.error({
                        duration: 10000,
                        location: "immersive",
                        title: 'Error',
                        message: data.error
                    });
                }
            },
            error: function(err) {
            }
        });
    }

    $(document).ready(function() {
        if (!window.favicon && typeof Favico !== "undefined") {
            window.favicon = new Favico({
                animation: 'popFade',
                bgColor: roja45_bg_color,
                textColor: roja45_txt_color
            });
        }

        loadQuotationNotification(roja45quotationspro_controller);
        setInterval(function() {
            loadQuotationNotification(roja45quotationspro_controller);
        }, 60000);
    });
</script>
