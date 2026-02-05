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
    <div class="td">{l s='shipping' d='Modules.Roja45quotationspro.Shop'}</div>
    <div class="td">{l s='cost' d='Modules.Roja45quotationspro.Shop'} {if $show_taxes}{l s='(inc.)' d='Modules.Roja45quotationspro.Shop'}{else}{l s='(exc.)' d='Modules.Roja45quotationspro.Shop'}{/if}</div>
    <div class="td">{l s='handling' d='Modules.Roja45quotationspro.Shop'} {if $show_taxes}{l s='(inc.)' d='Modules.Roja45quotationspro.Shop'}{else}{l s='(exc.)' d='Modules.Roja45quotationspro.Shop'}{/if}</div>
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
