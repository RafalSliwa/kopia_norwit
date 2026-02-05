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

<div id="roja45quotationspro_buttons_block" class="roja45quotationspro_button_container no-print">
    <div class="qty quote-product-quantity">
        <input
            type="text"
            name="quote_qty"
            id="quote_quantity_wanted"
            data-touchspin-vertical="{$roja45quotationspro_touchspin}"
            value="{if isset($product->product_attribute_minimal_quantity) && $product->product_attribute_minimal_quantity >= 1}{$product->product_attribute_minimal_quantity|intval}{else}{$product->minimal_quantity|intval}{/if}"
            class="input-group quote_quantity_wanted"
            min="{if isset($product->product_attribute_minimal_quantity) && $product->product_attribute_minimal_quantity >= 1}{$product->product_attribute_minimal_quantity|intval}{else}{$product->minimal_quantity|intval}{/if}"
            aria-label="{l s='Quantity' mod='roja45quotationspro'}">
    </div>
    <div class="add">
        <a class="btn btn-primary add-to-quote {if $roja45_quotation_enablequotecart}{if $roja45_quotation_useajax}ajax_add_quote_button{else}add_quote_button{/if}{/if}"
           href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'addToQuote', 'id_product' => $product->id, 'id_product_attribute' => $id_product_attribute, 'qty' => 1]}"
           rel="nofollow"
           title="{l s='Add to quote' mod='roja45quotationspro'}"
           data-url="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'addToQuote']}"
           data-id-product-attribute="{$id_product_attribute}"
           data-id-product="{$product->id}"
           data-cart-position="nav"
           data-minimal-quantity="{if isset($product->product_attribute_minimal_quantity) && $product->product_attribute_minimal_quantity >= 1}{$product->product_attribute_minimal_quantity|intval}{else}{$product->minimal_quantity|intval}{/if}">
            <svg xmlns="http://www.w3.org/2000/svg"><path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z"/></svg>
            {l s='Add to quote' mod='roja45quotationspro'}
        </a>
    </div>
</div>
