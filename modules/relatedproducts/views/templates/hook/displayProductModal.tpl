{if $related_products|@count > 0}
<div class="related-products-modal">
<div class="carousel-controls">
  {if $recommendation_type == 'accessories'}
    <h4 class="accessories-title">{l s='Useful accessories' mod='relatedproducts'}</h4>
  {else}
    <h4 class="accessories-title">{l s='Products from the same category' mod='relatedproducts'}</h4>
  {/if}
    <div class="carousel-navigation">
      <button class="carousel-btn prev" type="button">&#10094;</button>
      <button class="carousel-btn next" type="button">&#10095;</button>
    </div>
  </div>
  
  <div class="custom-carousel-wrapper">
    <div class="custom-carousel">
      {foreach from=$related_products item=related}
      <div class="carousel-item">
        <div class="product-miniature">
          <a href="{$related.link}" class="product-image">
            {if isset($related.image_url) && $related.image_url}
              <img src="{$related.image_url}" alt="{$related.name|escape:'htmlall':'UTF-8'}">
            {else}
              <img src="{$urls.no_picture_image.bySize.home_default.url}" alt="No image">
            {/if}
          </a>
          <h5 class="product-title">
            <a href="{$related.link}">{$related.name|truncate:40:'...'}</a>
          </h5>
        <div class="product-price-addtocart">
            <p class="price">
              {if $related.price}
                <span class="price-amount">
                  {$related.price}
                </span>
                <span class="price-tax-excl">
                  {$related.price_tax_exc} {l s='tax excl.' mod='relatedproducts'}
                </span>
              {else}
                <span class="aska-for-price">
                  {l s='Ask for price' mod='relatedproducts'}
                </span>
              {/if}
            </p>
            <p class="add-to-cart">
              {if $related.price > 0}
                <a class="add-to-cart-ajax"
                  href="#"
                  data-id-product="{$related.id_product}"
                  data-product-name="{$related.name|escape:'htmlall':'UTF-8'}"
                  data-product-price="{$related.price}"
                  rel="nofollow"
                  aria-label="{l s='Add to cart' mod='relatedproducts'}">
                  <img src="{if isset($module_dir)}{$module_dir}views/img/add_to_cart.svg{else}../modules/relatedproducts/views/img/add_to_cart.svg{/if}"
                       alt="{l s='Add to cart' mod='relatedproducts'}"
                       class="add-to-cart-icon" />
                </a>
              {/if}
            </p>
        </div>
        </div>
      </div>
      {/foreach}
    </div>
  </div>
</div>
{/if}

