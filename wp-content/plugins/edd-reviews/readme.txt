=== Easy Digital Downloads - Product Reviews ===
Contributors: sunnyratilal
Tags: easy digital downloads, reviews, sunny ratilal, sunnyratilal, edd, ratings
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.3.8

Adds a fully featured reviewing system for Easy Digital Downloads.

== Description ==

Reviews allows visitors and customers of your website to review the downloads offered on your website quickly and easily.

Reviews extends the WordPress commenting engine and all review submissions are moderated and can be edited which gives you full control of which reviews you'd like to display.

The add-on comes with built in styling meaning the reviews are presented beautifully on your site meaning it works with the majority of themes without breaking the layout.

The plugin uses HTML5 microdata and abides to the Review schema which search engines like Google see.

Shortcodes are available to easily embed or reference reviews in posts or pages.

Most importantly, the plugin is blazing fast and won't impact the performance on your site.

A widget is also included to display the latest reviews.  The widget also caches the output for better performance.

== Installation ==

1. Upload `edd-reviews` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Insert the License Key from your Purchase History in Downloads > Settings > Misc > Reviews License Key

== Changelog ==

= 1.3.8: October 7, 2014 =
* Fix: PHP Notices when adding a new Download

= 1.3.6: February 11, 2014 =
* Fix: incorrect average rating calculation due to author replies
* Fix: improved some spacing issues

= 1.3.5: January 11, 2014 =
* Fix: Ensure that comments that do not have proper review metadata are not considered reviews

= 1.3.4: January 3, 2014 =
* Fix: Reviews form didn't display on Shop Front theme

= 1.3.3: December 22, 2013 =
* Fix: Bug which didn't allow comments to be posted for different post types
* Fix: Bug which prevented editing standard comments from the WordPress Admin

= 1.3.2: November 23, 2013 =
* Fix: problem with must login message showing erroneously

= 1.3.1: November 20, 2013 =
* Fix: problem with setting for requiring reviewers be customers
* Fix: location of review breakdown
* Fix: compatibility issues with Twenty Thirteen theme

= 1.3: November 2013 =
* Make the templating system a lot more efficient
* Reduce redundant code

= 1.2: September 4, 2013 =
* Add the ability to limit buyers to review downloads
* Add the ability to disable multiple reviews by one person
* Move Settings to the Extensions tab
* Reformat the template code to improve readability
* Make sure admin stylesheet isn't loaded on the frontend

= 1.1.1: August 30, 2013 =
* Fix a bug which caused rendering issues with the shortcode

= 1.1: August 27, 2013 =
* Use the new license handler for updates
* Add new option to disable multiple reviews by the same author
* Reviews are now sent through a filter and verified if the option to disable multiple reviews by the same author is enabled
* Added new filter: edd_reviews_review_not_found_msg
* The [review] shortcode has been updated to allow for multiple reviews to be displayed.
* Increase the security of votes sent via AJAX
* Introduce EDD_Reviews_Shortcode_Review::render_multiple_reviews() to render the shortcode when multiple reviews are requested for display
* Updated documentation

= 1.0.2: July 10, 2013 =
* Make sure that the admin bar menu is only available to users who can moderate comments

= 1.0.1: April 3, 2013 =
* Fix: Nested comments were being treated as reviews
* Tweak: Review overview has been flipeed around (i.e. 5 is now at the top)

= 1.0: April 2, 2013 =
* Initial release.
