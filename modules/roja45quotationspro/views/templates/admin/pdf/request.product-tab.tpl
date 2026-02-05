{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<table id="product-tab" class="product">

	<tr>
		<th class="product header small" width="{$layout.image.width}%"></th>
		<th class="product header small" width="{$layout.product.width}%">
			<span >{l s='Product' mod='roja45quotationspro'}</span>
		</th>
		<th class="product header small" width="{$layout.customizations.width}%">
			<span >{l s='Customizations' mod='roja45quotationspro'}</span>
		</th>
		<th class="product header small" width="{$layout.unit_price.width}%">
			<span >{l s='Unit Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
		</th>
		<th class="product header small" width="{$layout.quantity.width}%">
			<span >{l s='Quantity' mod='roja45quotationspro'}</span>
		</th>
		<th class="product header small" width="{$layout.total_price.width}%">
			<span >{l s='Total Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
		</th>
		{if $use_tax}
			<th class="product header small" width="{$layout.tax.width}%">
				<span >{l s='Tax' mod='roja45quotationspro'}</span>
			</th>
			<th class="product header small" width="{$layout.tax_rate.width}%">
				<span >{l s='Tax Rate' mod='roja45quotationspro'}</span>
			</th>
		{/if}
	</tr>

	{foreach from=$requested_products item=product key=k}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
			<td class="product center">
				{if isset($product.image_tag) && !($product.image_missing)}<img src="{$product.image_url|escape:'html':'UTF-8'}" width=90 height=90 alt="{$product.product_title|escape:'html':'UTF-8'}" class="img img-thumbnail" />{/if}
			</td>
			<td class="product center">
				<span>{$product.product_title|escape:'html':'UTF-8'} {if $product.reference}({$product.reference|escape:'html':'UTF-8'}){/if}</span>
			</td>
			<td class="product center">
				{if (isset($product.customizations))}
					{foreach $product.customizations as $customization}
						<span> - {$customization.name|escape:'html':'UTF-8'} : {$customization.value|escape:'html':'UTF-8'}</span><br>
					{/foreach}
				{/if}
			</td>
			<td class="product center">
				<span>{if $use_tax}{$product.list_price_incl_formatted|escape:'html':'UTF-8'}{else}{$product.list_price_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
			</td>
			<td class="product center">
				<span>{$product.cart_quantity|escape:'html':'UTF-8'}</span>
			</td>
			<td class="product center">
				<span>{if $use_tax}{$product.product_price_list_subtotal_incl_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_list_subtotal_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
			</td>
			{if $use_tax}
				<td class="product center">
					<span>{$product.product_price_list_tax_formatted|escape:'html':'UTF-8'}</span>
				</td>
			{/if}
		</tr>
	{/foreach}

</table>
