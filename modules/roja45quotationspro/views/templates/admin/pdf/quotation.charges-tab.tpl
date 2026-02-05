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
			<table id="charges-tab">
				{if count($charges)}
					<tr>
						<th class="charge header small" width="50%">
							<span>{l s='Charge' mod='roja45quotationspro'}</span>
						</th>
						<th class="charge header small" width="25%">
							<span>{l s='Value' mod='roja45quotationspro'}</span>
						</th>
						<th class="charge header small" width="25%">
							<span>{l s='Value (inc.)' mod='roja45quotationspro'}</span>
						</th>
					</tr>
					{foreach from=$charges item=charge}
						<tr data-id-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
							<td class="center">
								<span>{$charge['charge_name']|escape:"html":"UTF-8"}</span>
							</td>
							<td class="center">
								<span>{$charge['charge_amount_formatted']|escape:'htmlall':'UTF-8'}</span>
							</td>
							<td class="center">
								<span>{$charge['charge_amount_wt_formatted']|escape:'htmlall':'UTF-8'}</span>
							</td>
						</tr>
					{/foreach}
				{/if}
			</table>
		</td>
		<td>
			<table id="discounts-tab">
				{if count($discounts)}
					<tr>
						<th class="charge header small" width="50%">
							<span>{l s='Discount' mod='roja45quotationspro'}</span>
						</th>
						<th class="charge header small" width="50%">
							<span>{l s='Amount' mod='roja45quotationspro'}</span>
						</th>
					</tr>
					{foreach from=$discounts item=discount}
						<tr class="discount_row">
							<td class="center">
								<span>{$discount['charge_name']|escape:"html":"UTF-8"} : {if ($discount['charge_method']  =='PERCENTAGE')}{$discount['charge_value']|escape:"html":"UTF-8"|string_format:"%.2f"}%{elseif ($discount['charge_method']  =='AMOUNT')}{$discount['charge_value_formatted']|escape:'htmlall':'UTF-8'}{/if}</span>
							</td>
							<td class="center">
								<span>{if ($template_data->use_taxes)}{$discount['amount_wt_formatted']|escape:'htmlall':'UTF-8'}{else}{$discount['amount_formatted']|escape:'htmlall':'UTF-8'}{/if}</span>
							</td>
						</tr>
					{/foreach}
				{/if}
			</table>
		</td>
	</tr>
</table>



