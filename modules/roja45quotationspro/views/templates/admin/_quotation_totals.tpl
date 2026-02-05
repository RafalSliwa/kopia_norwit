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
                    &nbsp;({$quotation_currency_symbol|escape:'html':'UTF-8'}/{$default_currency_symbol|escape:'html':'UTF-8'})
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
        <tr id="total_products">
            <td class="text-right">{l s='Products' mod='roja45quotationspro'}
                {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
            </td>
            <td class="amount text-right nowrap">
                {if $use_taxes}{$total_price_before_discount_wt_formatted|escape:'htmlall':'UTF-8'}{else}{$total_price_before_discount_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        {if $total_customizations_exc > 0}
            <tr id="total_products">
                <td class="text-right">{l s='Customizations' mod='roja45quotationspro'}
                    {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
                </td>
                <td class="amount text-right nowrap">
                    {if $use_taxes}{$total_customizations_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_customizations_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
                </td>
            </tr>
        {/if}
        {if $total_ecotax_exc > 0}
            <tr id="total_products">
                <td class="text-right">{l s='Ecotax' mod='roja45quotationspro'}
                    {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
                </td>
                <td class="amount text-right nowrap">
                    {if $use_taxes}{$total_ecotax_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_ecotax_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
                </td>
            </tr>
        {/if}
        <tr id="total_discounts">
            <td class="text-right">{l s='Discounts' mod='roja45quotationspro'}
                {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
            </td>
            <td class="amount text-right nowrap">
                {if $use_taxes}{$total_discounts_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_discounts_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        {if ($use_taxes)}
            <tr id="total_taxes">
                <td class="text-right">{l s='Product Taxes' mod='roja45quotationspro'}</td>
                <td class="amount text-right nowrap">{$total_tax_products_formatted}</td>
                <td class="partial_refund_fields current-edit" style="display:none;"></td>
            </tr>
        {/if}-
        <tr id="total_products_with_discount">
            <td class="text-right" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                {l s='Subtotal' mod='roja45quotationspro'}
                {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
            </td>
            <td class="amount text-right nowrap"
                style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                {if $use_taxes}{$total_products_after_discount_wt_formatted|escape:'htmlall':'UTF-8'}{else}{$total_products_after_discount_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        <tr id="total_shipping">
            <td class="text-right">{l s='Shipping' mod='roja45quotationspro'}
                {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
            </td>
            <td class="amount text-right nowrap">
                {if $use_taxes}{$total_shipping_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_shipping_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        <tr id="total_handling">
            <td class="text-right" style="border-bottom:1px solid #666;background-color:#fff;">
                {l s='Handling' mod='roja45quotationspro'}
                {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
            </td>
            <td class="amount text-right nowrap" style="border-bottom:1px solid #666;background-color:#fff;">
                {if $use_taxes}{$total_handling_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_handling_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        {if $deposit_enabled}
            <tr id="total_deposit_to_pay">
                <td class="text-right" style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {l s='Total Deposit' mod='roja45quotationspro'}
                    {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}
                </td>
                <td class="amount text-right nowrap"
                    style="font-weight: bold;border-bottom:1px solid #666;background-color:#f4f8fb;">
                    {if $use_taxes}{$total_deposit_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_deposit_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
                </td>
            </tr>
        {/if}

        <tr id="total_taxes">
            <td class="text-right">{l s='Taxes' mod='roja45quotationspro'}</td>
            <td class="amount text-right nowrap">{$total_tax_formatted}</td>
            <td class="partial_refund_fields current-edit" style="display:none;"></td>
        </tr>
        <tr id="total_quotation">
            <td class="text-right" style="background-color:#f4f8fb;">
                <strong>{l s='Total' mod='roja45quotationspro'}
                    {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</strong>
            </td>
            <td class="amount text-right nowrap" style="background-color:#f4f8fb;">
                <strong>{if $use_taxes}{$total_price_formatted|escape:'htmlall':'UTF-8'}{else}{$total_price_without_tax_formatted|escape:'htmlall':'UTF-8'}{/if}</strong>
            </td>
        </tr>
    </table>
</div>