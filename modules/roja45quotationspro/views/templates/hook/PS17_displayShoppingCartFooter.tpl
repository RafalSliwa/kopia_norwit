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

{if $roja45_contains_products && !$roja45_contains_quote}

    {if $roja45_convert_to_quote || $roja45_download_pdf || $roja45_email_pdf}
        <div class="card request-quote-card">
            <div class="card-block text-sm-center">
                <h5 class="h5">{l s='Quotation Options' mod='roja45quotationspro'}</h5>
                <div class="button-container">
                {if $roja45_convert_to_quote}
                <div class="text-sm-center">
                    <form action="{$request_quote_controller}" method="post">
                        <input type="hidden" name="id_cart" value="{$id_cart}"/>
                        <button type="submit" title="{l s='Select this option to create a quotation request with your cart content.' mod='roja45quotationspro'}" class="btn btn-primary">{l s='Request a Quote' mod='roja45quotationspro'}</button>
                    </form>
                </div>
                {/if}
                {if $roja45_download_pdf}
                <div class="text-sm-center">
                    <form action="{$download_pdf_controller}" method="post">
                        <input type="hidden" name="id_cart" value="{$id_cart}"/>
                        <button type="submit" title="{l s='Download a PDF copy of your cart as a quote.' mod='roja45quotationspro'}" class="btn btn-primary">{l s='Download As PDF' mod='roja45quotationspro'}</button>
                    </form>
                </div>
                {/if}
                </div>
            </div>
        </div>
    {/if}
{/if}
