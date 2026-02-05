Reference: @@quotation_reference@@

You have received a new quotation request

<!-- foreach quotation_products -->
Product: @@product_title@@ (@@reference@@)
Price @@tax_text@@: @@product_list_price@@
Quantity: @@quantity@@
Subtotal @@tax_text@@: @@product_subtotal@@

-  Customizations
<!-- foreach customizations --> - @@name@@ : @@value@@<!-- end foreach customizations -->
<!-- end foreach quotation_products -->

@@customer_form_text@@

@@shop_name@@ built by https://toolecommerce.com/