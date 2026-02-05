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
	{assign var=sep value="\n"}
	{foreach from=$aErrors name=condition key=nKey item=aError}
		{$aError.msg|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}
		{if $bDebug == true}
			{if !empty($aError.code)}{l s='Error code' mod='facebookproductad'} : {$aError.code|intval}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.file)}{l s='Error file' mod='facebookproductad'} : {$aError.file|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.line)}{l s='Error line' mod='facebookproductad'} : {$aError.line|intval}{$sep|escape:'htmlall':'UTF-8'}{/if}
			{if !empty($aError.context)}{l s='Error context' mod='facebookproductad'} : {$aError.context|escape:'htmlall':'UTF-8'}{$sep|escape:'htmlall':'UTF-8'}{/if}
		{/if}
	{/foreach}
{/if}