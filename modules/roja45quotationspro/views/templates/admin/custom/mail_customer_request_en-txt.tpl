Reference: @@quotation_reference@@

Dear @@customer_firstname@@ @@customer_lastname@@,

Thank you for your interest in our company and giving us an opportunity to provide you with a quotation.
We have received your request, and will respond as soon as possible with your quotation.
Please check the details below, and if you need to make any changes, please get in touch.

{if $show_summary}
    <!-- foreach quotation_products -->
    Product: @@product_title@@ (@@reference@@)
    Price @@tax_text@@: @@product_list_price@@
    Quantity: @@quantity@@
    Subtotal @@tax_text@@: @@product_subtotal@@

    -  Customizations
    <!-- foreach customizations --> - @@name@@ : @@value@@<!-- end foreach customizations -->
    <!-- end foreach quotation_products -->
{/if}


@@customer_form_text@@


If you would like to change the items on your quote you may either email us by replying to this email with your changes.

Many thanks


If you have any questions, please do not hesitate to contact us:  @@shop_email@@
@@shop_name@@ built by https://toolecommerce.com/
