<?php
/**
 * Wish List template
*/

// get list ID
$list_id = edd_wl_get_list_id();

// get the downloads from the wish list
$downloads = edd_wl_get_wish_list( $list_id );

// get list post object
$list = get_post( $list_id );

// title
$title = get_the_title( $list_id );

//status
$privacy = get_post_status( $list_id );

?>

<?php if ( $list_id ) : ?>
	<p><?php echo $list->post_content; ?></p>
<?php endif; ?>

<?php if ( $downloads ) : ?>

	<?php // All all items in list to cart
		echo edd_wl_add_all_to_cart_link( $list_id );
	?>

	<ul class="edd-wish-list">
		<?php foreach ( $downloads as $key => $item ) : ?>
			<li class="wl-row">
				<?php // item title
					echo edd_wl_item_title( $item ); 
				?>

				<?php // item price
					echo edd_wl_item_price( $item['id'], $item['options'] ); 
				?>

				<?php // purchase link
					echo edd_wl_item_purchase( $item );
				?>

				<?php // remove item link
					echo edd_wl_item_remove_link( $item['id'], $key, $list_id );
				?>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php 
	/**
	 * Sharing - only shown for public lists
	*/
	if ( 'private' !== get_post_status( $list_id ) && apply_filters( 'edd_wl_display_sharing', true ) ) : ?>
		<div class="edd-wl-sharing">
			<h3><?php _e( 'Share', 'edd-wish-lists' ); ?></h3>
			<p><?php echo wp_get_shortlink( $list_id ); // Shortlink to share ?></p>
			
			<?php 
				// Share via email
				echo edd_wl_share_via_email_link();
				
				// Social sharing services
				echo edd_wl_sharing_services();
			?>
		</div>
	<?php endif; ?>

<?php endif; ?>

<?php // edit settings
	echo edd_wl_edit_settings_link( $list_id );
?>