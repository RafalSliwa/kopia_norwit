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

{if !empty($sDisplay) && $sDisplay == 'export'}
    <script type="text/javascript">
        {literal}
            var oFeedSettingsCallBack = [{
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
            }];
        {/literal}
    </script>
{/if}

<div class="bootstrap">
    <form class="form-horizontal col-xs-12" action="{$sURI|escape:'htmlall':'UTF-8'}" method="post" id="bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form" name="bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form" {if $useJs == true}onsubmit="javascript: oFpa.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && $sDisplay == 'export')}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv');return false;" {/if}>
        <input type="hidden" name="sAction" value="{$aQueryParams.feed.action|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="sType" value="{$aQueryParams.feed.type|escape:'htmlall':'UTF-8'}" />
        <input type="hidden" name="sDisplay" id="sDisplay" value="{if !empty($sDisplay)}{$sDisplay|escape:'htmlall':'UTF-8'}{else}export{/if}" />

        {* USE CASE - Export *}
        {if !empty($sDisplay) && $sDisplay == 'export'}
            <h3 class="breadcrumb"><i class="icon-cog"></i>&nbsp;{l s='Export method' mod='facebookproductad'}</h3>

            {if !empty($bUpdate)}
                {include file="`$sConfirmInclude`"}
            {elseif !empty($aErrors)}
                {include file="`$sErrorInclude`"}
            {/if}

            <div {if !empty($bExportMode)}style="display: none;" {/if}>
                {if $iMaxPostVars != false && $iShopCatCount > $iMaxPostVars}
                    <div class="alert alert-warning">
                        {l s='Important note: Be careful, apparently your maximum post variables is limited by your server and your number of categories is higher than your max post variables' mod='facebookproductad'} :<br />
                        <strong>{$iShopCatCount|intval}{l s='categories' mod='facebookproductad'}</strong>&nbsp;{l s='on' mod='facebookproductad'}&nbsp;<strong>{$iMaxPostVars|intval}</strong>&nbsp;{l s='max post variables possible (PHP directive => max_input_vars)' mod='facebookproductad'}<br /><br />
                        <strong>{l s='It is possible that you cannot register properly all your categories, please visit our FAQ on this topic' mod='facebookproductad'}</strong>: <a target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/59">{$faqLink|escape:'htmlall':'UTF-8'}</a>
                    </div>
                {/if}
            </div>

            <div class="form-group" id="optionplus">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='You can choose to export your products by categories or by brands' mod='facebookproductad'}"><b>{l s='Select your export method' mod='facebookproductad'}</b></span> :
                </label>
                <div class="col-xs-12 col-md-3 col-lg-2">
                    <select name="bt_export" id="bt_export">
                        <option value="0" {if empty($bExportMode)}selected="selected" {/if}>{l s='Export by categories' mod='facebookproductad'}</option>
                        <option value="1" {if !empty($bExportMode)}selected="selected" {/if}>{l s='Export by brands' mod='facebookproductad'}</option>
                    </select>
                </div>
                <span class="icon-question-sign label-tooltip" title="{l s='You can choose to export your products by categories or by brands' mod='facebookproductad'}"></span>
            </div>
            {* categories tree *}
            <div id="bt_categories" {if !empty($bExportMode)}style="display: none;" {/if}>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='Select the categories you want to export. You will be able to exclude some products from these selected categories in "Product exclusion rules" tab' mod='facebookproductad'}"><b>{l s='Categories' mod='facebookproductad'}</b></span> :
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-4">
                        <div class="btn-actions">
                            <div class="btn btn-default btn-mini" id="categoryCheck" onclick="return oFpa.selectAll('input.categoryBox', 'check');"><span class="icon-plus-square"></span>&nbsp;{l s='Check All' mod='facebookproductad'}</div> - <div class="btn btn-default btn-mini" id="categoryUnCheck" onclick="return oFpa.selectAll('input.categoryBox', 'uncheck');"><span class="icon-minus-square"></span>&nbsp;{l s='Uncheck All' mod='facebookproductad'}</div>
                            <div class="mt-3"></div>
                        </div>
                        <table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
                            {foreach from=$aFormatCat name=category key=iKey item=aCat}
                                <tr class="alt_row">
                                    <td>
                                        {$aCat.id_category|intval}
                                    </td>
                                    <td>
                                        <input type="checkbox" name="bt_category-box[]" class="categoryBox" id="bt_category-box_{$aCat.iNewLevel|intval}" value="{$aCat.id_category|intval}" {if !empty($aCat.bCurrent)}checked="checked" {/if} />
                                    </td>
                                    <td>
                                        <span class="icon icon-folder{if !empty($aCat.bCurrent)}-open{/if}" style="margin-left: {$aCat.iNewLevel|intval}5px;"></span>&nbsp;&nbsp;<span style="font-size:12px;">{$aCat.name|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                </tr>
                            {/foreach}
                        </table><br /><br />
                    </div>
                </div>
            </div>

            {* brands tree *}
            <div id="bt_brands" {if empty($bExportMode)}style="display: none;" {/if}>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='Select the brands you want to export. You will be able to exclude some products from these selected brands in "Product exclusion rules" tab' mod='facebookproductad'}"><b>{l s='Brands' mod='facebookproductad'}</b></span> :
                    </label>
                    <div class="col-xs-12 col-md-5 col-lg-4">
                        <div class="btn-actions">
                            <div class="btn btn-default btn-mini" id="brandCheck" onclick="return oFpa.selectAll('input.brandBox', 'check');"><i class="icon-plus-square"></i>&nbsp;{l s='Check All' mod='facebookproductad'}</div> - <div class="btn btn-default btn-mini" id="brandUnCheck" onclick="return oFpa.selectAll('input.brandBox', 'uncheck');"><i class="icon-minus-square"></i>&nbsp;{l s='Uncheck All' mod='facebookproductad'}</div>
                            <div class="mt-3"></div>
                        </div>
                        <table cellspacing="0" cellpadding="0" class="table  table-bordered table-striped" style="width: 100%;">
                            {foreach from=$aFormatBrands name=brand key=iKey item=aBrand}
                                <tr class="alt_row">
                                    <td>
                                        {$aBrand.id|intval}
                                    </td>
                                    <td>
                                        <input type="checkbox" name="bt_brand-box[]" class="brandBox" id="bt_brand-box_{$aBrand.id|intval}" value="{$aBrand.id|intval}" {if !empty($aBrand.checked)}checked="checked" {/if} />
                                    </td>
                                    <td>
                                        <i class="icon icon-folder{if !empty($aBrand.checked)}-open{/if}">&nbsp;&nbsp;<span style="font-size:12px;"></i><span>{$aBrand.name|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                </tr>
                            {/foreach}
                        </table><br /><br />
                    </div>
                </div>
            </div>
        {/if}
        {* END - Export *}

        {* USE CASE - Exclusion *}
        {if !empty($sDisplay) && $sDisplay == 'exclusion'}

            <ul class="nav nav-tabs" id="myTab"">
			<li class=" {if empty($aExclusionRules)}active{/if}">
            <a data-toggle="tab" href="#basic"><i class="fa fa-file-code-o"></i>&nbsp;{l s='General exclusion' mod='facebookproductad'}</a>
            </li>
            <li class="{if !empty($aExclusionRules)}active{/if}">
                <a data-toggle="tab" href="#advanced"><i class="fa fa-server"></i>&nbsp;{l s='Advanced exclusion' mod='facebookproductad'}</a>
            </li>
        </ul>

        {if !empty($bUpdate)}
        {include file="`$sConfirmInclude`"}
        <div class="clr_20"></div>
        {elseif !empty($aErrors)}
        {include file="`$sErrorInclude`"}
        {/if}

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane {if empty($aExclusionRules)}active{/if}" id="basic">

                <div class="mt-3"></div>
                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select yes, all your active products will be exported. If you select no, only products that you have in stock will be exported.' mod='facebookproductad'}"><b>{l s='Do you want to export out of stock products?' mod='facebookproductad'}</b></span> :</label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_export-oos" id="bt_export-oos_on" value="1" {if !empty($bExportOOS)}checked="checked" {/if} />
                            <label for="bt_export-oos_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_export-oos" id="bt_export-oos_off" value="0" {if empty($bExportOOS)}checked="checked" {/if} />
                            <label for="bt_export-oos_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                        <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you select yes, all your active products will be exported. If you select no, only products that you have in stock will be exported.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                        <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/262" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product availability' mod='facebookproductad'}</a>
                    </div>
                </div>

                <div class="mt-3"></div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Facebook is sending you numerous errors due to missing EAN, UPC or ISBN codes, you can activate this option and products that have neither EAN13 nor UPC nor ISBN will not be exported. This will ensure that you no longer receive these errors while you recover all your products\' GTIN codes from your suppliers.' mod='facebookproductad'}"><b>{l s='Do you want to NOT export products without EAN13/JAN or UPC or ISBN?' mod='facebookproductad'}</b></span> :</label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_excl-no-ean" id="bt_excl-no-ean_on" value="1" {if !empty($bExcludeNoEan)}checked="checked" {/if} />
                            <label for="bt_excl-no-ean_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_excl-no-ean" id="bt_excl-no-ean_off" value="0" {if empty($bExcludeNoEan)}checked="checked" {/if} />
                            <label for="bt_excl-no-ean_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                        <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Facebook is sending you numerous errors due to missing EAN, UPC or ISBN codes, you can activate this option and products that have neither EAN13 nor UPC nor ISBN will not be exported. This will ensure that you no longer receive these errors while you recover all your products\' GTIN codes from your suppliers.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                        <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/263" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about GTIN codes' mod='facebookproductad'}</a>
                    </div>
                </div>

                <div class="clr_5"></div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Facebook is giving you many errors to missing MPN codes, you may activate this option and none of the products without a manufacturer reference will be exported. This will get rid of the Facebook errors until you are able to get all your product codes from suppliers.' mod='facebookproductad'}"><b>{l s='Do you want to NOT export products without a manufacturer (MPN) reference?' mod='facebookproductad'}</b></span> :</label>
                    <div class="col-xs-12 col-md-5 col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="bt_excl-no-mref" id="bt_excl-no-mref_on" value="1" {if !empty($bExcludeNoMref)}checked="checked" {/if} />
                            <label for="bt_excl-no-mref_on" class="radioCheck">
                                {l s='Yes' mod='facebookproductad'}
                            </label>
                            <input type="radio" name="bt_excl-no-mref" id="bt_excl-no-mref_off" value="0" {if empty($bExcludeNoMref)}checked="checked" {/if} />
                            <label for="bt_excl-no-mref_off" class="radioCheck">
                                {l s='No' mod='facebookproductad'}
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                        <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If Facebook is giving you many errors to missing MPN codes, you may activate this option and none of the products without a manufacturer reference will be exported. This will get rid of the Facebook errors until you are able to get all your product codes from suppliers.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                        <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/264" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about MPN codes' mod='facebookproductad'}</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-xs-12 col-md-3 col-lg-3">
                        <span class="label-tooltip" title="{l s='Enter 0 or leave the field blank to not apply any restrictions. Otherwise, any product whose CURRENT PRICE (taking specific prices into account) is lower than this value will be excluded from the feed. This allows you to exclude low margin products, making your Facebook ads more efficient and profitable.' mod='facebookproductad'}"><b>{l s='Do NOT export products with price lower than' mod='facebookproductad'}</b></span> :
                    </label>
                    <div class="col-xs-1 col-md-1 col-lg-1">
                        <input type="text" size="5" name="bt_min-price" value="{if !empty($iMinPrice)}{$iMinPrice|floatval}{/if}" />
                    </div>
                    {l s='Tax excluded' mod='facebookproductad'}
                    &nbsp;&nbsp;
                    <span class="icon-question-sign label-tooltip" title="{l s='Enter 0 or leave the field blank to not apply any restrictions. Otherwise, any product whose CURRENT PRICE (taking specific prices into account) is lower than this value will be excluded from the feed. This allows you to exclude low margin products, making your Facebook ads more efficient and profitable.' mod='facebookproductad'}">&nbsp;</span>
                </div>

                <div class="clr_5"></div>

            </div>
            <div class="tab-pane {if !empty($aExclusionRules)}active{/if}" id="advanced">
                <div class="mt-3"></div>
                <div class="alert alert-info pull-left">
                    {l s='Use this tool to create personal and very specific exclusion rules. To know how to use it, don\'t hesitate to read our FAQ :' mod='facebookproductad'}&nbsp;&nbsp;<a class="badge badge-info pulse pulse2" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/265" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How to create advanced exclusion rules?' mod='facebookproductad'}</a>
                </div>
                <a id="handleExclusion" class="btn btn-md btn-success fancybox.ajax btn btn-lg btn-success pull-right" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRule.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRule.type|escape:'htmlall':'UTF-8'}"><span class="icon-plus-circle"></span>&nbsp;{l s='Add exclusion rule' mod='facebookproductad'}</a>
                <div class="clr_20"></div>
                <p class="alert alert-warning">
                    {l s='Be careful: after having created custom rules, if you want to change the "About products with combinations" option value of the previous "Feed data options" tab, know that you will have to delete all the created rules and re-create them. Indeed, the exclusion management is different according to your choice to export or not by combination.' mod='facebookproductad'}
                </p>

                {if !empty($aExclusionRules)}

                <div class="form-group">
                    <button type="button" class="btn btn-default" onclick="return oFpa.selectAll('input.RulesBox', 'check');">
                        <i class="icon icon-plus-square"></i><span>&nbsp;{l s='Check All' mod='facebookproductad'}</span>
                    </button>
                    &nbsp;-&nbsp;
                    <button type="button" class="btn btn-default" onclick="return oFpa.selectAll('input.RulesBox', 'uncheck');">
                        <i class="icon icon-minus-square"></i><span>&nbsp;{l s='Unselect All' mod='facebookproductad'}</span>
                    </button>
                    &nbsp;-&nbsp;
                    <button class="btn btn-success " onclick="check = confirm('{l s='Are you sure you want to activate the selected rules?' mod='facebookproductad'}');if(!check)return false;iRulesId = oFpa.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oFpa.hide('bt_feed-settings-exclusion');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sUpdateType=bulk&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"><i
                            class="icon icon-cogs"></i><span>&nbsp;{l s='Activate selection' mod='facebookproductad'}</span>
                    </button>
                    &nbsp;-&nbsp;
                    <button class="btn btn-warning text-white"
                        onclick="check = confirm('{l s='Are you sure you want to deactivate the selected rules?' mod='facebookproductad'}');if(!check)return false;iRulesId = oFpa.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oFpa.hide('bt_feed-settings-exclusion');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sUpdateType=bulk&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"><i
                            class="icon icon-cogs"></i><span>&nbsp;{l s='Deactivate selection' mod='facebookproductad'}</span>
                    </button>
                    &nbsp;-&nbsp;
                    <button class="btn btn-danger "
                        onclick="check = confirm('{l s='Are you sure you want to delete the selected rules?' mod='facebookproductad'}');if(!check)return false;iRulesId = oFpa.getBulkCheckBox('bt_rules-box', false);$('#loadingGoogleDiv').show();oFpa.hide('bt_feed-settings-exclusion');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleDelete.type|escape:'htmlall':'UTF-8'}&iRuleId='+iRulesId+'&sDeleteType=bulk&sActionType=delete&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');">
                        <i class="icon icon-trash"></i><span>&nbsp;{l s='Delete selection' mod='facebookproductad'}</span>
                    </button>
                </div>

                {*Table for the rules saved*}
                <div class="row">
                    <table class="table table-striped">
                        <thead class="bt_tr_header">
                            <th class="center col-xs-1"></th>
                            <th class="center">#</th>
                            <th class="center"><b>{l s='Status' mod='facebookproductad'}</b></th>
                            <th class="center"><b>{l s='Rule\'s name' mod='facebookproductad'}</b></th>
                            <th class="center col-xs-2"><b>{l s='View affected products' mod='facebookproductad'}</b></th>
                            <th class="center">
                                <b>{l s='Actions' mod='facebookproductad'}</b>
                            </th>
                        </thead>
                        <tbody>
                            {foreach from=$aExclusionRules  key=key item=sRule}
                            <tr class="">
                                <td class="center"><input id="bt_rules-box_{$sRule.id|intval}" name="bt_rules-box" class="RulesBox" type="checkbox" value="{$sRule.id|escape:'htmlall':'UTF-8'}" /> </td>
                                <td class="center">{$sRule.id|escape:'htmlall':'UTF-8'}</td>
                                <td class="center">
                                    {if $sRule.status == 1}
                                    <a href="#"><i class="icon icon-2x icon-check-circle color_success" title="{l s='Deactivate' mod='facebookproductad'}"
                                            onclick="check = confirm('{l s='Are you sure you want to deactivate this rule?' mod='facebookproductad'}');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}&sUpdateType=one&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
                                    {else}
                                    <a href="#"><i class="icon icon-2x icon-check-circle color_danger" title="{l s='Activate' mod='facebookproductad'}"
                                            onclick="check = confirm('{l s='Are you sure you want to activate this rule?' mod='facebookproductad'}');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}&sUpdateType=one&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
                                    {/if}
                                </td>
                                <td class="center">{$sRule.name|escape:'htmlall':'UTF-8'}</td>
                                <td class="center">
                                    <a id="handleExclusionProducts" class="btn btn-md btn-success fancybox.ajax btn btn-mini btn-info" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleProducts.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleProducts.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}"><span class="fa fa-eye"></span></a>
                                </td>
                                <td class="center">
                                    <a id="handleExclusion" class="btn btn-md btn-success fancybox.ajax btn btn-info btn-min" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRule.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRule.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|escape:'htmlall':'UTF-8'}"><span class="icon icon-edit"></span></a>
                                    <a href="#"><i class="icon-trash btn btn-mini btn-danger" title="{l s='Delete' mod='facebookproductad'}"
                                            onclick="check = confirm('{l s='Are you sure you want to delete this rule?' mod='facebookproductad'} {l s='It will be definitely removed from your database' mod='facebookproductad'}');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRuleDelete.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRuleDelete.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}&sDeleteType=one&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
                                    {if $sRule.status == 1}
                                    <a href="#"><i class="btn btn-warning btn-mini fa fa-remove text-white" title="{l s='Deactivate' mod='facebookproductad'}" onclick="check = confirm('{l s='Are you sure you want to deactivate this rule?' mod='facebookproductad'}');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}&sUpdateType=one&bActivate=0&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
                                    {else}
                                    <a href="#"><i class="btn btn-success btn-mini fa fa-check" title="{l s='Activate' mod='facebookproductad'}" onclick="check = confirm('{l s='Are you sure you want to activate this rule?' mod='facebookproductad'}');if(!check)return false;$('#loadingGoogleDiv').show();oFpa.hide('bt_rules');oFpa.ajax('{$sURI|escape:'htmlall':'UTF-8'}', '{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.rulesList.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.rulesList.type|escape:'htmlall':'UTF-8'}&iRuleId={$sRule.id|intval}&sUpdateType=one&bActivate=1&sDisplay=exclusion', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', null, null, 'loadingGoogleDiv');"></i></a>
                                    {/if}
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {/if}

                <div class="d-flex justify-content-center">
                    <a id="handleExclusion" class="btn btn-md btn-success fancybox.ajax btn btn-lg btn-success col-3" href="{$sURI|escape:'htmlall':'UTF-8'}&{$sCtrlParamName|escape:'htmlall':'UTF-8'}={$sController|escape:'htmlall':'UTF-8'}&sAction={$aQueryParams.exclusionRule.action|escape:'htmlall':'UTF-8'}&sType={$aQueryParams.exclusionRule.type|escape:'htmlall':'UTF-8'}"><span class="icon-plus-circle"></span>&nbsp;{l s='Add exclusion rule' mod='facebookproductad'}</a>
                </div>
            </div>
        </div>
        {/if}
        {* END - Exclusion *}

        {* BEGIN - Feed data option *}
        {if !empty($sDisplay) && $sDisplay == 'data'}
        <h3 class="breadcrumb"><i class="fa fa-feed"></i>&nbsp;{l s='Feed data option' mod='facebookproductad'}</h3>

        <div class="alert alert-info">
            {l s='The more detailed information your provide to Facebook, the better your products will rank. Try to include as much information as possible.' mod='facebookproductad'}
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <b>{l s='About products with combinations' mod='facebookproductad'}</b>
            </label>
            <div class="col-xs-12 col-md-3 col-lg-3">
                <select name="bt_prod-combos" id="bt_prod-combos">
                    <option value="0" {if empty($bProductCombos)}selected="selected" {/if}>{l s='Export all combinations in a single product' mod='facebookproductad'}</option>
                    <option value="1" {if !empty($bProductCombos)}selected="selected" {/if}>{l s='Export each combination as a product in its own right' mod='facebookproductad'}</option>
                </select>
            </div>
        </div>

        <div id="bt_prod-combos-opts" style="display: {if !empty($bProductCombos)} block{else} none{/if}">

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This option will add for example size and color on product title in data feed' mod='facebookproductad'}">
                        <b>{l s='Include attribute values on product title ?' mod='facebookproductad'}</b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="bt_include_attribute_values" id="bt_include_attribute_values_on" value="1" {if !empty($bIncludeAttributeValue)}checked="checked" {/if} />
                        <label for="bt_include_attribute_values_on" class="radioCheck">
                            {l s='Yes' mod='facebookproductad'}
                        </label>
                        <input type="radio" name="bt_include_attribute_values" id="bt_include_attribute_values_off" value="0" {if empty($bIncludeAttributeValue)}checked="checked" {/if} />
                        <label for="bt_include_attribute_values_off" class="radioCheck">
                            {l s='No' mod='facebookproductad'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='This option will add for example size and color on product title in data feed' mod='facebookproductad'}"><span class="icon-question-sign"></span></span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Set this option to yes if you have bad redirection with product with combinaton' mod='facebookproductad'}">
                        <b>{l s='Include anchors in combination URLs?' mod='facebookproductad'}</b>
                    </span>
                </label>
                <div class="col-xs-12 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="bt_include_anchor" id="bt_include_anchor_on" value="1" {if !empty($bIncludeAnchor)}checked="checked" {/if} />
                        <label for="bt_include_anchor_on" class="radioCheck">
                            {l s='Yes' mod='facebookproductad'}
                        </label>
                        <input type="radio" name="bt_include_anchor" id="bt_include_anchor_off" value="0" {if empty($bIncludeAnchor)}checked="checked" {/if} />
                        <label for="bt_include_anchor_off" class="radioCheck">
                            {l s='No' mod='facebookproductad'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Set this option to yes if you have bad redirection with product with combinaton' mod='facebookproductad'}"><span class="icon-question-sign"></span></span>
                    &nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/521" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Should I activate this option?' mod='facebookproductad'}</a>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <span class="label-tooltip" title="{l s='The "feed id" of a product is built like this: "Shop prefix + Language + product id + separator + combination id". For example, the feed id BMFR17v32 corresponds to the combination of id 32 of the product of id 17 of the French feed of the BM shop. You can here choose the separator between product id and combination id. By default the separator is "v".' mod='facebookproductad'}"><b>{l s='Choose the separator between product id and combination id' mod='facebookproductad'}</b></span></label>
                <div class="col-xs-4 col-md-4 col-lg-2">
                    <input type="text" name="bt_combo-separator" value="{$sComboSeparator|escape:'htmlall':'UTF-8'}" />
                </div>
                <span class="icon-question-sign label-tooltip" title="{l s='The "feed id" of a product is built like this: "Shop prefix + Language + product id + separator + combination id". For example, the feed id BMFR17v32 corresponds to the combination of id 32 of the product of id 17 of the French feed of the BM shop. You can here choose the separator between product id and combination id. By default the separator is "v".' mod='facebookproductad'}">&nbsp;</span>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <b>{l s='Rewrite attribute numeric values with "," or "." in your combination URLs?' mod='facebookproductad'}</b>
                </label>
                <div class="col-xs-12 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="bt_rewrite-num-attr" id="bt_rewrite-num-attr_on" value="1" {if !empty($bRewriteNumAttrValues)}checked="checked" {/if} />
                        <label for="bt_rewrite-num-attr_on" class="radioCheck">
                            {l s='Yes' mod='facebookproductad'}
                        </label>
                        <input type="radio" name="bt_rewrite-num-attr" id="bt_rewrite-num-attr_off" value="0" {if empty($bRewriteNumAttrValues)}checked="checked" {/if} />
                        <label for="bt_rewrite-num-attr_off" class="radioCheck">
                            {l s='No' mod='facebookproductad'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    &nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/254" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='Should I activate this option?' mod='facebookproductad'}</a>
                </div>
            </div>

            {* USE CASE - we display this feature from PS 1.6.0.13 because they changed the way to format attributes into the combination URL and they added the attribute id, and sometimes some theme editors  *}

            <div class="form-group">
                <label class="control-label col-xs-12 col-md-3 col-lg-3">
                    <b>{l s='Include the attribute ID into the combination URL?' mod='facebookproductad'}</b>
                </label>
                <div class="col-xs-12 col-md-5 col-lg-6">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="bt_incl-attr-id" id="bt_incl-attr-id_on" value="1" {if !empty($bUrlInclAttrId)}checked="checked" {/if} />
                        <label for="bt_incl-attr-id_on" class="radioCheck">
                            {l s='Yes' mod='facebookproductad'}
                        </label>
                        <input type="radio" name="bt_incl-attr-id" id="bt_incl-attr-id_off" value="0" {if empty($bUrlInclAttrId)}checked="checked" {/if} />
                        <label for="bt_incl-attr-id_off" class="radioCheck">
                            {l s='No' mod='facebookproductad'}
                        </label>
                        <a class="slide-button btn"></a>
                    </span>
                    &nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/255" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How to set this option?' mod='facebookproductad'}</a>
                </div>
            </div>
            <div class="mt-3"></div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <b>{l s='Which description type do you want to use?' mod='facebookproductad'}</b>
            </label>
            <div class="col-xs-12 col-md-3 col-lg-3">
                <select name="bt_prod-desc-type">
                    {foreach from=$aDescriptionType name=desc key=iKey item=sType}
                    <option value="{$iKey|intval}" {if $iKey == $iDescType}selected="selected" {/if}>{$sType|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/273" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product description' mod='facebookproductad'}</a>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <b>{l s='About product availability' mod='facebookproductad'}</b>
            </label>
            <div class="col-xs-12 col-md-3 col-lg-3">
                <select name="bt_incl-stock">
                    <option value="1" {if $iIncludeStock == 1}selected="selected" {/if}>{l s='Only indicate products as available IF they are actually in stock' mod='facebookproductad'}</option>
                    <option value="2" {if $iIncludeStock == 2}selected="selected" {/if}>{l s='Always indicate products as available EVEN IF they are in fact out of stock' mod='facebookproductad'}</option>
                </select>
            </div>
            <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/262" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product availability' mod='facebookproductad'}</a>
        </div>


        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you use more than one type of code (EAN, UPC, or ISBN), this option allows you to prioritize one type of code over the others when setting the GTIN to send to Facebook. For example, if your store uses EAN-13 more frequently, but you also use UPC for some products, then set the option to "Check EAN-13/JAN code first". This way, the module will first check if an EAN code is present for the product and, if so, will use it as the GTIN code. If not, it will use the UPC code (if available).' mod='facebookproductad'}"><b>{l s='Determination of priority GTIN (EAN13/JAN or UPC or ISBN)' mod='facebookproductad'}</b></span>
            </label>
            <div class="col-xs-12 col-md-3 col-lg-3">
                <select name="bt_gtin-pref">
                    <option value="ean" {if $sGtinPreference == 'ean'}selected="selected" {/if}>{l s='Check EAN-13/JAN code first' mod='facebookproductad'}</option>
                    <option value="upc" {if $sGtinPreference == 'upc'}selected="selected" {/if}>{l s='Check UPC code first' mod='facebookproductad'}</option>
                    <option value="isbn" {if $sGtinPreference == 'isbn'}selected="selected" {/if}>{l s='Check ISBN code first' mod='facebookproductad'}</option>
                </select>
            </div>
            <div>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If you use more than one type of code (EAN, UPC, or ISBN), this option allows you to prioritize one type of code over the others when setting the GTIN to send to Facebook. For example, if your store uses EAN-13 more frequently, but you also use UPC for some products, then set the option to "Check EAN-13/JAN code first". This way, the module will first check if an EAN code is present for the product and, if so, will use it as the GTIN code. If not, it will use the UPC code (if available).' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/263" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about GTIN codes' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag for products that are for adults only. Select YES, save the form and then click "Configure the tag for each category"' mod='facebookproductad'}"><b>{l s='Do you want to include adult tags?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_incl-tag-adult" id="bt_incl-tag-adult_on" value="1" {if !empty($bIncludeTagAdult)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('tag_adult_link', 'tag_adult_link', null, null, true, true);" />
                    <label for="bt_incl-tag-adult_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_incl-tag-adult" id="bt_incl-tag-adult_off" value="0" {if empty($bIncludeTagAdult)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('tag_adult_link', 'tag_adult_link', null, null, true, false);" />
                    <label for="bt_incl-tag-adult_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='Use this tag for products that are for adults only. Select YES, save the form and then click "Configure the tag for each category"' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/274" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about adult tags' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="tag_adult_link" {if empty($bIncludeTagAdult)}style="display: none;" {/if}>
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                {if !empty($bIncludeTagAdult)}
                <a class="btn btn-md btn-success" href="{$handleTagAdultLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='facebookproductad'}</a>
                {else}
                <div class="alert alert-danger">{l s='Please save this page before configuring the tag' mod='facebookproductad'}</div>
                {/if}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to export product sizes in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the size of your products.' mod='facebookproductad'}"><b>{l s='Do you want to include product sizes?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <div class="col-xs-12 col-md-4 col-lg-6">
                    <select name="bt_incl-size" id="inc_size">
                        <option value="" {if $sIncludeSize == ''}selected="selected" {/if}>{l s='No' mod='facebookproductad'}</option>
                        <option value="attribute" {if $sIncludeSize == 'attribute'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) that define sizes' mod='facebookproductad'}</option>
                        <option value="feature" {if $sIncludeSize == 'feature'}selected="selected" {/if}>{l s='Yes : select FEATURE that define sizes' mod='facebookproductad'}</option>
                        <option value="both" {if $sIncludeSize == 'both'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) AND FEATURE that define sizes' mod='facebookproductad'}</option>
                    </select>
                </div>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='In order to export product sizes in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the size of your products.' mod='facebookproductad'}"><span class="icon-question-sign"></span></span>
                &nbsp;&nbsp;
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/275" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product sizes' mod='facebookproductad'}</a>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">

            </div>
        </div>

        <div class="form-group" id="div_size_opt_attr" style="display: none;">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                <select name="bt_size-opt[attribute][]" multiple="multiple" size="8" id="size_opt_attr">
                    <option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Attributes (multiple choice)' mod='facebookproductad'}</option>
                    {foreach from=$aAttributeGroups name=attribute key=iKey item=aGroup}
                    <option value="{$aGroup.id_attribute_group|intval}" {if !empty($aSizeOptions.attribute) && is_array($aSizeOptions.attribute) && in_array($aGroup.id_attribute_group, $aSizeOptions.attribute)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aGroup.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group" id="div_size_opt_feat" style="display: none;">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                <select name="bt_size-opt[feature][]" size="8" id="size_opt_feat">
                    <option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Features (one choice)' mod='facebookproductad'}</option>
                    {foreach from=$aFeatures name=feature key=iKey item=aFeature}
                    <option value="{$aFeature.id_feature|intval}" {if !empty($aSizeOptions.feature) && is_array($aSizeOptions.feature) && in_array($aFeature.id_feature, $aSizeOptions.feature)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aFeature.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>


        <div class="form-group">

            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <span class="label-tooltip" title="{l s='In order to export product colors in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the color of your products.' mod='facebookproductad'}"><b>{l s='Do you want to include product colors?' mod='facebookproductad'}</b></span>
            </label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                <select name="bt_incl-color" id="inc_color">
                    <option value="" {if $sIncludeColor == ''}selected="selected" {/if}>{l s='No' mod='facebookproductad'}</option>
                    <option value="attribute" {if $sIncludeColor == 'attribute'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) that define colors' mod='facebookproductad'}</option>
                    <option value="feature" {if $sIncludeColor == 'feature'}selected="selected" {/if}>{l s='Yes : select FEATURE that defines colors' mod='facebookproductad'}</option>
                    <option value="both" {if $sIncludeColor == 'both'}selected="selected" {/if}>{l s='Yes : select ATTRIBUTE(S) AND  FEATURE that define colors' mod='facebookproductad'}</option>
                </select>
            </div>
            <div>
                <span class="icon-question-sign label-tooltip" title="{l s='In order to export product colors in your data feed (hightly recommended for clothing), select what feature or(and) attribute(s) define the color of your products.' mod='facebookproductad'}"></span>
                &nbsp;&nbsp;
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/276" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about product colors' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="div_color_opt_attr" style="display: none;">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                <select name="bt_color-opt[attribute][]" multiple="multiple" size="8" id="color_opt_attr">
                    <option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Attributes (multiple choice)' mod='facebookproductad'}</option>
                    {foreach from=$aAttributeGroups name=attribute key=iKey item=aGroup}
                    <option value="{$aGroup.id_attribute_group|intval}" {if !empty($aColorOptions.attribute) && is_array($aColorOptions.attribute) && in_array($aGroup.id_attribute_group, $aColorOptions.attribute)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aGroup.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group" id="div_color_opt_feat" style="display: none;">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-12 col-md-4 col-lg-3">
                <select name="bt_color-opt[feature][]" size="8" id="color_opt_feat">
                    <option value="" disabled="disabled" style="color: #aaa;font-weight: bold;">{l s='Features (one choice)' mod='facebookproductad'}</option>
                    {foreach from=$aFeatures name=feature key=iKey item=aFeature}
                    <option value="{$aFeature.id_feature|intval}" {if !empty($aColorOptions.feature) && is_array($aColorOptions.feature) && in_array($aFeature.id_feature, $aColorOptions.feature)}selected="selected" {/if} style="padding-left: 10px;font-weight: bold;">{$aFeature.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3">
                <b>{l s='Fix my urls errors ?' mod='facebookproductad'}</b>
            </label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_url-error" id="bt_url-error_on" value="1" {if !empty($bUrlError)}checked="checked" {/if} />
                    <label for="bt_url-error_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_url-error" id="bt_url-error_off" value="0" {if empty($bUrlError)}checked="checked" {/if} />
                    <label for="bt_url-error_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                &nbsp;<a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/315" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='How to set this option?' mod='facebookproductad'}</a>
            </div>
        </div>
        {/if}
        {* END - Feed data option *}

        {* BEGIN - Advanced *}
        {if !empty($sDisplay) && $sDisplay == 'apparel'}
        <h3 class="breadcrumb">{l s='Apparel feed options' mod='facebookproductad'}</h3>

        {if !empty($bUpdate)}
        {include file="`$sConfirmInclude`"}
        {elseif !empty($aErrors)}
        {include file="`$sErrorInclude`"}
        {/if}

        <div class="alert alert-info">
            <p><b>{l s='Clothing and Apparel stores should try to include these tags if the information is available.' mod='facebookproductad'}</b>&nbsp;
                {l s='But, these tags can also be useful for other sales areas. Feel free to give as much information as you can about your products.' mod='facebookproductad'}</p>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the material of the products that are in this category.' mod='facebookproductad'}"><b>{l s='Do you want to include material tags?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_incl-material" id="bt_incl-material_on" value="1" {if !empty($bIncludeMaterial)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('material_link', 'material_link', null, null, true, true);" />
                    <label for="bt_incl-material_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_incl-material" id="bt_incl-material_off" value="0" {if empty($bIncludeMaterial)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('material_link', 'material_link', null, null, true, false);" />
                    <label for="bt_incl-material_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the material of the products that are in this category.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/268" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about material tags' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="material_link" {if empty($bIncludeMaterial)}style="display: none;" {/if}>
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-6 col-md-5 col-lg-4">
                {if !empty($bIncludeMaterial)}
                <a class="btn btn-md btn-success" href="{$handleTagMaterialLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='facebookproductad'}</a>
                {else}
                <div class="alert alert-danger" id="save_require">{l s='Please save this page before configuring the tag' mod='facebookproductad'}</div>
                {/if}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the pattern of the products that are in this category.' mod='facebookproductad'}"><b>{l s='Do you want to include pattern tags?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_incl-pattern" id="bt_incl-pattern_on" value="1" {if !empty($bIncludePattern)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('pattern_link', 'pattern_link', null, null, true, true);" />
                    <label for="bt_incl-pattern_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_incl-pattern" id="bt_incl-pattern_off" value="0" {if empty($bIncludePattern)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('pattern_link', 'pattern_link', null, null, true, false);" />
                    <label for="bt_incl-pattern_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='For each product default category, if available, you will have to indicate the feature that defines the pattern of the products that are in this category.' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/269" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about pattern tags' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="pattern_link" {if empty($bIncludePattern)}style="display: none;" {/if}>
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-6 col-md-5 col-lg-4">
                {if !empty($bIncludePattern)}
                <a class="btn btn-md btn-success" href="{$handleTagPatternLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='facebookproductad'}</a>
                {else}
                <div class="alert alert-danger" id="save_require">{l s='Please save this page before configuring the tag' mod='facebookproductad'}</div>
                {/if}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If available, you should indicate this for all apparel products' mod='facebookproductad'}"><b>{l s='Do you want to include gender tags?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_incl-gender" id="bt_incl-gender_on" value="1" {if !empty($bIncludeGender)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('gender_link', 'gender_link', null, null, true, true);" />
                    <label for="bt_incl-gender_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_incl-gender" id="bt_incl-gender_off" value="0" {if empty($bIncludeGender)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('gender_link', 'gender_link', null, null, true, false);" />
                    <label for="bt_incl-gender_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If available, you should indicate this for all apparel products' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/270" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about gender tags' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="gender_link" {if empty($bIncludeGender)}style="display: none;" {/if}>
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-6 col-md-5 col-lg-4">
                {if !empty($bIncludeGender)}
                <a class="btn btn-md btn-success" href="{$handleTagGenderLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='facebookproductad'}</a>
                {else}
                <div class="alert alert-danger" id="save_require">{l s='Please save this page before configuring the tag' mod='facebookproductad'}</div>
                {/if}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-3 col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If available, you should indicate this for all apparel products' mod='facebookproductad'}"><b>{l s='Do you want to include age group tags?' mod='facebookproductad'}</b></span></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_incl-age" id="bt_incl-age_on" value="1" {if !empty($bIncludeAge)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('age_group_link', 'age_group_link', null, null, true, true);" />
                    <label for="bt_incl-age_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_incl-age" id="bt_incl-age_off" value="0" {if empty($bIncludeAge)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('age_group_link', 'age_group_link', null, null, true, false);" />
                    <label for="bt_incl-age_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
                <span class="label-tooltip" data-toggle="tooltip" title data-original-title="{l s='If available, you should indicate this for all apparel products' mod='facebookproductad'}">&nbsp;<span class="icon-question-sign"></span></span>
                <a class="badge badge-info" href="{$faqLink|escape:'htmlall':'UTF-8'}/faq/271" target="_blank"><i class="icon icon-link"></i>&nbsp;{l s='FAQ about age group tags' mod='facebookproductad'}</a>
            </div>
        </div>

        <div class="form-group" id="age_group_link" {if empty($bIncludeAge)}style="display: none;" {/if}>
            <label class="control-label col-xs-12 col-md-3 col-lg-3"></label>
            <div class="col-xs-6 col-md-5 col-lg-4">
                {if !empty($bIncludeAge)}
                <a class="btn btn-md btn-success" href="{$handleTagAgeGroupeLink|escape:'htmlall':'UTF-8'}">{l s='Click here to configure the tag for each category' mod='facebookproductad'}</a>
                {else}
                <div class="alert alert-danger" id="save_require">{l s='Please save this page before configuring the tag' mod='facebookproductad'}</div>
                {/if}
            </div>
        </div>
        {/if}
        {* END - Apparel *}

        {* BEGIN - Tax and shipping fees *}
        {if !empty($sDisplay) && $sDisplay == 'tax'}
        <h3 class="breadcrumb">{l s='Shipping costs' mod='facebookproductad'}</h3>

        {if !empty($bUpdate)}
        {include file="`$sConfirmInclude`"}
        {elseif !empty($aErrors)}
        {include file="`$sErrorInclude`"}
        {/if}



        <div class="clr_15"></div>

        <div class="form-group">
            <label class="control-label col-xs-12 col-md-4 col-lg-4"><b>{l s='Do you want the module to handle shipping costs?' mod='facebookproductad'}</b></label>
            <div class="col-xs-12 col-md-5 col-lg-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bt_manage-shipping" id="bt_manage-shipping_on" value="1" {if !empty($bShippingUse)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('bt_conf-shipping', 'bt_conf-shipping', null, null, true, true);" />
                    <label for="bt_manage-shipping_on" class="radioCheck">
                        {l s='Yes' mod='facebookproductad'}
                    </label>
                    <input type="radio" name="bt_manage-shipping" id="bt_manage-shipping_off" value="0" {if empty($bShippingUse)}checked="checked" {/if} onclick="javascript: oFpa.changeSelect('bt_conf-shipping', 'bt_conf-shipping', null, null, true, false);" />
                    <label for="bt_manage-shipping_off" class="radioCheck">
                        {l s='No' mod='facebookproductad'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>


        <div id="bt_conf-shipping" {if empty($bShippingUse)}style="display: none;" {/if}>

            <div class="alert alert-info">
                {l s='Please select below the corresponding default carrier for each country, check the box if you do not want to apply taxes on shipping costs or if you want to offer shipping costs. You also have the option to enter a minimum product price (including taxes) above which shipping is free.' mod='facebookproductad'}
            </div>


            {if !empty($aShippingCarriers)}
            <table class="table">
                <tr>
                    <th class="bt_tr_header center">{l s='Country' mod='facebookproductad'}</th>
                    <th class="bt_tr_header center">{l s='Carrier' mod='facebookproductad'}</th>
                    <th class="bt_tr_header center">{l s='Do not apply taxes on shipping costs' mod='facebookproductad'}</th>
                    <th class="bt_tr_header center">{l s='Apply free shipping' mod='facebookproductad'}</th>
                    <th class="bt_tr_header center">{l s='Apply free shipping if the product price (incl. taxes) is higher than:' mod='facebookproductad'}</th>
                </tr>
                <tbody>
                    {foreach from=$aShippingCarriers name=shipping key=sCountry item=aShipping}
                    <tr>
                        <td class="center">{$sCountry|escape:'htmlall':'UTF-8'}</td>
                        <td class="center">
                            <select class="center col-xs-12" name="bt_ship-carriers[{$sCountry|escape:'htmlall':'UTF-8'}]">
                                {foreach from=$aShipping.carriers name=carrier key=iKey item=aCarrier}
                                <option {if $aCarrier.id_reference == $aShipping.shippingCarrierId}selected=selected{/if} value="{$aCarrier.id_reference|intval}">{$aCarrier.name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td class="center">
                            <input type="checkbox" name="bt_ship-carriers_no_tax[{$sCountry|escape:'htmlall':'UTF-8'}]" {if !empty($aShipping.noTaxCarrier)} checked {/if} />
                        </td>
                        <td class="center">
                            <input type="checkbox" name="bt_ship-carriers_free[{$sCountry|escape:'htmlall':'UTF-8'}]" {if !empty($aShipping.free)} checked {/if} />
                        </td>
                        <td class="center">
                            <input type="text" name="bt_ship-carriers_free_product_price[{$sCountry|escape:'htmlall':'UTF-8'}]" value="{$aShipping.productFree|escape:'htmlall':'UTF-8'}" placeholder="{l s='Price of the product with tax' mod='facebookproductad'}" />
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            {else}
            <div class="alert alert-warning">
                {l s='There isn\'t any carrier available' mod='facebookproductad'}
            </div>
            <div class="clr_15"></div>
            {/if}
        </div>
        {/if}
        {* END - Tax and shipping fees *}

        <div class="mt-3"></div>
        <div class="clr_hr"></div>
        <div class="mt-3"></div>

        <div class="navbar navbar-default navbar-fixed-bottom text-center">
            <div class="col-xs-12">
                <button class="btn btn-submit" onclick="oFpa.form('bt_feed-{$sDisplay|escape:'htmlall':'UTF-8'}-form', '{$sURI|escape:'htmlall':'UTF-8'}', null, 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', 'bt_feed-settings-{$sDisplay|escape:'htmlall':'UTF-8'}', false, false, {if empty($sDisplay) || (!empty($sDisplay) && $sDisplay == 'export')}oFeedSettingsCallBack{else}null{/if}, 'Feed{$sDisplay|escape:'htmlall':'UTF-8'}', 'loadingFeedDiv', false, 3);return false;">{l s='Save' mod='facebookproductad'}</button>
            </div>
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    $(document).ready(function() {

        $("a#handleExclusion").fancybox({
            'hideOnContentClick': false,
            'scrolling': 'yes'
        });

        $("a#handleExclusionProducts").fancybox({
            'hideOnContentClick': false,
            'scrolling': 'yes'
        });

        // handle export type
        $("#bt_prod-combos").bind('change', function(event) {
            $("#bt_prod-combos option:selected").each(function() {
                switch ($(this).val()) {
                    case '0':
                        $("#bt_prod-combos-opts").hide();
                        break;
                    case '1':
                        $("#bt_prod-combos-opts").show();
                        break;
                    default:
                        $("#bt_prod-combos-opts").hide();
                            break;
                    }
                });
            }).change();

        });
        //bootstrap components init
    {/literal}
    {if !empty($bAjaxMode)}
        {literal}
            $('.label-tooltip, .help-tooltip').tooltip();
            oFpa.runMainFeed();
            {/literal}{/if}{literal}
        </script>
    {/literal}