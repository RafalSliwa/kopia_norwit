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
            <th><span class="title_box ">{l s='Value' mod='roja45quotationspro'}</span></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$discounts item=discount}
            <tr class="discount_row">
                <td>{$discount['charge_name']|escape:'html':'UTF-8'}</td>
                <td>
                    {if ($discount['charge_method']  =='PERCENTAGE')}
                        {$discount['charge_value']|escape:'html':'UTF-8'}%
                    {elseif ($discount['charge_method']  =='AMOUNT')}
                        {$discount['charge_value_formatted']|escape:'html':'UTF-8'}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>