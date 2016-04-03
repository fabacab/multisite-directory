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
     * Set up tests.
     *
     * The `setUp` method is run by PHPUnit before each test method
     * is run.
     *
     * This allows us to reset back to a known, initial state after
     * each test.
     */
    public function setUp () {
        // Since the shortcode is only really intended for Multisite
        // installs, if this isn't a WP Multisite, we skip all tests.
        if (!is_multisite()) {
            $this->markTestSkipped('Shortcode tests are for WP Multisite only.');
        }
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
