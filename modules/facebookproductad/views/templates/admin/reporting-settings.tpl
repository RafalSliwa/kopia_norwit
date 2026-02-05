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
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_reporting-form" name="bt_reporting-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_feed-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_reporting-settings', 'bt_reporting-settings', false, false, null, 'Reporting', 'loadingReportingDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.reporting.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.reporting.type|escape:'htmlall':'UTF-8'}" />

		<h3 class="subtitle"><i class="icon-play"></i>&nbsp;{l s='Reporting' mod='facebookproductad'}</h3>

		{if !empty($bUpdate)}
			{include file="`$sConfirmInclude`"}
		{elseif !empty($aErrors)}
			{include file="`$sErrorInclude`"}
		{/if}

		<div class="alert alert-info" id="info_export">
			<p>{l s='Please read the following FAQ to learn how the tool works:' mod='facebookproductad'}
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/186" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How does the diagnostic tool work?' mod='facebookproductad'}</a>
			</p>
			<p>{l s='This tool allows you' mod='facebookproductad'}&nbsp;<b>{l s='to display again the last' mod='facebookproductad'}</b>&nbsp;{l s='feed quality diagnostic that has been generated. Click on the "Action" icon of the "country/language/currency" combination that matches with the feed you want to display again the last reporting.' mod='facebookproductad'}</p>
		</div>
		<div class="alert alert-warning">
			<p>{l s='WARNING : if you change your feed configuration, remember to manually generate again your feed (in "My feeds" tab -> "Your XML files") and to save again this current page, in order to be sure of having the very last reporting.' mod='facebookproductad'}</p>
		</div>

		<div class="clr_20"></div>

		<div class="form-group" id="optionplus">
			<label class="control-label col-xs-2 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" to fill out the reporting file automatically every time the matching feed URL is called (manually, or by an automated task). Select "No" to fill it out only for manually generating (in "My feeds" tab -> "Your XML files"). However, If you\'ve an important bulk of products (many thousands), you should leave this option on "No" in order to improve speed and performance of data feeds generating.' mod='facebookproductad'}"><b>{l s='Activate flux reporting' mod='facebookproductad'}</b></span> :</label>
			<div class="col-xs-12 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_reporting" id="bt_reporting_on" value="1" {if !empty($bReporting)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('display_reporting', 'display_reporting', null, null, true, true);" />
					<label for="bt_reporting_on" class="radioCheck">
						{l s='Yes' mod='facebookproductad'}
					</label>
					<input type="radio" name="bt_reporting" id="bt_reporting_off" value="0" {if empty($bReporting)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('display_reporting', 'display_reporting', null, null, true, false);" />
					<label for="bt_reporting_off" class="radioCheck">
						{l s='No' mod='facebookproductad'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" to fill out the reporting file automatically every time the matching feed URL is called (manually, or by an automated task). Select "No" to fill it out only for manually generating (in "My feeds" tab -> "Your XML files"). However, If you\'ve an important bulk of products (many thousands), you should leave this option on "No" in order to improve speed and performance of data feeds generating.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
			</div>
		</div>

		<div class="form-group" id="display_reporting" style="display: {if !empty($bReporting)}block{else}none{/if};">
			{if !empty($aLangCurrencies)}
				<div class="clr_10"></div>
				<table class="table">
					<thead>
						<th class="bt_tr_header center">{l s='Country' mod='facebookproductad'}</th>
						<th class="bt_tr_header center">{l s='Language' mod='facebookproductad'}</th>
						<th class="bt_tr_header center">{l s='Currency' mod='facebookproductad'}</th>
						<th class="bt_tr_header center">{l s='Action' mod='facebookproductad'}</th>
					</thead>
					<tbody>
						{foreach from=$aLangCurrencies item=sISO key=currency}
							<tr>
								<td class="center">{$sISO.country|escape:'htmlall':'UTF-8'}</td>
								<td class="center">{$sISO.lang_iso|escape:'htmlall':'UTF-8'}</td>
								<td class="center">{$sISO.currency|escape:'htmlall':'UTF-8'}</td>
								<td class="center">
									<a href="#themodal-reporting-{$sISO.full|escape:'htmlall':'UTF-8'}" onclick="oFpa.cleanModal('#themodal-reporting-{$sISO.full|escape:'htmlall':'UTF-8'}')" class="nav-link" data-remote="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.reportingBox.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.reportingBox.type|escape:'htmlall':'UTF-8'}&lang={$sISO.full|escape:'htmlall':'UTF-8'}" data-toggle="modal" data-target="#themodal-reporting-{$sISO.full|escape:'htmlall':'UTF-8'}"><i class="fa fa-file fa-2x"></i></a>
								</td>
							</tr>

							<div class="modal fade" id="themodal-reporting-{$sISO.full|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog">
								<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
									<div class="modal-content">
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
						{/foreach}
					</tbody>
				</table>
			{else}
				<label class="control-label col-lg-3">
				</label>

				<div class="col-xs-6">
					<div class="alert alert-warning">
						<p>{l s='There is currently no report available.' mod='facebookproductad'}</p>
						<p>{l s='Please generate again manually your feed (in "My feeds" tab -> "Your XML files" -> "Update"), save again this current page and if there is always nothing, make sure that the "reporting" folder inside the "facebookproductad" module folder has correct writing permissions.' mod='facebookproductad'}</p>
					</div>
				</div>
			{/if}
		</div>

		<div class="clr_10"></div>
		<div class="clr_hr"></div>
		<div class="clr_10"></div>

		<div class="center">
			<div class="row">
				<div class="col-xs-12">
					<div id="{$sModuleName|escape:'htmlall':'UTF-8'}ReportingError"></div>
				</div>
				<div class="col-xs-12">
					<button class="btn btn-submit" onclick="oFpa.form('bt_reporting-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_reporting-settings', 'bt_reporting-settings', false, false, null, 'Reporting', 'loadingReportingDiv');return false;">{l s='Save' mod='facebookproductad'}</button>
				</div>
			</div>
		</div>
	</form>
</div>
{literal}
	<script type="text/javascript">
		$(document).ready(function() {

			// manage change value for reporting
			$("#bt_reporting").change(function() {
				if ($(this).val() == "1") {
					$("#display_reporting").show();
				} else {
					$("#display_reporting").hide();
				}
			});
			//bootstrap components init
			{/literal}
				{if !empty($bAjaxMode)}
					{literal}
						$('.label-tooltip, .help-tooltip').tooltip();
						{/literal}{/if}{literal}
					});
				</script>
			{/literal}