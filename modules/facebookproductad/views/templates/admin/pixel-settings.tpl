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
        var oPixelCallback = [{
            'name': 'displayLog',
            'url' : '{/literal}{$sURI|escape:'javascript':'UTF-8'}{literal}',
            'params' : '{/literal}{$sCtrlParamName|escape:'htmlall':'UTF-8'}{literal}={/literal}{$sController|escape:'htmlall':'UTF-8'}{literal}&sAction=display&sType={/literal}{$aQueryParams.log.type|escape:'htmlall':'UTF-8'}{literal}',
            'toShow': 'bt_log-settings',
            'toHide': 'bt_log-settings',
            'bFancybox': false,
            'bFancyboxActivity': false,
            'sLoadbar': null,
            'sScrollTo': null,
            'oCallBack': {}
        }];
    {/literal}
</script>

<div class="bootstrap">
    <form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_pixel-form" name="bt_pixel-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_pixel-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_pixel-settings', 'bt_pixel-settings', false, false, oPixelCallback, 'Pixel', 'loadingPixelDiv');return false;" {/if}>
        <input type="hidden" name="sAction" value="{$aQueryParams.pixel.action|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="sType" value="{$aQueryParams.pixel.type|escape:'htmlall':'UTF-8'}" />


        <ul class="nav nav-tabs" id="myTab pixelTab">
            <li class="active">
                <a data-toggle="tab" href="#pixel"><i class="fa fa-file-code-o"></i>&nbsp;{l s='Pixel code' mod='facebookproductad'}</a>
            </li>
            <li class="nav-item">
                <a data-toggle="tab" href="#conversion"><i class="fa fa-shopping-cart"></i>&nbsp;{l s='Conversion management' mod='facebookproductad'}</a>
            </li>
            <li class="nav-item">
                <a data-toggle="tab" href="#html_element"><i class="fa fa-cogs"></i>&nbsp;{l s='Advanced options' mod='facebookproductad'}</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">

            {if !empty($bUpdate)}
                {include file="`$sConfirmInclude`"}
            {elseif !empty($aErrors)}
                {include file="`$sErrorInclude`"}
            {/if}

            <div class="tab-pane active" id="pixel">

                <div class="clr_30"></div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span><b>{l s='Facebook Pixel ID' mod='facebookproductad'}</b></span>:
                    </label>
                    <div class="col-xs-3">
                        <input type="text" size="5" name="bt_pixel" id="bt_pixel" value="{if !empty($sPixel)}{$sPixel|escape:'htmlall':'UTF-8'}{/if}" />
                    </div>
                    <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/136" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about Facebook Pixel creation' mod='facebookproductad'}</a>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <b>{l s='Activate Pixel?' mod='facebookproductad'}</b>
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_activate_pixel" id="bt_activate_pixel_on" value="1" {if !empty($hasActivatePixel)}checked="checked" {/if} />
                            <label for="bt_activate_pixel_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_activate_pixel" id="bt_activate_pixel_off" value="0" {if empty($hasActivatePixel)}checked="checked" {/if} />
                            <label for="bt_activate_pixel_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span><b>{l s='Business Manager ID' mod='facebookproductad'}</b></span>:
                    </label>
                    <div class="col-xs-3">
                        <input type="text" size="5" name="bt_business_id" id="bt_business_id" value="{if !empty($sBusinessId)}{$sBusinessId|escape:'htmlall':'UTF-8'}{/if}" />
                    </div>
                    <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/253" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about Business Manager ID' mod='facebookproductad'}</a>
                </div>

                <div class="clr_30"></div>


                <div class="alert alert-info">
                    {l s='To get a more accurate estimate of the number of conversions on your shop attributed to your Facebook ads and reach more people on the social network, you can activate the "Advanced matching" option below. This feature allows the pixel to send customer information, in an encrypted and secure manner, along with the detected events. Read' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/499" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='our FAQ' mod='facebookproductad'}</a>&nbsp;{l s='for more information.' mod='facebookproductad'}
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <b>{l s='Enable Advanced matching?' mod='facebookproductad'}</b>
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_advanced_matching" id="bt_advanced_matching_on" value="1" {if !empty($hasAdvancedMatching)}checked="checked" {/if} />
                            <label for="bt_advanced_matching_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_advanced_matching" id="bt_advanced_matching_off" value="0" {if empty($hasAdvancedMatching)}checked="checked" {/if} />
                            <label for="bt_advanced_matching_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="alert alert-info">{l s='If you want to enable sending your marketing data to Facebook directly from your server, enable the conversions API below, then enter your API access token in the field that appears (see our FAQ). Among other things, the conversions API helps you to know more precisely the number of conversions generated by your ads and allows you to better measure your ROI. Read' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/492" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='our FAQ' mod='facebookproductad'}</a>&nbsp;{l s='for more information.' mod='facebookproductad'}</div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <b>{l s='Enable the conversions API?' mod='facebookproductad'}</b>
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_api_conversion" id="bt_api_conversion_on" value="1" {if !empty($useApi)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('use_api', 'use_api', null, null, true, true);" />
                            <label for="bt_api_conversion_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_api_conversion" id="bt_api_conversion_off" value="0" {if empty($useApi)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('use_api', 'use_api', null, null, true, false);" />
                            <label for="bt_api_conversion_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                {if empty($is_curl)}
                    <div class="alert alert-danger">
                        {l s='Attention: Curl is required to use the API mode, please contact your webhoster.' mod='facebookproductad'}
                    </div>
                {/if}

                <div id="use_api" {if empty($useApi)}style="display: none;" {/if}>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-md-3 col-lg-3">
                            <span class="label-tooltip" title="{l s='Generate your API access token through your Pixel settings in your Business Manager account and paste it here' mod='facebookproductad'}"><b>{l s='API Token' mod='facebookproductad'}</b></span>
                        </label>
                        <div class="col-xs-4 col-md-4 col-lg-5">
                            <textarea cols="20" rows="3" name="bt_api_token">{if !empty($tokenApi)}{$tokenApi|escape:'htmlall':'UTF-8'}{/if}</textarea>
                        </div>
                        &nbsp;<span class="icon-question-sign label-tooltip" title="{l s='Generate your API access token through your Pixel settings in your Business Manager account and paste it here' mod='facebookproductad'}"></span>&nbsp;
                        <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/492" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about conversions API' mod='facebookproductad'}</a>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-12 col-md-3 col-lg-3">
                            <b>{l s='Send pageView event via API?' mod='facebookproductad'}</b>
                        </label>
                        <div class="col-xs-12 col-md-5 col-lg-6">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="bt_api_pageview" id="bt_api_pageview_on" value="1" {if !empty($pageViewApi)}checked="checked" {/if} />
                                <label for="bt_api_pageview_on" class="radioCheck">
                                    {l s='Yes' mod='facebookproductad'}
                                </label>
                                <input type="radio" name="bt_api_pageview" id="bt_api_pageview_off" value="0" {if empty($pageViewApi)}checked="checked" {/if} />
                                <label for="bt_api_pageview_off" class="radioCheck">
                                    {l s='No' mod='facebookproductad'}
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-12 col-md-3 col-lg-3">
                            <b>{l s='Enable the test event code API?' mod='facebookproductad'}</b>
                        </label>
                        <div class="col-xs-12 col-md-5 col-lg-6">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="bt_use_test_event_code" id="bt_test_event_code_on" value="1" {if !empty($bUseTestEventCode)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('use_test_event_code', 'use_test_event_code', null, null, true, true);" />
                                <label for="bt_test_event_code_on" class="radioCheck">
                                    {l s='Yes' mod='facebookproductad'}
                                </label>
                                <input type="radio" name="bt_use_test_event_code" id="bt_test_event_code_off" value="0" {if empty($bUseTestEventCode)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('use_test_event_code', 'use_test_event_code', null, null, true, false);" />
                                <label for="bt_test_event_code_off" class="radioCheck">
                                    {l s='No' mod='facebookproductad'}
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>

                    <div id="use_test_event_code" {if empty($bUseTestEventCode)}style="display: none;" {/if}>
                        <div class="form-group">
                            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                                <b>{l s='Test event code' mod='facebookproductad'}</b>
                            </label>
                            <div class="col-xs-4 col-md-4 col-lg-5">
                                <input type="text" name="bt_test_event_code" value="{if !empty($testEventCode)}{$testEventCode|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        {l s='The test event code is a temporary code provided by Facebook when you set up test events in Events Manager. It helps you verify that your server events are being received correctly by Facebook without affecting your actual data. Use this code during implementation and testing phases only, and remove it before going to production.' mod='facebookproductad'} <a class="badge badge-info" href="https://business.facebook.com/business/help/2040882565969969?id=1205376682832142" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Learn more about test event codes' mod='facebookproductad'}</a>
                    </div>

                    <div class="col-xs-12" {if empty($useApi)}style="display: none;" {/if}>
                        <div class="alert alert-warning">{l s='If you get the "Server Sending Invalid Match Key Parameters" warning in your Business Manager, this is due to the use by some of your customers of a cookie blocker like "adblock". Some data (IP, state, country, etc...) are then incorrectly formatted according to Facebook guidelines. These are only warnings and can be ignored. But if you don\'t want to see these warnings anymore, you can suppress the sending of the problematic data by activating the following option. Read' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/496" target="_blank"><i
                                    class="icon icon-link"></i>&nbsp;{l s='our FAQ' mod='facebookproductad'}</a>&nbsp;{l s='for more information.' mod='facebookproductad'}
                        </div>
                    </div>

                    <div class="form-group" {if empty($useApi)}style="display: none;" {/if}>
                        <label class="control-label col-xs-12 col-md-3 col-lg-3">
                            <b>{l s='No longer see warnings due to cookie blockers:' mod='facebookproductad'}</b>
                        </label>
                        <div class="col-xs-12 col-md-5 col-lg-6">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="bt_api_warning" id="bt_api_warning_on" value="1" {if !empty($hasWarning)}checked="checked" {/if} />
                                <label for="bt_api_warning_on" class="radioCheck">
                                    {l s='Yes' mod='facebookproductad'}
                                </label>
                                <input type="radio" name="bt_api_warning" id="bt_api_warning_off" value="0" {if empty($hasWarning)}checked="checked" {/if} />
                                <label for="bt_api_warning_off" class="radioCheck">
                                    {l s='No' mod='facebookproductad'}
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="conversion">
                <div class="mt-3"></div>

                <div class="col-xs-12">
                    <div class="alert alert-info">{l s='The options below allow you to accurately manage the conversion value that will be sent to Facebook when an order is placed. By default, the conversion value sent to Facebook includes taxes, shipping and wrapping fees. But, you may not want to include one or more of these values... In this case, select "No" for the corresponding options below:' mod='facebookproductad'}</div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-4">
                        <b>{l s='Include taxes in conversion value' mod='facebookproductad'}</b>:
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_use-tax" id="bt_use-tax_on" value="1" {if !empty($bUseTax)}checked="checked" {/if} />
                            <label for="bt_use-tax_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_use-tax" id="bt_use-tax_off" value="0" {if empty($bUseTax)}checked="checked" {/if} />
                            <label for="bt_use-tax_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-4">
                        <b>{l s='Include shipping cost in conversion value' mod='facebookproductad'}</b>:
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_use-shipping" id="bt_use-shipping_on" value="1" {if !empty($bUseShipping)}checked="checked" {/if} />
                            <label for="bt_use-shipping_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_use-shipping" id="bt_use-shipping_off" value="0" {if empty($bUseShipping)}checked="checked" {/if} />
                            <label for="bt_use-shipping_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-4">
                        <b>{l s='Include wrapping cost in conversion value' mod='facebookproductad'}</b>:
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_use-wrapping" id="bt_use-wrapping_on" value="1" {if !empty($bUseWrapping)}checked="checked" {/if} />
                            <label for="bt_use-wrapping_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_use-wrapping" id="bt_use-wrapping_off" value="0" {if empty($bUseWrapping)}checked="checked" {/if} />
                            <label for="bt_use-wrapping_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

            </div>
            <div class="tab-pane" id="html_element">
                <div class="mt-3"></div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            {l s='If you don\'t see the "add to wishlist" or "initiate checkout" events in your Facebook Business Manager account after a few hours when you should, it may be because your theme has changed the default HTML elements related to these events. In this case, ask your technical contact to replace the default HTML elements below with your theme\'s custom elements so that tracking can work.' mod='facebookproductad'}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='By default, the module triggers the "add to cart" event only when the "add to cart" button is clicked. But if you also want to trigger the "add to cart" event when the cart page is displayed, set this option to "YES".' mod='facebookproductad'}"><b>{l s='Also trigger the "add to cart" event when the cart page loads:' mod='facebookproductad'}</b></span>
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-6">

                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_track_cart_page" id="bt_track_cart_page_on" value="1" {if !empty($bTrackCartPage)}checked="checked" {/if} />
                            <label for="bt_track_cart_page_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_track_cart_page" id="bt_track_cart_page_off" value="0" {if empty($bTrackCartPage)}checked="checked" {/if} />
                            <label for="bt_track_cart_page_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                        &nbsp;<span class="icon-question-sign label-tooltip" title="{l s='By default, the module triggers the "add to cart" event only when the "add to cart" button is clicked. But if you also want to trigger the "add to cart" event when the cart page is displayed, set this option to "YES".' mod='facebookproductad'}"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span><b>{l s='HTML element for "add to wishlist" event on product page:' mod='facebookproductad'}</b></span>
                    </label>
                    <div class="col-xs-6">
                        <input type="text" size="5" name="bt_code_addtowishlist_product" id="bt_code_addtowishlist_product" value="{if !empty($wishSelectorProd)}{$wishSelectorProd|escape:'htmlall':'UTF-8'}{/if}" />
                    </div>
                    <a class="pull-left btn btn-md btn-info" onclick="$('#bt_code_addtowishlist_product').val('{$aSelectorDefault.wishlist_list|escape:'htmlall':'UTF-8'}')">{l s='Reset' mod='facebookproductad'}</a>
                </div>

                <div class="clr_30"></div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            {l s='The module already implements all the essential events for e-commerce area. However, if you want to track additional events or configure triggering on specific elements of your theme for example, you can do so through the Facebook Event Setup Tool.' mod='facebookproductad'}&nbsp;<a class="badge badge-info" target="blank" href="https://www.facebook.com/business/help/777099232674791?id=1205376682832142"><i class="icon icon-link"></i>&nbsp;{l s='Click here' mod='facebookproductad'}</a>&nbsp;{l s='to know more about the Facebook Event Setup Tool.' mod='facebookproductad'}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4"></div>
                    <div class="col-xs-4">
                        <a class="btn btn-lg new-bg col-xs-12" target="blank" href="https://business.facebook.com/events_manager2/list/pixel/{$sPixel|escape:'htmlall':'UTF-8'}/settings?business_id={$sBusinessId|escape:'htmlall':'UTF-8'}">{l s='Go to the Facebook Event Setup Tool' mod='facebookproductad'}</a>
                    </div>
                    <div class="col-xs-4"></div>
                </div>

                <div class="clr_20"></div>

            </div>
        </div>

        <div class="mt-3"></div>
        <div class="clr_hr"></div>
        <div class="mt-3"></div>

        <div class="navbar navbar-default navbar-fixed-bottom text-center">
            <div class="col-xs-12">
                <button class="btn btn-submit" onclick="oFpa.form('bt_pixel-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_pixel-settings', 'bt_pixel-settings', false, false, oPixelCallback, 'Pixel', 'loadingPixelDiv', false, 1);return false;">{l s='Save' mod='facebookproductad'}</button>
            </div>
        </div>

    </form>
</div>

{literal}
    <script type="text/javascript">
    {/literal}
    {if !empty($bDisplayCustomDomCode)}
        {literal}
            var aShow = ['#bt_dom-custom'];
            var aHide = [''];
            {/literal}{else}{literal}
            var aShow = [''];
            var aHide = ['#bt_dom-custom'];
            {/literal}{/if}{literal}

            oFpa.initHide(aHide);
            oFpa.initShow(aShow);

        {/literal}
        {if !empty($bAjaxMode)}
            {literal}
                $('.label-tooltip, .help-tooltip').tooltip();
                {/literal}{/if}{literal}
            </script>
        {/literal}