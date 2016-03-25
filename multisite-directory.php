<?php
/**
 * The Multisite Directory plugin for WordPress.
 *
 * WordPress plugin header information:
 *
 * * Plugin Name: Multisite Directory
 * * Plugin URI: https://wordpress.org/plugins/multisite-directory/
 * * Description: Adds a Network-wide site directory to your WP Multisite network.
 * * Version: 0.0.1
 * * Author: Meitar Moscovitz <meitar@maymay.net>
 * * Author URI: https://maymay.net/
 * * Text Domain: multisite-directory
 * * Domain Path: /languages
 *
 * @link https://developer.wordpress.org/plugins/the-basics/header-requirements/
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

if (!defined('ABSPATH')) { exit; } // Disallow direct HTTP access.

/**
 * Plugin class.
 */
class WP_Multisite_Directory {

    /**
     * Registers plugin functionality with the WordPress API.
     */
    public static function register () {
        require_once 'includes/class-multisite-directory-taxonomy.php';
        require_once 'includes/class-multisite-directory-entry.php';
        require_once 'includes/functions.php';
        require_once 'admin/class-multisite-directory-admin.php';

        add_action('init', array(__CLASS__, 'initialize'));
        add_action('wpmu_new_blog', array(__CLASS__, 'wpmu_new_blog'));
        add_action('network_admin_menu', array('WP_Multisite_Directory_Admin', 'network_admin_menu'));

        add_filter('dashboard_glance_items', array(__CLASS__, 'dashboard_glance_items'));

        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
    }

    /**
     * Sets up custom post type and taxonomy data structures.
     *
     * @link https://developer.wordpress.org/reference/hooks/init/
     */
    public static function initialize () {
        $tax = new Multisite_Directory_Taxonomy();
        $tax->register();

        $cpt = new Multisite_Directory_Entry();
        $cpt->register();
    }

    /**
     * Adds a new directory entry to the network directory when a new site is added.
     *
     * @link https://developer.wordpress.org/reference/hooks/wpmu_new_blog/
     */
    public static function wpmu_new_blog ($blog_id) {
        $cpt = new Multisite_Directory_Entry();
        $cpt->add_new_site_post($blog_id);
    }

    /**
     * Main plugin activation method.
     *
     * @link https://developer.wordpress.org/reference/hooks/activate_plugin/
     *
     * @param bool $network_wide
     */
    public static function activate ($network_wide) {
        self::initialize();

        $plugin = new self();
        $plugin->initializeDirectory();

        flush_rewrite_rules();
    }

    /**
     * Main plugin deactivation method.
     *
     * @link https://developer.wordpress.org/reference/hooks/deactivate_plugin/
     *
     * @param bool $network_wide
     */
    public static function deactivate ($network_wide) {
        // TODO?
    }

    /**
     * Creates site directory posts for each site.
     *
     * @todo Handle "large" networks correctly.
     */
    private function initializeDirectory () {
        $sites = wp_get_sites();
        $cpt = new Multisite_Directory_Entry();
        foreach ($sites as $site) {
            if ($site['spam']) {
                continue;
            }
            $posts = $cpt->get_posts(array(
                'post_status' => 'any',
                'meta_key'    => $cpt::blog_id_meta_key,
                'meta_value'  => $site['blog_id']
            ));
            if (empty($posts)) {
                $cpt->add_new_site_post($site['blog_id']);
            }
        }
    }

    /**
     * Adds an "At A Glance" item to a site dashboard showing this site's categorization in the directory.
     *
     * @link https://developer.wordpress.org/reference/hooks/dashboard_glance_items/
     *
     * @param array $items
     */
    public static function dashboard_glance_items ($items) {
        $terms = get_site_terms(get_current_blog_id());
        // TODO: Better HTML output.
        if ($terms) {
            $items['network_directory_categories'] = __('Site Directory Categories', 'multisite-directory').': ';
            foreach ($terms as $term) {
                $items['network_directory_categories'] .= $term->name;
            }
        }
        return $items;
    }

}

WP_Multisite_Directory::register();
