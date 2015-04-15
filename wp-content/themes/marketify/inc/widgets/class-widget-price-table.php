<?php
/**
 * Price table populated by Price Options
 *
 * @since Marketify 1.0
 */
class Marketify_Widget_Price_Table extends Marketify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_price_table';
		$this->widget_description = __( 'Output the price table (based on the "Price Table" widget area)', 'marketify' );
		$this->widget_id          = 'marketify_widget_price_table';
		$this->widget_name        = __( 'Marketify - Home: Price Table', 'marketify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Pricing Options',
				'label' => __( 'Title:', 'marketify' )
			),
			'description' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Description:', 'marketify' )
			),
			'nothing' => array(
				'type' => 'description',
				'std'  => __( 'Drag "Price Option" widgets to the "Price Table" widget area to populate this widget.', 'marketify' )
			)
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		$title        = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$description  = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : null;
		$the_sidebars = wp_get_sidebars_widgets();
		$widget_count = count( $the_sidebars[ 'widget-area-price-options' ] );

		$content = ob_get_clean();

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
		?>

		<?php if ( $description ) : ?>
			<h2 class="home-widget-description"><?php echo $description; ?></h2>
		<?php endif; ?>

		<div class="pricing-table-widget-<?php echo $widget_count; ?> row">
			<?php dynamic_sidebar( 'widget-area-price-options' ); ?>
		</div>

		<?php
		echo $after_widget;

		echo $content;

		$this->cache_widget( $args, $content );
	}
}