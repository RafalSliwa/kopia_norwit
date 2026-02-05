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
{extends file=$layout}

{block name='head' append}
    <script>
        window.additionalCarousel = window.additionalCarousel || function(){};
    </script>
    <meta property="og:type" content="product">
    {if $product.cover}
        <meta property="og:image" content="{$product.cover.large.url}">
    {/if}

    {if $product.show_price}
        <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
        <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
        <meta property="product:price:amount" content="{$product.price_amount}">
        <meta property="product:price:currency" content="{$currency.iso_code}">
    {/if}
    {if isset($product.weight) && ($product.weight != 0)}
        <meta property="product:weight:value" content="{$product.weight}">
        <meta property="product:weight:units" content="{$product.weight_unit}">
    {/if}
{/block}


{block name='head_microdata_special'}
    {include file='_partials/microdata/product-jsonld.tpl'}
{/block}

{block name='content'}
    <section id="main">
        <meta content="{$product.url}">
        {if isset($product.retired) && $product.retired}
            {hook h='displayRetiredProduct' product=$product}
        {else}
            {* Original content of product.tpl below *}
        <div class="row product-container js-product-container ">
            <div class="pp-left-column col-xs-12 col-sm-5">

                {block name='page_content_container'}
                    <section class="page-content" id="content">
                        <div class="product-leftside">
                            {block name='page_content'}
                                {block name='product_cover_thumbnails'}
                                    {include file='catalog/_partials/product-cover-thumbnails.tpl'}
                                {/block}
                            {/block}
                        </div>
                    </section>
                {/block}
            </div>

            <div class="top-wraper-product col-xs-12 col-sm-7">
                <div class="product-title-brand">
                    {block name='page_header_container'}
                        {block name='page_header'}
                            <h1 class="h1 productpage_title">{block name='page_title'}{$product.name}{/block}</h1>
                            <h2 class="h1 productpage_title_variant"> {hook h='displayProductCurrentVariant' product=$product}</h2>       
                            {/block}
                        {/block}
                        <div class="product-short-info">

                            {hook h='displayProductListReviews' product=$product}

                            {block name='product_reference'}
                            <div class="product_reference-block" data-product-refresh="reference">
                                <h4 class="product_reference title">{l s='Indeks:' d='Shop.Theme.Catalog'}</h4>
                                <div class="product-reference">
                                    <span itemprop="sku">{$product.reference_to_display|default:$product.reference|escape:'html'}</span>
                                </div>
                            </div>
                            {/block}

                            {block name='product_questions'}
                                <div class="product-questions">
                                    <a href="#">
                                        <h4 class="product-questions-title">{l s='Questions & Answers' d='Shop.Theme.Catalog'}
                                        </h4>
                                    </a>
                                </div>
                            {/block}
                        </div>
                </div>

                {block name='brand_name'}
                    <div class="brand-block">
                        {assign var='product_brand_url' value=$link->getManufacturerLink($product.id_manufacturer)}
                        {assign var='manufacturer_image_url' value=$link->getManufacturerImageLink($product.id_manufacturer, 'medium_default')}
                        {if isset($product.id_manufacturer) && $product.id_manufacturer}
                            {$manufacturer_name = Manufacturer::getNameById($product.id_manufacturer)}
                            <div class="bran-name-img">
                                <div class="product-brand-img">
                                    <a href="{$product_brand_url}">
                                        <img class="product-img img-responsive" src="{$manufacturer_image_url}"
                                            title="{$manufacturer_name}" alt="{$manufacturer_name}" width="110" height="110"
                                            loading="lazy">
                                    </a>
                                </div>
                            </div>
                        {/if}
                    </div>
                {/block}
            </div>

            <div class="pp-cnter-column col-xs-12  col-sm-6 col-md-2">
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
                            <a class="product_details_tabs"
                                href="#product-details">{l s='Full Specifications' d='Shop.Theme.Catalog'}</a>
                            <span class="material-icons">
                                keyboard_arrow_down
                            </span>
                        </div>
                    {/if}
                {/block}
            </div>


            <div class="pp-right-column col-xs-12 col-sm-6 col-md-5">
                <div class="product-information">
                    <div class="product-actions js-product-actions">
                        {block name='product_buy'}
                            <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                                <input type="hidden" name="token" value="{$static_token}">
                                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                                <input type="hidden" name="id_customization" value="{$product.id_customization}"
                                    id="product_customization_id" class="js-product-customization-id">

                                {block name='product_prices'}
                                    {include file='catalog/_partials/product-prices.tpl'}
                                {/block}


                                {block name='payment-plan-block'}
                                    <div class="payment-plan-block row">
                                        <div class="bank-block col-sm-6">
                                            {hook h='displayProductAdditionalInfo' product=$product}
                                        </div>
                                        {if $product.price_amount >= 1500}
                                            <div class="product-installments col-sm-6">
                                                <span>{l s='In installments from:' d='Shop.Theme.Catalog'}</span>
                                                <span>{sprintf("%.2f", $product.price_amount / 50)|replace:'.':','} zł</span>
                                            </div>
                                        {/if}
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

                                {block name='pp-cnter-column-mobile'}
                                    {hook h='displayOrderCountdownMobile' product=$product}
                                    {block name='product_features'}
                                        {if $product.grouped_features}
                                            <div class="product-features-block-mobile">
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
                                                <a class="product_details_tabs"
                                                    href="#nr-product-details-static">{l s='Full Specifications' d='Shop.Theme.Catalog'}</a>
                                                <span class="material-icons">
                                                    keyboard_arrow_down
                                                </span>
                                            </div>
                                        {/if}
                                    {/block}
                                    {hook h='displayPbpInfoMessageMobile'}
                                {/block}

                                {block name='payment-plan-block-mobile'}
                                    <div class="bank-block-mobile">
                                        {hook h='displayProductAdditionalInfo' product=$product}
                                    </div>
                                {/block}

                                {block name='product_in_installments_mobile'}
                                    {if $product.price_amount >= 1500}
                                        <div class="product-installments_mobile">
                                            <span>{l s='In installments from:' d='Shop.Theme.Catalog'}</span>
                                            <span>{sprintf("%.2f", $product.price_amount / 50)|replace:'.':','} zł</span>
                                        </div>
                                    {/if}
                                {/block}
                                <div class="add add-mobile">
                                    <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit"
                                        {if !$product.add_to_cart_url || $product.price|floatval == 0}disabled{/if}>
                                        <span class="material-icon">
                                            <img class="icon" src="{$urls.img_url}codezeel/cart.svg" width="30" height="30"
                                                loading="lazy" alt="Icon" />
                                        </span>
                                        {l s='Add to cart' d='Shop.Theme.Actions'}
                                    </button>
                                    {hook h='displayProductPriceBlock' product=$product type="after_price"}
                                </div>

                                {block name='product_refresh'}
                                     <div class="product-refresh js-product-refresh"></div>
                                {/block}
                            </form>
                        {/block}
                    </div>

                    {block name='product_offerts'}
                        <div class="product-offerts">
                            {hook h='displayProductAdditionalInfo' product=$product}
                            {hook h='displayCustomProductInquiry' product=$product}
                            {* <button type="button" class="btn btn-primary" onclick="window.location.href='tel:+48573580892'">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#f8f8f8">
                              <path
                                d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z" />
                            </svg>
                            {l s='Call Us' d='Shop.Theme.Catalog'}
                          </button>*}
                        </div>
                    {/block}

                    {block name='prize'}
                        <div class="prize-block">
                            <div class="prize-logo">
                                <span class="material-icon">
                                    <img class="icon" src="{$urls.img_url}codezeel/gazela-logo.svg" alt="Icon" />
                                </span>
                            </div>
                            <div class="prize-infio">
                                <p>Po raz kolejny zdobyliśmy<br> <span> Gazelę Biznesu</span>- dziękujemy Wam!</p>
                            </div>
                        </div>
                    {/block}

                    {block name='elementor-element-wraper'}
                        <div class="elementor-element-wraper row">
                            <div class="elementor-icon-box-wrapper col-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" viewBox="0 0 39 39" fill="none">
                                    <path
                                        d="M11.783 31.6875C10.5421 31.6875 9.48677 31.2531 8.61712 30.3842C7.74721 29.5151 7.31225 28.46 7.31225 27.2188H4.95925C4.61394 27.2188 4.32442 27.1019 4.09069 26.8682C3.85723 26.6344 3.7405 26.3449 3.7405 25.9996C3.7405 25.654 3.85723 25.3646 4.09069 25.1314C4.32442 24.898 4.61394 24.7812 4.95925 24.7812H8.14994C8.52694 24.1624 9.03475 23.6692 9.67337 23.3017C10.3117 22.9339 11.0143 22.75 11.781 22.75C12.5477 22.75 13.2504 22.9339 13.889 23.3017C14.5274 23.6692 15.0352 24.1624 15.4125 24.7812H22.556L26.0469 9.75H9.71847C9.37315 9.75 9.08377 9.63314 8.85031 9.39941C8.61658 9.16568 8.49972 8.87616 8.49972 8.53084C8.49972 8.18526 8.61658 7.89587 8.85031 7.66269C9.08377 7.42923 9.37315 7.3125 9.71847 7.3125H27.2498C27.7289 7.3125 28.1154 7.49748 28.4092 7.86744C28.7028 8.23712 28.7923 8.65326 28.6777 9.11584L27.6841 13.4062H30.2808C30.7461 13.4062 31.1867 13.5102 31.6027 13.7182C32.019 13.9265 32.3618 14.2141 32.631 14.5811L35.6153 18.5408C35.882 18.8886 36.0534 19.2599 36.1292 19.6548C36.2053 20.0494 36.208 20.4562 36.1373 20.8752L35.103 26.0439C35.032 26.3916 34.8603 26.6744 34.5879 26.8921C34.3154 27.1099 34.0059 27.2188 33.6592 27.2188H32.3121C32.3121 28.4584 31.8778 29.513 31.0092 30.3826C30.1407 31.2525 29.086 31.6875 27.8453 31.6875C26.6047 31.6875 25.5494 31.2531 24.6794 30.3842C23.8098 29.5151 23.375 28.46 23.375 27.2188H16.2497C16.2497 28.4584 15.8155 29.513 14.9469 30.3826C14.0783 31.2525 13.0237 31.6875 11.783 31.6875ZM25.8466 21.3281H33.5467L33.7717 20.178L30.5311 15.8438H27.1247L25.8466 21.3281ZM22.7936 23.8249L23.0313 22.7646C23.1894 22.0577 23.3904 21.1924 23.6342 20.1687C23.7363 19.7437 23.8305 19.351 23.9169 18.9906C24.0033 18.6301 24.0736 18.3062 24.1277 18.0188L24.3654 16.9585C24.5238 16.2516 24.7249 15.3863 24.9687 14.3626C25.2122 13.3388 25.4131 12.4735 25.5716 11.7666L25.8092 10.7063L26.0469 9.75L22.556 24.7812L22.7936 23.8249ZM3.28103 21.3874C2.94005 21.3874 2.65432 21.2706 2.42384 21.0368C2.19336 20.8031 2.07812 20.5136 2.07812 20.1683C2.07812 19.8227 2.19485 19.5333 2.42831 19.3001C2.66177 19.0667 2.95116 18.9499 3.29647 18.9499H8.96853C9.31384 18.9499 9.60323 19.0668 9.83669 19.3005C10.0704 19.5343 10.1873 19.8239 10.1873 20.1695C10.1873 20.5148 10.0704 20.8042 9.83669 21.0377C9.60323 21.2708 9.31384 21.3874 8.96853 21.3874H3.28103ZM6.53103 15.5813C6.18572 15.5813 5.89633 15.4644 5.66287 15.2307C5.42914 14.997 5.31228 14.7075 5.31228 14.3622C5.31228 14.0166 5.42914 13.7271 5.66287 13.4936C5.89633 13.2604 6.18572 13.1438 6.53103 13.1438H13.8435C14.1888 13.1438 14.4782 13.2607 14.7117 13.4944C14.9454 13.7281 15.0623 14.0177 15.0623 14.363C15.0623 14.7086 14.9454 14.9979 14.7117 15.2311C14.4782 15.4646 14.1888 15.5813 13.8435 15.5813H6.53103ZM11.781 29.25C12.3457 29.25 12.8253 29.0526 13.2199 28.6577C13.6148 28.2631 13.8122 27.7834 13.8122 27.2188C13.8122 26.6541 13.6148 26.1744 13.2199 25.7798C12.8253 25.3849 12.3457 25.1875 11.781 25.1875C11.2163 25.1875 10.7367 25.3849 10.3421 25.7798C9.94719 26.1744 9.74975 26.6541 9.74975 27.2188C9.74975 27.7834 9.94719 28.2631 10.3421 28.6577C10.7367 29.0526 11.2163 29.25 11.781 29.25ZM27.8437 29.25C28.4081 29.25 28.8878 29.0526 29.2827 28.6577C29.6775 28.2631 29.875 27.7834 29.875 27.2188C29.875 26.6541 29.6775 26.1744 29.2827 25.7798C28.8878 25.3849 28.4081 25.1875 27.8437 25.1875C27.279 25.1875 26.7992 25.3849 26.4044 25.7798C26.0095 26.1744 25.8121 26.6541 25.8121 27.2188C25.8121 27.7834 26.0095 28.2631 26.4044 28.6577C26.7992 29.0526 27.279 29.25 27.8437 29.25Z"
                                        fill="#1D1D1A" />
                                </svg>
                                <p> {l s='Free delivery over PLN 1,500' d='Shop.Theme.Catalog'}</p>
                            </div>
                            <div class="elementor-icon-box-wrapper col-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="31" viewBox="0 0 35 31" fill="none">
                                    <path
                                        d="M16.6899 28.4062C16.8502 28.4062 17.0132 28.3687 17.179 28.2937C17.3445 28.2187 17.4752 28.1332 17.571 28.0373L30.5678 15.0406C30.9345 14.6739 31.2106 14.2821 31.3961 13.8653C31.5814 13.4488 31.674 13.0114 31.674 12.5531C31.674 12.0781 31.5814 11.6208 31.3961 11.1812C31.2106 10.7414 30.9345 10.3465 30.5678 9.99659L24.0678 3.49659C23.7179 3.12989 23.3387 2.86163 22.9303 2.69181C22.5221 2.52227 22.0805 2.4375 21.6055 2.4375C21.1472 2.4375 20.7071 2.52227 20.2852 2.69181C19.8632 2.86163 19.4742 3.12989 19.118 3.49659L18.1865 4.42812L21.1927 7.45916C21.5573 7.80718 21.8266 8.20408 22.0008 8.64987C22.1747 9.09567 22.2616 9.55812 22.2616 10.0372C22.2616 11.029 21.9304 11.8561 21.2679 12.5186C20.6054 13.1811 19.7783 13.5123 18.7865 13.5123C18.3074 13.5123 17.8433 13.4331 17.3943 13.2746C16.9455 13.1165 16.5471 12.8634 16.1991 12.5153L13.4805 9.82191C13.3868 9.72793 13.2669 9.68094 13.121 9.68094C12.9753 9.68094 12.8555 9.72793 12.7618 9.82191L6.05871 16.5246C5.93576 16.6476 5.84354 16.7852 5.78206 16.9374C5.72058 17.0893 5.68984 17.2456 5.68984 17.4062C5.68984 17.706 5.79194 17.9622 5.99615 18.1748C6.20036 18.3874 6.45237 18.4937 6.75218 18.4937C6.91279 18.4937 7.07583 18.4562 7.24131 18.3812C7.40706 18.3062 7.53787 18.2207 7.63375 18.1248L12.1147 13.6435C12.3397 13.4184 12.6184 13.2991 12.9507 13.2856C13.2831 13.2721 13.5753 13.3914 13.8274 13.6435C14.0628 13.8789 14.1805 14.1643 14.1805 14.4999C14.1805 14.8352 14.0628 15.1205 13.8274 15.3558L9.37087 19.8372C9.24818 19.9601 9.1561 20.0977 9.09462 20.2499C9.03314 20.4019 9.0024 20.5581 9.0024 20.7188C9.0024 21.0083 9.10708 21.2577 9.31643 21.4671C9.52579 21.6764 9.77522 21.7811 10.0647 21.7811C10.2253 21.7811 10.3884 21.7436 10.5539 21.6686C10.7196 21.5935 10.8503 21.5081 10.9459 21.4122L15.6149 16.7684C15.84 16.5436 16.1185 16.4244 16.4506 16.4109C16.7829 16.3973 17.0751 16.5165 17.3273 16.7684C17.5626 17.004 17.6803 17.2895 17.6803 17.6248C17.6803 17.96 17.5626 18.2455 17.3273 18.4811L12.6834 23.1497C12.571 23.2456 12.4815 23.3763 12.4149 23.5418C12.3483 23.7075 12.315 23.8706 12.315 24.0309C12.315 24.3207 12.4196 24.5703 12.629 24.7796C12.8383 24.989 13.0878 25.0937 13.3773 25.0937C13.5376 25.0937 13.6939 25.0629 13.8461 25.0014C13.9981 24.94 14.1355 24.8477 14.2585 24.7248L18.9275 20.0809C19.1523 19.8559 19.4308 19.7366 19.7632 19.723C20.0955 19.7095 20.3877 19.8288 20.6398 20.0809C20.8752 20.3163 20.9929 20.6017 20.9929 20.9373C20.9929 21.2726 20.8752 21.5581 20.6398 21.7937L15.9712 26.4623C15.8483 26.5853 15.756 26.728 15.6946 26.8905C15.6331 27.053 15.6023 27.2093 15.6023 27.3593C15.6023 27.6591 15.7138 27.9086 15.9367 28.1076C16.1596 28.3067 16.4106 28.4062 16.6899 28.4062ZM16.6647 30.8433C15.746 30.8433 14.945 30.5247 14.2617 29.8874C13.5784 29.2499 13.2212 28.4561 13.19 27.506C12.2692 27.4434 11.4999 27.1164 10.8821 26.5249C10.2643 25.9331 9.92946 25.1559 9.87746 24.1934C8.91492 24.1311 8.13682 23.7947 7.54315 23.1843C6.94921 22.5738 6.63139 21.806 6.58968 20.8808C5.62308 20.8186 4.8252 20.4681 4.19606 19.8295C3.56691 19.1908 3.25234 18.3831 3.25234 17.4062C3.25234 16.9271 3.34347 16.4577 3.52575 15.9981C3.70802 15.5388 3.97316 15.1352 4.32118 14.7875L11.0397 8.06853C11.6001 7.50818 12.2896 7.228 13.1084 7.228C13.9274 7.228 14.617 7.50818 15.1774 8.06853L17.8806 10.7717C17.9762 10.8841 18.1016 10.9738 18.2568 11.0407C18.4122 11.1073 18.5806 11.1406 18.7617 11.1406C19.0575 11.1406 19.3126 11.0427 19.5271 10.8469C19.7419 10.6511 19.8493 10.3947 19.8493 10.0778C19.8493 9.89666 19.816 9.72847 19.7493 9.57328C19.6824 9.41809 19.5928 9.29256 19.4804 9.19669L13.7803 3.49659C13.4304 3.12989 13.0487 2.86163 12.6351 2.69181C12.2215 2.52227 11.7772 2.4375 11.3022 2.4375C10.8439 2.4375 10.4091 2.52227 9.99771 2.69181C9.58605 2.86163 9.19699 3.12989 8.83056 3.49659L3.49284 8.85909C3.01996 9.33197 2.70228 9.90126 2.53978 10.567C2.37728 11.2327 2.39393 11.8801 2.58975 12.5092C2.6607 12.8445 2.59977 13.1492 2.40693 13.4233C2.21437 13.6971 1.95031 13.8611 1.61475 13.9153C1.27945 13.9694 0.972193 13.9044 0.692963 13.7203C0.413734 13.5358 0.247037 13.2758 0.19287 12.9403C-0.0633385 11.9008 -0.0642864 10.866 0.190026 9.83572C0.444068 8.80547 0.965828 7.8956 1.75531 7.10612L7.07718 1.78425C7.68575 1.19248 8.3478 0.747228 9.06334 0.448499C9.77888 0.149499 10.5304 0 11.318 0C12.1053 0 12.8542 0.149499 13.5646 0.448499C14.2753 0.747228 14.9263 1.19248 15.5178 1.78425L16.4494 2.71538L17.3805 1.78425C17.9888 1.19248 18.6481 0.747228 19.3585 0.448499C20.0689 0.149499 20.8179 0 21.6055 0C22.3931 0 23.1446 0.149499 23.8602 0.448499C24.5757 0.747228 25.2294 1.19248 25.8212 1.78425L32.2805 8.24362C32.872 8.83539 33.3251 9.50882 33.6398 10.2639C33.9543 11.019 34.1115 11.7903 34.1115 12.5779C34.1115 13.3655 33.9543 14.1145 33.6398 14.8249C33.3251 15.5353 32.872 16.1862 32.2805 16.7777L19.2834 29.7497C18.9251 30.108 18.5215 30.3799 18.0727 30.5654C17.6237 30.7507 17.1544 30.8433 16.6647 30.8433Z"
                                        fill="#1D1D1A" />
                                </svg>
                                <p> {l s='24 years on the market' d='Shop.Theme.Catalog'}</p>
                            </div>
                            <div class="elementor-icon-box-wrapper col-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" viewBox="0 0 39 39" fill="none">
                                    <path
                                        d="M19.5 8.36874L6.81241 13.4436C6.7187 13.4853 6.64327 13.5479 6.58612 13.6313C6.52871 13.7147 6.5 13.8084 6.5 13.9124V30.3749C6.5 30.4895 6.55471 30.6014 6.66412 30.7109C6.77354 30.8203 6.88553 30.875 7.00009 30.875H10.5625V20.8126C10.5625 20.002 10.8494 19.3097 11.4233 18.7358C11.9975 18.1619 12.6898 17.875 13.5001 17.875H25.4999C26.3102 17.875 27.0025 18.1619 27.5767 18.7358C28.1506 19.3097 28.4375 20.002 28.4375 20.8126V30.875H31.9999C32.1145 30.875 32.2265 30.8203 32.3359 30.7109C32.4453 30.6014 32.5 30.4895 32.5 30.3749V13.9124C32.5 13.8084 32.4713 13.7147 32.4139 13.6313C32.3567 13.5479 32.2813 13.4853 32.1876 13.4436L19.5 8.36874ZM4.0625 30.3749V13.9124C4.0625 13.2979 4.22974 12.7458 4.56422 12.2561C4.89843 11.7668 5.34584 11.4106 5.90647 11.1877L18.4064 6.19084C18.7541 6.03863 19.1187 5.96252 19.5 5.96252C19.8813 5.96252 20.2459 6.03863 20.5936 6.19084L33.0935 11.1877C33.6542 11.4106 34.1016 11.7668 34.4358 12.2561C34.7703 12.7458 34.9375 13.2979 34.9375 13.9124V30.3749C34.9375 31.1852 34.6506 31.8775 34.0767 32.4516C33.5025 33.0255 32.8102 33.3125 31.9999 33.3125H26V20.8126C26 20.6666 25.9531 20.5468 25.8594 20.4531C25.7657 20.3593 25.6459 20.3125 25.4999 20.3125H13.5001C13.3541 20.3125 13.2343 20.3593 13.1406 20.4531C13.0469 20.5468 13 20.6666 13 20.8126V33.3125H7.00009C6.18976 33.3125 5.49751 33.0255 4.92334 32.4516C4.34945 31.8775 4.0625 31.1852 4.0625 30.3749ZM15.078 33.3125V30.4375H17.953V33.3125H15.078ZM18.0627 28.4375V25.5625H20.9373V28.4375H18.0627ZM21.047 33.3125V30.4375H23.922V33.3125H21.047Z"
                                        fill="#1D1D1A" />
                                </svg>
                                <p> {l s='Own warehouse 1200 m²' d='Shop.Theme.Catalog'}</p>
                            </div>
                            <div class="elementor-icon-box-wrapper col-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" viewBox="0 0 39 39" fill="none">
                                    <path
                                        d="M9.34375 19.5812C9.34375 20.0563 9.37598 20.5297 9.44044 21.0015C9.50517 21.4735 9.61567 21.9387 9.77194 22.3969C9.87594 22.7428 9.86497 23.0735 9.73903 23.389C9.61309 23.7045 9.399 23.9311 9.09675 24.0686C8.77798 24.2227 8.46083 24.2352 8.14531 24.106C7.82979 23.9768 7.6199 23.7395 7.51562 23.3939C7.29896 22.7812 7.14323 22.1525 7.04844 21.5076C6.95365 20.8631 6.90625 20.2209 6.90625 19.5812C6.90625 16.0666 8.1292 13.0755 10.5751 10.608C13.021 8.14013 15.996 6.90621 19.5 6.90621H20.7687L18.6188 4.75633C18.3938 4.53127 18.2787 4.24839 18.2735 3.90768C18.2684 3.56724 18.3835 3.27921 18.6188 3.04358C18.8542 2.80823 19.1395 2.69055 19.4748 2.69055C19.8104 2.69055 20.0958 2.80823 20.3312 3.04358L24.3843 7.09674C24.6782 7.39059 24.8251 7.73333 24.8251 8.12496C24.8251 8.51658 24.6782 8.85932 24.3843 9.15318L20.3312 13.2063C20.1061 13.4311 19.8234 13.5462 19.4829 13.5516C19.1422 13.5568 18.8542 13.4417 18.6188 13.2063C18.3835 12.9707 18.2658 12.6853 18.2658 12.35C18.2658 12.0147 18.3835 11.7292 18.6188 11.4936L20.7687 9.34371H19.5C16.6771 9.34371 14.2786 10.3374 12.3045 12.3248C10.3307 14.3124 9.34375 16.7312 9.34375 19.5812ZM29.6562 19.4187C29.6562 18.9437 29.624 18.4703 29.5596 17.9985C29.4948 17.5264 29.3843 17.0612 29.2281 16.603C29.1241 16.2571 29.135 15.9264 29.261 15.6109C29.3869 15.2954 29.601 15.0689 29.9032 14.9313C30.222 14.7772 30.5366 14.7647 30.847 14.8939C31.1573 15.0231 31.3645 15.2605 31.4685 15.6061C31.6852 16.2187 31.8436 16.8474 31.9438 17.4923C32.0438 18.1369 32.0938 18.779 32.0938 19.4187C32.0938 22.9333 30.8708 25.9244 28.4249 28.392C25.979 30.8598 23.004 32.0937 19.5 32.0937H18.2313L20.3812 34.2436C20.6062 34.4686 20.7213 34.7515 20.7265 35.0922C20.7316 35.4327 20.6165 35.7207 20.3812 35.9563C20.1458 36.1917 19.8605 36.3094 19.5252 36.3094C19.1896 36.3094 18.9042 36.1917 18.6688 35.9563L14.6157 31.9032C14.3218 31.6093 14.1749 31.2666 14.1749 30.875C14.1749 30.4833 14.3218 30.1406 14.6157 29.8467L18.6688 25.7936C18.8939 25.5688 19.1766 25.4537 19.5171 25.4483C19.8578 25.4431 20.1458 25.5582 20.3812 25.7936C20.6165 26.0292 20.7342 26.3147 20.7342 26.65C20.7342 26.9853 20.6165 27.2707 20.3812 27.5063L18.2313 29.6562H19.5C22.3229 29.6562 24.7214 28.6625 26.6955 26.6751C28.6693 24.6875 29.6562 22.2687 29.6562 19.4187Z"
                                        fill="#1D1D1A" />
                                </svg>
                                <p> {l s='Returns up to 14 days' d='Shop.Theme.Catalog'}</p>
                            </div>
                        </div>
                    {/block}

                    {block name= 'social-media'}
                        <div class="social-media">
                            <a href="https://www.facebook.com/norwit/" title="Facebook" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                            </a>
                            <a href="https://www.instagram.com/norwitmaszyny/" title="Instagram" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-instagram" aria-hidden="true"></i>
                            </a>
                            <a href="https://www.youtube.com/channel/UCPx-A1rXRMoc3s7RKn3v62Q" title="YouTube" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-youtube-play" aria-hidden="true"></i>
                            </a>
                            <a href="https://pl.linkedin.com/company/norwit" title="LinkedIn" target="_blank"
                                rel="noopener noreferrer">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                            </a>
                        </div>
                    {/block}
                </div>
            </div>
             {hook h='displayPbpInfoMessage'}
        </div>

         {/if}

        {block name='product_accessories'}
            {if $accessories}
                <section class="product-accessories clearfix">
                    <div class="accessories-wrapper">
                        <h2 class="h1 products-section-title text-uppercase">
                            <span>{l s='Accessories' d='Shop.Theme.Catalog'}</span>
                            <div>
                                <button class="carousel-btn prev-btn" aria-label="Previous">&lt;</button>
                                <button class="carousel-btn next-btn" aria-label="Next">&gt;</button>
                            </div>
                        </h2>
                        <div class="product-accessories-wrapper">
                            <div class="products">
                                <ul id="accessories-carousel" class="nr-carousel product_list">
                                    {foreach from=$accessories item="product_accessory"}
                                        <li class="item">
                                            {include file='catalog/_partials/miniatures/product-nocart.tpl' product=$product_accessory}
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            {/if}
        {/block}

        <section class="product-tabcontent nr-product-tabcontent">
            {block name='product_tabs'}
                <div class="tabs">
                    <!-- Nawigacja -->
                    <ul class="nav nav-tabs" role="tablist">
                        {if $product.description}
                            <li class="nav-item">
                                <a class="nav-link" href="#description"><span>{l s='Description' d='Shop.Theme.Catalog'}</span></a>
                            </li>
                        {/if}
                        <li class="nav-item">
                            <a class="nav-link"
                                href="#nr-product-details-static"><span>{l s='Product Details' d='Shop.Theme.Catalog'}</span></a>
                        </li>
                        {if $product.attachments}
                            <li class="nav-item">
                                <a class="nav-link" href="#attachments"><span>{l s='Attachments' d='Shop.Theme.Catalog'}</span></a>
                            </li>
                        {/if}
                        {foreach from=$product.extraContent item=extra key=extraKey}
                            <li class="nav-item">
                                <a class="nav-link" href="#extra-{$extraKey}"><span>{$extra.title}</span></a>

                            </li>
                        {/foreach}
                    </ul>

                    <!-- Section Contents -->
                    <div class="tab-content" id="tab-content" style="margin-top: 2rem;">
                        {if $product.description}
                            <div id="description" class="anchor-offset">
                                {block name='product_description'}
                                    <h2 class="h2">{l s='Description' d='Shop.Theme.Catalog'}</h2>
                                    <div class="product-description">{$product.description nofilter}</div>
                                {/block}
                            </div>
                        {/if}

                      
                        <div id="product-details" class="anchor-offset"></div>

                        
                        <div id="nr-product-details-static" class="anchor-offset">
                            {block name='nr_product_details'}
                                <h2 class="h2">{l s='Product Details' d='Shop.Theme.Catalog'}</h2>

                                <div class="product_reference-block" data-product-refresh="reference">
                                        <p class="product_reference title">{l s='Indeks:' d='Shop.Theme.Catalog'}</p>
                                         <div class="product-reference">
                                                <span itemprop="sku">{$product.reference_to_display|default:$product.reference|escape:'html'}</span>
                                        </div>
                                </div>

                                
                                {if $product.features}
                                    <dl class="data-sheet">
                                        {foreach from=$product.features item=feature}
                                            <dt class="name">{$feature.name}</dt>
                                            <dd class="value">{$feature.value|escape:'htmlall'|nl2br nofilter}</dd>
                                        {/foreach}
                                    </dl>
                                {/if}

                                {if $product.condition}
                                    <p><strong>{l s='Condition'}:</strong> {$product.condition}</p>
                                {/if}
                            {/block}
                        </div>


                        {if $product.attachments}
                            <div id="attachments" class="anchor-offset">
                                {block name='product_attachments'}
                                    <h2 class="h2">{l s='Attachments' d='Shop.Theme.Catalog'}</h2>
                                    <section class="product-attachments">
                                        <h3 class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</h3>
                                        {foreach from=$product.attachments item=attachment}
                                            <div class="attachment">
                                                <h4>
                                                    <a
                                                        href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                                        {$attachment.name}
                                                    </a>
                                                </h4>
                                                <p>{$attachment.description}</p>
                                                <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                                    {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                                                </a>
                                            </div>
                                        {/foreach}
                                    </section>
                                {/block}
                            </div>
                        {/if}

                        {foreach from=$product.extraContent item=extra key=extraKey}
                            <div id="extra-{$extraKey}" class="extra-tab-section anchor-offset"
                                {foreach $extra.attr as $key => $val} {$key}="{$val}" {/foreach}>
                                <h2 class="h2">{$extra.title}</h2>
                                {$extra.content nofilter}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/block}
        </section>

        {block name='product_footer'}
            {hook h='displayFooterProduct' product=$product category=$category}
        {/block}

        {block name='product_images_modal'}
            {include file='catalog/_partials/product-images-modal.tpl'}
        {/block}

        {block name='page_footer_container'}
            <footer class="page-footer">
                {block name='page_footer'}
                {/block}
            </footer>
        {/block}
    </section>
  
{/block}