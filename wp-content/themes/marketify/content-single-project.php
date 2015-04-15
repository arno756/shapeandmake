<?php
/**
 * @package Marketify
 */
?>

<?php do_action( 'marketify_single_project_content_before_content' ); ?>

<?php if ( get_post()->post_content ) : ?>
<h2 class="section-title"><span><?php _e( 'About the Project', 'marketify' ); ?></span></h2>
<?php endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php do_action( 'marketify_single_project_content_before' ); ?>

		<?php the_content(); ?>

		<?php do_action( 'marketify_single_project_content_after' ); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'marketify' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->

<?php do_action( 'marketify_single_project_after_content' ); ?>
