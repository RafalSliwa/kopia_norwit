{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
* @copyright 2010-2024 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{if $psver==5}
    <script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.1/d3.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.css">
{/if}

<div class="panel">
    <div class="panel-heading"><i class="icon-cloud"></i> {l d='Modules.Seoredirect.stats' s='Last week redirections - statistics' mod='seoredirect'}
    </div>
    <div id="chart">
        <svg></svg>
    </div>
</div>

<div class="panel">
    <div class="panel-heading"><i class="icon-cloud"></i> {l d='Modules.Seoredirect.stats' s='Last month redirections - statistics' mod='seoredirect'}
    </div>
    <div id="chartMonth">
        <svg></svg>
    </div>
</div>

<script>
    {literal}
    var data = [{/literal}{$last_week_data}{literal}];
    nv.addGraph(function() {
        var chart = nv.models.multiBarChart()
                .margin({top: 30, right: 60, bottom: 50, left: 70})
                .reduceXTicks(true)
                .showControls(false)
                .stacked(true)
                .showLegend(false)
                .x(function(d,i) { return i })
                .y(function(d) { return d['total'] })
                .color(d3.scale.category10().range())
                ;

        chart.xAxis
                .tickFormat(function(d) {
                    var mx = data[0].values;
                    console.log(mx[d]['date']);
                    return d3.time.format('%Y-%m-%d')(new Date(mx[d]['date']))
                });
        chart.tooltipContent(function(key, y, e, graph) {
            return '<p><strong>'+y+'</strong><br/>' + key + ': ' + e + '</p>'
        });

        chart.yAxis.tickFormat(d3.format(',.0f'));
        d3.select('#chart svg')
                .datum(data)
                .transition().duration(500)
                .call(chart);
        nv.utils.windowResize(chart.update);
        return chart;
    });

    var dataMonth = [{/literal}{$last_month_data}{literal}];
    nv.addGraph(function() {
        var chart = nv.models.multiBarChart()
                .margin({top: 30, right: 60, bottom: 50, left: 70})
                .reduceXTicks(true)
                .showControls(false)
                .stacked(true)
                .showLegend(false)
                .x(function(d,i) { return i })
                .y(function(d) { return d['total'] })
                .color(d3.scale.category10().range())
                ;

        chart.xAxis
                .tickFormat(function(d) {
                    var mx = dataMonth[0].values;
                    console.log(mx[d]['date']);
                    return d3.time.format('%Y-%m-%d')(new Date(mx[d]['date']))
                });

        chart.tooltipContent(function(key, y, e, graph) {
            return '<p><strong>'+y+'</strong><br/>' + key + ': ' + e + '</p>'
        });

        chart.yAxis.tickFormat(d3.format(',.0f'));
        d3.select('#chartMonth svg')
                .datum(dataMonth)
                .transition().duration(500)
                .call(chart);
        nv.utils.windowResize(chart.update);
        return chart;
    });

    {/literal}
</script>

<style>
    #chartMonth, #chart {
        position:relativ!important;
    }
    .nvtooltip {
        position:absolute!important;
        top:40px!important;
       right:20px!important;
        width:150px!important;
        z-index:2!important;
    }

    #chart svg, #chartMonth svg {
        height: 400px;
    }
</style>