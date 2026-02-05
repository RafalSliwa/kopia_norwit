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


<tr>
    <th class="product small" style="width: 12%;"></th>
    <th class="product small" style="width: 14%;">
        <span >{l s='Product' mod='roja45quotationspro'}</span>
    </th>
    <th class="product small" style="width: 14%;">
        <span >{l s='Customizations' mod='roja45quotationspro'}</span>
    </th>
    <th class="product small" style="width: 14%;">
        <span >{l s='Comment' mod='roja45quotationspro'}</span>
    </th>
    <th class="product small" style="width: 8%;">
        <span >{l s='Unit Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
    </th>
    <th class="product small" style="width: 8%;">
        <span >{l s='Offer Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
    </th>
    <th class="product small" style="width: 8%;">
        <span >{l s='Quantity' mod='roja45quotationspro'}</span>
    </th>
    <th class="product small" style="width: 8%;">
        <span >{l s='Total Price' mod='roja45quotationspro'} {if $use_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
    </th>
    {if $use_tax}
    <th class="product small" style="width: 6%;">
        <span >{l s='Tax' mod='roja45quotationspro'}</span>
    </th>
        <th class="product small" style="width: 8%;">
            <span >{l s='Tax Rate' mod='roja45quotationspro'}</span>
        </th>
    {/if}
</tr>

{foreach from=$requested_products item=product key=k}
    {if $use_tax}
        {assign var=product_price value=$product.unit_price_tax_excl}
        {assign var=product_price_formatted value=$product.unit_price_tax_excl_formatted}
        {assign var=product_price_subtotal_formatted value=$product.product_price_subtotal_excl_formatted}
    {else}
        {assign var=product_price value=$product.unit_price_tax_incl}
        {assign var=product_price_formatted value=$product.unit_price_tax_incl_formatted}
        {assign var=product_price_subtotal_formatted value=$product.unit_price_tax_incl_formatted}
    {/if}

<tr class="product">
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
        <span>{if $product.comment}{$product.comment|escape:'html':'UTF-8'}{/if}</span>
    </td>
    <td class="product center">
        <span>{if $use_tax}{$product.list_price_incl|escape:'html':'UTF-8'}{else}{$product.list_price_excl|escape:'html':'UTF-8'}{/if}</span>
    </td>
    <td class="product center">
        <span>{if $use_tax}{$product.unit_price_tax_incl_formatted|escape:'html':'UTF-8'}{else}{$product.unit_price_tax_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
    </td>
    <td class="product center">
        <span>{$product.qty|escape:'html':'UTF-8'}</span>
    </td>
    <td class="product center">
        <span>{if $use_tax}{$product.product_price_subtotal_incl_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_subtotal_excl_formatted|escape:'html':'UTF-8'}{/if}</span>
    </td>
    {if $use_tax}
        <td class="product center">
            <span>{$product.tax_paid_formatted|escape:'html':'UTF-8'}</span>
        </td>
        <td class="product center">
            <span>{$product.tax_rate_formatted|escape:'html':'UTF-8'}</span>
        </td>
    {/if}
</tr>
{/foreach}