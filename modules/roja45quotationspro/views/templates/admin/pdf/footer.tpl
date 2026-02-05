{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author          Roja45
*  @copyright       2016 roja45
*}

{if ($show_exchange_rate != 1)}
    <table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
        <tr>
            <td class="linkbelow">
                <span><font size="7">{l s='Your quote has been provided in your requested currency.  Please be aware that currency fluctuations may result in changes to the price you have been quoted.  We reserve the right to change or cancel this quote at any time.' mod='roja45quotationspro'}</font></span>
            </td>
        </tr>
    </table>
{/if}
<table style="width: 100%;">
    <tr>
        <td width="25%" align="left" style="width:33.33%">
            {if $logo_path}
                <img src="{$logo_path|escape:'htmlall':'UTF-8'}" style="width:{$width_logo|escape:'htmlall':'UTF-8'}px; height:{$height_logo|escape:'htmlall':'UTF-8'}px;" />
            {/if}
        </td>
        <td width="25%" align="left" style="width:33.33%">
            <font size="6">
                {if isset($shop_details)}
                    {$shop_details|escape:'html':'UTF-8'}
                    <br/>
                {/if}

                {if isset($free_text)}
                    {$free_text|escape:'html':'UTF-8'}
                    <br/>
                {/if}
            </font>
        </td>
        <td width="50%" align="right" style="width:33.33%">
            <font size="6">
                {$shop_address}<br/> {* HTML Content *}
                {if !empty($shop_phone) OR !empty($shop_fax)}
                    {l s='For more assistance, contact support:' mod='roja45quotationspro'}
                    <br/>
                    {if !empty($shop_phone)}
                        {l s='Tel: %s' sprintf=[$shop_phone|escape:'html':'UTF-8'] mod='roja45quotationspro'}
                    {/if}
                    <br/>
                    {if !empty($shop_fax)}
                        {l s='Fax: %s' sprintf=[$shop_fax|escape:'html':'UTF-8'] mod='roja45quotationspro'}
                    {/if}
                    <br/>
                {/if}
            </font>
        </td>
    </tr>
</table>