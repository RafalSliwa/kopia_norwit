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

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;font-size:9px;">
    <tr>
        <td width="50%">
            <span>{$customer_address}</span>
        </td>
        <td width="50%" style="text-align: right;">
            <span>{$shop_address}</span>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;font-size:9px;">
    <tr>
        <td width="50%">
            <p>{$customer_email|escape:'htmlall':'UTF-8'}</p>
        </td>
        <td width="50%" style="text-align: right;">
            <p>{$shop_email|escape:'htmlall':'UTF-8'}</p>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;font-size:9px;">
    <tr>
        <td width="100%" class="text-right">
            <p class="title">{l s='Many thanks for your request.' mod='roja45quotationspro'}<br/>{l s='We are pleased to provide below our quotation for the items you requested.' mod='roja45quotationspro'}</p>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <tr>
        <td width="100%" class="text-right">
            <h4 class="title">{l s='Your Quotation' mod='roja45quotationspro'}</h4>
        </td>
    </tr>
</table>
<table width="100%" id="body" border="0" cellpadding="2" cellspacing="0" style="margin:0;font-size:7px;">
    <tr style="width: 100%;">
        <th colspan=1 style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width: 10%;"></th>
        <th colspan=3 style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width: 25%;">
            <span >{l s='Product' mod='roja45quotationspro'}</span>
        </th>
        <th class="text-center" style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width: 20%;">
            <span >{l s='Customizations' mod='roja45quotationspro'}</span>
        </th>
        <th colspan=3 class="text-center" style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width: 15%;">
            <span >{l s='Unit Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
        <th colspan=3 class="text-center" style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width: 15%;">
            <span >{l s='Quantity' mod='roja45quotationspro'}</span>
        </th>
        <th colspan=3 class="text-center" style="text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;width:15%;">
            <span >{l s='Total Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
        </th>
    </tr>

    {foreach from=$requested_products item=product key=k}
        <tr>
            <td colspan=1 style="border:0px solid #D6D4D4;">
                {if isset($product.image_tag) && !($product.image_missing)}<img src="{$product.image_url|escape:'html':'UTF-8'}" width=90 height=90 alt="{$product.product_title|escape:'html':'UTF-8'}" class="img img-thumbnail" />{/if}
            </td>
            <td colspan=3 style="border:0px solid #D6D4D4;">
                <span>{$product.name|escape:'html':'UTF-8'} {if $product.reference}({$product.reference|escape:'html':'UTF-8'}){/if}</span>
            </td>
            <td style="border:0px solid #D6D4D4;">
                {if (isset($product.customizations))}
                    {foreach $product.customizations as $customization}
                        <span> - {$customization.name|escape:'html':'UTF-8'} : {$customization.value|escape:'html':'UTF-8'}</span><br>
                    {/foreach}
                {/if}
            </td>
            <td colspan=3 style="text-align: center;border:0px solid #D6D4D4;">
                <span>{if $use_tax}{$product.list_price_incl_formatted|escape:'html':'UTF-8'}{else}{$product.list_price_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
            </td>
            <td colspan=3 style="text-align: center;border:0px solid #D6D4D4;">
                <span>{$product.quote_quantity|escape:'html':'UTF-8'}</span>
            </td>
            <td colspan=3 style="text-align: center;border:0px solid #D6D4D4;">
                <span>{if $use_tax}{$product.product_price_list_subtotal_incl_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_list_subtotal_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
            </td>
        </tr>
    {/foreach}
</table>
<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <tr>
        <td class="space">&nbsp;</td>
    </tr>
</table>


<table width="100%" id="body" border="0" cellpadding="3" cellspacing="0" style="margin:0;font-size:8px;">
    {if ($template_data->use_taxes)}
        <tr id="total_before_discount">
            <td colspan=6 vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Sub-Total (inc.)' mod='roja45quotationspro'}</span>
            </th>
            <td colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
                <span>{$template_data->total_products_inc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_taxes">
            <td colspan=6 vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Taxes' mod='roja45quotationspro'}</span>
            </th>
            <td colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
                <span>{$template_data->total_tax_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_quotation">
            <td colspan=6 vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Total (inc.)' mod='roja45quotationspro'}</span>
            </th>
            <td colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
            <span>
                <strong>{$template_data->total_price_inc_formatted|escape:'htmlall':'UTF-8'}</strong>
            </span>
            </td>
        </tr>
    {else}
        <tr id="total_before_discount">
            <td colspan=6 vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Sub-Total Products (exc)' mod='roja45quotationspro'}</span>
            </th>
            <td colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
                <span>{$template_data->total_products_exc_formatted|escape:'htmlall':'UTF-8'}</span>
            </td>
        </tr>
        <tr id="total_quotation">
            <td colspan=6 vAlign=top style="width:50%;text-align: center;background-color: #ffffff;"></td>
            <th colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #f8f8f8;">
                <span>{l s='Total (exc)' mod='roja45quotationspro'}</span>
            </th>
            <td colspan=3 style="width:25%; text-align: center;border:0px solid #D6D4D4;background-color: #ffffff;">
            <span>
                <strong>{$template_data->total_price_exc_formatted|escape:'htmlall':'UTF-8'}</strong>
            </span>
            </td>
        </tr>
    {/if}
</table>
