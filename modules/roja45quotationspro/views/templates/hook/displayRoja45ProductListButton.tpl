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

<div class="product-list roja45-addtoquote-container">
    <form action="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront'}" method="post">
        <input type="hidden" name="action" value="addToQuote">
        <input type="hidden" name="id_product" value="{$product.id_product}">
        <input type="hidden" name="minimal_quantity" value="{$minimal_quantity}">
        <div class="roja45quotationspro_button_container">
            <div class="quantity">
                <input type="text" class="input-group form-control quote_quantity_wanted" name="quote_quantity_wanted"
                    min="{$minimum_quantity}" data-touchspin-vertical="{$roja45quotationspro_touchspin}"
                    value="{$minimum_quantity}">
            </div>
            <button type="submit"
                class="btn btn-default btn-primary btn-addtoquote {if $roja45_quotation_enablequotecart}ajax_add_quote_button{/if}"
                data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}"
                data-cart-position="nav"
                data-minimal-quantity="{$minimal_quantity}">{l s='add to quote' d='Modules.Roja45quotationspro.Shop'}</button>
        </div>
    </form>
</div>