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
$(document).ready(function(){
    if($('#form_step1_related_products').length)
    {
        $('#form_step1_related_products').autocomplete($('#desc-product-arrange').attr('href').replace('&arrangeproduct=1','&searchRelatedProduct=1&disableCombination=1&id_product='+$('#module_form_submit_btn').prev('input[name="id_product"]').val()),{
    		minChars: 1,
    		autoFill: true,
    		max:20,
    		matchContains: true,
    		mustMatch:false,
    		scroll:false,
    		cacheLength:0,
    		formatItem: function(item) {
    			return (item[4] ? '<img src="'+item[4]+'" style="width:24px;"/>':'') +' - '+item[2]+' <br/> '+(item[3] ? 'REF: '+item[3]:'');
    		}
    	}).result(etsPMNAddProductRelated);
    }
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
            $html +='<i class="ets_svg_icon ets_svg-times delete delete_related"></i>';
        $html +='</div>';
        $html +='<input name="related_products[]" value="'+id_product+'" type="hidden">';
    $html +='</li>';
    $('#form_step1_related_products-data').append($html);
}