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
var etsTransCombination = {
    ajaxXhrTranslatePage: null,
    stopTranslate: false,
    fields: {
        "ps17": [
            'combination_form_stock_available_now_label_',
            'combination_form_stock_available_later_label_'
        ],
        "ps810": [
            'combination_form_stock_available_now_label_',
            'combination_form_stock_available_later_label_'
        ]
    },
    colData: {
        "ps17": {
            'combination_form_stock_available_now_label_': 'available_now',
            'combination_form_stock_available_later_label_': 'available_later'
        },
        "ps810": {
            'combination_form_stock_available_now_label_': 'available_now',
            'combination_form_stock_available_later_label_': 'available_later'
        }
    },
    init: function () {
        this.initBtnTransCombination();
    },
    initBtnTransCombination: function () {
        var fields = ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2 ? etsTransCombination.fields.ps810 : etsTransCombination.fields.ps17;
        var classForTransBoundary = 'ets-trans-field-boundary ' + (ETS_TRANS_GTE_810 ? 'ets-trans-810' : ETS_TRANS_IS_1780 ? 'ets-trans-17' : '');
        $.each(fields, function (i, el) {
            $('[id*=' + el + ']').each(function () {
                if (!$(this).closest('.translations').find('.js-ets-trans-btn-trans-field-item ').length) {
                    $(this).closest('.translations').addClass(classForTransBoundary).append(etsTransCombination.renderBtnTransFieldItem(el)).parent().addClass('ets_button_top');
                    if ($(this).is('textarea')) {
                        $(this).closest('.translations').addClass('form-helper-editor');
                    }
                }
                if (ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2) {
                    if (!$(this).closest('.locale-input-group').find('.js-ets-trans-btn-trans-field-item ').length && !$(this).is('button')) {
                        $(this).closest('.locale-input-group').addClass(classForTransBoundary).append(etsTransCombination.renderBtnTransFieldItem(el)).parent().addClass('ets_button_top');
                        if ($(this).is('textarea')) {
                            $(this).closest('.translations').addClass('form-helper-editor');
                        }
                    }
                }
            });
        });
    },

    renderBtnTransFieldItem: function (field,className) {
        className = className || '';
        field = field || '';
        return '<button type="button" class="ets-trans-button ets-trans-btn-trans-combination-field-item has_tooltip btn btn-sm btn-outline-secondary ets-trans-btn-trans-field-item js-ets-trans-btn-trans-field-item '+className+'" title="'+ etsTransCombination.trans('g-translate') +'" data-field="' + field + '">'
            + '<span class="ets_tooltip">'+etsTransCombination.trans('g-translate')+'</span><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> '
            + '</button>';
    },
    trans: function(key){
        return etsTransText[key] || key;
    },
    getTransData: function (lang_source, langs_target, trans_option, fieldTrans) {
        var transData = {};
        var dataTransSource = etsTransCombination.getFormData(lang_source, fieldTrans);

        if (!dataTransSource) {
            return;
        }
        transData.source = dataTransSource;
        transData.target = {};
        $.each(langs_target, function (i, id_lang) {
            var dataTarget = etsTransCombination.getFormData(id_lang, fieldTrans);
            var targetOption = {};
            if (trans_option == 'both') { //target field trans is same source field or empty
                Object.keys(dataTarget).forEach(function (key) {
                    if ((dataTarget[key].trim().toLowerCase() == dataTransSource[key].trim().toLowerCase() || dataTarget[key].trim() == '') && dataTransSource[key].trim()) {
                        targetOption[key] = 1;
                    } else {
                        targetOption[key] = 0;
                    }
                });
            } else if (trans_option == 'only_empty') { // if target field trans is empty
                Object.keys(dataTarget).forEach(function (key) {
                    if (dataTarget[key].trim() == '') {
                        targetOption[key] = 1;
                    } else {
                        targetOption[key] = 0;
                    }
                });
            } else if (trans_option == 'same_source') { // if target field trans same content with source field
                Object.keys(dataTarget).forEach(function (key) {
                    if (dataTarget[key].trim().toLowerCase() == dataTransSource[key].trim().toLowerCase()) {
                        targetOption[key] = 1;
                    } else {
                        targetOption[key] = 0;
                    }
                });
            } else if (trans_option == 'all') { // don't care conditions, translate all
                Object.keys(dataTarget).forEach(function (key) {
                    targetOption[key] = 1;
                });
            }
            transData.target[id_lang] = targetOption;
        });
        return transData;
    },
    getFormData: function (id_lang, fieldTrans) {
        if (!id_lang) {
            return null;
        }
        fieldTrans = fieldTrans || null;
        var dataTrans = {};
        if (fieldTrans) {
            if (fieldTrans.indexOf('keywords') !== -1) {
                dataTrans[fieldTrans] = ($('#' + fieldTrans + id_lang).val() || '').replace(/,/g, '|');
            } else
                dataTrans[fieldTrans] = $('#' + fieldTrans + id_lang).val() || '';
        }
        return dataTrans;
    },
    translateCombination: function (formData, buttonTrans, pageType) {
        if (etsTransCombination.stopTranslate) {
            return;
        }
        var isDetailPage = 1;
        etsTransCombination.beforeTranslate(buttonTrans);

        etsTransCombination.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransTranslatePage: 1,
                pageType: pageType || etsTransPageType,
                isDetailPage: isDetailPage,
                formData: formData
            },
            success: function (res) {
                if (res.success) {
                    if (isDetailPage) {
                        var transResult = res.trans_data || {};
                        Object.keys(transResult).forEach(function (idLang) {
                            Object.keys(transResult[idLang]).forEach(function (key) {
                                if ($('#' + key + idLang).hasClass('js-taggable-field') || ($('#' + key + idLang).length && $('#' + key + idLang).attr('name').indexOf('keywords') !== -1)) {
                                    $('#' + key + idLang).val(transResult[idLang][key].replace(/\|/g, ','));
                                    if(!$('#' + key + idLang).parent().hasClass('ets-trans-field-translated-success')){
                                        $('#' + key + idLang).parent().addClass('ets-trans-field-translated-success');
                                    }
                                    $('#' + key + idLang).change();
                                    if ($('#' + key + idLang).hasClass('tagify')) {
                                        etsTransCombination.addKeywords('#' + key + idLang);
                                    }
                                } else {
                                    $('#' + key + idLang).val(transResult[idLang][key]);
                                    if(!$('#' + key + idLang).hasClass('ets-trans-field-translated-success')){
                                        $('#' + key + idLang).addClass('ets-trans-field-translated-success');
                                    }
                                }
                                if (typeof tinyMCE !== "undefined" && $('#' + key + idLang).hasClass('autoload_rte')) {
                                    console.log(key, idLang)
                                    $('#' + key + idLang).prev().find('iframe').next('div').hide();
                                    tinyMCE.get(key + idLang).setContent(transResult[idLang][key]);
                                    setTimeout(() => {
                                        tinyMCE.get(key + idLang).iframeElement.style.height = 'auto';
                                    }, 100);

                                    if(!$('#' + key + idLang).parent().hasClass('ets-trans-field-translated-success')){
                                        $('#' + key + idLang).parent().addClass('ets-trans-field-translated-success');
                                    }
                                }

                            });
                        });
                    }
                    etsTransCombination.afterTranslate(buttonTrans);
                    etsTransCombination.showSuccessMessage(res.message);
                    window.parent.document.querySelector('#combination-edit-modal .modal-footer .btn-primary').removeAttribute('disabled');
                } else {
                    if(res.message){
                        etsTransCombination.showErrorMessage(res.message);
                    }
                    else if (res.errors) {
                        etsTransCombination.showErrorTrans(res.errors);
                        etsTransCombination.afterTranslate(buttonTrans, false);
                    }
                }
            },
            complete: function () {
                etsTransCombination.afterTranslate(buttonTrans);
            },
            error: function (xhr) {
                etsTransCombination.afterTranslate(buttonTrans);
            }
        });
    },
    addKeywords: function (el) {
        var keywords = $(el).val();
        var splitKeywords = keywords.split(',');
        var oldKeywords = $(el).tagify('serialize');
        $.each(oldKeywords.split(','), function (i, item) {
            $(el).tagify('remove');
        });
        $.each(splitKeywords, function (i, item) {
            $(el).tagify('add', item);
        });
    },
    showErrorMessage: function (msg, time = 5000) {
        $.growl.error({message:msg, duration: time});
    },
    showSuccessMessage: function (msg, time = 5000) {
        $.growl.notice({message:msg, duration: time});
    },
    beforeTranslate: function (buttonTrans) {
        buttonTrans.addClass('active');
        buttonTrans.addClass('loading');
        buttonTrans.prop('disabled', true);
        buttonTrans.find('.text-btn-translate').html(etsTransCombination.trans('translating') + '...');
        $('#etsTransModalTrans').addClass('translating');
    },
    afterTranslate: function (buttonTrans, closeModal) {
        if (typeof closeModal === 'undefined') {
            closeModal = true;
        }
        buttonTrans.removeClass('active');
        buttonTrans.prop('disabled', false);
        buttonTrans.find('.text-btn-translate').html(etsTransCombination.trans('translate'));

        $('#etsTransModalTrans').removeClass('translating');
        $('#etsTransModalTrans .js-ets-trans-btn-strop-translate').remove();
        if (closeModal) {
            // etsTransFunc.hidePopupTrans();
        }
    },
}

$(document).ready(function () {
    etsTransCombination.init();

    $(document).on('click', '.js-ets-trans-btn-trans-field-item', function () {
        var itemTrans = $(this).attr('data-field');
        var langTarget = ETS_TRANS_DEFAULT_CONFIG.lang_target || '';
        var formData = {
            trans_option: ETS_TRANS_DEFAULT_CONFIG.field_option || '',
            trans_source: ETS_TRANS_DEFAULT_CONFIG.lang_source || '',
            trans_target: langTarget.split(','),
            ignore_product_name: ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME,
            ignore_content_has_product_name: ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME,
            auto_generate_link_rewrite: ETS_TRANS_AUTO_GENERATE_LINK_REWRITE,
        };
        formData.trans_data = etsTransCombination.getTransData(formData.trans_source, formData.trans_target, formData.trans_option, itemTrans);
        formData.col_data = ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2 ? etsTransCombination.colData.ps810 :etsTransCombination.colData.ps17;
        formData.combination_id = ETS_TRANS_ID_COMBINATION || 0;
        formData.page_id = formData.combination_id;
        etsTransCombination.translateCombination(formData, $(this), 'combination_product');
        return false;
    });
});