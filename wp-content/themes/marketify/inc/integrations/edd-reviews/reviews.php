<?php
/**
 * Easy Digital Downloads - Product Reviews
 *
 * @package Marketify
 */

function marketify_edd_reviews_reviews_title( $title ) {
	return __( 'Customer Reviews', 'marketify' );
}
add_filter( 'edd_reviews_reviews_title', 'marketify_edd_reviews_reviews_title' );

/**
 * Add the Star Rating to the download information at the top of the page,
 * as well in the download grid.
 *
 * @since Marketify 1.0
 *
 * @return void
 */
function marketify_download_entry_meta_rating( $comment_id = null ) {
	if ( ! class_exists( 'EDD_Reviews' ) )
		return;

	global $post;

	if ( ! $comment_id )
		$rating = edd_reviews()->average_rating( false );
	else
		$rating = get_comment_meta( $comment_id, 'edd_rating', true );

	if ( 0 == $rating || did_action( 'marketify_product_details_before' ) )
		return;
?>
	<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating">
		<?php for ( $i = 1; $i <= $rating; $i++ ) : ?>
		<i class="icon-star"></i>
		<?php endfor; ?>

		<?php for( $i = 0; $i < ( 5 - $rating ); $i++ ) : ?>
		<i class="icon-star2"></i>
		<?php endfor; ?>

		<div style="display:none" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
			<meta itemprop="worstRating" content="1" />
			<span itemprop="ratingValue"><?php echo $rating; ?></span>
			<span itemprop="bestRating">5</span>
		</div>
	</div>
<?php
}
add_action( 'marketify_download_info', 'marketify_download_entry_meta_rating' );
add_action( 'marketify_download_content_image_overlay_after', 'marketify_download_entry_meta_rating' );

/**
 * Download Rating/Rating Title
 *
 * Unhook the automatic output of the title and stars (edd_reviews_ratings_html)
 * from the plugin, and add our own to our own hook (marketify_edd_rating)
 *
 * @since Marketify 1.0
 *
 * @param object $comment
 * @return void
 */
function marketify_edd_download_rating( $comment ) {
	global $post;
?>
	<div class="marketify-edd-rating">
		<?php marketify_download_entry_meta_rating( $comment->comment_ID ); ?>

		<span itemprop="name" class="review-title-text"><?php echo get_comment_meta( $comment->comment_ID, 'edd_review_title', true ); ?></span>
	</div>
<?php
}
add_action( 'marketify_edd_rating', 'marketify_edd_download_rating' );
add_filter( 'edd_reviews_ratings_html', '__return_false', 10, 2 );

/**
 * Star Rating Selector
 *
 * No images allowed. Use our icon font to select a star.
 *
 * @since Marketify 1.0
 *
 * @return void
 */
function marketify_edd_reviews_rating_box() {
	ob_start();
?>
	<span class="edd_reviews_rating_box">
		<span class="edd_ratings">
			<a class="edd_rating" href="" data-rating="5"><i class="icon-star2"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="5"/>5&nbsp;</span>

			<a class="edd_rating" href="" data-rating="4"><i class="icon-star2"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="4"/>4&nbsp;</span>

			<a class="edd_rating" href="" data-rating="3"><i class="icon-star2"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="3"/>3&nbsp;</span>

			<a class="edd_rating" href="" data-rating="2"><i class="icon-star2"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="2"/>2&nbsp;</span>

			<a class="edd_rating" href="" data-rating="1"><i class="icon-star2"></i></a>
			<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="1"/>1&nbsp;</span>
		</span>
	</span>
<?php
	return ob_get_clean();
}
add_filter( 'edd_reviews_rating_box', 'marketify_edd_reviews_rating_box' );