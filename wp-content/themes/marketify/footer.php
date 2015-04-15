<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Marketify
 */
?>

	<footer id="colophon" class="site-footer <?php echo marketify_theme_mod( 'footer', 'footer-style' ); ?>" role="contentinfo">
		<div class="container">

			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="row">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>

			<div class="site-info row<?php echo is_active_sidebar( 'footer-1' ) ? ' has-widgets' : ''; ?>">
				<?php if ( marketify_get_theme_menu( 'social' ) ) : ?>
				<div class="col-md-4">
					<h1 class="footer-widget-title"><?php echo marketify_get_theme_menu_name( 'social' ); ?></h1>

					<?php
						$social = wp_nav_menu( array(
							'theme_location'  => 'social',
							'container_class' => 'footer-social',
							'items_wrap'      => '%3$s',
							'depth'           => 1,
							'echo'            => false,
							'link_before'     => '<span class="screen-reader-text">',
							'link_after'      => '</span>',
						) );

						echo strip_tags( $social, '<a><div><span>' );
					?>
				</div>
				<?php endif; ?>

				<?php $contact = marketify_theme_mod( 'footer', 'footer-contact-address' ); ?>

				<?php if ( $contact ) : ?>
				<div class="col-md-4">
					<h1 class="footer-widget-title"><?php _e( 'Contact Us', 'marketify' ); ?></h1>

					<?php echo wpautop( marketify_theme_mod( 'footer', 'footer-contact-address' ) ); ?>
				</div>
				<?php endif; ?>

				<?php
					$cols = 4;

					if ( ! $contact && ! marketify_get_theme_menu( 'social' ) )
						$cols = 12;
					else if ( ! $contact || ! marketify_get_theme_menu( 'social' ) )
						$cols = 8;
				?>

				<div class="col-md-<?php echo $cols; ?>">
					<h1 class="site-title"><a href="<?php echo home_url(); ?>">
						<?php if ( marketify_theme_mod( 'footer', 'footer-logo' ) ) : ?>
							<img src="<?php echo marketify_theme_mod( 'footer', 'footer-logo' ); ?>" />
						<?php else : ?>
							<?php bloginfo( 'name' ); ?>
						<?php endif; ?>
					</a></h1>

					<?php printf( __( '&copy; %d %s. All rights reserved.', 'marketify' ), date( 'Y' ), get_bloginfo( 'name' ) ); ?>
				</div>
			</div><!-- .site-info -->

		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>