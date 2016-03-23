<?php

/**
 * The file that defines the warpper class for abstracting network sites as posts
 *
 * A class definition that includes attributes and functions used primarily in the admin area.
 * Some functionality will be exposed on the public front-end through a class with static methods. 
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
 * Registers the custom post, taxnonomy, and exposes the functionality to met-document a site
 *
 * @package    Taxonomy_For_Network_Sites
 * @subpackage Taxonomy_For_Network_Sites/admin
 * @author     Aurovrata V. <vrata@syllogic.in>, May May <bitetheappleback@gmail.com>
 */
class Network_Sites_Wrapper {
  
  /**
	 * Constant for our custom taxonomy.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string T4NS_TAXONOMY sets taxonomy name
	 */
	const T4NS_TAXONOMY = 't4ns_category';
  
  /**
	 * Constant for our custom post.
	 *
	 * @since 1.0.0
	 * @access static
	 * @var string T4NS_CUSTOM_POST sets post type
	 */
	const T4NS_CUSTOM_POST = 't4ns_post';

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
    //$this->initialise();
	}
  /*
   * Initialise sites wrapper with custom post and taxonomy
   *
   * This function is hooked to 'init' through our admin interface,
   * and is only called on blog_id=1 for now.
   *
   */
  public static function initialise(){
    self::register_taxonomy_and_post();
    //now that we have intialised our custom post/taxonomy, let's create and populate the existing sites
    global $wpdb;
    $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    foreach($blogids as $blog_id){
      $postid = $wpdb->get_col("SELECT ID FROM ".$wpdb->prefix."posts
                               WHERE post_type LIKE '".self::T4NS_CUSTOM_POST."'
                               AND post_name LIKE 'site-".$blog_id."'");
      if(!empty($postid)) continue;
      $post_meta = array();
      $post_meta['post_status'] = 'publish';
      $post_meta['post_type'] = self::T4NS_CUSTOM_POST;
      $post_meta['post_name'] = 'site-'.$blog_id; //this becomes unique and easy to search/retrieve
      //get_blog_details( $fields, $getall_details )
      $blog_details = get_blog_details($blog_id, true);
      $post_meta['post_title'] = $blog_details->blogname;
      //wp_insert_post ( array $postarr, bool $wp_error = false )
      wp_insert_post ($post_meta);
    }
  }
  /*
   * Register custom post and taxonomy, this is called first thing on activation
   *
   * This function is hooked to 'init' through our admin interface,
   * and is only called on blog_id=1 for now.
   *
   */
  public static function register_taxonomy_and_post(){
    //custom taxonomy
    if(!taxonomy_exists(self::T4NS_TAXONOMY)){
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
        'update_count_callback'      => '',
      );
      //register_taxonomy( $taxonomy, $object_type, $args );
      register_taxonomy( self::T4NS_TAXONOMY, self::T4NS_CUSTOM_POST, $args );
    }
    //custom post
    if(!post_type_exists( self::T4NS_CUSTOM_POST )){
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
        'taxonomies'            => array( self::T4NS_TAXONOMY ),
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
      register_post_type( self::T4NS_CUSTOM_POST, $args );
      //if we make this post type rewrite-able and use it on the front-end, then we'll need to call
      //flush_rewrite_rules() via the 'switch_theme' hook as well as when the plugin activate to ensure the permalinks
      //work out of the box.
    }
  }
  /*
   * function to retrieve site terms
   *
   * @since    1.0.0
   * @param    int       $blog_id      blod id
   * @return   array     an array of terms with term_id=>term_title key/value pairs
   */
  public function get_site_terms($blog_id){
    //TODO, retrieve given $blog_id's t4ns_post type, and return array of t4ns_category terms associated with the post
  }
  
  /*
   * function to update site terms
   *
   * @since    1.0.0
   * @param    int       $blog_id      blog id
   * @param    array     $terms        an array of term IDs which represents all the terms currently assigned to this blog.
   */
  public function update_site_terms($blog_id, $terms){
    //TODO - assign/update terms to site post
  }
  /*
   * function to update site terms
   *
   * @since    1.0.0
   * @param    int       $blog_id      new blog id
   */
  public function add_new_site($blog_id){
    //TODO - crate a new post for the new blog
  }
  /*
   *function prints out a list of checkbox for each term
   *
   * This function is used for the sites tables in the network dashboard.
   * It can be used for widgets and other such form input needs.  It echo a list of '<li>' with nested children list
   *
   * @since    1.0.0
   * @param    int     $parent      term id whose children you want to list, top level starts at 0
   * @param    int     $level       the indent level for each recursive parent/child nesting
   * 
   */
  
  public static function terms_check_list($parent,$level=0){
    $terms = array(); //get terms
    
    if(!$terms) return;
    
    if($level>0) echo '<ul class="children" style="margin-left:18px;">';
    foreach($terms as $term){
        ?>
        <li class="sterm-<?php echo $term->term_id;?>">
            <label class="selectit"><input value="<?php echo $term->term_id;?>" name="st_site_terms[]" class="in-sterm-<?php echo $term->term_id;?>" type="checkbox"><?php echo $term->name;?></label>
        <?php self::terms_check_list($term->term_id,$level+1);?>
        </li>
        <?php
    }
    if($level>0) echo '</ul>';
  }
}
  