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
var add_keyword_text ='{l s='Add keyword' mod='ets_productmanager' js=1}';
var delete_item_comfirm = '{l s='Do you want to delete this item?' mod='ets_productmanager' js=1}';
var Edit_text = '{l s='Edit' mod='ets_productmanager' js=1}';
var Delete_text = '{l s='Delete' mod='ets_productmanager' js=1}';
</script>
{foreach from= $fields item='field'}
    {if isset($field.title_group) && $field.title_group}
        <h2>{$field.title_group|escape:'html':'UTF-8'}</h2>
    {/if}
    <div class="row form-group{if isset($field.form_group_class)} {$field.form_group_class|escape:'html':'UTF-8'}{/if}">
        <label class="col-lg-12 form-control-label{if isset($field.required) && $field.required} required{/if}" for="{$field.name|escape:'html':'UTF-8'}">
            <span class="form-control-label_content">{$field.label|escape:'html':'UTF-8'}</span>
            {if isset($field.condition_fields) && $field.condition_fields}
                {if isset($valueFieldPost) && isset($valueFieldPost['condition_fields'][$field.name])}
                    {assign var='condition_field' value=$valueFieldPost['condition_fields'][$field.name]}
                {else}
                    {assign var='condition_field' value=false}
                {/if}
                <div class="from-group-actions">
                    <span class="actions_title">{l s='Action:' mod='ets_productmanager'}</span>
                    <div class="group-radio-edit-actions">
                        {foreach from=$field.condition_fields item='condition'}
                            <label for="condition_field_{$field.name|escape:'html':'UTF-8'}_{$condition.id|escape:'html':'UTF-8'}">
                                <input type="radio" id="condition_field_{$field.name|escape:'html':'UTF-8'}_{$condition.id|escape:'html':'UTF-8'}" name="condition_field[{$field.name|escape:'html':'UTF-8'}]" value="{$condition.id|escape:'html':'UTF-8'}" class="condition-action" data-field="{$field.name|escape:'html':'UTF-8'}"{if isset($condition_field) && $condition_field}{if $condition_field == $condition.id} checked="checked"{/if} {else} {if isset($field.condition_fields_selected) && $field.condition_fields_selected==$condition.id} checked="checked"{/if}{/if} />
                                <span class="bg_color_input"></span>
                                <span>{$condition.name|escape:'html':'UTF-8'}</span>
                            </label>
                        {/foreach}
                    </div>
                </div>
            {/if}
        </label>
        <div class="col-lg-12{if $field.type=='date'} datepicker {/if}{if isset($field.condition_fields) && $field.condition_fields} massedit-form-field {$field.name|escape:'html':'UTF-8'}{/if}" {if isset($condition_field) && $condition_field} {if $condition_field=='off'} style="display: none"{/if} {else}{if isset($field.condition_fields_selected) && $field.condition_fields_selected=='off'} style="display:none"{/if}{/if}>
            {if $field.type=='custom_form'}
                {$field.html_form nofilter}
            {/if}
            {if $field.type=='select' && $field.values}
                <select name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}" class="form-control massedit-field">
                    {foreach from=$field.values item='value'}
                        <option{if isset($valueFieldPost[$field.name]) && $value.id== $valueFieldPost[$field.name]} selected="selected"{/if} value="{$value.id|escape:'html':'UTF-8'}"{if isset($value.parent)} data-parent="{$value.parent|intval}"{/if}>{$value.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            {/if}
            {if $field.type=='radio'}
                {foreach from=$field.values item='value'}
                    <div class="radio">
                        <label class="">
                            <input class="massedit-field" id="{$field.name|escape:'html':'UTF-8'}_{$value.id|escape:'html':'UTF-8'}" name="{$field.name|escape:'html':'UTF-8'}" value="{$value.id|escape:'html':'UTF-8'}" type="radio" {if isset($valueFieldPost[$field.name]) && $value.id == $valueFieldPost[$field.name]} checked="checked"{/if}/>
                            {$value.name|escape:'html':'UTF-8'}
                        </label>
                    </div>
                {/foreach}
            {/if}
            {if $field.type=='checkbox'}
                {foreach from=$field.values item='value'}
                    <div class="checkbox">
                        <label class="">
                            <input class="massedit-field" id="{$field.name|escape:'html':'UTF-8'}_{$value.id|escape:'html':'UTF-8'}" name="{$field.name|escape:'html':'UTF-8'}[]" value="{$value.id|escape:'html':'UTF-8'}" type="checkbox" {if isset($valueFieldPost[$field.name]) && is_array($valueFieldPost[$field.name]) && (in_array($value.id,$valueFieldPost[$field.name]) || in_array('all',$valueFieldPost[$field.name]))} checked="checked"{/if}/>
                            {$value.name|escape:'html':'UTF-8'}
                        </label>
                    </div>
                {/foreach}
            {/if}
            {if $field.type=='switch'}
                {if !(isset($field.condition_fields) && $field.condition_fields)}
                    <span class="switch prestashop-switch fixed-width-lg">
            			<input name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}_on" value="1"{if isset($valueFieldPost[$field.name])&& $valueFieldPost[$field.name]==1} checked="checked"{/if} type="radio" />
            			<label for="{$field.name|escape:'html':'UTF-8'}_on" class="radioCheck">
            				<i class="color_success"></i> {l s='Yes' mod='ets_productmanager'}
            			</label>
            			<input name="{$field.name|escape:'html':'UTF-8'}" id="{$field.name|escape:'html':'UTF-8'}_off" value="0" {if isset($valueFieldPost[$field.name]) && $valueFieldPost[$field.name]==0} checked="checked"{/if} type="radio" />
            			<label for="{$field.name|escape:'html':'UTF-8'}_off" class="radioCheck">
            				<i class="color_danger"></i> {l s='No' mod='ets_productmanager'}
            			</label>
            			<a class="slide-button btn"></a>
            		</span>
                    {if isset($field.desc) && $field.desc}
                        <span class="help-block">
                            {$field.desc nofilter}
                        </span>
                    {/if}
                {/if}
            {/if}
            {if $field.type=='text' || $field.type=='date' || $field.type=='tags'}
                {if isset($field.lang) && $field.lang}
                    {if $languages && count($languages)>1}
                        <div class="form-group row">
                            {foreach from=$languages item='language'}
                                <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang!=$id_lang_default} style="display:none;"{/if}>
                                    <div class="col-lg-10">
                                        {if isset($valueFieldPost) && isset($valueFieldPost[$field.name][$language.id_lang])}
                                            {assign var='value_text' value=$valueFieldPost[$field.name][$language.id_lang]}
                                        {else}
                                            {assign var='value_text' value=''}
                                        {/if}
                                        <input{if isset($field.autocomplete) && !$field.autocomplete} autocomplete="off"{/if} class="form-control {if $field.type=='tags'} tagify{/if} massedit-field" id="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" name="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}" value="{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}"  type="text" />
                                        {if isset($field.short_codes) && $field.short_codes}
                                            <div class="ets_pmn_short_code ">
                                                {foreach from=$field.short_codes key='key' item='short_code'}
                                                    <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg_icon fill_gray fill_hover_white">
                                                        <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                                    </i> 
                                                    {$key|escape:'html':'UTF-8'}</button>
                                                {/foreach}
                                            </div>
                                        {/if}
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
                                                        <a class="hideOtherLanguage" href="#" tabindex="-1" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        {if isset($valueFieldPost) && isset($valueFieldPost[$field.name][$id_lang_default])}
                            {assign var='value_text' value=$valueFieldPost[$field.name][$id_lang_default]}
                        {else}
                            {assign var='value_text' value=''}
                        {/if}
                        <input{if isset($field.autocomplete) && !$field.autocomplete} autocomplete="off"{/if} class="form-control {if $field.type=='tags'} tagify{/if} massedit-field" name="{$field.name|escape:'html':'UTF-8'}_{$id_lang_default|intval}" value="{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}"  type="text" />
                        {if isset($field.short_codes) && $field.short_codes}
                            <div class="ets_pmn_short_code ">
                                {foreach from=$field.short_codes key='key' item='short_code'}
                                    <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                    <i class="ets_svg_icon fill_gray fill_hover_white">
                                        <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                    </i> {$key|escape:'html':'UTF-8'}</button>
                                {/foreach}
                            </div>
                        {/if}
                    {/if}
                {else}
                    {if (isset($field.suffix) && $field.suffix) || (isset($field.group_addon) && $field.group_addon)}
                        <div class="input-group">
                    {/if}
                    {if isset($field.group_addon) && $field.group_addon}
                        <div class="input-group-prepend">
                             <span class="input-group-text">
                                {$field.group_addon nofilter}
                             </span>
                        </div>
                    {/if}
                        <input{if isset($field.autocomplete) && !$field.autocomplete} autocomplete="off"{/if} class="form-control{if $field.type=='tags'} tagify{/if} massedit-field" name="{$field.name|escape:'html':'UTF-8'}" value="{if isset($valueFieldPost[$field.name])}{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}{/if}"  type="text" {if isset($field.readonly) && $field.readonly} readonly="true"{/if} />
                    {if isset($field.suffix) && $field.suffix}
                        <div class="input-group-append">
                            <span class="input-group-text suffix" data-suffix="{$field.suffix|escape:'html':'UTF-8'}">
                                {$field.suffix nofilter}
                            </span>
                        </div>
                    {/if}
                    {if (isset($field.suffix) && $field.suffix) || (isset($field.group_addon) && $field.group_addon)}
                        </div>
                    {/if}
                    {if isset($field.short_codes) && $field.short_codes}
                        <div class="ets_pmn_short_code ">
                            {foreach from=$field.short_codes key='key' item='short_code'}
                                <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                <i class="ets_svg_icon fill_gray fill_hover_white">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                </i> {$key|escape:'html':'UTF-8'}</button>
                            {/foreach}
                        </div>
                    {/if}
                {/if}
            {/if}
            {if $field.type=='textarea'}
                {if isset($field.lang) && $field.lang}
                    {if $languages && count($languages)>1}
                        <div class="form-group row">
                            {foreach from=$languages item='language'}
                                <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang!=$id_lang_default} style="display:none;"{/if}>
                                    <div class="col-lg-10">
                                        {if isset($valueFieldPost) && isset($valueFieldPost[$field.name][$language.id_lang])}
                                            {assign var='value_text' value=$valueFieldPost[$field.name][$language.id_lang]}
                                        {else}
                                            {assign var='value_text' value=''}
                                        {/if}
                                        <textarea id="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}_{time()|escape:'html':'UTF-8'}" {if isset($field.placeholder)} placeholder="{$field.placeholder|escape:'html':'UTF-8'}"{/if} class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte{/if}{if isset($field.small_text) && $field.small_text} change_length{/if} massedit-field" name="{$field.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}">{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}</textarea>
                                        {if isset($field.short_codes) && $field.short_codes}
                                            <div class="ets_pmn_short_code ">
                                                {foreach from=$field.short_codes key='key' item='short_code'}
                                                    <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg_icon fill_gray fill_hover_white">
                                                        <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                                    </i> {$key|escape:'html':'UTF-8'}</button>
                                                {/foreach}
                                            </div>
                                        {/if}
                                    </div>
                                    <div class="col-lg-2">
                                        <button class="btn btn-default dropdown-toggle" type="button" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code|escape:'html':'UTF-8'}
                                        <i class="icon-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$languages item='lang'}
                                                <li>
                                                    <a class="hideOtherLanguage" href="#" tabindex="-1" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                    {if isset($field.small_text) && $field.small_text}
                                        <small class="form-text text-muted text-left col-xs-12 maxLength ">
                                            <em>
                                                <span class="currentLength">0</span>
                                                {l s='of' mod='ets_productmanager'}
                                                <span class="currentTotalMax">{$field.max_text|intval}</span>
                                                {$field.small_text|escape:'html':'UTF-8'}
                                            </em>
                                        </small>
                                    {/if}
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        {if isset($valueFieldPost) && isset($valueFieldPost[$field.name][$id_lang_default])}
                            {assign var='value_text' value=$valueFieldPost[$field.name][$id_lang_default]}
                        {else}
                            {assign var='value_text' value=''}
                        {/if}
                        <textarea id="{$field.name|escape:'html':'UTF-8'}_{$id_lang_default|intval}_{time()|escape:'html':'UTF-8'}" {if isset($field.placeholder)} placeholder="{$field.placeholder|escape:'html':'UTF-8'}"{/if} class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte{/if}{if isset($field.small_text) && $field.small_text} change_length{/if} massedit-field" name="{$field.name|escape:'html':'UTF-8'}_{$id_lang_default|intval}">{if isset($value_text)}{$value_text|escape:'html':'UTF-8'}{/if}</textarea>
                        {if isset($field.short_codes) && $field.short_codes}
                            <div class="ets_pmn_short_code ">
                                {foreach from=$field.short_codes key='key' item='short_code'}
                                    <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                    <i class="ets_svg_icon fill_gray fill_hover_white">
                                        <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                    </i> {$key|escape:'html':'UTF-8'}</button>
                                {/foreach}
                            </div>
                        {/if}
                        {if isset($field.small_text) && $field.small_text}
                            <small class="form-text text-muted text-left col-xs-12 maxLength ">
                                <em>
                                    <span class="currentLength">0</span>
                                    {l s='of' mod='ets_productmanager'}
                                    <span class="currentTotalMax">{$field.max_text|intval}</span>
                                    {$field.small_text|escape:'html':'UTF-8'}
                                </em>
                            </small>
                        {/if}
                        
                    {/if}
                {else}
                    <textarea {if isset($field.placeholder)} placeholder="{$field.placeholder|escape:'html':'UTF-8'}"{/if} class="form-control{if isset($field.autoload_rte) && $field.autoload_rte} ets_pmn_autoload_rte{/if}{if isset($field.small_text) && $field.small_text} change_length{/if} massedit-field" name="{$field.name|escape:'html':'UTF-8'}">{if isset($valueFieldPost[$field.name])}{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}{/if}</textarea>
                    {if isset($field.small_text) && $field.small_text}
                    <small class="form-text text-muted text-left col-xs-12 maxLength ">
                        <em>
                            <span class="currentLength">0</span>
                            {l s='of' mod='ets_productmanager'}
                            <span class="currentTotalMax">{$field.max_text|intval}</span>
                            {$field.small_text|escape:'html':'UTF-8'}
                        </em>
                    </small>
                    {if isset($field.short_codes) && $field.short_codes}
                        <div class="ets_pmn_short_code ">
                            {foreach from=$field.short_codes key='key' item='short_code'}
                                <button class="btn btn-default btn-add-short-code js-ets-pmn-add-short-code" type="button" data-code="{$short_code|escape:'html':'UTF-8'}">
                                <i class="ets_svg_icon fill_gray fill_hover_white">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                </i> {$key|escape:'html':'UTF-8'}</button>
                            {/foreach}
                        </div>
                    {/if}
                {/if}
                {/if}
            {/if}
            {if $field.type=='categories'}
                {$field.categories_tree nofilter}
            {/if}
            {if $field.type=='input_group'}
                <div class="row">
                    {if $field.inputs}
                        {foreach from=$field.inputs item='input'}
                            <div class="{$input.col|escape:'html':'UTF-8'} from-group">
                                {if isset($input.label) && $input.label}
                                    <label class="form-control-label" for="">{$input.label|escape:'html':'UTF-8'}</label>
                                {/if}
                                <div>
                                    {if $input.type=='text' || $input.type=='date'}
                                        {if (isset($input.suffix) && $input.suffix) || (isset($input.group_addon) && $input.group_addon)}
                                            <div class="input-group{if $input.type=='date'} datepicker{/if}">
                                            {if isset($input.group_addon) && $input.group_addon}
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        {$input.group_addon|escape:'html':'UTF-8'}
                                                    </span>
                                                </div>
                                            {/if}
                                        {/if}
                                            <input autocomplete="off" type="text" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{if isset($valueFieldPost[$input.name])}{$valueFieldPost[$input.name]|escape:'html':'UTF-8'}{/if}" />
                                        {if isset($input.suffix) && $input.suffix}
                                            <div class="input-group-append">
                                                <span class="input-group-text">{$input.suffix nofilter}</span>
                                            </div>
                                        {/if}
                                        {if (isset($input.suffix) && $input.suffix) || (isset($input.group_addon) && $input.group_addon)}
                                             </div>
                                        {/if}
                                    {/if}
                                    {if $input.type=='select'}
                                        <select name="{$input.name|escape:'html':'UTF-8'}">
                                            {foreach from = $input.values.query item='option'}
                                                <option value="{$option[$input.values.id]|escape:'html':'UTF-8'}"{if isset($valueFieldPost[$field.name]) && $valueFieldPost[$input.name]==$option[$input.values.id]} selected="selected"{/if}>{$option[$input.values.name]|escape:'html':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            {/if}
            {if $field.type=='product_features'}
                {$field.list_features nofilter}
            {/if}
            {if $field.type=='color'}
                <input class="color" type="color" name="{$field.name|escape:'html':'UTF-8'}" value="{if isset($valueFieldPost[$field.name])}{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}{/if}" data-hex="true" />
            {/if}
            {if $field.type=='file'}
                {if isset($valueFieldPost[$field.name]) && $valueFieldPost[$field.name]}    
                    <div class="shop_logo">
                        <img class="img-thumbnail ets_pmn_shop_logo" src="{$valueFieldPost[$field.name]|escape:'html':'UTF-8'}?time={time()|escape:'html':'UTF-8'}" alt="" style="width:98px" />
                        {if isset($field.link_del) && $field.link_del}
            				<a class="btn btn-default" onclick="return confirm('{l s='Do you want to delete this image?' mod='ets_productmanager' js=1}');"  href="{$field.link_del|escape:'html':'UTF-8'}">
            					<i class="icon-trash"></i> {l s='Delete' mod='ets_productmanager'}
            				</a>
             			{/if}
                    </div>
                {/if}
                <input type="file" name="{$field.name|escape:'html':'UTF-8'}" />
                {if isset($field.desc) && $field.desc}
                    <span class="help-block">
                        {$field.desc nofilter}
                    </span>
                {/if}
            {/if}
            {if isset($field.desc) && $field.desc}
                <span class="help-block">
                    {$field.desc nofilter}
                </span>
            {/if}
        </div>
    </div>
{/foreach}