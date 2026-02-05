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
<script type="text/javascript">
    var all_combinations_text ='{l s='All combinations' mod='ets_productmanager' js=1}';
    var all_currencies_text ='{l s='All currencies' mod='ets_productmanager' js=1}';
    var all_countries_text ='{l s='All countries' mod='ets_productmanager' js=1}';
    var all_groups_text ='{l s='All groups' mod='ets_productmanager' js=1}';
    var all_customer_text ='{l s='All customers' mod='ets_productmanager' js=1}';
    var all_customer_text = '{l s='All customers' mod='ets_productmanager' js=1}';
    var Unlimited_text = '{l s='Unlimited' mod='ets_productmanager' js=1}';
    var from_text = '{l s='From' mod='ets_productmanager' js=1}';
    var to_text = '{l s='To' mod='ets_productmanager' js=1}';
    var confirm_delete_specific = '{l s='Do you want to delete this item?' mod='ets_productmanager' js=1}';
</script>
<script type="text/javascript" src="{$module_dir|escape:'html':'UTF-8'}/views/js/autocomplete.js"></script>
<script type="text/javascript" src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$module_dir|escape:'html':'UTF-8'}/views/js/specific_prices.js?time={time()|escape:'html':'UTF-8'}"></script>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css"/>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css"/>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/plugins/autocomplete/jquery.autocomplete.css" rel="stylesheet" type="text/css"/>
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit specific price' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="ets_pmn_errors"></div>
        <div class="form-wrapper">
            <div id="html_form_specific_prices">
                <a id="js-open-create-specific-price-form" class="btn btn-outline-primary" href="#specific_price_form">

                        <svg class="w_14 h_14" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                    {l s='Add a specific price' mod='ets_productmanager'}
                </a>
                <div id="specific_price_form" class="hide" style="">
                    {$specific_prices_from nofilter}
                </div>
                <div class="table-responsive">
                    <table id="js-specific-price-list" class="table seo-table">
                        <thead class="thead-default">
                            <tr>
                              <th>{l s='Rule' mod='ets_productmanager'}</th>
                              <th>{l s='Combination' mod='ets_productmanager'}</th>
                              <th>{l s='Currency' mod='ets_productmanager'}</th>
                              <th>{l s='Country' mod='ets_productmanager'}</th>
                              <th>{l s='Group' mod='ets_productmanager'}</th>
                              <th>{l s='Customer' mod='ets_productmanager'}</th>
                              <th>{l s='Fixed price' mod='ets_productmanager'}</th>
                              <th>{l s='Impact' mod='ets_productmanager'}</th>
                              <th>{l s='Period' mod='ets_productmanager'}</th>
                              <th>{l s='From' mod='ets_productmanager'}</th>
                              <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $specific_prices}
                                {foreach from= $specific_prices item='specific_price'}
                                    <tr id="specific_price-{$specific_price.id_specific_price|intval}">
                                        <td><span class="text-center">--</span></td>
                                        <td>
                                            {if $specific_price.id_product_attribute==0}
                                                {l s='All combinations' mod='ets_productmanager'}
                                            {else}
                                                {$specific_price.attribute_name|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                        <td>
                                            {if $specific_price.id_currency==0}
                                                {l s='All currencies' mod='ets_productmanager'}
                                            {else}
                                                {$specific_price.currency_name|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                        <td>
                                            {if $specific_price.id_country==0}
                                                {l s='All countries' mod='ets_productmanager'}
                                            {else}
                                                {$specific_price.country_name|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                        <td>
                                            {if $specific_price.id_group==0}
                                                {l s='All groups' mod='ets_productmanager'}
                                            {else}
                                                {$specific_price.group_name|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                        <td>
                                            {if $specific_price.id_customer==0}
                                                {l s='All customers' mod='ets_productmanager'}
                                            {else}
                                                {$specific_price.customer_name|escape:'html':'UTF-8'}
                                            {/if}
                                        </td>
                                        <td>
                                            {$specific_price.price_text|escape:'html':'UTF-8'}
                                        </td>
                                        <td>
                                            -{$specific_price.reduction|escape:'html':'UTF-8'}
                                        </td>
                                        <td>
                                            {if $specific_price.from!='0000-00-00 00:00:00' || $specific_price.to!='0000-00-00 00:00:00'}
                                                {l s='From' mod='ets_productmanager'}: {dateFormat date=$specific_price.from full=1}<br />
                                                {l s='to' mod='ets_productmanager'}: {dateFormat date=$specific_price.to full=1}<br />
                                            {else}
                                                {l s='Unlimited' mod='ets_productmanager'}
                                            {/if}
                                        </td>
                                        <td>
                                            {$specific_price.from_quantity|intval}
                                        </td>
                                        <td class="ets-special-edit">
                                            <a title="{l s='Delete' mod='ets_productmanager'}" href="#" class="js-delete delete btn ets_mp_delete_specific delete pl-0 pr-0" data-id_specific_price="{$specific_price.id_specific_price|intval}">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete' mod='ets_productmanager'}
                                            </a>
                                            <a title="{l s='Edit' mod='ets_productmanager'}" class="js-edit edit btn tooltip-link delete pl-0 pr-0" href="#" data-id_specific_price="{$specific_price.id_specific_price|intval}">

                                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
                                                 {l s='Edit' mod='ets_productmanager'}
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>  
        </div>
    </div>
</form>