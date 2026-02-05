{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<div class="table-responsive">
    <table class="table">
        {assign var=quotation_discount_price value=0.00}
        {assign var=quotation_charges_price value=0.00}
        {assign var=quotation_wrapping_price value=0.00}
        {assign var=quotation_shipping_price value=0.00}
        {if ($show_exchange_rate == 1)}
            <tr id="exchange_rate">
                <td class="text-right">{l s='Exchange Rate' mod='roja45quotationspro'}
                    &nbsp;({$default_currency_symbol|escape:'html':'UTF-8'})
                </td>
                <td class="amount text-right nowrap">
                    {$exchange_rate|escape:'html':'UTF-8'}
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
        {/if}
        {if ($show_taxes==1)}
            <tr id="tax_rate">
                <td class="text-right">{l s='Tax Rate (%)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {$tax_average_rate|escape:'html':'UTF-8'}
                </td>
            </tr>

            <tr id="total_products">
                <td class="text-right">{l s='Total Products (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap" >
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_products_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_taxes">
                <td class="text-right">{l s='Taxes' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">{displayPrice price=Tools::ps_round(Tools::convertPrice($total_tax, $currency), 2) currency=$currency->id}</td>
                <td class="partial_refund_fields current-edit" style="display:none;"></td>
            </tr>
            <tr id="total_products">
                <td class="text-right"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">{l s='Total Products (inc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_products_inc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_discounts">
                <td class="text-right">{l s='Discounts' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_discounts_inc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_products_with_discount">
                <td class="text-right"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">{l s='Sub-Total' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_products_after_discount_wt, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_charges">
                <td class="text-right">{l s='Charges (inc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_charges_inc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_shipping">
                <td class="text-right">{l s='Shipping (inc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_shipping_inc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_handling">
                <td class="text-right" style="border-bottom:1px solid #666;background-color:#fff;">{l s='Handling (inc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap" style="border-bottom:1px solid #666;background-color:#fff;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_handling_inc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
        {else}
            <tr id="total_products">
                <td class="text-right" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">{l s='Sub-Total Products (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_products_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_discounts">
                <td class="text-right">{l s='Discounts' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_discounts_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_products_with_discount">
                <td class="text-right"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">{l s='Total Products (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_products_after_discount, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_charges">
                <td class="text-right">{l s='Charges (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_charges_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_shipping">
                <td class="text-right">{l s='Shipping (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_shipping_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
            <tr id="total_handling">
                <td class="text-right" style="border-bottom:1px solid #666;background-color:#fff;">{l s='Handling (exc.)' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap" style="border-bottom:1px solid #666;background-color:#fff;">
                    {displayPrice price=Tools::ps_round(Tools::convertPrice($total_handling_exc, $currency), 2) currency=$currency->id}
                </td>
            </tr>
        {/if}

        <tr id="total_quotation">
            <td class="text-right"
                style="background-color:#f4f8fb;">
                <strong>{l s='Total' mod='roja45quotationspro'}</strong>
            </td>
            <td class="amount text-right nowrap"
                style="background-color:#f4f8fb;">
                {if ($show_taxes)}
                    <strong>{displayPrice price=Tools::ps_round(Tools::convertPrice($total_price, $currency), 2) currency=$currency->id}</strong>
                {else}
                    <strong>{displayPrice price=Tools::ps_round(Tools::convertPrice($total_price_without_tax, $currency), 2) currency=$currency->id}</strong>
                {/if}
            </td>
        </tr>
    </table>
</div>
