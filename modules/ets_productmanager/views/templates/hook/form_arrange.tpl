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
<form id="form_arrange" class="defaultForm form-horizontal" action="{$link_module|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="fieldset_0">											
        <div class="panel-heading"><i class="icon-cog"></i> {l s='Custom columns' mod='ets_productmanager'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <div class="col-lg-8">
                    <div class="form-group">
                        {foreach from=$title_fields key='key' item='field'}
                            {if isset($field.beggin) && $field.beggin}
                                <div class="list-group">
                                    <div class="group-title">{$field.group|escape:'html':'UTF-8'} </div>
                                    <span class="open_close_list list_open" title="{l s='Open/Close' mod='ets_productmanager'}">
                                        <svg class="up" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1395 1184q0 13-10 23l-50 50q-10 10-23 10t-23-10l-393-393-393 393q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l466 466q10 10 10 23z"/></svg>
                                        <svg class="down" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
                                    </span>
                                    <div class="list-group-content row" style="display: block;">
                                        {if isset($field.all) && $field.all}
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                                                <label class="label_all_arrange_list_product">
                                                    <input type="checkbox" class="all_arrange_list_product"/>
                                                    <i class="md-checkbox-control"></i>
                                                    <span>{l s='All' mod='ets_productmanager'}</span>
                                                </label>
                                            </div>
                                        {/if}
                            {/if}
                                        {if isset($field.title) && $field.title != ''}
                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                                                <label for="list_{$key|escape:'html':'UTF-8'}" >
                                                    <input class="arrange_list_product" type="checkbox" value="{$key|escape:'html':'UTF-8'}" id="list_{$key|escape:'html':'UTF-8'}" name="listOrders[]"{if in_array($key,$list_fields)} checked="checked"{/if} data-title="{$field.title|escape:'html':'UTF-8'}"/>
                                                    <i class="md-checkbox-control"></i>
                                                    <span>{$field.title|escape:'html':'UTF-8'}</span>
                                                </label>
                                            </div>
                                        {/if}
                            {if isset($field.end) && $field.end}
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    </div> 
                </div>
                <div class="col-lg-4">
                    <div class="form_view_select_header">
                        <label>{l s='View' mod='ets_productmanager'}: </label>
                        <div id="form_view_selected">
                            <select name="id_view_selected" id="id_view_selected">
                                {if $list_views}
                                    {foreach from=$list_views item='view'}
                                        <option data-fields="{$view.fields|escape:'html':'UTF-8'}" value="{$view.id_ets_pmn_view|intval}"{if $id_view_selected==$view.id_ets_pmn_view} selected="selected"{/if}>{$view.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <ul id="list-product-fields">
                        {if $list_fields}
                            {foreach from=$list_fields item='field'}
                                {if isset($title_fields.$field)}
                                    <li class="field_{$field|escape:'html':'UTF-8'}">
                                        <label> 

                                            <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg>


                                        <input type="hidden" name="listFieldProducts[]" value="{$field|escape:'html':'UTF-8'}"/>
                                            {assign var='title_field' value= $title_fields.$field}
                                            {$title_field.title|escape:'html':'UTF-8'}
                                        </label>
                                        <span class="close_field" data-field="{$field|escape:'html':'UTF-8'}">{l s='Close' mod='ets_productmanager'}</span>
                                    </li>
                                {/if}
                            {/foreach}
                        {/if}
                    </ul>
                    <div class="ets_group_btn_save_view">
                        <button class="btn btn-default btn_save_view" type="button">
                            {if $id_view_selected}
                                {l s='Update view' mod='ets_productmanager'}
                            {else}
                                {l s='Save view' mod='ets_productmanager'}
                            {/if}
                        </button>
                        <button class="btn btn-default btn_delete_view" type="button" style="{if $id_view_selected} display:block{else}display:none{/if}">
                            {l s='Delete view' mod='ets_productmanager'}
                        </button>
                        <button class="clear_all_fields" type="button">
                            {l s='Clear selected' mod='ets_productmanager'}
                        </button>
                    </div>
                    <div class="form-save-view">
                        <div class="ets_table">
                            <div class="ets_table-cell">
                                <div class="form-save-view-content">
                                    <div class="form-header">
                                        {l s='Save view' mod='ets_productmanager'}
                                        <span class="close_form_save_view" title="{l s='Close' mod='ets_productmanager'}">+</span>
                                    </div>
                                    <div class="form-body">
                                        <div class="form-group row">
                                            <label class="control-label col-lg-3 col-sm-3 text-right required" for="view_name">{l s='View name' mod='ets_productmanager'}</label>
                                            <div class="col-lg-9 col-sm-9">
                                                <input name="view_name" id="view_name" />
                                            </div>
                                            <label class="control-label col-lg-3 col-sm-3"></label>
                                            <div class="col-lg-9 error col-sm-9"></div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-footer">
                                        <button type="button" class="btn btn-default pull-left tbn-cancel-view" >
                                            <i class="process-icon-cancel ets_svg_process">
                                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                            </i> {l s='Cancel' mod='ets_productmanager'}
                                        </button>
                            			<button type="button" class="btn btn-default pull-right" name="btnSubmitSaveAsView">
                            				<i class="process-icon-save ets_svg_process">
                                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                                            </i> {l s='Save as' mod='ets_productmanager'}
                            			</button>
                                        <button type="button" class="btn btn-default pull-right" name="btnSubmitSaveView">
                            				<i class="process-icon-save ets_svg_process">
                                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                                            </i> {l s='Save' mod='ets_productmanager'}
                            			</button>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-left" name="btnSubmitRessetToDefaultList">
                <i class="process-icon-repeat">
                    <svg width="27" height="27" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 256v448q0 26-19 45t-45 19h-448q-42 0-59-40-17-39 14-69l138-138q-148-137-349-137-104 0-198.5 40.5t-163.5 109.5-109.5 163.5-40.5 198.5 40.5 198.5 109.5 163.5 163.5 109.5 198.5 40.5q119 0 225-52t179-147q7-10 23-12 15 0 25 9l137 138q9 8 9.5 20.5t-7.5 22.5q-109 132-264 204.5t-327 72.5q-156 0-298-61t-245-164-164-245-61-298 61-298 164-245 245-164 298-61q147 0 284.5 55.5t244.5 156.5l130-129q29-31 70-14 39 17 39 59z"/></svg>
                </i>{l s='Reset to default' mod='ets_productmanager'}
            </button>
			<button type="submit" value="1" id="btn_module_form_submit" name="btnSubmitArrangeListProduct" class="btn btn-default pull-right">
				<i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i> {l s='Save' mod='ets_productmanager'}
			</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        var $myFields = $("#list-product-fields");
        $myFields.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function () {
            },
            stop: function (event, ui) {
            }
        });
        $myFields.hover(
            function () {
                $(this).css("cursor", "move");
            },
            function () {
                $(this).css("cursor", "auto");
            }
        ); 
        {literal}
        $('.all_arrange_list_product').each(function(){
            var $list_group = $(this).closest('.list-group');
            if($list_group.find('input.arrange_list_product:checked').length == $list_group.find('input.arrange_list_product').length)
            {
                $(this).attr('checked','checked');
            }
        });
        {/literal}
    });
</script>