<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Linkedin Class
 *
 * Handles all Linkedin functions 
 *
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
if( !class_exists( 'EDD_Slg_Social_LinkedIn' ) ) {
	
	class EDD_Slg_Social_LinkedIn {
		
		var $linkedinconfig,$linkedin;
		
		public function __construct() {
			
		}
		
		/**
		 * Include LinkedIn Class
		 * 
		 * Handles to load Linkedin class
		 * 
		 * @package Easy Digital Downloads - Social Login
	 	 * @since 1.0.0
		 */
		public function edd_slg_load_linkedin() {
			
			global $edd_options;
		
			//linkedin declaration
			if( !empty( $edd_options['edd_slg_enable_linkedin'] )
				 && !empty( $edd_options['edd_slg_li_app_id'] ) && !empty( $edd_options['edd_slg_li_app_secret'] ) ) {

				if( !class_exists( 'LinkedIn' ) ) { // loads the LinkedIn class
					require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/linkedin/class-linkedin.php'); 
				}
				
				//linkedin api configuration
				$this->linkedinconfig = array(
												'appKey'       => EDD_SLG_LI_APP_ID,
												'appSecret'    => EDD_SLG_LI_APP_SECRET,
												'callbackUrl'  => NULL 
											 );
				$this->linkedin = new LinkedIn($this->linkedinconfig); 
				
				return true;
				
			} else {
				
				return false;	
			}
		}
		
		/**
		 * Linkedin Initialize
		 * 
		 * Handles LinkedIn Login Initialize
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 * 
		 */	
		public function edd_slg_initialize_linkedin() {
		
			global $edd_options;
		
			//check enable linkedin & linkedin application id & linkedin application secret are not empty
			if( !empty( $edd_options['edd_slg_enable_linkedin'] ) && !empty( $edd_options['edd_slg_li_app_id'] )
				 && !empty( $edd_options['edd_slg_li_app_secret'] ) ) {
				
			 	//check $_GET['eddslg'] equals to linkedin
				if( isset( $_GET['eddslg'] ) && $_GET['eddslg'] == 'linkedin' ) {
					
					//load linkedin class 
					$linkedin = $this->edd_slg_load_linkedin();
					
					//check linkedin class is exis or not
					if( !$linkedin ) return false;
					
					// code will excute when user does connect with linked in
					if( isset( $_REQUEST[LINKEDIN::_GET_TYPE] ) && $_REQUEST[LINKEDIN::_GET_TYPE] == 'initiate' ) { // if user allows access to linkedin and wpsocial is linkedin or not
						
						// check for the correct http protocol (i.e. is this script being served via http or https)
						$protocol = is_ssl() ? 'https' : 'http';
						
						// set the callback url
						$this->linkedinconfig['callbackUrl'] = $protocol . '://' . $_SERVER['SERVER_NAME'] . ( ( ( $_SERVER['SERVER_PORT'] != LINKEDIN_PORT_HTTP ) && ( $_SERVER['SERVER_PORT'] != LINKEDIN_PORT_HTTP_SSL ) ) ? ':' . $_SERVER['SERVER_PORT'] : '' ) . $_SERVER['PHP_SELF'] . '?eddslg=linkedin&' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';
						$this->linkedin = new LinkedIn( $this->linkedinconfig );
						  
						// check for response from LinkedIn
						$_GET[LINKEDIN::_GET_RESPONSE] = ( isset( $_GET[LINKEDIN::_GET_RESPONSE] ) ) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
					  
						if( !$_GET[LINKEDIN::_GET_RESPONSE] ) { // this code get executed when the user clicks on the button and linkedin does ask for permission 
							
							// LinkedIn hasn't sent us a response, the user is initiating the connection
							// send a request for a LinkedIn access token
							$response = $this->linkedin->retrieveTokenRequest();
							   
							if( $response['success'] === TRUE) {
								
								$linkedin_oauth['linkedin']['request'] = $response['linkedin'];
								
								// store the request token
								EDD()->session->set( 'edd_slg_linkedin_oauth', $linkedin_oauth );
								
									// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
									wp_redirect( LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);
									exit;
								} else {
									// bad token request
									echo __('Request token retrieval failed','eddslg');
								}		        
						  } else { //this code will execute when the user clicks on the allow access button on linkedin
						  		
						  		$linkedin_oauth = EDD()->session->get( 'edd_slg_linkedin_oauth' );
						  		
								// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
								$response = $this->linkedin->retrieveTokenAccess( $linkedin_oauth['linkedin']['request']['oauth_token'], $linkedin_oauth['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier'] );
							   
								if( $response['success'] === TRUE ) {
									
									// the request went through without an error, gather user's 'access' tokens
									$linkedin_oauth['linkedin']['access'] = $response['linkedin'];
									
									// set the user as authorized for future quick reference
									$linkedin_oauth['linkedin']['authorized'] = TRUE;
									
									EDD()->session->set( 'edd_slg_linkedin_oauth', $linkedin_oauth );
								   
									//get profile data
									$this->linkedin = new LinkedIn( $this->linkedinconfig );
									
									$linkedinOauth = EDD()->session->get( 'edd_slg_linkedin_oauth' );
									
									$this->linkedin->setTokenAccess( $linkedinOauth['linkedin']['access'] );
									$this->linkedin->setResponseFormat(LINKEDIN::_RESPONSE_XML);
									
									//add user data to session for further user
									$resultdata = array();
									$response = $this->linkedin->profile( '~:(id,first-name,last-name,picture-url,email-address,date-of-birth,public-profile-url)' );
									//convert xml object to simple array
							        $resultdata = json_decode( json_encode( ( array ) simplexml_load_string( $response['linkedin'] ) ), 1 );
							        
							        //set user data to sesssion for further use
							        EDD()->session->set( 'edd_slg_linkedin_user_cache', $resultdata );
									
									// redirect the user back to the demo page
									wp_redirect($_SERVER['PHP_SELF']);
									exit;
								} else {
									// bad token access
									echo __( 'Access token retrieval failed', 'eddslg' );
								}
						  } //end else
				  		
					} //end if $_REQUEST[LINKEDIN::_GET_TYPE] == 'initiate'
					
				} //end if to check $_GET['eddslg'] equals to linkedin
			}
			
		}
		
		/**
		 * Get LinkedIn Auth URL
		 * 
		 * Handles to return linkedin auth url
		 * 
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_linkedin_auth_url() {
			
			//load linkedin class 
			$linkedin = $this->edd_slg_load_linkedin();
			
			if( !$linkedin ) return false;
			
			$li_authurl = add_query_arg( array( 'eddslg' => 'linkedin', LINKEDIN::_GET_TYPE => 'initiate' ), get_permalink() );
			
			return $li_authurl;
		}
		
		/**
		 * Get LinkedIn User Data
		 *
		 * Function to get LinkedIn User Data
		 *
		 * @package Easy Digital Downloads - Social Login
		 * @since 1.0.0
		 */
		public function edd_slg_get_linkedin_user_data() {
		
			$user_profile_data = '';
			
			$user_profile_data = EDD()->session->get( 'edd_slg_linkedin_user_cache' );
			
			return $user_profile_data;
		}
		
	}
}
?>