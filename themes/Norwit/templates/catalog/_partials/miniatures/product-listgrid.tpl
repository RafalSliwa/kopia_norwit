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
{block name='product_miniature_item'}
	<div class="product-miniature js-product-miniature" data-id-product="{$product.id_product}"
		data-id-product-attribute="{$product.id_product_attribute}">
		<div class="thumbnail-container">
			{block name='product_thumbnail'}
				{if $product.cover}
					<a href="{$product.url}" class="thumbnail product-thumbnail">
						<img class="lazyload test" src="{$urls.img_url}codezeel/image_loading.svg"
							data-src="{$product.cover.bySize.medium_default.url}"
							alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
							loading="lazy" data-full-size-image-url="{$product.cover.large.url}"
							width="{$product.cover.bySize.home_default.width}" height="{$product.cover.bySize.home_default.height}">
						{hook h="displayCzHoverImage" id_product=$product.id_product home='home_default' large='large_default'}
					</a>
				{else}
					<a href="{$product.url}" class="thumbnail product-thumbnail">
						<img class="lazyload test" src="{$urls.img_url}codezeel/image_loading.svg"
							data-src="{$urls.no_picture_image.bySize.home_default.url}" loading="lazy"
							width="{$urls.no_picture_image.bySize.home_default.width}"
							height="{$urls.no_picture_image.bySize.home_default.height}" />
					</a>
				{/if}
			{/block}

			{*<div class="highlighted-informations{if !$product.main_variants} no-variants{/if}">
				{block name='product_variants'}
					{if $product.main_variants}
						{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
					{/if}
				{/block}

				{block name='product_availability'}
					<span class="product-availability">
						{if $product.show_availability && $product.availability_message}
							{if $product.availability == 'available'}
								<span class="product-available">
									<i class="material-icons">&#xE5CA;</i>
								{elseif $product.availability == 'last_remaining_items'}
									<span class="product-last-items">
										<i class="material-icons">&#xE002;</i>
									{else}
										<span class="product-unavailable">
											<i class="material-icons">&#xE14B;</i>
										{/if}
										{$product.availability_message}
									</span>
								{/if}
							</span>
						{/block}
			</div>*}

			{include file='catalog/_partials/product-flags.tpl'}
			{*<div class="outer-functional">
				<div class="functional-buttons">
					{block name='quick_view'}
						<div class="quickview">
							<a href="#" class="quick-view js-quick-view" data-link-action="quickview">
								<i class="material-icons search">&#xE417;</i> {l s='Quick view' d='Shop.Theme.Actions'}
							</a>
						</div>
					{/block}
				</div>
			</div>*}
			{block name='brand_name'}
				<div class="brand-title">
					{assign var='product_brand_url' value=$link->getManufacturerLink($product.id_manufacturer)}
					{assign var='manufacturer_image_url' value=$link->getManufacturerImageLink($product.id_manufacturer, 'medium_default')}

					{if isset($product.id_manufacturer) && $product.id_manufacturer}
						{$manufacturer_name = Manufacturer::getNameById($product.id_manufacturer)}
						<div class="product-brand-img">
							<a href="{$product_brand_url}">
								<img class="product-img img-responsive" src="{$manufacturer_image_url}" title="{$manufacturer_name}"
									alt="{$manufacturer_name}" width="80" height="80" loading="lazy">
							</a>
						</div>
					{/if}

					{if isset($product.id_manufacturer) && $product.id_manufacturer}
						{$manufacturer_name = Manufacturer::getNameById($product.id_manufacturer)}
						<div class="product-brand-name">
							<a href="{$link->getManufacturerLink($product.id_manufacturer)}" title="{$manufacturer_name}">
								{$manufacturer_name}
							</a>
						</div>
					{/if}

				</div>
			{/block}
		</div>
		<div class="product-description">
			{block name='product_name'}
				<h3 class="h3 product-title"><a href="{$product.url}"
						content="{$product.url}">{$product.name|truncate:80:'...'}</a></h3>
			{/block}

			{block name='product_reference'}
				{if isset($product.reference_to_display) && $product.reference_to_display neq ''}
					<div class="product-reference">
						<label class="label">{l s='Reference' d='Shop.Theme.Catalog'}: </label>
						<span itemprop="sku">{$product.reference_to_display}</span>
					</div>
				{/if}
			{/block}

			{block name='brand_name'}
				<div class="brand-title">
					{assign var='product_brand_url' value=$link->getManufacturerLink($product.id_manufacturer)}
					{assign var='manufacturer_image_url' value=$link->getManufacturerImageLink($product.id_manufacturer, 'medium_default')}

					{if isset($product.id_manufacturer) && $product.id_manufacturer}
						{$manufacturer_name = Manufacturer::getNameById($product.id_manufacturer)}
						<div class="product-brand-img">
							<a href="{$product_brand_url}">
								<img class="product-img img-responsive" src="{$manufacturer_image_url}" title="{$manufacturer_name}"
									alt="{$manufacturer_name}" width="80" height="80" loading="lazy">
							</a>
						</div>
					{/if}

					{if isset($product.id_manufacturer) && $product.id_manufacturer}
						{$manufacturer_name = Manufacturer::getNameById($product.id_manufacturer)}
						<div class="product-brand-name">
							<a href="{$link->getManufacturerLink($product.id_manufacturer)}" title="{$manufacturer_name}">
								{$manufacturer_name}
							</a>
						</div>
					{/if}

				</div>
			{/block}

			{block name='product_reviews'}
				{hook h='displayProductListReviews' product=$product}
			{/block}

			{block name='deliveryMessage'}
				<div class="delivery-message-wrapper">
					{assign var="currentHour" value=$smarty.now|date_format:"%H"}
					{assign var="currentDay" value=$smarty.now|date_format:"%w"}
					{assign var="productQuantity" value=$product.quantity}
					{if $productQuantity <= 0}
						<p class="delivery-message-out-of-stock">
							<span class="material-icon">
								<svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px"
									fill="#1d1d1d">
									<path
										d="M273.33-160q-50 0-83-35.67-33-35.66-29.66-85h-59q-14.17 0-23.75-9.61-9.59-9.62-9.59-23.84 0-14.21 9.59-23.71 9.58-9.5 23.75-9.5h86q15.66-18.34 37.66-28.84 22-10.5 48-10.5t48 10.5q22 10.5 37.67 28.84h187.67l89.33-386H224.67q-14.34 0-23.5-9.62-9.17-9.62-9.17-23.83 0-14.22 9.58-23.72 9.59-9.5 23.75-9.5h452q16 0 26.34 12.67Q714-774.67 710.33-759L681-633.33h89q15.83 0 30 7.08t23.33 19.58l78.34 104.34q9 12 11.66 25.16 2.67 13.17.34 27.5L885.33-308q-2.54 11.96-11.84 19.65-9.31 7.68-21.16 7.68H800q3.33 49.34-30 85Q736.67-160 686.67-160t-83-35.67q-33-35.66-29.67-85H386.67q3.33 49.34-30 85Q323.33-160 273.33-160ZM635-433.33h207.67l5.33-29-78-104.34H665.73L635-433.33Zm-82.33 59.66 6.5-27q6.5-27 15.16-66.33 3.67-15 6.67-28.33 3-13.34 5.67-24.34l6.5-27q6.5-27 15.16-66.33 8.67-39.33 14.84-66.33l6.16-27 6.67-27-89.33 386 6-26.34Zm-490-60q-14.34 0-23.5-9.58Q30-452.83 30-467t9.5-23.75q9.5-9.58 23.83-9.58h153.34q14.16 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71-9.59 9.5-23.75 9.5h-154Zm80-146q-14.17 0-23.75-9.61-9.59-9.62-9.59-23.84 0-14.21 9.59-23.71 9.58-9.5 23.75-9.5h194q14.16 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71-9.59 9.5-23.75 9.5h-194Zm131 353q19.33 0 32.83-13.83t13.5-33.17q0-19.33-13.42-32.83-13.41-13.5-33.25-13.5-19 0-32.83 13.42-13.83 13.41-13.83 33.25 0 19 13.83 32.83 13.83 13.83 33.17 13.83Zm413.33 0q19.33 0 32.83-13.83t13.5-33.17q0-19.33-13.41-32.83Q706.5-320 686.67-320q-19 0-32.84 13.42Q640-293.17 640-273.33q0 19 13.83 32.83 13.84 13.83 33.17 13.83Z" />
								</svg>
							</span>
							<span class="delivery-mesage">
								{if $product.available_later}
									{$product.available_later}
								{elseif $product.availability_message}
									{$product.availability_message}
								{else}
									{l s='Shipping from supplier warehouse' d='Shop.Theme.Global'}
								{/if}
							</span>
						</p>
					{else}

						{* Przed 13:00: Pon-Czw → jutro, Pt → poniedziałek, Sob-Nd → wtorek *}
						{if $currentHour < 13}
							{if $currentDay >= 1 && $currentDay <= 4}
								{* Mon-Thu before 13:00 → tomorrow *}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place tomorrow' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 5}
								{* Friday before 13:00 → Monday *}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Monday' d='Shop.Theme.Global'}
									</span>
								</p>
							{else}
								{* Saturday (6) or Sunday (0) before 13:00 → Tuesday *}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Tuesday' d='Shop.Theme.Global'}
									</span>
								</p>
							{/if}
						{else}
							{if $currentDay == 5}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Tuesday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 6}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Tuesday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 0}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Tuesday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 1}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Wednesday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 2}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Thursday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 3}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Friday' d='Shop.Theme.Global'}
									</span>
								</p>
							{elseif $currentDay == 4}
								<p class="delivery-message">
									<span class="material-icon">
										<img class="icon" src="{$urls.img_url}codezeel/delivery_truck_speed.svg" width="80" height="80"
											loading="lazy" alt="Icon" />
									</span>
									<span class="delivery-mesage">
										{l s='At your place on Monday' d='Shop.Theme.Global'}
									</span>
								</p>
							{/if}
						{/if}
					{/if}
				</div>
			{/block}
			{block name='product_price_and_shipping'}
				{if $product.show_price}
					<div class="product-price-and-shipping">

						{if $product.has_discount}
							<div class="product-discount">
								{hook h='displayProductPriceBlock' product=$product type="old_price"}
								<span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">
									{$product.regular_price}</span>
								{if $product.discount_type === 'percentage'}
									<span class="discount-percentage discount-product">
										{$product.discount_percentage}
										<span>{l s='Off' d='Shop.Theme.Global'}</span>
									</span>
								{elseif $product.discount_type === 'amount'}
									<span class="discount-amount discount-product">
										{$product.discount_amount_to_display}
										<span>{l s='Off' d='Shop.Theme.Global'}</span>
									</span>
								{/if}
							</div>
						{/if}

						{hook h='displayProductPriceBlock' product=$product type="before_price"}

						{assign var="clean_price" value=$product.price|regex_replace:"/[^\d.,]/":""}
						{assign var="clean_price" value=$clean_price|replace:",":"."}
						{assign var="final_price" value=$clean_price|floatval}

						{if !$product.price || $final_price <= 0}
							<span class="price price-inc ask-for-price" aria-label="{l s='Ask for price' d='Shop.Theme.Catalog'}">
								<span>{l s='Ask for price' d='Shop.Theme.Catalog'}</span>
							</span>
						{else}
							<span class="price price-inc" aria-label="{l s='Price tax incl.' d='Shop.Theme.Catalog'}">
								{$product.price}
								<span class="tax-label">{l s='tax incl.' d='Shop.Theme.Catalog'}</span>
							</span>

							<span class="price price-exc" aria-label="{l s='Price tax excl.' d='Shop.Theme.Catalog'}">
								{$product.price_tax_exc|number_format:2:',':''} {$currency.sign}
								<span class="tax-label">{l s='tax excl.' d='Shop.Theme.Catalog'}</span>
							</span>
						{/if}

						{hook h='displayProductPriceBlock' product=$product type='unit_price'}
						{hook h='displayProductPriceBlock' product=$product type='weight'}

					</div>
				{/if}
			{/block}

			{block name='product_features'}
				{if $product.grouped_features}
					<div class="product-features">
						<h4 class="title-product-features">
							{l s='View technical data' d='Shop.Theme.Global'}
						</h4>
						<ul class="product-features-list hidden-features">
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
				{else}
					<div class="product-features">
						<h4 class="no-features-message">
							{l s='No technical data available' d='Shop.Theme.Global'}
						</h4>
					</div>
				{/if}
			{/block}
			{*{block name='product_buy'}







































				{if !$configuration.is_catalog}
																																																																																																																										<div class="product-actions">







































					{if !$product.main_variants}
																																																																																																																																																																			<form action="{$urls.pages.cart}" method="post" class="add-to-cart-or-refresh">
																																																																																																																																																																				<input type="hidden" name="token" value="{$static_token}">
																																																																																																																																																																				<input type="hidden" name="id_product" value="{$product.id}" class="product_page_product_id">
																																																																																																																																																																				<input type="hidden" name="id_customization" value="0" id="product_customization_id"
																																																																																																																																																																					class="js-product-customization-id">
																																																																																																																																																																				<button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit"







































						{if !$product.add_to_cart_url}disabled 






































						{/if}>







































						{l s='Add to cart' d='Shop.Theme.Actions'}
																																																																																																																																																																				</button>

																																																																																																																																																																				<button class="add-to-cart nr-add-to-cart" data-button-action="add-to-cart" type="submit">
																																																																																																																																																																					<svg id="Layer_1" class="icon-cart" data-name="Layer 1" fill="#009703"
																																																																																																																																																																						xmlns="http://www.w3.org/2000/svg" viewBox="0 0 423 376.06">
																																																																																																																																																																						<path class="cls-1"
																																																																																																																																																																							d="M10.6,4.67a10.15,10.15,0,0,0-1.83.7,7.77,7.77,0,0,0,0,13.87c1.89.94-.93.88,34.87.88H75.78l.06.27c0,.15,6.87,49.71,15.19,110.12S106.27,240.66,106.36,241a8,8,0,0,0,4,4.74,9.72,9.72,0,0,0,1.89.69c1.29.27,275.4.27,276.69,0a8,8,0,0,0,5.85-5.43c.24-.9,23.75-165.5,23.75-166.35a8.06,8.06,0,0,0-.87-3.42,7.93,7.93,0,0,0-5.29-4.09c-1.3-.27-269.66-.27-271,0a8,8,0,0,0-5.3,4.09,8.19,8.19,0,0,0-.86,3.42c0,.85,18,126.11,18.24,127.11a7.78,7.78,0,0,0,5.89,5.61,7.63,7.63,0,0,0,7.11-2.12,7.31,7.31,0,0,0,2.29-5c0-1.06-.74-5.82-8.44-59.36-4.6-32-8.37-58.21-8.37-58.24s45.79-.06,118.16-.06H401.69l-.06.39c0,.22-4.81,33.61-10.6,74.21L380.48,231H120.69l-.06-.29c0-.14-6.87-49.69-15.19-110.11S90.2,10.48,90.11,10.11a8,8,0,0,0-4-4.75c-1.91-.94,1.35-.87-38.75-.85C17.17,4.5,11.27,4.54,10.6,4.67Z" />
																																																																																																																																																																						<path class="cls-1"
																																																																																																																																																																							d="M165.67,278l-1.89.19a46.9,46.9,0,0,0-41.89,41.91,70.21,70.21,0,0,0,0,9.4,47.08,47.08,0,0,0,41.46,41.89,62.75,62.75,0,0,0,10.44,0A47,47,0,0,0,214.48,334l.27-1.34,20.62-.06c19.63-.06,20.65-.07,21.29-.28a7.86,7.86,0,0,0,0-15.06c-.64-.21-1.66-.22-21.29-.28l-20.61-.06-.27-1.35a47,47,0,0,0-40.58-37.34C172.36,278,166.8,277.85,165.67,278Zm7.26,15.86a29.78,29.78,0,0,1,5.37,1.28,30.44,30.44,0,0,1,12.41,7.7,31,31,0,0,1,8.85,18.11,47,47,0,0,1,0,7.69A31.87,31.87,0,0,1,194.61,342a33,33,0,0,1-8.52,8.64,32,32,0,0,1-13.71,5.17,47,47,0,0,1-7.69,0,31.7,31.7,0,0,1-15.62-6.59,34.53,34.53,0,0,1-6.38-6.88,31.27,31.27,0,0,1,22.86-48.66,45.45,45.45,0,0,1,7.38.17Z" />
																																																																																																																																																																						<path class="cls-1"
																																																																																																																																																																							d="M329.71,278l-1.89.19a48.39,48.39,0,0,0-10.4,2.26,47,47,0,0,0-31,36.45,40,40,0,0,0-.61,7.9c0,1.87.07,3.86.16,4.7a47.06,47.06,0,0,0,41.46,41.89,62.63,62.63,0,0,0,10.43,0,53.4,53.4,0,0,0,6.4-1.19,47,47,0,0,0,35-40.7,70.21,70.21,0,0,0,0-9.4A47,47,0,0,0,338,278.19C336.4,278,330.84,277.85,329.71,278ZM337,293.81a30,30,0,0,1,5.37,1.28,30.44,30.44,0,0,1,12.41,7.7,31,31,0,0,1,8.85,18.11,37.13,37.13,0,0,1,.16,3.85,28.69,28.69,0,0,1-1.51,9.76,30.61,30.61,0,0,1-7.5,12.2,31,31,0,0,1-18.33,9.06,47,47,0,0,1-7.69,0,31.66,31.66,0,0,1-15.62-6.59,34.24,34.24,0,0,1-6.38-6.88,31.26,31.26,0,0,1,22.86-48.66,45.45,45.45,0,0,1,7.38.17Z" />
																																																																																																																																																																					</svg>
																																																																																																																																																																				</button>
																																																																																																																																																																			</form>








































					{else}
																																																																																																																																																																			<a href="{$product.url}" class="btn btn-primary add-to-cart">







































						{l s='Options' d='Shop.Theme.Global'}
																																																																																																																																																																			</a>
																																																																																																																																																																			<a href="{$product.url}" class="nr-options add-to-cart">
																																																																																																																																																																				<svg class="icon-cart" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
																																																																																																																																																																					width="24px" fill="#009703">
																																																																																																																																																																					<path
																																																																																																																																																																						d="M450-130v-220h60v80h320v60H510v80h-60Zm-320-80v-60h220v60H130Zm160-160v-80H130v-60h160v-80h60v220h-60Zm160-80v-60h380v60H450Zm160-160v-220h60v80h160v60H670v80h-60Zm-480-80v-60h380v60H130Z" />
																																																																																																																																																																				</svg>
																																																																																																																																																																			</a>







































					{/if}
																																																																																																																										</div>







































				{/if}




































			{/block}*}
		</div>
	</div>
{/block}