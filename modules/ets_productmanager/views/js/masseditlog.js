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
var etsPMNAddCustomerSpecific = function(event, data, formatted) {
    if (data == null)
        return false;
    $('#specific_price_id_customer_hide').val(data[0]);
    if ($('#specific_price_id_customer').next('.customer_selected').length <= 0) {
        $('#specific_price_id_customer').after('<div class="customer_selected">' + data[1] + ' <span class="delete_customer_search">delete</span><div>');
        $('#specific_price_id_customer').val(data[0]);
    }
}
$(document).ready(function() {
    if ($('.ets_td').length > 0) {
        $('.ets_td .popup_change_product, .ets_td .span_change_product:not(.more)').each(function() {
            var content_height = $(this).find('.content_info').height();
            if (content_height > 60) {
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
    $(document).on('change', '.paginator_select_limit', function(e) {
        if ($(this).attr('name') != 'paginator_matching_products_select_limit')
            $(this).parents('form').submit();
    });
    if ($('.module_confirmation.alert-success').length) {
        setTimeout(function() {
            $('.module_confirmation.alert-success').parent().remove();
        }, 3000);
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
    if ($(".ets_pmn_datepicker input").length > 0) {
        $('.hasDatepicker').removeClass('hasDatepicker');
        $(".ets_pmn_datepicker input").datepicker({
            dateFormat: 'yy-mm-dd',
        });
    }
    $(document).on('click', '.pmn_logmassedit_boxs', function() {
        ets_pmn_updateBulkMenu();
    });
});