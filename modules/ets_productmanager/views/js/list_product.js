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
 var xhrAjax = null;
 var time_run_ajax = 500;
 var run_ajax = null;
 function checkShowReset()
 {
    var ok= false;
    if($('.with-filters input[name*="filter_column_"]').length)
    {
        $('.with-filters input[name*="filter_column_"]').each(function(){
            if($(this).val()!='')
            {
                ok = true;
            }
        })
    }
    if(!ok && $('.with-filters select[name*="filter_column_"]').length)
    {
        $('.with-filters select[name*="filter_column_"]').each(function(){
            if($(this).val()!='')
            {
                ok = true;
            }
        });
    }
    if(!ok)
    {
        setTimeout(function(){
            $('button[name="products_filter_submit"]').attr('disabled','disabled');
            $('button[name="products_filter_reset"]').hide();
        },500);

    }
}
 function runAjaxFilterProduct(url,formData)
 {
    if(xhrAjax)
        xhrAjax.abort();
    $('.tbody_list_product').parents('#product_catalog_list').addClass('loading');
    if ( $('#ets_warningGradientOuterBarG').length <= 0 ){
        $('.tbody_list_product').parents('#product_catalog_list.loading').find('.table-responsive').append('<div id="ets_warningGradientOuterBarG"><div id="ets_warningGradientFrontBarG" class="ets_warningGradientAnimationG"><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div></div></div>');
    }
    var numthItems = $('.tbody_list_product > tr').length;
    if ( numthItems < 5){
        $('#product_catalog_list.loading').addClass('small_space_top');
    }
    xhrAjax = $.ajax({
        url: url,
        data: formData,
        type: 'post',
        dataType: 'html',
        processData: false,
        contentType: false,
        success: function(html){
            if(html)
            {
               var begin =  html.indexOf('<script id="begin_tbody_list_product"></script>')+47;
               var end =  html.indexOf('<script id="end_tbody_list_product"></script>');
               if(begin >0 && end >0)
               {
                    var list_products = html.substr(begin,end-begin);
                   $('.tbody_list_product').replaceWith(list_products);
                   var begin_pagination =  html.indexOf('<script id="begin_pagination_list_product"></script>')+52;
                   var end_pagination =  html.indexOf('<script id="end_pagination_list_product"></script>');
                   var list_pagination = html.substr(begin_pagination,end_pagination-begin_pagination);
                   $('.pagination_list_product').replaceWith(list_pagination);
                   $('.tbody_list_product').parents('#product_catalog_list').removeClass('loading').removeClass('small_space_top');
               }
               else
                    $('.tbody_list_product').parents('#product_catalog_list').removeClass('loading').removeClass('small_space_top');
            }
            insertwidthvaluetable();
            if($('tbody.sortable').length){
                $('tbody.sortable', $('form#product_catalog_list')).sortable({
                    placeholder: 'placeholder',
                    update(event, ui) {
                        const positionSpan = $('span.position', ui.item)[0];
                        $(positionSpan).css('color', 'red');
                        bulkProductEdition(event, 'sort');
                    },
                });
            }
        },
        error: function(xhr, status, error){

        }
    });
}
 function ets_resizeColumn()
 {
    $('#product_catalog_list tr:first-child td').removeAttr('style');
    $('#product_catalog_list tr th').removeAttr('style');
    var index= 0;
    insertwidthvaluetable();
    var left = $('.table-responsive').scrollLeft();
    $('thead.scroll_heading.with-filters').css('margin-left', -left);
    $(window).scroll();
}
 function ets_productmanagerRenderText()
 {
    if($('#desc-product-sql-manager').length)
    {
        $('#desc-product-sql-manager').after('<a id="desc-productmanager-setting" class="dropdown-item" href="'+ets_pmg_link_productmanager_setting+'" >'+Product_manager_settings+'</a>');
        $('#desc-product-sql-manager').after('<a id="desc-product-arrange" class="dropdown-item" href="'+ets_pmg_link_product_arrange+'" >'+ Customize_product_list_text+'</a>');
    }
    if($('.product-edit-popup').length)
    {
        $('.product-edit-popup').attr('title',ets_pmn_edit_text);
        $('.product-edit-popup').html(ets_pmn_edit_text);
    }
    if($('button[name="submitProductChangeInLine"]').length)
    {
        $('button[name="submitProductChangeInLine"]').html(ets_pmn_update_text);
    }
    if($('.cancel_product_change_link').length)
    {
        $('.cancel_product_change_link').html(ets_pmn_cancel_text);
    }
    if($('.ets_pm_viewmore').length)
    {
        if ( viewmore_text == '' ){
            var viewmore_text = 'View more';
        }
        if (viewless_text == '' ){
            var viewless_text = 'View less';
        }
        $('.ets_pm_viewmore').attr('data-more',viewmore_text);
        $('.ets_pm_viewmore').attr('data-less',viewless_text);
    }
    if($('.column-headers').length && ETS_PMN_FIXED_HEADER_PRODUCT==1)
    {
        $('.column-headers').addClass('fixed');
        $('.column-filters').addClass('fixed');
    }
    if($('#product_catalog_category_tree_filter').length)
    {
        $.ajax({
            url: '',
            data: 'getFormListView&ajax=1',
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('#product_catalog_category_tree_filter').parent().append(json.html);
                $('#product_catalog_category_tree_filter').parent().append('<div class="d-inline-block">\n' +
                    '            <a id="desc-product-arrange2" class="btn btn-default btn-outline-secondary ml-2" href="'+ets_pmg_link_product_arrange+'">\n' +
                    '                '+Customize_product_list_text+'\n' +
                    '            </a>\n' +
                    '          </div>');
            },
            error: function(xhr, status, error)
            {

            }
        });

    }
}
 var column_filters_fixed_height = '';
 $(document).ready(function(){
    ets_productmanagerRenderText();
    $(document).on('change','input:checkbox[name="bulk_action_selected_products[]"]',function(){
        updateBulkMenu();
    });
    column_filters_fixed_height = $('.column-filters.fixed').height();
    $('.table-responsive >table').before('<div class="scroll_tabheader"><div class="scroll_tabheader_bar"></div></div>')
    var width_child = $('.table-responsive >table').width();
    var width_parent = $('.table-responsive').width();
    if(width_child > width_parent)
        $('.scroll_tabheader').addClass('show');
    $('.scroll_tabheader .scroll_tabheader_bar').css('width',width_child+'px');
    $('.scroll_tabheader').css('width',width_parent+'px');

    $(document).on('change','.form-min-max,.form-min-min',function(){
        if($(this).val()!='' && !validate_isFloat($(this).val()))
        {
            $(this).addClass('error');
            $(this).val('');
            $(this).change();
        }
        else if($(this).val()=='')
        {
            var $this = $(this);
            setTimeout(function(){$this.removeClass('error');},1000);
        }
    });
    if($('.tbody_list_product').hasClass('search'))
    {
        $(document).on('keyup','input[name="paginator_product_jump_page"]',function(e){
            var val = parseInt($(this).val());
            if(e.keyCode==13)
            {
                if (parseInt(val) > 0) {
    				var limit = $(this).attr('pslimit');
    				var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
    				var formData = new FormData($(this).parents('form').get(0));
                    if(run_ajax)
                        clearTimeout(run_ajax);
                    runAjaxFilterProduct(url,formData);
            		return false;
    			}
            }
			var max = parseInt($(this).attr('psmax'));
			if (val > max) {
				$(this).val(max);
				return false;
			}
        });
        $(document).on('change','input[name="paginator_product_jump_page"]',function(){
            var val = parseInt($(this).val());
			if (parseInt(val) > 0) {
				var limit = $(this).attr('pslimit');
				var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
				var formData = new FormData($(this).parents('form').get(0));
                if(run_ajax)
                    clearTimeout(run_ajax);
                runAjaxFilterProduct(url,formData);
        		return false;
			}
        });
        $(document).on('change','select[name="paginator_product_select_page_limit"]',function(){
            var url = $(this).attr('psurl').replace(/_limit/, $('option:selected', this).val());
            $('#product_catalog_list').addClass('loading');
            $('#product_catalog_list').attr('action',url);
            var formData = new FormData($(this).parents('form').get(0));
            if(run_ajax)
                clearTimeout(run_ajax);
            runAjaxFilterProduct(url,formData);
            window.history.pushState("", "", url);
    		return false;
        });
        $(document).on('click','.pagination_list_product a.page-link',function(){
            $('#product_catalog_list').addClass('loading');
            var formData = new FormData($(this).parents('form').get(0));
            if(run_ajax)
                clearTimeout(run_ajax);
            runAjaxFilterProduct($(this).attr('href'),formData);
            return false;
        });
        $(document).on('change','.with-filters select',function(){
            var formData = new FormData($(this).parents('form').get(0));
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            checkShowReset();
            runAjaxFilterProduct($(this).parents('form').attr('action'),formData);
        });
        $(document).on('input','.with-filters input',function(e){
            if(($(this).hasClass('form-min-max') || $(this).hasClass('form-min-min')) && !validate_isFloat($(this).val()))
            {
                return false;
            }
            if($('.with-filters input').length)
            {
                $('.with-filters input').each(function(){
                    if($(this).attr('sql')!=undefined)
                        $(this).val($(this).attr('sql'));
                });
            }
            var formData = new FormData($(this).parents('form').get(0));
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            checkShowReset();
            run_ajax = setTimeout(function(){runAjaxFilterProduct($(this).parents('form').attr('action'),formData);},time_run_ajax);
        });
        $(window).keydown(function(event){
            if(event.keyCode == 13 && ($('.with-filters input.is_focus').length>0 || $('input[name="paginator_product_jump_page"]').hasClass('is_focus')) ) {
              event.preventDefault();
              return false;
            }
        });
        $(document).on('focus','.with-filters input,input[name="paginator_product_jump_page"]',function(e){
            $(this).addClass('is_focus');
        });
        $(document).on('blur','.with-filters input,input[name="paginator_product_jump_page"]',function(e){
            $(this).removeClass('is_focus');
        });
        $(document).on('keyup','.with-filters input',function(e){
            var $this = $(this);
            $(this).change();
            if(($(this).hasClass('form-min-max') || $(this).hasClass('form-min-min')) && !validate_isFloat($(this).val()))
            {
                return false;
            }
            if($('.with-filters input').length)
            {
                $('.with-filters input').each(function(){
                    if($(this).attr('sql')!=undefined)
                        $(this).val($(this).attr('sql'));
                });
            }
            var formData = new FormData($(this).parents('form').get(0));
            if(!$(this).hasClass('error') &&( (e.keyCode >=48 && e.keyCode<=57) || (e.keyCode >=96 && e.keyCode<=105) || (e.keyCode >=65 && e.keyCode<=90) || e.keyCode==188 || e.keyCode==190 || e.keyCode==13 || e.keyCode==8) )
            {
                if(xhrAjax)
                    xhrAjax.abort();
                if(run_ajax)
                    clearTimeout(run_ajax);
                run_ajax = setTimeout(function(){runAjaxFilterProduct($this.parents('form').attr('action'),formData);},time_run_ajax);
            }

        });
        $(document).on('click','button[name="products_filter_reset"]',function(){
            $('.with-filters input').val('');
            var formData = new FormData($(this).parents('form').get(0));
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            runAjaxFilterProduct($(this).parents('form').attr('action'),formData);
            $('button[name="products_filter_submit"]').attr('disabled','disabled');
            $('button[name="products_filter_reset"]').hide();
            return false;
        });
    }

    if ( $('.ets_td').length > 0 ){
        $('.ets_td .popup_change_product, .ets_td .span_change_product').each(function(){
           var content_height = $(this).find('.content_info').height();
           if ( content_height > 60 ){
              $(this).addClass('more');
           }
        });
    }
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

    if($('.column-headers.fixed').length)
    {
        var index= 0;
        $('tr th').each(function(){
            var $width = $(this).width();
            $('tr:first-child td:not(.no-product)').eq(index).attr('style','min-width:'+$width+'px!important;max-width:'+$width+'px!important;width:'+$width+'px!important;');
            index++;
        });
    }
    if ($("input.searchdatepicker").length > 0) {
        var dateToday = new Date();
        $("input.searchdatepicker").removeClass('hasDatepicker').attr('autocomplete','off');
        $("input.searchdatepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            changeMonth:true,
            changeYear:true,
            onSelect: function() {
                var formData = new FormData($('input.searchdatepicker').parents('form').get(0));
                setTimeout(function(){runAjaxFilterProduct($('#product_catalog_list').attr('action'),formData)},500);
            }
        });
    }
    $('.table-responsive').on('scroll', function(e) {
        var left = $(this).scrollLeft();
        $('thead.scroll_heading.with-filters').css('margin-left', -left);
        $('.scroll_tabheader').scrollLeft(left);
    });
    $('.scroll_tabheader').on('scroll', function(e) {
        var left = $(this).scrollLeft();
        $('thead.scroll_heading.with-filters').css('margin-left', -left);
        $('.table-responsive').scrollLeft(left);
    });
    /*fixed header table*/
if($('.column-headers.fixed').length)
{
    $('#product_catalog_list').addClass('fixed_load');
    var sticky_navigation_offset_top = $('.with-filters').offset().top;
    var headerFloatingHeight = $('.header-toolbar').height()+ $('#header_infos').height();
    var sticky_navigation = function(){
        var scroll_top = $(window).scrollTop();
        var parent_width = $('#product_catalog_list .with-filters').parents('.table-responsive').width();
        if (scroll_top > sticky_navigation_offset_top - headerFloatingHeight) {
            insertwidthvaluetable();

            if($('.ets_thead').length==0)
            {
                $('thead.with-filters').addClass('ets_thead');
            }
            $('#product_catalog_list .ets_thead').addClass('scroll_heading').css({'margin-top':headerFloatingHeight+'px'});
            $('.scroll_tabheader').addClass('scroll_heading').css({'margin-top':(headerFloatingHeight+$('#product_catalog_list .ets_thead').height())+'px'});
            var theade_heaight = $('#product_catalog_list .ets_thead.scroll_heading').height();
            $('#product_catalog_list').css({'margin-top':theade_heaight+column_filters_fixed_height+'px'});
            var left = $('.table-responsive').scrollLeft();
            $('thead.scroll_heading.with-filters').css('margin-left', -left);
            $('#product_catalog_list.fixed_load').addClass('fixing');
            if ( $('#product_catalog_list.fixed_load').length > 0 ) {
                var loadmore_top = theade_heaight + headerFloatingHeight + 100;
                var nav_left_space = 165 - $('.nav-bar').width();

                $('#ets_warningGradientOuterBarG').css({'margin-top':loadmore_top+'px'}).css({'margin-left':nav_left_space+'px'});
            } else {
                $('#ets_warningGradientOuterBarG').css({'margin-top':'-'+theade_heaight+'px'});
            }
        } else {
            $('#product_catalog_list .ets_thead').removeClass('scroll_heading').css({'margin-top':''}).css({'width':''});
            $('.scroll_tabheader').removeClass('scroll_heading').css({'margin-top':''});
            $(this).parents('table.table.product').find('.with-filters').find('th').css({'width':'auto'});
            $('#product_catalog_list').css({'margin-top':''});
            $('#ets_warningGradientOuterBarG').css({'margin-top':''}).css({'margin-left':''});
            if($('.ets_thead').length>0)
            {
                $('.ets_thead').removeClass('ets_thead');
            }
            insertwidthvaluetable();
            $('#product_catalog_list.fixed_load').removeClass('fixing');
        }
    };
    $(window).scroll(function() {
        sticky_navigation();
        if ( $('#product_catalog_list.fixed_load').length > 0 ){
            if ( $('#ets_warningGradientOuterBarG').length <= 0 ){
                $('#product_catalog_list.fixed_load').find('.table-responsive').append('<div id="ets_warningGradientOuterBarG"><div id="ets_warningGradientFrontBarG" class="ets_warningGradientAnimationG"><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div></div></div>');
            }
        }

    });
}

$(document).on('click','.dropdown-toggle-poup',function(){
    $(this).parent().next('.dropdown-menu').toggle();
});
$(document).on('change','.custom-file-input',function(){
    $(this).next('.custom-file-label').html($(this).val().replace('C:\\fakepath\\',''));
});
$(document).on('click','#desc-product-arrange,#desc-product-arrange2',function(e){
    e.preventDefault();
    var href= $(this).attr('href');
    $('body').addClass('loading');
    $.ajax({
        url: href,
        data: 'ajax=1',
        type: 'post',
        dataType: 'json',
        success: function(json){
            $('#block-form-popup-dublicate').html(json.block_html);
            $('.ets_product_popup').addClass('show');
            $('body').removeClass('loading');
        },
        error: function(xhr, status, error)
        {

        }
    });
});
$(document).on('click','.ets_product_popup .product-combination-image',function(){
    $(this).toggleClass('img-highlight');
    var number = $(this).parents('.js-combination-images').find('.product-combination-image.img-highlight').length;
    var allProductCombination = $(this).parents('.js-combination-images').find('.product-combination-image').length;
    $(this).parents().find('.form-control-label.number-of-images').html(number+'/'+allProductCombination);
});
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
$(document).on('keyup','body',function(e){
    if(e.keyCode == 27) {
        if ($('.ets_product_popup').length)
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
    }
});
$(document).mouseup(function (e){
    if($('.customFieldCollection .dropdown-menu').length)
    {
        var container_dropdown = $('.customFieldCollection .dropdown-menu');
        if (!container_dropdown.is(e.target)&& container_dropdown.has(e.target).length === 0)
        {
            container_dropdown.hide();
        }
    }
});
$(document).on('click','.open_close_list',function(){
    $(this).toggleClass('list_close').toggleClass('list_open');
    $(this).next('.list-group-content').toggle();
});
$(document).on('click','.btn_save_view',function(){
    $('#form_arrange .form-save-view').addClass('show');
    if($('#id_view_selected').val()!=0)
    {
        $('button[name="btnSubmitSaveAsView"]').show();
        $('#view_name').val($('#id_view_selected option[value="'+$('#id_view_selected').val()+'"]').html());
    }
    else
    {
        $('button[name="btnSubmitSaveAsView"]').hide();
        $('#view_name').val('');
    }
});
$(document).on('click','.close_form_save_view,.tbn-cancel-view',function(){
    $('#form_arrange .form-save-view').removeClass('show');
});
$(document).on('change','#id_view_selected2',function(){
    var $this = $(this);
    $.ajax({
        url: $this.data('href'),
        data: 'submitChangeView=1&id_view_selected='+$this.val(),
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.success)
            {
                $.growl.notice({ message: json.success });
                window.location.reload();
            }
        },
        error: function(xhr, status, error)
        {
            $this.removeClass('loading');
        }
    });
});
$(document).on('change','#id_view_selected',function(){
    var fields = $('#id_view_selected option[value="'+$(this).val()+'"]').data('fields');
    $('#list-product-fields').html('');
    $('.list-group-content input[type="checkbox"]').removeAttr('checked');
    $('.list-group-content input[type="checkbox"]').prop('checked',false);
    if($(this).val()!=0)
    {
        $('.btn_delete_view').show();
        $('.btn_save_view').html(Update_view_text);
    }
    else
    {
        $('.btn_delete_view').hide();
        $('.btn_save_view').html(Save_view_text);
    }
    if(fields)
    {
        fields = fields.split(',');
        for(var i=0;i<fields.length;i++)
        {
            if($('input.arrange_list_product[value="'+fields[i]+'"]').length)
            {
                $('input.arrange_list_product[value="'+fields[i]+'"]').prop('checked',true);
                $('input.arrange_list_product[value="'+fields[i]+'"]').change();
            }

        }
    }
    var $this = $(this);
    $.ajax({
        url: $this.parents('form').attr('action'),
        data: 'submitChangeView=1&id_view_selected='+$this.val(),
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.success)
            {
                $.growl.notice({ message: json.success });
                window.location.reload();
            }
        },
        error: function(xhr, status, error)
        {
            $this.removeClass('loading');
        }
    });
});
$(document).on('click','.btn_delete_view',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loading') && $('#id_view_selected').val()!=0)
    {
        var $this = $(this);
        $(this).addClass('loading');
        $('.ets_alert_error').remove();
        $.ajax({
            url: $this.parents('form').attr('action'),
            data: 'btnSubmitDeleteView=1&id_view_selected='+$('#id_view_selected').val(),
            type: 'post',
            dataType: 'json',
            success: function(json){
                $this.removeClass('loading');
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    if(json.list_sellect_view)
                    {
                        $('#form_view_selected').html(json.list_sellect_view);
                        $('#id_view_selected').change();
                        $('.btn_delete_view').hide();
                    }
                }
                if(json.errors)
                {
                    $this.before(json.errors);
                }
            },
            error: function(xhr, status, error)
            {
                $this.removeClass('loading');
            }
        });
    }

});
$(document).on('click','button[name="btnSubmitSaveView"],button[name="btnSubmitSaveAsView"]',function(e){
    e.preventDefault();
    $('.ets_alert_error').remove();
    if(!$(this).hasClass('loading'))
    {
        var $this = $(this);
        var formData = new FormData($(this).parents('form').get(0));
        formData.append($(this).attr('name'),1);
        $(this).addClass('loading');
        $.ajax({
            url: $this.parents('form').attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $this.removeClass('loading');
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    window.location.reload();
                    if(json.list_sellect_view)
                    {
                        $('#form_view_selected').html(json.list_sellect_view);
                    }
                }
                if(json.errors)
                {
                    $this.parents('.form-save-view').find('.form-body .col-lg-9.error').append('<span class="ets_alert_error">'+json.errors+'</span>');
                }
            },
            error: function(xhr, status, error)
            {
                $this.removeClass('loading');
            }
        });
    }

});
$(document).on('click','button[name="btnSubmitArrangeListProduct"]',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loading'))
    {
        var $this = $(this);
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('submitArrangeListProduct',1);
        $(this).addClass('loading');
        $.ajax({
            url: $(this).parents('form').attr('action'),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $this.removeClass('loading');
                if(json.success) {
                    var $successMsg = $('<div class="ets-pm-alert-success alert alert-success"></div>');
                    $successMsg.html(json.message);
                    $('body').append($successMsg);
                    setTimeout(function() {
                        $successMsg.remove();
                    }, 3000);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }
                if(json.errors)
                {
                    $this.before(json.errors);
                    _etsPmncloseErrors();
                }
            },
            error: function(xhr, status, error)
            {
                $this.removeClass('loading');
            }
        });
    }
});
$(document).on('click','.all_arrange_list_product',function(){
    var $list_group = $(this).closest('.list-group');
    if($(this).is(':checked'))
    {
        $list_group.find('input.arrange_list_product').attr('checked','checked');
        $list_group.find('input.arrange_list_product').prop('checked',true);
    } else {
        $list_group.find('input.arrange_list_product').removeAttr('checked');
        $list_group.find('input.arrange_list_product').prop('checked',false);
    }

    $list_group.find('input.arrange_list_product').change();
});
$(document).on('click','.close_field',function(){
    var field= $(this).data('field');
    $('#list-product-fields .field_'+field).remove();
    $('input.arrange_list_product[value="'+field+'"]').removeAttr('checked');
    $('input.arrange_list_product[value="'+field+'"]').change();
});
$(document).on('click','.clear_all_fields',function(e){
    e.preventDefault();
    $('#list-product-fields').html('');
    $('.list-group-content input[type="checkbox"]').removeAttr('checked');
    $('.list-group-content input[type="checkbox"]').prop('checked',false);
});
$(document).on('change','.arrange_list_product',function(){
    var field = $(this).val();
    var field_title= $(this).data('title');
    if($(this).is(':checked'))
    {
        if($('#list-product-fields .field_'+field).length==0)
        {
            $('#list-product-fields').append('<li class="field_'+field+'"><label><i class="ets_svg_icon"><svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"></path></svg></i><input name="listFieldProducts[]" value="'+field+'" type="hidden">'+field_title+'</label><span class="close_field" data-field="'+field+'"> Close</span></li>')
        }
        if($(this).closest('.list-group').find('input.arrange_list_product:checked').length == $(this).closest('.list-group').find('input.arrange_list_product').length)
        {
            $(this).closest('.list-group').find('.all_arrange_list_product').attr('checked','checked');
            $(this).closest('.list-group').find('.all_arrange_list_product').prop('checked',true);
        }
    }
    else
    {
        $('#list-product-fields .field_'+field).remove();
        $(this).closest('.list-group').find('.all_arrange_list_product').removeAttr('checked');
        $(this).closest('.list-group').find('.all_arrange_list_product').prop('checked',false);
    }
});
$(document).on('click','button[name="btnSubmitRessetToDefaultList"]',function(e){
    e.preventDefault();
    var $this = $(this);
    $this.addClass('loading');
    $.ajax({
        url: $(this).parents('form').attr('action'),
        data: 'submitRessetToDefaultList=1',
        type: 'post',
        dataType: 'json',
        success: function(json){
            window.location.reload();
            $this.removeClass('loading');
        },
        error: function(xhr, status, error)
        {
            $this.removeClass('loading');
        }
    });
});
$('form#product_catalog_list').submit(function(e) {
    $('#filter_column_minimal_quantity', $(this)).val($('#filter_column_minimal_quantity', $(this)).attr('sql'));
    $('#filter_column_low_stock_threshold', $(this)).val($('#filter_column_low_stock_threshold',$(this)).attr('sql'));
    $('#filter_column_priority_product', $(this)).val($('#filter_column_priority_product',$(this)).attr('sql'));
});
$(document).on('click','.hideOtherLanguageInline',function(){
    hideOtherLanguageInline($(this).parents('tr').data('product-id'),$(this).data('id-lang')) ;
    return false;
});
$(document).on('click','.hideOtherLanguagePopup',function(){
    hideOtherLanguagePopup($(this).data('id-lang')) ;
    return false;
});
$(document).on('click','.hideOtherLanguageImage',function(){
    hideOtherLanguageImage($(this).data('id-lang'),$(this).data('iso')) ;
    return false;
});
$(document).on('click','.span_change_product.edit',function(){
    var id_product = $(this).closest('tr').data('product-id');
    $(this).next('.wapper-change-product').addClass('show');
    var $id = $(this).attr('data-name')+'_'+$(this).attr('data-lang');
    if($('.ets_pmn_autoload_rte').length)
    {
        $(this).parents('td').find('.ets_pmn_autoload_rte').addClass('ets_pmn_autoload_rte_runing');
        tinySetup({
            editor_selector :"ets_pmn_autoload_rte_runing",
        });
        $('.ets_pmn_autoload_rte_runing').removeClass('ets_pmn_autoload_rte_runing');
        if($('#'+$id).hasClass('ets_pmn_autoload_rte'))
        {
            tinyMCE.get($id).focus();
            tinyMCE.get($id).selection.select(tinyMCE.get($id).getBody(), true);
            tinyMCE.get($id).selection.collapse(false);
        }
    }
    if(!$('#'+$id).hasClass('ets_pmn_autoload_rte'))
    {
        var text_val = $('#'+$id).val();
        $('#'+$id).val('').focus();
        $('#'+$id).val(text_val);
    }
});
$(document).on('click','.cancel_product_change_link',function(e){
    e.preventDefault();
    var id_product = $(this).closest('tr').data('product-id');
    $(this).parents('tr').removeClass('ets_pmn_tr_editting').find('.group-action-update-product').hide(0);
    $(this).parents('tr').find('.span_change_product').show(0).removeClass('edit');
    $(this).parents('tr').find('.wapper-change-product').hide(0).removeClass('show');
    if($('tr[data-product-id="'+id_product+'"] .is_lang_default').length)
    {
        $('tr[data-product-id="'+id_product+'"] .is_lang_default').each(function(){
            var text_value = $(this).val().replace(/(<([^>]+)>)/gi, "");
            if(ets_max_lang_text && text_value.length > ets_max_lang_text)
            {
                text_value = text_value.substring(0,ets_max_lang_text)+'...';
            }
            $(this).parents('td').find('.span_change_product .content').html(text_value ? text_value :'<div class="content_info"><span class="text-center">--</span></div>');
        });
    }
    ets_changloadmore();
    if ( $(this).parents('.ets_td').length > 0 ){
        $(this).parents('tr .span_change_product').removeClass('more');
        $(this).parents('tr').find('.popup_change_product').each(function(){
            var content_height = $(this).find('.content_info').height();
            if ( content_height > 60 ){
                $(this).addClass('more');
            }
            else
                $(this).removeClass('more');
        });
        $(this).parents('tr').find('.span_change_product').each(function(){
            var content_height = $(this).find('.content_info').height();
            if ( content_height > 60 ){
                $(this).addClass('more');
            }
            else
                $(this).removeClass('more');
        });
    }

});
$(document).on('change','.column.price input[name="price"]',function(){
    var id_product = $(this).closest('tr').data('product-id');
    if($(this).val() =='')
        $(this).val('0.00');
    if($('tr[data-product-id="'+id_product+'"] input[name="price_final"]').length)
    {
        if(parseFloat($(this).val()) >=0)
        {
            if($('tr[data-product-id="'+id_product+'"] select[name="tax_name"]').length)
               var id_tax_rules_group = $('tr[data-product-id="'+id_product+'"] select[name="tax_name"]').val();
            else
                var id_tax_rules_group = $('tr[data-product-id="'+id_product+'"] input[name="id_tax_rules_group"]').val();
            if(tax = ets_pmn_tax_rule_groups[id_tax_rules_group])
            {
                var price_incl = parseFloat($(this).val()) + parseFloat($(this).val())* parseFloat(tax);
                $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val(price_incl.toFixed(6));
            }
            else
            {
                $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val($(this).val());
            }
        }
        else
            $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val('0.00');

    }
});
$(document).on('change','.column.price_final input[name="price_final"]',function(){
    var id_product = $(this).closest('tr').data('product-id');
    if($(this).val() =='')
        $(this).val('0.00');
    if($('tr[data-product-id="'+id_product+'"] input[name="price"]').length)
    {
        if(parseFloat($(this).val()) >=0)
        {
            if($('tr[data-product-id="'+id_product+'"] select[name="tax_name"]').length)
                var id_tax_rules_group = $('tr[data-product-id="'+id_product+'"] select[name="tax_name"]').val();
            else
                var id_tax_rules_group = $('tr[data-product-id="'+id_product+'"] input[name="id_tax_rules_group"]').val();
            if(tax = ets_pmn_tax_rule_groups[id_tax_rules_group])
            {
                var price_excl = parseFloat($(this).val())/(1+tax);
                $('tr[data-product-id="'+id_product+'"] input[name="price"]').val(price_excl.toFixed(6));
            }
            else
            {
                $('tr[data-product-id="'+id_product+'"] input[name="price"]').val($(this).val());
            }
        }
        else
            $('tr[data-product-id="'+id_product+'"] input[name="price"]').val('0.00');

    }
});
$(document).on('change','.column.tax_name select[name="tax_name"]',function(){
    var id_product = $(this).closest('tr').data('product-id');
    if($('tr[data-product-id="'+id_product+'"] input[name="price"]').length && $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').length)
    {
        var tax = ets_pmn_tax_rule_groups[$(this).val()];
        var price_excl = parseFloat($('tr[data-product-id="'+id_product+'"] input[name="price"]').val());
        if(price_excl >=0)
        {
            if(tax)
            {
                var price_incl = price_excl + price_excl* parseFloat(tax);
                $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val(price_incl.toFixed(6));
            }
            else
            {
                $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val(price_excl.toFixed(6));
            }
        }

    }
});

$(document).on('click','button[name="submitProductChangeInLine"],button[name="submitProductChangeInLine2"]',function(e){
    e.preventDefault();
    var $this = $(this);
    if(!$this.hasClass('loading'))
    {
        $this.addClass('loading');
        $('.module_error.alert').parent().remove();
        tinymce.triggerSave();
        var id_product = $(this).parents('tr').data('product-id');
        var checkboxs = '';
        if($('tr[data-product-id="'+id_product+'"]:input[type="checkbox"]').length)
        {
            $('tr[data-product-id="'+id_product+'"]:input[type="checkbox"]').each(function(){
                checkboxs += '&'.$(this).attr('name')+'='+($(this).is(':checked') ? '1':'0')
            });
        }
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: $('tr[data-product-id="'+id_product+'"] :input').serialize()+'&submitProductChangeInLine=1'+checkboxs,
            type: 'post',
            dataType: 'json',
            success: function(jsonData){
                $this.removeClass('loading');
                if(jsonData.success)
                {
                    $.growl.notice({ message: jsonData.success });
                    $(jsonData.columns).each(function(){
                        let $tdContent = $('tr[data-product-id="'+id_product+'"] td.column.'+this.name +' .content');
                        if($tdContent.length)
                        {
                            let _hasWrapContent = $tdContent.children('.content_info').length ? true : false,
                                _value = this.value ? this.value : '<div class="text-center">--</div>';
                            if($this.attr('name')=='submitProductChangeInLine') {
                                $tdContent.html(
                                    _hasWrapContent 
                                    ? '<div class="content_info">' + _value + '</div>'
                                    : _value
                                );
                            } else {
                                $tdContent.html(
                                    _hasWrapContent 
                                    ? '<div class="content_info">' + _value + '</div>'
                                    : _value
                                );
                                $tdContent.html('<div class="content_info">'+_value+'</div>');
                            }
                        }
                    });
                    if($this.attr('name')=='submitProductChangeInLine')
                    {
                        $('tr[data-product-id="'+id_product+'"]').find('.group-action-update-product').hide(0);
                        $('tr[data-product-id="'+id_product+'"]').removeClass('ets_pmn_tr_editting').find('.span_change_product').show().removeClass('edit');
                        $('tr[data-product-id="'+id_product+'"]').find('.wapper-change-product').hide().removeClass('show');
                    }
                    else{
                        $('tr[data-product-id="'+id_product+'"]').find('.wapper-change-product.popup').removeClass('show');
                    }
                    ets_changloadmore();
                }
                if(jsonData.errors){
                    $('#product_catalog_list').before(jsonData.errors);
                }

            },
            complete: function (data) {
                ets_changloadmore();
            },
            error: function(xhr, status, error)
            {
                $this.removeClass('loading');
            }
        });
    }

});
$(document).on('click','.ets_pmn-product-feature .ets_pmn-delete',function(){
    if(confirm(delete_item_comfirm))
    {
        $(this).closest('.ets_pmn-product-feature').remove();
    }
});
$(document).on('click','.ets_product_popup button#module_form_submit_btn',function(e){
    e.preventDefault();
    var $this = $(this);
    tinymce.triggerSave();
    $('.ets_product_popup .module_error').parent().remove();
    var id_product = $(this).prev('input[name="id_product"]').val();
    var formData = new FormData($(this).parents('form').get(0));
    formData.append($(this).attr('name'),1);
    $(this).addClass('loading');
    $.ajax({
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
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
                $('#block-form-popup-dublicate').html('');
                $.growl.notice({ message: json.success });
                if($('tr[data-product-id="'+id_product+'"] .column.'+json.row_name).length)
                {
                    $('tr[data-product-id="'+id_product+'"] .column.'+json.row_name+' .content').html('<div class="content_info">'+json.row_value+'</div>');
                    if(json.row_name=='combinations')
                    {
                        if($('tr[data-product-id="'+id_product+'"] td.column.sav_quantity .content').length)
                            $('tr[data-product-id="'+id_product+'"] td.column.sav_quantity').html(json.sav_quantity);
                    }
                    if(json.row_name=='sav_quantity')
                    {
                        if($('tr[data-product-id="'+id_product+'"] td.column.combinations .content').length)
                            $('tr[data-product-id="'+id_product+'"] td.column.combinations .content').html('<div class="content_info">'+json.list_combinations+'</div>');
                    }
                    if(json.czf_product)
                    {
                        var czfProduct = json.czf_product;
                        if($('tr[data-product-id="'+id_product+'"] .column.version').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.version .content').html(czfProduct.version ? '<div class="content_info">'+czfProduct.version+'</div>' :'--');
                            $('tr[data-product-id="'+id_product+'"] .column.version input[name="version"]').val(czfProduct.version);
                        }
                        if($('tr[data-product-id="'+id_product+'"] .column.compatibility').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.compatibility .content').html(czfProduct.compatibility ? '<div class="content_info">'+czfProduct.compatibility+'</div>' :'--');
                            $('tr[data-product-id="'+id_product+'"] .column.compatibility input[name="compatibility"]').val(czfProduct.compatibility);
                        }
                        if($('tr[data-product-id="'+id_product+'"] .column.min_ps_version').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.min_ps_version .content').html(czfProduct.min_ps_version ? '<div class="content_info">'+czfProduct.min_ps_version+'</div>' :'--');
                            $('tr[data-product-id="'+id_product+'"] .column.min_ps_version input[name="min_ps_version"]').val(czfProduct.min_ps_version);
                        }
                        if($('tr[data-product-id="'+id_product+'"] .column.module_logo').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.module_logo .content').html(czfProduct.logo ? '<img src="'+ETS_CZF_BASE_URI+czfProduct.logo+'">':'--');
                        }
                        if($('tr[data-product-id="'+id_product+'"] .column.module_name').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.module_name .content').html(czfProduct.display_name[json.id_lang] ? '<div class="content_info">'+czfProduct.display_name[json.id_lang]+'</div>' :'--');
                            $('tr[data-product-id="'+id_product+'"] input[name*="module_name_"]').each(function () {
                                var idLang = $(this).attr('id').replace('module_name_'+id_product+'_','');
                                $(this).val(czfProduct.display_name[idLang] || '');
                            });
                        }
                        if($('tr[data-product-id="'+id_product+'"] .column.module_description').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] .column.module_description .content').html(czfProduct.description[json.id_lang] ? '<div class="content_info">'+czfProduct.description[json.id_lang]+'</div>' :'--');
                            $('tr[data-product-id="'+id_product+'"] textarea[name*="module_description_"]').each(function () {
                                var idLang = $(this).attr('id').replace('module_description_'+id_product+'_','');
                                $(this).val(czfProduct.description[idLang] || '');
                            });
                        }
                    }
                }
                if(json.row_name=='name_category')
                {
                    if($('tr[data-product-id="'+id_product+'"] .column.categories').length)
                    {
                        $('tr[data-product-id="'+id_product+'"] .column.categories .content').html('<div class="content_info">'+json.categories+'</div>');

                    }
                }
                ets_changloadmore();
            }
            if(json.errors)
            {
                $this.after(json.errors);
                _etsPmncloseErrors();
            }
            ets_changloadmore();
        },
        error: function(xhr, status, error)
        {
            $this.removeClass('loading');
        }
    });
    ets_changloadmore();
});
$(document).on('click','button#create-combinations',function(e){
    e.preventDefault();
    $('.ets_pmn_errors').html('');
    var id_product = $('.ets_product_popup input[name="id_product"]').val();
    if($('.js-attribute-checkbox:checked').length>0)
    {
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('submitCreateCombination',1);
        formData.append('ajax', 1);
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    $('#attributes-generator .tokenfield').html('');
                    $('.js-attribute-checkbox:checked').prop('checked',false);
                    $('.combinations-list').html(json.list_combinations);
                    $('tr[data-product-id="'+id_product+'"] td.column.combinations .content').html('<div class="content_info">'+json.combinations+'</div>');
                    if($('tr[data-product-id="'+id_product+'"] td.column.sav_quantity .content').length)
                        $('tr[data-product-id="'+id_product+'"] td.column.sav_quantity').html('<div class="content_info">'+json.sav_quantity+'<div>');
                    $('.js-bulk-combinations').html('0');
                    $('#js-bulk-combinations-total').html($('.combinations-list tbody tr').length);
                    ets_changloadmore();
                }
                else if(json.errors)
                {
                    $('.ets_pmn_errors').html(json.errors);
                    _etsPmncloseErrors();
                }
            },
            error: function(xhr, status, error)
            {

            }
        });
    }
});
$(document).on('click','.attribute-actions.delete .btn.delete',function(e){
    e.preventDefault();
    if(!$(this).hasClass('active'))
    {
        if(confirm(delete_item_comfirm))
        {
            $(this).addClass('active');
            var $this= $(this);
            var id_product_attribute= $(this).attr('data');
            var id_product = $('.ets_product_popup input[name="id_product"]').val();
            $.ajax({
                url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
                data: {
                    id_product_attribute:id_product_attribute,
                    submitDeleteProductAttribute:1,
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#attribute_'+id_product_attribute).remove();
                        $('tr[data-product-id="'+id_product+'"] td.column.combinations .content').html('<div class="content_info">'+json.combinations+'</div>');
                        if($('tr[data-product-id="'+id_product+'"] td.column.sav_quantity .content').length)
                            $('tr[data-product-id="'+id_product+'"] td.column.sav_quantity').html('<div class="content_info">'+json.sav_quantity+'</div>');
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                    $this.removeClass('active');
                    ets_changloadmore();
                },
                error: function(error)
                {
                    $this.removeClass('active');
                }
            });
        }
    }
});
$(document).on('click','.attribute-actions.edit .btn-open',function(e){
    if($(this).next($(this).attr('href')).length && $('.ets_pmn-form-content-setting-combination '+$(this).attr('href')).length )
        $('.ets_pmn-form-content-setting-combination '+$(this).attr('href')).remove();
    if($('.ets_pmn-form-content-setting-combination '+$(this).attr('href')).length==0)
    {
        if($(this).next($(this).attr('href')).length)
        {
            $('.ets_pmn-form-content-setting-combination').append($(this).next($(this).attr('href')).clone());
            $(this).next($(this).attr('href')).remove();
        }
    }
    $('.combination-form.row').addClass('hide');
    $('.ets_pmn-form-content-setting-combination '+$(this).attr('href')).removeClass('hide');
    $('.ets_pmn-form-content').hide();
    if ($(".datepicker input").length > 0) {
        var dateToday = new Date();
        $(".datepicker input").removeClass('hasDatepicker');
        $(".datepicker input").datepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            minDate: dateToday,
        });
    }
    return false;
});
$(document).on('click','.combination-form .back-to-product',function(){
    $('.combination-form').addClass('hide');
    $('.ets_pmn-form-content').show();
});
$(document).on('change','.attribute_priceTE',function(){
    var id_product_attribute= $(this).data('id_product_attribute');
    var impact_price = $(this).val();
    var price = parseFloat($('.attribute-finalprice span[data-uniqid="'+id_product_attribute+'"]').data('price')) + parseFloat(impact_price);
    $('.attribute-finalprice span[data-uniqid="'+id_product_attribute+'"]').html(price.toFixed(6));
    $('.attribute_priceTE[data-id_product_attribute="'+id_product_attribute+'"]').val(impact_price);
    $('.final-price[data-uniqid="'+id_product_attribute+'"]').html(price.toFixed(6));
});
$(document).on('change','.quantity_product_attributes',function(){
    var id_product_attribute = $(this).data('id_product_attribute');
    var quantity_product_attribute = $(this).val();
    $('#combination_'+id_product_attribute+'_attribute_quantity').val(quantity_product_attribute);
});
$(document).on('change','.combinations_attribute_quantity',function(){
    var id_product_attribute = $(this).data('id_product_attribute');
    var quantity_product_attribute = $(this).val();
    $('input.quantity_product_attributes[data-id_product_attribute="'+id_product_attribute+'"]').val(quantity_product_attribute);
});
$(document).on('click','.attribute-default',function(){
    $('.attribute_default_checkbox:checked').removeAttr('checked');
    $('#combination_'+$(this).val()+'_attribute_default').attr('checked','checked');
});
$(document).on('click','#toggle-all-combinations',function(){
    if($(this).is(':checked'))
    {
        $('.js-combination').prop( "checked", true );
        $('.js-combination').parent().addClass('checked');
    }
    else
    {
        $('.js-combination').prop('checked',false);
        $('.js-combination').parent().removeClass('checked');
    }
    $('.js-bulk-combinations').text($('.js-combination:checked').length);
});
$(document).on('click','.js-combination',function(){
    if($(this).is(':checked'))
    {
        if($('.js-combination:checked').length==$('.js-combination').length)
        {
            $('#toggle-all-combinations').prop( "checked", true );
            $('#toggle-all-combinations').parent().addClass('checked');
        }
    }
    else
    {
        $('#toggle-all-combinations').prop('checked',false);
        $('#toggle-all-combinations').parent().removeClass('checked');
    }
    $('.js-bulk-combinations').text($('.js-combination:checked').length);
});
$(document).on('click','#combinations-bulk-form .ets-pmn-bulk-action-form-attribute',function(){
    ;
    $(this).toggleClass('active').parents('#combinations-bulk-form').find('#bulk-combinations-container').toggle();
});
$(document).on('click','#delete-combinations',function(e){
    e.preventDefault();
    var id_product = $('.ets_product_popup input[name="id_product"]').val();
    var $this = $(this);
    $('.ets_pmn_errors').html('');
    if(!$(this).hasClass('loading'))
    {
        if($('.js-combination:checked').length==0)
            alert('attribute null');
        else
        {
            if(confirm(delete_item_comfirm))
            {
                $(this).addClass('loading');
                $.ajax({
                    url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
                    data: $('.ets_pmn_combination_left :input').serialize()+'&submitDeletecombinations=1&id_product='+$('.ets_product_popup input[name="id_product"]').val(),
                    type: 'post',
                    dataType: 'json',
                    success: function(json){
                        $this.removeClass('loading');
                        if(json.errors)
                        {
                            $('.ets_pmn_errors').html(json.errors);
                            _etsPmncloseErrors();
                        }
                        if(json.success)
                        {
                            $.growl.notice({ message: json.success });
                            $('.combinations-list').html(json.list_combinations);
                            $('tr[data-product-id="'+id_product+'"] td.column.combinations .content').html('<div class="content_info">'+json.combinations+'</div>');
                            if($('tr[data-product-id="'+id_product+'"] td.column.sav_quantity .content').length)
                                $('tr[data-product-id="'+id_product+'"] td.column.sav_quantity').html('<div class="content_info">'+json.sav_quantity+'</div>');
                            $('.js-bulk-combinations').text('0');
                            $('#js-bulk-combinations-total').text($('.js-combination').length);
                        }
                        ets_changloadmore();
                    },
                    error: function(error)
                    {
                        $this.removeClass('loading');
                    }
                });
            }
        }
    }
});
$(document).on('click','#apply-on-combinations',function(e){
    e.preventDefault();
    var $this = $(this);
    var id_product = $('.ets_product_popup input[name="id_product"]').val();
    $('.ets_pmn_errors').html('');
    if(!$(this).hasClass('loading'))
    {
        if($('.js-combination:checked').length==0)
            alert('attribute null');
        else
        {
            $(this).addClass('loading');
            $.ajax({
                url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
                data: $('.ets_pmn_combination_left :input').serialize()+'&submitSavecombinations=1&id_product='+$('.ets_product_popup input[name="id_product"]').val(),
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $this.removeClass('loading');
                    if(json.errors)
                    {
                        $('.ets_pmn_errors').html(json.errors);
                        _etsPmncloseErrors();
                    }
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('.combinations-list').html(json.list_combinations);
                        $('tr[data-product-id="'+id_product+'"] td.column.combinations .content').html('<div class="content_info">'+json.combinations+'</div>');
                        if($('tr[data-product-id="'+id_product+'"] td.column.sav_quantity .content').length)
                            $('tr[data-product-id="'+id_product+'"] td.column.sav_quantity').html('<div class="content_info">'+json.sav_quantity+'</div>');
                        $('.js-bulk-combinations').text('0');
                        $('#js-bulk-combinations-total').text($('.js-combination').length);
                        ets_changloadmore();
                    }
                },
                error: function(error)
                {
                    $this.removeClass('loading');
                }
            });
        }
    }

});
$(document).on('click','#form_step6_suppliers input[type="checkbox"]',function(){
    if($('#form_step6_suppliers input[type="checkbox"]:checked').length)
        $('#supplier_combination_collection').show();
    else
        $('#supplier_combination_collection').hide();
    var id_supplier = $(this).val();
    if($(this).is(':checked'))
    {
        $('#form_step6_default_supplier_'+id_supplier).show();
        $('#uniform-form_step6_default_supplier_'+id_supplier).show();
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data:'id_supplier='+id_supplier+'&refreshProductSupplierCombinationForm=1&id_product='+$('.ets_product_popup input[name="id_product"]').val(),
            type: 'post',
            dataType: 'json',
            success: function(json){
                if(json.html_form)
                    $('#supplier_combination_collection .row').append(json.html_form);
                ets_changloadmore();
            },
            error: function(error)
            {

            }
        });
    }
    else
    {
        $('#supplier_combination_'+id_supplier).parent().remove();
        $('#form_step6_default_supplier_'+id_supplier).hide();
    }
});
$(document).on('click','.custom-select-supplier-currency option',function(){
    $(this).closest('tr').find('.input-group-text.currency').html($(this).data('symbol'));
});
$(document).on('click','.js-options-with-attachments .product_attachments',function(){
    if($(this).is(':checked'))
        added=1;
    else
        added=0;
    var id_product = $(this).data('id_product');
    var id_attachment = $(this).data('id_attachment');
    var data = new FormData();
    data.append('id_product',id_product);
    data.append('id_attachment',id_attachment);
    data.append('added',added);
    data.append('submitAddRemoveAttachment',1);
    $.ajax({
        type: 'POST',
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        data: data,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if(response.success)
            {
                $.growl.notice({ message: response.success });
                $('tr[data-product-id="'+id_product+'"] td.column.attached_files .content').html('<div class="content_info">'+response.attached_files+'</div>');
            }
            if(response.errors)
            {
                $.growl.error({ message: response.errors });
            }
            ets_changloadmore();
        },
        error: function(response) {

        },
    });
});
$(document).on('click','#form_step6_attachment_product_add',function(){
    $('#form_step6_attachment_product').prev('.bootstrap').remove();
    var buttonSave = $('#form_step6_attachment_product_add');
    var buttonCancel = $('#form_step6_attachment_product_cancel');
    var _this = $(this);
    var data = new FormData();
    var id_product = $(this).data('product-id');
    if ($('#form_step6_attachment_product_file')[0].files[0]) {
        data.append('product_attachment_file', $('#form_step6_attachment_product_file')[0].files[0]);
    }
    data.append('product_attachment_name', $('#form_step6_attachment_product_name').val());
    data.append('product_attachment_description', $('#form_step6_attachment_product_description').val());
    data.append('submitProductAttachment',1);
    data.append('id_product',id_product);
    buttonSave.addClass('loading');
    $.ajax({
        type: 'POST',
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        data: data,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function() {
            buttonSave.prop('disabled', 'disabled');
            $('ul.text-danger').remove();
            $('*.has-danger').removeClass('has-danger');
        },
        success: function(response) {
            if (response.id) {
                var row = '<tr>\
                        <td><input type="checkbox" class="product_attachments" data-id_product="'+id_product+'" data-id_attachment="'+response.id+'" checked="checked" value="'+response.id+'"></td>\
                        <td>' + response.real_name + '</td>\
                        <td>' + response.file_name + '</td>\
                        <td>' + response.mime + '</td>\
                        </tr>';
                $('#product-attachments tbody').append(row);
                $('.js-options-no-attachments').addClass('hide');
                $('#product-attachments').removeClass('hide');
                $('#form_step6_attachment_product_file').val('');
                $('#form_step6_attachment_product_file').next('label').html('');
                $('#form_step6_attachment_product_name').val('');
                $('#form_step6_attachment_product_description').val('');
                $('tr[data-product-id="'+id_product+'"] td.column.attached_files .content').html('<div class="content_info">'+response.attached_files+'</div>')
            }
            if(response.success)
            {
                $.growl.notice({ message: response.success });
            }
            if(response.errors)
            {
                $('#form_step6_attachment_product').before(response.errors);
            }
            buttonSave.removeClass('loading');
            ets_changloadmore();
        },
        error: function(response) {
            $.each(jQuery.parseJSON(response.responseText), function(key, errors) {
                var html = '<ul class="list-unstyled text-danger">';
                $.each(errors, function(key, error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul>';

                $('#form_step6_attachment_product_' + key).parent().append(html);
                $('#form_step6_attachment_product_' + key).parent().addClass('has-danger');
            });
            buttonSave.removeClass('loading');
        },
        complete: function() {
            buttonSave.removeAttr('disabled');
            buttonSave.removeClass('loading');
        }
    });
});
$(document).on('click','input[name="available_for_order"]',function(){
    if($(this).is(':checked'))
    {
        $(this).parent().next().hide();
    }
    else
        $(this).parent().next().show();

});
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
$(document).on('click','#js-open-create-specific-price-form',function(){
    $('#specific_price_form').toggleClass('hide');
    $('#specific_price_form input[name="specific_price_sp_reduction"]').val('');
    $('#specific_price_form input[name="specific_price_from_quantity"]').val('1');
    $('#specific_price_form input[name="id_specific_price"]').val('');
    $('#specific_price_id_customer').val('');
    if ($(".datepicker input").length > 0) {
        var dateToday = new Date();
        $(".datepicker input").datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd',
            minDate: dateToday,
        });
    }
    $('#specific_price_id_customer_hide').val('');
    $('.specific_price_id_customer .customer_selected').remove();
    return false;
});
$(document).on('click','button[name="specific_price_cancel"]',function(e){
    e.preventDefault();
    $('#specific_price_form').addClass('hide');
});
$(document).on('click','button[name="specific_price_save"]',function(e){
    e.preventDefault();
    var $this = $(this);
    $('.ets_pmn_errors').html('');
    if(!$(this).hasClass('loading'))
    {
        $(this).addClass('loading');
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: $('#specific_price_form :input').serialize(),
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('button[name="specific_price_save"]').removeClass('loading');
                if(json.errors)
                {
                    $('.ets_pmn_errors').html(json.errors);
                    _etsPmncloseErrors();
                }
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    var $html_tr = '<td><span class="text-center">--</span></td>';
                    if(json.specific.id_product_attribute!=0)
                        $html_tr += '<td>'+json.specific.attribute_name+'</td>';
                    else
                        $html_tr +='<td>'+all_combinations_text+'</td>';
                    if(json.specific.id_currency!=0)
                        $html_tr +='<td>'+json.specific.currency_name+'</td>';
                    else
                        $html_tr +='<td>'+all_currencies_text+'</td>';
                    if(json.specific.id_country!=0)
                        $html_tr +='<td>'+json.specific.country_name+'</td>';
                    else
                        $html_tr +='<td>'+all_countries_text+'</td>';
                    if(json.specific.id_group!=0)
                        $html_tr +='<td>'+json.specific.group_name+'</td>';
                    else
                        $html_tr +='<td>'+all_groups_text+'</td>';
                    if(json.specific.id_customer!=0)
                        $html_tr +='<td>'+json.specific.customer_name+'</td>';
                    else
                        $html_tr +='<td>'+all_customer_text+'</td>';
                    $html_tr +='<td>'+json.specific.price_text+'</td>';
                    $html_tr +='<td>-'+json.specific.reduction+'</td>';
                    if((json.specific.from !='0000-00-00 00:00:00' && json.specific.from !='' ) || (json.specific.to!='0000-00-00 00:00:00' && json.specific.to!='' ))
                        $html_tr += '<td>'+from_text+': '+json.specific.from+'<br/>'+to_text+': '+json.specific.to+'</td>';
                    else
                        $html_tr += '<td>'+Unlimited_text+'</td>';
                    $html_tr += '<td>'+json.specific.from_quantity+'</td>';
                    $html_tr += '<td class="ets-special-edit"><a class="js-delete delete btn ets_mp_delete_specific delete pl-0 pr-0" title="Delete" href="#" data-id_specific_price="'+json.specific.id_specific_price+'"><i class="ets_svg_icon ets_svg-delete"></i>Delete</a><a class="js-edit edit btn tooltip-link delete pl-0 pr-0" title="Edit" href="#" data-id_specific_price="'+json.specific.id_specific_price+'"><i class="icon-edit ets_svg_icon"><svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg></i>Edit</a></td>';
                    if($('#specific_price-'+json.specific.id_specific_price).length==0)
                        $('#js-specific-price-list tbody').append('<tr id="specific_price-'+json.specific.id_specific_price+'">'+$html_tr+'</tr>');
                    else
                        $('#specific_price-'+json.specific.id_specific_price).html($html_tr);
                    $('#specific_price_form').addClass('hide');
                    $('tr[data-product-id="'+json.id_product+'"] td.column.specific_prices .content').html('<div class="content_info">'+json.specific_prices+'</div>');
                }
                ets_changloadmore();
            },
            error: function(error)
            {
                $('button[name="specific_price_save"]').removeClass('loading');
            }
        });
    }
});
$(document).on('click','.ets-special-edit a.edit',function(e){
    e.preventDefault();
    var id_specific_price = $(this).data('id_specific_price');
    $.ajax({
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        data: {
            id_specific_price:id_specific_price,
            getFormSpecificPrice:1,
        },
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.form_html)
            {
                $('#specific_price_form').html(json.form_html);
                $('#specific_price_form').removeClass('hide');
                if ($(".datepicker input").length > 0) {
                    var dateToday = new Date();
                    $(".datepicker input").datepicker({
                        prevText: '',
                        nextText: '',
                        dateFormat: 'yy-mm-dd',
                        minDate: dateToday,
                    });
                }
                $('#specific_price_id_customer').autocomplete($('#desc-product-arrange').attr('href').replace('&arrangeproduct=1','&searchCustomer'),{
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
                }).result(etsMPAddCustomerSpecific);
            }
            if(json.errors)
                $.growl.error({message:json.errors});
            ets_changloadmore();
        },
        error: function(error)
        {
            $('#specific_price_form').removeClass('active');
        }
    });
});
$(document).on('click','.ets_mp_delete_specific',function(e){
    if(!$(this).hasClass('active'))
    {
        if(confirm(confirm_delete_specific))
        {
            var $this = $(this);
            $this.addClass('active')
            var id_specific_price = $(this).data('id_specific_price');
            $.ajax({
                url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
                data: {
                    id_specific_price:id_specific_price,
                    submitDeleteSpecificPrice:1,
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#specific_price-'+id_specific_price).remove();
                        $('tr[data-product-id="'+json.id_product+'"] td.column.specific_prices .content').html('<div class="content_info">'+json.specific_prices+'</div>');
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                    $this.removeClass('active');
                    ets_changloadmore();
                },
                error: function(error)
                {
                    $this.removeClass('active');
                }
            });
        }
    }
    return false;
});
$(document).on('click','#add-related-product-button',function(){
    $(this).hide();
    $('#related-content').removeClass('hide');
});
$(document).on('click','.ets_pmn_edit_image',function(){
    var imageID = $(this).data('id');
    var $this = $(this);
    $.ajax({
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        data: 'getFromImageProduct=1&id_image='+imageID+'&id_product='+$('#ets_pmn_id_product').val()+'&isoLang='+$('#form_switch_language').val(),
        type: 'post',
        dataType: 'json',
        success: function(json){
            $('.ets_pmn_edit_image').removeClass('active');
            $this.addClass('active');
            $this.parents('#product-images-container').addClass('show_info');
            $('#product-images-form-container').html(json.form_image);
            $(".open-image").fancybox();
            if('#form_image_file')
            {

            }
            ets_changloadmore();
        },
        error: function(error)
        {

        }
    });
});
$(document).on('click','.ets_pmn_close_image',function(){
    $('#product-images-form-container').html('');
    $('.ets_pmn_edit_image').removeClass('active');
    $('#product-images-container').removeClass('show_info');
});
$(document).on('click','.ets_pmn_save_image',function(e){
    e.preventDefault();
    var $this = $(this);
    $('.ets_pmn_errors').html('');
    if(!$(this).hasClass('loading'))
    {
        $(this).addClass('loading');
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: $('#product-images-form-container :input').serialize()+'&id_product='+$('#ets_pmn_id_product').val()+'&submitImageProduct',
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('.ets_pmn_save_image').removeClass('loading');
                if(json.errors)
                {
                    $('.ets_pmn_errors').html(json.errors);
                    _etsPmncloseErrors();
                }
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    if(json.cover)
                    {
                        $('.ets_pmn_edit_image .iscover').remove();
                        $('.ets_pmn_edit_image[data-id="'+json.id_image+'"]').append('<div class="iscover">'+cover_text+'</div>');
                        if($('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product img').length)
                            $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product img').attr('src',json.link);
                        else
                            $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product >a:first').html('<img class="imgm img-thumbnail" src="'+json.link+'">');
                    }
                    ets_changloadmore();
                }
            },
            error: function(error)
            {
                $('.ets_pmn_save_image').removeClass('loading');
            }
        });
    }
});
$(document).on('click','.ets_pmn_delete_image',function(e){
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this);
    var id_image = $(this).data('id');
    if(!$this.hasClass('loading'))
    {
        if(confirm(delete_image_comfirm))
        {
            $this.addClass('loading');
            $.ajax({
                url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
                data: 'id_image='+id_image+'&id_product='+$('#ets_pmn_id_product').val()+'&deleteImageProduct',
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $this.removeClass('loading');
                    if(json.errors)
                    {
                        $('.ets_pmn_errors').html(json.errors);
                        _etsPmncloseErrors();
                    }
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#product-images-form-container').html('');
                        $('.ets_pmn_edit_image[data-id="'+json.id_image+'"]').remove();
                        $this.remove();
                        if($('.ets_pmn_edit_image').length==0)
                        {
                            $('#product-images-dropzone').removeClass('dz-started');
                        }
                        $('#product-images-container.show_info').removeClass('show_info');
                        if(json.is_cover)
                        {
                            if(json.id_new_image)
                            {
                                $('.ets_pmn_edit_image .iscover').remove();
                                $('.ets_pmn_edit_image[data-id="'+json.id_new_image+'"]').append('<div class="iscover">'+cover_text+'</div>');
                                if($('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product img').length)
                                    $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product img').attr('src',json.link);
                                else
                                    $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product >a:first').html('<img class="imgm img-thumbnail" src="'+json.link+'">');
                            }
                            else
                                $('#product_catalog_list tr[data-product-id="'+$('#ets_pmn_id_product').val()+'"] .column.image .popup_change_product img').remove();
                        }
                        ets_changloadmore();
                    }
                },
                error: function(error)
                {
                    $('.ets_pmn_delete_image').removeClass('loading');
                }
            });
        }

    }
});
$(document).on('click','.default-category',function(){
    if(!$(this).parent().find('.category:checked').length)
    {
        var $input_category = $(this).parent().find('.category');
        $input_category.prop('checked',true);
        var id_category = $(this).val();
        $('#ps_categoryTags').append('<span class="pstaggerTag"><span data-id="'+id_category+'" title="'+$input_category.next('.label').html()+'"> '+$input_category.next('.label').html()+'</span><a class="pstaggerClosingCross" href="#" data-id="'+id_category+'">x</a></span>')
    }
});
$(document).on('click','.pstaggerClosingCross',function(e){
    e.preventDefault();
    if($('.pstaggerClosingCross').length >1)
    {
        var id_category = $(this).data('id');
        $(this).parent().remove();
        $('.form-wrapper-edit-category .category[value="'+id_category+'"]').prop('checked', false);
        if($('.form-wrapper-edit-category .category[value="'+id_category+'"]').parent().find('.default-category:checked').length)
        {
            $('.form-wrapper-edit-category .category[value="'+id_category+'"]').parent().find('.default-category').prop('checked',false);
            if($('.category-tree .category:checked').length)
            {
                $('.category-tree .category:checked:first').parent().find('.default-category').prop('checked',true);
            }
        }
    }
});
$(document).on('click','.ets_pmn_delete_file',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loading'))
    {
        if(confirm(delete_file_comfirm))
        {
            $(this).addClass('loading');
            var $this= $(this);
            url_ajax= $(this).attr('href');
            $.ajax({
                url: url_ajax,
                data: '',
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#form_step3_virtual_product_file_details').html('').removeClass('show').addClass('hide');
                        $('#form_step3_virtual_product_file_input').addClass('show').removeClass('hide');
                        $('#form_step3_virtual_product_name').val('');
                        $('label[for="form_step3_virtual_product_file"]').html('Choose file(s)');
                        $('tr[data-product-id="'+json.id_product+'"] .column.associated_file .content').html('--');
                        ets_changloadmore();
                    }
                    else if(json.errors)
                    {
                        $this.removeClass('loading');
                        $.growl.error({ message: json.errors });
                    }
                },
                error: function(xhr, status, error)
                {
                    $this.removeClass('loading');
                }
            });
        }
    }
});
$(document).on('click','.js-ets-ept-dropdown-switch-lang .js-ets-ept-lang-item',function(){
    var isoCode = $(this).attr('data-locale');
    $('.js-ets-ept-dropdown-switch-lang .js-locale-btn').html(isoCode);
    $('#form_switch_language option[value="' + isoCode + '"]').prop('selected', true);
    $('#etsEptExtraTab .tab-content-item .js-locale-input-group .js-locale-input:not(.js-locale-' + isoCode + ')').addClass('d-none');
    $('#etsEptExtraTab .tab-content-item .js-locale-input-group .js-locale-' + isoCode).removeClass('d-none');
});
if($('input.tagify').length>0)
{
    $('input.tagify').tokenfield();
}
});
function ets_changloadmore(){
    if ( $('.ets_td').length > 0 ){
        $('.ets_pmn_tr_editting .span_change_product').removeClass('more');
        $('.ets_pmn_tr_editting .ets_td .popup_change_product, .ets_pmn_tr_editting .ets_td .span_change_product').each(function(){
            var content_height = $(this).find('.content_info').height();
            if ( content_height > 60 ){
                $(this).addClass('more');
            }
            else
                $(this).removeClass('more');
        });
    }
}
function productColumnFilterResetEts(tr)
{
    $('#filter_column_minimal_quantity').attr('sql','');
    $('#filter_column_low_stock_threshold').attr('sql','');
    $('#filter_column_priority_product').attr('sql','');
    $('input:text', tr).val('');
    $('select option:selected', tr).prop('selected', false);
    $('#filter_column_price', tr).attr('sql', '');
    $('#filter_column_sav_quantity', tr).attr('sql', '');
    $('#filter_column_id_product', tr).attr('sql', '');
    if(!$('.tbody_list_product').hasClass('search'))
        $('#product_catalog_list').submit();
}
function insertwidthvaluetable(){
    var index= 0;
    var thindex= 0;
    if($('#product_catalog_list tbody tr:first-child td:not(.no-product)').length > 0)
    {
        if ( $('#product_catalog_list tr th.has_loaded').length > 0 && $('th.has_loaded[style^=""]').length > 0 ){
            $('#product_catalog_list thead tr:first-child th').each(function(){
                var thwidth = $(this).width();
                $('#product_catalog_list tr td').eq(thindex).find('.ets_td').attr('style','min-width:'+thwidth+'px!important;max-width:'+thwidth+'px!important;width:'+thwidth+'px!important;').addClass('has_loaded');
                $('#product_catalog_list tr td').eq(thindex).attr('style','min-width:'+thwidth+'px!important;max-width:'+thwidth+'px!important;width:'+thwidth+'px!important;').addClass('has_loaded');
                $('#product_catalog_list tr th').eq(thindex).attr('style','min-width:'+thwidth+'px!important;max-width:'+thwidth+'px!important;width:'+thwidth+'px!important;').addClass('has_loaded');

                thindex++;
            });
        } else {
            $('#product_catalog_list tbody tr:first-child td:not(.no-product)').each(function(){
                var $width = $(this).width();
                $('#product_catalog_list tr.column-headers th').eq(index).attr('style','min-width:'+$width+'px!important;max-width:'+$width+'px!important;width:'+$width+'px!important;').addClass('has_loaded');
                $('#product_catalog_list tr.column-filters th').eq(index).attr('style','min-width:'+$width+'px!important;max-width:'+$width+'px!important;width:'+$width+'px!important;').addClass('has_loaded');
                index++;
            });
        }

    }
}
function etsEditInlineProductAction($this)
{
    var id_product = $this.closest('tr').data('product-id');
    $this.addClass('loading');
    if($('tr[data-product-id="'+id_product+'"] input[name="price"]').length && $('tr[data-product-id="'+id_product+'"] input[name="price"]').val())
    {
        var price_excl = parseFloat($('tr[data-product-id="'+id_product+'"] input[name="price"]').val());
        $('tr[data-product-id="'+id_product+'"] input[name="price"]').val(price_excl.toFixed(6));
    }
    if($('tr[data-product-id="'+id_product+'"] input[name="price_final"]').length && $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val())
    {
        var price_final = parseFloat($('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val());
        $('tr[data-product-id="'+id_product+'"] input[name="price_final"]').val(price_final.toFixed(6));
    }
    if($('.product-edit-popup').length)
    {
        $('.product-edit-popup').html('<i class="ets_svg_icon">\n' +
            '                                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>\n' +
            '                                            </i>');
    }
    if($('tr[data-product-id="'+id_product+'"] .has_input').length && !$('tr[data-product-id="'+id_product+'"] .wapper-change-product').length)
    {
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: 'getFormEditInlineProduct=1&id_product='+id_product,
            type: 'post',
            dataType: 'json',
            success: function(jsonData){
                if(jsonData.error)
                    showErrorMessage(jsonData.error);
                if(jsonData.inputs)
                {
                    $(jsonData.inputs).each(function(){
                        if($('tr[data-product-id="'+id_product+'"] td.column.'+this.name +' .wapper-change-product').length)
                        {
                            $('tr[data-product-id="'+id_product+'"] td.column.'+this.name +' .wapper-change-product').replaceWith(this.form_html);
                        }
                        else
                        {
                            $('tr[data-product-id="'+id_product+'"] td.column.'+this.name +' .span_change_product').after(this.form_html);
                        }
                    });
                    if($('.ets_pmn_autoload_rte_runing').length)
                    {
                        tinySetup({
                            editor_selector :"ets_pmn_autoload_rte_runing"
                        });
                        $('.ets_pmn_autoload_rte_runing').removeClass('ets_pmn_autoload_rte_runing');
                    }
                }
                $this.removeClass('loading');
                $this.parents('tr').addClass('ets_pmn_tr_editting').find('.span_change_product').hide();
                $this.parents('tr').find('.wapper-change-product').show();
                $this.parents('tr').find('.wapper-change-product.popup').prev('.span_change_product').addClass('edit').show();
                $('tr[data-product-id="'+id_product+'"] .wapper-change-product .translatable-field .dropdown-toggle.disabled').removeClass('disabled');
                $this.parents('.span_change_product').next('.group-action-update-product').show(0);
                setTimeout(function(){
                    ets_resizeColumn();
                    var width_child = $('.table-responsive >table').width();
                    var width_parent = $('.table-responsive').width();
                    if(width_child > width_parent)
                        $('.scroll_tabheader').addClass('show');
                    $('.scroll_tabheader .scroll_tabheader_bar').css('width',width_child+'px');
                    $('.scroll_tabheader').css('width',width_parent+'px');
                },500);
                if($('tr[data-product-id="'+id_product+'"] .ets_seo_meta_code').length >0)
                {
                    $('tr[data-product-id="'+id_product+'"] input[name*="meta_title_"],tr[data-product-id="'+id_product+'"] textarea[name*="meta_description_"]').each(function (_i, el) {
                        var isSnippet = $(this).attr('name').indexOf('ets_seo_') < 0 ? false : true;
                        var meta_codes = $(this).attr('name').indexOf('title') < 0 ? etsSeoAdmin.getMetaCodeTemplate(false, isSnippet) : etsSeoAdmin.getMetaCodeTemplate(true, isSnippet);
                        if($(this).closest('.input-group').length && $(this).closest('.input-group').next('.ets_seo_meta_code').length==0)
                        {
                            $(this).closest('.input-group').after(meta_codes);
                        }
                        else{
                            if($(this).next('.ets_seo_meta_code').length==0)
                            {
                                $(this).after(meta_codes);
                            }

                        }
                    });
                    etsSEO.changePlaceholderMeta();
                    setTimeout(function(){etsSEO.disableMetaInputs(id_product);},1500);

                }
                if($('input.tagify').length>0)
                {
                    $('input.tagify').tokenfield();
                }
                $('tr[data-product-id="'+id_product+'"] .span_change_product.edit .text-center').remove();
                ets_changloadmore();
            },
            error: function(xhr, status, error)
            {
                $this.addClass('loading');
            }

        });
    }
    else
    {
        $this.parents('.span_change_product').next('.group-action-update-product').show(0);
        $this.removeClass('loading');
        $this.parents('tr').addClass('ets_pmn_tr_editting').find('.span_change_product').hide();
        $this.parents('tr').find('.wapper-change-product').show();
        $this.parents('tr').find('.wapper-change-product.popup').prev('.span_change_product').addClass('edit').show();
        ets_resizeColumn();
        $('tr[data-product-id="'+id_product+'"] .span_change_product.edit .text-center').remove();

    }
    ets_changloadmore();
}
function hideOtherLanguageInline(id_product,id_lang)
{
    $('tr[data-product-id="'+id_product+'"] .translatable-field').hide();
    $('tr[data-product-id="'+id_product+'"] .translatable-field.lang-'+id_lang).show();
    $('.dropdown-menu.show').removeClass('show');
    $('tr[data-product-id="'+id_product+'"] .span_change_product').attr('data-lang',id_lang);
}
function hideOtherLanguagePopup(id)
{
    $('.ets_product_popup .translatable-field').hide();
    $('.ets_product_popup .translatable-field.lang-'+id).show();
    //$('.translatable-field .dropdown-menu').hide();
    id_lang_current = id;
}
function hideOtherLanguageImage(id,iso_code)
{
    $('.translatable-field').hide();
    $('.translatable-field.lang-'+id).show();
    $('#form_switch_language').val(iso_code).change();
}
function unitProductActionEts(element,action)
{
    var id_product = $(element).parents('tr').data('product-id');
    $.ajax({
        url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
        data: 'unitProductAction=1&id_product='+id_product+'&action_field='+action+'&value_field='+($(element).find('.action-disabled').length ? 1 :0),
        type: 'post',
        dataType: 'json',
        success: function(jsonData){
            if(jsonData.success)
            {
                if($(element).find('.action-disabled').length)
                    $(element).html('<i class="material-icons action-enabled ">check</i>');
                else
                    $(element).html('<i class="material-icons action-disabled">clear</i>');
                $.growl.notice({ message: jsonData.success });
            }
            if(jsonData.errors)
                $.growl.error({ message: jsonData.errors });
        },
        error: function(xhr, status, error)
        {
        }
    });
}
function etsGetFormPopupProduct(element,field)
{
    var id_product = $(element).parents('tr').data('product-id');
    if(!$(element).hasClass('loading'))
    {
        $(element).addClass('loading');
        $('body').addClass('loading');
        $.ajax({
            url: $('#desc-product-arrange').attr('href').replace('&arrangeproduct=1',''),
            data: 'etsGetFormPopupProduct=1&id_product='+id_product+'&field='+field,
            type: 'post',
            dataType: 'json',
            success: function(jsonData){
                if(jsonData.html_form)
                {
                    $('.ets_product_popup').addClass('show');
                    $('#block-form-popup-dublicate').html(jsonData.html_form);
                    if ($(".datepicker input").length > 0) {
                        var dateToday = new Date();
                        $(".datepicker input").datepicker({
                            prevText: '',
                            nextText: '',
                            dateFormat: 'yy-mm-dd',
                            minDate: dateToday,
                        });
                    }
                    if($('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').length)
                    {
                        if($('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').hasClass('autoload_rte'))
                        {
                            var $id = $('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').attr('id');
                            setTimeout(function(){
                                tinyMCE.get($id).focus();
                                tinyMCE.get($id).selection.select(tinyMCE.get($id).getBody(), true);
                                tinyMCE.get($id).selection.collapse(false);
                            },1000);

                        }
                        else
                        {
                            var val_text = $('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').val();
                            $('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').val('').focus();
                            $('.js-ets-ept-edit-global-content .is_lang_default.extra_tab').val(val_text);
                        }
                    }
                }
                $(element).removeClass('loading');
                $('body').removeClass('loading');
            },
            error: function(xhr, status, error)
            {
                $(element).removeClass('loading');
                $('body').removeClass('loading');
            }
        });
    }
}
function ets_pmnChangeTinymceInput(id_product)
{
    if($('tr[data-product-id="'+id_product+'"] .is_lang_default').length)
    {
        $('tr[data-product-id="'+id_product+'"] .is_lang_default').each(function(){

            var text_value = $(this).val().replace(/(<([^>]+)>)/gi, "");
            if(ets_max_lang_text && text_value.length > ets_max_lang_text)
            {
                text_value = text_value.substring(0,ets_max_lang_text)+'...';
            }
            $(this).parents('td').find('.span_change_product .content').html(text_value);
        });
    }
}
function _etsPmncloseErrors()
{
    setTimeout(function(){$('.module_error.alert-danger').parent().remove();},3000)
}
