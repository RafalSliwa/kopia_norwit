Module is installed in a regular way - simply upload your archive and click install.

CHANGELOG:
===========================
v 2.7.7 (September 5, 2024)
===========================
- [+] New carousel type: Frequently bought together
- [*] Fixed irrelevant cached data in some specific scenarios
- [*] Improved compatibility with PrestaShop Edition Basic UI
- [*] Minor bug fixes and optimizations

Files modified
-----
- /democontent/carousels-16.txt
- /democontent/carousels.txt
- /easycarousels.php
- /upgrade/install-2.7.3.php
- /upgrade/install-2.7.4.php
- /upgrade/install-2.7.6.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-caching-form.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/admin/warnings.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/hook/product-item.tpl

Files added
-----
- /upgrade/install-2.7.7.php

===========================
v 2.7.6 (March 1, 2024)
===========================
- [+] Compatibility with hooks: displayAfterProductThumbs, displayNotFound
- [+] Lazy load images in modern browsers
- [+] Compatibility with Swiper 11 - modern mobile touch slider
- [*] Optimized carousel layout to minimize CLS issues
- [*] Minor bug fixes and optimizations

Files modified
-----
- /democontent/carousels-16.txt
- /democontent/carousels.txt
- /easycarousels.php
- /override_files/classes/Product.php
- /upgrade/install-1.7.0.php
- /upgrade/install-2.7.5.php
- /views/css/back.css
- /views/css/front.css
- /views/js/adapter/bx-adapter.js
- /views/js/adapter/swiper3-adapter.js
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/configure.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/hook/item.tpl
- /views/templates/hook/product-item-16.tpl

Files added
-----
- /classes/RelatedOverrides.php
- /upgrade/install-2.7.6.php
- /views/css/lib/swiper11.css
- /views/js/adapter/swiper4-adapter.js
- /views/js/adapter/swiper5-adapter.js
- /views/js/lib/swiper11.js
- /views/templates/hook/product-item.tpl

Files removed
-----
views/templates/hook/product-item-17.tpl

===========================
v 2.7.5 (November 12, 2023)
===========================
- [*] Possibility to display all manufacturers/suppliers without specifying IDs
- [*] Improved compatibility with PS 8.1
- [*] Minor bug fixes and optimizations

Files modified
-----
- /controllers/front/ajax.php
- /democontent/carousels-16.txt
- /easycarousels.php
- /override_files/classes/Product.php
- /upgrade/install-1.7.0.php
- /upgrade/install-2.5.0.php
- /upgrade/install-2.6.2.php
- /upgrade/install-2.7.0.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/hook/layout.tpl

Files added
-----
- /.htaccess
- /democontent/carousels.txt
- /upgrade/install-2.7.5.php
- /views/templates/hook/dynamic.tpl

Files removed
-----
- /democontent/carousels-17.txt

===========================
v 2.7.4 (November 14, 2022)
===========================
- [+] Compatibility with PS 8.0.0
- [+] New carousel type: Products by default supplier
- [*] Fixed "All items" link below carousels
- [*] Minor bug fixes and optimizations

Files modified
-----
- /democontent/carousels-17.txt
- /easycarousels.php
- /upgrade/install-1.7.0.php
- /upgrade/install-1.7.2.php
- /upgrade/install-1.7.8.php
- /upgrade/install-1.8.0.php
- /upgrade/install-2.0.3.php
- /upgrade/install-2.5.5.php
- /upgrade/install-2.6.2.php
- /upgrade/install-2.7.0.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-caching-form.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/hook/product-item-16.tpl
- /views/templates/hook/product-item-17.tpl

===========================
v 2.7.3 (August 5, 2022)
===========================
- [+] Optionally cache carousels displayed within a hook for faster loading
- [+] New carousel type: Other products of same brand
- [+] Configurable sorting for categories, suppliers, manufacturers
- [+] Configurable number of days for best sales
- [+] Added support for hooks displayLeftColumnProduct/displayRightColumnProduct
- [*] Minor fixes and optimizations

Files modified
-----
- /democontent/carousels-17.txt
- /easycarousels.php
- /readme_en.pdf
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-display-form.tpl
- /views/templates/admin/warnings.tpl
- /views/templates/hook/product-item-17.tpl

Files added
-----
- /upgrade/install-2.7.3.php
- /views/templates/admin/hook-caching-form.tpl
- /views/templates/admin/input.tpl
- /views/templates/admin/multilang-input.tpl

Directories added
-----
- /cache/

Files removed
-----
- /upgrade/install-2.7.2.php

===========================
v 2.7.2 (March 21, 2022)
===========================
- [+] Optionally sort products by Best Sales
- [+] Optionally take into account last visited category for related products
- [+] Optionally filter related products by current manufacturer(s)
- [*] Improved compatibility with PS 1.7.8
- [*] Minor fixes and optimizations

Files modified
-----
- /democontent/carousels-17.txt
- /easycarousels.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-display-form.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/hook/product-item-16.tpl
- /views/templates/hook/product-item-17.tpl

Files added
-----
- /upgrade/install-2.7.2.php

===========================
v 2.7.1 (October 29, 2020)
===========================
- [+] New carousel type: Products by tags
- [+] Optionally exclude non-available products from carousel
- [+] Optionally filter products in carousel by categories/manufacturers
- [+] Optionally display only selected manufacturers/suppliers in carousel
- [+] Added Polish translation
- [*] Updated Russian translation
- [*] Display default image if product has no image in PS 1.7
- [*] Fixed supplier images
- [*] Minor fixes and optimizations

Files modified
-----
- /controllers/front/ajax.php
- /democontent/carousels-16.txt
- /democontent/carousels-17.txt
- /easycarousels.php
- /readme_en.pdf
- /views/css/back.css
- /views/css/front.css
- /views/js/adapter/bx-adapter.js
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/hook/product-item-17.tpl
- /translations/ru.php

Files added
-----
- /translations/pl.php

===========================
v 2.7.0 (December 5, 2019)
===========================
- [+] Configurable slider library: Swiper/BxSlider
- [+] Optionally load or don't load custom slider library
- [+] Possibility to display products associated with multiple manufacturers/suppliers
- [+] New exception rule: display carousel on product pages of selected manufacturers
- [+] Added French translation
- [*] Updated Russian translation
- [*] Easily edit css styles for carousel navigation in admin interface
- [*] Minor fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/hook/product-item-17.tpl

Files added
-----
- /views/css/adapter/bx-adapter.css
- /views/css/lib/swiper3.css
- /views/css/lib/swiper4.css
- /views/css/lib/swiper5.css
- /views/js/adapter/bx-adapter.js
- /views/js/adapter/swiper3-adapter.js
- /views/js/lib/swiper3.js
- /views/js/lib/swiper4.js
- /views/js/lib/swiper5.js
- /translations/fr.php
- /upgrade/install-2.7.0.php

Files removed
-----
- /views/css/bx-styles.css

===========================
v 2.6.2 (August 16, 2019)
===========================
- [+] New carousel option: autoplay interval
- [+] New exception rules: Subcategories of selected categories, Products in selected categories
- [+] Admin interface for editing custom CSS/JS code
- [*] Improved compatibility with PS 1.7.6

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/front-17.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/admin/warnings.tpl

Files added
-----
- /democontent/carousels-16.txt
- /democontent/carousels-17.txt
- /upgrade/install-2.6.2.php

Files removed
-----
- /democontent/carousels.txt
- /views/css/common-classes.css
- /views/css/front-16.css
- /views/css/front-17.css

===========================
v 2.6.1 (February 21, 2019)
===========================
- [*] Improved selection of other products from same category
- [*] Don't render carousel if total items is set to 0
- [*] Fixed related products on checkout page in PS 1.6
- [*] Minor fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/front.css
- /views/js/front.js
- /views/templates/hook/layout.tpl
- /views/templates/hook/product-item-16.tpl
- /views/templates/hook/product-item-17.tpl

===========================
v 2.6.0 (September 19, 2018)
===========================
- [+] Related products on checkout page in PS 1.7
- [+] Optionally show number of matches for categories, manufacturers and suppliers
- [+] New hooks: displayShoppingCart, displayShoppingCartFooter
- [+] Introduced secret options for advanced use (more info in documentation)
- [*] Fixed TinyMCE scrolling in PS 1.7
- [*] Fixed editing carousels for different shops in multistore
- [*] Fixed featured items in current category
- [*] Image width/height attributes in PS 1.7
- [*] Consider group restrictions for category carousels
- [*] Misc fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/bx-styles.css
- /views/css/front-16.css
- /views/css/front-17.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/hook/item.tpl
- /views/templates/hook/layout.tpl
- /views/templates/hook/product-item-16.tpl
- /views/templates/hook/product-item-17.tpl
- /readme_en.pdf

Files added
-----
- /views/css/mce_custom.css

===========================
v 2.5.5 (April 5, 2018)
===========================
- [+] Optional custom sorting
- [+] Optionally link carousel title to all items page
- [*] Improved structure of scripts for flexible overriding
- [*] Minor fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/bx-styles.css
- /views/css/front.css
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/hook/product-item-16.tpl

Files added
-----
- /upgrade/install-2.5.5.php

===========================
v 2.5.4 (February 25, 2018)
===========================
- [+] New hooks

Files modified
-----
- /easycarousels.php

===========================
v 2.5.3 (October 20, 2017)
===========================
- [*] Display discounted products basing on customer group and customer_id
- [*] Fixed adding to cart by registered users in PS 1.7
- [*] Fixed visibility for products with status "catalog only"
- [*] Minor fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/front-16.css
- /views/templates/hook/product-item-17.tpl

===========================
v 2.5.2 (July 17, 2017)
===========================
- [+] New carousel type: Other products with same tags

Files modified
-----
- /easycarousels.php

===========================
v 2.5.1 (May 8, 2017)
===========================
- [+] New field: minimum matching features for related products
- [*] Minor fixes

Files modified
-----
- /easycarousels.php
- /views/js/back.js
- /views/templates/hook/carousel.tpl
- /views/templates/admin/carousel-form.tpl

===========================
v 2.5.0 (April 10, 2017)
===========================
- [+] Compatibility with PS 1.7
- [+] Custom settings for mobile devices
- [+] New carousel types: Selected products, Selected categories, Subcategories
- [+] New available hooks: displayTopColumn (PS 1.6), displayFooter
- [+] Interface for adding/removing class overrides used by module
- [*] Display related products by multiple matching features
- [*] Improved exceptions mechanism (possibility to display hook only on selected pages)
- [*] Extendable template structure. Now you can customize only selected blocks of templates, no need to override whole files
- [*] Microdata markup
- [*] Order viewed products by "last viewed"
- [*] Possibility to select original image type
- [*] Misc fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/bx-styles.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/admin/warnings.tpl
- /views/templates/hook/carousel.tpl
- /readme_en.pdf

Files added
-----
- /upgrade/install-2.5.0.php
- /views/css/front-16.css
- /views/css/front-17.css
- /views/templates/hook/item.tpl
- /views/templates/hook/layout.tpl
- /views/templates/hook/product-item-16.tpl
- /views/templates/hook/product-item-17.tpl

Files moved:
- /override/ -> /override_files/. /override/ directory kept

===========================
v 2.0.3 (February 14, 2017)
===========================
- [+] Optionally render carousel as native horizontal scroll, without scripts
- [+] Optional separate type for mobile carousel (grid, carousel, native scroll)
- [+] Autodetect customized files and show warning if they have to be updated
- [*] Fixed ajax path for cases, when SSL is activated, but not enabled on all pages
- [*] Minor fixes and optimizations

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/hook/carousel.tpl

Files added
-----
- /upgrade/install-2.0.3.php
- /views/templates/admin/warnings.tpl

===========================
v 2.0.2 (October 17, 2016)
===========================
- [*] Fixed hook settings data import/export

Files modified
-----
- /easycarousels.php

===========================
v 2.0.1 (July 28, 2016)
===========================
- [*] Fixed wrappers data import
- [*] Fixed error reporting on saving carousels
- [*] Highlight saved wrapper class
- [*] Improved detection of products from same category

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/js/back.js

===========================
v 2.0.0 (July 23, 2016)
===========================
- [+] Configurable carousel wrappers. If you use customized carousel.tpl and front.js, make sure you update them.
- [*] Improved carousel data fetching
- [*] Dynamic loading is initially set to OFF
- [*] Misc bug fixes and code optimizations

Files modified
-----
- /easycarousels.php
- /views/css/back.css
- /views/css/front.css
- /views/css/bx-styles.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/hook/carousel.tpl

Files added
- /upgrade/install-2.0.0.php

===========================
v 1.8.0 (Summer 2016)
===========================
- [+] Optional second image on hover
- [+] Optional responsive grid view without activating carousel
- [+] Option to stop autoplay on hover
- [+] Optional height normalization for carousel elements
- [+] Warning on saving multi-row carousel with possible empty elements
- [*] Fixed visibility in catalog view

Files modified
-----
- /easycarousels.php
- /views/css/front.css
- /views/js/front.js
- /views/templates/hook/carousel.tpl
- /views/templates/admin/carousel-form.tpl

Files added
- /upgrade/install-1.8.0.php
- /readme_en.pdf

Files removed
- /documentation_en.pdf

===========================
v 1.7.9 (March 07, 2016)
===========================
- [*] Fixed carousels for rtl pages
- [*] Fixed textarea bug on language switch
- [*] Fixed "Other products from same category"
- [*] Take visibility status in consideration

Files modified
-----
- /easycarousels.php
- /views/css/bx-styles.css
- /views/templates/admin/carousel-form.tpl

===========================
v 1.7.8 (February 28, 2016)
===========================
- [+] Carousel for Viewed products
- [+] Multilingual description field
- [+] New option: pre-load or post-load carousels
- [+] Documentation
- [*] Fixed reverse links for suppliers/manufacturers
- [*] Fixed wrong positions on saving carousels
- [*] PSR-2
- [*] Misc code optimizations

Files modified
-----
- /easycarousels.php
- /views/templates/hook/carousel.tpl
- /views/templates/admin/cofigure.tpl
- /views/templates/admin/carousel-form.tpl
- /views/templates/admin/hook-display-form.tpl
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /controllers/front/ajax.php
- /override/classes/Product.php
- /upgrade/install-1.0.1.php
- /upgrade/install-1.1.1.php
- /upgrade/install-1.7.0.php
- /upgrade/install-1.7.1.php
- /upgrade/install-1.7.2.php

Files added
-----
- /upgrade/install-1.7.8.php
- /documentation_en.pdf

===========================
v 1.7.7 (December 02, 2015)
===========================
[*] Don't display inactive products in accessories. Thanks to PS Ambassador Ismael
[*] Fixed out_of_stock status + added styling for stock labels. Thanks to datapartner.dk

Files modified
-----
- /easycarousels.php
- /views/css/front.css
- /views/templates/hook/carousel.tpl
- /Readme.md

===========================
v 1.7.6 (November 14, 2015)
===========================
[+] Possibility to display manufacturer logo in product carousels
[*] Minor code fixes

Files modified
-----
- /easycarousels.php
- /views/templates/admin/carousel-form.tpl
- /views/templates/hook/carousel.tpl
- /views/templates/js/back.js
- /Readme.md

===========================
v 1.7.5 (July 23, 2015)
===========================
[*] Misc bug fixes: autoscroll, importer, exceptions and others

===========================
v 1.7.4 (July 10, 2015)
===========================
- [+] Product reference display

Files modified
-----
- /easycarousels.php
- /views/templates/hook/carousel.tpl
- /Readme.md

===========================
v 1.7.3 (June 14, 2015)
===========================
Fixed
-----
- Min width fix

Files modified
-----
- /easycarousels.php (version update)
- /views/js/front.js
- /Readme.md

===========================
v 1.7.2 (May 16, 2015)
===========================
Added
-----
- View all links for new products, prices drop, bestsellers, products by supplier, products by manufacturer
- Possibility to force one line for title
- Possibility to show/hide title and define its length
- Possibility to show/hide description and define its length
- Possibility to show/hide product thumbnails, displayed using productlistthumbnails v >= 1.0.1

Fixed
-----
- Retro-compatibility for getAccessories() in /override/classes/Product.php
- Minor bug fixes

Changed
-----
- Replaced "float:left" by "display:inline-block" for all carousel items

Files modified
-----
- /easycarousels.php
- /views/templates/hook/carousel.tpl
- /views/templates/admin/carousel-form.tpl
- /views/css/front.css
- /views/js/front.js
- /override/classes/Product.php
- /Readme.md

===========================
v 1.7.1 (April 23, 2015)
===========================
Added
-----
- Possibility to enable/disable compact tabs
- Possibility to add custom class for carousels container
- Dynamic class for each carousel, indicating current number of visible items (columns) "items-num-xx"

Changed
-----
- Slight changes in FO layout. All carousels are wrapped by a common container that can take user-defined class
- Tabs are transformed to compact list only if they overlap container
- Updated russian translation
- Minor code fixes

Files modified
-----
- /easycarousels.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/hook/carousel.tpl
- /views/js/back.js
- /views/js/front.js
- /views/css/front.css
- /views/css/bx-styles.css
- /translations/ru.php
- /Readme.md

Files added
-----
- /views/templates/admin/hook-display-form.tpl
- /upgrade/install-1.7.1.php

CHANGELOG:
===========================
v 1.7.0 (April 21, 2015)
===========================
Added
-----
- Carousel for Accessories
- Possibility to set random ordering
- Possibility to display carousels only for current category
- Possibility to show/hide product category
- Possibility to show/hide product manufacturer
- Possibility to show/hide hooks in product listings used for carousels
- Some new settings for carousels (loop, speed, slides moved, slides numbers for different resolutions, min slide width)
- Bulk actions
- Manipulating other modules in hook position settings (activate/deactivate, unhook, uninstall)
- Importer how-to

Changed
-----
- Replaced Owl carousel by BxSlider, that is included in default PS installation
- Optimized carousels for faster loading and displaying predefined number of slides for different resolutions
- Some FO and BO layout changes
- Minor code fixes

Files modified
-----
- /democontent/carousels.txt
- /easycarousels.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/carousel-form.tpl
- /views/templates/hook/carousel.tpl
- /views/js/back.js
- /views/js/front.js
- /views/css/back.css
- /views/css/front.css
- /Readme.md

Files added
-----
- /views/css/common-classes.css
- /views/css/bx-styles.css
- /views/templates/admin/hook-exceptions-form.tpl
- /views/templates/admin/hook-positions-form.tpl
- /upgrade/install-1.7.0.php
- /override/index.php
- /override/classes/index.php
- /override/classes/Product.php (prevents native fetching of accessories if they are displayed in carousel)

Files removed
-----
- /views/templates/admin/exceptions-settings-form.tpl

Directories removed
-----
- /views/js/owl/
- /views/css/owl/
- /views/img/owl/

===========================
v 1.1.0 (March 7, 2015)
===========================
Added
-----
- Possibility to import/export carousels
- Possibility to edit hook exceptions on module settings page
- Added counter to hooks selector in BO

Changed
-----
- Demo content is installed from editable file /democontent/carouselts.txt
- Added 'display' prefix to custom hooks (displayEasyCarousel1, displayEasyCarousel2, displayEasyCarousel3)
- Front-office Hooks are registered only after you add carousels to them
- Minor code fixes

Files modified
-----
- /easycarousels.php
- /views/templates/configure.tpl
- /views/js/back.js
- /views/css/back.css
- /translations/ru.php

Files added
-----
- /Readme.md
- /upgrade/install-1.1.0.php
- /upgrade/install-1.0.1.php
- /views/templates/admin/exceptions-settings-form.tpl
- /views/img/grab.cur
- /views/img/grabbing.cur

Files removed
-----
- /readme_en.txt
- /upgrade/upgrade-1.0.1.php

===========================
v 1.0.1 (February 14, 2015)
===========================
Fixed
-----
- Possibility to override carousel.tpl in theme directory
- Minor code fixes

Updated
-----
- Moved 'css', 'js', 'img' to 'views' basing on validator requirements

Added
-----
- Autoupgrage functionality

Directories moved to /views/:
-----
- /js
- /css
- /img

Files modified:
-----
- /easycarousels.php
- /views/templates/hook/carousel.tpl
- /views/templates/admin/configure.tpl
- /views/templates/js/back.js

Files added:
-----
- /upgrade/index.php
- /upgrade/upgrade-1.0.1.php

Files removed:
-----
- /views/templates/hook/product-details.tpl
- /views/templates/hook/manufacturer-details.tpl
- /views/templates/hook/supplier-details.tpl

===========================
v 1.0.0 (February 06, 2015)
===========================
Initial relesase
