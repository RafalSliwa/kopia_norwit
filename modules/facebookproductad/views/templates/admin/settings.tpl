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
<script type="text/javascript">
	{literal}
	var oFacebookCallback = [{}];
	{/literal}
</script>

<div class="bootstrap">
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_facebook-{$sDisplay|escape:'htmlall':'UTF-8'}-form" name="bt_facebook-{$sDisplay|escape:'htmlall':'UTF-8'}-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_facebook-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, oFacebookCallback, 'Facebook', 'loadingGoogleDiv');return false;"{/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.facebook.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.facebook.type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sDisplay" id="sGsDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}categories{/if}" />

		{* USE CASE - Facebook categories *}
		{if !empty($sDisplay) && $sDisplay == 'categories'}
			<h3 class="breadcrumb"><i class="icon-briefcase"></i>&nbsp;{l s='Facebook Categories' mod='facebookproductad'}</h3>

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div class="alert alert-info" id="info_export">
				<p><b>{l s='Each merchant has his own category names. But Facebook cannot manage all the possible and imaginable names. So to solve this problem, Facebook uses official category names that are, in fact, those created by Google. You have to match your own categories with these official ones. As each country has its own categories taxonomy, you have to make the association for each country where you want to display Facebook ads.' mod='facebookproductad'}</b></p>
				<br />
				<p>{l s='Please visit' mod='facebookproductad'}&nbsp;<b><a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/281" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='this FAQ' mod='facebookproductad'}</a></b>&nbsp;{l s='for more information.' mod='facebookproductad'}</p>
				<br />
				<ol>
				<li>{l s='Firstly, click on the loading icon' mod='facebookproductad'}&nbsp;<span class="icon-refresh">&nbsp;</span>{l s='to update the Facebook categories official list.' mod='facebookproductad'}</li>
				<li>{l s='Then, click on the pencil icon' mod='facebookproductad'}&nbsp;<span class="icon-pencil"></span>&nbsp;{l s='to match your own PrestaShop categories with the official ones.' mod='facebookproductad'}</li>
				</ol>
			</div>

			<div class="clr_20"></div>

			<div id="bt_facebook-cat-list">
				{include file="`$sFacebookCatListInclude`"}
			</div>

			<div class="clr_20"></div>
			<div id="loadingGoogleCatListDiv" style="display: none;">
				<div class="alert alert-info">
					<p style="text-align: center !important;"><img src="{$sBigLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p><div class="clr_20"></div>
					<p style="text-align: center !important;">{l s='The Facebook categories update is in progress...' mod='facebookproductad'}</p>
				</div>
			</div>
		{/if}
		{* END - Facebook categories *}

		{* USE CASE - Facebook analytics *}
		{if !empty($sDisplay) && $sDisplay == 'analytics'}
			<h3 class="breadcrumb">{l s='Google Analytics integration' mod='facebookproductad'}</h3>

			{if !empty($bUpdate)}
				{include file="`$sConfirmInclude`"}
			{elseif !empty($aErrors)}
				{include file="`$sErrorInclude`"}
			{/if}

			<div class="alert alert-info" id="info_export">
				<p><b>{l s='This section allows you to add some parameters in your product links (utm_campaign, utm_source and utm_medium) so that you can better track clicks and sales from your Facebook product ads campaigns in your Google Analytics account.' mod='facebookproductad'}</b></p>
				<br />
				<p>{l s='If a parameter is left empty below, it will not be added. Please add alphanumerical characters ONLY, without spaces. You can use "-" or "_" signs however. For more information please visit' mod='facebookproductad'}&nbsp;<b><a href="https://support.google.com/analytics/answer/1033863?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='this Google Analytics help page.' mod='facebookproductad'}</a></b></p>
				<br />
				<p>{l s='Note: if you want to use this feature, please make sure that the utm_campaign, utm_source and utm_medium parameters are not disallowed in your robots.txt file.' mod='facebookproductad'}</p>
			</div>

			<div class="clr_20"></div>

			<div class="form-group ">
				<label class="control-label col-xs-12 col-lg-3">
					<span><b>{l s='Value of utm_campaign parameter' mod='facebookproductad'}</b></span> :
				</label>
				<div class="col-xs-12 col-lg-3">
					<input type="text" size="30" name="bt_utm-campaign" value="{$sUtmCampaign|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-lg-3">
					<span><b>{l s='Value of utm_source parameter' mod='facebookproductad'}</b></span> :
				</label>
				<div class="col-xs-12 col-lg-3">
					<input type="text" size="30" name="bt_utm-source" value="{$sUtmSource|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="form-group ">
				<label class="control-label col-xs-12 col-lg-3">
					<span><b>{l s='Value of utm_medium parameter' mod='facebookproductad'}</b></span> :
				</label>
				<div class="col-xs-12 col-lg-3">
					<input type="text" size="30" name="bt_utm-medium" value="{$sUtmMedium|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
		{/if}
		{* END - Facebook analytics *}

		{* USE CASE - Facebook custom label *}
		{if !empty($sDisplay) && $sDisplay == 'adwords'}

			<div class="row">
				<div class="col-xs-12">
					<h3 class="breadcrumb"><i class="fa fa-bookmark-o"></i>&nbsp;{l s='Custom labels integration' mod='facebookproductad'}</h3>
				</div>
			</div>

			<div class="mt-3"></div>

			<div class="alert alert-info" id="info_export">
				<p><b>{l s='This section allows you to assign custom labels to your products. It is the same principle as' mod='facebookproductad'}&nbsp;<a href="https://support.google.com/google-ads/answer/6275295?hl={$sCurrentIso|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Google\'s custom labels.' mod='facebookproductad'}</a>&nbsp;{l s='You can visit our' mod='facebookproductad'}&nbsp;<a href="{$faqLink|escape:'htmlall':'UTF-8'}/{$sCurrentLang|escape:'htmlall':'UTF-8'}/faq/272" target="_blank">{l s='FAQ about custom label creation' mod='facebookproductad'}</a>.</b></p>
				<br />
				<p>{l s='Note : Facebook does not allow more than 5 labels per product. So, if one of your products has more than 5 custom labels, our module will select only the first 5 ones (in order of appearance below). You can change the sort order of the custom labels via drag and drop.' mod='facebookproductad'}</p>
			</div>

			<div class="mt-3"></div>

			<div class="col-xs-6">
				<div class="add_adwords">
					<a id="handleGoogleAdwords" class="fancybox.ajax btn btn-lg btn-success pull-right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8':'UTF-8'}"><i class="icon icon-plus-square"></i>&nbsp;{l s='Add a custom label' mod='facebookproductad'}</a>
				</div>
			</div>

			<div class="clr_20"></div>

			{if !empty($aTags)}
				<div class="clr_15"></div>

				<div class="form-group">
					<button type="button" class="btn btn-default" onclick="return oFpa.selectAll('input.CustomLabelBox', 'check');">
						<i class="icon icon-plus-square"></i><span>&nbsp;{l s='Check All' mod='facebookproductad'}</span>
					</button>
					<i class="fa fa-minus"></i>
					<button type="button" class="btn btn-default" onclick="return oFpa.selectAll('input.CustomLabelBox', 'uncheck');" >
						<i class="icon icon-minus-square"></i><span>&nbsp;{l s='Unselect All' mod='facebookproductad'}</span>
					</button>
					<i class="fa fa-minus"></i>
					<button class="btn btn-success " onclick="check = confirm('{l s='Are you sure you want to activate the selected custom label set(s)' mod='facebookproductad'} ?');if(!check)return false;iTagIds = oFpa.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&bActive=1&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-cogs"></i><span>&nbsp;{l s='Activate selection' mod='facebookproductad'}</span>
					</button>
					<i class="fa fa-minus"></i>
					<button class="btn btn-warning " onclick="check = confirm('{l s='Are you sure you want to deactivate the selected custom label set(s)' mod='facebookproductad'} ?');if(!check)return false;iTagIds = oFpa.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&bActive=0&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-cogs"></i><span>&nbsp;{l s='Deactivate selection' mod='facebookproductad'}</span>
					</button>
					<i class="fa fa-minus"></i>
					<button class="btn btn-danger " onclick="check = confirm('{l s='Are you sure you want to delete the selected custom label set(s)' mod='facebookproductad'} ?');if(!check)return false;iTagIds = oFpa.getBulkCheckBox('bt_custom_label-box');$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customDelete.type|escape:'htmlall':'UTF-8'}&iTagIds='+iTagIds+'&sDeleteType=bulk&sActionType=delete&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
						<i class="icon icon-trash"></i><span>&nbsp;{l s='Delete Selection' mod='facebookproductad'}</span>
					</button>
				</div>

				<div class="form-group">
					<div class="col-xs-12">
						<div class="alert alert-info">
							{l s='You can drag and drop the lines to sort the custom labels sets, thanks to the little cross at the left of each line.' mod='facebookproductad'}
						</div>
						<div id="bt_save_reoder" class="col-xs-12 alert alert-success">
							{l s='Your custom labels are saved' mod='facebookproductad'}
						</div>
						<table id="diagnosis_list" class="table tags" data-toggle="table" data-url="data.json" >
							<thead>
							<thead>
							<tr  class="bt_tr_header">
								<th></th>
								<th></th>
								<th data-sortable="true" style="text-align: center"># &nbsp;<i class="icon icon-sort"></i></th>
								<th data-sortable="true" style="text-align: center">{l s='Custom labels set name' mod='facebookproductad'}&nbsp;<i class="icon icon-sort"></i></th>
								<th data-sortable="true" style="text-align: center">{l s='Number' mod='facebookproductad'}&nbsp;<i class="icon icon-sort"></i></th>
								<th data-sortable="true" style="text-align: center">{l s='Custom labels valid until' mod='facebookproductad'}&nbsp;<i class="icon icon-sort"></i></th>
								<th data-sortable="true" style="text-align: center">{l s='State' mod='facebookproductad'}&nbsp;<i class="icon icon-sort"></i></th>
								<th style="text-align: center;">{l s='Activate / Deactivate' mod='facebookproductad'}</th>
								<th style="text-align: center;">{l s='Edit' mod='facebookproductad'}</th>
								<th style="text-align: center;">{l s='Delete' mod='facebookproductad'}</th>
								<th style="text-align: center" >{l s='Labelled products' mod='facebookproductad'}</th>
								<th style="text-align: center" ><a id="handleGoogleAdwords" class="fancybox.ajax btn btn-success" style="float: right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8':'UTF-8'}"><i class="icon icon-plus-square"></i></a></th>
							</tr>
							</thead>
							<tbody>

							{foreach from=$aTags name=label key=iKey item=aTag}
								<tr {if $aTag.active == 1}class="success ui-state-default"{else}class="danger ui-state-default"{/if} style="text-align: center">
									<td><i class="icon icon-move"></i> </td>
									<td><input type="checkbox" name="bt_custom_label-box" class="CustomLabelBox" id="bt_custom_label-box_{$aTag.id_tag|intval}" value="{$aTag.id_tag|intval}" /></td>
									<td>
										<span class="fpa_count_html"></span>
										<input type="hidden" class="priority" value="{$aTag.position|escape:'htmlall':'UTF-8'}"/>
									</td>
									<td>{$aTag.name|escape:'htmlall':'UTF-8'}</td>
									<td>{$aTag.custom_label_set_postion|escape:'htmlall':'UTF-8'}</td>
									<td>{$aTag.end_date|escape:'htmlall':'UTF-8'} <input type="hidden" id="fpa_date_custom_label" value="{$aTag.end_date|escape:'htmlall':'UTF-8'}"/></td>
									<td style="text-align: center">
										{if $aTag.active == 1}<i class="icon icon-check"></i>{else}<i class="icon icon-off"></i>{/if}
									</td>
									<td style="text-align: center">
										{if $aTag.active == 1}
											<button class="btn btn-warning btn-mini" onclick="check = confirm('{l s='Are you sure you want to deactivate this custom labels set' mod='facebookproductad'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|intval}&sDeleteType=one&bActive=0&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
												<i class="icon icon-off"></i>
											</button>
										{else}
											<button class="btn btn-success btn-mini"  id="fpa_process_activation" onclick="check = confirm('{l s='Are you sure you want to activate this custom labels set' mod='facebookproductad'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customActivate.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customActivate.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|intval}&sDeleteType=one&bActive=1&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
												<i class="icon icon-check"></i>
											</button>
										{/if}
									</td>
									<td>
										<a id="handleGoogleAdwordsEdit" class="fancybox.ajax btn btn-default btn-mini" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.custom.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.custom.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|intval}&sDisplay=button3"><i class="icon icon-edit"></i></a>
									<td>
										<button class="btn btn-danger btn-mini"  id="fpa_process_activation" onclick="check = confirm('{l s='Are you sure you want to delete this custom labels set' mod='facebookproductad'} ?');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_google-settings');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customDelete.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|intval}&sDeleteType=one&sDisplay=button3', 'bt_google-settings', 'bt_google-settings', null, null, 'loadingGoogleDiv');">
											<i class="icon icon-trash"></i>
										</button>
									</td>
									<td>
										<a id="cutomLabelProducDetails" class="fancybox.ajax btn btn-mini btn-default" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.customProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.customProduct.type|escape:'htmlall':'UTF-8'}&iTagId={$aTag.id_tag|intval}&sDisplay=button3""><i class="icon icon-zoom-in"></i></a>
									</td>
									<td></td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			{/if}
		{/if}
		{* END - Facebook custom label *}

		{if !empty($sDisplay) && $sDisplay == 'analytics'}

			<div class="mt-3"></div>
			<div class="clr_hr"></div>
			<div class="mt-3"></div>

			<div class="navbar navbar-default navbar-fixed-bottom text-center">
				<div class="col-xs-12">
					<button  class="btn btn-submit" onclick="oFpa.form('bt_facebook-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, oFacebookCallback, 'Facebook', 'loadingGoogleDiv');return false;">{l s='Save' mod='facebookproductad'}</button>
				</div>
			</div>
		{/if}
	</form>
</div>
{literal}

<script type="text/javascript">
	$(document).ready(function(){
		//get date for CL
		oFpa.manageCustomLabelDate('#diagnosis_list','#fpa_date_custom_label','#fpa_process_activation');

		// all process for table sort management
		$(function(){
			$(".tags").tablesorter();
		});

		var fixHelperModified = function(e, tr) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each(function(index)
			{
				$(this).width($originals.eq(index).width())
			});
			return $helper;
		};

		//Make diagnosis table sortable
		$("#diagnosis_list tbody").sortable({
			helper: fixHelperModified,
			stop: function(event,ui) {renumber_table('#diagnosis_list'),updateDataBase("#diagnosis_list")}
		}).disableSelection();
	});

	$("#diagnosis_list tbody tr").each(function () {
		count = $(this).parent().children().index($(this)) + 1;
		if ($(this).find(".fpa_count_html").html(count) == '')
		{
			$(this).find('.fpa_count_html').html(count);
		}
		$(this).find('.priority').val(count);
	});

	function renumber_table(tableID)
	{
		$(tableID + " tr").each(function () {
			count = $(this).parent().children().index($(this)) + 1;
			$(this).find(".fpa_count_html").html(count)
			$(this).find('.priority').val(count);
		});
	}

	function updateDataBase(tableID)
	{
		$(tableID + " tr").each(function () {
			//get value for request magement
			iTagIdMoveToNewPos = $(this).find(".CustomLabelBox").val();
			iNewPosition = $(this).find(".priority").val();
			iTagIdMoveToOldPos = $(this).parent().find(".CustomLabelBox").val();
			iOldPosition = $(this).parent().find(".priority").val();

			// construct data here
			sDataPrestashop = '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.position.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.position.type|escape:'htmlall':'UTF-8'}{literal}';
			sDataModule = '&iTagIdMoveToNewPos=' + iTagIdMoveToNewPos + '&iNewPosition=' + iNewPosition + '&iTagIdMoveToOldPos=' + iTagIdMoveToOldPos + '&iOldPosition=' + iOldPosition ;
			sData = sDataPrestashop + sDataModule;

			$.ajax({
				url : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				type : 'POST',
				data : sData,
				dataType : 'json',
				async : true,
				complete: function(result) {
					$('#bt_save_reoder').slideDown().delay(2000).slideUp();
				}
			});
		});
	}

	//bootstrap components init
	{/literal}{if !empty($bAjaxMode)}{literal}
	$('.label-tooltip, .help-tooltip').tooltip();
	oFpa.runMainGoogle();
	{/literal}{/if}{literal}

	$("#bt_save_reoder").delay(2000).slideUp();
</script>

{/literal}