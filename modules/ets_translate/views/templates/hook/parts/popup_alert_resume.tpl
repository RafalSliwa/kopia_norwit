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
<div class="modal fade ets-trans-modal" id="etsTransModalTrans" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
    <div class="ets_table ets_trans_table">
    <div class="ets_table-cell">
        <div class="modal-content">
            <form id="formEtsTransResumeAlert">
                <div class="panel_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <i class="ets_svg_icon" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 107-36 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg></i>
                        {if isset($page_type) && $page_type == 'all'}
                            {l s='1-Click translate' mod='ets_translate'}
                        {else}
                            {l s='Translate' mod='ets_translate'}
                        {/if}
                    </h4>
                </div>
                <div class="panel_body">
                    <div class="ets-trans-content">
                        <div class="alert alert-info">
                            <p>{l s='Total translated' mod='ets_translate'}: {if $page_type == 'pc' || $page_type == 'blog'}{$nb_path|escape:'html':'UTF-8'}{else}{$nb_translated|escape:'html':'UTF-8'}{/if}
                                {if $page_type == 'product'}
                                    {l s='products' mod='ets_translate'}
                                {elseif $page_type == 'category'}
                                    {l s='categories' mod='ets_translate'}
                                {elseif $page_type == 'cms'}
                                    {l s='CMSs' mod='ets_translate'}
                                {elseif $page_type == 'cms_category'}
                                    {l s='CMS categories' mod='ets_translate'}
                                {elseif $page_type == 'manufacturer'}
                                    {l s='Manufacturers' mod='ets_translate'}
                                {elseif $page_type == 'supplier'}
                                    {l s='suppliers' mod='ets_translate'}
                                {elseif $page_type == 'email'}
                                    {l s='emails' mod='ets_translate'}
                                {elseif $page_type == 'pc'}
                                    {l s='product comments' mod='ets_translate'}
                                {elseif $page_type == 'blog'}
                                    {if $blog_type == 'category'}
                                        {l s='blog categories' mod='ets_translate'}
                                    {else}
                                        {l s='blog posts' mod='ets_translate'}
                                    {/if}

                                {else}
                                    {l s='texts' mod='ets_translate'}
                                {/if}
                                ({$nb_char_translated|escape:'html':'UTF-8'} {l s='characters' mod='ets_translate'})</p>
                        </div>
                        <div>
                            <input type="hidden" name="nb_translated" value="{$nb_translated|escape:'html':'UTF-8'}">
                            <input type="hidden" name="nb_char_translated" value="{$nb_char_translated|escape:'html':'UTF-8'}">
                            <input type="hidden" name="pageType" value="{$page_type|escape:'html':'UTF-8'}">
                            <input type="hidden" name="trans_source" value="{$lang_source|escape:'html':'UTF-8'}">
                            <input type="hidden" name="trans_target" value="{$lang_target|escape:'html':'UTF-8'}">
                            <input type="hidden" name="trans_option" value="{$field_option|escape:'html':'UTF-8'}">
                            <input type="hidden" name="total_item" value="{$total_translate|escape:'html':'UTF-8'}">
                            <input type="hidden" name="nb_path" value="{if isset($nb_path)}{$nb_path|escape:'html':'UTF-8'}{/if}">
                            <input type="hidden" name="ignore_product_name" value="{if isset($ignore_product_name)}{$ignore_product_name|escape:'html':'UTF-8'}{/if}">
                            <input type="hidden" name="ignore_content_has_product_name" value="{if isset($ignore_content_has_product_name)}{$ignore_content_has_product_name|escape:'html':'UTF-8'}{/if}">
                            <input type="hidden" name="auto_generate_link_rewrite" value="{if isset($auto_generate_link_rewrite)}{$auto_generate_link_rewrite|escape:'html':'UTF-8'}{/if}">
                            {if $page_type == 'email'}
                                <input type="hidden" name="mail_option" value="{if isset($mail_option)}{$mail_option|escape:'html':'UTF-8'}{/if}">
                            {/if}
                            <input type="hidden" name="page_id" value="">
                        </div>
                    </div>
                    {include './popup_translating.tpl'}
                </div>
                <div class="panel_footer">
                    <div class="btn-group-trans btn-group-translating">
                        <button type="button" class="btn btn-primary js-ets-trans-translate-from-resume">{l s='Resume' mod='ets_translate'}</button>
                        <button type="button" class="btn btn-outline-secondary js-ets-trans-translate-from-zero">{l s='Retranslate all' mod='ets_translate'}</button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
        </div>
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
