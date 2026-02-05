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

{extends file='customer/page.tpl'}

{block name="page_title"}
    <h4>{l s='Open Quotes' mod='roja45quotationspro'}</h4>
{/block}

{block name='page_content'}
    {if isset($customerquotes) && ($customerquotes|@count gt 0)}
        <div id="roja45_customer_quotes" class="table-container" role="table" aria-label="Destinations">
            <div class="flex-table" role="rowgroup">
                <div class="column">
                    <div class="flex-row header">
                        <div class="flex-cell first" role="columnheader">{l s='Reference' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Requested' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Last Update' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Expires' mod='roja45quotationspro'} </div>
                        <div class="flex-cell" role="columnheader">{l s='Total (exc)' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Total (inc)' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Shipping (inc)' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader"></div>
                        <div class="flex-cell" role="columnheader"></div>
                        <div class="flex-cell last_item" role="columnheader"></div>
                    </div>
                    {foreach $customerquotes as $customerquote}
                        <div class="flex-row">
                            <div class="flex-cell quote-product-image" role="cell">
                                <a href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = [
                                'action' => 'getQuotationDetails',
                                'id_roja45_quotation' => $customerquote->id_roja45_quotation,
                                'back' => 'getCustomerQuotes']}" class="ajax-view-quote"
                                    title="{l s='View Quote Details' mod='roja45quotationspro'}">{$customerquote->reference}</a>
                            </div>
                            <div class="flex-cell" role="cell">{dateFormat date=$customerquote->date_add full=false}</div>
                            <div class="flex-cell" role="cell">{dateFormat date=$customerquote->date_upd full=false}</div>
                            <div class="flex-cell" role="cell">
                                {if ($customerquote->expiry_date != '0000-00-00 00:00:00')}{dateFormat date=$customerquote->expiry_date full=true}{/if}
                            </div>
                            <div class="flex-cell" role="cell">
                                {if $customerquote->quote_sent=="1"}{$customerquote->total_exc_formatted}{/if}</div>
                            <div class="flex-cell" role="cell">
                                {if $customerquote->quote_sent=="1"}{if $customerquote->calculate_taxes}{$customerquote->total_inc_formatted}{else}{l s='N/A' mod='roja45quotationspro'}{/if}{/if}
                            </div>
                            <div class="flex-cell" role="cell">
                                {if $customerquote->quote_sent=="1"}{$customerquote->total_shipping_inc_formatted}{/if}
                            </div>
                            <div class="flex-cell" role="cell">
                                {if $customerquote->quote_sent=="1"}
                                    <a href="{url entity='module'
                                name='roja45quotationspro'
                                controller='QuotationsProFront'
                                params = [
                                'action' => 'getQuotationDetails',
                                'id_roja45_quotation' => $customerquote->id_roja45_quotation,
                                'back' => 'getCustomerQuotes']}"
                                        class="btn btn-default btn-primary btn-view-quote ajax-view-quote"
                                        title="{l s='View Quote Details' mod='roja45quotationspro'}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                            <path
                                                d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H15v-8h5V8l-6-6zm-1 7V3.5L18.5 9H13zm4 12.66V16h5.66v2h-2.24l2.95 2.95l-1.41 1.41L19 19.41v2.24h-2z" />
                                        </svg>
                                    </a>
                                    <a href="{url entity='module'
                            name='roja45quotationspro'
                            controller='QuotationsProFront'
                            params = [
                            'action' => 'downloadPDF',
                            'id_roja45_quotation' => $customerquote->id_roja45_quotation
                            ]}" title="{l s='Download PDF' mod='roja45quotationspro'}" target="_blank"
                                        class="btn btn-default btn-primary btn-download-pdf ajax-download-pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                            <path d="M19 9h-4V3H9v6H5l7 7l7-7zM5 18v2h14v-2H5z" />
                                        </svg>
                                    </a>
                                {/if}
                            </div>
                            <div class="flex-cell" role="cell">
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
                                        <a title="{l s='Click here to request an order.  You request will be sent to our operators.' mod='roja45quotationspro'}"
                                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitRequestOrder', 'id_roja45_quotation' => $customerquote->id_roja45_quotation]}"
                                            class="btn btn-default btn-primary btn-add-to-order ajax-add-to-order">
                                            <svg width="40" height="40" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z" />
                                            </svg>
                                        </a>
                                    {else}
                                        <a title="{l s='Click to add this quote to your cart.' mod='roja45quotationspro'}"
                                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitAddToCart', 'id_roja45_quotation' => $customerquote->id_roja45_quotation]}"
                                            class="btn btn-default btn-primary btn-add-to-cart ajax-add-to-cart">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2s-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2s2-.9 2-2s-.9-2-2-2zm-9.83-3.25l.03-.12l.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2l-2.76 5H8.53l-.13-.27L6.16 6l-.95-2l-.94-2H1v2h2l3.6 7.59l-1.35 2.45c-.16.28-.25.61-.25.96c0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z" />
                                            </svg>
                                        </a>
                                    {/if}
                                {else}
                                    <a title="{l s='Please wait, quotation in progress' mod='roja45quotationspro'}" href="#"
                                        class="btn btn-default btn-secondary btn-please-wait">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                            <path
                                                d="M11 17c0 .55.45 1 1 1s1-.45 1-1s-.45-1-1-1s-1 .45-1 1zm0-14v4h2V5.08c3.39.49 6 3.39 6 6.92c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-1.68.59-3.22 1.58-4.42L12 13l1.41-1.41l-6.8-6.8v.02C4.42 6.45 3 9.05 3 12c0 4.97 4.02 9 9 9a9 9 0 0 0 0-18h-1zm7 9c0-.55-.45-1-1-1s-1 .45-1 1s.45 1 1 1s1-.45 1-1zM6 12c0 .55.45 1 1 1s1-.45 1-1s-.45-1-1-1s-1 .45-1 1z" />
                                        </svg>
                                    </a>
                                {/if}
                            </div>
                            <div class="flex-cell" role="cell">
                                <a href="{url entity='module'
                            name='roja45quotationspro'
                            controller='QuotationsProFront'
                            params = [
                            'action' => 'customerDelete',
                            'id_roja45_quotation' => $customerquote->id_roja45_quotation
                            ]}" title="{l s='Delete Quote' mod='roja45quotationspro'}"
                                    class="btn btn-default btn-primary btn-delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

    {else}
        <div class="box">
            <p class="warning">{l s='You have no available quotes.' mod='roja45quotationspro'}</p>
        </div>
    {/if}
{/block}