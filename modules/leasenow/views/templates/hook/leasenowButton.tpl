{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    ING Lease Now
 *  @copyright 2022-now ING Lease Now
 *  @license   GNU General Public License
 */
*}

<style>
	@font-face {
		font-family:"ING Me";
		font-style:normal;
		font-weight:300;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:italic;
		font-weight:300;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:normal;
		font-weight:400;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:italic;
		font-weight:400;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Italic.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:normal;
		font-weight:600;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:italic;
		font-weight:600;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Regular.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-BoldItalic.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-BoldItalic.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:normal;
		font-weight:700;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-Bold.ttf") format("truetype");
	}

	@font-face {
		font-family:"ING Me";
		font-style:italic;
		font-weight:700;
		src:url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-BoldItalic.woff2") format("woff2"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-BoldItalic.woff") format("woff"), url("{$urls.base_url}/modules/leasenow/views/fonts/INGMeWeb-BoldItalic.ttf") format("truetype");
	}

	.leasenow_button {

		width:100%;

		height:auto !important;
		border:none;
		padding:0;

		position:relative;
		background-color:rgba(0, 0, 0, 0);
		margin:0 !important;
		font-family:"ING Me", serif;

	}

	.leasenow_button-image {
		width:100% !important;
		height:auto !important;
	}

	.leasenow_button-content {
		margin-top:20px;
		position:relative;
		height:auto !important;
		width:{if isset($leasenow_button_scale) && $leasenow_button_scale}{$leasenow_button_scale|escape:'htmlall':'UTF-8'}{else}100{/if}% !important;
	}

	.leasenow_button:hover {
		background-color:rgba(0, 0, 0, 0);
	}

	.leasenow_button-overlay {
		bottom:0;
		display:flex;
		align-items:center;
		justify-content:center;
		left:0;
		right:0;
		top:0;
		position:absolute;
	}

	.leasenow_loading_gif-center {
		top:0;
		bottom:0;
		left:0;
		right:0;
		margin:5px auto auto;
	}

	.leasenow_d--none {
		display:none;
	}

	.leasenow_v--none {
		visibility:hidden;
	}

	.leasenow_tooltip span {
		white-space:normal;
		margin-bottom:12px;
		font-weight:normal;
		line-height:normal;
		z-index:10;display:none;
		padding:6px 8px;
		box-shadow:0px 0px 0px 1px rgba(25, 25, 43, 0.04);
		filter:drop-shadow(0px 9px 24px rgba(25, 25, 43, 0.09)) drop-shadow(0px 3px 6px rgba(25, 25, 43, 0.06));
	}

	.leasenow_tooltip:hover span {
		width:100%;
		display:block;
		position:absolute;
		bottom:100%;
		left:0;
		color:#373535;
		background:#fffFff;
	}

	.leasenow_button-content-div {
		position:relative;width:100%;display:inline-block;
	}

	.leasenow_button:focus, .leasenow_button:active:focus, .leasenow_button:active, .leasenow_button:target {
		outline:none;
	}
</style>

<div class="leasenow_button-content {if !$leasenow_display}leasenow_d--none{/if}">

	<div class="leasenow_button-content-div">
        {if isset($leasenow_code) && $leasenow_code}
			<div class="leasenow_d--none">
				ln__{$leasenow_code|escape:'htmlall':'UTF-8'}
			</div>
        {/if}

		<button type="button" class="btn leasenow_tooltip leasenow_button" {if $leasenow_availability && (isset($leasenow_redirect_url) && $leasenow_redirect_url)}onclick="window.open('{$leasenow_redirect_url}', '_blank')"{/if}>
			<img alt="ING Lease Now" class="leasenow_button-image" src="{if $leasenow_image_url}{$leasenow_image_url|escape:'htmlall':'UTF-8'}{/if}">
            {if (isset($leasenow_tooltip_display) && $leasenow_tooltip_display) && (isset($leasenow_missing_amount) && $leasenow_missing_amount)}
				<span id="leasenow_toooltip_message">{l s='Only ' mod='leasenow'}<strong>{$leasenow_missing_amount}</strong>{l s=' is missing to take advantage of the lease. Add more items to your cart and ' mod='leasenow'}<strong style="color:#ff6200">{l s='Lease with ING.' mod='leasenow'}</strong></span>
            {/if}
		</button>

		<div class="leasenow_button-overlay leasenow_d--none">
			<div class="leasenow_button-overlay-content">
				<img class="leasenow_loading_gif-center" src="{$leasenow_loading_gif|escape:'htmlall':'UTF-8'}"
				     alt="Loading..."/>
			</div>
		</div>

	</div>

	<script type="text/javascript">

		window.onload = function () {

			var $leasenow_button_overlay = $('.leasenow_button-overlay'),
				$leasenow_button_content = $('.leasenow_button-content'),
				$leasenow_button_image = $('.leasenow_button-image'),
				$leasenow_button_message = $('#leasenow_button-message'),
				$leasenow_button = $('.leasenow_button'),
				leasenow_class_d_none = 'leasenow_d--none',
				leasenow_v_none = 'leasenow_v--none',
				leasenow_tooltip_span = '<span id="leasenow_toooltip_message"></span>',
				isProcessing = false,
				cartId = null;

			var availabilityUrl = "{$leasenow_availability_controller nofilter}";

            {if $leasenow_cart_id}
			cartId = {$leasenow_cart_id};
            {/if}

            {if $is_ps_17}

			// region listeners
			if (typeof prestashop === 'undefined'
				|| !cartId) {
				return;
			}

			prestashop.on(
				'updateCart',
				function () {
					updateButtonCart()
				}
			);

			prestashop.on(
				'updateProduct',
				function () {
					$('.leasenow_button-overlay').removeClass(leasenow_class_d_none);
					$('.leasenow_button').addClass(leasenow_v_none);
				}
			);

			// endregion

			function updateButtonCart() {

				$leasenow_button_content.removeClass(leasenow_class_d_none);
				$('.leasenow_button-overlay').removeClass(leasenow_class_d_none);

				availabilityCall(availabilityUrl, {
					idCart: cartId,
					action: 'cart'
				})
			}

            {else}

			function process(simple = false) {

				if (isProcessing) {
					return '';
				}

				var idProduct = $("input[name='id_product']").val(),
					quantity = $("input[name='qty']").val()

				if (!idProduct) {
					return;
				}

				if (simple) {
					availabilityCall(availabilityUrl, {
						idProduct: idProduct,
						quantity:  quantity,
						action:    'simple'
					});
					return;
				}

				var idAttribute = $("input[name='id_product_attribute']").val();

				if (!idAttribute) {
					return;
				}

				availabilityCall(availabilityUrl, {
					idAttribute: idAttribute,
					idProduct:   idProduct,
					quantity:    quantity,
					action:      'attribute'
				})
			}

			var idAttribute = $("input[name='id_product_attribute']").val();

			if (idAttribute) {
				process();
			} else {
				$('#quantity_wanted').on('change', function (event) {

					process(true);

				});
			}

			$('#our_price_display').on('change', function (event) {
				process();

			});

            {/if}

			function leasenow_process_request() {

				var $tooltipMessage = $('#leasenow_toooltip_message');

				if ($tooltipMessage) {
					$tooltipMessage.remove();
				}

				$leasenow_button.removeAttr('onclick');
				$leasenow_button.addClass(leasenow_v_none);
				// $leasenow_button_overlay.removeClass(leasenow_class_d_none);
				$leasenow_button_content.removeClass(leasenow_v_none);
				$leasenow_button_message.html('');
				isProcessing = true;
			}

			function availabilityCall(url, body) {
				jQuery.ajax({
					type:       "POST",
					dataType:   "json",
					url:        url,
					data:       body,
					beforeSend: function () {
						leasenow_process_request();
					},
					success:    function (response) {

						if (!response) {
							$leasenow_button_content.addClass(leasenow_v_none);
							return '';
						}

						if (!response.success
							|| !(response.data.leasenow_redirect_url || response.data.leasenow_missing_amount)
							|| !response.data?.leasenow_image_url
							|| !response.data?.leasenow_button_scale) {
							$leasenow_button_content.addClass(leasenow_class_d_none);

							if (response.data?.body?.error?.code) {
								$leasenow_button_content.append('<div class="leasenow_d--none">leasenow__' + response.data.body.error.code + '</div>');
							}
							return ''
						}

						if (response.data.leasenow_redirect_url) {
							$leasenow_button.attr('onclick', "window.open('" + response.data.leasenow_redirect_url + "', '_blank')")
						}

						if (response.data.leasenow_missing_amount) {

							$leasenow_button.append(leasenow_tooltip_span);
							var temp = $('#leasenow_toooltip_message');
							temp.append("{l s='Only ' mod='leasenow'}<strong>"
								+ response.data.leasenow_missing_amount
								+ "</strong>{l s=' is missing to take advantage of the lease. Add more items to your cart and ' mod='leasenow'}<strong style='color:#ff6200'>{l s='Lease with ING.' mod='leasenow'}</strong>");
							temp.attr("style", "width: " + response.data.leasenow_image_scale + "% !important");
						}

						$leasenow_button.removeClass(leasenow_v_none);
						$leasenow_button_overlay.addClass(leasenow_class_d_none);
						$leasenow_button_image.attr('src', response.data.leasenow_image_url);
					},
					error:      function () {
						$leasenow_button_content.addClass(leasenow_class_d_none);
					},
					complete:   function () {
						isProcessing = false;
					}
				});
			}
		}
	</script>
</div>
