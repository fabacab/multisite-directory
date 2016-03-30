<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://syllogic.in
 * @since             1.0.0
 * @package           Taxonomy_For_Network_Sites
 *
 * @wordpress-plugin
 * Plugin Name:       Taxonomy for Network Sites
 * Plugin URI:        http://wordpress.syllogic.in
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Aurovrata V., May May
 * Author URI:        http://syllogic.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       taxonomy-for-network-sites
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-taxonomy-for-network-sites-activator.php
 */
function activate_taxonomy_for_network_sites() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-taxonomy-for-network-sites-activator.php';
	Taxonomy_For_Network_Sites_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-taxonomy-for-network-sites-deactivator.php
 */
function deactivate_taxonomy_for_network_sites() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-taxonomy-for-network-sites-deactivator.php';
	Taxonomy_For_Network_Sites_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_taxonomy_for_network_sites' );
register_deactivation_hook( __FILE__, 'deactivate_taxonomy_for_network_sites' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-taxonomy-for-network-sites.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_taxonomy_for_network_sites() {

	$plugin = new Taxonomy_For_Network_Sites();
	$plugin->run();

}
run_taxonomy_for_network_sites();
