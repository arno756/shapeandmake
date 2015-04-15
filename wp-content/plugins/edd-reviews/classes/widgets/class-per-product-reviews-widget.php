<?php
/**
 * Per Product Reviews Widget
 *
 * Designed to be used on a download page. Shows the most recent x reviews posted against a
 * the download being viewed.
 *
 * @package EDD_Reviews
 * @subpackage Widgets
 * @copyright Copyright (c) 2014, Lee Willis
 * @since 1.3.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Reviews_Per_Product_Reviews_Widget' ) ) :

/**
 * EDD_Reviews_Per_Product_Reviews_Widget Class
 *
 * @package EDD_Reviews
 * @since 1.3.7
 * @version 1.3.7
 * @author Lee Willis
 * @see WP_Widget
 */
final class EDD_Reviews_Per_Product_Reviews_Widget extends WP_Widget {

	/**
	 * Constructor Function
	 *
	 * @since 1.3.7
	 * @access public
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		parent::__construct(
			false,
			__( 'EDD Per Product Reviews', 'edd-reviews' ),
			apply_filters(
				'edd_reviews_per_product_widget_options',
				array(
					'classname'   => 'widget_edd_per_product_reviews',
					'description' => __( 'Display the latest reviews about a specific download.', 'edd-reviews' )
				)
			)
		);
		$this->alt_option_name = 'widget_edd_per_product_reviews';
		$this->defaults = array(
			'title' => __( 'Recent reviews', 'edd-reviews' ),
			'number' => 5,
		);
		add_action( 'comment_post',              array( $this, 'flush_widget_cache' ), 10, 2 );
		add_action( 'transition_comment_status', array( $this, 'flush_widget_cache' ), 10, 3 );
	}

	/**
	 * Flush Comment Cache.
	 *
	 * @since 1.3.7
	 * @access public
	 * @uses wp_cache_delete()
	 * @return void
	 */
	public function flush_widget_cache( $comment_id, $status ) {
		wp_cache_delete( 'widget_edd_per_product_reviews', 'widget' );
	}

	/**
	 * Render the widget output.
	 *
	 * @since 1.3.7
	 * @access public
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$post = get_queried_object();
		if ( !$post || !$post->ID ) {
			return;
		}
		$post_id = $post->ID;

		extract( $args, EXTR_SKIP );

		// Begin output
		$output = '';

		// Get cached items if they exist
		$cache = wp_cache_get( 'widget_edd_per_product_reviews', 'widget' );
		$cache_arr_key = $args['widget_id'] . '_' . $post_id;

		// Use cached information if it exists
		if ( $cache !== false ) {
			if ( !empty( $cache[$cache_arr_key] ) ) {
				echo $cache[$cache_arr_key];
				return;
			}
		} else {
			$cache = array();
		}


		// Otherwise generate the information
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number = absint( $instance['number'] );

		$reviews = get_comments(
			apply_filters(
				'widget_edd_per_product_reviews_args',
				array(
					'number' => $number,
					'status' => 'approve',
					'post_status' => 'publish',
					'post_type' => 'download',
					'post_id' => $post_id,
				)
			)
		);

		$output .= $before_widget;

		if ( ! empty( $title ) )
			$output .= $before_title . $title . $after_title;

		if ( $reviews ) {
			$output .= '<ul id="edd_per_product_reviews" class="edd_reviews_list edd_widget_list">';

			foreach ( (array) $reviews as $review ) {
				$review_meta = get_comment_meta( $review->comment_ID );
				if ( ! isset( $review_meta['edd_rating'] ) )
					continue;
				$rating = $review_meta['edd_rating'][0];
				$review_title = $review_meta['edd_review_title'][0];
				if ( $review->user_id ) {
					$author = get_userdata( $review->user_id );
					$author = !empty( $author->display_name ) ? $author->display_name : $author->user_nicename;
				} else {
					$author = $review->comment_author;
				}
				$output .= '<li class="edd_recent_review">';
				$output .= '<div role="img"';
				$output .= ' aria-label="' . $rating . ' ' . __( 'stars', 'edd-reviews' );
				$output .= '">';
				$output .= '<div class="edd_star_rating" style="float: none; width: ' . ( 19 * $rating ) . 'px;"></div>';
				$output .= '<div class="edd_recent_review_author">';
				$output .= '<span class="edd_review_title">' . esc_html( $review_title ) . '</span>';
				$output .= ' &mdash; <span class="edd_review_author">';
				$output .= esc_html( $author ) . '</span></div>';
				$output .= '</div>';
				$output .= '</li>';
			}

			$output .= '</ul>';
		} else {
			$output .= '<span class="edd-per-product-reviews-no-reviews">' . __( 'There are no reviews yet.', 'edd-reviews' ) . '</span>';
		}

		$output .= $after_widget;

		echo $output;

		$cache[$cache_arr_key] = $output;

		// Puts the reviews data in the cache for performance enhancements
		wp_cache_set( 'widget_edd_per_product_reviews', $cache, 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.3.7
	 * @access public
	 * @uses EDD_Reviews_Per_Product_Reviews_Widget::flush_widget_cache()
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		$this->flush_widget_cache();
		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.3.7
	 * @access public
	 * @param array $instance The array of keys and values for the widget.
	 * @return void
	 */
	public function form( $instance ) {
		$config = array_merge( $this->defaults, $instance );
		extract( $config );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd-reviews' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of reviews to show:', 'edd-reviews' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
		</p>
		<?php
	}
}

endif;