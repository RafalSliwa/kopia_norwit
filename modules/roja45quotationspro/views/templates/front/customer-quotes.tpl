{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"
        title="{l s='My account' mod='roja45quotationspro'}" data-gg="">
        {l s='My account' mod='roja45quotationspro'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe|escape:'htmlall':'UTF-8'}
    </span>
    <span class="navigation_page">
        {l s='My Quotes' mod='roja45quotationspro'}
    </span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div id="customerquotes_block_account" class="block">
    <div id="customerquotes_block_account_modal" class="invisible">
        <div class="modal-wait-icon">
            <i class="icon-refresh icon-spin animated"></i>
            <h2>{l s='Please Wait..' mod='roja45quotationspro'}</h2>
        </div>
    </div>

    <h4 class="title_block">{l s='My Quotes' mod='roja45quotationspro'}</h4>

    {if !$num_address}
        <div id="customerquotes_block_address_warning" class="">
            <p class="alert alert-warning">
                {l s='Please enter an address before requesting an order: ' mod='roja45quotationspro'}<a
                    href="{$link->getPageLink('address', true, NULL,null)|escape:'htmlall':'UTF-8'}">{l s='Click here.' mod='roja45quotationspro'}</a>
            </p>
        </div>
    {/if}

    {if isset($customerquotes) && ($customerquotes|@count gt 0)}
        <table class="table tableDnD" id="downloadableItemsTable">
            <thead>
                <tr class="nodrag nodrop">
                    <th class="fixed-width-lg"><span class="title_box">{l s='Reference' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg"><span class="title_box">{l s='Received' mod='roja45quotationspro'}</span>
                    </th>
                    <th><span class="title_box">{l s='Last Update' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg">
                        <span class="title_box">{l s='Expires' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg"><span class="title_box">{l s='Total (exc)' mod='roja45quotationspro'}</span>
                    </th>
                    {if $tax_enabled && !$customer_group_without_tax}
                        <th class="fixed-width-lg"><span class="title_box">{l s='Total (inc)' mod='roja45quotationspro'}</span>
                        </th>
                    {/if}
                    <th class="fixed-width-lg"><span
                            class="title_box">{l s='Shipping (inc)' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-xs"></th>
                    <th class="fixed-width-xs"></th>
                    <th class="fixed-width-xs"></th>
                </tr>
            </thead>
            <tbody id="fileList">
                {foreach $customerquotes as $customerquote}
                    <tr class="nodrag nodrop" data-id="{$customerquote->id_roja45_quotation|escape:'htmlall':'UTF-8'}">
                        <td class="fixed-width-lg reference">{$customerquote->reference|escape:'htmlall':'UTF-8'}</td>
                        <td class="fixed-width-lg added-date">{dateFormat date=$customerquote->date_add full=false}</td>
                        <td class="updated-date">{dateFormat date=$customerquote->date_upd full=true}</td>
                        <td class="fixed-width-lg expiration-date">
                            {if ($customerquote->expiry_date != '0000-00-00 00:00:00')}{dateFormat date=$customerquote->expiry_date full=true}{/if}
                        </td>
                        <td class="fixed-width-lg total">{$customerquote->total_exc_formatted|escape:'htmlall':'UTF-8'}</td>
                        {if $tax_enabled && !$customer_group_without_tax}
                            <td class="fixed-width-lg total">{$customerquote->total_inc_formatted|escape:'html':'UTF-8'}</td>
                        {/if}
                        <td class="fixed-width-lg total">{$customerquote->total_shipping_inc_formatted|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'getQuotationDetails','id_roja45_quotation'=>$customerquote->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}"
                                class="btn btn-default btn-view-quote ajax-view-quote">
                                {if $roja45quotationspro_iconpack=='1'}
                                <i class="icon-eye"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i
                                    class="fa fa-eye"></i>{else}<i class="icon-eye"></i>
                                {/if}
                            </a>
                        </td>
                        <td>
                            <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'downloadPDF','id_roja45_quotation'=>$customerquote->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}"
                                class="btn btn-default btn-download-pdf ajax-download-pdf">
                                {if $roja45quotationspro_iconpack=='1'}<i
                                    class="icon-file-pdf-o"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i
                                    class="fa fa-file-pdf-o"></i>{else}<i class="icon-file-pdf-o"></i>
                                {/if}
                            </a>
                        </td>
                        <td>
                            {if $customerquote->expired=="1"}
                                <div class="quote-expired">
                                    <span>{l s='EXPIRED' mod='roja45quotationspro'}</span>
                                </div>
                            {elseif $customerquote->quote_sent=="1"}
                                {if $customerquote->ordered && !$roja45_multiple_customer_orders}
                                    <div class="quote-ordered">
                                        <span>{l s='ORDERED' mod='roja45quotationspro'}</span>
                                    </div>

                                {elseif $catalog_mode}
                                    <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'submitRequestOrder','id_roja45_quotation'=>$customerquote->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}"
                                        class="btn btn-default btn-add-to-cart ajax-add-to-cart {if !$num_address}disabled{/if}">
                                        <span>{l s='Order' mod='roja45quotationspro'}</span>{if $roja45quotationspro_iconpack=='1'}<i
                                            class="icon-shopping-cart"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i
                                            class="fa fa-shopping-cart"></i>{else}<i class="icon-shopping-cart"></i>
                                        {/if}
                                    </a>
                                {else}
                                    <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'submitAddToCart','id_roja45_quotation'=>$customerquote->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}"
                                        class="btn btn-default btn-add-to-cart ajax-add-to-cart {if !$num_address}disabled{/if}">
                                        {if $roja45quotationspro_iconpack=='1'}<i
                                            class="icon-shopping-cart"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i
                                            class="fa fa-shopping-cart"></i>{else}<i class="icon-shopping-cart"></i>
                                        {/if}
                                    </a>
                                {/if}
                            {else}
                                <div class="quote-in-progress">
                                    <span>{l s='In Progress' mod='roja45quotationspro'}</span>
                                </div>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <p class="warning">{l s='You have no open quotes.' mod='roja45quotationspro'}</p>
    {/if}

    {if isset($historicalquotes) && ($historicalquotes|@count gt 0)}
        <h4>{l s='Quote History' mod='roja45quotationspro'}</h4>
        <table class="table tableDnD" id="downloadableItemsTable">
            <thead>
                <tr class="nodrag nodrop">
                    <th class="fixed-width-lg"><span class="title_box">{l s='Reference' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg"><span class="title_box">{l s='Date Added' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg"><span class="title_box">{l s='Last Updated' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg">
                        <span class="title_box">{l s='Purchased On' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="fixed-width-lg"><span class="title_box">{l s='Total' mod='roja45quotationspro'}</span></th>
                    <th class="fixed-width-xs">

                    </th>
                </tr>
            </thead>
            <tbody id="fileList">
                {foreach $historicalquotes as $historicalquote}
                    <tr class="nodrag nodrop" data-id="{$historicalquote->id_roja45_quotation|escape:'htmlall':'UTF-8'}">
                        <td class="fixed-width-lg reference">{$historicalquote->reference|escape:'htmlall':'UTF-8'}</td>
                        <td class="fixed-width-lg added-date">{dateFormat date=$historicalquote->date_add full=false}</td>
                        <td class="fixed-width-lg updated-date">{dateFormat date=$historicalquote->date_upd full=true}</td>
                        <td class="fixed-width-lg purchased-date">{dateFormat date=$historicalquote->purchase_date full=false}
                        </td>
                        <td class="fixed-width-lg total">
                            {displayPrice price=Tools::ps_round(Tools::convertPrice($historicalquote->getQuotationTotal(), $currency), 2) currency=$currency->id|escape:'htmlall':'UTF-8'}
                        </td>
                        <td>
                            <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'getQuotationDetails','id_roja45_quotation'=>$historicalquote->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}"
                                class="btn btn-default btn-view-quote ajax-view-quote">
                                <span>{l s='View' mod='roja45quotationspro'}</span><i class="icon-eye"></i>
                            </a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}

    <ul class="footer_links">
        <li class="fleft">
            <a class="btn btn-default button button-small"
                href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}"
                title="{l s='Back to Your Account' mod='roja45quotationspro'}">
                <span>
                    <i class="icon-chevron-left"></i>{l s='Back to Your Account' mod='roja45quotationspro'}
                </span>
            </a>
        </li>
    </ul>
</div>