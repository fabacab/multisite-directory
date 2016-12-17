<?php
/**
 * Tests for the Multisite Directory Shortcode.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 Meitar "maymay" Moscovitz
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
     * Ensures that sites marked "spam," "deleted," or "archived" are not shown in the directory.
     */
    public function test_inactive_sites_are_hidden () {
        $blog_ids = array();
        $blog_ids[] = $this->factory->blog->create(array('meta' => array(
            'spam' => true
        )));
        $blog_ids[] = $this->factory->blog->create(array('meta' => array(
            'archived' => true
        )));
        $blog_ids[] = $this->factory->blog->create(array('meta' => array(
            'deleted' => true
        )));
        $blog_ids[] = $this->factory->blog->create(array('title' => 'Active Site'));
        $term = wp_insert_term('Test Category', 'subsite_category');
        $posts = get_posts(array(
            'post_type' => 'network_directory',
            'meta_key' => Multisite_Directory_Entry::blog_id_meta_key,
            'meta_value' => $blog_ids
        ));
        foreach ($posts as $post) {
            wp_set_post_terms($post->ID, $term['term_id'], 'subsite_category');
        }
        $this->setOutputCallback(array($this, 'collectBlogTitles'));
        $this->expectOutputString('Active Site');
        do_shortcode('[site-directory display="list"]');
    }

    /**
     * Ensure that list output can be alphabetized.
     */
    public function test_query_args_can_alphabetize_list_output () {
        // Create a set of new sites with custom names.
        $blog_ids = array();
        $blog_ids[] = $this->factory->blog->create(array('title' => 'A Blog'));
        $blog_ids[] = $this->factory->blog->create(array('title' => 'B Blog'));
        $blog_ids[] = $this->factory->blog->create(array('title' => 'C Blog'));

        // Assign the new site entries a test category to group them.
        // TODO: Add a custom taxonomy factory helper.
        $term = wp_insert_term('Test Category', 'subsite_category');
        $posts = get_posts(array(
            'post_type' => 'network_directory',
            'meta_key' => Multisite_Directory_Entry::blog_id_meta_key,
            'meta_value' => $blog_ids
        ));
        foreach ($posts as $post) {
            wp_set_post_terms($post->ID, $term['term_id'], 'subsite_category');
        }

        $this->setOutputCallback(array($this, 'collectBlogTitles'));
        $this->expectOutputString('A Blog,B Blog,C Blog');

        $query_args = json_encode(array(
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        do_shortcode("[site-directory display='list' query_args='$query_args']");
    }

    /**
     * Makes a CSV-formatted string of site (blog) titles in the order they were printed.
     *
     * This is useful for testing ordering.
     */
    public function collectBlogTitles ($output) {
        preg_match_all('/a href="(?:.*?)">(.*?)</', $output, $matches);
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
