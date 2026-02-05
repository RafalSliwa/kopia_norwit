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

<tr id="product_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
    class="quote_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if $displayQuantity == 0} alternate_item{/if} {if $odd}odd{else}even{/if}">
    <td class="quote_product">
        <a href="{$product.link|escape:'html':'UTF-8'}">
            <img src="{$product.image_quote|escape:'html':'UTF-8'}"
                 alt="{$product.name|escape:'html':'UTF-8'}"
                 {if isset($smallSize)}
                 width="{$smallSize.width|escape:'html':'UTF-8'}"
                 height="{$smallSize.height|escape:'html':'UTF-8'}"{/if}/>
        </a>
    </td>
    <td class="quote_description">
        {capture name=sep} : {/capture}
        {capture}{l s=' : ' mod='roja45quotationspro'}{/capture}
        <p class="product-name">
            <a href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
        </p>
        {if $product.reference}
        <small class="quote_ref">{l s='SKU' mod='roja45quotationspro'}{$smarty.capture.default|escape:'html':'UTF-8'}{$product.reference|escape:'html':'UTF-8'}</small>
        {/if}
        {if isset($product.attributes) && $product.attributes}
        <small>
            <a href="{$product.link|escape:'html':'UTF-8'}">{$product.attributes|@replace: $smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a>
        </small>
        {/if}
    </td>
    {if $quotation_has_customizations}
        <td class="flex-cell quote-product-customizations" role="cell">
            {foreach $product.customizations as $customization}
                <div>
                    <span class="customization-title">{$customization.name} : </span><span class="customization-value">{$customization.value}</span>
                </div>
            {/foreach}
        </td>
    {/if}
    {if $PS_STOCK_MANAGEMENT}
        <td class="quote_avail">
            <span class="label{if $product.quantity_available <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity_available <= 0} label-warning{else} label-success{/if}">{if $product.quantity_available <= 0}{if isset($product.allow_oosp) && $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='roja45quotationspro'}{/if}{else}{l s='Out of stock' mod='roja45quotationspro'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='roja45quotationspro'}{/if}{/if}</span>{if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
        </td>
    {/if}

    <td class="quote_quantity text-center"
        data-title="{l s='Quantity' mod='roja45quotationspro'}"
        data-id-product="{$product.id_product|escape:'html':'UTF-8'}"
        data-id-product-attribute="{$product.id_product_attribute|escape:'html':'UTF-8'}">
        {if (isset($cannotModify) && $cannotModify == 1)}
            <span>
                {$product.quote_quantity-$displayQuantity|escape:'html':'UTF-8'}
			</span>
        {else}
            {if $displayQuantity > 0}
            <input type="hidden"
                   value="{$product.quote_quantity|escape:'html':'UTF-8'}"
                   name="quantity_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}_hidden"/>
            <input size="2" type="text" autocomplete="off" class="quote_quantity_input form-control grey"
                   value="{$product.quote_quantity|escape:'html':'UTF-8'}"
                   name="quantity_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"/>
            <div class="quote_quantity_button clearfix">
                {if $product.minimal_quantity < ($product.quote_quantity-$displayQuantity) OR $product.minimal_quantity <= 1}
                    <a rel="nofollow" class="quote_quantity_down btn btn-default btn-change-qty button-minus"
                       id="quote_quantity_down_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
                       href="#"
                       title="{l s='Subtract' mod='roja45quotationspro'}">
                        <span><i class="icon-minus"></i></span>
                    </a>
                {else}
                    <a class="quote_quantity_down btn btn-default btn-change-qty button-minus disabled" href="#"
                       id="quote_quantity_down_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
                       title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity mod='roja45quotationspro'}">
                        <span><i class="icon-minus"></i></span>
                    </a>
                {/if}
                <a rel="nofollow" class="quote_quantity_up btn btn-default btn-change-qty button-plus"
                   id="quote_quantity_up_{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
                   href="#"
                   title="{l s='Add' mod='roja45quotationspro'}">
                    <span><i class="icon-plus"></i></span>
                </a>
            </div>
            {/if}
        {/if}
    </td>

    {if !isset($cannotModify) || !$cannotModify}
        <td class="quote_delete text-center" data-title="{l s='Delete' mod='roja45quotationspro'}">
            <div>
                <a rel="nofollow" title="{l s='Delete' mod='roja45quotationspro'}" class="quote_quantity_delete"
                   id="{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
                   href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'deleteFromQuote', 'id_roja45_quotation_requestproduct'=>$product.id_roja45_quotation_requestproduct],true)|escape:'htmlall':'UTF-8'}">
                    {if $roja45quotationspro_iconpack=='1'}<i class="icon-trash"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-trash"></i>{else}<i class="icon-trash"></i>{/if}
                </a>
            </div>
        </td>
    {/if}
</tr>
