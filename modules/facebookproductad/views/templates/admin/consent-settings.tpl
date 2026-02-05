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
        var oConsentCallBack = [{}];
    {/literal}
</script>

<form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_consent-form" name="bt_consent-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_consent-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_consent-settings', 'bt_consent-settings', false, false, oConsentCallBack, 'Consent', 'loadingConsentDiv');return false;" {/if}>
    <input type="hidden" name="sAction" value="{$aQueryParams.consent.action|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="sType" value="{$aQueryParams.consent.type|escape:'htmlall':'UTF-8'}" />

    <h3 class="breadcrumb"><i class="fa fa-check"></i>&nbsp;{l s='Consent mode' mod='facebookproductad'}</h3>

    {if !empty($bUpdate)}
        {include file="`$sConfirmInclude`"}
    {elseif !empty($aErrors)}
        {include file="`$sErrorInclude`"}
    {/if}

    <div class="alert alert-info">
        {l s='If you want to condition the use of Facebook cookies on the consent of your visitors, activate the consent mode below.' mod='facebookproductad'}&nbsp;
    </div>

    <div class="clr_20"></div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-md-3  col-lg-2"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" to activate the consent mode. The Facebook cookies will only be used if they are authorized by your visitors.' mod='facebookproductad'}"><b>{l s='Enable consent mode?' mod='facebookproductad'}</b></span></label>
        <div class="col-xs-12 col-md-5 col-lg-6">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="bt_activate_consent" id="bt_activate_consent_on" value="1" {if !empty($bActivateConsent|escape:'htmlall':'UTF-8')}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('consent_config', 'consent_config', null, null, true, true);" />
                <label for="bt_activate_consent_on" class="radioCheck">
                    {l s='Yes' mod='facebookproductad'}
                </label>
                <input type="radio" name="bt_activate_consent" id="bt_activate_consent_off" value="0" {if empty($bActivateConsent|escape:'htmlall':'UTF-8')}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('consent_config', 'consent_config', null, null, true, false);" />
                <label for="bt_activate_consent_off" class="radioCheck">
                    {l s='No' mod='facebookproductad'}
                </label>
                <a class="slide-button btn"></a>
            </span>
            <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" to activate the consent mode. The Facebook cookies will only be used if they are authorized by your visitors.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
        </div>
    </div>

    <div class="clr_20"></div>

    <div id="consent_config" {if empty($bActivateConsent)}style="display: none;" {/if}>
        {if !empty($bPmCookieBanner)}
            <div class="alert alert-success">
                {l s='We\'ve detected that the module "Advanced Cookie Banner" is installed. Your two modules are now synchronized to respect your visitor preferences.' mod='facebookproductad'}
            </div>
        {else}
            <div class="form-group">
                <label class="control-label col-xs-12 col-md-4 col-lg-2"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" if you use the Axeptio module' mod='facebookproductad'}"><b>{l s='Do you use the Axeptio module?' mod='facebookproductad'}</b></span></label>
                <div class="col-xs-12 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="bt_activate_axeptio" id="bt_activate_axeptio_on" value="1" {if !empty($bActivateAxeptio|escape:'htmlall':'UTF-8')}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('axeptio_config', 'axeptio_config', null, null, true, false);oFpa.changeSelect('axeptio_config_info', 'axeptio_config_info', null, null, true, true);" />
                        <label for="bt_activate_axeptio_on" class="radioCheck">
                            {l s='Yes' mod='facebookproductad'}
                        </label>
                        <input type="radio" name="bt_activate_axeptio" id="bt_activate_axeptio_off" value="0" {if empty($bActivateAxeptio|escape:'htmlall':'UTF-8')}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('axeptio_config', 'axeptio_config', null, null, true, true);oFpa.changeSelect('axeptio_config_info', 'axeptio_config_info', null, null, true, false);" />
                        <label for="bt_activate_axeptio_off" class="radioCheck">
                            {l s='No' mod='facebookproductad'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Select "Yes" if you use the Axeptio module' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                </div>
            </div>
            <div id="axeptio_config_info" {if empty($bActivateAxeptio)}style="display: none;" {/if}>
                <div class="alert alert-info">
                    {l s='Our module is fully compliant with the Axeptio module, so you don\'t need to configure anything regarding the collection of consent.' mod='facebookproductad'}
                </div>
            </div>

            <div id="axeptio_config" {if !empty($bActivateAxeptio)}style="display: none;" {/if}>
                <div class="alert alert-info">
                    {l s='Indicate in the option below the ID or CLASS of the button that allows your visitors to authorize the installation of cookies, and save.' mod='facebookproductad'}
                    <br />
                    {l s='For a complete management of your visitors\' consent regarding cookies, discover' mod='facebookproductad'}&nbsp;<a class="badge badge-info" href="https://addons.prestashop.com/24853-advanced-cookie-banner-loi-cookies-mars-2021-cnil-rgpd.html" target="blank"><i class="icon icon-link"></i>&nbsp;{l s='Advanced Cookie Banner' mod='facebookproductad'}</a>
                </div>

                <div class="clr_20"></div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-12 col-md-5 col-lg-2">
                        <span>
                            <strong>{l s='HTML element of the button that allows cookies' mod='facebookproductad'}</strong>
                        </span>
                    </label>
                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-link"></i></span>
                            <input type="text" id="bt_accept_element-id" name="bt_accept_element-id" size="35" value="{if !empty($sAcceptElement)}{$sAcceptElement|escape:'htmlall':'UTF-8'}{/if}" placeholder="# for id and . for class" />
                        </div>
                        <p class="help-block">{l s='Don\'t forget to enter a "#" for an id and a "." for a class' mod='facebookproductad'}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-sm-12 col-md-5 col-lg-2">
                        <span>
                            <strong>{l s='Second HTML element of the button that allows cookies' mod='facebookproductad'}</strong>
                        </span>
                    </label>
                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-link"></i></span>
                            <input type="text" id="bt_accept_element-id_second" name="bt_accept_element-id_second" size="35" value="{if !empty($sAcceptElementSecond)}{$sAcceptElementSecond|escape:'htmlall':'UTF-8'}{/if}" placeholder="# for id and . for class" />
                        </div>
                        <p class="help-block">{l s='Don\'t forget to enter a "#" for an id and a "." for a class. And use it if your cookie banner can allow cookie from a second button' mod='facebookproductad'}</p>
                    </div>
                </div>
            </div>
        {/if}
    </div>

    <div class="mt-3"></div>
    <div class="clr_hr"></div>
    <div class="mt-3"></div>

    <div class="navbar navbar-default navbar-fixed-bottom text-center">
        <div class="col-xs-12">
            <button class="btn btn-submit" onclick="oFpa.form('bt_consent-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_consent-settings', 'bt_consent-settings', false, false, oConsentCallBack, 'Consent', 'loadingConsentDiv', false, 2);return false;">{l s='Save' mod='facebookproductad'}</button>
        </div>
    </div>
</form>