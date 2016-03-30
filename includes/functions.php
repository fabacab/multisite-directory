<?php
/**
 *
 */

/**
 * Gets the categories in the network directory of a given blog.
 *
 * @param int $blog_id
 *
 * @uses get_the_terms()
 *
 * @return array|false|WP_Error
 */
if (!function_exists('get_site_terms')) :
    function get_site_terms ($blog_id) {
        $cpt = new Multisite_Directory_Subsite_Post();
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
