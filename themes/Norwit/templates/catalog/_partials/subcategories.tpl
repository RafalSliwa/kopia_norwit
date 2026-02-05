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
{if !empty($subcategories)}
  {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories)}
    <div id="subcategories">
      {*<h4 class="subcategory-heading">{l s='Subcategories' d='Shop.Theme.Category'}
      </h4>*}
      <div class="nr-subcategory-carousel">
        <div class="carousel-track">
          {foreach from=$subcategories item=subcategory}
            <div class="carousel-item">
              <div class="subcategory-image">
                <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}"
                  title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                  {if !empty($subcategory.image.small.url)}
                    <img class="replace-2x lazyload img img-fluid" src="{$urls.img_url}codezeel/image_loading.svg"
                      data-src="{$subcategory.image.small.url}" alt="{$subcategory.name|escape:'html':'UTF-8'}" loading="lazy"
                      width="{$subcategory.image.small.width}" height="{$subcategory.image.small.height}" />
                  {else}
                    <img class="replace-2x lazyload img img-fluid " src="{$urls.img_url}codezeel/image_loading.svg"
                      data-src="{$urls.no_picture_image.bySize.small_default.url}"
                      alt="{$subcategory.name|escape:'html':'UTF-8'}"
                      width="{$urls.no_picture_image.bySize.small_default.width}"
                      height="{$urls.no_picture_image.bySize.small_default.height}" />
                  {/if}
                </a>
              </div>

              <a class="subcategory-name"
                href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">
                {$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}
              </a>
              {*
              {if $subcategory.description}
                <div class="cat_desc">{$subcategory.description|unescape:'html' nofilter}</div>
              {/if}
              *}
            </div>
          {/foreach}
        </div>
        <div class="carousel-dots"></div>
      </div>
    </div>
  {/if}
{/if}