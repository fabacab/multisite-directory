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
            'display' => 'list',
            'show_site_logo' => 1,
            'logo_size' => 'post-thumbnail',
        ));
?>
<ul>
    <li>
        <input type="radio"
            id="<?php print $this->get_field_id('display_as_list');?>"
            name="<?php print $this->get_field_name('display')?>"
            value="list"
            <?php checked($instance['display'], 'list');?>
        />
        <label for="<?php print $this->get_field_id('display_as_list');?>">
            <?php esc_html_e('Display as list', 'multisite-directory');?>
        </label>
    </li>
    <li>
        <input type="radio"
            id="<?php print $this->get_field_id('display_as_map');?>"
            name="<?php print $this->get_field_name('display')?>"
            value="map"
            <?php checked($instance['display'], 'map');?>
        />
        <label for="<?php print $this->get_field_id('display_as_map');?>">
            <?php esc_html_e('Display as map', 'multisite-directory');?>
        </label>
    </li>
</ul>
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
    <label for="<?php print $this->get_field_id('logo_size');?>">
        <?php
        /* translators: Part of the logo size widget, like "Show site logo at size: " */
        esc_html_e('at size', 'multisite-directory');
        ?>
    </label>
    <select
        id="<?php print $this->get_field_id('logo_size');?>"
        name="<?php print $this->get_field_name('logo_size');?>"
    >
        <?php foreach (get_intermediate_image_sizes() as $size) { if (has_image_size($size)) : ?>
        <option <?php selected($instance['logo_size'], $size);?>><?php print esc_html($size);?></option>
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

        $instance['display'] = sanitize_text_field($new_instance['display']);
        $instance['show_site_logo'] = (int) $new_instance['show_site_logo'];
        $instance['logo_size'] = (has_image_size($new_instance['logo_size'])) ? $new_instance['logo_size'] : 0;

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
        $atts = '';
        foreach ($instance as $k => $v) {
            if ($v) {
                $atts .= "$k='$v' ";
            }
        }
        print do_shortcode('['.Multisite_Directory_Shortcode::tagname." $atts]");
    }

}
