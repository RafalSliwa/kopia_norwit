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
<script type="text/javascript">
	{literal}
		var oBasicCallBack = [{
				'name': 'displayFeedList',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedList.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedList.type|escape:'htmlall':'UTF-8'}{literal}',
				'toShow': 'bt_feed-list-settings',
				'toHide': 'bt_feed-list-settings',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			},
			{
				'name': 'displayFeed',
				'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
				'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction={/literal}{$aQueryParams.feedDisplay.action|escape:'htmlall':'UTF-8'}{literal}&sType={/literal}{$aQueryParams.feedDisplay.type|escape:'htmlall':'UTF-8'}{literal}',
				'toShow': 'bt_feed-settings',
				'toHide': 'bt_feed-settings',
				'bFancybox': false,
				'bFancyboxActivity': false,
				'sLoadbar': null,
				'sScrollTo': null,
				'oCallBack': {}
			}
		];
	{/literal}
</script>

<div class="bootstrap">
	<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_basics-form" name="bt_basics-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_basics-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_basics-settings', 'bt_basics-settings', false, false, oBasicCallBack, 'Basics', 'loadingBasicsDiv');return false;" {/if}>
		<input type="hidden" name="sAction" value="{$aQueryParams.basic.action|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="sType" value="{$aQueryParams.basic.type|escape:'htmlall':'UTF-8'}" />

		<h3 class="breadcrumb"><i class="icon-heart"></i>&nbsp;{l s='Basic settings' mod='facebookproductad'}</h3>

		{if !empty($bUpdate)}
			{include file="`$sConfirmInclude`"}
		{elseif !empty($aErrors)}
			{include file="`$sErrorInclude`"}
		{/if}

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="{l s='Example: http://www.myshop.com - Even if your shop is located in a sub-directory (e.g. http://www.myshop.com/shop), you should still only enter the fully qualified domain name http://www.myshop.com - DO NOT include a trailing slash (/) at the end' mod='facebookproductad'}"><b>{l s='Your Prestashop\'s URL' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-4 col-lg-2">
				<input type="text" name="bt_link" value="{$sLink|escape:'htmlall':'UTF-8'}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="{l s='Example: http://www.myshop.com - Even if your shop is located in a sub-directory (e.g. http://www.myshop.com/shop), you should still only enter the fully qualified domain name http://www.myshop.com - DO NOT include a trailing slash (/) at the end' mod='facebookproductad'}">&nbsp;</span>
			<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/260" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about shop\'s URL' mod='facebookproductad'}</a>
		</div>

		<div class="form-group" id="id_tag_product">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Choose how you want the product IDs to be built in the feed' mod='facebookproductad'}"><b>{l s='Construction mode of the product IDs in the feed' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-4 col-lg-3">
				<select name="bt_feed-tag-id" id="bt_feed-tag-id" class="col-xs-12 col-md-12 col-lg-12" onchange="javascript: oFpa.changeOptionIdPreferencies('bt_feed-tag-id','tag_id_lang_basic');">
					<option value="tag-id-basic" {if $feedTagId == 'tag-id-basic'}selected="selected" {/if}>
						{l s='Use the IDs of the products in the back-office' mod='facebookproductad'}</option>
					<option value="tag-id-product-ref" {if $feedTagId == 'tag-id-product-ref'}selected="selected" {/if}>
						{l s='Use the product references' mod='facebookproductad'}
					</option>
					<option value="tag-id-ean" {if $feedTagId == 'tag-id-ean'}selected="selected" {/if}>
						{l s='Use the EAN codes' mod='facebookproductad'}
					</option>
				</select>
			</div>
			<span class="icon-question-sign label-tooltip" title="{l s='Choose how you want the product IDs to be built in the feed' mod='facebookproductad'}">&nbsp;</span>
			<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/529" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product ID construction mode' mod='facebookproductad'}</a>
		</div>
		<div {if $feedTagId == 'tag-id-basic'} class="hide" {/if} id="tag_id_warning_not_basic">
			<div class="form-group" id="id_tag_product_warning">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">
					<div class="alert alert-warning">
						{l s='Be careful : if you want to use product references or EAN codes, make sure that this information is filled in for all products (or product combinations) to be exported, and is unique for each of them. If the information is missing, the module will use the ID of the product in the back-office. If you have several products or combinations that have the same reference or code, we recommend that you use the IDs of products in the back-office.' mod='facebookproductad'}
					</div>
				</div>
			</div>
		</div>

		<div {if $feedTagId != 'tag-id-basic'} class="hide" {/if} id="tag_id_lang_basic">
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "NO" if you do not want to include the language information in the product ID prefix' mod='facebookproductad'}"><b>{l s='Include LANG ID as a prefix in product ID?' mod='facebookproductad'}</b></span> :</label>
				<div class="col-xs-12 col-md-5 col-lg-6">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="bt_add_lang_id" id="bt_add_lang_id_on" value="1" {if !empty($bIncludeLangId)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('id_lang_info', 'id_lang_info', null, null, true, true); " />
						<label for="bt_add_lang_id_on" class="radioCheck">
							{l s='Yes' mod='facebookproductad'}
						</label>
						<input type="radio" name="bt_add_lang_id" id="bt_add_lang_id_off" value="0" {if empty($bIncludeLangId)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('id_lang_info', 'id_lang_info', null, null, true, false);" />
						<label for="bt_add_lang_id_off" class="radioCheck">
							{l s='No' mod='facebookproductad'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "NO" if you do not want to include the language information in the product ID prefix' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>&nbsp;
					<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/266" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about language prefix' mod='facebookproductad'}</a>
				</div>
			</div>

			<div class="form-group" id="id_lang_info" {if empty($bIncludeLangId)}style="display: none;" {/if}>
				<label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
				<div class="col-xs-6 col-md-5 col-lg-4">
					<div class="alert alert-warning">
						{l s='Be careful : if you want to use Facebook country or language feeds you must DEACTIVATE this option. Indeed, in order for the use of this kind of feeds to work properly, you must not include a language ID in the product ID since Facebook is in charge of doing the matching based on the location.' mod='facebookproductad'}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-md-3 col-lg-3">
					<span class="label-tooltip" title="{l s='Enter a short prefix for your shop. For example, if your shop is called "Janes\'s Flowers", enter JF. This prefix is mandatory if you export feeds from different shops and must be unique for each shop.' mod='facebookproductad'}"><b>
							{l s='Product ID prefix for your shop' mod='facebookproductad'}</b></span> :
				</label>
				<div class="col-xs-12 col-md-3 col-lg-2">
					<input type="text" name="bt_prefix-id" value="{$sPrefixId|escape:'htmlall':'UTF-8'}" />
				</div>
				<span class="icon-question-sign label-tooltip" title="
						{l s='Enter a short prefix for your shop. For example, if your shop is called "Janes\'s Flowers", enter JF. This prefix is mandatory if you export feeds from different shops and must be unique for each shop.' mod='facebookproductad'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/261" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about shop prefix' mod='facebookproductad'}</a>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
						{l s='This determines how many products are processed per AJAX / CRON cycle. Default is 200. Only increase this value if you have a large shop and run into problems with server limits. Otherwise, leave it at its default 200 value. It should not be higher than 1000 in any case.' mod='facebookproductad'}"><b>
						{l s='Number of products per cycle' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<input type="text" name="bt_ajax-cycle" value="{$iProductPerCycle|intval}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="
						{l s='This determines how many products are processed per AJAX / CRON cycle. Default is 200. Only increase this value if you have a large shop and run into problems with server limits. Otherwise, leave it at its default 200 value. It should not be higher than 1000 in any case.' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
						{l s='Select the size of the product images to export on Facebook. Be sure to follow Facebook\'s guidelines for acceptable image sizes (refer to the Facebook "Product Image Specifications for Catalogs" official guide)' mod='facebookproductad'}"><b>

							{l s='Product image size' mod='facebookproductad'}</b></span> :
				</label>
				<div class="col-xs-12 col-md-3 col-lg-2">
					<select name="bt_image-size">';


							{foreach from=$aImageTypes item=aImgType}


								{if $aImgType.width >= '250' && $aImgType.height >= '250'}
								<option value="{$aImgType.name|escape:'htmlall':'UTF-8'}"
									{if $aImgType.name == $sImgSize}selected="selected"
									{/if}>{$aImgType.name|escape:'htmlall':'UTF-8'}</option>


								{/if}


							{/foreach}
					</select>
				</div>
				<div>
					<span class="icon-question-sign label-tooltip" title="
							{l s='Select the size of the product images to export on Facebook. Be sure to follow Facebook\'s guidelines for acceptable image sizes (refer to the Facebook "Product Image Specifications for Catalogs" official guide)' mod='facebookproductad'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/257" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about image size' mod='facebookproductad'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
							{l s='If you want to export only the product cover image select "NO"' mod='facebookproductad'}"><b>
						{l s='Do you want to export additional images?' mod='facebookproductad'}</b></span> :</label>
			<div class="col-xs-5 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_add_images" id="bt_add_images_on" value="1" {if !empty($bAddImages)}checked="checked" {/if} />
					<label for="bt_add_images_on" class="radioCheck">

						{l s='Yes' mod='facebookproductad'}
					</label>
					<input type="radio" name="bt_add_images" id="bt_add_images_off" value="0" {if empty($bAddImages)}checked="checked" {/if} />
					<label for="bt_add_images_off" class="radioCheck">

						{l s='No' mod='facebookproductad'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
							{l s='If you want to export only the product cover image select "NO"' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span>&nbsp;</span>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
							{l s='Select "NO" if you do not want to export tax in product price' mod='facebookproductad'}"><b>
						{l s='Include Tax in product price?' mod='facebookproductad'}</b></span> :</label>
			<div class="col-xs-12 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_add_product_price_tax" id="bt_add_product_price_tax_on" value="1" {if !empty($bIncludeProductPriceTax)}checked="checked" {/if} />
					<label for="bt_add_product_price_tax_on" class="radioCheck">

						{l s='Yes' mod='facebookproductad'}
					</label>
					<input type="radio" name="bt_add_product_price_tax" id="bt_add_product_price_tax_off" value="0" {if empty($bIncludeProductPriceTax)}checked="checked" {/if} />
					<label for="bt_add_product_price_tax_off" class="radioCheck">

						{l s='No' mod='facebookproductad'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
							{l s='Select "NO" if you do not want to export tax in product price' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>&nbsp;
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
							{l s='Please select the category that is the starting point of your tree view (it\'s usually your root or home category)' mod='facebookproductad'}"><b>

								{l s='Please select your "Home" category' mod='facebookproductad'}</b></span> :
					</label>
					<div class="col-xs-12 col-md-3 col-lg-2">
						<select name="bt_home-cat-id">';


								{foreach from=$aHomeCat item=aCat}
								<option value="{$aCat.id_category|intval}"
									{if $aCat.id_category == $iHomeCatId}selected="selected"
									{/if}>{$aCat.name|escape:'htmlall':'UTF-8'}</option>


								{/foreach}
						</select>
					</div>
					<span class="icon-question-sign label-tooltip" title="
								{l s='Please select the category that is the starting point of your tree view (it\'s usually your root or home category)' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
								{l s='For example "Electronic" or "Clothing". In most cases, the product path will correctly be retreived. But, for security reasons, in case where the product parent category wouldn\'t be found, the module needs to have a replacement value to enter in place of it. This value will then allow you to easily find, in your Business Manager account, the products concerned.' mod='facebookproductad'}"><b>

									{l s='What type of products are you selling ?' mod='facebookproductad'}</b></span> :
						</label>
						<div id="homecat" class="col-xs-12 col-md-3 col-lg-3">


									{foreach from=$aLangs item=aLang}
								<div id="bt_home-cat-name_{$aLang.id_lang|intval}" class="translatable-field row lang-{$aLang.id_lang|intval}"
										{if $aLang.id_lang != $iCurrentLang}style="display:none"
										{/if}>
									<div class="col-xs-9 col-md-9 col-lg-9">
										<input type="text" id="bt_home-cat-name_{$aLang.id_lang|intval}" name="bt_home-cat-name_{$aLang.id_lang|intval}"
										{if !empty($aHomeCatLanguages)}
											{foreach from=$aHomeCatLanguages key=idLang item=sLangTitle}
												{if $idLang == $aLang.id_lang} value="{$sLangTitle|escape:'htmlall':'UTF-8'}"
												{/if}
											{/foreach}
										{/if} />
									</div>
									<div class="col-xs-12 col-md-3 col-lg-3">
										<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">{$aLang.iso_code|escape:'htmlall':'UTF-8'}&nbsp;<i class="icon-caret-down"></i></button>
										<ul class="dropdown-menu">


										{foreach from=$aLangs item=aLang}
												<li><a href="javascript:hideOtherLanguage({$aLang.id_lang|intval});" tabindex="-1">{$aLang.name|escape:'htmlall':'UTF-8'}</a></li>


										{/foreach}
										</ul>
									</div>
								</div>


									{/foreach}
						</div>
						<div>
							<span class="icon-question-sign label-tooltip" title="
									{l s='For example "Electronic" or "Clothing". In most cases, the product path will correctly be retreived. But, for security reasons, in case where the product parent category wouldn\'t be found, the module needs to have a replacement value to enter in place of it. This value will then allow you to easily find, in your Business Manager account, the products concerned.' mod='facebookproductad'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/258" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about product type' mod='facebookproductad'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="
									{l s='If your shop uses multiple currencies, you have to select "Yes" and modify the robot.txt file as explained in our FAQ (see link opposite)' mod='facebookproductad'}"><b>
						{l s='Does your shop use multiple currencies?' mod='facebookproductad'}</b></span> :</label>
			<div class="col-xs-5 col-md-5 col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bt_add-currency" id="bt_add-currency_on" value="1" {if !empty($bAddCurrency)}checked="checked" {/if} />
					<label for="bt_add-currency_on" class="radioCheck">

						{l s='Yes' mod='facebookproductad'}
					</label>
					<input type="radio" name="bt_add-currency" id="bt_add-currency_off" value="0" {if empty($bAddCurrency)}checked="checked" {/if} />
					<label for="bt_add-currency_off" class="radioCheck">

						{l s='No' mod='facebookproductad'}
					</label>
					<a class="slide-button btn"></a>
				</span>
				<span class="label-tooltip" data-toggle="tooltip" title data-original-title="
									{l s='If your shop uses multiple currencies, you have to select "Yes" and modify the robot.txt file as explained in our FAQ (see link opposite)' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/130" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about robot.txt file' mod='facebookproductad'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
									{l s='In most cases, the product condition will correctly be retreived. But, for security reasons, in case where it wouldn\'t be found, the module needs to have a replacement value to enter in place of it. The products concerned will have this condition in your Business Manager account.' mod='facebookproductad'}"><b>
						{l s='In general, what\'s your products condition?' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-2">
				<select name="bt_product-condition">
					<option value="0" {if empty($sCondition)}selected="selected" {/if}>--</option>

					{foreach from=$aAvailableCondition item=aCondition key=sCondName}
					<option value="{$sCondName|escape:'htmlall':'UTF-8'}" {if $sCondition == $sCondName}selected="selected" {/if}>{$aCondition|escape:'htmlall':'UTF-8'}</option>

					{/foreach}
				</select>
			</div>
			<div>
				<span class="icon-question-sign label-tooltip" title="
									{l s='In most cases, the product condition will correctly be retreived. But, for security reasons, in case where it wouldn\'t be found, the module needs to have a replacement value to enter in place of it. The products concerned will have this condition in your Business Manager account.' mod='facebookproductad'}">&nbsp;</span>
				<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/259" target="_blank"><i class="icon icon-link"></i>&nbsp;
					{l s='FAQ about product condition' mod='facebookproductad'}</a>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
									{l s='We advise you to add either the product category or the product brand in your product titles.'  mod='facebookproductad'}"><b>
						{l s='Advanced product name' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<select name="bt_advanced-prod-name" id="bt_advanced-prod-name">
					<option value="0" {if $iAdvancedProductName == 0}selected="selected" {/if}>
						{l s='Just the normal product name' mod='facebookproductad'}</option>
					<option value="1" {if $iAdvancedProductName == 1}selected="selected" {/if}>
						{l s='Current category name + Product name' mod='facebookproductad'}</option>
					<option value="2" {if $iAdvancedProductName == 2}selected="selected" {/if}>
						{l s='Product name + Current category name' mod='facebookproductad'}</option>
					<option value="3" {if $iAdvancedProductName == 3}selected="selected" {/if}>
						{l s='Brand name + Product name' mod='facebookproductad'}</option>
					<option value="4" {if $iAdvancedProductName == 4}selected="selected" {/if}>
						{l s='Product name + Brand name' mod='facebookproductad'}</option>
				</select>
				<br />
				<div class="alert alert-warning" id="bt_info-title-category">

					{l s='Be careful : Facebook requires your product titles to be NO MORE than 150 characters long. So, make sure your titles include less than 150 characters and if they don\'t, change the drag and drop menu value above.' mod='facebookproductad'}
				</div>
				<div class="alert alert-warning" id="bt_info-title-brand">

					{l s='Be careful : Facebook requires your product titles to be NO MORE than 150 characters long. So, make sure your titles include less than 150 characters and if they don\'t, change the drag and drop menu value above.' mod='facebookproductad'}
				</div>
			</div>
			<span class="icon-question-sign label-tooltip" title="
									{l s='We advise you to add either the product category or the product brand in your product titles.' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
									{l s='To avoid that some products are refused by Facebook because of forbidden terms in the title, you can ask the module to remove these terms from the titles in the feed. Enter the exact phrases one after the other by separating them with commas and WITHOUT including spaces between them (spaces within phrases are allowed). Example : word1 word2,word3 will exclude the "word1 word2" exact phrase and the word "word3"' mod='facebookproductad'}"><b>
						{l s='Exclude the following exact phrases from product titles:' mod='facebookproductad'}</b></span></label>
			<div class="col-xs-4 col-md-4 col-lg-5">
				<textarea cols="20" rows="10" name="bt_excluded_words">
						{if !empty($excludedWords)}{$excludedWords|escape:'htmlall':'UTF-8'}
						{/if}</textarea>
			</div>
			<span class="icon-question-sign label-tooltip" title="
									{l s='To avoid that some products are refused by Facebook because of forbidden terms in the title, you can ask the module to remove these terms from the titles in the feed. Enter the exact phrases one after the other by separating them with commas and WITHOUT including spaces between them (spaces within phrases are allowed). Example : word1 word2,word3 will exclude the "word1 word2" exact phrase and the word "word3"' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
									{l s='Facebook will refuse your product feed if your product titles have too many UPPERCASE letters. So if it\'s the case, choose one of the two solutions suggested in the opposite drag and drop menu.'  mod='facebookproductad'}"><b>
						{l s='Do you have too many uppercases in titles?' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<select name="bt_advanced-prod-title" id="bt_advanced-prod-title">
					<option value="0" {if $iAdvancedProductTitle == 0}selected="selected" {/if}>
						{l s='No' mod='facebookproductad'}</option>
					<option value="1" {if $iAdvancedProductTitle == 1}selected="selected" {/if}>
						{l s='Yes: uppercase the first character of each title word' mod='facebookproductad'}</option>
					<option value="2" {if $iAdvancedProductTitle == 2}selected="selected" {/if}>
						{l s='Yes: uppercase the title first character only' mod='facebookproductad'}</option>
				</select>
			</div>
			<span class="icon-question-sign label-tooltip" title="
									{l s='Facebook will refuse your product feed if your product titles have too many UPPERCASE letters. So if it\'s the case, choose one of the two solutions suggested in the opposite drag and drop menu.' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="mt-5"></div>

		<h3>
			{l s='Advanced file security' mod='facebookproductad'}
		</h3>

		<div class="form-group">
			<label class="control-label col-xs-12 col-md-3 col-lg-3">
				<span class="label-tooltip" title="
									{l s='This is a security measure so people from the outside cannot call the feed URL and be able to view your information. We have automatically generated one on install for your convenience.' mod='facebookproductad'}"><b>
						{l s='Your secure token' mod='facebookproductad'}</b></span> :
			</label>
			<div class="col-xs-12 col-md-3 col-lg-3">
				<input type="text" maxlength="32" name="bt_feed-token" id="bt_feed-token" value="{$sFeedToken|escape:'htmlall':'UTF-8'}" />
			</div>
			<span class="icon-question-sign label-tooltip" title="
									{l s='This is a security measure so people from the outside cannot call the feed URL and be able to view your information. We have automatically generated one on install for your convenience.' mod='facebookproductad'}">&nbsp;</span>
		</div>

		<div class="mt-3"></div>
		<div class="clr_hr"></div>
		<div class="mt-3"></div>

		<div class="navbar navbar-default navbar-fixed-bottom text-center">
			<div class="col-xs-12">
				<button class="btn btn-submit" onclick="oFpa.form('bt_basics-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_basics-settings', 'bt_basics-settings', false, false, oBasicCallBack, 'Basics', 'loadingBasicsDiv', false, 2);return false;">
					{l s='Save' mod='facebookproductad'}</button>
			</div>
		</div>

	</form>
</div>


{literal}
<script type="text/javascript">
	//bootstrap components init
	// manage change value for advance protection
	//$("#bt_protection-mode").change(function() {
	$("input [name='bt_protection-mode']").bind($.browser.msie ? 'click' : 'change', function(event) {
		if ($(this).val() == "0") {
			$("#protection_off").show();
		} else {
			$("#protection_off").hide();
		}
	});

	//manage information for info title
	if ($("#bt_advanced-prod-name").val() == "0") {
		$("#bt_info-title-category").hide();
		$("#bt_info-title-brand").hide();
	}
	if ($("#bt_advanced-prod-name").val() == "1" ||
		$("#bt_advanced-prod-name").val() == "2"
	) {
		$("#bt_info-title-category").show();
		$("#bt_info-title-brand").hide();
	}
	if ($("#bt_advanced-prod-name").val() == "3" ||
		$("#bt_advanced-prod-name").val() == "4"
	) {
		$("#bt_info-title-category").hide();
		$("#bt_info-title-brand").show();
	}
	$("#bt_advanced-prod-name").change(function() {
		if ($(this).val() == "0") {
			$("#bt_info-title-category").hide();
			$("#bt_info-title-brand").hide();
		}
		if ($(this).val() == "1" ||
			$(this).val() == "2"
		) {
			$("#bt_info-title-category").show();
			$("#bt_info-title-brand").hide();
		}
		if ($(this).val() == "3" ||
			$(this).val() == "4"
		) {
			$("#bt_info-title-category").hide();
			$("#bt_info-title-brand").show();
						}
					});
				{/literal}
				{if !empty($bAjaxMode)}
					{literal}
						$('.label-tooltip, .help-tooltip').tooltip();
						{/literal}{/if}{literal}
					</script>
				{/literal}