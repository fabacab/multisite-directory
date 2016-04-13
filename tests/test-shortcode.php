<?php
/**
 * Tests for the Multisite Directory Shortcode.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * Shortcode tests class.
 *
 * The shortcode is the primary workhorse for front-end display.
 */
class Multisite_Directory_Shortcode_TestCase extends WP_UnitTestCase {

    /**
     * Sets up the network in a way appropriate for network-wide tests.
     */
    public static function setUpBeforeClass () {
        $wp_testcase = new WP_UnitTestCase();
        // Since the shortcode is only really intended for Multisite
        // installs, if this isn't a WP Multisite, we skip all tests.
        if (!is_multisite()) {
            $wp_testcase->markTestSkipped('Shortcode tests are for WP Multisite only.');
        }

        $wp_testcase->factory->blog->create_many(4); // 5 total
    }

    /**
     * Cleans up the testing database for future test runs.
     */
    public static function tearDownAfterClass () {
        $blogs = wp_get_sites();
        foreach ($blogs as $blog) {
            wpmu_delete_blog($blog['blog_id'], true); // drop database tables, too
        }
    }

    /**
     * Set up tests.
     *
     * The `setUp` method is run by PHPUnit before each test method
     * is run.
     *
     * This allows us to reset back to a known, initial state after
     * each test.
     */
    public function setUp () {
        // Reset invocation count to initial state before each test.
        // For the shortcode, we want to ensure that its class
        // will "forget" that it has been run at all between tests, since
        // it is designed to remember how many times it was invoked by
        // storing this data in a static class member variable. We want
        // the count of how many invocations in order to ensure multiple
        // invocations on the same HTML page produce different HTML `id`
        // attribute values but that's actually troublesome in unit tests.
        Multisite_Directory_Shortcode::$invocations = 0;
    }

    /**
     * Running the shortcode by itself should not raise any PHP warnings.
     *
     * @link https://wordpress.org/support/topic/problem-with-shortcode-on-page
     */
    public function test_invoking_without_attributes_does_not_trigger_warning () {
        do_shortcode('[site-directory]');
    }

    /**
     * Ensure that the shortcode can correctly count how many times it's been invoked.
     */
    public function test_can_remember_own_invocation_count () {
        for ($i = 0; $i < 5; $i++) {
            do_shortcode('[site-directory]');
        }
        $this->assertSame(5, Multisite_Directory_Shortcode::$invocations);
    }

    /**
     * Ensure default output is a map container.
     */
    public function test_default_display_is_map_container () {
        $this->expectOutputString('<div id="site-directory-1" class="site-directory-map" style=""></div>');
        do_shortcode('[site-directory]');
    }

    /**
     * Ensure that list output can be alphabetized.
     */
    public function test_query_args_can_alphabetize_list_output () {
        // TODO: Add a custom taxonomy factory helper.
        $term = wp_insert_term('Test Category', 'subsite_category');
        $post_ids = $this->factory->post->create_many(5, array(
            'post_type' => 'network_directory',
        ));
        foreach ($post_ids as $blog_id => $id) {
            $blog_id++; // blog_ids start at 1
            wp_set_post_terms($id, $term['term_id'], 'subsite_category');
            update_post_meta($id, Multisite_Directory_Entry::blog_id_meta_key, $blog_id);
        }

        $this->setOutputCallback(array($this, 'collectSiteIDs'));
        $this->expectOutputString('17,18,19,20'); // I'm not sure why these are the blog IDs created, but they're consistent.

        $query_args = json_encode(array(
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        do_shortcode("[site-directory display='list' query_args='$query_args']");
    }

    /**
     * Makes a CSV-formatted string of site (blog) IDs in the order they were printed.
     *
     * This is useful for testing ordering.
     */
    public function collectSiteIDs ($output) {
        preg_match_all('/>Site (\d+)</', $output, $matches);
        return join(',', array_pop($matches));
    }

}

/**
 * This small class tests the shortcode on WP single site installs.
 */
class Multisite_Directory_Shortcode_On_Singlesite_TestCase extends WP_UnitTestCase {

    /**
     * Set up tests for single site.
     */
    public function setUp () {
        if (is_multisite()) {
            $this->markTestSkipped('Single-site tests are skipped on WP Multisite builds.');
        }
        // Reset invocation count to initial state before each test.
        Multisite_Directory_Shortcode::$invocations = 0;
    }

    /**
     * Ensure shortode is silent on single site installs.
     */
    public function test_output_is_empty () {
        $this->expectOutputString('');
        do_shortcode('[site-directory]');
    }
}
