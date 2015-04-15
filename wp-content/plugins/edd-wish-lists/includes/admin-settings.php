<?php
/**
 * Admin settings
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function edd_dropdown_pages_callback( $args ) {
	global $edd_options;

	$defaults = array(
		'depth' => 0, 
		'child_of' => 0,
		'selected' => 0, 
		'echo' => 1,
		'name' => 'page_id', 
		'id' => '',
		'show_option_none' => __( 'No page selected', 'edd-wish-lists' ), 
		'show_option_no_change' => '',
		'option_none_value' => 'none'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty($id) )
		$id = $name;

	if ( ! empty( $pages ) ) {
		$output = '<select name="edd_settings[' . $args['id'] . ']" id="edd_settings[' . $args['id'] . ']">';
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= edd_wl_walk_page_dropdown_tree( $pages, $depth, $r, $args);
		$output .= "</select>\n";
		$output .= '<label for="edd_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
	}

	if ( $echo )
		echo $output;

	return $output;
}


/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 */
function edd_wl_walk_page_dropdown_tree() {
	$args = func_get_args();

	$walker = new EDD_Wish_Lists_Walker_PageDropdown;

	return call_user_func_array(array($walker, 'walk'), $args);
}


/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 1.0
 * @uses Walker
 */
class EDD_Wish_Lists_Walker_PageDropdown extends Walker {
	var $tree_type = 'page';
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	function start_el( &$output, $page, $depth = 0, $args = array(), $id = 0 ) {
		global $edd_options;

		$pad = str_repeat('&nbsp;', $depth * 3);
		
		$output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
		
		$option = isset( $edd_options[$args['id']] ) ? $edd_options[$args['id']] : '';

		$selected = selected( $option, $page->ID, false );
		$output .= $selected;

		$output .= '>';
		$title = $page->post_title;
		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}
}

/**
 * Settings
 *
 * @since 1.0
*/
function edd_wl_settings( $settings ) {
	
	$plugin_settings = array(
		array(
			'id' => 'edd_wl_header',
			'name' => '<strong>' . sprintf( __( '%s', 'edd-wish-lists' ), edd_wl_get_label_plural() ) . '</strong>',
			'type' => 'header'
		),
		array(
			'id' => 'edd_wl_page',
			'name' => sprintf( __( '%s Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will view their %s. This page should include the [edd_wish_lists] shortcode', 'edd-wish-lists' ), edd_wl_get_label_plural( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_wl_page_view',
			'name' => sprintf( __( '%s View Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will view each %s. This page should include the [edd_wish_lists_view] shortcode', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_wl_page_edit',
			'name' => sprintf( __( '%s Edit Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will edit a %s. This page should include the [edd_wish_lists_edit] shortcode', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_wl_page_create',
			'name' => sprintf( __( '%s Create Page', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
			'desc' => '<p class="description">' . sprintf( __( 'Select the page where users will create a %s. This page should include the [edd_wish_lists_create] shortcode', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'dropdown_pages',
		),
		array(
			'id' => 'edd_wl_add_to_wish_list',
			'name' => sprintf( __( 'Add To %s Text', 'edd-wish-lists' ), edd_wl_get_label_singular() ),
			'desc' => '<p class="description">' . sprintf( __( 'Enter the text you\'d like to appear for adding a %s to a %s', 'edd-wish-lists' ), edd_get_label_singular( true ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'text',
			'std' => sprintf( __( 'Add to %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) )
		),
		array(
			'id' => 'edd_wl_add_to_cart',
			'name' => __( 'Add To Cart Text', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . sprintf( __( 'Enter the add to cart text you\'d like to appear on the single %s page', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'text',
			'std' => __( 'Add to cart', 'edd-wish-lists' )
		),
		array(
			'id' => 'edd_wl_allow_guests',
			'name' => sprintf( __( 'Allow Guests To Create %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ),
		//	'desc' => '<p class="description">' . sprintf( __( 'Allow Guests To Create %s', 'edd-wish-lists' ), edd_wl_get_label_plural() ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'yes' =>  __( 'Yes', 'edd-wish-lists' ),
				'no' =>  __( 'No', 'edd-wish-lists' ),
			),
			'std' => 'yes'
		),
		array(
			'id' => 'edd_wl_icon',
			'name' => __( 'Icon', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . sprintf( __( 'The icon to show next to the add to %s links', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ) . '</p>',
			'type' => 'select',
			'options' =>  apply_filters( 'edd_wl_icons', 
				array(
					'add' 		=>  __( 'Add', 'edd-wish-lists' ),
					'bookmark' 	=>  __( 'Bookmark', 'edd-wish-lists' ),
					'gift' 		=>  __( 'Gift', 'edd-wish-lists' ),
					'heart' 	=>  __( 'Heart', 'edd-wish-lists' ),
					'star' 		=>  __( 'Star', 'edd-wish-lists' ),
					'none' 		=>  __( 'No Icon', 'edd-wish-lists' ),
				)
			),
			'std' => 'star'
		),
		array(
			'id' => 'edd_wl_button_style',
			'name' => __( 'Button Style', 'edd-wish-lists' ),
			'desc' => '<p class="description">' . __( 'Display a button or a plain text link', 'edd-wish-lists' ) . '</p>',
			'type' => 'select',
			'options' =>  array(
				'plain' =>  __( 'Plain Text', 'edd-wish-lists' ),
				'button' =>  __( 'Button', 'edd-wish-lists' ),
			),
			'std' => 'button'
		),
		array(
			'id' => 'edd_wl_services',
			'name' => __( 'Sharing', 'edd-wish-lists' ),
			'desc' => __( 'Select the services you\'d like users to share to', 'edd-wish-lists' ),
			'type' => 'multicheck',
			'options' => apply_filters( 'edd_wl_settings_services', array(
					'twitter' =>  __( 'Twitter', 'edd-wish-lists' ),
					'facebook' =>  __( 'Facebook', 'edd-wish-lists' ),
					'googleplus' =>  __( 'Google+', 'edd-wish-lists' ),
					'linkedin' =>  __( 'LinkedIn', 'edd-wish-lists' ),
					'email' =>  __( 'Email', 'edd-wish-lists' ),
				)
			)
		),
	);
	
	return array_merge( $settings, $plugin_settings );
}
add_filter( 'edd_settings_extensions', 'edd_wl_settings' );