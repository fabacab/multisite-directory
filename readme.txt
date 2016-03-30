=== Multisite Directory ===
Contributors: aurovrata, meitar
Donate link: http://syllogic.in
Tags: multisite, network, taxonomy, posts
Requires at least: 4.4.0
Tested up to: 4.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a browseable, flexible directory of the sites in a WP Multisite network. Each subsite gets its own page.

== Description ==

Multisite Directory provides a customizable Network-wide site directory for WordPress Multisite installations from the Network's main site. You can categorize subsites in your Network, add full-length descriptions and featured images to them, and more. Theme developers can customize the look and feel of the site directory using all the tools they're already familiar with.

Each time a new site or blog is added to your Network, a corresponding entry in the site directory is added, too.

Site directory entries are implemented as a hierarchical custom post type in the main site. These pages can be categorized with a custom "subsite category" taxonomy. This combination allows maximum flexibility while providing the tightest integration with existing WordPress core features, without any custom tables or unexpected side effects.

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

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= Version 0.1 =

* First public release.
