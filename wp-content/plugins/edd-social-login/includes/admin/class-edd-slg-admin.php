<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 * 
 * Handles generic Admin functionality and AJAX requests.
 * 
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
class EDD_Slg_Admin {
	
	var $model, $scripts;
	
	public function __construct() {
		
		global $edd_slg_model, $edd_slg_scripts;
		
		$this->model	= $edd_slg_model;
		$this->scripts	= $edd_slg_scripts;
	}
	
	/**
	 * Register All need admin menu page
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function edd_slg_admin_menu_pages(){
		
		$edd_slg_social_login = add_submenu_page( 'edit.php?post_type=download', __( 'Easy Digital Download Social Login', 'eddslg' ), __( 'Social Login', 'eddslg' ), 'manage_shop_settings', 'edd-social-login', array( $this, 'edd_slg_social_login' ));
		//script for social login page
		
	}
	
	/**
	 * Add Social Login Page
	 * 
	 * Handles to load social login
	 * page to show social login register data
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function edd_slg_social_login() {
		
		include_once( EDD_SLG_ADMIN . '/forms/edd-social-login-data.php' );
		
	}
	
	/**
	 * Pop Up On Editor
	 * 
	 * Includes the pop up on the WordPress editor
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.1.1
	 */
	public function wps_deals_shortcode_popup() {
		
		include_once( EDD_SLG_ADMIN . '/forms/edd-slg-admin-popup.php' );
	}
	
	/**
	 * Validate Settings
	 * 
	 * Handles to validate settings
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.1.1
	 */
	function edd_slg_settings_validate( $input ) {
		
		// General Settings
		$input['edd_slg_login_heading'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_login_heading']) );
		$input['edd_slg_redirect_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_redirect_url']) );
		
		// Facebook Settings
		$input['edd_slg_fb_app_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fb_app_id']) );
		$input['edd_slg_fb_app_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fb_app_secret']) );
		$input['edd_slg_fb_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fb_icon_url']) );
		
		// Google+ Settings 
		$input['edd_slg_gp_client_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_gp_client_id']) );
		$input['edd_slg_gp_client_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_gp_client_secret']) );
		$input['edd_slg_gp_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_gp_icon_url']) );
		
		// LinkedIn Settings
		$input['edd_slg_li_app_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_li_app_id']) );
		$input['edd_slg_li_app_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_li_app_secret']) );
		$input['edd_slg_li_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_li_icon_url']) );
		
		// Twitter Settings
		$input['edd_slg_tw_consumer_key'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_tw_consumer_key']) );
		$input['edd_slg_tw_consumer_secret']= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_tw_consumer_secret']) );
		$input['edd_slg_tw_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_tw_icon_url']) );
		
		// Yahoo Settings
		$input['edd_slg_yh_consumer_key'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_yh_consumer_key']) );
		$input['edd_slg_yh_consumer_secret']= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_yh_consumer_secret']) );
		$input['edd_slg_yh_app_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_yh_app_id']) );
		$input['edd_slg_yh_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_yh_icon_url']) );
		
		// Foursquare Settings
		$input['edd_slg_fs_client_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fs_client_id']) );
		$input['edd_slg_fs_client_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fs_client_secret']) );
		$input['edd_slg_fs_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_fs_icon_url']) );
		
		// Windows Live Settings
		$input['edd_slg_wl_client_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_wl_client_id']) );
		$input['edd_slg_wl_client_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_wl_client_secret']) );
		$input['edd_slg_wl_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_wl_icon_url']) );
		
		// VK Settings
		$input['edd_slg_vk_app_id'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_vk_app_id']) );
		$input['edd_slg_vk_app_secret'] 	= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_vk_app_secret']) );
		$input['edd_slg_vk_icon_url'] 		= $this->model->edd_slg_escape_slashes_deep( trim($input['edd_slg_vk_icon_url']) );
		
		return $input;
	}
	
	/**
	 * Adding Hooks
	 * 
	 * @package Easy Digital Downloads - Social Login
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add admin menu pages
		add_action ( 'admin_menu', array($this,'edd_slg_admin_menu_pages' ));
		
		//add filter to add settings
		add_filter( 'edd_settings_extensions', array( $this->model , 'edd_slg_settings') );
		
		//add filter to add settings
		add_filter( 'edd_settings_extensions_sanitize', array( $this, 'edd_slg_settings_validate') );
		
		// mark up for popup
		add_action( 'admin_footer-post.php', array( $this,'wps_deals_shortcode_popup' ) );
		add_action( 'admin_footer-post-new.php', array( $this,'wps_deals_shortcode_popup' ) );
	}
}