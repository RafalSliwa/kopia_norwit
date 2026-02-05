PKO Leasing Integration Module
===============================

Overview
--------
This module integrates PKO Leasing payment options with PrestaShop, allowing customers to use leasing as a payment method in your online store.  
The module includes a configurable button that directs users to the PKO Leasing form.

Installation
------------
1. Log in to the PrestaShop Admin Panel.
2. Navigate to "Modules" -> "Module Manager."
3. Click "Upload a module."
4. Select the .zip file of the module from your computer.
5. After the module is uploaded, click "Install."
6. Proceed to the module configuration.

**Note:** This module requires PHP with cURL support enabled.

Configuration
-------------
1. Go to "Modules" -> "Module Manager" and find the PKO Leasing module.
2. Fill in the required fields with the information provided by PKO Leasing:
    - Shop ID
    - Secret Key
3. Click "Test Connection" to validate the settings.

Important:
----------
- Always save your settings before testing the connection.
- If the connection is successful, you will see a success message (HTTP 200).
- If there is an error, check the configuration and correct the following:
    - Incorrect Shop ID or Secret Key will result in HTTP 401 or 500 errors.

Troubleshooting
---------------
- Ensure the product categories are correctly set up for leasing eligibility.
- Verify stock levels are greater than 0.
- Confirm correct tax rules are applied for your region.
- Check that all customer groups have access to the product.

Support
-------
If you encounter any issues, please contact support at: dostawcypc@pkoleasing.pl
