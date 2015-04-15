=== Plugin Name ===
Contributors: Rustaurius, EtoileWebDesign
Donate Link: http://www.etoilewebdesign.com/plugin-donations/
Tags: product catalogue, product catalog, restaurant menu, responsive, customizable CSS, SEO friendly, affiliate links,  affiliates, attributes, blog catalog, catalog, catalogue, katalog, commerce, directory, display products, e-commerce, ecommerce, gallery, inventory, list products, manage, plugin, product, product feed, product gallery, product management, product portfolio, products, sales, sell, shipping, shop, shopping, shortcode, shortcodes, store, wp catalog, wp catalogue, widget, widgets, easy to use, user friendly
Requires at least: 3.5.0
Tested up to: 4.1
License: GPLv3
License URI:http://www.gnu.org/licenses/gpl-3.0.html

A responsive and easily customizable plugin for all your product catalogue needs.

== Description ==

Use this plugin to display your products in an a sleek and easy to customize catalogue. Choose between three default responsive layouts or customize it to suit your needs in the Custom CSS. Make your catalogue easy to browse, sort and update with categories, sub-categories, and tags. Simple to add to any page using the [product-catalogue] shortcode!

Perfect for your store, restaurant and more!

= Key Features =

* 3 default layouts (thumbnail, detail and list)
* Categories and sub-categories
* Custom fields
* Custom product pages 
* Fully customizable with CSS
* Widgets to display recent products, product list, random products
* Include catalogues using the [product-catalogue id=’X’] shortcode
* Upload products directly from a spreadsheet
* UTF8 support
* Search function with Javascript or AJAX
* Easy to use attributes like [starting_layout], [excluded_layout]
* Options page that makes it easy to customize features


= Premium Features =
The premium version includes a lot of great features including: the ability to add over 100 products, additional product images, SEO friendly URLs, product tags and custom fields for sorting, custom product pages, a minimalist layout option and more!

* Drag-and-drop product pages layout
* Product tags
* Custom fields that can be used to include product manuals, additional information, etc.
* SEO friendly single product pages
* For a more in depth list, please visit our FAQ page:

<http://www.etoilewebdesign.com/ultimate-product-catalogue-faq>

= Additional Languages =
* Brazilian Portugese (thanks to <a href='http://wordpress.org/support/profile/tito_cadallora'>Tito_Cadallora</a>);
* Canadian French (thanks to Pascale DRP)
* Dutch (Thanks to Martin S.)
* Italian
* Lithuanian (thanks to <a href='http://wordpress.org/support/profile/adart'>AdArt</a>);
* Russian (thanks to Alexander M.)
* Spanish (thanks to Irene L.)
* Turkish (thanks to Ayhan)

Check out our Frequently Asked Questions here:
<https://wordpress.org/plugins/ultimate-product-catalogue/faq/>

Head over to the "Support" forum to report issues or make suggestions:
<https://wordpress.org/support/plugin/ultimate-product-catalogue>

[youtube https://www.youtube.com/watch?v=z6XL7whjY1Q]

Check out more videos on the FAQ page.

== Installation ==

1. Upload the `ultimate-product-catalogue` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place "[product-catalogue id='X']" on the page where you want your catalogue to display, where X is the ID of the catalogue to display
4. *The page that displays the product catalogue needs to be a full-width type page for the catalogue to display correctly*

--------------------------------------------------------------

To get started using the plugin, you can:
- Go to the "Products" tab in the Ultimate Product Catalogue Plugin (UPCP) admin menu, add products one at a time or by uploading a spreadsheet
- Create categories in the "Categories" tab, then create sub-categories in the "Sub-Categories" tab and assign your sub-categories to the correct category
- Create a catalogue in the "Catalogues" tab, then add products, categories, or sub-categories to that catalogue
- Add additional images to a product by clicking on it and then adding them from the "Product Details" screen
- Create tags in the "Tags" tab, then assign as many tags as necessary to each product from the "Product Details" screen
- Re-order your catalogues by dragging and dropping items in the "Catalogue Details" screen


== Frequently Asked Questions ==

= How do I display my catalogue? If I have more than one catalogue, what do I do? =

Put this shortcode on whatever page you’re trying to put the catalogue on: ‘[product-catalogue id=’X’]’ and replace the ‘X’ with the ID of your catalogue. This can be found by clicking on your catalogue, the catalogue ID  is beside the catalogue name on the catalogue’s details page. You can also copy the shortcode directly from the “Catalogues” page.

To display one of your catalogues use the corresponding attribute ‘id='X'’ in the product-catalogue shortcode to specify that catalogue, where 'X' is replaced with the catalogue ID.

= Can my products be in more than one category or sub-category? =

No, products can only be in a single category and sub-category. Products can have as many tags desired, since tags are used to represent product attributes.

= Can I start with a layout other than the default "Thumbnail" layout? =

Add the attribute [starting_layout='LAYOUT’], where LAYOUT is replaced with the layout you would like to be the starting layout (List or Detail are the two options currently).

= I only want to display one layout and to remove the layout bar at the top from my catalogue? =

The attribute ‘excluded_layouts’ lets you stop one or more layouts from being displayed. It accepts a comma separated list of layouts you wish to exclude. For example, [excluded_layouts='Thumbnail, List’] would make your catalogue display only the "Detail" view.
To remove the layout bar you can try adding the following into your Custom.css box (where the colour matches the colour of the catalogue you are using):
‘.Blue-prod-cat-header-div {display: none;}’

= My product description gets cut off even though I have changed the max characters, how can I fix this? =

There is a text area labelled "Custom CSS" when you click on the catalogue that you're editing. Try adding this to it:  ‘.prod-cat-addt-details-desc-div {max-height:none;}’

= How do I remove the sidebar? =

You can use the attribute [sidebar=‘No'] in the product-catalogue shortcode to remove the sidebar from a catalogue.

= Why aren’t my products uploading from the spreadsheet? =

There are a number of reasons why products might not be uploading correctly.
First, make sure that you’re uploading either a .xls or .xlsx file type (not .csv).
Second, make sure that you’ve written all of your field names correctly (Name, Slug, Description, Price, Image, Category, Sub-Category, Tags), especially custom fields, which need to be exactly the same as the field name.
Third, make sure that you don’t have any columns that have been edited unintentionally (which gives an “incorrect field name” error).

= How do I get custom changes I’ve made to the product page layout or the custom fields I’ve added in the product details to display? =

On  the “Options” page the “Custom Product Page” needs to be set to “Yes” for custom layout to display.

= How do I sort the order of the products within the catalogue? =

You can arrange the order of the products by dragging and dropping the products on the “Catalogue Details” page .

= How do I translate the plugin into my language? =
A great place to start learning about how to translate a plugin is at the link below: <http://premium.wpmudev.org/blog/how-to-translate-a-wordpress-plugin>

Once translated, you'll need to put the translated mo- and po- files directly in the lang folder and make sure they are named properly for your localization.
If you do translate the plugin, other users would love to have access to the files in your language. You can send them to us at Contact@EtoileWebDesign.com, and we’ll be sure they’re included in a future release.

= What is enabled in the 'Premium' version? =

The premium version includes a lot of great features including: the ability to add over 100 products, additional product images, SEO friendly URLs, product tags and custom fields for sorting, custom product pages, a minimalist layout option and more!

* Drag-and-drop product pages layout
* Product tags
* Custom fields that can be used to include product manuals, additional information, etc.
* SEO friendly single product pages
* Unlimited products
* Additional images per product
* Pretty permalinks
* More layout options
* Related and Next/Previous product display

For a more in depth list, please visit our FAQ page:
<http://www.etoilewebdesign.com/ultimate-product-catalogue-faq>

For more questions and support you can post in the support forum:
<https://wordpress.org/support/plugin/ultimate-product-catalogue>



= Videos =

Tutorial Part 1
[youtube http://www.youtube.com/watch?v=9sdHtGGZpKU]

Tutorial Part 2
[youtube http://www.youtube.com/watch?v=KBhdiNCtLvM]

Premium Features
[youtube http://www.youtube.com/watch?v=l9mWNqOIB_w]

== Screenshots ==

1. The "detail" catalogue view
2. A product's detailed view, with mutiple images that can be clicked to display in the main image area
3. The "thumbnail" catalogue view
4. The "list" catalogue view
5. The admin area

== Changelog ==
= 3.0.16 =
- Fixed an error with the recent and random product widgets

= 3.0.15 =
- Fixed an error with Next/Previous products

= 3.0.14 =
- Fixed a mobile pagination error
- Fixed a text filtering problem with pagination

= 3.0.13=
- Fixed a small display bug

= 3.0.12 =
- Fixed an error with the access role option

= 3.0.11 =
- Fixed a mobile display issue
- Fixed an error with setting user access role

= 3.0.10 =
- Added a new premium option to set the user role that can access the plugin menus

= 3.0.9 =
- Fixed a related products link error

= 3.0.8 =
- Made searcing more than one custom field at a time possible
- Returned the missing category labels on category pages

= 3.0.7 =
- Fixed a mobile page display error

= 3.0.6 =
- Removed some debugging code accidentally left in from the last update

= 3.0.5 =
- Fixed an error on for checkbox custom field types

= 3.0.4 =
- Fixed a Tags display problem with category items in catalogues

= 3.0.3 =
- Fixed an error with filtering and labels
- Fixed a List-view height adjustment problem

= 3.0.2 =
- Fixed a repeating div that would be added in AJAX filtering

= 3.0.1 =
- Fixed a filtering error

= 3.0.0 =
- Added the ability to search custom fields
- Added a new display style for the catalogue
- Added arguments to the new "insert-products" shortcode, so that products from a category or sub-category can be displayed
- Fixed a number of small display bugs

= 2.6.1 =
- Changed the behaviour of the new insert-products shortcode, when a catalogue_url is included

= 2.6 =
- Added 3 widgets, which let users display a number of products
- Added a new shortcode, insert-products, which lets users insert a small number of products
- Added next/previous products for individual products
- Added related products feature for individual products
- Fixed a "Back to Catalogue" potential link error
- Fixed an image display problem with https sites

= 2.5.12 =
- Fixed error where links would not open in new windows even with the option set

= 2.5.11 =
- Added a search box for products
- Fixed a translation filtering error
- Fixed a redirect error for sub-domains using default WordPress permalink structure

= 2.5.10 =
- Fixed a small product pages bug

= 2.5.9 = 
- Fixes an important jquery error on non-plugin pages

= 2.5.8 =
- Fixed a linking error for users using the default WordPress link structure

= 2.5.7 =
- Added a label for "Back to Catalogue"
- Added an option to not maintain filtering between page loads, to address some redirection issues
- Fixed an export to Excel error
- Fixed a filtering error on custom product pages

= 2.5.6=
- Correct version of Shortcodes.php file included with this version (sorry!)

= 2.5.5 =
- Included the missing "Export to Excel" files
- Added an option to turn on custom field slug conversions (which are not set to off by default)

= 2.5.4 =
- Filtering settings are preserved when using the "Back to Catalogue" link
- Added an option to display a message when no products are found 
- Added a label for the placeholder text in the text search field

= 2.5.3 =
- Fixed a checkboxes error with custom fields
- Additional fix for products not displaying on homepage

= 2.5.2 =
- Fixed a bug that didn't allow catalogues to be displayed on home pages
- Fixed a labelling search bug

= 2.5.1 =
- Added custom fields to Fancyboxes

= 2.5 =
- Allows relabelling of some front-end text, the first in a series of major new features being rolled out over the next few weeks

= 2.4.43 =
- Fixes an image switching error with mobile product pages
- Support for FancyBox for WP is being dropped. We believe the plugins will still work together to create a FancyBox for each product, but we're no longer actively supporting that feature.

= 2.4.42 =
- Fixed a mobile custom product page bug

= 2.4.41 =
- Made the selector for custom product page list elements more selective
- Added a div around custom fields in the thumbnail and details layouts, so that they can displayed or hidden
- Included an improved version of the Brazillian Portugese translation

= 2.4.40 =
- Fixed two potential admin display issues

= 2.4.39 =
- Shortcodes can be used in "text" custom elements
- Fixes custom product page element styles

= 2.4.38 = 
- Removes the grey background from custom product page elements

= 2.4.37 =
- Fixed a potential filtering error

= 2.4.36 =
- New premium feature: ability to export all products to Excel!

= 2.4.35 =
- Added the abilit to put [upcp-price] in a product's description, to include the product's price

= 2.4.34 =
- Fixed a bug on the sub-category details page

= 2.4.33 =
- Fixed a commenting error with AJAX searches

= 2.4.32 =
- Added support for additional shortcodes inside of descriptions

= 2.4.31 =
- Fixed the translation bug where no products would display if "Name" had been translated

= 2.4.30 =
- Fixed a spreadsheet upload warning when no custom fields existed

= 2.4.29=
- Added a new .pot file with many of the missing strings
- Fixed an error that was preventing images from being uploaded

= 2.4.28 = 
- Prices with text and currency signs will now be sorted correctly
- Fixed sorting so that products stay sorted after selecting a category, sub-category or tag

= 2.4.27 =
- Added tooltip help for the "Options" tab
- Made it easier to identify category, sub-category and tag IDs
- Fixed an error where products deleted from catalogues were left in as blank entries

= 2.4.26 =
- Custom product pages minor update
- Fixed a fields label error

= 2.4.25 =
- Added hierarchical sub-categories as a sidebar option 

= 2.4.24 =
- Fixed a product pages CSS error

= 2.4.23 =
- Fixed a javascript error with image zooming in FancyBox
- Updated to the newest version of Gridster

= 2.4.22 =
- Fixed a sorting PHP warning

= 2.4.21 =
- Categories, Sub-Categories and Tags are now listed alphabetically
- Added an option to add pagination links to the bottom of the page
- Fixed a small jQuery error

= 2.4.20 =
- Fixed an error with "Checkbox" type custom fields

= 2.4.19 =
- Fixed a "Custom Product Pages" error
- Fixed an error where the "Product Sort" option was not displaying on the "Options" page

= 2.4.18 =
- "Catalogue ID" can now be included in spreadsheet product uploads to add a product directly to a certain catalogue
- Eliminated "Mobile Stylesheet" as an option, since there is a custom mobile layout option
- Fixed a product page bug that didn't allow images at the start of a product description
- Eliminated the max-width and max-height restrictions on main images on custom product pages
- Renamed French and Spanish translation files so that they should work correctly
- Fixed a bug where mobile layout images couldn't be swapped

= 2.4.17 =
- Fixed a product page error

= 2.4.16 =
- Fixed a product page error
- Fixed the "Back to Catalogue" link

= 2.4.15 =
- Added a second custom product display, "Mobile", that can be used for small-screen devices
- Added options to customize the "Sort By" box so that it can be eliminated or reduced

= 2.4.14 =
- Product link can now be included in a spreadsheet upload
- An XML sitemap of the products in a catalogue is automatically generated each time a product is created or edited

= 2.4.13 =
- Tags should now display correctly on custom product pages
- Categories, Sub-Categories and Tags can now be added as URL parameters (categories, subcategories, tags are the parameters)

= 2.4.12 =
- In the sidebar, Category, Sub-Category and Tag checkboxes should now be ordered by date created

= 2.4.11 =
- Current layout is now saved when visitors switch pages using pagination
- Fixed a height error for pagination

= 2.4.10 =
- Fixed an error when custom fields and tags were uploaded in the same sheet

= 2.4.9 =
- Fixed two custom product page image bugs

= 2.4.8 =
- Added an option to deal with Custom product pages on mobile devices

= 2.4.7 =
- Fixed category and sub-category count when pagination is being used

= 2.4.6 =
- Fixed sorting by name error

= 2.4.5 =
- Extended "AND" logic for Tags to AJAX filtering
- Fixed a small error with "Delete All Products"

= 2.4.4 =
- Fixed the filtering errors with "Molbile Stylesheet"

= 2.4.3 =
- Fixed a responsive CSS error that was stopping clicks from being able to be clicked

= 2.4.2 =
- Fixed an error where multiple custom fields being uploaded from a spreadsheet would sometimes fail
- Added a Russian translation

= 2.4.1 =
- Fixed a custom fields error
- Changed the text on the product pages restore confirmation

= 2.4 =
- Added pagination, allowing large catalogues to be split onto multiple pages
- Fixed a small display error
 
= 2.3.12 =
- Catalogue product count should now be accurate
- Product page grid widths, heights and margins are now adjustable
- "Restore Default" button added to the individual product pages
- Made saving of a custom layout an explicit action instead of saving each time an action was performed

= 2.3.11 =
- It is now possible to put code into the "Image" field instead of the URL of an image (ex: to add a slideshow for a product instead of an image)
- Added a visual editor for "Description" instead of a plain text area input
 
= 2.3.10 = 
- Made it possible to upload "slugs" from a spreadsheet

= 2.3.9 = 
- Updated CSS for single product pages for small screen devices
- Added a advisory on the Custom Product Pages feature tab
- Added a Dutch translation

= 2.3.8 = 
- Fixed a spreadsheet upload bug

= 2.3.7 = 
 - Added a new product search option
 - Fixed an error on the "Product Page" tab
 - Added Spanish translation files

= 2.3.6 = 
- Fixed a search error related to the new options

= 2.3.5 = 
- Make searching more than 1 category at a time possible
- Added an option to search product description as well as name
- Fixed a problem that prevented most users from using the custom product pages feature
- Fixed a number of small CSS problems

= 2.3.4 = 
- Fixed a spreadsheet upload error

= 2.3.3 =
- Fixed a permalinks error after AJAX sorting
- Fixed a "Read more" link error

= 2.3.2 =
- Fixed a JQuery error

= 2.3.1 =
- Fixed a product count error

= 2.3 =
- New premium feature: custom product page design, let's you drag and drop product pages to change the layout in the back-end
- Contact forms and PayPal buttons can be included on product pages with the new layout feature
- Added the ability to add multiple additional images at once
- Custom fields can now be uploaded with products being uploaded by spreadsheet (Limited testing, please let us know if you find any errors with this)
- Added a new custom field type, file, so that PDF's and other files can be included with products
- Added a "Details" image option, so that the arrow can be replaced with any image of your choosing
- Added an italian language translation
- Fixed a small spreadsheet error

= 2.2.12 =
 - Fixed a display bug for individual product pages

= 2.2.11 =
- Major custom fields improvement: allow fields to be displayed on main catalogue pages
- Minor css upgrades to the main catalogue pages

= 2.2.10 =
- Fixed an individual product pages bug

= 2.2.9 =
- Fixed the pretty permalinks rewrites to be compatible with recent WordPress updates
- Added a "Delete All Products" button
- Added the ability to require confirmation
- Added Turkish as a language option

= 2.2.8 =
- Disabled the "Enter" function on the search form
- Fixed spreadsheet upload bugs
- Fixed FancyBoxes after AJAX search

= 2.2.7 =
- Fixed "custom fields" error with no validation entered

= 2.2.6 =
- Added French as a supported language
- Improved the AJAX search function

= 2.2.5 =
- Added css support for a large number of new themes

= 2.2.4 =
- Added case-insensitive search for AJAX filtering

= 2.2.3 =
- Small update for uploading products from spreadsheets

= 2.2.2 =
- Bug fixes for custom fields
- Shortcodes can now be used in product descriptions

= 2.2.1 =
- Two small bug fixes

= 2.2 =
- New Premium feature: Custom Fields!
- Custom fields let you add fields to your products, that can be included in the description of your products via shortcode, so that you can have a common product template
- New feature: Non-displayed products
- Non-displayed products let you temporarily remove a product from all catalogues (ex: if it's out of stock)
- Tags can now be imported via spreadsheet
- Catalogue height now adjusts depending on the size of the current layout
- Tutorial videos are available in the FAQ section

= 2.1.5 =
- Small bug fix
- Some language files added

= 2.1.4 =
- Three small fixes for the front-end and product page

= 2.1.3 =
- Small fix for "Tags" functionality with new AJAX filtering
- Small optimizations to return catalogue quicker after users filter the catalogue

= 2.1.2 =
- Beta AJAX catalogue filtering as an option
- Number of characters in a product's "details" view description added as an option
- Fixed a small catalogue detail's page bug

= 2.1 =
- Implemented view counting for products, based on clicks on title or image links
- Added mobile stylesheet (v1) and product sorting for premium users
- Increased compatibility for uploaded product spreadsheets (more forgiving of small errors in column names, better error reporting)
- Attempted to make tables compatible with MySQL strict mode
- Fixed an error where SEO friendly URL's stopped working shortly after being setup

= 2.0.1 =
- Added in the WordPress Uploader for product images
- SEO friendly single product URLs are now an option
- Plugin tables now use UTF8 encoding

= 2.0 =
- Added an 'Options' page
- Added 'Read More' as an option on the 'Options' page
- Added 'Color Scheme' as an option on the 'Options' page
- Added 'Tags Logic' as an option on the 'Options' page
- Added 'Product Links' as an option on the 'Options' page
- Implemented a premium version for new users

= 1.2 = 
- Added pagination for the admin pages to allow easier access to all products
- Added a 'product link' field for sites participating in affiliate programs
- Made shortcode easier to find
- Added single product pages if FancyBox for WordPress isn't installed
- Fixed the issue with product images being deleted on upgrade
- Fixed category labels behaviour when products are being filtered

= 1.1 = 
- Added localization (hopefully!)
- Added an "initial_layout" shortcode
- Added an "exclude_layouts" shortcode
- Added individual product URLs, when blogs have FancyBox for WordPress installed
- Made a number of small changes

= 1.0 =
Initial Version.

== Upgrade Notice ==

= 2.1.4 = 

- Three small fixes for the front-end and product page
