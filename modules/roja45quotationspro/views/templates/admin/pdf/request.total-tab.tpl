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

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<tr>
		<td>
			<table width="50%" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
				<tr id="total_before_discount">
					<td class="white" width="50%">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td>
			<table id="total-tab" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
				{if ($show_exchange_rate == 1)}
					<tr id="exchange_rate">
						<th class="grey" width="50%">
							<span>{l s='Exchange Rate' mod='roja45quotationspro'} ({$default_currency_symbol|escape:'html':'UTF-8'})</span>
						</td>
						<td class="white" width="50%">
							<span>{$exchange_rate|escape:'html':'UTF-8'}</span>
						</td>
					</tr>
				{/if}
				<tr id="total_products">
					<td class="grey" width="50%">
						<span>{l s='Total Products' mod='roja45quotationspro'} {if ($show_taxes)}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
					</td>
					<td class="white" width="50%">
						<span>{if ($show_taxes)}{$total_products_inc_formatted}{else}{$total_products_exc_formatted}{/if}</span>
					</td>
				</tr>

				<tr id="total_taxes">
					<td class="grey" width="50%">
						<span>{l s='Taxes' mod='roja45quotationspro'}</span>
					</td>
					<td class="white" width="50%">
						<span>{$quotation_tax}</span>
					</td>
				</tr>
				<tr id="total_quotation">
					<td class="grey" width="50%">
						<span>{l s='Total' mod='roja45quotationspro'}  {if ($show_taxes)}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
					</td>
					<td class="white" width="50%">
						<span>
							<span>{if ($show_taxes)}{$total_products_inc_formatted}{else}{$total_products_formatted}{/if}</span>
						</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
