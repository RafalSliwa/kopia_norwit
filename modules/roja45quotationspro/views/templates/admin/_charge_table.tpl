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

<div class="table-responsive" style="overflow: auto;">
    <table class="table">
        <thead>
        <tr>
            <th class="center"><span class="title_box ">{l s='Shipping' mod='roja45quotationspro'}</span></th>
            <th class="center fixed-width-lg"><span class="title_box ">{l s='Cost' mod='roja45quotationspro'} {if $quotation->calculate_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span></th>
            <th class="center"><span class="title_box ">{l s='Handling' mod='roja45quotationspro'} {if $quotation->calculate_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</span></th>
            <th class="center"><span class="title_box ">{l s='Default' mod='roja45quotationspro'}</span></th>
            {if !($quotation->isLocked())}
                <th></th>
            {/if}
        </tr>
        </thead>
        <tbody>
        {foreach from=$shipping item=charge}
            <tr class="charge_row"
                data-id-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}"
                data-id-carrier="{$charge['id_carrier']|escape:"html":"UTF-8"}"
                data-charge="{$charge['charge_amount']}"
                data-charge-wt="{$charge['charge_amount_wt']}">
                <td class="center">{$charge['charge_name']|escape:"html":"UTF-8"}</td>
                <td class="center">
                    <div class="input-group">
                        <div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</div>
                        <input type="text" name="charge_amount" class="" value="{if $quotation->calculate_taxes}{$charge['charge_amount_wt_currency']}{else}{$charge['charge_amount_currency']}{/if}">
                    </div>
                </td>
                <td class="center">
                    <span>{if $quotation->calculate_taxes}{$charge['charge_handling_wt_formatted']}{else}{$charge['charge_handling_formatted']}{/if}</span>
                </td>
                <td class="center">
                    <span>{if $charge['charge_default']==1}<i class="icon-check"></i>{else}<i class="icon-cross"></i>{/if}</span>
                </td>
                {if !($quotation->isLocked())}
                    <td>
                        {if !isset($email)}
                            <div class="btn-group">
                                <button type="button"
                                        title="Save"
                                        class="btn btn-primary btn-save-charge"
                                        data-id-roja45-quotation="{$charge['id_roja45_quotation']|escape:"html":"UTF-8"}"
                                        data-id-roja45-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
                                    <i class="icon-save"></i>
                                </button>
                                <button type="button"
                                        title="Delete"
                                        class="btn btn-primary btn-delete-charge"
                                        data-id-roja45-quotation="{$charge['id_roja45_quotation']|escape:"html":"UTF-8"}"
                                        data-id-roja45-quotation-charge="{$charge['id_roja45_quotation_charge']|escape:"html":"UTF-8"}">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>

                        {/if}
                        {/if}
                    </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
