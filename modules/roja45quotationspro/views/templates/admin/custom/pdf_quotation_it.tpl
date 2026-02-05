<!-- BEGIN HEADER --> <!-- END HEADER -->
<table id="body" style="margin: 0;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12">
<table id="addresses-tab">
<tbody>
<tr>
<th class="left">A</th>
<th class="right">@@shop_name@@</th>
</tr>
<tr>
<td class="customer left">@@customer_address@@</td>
<td class="shop right">@@shop_address@@</td>
</tr>
<tr>
<td class="customer left">@@customer_phone@@</td>
<td class="shop right">@@shop_phone@@</td>
</tr>
<tr>
<td class="customer left">&nbsp;</td>
<td class="shop right">&nbsp;</td>
</tr>
<tr>
<td class="customer left">@@customer_email@@</td>
<td class="shop right">@@shop_email@@</td>
</tr>
<tr>
<td class="customer">&nbsp;</td>
<td class="shop right"><img class="img-responsive" src="@@shop_logo@@" width="@@shop_logo_width@@" height="@@shop_logo_height@@" /></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="12" height="20">&nbsp;</td>
</tr>
<!-- TVA Info -->
<tr>
<td colspan="12">
<p>Caro @@customer_title@@ @@customer_firstname@@ @@customer_lastname@@</p>
<p>Grazie per il vostro interesse per la nostra azienda e per averci dato l'opportunità di fornirvi un preventivo.</p>
<p>Siamo lieti di fornirvi un preventivo come segue:</p>
</td>
</tr>
<tr>
<td colspan="12" height="20">&nbsp;</td>
</tr>
<tr>
<td colspan="12">
<table id="summary-tab" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<th class="header summary center">Numero preventivo</th>
<th class="header summary center">Creata</th>
<th class="header summary center">Scade</th>
</tr>
<tr>
<td class="summary center">@@quotation_reference@@</td>
<td class="summary center">@@quotation_date_created@@</td>
<td class="summary center">@@quotation_expiry_date@@ @@quotation_expiry_time@@</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="12" height="20">&nbsp;</td>
</tr>
</tbody>
</table>
<table id="product-tab" class="product">
<tbody>
<tr class="product product-header" style="height: 20px;">
<th class="product header" width="12%">&nbsp;</th>
<th class="product header" width="13%">Prodotto</th>
<th class="product header" width="13%">Personalizzazioni</th>
<th class="product header" width="14%">Commento</th>
<th class="product header" width="10%">Prezzo @@tax_text@@</th>
<th class="product header" width="10%">Offerta @@tax_text@@</th>
<th class="product header" width="6%">Quantità</th>
<th class="product header" width="8%">Totale parziale @@tax_text@@</th>
<th class="product header" width="6%">Imposta</th>
<th class="product header" width="8%">Valutare</th>
</tr>
<!-- foreach quotation_products -->
<tr class="product product-row align-center" data-do-not-remove="true">
<td class="product center"><img class="product_image" src="@@image_url@@" alt="@@image_legend@@" /></td>
<td class="product center">@@product_title@@ (@@reference@@)<br />@@attributes@@</td>
<td class="product center"><!-- foreach customizations --> - @@name@@ : @@value@@<br /><span style="font-size: 8pt;"><!-- end foreach customizations --></span></td>
<td class="product center">@@comment@@</td>
<td class="product center">@@product_list_price@@</td>
<td class="product center">@@product_unit_price@@</td>
<td class="product center">@@quantity@@</td>
<td class="product center">@@product_subtotal@@</td>
<td class="product center">@@product_tax@@</td>
<td class="product center">@@tax_rate@@</td>
</tr>
<!-- end foreach quotation_products --></tbody>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12" height="20">&nbsp;</td>
</tr>
</tbody>
</table>
<table id="additionals-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr class="additionals-row">
<td class="charges">
<table id="charges-tab">
<tbody>
<tr>
<th class="charge header center">Costo</th>
<th class="charge header center">Amount @@tax_text@@</th>
</tr>
<!-- foreach charges -->
<tr>
<td class="charge center">@@charge_name@@</td>
<td class="charge center">@@charge_amount@@</td>
</tr>
<!-- end foreach charges --></tbody>
</table>
</td>
<td class="discounts">
<table id="discounts-tab">
<tbody>
<tr>
<th class="discount header center">Sconto</th>
<th class="discount header scentermall">Amount @@tax_text@@</th>
</tr>
<!-- foreach discounts -->
<tr>
<td class="discount center">@@discount_name@@</td>
<td class="discount center">@@discount_amount@@</td>
</tr>
<!-- end foreach discounts --></tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12" height="30">&nbsp;</td>
</tr>
</tbody>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr id="total_discounts">
<td style="width: 50%;">&nbsp;</td>
<th class="header" style="width: 25%;">Sconti @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_discounts@@</th>
</tr>
<tr id="total_charges">
<td style="width: 50%;">&nbsp;</td>
<th class="header" style="width: 25%;">Costi @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_charges@@</th>
</tr>
<tr id="total_shipping">
<td style="width: 50%;">&nbsp;</td>
<th class="header" style="width: 25%;">Spedizione @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_shipping@@</th>
</tr>
<tr id="total_handling">
<td style="width: 50%;" valign="top">&nbsp;</td>
<th class="header" style="width: 25%;">Gestione @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_handling@@</th>
</tr>
<tr id="total_products">
<td style="width: 50%;">&nbsp;</td>
<th class="header" style="width: 25%;">Prodotti totali @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_subtotal@@</th>
</tr>
<tr id="total_taxes">
<td style="width: 50%;" valign="top">&nbsp;</td>
<th class="header" style="width: 25%;">Le tasse</th>
<th class="header-right" style="width: 25%;">@@quotation_tax@@</th>
</tr>
<tr id="total_quotation">
<td style="width: 50%;">&nbsp;</td>
<th class="header" style="width: 25%;">Totale @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_total_inc@@</th>
</tr>
</tbody>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12" height="30">&nbsp;</td>
</tr>
<tr>
<td class="notes">
<p class="note">- I prezzi sono validi fino alla data di scadenza del preventivo fornito.</p>
<p class="note">- Se non sei sicuro delle informazioni o dei prezzi forniti, non esitare a contattarci.</p>
<p class="note">- Il tuo preventivo è stato fornito nella valuta richiesta. Tieni presente che le fluttuazioni valutarie possono comportare modifiche al prezzo che ti è stato indicato. Ci riserviamo il diritto di modificare o annullare questo preventivo in qualsiasi momento.</p>
</td>
</tr>
</tbody>
</table>
<!-- BEGIN FOOTER -->
<p>&nbsp;</p>
<!-- DO NOT REMOVE THIS LINE -->
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 33.33%;" align="left" width="50%">&nbsp;</td>
<td style="width: 33.33%;" align="right" width="50%">@@shop_name@@ @@shop_address_line@@ <br />@@shop_phone@@ <br />@@shop_fax@@</td>
</tr>
</tbody>
</table>
<!-- END FOOTER -->
<p>&nbsp;</p>
<!-- DO NOT REMOVE THIS LINE -->
