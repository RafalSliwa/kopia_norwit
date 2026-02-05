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
<div id="product-list-header" class="nr-product-list-header">

    <div class="block-category card card-block">

        {**{if !empty($category.image.large.url)}
                <div class="category-cover">
                    <img 
                        class="lazyload" 
                        src="{$urls.img_url}codezeel/image_loading.svg"
                        data-src="{$category.image.large.url}" 
                        alt="{if !empty($category.image.legend)}{$category.image.legend}{else}{$category.name}{/if}" 
                        loading="lazy"
                        width="{$category.image.large.width}"
                        height="{$category.image.large.height}"
                    >
                </div>
            {/if} 
            <div class="nr-number-products">
                {if $listing.pagination.total_items > 1}
                    <p>{l s='(%product_count% products)' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.pagination.total_items]}
                    </p>
                {else if $listing.pagination.total_items > 0}
                    <p>{l s='There is 1 product.' d='Shop.Theme.Catalog'}</p>
                {/if}
            </div>*}

        <h1 class="h1">{$category.name} </h1>

        {*{if $category.description}
            <div id="category-description" class="text-muted top-category-description">{$category.description nofilter}
            </div>
        {/if}*}
    </div>
</div>