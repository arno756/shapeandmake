<?php
class UPCP_Widget_Product_List extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'upcp_product_list_widget', // Base ID
			__('UPCP Product(s) List', 'UPCP'), // Name
			array( 'description' => __( 'Insert a list of product(s)', 'UPCP' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		/*if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', 'UPCP' );*/
		echo do_shortcode("[insert-products product_ids='". $instance['product_list'] . "' catalogue_url='" . $instance['catalogue_url'] . "']");
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$product_list = ! empty( $instance['product_list'] ) ? $instance['product_list'] : __( 'Product IDs', 'UPCP' );
		$catalogue_url = ! empty( $instance['catalogue_url'] ) ? $instance['catalogue_url'] : __( 'Catalogue URL', 'UPCP' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'product_list' ); ?>"><?php _e( 'Comma-separated product IDs:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'product_list' ); ?>" name="<?php echo $this->get_field_name( 'product_list' ); ?>" type="text" value="<?php echo esc_attr( $product_list ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'catalogue_url' ); ?>"><?php _e( 'The URL of your catalogue:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'catalogue_url' ); ?>" name="<?php echo $this->get_field_name( 'catalogue_url' ); ?>" type="text" value="<?php echo esc_attr( $catalogue_url ); ?>">
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['product_list'] = ( ! empty( $new_instance['product_list'] ) ) ? strip_tags( $new_instance['product_list'] ) : '';
		$instance['catalogue_url'] = ( ! empty( $new_instance['catalogue_url'] ) ) ? strip_tags( $new_instance['catalogue_url'] ) : '';

		return $instance;
	}
}
add_action('widgets_init', create_function('', 'return register_widget("UPCP_Widget_Product_List");'));

class UPCP_Widget_Random_Products extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'upcp_random_products_widget', // Base ID
			__('UPCP Random Products', 'UPCP'), // Name
			array( 'description' => __( 'Inserts a number of random products', 'UPCP' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		/*if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', 'UPCP' );*/
		echo do_shortcode("[insert-products catalogue_id='". $instance['catalogue_id'] . "' product_count='" . $instance['product_count'] ."' catalogue_url='" . $instance['catalogue_url'] . "']");
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$catalogue_id = ! empty( $instance['catalogue_id'] ) ? $instance['catalogue_id'] : __( 'Catalogue ID', 'UPCP' );
		$product_count = ! empty( $instance['product_count'] ) ? $instance['product_count'] : __( 'Product Count', 'UPCP' );
		$catalogue_url = ! empty( $instance['catalogue_url'] ) ? $instance['catalogue_url'] : __( 'Catalogue URL', 'UPCP' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'catalogue_id' ); ?>"><?php _e( 'Catalogue ID:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'catalogue_id' ); ?>" name="<?php echo $this->get_field_name( 'catalogue_id' ); ?>" type="text" value="<?php echo esc_attr( $catalogue_id ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'product_count' ); ?>"><?php _e( 'Number of products to display:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'product_count' ); ?>" name="<?php echo $this->get_field_name( 'product_count' ); ?>" type="text" value="<?php echo esc_attr( $product_count ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'catalogue_url' ); ?>"><?php _e( 'The URL of your catalogue:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'catalogue_url' ); ?>" name="<?php echo $this->get_field_name( 'catalogue_url' ); ?>" type="text" value="<?php echo esc_attr( $catalogue_url ); ?>">
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['catalogue_id'] = ( ! empty( $new_instance['catalogue_id'] ) ) ? strip_tags( $new_instance['catalogue_id'] ) : '';
		$instance['product_count'] = ( ! empty( $new_instance['product_count'] ) ) ? strip_tags( $new_instance['product_count'] ) : '';
		$instance['catalogue_url'] = ( ! empty( $new_instance['catalogue_url'] ) ) ? strip_tags( $new_instance['catalogue_url'] ) : '';

		return $instance;
	}
}
add_action('widgets_init', create_function('', 'return register_widget("UPCP_Widget_Random_Products");'));

class UPCP_Widget_Recent_Products extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'upcp_recent_products_widget', // Base ID
			__('UPCP Recent Products', 'UPCP'), // Name
			array( 'description' => __( 'Insert a number of recent products', 'UPCP' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		/*if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', 'UPCP' );*/
		echo do_shortcode("[insert-products product_count='" . $instance['product_count'] ."' catalogue_url='" . $instance['catalogue_url'] . "']");
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$product_count = ! empty( $instance['product_count'] ) ? $instance['product_count'] : __( 'Product Count', 'UPCP' );
		$catalogue_url = ! empty( $instance['catalogue_url'] ) ? $instance['catalogue_url'] : __( 'Catalogue URL', 'UPCP' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'product_count' ); ?>"><?php _e( 'Number of products to display:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'product_count' ); ?>" name="<?php echo $this->get_field_name( 'product_count' ); ?>" type="text" value="<?php echo esc_attr( $product_count ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'catalogue_url' ); ?>"><?php _e( 'The URL of your catalogue:', 'UPCP' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'catalogue_url' ); ?>" name="<?php echo $this->get_field_name( 'catalogue_url' ); ?>" type="text" value="<?php echo esc_attr( $catalogue_url ); ?>">
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['product_count'] = ( ! empty( $new_instance['product_count'] ) ) ? strip_tags( $new_instance['product_count'] ) : '';
		$instance['catalogue_url'] = ( ! empty( $new_instance['catalogue_url'] ) ) ? strip_tags( $new_instance['catalogue_url'] ) : '';

		return $instance;
	}
}
add_action('widgets_init', create_function('', 'return register_widget("UPCP_Widget_Recent_Products");'));

?>