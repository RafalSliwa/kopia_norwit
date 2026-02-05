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


{*<div class="nav_top">
	{hook h='displayNavTop'}
</div>*}

{*{block name='header_nav'}
	<nav class="header-nav">
		<div class="container">
			<div class="hidden-sm-down">
	<div class="left-nav">
		{hook h='displayNav1'}
	</div>

	<div class="right-nav">
		{hook h='displayNav2'}
		{hook h='displayStWishlistTopLink'}
		{hook h='displayStCompareTopLink'}
	</div>
	</div>

<div class="hidden-md-up text-xs-center mobile">
			<div class="pull-xs-left" id="menu-icon">
				<i class="material-icons menu-open">&#xE5D2;</i>
				<i class="material-icons menu-close">&#xE5CD;</i>			  
			</div>
			<div class="pull-xs-right" id="_mobile_cart"></div>
			<div class="pull-xs-right" id="_mobile_user_info"></div>
			<div class="top-logo" id="_mobile_logo"></div>
			<div class="clearfix"></div>
		</div> 
		</div>
	</nav>
{/block}*}
{block name='header_banner'}
	<div class="header-info">
		<div class="own-warehouse">
			<span>{l s='OWN <p>WAREHOUSE</p>' d='Shop.Theme.Catalog'}</span>
		</div>

		<span class="separator">|</span>

		<div class="support">
			<span>{l s='24/7 <p>SUPPORT</p>' d='Shop.Theme.Catalog'}</span>
		</div>

		<span class="separator">|</span>

		<div class="returns">
			<span>{l s='RETURNS UP TO <p>14 DAYS</p>' d='Shop.Theme.Catalog'}</span>
		</div>

		<span class="separator">|</span>

		<div class="market">
			<span>{l s='23 YEARS <p>ON THE MARKET</p>' d='Shop.Theme.Catalog'}</span>
		</div>

		<span class="separator">|</span>

		<div class="leasing">
			<span>{l s='LEASING' d='Shop.Theme.Catalog'}</span>
		</div>
		<span class="separator">|</span>

		<div class="right-nav">
			{hook h='displayNav2'}
		</div>
	</div>
	<div class="header-info mobile">
		<img class="lazyload" src="{$urls.img_url}codezeel/mobile_top.png">
	</div>
{/block}
{block name='header_top'}
	<div class="header-top">
		<div class="container">
			<div class="row">
				<div class="header_logo">

					{if $page.page_name == 'index'}
						<a href="{$urls.pages.index}">
							<img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}" loading="lazy">
						</a>
					{else}
						<a href="{$urls.pages.index}">
							<img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}" loading="lazy">
						</a>
					{/if}

				</div>
				<div class="mobile_logo" id="mobile_logo">
					<a href="{$urls.pages.index}">
						<img class="logo img-responsive" src="{$urls.img_url}codezeel/logo_mobile.png" width="40"
							height="40" alt="{$shop.name}" loading="lazy">
					</a>
				</div>

				<div id="call-us">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
						<path
							d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z" />
					</svg>
					<a class="call-us" href="tel:+48 +48 732 081 091">
						+48 732 081 091
					</a>
				</div>

				{hook h='displayTop'}

				<div id="promotion">
					<a class="promotion" href="/c/wyprzedaze" title="SALE">
						<svg viewBox="0 0 36 37" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M16.1824 9.696C16.1824 11.008 15.9584 12.192 15.5104 13.248C15.0784 14.304 14.4944 15.208 13.7584 15.96C13.0224 16.696 12.1664 17.264 11.1904 17.664C10.2144 18.064 9.19844 18.264 8.14244 18.264C6.99044 18.264 5.92644 18.064 4.95044 17.664C3.97444 17.264 3.12644 16.696 2.40644 15.96C1.70244 15.208 1.15044 14.304 0.750439 13.248C0.350439 12.192 0.150439 11.008 0.150439 9.696C0.150439 8.352 0.350439 7.136 0.750439 6.048C1.15044 4.96 1.70244 4.04 2.40644 3.288C3.12644 2.536 3.97444 1.96 4.95044 1.56C5.92644 1.144 6.99044 0.935998 8.14244 0.935998C9.29444 0.935998 10.3584 1.144 11.3344 1.56C12.3264 1.96 13.1824 2.536 13.9024 3.288C14.6224 4.04 15.1824 4.96 15.5824 6.048C15.9824 7.136 16.1824 8.352 16.1824 9.696ZM11.6224 9.696C11.6224 8.768 11.5264 7.992 11.3344 7.368C11.1584 6.744 10.9104 6.24 10.5904 5.856C10.2864 5.472 9.91844 5.2 9.48644 5.04C9.07044 4.864 8.62244 4.776 8.14244 4.776C7.66244 4.776 7.21444 4.864 6.79844 5.04C6.38244 5.2 6.02244 5.472 5.71844 5.856C5.43044 6.24 5.19844 6.744 5.02244 7.368C4.84644 7.992 4.75844 8.768 4.75844 9.696C4.75844 10.592 4.84644 11.344 5.02244 11.952C5.19844 12.544 5.43044 13.024 5.71844 13.392C6.02244 13.76 6.38244 14.024 6.79844 14.184C7.21444 14.344 7.66244 14.424 8.14244 14.424C8.62244 14.424 9.07044 14.344 9.48644 14.184C9.91844 14.024 10.2864 13.76 10.5904 13.392C10.9104 13.024 11.1584 12.544 11.3344 11.952C11.5264 11.344 11.6224 10.592 11.6224 9.696ZM27.7264 2.232C27.9344 1.992 28.1744 1.776 28.4464 1.584C28.7344 1.392 29.1344 1.296 29.6464 1.296H33.9664L8.19044 35.112C7.98244 35.368 7.73444 35.584 7.44644 35.76C7.15844 35.92 6.80644 36 6.39044 36H1.97444L27.7264 2.232ZM35.7904 27.84C35.7904 29.152 35.5664 30.336 35.1184 31.392C34.6864 32.448 34.1024 33.352 33.3664 34.104C32.6304 34.84 31.7744 35.416 30.7984 35.832C29.8224 36.232 28.8064 36.432 27.7504 36.432C26.5984 36.432 25.5344 36.232 24.5584 35.832C23.5824 35.416 22.7344 34.84 22.0144 34.104C21.3104 33.352 20.7584 32.448 20.3584 31.392C19.9584 30.336 19.7584 29.152 19.7584 27.84C19.7584 26.496 19.9584 25.28 20.3584 24.192C20.7584 23.104 21.3104 22.184 22.0144 21.432C22.7344 20.68 23.5824 20.104 24.5584 19.704C25.5344 19.288 26.5984 19.08 27.7504 19.08C28.9024 19.08 29.9664 19.288 30.9424 19.704C31.9344 20.104 32.7824 20.68 33.4864 21.432C34.2064 22.184 34.7664 23.104 35.1664 24.192C35.5824 25.28 35.7904 26.496 35.7904 27.84ZM31.2064 27.84C31.2064 26.912 31.1184 26.136 30.9424 25.512C30.7664 24.888 30.5184 24.384 30.1984 24C29.8944 23.616 29.5264 23.344 29.0944 23.184C28.6784 23.008 28.2304 22.92 27.7504 22.92C27.2704 22.92 26.8224 23.008 26.4064 23.184C25.9904 23.344 25.6304 23.616 25.3264 24C25.0224 24.384 24.7824 24.888 24.6064 25.512C24.4464 26.136 24.3664 26.912 24.3664 27.84C24.3664 28.736 24.4464 29.488 24.6064 30.096C24.7824 30.704 25.0224 31.192 25.3264 31.56C25.6304 31.928 25.9904 32.192 26.4064 32.352C26.8224 32.512 27.2704 32.592 27.7504 32.592C28.2304 32.592 28.6784 32.512 29.0944 32.352C29.5264 32.192 29.8944 31.928 30.1984 31.56C30.5184 31.192 30.7664 30.704 30.9424 30.096C31.1184 29.488 31.2064 28.736 31.2064 27.84Z"
								fill="#FFC009" />
						</svg>
						<span>{l s='SALE' d='Shop.Theme.Catalog'}</span>
					</a>
				</div>

				<div id="contact">
					<div class="dropdown show">
						<a class="contact dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<svg id="Warstwa_2" data-name="Warstwa 2" xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 266.74 272.43">
								<defs>
									<style>
										.cls-1 {
											fill: none;
											stroke: #1d1d1a;
											stroke-linecap: round;
											stroke-linejoin: round;
											stroke-width: 7px;
										}

										.cls-2 {
											fill: #ffc009;
										}
									</style>
								</defs>
								<g id="Warstwa_1-2" data-name="Warstwa 1">
									<g id="Layer_2" data-name="Layer 2">
										<path class="cls-2"
											d="M7.92,96.7v-26.4C7.92,31.47,39.39,0,78.22,0h91.4c38.83,0,70.3,31.47,70.3,70.3v26.4c0,27.8-26.9,28.62-26.9,28.62,0,0-6.19-10.09-10.79-10.38s-16.88,9-16.88,9h-91.02s-8.22-14.72-23.8-15.48c-20.11-.98-34.04,12.24-34.04,12.24v30.8S7.92,120.96,7.92,96.7Z" />
										<path class="cls-2"
											d="M177.6,68V0h51.4c14.14,0,25.6,11.46,25.6,25.6v6c0,20.1-16.3,36.4-36.4,36.4h-40.6Z" />
										<path class="cls-2"
											d="M211.29,119.08c-10-10.98-25.55,5.18-25.55,5.18v-38.31h39.6c22.86,0,41.4,18.53,41.4,41.4v1.21c0,22.86-17.53,34.24-40.4,34.24l-11.85-1.34s5.38-32.96-3.2-42.38Z" />
										<path class="cls-2"
											d="M248.08,52.38c-6.26,7.6-8.16,17.67-8.16,24.74,0,2.9-.07,9.28,8.82,16.08-6.01-.06-11.65,4.19-17.62,4.85-.93-12.27-.54-24.63,1.15-36.81.47-3.34,1.03-6.74.51-10.08" />
										<path class="cls-1"
											d="M214.5,131.18v67.75c0,38.66-31.34,70-70,70h-38c-38.66,0-70-31.34-70-70v-75" />
										<line class="cls-1" x1="94.34" y1="123.93" x2="185.35" y2="123.93" />
										<path class="cls-1" d="M36.5,123.18c2.59-19.62,55.58-19.89,57.84.75" />
										<line class="cls-1" x1="70.54" y1="99.14" x2="88.87" y2="114.89" />
										<path class="cls-1"
											d="M185.74,123.93l6.71-5.87c2.36-2.07,5.41-3.19,8.55-3.14l1.23.02c4.5.06,7.36,1.69,10.09,5.45,0,0,2.18,2.55,2.18,10.79" />
										<path class="cls-1" d="M159.27,92.17c28.87-25.06,47.89,7.18,40.05,22.72" />
										<path class="cls-1"
											d="M214.5,201.29v-38.5h13.75c10.63,0,19.25,8.62,19.25,19.25h0c0,10.63-8.62,19.25-19.25,19.25h-13.75Z" />
										<path class="cls-1"
											d="M36.5,201.29v-38.5h-13.75c-10.63,0-19.25,8.62-19.25,19.25h0c0,10.63,8.62,19.25,19.25,19.25h13.75Z" />
										<path class="cls-1" d="M91.99,224.3c14.15,17.78,48.38,18.37,67.28-.52" />
										<path class="cls-1" d="M142.26,174.03c10.18-21.43,44.76-20.37,51.05,1.44" />
										<path class="cls-1" d="M59.26,174.03c10.18-21.43,44.76-20.37,51.05,1.44" />
									</g>
								</g>
							</svg>
							<span>{l s='CONTACT' d='Shop.Theme.Catalog'}</span>
						</a>

						<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
							<a class="dropdown-item"
								href="/content/serwis-i-czesci-zamienne"><span>{l s='Service | Parts' d='Shop.Theme.Catalog'}</span></a>
							<a class="dropdown-item"
								href="/content/zwroty-i-reklamacje"><span>{l s='Returns | Complaints' d='Shop.Theme.Catalog'}</span></a>
							<a class="dropdown-item"
								href="/content/praca-w-norwitpl"><span>{l s='Work at NORWIT.PL' d='Shop.Theme.Catalog'}</span></a>
							<a class="dropdown-item"
								href="{$urls.pages.contact}"><span>{l s='Contact us' d='Shop.Theme.Catalog'}</span></a>
							<div id="contact-us">
								<div class="contact-us">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
										<path
											d="M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z" />
									</svg>
									<a class="call-us" href="tel:+48 +48 732 081 091">+48 732 081 091</a>
								</div>
								<span class="info">
									<span class="day">pon. - nd. 7:00 - 21:00</span>
								</span>

								<a class="mail" href="mailto:sprzedaz@norwit.pl">
									<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
										width="24px" fill="#e8eaed">
										<path
											d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z">
										</path>
									</svg>
									<span>sprzedaz@norwit.pl</span>
								</a>
							</div>
						</div>
					</div>
				</div>
				{hook h='displayNav2'}
			</div>
			{*<div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
			<div class="js-top-menu mobile" id="_mobile_top_menu"></div>
			<div class="js-top-menu-bottom">
				<div id="_mobile_currency_selector"></div>
				<div id="_mobile_language_selector"></div>
				<div id="_mobile_contact_link"></div>
			</div>
		</div>*}
		</div>
	</div>
{/block}
{*<div class="header-top-inner">
	<div class="container">
		{hook h='displayNavFullWidth'}
	</div>
</div>*}
{hook h='displayMegaMenu'}