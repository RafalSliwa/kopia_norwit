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

{if !empty($sPixel) && !empty($sBusinessId) && !empty($bConfigureStep1) && !empty($bConfigureStep2) && !empty($bConfigureStep3) && !empty($bConfigureStep4)}
	<h3 class="breadcrumb">{l s='Test Events Tool' mod='facebookproductad'}</h3>
	<div class="col-xs-12 alert alert-info">
		<p>{l s='You can test the good reception of the pixel events by Facebook using the Business Manager Test Events Tool. Click on the button below and follow the steps indicated.' mod='facebookproductad'}</p>
		<p><a class="badge badge-info" target="blank" href="https://www.facebook.com/business/help/2040882565969969?id=1205376682832142"><i class="icon icon-link"></i>&nbsp;{l s='Click here' mod='facebookproductad'}</a>&nbsp;{l s='to know more about the Test Events Tool.' mod='facebookproductad'}</p>
	</div>

	<div class="clr_20"></div>

	<div class="row">
		<div class="col-xs-4"></div>
		<div class="col-xs-4">
			<a class="btn btn-lg new-bg col-xs-12" target="_blank" href="https://business.facebook.com/events_manager2/list/pixel/{$sPixel|escape:'htmlall':'UTF-8'}/test_events?business_id={$sBusinessId|escape:'htmlall':'UTF-8'}"><i class="fa fa-stethoscope"></i>&nbsp;{l s='Go to the Facebook Test Events Tool' mod='facebookproductad'}</a>
		</div>
		<div class="col-xs-4"></div>
	</div>

{else}
	<div class="alert alert-warning">
		{l s='Before using the pixel events testing tool you must complete all the steps of the module configuration and export your feeds on Facebook.' mod='facebookproductad'}
	</div>

	{if empty($bConfigureStep4)}
		<div class="alert alert-warning pull-left">
		{l s='The pixel events testing tool can only work if you have exported your feeds on Facebook. If it\'s actually the case, then click on the "Configure" button of the step 4 above and indicate that you have finished importing your feeds to facebook, by clicking on the corresponding button. Return to this tab after refreshing the page.' mod='facebookproductad'}
		</div>
	{/if}
{/if}

