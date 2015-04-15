<?php
/**
 * Modal dialogs
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load skeleton for modal window in the footer
 *
 * @since 1.0
*/
function edd_wl_modal_window() {
	ob_start();
	?>
	<div class="modal fade" id="edd-wl-modal" tabindex="-1" role="dialog" aria-labelledby="edd-wl-modal-label" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	    	<?php do_action( 'edd_wl_modal_content' ); ?>
	    </div>
	  </div>
	</div>

	<?php
		$html = ob_get_clean();
		echo apply_filters( 'edd_wl_modal_window', $html );
}
add_action( 'wp_footer', 'edd_wl_modal_window', 100 );

/**
 * Share list via email
 *
 * @since 1.0
*/
function edd_wl_modal_share_via_email() {
	$messages = edd_wl_messages();

	// only load on view page, when email sharing is present
	if ( ! ( edd_wl_is_view_page() && edd_wl_sharing_is_enabled( 'email' ) ) )
		return;

	$list_id = get_query_var( 'wl_view' );
	?>
	<form class="wish-list-form" id="edd-wl-share-email-form" method="get" action="">
	<div class="modal-header">
		<h2 id="edd-wl-modal-label">
			<?php echo $messages['modal_share_title']; ?>
		</h2>
		<a class="edd-wl-close" href="#" data-dismiss="modal">
			<i class="glyphicon glyphicon-remove"></i>
			<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
		</a>
	</div>
	<div class="modal-body">
	
	<?php
		// get template
		edd_get_template_part( 'wish-list-email-share' );
	?>

	<input type="hidden" name="submitted" id="submitted" value="true">
	<input name="referrer" type="hidden" value="<?php echo get_the_ID(); ?>">

	<?php wp_nonce_field( 'share_via_email_nonce', 'share_via_email_nonce_field' ); ?>

	</div>
	<div class="modal-footer">
		<a href="#" data-action="edd_wl_share_via_email" data-post-id="<?php echo $list_id; ?>" class="edd-wl-button edd-wl-action edd-wl-share-via-email">
			<span class="label"><?php _e( 'Send Now', 'edd-wish-lists' ); ?></span>
			<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>
		</a>
	</div>
	</form>
<?php }
add_action( 'edd_wl_modal_content', 'edd_wl_modal_share_via_email' );

/**
 * Share list via email thanks message
 *
 * @since 1.0
*/
function edd_wl_modal_share_via_email_success() {
	$messages = edd_wl_messages();

	ob_start();
	?>
	<div class="modal-header">
		<h2 id="edd-wl-modal-label">
			<?php echo $messages['modal_share_success']; ?>
		</h2>
		<a class="edd-wl-close" href="#" data-dismiss="modal">
			<i class="glyphicon glyphicon-remove"></i>
			<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
		</a>
	</div>

	<div class="modal-footer">
		<a class="edd-wl-button edd-wl-success edd-wl-action edd-wl-email-share-success" href="#" data-dismiss="modal">
			<?php echo $messages['modal_option_close']; ?>
		</a>
	</div>

	<?php
		$html = ob_get_clean();
		return apply_filters( 'edd_wl_modal_share_via_email_success', $html );
	?>

<?php }

/**
 * Confirm delete modal for edit wish list page
 *
 * @since 1.0
*/
function edd_wl_list_delete_confirm() {
	// only load on edit page
	if ( ! get_query_var( 'wl_edit' ) )
		return;

	$messages = edd_wl_messages();
	?>
	<div class="modal-header">
		<h2 id="edd-wl-modal-label">
			<?php echo $messages['modal_delete_title']; ?>
		</h2>
		<a class="edd-wl-close" href="#" data-dismiss="modal">
			<i class="glyphicon glyphicon-remove"></i>
			<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
		</a>
	</div>
	<div class="modal-body">
		<p>
			<?php echo $messages['list_delete_confirm']; ?>
		</p>
	</div>
	<div class="modal-footer">
		<a href="#" data-action="edd_wl_delete_list" data-post-id="<?php echo get_query_var( 'wl_edit' ); ?>" class="edd-wl-button edd-wl-action edd-wl-delete-list-confirm">
			<span class="label"><?php echo $messages['modal_button_delete_confirm']; ?></span>
			<span class="edd-loading"><i class="edd-icon-spinner edd-icon-spin"></i></span>
		</a>
	</div>
<?php }
add_action( 'edd_wl_modal_content', 'edd_wl_list_delete_confirm' );

/**
 * Get lists for post ID
 *
 * @since 1.0
*/
function edd_wl_get_wish_lists( $download_id, $price_ids, $items, $price_option_single ) {
	ob_start();
	$messages = edd_wl_messages();

	global $edd_options;
	$text = ! empty( $edd_options[ 'edd_wl_add_to_wish_list' ] ) ? $edd_options[ 'edd_wl_add_to_wish_list' ] : sprintf( __( 'Add to %s', 'edd-wish-lists' ), edd_wl_get_label_singular( true ) );
?>

<div class="modal-header">

	<h2 id="edd-wl-modal-label">
		<?php echo esc_attr( $text ); ?>
	</h2>

   <?php
        $download = $download_id ? get_the_title( $download_id ) : '';

        // price variations
        // EG: Download Name - Option 1, Option 2, Option 3
        if ( edd_has_variable_prices( $download_id ) ) {
        	$price_options = array();

        	foreach ( $items as $item ) {
        		$price_options[] = edd_get_price_name( $item['id'], $item['options'] );
        	}
    	}

    	$options = !empty ( $price_options ) ? ' - ' . implode( ', ', $price_options ) : '';

        // show user what they have selected
        echo '<p>' . sprintf( '%1$s%2$s', $download, $options ) . '</p>';
    ?>

	<a class="edd-wl-close" href="#" data-dismiss="modal">
		<i class="glyphicon glyphicon-remove"></i>
		<span class="hide-text"><?php _e( 'Close', 'edd-wish-lists' ); ?></span>
	</a>
	
</div>

<div class="modal-body">
	<?php
		// show lists this item is already included in
		echo edd_wl_lists_included( $download_id, $items[0]['options'] );
	?>

	<?php if ( ! edd_wl_allow_guest_creation() ) : ?>
		<?php echo '<p>' . $messages['must_login'] . '</p>'; ?>
	<?php else : ?>
		
		<?php
			$list_query 		= edd_wl_get_query();
			$private  			= edd_wl_get_query( 'private' );
		  	$public   			= edd_wl_get_query( 'public' );
			$variable_pricing   = edd_has_variable_prices( $download_id );
			$data_variable      = $variable_pricing ? ' data-variable-price=yes' : 'data-variable-price=no';
			$type               = edd_single_price_option_mode( $download_id ) ? 'data-price-mode=multi' : 'data-price-mode=single';
		?>

		<form method="post" action="" class="form-modal">
		      
			<?php if ( $list_query ) : ?>
		            <p id="current_lists">
		            <input type="radio" checked="" id="existing-list" value="existing-list" name="list-options">
		            <label for="existing-list"><?php echo $messages['modal_option_add_to_existing']; ?></label>

		              <select id="user-lists" name="user-lists">
		            	
		            	<?php
		            	/**
		            	 * Public lists
		            	*/
		            	if ( $public ) : ?>

		            	  <optgroup label="Public">
		            	 
		            	  <?php foreach ( $public as $id ) : ?>
		            	    <option value="<?php echo $id; ?>"><?php echo get_the_title( $id ) . ' ' . edd_wl_get_item_count( $id ); ?></option>  
		            	 <?php endforeach; ?>
		            	  
		            	   </optgroup>

		            	<?php endif; ?>

		               <?php
		              /**
		               * Private lists
		              */
		              if ( $private ) : ?>

		                <optgroup label="Private">
		               
		                <?php foreach ( $private as $id ) : ?>
		                  <option value="<?php echo $id; ?>"><?php echo get_the_title( $id ) . ' ' . edd_wl_get_item_count( $id ); ?></option> 
		                <?php endforeach; ?>
		                
		                 </optgroup>
		              <?php endif; ?>

		              </select>

		            </p>

		    <?php endif; ?>

		             <p>
						<input type="radio" id="new-list" value="new-list" name="list-options">
						<label for="new-list"><?php echo $messages['modal_option_add_new']; ?></label>

						<input type="text" id="list-name" name="list-name" placeholder="<?php echo $messages['modal_option_title']; ?>">

						<select id="list-status" name="list-status">
							<option value="private"><?php echo $messages['list_option_private']; ?></option>
							<option value="publish"><?php echo $messages['list_option_public']; ?></option>
						</select>
		            </p>

		              </div>

		         <?php
		         	// add a hidden input field for each price ID which our next ajax function will grab
		         	foreach ( $price_ids as $id ) { ?>
		         		<input name="edd-wish-lists-post-id" type="hidden" value="<?php echo $id; ?>">
		         	<?php }
		         ?>     

		        
		         <?php if ( $price_option_single ) : ?>
		         <input name="edd-wl-single-price-option" type="hidden" value="yes">
		     	<?php endif; ?>

		         <div class="modal-footer"> 			
        				<?php
        					$args = array(
        						'download_id' 	=> $download_id,
        						'text' 			=> $messages['modal_option_save'],
        						'icon'			=> '',
        						'action'		=> 'edd_add_to_wish_list',
        						'class'			=> 'edd-wl-save edd-wl-action',
        						'style'			=> 'button',
        					);
        					edd_wl_wish_list_link( $args );
        				?>

        				<a class="edd-wl-button edd-wl-success edd-wl-action" href="#" data-dismiss="modal" style="display:none;">
        					<?php echo $messages['modal_option_close']; ?>
        				</a>
	
      				</div>

		            </form>
	<?php endif; ?>
 	
  </div>
	<?php

	$html = ob_get_clean();
	return apply_filters( 'edd_wl_get_wish_lists', $html );
}