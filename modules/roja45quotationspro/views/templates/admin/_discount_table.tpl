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

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th><span class="title_box ">{l s='Discount name' mod='roja45quotationspro'}</span></th>
            <th><span class="title_box ">{l s='Amount' mod='roja45quotationspro'} {if $quotation->calculate_taxes}{l s='(inc)' mod='roja45quotationspro'}{else}{l s='(exc)' mod='roja45quotationspro'}{/if}</span></th>
            {if !($quotation->isLocked())}
                <th></th>
            {/if}
        </tr>
        </thead>
        <tbody>
        {foreach from=$discounts item=discount}
            <tr class="discount_row">
                <td>{$discount['charge_name']|escape:"html":"UTF-8"}</td>
                <td>
                    {if ($discount['charge_method']=='PERCENTAGE')}
                        {$discount['charge_value']|escape:"html":"UTF-8"|string_format:"%.2f"}%
                    {elseif ($discount['charge_method']=='VALUE')}
                    {if $quotation->calculate_taxes}{$discount['charge_amount_wt_formatted']|escape:"html":"UTF-8"}{else}{$discount['charge_amount_formatted']|escape:"html":"UTF-8"}{/if}
                    {/if}
                </td>
                {if !($quotation->isLocked())}
                    <td>
                        <a href="#" class="submitDeleteVoucher pull-right"
                           data-id-roja45-quotation="{$discount['id_roja45_quotation']|escape:"html":"UTF-8"}"
                           data-id-roja45-quotation-charge="{$discount['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
                            <i class="icon-minus-sign"></i>
                            {l s='Delete' mod='roja45quotationspro'}
                        </a>
                    </td>
                {/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
