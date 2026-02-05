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
    <h2 class="title_block">{l s='Quote Details' mod='roja45quotationspro'}</h2>
    <div id="view_quotation_details">
        <div id="quotation_details">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table" id="quotationProducts">
                        <thead>
                        <tr>
                            <th colspan="1"></th>
                            <th colspan="3"><span class="title_box">{l s='Product' mod='roja45quotationspro'}</span></th>
                            <th colspan="1"><span class="title_box">{l s='Reference' mod='roja45quotationspro'}</span></th>
                            <th colspan="2"><span class="title_box">{l s='Comment' mod='roja45quotationspro'}</span></th>
                            <th colspan="2">
                                <span class="title_box ">{l s='Unit Price' mod='roja45quotationspro'}{if ($show_taxes==1)} {l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
                            </th>
                            <th class="text-center" colspan="1"><span class="title_box ">{l s='Qty' mod='roja45quotationspro'}</span></th>
                            <th colspan=2>
                                <span class="title_box ">{l s='Total' mod='roja45quotationspro'}{if ($show_taxes==1)} {l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$quotation_products item=product key=k}
                            {* Include product line partial *}
                            {include file='./customer-quote-product-line.tpl'}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            {if (count($charges))}
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-charges">
                        <div id="charges_table">
                            {include file='./customer-quote-charge-table.tpl'}
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            {if (count($discounts))}
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-vouchers">
                        <div id="discount_table">
                            {include file='./customer-quote-discount-table.tpl'}
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            <div class="row">
                <div class="col-xs-6">
                </div>
                <div class="col-xs-6">
                    <div class="panel panel-total">
                        {include file='./customer-quote-totals.tpl'}
                    </div>
                </div>
            </div>
            {if ($exchange_rate != 1)}
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-total">
                        <p class="alert alert-warning">
                            {l s='Your quote has been provided in your requested currency.  Please be aware that currency fluctuations may result in the price you have been quoted changing.  We reserve the right to change or cancel this quote at any time.' mod='roja45quotationspro'}
                        </p>
                    </div>
                </div>
            </div>
            {/if}
            {if $quotation->quote_sent=="1"}
                <div class="row">
                    <div class="col-xs-12">
                        {if $catalog_mode}
                            {if $num_address}
                                <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'submitRequestOrder','id_roja45_quotation' => $quotation->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-add-to-cart ajax-add-to-cart pull-right">
                                    {l s='Order' mod='roja45quotationspro'}
                                </a>
                            {/if}
                        {else}
                            {if $num_address}
                                <a href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'submitAddToCart','id_roja45_quotation' => $quotation->id_roja45_quotation],true)|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-add-to-cart ajax-add-to-cart pull-right">
                                    {l s='Add To Cart' mod='roja45quotationspro'}
                                </a>
                            {/if}
                        {/if}
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <ul class="footer_links">
        <li class="fleft">
            <a class="btn btn-default button button-small"
               href="{$link->getModuleLink('roja45quotationspro','QuotationsProFront',['action'=>'getCustomerQuotes'],true)|escape:'htmlall':'UTF-8'}"
               title="{l s='Back to Your Quotes' mod='roja45quotationspro'}">
				<span>
					<i class="icon-chevron-left"></i>{l s='Back to Your Quotes' mod='roja45quotationspro'}
				</span>
            </a>
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
