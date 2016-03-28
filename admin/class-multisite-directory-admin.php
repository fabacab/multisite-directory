<?php
/**
 * Administration handlers for the (Multi)Site Directory.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * Handles WordPress admin screens, etc.
 */
class WP_Multisite_Directory_Admin {

    /**
     * Enqueues JavaScript needed on admin pages.
     *
     * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     *
     * @param string $hook_suffix
     */
    public static function enqueue_scripts ($hook_suffix) {
        $screen = get_current_screen();
        if ('edit-'.Multisite_Directory_Taxonomy::name === $screen->id) {
            wp_enqueue_style(
                'leaflet',
                plugins_url('vendor/leaflet/dist/leaflet.css', dirname(__FILE__))
            );
            wp_enqueue_script(
                'leaflet',
                plugins_url('vendor/leaflet/dist/leaflet.js', dirname(__FILE__)),
                array(),
                false,
                true
            );
            wp_enqueue_script(
                basename(__FILE__),
                plugins_url('js/multisite-directory-admin.js', __FILE__),
                array('leaflet'),
                false,
                true
            );
        }
    }

    /**
     * Adds the network directory taxonomy page to the Network Sites menu.
     *
     * @link https://developer.wordpress.org/reference/hooks/network_admin_menu/
     */
    public static function network_admin_menu () {
        add_submenu_page(
            'sites.php',
            __('Site Directory Categories', 'multisite-directory'),
            __('Categories', 'multisite-directory'),
            'manage_sites',
            '../edit-tags.php?taxonomy='.Multisite_Directory_Taxonomy::name.'&post_type='.Multisite_Directory_Entry::name
            // no callback needed
        );
    }

}
