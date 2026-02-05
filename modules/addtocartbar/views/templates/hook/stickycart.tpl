{*
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 * 
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
*}
<div id="sticky-container" class="container-fluid sticky-containers pt-1" {if $shadow }style="box-shadow: 2px 2px 11px 0 rgba(0,0,0,.3);{/if}">
  <div class="row">
    <div class="product-actions js-product-actions">
      <form action="{$urls.pages.cart|escape:'htmlall':'UTF-8'}" method="post" id="add-to-cart-or-refreshs">
        <input type="hidden" name="token" value="{$static_token|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="id_product" value="{$id_product|escape:'htmlall':'UTF-8'}" id="product_page_product_id">
        <input type="hidden" name="id_customization" value="{$id_customization|escape:'htmlall':'UTF-8'}" id="product_customization_id" class="js-product-customization-id">
        <div id="stickyImg" class="   col-md-1 col-lg-1">
          {include file='catalog/_partials/product-cover-thumbnails.tpl'}
        </div>
        <div id="sticky-price" class=" col-xs-12 col-sm-3 col-md-2 col-lg-3 ">
          <p id="sticky-name">{$productName|escape:'htmlall':'UTF-8'}</p>
          {include file='catalog/_partials/product-prices.tpl'}
        </div>
        <div id="sticky-variants" class=" col-xs-12 col-sm-9 col-md-9 col-lg-8">
            <div id="sticky-comb" class="col-xs-5 col-sm-5  col-md-5  col-lg-6 ">
              {include file='catalog/_partials/product-variants.tpl'}
            </div>
            <div id="sticky-cart" class="col-xs-7 col-sm-7  col-md-7 col-lg-6 ">
              {include file='catalog/_partials/product-add-to-cart.tpl'}
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
{if $stickybar_pos == 'bottom'}
<style type="text/css">
body{
  padding-bottom: {$STICKY_BODY_PAD|escape:'htmlall':'UTF-8'}px !important;
  height: auto !important;
}
</style>
{/if}