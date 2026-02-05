{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   productlabelsandstickers
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{literal}
	<style type="text/css">
		.dragcontainer{border:1px solid #000;position: relative; overflow:hidden;}
		.dragobject{border: 1px solid #000;position:absolute;}
		.hide{display:none;}
		.language_flags {
			display: none;
			border-radius: 4px;
			border: 1px solid #ccc;
			padding: 5px;
			position: absolute
		}

		.fmm_lang_holder {
			position: relative;
		}

		.fmm_lang_holder img {
			position: relative;
			z-index: 999
		}
	</style>
{/literal}
<script>
	var sticker_id = parseInt("{if isset($sticker_id) AND $sticker_id}{$sticker_id|escape:'htmlall':'UTF-8'}{else}0{/if}");
</script>
<input type="hidden" name="text_status" value="1" />
<input type="hidden" name="fmm_stickers_rules_id"
	value="{if isset($fstickerrule) && $fstickerrule}{$fstickerrule['fmm_stickers_rules_id']|escape:'htmlall':'UTF-8'}{/if}" />
{if $version >= 1.6}<div class="panel-heading"><i class="icon-cogs"></i> {l s='Sticker' mod='productlabelsandstickers'}
</div>{/if}
<div class="form-wrapper">
	{if $version >= 1.6}
		<div class="form-group">
			<div>
				<label class="control-label col-lg-3 required">{l s='Status' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<p class="radio">
						<label for="PQ_STAT_1"><input type="radio" value="1"
								{if isset($fstickerrule['status']) && $fstickerrule['status'] > 0} checked="checked" {/if}
								id="PQ_STAT_1" name="status">{l s='Enable' mod='productlabelsandstickers'}</label>
					</p>
					<p class="radio">
						<label for="PQ_STAT_2"><input type="radio" value="0"
								{if isset($fstickerrule['status']) && $fstickerrule['status'] <= 0} checked="checked" {/if}
								id="PQ_STAT_2" name="status">{l s='Disable' mod='productlabelsandstickers'}</label>
					</p>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3 required">{l s='Sticker Name' mod='productlabelsandstickers'}</label>
			<div class="col-lg-6 ">
				<input type="text" size="60" value="{$sticker_name|escape:'htmlall':'UTF-8'}" id="sticker_name"
					name="sticker_name">
			</div>
		</div>
		<div class="clearfix"></div>

		<div class="form-group">
			<label class="control-label col-lg-3 required">{l s='Visibility' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9">
				<div class="col-lg-4">
					<label class="control-label col-lg-6">{l s='Home Page:' mod='productlabelsandstickers'}</label>
					<div class="col-lg-5">
						<span class="switch prestashop-switch fixed-width-lg">
							<input id="home_1" name="home" value="1" type="radio" {if ($home > 0)} checked="checked"
								{/if}><label for="home_1">{l s='Yes' mod='productlabelsandstickers'}</label>
							<input id="home_0" name="home" value="0" type="radio" {if ($home <= 0)} checked="checked"
								{/if}><label for="home_0">{l s='No' mod='productlabelsandstickers'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="col-lg-4">
					<label class="control-label col-lg-6">{l s='Product Page:' mod='productlabelsandstickers'}</label>
					<div class="col-lg-5">
						<span class="switch prestashop-switch fixed-width-lg">
							<input id="product_1" name="product" value="1" type="radio" {if ($product > 0)}
								checked="checked" {/if}><label
								for="product_1">{l s='Yes' mod='productlabelsandstickers'}</label>
							<input id="product_0" name="product" value="0" type="radio" {if ($product <= 0)}
								checked="checked" {/if}><label
								for="product_0">{l s='No' mod='productlabelsandstickers'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
				<div class="col-lg-4">
					<label class="control-label col-lg-6">{l s='Product Listings:' mod='productlabelsandstickers'}</label>
					<div class="col-lg-5">
						<span class="switch prestashop-switch fixed-width-lg">
							<input id="listing_1" name="listing" value="1" type="radio" {if ($listing > 0)}
								checked="checked" {/if}><label
								for="listing_1">{l s='Yes' mod='productlabelsandstickers'}</label>
							<input id="listing_0" name="listing" value="0" type="radio" {if ($listing <= 0)}
								checked="checked" {/if}><label
								for="listing_0">{l s='No' mod='productlabelsandstickers'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>


		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Sticker Type' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
					<div class="radio">
						<label><input type="radio" name="sticker_type" id="sticker_type_text" value="text"
								{if $sticker_type == 'text'} checked
								{/if}>{l s='Text' mod='productlabelsandstickers'}</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="sticker_type" id="sticker_type_image" value="image"
								{if $sticker_type == 'image'} checked
								{/if}>{l s='Image' mod='productlabelsandstickers'}</label>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label col-lg-3 required">{l s='Sticker Text' mod='productlabelsandstickers'}</label>

			<div class="col-lg-9">
				{assign var=divLangName value='cpara&curren;dd'}
				{foreach from=$languages item=language}
					<div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'} col-lg-8"
						id="cpara_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						style="display: {if $language.id_lang == $current_lang} block{else}none{/if};float: left;">
						<input type="text" id="sticker_text{$language.id_lang|escape:'htmlall':'UTF-8'}"
							name="sticker_text{$language.id_lang|escape:'htmlall':'UTF-8'}"
							value="{$current_object->getFieldTitle($id_sticker, $language.id_lang)|escape:'htmlall':'UTF-8'}" />
					</div>
				{/foreach}
				<div class="col-lg-4 fmm_lang_holder">
					{$module->displayFlags($languages, $current_lang, $divLangName, 'cpara', true)}{* html code *}</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label required">{l s='Color' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
					<input type="text" class="mColorPicker mColorPickerTrigger form-control"
						style="display:inline-block;color:#fff;background-color:{$color|escape:'htmlall':'UTF-8'}"
						id="color_0" value="{$color|escape:'htmlall':'UTF-8'}" name="color" data-hex="true" />
					<span id="icp_color_0" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img
							src="../img/admin/color.png" /></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'} style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label required">{l s='Background Color' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
					<input type="text" class="mColorPicker mColorPickerTrigger form-control"
						style="display:inline-block;color:#fff;background-color:{$bg_color|escape:'htmlall':'UTF-8'}"
						id="color_1" value="{$bg_color|escape:'htmlall':'UTF-8'}" name="bg_color" data-hex="true" /><span
						id="icp_color_1" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img
							src="../img/admin/color.png" /></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label">{l s='Font' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9">
				<select id="font" name="font" class="form-control fixed-width-xxl">
					{if !empty($font)}<option value="{$font|escape:'htmlall':'UTF-8'}" selected="selected">
						{$font|escape:'htmlall':'UTF-8'}</option>{/if}
					<option value="Arial">Arial</option>
					<option value="Open Sans">Open Sans</option>
					<option value="Helvetica">Helvetica</option>
					<option value="sans-serif">sans-serif</option>
				</select>
			</div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'} style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label">{l s='Font Size (Home)' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9">
				<select id="font_size" name="font_size" class="form-control fixed-width-xxl">
					{if $font_size > 0}<option value="{$font_size|escape:'htmlall':'UTF-8'}" selected="selected">
						{$font_size|escape:'htmlall':'UTF-8'}</option>{/if}
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
				</select>
			</div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'} style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label"
				id="font_size_listing">{l s='Font Size (Listing)' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9">
				<select id="font_size_listing" name="font_size_listing" class="form-control fixed-width-xxl">
					{if $font_size_listing > 0}<option value="{$font_size_listing|escape:'htmlall':'UTF-8'}"
						selected="selected">{$font_size_listing|escape:'htmlall':'UTF-8'}</option>{/if}
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
				</select>
			</div>
		</div>

		<div class="form-group sticker_type_text" {if $sticker_type == 'text'} style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="col-lg-3 control-label"
				id="font_size_product">{l s='Font Size (Product)' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9">
				<select id="font_size_product" name="font_size_product" class="form-control fixed-width-xxl">
					{if $font_size_product > 0}<option value="{$font_size_product|escape:'htmlall':'UTF-8'}"
						selected="selected">{$font_size_product|escape:'htmlall':'UTF-8'}</option>{/if}
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
				</select>
			</div>
		</div>

		<div class="form-group sticker_type_image" {if $sticker_type == 'image'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label col-lg-3">{l s='Sticker Image(Optional)' mod='productlabelsandstickers'}</label>
			<div class="col-lg-9 ">
				<input class="btn btn-default" type="file" name="sticker_image" id="sticker_image"
					value="{$sticker_image|escape:'htmlall':'UTF-8'}" />
				{if $sticker_image}
					<br />
					<img src="{$base_uri|escape:'htmlall':'UTF-8'}img/{$sticker_image|escape:'htmlall':'UTF-8'}"
					class="imgm img-thumbnail" />{/if}
				<br />
				<img id="image-preview" class="imgm img-thumbnail hide" width="150" src="">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3 required">{l s='Sticker Alignment' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div id="psl_wrapper">
					<div class="psl_unit" id="lefttop" onclick="posSelect(this,'left','top',0);"></div>
					<div class="psl_unit" id="centertop" onclick="posSelect(this,'center','top',0);"></div>
					<div class="psl_unit" id="righttop" onclick="posSelect(this,'right','top',0);"></div>
					<div class="psl_break"></div>
					<div class="psl_unit" id="leftcenter" onclick="posSelect(this,'left','center',1);"></div>
					<div class="psl_unit" id="centercenter" onclick="posSelect(this,'center','center',1);"></div>
					<div class="psl_unit" id="rightcenter" onclick="posSelect(this,'right','center',1);"></div>
					<div class="psl_break"></div>
					<div class="psl_unit" id="leftbottom" onclick="posSelect(this,'left','bottom',0);"></div>
					<div class="psl_unit" id="centerbottom" onclick="posSelect(this,'center','bottom',0);"></div>
					<div class="psl_unit" id="rightbottom" onclick="posSelect(this,'right','bottom',0);"></div>
					<input type="hidden" id="psl_align_y" name="y_align"
						value="{if isset($y_align) AND !empty($y_align)}{$y_align|escape:'htmlall':'UTF-8'}{else}top{/if}" />
					<input type="hidden" id="psl_align_x" name="x_align"
						value="{if isset($x_align) AND !empty($x_align)}{$x_align|escape:'htmlall':'UTF-8'}{else}right{/if}" />
				</div>
				<div class="help-block pls_help_block">{l s='Click on any box to select' mod='productlabelsandstickers'}
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group axis_distance_top">
			<label
				class="control-label col-lg-3">{l s='Distance from Top(Detail page):' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$y_coordinate_product|escape:'htmlall':'UTF-8'}" placeholder="42"
						id="y_coordinate_product" name="y_coordinate_product">
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block">
					{l s='Fill the distance from top in percentage for Product detail page.' mod='productlabelsandstickers'}
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group axis_distance_top">
			<label
				class="control-label col-lg-3">{l s='Distance from Top(Listing Page):' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$y_coordinate_listing|escape:'htmlall':'UTF-8'}" placeholder="42"
						id="y_coordinate_listing" name="y_coordinate_listing">
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block">
					{l s='Fill the distance from top in percentage for Product Listing page.' mod='productlabelsandstickers'}
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_image" {if $sticker_type == 'image'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label
				class="control-label col-lg-3 required">{l s='Product Page Sticker Size:' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$sticker_size|escape:'htmlall':'UTF-8'}" id="sticker_size"
						name="sticker_size">
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block">{l s='No need to fill for only text stickers.' mod='productlabelsandstickers'}</div>
			</div>
			<div class="clearfix"></div>
		</div>



		<div class="form-group sticker_type_image" {if $sticker_type == 'image'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label
				class="control-label col-lg-3 required">{l s='Listing Page Sticker Size:' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$sticker_size_list|escape:'htmlall':'UTF-8'}"
						id="sticker_size_list" name="sticker_size_list">
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block">{l s='No need to fill for only text stickers.' mod='productlabelsandstickers'}</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group sticker_type_image" {if $sticker_type == 'image'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label
				class="control-label col-lg-3 required">{l s='Home Page Sticker Size:' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$sticker_size_home|escape:'htmlall':'UTF-8'}"
						id="sticker_size_home" name="sticker_size_home">
					<span class="input-group-addon">%</span>
				</div>
				<div class="help-block">{l s='No need to fill for only text stickers.' mod='productlabelsandstickers'}</div>
			</div>
			<div class="clearfix"></div>
		</div>
		{* 
			<div class="form-group">
				<label class=" col-lg-3">{l s='Sticker Opacity:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_opacity|escape:'htmlall':'UTF-8'}" id="sticker_opacity" name="sticker_opacity"> &nbsp;%
					<br/>
				</div>
			</div> *}

		<div class="form-group sticker_type_image" {if $sticker_type == 'image'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label col-lg-3">Sticker Opacity:</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" value="{$sticker_opacity|escape:'htmlall':'UTF-8'}" id="sticker_opacity"
						name="sticker_opacity">
					<span class="input-group-addon"></span>
				</div>
				<div class="help-block">Only for Stickers CSS base: Example : 0.4 </div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Link (Optional):' mod='productlabelsandstickers'}</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" size="60" placeholder="https://www.domain.com"
						value="{$sticker_link|escape:'htmlall':'UTF-8'}" id="url" name="url">
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Start Date(Optional)' mod='productlabelsandstickers'}:</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" class="startdatepicker" value="{$start_date|escape:'htmlall':'UTF-8'}"
						name="start_date">
					<span class="input-group-addon"><img
							src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Expiry Date(Optional)' mod='productlabelsandstickers'}:</label>
			<div class="col-lg-3">
				<div class="input-group">
					<input type="text" class="expirydatepicker" value="{$expiry_date|escape:'htmlall':'UTF-8'}"
						name="expiry_date">
					<span class="input-group-addon"><img
							src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>

		{* shop =======================================================*}
		<div class="form-group">
			<label class="control-label col-lg-3 required">{l s='Select Shop' mod='productlabelsandstickers'}</label>
			<div class="col-lg-6">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="fixed-width-xs"> </th>
							<th class="fixed-width-xs"><span class="title_box">ID</span></th>
							<th>
								<span class="title_box">
									{l s='Store name' mod='productlabelsandstickers'}
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="checkbox" value="0" id="groupBox_1" onclick="selectAllShops(this);"
									class="groupBox" name="shops[]">
							</td>
							<td>0</td>
							<td>
								<label for="groupBox_1">{l s='All' mod='productlabelsandstickers'}</label>
							</td>
						</tr>
						{foreach from=$myshops item=_item}
							<tr>
								<td>
									<input type="checkbox" value="{$_item.id_shop|escape:'htmlall':'UTF-8'}" id="groupBox_2"
										class="groupBox sub_sp" name="shops[]" {if in_array($_item.id_shop, $fstickershop)}
										checked {/if}>
								</td>
								<td>{$_item.id_shop|escape:'htmlall':'UTF-8'}</td>
								<td>
									<label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		{* ========================================================== *}
	{else}
		<fieldset>
			<legend>{l s='Sticker' mod='productlabelsandstickers'}</legend>
			<div>
				<label class="required">{l s='Sticker Name' mod='productlabelsandstickers'}</label>
				<div>
					<input type="text" size="60" value="{$sticker_name|escape:'htmlall':'UTF-8'}" id="sticker_name"
						name="sticker_name">
					<sup>*</sup>
				</div>
			</div>
			<br />

			<div>
				<label class="required">{l s='Sticker Image' mod='productlabelsandstickers'}</label>
				<div>
					<input type="file" name="sticker_image" id="sticker_image" />
					{if $sticker_image}
						<br />
						<img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/{$sticker_image|escape:'htmlall':'UTF-8'}"
						class="imgm img-thumbnail hide margin-form" />{/if}
					<br />
					<img id="image-preview" class="imgm img-thumbnail hide margin-form" width="150" src="">
				</div>
			</div>
			<br />
			<div>
				<label>{l s='Watermark X align' mod='productlabelsandstickers'}</label>
				<div>
					<select id="x_align" name="x_align">
						<option value="left" {if $x_align eq 'left'}selected="selected" {/if}>left</option>
						<option value="right" {if $x_align eq 'right'}selected="selected" {/if}>right</option>
					</select>
					<br />
				</div>
			</div>
			<br />
			<div>
				<label>{l s='Watermark Y align' mod='productlabelsandstickers'}</label>
				<div>
					<select id="y_align" name="y_align">
						<option value="top" {if $y_align eq 'top'}selected="selected" {/if}>top</option>
						<option value="bottom" {if $y_align eq 'bottom'}selected="selected" {/if}>bottom</option>
					</select>
					<br />
				</div>
			</div>
			<br />
			<div class="form-group">
				<label class=" col-lg-3">{l s='Product Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_size|escape:'htmlall':'UTF-8'}" id="sticker_size"
						name="sticker_size"> &nbsp;%
					<br />
				</div>
			</div>
			<br />
			<div class="form-group">
				<label class=" col-lg-3">{l s='Listing Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_size_list|escape:'htmlall':'UTF-8'}"
						id="sticker_size_list" name="sticker_size_list"> &nbsp;%
					<br />
				</div>
			</div>
			<br />
			<div class="form-group">
				<label class="col-lg-3">{l s='Home Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_size_list|escape:'htmlall':'UTF-8'}"
						id="sticker_size_home" name="sticker_size_home"> &nbsp;%
					<br />
				</div>
			</div>
			<br />

			<div class="form-group">
				<label class=" col-lg-3">{l s='Sticker Opacity:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_opacity|escape:'htmlall':'UTF-8'}" id="sticker_opacity"
						name="sticker_opacity"> &nbsp;%
					<br />
				</div>
			</div>


			<br />
			<div class="form-group">
				<label class=" col-lg-3">{l s='Start Date' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" class="startdatepicker" value="{$start_date|escape:'htmlall':'UTF-8'}"
						name="estart_date">
					<br />
				</div>
			</div>
			<br />
			<div class="form-group">
				<label class=" col-lg-3">{l s='Expiry Date' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" class="expirydatepicker" value="{$expiry_date|escape:'htmlall':'UTF-8'}"
						name="expiry_date">
					<br />
				</div>
			</div>
			<br /><br />
		</fieldset>
	{/if}

	<div style="display:none;">
		<label>{l s='Medium Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="medium_default" class="dragcontainer" style="width:58px; height:58px;">
				<div id="md_dragThis" class="dragobject"
					style="width:{$medium_width|escape:'htmlall':'UTF-8'}px; height:{$medium_height|escape:'htmlall':'UTF-8'}px;left:{$medium_x|escape:'htmlall':'UTF-8'}px;top:{$medium_y|escape:'htmlall':'UTF-8'}px;">
				</div>
			</div>
			<input type="hidden" name="medium_x" value="{$medium_x|escape:'htmlall':'UTF-8'}" id="medium_x" />
			<input type="hidden" name="medium_y" value="{$medium_y|escape:'htmlall':'UTF-8'}" id="medium_y" />
		</div>
		<label>{l s='Medium Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$medium_width|escape:'htmlall':'UTF-8'}" id="medium_width"
				name="medium_width" onkeyup="keyupWidth(this.value, '#md_dragThis', 58)" />
			<input type="text" size="10" value="{$medium_height|escape:'htmlall':'UTF-8'}" id="medium_height"
				name="medium_height" onkeyup="keyupHeight(this.value, '#md_dragThis', 58)" />
		</div>

		<label>{l s='Home Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="home_default" class="dragcontainer" style="width:124px; height:124px;">
				<div id="hd_dragThis" class="dragobject"
					style="width:{$home_width|escape:'htmlall':'UTF-8'}px; height:{$home_height|escape:'htmlall':'UTF-8'}px;left:{$home_x|escape:'htmlall':'UTF-8'}px;top:{$home_y|escape:'htmlall':'UTF-8'}px;">
				</div>
			</div>
			<input type="hidden" name="home_x" value="{$home_x|escape:'htmlall':'UTF-8'}" id="home_x" />
			<input type="hidden" name="home_y" value="{$home_y|escape:'htmlall':'UTF-8'}" id="home_y" />
		</div>
		<label>{l s='Home Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$home_width|escape:'htmlall':'UTF-8'}" id="home_width"
				name="home_width" onkeyup="keyupWidth(this.value, '#hd_dragThis', 124)" />
			<input type="text" size="10" value="{$home_height|escape:'htmlall':'UTF-8'}" id="home_height"
				name="home_height" onkeyup="keyupHeight(this.value, '#hd_dragThis', 124)" />
		</div>

		<label>{l s='Large Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="large_default" class="dragcontainer" style="width:264px; height:264px;">
				<div id="ld_dragThis" class="dragobject"
					style="width:{$large_width|escape:'htmlall':'UTF-8'}px; height:{$large_height|escape:'htmlall':'UTF-8'}px;left:{$large_x|escape:'htmlall':'UTF-8'}px;top:{$large_y|escape:'htmlall':'UTF-8'}px;">
				</div>
			</div>
			<input type="hidden" name="large_x" value="{$large_x|escape:'htmlall':'UTF-8'}" id="large_x" />
			<input type="hidden" name="large_y" value="{$large_y|escape:'htmlall':'UTF-8'}" id="large_y" />
		</div>
		<label>{l s='Large Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$large_width|escape:'htmlall':'UTF-8'}" id="large_width"
				name="large_width" onkeyup="keyupWidth(this.value, '#ld_dragThis', 264)" />
			<input type="text" size="10" value="{$large_height|escape:'htmlall':'UTF-8'}" id="large_height"
				name="large_height" onkeyup="keyupHeight(this.value, '#ld_dragThis', 264)" />
		</div>
	</div>
	<div style="display:none;">
		<label>{l s='Small Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="small_default" class="dragcontainer" style="width:45px; height:45px;">
				<div id="sd_dragThis" class="dragobject"
					style="width:{$small_width|escape:'htmlall':'UTF-8'}px; height:{$small_height|escape:'htmlall':'UTF-8'}px;left:{$small_x|escape:'htmlall':'UTF-8'}px;top:{$small_y|escape:'htmlall':'UTF-8'}px;">
				</div>
			</div>
			<input type="hidden" name="small_x" value="{$small_x|escape:'htmlall':'UTF-8'}" id="small_x" />
			<input type="hidden" name="small_y" value="{$small_y|escape:'htmlall':'UTF-8'}" id="small_y" />
		</div>
		<label>{l s='Small Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$small_width|escape:'htmlall':'UTF-8'}" id="small_width"
				name="small_width" onkeyup="keyupWidth(this.value, '#sd_dragThis', 45)" />
			<input type="text" size="10" value="{$small_height|escape:'htmlall':'UTF-8'}" id="small_height"
				name="small_height" onkeyup="keyupHeight(this.value, '#sd_dragThis', 45)" />
		</div>
		<label>{l s='Thickbox Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="thickbox_default" class="dragcontainer" style="width:600px; height:600px;">
				<div id="tb_dragThis" class="dragobject"
					style="width:{$thickbox_width|escape:'htmlall':'UTF-8'}px; height:{$thickbox_height|escape:'htmlall':'UTF-8'}px;left:{$thickbox_x|escape:'htmlall':'UTF-8'}px;top:{$thickbox_y|escape:'htmlall':'UTF-8'}px;">
				</div>
			</div>
			<input type="hidden" name="thickbox_x" value="{$thickbox_x|escape:'htmlall':'UTF-8'}" id="thickbox_x" />
			<input type="hidden" name="thickbox_y" value="{$thickbox_y|escape:'htmlall':'UTF-8'}" id="thickbox_y" />
		</div>
		<label>{l s='Thickbox Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$thickbox_width|escape:'htmlall':'UTF-8'}" id="thickbox_width"
				name="thickbox_width" onkeyup="keyupWidth(this.value, '#tb_dragThis', 600)" />
			<input type="text" size="10" value="{$thickbox_height|escape:'htmlall':'UTF-8'}" id="thickbox_height"
				name="thickbox_height" onkeyup="keyupHeight(this.value, '#tb_dragThis', 600)" />
		</div>
	</div>
	<br /><br />
</div>

{* rule section ========================= *}
<div class="panel-heading"><i class="icon-cogs"></i> {l s='Rules Section' mod='productlabelsandstickers'}</div>
<div class="rules-list-container">
	<div class="form-group rules-list">
		<label class="control-label required">{l s='Select Rule' mod='productlabelsandstickers'}</label>
		<div class="">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th> </th>
						<th>
							<span class="title_box">
								{l s='Rule Type' mod='productlabelsandstickers'}
							</span>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<input type="radio" name="rule" value="product" id="rule_product" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'product'}checked{/if} />
						</td>
						<td>
							<label for="rule_product">
								{l s='Has Product' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_onsale" name="rule" value="onsale" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'onsale'}checked{/if} />
						</td>
						<td>
							<label for="rule_onsale">
								{l s='On Sale - Has Specific Prices' mod='productlabelsandstickers'}

							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_outofstock" name="rule" value="outofstock"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'outofstock'}checked{/if} />
						</td>
						<td>
							<label for="rule_outofstock">
								{l s='Out of stock - Not combination base' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_new" name="rule" value="new" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'new'}checked{/if} />
						</td>
						<td>
							<label for="rule_new">
								{l s='New Product' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_bestseller" name="rule" value="bestseller"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'bestseller'}checked{/if} />
						</td>
						<td>
							<label for="rule_bestseller">
								{l s='Bestsellers' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_price_less" name="rule" value="price_less"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'price_less'}checked{/if} />
						</td>
						<td>
							<label for="rule_price_less">
								{l s='Price Less Than' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_price_greater" name="rule" value="price_greater"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'price_greater'}checked{/if} />
						</td>
						<td>
							<label for="rule_price_greater">
								{l s='Price Greater Than' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_reference" name="rule" value="reference"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'reference'}checked{/if} />
						</td>
						<td>
							<label for="rule_reference">
								{l s='Has Reference' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_tag" name="rule" value="tag" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'tag'}checked{/if} />
						</td>
						<td>
							<label for="rule_tag">
								{l s='Has Tag' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_category" name="rule" value="category"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'category'}checked{/if} />
						</td>
						<td>
							<label for="rule_category">
								{l s='Has Category' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_brand" name="rule" value="brand" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'brand'}checked{/if} />
						</td>
						<td>
							<label for="rule_brand">
								{l s='Has Brand/Manufacturer' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_supplier" name="rule" value="supplier"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'supplier'}checked{/if} />
						</td>
						<td>
							<label for="rule_supplier">
								{l s='Has Supplier' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_customer" name="rule" value="customer"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'customer'}checked{/if} />
						</td>
						<td>
							<label for="rule_customer">
								{l s='Has Customer Group' mod='productlabelsandstickers'}
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_stock_g" name="rule" value="stock_g" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'stock_g'}checked{/if} />
						</td>
						<td>
							<label for="rule_stock_g">
								{l s='Stock/Quantity Greater Than' mod='productlabelsandstickers'}
								({l s='Not combination relative' mod='productlabelsandstickers'})
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" id="rule_stock_l" name="rule" value="stock_l" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'stock_l'}checked{/if} />
						</td>
						<td>
							<label for="rule_stock_l">
								{l s='Stock/Quantity Less Than' mod='productlabelsandstickers'}
								({l s='Not combination relative' mod='productlabelsandstickers'})

							</label>
						</td>
					</tr>

					<tr>
						<td>
							<input type="radio" id="rule_condition" name="rule" value="condition"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'condition'}checked{/if} />
						</td>
						<td>
							<label for="rule_condition">
								{l s='Product Condition' mod='productlabelsandstickers'}

							</label>
						</td>
					</tr>

					<tr>
						<td>
							<input type="radio" id="rule_p_type" name="rule" value="p_type" onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'p_type'}checked{/if} />
						</td>
						<td>
							<label for="rule_p_type">
								{l s='Product Type' mod='productlabelsandstickers'}

							</label>
						</td>
					</tr>

					<tr>
						<td>
							<input type="radio" id="rule_p_feature" name="rule" value="p_feature"
								onclick="checkMate(this);"
								{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'p_feature'}checked{/if} />
						</td>
						<td>
							<label for="rule_p_feature">
								{l s='Has Features' mod='productlabelsandstickers'}

							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="rules-item">
		<div class="form-group rules-list" id="rule_brands_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'brand'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Brands/Manufacturers' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{if !isset($brands) || empty($brands)}
								<tr>
									<td>{l s='No brands found.' mod='productlabelsandstickers'}</td>
								</tr>
							{else}
								{foreach from=$brands item=brand}
									<tr>
										<td>
											<input type="checkbox" name="brands[]" value="{$brand.id|escape:'htmlall':'UTF-8'}"
												{if isset($fstickerrule['value']) && in_array($brand.id, explode(',', $fstickerrule['value']))}
												checked="checked" {/if} />
										</td>
										<td>
											{$brand.id|escape:'htmlall':'UTF-8'}
										</td>
										<td>
											{$brand.name|escape:'htmlall':'UTF-8'}
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_supplier_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'supplier'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Suppliers' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{if !isset($suppliers) || empty($suppliers)}
								<tr>
									<td>{l s='No suppliers found.' mod='productlabelsandstickers'}</td>
								</tr>
							{else}
								{foreach from=$suppliers item=brand}
									<tr>
										<td>
											<input type="checkbox" name="suppliers[]" value="{$brand.id|escape:'htmlall':'UTF-8'}"
												{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'supplier' && in_array($brand.id, explode(',', $fstickerrule['value']))}
												checked="checked" {/if} />
										</td>
										<td>
											{$brand.id|escape:'htmlall':'UTF-8'}
										</td>
										<td>
											{$brand.name|escape:'htmlall':'UTF-8'}
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
					<div class="col-lg-9">
						<div class="help-block">
							{l s='*Rule is only for ' mod='productlabelsandstickers'} <i
								style="color: red">{l s='default supplier' mod='productlabelsandstickers'}</i>

						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_condition_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'condition'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Product Condition' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="checkbox" name="conditions[]" value="1"
										{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'condition' && in_array(1, explode(',', $fstickerrule['value']))}
										checked="checked" {/if}>
								</td>
								<td>
									1
								</td>
								<td>
									{l s='NEW' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="checkbox" name="conditions[]" value="2"
										{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'condition' && in_array(2, explode(',', $fstickerrule['value']))}
										checked="checked" {/if} />
								</td>
								<td>
									2
								</td>
								<td>
									{l s='Used' mod='productlabelsandstickers'}
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" name="conditions[]" value="3"
										{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'condition' && in_array(2, explode(',', $fstickerrule['value']))}
										checked="checked" {/if} />
								</td>
								<td>
									3
								</td>
								<td>
									{l s='Refurbished' mod='productlabelsandstickers'}
								</td>
							</tr>
						</tbody>
					</table>
					<div class="col-lg-9">

					</div>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_p_type_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'p_type'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Product Type' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="checkbox" name="p_types[]" value="0"
										{if isset($fstickerrule['value']) && in_array(0, explode(',', $fstickerrule['value']))}
										checked="checked" {/if} />
								</td>
								<td>
									0
								</td>
								<td>
									{l s='Standard product' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="checkbox" name="p_types[]" value="1"
										{if isset($fstickerrule['value']) && in_array(1, explode(',', $fstickerrule['value']))}
										checked="checked" {/if} />
								</td>
								<td>
									1
								</td>
								<td>
									{l s='Pack of products' mod='productlabelsandstickers'}
								</td>
							</tr>

							<tr>
								<td>
									<input type="checkbox" name="p_types[]" value="2"
										{if isset($fstickerrule['value']) && in_array(2, explode(',', $fstickerrule['value']))}
										checked="checked" {/if} />
								</td>
								<td>
									2
								</td>
								<td>
									{l s='Virtual product' mod='productlabelsandstickers'}
								</td>
							</tr>
						</tbody>
					</table>
					<div class="col-lg-9">

					</div>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_feature_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'p_feature'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Features' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{if !isset($allfeatures) || empty($allfeatures)}
								<tr>
									<td>{l s='No Feature found.' mod='productlabelsandstickers'}</td>
								</tr>
							{else}
								{foreach from=$allfeatures item=feature}
									<tr>
										<td>
											<input type="checkbox" name="feature[]" value="{$feature.id_feature_value|escape:'htmlall':'UTF-8'}"
												{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'p_feature' && in_array($feature['id_feature_value'], explode(',', $fstickerrule['value']))}
												checked="checked" {/if} />
										</td>
										<td>
											{$feature.id_feature_value|escape:'htmlall':'UTF-8'}
										</td>
										<td>
											{$feature.name|escape:'htmlall':'UTF-8'}-> {$feature.value|escape:'htmlall':'UTF-8'}
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_category_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'category'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<label class="control-label">{l s='Categories' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{if !isset($categories) || empty($categories)}
								<tr>
									<td>{l s='No brands found.' mod='productlabelsandstickers'}</td>
								</tr>
							{else}
								{foreach from=$categories item=category}
									<tr>
										<td>
											<input type="checkbox" name="category[]" value="{$category.id_category|escape:'htmlall':'UTF-8'}"
												{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'category' && in_array($category.id_category, explode(',', $fstickerrule['value']))}
												checked="checked" {/if} />
										</td>
										<td>
											{$category.id_category|escape:'htmlall':'UTF-8'}
										</td>
										<td>
											{$category.name|escape:'htmlall':'UTF-8'}
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_product_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'product'}style="display: block;"
			{else}style="display: none;" 
			{/if}>
			<br />
			<label class="control-label">{l s='Find Product' mod='productlabelsandstickers'}</label>
			<div>
				<div class="placeholder_holder">
					<input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProducts(this);" />
					<div id="rel_holder"></div>
					<div id="rel_holder_temp">
						<ul>
							{if (!empty($products)) && $products[0] != null}
								{foreach from=$products item=product}
									<li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
										<div class="media-left"><img
												src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
												class="media-object image"></div>
										<div class="media-body media-middle"><span
												class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i
												onclick="relDropThis(this);" class="material-icons delete">clear</i></div>
										<input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}"
											name="related_products[]"
											{if isset($fstickerrule['value']) && in_array($product->id, explode(',', $fstickerrule['value']))}
											checked="checked" {/if} />
									</li>
								{/foreach}
							{/if}
						</ul>
					</div>
				</div>
			</div>
		</div>


		<div class="form-group rules-list" id="rule_product_list_exclude" {if isset($fstickerrule['rule_type']) }
			style="display: block;" {else}style="display: none;" 
			{/if}>
			<br />
			<label class="control-label">{l s='Excluded Products' mod='productlabelsandstickers'}</label>
			<div>
				<div class="placeholder_holder">
					<input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProductsEx(this);" />
					<div id="rel_holder_ex"></div>
					<div id="ex_rel_holder_temp">
						<ul>
							{if (!empty($ex_products)) && $ex_products[0] != null}
								{foreach from=$ex_products item=product}
									<li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
										<div class="media-left"><img
												src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
												class="media-object image"></div>
										<div class="media-body media-middle"><span
												class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i
												onclick="relDropThisEx(this);" class="material-icons delete">clear</i></div>
										<input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}"
											name="excluded_products[]">
									</li>
								{/foreach}
							{/if}
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_value"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'new' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'onsale' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'outofstock' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'bestseller' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'brand' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'supplier' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'category' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'p_feature' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'product' || isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'customer' || isset($fstickerrule['rule_type']) && !isset($fstickerrule['rule_type'])}
			style="display: none" {else} style="display: block" 
			{/if}>
			<label class="control-label required">{l s='Rule Value' mod='productlabelsandstickers'}</label>
			<div class="">
				<input type="text" name="rule_value"
					value="{if !empty($fstickerrule["value"])}{$fstickerrule["value"]|escape:'htmlall':'UTF-8'}{/if}" />
			</div>
			<div class="">
				<div class="help-block">
					{l s='In case of' mod='productlabelsandstickers'} <i
						style="color: red">{l s='reference OR tags' mod='productlabelsandstickers'}</i>
					{l s='DO NOT add space after comma.' mod='productlabelsandstickers'}<br />
				</div>
			</div>
		</div>

		<div class="form-group rules-list" id="rule_customer_list"
			{if isset($fstickerrule['rule_type']) && $fstickerrule['rule_type'] == 'customer'} style="display: block"
			{else} style="display: none" 
			{/if}>
			<label class="control-label">{l s='Customer Groups' mod='productlabelsandstickers'}</label>
			<div>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='ID' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							{if !isset($customers) || empty($customers)}
								<tr>
									<td>{l s='No Customer groups found.' mod='productlabelsandstickers'}</td>
								</tr>
							{else}

								{foreach from=$customers item=customer}
									<tr>
										<td>
											<input type="checkbox" name="customers[]" value="{$customer.id_group|escape:'htmlall':'UTF-8'}"
												{if isset($fstickerrule['value']) && $fstickerrule['rule_type'] == 'customer' && in_array($customer.id_group, explode(',', $fstickerrule['value']))}
												checked="checked" {/if} />
										</td>
										<td>
											{$customer.id_group|escape:'htmlall':'UTF-8'}
										</td>
										<td>
											{$customer.name|escape:'htmlall':'UTF-8'}
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>{* rule-list container end *}
</div> {* rule, list container end *}

{* end rule section ========================= *}

<div class="panel-heading"><i class="icon-cogs"></i> {l s='Hint Section' mod='productlabelsandstickers'}</div>
<div class="form-wrapper">
	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Enable Hints' mod='productlabelsandstickers'}</label>
		<div class="col-lg-6">
			<span class="switch prestashop-switch fixed-width-lg">

				<input id="control_hints_1" name="tip" value="1" type="radio" {if ($hints > 0)} checked="checked"
					{/if}><label for="control_hints_1">{l s='Yes' mod='productlabelsandstickers'}</label>
				<input id="control_hints_0" name="tip" value="0" type="radio" {if ($hints <= 0)} checked="checked"
					{/if}><label for="control_hints_0">{l s='No' mod='productlabelsandstickers'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Hint Text' mod='productlabelsandstickers'}</label>

		<div class="col-lg-9">
			{assign var=divLangName value='cparasec&curren;dd'}
			{foreach from=$languages item=language}
				<div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'} col-lg-8"
					id="cparasec_{$language.id_lang|escape:'htmlall':'UTF-8'}"
					style="display: {if $language.id_lang == $current_lang} block{else}none{/if};float: left;">
					<input type="text" id="tip_txt_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						name="tip_txt_{$language.id_lang|escape:'htmlall':'UTF-8'}"
						value="{$current_object->getFieldHint($id_sticker, $language.id_lang)|escape:'htmlall':'UTF-8'}" />
				</div>
			{/foreach}
			<div class="col-lg-4 fmm_lang_holder">
				{$module->displayFlags($languages, $current_lang, $divLangName, 'cparasec', true)}{* html code *}</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Background Color' mod='productlabelsandstickers'}</label>
		<div class="col-lg-5">
			<div class="input-group">
				<input type="text" class="mColorPicker mColorPickerTrigger form-control"
					style="display:inline-block;{if !empty($tip_bg)}background-color:{$tip_bg|escape:'htmlall':'UTF-8'};{/if}"
					id="color_11" value="{if !empty($tip_bg)}{$tip_bg|escape:'htmlall':'UTF-8'}{/if}" name="tip_bg"
					data-hex="true" /><span id="icp_color_11" class="mColorPickerTrigger input-group-addon"
					data-mcolorpicker="true"><img
						src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/admin/color.png" /></span>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Text Color' mod='productlabelsandstickers'}</label>
		<div class="col-lg-5">
			<div class="input-group">
				<input type="text" class="mColorPicker mColorPickerTrigger form-control"
					style="display:inline-block;{if !empty($tip_color)}background-color:{$tip_color|escape:'htmlall':'UTF-8'};{/if}"
					id="color_21" value="{if !empty($tip_color)}{$tip_color|escape:'htmlall':'UTF-8'}{/if}"
					name="tip_color" data-hex="true" /><span id="icp_color_21"
					class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img
						src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/admin/color.png" /></span>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Hint Open Position' mod='productlabelsandstickers'}</label>
		<div class="col-lg-5">
			<div class="input-group">
				<div class="radio">
					<label><input type="radio" name="tip_pos" id="active_r" value="0" {if $tip_pos <= 0}
							checked="checked" {/if}>{l s='Right' mod='productlabelsandstickers'}</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="tip_pos" id="active_l" value="1" {if $tip_pos > 0}
							checked="checked" {/if}>{l s='Left' mod='productlabelsandstickers'}</label>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<!-- Multishop -->
{if isset($shops) AND $shops}
	<label class="col-lg-3 control-label">{l s='Shop Association' mod='productlabelsandstickers'}</label>
	<div class="form-group">
		<div class="col-lg-6">{$shops}{* html content *}
		</div>
	</div>
	<div class="clearfix"></div>
{/if}
{if $version < 1.6}</fieldset><br />{/if}

<style type="text/css">
	.sticker_type_image {
		display: none;
	}

	.rules-list-container {
		display: flex;
		gap: 2rem;
		justify-content: space-between;
		width: 85%;
		margin-inline: auto;
		margin-bottom: 1rem;
	}

	.rules-list-container label {
		font-weight: 500 !important;
	}

	.rules-list-container .rules-list {
		background-color: #eff2f5;
		padding: 1rem;
		border-radius: 8px;
	}

	.rules-list-container .rules-item {
		flex: 1;
	}

	.rules-list-container .rules-list table thead {
		/*		background-color: blue;*/
		background-color: #2196f3;
	}

	.rules-list-container .rules-list .title_box {
		color: white !important;
	}
</style>

<script language="javascript">
	//<![CDATA[ 
	jQuery(function() {
		$('#md_dragThis').draggable({
			containment: "#medium_default",
			scroll: false,
			drag: function() {
				o = $(this).offset();
				p = $(this).position();
				$('#medium_x').val(p.left);
				$('#medium_y').val(p.top);
			}
		});

		$('#hd_dragThis').draggable({
			containment: "#home_default",
			scroll: false,
			drag: function() {
				o = $(this).offset();
				p = $(this).position();
				$('#home_x').val(p.left);
				$('#home_y').val(p.top);
			}
		});

		$('#sd_dragThis').draggable({
			containment: "#small_default",
			scroll: false,
			drag: function() {
				o = $(this).offset();
				p = $(this).position();
				$('#small_x').val(p.left);
				$('#small_y').val(p.top);
			}
		});

		$('#ld_dragThis').draggable({
			containment: "#large_default",
			scroll: false,
			drag: function() {
				o = $(this).offset();
				p = $(this).position();
				$('#large_x').val(p.left);
				$('#large_y').val(p.top);
			}
		});

		$('#tb_dragThis').draggable({
			containment: "#thickbox_default",
			scroll: false,
			drag: function() {
				o = $(this).offset();
				p = $(this).position();
				$('#thickbox_x').val(p.left);
				$('#thickbox_y').val(p.top);
			}
		});

		$('#sticker_image').on('change', function() {
			readURL(this);
		})
	});

	/* ====================================================== */
	function checkMate(_e) {
		var e_val = jQuery(_e).val();
		if (e_val === 'new' || e_val === 'onsale' || e_val === 'bestseller' || e_val === 'outofstock') {
			jQuery('#rule_value').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_p_type_list').hide();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_product_list_exclude').show();
		} else if (e_val === 'brand') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_brands_list').show();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_p_type_list').hide();
		} else if (e_val === 'supplier') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').show();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_p_type_list').hide();
		} else if (e_val === 'category') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_category_list').show();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').hide();
		} else if (e_val === 'p_feature') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').show();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').hide();
		} else if (e_val === 'product') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list').show();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').hide();
			jQuery('#rule_product_list_exclude').hide();
		} else if (e_val === 'customer') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list_exclude').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_customer_list').show();
			jQuery('#rule_p_type_list').hide();
			jQuery('#rule_condition_list').hide();
		} else if (e_val === 'condition') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').hide();
			jQuery('#rule_condition_list').show();
		} else if (e_val === 'p_type') {
			jQuery('#rule_value').hide();
			jQuery('#rule_value input').attr('disabled', 'disabled');
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').show();
			jQuery('#rule_condition_list').hide();
		} else {
			jQuery('#rule_value').show();
			jQuery('#rule_value input').removeAttr('disabled');
			jQuery('#rule_brands_list').hide();
			jQuery('#rule_supplier_list').hide();
			jQuery('#rule_category_list').hide();
			jQuery('#rule_feature_list').hide();
			jQuery('#rule_product_list').hide();
			jQuery('#rule_product_list_exclude').show();
			jQuery('#rule_condition_list').hide();
			jQuery('#rule_customer_list').hide();
			jQuery('#rule_p_type_list').hide();
		}
		if (e_val === 'reference' || e_val === 'tag' || e_val === 'stock_g' || e_val === 'stock_l') {
			jQuery('#rule_product_list_exclude').hide();
		}
		console.log(e_val);
	}

	function selectAllShops(g) {
		if (jQuery(g).is(":checked")) {
			// jQuery('.sub_sp').attr('disabled', 'disabled');
			jQuery('.sub_sp').prop('checked', true);
		} else {
			// jQuery('.sub_sp').removeAttr('disabled');
			jQuery('.sub_sp').removeAttr('checked');
		}
	}

	/* ====================================================== */

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#image-preview').attr('src', e.target.result).removeClass('hide');

			}

			reader.readAsDataURL(input.files[0]);

		}
	}

	function keyupWidth(value, divId, maxWidth) {
		if (value < maxWidth) {
			$(divId).css("width", value);
		} else {
			alert('invalid width');
		}
	}

	function keyupHeight(value, divId, maxHeight) {
		if (value < maxHeight) {
			$(divId).css("height", value);
		} else {
			alert('invalid height');
		}
	}
	var selected_shops = "{$selected_shops|escape:'htmlall':'UTF-8'}";
	$(document).ready(function() {
		$('input[name="sticker_type"]').change(function() {
			var selectedOption = $('input[name="sticker_type"]:checked').val();

			if (selectedOption == 'text') {
				$('.sticker_type_text').css("display", "block");

				$('.sticker_type_image').css("display", "none")
			} else {
				$('.sticker_type_text').css("display", "none")
				$('.sticker_type_image').css("display", "block")
			}

		});

		$('.displayed_flag').addClass('btn btn-default');
		$('.expirydatepicker, .startdatepicker').datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',
			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText : '{l s='Now' mod='productlabelsandstickers' js=1}',
			closeText 	: '{l s='Done' mod='productlabelsandstickers' js=1}',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: '{l s='Choose Time' mod='productlabelsandstickers' js=1}',
			timeText 	: '{l s='Time' mod='productlabelsandstickers' js=1}',
			hourText 	: '{l s='Hour' mod='productlabelsandstickers' js=1}',
			minuteText 	: '{l s='Minute' mod='productlabelsandstickers' js=1}',
		});

		// shop association
		$(".tree-item-name input[type=checkbox]").each(function() {

			$(this).prop("checked", false);
			$(this).removeClass("tree-selected");
			$(this).parent().removeClass("tree-selected");
			if ($.inArray($(this).val(), selected_shops) != -1) {
				$(this).prop("checked", true);
				$(this).parent().addClass("tree-selected");
				$(this).parents("ul.tree").each(
					function() {
						$(this).children().children().children(".icon-folder-close")
							.removeClass("icon-folder-close")
							.addClass("icon-folder-open");
						$(this).show();
					}
				);
			}

		});
		//Pre-Select Position if edit mode
		if (sticker_id > 0) {
			var psl_y = $('#psl_align_y').val();
			var psl_x = $('#psl_align_x').val();
			var psl_xy_pos = psl_x + psl_y;
			$('#' + psl_xy_pos).addClass('selected');
			if (psl_y === 'center') {
				$('.axis_distance_top').show();
			}
			console.log('XY Pos= ' + psl_xy_pos);
		}
	});
	//Position Selector
	function posSelect(el, x, y, axis) {
		axis = parseInt(axis);
		$('#psl_wrapper div').removeClass('selected');
		$(el).addClass('selected');
		$('#psl_align_y').val(y);
		$('#psl_align_x').val(x);
		if (y === 'center') {
			$('.axis_distance_top').show();
		} else {
			$('.axis_distance_top').hide();
		}
		console.log('X= ' + x + ' Y= ' + y + ' Axis= ' + axis);
	}
	//]]>

	{literal}
		var mod_url = "{/literal}{$action_url|escape:'htmlall':'UTF-8'}{literal}";
		mod_url = mod_url.replace(/&amp;/g, "&");

		function relSelectThis(id, ipa, name, img) {
			if ($('#row_' + id + '_' + ipa).length > 0) {
				showErrorMessage(error_msg);
			} else {
				var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img +
					'" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name +
					'&nbsp;(ID:' + id +
					')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' +
					id + '" name="related_products[]"></li>'
				$('#rel_holder_temp ul').append(draw_html);
			}
		}

		function relSelectThisEx(id, ipa, name, img) {
			if ($('#row_' + id + '_' + ipa).length > 0) {
				showErrorMessage(error_msg);
			} else {
				var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img +
					'" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name +
					'&nbsp;(ID:' + id +
					')</span><i onclick="relDropThisEx(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' +
					id + '" name="excluded_products[]"></li>'
				$('#ex_rel_holder_temp ul').append(draw_html);
			}
		}

		function relClearData() {
			$('#rel_holder').html('');
		}

		function relClearDataEx() {
			$('#rel_holder_ex').html('');
		}


		function relDropThis(e) {
			$(e).parent().parent().remove();
		}

		function relDropThisEx(e) {
			$(e).parent().parent().remove();
		}

		function getRelProducts(e) {
			var search_q_val = $(e).val();
			//controller_url = controller_url+'&q='+search_q_val;
			console.log('getRelProducts');

			if (typeof search_q_val !== 'undefined' && search_q_val) {
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: mod_url, // + '&q=' + search_q_val,
					dataType: 'json',
					data: {
						ajax: 1,
						q: search_q_val,
						action: 'getSearchProducts',
					},
					success: function(data) {
						var quicklink_list =
							'<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
						$.each(data, function(index, value) {
							if (typeof data[index]['id'] !== 'undefined')
								quicklink_list += '<li onclick="relSelectThis(' + data[index]['id'] + ',' +
								data[index]['id_product_attribute'] + ',\'' + data[index]['name'] +
								'\',\'' + data[index]['image'] + '\');"><img src="' + data[index][
									'image'
								] + '" width="60"> ' + data[index]['name'] + '</li>';
						});
						if (data.length == 0) {
							quicklink_list = '';
						}
						$('#rel_holder').html('<ul>' + quicklink_list + '</ul>');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(XMLHttpRequest);
						console.log(textStatus);
						console.log(errorThrown);
					}
				});
			} else {
				$('#rel_holder').html('');
			}
		}


		function getRelProductsEx(e) {
			var search_q_val = $(e).val();
			//controller_url = controller_url+'&q='+search_q_val;
			if (typeof search_q_val !== 'undefined' && search_q_val) {
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: mod_url, // + '&q=' + search_q_val,
					dataType: 'json',
					data: {
						ajax: 1,
						q: search_q_val,
						action: 'getSearchProducts',
					},
					success: function(data) {
						var quicklink_list =
							'<li class="rel_breaker" onclick="relClearDataEx();"><i class="material-icons">&#xE14C;</i></li>';
						$.each(data, function(index, value) {
							if (typeof data[index]['id'] !== 'undefined')
								quicklink_list += '<li onclick="relSelectThisEx(' + data[index]['id'] +
								',' + data[index]['id_product_attribute'] + ',\'' + data[index]['name']
								.replace(/'/g, '') +
							'\',\'' + data[index]['image'] + '\');"><img src="' + data[index][
									'image'
								] + '" width="60"> ' + data[index]['name'] + '</li>';
						});
						if (data.length == 0) {
							quicklink_list = '';
						}
						$('#rel_holder_ex').html('<ul>' + quicklink_list + '</ul>');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(textStatus);
					}
				});
			} else {
				$('#rel_holder_Ex').html('');
			}
		}


		function selectAllShops(g) {
			if (jQuery(g).is(":checked")) {
				// jQuery('.sub_sp').attr('disabled', 'disabled');
				jQuery('.sub_sp').prop('checked', true);
			} else {
				// jQuery('.sub_sp').removeAttr('disabled');
				jQuery('.sub_sp').removeAttr('checked');
			}
		}


	{/literal}
</script>


{literal}
	<style type="text/css">
		#rule_category_list {
			max-height: 600px;
			overflow-y: scroll
		}

		#rel_holder ul {
			position: absolute;
			left: 12px;
			border-radius: 4px;
			top: 40px;
			margin: 0px 0 20%;
			padding: 0;
			background: #fff;
			border: 1px solid #BBCDD2;
			z-index: 999
		}

		#rel_holder ul li {
			list-style: none;
			padding: 5px 10px;
			display: block;
			margin: 0px
		}

		#rel_holder ul li:hover {
			cursor: pointer;
			background: #25B9D7
		}

		#rel_holder ul li.rel_breaker {
			padding: 0px;
			margin: -1px -22px 0 0;
			background: #fff;
			float: right;
			border: 1px solid #BBCDD2;
			border-left: 0px;
			height: 24px;
		}

		#rel_holder ul li.rel_breaker:hover {
			background: #fff;
		}

		.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
		#rel_holder_temp {
			clear: both;
			padding: 10px 0
		}

		#rel_holder_temp ul {
			padding: 0;
			margin: 0
		}

		#rel_holder_temp ul li {
			list-style: none;
			padding: 3px 5px;
			border-radius: 5px;
			margin: 6px 0;
			border: 1px solid #E5E5E5;
			display: block
		}

		#rel_holder_temp ul li div {
			display: inline-block;
			vertical-align: middle
		}

		#rel_holder_temp ul li .media-left {
			width: 8%
		}

		#rel_holder_temp ul li .media-left img {
			max-width: 100%
		}

		#rel_holder_temp ul li .media-body {
			width: 86%;
			margin-left: 5%
		}

		#rel_holder_temp ul li .media-body span {
			float: left;
			font-size: 13px;
			color: #6c868e;
			font-weight: normal;
			white-space: normal !important;
			text-align: left;
			width: 92%
		}

		#rel_holder_temp ul li .media-body i {
			float: right;
			cursor: pointer
		}

		.placeholder_holder {
			position: relative
		}

		.ps_16_specific .material-icons {font-size: 1px;color: #fff;}
		.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
		color: red;
		font-style: normal;
		text-indent: -9999px;
		font-weight: normal;
		line-height: 20px;
		}


		#rel_holder_ex ul {
			position: absolute;
			left: 12px;
			border-radius: 4px;
			top: 40px;
			margin: 0px 0 20%;
			padding: 0;
			background: #fff;
			border: 1px solid #BBCDD2;
			z-index: 999
		}

		#rel_holder_ex ul li {
			list-style: none;
			padding: 5px 10px;
			display: block;
			margin: 0px
		}

		#rel_holder_ex ul li:hover {
			cursor: pointer;
			background: #25B9D7
		}

		#rel_holder_ex ul li.rel_breaker {
			padding: 0px;
			margin: -1px -22px 0 0;
			background: #fff;
			float: right;
			border: 1px solid #BBCDD2;
			border-left: 0px;
			height: 24px;
		}

		#rel_holder_ex ul li.rel_breaker:hover {
			background: #fff;
		}

		.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
		#ex_rel_holder_temp {
			clear: both;
			padding: 10px 0
		}

		#ex_rel_holder_temp ul {
			padding: 0;
			margin: 0
		}

		#ex_rel_holder_temp ul li {
			list-style: none;
			padding: 3px 5px;
			border-radius: 5px;
			margin: 6px 0;
			border: 1px solid #E5E5E5;
			display: block
		}

		#ex_rel_holder_temp ul li div {
			display: inline-block;
			vertical-align: middle
		}

		#ex_rel_holder_temp ul li .media-left {
			width: 8%
		}

		#ex_rel_holder_temp ul li .media-left img {
			max-width: 100%
		}

		#ex_rel_holder_temp ul li .media-body {
			width: 86%;
			margin-left: 5%
		}

		#ex_rel_holder_temp ul li .media-body span {
			float: left;
			font-size: 13px;
			color: #6c868e;
			font-weight: normal;
			white-space: normal !important;
			text-align: left;
			width: 92%
		}

		#ex_rel_holder_temp ul li .media-body i {
			float: right;
			cursor: pointer
		}
	</style>
{/literal}