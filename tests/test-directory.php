<?php
/**
 * Tests for the Multisite Directory itself.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
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
        $blogs = wp_get_sites();
        foreach ($blogs as $blog) {
            wpmu_delete_blog($blog['blog_id'], true); // drop database tables, too
        }
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
