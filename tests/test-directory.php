<?php
/**
 * Tests for the Multisite Directory itself.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 Meitar "maymay" Moscovitz
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * Directory test cases.
 *
 * These tests ensure management of the directory itself is working.
 */
class Multisite_Directory_TestCase extends WP_UnitTestCase {

    /**
     * Sets up tests.
     *
     * Tests whether or not this is a multisite install, skips if not.
     */
    public static function setUpBeforeClass () {
        $wp_testcase = new WP_UnitTestCase();
        // Since the shortcode is only really intended for Multisite
        // installs, if this isn't a WP Multisite, we skip all tests.
        if (!is_multisite()) {
            $wp_testcase->markTestSkipped('Multisite Directory tests are for WP Multisite only.');
        }
    }

    /**
     * Cleans up the testing database for future test runs.
     */
    public static function tearDownAfterClass () {
        WP_Multisite_Directory_UnitTest_Helper::tearDownAfterClass();
    }

    /**
     * Tests addition to directory when blog is added.
     */
    public function test_directory_entry_is_created_when_blog_is_created () {
        $blog = $this->factory->blog->create_and_get();
        $posts = get_posts(array(
            'post_type' => Multisite_Directory_Entry::name,
            'meta_key' => Multisite_Directory_Entry::blog_id_meta_key,
            'meta_value' => $blog->blog_id,
        ));
        $post = array_pop($posts);
        $this->assertSame($blog->blog_id, $post->{Multisite_Directory_Entry::blog_id_meta_key});
    }

    /**
     * Tests removal from directory on deletion of site.
     */
    public function test_directory_entry_is_deleted_when_blog_is_deleted () {
        $blog = $this->factory->blog->create_and_get();
        wpmu_delete_blog($blog->blog_id);
        $posts = get_posts(array(
            'post_type' => Multisite_Directory_Entry::name,
            'meta_key' => Multisite_Directory_Entry::blog_id_meta_key,
            'meta_value' => $blog->blog_id,
        ));
        $this->assertEmpty($posts);
    }

}

/**
 * This small class tests the directory itself on WP single site installs.
 */
class Multisite_Directory_On_Singlesite_TestCase extends WP_UnitTestCase {

    /**
     * Set up tests for single site.
     */
    public function setUp () {
        if (is_multisite()) {
            $this->markTestSkipped('Single-site tests are skipped on WP Multisite builds.');
        }
    }

    /**
     * Protect users if they activate this plugin on a WP single site.
     *
     * @link https://wordpress.org/support/topic/fatal-error-2384/
     */
    public function test_activating_on_singlesite_is_a_noop () {
        $this->assertSame(false, has_action('init', array('WP_Multisite_Directory', 'initialize')));
    }
}
