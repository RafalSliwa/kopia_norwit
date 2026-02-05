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
    createChart();
    $(document).on('change', '#ets_cfu_years', function () {
        changeFilterDate($(this));
    });
    $(document).on('change', '#ets_cfu_months', function () {
        changeFilterDate($('#ets_cfu_years'));
    });
    $(document).on('click', '.etsCfuAddToBlackList', function () {
        var url = $(this).attr('href');
        $this = $(this);
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: url,
            dataType: "json",
            data: 'etsCfuAjax=1',
            success: function (jsonData) {
                $('#list-logs').append('<div class="ets_successfull_ajax"><span>' + text_add_to_black_list + '</span></div>');
                $('.etsCfuAddToBlackList[data-ip="' + $this.attr('data-ip') + '"]').each(function () {
                    $(this).closest('td').append('<span title="IP added to blacklist"><i class="icon icon-user-times"></i></span>');
                    $(this).remove();
                });
                setTimeout(function () {
                    $('.ets_successfull_ajax').remove();
                }, 1500);
            }
        });
        return false;
    });
    $('button[name="etsCfuClearLogSubmit"]').click(function () {
        var result = confirm(detele_log);
        if (result) {
            return true;
        }
        return false;
    });
});

function changeFilterDate(selector) {
    if (selector.length > 0) {
        if (selector.val() == '')
            $('#ets_cfu_months option[value=""]').prop('selected', true);
    }
}

function createChart() {
    if (typeof ets_cfu_line_chart !== "undefined") {
        if (typeof ets_cfu_line_chart !== 'object') {
            ets_cfu_line_chart = JSON.parse(ets_cfu_line_chart);
        }
        if (typeof ets_cfu_lc_labels !== 'object') {
            ets_cfu_lc_labels = JSON.parse(ets_cfu_lc_labels);
        }
        ets_cfu_chart.config(ets_cfu_lc_title, $('#ets_cfu_line_chart'), ets_cfu_line_chart, ets_cfu_lc_labels, ets_cfu_y_max);
        ets_cfu_chart.create();
    }
}