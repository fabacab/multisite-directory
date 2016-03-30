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
