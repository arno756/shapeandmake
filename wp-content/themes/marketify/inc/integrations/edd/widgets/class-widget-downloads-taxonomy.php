<?php
/**
 * Download Taxonomy
 *
 * @since Marketify 1.0
 */
class Marketify_Widget_Downloads_Taxonomy extends Marketify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_downloads_taxonomy';
		$this->widget_description = __( 'Display a list of download taxonomies.', 'marketify' );
		$this->widget_id          = 'marketify_widget_downloads_taxonomy';
		$this->widget_name        = __( 'Marketify - Download Single: Taxonomies', 'marketify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Categories',
				'label' => __( 'Title:', 'marketify' )
			),
			'taxonomy' => array(
				'type'  => 'select',
				'std'   => 'category',
				'label' => __( 'Taxonomy:', 'marketify' ),
				'options' => array(
					'download_category' => __( 'Category', 'marketify' ),
					'download_tag'      => __( 'Tag', 'marketify' )
				)
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

		global $post;

		ob_start();

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$taxonomy = isset ( $instance[ 'taxonomy' ] ) ? $instance[ 'taxonomy' ] : 'download_category';

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;

		echo '<ul class="edd-taxonomy-widget">';
		wp_list_categories( array(
			'title_li' => '',
			'taxonomy' => $taxonomy
		) );
		echo '</ul>';

		echo $after_widget;

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}