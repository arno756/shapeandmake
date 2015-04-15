<?php
/**
 * User functions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Create a token for logged out users
 *
 * @since 1.0
 * @param int $list_id list ID
*/
function edd_wl_create_token( $list_id = '' ) {
	// if user is not logged in, we check to see if they already have a token.
	// If not, we create a token and store it as a cookie
	// Each list that is created will have this token stored with it
	if ( ! is_user_logged_in() ) {
		$token = edd_wl_get_list_token();

		if ( $token ) {
			update_post_meta( $list_id, 'edd_wl_token', $token );
		}
		else {
			// append their first list id to the front of the timestamp. The list ID auto increments and will always be unique
			$token 		= $list_id . time();
			$token_expire 	= apply_filters( 'edd_wl_token_expire', time()+3600*24*30 );

			$cookie = setcookie( 'edd_wl_token', $token, $token_expire, COOKIEPATH, COOKIE_DOMAIN );
			// store edd_wl_token against list with the same time stamp
			// we'll use this to verify that this belongs to the user
			update_post_meta( $list_id, 'edd_wl_token', $token );

		}

		return $token;
	}

}


/**
 * Check if the list belongs to the current user.
 *
 * @return  boolean true if list belongs to current user, false otherwise
 * @since 1.0
*/
function edd_wl_is_users_list( $list_id ) {

	$list = get_post( $list_id );
	$current_user_id = get_current_user_id();

	// should only be on view or edit page
	if ( ! ( get_query_var('wl_edit') || get_query_var('wl_view') ) )
		return;

	// logged in users
	if ( is_user_logged_in() && (int) $list->post_author === $current_user_id ) {
		return true;
	}
	// non logged in users
	elseif ( ! is_user_logged_in() ) {
		// get token
		$token = edd_wl_get_list_token();
		$token = isset( $token ) ? $token : '';

		if ( $token ) {
			// get custom meta of post
			$current_list_meta = get_post_custom( $list_id );
			$current_list_token = isset ( $current_list_meta['edd_wl_token'][0] ) ? $current_list_meta['edd_wl_token'][0] : '';
			// if token matches the edd_wl_token then this list belongs to user
			if ( $token === $current_list_token ) {
				return true;
			}
		}
	}

	return false;
}



/**
 * Retrieve a saved wish list token. Used in validating wish list
 * 
 * @since 1.0
 * @return int
 */
function edd_wl_get_list_token() {
	if( ! is_user_logged_in() ) {
		$token = isset( $_COOKIE['edd_wl_token'] ) ? $_COOKIE['edd_wl_token'] : null;

		return apply_filters( 'edd_wl_get_list_token', $token );
	}
		
	return null;
}

/**
 * Get lists by specific user (author)
 *
 * @since 1.0
*/
function edd_wl_get_guest_lists( $token ) {
	
	$args = array(
	    'post_type'			=> 'edd_wish_list',
	    'posts_per_page' 	=> -1,
	    'post_status'		=> array( 'publish', 'private' ),
	    'meta_key'			=> 'edd_wl_token',
	    'meta_value'		=> $token
	);

	$lists = get_posts( $args );

	if ( $lists )
		return $lists;

	return null;
}

/**
 * When user registers and has guest lists, remove token meta key so their lists are saved indefinately
 *
 * @since 1.0
 * @param  int $user_id newly created user id
 * @return void
 */
function edd_wl_new_user_registration( $user_id ) {
	// get user's token if present
	$lists = edd_wl_get_guest_lists( edd_wl_get_list_token() );

	// attribute posts to new author
	if ( $lists ) {
		
		// loop throgh each list and assign the new user ID to their list
		foreach ( $lists as $key => $list ) {
			$args = array(
				'ID'          => $list->ID,
				'post_author' => $user_id
			);
			wp_update_post( $args );

			// delete token one each list
			delete_post_meta( $list->ID, 'edd_wl_token', edd_wl_get_list_token() );
		}

		// remove cookie
		setcookie( 'edd_wl_token', '', time()-3600, COOKIEPATH, COOKIE_DOMAIN );
	}
}
add_action( 'user_register', 'edd_wl_new_user_registration', 10, 1 );
add_action( 'wpmu_new_user', 'edd_wl_new_user_registration', 10, 1 );