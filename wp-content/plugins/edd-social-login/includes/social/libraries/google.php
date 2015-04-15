<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Google Class
 *
 * Handles all google functions 
 *
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
if( !class_exists( 'EDD_Slg_Social_Google' ) ) {
	
	class EDD_Slg_Social_Google {
		
		var $google, $googleplus, $googleoauth2, $_google_user_cache;
		
		public function __construct(){
			
		}
		
		/**
		 * Include google Class
		 * 
		 * Handles to load google class
		 * 
		 * @package Easy Digital Downloads - Social Login
	 	 * @since 1.0.0
		 */
		public function edd_slg_load_google() {
			
			global $edd_options;
			
			//google class declaration
			if( !empty( $edd_options['edd_slg_enable_googleplus'] ) 
				&& !empty( $edd_options['edd_slg_gp_client_id'] ) && !empty( $edd_options['edd_slg_gp_client_secret'] ) ) {
			 
				if( !class_exists( 'apiClient' ) ) { // loads the Google class
					require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/google/src/apiClient.php' ); 
				}
				if( !class_exists( 'apiPlusService' ) ) { // Loads the google plus service class for user data
					require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/google/src/contrib/apiPlusService.php' ); 
				}
				if( !class_exists( 'apiOauth2Service' ) ) { // loads the google plus service class for user email
					require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/google/src/contrib/apiOauth2Service.php' ); 
				}
				
				// Google Objects
				$this->google = new apiClient();
				$this->google->setApplicationName( "Google+ PHP Starter Application" );
				$this->google->setClientId( EDD_SLG_GP_CLIENT_ID );
				$this->google->setClientSecret( EDD_SLG_GP_CLIENT_SECRET );
				$this->google->setRedirectUri( EDD_SLG_GP_REDIRECT_URL );
				$this->google->setScopes( array( 'https://www.googleapis.com/auth/plus.me','https://www.googleapis.com/auth/userinfo.email' ) );
				
				$this->googleplus = new apiPlusService( $this->google ); // For getting user detail from google
				$this->googleoauth2 = new apiOauth2Service( $this->google ); // For gettting user email from google
				
				return true;
									  
			} else {
		
				return false;
			}	
		}
		
		/**
		 * Initialize API
		 * 
		 * Getting Initializes Google Plus API
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_initialize_google() {
		
			global $edd_options;
			
			//Google integration begins here 		
			// not isset state condition required, else google code executed when google called 
			//and check eddslg is equal to google
			if( isset( $_GET['code'] ) && !isset( $_GET['state'] ) 
				&& isset( $_GET['eddslg'] ) && $_GET['eddslg'] == 'google' ) {
			
				//load google class
				$google = $this->edd_slg_load_google();
				
				//check google class is loaded
				if( !$google ) return false;
				
				//Get access tocken
				$gplus_access_token = $this->google->getAccessToken();
				
				if( empty( $gplus_access_token ) ) {//If empty access tocken
					
					//generate new access token
					$this->google->authenticate();
					$gplus_access_token = $this->google->getAccessToken();
				}
				
				//check access token is set or not
				if ( !empty( $gplus_access_token ) ) {
				
					$userdata = $this->googleplus->people->get('me');
					$useremail = $this->googleoauth2->userinfo->get(); // to get email
					
					$userdata['email'] = $useremail['email'];
					EDD()->session->set( 'edd_slg_google_user_cache', $userdata );
				}
			
			}
		}
		
		/**
		 * Get Google User Data
		 * 
		 * Getting all the google+ connected user data
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		
		public function edd_slg_get_google_user_data() {
			
			$user_profile_data = '';
			
			$user_profile_data = EDD()->session->get( 'edd_slg_google_user_cache' );
			
			return $user_profile_data;
		}
		
		/**
		 * Get Google Authorize URL
		 * 
		 * Getting Authentication URL connect with google+
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		
		public function edd_slg_get_google_auth_url() {
		
			//load google class
			$google = $this->edd_slg_load_google();
			
			//check google class is loaded
			if( !$google ) return false;
		
			$url = $this->google->createAuthUrl();
			$authurl = isset( $url ) ? $url : '';
			
			return $authurl;
		}
		
	}
}
?>