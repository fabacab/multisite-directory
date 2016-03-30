<?php
/**
 * The Multisite Directory Widget
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * The widget implementation.
 *
 * @link https://developer.wordpress.org/reference/classes/WP_Widget/
 */
class Multisite_Directory_Widget extends WP_Widget {

    public function __construct () {
        parent::__construct(
            strtolower(__CLASS__),
            __('Network Directory Widget', 'multisite-directory'),
            array(
                'description' => __('Shows a portion of the Sites from the Network Directory.', 'multisite-directory')
            )
        );
    }

    /**
     * Outputs the widget's settings form HTML.
     *
     * @param array $instance
     *
     * @return string
     */
    public function form ($instance) {
    }

    /**
     * Updates the widget instance.
     *
     * @link https://developer.wordpress.org/reference/classes/wp_widget/update/
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array|bool Settings to save or bool false to cancel saving.
     */
    public function update ($new_instance, $old_instance) {
    }

    /**
     * Outputs widget HTML.
     *
     * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
     *
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget ($args, $instance) {
        $terms = get_site_terms(get_current_blog_id());
        if (!is_wp_error($terms) && !empty($terms)) {
?>
<h1><?php esc_html_e('Similar sites', 'multisite-directory');?></h1>
<ul class="network-directory-similar-sites">
    <?php foreach ($terms as $term) { $similar_sites = get_sites_in_directory_by_term($term); ?>
    <li><?php print esc_html($term->name); ?>
        <ul>
        <?php foreach ($similar_sites as $site_detail) { if (get_current_blog_id() == $site_detail->blog_id) { continue; } ?>
        <li>
            <a href="<?php print esc_url($site_detail->siteurl);?>"><?php print esc_html($site_detail->blogname);?></a>
        </li>
        <?php } ?>
        </ul>
    </li>
    <?php } ?>
</ul>
<?php
        }
    }

}
