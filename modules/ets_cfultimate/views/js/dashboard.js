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
    //createLineChart();
    if ($('#ets_cfu_line_chart').length > 0 && typeof ets_cfu_line_chart !== "undefined") {
        if (typeof ets_cfu_line_chart !== 'object') {
            ets_cfu_line_chart = JSON.parse(ets_cfu_line_chart);
        }
        if (typeof ets_cfu_lc_labels !== 'object') {
            ets_cfu_lc_labels = JSON.parse(ets_cfu_lc_labels);
        }
        ets_cfu_chart.config(
            ets_cfu_lc_title,
            $('#ets_cfu_line_chart'),
            ets_cfu_line_chart,
            ets_cfu_lc_labels,
            ets_cfu_y_max
        );
        ets_cfu_chart.create();
    }
    $(document).on('click', '.ets_cfu_item_filter a', function (evt) {
        evt.preventDefault();
        var btn = $(this), li_element = btn.parents('.ets_cfu_item_filter');
        if (!btn.hasClass('active') && !li_element.hasClass('active') && btn.attr('href')) {
            btn.addClass('active');
            $.ajax({
                type : 'POST',
                dataType : 'json',
                url : btn.attr('href'),
                data : {ajax : 1},
                success : function (json) {
                    btn.removeClass('active');
                    if (json)
                    {
                        ets_cfu_chart.labels = json.ets_cfu_lc_labels;
                        ets_cfu_chart.datasets = json.ets_cfu_line_chart;
                        ets_cfu_chart.y_max_value = json.y_max_value;
                        ets_cfu_chart.update();
                        $('.ets_cfu_item_filter.active').removeClass('active');
                        li_element.addClass('active');
                    }
                },
                error : function () {
                    btn.removeClass('active');
                }
            });
        }
        return false;
    });
});