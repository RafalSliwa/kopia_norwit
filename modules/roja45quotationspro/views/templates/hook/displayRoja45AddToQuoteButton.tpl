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

<div id="add_to_quote_container">
    <div id="roja45quotationspro_buttons_block" class="">
        <div id="quantity_wanted_p">
            <div class="quantity-input-wrapper">
                <input type="text" name="quote_qty" id="quote_quantity_wanted" class="text" value="1">
                <a href="#" data-field-qty="qty" class="transition-300 product_quantity_down">
                    <span><i class="icon-caret-down"></i></span>
                </a>
                <a href="#" data-field-qty="qty" class="transition-300 product_quantity_up ">
                    <span><i class="icon-caret-up"></i></span>
                </a>
            </div>
            <span class="clearfix"></span>
        </div>
        <p id="request_quote" class="buttons_bottom_block no-print">
            {if $roja45_quotation_enablequotecart}
            <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'addToQuote'],true)|escape:'htmlall':'UTF-8'}"
               id="{if $roja45_quotation_useajax}ajax_add_quote_button{else}add_quote_button{/if}"
               class="btn btn-block btn-quote {if $roja45_quotation_useajax}ajax_add_quote_button{/if}"
               data-id-product="{$product->id|escape:'htmlall':'UTF-8'}"
               data-id-product-attribute="{$id_product_attribute|escape:'htmlall':'UTF-8'}"
               data-minimal-quantity="{$minimal_quantity|escape:'htmlall':'UTF-8'}">
                <span>{l s='Add To Quote' mod='roja45quotationspro'}</span>
            </a>
            {else}
            <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'addToQuote'],true)|escape:'htmlall':'UTF-8'}"
               id="{if $roja45_quotation_useajax}ajax_add_quote_button{else}add_quote_button{/if}"
               class="btn btn-block btn-quote {if $roja45_quotation_useajax}ajax_add_quote_button{/if}"
               data-id-product="{$product->id|escape:'htmlall':'UTF-8'}"
               data-id-product-attribute="{$id_product_attribute|escape:'htmlall':'UTF-8'}"
               data-minimal-quantity="{$minimal_quantity|escape:'htmlall':'UTF-8'}">
                <span>{l s='Add To Quote' mod='roja45quotationspro'}</span>
            </a>
            {/if}
        </p>
    </div>
</div>

