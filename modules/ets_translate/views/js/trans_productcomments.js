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
var etsTransModulePc = {
    ajaxXhrTranslatePage: null,
    pcType: null,
    txtTransAll: 'G-translate',
    renderBtnTransItem: function (extraClass) {
        return '<button class="btn btn-default ets-trans-btn-trans-field-item js-ets-trans-pc-item '+(extraClass ?? '')+'" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i></button>';
    },
    renderBtnTransForm: function (extraClass) {
        return '<button class="btn btn-default js-ets-trans-pc-form pull-right '+(extraClass ? extraClass : '')+'" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"></path></svg></i> ' + etsTransFunc.trans('g-translate') + '</button>';
    },
    renderBtnTransAllModule: function (className , hasLi, textTitle) {
        className = className || '';
        hasLi = hasLi || false;
        textTitle = textTitle || etsTransModulePc.txtTransAll;
        var btn =  '<a class="toolbar_btn ets-trans-pc-all js-ets-trans-pc-all '+(typeof ETS_TRANS_IS_1780 != 'undefined' && ETS_TRANS_IS_1780 ? 'is_ps1780' : '')+' '+className+'">' +
            '<span title="' + textTitle + '" data-toggle="tooltip" class="label-tooltip" data-original-title="'+ textTitle +'" data-html="true" data-placement="top"><i class="ets_svg_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"></path></svg></i> <div style="display: none;">'+ etsTransFunc.trans('g-translate') +'</div></span>' +
            '</a>';
        if(hasLi){
            return '<li>'+btn+'</li>';
        }
        return btn;
    },
    renderBtnTransListItem: function(id, hasLi){
        hasLi = hasLi || false;
        var btn = '<li><a href="#" class="js-ets-trans-pc-list-item" data-id="'+(id || '')+'"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> '+etsTransFunc.trans('trans_review')+'</a></li>';
        if(hasLi){
            return '<li class="divider"></li>'+btn+'';
        }
        return btn;
    },
    renderBtnTransBulk: function () {
        return '<li><a href="javascript:void(0)" class="js-ets-trans-pc-list-bulk"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> '+etsTransFunc.trans('g-translate')+'</a></li>';
    },
    addBtnTransToInput: function () {
        var extraInputTrans = ['ets_rv_comment','ets_rv_reply_comment'];

        if(ETS_TRANS_ENABLE_TRANS_FIELD) {
            $('.ets_rv_form .translatable-field').each(function () {
                if (!$(this).parent().find('.js-ets-trans-pc-item').length) {
                    var idInput = $(this).find('input[type=text],textarea').first().attr('id') || null;
                    if (idInput && idInput.indexOf('url_') === -1 && !$(this).find('input[type=file]').length) {
                        $(this).parent().addClass('add_ggtran_review').append(etsTransModulePc.renderBtnTransItem());
                    }else{
                        var $this = $(this);
                        $.each(extraInputTrans, function (key,el){
                            if ($this.find('textarea.'+el).length) {
                                var extraClass = 'ets_trans_rv_cz';
                                if ($this.parent().find('.translatable-field>.col-lg-10').length)
                                    extraClass += ' trans-has-col-10';
                                $this.parent().append(etsTransModulePc.renderBtnTransItem(extraClass));
                                var firstId = $this.parent().find('textarea.'+el).first().attr('name');
                                var extraId = etsTransModulePc.generateRandString(5);
                                var prefixId = firstId.replace(/[1-9]+$/, '');

                                $this.parent().find('textarea.'+el).each(function () {
                                    var idLang = $(this).attr('name').replace(prefixId, '');
                                    $(this).attr('id', extraId+'_'+prefixId+idLang);
                                });
                            }
                        });
                    }
                }
            });
        }

        if(!$('.ets_rv_form .panel-footer .js-ets-trans-pc-form').length) {
            var extraClass = '';
            if ($('.ets_rv_form .panel-footer .edit').length)
                extraClass = 'trans-view-rv';
            $('.ets_rv_form .panel-footer').append(etsTransModulePc.renderBtnTransForm(extraClass));
        }
    },
    generateRandString(length) {
        var characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        var charactersLength = characters.length;
        for ( let i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    },

    translate: function (btnClick, formData, isTransAll, loop, $thisBtn) {
        loop = loop || 0;
        isTransAll = isTransAll || 0;
        isTransAll = parseInt(isTransAll);
        etsTransModulePc.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            data: {
                etsTransModulePc: 1,
                isTransAll: isTransAll,
                formData: formData,
                pcType: etsTransModulePc.pcType,
            },
            dataType: 'json',
            beforeSend: function () {
                $(btnClick).prop('disabled', true);
                $(btnClick).addClass('loading');
                if($thisBtn) {
                    $thisBtn.addClass('loading');
                    $thisBtn.prop('disabled', true);
                }
                if (isTransAll) {
                    if(!loop){
                        etsTransFunc.showPopupTranslating(1, 'pc', 0, formData.trans_source, formData.trans_target, formData.trans_option);
                        etsTransFunc.setConfigTranslating('pc',formData.trans_source, formData.trans_target, formData.trans_option);
                    }
                } else
                    etsTransFunc.showTranslatingField();

            },
            success: function (res) {
                console.log('trans product comment', isTransAll, res)
                if (res.success) {
                    if (isTransAll) {
                        var transData = res.data || null;
                        if (transData) {
                            formData.nb_text = parseInt(formData.nb_text) + transData.nb_text;
                            formData.nb_char = parseInt(formData.nb_char) + transData.nb_char;
                            formData.nb_money = parseFloat(formData.nb_money) + transData.nb_money;
                            if (!transData.stop) {
                                formData.offset = transData.offset;
                                if (typeof transData.page_type !== 'undefined') {
                                    formData.page_type = transData.page_type;
                                }
                                etsTransModulePc.translate(btnClick, formData, 1, 1);
                                etsTransFunc.updateDataTranslating(formData.nb_text, formData.nb_char);
                                etsTransFunc.updateTotalFilePath(transData.offset);
                            } else {
                                etsTransFunc.updateDataTranslating(formData.nb_text, formData.nb_char);
                                etsTransFunc.updateTotalFilePath(transData.offset);
                                etsTransFunc.setTranslateDone();
                            }
                        }
                    }
                    else {
                        var transData = res.trans_data || null;
                        if (transData) {
                            Object.keys(transData).forEach(function (idLang) {
                                Object.keys(transData[idLang]).forEach(function (key) {
                                    if ($('#' + key + idLang).attr('id') && $('#' + key + idLang).attr('id').indexOf('keywords') !== -1) {
                                        $('#' + key + idLang).val(transData[idLang][key].replace(/\|/g, ','));
                                    } else {
                                        $('#' + key + idLang).val(transData[idLang][key]);
                                    }

                                    if (typeof tinyMCE !== "undefined" && $('#' + key + idLang).hasClass('autoload_rte')) {
                                        $('#' + key + idLang).prev().find('iframe').next('div').hide();
                                        tinyMCE.get(key + idLang).setContent(transData[idLang][key]);
                                    }
                                    if ($('#' + key + idLang).hasClass('tagify')) {
                                        etsTransFunc.addKeywords('#' + key + idLang);
                                    }
                                });
                            });
                        }
                        $('#etsTransModalTrans').modal('hide');
                    }
                    if ((typeof res.no_trans !== 'undefined' && res.no_trans) || (isTransAll && !formData.nb_text)) {
                        etsTransFunc.showSuccessMessage(etsTransFunc.trans('no_text_trans'));
                    }
                    else if(res.message)
                        etsTransFunc.showSuccessMessage(res.message, 8000);
                }
                else{
                    var errorMessage = res.message || res.errors;
                    etsTransFunc.showErrorMessage(errorMessage);
                    if ($('.js-ets-tran-btn-pause-translate').length ){
                        $('.js-ets-tran-btn-pause-translate').click();
                    }
                }
            },
            complete: function () {
                $(btnClick).removeClass('loading');
                $(btnClick).prop('disabled', false);
                if (isTransAll) {
                    //
                } else
                    etsTransFunc.hideTranslatingField();
                if($thisBtn) {
                    $thisBtn.removeClass('loading');
                    $thisBtn.prop('disabled', false);
                }
            }
        });
    },
    getInputData: function (idLang, fieldTrans) {
        fieldTrans = fieldTrans || null;
        var transData = {};
        var boxSearch = null;
        var classPopup = '#ets-rv-product-comments-list';
        if ($('#ets-rv-product-questions-list').length){
            classPopup = '#ets-rv-product-questions-list';
        }
        var isView = $(classPopup).length;
        if (!fieldTrans) {
            if (isView)
                boxSearch = $('.ets_rv_form #ets-rv-product-comments-list .translatable-field.lang-' + idLang);
            else
                boxSearch = $('.ets_rv_form .translatable-field.lang-' + idLang);
        } else {
            boxSearch = $(fieldTrans).find('.translatable-field.lang-' + idLang);
        }

        boxSearch.each(function () {
            if ((isView && $(this).closest(classPopup).length) || !isView){
                var input = $(this).find('input[type=text], textarea');
                var idInput = input.attr('id');
                if (idInput && idInput.indexOf('url_' + idLang) === -1 && !input.parent().find('input[type=file]').length) {
                    var keyInput = etsTransModulePc.getInputKey(idInput, idLang);
                    if (idInput.indexOf('keywords') !== -1) {
                        var keywords = input.tagify('serialize');
                        transData[keyInput] = keywords.replace(/,/g, '|');
                    } else
                        transData[keyInput] = input.val();
                }
            }

        });
        return transData;
    },
    getFormData: function (langSource, langTarget, transOption, fieldTrans) {
        var transData = {};
        transData.source = etsTransModulePc.getInputData(langSource, fieldTrans);
        transData.target = {};

        $.each(langTarget, function (i, idLang) {
            transData.target[idLang] = {};
            var transLangData = etsTransModulePc.getInputData(idLang, fieldTrans);
            switch (transOption) {
                case 'only_empty':
                    Object.keys(transLangData).forEach(function (k) {
                        if (!transLangData[k].trim()) {
                            transData.target[idLang][k] = 1;
                        } else {
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'both':
                    Object.keys(transLangData).forEach(function (k) {
                        if (!transLangData[k].trim() || transLangData[k].trim().toLowerCase() == transData.source[k].trim().toLowerCase()) {
                            transData.target[idLang][k] = 1;
                        } else {
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'same_source':
                    Object.keys(transLangData).forEach(function (k) {
                        if (transLangData[k].trim().toLowerCase() == transData.source[k].trim().toLowerCase()) {
                            transData.target[idLang][k] = 1;
                        } else {
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
    getFormConfig: function (btnClicked, isTransAll, fieldTrans, ids, resetTrans) {
        resetTrans = resetTrans || 0;
        var rv_comment_id = 0;
        if ($(btnClicked).hasClass('js-ets-trans-pc-form')) {
            if ($(btnClicked).hasClass('trans-view-rv'))
                rv_comment_id = $('#ets-rv-product-comments-list .ets-rv-product-comment-list-item').attr('data-product-comment-id') || 0;
            else {
                rv_comment_id = $('.ets_rv_form').find('input[name=id_ets_rv_product_comment]').val();
            }
        }
        if ($(btnClicked).hasClass('js-ets-trans-pc-list-item'))
            rv_comment_id = ids;
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'GET',
            dataType: 'json',
            data: {
                etsTransGetFormTranslate: 1,
                isNewTemplate: ETS_TRANS_IS_NEW_TEMPLATE,
                pageId: ids,
                pageType: 'pc',
                pcType: etsTransModulePc.pcType,
                isDetailPage: 1,
                isTransAll: isTransAll || 0,
                fieldTrans: fieldTrans || '',
                resetTrans: resetTrans,
                autoDetectLang: ETS_TRANS_AUTO_DETECT_LANG,
                rv_comment_id: rv_comment_id,
            },
            beforeSend: function () {
                if (btnClicked) {
                    $(btnClicked).addClass('loading');
                    $(btnClicked).prop('disabled', true);
                }
            },
            success: function (res) {
                if (res.success) {
                    $('#etsTransModalTrans').remove();
                    if($('#content.bootstrap').length){
                        $('#content.bootstrap').append(res.form);
                    }
                    else{
                        $('body').append(res.form);
                    }
                    etsTransFunc.showPopupTrans();
                    if ($(btnClicked).hasClass('trans-view-rv') || $(btnClicked).hasClass('js-ets-trans-pc-list-bulk')){
                        $('.js-ets-trans-btn-translate-page').addClass('trans-view-rv');
                    }
                    if ($(btnClicked).hasClass('js-ets-trans-pc-list-item')){
                        $('.js-ets-trans-btn-translate-page').addClass('trans-list-item');
                    }
                }
            },
            complete: function () {
                if (btnClicked) {
                    $(btnClicked).removeClass('loading');
                    $(btnClicked).prop('disabled', false);
                }
            }
        });
    },
    getInputKey: function (inputName, idLang) {
        var regex = new RegExp(idLang + '$', 'g');
        return inputName.replace(regex, '');
    },
    analysisBeforeTranslate: function (pageType, formData) {
        $('#etsTransPopupAnalyzing').addClass('active');
        etsTransFunc.hidePopupTrans();
        etsTransModulePc.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransAnalyzing: 1,
                pageType: pageType,
                formData: formData,
                pcType: etsTransModulePc.pcType,
            },
            success: function (res) {
                if (res.success) {
                    var resData = res.data || {};
                    if (Object.keys(resData).length) {
                        formData.nb_text = parseInt(formData.nb_text) + resData.nb_text;
                        formData.nb_char = parseInt(formData.nb_char) + resData.nb_char;
                        formData.nb_money = parseFloat(formData.nb_money) + resData.nb_money;
                        if (resData.stop != 1) {
                            formData.offset = resData.offset;
                            etsTransModulePc.analysisBeforeTranslate(pageType, formData);
                        } else {
                            $('#etsTransPopupAnalyzing').removeClass('active');
                            etsTransFunc.showAnalysisCompleted(pageType, formData, resData.total_item || 0);
                        }
                    }
                } else {
                    $('#etsTransPopupAnalyzing').removeClass('active');
                    if(res.message)
                        etsTransFunc.showErrorMessage(res.message);
                    else
                        etsTransFunc.showErrorMessage('Has error');
                }
            },
            complete: function () {

            },
            error: function () {
                $('#etsTransPopupAnalyzing').removeClass('active');
                etsTransFunc.showErrorMessage('Has error');
            }
        });
    },
    pauseTranslate: function (btn, data) {
        $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            data: {
                etsTransPauseTranslate: 1,
                transInfo: data
            },
            dataType: 'json',
            beforeSend: function () {
                $(btn).addClass('loading');
                $(btn).prop('disabled', true);
            },
            success: function (res) {
                if(res.success){
                    etsTransFunc.setPauseTranslate(data);
                    etsTransFunc.showSuccessMessage(etsTransFunc.trans('pause_success'));
                }
            },
            complete: function () {
                $(btn).removeClass('loading');
                $(btn).prop('disabled', false);
            }
        });
    },
    stopTranslatePage: function () {
        if (etsTransModulePc.ajaxXhrTranslatePage && etsTransModulePc.ajaxXhrTranslatePage.readyState != 4) {
            etsTransModulePc.ajaxXhrTranslatePage.abort();
        }
    },
    addBtnTransToList: function(){
        if(!$('.js-ets-trans-pc-all').length)
            $('form .panel-heading-action').prepend(etsTransModulePc.renderBtnTransAllModule('list-toolbar-btn', false, etsTransModulePc.txtTransAll));
        var items = ['ets_rv_product_comment', 'ets_rv_comment', 'ets_rv_reply_comment'];

        $.each(items, function (i, el) {
            if(!$('#bulk_action_menu_'+el).next('.dropdown-menu').find('.js-ets-trans-pc-list-bulk').length){
                $('#bulk_action_menu_'+el).next('.dropdown-menu').append(etsTransModulePc.renderBtnTransBulk());
            }
            $('#table-'+el+' tbody tr').each(function () {
                var idPc = ($(this).find('td.id').text() || '').trim();
                if(!$(this).find('.js-ets-trans-pc-list-item').length){
                    $(this).find('td:last-child .btn-group-action .dropdown-menu').append(etsTransModulePc.renderBtnTransListItem(idPc,true));
                }
            });
        });

    },
    colData: {
        'title_': 'title',
        'content_': 'content',
    }
};
$(document).ready(function () {
    var etsPcController = etsTransFunc.getParameterByName('controller');
    switch (etsPcController) {
        case 'AdminEtsRVReviewsRatings':
        case 'AdminEtsRVReviews':
            etsTransModulePc.pcType = 'review'; //product_comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_reviews');
            break;
        case 'AdminEtsRVComments':
            etsTransModulePc.pcType = 'comment'; // comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_comments');
            break;
        case 'AdminEtsRVReplies':
            etsTransModulePc.pcType = 'reply'; // reply_comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_replies');
            break;
        case 'AdminEtsRVQuestions':
        case 'AdminEtsRVQuestionsAnswers':
            etsTransModulePc.pcType = 'question';//product_comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_questions');
            break;
        case 'AdminEtsRVQuestionComments':
            etsTransModulePc.pcType = 'question_comment'; // comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_comments_for_questions');
            break;
        case 'AdminEtsRVAnswers':
            etsTransModulePc.pcType = 'answer'; // comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_answers');
            break;
        case 'AdminEtsRVAnswerComments':
            etsTransModulePc.pcType = 'answer_comment'; //reply_comment
            etsTransModulePc.txtTransAll = etsTransFunc.trans('translate_all_comments_for_answers');
            break;
    }
    etsTransModulePc.addBtnTransToList();
    $(document).ajaxSuccess(function( event, xhr, settings ) {
        var listControllers = ['AdminEtsRVReviewsRatings','AdminEtsRVReviews', 'AdminEtsRVComments', 'AdminEtsRVReplies',
            'AdminEtsRVQuestions', 'AdminEtsRVQuestionComments', 'AdminEtsRVAnswers', 'AdminEtsRVAnswerComments','AdminEtsRVQuestionsAnswers'];
        if(listControllers.indexOf(etsTransFunc.getParameterByName('controller', settings.url)) !== -1){
            etsTransModulePc.addBtnTransToInput();
        }
        etsTransModulePc.addBtnTransToList();
    });


    $(document).on('click', '.js-ets-trans-pc-item', function (event) {
        event.preventDefault();
        if (ETS_TRANS_IS_AUTO_CONFIG) {
            var langTarget = ETS_TRANS_DEFAULT_CONFIG.lang_target || '';
            var formData = {
                trans_option: ETS_TRANS_DEFAULT_CONFIG.field_option,
                trans_source: ETS_TRANS_DEFAULT_CONFIG.lang_source,
                trans_target: langTarget.split(','),
            };
            formData.trans_data = etsTransModulePc.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, $(this).parent()[0]);
            if($(this).hasClass('ets_trans_rv_cz')){
                formData.page_id = 0;
            }
            else
                formData.page_id = $(this).closest('form').find('input[value=edit]').attr('id') || 0;
            formData.col_data = etsTransModulePc.colData;

            etsTransModulePc.translate(this, formData);
        } else {
            var fieldTrans = $(this).parent().find('input[type=text], textarea').attr('id');
            etsTransModulePc.getFormConfig(this, 0, fieldTrans);
        }
        return false;
    });

    $(document).on('click', '.js-ets-trans-pc-form', function (event) {
        event.preventDefault();
        etsTransModulePc.getFormConfig(this, 0, 'panel');
        return false;
    });
    $(document).on('click', '.js-ets-trans-pc-all', function () {
        etsTransModulePc.getFormConfig(this, 1);
    });

    $(document).on('click', '.js-ets-trans-analysis-text', function () {
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.nb_text = 0;
        formData.nb_char = 0;
        formData.nb_money = 0;
        formData.is_trans_all = 1;
        if (typeof formData.trans_target == 'undefined' || !formData.trans_target) {
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        formData.pc_type = etsTransModulePc.pcType;
        var $this = $(this);
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            data: {
                etsTransGetFormAnalysis: 1,
                pcType: etsTransModulePc.pcType
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
                    etsTransModulePc.analysisBeforeTranslate('pc', formData);
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

    $(document).on('click', '.js-ets-trans-btn-translate-page, .js-ets-trans-analysis-accept', function () {

        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);

        if (typeof formData.trans_target == 'undefined' || !formData.trans_target) {
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        etsTransFunc.hideAnalysisCompleted();
        etsTransFunc.hidePopupTrans();
        var fieldTrans = $(this).attr('data-field') || '';
        var fieldEl = null;
        var btnClick = null;
        if (fieldTrans == 'panel') {
            fieldEl = $('.ets_rv_form')[0];
            btnClick = $('.ets_rv_form').find('.js-ets-trans-pc-form')[0];
        } else {
            if (fieldTrans) {
                fieldEl = $('#' + fieldTrans).closest('.translatable-field').parent()[0];
                btnClick = $('#' + fieldTrans).closest('.translatable-field').parent().find('.js-ets-trans-pc-item')[0];
            }
        }
        var isTransAll = $(this).attr('data-trans-all') ?? 0;

        if (isTransAll != 0) {
            formData.nb_text = 0;
            formData.nb_char = 0;
            formData.nb_money = 0;
            formData.offset = 0;
        }
        if (!formData.page_id){
            formData.page_id = $('.ets_rv_form').find('input[name=id_ets_rv_product_comment]').val() || 0;
        }
        if (!formData.page_id){
            formData.page_id = $('#ets-rv-product-comments-list .ets-rv-product-comment-list-item').attr('data-product-comment-id') || 0;
        }
        if ($(this).hasClass('trans-view-rv')){
            formData.isTransView = 1;
        }
        if (!$(this).hasClass('trans-list-item')){
            formData.col_data = etsTransModulePc.colData;
        }

        if (isTransAll == 1){
            etsTransFunc.setResumeTranslate({
                pageType: 'pc',
                nbTranslated: 0,
                nbCharTranslated: 0,
                langSource: formData.trans_source,
                langTarget: formData.trans_target,
                fieldOption: formData.trans_option,
            });
        }
        formData.pc_type = etsTransModulePc.pcType;
        if ($(this).hasClass('js-ets-trans-analysis-accept')) {
            etsTransModulePc.translate(btnClick, formData, 1, 0, $(this));
            return false;
        }
        formData.trans_data = etsTransModulePc.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, fieldEl);
        etsTransModulePc.translate(btnClick, formData, isTransAll,0, $(this));
        return false;
    });

    $(document).on('click', '.js-ets-trans-pc-list-bulk', function () {
        var ids = [];
        var items = ['ets_rv_product_comment', 'ets_rv_comment', 'ets_rv_reply_comment'];
        $.each(items, function (i, el) {
            $('#table-'+el+' tbody').find('input[name^="ets_rv_"][type="checkbox"]:checked').each(function () {
                if(ids.indexOf($(this).val()) === -1)
                    ids.push($(this).val());
            });
        });

        if(!ids || !ids.length){
            alert(etsTransFunc.trans('no_item_to_trans'));
            return false;
        }
        etsTransModulePc.getFormConfig(this, 0, null, ids);
        return false;
    });
    $(document).on('click', '.js-ets-trans-pc-list-item', function (event) {
        event.preventDefault();
        var $this = $(this);
        setTimeout(function () {
            $this.closest('.dropdown-menu').prev().prev().removeClass('active');
        },100);
        var id = $(this).attr('data-id') || null;
        if(!id){
            alert(etsTransFunc.trans('can_not_trans_item'));
            return false;
        }
        etsTransModulePc.getFormConfig(this, 0, null, id);
        $(this).parents('.btn-group.open').removeClass('open');
        $(this).parents('.btn-group').find('a.btn').addClass('active');
        return false;
    });
    $(document).on('click', '.js-ets-tran-btn-pause-translate', function () {
        etsTransModulePc.stopTranslatePage();
        var dataPause = {};
        dataPause.pageType = $(this).attr('data-page-type');
        dataPause.nbTranslated = $(this).attr('data-nb-translated');
        dataPause.nbCharTranslated = $(this).attr('data-nb-char');
        dataPause.langSource = $(this).attr('data-lang-source');
        dataPause.langTarget = $(this).attr('data-lang-target');
        dataPause.fieldOption = $(this).attr('data-field-option');
        dataPause.nbPath = $(this).attr('data-total-path');
        dataPause.pcType = etsTransModulePc.pcType;
        etsTransModulePc.pauseTranslate(this, dataPause);
        return false;
    });
    $(document).on('click', '.js-ets-trans-btn-resume-translate', function () {
        var dataResume = {};
        dataResume.pageType = $(this).attr('data-page-type');
        dataResume.nbTranslated = $(this).attr('data-nb-translated');
        dataResume.nbCharTranslated = $(this).attr('data-nb-char');
        dataResume.langSource = $(this).attr('data-lang-source');
        dataResume.langTarget = $(this).attr('data-lang-target');
        dataResume.fieldOption = $(this).attr('data-field-option');
        etsTransFunc.setResumeTranslate(dataResume);
        var offset = $(this).attr('data-total-path') || 0;
        etsTransFunc.updateTotalFilePath(offset);
        var formData = {};
        formData.pageType = dataResume.pageType;
        formData.isDetailPage = 0;
        formData.page_id = '';
        formData.trans_source = dataResume.langSource;
        formData.trans_target = dataResume.langTarget.split(',');
        formData.trans_option = dataResume.fieldOption;
        formData.trans_all = 1;
        formData.nb_translated = dataResume.nbTranslated;
        formData.nb_char_translated = dataResume.nbCharTranslated;
        formData.offset = offset;
        formData.nb_text = formData.nb_translated || 0;
        formData.nb_char = formData.nb_char_translated || 0;
        formData.nb_money = etsTransFunc.getNbMoney(formData.nb_char);
        etsTransModulePc.translate(this, formData, 1);
    });

    $(document).on('click', '.js-ets-trans-translate-from-resume', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.trans_target = formData.trans_target.split(',');
        formData.trans_all = 1;
        formData.isDetailPage = 0;
        etsTransFunc.showPopupTranslating(1, formData.pageType, formData.total_item, formData.trans_source, formData.trans_target, formData.trans_option);
        etsTransFunc.setResumeTranslate({
            pageType: formData.pageType,
            nbTranslated: formData.nb_translated,
            nbCharTranslated: formData.nb_char_translated,
            langSource: formData.trans_source,
            langTarget: formData.trans_target,
            fieldOption: formData.trans_option,
        });
        etsTransFunc.updateDataTranslating(formData.nb_translated, formData.nb_char_translated, formData.total_item);
        formData.offset = formData.nb_path;
        formData.nb_text = formData.nb_translated || 0;
        formData.nb_char = formData.nb_char_translated || 0;
        formData.nb_money = etsTransFunc.getNbMoney(formData.nb_char);
        etsTransFunc.updateTotalFilePath(formData.offset);
        etsTransModulePc.translate(this, formData, 1);
    });
    $(document).on('click', '.js-ets-trans-translate-from-zero', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        etsTransFunc.hidePopupTrans();
        etsTransModulePc.getFormConfig(this, 1, null, null, 1);
    });

    $(document).on('hidden.bs.modal','#etsTransModalTrans .close,#etsTransModalTrans .btn-group-translate-close', function (e) {
        etsTransModulePc.stopTranslatePage();
    });
});