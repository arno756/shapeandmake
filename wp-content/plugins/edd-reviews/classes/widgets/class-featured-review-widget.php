<?php
/**
 * Featured Review Widget
 *
 * @package EDD_Reviews
 * @subpackage Widgets
 * @copyright Copyright (c) 2014, Sunny Ratilal
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Reviews_Widget_Featured_Review' ) ) :

/**
 * EDD_Reviews_Widget_Featured_Reviews Class
 *
 * @package EDD_Reviews
 * @since 1.0
 * @version 1.0
 * @author Sunny Ratilal
 * @see WP_Widget
 */
final class EDD_Reviews_Widget_Featured_Review extends WP_Widget {
	/**
	 * Constructor Function
	 *
	 * @since 1.0
	 * @access public
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		parent::__construct(
			false,
			__( 'EDD Featured Review', 'edd-reviews' ),
			apply_filters( 'edd_reviews_widget_featured_review_options', array(
				'classname'   => 'widget_edd_reviews_featured_review',
				'description' => __( 'Display a featured review.', 'edd-reviews' )
			) )
		);

		$this->alt_option_name = 'widget_edd_reviews_featured_review';
	}

	/**
	 * Widget API Function
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		// Begin output
		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Featured Review ', 'edd-reviews' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$review_id = ( ! empty( $instance['review'] ) ) ? absint( $instance['review'] ) : '';

		if ( ! $review_id )
			return _e( 'No review was selected. Please configure it from within the widget settings.', 'edd-reviews' );

		$review = get_comment( $review_id );

		$output .=  $before_widget;

		if ( ! empty( $title ) )
			$output .= $before_title . $title . $after_title;

		if ( $review ) {
			$output .= '<div class="edd_featured_reviews">';
			$output .= '<div class="edd-featured-review-h-card">';

			if ( get_option( 'show_avatars' ) ) $output .= get_avatar( $review, apply_filters( 'edd_reviews_widget_avatar_size', 40 ) );

			$output .= '<p>' . get_comment_meta( $review->comment_ID, 'edd_review_title', true ) . ' ' . __( 'by', 'edd-reviews' ) . ' ' . get_comment_author_link( $review->comment_ID ) . '</p>';
			$output .= '<p><a href="' . get_permalink( $review->comment_post_ID ) . '">' . get_the_title( $review->comment_post_ID ) . '</a>' . '</p>';

			$rating = get_comment_meta( $review->comment_ID, 'edd_rating', true );

			$output .= '<div class="edd_reviews_rating_box" role="img" aria-label="'. $rating . ' ' . __( 'stars', 'edd-reviews' ) .'">';
			$output .= '<div class="edd_star_rating" style="width: ' . 19 * $rating . 'px"></div>';
			$output .= '</div>';
			$output .= '<div class="clear"></div>';
			$output .= '</div>';
			$output .= '<div class="edd-review-content">';
			$output .= $review->comment_content;
			$output .= '</div>';
			$output .= '<div class="edd-review-dateline">';
			$output .= get_comment_date( apply_filters( 'edd_reviews_widget_date_format', get_option( 'date_format' ) ), $review->comment_ID );
			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= $after_widget;

		echo $output;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['review'] = absint( $new_instance['review'] );

		if ( isset( $alloptions['widget_edd_reviews_featured_review'] ) )
			delete_option( 'widget_edd_reviews_featured_review' );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget
	 *
	 * @since 1.0
	 * @access public
	 * @param array $instance The array of keys and values for the widget
	 * @return void
	 */
	public function form( $instance ) {
		global $wpdb;

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$review_id = isset( $instance['review'] ) ? absint( $instance['review'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd-reviews' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'review' ); ?>"><?php _e( 'Review to display:', 'edd-reviews' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'review' ); ?>" name="<?php echo $this->get_field_name( 'review' ); ?>">
				<?php
				$reviews = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value, comment_id FROM {$wpdb->commentmeta} WHERE meta_key = %s", 'edd_review_title' ) );

				if ( $reviews ) :
					foreach ( $reviews as $review ) :
						echo '<option value="' . $review->comment_id . '"' . selected( $review_id, $review->comment_id ) .'>' . esc_html( $review->meta_value ) . '</option>';
					endforeach;
				endif;
				?>
			</select>
		</p>
		<?php
	}
}

endif;