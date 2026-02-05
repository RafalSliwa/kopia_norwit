{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
* NOTE: this file is extendable. You can override only selected blocks in your template.
* Path for extending: 'modules/easycarousels/views/templates/hook/product-item.tpl'
*
**}

{block name='product_item'}
    <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}"
        data-id-product-attribute="{$product.id_product_attribute}">
        <div class="thumbnail-container">
            {block name='product_image'}
                {if $settings.image_type != '--'}
                    {$img_type = $settings.image_type}
                    <a href="{$product.url}" class="thumbnail product-thumbnail">
                        {if $product.cover}
                            <img src="{$product.cover.bySize.$img_type.url}" alt="{$product.cover.legend}"
                                {if !empty($image_sizes[$img_type])} width="{$image_sizes[$img_type]['width']}"
                                    height="{$image_sizes[$img_type]['height']}" {/if}{if !empty($product.second_img_src)}
                                data-toggle-src="{$product.second_img_src}" {/if} loading="lazy">
                        {else if isset($urls.no_picture_image)}
                            <img src="{$urls.no_picture_image.bySize.$img_type.url}" {if !empty($image_sizes[$img_type])}
                                width="{$image_sizes[$img_type]['width']}" height="{$image_sizes[$img_type]['height']}" {/if}
                                loading="lazy">
                        {/if}

                    </a>
                {/if}
                {if !empty($settings.product_man) && $product.id_manufacturer && $product.man_name && !empty($product.man_url)}
                    <div class="prop-line product-manufacturer">
                        <a href="{$product.man_url}" class="man-name nowrap">
                            {if !empty($product.man_img_src)}
                                {$m_img_type = $settings.product_man}
                                <img src="{$product.man_img_src}" class="product-manufacturer-img"
                                    {if !empty($image_sizes[$m_img_type])} width="{$image_sizes[$m_img_type]['width']}"
                                    height="{$image_sizes[$m_img_type]['height']}" {/if} loading="lazy">
                            {else}
                                {$product.man_name|truncate:45:'...'}
                            {/if}
                        </a>
                    </div>
                {/if}
            {/block}
            {block name='product_quick_view'}
                {if $settings.quick_view}
                    <div class="highlighted-informations">
                        <a href="#" class="quick-view" data-link-action="quickview">{l s='Quick view' mod='easycarousels'}</a>
                    </div>
                {/if}
            {/block}
            {block name='product_variants'}{* can be optionally filled in extended file *}{/block}
            {block name='product_informations'}
                <div class="product-description">
                    {block name='product_title'}
                        {if !empty($settings.title)}
                            <h5 class="product-title">
                                <a href="{$product.url}" {if !empty($settings.title_one_line)} {/if}>
                                    {$product.name}
                                </a>
                            </h5>
                        {/if}
                    {/block}
                    {block name='product_other_fields'}
                        {if !empty($settings.reference)}
                            <div class="prop-line product-reference"><span class="nowrap">{$product.reference}</span></div>
                        {/if}
                        {if !empty($settings.product_cat) && !empty($product.cat_url)}
                            <div class="prop-line product-category">
                                <a href="{$product.cat_url}" class="cat-name nowrap">{$product.cat_name|truncate:45:'...'}</a>
                            </div>
                        {/if}

                        {if !empty($settings.description)}
                            <div class="prop-line product-description-short">
                                {$product.description_short|strip_tags:'UTF-8'|truncate:$settings.description:'...'}
                            </div>
                        {/if}
                        {if !empty($settings.stock) && $product.availability_message}
                            <div class="prop-line product-availability {$product.availability}">
                                <span class="inline-block nowrap">{$product.availability_message}</span>
                            </div>
                        {/if}
                    {/block}
                    {block name='product_price'}
                        {if $settings.price && $product.show_price}
                            <div class="product-price-and-shipping">
                                {if $product.has_discount}
                                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                                    <span class="regular-price"
                                        aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>

                                    {if $product.discount_type === 'percentage'}
                                        <span class="discount-percentage discount-product">{$product.discount_percentage}
                                            <span>{l s='Off' d='Shop.Theme.Global'}</span>
                                        </span>
                                    {elseif $product.discount_type === 'amount'}
                                        <span class="discount-amount discount-product">{$product.discount_amount_to_display}
                                            <span>{l s='Off' d='Shop.Theme.Global'}</span>
                                        </span>
                                    {/if}
                                {/if}

                                {hook h='displayProductPriceBlock' product=$product type="before_price"}

                                {* Wyświetlanie ceny brutto bez dodatkowego symbolu waluty *}
                                <span class="price price-inc" aria-label="{l s='Price tax incl.' d='Shop.Theme.Catalog'}">
                                    {$product.price}
                                    <span class="tax-label">{l s='tax incl.' d='Shop.Theme.Catalog'}</span>
                                </span>

                                {* Wyświetlanie ceny netto z symbolem waluty i osobnym span dla etykiety *}
                                <span class="price price-exc" aria-label="{l s='Price tax excl.' d='Shop.Theme.Catalog'}">
                                    {$product.price_tax_exc|number_format:2:'.':''} {$currency.sign}
                                    <span class="tax-label">{l s='tax excl.' d='Shop.Theme.Catalog'}</span>
                                </span>

                                {hook h='displayProductPriceBlock' product=$product type='unit_price'}
                                {hook h='displayProductPriceBlock' product=$product type='weight'}
                            </div>
                        {/if}
                    {/block}
                    {block name='product_buttons'}
                        {if $settings.add_to_cart || $settings.view_more || $settings.displayProductListFunctionalButtons}
                            <form type="post" action="{$urls.pages.cart}" class="product-item-buttons">
                                {$customization_required = $product.customizable == 2}
                                {if $settings.add_to_cart}
                                    {if $customization_required && !$settings.view_more}
                                        <a href="{$product.url}"
                                            class="btn btn-primary btn-customize">{l s='Personalize' mod='easycarousels'}</a>
                                    {else}
                                        {if !empty($product.available_atts)}
                                            <div class="ec-atts">
                                                {foreach $product.available_atts as $group_name => $atts}
                                                    <div class="ec-att-group">
                                                        <span class="ec-att-group-name">{$group_name}</span>
                                                        <select name="att_ids[]" class="att-group">
                                                            {foreach $atts as $id_att => $att_name}
                                                                {if $id_att}
                                                                    <option value="{$id_att}" {if $id_att == $atts[0]} selected{/if}>{$att_name}</option>
                                                                {/if}
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        {/if}
                                        <input type="hidden" name="token" value="{$static_token}">
                                        <input type="hidden" name="id_product" value="{$product.id_product}">
                                        <input type="hidden" name="id_product_attribute" value="{$product.id_product_attribute}">
                                        <input type="hidden" name="qty" value="{$product.minimal_quantity}">

                                        <button data-button-action="add-to-cart" class="btn btn-primary" {if !$product.add_to_cart_url}
                                            disabled{/if}>
                                            <span class="material-icon">
                                                <img class="icon" src="{$module_dir}views/img/icon/cart.svg" alt="Cart icon" width="25px"
                                                    height="25px" loading="lazy" />
                                            </span>
                                            <span>{l s='Add to cart' mod='easycarousels'}</span>
                                        {/if}
                                    {/if}
                                    {if $settings.view_more}
                                        <a href="{$product.url}" class="btn btn-tertiary-outline btn-viewmore">
                                            {if $customization_required && $settings.add_to_cart}
                                                <span class="material-icon">
                                                    <img class="icon" src="{$module_dir}views/img/icon/view.svg" alt="See more" width="25px"
                                                        height="25px" loading="lazy" />
                                                </span>
                                                <span>{l s='Personalize' mod='easycarousels'}</span>
                                            {else}
                                                <span class="material-icon">
                                                    <img class="icon" src="{$module_dir}views/img/icon/view.svg" alt="See more" width="25px"
                                                        height="25px" loading="lazy" />
                                                </span>
                                                <span>{l s='View' mod='easycarousels'}</span>
                                            {/if}
                                        </a>
                                    {/if}
                                    {if !empty($settings.displayProductListFunctionalButtons)}
                                        {hook h='displayProductListFunctionalButtons' product=$product}
                                    {/if}
                            </form>
                        {/if}
                    {/block}
                </div>
            {/block}
            {block name='hook_reviews'}
                {if !empty($settings.displayProductListReviews)}
                    {hook h='displayProductListReviews' product=$product hide_thumbnails=empty($settings.thumbnails)|intval}
                {/if}
            {/block}
            {block name='product_stickers'}
                {if $settings.stickers }
                    <ul class="product-flags">
                        {foreach $product.flags as $flag}
                            {if $flag.type == 'out_of_stock' && !empty($settings.stock)}{continue}{/if}
                            <li class="{$flag.type}">{$flag.label}</li>
                        {/foreach}
                    </ul>
                {/if}
            {/block}
        </div>
    </article>
{/block}
{* since 2.7.6 *}