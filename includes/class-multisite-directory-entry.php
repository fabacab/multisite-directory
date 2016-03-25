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
            'name'                  => _x('Site directory entries', 'Post Type General Name', 'multisite-directory'),
            'singular_name'         => _x('Site directory entry', 'Post Type Singular Name', 'multisite-directory'),
        );
    }

    /**
     * Registers the custom post type with via WordPress API.
     */
    public function register () {
        register_post_type(self::name, array(
            'labels'       => $this->labels,
            'public'       => true,
            'show_in_menu' => (1 === get_current_blog_id()) ? true : false,
            'hierarchical' => true,
            'has_archive'  => true,
            'capabilities' => $this->capabilities,
            'supports'     => array(
                'title',
                'editor',
                'revisions',
                'excerpt',
                'thumbnail',
                'page-attributes'
            ),
            'taxonomies'   => array(Multisite_Directory_Taxonomy::name),
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
        switch_to_blog(1); // 1 is (always?) the main blog
        $posts = get_posts($args);
        restore_current_blog();
        return $posts;
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

        switch_to_blog(1);
        $result = wp_insert_post($postarr);
        restore_current_blog();
        return $result;
    }

}
