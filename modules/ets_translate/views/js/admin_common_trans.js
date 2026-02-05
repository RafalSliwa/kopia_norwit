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
var etsTransFunc = {
    formatFormData: function(formData){
        var data = {};
        $.each(formData, function (i, el) {
            if(el.name.endsWith('[]'))
            {
                var name = el.name.substring(0, el.name.length - 2);
                if(!(name in data)){
                    data[name] = [];
                }
                data[name].push(el.value);
            }
            else
                data[el.name] = el.value;
        });
        return data;
    },
    trans: function(key){
        return etsTransText[key] || key;
    },
    showErrorTrans: function(errors){
        var errorHtml = '';
        if(typeof errors == 'string'){
            errorHtml = '<ul><li>'+errors+'</li></ul>';
        }
        else{
            errorHtml += '<ul>';
            $.each(errors, function(i, el){
                errorHtml += '<li>'+el+'</li>';
            });
            errorHtml += '</ul>';
        }
        $('#etsTransModalTrans .form-errors').html('<div class="alert alert-danger">' +errorHtml+ '</div>');
    },
    showErrorMessage: function (msg, time = 5000) {
        $.growl.error({message:msg, duration: time});
    },
    showSuccessMessage: function (msg, time = 5000) {
        $.growl.notice({message:msg, duration: time});
    },
    renderBtnStopTranslate: function(){
        return '<button type="button" class="btn btn-danger js-ets-trans-btn-strop-translate">' +
            '<i class="material-icons">close</i> ' +etsTransFunc.trans('stop')+
            '</button>'
    },
    renderBtnResumeTranslate: function(dataResume){
        dataResume = dataResume || {};
        var attrBtn = 'data-page-type="'+(dataResume.pageType || '')+'"';
        attrBtn += ' data-nb-translated="'+(dataResume.nbTranslated || 0)+'"';
        attrBtn += ' data-nb-char="'+(dataResume.nbCharTranslated || 0)+'"';
        attrBtn += ' data-lang-source="'+(dataResume.langSource || '')+'"';
        attrBtn += ' data-lang-target="'+(dataResume.langTarget || '')+'"';
        attrBtn += ' data-field-option="'+(dataResume.fieldOption || '')+'"';
        attrBtn += ' data-total-path="'+(dataResume.nbPath || '')+'"';
        attrBtn += ' data-ignore_product_name="'+(dataResume.ignore_product_name || '')+'"';
        attrBtn += ' data-ignore_content_has_product_name="'+(dataResume.ignore_content_has_product_name || '')+'"';
        attrBtn += ' data-auto_generate_link_rewrite="'+(dataResume.auto_generate_link_rewrite || '')+'"';
        return '<button type="button" class="btn btn-primary js-ets-trans-btn-resume-translate" '+attrBtn+'>' +etsTransFunc.trans('resume')+'</button>'+
        '<button type="button" class="btn btn-default btn-outline-secondary btn-group-translate-close" data-close="close">'+etsTransFunc.trans('Cancel')+'</button>' ;
    },
    renderBtnCloseTranslate: function(){
        return '<button type="button" class="btn btn-default btn-outline-secondary js-ets-trans-btn-close-translate">' +etsTransFunc.trans('close')+'</button>';
    },
    renderBtnPauseTranslate: function(dataPause){
        dataPause = dataPause || {};
        var attrBtn = 'data-page-type="'+(dataPause.pageType || '')+'"';
        attrBtn += ' data-nb-translated="'+(dataPause.nbTranslated || 0)+'"';
        attrBtn += ' data-auto-detect-language="'+(dataPause.autoDetectLang || '')+'"';
        attrBtn += ' data-nb-char="'+(dataPause.nbCharTranslated || 0)+'"';
        attrBtn += ' data-lang-source="'+(dataPause.langSource || '')+'"';
        attrBtn += ' data-lang-target="'+(dataPause.langTarget || '')+'"';
        attrBtn += ' data-field-option="'+(dataPause.fieldOption || '')+'"';
        attrBtn += ' data-ignore_product_name="'+(dataPause.ignore_product_name ?? '')+'"';
        attrBtn += ' data-ignore_content_has_product_name="'+(dataPause.ignore_content_has_product_name ?? '')+'"';
        attrBtn += ' data-auto_generate_link_rewrite="'+(dataPause.auto_generate_link_rewrite ?? '')+'"';
        return '<button type="button" class="btn btn-info js-ets-tran-btn-pause-translate" '+attrBtn+'>'+etsTransFunc.trans('pause')+'</button>';
    },
    getParameterByName: function(name, url) {
        if (typeof url === 'undefined' || !url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    },
    showPopupTranslating: function(canPause, pageType, totalTranslate, langSource, langTarget, fieldOptions, ignore_product_name, ignore_content_has_product_name, auto_generate_link_rewrite){
        $('#etsTransModalTrans #etsTransPopupTranslating').removeClass('hide');
        $('#etsTransModalTrans .ets-trans-content').remove();
        $('#etsTransModalTrans .close').addClass('hide');
        pageType = pageType || '';
        totalTranslate = totalTranslate || 0;
        var pageName = etsTransFunc.getPageName(pageType);
        etsTransFunc.setPageNameTranslating(pageName);
        if(totalTranslate)
            etsTransFunc.setTotalItemTranslating(totalTranslate);

        etsTransFunc.setConfigTranslating(pageType, langSource, langTarget, fieldOptions, ignore_product_name, ignore_content_has_product_name, auto_generate_link_rewrite);
        if(canPause) {
            etsTransFunc.setResumeTranslate();
        }
        else{
            etsTransFunc.setBulkTranslating();
        }
        $('#etsTransPopupTrans .form-errors').html('');
        $('#etsTransPopupTranslating .form-errors').html('');

        $('body').addClass('etsTransPopupActive');
        $('#etsTransPopupTranslating').addClass('active');
        if(pageType == 'theme' || pageType == 'module' || !$('#etsTransPopupTranslating .total_translate').html() || $('#etsTransPopupTranslating .total_translate').html() == 0){
            $('#etsTransPopupTranslating .suffix_total_translate').addClass('hide');
        }
        if(pageType == 'all' || pageType == 'inter'){
            $('#etsTransPopupTranslating .for-trans-all-website').removeClass('hide');
        }
        $('#etsTransModalTrans .btn-group-trans').addClass('hide');
        $('#etsTransModalTrans .btn-group-translating').removeClass('hide');
        $('#etsTransModalTrans .js-ets-trans-analysis-text').addClass('hide');
    },
    hidePopupTranslating: function(){
        $('#etsTransPopupTranslating').removeClass('active');
    },
    getNbMoney: function(nbCharacters){
        if(ETS_TRANS_RATE && ETS_TRANS_RATE.length){
            var perChar = parseFloat(ETS_TRANS_RATE) /1000000;
            return nbCharacters * perChar;
        }
        return 0;
    },
    updateDataTranslating: function(nbTranslated, nbCharacters, totalTranslate){
        nbTranslated = nbTranslated || 0;
        totalTranslate = totalTranslate || 0;
        nbCharacters = nbCharacters || 0;
        var nbMoney = etsTransFunc.getNbMoney(nbCharacters);

        if(totalTranslate) {
            $('#etsTransPopupTranslating .total_translate').html(totalTranslate);
        }
        $('#etsTransPopupTranslating .nb_translated').html(nbTranslated);
        $('#etsTransModalTrans .js-ets-tran-btn-pause-translate').attr('data-nb-translated', nbTranslated);
        var nbCharFormated = nbCharacters.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        $('#etsTransPopupTranslating .nb_char').html(nbCharFormated);
        $('#etsTransModalTrans .js-ets-tran-btn-pause-translate').attr('data-nb-char', nbCharacters);
        $('#etsTransPopupTranslating .nb_money').html(nbMoney.toFixed(5)+' '+ETS_TRANS_RATE_SUFFIX);
    },

    updateTotalFilePath: function(nb_path){
        $('#etsTransModalTrans .js-ets-tran-btn-pause-translate').attr('data-total-path', nb_path);
    },
    setPageNameTranslating: function(pageName){
        $('#etsTransModalTrans #etsTransPopupTranslating .page_name').html(pageName);
    },
    setConfigTranslating: function(pageType, langSource, langTarget, fieldOption, ignore_product_name, ignore_content_has_product_name, auto_generate_link_rewrite){

        $('#etsTransModalTrans .js-ets-tran-btn-pause-translate').attr('abcccc', 'hhhhhhhhhhh');
        $('#etsTransModalTrans .js-ets-tran-btn-pause-translate').attr({
            "data-page-type": pageType,
            "data-lang-source": langSource,
            "data-lang-target": langTarget.toString(),
            "data-field-option": fieldOption,
            "data-ignore_product_name": "zzz",
            "data-ignore_content_has_product_name": ignore_content_has_product_name,
            "data-auto_generate_link_rewrite": auto_generate_link_rewrite,
        });

    },
    setTotalItemTranslating: function(totalTranslate){
        $('#etsTransPopupTranslating .total_translate').html(totalTranslate);
    },
    getPageName: function(pageType, isSingular){
        if(typeof isSingular === 'undefined'){
            isSingular = 0;
        }
        switch (pageType) {
            case 'product':
                if(isSingular)
                    return  etsTransFunc.trans('product');
                return etsTransFunc.trans('products');
            case 'category':
                if(isSingular)
                    return  etsTransFunc.trans('category');
                return etsTransFunc.trans('categories');
            case 'cms':
                if(isSingular)
                    return  etsTransFunc.trans('CMS');
                return etsTransFunc.trans('CMSs');
            case 'cms_category':
                if(isSingular)
                    return  etsTransFunc.trans('CMS_category');
                return etsTransFunc.trans('CMSs_categories');
            case 'manufacurer':
                if(isSingular)
                    return  etsTransFunc.trans('manufacturer');
                return etsTransFunc.trans('manufacturers');
            case 'supplier':
                if(isSingular)
                    return  etsTransFunc.trans('supplier');
                return etsTransFunc.trans('suppliers');
            case 'attribute':
                if(isSingular)
                    return  etsTransFunc.trans('attribute');
                return etsTransFunc.trans('attributes');
            case 'attribute_group':
                if(isSingular)
                    return  etsTransFunc.trans('attribute_group');
                return etsTransFunc.trans('attribute_groups');
            case 'feature':
                if(isSingular)
                    return  etsTransFunc.trans('feature');
                return etsTransFunc.trans('features');
            case 'feature_value':
                if(isSingular)
                    return  etsTransFunc.trans('feature_value');
                return etsTransFunc.trans('feature_values');
            case 'ets_extraproducttabs':
                if(isSingular)
                    return  etsTransFunc.trans('tab');
                return etsTransFunc.trans('tabs');
            default:
                if(isSingular)
                    return  etsTransFunc.trans('text');
                return etsTransFunc.trans('texts');

        }
    },
    setPauseTranslate: function(datResume){
        $('#etsTransPopupTranslating .msg-box-info').removeClass('hide');
        $('#etsTransPopupTranslating .text-loading').addClass('hide');
        $('#etsTransPopupTranslating .text-completed').addClass('hide');
        $('#etsTransPopupTranslating .text-initialize').addClass('hide');
        $('.js-ets-tran-btn-pause-translate').remove();
        if(!$('#etsTransModalTrans').find('.js-ets-trans-btn-resume-translate').length){
            $('#etsTransModalTrans .btn-group-translating').append(etsTransFunc.renderBtnResumeTranslate(datResume));
        }
        $('#etsTransModalTrans .close').removeClass('hide');
    },
    setResumeTranslate: function(datResume){
        $('#etsTransPopupTranslating .msg-box-info').removeClass('hide');
        $('#etsTransPopupTranslating .text-loading').removeClass('hide');
        $('#etsTransPopupTranslating .text-completed').addClass('hide');
        $('#etsTransPopupTranslating .text-initialize').addClass('hide');
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').next().remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-translate-from-zero').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-translate-from-resume').remove();
        if($('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').length){
            $('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').remove();
        }
        $('#etsTransModalTrans .btn-group-translating').append(etsTransFunc.renderBtnPauseTranslate(datResume));
        $('#etsTransModalTrans .close').addClass('hide');
    },
    setTranslateDone: function(){
        $('#etsTransPopupTranslating .msg-box-info').removeClass('hide');
        $('#etsTransPopupTranslating .text-loading').addClass('hide');
        $('#etsTransPopupTranslating .text-initialize').addClass('hide');
        $('#etsTransPopupTranslating .text-completed').removeClass('hide');
        $('#etsTransModalTrans .close').removeClass('hide');

        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').next('.btn-group-translate-close').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').remove();
        if(!$('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').length){
            $('#etsTransModalTrans .btn-group-translating').append(etsTransFunc.renderBtnCloseTranslate());
        }
        /**/
        var totalTrans = $('#etsTransPopupTranslating .total_translate').text();
        if(totalTrans && totalTrans != '0'){
            $('#etsTransPopupTranslating .js-ets-tran-btn-pause-translate').attr('data-nb-translated', totalTrans);
            $('#etsTransPopupTranslating .nb_translated').html(totalTrans);
        }
        $('#etsTransModalTrans .file-data-translated').addClass('hide');
    },
    setInitTrans: function(){
        $('#etsTransPopupTranslating .msg-box-info').addClass('hide');
        $('#etsTransPopupTranslating .text-loading').addClass('hide');
        $('#etsTransPopupTranslating .text-completed').addClass('hide');
        $('#etsTransPopupTranslating .text-initialize').removeClass('hide');
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').next('.btn-group-translate-close').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').remove();
        $('#etsTransModalTrans .js-ets-trans-btn-close-x-translating').addClass('active');
    },
    setTranslateError: function(errors){
        var errorHtml = '';
        if(typeof errors == 'string'){
            errorHtml = '<ul><li>'+errors+'</li></ul>';
        }
        else{
            errorHtml += '<ul>';
            $.each(errors, function(i, el){
                errorHtml += '<li>'+el+'</li>';
            });
            errorHtml += '</ul>';
        }
        $('#etsTransPopupTranslating .form-errors').html('<div class="alert alert-danger">'+errorHtml+'</div>');
        $('#etsTransPopupTranslating .text-loading').addClass('hide');
        $('#etsTransPopupTranslating .text-completed').addClass('hide');
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').next('.btn-group-translate-close').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').remove();
        $('#etsTransModalTrans .js-ets-trans-btn-close-x-translating').removeClass('active');
        if(!$('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').length){
            $('#etsTransModalTrans .btn-group-translating').append(etsTransFunc.renderBtnCloseTranslate());
        }
    },
    setBulkTranslating: function(){
        $('#etsTransPopupTranslating .text-loading').removeClass('hide');
        $('#etsTransPopupTranslating .text-completed').addClass('hide');
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-tran-btn-pause-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').next('.btn-group-translate-close').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-resume-translate').remove();
        $('#etsTransModalTrans .btn-group-translating').find('.js-ets-trans-btn-close-translate').remove();
    },
    showTranslatingField: function(){
        //
    },
    hideTranslatingField: function(){
    },
    showAnalysisCompleted: function(pageType, formData, totalItem){
        console.log('showAnalysisCompleted: ', formData)
        $.ajax({
            url: ETS_TRANS_LINK_AJAX_MODULE,
            type: 'GET',
            dataType: 'json',
            data: {etsTransGetFormAnalysisCompleted: 1},
            success: function (res) {
                if(res.success){
                    $('#etsTransModalTrans .js-ets-trans-analysis-text').removeClass('hide');
                    $('#etsTransModalTrans .ets-trans-content').html(res.form_html);
                    $('#etsTransPopupAnalysisCompleted').addClass('active');
                    $('#etsTransModalTrans .btn-group-analysis-completed').removeClass('hide');
                    $('#etsTransModalTrans .btn-group-translate').addClass('hide');
                    if(!formData.nb_text){
                        $('#etsTransPopupAnalysisCompleted .nothing-to-translate.hide').removeClass('hide');
                        $('#etsTransPopupAnalysisCompleted .info-analysis').addClass('hide');
                        $('#etsTransModalTrans .js-ets-trans-analysis-accept').addClass('hide');
                        return false;
                    }
                    if(pageType == 'email'){
                        $('#etsTransPopupAnalysisCompleted .text_type').html(etsTransFunc.trans('file_emails'));
                    }
                    $('#etsTransPopupAnalysisCompleted .nothing-to-translate').addClass('hide');
                    $('#etsTransPopupAnalysisCompleted .info-analysis').removeClass('hide');
                    $('#etsTransModalTrans .js-ets-trans-analysis-accept').removeClass('hide');

                    $('#etsTransPopupAnalysisCompleted .nb_text').html(formData.nb_text.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    $('#etsTransPopupAnalysisCompleted .nb_char').html(formData.nb_char.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    $('#etsTransPopupAnalysisCompleted .nb_money').html(formData.nb_money.toFixed(5)+' '+ETS_TRANS_RATE_SUFFIX);
                    $('#etsTransPopupAnalysisCompleted input[name=trans_source]').val(formData.trans_source);
                    $('#etsTransPopupAnalysisCompleted input[name=trans_target]').val(formData.trans_target);
                    $('#etsTransPopupAnalysisCompleted input[name=auto_detect_language]').val(formData.auto_detect_language);
                    $('#etsTransPopupAnalysisCompleted input[name=trans_option]').val(formData.trans_option);
                    $('#etsTransPopupAnalysisCompleted input[name=auto_generate_link_rewrite]').val(formData.auto_generate_link_rewrite);

                    var totalTarget = formData.trans_target.length;
                    if (totalTarget > 1){
                        $('#etsTransPopupAnalysisCompleted .nb_lang_target').html('x'+totalTarget+' '+etsTransFunc.trans('languages'));
                    }
                    else{
                        $('#etsTransPopupAnalysisCompleted .nb_lang_target').html('');
                    }
                    if (typeof formData.ignore_product_name !== 'undefined'){
                        $('#etsTransPopupAnalysisCompleted input[name=ignore_product_name]').val(formData.ignore_product_name);
                        $('#etsTransPopupAnalysisCompleted input[name=ignore_content_has_product_name]').val(formData.ignore_content_has_product_name);
                    }
                    if (typeof formData.etsTransFields !== 'undefined' && formData.etsTransFields.length) {
                        var container = document.querySelector('#etsTransPopupAnalysisCompleted');
                        for (i=0;i<formData.etsTransFields.length;i++){
                            // Create an <input> element, set its type and name attributes
                            var input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "etsTransFields[]";
                            input.value = formData.etsTransFields[i]
                            container.appendChild(input);
                        }
                    }

                    if(pageType == 'inter'){
                        var wd = formData.trans_wd || '';
                        if(typeof  wd !== 'string'){
                            wd = wd.join(',');
                        }
                        $('#etsTransPopupAnalysisCompleted input[name=trans_wd]').val(wd);
                    }
                    if(pageType == 'email'){
                        var mailOption = formData.mail_option || [];
                        mailOption = mailOption.join(',');

                        $('#etsTransPopupAnalysisCompleted input[name=mail_option]').val(mailOption);
                    }
                    $('#etsTransModalTrans .js-ets-trans-analysis-accept').attr({
                        'data-page-type': pageType,
                        'data-total-item': totalItem,
                        'data-total-path': formData.nb_path || 0,
                        'data-trans-all': formData.is_trans_all || 0,
                        'data-auto_generate_link_rewrite': formData.auto_generate_link_rewrite || 0,
                    });
                    if (pageType == 'product'){
                        $('#etsTransModalTrans .js-ets-trans-analysis-accept').attr({
                            'data-ignore_content_has_product_name': formData.ignore_content_has_product_name,
                            'data-ignore_product_name': formData.ignore_product_name
                        });
                    }
                    if(pageType == 'blog'){
                        $('#etsTransModalTrans .js-ets-trans-analysis-accept').attr({
                            'data-blog-type': formData.blog_type || '',
                        });
                    }

                }
            },
        });
    },
    showInitializing: function(){
        etsTransFunc.showPopupTrans();
        $('#etsTransModalTrans .trans-content-append').html('<div class="init-content text-loading"><span>'+etsTransFunc.trans('initializing')+'</span></div>');
    },
    hideInitializing: function(){
        $('#etsTransModalTrans .trans-content-append').html('');
        etsTransFunc.hidePopupTrans();
    },
    hideAnalysisCompleted: function(){
        $('#etsTransPopupAnalysisCompleted').removeClass('active');
    },
    formatTextContains: function(text){
        return text.replace(/\'/g, '\\\'').replace(/\"/g, '\\"');
    },
    updatePercentageTranslated: function(percent){
        $('#etsTransPopupTranslating .nb_percentage').html(percent);
    },
    updateListFileTranslated: function(listFile){
        var html = '';
        $.each(listFile, function(i, el){
            html += '<p class="file-item">'+el+'</p>';
        });
        $('#etsTransPopupTranslating .list_filepath').html(html);
    },
    updatePageTranslated: function(pageType, nbItem){
        var html = '';
        switch (pageType) {
            case 'product':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('product') : etsTransFunc.trans('products'));
                break;
            case 'category':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('category') : etsTransFunc.trans('categories'));
                break;
            case 'cms':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('CMS') : etsTransFunc.trans('CMSs'));
                break;
            case 'cms_category':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('CMS_category') : etsTransFunc.trans('CMS_categories'));
                break;
            case 'manufacturer':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('manufacture') : etsTransFunc.trans('manufactures'));
                break;
            case 'supplier':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('suppliers') : etsTransFunc.trans('supplier'));
                break;
            case 'blog_post':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('blog_posts') : etsTransFunc.trans('blog_post'));
                break;
            case 'blog_category':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('blog_categories') : etsTransFunc.trans('blog_category'));
                break;
            case 'megamemnu':
                html = etsTransFunc.trans('megamenu');
                break;
            case 'email':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('email') : etsTransFunc.trans('email'));
                break;
            case 'attribute':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('attributes') : etsTransFunc.trans('attribute'));
                break;
            case 'attribute_group':
                html = nbItem+' '+(nbItem > 1 ? etsTransFunc.trans('attribute_groups') : etsTransFunc.trans('attribute_group'));
                break;
        }
        $('#etsTransPopupTranslating .list_filepath').html(html);
    },
    hidePopupTrans: function(){
        $('body').removeClass('etsTransPopupActive');

    },
    showPopupTrans: function(){
        $('body').addClass('etsTransPopupActive');
        $('#etsTransModalTrans').modal({backdrop: 'static', keyboard: false});
        $('#etsTransModalTrans').modal('show');
    },
    showPopupMM: function () {
        $('.mm_forms.mm_popup_overlay').removeClass('hidden');
        $('.mm_forms.mm_popup_overlay .mm_menu_form.mm_pop_up').removeClass('hidden');
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
    validPhraseKey: function () {
        var items = $('.js-ets_trans_key_phrase_group');
        var valid = true;
        if (items && items.length) {
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                console.log('iiii: ', i, $(item).find('.ets_trans_key_phrase_from input').val())
                if (!$(item).find('.ets_trans_key_phrase_from input').val()) {
                    valid = false;
                    break;
                }
            }
        }
        return valid;
    },
    toast: function (txt) {
        var x = document.getElementById("snackbar");
        x.innerHTML=txt;
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 3000);
    },
    removeHtmlTag: function (html) {
        if (!html)
            return html;
        var div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    },
    getContentByShortCode: function (content, id_lang) {
        var find_arr = {};
        if (ETS_TRANS_GTE_810 && USE_PRODUCT_PAGE_V2) {
            find_arr = {
                'product_header_name_': {
                    'shortcode': '{product_name}',
                    'multi_lang': true,
                },
                'product_description_description_short_': {
                    'shortcode': '{product_summary}',
                    'multi_lang': true,
                },
                'product_description_description_': {
                    'shortcode': '{product_description}',
                    'multi_lang': true,
                },
                'product_seo_tags_': {
                    'shortcode': '{product_tags}',
                    'multi_lang': true,
                },
                'product_details_references_reference': {
                    'shortcode': '{product_reference}',
                    'multi_lang': false,
                },
                'select2-product_description_manufacturer-container': {
                    'shortcode': '{product_brand}',
                    'multi_lang': false,
                    'is_text': true
                },
                'current_lang': {
                    'shortcode': '{current_language}',
                    'multi_lang': false,
                }
            };
        } else {
            find_arr = {
                'form_step1_name_': {
                    'shortcode': '{product_name}',
                    'multi_lang': true,
                },
                'form_step1_description_short_': {
                    'shortcode': '{product_summary}',
                    'multi_lang': true,
                },
                'form_step1_description_': {
                    'shortcode': '{product_description}',
                    'multi_lang': true,
                },
                'form_step6_tags_': {
                    'shortcode': '{product_tags}',
                    'multi_lang': true,
                },
                'form_step6_reference': {
                    'shortcode': '{product_reference}',
                    'multi_lang': false,
                },
                'form_step1_id_manufacturer': {
                    'shortcode': '{product_brand}',
                    'multi_lang': false,
                    'is_select_input': true
                },
                'current_lang': {
                    'shortcode': '{current_language}',
                    'multi_lang': false,
                }
            };
        }
        for (var i = 0; i < Object.keys(find_arr).length; i++) {
            const key = Object.keys(find_arr)[i];
            const find = find_arr[key].shortcode;
            if (content.indexOf(find) !== -1) {
                var re = new RegExp(find, 'g');
                var id = find_arr[key].multi_lang ? key + id_lang : key;
                if (key == 'current_lang') {
                    if (typeof ETS_TRANS_ALL_LANGUAGES) {
                        for (var j = 0; j < ETS_TRANS_ALL_LANGUAGES.length; j++) {
                            if (ETS_TRANS_ALL_LANGUAGES[j].id_lang == id_language) {
                                content = content.replace(re, ETS_TRANS_ALL_LANGUAGES[j].name);
                                break;
                            }
                        }
                    }
                }else if ($('#' + id).length) {
                    var val = this.removeHtmlTag($('#' + id).val());
                    if (typeof find_arr[key].is_select_input !== "undefined" && find_arr[key].is_select_input) {
                        val = this.removeHtmlTag($('#' + id + ' option:selected').text());
                    }
                    if (typeof find_arr[key].is_text !== "undefined" && find_arr[key].is_text) {
                        val = this.removeHtmlTag($('#' + id).text());
                    }
                    content = content.replace(re, val);
                }
            }
        }
        return content;
    }
};

$(document).ready(function(){
    $(document).on('click', '.js-ets-trans-lang-source', function(){
        var idLang = $(this).attr('data-lang-id');
        var htmlLang = $(this).html();
        $('.js-ets-trans-btn-lang-source>.text-html').html(htmlLang);
        $(this).closest('form').find('input[name="trans_source"]').val(idLang);
        $('.js-ets-trans-lang-target').removeClass('hide');
        $('.js-ets-trans-lang-target.lang-'+idLang).addClass('hide');
        $('.js-ets-trans-lang-target.lang-'+idLang+' input[type="checkbox"]:checked').prop('checked', false);
        $('#etsTransFormTransPages .title_lang_source').html(htmlLang);
        var targetText = [];
        var targetShort = [];

        if($('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').length > 1){
            $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                targetText.push($(this).next('label').html());
                targetShort.push('<img src="'+$(this).next('label').find('img').attr('src')+'" /><span>'+($(this).parent().attr('data-isocode') || '')+'</span>');
            });
        }
        else{
            $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                targetText.push($(this).next('label').html());
                targetShort.push($(this).next('label').html());
            });
        }
        $('#etsTransFormTransPages .title_lang_target').html(targetText.join(','));
        if(targetShort.length)
            $('#etsTransFormTransPages #langTargetDropdown>.text-html').html(targetShort.join(', '));
        else
            $('#etsTransFormTransPages #langTargetDropdown>.text-html').html('--');

        var langTargetChecked = $('.js-ets-trans-lang-target-input:checked').length;
        var langTargetAll = $('.js-ets-trans-lang-target-input').length;

        if(langTargetChecked != langTargetAll-1){
            $('#etsTransSelectLangTarget_all').prop('checked', false);
        }
    });

    $(document).on('click', '#etsTransModalTrans [data-close="close"]', function () {
        $('#etsTransModalTrans').modal('hide');
        if (!$('#etsTransModalTrans').hasClass('translating')) {
            etsTransFunc.hidePopupTrans();
        }
    });
    $(document).on('click', '#etsTransModalTrans .js-ets-trans-analysis-cancel-trans', function () {
        $('#etsTransModalTrans').modal('hide');
        return false;
    });

    $(document).on('click', '#etsTransSelectLangTarget_all', function (e) {
        if($(this).is(':checked')){
            $('.js-ets-trans-lang-target:not(.hide) .js-ets-trans-lang-target-input').prop('checked', true);
            var targetText = [];
            var targetShort = [];
            if($('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').length > 1){
                $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                    targetText.push($(this).next('label').html());
                    targetShort.push('<img src="'+$(this).next('label').find('img').attr('src')+'" /><span>'+($(this).parent().attr('data-isocode') || '')+'</span>');
                });
            }
            else{
                $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                    targetText.push($(this).next('label').html());
                    targetShort.push($(this).next('label').html());
                });
            }
            $('#etsTransFormTransPages .title_lang_target').html(targetText.join(','));
            if(targetShort.length) {
                $('#etsTransFormTransPages #langTargetDropdown>.text-html').html(targetShort.join(', '));
                if(targetShort.length == 1){
                    $('#etsTransFormTransPages #langTargetDropdown>.text-html').addClass('single-lang');
                }
                else{
                    $('#etsTransFormTransPages #langTargetDropdown>.text-html').removeClass('single-lang');
                }
            }
            else
                $('#etsTransFormTransPages #langTargetDropdown>.text-html').html('--');
        }
        else{
            $('.js-ets-trans-lang-target:not(.hide) .js-ets-trans-lang-target-input').prop('checked', false);
            $(this).closest('.dropdown').find('#langTargetDropdown>.text-html').html('--');
            $('#etsTransFormTransPages .title_lang_target').html('--');
        }
        if(!$('.js-ets-trans-lang-target-input:checked').length){
            $('#etsTransModalTrans #langTargetDropdown .text-html').html('--');
        }
    });
    $(document).on('change', '.js-ets-trans-select-product-field', function (e) {
        e.preventDefault();
        var totalTarget = $('.js-ets-trans-select-product-field:not(.hide) .js-ets-trans-select-product-field-input').length;
        var totalChecked = $('.js-ets-trans-select-product-field:not(.hide) input[type="checkbox"]:checked').length

        console.log('click: ', totalTarget, totalChecked)
        if(totalChecked != totalTarget ){
            $('.js-ets-trans-select-product-field-all input[type="checkbox"]:checked').prop('checked', false);
        }
        else{
            $('.js-ets-trans-select-product-field-all input[type="checkbox"]').prop('checked', true);
        }
    })
    $(document).on('click', '.js-ets-trans-select-product-field-all', function (e) {
        e.preventDefault()
        var all_is_checked = $(this).find('#etsTransSelectListFieldsTransProduct_all').is(':checked');
        $(this).find('#etsTransSelectListFieldsTransProduct_all').prop("checked", !all_is_checked);
        $('.js-ets-trans-select-product-field:not(.hide) .js-ets-trans-select-product-field-input').each(function () {
            this.checked = !all_is_checked
        })
    })
    $(document).on('change', '.js-ets-trans-lang-target', function (e) {
        e.preventDefault();
        var totalTarget = $('.js-ets-trans-lang-target:not(.hide) .js-ets-trans-lang-target-input').length;
        console.log('total target: ', totalTarget)
        if($('.js-ets-trans-lang-target:not(.hide) input[type="checkbox"]:checked').length != totalTarget){
            $('.js-ets-trans-lang-target-all input[type="checkbox"]:checked').prop('checked', false);
        }
        else{
            $('.js-ets-trans-lang-target-all input[type="checkbox"]').prop('checked', true);
        }
        var targetText = [];
        var targetShort = [];
        if($('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').length > 1){
            console.log('length lang target: ', $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').length)
            $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                targetText.push($(this).next('label').html());
                targetShort.push('<img src="'+$(this).next('label').find('img').attr('src')+'" /><span>'+($(this).parent().attr('data-isocode') || '')+'</span>');
            });
        }
        else{
            $('.js-ets-trans-lang-target .js-ets-trans-lang-target-input:checked').each(function () {
                targetText.push($(this).next('label').html());
                targetShort.push($(this).next('label').html());
            });
        }
        $('#etsTransFormTransPages .title_lang_target').html(targetText.join(','));
        if(targetShort.length){
            $('#etsTransFormTransPages #langTargetDropdown>.text-html').html(targetShort.join(', '));
            if(targetShort.length == 1){
                $('#etsTransFormTransPages #langTargetDropdown>.text-html').addClass('single-lang');
            }
            else{
                $('#etsTransFormTransPages #langTargetDropdown>.text-html').removeClass('single-lang');
            }
        }
        else
            $('#etsTransFormTransPages #langTargetDropdown>.text-html').html('--');
        $(this).closest('.dropdown').toggleClass('open');
        if(!$('.js-ets-trans-lang-target-input:checked').length) {
            $('#etsTransModalTrans #langTargetDropdown .text-html').html('--');
            $('#etsTransFormTransPages .title_lang_target').html('--');
        }
        return false;
    });

    $(document).on('change', '#etsTransFormTransPages input[name=trans_option]', function () {
        $('#etsTransFormTransPages .field_option_text').html($(this).next('label').html());
    });

    $(document).on('change', '#etsTransFormTransPages input[name=ignore_product_name]', function () {
        if ($('#etsTransFormTransPages input[name=ignore_product_name]:checked').val() == 1){
            $('#etsTransFormTransPages .ignore_prod_name_text').removeClass('option_switch_off').addClass('option_switch_on');
        }
        else{
            $('#etsTransFormTransPages .ignore_prod_name_text').removeClass('option_switch_on').addClass('option_switch_off');
        }
        $('#etsTransFormTransPages .ignore_prod_name_text').html($(this).next('label').html());
    });
    $(document).on('change', '#etsTransFormTransPages input[name=ignore_content_has_product_name]', function () {
        if ($('#etsTransFormTransPages input[name=ignore_content_has_product_name]:checked').val() == 1){
            $('#etsTransFormTransPages .ignore_content_has_prod_name_text').removeClass('option_switch_off').addClass('option_switch_on');
        }
        else{
            $('#etsTransFormTransPages .ignore_content_has_prod_name_text').removeClass('option_switch_on').addClass('option_switch_off');
        }
        $('#etsTransFormTransPages .ignore_content_has_prod_name_text').html($(this).next('label').html());
    });

    $(document).on('click', '.js-ets-trans-modify-settings', function (e) {
        e.preventDefault();
        $(this).closest('.trans-data-info').addClass('hide');
        $('#etsTransFormTransPages .modify-setting').removeClass('hide');
        $(this).parents('.ets-trans-modal').addClass('ets_modify');
        return false;
    });
    $(document).on('click', '.js-btn-ets-trans-hide-modify-setting', function (e) {
        e.preventDefault();
        $('#etsTransFormTransPages .modify-setting').addClass('hide');
        $('#etsTransFormTransPages .trans-data-info').removeClass('hide');
        $(this).parents('.ets-trans-modal').removeClass('ets_modify');
        return false;
    });
    $(document).on('click', '.js-ets-trans-btn-close-translate', function (e) {
        e.preventDefault();
        $('#etsTransModalTrans').modal('hide');
        $('body').removeClass('etsTransPopupActive');
    });
    $(document).on('click', '.js-ets-trans-btn-close-x-translating', function (e) {
        e.preventDefault();
        etsTransFunc.hidePopupTranslating();
        $('body').removeClass('etsTransPopupActive');
    });

    $(document).on('hidden.bs.modal','#etsTransModalTrans', function (e) {
        $('body').removeClass('etsTransPopupActive');
        if($('.mm_forms.mm_popup_overlay').length){
            $('#etsTransModalTrans').remove();
        }
    });
    $(document).on('click', '.ets_dropdown button', function(e){
        if ( $(this).parents('.ets_dropdown').hasClass('open') ){
            $(this).stop().next('.dropdown-menu').removeClass('open');
            $(this).stop().parents('.ets_dropdown').removeClass('open');
        } else {
            $(this).stop().next('.dropdown-menu').addClass('open');
            $(this).stop().parents('.ets_dropdown').addClass('open');
        }

    });
    $(document).on('click', '.js-ets-btn-add-phrase', function (e) {
        e.preventDefault();
        if (!etsTransFunc.validPhraseKey()) {
            etsTransFunc.showErrorMessage(etsTransFunc.trans('invalid_phrase_key'));
            return false;
        }
        var id_row = $(this).attr('data-length');
        var html = '<div data-key="' + id_row + '" class="form-group row col-lg-12 ets_trans_key_phrase_group js-ets_trans_key_phrase_group ets_trans_key_phrase_group_' + id_row + '">';
            html += '<div class="group-input ets_trans_key_phrase_from col-lg-6">';
            html += '<input type="text" class="ets_trans_key_phrase_from_input" name="ETS_TRANS_KEY_PHRASE_FROM[]" id="ETS_TRANS_KEY_PHRASE_FROM_' + id_row + '" value="" />'
            html += '</div>';

            html += '<div class="group-input ets_trans_key_phrase_to col-lg-6">';
            html += '<div class="ets_trans_key_phrase_to_group">';
                if (ETS_TRANS_ALL_LANGUAGES.length) {
                    if (ETS_TRANS_ALL_LANGUAGES.length > 1)
                        html += '<div class="form-group row">';
                    for (var i = 0; i < ETS_TRANS_ALL_LANGUAGES.length; i++) {
                        var lang = ETS_TRANS_ALL_LANGUAGES[i];
                        var style = typeof ETS_TRANS_CURRENT_LANGUAGE !== "undefined" && lang['id_lang'] != ETS_TRANS_CURRENT_LANGUAGE.id ? 'style="display:none"' : '';
                        html += '<div class="translatable-field lang-'+ lang["id_lang"] +'" ' + style + '>';
                        html += '<div class="col-lg-9">';
                        html += '<input type="text" class="ets_trans_key_phrase_to_input" name="ETS_TRANS_KEY_PHRASE_TO_' + lang['id_lang'] + '[]" id="ETS_TRANS_KEY_PHRASE_TO_' + lang['id_lang'] + '_' + id_row + '" value="" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();" />'
                        html += '</div>';
                        html += '<div class="col-lg-2">';
                        html += '<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">';
                        html += lang['iso_code'];
                        html += ' <span class="caret"></span>';
                        html += '</button>';
                        html += '<ul class="dropdown-menu">';
                        for (var j = 0; j < ETS_TRANS_ALL_LANGUAGES.length; j++) {
                            const _lang = ETS_TRANS_ALL_LANGUAGES[j];
                            html += '<li><a href="javascript:hideOtherLanguage(' + _lang['id_lang'] + ');" tabindex="-1">' + _lang['name'] + '</a></li>';
                        }
                        html += '</ul>';
                        html += '</div>';
                        html += '</div>';
                    }

                    if (ETS_TRANS_ALL_LANGUAGES.length > 1)
                        html += '</div>';
                }
                html += '<button data-key="' + id_row + '" class="ets_button ets-btn ets-btn-delete-phrase js-ets-btn-delete-phrase" title="' + etsTransFunc.trans("delete") + '"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></button>';
            html += '</div>';
            html += '</div>';
        html += '</div>';
        $(this).attr('data-length', parseInt(id_row) + 1);
        var items = $('.js-ets_trans_key_phrase_group');
        $(this).parent('.js-ets_trans_key_phrase').find('.js-ets_trans_key_phrase_row').append(html);
        if (items && items.length == 1) {
            $(items[0]).find('.ets_trans_key_phrase_to .ets_trans_key_phrase_to_group').append('<button data-key="0" class="ets_button ets-btn ets-btn-delete-phrase js-ets-btn-delete-phrase" title="' + etsTransFunc.trans("delete") + '"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></button>')
        }
    });
    $(document).on('click', '.js-ets-btn-delete-phrase', function (e) {
        e.preventDefault();
        console.log('click js-ets-btn-delete-phrase', $(this).parents('.js-ets_trans_key_phrase_group'))
        $(this).parents('.js-ets_trans_key_phrase_group').remove();
        var items = $('.js-ets_trans_key_phrase_group');
        if (items && items.length == 1) {
            console.log('items', items)
            $(items[0]).find('.js-ets-btn-delete-phrase').remove();
            console.log('kkk', $(items[0]), $(items[0]).find('.ets_trans_key_phrase_from > label'))
            if (!$(items[0]).find('.ets_trans_key_phrase_from > label') || !$(items[0]).find('.ets_trans_key_phrase_from > label').length) {
                $(items[0]).find('.ets_trans_key_phrase_from').prepend('<label class="ets-trans-label ets-trans-label-phrase required">' + etsTransFunc.trans("label_phrase_key") + '</label>');
                $(items[0]).find('.ets_trans_key_phrase_to').prepend('<label class="ets-trans-label ets-trans-label-phrase">' + etsTransFunc.trans("label_translate_to") + '</label>');
            }
        }
    });
    $(document).mouseup(function (e)
    {
        if (!$('.ets_dropdown.open').is(e.target)&& $('.ets_dropdown.open').has(e.target).length === 0)
        {
            $('.ets_dropdown').removeClass('open').find('.dropdown-menu').removeClass('open');
        }
    });

    window.onclick = function(event) {
        var modal = document.getElementById('etsTransModalTrans');
        if (event.target == modal && !$('#etsTransModalTrans').hasClass('translating')) {
            etsTransFunc.hidePopupTrans();
        }

    }
});