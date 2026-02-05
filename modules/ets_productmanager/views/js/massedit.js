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
var xhrMasseditAjax = false;
var etsPMNAddCustomerSpecific = function(event,data,formatted)
{
    if (data == null)
        return false;
    $('#specific_price_id_customer_hide').val(data[0]);
    if($('#specific_price_id_customer').next('.customer_selected').length <=0)
    {
       $('#specific_price_id_customer').after('<div class="customer_selected">'+data[1]+' <span class="delete_customer_search">delete</span><div>');
       $('#specific_price_id_customer').val(data[0]);  
    }
}
$(document).ready(function(){
    $(document).on('click','.close_popup,button[name="btnCancel"]',function(){
        if($('.ets_product_popup').length)
        {
            $('.ets_product_popup').removeClass('show');
            $('#block-form-popup-dublicate').html('');
        }
        if($('.wapper-change-product.popup').length)
        {
            var id_product = $('.wapper-change-product.popup.show').parents('tr').data('product-id');
            $('.wapper-change-product.popup').removeClass('show');
            tinymce.triggerSave();
            ets_pmnChangeTinymceInput(id_product);
        }
        if($('tr[data-product-id="'+id_product+'"] .is_lang_default').length)
        {
            $('tr[data-product-id="'+id_product+'"] .is_lang_default').each(function(){
                var text_value = $(this).val().replace(/(<([^>]+)>)/gi, "");
                if(ets_max_lang_text && text_value.length > ets_max_lang_text)
                {
                    text_value = text_value.substring(0,ets_max_lang_text)+'...';
                }
                $(this).parents('td').find('.span_change_product .content').html(text_value ? '<div class="content_info">'+text_value+'</div>' :'');
            });
        }
    });
    $(document).on('click', '.ets_pm_viewmore:not(.less)', function(e){
        e.preventDefault();
        var text_less = $(this).attr('data-less');
        $(this).addClass('less').html(text_less);
        $(this).parent('.popup_change_product.more,.span_change_product.more').addClass('show_less');
    });

    $(document).on('click', '.ets_pm_viewmore.less', function(e){
        e.preventDefault();
        var text_more = $(this).attr('data-more');
        $(this).removeClass('less').html(text_more);
        $(this).parent('.popup_change_product.more,.span_change_product.more').removeClass('show_less');
    });
    if ( $('.ets_td').length > 0 ){
        $('.ets_td .popup_change_product, .ets_td .span_change_product:not(.more)').each(function(){
            var content_height = $(this).find('.content_info').height();
            if ( content_height > 60 ){
                $(this).addClass('more');
            }
        });
    }
    $(document).on('change','.paginator_select_limit',function(e){
        if($(this).attr('name')!='paginator_matching_products_select_limit')
            $(this).parents('form').submit();
    });
    if($('#list-pmn_products .not_items_found').length>0)
    {
        $('button[name="btnSubmitMassedit"]').attr('disabled','disabled');
        $('.btn-save-msssive-template-popup').attr('disabled','disabled');
    }    
    else
    {
        $('button[name="btnSubmitMassedit"]').removeAttr('disabled');
        if(!$('#block-wait-filter-products:not(:hidden)').length)
        {
            $('.btn-save-msssive-template-popup').removeAttr('disabled');
        }
        else
            $('.btn-save-msssive-template-popup').attr('disabled','disabled');
    }
    if($('.module_confirmation.alert-success').length)
    {
        setTimeout(function(){
            $('.module_confirmation.alert-success').parent().remove();
        },3000);
    }
    $(document).on('change input','#ets_pmn_product_form .massedit-field,.category-tree input.category',function(){
       ets_pmn_checkFormEditAction(); 
    });
    $(document).on('click','.field-error',function(){
        $(this).removeClass('field-error'); 
    });
    $(document).on('keyup','.tagify-container input[type="text"]',function(e){
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 || keyCode===188) { 
            if($('form input.tagify').length)
            {
                $('form input.tagify').each(function(){
                    $(this).val($(this).tagify('serialize'));
                });
                $('.tagify-container a').unbind('click');
                $('.tagify-container a').click(function(e){
                    e.preventDefault();
                    $(this).addClass('abc');
                    var spans = $(this).parents('.tagify-container').find('a');
                    var idTagify = $(this).parents('.tagify-container').prev('input').attr('id');
                    var indexTag = 0;
                    spans.each(function() {
                        if($(this).hasClass('abc')) {
                            return;
                        }
                        indexTag++;
                    }); 
                    $('#'+idTagify).tagify('remove',indexTag);
                    $('form input.tagify').each(function(){
                        $(this).val($(this).tagify('serialize'));
                    });
                    ets_pmn_checkFormEditAction();
                });
                ets_pmn_checkFormEditAction();
            }
        }
    });
    $(document).on('change','.list-templates-massives',function(){
        if(!$('#massedit_form').hasClass('loading'))
        {
            $('#massedit_form').addClass('loading');
            var id_ets_pmn_massedit = $(this).val();
            $.ajax({
                url: '',
                data: 'getFormTemplatesMassive&id_ets_pmn_massedit='+id_ets_pmn_massedit,
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $('#massedit_form').removeClass('loading');
                    if(json.error)
                        showErrorMessage(json.error);
                    if(json.html_form)
                        $('#massedit_form .block-massedit-form').html(json.html_form);
                    if($('#massedit_form .row_massedit .condition_field').length)
                    {
                        $('#massedit_form .row_massedit .condition_field').each(function(){
                            ets_pmn_displayFormMassedit($(this).parents('.row_massedit'),$(this).val());
                        });
                    }
                    $('li.list-massedit').html(json.list_template_massedit);
                    $('.steps.nbr_steps_active_1').show();
                    $('#massedit_form').show();
                    $('#form_step2').hide();
                    $('#form_step3').hide();
                    $('.step4').removeClass('active').removeClass('selected').addClass('disabled');
                    $('.step4 a').removeClass('active').removeClass('selected').addClass('disabled');
                },
                error: function(xhr, status, error)
                { 
                    $('#massedit_form').removeClass('loading');
                }
            });
        }
    });
    $(document).on('click','.btn-save-msssive-template-popup',function(e){
        e.preventDefault();
        if(!ets_pmn_checkFormFilter())
            return false;
        if($('.btn-add-filter-product:not(:hidden)').length==0)
            $('.ets_product_popup').addClass('show');
        else
            showErrorMessage('Product filter is required');
    });
    $(document).on('click','.btn-back-1,.step1',function(e){
        e.preventDefault();
        if(!$('.step4').hasClass('active'))
        {
            $('.steps.nbr_steps_active_1').show();
            $('#massedit_form').show();
            $('#form_step2').hide();
            $('#form_step3').hide();
        }
    });
    $(document).on('click','.btn-back-2',function(e){
        e.preventDefault();
        $('#form_step2').show();
        $('#form_step3').hide();
    });
    $(document).on('click','.step2',function(e){
        e.preventDefault();
        if($(this).hasClass('selected') && !$('.step4').hasClass('active'))
        {
            $('#form_step2').show();
            $('#form_step3').hide();
        }
    });
    if($('#specific_price_id_customer').length)
    {
        $('#specific_price_id_customer').autocomplete(link_search_customer,{
        		minChars: 1,
        		autoFill: true,
        		max:20,
        		matchContains: true,
        		mustMatch:false,
        		scroll:false,
        		cacheLength:0,
        		formatItem: function(item) {
        			return item[1]+' ('+item[2]+')';
        		}
   	    }).result(etsPMNAddCustomerSpecific); 
        $(document).on('click','.delete_customer_search',function(){
            $('.customer_selected').remove();
            if($('#customerFilter').length)
            {
                $('#id_customer').val('');
                $('#customerFilter').val('');
            }
            if($('#specific_price_id_customer_hide').length)
            {
                $('#specific_price_id_customer_hide').val('');
                $('#specific_price_id_customer').val('');
            }
            
        });
    }
    if ($(".datepicker input").length > 0) {
        var dateToday = new Date();
        $(".datepicker input").removeClass('hasDatepicker');
		$(".datepicker input").datepicker({
			dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minDate: dateToday,
		});
	}
    $(document).on('click','#specific_price_leave_bprice',function(){
       if($(this).is(':checked'))
            $('input#specific_price_product_price').attr('disabled','disabled');
       else
            $('input#specific_price_product_price').removeAttr('disabled');
    });
    $(document).on('change','#specific_price_sp_reduction_type',function(){
        $('#specific_price_sp_reduction').prev('.input-group-prepend').html('<span class="input-group-text">'+$('#specific_price_sp_reduction_type option[value="'+$(this).val()+'"]').html()+'</span>');
        if($(this).val()=='percentage')
        {
            $('select#specific_price_sp_reduction_tax').hide();
        } 
        else
           $('select#specific_price_sp_reduction_tax').show(); 
    });
    if ($(".ets_pmn_datepicker input").length > 0) {
        $('.hasDatepicker').removeClass('hasDatepicker');
		$(".ets_pmn_datepicker input").datepicker({
			dateFormat: 'yy-mm-dd',
		});
	}
    $(document).on('click','.pmn_logmassedit_boxs',function(){
        ets_pmn_updateBulkMenu();
    });
    $(document).on('click','button[name="submitSaveMasseEditProduct"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading');
            $('.module_error').parent().remove();        
            ets_pmn_submitSaveMasseEditProduct(0,0,1);
        }
    });
    $(document).on('click','button[name="submitSaveMasseActionEdit"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            tinymce.triggerSave();
            if($('form input.tagify').length)
            {
                $('form input.tagify').each(function(){
                    $(this).val($(this).tagify('serialize'))
                });
            }
            $('.module_error.alert.alert-danger').parent('.bootstrap').remove();
            $(this).addClass('loading');
            $('.module_error').parent().remove();
            var formData = new FormData($('button[name="submitSaveMasseActionEdit"]').parents('form').get(0));
            formData.append('submitSaveMasseActionEdit',1);
            xhrMasseditAjax = $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $('button[name="submitSaveMasseActionEdit"]').removeClass('loading');
                    if(json.success)
                    {
                        if(json.html_form)
                        {
                            if($('#form_step3').length==0)
                                $('#form_step2').after('<div id="form_step3">'+json.html_form+'</div>');
                            else
                                $('#form_step3').html(json.html_form);
                            $('#form_step3').show();
                            $('#form_step2').hide();
                            $('.btn-save-msssive-template-popup').removeAttr('disabled');
                        }
                        else
                            window.location.href = json.url_reload;
                    }
                    if(json.error_required)
                    {
                        showErrorMessage(json.error_required);
                        if(!$('label[for="'+json.field+'"]').parents('.ets_pmn_tab_content').hasClass('active'))
                        {
                            var current_tab = $('label[for="'+json.field+'"]').parents('.ets_pmn_tab_content').data('tab');
                            $('.ets_pmn_product_tab .ets_pmn_tab').removeClass('active');
                            $('.ets_pmn-form-content .ets_pmn_tab_content').removeClass('active');
                            $('.ets_pmn_product_tab .ets_pmn_tab[data-tab="'+current_tab+'"]').addClass('active');
                            $('.ets_pmn-form-content .ets_pmn_tab_content.'+current_tab).addClass('active');
                        }
                        if(json.id_lang_required!=0)
                        {
                            $('.translatable-field').hide();
                            $('.translatable-field.lang-'+json.id_lang_required).css('display', '');                          
                        }
                        if(json.field=='specific_prices')
                        {
                            $('#specific_price_sp_reduction').focus();
                        }
                        else
                        {
                            if($('.massedit-form-field.'+json.field+ ' input[type="text"]:not(:hidden)').length)
                                $('.massedit-form-field.'+json.field+ ' input[type="text"]:not(:hidden)').focus();
                            if($('.massedit-form-field.'+json.field+ ' textarea:not(:hidden)').length)
                                    $('.massedit-form-field.'+json.field+ ' textarea:not(:hidden)').focus();
                            if($('.massedit-form-field.'+json.field+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').length)
                            {
                                var Idrte = $('.massedit-form-field.'+json.field+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').attr('id');
                                tinyMCE.get(Idrte).execCommand('mceFocus', false);
                            }
                        }
                         
                    }
                    if(json.errors)
                    {
                        $('#ets_pmn_product_form').before(json.errors);
                        if(json.fields_error)
                        {
                            var active_tab = false;
                            var active_lang = false;
                            $(json.fields_error).each(function(){
                                if(!active_lang && this.id_lang!=0)
                                {
                                    active_lang = true;
                                    $('.translatable-field').hide();
                                    $('.translatable-field.lang-'+this.id_lang).css('display', '');                          
                                }
                                if(!active_tab)
                                {
                                    active_tab = true;
                                    if(!$('label[for="'+this.field_error+'"]').parents('.ets_pmn_tab_content').hasClass('active'))
                                    {
                                        var current_tab = $('label[for="'+this.field_error+'"]').parents('.ets_pmn_tab_content').data('tab');
                                        $('.ets_pmn_product_tab .ets_pmn_tab').removeClass('active');
                                        $('.ets_pmn-form-content .ets_pmn_tab_content').removeClass('active');
                                        $('.ets_pmn_product_tab .ets_pmn_tab[data-tab="'+current_tab+'"]').addClass('active');
                                        $('.ets_pmn-form-content .ets_pmn_tab_content.'+current_tab).addClass('active');
                                        if(this.field=='specific_prices')
                                        {
                                            $('#specific_price_sp_reduction').focus();
                                        }
                                        else
                                        {
                                            if($('.massedit-form-field.'+this.field_error+ ' input[type="text"]:not(:hidden)').length)
                                            {
                                                $('.massedit-form-field.'+this.field_error+ ' input[type="text"]:not(:hidden)').focus();
                                            }
                                            if($('.massedit-form-field.'+this.field_error+ ' textarea:not(:hidden)').length)
                                            {
                                                $('.massedit-form-field.'+this.field_error+ ' textarea:not(:hidden)').focus();
                                            }
                                            if($('.massedit-form-field.'+this.field_error+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').length)
                                            {
                                                var Idrte = $('.massedit-form-field.'+this.field_error+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').attr('id');
                                                tinyMCE.get(Idrte).execCommand('mceFocus', false);
                                            }
                                        }
                                    }
                                    
                                }
                                if(this.field=='specific_prices')
                                {
                                    if($('#specific_price_sp_reduction').val()!='')
                                        $('#specific_price_sp_reduction').addClass('field-error');
                                }
                                else
                                {
                                    if(this.id_lang)
                                    {
                                        if($('.massedit-form-field.'+this.field_error+ ' input[name="'+this.field_error+'_'+this.id_lang+'"]').val()!='')
                                            $('.massedit-form-field.'+this.field_error+ ' input[name="'+this.field_error+'_'+this.id_lang+'"]').addClass('field-error');
                                        if($('.massedit-form-field.'+this.field_error+ ' textarea[name="'+this.field_error+'_'+this.id_lang+'"]').val()!='')
                                            $('.massedit-form-field.'+this.field_error+ ' textarea[name="'+this.field_error+'_'+this.id_lang+'"]').addClass('field-error');
                                    }
                                    else
                                    {
                                        if($('.massedit-form-field.'+this.field_error+ ' input[name="'+this.field_error+'"]').val()!='')
                                            $('.massedit-form-field.'+this.field_error+ ' input[name="'+this.field_error+'"]').addClass('field-error');
                                        if($('.massedit-form-field.'+this.field_error+ ' textarea[name="'+this.field_error+'"]').val()!='')
                                            $('.massedit-form-field.'+this.field_error+ ' textarea[name="'+this.field_error+'"]').addClass('field-error');
                                    }
                                    
                                }
    						});
                        }
                        if(json.field_error)
                        {
                            
                            if(!$('label[for="'+json.field_error+'"]').parents('.ets_pmn_tab_content').hasClass('active'))
                            {
                                var current_tab = $('label[for="'+json.field_error+'"]').parents('.ets_pmn_tab_content').data('tab');
                                $('.ets_pmn_product_tab .ets_pmn_tab').removeClass('active');
                                $('.ets_pmn-form-content .ets_pmn_tab_content').removeClass('active');
                                $('.ets_pmn_product_tab .ets_pmn_tab[data-tab="'+current_tab+'"]').addClass('active');
                                $('.ets_pmn-form-content .ets_pmn_tab_content.'+current_tab).addClass('active');
                            }
                            
                            if(json.field=='specific_prices')
                            {
                                $('#specific_price_sp_reduction').focus();
                                if($('#specific_price_sp_reduction').val()!='')
                                    $('#specific_price_sp_reduction').addClass('field-error');
                            }
                            else
                            {
                                if($('.massedit-form-field.'+json.field_error+ ' input[type="text"]:not(:hidden)').length)
                                {
                                    $('.massedit-form-field.'+json.field_error+ ' input[type="text"]:not(:hidden)').focus();
                                    if($('.massedit-form-field.'+json.field_error+ ' input[type="text"]:not(:hidden)').val()!='')
                                        $('.massedit-form-field.'+json.field_error+ ' input[type="text"]:not(:hidden)').addClass('field-error');
                                }
                                if($('.massedit-form-field.'+json.field_error+ ' textarea:not(:hidden)').length)
                                {
                                    $('.massedit-form-field.'+json.field_error+ ' textarea:not(:hidden)').focus();
                                    if($('.massedit-form-field.'+json.field_error+ ' textarea:not(:hidden)').val()!='')
                                    {
                                        $('.massedit-form-field.'+json.field_error+ ' textarea:not(:hidden)').addClass('field-error');
                                    }
                                }
                                if($('.massedit-form-field.'+json.field_error+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').length)
                                {
                                    var Idrte = $('.massedit-form-field.'+json.field_error+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').attr('id');
                                    tinyMCE.get(Idrte).execCommand('mceFocus', false);
                                }
                            }
                            
                        }
                    }
                },
                error: function(xhr, status, error)
                { 
                    $('button[name="submitSaveMasseActionEdit"]').removeClass('loading');
                }
            });
        }
    });
    $(document).on('click','.btn-add-filter-product',function(e){
        e.preventDefault();
        $('button[name="btnSubmitMassedit"]').removeAttr('disabled');
        $('.btn-save-msssive-template-popup').removeAttr('disabled');
        $('#block-wait-filter-products').hide();
        $('#block-filter-products').show();
    });
    $(document).on('click','button[name="btnSubmitSaveNameMassedit"]',function(e){
        e.preventDefault();
        var $this = $(this);
        if(!$this.hasClass('loading'))
        {
            $this.addClass('loading');
            var formData = new FormData($this.parents('form').get(0));
            formData.append('submitSaveNameMassedit',1);
            $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        showSuccessMessage(json.success);
                        $('.list-massedit').html(json.massedits_list);
                        $('.ets_product_popup').removeClass('show');
                        $('.btn-delete-template').show();
                        $('input[name="id_ets_pmn_massedit"]').val(json.id_ets_pmn_massedit);
                    }
                    if(json.errors)
                    {
                         showErrorMessage(json.errors);
                    }
                },
                error: function(xhr, status, error)
                { 
                    $this.removeClass('loading');
                }
            });
        }
    });
    $(document).on('click','button[name="btnSubmitMassedit"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading') && $('.btn-add-filter-product:not(:hidden)').length==0 && $('#massedit_form #list-pmn_products .not_items_found').length==0)
        {
            if(!ets_pmn_checkFormFilter())
                return false;
            $(this).addClass('loading');
            var $this = $(this);
            $('.module_error').parent().remove();
            var formData = new FormData($('button[name="btnSubmitMassedit"]').parents('form').get(0));
            formData.append('btnSubmitMassedit',1);
            if($('.ets_popup-save-massage').hasClass('show'))
            {
                formData.append('saveMassedit',1);
            }
            xhrMasseditAjax = $.ajax({
                url: $('button[name="btnSubmitMassedit"]').parents('form').attr('action'),
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $('.ets_product_popup').removeClass('show');
                        if(json.html_form)
                        {
                            $('#id_ets_pmn_massedit').val(json.id_ets_pmn_massedit);
                            if(!$this.hasClass('btn-save-template'))
                            {
                                if($('#form_step2').length==0)
                                    $('#massedit_form').after('<div id="form_step2">'+json.html_form+'</div>');
                                else
                                    $('#form_step2').html(json.html_form);
                                $('#form_step2').show();
                                $('#massedit_form').hide();
                                $('.steps.nbr_steps_active_1').hide();
                            }
                            else
                            {
                                $('li.list-massedit').html(json.list_template_massedit);
                                $('.btn-delete-template').show();
                            }
                            if($('input.tagify').length>0)
                            {
                                $('input.tagify').tagify({delimiters: [13,44], addTagPrompt: 'Add tags'});
                            }
                            if($('.ets_pmn_autoload_rte').length)
                            {
                                tinySetup({
                        			editor_selector :"ets_pmn_autoload_rte",
                                    setup: function (ed) {
                            	        ed.on('change blur', function (ed) {
                            	            tinyMCE.triggerSave();
                                            ets_pmn_checkFormEditAction();
                            	        });
                            	    },
                        		});
                            }
                            if($('#form_step1_related_products').length)
                            {
                                $('#form_step1_related_products').autocomplete(ets_mp_url_search_related_product,{
                            		minChars: 1,
                            		autoFill: true,
                            		max:20,
                            		matchContains: true,
                            		mustMatch:false,
                            		scroll:false,
                            		cacheLength:0,
                            		formatItem: function(item) {
                            			return (item[4] ? '<img src="'+item[4]+'" style="width:24px;"/>':'') +' - '+item[2]+(item[3] ? ' (REF: '+item[3]+')':'');
                            		}
                            	}).result(etsPMNAddProductRelated);
                            }
                            if ($(".datepicker input").length > 0) {
                                var dateToday = new Date();
                                $(".datepicker input").removeClass('hasDatepicker');
                        		$(".datepicker input").datepicker({
                        			dateFormat: 'yy-mm-dd',
                                    timeFormat: 'hh:mm:ss',
                                    minDate: dateToday,
                        		});
                        	}
                            if($('#specific_price_id_customer').length)
                            {
                                $('#specific_price_id_customer').autocomplete(link_search_customer,{
                                		minChars: 1,
                                		autoFill: true,
                                		max:20,
                                		matchContains: true,
                                		mustMatch:false,
                                		scroll:false,
                                		cacheLength:0,
                                		formatItem: function(item) {
                                			return item[1]+' ('+item[2]+')';
                                		}
                           	    }).result(etsPMNAddCustomerSpecific); 
                            }
                            $('.btn-save-msssive-template-popup').removeAttr('disabled');
                            ets_pmn_checkFormEditAction();
                            if($('input.condition-action').length)
                            {
                                $('input.condition-action').each(function(){
                                    if($(this).val()=='off') {
                                        $(this).parents('.form-group').removeClass('status_active');
                                    } else {
                                        $(this).parents('.form-group').addClass('status_active');
                                    }
                                });
                            }
                            if($('.js-attribute-checkbox:checked').length) {
                                $('.js-attribute-checkbox:checked').each(function(){
                                    $('#attributes-generator .tokenfield').addClass('has_attribute').append('<div class="token" data-value="' + $(this).data('value') + '"><span class="token-label" style="max-width: 713.184px;">' + $(this).data('label') + '</span><a href="#" class="ets_pmn_close_attribute" tabindex="-1"></a></div>');
                                });
                            }
                        }
                        else    
                            window.location.href = json.url_reload;
                    }
                    if(json.errors)
                    {
                         if($('.ets_popup-save-massage').hasClass('show'))
                            $('.ets_popup-save-massage .form-wrapper').after(json.errors);
                         else
                            $('#massedit_form').before(json.errors);
                    }
                },
                error: function(xhr, status, error)
                { 
                    $this.removeClass('loading');
                }
            });
        }  
    });
    if($('.ets_pmn_autoload_rte').length)
    {
        tinySetup({
			editor_selector :"ets_pmn_autoload_rte",
            setup: function (ed) {
    	        ed.on('keyup change blur', function (ed) {
    	            tinyMCE.triggerSave();
    	        });
    	    },
		});
    }
    $(document).on('click','.exclued_massive_id_products',function(){
       if($(this).is(':checked'))
       {
            $('#product_excluded').val($('#product_excluded').val()+' '+$(this).val()+',');
            $(this).attr('title',Click_to_select_text);
       }
       else
       {
            $('#product_excluded').val($('#product_excluded').val().replace(' '+$(this).val()+',',''));
            $(this).attr('title',Click_to_unselect_text);
       }
    });
    $(document).on('change','select[name="paginator_matching_products_select_limit"]',function(){
        $('.list_massedit_products').addClass('loading');
        if(xhrMasseditAjax)
            xhrMasseditAjax.abort();
        var formData = new FormData($('button[name="btnSubmitMassedit"]').parents('form').get(0));
        formData.append('submitSearchProductEdit',1);
        formData.append('stepNumber',$('.steps .active .stepNumber').text());
        formData.append('paginator_matching_products_select_limit',$(this).val());
        formData.append('getProducts',1);
        formData.append('page',1);
        xhrMasseditAjax = $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('.list_massedit_products').removeClass('loading');
                if(json.error)
                    showErrorMessage(json.error);
                if(json.product_list)
                {
                    $('.list_massedit_products').html(json.product_list);
                }
            },
            error: function(xhr, status, error)
            {
                $('.list_massedit_products').removeClass('loading');
            }
        });
    });
    $(document).on('click', '.list_massedit_products .ets_pmn_paggination a',function(e){
        e.preventDefault();
        if(!$('.list_massedit_products').hasClass('loading'))
        {
            $('.list_massedit_products').addClass('loading');
            if(xhrMasseditAjax)
                xhrMasseditAjax.abort();
            var formData = new FormData($('button[name="btnSubmitMassedit"]').parents('form').get(0));
            formData.append('submitSearchProductEdit',1);
            formData.append('stepNumber',$('.steps .active .stepNumber').text());
            var url_ajax = $(this).attr('href');
            formData.append('paginator_matching_products_select_limit',$('select[name="paginator_matching_products_select_limit"]').val());
            xhrMasseditAjax = $.ajax({
                url: url_ajax,
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $('.list_massedit_products').removeClass('loading');
                    if(json.error)
                        showErrorMessage(json.error);
                    if(json.product_list)
                    {
                        $('.list_massedit_products').html(json.product_list);
                    }
                },
                error: function(xhr, status, error)
                { 
                    $('.list_massedit_products').removeClass('loading');
                }
            });
        }
        
    });
    $(document).on('change','#massedit_form .row_massedit .condition_field,input[name="type_combine_condition"]',function(){
        ets_pmn_displayFormMassedit($(this).parents('.row_massedit'),$(this).val());
        $(this).find('.condition_operator').val('');
        ets_pmn_AjaxFilterProductEdit();
    });
    $(document).on('change','.operator_value_text_lang,.operator_value_text input[type="text"],select.condition_operator',function(){
        ets_pmn_AjaxFilterProductEdit();        
    });
    $('#massedit_form').on('keyup', function(e) {
          var keyCode = e.keyCode || e.which;
          if (keyCode === 13) { 
                e.preventDefault();    
                $('.operator_value_text input[type="text"]').blur();           
                ets_pmn_AjaxFilterProductEdit();
                return false;
          }
    });
    $('#massedit_form').on('keypress', function(e) {
          var keyCode = e.keyCode || e.which;
          if (keyCode === 13) { 
                e.preventDefault();
                return false;
          }
    });
    if($('#massedit_form .row_massedit .condition_field').length)
    {
        $('#massedit_form .row_massedit .condition_field').each(function(){
            ets_pmn_displayFormMassedit($(this).parents('.row_massedit'),$(this).val());
        });
    }
    $(document).on('change','.form_operator input[type="checkbox"]',function(){
        text_value ='';
        if($(this).parents('.form_operator').find('input[type="checkbox"]:checked').length)
        {
            $(this).parents('.form_operator').find('input[type="checkbox"]:checked').each(function(){
                text_value += $(this).val()+',';
            });
        }
        $(this).parents('.form_operator').find('.input_operator_value').val(text_value);
        ets_pmn_AjaxFilterProductEdit();
    });
    $(document).on('change','select.operator_value',function(){
        $(this).prev('.input_operator_value').val($(this).val());        
        ets_pmn_AjaxFilterProductEdit();
    });
    $(document).on('click','.btn-add-filter',function(){
       $(this).prev().prev('.ets_list_form_massedit').append($('.form-add-filter').html()); 
       ets_pmn_displayFormMassedit($('#massedit_form .row_massedit:last'),1);
    });
    $(document).on('click','.btn-delete-filter',function(){
        if($('.ets_list_form_massedit .row_massedit').length==1)
        {
            $('button[name="btnSubmitMassedit"]').attr('disabled','disabled');
            $('.btn-save-msssive-template-popup').attr('disabled','disabled');
            $('#block-wait-filter-products').show();
            $('#block-filter-products').hide();
            $('#massedit_form .row_massedit:last .condition_field').val("1");
            $('#massedit_form .row_massedit:last .condition_field').change();
            $('#massedit_form .row_massedit:last .condition_operator').val("has_words");
            $('#massedit_form .row_massedit:last .condition_operator').change();
            $('.operator_value_text').val('');
            $('.list_massedit_products').removeClass('load_product');
            if(xhrMasseditAjax)
                xhrMasseditAjax.abort();
        }
        else
        {
            $(this).parents('.row_massedit').remove();          
            ets_pmn_AjaxFilterProductEdit();
        }
    });
    $(document).on('click','#ets_pmn_product_form .category-tree .category',function(){
        var id_category = $(this).val();
        if($(this).is(':checked'))
        {
            $(this).parents('.form-wrapper-edit-category').find('.ps_categoryTags').show().append('<span class="pstaggerTag"><span data-id="'+id_category+'" title="'+$(this).next('.label').html()+'"> '+$(this).next('.label').html()+'</span><a class="pstaggerClosingCross" href="#" data-id="'+id_category+'">x</a></span>')
        }
        else
        {
            $(this).parents('.form-wrapper-edit-category').find('.pstaggerTag span[data-id="'+id_category+'"]').parent().remove();
            if ( $('#ps_categoryTags .pstaggerTag').length <= 0 ){
                $('.ps_categoryTags').hide();
            }
        }
    });
    $(document).on('click','.pstaggerClosingCross',function(e){
        e.preventDefault();
        if($(this).parents('.form-wrapper-edit-category').find('.pstaggerClosingCross').length)
        {
            var id_category = $(this).data('id');
            $(this).parents('.form-wrapper-edit-category').find('.category[value="'+id_category+'"]').prop('checked', false).change(); 
            $(this).parent().remove();
            if ( $('#ps_categoryTags .pstaggerTag').length <= 0 ){
                $('.ps_categoryTags').hide();
            }
        }
    });
    $(document).on('click','.categories-tree-reduce',function(){
        $(this).hide();
        $(this).parents('.form-wrapper-edit-category').find('.categories-tree-expand').show();
        if($(this).parents('.form-wrapper-edit-category').find('.category-tree .category').length)
        {
            $(this).parents('.form-wrapper-edit-category').find('.category-tree .category').each(function(){
                if($(this).parent().parent().next('.children').length)
                {
                    if($(this).parent().parent().next('.children').length)
                    {
                        $(this).parent().parent().next('.children').hide();
                        $(this).parent().removeClass('opend');
                    }   
                }
            });
        }
    });
    $(document).on('click','.categories-tree-expand',function(){
        $(this).hide();
        $(this).parents('.form-wrapper-edit-category').find('.categories-tree-reduce').show();
        if($(this).parents('.form-wrapper-edit-category').find('.category-tree .category').length)
        {
            $(this).parents('.form-wrapper-edit-category').find('.category-tree .category').each(function(){
                if($(this).parent().parent().next('.children').length)
                {
                    if($(this).parent().parent().next('.children').length)
                    {
                        $(this).parent().parent().next('.children').show();
                        $(this).parent().addClass('opend');
                    }   
                }
            });
        }
    });
    $(document).on('click','.ets_pmn_product_tab .ets_pmn_tab',function(){
        $('.ets_pmn_product_tab .ets_pmn_tab').removeClass('active');
        $('.ets_pmn-form-content .ets_pmn_tab_content').removeClass('active');
        $(this).addClass('active');
        $('.ets_pmn-form-content .ets_pmn_tab_content.'+$(this).data('tab')).addClass('active');
        ets_pmn_checkFormEditAction();
    });
    $(document).on('click','#ets_pmn_add_feature_button',function(){
       $('#ets-pmn-features-content').append($('#ets-pmn-feature-add-content').html()); 
    });
    $(document).on('click','.ets-pmn-delete',function(){
       if(confirm(delete_feature_comfirm))
       {
            $(this).closest('.etm-pmn-product-feature').remove();
            ets_pmn_checkFormEditAction();            
       } 
    });
    $(document).on('change','.id_features',function(){
        var id_feature = $(this).val();
        $(this).closest('.etm-pmn-product-feature').find('.id_feature_value').hide();
        $(this).closest('.etm-pmn-product-feature').find('.id_feature_value').removeAttr('selected');
        $(this).closest('.etm-pmn-product-feature').find('.id_feature_value[value="0"]').attr('selected','selected');
        $(this).closest('.etm-pmn-product-feature').find('.id_feature_values').removeAttr('disabled');
        if($(this).closest('.etm-pmn-product-feature').find('.id_feature_value[data-id-feature="'+id_feature+'"]').length)
        {
            $(this).closest('.etm-pmn-product-feature').find('.id_feature_value[data-id-feature="'+id_feature+'"]').show();
        }
        else
        {
            $(this).closest('.etm-pmn-product-feature').find('.id_feature_values').attr('disabled','disabled');
        }
    });
    $(document).on('click','#add-related-product-button',function(){
        $(this).hide();
        $('#related-content').removeClass('hide');
    });
    if($('#form_step1_related_products').length)
    {
        $('#form_step1_related_products').autocomplete(ets_mp_url_search_related_product,{
    		minChars: 1,
    		autoFill: true,
    		max:20,
    		matchContains: true,
    		mustMatch:false,
    		scroll:false,
    		cacheLength:0,
    		formatItem: function(item) {
    			return (item[4] ? '<img src="'+item[4]+'" style="width:24px;"/>':'') +' - '+item[2]+(item[3] ? ' (REF: '+item[3]+')':'');
    		}
    	}).result(etsPMNAddProductRelated);
    }
    $('#custom_fields a.add').on('click', function(e) {
        e.preventDefault();
        var collectionHolder = $('ul.customFieldCollection');
        var maxCollectionChildren = collectionHolder.children().length;
        var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, maxCollectionChildren);
        collectionHolder.append('<li>' + newForm + '</li>');
    });
    $(document).on('click','.hideOtherLanguage',function(){
        $('.translatable-field').hide();
        $('.translatable-field.lang-'+$(this).data('id-lang')).css('display', ''); 
    });
    if($('input.tagify').length>0)
    {
        $('input.tagify').tagify({delimiters: [13,44], addTagPrompt: 'Add tags'});
    }
    $(document).on('click','input.condition-action',function(){
        if($(this).val()=='off' || $(this).val()=='remove_all') {
            $('.massedit-form-field.' + $(this).data('field')).hide();
        } else {
            $('.massedit-form-field.' + $(this).data('field')).show();
        }
        if($(this).val()=='off') {
            $(this).parents('.form-group').removeClass('status_active');
        } else {
            $(this).parents('.form-group').addClass('status_active');
        }
        if($('.massedit-form-field.'+$(this).data('field')+' .suffix').length)
        {
            if($(this).val()=='plus_percent' || $(this).val()=='minus_percent')
            {
                $('.massedit-form-field.'+$(this).data('field')+' .suffix').html('%');
            }
            else
            {
                $('.massedit-form-field.'+$(this).data('field')+' .suffix').html($('.massedit-form-field.'+$(this).data('field')+' .suffix').data('suffix'));
            }
        }
        if($('.massedit-form-field.'+$(this).data('field')+' select option[value=""]').length)
        {
            if($(this).val()=='remove')
            {
                $('.massedit-form-field.'+$(this).data('field')+' select option[value=""]').hide();
                if($('.massedit-form-field.'+$(this).data('field')+' select').val()=='')
                {
                    $('.massedit-form-field.'+$(this).data('field')+' select option').removeAttr('selected');
                    $('.massedit-form-field.'+$(this).data('field')+' select option[value=""]').next().attr('selected','selected');
                    $('.massedit-form-field.'+$(this).data('field')+' select').change();
                }
            }
            else
            {
                $('.massedit-form-field.'+$(this).data('field')+' select option[value=""]').hide();
            }
        }
        if($(this).data('field')!='specific_prices')
        {
            if($('.massedit-form-field.'+$(this).data('field')+ ' input[type="text"]:not(:hidden)').length)
                $('.massedit-form-field.'+$(this).data('field')+ ' input[type="text"]:not(:hidden)').focus();
            if($('.massedit-form-field.'+$(this).data('field')+ ' textarea:not(:hidden)').length)
                    $('.massedit-form-field.'+$(this).data('field')+ ' textarea:not(:hidden)').focus();
            if($('.massedit-form-field.'+$(this).data('field')+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').length)
            {
                var Idrte = $('.massedit-form-field.'+$(this).data('field')+ ' .translatable-field:not(:hidden) textarea.ets_pmn_autoload_rte').attr('id');
                tinyMCE.get(Idrte).execCommand('mceFocus', false);
            }
        }
        else
        {
            $('#specific_price_sp_reduction').focus();
        }
        ets_pmn_checkFormEditAction();
    });
    $(document).on('click','.js-ets-pmn-add-short-code',function(e){
         e.preventDefault();
         var meta_code = $(this).attr('data-code');
         if (meta_code) {
            tinymce.triggerSave();
            var meta_box = $(this).parent('.ets_pmn_short_code');
            ets_pmn_insertAtCaret(meta_box.parent().find('input, textarea'), meta_code);    
         }
    });
});
var etsPMNAddProductRelated = function(event, data, formatted)
{
	if (data == null)
		return false;
	var id_product = data[0];
	var id_product_attribute = data[1];
    var name_product = data[2];
    var reference_product= data[3];
    var image_product= data[4];
    $('#form_step1_related_products').val('');
    var $html = '<li class="media">';
        $html +=' <div class="media-left">'+(image_product ? '<img class="media-object image" src="'+image_product+'" />':'')+' </div>';
        $html +='<div class="media-body media-middle">';
            $html +='<span class="label">'+name_product+(reference_product ? ' (ref: '+reference_product+')':'')+'</span>';
            $html +='<i class="fa fa-times delete delete_related"></i>';
        $html +='</div>';
        $html +='<input name="related_products[]" value="'+id_product+'" type="hidden">';
    $html +='</li>';
    $('#form_step1_related_products-data').append($html);
    ets_pmn_checkFormEditAction();
};
function ets_pmn_AjaxFilterProductEdit()
{  
    if(!ets_pmn_checkAjaxSelect())
    {
        var $html = '<div class="panel ets_mp-panel">';
        $html += '<div class="panel-heading">';
        $html += Matching_products_text;                            
        $html +='<span class="panel-heading-action"> </span>';
        $html +='</div>';
        $html +='<div class="alert alert-warning">'+No_product_available_text+'</div>'
        $html +='</div>';
        $('.list_massedit_products').html($html);
        return false;
    }
    $('.massedit_operator_value.error').removeClass('error');
    if(xhrMasseditAjax)
        xhrMasseditAjax.abort();
    var formData = new FormData($('button[name="btnSubmitMassedit"]').parents('form').get(0));
    formData.append('submitSearchProductEdit',1);
    $('.list_massedit_products').addClass('load_product');
    xhrMasseditAjax = $.ajax({
        url: $('button[name="btnSubmitMassedit"]').parents('form').attr('action'),
        data: formData,
        type: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(json){
            $('.list_massedit_products').removeClass('load_product');
            if(json.error)
                showErrorMessage(json.error);
            if(json.product_list)
            {
                $('.list_massedit_products').html(json.product_list).show();
                if($('#list-pmn_products .not_items_found').length>0)
                {
                    $('button[name="btnSubmitMassedit"]').attr('disabled','disabled');
                    $('.btn-save-msssive-template-popup').attr('disabled','disabled');
                }    
                else
                {
                    $('button[name="btnSubmitMassedit"]').removeAttr('disabled');
                    $('.btn-save-msssive-template-popup').removeAttr('disabled');
                }
            }
        },
        error: function(xhr, status, error)
        {

        }
    });
}
function ets_pmn_displayFormMassedit($row,condition_field)
{
    if(condition_field==FILTERED_FIELD_ALL)
    {
        $row.find('.col.massedit_operator').hide();
        $row.find('.col.massedit_operator_value').hide();
        $row.addClass('all');
        $('#massedit_form .row_massedit:not(.all)').remove();
        $('.btn-add-filter').hide();
    }
    else
    {
        $row.removeClass('all');
        $('.btn-add-filter').show();
        $row.find('.col.massedit_operator_value').show();
        $row.find('.col.massedit_operator').hide();
        $row.find('.col.massedit_operator .operator').hide();
        $row.find('.operator_value_text').hide();
        $row.find('.operator_value_text_lang').hide();
        $row.find('.operator_value_attribute').hide();
        $row.find('.operator_value_brand').hide();
        $row.find('.operator_value_supplier').hide();
        $row.find('.operator_value_features').hide();
        $row.find('.operator_value_categories').hide();
        $row.find('.operator_value_color').hide();
        if(condition_field==FILTERED_FIELD_NAME || condition_field==FILTERED_FIELD_DESCRIPTION || condition_field==FILTERED_FIELD_SUMMARY)
        {
            $row.find('.col.massedit_operator').show();
            $row.find('.col.massedit_operator .operator.text').show();
            if($row.find('select.condition_operator option[selected="selected"]').length==0 || !$row.find('select.condition_operator option[selected="selected"]').hasClass('text'))
            {
                $row.find('select.condition_operator option').removeAttr('selected');
                $row.find('select.condition_operator option[value="has_words"]').attr('selected','selected');
                $row.find('select.condition_operator').val('has_words');
                $row.find('select.condition_operator').change();
            }
            $row.find('.operator_value_text').show();
            $row.find('.operator_value_text_lang').show();
            
        }
        if(condition_field==FILTERED_FIELD_REFERENCE)
        {
            $row.find('.col.massedit_operator').show();
            $row.find('.col.massedit_operator .operator.text').show();
            if($row.find('select.condition_operator option[selected="selected"]').length==0 || !$row.find('select.condition_operator option[selected="selected"]').hasClass('text'))
            {
                $row.find('select.condition_operator option').removeAttr('selected');
                $row.find('select.condition_operator option[value="has_words"]').attr('selected','selected');
                $row.find('select.condition_operator').val('has_words');
                $row.find('select.condition_operator').change();
            }
            $row.find('.operator_value_text').show();
        }
        if(condition_field==FILTERED_FIELD_QUANTITY || condition_field==FILTERED_FIELD_ID_PRODUCT || condition_field==FILTERED_FIELD_PRICE)
        {
            $row.find('.col.massedit_operator').show();
            $row.find('.col.massedit_operator .operator.number').show();
            if(!$row.find('select.condition_operator option[selected="selected"]').hasClass('number') || $row.find('select.condition_operator option[selected="selected"]').length==0)
            {
                $row.find('select.condition_operator option').removeAttr('selected');
                $row.find('select.condition_operator option[value="equal_to"]').attr('selected','selected');
                $row.find('select.condition_operator').val('equal_to');
                $row.find('select.condition_operator').change();
            }
            $row.find('.operator_value_text').show();
        }
        if(condition_field==FILTERED_FIELD_CATEGORIES || condition_field==FILTERED_FIELD_SUPPLIER)
        {
            $row.find('.col.massedit_operator').show();
            $row.find('.col.massedit_operator .operator.default').show();
            if(!$row.find('select.condition_operator option[selected="selected"]').hasClass('default') || $row.find('select.condition_operator option[selected="selected"]').length==0)
            {
                $row.find('select.condition_operator option').removeAttr('selected');
                $row.find('select.condition_operator option[value="only_default"]').attr('selected','selected');
                $row.find('select.condition_operator').val('only_default');
                $row.find('select.condition_operator').change();
            }
        }
        if(condition_field== FILTERED_FIELD_ATTRIBUTE || condition_field== FILTERED_FIELD_FEATURES || condition_field==FILTERED_FIELD_BRAND || condition_field==FILTERED_FIELD_COLOR)
        {
            $row.find('.col.massedit_operator').show();
            $row.find('.col.massedit_operator .operator.in').show();
            if(!$row.find('select.condition_operator option[selected="selected"]').hasClass('in') || $row.find('select.condition_operator option[selected="selected"]').length==0)
            {
                $row.find('select.condition_operator option').removeAttr('selected');
                $row.find('select.condition_operator option[value="in"]').attr('selected','selected');
                $row.find('select.condition_operator').val('in');
                $row.find('select.condition_operator').change();
            }
        }
        if(condition_field==FILTERED_FIELD_ATTRIBUTE)
        {
            $row.find('.operator_value_attribute').show();
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_attribute_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_attribute_text);
        }
        if(condition_field==FILTERED_FIELD_FEATURES)
        {
            $row.find('.operator_value_features').show();
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_features_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_features_text);
        }
        if(condition_field==FILTERED_FIELD_BRAND)
        {
            $row.find('.operator_value_brand').show();
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_brand_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_brand_text);
        }
        if(condition_field==FILTERED_FIELD_SUPPLIER)
        {
            $row.find('.operator_value_supplier').show();
            $row.find('.col.massedit_operator .operator.default[value="only_default"]').text(Default_supplier_text);
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_supplier_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_supplier_text);
        }
        if(condition_field==FILTERED_FIELD_CATEGORIES)
        {
            $row.find('.operator_value_categories').show();
            $row.find('.col.massedit_operator .operator.default[value="only_default"]').text(Default_category_text);
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_category_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_category_text);
        }
        if(condition_field==FILTERED_FIELD_COLOR)
        {
            $row.find('.operator_value_color').show();
            $row.find('.col.massedit_operator .operator.default[value="in"]').text(Has_color_text);
            $row.find('.col.massedit_operator .operator.default[value="not_in"]').text(Not_has_color_text);
        }
    }
}
function ets_pmn_insertAtCaret(element,data)
{
    var areaId = element.attr('id');
    if($('#'+areaId).hasClass('ets_pmn_autoload_rte'))
    {
        tinyMCE.get(areaId).execCommand('mceInsertContent', false, data);
        return true;
    }
    var text = data;
    var txtarea = element[0];
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
        "ff" : (document.selection ? "ie" : false));
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") strPos = txtarea.selectionStart;

    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
    ets_pmn_checkFormEditAction();
}
function ets_pmn_checkAjaxSelect()
{
    var checked = false;
    if($('.condition_field').length)
    {
        
        $('.condition_field').each(function(){
            if($(this).val()=='0')
            {
                checked = true;
                return true;
            } 
        });
        if(checked)
            return true;
    }
    if($('.massedit_operator_value').length)
    {
        $('.massedit_operator_value').each(function(){
            if((input_texts = $(this).find('.operator_value_text:not(:hidden) input[type="text"]')).length && input_texts.val()!='')
            {
               checked = true; 
               return true;
            }
            if($(this).find('.form_operator:not(:hidden) input[type="checkbox"]').length && $(this).find('.form_operator:not(:hidden) input[type="checkbox"]:checked').length)
            {
               checked = true;
               return true; 
            }
            if((selects = $(this).find('.form_operator:not(:hidden) select')).length && selects.val()!='' && selects.val()!=null)
            {
                checked = true;
               return true; 
            }
        });
        return checked;
    }
    return false;
}
function ets_pmn_checkFormFilter()
{
    var error = false;
    if($('.massedit_operator_value').length)
    {
        $('.massedit_operator_value').each(function(){
            if($(this).find('.operator_value_text:not(:hidden) input[type="text"]').val()=='')
            {
               $(this).addClass('error');
               error = true; 
            }
            if($(this).find('.form_operator:not(:hidden) input[type="hidden"]').val()=='')
            {
               $(this).addClass('error');
               error = true; 
            }
        });
    }
    if(error)
    {
        $('.massedit_operator_value.error:first input[type="text"]').focus();
        showErrorMessage(text_all_filter_required);
        return false;
    }
    else
        return true;
}
function ets_pmn_checkFormEditAction()
{
    if($('.ets_pmn_tab').length)
    {
        $('.ets_pmn_tab').each(function(){
            var tab = $(this).data('tab');
            if($('.ets_pmn_tab_content.'+tab+' input.condition-action:checked').length)
            {
                var tab_edit = false;
                $('.ets_pmn_tab_content.'+tab+' input.condition-action:checked').each(function(){
                    var $field  = $(this).data('field');
                    if($(this).val()!='off')
                    {
                        if(!tab_edit)
                            tab_edit = 'edited';
                        var field_edited = false;
                        if($('.massedit-form-field.'+$field+ ' input[name="'+$field+'"]').length)
                        {
                            $('.massedit-form-field.'+$field+ ' input[name="'+$field+'"]').each(function(){
                                if($(this).val()!='')
                                {
                                    field_edited = true;
                                    return '';
                                } 
                            });
                        }
                        if($('.massedit-form-field.'+$field+ ' select[name="'+$field+'"]').length)
                        {
                            $('.massedit-form-field.'+$field+ ' select[name="'+$field+'"]').each(function(){
                                if($(this).val()!='')
                                {
                                    field_edited = true;
                                    return '';
                                } 
                            });
                        }
                        if(!field_edited &&  $('.massedit-form-field.'+$field+ ' input[name^="'+$field+'_"]').length)
                        {
                            $('.massedit-form-field.'+$field+ ' input[name^="'+$field+'_"]').each(function(){
                                if($(this).val()!='')
                                {
                                    field_edited = true;
                                    return '';
                                }
                            });
                        }
                        if(!field_edited && $('.massedit-form-field.'+$field+ ' textarea[name="'+$field+'"]').length)
                        {
                            $('.massedit-form-field.'+$field+ ' textarea[name="'+$field+'"]').each(function(){
                                if($(this).val()!='')
                                {
                                    field_edited = true;
                                    return '';
                                }
                            });
                        }
                        if(!field_edited && $('.massedit-form-field.'+$field+ ' textarea[name^="'+$field+'_"]').length)
                        {
                            
                            $('.massedit-form-field.'+$field+ ' textarea[name^="'+$field+'_"]').each(function(){
                                if($(this).val()!='')
                                {
                                    field_edited = true;
                                    return '';
                                }
                            });
                        }
                        if($field=='id_categories' || $field=='selectedCarriers' || $field=='combinations')
                        {
                            if($('.massedit-form-field.'+$field+ ' input[type="checkbox"]:checked').length>0)
                            {
                                field_edited = true;
                            }
                        }
                        if($field=='id_category_default' || $field=='additional_delivery_times')
                        {
                            if($('.massedit-form-field.'+$field+ ' input[type="radio"]:checked').length>0)
                            {
                                
                                field_edited = true;
                            }
                            else
                                field_edited = false;
                        }
                        if($field=='features')
                        {
                            if($('#ets-pmn-features-content .etm-pmn-product-feature').length)
                            {
                                $('#ets-pmn-features-content .etm-pmn-product-feature').each(function(){
                                    if($(this).find('.id_features').val()!='0')
                                    {
                                        if($(this).find('.id_feature_values').val()!='0' || $(this).find('.feature_value_custom').val()!='')
                                        {
                                            field_edited = true;
                                            return '';
                                                                                    
                                        }                                        
                                                                            
                                    }                                    
                                  ;                                  
                                })
                                                            
                            }                                                   
                        }
                        if($field=='related_products')
                        {
                            if($('#form_step1_related_products-data li').length >0)
                                field_edited = true;
                        }
                        if($field=='specific_prices')
                        {
                            if($('#specific_price_sp_reduction').val()!='')
                                field_edited = true;
                        }                                                
                        if($field=='active' || $field=='low_stock_alert' || $field =='on_sale' || $field=='available_for_order' || $field=='online_only' || $field=='show_condition')
                            field_edited = true;
                        if($field=='customization')
                        {
                            if($('.massedit-form-field.customization input[type="text"]').length)
                            {
                                $('.massedit-form-field.customization input[type="text"]').each(function(){
                                    if($(this).val()!='')
                                    {
                                        field_edited = true;
                                        return '';
                                    }
                                });
                            }
                        }
                        if($(this).val()=='remove_all')
                            field_edited = true;
                        if(!field_edited)
                        {
                            if($('label[for="'+$field+'"]').length)
                                $('label[for="'+$field+'"]').addClass('editing').removeClass('edited');
                            else
                                $('label[for^="'+$field+'"]').addClass('editing').removeClass('edited');
                            tab_edit ='editing';
                        }
                        else
                        {
                            if($('label[for="'+$field+'"]').length)
                                $('label[for="'+$field+'"]').addClass('edited').removeClass('editing');
                            else
                                $('label[for^="'+$field+'"]').addClass('edited').removeClass('editing');
                        }
                    }
                    else
                    {
                        if($('label[for="'+$field+'"]').length)
                            $('label[for="'+$field+'"]').removeClass('edited').removeClass('editing');
                        else
                            $('label[for^="'+$field+'"]').removeClass('edited').removeClass('editing');
                    }
                });
                if(tab_edit)
                {
                    if(tab_edit=='editing')
                    {
                        if($('.ets_pmn_tab[data-tab="'+tab+'"] .icon').length)
                        {
                            $('.ets_pmn_tab[data-tab="'+tab+'"] .icon').removeClass('edited').addClass('editing');
                        }
                        else
                        {
                            $('.ets_pmn_tab[data-tab="'+tab+'"]').append('<i class="icon editing"></i>');
                        }
                    }
                    else
                    {
                        if($('.ets_pmn_tab[data-tab="'+tab+'"] .icon').length)
                        {
                            $('.ets_pmn_tab[data-tab="'+tab+'"] .icon').removeClass('editing').addClass('edited');
                        }
                        else
                        {
                            $('.ets_pmn_tab[data-tab="'+tab+'"]').append('<i class="icon edited"></i>');
                        }
                    }
                }
                else
                {
                    if($('.ets_pmn_tab[data-tab="'+tab+'"] .icon').length)
                        $('.ets_pmn_tab[data-tab="'+tab+'"] .icon').remove();
                }
            }
        });
    }
}
function ets_pmn_submitSaveMasseEditProduct(id_ets_massedit_history,total_products,current_page)
{
    var formData = new FormData($('button[name="submitSaveMasseEditProduct"]').parents('form').get(0));
    formData.append('submitSaveMasseEditProduct',1);
    formData.append('total_products',total_products);
    formData.append('id_ets_massedit_history',id_ets_massedit_history);
    formData.append('current_page',current_page);
    xhrMasseditAjax = $.ajax({
        url: '',
        data: formData,
        type: 'post',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(json){ 
            if(json.edit_continue)
            {
                ets_pmn_submitSaveMasseEditProduct(json.id_ets_massedit_history,json.total_products,json.current_page);
            }
            else
            {
                $('#preview_massedit_form').hide();
                $('.complete_massedit_form').addClass('loading').show();
                $('.nbr_steps_4 .step3').removeClass('active');
                $('.nbr_steps_4 .step3 > a').removeClass('active');
                $('.nbr_steps_4 .step4').removeClass('disabled').addClass('active').addClass('selected').parents('.steps.nbr_steps_4').addClass('nbr_steps_success');
                $('.nbr_steps_4 .step4 >a').removeClass('disabled').addClass('active').addClass('selected');
                $('.complete_massedit_form').removeClass('loading');
                $('.complete_massedit_form .alert-info').remove();
                $('button[name="submitSaveMasseEditProduct"]').removeClass('loading');
    
                if(json.success)
                {
                    $('.complete_massedit_form').before(json.success);
                    $('.complete_massedit_form .complete').html(json.product_list);
                    $('.btn-view-log').show();
                }
                if(json.errors)
                {
                    $('.complete_massedit_form .complete').html(json.errors);
                }
                $('.btn-save-msssive-template-popup').removeAttr('disabled');
    
                if ( $('.ets_td').length > 0 ){
                    $('.ets_td .popup_change_product, .ets_td .span_change_product').each(function(){
                        var content_height = $(this).find('.content_info').height();
                        if ( content_height > 60 ){
                            $(this).addClass('more');
                        }
                    });
                }
            }
            
        },
        error: function(xhr, status, error)
        { 
            $('button[name="submitSaveMasseEditProduct"]').removeClass('loading');
        }
    });
}
