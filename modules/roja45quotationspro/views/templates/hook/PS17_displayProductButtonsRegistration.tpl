{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*}

<div id="roja45quotationspro_registration_block" class="roja45quotationspro_registration_block no-print">
    <div class="card card-block">
        <h4 class="h4 card-title">{l s='Login or Register For Quotation Options' mod='roja45quotationspro'}</h4>
        <p>{l s='If you would like to request a quotation, or have specific needs, please login to your account or register for quotation options.' mod='roja45quotationspro'}</p>
        <div class="clearfix">
            <a class="btn btn-primary float-xs-right" href="{if isset($login_url)}{$login_url}{else}{'index.php?controller=authentication'}{/if}">{l s='Login/Register' mod='roja45quotationspro'}</a>
        </div>
    </div>
</div>
