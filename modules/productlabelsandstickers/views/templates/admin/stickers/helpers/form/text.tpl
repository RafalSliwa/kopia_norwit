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
<script type="text/javascript" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/js/jquery-ui.js"></script>
{if $version < 1.6}{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}{/if}
<div class="leadin">{block name="leadin"}{/block}</div>
<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&token={$currentToken|escape:'htmlall':'UTF-8'}&addfmm_stickers" class="defaultForm form-horizontal" name="fmm_stickers_form" id="fmm_stickers_form" method="post" enctype="multipart/form-data">
	<input type="hidden" name="sticker_id" value="{if isset($sticker_id) AND $sticker_id}{$sticker_id|intval|escape:'htmlall':'UTF-8'}{/if}"/>
	<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
	<div id="advance_blog_informations" class="cart_rule_tab">
		<div class="panel">
		{include file=$informations}
		
		{if $version >= 1.6}
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminStickers')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='productlabelsandstickers'}</a>
				<button style="margin-bottom:10px" type="submit" value="{l s='Save' mod='productlabelsandstickers'}" class="btn btn-default pull-right" name="submitAddfmm_stickers" id="{$table|escape:'htmlall':'UTF-8'}_form_submit_btn"><i class="process-icon-save"></i> {l s='Save' mod='productlabelsandstickers'}</button>
			</div>
        {else}
	        <div style="text-align:center; margin-top: -3%;margin-right: 50%;">
	            <input style="margin-bottom:10px; padding-left: 1%;padding-right: 1%;" type="submit" value="{l s='Save' mod='productlabelsandstickers'}" class="button" name="submitAddfmm_stickers" id="{$table|escape:'htmlall':'UTF-8'}_form_submit_btn" />
	        </div>
        {/if}
    	</div>
    </div>
</form>
