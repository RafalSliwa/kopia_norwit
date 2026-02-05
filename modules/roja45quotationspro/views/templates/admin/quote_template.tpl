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

<table class="table" bgcolor="#ffffff" style="width:100%;border:1px solid #D6D4D4;background-color: #ffffff;" cellSpacing=0 cellPadding=0>
    <tr>
        <th colspan=1 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;"></th>
        <th colspan=3 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Product' mod='roja45quotationspro'}</th>
        <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Reference' mod='roja45quotationspro'}</th>
        <th colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Comment' mod='roja45quotationspro'}</th>
        <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">
            <div>{l s='Unit price' mod='roja45quotationspro'}</div>
            <small>{if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</small>
        </th>
        <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Quantity' mod='roja45quotationspro'}</th>
        <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">
            <div>{l s='Total price' mod='roja45quotationspro'}</div>
            <small>{if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</small>
        </th>
        {if $use_taxes}
            <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Tax' mod='roja45quotationspro'}</th>
            <th align="center" colspan=2 bgcolor="#f8f8f8" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;padding: 10px;">{l s='Rate' mod='roja45quotationspro'}</th>
        {/if}
    </tr>
    {foreach from=$quotation_products item=product key=k}
        {include file='./quote_template_product_line.tpl'}
    {/foreach}
</table>
<table class="table" bgcolor="#ffffff" style="border: 0;width:100%" cellSpacing=0 cellPadding=0 border="0">
    <tr>
        <td border="0" align="left" class="titleblock" style="padding:5px"></td>
    </tr>
</table>


{if (sizeof($charges))}
<table class="table" bgcolor="#ffffff" style="width:100%;border:0px;background-color: #ffffff;" cellSpacing=0 cellPadding=0 border="0">
    <tr>
        <th vAlign=top border="0" style="border:0;background-color: #ffffff;width:50%;">
        </th>
        <th colspan=2 align="center" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Charge' mod='roja45quotationspro'}</span>
        </th>
        <th colspan=2 align="center" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Type' mod='roja45quotationspro'}</span>
        </th>
        {if $use_taxes}
            <th colspan=2 align="center" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Value (inc.)' mod='roja45quotationspro'}</span>
            </th>
        {else}
            <th colspan=2 align="center" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Value (exc.)' mod='roja45quotationspro'}</span>
            </th>
        {/if}
    </tr>
    {foreach from=$charges item=charge}
        {if !$charge['charge_type']=='SHIPPING'}
    <tr class="charge_row" data-id-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
        <td vAlign=top border="0" style="border:0;border-bottom: 0;background-color: #ffffff;width:50%;">
        </td>
        <td colspan=2 align="center" class="amount nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$charge['charge_name']|escape:"html":"UTF-8"}</span>
        </td>
        {if $charge['charge_type']=='HANDLING'}
            <td colspan=2 align="center" class="amount nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Handling' mod='roja45quotationspro'}</span>
            </td>
        {/if}
        {if $use_taxes}
            <td colspan=2 align="center" class="amountnowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$charge['charge_amount_wt_formatted']|escape:'htmlall':'UTF-8'}</span>
            </td>
        {else}
            <td colspan=2 align="center" class="amount nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$charge['charge_amount_formatted']|escape:'htmlall':'UTF-8'}</span>
            </td>
        {/if}
    </tr>
        {/if}
    {/foreach}
</table>
<table class="table" bgcolor="#ffffff" style="border: 0;width:100%" cellSpacing=0 cellPadding=0 border="0">
    <tr>
        <td border="0" align="left" class="titleblock" style="padding:5px"></td>
    </tr>
</table>
{/if}
{if (sizeof($discounts))}
<table class="table" bgcolor="#ffffff" style="width:100%;border:0px;background-color: #ffffff;" cellSpacing=0 cellPadding=0 border="0">
    <tr>
        <th border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;width:50%;">
        </th>
        <th width="25%" align="center" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-top: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;border-left: 1px solid #d6d4d4;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Discount' mod='roja45quotationspro'}</span>
        </th>
        <th width="25%" align="center" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-top: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Amount' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
    </tr>
    {foreach from=$discounts item=discount}
        <tr class="discount_row">
            <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;width:50%;">
            </td>
            <td width="25%" align="center" class="amount text-left nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$discount['charge_name']|escape:"html":"UTF-8"} : {if ($discount['charge_method']  =='PERCENTAGE')}{$discount['charge_value']|escape:"html":"UTF-8"|string_format:"%.2f"}%{elseif ($discount['charge_method']  =='AMOUNT')}{$discount['charge_value_formatted']|escape:'htmlall':'UTF-8'}{/if}</span>
            </td>
            <td width="25%" align="center" class="amount text-left nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if $use_taxes}{$discount['amount_wt_formatted']|escape:'htmlall':'UTF-8'}{else}{$discount['amount_formatted']|escape:'htmlall':'UTF-8'}{/if}
            </span>
            </td>
        </tr>
    {/foreach}
</table>
<table class="table" bgcolor="#ffffff" style="border: 0;width:100%">
    <tr>
        <td border="0" align="left" class="titleblock" style="padding:5px"></td>
    </tr>
</table>
{/if}

<table class="table" bgcolor="#ffffff" style="width:100%;border:0px;background-color: #ffffff;" cellSpacing=0 cellPadding=0 border=0>
    {if ($show_exchange_rate == 1)}
    <tr id="exchange_rate">
        <td  width="50%" vAlign=top style="border:0;background-color: #ffffff;width:50%;"></td>
        <th  width="25%" class="text-right" style="border:1px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;border-left: 1px solid #d6d4d4;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Exchange Rate' mod='roja45quotationspro'} ({$default_currency_symbol|escape:'html':'UTF-8'})</span>
        </th>
        <td  width="25%" class="amount text-right nowrap" style="border:1px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$exchange_rate|escape:'html':'UTF-8'}</span>
        </td>
    </tr>
    {/if}

    <tr id="total_before_discount" {if $total_products_exc == 0}style="display: none;"{/if}>
        <th border="0" vAlign=top width="50%" style="border:0;background-color: #ffffff;width:50%;"></th>
        <th align="center" width="25%" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Subtotal' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
        <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if $use_taxes}{$total_products_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_products_exc_formatted|escape:'htmlall':'UTF-8'}{/if}</span>
        </td>
    </tr>
    {if $use_taxes}
        <tr id="total_taxes">
            <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
            <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Tax' mod='roja45quotationspro'}</span>
            </th>
            <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{$total_tax_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
    {/if}

    {if $total_discounts_exc > 0}
        <tr id="total_discounts">
            <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
            <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Discounts' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
            </th>
            <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                {if $use_taxes}{$total_discounts_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_discounts_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>
        <tr id="total_before_discount" {if $total_products_exc == 0}style="display: none;"{/if}>
            <th border="0" vAlign=top width="50%" style="border:0;background-color: #ffffff;width:50%;"></th>
            <th align="center" width="25%" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Subtotal' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
            </th>
            <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-top: 1px solid #d6d4d4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                {if $use_taxes}{$total_products_after_discount_wt_formatted|escape:'htmlall':'UTF-8'}{else}{$total_products_after_discount_formatted|escape:'htmlall':'UTF-8'}{/if}
            </td>
        </tr>

    {/if}
    {if $total_charges_exc > 0}
    <tr id="total_charges">
        <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
        <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Charges' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
        <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            {if $use_taxes}{$total_charges_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_charges_exc_formatted|escape:'htmlall':'UTF-8'}{/if}
        </td>
    </tr>
    {/if}
    <tr id="total_shipping">
        <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
        <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Shipping' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
        <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if $use_taxes}{$total_shipping_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_shipping_exc_formatted|escape:'htmlall':'UTF-8'}{/if}</span>
        </td>
    </tr>
    {if $total_handling_exc > 0}
    <tr id="total_handling">
        <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
        <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
            <span style="color: #333;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Handling' mod='roja45quotationspro'}{if $use_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
        <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
            <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if $use_taxes}{$total_handling_inc_formatted|escape:'htmlall':'UTF-8'}{else}{$total_handling_exc_formatted|escape:'htmlall':'UTF-8'}{/if}</span>
        </td>
    </tr>
    {/if}

    {if $use_taxes}
        <tr id="total_quotation">
            <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
            <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Total' mod='roja45quotationspro'}</span>
            </th>
            <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">
                    <strong>{$total_price_formatted|escape:'htmlall':'UTF-8'}</strong>
                </span>
            </td>
        </tr>
        {else}
        <tr id="total_quotation">
            <td border="0" width="50%" vAlign=top style="border:0;background-color: #ffffff;"></td>
            <th align="center" width="25%" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{l s='Total' mod='roja45quotationspro'}</span>
            </th>
            <td align="center" width="25%" class="amount text-right nowrap" style="border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;border-left: 1px solid #d6d4d4;border-right: 1px solid #d6d4d4;background-color: #ffffff;padding: 10px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">
                    <strong>{$total_price_without_tax_formatted|escape:'htmlall':'UTF-8'}</strong>
                </span>
            </td>
        </tr>
    {/if}
</table>

{if ($quotation_expiry_date && $quotation_expiry_date != '0000-00-00 00:00:00')}
    <table class="table" bgcolor="#ffffff" style="width:100%">
        <tr>
            <td style="padding:7px 0">
                <p style="font-family: Helvetica, 'Open Sans', Arial, sans-serif;border:0px solid #D6D4D4;color: #c70005;margin:3px 0 7px;font-weight:500;font-size:14px;padding-bottom:10px">{l s='This quote will expire on %s at %s' sprintf=[$quotation_expiry_date|escape:'html':'UTF-8', $quotation_expiry_time|escape:'html':'UTF-8'] mod='roja45quotationspro'}</p>
            </td>
        </tr>
    </table>
{/if}
{if ($exchange_rate != 1)}
<table class="table" bgcolor="#ffffff" style="width:100%">
    <tr>
        <td class="linkbelow" style="padding:7px 0">
            <p style="font-family: Helvetica, 'Open Sans', Arial, sans-serif;border-bottom:1px solid #D6D4D4;margin:3px 0 7px;font-weight:500;font-size:14px;padding-bottom:10px">{l s='Your quote has been provided in your requested currency.  Please be aware that currency fluctuations may result in changes to the price you have been quoted.  We reserve the right to change or cancel this quote at any time.' mod='roja45quotationspro'}</p>
        </td>
    </tr>
</table>
{/if}