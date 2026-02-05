/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var wpcfu = {
    "apiSettings": {
        "root": "http:\/\/web.prestahero.com\/chung\/wp494\/wp-json\/contact-form-7\/v1",
        "namespace": "contact-form-7\/v1"
    }, "recaptcha": {"messages": {"empty": "Please verify that you are not a robot."}}
};
(function ($) {
    'use strict';
    if (typeof wpcfu === 'undefined' || wpcfu === null) {
        return;
    }

    wpcfu = $.extend({
        cached: 0,
        inputs: []
    }, wpcfu);

    $(function () {
        wpcfu.supportHtml5 = (function () {
            var features = {};
            var input = document.createElement('input');

            features.placeholder = 'placeholder' in input;

            var inputTypes = ['email', 'url', 'tel', 'number', 'range', 'date'];

            $.each(inputTypes, function (index, value) {
                input.setAttribute('type', value);
                features[value] = input.type !== 'text';
            });
            return features;
        })();

        $('div.wpcfu > form').each(function () {
            var $form = $(this);
            wpcfu.initForm($form);

            if (wpcfu.cached) {
                wpcfu.refill($form);
            }
        });
    });

    wpcfu.getId = function (form) {
        return parseInt($('input[name="_wpcfu"]', form).val(), 10);
    };

    wpcfu.initForm = function (form) {
        var $form = $(form);
        $form.submit(function (event) {
            if (typeof window.FormData !== 'function') {
                return;
            }
            wpcfu.submit($form);
            event.preventDefault();
        });
        if ($form.find('.ajax-loader').length <= 0)
            $form.find('.wpcfu-submit').after('<span class="ajax-loader"></span>');
        wpcfu.toggleSubmit($form);

        $form.on('click', '.wpcfu-acceptance', function () {
            wpcfu.toggleSubmit($form);
        });

        // Exclusive Checkbox
        $('.wpcfu-exclusive-checkbox', $form).on('click', 'input:checkbox', function () {
            var name = $(this).attr('name');
            $form.find('input:checkbox[name="' + name + '"]').not(this).prop('checked', false);
        });

        // Free Text Option for Checkboxes and Radio Buttons
        $('.wpcfu-list-item.has-free-text', $form).each(function () {
            var $freetext = $(':input.wpcfu-free-text', this);
            var $wrap = $(this).closest('.wpcfu-form-control');
            if ($(':checkbox, :radio', this).is(':checked')) {
                $freetext.prop('disabled', false);
            } else {
                $freetext.prop('disabled', true);
            }
            $wrap.on('change', ':checkbox, :radio', function () {
                var $cb = $('.has-free-text', $wrap).find(':checkbox, :radio');
                if ($cb.is(':checked')) {
                    $freetext.prop('disabled', false).focus();
                } else {
                    $freetext.prop('disabled', true);
                }
            });
        });

        // Placeholder Fallback
        if (!wpcfu.supportHtml5.placeholder) {
            $('[placeholder]', $form).each(function () {
                $(this).val($(this).attr('placeholder'));
                $(this).addClass('placeheld');

                $(this).focus(function () {
                    if ($(this).hasClass('placeheld')) {
                        $(this).val('').removeClass('placeheld');
                    }
                });

                $(this).blur(function () {
                    if ('' === $(this).val()) {
                        $(this).val($(this).attr('placeholder'));
                        $(this).addClass('placeheld');
                    }
                });
            });
        }

        if (wpcfu.jqueryUi && !wpcfu.supportHtml5.date) {
            $form.find('input.wpcfu-date[type="date"]').each(function () {
                $(this).datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: new Date($(this).attr('min')),
                    maxDate: new Date($(this).attr('max')),
                    changeMonth: true,
                    changeYear: true,
                });
            });
        }

        if (wpcfu.jqueryUi && !wpcfu.supportHtml5.number) {
            $form.find('input.wpcfu-number[type="number"]').each(function () {
                $(this).spinner({
                    min: $(this).attr('min'),
                    max: $(this).attr('max'),
                    step: $(this).attr('step')
                });
            });
        }

        // Character Count
        $('.wpcfu-character-count', $form).each(function () {
            var $count = $(this);
            var name = $count.attr('data-target-name');
            var down = $count.hasClass('down');
            var starting = parseInt($count.attr('data-starting-value'), 10);
            var maximum = parseInt($count.attr('data-maximum-value'), 10);
            var minimum = parseInt($count.attr('data-minimum-value'), 10);

            var updateCount = function (target) {
                var $target = $(target);
                var length = $target.val().length;
                var count = down ? starting - length : length;
                $count.attr('data-current-value', count);
                $count.text(count);

                if (maximum && maximum < length) {
                    $count.addClass('too-long');
                } else {
                    $count.removeClass('too-long');
                }

                if (minimum && length < minimum) {
                    $count.addClass('too-short');
                } else {
                    $count.removeClass('too-short');
                }
            };

            $(':input[name="' + name + '"]', $form).each(function () {
                updateCount(this);

                $(this).keyup(function () {
                    updateCount(this);
                });
            });
        });

        // URL Input Correction
        $form.on('change', '.wpcfu-validates-as-url', function () {
            var val = $.trim($(this).val());

            if (val
                && !val.match(/^[a-z][a-z0-9.+-]*:/i)
                && -1 !== val.indexOf('.')) {
                val = val.replace(/^\/+/, '');
                val = 'http://' + val;
            }

            $(this).val(val);
        });
    };

    wpcfu.grecaptchaReset = function () {

    }

    wpcfu.submit = function (form) {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.editors.length > 0 && $('.autoload_rte_cfu').length) {
            tinyMCE.triggerSave();
        }
        if (typeof window.FormData !== 'function') {
            return;
        }
        var $form = $(form);
        if ($form.hasClass('is-active'))
            return false;
        $('.ajax-loader', $form).addClass('is-active');
        $form.addClass('is-active');
        $('[placeholder].placeheld', $form).each(function (i, n) {
            $(n).val('');
        });

        wpcfu.clearResponse($form);

        var formData = new FormData($form.get(0));

        var detail = {
            id: $form.closest('div.wpcfu').attr('id'),
            status: 'init',
            inputs: [],
            formData: formData
        };

        $.each($form.serializeArray(), function (i, field) {
            if ('_wpcfu' == field.name) {
                detail.contactFormId = field.value;
            } else if ('_ets_cfu_version' == field.name) {
                detail.pluginVersion = field.value;
            } else if ('_ets_cfu_locale' == field.name) {
                detail.contactFormLocale = field.value;
            } else if ('_ets_cfu_unit_tag' == field.name) {
                detail.unitTag = field.value;
            } else if ('_ets_cfu_container_post' == field.name) {
                detail.containerPostId = field.value;
            } else if (field.name.match(/^_ets_cfu_\w+_free_text_/)) {
                var owner = field.name.replace(/^_ets_cfu_\w+_free_text_/, '');
                detail.inputs.push({
                    name: owner + '-free-text',
                    value: field.value
                });
            } else if (field.name.match(/^_/)) {
                // do nothing
            } else {
                detail.inputs.push(field);
            }
        });
        wpcfu.triggerEvent($form.closest('div.wpcfu'), 'beforesubmit', detail);
        var ajaxSuccess = function (data, status, xhr, $form) {

            detail.id = $(data.into).attr('id');
            detail.status = data.status;
            detail.apiResponse = data;
            var $message = $('.wpcfu-response-output', $form);

            $message.removeClass('alert-warning');
            $message.removeClass('alert-success');
            if (data.status && typeof grecaptcha !== "undefined" && $form.find(':input[name=g-recaptcha-response]').length > 0) {
                if (!ets_cfu_recaptcha_v3) {
                    grecaptcha.reset();
                }
            }
            switch (data.status) {
                case 'validation_failed':
                    $.each(data.invalidFields, function (i, n) {
                        $(n.into, $form).each(function () {
                            wpcfu.notValidTip(this, n.message);
                            $('.wpcfu-form-control', this).addClass('wpcfu-not-valid');
                            $('[aria-invalid]', this).attr('aria-invalid', 'true');
                        });
                    });
                    $message.addClass('alert alert-warning');
                    $form.addClass('invalid');
                    wpcfu.triggerEvent(data.into, 'invalid', detail);
                    break;
                case 'acceptance_missing':
                    $message.addClass('alert alert-warning');
                    $form.addClass('unaccepted');
                    wpcfu.triggerEvent(data.into, 'unaccepted', detail);
                    break;
                case 'spam':
                    $message.addClass('alert alert-warning');
                    $form.addClass('spam');
                    $('[name="g-recaptcha-response"]', $form).each(function () {
                        if ('' === $(this).val()) {
                            var $recaptcha = $(this).closest('.wpcfu-form-control-wrap');
                            wpcfu.notValidTip($recaptcha, wpcfu.recaptcha.messages.empty);
                        }
                    });
                    wpcfu.triggerEvent(data.into, 'spam', detail);
                    break;
                case 'aborted':
                    $message.addClass('alert alert-warning');
                    $form.addClass('aborted');

                    wpcfu.triggerEvent(data.into, 'aborted', detail);
                    break;
                case 'mail_sent':
                    $message.addClass('alert alert-success');
                    $form.addClass('sent');
                    wpcfu.triggerEvent(data.into, 'mailsent', detail);
                    break;
                case 'mail_failed':
                    $message.addClass('alert alert-warning');
                    $form.addClass('failed');
                    wpcfu.triggerEvent(data.into, 'mailfailed', detail);
                    break;
                case 'mail_redirect':
                    $message.hide().remove();
                    window.location.href = data.message;
                    break;
                case 'load_thank_page':
                    $message.hide().remove();
                    window.location.href = data.message;
                    break;
                case 'not_found':
                    $message.addClass('alert alert-danger');
                    $form.addClass('failed');
                    break;
                default:
                    var customStatusClass = 'custom-' + data.status.replace(/[^0-9a-z]+/i, '-');
                    $message.addClass('wpcfu-' + customStatusClass);
                    $form.addClass(customStatusClass);
            }

            wpcfu.refill($form, data);

            wpcfu.triggerEvent(data.into, 'submit', detail);

            if ('mail_sent' === data.status) {
                $form.each(function () {
                    this.reset();
                });
            }

            $form.find('[placeholder].placeheld').each(function (i, n) {
                $(n).val($(n).attr('placeholder'));
            });

            $message.html('').append(data.message).slideDown('fast');
            $message.attr('role', 'alert');

            $('.screen-reader-response', $form.closest('.wpcfu')).each(function () {
                var $response = $(this);
                $response.html('').attr('role', '').append(data.message);

                if (data.invalidFields) {
                    var $invalids = $('<ul></ul>');

                    $.each(data.invalidFields, function (i, n) {
                        if (n.idref) {
                            var $li = $('<li></li>').append($('<a></a>').attr('href', '#' + n.idref).append(n.message));
                        } else {
                            var $li = $('<li></li>').append(n.message);
                        }

                        $invalids.append($li);
                    });

                    $response.append($invalids);
                }

                $response.attr('role', 'alert').focus();
            });
        };
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false
        }).done(function (data, status, xhr) {
            ajaxSuccess(data, status, xhr, $form);
            $('.ajax-loader', $form).removeClass('is-active');
            $form.removeClass('is-active');
            if ($form.find('.pa-captcha-refesh').length)
                refeshImage($form.find('.pa-captcha-refesh'));
        }).fail(function (xhr, status, error) {
            $form.find('.wpcfu-response-output').removeClass('alert-warning');
            $form.find('.wpcfu-response-output').removeClass('alert-success');
            $form.find('.wpcfu-response-output').html('There was a technical error when submitting the form. Please contact webmaster for more information');
            $form.find('.wpcfu-response-output').addClass('alert alert-warning');
            $form.find('.wpcfu-response-output').addClass('invalid').show();
            $form.find('.ajax-loader').removeClass('is-active');
            $form.removeClass('is-active');
            if ($form.find('.pa-captcha-refesh'))
                refeshImage($form.find('.pa-captcha-refesh'));
        });
    };
    wpcfu.triggerEvent = function (target, name, detail) {
        var $target = $(target);

        /* DOM event */
        var event = new CustomEvent('wpcfu' + name, {
            bubbles: true,
            detail: detail
        });
        /* jQuery event */
        $target.trigger('wpcfu:' + name, detail);
        $target.trigger(name + '.wpcfu', detail); // deprecated
    };

    wpcfu.toggleSubmit = function (form, state) {
        var $form = $(form);
        var $submit = $('input:submit', $form);

        if (typeof state !== 'undefined') {
            $submit.prop('disabled', !state);
            return;
        }

        if ($form.hasClass('wpcfu-acceptance-as-validation')) {
            return;
        }
        $submit.prop('disabled', false);

        $('.wpcfu-acceptance', $form).each(function () {
            var $span = $(this);
            var $input = $('input:checkbox', $span);

            if (!$span.hasClass('optional')) {
                if ($span.hasClass('invert') && $input.is(':checked')
                    || !$span.hasClass('invert') && !$input.is(':checked')) {
                    $submit.prop('disabled', true);
                    return false;
                }
            }
        });
    };

    wpcfu.notValidTip = function (target, message) {
        var $target = $(target);
        $('.wpcfu-not-valid-tip', $target).remove();
        $('<span role="alert" class="wpcfu-not-valid-tip"></span>')
            .text(message).appendTo($target);

        if ($target.is('.use-floating-validation-tip *')) {
            var fadeOut = function (target) {
                $(target).not(':hidden').animate({
                    opacity: 0
                }, 'fast', function () {
                    $(this).css({'z-index': -100});
                });
            };

            $target.on('mouseover', '.wpcfu-not-valid-tip', function () {
                fadeOut(this);
            });

            $target.on('focus', ':input', function () {
                fadeOut($('.wpcfu-not-valid-tip', $target));
            });
        }
    };

    wpcfu.refill = function (form, data) {
        var $form = $(form);

        var refillCaptcha = function ($form, items) {
            $.each(items, function (i, n) {
                $form.find(':input[name="' + i + '"]').val('');
                $form.find('img.wpcfu-captcha-' + i).attr('src', n);
                var match = /([0-9]+)\.(png|gif|jpeg)$/.exec(n);
                $form.find('input:hidden[name="_ets_cfu_captcha_challenge_' + i + '"]').attr('value', match[1]);
            });
        };

        var refillQuiz = function ($form, items) {
            $.each(items, function (i, n) {
                $form.find(':input[name="' + i + '"]').val('');
                $form.find(':input[name="' + i + '"]').siblings('span.wpcfu-quiz-label').text(n[0]);
                $form.find('input:hidden[name="_ets_cfu_quiz_answer_' + i + '"]').attr('value', n[1]);
            });
        };

        if (typeof data === 'undefined') {
            $.ajax({
                type: 'GET',
                url: wpcfu.apiSettings.getRoute(
                    '/contact-forms/' + wpcfu.getId($form) + '/refill'),
                beforeSend: function (xhr) {
                    var nonce = $form.find(':input[name="_wpnonce"]').val();

                    if (nonce) {
                        xhr.setRequestHeader('X-WP-Nonce', nonce);
                    }
                },
                dataType: 'json'
            }).done(function (data, status, xhr) {
                if (data.captcha) {
                    refillCaptcha($form, data.captcha);
                }

                if (data.quiz) {
                    refillQuiz($form, data.quiz);
                }
            });

        } else {
            if (data.captcha) {
                refillCaptcha($form, data.captcha);
            }

            if (data.quiz) {
                refillQuiz($form, data.quiz);
            }
        }
    };

    wpcfu.clearResponse = function (form) {
        var $form = $(form);
        $form.removeClass('invalid spam sent failed');
        $form.siblings('.screen-reader-response').html('').attr('role', '');

        $('.wpcfu-not-valid-tip', $form).remove();
        $('[aria-invalid]', $form).attr('aria-invalid', 'false');
        $('.wpcfu-form-control', $form).removeClass('wpcfu-not-valid');

        $('.wpcfu-response-output', $form)
            .hide().empty().removeAttr('role')
            .removeClass('wpcfu-mail-sent-ok wpcfu-mail-sent-ng wpcfu-validation-errors wpcfu-spam-blocked');
    };

    wpcfu.apiSettings.getRoute = function (path) {
        var url = wpcfu.apiSettings.root;

        url = url.replace(
            wpcfu.apiSettings.namespace,
            wpcfu.apiSettings.namespace + path);

        return url;
    };

})(jQuery);



$(document ).ajaxComplete(function( event, xhr, settings ) {
    var data_post = settings.data;
    if (xhr.responseText && xhr.responseText.indexOf('quickview_html')) {

        $('div.wpcfu > form').each(function () {
            var $form = $(this);
            wpcfu.initForm($form);

            if (wpcfu.cached) {
                wpcfu.refill($form);
            }
        });
    }
    // if(typeof data_post!= 'object' && data_post )
    // {
    //     if(data_post.indexOf('addDiscount')>=0 && data_post.indexOf('discount_name')>=0 )
    //         $('.loading').removeClass('loading');
    // }
    // if(xhr.responseText && xhr.responseText.indexOf("cart_detailed")>=0)
    // {
    //     var json= $.parseJSON(xhr.responseText);
    //
    //     if($('.block-shopping-cart').attr('data-change-cart'))
    //     {
    //         $('.block-shopping-cart').attr('data-change-cart','');
    //         ets_refresh_shipping_cart(true);
    //     }
    //     $(document).trigger('updatePayMentMethod');
    //     $(document).trigger('DOMContentLoaded');
    // }
});


/*
 * Polyfill for Internet Explorer
 * See https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
 */
(function () {
    if (typeof window.CustomEvent === "function") return false;

    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: undefined};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event,
            params.bubbles, params.cancelable, params.detail);
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();

$(document).ready(function () {
    $('.wpcfu input[type="url"]').each(function () {
        if ($(this).val()) {
            $(this).val($(this).val().replace('default:current_url', window.location.href));
        }
    });
    $(document).on('click', '.wpcfu .ets_cfu_show_hide', function () {
        if ($(this).find('i.svg-eye-slash').length > 0) {
            $(this).find('i').removeClass('svg-eye-slash').addClass('svg-eye');
            $(this).prev('input').attr('type', 'text');
        } else {
            $(this).find('i').removeClass('svg-eye').addClass('svg-eye-slash');
            $(this).prev('input').attr('type', 'password');
        }
    });
    $(document).on('click', '.ctf_click_open_contactform7', function () {
        var id = $(this).attr('data-id');
        $('#ctf-popup-wapper-' + id).addClass('show');
        if (!$(this).hasClass('addlogded')) {
            ajaxAddLoger($('#ctf-popup-wapper-' + id).find('.wpcfu').attr('data-id'));
            $(this).addClass('addlogded');
        }
    });
    $(document).on('click', '.wpcfu.hook', function () {
        var id = $(this).attr('data-id');
        $('#ctf-popup-wapper-' + id).addClass('show');
        if (!$(this).hasClass('addlogded')) {
            ajaxAddLoger(id);
            $(this).addClass('addlogded');
        }
    });
    $(document).on('click', '.ctf_close_popup', function () {
        $(this).closest('.ctf-popup-wapper').removeClass('show');
    });
    $(document).mouseup(function (e) {
        var container = $('.ctf-popup-content');
        var datepicker = $('.ui-datepicker');
        if (!container.is(e.target) && container.has(e.target).length === 0 && !datepicker.is(e.target) && datepicker.has(e.target).length === 0) {
            $('.ctf-popup-wapper').removeClass('show');
        }
    });
    $(document).keyup(function (e) {
        if (e.keyCode == 27) { // escape key maps to keycode `27`
            $('.ctf-popup-wapper').removeClass('show');
        }
    });
    if ($(".wpcfu .datetimepicker").length > 0) {
        $(".wpcfu .datetimepicker").datetimepicker({
            timepicker: true,
            format: 'Y-m-d h:i',
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm',
            changeMonth: true,
            changeYear: true,
        });
    }
    if ($(".wpcfu .datepicker").length > 0) {
        $(".wpcfu .datepicker").datepicker({
            timepicker: false,
            format: 'Y-m-d',
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
        });
    }
    $('.pa-captcha-refesh').click(function () {
        refeshImage($(this));
        return false;
    });
    if ($('input[type="range"]').length) {
        $('input[type="range"]').each(function () {
            if ($(this).prev('.rang-value').length > 0)
                $(this).prev('.rang-value').html($(this).val());
        });
    }
    $('input[type="range"]').change(function () {
        if ($(this).prev('.rang-value').length > 0)
            $(this).prev('.rang-value').html($(this).val());
    });


    //Init MCE
    if (typeof tinyMCE !== 'undefined' && $('.autoload_rte_cfu').length > 0) {
        tinyMCE.init({
            selector: '.autoload_rte_cfu',
            plugins: "align link image media code emoticons",
            browser_spellcheck: true,
            toolbar1: "emoticons,bold,italic,underline,link,align,bulli,numlist,table,image",
            convert_urls: false,
        });
    }
});

function getDataContent(object) {
    var idEl = object[0].id,
        body = tinyMCE.get(idEl).getBody(),
        text = body.innerText || body.textContent;
    var re = /\n$/g;
    var OK = re.exec(text);
    if (OK) {
        text = text.replace(/\n$/g, '');
    }
    return {
        idEl: idEl,
        chars: text.replace(/[\t\n]+/g, ' ').length,
        words: text.split(/[\w\u2019\'-]+/).length
    };
};

function excuteType(objectData) {
    var elMax = $('#' + objectData['idEl']).next('.number_max'),
        elIndex = elMax.find('.index_type').first();

    if (parseInt(objectData['chars']) >= parseInt($('#' + objectData['idEl']).attr('maxlength'))) {
        elIndex.html(parseInt($('#' + objectData['idEl']).attr('maxlength')));
        return false;
    }
    elIndex.html(parseInt(objectData['chars']));
    return true;
}


function refeshImage($this) {
    if ($this.prev('.pa-captcha-img-data').length) {
        originalCapcha = $this.prev('.pa-captcha-img-data').attr('src');
        originalCode = $this.attr('data-rand');
        newCode = Math.random();
        $this.prev('.pa-captcha-img-data').attr('src', originalCapcha.replace(originalCode, newCode));
        $this.attr('data-rand', newCode);
        $('input[type="captcha"]').val('');
    }
}

function ajaxAddLoger(id_contact) {
    $.ajax({
        type: 'POST',
        headers: {"cache-control": "no-cache"},
        url: link_contact_ets,
        dataType: "json",
        data: 'action=etsCfuAddLogger&id_contact=' + id_contact,
        success: function (jsonData) {

        }
    });
}

function ets_cfu_loading_captcha() {
    var img = $('.pa-captcha-img-data:not(.loaded)').first();
    if (img.length > 0) {
        img.load(function () {
            if (!img.hasClass('loaded')) {
                ets_cfu_refresh_captcha(img);
            }
            if (img[0].complete && img.hasClass('loaded')) {
                ets_cfu_loading_captcha();
            }
        }).filter(function () {
            return this.complete;
        }).load();
    }
}

function ets_cfu_refresh_captcha(img) {
    if (img.length && !img.hasClass('loaded')) {
        var org_link = img.attr('src'),
            org_code = img.attr('data-rand'),
            rand = Math.random();
        img.attr({'src': org_link.replace(org_code, rand), 'data-rand': rand});
        if (!img.hasClass('loaded')) {
            img.addClass('loaded');
        }
        img.next(".pa-captcha-refesh").attr('data-rand', rand);
    }
}

document.addEventListener("DOMContentLoaded", function (event) {
    if ($('.pa-captcha-img-data').length > 1) {
        ets_cfu_loading_captcha();
    }
});

var ets_cfu_update = {
    init: function () {
        this.check_maxlength_number();
        this.caculate_maxleng();
        this.hidden_reference();
    },
    check_maxlength_number: function () {
        var input_number = $('.ets_cfu_form-control-wrap input[type="number"]');
        if (input_number.length > 0) {
            input_number.on("keyup", function (e) {
                var $field = $(this),
                    val = this.value; // retrieve the index
                if (val.length > Number($field.attr("maxlength"))) {
                    val = val.slice(0, $field.attr('maxlength'));
                    $field.val(val);
                }
            });
        }
    },
    caculate_maxleng: function () {
        $(document).on('keyup', 'input[maxlength], textarea[maxlength]', function () {
            var leng = $(this).val().length;
            if (leng > $(this).attr('maxlength')) {
                return;
            }
            var el_numbermax = $(this).next('.number_max');
            el_numbermax.find('.index_type').first().html(leng);
        });
    },
    hidden_reference: function () {
        if ($('.ets_cfu_input_reference').length > 0 && typeof hidden_reference !== "undefined" && hidden_reference) {
            $('.ets_cfu_input_reference').hide();
        }
        if ($('.ets_cfu_input_referrence').length > 0 && typeof hidden_reference !== "undefined" && hidden_reference) {
            $('.ets_cfu_input_referrence').hide();
        }
    },
};

$(document).ready(function () {
    ets_cfu_update.init();
});

$(document).on('click', '.ets_cfu_form-control-wrap .number_plus,.ets_cfu_form-control-wrap .number_minus', function () {
    var $qty = $(this).closest('.ets_cfu_form-control-wrap').find('.ets_cfu-number').first(),
        currentVal = parseFloat($qty.val()),
        max = parseFloat($qty.attr('max')),
        min = parseFloat($qty.attr('min')),
        step = $qty.attr('step');

    // Format values
    if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
    if (max === '' || max === 'NaN') max = '';
    if (min === '' || min === 'NaN') min = 0;
    if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

    // Change the value
    if ($(this).is('.ets_cfu_form-control-wrap .number_plus')) {
        if (max && (currentVal >= max)) {
            $qty.val(max);
        } else {
            $qty.val((currentVal + parseFloat(step)));
        }
    } else {
        if (min && (currentVal <= min)) {
            $qty.val(min);
        } else if (currentVal > 0) {
            $qty.val((currentVal - parseFloat(step)));
        }
    }

    // Trigger change event
    $qty.trigger('change');
});

$(".access_numer input").keypress(function (event) {
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122))
        return false;
    else return true;
});


//newCode:
const ETS_CFU_DO_SHOW = 1;
const ETS_CFU_DO_HIDE = 2;
const ETS_CFU_DO_SHOW_MULTIPLE = 3;
const ETS_CFU_DO_HIDE_MULTIPLE = 4;

const ETS_CFU_OPERATOR_CONTAINER = 1;
const ETS_CFU_OPERATOR_DO_NOT_CONTAINER = 2;
const ETS_CFU_OPERATOR_EMPTY = 3;
const ETS_CFU_OPERATOR_FILLED = 4;
const ETS_CFU_OPERATOR_EQUAL = 5;
const ETS_CFU_OPERATOR_NOT_EQUAL = 6;
const ETS_CFU_OPERATOR_LESS_THAN = 7;
const ETS_CFU_OPERATOR_GREATER_THAN = 8;
const ETS_CFU_OPERATOR_BEFORE = 9;
const ETS_CFU_OPERATOR_AFTER = 10;

const ETS_CFU_INPUT_TYPES = ['menu', 'radio', 'checkbox'];

function ets_cfu_init_condition_form(form, load) {
    if (!form) {
        form = $('form.ets-cfu-form:not(.ets-cfu-condition-loaded)');
    }
    if (!form.length > 0)
        return;
    form.each(function () {
        let _this = $(this)
            , id = _this.attr('data-id')
        ;
        try {
            if (typeof eval(`ETS_CFU_CONDITION_FF_${id}`) === typeof undefined) return;
        } catch (error) {
            return error;
        }
        let fields_form = eval(`ETS_CFU_CONDITION_FF_${id}`)
            , ifs = eval(`ETS_CFU_CONDITION_IF_${id}`)
            , operators = eval(`ETS_CFU_CONDITION_OP_${id}`)
            , values = eval(`ETS_CFU_CONDITION_VL_${id}`)
            , dos = eval(`ETS_CFU_CONDITION_DO_${id}`)
            , fields = eval(`ETS_CFU_CONDITION_FI_${id}`)
        ;
        if (typeof ifs !== typeof undefined) {
            let keys = Object.keys(ifs);
            keys.forEach((key) => {
                let field = ets_cfu_get_field_form(fields_form, ifs[key])
                    , selector = `form[data-id=${id}] [name^=${ifs[key]}]`
                    , selectorForm = `form[data-id=${id}]`
                ;
                if (load) {
                    let _this = $(selector);
                    ets_cfu_condition(selectorForm, _this, key, ifs, operators, values, dos, fields, field);
                }
                $(document).on('change keyup', selector, function (e) {
                    e.preventDefault();
                    let _this = $(this);
                    ets_cfu_condition(selectorForm, _this, key, ifs, operators, values, dos, fields, field);
                });
            });
        }
    });
}

function ets_cfu_condition(selectorForm, _this, key, ifs, operators, values, dos, fields, field) {
    let logic = false;
    switch (operators[key]) {
        case `${ETS_CFU_OPERATOR_CONTAINER}`:
            if (_this.val().indexOf(values[key]) !== -1) {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_DO_NOT_CONTAINER}`:
            if (_this.val().indexOf(values[key]) === -1) {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_EMPTY}`:
            if (_this.val().trim() === '') {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_FILLED}`:
            if (_this.val().trim() !== '') {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_EQUAL}`:
            let eq = ets_cfu_convert_value(_this, values[key], field.type);
            if (eq[0] === eq[1] || eq[1].indexOf(eq[0]) !== -1) {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_NOT_EQUAL}`:
            let not_eq = ets_cfu_convert_value(_this, values[key], field.type);
            if (not_eq[0] !== not_eq[1] || eq[1].indexOf(eq[0]) !== -1) {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_BEFORE}`:
        case `${ETS_CFU_OPERATOR_LESS_THAN}`:
            let le = ets_cfu_convert_value(_this, values[key], field.type);
            if (le[0] < le[1]) {
                logic = true;
            }
            break;
        case `${ETS_CFU_OPERATOR_AFTER}`:
        case `${ETS_CFU_OPERATOR_GREATER_THAN}`:
            let gt = ets_cfu_convert_value(_this, values[key], field.type);
            if (gt[0] > gt[1]) {
                logic = true;
            }
            break;
    }
    ets_cfu_do_field(selectorForm, dos[key], fields[key], logic);
}

function ets_cfu_convert_value(_this, value2, type) {
    let convert1 = null, convert2 = null;
    switch (type) {
        case 'number':
            convert1 = new Number(_this.val());
            convert2 = new Number(value2);
            break;
        case 'date':
            convert1 = new Date(_this.val()).getTime();
            convert2 = new Date(value2).getTime();
            break;
        case 'checkbox':
            convert1 = 0;
            convert2 = 1;
            let values = $(`input[name="${_this.attr('name')}"]:checked`), list = [];
            if (values.length > 0) {
                values.each(function () {
                    list.push($(this).val());
                });
            }
            if (value2.length > 0 && list.length > 0 && value2.length === list.length) {
                convert1 = 1;
                convert2 = 1;
                list.forEach((item) => {
                    if (value2.indexOf(item) === -1) {
                        convert1 = 0;
                        convert2 = 1;
                        return [convert1, convert2];
                    }
                });
            }
            break;
        case 'radio':
            convert1 = $(`input[name="${_this.attr('name')}"]:checked`).length > 0 ? $(`input[name="${_this.attr('name')}"]:checked`).val().trim() : null;
            convert2 = value2[0].trim();
            break;
        case 'menu':
            convert1 = _this.val();
            convert2 = value2.trim();
            if (_this.attr('multiple') === 'multiple') {
                if (convert1 !== null && convert1.length > 0) {
                    convert1.forEach((item) =>{
                        if (item === convert2) {
                            convert1 = item;
                            return [convert1, convert2];
                        }
                    });
                }
            }
            break;
        default:
            convert1 = _this.val().trim();
            convert2 = value2.trim();
            break;
    }
    return [convert1, convert2];
}

function ets_cfu_do_field(selectorForm, _do, fields, logic) {
    if (!_do || !fields)
        return;
    switch (_do) {
        case `${ETS_CFU_DO_HIDE}`:
        case `${ETS_CFU_DO_HIDE_MULTIPLE}`:
            fields.forEach((field) => {
                let wrapper_field = $(`${selectorForm} [name^=${field}]`).closest('.ets_cfu_input');
                if (logic)
                    wrapper_field.hide();
                else
                    wrapper_field.show();
            });
            break;
        case `${ETS_CFU_DO_SHOW}`:
        case `${ETS_CFU_DO_SHOW_MULTIPLE}`:
            fields.forEach((field) => {
                let wrapper_field = $(`${selectorForm} [name=${field}]`).closest('.ets_cfu_input');
                if (logic)
                    wrapper_field.show();
                else
                    wrapper_field.hide();
            });
            break;
    }
}

function ets_cfu_get_field_form(fields_form, name) {
    if (!fields_form)
        return {};
    let find = {};
    fields_form.forEach((field) => {
        if (name === field.name) {
            find = field;
        }
    });
    return find;
}

$(document).ready(function () {
    ets_cfu_init_condition_form(false, true);
});

