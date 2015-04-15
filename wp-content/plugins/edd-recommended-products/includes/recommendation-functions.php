<?php
/**
 * edd_rp_generate_stats
 *
 * Generates the full relational data array for all downloads
 * *
 * @since       1.0
*/

function edd_rp_generate_stats() {
	$defaults = array(
		'nopaging'		=> true,
		'number'		=> 250
	);

	// Data is acquired from the most recent 250 purchase logs to protect performance. If you need to increase or decrease this, use the following filter
	$log_query = apply_filters( 'edd_rp_log_query_args', $defaults );
	$edd_payments_query = new EDD_Payments_Query( $log_query );
	$edd_payments = $edd_payments_query->get_payments();

	// Determine what users have purchased what products
	if ( !empty( $edd_payments ) ) {
		foreach ( $edd_payments as $payment ) {

			$user_email = $payment->user_info['email'];
			$cart_items = $payment->cart_details;

			if ( is_array( $cart_items ) ) {
				foreach ( $cart_items as $item ) {
					$logs_data[md5( $user_email )][] = $item['id'];
				}
			}
		}

		foreach ( $logs_data as &$log ) {
			$log = array_unique( $log );
		}

		// Itterate through each download and find users who have purchased it, then if they have purhcased any other downloads
		// add those to the count.
		$downloads = get_posts( array( 'post_type' => 'download', 'posts_per_page' => '-1', 'fields' => 'ids' ) );
		$relation_array = array();
		$display_free = edd_get_option( 'edd_rp_show_free', 1 );

		foreach ( $downloads as $download ) {
			$relation_array[$download] = array();

			foreach ( $logs_data as $log ) {
				if ( in_array( $download, $log ) ) {
					foreach ( $log as $item ) {
						if ( $display_free && !edd_has_variable_prices( $item ) && absint( edd_get_download_price( $item ) ) === 0 )
							continue;

						if ( !isset( $relation_array[$download][$item] ) )
							$relation_array[$download][$item] = 0;

						$relation_array[$download][$item]++;
					}
				}
			}

			// Since the data inherintly includes itself in it's own array, just unset it.
			unset( $relation_array[$download][$download] );
			arsort( $relation_array[$download] );
		}

		update_option( '_edd_rp_master_array', $relation_array );
	}
}

/**
 * edd_rp_get_suggestions
 *
 * Generates the suggestions for a download based off post_id
 * If the user is logged it, it will remove already purchased items from the array
 * *
 * @since       1.0
 * @param 		$post_id int the download ID being requested
 * @param   	$user_id int a user ID to filter already purchased items from the return
 * @param       $count   int The number of items to display
 * @return      array
*/
function edd_rp_get_suggestions( $post_id = false, $user_id = false, $count = false ) {
	if ( !$post_id )
		return false;

	global $master_array;
	if ( !is_array( $master_array ) || ( !isset( $master_array[$post_id] ) || empty ( $master_array[$post_id] ) ) ) {
		$recommendations = array();
	} else {
		// If a user ID is supplied and they have purchased the item suggested, remove it
		if ( $user_id ) {
			foreach ( $master_array[$post_id] as $id => $count ) {
				if ( edd_has_user_purchased( $user_id, $id, NULL ) )
					unset( $master_array[$post_id][$id] );
			}
		}

		// If the user has items in the cart, remove those from the suggestions
		$cart_items = edd_get_cart_contents();

		if ( $cart_items ) {
			$cart_post_ids = wp_list_pluck( $cart_items, 'id' );

			foreach ( $cart_post_ids as $cart_item ) {
				if ( isset( $master_array[$post_id][$cart_item] ) )
					unset( $master_array[$post_id][$cart_item] );
			}
		}

		$recommendations = $master_array[$post_id];
	}

	// If we didn't get a new length to slice to ( usually from short codes )
	if ( empty( $count ) ) {
		$count = apply_filters( 'edd_rp_single_recommendation_count', edd_get_option( 'edd_rp_suggestion_count', 3 ) );
	}

	return apply_filters( 'edd_rp_single_recommendation_results', array_slice( $recommendations, 0, $count, true ) );
}

/**
 * edd_rp_get_multi_suggestions
 *
 * Generates the suggestions for an array of post IDs
 * *
 * @since       1.0
 * @param 		$post_ids array post IDs to get suggestions for
 * @param   	$user_id int a user ID to filter already purchased items from the return
 * @param       $count   int The number of items to display
 * @return      array
*/
function edd_rp_get_multi_suggestions( $post_ids = false, $user_id = false, $count = false ) {
	if ( !is_array( $post_ids ) || count( $post_ids ) == 0 )
		return false;

	$suggestions = array();

	foreach ( $post_ids as $post ) {
		$new_suggestions = edd_rp_get_suggestions( $post, $user_id, $count );

		if ( is_array( $new_suggestions ) )
			$suggestions = $suggestions + $new_suggestions;
	}

	if ( count( $suggestions ) == 0 ) {
		$suggestions = array();
	} else {
		// Remove any items from the suggestions that were used as the basis
		foreach ( $post_ids as $post_id ) {
			if ( isset( $suggestions[$post_id] ) )
				unset( $suggestions[$post_id] );
		}

		// Sort by the most times purchased
		arsort( $suggestions );
	}


	// If we didn't get a new length to slice to ( usually from short codes )
	if ( empty( $count ) ) {
		$count = apply_filters( 'edd_rp_multi_recommendation_count', edd_get_option( 'edd_rp_suggestion_count', 3 ) );
	}

	return apply_filters( 'edd_rp_multi_recommendation_results', array_slice( $suggestions, 0, $count, true ) );
}
