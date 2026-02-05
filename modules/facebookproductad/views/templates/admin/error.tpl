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
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	{foreach from=$aErrors name=condition key=nKey item=aError}
	<strong>{$aError.msg|escape:'htmlall':'UTF-8'}</strong><br/>
	{if $bDebug == true}
	<ol>
		{if !empty($aError.code)}<li>{l s='Error code' mod='facebookproductad'} : {$aError.code|intval}</li>{/if}
		{if !empty($aError.file)}<li>{l s='Error file' mod='facebookproductad'} : {$aError.file|escape:'htmlall':'UTF-8'}</li>{/if}
		{if !empty($aError.line)}<li>{l s='Error line' mod='facebookproductad'} : {$aError.line|intval}</li>{/if}
		{if !empty($aError.context)}<li>{l s='Error context' mod='facebookproductad'} : {$aError.context|escape:'htmlall':'UTF-8'}</li>{/if}
	</ol>
		{if !empty($aError.howTo)}
			<strong>{$aError.howTo|escape:'htmlall':'UTF-8'}</strong><br/><br/>
			<div class="clr_hr_danger"></div>
			<div class="mt-3"></div>
		{/if}
	{/if}
	{/foreach}
</div>