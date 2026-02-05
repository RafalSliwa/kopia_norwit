Référence: @@quotation_reference@@

Cher @@customer_firstname@@ @@customer_lastname@@,

Merci de l'intérêt que vous portez à notre entreprise et de nous donner l'opportunité de vous fournir un devis.
Nous avons bien reçu votre demande et vous répondrons dans les plus brefs délais avec votre devis.
Veuillez vérifier les détails ci-dessous, et si vous devez apporter des modifications, veuillez nous contacter.

{if $show_summary}
    <!-- foreach quotation_products -->
    Produit: @@product_title@@ (@@reference@@)
    Prix @@tax_text@@: @@product_list_price@@
    Quantité: @@quantity@@
    Subtotal @@tax_text@@: @@product_subtotal@@

    -  Personnalisations
    <!-- foreach customizations --> - @@name@@ : @@value@@<!-- end foreach customizations -->
    <!-- end foreach quotation_products -->
{/if}


@@customer_form_text@@


Si vous souhaitez modifier les éléments de votre devis, vous pouvez nous envoyer un e-mail en répondant à cet e-mail avec vos modifications.

Merci beaucoup


Si vous avez des questions, n'hésitez pas à nous contacter:  @@shop_email@@
@@shop_name@@ built by https://toolecommerce.com/