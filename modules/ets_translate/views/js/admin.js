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
var etsTransAdminConfig = {
    transAllAjax: null,
    pageList: ['product', 'category', 'cms', 'cms_category', 'manufacturer', 'supplier'],
    renderBtnTransAllPages: function(){
        return '<button class="btn btn-primary pull-right js-ets-trans-btn-trans-all-website"><i class="fa fa-globe"></i> '+etsTransFunc.trans('g-translate')+'</button>';
    },
    renderBtnTransAllInter: function(className){
        className = className || '';
      return '<a class="btn btn-outline-secondary js-ets-trans-btn-trans-all-inter '+className+'"><i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> '+etsTransFunc.trans('g-translate')+'</a>';
    },
    getFormModify: function(btnClicked, resetTrans, transWd){
        var interTran = $('.js-ets-trans-btn-trans-all-website').length ? 0 : 1;
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransGetFormConfigTransAll: 1,
                isNewTemplate: ETS_TRANS_IS_NEW_TEMPLATE,
                pageType: 'all',
                interTrans: interTran,
                resetTrans: resetTrans || 0,
                transWd: transWd || null
            },
            beforeSend: function(){
                if (btnClicked) {
                    $(btnClicked).addClass('loading');
                    $(btnClicked).prop('disabled', true);
                }
            },
            success: function (res) {
                if (res.success) {
                    if($('#etsTransModalTrans').length){
                        $('#etsTransModalTrans').remove();
                    }
                    if($('#content.bootstrap').length){
                        $('#content.bootstrap').append(res.form);
                    }
                    else{
                        $('body').append(res.form);
                    }
                    $('#etsTransModalTrans').modal({backdrop: 'static', keyboard: false});
                    $('#etsTransModalTrans').modal('show');
                }
            },
            complete: function(){
                if (btnClicked) {
                    $(btnClicked).removeClass('loading');
                    $(btnClicked).prop('disabled', false);
                }
            }
        });
    },
    loadFileData: function(btnClick, formData, pageType){
        $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransAllLoadFile: 1,
                pageType: pageType || 'all',
                formData: formData,
            },
            beforeSend: function(){
                $(btnClick).prop('disabled', true);
                $(btnClick).addClass('loading');
                etsTransFunc.showInitializing();
            },
            success: function (res) {
                if(res.success){
                    formData.page_type = '';
                    formData.nb_item = 0;
                    formData.nb_text = 0;
                    formData.nb_char = 0;
                    formData.nb_path = res.total_item || 0;
                    etsTransAdminConfig.translate(btnClick, formData, pageType);
                }
                else{
                    if(res.errors){
                        etsTransFunc.showErrorMessage(res.errors);
                    }
                }
            },
            complete: function(){
                $(btnClick).prop('disabled', false);
                $(btnClick).removeClass('loading');
                etsTransFunc.hideInitializing();
            }
        });
    },
    analysisBeforeTranslate: function(pageType, formData, offset, init){
        init = init || 0;
        etsTransAdminConfig.transAllAjax = $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransAnalyzingAllPage: 1,
                formData: formData,
                offset: offset,
                init: init,
                pageType: pageType || 'all',
            },
            success: function(res){
                if(res.success){
                    if(init){
                        etsTransAdminConfig.analysisBeforeTranslate(pageType, formData);
                        return;
                    }
                    var resData = res.data || {};
                    if(resData && Object.keys(resData).length){
                        formData.nb_text = parseInt(formData.nb_text) + parseInt(resData.nb_text);
                        formData.nb_char = parseInt(formData.nb_char) + parseInt(resData.nb_char);
                        formData.nb_money = parseFloat(formData.nb_money) + parseFloat(resData.nb_money);
                        if(typeof resData.mail_checked !== 'undefined' && resData.mail_checked){
                            if(typeof formData.mail_checked !== 'undefined' && formData.mail_checked){
                                formData.mail_checked = formData.mail_checked.concat(resData.mail_checked);
                            }
                            else{
                                formData.mail_checked = resData.mail_checked;
                            }
                        }
                        if(resData.stop != 1){
                            var offsetData = resData.offset || 0;
                            etsTransAdminConfig.analysisBeforeTranslate(pageType,formData, offsetData);
                        }
                        else{
                            formData.nb_path = resData.nb_path || 0;
                            etsTransFunc.showAnalysisCompleted(pageType, formData, formData.nb_text || 0);
                        }
                    }
                }
                else{
                    if(res.message)
                        etsTransFunc.showErrorMessage(res.message);
                    else
                        etsTransFunc.showErrorMessage('Has error');
                }
            },
            complete: function(){

            },
            error: function () {

            }
        });
    },
    translate: function(btnClick, formData, pageType, totalItem){
        totalItem = totalItem || 0;
        etsTransAdminConfig.transAllAjax = $.ajax({
            url:  ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransTransAll: 1,
                pageType: pageType || 'all',
                formData: formData,
            },
            beforeSend: function(){
                etsTransFunc.showPopupTranslating(1, pageType, totalItem, formData.trans_source, formData.trans_target, formData.trans_option, formData.ignore_product_name, formData.ignore_content_has_product_name, formData.auto_generate_link_rewrite);
                etsTransFunc.setConfigTranslating(pageType, formData.trans_source, formData.trans_target, formData.trans_option, formData.ignore_product_name, formData.ignore_content_has_product_name, formData.auto_generate_link_rewrite);
            },
            success: function (res) {
                if(res.success){
                    var result = res.data;
                    formData.page_type = result.page_type;
                    formData.nb_text = parseInt(formData.nb_text) + parseInt(result.nb_text);
                    formData.nb_char = parseInt(formData.nb_char) + parseInt(result.nb_char);
                    formData.nb_item = result.nb_item || 0;
                    formData.offset = result.offset || 0;
                    if(result.page_type == 'pc')
                        formData.offset = result.offset || 0;
                    if(!result.stop_translate){
                        etsTransAdminConfig.translate(btnClick, formData, pageType);
                    }
                    else{
                        etsTransFunc.setTranslateDone();
                        if(!formData.nb_text){
                            etsTransFunc.showSuccessMessage(etsTransFunc.trans('no_text_trans'));
                        }
                         else
                             if (result.errors)
                                etsTransFunc.showErrorMessage(result.message);
                             else
                                etsTransFunc.showSuccessMessage(result.message);
                    }
                    etsTransFunc.updateDataTranslating(formData.nb_text, formData.nb_char, null);
                    etsTransFunc.updateTotalFilePath(formData.nb_path);
                    var filePath = result.file_path || [];
                    if(etsTransAdminConfig.pageList.indexOf(formData.page_type) !== -1 || formData.page_type == 'blog_post' || formData.page_type == 'blog_category' || formData.page_type == 'megamenu'){
                        etsTransFunc.updatePageTranslated(formData.page_type, formData.nb_item);
                    }
                    else if(formData.page_type == 'email'){
                        formData.nb_email = formData.nb_email || 0;
                        if(typeof result.nb_email !== 'undefined'){
                            formData.nb_email = formData.nb_email + result.nb_email;
                        }
                        etsTransFunc.updatePageTranslated(formData.page_type, formData.nb_email);
                    }
                    else{
                        etsTransFunc.updateListFileTranslated(filePath);
                    }
                    var nb_path = formData.nb_path || 0;
                    var nb_path_remain = result.total_path_remain || 0;
                    var percentTranslated = 0;
                    if(nb_path)
                        percentTranslated = (nb_path - nb_path_remain) / nb_path * 100;
                    etsTransFunc.updatePercentageTranslated(percentTranslated.toFixed(2));
                }
                else{
                    etsTransFunc.setTranslateDone();
                    var errorMessage = res.errors || res.message;
                    etsTransFunc.showErrorMessage(errorMessage);
                }
            },
            error: function(){

            },
        });
    },
    savePauseData: function(dataPause, btnClick){
        $.ajax({
            url: ETS_TRANS_LINK_AJAX,
            type: 'POST',
            dataType: 'json',
            data: {
                etsTransPauseTranslate: 1,
                transInfo: dataPause
            },
            beforeSend: function(){
                $(btnClick).prop('disabled', true);
                $(btnClick).addClass('loading');
            },
            success: function (res) {
                if(res.success){
                    etsTransFunc.setPauseTranslate(dataPause);
                    etsTransFunc.showSuccessMessage(etsTransFunc.trans('pause_success'));
                }
            },
            complete: function(){
                $(btnClick).prop('disabled', false);
                $(btnClick).removeClass('loading');
            },
            error: function(){}
        });
    },
    setCheckedWd: function(input, inputName){
        if($(input).attr('id') == 'wd_all'){
            var isChecked = $(input).is(':checked');
            $('input[name="'+inputName+'"]').prop('checked', isChecked);
            $('input[name="'+inputName+'"]:not(#wd_all)').prop('disabled', isChecked);
        }
        else{
            if($(input).parent().find('ul').length){
                var isChecked = $(input).is(':checked');
                $(input).parent().find('ul input[name="'+inputName+'"]').prop('checked', isChecked);
                $(input).parent().find('ul input[name="'+inputName+'"]').prop('disabled', isChecked);
            }
        }
    },
    openWdWhenSelected: function () {
        $('input[name="ETS_TRANS_WD_CONFIG[]"]').each(function () {
            if($(this).is(':checked')){
                var ul = $(this).closest('ul');
                if(ul.parent('li').length && !ul.parent('li').children('input[name="ETS_TRANS_WD_CONFIG[]"]').is('checked')){
                    var idUl = ul.attr('id');
                    $('#'+idUl).collapse('show').prev('a[data-toggle="collapse"]').removeClass('collapsed');
                }
            }
        });
    },
    stopTranslatePage: function(){
        if(etsTransAdminConfig.transAllAjax && etsTransAdminConfig.transAllAjax.readyState != 4){
            etsTransAdminConfig.transAllAjax.abort();
        }
    },
};
$(document).ready(function(){

    etsTransAdminConfig.openWdWhenSelected();
    if($('.admintranslations #page-header-desc-configuration-modules-list, #recommended-modules-button').length) {
        $('.admintranslations #page-header-desc-configuration-modules-list, #recommended-modules-button').before(etsTransAdminConfig.renderBtnTransAllInter());
    }
    else
    {
        if($('#product_form_open_help').length)
        {
            $('#product_form_open_help').before(etsTransAdminConfig.renderBtnTransAllInter());
        }
        else{
            $('.admintranslations .page-head .page-bar.toolbarBox').prepend(etsTransAdminConfig.renderBtnTransAllInter('old17'));
        }
    }

   $(document).on('click', '.js-ets-trans-check-api', function(){
       $('.bootstrap > .alert[class^="alert"]').remove();
       var apiKey = $(this).parent().prev('input').val();
       var data = {
           apiKey,
           etsTransCheckApiKey: 1
       }
       if ($(this).hasClass('js-btn-check-gpt-api')) {
           data['isCheckChatgpt'] = 1;
       } else {
           data['apiType'] = $(document).find('input[name="ETS_TRANS_SELECT_API"]').val();
       }
        var btn = $(this);
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function(){
                btn.addClass('loading');
                btn.prop('disabled', true);
            },
            success: function(res){

                if(res.success){
                    btn.addClass('is-good');
                    etsTransFunc.showSuccessMessage(res.message);
                }
                else{
                    etsTransFunc.showErrorMessage(res.message);
                }
            },
            complete: function(){
                btn.removeClass('loading');
                btn.prop('disabled', false);
            }
        });

       return false;
   });

   $('input[name=ETS_TRANS_AUTO_SETTING_ENABLED]').change(function(){
      if($('input[name=ETS_TRANS_AUTO_SETTING_ENABLED]:checked').val() == 1){
          $('.ets-trans-auto-setting-group').removeClass('hide');
      }
      else{
          $('.ets-trans-auto-setting-group').addClass('hide');
      }
   });

   $(document).on('click', '#dropdown_ETS_TRANS_LANG_SOURCE .js-ets-trans-choose-lang-source-item', function(){
        var idLang = $(this).attr('data-lang');
        $('input[name=ETS_TRANS_LANG_SOURCE]').val(idLang);
        $('#dropdown_ETS_TRANS_LANG_SOURCE').find('[data-toggle="dropdown"] .content-lang').html($(this).html());
        $('input[name=ETS_TRANS_LANG_SOURCE]').change();
       var targetLang = [];
       if($('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').length > 1){
           $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
               targetLang.push('<span><img src="'+$(this).next('label').find('img').attr('src')+'"/><span>'+($(this).attr('data-isocode') || '')+'</span></span>');
           });
       }
       else{
           $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
               targetLang.push($(this).next('label').html());
           });
       }
       if(targetLang.length)
           $('#langTargetDropdownBo>.text-html').html(targetLang.join(', '));
       else
           $('#langTargetDropdownBo>.text-html').html('--');
   });

   $(document).on('click', '#dropdown_ETS_TRANS_SELECT_API .js-ets-trans-choose-api-item', function (e) {
       var select_api = $(this).attr('data-api').trim();
       $('input[name=ETS_TRANS_SELECT_API]').val(select_api);
       $('#dropdown_ETS_TRANS_SELECT_API').find('[data-toggle="dropdown"] .content-select-api').html($(this).html());
       var inputApiGroups = document.querySelectorAll('.js-ets-trans-api-group');
       if (inputApiGroups && inputApiGroups.length) {
           inputApiGroups.forEach( item => {
               if (item.classList.contains(`ets-trans-api-group_${select_api}`)) {
                   item.classList.remove('hide');
               } else {
                   item.classList.add('hide');
               }
           })
       }
   })

   $(document).on('change', '[name=ETS_TRANS_LANG_SOURCE]', function(){
       var langSource = $(this).val();

       $('#ETS_TRANS_LANG_TARGET_all').prop('checked', false);
       $('input[name="ETS_TRANS_LANG_TARGET[]"]').closest('li').removeClass('hide');
       $('input[name="ETS_TRANS_LANG_TARGET[]"][value="'+langSource+'"]').closest('li').addClass('hide');
       $('input[name="ETS_TRANS_LANG_TARGET[]"][value="'+langSource+'"]').prop('checked', false);
   });

    $(document).on('change', 'input[name="ETS_TRANS_LANG_TARGET[]"]', function (e) {
        var totalTarget = $('input[name="ETS_TRANS_LANG_TARGET[]"]').length - 1;
        if($('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').length != totalTarget){
            $('#ETS_TRANS_LANG_TARGET_all:checked').prop('checked', false);
        }
        else{
            $('#ETS_TRANS_LANG_TARGET_all').prop('checked', true);
        }
        var targetLang = [];
        if($('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').length > 1){
            $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
                targetLang.push('<span><img src="'+$(this).next('label').find('img').attr('src')+'"/><span>'+($(this).attr('data-isocode') || '')+'</span></span>');
            });
        }
        else{
            $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
                targetLang.push($(this).next('label').html());
            });
        }
        if(targetLang.length){
            $('#langTargetDropdownBo>.text-html').html(targetLang.join(', '));
            if(targetLang.length === 1){
                $('#langTargetDropdownBo>.text-html').addClass('single-lang');
            }
            else{
                $('#langTargetDropdownBo>.text-html').removeClass('single-lang');
            }
        }
        else
            $('#langTargetDropdownBo>.text-html').html('--');
        $(this).closest('.dropdown').toggleClass('open');
    });
    $(document).on('click', '#ETS_TRANS_LANG_TARGET_all', function () {
        if($(this).is(':checked')){
            $('input[name="ETS_TRANS_LANG_TARGET[]"]').each(function () {
                if(!$(this).closest('li').hasClass('hide')){
                    $(this).prop('checked', true);
                }
            });
            var targetLang = [];
            if($('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').length > 1){
                $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
                    targetLang.push('<span><img src="'+$(this).next('label').find('img').attr('src')+'"/><span>'+($(this).attr('data-isocode') || '')+'</span></span>');
                });
            }
            else{
                $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').each(function () {
                    targetLang.push($(this).next('label').html());
                });
            }
            if(targetLang.length) {
                $('#langTargetDropdownBo>.text-html').html(targetLang.join(', '));
                if(targetLang.length === 1){
                    $('#langTargetDropdownBo>.text-html').addClass('single-lang');
                }
                else{
                    $('#langTargetDropdownBo>.text-html').removeClass('single-lang');
                }
            }
            else
                $('#langTargetDropdownBo>.text-html').html('--');
        }
        else{
            $('#langTargetDropdownBo>.text-html').html('--');
            $('input[name="ETS_TRANS_LANG_TARGET[]"]:checked').prop('checked', false);
        }
    });
    $(document).on('click', '.js-ets-trans-clear-all-logs', function(){
        if($(this).hasClass('loading')){
            return false;
        }
        var $this = $(this);
       if(confirm(etsTransText['confirm_clear_all_logs'])){
           $.ajax({
               url: ETS_TRANS_LINK_AJAX_MODULE,
               type: 'POST',
               data: {
                   etsTransClearAllLogs: 1
               },
               dataType: 'json',
               beforeSend: function(){
                   $this.addClass('loading');
                   $this.prop('disabled', true);
               },
               success: function(res){
                   if(res.success){
                       etsTransFunc.showSuccessMessage(res.message);
                       window.location.reload();
                   }
               },
               complete: function(){
                   $this.removeClass('loading');
                   $this.prop('disabled', false);
               },
           });
       }
       return false;
    });
    $(document).on('click', '.js-ets-trans-clear-log-item', function(){
        if(!confirm(etsTransText['confirm_delete_log_item'])){
            return false;
        }
        if($(this).hasClass('loading')){
            return false;
        }
        var $this = $(this);
        var idItem = $(this).attr('data-id');
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'POST',
            data: {
                etsTransClearLogItem: 1,
                idLog: idItem
            },
            dataType: 'json',
            beforeSend: function(){
                $this.addClass('loading');
                $this.prop('disabled', true);
            },
            success: function(res){
                if(res.success){
                    etsTransFunc.showSuccessMessage(res.message);
                    $this.closest('tr').remove();
                }
            },
            complete: function(){
                $this.removeClass('loading');
                $this.prop('disabled', false);
            },
        });
       return false;
    });

    $(document).on('click', 'input[name=ETS_TRANS_WD_ALL]', function () {
        if($(this).is(':checked')){
            $(this).closest('ul').find('input[type=checkbox]').prop('checked', true);
        }
        else{
            $(this).closest('ul').find('input[type=checkbox]').prop('checked', false);
        }
    });

    $(document).on('click', '.js-ets-trans-btn-trans-all-website, .js-ets-trans-btn-trans-all-inter', function () {
        var transWd = null;
        if($(this).hasClass('js-ets-trans-btn-trans-all-website') && $(this).hasClass('saveConfigAll')){
            var formData = $(this).closest('form').serializeArray();
            formData = etsTransFunc.formatFormData(formData);
            transWd = formData.ETS_TRANS_WD_CONFIG || [];
            ETS_TRANS_DEFAULT_CONFIG.wd_data = transWd.join(',');
        }
        etsTransAdminConfig.getFormModify(this, 0, transWd);
        return false;
    });

    $(document).on('click', '.js-ets-trans-btn-translate-page, .js-ets-trans-analysis-accept', function(){

        var formData = $(this).closest('form').serializeArray();
        var pageType = $(this).attr('data-page-type') || 'all';
        var totalItem = $(this).attr('data-total-item') || 0;
        formData = etsTransFunc.formatFormData(formData);
        if(pageType == 'all') {
            formData.trans_wd = ETS_TRANS_DEFAULT_CONFIG.wd_data || null;
        }
       if(!formData.trans_target || !formData.trans_target.length){
           etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
           return false
       }
       if(!formData.trans_wd){
           etsTransFunc.showErrorTrans(etsTransFunc.trans('web_data_required'));
           return false;
       }
       else{
           $('#etsTransModalTrans .form-errors').html('');
       }
        if(formData.trans_target && typeof formData.trans_target == 'string'){
            formData.trans_target = formData.trans_target.split(',');
        }

       if($(this).hasClass('js-ets-trans-analysis-accept')){
           formData.page_type = '';
           formData.nb_item = 0;
           formData.nb_text = 0;
           formData.nb_char = 0;
           formData.nb_path = $(this).attr('data-total-path') || 0;
           etsTransFunc.hideAnalysisCompleted();
           etsTransAdminConfig.translate(this, formData, pageType, totalItem);
       }
       else {
           etsTransAdminConfig.loadFileData(this, formData, pageType);
       }
       return false;
    });

    $(document).on('click', '.js-ets-tran-btn-pause-translate', function(e){
        if(etsTransAdminConfig.transAllAjax && etsTransAdminConfig.transAllAjax.readyState != 4){
            etsTransAdminConfig.transAllAjax.abort();
        }
        var dataPause = {
            pageType: $(this).attr('data-page-type') || 'all',
            langSource: $(this).attr('data-lang-source') || '',
            langTarget: $(this).attr('data-lang-target') || '',
            fieldOption: $(this).attr('data-field-option') || '',
            nbTranslated: $(this).attr('data-nb-translated') || '',
            nbCharTranslated: $(this).attr('data-nb-char') || '',
            nbPath:$(this).attr('data-total-path') || '',
            ignore_product_name:$(this).attr('data-ignore_product_name') ?? '',
            ignore_content_has_product_name: $(this).attr('data-ignore_content_has_product_name') ?? '',
            auto_generate_link_rewrite: $(this).attr('data-auto_generate_link_rewrite') ?? '',
        };
        etsTransAdminConfig.savePauseData(dataPause, this);
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
        dataResume.ignore_product_name = $(this).attr('data-ignore_product_name');
        dataResume.ignore_content_has_product_name = $(this).attr('data-ignore_content_has_product_name');
        dataResume.auto_generate_link_rewrite = $(this).attr('data-auto_generate_link_rewrite');
        etsTransFunc.setResumeTranslate(dataResume);
        var formData = {};
        formData.pageType = dataResume.pageType || 'all';
        formData.trans_source = dataResume.langSource;
        formData.trans_target = dataResume.langTarget.split(',');
        formData.trans_option = dataResume.fieldOption;
        formData.trans_all = 1;
        formData.nb_text = dataResume.nbTranslated;
        formData.nb_char = dataResume.nbCharTranslated;
        formData.nb_path = $(this).attr('data-total-path') || 0;
        formData.ignore_product_name = dataResume.ignore_product_name;
        formData.ignore_content_has_product_name = dataResume.ignore_content_has_product_name;
        formData.auto_generate_link_rewrite = dataResume.auto_generate_link_rewrite;
        etsTransAdminConfig.translate(this, formData, formData.pageType);
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
            ignore_product_name: formData.ignore_product_name,
            ignore_content_has_product_name: formData.ignore_content_has_product_name,
            auto_generate_link_rewrite: formData.auto_generate_link_rewrite
        });
        formData.nb_text = formData.nb_translated;
        formData.nb_char = formData.nb_char_translated;
        etsTransFunc.updateDataTranslating(formData.nb_translated, formData.nb_char_translated, formData.total_item);
        etsTransAdminConfig.translate(this, formData, formData.pageType);
    });
    $(document).on('click', '.js-ets-trans-translate-from-zero', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        $('#etsTransModalTrans').modal('hide');
        etsTransFunc.hidePopupTrans();
        etsTransAdminConfig.getFormModify(this, 1);
    });

    $(document).on('click', '.js-ets-trans-analysis-text', function(){
        var $this = $(this);
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.nb_text = 0;
        formData.nb_char = 0;
        formData.nb_money = 0;
        if(typeof formData.trans_target == 'undefined' || !formData.trans_target){
            etsTransFunc.showErrorTrans(etsTransFunc.trans('target_lang_required'));
            return false;
        }
        if(typeof formData.trans_wd == 'undefined' || !formData.trans_wd){
            etsTransFunc.showErrorTrans(etsTransFunc.trans('web_data_required'));
            return false;
        }
        var pageType = $(this).attr('data-page-type') || 'all';
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
                    etsTransAdminConfig.analysisBeforeTranslate(pageType, formData, 0, 1);
                    $('#etsTransModalTrans .js-ets-trans-analysis-text').addClass('hide');
                }
            },
            complete: function () {
                $this.prop('disabled', false);
                $this.removeClass('loading');
            }
        });
        $(this).parents('.ets-trans-modal').removeClass('ets_modify');
        return false;
    });
    $('input[name="ETS_TRANS_WD_CONFIG[]"]:checked').each(function () {
        etsTransAdminConfig.setCheckedWd(this, 'ETS_TRANS_WD_CONFIG[]');
    });
    $('input[name="trans_wd[]"]:checked').each(function () {
        etsTransAdminConfig.setCheckedWd(this, 'trans_wd[]');
    });

    $(document).on('click', 'input[name="ETS_TRANS_WD_CONFIG[]"]', function(){
        etsTransAdminConfig.setCheckedWd(this, 'ETS_TRANS_WD_CONFIG[]');
    });
    $(document).on('click', '#etsTransModalTrans input[name="trans_wd[]"]', function(e){
        etsTransAdminConfig.setCheckedWd(this, 'trans_wd[]');
    });

    $(document).on('click','#etsTransModalTrans .close,#etsTransModalTrans .btn-group-translate-close', function (e) {
        etsTransAdminConfig.stopTranslatePage();
    });

    $('input[name="ETS_TRANS_ENABLE_APPEND_CONTEXT_WORD"]').change(function () {
        if($(this).val() == 1){
            $('.ets-trans-append-context-word').removeClass('hide');
        }
        else{
            $('.ets-trans-append-context-word').addClass('hide');
        }
    });

    $('input[name="ETS_TRANS_ENABLE_KEY_PHRASE"]').change(function () {
        if($(this).val() == 1){
            $('.js-ets_trans_key_phrase').removeClass('hide');
        }
        else{
            $('.js-ets_trans_key_phrase').addClass('hide');
        }
    });

    $('input[name="ETS_TRANS_ENABLE_CHATGPT"]').change(function () {
        if($(this).val() == 1){
            $('.ets-trans-toggle-parent-enable-chatgpt').removeClass('hide');
        }
        else{
            $('.ets-trans-toggle-parent-enable-chatgpt').addClass('hide');
        }
    });

    $('.ets-trans-tabs-setting .js-ets-trans-tab-item').on('click', function (e) {
        e.preventDefault();
        $(this).parent('.ets-trans-tabs-setting').find('.js-ets-trans-tab-item').removeClass('active');
        $(this).addClass('active');
        var tab = $(this).attr('data-tab');
        $('.js-ets-trans-tab-setting-input').val(tab)
        var elements = $('.js-ets-trans-tab-element');
        if (elements && elements.length) {
            for (var i = 0; i < elements.length; i++) {
                var ele = elements[i];
                var _tab = $(ele).attr('data-tab');
                if (_tab == tab) {
                    $(ele).removeClass('hide');
                    $(ele).addClass('show');
                } else  {
                    $(ele).addClass('hide');
                    $(ele).removeClass('show');
                }
            }
        }
    }) ;

    $('#ETS_TRANS_PAGE_APPEND_CONTEXT_WORD_all').click(function () {
        if($(this).is(':checked'))
            $('input[name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]"]').prop('checked', true);
        else
            $('input[name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]"]').prop('checked', false);
    });
    $('input[name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]"]').click(function () {
        if($('input[name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]"]:checked').length == $('input[name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]"]').length){
            $('#ETS_TRANS_PAGE_APPEND_CONTEXT_WORD_all').prop('checked', true);
        }
        else
            $('#ETS_TRANS_PAGE_APPEND_CONTEXT_WORD_all').prop('checked', false);
    });

    var alerts = document.querySelectorAll('.adminetstransconfig .alert');
    if(alerts && alerts.length) {
        alerts.forEach(alert => {
            var btn = document.createElement('button');
            btn.innerHTML = '<span aria-hidden="true"><i class="material-icons">close</i></span>';
            btn.setAttribute('class', 'close');
            btn.setAttribute('type', 'button');
            btn.setAttribute('data-dismiss', 'alert');
            btn.setAttribute('data-label', 'Close');
            if($(alert).find('.close').length==0)
                alert.appendChild(btn);
        })
    }

    $(document).on('click', '.js-ets-trans-shortcodes .shortcode', function (e) {
        e.preventDefault();
        var content = $(this).html();
        if (content) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(content).select();
            document.execCommand("copy");
            $temp.remove();
            etsTransFunc.showSuccessMessage('Copied!');
        }
        return false;
    })
});