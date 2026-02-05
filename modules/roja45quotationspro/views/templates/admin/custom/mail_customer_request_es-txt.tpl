Referencia: @@quotation_reference@@

Estimado/a @@customer_firstname@@ @@customer_lastname@@,

Gracias por su interés en nuestra empresa y por darnos la oportunidad de ofrecerle un presupuesto.
Hemos recibido su solicitud y le responderemos lo antes posible con su cotización.
Verifique los detalles a continuación y, si necesita realizar algún cambio, comuníquese con nosotros.

{if $show_summary}
    <!-- foreach quotation_products -->
    Producto: @@product_title@@ (@@reference@@)
    Precio @@tax_text@@: @@product_list_price@@
    Catidad: @@quantity@@
    Subtotal @@tax_text@@: @@product_subtotal@@

    -  Personalizaciones
    <!-- foreach customizations --> - @@name@@ : @@value@@<!-- end foreach customizations -->
    <!-- end foreach quotation_products -->
{/if}


@@customer_form_text@@


Si desea cambiar los elementos de su cotización, puede enviarnos un correo electrónico respondiendo a este correo electrónico con sus cambios.

Muchas gracias


Si tiene alguna pregunta, no dude en ponerse en contacto con nosotros.:  @@shop_email@@
@@shop_name@@ hecho por https://toolecommerce.com/