Reference: @@quotation_reference@@


Dear @@customer_firstname@@ @@customer_lastname@@,

Thank you for your interest in our company and giving us an opportunity to provide you with a quotation.
We are pleased to provide you with a quote as follows:

**** YOUR QUOTE ****
<!-- foreach quotation_products -->
Product: @@product_title@@ (@@reference@@)      
Comment: @@comment@@     
Price @@tax_text@@: @@product_list_price@@      
Offer @@tax_text@@: @@product_unit_price@@     
Quantity: @@quantity@@     
Subtotal @@tax_text@@: @@product_subtotal@@     
Tax: @@product_tax@@     
Rate: @@tax_rate@@%
-  Customizations
<!-- foreach customizations -->- @@name@@ : @@value@@
<!-- end foreach customizations -->

<!-- end foreach quotation_products -->
<!-- foreach charges -->
Charge: @@charge_name@@
Amount @@tax_text@@: @@charge_amount@@
<!-- end foreach charges -->
<!-- foreach discounts -->
Discount: @@discount_name@@
Amount @@tax_text@@: @@discount_amount@@
<!-- end foreach discounts -->

Discounts @@tax_text@@
@@quotation_discounts@@

Charges @@tax_text@@
@@quotation_charges@@

Shipping @@tax_text@@
@@quotation_shipping@@

Handling @@tax_text@@
@@quotation_handling@@

Total Products @@tax_text@@
@@quotation_subtotal@@

Taxes
@@quotation_tax@@

Total @@tax_text@@
@@quotation_total@@

{if $show_account}

    *************************
    Your New Customer Account

    So you may review your quote and more easily make your purchase, we have created your account in our system.
    Please log in using the link and account details provided below.
    In the My Quotes section you will find your approved quotes, if you are happy to proceed select the Add To Cart option and check out as normal with the agreed prices: @@customer_quotes_link@@

    Link To Your Account: @@customer_account_quotation_link@@
    Username: @@customer_username@@
    Temporary Password: @@customer_temporary_password@@
    Please change your password as soon as possible.
    *************************
    
{/if}

You can purchase this quote now by copying the following link into your browser: @@quotation_purchase_link@@
You will need to log in to your account to see the offered price.


If you would like to change the items on your quote you may either email us by replying to this email, with your changes, or create a new quotation request via the website.

- Prices are valid until the provided quotation expiry date.
- If you are unsure of any of the information or prices provided, please do not hesitate to contact us.
- Your quote has been provided in your requested currency. Please be aware that currency fluctuations may result in changes to the price you have been quoted. We reserve the right to change or cancel this quote at any time.


If you have any questions, please do not hesitate to contact us:  @@shop_email@@
@@shop_name@@ built by https://toolecommerce.com/