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
            <p>Estimado/a @@customer_firstname@@ @@customer_lastname@@</p>
            <p>Gracias por su inter&eacute;s en nuestra empresa y nuestros productos.</p>
            <p>A continuaci&oacute;n, encontrar&aacute; un resumen de su solicitud, le proporcionaremos su cotizaci&oacute;n lo antes posible.</p>
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
                    <th class="header summary center">N&uacute;mero de cotizaci&oacute;n</th>
                    <th class="header summary center">Creado</th>
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
        <th class="product header" width="13%"><span style="font-size: 8pt;">Producto</span></th>
        <!--{if $show_product_customizations}-->
        <th class="product header" width="13%"><span style="font-size: 8pt;">Personalizaciones</span></th>
        <!--{/if}--> <!--{if $show_prices}-->
        <th class="product header" width="10%"><span style="font-size: 8pt;">Precio Unitario @@tax_text@@</span></th>
        <!--{/if}-->
        <th class="product header" width="6%"><span style="font-size: 8pt;">Cantidad</span></th>
        <th class="product header" width="6%"><span style="font-size: 8pt;">Impuesto</span></th>
        <th class="product header" width="8%"><span style="font-size: 8pt;">Tasa</span></th>
    </tr>
    <!-- foreach quotation_products -->
    <tr class="product product-row align-center" data-do-not-remove="true">
        <td class="product center"><img class="product_image" src="@@image_url@@" alt="@@image_legend@@" /></td>
        <td class="product center">@@product_title@@ (@@reference@@)<br />@@attributes@@</td>
        <!--{if $show_product_customizations}-->
        <td class="product center"><!-- foreach customizations --> - @@name@@ : @@value@@<br /><!--{/if}--><!-- end foreach customizations --></td>
        <!--{if $show_prices}-->
        <td class="product center">@@product_list_price@@</td>
        <!--{/if}-->
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
        <th class="header-left" style="width: 25%;">Total Productos @@tax_text@@</th>
        <th class="header-right" style="width: 25%;">@@quotation_subtotal@@</th>
    </tr>
    <tr id="total_taxes">
        <td style="width: 50%;" valign="top">&nbsp;</td>
        <th class="header-left" style="width: 25%;">Impuestos</th>
        <th class="header-right" style="width: 25%;">@@quotation_tax@@</th>
    </tr>
    <tr id="total_quotation">
        <td style="width: 50%;">&nbsp;</td>
        <th class="header-left" style="width: 25%;">Total @@tax_text@@</th>
        <th class="header-right" style="width: 25%;">@@quotation_total_inc@@</th>
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
