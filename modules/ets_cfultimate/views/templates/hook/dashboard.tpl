{*
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
*}
<script type="text/javascript">
    var ets_cfu_line_chart = '{$ets_cfu_line_chart|json_encode}';
    var ets_cfu_lc_labels = '{$ets_cfu_lc_labels|json_encode}';
    var ets_cfu_lc_title = "{l s='Statistic' mod='ets_cfultimate'}";
    var ets_cfu_y_max = {$y_max_value|intval};
    var text_add_to_black_list = "{l s='Add IP address to blacklist successful' js='1' mod='ets_cfultimate' }";
    var detele_log = "{l s='If you clear \"View log\", view chart will be reset. Do you want to do that?' js='1' mod='ets_cfultimate' }";
</script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}chart.js"></script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}common.js"></script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}dashboard.js"></script>
{hook h='contactFormUltimateTopBlock'}
<div class="cfu-content-block">
    <div class="row manage_form_items">
        <div class="col-xs-12 col-md-6 col-sm-12">
            <div class="panel ets-cfu-panel">
                <div class="panel-header">
                    <p class="panel-title"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1248v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm-640-1024v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm-640-1024v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68z"/></svg> {l s='Contact Management' mod='ets_cfultimate'}</p>
                </div>
                <div class="panel-content mt-15">
                    <div class="row">
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateContactForm',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_contactform.png" /><br />
                                        <h4 class="title">{l s='Contact Forms' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_message.png" />{if $ets_cfu_total_message}<span class="ets-cfu-messages">{$ets_cfu_total_message|intval}</span>{/if}<br />
                                        <h4 class="title">{l s='Messages' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateEmail',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_temp.png" /><br />
                                        <h4 class="title">{l s='Email Templates' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateImportExport',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_backup.png" /><br />
                                        <h4 class="title">{l s='Import/Export' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateIntegration',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_integration.png" />
                                        <h4 class="title">{l s='Integration' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateStatistics',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_statistics.png" />
                                        <h4 class="title">{l s='Statistics' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateIpBlacklist',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_blacklist.png" />
                                        <h4 class="title">{l s='IP & Email blacklist' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="manage_item col-xs-4 col-sm-6 col-md-3">
                            <a href="{$link->getAdminLink('AdminContactFormUltimateHelp',true)|escape:'html':'UTF-8'}">
                                <div class="ets-cfu-box">
                                    <div class="ets-cfu-box-content">
                                        <img src="{$ets_cfu_img_dir_path|escape:'html':'UTF-8'}i_help.png" />
                                        <h4 class="title">{l s='Help' mod='ets_cfultimate'}</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-sm-12">
            <div class="panel ets-cfu-panel">
                <div class="panel-header">
                    <span class="panel-title"><svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M640 896v512h-256v-512h256zm384-512v1024h-256v-1024h256zm1024 1152v128h-2048v-1536h128v1408h1920zm-640-896v768h-256v-768h256zm384-384v1152h-256v-1152h256z"/></svg> {l s='Contact Traffic' mod='ets_cfultimate'}</span>
                    {if isset($filters) && $filters}<ul class="ets_cfu_filter">
                        {foreach from=$filters item = 'filter'}
                            <li class="ets_cfu_item_filter item{$filter.id|escape:'html':'UTF-8'}{if $filter_active == $filter.id} active{/if}">
                                <a href="{$action|cat:'&filter='|cat:$filter.id|escape:'html':'UTF-8'}" title="{$filter.label|escape:'html':'UTF-8'}">{$filter.label|escape:'html':'UTF-8'}</a>
                            </li>
                        {/foreach}
                    </ul>{/if}
                </div>
                <div class="panel-content dashboar_traffix">
                    <div class="row">
                        <div class="col-md-7 col-lg-8">
                            <div class="ets_cfu_admin_chart">
                                <div class="ets_cfu_line_chart">
                                    <h4 class="ets_cfu_title_chart">{l s='Statistics' mod='ets_cfultimate'}</h4>
                                    <canvas id="ets_cfu_line_chart" style="width:100%; height: 300px;"></canvas>
                                    <a class="ets_cfu_statistics_link" href="{$ets_cfu_link->getAdminLink('AdminContactFormUltimateStatistics',true)|escape:'html':'UTF-8'}">{l s='View more' mod='ets_cfultimate'}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-4">
                            <div class="ets_cfu_dashboard-logs">
                                <h5>{l s='Last visits' mod='ets_cfultimate'}</h5>
                                {if $ets_cfu_logs}
                                    <ul>{foreach from=$ets_cfu_logs item=log}
                                        <li>
                                            <span class="browser-icon {$log.class|escape:'html':'UTF-8'}"></span> {$log.browser|escape:'html':'UTF-8'}<br>
                                            <a class="log_ip" href="https://www.infobyip.com/ip-{$log.ip|escape:'html':'UTF-8'}.html" title="{l s='View location' mod='ets_cfultimate'}" target="_blank">{$log.ip|escape:'html':'UTF-8'}</a>
                                            <span class="log_date">{$log.datetime_added|escape:'html':'UTF-8'}</span>
                                        </li>
                                    {/foreach}</ul>
                                    {if $ets_cfu_logs|count >=5}<a class="ets_cfu_statistics_link" href="{$ets_cfu_link->getAdminLink('AdminContactFormUltimateStatistics',true)|escape:'html':'UTF-8'}">{l s='View more' mod='ets_cfultimate'}</a>{/if}
                                {else}
                                    <ul>
                                        <li>{l s='No views log' mod='ets_cfultimate'}</li>
                                    </ul>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row form_dashboard-card">
        <div class="col-md-12">
            {if isset($ets_cfu_stats) && $ets_cfu_stats}{foreach from=$ets_cfu_stats item='stat'}
                <div class="dashboard-card card-{$stat.color|escape:'html':'UTF-8'}">
                    <div class="dashboard-card-content">
                        <div class="pull-left">
                            <h2 class="value1">{$stat.value1|intval} <span class="value2">{$stat.value2|escape:'html':'UTF-8'}</span></h2>
                            <span class="color">{$stat.label|escape:'html':'UTF-8'}</span>
                        </div>
                        <div class="pull-right">
                            {if $stat.icon == 'sign-in'}
                                <svg width="30" height="30" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1312 896q0 26-19 45l-544 544q-19 19-45 19t-45-19-19-45v-288h-448q-26 0-45-19t-19-45v-384q0-26 19-45t45-19h448v-288q0-26 19-45t45-19 45 19l544 544q19 19 19 45zm352-352v704q0 119-84.5 203.5t-203.5 84.5h-320q-13 0-22.5-9.5t-9.5-22.5q0-4-1-20t-.5-26.5 3-23.5 10-19.5 20.5-6.5h320q66 0 113-47t47-113v-704q0-66-47-113t-113-47h-312l-11.5-1-11.5-3-8-5.5-7-9-2-13.5q0-4-1-20t-.5-26.5 3-23.5 10-19.5 20.5-6.5h320q119 0 203.5 84.5t84.5 203.5z"/></svg>
                            {elseif $stat.icon =='share'}
                                <svg width="30" height="30" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 640q0 26-19 45l-512 512q-19 19-45 19t-45-19-19-45v-256h-224q-98 0-175.5 6t-154 21.5-133 42.5-105.5 69.5-80 101-48.5 138.5-17.5 181q0 55 5 123 0 6 2.5 23.5t2.5 26.5q0 15-8.5 25t-23.5 10q-16 0-28-17-7-9-13-22t-13.5-30-10.5-24q-127-285-127-451 0-199 53-333 162-403 875-403h224v-256q0-26 19-45t45-19 45 19l512 512q19 19 19 45z"/></svg>
                            {elseif $stat.icon =='envelope'}
                                <svg width="30" height="30" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg>
                            {elseif $stat.icon =='users'}
                                <svg width="30" height="30" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M657 896q-162 5-265 128h-134q-82 0-138-40.5t-56-118.5q0-353 124-353 6 0 43.5 21t97.5 42.5 119 21.5q67 0 133-23-5 37-5 66 0 139 81 256zm1071 637q0 120-73 189.5t-194 69.5h-874q-121 0-194-69.5t-73-189.5q0-53 3.5-103.5t14-109 26.5-108.5 43-97.5 62-81 85.5-53.5 111.5-20q10 0 43 21.5t73 48 107 48 135 21.5 135-21.5 107-48 73-48 43-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-1024-1277q0 106-75 181t-181 75-181-75-75-181 75-181 181-75 181 75 75 181zm704 384q0 159-112.5 271.5t-271.5 112.5-271.5-112.5-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5zm576 225q0 78-56 118.5t-138 40.5h-134q-103-123-265-128 81-117 81-256 0-29-5-66 66 23 133 23 59 0 119-21.5t97.5-42.5 43.5-21q124 0 124 353zm-128-609q0 106-75 181t-181 75-181-75-75-181 75-181 181-75 181 75 75 181z"/></svg>
                            {elseif $stat.icon =='th-large'}
                                <svg width="30" height="30" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M832 1024v384q0 52-38 90t-90 38h-512q-52 0-90-38t-38-90v-384q0-52 38-90t90-38h512q52 0 90 38t38 90zm0-768v384q0 52-38 90t-90 38h-512q-52 0-90-38t-38-90v-384q0-52 38-90t90-38h512q52 0 90 38t38 90zm896 768v384q0 52-38 90t-90 38h-512q-52 0-90-38t-38-90v-384q0-52 38-90t90-38h512q52 0 90 38t38 90zm0-768v384q0 52-38 90t-90 38h-512q-52 0-90-38t-38-90v-384q0-52 38-90t90-38h512q52 0 90 38t38 90z"/></svg>
                            {/if}
                        </div>
                        <div class="clearfix"></div>
                        <div class="percentage"><span class="percentage_content" style="width: {$stat.percent|escape:'html':'UTF-8'}%"></span></div>
                        <a href="{$stat.link|escape:'html':'UTF-8'}" class="dashboard-card-link">{l s='View more' mod='ets_cfultimate'} <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1413 896q0-27-18-45l-91-91-362-362q-18-18-45-18t-45 18l-91 91q-18 18-18 45t18 45l189 189h-502q-26 0-45 19t-19 45v128q0 26 19 45t45 19h502l-189 189q-19 19-19 45t19 45l91 91q18 18 45 18t45-18l362-362 91-91q18-18 18-45zm251 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></a>
                    </div>
                </div>
            {/foreach}{/if}
        </div>
    </div>
</div>

