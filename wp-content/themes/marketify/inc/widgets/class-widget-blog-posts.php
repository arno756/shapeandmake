<?php
/**
 * Recent Blog Posts
 *
 * @since Marketify 1.0
 */
class Marketify_Widget_Recent_Posts extends Marketify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_recent_posts';
		$this->widget_description = __( 'Display recent blog posts.', 'marketify' );
		$this->widget_id          = 'marketify_widget_recent_posts';
		$this->widget_name        = __( 'Marketify - Home: Recent Posts', 'marketify' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Recent Posts',
				'label' => __( 'Title:', 'marketify' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 4,
				'label' => __( 'Number to display:', 'marketify' )
			),
			'style' => array(
				'type'  => 'select',
				'std'   => 'classic',
				'options' => array(
					'classic' => __( 'Classic', 'marketify' ),
					'grid'    => __( 'Grid', 'marketify' )
				),
				'label' => __( 'Display Style:', 'marketify' )
			),
			'description' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Description:', 'marketify' )
			),
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

		global $post;

		extract( $args );

		$title        = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$description  = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : null;
		$number       = isset ( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : 8;
		$style        = isset ( $instance[ 'style' ] ) ? $instance[ 'style' ] : 'classic';

		$posts = new WP_Query( array(
			'post_type'              => 'post',
			'posts_per_page'         => $number,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false
		) );

		if ( ! $posts->have_posts() )
			return;

		global $more;

		$more = 0;

		echo $before_widget;

		if ( $title ) echo $before_title . $title . $after_title;
		?>

		<?php if ( $description ) : ?>
			<h2 class="home-widget-description"><?php echo $description; ?></h2>
		<?php endif; ?>

		<div class="row">
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
			<div class="col-lg-<?php echo 'grid' == $style ? marketify_get_download_columnns() : '6'; ?> col-sm-6 col-xs-12">
				<?php get_template_part( 'content', 'grid' == $style ? 'grid' : get_post_format() ); ?>
			</div>
			<?php endwhile; ?>
		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}