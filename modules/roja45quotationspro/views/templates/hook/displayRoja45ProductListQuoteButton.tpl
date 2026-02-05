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
    <a class="button ajax_add_quote_button btn btn-default btn-quote"
       rel="nofollow"
       title="{l s='Add to quote' mod='roja45quotationspro'}"
       data-id-product-attribute="{$product.id_product_attribute|intval|escape:'html':'UTF-8'}"
       data-id-product="{$product.id_product|intval|escape:'html':'UTF-8'}"
       data-minimal-quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval|escape:'html':'UTF-8'}{else}{$product.minimal_quantity|intval|escape:'html':'UTF-8'}{/if}">
        <span>{l s='Add to quote' mod='roja45quotationspro'}</span>
    </a>
    <a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View' mod='roja45quotationspro'}">
        <span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize' mod='roja45quotationspro'}{else}{l s='More' mod='roja45quotationspro'}{/if}</span>
    </a>
</div>