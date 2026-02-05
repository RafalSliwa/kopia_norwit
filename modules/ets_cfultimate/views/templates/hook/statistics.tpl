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
    var text_add_to_black_list = '{l s='Add IP address to blacklist successful' js='1' mod='ets_cfultimate' }';
    var detele_log = '{l s='If you clear "View log", view chart will be reset. Do you want to do that?' js='1' mod='ets_cfultimate' }';
    var ets_cfu_x_days = '{l s='Day' mod='ets_cfultimate'}';
    var ets_cfu_x_months = '{l s='Month' mod='ets_cfultimate'}';
    var ets_cfu_x_years = '{l s='Year' mod='ets_cfultimate'}';
    var ets_cfu_y_label = '{l s='Count' mod='ets_cfultimate'}';
    var ets_cfu_lc_title = '{l s='Statistics' mod='ets_cfultimate'}';
    var ets_cfu_line_chart = '{$ets_cfu_line_chart|json_encode}';
    var ets_cfu_lc_labels = '{$ets_cfu_lc_labels|json_encode}';
    var ets_cfu_y_max = {$y_max_value|intval};
</script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}chart.js"></script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}common.js"></script>
<script type="text/javascript" src="{$ets_cfu_js_dir_path|escape:'quotes':'UTF-8'}statistics.js"></script>
{hook h='contactFormUltimateTopBlock'}
<div class="cfu-content-block">
    <div class="panel statics_form">
        <div class="panel-heading">
            <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M2048 1536v128h-2048v-1536h128v1408h1920zm-128-1248v435q0 21-19.5 29.5t-35.5-7.5l-121-121-633 633q-10 10-23 10t-23-10l-233-233-416 416-192-192 585-585q10-10 23-10t23 10l233 233 464-464-121-121q-16-16-7.5-35.5t29.5-19.5h435q14 0 23 9t9 23z"/></svg> {l s='Statistics' mod='ets_cfultimate'}
        </div>
        <div class="form-wrapper">
            <div class="ets_form_tab_header">
                <span {if $cfu_tab_ets=='chart'}class="active"{/if} data-tab="chart">{l s='Chart' mod='ets_cfultimate'}</span>
                <span {if $cfu_tab_ets=='view-log'}class="active"{/if}  data-tab="view-log">{l s='Views log' mod='ets_cfultimate'}</span>
                <span class="more_tab"></span>
            </div>
            <div class="form-group-wapper">
                <div class="ets_cfu_admin_statistic form-group form_group_contact chart">
                    <div class="ets_cfu_admin_chart">
                        <div class="ets_cfu_line_chart">
                            <canvas id="ets_cfu_line_chart" style="width:100%; height: 500px;"></canvas>
                        </div>
                    </div>
                    <div class="ets_cfu_admin_filter">
                        <form id="ets_cfu_admin_filter_chart" class="defaultForm form-horizontal"
                              action="{$action nofilter}" enctype="multipart/form-data" method="POST">
                            <div class="ets_cfu_admin_filter_chart_settings">
                                <div class="ets_cfu_admin_filter_cotactform">
                                    <label>{l s='Contact form' mod='ets_cfultimate'}</label>
                                    <select id="ets_cfu_id_contact" name="id_contact" class="form-control">
                                        <option value=""{if !$sl_contact} selected="selected"{/if}>{l s='All contact form' mod='ets_cfultimate'}</option>
                                        {foreach from=$ets_cfu_contacts item=contact}
                                            <option value="{$contact.id_contact|intval}" {if $sl_contact == $contact.id_contact} selected="selected"{/if}>{$contact.title|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ets_cfu_admin_filter_date">
                                    <label>{l s='Month' mod='ets_cfultimate'}</label>
                                    <select id="ets_cfu_months" name="ets_cfu_months" class="form-control">
                                        <option value="" {if !$sl_month} selected="selected"{/if}>{l s='All' mod='ets_cfultimate'}</option>
                                        {foreach from=$ets_cfu_months key=k item=month}
                                            <option value="{$k|intval}"{if $sl_month == $k} selected="selected"{/if}>{l s=$month mod='ets_cfultimate'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ets_cfu_admin_filter_date">
                                    <label>{l s='Year' mod='ets_cfultimate'}</label>
                                    <select id="ets_cfu_years" name="ets_cfu_years" class="form-control">
                                        <option value="" {if !$sl_year} selected="selected"{/if}>{l s='All' mod='ets_cfultimate'}</option>
                                        {foreach from=$ets_cfu_years item=year}
                                            <option value="{$year|intval}" {if $sl_year == $year} selected="selected"{/if}>{$year|intval}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="ets_cfu_admin_filter_button">
                                    <button name="etsCfuSubmitFilterChart" class="btn btn-default"
                                            type="submit">{l s='Filter' mod='ets_cfultimate'}</button>
                                    {if $ets_cfu_show_reset}
                                        <a href="{$action nofilter}"
                                           class="btn btn-default">{l s='Reset' mod='ets_cfultimate'}</a>
                                    {/if}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="ets_cfu_admin_log form-group form_group_contact view-log">
                    {if $ets_cfu_logs}
                        <table id="table-log" class="table log">
                            <thead>
                            <tr class="nodrag nodrop">
                                <th>{l s='IP address' mod='ets_cfultimate'}</th>
                                <th>{l s='Browser' mod='ets_cfultimate'}</th>
                                <th>{l s='Customer' mod='ets_cfultimate'}</th>
                                <th>{l s='Contact form' mod='ets_cfultimate'}</th>
                                <th>{l s='Date' mod='ets_cfultimate'}</th>
                                <th>{l s='Action' mod='ets_cfultimate'}</th>
                            </tr>
                            </thead>
                            <tbody id="list-ets_cfu_logs">
                            {foreach from=$ets_cfu_logs item='log'}
                                <tr>
                                    <td>{$log.ip|escape:'html':'UTF-8'}</td>
                                    <td>
                                        <span class="browser-icon {$log.class|escape:'html':'UTF-8'}"></span> {$log.browser|escape:'html':'UTF-8'}
                                    </td>
                                    <td>{if $log.id_customer}<a
                                            href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}"
                                            >{$log.firstname|escape:'html':'UTF-8'}
                                            &nbsp;{$log.lastname|escape:'html':'UTF-8'}</a>{else}--{/if}</td>
                                    <td>
                                        {if $log.enable_form_page}
                                        <a href="{Ets_CfUltimate::getLinkContactForm($log.id_contact|intval)|escape:'html':'UTF-8'}"
                                           class="dropdown-item product-edit" target="_blank"
                                           >
                                            {/if}
                                            {$log.title|escape:'html':'UTF-8'}
                                            {if $log.enable_form_page}
                                        </a>
                                        {/if}
                                    </td>
                                    <td>{$log.datetime_added|escape:'html':'UTF-8'}</td>
                                    <td class="statitics_form_action">
                                        <a class="btn btn-default view_location"
                                           href="https://www.infobyip.com/ip-{$log.ip|escape:'html':'UTF-8'}.html"
                                           target="_blank">{l s='View location' mod='ets_cfultimate'}</a>
                                        {if !$log.black_list}
                                            <a class="btn btn-default etsCfuAddToBlackList " data-ip="{$log.ip|escape:'html':'UTF-8'}" href="{$action nofilter}&etsCfuAddToBlackList={$log.ip|escape:'html':'UTF-8'}">{l s='Add to blacklist' mod='ets_cfultimate'}</a>
                                        {else}
                                            <span><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></span>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        <form action="{$action nofilter}" enctype="multipart/form-data" method="POST">
                            <input type="hidden" value="1" name="etsCfuClearLogSubmit"/>
                            <div class="ets_pagination ets_cfu_actions_footer">
                                {$ets_cfu_pagination_text nofilter}
                                <button class="clear-log btn btn-default" type="submit" name="etsCfuClearLogSubmit">{l s='Clear all view logs' mod='ets_cfultimate'}</button>
                            </div>

                        </form>
                    {else}
                        {l s='No views log' mod='ets_cfultimate'}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>