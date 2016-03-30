<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/includes
 * @author     Aurovrata V., May May <vrata@syllogic.in>
 */
class Taxonomy_For_Network_Sites {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Taxonomy_For_Network_Sites_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->define_constants();

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	
	/**
	 * Load the required constants for this plugin.
	 *
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_constants(){
		$this->plugin_name = 'taxonomy-for-network-sites';
		$this->version = '1.0.0';
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Taxonomy_For_Network_Sites_Loader. Orchestrates the hooks of the plugin.
	 * - Taxonomy_For_Network_Sites_i18n. Defines internationalization functionality.
	 * - Taxonomy_For_Network_Sites_Admin. Defines all hooks for the admin area.
	 * - Taxonomy_For_Network_Sites_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-taxonomy-for-network-sites-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-taxonomy-for-network-sites-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-taxonomy-for-network-sites-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-taxonomy-for-network-sites-public.php';

		$this->loader = new Taxonomy_For_Network_Sites_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Taxonomy_For_Network_Sites_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Taxonomy_For_Network_Sites_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Taxonomy_For_Network_Sites_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts',                 $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts',                 $plugin_admin, 'enqueue_scripts' );
		
		//submenu for Network dashboard menu Sites
		$this->loader->add_action( 'network_admin_menu',                    $plugin_admin, 'add_category_submenu_to_sites' );
		//resgister our custom taxnonmy/post, this is not needed at this point since we have done this at activation time
		//$this->loader->add_action( 'init', $plugin_admin, 'regsiter_custom_post_and_taxononmy');
		
		//add cloumns to network dashboard sites table, and make it sortable
		$this->loader->add_action( 'wpmu_blogs_columns',                    $plugin_admin, 'sites_table_category_column');
		$this->loader->add_action( 'manage_sites-network_sortable_columns', $plugin_admin, 'sites_table_category_sortable_column');
		$this->loader->add_action( 'manage_sites_custom_column',            $plugin_admin, 'populate_sites_table_category_column',10, 2);
		
		//new blog creation
		$this->loader->add_action( 'wpmu_new_blog',                         $plugin_admin, 'add_new_blog');
		
		//manage terms in sites tables
		$this->loader->add_action( 'admin_footer-sites.php',                $plugin_admin, 'add_quick_edit_sites');
		//ajax save site terms
		$this->loader->add_action( 'wp_ajax_t4ns_update_site_terms',        $plugin_admin, 'ajax_save_site_terms');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Taxonomy_For_Network_Sites_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Taxonomy_For_Network_Sites_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
