<?php
/**
 * A Taxonomy for the (Multi)Site Directory.
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * @copyright Copyright (c) 2016 TK-TODO
 *
 * @package WordPress\Plugin\Multisite_Directory
 */

/**
 * Class defining the taxonomy for the site directory.
 */
class Multisite_Directory_Taxonomy {

    /**
     * Name of the taxonomy.
     *
     * @var string
     */
    const name = 'subsite_category';

    /**
     * Capabilities needed to act on the taxonomy.
     *
     * @var array
     */
    private $capabilities = array(
        'manage_terms' => 'manage_sites',
        'edit_terms'   => 'manage_sites',
        'delete_terms' => 'manage_network',
        'assign_terms' => 'edit_posts',
    );

    /**
     * Constructor.
     */
    public function __construct () {
    }

    /**
     * Sanitizes a geo (lat/lon pair) string input.
     *
     * @param string $geo
     *
     * @return string
     */
    public function sanitizeGeoString ($geo) {
        return implode(',', filter_var_array(explode(',', $geo), FILTER_VALIDATE_FLOAT));
    }

    /**
     * Registers the taxonomy.
     */
    public function register () {
        register_taxonomy(self::name, Multisite_Directory_Entry::name, array(
            'hierarchical' => true,
            'capabilities' => $this->capabilities,
        ));

        register_meta('term', 'geo', array($this, 'sanitizeGeoString'));

        if (is_admin()) {
            add_action('admin_enqueue_scripts', array('WP_Multisite_Directory_Admin', 'enqueue_scripts'));
            add_action(self::name.'_add_form_fields', array($this, 'add_form_fields'));
            add_action('create_'.self::name, array($this, 'saveTermGeo'));
            add_action('edit_'.self::name, array($this, 'saveTermGeo'));
        }

    }

    /**
     * Outputs taxonomy meta fields to the add new term form.
     *
     * @link https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
     *
     * @param string $taxonomy
     */
    public function add_form_fields ($taxonomy) {
?>
<div class="form-field term-geo-wrap">
    <label for="term-geo"><?php esc_html_e('Location', 'multisite-directory');?></label>
    <div id="term-map" style="height: 180px;"></div>
    <p><a href="#" class="button"><?php esc_html_e('Remove location', 'multisite-directory');?></a></p>
    <input type="hidden" id="term-geo" name="geo" value="" />
    <p><?php esc_html_e('If this category relates to a physical location or area, add its geographical coordinates.' ,'multisite-directory');?></p>
</div>
<script>
jQuery(document).ready(function () {
    var term_geo = jQuery('#term-geo');
    var button = jQuery('.term-geo-wrap a.button');
    var mapmarker;
    var mymap = L.map('term-map').setView({lat: 40.730608477796636, lng: -73.99017333984375}, 10);
    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mymap);
    mymap.on('click', function (e) {
        if (mapmarker) {
            mymap.removeLayer(mapmarker);
        }
        mapmarker = L.marker(e.latlng).addTo(mymap);
        term_geo.val(e.latlng.lat + ',' + e.latlng.lng);
    });
    button.on('click', function (e) {
        e.preventDefault();
        term_geo.val('');
        mymap.removeLayer(mapmarker);
    });
});
</script>
<?php
    }

    /**
     * Saves the term geo value.
     *
     * @link https://developer.wordpress.org/reference/hooks/create_taxonomy/
     * @link https://developer.wordpress.org/reference/hooks/edit_taxonomy/
     *
     * @param int $term_id
     */
    public function saveTermGeo ($term_id) {
        if (empty($_POST['geo'])) {
            return;
        }
        $geo = $_POST['geo'];

        $old_geo = get_term_meta($term_id, 'geo', true);
        $new_geo = $this->sanitizeGeoString($geo);

        if ($old_geo && '' === $new_geo) {
            delete_term_meta($term_id, 'geo');
        } else if ($old_geo !== $new_geo) {
            update_term_meta($term_id, 'geo', $new_geo);
        }
    }

}
