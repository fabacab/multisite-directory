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
                'description' => __('Shows similarly-categorized Sites from the Network Directory.', 'multisite-directory')
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
        $instance = wp_parse_args($instance, array(
            // Widget defaults.
            'only_locations' => 0,
            'show_site_logo' => 1,
            'site_logo_size' => 'post-thumbnail',
        ));
?>
<p>
    <input type="checkbox"
        id="<?php print $this->get_field_id('only_locations');?>"
        name="<?php print $this->get_field_name('only_locations')?>"
        value="1"
        <?php checked($instance['only_locations']);?>
    />
    <label for="<?php print $this->get_field_id('only_locations');?>">
        <?php esc_html_e('Limit to locations', 'multisite-directory');?>
    </label>
</p>
<p>
    <input type="checkbox"
        id="<?php print $this->get_field_id('show_site_logo');?>"
        name="<?php print $this->get_field_name('show_site_logo')?>"
        value="1"
        <?php checked($instance['show_site_logo']);?>
    />
    <label for="<?php print $this->get_field_id('show_site_logo');?>">
        <?php esc_html_e('Show site logos', 'multisite-directory');?>
    </label>
    <label for="<?php print $this->get_field_id('site_logo_size');?>">
        <?php
        /* translators: Part of the logo size widget, like "Show site logo at size: " */
        esc_html_e('at size', 'multisite-directory');
        ?>
    </label>
    <select
        id="<?php print $this->get_field_id('site_logo_size');?>"
        name="<?php print $this->get_field_name('site_logo_size');?>"
    >
        <?php foreach (get_intermediate_image_sizes() as $size) { if (has_image_size($size)) : ?>
        <option <?php selected($instance['site_logo_size'], $size);?>><?php print esc_html($size);?></option>
        <?php endif; } ?>
    </select>
</p>
<?php
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
        $instance = array();

        $instance['only_locations'] = (int) $new_instance['only_locations'];
        $instance['show_site_logo'] = (int) $new_instance['show_site_logo'];
        $instance['site_logo_size'] = (has_image_size($new_instance['site_logo_size'])) ? $new_instance['site_logo_size'] : 0;

        return $instance;
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

            // The "only locations" option filters categories to ones
            // that have a geographical coordinate associated with it.
            if (!empty($instance['only_locations'])) {
                switch_to_blog(1);
                foreach ($terms as $k => $v) {
                    if (!get_term_meta($v->term_id, 'geo', true)) {
                        unset($terms[$k]);
                    }
                }
                restore_current_blog();
            }

?>
<h1><?php esc_html_e('Similar sites', 'multisite-directory');?></h1>
<ul class="network-directory-similar-sites">
    <?php foreach ($terms as $term) { $similar_sites = get_sites_in_directory_by_term($term); ?>
    <li><?php print esc_html($term->name); ?>
        <ul>
        <?php foreach ($similar_sites as $site_detail) { if (get_current_blog_id() == $site_detail->blog_id) { continue; } ?>
        <li>
            <?php if (!empty($instance['show_site_logo'])) { the_site_directory_logo($site_detail->blog_id, $instance['site_logo_size']); } ?>
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
