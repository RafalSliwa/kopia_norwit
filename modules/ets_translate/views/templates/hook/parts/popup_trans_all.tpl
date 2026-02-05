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
{assign 'ETS_TRANS_WD_CONFIG' ','|explode:$wdConfig}

{if !isset($isInterTrans)}
    {assign var='isInterTrans' value=0}
{/if}
{capture name='dataTotalWillTrans'}
        {if $pageType == 'theme'}
            {if $sfType == 'themes'}
                {l s='theme' mod='ets_translate'} {$selectedTheme|escape:'html':'UTF-8'}
            {elseif $sfType == 'modules'}
                {l s='module' mod='ets_translate'} {$selectedTheme|escape:'html':'UTF-8'}
            {elseif $sfType == 'back'}
                {l s='Back office' mod='ets_translate'}
            {elseif $sfType == 'mails'}
                {l s='Email subjects' mod='ets_translate'}
            {elseif $sfType == 'others'}
                {l s='Others' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'module'}
            {l s='module' mod='ets_translate'} {$moduleName|escape:'html':'UTF-8'}
        {elseif $pageType == 'megamenu'}
            {l s='Module mega menu' mod='ets_translate'}
        {elseif $pageType == 'pc'}
            {if isset($pcType)}

                {if $pcType == 'review'}
                    {l s='Reviews & Ratings' mod='ets_translate'}
                {elseif $pcType == 'comment'}
                    {l s='Comments' mod='ets_translate'}
                {elseif $pcType == 'reply'}
                    {l s='Replies' mod='ets_translate'}
                {elseif $pcType == 'question'}
                    {l s='Questions and Answers' mod='ets_translate'}
                {elseif $pcType == 'question_comment'}
                    {l s='Question Comments' mod='ets_translate'}
                {elseif $pcType == 'answer'}
                    {l s='Answers' mod='ets_translate'}
                {elseif $pcType == 'answer_comment'}
                    {l s='Answer Comments' mod='ets_translate'}
                {/if}

            {/if}
        {elseif $pageType == 'blog'}
            {if $blogType == 'category'}
                {l s='Blog categories' mod='ets_translate'}
            {elseif $blogType == 'post'}
                {l s='Blog posts' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'all' || $pageType == 'inter'}

        {else}<span class="total_items">
            {$totalTranslate|escape:'html':'UTF-8'}</span>
        {/if}
        {if $pageType == 'product'}
            {if $totalTranslate > 1}
                {l s='products' mod='ets_translate'}
            {else}
                {l s='product' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'category'}
            {if $totalTranslate > 1}
                {l s='categories' mod='ets_translate'}
            {else}
                {l s='category' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'cms'}
            {if $totalTranslate > 1}
                {l s='CMSs' mod='ets_translate'}
            {else}
                {l s='CMS' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'cms_category'}
            {if $totalTranslate > 1}
                {l s='CMS categories' mod='ets_translate'}
            {else}
                {l s='CMS category' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'manufacturer'}
            {if $totalTranslate > 1}
                {l s='manufacturers' mod='ets_translate'}
            {else}
                {l s='manufacturer' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'supplier'}
            {if $totalTranslate > 1}
                {l s='suppliers' mod='ets_translate'}
            {else}
                {l s='supplier' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'attribute_group'}
            {if $totalTranslate > 1}
                {l s='attribute groups' mod='ets_translate'}
            {else}
                {l s='attribute group' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'attribute'}
            {if $totalTranslate > 1}
                {l s='attributes' mod='ets_translate'}
            {else}
                {l s='attribute' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'feature'}
            {if $totalTranslate > 1}
                {l s='features' mod='ets_translate'}
            {else}
                {l s='feature' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'feature_value'}
            {if $totalTranslate > 1}
                {l s='feature values' mod='ets_translate'}
            {else}
                {l s='feature value' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'blockreassurance'}
            {if $totalTranslate > 1}
                {l s='reassurance blocks' mod='ets_translate'}
            {else}
                {l s='reassurance block' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'ps_linklist'}
            {if $totalTranslate > 1}
                {l s='blocks' mod='ets_translate'}
            {else}
                {l s='block' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'ps_mainmenu'}
            {if $totalTranslate > 1}
                {l s='menu items' mod='ets_translate'}
            {else}
                {l s='menu item' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'ps_imageslider'}
            {if $totalTranslate > 1}
                {l s='slides' mod='ets_translate'}
            {else}
                {l s='slide' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'ps_customtext'}
            {if $totalTranslate > 1}
                {l s='text blocks' mod='ets_translate'}
            {else}
                {l s='text block' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'ets_extraproducttabs'}
            {if $totalTranslate > 1}
                {l s='tabs' mod='ets_translate'}
            {else}
                {l s='tab' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'email'}
            {if $totalTranslate > 1}
                {l s='emails' mod='ets_translate'}
            {else}
                {l s='email' mod='ets_translate'}
            {/if}
        {elseif $pageType == 'theme' || $pageType == 'module' || $pageType == 'blog' || $pageType == 'megamenu' || $pageType == 'all'|| $pageType == 'inter'|| $pageType == 'pc'}
        {else}
            {if $totalTranslate > 1}
                {l s='text' mod='ets_translate'}
            {else}
                {l s='texts' mod='ets_translate'}
            {/if}
        {/if}
{/capture}
<div class="modal fade ets-trans-modal bootstrap" id="etsTransModalTrans" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    <div class="ets_table ets_trans_table">
    <div class="ets_table-cell">
        <div class="modal-content">
            <form id="etsTransFormTransPages">
                <div class="panel_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="ets-trans-modal-header-flex">
                        <h4 class="modal-title">
                            <i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i>
                            {if isset($wdConfig) && !$isInterTrans}
                                {l s='1-Click translate' mod='ets_translate'}
                            {else}
                                {l s='Translate' mod='ets_translate'}
                            {/if}
                            <div class="modal-title-right">{if $smarty.capture.dataTotalWillTrans && trim($smarty.capture.dataTotalWillTrans)}(<span>{$smarty.capture.dataTotalWillTrans|escape:'quotes':'UTF-8'}</span>){/if}</div>
                        </h4>

                    </div>
                </div>
                <div class="panel_body">
                    <div class="ets-trans-content">

                    {if $totalTranslate <= 0 && $pageType != 'email' && $pageType != 'module' && $pageType != 'theme' && $pageType != 'megamenu' && $pageType != 'blog' && $pageType != 'pc' && $pageType != 'inter' && $pageType != 'all'}
                        <div class="alert alert-warning">
                            {if $pageType == 'product'}
                                {l s='No product to translate' mod='ets_translate'}
                            {elseif $pageType == 'category'}
                                {l s='No category to translate' mod='ets_translate'}
                            {elseif $pageType == 'cms'}
                                {l s='No CMS to translate' mod='ets_translate'}
                            {elseif $pageType == 'cms_category'}
                                {l s='No CMS category to translate' mod='ets_translate'}
                            {elseif $pageType == 'manufacturer'}
                                {l s='No manufacturer to translate' mod='ets_translate'}
                            {elseif $pageType == 'supplier'}
                                {l s='No supplier to translate' mod='ets_translate'}
                            {elseif $pageType == 'email'}
                                {l s='No email to translate' mod='ets_translate'}
                            {elseif $pageType == 'attribute_group'}
                                {l s='No attribute group to translate' mod='ets_translate'}
                            {elseif $pageType == 'attribute'}
                                {l s='No attribute to translate' mod='ets_translate'}
                            {elseif $pageType == 'feature'}
                                {l s='No feature to translate' mod='ets_translate'}
                            {elseif $pageType == 'feature_value'}
                                {l s='No feature values to translate' mod='ets_translate'}
                            {elseif $pageType == 'blockreassurance'}
                                {l s='No reassurance block values to translate' mod='ets_translate'}
                            {elseif $pageType == 'ps_linklist'}
                                {l s='No link widget to translate' mod='ets_translate'}
                            {elseif $pageType == 'ps_mainmenu'}
                                {l s='No menu to translate' mod='ets_translate'}
                            {elseif $pageType == 'all'}
                                {l s='Nothing to translate' mod='ets_translate'}
                            {else}
                                {l s='No text to translate' mod='ets_translate'}
                            {/if}
                        </div>
                    {elseif ((!$isInterTrans && (count($allLanguages) > 1 || (count($allLanguages) == 1 && $isLocalize && $allLanguages[0].id_lang !== 'en'))) || ($isInterTrans && count($allLanguages) > 1)) && $hasApiKey}
                        <div class="form-trans">
                            <div class="trans-data-info hide">
                                {if $configAutoEnable}
                                    <div class="row form-group">
                                        <label class="col-md-3">{l s='Translate from' mod='ets_translate'}:</label>
                                        <div class="col-md-9">
                                            {if $pageType == 'email' || $pageType == 'module' || $pageType == 'theme'}
                                                <span class="title_lang_source">
                                                <img src="{$imgDir|escape:'html':'UTF-8'}l/{$langSourceDefault.id_lang|escape:'html':'UTF-8'}.jpg" />
                                                {$langSourceDefault.name|escape:'html':'UTF-8'}
                                            </span>
                                            {else}
                                                <span class="title_lang_source">
                                                <img src="{$imgDir|escape:'html':'UTF-8'}l/{$langSource.id_lang|escape:'html':'UTF-8'}.jpg" />
                                                {$langSource.name|escape:'html':'UTF-8'}
                                            </span>
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-3">{l s='Translate into' mod='ets_translate'}:</label>
                                        <div class="col-md-9">
                                            {if $pageType == 'email' || $pageType == 'module' || $pageType == 'theme'}
                                                {if $langTargetDefault}
                                                    <span class="title_lang_target">
                                                        <img src="{$imgDir|escape:'html':'UTF-8'}l/{$langTargetDefault.id_lang|escape:'html':'UTF-8'}.jpg" />
                                                        {$langTargetDefault.name|escape:'html':'UTF-8'}
                                                    </span>
                                                {/if}
                                            {else}
                                                <span class="title_lang_target">
                                                    {if $langTarget}
                                                        {foreach $langTarget as $k=>$item}
                                                            {if $k < count($langTarget) - 1}
                                                                <img src="{$imgDir|escape:'html':'UTF-8'}l/{$item.id_lang|escape:'html':'UTF-8'}.jpg" />
                                                                        {$item.name|escape:'html':'UTF-8'},
                                                                {else}
                                                                    <img src="{$imgDir|escape:'html':'UTF-8'}l/{$item.id_lang|escape:'html':'UTF-8'}.jpg" />
                                                                {$item.name|escape:'html':'UTF-8'}
                                                            {/if}
                                                        {/foreach}
                                                    {else}
                                                        --
                                                    {/if}
                                                </span>
                                            {/if}
                                        </div>
                                    </div>

                                    {if $fieldTranslate}
                                        <div class="row form-group">
                                            <label class="col-md-3">{l s='How to translate' mod='ets_translate'}:</label>
                                            <div class="col-md-9">
                                                <span class="field_option_text">{$transOptions[$fieldTranslate].title|escape:'html':'UTF-8'}</span>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $pageType == 'product' || ($pageType == 'all' && isset($wdConfig) && ($wdConfig == 'wd_all' || in_array('catalog_product', $ETS_TRANS_WD_CONFIG) || in_array('catalog_all', $ETS_TRANS_WD_CONFIG)))}
                                        <div class="row form-group">
                                            <label class="col-md-3">{l s='Ignore product name when translating' mod='ets_translate'}:</label>
                                            <div class="col-md-9">
                                                <span class="ignore_prod_name_text {if $ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME == 1}option_switch_on{else}option_switch_off{/if}">{if $ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME == 1}{l s='Yes' mod='ets_translate'}{else}{l s='No' mod='ets_translate'}{/if}</span>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="col-md-3">{l s='Ignore product name if appear in content' mod='ets_translate'}:</label>
                                            <div class="col-md-9">
                                                <span class="ignore_content_has_prod_name_text {if $ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME == 1}option_switch_on{else}option_switch_off{/if}">{if $ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME == 1}{l s='Yes' mod='ets_translate'}{else}{l s='No' mod='ets_translate'}{/if}</span>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $pageType == 'all' && !$isInterTrans}
                                        <div class="data-to-translate">
                                            <div class="row form-group">
                                                <label class="col-md-3">{l s='Data to translate' mod='ets_translate'}:</label>
                                                <div class="col-md-9">
                                                    {if isset($wdConfig) && $wdConfig}
                                                        {include './tree_trans_all.tpl' treeWebTranslations=$treeWebTranslations wdConfig=$wdConfig}
                                                        <input type="hidden" name="trans_wd" value="{$wdConfig|escape:'html':'UTF-8'}"/>
                                                    {else}
                                                        {l s='No data to translate' mod='ets_translate'}
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="row form-group">
                                        <label class="col-md-3">&nbsp;</label>
                                        <div class="col-md-9 xs-hide">
                                            <a href="javascript:void(0)"
                                               class="btn btn-default btn-outline-secondary js-ets-trans-modify-settings"><i
                                                        class="fa fa-cogs"></i> {l s='Modify translation settings' mod='ets_translate'}
                                            </a>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                            <div class="modify-setting">
                                {if isset($pageType) && $pageType !== 'theme' && $pageType !== 'email' && $pageType !== 'module'}
                                    <div class="row form-group" >
                                        <label class="col-md-3 ets_mt_6" >{l s='Translate from' mod='ets_translate'}</label>
                                        <div class="col-md-9">
                                            <div class="group-fields">
                                                <div class="trans-lang-options b-none">
                                                    <div class="dropdown" >
                                                        <button class="btn btn-default dropdown-toggle js-ets-trans-btn-lang-source"
                                                                type="button"
                                                                id="etsTransSelectLangSource" data-toggle="dropdown"
                                                                aria-haspopup="true"
                                                                aria-expanded="false">
                                                            <span class="text-html">
                                                                {if $configAutoEnable && $langSource}
                                                                    <img src="{$imgDir|escape:'html':'UTF-8'}l/{$langSource.id_lang|escape:'html':'UTF-8'}.jpg" /><span>{$langSource.name|escape:'html':'UTF-8'}</span>
    
                                                                {else}
                                                                    {foreach $allLanguages as $lang}
                                                                        {if $lang.id_lang == $idLangDefault}
                                                                            <img src="{$lang.flag|escape:'quotes':'UTF-8'}" />
                                                                            <span>{$lang.name|escape:'html':'UTF-8'} {if isset($origin_lang_id) && $origin_lang_id} - {l s='Original' mod='ets_translate'}{/if}</span>
                                                                        {/if}
                                                                    {/foreach}
                                                                {/if}
                                                            </span>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <div class="dropdown-menu"
                                                             aria-labelledby="etsTransSelectLangSource">
                                                            {foreach $allLanguages as $lang}
                                                                <a class="dropdown-item js-ets-trans-lang-source"
                                                                   href="#"
                                                                   data-lang-id="{$lang.id_lang|escape:'html':'UTF-8'}">
                                                                    <img src="{$lang.flag|escape:'quotes':'UTF-8'}" />
                                                                    <span>
                                                                        {$lang.name|escape:'html':'UTF-8'}
                                                                        {if isset($origin_lang_id) && $origin_lang_id} - {l s='Original' mod='ets_translate'}{/if}
                                                                    </span>
                                                                </a>
                                                            {/foreach}
                                                            {if isset($pcType) && $pcType=='review' && (!isset($origin_lang_id) || !$origin_lang_id)}
                                                                <a class="dropdown-item js-ets-trans-lang-source"
                                                                   href="#"
                                                                   data-lang-id="0">
                                                                    <span>{l s='Original language' mod='ets_translate'}</span>
                                                                </a>
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="trans_source"
                                                           value="{if $configAutoEnable && $langSource}{$langSource.id_lang|escape:'html':'UTF-8'}{else}{$idLangDefault|escape:'html':'UTF-8'}{/if}"/>
                                                    <input type="hidden" name="page_id"
                                                           value="{$pageId|escape:'html':'UTF-8'}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-3 choose_list_lang ets_mt_6">{l s='Translate into' mod='ets_translate'}</label>
                                        <div class="col-md-9">
                                            <div class="group-fields group-fields-to">
                                                <div class="trans-lang-options pd-0">
                                                    <div class="ets_dropdown trans-lang-options_dropdown_content">
                                                        <button type="button" id="langTargetDropdown"
                                                                class="btn btn-default ">
                                                            <span class="text-html {if count($langTargetIds) == 1}single-lang{/if}">
                                                                {if count($allLanguages) <=1}
                                                                    --
                                                                {else}
                                                                {/if}
                                                                {foreach $allLanguages as $index_op => $lang}
                                                                    {if $configAutoEnable && $langSource.id_lang != $lang.id_lang && (in_array($lang.id_lang, $langTargetIds) || !$langTargetIds || !count($langTargetIds))}
                                                                        <img src="{$lang.flag|escape:'quotes':'UTF-8'}"/>
                                                                        <span>{if count($langTargetIds) == 1}{$lang.name|escape:'html':'UTF-8'}{else}{$lang.iso_code|escape:'html':'UTF-8'}{/if}</span>
                                                                        {if (count($langTargetIds) && $lang.id_lang != $langTargetIds[count($langTargetIds)-1]) || (!count($langTargetIds) && $index_op < (count($allLanguages) - 1))}, {/if}
                                                                    {/if}
                                                                {/foreach}
                                                            </span>
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="langTargetDropdown">
                                                            <li>
                                                                <div class="form-check form-check-inline js-ets-trans-lang-target-all">
                                                                    <input class="form-check-input" type="checkbox"
                                                                           id="etsTransSelectLangTarget_all"
                                                                           value="all"
                                                                           {if $configAutoEnable && (!$langTargetIds || count($langTargetIds) == 0 || count($langTargetIds) == count($allLanguages)-1)}checked="checked"{/if}
                                                                    >
                                                                    <label for="etsTransSelectLangTarget_all">
                                                                        {l s='All languages' mod='ets_translate'}
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            {foreach $allLanguages as $lang}
                                                                <li class="">
                                                                    <div class="form-check form-check-inline js-ets-trans-lang-target lang-{$lang.id_lang|escape:'html':'UTF-8'} {if $configAutoEnable && $langSource && $langSource.id_lang == $lang.id_lang}hide{/if}"
                                                                         data-isocode="{$lang.iso_code|escape:'html':'UTF-8'}">
                                                                        <input class="form-check-input js-ets-trans-lang-target-input"
                                                                               type="checkbox"
                                                                               id="etsTransSelectLangTarget_{$lang.id_lang|escape:'html':'UTF-8'}"
                                                                               value="{$lang.id_lang|escape:'html':'UTF-8'}"
                                                                               name="trans_target[]"
                                                                               {if $configAutoEnable && (!$langTargetIds || !count($langTargetIds) || in_array($lang.id_lang, $langTargetIds))}checked="checked"{/if} />
                                                                        <label for="etsTransSelectLangTarget_{$lang.id_lang|escape:'html':'UTF-8'}">
                                                                            <img src="{$lang.flag|escape:'quotes':'UTF-8'}"/>
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
                                                            <input class="form-check-input js-ets-trans-mail-option-item"
                                                                   type="checkbox"
                                                                   name="mail_option[]"
                                                                   id="etsTransMailOption_{$key|escape:'html':'UTF-8'}"
                                                                   checked="checked"
                                                                   value="{$option.key|escape:'html':'UTF-8'}"
                                                            >
                                                            <label class="form-check-label"
                                                                   for="etsTransMailOption_{$key|escape:'html':'UTF-8'}">
                                                                ({if $option.type == 'core_email'}{l s='Core email' mod='ets_translate'}{else}{l s='Module: ' mod='ets_translate'}{$option.name|escape:'html':'UTF-8'}{/if}
                                                                ) {$option.file|escape:'html':'UTF-8'}
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
                                                            {if $configAutoEnable}
                                                                {if $fieldTranslate == $key}checked="checked"{/if}
                                                            {else}
                                                                {if isset($option.default) && $option.default}checked="checked"{/if}
                                                            {/if}
                                                    >
                                                    <label class="form-check-label"
                                                           for="etsTransOption_{$key|escape:'html':'UTF-8'}">{$option.title|escape:'html':'UTF-8'}</label>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                                {if $pageType == 'product' || ($pageType == 'all' && isset($wdConfig) && $wdConfig && ($wdConfig == 'wd_all' || in_array('catalog_product', $ETS_TRANS_WD_CONFIG) || in_array('catalog_all', $ETS_TRANS_WD_CONFIG)))}
                                {/if}
                                <div class="trans-options-type {if isset($listNoLinkRewriteItems) && in_array($pageType,$listNoLinkRewriteItems)}hide{/if}">
                                    <div class="row form-group">
                                        <label class="col-lg-3 col-md-3 mb_text_left" data-auto-generate="{$ETS_TRANS_AUTO_GENERATE_LINK_REWRITE|escape:'html':'UTF-8'}">{l s='Regenerate friendly URL when translating titles' mod='ets_translate'}</label>
                                        <div class="col-lg-9 col-md-9">
{*                                            {if isset($isNewTemplate) && $isNewTemplate}*}
{*                                                <span class="ps-switch switch prestashop-switch ets-trans-switch">*}
{*                                                    <input id="auto_generate_link_rewrite_1" class="ps-switch" name="auto_generate_link_rewrite" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if} type="radio" />*}
{*                                                    <label for="auto_generate_link_rewrite_1" class="label-switch" data-ui="option_switch_on">{l s='Yes' mod='ets_translate'}</label>*}
{*                                                    <input id="auto_generate_link_rewrite_0" class="ps-switch" name="auto_generate_link_rewrite" value="0" type="radio" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if} />*}
{*                                                    <label for="auto_generate_link_rewrite_0" class="label-switch" data-ui="option_switch_off">{l s='No' mod='ets_translate'}</label>*}
{*                                                    <span class="slide-button"></span>*}
{*                                                </span>*}

{*                                            {else}*}
{*                                                <span class="switch prestashop-switch fixed-width-lg">*}
{*                                                    <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_on" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if}>*}
{*                                                    <label for="auto_generate_link_rewrite_on">{l s='Yes' mod='ets_translate'}</label>*}
{*                                                    <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_off" value="0" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if}>*}
{*                                                    <label for="auto_generate_link_rewrite_off">{l s='No' mod='ets_translate'}</label>*}
{*                                                    <a class="slide-button btn"></a>*}
{*                                                </span>*}
{*                                            {/if}*}

                                            {if isset($isNewTemplate) && $isNewTemplate}
                                                <span class="ps-switch switch prestashop-switch ets-trans-switch">

                                                    {if $gte810}
                                                        <input id="auto_generate_link_rewrite_1" class="ps-switch" name="auto_generate_link_rewrite" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_generate_link_rewrite_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        <input id="auto_generate_link_rewrite_0" class="ps-switch" name="auto_generate_link_rewrite" value="0" type="radio" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if} />
                                                        <label for="auto_generate_link_rewrite_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                    {else}
                                                        {if in_array($pageType, ['product'])}
                                                        <input id="auto_generate_link_rewrite_0" class="ps-switch" name="auto_generate_link_rewrite" value="0" type="radio" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if} />
                                                        <label for="auto_generate_link_rewrite_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                        <input id="auto_generate_link_rewrite_1" class="ps-switch" name="auto_generate_link_rewrite" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_generate_link_rewrite_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        {else}
                                                            <input id="auto_generate_link_rewrite_1" class="ps-switch" name="auto_generate_link_rewrite" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_generate_link_rewrite_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        <input id="auto_generate_link_rewrite_0" class="ps-switch" name="auto_generate_link_rewrite" value="0" type="radio" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if} />
                                                        <label for="auto_generate_link_rewrite_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                    {/if}
                                                    {/if}
                                                    <span class="slide-button"></span>
                                                </span>

                                            {else}
                                                <span class="switch prestashop-switch fixed-width-lg">


                                                    {if $gte810}
                                                        <input id="auto_generate_link_rewrite_1" class="ps-switch" name="auto_generate_link_rewrite" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_generate_link_rewrite_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        <input id="auto_generate_link_rewrite_0" class="ps-switch" name="auto_generate_link_rewrite" value="0" type="radio" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if} />
                                                        <label for="auto_generate_link_rewrite_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                    {else}
                                                        {if in_array($pageType, ['product'])}
                                                        <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_off" value="0" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if}>
                                                        <label for="auto_generate_link_rewrite_off">{l s='No' mod='ets_translate'}</label>
                                                        <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_on" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if}>
                                                        <label for="auto_generate_link_rewrite_on">{l s='Yes' mod='ets_translate'}</label>
                                                        {else}
                                                            <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_on" value="1" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 1}checked="checked"{/if}>
                                                        <label for="auto_generate_link_rewrite_on">{l s='Yes' mod='ets_translate'}</label>
                                                        <input type="radio" name="auto_generate_link_rewrite" id="auto_generate_link_rewrite_off" value="0" {if $ETS_TRANS_AUTO_GENERATE_LINK_REWRITE == 0}checked="checked"{/if}>
                                                        <label for="auto_generate_link_rewrite_off">{l s='No' mod='ets_translate'}</label>
                                                    {/if}
                                                    {/if}
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <div class="trans-options-type trans-options-type-auto-detect-lang {if !isset($autoDetectLanguage) }hide{/if}">
                                    <div class="row form-group">
                                        <label class="col-lg-3 col-md-3 mb_text_left">{l s='Auto detect language' mod='ets_translate'}</label>
                                        <div class="col-lg-9 col-md-9">
                                            {if isset($autoDetectLanguage)}
                                                <span class=" ps-switch switch prestashop-switch ets-trans-switch">
                                                    {if $gte810}

                                                        <input id="auto_detect_language_1" class="ps-switch" name="auto_detect_language" value="1" {if $autoDetectLanguage == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_detect_language_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        <input id="auto_detect_language_0" class="ps-switch" name="auto_detect_language" value="0" type="radio" {if $autoDetectLanguage == 0}checked="checked"{/if} />
                                                        <label for="auto_detect_language_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                    {else}
                                                        {if in_array($pageType, ['product'])}
                                                        <input id="auto_detect_language_0" class="ps-switch" name="auto_detect_language" value="0" type="radio" {if $autoDetectLanguage == 0}checked="checked"{/if} />
                                                        <label for="auto_detect_language_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                        <input id="auto_detect_language_1" class="ps-switch" name="auto_detect_language" value="1" {if $autoDetectLanguage == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_detect_language_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        {else}

                                                            <input id="auto_detect_language_1" class="ps-switch" name="auto_detect_language" value="1" {if $autoDetectLanguage == 1}checked="checked"{/if} type="radio" />
                                                        <label for="auto_detect_language_1" class="label-switch">{l s='Yes' mod='ets_translate'}</label>
                                                        <input id="auto_detect_language_0" class="ps-switch" name="auto_detect_language" value="0" type="radio" {if $autoDetectLanguage == 0}checked="checked"{/if} />
                                                        <label for="auto_detect_language_0" class="label-switch">{l s='No' mod='ets_translate'}</label>
                                                    {/if}
                                                    {/if}

                                                    <span class="slide-button"></span>
                                                </span>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                {if $pageType == 'product'}
                                    <div class="trans-options-type">
                                        <div class="row form-group">
                                            <label class="col-lg-3 col-md-3 mb_text_left">{l s='Choose fields to translate' mod='ets_translate'}</label>
                                            <div class="col-lg-9 col-md-9">
                                                <div class="form-check form-check-inline js-ets-trans-select-product-field-all">
                                                    <input class="form-check-input" type="checkbox" id="etsTransSelectListFieldsTransProduct_all" value="all"
                                                           {if isset($ETS_TRANS_LIST_FIELDS_PRODUCT) && (count($ETS_TRANS_LIST_FIELDS_PRODUCT) == 0 || count($ETS_TRANS_LIST_FIELDS_PRODUCT) == count($listFieldsTransProduct))}checked="checked"{/if} >
                                                    <label for="etsTransSelectListFieldsTransProduct_all">
                                                        {l s='All' mod='ets_translate'}
                                                    </label>
                                                </div>
                                                {if count($listFieldsTransProduct)}
                                                    {foreach $listFieldsTransProduct as $key => $field}
                                                        <div class="form-check form-check-inline js-ets-trans-select-product-field">
                                                            <input name="etsTransFields[]" class="form-check-input js-ets-trans-select-product-field-input" type="checkbox" id="etsTransSelectListFieldsTransProduct_{$key|escape:'html':'UTF-8'}" value="{$key|escape:'html':'UTF-8'}"
                                                                   {if isset($ETS_TRANS_LIST_FIELDS_PRODUCT) && (in_array($key, $ETS_TRANS_LIST_FIELDS_PRODUCT) || count($ETS_TRANS_LIST_FIELDS_PRODUCT) == 0)}checked="checked"{/if} >
                                                            <label for="etsTransSelectListFieldsTransProduct_{$key|escape:'html':'UTF-8'}">
                                                                {l s=$field mod='ets_translate'}
                                                            </label>
                                                        </div>
                                                    {/foreach}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                            {if (!isset($hideDataToTrans) || !$hideDataToTrans) && ($pageType == 'all' || $pageType == 'inter')}
                            <div class="data-to-translate">
                                <div class="row form-group">
                                    <label class="col-md-3">{l s='Data to translate' mod='ets_translate'}:</label>
                                    <div class="col-md-9">
                                    {if $pageType == 'all' && !$isInterTrans}
                                        {if isset($wdConfig)}
                                            {include './tree_trans_all.tpl' treeWebTranslations=$treeWebTranslations wdConfig=$wdConfig}
                                            <input type="hidden" name="trans_wd" value="{$wdConfig|escape:'html':'UTF-8'}"/>
                                        {else}
                                            {l s='No data to translate' mod='ets_translate'}
                                        {/if}
                                    {else}
                                        <span>
                                        {if $pageType == 'all'}
                                            {l s='Some pages' mod='ets_translate'}
                                        {elseif $pageType == 'inter'}
                                            {if isset($isInterTrans) && $isInterTrans && isset($treeWebPageOption)}
                                                <div class="tree-trans-wd-option {if !$configAutoEnable}always-show{/if}">
                                                    {include './tree_trans_option.tpl'}
                                                </div>
                                            {else}
                                                {if isset($wdConfig) && $wdConfig}
                                                    <input type="hidden" name="trans_wd" value="{$wdConfig|escape:'html':'UTF-8'}"/>
                                                {/if}
                                            {/if}
                                        {/if}
                                    </span>
                                    {/if}
                                </div>
                                </div>
                            </div>
                            {/if}
                        </div>
                    {elseif !$hasApiKey}
                        <div class="alert alert-warning">
                            {if $apiType == EtsTransApi::$_BING_API_TYPE}
                                {l s='The Bing translate API key is not configured. Please configure it by clicking' mod='ets_translate'}
                            {elseif $apiType == EtsTransApi::$_DEEPL_API_TYPE}
                                {l s='The DeepL translate API key is not configured. Please configure it by clicking' mod='ets_translate'}
                            {elseif $apiType == EtsTransApi::$_LECTO_API_TYPE}
                                {l s='The Lecto translate API key is not configured. Please configure it by clicking' mod='ets_translate'}
                            {elseif $apiType == EtsTransApi::$_LIBRE_API_TYPE}
                                {l s='The Libre translate API key is not configured. Please configure it by clicking' mod='ets_translate'}
                            {else}
                                {l s='The Google translate API key is not configured. Please configure it by clicking' mod='ets_translate'}
                            {/if}
                            <a
                                    href="{$linkConfigApi|escape:'quotes':'UTF-8'}"
                                    style="padding: 0;">{l s='here' mod='ets_translate'}</a>
                        </div>
                    {else}
                        <div class="alert alert-info">
                            {l s='Your shop has only one language and you do not need to translate' mod='ets_translate'}
                        </div>
                    {/if}
                        <div class="form-errors"></div>
                    </div>
                    {include './popup_translating.tpl'}
                </div>
                <div class="panel_footer">
                    <div class="btn-group-trans btn-group-translate">
                        <a href="javascript:void(0)" class="btn-ets-trans-hide-modify-setting js-btn-ets-trans-hide-modify-setting btn btn-default btn-outline-secondary pull-left">{l s='Back to translate' mod='ets_translate'}</a>
                        {if $configAutoEnable}
                            <button type="button" class="btn btn-default btn-outline-secondary pull-left btn-group-translate-close" data-close="close">{l s='Cancel' mod='ets_translate'}</button>
                            {if $hasApiKey && ( ($isInterTrans && count($allLanguages) > 1) || (!$isInterTrans && (count($allLanguages) > 1 || (count($allLanguages) == 1 && $isLocalize  && $allLanguages[0].iso_code != 'en'))))}
                            <button type="button"
                                        class="btn btn-primary trans-multiple {if $enableAnalysis && $isTransAll}js-ets-trans-analysis-text{else}{if isset($pageType) && ($pageType == 'theme' || $pageType == 'email' || $pageType == 'module')}js-ets-trans-btn-inter-trans{else}js-ets-trans-btn-translate-page{/if}{/if}"
                                        data-total-item="{$totalTranslate|escape:'html':'UTF-8'}"
                                        data-page-type="{if isset($pageType)}{$pageType|escape:'html':'UTF-8'}{/if}"
                                        data-trans-all="{if $isTransAll}1{else}0{/if}"
                                        data-field="{if $fieldTrans}{$fieldTrans|escape:'html':'UTF-8'}{/if}"
                                        data-blog-type="{if isset($blogType)}{$blogType|escape:'html':'UTF-8'}{/if}"
                                >{l s='Translate' mod='ets_translate'}</button>
                            {/if}
                        {/if}
                        {if !$configAutoEnable}
                            <div class="trans-actions text-right">
                                <button type="button" class="btn btn-default btn-outline-secondary pull-left btn-group-translate-close" data-close="close">{l s='Cancel' mod='ets_translate'}</button>
                                {if $hasApiKey && ( ($isInterTrans && count($allLanguages) > 1) || (!$isInterTrans && (count($allLanguages) > 1 || (count($allLanguages) == 1 && $isLocalize  && $allLanguages[0].iso_code != 'en'))))}
                                <button type="button"
                                        class="btn btn-primary trans-multiple {if $enableAnalysis && $isTransAll}js-ets-trans-analysis-text{else}{if isset($pageType) && ($pageType == 'theme' || $pageType == 'email' || $pageType == 'module')}js-ets-trans-btn-inter-trans{else}js-ets-trans-btn-translate-page{/if}{/if}"
                                        data-total-item="{$totalTranslate|escape:'html':'UTF-8'}"
                                        data-page-type="{if isset($pageType)}{$pageType|escape:'html':'UTF-8'}{/if}"
                                        data-trans-all="{if $isTransAll}1{else}0{/if}"
                                        data-field="{if $fieldTrans}{$fieldTrans|escape:'html':'UTF-8'}{/if}"
                                        data-blog-type="{if isset($blogType)}{$blogType|escape:'html':'UTF-8'}{/if}" >
                                <span class="text-btn-translate">{l s='Translate' mod='ets_translate'}</span>
                                </button>
                                {/if}
                                <span class="append-btn-stop"></span>
                            </div>
                        {/if}
                    </div>

                    <div class="btn-group-trans btn-group-analysis-completed hide">
                        <button class="btn btn-default btn-outline-secondary js-ets-trans-analysis-cancel-trans pull-left">{l s='Cancel' mod='ets_translate'}</button>
                        <button class="btn btn-primary trans-multiple js-ets-trans-analysis-accept" data-total-item="" data-page-type="" data-trans-all="" data-field="">{l s='Confirm & translate now' mod='ets_translate'}</button>
                    </div>
                    <div class="btn-group-trans btn-group-translating hide">
                        <button type="button" class="btn btn-info js-ets-tran-btn-pause-translate"
                                data-page-type=""
                                data-nb-translated=""
                                data-nb-char=""
                                data-lang-source=""
                                data-lang-target=""
                                data-field-option=""
                        >{l s='Pause' mod='ets_translate'}</button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
        </div>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
