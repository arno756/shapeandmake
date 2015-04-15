<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Uninstall
 *
 * Does delete the created tables and all the plugin options
 * when uninstalling the plugin
 *
 * @package Easy Digital Downloads - Points and Rewards
 * @since 1.0.0
 */

// check if the plugin really gets uninstalled 
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();
	
	//delete custom main post data
	$queryargs = array( 'post_type' => 'eddpointslog', 'post_status' => 'any' , 'numberposts' => '-1' );
	$queryargsdata = get_posts( $queryargs );
	
	//delete all points log posts
	foreach ($queryargsdata as $post) {
		wp_delete_post($post->ID,true);
	}
	
	//get all user which meta key '_edd_userpoints' not equal to empty
	$all_user = get_users( array( 'meta_key' => '_edd_userpoints', 'meta_value'	=> '', 'meta_compare' => '!=' ) );
	
	foreach ( $all_user as $key => $value ){
		delete_user_meta( $value->ID, '_edd_userpoints' );
	}	
?>