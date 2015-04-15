<?php
/**
 * Plugin Name: Easy Digital Downloads - Points and Rewards
 * Plugin URI: https://easydigitaldownloads.com/extensions/points-rewards/
 * Description: With this extension you can reward customers for purchases and other actions with points which can be redeemed for discounts.
 * Version: 1.3.1
 * Author: WPWeb
 * Author URI: http://wpweb.co.in
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;

/**
 * Basic plugin definitions
 * 
 * @package Easy Digital Downloads - Points and Rewards
 * @since 1.0.0
 */
if( !defined( 'EDD_POINTS_URL' ) ) {
	define( 'EDD_POINTS_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'EDD_POINTS_DIR' ) ) {
	define( 'EDD_POINTS_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'EDD_POINTS_IMG_URL' ) ) {
	define( 'EDD_POINTS_IMG_URL', EDD_POINTS_URL . 'includes/images' ); // plugin url
}
if( !defined( 'EDD_POINTS_ADMIN' ) ) {
	define( 'EDD_POINTS_ADMIN', EDD_POINTS_DIR . '/includes/admin' ); // plugin admin dir
}
if( !defined( 'EDD_POINTS_LOG_POST_TYPE' ) ) {
	define( 'EDD_POINTS_LOG_POST_TYPE', 'eddpointslog'); //post type for points log
}
if( !defined( 'EDD_POINTS_BASENAME' ) ) {
	define( 'EDD_POINTS_BASENAME', 'edd-points-and-rewards' ); //points and rewards basename
}

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package Easy Digital Downloads - Points and Rewards
 * @since 1.0.0
 */
load_plugin_textdomain( 'eddpoints', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

//check easy digital downloads is activated or not
if( class_exists( 'Easy_Digital_Downloads' ) ) {
	
	//check EDD_License class is exist
	if( class_exists( 'EDD_License' ) ) {
		
		// Instantiate the licensing / updater. Must be placed in the main plugin file
		$license = new EDD_License( __FILE__, 'Points and Rewards', '1.3.1', 'WPWeb' );
	}
	
	/**
	 * Activation Hook
	 * 
	 * Register plugin activation hook.
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 */
	register_activation_hook( __FILE__, 'edd_points_install' );
	
	/**
	 * Deactivation Hook
	 * 
	 * Register plugin deactivation hook.
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 */
	register_deactivation_hook( __FILE__, 'edd_points_uninstall' );
	
	/**
	 * Plugin Setup (On Activation)
	 * 
	 * Does the initial setup,
	 * stest default values for the plugin options.
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.1.2
	 */
	function edd_points_install() {
		
		global $wpdb, $edd_options;
		
		//register post type
		edd_points_register_post_type();
		
		//IMP Call of Function
		//Need to call when custom post type is being used in plugin
		flush_rewrite_rules();
		
		$udpopt = false;
		//check earning points conversion not set
		if( !isset( $edd_options['edd_points_earn_conversion'] ) ) {
			$edd_options['edd_points_earn_conversion'] = array( 'points' => __( '1', 'eddpoints' ), 'rate' => __( '1', 'eddpoints' ) );
			$udpopt = true;
		}//end if
		
		//check redeem conversion is not set
		if( !isset( $edd_options['edd_points_redeem_conversion'] ) ) {
			$edd_options['edd_points_redeem_conversion'] = array( 'points' => __( '100', 'eddpoints' ), 'rate' => __( '1', 'eddpoints' ) );
			$udpopt = true;
		} //end if
		
		//check buy conversion rate is not set
		if( !isset( $edd_options['edd_points_buy_conversion'] ) ) {
			$edd_options['edd_points_buy_conversion'] = array( 'points' => __( '100', 'eddpoints' ), 'rate' => __( '1', 'eddpoints' ) );
			$udpopt = true;
		}//end if
		
		//check selling conversion rate is not set
		if( !isset( $edd_options['edd_points_selling_conversion'] ) ) {
			$edd_options['edd_points_selling_conversion'] = array( 'points' => __( '0.00', 'eddpoints' ), 'rate' => __( '0.00', 'eddpoints' ) );
			$udpopt = true;
		}//end if
		
		//check cart maximum discount is not set
		if( !isset( $edd_options['edd_points_cart_max_discount'] ) ) {
			$edd_options['edd_points_cart_max_discount'] = '';
			$udpopt = true;
		} //end if
		
		//check max discount is not set
		if( !isset( $edd_options['edd_points_max_discount'] ) ) {
			$edd_options['edd_points_max_discount'] = '';
			$udpopt = true;
		} //end if
		
		//check points label is not set
		if( !isset( $edd_options['edd_points_label'] ) ) {
			$edd_options['edd_points_label'] = array( 'singular' => __( 'Point', 'eddpoints' ), 'plural' => __( 'Points', 'eddpoints' ) );
			$udpopt = true;
		} //end if
		
		//check points single product message
		if( !isset( $edd_options['edd_points_single_product_messages'] ) ) {
			$edd_options['edd_points_single_product_messages'] = sprintf(__( 'Purchase this product now and earn %s!','eddpoints'),'<strong>{points}</strong> {points_label}');
			$udpopt = true;
		} //end if
		
		//check points cart message
		if( !isset( $edd_options['edd_points_cart_messages'] ) ) {
			$edd_options['edd_points_cart_messages'] = sprintf(__('Complete your order and earn %s for a discount on a future purchase.','eddpoints'),'<strong>{points}</strong> {points_label}');
			$udpopt = true;
		} //end if
		
		//check redeem cart message
		if( !isset( $edd_options['edd_points_reedem_cart_messages'] ) ) {
			$edd_options['edd_points_reedem_cart_messages'] = sprintf(__('Use %s for a %s discount on this order.','eddpoints'),'<strong>{points}</strong> {points_label}','<strong>{points_value}</strong>');
			$udpopt = true;
		} //end if
		
		//check earn guest message
		if( !isset( $edd_options['edd_points_earn_guest_messages'] ) ) {
			$edd_options['edd_points_earn_guest_messages'] = sprintf(__( 'You need to register an account in order to earn %s.','eddpoints' ),'<strong>{points}</strong> {points_label}');
			$udpopt = true;
		} //end if
		
		//check bought guest message
		if( !isset( $edd_options['edd_points_bought_guest_messages'] ) ) {
			$edd_options['edd_points_bought_guest_messages'] = sprintf(__( 'You need to register an account in order to fund %s into your account.','eddpoints' ),'<strong>{points}</strong> {points_label}');
			$udpopt = true;
		} //end if
		
		//check earning points for accoutn signup
		if( !isset( $edd_options['edd_points_earned_account_signup'] ) ) {
			$edd_options['edd_points_earned_account_signup'] = '500';
			$udpopt = true;
		} //end if
		
		//check need to update the defaults value to options
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option( 'edd_settings', $edd_options );
		}
	}
	
	/**
	 * Plugin Setup (On Deactivation)
	 * 
	 * Delete  plugin options.
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 */
	function edd_points_uninstall() {
		
		global $wpdb;
		
		//IMP Call of Function
		//Need to call when custom post type is being used in plugin
		flush_rewrite_rules();
	}
	
	/**
	 * Includes Files
	 * 
	 * Includes some required files for plugin
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 */
	global $edd_points_model, $edd_points_scripts,
		$edd_points_render, $edd_points_shortcodes,
		$edd_points_public, $edd_points_admin,
		$edd_points_log;
	
	// loads the Misc Functions file
	require_once ( EDD_POINTS_DIR . '/includes/edd-points-misc-functions.php' );
	
	//Pagination Class
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-pagination-public.php' ); // front end pagination class
	
	//Model Class for generic functions
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-model.php' );
	$edd_points_model = new EDD_Points_Model();
	
	//Scripts Class for scripts / styles
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-scripts.php' );
	$edd_points_scripts = new EDD_Points_Scripts();
	$edd_points_scripts->add_hooks();
	
	//Renderer Class for HTML
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-renderer.php' );
	$edd_points_render = new EDD_Points_Renderer();
	
	//Shortcodes class for handling shortcodes
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-shortcodes.php' );
	$edd_points_shortcodes = new EDD_Points_Shortcodes();
	$edd_points_shortcodes->add_hooks();
	
	//Add post type page for points functionality.
	require_once( EDD_POINTS_DIR . '/includes/edd-points-post-types.php');
	
	//Insert logs for points functionality.
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-log.php');
	$edd_points_log = new EDD_Points_Logging();
	
	//Public Class for public functionlities
	require_once( EDD_POINTS_DIR . '/includes/class-edd-points-public.php' );
	$edd_points_public = new EDD_Points_Public();
	$edd_points_public->add_hooks();
	
	//Admin Pages Class for admin site
	require_once( EDD_POINTS_ADMIN . '/class-edd-points-admin.php' );
	$edd_points_admin = new EDD_Points_Admin();
	$edd_points_admin->add_hooks();
}
?>