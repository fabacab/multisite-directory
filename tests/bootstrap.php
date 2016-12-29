<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/multisite-directory.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

/**
 * Unit test helper methods.
 */
class WP_Multisite_Directory_UnitTest_Helper {

    /**
     * Shared version-handling code for various unit testing classes.
     *
     * @uses get_sites
     * @uses wp_get_sites
     * @uses wpmu_delete_blog
     */
    public static function tearDownAfterClass () {
        if (function_exists('get_sites')) {
            // As of WordPress 4.6, this function exists
            $blogs = get_sites(); // returns WP_Site objects
        } else {
            $blogs = wp_get_sites(); // returns arrays
        }
        foreach ($blogs as $blog) {
            if (is_array($blog)) {
                wpmu_delete_blog($blog['blog_id'], true); // drop database tables, too
            } else {
                wpmu_delete_blog($blog->blog_id, true);   // drop database tables, too
            }
        }
    }

}
