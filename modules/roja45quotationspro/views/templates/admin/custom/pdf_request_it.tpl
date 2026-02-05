<!-- BEGIN HEADER --> <!-- END HEADER -->
<table id="body" style="margin: 0;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12">
<table id="addresses-tab">
<tbody>
<tr>
<th class="left">&nbsp;</th>
<th class="right">@@shop_name@@</th>
</tr>
<tr>
<td class="customer left">@@customer_title@@ @@customer_firstname@@ @@customer_lastname@@</td>
<td class="shop right">&nbsp;</td>
</tr>
<tr>
<td class="customer left">&nbsp;</td>
<td class="shop right">@@shop_address@@</td>
</tr>
<tr>
<td class="customer left">&nbsp;</td>
<td class="shop right">@@shop_phone@@</td>
</tr>
<tr>
<td class="customer left">&nbsp;</td>
<td class="shop right">&nbsp;</td>
</tr>
<tr>
<td class="customer left">&nbsp;</td>
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
<p>Caro @@customer_firstname@@ @@customer_lastname@@</p>
<p>Grazie per il vostro interesse nella nostra azienda e nei nostri prodotti.</p>
<p>Di seguito trovi un riepilogo della tua richiesta, ti forniremo il tuo preventivo nel più breve tempo possibile.</p>
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
</tr>
<tr>
<td class="summary center">@@quotation_reference@@</td>
<td class="summary center">@@quotation_date_created@@</td>
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
<th class="product header" width="13%"><span style="font-size: 8pt;">Prodotto</span></th>
<th class="product header" width="13%"><span style="font-size: 8pt;">Personalizzazioni</span></th>
<th class="product header" width="10%"><span style="font-size: 8pt;">Prezzo @@tax_text@@</span></th>
<th class="product header" width="6%"><span style="font-size: 8pt;">Quantità</span></th>
<th class="product header" width="6%"><span style="font-size: 8pt;">Imposta</span></th>
<th class="product header" width="8%"><span style="font-size: 8pt;">Valutare</span></th>
</tr>
<!-- foreach quotation_products -->
<tr class="product product-row align-center" data-do-not-remove="true">
<td class="product center"><img class="product_image" src="@@image_url@@" alt="@@image_legend@@" /></td>
<td class="product center">@@product_title@@ (@@reference@@)<br />@@attributes@@</td>
<td class="product center"><!-- foreach customizations --> - @@name@@ : @@value@@<br /><!-- end foreach customizations --></td>
<td class="product center">@@product_list_price@@</td>
<td class="product center">@@quantity@@</td>
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
<!-- BEGIN FOOTER -->
<p>&nbsp;</p>
<!-- DO NOT REMOVE THIS LINE -->
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 33.33%;" align="left" width="50%">&nbsp;</td>
<td style="width: 33.33%;" align="right" width="50%">@@shop_address_line@@ <br />@@shop_phone@@ <br />@@shop_fax@@</td>
</tr>
</tbody>
</table>
<!-- END FOOTER -->
<p>&nbsp;</p>
<!-- DO NOT REMOVE THIS LINE -->
