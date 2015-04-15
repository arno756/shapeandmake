<?php
/**
 * Reviews Widget
 *
 * @package EDD_Reviews
 * @subpackage Widgets
 * @copyright Copyright (c) 2014, Sunny Ratilal
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Reviews_Widget_Reviews' ) ) :

/**
 * EDD_Reviews_Widget_Reviews Class
 *
 * @package EDD_Reviews
 * @since 1.0
 * @version 1.0
 * @author Sunny Ratilal
 * @see WP_Widget
 */
final class EDD_Reviews_Widget_Reviews extends WP_Widget {
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
			__( 'EDD Reviews', 'edd-reviews' ),
			apply_filters( 'edd_reviews_widget_options', array(
				'classname'   => 'widget_edd_reviews',
				'description' => __( 'Display the latest reviews posted on your store.', 'edd-reviews' )
			) )
		);

		$this->alt_option_name = 'widget_edd_reviews';

		add_action( 'comment_post',              array( $this, 'flush_widget_cache' ) );
		add_action( 'transition_comment_status', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Flush Comment Cache
	 *
	 * @since 1.0
	 * @access public
	 * @uses wp_cache_delete()
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_edd_recent_reviews', 'widget' );
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

		// Get cached items if they exist
		$cache = wp_cache_get( 'widget_edd_recent_reviews', 'widget' );

		// If cache doesn't exist, create an array for the cache
		if ( $cache !== false ) {
			if ( !empty( $cache[$args['widget_id']] ) ) {
				echo $cache[$args['widget_id']];
				return;
			}
		} else {
			$cache = array();
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Reviews', 'edd-reviews' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

		if ( ! $number )
 			$number = 5; // Sets default number of reviews to display as 5

		$reviews = get_comments( apply_filters( 'widget_edd_reviews_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'download' ) ) );

		$output .=  $before_widget;

		if ( ! empty( $title ) )
			$output .= $before_title . $title . $after_title;

		if ( $reviews ) {
			$output .= '<ul id="edd_recent_reviews" class="edd_reviews_list edd_widget_list">';

			foreach ( (array) $reviews as $review ) {
				$rating = get_comment_meta( $review->comment_ID, 'edd_rating', true );

				$output .= '<li class="edd_recent_review">';
				$output .= sprintf( _x( '%1$s on %2$s', 'widgets', 'edd-reviews' ), get_comment_author_link(), '<a href="' . esc_url( get_comment_link( $review->comment_ID ) ) . '">' . get_the_title( $review->comment_post_ID ) . '</a>' );
				$output .= '</li>';
			}

			$output .= '</ul>';
		} else {
			$output .= '<span class="edd-reviews-no-reviews">' . __( 'There are no reviews yet.', 'edd-reviews' ) . '</span>';
		}

		$output .= $after_widget;

		echo $output;

		// Stores the output in the $cache array
		$cache[$args['widget_id']] = $output;

		// Puts the reviews data in the cache for performance enhancements
		wp_cache_set( 'widget_edd_recent_reviews', $cache, 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0
	 * @access public
	 * @uses EDD_Reviews_Widget_Reviews::flush_widget_cache()
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['number'] = absint( $new_instance['number'] );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_edd_reviews'] ) )
			delete_option( 'widget_edd_reviews' );

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
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? esc_attr( $instance['number'] ) : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd-reviews' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of reviews to show:', 'edd-reviews' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		</p>
		<?php
	}
}

endif;