Referencia: @@quotation_reference@@


Estimado/a @@customer_firstname@@ @@customer_lastname@@,

Gracias por su inter&eacute;s en nuestra empresa y por darnos la oportunidad de ofrecerle un presupuesto.
Nos complace ofrecerle una cotizaci&oacute;n de la siguiente manera:

**** SU PRESUPUESTO ****
<!-- foreach quotation_products -->
Producto: @@product_title@@ (@@reference@@)
Comentario: @@comment@@
Precio @@tax_text@@: @@product_list_price@@
Oferta @@tax_text@@: @@product_unit_price@@
Cantidad: @@quantity@@
Subtotal @@tax_text@@: @@product_subtotal@@
Impuesto: @@product_tax@@
Tasa: @@tax_rate@@%
-  Personalizaciones
<!-- foreach customizations -->- @@name@@ : @@value@@
<!-- end foreach customizations -->

<!-- end foreach quotation_products -->
<!-- foreach charges -->
Carga:
Valor @@tax_text@@:
@@charge_name@@     @@charge_amount@@
<!-- end foreach charges -->
<!-- foreach discounts -->
Descuento: @@discount_name@@
Valor @@tax_text@@: @@discount_amount@@
<!-- end foreach discounts -->

Descuentos @@tax_text@@
@@quotation_discounts@@

Cargas @@tax_text@@
@@quotation_charges@@

Transporte @@tax_text@@
@@quotation_shipping@@

Manejo @@tax_text@@
@@quotation_handling@@

Total Productos @@tax_text@@
@@quotation_subtotal@@

Impuestos
@@quotation_tax@@

Total @@tax_text@@
@@quotation_total@@

{if $show_account}

    *************************
    Su Nueva Cuenta de Cliente

    Para que puedas revisar tu cotizaci&oacute;n y realizar tu compra m&aacute;s f&aacute;cilmente, hemos creado tu cuenta en nuestro sistema.
    Inicie sesi&oacute;n utilizando el enlace y los detalles de la cuenta que se proporcionan a continuaci&oacute;n.
    En la seccion Mis Cotizaciones encontrar&aacute; sus cotizaciones aprobadas, si est&aacute; contento de continuar, seleccione la opci&oacute;n Agregar al carrito y verifique como de costumbre con los precios acordados: @@customer_quotes_link@@

    Enlace a su cuenta: @@customer_account_quotation_link@@
    Usuario: @@customer_username@@
    Contrase&ntilde;a Temporal: @@customer_temporary_password@@
    Por favor, cambia su contrase&ntilde;a lo antes posible.
    *************************
    
{/if}

Puede adquirir esta cotización ahora copiando el siguiente enlace en su navegador: @@quotation_purchase_link@@
Deber&aacute; iniciar sesi&oacute;n en su cuenta para ver el precio ofrecido.


Si quiere cambiar los elementos de su cotizaci&oacute;n, puede enviarnos un correo electr&oacute;nico respondiendo a este correo electr&oacute;nico, con sus cambios, o crear una nueva solicitud de cotizaci&oacute;n a trav&eacute;s del sitio web.

- Los precios son v&aacute;lidos hasta la fecha de vencimiento de la cotizaci&oacute;n proporcionada.
- Si no est&aacute; seguro de la informaci&oacute;n o los precios proporcionados, no dude en ponerse en contacto con nosotros.
- Su cotizaci&oacute;n se ha proporcionado en la moneda solicitada. Tenga en cuenta que las fluctuaciones monetarias pueden provocar cambios en el precio que se le ha cotizado. Nos reservamos el derecho de cambiar o cancelar esta cotizaci&oacute;n en cualquier momento.


Si tiene alguna pregunta, no dude en contactarnos:  @@shop_email@@
@@shop_name@@ hecho por https://toolecommerce.com/