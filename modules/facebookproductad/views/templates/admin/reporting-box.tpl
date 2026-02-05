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
<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
	{if !empty($aErrors)}
		{include file="`$sErrorInclude`"}
		{* USE CASE - edition review mode *}
	{else}
		<div class="modal-header">
			<h3 class="modal-title">
				<div class="d-flex flex-nowrap bd-highlight">
					<div class="order-1 p-2 bd-highlight">
						{l s='language' mod='facebookproductad'} "<strong>{$sLangName|escape:'htmlall':'UTF-8'}</strong>" / {l s='country' mod='facebookproductad'} "<strong>{$sCountryName|escape:'htmlall':'UTF-8'}</strong>"
					</div>
					<div class="order-2 p-2 bd-highlight">
						{if isset($iProductCount)}
							{if $iProductCount > 0}
								<p class="badge badge-success pull-right"> {l s='Total of products exported:' mod='facebookproductad'} <strong>{$iProductCount|intval}</strong></p>
							{else}
								<p class="badge badge-danger pull-right"> {l s='Total of products exported:' mod='facebookproductad'} <strong>{$iProductCount|intval}</strong></p>
							{/if}
						{/if}
					</div>
				</div>
			</h3>
		</div>
		<div class="modal-body">
			{if empty($aReport.warning) && empty($aReport.notice) && empty($aReport.error)}
				<div class="alert alert-success">
					{l s='All products data are ok' mod='facebookproductad'}
				</div>
			{else}
				<div class="alert alert-info">
					{l s='This tool allows you to perform a diagnostic of your feed in order to check the quality of the data before exporting it to Facebook. Please follow' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/186" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='this FAQ' mod='facebookproductad'}</a>&nbsp;{l s='to understand how this diagnostic tool works and how to interpret the results.' mod='facebookproductad'}
				</div>

				{if !empty($aReport) && !empty($aReport.error)}
					<div class="d-flex flex-nowrap bd-highlight">
						<div class="box box-reporting box-reporting-error">
							<div class="box-icon box-icon-danger">
								<span class="icon icon-exclamation-sign icon-3x"></span>
							</div>
							<div class="info" style="min-height: 120px;">
								<h4 class="text-center">{l s='ERRORS' mod='facebookproductad'}</h4>
								<p class="center">{l s='Click on "View details" to see which products generate errors.' mod='facebookproductad'}</p>
								<p class="center"><strong>{l s='These products will not be exported because they lack mandatory information for the social network. Please fix the errors and re-generate the feed.' mod='facebookproductad'}</strong></p>
								<div class="clr_30"></div>
								<div class="center">
									<a id="btn-reporting-error" class="btn btn-lg btn-lg-custom btn-danger">{l s='View details' mod='facebookproductad'}</a>
								</div>
							</div>
						</div>
					</div>
					<div id="btn-reporting-error-box" style="display: none">
						<div class="clr_20"></div>
						<table class="table table-responsive">
							<thead>
								<th class="bt_error">{l s='Type' mod='facebookproductad'}</th>
								<th class="bt_error">{l s='Description' mod='facebookproductad'}</th>
								<th class="bt_error"> {l s='View product' mod='facebookproductad'}</th>
								<th class="bt_error"> {l s='View solution' mod='facebookproductad'}</th>
							</thead>
							<tbody>
								{foreach from=$aReport.error item=aTag key=tagName name=report}
									<tr class="bt_reporting_line">
										<td>
											<span class="bt_report_notification">{$aTag.label|escape:'htmlall':'UTF-8'}</span><span class="badge badge-danger pull-right">{$aTag.count|intval}</strong>&nbsp;{l s='notice' mod='facebookproductad'}{if $aTag.count > 1}s{/if}</span>
										</td>
										<td>
											<p class="summary bt_report_notification">{$aTag.msg|escape:'htmlall':'UTF-8'}</p>
										</td>
										<td>
											<span class="bt_report_notification"><a href="#" class="btn btn-sm btn-warning text-white" onclick="$('#tagReport{$tagName|escape:'htmlall':'UTF-8'}').toggle(); return false;"><i class="icon-eye-open"></i> &nbsp;{l s='View affected products' mod='facebookproductad'}</a></span>
										</td>
										<td>
											<span class="bt_report_notification"><a class="btn btn-sm btn-info" href="{$faqLink|escape:'htmlall':'UTF-8'}faq/{$aTag.faq_id|intval}{if !empty($aTag.anchor)}#{$aTag.anchor|escape:'htmlall':'UTF-8'}{/if}" target="_blank"><i class="icon-question-sign"></i>&nbsp; {l s='Learn how to fix this problem' mod='facebookproductad'}</a></span>
										</td>
									</tr>
									<tr>
										<td class="bt_error_products" colspan="5" id="tagReport{$tagName|escape:'htmlall':'UTF-8'}" style="display: none;">
											<table class="table table-striped">
												<thead>
													<th class="bt_reporting_products">{l s='Product Id' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Product name' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Actions' mod='facebookproductad'}</th>
												</thead>
												<tbody>
													{foreach from=$aTag.data item=aProduct key=key}
														<tr>
															<td class="bt_reporting_products-lines">{$aProduct.productId|intval}</td>
															<td class="bt_reporting_products-lines">{$aProduct.productName|escape:'htmlall':'UTF-8'}</td>
															<td class="bt_reporting_products-lines"><a class="btn btn-sm btn-success" href="{$aProduct.productUrl|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-eye-open"></i> {l s='View product' mod='facebookproductad'}</a>
																&nbsp;
																<a class="btn btn-sm btn-success" href="{$sProductLinkController|escape:'htmlall':'UTF-8'}&id_product={$aProduct.productId|intval}{$sProductAction|escape:'htmlall':'UTF-8'}&token={$sToken|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-edit"></i> {l s='Edit' mod='facebookproductad'}</a>
															</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				{/if}

				{if !empty($aReport) && !empty($aReport.warning)}
					<div class="d-flex flex-nowrap bd-highlight">
						<div class="box  box-reporting box-reporting-warning">
							<div class="box-icon box-icon-warning">
								<span class="icon icon-warning icon-3x"></span>
							</div>
							<div class="info" style="min-height: 120px;">
								<h4 class="text-center">{l s='WARNINGS' mod='facebookproductad'}</h4>
								<p class="center">{l s='Click on "View details" to see which products deserve your attention because their data quality needs to be improved.' mod='facebookproductad'}</p>
								<p class="center"><strong>{l s='However, since these are just warnings and not errors, these products will still be exported in the data feed.' mod='facebookproductad'}</strong></p>
								<div class="clr_30"></div>
								<div class="center">
									<a id="btn-reporting-warning" class="btn btn-lg btn-lg-custom btn-warning text-white">{l s='View details' mod='facebookproductad'}</a>
								</div>
							</div>
						</div>
					</div>
					<div id="btn-reporting-warning-box" style="display: none">
						<div class="clr_20"></div>
						<table class="table table-responsive">
							<thead>
								<th class="bt_warning">{l s='Type' mod='facebookproductad'}</th>
								<th class="bt_warning">{l s='Description' mod='facebookproductad'}</th>
								<th class="bt_warning"> {l s='View products' mod='facebookproductad'}</th>
								<th class="bt_warning"> {l s='View solution' mod='facebookproductad'}</th>
							</thead>
							<tbody>
								{foreach from=$aReport.warning item=aTag key=tagName name=report}
									<tr class="bt_reporting_line">
										<td>
											<span class="bt_report_notification">{$aTag.label|escape:'htmlall':'UTF-8'}</span><span class="badge badge-warning pull-right">{$aTag.count|intval}</strong>&nbsp;{l s='notice' mod='facebookproductad'}{if $aTag.count > 1}s{/if}</span>
										</td>
										<td>
											<p class="summary bt_report_notification">{$aTag.msg|escape:'htmlall':'UTF-8'}</p>
										</td>
										<td>
											<span class="bt_report_notification"><a href="#" class="btn btn-sm btn-warning text-white" onclick="$('#tagReport{$tagName|escape:'htmlall':'UTF-8'}').toggle(); return false;"><i class="icon-eye-open"></i> &nbsp;{l s='View affected products' mod='facebookproductad'}</a></span>
										</td>
										<td>
											<span class="bt_report_notification"><a class="btn btn-sm btn-info" href="{$faqLink|escape:'htmlall':'UTF-8'}faq/{$aTag.faq_id|intval}{if !empty($aTag.anchor)}#{$aTag.anchor|escape:'htmlall':'UTF-8'}{/if}" target="_blank"><i class="icon-question-sign"></i>&nbsp; {l s='Learn how to fix this problem' mod='facebookproductad'}</a></span>
										</td>
									</tr>
									<tr>
										<td class="bt_warning_products" colspan="5" id="tagReport{$tagName|escape:'htmlall':'UTF-8'}" style="display: none;">
											<table class="table table-striped">
												<thead>
													<th class="bt_reporting_products">{l s='Product Id' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Product name' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Actions' mod='facebookproductad'}</th>
												</thead>
												<tbody>
													{foreach from=$aTag.data item=aProduct key=key}
														<tr>
															<td class="bt_reporting_products-lines">{$aProduct.productId|intval}</td>
															<td class="bt_reporting_products-lines">{$aProduct.productName|escape:'htmlall':'UTF-8'}</td>
															<td class="bt_reporting_products-lines"><a class="btn btn-sm btn-success" href="{$aProduct.productUrl|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-eye-open"></i> {l s='View product' mod='facebookproductad'}</a>
																&nbsp;
																<a class="btn btn-sm btn-success" href="{$sProductLinkController|escape:'htmlall':'UTF-8'}&id_product={$aProduct.productId|intval}{$sProductAction|escape:'htmlall':'UTF-8'}&token={$sToken|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-edit"></i> {l s='Edit' mod='facebookproductad'}</a>
															</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				{/if}

				{if !empty($aReport) && !empty($aReport.notice)}
					<div class="d-flex flex-nowrap bd-highlight">
						<div class="box  box-reporting box-reporting-info">
							<div class="box-icon box-icon-info">
								<span class="icon icon-info icon-3x"></span>
							</div>
							<div class="info" style="min-height: 120px;">
								<h4 class="text-center">{l s='INFORMATION' mod='facebookproductad'}</h4>
								<p class="center">{l s='Click on "View details" to see the products you have decided to exclude. This only concerns general exclusion rules. If you have created advanced exclusion rules, the products excluded will not appear here.' mod='facebookproductad'}</p>
								<p class="center"><strong>{l s='As you have decided, these products will not be exported in the data feed.' mod='facebookproductad'}</strong></p>
								<div class="clr_30"></div>
								<div class="center">
									<a id="btn-reporting-info" class="btn btn-lg btn-lg-custom btn-info text-white">{l s='View details' mod='facebookproductad'}</a>
								</div>
							</div>
						</div>
					</div>
					<div id="btn-reporting-info-box" style="display: none">
						<div class="clr_20"></div>
						<table class="table table-responsive">
							<thead>
								<th class="bt_notice">{l s='Type' mod='facebookproductad'}</th>
								<th class="bt_notice">{l s='Description' mod='facebookproductad'}</th>
								<th class="bt_notice"> {l s='View products' mod='facebookproductad'}</th>
								<th class="bt_notice"> {l s='View solution' mod='facebookproductad'}</th>
							</thead>
							<tbody>
								{foreach from=$aReport.notice item=aTag key=tagName name=report}
									<tr class="bt_reporting_line">
										<td>
											<span class="bt_report_notification">{$aTag.label|escape:'htmlall':'UTF-8'}</span><span class="badge badge-info pull-right">{$aTag.count|intval}</strong>&nbsp;{l s='notice' mod='facebookproductad'}{if $aTag.count > 1}s{/if}</span>
										</td>
										<td>
											<p class="summary bt_report_notification">{$aTag.msg|escape:'htmlall':'UTF-8'}</p>
										</td>
										<td>
											<span class=" bt_report_notification"><a href="#" class="btn btn-sm btn-warning" onclick="$('#tagReport{$tagName|escape:'htmlall':'UTF-8'}').toggle(); return false;"><i class="icon-eye-open"></i> &nbsp;{l s='View affected products' mod='facebookproductad'}</a></span>
										</td>
										<td>
											<span class="bt_report_notification"><a class="btn btn-sm btn-info" href="{$faqLink|escape:'htmlall':'UTF-8'}faq/{$aTag.faq_id|intval}{if !empty($aTag.anchor)}#{$aTag.anchor|escape:'htmlall':'UTF-8'}{/if}" target="_blank"><i class="icon-question-sign"></i>&nbsp; {l s='Learn how to fix this problem' mod='facebookproductad'}</a></span>
										</td>
									</tr>
									<tr>
										<td class="bt_notice_products" colspan="5" id="tagReport{$tagName|escape:'htmlall':'UTF-8'}" style="display: none;">
											<table class="table table-striped">
												<thead>
													<th class="bt_reporting_products">{l s='Product Id' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Product name' mod='facebookproductad'}</th>
													<th class="bt_reporting_products">{l s='Actions' mod='facebookproductad'}</th>
												</thead>
												<tbody>
													{foreach from=$aTag.data item=aProduct key=key}
														<tr>
															<td class="bt_reporting_products-lines">{$aProduct.productId|intval}</td>
															<td class="bt_reporting_products-lines">{$aProduct.productName|escape:'htmlall':'UTF-8'}</td>
															<td class="bt_reporting_products-lines"><a class="btn btn-sm btn-success" href="{$aProduct.productUrl|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-eye-open"></i> {l s='View product' mod='facebookproductad'}</a>
																&nbsp;
																<a class="btn btn-sm btn-success" href="{$sProductLinkController|escape:'htmlall':'UTF-8'}&id_product={$aProduct.productId|intval}{$sProductAction|escape:'htmlall':'UTF-8'}&token={$sToken|escape:'htmlall':'UTF-8'}" target="_blank"><i class="icon icon-edit"></i> {l s='Edit' mod='facebookproductad'}</a>
															</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				{/if}
			{/if}
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">{l s='Close' mod='facebookproductad'}</button>
		</div>
	{/if}
</div>

{literal}
	<script type="text/javascript">
		// This code manages the event click and color display on the reporting

		// Manage the click event on reporting block error
		$("#btn-reporting-error").click(function() {
			$("#btn-reporting-warning-box").slideUp();
			$("#btn-reporting-info-box").slideUp();
			$("#btn-reporting-error-box").slideDown();

			// Bloc for error
			$(".box-reporting-error").css('border', '2px solid #da7b82');
			$("#btn-reporting-error").css('background-color', '#a94442');
			$("#btn-reporting-error").css('text-decoration', 'underline');

			// Bloc for notice
			$(".box-reporting-info").css('border', '2px solid #CCCED7');
			$("#btn-reporting-info").css('background-color', '#4ac7e0');
			$("#btn-reporting-info").css('text-decoration', 'none');

			// bloc for warning
			$(".box-reporting-warning").css('border', '2px solid #CCCED7');
			$("#btn-reporting-warning").css('background-color', '#fcc94f');
			$("#btn-reporting-warning").css('text-decoration', 'none');
		});

		// Manage the click event on reporting block info
		$("#btn-reporting-info").click(function() {
			$("#btn-reporting-warning-box").slideUp();
			$("#btn-reporting-error-box").slideUp();
			$("#btn-reporting-info-box").slideDown();

			// Bloc for error
			$(".box-reporting-error").css('border', '2px solid #CCCED7');
			$("#btn-reporting-error").css('background-color', '#da7b82');
			$("#btn-reporting-error").css('text-decoration', 'none');

			// Bloc for notice
			$(".box-reporting-info").css('border', '2px solid #4ac7e0');
			$("#btn-reporting-info").css('background-color', '#21a6c1');
			$("#btn-reporting-info").css('text-decoration', 'underline');

			// bloc for warning
			$(".box-reporting-warning").css('border', '2px solid #CCCED7');
			$("#btn-reporting-warning").css('background-color', '#fcc94f');
			$("#btn-reporting-warning").css('text-decoration', 'none');

		});

		// Manage the click event on reporting block warning
		$("#btn-reporting-warning").click(function() {
			$("#btn-reporting-error-box").slideUp();
			$("#btn-reporting-info-box").slideUp();
			$("#btn-reporting-warning-box").slideDown();

			// Bloc for error
			$(".box-reporting-error").css('border', '2px solid #CCCED7');
			$("#btn-reporting-error").css('background-color', '#da7b82');
			$("#btn-reporting-error").css('text-decoration', 'none');

			// Bloc for notice
			$(".box-reporting-info").css('border', '2px solid #CCCED7');
			$("#btn-reporting-info").css('background-color', '#4ac7e0');
			$("#btn-reporting-info").css('text-decoration', 'none');

			//bloc for warning
			$(".box-reporting-warning").css('border', '2px solid #fbb309');
			$("#btn-reporting-warning").css('background-color', '#f0ad4e');
			$("#btn-reporting-warning").css('text-decoration', 'underline');
		});
	</script>
{/literal}