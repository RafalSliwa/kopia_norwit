{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*}

<div class="button-container">
    {if $roja45_quotation_enablequotecart}
        {if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}
            {assign var="min_qty" value=$product.product_attribute_minimal_quantity}
        {elseif isset($product.minimal_quantity) && $product.minimal_quantity}
            {assign var="min_qty" value=$product.minimal_quantity}
        {else}
            {assign var="min_qty" value=1}
        {/if}
        <a class="button ajax_add_quote_button btn btn-default btn-quote"
           href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action' => 'addToQuote','id_product' => $id_product,'id_product_attribute' => $id_product_attribute,'minimum_quantity' => $min_qty],true)|escape:'html':'UTF-8'}"
           rel="nofollow"
           title="{l s='Add to quote' mod='roja45quotationspro'}"
           data-id-product-attribute="{$product.id_product_attribute|intval|escape:'html':'UTF-8'}"
           data-id-product="{$product.id_product|intval|escape:'html':'UTF-8'}"
           data-minimal-quantity="{$min_qty|escape:'html':'UTF-8'}">
            <span>{l s='Add to quote' mod='roja45quotationspro'}</span>
        </a>
    {else}
        <a class="button btn btn-default btn-quote"
           href="{$product.link|escape:'html':'UTF-8'}"">
            <span>{l s='Get a quote' mod='roja45quotationspro'}</span>
        </a>
    {/if}
</div>