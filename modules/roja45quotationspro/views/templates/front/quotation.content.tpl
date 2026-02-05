{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author          Roja45 <support@roja45.com>
*  @copyright       2016 roja45.com
*}

{$style_tab}

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">
            <span>{$customer_address}</span> {* HTML Content *}
        </td>
        <td width="50%" style="text-align: right;">
            <span>{$shop_address}</span> {* HTML Content *}
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">
            <p>{$customer_email|escape:'htmlall':'UTF-8'}</p>
        </td>
        <td width="50%" style="text-align: right;">
            <p>{$shop_email|escape:'htmlall':'UTF-8'}</p>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" class="text-right">
            <p class="title">{l s='Many thanks for your request.' mod='roja45quotationspro'}<br/>{l s='We are pleased to provide below our quotation for the items you requested.' mod='roja45quotationspro'}</p>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <tr>
        <td width="100%" class="text-right">
            <h4 class="title">{l s='Your Quotation' mod='roja45quotationspro'}</h4>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>
<table class="product" width="100%" id="body" border="0" cellpadding="2" cellspacing="0">
    {$products_tab} {* HTML Content *}
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>
{if (sizeof($charges)>0)}
<table width="100%" id="body" border="0" cellpadding="3" cellspacing="0">
    <tr>
        <th vAlign=top"></th>
        <th style="width:16.666%;">
            <span style="font-weight:500;">{l s='Charge' mod='roja45quotationspro'}</span>
        </th>
        <th style="width:16.666%;">
            <span>{l s='Value' mod='roja45quotationspro'}</span>
        </th>
        <th style="width:16.666%;">
            <span>{l s='Value (inc.)' mod='roja45quotationspro'}</span>
        </th>
    </tr>
    {foreach from=$charges item=charge}
    <tr class="charge_row" data-id-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
        <td vAlign=top></td>
        <td style="width:16.666%; ">
            <span>{$charge['charge_name']|escape:"html":"UTF-8"}</span>
        </td>
        <td style="width:16.666%;">
            <span>{$charge['charge_amount_formatted']|escape:'htmlall':'UTF-8'}</span>
        </td>
        <td style="width:16.666%;">
            <span>{$charge['charge_amount_wt_formatted']|escape:'htmlall':'UTF-8'}</span>
        </td>
    </tr>
    {/foreach}
</table>
    <table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
        <tr>
            <td class="space">&nbsp;</td>
        </tr>
    </table>
{/if}
{if (sizeof($discounts))}
    <table width="100%" id="body" border="0" cellpadding="3" cellspacing="0" style="margin:0;margin:0;font-size:8px;">
        <tr>
            <th vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></th>
            <th style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Discount' mod='roja45quotationspro'}</span>
            </th>
            <th style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Amount' mod='roja45quotationspro'}</span>
            </th>
        </tr>
        {foreach from=$discounts item=discount}
        <tr class="discount_row">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <td style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
                <span>{$discount['charge_name']|escape:"html":"UTF-8"} : {if ($discount['charge_method']  =='PERCENTAGE')}{$discount['charge_value']|escape:"html":"UTF-8"|string_format:"%.2f"}%{elseif ($discount['charge_method']  =='AMOUNT')}{$discount['charge_value_formatted']|escape:'htmlall':'UTF-8'}{/if}</span>
            </td>
            <td style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
                <span>{if ($show_taxes)}{$discount['amount_wt_formatted']|escape:'htmlall':'UTF-8'}{else}{$discount['amount_formatted']|escape:'htmlall':'UTF-8'}{/if}</span>
            </td>
        </tr>
        {/foreach}
    </table>
    <table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
        <tr>
            <td class="space">&nbsp;</td>
        </tr>
    </table>
{/if}

<table width="100%" id="total-tab" border="0" cellpadding="3" cellspacing="0" style="margin:0;font-size:8px;">
    {if ($show_exchange_rate == 1)}
        <tr id="exchange_rate">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Exchange Rate' mod='roja45quotationspro'} ({$default_currency_symbol|escape:'html':'UTF-8'})</span>
            </td>
            <td class="totals-value" style="width:25%;">
                <span>{$exchange_rate|escape:'html':'UTF-8'}</span>
            </td>
        </tr>
    {/if}

    {if ($show_taxes)}
        <tr id="total_before_discount">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Sub-Total (exc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_products_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {if $total_discounts > 0}
            <tr id="total_discounts">
                <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
                <th class="totals-title" style="width:25%;">
                    <span>{l s='Discounts' mod='roja45quotationspro'}</span>
                </th>
                <td class="totals-value" style="width:25%;">
                    <span>{$total_discounts_inc_formatted|escape:'htmlall':'UTF-8'}</span>
                </td>
            </tr>
        {/if}
        {if $total_charges_inc > 0}
        <tr id="total_charges">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Charges (inc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_charges_inc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {/if}
        <tr id="total_shipping">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Shipping (inc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_shipping_inc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {if $total_handling_inc > 0}
        <tr id="total_handling">
            <td vAlign=top style="width:50%;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Handling (inc. tax)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_handling_wt_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {/if}
        <tr id="total_products">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Total Products' mod='roja45quotationspro'}</span></th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_products_after_discount_wt_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_taxes">
            <td vAlign=top style="width:50%;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Taxes' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_tax_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>

        <tr id="total_quotation">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Total' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
            <span>
                <strong>{$total_price_formatted|escape:'htmlall':'UTF-8'}</strong>
            </span>
            </td>
        </tr>
    {else}
        <tr id="total_before_discount">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Sub-Total Products (exc)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_products_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_discounts">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Discounts' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_discounts_exc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {if $total_charges_exc > 0}
        <tr id="total_charges">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Charges (exc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_charges_exc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {/if}
        <tr id="total_shipping">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Shipping (exc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_shipping_exc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {if $total_charges > 0}
        <tr id="total_handling">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Handling (exc.)' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value">
                <span>{$total_handling_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        {/if}
        <tr id="total_products">
            <td vAlign=top></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Total Products' mod='roja45quotationspro'}</span></th>
            <td class="totals-value" style="width:25%;">
                <span>{$total_products_after_discount_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_quotation">
            <td vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th class="totals-title" style="width:25%;">
                <span>{l s='Total' mod='roja45quotationspro'}</span>
            </th>
            <td class="totals-value" style="width:25%;">
            <span>
                <strong>{$total_price_without_tax_formatted|escape:'htmlall':'UTF-8'}</strong>
            </span>
            </td>
        </tr>
    {/if}
</table>
