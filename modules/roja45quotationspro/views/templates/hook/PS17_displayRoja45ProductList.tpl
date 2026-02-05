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

<div class="roja45quotationspro roja45quotationspro_button_container product-list addtoquote">
    {if $roja45_quotation_enablequotecart}
    <form action="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront'}" method="post">
        <input type="text" class="input-group form-control quote_quantity_wanted" name="quote_qty_wanted" min="{$minimum_quantity}" value="{$minimum_quantity}">
        <input type="hidden" name="action" value="addToQuote">
        <input type="hidden" name="id_product" value="{$product.id_product}">
        <input type="hidden" name="minimal_quantity" value="{$minimum_quantity}">
        <button type="submit"
                class="btn btn-default btn-primary btn-addtoquote {if $roja45_quotation_useajax}ajax_add_quote_button{/if}"
                data-id-product="{$product.id_product}"
                data-id-product-attribute="{$product.id_product_attribute}"
                data-minimal-quantity="{$minimum_quantity}">{l s='Add To Quote' mod='roja45quotationspro'}</button>
    </form>
    {else}
    <form action="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront'}" method="post">
        <input type="text" class="input-group form-control quote_quantity_wanted" name="quote_qty_wanted" min="{$minimum_quantity}" value="{$minimum_quantity}">
        <input type="hidden" name="action" value="addToQuote">
        <input type="hidden" name="id_product" value="{$product.id_product}">
        <input type="hidden" name="minimal_quantity" value="{$minimum_quantity}">
        <button type="submit"
                class="btn btn-primary add-to-quote">
            <span>{l s='Get A Quote' mod='roja45quotationspro'}</span>
        </button>
    </form>
    {/if}
</div>
