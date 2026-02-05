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

<div{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE} class="unvisible"{/if}>
    <p id="add_to_cart" class="buttons_bottom_block no-print">
        <button type="submit" name="Submit" class="exclusive">
            <span>{if $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize' mod='roja45quotationspro'}{else}{l s='Add to cart' mod='roja45quotationspro'}{/if}</span>
        </button>
    </p>
</div>