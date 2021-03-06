<?php
/**
 * The template for displaying search forms in Marketify
 *
 * @package Marketify
 */
?>

<div class="search-form-overlay">

	<form role="search" method="get" class="search-form<?php echo '' != get_search_query() ? ' active' : ''; ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<button type="submit" class="search-submit"><i class="icon-search"></i></button>
		<label>
			<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'marketify' ); ?></span>
			<input type="search" class="search-field" placeholder="<?php echo esc_attr__( 'Search', 'marketify' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_attr__( 'Search for:', 'marketify' ); ?>">
		</label>

		<a href="#" class="header-search-toggle"><i class="icon-cross"></i></a>

		<input type="hidden" name="post_type" value="download" />
	</form>

</div>