<?php
/**
 * Create Wish List template
*/
?>

<?php 
/**
 * Add new list button
 * Only shows if guests are allowed to create lists
*/
if ( edd_wl_allow_guest_creation() ) : ?>		
<form action="<?php echo add_query_arg( 'created', true ); ?>" class="wish-list-form" method="post">
	<p>
	    <label for="list-title"><?php _e( 'Title:', 'edd-wish-lists' ); ?></label>
	    <input type="text" name="list-title" id="list-title">
	</p>
	<p>
	    <label for="list-description"><?php _e( 'Description:', 'edd-wish-lists' ); ?></label>
	    <textarea name="list-description" id="list-description" rows="3" cols="30"></textarea>
	</p>
	<p>
	  <select name="privacy">
	  	<option value="private"><?php _e( 'Private - only viewable by you', 'edd-wish-lists' ); ?></option>
	    <option value="publish"><?php _e( 'Public - viewable by anyone', 'edd-wish-lists' ); ?></option>
	  </select>
	</p>
	<p> 
	    <input type="submit" value="<?php _e( 'Create', 'edd-wish-lists' ); ?>" class="button">
	</p>

	<input type="hidden" name="submitted" id="submitted" value="true">

	<?php wp_nonce_field( 'list_nonce', 'list_nonce_field' ); ?>
</form>
<?php endif; ?>