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
                                data-src="{$image.bySize.home_default.url}" src="{$urls.img_url}codezeel/image_loading.svg"
                                width="300" height="300" style="aspect-ratio: 1/1;" alt="{$image.legend}"
                                title="{$image.legend}" loading="lazy">
                        </li>
                    {/foreach}
                {/block}
            </ul>
            <div class="carousel-dots"></div>
        </div>
    </div>
</div>
{hook h='displayAfterProductThumbs' product=$product}