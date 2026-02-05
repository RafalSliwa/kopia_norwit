{**
 * Cart Accessories Module - Accessories display template
 * Structure matches product.tpl accessories carousel
 *}
{if $accessories && count($accessories) > 0}
<section class="product-accessories cart-accessories clearfix">
    <div class="accessories-wrapper">
        <h2 class="h1 products-section-title text-uppercase">
            <span>{$accessories_title}</span>
            <div>
                <button class="carousel-btn prev-btn js-cart-acc-prev" aria-label="Previous">&lt;</button>
                <button class="carousel-btn next-btn js-cart-acc-next" aria-label="Next">&gt;</button>
            </div>
        </h2>
        <div class="product-accessories-wrapper">
            <div class="products">
                <ul id="cart-accessories-carousel" class="nr-carousel product_list">
                    {foreach from=$accessories item=product}
                        <li class="item">
                            {include file='catalog/_partials/miniatures/product-nocart.tpl' product=$product}
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
</section>
{/if}
