# TOOLE: Quotations Pro

Enable product quotations for customers with custom forms and quotation response system.

## Changelog

### 1.0.0

- Initial addons release

### 1.0.1

- Added check to ensure quotation belongs to the customer logged in

### 1.0.2

- Adding same product to quote now increases qty

### 1.0.3

- Register back office footer hook correctly
- Update override installation version to fix Prestshop bug: http://forge.prestashop.com/browse/PSCSX-7849

### 1.0.4

- Change event not registered on select field element.

### 1.0.5

- Fix bad styling on select drop down on popup

### 1.0.6

- Single product quote request used incorrect email name

### 1.0.7

- Improved error message responses when not setup correctly.

### 1.0.8

- Spanish email templates using incorrect sub-templates.

### 1.0.9

- More spanish translation.
- Add better error messages when administering quotation requests
- Added option to select and create customer addresses from quotation.

### 1.0.10

- Fix incompatability with Prestashop versions prior to 1.6.0.11

### 1.0.11

- Accept new products without default combination.
- Display correct product image in product list on quotation detail page
- Update override installation version to fix Prestshop bug: http://forge.prestashop.com/browse/PSCSX-7849

### 1.0.12

- Not loading admin javascript in some cases.
- Update override installation version to fix Prestshop bug: http://forge.prestashop.com/browse/PSCSX-7849
- Delete any remainaing enabled products when none selected

### 1.0.13

- Change install order to prevent non-recoverable error.

### 1.0.14

- Fix quick view display
- Update override installation version to fix Prestshop bug: http://forge.prestashop.com/browse/PSCSX-7849

### 1.0.15

- Minor template change to dialog popup

### 1.0.16

- Avoid JS object name clash
- New customer quote received email template
- Fixed TinyMCE library loading error

### 1.0.17

- Add option to disable JS button changes and use templates only.
- New custom hook to display quotation form anywhere.

### 1.0.18

- Hide custom hook dialog on non-enabled products.

### 1.0.19

- Remove newline character incorrectly saved on default form install
- Textarea form component error
- Fix form reset between form field changes

### 1.0.20

- Fix database error when creating a back office quotation.

### 1.0.21

- Remove override on upgrade to 1.6.11

### 1.0.22

- Include workaround to new Prestashop specific prices cache.
- Fix quotation status email preview.
- Fix errors creating new quotation from back office.

### 1.0.23

- Italian translation added
- Fixed error logging for customer add to cart process.
- Fix shipping methods not appearing after editing a product line.
- Display error message to user if unable to create cart from quotation.
- Fix quotation administration event handlers not corretly attaching sometimes.

### 1.1.0

- Add quotations cart
- Fix bug adding product with attribute id to quotation.
- Fix bug with adding/deleting private notes
- QPR-7 Single language installations not loading quotation language correctly
- QPR-8 Update templates to support one page checkout
- QPR-10 Add wholesale price to product line added to quotation
- QPR-9 Remove unnecessary confirm dialogs from back office
- QPR-12 Change currency causes error
- QPR-16 Mails folder contains smarty template files used to populate data in the email template.

### 1.1.2

- Fix bad method call

### 1.1.3

- Fix template error
- Fix create customer order from back office
- Remove deprecated discount class

### 1.2.0

- Prestashop 1.7.2 support

### 1.2.1

- Fix error in php 5.6

### 1.2.2

- QPR-28 Price calculation precision not consistent

### 1.2.3

- Fix errors changing country when creating a new quotation
- Change cookie values to track modifications

### 1.2.4

- Changes to incorporate free version branch

### 1.2.5

- QPR-33 Pre-populate customer name and email if logged in.
- QPR-37 Include customer file uploads
- QPR-38 Add to quote modal not showing in PS1.6

### 1.2.6

- QPR-40 Specific price not assigning when creating back office order from quote

### 1.2.7

- QPR-43 Add product combination selector when adding product to admin created quotation

### 1.2.8

- QPR-39 Add add to cart modal in PS1.7
- QPR-44 Error displaying quotation list if a product has been deleted.

### 1.2.9

- QPR-45 Add option to enable/disable whether available for order when creating the quotation request.

### 1.2.10

- QPR-34 Add option to attach pdf copy of quote request to customer email
- Customer hook to add enabled indicator to product list.

### 1.2.11

- Available to order check not applied when adding product to quote in single quote mode.
- Change tax processing in back office to use Prestashop settings by default

### 1.2.12

- Fix tax been incorrectly applied to reduction amount.

### 1.2.13

- QPR-48 adding a non-shipping charge raises error

### 1.2.14

- Create product and image links prior to loading template in front office templates

### 1.2.15

- Fix bug introduced by changes for PS1.7

### 1.2.16

- Remove PHP notice warning when in debug mode.

### 1.2.17

- Add Catalog option to main menu to enable/disable quotable products for large product catalogs

### 1.2.18

- Update answer template process
- Fix bug with fixed discount amounts

### 1.2.19

- QPR-50 Add multistore support

### 1.2.20

- PS 1.7 Fix single product quote from product screen.
- PS 1.6 Correct product quote labels in product lists.

### 1.2.21

- Include an editable comment field for any product assigned to a quotation.
- Add a link to download a PDF copy of the quotation from the customer account area
- Add a link to download a PDF copy of the quotation from the back office quotation screen

### 1.2.22

- Add Prestatrust support
- Add option to include/exclude taxes from back office quotation.
- Fix bug with price reduction not calculating correctly when taxes enabled.

### 1.2.23

- Remove restriction on 1 order per quotation.
- Add raised orders list to quotation detail page.
- Update spanish translations

### 1.2.24

- Display prices in front end based on customer group tax settings.

### 1.2.25

- Add quote cart custom hook
- Improve compatibility with third-party theme styles

### 1.3.0

- QPR-53 Add quotation templates
- QPR-54 Add quotation system enable by customer group
- QPR-55 Add shipping method select form element
- Make customer account creation optional
- Add option to enable new products as quotable automatically

### 1.3.1

- Workaround for pdf quotation title not picked up for translation in PS 1.6.0
- Add options to hide add to cart buttons from quotable products
- Add options to hide the price for quotable products.
- Allow quote product price increases to be added to the cart.

### 1.3.2

- Add custom hook to mark a page as a quotable product
- Save customer service email on install.

### 1.3.3

- Add GDPR compliance.

### 1.3.4

- Disable customer groups correctly.

### 1.3.5

- Fix hide add to cart button for Prestashop ### 1.7

### 1.3.6

- Fix total in quote summary not taking requested quantity
- Add required field indicator (PS 1.7)
- Fix form being submitted using keyboard when recaptcha enabled but not passed.

### 1.3.7

- Fix form text field custom regex not triggering.

### 1.3.8

- Configure controller correctly so friendly urls can be used.
- Fix invisible recaptcha callback not being executed.
- Disable editing internal name of default form fields to prevent errors in submitting form.,

### 1.3.9

- Save BCC email address when using customer service account.

### 1.3.10

- Add hook to show add to quote or cart button.
- Update email subjects.

### 1.3.11

- Multistore updates.

### 1.3.12

- Disable send quote button while ajax call in-flight
- Seperate processes to send quotation and general messages to customer

### 1.3.13

- Hide add to cart via javascript when changing combination
- Disable form field id via css to allow upload of value for disabled fields

### 1.3.14

- Change enabled product array in template to reduce template size for large catalogs.

### 1.3.15

- Fix 500 server error in customer open quote list on Prestashop ### 1.7.4
- Fix filtering quot ation list by status
- Remove new template button from template list page to avoid confusion

### 1.3.16

- Hide quotation template totals if they are 0

### 1.3.17

- Add selector overrides to advanced settings in module configuration
- Fix hide add to cart and price on the faceted search pages.
- Prevent all customer cart rules from being deleted.

### 1.3.18

- Fix rounding error when rounding by item selected
- Fix tinyMCE editor bug after update for PS 1.7

### 1.3.19

- Updates for PHP 7.2

### 1.3.20

- Fix icons in Prestashop ### 1.7.4
- Add request summary email template
- Fix js url in quotation admin

### 1.3.21

- Use group specific product price is set when adding a product to a quotation.

### 1.3.22

- Fix bulk disable products not working

### 1.3.23

- Fix multistore enable/disable process

### 1.3.24

- Update process to translate custom email template subjects
- Form designer switch field always 0

### 1.3.25

- Move menu options to third level to reduce length
- Fix shipping charge option when using PS 1.7.4.x

### 1.3.26

- Show shipping charge in front office quote summary
- Change Recaptcha request method

### 1.3.27

- Enable show/hide label option for PS1.7
- Disable editing of mandatory fields in form designer.

### 1.3.28

- QPR-23 Display modal to confirm product added to quote cart
- QPR-49 Module should initialise with default email account so it works without a configuration save
- QPR-51 Set default contact on install to prevent error sending quote request if configuration has not been saved
- QPR-60 clear cache when enabling/disabling products
- Fix error when loading quote cart modal dialog in PS1.7
- QPR-61 Multiple columns in form in PS1.7 always full width
- QPR-62 Can't bulk delete templates

### 1.3.29

- Add selectors for product list button block and product page quantity selector
- QPR-64 Module should add status id to db instead of using text code
- Fix updates when ajax disabled.

### 1.3.30

- QPR-21 Add quotation cart menu option
- Add customer address to PDF generated in the back office.
- Fix minimum quantity not being respected in some templates.
- QPR-65 Check product added to cart when cache enabled.

### 1.3.31

- QPR-66 Add quote expiration process

### 1.3.32

- Add wrapper for displayProductAdditionalInfo
- PS1.7 multistore updates

### 1.3.33

- Add configured BCC email to quote summary email
- Fix quote summary contents drop down in PS1.6

### 1.3.34

- Update css for quote cart in PS1.7

### 1.3.35

- Make add to quote button visible when catalog mode enabled.
- Update Recaptcha process to remove retired v1 option.
- QPR-69 Add direct purchase link to received email
- Update css for quote cart in PS1.7

### 1.3.36

- Fix bulk enable/disable
- Fix button appearing on disabled products when quote cart disabled.

### 1.3.37

- Default back office quote email to English if not available for current language.

### 1.3.38

- Merge some administration functions
- Change quotation mail format to smarty template
- Add email subject customization
- Remove unnecessary quotation summary page components

### 1.3.39

- Change cart rule processing when adding quote to cart

### 1.3.40

- Replace error_log function for PS 1.6.0.x

### 1.3.41

- Force customer log in when adding quote to cart from email.
- Handle case when product is disabled from ordering better

### 1.3.42

- Recreate customer account if deleted after a quotation has been sent.
- Display modified cart warning in shopping cart summary.

### 1.3.43

- Return better error message for failed file uploads

### 1.3.44

- Use attribute wholesale price in back office quotation product line.
- Set custom shipping value in quotation admin.
- Add handling charge automatically if value set in Prestashop configuration.

### 1.3.45

- Fix customer name filter in quotation list.

### 1.3.46

- Remove error shown when debug mode enabled.

### 1.3.47

- Fix owner filter in quotation list

### 1.3.48

- Add option to convert contents of shopping cart to a quote when the customer logs in.
- Allow admin email templates to be overridden.

### 1.3.49

- Add selector to find responsive location when moving elements for small screens.
- Add responsive JS process to fix missing Prestashop function.
- Allow deletion from nav bar quote summary
- Add convert to quote function to front controller to allow a product list to be converted to a quote.

### 1.3.50

- Disable form submit button when quote form submitted to avoid multiple requests.
- Use combination reference code in forms when available.
- Quotation summary search by partial email.
- Display tax in customer area based on tax setting set on the quotation.

### 1.3.51

- Fix comment not appearing in pdf in certain instances.

### 1.3.52

- Fix js error if responsive screen changes not available for third-party theme
- Automatically save quotation when customer account selected.

### 1.3.53

- Add option to include quotation handling costs in shipping total added to cart
- Add custom shipping and handling costs to orders created from quote in back office.
- Add hooks to stats page for future updates.

### 1.3.54

- Fix front controller translations not being used.
- Fix error when editing status details.
- Fix email template preview in quotation status.

### 1.3.55

- Change initial module configuration.
- Rename classes to avoid name clashes

### 1.3.56

- Remove unused token from spanish admin email template.
- Quote email template updated
- Quote pdf template updated
- Fix rounding error when calculating total product line price

### 1.3.57

- Minor changes to improve compatibility with PHP 5.3
- Fix incorrect list price in the back office when using a different currency.

### 1.3.58

- Update upgrade files to allow upgrade from free module
- Don't add tax twice when adding a product to a quote in the back office when using taxes.

### 1.3.59

- Add manufacturer to data available to summary template.

### 1.3.60

- Update quotation modal for PS1.7.6
- Reload touchspin if adding product to purchase cart.

### 1.3.61

- Allow admin overrides of quotation templates.

### 1.3.62

- Don't delete previous product special prices when multiple items in quote.
- Set quotation product line tax excluded price correctly.

### 1.3.63

- Change quote qty input name to avoid clash with quote qty

### 1.3.64

- Update css styling for quote cart nav bar block.

### 1.3.65

- Create multiple forms, and assign forms to product categories.
- Add recaptcha v3 support

### 1.3.66

- Add labels to product list when updating filters in block layered module.
- Add product line comment to quote summary in customer account area.

### 1.3.67

- Link product customization fields to quotation request product lines.
- Show country name in email when countries selection element used in form.

### 1.3.68

- Remove HTML formatting tags from PDF
- Remove customer name from address block in PDF

### 1.3.69

- Fix styling issue

### 1.3.70

- Fix quotation admin rich text editor from taking focus.

### 1.3.71

- Remove unused templates
- Update favicon with new quotations counter
- Add notification bar non-responded quotation counter

### 1.3.73

- New product entry form for back office quotations, search and add multiple products at the same time.

### 1.3.74

- Change nav bar quote cart element id and class to prevent Prestashop responsive code from deleting it on some themes.
- Change recaptcha v3 token request to prevent token expiring.

### 1.3.75

- Use mail logo in mail templates instead of site logo.

### 1.3.76

Fix save quotation as template button not working.
Fix misaligned columns in customer quote detail in account area.

### 1.3.77

Add enable all products option to catalog screen.
Fix fistname, lastname filter in back office quotations list

### 1.3.78

- Remove back office footer hook due to inconsistent usage in PS versions.

### 1.3.79

- Add table index for performance on large catalog installations.
- Add default category filter to catalog page.

### 1.3.80

- Add checkbox form item icon to font pack selection.
- Include notifications in shopping cart when modifications disallowed
- Add data tag to switch between touchspin config via template
- Fix error message not displaying when unable to update quote quantity
- Remove requirement to have an address before adding quote to cart in PS 1.7
- Show add to cart button in custom hook when enabled.
- Fix customer copy not being sent when fast response enabled.

### 1.3.81

- Allow custom parameters to be passed through to templates.
- Fix quotation catalog page in multistore.

### 1.3.82

- Allow shipping charge to be set to 0.

### 1.3.83

- Fix incorrectly validating non-required form fields

### 1.4.0

- Add documents to quotations.
- Change customer search to use firstname, lastname, or email.
- Fix error with Enable All option.

### 1.4.1

- Minimal quantity not respected when changing combination in PS 1.7
- Text area validating incorrectly.
- Changes for compatibility with PS 1.6.0

### 1.4.2

- Remove unused email token

### 1.4.3

- Fix recaptcha v3 in PS 1.6.1

### 1.4.4

- When adding product to quotation in back office, use product combination price for default quote value.
- Add page refresh when adding products to quotation.
- Fix answer content not loading when editing an answer.
- Fix Save As Template button not doing anything.

### 1.4.5

- Fix form submission when required fields are not populated and fast response enabled.
- Add option to disable javascript page changes.
- Fix cart summary convert to quote option not saving.

### 1.4.6

- Save attachment when fast response is enabled.
- Fix TinyMCE crash on Safari.
- Show quotation as expired in customer area quotation detail screen.

### 1.4.7

- Add convert to quote option in shopping cart summary in PS 1.6
- Show add to cart button for non-enabled product in custom product list hook.
- Hide add to quote button when group disabled in custom hook on product page.
- Prevent same carrier being added twice to quotation.
- Reset assigned carrier when shipping charge removed from quotation.
- Add the request a quote from cart option to the shopping cart footer for themes that don't support the displayReassurance hook
- Move the cart modification warning messages.
- Use customer address to calculate taxes if available.

### 1.4.8

- Remove font pack icons from templates and move into css.
- Add option for custom icon pack css file.

### 1.4.9

- Add carrier charge to quotation if carrier selection element included in quotation request form.
- Add option to set a default carrier that will be assigned to all newly created customer quotation requests.

### 1.4.10

- Update pdf format

### 1.4.11

- Allow 0 price products to be added to quotations.
- Retrieve all carriers in module configuration.

### 1.4.12

- Add option to show the product price in the quotation request summary.
- Only change carrier rate for the selected carrier.
- Convert to cart option uses minimal quantity not quantity.

### 1.4.13

- [FEATURE] Add additional cart update hook processing
- [FEATURE] Show product customizations in request summary
- [BUG] Apply default expiry days to quotation request
- [BUG] Show shipping and handling in quotation email without tax when selected.

### 1.4.14

- [BUG] Remove average tax rate
- [FEATURE] Show product line tax rate and tax paid

### 1.4.15

- [BUG] Fix controller error when friendly urls disabled.
- [BUG] Changing quantity in quotation summary in front office not updating quote request.

### 1.4.16

- [BUG] Remove square brackets from email subject.

### 1.4.17

### 1.4.18

- [BUG] Refactor code to allow product customization process to be overridden.
- [BUG] Add touchspin layout option to configuration

### 1.4.19

- [BUG] Change add to cart modal template location, modal not appearing in some mobile browsers.
- [BUG] Fix date field custom format being ignored.
- [BUG] Prevent non-json text being returned in ajax calls.

### 1.4.20

- [BUG] Quantity not taken into account in custom product list hook.
- [BUG] Set carrier correctly when applying a default carrier.

### 1.4.21

- [BUG] JS error when adding non-quotation product to cart.

### 1.4.22

- [BUG] Add quotation discount using quotation tax setting.
- [BUG] Add scrollbar to customer message dialog

### 1.4.23

- [FEATURE] Add GET operation for quotations via webservice
- [BUG] Change expiry date to datetimepicker in back office quotation summary
- [BUG] Select bulk discount when adding item to quote cart if applicable

### 1.4.24

- [BUG] Redirect to summary after converting cart to quote to prevent resubmission on refresh.
- [BUG] Calculate correct quotation price when group discounts are used.

### 1.4.25

- [BUG] Fix error when adding customization to quotation request.
- [BUG] Fix request received response not showing when fast response enabled.
- [FEATURE] Add product customizations to summary screen in PS1.6.1

### 1.4.26

- [BUG] Get carrier module shipping cost if external module.
- [FEATURE] Add quote cart contents to customer quote cart when logging in if current customer cart is empty.
- [BUG] Correctly load vertical or horizontal touchspin on summary page.

### 1.4.27

- [BUG] Show attribute price on quote summary.
- [BUG] Attach add to quote modal to body to stop third party themes hiding it.
- [BUG] Correctly load the displayTop template when the quote cart is hooked to the DisplayTop hook
- [BUG] Fix adding customization to quotation request multistore compatible.
- [BUG] Show third-party carriers in form carriers drop down
- [BUG] Update group discount fix when quotation price is greater then original sale price.
- [FEATURE] Add french translation file.
- [FEATURE] Add option to create customer address directly from quotation.

### 1.4.28

- [BUG] Update quotation db table to remove some non-required fields requiring data
- [BUG] Correctly initialise quantity touchspin in quick view modal.
- [FEATURE] Add option to highlight expirying quotations in the quotation list.
- [FEATURE] Option to order quotations by received date or expiring date.
- [BUG] Load expiry datetime picker in back office with correct time format.

### 1.5.0

- [FEATURE] Migrate PDF renderer from Prestashop default to MPDF package
- [FEATURE] Add Email and PDF editor
- [FEATURE] Add custom quotation reference numbers
- [FEATURE] Change expiry date to datetimepicker in back office quotation summary
- [FEATURE] Add option to download pdf copy of quotation cart.
- [FEATURE] Link quotation requests and quotations by reference.
- [FEATURE] Add new Quotation Open status
- [FEATURE] Add all message to customer thread
- [FEATURE] Quotation forms per store
- [FEATURE] Add multiple products to quotation when browsing multiple pages of results in back office.
- [FEATURE] Update multiple quotation product lines without having to edit/load/save each one individually.
- [FEATURE] Add new Address form field, and create customer address automatically.
- [FEATURE] Add override option to only show carriers included in quotation.
- [FEATURE] Store specific module configurations.
- [FEATURE] Change quantity fields to text format to prevent default arrows, and add number filter.
- [FEATURE] Add warning when creating order if quotation not sent to customer
- [BUG] Remove document files when document deleted.
- [BUG] Make documents specific to store.
- [BUG] Set expiry date correctly when creating back office quotation.
- [FEATURE] Add additional ES language files.
- [BUG] Fix 100% quotation discount causing an error
- [BUG] Allow decimal percentage discounts

### 1.5.1

- [FEATURE] Move templates to spcific database table
- [BUG] Make templates shop specific.
- [BUG] Correctly set quotation forms shop settings.
- [BUG] Deleting template causes error
- [BUG] Creating a template creates two identical discounts.

### 1.5.2

- [FEATURE] Add delivery and invoice addresses, calculate taxes based on selected address.

### 1.5.3

- [BUG] Fix account selection dialog scrolling off screen when large number of accounts found.
- [BUG] Fix hide cart function hiding add to cart block when javascript page changes are disabled.

### 1.5.4

- [BUG] Allow multiple product customizations for the same product/combination to the added to a quotation.
- [BUG] Copy EN mail/pdf templates to default installation language if they do not exist.
- [BUG] @@tax_text@@ token missing from final email sending function.

### 1.5.5

- [BUG] Add missing tokens to template preview option.
- [BUG] Use selected delivery address when calculating taxes to add to delivery.
- [BUG] Use selected delivery address state when adding product to quotation.
- [BUG] Change quotation date tokens to be in format for locale

### 1.5.6

- [FEATURE] Quotations sent from back office can be edited before sending.
- [BUG] Send all emails to BCC if set.
- [FEATURE] Add script to remove delete button and prevent quantity changes in cart summary for PS1.7
- [BUG] Fix not correctly linking to order when quotation purchased.
- [FEATURE] Show payment method friendly name when creating order from the back office.
- [BUG] Implement ActionGetProductPropertiesAfter hook to make sure Prestashop displays cart specific price, over non-cart specific price.

### 1.5.7

- [FEATURE] Add pdf and email templates for latin american spanish
- [BUG] Fix syntax error in hookActionGetProductPropertiesAfter hook call.

### 1.5.8

- [BUG] Add missing cart template in PS1.6
- [BUG] Fix buy now link not opening the cart order directly.
- [FEATURE] Add option to select email template to use when sending quotation to customer

### 1.5.9

- [BUG] Load and save mail/pdf templates for installed but disabled languages.
- [BUG] Set default language for mail editor if only a single language is installed.

### 1.5.10

- [BUG] Fix price not showing on quote summary page.

### 1.5.11

- [BUG] Fix fixed discount amount value not showing in email.
- [BUG] Fix currency error when adding shipping charge to quotation.
- [BUG] Fix download cart as pdf function not working.
- [BUG] Fix error when converting guest cart to customer cart when logging in.

### 1.5.12

- [FEATURE] Add full attribute information to quotation product line.

### 1.5.13

- [FEATURE] Updates for PS 1.7.8 and PHP 7.4
- [FEATURE] Store tax id applied to quotation product line.

### 1.5.14

- [FEATURE] Add quotation owner functions, assign/reassign quotations to employees
- [BUG] Apply custom reference number to customer created quotation requests.
- [BUG] Incorrect shipping charge being applied to quotation when handling enabled

### 1.5.15

- [FEATURE] Support chinese characters in PDF

### 1.5.16

- [FEATURE] Move documents to PS_DOWNLOAD_DIR for better security
- [FEATURE] Add customer attachments to quotation as a document
- [FEATURE] Add option for multiple documents to be uploaded
- [BUG] Show correct discount line total in PDF.
- [BUG] Hide price in quick view modal when enabled.
- [BUG] Reinsert special mail/pdf template product row codes if deleted by editor.
- [BUG] Prevent quotation totals currency converting value twice when changing quotation currency.
- [BUG] Stop sending two messages to customer when sending a quote request.
- [BUG] Custom email address set ion configuration is ignored.
- [FEATURE] Save email and pdf template changes to theme directory.
- [FEATURE] Show expired quotes in quotation history in customer account.
- [FEATURE] Load PDF template preview in frame
- [BUG] No need to recalculate prices when changing quotation country or currency

### 1.5.17

- [FEATURE] Change charges to shipping offers to allow multiple shipping options to be shown to customer
- [BUG] Add missing company name token to templates.
- [FEATURE] Add form components as tokens for the email and pdf templates.
- [FEATURE] Add registration required message option for product page and cart.
- [FEATURE] Add customer/admin conversation options to account area.
- [FEATURE] Let shipping costs to be edited inline.
- [FEATURE] Remove icon packs options, replace with svg icons for better theme compatibility.
- [BUG] Fix documents not being attached.

### 1.5.18

- [BUG] Stop setting default owner when changing quotation status.
- [BUG] Calculate shipping price using defined zone of state when available.

### 1.5.19

- [BUG] Add missing @@date@ tokens to email/pdf templates

### 1.5.20

- [FEATURE] Add option to select the PDFs that should be attached to customer and admin emails.
- [BUG] Add missing data to cart pdf copy request
- [BUG] Prevent call to 1.7.8 prestashop function in previous versions.
- [BUG] Show OPEN quotations in back office header notification section.

### 1.5.21

- [BUG] Fix incorrectly formatted html in shopping cart.
- [BUG] Fix missing status columns.
- [BUG] Hide quote details in customer account summary pages for quotations not sent to the customer.

### 1.5.22

- [FEATURE] Add discount option to product lines when adding products to a quotation in the back office.
- [FEATURE] Add option to change the discount applied to a product line in a quotation in the back office.
- [BUG] Correctly load products without a default image when adding products to a quotation in the back office.
- [FEATURE] Allow PDF templates to resolve smarty functions.
- [BUG] Stop quotation editor refreshing page when populating country field using data saved in the browser.
- [BUG] Correctly resolve sequential and random reference numbers when greater than 10 in length.

### 1.5.23

- [BUG] Ignoring customer copy selection when submitting request.
- [BUG] Empty cart if currency changed during checkout.

### 1.5.24

- [BUG] Incorrect build number uploaded.

### 1.5.25

- [FEATURE] Add support for ecotaxed items
- [BUG] Validate custom drop down select fields in front end quotation forms.

### 1.5.26

- [FEATURE] Add option to allow 0 stock items in a quotation to be purchased.

### 1.5.27

- [FEATURE] Allow status codes to be language specific.
- [BUG] Order created from quotation in the back office will not use custom shipping price.

### 1.5.28

- [FEATURE] Allow products to be reordered in quotation using the back office admin.
- [FEATURE] Add new searchable columns to quotation list.

### 1.5.29

- [BUG] Fix search product dialog showing mix of product combinations.
- [BUG] Fix reference to currency variable only available after 1.7.6
- [FEATURE] Add advanced settings option to use local installation temporary directory for pdf creation.
- [BUG] Show discount ex. tax in quotation editor when configured without taxes.
- [FEATURE] Add advanced settings option to force emails and PDFs to use quotation tax setting instead of customer's group tax setting when sending.
- [BUG] Remove rounding to 2 decimal places on quoted price in back office

### 1.5.30

- [FEATURE] Add product line customization cost
- [BUG] Prevent negative discounts in back office.
- [FEATURE] Add reset option to Mail/PDF editor to reset templates back to the originals.

### 1.5.31

- [BUG] Update classloader to avoid clashes between Roja45 modules.
- [BUG] Fix incompatibility with PHP 7.0 (method return types)
- [FEATURE] Remove enabled product ids from page load to lessen download sive for large catalogs.

### 1.5.32

- [FEATURE] Add indicator when price rule has been applied when adding an item to a quotation from the back office
- [FEATURE] Update the quotation unit price if a price rule matched when changing the quantity when adding an item to a quotation in the back office.
- [BUG] Fix quotation product line discount not applying correctly.
- [FEATURE] Add State to Address form component and add to address when creating automatically.
- [FEATURE] Add DNI to Address form component.
- [FEATURE] Add VAT Number to Address form component.

### 1.5.33

- [FEATURE] Add advanced option to send emails with hidden customer service tracking codes, removing them from the subject line.
- [BUG] Unformat currency values when using currencies that use different 1000's seperators
- [BUG] Save product line comment when changing other values in the quotation product line.

### 1.5.34

- [FEATURE] Add support for Prestashop 8
- [BUG] Create customer account in store assigned to quotation in multistore context.
- [BUG] Add correct store url to quotation email sent to customer.
- [BUG] Direct customer to quotation detail screen in customer account area when in catalog mode.
- [FEATURE] Include ecotax in quotation totals.

### 1.5.35

- [FEATURE] Set reply-to address to customer for quotation status update emails.

### 1.5.36

- [FEATURE] Calculate quotation unit price by setting the product line total in quotation editor
- [FEATURE] All product line calculations shown exc. tax, show tax value applied when taxes enabled.

### 1.5.37

- [FEATURE] Add option to change the picture associated with the product line in a quotation.
- [BUG] Refresh quotation after sending to customer.
- [BUG] Prevent product quantity being doubled when creating a product customization.
- [BUG] Handle communication error gracefully
- [BUG] Remove rounding on shipping charge when adding in the back office.
- [BUG] Fix error converting a template into a quote.
- [BUG] Remove cookie use when validating an order created from a quotation.
- [BUG] Format offer price in email and pdf template correctly.
- [BUG] Don't create CS thread when CS account is not being used.

### 1.5.38

- [BUG] Add ecotax to product total and remove from product line total to prevent rounding error.
- [BUG] Stop retrieving carrier cost twice when selecting and adding a carrier (unneccessary cart created)
- [BUG] Show correct quotation total in quotation list in back office
- [BUG] Fix discount rounding error.
- [BUG] Show shipping value including handling in customer quote list in front end

### 1.5.39

- [BUG] Fix mail tokens not being resolved for the order request email.
- [BUG] Quotation product line total only shows in base currency.

### 1.5.40

- [BUG] Fix state not resolving to name in emails
- [FEATURE] Add product additional shipping cost to product line and shipping total.

### 1.5.41
- [BUG] Recursively create directory when adding document to quotation.
- [BUG] Fix quote summary modal not hiding when fast response active without recaptcha.
- [BUG] Fix quantity duplicating if downloading multiple pdfs from shopping cart before selecting request quote.
- [BUG] Fix mail/pdf editor not loading template if supported language added after installation
- [BUG] Fix quotation state pdf assignment not working after latest prestashop update

### 1.5.42
- [BUG] Add actionCustomerAccountAdd hook and copy quotation cart.
- [FEATURE] Use composer to manage dependencies
- [BUG] Update mPDF to version that supports PHP 8
- [BUG] Add discount fixed value inclusive of tax
- [BUG] Remove simple_html_dom use
- [BUG] Add products to quotation ex-tax and apply discount correctly.
- [BUG] Fix incorrect tax value when adding two products with an additional shipping cost.

### 1.5.43
- [FEATURE] Let discounts be added inc/exc vat.
- [BUG] Fix original product unit price being shown in quotation request email

### 1.5.44
- [FEATURE] Add quotation reference as order note

### 1.5.45
- [BUG] Change license validation timeout.
- [FEATURE] Add product combination ordering and search option
- [BUG] Set quotation tokens when changing status in quotation admin.

### 1.5.46
- [FEATURE] Restrict carriers when product carriers have been selected.  
- [BUG] Include product combo weight in calculation
- [BUG] Round percentage discount to 2 decimal places.
- [BUG] Suppress errors and warnings from Html2Text
- [BUG] Add additional error checks for missing customer service accounts

### 1.5.47
- [FEATURE] Post errors to prestashop logs screen.
- [BUG] Don't convert currency fixed value in quotation product line
- [BUG] Fix quotation owner filter
- [BUG] Add customer form data to pdf templates

### 1.5.48
- [BUG] Update translations
- [BUG] Prevent possible template error when creating a new quotation.

### 1.5.49
- [FEATURE] Add option to delete or maintain current quotation request when converting shopping cart
- [FEATURE] Add warning when product has not stock when adding to quotation in the back office

### 1.5.50
- [FEATURE] Add new form elements, section header with text or html, start/end dates.
- [FEATURE] Support themes using data-bs-modal
- [BUG] Remove str_contains use for < PHP 8
- [BUG] Not calling hide cart button function when changing pages in product lists
- [FEATURE] Add option to lock the name and email fields on the quotation form when logged in.
- [FEATURE] Add option to display select fields as radio buttons
- [FEATURE] Add form address selector when customer logged in.
- [FEATURE] Add form option to collapse a form field
- [FEATURE] Add warning when entering form id that is already in use

### 1.5.51
- [BUG] Prevent expired quote being purchased from email link
- [FEATURE] Add option to set the replyTo value for a quotation email to the quotation owner
- [BUG] Fix getTotalWeight exception in PHP 8.1

### 1.5.52
 - [BUG] Only show address selector when a customer has an address
 - [BUG] Process differences between short and long date formats correctly

### 1.5.53
 - [BUG] Show margin in quotation product line ex. tax.

### 1.5.54
 - [FEATURE] Allow bulk update of quotation status
 - [BUG] Security tab is hidden when CS option is disabled.
 - [FEATURE] Add option to hide product page add to quote button if combined stock level is zero
 - [FEATURE] Add option to show unit price with or without discount applied
 - [FEATURE] Show customer group discount value in quote summary
 - [BUG] Fix error when manually changing the status of a quotation with no account connected

### 1.5.55
 - [BUG] Fix validation error

### 1.5.56
 - [BUG] Set default lang in override if not provided

### 1.5.57
 - [FEATURE] Add monolog
 - [BUG] Trim email address before saving to prevent errors downstream
 - [BUG] Error if product does not exist when adding to quote cart