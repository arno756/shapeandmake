<?php
/*
Plugin Name: Easy Digital Downloads - Recommended Products
Plugin URI: https://filament-studios.com
Description: Recommended downloads plugin for Easy Digital Downloads
Version: 1.2.3.2
Author: Chris Klosowski
Author URI: http://filament-studios.com
Text Domain: edd-rp-txt
*/
// plugin folder url
if( !defined( 'EDD_RP_PLUGIN_URL' ) )
	define( 'EDD_RP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// plugin folder path
if( !defined( 'EDD_RP_PLUGIN_DIR' ) )
	define( 'EDD_RP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// plugin root file
if( !defined( 'EDD_RP_PLUGIN_FILE' ) )
	define( 'EDD_RP_PLUGIN_FILE', __FILE__ );


// Load the EDD license handler only if not already loaded. Must be placed in the main plugin file
if( ! class_exists( 'EDD_License' ) )
	include( dirname( __FILE__ ) . '/includes/EDD_License_Handler.php' );

define( 'EDD_RP_STORE_API_URL', 'https://easydigitaldownloads.com' );
define( 'EDD_RP_PRODUCT_NAME', 'Recommended Products' );
define( 'EDD_RP_VERSION', '1.2.3.2' );
define( 'EDD_RP_TEXT_DOMAIN', 'edd-rp-txt' );

class EDDRecommendedDownloads {
	private static $edd_rp_instance;

	private function __construct() {
		if ( !defined( 'EDD_PLUGIN_FILE' ) ) {
			if ( is_admin() )
				add_action( 'admin_notices', array( $this, 'missing_edd_core' ) );
		}

		$license = new EDD_License( __FILE__, EDD_RP_PRODUCT_NAME, EDD_RP_VERSION, 'Chris Klosowski' );

		include_once( EDD_RP_PLUGIN_DIR . '/includes/recommendation-functions.php' );
		include_once( EDD_RP_PLUGIN_DIR . '/includes/template-functions.php' );
		include_once( EDD_RP_PLUGIN_DIR . '/includes/shortcodes.php' );

		register_activation_hook( EDD_RP_PLUGIN_FILE, array( $this, 'activate' ) );

		if ( is_admin() ) {
			include_once( EDD_RP_PLUGIN_DIR . '/includes/settings.php' );
			add_action( 'admin_init', array( $this, 'admin_hooks'  ) );
		}

		add_action( 'plugins_loaded', array( $this, 'ckpn_edd_rp_loaddomain' ) );
		add_action( 'init', array( $this, 'frontend_hooks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'define_custom_scripts' ) );

		// Register shortcodes
		add_shortcode( 'recommended_products', 'edd_rp_render_shortcode' );
	}

	public static function getInstance() {
		if ( !self::$edd_rp_instance ) {
			self::$edd_rp_instance = new EDDRecommendedDownloads();
		}

		return self::$edd_rp_instance;
	}

	public function missing_edd_core() {
		add_settings_error( 'edd-rp-notices', 'missing-edd', sprintf( __( 'Recommended Products for Easy Digital Downloads requires Easy Digital Downloads. Please <a href="%s">install & activate</a> Easy Digital Downloads.', 'edd-rp-txt' ), admin_url( 'plugins.php' ) ) );

		settings_errors( 'edd-rp-notices' );
	}

	public function activate() {
		edd_rp_generate_stats();
	}

	public function ckpn_edd_rp_loaddomain() {

		// Set filter for language directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'edd_recommended_products_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'edd-rp-txt' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd-rp-txt', $locale );

		// Setup paths to current locale file
		$mofile_local   = $lang_dir . $mofile;
		$mofile_global  = WP_LANG_DIR . '/edd-recommended-products/' . $mofile;
		if( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/edd-recommended-products/ folder
			load_textdomain( 'edd-rp-txt', $mofile_global );
		} elseif( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/edd-recommended-products/languages/ folder
			load_textdomain( 'edd-rp-txt', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'edd-rp-txt', false, $lang_dir );
		}
	}

	public function admin_hooks() {

	}

	public function frontend_hooks() {
		if ( !defined( 'EDD_VERSION' ) )
			return false;

		global $master_array;
		$master_array = get_option( '_edd_rp_master_array' );

		$single_status = edd_get_option( 'edd_rp_display_single',  1 );
		if ( $single_status )
			add_filter( 'edd_after_download_content', 'edd_rp_display_single', 10, 1 );

		$checkout_status = edd_get_option( 'edd_rp_display_checkout', 1 );
		if ( $checkout_status )
			add_filter( 'edd_after_checkout_cart', 'edd_rp_display_checkout' );

		$this->determine_cron_schedule();
	}

	public function define_custom_scripts() {
		wp_register_style( 'edd-rp-styles', EDD_RP_PLUGIN_URL . 'css/style.css', NULL, EDD_RP_VERSION, 'all' );
		wp_enqueue_style( 'edd-rp-styles' );
	}

	/*
	 * determine_cron_schedule
	 *
	 * Is used to figure out if our cron is already determined and then adds the hook for updating the suggestion stats
	 */
	public function determine_cron_schedule() {
		if ( !wp_next_scheduled( 'edd_rp_suggestions' ) ) {
			$next_run = strtotime( '23:00' ) + ( -( get_option('gmt_offset') * 60 * 60 ) ); // Calc for the WP timezone

			if ( (int)date_i18n( 'G' ) >= 23 )
				$next_run = strtotime( 'next day 23:00' ) + ( -( get_option('gmt_offset') * 60 * 60 ) ); // Calc for the WP timezone;

			wp_schedule_event( $next_run, 'daily', 'edd_rp_suggestions' );
		}
		add_action( 'edd_rp_suggestions', 'edd_rp_generate_stats' );
	}
}

$edd_rp_loaded = EDDRecommendedDownloads::getInstance();
