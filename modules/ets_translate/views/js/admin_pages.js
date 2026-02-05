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

$(document).ready(function () {
    etsTranslateDefine.initBtnTranslate();
    $(document).on('click', '.js-ets-trans-btn-trans-toolbar', function(){
        etsTranslateDefine.getFormSelectTranslate(etsTransPageId, null, 0, null, this);
        return false;
    });

    $(document).on('click', '.js-ets-trans-btn-translate-page, .js-ets-trans-analysis-accept', function(){
        var formData = $(this).closest('form').serializeArray();
        formData = etsTranslateDefine.formatFormData(formData);
        $('#etsTransModalTrans .form-errors').html('');
        var transSource = formData.trans_source || null;
        var transTarget = formData.trans_target || [];
        var transOption = formData.trans_option || null;
        var auto_detect_language = formData.auto_detect_language || null;
        var pageType = $(this).attr('data-page-type') || etsTransPageType;
        var fieldTrans = $(this).attr('data-field') || null;
        var isTransAll = $(this).attr('data-trans-all') || 0;
        var totalTranslate = $(this).attr('data-total-item') || 0;
        if (!formData.ignore_content_has_product_name){
            formData.ignore_content_has_product_name = $(this).attr('data-ignore_content_has_product_name');
        }
        if (!formData.ignore_product_name){
            formData.ignore_product_name = $(this).attr('data-ignore_product_name');
        }
        if (!formData.auto_generate_link_rewrite){
            formData.auto_generate_link_rewrite = $(this).attr('data-auto_generate_link_rewrite');
        }

        $('#etsTransModalTrans .form-errors').html('');
        if(!transTarget || !transTarget.length){
            etsTranslateDefine.showErrorTrans(etsTranslateDefine.trans('target_lang_required'));
            return false;
        }
        var transData = {};
        if((pageType == 'ps_mainmenu'|| pageType == 'ets_extraproducttabs' || (pageType == 'blockreassurance' && etsTranslateDefine.blockreassurance.isNewVersion())) && isTransAll == 1){
            //
        }
        else{
            transData = etsTranslateDefine.getTransDataPage(transSource, transTarget, transOption, pageType, fieldTrans);
        }
        formData.trans_data = transData;
        formData.trans_all = isTransAll == 1 ? 1 : 0;
        formData.nb_translated = 0;
        formData.nb_char_translated = 0;
        formData.total_item = totalTranslate;
        formData.page_id = formData.page_id || etsTransPageId;

        if(etsTransIsDetailPage){
            if(fieldTrans && fieldTrans.indexOf('ets_ept_content') !== -1){
                formData.col_data = {};
                formData.col_data[fieldTrans] = 'content';
                formData.ept_tab_id= etsTranslateDefine.ets_extraproducttabs.getIdTabFromPrefix(fieldTrans);
            }
            else {
                formData.col_data = etsTranslateDefine.getColData(etsTransPageType);
            }
        }

        if (etsTransPageType == 'product' && $('[id^="ets_ept_content"]').length){
            $('[id^="ets_ept_content"],[id^="ets_ept_file_desc"]').each(function (){
                var id = $(this).attr('id');
                var prefixId = id.replace(/[0-9]+$/, '');
                var itemTransData = etsTranslateDefine.getTransDataPage(formData.trans_source, formData.trans_target, formData.trans_option, etsTransPageType, prefixId);
                formData.trans_data.source = Object.assign( formData.trans_data.source, itemTransData.source);
                if (formData.trans_data.target && itemTransData.target){
                    Object.keys(itemTransData.target).forEach(function (key) {
                        formData.trans_data.target[key] = Object.assign( formData.trans_data.target[key], itemTransData.target[key]);
                    });
                }
                else if(itemTransData.target)
                    formData.trans_data.target = itemTransData.target;
                var colData = {};
                if(id.match(/ets_ept_file_desc/))
                    colData[prefixId] = 'file_desc';
                else
                    colData[prefixId] = 'content';
                formData.col_data = Object.assign(formData.col_data,colData);
            });
        }

        if($(this).hasClass('trans-multiple')){
            etsTransFunc.updateDataTranslating(0,0,0);
            etsTransFunc.showPopupTranslating(parseInt(isTransAll), pageType, totalTranslate, transSource, transTarget, transOption, formData.ignore_product_name, formData.ignore_content_has_product_name, formData.auto_generate_link_rewrite);
            if (isTransAll == 1) {
                etsTransFunc.setResumeTranslate({
                    pageType: pageType,
                    nbTranslated: 0,
                    nbCharTranslated: 0,
                    langSource: transSource,
                    autoDetectLang: auto_detect_language,
                    langTarget: transTarget,
                    fieldOption: transOption,
                    ignore_product_name: formData.ignore_product_name,
                    ignore_content_has_product_name: formData.ignore_content_has_product_name,
                    auto_generate_link_rewrite: formData.auto_generate_link_rewrite
                });
            }
        }
        else{
            etsTransFunc.showTranslatingField();
        }

        $('#etsTransPopupAnalysisCompleted').removeClass('active');
        etsTranslateDefine.stopTranslate = false;
        if(!isTransAll){
            etsTransFunc.showTranslatingField();
        }



        etsTranslateDefine.translatePage(formData, $(this), pageType);

        return false;
    });

    $(document).on('click', '.js-ets-trans-bulk-trans', function(){
        if(etsTransPageType == 'product') {
            var ids = etsTranslateDefine.product.getBulkTransIds();
            console.log('id', ids);
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, null, 0, null, this);
        }
        else if(etsTransPageType == 'category') {
            var ids = etsTranslateDefine.category.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, null, 0, null, this);
        }
        else if(etsTransPageType == 'cms') {
            if($(this).hasClass('is_cms_category'))
            {
                var ids = etsTranslateDefine.cms_category.getBulkTransIds();
                if(!ids){
                    return false;
                }
                etsTranslateDefine.getFormSelectTranslate(ids, 'cms_category', 0, null, this);
            }
            else {
                var ids = etsTranslateDefine.cms.getBulkTransIds();
                if(!ids){
                    return false;
                }
                etsTranslateDefine.getFormSelectTranslate(ids, 'cms', 0, null, this);
            }
        }
        else if(etsTransPageType == 'manufacturer') {
            var ids = etsTranslateDefine.manufacturer.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'manufacturer');
        }
        else if(etsTransPageType == 'supplier') {
            var ids = etsTranslateDefine.supplier.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'supplier');
        }
        else if(etsTransPageType == 'attribute_group') {
            var ids = etsTranslateDefine.attribute_group.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'attribute_group');
        }
        else if(etsTransPageType == 'attribute') {
            var ids = etsTranslateDefine.attribute.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'attribute');
        }
        else if(etsTransPageType == 'feature') {
            var ids = etsTranslateDefine.feature.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'feature');
        }
        else if(etsTransPageType == 'feature_value') {
            var ids = etsTranslateDefine.feature_value.getBulkTransIds();
            if(!ids){
                return false;
            }
            etsTranslateDefine.getFormSelectTranslate(ids, 'feature_value');
        }
    });

    $(document).on('click', '.js-ets-trans-trans-all', function(){
        var ids = '';
        var pageType = null;
        if(etsTransPageType == 'cms') {
            if($(this).hasClass('is_cms_category'))
            {
                pageType = 'cms_category';
            }
            else {
                pageType = 'cms';
            }
        }
        else{
            pageType = etsTransPageType;
        }

        console.log('ets: ', etsTransPageType, pageType)

        if(pageType)
            etsTranslateDefine.getFormSelectTranslate(ids, pageType, 1, null, this);
        return false;
    });

    $(document).on('click', '.js-ets-trans-analysis-text', function(){
        var pageType = $(this).attr('data-page-type') || etsTransPageType;
        if(pageType == 'cms' && $(this).hasClass('is_cms_category')){
            pageType = 'cms_category';
        }
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.nb_text = 0;
        formData.nb_char = 0;
        formData.nb_money = 0;
        formData.is_trans_all = $(this).attr('data-trans-all') || 0;
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
                    etsTranslateDefine.analysisBeforeTranslate(pageType, formData, 0);
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

    $(document).on('click', '.js-ets-trans-btn-trans-field-item', function () {
        var itemTrans = $(this).attr('data-field');

        if(ETS_TRANS_IS_AUTO_CONFIG == 1){
            var langTarget = ETS_TRANS_DEFAULT_CONFIG.lang_target || '';
            var formData = {
                trans_option: ETS_TRANS_DEFAULT_CONFIG.field_option || '',
                trans_source: ETS_TRANS_DEFAULT_CONFIG.lang_source || '',
                trans_target: langTarget.split(','),
                ignore_product_name: ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME,
                ignore_content_has_product_name: ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME,
                auto_generate_link_rewrite: ETS_TRANS_AUTO_GENERATE_LINK_REWRITE,
            };
            if (etsTransPageType == 'product' && itemTrans == 'form_image_legend_') {
                formData.image_id = $(this).closest('#product-images-container').find('#product-images-dropzone .dz-image-preview.active').attr('data-id').trim();
            }
            formData.trans_data = etsTranslateDefine.getTransDataPage(formData.trans_source, formData.trans_target, formData.trans_option, etsTransPageType, itemTrans);
            if(etsTransIsDetailPage){
                if($(this).hasClass('trans-item-extra-tab')){
                    formData.col_data = {};
                    if(itemTrans.match(/ets_ept_file_desc/)){
                        formData.col_data[itemTrans] = 'file_desc';
                    }
                    else
                        formData.col_data[itemTrans] = 'content';

                    formData.ept_tab_id= etsTranslateDefine.ets_extraproducttabs.getIdTabFromPrefix(itemTrans);
                }
                else
                    formData.col_data = etsTranslateDefine.getColData(etsTransPageType);
            }

            formData.page_id = etsTransPageId || 0;
            etsTranslateDefine.translatePage(formData, $(this), etsTransPageType);
            return false;
        }
        etsTranslateDefine.getFormSelectTranslate(null, null, 0, itemTrans, this);
        return false;
    });

    $(document).on('click', '.js-ets-trans-btn-strop-translate', function () {
        etsTranslateDefine.stopTranslatePage();
    });

    $(document).on('click', '.js-ets-tran-btn-pause-translate', function () {
        etsTranslateDefine.stopTranslatePage();
        var dataPause = {};
        var $this = $(this);
        dataPause.pageType = $(this).attr('data-page-type');
        dataPause.nbTranslated = $(this).attr('data-nb-translated');
        dataPause.nbCharTranslated = $(this).attr('data-nb-char');
        dataPause.langSource = $(this).attr('data-lang-source');
        dataPause.langTarget = $(this).attr('data-lang-target');
        dataPause.fieldOption = $(this).attr('data-field-option');
        dataPause.ignore_product_name = $(this).attr('data-ignore_product_name') ?? '';
        dataPause.ignore_content_has_product_name = $(this).attr('data-ignore_content_has_product_name') ?? '';
        dataPause.auto_generate_link_rewrite = $(this).attr('data-auto_generate_link_rewrite') ?? '';
        etsTranslateDefine.pauseTranslate($(this), dataPause);
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
        formData.pageType = dataResume.pageType;
        formData.isDetailPage = 0;
        formData.page_id = '';
        formData.trans_source = dataResume.langSource;
        formData.trans_target = dataResume.langTarget.split(',');
        formData.trans_option = dataResume.fieldOption;
        formData.trans_all = 1;
        formData.nb_translated = dataResume.nbTranslated;
        formData.nb_char_translated = dataResume.nbCharTranslated;
        formData.ignore_product_name = dataResume.ignore_product_name;
        formData.ignore_content_has_product_name = dataResume.ignore_content_has_product_name;
        formData.auto_generate_link_rewrite = dataResume.auto_generate_link_rewrite;
        etsTranslateDefine.stopTranslate = false;
        etsTranslateDefine.translatePage(formData, $(this) ,dataResume.pageType);
    });

    $(document).on('click', '.js-ets-trans-translate-from-resume', function (e) {
        e.preventDefault();
        etsTranslateDefine.stopTranslate = false;
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        formData.trans_target = formData.trans_target.split(',');
        formData.trans_all = 1;
        formData.isDetailPage = 0;
        etsTransFunc.showPopupTranslating(1, formData.pageType, formData.total_item, formData.trans_source, formData.trans_target, formData.trans_option, formData.ignore_product_name, formData.ignore_content_has_product_name, formData.auto_generate_link_rewrite);
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
        etsTransFunc.updateDataTranslating(formData.nb_translated, formData.nb_char_translated, formData.total_item);
        etsTranslateDefine.translatePage(formData, $(this), formData.pageType);
    });
    $(document).on('click', '.js-ets-trans-translate-from-zero', function (e) {
        e.preventDefault();
        var formData = $(this).closest('form').serializeArray();
        formData = etsTransFunc.formatFormData(formData);
        $('#etsTransModalTrans').modal('hide');
        etsTranslateDefine.getFormSelectTranslate('', formData.pageType, 1, null, this, 1);
    });

    if(etsTransPageType == 'product' && etsTransIsDetailPage){
        $(document).on('click', '[type=submit]', function(){
            $('.ets-trans-field-translated-success').removeClass('ets-trans-field-translated-success');
        });
    }


    $(document).on('click', '#link_block_form .add-collection-btn', function () {
        $('[id*=form_link_block_custom_]').each(function () {
            if($(this).attr('id').indexOf('_url') === -1){
                if(!$(this).closest('.form-group').find('.js-ets-trans-btn-trans-field-item').length){
                    $(this).closest('.form-group').addClass('ets-trans-field-boundary form-helper').append(etsTranslateDefine.renderBtnTransFieldItem($(this).attr('id')))
                }
            }

        });
    });

    $(document).on('click', '#reminder_listing .psre-edit', function () {
        etsTransPageId = $(this).attr('data-id');
    });

    $(document).on('hidden.bs.modal','#etsTransModalTrans .close,#etsTransModalTrans .btn-group-translate-close', function (e) {
        etsTranslateDefine.stopTranslatePage();
    });

    //add button trans for alt image
    $('#product-images-form').on('DOMSubtreeModified', function () {
        $('[id*=form_image_legend_]').each(function () {
            if (!$(this).closest('.translations').find('.js-ets-trans-btn-trans-field-item ').length) {
                $(this).closest('.translations').addClass('ets-trans-field-boundary').append(etsTranslateDefine.renderBtnTransFieldItem('form_image_legend_', 'ets-trans-alt-img-product')).parent().addClass('ets_button_top');
                if ($(this).is('textarea')) {
                    $(this).closest('.translations').addClass('form-helper-editor');
                }

            }
        });
    })

});

function etsRefreshFormChatGPT(el) {
    $(el).closest('.ets-trans-chatgpt-field-boundary').find('.js-ets-trans-btn-trans-chatgpt-field-item').removeClass('hide');
    $(el).closest('.js-ets-trans-form-chatgpt').find('.js-ets-trans-textarea-chatgpt').val('');
    $(el).closest('.js-ets-trans-form-chatgpt').addClass('hide');
}










