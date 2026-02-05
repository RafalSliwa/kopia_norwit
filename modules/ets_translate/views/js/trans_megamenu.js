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

var etsTransMMItemClick = null;
var etsTransMegamenu = {
    ajaxXhrTranslatePage: null,
    renderBtnTransItem: function () {
        return '<button class="btn btn-default ets-trans-btn-trans-field-item js-ets-trans-mm-item" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i></button>';
    },
    renderBtnTransForm: function () {
        return '<button class="btn btn-default js-ets-trans-mm-form pull-right" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"></path></svg></i> ' + etsTransFunc.trans('g-translate') + '</button>';
    },
    renderBtnTransAllModule: function(){
        return '<button class="btn btn-default js-ets-trans-mm-all" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> ' + etsTransFunc.trans('g-translate') + '</button>';
    },
    addBtnTransToInput: function () {
        if(ETS_TRANS_ENABLE_TRANS_FIELD) {
            $('.mm_menu_form.mm_pop_up .mm_form .translatable-field').each(function () {
                if ($(this).find('input[type=text],textarea').attr('id').indexOf('url_') === -1 && !$(this).parent().find('.js-ets-trans-mm-item').length) {
                    $(this).parent().addClass('menu_trans_button').append(etsTransMegamenu.renderBtnTransItem());
                }
            });
        }
        if ($('.mm_save_wrapper').length) {
            $('.mm_save_wrapper').each(function () {
                if (!$(this).find('.js-ets-trans-mm-form').length && $(this).closest('form').attr('id') !== 'ets_mm_column_form') {
                    $(this).append(etsTransMegamenu.renderBtnTransForm());
                }
            });
        }
    },
    getParameterByName: function (name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    },
    translate: function(btnClick, formData, isTransAll){
        
        isTransAll =isTransAll || 0;
        isTransAll = parseInt(isTransAll);
        etsTransMegamenu.ajaxXhrTranslatePage = $.ajax({
           url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            data: {
                etsTransMegamenu: 1,
                isTransAll: isTransAll,
                formData: formData,
            },
            dataType: 'json',
            beforeSend: function(){
                $(btnClick).addClass('loading');
                if(isTransAll){
                    etsTransFunc.showPopupTranslating(0, 'megamenu', 0, formData.trans_source, formData.trans_target, formData.trans_option);
                }
                else
                    etsTransFunc.showTranslatingField();
            },
            success: function (res) {
                if(res.success){
                    if(isTransAll){
                        var transData = res.data || null;
                        if(transData){
                            etsTransFunc.updateDataTranslating(transData.nb_text, transData.nb_char);
                        }
                    }
                    else{
                        var transData = res.trans_data || null;
                        if(transData){
                            Object.keys(transData).forEach(function (idLang) {
                                Object.keys(transData[idLang]).forEach(function (key) {
                                    $('.mm_menu_form.mm_pop_up #'+key+idLang).val(transData[idLang][key]);
                                });
                            });
                        }
                        $('#etsTransModalTrans').modal('hide');
                    }
                    if(typeof res.no_trans !== 'undefined' && res.no_trans){
                        etsTransFunc.showSuccessMessage(etsTransFunc.trans('no_text_trans'));
                    }
                    else{
                        etsTransFunc.showSuccessMessage(res.message);
                    }
                }
                else{
                    var errorMessage = res.errors || res.message;
                    etsTransFunc.showErrorMessage(errorMessage);
                }
                $('.ets-trans-modal.show,.modal-backdrop.show,.modal-backdrop.in,.ets-trans-modal.in').remove();
                $('body').removeClass('etsTransPopupActive').removeClass('modal-open');
            },
            complete: function(){
                $(btnClick).removeClass('loading');
                if(isTransAll){
                    etsTransFunc.setTranslateDone();
                }
                else
                    etsTransFunc.hideTranslatingField();
                $('.ets-trans-modal.show,.modal-backdrop.show,.modal-backdrop.in,.ets-trans-modal.in').remove();
                $('body').removeClass('etsTransPopupActive').removeClass('modal-open');
            }
        });
    },
    getInputData: function(idLang, fieldTrans){
        fieldTrans = fieldTrans || null;
        var transData = {};
        var boxSearch = null;
        if(!fieldTrans){
            boxSearch = $('.mm_menu_form.mm_pop_up .mm_form .translatable-field.lang-'+idLang);
        }
        else{
            boxSearch = $(fieldTrans).find('.translatable-field.lang-'+idLang);
        }
        boxSearch.each(function () {
            var input = $(this).find('input[type=text], textarea');
            if(input.attr('id').indexOf('url_'+idLang) === -1){
                var keyInput = etsTransMegamenu.getInputKey(input.attr('id'), idLang);
                transData[keyInput] = input.val();
            }
        });
        return transData;
    },
    getFormData: function(langSource, langTarget, transOption, fieldTrans){
        var transData = {};
        transData.source = etsTransMegamenu.getInputData(langSource, fieldTrans);
        transData.target = {};
        $.each(langTarget, function(i, idLang){
            transData.target[idLang] = {};
            var transLangData = etsTransMegamenu.getInputData(idLang, fieldTrans);
            switch (transOption) {
                case 'only_empty':
                    Object.keys(transLangData).forEach(function (k) {
                        if(!transLangData[k].trim()){
                            transData.target[idLang][k] = 1;
                        }
                        else{
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'both':
                    Object.keys(transLangData).forEach(function (k) {
                        if(!transLangData[k].trim() || transLangData[k].trim().toLowerCase() == transData.source[k].trim().toLowerCase()){
                            transData.target[idLang][k] = 1;
                        }
                        else{
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'same_source':
                    Object.keys(transLangData).forEach(function (k) {
                        if(transLangData[k].trim().toLowerCase() == transData.source[k].trim().toLowerCase()){
                            transData.target[idLang][k] = 1;
                        }
                        else{
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'all':
                    Object.keys(transLangData).forEach(function (k) {
                        transData.target[idLang][k] = 1;
                    });
                    break;
            }
        });
        return transData;
    },
    getFormConfig: function(btnClicked, isTransAll, fieldTrans){
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'GET',
            dataType: 'json',
            data: {
                etsTransGetFormTranslate: 1,
                isNewTemplate: ETS_TRANS_IS_NEW_TEMPLATE,
                pageId: '',
                pageType: 'megamenu',
                isDetailPage: 1,
                isTransAll: isTransAll || 0,
                fieldTrans: fieldTrans || '',
                resetTrans: 0
            },
            beforeSend: function () {
                if (btnClicked) {
                    $(btnClicked).addClass('loading');
                }
            },
            success: function (res) {
                if (res.success) {
                    $('#etsTransModalTrans').remove();
                    if($('.mm_popup_overlay .mm_pop_up').length && $(btnClicked).closest('.mm_popup_overlay').length)
                        $('.mm_popup_overlay .mm_pop_up:not(.mm_config_form)').prepend(res.form);
                    else{
                        if($('#content.bootstrap').length){
                            $('#content.bootstrap').append(res.form);
                        }
                        else
                            $('body').append(res.form);
                    }

                    etsTransFunc.showPopupTrans();
                }
            },
            complete: function () {
                if (btnClicked) {
                    $(btnClicked).removeClass('loading');
                }
            }
        });
    },
    getInputKey: function(inputName, idLang){
        var regex = new RegExp(idLang+'$', 'g');
        return inputName.replace(regex, '');
    },
    analysisBeforeTranslate: function(pageType, formData, offset){
        $('#etsTransPopupAnalyzing').addClass('active');
        etsTransFunc.hidePopupTrans();
        etsTransMegamenu.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransAnalyzing: 1,
                pageType: pageType,
                formData: formData,
                offset: offset
            },
            success: function(res){
                if(res.success){
                    var resData = res.data || {};
                    if(Object.keys(resData).length){
                        formData.nb_text = parseInt(formData.nb_text) + resData.nb_text;
                        formData.nb_char = parseInt(formData.nb_char) + resData.nb_char;
                        formData.nb_money = parseFloat(formData.nb_money) + resData.nb_money;
                        if(resData.stop != 1){
                            etsTransMegamenu.analysisBeforeTranslate(pageType, formData, resData.offset);
                        }
                        else{
                            $('#etsTransPopupAnalyzing').removeClass('active');
                            etsTransFunc.showAnalysisCompleted(pageType, formData, resData.total_item || 0);
                        }
                    }
                }
                else{
                    $('#etsTransPopupAnalyzing').removeClass('active');
                    if(res.message)
                        etsTransFunc.showErrorMessage(res.message);
                    else
                        etsTransFunc.showErrorMessage('Has error');
                }
            },
            complete: function(){

            },
            error: function () {
                $('#etsTransPopupAnalyzing').removeClass('active');
                etsTransFunc.showErrorMessage('Has error');
            }
        });
    },
    getItemType: function(mmObject){
        switch (mmObject) {
            case 'MM_Menu':
                return 'menu';
            case 'MM_Tab':
                return 'tab';
            case 'MM_Block':
                return 'block';
            default:
                return '';
        }
    },
    colData: {
        menu: {
            'title_': 'title',
            'bubble_text_': 'bubble_text',
        },
        tab: {
            'title_': 'title',
            'bubble_text_': 'bubble_text',
        },
        block: {
            'title_': 'title',
            'content_': 'content',
        }
    },
    stopTranslatePage: function () {
        if (etsTransMegamenu.ajaxXhrTranslatePage && etsTransMegamenu.ajaxXhrTranslatePage.readyState != 4) {
            etsTransMegamenu.ajaxXhrTranslatePage.abort();
        }
    },
};
$(document).ready(function () {
    $('.ets_megamenu_tool_bar .ets_megamenu_buttons').prepend(etsTransMegamenu.renderBtnTransAllModule());

    $(document).on('click', '.mm_add_menu, .mm_add_block, .mm_add_tab', function () {
        setTimeout(function () {
            etsTransMegamenu.addBtnTransToInput();
        }, 100);
    });

    $(document).ajaxSuccess(function (event, xhr, settings) {
        var url = settings.url;
        if (etsTransMegamenu.getParameterByName('configure', url) !== 'ets_megamenu') {
            return;
        }
        etsTransMegamenu.addBtnTransToInput();
    });

    $(document).on('click', '.js-ets-trans-mm-item', function () {
        if(ETS_TRANS_IS_AUTO_CONFIG){
            var langTarget = ETS_TRANS_DEFAULT_CONFIG.lang_target || '';
            var formData = {
                trans_option: ETS_TRANS_DEFAULT_CONFIG.field_option,
                trans_source: ETS_TRANS_DEFAULT_CONFIG.lang_source,
                trans_target: langTarget.split(','),
            };
            formData.trans_data = etsTransMegamenu.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, $(this).parent()[0]);
            formData.page_id = $(this).closest('form').find('input[name=itemId]').val();
            formData.menu_type = etsTransMegamenu.getItemType($(this).closest('form').find('input[name=mm_object]').val());
            formData.col_data = etsTransMegamenu.colData[formData.menu_type];
            etsTransMegamenu.translate(this, formData);
        }
        else{
            var fieldTrans = $(this).parent().find('input[type=text], textarea').attr('id');
            etsTransMegamenu.getFormConfig(this, 0, fieldTrans);
        }
        return false;
    });

    $(document).on('click', '.js-ets-trans-mm-form', function () {
        etsTransMegamenu.getFormConfig(this, 0, 'panel');
        return false;
    });
    $(document).on('click', '.js-ets-trans-mm-all', function () {
        etsTransMMItemClick = 'js-ets-trans-mm-all';
        etsTransMegamenu.getFormConfig(this, 1);
        return false;
    });

    $(document).on('click','.js-ets-trans-analysis-text', function () {
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.nb_text = 0;
        formData.nb_char = 0;
        formData.nb_money = 0;
        if(typeof formData.trans_target == 'undefined' || !formData.trans_target){
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        var $this = $(this);
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            data: {
                etsTransGetFormAnalysis: 1,
            },
            dataType: 'json',
            beforeSend: function () {
                $this.prop('disabled', true);
                $this.addClass('loading');
            },
            success: function (res) {
                if(res.success){
                    $('#etsTransModalTrans').find('.ets-trans-content').html(res.form_html);
                    $this.parents('.ets-trans-modal').removeClass('ets_modify');
                    etsTransMegamenu.analysisBeforeTranslate('megamenu', formData, 1);
                    $('#etsTransModalTrans .js-ets-trans-analysis-text').addClass('hide');
                }
            },
            complete: function () {
                $this.prop('disabled', false);
                $this.removeClass('loading');
            }
        });

        return false;
    });

    $(document).on('click','.js-ets-trans-btn-translate-page, .js-ets-trans-analysis-accept', function () {
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        if(typeof formData.trans_target == 'undefined' || !formData.trans_target){
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        etsTransFunc.hideAnalysisCompleted();
        etsTransFunc.hidePopupTrans();
        var fieldTrans = $(this).attr('data-field') || '';
        var fieldEl = null;
        var btnClick = null;
        if(fieldTrans == 'panel'){
            fieldEl = $('.mm_menu_form.mm_pop_up').find('.panel')[0];
            btnClick = $('.mm_menu_form.mm_pop_up').find('.js-ets-trans-mm-form')[0];
        }
        else{
            if(fieldTrans) {
                fieldEl = $('.mm_menu_form.mm_pop_up').find('#' + fieldTrans).closest('.translatable-field').parent()[0];
                btnClick = $('.mm_menu_form.mm_pop_up').find('#' + fieldTrans).closest('.translatable-field').parent().find('.js-ets-trans-mm-item')[0];
            }
        }

        if($(this).hasClass('js-ets-trans-analysis-accept')){
            etsTransMegamenu.translate(btnClick, formData, 1);
            return false;
        }
        var isTransAll = $(this).attr('data-trans-all') || 0;
        formData.trans_data = etsTransMegamenu.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, fieldEl);
        formData.page_id = $('.mm_menu_form.mm_pop_up .panel').find('input[name=itemId]').val() || 0;
        formData.menu_type = etsTransMegamenu.getItemType($('.mm_menu_form.mm_pop_up .panel').find('input[name=mm_object]').val());
        formData.col_data = etsTransMegamenu.colData[formData.menu_type];
        etsTransMegamenu.translate(btnClick, formData, isTransAll);
        $('.ets-trans-modal.show,.modal-backdrop.show,.modal-backdrop.in,.ets-trans-modal.in').remove();
        $('body').removeClass('etsTransPopupActive').removeClass('modal-open');
        return false;
    });
    $(document).on('hidden.bs.modal','#etsTransModalTrans .close,#etsTransModalTrans .btn-group-translate-close', function (e) {
        etsTransMegamenu.stopTranslatePage();
    });
});