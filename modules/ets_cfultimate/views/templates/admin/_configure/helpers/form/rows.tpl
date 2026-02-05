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
<h3 class="ets_cfu_title">{l s='Add row' mod='ets_cfultimate'}</h3>
<div class="ets_cfu_rows">
    {*column-1-0*}
    <div class="ets_cfu_box style1" data-type="style1" data-order="0">
        {include file="./row.tpl" col = 1}
        <span class="ets_cfu_title_box"><b>{l s='1 column in a row' mod='ets_cfultimate'}</b></span>
    </div>
    
    {*column-2-i*}
    {for $type = 0 to 2}
    <div class="ets_cfu_box style3{if $type}{$type|intval}{/if}" data-type="style3{if $type}{$type|intval}{/if}" data-order="0">
        {include file="./row.tpl" col = 2}
        <span class="ets_cfu_title_box">
            <b>{l s='2 columns' mod='ets_cfultimate'}</b><br />
            {if $type == 0}({l s='same width' mod='ets_cfultimate'})
            {elseif $type == 1}({l s='Large right' mod='ets_cfultimate'})
            {elseif $type == 2}({l s='Large left' mod='ets_cfultimate'})
            {/if}
        </span>
    </div>
    {/for}
    
    {*column-3-i*}
    {for $type = 0 to 3}<div class="ets_cfu_box style2{if $type}{$type|intval}{/if}" data-type="style2{if $type}{$type|intval}{/if}" data-order="0">
        {include file="./row.tpl" col = 3}
        <span class="ets_cfu_title_box">
            <b>{l s='3 columns' mod='ets_cfultimate'}</b><br />
            {if $type == 0}({l s='same width' mod='ets_cfultimate'})
            {elseif $type == 1}({l s='Large right' mod='ets_cfultimate'})
            {elseif $type == 2}({l s='Large center' mod='ets_cfultimate'})
            {elseif $type == 3}({l s='Large left' mod='ets_cfultimate'})
            {/if}
        </span>
    </div>{/for}
    
    {*column-4-0*}
    <div class="ets_cfu_box style4" data-type="style4" data-order="0">
        {include file="./row.tpl" col = 4}
        <span class="ets_cfu_title_box">
            <b>{l s='4 columns' mod='ets_cfultimate'}</b><br />
            ({l s='same width' mod='ets_cfultimate'})</span>
    </div>
</div>