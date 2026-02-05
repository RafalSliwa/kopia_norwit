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
var etsTransBlog = {
    ajaxXhrTranslatePage: null,
    blogType: ETS_TRANS_BLOG_TYPE,
    renderBtnTransItem: function () {
        return '<button class="btn btn-default ets-trans-btn-trans-field-item js-ets-trans-blog-item" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i></button>';
    },
    renderBtnTransForm: function () {
        return '<button class="btn btn-default js-ets-trans-blog-form pull-right" title="' + etsTransFunc.trans('g-translate') + '"><i class="ets_svg_icon"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"></path></svg></i> ' + etsTransFunc.trans('g-translate') + '</button>';
    },
    renderBtnTransListItem: function(id, type){
        return '<li><a href="javascript:void(0)" class="js-ets-trans-blog-list-item" data-id="'+id+'" data-type="'+type+'"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> '+etsTransFunc.trans('g-translate')+'</a></li>';
    },
    renderBtnTransListAll: function (type) {
        type = type || '';
        return '<a class="list-toolbar-btn js-ets-trans-blog-list-all" data-type="' + type + '" title="' + (type == 'post' ? etsTransFunc.trans('translate_all_posts') : etsTransFunc.trans('translate_all_categories')) + '">' +
            '<span data-placement="top" data-html="true" data-original-title="' + (type == 'post' ? etsTransFunc.trans('translate_all_posts') : etsTransFunc.trans('translate_all_categories')) + '" class="label-tooltip" data-toggle="tooltip" title="">' +
            '<i class="ets_svg_icon" ><svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i>' +
            '</span>'
        '</a>';
    },
    addBtnTransToInput: function () {
        if(ETS_TRANS_ENABLE_TRANS_FIELD) {
            $('.ybc_blog_form_post .translatable-field, .ybc_blog_form_category .translatable-field, .ybc_blog_form_slide .translatable-field, .ybc_blog_form_gallery .translatable-field').each(function () {
                if (!$(this).parent().find('.js-ets-trans-blog-item').length) {
                    var idInput = $(this).find('input[type=text],textarea').first().attr('id') || null;
                    if (idInput && idInput.indexOf('url_') === -1 && !$(this).find('input[type=file]').length) {

                        $(this).parent().append(etsTransBlog.renderBtnTransItem());
                        setTimeout(function(){
                            if ( $('.translatable-field > .col-lg-10').length > 0 ){
                                $('.ets-trans-btn-trans-field-item').addClass('has_col_10');
                            }
                        }, 200);
                    }
                }
            });
        }
        $('.ybc_blog_form_post .panel:not(.ybc-chatgpt-box) .panel-footer, .ybc_blog_form_category .panel-footer').append(etsTransBlog.renderBtnTransForm());
        if (ETS_TRANS_IS_BLOG_LIST_POST){
            if (!$('.ybc_blog_form_post #module_form').length)
                $('.ybc_blog_form_post .panel-heading-action').prepend(etsTransBlog.renderBtnTransListAll('post'));
        }
        if (ETS_TRANS_IS_BLOG_LIST_CATEGORY){
            if (!$('.ybc_blog_form_category #module_form').length)
                $('.ybc_blog_form_category .panel-heading-action').prepend(etsTransBlog.renderBtnTransListAll('category'));
        }

        $('#list-ybc_post tr').each(function () {
            var idPost = $(this).closest('tr').attr('id');
            if (idPost && typeof idPost === "string") {
                idPost = idPost.split("-")
                $(this).find('td:last-child .btn-group-action .dropdown-menu').append(etsTransBlog.renderBtnTransListItem(idPost[1], 'post'));
            }
        });

        $('#list-ybc_category tr').each(function () {
            var idCategory = $(this).find('td:first-child').text().trim();
            $(this).find('td:last-child .btn-group-action .dropdown-menu').append(etsTransBlog.renderBtnTransListItem(idCategory, 'category'));
        });
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
    isBlogCategory: function(){
      if($('#list-ybc_category').length){
          return true;
      }
      return false;
    },
    translate: function (btnClick, formData, isTransAll, loop) {
        isTransAll = isTransAll || 0;
        isTransAll = parseInt(isTransAll);
        loop = loop || 0;
        etsTransBlog.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            data: {
                etsTransBlog: 1,
                isTransAll: isTransAll,
                formData: formData,
            },
            dataType: 'json',
            beforeSend: function () {
                $(btnClick).addClass('loading');
                $(btnClick).prop('disabled', true);
                if (isTransAll) {
                    if(!loop){
                        etsTransFunc.showPopupTranslating(1, 'blog', 0, formData.trans_source, formData.trans_target, formData.trans_option, 0,0, formData.auto_generate_link_rewrite);
                        etsTransFunc.setConfigTranslating('blog',formData.trans_source, formData.trans_target, formData.trans_option, 0,0, formData.auto_generate_link_rewrite);
                    }
                } else
                    etsTransFunc.showTranslatingField();
            },
            success: function (res) {
                console.log(res, isTransAll);
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
                                etsTransBlog.translate(btnClick, formData, 1, 1);
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
                        $('#etsTransModalTrans').modal('hide');
                        var transData = res.trans_data || null;
                        if (transData) {
                            Object.keys(transData).forEach(function (idLang) {
                                Object.keys(transData[idLang]).forEach(function (key) {
                                    if ($('#' + key + idLang).attr('id').indexOf('keywords') !== -1) {
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
                                    var generateLinkRewrite = ETS_TRANS_AUTO_GENERATE_LINK_REWRITE;
                                    if (typeof formData.auto_generate_link_rewrite !== 'undefined')
                                        generateLinkRewrite = formData.auto_generate_link_rewrite;
                                    if(key == 'title_' && generateLinkRewrite == 1){
                                        $('#url_alias_'+idLang).val(str2url(transData[idLang][key]));
                                    }
                                });
                            });
                        } else {
                            const url = window.location.href;
                            const queryString = url.split('?')[1];
                            const urlParams = new URLSearchParams(queryString);
                            const paramValue = urlParams.get('addNew');
                            if (typeof urlParams !== "undefined" && urlParams.get('addNew')) {
                                if (formData.page_id) {
                                    const currentUrl = new URL(window.location.href);
                                    currentUrl.searchParams.delete('addNew');
                                    currentUrl.searchParams.append('editybc_post', '');
                                    currentUrl.searchParams.append('id_post', formData.page_id);
                                    window.location.href = currentUrl.toString();

                                } else
                                    $('.ybc_blog_form_content_admin form#module_form button#module_form_submit_btn').click();
                            }
                            else {
                                location.reload();
                            }
                        }
                    }
                    if ((typeof res.no_trans !== 'undefined' && res.no_trans) || (isTransAll && !formData.nb_text)) {
                        etsTransFunc.showSuccessMessage(etsTransFunc.trans('no_text_trans'));
                    }
                    else if(res.message)
                        etsTransFunc.showSuccessMessage(res.message);
                }
                else{
                    var errorMessage = res.message || '';
                    if (typeof res.errors === 'string' && res.errors){
                        errorMessage = res.errors;
                    } else if (typeof res.errors === 'object' && res.errors.length) {
                        errorMessage = res.errors[0];
                    }
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
            }
        });
    },
    getInputData: function (idLang, fieldTrans) {
        fieldTrans = fieldTrans || null;
        var transData = {};
        var boxSearch = null;
        if (!fieldTrans) {
            boxSearch = $('.ybc_blog_form_content_admin .translatable-field.lang-' + idLang);
        } else {
            boxSearch = $(fieldTrans).find('.translatable-field.lang-' + idLang);
        }
        boxSearch.each(function () {
            var input = $(this).find('input[type=text], textarea');
            var idInput = input.attr('id');
            if (idInput && idInput.indexOf('url_alias_' + idLang) === -1 && !input.parent().find('input[type=file]').length) {
                var keyInput = etsTransBlog.getInputKey(input.attr('id'), idLang);
                if ($(this).find('iframe[id=' + keyInput + idLang + '_ifr]') && typeof $(this).find('iframe[id=' + keyInput + idLang + '_ifr]').contents().find('body.mce-content-body').html() !== "undefined") {
                    transData[keyInput] = $(this).find('iframe[id=' + keyInput + idLang + '_ifr]').contents().find('body.mce-content-body').html();
                } else  {
                    if (input.attr('id').indexOf('keywords') !== -1) {
                        var keywords = input.tagify('serialize');
                        transData[keyInput] = keywords.replace(/,/g, '|');
                    } else {
                        transData[keyInput] = input.val();
                    }
                }
            }
        });
        return transData;
    },
    getFormData: function (langSource, langTarget, transOption, fieldTrans) {
        var transData = {};
        transData.source = etsTransBlog.getInputData(langSource, fieldTrans);
        transData.target = {};
        console.log('getFormData: ', langSource, langTarget, transOption, fieldTrans, transData)
        $.each(langTarget, function (i, idLang) {
            transData.target[idLang] = {};
            var transLangData = etsTransBlog.getInputData(idLang, fieldTrans);
            console.log('transLangData: ', idLang, transLangData);
            var emty_content_mce = '<p><br data-mce-bogus="1"></p>';
            switch (transOption) {
                case 'only_empty':
                    Object.keys(transLangData).forEach(function (k) {
                        if (!transLangData[k].trim() || transLangData[k].trim() == emty_content_mce) {
                            transData.target[idLang][k] = 1;
                        } else {
                            transData.target[idLang][k] = 0;
                        }
                    });
                    break;
                case 'both':
                    Object.keys(transLangData).forEach(function (k) {
                        if (!transLangData[k].trim() || transLangData[k].trim() == emty_content_mce || transLangData[k].trim().toLowerCase() == transData.source[k].trim().toLowerCase()) {
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
        console.log('get form config: ', btnClicked, isTransAll, fieldTrans, ids, resetTrans)
        resetTrans = resetTrans ?? 0;
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'GET',
            dataType: 'json',
            data: {
                etsTransGetFormTranslate : 1,
                isNewTemplate: ETS_TRANS_IS_NEW_TEMPLATE,
                pageId: ids,
                pageType: 'blog',
                isDetailPage: 1,
                isTransAll: isTransAll || 0,
                fieldTrans: fieldTrans || '',
                resetTrans: resetTrans,
                blogType: $(btnClicked).attr('data-type') || etsTransBlog.blogType
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
        etsTransBlog.ajaxXhrTranslatePage = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransAnalyzing: 1,
                pageType: pageType,
                formData: formData,
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
                            etsTransBlog.analysisBeforeTranslate(pageType, formData);
                        } else {
                            $('#etsTransPopupAnalyzing').removeClass('active');
                            etsTransFunc.showAnalysisCompleted(pageType, formData, resData.total_item || 0, resData.nb_money);
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
        if (etsTransBlog.ajaxXhrTranslatePage && etsTransBlog.ajaxXhrTranslatePage.readyState != 4) {
            etsTransBlog.ajaxXhrTranslatePage.abort();
        }
    },
    colData: {
        post: {
           'title_': 'title',
           'meta_title_': 'meta_title',
           'description_': 'description',
           'short_description_': 'short_description',
           'meta_description_': 'meta_description',
           'meta_keywords_': 'meta_keywords',
        },
        category: {
           'title_': 'title',
           'meta_title_': 'meta_title',
           'description_': 'description',
           'meta_description_': 'meta_description',
           'meta_keywords_': 'meta_keywords',
        },
    }
};
$(document).ready(function () {
    etsTransBlog.addBtnTransToInput();

    $(document).on('click', '.js-ets-trans-blog-item', function () {
        if (ETS_TRANS_IS_AUTO_CONFIG) {
            var langTarget = ETS_TRANS_DEFAULT_CONFIG.lang_target || '';
            var formData = {
                trans_option: ETS_TRANS_DEFAULT_CONFIG.field_option,
                trans_source: ETS_TRANS_DEFAULT_CONFIG.lang_source,
                trans_target: langTarget.split(','),
            };

            formData.trans_data = etsTransBlog.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, $(this).parent()[0]);
            formData.blog_type = etsTransBlog.blogType;
            if(formData.blog_type == 'post')
                formData.page_id = $('.ybc_blog_form_content_admin').find('form input[name=id_post]').val() || 0;
            else
                formData.page_id = $('.ybc_blog_form_content_admin').find('form input[name=id_category]').val() || 0;
            formData.col_data = etsTransBlog.colData[etsTransBlog.blogType];
            etsTransBlog.translate(this, formData);
        } else {
            var fieldTrans = $(this).parent().find('input[type=text], textarea').attr('id');
            etsTransBlog.getFormConfig(this, 0, fieldTrans);
        }
        return false;
    });

    $(document).on('click', '.js-ets-trans-blog-form', function () {
        etsTransBlog.getFormConfig(this, 0, 'panel');
        return false
    });
    $(document).on('click', '.js-ets-trans-blog-all', function () {
        etsTransBlog.getFormConfig(this, 1);
    });

    $(document).on('click', '.js-ets-trans-analysis-text', function () {
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.nb_text = 0;
        formData.nb_char = 0;
        formData.nb_money = 0;
        if (typeof formData.trans_target == 'undefined' || !formData.trans_target) {
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        var blogType = $(this).attr('data-blog-type') || 'post';
        formData.blog_type = blogType;
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
                    etsTransBlog.analysisBeforeTranslate('blog', formData);
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
            fieldEl = $('.ybc_blog_form_content_admin')[0];
            btnClick = $('.ybc_blog_form_content_admin').find('.js-ets-trans-blog-form')[0];
        } else {
            if (fieldTrans) {
                fieldEl = $('#' + fieldTrans).closest('.translatable-field').parent()[0];
                btnClick = $('#' + fieldTrans).closest('.translatable-field').parent().find('.js-ets-trans-blog-item')[0];
            }
        }
        var blogType = $(this).attr('data-blog-type') || etsTransBlog.blogType;
        var pageIdExtra = 0;
        if(blogType == 'post') {
            pageIdExtra = $('.ybc_blog_form_content_admin').find('form input[name=id_post]').val() || 0;
            if (pageIdExtra)
                formData.page_id = pageIdExtra;
        }
        else {
            pageIdExtra = $('.ybc_blog_form_content_admin').find('form input[name=id_category]').val() || 0;
            if (pageIdExtra)
                formData.page_id = pageIdExtra;
        }
        if (!$(this).hasClass('.js-ets-trans-btn-translate-page') && !formData.page_id){
            formData.col_data = etsTransBlog.colData[blogType];
        }

        if (isTransAll != 0) {
            formData.nb_text = 0;
            formData.nb_char = 0;
            formData.nb_money = 0;
            formData.blog_type = blogType;
            formData.offset = 0;
        }

        if ($(this).hasClass('js-ets-trans-analysis-accept')) {
            formData.blog_type = blogType;
            etsTransBlog.translate(btnClick, formData, 1);
            return false;
        }
        var isTransAll = $(this).attr('data-trans-all') || 0;
        formData.trans_data = etsTransBlog.getFormData(formData.trans_source, formData.trans_target, formData.trans_option, fieldEl);
        etsTransBlog.translate(btnClick, formData, isTransAll);
        return false;
    });

    $(document).on('click', '.js-ets-trans-blog-list-all', function () {
        var blogType = $(this).attr('data-type') || '';
        etsTransBlog.getFormConfig(this, 1);
    });
    $(document).on('click', '.js-ets-trans-blog-list-item', function () {
        var id = $(this).attr('data-id') || null;
        var blogType = $(this).attr('data-type') || null;
        if(!id || !blogType){
            alert(etsTransFunc.trans('can_not_trans_item'));
            return;
        }
        etsTransBlog.getFormConfig(this, 0, null, id);
        return;
    });
    $(document).on('click', '.js-ets-tran-btn-pause-translate', function () {
        etsTransBlog.stopTranslatePage();
        var dataPause = {};
        if(etsTransBlog.isBlogCategory())
            dataPause.pageType = 'blog_category';
        else
            dataPause.pageType = 'blog_post';
        dataPause.nbTranslated = $(this).attr('data-nb-translated');
        dataPause.nbCharTranslated = $(this).attr('data-nb-char');
        dataPause.langSource = $(this).attr('data-lang-source');
        dataPause.langTarget = $(this).attr('data-lang-target');
        dataPause.fieldOption = $(this).attr('data-field-option');
        dataPause.nbPath = $(this).attr('data-total-path');
        dataPause.auto_generate_link_rewrite = $(this).attr('data-auto_generate_link_rewrite');
        etsTransBlog.pauseTranslate(this, dataPause);
        return false;
    });
    $(document).on('click', '.js-ets-trans-btn-resume-translate', function () {
        var dataResume = {};
        if(etsTransBlog.isBlogCategory())
            dataResume.pageType = 'blog_category';
        else
            dataResume.pageType = 'blog_post';
        dataResume.nbTranslated = $(this).attr('data-nb-translated');
        dataResume.nbCharTranslated = $(this).attr('data-nb-char');
        dataResume.langSource = $(this).attr('data-lang-source');
        dataResume.langTarget = $(this).attr('data-lang-target');
        dataResume.fieldOption = $(this).attr('data-field-option');
        dataResume.auto_generate_link_rewrite = $(this).attr('data-auto_generate_link_rewrite');
        etsTransFunc.setResumeTranslate(dataResume);
        var offset = $(this).attr('data-total-path') || 0;
        etsTransFunc.updateTotalFilePath(offset);
        var formData = {};
        formData.pageType = 'blog';
        if(etsTransBlog.isBlogCategory()){
            formData.blog_type = 'category';
        }
        else
            formData.blog_type = 'post';
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
        formData.auto_generate_link_rewrite = dataResume.auto_generate_link_rewrite;
        etsTransBlog.translate(this, formData, 1);
    });

    $(document).on('click', '.js-ets-trans-translate-from-resume', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.trans_target = formData.trans_target.split(',');
        formData.trans_all = 1;
        if(etsTransBlog.isBlogCategory()){
            formData.blog_type = 'category';
        }
        else
            formData.blog_type = 'post';
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
        etsTransBlog.translate(this, formData, 1);
    });
    $(document).on('click', '.js-ets-trans-translate-from-zero', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        etsTransFunc.hidePopupTrans();
        etsTransBlog.getFormConfig(this, 1, null, null, 1);
    });

    $(document).on('click','#etsTransModalTrans .close,#etsTransModalTrans .btn-group-translate-close', function (e) {
        etsTransBlog.stopTranslatePage();
    });
});