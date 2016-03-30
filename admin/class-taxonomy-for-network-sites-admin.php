<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/admin
 * @author     Aurovrata V., May May <vrata@syllogic.in>
 */
class Taxonomy_For_Network_Sites_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Taxonomy_For_Network_Sites_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Taxonomy_For_Network_Sites_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/taxonomy-for-network-sites-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Taxonomy_For_Network_Sites_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Taxonomy_For_Network_Sites_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/taxonomy-for-network-sites-admin.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * Register the Network dashboard Sites sub-menu.
	 *
	 * This function is called using the hook 'network_admin_menu'
	 *
	 * @since    1.0.0
	 */
	public function add_category_submenu_to_sites(){
		//add_submenu_page ( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
		//option 1
		add_submenu_page( 'sites.php', 'Site Categories', 'Categories', 'manage_sites', '../edit-tags.php?taxonomy='.T4NS_TAXONOMY.'&post_type='.T4NS_CUSTOM_POST, '' );
		//option 2 (manually copy the edit-tags.php file from wp-admin to wp-admin/network)
		//add_submenu_page( 'sites.php', 'Site Categories', 'Categories', 'manage_sites', 'edit-tags.php?taxonomy='.T4NS_TAXONOMY.'&post_type='.T4NS_CUSTOM_POST, '' );
		//option 3
		//add_submenu_page( 'sites.php', 'Site Categories', 'Categories', 'manage_sites', 'edit-site-categories', 'manage_sites_categories' );
	}
	
	/*
	 * Display site categories edit page in the dashboard
	 * 
	 * This is a callback function form the add_submenu_page function.
	 *
	 * @since 1.0.0
	 */
	public function manage_sites_categories(){
		require(dirname( __FILE__ )  . 'admin/edit_site_taxonomy.php');
	}
	
	/**
	 * Register the custom taxonomy used for organising custom site post.
	 *
	 * This function is called using the hook 'init'
	 *
	 * @since    1.0.0
	 */
	public function regsiter_custom_taxonomy(){
		$blog_id = get_current_blog_id();
		if(1!=$blog_id) return; //we only want to register in the home site
		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'taxonomy-for-network-sites' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'taxonomy-for-network-sites' ),
			'menu_name'                  => __( 'Categories', 'taxonomy-for-network-sites' ),
			'all_items'                  => __( 'All Categories', 'taxonomy-for-network-sites' ),
			'parent_item'                => __( 'Parent', 'taxonomy-for-network-sites' ),
			'parent_item_colon'          => __( 'Parent:', 'taxonomy-for-network-sites' ),
			'new_item_name'              => __( 'New Category Name', 'taxonomy-for-network-sites' ),
			'add_new_item'               => __( 'Add New Category', 'taxonomy-for-network-sites' ),
			'edit_item'                  => __( 'Edit Category', 'taxonomy-for-network-sites' ),
			'update_item'                => __( 'Update Category', 'taxonomy-for-network-sites' ),
			'view_item'                  => __( 'View Category', 'taxonomy-for-network-sites' ),
			'separate_items_with_commas' => __( 'Separate Categories with commas', 'taxonomy-for-network-sites' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'taxonomy-for-network-sites' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'taxonomy-for-network-sites' ),
			'popular_items'              => __( 'Popular Categories', 'taxonomy-for-network-sites' ),
			'search_items'               => __( 'Search Categories', 'taxonomy-for-network-sites' ),
			'not_found'                  => __( 'Not Found', 'taxonomy-for-network-sites' ),
			'no_terms'                   => __( 'No Categories', 'taxonomy-for-network-sites' ),
			'items_list'                 => __( 'categories list', 'taxonomy-for-network-sites' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'taxonomy-for-network-sites' ),
		);
		$capabilities = array(
			'manage_terms'               => 'manage_network',
			'edit_terms'                 => 'manage_network',
			'delete_terms'               => 'manage_network',
			'assign_terms'               => 'edit_posts',
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'capabilities'               => $capabilities,
			'update_count_callback'      => array($this, 'site_assigned_term_update'),
		);
		//register_taxonomy( $taxonomy, $object_type, $args );
		register_taxonomy( T4NS_TAXONOMY, T4NS_CUSTOM_POST, $args );
	}
	/*
	 *Call back function for when a site term update occurs
	 *
	 *This is a callback function
	 */
	public function site_assigned_term_update(){
		//TODO: do we need to do anything here?
	}
	
	/**
	 * Register the custom post type for organising sites
	 *
	 * This function is called using the hook 'init'
	 *
	 * @since    1.0.0
	 */
	public function regsiter_custom_post(){
		$labels = array(
			'name'                  => _x( 'Sites', 'Post Type General Name', 'taxonomy-for-network-sites' ),
			'singular_name'         => _x( 'Site', 'Post Type Singular Name', 'taxonomy-for-network-sites' ),
			'menu_name'             => __( 'Sites', 'taxonomy-for-network-sites' ),
			'name_admin_bar'        => __( 'Site', 'taxonomy-for-network-sites' ),
			'archives'              => __( 'Site Archives', 'taxonomy-for-network-sites' ),
			'parent_item_colon'     => __( 'Parent site:', 'taxonomy-for-network-sites' ),
			'all_items'             => __( 'All sites', 'taxonomy-for-network-sites' ),
			'add_new_item'          => __( 'Add New site', 'taxonomy-for-network-sites' ),
			'add_new'               => __( 'Add New', 'taxonomy-for-network-sites' ),
			'new_item'              => __( 'New Site', 'taxonomy-for-network-sites' ),
			'edit_item'             => __( 'Edit Site', 'taxonomy-for-network-sites' ),
			'update_item'           => __( 'Update Site', 'taxonomy-for-network-sites' ),
			'view_item'             => __( 'View Site', 'taxonomy-for-network-sites' ),
			'search_items'          => __( 'Search Site', 'taxonomy-for-network-sites' ),
			'not_found'             => __( 'Not found', 'taxonomy-for-network-sites' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'taxonomy-for-network-sites' ),
			'featured_image'        => __( 'Screenshot', 'taxonomy-for-network-sites' ),
			'set_featured_image'    => __( 'Set Screenshot', 'taxonomy-for-network-sites' ),
			'remove_featured_image' => __( 'Remove Screenshot', 'taxonomy-for-network-sites' ),
			'use_featured_image'    => __( 'Use as Screenshot', 'taxonomy-for-network-sites' ),
			'insert_into_item'      => __( 'Insert into Site', 'taxonomy-for-network-sites' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Site', 'taxonomy-for-network-sites' ),
			'items_list'            => __( 'Sites list', 'taxonomy-for-network-sites' ),
			'items_list_navigation' => __( 'Sites list navigation', 'taxonomy-for-network-sites' ),
			'filter_items_list'     => __( 'Filter sites list', 'taxonomy-for-network-sites' ),
		);
		$capabilities = array(
			'edit_post'             => 'manage_network',
			'read_post'             => 'manage_network',
			'delete_post'           => 'manage_network',
			'edit_posts'            => 'manage_network',
			'edit_others_posts'     => 'manage_network',
			'publish_posts'         => 'manage_network',
			'read_private_posts'    => 'manage_network',
		);
		$args = array(
			'label'                 => __( 'Site', 'taxonomy-for-network-sites' ),
			'description'           => __( 'Custom Post for Network site taxonomy', 'taxonomy-for-network-sites' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', ),
			'taxonomies'            => array( T4NS_TAXONOMY ),
			'hierarchical'          => true,
			'public'                => false,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => 'sites',
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capabilities'          => $capabilities,
		);
		register_post_type( T4NS_CUSTOM_POST, $args );
	}
}
