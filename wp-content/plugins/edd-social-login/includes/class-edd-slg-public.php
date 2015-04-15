<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Pages Class
 *
 * Handles all the different features and functions
 * for the front end pages.
 *
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
class EDD_Slg_Public	{
	
	var $render,$model;
	var $socialfacebook;
	var $socialgoogle;
	var $sociallinkedin;
	var $socialtwitter;
	var $socialfoursquare;
	var $socialyahoo;
	var $socialwindowslive;
	var $socialvk;
	
	public function __construct() {
		
		global $edd_slg_render,$edd_slg_model,$edd_slg_social_facebook,
			$edd_slg_social_google,$edd_slg_social_linkedin,$edd_slg_social_twitter,
			$edd_slg_social_yahoo,$edd_slg_social_foursquare,$edd_slg_social_windowslive,$edd_slg_social_vk;
		
		$this->render = $edd_slg_render;
		$this->model = $edd_slg_model;
		
		//social class objects
		$this->socialfacebook 	= $edd_slg_social_facebook;
		$this->socialgoogle		= $edd_slg_social_google;
		$this->sociallinkedin 	= $edd_slg_social_linkedin;
		$this->socialtwitter 	= $edd_slg_social_twitter;
		$this->socialyahoo		= $edd_slg_social_yahoo;
		$this->socialfoursquare	= $edd_slg_social_foursquare;
		$this->socialwindowslive = $edd_slg_social_windowslive;
		$this->socialvk 		= $edd_slg_social_vk;
		
	}
	
	/**
	 * AJAX Call
	 * 
	 * Handles to Call ajax for register user
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function edd_slg_social_login() {
		
		global $edd_options;		
		
		$type = $_POST['type'];		
		
		$result = array();
		$data = array();
		$usercreated = 0;
		
		//created user who will connect via facebook
		if( $type == 'facebook' ) {
			
			$userid = $this->socialfacebook->edd_slg_get_fb_user();
			
			//if user id is null then return
			if( empty( $userid ) ) return;
			
			$userdata = $this->socialfacebook->edd_slg_get_fb_userdata( $userid );
			
			//check permission data user given to application
			$permData = $this->socialfacebook->edd_slg_check_fb_app_permission( 'publish_stream' );
		
			if( empty( $permData ) ) { //if user not give the permission to api and user type is facebook then it will redirected
				
				$result['redirect'] = '1';
				echo json_encode( $result );
				//do exit to get proper result
				exit;
			}
			
			//check facebook user data is not empty
			if( !empty( $userdata ) && isset( $userdata['email'] ) ) { //check isset user email from facebook
				
				$data['first_name'] = $userdata['first_name'];
				$data['last_name'] = $userdata['last_name'];
				$data['name'] = $userdata['name'];
				$data['email'] = $userdata['email'];
				$data['type'] = $type;
				$data['all'] = $userdata;
				$data['link'] = $userdata['link'];
				$data['id']	= $userdata['id'];
				
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
		} else if( $type == 'googleplus' ) {
			
			$gp_userdata = $this->socialgoogle->edd_slg_get_google_user_data();
			
			if( !empty( $gp_userdata ) ) {
				
				$data['first_name'] = $gp_userdata['name']['givenName'];
				$data['last_name'] = $gp_userdata['name']['familyName'];
				$data['name'] = $gp_userdata['displayName'];
				$data['email'] = $gp_userdata['email'];
				$data['type'] = $type;
				$data['all'] = $gp_userdata;
				$data['link'] = $gp_userdata['url'];
				$data['id']	= $gp_userdata['id'];
				
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
		} else if( $type == 'linkedin' ) {
			
			$li_userdata = $this->sociallinkedin->edd_slg_get_linkedin_user_data();
			
			if( !empty( $li_userdata ) ) {
				
				$data['first_name'] = $li_userdata['first-name'];
				$data['last_name'] = $li_userdata['last-name'];
				$data['name'] = $li_userdata['first-name'].' '.$li_userdata['last-name'];
				$data['email'] = $li_userdata['email-address'];
				$data['type'] = $type;
				$data['all'] = $li_userdata;
				$data['link'] = $li_userdata['public-profile-url'];
				$data['id']	= $li_userdata['id'];
				 
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
			
		} else if( $type == 'yahoo' ) {
			
			$yh_userdata = $this->socialyahoo->edd_slg_get_yahoo_user_data();
			
			if( !empty( $yh_userdata ) ) {
				
				$email		= '';
				$last_email	= '';
				
				if( isset( $yh_userdata->emails ) && !empty( $yh_userdata->emails ) && is_array( $yh_userdata->emails ) ) {
					foreach ( $yh_userdata->emails as $key => $value ) {
						$last_email	= isset( $value->handle ) ? $value->handle : '';
						if( isset($value->primary) && $value->primary ) {
							$email	= $value->handle;
						}
					}
					
					if( empty( $email ) ) {
						$email	= $last_email;
					}
				}
				
				$data['first_name'] = $yh_userdata->givenName;
				$data['last_name'] = $yh_userdata->familyName;
				$data['name'] = $yh_userdata->givenName.' '.$yh_userdata->familyName;
				$data['email'] = $email;
				$data['type'] = $type;
				$data['all'] = $yh_userdata;
				$data['link'] = $yh_userdata->profileUrl;
				$data['id']	= $yh_userdata->guid;
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
			
		} else if( $type == 'foursquare' ) { //check type is four squere
			
			$fs_userdata = $this->socialfoursquare->edd_slg_get_foursquare_user_data();
			
			if( !empty( $fs_userdata ) ) {
				
				$data['first_name'] = $fs_userdata->firstName;
				$data['last_name'] = $fs_userdata->lastName;
				$data['name'] = $fs_userdata->firstName.' '.$fs_userdata->lastName;
				$data['email'] = $fs_userdata->contact->email;
				$data['type'] = $type;
				$data['all'] = $fs_userdata;
				$data['link'] = 'https://foursquare.com/user/' . $fs_userdata->id;
				$data['id']	= $fs_userdata->id;
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
			
		} else if( $type == 'windowslive' ) { //check type is four squere
			
			$wl_userdata = $this->socialwindowslive->edd_slg_get_windowslive_user_data();
			
			//check windowslive user data is not empty
			if( !empty( $wl_userdata ) ) {
			
				$wlemail = isset( $wl_userdata->emails->preferred ) ? $wl_userdata->emails->preferred
							: $wl_userdata->emails->account;
				
				$data['first_name'] = $wl_userdata->first_name;
				$data['last_name'] = $wl_userdata->last_name;
				$data['name'] = $wl_userdata->name;
				$data['email'] = $wlemail;
				$data['type'] = $type;
				$data['all'] = $wl_userdata;
				$data['link'] = $wl_userdata->link;
				$data['id']	= $wl_userdata->id;
			
				//create user
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}
			
		} else if( $type == 'vk' ) { //check type is vk
									
			$vk_userdata = $this->socialvk->edd_slg_get_vk_user_data();
						
			if( !empty( $vk_userdata ) ) {						
				
				$data['first_name'] = $vk_userdata['first_name'];
				$data['last_name'] = $vk_userdata['last_name'];
				$data['name'] = $vk_userdata['first_name'] . ' ' . $vk_userdata['last_name'];
				$data['email'] = isset($vk_userdata['email']) ? $vk_userdata['email'] : '';  
				$data['type'] = $type;
				$data['all'] = $vk_userdata;
				$data['link'] = EDD_SLG_VK_LINK . '/' . $vk_userdata['screen_name'];
				$data['id']	= $vk_userdata['uid'];
				
				//create user				
				$usercreated = $this->edd_slg_add_user( $data );
				
				if( $usercreated ) {
					$result['success'] = '1';
				}
			}						
			
		}		
		
		//do action when user successfully created
		do_action( 'edd_slg_social_create_user_after', $type, $usercreated );
		
		echo json_encode( $result );
		//do exit to get proper result
		exit;
	}
	
	/**
	 * Add User
	 * 
	 * Handles to Add user to wordpress database
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function edd_slg_add_user( $userdata ) {
		
		// register a new WordPress user
		$wp_user_data = array();
		$wp_user_data['name']		= $userdata['name'];
		$wp_user_data['first_name']	= $userdata['first_name'];
		$wp_user_data['last_name']	= $userdata['last_name'];
		$wp_user_data['email']		= $userdata['email'];
		
		// added for vk.com
		$wp_user_data['id']			= $userdata['id'];
		$wp_user_data['type']		= $userdata['type'];
		
		$wp_id = $this->model->edd_slg_add_wp_user( $wp_user_data );
		
		if( $wp_id > 0 ) { //check wordpress user id is greater then zero
			
			update_user_meta( $wp_id, 'edd_slg_social_data', $userdata['all'] );
			update_user_meta( $wp_id, 'edd_slg_social_user_connect_via', $userdata['type'] );
			update_user_meta( $wp_id, 'edd_slg_social_identifier', $userdata['id'] );
			
			$wpuserdetails = array ( 	
										'ID'			=>	$wp_id, 
										'user_url'		=>	$userdata['link'], 
										'first_name'	=>  $userdata['first_name'],
										'last_name'		=>	$userdata['last_name'],
										'nickname'		=>	$userdata['name'],
										'user_url'		=>	$userdata['link'],
										'display_name'	=>	$userdata['name']
									);
			
			wp_update_user( $wpuserdetails );
			
			//make user logged in
			wp_set_auth_cookie( $wp_id, false );
			return $wp_id;
		
		}
		return false;
	}
	
	/**
	 * Load Login Page For Social
	 * 
	 * Handles to load login page for social
	 * when no email address found
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function edd_slg_social_login_redirect() {
		
		global $edd_options;
		
		$socialtype = isset( $_GET['eddslgnetwork'] ) ? $_GET['eddslgnetwork'] : '';
		
		//get all social networks
		$allsocialtypes = edd_slg_social_networks();
		
		if( !is_user_logged_in() && isset( $_GET['edd_slg_social_login'] ) 
			&& !empty( $socialtype ) && array_key_exists( $socialtype, $allsocialtypes ) ) {
		
			// get redirect url from shortcode 
			$stcd_redirect_url = EDD()->session->get( 'edd_slg_stcd_redirect_url' );
			
			//check button clicked from widget then redirect to widget page url
			if( isset( $_GET['container'] ) && $_GET['container'] == 'widget' ) {
				
				// get redirect url from widget 
				$stcd_redirect_url = EDD()->session->get( 'edd_slg_stcd_redirect_url_widget' );
			}
			$redirect_url = !empty( $stcd_redirect_url ) ? $stcd_redirect_url : edd_slg_get_current_page_url();
			
			$data = array();
			
			//wordpress error class		
			$errors = new WP_Error();
			  		
	  		switch ( $socialtype ) {
	  			
	  			case 'twitter'	:
							//get twitter user data
							$tw_userdata = $this->socialtwitter->edd_slg_get_twitter_user_data();
							
							//check user id is set or not for twitter
							if( !empty( $tw_userdata ) && isset( $tw_userdata->id ) && !empty( $tw_userdata->id ) ) {
								
								$data['first_name'] = $tw_userdata->name;
								$data['last_name'] = '';
								$data['name'] = $tw_userdata->screen_name; //display name of user
								$data['type'] = 'twitter';
								$data['all'] = $tw_userdata;
								$data['link'] = 'https://twitter.com/' . $tw_userdata->screen_name;
								$data['id']	= $tw_userdata->id;
							}
							break;
	  			
	  		}
			
	  		//if cart is empty or user is not logged in social media
	  		//and accessing the url then send back user to checkout page
	  		if( !isset( $data['id'] ) || empty( $data['id'] ) ) {
	  			
	  			/*if( isset( $_GET['page_id'] ) && !empty( $_GET['page_id'] ) ) {
					$redirect_url = get_permalink( $_GET['page_id'] );
				} else {
					$redirect_url = home_url();
				}*/
	  			if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] ) ) {
	  				unset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] );
	  			}
				if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] ) ) {
					unset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] );
				}
				wp_redirect( $redirect_url );
				exit;
	  			//send user to checkout page
				//edd_slg_send_on_checkout_page();
				
	  		}
	  		
			//when user will click submit button of custom login
			//check user clicks submit button of registration page and get parameter should be valid param
			if( ( isset( $_POST['edd-slg-submit'] ) && !empty( $_POST['edd-slg-submit'] ) 
					&& $_POST['edd-slg-submit'] == __( 'Register', 'eddslg' ) ) ) {  
				
				$loginurl = wp_login_url();
					
				if( isset( $_POST['edd_slg_social_email'] ) ) { //check email is set or not
				  
					$socialemail = $_POST['edd_slg_social_email'];
				  
					  if ( empty( $socialemail ) ) { //if email is empty
						$errors->add( 'empty_email', '<strong>'.__( 'ERROR', 'eddslg').' :</strong> '.__( 'Enter your email address.', 'eddslg' ) );
					  } elseif ( !is_email( $socialemail ) ) { //if email is not valid
						$errors->add( 'invalid_email', '<strong>'.__( 'ERROR', 'eddslg').' :</strong> '.__('The email address did not validate.', 'eddslg' ) );
						$socialemail = '';
					  } elseif ( email_exists( $socialemail ) ) {//if email is exist or not
					  	
						$errors->add('email_exists', '<strong>'.__( 'ERROR', 'eddslg').' :</strong> '.__('Email already exists, If you have an account login first.', 'eddslg' ) );
					  }
					  
					if ( $errors->get_error_code() == '' ) { //
					  	
			  		 	if( !empty( $data ) ) { //check user data is not empty
							
			  		 		$data['email'] = $socialemail;
			  		 		
			  		 		//create user
							$usercreated = $this->edd_slg_add_user( $data );
							
				  			if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] ) ) {
				  				unset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] );
				  			}
							if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] ) ) {
								unset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] );
							}
							wp_redirect( $redirect_url );
							exit;
							//send user to checkout page
							//edd_slg_send_on_checkout_page();
							
						} 
				  	}
			  	}
			}
			
			//redirect user to custom registration form
			if( isset( $_GET['edd_slg_social_login'] ) && !empty( $_GET['edd_slg_social_login'] ) ) {
			
				//login call back url after registration
				/*$callbackurl = wp_login_url();
				$callbackurl = add_query_arg('edd_slg_social_login_done', 1, $callbackurl);*/
				$socialemail = isset( $_POST['edd_slg_social_email'] ) ? $_POST['edd_slg_social_email'] : '';
			
		  		//check the user who is going to connect with site
				//it is alreay exist with same data or not 
				//if user is exist then simply make that user logged in
				$metaquery = array(
									array( 
											'key'	=>	'edd_slg_social_user_connect_via', 
											'value'	=>	$data['type'] 
										),
									array( 
											'key'	=>	'edd_slg_social_identifier', 
											'value'	=>	$data['id']
										)
								);
								
				$getusers = get_users( array( 'meta_query' => $metaquery ) );
				$wpuser = array_shift( $getusers ); //getting users 
				
				//check user is exist or not conected with same metabox
				if( !empty( $wpuser ) ) {
					
					//make user logged in
					wp_set_auth_cookie( $wpuser->ID, false );
					
		  			if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] ) ) {
		  				unset( $_SESSION['edd']['edd_slg_stcd_redirect_url_widget'] );
		  			}
					if( isset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] ) ) {
						unset( $_SESSION['edd']['edd_slg_stcd_redirect_url'] );
					}
					wp_redirect( $redirect_url );
					exit;
					//send user to checkout page
					//edd_slg_send_on_checkout_page();
					
				} else {
					
					//if user is not exist then show register user form
					
					login_header(__('Registration Form', 'eddslg') , '<p class="message register">' . __('Please enter your email address to complete registration.', 'eddslg' ) . '</p>', $errors );
					
					?>
						<form name="registerform" id="registerform" action="" method="post">
							  <p>
								  <label for="wcsl_email"><?php _e( 'E-mail', 'eddslg' ); ?><br />
								  <input type="text" name="edd_slg_social_email" id="edd_slg_social_email" class="input" value="<?php  echo $socialemail ?>" size="25" tabindex="20" /></label>
							  </p>
							  <p id="reg_passmail">
							  	<?php _e( 'Username and Password will be sent to your email.', 'eddslg' ); ?>
							  </p>
							  <br class="clear" />
							  <p class="submit"><input type="submit" name="edd-slg-submit" id="edd-slg-submit" class="button-primary" value="<?php _e( 'Register', 'eddslg' ); ?>" tabindex="100" /></p>
						</form>
					<?php
					
					login_footer('user_login');
					exit;
				}
			}
		}
	}
	
	/**
	 * Handles to change avatar image if user is connected via social service
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.4
	 */
	function edd_slg_get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
		
		$user_id = false;
		
		if ( is_numeric( $id_or_email ) ) { // If user id is there
			
			$user_id = $id_or_email;
			
		} elseif ( is_object( $id_or_email ) ) { // If data is from comment then take user id
			
			if ( !empty( $id_or_email->user_id ) ) {
				$user_id = $id_or_email->user_id;
			}
			
		} else {
			$user 		= get_user_by( 'email', $id_or_email );
			$user_id	= isset($user->ID) ? $user->ID : '';
		}
		
		// Getting profile pic
		$avatar_pic = $this->model->edd_slg_get_user_profile_pic( $user_id );
		
		if( !empty($avatar_pic) ) {
			$avatar = '<img width="'.$size.'" height="'.$size.'" class="avatar avatar-'.$size.'" src="'.$avatar_pic.'" alt="" />';
		}
		
		return $avatar;
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding proper hoocks for the public pages.
	 *
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//check is there any social media is enable or not
		if( edd_slg_check_social_enable() ){
			
			$edd_social_order = get_option( 'edd_social_order' );
			
			//Initializes Google Plus API
			add_action( 'init', array( $this->socialgoogle, 'edd_slg_initialize_google' ) );
			
			// add action for linkedin login
			add_action( 'init', array( $this->sociallinkedin, 'edd_slg_initialize_linkedin' ) );
			
			// add action for twitter login
			add_action( 'init', array( $this->socialtwitter, 'edd_slg_initialize_twitter' ) );
			
			// add action for yahoo login
			add_action( 'init', array( $this->socialyahoo, 'edd_slg_initialize_yahoo' ) );
			
			// add action for foursquare login
			add_action( 'init', array( $this->socialfoursquare, 'edd_slg_initialize_foursquare' ) );
			
			//add action for windows live login
			add_action( 'init', array( $this->socialwindowslive, 'edd_slg_initialize_windowslive' ) );
			
			// add action for vk login
			add_action( 'init', array( $this->socialvk, 'edd_slg_initialize_vk' ) );
									
			//add action to add social login button to before payment gateways
			add_action( 'edd_checkout_form_top', array( $this->render, 'edd_slg_social_login_buttons' ) );
			
			//add action to load login page
			add_action( 'login_init', array( $this, 'edd_slg_social_login_redirect' ) );
			
			if( !empty( $edd_social_order ) ) {
				$priority = 5;
				foreach ( $edd_social_order as $social ) {
					add_action( 'edd_slg_checkout_social_login', array( $this->render, 'edd_slg_login_'.$social ), $priority );
					$priority += 5;
				}
			}
		}
		
		// Filter to change the avatar image
		add_filter( 'get_avatar', array( $this, 'edd_slg_get_avatar' ), 10, 5 );
		
		//AJAX Call to Login Via Social Media
		add_action( 'wp_ajax_edd_slg_social_login', array( $this, 'edd_slg_social_login' ) );
		add_action( 'wp_ajax_nopriv_edd_slg_social_login', array( $this, 'edd_slg_social_login' ) );
		
		
	}
}
?>