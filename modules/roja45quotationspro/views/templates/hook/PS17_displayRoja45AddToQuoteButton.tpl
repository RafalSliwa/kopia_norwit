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
            {l s='Add to quote' mod='roja45quotationspro'}
        </a>
    </div>
</div>
