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
<span class="{if isset($each_a_line) && ! $each_a_line}each_a_line{/if} {$class|escape:'html':'UTF-8'}">
    <label for="{$label_for|escape:'html':'UTF-8'}">
        {if $label_first}
            <span class="wpcfu-list-item-label">{$label nofilter}</span>
            <input{if isset($item_attrs) && $item_attrs}{foreach from=$item_attrs key='key' item='item'} {if $item}{$key nofilter}="{$item nofilter}"{/if} {/foreach} {/if}/>

        {else}
            <input{if isset($item_attrs) && $item_attrs}{foreach from=$item_attrs key='key' item='item'} {if $item}{$key nofilter}="{$item nofilter}"{/if} {/foreach}{/if} />
            <span class="wpcfu-list-item-label">{$label nofilter}</span>
        {/if}
    </label>
    {if count( $values ) == $count && isset($free_text_atts) && $free_text_atts}
        <input type="text" {foreach from=$free_text_atts key='key' item='item'} {if $item}{$key nofilter}="{$item nofilter}"{/if} {/foreach} />
    {/if}
</span>