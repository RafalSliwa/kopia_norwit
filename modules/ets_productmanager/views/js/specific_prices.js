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
});
var etsMPAddCustomerSpecific = function(event,data,formatted)
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