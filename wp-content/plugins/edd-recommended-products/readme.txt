=== Easy Digital Downloads - Recommended Products ===
Contributors: cklosows
Tags: Easy Digital Downloads, Recommendations
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.2.3.2
Donate link: https://filament-studios.com.com
License: GPLv2 or later

Show recommended products based on other customer purchases.

== Description ==

Recommended Products for Easy Digital Downloads allows you to show a cross sell of recommended products when viewing downloads or the checkout screen, with just a couple of checkboxes. These aren’t “related products” but specifically chosen products based off your stores previous sales. Every night, the recommendations are refreshed to include the day’s sales. You can manually run the update script as well via the Easy Digital Downloads Misc Settings

You can use the [recommended_products] shortcode to display recommenations for a download or downloads. It accepts the following parameters:
ids: a comma seprated list of Download IDs to provide suggestions for (default: current post ID)
user: "true" or "false", wheather to hide already purchased items from displaying (default: false)
count: number of suggestions to show (default: 3)
title: A title to display (default: "Recommended Products")

== Changelog ==
= 1.2.3.2 =
* FIX: Fixed the way that the plugin text-domain is loaded, so translations work, and allowed them to be globally located.

= 1.2.3 =
* TWEAK: Allow the recommendation functions to be filtered with ```edd_rp_multi_recommendation_results``` and ```edd_rp_single_recommendation_results```
* FIX: Issue with Shortcode registration working
* TWEAK: Improved i18n configuration

= 1.2.2 =
* NEW: Shortcode recommended_products for dislaying recommendations where desired.

= 1.2.1 =
* FIX: formatting prices, above buttons and forcing no prices on recommended products buttons
* FIX: Only add a breakspace between title and price if theme doesn't support featured images, or one isn't present
* FIX: Clean up the Template files to be more consistant with template code formatting

= 1.2 =
* FIX: Removing warnings and notices on activation
* FIX: Fixing fatal error when EDD isn't active
* FIX: Updating to use edd_get_option() instead of calling the settings directly.
* UPDATE: Moving to the Extensions tab, instead of Misc settings

= 1.1 =
* NEW: Added new EDD v1.7 licensing system.

= 1.0 =
* NEW: Initial release.
