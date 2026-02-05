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
{hook h='contactFormUltimateTopBlock'}
<script type="text/javascript">
    {if isset($is_ps15) && $is_ps15}
    $(document).on('click', '.dropdown-toggle', function () {
        $(this).closest('.btn-group').toggleClass('open');
    });
    {/if}
</script>
<div class="cfu-content-block">
    <form id="form-message" class="form-horizontal clearfix products-catalog"
          action="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}" method="post">
        <input id="submitFilterMessage" type="hidden" value="0" name="submitFilterMessage"/>
        <input type="hidden" value="1" name="page"/>
        <input type="hidden" value="50" name="selected_pagination"/>
        <div class="panel col-lg-12">
            <div class="panel-heading">
                {l s='Messages' mod='ets_cfultimate'}
                <span class="badge">{$totalMessage|intval}</span>
            </div>
            <div class="table-responsive-row clearfix">
                <table id="table-message" class="table message">
                    <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-xs">
                                <span class="title_box">
                                    {if count($messages)}
                                        <input value="" class="message_readed_all" type="checkbox"/>
                                    {/if}
                                </span>
                        </th>
                        <th class="subject_col">
                                <span class="title_box">
                                    {l s='Subject' mod='ets_cfultimate'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.subject&OrderWay=DESC"
                                       {if $orderBy=='m.subject' && $orderWay=='DESC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.subject&OrderWay=ASC"
                                       {if $orderBy=='m.subject' && $orderWay=='ASC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg>
    								</a>
                                </span>
                        </th>
                        <th class="message_col">
                                <span class="title_box">
                                    {l s='Message' mod='ets_cfultimate'}
                                </span>
                        </th>
                        <th class="form_col">
                                <span class="title_box">
                                    {l s='Contact form' mod='ets_cfultimate'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.id_contact&OrderWay=DESC"
                                       {if $orderBy=='m.id_contact' && $orderWay=='DESC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=m.id_contact&OrderWay=ASC"
                                       {if $orderBy=='m.id_contact' && $orderWay=='ASC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg>
    								</a>
                                </span>
                        </th>
                        <th class="reply_col">
                                <span class="title_box">
                                    {l s='Replied' mod='ets_cfultimate'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=replied&OrderWay=DESC"
                                       {if $orderBy=='replied' && $orderWay=='DESC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=replied&OrderWay=ASC"
                                       {if $orderBy=='replied' && $orderWay=='ASC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg>
    								</a>
                                </span>
                        </th>
                        <th class="text-center">
                                <span class="title_box">
                                    {l s='Date' mod='ets_cfultimate'}
                                    <a href="{$url_full|escape:'html':'UTF-8'}&OrderBy=m.id_contact_message&OrderWay=DESC"
                                       {if $orderBy=='m.id_contact_message' && $orderWay=='DESC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
    								</a>
                                    <a href="{$url_full|escape:'html':'UTF-8'}&&OrderBy=m.id_contact_message&OrderWay=ASC"
                                       {if $orderBy=='m.id_contact_message' && $orderWay=='ASC'}class="active"{/if}>
    									<svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg>
    								</a>
                                </span>

                        </th>
                        <th class="text-center" style="width: 170px;">
                                <span class="title_box">
                                    {l s='Action' mod='ets_cfultimate'}
                                </span>
                        </th>
                    </tr>
                    <tr class="nodrag nodrop filter row_hover">
                        <th>

                        </th>
                        <th class="subject_col">
                            <input class="form-control" name="subject"
                                   value="{if isset($values_submit.subject)}{$values_submit.subject|escape:'html':'UTF-8'}{/if}"/>
                        </th>
                        <th class="messsage_col">
                            <input class="form-control" name="messageFilter_message"
                                   value="{if isset($values_submit.messageFilter_message)}{$values_submit.messageFilter_message|escape:'html':'UTF-8'}{/if}"/>
                        </th>
                        <th class="form_col">
                            <select class="form-control" name="id_contact" id="ets_cfu_id_contact">
                                <option value="0">---</option>{foreach from=$ets_cfu_contacts item='contact'}
                                <option value="{$contact.id_contact|intval}"
                                        {if isset($values_submit.id_contact)&&$values_submit.id_contact==$contact.id_contact}selected="selected"{/if}>{$contact.title|escape:'html':'UTF-8'|truncate:100:'...'}</option>{/foreach}
                            </select>
                        </th>
                        <th class="reply_col text-center">
                            <select id="messageFilter_replied" name="messageFilter_replied">
                                <option value="">---</option>
                                <option value="0"{if isset($values_submit.messageFilter_replied) && $values_submit.messageFilter_replied==0} selected="selected"{/if} >{l s='No' mod='ets_cfultimate'}</option>
                                <option value="1"{if isset($values_submit.messageFilter_replied) && $values_submit.messageFilter_replied==1} selected="selected"{/if}>{l s='Yes' mod='ets_cfultimate'}</option>
                            </select>
                        </th>
                        <th class="date_col">
                            <div class="date_range row">
                                <div class="input-group center">
                                    <input type="text"
                                           value="{if isset($values_submit.messageFilter_dateadd_from)}{$values_submit.messageFilter_dateadd_from|escape:'html':'UTF-8'}{/if}"
                                           placeholder="{l s='From' mod='ets_cfultimate'}"
                                           autocomplete="off"
                                           name="messageFilter_dateadd_from" id="messageFilter_dateadd_from"
                                           class="filter datepicker date-input form-control"/>
                                    <span class="input-group-addon">
											<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>
										</span>
                                </div>
                                <div class="input-group center">
                                    <input type="text"
                                           value="{if isset($values_submit.messageFilter_dateadd_to)}{$values_submit.messageFilter_dateadd_to|escape:'html':'UTF-8'}{/if}"
                                           placeholder="{l s='To' mod='ets_cfultimate'}" name="messageFilter_dateadd_to"
                                           id="messageFilter_dateadd_to"
                                           autocomplete="off"
                                           class="filter datepicker date-input form-control"/>
                                    <span class="input-group-addon">
											<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>
										</span>
                                </div>
                            </div>
                        </th>
                        <th class="action_col text-center">
                                <span class="pull-right">
                                    <button id="submitFilterButtonMessage" class="btn btn-default"
                                            name="submitFilterButtonMessage" type="submit">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                                        {l s='Search' mod='ets_cfultimate'}
                                    </button>
                                    <button id="etsCfuSubmitExportButtonMessage" name="etsCfuSubmitExportButtonMessage"
                                            class="btn btn-default" type="submit">
                                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h465l135 136q58 56 136 56t136-56l136-136h464q40 0 68 28t28 68zm-325-569q17 41-14 70l-448 448q-18 19-45 19t-45-19l-448-448q-31-29-14-70 17-39 59-39h256v-448q0-26 19-45t45-19h256q26 0 45 19t19 45v448h256q42 0 59 39z"/></svg>
                                        {l s='Export' mod='ets_cfultimate'}
                                    </button>
                                    {if isset($filter)&& $filter}
                                        <a class="btn btn-warning"
                                           href="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}">
                                            <svg width="14" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 1408l336-384h-768l-336 384h768zm1013-1077q15 34 9.5 71.5t-30.5 65.5l-896 1024q-38 44-96 44h-768q-38 0-69.5-20.5t-47.5-54.5q-15-34-9.5-71.5t30.5-65.5l896-1024q38-44 96-44h768q38 0 69.5 20.5t47.5 54.5z"/></svg>
                                            {l s='Reset' mod='ets_cfultimate'}
                                        </a>
                                    {/if}
                                </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {if $messages}
                        {foreach from=$messages item='message'}
                            <tr id="tr-message-{$message.id_contact_message|intval}"
                                class="{if !$message.readed}no-reaed{/if}">
                                {$message.row_message nofilter}
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="7">
                                <p class="alert alert-warning">{l s='No messages available' mod='ets_cfultimate'}</p>
                            </td>
                        </tr>
                    {/if}
                    </tbody>
                </table>
                <div class="ets_cfu_actions_footer">
                    <select id="bulk_action_message" name="bulk_action_message" style="display:none">
                        <option value="">{l s='Bulk actions' mod='ets_cfultimate'}</option>
                        <option value="mark_as_read">{l s='Mark as read' mod='ets_cfultimate'}</option>
                        <option value="mark_as_unread">{l s='Mark as  unread' mod='ets_cfultimate'}</option>
                        <option value="delete_selected">{l s='Delete selected' mod='ets_cfultimate'}</option>
                    </select>
                    {if $paginator_per_page}
                        <div class="paginator_per_page">
                            <label>{l s='Number of items displayed per page' mod='ets_cfultimate'}</label>
                            <select name="paginator_message_select_limit">
                                {foreach from=$paginator_per_page item='per'}
                                    <option value="{$per|intval}"{if $per==$selected_per_page} selected{/if}>{$per|intval}</option>
                                {/foreach}
                            </select>
                        </div>
                    {/if}
                    {if $messages}
                        {$ets_cfu_pagination_text nofilter}
                    {/if}
                </div>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($("table .datepicker").length > 0) {
                $("table input.datepicker").attr('autocomplete','off');
                $("table .datepicker").datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                });
            }
        });
    </script>
</div>
<div class="ctf-popup-wapper-admin">
    <div class="fuc"></div>
    <div class="ctf-popup-tablecell">
        <div class="ctf-popup-content">
            <div class="ctf_close_popup">{l s='close' mod='ets_cfultimate'}</div>
            <div id="form-message-preview">

            </div>
        </div>
    </div>
</div>