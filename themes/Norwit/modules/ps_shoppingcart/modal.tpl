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

<div id="blockcart-modal" class="modal fade nr-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
          <span aria-hidden="true"><i class="material-icons">close</i></span>
        </button>
        <h4 class="modal-title h6 text-sm-center" id="myModalLabel">
          {l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}</h4>
      </div>
      <div class="modal-body">
        
          <div class="row body-modal">
            <div class="col-md-4 divide-right">
              {if $product.default_image}
                    <img src="{$product.default_image.medium.url}"
                      data-full-size-image-url="{$product.default_image.large.url}" title="{$product.default_image.legend}"
                      alt="{$product.default_image.legend}" loading="lazy" class="product-image">
                  {else}
                    <img src="{$urls.no_picture_image.bySize.medium_default.url}" loading="lazy" class="product-image" />
                  {/if}
            </div>
            <div class="col-md-8">
             <h5 class="h6 product-name">{$product.name}</h5>
               <div class="cart-info">
                 <div class="delivery-info">
                    <div class="delivery-message-wrapper" id="delivery-message-wrapper">
                      {* Check if custom delivery time module is available and has custom message *}
                      {assign var="customDeliveryModule" value=Module::getInstanceByName('customdeliverytime')}
                      {if $customDeliveryModule && $customDeliveryModule->active}
                        {assign var="customDeliveryMessage" value=$customDeliveryModule->getCustomDeliveryMessage($product.id_product)}
                      {/if}
                      
                      {if isset($customDeliveryMessage) && $customDeliveryMessage}
                        {* Display custom delivery message *}
                         {capture name="timeIcon"}<img class="icon" src="{$urls.base_url}modules/relatedproducts/views/img/time.svg" width="35" height="35" loading="lazy" alt="Icon" />{/capture}
                        <p class="delivery-message-custom">
                          <span class="material-icon">
                             {$smarty.capture.timeIcon nofilter}
                          </span>
                          <span class="delivery-message">
                            {$customDeliveryMessage|escape:'html':'UTF-8'}
                          </span>
                        </p>
                      {else}
                        {* Original delivery logic *}
                         {capture name="timeIcon"}<img class="icon" src="{$urls.base_url}modules/relatedproducts/views/img/time.svg" width="35" height="35" loading="lazy" alt="Icon" />{/capture}
                        {assign var="currentHour" value=$smarty.now|date_format:"%H"}
                        {assign var="currentDay" value=$smarty.now|date_format:"%w"}
                        {assign var="productQuantity" value=$product.quantity_available}
                       
                       {if $productQuantity <= 0}
                          <p class="delivery-message-out-of-stock">
                              <span class="material-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px"
                                  fill="#1d1d1d">
                                  <path
                                    d="M273.33-160q-50 0-83-35.67-33-35.66-29.66-85h-59q-14.17 0-23.75-9.61-9.59-9.62-9.59-23.84 0-14.21 9.59-23.71 9.58-9.5 23.75-9.5h86q15.66-18.34 37.66-28.84 22-10.5 48-10.5t48 10.5q22 10.5 37.67 28.84h187.67l89.33-386H224.67q-14.34 0-23.5-9.62-9.17-9.62-9.17-23.83 0-14.22 9.58-23.72 9.59-9.5 23.75-9.5h452q16 0 26.34 12.67Q714-774.67 710.33-759L681-633.33h89q15.83 0 30 7.08t23.33 19.58l78.34 104.34q9 12 11.66 25.16 2.67 13.17.34 27.5L885.33-308q-2.54 11.96-11.84 19.65-9.31 7.68-21.16 7.68H800q3.33 49.34-30 85Q736.67-160 686.67-160t-83-35.67q-33-35.66-29.67-85H386.67q3.33 49.34-30 85Q323.33-160 273.33-160ZM635-433.33h207.67l5.33-29-78-104.34H665.73L635-433.33Zm-82.33 59.66 6.5-27q6.5-27 15.16-66.33 3.67-15 6.67-28.33 3-13.34 5.67-24.34l6.5-27q6.5-27 15.16-66.33 8.67-39.33 14.84-66.33l6.16-27 6.67-27-89.33 386 6-26.34Zm-490-60q-14.34 0-23.5-9.58Q30-452.83 30-467t9.5-23.75q9.5-9.58 23.83-9.58h153.34q14.16 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71-9.59 9.5-23.75 9.5h-154Zm80-146q-14.17 0-23.75-9.61-9.59-9.62-9.59-23.84 0-14.21 9.59-23.71 9.58-9.5 23.75-9.5h194q14.16 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71-9.59 9.5-23.75 9.5h-194Zm131 353q19.33 0 32.83-13.83t13.5-33.17q0-19.33-13.42-32.83-13.41-13.5-33.25-13.5-19 0-32.83 13.42-13.83 13.41-13.83 33.25 0 19 13.83 32.83 13.83 13.83 33.17 13.83Zm413.33 0q19.33 0 32.83-13.83t13.5-33.17q0-19.33-13.41-32.83Q706.5-320 686.67-320q-19 0-32.84 13.42Q640-293.17 640-273.33q0 19 13.83 32.83 13.84 13.83 33.17 13.83Z" />
                                </svg>
                              </span>
                              <span class="delivery-message">
                                {if $product.available_later}
                                  {$product.available_later|escape:'html':'UTF-8'}
                                {elseif $product.availability_message}
                                  {$product.availability_message|escape:'html':'UTF-8'}
                                {else}
                                  {l s='Shipping from supplier warehouse' d='Shop.Theme.Global'}
                                {/if}
                              </span>
                          </p>
                        {elseif $productQuantity > 0}
                          {* Calculate delivery day label based on current hour and day of week *}
                          {* %w: 0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday *}
                          {* Before 13:00: Ships today, arrives next business day *}
                          {* After 13:00: Ships tomorrow, arrives day after next business day *}
                          {if $currentHour < 13}
                            {* Mon-Thu (1-4): tomorrow *}
                            {* Friday (5): Monday (weekend skip) *}
                            {* Sat-Sun (6, 0): Tuesday *}
                            {if $currentDay >= 1 && $currentDay <= 4}
                              {assign var="deliveryLabel" value={l s='At your place tomorrow' d='Shop.Theme.Global'}}
                            {elseif $currentDay == 5}
                              {assign var="deliveryLabel" value={l s='At your place on Monday' d='Shop.Theme.Global'}}
                            {else}
                              {assign var="deliveryLabel" value={l s='At your place on Tuesday' d='Shop.Theme.Global'}}
                            {/if}
                          {else}
                            {* After 13:00: ships next business day *}
                            {if $currentDay == 1}
                              {assign var="deliveryLabel" value={l s='At your place on Wednesday' d='Shop.Theme.Global'}}
                            {elseif $currentDay == 2}
                              {assign var="deliveryLabel" value={l s='At your place on Thursday' d='Shop.Theme.Global'}}
                            {elseif $currentDay == 3}
                              {assign var="deliveryLabel" value={l s='At your place on Friday' d='Shop.Theme.Global'}}
                            {elseif $currentDay == 4}
                              {assign var="deliveryLabel" value={l s='At your place on Monday' d='Shop.Theme.Global'}}
                            {else}
                              {* Fri, Sat, Sun after 13:00 → Tuesday *}
                              {assign var="deliveryLabel" value={l s='At your place on Tuesday' d='Shop.Theme.Global'}}
                            {/if}
                          {/if}

                          <p class="delivery-message">
                            <span class="material-icon">
                              {$smarty.capture.timeIcon nofilter}
                            </span>
                            <span class="delivery-message">
                              {$deliveryLabel}
                            </span>
                          </p>
                        {/if}
                      {/if}
                    </div>


                    <div class="summary-product">
                      {capture name="truckIcon"}<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="35" height="35" loading="lazy" alt="Icon" />{/capture}
                      {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                        {assign var="cartTotal" value=$cart.totals.total_including_tax.amount}
                      {else}
                        {assign var="cartTotal" value=$cart.totals.total.amount}
                      {/if}

                      {* Call hook early to assign customcarrier flags before they're used *}
                      {hook h='displayCartModalContent' product=$product}

                      {if isset($free_shipping_threshold) && $free_shipping_threshold > 0}
                          {assign var="dataThreshold" value=$free_shipping_threshold}
                      {else}
                          {assign var="dataThreshold" value=0}
                      {/if}

                      <div class="delivery-status-wrapper" data-cart-total="{$cartTotal}" data-free-threshold="{$dataThreshold}">
                        <p class="delivery-info-message">
                          <span class="material-icon">
                               {$smarty.capture.truckIcon nofilter}
                          </span>
                          <span class="delivery-text dynamic-shipping-price"
                                data-product-id="{$product.id_product}"
                                data-paid-label="{l s='Dostawa od:' d='Modules.Relatedproducts.Shop'}"
                                data-free-label="{l s='Dostawa:' d='Modules.Relatedproducts.Shop'}"
                                data-free-value="{l s='Za darmo!' d='Modules.Relatedproducts.Shop'}"
                                data-initial-price="{if isset($is_product_free_shipping) && $is_product_free_shipping}{l s='Za darmo!' d='Modules.Relatedproducts.Shop'}{else}...{/if}"
                                {if isset($is_product_free_shipping) && $is_product_free_shipping}data-initial-free="1"{/if}>
                            {if isset($is_product_free_shipping) && $is_product_free_shipping}
                              Dostawa: Za darmo!
                            {else}
                              Dostawa od: ...
                            {/if}
                          </span>
                        </p>
                      </div>
                    </div>


                  </div>
                  <div class="product-pricing">
                    <p class="product-price-gross">{$product.price} </p>
                    {if $product.price_tax_exc && $product.price_tax_exc != $product.price}
                      <p class="product-price-net"> {$product.price_tax_exc|number_format:2:",":" "} {$currency.sign} {l s='net price' d='Shop.Theme.Catalog'}</p>
                    {/if}
                  </div>
                  
                  {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                </div>

              <div class="cart-content">
                {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                  <p><span
                      class="label">{$cart.totals.total.label}&nbsp;{$cart.labels.tax_short}</span>&nbsp;<span>{$cart.totals.total.value}</span>
                  </p>
                {/if}

                {if $cart.subtotals.tax}
                  <p class="product-tax"><span
                      class="label">{l s='%label%:' sprintf=['%label%' => $cart.subtotals.tax.label] d='Shop.Theme.Global'}</span>&nbsp;<span
                      class="value">{$cart.subtotals.tax.value}</span></p>
                {/if}
                {* ========== PROGRESSBAR COMMENTED OUT ==========
                 Show progress bar based on customcarrier flags:
                   - Hide if product has free_shipping (already free)
                   - Hide if product always pays (excluded or separate_package)
                   - Hide if no threshold configured
                   - Hide if ANY product in cart blocks free shipping threshold
                     (has custom settings with base_shipping_cost > 0 AND apply_threshold = 0)
                   - Show otherwise *}
                {*
                {assign var="showProgressBar" value=true}
                {if isset($is_product_free_shipping) && $is_product_free_shipping}
                  {assign var="showProgressBar" value=false}
                {/if}
                {if isset($product_always_pays_shipping) && $product_always_pays_shipping}
                  {assign var="showProgressBar" value=false}
                {/if}
                {if isset($cart_has_products_blocking_free_shipping) && $cart_has_products_blocking_free_shipping}
                  {assign var="showProgressBar" value=false}
                {/if}
                {if $dataThreshold <= 0}
                  {assign var="showProgressBar" value=false}
                {/if}

                {if $showProgressBar}
                <div class="free-delivery-info-wrapper">
                    {assign var="freeShippingThreshold" value=$dataThreshold}
                    {assign var="dynamicThreshold" value=$dataThreshold}

                    {assign var="amountNeeded" value=($freeShippingThreshold - $cartTotal)}
                    {assign var="progressPercentage" value=0}
                    {if $freeShippingThreshold > 0}
                      {assign var="rawPercentage" value=($cartTotal / $freeShippingThreshold * 100)}
                      {if $rawPercentage > 100}
                        {assign var="progressPercentage" value=100}
                      {elseif $rawPercentage < 0}
                        {assign var="progressPercentage" value=0}
                      {else}
                        {assign var="progressPercentage" value=$rawPercentage}
                      {/if}
                    {/if}

                    <p class="free-delivery-info" data-free-delivery-info>
                      <span class="delivery-text">{l s='Do darmowej dostawy brakuje Ci' d='Modules.Relatedproducts.Shop'}
                        <span class="amount-needed" data-amount-needed></span>
                      </span>
                    </p>

                     Progress bar 
                    <div class="delivery-progress-wrapper">
                      <div class="delivery-progress-labels">
                        <span class="progress-start"><p>0</p><p>{$currency.sign}</p></span>
                        <div class="delivery-progress-bar">
                          <div class="delivery-progress-fill" style="width: {$progressPercentage|string_format:"%.1f"}%;" data-cart-total="{$cartTotal}" data-progress-fill></div>
                        </div>
                        <span class="progress-end"><p class="threshold-value" data-threshold-value>{$freeShippingThreshold}</p><p>{$currency.sign}</p></span>
                      </div>
                    </div>
                </div>
                {/if}
                *}
                {* ========== END PROGRESSBAR COMMENTED OUT ========== *}
                <div class="cart-content-btn">
                  <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">{l s='Continue shopping' d='Shop.Theme.Actions'}</button>
                  <a href="{$cart_url}" class="btn btn-primary">{l s='Proceed to checkout' d='Shop.Theme.Actions'}</a>
                </div>
              </div>
            </div>  
          </div>
        {hook h='displayProductModal' id_product=$product.id_product}
      </div>
      {hook h='displayCartModalFooter' product=$product}
  </div>
  </div>
</div>