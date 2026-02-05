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
<div class="images-container js-images-container">
  <div class="images-container-slider">
    {block name='product_cover'}
      <div class="product-cover">
        {if $product.default_image}
          <img class="js-qv-product-cover img-fluid zoom-product lazyload"
            data-zoom-image="{$product.default_image.bySize.large_default.url}"
            data-src="{$product.default_image.bySize.large_default.url}" src="{$urls.img_url}codezeel/image_loading.svg"
            height="{$product.default_image.bySize.large_default.height}"
            width="{$product.default_image.bySize.large_default.width}" {if !empty($product.default_image.legend)}
            alt="{$product.default_image.legend}" title="{$product.default_image.legend}" {else} alt="{$product.name}" 
            {/if}
            loading="lazy" />
          <div class="layer" data-toggle="modal" data-target="#product-modal">
            <i class="fa fa-arrows-alt zoom-in"></i>
          </div>
        {else}
          <img class="img-fluid lazyload" data-src="{$urls.no_picture_image.bySize.large_default.url}"
            src="{$urls.img_url}codezeel/image_loading.svg" height="{$urls.no_picture_image.bySize.large_default.height}"
            width="{$urls.no_picture_image.bySize.large_default.width}" loading="lazy" />
        {/if}
        {include file='catalog/_partials/product-flags.tpl'}
      </div>
    {/block}

    {block name='product_images'}
      {assign var='sliderFor' value=1}
      <!-- Define Number of product for SLIDER -->
      {assign var='thumbCount' value=count($product.images)}

      <div class="js-qv-mask mask {if $thumbCount >= $sliderFor}additional_slider{else}additional_grid{/if}">
        {if $thumbCount >= $sliderFor}
          <ul class="cz-carousel product_list additional-carousel additional-image-slider">
          {else}
            <ul class="product_list grid row gridcount additional-image-slider">
            {/if}

            {foreach from=$product.images item=image}
              <li
                class="thumb-container js-thumb-container {if $thumbCount >= $sliderFor}item{else}product_item col-xs-12 col-sm-6 col-md-6 col-lg-4 col-xl-3{/if}">
                <a href="javaScript:void(0)" class="elevatezoom-gallery" data-image="{$image.bySize.medium_default.url}"
                  data-zoom-image="{$image.bySize.large_default.url}">
                  <img
                    class="thumb js-thumb lazyload {if $image.id_image == $product.default_image.id_image} selected js-thumb-selected{/if}"
                    data-image-medium-src="{$image.bySize.medium_default.url}"
                    data-image-large-src="{$image.bySize.large_default.url}" data-src="{$image.bySize.home_default.url}"
                    src="{$urls.img_url}codezeel/image_loading.svg"
                    width="{$product.default_image.bySize.home_default.width}"
                    height="{$product.default_image.bySize.home_default.height}" {if !empty($image.legend)}
                    alt="{$image.legend}" title="{$image.legend}" {else} alt="{$product.name}" 
                    {/if} loading="lazy">
                </a>
              </li>
            {/foreach}

          </ul>

          {if $thumbCount >= $sliderFor}
            <div class="customNavigation">
              <a class="btn prev additional_prev">&nbsp;</a>
              <a class="btn next additional_next">&nbsp;</a>
            </div>
          {/if}

      </div>

      <div class="image-block_slider">
        {assign var=imagesCount value=$product.images|count}
        <aside id="thumbnails" class="thumbnails js-thumbnails text-xs-center">
          {block name='product_images'}
            <div class="js-modal-mask mask {if $imagesCount <= 5} nomargin {/if}">
              <ul class="product-images js-modal-product-images additional-image-slider">
                {foreach from=$product.images item=image}
                  <li class="thumb-container">
                    <a href="javaScript:void(0)" class="elevatezoom-gallery" data-image="{$image.bySize.medium_default.url}"
                      data-zoom-image="{$image.bySize.large_default.url}">
                      <img
                        class="thumb js-thumb lazyload {if $image.id_image == $product.default_image.id_image} selected {/if}"
                        data-image-medium-src="{$image.bySize.medium_default.url}"
                        data-image-large-src="{$image.bySize.large_default.url}" data-src="{$image.bySize.home_default.url}"
                        src="{$urls.img_url}codezeel/image_loading.svg"
                        width="{$product.default_image.bySize.home_default.width}"
                        height="{$product.default_image.bySize.home_default.height}" {if !empty($image.legend)}
                        alt="{$image.legend}" title="{$image.legend}" {else} alt="{$product.name}" 
                        {/if} loading="lazy">
                    </a>
                  </li>
                {/foreach}
              </ul>
            </div>
          {/block}
          {if $imagesCount > 5}
            <div class="arrows js-modal-arrows">
              <i class="material-icons arrow-up js-modal-arrow-up">&#xE5C7;</i>
              <i class="material-icons arrow-down js-modal-arrow-down">&#xE5C5;</i>
            </div>
          {/if}
        </aside>
      </div>
    {/block}
  </div>
   {* ▼ MOBILE slider *}
{if isset($product.images) && count($product.images) > 0}
  <div class="images-container-slider-mobile">
    <div class="nr-carousel-wrapper">
      <ul class="nr-carousel product_list">
        {block name='product_images_mobile'}
          {assign var='sliderFor' value=1}
          {assign var='thumbCount' value=count($product.images)}

          {foreach from=$product.images item=image}
            <li class="thumb-container js-thumb-container">
              <img class="thumb js-thumb {if $image.id_image == $product.default_image.id_image}selected{/if}"
                   src="{$image.bySize.home_default.url}"
                   alt="{$image.legend|escape:'htmlall':'UTF-8'}"
                   width="400" height="400"
                   loading="lazy">
            </li>
          {/foreach}
        {/block}
      </ul>
      <div class="carousel-dots"></div>
    </div>

    {include file='catalog/_partials/product-flags.tpl'}
  </div>
{else}
  <img class="img-fluid"
       src="{$urls.no_picture_image.bySize.home_default.url}"
       height="{$urls.no_picture_image.bySize.large_default.height}"
       width="{$urls.no_picture_image.bySize.large_default.width}"
       loading="lazy" />
{/if}

</div>
{hook h='displayAfterProductThumbs' product=$product}