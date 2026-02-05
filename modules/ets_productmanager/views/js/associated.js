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
    if ($(".datepicker input").length > 0) {
        var dateToday = new Date();
		$(".datepicker input").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});
	}
    if($('input[name="is_virtual_file"]').length)
    {
        if($('input[name="is_virtual_file"]:checked').val()==1)
            $('#virtual_product_content').show();
        else
            $('#virtual_product_content').hide();
        $('input[name="is_virtual_file"]').click(function(){
            if($(this).val()==1)
                $('#virtual_product_content').show();
            else
                $('#virtual_product_content').hide();
        });
    }
    
});