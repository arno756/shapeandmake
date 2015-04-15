<?php

/**
 * Search 
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form role="search" method="get" id="bbp-search-form" class="search-form" action="<?php bbp_search_url(); ?>">
	<label>
		<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'marketify' ); ?></span>
		<input type="search" id="bbp_search" class="search-field" placeholder="<?php echo esc_attr__( 'Search', 'marketify' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="bbp_search" title="<?php echo esc_attr__( 'Search for:', 'marketify' ); ?>">
	</label>
	<button type="submit" class="search-submit" id="bbp_search_submit"><i class="icon-search"></i></button>

	<input type="hidden" name="action" value="bbp-search-request" />
</form>