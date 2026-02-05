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

<div id="_desktop_quotecart" class="_desktop_quotecart" data-position="top">
    <div class="quotation_cart {if $request_qty > 0}active{else}inactive{/if} collapsed">
        <a class="quotation-cart-link"
           title="{l s='Quotation Cart' mod='roja45quotationspro'}"
           href="{if $request_qty > 0}{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}{/if}"
           class="quotation-cart-link">
            <div class="header"
                 data-refresh-url="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}">
                <div class="quote-header-element quote-icon">
                    <svg xmlns="http://www.w3.org/2000/svg"><path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z"/></svg>
                </div>
                <div class="quote-header-element quote-text hidden-sm-down">{l s='Quote' mod='roja45quotationspro'}</div>
                <span class="ajax_quote_quantity">{$request_qty|escape:'html':'UTF-8'}</span>
                <span class="block_quote_expand{if !isset($colapseExpandStatus) || (isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded')} roja45_hidden{/if}">&nbsp;</span>
                <span class="block_quote_collapse{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'collapsed'} roja45_hidden{/if}">&nbsp;</span>
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
        {if isset($requested_products) && ($request_qty > 0)}
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
                        <a  class="remove-from-cart"
                            rel="nofollow"
                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'deleteProductFromRequest', 'ajax' => 1, 'id_roja45_quotation_request' => $product.id_roja45_quotation_request, 'id_roja45_quotation_requestproduct' => $product.id_roja45_quotation_requestproduct]}"
                            data-link-action="remove-from-cart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </a>
                    </dt>
                    {/foreach}
                    <p class="cart-buttons">
                        <a class="btn btn-primary btn-request-quote" href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'quoteSummary']}" title="Check out" rel="nofollow">
                            <span>{l s='Request Quote' mod='roja45quotationspro'}</span>
                        </a>
                    </p>
                </dl>
            </div>
        </div>
        {/if}
    </div>
</div>


