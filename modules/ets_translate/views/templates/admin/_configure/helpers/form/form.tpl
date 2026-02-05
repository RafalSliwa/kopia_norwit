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

{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
    {if !isset($isMultipleLanguage) || !$isMultipleLanguage}
        <div class="alert alert-warning">
            {l s='Translation is not available because your website has not supported multi-language yet.' mod='ets_translate'} <a href="{if isset($linkToConfigLang) && $linkToConfigLang}{$linkToConfigLang|escape:'quotes':'UTF-8'}{else}#{/if}">{l s='Install new language' mod='ets_translate'}</a>
        </div>
    {/if}
    {if !isset($isEnableModule) || !$isEnableModule}
        <div class="alert alert-warning">
            {l s='You must enable module "Free Translate & AI Content Generator" to configure its features.' mod='ets_translate'}
        </div>
    {/if}
    {$smarty.block.parent}
{/block}
{block name="legend"}
    <div class="panel-heading">
        {if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'quotes':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
        {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
        {$field.title|escape:'html':'UTF-8'}
        {if isset($fieldset) && isset($fieldset['form']['submit']['name']) && $fieldset['form']['submit']['name'] == 'saveEtstransSettings'}
            <a href="{if isset($linkToConfigWd)}{$linkToConfigWd|escape:'quotes':'UTF-8'}{/if}" class="btn btn-default btn-sm ets-trans-link-to-config-wd pull-right">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg>
                <span>{l s='1-Click translate' mod='ets_translate'}</span>
            </a>
        {/if}

    </div>
    {if isset($field.tabs) && count($field.tabs)}
        {assign var='tab_active' value='setting'}
        <div class="ets-trans-tabs-setting js-ets-trans-tabs-setting">
            {foreach $field.tabs as $key => $tab}
                {if $tab.active}{assign var='tab_active' value=$key}{/if}
                <div data-tab="{$key|escape:'html':'UTF-8'}" class="ets-trans-tab-item js-ets-trans-tab-item ets-trans-tab-{$key|escape:'html':'UTF-8'} {if $tab.active} active{/if}">
                    <i class="ets_icon_svg">
                        {if $key == 'setting'}<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg>{/if}

                        {if $key == 'exception'}
                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1277 1122q0-26-19-45l-181-181 181-181q19-19 19-45 0-27-19-46l-90-90q-19-19-46-19-26 0-45 19l-181 181-181-181q-19-19-45-19-27 0-46 19l-90 90q-19 19-19 46 0 26 19 45l181 181-181 181q-19 19-19 45 0 27 19 46l90 90q19 19 46 19 26 0 45-19l181-181 181 181q19 19 45 19 27 0 46-19l90-90q19-19 19-46zm387-226q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                        {/if}
                        {if $key == 'chatgpt'}
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512">
                                <rect ry="105.042" fill="#10A37F" rx="105.187" width="512" height="512"/>
                                <path fill="#fff" fill-rule="nonzero" d="M378.68 230.011a71.432 71.432 0 003.654-22.541 71.383 71.383 0 00-9.783-36.064c-12.871-22.404-36.747-36.236-62.587-36.236a72.31 72.31 0 00-15.145 1.604 71.362 71.362 0 00-53.37-23.991h-.453l-.17.001c-31.297 0-59.052 20.195-68.673 49.967a71.372 71.372 0 00-47.709 34.618 72.224 72.224 0 00-9.755 36.226 72.204 72.204 0 0018.628 48.395 71.395 71.395 0 00-3.655 22.541 71.388 71.388 0 009.783 36.064 72.187 72.187 0 0077.728 34.631 71.375 71.375 0 0053.374 23.992H271l.184-.001c31.314 0 59.06-20.196 68.681-49.995a71.384 71.384 0 0047.71-34.619 72.107 72.107 0 009.736-36.194 72.201 72.201 0 00-18.628-48.394l-.003-.004zM271.018 380.492h-.074a53.576 53.576 0 01-34.287-12.423 44.928 44.928 0 001.694-.96l57.032-32.943a9.278 9.278 0 004.688-8.06v-80.459l24.106 13.919a.859.859 0 01.469.661v66.586c-.033 29.604-24.022 53.619-53.628 53.679zm-115.329-49.257a53.563 53.563 0 01-7.196-26.798c0-3.069.268-6.146.79-9.17.424.254 1.164.706 1.695 1.011l57.032 32.943a9.289 9.289 0 009.37-.002l69.63-40.205v27.839l.001.048a.864.864 0 01-.345.691l-57.654 33.288a53.791 53.791 0 01-26.817 7.17 53.746 53.746 0 01-46.506-26.818v.003zm-15.004-124.506a53.5 53.5 0 0127.941-23.534c0 .491-.028 1.361-.028 1.965v65.887l-.001.054a9.27 9.27 0 004.681 8.053l69.63 40.199-24.105 13.919a.864.864 0 01-.813.074l-57.66-33.316a53.746 53.746 0 01-26.805-46.5 53.787 53.787 0 017.163-26.798l-.003-.003zm198.055 46.089l-69.63-40.204 24.106-13.914a.863.863 0 01.813-.074l57.659 33.288a53.71 53.71 0 0126.835 46.491c0 22.489-14.033 42.612-35.133 50.379v-67.857c.003-.025.003-.051.003-.076a9.265 9.265 0 00-4.653-8.033zm23.993-36.111a81.919 81.919 0 00-1.694-1.01l-57.032-32.944a9.31 9.31 0 00-4.684-1.266 9.31 9.31 0 00-4.684 1.266l-69.631 40.205v-27.839l-.001-.048c0-.272.129-.528.346-.691l57.654-33.26a53.696 53.696 0 0126.816-7.177c29.644 0 53.684 24.04 53.684 53.684a53.91 53.91 0 01-.774 9.077v.003zm-150.831 49.618l-24.111-13.919a.859.859 0 01-.469-.661v-66.587c.013-29.628 24.053-53.648 53.684-53.648a53.719 53.719 0 0134.349 12.426c-.434.237-1.191.655-1.694.96l-57.032 32.943a9.272 9.272 0 00-4.687 8.057v.053l-.04 80.376zm13.095-28.233l31.012-17.912 31.012 17.9v35.812l-31.012 17.901-31.012-17.901v-35.8z"/>
                            </svg>
                        {/if}</i>
                    <label>{$tab.label|escape:'html':'UTF-8'}</label>
                </div>
            {/foreach}
            <input type="hidden" class="js-ets-trans-tab-setting-input" name="ETS_TRANS_TAB_SETTING" value="{$tab_active|escape:'html':'UTF-8'}">
        </div>
    {/if}
{/block}
{block name='input_row'}
    {if $input.name == 'ETS_TRANS_KEY_PHRASE_FROM'}
        {assign var='is_enable_phrase_key' value=$fields_value['ETS_TRANS_ENABLE_KEY_PHRASE']}
        {assign var='value_text_from' value=$fields_value['ETS_TRANS_KEY_PHRASE_FROM']}
        {assign var='value_text_to' value=$fields_value['ETS_TRANS_KEY_PHRASE_TO']}
        {assign var='input_to' value=$field['ETS_TRANS_KEY_PHRASE_TO']}
        <!-- begin div ets-trans-tab-element of key phrase -->
        <div data-tab="{if isset($input.tab)}{$input.tab|escape:'html':'UTF-8'}{/if}" class="ets-trans-tab-element js-ets-trans-tab-element ets-trans-tab-element-{$input.name|escape:'html':'UTF-8'}-{$input_to.name|escape:'html':'UTF-8'} {if isset($fieldset) && isset($input.tab) && isset($fieldset['form']['legend']['tabs'][$input.tab]) && $fieldset['form']['legend']['tabs'][$input.tab]['active']}show{else}hide{/if} row">
            <label class="control-label col-lg-4"></label>
        <div class="form-group ets_trans_key_phrase js-ets_trans_key_phrase {if !$is_enable_phrase_key}hide{/if} col-lg-8"> <!-- begin ets_trans_key_phrase -->
            <div class="row ets_trans_key_phrase_row js-ets_trans_key_phrase_row ">
                {if is_array($value_text_from)}
                    {foreach $value_text_from as $key => $val}
                        <div data-key="{$key|escape:'html':'UTF-8'}" class="form-group row col-lg-12 ets_trans_key_phrase_group js-ets_trans_key_phrase_group ets_trans_key_phrase_group_{$key|escape:'html':'UTF-8'}">
                            <div class="group-input ets_trans_key_phrase_from col-lg-6">
                                {if $key == 0}<label class="ets-trans-label ets-trans-label-phrase {if isset($input.required) && $input.required}required{/if}">{$input.label|escape:'html':'UTF-8'}</label>{/if}

                                <input type="text" name="{$input.name|escape:'html':'UTF-8'}[]" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{/if}" value="{$val|escape:'html':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"/>
                            </div>
                            <div class="group-input ets_trans_key_phrase_to col-lg-6">
                                {if $key == 0}<label class="ets-trans-label ets-trans-label-phrase {if isset($input_to.required) &&  $input_to.required}required{/if}">{$input_to.label|escape:'html':'UTF-8'}</label>{/if}
                                <div class="ets_trans_key_phrase_to_group">
                                    {if $languages|count > 1}
                                    <div class="form-group row">
                                    {/if}
                                        {foreach $languages as $language}
                                            {if isset($value_text_to[$language.id_lang])}
                                                {assign var='value_text' value=$value_text_to[$language.id_lang]}
                                            {else}
                                                {assign var='value_text' value=''}
                                            {/if}
                                            {if $languages|count > 1}
                                                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                                <div class="col-lg-9">
                                            {/if}

                                            <input type="text" id="{if isset($input_to.id)}{$input_to.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{else}{$input_to.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{/if}" name="{$input_to.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}[]" class="{if isset($input_to.class)}{$input_to.class|escape:'html':'UTF-8'}{/if}" value="{if is_array($value_text)}{$value_text[$key]|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();" />
                                            {if $languages|count > 1}
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        {foreach from=$languages item=language}
                                                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                                </div>
                                            {/if}
                                        {/foreach}
                                    {if $languages|count > 1}
                                    </div>
                                    {/if}
                                    {if count($value_text_from) > 1}
                                        <button data-key="{$key|escape:'html':'UTF-8'}" class="ets_button ets-btn ets-btn-delete-phrase js-ets-btn-delete-phrase" title="{l s='Delete' mod='ets_translate'}">
                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                        </button>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {else}

                    {assign var='key' value=0}
                    <div class="form-group col-lg-12 ets_trans_key_phrase_group row js-ets_trans_key_phrase_group ets_trans_key_phrase_group_{$key|escape:'html':'UTF-8'}">
                        <div class="group-input ets_trans_key_phrase_from col-lg-6">
                            {if $key == 0}<label class="ets-trans-label ets-trans-label-phrase {if isset($input.required) &&  $input.required}required{/if}">{$input.label|escape:'html':'UTF-8'}</label>{/if}

                            <input type="text" name="{$input.name|escape:'html':'UTF-8'}[]" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{/if}" value="" class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"/>
                        </div>
                        <div class="group-input ets_trans_key_phrase_to col-lg-6">
                            {if $key == 0}<label class="ets-trans-label ets-trans-label-phrase {if isset($input_to.required) &&  $input_to.required}required{/if}">{$input_to.label|escape:'html':'UTF-8'}</label>{/if}
                            <div class="ets_trans_key_phrase_to_group">
                                {if $languages|count > 1}
                                <div class="form-group row">
                                {/if}
                                    {foreach $languages as $language}
                                        {if isset($value_text_to[$language.id_lang])}
                                            {assign var='value_text' value=$value_text_to[$language.id_lang]}
                                        {else}
                                            {assign var='value_text' value=''}
                                        {/if}
                                        {if $languages|count > 1}
                                            <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}

                                        <input type="text" id="{if isset($input_to.id)}{$input_to.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{else}{$input_to.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}{/if}" name="{$input_to.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}[]" class="{if isset($input_to.class)}{$input_to.class|escape:'html':'UTF-8'}{/if}" value="" onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();" />
                                        {if $languages|count > 1}
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                    {$language.iso_code|escape:'html':'UTF-8'}
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language}
                                                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                            </div>
                                        {/if}
                                    {/foreach}
                                {if $languages|count > 1}
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
    {elseif $input.name == 'ETS_TRANS_KEY_PHRASE_TO'}
            </div>
        <button data-length="{if is_array($value_text_from)}{count($value_text_from)|escape:'html':'UTF-8'}{else}1{/if}" class="ets_button ets-btn ets-btn-add-phrase js-ets-btn-add-phrase">{l s='Add' mod='ets_translate'}</button>
        </div><!-- end div ets_trans_key_phrase -->
        </div>
        <!-- end div ets-trans-tab-element of key phrase -->
    {elseif $input.name == 'ETS_TRANS_WD_CONFIG'}
        <div class="form-group">
            <div class="col-lg-12">
                <div class="alert alert-info">
                    {l s='Here you can quickly do bulk translation for any kind of data you want or 1-click to translate the whole website.' mod='ets_translate'}
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3"><b>{l s='Data to translate' mod='ets_translate'}: </b></label>
            <div class="col-lg-9">
                <ul class="ets-trans-tree-web-page">
                    {foreach $treeWebPageOption as $op}
                        <li>
                            <input type="checkbox" id="{$op.name|escape:'html':'UTF-8'}" name="ETS_TRANS_WD_CONFIG[]"
                                   value="{$op.name|escape:'html':'UTF-8'}"
                                   {if in_array($op.name, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if}/>
                            <label for="{$op.name|escape:'html':'UTF-8'}">{$op.title|escape:'html':'UTF-8'}</label>
                            {if isset($op.items) && $op.items}
                                <a data-toggle="collapse" class="collapsed"  href="#cp_{$op.name|escape:'html':'UTF-8'}"><i
                                            class="fa fa-angle-down"></i></a>
                                <ul class="sub-tree collapse" id="cp_{$op.name|escape:'html':'UTF-8'}">
                                    {foreach $op.items as $op2}
                                        <li>
                                            <input type="checkbox" id="{$op2.name|escape:'html':'UTF-8'}"
                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                   value="{$op2.name|escape:'html':'UTF-8'}"
                                                   {if in_array($op2.name, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if}/>
                                            <label for="{$op2.name|escape:'html':'UTF-8'}">{$op2.title|escape:'html':'UTF-8'}</label>
                                            {if isset($op2.items) && $op2.items}
                                                <a data-toggle="collapse" class="collapsed"
                                                   href="#cp_{$op2.name|escape:'html':'UTF-8'}"><i
                                                            class="fa fa-angle-down"></i></a>
                                                <ul class="sub-tree collapse" id="cp_{$op2.name|escape:'html':'UTF-8'}">
                                                    {foreach $op2.items as $op3}
                                                        <li>
                                                            <input type="checkbox"
                                                                   id="{$op3.name|escape:'html':'UTF-8'}"
                                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                                   value="{$op3.name|escape:'html':'UTF-8'}"
                                                                   {if in_array($op3.name, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if} />
                                                            <label for="{$op3.name|escape:'html':'UTF-8'}">{$op3.title|escape:'html':'UTF-8'}</label>
                                                            {if isset($op3.items) && $op3.items}
                                                                <a data-toggle="collapse" class="collapsed"
                                                                   href="#cp_{$op3.name|escape:'html':'UTF-8'}"><i
                                                                            class="fa fa-angle-down"></i></a>
                                                                <ul class="sub-tree collapse"
                                                                    id="cp_{$op3.name|escape:'html':'UTF-8'}">
                                                                    {foreach $op3.items as $op4}
                                                                        <li>
                                                                            <input type="checkbox"
                                                                                   id="{$op4.name|escape:'html':'UTF-8'}"
                                                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                                                   value="{$op4.name|escape:'html':'UTF-8'}"
                                                                                   {if in_array($op4.name, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if} />
                                                                            <label for="{$op4.name|escape:'html':'UTF-8'}">{$op4.title|escape:'html':'UTF-8'}</label>
                                                                            {if isset($op4.items) && $op4.items}
                                                                                <a data-toggle="collapse" class="collapsed"
                                                                                   href="#cp_{$op4.name|escape:'html':'UTF-8'}"><i
                                                                                            class="fa fa-angle-down"></i></a>
                                                                                <ul class="sub-tree collapse"
                                                                                    id="cp_{$op4.name|escape:'html':'UTF-8'}">
                                                                                    {foreach $op4.items as $op5}
                                                                                        <li>
                                                                                            <input type="checkbox"
                                                                                                   id="{$op5.name|escape:'html':'UTF-8'}"
                                                                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                                                                   value="{$op5.name|escape:'html':'UTF-8'}"
                                                                                                   {if in_array($op5.name, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if} />
                                                                                            <label for="{$op5.name|escape:'html':'UTF-8'}">{$op5.title|escape:'html':'UTF-8'}</label>
                                                                                            {if isset($op5.emails) && $op5.emails}
                                                                                                <a data-toggle="collapse" class="collapsed"
                                                                                                   href="#cp_{$op5.name|escape:'html':'UTF-8'}"><i
                                                                                                            class="fa fa-angle-down"></i></a>
                                                                                                <ul class="sub-tree collapse"
                                                                                                    id="cp_{$op5.name|escape:'html':'UTF-8'}">
                                                                                                    {foreach $op5.emails as $mailItem}
                                                                                                        <li>
                                                                                                            <input type="checkbox"
                                                                                                                   id="{$mailItem.val|escape:'html':'UTF-8'}"
                                                                                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                                                                                   value="{$mailItem.val|escape:'html':'UTF-8'}"
                                                                                                                   {if in_array($mailItem.val, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if} />
                                                                                                            <label for="{$mailItem.val|escape:'html':'UTF-8'}">
                                                                                                                ({if $mailItem.type == 'core_email'}{l s='Core email' mod='ets_translate'}{else}{l s='Module:' mod='ets_translate'} {$mailItem.name|escape:'html':'UTF-8'}{/if}
                                                                                                                ) {$mailItem.file|escape:'html':'UTF-8'}
                                                                                                            </label>
                                                                                                        </li>
                                                                                                    {/foreach}
                                                                                                </ul>
                                                                                            {/if}
                                                                                        </li>
                                                                                    {/foreach}
                                                                                </ul>
                                                                            {/if}
                                                                            {if isset($op4.emails) && $op4.emails}
                                                                                <a data-toggle="collapse" class="collapsed"
                                                                                   href="#em_{$op4.name|escape:'html':'UTF-8'}"><i
                                                                                            class="fa fa-angle-down"></i></a>
                                                                                <ul class="sub-tree collapse"
                                                                                    id="em_{$op4.name|escape:'html':'UTF-8'}">
                                                                                    {foreach $op4.emails as $mailItem}
                                                                                        <li>
                                                                                            <input type="checkbox"
                                                                                                   id="{$mailItem.val|escape:'html':'UTF-8'}"
                                                                                                   name="ETS_TRANS_WD_CONFIG[]"
                                                                                                   value="{$mailItem.val|escape:'html':'UTF-8'}"
                                                                                                   {if in_array($mailItem.val, $ETS_TRANS_WD_CONFIG)}checked="checked"{/if} />
                                                                                            <label for="{$mailItem.val|escape:'html':'UTF-8'}">
                                                                                                ({if $mailItem.type == 'core_email'}{l s='Core email' mod='ets_translate'}{else}{l s='Module:' mod='ets_translate'} {$mailItem.name|escape:'html':'UTF-8'}{/if}
                                                                                                ) {$mailItem.file|escape:'html':'UTF-8'}
                                                                                            </label>
                                                                                        </li>
                                                                                    {/foreach}
                                                                                </ul>
                                                                            {/if}
                                                                        </li>
                                                                    {/foreach}
                                                                </ul>
                                                            {/if}
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {elseif $input.name == 'ETS_TRANS_SUFFIX_RATE_GOOGLE' || $input.name == 'ETS_TRANS_SUFFIX_RATE_BING' || $input.name == 'ETS_TRANS_SUFFIX_RATE_DEEPL' || $input.name == 'ETS_TRANS_SUFFIX_RATE_AWS' || $input.name == 'ETS_TRANS_SUFFIX_RATE_LIBRE' || $input.name == 'ETS_TRANS_SUFFIX_RATE_LECTO' || $input.name == 'ETS_TRANS_SUFFIX_RATE_YANDEX'}
        {*Do nothing*}
    {elseif $input.name == 'ETS_TRANS_CHATGPT_TEMPLATES'}
        <div data-tab="{if isset($input.tab)}{$input.tab|escape:'html':'UTF-8'}{/if}" class="ets-trans-tab-element js-ets-trans-tab-element ets-trans-tab-element-{$input.name|escape:'html':'UTF-8'} {if isset($fieldset) && isset($input.tab) && isset($fieldset['form']['legend']['tabs'][$input.tab]) && $fieldset['form']['legend']['tabs'][$input.tab]['active']}show{else}hide{/if}">
            <div class="form-group chatgpt list-chatgpt {if isset($input.form_group_class)}{$input.form_group_class|escape:'html':'UTF-8'}{/if}">
                {$ets_translate->displayListTemplateChatGPT() nofilter}
            </div>
            <div class="ets_trans_chatgpt_template_modal hide">
                {if isset($ets_trans_box_add_template_chatgpt) && $ets_trans_box_add_template_chatgpt}{$ets_trans_box_add_template_chatgpt nofilter}{/if}
            </div>
        </div>
    {else}
        {if !isset($wrap_tab) || $wrap_tab}
            <div data-tab="{if isset($input.tab)}{$input.tab|escape:'html':'UTF-8'}{/if}" class="ets-trans-tab-element js-ets-trans-tab-element ets-trans-tab-element-{$input.name|escape:'html':'UTF-8'} {if isset($fieldset) && isset($input.tab) && isset($fieldset['form']['legend']['tabs'][$input.tab]) && $fieldset['form']['legend']['tabs'][$input.tab]['active']}show{else}hide{/if}">
                {$smarty.block.parent}
            </div>
        {else}
            {$smarty.block.parent}
        {/if}
    {/if}
{/block}
{block name='input'}
    {if $input.name == 'ETS_TRANS_SELECT_API'}
        <input type="hidden" name="ETS_TRANS_SELECT_API" id="ETS_TRANS_SELECT_API"
               value="{$fields_value['ETS_TRANS_SELECT_API']|escape:'html':'UTF-8'}"/>
        <div class="dropdown" id="dropdown_ETS_TRANS_SELECT_API">
            <button class="btn btn-default dropdown-toggle" type="button" id="btn_ETS_TRANS_SELECT_API"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="content-lang content-select-api">
                    {foreach $listApi as $op}
                        {if $op.id == $fields_value['ETS_TRANS_SELECT_API']}
                            {$op.name|escape:'html':'UTF-8'}
                        {/if}
                    {/foreach}
                </span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="btn_ETS_TRANS_SELECT_API">
                {foreach $listApi as $op}
                    <li>
                        <a href="#" class="js-ets-trans-choose-api-item"
                           data-api="{$op.id|escape:'html':'UTF-8'}">
                            {$op.name|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {elseif $input.name == 'ETS_TRANS_SELECT_AWS_REGION'}
        <input type="hidden" name="ETS_TRANS_SELECT_AWS_REGION" id="ETS_TRANS_SELECT_AWS_REGION"
               value="{$fields_value['ETS_TRANS_SELECT_AWS_REGION']|escape:'html':'UTF-8'}"/>
        <div class="dropdown" id="dropdown_ETS_TRANS_SELECT_AWS_REGION">
            <button class="btn btn-default dropdown-toggle" type="button" id="btn_ETS_TRANS_SELECT_AWS_REGION"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="content-lang content-select-api content-select-aws-region" data-action="{$fields_value['ETS_TRANS_SELECT_AWS_REGION']|escape:'html':'UTF-8'}">
                    {foreach $listRegion as $op}
                        {if $op.value == $fields_value['ETS_TRANS_SELECT_AWS_REGION']}
                            {$op.label|escape:'html':'UTF-8'}
                        {/if}
                    {/foreach}
                </span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="btn_ETS_TRANS_SELECT_AWS_REGION">
                {foreach $listRegion as $op}
                    <li>
                        <a href="#" class="js-ets-trans-choose-api-item"
                           data-api="{$op.value|escape:'html':'UTF-8'}">
                            {$op.label|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {elseif $input.name == 'ETS_TRANS_LANG_SOURCE'}
        <div class="ets-trans-checkbox-dropdown">
        <input type="hidden" name="ETS_TRANS_LANG_SOURCE" id="ETS_TRANS_LANG_SOURCE"
               value="{$fields_value['ETS_TRANS_LANG_SOURCE']|escape:'html':'UTF-8'}"/>
        <div class="ets_dropdown" id="dropdown_ETS_TRANS_LANG_SOURCE">
            <button class="btn btn-default dropdown-toggle" type="button" id="btn_ETS_TRANS_LANG_SOURCE"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="content-lang">
                    {foreach $langWithFlag as $op}
                        {if $op.id_lang == $fields_value['ETS_TRANS_LANG_SOURCE']}
                            <img src="{$op.flag|escape:'quotes':'UTF-8'}" class="lang-flag"/>
                            {$op.name|escape:'html':'UTF-8'}
                        {/if}
                    {/foreach}
                </span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="btn_ETS_TRANS_LANG_SOURCE">
                {foreach $langWithFlag as $op}
                    <li>
                        <a href="#" class="js-ets-trans-choose-lang-source-item"
                           data-lang="{$op.id_lang|escape:'html':'UTF-8'}">
                            <img src="{$op.flag|escape:'quotes':'UTF-8'}" class="lang-flag"/> {$op.name|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        </div>
    {elseif $input.name == 'ETS_TRANS_LANG_TARGET'}
        <div class="ets-trans-checkbox-dropdown">
            <div class="ets_dropdown">
                <button type="button" id="langTargetDropdownBo" class="btn btn-default">
                    <span class="text-html {if count($ETS_TRANS_LANG_TARGET) == 1}single-lang{/if}">
                        {if count($langWithFlag) <=1}
                            --
                        {else}
                            {foreach $langWithFlag as $index_op => $op}
                                {if (!$ETS_TRANS_LANG_TARGET || !count($ETS_TRANS_LANG_TARGET) || in_array($op.id_lang, $ETS_TRANS_LANG_TARGET)) && $op.flag && $fields_value['ETS_TRANS_LANG_SOURCE'] != $op.id_lang}
                                    <img src="{$op.flag|escape:'quotes':'UTF-8'}"/><span>{if count($ETS_TRANS_LANG_TARGET) == 1}{$op.name|escape:'html':'UTF-8'}{else}{$op.iso_code|escape:'html':'UTF-8'}{/if}</span>{if (count($ETS_TRANS_LANG_TARGET) && $op.id_lang != $ETS_TRANS_LANG_TARGET[count($ETS_TRANS_LANG_TARGET)-1]) || (!count($ETS_TRANS_LANG_TARGET) && $index_op < (count($langWithFlag) - 1))}, {/if}
                                {/if}
                            {/foreach}
                        {/if}
                    </span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="langTargetDropdownBo">
                    <li>
                        <div class="checkbox-item checkbox-all">
                            <input type="checkbox" name="ETS_TRANS_LANG_TARGET_ALL" id="ETS_TRANS_LANG_TARGET_all"
                                   value="1" {if !$ETS_TRANS_LANG_TARGET || count($ETS_TRANS_LANG_TARGET) == 0 || count($ETS_TRANS_LANG_TARGET) == count($langTarget)-1}checked="checked"{/if} />
                            <label for="ETS_TRANS_LANG_TARGET_all">{l s='All languages' mod='ets_translate'}</label>
                        </div>
                    </li>
                    {foreach $langWithFlag as $op}
                        <li class="{if $fields_value['ETS_TRANS_LANG_SOURCE'] == $op.id_lang}hide{/if}">
                            <div class="checkbox-item checkbox-option">
                                <input type="checkbox" name="ETS_TRANS_LANG_TARGET[]"
                                       id="ETS_TRANS_LANG_TARGET_{$op.id_lang|escape:'html':'UTF-8'}"
                                       value="{$op.id_lang|escape:'html':'UTF-8'}"
                                       {if !$ETS_TRANS_LANG_TARGET || !count($ETS_TRANS_LANG_TARGET) || in_array($op.id_lang, $ETS_TRANS_LANG_TARGET)}checked="checked"{/if} data-isocode="{$op.iso_code|escape:'html':'UTF-8'}" />
                                <label for="ETS_TRANS_LANG_TARGET_{$op.id_lang|escape:'html':'UTF-8'}">
                                    <img src="{$op.flag|escape:'quotes':'UTF-8'}"/>{$op.name|escape:'html':'UTF-8'}
                                </label>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {elseif $input.name == 'ETS_TRANS_PAGE_APPEND_CONTEXT_WORD'}
        <p class="checkbox">
            <label>
                <input type="checkbox" name="" id="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD_all" value="all" {if count($ETS_TRANS_PAGE_APPEND_CONTEXT_WORD) == count($pageAppendContextWords)}checked="checked"{/if} />
                {l s='All pages' mod='ets_translate'}
            </label>
        </p>
        {foreach $pageAppendContextWords as $pageItem}
            <p class="checkbox">
                <label>
                    <input type="checkbox" name="ETS_TRANS_PAGE_APPEND_CONTEXT_WORD[]" value="{$pageItem.value|escape:'html':'UTF-8'}" {if in_array($pageItem.value, $ETS_TRANS_PAGE_APPEND_CONTEXT_WORD)}checked="checked"{/if} />
                    {$pageItem.title|escape:'html':'UTF-8'}
                </label>
            </p>
        {/foreach}
    {elseif $input.name == 'ETS_TRANS_KEY_PHRASE_FROM'}
        {*        do nothing*}
    {elseif $input.name == 'ETS_TRANS_KEY_PHRASE_TO'}
        {*        do nothing*}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='description'}
    {$smarty.block.parent}
    {if isset($input.shortcodes) && count($input.shortcodes)}
        <div class="shortcodes ets-trans-shortcodes js-ets-trans-shortcodes">
            {if isset($input.shortcode_label) && $input.shortcode_label}
                <label class="shortcode-label">{$input.shortcode_label|escape:'html':'UTF-8'}</label>
            {/if}
            {foreach $input.shortcodes as $k => $shortcode}
                <span title="{l s='Click to copy' mod='ets_translate'}" class="shortcode shortcode-{$k|escape:'html':'UTF-8'}">{$shortcode|escape:'html':'UTF-8'}</span>
            {/foreach}
        </div>
    {else}
    {/if}
{/block}