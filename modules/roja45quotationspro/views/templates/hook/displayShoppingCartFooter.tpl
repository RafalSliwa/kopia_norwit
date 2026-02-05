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

{if $roja45_contains_products && $roja45_convert_to_quote && !$roja45_contains_quote}
    <form action="{$request_quote_controller}" method="post" id="roja45_convert_to_quote"> {* HTML Content *}
        <input type="hidden" name="id_cart" value="{$id_cart|escape:'htmlall':'UTF-8'}"/>
        <p class="cart_navigation clearfix">
            <button type="submit" class="button btn btn-default button-medium">
                <span>{l s='Convert To Quote' mod='roja45quotationspro'}<i class="icon-chevron-right right"></i></span>
            </button>
        </p>
    </form>
{/if}
{if (isset($roja45_contains_products) && $roja45_contains_products)}
<div class="roja-modified-quote card" {if $roja45_contains_quote && $roja45_cartmodified}style="display: block{/if}">
    <div class="shopping-cart-footer-quotation-order">
        <i class="icon-warning"></i> <span>{l s='You are not permitted to modify the cart created for your quotation.  You will need to reload the order from the quote available in the My Quotes section.' mod='roja45quotationspro'}</span>
    </div>
</div>

<div class="roja-quote-in-cart card" {if $roja45_contains_quote && !$roja45_modify_quote_allowed}style="display: block{/if}">
    <div class="shopping-cart-footer-quotation-order">
        <i class="icon-warning"></i> <span>{l s='Your cart contains items from your selected quotation, please complete the checkout process as normal.  Modifying this cart may invalidate the quotation.' mod='roja45quotationspro'}</span>
    </div>
</div>
{/if}