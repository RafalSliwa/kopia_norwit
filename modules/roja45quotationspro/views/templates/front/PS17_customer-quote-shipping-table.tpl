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
    <div class="td">{l s='Shipping' mod='roja45quotationspro'}</div>
    <div class="td">{l s='Cost' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
    <div class="td">{l s='Handling' mod='roja45quotationspro'} {if $show_taxes}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
</div>
{foreach from=$shipping item=charge}
<div class="tr">
    <div class="td">{$charge['charge_name']|escape:'html':'UTF-8'}</div>
    <div class="td">
        <span>{if $show_taxes}{$charge['charge_amount_wt_formatted']|escape:'html':'UTF-8'}{else}{$charge['charge_amount_formatted']|escape:'html':'UTF-8'}{/if}</span>
    </div>
    <div class="td">
        <span>{if $show_taxes}{$charge['charge_handling_wt_formatted']|escape:'html':'UTF-8'}{else}{$charge['charge_handling_formatted']|escape:'html':'UTF-8'}{/if}</span>
    </div>
</div>
{/foreach}
