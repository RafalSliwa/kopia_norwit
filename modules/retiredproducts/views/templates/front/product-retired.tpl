<section id="main" class="retired-product container">

  <div class="row" id="retired-product-wrapper">
      <div class="col-md-5 text-center product-withdrawn">
        <div class="top-wraper-product">
          <div class="product-title-wrapper">
              {* Display information about the retired product *}
              <h2 class="product-title">{$product.product_from->name|escape:'html':'UTF-8'}</h2>
              <p>
                {l s='Reference:' mod='retiredproducts'}
                {if isset($product.product_from->reference_to_display) && $product.product_from->reference_to_display}
                  {$product.product_from->reference_to_display}
                {else}
                  {$product.product_from->reference}
                {/if}
              </p>
          </div>
          <div class="manufacturer-logo">
              {* Display manufacturer and logo of the retired product *}
              {if $product.product_from->id_manufacturer}
                <p>
                  <img src="{$product.link->getManufacturerImageLink($product.product_from->id_manufacturer, 'medium_default')}" alt="{$product.product_from->manufacturer_name|escape:'html':'UTF-8'}" width="70" height="70">
                </p>
              {/if}
          </div>
        </div>

        {* Display image of the retired product *}
        <div class="product-image">
          {if $product.product_from_image_id}
            <img class="withdrawn-image" src="{$product.link->getImageLink($product.product_from->link_rewrite, $product.product_from_image_id, 'large_default')}" alt="{$product.product_from->name|escape:'html':'UTF-8'}">
            <div class="label-withdrawn">
              <p>{l s='production has been completed' mod='retiredproducts'}</p>
            </div>
          {/if}
        </div>
        <div class="product-description">
          {* Display description of the retired product *}
          <div class="description-icon">
            <span>
              <img class="icon" src="{$retiredproducts_img_url}not_available.svg" loading="lazy" alt="arrow" />
            </span>
            <div>
              <p class="availability">{l s='Availability' mod='retiredproducts'}</p>
              <p class="product-withdrawn">{l s='Product withdrawn' mod='retiredproducts'}</p>
            </div>
          </div>
          {* Display retired product description text *}

          <div class="description-text">
            <p>{l s='This product is no longer available for sale.' mod='retiredproducts'}</p>
            <p>{l s='An alternative product with comparable parameters is available, carefully selected by our specialists.' mod='retiredproducts'}</p>
          </div>

        </div>
      </div>
      
      <div class="col-md-2 text-center arrow-wrapper">
        <img class="icon" src="{$retiredproducts_img_url}arrow.svg"  loading="lazy" alt="arrow" /> 
      </div>

      <div class="col-md-5 text-center suggested-product">
        {if $product.product_to}
          <div class="top-wraper-product">
            <div class="product-title-wrapper">
              {* Display information about the replacement product *}
              <h2 class="product-title">{$product.product_to->name|escape:'html':'UTF-8'}</h2>
              <p>
                {l s='Reference:' mod='retiredproducts'}
                {if isset($product.product_to->reference_to_display) && $product.product_to->reference_to_display}
                  {$product.product_to->reference_to_display}
                {else}
                  {$product.product_to->reference}
                {/if}
              </p>
              </div>
            <div class="manufacturer-logo">
              {* Display manufacturer and logo of the replacement *}
              {if $product.product_to->id_manufacturer}  
                <p>
                  <img src="{$product.link->getManufacturerImageLink($product.product_to->id_manufacturer, 'medium_default')}" alt="{$product.product_to->manufacturer_name|escape:'html':'UTF-8'}" width="70" height="70">
                </p>
              {/if}
            </div>
          </div>

          {* Display image of the replacement *}
          <div class="product-image">
          {if $product.product_to_image_id}
            <img class="suggested-image" src="{$product.link->getImageLink($product.product_to->link_rewrite, $product.product_to_image_id, 'large_default')}" alt="{$product.product_to->name|escape:'html':'UTF-8'}" >
            <div class="label-suggested">
              <p>{l s='new model' mod='retiredproducts'}</p>
            </div>
          {/if}
          </div>
          <div class="product-description">
          {* Display description of the replacement product *}
          <div class="description-icon">
            <span>
              <img class="icon" src="{$retiredproducts_img_url}available.svg"  loading="lazy" alt="arrow" />
            </span>
            <div>
              <p class="availability">{l s='Availability' mod='retiredproducts'}</p>
              <p class="product-available">{l s='Product available' mod='retiredproducts'}</p>
            </div>
            <div class="product-price">
              <p class="gross_price">{$product.redirect_product_price_gross}</p>
              <p class="net_price">{$product.redirect_product_price_net} <span>{l s='tax excl' mod='retiredproducts'}</span></p>       
            </div>
          </div>
          {* Display replacement product description text *}

          <div class="description-text">
            <p>
            <a href="{$product.link->getProductLink($product.product_to)}" class="btn btn-primary">
              {l s='See more' mod='retiredproducts'}
            </a>
          </p>
          </div>
        </div>
        {/if}
      </div>
  </div>
</section>




