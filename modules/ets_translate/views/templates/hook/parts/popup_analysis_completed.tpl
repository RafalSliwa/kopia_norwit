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

<div id="etsTransPopupAnalysisCompleted">
    <form>
        <input type="hidden" name="trans_source" value="">
        <input type="hidden" name="trans_target" value="">
        <input type="hidden" name="trans_option" value="">
        <input type="hidden" name="auto_detect_language" value="">
        <input type="hidden" name="mail_option" value="">
        <input type="hidden" name="trans_wd" value="">
        <input type="hidden" name="ignore_product_name" value="">
        <input type="hidden" name="ignore_content_has_product_name" value="">
        <input type="hidden" name="auto_generate_link_rewrite" value="">
        <div class="nothing-to-translate hide">
            <div class="alert alert-info">{l s='All content has been translated or there is no data to translate, nothing to do!' mod='ets_translate'}</div>
        </div>
        <div class="info-analysis">
            <p>{l s='You are going to translate' mod='ets_translate'} <span class="nb_text"></span> <span class="text_type">{l s='texts' mod='ets_translate'}</span> (<span class="nb_char"></span> {l s='characters' mod='ets_translate'}) <span class="nb_lang_target"></span></p>
            <p class="{if isset($isConfigGoogleRate) && $isConfigGoogleRate}{else}hide{/if}">{l s='Estimated price:' mod='ets_translate'} <span class="nb_money"></span></p>
        </div>
        <div class="alert alert-info">
            <p>{l s='Total characters you translated in this month:' mod='ets_translate'} <span class="total-char">{if $total_character}{$total_character|escape:'html':'UTF-8'}{else}0{/if}</span> {if $total_character <= 1} {l s='character' mod='ets_translate'} {else} {l s='characters' mod='ets_translate'} {/if} </p>
        </div>
    </form>
</div>