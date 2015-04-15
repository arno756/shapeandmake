<?php
/**
 * Messages
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Messages
 *
 * @since 1.0
*/
function edd_wl_messages() {
	$messages = array(
		'must_login'                   => sprintf( __( 'Sorry, you must login to create a %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		'list_updated'                 => sprintf( __( '%s updated', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
		'list_created_guest'           => sprintf( __( '%s created and saved for 30 days. If you wish to keep it longer, please create an account.', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
		'list_created'                 => sprintf( __( '%s created', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
		'list_deleted'                 => sprintf( __( '%s deleted', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
		'no_lists'                     => sprintf( __( 'You currently have no %s', 'edd-wish-lists' ), edd_wl_get_label_plural( true ) ),
		'list_delete_confirm'          => sprintf( __( 'You are about to delete this %s, are you sure?', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		'no_downloads'                 => sprintf( __( 'Nothing here yet, how about adding some %s?', 'edd-wish-lists' ), edd_get_label_plural( true ) ),
		'lists_included'               => __( 'This item has already been added to: ', 'edd-wish-lists' ),
		'modal_option_save'	           => __( 'Save', 'edd-wish-lists' ),
		'modal_option_close'           => __( 'Great, I\'m done', 'edd-wish-lists' ),
		'modal_option_add_new'         => __( 'Add to new', 'edd-wish-lists' ),
		'modal_option_add_to_existing' => __( 'Add to existing', 'edd-wish-lists' ),
		'modal_option_title'           => __( 'Title', 'edd-wish-lists' ),
		'modal_delete_title'           => sprintf( __( 'Delete %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		'modal_button_delete_confirm'  => sprintf( __( 'Yes, delete this %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		'modal_share_title'	           => sprintf( __( 'Share this %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
		'modal_share_success'          => __( 'Successfully shared', 'edd-wish-lists' ),
		'list_option_private'          => __( 'Private - only viewable by you', 'edd-wish-lists' ),
		'list_option_public'           => __( 'Public - viewable by anyone', 'edd-wish-lists' ),
		'list_title_required'          => __( 'You need to enter a title', 'edd-wish-lists' ),
		'item_already_purchased'       => __( 'Already purchased', 'edd-wish-lists' ),
	);

	return apply_filters( 'edd_wl_messages', $messages );
}

/**
 * Set various messages
 *
 * @since 1.0
 * @todo  provide better filtering of messages
*/
function edd_wl_set_messages() {
	// get array of messages
	$messages = edd_wl_messages();

	/**
	 * wish-lists.php
	*/

	// no lists if no posts
	if ( ! edd_wl_get_query() && edd_wl_is_page( 'wish-lists' ) ) {
		edd_wl_set_message( 'no_lists', $messages['no_lists'] );
	}

	/**
	 * wish-list-create.php
	*/
	// must login
	if ( edd_wl_is_page( 'create' ) && ! edd_wl_allow_guest_creation() ) {
		edd_wl_set_message( 'must_login', $messages['must_login'] );
	}
	
	/**
	 * wish-list-view.php
	*/
	if ( edd_wl_is_page( 'view' ) ) {
		
		$downloads = edd_wl_get_list_id() ? edd_wl_get_wish_list( edd_wl_get_list_id() ) : array();

		// list updated
		if ( isset( $_GET['list'] ) && $_GET['list'] == 'updated' ) {
			edd_wl_set_message( 'list_updated', $messages['list_updated'] );
		}

		// list created
		if ( isset( $_GET['list'] ) && $_GET['list'] == 'created' ) {
			if ( is_user_logged_in() ) {
				edd_wl_set_message( 'list_created', $messages['list_created'] );
			}
			else {
				edd_wl_set_message( 'list_created', $messages['list_created_guest'] );
			}
		}

		// no downloads
		if ( empty( $downloads ) ) {
			edd_wl_set_message( 'no_downloads', $messages['no_downloads'] );
		}
	}

}
add_action( 'template_redirect', 'edd_wl_set_messages' );

/**
 * Print Messages
 *
 * Prints all stored messages.
 * If messages exist, they are returned.
 *
 * @since 1.0
 * @uses edd_wl_get_messages()
 * @uses edd_wl_clear_errors()
 * @return void
 */
function edd_wl_print_messages() {
	ob_start();
	$messages = edd_wl_get_messages();
	if ( $messages ) {
		$classes = apply_filters( 'edd_wl_classes', array(
			'edd_errors', 
			'edd-wl-msgs',
		) );
		echo '<div class="' . implode( ' ', $classes ) . '">';
		   foreach ( $messages as $msg_id => $msg ) {
		        echo '<p class="edd-wl-msg" id="edd-wl-msg-' . $msg_id . '">' . $msg . '</p>';
		   }
		echo '</div>';
		edd_wl_clear_messages();
	}

	return ob_get_clean();
}

/**
 * Get Messages
 *
 * Retrieves all messages stored
 *
 * @since 1.0
 * @uses EDD_Session::get()
 * @return mixed array if errors are present, false if none found
 */
function edd_wl_get_messages() {
	return EDD()->session->get( 'edd_wl_messages' );
}

/**
 * Set Message
 *
 * Stores a message  in a session var.
 *
 * @since 1.0
 * @uses EDD_Session::get()
 * @param int $msg_id ID of the message being set
 * @param string $message Message to store
 * @return void
 */
function edd_wl_set_message( $msg_id, $message ) {
	$msgs = edd_wl_get_messages();

	if ( ! $msgs ) {
		$msgs = array();
	}

	$msgs[ $msg_id ] = $message;
	EDD()->session->set( 'edd_wl_messages', $msgs );
}

/**
 * Clears all stored messages.
 *
 * @since 1.0
 * @uses EDD_Session::set()
 * @return void
 */
function edd_wl_clear_messages() {
	EDD()->session->set( 'edd_wl_messages', null );
}

/**
 * Removes (unsets) a stored message
 *
 * @since 1.0
 * @uses EDD_Session::set()
 * @param int $msg_id ID of the error being set
 * @return void
 */
function edd_wl_unset_message( $msg_id ) {
	$msgs = edd_wl_get_messages();
	if ( $msgs ) {
		unset( $msgs[ $msg_id ] );
		EDD()->session->set( 'edd_wl_messages', $msgs );
	}
}