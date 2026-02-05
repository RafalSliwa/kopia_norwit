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
.hide,.language_flags{display:none;}
</style>
{/literal}
<script>
var sticker_id = parseInt("{if isset($sticker_id) AND $sticker_id}{$sticker_id|escape:'htmlall':'UTF-8'}{else}0{/if}");
</script>

	{if $version >= 1.6}<div class="panel-heading"><i class="icon-cogs"></i> {l s='Sticker' mod='productlabelsandstickers'}</div>{/if}
	<div class="form-wrapper">
		{if $version >= 1.6}
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Sticker Name' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6 ">
					<input type="text" size="60" value="{$sticker_name|escape:'htmlall':'UTF-8'}" id="sticker_name" name="sticker_name">
				</div>
			</div><div class="clearfix"></div>
			
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Visibility' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-6">
						<label class="control-label col-lg-4">{l s='Product Page:' mod='productlabelsandstickers'}</label>
						<div class="col-lg-8">
							<span class="switch prestashop-switch fixed-width-lg">
								<input id="product_1" name="product" value="1" type="radio"{if ($product > 0)} checked="checked"{/if}><label for="product_1">{l s='Yes' mod='productlabelsandstickers'}</label>
								<input id="product_0" name="product" value="0" type="radio"{if ($product <= 0)} checked="checked"{/if}><label for="product_0">{l s='No' mod='productlabelsandstickers'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="control-label col-lg-4">{l s='Product Listings:' mod='productlabelsandstickers'}</label>
						<div class="col-lg-8">
							<span class="switch prestashop-switch fixed-width-lg">
								<input id="listing_1" name="listing" value="1" type="radio"{if ($listing > 0)} checked="checked"{/if}><label for="listing_1">{l s='Yes' mod='productlabelsandstickers'}</label>
								<input id="listing_0" name="listing" value="0" type="radio"{if ($listing <= 0)} checked="checked"{/if}><label for="listing_0">{l s='No' mod='productlabelsandstickers'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
					</div>
				</div>
			</div><div class="clearfix"></div>
			
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Sticker Image' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input class="btn btn-default" type="file" name="sticker_image" id="sticker_image" value="{$sticker_image|escape:'htmlall':'UTF-8'}" />
					{if $sticker_image}
					<br/>
					<img src="{$base_uri|escape:'htmlall':'UTF-8'}img/{$sticker_image|escape:'htmlall':'UTF-8'}" class="imgm img-thumbnail"/>{/if}
					<br/>
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
						<input type="hidden" id="psl_align_y" name="y_align" value="{if isset($y_align) AND !empty($y_align)}{$y_align|escape:'htmlall':'UTF-8'}{else}top{/if}" />
						<input type="hidden" id="psl_align_x" name="x_align" value="{if isset($x_align) AND !empty($x_align)}{$x_align|escape:'htmlall':'UTF-8'}{else}right{/if}" />
					</div>
					<div class="help-block pls_help_block">{l s='Click on any box to select' mod='productlabelsandstickers'}</div>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="form-group axis_distance_top">
				<label class="control-label col-lg-3">{l s='Distance from Top(Detail page):' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" value="{$y_coordinate_product|escape:'htmlall':'UTF-8'}" placeholder="42" id="y_coordinate_product" name="y_coordinate_product">
						<span class="input-group-addon">%</span>
					</div>
					<div class="help-block">{l s='Fill the distance from top in percentage for Product detail page.' mod='productlabelsandstickers'}</div>
				</div><div class="clearfix"></div>
			</div>
			
			<div class="form-group axis_distance_top">
				<label class="control-label col-lg-3">{l s='Distance from Top(Listing Page):' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" value="{$y_coordinate_listing|escape:'htmlall':'UTF-8'}" placeholder="42" id="y_coordinate_listing" name="y_coordinate_listing">
						<span class="input-group-addon">%</span>
					</div>
					<div class="help-block">{l s='Fill the distance from top in percentage for Product Listing page.' mod='productlabelsandstickers'}</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Product Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" value="{$sticker_size|escape:'htmlall':'UTF-8'}" id="sticker_size" name="sticker_size">
						<span class="input-group-addon">%</span>
					</div>
					<div class="help-block">{l s='No need to fill for only text stickers.' mod='productlabelsandstickers'}</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Sticker Opacity:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" value="{$sticker_opacity|escape:'htmlall':'UTF-8'}" id="sticker_opacity" name="sticker_opacity">
						<span class="input-group-addon"></span>
					</div>
					<div class="help-block">{l s='Only for Stickers CSS base: Example : 0.4 ' mod='productlabelsandstickers'}</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Listing Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" value="{$sticker_size_list|escape:'htmlall':'UTF-8'}" id="sticker_size_list" name="sticker_size_list">
						<span class="input-group-addon">%</span>
					</div>
					<div class="help-block">{l s='No need to fill for only text stickers.' mod='productlabelsandstickers'}</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Link (Optional):' mod='productlabelsandstickers'}</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" size="60" placeholder="https://www.domain.com" value="{$sticker_link|escape:'htmlall':'UTF-8'}" id="url" name="url">
					</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Start Date (Optional)' mod='productlabelsandstickers'}:</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" class="startdatepicker" value="{$start_date|escape:'htmlall':'UTF-8'}" name="start_date">
						<span class="input-group-addon"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
					</div>
				</div><div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Expiry Date (Optional)' mod='productlabelsandstickers'}:</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" class="expirydatepicker" value="{$expiry_date|escape:'htmlall':'UTF-8'}" name="expiry_date">
						<span class="input-group-addon"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
					</div>
				</div><div class="clearfix"></div>
			</div>
		{else}
		<fieldset>
			<legend>{l s='Sticker' mod='productlabelsandstickers'}</legend>
			<div>
				<label class="required">{l s='Sticker Name' mod='productlabelsandstickers'}</label>
				<div>
					<input type="text" size="60" value="{$sticker_name|escape:'htmlall':'UTF-8'}" id="sticker_name" name="sticker_name">
					<sup>*</sup>
				</div>
			</div>
			<br />

			<div>
				<label class="required">{l s='Sticker Image' mod='productlabelsandstickers'}</label>
				<div>
					<input type="file" name="sticker_image" id="sticker_image" />
					{if $sticker_image}
					<br/>
					<img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/{$sticker_image|escape:'htmlall':'UTF-8'}" class="imgm img-thumbnail hide margin-form"/>{/if}
					<br/>
					<img id="image-preview" class="imgm img-thumbnail hide margin-form" width="150" src="">
				</div>
			</div>
			<br />
			<div>
				<label >{l s='Watermark X align' mod='productlabelsandstickers'}</label>
				<div>
					<select id="x_align" name="x_align">
						<option value="left" {if $x_align eq 'left'}selected="selected"{/if}>left</option>
						<option value="right" {if $x_align eq 'right'}selected="selected"{/if}>right</option>
					</select>
					<br/>
				</div>
			</div>
			<br />
			<div>
				<label>{l s='Watermark Y align' mod='productlabelsandstickers'}</label>
				<div>
					<select id="y_align" name="y_align">
						<option value="top" {if $y_align eq 'top'}selected="selected"{/if}>top</option>
						<option value="bottom" {if $y_align eq 'bottom'}selected="selected"{/if}>bottom</option>
					</select>
					<br/>
				</div>
			</div>
			<br/>
			<div class="form-group">
				<label class=" col-lg-3">{l s='Product Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_size|escape:'htmlall':'UTF-8'}" id="sticker_size" name="sticker_size"> &nbsp;%
					<br/>
				</div>
			</div>
			<br/>
			<div class="form-group">
				<label class=" col-lg-3">{l s='Sticker Opacity:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_opacity|escape:'htmlall':'UTF-8'}" id="sticker_opacity" name="sticker_opacity"> &nbsp;%
					<br/>
				</div>
			</div>
			<br/>
			<div class="form-group">
				<label class=" col-lg-3">{l s='Listing Page Sticker Size:' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" size="60" value="{$sticker_size_list|escape:'htmlall':'UTF-8'}" id="sticker_size_list" name="sticker_size_list"> &nbsp;%
					<br/>
				</div>
			</div>
			<br/>
			<div class="form-group">
				<label class=" col-lg-3">{l s='Start Date' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" class="startdatepicker" value="{$start_date|escape:'htmlall':'UTF-8'}" name="estart_date">
					<br/>
				</div>
			</div>
			<br/>
			<div class="form-group">
				<label class=" col-lg-3">{l s='Expiry Date' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9 ">
					<input type="text" class="expirydatepicker" value="{$expiry_date|escape:'htmlall':'UTF-8'}" name="expiry_date">
					<br/>
				</div>
			</div>
			<br /><br/>
		</fieldset>
		{/if}

		<div style="display:none;">
		<label>{l s='Medium Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="medium_default" class="dragcontainer" style="width:58px; height:58px;">
				<div id="md_dragThis" class="dragobject" style="width:{$medium_width|escape:'htmlall':'UTF-8'}px; height:{$medium_height|escape:'htmlall':'UTF-8'}px;left:{$medium_x|escape:'htmlall':'UTF-8'}px;top:{$medium_y|escape:'htmlall':'UTF-8'}px;"></div>
			</div>
			<input type="hidden" name="medium_x" value="{$medium_x|escape:'htmlall':'UTF-8'}" id="medium_x" />
			<input type="hidden" name="medium_y" value="{$medium_y|escape:'htmlall':'UTF-8'}" id="medium_y" />
		</div>
		<label>{l s='Medium Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$medium_width|escape:'htmlall':'UTF-8'}" id="medium_width" name="medium_width" onkeyup="keyupWidth(this.value, '#md_dragThis', 58)" />
			<input type="text" size="10" value="{$medium_height|escape:'htmlall':'UTF-8'}" id="medium_height" name="medium_height" onkeyup="keyupHeight(this.value, '#md_dragThis', 58)" />
		</div>
		
		<label>{l s='Home Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="home_default" class="dragcontainer" style="width:124px; height:124px;">
				<div id="hd_dragThis" class="dragobject" style="width:{$home_width|escape:'htmlall':'UTF-8'}px; height:{$home_height|escape:'htmlall':'UTF-8'}px;left:{$home_x|escape:'htmlall':'UTF-8'}px;top:{$home_y|escape:'htmlall':'UTF-8'}px;"></div>
			</div>
			<input type="hidden" name="home_x" value="{$home_x|escape:'htmlall':'UTF-8'}" id="home_x" />
			<input type="hidden" name="home_y" value="{$home_y|escape:'htmlall':'UTF-8'}" id="home_y" />
		</div>
		<label>{l s='Home Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$home_width|escape:'htmlall':'UTF-8'}" id="home_width" name="home_width" onkeyup="keyupWidth(this.value, '#hd_dragThis', 124)" />
			<input type="text" size="10" value="{$home_height|escape:'htmlall':'UTF-8'}" id="home_height" name="home_height" onkeyup="keyupHeight(this.value, '#hd_dragThis', 124)" />
		</div>
		
		<label>{l s='Large Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="large_default" class="dragcontainer" style="width:264px; height:264px;">
				<div id="ld_dragThis" class="dragobject" style="width:{$large_width|escape:'htmlall':'UTF-8'}px; height:{$large_height|escape:'htmlall':'UTF-8'}px;left:{$large_x|escape:'htmlall':'UTF-8'}px;top:{$large_y|escape:'htmlall':'UTF-8'}px;"></div>
			</div>
			<input type="hidden" name="large_x" value="{$large_x|escape:'htmlall':'UTF-8'}" id="large_x" />
			<input type="hidden" name="large_y" value="{$large_y|escape:'htmlall':'UTF-8'}" id="large_y" />
		</div>
		<label>{l s='Large Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$large_width|escape:'htmlall':'UTF-8'}" id="large_width" name="large_width" onkeyup="keyupWidth(this.value, '#ld_dragThis', 264)" />
			<input type="text" size="10" value="{$large_height|escape:'htmlall':'UTF-8'}" id="large_height" name="large_height" onkeyup="keyupHeight(this.value, '#ld_dragThis', 264)" />
		</div>
		</div>
		<div style="display:none;">
		<label>{l s='Small Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="small_default" class="dragcontainer" style="width:45px; height:45px;">
				<div id="sd_dragThis" class="dragobject" style="width:{$small_width|escape:'htmlall':'UTF-8'}px; height:{$small_height|escape:'htmlall':'UTF-8'}px;left:{$small_x|escape:'htmlall':'UTF-8'}px;top:{$small_y|escape:'htmlall':'UTF-8'}px;"></div>
			</div>
			<input type="hidden" name="small_x" value="{$small_x|escape:'htmlall':'UTF-8'}" id="small_x" />
			<input type="hidden" name="small_y" value="{$small_y|escape:'htmlall':'UTF-8'}" id="small_y" />
		</div>
		<label>{l s='Small Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$small_width|escape:'htmlall':'UTF-8'}" id="small_width" name="small_width" onkeyup="keyupWidth(this.value, '#sd_dragThis', 45)" />
			<input type="text" size="10" value="{$small_height|escape:'htmlall':'UTF-8'}" id="small_height" name="small_height" onkeyup="keyupHeight(this.value, '#sd_dragThis', 45)" />
		</div>
		<label>{l s='Thickbox Default' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<div id="thickbox_default" class="dragcontainer" style="width:600px; height:600px;">
				<div id="tb_dragThis" class="dragobject" style="width:{$thickbox_width|escape:'htmlall':'UTF-8'}px; height:{$thickbox_height|escape:'htmlall':'UTF-8'}px;left:{$thickbox_x|escape:'htmlall':'UTF-8'}px;top:{$thickbox_y|escape:'htmlall':'UTF-8'}px;"></div>
			</div>
			<input type="hidden" name="thickbox_x" value="{$thickbox_x|escape:'htmlall':'UTF-8'}" id="thickbox_x" />
			<input type="hidden" name="thickbox_y" value="{$thickbox_y|escape:'htmlall':'UTF-8'}" id="thickbox_y" />
		</div>
		<label>{l s='Thickbox Default Dimentions' mod='productlabelsandstickers'}</label>
		<div class="form-group margin-form">
			<input type="text" size="10" value="{$thickbox_width|escape:'htmlall':'UTF-8'}" id="thickbox_width" name="thickbox_width" onkeyup="keyupWidth(this.value, '#tb_dragThis', 600)" />
			<input type="text" size="10" value="{$thickbox_height|escape:'htmlall':'UTF-8'}" id="thickbox_height" name="thickbox_height" onkeyup="keyupHeight(this.value, '#tb_dragThis', 600)" />
		</div>
		</div>
		<br /><br/>
	</div>
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Hint Section' mod='productlabelsandstickers'}</div>
	<div class="form-wrapper">
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Enable Hints' mod='productlabelsandstickers'}</label>
			<div class="col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					
					<input id="control_hints_1" name="tip" value="1" type="radio"{if ($hints > 0)} checked="checked"{/if}><label for="control_hints_1">{l s='Yes' mod='productlabelsandstickers'}</label>
					<input id="control_hints_0" name="tip" value="0" type="radio"{if ($hints <= 0)} checked="checked"{/if}><label for="control_hints_0">{l s='No' mod='productlabelsandstickers'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Hint Text' mod='productlabelsandstickers'}</label>
			
			<div class="col-lg-9">
				{assign var=divLangName value='cpara&curren;dd'}
				{foreach from=$languages item=language}
					<div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'} col-lg-8" id="cpara_{$language.id_lang|escape:'htmlall':'UTF-8'}" style="display: {if $language.id_lang == $current_lang} block{else}none{/if};float: left;">
						<input type="text" id="tip_txt_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="tip_txt_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="{$current_object->getFieldHint($id_sticker, $language.id_lang)|escape:'htmlall':'UTF-8'}" />
					</div>
				{/foreach}
				<div class="col-lg-4 fmm_lang_holder">{$module->displayFlags($languages, $current_lang, $divLangName, 'cpara', true)}{* html code *}</div>
			</div><div class="clearfix"></div>
		</div>
		
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Background Color' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
				<input type="text" class="mColorPicker mColorPickerTrigger form-control" style="display:inline-block;{if !empty($tip_bg)}background-color:{$tip_bg|escape:'htmlall':'UTF-8'};{/if}" id="color_1" value="{if !empty($tip_bg)}{$tip_bg|escape:'htmlall':'UTF-8'}{/if}" name="tip_bg" data-hex="true" /><span id="icp_color_1" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/admin/color.png" /></span>
				</div>
			</div><div class="clearfix"></div>
		</div>
		
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Text Color' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
				<input type="text" class="mColorPicker mColorPickerTrigger form-control" style="display:inline-block;{if !empty($tip_color)}background-color:{$tip_color|escape:'htmlall':'UTF-8'};{/if}" id="color_2" value="{if !empty($tip_color)}{$tip_color|escape:'htmlall':'UTF-8'}{/if}" name="tip_color" data-hex="true" /><span id="icp_color_2" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}img/admin/color.png" /></span>
				</div>
			</div><div class="clearfix"></div>
		</div>
		
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Hint Open Position' mod='productlabelsandstickers'}</label>
			<div class="col-lg-5">
				<div class="input-group">
					<div class="radio">
						<label><input type="radio" name="tip_pos" id="active_r" value="0"{if $tip_pos <= 0} checked="checked"{/if}>{l s='Right' mod='productlabelsandstickers'}</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="tip_pos" id="active_l" value="1"{if $tip_pos > 0} checked="checked"{/if}>{l s='Left' mod='productlabelsandstickers'}</label>
					</div>
				</div>
			</div><div class="clearfix"></div>
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
	{if $version < 1.6}</fieldset><br/>{/if}

<script language="javascript">//<![CDATA[ 
jQuery(function() {
	$('#md_dragThis').draggable({
		containment: "#medium_default",
		scroll: false,
		drag: function () {
			o = $(this).offset();
			p = $(this).position();
			$('#medium_x').val(p.left);
			$('#medium_y').val(p.top);
		}
	});
	
	$('#hd_dragThis').draggable({
		containment: "#home_default",
		scroll: false,
		drag: function () {
			o = $(this).offset();
			p = $(this).position();
			$('#home_x').val(p.left);
			$('#home_y').val(p.top);
		}
	});
	
	$('#sd_dragThis').draggable({
		containment: "#small_default",
		scroll: false,
		drag: function () {
			o = $(this).offset();
			p = $(this).position();
			$('#small_x').val(p.left);
			$('#small_y').val(p.top);
		}
	});
	
	$('#ld_dragThis').draggable({
		containment: "#large_default",
		scroll: false,
		drag: function () {
			o = $(this).offset();
			p = $(this).position();
			$('#large_x').val(p.left);
			$('#large_y').val(p.top);
		}
	});
	
	$('#tb_dragThis').draggable({
		containment: "#thickbox_default",
		scroll: false,
		drag: function () {
			o = $(this).offset();
			p = $(this).position();
			$('#thickbox_x').val(p.left);
			$('#thickbox_y').val(p.top);
		}
	});

	$('#sticker_image').on('change', function()
	{
	    readURL(this);
	})
});

function readURL(input)
{
    if (input.files && input.files[0])
    {
        var reader = new FileReader();

        reader.onload = function (e)
        {
            $('#image-preview').attr('src', e.target.result).removeClass('hide');

        }

        reader.readAsDataURL(input.files[0]);

    }
}

function keyupWidth(value, divId, maxWidth)
{
	if(value<maxWidth){
		$(divId).css("width",value);
	}else{
		alert('invalid width');
	}
}
function keyupHeight(value, divId, maxHeight)
{
	if(value < maxHeight){
		$(divId).css("height",value);
	}else{
		alert('invalid height');
	}
}
var selected_shops = "{$selected_shops|escape:'htmlall':'UTF-8'}";
$(document).ready(function()
{
	$('.displayed_flag').addClass('btn btn-default');
	$('.expirydatepicker, .startdatepicker').datepicker({
		prevText 	: '',
		nextText 	: '',
		dateFormat	: 'yy-mm-dd',
		// Define a custom regional settings in order to use PrestaShop translation tools
		currentText : '{l s='Now' mod='productlabelsandstickers' js=1}',
		closeText 	: '{l s='Done' mod='productlabelsandstickers' js=1}',
		ampm 		: false,
		amNames 	: ['AM', 'A'],
		pmNames 	: ['PM', 'P'],
		timeFormat 	: 'hh:mm:ss tt',
		timeSuffix 	: '',
		timeOnlyTitle: '{l s='Choose Time' mod='productlabelsandstickers' js=1}',
		timeText 	: '{l s='Time' mod='productlabelsandstickers' js=1}',
		hourText 	: '{l s='Hour' mod='productlabelsandstickers' js=1}',
		minuteText 	: '{l s='Minute' mod='productlabelsandstickers' js=1}',
	});

	// shop association
    $(".tree-item-name input[type=checkbox]").each(function()
    {

        $(this).prop("checked", false);
        $(this).removeClass("tree-selected");
        $(this).parent().removeClass("tree-selected");
        if ($.inArray($(this).val(), selected_shops) != -1)
            {
                $(this).prop("checked", true);
                $(this).parent().addClass("tree-selected");
                $(this).parents("ul.tree").each(
                    function()
                    {
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
		var psl_xy_pos = psl_x+psl_y;
		$('#'+psl_xy_pos).addClass('selected');
		if (psl_y === 'center') {
			$('.axis_distance_top').show();
		}
		console.log('XY Pos= '+psl_xy_pos);
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
	}
	else {
		$('.axis_distance_top').hide();
	}
	console.log('X= '+x+' Y= '+y+' Axis= '+axis);
}
//]]>
</script>
