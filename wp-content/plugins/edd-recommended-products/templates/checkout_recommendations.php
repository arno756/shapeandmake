<?php
$cart_items = edd_get_cart_contents();
$post_ids = wp_list_pluck( $cart_items, 'id' );
?>

<?php if ( $cart_items ) : ?>
<?php
	$user_id = ( is_user_logged_in() ) ? get_current_user_id() : false;
	$suggestion_data = edd_rp_get_multi_suggestions( $post_ids, $user_id );

	if ( is_array( $suggestion_data ) && !empty( $suggestion_data ) ) :
	$suggestions = array_keys( $suggestion_data );

	$suggested_downloads = new WP_Query( array( 'post__in' => $suggestions, 'post_type' => 'download' ) );

	if ( $suggested_downloads->have_posts() ) :
		$single = __( 'this item', 'edd-rp-txt' );
		$plural = __( 'these items', 'edd-rp-txt' );
		$cart_items_text = _n( $single, $plural, count( $post_ids ), 'edd-rp-txt' );
		?>
		<div id="edd-rp-checkout-wrapper">
			<h5 id="edd-rp-checkout-header"><?php echo sprintf( __( 'Users who purchased %s, also purchased:', 'edd-rp-txt' ), $cart_items_text ); ?></h5>
			<div id="edd-rp-items-wrapper" class="edd-rp-checkout">
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

	<?php endif; ?>

<?php endif; ?>
