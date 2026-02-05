Référence: @@quotation_reference@@

Vous avez reçu une nouvelle demande de devis

<!-- foreach quotation_products -->
Produit: @@product_title@@ (@@reference@@)
Prix @@tax_text@@: @@product_list_price@@
Quantité: @@quantity@@
Subtotal @@tax_text@@: @@product_subtotal@@

-  Personnalisations
<!-- foreach customizations --> - @@name@@ : @@value@@<!-- end foreach customizations -->
<!-- end foreach quotation_products -->

@@customer_form_text@@

@@shop_name@@ construit par https://toolecommerce.com/