<?php
/**
 * The Multisite Directory plugin for WordPress.
 *
 * WordPress plugin header information:
 *
 * * Plugin Name: Multisite Directory
 * * Plugin URI: https://wordpress.org/plugins/multisite-directory/
 * * Description: Adds a Network-wide site directory to your WP Multisite network.
 * * Version: 0.1.2
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
        require_once 'includes/class-multisite-directory-widget.php';
        require_once 'includes/class-multisite-directory-shortcode.php';
        require_once 'includes/functions.php';
        require_once 'admin/class-multisite-directory-admin.php';

        add_action('init', array(__CLASS__, 'initialize'));
        add_action('widgets_init', array(__CLASS__, 'widgets_initialize'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'register_scripts'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'register_scripts'));

        add_action('wpmu_new_blog', array(__CLASS__, 'wpmu_new_blog'));
        add_action('delete_blog', array(__CLASS__, 'delete_blog'), 10, 2);
        add_action('network_admin_menu', array('WP_Multisite_Directory_Admin', 'network_admin_menu'));
        add_action('signup_blogform', array(__CLASS__, 'signup_blogform'));
        add_action('add_signup_meta', array(__CLASS__, 'add_signup_meta'));

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

        Multisite_Directory_Shortcode::register();
    }

    /**
     * Registers plugin widgets.
     *
     * @link https://developer.wordpress.org/reference/hooks/widgets_init/
     *
     * @uses register_widget()
     */
    public static function widgets_initialize () {
        register_widget('Multisite_Directory_Widget');
    }

    /**
     * Loads plugin-wide scripts and styles.
     *
     * @link https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
     */
    public static function register_scripts () {
        wp_register_style(
            'leaflet',
            plugins_url('vendor/leaflet/dist/leaflet.css', __FILE__)
        );
        wp_register_script(
            'leaflet',
            plugins_url('vendor/leaflet/dist/leaflet.js', __FILE__),
            array(),
            false,
            true
        );
    }

    /**
     * Adds a new directory entry to the network directory when a new site is added.
     *
     * @link https://developer.wordpress.org/reference/hooks/wpmu_new_blog/
     *
     * @param int $blog_id
     */
    public static function wpmu_new_blog ($blog_id) {
        $signup_cats = get_blog_option($blog_id, 'multisite-directory-signup-categories');
        $cpt = new Multisite_Directory_Entry();
        $post_id = $cpt->add_new_site_post($blog_id);
        if (!is_wp_error($post_id) && !empty($signup_cats)) {
            $result = wp_set_post_terms($post_id, $signup_cats, Multisite_Directory_Taxonomy::name);
            if (is_array($result)) {
                delete_blog_option($blog_id, 'multisite-directory-signup-categories');
            }
        }
    }

    /**
     * Removes the site directory entry for a blog as it's being deleted.
     *
     * @link https://developer.wordpress.org/reference/hooks/delete_blog/
     *
     * @param int $blog_id
     * @param bool $drop
     */
    public static function delete_blog ($blog_id, $drop) {
        switch_to_blog(1);
        $posts = get_posts(array(
            'post_type' => Multisite_Directory_Entry::name,
            'meta_key' => Multisite_Directory_Entry::blog_id_meta_key,
            'meta_value' => $blog_id
        ));
        if (!empty($posts)) {
            $post_id = $posts[0]->ID;
            wp_delete_post($post_id, $drop);
        }
        restore_current_blog();
    }

    /**
     * Main plugin activation method.
     *
     * @link https://developer.wordpress.org/reference/hooks/activate_plugin/
     *
     * @param bool $network_wide
     */
    public static function activate ($network_wide) {
        if (!is_multisite()) {
            deactivate_plugins(plugin_basename(__FILE__));
        }

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
        $sites = wp_get_sites(array(
            'spam'    => 0, // don't include sites marked as spam
            'deleted' => 0, // or sites that have been "deleted".
        ));
        $cpt = new Multisite_Directory_Entry();
        foreach ($sites as $site) {
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
     * Outputs site directory category fields during new site signup on front-end.
     *
     * @link https://developer.wordpress.org/reference/hooks/signup_blogform/
     */
    public static function signup_blogform () {
        require_once ABSPATH.'wp-admin/includes/template.php';
        $html = wp_terms_checklist(0, array(
            'taxonomy' => Multisite_Directory_Taxonomy::name,
            'echo'     => false,
        ));
        print '<div id="multisite-directory-signup-categories">';
        print '<label>'.__('Site Categories:', 'multisite-directory').'</label>';
        print str_replace("disabled='disabled'", '', $html);
        print '</div><!-- #multisite-directory-signup-categories -->';
    }

    /**
     * Adds the site categories to the default site creation meta variables.
     *
     * @link https://developer.wordpress.org/reference/hooks/add_signup_meta/
     *
     * @param array $meta
     */
    public static function add_signup_meta ($meta) {
        if (isset($_POST['tax_input']) && !empty($_POST['tax_input'][Multisite_Directory_Taxonomy::name])) {
            $meta['multisite-directory-signup-categories'] = $_POST['tax_input'][Multisite_Directory_Taxonomy::name];
        }
        return $meta;
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
