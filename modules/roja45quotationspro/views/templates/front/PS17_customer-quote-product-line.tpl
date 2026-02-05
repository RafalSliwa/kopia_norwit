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

<div class="tr">
    <div class="td">
        {if isset($product.image_tag)}
            <div class="product-image-container">
                <img src="{$product.image_tag|escape:'html':'UTF-8'}" alt="{$product.product_title|escape:'html':'UTF-8'}"
                    class="img img-thumbnail" width="{$product['image_width']}" height="{$product['image_height']}" />
                {if !empty($product['custom_image'])}
                    <div class="image-download-container">
                        <a href="{$product['image_url']}" class="download-custom-image" target='_blank'>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z" />
                            </svg>
                        </a>
                    </div>
                {/if}
            </div>
        {/if}
    </div>
    <div class="td">
        <div>{$product.product_title|escape:'html':'UTF-8'}</div>
        {if (isset($product.customizations))}
            {foreach $product.customizations as $customization}
                <div><span> - {$customization.name|escape:'html':'UTF-8'} : {$customization.value|escape:'html':'UTF-8'}</span>
                </div>
            {/foreach}
        {/if}
    </div>
    <div class="td">
        {if $product.reference}{$product.reference|escape:'html':'UTF-8'}{/if}
    </div>
    <div class="td">
        {if $product.comment}{$product.comment|escape:'html':'UTF-8'}{/if}
    </div>
    <div class="td">
        {if $quotation->quote_sent=="1"}
            {if ($quotation->calculate_taxes)}
                {$product.unit_price_tax_incl_formatted|escape:'html':'UTF-8'}
            {else}
                {$product.unit_price_tax_excl_formatted|escape:'html':'UTF-8'}
            {/if}
        {/if}
    </div>
    <div class="td">
        {$product.qty|escape:'html':'UTF-8'}
    </div>
    <div class="td">
        {if $quotation->quote_sent=="1"}
            {if ($quotation->calculate_taxes)}
                {$product.product_price_subtotal_incl_formatted|escape:'html':'UTF-8'}
            {else}
                {$product.product_price_subtotal_excl_formatted|escape:'html':'UTF-8'}
            {/if}
        {/if}
    </div>
    {if ($quotation->calculate_taxes)}
        <div class="td">
            {if $quotation->quote_sent=="1"}
                {$product.tax_paid_formatted|escape:'html':'UTF-8'}
            {/if}
        </div>
        <div class="td">
            {if $quotation->quote_sent=="1"}
                {$product.tax_rate_formatted|escape:'html':'UTF-8'}
            {/if}
        </div>
    {/if}
</div>