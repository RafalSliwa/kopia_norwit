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
<div class="bootstrap" id="{$sModuleName|escape:'htmlall':'UTF-8'}">
	<form class="col-xs-12 bt_advice-form"  method="post" id="bt_advice-form" name="bt_advice-form" {if $useJs == true}onsubmit="oFpa.form('bt_advice-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_advice-form', 'bt_advice-form', false, false, '' , 'Advice', '');$.fancybox.close();return false;"{/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.adviceUpd.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.adviceUpd.type|escape:'htmlall':'UTF-8'}" />
		<h3 class="text-center">{l s='Feed(s) import in my Business Manager account' mod='facebookproductad'}</h3>
		<div class="mt-3"></div>
		<div class="clr_hr"></div>
		<div class="mt-3"></div>

		<div class="row">
			<div class="col-xs-12 text-center">
				<a class="btn btn-info btn-md" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/228" target="_blank">{l s='FAQ' mod='facebookproductad'}&nbsp;:&nbsp;{l s='How to import my products into a Facebook catalog?' mod='facebookproductad'}</a>
			</div>
		</div>
		<div class="mt-3"></div>
		<div class="clr_hr"></div>
		<div class="mt-3"></div>

		<div class="col-xs-12 text-center">
			<a type="button" name="bt_advice-button" id="bt_advice-button" class="btn btn-success btn-lg pull-left" class="center button" onclick="oFpa.form('bt_advice-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_advice-form', 'bt_advice-form', false, false, '', 'Advice', '', false, 4);$.fancybox.close();return false;" ><i class="fa fa-check">&nbsp;</i>{l s='It\'s OK, my feed has been well imported' mod='facebookproductad'}</a>
			<a type="button" name="no_import" id="" class="btn btn-danger btn-lg pull-right" value="{l s='I haven\'t finished this step yet' mod='facebookproductad'}" class="center button" onclick="$.fancybox.close();return false;" ><i class="fa fa-warning">&nbsp;&nbsp;</i>{l s='I haven\'t finished this step yet' mod='facebookproductad'}</a>
		</div>
	</form>
</div>
