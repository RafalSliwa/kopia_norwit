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
{/if}
<table  cellpadding="2" cellspacing="2" width="400px" class="table table-striped">
	<tr>
		<th class="bt_tr_header center">{l s='Facebook ISO code' mod='facebookproductad'}</th>
		<th class="bt_tr_header center">{l s='Concerned countries' mod='facebookproductad'}</th>
		<th class="bt_tr_header center">{l s='Update my categories' mod='facebookproductad'}</th>
		<th class="bt_tr_header center">{l s='Synch from Facebook' mod='facebookproductad'}</th>
	</tr>
	{foreach from=$aCountryTaxonomies name=taxonomy key=sCode item=aTaxonomy}
		<tr>
			<td class="center">{$sCode|escape:'htmlall':'UTF-8'}</td>
			<td class="center">{$aTaxonomy.countryList|escape:'htmlall':'UTF-8'}</td>
			{if !empty($aTaxonomy.updated)}
				<td class="center" id="gcupd_{$sCode|escape:'htmlall':'UTF-8'}">
					<a href="{$taxonomyController|escape:'htmlall':'UTF-8'}&iLangId={$aTaxonomy.id_lang|intval}&sLangIso={$sCode|escape:'htmlall':'UTF-8'}" class="btn btn-info btn-lg"><span class="icon-pencil"></span></a>
				</td>
			{else}
				<td class="center" id="gcupd_{$sCode|escape:'htmlall':'UTF-8'}">{l s='Please synch first, click there -->' mod='facebookproductad'}</td>
			{/if}
			<td class="center">
				<a id="updateFacebookCategories" class="btn btn-sm btn-default" href="#" onclick="$('#loadingGoogleCatListDiv').show();oFpa.hide('bt_facebook-cat-list');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.facebookCatSync.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.facebookCatSync.type|escape:'htmlall':'UTF-8'}&iLangId={$aTaxonomy.id_lang|intval}&sLangIso={$sCode|escape:'htmlall':'UTF-8'}', 'bt_facebook-cat-list', 'bt_facebook-cat-list', null, null, 'loadingGoogleCatListDiv');"><span class="icon-refresh"></span></a>
				{if !empty($aTaxonomy.currentUpdated)}<span class="icon-ok-sign"></span>{/if}
			</td>
		</tr>
	{/foreach}
</table>