<?php
/**
 * Form processing
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes the form requests
 *
 * @since 1.0
*/
function edd_wl_process_form_requests() {
  $messages = edd_wl_messages();

  // create and update forms
  if ( isset( $_POST['submitted'] ) && isset( $_POST['list_nonce_field'] ) && wp_verify_nonce( $_POST['list_nonce_field'], 'list_nonce' ) ) {

    // list title
    if ( trim( $_POST['list-title'] ) === '' ) {
        edd_wl_set_message( 'list_title_required', $messages['list_title_required'] );
        $has_error        = true;
    }

    // only process the form if there are no errors
    if ( ! isset( $has_error ) ) {
      // edit form
      if ( isset( $_GET['created'] ) && $_GET['created'] == true ) {
        $args = array(
          'post_title'    => isset( $_POST['list-title'] ) ? wp_strip_all_tags( $_POST['list-title'] ) : '',
          'post_content'  => isset( $_POST['list-description'] ) ? $_POST['list-description'] : '',
          'post_status'   => $_POST['privacy'],
          'post_type'     => 'edd_wish_list',
        );

        $post_id = wp_insert_post( $args );

        // redirect to success page if successful
        if ( $post_id ) {
          // create token for logged in user and store against list
          edd_wl_create_token( $post_id );

          // redirect to newly created list
          wp_redirect( add_query_arg( 'list', 'created', get_permalink( $post_id ) ) ); exit;
        }
      }
      // update form
      elseif ( isset( $_GET['updated'] ) && $_GET['updated'] == true ) {

        $wish_list  = get_post( get_query_var('wl_edit') ); // get wish list
        $post_id    = $wish_list->ID;

        $args = array(
          'ID'            => $post_id,
          'post_title'    => esc_attr( strip_tags( $_POST['list-title'] ) ),
          'post_content'  => esc_attr( strip_tags( $_POST['list-description'] ) ),
          'post_type'     => 'edd_wish_list',
          'post_status'   => $_POST['privacy'],
        );

        $updated_post_id = wp_update_post( $args );

        //  redirect to success page
        if ( $updated_post_id ) {
          $messages = edd_wl_messages();
          // redirect user back to list they just updated
           wp_redirect( add_query_arg( 'list', 'updated', get_permalink( $updated_post_id ) ) ); exit;  

        }
      } // end edit form process
    } // end has error
  }

}
add_action( 'template_redirect', 'edd_wl_process_form_requests' );