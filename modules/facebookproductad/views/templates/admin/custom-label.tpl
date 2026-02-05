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
{if !empty($aErrors)}
	{include file="`$sErrorInclude`"}
	{* USE CASE - edition add/edit custom label mode *}
{else}
	<div id="{$sModuleName|escape:'htmlall':'UTF-8'}" class="bootstrap">
		<div id="bt_custom-tag" class="col-xs-12 bt_adwords">
			{if !empty($aTag)}
				<h3 class="text-center"><i class="fa fa-tags"></i>&nbsp; {l s='Update a custom label' mod='facebookproductad'}</h3>
			{else}
				<h3 class="text-center"><i class="fa fa-tags"></i>&nbsp; {l s='Create a custom label' mod='facebookproductad'}</h3>
			{/if}
			<div class="clr_hr"></div>
			<div class="clr_20"></div>

			<script type="text/javascript">
				{literal}
					var oCustomCallBack = [{
						'name': 'displayGoogleList',
						'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
						'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType={/literal}{$aQueryParams.facebook.type|escape:'htmlall':'UTF-8'}{literal}&sDisplay=adwords',
						'toShow': 'bt_settings-adwords',
						'toHide': 'bt_settings-adwords',
						'bFancybox': false,
						'bFancyboxActivity': false,
						'sLoadbar': null,
						'sScrollTo': null,
						'oCallBack': {}
					}];
				{/literal}
			</script>

			<form class="form-horizontal" method="post" id="bt_form-custom-tag" name="bt_form-custom-tag" {if $useJs == true}onsubmit="oFpa.form('bt_form-custom-tag', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_custom-tag', 'bt_custom-tag', false, true, oCustomCallBack, 'CustomTag', 'loadingCustomTagDiv');return false;" {/if}>
				<input type="hidden" name="{$sCtrlParamName|escape:'htmlall':'UTF-8'}" value="{$sController|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sAction" value="{$aQueryParams.customUpdate.action|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sType" value="{$aQueryParams.customUpdate.type|escape:'htmlall':'UTF-8'}" />
				{if !empty($aTag)}
					<input type="hidden" name="bt_tag-id" value="{$aTag.id_tag|intval}" id="tag_id" />
				{/if}

				<div class="alert alert-info">
					<p>{l s='To help you in your custom labels creation, don\'t hesitate to read our ' mod='facebookproductad'}
							<a class="badge badge-info pulse pulse2" href="{$faqLink|escape:'htmlall':'UTF-8'}/{$sCurrentLang|escape:'htmlall':'UTF-8'}/faq/272" target="_blank">{l s='FAQ : How to create custom labels ?' mod='facebookproductad'}</a></p>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<b>{l s='Do you want to activate these labels ?' mod='facebookproductad'}</b>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl-statut" id="bt_cl-statut">
							<option value="1" {if $bActive == 1} selected="selected" {/if}>{l s='Yes' mod='facebookproductad'}</option>
							<option value="0" {if $bActive == 0} selected="selected" {/if}>{l s='No' mod='facebookproductad'}</option>
						</select>
					</div>
				</div>

				<div class="alert alert-warning">
					{l s='Give below a number to this set of custom labels. Be careful not to assign the same number to several active sets of labels! If two sets have the same number, their activation periods must not overlap. See the FAQ for more details.' mod='facebookproductad'}
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">
						<b>{l s='Custom label number' mod='facebookproductad'}</b>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl_association" id="bt_cl-statut">
							{foreach from=$labelPosition key=myKey item=position}
								<option value="{$position|escape:'htmlall':'UTF-8'}" {if $customLabelSetPosition == $position} selected {/if}>{$position|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<span><b>{l s='Configuration type' mod='facebookproductad'}</b></span>
					</label>
					<div class="col-xs-3">
						<select name="bt_cl-type" id="bt_cl-type">
							{if isset($aCustomLabelType.$sCurrentIso)}
								{foreach from=$aCustomLabelType.$sCurrentIso key=myKey item=CustomLabelType}
									{if !empty($aTag.type)}
										<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $aTag.type} selected="selected" {/if}>{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
									{else}
										<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
									{/if}
								{/foreach}
							{else}
								{foreach from=$aCustomLabelType.en key=myKey item=CustomLabelType}
									{if !empty($aTag.type)}
										<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $aTag.type} selected="selected" {/if}>{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
									{else}
										<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomLabelType|escape:'htmlall':'UTF-8'}</option>
									{/if}
								{/foreach}
							{/if}
						</select>
					</div>
				</div>

				<div class="form-group" id="optionplus">
					<label class="control-label col-xs-2">
						<b>{l s='Value' mod='facebookproductad'}</b>
					</label>
					<div class="col-xs-3">
						<div id="fpa_infobox_dynamique_cat">
							<p class="alert alert-info col-xs-12">
								{l s='For each product, the value of the custom label will be its default category name.' mod='facebookproductad'}<br />
								{l s='The "Value" field below only allows you to give a name to this set of your custom labels you\'re going to create.  It also allows you to locate this set in the custom labels list' mod='facebookproductad'}
								<b><a href="{$faqLink|escape:'htmlall':'UTF-8'}/{$sCurrentLang|escape:'htmlall':'UTF-8'}/faq/272" target="_blank">{l s='(see our FAQ).' mod='facebookproductad'}</a></b>
							<div class="clr_20"></div>
							</p>
						</div>
						<input type="text" id="bt_label-name" name="bt_label-name" value="{if !empty($aTag)}{$aTag.name|escape:'htmlall':'UTF-8'}{/if}" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-xs-2">
						<span><b>{l s='This custom label will be valid until :' mod='facebookproductad'}</b></span>
					</label>
					<div class="col-xs-3">
						<div class="col-12">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon icon-calendar"></i> </span>
								<input type="text" name="bt_cl_date_end" id="bt_cl_date_end" class="date-picker" value="{$sDate|escape:'htmlall':'UTF-8'}" />
							</div>
						</div>
					</div>
				</div>

				<div id="bt_add_filter">
					<div class="row">
						<div class="col-xs-12" id="bt_cl_configure_new_products">
							<div class="form-group">
								<label class="control-label col-xs-2">
									<b>{l s='Select a add date from which a product is considered as "new"' mod='facebookproductad'}</b>
								</label>
								<div class="col-xs-2">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; </span>
										<input type="text" name="bt_cl_dyn_date_start" id="bt_cl_dyn_date_start" class="date-picker" value="{$sDateNewPoduct|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
							</div>
						</div>
					</div>

					<p class="alert alert-info" id="fpa_manual_info">{l s='Filters below can be combined.' mod='facebookproductad'}</p>

					<div class="col-xs-12">
						<table class="table">
							<thead>
								<tr class="bt_tr_header">
									<th id="bt_cl_configure_cat_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-123">
												<b>
													<h4>{l s='Manage by categories' mod='facebookproductad'}</h4>
													<b>
											</div>
											<div class="col-12 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="categoryCheck" onclick="return oFpa.selectAll('input.categoryBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='facebookproductad'}</div> - <div class="btn btn-default btn-sm" id="categoryUnCheck" onclick="return oFpa.selectAll('input.categoryBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='facebookproductad'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_brand_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-12">
												<b>
													<h4>{l s='Manage by brands' mod='facebookproductad'}</h4><b>
											</div>
											<div class="col-12 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="brandCheck" onclick="return oFpa.selectAll('input.brandBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='facebookproductad'}</div> - <div class="btn btn-default btn-sm" id="brandUnCheck" onclick="return oFpa.selectAll('input.brandBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='facebookproductad'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_supplier_header" style="border-right: 1px solid #FFFFFF" class="col-xs-3">
										<div class="row">
											<div class="col-12">
												<b>
													<h4>{l s='Manage by suppliers' mod='facebookproductad'}</h4><b>
											</div>
											<div class="col-12 pull-right">
												<span class="pull-right">
													<div class="btn btn-default btn-sm" id="supplierCheck" onclick="return oFpa.selectAll('input.supplierBoxLabel', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='facebookproductad'}</div> - <div class="btn btn-default btn-sm" id="supplierUnCheck" onclick="return oFpa.selectAll('input.supplierBoxLabel', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='facebookproductad'}</div>
												</span>
											</div>
										</div>
									</th>
									<th id="bt_cl_configure_product_header">
										<div class="row">
											<div class="col-xs-6">
												<b>
													<h4>{l s='Manage by products (individually)' mod='facebookproductad'}</h4><b>
											</div>
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="bt_table_td" id="bt_cl_configure_cat">
										<div id="bt_cat_tree" class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatCat name=category key=iKey item=aCat}
													<tr class="alt_row">
														<td>
															{$aCat.id_category|intval}
														</td>
														<td>
															<input type="checkbox" name="bt_category-box[]" class="categoryBoxLabel" id="bt_category-box_{$aCat.iNewLevel|intval}" value="{$aCat.id_category|intval}" {if !empty($aCat.bCurrent)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aCat.bCurrent)}-open{/if}" style="margin-left: {$aCat.iNewLevel|escape:'htmlall':'UTF-8'}5px;"></i>&nbsp;<span style="font-size:12px;">{$aCat.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_brand">
										<div class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatBrands name=brand key=iKey item=aBrand}
													<tr class="alt_row">
														<td>
															{$aBrand.id|intval}
														</td>
														<td>
															<input type="checkbox" name="bt_brand-box[]" class="brandBoxLabel" id="bt_brand-box_{$aBrand.id|intval}" value="{$aBrand.id|intval}" {if !empty($aBrand.checked)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aBrand.checked)}-open{/if}"></i>&nbsp;&nbsp;<span style="font-size:12px;">{$aBrand.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_supplier">
										<div class="col-xs-12 bt_select_product">
											<table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
												{foreach from=$aFormatSuppliers name=supplier key=iKey item=aSupplier}
													<tr class="alt_row">
														<td>
															{$aSupplier.id|intval}
														</td>
														<td>
															<input type="checkbox" name="bt_supplier-box[]" class="supplierBoxLabel" id="bt_supplier-box_{$aSupplier.id|intval}" value="{$aSupplier.id|intval}" {if !empty($aSupplier.checked)}checked="checked" {/if} />
														</td>
														<td>
															<i class="icon icon-folder{if !empty($aSupplier.checked)}-open{/if}"></i>&nbsp;&nbsp;<span style="font-size:12px;">{$aSupplier.name|escape:'htmlall':'UTF-8'}</span>
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									</td>
									<td class="bt_table_td" id="bt_cl_configure_product">
										<div class="col-xs-12">
											<div class="form-group bt_select_product">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon icon-AdminCatalog"></i> </span>
													<input type="text" placeholder="{l s='Start writing a product name' mod='facebookproductad'}" size="5" id="bt_search-cl-p" name="bt_search-cl-p" value="" />
												</div>
											</div>

											<input type="hidden" value="{if !empty($sProductIds)}{$sProductIds|escape:'htmlall':'UTF-8'}{else}{/if}" id="hiddenProductIds" name="hiddenProductIds-cl" />
											<input type="hidden" value="{if !empty($sProductNames)}{$sProductNames|escape:'htmlall':'UTF-8'}{/if}" id="hiddenProductNames" name="hiddenProductNames-cl" />

											<h4>{l s='List of products :' mod='facebookproductad'}</h4>

											<div class="clr_hr"></div>
											<div class="mt-3"></div>

											<div class="col-xs-12">
												<table id="bt_product-list" cellpadding="2" cellspacing="2" class="table table-striped">
													<thead>
														<tr>
															<th>{l s='Product(s)' mod='facebookproductad'}</th>
															<th>{l s='Delete' mod='facebookproductad'}</th>
														</tr>
													</thead>
													<tbody id="bt_excluded-products">
														{if !empty($aProducts)}
															{foreach name=product key=key item=aProduct from=$aProducts}
																<tr>
																	<td><input type="hidden" name="selectProduct[]" value="{$aProduct.id|intval}">{$aProduct.id|intval} - {$aProduct.name|escape:'htmlall':'UTF-8'}</td>
																	<td><span class="icon-trash" style="cursor:pointer;" onclick="oFpa.deleteProduct({$aProduct.id|intval});"></span></td>
																</tr>
															{/foreach}
														{else}
															<tr id="bt_exclude-no-products">
																<td colspan="2">{l s='No product' mod='facebookproductad'}</td>
															</tr>
														{/if}
													</tbody>
												</table>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="row">

						<div class="col-xs-12" id="bt_cl_configure_attribute">
							<div class="form-group">
								<label class="control-label col-xs-3">
									<span><b>{l s='Type of feature you want to use' mod='facebookproductad'}</b></span>
								</label>
								<div class="col-xs-3">
									<select name="dynamic_features_list" id="dynamic_features_list">
										<option value="0"> --- </option>
										{foreach from=$aFeatureAvailable item=feature}
											{if !empty($iFeatureId)}
												<option value="{$feature.id_feature|intval}" {if $feature.id_feature == $iFeatureId} selected="selected" {/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
											{else}
												<option value="{$feature.id_feature|intval}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
											{/if}
										{/foreach}
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_last_order">
							<label class="control-label col-xs-2">
								<b>{l s='Set your order period' mod='facebookproductad'}</b>
							</label>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='From' mod='facebookproductad'} </span>
									<input type="text" name="bt_dyn_last_order_start" id="bt_dyn_last_order_start" class="date-picker" value="{$sStartDateLastOrdered|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>

							<div class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='To' mod='facebookproductad'} </span>
									<input type="text" name="bt_dyn_last_order_end" id="bt_dyn_last_order_end" class="date-picker" value="{$sEndDateLastOrdered|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_best_sales">
							<div class="form-group">
								<label class="control-label col-xs-3">
									<span><b>{l s='How do you want to define your best sales ?' mod='facebookproductad'}</b></span>
								</label>
								<div class="col-xs-3">
									<select name="dynamic_best_sales_unit" id="dynamic_best_sales_unit">
										{if isset($aCustomBestType.$sCurrentIso)}
											{foreach from=$aCustomBestType.$sCurrentIso key=myKey item=CustomBestType}
												{if !empty($sUnit)}
													<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $sUnit} selected {/if}>{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
												{else}
													<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
												{/if}
											{/foreach}
										{else}
											{foreach from=$aCustomBestType.en key=myKey item=CustomBestType}
												{if !empty($sUnit)}
													<option value="{$myKey|escape:'htmlall':'UTF-8'}" {if $myKey == $sUnit} selected {/if}>{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
												{else}
													<option value="{$myKey|escape:'htmlall':'UTF-8'}">{$CustomBestType|escape:'htmlall':'UTF-8'}</option>
												{/if}
											{/foreach}
										{/if}
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-xs-3">
									<b>{l s='Quantity / Amount from which the product is a best sale' mod='facebookproductad'}</b>
								</label>
								<div class="col-xs-3">
									<div class="input-group">
										<input type="text" name="bt_cl_dyn_amount" id="bt_cl_dyn_amount" value="{$fAmount|escape:'htmlall':'UTF-8'}" />
										<span class="input-group-addon" id="cl_dyn_unit_help"></span>
									</div>
								</div>
							</div>

							<div class="form-group" id="bt_cl_best_sale_from">
								<div class="col-xs-12">
									<div class="alert alert-info">
										{l s='Set the date fields below knowing that' mod='facebookproductad'} :
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you select a start and end date, you will get all best sales for this date range' mod='facebookproductad'}
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you select only a start date, you will get all best sales since this start date' mod='facebookproductad'}
										<br />
										<i class="icon icon-chevron-right"></i>&nbsp;{l s='If you select only an end date, you will get all best sales before this end date' mod='facebookproductad'}
									</div>
								</div>
							</div>


							<div class="form-group" id="bt_cl_best_sale_from">

								<label class="control-label col-xs-3">
									<b>{l s='Set your best sales period' mod='facebookproductad'}</b>
								</label>

								<div class="col-xs-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='From' mod='facebookproductad'} </span>
										<input type="text" name="bt_dyn_best_sale_start" id="bt_dyn_best_sale_start" class="date-picker" value="{$sStartDate|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>

								<div class="col-xs-3">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon icon-calendar"></i> &nbsp; {l s='To' mod='facebookproductad'} </span>
										<input type="text" name="bt_dyn_best_sale_end" id="bt_dyn_best_sale_end" class="date-picker" value="{$sEndDate|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12" id="bt_cl_configure_price_range">

							<div class="alert alert-info">
								{l s='Set your price range (Without Tax)' mod='facebookproductad'}
							</div>
							<label class="control-label col-xs-3"></label>

							<div class="col-xs-3">
								<div class="input-group">
									<span class="input-group-addon"> {l s='Min price' mod='facebookproductad'} </span>
									<input type="text" name="bt_dyn_min_price" id="bt_dyn_min_price" value="{$fPriceMin|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>

							<div class="col-xs-3">
								<div class="input-group">
									<span class="input-group-addon">{l s='Max price' mod='facebookproductad'} </span>
									<input type="text" name="bt_dyn_max_price" id="bt_dyn_max_price" value="{$fPriceMax|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
						</div>
					</div>

					<div class="d-flex justify-content-center">
						<button class="btn btn-success btn-lg mr-2 col-1" onclick="oFpa.form('bt_form-custom-tag', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_custom-tag', 'bt_custom-tag', false, true, oCustomCallBack, 'CustomTag', 'loadingCustomTagDiv');return false;">{if !empty($aTag)}{l s='Modify' mod='facebookproductad'}{else}{l s='Add' mod='facebookproductad'}{/if}</button>
						<button class="btn btn-danger btn-lg col-1" value="{l s='Cancel' mod='facebookproductad'}" onclick="$.fancybox.close();return false;">{l s='Cancel' mod='facebookproductad'}</button>
					</div>
			</form>
		</div>
	</div>
	<div id="loadingCustomTagDiv" style="display: none;">
		<div class="alert alert-info">
			<p style="text-align: center !important;"><img src="{$sLoadingImg|escape:'htmlall':'UTF-8'}" alt="Loading" /></p>
			<div class="clr_20"></div>
			<p style="text-align: center !important;">{l s='Your configuration update is in progress...' mod='facebookproductad'}</p>
		</div>
	</div>
{/if}

{literal}
	<script type="text/javascript">
		$(".date-picker").datepicker({
			dateFormat: 'yy-mm-dd'
		});

		// set all elements for autocomplete
		oFpa.aParamsAutcomplete = {sInputSearch : '#bt_search-cl-p', sExcludeNoProducts : '#bt_exclude-no-products-cl', sExcludeProducts : '#bt_excluded-products-cl', sHiddenProductNames : '#hiddenProductNames-cl' , sHiddenProductIds : '#hiddenProductIds-cl'};
		//autocomplete
		oFpa.autocomplete('{/literal}{$sURI|escape:'javascript':'UTF-8'}&sAction={$aQueryParams.searchProduct.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.searchProduct.type|escape:'htmlall':'UTF-8'}{literal}&isCustomLabel=1', '#bt_search-cl-p' );


		// var for dynamique title
		var sGmcpLabel = '{/literal}{l s='Filter:' mod='facebookproductad'}{literal}';
		var sCurreny = '{/literal}{$sCurrency|escape:'htmlall':'UTF-8'}{literal}';
		var sSelectElem = '';

		oFpa.initShow(aShow);
		oFpaLabel.initForm('bt_cl-type', sGmcpLabel);
		oFpaLabel.displayClElement('bt_cl-type', sGmcpLabel);
	</script>
{/literal}