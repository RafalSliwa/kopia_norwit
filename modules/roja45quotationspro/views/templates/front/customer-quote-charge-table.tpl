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
            <th class="center title_box ">{l s='Charge Name' mod='roja45quotationspro'}</th>
            <th class="center title_box ">{l s='Shipping' mod='roja45quotationspro'} {if ($show_taxes==1)}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</th>
            <th class="center title_box ">{l s='Handling' mod='roja45quotationspro'} {if ($show_taxes==1)}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$shipping item=charge}
            <tr class="charge_row" data-id-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
                <td class="center">{$charge['charge_name']|escape:"html":"UTF-8"}</td>
                <td class="center">{if ($show_taxes==1)}{$charge.charge_amount_wt_formatted|escape:'html':'UTF-8'}{else}{$charge.charge_amount_formatted|escape:'html':'UTF-8'}{/if}</td>
                <td class="center">{if ($show_taxes==1)}{$charge.charge_handling_wt_formatted|escape:'html':'UTF-8'}{else}{$charge.charge_handling_formatted|escape:'html':'UTF-8'}{/if}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
