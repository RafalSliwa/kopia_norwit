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

{foreach $quotations as $quotation}
<a class="notif" href="{$quotation.link}">{$quotation.id_roja45_quotation} : {$quotation.reference} - <strong>{$quotation.firstname} {$quotation.lastname}</strong> - {$quotation.difference}</a>
{/foreach}