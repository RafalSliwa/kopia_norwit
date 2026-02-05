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

<div class="button-container">
    {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
        {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
            {capture}add=1&amp;id_product={$product.id_product|intval|escape:'html':'UTF-8'}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval|escape:'html':'UTF-8'}{/if}{if isset($static_token)}&amp;token={$static_token|escape:'html':'UTF-8'}{/if}{/capture}
            <a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='roja45quotationspro'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal-quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
                <span>{l s='Add to cart' mod='roja45quotationspro'}</span>
            </a>
        {else}
            <span class="button ajax_add_to_cart_button btn btn-default disabled">
                <span>{l s='Add to cart' mod='roja45quotationspro'}</span>
            </span>
        {/if}
    {/if}
    <a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View' mod='roja45quotationspro'}">
        <span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize' mod='roja45quotationspro'}{else}{l s='More' mod='roja45quotationspro'}{/if}</span>
    </a>
</div>