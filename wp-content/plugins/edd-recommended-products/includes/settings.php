<?php

function edd_rp_settings( $settings ) {

	$suggested_download_settings = array(
		array(
			'id' => 'edd_rp_header',
			'name' => '<strong>' . __('Recommended Products', 'edd-rp-txt') . '</strong>',
			'desc' => '',
			'type' => 'header',
			'size' => 'regular'
		),
		array(
			'id' => 'edd_rp_display_single',
			'name' => __('Show on Downloads', 'edd-rp-txt'),
			'desc' => __('Display the recommended products on the download post type', 'edd-rp-txt'),
			'type' => 'checkbox',
			'size' => 'regular'
		),
		array(
			'id' => 'edd_rp_display_checkout',
			'name' => __('Show on Checkout', 'edd-rp-txt'),
			'desc' => __('Display the recommended products after the Checkout Cart, and before the Checkout Form', 'edd-rp-txt'),
			'type' => 'checkbox',
			'size' => 'regular'
		),
		array(
			'id' => 'edd_rp_suggestion_count',
			'name' => __('Number of Recommendations', 'edd-rp-txt'),
			'desc' => __('How many recommendations should be shown to users', 'edd-rp-txt'),
			'type' => 'select',
			'options' => edd_rp_suggestion_count()
		),
		array(
			'id' => 'edd_rp_show_free',
			'name' => __('Show Free Products', 'edd-rp-txt'),
			'desc' => __('Allows free products to be shown in the recommendations. (Requires Refresh of Recommendations after save)', 'edd-rp-txt'),
			'type' => 'checkbox',
			'size' => 'regular'
		),
		array(
			'id' => 'rp_settings_additional',
			'name' => '',
			'desc' => '',
			'type' => 'hook'
		)
	);

	return array_merge( $settings, $suggested_download_settings );
}
add_filter( 'edd_settings_extensions', 'edd_rp_settings' );


function edd_rp_suggestion_count() {
	for ( $i = 1; $i <= 5; $i++ )
		$count[$i] = $i;

		$count[3] = __( '3 - Default', 'edd-rp-txt' );

	return apply_filters( 'edd_rp_suggestion_counts', $count );
}

function edd_rp_recalc_suggestions_button() {
	echo '<a href="' . wp_nonce_url( add_query_arg( array( 'edd_action' => 'refresh_edd_rp' ) ), 'edd-rp-recalculate' ) . '" class="button-secondary">' . __( 'Refresh Recommendations', 'edd-rp-txt' ) . '</a>';
}
add_action( 'edd_rp_settings_additional', 'edd_rp_recalc_suggestions_button' );

function refresh_edd_rp( $data ) {
	if ( ! wp_verify_nonce( $data['_wpnonce'], 'edd-rp-recalculate' ) )
		return;

	// Refresh Suggestions
	edd_rp_generate_stats();
	add_action( 'admin_notices', 'edd_rp_recalc_notice' );
}
add_action( 'edd_refresh_edd_rp', 'refresh_edd_rp' );

function edd_rp_recalc_notice() {
	printf( '<div class="updated settings-error"> <p> %s </p> </div>', esc_html__( 'Recommendations Updated.', 'edd-rp-txt' ) );
}
