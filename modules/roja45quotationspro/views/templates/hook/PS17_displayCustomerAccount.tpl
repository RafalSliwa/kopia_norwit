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

<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12"
   id="quotes-link"
   href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'getCustomerQuotes']}">
      <span class="link-item">
           <svg class="customer-account-link-item-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5s1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5S5.5 6.83 5.5 6S4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5s1.5-.68 1.5-1.5s-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"/></svg>
        <span>{l s='Open Quotes' mod='roja45quotationspro'}</span>
      </span>
</a>

<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12"
   id="quote-history-link"
   href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'getCustomerQuoteHistory']}">
      <span class="link-item">
           <svg class="customer-account-link-item-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5s1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5S5.5 6.83 5.5 6S4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5s1.5-.68 1.5-1.5s-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"/></svg>
        <span>{l s='Quote History' mod='roja45quotationspro'}</span>
      </span>
</a>
