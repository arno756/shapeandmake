<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Facebook Class
 *
 * Handles all facebook functions
 *
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
if( !class_exists( 'EDD_Slg_Social_Facebook' ) ) {
	
	class EDD_Slg_Social_Facebook {
		
		var $facebook;
		
		public function __construct() {
			
		}
		/**
		 * Include Facebook Class
		 * 
		 * Handles to load facebook class
		 * 
		 * @package Easy Digital Downloads - Social Login
	 	 * @since 1.0.0
		 */
		public function edd_slg_load_facebook() {
			
			global $edd_options;
			
			//check facebook is enable and application id and application secret is not empty			
			if( !empty( $edd_options['edd_slg_enable_facebook'] ) 
				&& !empty( $edd_options['edd_slg_fb_app_id'] ) && !empty($edd_options['edd_slg_fb_app_secret']) ) {
				
				if( !class_exists( 'Facebook' ) ) { // loads the facebook class
					require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/facebook/facebook.php' );
				}
	
				$this->facebook = new Facebook( array(
						'appId' => EDD_SLG_FB_APP_ID,
						'secret' => EDD_SLG_FB_APP_SECRET,
						'cookie' => true
				));
				
				return true;
				
			} else {
				
				return false;
			}
			
		}
		
		/**
		 * Get Facebook User
		 * 
		 * Handles to return facebook user id
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 * 
		 */
		public function edd_slg_get_fb_user() {
			
			//load facebook class
			$facebook = $this->edd_slg_load_facebook();
			
			//check facebook class is exis or not
			if( !$facebook ) return false;
			
			$user = $this->facebook->getUser();
			return $user;
			
		}
		
		/**
		 * Facebook User Data
		 *
		 * Getting the all the needed data of the connected Facebook user.
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_get_fb_userdata( $user ) {
		
			//load facebook class
			$facebook = $this->edd_slg_load_facebook();
			
			//check facebook class is exis or not
			if( !$facebook ) return false;
			
				$fb = array();
				$fb = $this->facebook->api( '/'.$user );
				$fb['picture'] = $this->edd_slg_fb_get_profile_picture( array( 'type' => 'square' ), $user );
				return  $fb;
		}
		
		/**
		 * Access Token
		 *
		 * Getting the access token from Facebook.
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_fb_getaccesstoken() {
		
			//load facebook class
			$facebook = $this->edd_slg_load_facebook();
			
			//check facebook class is exis or not
			if( !$facebook ) return false;
			
			return $this->facebook->getAccessToken();
		}
		
		/**
		 * Check Application Permission
		 *
		 * Handles to check facebook application
		 * permission is given by user or not
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_check_fb_app_permission( $perm="" ) {
			
			$data = '1';
			if( !empty( $perm ) ) {
				$userID = $this->edd_slg_get_fb_user();
				$accToken = $this->edd_slg_fb_getaccesstoken();
				$url = "https://api.facebook.com/method/users.hasAppPermission?ext_perm=$perm&uid=$userID&access_token=$accToken&format=json";
				$data = json_decode( $this->edd_slg_get_data_from_url( $url ) );			
			}
			return $data;
		}
		
		/**
		 * User Image
		 *
		 * Getting the the profile image of the connected Facebook user.
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_fb_get_profile_picture( $args=array(), $user ) {
			
			if( isset( $args['type'] ) && !empty( $args['type'] ) ) {
				$type = $args['type'];
			} else {
				$type = 'large';
			}
			$url = 'https://graph.facebook.com/' . $user . '/picture?type=' . $type;
			return $url;
		}
		
		/**
		 * Get Data From URL
		 *
		 * Handles to get data from url
		 * via CURL
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		
		public function edd_slg_get_data_from_url( $url ) {
			
			//Comment out the curl code
			/*$ch = curl_init();
			$timeout = 5;
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
			$data = curl_exec( $ch );
			curl_close( $ch );
			return $data;*/
			
			//Use wp_remote_post and wp_remote_get
			$data	= wp_remote_retrieve_body( wp_remote_get( $url ) );
			
			return $data;
		}
	}
}
?>