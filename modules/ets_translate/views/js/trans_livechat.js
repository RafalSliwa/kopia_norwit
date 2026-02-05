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

var etsTransLivechat = {
    getBoxMessage: function (placeholder) {
        if (!placeholder) placeholder = '';
        return `<div class="ets-trans-lc-input-item">
                    <label>${etsTransLivechat.getTextTrans('original_content')}</label>
                    <textarea class="form-control text-trans-origin" id="etsTransOriginMessage" placeholder="${placeholder}"></textarea>
                    <label for="keep_original_content"><input type="checkbox" value="1" name="keep_original_content" id="keep_original_content" checked="checked"> ${etsTransLivechat.getTextTrans('keep_original_content')}</label>
                </div>
               <input type="hidden" id="etsTransLcLangDetacted" value="" />
               <input type="hidden" id="etsTransLcLangTargetValue" value="" />
            <div class="ets-trans-lc-swaplang-box">
                <button class="btn btn-default js-btn-ets-trans-lc-swap-lang btn-ets-trans-lc-swap-lang" data-toggle="tooltip" data-placement="top" title="${etsTransLivechat.getTextTrans('swap_language', 'Swap language')}">
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
        if (source) langSource = etsTransLivechat.getLangByIsoCode(source);
        var langTarget = null;
        if (target) langTarget = etsTransLivechat.getLangByIsoCode(target);
        return `<div class="ets-trans-lc-group-btn">
                    <div class="ets-trans-lc-group-item">
                        <label class="ets-trans-lc-label-trans-from">${etsTransLivechat.getTextTrans('translate_from', 'Translate from')}</label>
                        <div class="dropdown dropdown-lang-selection">
                            <button id="btnEtsTransLcSelectLangSource" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-selected">${langSource ? `<img src="${langSource.flag}" class="lang-img" /> <span class="lang-title">${langSource.name}</span>` : etsTransLivechat.getTextTrans('auto_detect_language', 'Auto detect language')}</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                ${etsTransLivechat.renderListLanguage('source')}
                            </ul>
                        </div>
                    </div>
                    <div class="ets-trans-lc-group-item">
                        <label class="ets-trans-lc-label-trans-from">${etsTransLivechat.getTextTrans('translate_to', 'To')}</label>
                        <div class="dropdown dropdown-lang-selection">
                            <button id="btnEtsTransLcSelectLangTarget" type="button" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-selected">${langTarget ? `<img src="${langTarget.flag}" class="lang-img" /> <span class="lang-title">${langTarget.name}</span>` : etsTransLivechat.getTextTrans('lang_customer', 'Current customer language')}</span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">
                                ${etsTransLivechat.renderListLanguage('target')}
                            </ul>
                        </div>
                    </div>
            <button type="button" class="btn btn-default js-btn-ets-trans-lc-ticket" data-source="${source}" data-target="${target}">${typeof etsTransText !== 'undefined' ? etsTransText.translate : 'G-Translate'}</button>
        </div>
        <div class="ets-trans-message-lang-detected"></div>`;
    },
    renderListLanguage: function (langType) {
        if (typeof ETS_TRANS_LANG_WITH_FLAG == 'undefined' || !ETS_TRANS_LANG_WITH_FLAG || !ETS_TRANS_LANG_WITH_FLAG.length) return false;
        var className = 'js-ets-trans-lc-source-item';
        if (langType == 'target'){
            className = 'js-ets-trans-lc-target-item';
        }
        var html = '';
        if (langType == 'source'){
            html = `<li><a href="" data-lang="" class="${className} active">
                ${etsTransLivechat.getTextTrans('auto_detect_language', 'Auto detect language')}
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
        if (!$('#ticket_note').length) return false;
        var noteEl = $('#ticket_note');
        var placeholder = noteEl.attr('placeholder');
        noteEl.attr('placeholder', etsTransLivechat.getTextTrans('blank_to_use_origin'));
        noteEl.wrap('<div class="ets-trans-lc-input-item"></div>');
        noteEl.before(`<label>${etsTransLivechat.getTextTrans('translated_content')}</label>`);
        $('.ets-trans-lc-input-item').wrap('<div class="ets-trans-lc-wrapper-box"></div>');
        $('.ets-trans-lc-wrapper-box').prepend(etsTransLivechat.getBoxMessage(placeholder));
        $('.ets-trans-lc-wrapper-box').before(etsTransLivechat.getBtnTrans(typeof lcLangSource !== 'undefined' ? lcLangSource : '', typeof lcLangTarget !== 'undefined' ? lcLangTarget : ''));
        $('.js-btn-ets-trans-lc-swap-lang').tooltip();
    },
    getLangCode: function (langCode) {
        return langCode.replace(/\-\w+$/, '');
    },
    translateTicket: function (btn, idTicket, text, sourceLang, targetLang, isInverse){
        if (isInverse && !targetLang){
            targetLang = $('#etsTransLcLangDetacted').val();
        }
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransLivechatTicket: 1,
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
                            $('#etsTransLcLangDetacted').val(dataTrans.lang_source.iso_code);
                        }
                        if (dataTrans.lang_target && dataTrans.lang_target.iso_code){
                            $('#etsTransLcLangTargetValue').val(dataTrans.lang_target.iso_code);
                        }
                        if (!dataTrans.lang_source && typeof dataTrans.detectedSourceLanguage !== 'undefined' && dataTrans.detectedSourceLanguage.length){
                            $('#etsTransLcLangDetacted').val(dataTrans.detectedSourceLanguage[0])
                        }

                        if (isInverse){
                            $('#etsTransOriginMessage').val($('#ticket_note').val());
                        }
                        $('#ticket_note').val(dataTrans.data[0]);
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
    if ($('#ticket_note').length && typeof ETS_TRANS_ENABLE_TRANSLATE_TICKET !== 'undefined' && ETS_TRANS_ENABLE_TRANSLATE_TICKET) {
        etsTransLivechat.addBoxTranslate();
    }
    $(document).on('click', '.js-ets-trans-lc-source-item,.js-ets-trans-lc-target-item', function (event) {
        event.preventDefault();
        var dataLang = $(this).attr('data-lang');
        var textContent = $(this).html();
        $(this).addClass('active');
        $(this).closest('.dropdown-lang-selection').find('button .text-selected ').html(textContent);
        if ($(this).hasClass('js-ets-trans-lc-target-item')) {
            $('#btnEtsTransLcSelectLangTarget').dropdown('toggle');
            $('.js-btn-ets-trans-lc-ticket').attr('data-target', dataLang);
        }
        else {
            $('#btnEtsTransLcSelectLangSource').dropdown('toggle');
            $('.js-btn-ets-trans-lc-ticket').attr('data-source', dataLang);
        }
        return false;
    });

    $(document).on('click', '.js-btn-ets-trans-lc-ticket', function () {
        var $this = $(this);
        if (typeof ETS_TRANS_LINK_AJAX_MODULE == 'undefined' || $this.hasClass('loading'))
            return false;
        var text = $('#etsTransOriginMessage').val();
        if (!text || !text.trim()) return false;
        var sourceLang = $(this).attr('data-source');
        var targetLang = $(this).attr('data-target');
        var idTicket = $('#ticket_note').closest('form').find('input[name="id_ticket"]').val();

        etsTransLivechat.translateTicket($(this), idTicket, text, sourceLang, targetLang, false);
    });

    $(document).on('click', '.js-btn-ets-trans-lc-swap-lang', function (){
        var $this = $(this);
        if (typeof ETS_TRANS_LINK_AJAX_MODULE == 'undefined' || $this.hasClass('loading'))
            return false;
        var text = $('#ticket_note').val();
        if (!text || !text.trim()) return false;
        var sourceLang = $('.js-btn-ets-trans-lc-ticket').attr('data-target');
        if (!sourceLang){
            sourceLang = $('#etsTransLcLangTargetValue').val();
        }
        var targetLang = $('.js-btn-ets-trans-lc-ticket').attr('data-source');
        var idTicket = $('#ticket_note').closest('form').find('input[name="id_ticket"]').val();
        $('.js-ets-trans-lc-source-item[data-lang="'+sourceLang+'"]').click();
        $('.js-ets-trans-lc-target-item[data-lang="'+targetLang+'"]').click();
        etsTransLivechat.translateTicket($(this), idTicket, text, sourceLang, targetLang, true);
    });

    $(document).ajaxSuccess(function (event, request, setting){

        var formData = setting.data;
        if (formData instanceof FormData && formData.get('lc_send_message_ticket') == '1'){
            $('#etsTransOriginMessage').val('');
        }
    });
});