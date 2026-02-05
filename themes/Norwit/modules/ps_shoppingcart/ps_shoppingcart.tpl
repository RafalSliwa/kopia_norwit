{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{*<div class="overlay"></div>*}
<div id="desktop_cart">
	<div class="blockcart" data-refresh-url="{$refresh_url}">
		<div class="header blockcart-header">

			{if $cart.products_count > 0}
				<a href="{$cart_url}" class="shopping-cart" rel="nofollow">
					<span class="icon"> </span>
				{/if}
				<div class="shopping-cart">
					{*<span class="hidden-sm-down cart-headding">{l s='Shopping Cart' d='Shop.Theme.Global'}</span>*}
					<span class="icon"> </span>
					<span class="mobile_count">{$cart.products_count}</span>
					<span class="cart-products-count hidden-sm-down">
						{if $cart.products_count > 0}
							{l s='My Cart' d='Shop.Theme.Global'}
						{else}
							{l s='My Cart' d='Shop.Theme.Global'}
						{/if}
						{*<span class="value"> {$cart.totals.total.value}</span>*}
					</span>
				</div>
				{if $cart.products_count > 0}
				</a>
			{/if}
			<div class="cart_block block exclusive">
				<div class="top-block-cart">
					<div class="toggle-title">{l s='Shopping Cart' d='Shop.Theme.Global'} ({$cart.products_count})
					</div>
					<div class="close-icon">{l s='close' d='Shop.Theme.Global'}</div>
				</div>
				{if $cart.products_count > 0}
					<div class="block_content">
						<div class="cart_block_list">
							{foreach from=$cart.products item=product}
								<div class="cart-item">
									<div class="cart-image">
										{if $product.cover}
											<a href="{$product.url}">
												<img class="lazyload" src="{$urls.img_url}codezeel/image_loading.svg"
													data-src="{$product.cover.bySize.cart_default.url}"
													alt="{$product.name|escape:'quotes'}"
													width="{$product.cover.bySize.cart_default.width}"
													height="{$product.cover.bySize.cart_default.height}">
											</a>
										{else}
											<a href="{$product.url}" class="thumbnail product-thumbnail">
												<img class="lazyload" src="{$urls.img_url}codezeel/image_loading.svg"
													data-src="{$urls.no_picture_image.bySize.cart_default.url}"
													width="{$urls.no_picture_image.bySize.cart_default.width}"
													height="{$urls.no_picture_image.bySize.cart_default.height}">
											</a>
										{/if}
									</div>

									<div class="cart-info">
										<span class="product-name"><a href="{$product.url}">
												{$product.name|truncate:50:'...'}</a></span>
										<div>
											<span class="product-quantity">{$product.quantity} x</span>
											<span class="product-price"> {$product.price}</span>
										</div>
										<a class="remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}"
											data-link-action="delete-from-cart"
											data-id-product="{$product.id_product|escape:'javascript'}"
											data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
											data-id-customization="{$product.id_customization|escape:'javascript'}">
											<i class="material-icons pull-xs-left">delete</i>
										</a>
										{if $product.customizations|count}
											<div class="customizations">
												<ul>
													{foreach from=$product.customizations item='customization'}
														<li>
															<span class="product-quantity">{$customization.quantity}</span>
															<a href="{$customization.remove_from_cart_url}"
																title="{l s='remove from cart' d='Shop.Theme.Actions'}"
																class="remove-from-cart" rel="nofollow"></a>
															<ul>
																{foreach from=$customization.fields item='field'}
																	<li>
																		<span>{$field.label}</span>
																		{if $field.type == 'text'}
																			<span>{$field.text}</span>
																		{else if $field.type == 'image'}
																			<img src="{$field.image.small.url}">
																		{/if}
																	</li>
																{/foreach}
															</ul>
														</li>
													{/foreach}
												</ul>
											</div>
										{/if}
									</div>
								</div>
							{/foreach}
						</div>
					</div>

					<div class="card cart-summary">
						<div class="card-block">
							{foreach from=$cart.subtotals item="subtotal"}
								{if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
									<div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
										<span class="label{if 'products' === $subtotal.type} js-subtotal{/if}">
											{if 'products' == $subtotal.type}
												{$cart.summary_string}
											{else}
												{$subtotal.label}
											{/if}
										</span>
										<span class="value">
											{if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
										</span>
										{if $subtotal.type === 'shipping'}
											<div><small
													class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small>
											</div>
										{/if}
									</div>
								{/if}
							{/foreach}
						</div>
						<div class="card-block">
							{block name='cart_summary_total'}
								{if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
									<div class="cart-summary-line">
										<span class="label">{$cart.totals.total.label}&nbsp;{$cart.labels.tax_short}</span>
										<span class="value">{$cart.totals.total.value}</span>
									</div>
									<div class="cart-summary-line cart-total">
										<span class="label">{$cart.totals.total_including_tax.label}</span>
										<span class="value">{$cart.totals.total_including_tax.value}</span>
									</div>
								{else}
									<div class="cart-summary-line cart-total">
										<span
											class="label">{$cart.totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}</span>
										<span class="value">{$cart.totals.total.value}</span>
									</div>
								{/if}
							{/block}
							{block name='cart_summary_tax'}
								{if $cart.subtotals.tax}
									<div class="cart-summary-line">
										<span
											class="label sub">{l s='%label%:' sprintf=['%label%' => $cart.subtotals.tax.label] d='Shop.Theme.Global'}</span>
										<span class="value sub">{$cart.subtotals.tax.value}</span>
									</div>
								{/if}
							{/block}
						</div>
					</div>

					<div class="checkout card-block">
						<a rel="nofollow" href="{$cart_url}" class="viewcart">
							<button type="button" class="btn btn-primary">{l s='View Cart' d='Shop.Theme.Actions'}</button>
						</a>
						<a rel="nofollow" href="{$urls.pages.order_login}" class="checkout"><button type="button"
								class="btn btn-primary checkout_button">{l s='CheckOut' d='Shop.Theme.Global'}</button></a>
					</div>

				{else}
					<div class="block_content">
						<div class="no-more-item">
							<div class="no-img"></div>
							<div class="empty-text">{l s='There are no more items in your cart' d='Shop.Theme.Global'}
							</div>
							<a rel="nofollow" href="{$urls.pages.index}" class="continue"><button type="button"
									class="btn btn-secondary btn-primary">{l s='Continue shopping' d='Shop.Theme.Global'}</button></a>
						</div>
					</div>
				{/if}
			</div>

		</div>
	</div>
</div>