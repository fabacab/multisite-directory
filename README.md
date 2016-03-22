# taxonomy-for-network-sites
=== Network-wide posts ===
Contributors: aurovrata
Donate link: http://syllogic.in
Tags: multisite, network, taxonomy, posts
Requires at least: 3.0.1
Tested up to: 4.4.1
Stable tag: 4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

.

== Description ==

Users

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `network-wide-posts.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

== 0.1 ==

The plugin is able to create a hierarchical taxonomy the Network dashboard.  This is done through a Sites->Category page (see screenshot 1).  
Terms can be added, edited, deleted.

Once a hierarchy is created, these can be assigned to Sites->All Sites dahsboard table.  A new quick-edit menu has been added to each site row.
Hovering on the row will show a 'Quick edit' menu which allows the terms to be checked for that given site. (see screenshot 2 and 3)

The terms are stored in 2 tables, wp_network_taxonomy_term, and wp_network_term_relationship.

== Future updates ==

Enable assignment of terms when creating new site using the new filers introduced in WP (see https://core.trac.wordpress.org/ticket/34739)

Allow child-sites Admins to add their site to a given category.

A funciton Retrieve a an array of sites for a given term for theme developers.

A function to look up a parent site, a child site can look up its current category term and retrieve a parent term, 
and an array of parent sites.

A function to look up the child site, the reverse of the above function.  This will alowa site to list its child in the hierarchy.

A menu addition in all site dahsboard to allow admins to add menu links to other child sites in a given category term.

Clean up the plugin and move it to cleaner strucutre, possibly boilerplate template 3.0



== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
