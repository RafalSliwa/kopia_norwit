{if $product.show_price}
    <div class="product-prices js-product-prices">
        {block name='product_discount'}
            {if $product.has_discount}
                <div class="product-discount">
                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
                    <!--<span class="regular-price">{$product.regular_price}</span>-->
                </div>
            {/if}
        {/block}

        {block name='product_price'}
            <div class="product-price{if $product.has_discount} has-discount{/if}">

                <div class="price">
                    {* Remove unnecessary characters from price and convert to number *}
                    {assign var="clean_price" value=$product.price|replace:' ':''} {* usuń spacje *}
                    {assign var="clean_price" value=$clean_price|regex_replace:"/[^\d.,]/":""}
                   {* If the price contains both a period and a comma, remove the periods (thousand separator), replace the comma with a period *}
                    {if $clean_price|strpos:',' !== false && $clean_price|strpos:'.' !== false}
                        {assign var="clean_price" value=$clean_price|replace:'.':''}
                        {assign var="clean_price" value=$clean_price|replace:',':'.'}
                    {else}
                        {assign var="clean_price" value=$clean_price|replace:',':'.'}
                    {/if}
                    {assign var="final_price" value=$clean_price|floatval}

                    
                    {* If price is 0 or doesn't exist, display message *}
                    {if !$product.price || $final_price <= 0}
                        <span class="price-inc ask-for-price">
                            <span>{l s='Ask for price' d='Shop.Theme.Catalog'}</span>
                        </span>
                    {else}
                        {* Display gross price *}
                        <span class="price-inc">
                            {$product.price}
                            <span class="tax-label">{l s='tax incl.' d='Shop.Theme.Catalog'}</span>
                        </span>

                        {* Display net price *}
                        <span class="price-exc">
                            {$product.price_tax_exc|number_format:2:',':''} {$currency.sign}
                            <span class="tax-label">{l s='tax excl.' d='Shop.Theme.Catalog'}</span>
                        </span>

                        {*{if $product.has_discount}
                        {if $product.discount_type === 'percentage'}
                            <span class="discount discount-percentage">
                                {l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}
                            </span>
                        {else}
                            <span class="discount discount-amount">
                                {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
                            </span>
                        {/if}
                    {/if}*}
                    {/if}
                </div>

                <div class="add">
                    {* Button locked only when price is 0 *}
                  <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit"
                        {if $final_price <= 0}disabled{/if}>
                        <span class="material-icon">
                            <img class="icon" src="{$urls.img_url}codezeel/cart.svg" width="30" height="30" loading="lazy"
                                alt="Icon" />
                        </span>
                        {l s='Add to cart' d='Shop.Theme.Actions'}
                    </button>
                    {hook h='displayProductPriceBlock' product=$product type="after_price"}
                </div>

                <div class="product-offerts-mobile">
                    {hook h='displayProductAdditionalInfo' product=$product}
                    {* <button type="button" class="btn btn-primary" onclick="window.location.href='tel:+48573580892'">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                            fill="#f8f8f8">
                            <path
                                d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z" />
                        </svg>
                    </button> *}
                </div>
            </div>

            {block name='product_unit_price'}
                {if $displayUnitPrice}
                    <p class="product-unit-price sub">{$product.unit_price_full}</p>
                {/if}
            {/block}
        </div>

    {/block}

    {block name='product_without_taxes'}
        {if $priceDisplay == 2}
            <p class="product-without-taxes">
                {$product.price_tax_exc|number_format:2:'.':''} {$currency.sign}
                <span class="tax-label">{l s='tax excl.' d='Shop.Theme.Catalog'}</span>
            </p>
        {/if}
    {/block}

    {block name='product_pack_price'}
        {if $displayPackPrice}
            <p class="product-pack-price">
                <span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span>
            </p>
        {/if}
    {/block}

    {block name='product_ecotax'}
        {if !$product.is_virtual && $product.ecotax.amount > 0}
            <p class="price-ecotax">
                {l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
                {if $product.has_discount}
                    {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
                {/if}
            </p>
        {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

    {**<div class="tax-shipping-delivery-label">
        {if !$configuration.taxes_enabled}
          {l s='No tax' d='Shop.Theme.Catalog'}
        {elseif $configuration.display_taxes_label}
          {$product.labels.tax_long}
        {/if}
        {hook h='displayProductPriceBlock' product=$product type="price"}
        {hook h='displayProductPriceBlock' product=$product type="after_price"}
        {if $product.is_virtual == 0}
          {if $product.additional_delivery_times == 1}
            {if $product.delivery_information}
              <span class="delivery-information">{$product.delivery_information}</span>
            {/if}
          {elseif $product.additional_delivery_times == 2}
            {if $product.quantity >= $product.quantity_wanted}
              <span class="delivery-information">{$product.delivery_in_stock}</span>
               Out of stock message should not be displayed if customer can't order the product. 
            {elseif $product.add_to_cart_url}
              <span class="delivery-information">{$product.delivery_out_stock}</span>
            {/if}
          {/if}
        {/if}
      </div>
    </div>*}
{/if}