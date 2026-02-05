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

<div id="_mobile_quotecart" class="_mobile_quotecart" data-position="custom">
    <div class="quotation_cart {if $nbr_products > 0}active{else}inactive{/if} collapsed">
        <a title="{l s='quotation cart' d='Modules.Roja45quotationspro.Shop'}"
           href="{if $nbr_products > 0}{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}{/if}"
           class="quotation-cart-link">
            <div class="header" data-refresh-url="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}">
                <span class="hidden-sm-down">{l s='quote' d='Modules.Roja45quotationspro.Shop'}</span>
                <span class="ajax_quote_quantity">{$request_qty}</span>
                {if $request_qty > 0}
                {if $roja45quotationspro_enable_quote_dropdown}
                    <div class="quote-header-element arrow_drop_down hidden-sm-down">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7 10l5 5l5-5z"/></svg>
                    </div>
                    <div class="quote-header-element arrow_drop_up hidden-sm-down">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7 14l5-5l5 5z"/></svg>
                    </div>
                {/if}
                {/if}
            </div>
        </a>
        {if isset($requested_products)}
        <div class="quote-cart-block dropdown-menu">
            <div class="block-content">
                <dl class="products">
                    {foreach from=$requested_products item=product}
                    <dt data-id="cart_block_product_3_13_0" class="first_item">
                        <a class="cart-images" href="{$product.link}" title="{$product.name}">
                            <img src="{$product.image_quote_cart}" alt="{$product.image_title}">
                        </a>
                        <div class="cart-info">
                            <div class="product-name">
                                <span class="product-quantity">{$product.qty_in_cart|escape:'html':'UTF-8'} <small>x</small></span>
                                <a class="cart_block_product_name" href="{$product.link}" title="{$product.name}">{$product.name}</a>
                            </div>
                            {if isset($product.attributes)}
                            <div class="product-atributes">
                                <a href="{$product.link}" title="Product detail">{$product.attributes}</a>
                            </div>
                            {/if}
                        </div>
                        <a class="remove-from-cart"
                            rel="nofollow"
                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'deleteProductFromRequest', 'ajax' => 1, 'id_roja45_quotation_request' => $product.id_roja45_quotation_request, 'id_roja45_quotation_requestproduct' => $product.id_roja45_quotation_requestproduct]}"
                            data-link-action="remove-from-cart">
                        </a>
                    </dt>
                    {/foreach}
                    <p class="cart-buttons">
                        <a class="btn btn-primary btn-request-quote" href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}" title="Check out" rel="nofollow">
                            <span>{l s='request quote' d='Modules.Roja45quotationspro.Shop'}</span>
                        </a>
                    </p>
                </dl>
            </div>
        </div>
        {/if}
    </div>
</div>


