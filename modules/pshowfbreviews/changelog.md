PSHOWFBREVIEWS CHANGELOG

## v1.13.38 - 2025-03-25

### Fixed
* The problem with event name "ViewCms"

## v1.13.37 - 2025-02-18

### Changed
* The optimization of collecting fb pixel events

## v1.13.36 - 2025-01-30

### Fixed
* The compatibility problem with php 8.1+

## v1.13.35 - 2024-11-06

### Fixed
* The problem with unused hook

## v1.13.34 - 2024-10-29

### Fixed
* The problem with cart page in prestashop 8.1

## v1.13.33 - 2024-10-25

### Fixed
* The problem with send PageView event on product page

## v1.13.32 - 2024-10-16

### Fixed
* The problem with disabling fb pixel

## v1.13.31 - 2024-10-11

### Added
* The ability to choose statuses that orders must have to be sent to fb pixel

## v1.13.30 - 2024-09-20

### Fixed
* The problem with send some events on product page

## v1.13.29 - 2024-09-04

### Fixed
* The problem with send add to cart event to fb pixel

## v1.13.28 - 2024-05-31

### Fixed
* The problem with missing ViewContent event on product page
* The problem with too many decimal in the order amount in the Purchase event
* The problem with missing variables in the Purchase event

## v1.13.27 - 2024-05-21

### Added
* The information about the most recently run cron job

### Fixed
- The compatibility problem with prestashop 1.7.5.2 and lower

## v1.13.26 - 2024-05-21

### Fixed
* The problem with date in events sent to Facebook Pixel

## v1.13.25 - 2024-05-20

### Fixed
* The problem with create the fbp variable in the cookie in the firefox browser

## v1.13.24 - 2024-02-21

### Fixed
* The problem with duplicate addtocart events
* The problem with missing fbp and fbc variables in cookies

## v1.13.23 - 2024-02-14

### Fixed
* The problem with send correct order value

## v1.13.22 - 2024-02-06

### Added
* The ability to send events simultaneously using javascript and conversion API

## v1.13.21 - 2024-01-29

### Fixed
* The compatibility problem with prestashop 1.6

## v1.13.20 - 2024-01-27

### Fixed
* The problem with display logs after sending events to fb pixel using cron

## v1.13.19 - 2024-01-27

### Fixed
* The problem with sending events to fb pixel

## v1.13.18 - 2024-01-25

### Fixed
* The problem with translations in the module

## v1.13.17 - 2024-01-18

### Changed
* The format of sent ip address to fb pixel from ipv4 to ipv6

## v1.13.16 - 2024-01-11

### Fixed
* The problem with duplicate pageview events

## v1.13.15 - 2023-12-19

### Fixed
* The problem with sending some events to fb pixel

## v1.13.14 - 2023-12-18

### Fixed
* The problem with too long variables

## v1.13.13 - 2023-12-15

### Fixed
* The compatibility problem with older versions of prestashop

## v1.13.12 - 2023-12-13

### Fixed
* The problem with logs when sending events to fb pixel

## v1.13.11 - 2023-12-09

### Added
* The support for more events, and more parameters added to events

## v1.13.10 - 2023-11-23

### Fixed
* The problem with sending the pageview event via browser pixel

## v1.13.9 - 2023-11-03

### Fixed
* The problem with cron job in prestashop 8.1

## v1.13.8 - 2023-10-11

### Added
* The ability to send all events via browser pixel

## v1.13.7 - 2023-08-28

### Fixed
* The problem with sending orders to fb on prestashop 1.6

## v1.13.6 - 2023-08-12

### Fixed
* The problem with undefined function on category page

## v1.13.5 - 2023-08-10

### Fixed
* The problem with products without a default category

## v1.13.4 - 2023-07-26

### Fixed
* The problem with duplicate class name

## v1.13.3 - 2023-07-21

### Added
- Set default option send events by CRON
## v1.13.2 - 2023-06-06

### Fixed
* The problem with token in urls from module

## v1.13.1 - 2023-05-29

### Fixed
* The problem with sending orders to fb pixel when the customer's email has an incorrect structure

## v1.13.0 - 2023-05-19

### Fixed
* The problem with missing fbp and fbc variables in orders sent to fb pixel

### Added
- Add support for Prestashop 8.0 and PHP 8.1
- Add support for themes with installed elementor module

## v1.12.0 - 2023-03-25

### NEW
* Improvements to Facebook API connection
* Better conversion and traffic sources tracking

## v1.11.12 - 2023-03-24

### Fixed
* The problem with sending events to fb pixel

## v1.11.11 - 2023-03-13

### Fixed
* The problem with module installation
* The problem with missing ip address in orders sent to fb pixel

### Added
* The ability to enable/disable sending information about the customer's ip address to fb pixel with orders

## v1.11.10 - 2023-03-01

### Fixed
* The problem with missing content id variable in content view event

## v1.11.9 - 2023-02-09

### Fixed
* The problem with missing fbp variable in cookies
* The problem with downloading all necessary variables to fb pixel when the display reviews function is enabled

## v1.11.8 - 2023-01-27

### Fixed
* The problem with sending page view event using cron

## v1.11.7 - 2023-01-23

### Added
* The ability to send events only using cron

## v1.11.6 - 2022-12-07

### Fixed
* The problem with events ids

## v1.11.5 - 2022-11-24

### Fixed
* The problem with sending events to fb pixel

## v1.11.4 - 2022-11-17

### Fixed
* The problem with adding products to the cart after configuring reviews

## v1.11.3 - 2022-11-09

### Fixed
* The problem with missing "event id" value in events

## v1.11.2 - 2022-08-19

### Fixed
* The problem with performance in sending events to fb pixel

## v1.11.1 - 2022-07-07

### Added
- Send personal customer data with 'purchase' event

## v1.11.0 - 2022-06-21

### Fixed
* The problem adding a product with an invalid category id to the cart.

### Added
* added ViewContent event

v1.10.0 - 23/07/2021
* improved server side event sending
* fixed reported bugs

v1.9.0 - 30/06/2021
* send events using conversion api (backend) to facebook

v1.8.0 - 14/06/2021
* fixed problems with conversions

v1.7.0 - 05/06/2021
* fb pixel support added

v1.6.0 - 05/08/2019
* fixed problems with download and display reviews from facebook

v1.5.0 - 23/05/2019
* the name of the module has been changed
* module settings have been expanded with additional functions

v1.4.0 - 09/02/2019
* adaptation of the module for the new feedback from facebook

v1.03.0 - 27/09/2018
* bug fixes
* new features
* new layout

v1.1 - 23/08/2018
*added compatibility with prestashop 1.7+

v1.0.0

* manage categories and entries
* assign category/entry to any hook from PrestaShop
* call module from tpl files and CMS pages 
