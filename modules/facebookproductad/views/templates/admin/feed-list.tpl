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

<div class="bootstrap">
	{if !empty($sFpaLink)}
		{if !empty($iTotalProductToExport)}
			{literal}
				<script type="text/javascript">
					var aDataFeedGenOptions = {
						'sURI' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
						'sParams' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.dataFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.dataFeed.type|escape:'htmlall':'UTF-8'}{literal}',
						'iShopId' : {/literal}{$iShopId|intval}{literal},
						'sFilename': '',
						'iLangId': 0,
						'sLangIso': '',
						'sCountryIso': '',
						'sCurrencyIso': '',
						'iStep': 0,
						'iTotal' : {/literal}{$iTotalProductToExport|intval}{literal},
						'iProcess': 0,
						'sDisplayedCounter': '#regen_counter',
						'sDisplayedBlock': '#syncCounterDiv',
						'sDisplaySuccess': '#regen_xml',
						'sDisplayTotal': '#total_product_processed',
						'sLoaderBar': 'myBar',
						'sErrorContainer': 'AjaxFeed',
						'bReporting': 1,
						'sDisplayReporting': '',
						'sResultText' : '{/literal}{l s='product(s) exported' mod='facebookproductad'}{literal}',
						'bExcludedProduct' : {/literal}{$bExcludedProduct|escape:'htmlall':'UTF-8'}{literal}
					};
				</script>
			{/literal}
			<h3 class="breadcrumb"><i class="icon-th-list"></i>&nbsp;{l s='Your export files' mod='facebookproductad'}</h3>
			{if !empty($exportMode)}
				<input type="text" id="export_mode" value="{$exportMode|escape:'htmlall':'UTF-8'}" />
			{/if}
			{* USE CASE - AVAILABLE FEED FILE LIST *}
			{if !empty($aFeedFileList)}

				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-info">
							{l s='In this tab you will find the URLs of your product data feeds to be entered into your Facebook Business Manager account, depending on the retrieval method you choose. To know how to import your products into your Facebook Business Manager account, don\'t hesitate to read' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/228" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='our FAQ' mod='facebookproductad'}</a>
							<br />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="jumbotron">
							{if $iTotalProduct <= 30000}
								<span class="badge badge-pill badge-warning float-left p-2 mr-5">{l s='Recommended' mod='facebookproductad'}</span>
							{/if}
							<h1 class="display-4">{l s='ON THE FLY OUTPUT' mod='facebookproductad'}</h1>
							<p class="lead w-75">{l s='This export method is recommended for catalogs with less than about 30000 products' mod='facebookproductad'}</p>
							<p class="lead text-center">
								<a id="btn-fly" class="btn btn-lg btn-primary w-25 mt-2 py-3">
									{l s='Use this solution' mod='facebookproductad'}
								</a>
							</p>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<div class="jumbotron">
							{if $iTotalProduct > 30000}
								<span class="badge badge-pill badge-warning float-left p-2 mr-5">{l s='Recommended' mod='facebookproductad'}</span>
							{/if}
							<h1 class="display-4">{l s='XML + CRON' mod='facebookproductad'}</h1>
							<p class="lead w-75">{l s='This export method is recommended for catalogs with more than about 30000 products' mod='facebookproductad'}</p>
							<p class="lead text-center">
								<a id="btn-xml" class="btn btn-lg w-25 btn-primary mt-2 py-3">
									{l s='Use this solution' mod='facebookproductad'}
								</a>

							</p>
						</div>
					</div>
				</div>

				<div class="bt-fb-cron" style="display: none;">

					<h1 class="display-4">{l s='XML + CRON' mod='facebookproductad'}</h1>
					<hr />

					<ul class="nav nav-tabs nav-tabs-middle mt-2" id="myTab">
						<li class="active">
							<a data-toggle="tab" href="#xml"><i class="fa fa-file-code-o"></i>&nbsp;{l s='Your XML files' mod='facebookproductad'}</a>
						</li>
						<li class="nav-item">
							<a data-toggle="tab" href="#cron"><i class="fa fa-server"></i>&nbsp;{l s='Your CRON URL\'s' mod='facebookproductad'}</a>
						</li>
					</ul>

					<div class="tab-content" id="myTabContent">
						<div class="tab-pane active" id="xml">
							<form class="form-horizontal col-xs-12" method="post" id="bt_feedlist-form" name="bt_feedlist-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_feedlist-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-list-settings', 'bt_feed-list-settings', false, false, null, 'FeedList', 'loadingFeedListDiv');return false;" {/if}>
								<input type="hidden" name="sAction" value="{$aQueryParams.feedListUpdate.action|escape:'htmlall':'UTF-8'}" />
								<input type="hidden" name="sType" value="{$aQueryParams.feedListUpdate.type|escape:'htmlall':'UTF-8'}" />
								<div class="clr_50"></div>
								<div id="syncCounterDiv" style="display: none;" class="alert alert-success">
									<button type="button" class="close" onclick="$('#syncCounterDiv').hide();">×</button>
									<div class="row mb-3 h3">
										{l s='Export in progress' mod='facebookproductad'}
									</div>
									<hr />
									<div class="row">
										<b>{l s='Number of products exported:' mod='facebookproductad'}</b>&nbsp;
										<input size="5" name="bt_regen-counter" id="regen_counter" value="0" disabled />&nbsp;
										{l s='out of' mod='facebookproductad'}&nbsp;{$iTotalProduct|intval} ({l s='(total number of products in the shop)' mod='facebookproductad'})
									</div>
									<div class="row mt-2">
										<div class="mt-2"></div>
										<div class="progress col-xs-12" style="height: 20px;">
											<div class="progress-bar bg-success progress-bar-striped active" id="myBar"></div>
										</div>
									</div>
									<div class="row">
										<div id="{$sModuleName|escape:'htmlall':'UTF-8'}AjaxFeedError"></div>
									</div>
									<div class="clr_20"></div>
								</div>

								<table cellpadding="2" cellspacing="2" class="table">
									<tr class="bt_tr_header text-center">
										{* <th class="center col-xs-1">{l s='Regenerate during CRON' mod='facebookproductad'}</th> *}
										<th class="center">{l s='Language' mod='facebookproductad'}</th>
										<th class="center">{l s='Country' mod='facebookproductad'}</th>
										<th class="center">{l s='Currency' mod='facebookproductad'}</th>
										<th class="center">{l s='Taxonomy' mod='facebookproductad'}</th>
										<th class="center">{l s='Last update' mod='facebookproductad'}</th>
										<th class="center">{l s='Action' mod='facebookproductad'}</th>
									</tr>
									{foreach from=$aFeedFileList name=feed key=iKey item=aFeed}
										<tr id="regen_xml_{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|lower|escape:'htmlall':'UTF-8'}">
											{* <td class="center"><input type="checkbox" class="bt_export_feed" name="bt_cron-export[]" value="{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|escape:'htmlall':'UTF-8'}_{$aFeed.currencyIso|escape:'htmlall':'UTF-8'}" {if !empty($aFeed.checked)}checked="checked"{/if} /></td> *}
											<td class="center">
												{$aFeed.langName|escape:'htmlall':'UTF-8'}
												{if empty($aFeed.is_default)}
													<span class="badge badge-sm badge-info ml-2">{l s='Custom feed' mod='facebookproductad'}</span>
												{/if}
											</td>
											<td class="center">{$aFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFeed.country|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aFeed.currencyIso|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aFeed.filemtime|escape:'htmlall':'UTF-8'}</td>
											<td class="center">
												<a class="label-tooltip btn btn-sm btn-default regenXML" title="{l s='Generate' mod='facebookproductad'}" href="javascript:void(0);"
													onclick="if (oFpa.bGenerateXmlFlag){literal}{{/literal}alert('{l s='A data feed is being generated...' mod='facebookproductad'}'); return false;{literal}}{/literal}aDataFeedGenOptions.sLangIso='{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sCountryIso='{$aFeed.country|lower|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sCurrencyIso='{$aFeed.currencyIso|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.iLangId='{$aFeed.langId|intval}';aDataFeedGenOptions.sFilename='{$aFeed.filename|escape:'htmlall':'UTF-8'}';aDataFeedGenOptions.sFeedType='product';$('#syncCounterDiv').show();oFpa.generateDataFeed(aDataFeedGenOptions);"><span
														class="icon-refresh"></span></a>&nbsp;<div id="total_product_processed_{$aFeed.lang|lower|escape:'htmlall':'UTF-8'}_{$aFeed.country|lower|escape:'htmlall':'UTF-8'}" style="font-style: bold; display: none; margin-left:20px; vertical-align:text-top;"></div>
												<a class="label-tooltip btn btn-default btn-md" title="{l s='See' mod='facebookproductad'}" target="_blank" href="{$aFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-eye"></i></a>
												<a type="button" href="{$aFeed.link|escape:'htmlall':'UTF-8'}" download class="label-tooltip btn btn-md btn-default" title="{l s='Download' mod='facebookproductad'}">&nbsp;<i class="fa fa-download"></i></a>
												<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy URL' mod='facebookproductad'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>
												<a style="display:none;" href="#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}" id="reporting-data-{$aFeed.full|escape:'htmlall':'UTF-8'}" onclick="oFpa.cleanModal('#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}')" class="nav-link" data-remote="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.reportingBox.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.reportingBox.type|escape:'htmlall':'UTF-8'}&lang={$aFeed.full|escape:'htmlall':'UTF-8'}" data-toggle="modal" data-target="#theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}"><i class="fa fa-file fa-2x"></i></a>

												{if empty($aFeed.is_default)}
													<a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='facebookproductad'}" onclick="check = confirm('{l s='Are you sure you want to delete this data feed?' mod='facebookproductad'} {l s='It will be definitely removed from your database' mod='facebookproductad'}');if(!check)return false;$('#loadingFeedListDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.deleteFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.deleteFeed.type|escape:'htmlall':'UTF-8'}&export_mode=xml&id_feed={$aFeed.id_feed|intval}', 'bt_feed-list-settings', 'bt_feed-list-settings', null, null, 'loadingFeedListDiv');"></i></a>
												{/if}

												<div class="modal fade" id="theModal-{$aFeed.full|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog">
													<div class="modal-dialog modal-lg modal-dialog-centered" style="width:80%;" role="document">
														<div class="modal-content">
															<div class="modal-header"></div>
															<div class="modal-body">
																<div class="alert alert-info">
																	<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
																	<div class="clr_20"></div>
																	<p style="text-align: center !important;">{l s='The update of your configuration is in progress...' mod='facebookproductad'}</p>
																</div>
															</div>
														</div>
													</div>
												</div>
											</td>
										</tr>
									{/foreach}
								</table>

								<div class="row">
									<div class="col-xs-10">

									</div>
									<div class="navbar navbar-default navbar-fixed-bottom text-center">
										<button class="btn btn-submit" onclick="oFpa.form('bt_feedlist-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-list-settings', 'bt_feed-list-settings', false, false, null, 'FeedList', 'loadingFeedListDiv');return false;">{l s='Save' mod='facebookproductad'}</button>
									</div>
								</div>

							</form>
						</div>
						<div class="tab-pane fade" id="cron" role="tabpanel">
							<div class="clr_20"></div>
							<div class="alert alert-info form-group">
								<p>{l s='Please follow our FAQ to know' mod='facebookproductad'}&nbsp;&nbsp;<a class="badge badge-info" target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/217"><i class="icon icon-link"></i>&nbsp;{l s='how to create a CRON task' mod='facebookproductad'}</a></p>
								<p><b>{l s='Be careful:' mod='facebookproductad'}</b>&nbsp;{l s='schedule your CRON task so that the XML files are up to date when Facebook will retreive them to update your data.' mod='facebookproductad'}</p>
							</div>

							<div class="clr_15"></div>

							{if !empty($aCronList)}
								<table cellpadding="2" cellspacing="2" class="table">
									<tr class="bt_tr_header text-center">
										<th class="center">{l s='Language' mod='facebookproductad'}</th>
										<th class="center">{l s='Country' mod='facebookproductad'}</th>
										<th class="center">{l s='Currency' mod='facebookproductad'}</th>
										<th class="center">{l s='Taxonomy' mod='facebookproductad'}</th>
										<th class="center">{l s='Action' mod='facebookproductad'}</th>
									</tr>
									{foreach from=$aCronList name=feed key=iKey item=aCronFeed}
										<tr>
											<td class="center">{$aCronFeed.langName|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aCronFeed.countryName|escape:'htmlall':'UTF-8'} - {$aCronFeed.country|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aCronFeed.currencyIso|escape:'htmlall':'UTF-8'}</td>
											<td class="center">{$aCronFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
											<td class="center">
												<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy URL' mod='facebookproductad'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aCronFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>
												<a class="label-tooltip btn btn-default btn-md" target="_blank" title="{l s='Execute' mod='facebookproductad'}" href="{$aCronFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-play-circle"></i></a>
											</td>
										</tr>
									{/foreach}
								</table>
							{/if}
						</div>
					</div>
				</div>
				{* USE CASE - NO AVAILABLE LANGUAGE : CURRENCY : COUNTRY *}
			{else}
				<div class="clr_15"></div>
				<div class="alert alert-warning">
					{l s='Either you just updated your configuration by deactivating the advanced file security feature (in which case, please please reload the page), or there are no files because no valid languages / currencies / countries are available according to the Facebook\'s requirements' mod='facebookproductad'}.
				</div>
			{/if}

			{* USE CASE - AVAILABLE FEED FILE LIST *}
			{if !empty($aFlyFileList)}

				<div class="bt-fb-fly mt-1" id="bt-fb-fly-block" style="display: none;">

					<h1 class="display-4 mb-1">{l s='ON THE FLY OUTPUT' mod='facebookproductad'}</h1>
					<hr />

					<table cellpadding="2" cellspacing="2" class="table ">
						<tr class="bt_tr_header text-center">
							<th class="center">{l s='Language ' mod='facebookproductad'}</th>
							<th class="center">{l s='Country' mod='facebookproductad'}</th>
							<th class="center">{l s='Currency' mod='facebookproductad'}</th>
							<th class="center">{l s='Taxonomy' mod='facebookproductad'}</th>
							<th class="center"></th>
						</tr>
						{foreach from=$aFlyFileList name=feed key=iKey item=aFlyFeed}
							<tr>
								<td class="center">
									{$aFlyFeed.langName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.iso_code|escape:'htmlall':'UTF-8'}
									{if empty($aFlyFeed.is_default)}
										<span class="badge badge-sm badge-info ml-2">{l s='Custom feed' mod='facebookproductad'}</span>
									{/if}
								</td>
								<td class="center">{$aFlyFeed.countryName|escape:'htmlall':'UTF-8'} - {$aFlyFeed.country|escape:'htmlall':'UTF-8'}</td>
								<td class="center">{$aFlyFeed.currencyIso|escape:'htmlall':'UTF-8'}</td>
								<td class="center">{$aFlyFeed.taxonomy|escape:'htmlall':'UTF-8'}</td>
								<td class="center">
									<a class="label-tooltip btn btn-default btn-md" title="{l s='See' mod='facebookproductad'}" target="_blank" href="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}"><i class="fa fa-eye"></i></a>
									<a type="button" class="label-tooltip btn btn-md btn-default btn-copy js-tooltip js-copy" title="{l s='Copy URL' mod='facebookproductad'}" data-toggle="tooltip" data-placement="bottom" data-copy="{$aFlyFeed.link|escape:'htmlall':'UTF-8'}">&nbsp;<i class="fa fa-copy"></i></a>

									{if empty($aFlyFeed.is_default)}
										<a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='facebookproductad'}" onclick="check = confirm('{l s='Are you sure you want to delete this data feed?' mod='facebookproductad'} {l s='It will be definitely removed from your database' mod='facebookproductad'}');if(!check)return false;$('#loadingFeedListDiv').show();oFpa.hide('bt_feed-list-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.deleteFeed.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.deleteFeed.type|escape:'htmlall':'UTF-8'}&export_mode=fly&id_feed={$aFlyFeed.id_feed|intval}', 'bt_feed-list-settings', 'bt_feed-list-settings', null, null, 'loadingFeedListDiv');"></i></a>
									{/if}
								</td>
							</tr>
						{/foreach}
					</table>
				</div>

				{* USE CASE - THE OUTPUT PHP FILE HASN'T BEEN COPIED *}
			{else}
				<div class="mt-3"></div>
				<div class="alert alert-warning">
					{l s='To use this feature, please copy the facebookproductad.xml.php file from the facebookproductad module\'s directory to your shop\'s root directory' mod='facebookproductad'}.
				</div>
			{/if}

			<div id="{$sModuleName|escape:'htmlall':'UTF-8'}FeedListError"></div>
			{* USE CASE - NO CATEGORY OR BRAND HAVE BEEN SELECTED *}
		{else}
			<div class="clr_15"></div>

			<div class="alert alert-warning">
				{l s='You must first select an export method and check each category/brand you want to export (also be sure that there is at least one active product in the selected categories or brands). Please click on the "Feed management -> Export method" tab' mod='facebookproductad'}.
			</div>
		{/if}
		{* USE CASE - NO FACEBOOK LINK HAS BEEN FILLED OUT *}
	{else}
		<div class="clr_15"></div>

		<div class="alert alert-warning">
			{l s='You must first update the module\'s configuration options before the files can be accessed' mod='facebookproductad'}.
		</div>
	{/if}
</div>

{literal}
	<script type="text/javascript">
		oFpaFeedList.dynamicDisplay();

		$(document).ready(function() {
			oFpa.exportMode();
		});

		//bootstrap components init
	{/literal}
	{if !empty($bAjaxMode)}
		{literal}
			$('.label-tooltip, .help-tooltip').tooltip();
			{/literal}{/if}{literal}
		</script>
	{/literal}