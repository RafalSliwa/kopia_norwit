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
if (ets_cfu_recaptcha_enabled) {
    if (!ets_cfu_recaptcha_v3) {
        //v2.
        var recaptchaWidgets = [];
        var recaptchaCallback = function () {
            if (typeof grecaptcha == "undefined")
                return;
            var forms = document.getElementsByTagName('form');
            var pattern = /(^|\s)g-recaptcha(\s|$)/;
            for (var i = 0; i < forms.length; i++) {
                var divs = forms[i].getElementsByTagName('div');
                for (var j = 0; j < divs.length; j++) {
                    var sitekey = divs[j].getAttribute('data-sitekey');
                    if (divs[j].className && divs[j].className.match(pattern) && sitekey) {
                        var params = {
                            'sitekey': sitekey,
                            'type': divs[j].getAttribute('data-type'),
                            'size': divs[j].getAttribute('data-size'),
                            'theme': divs[j].getAttribute('data-theme'),
                            'badge': divs[j].getAttribute('data-badge'),
                            'tabindex': divs[j].getAttribute('data-tabindex')
                        };
                        var callback = divs[j].getAttribute('data-callback');
                        if (callback && 'function' == typeof window[callback]) {
                            params['callback'] = window[callback];
                        }
                        var expired_callback = divs[j].getAttribute('data-expired-callback');
                        if (expired_callback && 'function' == typeof window[expired_callback]) {
                            params['expired-callback'] = window[expired_callback];
                        }
                        var widget_id = grecaptcha.render(divs[j], params);
                        recaptchaWidgets.push(widget_id);
                        break;
                    }
                }
            }
        };
        document.addEventListener('ets_cfusubmit', function (event) {
            switch (event.detail.status) {
                case 'spam':
                case 'mail_sent':
                case 'mail_failed':
                    for (var i = 0; i < recaptchaWidgets.length; i++) {
                        grecaptcha.reset(recaptchaWidgets[i]);
                    }
            }
        }, false);
    } else {
        //v3.
        (function ($) {
            var cfu_re_captcha_v3 = function (form) {
                if ($('.wpcfu form:not(.g-loaded)').length <= 0)
                    return;
                var g_captcha = form.find('.g-recaptcha').eq(0);
                if (g_captcha.length > 0) {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(ets_cfu_recaptcha_key, {action: 'contact'}).then(function (token) {
                            g_captcha.html('<input type="hidden" class="g-recaptcha-response" name="g-recaptcha-response" value="' + token + '"/>');
                            if (g_captcha.find('.g-recaptcha-response').length > 0) {
                                form.addClass('g-loaded');
                                cfu_re_captcha_v3(form);
                            }
                        });
                    });
                }
            }
            $(document).ready(function () {
                if (ets_cfu_recaptcha_v3) {
                    cfu_re_captcha_v3($('.wpcfu form:not(.g-loaded)'));
                }
            });
        })(jQuery);
    }
}