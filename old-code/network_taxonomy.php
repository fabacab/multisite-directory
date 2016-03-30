<?php 
/*
Plugin Name: Taxonomy for Network Sites
Plugin URI: http://wordpress.syllogic.in
Description: Plugin for classifying sies in a network install using taxonomies
Author: Aurovrata V.
Version: 0.1
Author URI: http://www.syllogic.in
*/

/*
 * Constants
 */
define('T4NS_TERMS_TABLE','network_taxonomy_term');
define('T4NS_RELATION_TABLE','network_term_relationship');
define('T4NS_TAXONOMY_CATEGORY','sites_category');

/*
 *  Files
 */
require('include/t4ns_functions.php');
/*
 * Actions
 */

add_action('plugins_loaded', 't4ns_plugin_init'); //translation DONE
add_action('network_admin_menu', 't4ns_admin_menu');//DONE
register_activation_hook( __FILE__, 't4ns_plugin_activate' );//DONE
register_deactivation_hook( __FILE__, 't4ns_plugin_deactivate' );//DONE

add_action('admin_footer-site-new.php','t4ns_wp_site_new'); //add new site form injection
add_action( 'wpmu_new_blog', 't4ns_insert_site_terms' ); //DONE

add_filter('wpmu_blogs_columns', 't4ns_register_sites_column'); //add extra column to sites table DONE
add_filter('manage_sites-network_sortable_columns', 't4ns_register_sortable_column');	//DONE
add_filter('manage_sites_custom_column', 't4ns_blog_term_field', 10, 2); //DONE

add_action('admin_footer-sites.php','t4ns_wp_site_list'); //inject quick-edit code into sites table

function t4ns_admin_menu() {
    add_submenu_page( 'sites.php', 'Sites Categories', 'Categories', 'manage_sites', 'sites-categories', 'display_sites_categories' );
}

function display_sites_categories() {
    require('include/sites_taxonomy.php');
}

function t4ns_plugin_init() {
 $plugin_dir = basename(dirname(__FILE__));
 error_log("Loading Translations from path: ".$plugin_dir.'/language/');
 load_plugin_textdomain( 'sitesTax_syllogic_in', false, $plugin_dir.'/language/' );
}

function t4ns_plugin_activate($networkwide) {
    global $wpdb;

    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
    if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_terms}'") != $table_terms) {
 
        if (!empty ($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        if (!empty ($wpdb->collate))
            $charset_collate .= " COLLATE {$wpdb->collate}";
                 
        $sql_terms = "CREATE TABLE IF NOT EXISTS {$table_terms} (
            term_id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(200) NOT NULL,
            description longtext NOT NULL DEFAULT '',
            taxonomy varchar(32) NOT NULL DEFAULT '',
            parent bigint(20) DEFAULT 0,
            count bigint(20) DEFAULT 0,
            UNIQUE KEY term_id (term_id)
        ) {$charset_collate};";
            
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_terms);
        
        $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
        $sql_relation = "CREATE TABLE IF NOT EXISTS {$table_relation} (
            blog_id bigint(20) unsigned NOT NULL DEFAULT '0',
            term_id bigint(20) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (blog_id,term_id),
            KEY term_id (term_id)
        ) {$charset_collate};";
        dbDelta($sql_relation);
    }
}
function t4ns_plugin_deactivate($networkwide) {
    global $wpdb;

    $table_terms = $wpdb->prefix . T4NS_TERMS_TABLE;
    $table_relation = $wpdb->prefix . T4NS_RELATION_TABLE;
    $sql_drop = "DROP TABLE IF EXISTS {$table_terms},{$table_relation};";
    $wpdb->query($sql_drop);
}
?>