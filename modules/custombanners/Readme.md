Module is installed in a regular way - simply upload your archive and click install

CHANGELOG:
===========================
3.0.1 (December 5, 2024)
===========================
- [*] Fixed copying data to other languages
- [*] Improved compatibility with PrestaShop Edition Basic UI
- [*] Minor bug fixes and optimizations

Files modified:
-----
- /classes/BannerImg.php
- /custombanners.php
- /views/css/back.css
- /views/js/back.js
- /views/css/common-classes.css
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/wrapper-form.tpl

Files added:
-----
- /views/css/ps-edition-basic.css

===========================
3.0.0 (December 23, 2023)
===========================
- [+] Compatibility with hooks: displayAfterProductThumbs, displayCustomerLoginFormAfter
- [+] Lazy load images in modern browsers
- [+] Compatibility with Swiper 11 - modern mobile touch slider
- [*] Optimized carousel layout to minimize CLS issues
- [*] Fixed datepicker in BackOffice
- [*] Improved compatibility with PS 8.1
- [*] Minor bug fixes and optimizations

Files modified:
-----
- /classes/BannerImg.php
- /classes/BannersData.php
- /defaults/data-16.zip
- /defaults/data.zip
- /custombanners.php
- /upgrade/install-2.7.0.php
- /upgrade/install-2.8.0.php
- /upgrade/install-2.9.9.php
- /views/css/adapter/bx-adapter.css
- /views/css/back.css
- /views/css/front.css
- /views/js/adapter/bx-adapter.js
- /views/js/adapter/swiper3-adapter.js
- /views/js/back.js
- /views/js/slider.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/admin/input.tpl
- /views/templates/admin/wrapper-form.tpl
- /views/templates/hook/banners.tpl

Files added:
-----
- /.htaccess
- /upgrade/install-3.0.0.php
- /views/css/adapter/swiper3-adapter.css
- /views/css/adapter/swiper4-adapter.css
- /views/css/adapter/swiper5-adapter.css
- /views/css/lib/swiper11.css
- /views/js/adapter/swiper4-adapter.js
- /views/js/adapter/swiper5-adapter.js
- /views/js/lib/swiper11.js

===========================
2.9.9 (March 4, 2023)
===========================
- [+] Compatibility with PS 8.0+
- [+] Configurable image fields: file_name, title, alt
- [+] Compatibility with hook displayHeaderCategory
- [*] Refactored DB structure
- [*] Fixed bug with incorrectly saving banner positions
- [*] Minor bug fixes and optimizations

Files modified:
-----
- /classes/BannerImg.php
- /classes/BannersData.php
- /classes/OptimizerModel.php
- /classes/retro.php
- /classes/TinyPNGOptimizer.php
- /custombanners.php
- /defaults/data.zip
- /readme_en.pdf
- /upgrade/install-2.0.0.php
- /upgrade/install-2.7.0.php
- /upgrade/install-2.8.0.php
- /upgrade/install-2.9.5.php
- /views/css/back.css
- /views/css/common-classes.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/form-group.tpl
- /views/templates/admin/wrapper-form.tpl
- /views/templates/hook/banners.tpl

Files added:
-----
- /defaults/data-16.zip
- /upgrade/install-2.9.9.php
- /views/templates/admin/img-preview.tpl
- /views/templates/admin/input.tpl

Files removed:
- /defaults/data-17.zip

===========================
2.9.8 (August 11, 2022)
===========================
- [+] Possibility to display different banners based on device type: mobile/tablet/desktop
- [*] Misc fixes and optimizations

Files modified:
-----
- /classes/retro.php
- /custombanners.php
- /upgrade/install-2.9.6.php
- /upgrade/install-2.9.7.php
- /views/css/back.css
- /views/css/common-classes.css
- /views/js/back.js
- /views/js/slider.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/form-group.tpl

Files added:
-----
- /upgrade/install-2.9.8.php

===========================
2.9.7 (January 20, 2022)
===========================
- [+] Possibility to upload WebP images with fallback for browsers not supporting that format
- [+] Possibility to compress images using TinyPNG, ReSmushit and others methods
- [*] Fixed saving backslashes in custom CSS/JS in PS 1.6
- [*] Improved compatibility with PS 1.7.8
- [*] Misc fixes and optimizations

Files modified:
-----
- /custombanners.php
- /readme_en.pdf
- /upgrade/install-2.0.0.php
- /upgrade/install-2.9.5.php
- /upgrade/install-2.9.6.php
- /views/css/adapter/bx-adapter.css
- /views/css/back.css
- /views/css/common-classes.css
- /views/css/front.css
- /views/js/back.js
- /views/js/slider.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/form-group.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/hook/banners.tpl

Files added:
-----
- /classes/BannerImg.php
- /classes/BannersData.php
- /classes/BasicOptimizer.php
- /classes/NoOptimizer.php
- /classes/OptimizerModel.php
- /classes/ReSmushitOptimizer.php
- /classes/retro.php
- /classes/TinyPNGOptimizer.php
- /classes/WebP.php
- /upgrade/install-2.9.7.php
- /views/templates/admin/optimizer-how-to.tpl

Directories added:
-----
- /views/img/uploads/orig/

===========================
2.9.6 (January 28, 2021)
===========================
- [+] New display type: Accordion
- [+] Support for hook displayWrapperBottom
- [+] Added width/height attributes for banner images
- [*] Updated some class names in layout
- [*] Misc fixes and optimizations

Files modified
-----
- /custombanners.php
- /defaults/data.zip
- /defaults/data-17.zip
- /views/css/back.css
- /views/css/front.css
- /views/js/adapter/bx-adapter.js
- /views/js/back.js
- /views/js/slider.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/wrapper-form.tpl
- /views/templates/hook/banners.tpl

Files added
-----
- /classes/retro.php
- /upgrade/install-2.9.6.php
- /views/js/front.js

===========================
2.9.5 (June 15, 2020)
===========================
- [+] Configurable slider library: Swiper/BxSlider
- [+] Optionally load or don't load custom slider library
- [+] New exception rules: subcategory pages, product pages of selected manufacturers/categories
- [+] New carousel option: custom margin between items
- [+] Optionally copy selected fields to other languages on saving banners
- [+] Support for hooks displayAfterBodyOpeningTag & displayBeforeBodyClosingTag
- [*] Slightly updated configuration interface, improved CSS/JS code editor
- [*] Load theme styles in TinyMCE editor
- [*] Misc fixes and optimizations

Files modified
-----
- /custombanners.php
- /views/css/back.css
- /views/css/common-classes.css
- /views/css/front.css
- /views/js/back.js
- /readme_en.pdf
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/form-group.tpl
- /views/templates/admin/importer-how-to.tpl

Files added
-----
- /upgrade/install-2.9.5.php
- /views/css/adapter/bx-adapter.css
- /views/css/lib/swiper3.css
- /views/css/lib/swiper4.css
- /views/css/lib/swiper5.css
- /views/css/mce.css
- /views/js/adapter/bx-adapter.js
- /views/js/adapter/swiper3-adapter.js
- /views/js/lib/swiper3.js
- /views/js/lib/swiper4.js
- /views/js/lib/swiper5.js
- /views/js/slider.js

Files removed
-----
- /views/js/front.js
- /views/templates/admin/custom-file-form.tpl

Directories removed
-----
- /views/js/custom/
- /views/css/custom/

===========================
2.9.3 (February 25, 2018)
===========================
- [+] 2 new custom hooks: displayCustomBanners6, displayCustomBanners7
- [*] Retro compatibility fix for modals in PS < 1.6.0.9
- [*] Fixed admin listing with banners having different wrappers in different shops
- [*] Minor fixes and optimizations

Files modified
-----
- /custombanners.php
- /views/templates/admin/configure.tpl

===========================
2.9.2 (September 20, 2017)
===========================
- [+] Optionally display banner on all pages of selected type except specified IDs

Files modified
-----
- /custombanners.php
- /views/js/back.js
- /views/templates/admin/banner-form.tpl

===========================
2.9.1 (August 12, 2017)
===========================
- [+] Optional second image on hover
- [+] Dynamic classes for visible carousel items: first, middle, last
- [*] Minor fixes and optimizations

Files modified
-----
- /custombanners.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/hook/banners.tpl
- /views/templates/admin/banner-form.tpl
- /views/css/front.css

===========================
2.9.0 (July 24, 2017)
===========================
- [+] Scheduled publication
- [+] New carousel options: Loop, Ticker mode, Autoplay time interval, Pause autoplay on hover
- [+] Admin interface for quick banners search
- [*] Added alt to images
- [*] Minor fixes and optimizations

Files modified
-----
- /custombanners.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/hook/banners.tpl
- /defaults/data-17.zip

Files added
-----
- /upgrade/install-2.9.0.php

===========================
2.8.2 (February 26, 2017)
===========================
- [+] Possibility to display a random banner on every page load
- [*] Special labels for banners with with exceptions (BO)

Files modified
-----
- /custombanners.php
- /views/templates/admin/banner-form.tpl
- /views/css/back.css

===========================
2.8.1 (February 21, 2017)
===========================
- [*] Improved importing data from previous versions
- [*] Minor improvements

Files modified
-----
- /custombanners.php
- /views/templates/admin/configure.tpl
- /views/js/back.js

===========================
2.8.0 (December 21, 2016)
===========================
- [+] Display banners only for selected customers/customer groups
- [*] Compatibility with PS 17
- [*] Minor fixes and optimizations

Files modified
-----
- /custombanners.php
- /defaults/data.zip
- /readme_en.pdf
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl

Files added
-----
- /defaults/data-17.zip
- /upgrade/install-2.8.0.php

===========================
2.7.1 (November 14, 2016)
===========================
- [*] Retro compatibility for exceptions on controller-pages: "auth" and "compare"

Files modified
-----
- /custombanners.php

===========================
2.7.0 (September 22, 2016)
===========================
- [+] Configurable wrappers. Make sure you update overriden banners.tpl and custom css/js codes (if you use any)
- [+] Improved exceptions mechanism: display hook only on selected pages
- [+] Official documentation
- [*] Fixed carousel responsivity on resizing window
- [*] Optimized hook layout. Removed empty containers
- [*] Data is installed/imported for all available shops, not only for those in current context
- [*] Misc code optimizations

Files modified
-----
- /custombanners.php
- /views/css/back.css
- /views/css/front.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/hook-exceptions-form.tpl
- /views/templates/admin/importer-how-to.tpl
- /views/templates/hook/banners.tpl --------------------> IMPORTANT!
- /defaults/data.zip

Files added
-----
- /views/templates/admin/wrapper-form.tpl
- /views/templates/admin/form-group.tpl
- /readme_en.pdf
- /upgrade/install-2.7.0.php

===========================
v 2.5.4 (November 12, 2015)
===========================
- [+] Added russian translation
- [-] Fixed autoplay for carousels
- [-] Fixed importing custom js/css for multishop
- [*] Minor code optimizations
- [*] PSR-2

Files modified
-----
- /custombanners.php
- /views/css/back.css
- /views/js/front.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/hook-carousel-form.tpl
- /translations/ru.php
- /Readme.md
- /logo.png

===========================
v 2.5.3 (September 23, 2015)
===========================
- [+] Possibility to delete all banners in current shop context

Files modified
-----
- /custombanners.php
- /views/templates/admin/configure.tpl
- /views/css/back.css
- /views/js/back.js
- /Readme.md
- /logo.png

===========================
v 2.5.2 (June 28, 2015)
===========================
Changed
-----
- Keep hook positions and exceptions for other shops during reset
- Automatically unhook module after last banner in hook is deleted
- Minor code optimizations

Files modified
-----
- /custombanners.php
- /Readme.md

===========================
v 2.5.1 (June 26, 2015)
===========================
Fixed
-----
- Fix for empty links, defined by id (product, category etc.)
- Added 'UTF-8' to escape modifiers in tpl-s, basing on validator requirements

Files modified
-----
- /custombanners.php
- /views/templates/admin/banner-form.tpl
- /views/templates/admin/configure.tpl
- /views/templates/admin/custom-file-form.tpl
- /views/templates/admin/hook-carousel-form.tpl
- /views/templates/admin/hook-exceptions-form.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/hook/banners.tpl
- /Readme.md

===========================
v 2.5.0 (April 5, 2015)
===========================
Added
-----
- Custom classes for banners
- Custom css/js, compatible with multishop
- Restrictions by products/categories/manufacturers/suppliers/cms
- Manipulating other modules in hook position settings (activate/deactivate, unhook, uninstall)
- Some new editable settings for slider
- Moving banners between hooks
- Bulk actions for banners (activate/deactivate, move, copy, delete)
- Instruction for using the importer, easily accessible on module config page

Changed
-----
- Replaced Owl carousel by BxSlider, that is included in default PS installation
- Modified BxSlider to display predefined number of slides for different resolutions
- Optimized some layout elements in backoffice for faster loading
- Updated the predefined content, imitating default banners layout and slider
- When installing/uninstalling the module, only banners for selected context shops are affected. Same for importing.

Files modified
-----
- /custombanners.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/banner-form.tpl
- /views/templates/hook/banners.tpl
- /views/js/back.js
- /views/css/back.css
- /views/css/front.css
- /defaults/data.zip
- /Readme.md

Directories added
- /views/js/custom/
- /views/css/custom/

Files added
-----
- /upgrade/install-2.5.0.php
- /views/templates/admin/importer-how-to.tpl
- /views/templates/admin/hook-exceptions-form.tpl
- /views/templates/admin/hook-carousel-form.tpl
- /views/templates/admin/hook-positions-form.tpl
- /views/templates/admin/custom-file-form.tpl
- /views/css/common-classes.css
- /views/css/custom/index.php
- /views/js/custom/index.php

Files removed
-----
- /views/templates/admin/exceptions-settings-form.tpl
- /views/templates/admin/carousel-settings-form.tpl
- /views/templates/admin/positions-settings-form.tpl

Directories removed
-----
- /views/js/owl/
- /views/css/owl/
- /views/img/owl/


===========================
v 2.0.0 (March 25, 2015)
===========================
Added
-----
- Advanced hook settings: exceptions, positions, carousel
- Drag-n-drop on image upload
- Advanced link creation by id: product link, category link etc.
- Possibility to copy banner to any hook
- Editable caption
- Autoupgrade: file locations and database tables are updated automatically on uploading new version
- Magic quotes warning

Changed
-----
- Updated user interface in BO
- Optimized tinyMCE loading
- Optimized Hook registrations: only used hooks are registered
- Improved export/import/installation: included page exceptions and module positions information
- Changed and optimized database tables

Fixed
-----
- Multisop issues on import/export/install
- Minor code fixes

Directories moved
-----
- /js/  -> /views/js/
- /css/ -> /views/css/
- /img/ -> /views/img/

Files modified
-----
- /custombanners.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/banner-form.tpl
- /views/templates/hook/banners.tpl
- /views/js/back.js
- /views/css/back.css
- /views/css/front.css

Files added
-----
- /Readme.md
- /upgrade/install-2.0.0.php
- /views/templates/admin/exceptions-settings-form.tpl
- /views/templates/admin/carousel-settings-form.tpl
- /views/templates/admin/positions-settings-form.tpl

Files removed
-----
- /views/templates/admin/carousel-form.tpl

Directories removed
-----
- /views/templates/front/

===========================
v 1.5.0 (February 20, 2015)
===========================
Changes not documented

===========================
v 1.0.0 (2014)
===========================
Initial relesase
