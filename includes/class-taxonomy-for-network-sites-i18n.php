<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 * @author     Aurovrata V., May May <vrata@syllogic.in>
 */
class Taxonomy_For_Network_Sites_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'taxonomy-for-network-sites',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
