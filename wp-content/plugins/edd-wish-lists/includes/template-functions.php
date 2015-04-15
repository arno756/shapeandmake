<?php
/**
 * Template functions
 *
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add path for template files
 *
 * @since 1.0
*/
function edd_wl_edd_template_paths( $file_paths ) {
	$file_paths[95] = edd_wl_get_templates_dir();
	return $file_paths;
}
add_filter( 'edd_template_paths', 'edd_wl_edd_template_paths' );


/**
 * Returns the path to the templates directory
 *
 * @since 1.0
 * @return string
 */
function edd_wl_get_templates_dir() {
	return EDD_WL_PLUGIN_DIR . 'templates';
}

/**
 * Returns the URL to the EDD templates directory
 *
 * @since 1.0
 * @return string
 */
function edd_wl_get_templates_url() {
	return EDD_WL_PLUGIN_URL . 'templates';
}

/**
 * Get number of items in list
 *
 * @since 1.0
*/
function edd_wl_get_item_count( $list_id ) {
	$items = get_post_meta( $list_id, 'edd_wish_list', true );
	$count = ! empty ( $items ) ? '(' . count ( $items ) . ')' : '(0)';

	return $count;
}

/**
 * Displays the total price of downloads in a wishlist
 * @param  int $list_id ID of lsit
 * @return string total
 * @uses  edd_wl_get_list_total()
 * @since  1.0
 */
function edd_wl_list_total( $list_id ) {
	// get the list total
	$total = edd_wl_get_list_total( $list_id );

	echo apply_filters( 'edd_wl_list_total', '<p>' . __( ' Total: ', 'edd-wish-lists' ) . $total . '</p>' );
}

/**
 * Filter title to include the list name on either the view or edit pages
 *
 * @since 1.0
*/
function edd_wl_wp_title( $title, $sep ) {
	$view_page = edd_get_option( 'edd_wl_page_view' );
	$edit_page = edd_get_option( 'edd_wl_page_edit' );
	
	if ( is_page( $view_page ) || is_page( $edit_page ) ) {
		
		// prevent the title from changing
		if ( edd_wl_is_private_list() )
			return $title;

		if ( is_page( $view_page ) ) {
			$list_id = get_query_var( 'wl_view' );
		}
		elseif ( is_page( $edit_page ) ) {
			$list_id = get_query_var( 'wl_edit' );
		}

		$list_title = get_the_title( $list_id );

		// Prepend the list name to the site title.
		$title = $list_title . " $sep " . $title;
	}
	
	return $title;
}
add_filter( 'wp_title', 'edd_wl_wp_title', 10, 2 );

/**
 * Filter the page titles
 *
 * @since 1.0
*/
function edd_wl_the_title( $title, $id = '' ) {

	// prevent the title from changing
	if ( edd_wl_is_private_list() )
		return $title;

	// View page - replace the main page title with the name of the list
	if ( edd_wl_is_view_page() && get_query_var( 'wl_view' ) && in_the_loop() && $id == get_the_ID() ) {
		$title = get_the_title( get_query_var( 'wl_view' ) );
	}	

    return $title;
}
add_filter( 'the_title', 'edd_wl_the_title', 10, 2 );


/**
 * Handles loading of the wish list link
 * @param  int $download_id download ID
 * @return void
 * @since  1.0
 */
function edd_wl_load_wish_list_link( $download_id = '' ) {
	// set the $download_id to the post ID if $download_id is not present
	if ( ! $download_id ) {
		$download_id = get_the_ID();
	}

	$classes = array();
	// assign a class to the link depending on where it's hooked to
	// this way we can control the margin needed at the top or bottom of the link
	if( has_action( 'edd_purchase_link_end', 'edd_wl_load_wish_list_link' ) ) {
		$classes[] = 'after';
	}
	elseif( has_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' ) ) {
		$classes[] = 'before';
	}

	// default classes
	$classes[] = 'edd-wl-action';
	$classes[] = 'edd-wl-open-modal';

	$args = array(
		'download_id'	=> $download_id,			// available on edd_purchase_link_end, edd_purchase_link_top hooks
		'action'		=> 'edd_wl_open_modal',
		'class'			=> implode( ' ', $classes ),
		'link_size'		=> apply_filters( 'edd_wl_link_size', '' )
	);
	edd_wl_wish_list_link( $args );
}

/**
 * Possible hooks this link could be loaded on
 *
 * edd_purchase_link_end 				After standard purchase button which is used by shortcode and standard buttons. Displays within form tag. Will also show on a single page with purchase_link shortcode
 * edd_purchase_link_top (default)		Before purchase button. Will also show on a single page with purchase_link shortcode
 * edd_after_download_content 			After download content, outside of form tag
 * your_own_custom_hook					Your own hook using do_action()
*/
add_action( 'edd_purchase_link_top', 'edd_wl_load_wish_list_link' );


/**
 * The Wish list link
 *
 * @since 1.0
*/
function edd_wl_wish_list_link( $args = array() ) {
	global $edd_options, $post;

	$defaults = apply_filters( 'edd_wl_link_defaults', 
		array(
			'download_id' 	=> isset( $post->ID ) ? $post->ID : '',
			'text'        	=> ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : '',
			'style'       	=> edd_get_option( 'edd_wl_button_style', 'button' ),
			'color'       	=> '',
			'class'       	=> 'edd-wl-action',
			'icon'			=> edd_get_option( 'edd_wl_icon', 'star' ),
			'action'		=> '',
			'link'			=> '',
			'link_size'		=> '',
			'price_option'	=> '',
		) 
	);

	// merge arrays
	$args = wp_parse_args( $args, $defaults );

	// extract $args so we can use the variable names
	extract( $args, EXTR_SKIP );

	// manually select price option for shortcode
	$price_opt 				= isset( $price_option ) ? ( $price_option - 1 ) : ''; // so user can enter in 1, 2,3 instead of 0, 1, 2 as option
	$price_option 			= $price_option ? ' data-price-option="' . $price_opt . '"' : '';

	if ( ! $price_option ) {
		$variable_pricing 	= edd_has_variable_prices( $args['download_id'] );
		$data_variable  	= $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
		$type             	= edd_single_price_option_mode( $args['download_id'] ) ? 'data-price-mode=multi' : 'data-price-mode=single';	
	}
	else {
		$data_variable = '';
		$type = '';
	}

	ob_start();

	$icon = $icon && 'none' != $icon ? '<i class="glyphicon glyphicon-' . $icon . '"></i>' : '';

	// shortcode parameter for returning function
	$shortcode = isset( $shortcode ) ? $shortcode : '';

	// size of plain text or button link
	$link_size = $link_size ? $link_size : '';

	// show the icon on either the left or right
	$icon_position = apply_filters( 'edd_wl_icon_position' , 'left' );

	// move the icon based on the location of the icon
	$icon_left = 'left' == $icon_position ? $icon : '';
	$icon_right = 'right' == $icon_position ? $icon : '';

	$class .= 'right' == $icon_position ? ' glyph-right' : ' glyph-left';
	$class .= ! $text ? ' no-text' : '';


	// change CSS class based on style chosen
	if ( 'button' == $style )
		$style = 'edd-wl-button';
	elseif ( 'plain' == $style )
		$style = 'plain';

	// if link is specified, don't show spinner
	$loading = ! $link ? '<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>' : '';
	$link = ! $link ? '#' : $link; 

	// text
	$text = $text ? '<span class="label">' . esc_attr( $text ) . '</span>' : '';

	// data action
	$action = $action ? 'data-action="' . $action . '"' : '';

	// download ID
	$download_id = $download_id ? 'data-download-id="' . esc_attr( $download_id ) . '"' : '';

	printf(
		'<a href="%1$s" class="%2$s %3$s" %4$s %5$s %6$s %7$s %12$s>%8$s%9$s%10$s%11$s</a>',
		$link, 														// 1
		implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 2
		$link_size, 												// 3
		$action, 													// 4
		$download_id, 												// 5
		esc_attr( $data_variable ),									// 6
		esc_attr( $type ), 											// 7
		$icon_left, 												// 8
		$text,														// 9
		$loading, 													// 10
		$icon_right, 												// 11
		$price_option 												// 12
	);

	$html = apply_filters( 'edd_wl_link', ob_get_clean() );

	// return for shortcode, else echo
	if ( $shortcode ) {
		return $html;
	}
	else {
		echo $html;
	}
}

/**
 * Load the correct template
 * $type string view | edit | create
 *
 * @since 1.0
*/
function edd_wl_load_template( $type ) {
	ob_start();


	// display messages
	echo edd_wl_print_messages();

	// only show template if there's a list ID
	if ( 'view' == $type && edd_wl_get_list_id() ) {
		edd_get_template_part( 'wish-list-' . $type );
	}
	else {
		edd_get_template_part( 'wish-list-' . $type );
	}

	// load required scripts when shortcodes are used
	wp_enqueue_script( 'edd-wl' );
	wp_enqueue_script( 'edd-wl-modal' );

	$template = ob_get_clean();
	return apply_filters( 'edd_wl_load_template', $template );
}

/**
 * Loads the required scripts any time the [downloads] shortcode is used
 * @param  [type] $display [description]
 * @return [type]          [description]
 */
function edd_wl_enqueue_scripts( $display ) {
	wp_enqueue_script( 'edd-wl' );
	wp_enqueue_script( 'edd-wl-modal' );

	return $display;
}
add_filter( 'downloads_shortcode', 'edd_wl_enqueue_scripts' );


/**
 * Main Wish List function called by [edd_wish_lists] shortcode
 * This template can be found in the /templates folder. 
 * Copy wish-lists.php to your edd_templates folder in your child theme
 * Would be nice to use get_template_part but you cannot pass variables along
 *
 * @since  1.0
 * @return [type]        [description]
 */
function edd_wl_wish_list() {
	ob_start();
	echo edd_wl_print_messages();
	edd_get_template_part( 'wish-lists' );
	
	return ob_get_clean();
}

/**
 * Wish list item purchase link
 *
 * @since  1.0
 * @param  [type] $item [description]
 * @return [type]       [description]
 */
function edd_wl_item_purchase( $item, $args = array() ) {
	global $edd_options;

	ob_start();
	
	$defaults = apply_filters( 'edd_wl_add_to_cart_defaults', array(
		'download_id' 	=> $item['id'],
		'text'        	=> ! empty( $edd_options[ 'edd_wl_add_to_cart' ] ) ? $edd_options[ 'edd_wl_add_to_cart' ] : __( 'Add to cart', 'edd-wish-lists' ),
		'checkout_text' => __( 'Checkout', 'edd-wish-lists' ),
		'style'       	=> edd_get_option( 'edd_wl_button_style', 'button' ),
		'color'       	=> '',
		'class'       	=> 'edd-wl-action',
		'wrapper'		=> 'span',
		'wrapper_class'	=> '',
		'link'			=> '',
	), $item['id'] );

	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	$style = 'edd-wl-button';

	$variable_pricing 	= edd_has_variable_prices( $download_id );
	$data_variable  	= $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';

	// price option
	$data_price_option  = $variable_pricing ? ' data-price-option=' . $item['options']['price_id'] : '';

	$type             	= edd_single_price_option_mode( $download_id ) ? 'data-price-mode=multi' : 'data-price-mode=single';

	if ( edd_item_in_cart( $download_id ) && ! $variable_pricing ) {
		// hide the 'add to cart' link
		$button_display   = 'style="display:none;"';
		// show the 'checkout' link
		$checkout_display = '';
	}
	// if the variable priced download is in cart, show 'checkout'
	elseif( $variable_pricing &&  edd_item_in_cart( $download_id, array( 'price_id' => $item['options']['price_id'] ) ) ) {
		// hide the 'add to cart' link
		$button_display   = 'style="display:none;"';
		// show the 'checkout' link
		$checkout_display = '';
	}
	else {
		// show the 'add to cart' link
		$button_display   = '';
		// hide the 'checkout' link
		$checkout_display = 'style="display:none;"';
	}

	$button_size = '';
	// if link is specified, don't show spinner
	$loading = ! $link ? '<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>' : '';
	$link = ! $link ? '#' : $link; 

	$form_id = ! empty( $form_id ) ? $form_id : 'edd_purchase_' . $download_id;
	
	// add our default class
	$default_wrapper_class = ' edd-wl-item-purchase';
	$wrapper_class .= $wrapper_class ? $default_wrapper_class : trim( $default_wrapper_class );	
	
	$default_css_class = apply_filters( 'edd_wl_item_purchase_default_css_classes', 'edd-add-to-cart-from-wish-list', $download_id );
	?>

	<form id="<?php echo $form_id; ?>" class="edd_download_purchase_form" method="post">
		<div class="edd_purchase_submit_wrapper">
		<?php 
			printf(
				'<a href="%10$s" class="%11$s %1$s %8$s" data-action="edd_add_to_cart_from_wish_list" data-download-id="%3$s" %4$s %5$s %6$s %7$s><span class="label">%2$s</span>%9$s</a>',
				implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 1
				esc_attr( $text ),											// 2
				esc_attr( $download_id ),									// 3
				esc_attr( $data_variable ),									// 4
				esc_attr( $type ),											// 5
				$button_display,											// 6
				esc_attr( $data_price_option ),								// 7
				$button_size, 												// 8
				$loading, 													// 9
				$link, 														// 10
				$default_css_class 											// 11
			);

			// checkout link that shows when item is added to the cart
			printf(
				'<a href="%1$s" class="%2$s %3$s %5$s" %4$s>' . $checkout_text . '</a>',
				esc_url( edd_get_checkout_uri() ),									// 1
				esc_attr( 'edd-go-to-checkout-from-wish-list' ),					// 2
				implode( ' ', array( $style, $color, trim( $class ) ) ),			// 3
				$checkout_display,													// 4
				$button_size 														// 5
			);

		?>
		</div>
	</form>
	
<?php 
	
	$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . ob_get_clean() .'</' . $wrapper . '>';
	return apply_filters( 'edd_wl_item_purchase', $html, $item );
}

/**
 * Purchase all items in wish list
 *
 * @since  1.0.2
 * @param  [type] $item [description]
 * @return [type]       [description]
 */
function edd_wl_add_all_to_cart_link( $list_id = 0, $args = array() ) {

	if ( ! apply_filters( 'edd_wl_show_add_all_to_cart_link', true ) )
		return;

	$defaults = apply_filters( 'edd_wl_add_all_to_cart_link_defaults', 
		array(
			'list_id'		=> $list_id,
			'text' 			=> __( 'Add all to cart', 'edd-wish-lists' ),
			'style'			=> 'button',
			'color'			=> '',
			'class'			=> 'edd-wl-action edd-wl-add-all-to-cart',
			'wrapper'		=> 'p',
			'wrapper_class'	=> ''
		)
	);


	$args = wp_parse_args( $args, $defaults );

	extract( $args, EXTR_SKIP );

	// return if there's only 1 item in list
	$list = edd_wl_get_wish_list( $list_id );
	if ( count ( $list ) == 1 )
		return;

	// change CSS class based on style chosen
	if ( 'button' == $style )
		$style = 'edd-wl-button';
	elseif ( 'plain' == $style )
		$style = 'plain';

	$html = '';

	$button = sprintf(
		'<a href="' . add_query_arg( array( 'edd_action' => 'wl_purchase_all', 'list_id' => $list_id ) ) . '" class="%1$s">%2$s</a>',
		implode( ' ', array( $style, $color, trim( $class ) ) ), 	// 1
		esc_attr( $text )											// 2
	);

	$wrapper_class = $wrapper_class ? ' class="' . $wrapper_class . '"' : '';

	if ( $wrapper )
		$html .= '<' . $wrapper . $wrapper_class . '>' . $button .'</' . $wrapper . '>';
	else 
		$html .= $button;


	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_add_all_to_cart_link', $html );
}

/**
 * Delete list link
 *
 * @since  1.0.2
 * @param  array  $args link arguments
 * @return string       delete link
 */
function edd_wl_delete_list_link( $args = array() ) {
	if ( ! apply_filters( 'edd_wl_show_delete_link', true ) )
		return;

	$defaults = apply_filters( 'edd_wl_delete_list_link_defaults', 
		array(
			'text' 		=> sprintf( __( 'Delete %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
			'class'		=> '',
			'wrapper' 	=> 'p',
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	// add our default class
	$default_class = ' edd-wl-delete-list';
	$class .= $class ? $default_class : trim( $default_class );

	ob_start(); 

	$html = '';

	$link = '<a href="#" class="' . $class . '" title="' . $text . '">' . $text . '</a>';

	if ( $wrapper )
		$html .= '<' . $wrapper . '>' . $link .'</' . $wrapper . '>';
	else 
		$html .= $link;

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_delete_list_link', $html );
}

/**
 * Outputs the item price
 *
 * @since  1.0.2
 * @return [type] [description]
 */
function edd_wl_item_price( $item_id, $options, $args = array() ) {
	$defaults = apply_filters( 'edd_wl_item_price_defaults', 
		array(
			'wrapper_class'	=> '',
			'wrapper' 		=> 'span',
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	ob_start(); 

	// add our default class
	$default_class = ' edd-wl-item-price';
	$wrapper_class .= $wrapper_class ? $default_class : trim( $default_class );

	$price = apply_filters( 'edd_wl_item_price', edd_cart_item_price( $item_id, $options ), $item_id );

	$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $price .'</' . $wrapper . '>';
	
	echo $html;

	$template = ob_get_clean();
	return apply_filters( 'edd_wl_item_price_html', $template );
}

/**
 * Remove item link
 *
 * @since  1.0.2
 * @param  [type] $item_id [description]
 * @param  [type] $key     [description]
 * @param  [type] $list_id [description]
 * @param  array  $args    [description]
 * @return [type]          [description]
 */
function edd_wl_item_remove_link( $item_id, $key, $list_id, $args = array() ) {

	if ( ! edd_wl_is_users_list( $list_id ) )
		return;

	$defaults = apply_filters( 'edd_wl_item_remove_link_defaults', 
		array(
			'text' 			=> __( 'Remove', 'edd-wish-lists' ),
			'wrapper_class'	=> '',
			'wrapper' 		=> 'span',
			'class'			=> ''
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	// add our default class
	$default_wrapper_class = ' edd-wl-item-remove';
	$wrapper_class .= $wrapper_class ? $default_wrapper_class : trim( $default_wrapper_class );

	$default_class = ' edd-remove-from-wish-list';
	$class .= $class ? $default_class : trim( $default_class );

	ob_start(); 

	$html = '';

	$label = $text ? '<span class="hide-text">' . $text . '</span>' : '';

	$link = '<a href="#" data-cart-item="' . $key . '" data-download-id="' . $item_id . '" data-list-id="' . $list_id . '" data-action="edd_remove_from_wish_list" class="' . $class . '" title="' . $text . '"><i class="glyphicon glyphicon-remove"></i>' . $label . '</a>';

	if ( $wrapper )
		$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $link .'</' . $wrapper . '>';
	else 
		$html .= $link;

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_item_remove_link', $html );
}

/**
 * Item title
 * @since  1.0.2
 */
function edd_wl_item_title( $item, $args = array() ) {

	$item_id 			= $item['id'];
	$item_option 		= ! empty( $item['options'] ) ? apply_filters( 'edd_wl_item_title_options', '<span class="edd-wl-item-title-option">' . edd_get_cart_item_price_name( $item ) . '</span>' ) : '';
	$variable_price_id 	= isset( $item['options']['price_id'] ) ? $item['options']['price_id'] : '';
	$already_purchased 	= apply_filters( 'edd_wl_item_title_already_purchased', edd_wl_has_purchased( $item_id, $variable_price_id ) );

	if ( current_theme_supports( 'post-thumbnails' ) ) {
		$item_image = has_post_thumbnail( $item_id ) ? apply_filters( 'edd_wl_item_image', '<span class="edd-wl-item-image">' . get_the_post_thumbnail( $item_id, apply_filters( 'edd_checkout_image_size', array( 50, 50 ) ) ) . '</span>' ) : '';
	}

	$defaults = apply_filters( 'edd_wl_item_title_defaults', 
		array(
			'wrapper_class'	=> '',
			'wrapper' 		=> 'span',
			'class'			=> ''
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	// add our default class
	$default_wrapper_class = ' edd-wl-item-title';
	$wrapper_class .= $wrapper_class ? $default_wrapper_class : trim( $default_wrapper_class );

	$class = $class ? 'class="' . trim( $class ) . '"' : '';

	ob_start(); 

	$html = '';

	$link = '<a href="' . apply_filters( 'edd_wl_item_title_permalink', post_permalink( $item_id ), $item_id ) . '" ' . $class . ' title="' . get_the_title( $item_id ) . '">' . get_the_title( $item_id ) . '</a>';

	$output = $link . $item_option . $already_purchased . $item_image;

	if ( $wrapper ) {
		$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $output . '</' . $wrapper . '>';
	}
	else {
		$html .= $output;
	}

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_item_title', $html );
}

/**
 * Edit settings link
 * @since  1.0.2
 */
function edd_wl_edit_settings_link( $list_id, $args = array() ) {

	// exit if no edit page is selection
	if ( apply_filters( 'edd_wl_edit_settings_link_return', 'none' == edd_get_option( 'edd_wl_page_edit' ) ) )
		return;

	// don't show if not user's list
	if ( ! edd_wl_is_users_list( $list_id ) )
		return;

	$defaults = apply_filters( 'edd_wl_edit_settings_link_defaults', 
		array(
			'text'			=> __( 'Edit settings', 'edd-wish-lists' ),
			'wrapper_class'	=> '',
			'wrapper' 		=> 'p',
			'class'			=> ''
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$class = $class ? 'class="' . trim( $class ) . '"' : '';

	ob_start(); 

	$uri = apply_filters( 'edd_wl_edit_settings_link_uri', edd_wl_get_wish_list_edit_uri( $list_id ), $list_id );

	$html = '';

	$link = '<a href="' . $uri . '" ' . $class . ' title="' . $text . '">' . $text . '</a>';

	if ( $wrapper ) {
		$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $link . '</' . $wrapper . '>';
	}
	else {
		$html .= $link;
	}

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_edit_settings_link', $html );
}

/**
 * Edit settings link
 * @since  1.0.2
 */
function edd_wl_edit_link( $list_id, $args = array() ) {

	if ( 'none' == edd_get_option( 'edd_wl_page_edit' ) )
		return;

	$defaults = apply_filters( 'edd_wl_edit_link_defaults', 
		array(
			'text'			=> __( 'edit', 'edd-wish-lists' ),
			'wrapper_class'	=> '',
			'wrapper' 		=> 'span',
			'class'			=> ''
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$class = $class ? 'class="' . trim( $class ) . '"' : '';

	// add our default class
	$default_wrapper_class = ' edd-wl-edit';
	$wrapper_class .= $wrapper_class ? $default_wrapper_class : trim( $default_wrapper_class );

	ob_start(); 

	$html = '';

	$link = '<a href="' . edd_wl_get_wish_list_edit_uri( $list_id ) . '" ' . $class . ' title="' . $text . '">' . $text . '</a>';

	if ( $wrapper ) {
		$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $link . '</' . $wrapper . '>';
	}
	else {
		$html .= $link;
	}

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_edit_link', $html );
}

/**
 * Create list link
 * @since  1.0.2
 */
function edd_wl_create_list_link( $args = array() ) {

	// exit if page is not selected in options, or guest creation is not allowed
	if ( 'none' == edd_get_option( 'edd_wl_page_create' ) || ! edd_wl_allow_guest_creation() )
		return;

	$defaults = apply_filters( 'edd_wl_create_list_link_defaults', 
		array(
			'text'			=> sprintf( __( 'Create new %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) ),
			'wrapper_class'	=> '',
			'wrapper' 		=> 'p',
			'class'			=> ''
		)
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$default_class = ' edd-wl-button edd-wl-action';
	$class .= $class ? $default_class : trim( $default_class );

	ob_start(); 

	$html = '';

	$link = '<a href="' . edd_wl_get_wish_list_create_uri() . '" class="' . $class . '" title="' . $text . '">' . $text . '</a>';

	if ( $wrapper ) {
		$html = '<' . $wrapper . ' class="' . $wrapper_class . '"' . '>' . $link . '</' . $wrapper . '>';
	}
	else {
		$html .= $link;
	}

	echo $html;

	$html = ob_get_clean();

	return apply_filters( 'edd_wl_create_list_link', $html );
}