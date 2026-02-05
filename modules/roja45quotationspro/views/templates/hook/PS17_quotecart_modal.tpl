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

<div class="modal-dialog" role="document">
    <div class="modal-content cart-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title h6 text-sm-center" id="myModalLabel">
                {l s='Product successfully added to your quote' mod='roja45quotationspro'}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="modal-body-content col-md-5 divide-right">
                    <div class="row">
                        <div class="col-md-6">
                            <img class="product-image" src="{$product.image_quote}" alt="{$product.image_title}" title="{$product.image_title}" width="{$product.image_width}" height="{$product.image_height}" itemprop="image">
                        </div>
                        <div class="col-md-6">
                            <h6 class="h6 product-name">{$product.name}</h6>
                            <p><strong>{l s='Quantity' mod='roja45quotationspro'}</strong>&nbsp;{$product.qty_in_cart}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-body-content col-md-7">
                    {if $nbr_products > 1}
                        <p class="cart-products-count">{l s='There are %d products in your quote.' mod='roja45quotationspro' sprintf=[$nbr_products]}</p>
                    {else}
                        <p class="cart-products-count">{l s='There is 1 product in your quote.' mod='roja45quotationspro'}</p>
                    {/if}
                </div>
            </div>
            <div class="modal-footer">
                <div class="cart-content-btn">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Continue shopping' mod='roja45quotationspro'}</button>
                    <a href="{$request_link}" class="btn btn-primary">
                        {l s='Request Quote' mod='roja45quotationspro'}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
