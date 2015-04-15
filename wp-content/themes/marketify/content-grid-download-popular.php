<?php
/**
 * Popular downloads
 *
 * @since Marketify 1.0
 */

$popular = marketify_download_archive_popular();

if ( ! $popular->have_posts() )
	return;
?>

<div class="marketify_widget_featured_popular popular">

	<h1 class="section-title"><span><?php _e( 'Popular', 'marketify' ); ?></span></h1>

	<div id="items-popular" class="row flexslider">
		<ul class="slides">
			<?php while ( $popular->have_posts() ) : $popular->the_post(); ?>
			<li class="col-lg-3 col-sm-6">
				<?php get_template_part( 'content-grid', 'download' ); ?>
			</li>
			<?php endwhile; ?>
		</ul>
	</div>

</div>