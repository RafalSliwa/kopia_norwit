{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<li class="ets_cfu_li">
    <div class="ets_cfu_{$input.name|lower|escape:'html':'UTF-8'} col-lg-4">
        <input type="text" class="ets_cfu_name" data-type="text" name="{$key|escape:'html':'UTF-8'}[name][]"  value="{if isset($element) && $element}{$element.name|escape:'html':'UTF-8'}{/if}" placeholder="{l s='Name' mod='ets_cfultimate'}" />
    </div>
    <div class="ets_cfu_{$input.name|lower|escape:'html':'UTF-8'} col-lg-6">
        <input type="text" class="ets_cfu_email" data-type="email" name="{$key|escape:'html':'UTF-8'}[email][]"  value="{if isset($element) && $element}{$element.email|escape:'html':'UTF-8'}{/if}" placeholder="{l s='Email' mod='ets_cfultimate'}" />
    </div>
    {if isset($input.show_btn_add) && $input.show_btn_add}
        <div class="ets_cfu_{$input.name|lower|escape:'html':'UTF-8'} button col-lg-2">
            {if $ik > $end}<span class="ets_cfu_add btn btn-primary" title="{l s='Add' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></span>
            {else}<span class="ets_cfu_del btn btn-primary" title="{l s='Delete' mod='ets_cfultimate'}"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></span>{/if}
        </div>
    {/if}
</li>