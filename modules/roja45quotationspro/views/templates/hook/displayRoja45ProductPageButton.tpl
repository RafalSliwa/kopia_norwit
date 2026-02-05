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

{if !$roja45_quotation_enabled}
    {include file='catalog/_partials/product-add-to-cart.tpl'}
{else}
    {if !$roja45_quotation_hideaddtocart}
        {include file='catalog/_partials/product-add-to-cart.tpl'}
    {/if}
    {include file="module:roja45quotationspro/views/templates/hook/displayProductAdditionalInfo.tpl"}
{/if}