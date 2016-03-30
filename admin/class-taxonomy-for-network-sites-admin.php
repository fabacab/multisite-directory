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

	private $sites_wrapper;
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
		$this->load_dependencies();
		$this->sites_wrapper = new Network_Sites_Wrapper( $plugin_name, $version );

	}
	
	/**
	 * Load the required dependencies for admin functionality.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for Functionality for wrapping the sites taxonomy functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-network-sites-wrapper.php';
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
		add_submenu_page( 'sites.php', 'Site Categories', 'Categories', 'manage_sites', '../edit-tags.php?taxonomy='.Network_Sites_Wrapper::T4NS_TAXONOMY.'&post_type='.Network_Sites_Wrapper::T4NS_CUSTOM_POST, '' );
		//option 2 (manually copy the edit-tags.php file from wp-admin to wp-admin/network)
		//add_submenu_page( 'sites.php', 'Site Categories', 'Categories', 'manage_sites', 'edit-tags.php?taxonomy='.Network_Sites_Wrapper::T4NS_TAXONOMY.'&post_type='.Network_Sites_Wrapper::T4NS_CUSTOM_POST, '' );
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
	 * Register the custom taxonomy/post used for organising custom site post.
	 *
	 * This function is called using the hook 'init'
	 *
	 * @since    1.0.0
	 */
	public function regsiter_custom_post_and_taxononmy(){
		$blog_id = get_current_blog_id();
		if(1!=$blog_id) return; //we only want to register in the home site
		$this->sites_wrapper->register_taxonomy_and_post();
	}
	/*
	 *Call back function for when a site term update occurs
	 *
	 *This is a callback function
	 */
	public function site_assigned_term_update(){
		//TODO: do we need to do anything here?
	}
	
	
	/*
	 * Add a column to show the categories in teh Network dashboard sites table
	 *
	 * this function is hooked to WP filter 'wpmu_blogs_columns'
	 *
	 * @since    1.0.0
	 * @param    array    $columns       list of Columns parsed by WP 
	 */
	public function sites_table_category_column($columns){
		$columns['t4ns_terms'] = __('Categories','sitesTax_syllogic_in');
		return $columns;
	}
	
	/*
	 * Make newly added column sortable to show the categories in the Network dashboard sites table sor
	 *
	 * this function is hooked to WP filter 'manage_sites-network_sortable_columns'
	 * it combines the filter as well as the role allowed to execute this function
	 *
	 * @since    1.0.0
	 * @param    array    $columns       list of Columns parsed by WP 
	 */
	public function sites_table_category_sortable_column($columns){
		$columns['t4ns_terms'] = 't4ns_terms';
		return $columns;
	}
	
	/*
	 * This function get called by WP dashboard to populate the custom taxonomy column
	 *
	 * this function is hooked to WP filter 'manage_sites_custom_column'
	 * it combines the filter as well as the role allowed to execute this function
	 *
	 * @since    1.0.0
	 * @param    string    $column       name of the column
	 * @param    int       $blog_id      site id for a given row
	 */
	function populate_sites_table_category_column($column, $blogid){
		if ($column == 't4ns_terms'){
        $terms= $this->sites_wrapper->get_site_terms($blogid);
        $field='';
        foreach($terms as $term) $field .= '<a href="#?term_id='.$term->term_id.'">'.$term->name.'</a>, ';
        echo $field;
		}
		return $column;
	}
	
	/*
	 *  Function called when a new blog is created.
	 *
	 *This function is hooked to 'wpmu_new_blog'
	 *
	 * @since 		1.0.0
	 * @param 		int 		$blog_id 		blog id
	 * @param 		int 		$user_id 		user id who created the blog
	 * @param 		string 		$domain 	domain name
	 * @param 		string 		$path 		path
	 * @param 		int 		$site_id 		site id
	 * @param 		array 		$meta 		array of additional site parameters
	 */
	public function add_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
		$this->sites_wrapper->add_new_site($blog_id);
	}
	
/*
 * Function called to inject code in sites.php dashboard sites management page.
 *
 * This code creates a quick-edit menu in the sites row which enables network admin users to manage the sites
 * taxonomy terms
 *
 */
function add_quick_edit_sites(){
    $taxonomy = Network_Sites_Wrapper::T4NS_TAXONOMY;
    ob_start();
    ?>
    <tr id="edit-site" class="alternative-row-temp inline-edit-row quick-edit-row inline-editor hide-row">
        <td colspan="5" class="all-columns">
            <ul>
            <?php Network_Sites_Wrapper::terms_check_list(0,0);?>
            </ul>
            <p>
                <a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft">Cancel</a>
                <input id="_inline_edit" name="_inline_edit" value="2d057e955a" type="hidden">
                <a accesskey="s" href="#inline-edit" class="button-primary save alignright">Update</a>
                <span class="spinner"></span>
                <input name="post_view" value="list" type="hidden">
                <input name="screen" value="edit-post" type="hidden">
                <input name="blog_id" value="t4ns-blog-id" type="hidden">
                <span class="error" style="display:none"></span>
                <br class="clear">
            </p>
        </td>
    </tr>
    <?php
    $template = ob_get_contents();
		// An FTP client might have changed the \n to \r\n.
		$template = str_replace( array ("\n", "\r", "'" ), array( '', '', "\\'" ), $template );
		ob_end_clean();
    $ajax_nonce = wp_create_nonce( 't4ns_update_site_terms_nonce' );
		?>
		<script>
			( function( $ ) {
				$(document).ready( function(){

					var template    = '<?php echo $template; ?>',
          colCount = 0;
          $('tbody#the-list tr:nth-child(1) td').each(function () {
            if ($(this).attr('colspan')) {
              colCount += +$(this).attr('colspan');
            } else {
              colCount++;
            }
          });
          $('tbody#the-list tr:nth-child(1) th').each(function () {
            if ($(this).attr('colspan')) {
              colCount += +$(this).attr('colspan');
            } else {
              colCount++;
            }
          });
          template = template.replace(/colspan="5/i,'colspan="'+colCount);
          $( 'tbody#the-list tr' ).each(function(index,value){
            var link = $(this).find('a.edit').attr('href');
            var quickEdit='<span class="quick-edit"><a href="#" class="inline">Quick Edit</a> | </span>';
            if (link.length > 0) {
                var blogID = link.match(/id=[0-9]+/).toString().replace(/id=/,"");
                $(value).attr('id','site-'+blogID);
                var classVal = $(value).attr('class');
                /*set id of new row*/
                var newRow = template.replace(/id="edit-site/i,'id="edit-site-'+blogID);
                /*set class new row*/
                newRow = newRow.replace(/class="alternative-row-temp/i,'class="'+classVal);
                /*set blog id in hidden input field*/
                newRow = newRow.replace(/value="t4ns-blog-id/i,'value="'+blogID);
                $(this).find('div.row-actions > span.edit').after(quickEdit);
            } else $(value).attr('id','idx-'+index);

            $(value).after( newRow );
          });
					$('span.quick-edit a.inline').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id');
            editRow.hide();
            $(this).closest('tbody').find('#edit-'+pid).show();
            
          });
          $('a.button-secondary.cancel').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id').replace(/edit-/,"");
            editRow.hide();
            $(this).closest('tbody').find('#'+pid).show();
            
          });
          $('a.button-primary.save').on('click', function(){
            var editRow = $(this).closest('tr');
            var pid = editRow.attr('id').replace(/edit-/,"");
            var siteRow = $(this).closest('tbody').find('#'+pid);
            /*TODO pick up checked values and arrange them as comma separated list*/
            /*we need to update the back end through ajax*/
            var data = {
                action: 't4ns_update_site_terms',
                security: '<?php echo $ajax_nonce; ?>',
                st_site_terms: editRow.find(' ul li input[type=checkbox]:checked').serializeArray(),
                blog_id: editRow.find('p input[name=blog_id]').val()
              };
              /* since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php*/
              $.post( ajaxurl, data, function( response)  {
                if(1==response){
                    editRow.hide();
                    siteRow.show();
                    /*TODO update siteRow t4ns_coumn with checked terms*/
                }else{
                    var errMsg = editRow.find('p span.error');
                    errMsg.text('Unable to update, refresh page and try again.');
                    errMsg.show();
                }
              });
          });
          <?php
          global $wpdb;
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
          foreach($blogids as $blog_id){
            $terms = $this->sites_wrapper->get_site_terms($blog_id);
            foreach($terms as $term_id=>$term_title){
                echo "$('tr#edit-site-".$blog_id." input.in-sterm-".$term_id."').prop( 'checked', true );";
            }
          }
        ?>
				} );
			} )( jQuery );
		</script>
    <style> .hide-row{display:none;}</style>
		<?php
		
	}
	/**
	 * Hook referenced function to capture Ajax calls from the sites management page
	 *
	 * @since 		1.0.0
	 * @param 		array 		$menu_ord 		Empty array, only used when changing section menu order, Dashboard, Posts, Media and so on
	 * @return 		array 									In this case it is empty as we are changing the sub menu using the global $submenu.
	 */
	public function ajax_save_site_terms() {
    check_ajax_referer( 't4ns_update_site_terms_nonce', 'security' );
    
    if(isset($_POST['blog_id']) && isset($_POST['st_site_terms']) && is_array($_POST['st_site_terms'])){
        $blog_id = intval( $_POST['blog_id'] );
        $terms = $_POST['st_site_terms'];
        
        $this->sites_wrapper->update_site_terms($blog_id, $terms);
				
        echo true; //return successful completion of update
    }else echo false; //missing data, cannot update the DB
		die(); // this is required to return a proper result
	}
}
