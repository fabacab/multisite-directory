<?php
/**
 * The Multisite Directory Shortcode
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * The shortcode implementation.
 *
 * Defines a shortcode called `multisite-directory` that accepts some
 * attributes with which to customize the display of a simple network
 * directory. Valid attributes and their values are:
 *
 * * `style` - An inline `style` attribute, useful for adding custom CSS to the container.
 * * `display` - A string describing how to display the sites. Can be either `list` or `map`. (Default: `map`.)
 * * `terms` - A space-separated list of term slugs to limit the directory to. Omit this to include all terms.
 * * `show_site_logo` - Whether or not to show site logos in output. Omit this not to show site logos.
 * * `logo_size` - A registered image size that your theme supports. Ignored if `show_site_logo` is not enabled. Defaults to 70px by 70px.
 *
 * @link https://codex.wordpress.org/Shortcode_API
 */
class Multisite_Directory_Shortcode {

    /**
     * The tag for the shortcode itself.
     *
     * @var string
     */
    const tagname = 'site-directory';

    /**
     * How many times have we invoked the shortcode?
     *
     * @var int
     */
    public static $invocations = 0;

    /**
     * Attributes passed to the shortcode.
     *
     * @var array
     */
    private $atts;

    /**
     * Any content within the opening and closing tags.
     *
     * @var string
     */
    private $content;

    /**
     * Constructor.
     *
     * @param array $atts
     * @param string $content
     */
    public function __construct ($atts, $content = null) {
        if (empty($atts)) { $atts = array(); }
        $this->atts = shortcode_atts(array(
            // Recognized shortcode attribute names and their values.
            'display' => 'map',
            'style'  => '',
            'terms' => array(),
            'show_site_logo' => false,
            'logo_size' => array(72,72),
        ), array_map(array($this, 'parseJsonAttribute'), $atts));
        $this->content = $content;
    }

    /**
     * Parses a complex shortcode attribute.
     *
     * Some attributes can be passed as JSON. This method detects the
     * ones that are and decodes their values.
     *
     * @param string $val
     *
     * @return mixed
     */
    private function parseJsonAttribute ($val) {
        $parsed = json_decode($val);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $parsed;
        } else {
            return $val;
        }
    }

    /**
     * Gets data from WordPress based on shortcode attributes.
     *
     * This will set the `$html` member to the appropriate output.
     *
     * @return void
     */
    private function prepare () {
        $cpt = new Multisite_Directory_Entry();
        $html = '';

        // When displaying a map
        if ('map' === $this->atts['display']) {
            // Find all mappable terms
            $terms = get_site_directory_location_terms();

            // Turn that into GeoJSON (easier to map)
            $data = $this->makeGeoJSON($terms);

            // Turn data into the appropriate HTML/JS, etc.
            $html = $this->prepareMap($data);
        } else if ('list' === $this->atts['display']) {
            ob_start();

            $terms = get_site_terms(get_current_blog_id());
            if (!is_wp_error($terms) && !empty($terms)) {
                // TODO: Refactor this so it's not embedded HTML.
                //       I used output buffering just for now.
?>
<h1><?php esc_html_e('Similar sites', 'multisite-directory');?></h1>
<ul class="network-directory-similar-sites">
    <?php foreach ($terms as $term) { $similar_sites = get_sites_in_directory_by_term($term); ?>
    <li><?php print esc_html($term->name); ?>
        <ul>
        <?php foreach ($similar_sites as $site_detail) { if (get_current_blog_id() == $site_detail->blog_id) { continue; } ?>
        <li>
            <?php if (!empty($this->atts['show_site_logo'])) { the_site_directory_logo($site_detail->blog_id, $this->atts['logo_size']); } ?>
            <a href="<?php print esc_url($site_detail->siteurl);?>"><?php print esc_html($site_detail->blogname);?></a>
        </li>
        <?php } ?>
        </ul>
    </li>
    <?php } ?>
</ul>
<?php
            }
            $html = ob_get_contents();
            ob_end_clean();
        }

        // Save the HTML for later display.
        $this->html = $html;
    }

    /**
     * Makes a GeoJSON-compliant object from the set of mappable terms.
     *
     * @todo Possibly useful to generalize this at some point?
     *
     * @link http://geojson.org/
     *
     * @return object GeoJSON-compliant object.
     */
    private function makeGeoJSON ($terms) {
        $data = new stdClass();
        $data->type = 'FeatureCollection';
        $data->features = array();
        foreach ($terms as $term) {
            $item = new stdClass();
            $item->type = 'Feature';

            $item->geometry = new stdClass();
            $item->geometry->type = 'Point';
            $item->geometry->coordinates = $this->geoStringToGeoJSONPosition(
                get_term_meta($term->term_id, 'geo', true)
            );

            // Augment the map point with term details
            $item->properties = new stdClass();
            $item->properties->id = $term->term_id;
            $item->properties->name = $term->name;
            $item->properties->slug = $term->slug;
            // and list of sites categorized with the given location
            $cpt = new Multisite_Directory_Entry();
            $sites = $cpt->get_posts(array(
                'numberposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => Multisite_Directory_Taxonomy::name,
                        'terms' => $term->term_id
                    )
                )
            ));
            $item->properties->sites = array();
            foreach ($sites as $k => $site) {
                $item->properties->sites[$k] = new stdClass();
                $item->properties->sites[$k]->post_name = $site->post_name;
                $item->properties->sites[$k]->post_title = $site->post_title;
                $item->properties->sites[$k]->post_excerpt = $site->post_excerpt;
                $item->properties->sites[$k]->post_content = $site->post_content;
                $item->properties->sites[$k]->meta = new stdClass();
                $item->properties->sites[$k]->meta->siteurl = get_site_permalink($site->{$cpt::blog_id_meta_key});
                $item->properties->sites[$k]->meta->sitelogo = get_site_directory_logo($site->{$cpt::blog_id_meta_key}, $this->atts['logo_size']);
            }

            $data->features[] = $item;
        }

        return $data;
    }

    /**
     * Helper to convert a "lat,lng" string into GeoJSON Position (an array).
     *
     * @param string $geo
     *
     * @return float[]
     */
    private function geoStringToGeoJSONPosition ($geo) {
        if (!is_string($geo)) {
            return $geo;
        }
        $coords = explode(',', $geo);
        return array(
            // Note that this is reversed (lng,lat) because the
            // GeoJSON spec expects it this way, confusingly.
            $coords[1], // Longitude
            $coords[0]  // Latitude
        );
    }

    /**
     * Prepares front-end assets for a map.
     *
     * This will actually enqueue various JavaScript and CSS assets
     * if they have not been enqueued already, builds an appropriate
     * HTML element for the map container and sets its `id` and
     * `class` attributes, etc.
     *
     * @param array $data Info to show on the map.
     *
     * @return string HTML ready for display.
     */
    private function prepareMap ($data) {
        $id = join('-', array(self::tagname, self::$invocations));

        // Prep JS.
        if (!wp_script_is('leaflet') || !wp_style_is('leaflet')) {
            wp_enqueue_style('leaflet');
            wp_enqueue_script('leaflet');
        }

        // Send shortcode map data to the front-end as JavaScript.
        wp_localize_script('multisite-directory-map', str_replace('-', '_', "multisite_directory_$id"), (array) $data);
        wp_localize_script('multisite-directory-map', 'multisite_directory_map_ui_strings', array(
            'i18n_no_sites_at_location' => __('No sites at this location.'),
        ));

        wp_enqueue_style('multisite-directory-map');
        wp_enqueue_script('multisite-directory-map');

        // Prep HTML.
        $class = join('-', array(self::tagname, 'map'));
        $html  = '<div id="'.esc_attr($id).'" class="'.esc_attr($class).'" style="'.esc_attr($this->atts['style']).'">';
        if ($this->content) {
            // If the shortcode is a map, allow content in the shortcode tags
            // to serve as a user-supplied fallback in case JS is disabled.
            $html .= '<noscript>'.esc_html($this->content).'</noscript>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Prints the shortcode output to the browser.
     */
    private function display () {
        print $this->html;
    }

    /**
     * Registers the shortcode and its assets.
     */
    public static function register () {
        add_shortcode('site-directory', array(__CLASS__, 'doShortcode'));

        wp_register_style(
            'multisite-directory-map',
            plugins_url('public/css/multisite-directory-map.css', dirname(__FILE__))
        );
        wp_register_script(
            'multisite-directory-map',
            plugins_url('public/js/multisite-directory-map.js', dirname(__FILE__)),
            array('leaflet', 'jquery'),
            false,
            true
        );
    }

    /**
     * Shortcode handler.
     *
     * @param array $atts
     * @param string $content
     */
    public static function doShortcode ($atts, $content = null) {
        self::$invocations++;
        $shortcode = new self($atts, $content);
        $shortcode->prepare();
        $shortcode->display();
    }

}
