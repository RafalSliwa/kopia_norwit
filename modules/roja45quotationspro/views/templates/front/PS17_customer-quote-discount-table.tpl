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

<div class="tr th">
    <div class="td">{l s='Discount name' mod='roja45quotationspro'}</div>
    <div class="td">{l s='Value' mod='roja45quotationspro'}</div>
</div>
{foreach from=$discounts item=discount}
    <div class="tr">
        <div class="td">{$discount['charge_name']|escape:'html':'UTF-8'}</div>
        <div class="td">
            {if ($discount['charge_method']  =='PERCENTAGE')}
                {$discount['charge_value']|escape:'html':'UTF-8'}%
            {elseif ($discount['charge_method']  =='AMOUNT')}
                {$discount['charge_value_formatted']|escape:'html':'UTF-8'}
            {/if}
        </div>
    </div>
{/foreach}