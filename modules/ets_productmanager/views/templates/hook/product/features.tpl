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
var delete_item_comfirm = '{l s='Do you want to delete this item?' mod='ets_productmanager' js=1}';
</script>
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit feature' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div id="ets_pmn-features-content">
                {if $product_features}
                    {foreach from =$product_features item='product_feature'}
                        <div class="form-group ets_pmn-product-feature">
                            <div class="row">
                                <div class="col-lg-4 form-group ets_pmn-product-feature-col col-id_features">
                                    <label>{l s='Feature' mod='ets_productmanager'}</label>
                                    <div>
                                        <select name="id_features[]" class="id_features">
                                            <option value="0">{l s='Choose a feature' mod='ets_productmanager'}</option>
                                            {if $features}
                                                {foreach from=$features item='feature'}
                                                    <option class="id_feature" {if $product_feature.id_feature==$feature.id_feature} selected="selected"{/if} value="{$feature.id_feature|intval}">{$feature.name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 form-group ets_pmn-product-feature-col col-id_feature_values">
                                    <label>{l s='Pre-defined value' mod='ets_productmanager'}</label>
                                    <div>
                                        <select class="id_feature_values {if !$product_feature.feature_values}disabled{/if}" name="id_feature_values[]">
                                            <option value="0">{l s='Choose a value' mod='ets_productmanager'}</option>
                                            {if $features_values}
                                                {foreach from=$features_values item='feature_value'}
                                                    <option class="id_feature_value" data-id-feature="{$feature_value.id_feature|intval}" value="{$feature_value.id_feature_value|intval}"{if $product_feature.id_feature_value==$feature_value.id_feature_value && $feature_value.id_feature==$product_feature.id_feature} selected="selected"{/if}{if $feature_value.id_feature!=$product_feature.id_feature} style="display:none;"{/if} >{$feature_value.value|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 form-group ets_pmn-product-feature-col col-feature_value_custom">
                                    <label>{l s='OR Customized value' mod='ets_productmanager'}</label>
                                    <div>
                                        <input type="text" name="feature_value_custom[]" value="{if isset($product_feature.feature_value) && $product_feature.feature_value && isset($product_feature.feature_value.custom) && $product_feature.feature_value.custom==1}{$product_feature.feature_value.value|escape:'html':'UTF-8'}{/if}"/>
                                    </div>
                                </div>
                                <div class="col-lg-1 form-group ets_pmn-product-feature-col col-delete">
                                    <label>&nbsp;</label>
                                    <a class="btn tooltip-link ets_pmn-delete" title="{l s='Delete' mod='ets_productmanager'}">
                                        <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 1376v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
            {if $features}
                <div class="row">
                    <div class="col-md-4">
                        <button id="ets_pmn_add_feature_button" class="btn btn-outline-primary sensitive add" type="button">

                            <svg class="w_14 h_14" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>

                        {l s='Add a feature' mod='ets_productmanager'}
                        </button>
                    </div>
                </div>
            {/if}
        </div>
        {if $features}
            <div class="panel-footer">
                <button type="button" name="btnCancel" class="btn btn-default pull-left">
                <i class="process-icon-cancel svg_process-icon">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
    
                </i> {l s='Cancel' mod='ets_productmanager'}</button>
                <input type="hidden" name="id_product" value="{$id_product|intval}" />
                <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitFeatureProduct">
                <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                    {l s='Save' mod='ets_productmanager'}
                </button>
            </div>
        {/if}
    </div>
</form>
{if $features}
    <div class="row">
        <div id="ets_pmn-feature-add-content" style="display:none;">
            <div class="form-group ets_pmn-product-feature">
                <div class="row">
                    <div class="col-lg-4 form-group ets_pmn-product-feature-col col-id_features">
                        <label>{l s='Feature' mod='ets_productmanager'}</label>
                        <div>
                            <select name="id_features[]" class="id_features">
                                <option value="0">{l s='Choose a feature' mod='ets_productmanager'}</option>
                                {if $features}
                                    {foreach from=$features item='feature'}
                                        <option class="id_feature" value="{$feature.id_feature|intval}">{$feature.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 form-group ets_pmn-product-feature-col col-id_feature_values">
                        <label>{l s='Pre-defined value' mod='ets_productmanager'}</label>
                        <div>
                            <select class="id_feature_values" name="id_feature_values[]">
                                <option value="0">{l s='Choose a value' mod='ets_productmanager'}</option>
                                {if $features_values}
                                    {foreach from=$features_values item='feature_value'}
                                        <option class="id_feature_value" data-id-feature="{$feature_value.id_feature|intval}" value="{$feature_value.id_feature_value|intval}" style="display:none;">{$feature_value.value|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 form-group ets_pmn-product-feature-col col-feature_value_custom">
                        <label class="">{l s='OR Customized value' mod='ets_productmanager'}</label>
                        <div>
                            <input type="text" name="feature_value_custom[]" value=""/>
                        </div>
                    </div>
                    <div class="col-lg-1 form-group ets_pmn-product-feature-col col-delete">
                        <label>&nbsp;</label>
                        <a class="btn tooltip-link ets_pmn-delete" title="{l s='Delete' mod='ets_productmanager'}">
                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 1376v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
