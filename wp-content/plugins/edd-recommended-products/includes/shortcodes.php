<?php

function edd_rp_render_shortcode( $attributes, $content = null ) {
	global $post;
	extract( shortcode_atts( array( 'ids'   => $post->id,
									'user'  => 'false',
									'count' => 3,
									'title' => __( 'Recommended Products', 'edd-rp-txt' ) ),
							 $attributes,
							 'recommended_products' ) );

	$ids   = str_replace( ' ', '', $ids );
	$ids   = explode( ',', $ids );
	$count = absint( $count );

	$user_id = ( $user == 'true' ) ? get_current_user_id() : false;

	$suggestions = edd_rp_get_multi_suggestions( $ids, $user_id, $count );

	if ( is_array( $suggestions ) && !empty( $suggestions ) ) :
		$suggestions = array_keys( $suggestions );

		$suggested_downloads = new WP_Query( array( 'post__in' => $suggestions, 'post_type' => 'download' ) );

		if ( $suggested_downloads->have_posts() ) : ?>
			<div id="edd-rp-single-wrapper">
				<h5 id="edd-rp-single-header"><?php echo $title; ?></h5>
				<div id="edd-rp-items-wrapper" class="edd-rp-single">
					<?php while ( $suggested_downloads->have_posts() ) : ?>
						<?php $suggested_downloads->the_post();	?>
						<div class="edd-rp-item <?php echo ( !current_theme_supports( 'post-thumbnails' ) ) ? 'edd-rp-nothumb' : ''; ?>">
							<a href="<?php the_permalink(); ?>">
							<?php the_title(); ?>
							<?php if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( get_the_ID() ) ) :?>
								<div class="edd_cart_item_image">
									<?php echo get_the_post_thumbnail( get_the_ID(), apply_filters( 'edd_checkout_image_size', array( 125,125 ) ) ); ?>
								</div>
							<?php else: ?>
								<br />
							<?php endif; ?>
							</a>
							<?php if ( !edd_has_variable_prices( get_the_ID() ) ) : ?>
								<?php edd_price( get_the_ID() ); ?>
							<?php endif; ?>

							<?php echo edd_get_purchase_link( array( 'download_id' => get_the_ID(),
																	 'price' => false,
																	 'direct' => false ) );	?>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	<?php endif;
}
