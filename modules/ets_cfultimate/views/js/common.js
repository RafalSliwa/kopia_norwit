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
var ets_cfu_chart = {
    chart_type : 'line',
    chart: '',
    canvas: '',
    datasets : [],
    labels : [],
    y_max_value : 0,
    title : '',
    config : function(title, canvas, datasets, labels, y_max) {
        this.title = canvas;
        this.canvas = canvas;
        this.datasets = datasets;
        this.labels = labels;
        this.y_max_value = y_max;
    },
    create : function () {
        this.chart =  new Chart(this.canvas, {
            type: this.chart_type,
            data: {
                datasets: this.datasets,
                labels: this.labels,
            },
            options: {
                element : {
                    line : {
                        cubicInterpolationMode: 'monotone',
                    }
                },
                responsive: true,
                title: {text: this.title, position : 'top',},
                scales: {
                    xAxes: [{
                        display: true,
                        stepSize: 3,
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max : this.y_max_value,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        },
                        display: true,
                    }]
                },
                legend: {fullWidth : true, position : 'bottom',},
                layout: {padding: {left: 0, right: 0, top: 0, bottom: 10}},
                tooltips: {mode: 'point', intersect: true,}
            }
        });
        return this.chart;
    },
    update : function () {
        this.chart.data.labels = this.labels;
        this.chart.data.datasets = this.datasets;
        this.chart.options.scales.yAxes = [{
            ticks: {
                min: 0,
                max : this.y_max_value,
                callback: function(value) {if (value % 1 === 0) {return value;}}
            },
        }];
        this.chart.update();
    },
};