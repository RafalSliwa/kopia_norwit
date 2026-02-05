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
    <h4>{l s='Quote History' mod='roja45quotationspro'}</h4>
{/block}

{block name='page_content'}
    {if isset($quotes) && ($quotes|@count gt 0)}
        <div id="roja45_customer_quotes" class="table-container" role="table" aria-label="Destinations">
            <div class="flex-table" role="rowgroup">
                <div class="column">
                    <div class="flex-row header">
                        <div class="flex-cell first" role="columnheader">{l s='Reference' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Requested' mod='roja45quotationspro'}</div>
                        <div class="flex-cell" role="columnheader">{l s='Expired' mod='roja45quotationspro'} </div>
                        <div class="flex-cell" role="columnheader">{l s='Purchased' mod='roja45quotationspro'} </div>
                    </div>
                    {foreach $quotes as $customerquote}
                        <div class="flex-row">
                            <div class="flex-cell quote-product-image" role="cell">
                                {$customerquote->reference}
                            </div>
                            <div class="flex-cell" role="cell">{dateFormat date=$customerquote->date_add full=false}</div>
                            <div class="flex-cell" role="cell">{if ($customerquote->expiry_date != '0000-00-00 00:00:00')}{dateFormat date=$customerquote->expiry_date full=true}{/if}</div>
                            <div class="flex-cell" role="cell">{if ($customerquote->purchase_date != '0000-00-00 00:00:00')}{dateFormat date=$customerquote->purchase_date full=true}{/if}</div>
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
