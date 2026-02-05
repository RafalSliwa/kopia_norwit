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

<div class="quotation_cart nav {if $nbr_products > 0} has_products{/if} collapsed">
    <a href="{$request_link|escape:'html':'UTF-8'}"
       title="{l s='View my request' mod='roja45quotationspro'}" rel="nofollow">
        <span class="ajax_quote_txt">{l s='My Quote' mod='roja45quotationspro'}</span>
        <span class="ajax_quote_quantity{if $nbr_products == 0} unvisible{/if}">{$request_qty|escape:'html':'UTF-8'}</span>
        <span class="ajax_quote_product_txt{if $nbr_products != 1} unvisible{/if}">{l s='Product' mod='roja45quotationspro'}</span>
        <span class="ajax_quote_product_txt_s{if $nbr_products < 2} unvisible{/if}">{l s='Products' mod='roja45quotationspro'}</span>
        <span class="ajax_quote_no_product{if $nbr_products > 0} unvisible{/if}">{l s='(empty)' mod='roja45quotationspro'}</span>
        <span class="block_quote_expand{if !isset($colapseExpandStatus) || (isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded')} unvisible{/if}">&nbsp;</span>
        <span class="block_quote_collapse{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'collapsed'} unvisible{/if}">&nbsp;</span>
    </a>
    <div class="roja45quotationspro_block block exclusive">
        <div class="block_content">
            <!-- block list of products -->
            <div class="quote_block_list">
                {if isset($requested_products)}
                    <dl class="products">
                        {foreach from=$requested_products item='product' name='myLoop'}
                            {assign var='productId' value=$product.id_product}
                            {assign var='productAttributeId' value=$product.id_product_attribute}
                            <dt data-id="quote_block_product_{$product.id_product|intval}_{if $product.id_product_attribute}{$product.id_product_attribute|intval}{else}0{/if}"
                                class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                                <a class="quote-product-image"
                                   href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'html':'UTF-8'}"
                                   title="{$product.name|escape:'html':'UTF-8'}">
                                    <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')|escape:'html':'UTF-8'}"
                                         alt="{$product.name|escape:'html':'UTF-8'}"/></a>
                            <div class="quote-product-info">
                                <div class="product-name">
                                    <span class="quantity-formated">
                                        <span class="quantity">{$product.quote_quantity|escape:'html':'UTF-8'|escape:'html':'UTF-8'}</span>&nbsp;x&nbsp;</span>
                                        <a class="quote_block_product_name"
                                           href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}"
                                           title="{$product.name|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                                </div>
                                {if isset($product.attributes)}
                                    <div class="product-atributes">
                                        <a href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}"
                                           title="{l s='Product detail' mod='roja45quotationspro'}">{$product.attributes|escape:'html':'UTF-8'}</a>
                                    </div>
                                {/if}
                            </div>
                            <span class="remove_link">
                                {if !isset($customizedDatas.$productId.$productAttributeId) && (!isset($product.is_gift) || !$product.is_gift)}
                                    <a class="ajax_quote_block_remove_link"
                                       href="#"
                                       rel="nofollow"
                                       title="{l s='remove this product from my cart' mod='roja45quotationspro'}">&nbsp;</a>
                                {/if}
                            </span>
                            </dt>
                            {if isset($product.attributes_small)}
                                <dd data-id="cart_block_combination_of_{$product.id_product|intval}{if $product.id_product_attribute}_{$product.id_product_attribute|intval}{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
                            {/if}
                            {if isset($product.attributes_small)}</dd>{/if}
                        {/foreach}
                    </dl>
                {else}
                <p class="quote_block_no_products{if $products} unvisible{/if}">
                    {l s='No products' mod='roja45quotationspro'}
                </p>
                {/if}
                <p class="quote-buttons">
                    <a id="button_request_quote" class="btn btn-primary"
                       href="{$request_link|escape:'html':'UTF-8'}"
                       title="{l s='Request Quote' mod='roja45quotationspro'}" rel="nofollow">
                        <span>
                            {l s='Request Quote' mod='roja45quotationspro'}<i class="icon-chevron-right right"></i>
                        </span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
{if ($roja45_quotation_enablequotecart==1) && ($roja45quotationspro_enable_inquotenotify == 1)}
<div id="layer_quote">
    <div class="clearfix">
        <div class="layer_quote_product col-xs-12 col-md-6">
            <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            <span class="title">
                 {if $roja45quotationspro_iconpack=='1'}<i class="icon-check"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-check"></i>{else}<i class="icon-check"></i>{/if}{l s='Product successfully added to your quote' mod='roja45quotationspro'}
            </span>
            <div class="product-image-container layer_quote_img">
            </div>
            <div class="layer_quote_product_info">
                <span id="layer_quote_product_title" class="product-name"></span>
                <span id="layer_quote_product_attributes"></span>
                <div>
                    <strong class="dark">{l s='Quantity Available' mod='roja45quotationspro'}</strong>
                    <span id="layer_quote_product_quantity"></span>
                </div>
            </div>
        </div>
        <div class="layer_quote_cart col-xs-12 col-md-6">
            <span class="title">
                <!-- Plural Case [both cases are needed because page may be updated in Javascript] -->
                <span class="modal_quote_product_txt_s {if $nbr_products < 2} unvisible{/if}">
                    {l s='There are [1]%d[/1] products in your quote.' mod='roja45quotationspro' sprintf=[$nbr_products] tags=['<span class="ajax_quote_quantity">']}
                </span>
                <!-- Singular Case [both cases are needed because page may be updated in Javascript] -->
                <span class="modal_quote_product_txt {if $nbr_products > 1} unvisible{/if}">
                    {l s='There is 1 product in your quote.' mod='roja45quotationspro'}
                </span>
            </span>

            <div class="button-container">
                <span class="continue btn btn-default button exclusive-medium" title="{l s='Continue shopping' mod='roja45quotationspro'}">
                    <span>
                        {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-left left"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-left"></i>{else}<i class="icon-chevron-left left"></i>{/if}
                        {l s='Continue shopping' mod='roja45quotationspro'}
                    </span>
                </span>
                <a class="btn btn-default button button-medium"	href="{$request_link|escape:"html":"UTF-8"}" title="{l s='Request Quote' mod='roja45quotationspro'}" rel="nofollow">
                    <span>
                        {l s='Request Quote' mod='roja45quotationspro'}{if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
                    </span>
                </a>
            </div>
        </div>
    </div>
    <div class="crossseling"></div>
</div> <!-- #layer_quote -->
<div class="layer_quote_overlay"></div>
{/if}
<!-- /MODULE Block cart -->