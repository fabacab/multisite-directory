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
     * Registers the taxonomy.
     */
    public function register () {
        register_taxonomy(self::name, Multisite_Directory_Entry::name, array(
            'hierarchical' => true,
            'capabilities' => $this->capabilities,
        ));
    }
}
