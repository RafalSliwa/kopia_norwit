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
<span class="ets_cfu_form-control-wrap {$html_class|escape:'html':'UTF-8'}">
    <span {foreach from=$atts key='key' item='item'} {if $item} {$key|escape:'html':'UTF-8'}="{$item|escape:'html':'UTF-8'}" {/if} {/foreach} >
        {if $content}
            <span class="wpcfu-list-item"><label><input {if isset($item_attrs) && $item_attrs}{foreach from=$item_attrs key='key' item='item'}{if $item} {$key|escape:'html':'UTF-8'}="{$item|escape:'html':'UTF-8'}" {/if}{/foreach}{/if} /> <span
                            class="wpcfu-list-item-label">{$content nofilter}</span></label></span>

{else}

            <span class="wpcfu-list-item"><input {if isset($item_attrs) && $item_attrs}{foreach from=$item_attrs key='key' item='item'} {if $item}{$key|escape:'html':'UTF-8'}="{$item|escape:'html':'UTF-8'}" {/if}{/foreach}{/if} /></span>
        {/if}
    </span>
{$validation_error|escape:'html':'UTF-8'}</span>