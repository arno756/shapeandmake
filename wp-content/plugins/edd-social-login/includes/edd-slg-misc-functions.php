<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * All misc functions handles to 
 * different functions 
 * 
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 *
 */
	/**
	 * All Social Deals Networks
	 * 
	 * Handles to return all social networks
	 * names
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_social_networks() {
		
		$socialnetworks = array(
									'facebook'		=>	__( 'Facebook', 'eddslg' ),
									'twitter'		=>	__( 'Twitter', 'eddslg' ),
									'googleplus'	=>	__( 'Google+', 'eddslg' ),
									'linkedin'		=>	__( 'LinkedIn', 'eddslg' ),
									'yahoo'			=>	__( 'Yahoo', 'eddslg' ),
									'foursquare'	=>	__( 'Foursquare', 'eddslg' ),
									'windowslive'	=>	__( 'Windows Live', 'eddslg' ),
									'vk'			=>	__( 'VK', 'eddslg' ),
								);
		return apply_filters( 'edd_slg_social_networks', $socialnetworks );
		
	}
	
	/**
	 * Get Social Network Sorted List
	 * as per saved in options
	 * 
	 * Handles to return social networks sorted
	 * array to list in page
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_get_sorted_social_network() {
		
		global $edd_options;
		
		$edd_social_order = get_option( 'edd_social_order' );
		
		$socials = edd_slg_social_networks();
		
		if( !isset( $edd_social_order ) || empty( $edd_social_order ) ){
			return $socials;
		}
		$sorted_socials = $edd_social_order;
		$return = array();
		for( $i = 0; $i < count( $socials ); $i++ ){
			$return[$sorted_socials[$i]] = $socials[$sorted_socials[$i]];
		}
		return apply_filters( 'edd_slg_sorted_social_networks', $return );
	}
	
	/**
	 * Initialize some needed variables
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_initialize() {
		
		global $edd_options;
		
		//facebook variable initialization
		$fb_app_id = isset( $edd_options['edd_slg_fb_app_id'] ) ? $edd_options['edd_slg_fb_app_id'] : '';
		$fb_app_secret = isset( $edd_options['edd_slg_fb_app_secret'] ) ? $edd_options['edd_slg_fb_app_secret'] : '';
		
		if( !defined( 'EDD_SLG_FB_APP_ID' ) ){
			define( 'EDD_SLG_FB_APP_ID', $fb_app_id );
		}
		if( !defined( 'EDD_SLG_FB_APP_SECRET' ) ){
			define( 'EDD_SLG_FB_APP_SECRET', $fb_app_secret );
		}
		
		//google+ variable initialization
		$gp_client_id = isset( $edd_options['edd_slg_gp_client_id'] ) ? $edd_options['edd_slg_gp_client_id'] : '';
		$gp_client_secret = isset( $edd_options['edd_slg_gp_client_secret'] ) ? $edd_options['edd_slg_gp_client_secret'] : '';
		
		if( !defined( 'EDD_SLG_GP_CLIENT_ID' ) ){
			define( 'EDD_SLG_GP_CLIENT_ID', $gp_client_id );
		}
		if( !defined( 'EDD_SLG_GP_CLIENT_SECRET' ) ){
			define( 'EDD_SLG_GP_CLIENT_SECRET', $gp_client_secret );
		}
		if( !defined( 'EDD_SLG_GP_REDIRECT_URL' ) ) {
			$googleurl = add_query_arg( 'eddslg', 'google', site_url() );
			define( 'EDD_SLG_GP_REDIRECT_URL', $googleurl );
		}
		
		//linkedin variable initialization
		$li_app_id = isset( $edd_options['edd_slg_li_app_id'] ) ? $edd_options['edd_slg_li_app_id'] : '';
		$li_app_secret = isset( $edd_options['edd_slg_li_app_secret'] ) ? $edd_options['edd_slg_li_app_secret'] : '';
		
		if( !defined( 'EDD_SLG_LI_APP_ID' ) ){
			define( 'EDD_SLG_LI_APP_ID', $li_app_id );
		}
		if( !defined( 'EDD_SLG_LI_APP_SECRET' ) ){
			define( 'EDD_SLG_LI_APP_SECRET', $li_app_secret );
		}
		
		// For LinkedIn Port http / https
		if( !defined( 'LINKEDIN_PORT_HTTP' ) ) { //http port value
		 	define( 'LINKEDIN_PORT_HTTP', '80' );
		}
		if( !defined( 'LINKEDIN_PORT_HTTP_SSL' ) ) { //ssl port value
		  	define( 'LINKEDIN_PORT_HTTP_SSL', '443' );
		}
		
		//twitter variable initialization
		$tw_consumer_key = isset( $edd_options['edd_slg_tw_consumer_key'] ) ? $edd_options['edd_slg_tw_consumer_key'] : '';
		$tw_consumer_secrets = isset( $edd_options['edd_slg_tw_consumer_secret'] ) ? $edd_options['edd_slg_tw_consumer_secret'] : '';
		
		if( !defined( 'EDD_SLG_TW_CONSUMER_KEY' ) ) {
			define( 'EDD_SLG_TW_CONSUMER_KEY', $tw_consumer_key );
		}
		if( !defined( 'EDD_SLG_TW_CONSUMER_SECRET' ) ) {
			define( 'EDD_SLG_TW_CONSUMER_SECRET', $tw_consumer_secrets );
		}
		
		//yahoo variable initialization
		$yh_consumer_key = isset( $edd_options['edd_slg_yh_consumer_key'] ) ? $edd_options['edd_slg_yh_consumer_key'] : '';
		$yh_consumer_secret = isset( $edd_options['edd_slg_yh_consumer_secret'] ) ? $edd_options['edd_slg_yh_consumer_secret'] : '';
		$yh_app_id = isset( $edd_options['edd_slg_yh_app_id'] ) ? $edd_options['edd_slg_yh_app_id'] : '';
		
		if( !defined( 'EDD_SLG_YH_CONSUMER_KEY' ) ){
			define( 'EDD_SLG_YH_CONSUMER_KEY', $yh_consumer_key );
		}
		if( !defined( 'EDD_SLG_YH_CONSUMER_SECRET' ) ){
			define( 'EDD_SLG_YH_CONSUMER_SECRET', $yh_consumer_secret );
		}
		if( !defined( 'EDD_SLG_YH_APP_ID' ) ){
			define( 'EDD_SLG_YH_APP_ID', $yh_app_id );
		}
		if( !defined( 'EDD_SLG_YH_REDIRECT_URL' ) ) {
			$yahoourl = add_query_arg( 'eddslg', 'yahoo', site_url() );
			define( 'EDD_SLG_YH_REDIRECT_URL', $yahoourl );
		}
		
		//foursquare variable initialization
		$fs_client_id = isset( $edd_options['edd_slg_fs_client_id'] ) ? $edd_options['edd_slg_fs_client_id'] : '';
		$fs_client_secrets = isset( $edd_options['edd_slg_fs_client_secret'] ) ? $edd_options['edd_slg_fs_client_secret'] : '';
		
		if( !defined( 'EDD_SLG_FS_CLIENT_ID' ) ) {
			define( 'EDD_SLG_FS_CLIENT_ID', $fs_client_id );
		}
		if( !defined( 'EDD_SLG_FS_CLIENT_SECRET' ) ) {
			define( 'EDD_SLG_FS_CLIENT_SECRET', $fs_client_secrets );
		}
		if( !defined( 'EDD_SLG_FS_REDIRECT_URL' ) ) {
			$fsredirecturl = add_query_arg( 'eddslg', 'foursquare', site_url() );
			define( 'EDD_SLG_FS_REDIRECT_URL', $fsredirecturl );
		}
		
		//windows live variable initialization
		$wl_client_id = isset( $edd_options['edd_slg_wl_client_id'] ) ? $edd_options['edd_slg_wl_client_id'] : '';
		$wl_client_secrets = isset( $edd_options['edd_slg_wl_client_secret'] ) ? $edd_options['edd_slg_wl_client_secret'] : '';
		
		if( !defined( 'EDD_SLG_WL_CLIENT_ID' ) ) {
			define( 'EDD_SLG_WL_CLIENT_ID', $wl_client_id );
		}
		if( !defined( 'EDD_SLG_WL_CLIENT_SECRET' ) ) {
			define( 'EDD_SLG_WL_CLIENT_SECRET', $wl_client_secrets );
		}
		if( !defined( 'EDD_SLG_WL_REDIRECT_URL' ) ) {
			$wlredirecturl = add_query_arg( 'eddslg', 'windowslive', site_url() );
			define( 'EDD_SLG_WL_REDIRECT_URL', $wlredirecturl );
		}
		
		//vk variable initialization
		$vk_client_id = isset( $edd_options['edd_slg_vk_app_id'] ) ? $edd_options['edd_slg_vk_app_id'] : '';
		$vk_client_secrets = isset( $edd_options['edd_slg_vk_app_secret'] ) ? $edd_options['edd_slg_vk_app_secret'] : '';
		
		if( !defined( 'EDD_SLG_VK_APP_ID' ) ) {
			define( 'EDD_SLG_VK_APP_ID', $vk_client_id );
		}
		if( !defined( 'EDD_SLG_VK_APP_SECRET' ) ) {
			define( 'EDD_SLG_VK_APP_SECRET', $vk_client_secrets );
		}
		if( !defined( 'EDD_SLG_VK_REDIRECT_URL' ) ) {
			$vkredirecturl = add_query_arg( 'eddslg', 'vk', site_url() );
			define( 'EDD_SLG_VK_REDIRECT_URL', $vkredirecturl );
		}
		
		if( !defined( 'EDD_SLG_VK_LINK' ) ) {       //  define vk variable for link
			$vk_link = 'https://vk.com';
			define( 'EDD_SLG_VK_LINK', $vk_link );
		}
	}
	
	/**
	 * Checkout Page URL
	 * 
	 * Handles to return checkout page url
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_send_on_checkout_page( $queryarg = array() ) {
		
		global $edd_options;
		
		$sendcheckout = get_permalink($edd_options['purchase_page']);
	
		$sendcheckouturl = add_query_arg( $queryarg, $sendcheckout );
		
		wp_redirect( apply_filters( 'edd_slg_checkout_page_redirect', $sendcheckouturl, $queryarg ) );
		exit;
	}
	/**
	 * Check Any One Social Media
	 * Login is enable or not
	 * 
	 * Handles to Check any one social 
	 * media login is enable or not
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_check_social_enable() {
		
		global $edd_options;
		
		$return = false;
		//check if any social is activated or not
		if( !empty( $edd_options['edd_slg_enable_facebook'] ) || !empty( $edd_options['edd_slg_enable_googleplus'] ) || 
			!empty( $edd_options['edd_slg_enable_linkedin'] ) || !empty( $edd_options['edd_slg_enable_twitter'] ) || 
			!empty( $edd_options['edd_slg_enable_yahoo'] ) 	  || !empty( $edd_options['edd_slg_enable_windowslive'] ) || 
			!empty( $edd_options['edd_slg_enable_vk'] ) ) {
				
			$return = true;
		}
		return $return;
	}
	
	/**
	 * Google Redirect URL
	 * 
	 * Handle to display google redirect url description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_gp_redirect_url_callback( $args ) {
		
		echo '<code><strong>' . EDD_SLG_GP_REDIRECT_URL . '</strong></code>';
	}
	
	
	/**
	 * Yahoo Redirect URL
	 * 
	 * Handle to display yahoo redirect url description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_yh_redirect_url_callback( $args ) {
		
		echo '<code><strong>' . EDD_SLG_YH_REDIRECT_URL . '</strong></code>';
	}
	
	/**
	 * Windows Live Redirect URL
	 * 
	 * Handle to display windows live redirect url description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_wl_redirect_url_callback( $args ) {
		
		echo '<code><strong>' . site_url() . '</strong></code>';
	}

	/**
	 * VK Redirect URL
	 * 
	 * Handle to display vk redirect url description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.3.0
	 * 
	 */
	function edd_vk_redirect_url_callback( $args ) {
		
		echo '<code><strong>' . EDD_SLG_VK_REDIRECT_URL . '</strong></code>';
	}
	
	/**
	 * Facebook Description
	 * 
	 * Handle to display facebook description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_facebook_desc_callback( $args ) {
		
		?>
			<p>
				<?php printf( __( 'Before you can start using Facebook for the social login, you need to create a Facebook Application. You can get a step by step tutorial on how to create Facebook Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/facebook/" target="_blank">', '</a>' ); ?>
			</p>
			
		<?php
	}
	
	/**
	 * Google+ Description
	 * 
	 * Handle to display google+ description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_googleplus_desc_callback( $args ) {
		
	?>
		<p>
			<?php printf( __( 'Before you can start using Google+ for the social login, you need to create a Google+ Application. You can get a step by step tutorial on how to create Google+ Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/google/" target="_blank">', '</a>' ); ?>
		</p>
	<?php	
	}
		
	/**
	 * LinkedIn Description
	 * 
	 * Handle to display linkedin description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_linkedin_desc_callback( $args ) {
		
		?>
			<p>
				<?php printf( __( 'Before you can start using LinkedIn for the social login, you need to create a LinkedIn Application. You can get a step by step tutorial on how to create LinkedIn Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/linkedin/" target="_blank">', '</a>' ); ?>
			</p>
		<?php
	}
		
	/**
	 * Twitter Description
	 * 
	 * Handle to display twitter description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_twitter_desc_callback( $args ) {
		
		?>
			<p>
				<?php printf( __( 'Before you can start using Twitter for the social login, you need to create a Twitter Application. You can get a step by step tutorial on how to create Twitter Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/twitter/" target="_blank">', '</a>' ); ?>
			</p>
		<?php
	}
		
	/**
	 * Yahoo Description
	 * 
	 * Handle to display yahoo description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_yahoo_desc_callback( $args ) {
		?>
			<p>
				<?php printf( __( 'Before you can start using Yahoo for the social login, you need to create a Yahoo Application. You can get a step by step tutorial on how to create Yahoo Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/yahoo/" target="_blank">', '</a>' ); ?>
			</p>
		<?php
	}
	
	/**
	 * Foursquare Description
	 * 
	 * Handle to display foursquare description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_foursquare_desc_callback( $args ) {
		
	?>
		<p>
			<?php printf( __( 'Before you can start using Foursquare for the social login, you need to create a Foursquare Application. You can get a step by step tutorial on how to create Foursquare Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/foursquare/" target="_blank">', '</a>' ); ?>
		</p>
		
	<?php
		
	}
	
	/**
	 * Windows Live Description
	 * 
	 * Handle to display windowslive description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 * 
	 */
	function edd_windowslive_desc_callback( $args ) {
		
	?>
		<p>
			<?php printf( __( 'Before you can start using Windows Live for the social login, you need to create a Windows Live Application. You can get a step by step tutorial on how to create Windows Live Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/windows_live/" target="_blank">', '</a>' ); ?>
		</p>
		
	<?php
		
	}

	/**
	 * VK Description
	 * 
	 * Handle to display vk description in settings
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.3.0
	 * 
	 */
	function edd_vk_desc_callback( $args ) {
		
		?>
			<p>
				<?php printf( __( 'Before you can start using VK for the social login, you need to create a VK Application. You can get a step by step tutorial on how to create VK Application on our %sDocumentation%s.' , 'eddslg') , '<a href="http://wpweb.co.in/documents/social-network-integration/vk/" target="_blank">', '</a>' ); ?>
			</p>
			
		<?php
	}
	
	/**
	 * Current Page URL
	 *
	 * @package  Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	function edd_slg_get_current_page_url(){
		
		$curent_page_url = remove_query_arg( array( 'oauth_token', 'oauth_verifier' ), edd_get_current_page_url() );
		return $curent_page_url;
	}
	
?>