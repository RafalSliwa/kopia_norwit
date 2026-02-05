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
<div class="quotation-products table" id="quotationProducts">
    {if ($show_exchange_rate == 1)}
    <div class="tr">
        <div class="th">{l s='Exchange Rate' mod='roja45quotationspro'} ({$default_currency_symbol|escape:'html':'UTF-8'})</div>
        <div class="td">{$exchange_rate|escape:'html':'UTF-8'}</div>
    </div>
    {/if}
    <div class="tr">
        <div class="th">{l s='Sub-Total' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td"> {if $show_taxes}{$total_products_inc_formatted|escape:'html':'UTF-8'}{else}{$total_products_exc_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    {if $show_taxes}
    <div class="tr">
        <div class="th">{l s='Taxes' mod='roja45quotationspro'}</div>
        <div class="td">{$total_tax_formatted|escape:'html':'UTF-8'}</div>
    </div>
    {/if}
    <div class="tr">
        <div class="th">{l s='Discounts' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td">{if $show_taxes}{$total_discounts_inc_formatted|escape:'html':'UTF-8'}{else}{$total_discounts_exc_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    <div class="tr">
        <div class="th">{l s='Total Products' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td">{if $show_taxes}{$total_products_after_discount_wt_formatted|escape:'html':'UTF-8'}{else}{$total_products_after_discount_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    <div class="tr">
        <div class="th">{l s='Charges' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td">{if $show_taxes}{$total_charges_inc_formatted|escape:'html':'UTF-8'}{else}{$total_charges_exc_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    <div class="tr">
        <div class="th">{l s='Shipping' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td">{if $show_taxes}{$total_shipping_inc_formatted|escape:'html':'UTF-8'}{else}{$total_shipping_exc_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    <div class="tr">
        <div class="th">{l s='Handling' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
        <div class="td">{if $show_taxes}{$total_handling_inc_formatted|escape:'html':'UTF-8'}{else}{$total_handling_exc_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
    <div class="tr">
        <div class="th">{l s='Total' mod='roja45quotationspro'}</div>
        <div class="td">{if $show_taxes}{$total_price_formatted|escape:'html':'UTF-8'}{else}{$total_price_without_tax_formatted|escape:'html':'UTF-8'}{/if}</div>
    </div>
</div>
