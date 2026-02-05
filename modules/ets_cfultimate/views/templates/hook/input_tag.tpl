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
<div class="ets_cfu_input_tag hide">
    {foreach $languages as $language}
        {if $is_multi_lang}
            <div class="translatable-field lang-{$language.id_lang|intval}"{if $is_multi_lang} data-lang="{$language.id_lang|intval}"{/if}{if $language.id_lang != $defaultFormLanguage} style="display:none"{/if}>
        {/if}
        <input type="text" data-type="{if !empty($input_type)}{$input_type|escape:'html':'UTF-8'}{else}text{/if}" name="{if !empty($input_type)}{$input_type|escape:'html':'UTF-8'}{else}text{/if}_{if $is_multi_lang}{$language.id_lang|intval}{else}{$defaultFormLanguage|intval}{/if}"{if $is_multi_lang} data-lang="{$language.id_lang|intval}"{/if} class="tag code" readonly="readonly" onfocus="this.select()"/>
        {if $is_multi_lang}
            </div>
        {/if}
    {/foreach}
</div>
<div class="submitbox">
    <input type="button" class="button button-primary insert-tag insert_field" value="{l s='Add input field' mod='ets_cfultimate'}"/>
    <input type="button" class="button button-primary insert-tag update_field" value="{l s='Update input field' mod='ets_cfultimate'}"/>
</div>