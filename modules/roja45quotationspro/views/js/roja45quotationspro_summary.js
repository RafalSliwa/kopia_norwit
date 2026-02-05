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
    $(document).on('click', '#submitRequest', function (e) {
        e.preventDefault();
        if (roja45quotationspro_summary.processSend()) {
            $('.btn.btn-default.request-quotation').prop('disabled', 'disabled').addClass('disabled');
            if(parseInt(roja45quotationspro_enable_captcha) && roja45quotationspro_enable_captchatype==1) {
                grecaptcha.execute();
            } else {
                roja45quotationspro_summary.submitForm();
            }
        }
    });

    $(document).on('click', '.quote_quantity .quote_quantity_up', function(e) {
        e.preventDefault();
        roja45quotationspro_summary.updateQty($(this), 'up');
    });

    $(document).on('click', '.quote_quantity .quote_quantity_down', function(e) {
        e.preventDefault();
        roja45quotationspro_summary.updateQty($(this), 'down');
    });

    $(document).on('change', '.quote_quantity .quote_quantity_input ', function(e) {
        e.preventDefault();
        roja45quotationspro_summary.updateQty($(this), 'update');
    });

    if(roja45quotationspro_enable_captcha) {
        if (roja45quotationspro_enable_captchatype==0) {
            onloadRecaptchaCallback = function() {
                if (typeof roja45_recaptcha_widgets !== "undefined") {
                    $.each(roja45_recaptcha_widgets, function (index, value) {
                        grecaptcha.render(value, {
                            'sitekey': roja45quotationspro_recaptcha_site_key,
                            'callback' : onRecaptchaSubmitCallback}
                        );
                    });
                }
            }
            onRecaptchaSubmitCallback = function(token) {
                $('.quote_navigation .request-quotation').removeClass("disabled");
            }
            var url = '//www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render=explicit';
            var element = document.createElement("script");
            element.src = url;
            document.body.appendChild(element);
        } else if (roja45quotationspro_enable_captchatype==1) {
            onRecaptchaInvisibleSubmitCallback = function(response) {
                roja45quotationspro_summary.submitForm();
            };
            var url = '//www.google.com/recaptcha/api.js';
            var element = document.createElement("script");
            element.src = url;
            document.body.appendChild(element);
        } else if (roja45quotationspro_enable_captchatype==2) {
            onloadRecaptchaCallback = function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute(roja45quotationspro_recaptcha_site_key, {action: 'homepage'}).then(function(token) {
                        $('#quotationspro_request_form').append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        $('.quote_navigation .request-quotation').removeClass("disabled");
                    });
                });
            };
            var url = '//www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render='+roja45quotationspro_recaptcha_site_key;
            var element = document.createElement("script");
            element.src = url;
            document.body.appendChild(element);
        }
    }
});

roja45quotationspro_summary = {
    processSend : function() {
        var url = $('#quotationspro_request_form').attr('action');
        var errors = 0;

        $('#quotationspro_request_form .form-field').each(function (index, value) {
            $(this).parent().removeClass('form-error').addClass('form-ok');
            if ($(this).hasClass('is_required') && $(this).val().length == 0) {
                $(this).parent().addClass('form-error').removeClass('form-ok');
                errors++;
            } else if (typeof $(this).attr('data-validate') !== "undefined" &&  $(this).attr('data-validate') !== "none") {
                if ($(this).val().length > 0) {
                    if ($(this).attr('name') == 'postcode' && typeof(countriesNeedZipCode[$('#id_country option:selected').val()]) !== "undefined") {
                        var result = window['validate_' + $(this).attr('data-validate')]($(this).val(), countriesNeedZipCode[$('#id_country option:selected').val()]);
                    } else if ($(this).attr('type') == 'checkbox') {
                        var result = window['validate_' + $(this).attr('data-validate')]($(this));
                    }  else if ($(this).data('validate') == 'isCustom') {
                        var result = validate_isCustom($(this).val(), new RegExp(decodeURIComponent($(this).data('custom-regex')), "i"));
                    } else {
                        var result = window['validate_' + $(this).attr('data-validate')]($(this).val());
                    }
                    if (!result) {
                        $(this).parent().addClass('form-error').removeClass('form-ok');
                        errors++;
                    }
                }
            }
        });

        if (errors == 0) {
            return true;
        } else {
            return false;
        }
    },

    submitForm: function () {
        var formData = new FormData();
        var fileInput = $('input[name=uploadedfile]');
        if (fileInput.length > 0) {
            var file = fileInput[0].files[0];
            formData.append('uploadedfile', file);
        }

        formData.append('ajax', 1);
        formData.append('action', 'SubmitInstantRequest');
        formData.append('ROJA45QUOTATIONSPRO_EMAIL', $('input[name=ROJA45QUOTATIONSPRO_EMAIL]').val());
        formData.append('ROJA45QUOTATIONSPRO_FIRSTNAME', $('input[name=ROJA45QUOTATIONSPRO_FIRSTNAME]').val());
        formData.append('ROJA45QUOTATIONSPRO_LASTNAME', $('input[name=ROJA45QUOTATIONSPRO_LASTNAME]').val());
        formData.append('ROJA45QUOTATIONSPRO_CUSTOMER_COPY', $('input[name=ROJA45QUOTATIONSPRO_CUSTOMER_COPY]').val());

        var request = {};
        request.columns = [];
        $('#quotationspro_request_form .quotationspro_request.column').each(function (i) {
            var column = {};
            var heading = $(this).find('.page-subheading').html();
            column.heading = heading;
            // column id
            var col = $(this).data('column');
            column.num = col;
            column.fields = [];

            $(this).find('.form-field').each(function (j) {
                var label = $(this).closest('.form-group').find('.control-label').html();
                var field = {};
                field.pos = j;
                field.name = $(this).attr('name');
                field.type = $(this).attr('data-field-type');
                field.label = label.trim();
                if (field.type == 'SWITCH') {
                    field.value = $('input[name='+field.name+']:checked').val();
                } else {
                    field.value = $(this).val();
                }
                column.fields[j] = field;
            });
            request.columns[i] = column;
        });
        formData.append('ROJA45QUOTATIONSPRO_FORMDATA', JSON.stringify(request));

        $('#submitRequest').attr('disabled', 'disabled');
        if (roja45quotationspro_instantresponse==1) {
            if (roja45quotationspro_enable_captcha==1) {
                $.ajax({
                    url: $('#quotationspro_request_form').attr('action'),
                    type: 'post',
                    data : {
                        'ajax' : 1,
                        'action' : 'ValidateRecaptcha',
                        'g-recaptcha-response' : $('input[name=g-recaptcha-response]').val(),
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.result) {
                            roja45quotationspro_summary.sendRequest(formData);
                        } else {
                            $.each(data.errors, function (index, value) {
                                roja45quotationspro.displayErrorMsg(value);
                            });
                        }
                    },
                    error: function (data) {
                        roja45quotationspro.displayErrorMsg(roja45quotationspro_sent_failed);
                        return 0;
                    },
                    complete: function() {

                    }
                });
            } else {
                roja45quotationspro_summary.sendRequest(formData);
            }
        } else {
            $('input[name=ROJA45QUOTATIONSPRO_FORMDATA]').val(JSON.stringify(request));
            $('#quotationspro_request_form').submit();
        }
    },

    sendRequest : function(formData) {
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: $('#quotationspro_request_form').attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
            },
            error: function (data) {
                roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            success: function (data) {
                if (data.result) {
                }
            },
            complete: function() {
            }
        });
        $('#quotationspro_request_form').fadeOut('fast', function(e) {
            $('#quotationspro_request_form').remove();
            $('.title_block.quote_title').hide();
            $('.title_block.received_title').show();
            $('#quotationspro_request_container').fadeIn();
        });
    },

    updateButtons: function (id_product) {
        var url = $('.request-quote').attr('href');
        $.ajax({
            url: url + '?submitUpdateSummaryButtons=1&id_product='+id_product+'&ajax=1',
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.result == 'success') {
                    roja45quotationspro.displaySuccessMsg(data.response);
                    if (data.enable == 0) {
                        $('.standard-checkout').show();
                        $('.request-quote').hide();
                    }
                } else if (data.result == 'error') {
                    $.each(data.errors, function (index, value) {
                        roja45quotationspro.displayErrorMsg(value);
                    });
                }
            },
            error: function (data) {
                roja45quotationspro.displayErrorMsg(roja45quotationspro_sent_failed);
            },
            complete: function (data) {
            }
        });
    },

    updateQty: function (ele, mode) {
        //var url = $(ele).closest('.quote_quantity').attr('data-url');
        var qty = parseInt($(ele).closest('.quote_quantity').find('.quote_quantity_input').val());
        if (mode == 'up') {
            qty++;
        } else if (mode == 'down') {
            qty--;
        }
        $(ele).closest('.quote_quantity').find('.quote_quantity_input').val(qty)
        $('#submitRequest').attr('disabled', 'disabled');
        //if (qty != 0) {
            $.ajax({
                url: roja45_quoationspro_controller,
                type: 'post',
                dataType: 'json',
                data : {
                    'ajax' : 1,
                    'action' : 'submitQuantity',
                    'id_product' : parseInt($(ele).closest('.quote_quantity').attr('data-id-product')),
                    'id_product_attribute' : parseInt($(ele).closest('.quote_quantity').attr('data-id-product-attribute')),
                    'quantity' : parseInt($(ele).closest('.quote_quantity').find('.quote_quantity_input').val())
                },
                success: function (data) {
                    if (data.result == 'success') {
                        //roja45quotationspro.displaySuccessMsg(data.response);
                    } else if (data.result == 'error') {
                        roja45quotationspro.displayErrorMsg(data.errors);
                    }
                },
                error: function (data) {
                    roja45quotationspro.displayErrorMsg(roja45quotationspro_sent_failed);
                },
                complete: function (data) {
                    $('#submitRequest').removeAttr('disabled');
                }
            });

            //window.location = url + '&quantity='+qty+'&mode='+mode;
        //}
    },
}


