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
{if isset($item_attrs.value) && $item_attrs.value || $label}
    <option
{if isset($item_attrs) && $item_attrs}
        {foreach from=$item_attrs key='key' item='item'}
            {$key nofilter}="{$item nofilter}"
        {/foreach} {/if}>
        {$label nofilter}
    </option>
{/if}