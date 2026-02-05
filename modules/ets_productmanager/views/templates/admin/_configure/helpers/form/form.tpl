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
{extends file="helpers/form/form.tpl"}
{block name="legend"}
	<div class="panel-heading">
		{if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
		{$field.title|escape:'html':'UTF-8'}
        {if $table=='configuration'}
            <span class="panel-heading-action">
                <a class="list-toolbar" target="_blank" href="{$link->getAdminLink('AdminProducts',true)|escape:'html':'UTF-8'}">
        				<i class="icon icon-edit"></i> {l s='Quick Edit' mod='ets_productmanager'} <i class="pmn_icon_hover icon icon-external-link" aria-hidden="true"></i>
                        
                </a>
                <a class="list-toolbar" target="_blank" href="{$link->getAdminLink('AdminProductManagerMassiveEdit', true)|escape:'html':'UTF-8'}">
    				<i class="icon icon-list"></i> {l s='Mass Edit' mod='ets_productmanager'} <i class="pmn_icon_hover icon icon-external-link" aria-hidden="true"></i>
                </a>
            </span>
        {/if}
	</div>
{/block}