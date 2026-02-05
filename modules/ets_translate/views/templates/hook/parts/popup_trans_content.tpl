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
{if count($allLanguages) > 1 && $hasGoogleApiKey}
<div class="form-trans others_page">
    <form id="etsTransFormTransPages">
        {if isset($pageType) && $pageType !== 'theme' && $pageType !== 'email' && $pageType !== 'module' && $pageType !== 'all'}
            
                <div class="row form-group">
                    <div class="group-fields">
                        <label class="col-lg-3 col-md-3">{l s='Translate from (source language)' mod='ets_translate'}</label>
                        <div class="col-lg-9 col-md-9 trans-lang-options">
                            <div class="dropdown">
                                <button class="btn btn-default dropdown-toggle js-ets-trans-btn-lang-source" type="button"
                                        id="etsTransSelectLangSource" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    <span class="text-html">
                                        {foreach $allLanguages as $lang}
                                            {if $lang.id_lang == $idLangDefault}
                                                <img src="{$lang.flag|escape:'quotes':'UTF-8'}"><span>{$lang.name|escape:'html':'UTF-8'}</span>
                                            {/if}
                                        {/foreach}
                                    </span>
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="etsTransSelectLangSource">
                                    {foreach $allLanguages as $lang}
                                        <a class="dropdown-item js-ets-trans-lang-source" href="#"
                                           data-lang-id="{$lang.id_lang|escape:'html':'UTF-8'}">
                                            <img src="{$lang.flag|escape:'quotes':'UTF-8'}">
                                            <span>{$lang.name|escape:'html':'UTF-8'}</span>
                                        </a>
                                    {/foreach}
                                </div>
                            </div>
                            <input type="hidden" name="trans_source" value="{$idLangDefault|escape:'html':'UTF-8'}" />
                            <input type="hidden" name="page_id" value="{if isset($pageId)}{$pageId|escape:'html':'UTF-8'}{/if}" />
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="group-fields">
                        <label class="choose_list_lang ets_mt_6 col-lg-3 col-md-3">{l s='to (destination languages)' mod='ets_translate'}</label>
                        <div class="col-lg-9 col-md-9 trans-lang-options">
                            <div class="dropdown trans-lang-options_dropdown_content asaa">
                                <button type="button" id="langTargetDropdown" class="btn btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-html {if count($langTargetIds) == 1}single-lang{/if}">
                                                {if !$langTargetIds}
                                                    --
                                                {else}
                                                    {foreach $allLanguages as $lang}
                                                        {if $configAutoEnable && in_array($lang.id_lang, $langTargetIds)}
                                                            <img src="{$lang.flag|escape:'quotes':'UTF-8'}" /><span>{if count($langTargetIds) == 1}{$lang.name|escape:'html':'UTF-8'}{else}{$lang.iso_code|escape:'html':'UTF-8'}{/if}</span>{if $lang.id_lang != $langTargetIds[count($langTargetIds)-1]}, {/if}
                                                        {/if}
                                                    {/foreach}
                                                {/if}
                                            </span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="langTargetDropdown">
                                    <li>
                                        <div class="form-check form-check-inline js-ets-trans-lang-target-all">
                                            <input class="form-check-input" type="checkbox"
                                                   id="etsTransSelectLangTarget_all"
                                                   value="all"
                                                   {if $configAutoEnable && count($langTargetIds) == count($allLanguages)-1}checked="checked"{/if}
                                            >
                                            <label for="etsTransSelectLangTarget_all">
                                                {l s='All languages' mod='ets_translate'}
                                            </label>
                                        </div>
                                    </li>
                                    {foreach $allLanguages as $lang}
                                        <li class="{if $configAutoEnable && $langSource && $langSource.id_lang == $lang.id_lang}hide{elseif !$configAutoEnable && $lang.id_lang == $idLangDefault}hide{/if}">
                                            <div class="form-check form-check-inline js-ets-trans-lang-target lang-{$lang.id_lang|escape:'html':'UTF-8'} "
                                                 data-isocode="{$lang.iso_code|escape:'html':'UTF-8'}">
                                                <input class="form-check-input js-ets-trans-lang-target-input" type="checkbox"
                                                       id="etsTransSelectLangTarget_{$lang.id_lang|escape:'html':'UTF-8'}"
                                                       value="{$lang.id_lang|escape:'html':'UTF-8'}"
                                                       name="trans_target[]"
                                                       {if $configAutoEnable && in_array($lang.id_lang, $langTargetIds)}checked="checked"{/if} />
                                                <label for="etsTransSelectLangTarget_{$lang.id_lang|escape:'html':'UTF-8'}">
                                                    <img src="{$lang.flag|escape:'quotes':'UTF-8'}" />
                                                    <span>{$lang.name|escape:'html':'UTF-8'}</span>
                                                </label>
                                            </div>

                                        </li>
                                    {/foreach}
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
        {/if}
        {if isset($optionMailTrans) && $optionMailTrans}
            <div class="section-mail-options mt-3">
                <div class="row">
                    <label class="col-lg-3">{l s='Select mails to translate' mod='ets_translate'}</label>
                    
                    <div class="col-lg-9">
                        <div class="mail-options-selector">
                            <div class="form-check form-check-inline ets-trans-mail-option">
                                <input class="form-check-input" type="checkbox"
                                       id="etsTransMailOption_all"
                                       value="all"
                                       checked="checked">
                                <label class="form-check-label"
                                       for="etsTransMailOption_all">
                                    {l s='All' mod='ets_translate'}
                                </label>
                            </div>
                            {foreach $optionMailTrans as $key=>$option}
                                <div class="form-check form-check-inline ets-trans-mail-option">
                                    <input class="form-check-input js-ets-trans-mail-option-item" type="checkbox" name="mail_option[]"
                                           id="etsTransMailOption_{$key|escape:'html':'UTF-8'}"
                                           checked="checked"
                                           value="{$option.key|escape:'html':'UTF-8'}"
                                          >
                                    <label class="form-check-label"
                                           for="etsTransMailOption_{$key|escape:'html':'UTF-8'}">
                                        ({if $option.type == 'core_email'}{l s='Core email' mod='ets_translate'}{else}{l s='Module: ' mod='ets_translate'}{$option.name|escape:'html':'UTF-8'}{/if}) {$option.file|escape:'html':'UTF-8'}
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <div class="trans-options-type">
            <div class="row form-group">
                <label class="col-lg-3 col-md-3 mb_text_left">{l s='How to translate' mod='ets_translate'}</label>
                
                <div class="col-lg-9 col-md-9">
                    {foreach $transOptions as $key=>$option}
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trans_option"
                                   id="etsTransOption_{$key|escape:'html':'UTF-8'}"
                                   value="{$key|escape:'html':'UTF-8'}"
                                    {if isset($option.default) && $option.default}checked="checked"{/if}>
                            <label class="form-check-label"
                                   for="etsTransOption_{$key|escape:'html':'UTF-8'}">{$option.title|escape:'html':'UTF-8'}</label>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="trans-actions text-center">
                    {if $hasGoogleApiKey}
                    <button type="button"
                            class="btn btn-primary {if isset($pageType) && ($pageType == 'theme' || $pageType == 'email' || $pageType == 'module')}js-ets-trans-btn-inter-trans{else}js-ets-trans-btn-translate-page{/if}"
                            data-page-type="{if isset($pageType)}{$pageType|escape:'html':'UTF-8'}{/if}"
                            data-trans-all="{if $isTransAll}1{else}0{/if}"
                            data-field="{if $fieldTrans}{$fieldTrans|escape:'html':'UTF-8'}{/if}"
                            data-blog-type="{if isset($blogType)}{$blogType|escape:'html':'UTF-8'}{/if}"
                    >
                        {/if}
                        <i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i> <span class="text-btn-translate">{l s='Translate' mod='ets_translate'}</span>
                    </button>
                    <span class="append-btn-stop"></span>
                </div>
            </div>
        </div>
    </form>
</div>
{elseif !$hasGoogleApiKey}
    <div class="alert alert-warning">
        {l s='The Google translate API key is not configured. Please configure it by clicking' mod='ets_translate'} <a href="{$linkConfigApi|escape:'quotes':'UTF-8'}" style="padding: 0;">{l s='here' mod='ets_translate'}</a>
    </div>
{else}
    <div class="alert alert-info">
        {l s='Your shop has only one language and you do not need to translate' mod='ets_translate'}
    </div>
{/if}