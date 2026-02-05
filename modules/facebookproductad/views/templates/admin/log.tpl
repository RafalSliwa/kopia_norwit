{*
*
* Dynamic Ads + Log
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
<div class="bootstrap">
    <form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_log-form" name="bt_log-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_log-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_log-settings', 'bt_log-settings', false, false, oBasicCallBack, 'Log', 'loadingLogDiv');return false;" {/if}>
        <input type="hidden" name="sAction" value="{$aQueryParams.log.action|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="sType" value="{$aQueryParams.log.type|escape:'htmlall':'UTF-8'}" />

        <h3 class="breadcrumb"><i class="icon-stethoscope"></i>&nbsp;{l s='Conversion API log' mod='facebookproductad'}</h3>

        {if !empty($bUpdate)}
            {include file="`$sConfirmInclude`"}
        {elseif !empty($aErrors)}
            {include file="`$sErrorInclude`"}
        {/if}

        {if !empty($useApi)}
            <div class="alert alert-info">
                {l s='This section let you check if you have errors on Facebook API call. If you see errors do not hesitate contact our technical support for more information' mod='facebookproductad'}
            </div>
            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-2">
                    <b>{l s='Event type' mod='facebookproductad'}</b>
                </label>
                <div class="col-xs-12 col-md-3 col-lg-4">
                    <select name="bt_event_type">
                        <option value="">{l s='All event' mod='facebookproductad'}</option>
                        {foreach from=$aEventType name=desc key=iKey item=sType}
                            <option value="{$iKey|escape:'htmlall':'UTF-8'}">{$sType|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-2">
                    <span><b>{l s='Event date' mod='facebookproductad'}</b></span>
                </label>
                <div class="col-xs-3">
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon icon-calendar"></i></span>
                            <input type="text" name="bt_event_date_start" id="bt_event_date_start" class="date-picker" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 text-center">
                <button class="btn btn-submit" onclick="oFpa.form('bt_log-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_log-settings', 'bt_log-settings', false, false, oBasicCallBack, 'Log', 'loadingLogDiv', false, 1);return false;"><i class="fa fa-search"></i>&nbsp;{l s='Search' mod='facebookproductad'}</button>
            </div>
        {else}
            <div class="alert alert-info">
                {l s='This section can only be used if you activated API mode in Facebook Pixel menu.' mod='facebookproductad'}
            </div>
        {/if}
    </form>

    {if !empty($dataLog)}
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" scope="col">{l s='Event' mod='facebookproductad'}</th>
                    <th class="text-center" scope="col">{l s='Error message' mod='facebookproductad'}</th>
                    <th class="text-center" scope="col">{l s='Error code' mod='facebookproductad'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$dataLog item=log key=sKey}
                    <tr>
                        <td class="text-center">{$log[1]|escape:'htmlall':'UTF-8'}</td>
                        <td class="text-center"> {$log[0].title|escape:'htmlall':'UTF-8'} - {$log[0].message|escape:'htmlall':'UTF-8'}</td>
                        <td class="text-center"> {$log[0].code|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/foreach}
            <tbody>
        </table>
    {/if}
</div>

<script type="text/javascript">
    $(".date-picker").datepicker({
        dateFormat: 'yy-mm-dd'
    });
</script>