<?php

/**
 * Fired during plugin activation
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 * @author     Aurovrata V., May May <vrata@syllogic.in>
 */
class Taxonomy_For_Network_Sites_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate($networkwide) {
    //TODO: do we need to activate on separate sites?  At this point most likely not
    if (function_exists('is_multisite') && is_multisite()) { 
      //let's register our taxonoy and post, as well as create wrapper post for each site
      switch_to_blog(1);
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-network-sites-wrapper.php';
      Network_Sites_Wrapper::initialise();
      restore_current_blog();
      return;
    }else{
      exit("Network Wide Posts works only on multisites");
    }
	}

}
