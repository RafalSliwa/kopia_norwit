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

{if !$roja45_quotation_hideaddtocart}
    <div class="product-add-to-cart">
        {if !$configuration.is_catalog}
            <span class="control-label">{l s='Quantity' d='Shop.Theme.Catalog'}</span>

            {block name='product_quantity'}
                <div class="product-quantity clearfix">
                    <div class="qty">
                        <input
                                type="number"
                                name="qty"
                                id="quantity_wanted"
                                value="{$product.quantity_wanted}"
                                class="input-group"
                                min="{$product.minimal_quantity}"
                                aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
                        >
                    </div>

                    <div class="add">
                        <button
                                class="btn btn-primary add-to-cart"
                                data-button-action="add-to-cart"
                                type="submit"
                                {if !$product.add_to_cart_url}
                                    disabled
                                {/if}
                        >
                            <i class="material-icons shopping-cart">&#xE547;</i>
                            {l s='Add to cart' d='Shop.Theme.Actions'}
                        </button>
                    </div>

                    {hook h='displayProductActions' product=$product}
                </div>
            {/block}

            {block name='product_availability'}
                <span id="product-availability">
        {if $product.show_availability && $product.availability_message}
            {if $product.availability == 'available'}
                <i class="material-icons rtl-no-flip product-available">&#xE5CA;</i>
          {elseif $product.availability == 'last_remaining_items'}
            <i class="material-icons product-last-items">&#xE002;</i>
          {else}
            <i class="material-icons product-unavailable">&#xE14B;</i>
            {/if}
            {$product.availability_message}
        {/if}
      </span>
            {/block}

            {block name='product_minimal_quantity'}
                <p class="product-minimal-quantity">
                    {if $product.minimal_quantity > 1}
                        {l
                        s='The minimum purchase order quantity for the product is %quantity%.'
                        d='Shop.Theme.Checkout'
                        sprintf=['%quantity%' => $product.minimal_quantity]
                        }
                    {/if}
                </p>
            {/block}
        {/if}
    </div>
{/if}
<div id="roja45quotationspro_buttons_block" class="roja45quotationspro_button_container no-print">
    <div class="qty">
        <input
                type="text"
                name="quote_qty"
                id="quote_quantity_wanted"
                data-touchspin-vertical="{$roja45quotationspro_touchspin}"
                value="{if isset($product->product_attribute_minimal_quantity) && $product->product_attribute_minimal_quantity >= 1}{$product->product_attribute_minimal_quantity|intval}{else}{$product->minimal_quantity|intval}{/if}"
                class="input-group quote_quantity_wanted"
                min="{if isset($product->product_attribute_minimal_quantity) && $product->product_attribute_minimal_quantity >= 1}{$product->product_attribute_minimal_quantity|intval}{else}{$product->minimal_quantity|intval}{/if}"
                aria-label="{l s='Quantity' mod='roja45quotationspro'}"
        >
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