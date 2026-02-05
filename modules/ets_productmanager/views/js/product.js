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
$(document).on('click','.category',function(){
    var id_category = $(this).val();
    if($(this).is(':checked'))
    {
        if(!$('.default-category:checked').length)
            $(this).parent().find('.default-category').prop('checked',true);
        $('#ps_categoryTags').append('<span class="pstaggerTag"><span data-id="'+id_category+'" title="'+$(this).next('.label').html()+'"> '+$(this).next('.label').html()+'</span><a class="pstaggerClosingCross" href="#" data-id="'+id_category+'">x</a></span>')
    }
    else
    {
        if($(this).parent().find('.default-category:checked').length)
        {
            $(this).parent().find('.default-category').prop('checked',false);
            if($('.category-tree .category:checked').length)
            {
                $('.category-tree .category:checked:first').parent().find('.default-category').prop('checked',true);
            }
        }
        $('.pstaggerTag span[data-id="'+id_category+'"]').parent().remove();
    }
});
$(document).on('click','.category-tree .label',function(){
    $(this).parent().parent().next('.children').toggle();
    $(this).parent().toggleClass('opend');
});
$(document).on('click','#categories-tree-reduce',function(){
    $(this).hide();
    $('#categories-tree-expand').show();
    if($('.category-tree .category').length)
    {
        $('.category-tree .category').each(function(){
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
$(document).on('click','#categories-tree-expand',function(){
    $(this).hide();
    $('#categories-tree-reduce').show();
    if($('.category-tree .category').length)
    {
        $('.category-tree .category').each(function(){
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
$(document).on('change','.id_features',function(){
    var id_feature = $(this).val();
    $(this).closest('.ets_pmn-product-feature').find('.id_feature_value').hide();
    $(this).closest('.ets_pmn-product-feature').find('.id_feature_value').removeAttr('selected');
    $(this).closest('.ets_pmn-product-feature').find('.id_feature_value[value="0"]').attr('selected','selected');
    $(this).closest('.ets_pmn-product-feature').find('.id_feature_values').removeClass('disabled');
    if($(this).closest('.ets_pmn-product-feature').find('.id_feature_value[data-id-feature="'+id_feature+'"]').length)
    {
        $(this).closest('.ets_pmn-product-feature').find('.id_feature_value[data-id-feature="'+id_feature+'"]').show();
    }
    else
    {
        $(this).closest('.ets_pmn-product-feature').find('.id_feature_values').addClass('disabled');
    }
});
$(document).on('click','#ets_pmn_add_feature_button',function(e){
    $('#ets_pmn-features-content').append($('#ets_pmn-feature-add-content').html());
});
$(document).on('click','#reset_related_product',function(){
    if(confirm(delete_related_comfirm))
    {
        $('#add-related-product-button').show();
        $('#related-content').addClass('hide');
        $('#form_step1_related_products-data').html('');
        if($('.adminproductmanagermassiveedit').length)
            ets_pmn_checkFormEditAction();
    }
});
$(document).on('click','.media-body .delete_related',function(){
    if(confirm(delete_item_comfirm))
    {
        $(this).closest('.media').remove();
        if($('.adminproductmanagermassiveedit').length)
            ets_pmn_checkFormEditAction();
    }
});
$(document).on('click','.js-attribute-checkbox',function(){
    if($(this).is(':checked'))
    {
        $('#attributes-generator .tokenfield').addClass('has_attribute').append('<div class="token" data-value="'+$(this).data('value')+'"><span class="token-label" style="max-width: 713.184px;">'+$(this).data('label')+'</span><a href="#" class="ets_pmn_close_attribute" tabindex="-1"></a></div>');
    }
    else
    {
        $('#attributes-generator .tokenfield .token[data-value="'+$(this).data('value')+'"]').remove();
        if ( $('#attributes-generator .tokenfield .token').length == 0 ){
            $('#attributes-generator .tokenfield').removeClass('has_attribute');
        }
    }
});
$(document).on('click','.ets_pmn_close_attribute',function(){
    $('.js-attribute-checkbox[data-value="'+$(this).parent().data('value')+'"]').prop('checked',false);
    $(this).parent().remove();
    if($('.adminproductmanagermassiveedit').length)
        ets_pmn_checkFormEditAction();
    return false;
});
$(document).on('change','#specific_price_sp_reduction_type',function(){
    $('#specific_price_sp_reduction').prev('.input-group-prepend').html('<span class="input-group-text">'+$('#specific_price_sp_reduction_type option[value="'+$(this).val()+'"]').html()+'</span>');
    if($(this).val()=='percentage')
    {
        $('select[name="specific_price_sp_reduction_tax"]').hide();
    }
    else
        $('select[name="specific_price_sp_reduction_tax"]').show();
});
$(document).on('click','#specific_price_leave_bprice',function(){
    if($(this).is(':checked'))
        $('input[name="specific_price_product_price"]').attr('disabled','disabled');
    else
        $('input[name="specific_price_product_price"]').removeAttr('disabled');
});
if ($("input.datepicker").length > 0) {
    var dateToday = new Date();
    $("input.datepicker").removeClass('hasDatepicker').attr('autocomplete','off');
    $("input.datepicker").datepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minDate: dateToday,
    });
}
$(document).on('click', 'ul.customFieldCollection .delete', function(e) {
    e.preventDefault();
    var _this = $(this);
    if(confirm('Are you sure to delete this?'))
    {
        _this.parent().parent().parent().remove();
        if($('.adminproductmanagermassiveedit').length)
            ets_pmn_checkFormEditAction();
    }
});
$(document).on('click','.ets_addfile_customization',function(e){
    e.preventDefault();
    var collectionHolder = $('ul.customFieldCollection');
    var maxCollectionChildren = collectionHolder.children().length;
    var newForm = collectionHolder.attr('data-prototype').replace(/__name__/g, maxCollectionChildren);
    collectionHolder.append('<li>' + newForm + '</li>');
    hideOtherLanguagePopup(id_lang_current);
});