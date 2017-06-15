<?php
/**
 * Multisite Directory uninstaller.
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

// Don't execute any uninstall code unless WordPress core requests it.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); }

require_once 'multisite-directory.php';
// re-init just for now to re-register post types, etc.
// this is useful for using said code to clean up itself.
WP_Multisite_Directory::initialize();

$networks = array( $GLOBALS['current_site'] );
if ( function_exists( 'get_networks' ) ) {
    $networks = get_networks();
}

foreach ( $networks as $network ) {
    if ( function_exists( 'get_main_site_for_network' ) ) {
        switch_to_blog( get_main_site_for_network( $network->id ) );
    } else {
        switch_to_blog( 1 );
    }

    // Delete terms.
    $terms = get_terms( Multisite_Directory_Taxonomy::name, array(
        'fields'     => 'ids',
        'hide_empty' => false,
    ) );
    foreach ( $terms as $term_id ) {
        wp_delete_term( $term_id, Multisite_Directory_Taxonomy::name );
    }

    // Delete site directory entries.
    $pages = get_pages( array(
        'post_type'   => Multisite_Directory_Entry::name,
        'post_status' => implode( ',', array_keys( get_page_statuses() ) )
    ) );
    foreach ( $pages as $page ) {
        wp_delete_post( $page->ID, true );
    }

    // Delete site options.
    $options = array(
        'multisite-directory-auto-update-entry-title',
    );
    foreach ( $options as $option ) {
        delete_site_option( $option );
    }

    restore_current_blog();
}
