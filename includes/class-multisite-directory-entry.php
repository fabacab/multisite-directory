<?php
/**
 * A Subsite Post
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * Class defining a "Subsite" custom post type.
 */
class Multisite_Directory_Entry {

    /**
     * Name of the custom post type.
     *
     * @var string
     */
    const name = 'network_directory';

    /**
     * Post meta key for the blog ID.
     *
     * @var string
     */
    const blog_id_meta_key = 'site_directory_blog_id';

    /**
     * Custom post type UI labels.
     *
     * @var array
     */
    private $labels;

    /**
     * Capabilities needed to act on the custom post type.
     *
     * @var array
     */
    private $capabilities = array(
        'edit_post'          => 'manage_sites',
        'read_post'          => 'manage_sites',
        'delete_post'        => 'manage_sites',
        'edit_posts'         => 'manage_sites',
        'edit_others_posts'  => 'manage_sites',
        'publish_posts'      => 'manage_sites',
        'read_private_posts' => 'manage_sites',
    );

    /**
     * Constructor.
     */
    public function __construct () {
        $this->labels = array(
            'name'                  => _x( 'Sites', 'Post Type General Name', 'multisite-directory' ),
            'singular_name'         => _x( 'Site', 'Post Type Singular Name', 'multisite-directory' ),
            'menu_name'             => __( 'Directory', 'multisite-directory' ),
            'name_admin_bar'        => __( 'Site', 'multisite-directory' ),
            'archives'              => __( 'Sites', 'multisite-directory' ),
            'all_items'             => __( 'All Sites', 'multisite-directory' ),
            'add_new_item'          => __( 'Add New Site', 'multisite-directory' ),
            'add_new'               => __( 'Add New', 'multisite-directory' ),
            'new_item'              => __( 'New Site', 'multisite-directory' ),
            'edit_item'             => __( 'Edit Site', 'multisite-directory' ),
            'update_item'           => __( 'Update Site', 'multisite-directory' ),
            'view_item'             => __( 'View Site', 'multisite-directory' ),
            'search_items'          => __( 'Search Sites', 'multisite-directory' ),
        );
    }

    /**
     * Registers the custom post type with via WordPress API.
     */
    public function register () {
        register_post_type(self::name, array(
            'labels'       => $this->labels,
            'public'       => true,
            'show_in_menu' => (get_directory_blog_id() === get_current_blog_id()) ? true : false,
            'hierarchical' => true,
            'has_archive'  => true,
            'capabilities' => $this->capabilities,
            'supports'     => array(
                'title',
                'editor',
                'revisions',
                'excerpt',
                'thumbnail',
                'page-attributes',
                'custom-fields',
            ),
            'menu_icon'    => 'dashicons-networking',
            'taxonomies'   => array(Multisite_Directory_Taxonomy::name),
            'rewrite'      => array(
                'slug' => str_replace('_', '-', self::name),
                'with_front' => false,
            ),
        ));

        add_action('load-post.php', array(__CLASS__, 'addHelpTabs'));
    }

    public static function addHelpTabs () {
        $screen = get_current_screen();
        if (self::name !== $screen->post_type) {
            return;
        }

        $content = sprintf(
            esc_html__(
                '
                %1$s
                On this page you edit the Site Directory entry associated with one of your Network blogs.
                Each Site entry is associated with a site in your WP Multisite network using a %3$s custom field.
                %2$s
                ',
                'multisite-directory'
            ),
            '<p>',                                    // %1$s
            '</p>',                                   // %2$s
            '<code>'.self::blog_id_meta_key.'</code>' // %3$s
        );

        $screen->add_help_tab(array(
            'title' => esc_html__('Editing a Directory entry', 'multisite-directory'),
            'id' => esc_attr('network_directory-help_tab'),
            'content' => $content,
        ));
    }

    /**
     * Gets posts of this post type from the Single Point of Truth.
     *
     * @link https://developer.wordpress.org/reference/functions/get_posts/
     *
     * @return array
     */
    public function get_posts ($args = null) {
        if (!is_null($args)) {
            $args = wp_parse_args($args, array(
                'post_type' => self::name
            ));
        }
        // TODO: We should consider making this a variable so the end
        //       user can determine which blog to save the site-wide
        //       directory metadata to.
        switch_to_blog(get_directory_blog_id());
        $posts = get_posts($args);
        restore_current_blog();
        return array_filter($posts, array($this, 'is_active'));
    }

    /**
     * Tests whether or not the given site is "active."
     *
     * A site is "active" if none of its flags for `spam`, `archived`,
     * and `deleted` are truthy.
     *
     * @param int|WP_Post $entry The ID of the blog if an integer, or the Network Directory post if a WP_Post
     *
     * @return bool
     */
    public function is_active ($entry) {
        $blog_id = false;

        if (is_int($entry)) {
            $blog_id = $entry;
        }

        if ($entry instanceof WP_Post) {
            $blog_id = $entry->{self::blog_id_meta_key};
        }

        if (get_blog_status($blog_id, 'archived')) {
            return false;
        }

        if (get_blog_status($blog_id, 'spam')) {
            return false;
        }

        if (get_blog_status($blog_id, 'deleted')) {
            return false;
        }

        return true;
    }

    /**
     * Inserts a new post for the given subsite.
     *
     * @param int $blog_id
     *
     * @uses get_blog_details()
     * @uses wp_insert_post()
     *
     * @return int|WP_Error
     */
    public function add_new_site_post ($blog_id) {
        $blog = get_blog_details($blog_id);
        $postarr = array(
            'post_type'   => self::name,
            'post_status' => 'publish',
            'post_title'  => $blog->blogname,
            'meta_input'  => array(
                self::blog_id_meta_key => $blog_id
            )
        );

        switch_to_blog(get_directory_blog_id());
        $result = wp_insert_post($postarr);
        restore_current_blog();
        return $result;
    }

}
