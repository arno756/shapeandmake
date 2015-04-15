<?php
/**
 * Dashboard Columns
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wish List Columns
 *
 * @since 1.0
*/
function edd_wl_admin_columns( $download_columns ) {
	$download_columns = array(
		'cb'          => '<input type="checkbox"/>',
		'title'       => __( 'Title', 'edd-wish-lists' ),
		'downloads'   => __( 'Downloads', 'edd-wish-lists' ),
		'list_author' => __( 'Author', 'edd-wish-lists' ),
		'total'       => __( 'Total', 'edd-wish-lists' ),
		'date'        => __( 'Date', 'edd-wish-lists' )
	);

	return apply_filters( 'edd_wl_admin_columns', $download_columns );
}
add_filter( 'manage_edit-edd_wish_list_columns', 'edd_wl_admin_columns' );

/**
 * Render Wish List Columns
 *
 * @since 1.0
 * @param string $column_name Column name
 * @param int $post_id Download (Post) ID
 * @return void
 */
function edd_wl_render_admin_columns( $column_name, $post_id ) {
	if ( get_post_type( $post_id ) == 'edd_wish_list' ) {

		$items = get_post_meta( get_the_ID(), 'edd_wish_list', true );

		switch ( $column_name ) {
		
			case 'downloads':
				if ( $items ) {
					echo count( $items );
				} else {
					echo 0;
				}
			break;

			case 'total':
				echo edd_wl_get_list_total( get_the_ID() );
			break;

			case 'list_author':
				$post = get_post();
				if ( 0 == $post->post_author )
					echo __( 'Guest', 'edd-wish-lists' );
				 else {
				 	printf( '<a href="%s">%s</a>',
				 		esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'author' => get_the_author_meta( 'ID' ) ), 'edit.php' )),
				 		get_the_author() // display name
				 	);
				 }
			break;

		}
	}
}
add_action( 'manage_posts_custom_column', 'edd_wl_render_admin_columns', 10, 2 );