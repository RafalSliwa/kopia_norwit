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

var etsTransHelpdesk = {
    getBoxMessage: function (placeholder) {
        if (!placeholder) placeholder = '';
        return `<div class="ets-trans-hd-input-item">
                    <label>${etsTransHelpdesk.getTextTrans('original_content')}</label>
                    <textarea class="form-control text-trans-origin" id="etsTransOriginMessage" placeholder="${placeholder}"></textarea>
                </div>
               <input type="hidden" id="etsTransHdLangDetacted" value="" />
               <input type="hidden" id="etsTransHdLangTargetValue" value="" />
            <div class="ets-trans-hd-swaplang-box">
                <button class="btn btn-default js-btn-ets-trans-hd-swap-lang btn-ets-trans-hd-swap-lang" data-toggle="tooltip" data-placement="top" title="${etsTransHelpdesk.getTextTrans('swap_language', 'Swap language')}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                </button>
            </div>`;
    },
    getTextTrans: function (key, defaultText){
        if (typeof etsTransText !== 'undefined' && typeof etsTransText[key] !== 'undefined'){
            return etsTransText[key];
        }
        if (typeof defaultText !== 'undefined' && defaultText){
            return defaultText;
        }
        return key;
    },
    getBtnTrans: function (source, target) {
        if (typeof source === 'undefined') source = '';
        if (typeof target === 'undefined') target = '';
        var langSource = null;
        if (source) langSource = etsTransHelpdesk.getLangByIsoCode(source);

        var langTarget = null;
        if (target) langTarget = etsTransHelpdesk.getLangByIsoCode(target);

        return `<div class="form-group">
                <label class="control-label col-lg-3"></label>
                <div class="col-lg-9">
                <div class="ets-trans-hd-group-btn">
                    <div class="ets-trans-hd-group-select">
                    <div class="ets-trans-hd-group-item">
                        <label class="ets-trans-hd-label-trans-from">${etsTransHelpdesk.getTextTrans('translate_from', 'Translate from')}</label>
                        <div class="dropdown dropdown-lang-selection">
                            <button id="btnEtsTransHdSelectLangSource" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-selected">${langSource ? `<img src="${langSource.flag}" class="lang-img" /> <span class="lang-title">${langSource.name}</span>` : etsTransHelpdesk.getTextTrans('auto_detect_language', 'Auto detect language')}</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                ${etsTransHelpdesk.renderListLanguage('source')}
                            </ul>
                        </div>
                    </div>
                    <div class="ets-trans-hd-group-item">
                        <label class="ets-trans-hd-label-trans-from">${etsTransHelpdesk.getTextTrans('translate_to', 'To')}</label>
                        <div class="dropdown dropdown-lang-selection">
                            <button id="btnEtsTransHdSelectLangTarget" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-selected">${langTarget ? `<img src="${langTarget.flag}" class="lang-img" /> <span class="lang-title">${langTarget.name}</span>` : etsTransHelpdesk.getTextTrans('lang_customer', 'Current customer language')}</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                ${etsTransHelpdesk.renderListLanguage('target')}
                            </ul>
                        </div>
                    </div>
                    </div>
            <button type="button" class="btn btn-default js-btn-ets-trans-hd-ticket" data-source="${source}" data-target="${target}">${typeof etsTransText !== 'undefined' ? etsTransText.translate : 'Translate'}</button>
        </div>
        <div class="ets-trans-message-lang-detected"></div></div></div>`;
    },
    renderListLanguage: function (langType) {
        if (typeof ETS_TRANS_LANG_WITH_FLAG == 'undefined' || !ETS_TRANS_LANG_WITH_FLAG || !ETS_TRANS_LANG_WITH_FLAG.length) return false;
        var className = 'js-ets-trans-hd-source-item';
        if (langType == 'target'){
            className = 'js-ets-trans-hd-target-item';
        }
        var html = '';
        if (langType == 'source'){
            html = `<li><a href="" data-lang="" class="${className} active">
                ${etsTransHelpdesk.getTextTrans('auto_detect_language', 'Auto detect language')}
                </a></li>`;
        }

        ETS_TRANS_LANG_WITH_FLAG.forEach(function (lang) {
            html += `<li><a href="#" data-lang="${lang.iso_code}" class="${className}"><img src="${lang.flag}" class="lang-img" /> <span class="lang-title">${lang.name}</span></a></li>`;
        });
        return html;
    },
    getLangByIsoCode: function (iso_code){
        if (typeof ETS_TRANS_LANG_WITH_FLAG == 'undefined' || !ETS_TRANS_LANG_WITH_FLAG || !ETS_TRANS_LANG_WITH_FLAG.length) return {};
        var result = {};
        ETS_TRANS_LANG_WITH_FLAG.forEach(function (lang) {
            if (lang.iso_code == iso_code){
                result = lang;
            }
        });
        return result;
    },
    addBoxTranslate: function () {
        if (!$('#ets_hd_ticket_message_form #message').length) return false;
        var noteEl = $('#ets_hd_ticket_message_form #message');
        var placeholder = noteEl.attr('placeholder');
        noteEl.attr('placeholder', etsTransHelpdesk.getTextTrans('blank_to_use_origin'));
        noteEl.wrap('<div class="ets-trans-hd-input-item"></div>');
        noteEl.before(`<label>${etsTransHelpdesk.getTextTrans('translated_content')}</label>`);
        $('.ets-trans-hd-input-item').wrap('<div class="ets-trans-hd-wrapper-box"></div>');
        $('.ets-trans-hd-wrapper-box').prepend(etsTransHelpdesk.getBoxMessage(placeholder));
        $('.ets-trans-hd-wrapper-box').parents('.form-group').before(etsTransHelpdesk.getBtnTrans(typeof hdLangSource !== 'undefined' ? hdLangSource : '', typeof hdLangTarget !== 'undefined' ? hdLangTarget : ''));
        $('.js-btn-ets-trans-hd-swap-lang').tooltip();
    },
    getLangCode: function (langCode) {
        return langCode.replace(/\-\w+$/, '');
    },
    translateTicket: function (btn, idTicket, text, sourceLang, targetLang, isInverse){
        if (isInverse && !targetLang){
            targetLang = $('#etsTransHdLangDetacted').val();
        }
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransHelpdeskTicket: 1,
                ticketText: text,
                sourceLang: sourceLang,
                targetLang: targetLang,
                idTicket: idTicket,
                isInverse: isInverse
            },
            beforeSend: function () {
                btn.addClass('loading');
                btn.prop('disabled', true);
            },
            success: function (res) {
                if (res.success) {
                    var dataTrans = res.data;
                    if (dataTrans.errors){
                        etsTransFunc.showErrorMessage(dataTrans.message);
                    }
                    else{
                        if (dataTrans.lang_source && dataTrans.lang_source.iso_code){
                            $('#etsTransHdLangDetacted').val(dataTrans.lang_source.iso_code);
                        }
                        if (dataTrans.lang_target && dataTrans.lang_target.iso_code){
                            $('#etsTransHdLangTargetValue').val(dataTrans.lang_target.iso_code);
                        }
                        if (!dataTrans.lang_source && typeof dataTrans.detectedSourceLanguage !== 'undefined' && dataTrans.detectedSourceLanguage.length){
                            $('#etsTransHdLangDetacted').val(dataTrans.detectedSourceLanguage[0])
                        }
                        if (isInverse){
                            $('#etsTransOriginMessage').val($('#ets_hd_ticket_message_form #message').val());
                        }
                        $('#ets_hd_ticket_message_form #message').val(dataTrans.data[0]);

                        etsTransFunc.showSuccessMessage(res.message);
                    }
                }
                else{
                    etsTransFunc.showErrorMessage(res.message);
                }
            },
            complete: function () {
                btn.removeClass('loading');
                btn.prop('disabled', false);
            }
        })
    }
};
$(document).ready(function () {
    if ($('#ets_hd_ticket_message_form #message').length && typeof ETS_TRANS_ENABLE_TRANSLATE_TICKET !== 'undefined' && ETS_TRANS_ENABLE_TRANSLATE_TICKET) {
        etsTransHelpdesk.addBoxTranslate();
    }
    $(document).on('click', '.js-ets-trans-hd-source-item,.js-ets-trans-hd-target-item', function (event) {
        event.preventDefault();
        var dataLang = $(this).attr('data-lang');
        var textContent = $(this).html();
        $(this).addClass('active');
        $(this).closest('.dropdown-lang-selection').find('button .text-selected ').html(textContent);
        if ($(this).hasClass('js-ets-trans-hd-target-item')) {
            $('#btnEtsTransHdSelectLangTarget').dropdown('toggle');
            $('.js-btn-ets-trans-hd-ticket').attr('data-target', dataLang);
        }
        else {
            $('#btnEtsTransHdSelectLangSource').dropdown('toggle');
            $('.js-btn-ets-trans-hd-ticket').attr('data-source', dataLang);
        }

        return false;
    });

    $(document).on('click', '.js-btn-ets-trans-hd-ticket', function () {
        var $this = $(this);
        if (typeof ETS_TRANS_LINK_AJAX_MODULE == 'undefined' || $this.hasClass('loading'))
            return false;
        var text = $('#etsTransOriginMessage').val();
        if (!text || !text.trim()) return false;
        var sourceLang = $(this).attr('data-source');
        var targetLang = $(this).attr('data-target');
        var idTicket = $('#ets_hd_ticket_message_form #message').closest('form').find('input[name="id_ticket"]').val();

        etsTransHelpdesk.translateTicket($(this), idTicket, text, sourceLang, targetLang, false);
    });

    $(document).on('click', '.js-btn-ets-trans-hd-swap-lang', function (){
        var $this = $(this);
        if (typeof ETS_TRANS_LINK_AJAX_MODULE == 'undefined' || $this.hasClass('loading'))
            return false;
        var text = $('#ets_hd_ticket_message_form #message').val();
        if (!text || !text.trim()) return false;
        var sourceLang = $('.js-btn-ets-trans-hd-ticket').attr('data-target');
        if (!sourceLang){
            sourceLang = $('#etsTransHdLangTargetValue').val();
        }
        var targetLang = $('.js-btn-ets-trans-hd-ticket').attr('data-source');
        $('.js-ets-trans-hd-source-item[data-lang="'+sourceLang+'"]').click();
        $('.js-ets-trans-hd-target-item[data-lang="'+targetLang+'"]').click();

        var idTicket = $('#ets_hd_ticket_message_form #message').closest('form').find('input[name="id_ticket"]').val();

        etsTransHelpdesk.translateTicket($(this), idTicket, text, sourceLang, targetLang, true);
    });

    $(document).ajaxSuccess(function (event, request, setting){

        var formData = setting.data;
        if (formData instanceof FormData && formData.get('submitAddMessage') == '1'){
            $('#etsTransOriginMessage').val('');
        }
    });
});