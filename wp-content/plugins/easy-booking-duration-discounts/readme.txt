==== Easy Booking : Duration Discounts ====
Contributors: @_Ashanna
Tags: woocommerce, booking, renting, products, discounts, duration, price, prices
Requires at least: 4.0, WooCommerce 2.5, WooCommerce Easy Booking 2.0
Tested up to: 4.7.1
Stable tag: 1.7.4
License: GPLv2 or later

Easy Booking : Duration Discounts is an add-on for WooCommerce Easy Booking to add discounts to bookable products depending on the duration booked.

== Description ==

This plugin creates product discounts depending on the duration booked with "WooCommerce Easy Booking".

It allows you to :

- Choose custom discount amounts.
- Choose between "Product % discount", "Product discount", "Total % discount" or "Total discount".
- Define the booked duration to add the discount (E.g : from 10 to 20 days).
- Add as many discounts as you want per product or variation.

The plugin then calculates the new product price when the client orders, depending on the discounts set.

== Installation ==

Make sure you have installed and activated WooCommerce and WooCommerce Easy Booking.

1. Install the “Easy Booking: Duration Discounts” Plugin.
2. Activate the plugin.
3. Enter your license key. For multisites, install the plugin and enter the license key on the network. Then, activate it on each sites.
4. In your administration panel, go to the Easy Booking menu, and the Duration Discounts sub-menu to set the different options and the discounts applied to every products.
5. In your administration panel, go to the bookable product page, and set the discounts you want to apply to the product or variation in the "Bookings" tab (and below the price inputs on the "Variations" tab for variable products). Save the product.
6. And that’s it !

== Changelog ==

= 1.7.4 =

* Fix - Comptibility with Easy Booking 2.0.8 and the future Easy Booking: Pricing extension.
* Fix - Compatibility with WooCommerce Product Bundles > 5.0.
* Updated ebdd.pot file.
* Removed included language files, please visit http://herownsweetcode.com/easy-booking/documentation/duration-discounts/localization/ to download available translation files.

= 1.7.3 =

* Fix - [Admin] Fixed an issue with discount/surcharge amounts with decimals not saving correctly if decimal seperator was not a dot.

= 1.7.2 =

* Compatiblity with Easy Booking 2.0.6.
* Add - [Frontend] Display regular and sale booking prices if the product is on sale.

= 1.7.1 =

* Fix - Issue with PDF Catalog plugin.

= 1.7.0 =

* Add - [Feature] Compatibility with one date only (no price calculation, no discount applied).
* Add - [Feature] Compatibility with WooCommerce Product Bundles.
* Fix - [Frontend] Issue when discount amount is 0 (used to override a global discount).
* Fix - [Frontend] Small fixes for price calculation.
* Fix - [Admin] Issue with the discounts table showing on the first variation only.

= 1.6.1 =

* Fix - [Frontend] Issue with non integer discount amounts.

= 1.6 =

* Add   - [Feature] Compatiblity with weekly and custom booking duration.
* Add   - [Admin] Variation parent product discounts.
* Fix   - [Frontend] price calculation with or without taxes.
* Fix   - [Admin] Price inputs for discounts (inputs type number instead of text).
* Tweak - Reviewed, updated and improved code to optimize the plugin.

= 1.5.1 =

* Fix     - [Frontend] Global discounts are now calculated.
* Fix     - [Frontend] Set the price to 0 if the discounted price is inferior or equal to 0.
* Fix     - [Localization] Plural forms were not translated correctly.
* Removed - [Admin] Removed the price calculation on the order page when modifying dates.

= 1.5 =

* Add   - [Feature] Global discounts to apply to all products.
* Add   - [Feature] Allow discount amount to 0 in case you want to unset a global discount for a particular product.
* Fix   - [Admin] Undefined $overlap variable when saving a product.
* Fix   - [Frontend][Grouped product] Discounts are not displayed on the archive page anymore.
* Tweak - [Frontend] Sort discounts before displaying them.

= 1.4.2 =

* Add - Multisite compatibility.
* Tweak - Delete database entries for each sites on multisites when uninstalling the plugin.

= 1.4.1 =

* Add - WCEB_PATH and WCEB_SUFFIX constants to load scripts and styles.
* Add - ebdd.pot file for translations.
* Fix - Set $overlap variable to false to prevent undefined.
* Fix - Update variations after adding or removing a discount.
* Update - French translation.

= 1.4 =

* Add - Compatibility with grouped products.
* Fix - Wrong calculation with prices excluding tax.
* Fix - Update process.
* Fix - Discount calculation when settings "From" to zero.

= 1.3 =

* Add - Option to display discounts as price or reduction.
* Fix - Error when setting overlapping discounts.
* Tweak - Better discount display.

= 1.2.1 =

* Fix - Error when no discount is set.
* Fix - Price calculation when no end is set.

= 1.2 =

For further information about this update, please visit the blog : https://herownsweetway.com/whats-new-in-easy-booking-duration-discounts-1-2/

* Add - Discount action : possibility to apply surcharges instead of discounts.
* Add - Option to display or not available discounts on the product page.
* Add - Option to choose between normal and cumulative mode for discounts (normal mode will only apply the discount corresponding to the selected duration, whereas cumulative mode will cumulate every discounts until selected duration).
* Fix - Overlapping discounts when setting 0 to start and end limits.

= 1.1 =

* Fix - Single amount discount not displayed in the admin
* Fix - Variable products discount calculation
* Fix - Overlaping days when saving products
* Fix - Automatic update system

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.7.2 =

This version requires WooCommerce Easy Booking 2.0.6.

= 1.7.0 =

This version requires WooCommerce Easy Booking 2.0.

= 1.6 =

This version requires WooCommerce Easy Booking 1.9, please update it before or it won't work.