<!-- BEGIN HEADER --> <!-- END HEADER -->
<table id="body" style="margin: 0;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td colspan="12">
<table id="addresses-tab">
<tbody>
<tr>
<th class="left">À</th>
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
<p>Cher @@customer_title@@ @@customer_firstname@@ @@customer_lastname@@</p>
<p>Nous vous remercions de l'intérêt que vous portez à notre entreprise et de nous donner l'opportunité de vous fournir un devis.</p>
<p>Nous sommes heureux de vous fournir un devis comme suit:</p>
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
<th class="header summary center">Numéro de devis</th>
<th class="header summary center">Établi</th>
<th class="header summary center">Expire</th>
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
<th class="product header" width="11%">&nbsp;</th>
<th class="product header" width="12%">Produit</th>
    <!--{if $show_product_customizations}--><th class="product header" width="13%">Personnalisations</th><!--{/if}-->
    <!--{if $show_product_comments}--><th class="product header" width="13%">Commentaire</th><!--{/if}-->
    <th class="product header" width="7%">Prix @@tax_text@@</th>
    <!--{if $show_product_discounts}--><th class="product header" width="5%">Réductions</th><!--{/if}-->
<th class="product header" width="7%">Offre @@tax_text@@</th>
    <!--{if $show_customization_cost}-->
    <th class="product header" width="7%">Personnalisation @@tax_text@@</th>
    <!--{/if}-->
<th class="product header" width="6%">Quantit&eacute;</th>
<th class="product header" width="7%">Total @@tax_text@@</th>
<th class="product header" width="6%">Imp&ocirc;t</th>
<th class="product header" width="6%">Taux d'imposition</th>
    <!--{if $show_ecotax}--><th class="product header" width="6%">Ecotax</th><!--{/if}-->
</tr>
<!-- foreach quotation_products -->
<tr class="product product-row align-center" data-do-not-remove="true">
<td class="product center"><img class="product_image" src="@@image_url@@" alt="@@image_legend@@" /></td>
<td class="product center">@@product_title@@ (@@reference@@)<br />@@attributes@@</td>
    <!--{if $show_product_customizations}--><td class="product center"><!-- foreach customizations --> - @@name@@ : @@value@@<br /><span style="font-size: 8pt;"><!-- end foreach customizations --></span></td><!--{/if}-->
    <!--{if $show_product_comments}--><td class="product center">@@comment@@</td><!--{/if}-->
<td class="product center">@@product_list_price@@</td>
    <!--{if $show_product_discounts}--><td class="product center">@@product_discount_prefix@@@@product_discount@@@@product_discount_postfix@@</td><!--{/if}-->
<td class="product center">@@product_unit_price@@</td>
    <!--{if $show_customization_cost}-->
    <td class="product center">@@product_customization_total@@</td>
    <!--{/if}-->
<td class="product center">@@quantity@@</td>
<td class="product center">@@product_subtotal@@</td>
<td class="product center">@@product_tax@@</td>
<td class="product center">@@tax_rate@@</td>
    <!--{if $show_ecotax}--><td class="product center">@@product_ecotax@@</td><!--{/if}-->
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
            <th class="charge header center">Expédition</th>
            <th class="charge header center">Délai</th>
            <th class="charge header center">Cost (exc.)</th>
            <th class="charge header center">Impot</th>
            <th class="charge header center">Cost (inc.)</th>
        </tr>
        <!-- foreach shipping -->
        <tr>
            <td class="charge center">@@charge_name@@</td>
            <td class="charge center">@@delay@@</td>
            <td class="charge center">@@charge_amount_exc@@</td>
            <td class="charge center">@@charge_amount_tax@@</td>
            <td class="charge center">@@charge_amount_inc@@</td>
        </tr>
        <!-- end foreach shipping --></tbody>
    </table>
</td>
<td class="discounts">
<table id="discounts-tab">
<tbody>
<tr>
<th class="discount header center">Réduction</th>
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
<tr id="total_products">
    <td style="width: 50%;">&nbsp;</td>
    <th class="header-left" style="width: 25%;">Produits totaux @@tax_text@@</th>
    <th class="header-right" style="width: 25%;">@@quotation_subtotal@@</th>
</tr>
<!--{if $show_customization_cost}-->
<tr id="total_customization">
    <td style="width: 50%;">&nbsp;</td>
    <th class="header-left" style="width: 25%;">Personnalisation @@tax_text@@</th>
    <th class="header-right" style="width: 25%;">@@quotation_customizations@@</th>
</tr>
<!--{/if}-->
<!--{if $show_ecotax}-->
<tr id="total_ecotax">
    <td style="width: 50%;">&nbsp;</td>
    <th class="header-left" style="width: 25%;">Ecotax @@tax_text@@</th>
    <th class="header-right" style="width: 25%;">@@quotation_ecotax@@</th>
</tr>
<!--{/if}-->
<tr id="total_discounts">
<td style="width: 50%;">&nbsp;</td>
<th class="header-left" style="width: 25%;">Discounts @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_discounts@@</th>
</tr>
<tr id="total_shipping">
<td style="width: 50%;">&nbsp;</td>
<th class="header-left" style="width: 25%;">Shipping @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_shipping@@</th>
</tr>
<tr id="total_handling">
<td style="width: 50%;" valign="top">&nbsp;</td>
<th class="header-left" style="width: 25%;">Handling @@tax_text@@</th>
<th class="header-right" style="width: 25%;">@@quotation_handling@@</th>
</tr>
<tr id="total_taxes">
<td style="width: 50%;" valign="top">&nbsp;</td>
<th class="header-left" style="width: 25%;">Taxes</th>
<th class="header-right" style="width: 25%;">@@quotation_tax@@</th>
</tr>
<tr id="total_quotation">
<td style="width: 50%;">&nbsp;</td>
<th class="header-left" style="width: 25%;">Total @@tax_text@@</th>
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
<p class="note">- Les prix sont valables jusqu'à la date d'expiration du devis fourni.</p>
<p class="note">- Si vous n'êtes pas sûr de l'une des informations ou des prix fournis, n'hésitez pas à nous contacter.</p>
<p class="note">- Votre devis a été fourni dans la devise demandée. Veuillez noter que les fluctuations des devises peuvent entraîner des modifications du prix qui vous a été proposé. Nous nous réservons le droit de modifier ou d'annuler ce devis à tout moment.</p>
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
