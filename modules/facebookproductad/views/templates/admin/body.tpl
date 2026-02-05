{*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}

<div id='{$sModuleName|escape:'htmlall':'UTF-8'}' class="bootstrap form">
	{* HEADER *}
	{include file="`$sHeaderInclude`"  bContentToDisplay=true}
	{* /HEADER *}
	{include file="`$sHeaderBar`"}
	{*Header bar include*}
	{* USE CASE - module update not ok  *}
	{if !empty($aUpdateErrors)}
		{include file="`$sErrorInclude`" aErrors=$aUpdateErrors bDebug=true}
		{* USE CASE - display configuration ok *}
	{else}
		{literal}
			<script type="text/javascript">
				var id_language = Number({/literal}{$iCurrentLang|intval}{literal});
				function hideOtherLanguage(id) {
					$('.translatable-field').hide();
					$('.lang-' + id).show();

					var id_old_language = id_language;
					id_language = id;
				}
			</script>
		{/literal}
		{if $number_of_feeds > 1}
			<div class="mt-3"></div>
			<div class="alert alert-info ">
				{l s='We have detected several languages and countries installed on your store. Good news! Our module is compatible with the use of Facebook country and language feeds. Just read our FAQ to know how to properly configure the module for their use.' mod='facebookproductad'}&nbsp;<a class="badge badge-info" target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/498"><i class="icon icon-link"></i>&nbsp;{l s='FAQ : How to use country and language feeds?' mod='facebookproductad'}</a>
			</div>
		{/if}

		<div id="{$sModuleName|escape:'htmlall':'UTF-8'}BlockTab">

			<div class="row">
				<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
					{*START LEFT MENU*}
					<div class="list-group workTabs">
						{*<a class="list-group-item active" id="tab-0"><span class="icon-home"></span>&nbsp;&nbsp;{l s='Welcome' mod='facebookproductad'}</a>*}
						<a class="list-group-item active" id="tab-5"><span class="fa fa-check-square"></span>&nbsp;&nbsp;{l s='Consent' mod='facebookproductad'}</a>
						<a class="list-group-item" id="tab-1"><span class="icon-code"></span>&nbsp;&nbsp;{l s='Facebook Pixel' mod='facebookproductad'}</a>
						<a class="list-group-item" id="tab-2"><span class="icon-heart"></span>&nbsp;&nbsp;{l s='Basic settings' mod='facebookproductad'}</a>
						{*start colapse*}
						<a class="list-group-item" id="tab-001" data-toggle="collapse" href="#collapseOne"><span class="icon-cog"></span>&nbsp;&nbsp;{l s='Feed management' mod='facebookproductad'}<span class="pull-right"><i class="icon-caret-down"></i></span> </a>
						<div id="collapseOne" class="panel-collapse collapse">
							<a class="list-group-item" id="tab-001"><i class="submenu fa fa-check-square"></i>&nbsp;{l s='Export method' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-003" href="#feed-management-dropdown3"><i class="submenu fa fa-feed"></i>&nbsp;{l s='Feed data options' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-002" href="#feed-management-dropdown2"><i class="submenu fa fa-ban"></i>&nbsp;{l s='Product exclusion rules' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-004" href="#feed-management-dropdown4"><i class="submenu fa fa-bookmark"></i>&nbsp;{l s='Apparel feed options' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-005" href="#feed-management-dropdown4"><i class="submenu fa fa-truck"></i>&nbsp;{l s='Shipping' mod='facebookproductad'}</a>
						</div>
						<a class="list-group-item" id="tab-010" data-toggle="collapse" href="#collapseTwo"><span class="fa fa-briefcase"></span>&nbsp;&nbsp;{l s='Advanced data feed options' mod='facebookproductad'}<span class="pull-right"><i class="icon-caret-down"></i></span> </a>
						<div id="collapseTwo" class="panel-collapse collapse">
							<a class="list-group-item" id="tab-010" href="#fb-management-dropdown1"><i class="submenu fa fa-copy"></i>&nbsp;{l s='Matching with Facebook categories' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-011" href="#fb-management-dropdown2"><i class="submenu fa fa-code"></i>&nbsp;{l s='Google Analytics integration' mod='facebookproductad'}</a>
							<a class="list-group-item" id="tab-012" href="#fb-management-dropdown3"><i class="submenu fa fa-bookmark-o"></i>&nbsp;{l s='Custom labels integration' mod='facebookproductad'}</a>
						</div>
						<a class="list-group-item" id="tab-3"><span class="icon-align-justify"></span>&nbsp;&nbsp;{l s='My feeds' mod='facebookproductad'}</a>
						<a class="list-group-item" id="tab-013" href="#feed-management-dropdown5"><i class="fa fa-plus"></i>&nbsp;{l s='Additional feed creation' mod='facebookproductad'}</a>
						<a class="list-group-item" id="tab-4"><span class="icon-play"></span>&nbsp;&nbsp;{l s='Reporting' mod='facebookproductad'}</a>
						<a class="list-group-item" id="tab-6"><span class="fa fa-stethoscope"></span>&nbsp;&nbsp;{l s='Conversion API log' mod='facebookproductad'}</a>
						<a class="list-group-item pointer" id="tab-7"><span class="icon-random"></span>&nbsp;&nbsp;{l s='Our other modules' mod='facebookproductad'}</a>
					</div>


					<div class="row">
						<div class="col-xs-12 text-center">
							{if $sCurrentLang == 'fr'}
								<a href="https://www.touchweb.fr/cybersecurite-prestashop/charte-cybersecurite-responsable" target="_blank">
									<img src="{$imagePath|escape:'htmlall':'UTF-8'}security/fr-secure.png" alt="Security badge" width="175" />
								</a>
							{else}
								<a href="https://www.touchweb.fr/cybersecurity-prestashop/responsible-cybersecurity-charter" target="_blank">
									<img src="{$imagePath|escape:'htmlall':'UTF-8'}security/en-secure.png" alt="Security badge" width="175" />
								</a>
							{/if}
						</div>
					</div>
				</div>
				{*END LEFT MENU*}
				<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
					{*STAR TAB CONTENT*}
					<div class="tab-content">
						{if empty($bHideConfiguration)}

							{* CONSTENT TAB *}
							<div id="content-tab-5" class="tab-pane panel active information">
								<div id="bt_consent-settings">
									{include file="`$sConsentInclude`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingConsentDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>
							{* /CONSTENT TAB *}

							{* PIXEL SETTINGS *}
							<div id="content-tab-1" class="tab-pane panel">
								<div id="bt_pixel-settings">
									{include file="`$sPixelInclude`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingPixelDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>
							{* /PIXEL SETTINGS *}

							{* BASICS SETTINGS *}
							<div id="content-tab-2" class="tab-pane panel">
								<div id="bt_basics-settings">
									{include file="`$sBasicsInclude`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingBasicsDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>
							{* /BASICS SETTINGS *}

							{* FEED MANAGEMENT SETTINGS *}
							<div id="content-tab-001" class="tab-pane panel">
								<div id="bt_feed-settings-export">
									{include file="`$sFeedInclude`" sDisplay="export"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-002" class="tab-pane panel">
								<div id="bt_feed-settings-exclusion">
									{include file="`$sFeedInclude`" sDisplay="exclusion"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-003" class="tab-pane panel">
								<div id="bt_feed-settings-data">
									{include file="`$sFeedInclude`" sDisplay="data"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-004" class="tab-pane panel">
								<div id="bt_feed-settings-apparel">
									{include file="`$sFeedInclude`" sDisplay="apparel"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-005" class="tab-pane panel">
								<div id="bt_feed-settings-tax">
									{include file="`$sFeedInclude`" sDisplay="tax"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-013" class="tab-pane panel">
								<div id="bt_feed-settings-add">
									{include file="`$sCustomFeed`"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="loadingFeedDiv" style="display: none;">
								<div class="alert alert-info">
									<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
									<div class="clr_20"></div>
									<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
								</div>
							</div>

							{literal}
								<script type="text/javascript">
									// run main feed JS
									oFpa.runMainFeed();
								</script>
							{/literal}
							{* /FEED MANAGEMENT SETTINGS *}

							{* FACEBOOK MANAGEMENT SETTINGS *}
							<div id="content-tab-010" class="tab-pane panel">
								<div id="bt_settings-categories">
									{include file="`$sFacebookInclude`" sDisplay="categories"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-011" class="tab-pane panel">
								<div id="bt_settings-analytics">
									{include file="`$sFacebookInclude`" sDisplay="analytics"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="content-tab-012" class="tab-pane panel">
								<div id="bt_settings-adwords">
									{include file="`$sFacebookInclude`" sDisplay="adwords"}
								</div>
								<div class="clr_20"></div>
							</div>

							<div id="loadingGoogleDiv" style="display: none;">
								<div class="alert alert-info">
									<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
									<div class="clr_20"></div>
									<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
								</div>
							</div>

							{literal}
								<script type="text/javascript">
									// run main Facebook JS
									oFpa.runMainGoogle();
								</script>
							{/literal}
							{* /FACEBOOK MANAGEMENT SETTINGS *}

							{* MY FEEDS SETTINGS *}
							<div id="content-tab-3" class="tab-pane panel">
								<div id="bt_feed-list-settings">
									{include file="`$sFeedListInclude`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingFeedListDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>
							{* /MY FEEDS SETTINGS *}

							{* REPORTING SETTINGS *}
							<div id="content-tab-4" class="tab-pane panel">
								<div id="bt_reporting-settings">
									{include file="`$sReportingInclude`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingReportingDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>
							{* /REPORTING SETTINGS *}

							{* LOG TOOLS *}
							<div id="content-tab-6" class="tab-pane panel">
								<div id="bt_log-settings">
									{include file="`$sApiLog`"}
								</div>
								<div class="clr_20"></div>
								<div id="loadingLogDiv" style="display: none;">
									<div class="alert alert-info">
										<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
										<div class="clr_20"></div>
										<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
									</div>
								</div>
							</div>

							<div id="content-tab-7" class="tab-pane panel">
								<div id="bt_cross-selling-settings">
									{include file="`$sCrossSelling`"}
								</div>
								<div class="clr_20"></div>
							</div>
							{* LOG TOOLS *}
						</div>

						<div class="footer">
							<div class="row">
								<div class="col-xs-12">
									<div class="col-xs-4">
										<ul class="unstyled">
											<li class="footer_title">{l s='Feed Configuration & Export' mod='facebookproductad'}
											<li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/132">{l s='How to configure my module ?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/265">{l s='How to create advanced exclusion rules?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/228">{l s='How to import my products into a Facebook catalog?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/177">{l s='How to automatically update my feeds?' mod='facebookproductad'}</a></li>
										</ul>
									</div>

									<div class="col-xs-4">
										<ul class="unstyled">
											<li class="footer_title">{l s='Facebook Catalog & Pixel' mod='facebookproductad'}
											<li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/227">{l s='How to create a Facebook product catalog?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/136">{l s='How to create and install my Facebook Pixel?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/137">{l s='How to test my Pixel code?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/492">{l s='What is the API conversions and how to enable it?' mod='facebookproductad'}</a></li>
										</ul>
									</div>

									<div class="col-xs-4">
										<ul class="unstyled">
											<li class="footer_title">{l s='Facebook & Instagram Shops' mod='facebookproductad'}
											<li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/240">{l s='How to add a Shop on my Facebook (and/or Instagram) Page?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/413">{l s='How to customize my Facebook (and/or Instagram) Shop?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/455">{l s='How do I get my Facebook Shop to appear on Instagram?' mod='facebookproductad'}</a></li>
											<li class="footer_link"><a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/231">{l s='How to activate the Instagram Shopping features?' mod='facebookproductad'}</a></li>
										</ul>
									</div>
								</div>
							</div>
							<div class="clr_50"></div>
						</div>

					</div>
				{else}
					<div class="clr_20"></div>

					{if !empty($bCurlAndContentStopExec)}
						<div class="alert alert-danger">
							{l s='You need to have either file_get_contents() with the allow_url_fopen directive enabled in the php.ini file, or have the PHP CURL extension enabled in order to retrieve the Facebook category definition files from Facebook\'s website. Please contact your web host. If neither of these options are available to you on your server (but at least one should be in most cases), you will not be able to use this module' mod='facebookproductad'}.
						</div>
					{/if}

					{if !empty($bMultishopGroupStopExec)}
						<div class="alert alert-danger">
							{l s='For performance reasons, this module cannot be configured within a shop group context. You must configure it one shop at a time' mod='facebookproductad'}.
						</div>
					{/if}

					<div class="clr_20"></div>
				{/if}

				{literal}
					<script type="text/javascript">
						oFpa.tabManagement();
						$(document).ready(function() {
							redirectTab = oFpa.getUrlParam('tab', 'empty');

							if (redirectTab != 'empty') {
								if (redirectTab == 'reporting') {
									$("#tab-4").trigger("click");
								} else if (redirectTab == 'adult') {
									$("#tab-001").trigger("click");
									$("#tab-003").trigger("click");
								} else if (redirectTab == 'appreal') {
									$("#tab-001").trigger("click");
									$("#tab-004").trigger("click");
								} else if (redirectTab == 'taxonomies') {
									$("#tab-001").trigger("click");
									$("#tab-010").trigger("click");
								} else if (redirectTab == 'feeds') {
									$("#tab-3").trigger("click");
								}
							}
						});
					</script>
				{/literal}
			{/if}
</div>