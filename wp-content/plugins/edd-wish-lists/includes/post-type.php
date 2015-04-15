<?php
/**
 * Sets up the custom post type
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register post type
 *
 * @since 1.0
*/
function edd_wl_register_post_type() {
	$labels =  apply_filters( 'edd_wl_post_type_labels', array(
		'name' 				=> '%2$s',
		'singular_name' 	=> '%1$s',
		'add_new' 			=> __( 'Add New', 'edd-wish-lists' ),
		'add_new_item' 		=> __( 'Add New %1$s', 'edd-wish-lists' ),
		'edit_item' 		=> __( 'Edit %1$s', 'edd-wish-lists' ),
		'new_item' 			=> __( 'New %1$s', 'edd-wish-lists' ),
		'all_items' 		=> __( '%2$s', 'edd-wish-lists' ),
		'view_item' 		=> __( 'View %1$s', 'edd-wish-lists' ),
		'search_items' 		=> __( 'Search %2$s', 'edd-wish-lists' ),
		'not_found' 		=> __( 'No %2$s found', 'edd-wish-lists' ),
		'not_found_in_trash'=> __( 'No %2$s found in Trash', 'edd-wish-lists' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( '%2$s', 'edd-wish-lists' )
	) );

	foreach ( $labels as $key => $value ) {
	   $labels[ $key ] = sprintf( $value, edd_wl_get_label_singular(), edd_wl_get_label_plural() );
	}

	$args = apply_filters( 'edd_wl_post_type_args' , array(
		'labels' 				=> $labels,
		'public'			  	=> true,
		'capability_type'	  	=> 'post',
		'map_meta_cap' 			=> true,
		'show_ui' 				=> true,
		'hierarchical' 			=> false,
		'query_var' 			=> true,
		'exclude_from_search'	=> true,
		'rewrite'				=> false,
		'supports'			  	=> array( 'title', 'editor' ),
		'show_in_menu' 			=> 'edit.php?post_type=download',
	));

	register_post_type( 'edd_wish_list', $args );

}
add_action( 'init', 'edd_wl_register_post_type', 1 );

/**
 * Prevents 'Private:' from being prepended to the post title 
 *
 * @since 1.0
*/
function edd_wl_private_list_title( $format ) {
    return '%s';
}
add_filter( 'private_title_format', 'edd_wl_private_list_title' );

/**
 * Get Default Labels
 *
 * @since 1.0
 * @return array $defaults Default labels
 */
function edd_wl_get_default_labels() {
	$defaults = array(
	   'singular' => __( 'Wish List', 'edd-wish-lists' ),
	   'plural' => __( 'Wish Lists', 'edd-wish-lists')
	);

	return apply_filters( 'edd_wl_default_labels', $defaults );
}

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function edd_wl_get_label_singular( $lowercase = false ) {
	$defaults = edd_wl_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function edd_wl_get_label_plural( $lowercase = false ) {
	$defaults = edd_wl_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}