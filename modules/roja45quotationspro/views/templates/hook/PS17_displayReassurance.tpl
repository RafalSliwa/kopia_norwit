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

<div class="card request-quote-card">
    <div class="cart-detailed-actions card-block text-sm-center">
        <h1 class="h1">{l s='Request a Quote' mod='roja45quotationspro'}</h1>
        <p class="">{l s='Select this option to create a quotation request with your cart content.' mod='roja45quotationspro'}</p>
        <div class="text-sm-center">
            <form action="{$controller}" method="post">
                <input type="hidden" name="id_cart" value="{$id_cart}"/>
                <button type="submit" class="btn btn-primary">{l s='Request a Quote' mod='roja45quotationspro'}</button>
            </form>
        </div>
    </div>
</div>