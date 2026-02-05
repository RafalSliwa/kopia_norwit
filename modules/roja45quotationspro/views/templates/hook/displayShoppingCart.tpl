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

{if (isset($roja45_contains_products) && $roja45_contains_products)}
<div class="roja-modified-quote" {if $roja45_contains_quote && $roja45_cartmodified}style="display: block{/if}">
    <div class="shopping-cart-footer-quotation-order">
        <i class="icon-warning"></i> <span>{l s='You are not permitted to modify this cart.  If you wish to purchase your quote, please reload it form the \'My Quotes\' area.' mod='roja45quotationspro'}</span>
        <div class="text-sm-center">
            <a href="{$account_link}">
                {l s='My Quotes' mod='roja45quotationspro'}
            </a>
        </div>
    </div>
</div>

<div class="roja-quote-in-cart" {if $roja45_contains_quote && !$roja45_modify_quote_allowed}style="display: block{/if}">
    <div class="shopping-cart-footer-quotation-order">
        <i class="icon-warning"></i> <span>{l s='Your cart contains items from your selected quotation, please complete the checkout process as normal.  Modifying this cart may invalidate the quotation.' mod='roja45quotationspro'}</span>
    </div>
</div>
{/if}