{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45 <support@roja45.com>
*  @copyright  		2016 roja45.com
*}

{if !$roja45quotationspro_usejs}
<div class="button-container roja45quotationspro_buttons_block no-print">
{/if}
        <a class="button ajax_add_quote_button btn btn-default btn-quote catalog-mode {if $roja45quotationspro_usejs|escape:'html':'UTF-8'}hidden{/if}"
           href="{$roja45quotationspro_controller|escape:'html':'UTF-8'}?action=addToQuote&id_product={$product.id_product|intval|escape:'html':'UTF-8'}&id_product_attribute={$product.id_product_attribute|intval|escape:'html':'UTF-8'}&minimal_quantity={if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval|escape:'html':'UTF-8'}{else}{$product.minimal_quantity|intval|escape:'html':'UTF-8'}{/if}&quantity=1"
           rel="nofollow"
           title="{l s='Add to quote' mod='roja45quotationspro'}"
           data-id-product-attribute="{$product.id_product_attribute|intval|escape:'html':'UTF-8'}"
           data-id-product="{$product.id_product|intval|escape:'html':'UTF-8'}"
           data-quantity="1"
           data-minimal-quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval|escape:'html':'UTF-8'}{else}{$product.minimal_quantity|intval|escape:'html':'UTF-8'}{/if}">
            <span>{l s='Add to quote' mod='roja45quotationspro'}</span>
        </a>
{if !$roja45quotationspro_usejs}
</div>
{/if}
