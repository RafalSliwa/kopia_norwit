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
<div class="form-group row row_massedit">
    <input type="hidden" name="id_ets_pmn_massedit_condition[]" value="" />
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ets_pm_field_condition">
        <select class="condition_field" name="condition_field[]">
            {foreach from = $condition_fields item='condition_field'}
                <option value="{$condition_field.id|intval}"{if $condition && $condition.filtered_field ==$condition_field.id} selected="selected"{/if}>{$condition_field.name|escape:'html':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-5 ets_pm_operator">
        <div class="col massedit_operator">
            <select class="condition_operator" name="condition_operator[]">
                {foreach from=$condition_operators item='operator'}
                    <option value="{$operator.id|escape:'html':'UTF-8'}" class="{$operator.class|escape:'html':'UTF-8'}"{if $condition && $condition.operator ==$operator.id} selected="selected"{/if}>{$operator.name|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 ets_pm_operator_value">
        <div class="col massedit_operator_value">
            <div class="operator_value_text">
                {if count($languages) >1}
                    <div class="form-group">
                        <div class="col-lg-9">
                        
                {/if}
                <input name="operator_value_text[]" value="{if $condition && !is_array($condition.compared_value)}{$condition.compared_value|escape:'html':'UTF-8'}{/if}" class="form-control operator_value_text" type="text"  autocomplete="off"/>
                {if Count($languages) >1}
                        </div>
                        <div class="col-lg-3">
                            <select class="operator_value_text_lang" name="operator_value_text_lang[]">
                                {foreach from=$languages item='language'}
                                    <option value="{$language.id_lang|intval}" {if $condition && $condition.id_lang ==$language.id_lang} selected="selected"{/if}>{$language.iso_code|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}
            </div>
            {if $attributes}
                <div class="operator_value_attribute form_operator">
                    <input type="hidden" name="operator_value_attribute[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                    <select class="operator_value" multiple="">
                          {foreach from =$attributes item='attribute'}
                                <option value="{$attribute.id_attribute|intval}" {if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_ATTRIBUTE &&  in_array($attribute.id_attribute,$condition.compared_value)} selected="selected"{/if}>{$attribute.name|escape:'html':'UTF-8'}</option>
                          {/foreach}  
                    </select>
                </div>
            {/if}
            {if $features}
                <div class="operator_value_features form_operator">
                    <input type="hidden" name="operator_value_features[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                    <select class="operator_value" multiple="">
                          {foreach from =$features item='feature'}
                                <option value="{$feature.id_feature_value|intval}" {if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_FEATURES &&  in_array($feature.id_feature_value,$condition.compared_value)} selected="selected"{/if}>{$feature.name|escape:'html':'UTF-8'}</option>
                          {/foreach}  
                    </select>
                </div>
            {/if}
            {if $manufacturers}
                <div class="operator_value_brand form_operator">
                    <input type="hidden" name="operator_value_manufacturer[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                    <select class="operator_value" multiple="">
                        {foreach from=$manufacturers item='manufacturer'}
                            <option value="{$manufacturer.id_manufacturer|intval}" {if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_BRAND &&  in_array($manufacturer.id_manufacturer,$condition.compared_value)} selected="selected"{/if}>{$manufacturer.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}
            {if $suppliers}
                <div class="operator_value_supplier form_operator">
                    <input type="hidden" name="operator_value_supplier[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                    <select class="operator_value" multiple="">
                        {foreach from =$suppliers item='supplier'}
                            <option value="{$supplier.id_supplier|intval}" {if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_SUPPLIER &&  in_array($supplier.id_supplier,$condition.compared_value)} selected="selected"{/if}>{$supplier.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}
            {if $colors}
                <div class="operator_value_color form_operator">
                    <input type="hidden" name="operator_value_color[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                    <ul>
                        {foreach from=$colors item='color'}
                            <li class="float-xs-left input-container">
                                <label>
                                    <input class="input-color operator_value" value="{$color.id_attribute|intval}" type="checkbox" {if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_COLOR &&  in_array($color.id_attribute,$condition.compared_value)} checked="checked"{/if}/> 
                                    <span class="color"{if $color.color} style="background-color: {$color.color|escape:'html':'UTF-8'}"{/if}>
                                        <span class="sr-only">{$color.name|escape:'html':'UTF-8'}</span>
                                    </span>
                                </label>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            <div class="operator_value_categories form_operator">
                <input type="hidden" name="operator_value_categories[]" value="{if $condition && $condition.filtered_field== Ets_pmn_massedit_condition::FILTERED_FIELD_CATEGORIES &&  $condition.compared_value}{implode(',',$condition.compared_value)|escape:'html':'UTF-8'}{/if}" class="input_operator_value" />
                {$tree_categories nofilter}
            </div>
        </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 ets_pm_delete_action">
        <button class="btn btn-default btn-delete-filter" type="button" title="{l s='Delete filter' mod='ets_productmanager'}">
            <svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
        </button>
    </div>
</div>