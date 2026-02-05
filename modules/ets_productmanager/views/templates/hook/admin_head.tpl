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
var ets_pmn_tax_rule_groups = {literal}{}{/literal};
    {if $tax_rule_groups}
        {foreach from=$tax_rule_groups item='rule_groups'}
            ets_pmn_tax_rule_groups[{$rule_groups.id_tax_rules_group|intval}] = {$rule_groups.value_tax|floatval};
        {/foreach}
    {/if}
    var ets_max_lang_text = {$ets_max_lang_text|intval};
    var ets_pmn_lang_current ={$ets_pmn_lang_current|intval};
    var update_successully_text ='{l s='Upload successfully' mod='ets_productmanager' js=1}';
    var Save_view_text ='{l s='Save view' mod='ets_productmanager' js=1}';
    var Update_view_text ='{l s='Update view' mod='ets_productmanager' js=1}';
    var Save_text ='{l s='Save' mod='ets_productmanager' js=1}';
    var Update_text = '{l s='Update' mod='ets_productmanager' js=1}'; 
</script>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css"/>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css"/>
{if isset($ets_pmn_seo_meta_titles) && $ets_pmn_seo_meta_titles}
    {foreach from= $ets_pmn_seo_meta_titles key='id_lang' item='meta_title'}
        {if $meta_title}
            <input type="hidden" id="ets_pmn_seo_metatitle_{$id_lang|intval}" value="{$meta_title|escape:'html':'UTF-8'}" />
        {/if}
    {/foreach}
    {foreach from=$ets_pmn_seo_meta_descriptions key='id_lang' item='meta_description'}
        {if $meta_description}
            <textarea id="ets_pmn_seo_metadescription_{$id_lang|intval}" style="display:none">{$meta_description|escape:'html':'UTF-8'}</textarea>
        {/if}
    {/foreach}
{/if}
<div class="ets_product_popup">
    <div class="popup_content table">
        <div class="popup_content_tablecell">
            <div class="popup_content_wrap" style="position: relative">
                <span class="close_popup" title="Close">+</span>
                <div id="block-form-popup-dublicate">

                </div>
            </div>
        </div>
    </div>
</div>