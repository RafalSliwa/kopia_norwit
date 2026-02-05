{*
*
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
<div class="panel product-tab" style=" position: absolute; width: 83%;">
<h3>{l s='Stickers/Labels for this product.' mod='productlabelsandstickers'}</h3>

<table cellpadding="5" cellspacing="10" id="related" class="table" style="width:100%;margin-bottom: 10px;color:#e6e6e6" align="left">
	<thead style="width:100%; ">
		<tr>
			<th width="14%" style="padding-left:3px;"><h1 style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important; margin-left:7px !important">{l s='Select' mod='productlabelsandstickers'}</h1></th>
			<th width="70%" style="padding-left:3px"><h1 style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important;margin-left:7px !important">{l s='Sticker Image' mod='productlabelsandstickers'}</h1></th>
			<th width="16%" style="padding-left:3px"></th>
		</tr>
	</thead>
	<tbody style="width:100%;">
		{section name=stickers loop=$fmm_stickers}
		<tr>
			<td style="padding-left:6px"><input {section name=ex loop=$selected_stickers}{if $fmm_stickers[stickers].sticker_id eq $selected_stickers[ex].sticker_id}checked="checked"{/if}{/section} type="checkbox" name="stickerIds[]" id="stickerIds[]" value="{$fmm_stickers[stickers].sticker_id|escape:'htmlall':'UTF-8'}"  /></td>
			<td><img style="width:100px" alt="{$fmm_stickers[stickers].sticker_name|escape:'htmlall':'UTF-8'}" src="{$base_image|escape:'htmlall':'UTF-8'}{$fmm_stickers[stickers].sticker_image|escape:'htmlall':'UTF-8'}" /></td>
			<td><button class="button btn btn-default" type="button"{if $fmm_stickers[stickers].text_status > 0} onclick="generateFullPreview(this, {$fmm_stickers[stickers].sticker_size|escape:'htmlall':'UTF-8'}, '{$fmm_stickers[stickers].bg_color|escape:'htmlall':'UTF-8'}', '{$fmm_stickers[stickers].color|escape:'htmlall':'UTF-8'}', '{$fmm_stickers[stickers].font|escape:'htmlall':'UTF-8'}', {$fmm_stickers[stickers].font_size|escape:'htmlall':'UTF-8'});"{else} onclick="generatePreview(this, {$fmm_stickers[stickers].sticker_size|escape:'htmlall':'UTF-8'});"{/if}><i class="icon-eye"></i> {l s='Preview' mod='productlabelsandstickers'}</button></td>
		</tr>
		{/section}
		<input type="hidden" name="selected_stickers" id="selected_stickers" value="{$selected_stickers|count|escape:'htmlall':'UTF-8'}" />
	</tbody>
</table>
<div id="bigpic">
	{l s='Preview' mod='productlabelsandstickers'}
	<img src="{$img_url|escape:'htmlall':'UTF-8'}" />
	<div class="floater_bigpic"></div>
</div>
<br /><br />
<h3>{l s='Banners for this product.' mod='productlabelsandstickers'}</h3>
<table cellpadding="5" cellspacing="10" class="table" style="width:100%;margin-bottom: 10px;color:#e6e6e6" align="left">
	<thead style="width:100%;">
		<tr>
			<th width="15%" style="padding-left:3px;"><h1 style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important; margin-left:7px !important">{l s='Select' mod='productlabelsandstickers'}</h1></th>
			<th width="85%" style="padding-left:3px"><h1 style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important;margin-left:7px !important">{l s='Banner' mod='productlabelsandstickers'}</h1></th>
		</tr>
	</thead>
	<tbody style="width:100%;">
		<tr>
			<td style="padding-left:6px"><input id="stickerbanner_0" type="radio" name="stickerbanner" value="0"{if $banners_selected <= 0} checked="checked"{/if} /></td>
			<td><label for="stickerbanner_0">{l s='Disable' mod='productlabelsandstickers'}</label></td>
		</tr>
		{foreach from=$fmm_banners key=i item=banner}
		<tr>
			<td style="padding-left:6px"><input type="radio"{if $banners_selected == $banner.stickersbanners_id} checked="checked"{/if} id="stickerbanner_{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}" name="stickerbanner" value="{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}" /></td>
			<td><label for="stickerbanner_{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}"><div style="padding: 10px; text-align: center; color: {$banner.color|escape:'htmlall':'UTF-8'}; background: {$banner.bg_color|escape:'htmlall':'UTF-8'}; border: 1px solid {$banner.border_color|escape:'htmlall':'UTF-8'};font-family: {$banner.font|escape:'htmlall':'UTF-8'}; font-size: {$banner.font_size|escape:'htmlall':'UTF-8'}px;font-weight: {$banner.font_weight|escape:'htmlall':'UTF-8'};">{$banner.title|escape:'htmlall':'UTF-8'}</div></label></td>
		</tr>
		{/foreach}
	</tbody>
</table>
{if ($_PS_VERSION_ >= 1.6)}
    <div>
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='productlabelsandstickers'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='productlabelsandstickers'}</button>
        &nbsp;
		<button type="submit" name="submitAddproductAndStay" style="margin-right:10px" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='productlabelsandstickers'}</button>
        &nbsp;
	</div>
{/if}	
</div>
{literal}
<style type="text/css">
#bigpic { border: 1px solid #ccc; position: relative; clear: both; text-align: center; width: 380px; margin: 20px auto}
.floater_bigpic { position: absolute; z-index: 1; right: 10px; top: 10px; text-align: center}
.floater_bigpic span {-webkit-border-radius: 8px; -moz-border-radius: 8px;
border-radius: 8px; padding: 5px; display: inline-block}
#bigpic img { max-width: 100%; width: 100%}
</style>
<script>
	function generatePreview(e, x) {
        var row_object = $(e).parent().parent();
		var row_object_img = row_object.find('img').attr('src');
		$('.floater_bigpic').html('<img src="'+row_object_img+'">');
		$('.floater_bigpic').css('width',x+'%');
    }
	function generateFullPreview(e, x, bg, co, fo, fos) {
        var row_object = $(e).parent().parent();
		var row_object_img = row_object.find('img').attr('src');
		var row_object_img_alt = row_object.find('img').attr('alt');
		if (row_object_img.indexOf('.') !== -1) {
            $('.floater_bigpic').html('<span style="background:'+bg+';color:'+co+';font-family:'+fo+';font-size:'+fos+'px;"><img src="'+row_object_img+'">'+row_object_img_alt+'</span>');
			$('.floater_bigpic').css('width',x+'%');
        }
		else
		{
			$('.floater_bigpic').html('<span style="background:'+bg+';color:'+co+';font-family:'+fo+';font-size:'+fos+'px;">'+row_object_img_alt+'</span>');
			$('.floater_bigpic').css('width',x+'%');
		}
		
    }
</script>
{/literal}