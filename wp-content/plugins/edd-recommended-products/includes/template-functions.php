<?php

function edd_rp_get_templates_dir() {
	return EDD_RP_PLUGIN_DIR . 'templates';
}

function edd_rp_get_template_part( $slug, $name = null, $load = true ) {
	// Execute code for this part

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) )
		$templates[] = $slug . '-' . $name . '.php';
	$templates[] = $slug . '.php';

	// Return the part that is found
	return edd_rp_locate_template( $templates, $load, false );
}

function edd_rp_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) )
			continue;

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// Check child theme first
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'edd_templates/' . $template_name ) ) {
			$located = trailingslashit( get_stylesheet_directory() ) . 'edd_templates/' . $template_name;
			break;

		// Check parent theme next
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'edd_templates/' . $template_name ) ) {
			$located = trailingslashit( get_template_directory() ) . 'edd_templates/' . $template_name;
			break;

		// Check theme compatibility last
		} elseif ( file_exists( trailingslashit( edd_rp_get_templates_dir() ) . $template_name ) ) {
			$located = trailingslashit( edd_rp_get_templates_dir() ) . $template_name;
			break;
		}
	}

	if ( ( true == $load ) && ! empty( $located ) )
		load_template( $located, $require_once );

	return $located;
}


function edd_rp_display_single( $post_id ) {
	edd_rp_get_template_part( 'single_recommendations' );
}

function edd_rp_display_checkout() {
	if( edd_is_checkout() ) {
		// GitHub Issue: https://github.com/pippinsplugins/Easy-Digital-Downloads/issues/1059
		add_filter( 'edd_straight_to_checkout', '__return_true' );
	}

	edd_rp_get_template_part( 'checkout_recommendations' );	
}