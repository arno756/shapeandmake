<?php
/**
 * Ajax functions
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds item to the cart from the wish list via AJAX. Based off edd_ajax_add_to_cart()
 *
 * @since 1.0
 * @return void
 */
function edd_ajax_add_to_cart_from_wish_list() {
	if ( isset( $_POST['download_id'] ) ) {
		global $post;

		$to_add = array();

		if ( isset( $_POST['price_ids'] ) && is_array( $_POST['price_ids'] ) ) {
			foreach ( $_POST['price_ids'] as $price ) {
				$to_add[] = array( 'price_id' => $price ); 
			}
		}

		foreach ( $to_add as $options ) {

			if( $_POST['download_id'] == $options['price_id'] )
				$options = array();

			// call EDD's edd_add_to_cart function
			$key = edd_add_to_cart( $_POST['download_id'], $options );

			$item = array(
				'id'      => $_POST['download_id'],
				'options' => $options
			);

			$item = apply_filters( 'edd_wl_ajax_pre_cart_item_template', $item );

			$return = array(
				'add_to_cart'  => 'Added to Cart',
				'subtotal'  => html_entity_decode( edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
				'cart_item' => html_entity_decode( edd_get_cart_item_template( $key, $item, true ), ENT_COMPAT, 'UTF-8' )
			);

			echo json_encode( $return );

		}
	}
	edd_die();
}
add_action( 'wp_ajax_edd_add_to_cart_from_wish_list', 'edd_ajax_add_to_cart_from_wish_list' );
add_action( 'wp_ajax_nopriv_edd_add_to_cart_from_wish_list', 'edd_ajax_add_to_cart_from_wish_list' );

/**
 * Removes item from cart via AJAX. Based off edd_ajax_remove_from_cart()
 *
 * @since 1.0
 * @return void
 */
function edd_ajax_remove_from_wish_list() {
	if ( isset( $_POST['cart_item'] ) ) {
		
		edd_remove_from_wish_list( $_POST['cart_item'], $_POST['list_id'] );
		
		$list = get_post_meta( $_POST['list_id'], 'edd_wish_list', true );
		
		$return = array(
			'removed'  => true,
		);

		// list is empty
		if ( ! $list ) {
			$messages = edd_wl_messages();
			edd_wl_set_message( 'list_empty', $messages['no_downloads'] );
			$return['message'] = html_entity_decode( edd_wl_print_messages(), ENT_COMPAT, 'UTF-8' );
		}

		echo json_encode( $return );

	}
	edd_die();
}
add_action( 'wp_ajax_edd_remove_from_wish_list', 'edd_ajax_remove_from_wish_list' );
add_action( 'wp_ajax_nopriv_edd_remove_from_wish_list', 'edd_ajax_remove_from_wish_list' );

/**
 * Delete Wish List
 *
 * @since 1.0
*/
function edd_wl_delete_list() {
	check_ajax_referer( 'edd_wl_ajax_nonce', 'nonce' );

	if ( ! isset( $_POST['post_id'] ) )
		return;

    $list_id = intval( $_POST['post_id'] );

    if ( wp_delete_post( $list_id ) === false ) {
        $return['msg'] = 'failed';
    } else {
    	$messages = edd_wl_messages();
    	edd_wl_set_message( 'list_deleted', $messages['list_deleted'] );
        $return['msg'] = 'success';
    }

	echo json_encode( $return );
	edd_die();
}
add_action( 'wp_ajax_edd_wl_delete_list', 'edd_wl_delete_list' );
add_action( 'wp_ajax_nopriv_edd_wl_delete_list', 'edd_wl_delete_list' );


/**
 * Adds item to the selected wish list, or creates a new list, via AJAX. Based off edd_ajax_add_to_cart()
 *
 * @since 1.0
 * @return void
 */
function edd_ajax_add_to_wish_list() {

	if ( isset( $_POST['download_id'] ) ) {
		global $post;

		$to_add = array();

		if ( isset( $_POST['price_ids'] ) && is_array( $_POST['price_ids'] ) ) {
			foreach ( $_POST['price_ids'] as $price ) {
				$to_add[] = array( 'price_id' => $price );
			}
		}

		// create a new list
		$create_list = isset( $_POST['new_or_existing'] ) && 'new-list' == $_POST['new_or_existing'] ? true : false;

		// the new list name being created. Fallback for blank list names
		$list_name = isset( $_POST['list_name'] ) && ! empty( $_POST['list_name'] ) ? $_POST['list_name'] : __( 'My list', 'edd-wish-lists' ); 

		// the new list's status
		$list_status = isset( $_POST['list_status'] ) ? $_POST['list_status'] : ''; 

		$list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';

		$return = array();

		// create new list
		if ( true == $create_list ) {
			$args = array(
				'post_title'    => $list_name,
				'post_content'  => '',
				'post_status'   => $list_status,
				'post_type'     => 'edd_wish_list',
			);

			$list_id = wp_insert_post( $args );

			if ( $list_id ) {
				$return['list_created'] = true;
				$return['list_name'] = $list_name;
			}
		}

		// add each download to wish list
		foreach ( $to_add as $options ) {
			if ( $_POST['download_id'] == $options['price_id'] ) {
				$options = array();
			}

			edd_wl_add_to_wish_list( $_POST['download_id'], $options, $list_id );
		}

		// get title of list
		$title = get_the_title( $list_id );
		// get URL of list
		$url = get_permalink( $list_id );

		$return['success'] = sprintf( __( 'Successfully added to <strong>%s</strong>', 'edd-wish-lists' ), '<a href="' . $url . '">' . $title . '</a>' );

		echo json_encode( $return );
	}
	edd_die();
}
add_action( 'wp_ajax_edd_add_to_wish_list', 'edd_ajax_add_to_wish_list' );
add_action( 'wp_ajax_nopriv_edd_add_to_wish_list', 'edd_ajax_add_to_wish_list' );

/**
 * Open Modal
 *
 * @since 1.0
*/
function edd_wl_open_modal() {
	check_ajax_referer( 'edd_wl_ajax_nonce', 'nonce' );

	if ( ! isset( $_POST['post_id'] ) )
		return;

    $download_id = intval( $_POST['post_id'] );

    // get price IDs
    $price_ids = isset( $_POST['price_ids'] ) && is_array( $_POST['price_ids'] ) ? $_POST['price_ids'] : '';

    // single price option (shortcode)
    $price_option_single = isset( $_POST['price_option_single'] ) ? $_POST['price_option_single'] : '';

	$to_add = array();

	if ( isset( $_POST['price_ids'] ) && is_array( $_POST['price_ids'] ) ) {
		foreach ( $_POST['price_ids'] as $price ) {
			$to_add[] = array( 'price_id' => $price );
		}
	}

	$items = '';

	foreach ( $to_add as $options ) {

		if( $download_id == $options['price_id'] )
			$options = array();

		$item = array(
			'id'      =>  $download_id,
			'options' => $options
		);

		// add each item to array
		$items[] = $item;
	}

    // get wish lists and send price IDs + items array
    $lists = edd_wl_get_wish_lists( $download_id, $price_ids, $items, $price_option_single );
    
    // count lists
    $list_count = edd_wl_get_query() ? count ( edd_wl_get_query() ) : 0;

    $return = array(
		'post_id'    => $download_id,
		'list_count' => $list_count,	// count how many lists the user has
		'lists'      => html_entity_decode( $lists, ENT_COMPAT, 'UTF-8' )
	);

	echo json_encode( $return );
	
	edd_die();
}
add_action( 'wp_ajax_edd_wl_open_modal', 'edd_wl_open_modal' );
add_action( 'wp_ajax_nopriv_edd_wl_open_modal', 'edd_wl_open_modal' );


/**
 * Share via email
 *
 * @since 1.0
*/
function edd_wl_share_via_email() {
	check_ajax_referer( 'edd_wl_ajax_nonce', 'nonce' );

	global $edd_options;

	if ( ! isset( $_POST['post_id'] ) )
		return;

	// referrer
	$referrer = $_POST['referrer'] ? $_POST['referrer'] : '';

	// sender details
	$sender_name  = isset( $_POST['from_name'] ) ? $_POST['from_name'] : '';
	$sender_email = isset( $_POST['from_email'] ) ? $_POST['from_email'] : '';
	$emails       = isset( $_POST['emails'] ) ? $_POST['emails'] : '';
	$post_id      = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';
	$from_name    = isset( $edd_options['from_name'] ) ? $edd_options['from_name'] : get_bloginfo('name');
	$from_email   = isset( $edd_options['from_email'] ) ? $edd_options['from_email'] : get_option('admin_email');
	$message      = isset( $_POST['message'] ) ? $_POST['message'] : '';

    // validation
    if ( ! ( $sender_name || $sender_email || ! edd_wl_validate_share_emails( $emails ) ) ) {
        $has_error = true;
    }

	if ( ! isset( $has_error ) ) {
		$shortlink = home_url( '?p=' . $post_id );

		$subject   = edd_wl_share_via_email_subject( $sender_name, $referrer ); 	
		$message   = edd_wl_share_via_email_message( $shortlink, $sender_name, $sender_email, $message, $referrer );

		$headers   = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
		$headers  .= "Reply-To: ". $sender_email . "\r\n";
		$headers  .= "Content-Type: text/html; charset=utf-8\r\n";
		$headers   = apply_filters( 'edd_wl_share_via_email_headers', $headers );

		// send email
		wp_mail( $emails, $subject, $message, $headers );
	}

	$return['success'] = edd_wl_modal_share_via_email_success();
 
	echo json_encode( $return );
	
	edd_die();
}
add_action( 'wp_ajax_edd_wl_share_via_email', 'edd_wl_share_via_email' );
add_action( 'wp_ajax_nopriv_edd_wl_share_via_email', 'edd_wl_share_via_email' );