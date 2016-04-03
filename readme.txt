=== Multisite Directory ===
Contributors: meitar, aurovrata
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=TJLPJYXHSRBEE&lc=US&item_name=Multisite%20Directory%20WordPress%20Plugin&item_number=multisite-directory&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: multisite, network, taxonomy, posts
Requires at least: 4.4
Tested up to: 4.5
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a browseable, flexible directory of the sites in a WP Multisite network. Each subsite gets its own page.

== Description ==

Multisite Directory provides a customizable Network-wide site directory for WordPress Multisite installations. You can categorize subsites in your Network, add full-length descriptions and featured images to them, and more. Theme developers can customize the look and feel of the site directory using all the tools they're already familiar with. Blog authors can use a highly-configurable shortcode (`[site-directory]`) to display 

Each time a new site or blog is added to your Network, a corresponding entry in the site directory is added, too. The directory is managed from the Network's main site, but most changes to the subsites (other blogs) automatically update the directory. Many parts of a given site's appearance in the directory can be modified without affecting the site itself. This means a Super Admin can use a custom site logo, tagline, and so on in the directory itself, without changing the site's *actual* logo, tagline, and so on.

Site directory entries are implemented as a hierarchical custom post type in the main site. These pages can be categorized with a custom `subsite_category` taxonomy. We've found that this combination allows maximum flexibility while providing the tightest integration with existing WordPress core features. As a result, no new tables are added to your install, and no side effects are introduced. It Just Works.(TM)

**Quickstart guide**

After [installing](https://wordpress.org/plugins/multisite-directory/installation/) the plugin, you'll want to:

1. As a Super Admin, go to My Sites &rarr; Network Admin &rarr Sites. Notice the new "Categories" link on the sidebar.
1. Create some categories! If the category relates to a location (maybe it's a site for a regional chapter of your organization?), be sure to click the map to geotag it.
1. Click on "Sites," immediately above "Categories." These pages are the individual directory entries, *not* the blogs. Categorize these pages as you would regular WordPress Pages to organize your Site Directory.
1. Go any site's Appearance &rarr; Widgets screen, and add a "Network Directory Widget" to a widget area.
1. If you've geotagged some of your Site Categories, choose "Display as list" from the widget options. (Otherwise, leave it on "Display as list.")
1. Click *Save*, view your blog, and enjoy your new Site Directory!

Check out the [Screenshots](https://wordpress.org/plugins/multisite-directory/screenshots/) for a few examples.

== Installation ==

Multisite Directory is for WordPress Multisite installations only. Do not use this plugin for single-site installs.

To manually install Multisite Directory:

1. Upload the unzipped `multisite-directory.zip` file to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Customize your site directory in the main site's Dashboard.

== Frequently Asked Questions ==

= How do I customize the directory? =

Edit your theme's templates! :) This plugin makes no assumptions about the look-and-feel of your directory so that any theme that wants to can customize it. You only need to customize your theme if you want to change the default appearance.

As a Theme author, you will probably want to create at least two new files:

* `archive-network_directory.php`
* `taxonomy-subsite_category.php`

The `archive-*` page is the main directory page. It will list all the sites in your multisite network. The `taxonomy-*.php` page will list a subset of the sites in your network based on their categorization. You can customize these files as you would any other theme file.

If you do not create these files, then the [default WordPress template hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/) will take over, meaning that either the `archive.php` or `index.php` template file in your main site's currently active theme will be used to display the network directory itself and one of `taxonomy.php` or `index.php` will be used to display a filtered view of your directory's categories.

== Screenshots ==

1. Multisite Directory adds a "Categories" entry to your Sites menu so you can organize your sites using the familiar Category interface.

2. Site categories include geotagging capabilities out-of-the-box, so you can easily group your sites by geographic region.

3. The plugin includes a Network Directory Widget as well as a shortcode (`[site-directory]`) that make it easy to publish a simple Multisite Network Directory in a post, page, or widget area.

4. When users create new sites, they can categorize their blog under the scheme you created. Once their site is created, however, the Super Admin can always re-categorize the new site at will.

== Changelog ==

= Version 0.1.1 =

* [Bugfix](https://wordpress.org/support/topic/problem-with-shortcode-on-page): Fix PHP warning when no attributes are passed to the shortcode.

= Version 0.1 =

* First public release.

== Other notes ==

This plugin provides a number of functions to Theme authors so that they can make use of the Multisite Network Directory features in their themes. This section documents those functions. For implementation details, see this plugin's `includes/functions.php` file.

= get_site_directory_terms =

Gets all categories in the site directory.

* @return array|false|WP_Error

= Get site terms =

Gets the categories assigned to a given blog in the network directory.

* @param int $blog_id
* @return array|false|WP_Error

= the_site_directory_logo =

Prints the site's custom logo or the site directory entry's featured image, if it has one.

* @param int $blog_id Optional. The ID of the site whose logo to get. Default is the current directory entry's site's logo.
* @param string|int[] $size
* @param string|string[] $attr
* @return void
