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
            <p>Dear @@customer_firstname@@ @@customer_lastname@@,</p>
            <p>Thank you for your interest in our company and our products.</p>
            <p>Please find below a summary of your request, we will provide you with your quotation at the earliest opportunity.</p>
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
                    <th class="header summary center">Quotation Number</th>
                    <th class="header summary center">Created</th>
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
<table class="table" style="width: 100%; border: 1px solid #d6d4d4; background-color: #ffffff;" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tbody>
    <tr class="product product-header align-center" align="center">
        <th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; background-color: #eaeaea; text-align: center;" width="12%">&nbsp;</th>
        <th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; background-color: #eaeaea; text-align: center;" align="center" width="13%">Product</th>
        <!--{if $show_product_customizations}--><th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; background-color: #eaeaea; text-align: center;" width="13%">Customizations</th><!--{/if}-->
        <!--{if $show_prices}--><th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; background-color: #eaeaea; text-align: center;" width="10%">Price @@tax_text@@</th><!--{/if}-->
        <th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; background-color: #eaeaea; text-align: center;" align="center" width="6%">Quantity</th>
        <th class="product header center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; background-color: #eaeaea; text-align: center;" align="center" width="8%">Subtotal @@tax_text@@</th>
    </tr>
    <!-- foreach quotation_products -->
    <tr class="product product-row align-center" data-do-not-remove="true">
        <td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;"><img class="product_image" src="@@image_url@@" alt="@@image_legend@@" /></td>
        <td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;" align="center">@@product_title@@ (@@reference@@)<br />@@attributes@@</td>
        <!--{if $show_product_customizations}-->
        <td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;"><!-- foreach customizations --> - @@name@@ : @@value@@<br /><!-- end foreach customizations --></td>
        <!--{/if}-->
        <!--{if $show_prices}--><td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;" align="center">@@product_list_price@@</td><!--{/if}-->
        <td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;">@@quantity@@</td>
        <td class="product center" style="border-right: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC; font-size: 9px; text-align: center;" align="center">@@product_subtotal@@</td>
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
        <th class="header-left" style="width: 25%;">Total Products @@tax_text@@</th>
        <th class="header-right" style="width: 25%;">@@quotation_subtotal@@</th>
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
