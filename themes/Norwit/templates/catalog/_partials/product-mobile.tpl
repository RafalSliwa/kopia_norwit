{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

<div class="row product-container js-product-container mobile">
    <div class="row">
        <div class="col-md-6 col-sm-6">
            {block name='product_cover_thumbnails'}
                <div class="images-container-slider">
                    <div class="nr-carousel-wrapper">
                        <ul class="nr-carousel product_list">
                            {block name='product_images'}
                                {assign var='sliderFor' value=1}
                                {assign var='thumbCount' value=count($product.images)}
                                {foreach from=$product.images item=image}
                                    <li class="thumb-container js-thumb-container">
                                        <img class="thumb js-thumb lazyload {if $image.id_image == $product.default_image.id_image} selected js-thumb-selected{/if}"
                                            data-image-medium-src="{$image.bySize.medium_default.url}"
                                            data-image-large-src="{$image.bySize.large_default.url}"
                                            data-src="{$image.bySize.home_default.url}"
                                            src="{$urls.img_url}codezeel/image_loading.svg" width="300" height="300"
                                            style="aspect-ratio: 1/1;" alt="{$image.legend}" title="{$image.legend}" loading="lazy">
                                    </li>
                                {/foreach}
                            {/block}
                        </ul>
                        <div class="carousel-dots"></div>
                    </div>
                </div>
            {/block}
        </div>
        <div class="col-md-6 col-sm-6">
            <h1 class="h1">{$product.name}</h1>
            <div class="product-short-info">

                {hook h='displayProductListReviews' product=$product}

                {block name='product_reference'}
                    {if isset($product.reference_to_display) && $product.reference_to_display neq ''}
                        <div class="product_reference-block">
                            <h4 class="product_reference title">{l s='Reference' d='Shop.Theme.Catalog'}</h4>
                            <div class="product-reference">
                                <span itemprop="sku">{$product.reference_to_display}</span>
                            </div>
                        </div>
                    {/if}
                {/block}
            </div>

            {block name='product_buy'}
                <div class="product-actions js-product-actions">
                    <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                        <input type="hidden" name="token" value="{$static_token}">
                        <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                        <input type="hidden" name="id_customization" value="{$product.id_customization}"
                            id="product_customization_id" class="js-product-customization-id">
                        {block name='product_prices'}
                            <div class="price-offerts">
                                <div class="product-prices js-product-prices">
                                    <div class="product-price{if $product.has_discount}has-discount{/if}">
                                        <div class="price">
                                            {* Wyświetlanie ceny brutto z osobnym span dla etykiety *}
                                            <span class="price-inc">
                                                {$product.price}
                                                <span class="tax-label">{l s='tax incl.' d='Shop.Theme.Catalog'}</span>
                                            </span>

                                            {* Wyświetlanie ceny netto z dwoma miejscami po przecinku i symbolem waluty z systemu *}
                                            <span class="price-exc">
                                                {$product.price_tax_exc|number_format:2:',':''} {$currency.sign}
                                                <span class="tax-label">{l s='tax excl.' d='Shop.Theme.Catalog'}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-offerts">
                                    {hook h='displayProductAdditionalInfo' product=$product}
                                    <button type="button" class="btn btn-primary"
                                        onclick="window.location.href='tel:+123456789'">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                            width="24px" fill="#f8f8f8">
                                            <path
                                                d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z" />
                                        </svg>

                                    </button>
                                </div>
                            </div>

                        {/block}


                        {block name='product_variants'}
                            {include file='catalog/_partials/product-variants.tpl'}
                        {/block}

                        {block name='product_pack'}
                            {if $packItems}
                                <section class="product-pack">
                                    <h3 class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</h3>
                                    {foreach from=$packItems item="product_pack"}
                                        {block name='product_miniature'}
                                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack showPackProductsPrice=$product.show_price}
                                        {/block}
                                    {/foreach}
                                </section>
                            {/if}
                        {/block}

                        {hook h='displayOrderCountdown' product=$product}

                        {block name='product_features'}
                            {if $product.grouped_features}
                                <div class="product-features-block">
                                    <h4 class="technical_data-title">{l s='Technical Data' d='Shop.Theme.Catalog'}</h4>
                                    <ul class="product-features-list">
                                        {assign var="feature_count" value=0}
                                        {foreach from=$product.grouped_features item=feature}
                                            {if $feature_count < 5}
                                                <li class="feature-item">
                                                    <span class="feature-name">{$feature.name}</span>:
                                                    <span class="feature-value">{$feature.value|escape:'htmlall'|nl2br nofilter}</span>
                                                </li>
                                                {assign var="feature_count" value=$feature_count+1}
                                            {/if}
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}
                        {/block}


                        {block name='product_in_installments'}
                            {if $product.price_amount >= 1500}
                                <div class="product-installments">
                                    <span>{l s='In installments from:' d='Shop.Theme.Catalog'}</span>
                                    <span>{$product.price_amount / 50|number_format:2} zł</span>
                                </div>
                            {/if}
                        {/block}

                        <div class="add">
                            <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit"
                                {if !$product.add_to_cart_url}disabled{/if}>
                                <span class="material-icon">
                                    <img class="icon" src="{$urls.img_url}codezeel/cart.svg" alt="Icon" />
                                </span>
                                {l s='Add to cart' d='Shop.Theme.Actions'}
                            </button>
                            {hook h='displayProductPriceBlock' product=$product type="after_price"}
                        </div>

                        {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                        {block name='product_refresh'}{/block}
                    </form>
                </div>
            {/block}
        </div>
    </div>
</div>