<?php
/*
Plugin Name: Business Directory Plugin - Google Maps Module
Description: Adds support for Google Maps for display in a Business Directory listing.  Allows you to map any set of fields to the address for use by Google Maps.  REQUIRES Business Directory 3.0 or higher to run.
Plugin URI: http://www.businessdirectoryplugin.com
Version: 3.5
Author: D. Rodenbaugh
Author URI: http://businessdirectoryplugin.com
License: GPLv2 or any later version
*/

class BusinessDirectory_GoogleMapsPlugin {

    const VERSION = '3.5';
    const REQUIRED_BD_VERSION = '3.5.2';

    const GOOGLE_MAPS_JS_URL = 'https://maps.google.com/maps/api/js';

    private $maps_handle = '';
    private $maps_handles_remove = array();

    private $map_locations = array();
    private $doing_map = false;


    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'load_i18n' ) );
        add_action('admin_notices', array($this, '_admin_notices'));
        add_action( 'wpbdp_modules_loaded', array( $this, '_initialize' ) );
    }

    public function fix_scripts_src( $src, $handle ) {
        // Make sure the original src was used (no args removed, etc).
        if ( $this->maps_handle == $handle ) {
            global $wp_scripts;
            return $wp_scripts->registered[ $handle ]->src;
        }

        // Load dummy JS for other instances of Google Maps API, as to not break dependencies.
        if ( in_array( $handle, $this->maps_handles_remove, true ) )
            return plugins_url( '/resources/dummy.js', __FILE__ );

        return $src;
    }

    public function load_i18n() {
        load_plugin_textdomain( 'wpbdp-googlemaps', false, trailingslashit( basename( dirname( __FILE__ ) ) ) . 'translations/' );
    }    

    public function _initialize() {
        if ( version_compare( WPBDP_VERSION, self::REQUIRED_BD_VERSION, '<' ) )
            return;

        if ( ! wpbdp_licensing_register_module( 'Google Maps Module', __FILE__, self::VERSION ) )
           return;

        add_action( 'wpbdp_register_settings', array( &$this, 'register_settings' ) );
        add_action( 'wpbdp_modules_init', array( &$this, '_setup_actions' ) );
    }

    public function _setup_actions() {
        if ( !wpbdp_get_option( 'googlemaps-on' ) )
            return;

        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 9999 ); // We run with a huge priority in order to be last.
        add_action( 'script_loader_src', array( &$this, 'fix_scripts_src' ), 9999, 2 );
        add_action( 'save_post', array( $this, 'update_listing_geolocation' ), 10, 1 );
        add_action( 'wpbdp_save_listing', array( &$this, 'update_listing_geolocation' ), 10, 1 );
        add_filter( 'wpbdp_listing_view_after', array( $this, 'add_map_to_listing' ), 10, 2 );

        if ( wpbdp_get_option( 'googlemaps-show-category-map' ) )
            add_action( 'wpbdp_after_category_page', array( $this, '_category_map' ) );

        if ( wpbdp_get_option( 'googlemaps-show-viewlistings-map' ) )
            add_action( 'wpbdp_after_viewlistings_page', array( $this, '_view_listings_map' ) );

        if ( wpbdp_get_option( 'googlemaps-show-search-map' ) )
            add_action( 'wpbdp_after_search_results', array( $this, '_search_map' ) );
    }

    public function enqueue_scripts() {
        global $wpbdp;

        if ( method_exists( $wpbdp, 'is_plugin_page' ) && ! $wpbdp->is_plugin_page() )
            return;

        $key = wpbdp_get_option( 'googlemaps-apikey' );
        wp_enqueue_style( 'wpbdp-googlemaps-css', plugins_url( '/resources/googlemaps' . ( $wpbdp->is_debug_on() ? '' : '.min' ) . '.css', __FILE__ ) );

        $this->obtain_google_maps_handle();
        if ( ! $this->maps_handle ) {
            wp_register_script( 'googlemaps-api',
                                self::GOOGLE_MAPS_JS_URL . '?' . ( $key ? 'key=' . $key . '&' : '' ) . 'sensor=false',
                                null,
                                null );
            $this->maps_handle = 'googlemaps-api';
        }
        wp_register_script( 'oms-js',
                            plugins_url( '/resources/oms.min.js', __FILE__ ),
                            array( $this->maps_handle ) );

        wp_enqueue_script( 'wpbdp-googlemaps-js',
                           plugins_url( '/resources/googlemaps' . ( $wpbdp->is_debug_on() ? '' : '.min' ) . '.js', __FILE__ ),
                           array( 'jquery', 'oms-js' ) );
    }

    private function obtain_google_maps_handle() {
        global $wp_scripts;
        $candidates = array();

        foreach ( $wp_scripts->registered as $script ) {
            if ( ( false !== stripos( $script->src, 'maps.google.com/maps/api' ) || 
                   false !== stripos( $script->src, 'maps.googleapis.com/maps/api' ) ) &&
                   false === stripos( $script->src, 'callback' )  ) {
                $candidates[] = $script->handle;
            }
        }

        if ( $candidates ) {
            $this->maps_handle = array_shift( $candidates );
            $this->maps_handles_remove = $candidates;
        }
    }

    public function register_settings($settingsapi) {
        $g = $settingsapi->add_group('googlemaps', _x('Google Maps', 'settings', 'wpbdp-googlemaps'));

        // General settings
        $s = $settingsapi->add_section($g, 'googlemaps', _x('General Settings', 'settings', 'wpbdp-googlemaps'));
        $settingsapi->add_setting($s, 'googlemaps-on', _x('Turn on Google Maps integration?', 'settings', 'wpbdp-googlemaps'), 'boolean', false);
        $settingsapi->add_setting( $s,
                                   'googlemaps-apikey',
                                   _x( 'Google Maps API Key (optional)', 'settings', 'wpbdp-googlemaps' ),
                                   'text',
                                   '',
                                   _x('Enter your API key only if you are using Maps for Business or have Automated Billing enabled in your Google Console.', 'settings', 'wpbdp-googlemaps' ) );
        $settingsapi->add_setting($s, 'googlemaps-show-category-map', _x( 'Show listings map in categories?', 'settings', 'wpbdp-googlemaps' ), 'boolean', false  );
        $settingsapi->add_setting($s, 'googlemaps-show-viewlistings-map', _x( 'Show listings map in "View Listings"?', 'settings', 'wpbdp-googlemaps' ), 'boolean', false  );
        $settingsapi->add_setting($s, 'googlemaps-show-search-map', _x( 'Show listings map in search results?', 'settings', 'wpbdp-googlemaps' ), 'boolean', false  );
        $settingsapi->add_setting( $s,
                                   'googlemaps-position',
                                   _x( 'Display Map position', 'settings', 'wpbdp-googlemaps' ),
                                   'choice',
                                   'bottom',
                                   _x( 'Applies only to category, "View Listings" and search results maps.', 'settings', 'wpbdp-googlemaps' ),
                                   array( 'choices' => array( array( 'top', _x( 'Above all listings', 'settings', 'wpbdp-googlemaps' ) ),
                                                              array( 'bottom', _x( 'Below all listings', 'settings', 'wpbdp-googlemaps' ) ) ) ) );

        // Appearance
        $s = $settingsapi->add_section($g, 'appearance', _x('Appearance', 'settings', 'wpbdp-googlemaps'));
        $settingsapi->add_setting($s, 'googlemaps-size', _x('Map Size', 'settings', 'wpbdp-googlemaps'),
                                  'choice', null, null, array('choices' => array(array('small', _x('Small map (250x250px)', 'settings', 'wpbdp-googlemaps')),
                                                                                 array('large', _x('Large map (400x600px)', 'settings', 'wpbdp-googlemaps')),
                                                                                 array('auto', _x('Automatic', 'settings', 'wpbdp-googlemaps')),
                                                                                 array('custom', _x('Custom size', 'settings', 'wpbdp-googlemaps'))
                                                                                 ) ));
        $settingsapi->add_setting( $s, 'googlemaps-size-custom-w', _x( 'Custom map size width (px)', 'settings', 'wpbdp-googlemaps' ), 'text', '250', _x( 'Applies only to the "Custom size" map size', 'settings', 'wpbdp-googlemaps' ) );
        $settingsapi->add_setting( $s, 'googlemaps-size-custom-h', _x( 'Custom map size height (px)', 'settings', 'wpbdp-googlemaps' ), 'text', '250', _x( 'Applies only to the "Custom size" map size', 'settings', 'wpbdp-googlemaps' ) );        
        $settingsapi->add_setting( $s,
                                   'googlemaps-size-auto',
                                   _x( 'Auto-resize map when container is stretched (makes Maps responsive)', 'settings', 'wpbdp-googlemaps' ),
                                   'boolean',
                                   false );

        $settingsapi->add_setting($s, 'googlemaps-maptype', _x('Map Type', 'settings', 'wpbdp-googlemaps'),
                                  'choice', null, null, array('choices' => array(
                                        array('roadmap', _x('Roadmap', 'settings', 'wpbdp-googlemaps')),
                                        array('satellite', _x('Satellite', 'settings', 'wpbdp-googlemaps')),
                                        array('hybrid', _x('Hybrid', 'settings', 'wpbdp-googlemaps')),
                                        array('terrain', _x('Terrain', 'settings', 'wpbdp-googlemaps')),
                                    )));
        $settingsapi->add_setting($s, 'googlemaps-animate-marker', _x('Animate markers', 'settings', 'wpbdp-googlemaps'), 'boolean');

        // Field Options
        $fields_api = wpbdp_formfields_api();
        $s = $settingsapi->add_section($g, 'googlemaps-fields', _x('Field Options', 'settings', 'wpbdp-googlemaps'));

        $choices = array();
        $choices[] = array('0', _x('-- None --', 'settings', 'wpbdp-googlemaps'));
        
        foreach ( $fields_api->get_fields( true ) as $field ) {
            $choices[] = array( $field->id, esc_attr( $field->label ) );
        }

        foreach (array('googlemaps-fields-address' => _x('address', 'settings', 'wpbdp-googlemaps'),
                       'googlemaps-fields-city' => _x('city', 'settings', 'wpbdp-googlemaps'),
                       'googlemaps-fields-state' => _x('state', 'settings', 'wpbdp-googlemaps'),
                       'googlemaps-fields-zip' => _x('ZIP code', 'settings', 'wpbdp-googlemaps'),
                       'googlemaps-fields-country' => _x('country', 'settings', 'wpbdp-googlemaps')) as $k => $v) {
            $settingsapi->add_setting($s, $k,
                                      sprintf(_x('Use this field as %s:', 'settings', 'wpbdp-googlemaps'), $v),
                                      'choice', null, null, array('choices' => $choices));
        }
    }

    /**
     * Builds the address for a given listing using the current settings.
     * @param int $listing_id the listing ID
     * @param bool $pretty whether to pretty-format the address or not (defaults to FALSE)
     * @return string the listing full address
     */
    public function get_listing_address( $listing_id, $pretty=false ) {
        $settingsapi = wpbdp_settings_api();
        $fieldsapi = wpbdp_formfields_api();

        $address = '';
        foreach ( array( 'address', 'city', 'state', 'zip', 'country' ) as $field_name ) {
            if ( $field_id = wpbdp_get_option( 'googlemaps-fields-' . $field_name ) ) {
                $field = $fieldsapi->get_field( $field_id );

                if ( !$field )
                    continue;

                if ( $value = $field->plain_value( $listing_id ) ) {
                    $address .= $value . ( $pretty ? "\n" : ',' ); // TODO: this probably could be prettier like Address<EOL>City, State<EOL>...
                }
            }
        }

        $address = esc_attr( substr( $address, 0, -1) );

        return trim( $address );
    }

    /**
     * Returns a hash code used to verify that our location cache is kept current.
     * @return string
     */
    public function field_hash() {
        $hash = '';

        foreach ( array( 'address', 'city', 'state', 'zip', 'country' ) as $field_name ) {
            $field_id = wpbdp_get_option( 'googlemaps-fields-' . $field_name );
            $field_id = !$field_id ? 0 : $field_id;
            $hash .= $field_id . '-';
        }

        return substr( $hash, 0, -1 );
    }

    /**
     * Returns the latitude & longitude for the address of a given listing.
     * @param int $listing_id the listing ID.
     * @param bool $nocache wheter to bypass the cache or not. Default is FALSE.
     * @return bool|object an object with lat (latitude) & lng (longitude) keys or FALSE if geolocation fails.
     * @since 1.4
     */
    public function listing_geolocate( $listing_id, $nocache=false ) {
        if ( !$listing_id )
            return false;

        $address = $this->get_listing_address( $listing_id );
        if ( !$address )
            return false;

        $location = !$nocache ? get_post_meta( $listing_id, '_wpbdp[googlemaps][geolocation]', true ) : '';
        if ( $location && ( !isset( $location->field_hash ) || $location->field_hash != $this->field_hash() ) ) {
            return $this->listing_geolocate( $listing_id, true );
        }

        if ( $location && isset( $location->lat ) && isset( $location->lng ) )
            return $location;

        $location = $this->geolocate( $address );

        if ( !$location )
            return false;

        $location->field_hash = $this->field_hash();

        update_post_meta( $listing_id, '_wpbdp[googlemaps][geolocation]', $location );
        return $location;
    }

    private function limit_warning() {
        return get_option( 'wpbdp-googlemaps-limit-warning', false );
    }

    private function toggle_limit_warning( $warn ) {
        $option = get_option( 'wpbdp-googlemaps-limit-warning', false );

        if ( $warn != $option )
            update_option( 'wpbdp-googlemaps-limit-warning', $warn );
    }

    /**
     * Obtains the latitude & longitude for a given plain text address.
     * @param string $address the address.
     * @return bool|object an object with lat (latitude) & lng (longitude) keys or FALSE if geolocation fails.
     * @since 1.4
     */
    public function geolocate( $address='' ) {
        $address = trim ( $address );

        if ( !$address )
            return false;

        $key = wpbdp_get_option( 'googlemaps-apikey' );

        $response = wp_remote_get( 'http://maps.googleapis.com/maps/api/geocode/json?' . ( $key ? 'key=' . $key . '&' : '' ) . 'sensor=false&address=' . urlencode( $address ),
                                   array( 'timeout' => 15 )  );

        if ( is_wp_error( $response ) )
            return false;

        $response = json_decode( $response['body'] );

        if ( $response && 'OVER_QUERY_LIMIT' == $response->status )
            $this->toggle_limit_warning( true );

        if ( is_null( $response ) || $response->status != 'OK' )
            return false;

        $this->toggle_limit_warning( false );

        return $response->results[0]->geometry->location;
    }

    public function add_map_to_listing($html, $listing_id) {
        $show_google_maps = apply_filters( 'wpbdp_show_google_maps', wpbdp_get_option( 'googlemaps-on' ), $listing_id );

        if ( !$show_google_maps )
            return '';

        $this->add_listing_to_map( $listing_id );
        return $this->map( array( 'listingID' => $listing_id ) );
    }

    public function map( $args=array() ) {
        static $uid = 0;

        $args = wp_parse_args( $args, array(
                'map_uid' => $uid,
                'map_type' => wpbdp_get_option( 'googlemaps-maptype', 'roadmap' ),
                'animate_markers' => wpbdp_get_option( 'googlemaps-animate-marker', false ),
                'map_size' => wpbdp_get_option( 'googlemaps-size', 'small' ),
                'map_style_attr' => wpbdp_get_option( 'googlemaps-size' ) == 'custom' ? sprintf('width: %dpx; height: %dpx;', wpbdp_get_option( 'googlemaps-size-custom-w' ), wpbdp_get_option( 'googlemaps-size-custom-h' ) ) : '',
                'position' => wpbdp_get_option( 'googlemaps-position', 'bottom' ),
                'auto_resize' => wpbdp_get_option( 'googlemaps-size-auto', 0 ),
                'listingID' => 0
        ) );

        if ( !$this->map_locations )
            return '';

        return wpbdp_render_page( plugin_dir_path( __FILE__ ) . '/templates/map.tpl.php',
                                  array( 'locations' => $this->map_locations,
                                         'settings' => $args )
                         );

        $uid += 1;
    }

    public function _doing_map_on() {
        $this->doing_map = true;
        $this->map_locations = array();
    }

    /**
     * Adds a listing to the current map locations.
     * @param int $post_id listing ID.
     */
    public function add_listing_to_map( $post_id ) {
        $address = $this->get_listing_address( $post_id );
        $geolocation = $this->listing_geolocate( $post_id );

        if ( !$address || !$geolocation )
            return;

        $this->map_locations[] = array(
            'address' => $address,
            'geolocation' => $geolocation,
            'title' => get_the_title( $post_id ),
            'url' => get_permalink( $post_id ),
            'content' => $this->get_listing_address( $post_id, true )
        );
    }

    public function _category_map( $category ) {
        if ( ! $category )
            return;

        global $wp_query;

        // try to respect the query as much as we can to be compatible with Regions and other plugins
        $args = array_merge( $wp_query ? $wp_query->query : array(), array() );
        $args['post_type'] = WPBDP_POST_TYPE;
        $args['posts_per_page'] = -1;
        $args['post_status'] = 'publish';
        if ( isset( $args['paged'] ) ) unset( $args['paged'] );
        if ( isset( $args['numberposts'] ) ) unset( $args['paged'] );
        if ( isset( $args['paged'] ) ) unset( $args['paged'] );

        if ( !isset( $args['tax_query'] ) )
            $args['tax_query'][] = array( 'taxonomy' => WPBDP_CATEGORY_TAX, 'field' => 'id', 'terms' => $category->term_id );
        $args['fields'] = 'ids';

        if ( $listings = get_posts( $args ) ) {
            array_walk( $listings, array( $this, 'add_listing_to_map' ) );
            echo $this->map();
            $this->map_locations = array();
        }
    }

    public function _search_map() {
        global $wp_query;

        if ( !$wp_query ) return;

        $posts = $wp_query->query['post__in'];
        if ( !$posts ) return;

        array_walk( $posts,  array( $this, 'add_listing_to_map' ) );
        echo $this->map();
        $this->map_locations = array();
    }

    public function _view_listings_map() {
        global $wp_query;

        // try to respect the query as much as we can to be compatible with Regions and other plugins
        $args = array_merge( $wp_query ? $wp_query->query : array(), array() );
        $args['post_type'] = WPBDP_POST_TYPE;
        $args['posts_per_page'] = -1;
        $args['post_status'] = 'publish';
        $args['fields'] = 'ids';
        if ( isset( $args['paged'] ) ) unset( $args['paged'] );
        if ( isset( $args['numberposts'] ) ) unset( $args['paged'] );
        if ( isset( $args['paged'] ) ) unset( $args['paged'] );

        if ( $listings = get_posts( $args ) ) {
            array_walk( $listings, array( $this, 'add_listing_to_map' ) );
            echo $this->map();
            $this->map_locations = array();
        }

    }

    public function update_listing_geolocation( $listing ) {
        $listing_id = is_object( $listing ) ? $listing->get_id() : $listing;

        if ( !$listing_id || wp_is_post_revision( $listing_id ) )
            return;

        if ( get_post_type( $listing_id ) != WPBDP_POST_TYPE )
            return;

        $this->listing_geolocate( $listing_id, true );
    }

    /* Activation */
    private function check_requirements() {
        return function_exists('wpbdp_get_version') && version_compare(wpbdp_get_version(), self::REQUIRED_BD_VERSION, '>=');
    }

    public function _admin_notices() {
        if ( ! current_user_can( 'administrator' ) )
            return;

        if ( ! $this->check_requirements() ) {
            printf( '<div class="error"><p>Business Directory - Google Maps Module requires Business Directory Plugin >= %s.</p></div>', self::REQUIRED_BD_VERSION );
            return;
        }

        if ( $this->limit_warning() ) {
            echo '<div class="error"><p>';
            echo '<b>';
            _e( 'Business Directory - Google Maps Module has detected some issues while trying to contact the Google Maps API.', 'wpbdp-googlemaps' );
            echo '</b><br />';
            _e( 'This usually happens because Google imposes a daily limit on the number of requests a site can make. If you have been seeing this warning for more than 24 hours it could be because:', 'wpbdp-googlemaps' );
            echo '<br />';
            _e( '- You have a huge number of listings that need to be geocoded. If this is the case you might need to wait several days before Business Directory has cached all the locations.', 'wpbdp-googlemaps' );
            echo '<br />';
            _e( '- You are on a shared hosting and other sites are using up the request allowance for your IP.', 'wpbdp.-googlemaps' );
            echo '<br />';
            _e( '- The number of requests or Google map views in use by your site really exceeds the Google Maps API limits.', 'wpbdp-googlemaps' );
            echo '<br /><br />';
            echo str_replace( '<a>',
                              '<a href="https://developers.google.com/maps/usagelimits/" target="_blank">',
                              __( 'You might need to apply for automated billing or a business account with Google. Please read <a>Google\'s documentation on usage limits</a>.', 'wpbdp-googlemaps' ) );
            echo '</p></div>';
        }
    }

}


new BusinessDirectory_GoogleMapsPlugin();
