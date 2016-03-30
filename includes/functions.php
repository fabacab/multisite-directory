<?php
/**
 * Convenience functions and templating wrappers.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

if (!function_exists('get_site_directory_terms')) :
    /**
     * Gets all categories in the site directory.
     *
     * @uses get_terms()
     *
     * @return array|false|WP_Error
     */
    function get_site_directory_terms () {
        switch_to_blog(1);
        $terms = get_terms(Multisite_Directory_Taxonomy::name, array(
            'hide_empty' => false,
        ));
        restore_current_blog();
        return $terms;
    }
endif;

if (!function_exists('get_site_terms')) :
    /**
     * Gets the categories in the network directory of a given blog.
     *
     * @param int $blog_id
     *
     * @uses get_the_terms()
     *
     * @return array|false|WP_Error
     */
    function get_site_terms ($blog_id) {
        $cpt = new Multisite_Directory_Entry();
        switch_to_blog(1);
        $posts = $cpt->get_posts(array(
            'meta_key'  => $cpt::blog_id_meta_key,
            'meta_value' => $blog_id,
            'post_status' => 'any',
        ));
        $terms = get_the_terms(array_pop($posts), Multisite_Directory_Taxonomy::name);
        restore_current_blog();
        return $terms;
    }
endif;
