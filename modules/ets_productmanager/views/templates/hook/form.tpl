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
{if $field.name && ($field.name!='sav_quantity' || $has_attribute==false) }
    <div class="wapper-change-product {$field.type|escape:'html':'UTF-8'} {if isset($field.popup) && $field.popup} popup{/if}">
        {if isset($field.popup) && $field.popup}
        <div class="popup_content table">
            <div class="popup_content_tablecell">
                <div class="popup_content_wrap">
                    <span class="close_popup" title="Close">+</span>
                    <div id="fieldset_0" class="panel">
                        <div class="panel-heading">{$field_title|escape:'html':'UTF-8'}: {$current_product->name[$id_lang_current]|escape:'html':'UTF-8'}</div>
                        <div class="form-wrapper">
        {/if}
                            {if $field.type=='select' && $field.values}
                                <select name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}" class="form-control">
                                    {foreach from = $field.values.query item='option'}
                                        <option value="{$option[$field.values.id]|escape:'html':'UTF-8'}" {if $valueFieldPost[$field.name]== $option[$field.values.id]} selected="selected"{/if}>{$option[$field.values.name]|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                {if isset($field.desc) && $field.desc}
                                    <span class="help-block">
                                        {$field.desc nofilter}
                                    </span>
                                {/if}
                            {elseif $field.type=='text' || $field.type=='date' || $field.type=='tags'}
                                {if isset($field.lang) && $field.lang}
                                    {if count($languages) >1 }
                                        <div class="form-group row">
                                            {foreach from=$languages item='language'}
                                                <div class="translatable-field lang-{$language.id_lang|intval}"{if $language.id_lang!=$id_lang_current} style="display:none;"{/if}>
                                                    <div class="col-lg-10">
                                                        {if isset($valueFieldPost)}
                                                            {if isset($valueFieldPost[$field.name][$language.id_lang])}
                                                                {assign var='value_text' value=$valueFieldPost[$field.name][$language.id_lang]}
                                                            {else}
                                                                {assign var='value_text' value=''}
                                                            {/if}
                                                        {/if}
                                                        <input placeholder="{$row.title|escape:'html':'UTF-8'}" title="{$row.title|escape:'html':'UTF-8'}" class="form-control {if $field.type=='tags'} tagify{/if}{if isset($field.popup) && $field.popup && $language.id_lang==$id_lang_current} is_lang_default{/if}" id="{$field.name|escape:'html':'UTF-8'}_{$id_product|intval}_{$language.id_lang|intval}" name="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" value="{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}"  type="text" />
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="toggle_form">
                                                            <button class="btn btn-default dropdown-toggle" type="button" tabindex="-1" data-toggle="dropdown">
                                                                {$language.iso_code|escape:'html':'UTF-8'}
                                                                <i class="icon-caret-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                {foreach from=$languages item='lang'}
                                                                    <li>
                                                                        <a class="hideOtherLanguageInline" href="#" tabindex="-1" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a>
                                                                    </li>
                                                                {/foreach}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    {else}
                                        {if isset($valueFieldPost)}
                                            {if isset($valueFieldPost[$field.name][$id_lang_current])}
                                                {assign var='value_text' value=$valueFieldPost[$field.name][$id_lang_current]}
                                            {else}
                                                {assign var='value_text' value=''}
                                            {/if}
                                        {/if}
                                        <input class="form-control{if $field.type=='tags'} tagify{/if}{if isset($field.popup) && $field.popup} is_lang_default{/if}" name="{$field.name|escape:'html':'UTF-8'}_{$id_lang_current|intval}" value="{$value_text|escape:'html':'UTF-8'}"  type="text"  title="{$field_title|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}_{$id_product|intval}_{$id_lang_current|intval}" placeholder="{$field_title|escape:'html':'UTF-8'}"/>
                                    {/if}
                                {else}
                                    {if isset($field.suffix) && $field.suffix}
                                        <div class="input-group">
                                    {/if}
                                    <input class="form-control {if $field.type=='date'} datepicker{/if}{if isset($field.popup) && $field.popup} is_lang_default{/if}" name="{$field.name|escape:'html':'UTF-8'}" value="{if $valueFieldPost[$field.name]!='0000-00-00'}{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}{/if}"  type="text"  title="{$field_title|escape:'html':'UTF-8'}" autocomplete="off" placeholder="{$field_title|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}_{$current_product->id|intval}"/>
                                    {if isset($field.suffix) && $field.suffix}
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                {$field.suffix|escape:'html':'UTF-8'}
                                            </span>
                                        </div>
                                    {/if}
                                    {if isset($field.suffix) && $field.suffix}
                                        </div>
                                    {/if}
                                {/if}
                            {elseif $field.type=='textarea'}
                                {if isset($field.lang) && $field.lang }
                                    {if count($languages) > 1}
                                        {foreach from=$languages item='language'}
                                            <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang!=$id_lang_default} style="display:none;"{/if}>
                                                <div class="col-lg-10">
                                                    {if isset($valueFieldPost[$field.name][$language.id_lang])}
                                                        {assign var='value_text' value=$valueFieldPost[$field.name][$language.id_lang]}
                                                    {else}
                                                        {assign var='value_text' value=''}
                                                    {/if}
                                                    <textarea id="{$field.name|escape:'html':'UTF-8'}_{$id_product|intval}_{$language.id_lang|intval}" placeholder="{$row.title|escape:'html':'UTF-8'}" class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte ets_pmn_autoload_rte_runing{/if}{if isset($field.small_text) && $field.small_text} change_length{/if} {if isset($field.popup) && $field.popup && $language.id_lang==$id_lang_current} is_lang_default{/if}" name="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}">{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}</textarea>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button class="btn btn-default dropdown-toggle" type="button" tabindex="-1" data-toggle="dropdown">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                        <i class="icon-caret-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        {foreach from=$languages item='lang'}
                                                            <li>
                                                                <a class="hideOtherLanguageInline" href="#" tabindex="-1" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a>
                                                            </li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                            </div>
                                        {/foreach}
                                    {else}
                                        {if isset($valueFieldPost)}
                                            {if isset($valueFieldPost[$field.name][$id_lang_current])}
                                                {assign var='value_text' value=$valueFieldPost[$field.name][$id_lang_current]}
                                            {else}
                                                {assign var='value_text' value=''}
                                            {/if}
                                        {/if}
                                        <textarea  class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte{/if}{if isset($field.popup) && $field.popup} is_lang_default{/if}" name="{$field.name|escape:'html':'UTF-8'}_{$id_lang_current|intval}" id="{$field.name|escape:'html':'UTF-8'}_{$id_product|intval}_{$id_lang_current|intval}">{$value_text nofilter}</textarea>
                                    {/if}
                                {else}
                                    <textarea  class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte{/if} {if isset($field.popup) && $field.popup} is_lang_default{/if}" name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}_{$id_product|intval}_{$id_lang_current|intval}">{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}</textarea>
                                {/if}
                            {/if}
        {if isset($field.popup) && $field.popup}
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-default pull-left" type="button" name="btnCancel">
                                <i class="process-icon-cancel svg_process-icon">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                </i>{l s='Cancel' mod='ets_productmanager'}
                            </button>
                            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitProductChangeInLine2">
                                <i class="process-icon-save">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z">
                                    </svg>
                                </i>
                                {l s='Save' mod='ets_productmanager'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/if}
    {if $field.name=='low_stock_threshold'}
        <label class="required">
            <input id="form_step3_low_stock_alert" name="low_stock_alert" value="1" type="checkbox"{if $current_product->low_stock_alert} checked="checked"{/if} />
            {l s='Send me an email when the quantity is below or equals this level' mod='ets_productmanager'}
        </label>
    {elseif $field.name=='visibility'}
        <label>
            <input id="form_step6_display_options_available_for_order" name="available_for_order" value="1"{if $current_product->available_for_order} checked="checked"{/if} type="checkbox" />
            {l s='Available for order' mod='ets_productmanager'}
        </label>
        <label {if $current_product->available_for_order} style="display:none;"{/if}>
            <input id="form_step6_display_options_show_price" name="show_price" value="1"{if $current_product->show_price} checked="checked"{/if} type="checkbox" />
            {l s='Show price' mod='ets_productmanager'}
            </label>
        <label>
            <input id="form_step6_display_options_online_only" name="online_only" value="1"{if $current_product->online_only} checked="checked"{/if} type="checkbox" />
            {l s='Web only (not sold in your retail store)' mod='ets_productmanager'}
        </label>
    {elseif $field.name=='condition'}
            <label>
            <input id="form_step6_show_condition" name="show_condition" value="1"{if $current_product->show_condition} checked="checked"{/if} type="checkbox" />
                {l s='Display condition on product page' mod='ets_productmanager'}
            </label>
    {/if}
    </div>
{/if}