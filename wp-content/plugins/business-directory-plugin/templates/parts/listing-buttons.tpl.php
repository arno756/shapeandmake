<div class="listing-actions cf">
<?php if ($view == 'single'): ?>
    <?php if (wpbdp_user_can('edit', $listing_id)): ?>
    <form action="<?php echo wpbdp_get_page_link('editlisting', $listing_id); ?>" method="POST"><input type="submit" name="" value="<?php _ex('Edit', 'templates', 'WPBDM'); ?>" class="button edit-listing" /></form>
    <?php endif; ?>
    <?php if (wpbdp_user_can('upgrade-to-sticky', $listing_id)): ?>
    <form action="<?php echo wpbdp_get_page_link('upgradetostickylisting', $listing_id); ?>" method="POST"><input type="submit" name="" value="<?php _ex('Upgrade Listing', 'templates', 'WPBDM'); ?>" class="button upgrade-to-sticky" /></form>
    <?php endif; ?>
    <?php if (wpbdp_user_can('delete', $listing_id)): ?>
    <form action="<?php echo wpbdp_get_page_link('deletelisting', $listing_id); ?>" method="post"><input type="submit" name="" value="<?php _ex('Delete', 'templates', 'WPBDM'); ?>" class="button delete-listing" data-confirmation-message="<?php _ex('Are you sure you wish to delete this listing?', 'templates', 'WPBDM'); ?>" /></form>
    <?php endif; ?>
    <?php if (wpbdp_get_option('show-directory-button')) :?>
     <input type="button" value="<?php echo __('← Back to Directory', 'WPBDM'); ?>" onclick="window.location.href = '<?php echo wpbdp_get_page_link('main'); ?>'" class="wpbdp-hide-on-mobile button back-to-dir" />
     <input type="button" value="←" onclick="window.location.href = '<?php echo wpbdp_get_page_link('main'); ?>'" class="wpbdp-show-on-mobile button back-to-dir" />
    <?php endif; ?>
<?php elseif ($view == 'excerpt'): ?>
<?php if (wpbdp_user_can('view', $listing_id)): ?><a class="button view-listing" href="<?php the_permalink(); ?>"><?php _ex('View', 'templates', 'WPBDM'); ?></a><?php endif; ?>
<?php if (wpbdp_user_can('edit', $listing_id)): ?><a class="button edit-listing" href="<?php echo wpbdp_get_page_link('editlisting', $listing_id); ?>"><?php _ex('Edit', 'templates', 'WPBDM'); ?></a><?php endif; ?>
<?php if (wpbdp_user_can('delete', $listing_id)): ?><a class="button delete-listing" href="<?php echo wpbdp_get_page_link('deletelisting', $listing_id); ?>"><?php _ex('Delete', 'templates', 'WPBDM'); ?></a><?php endif; ?>

<!--<?php if (wpbdp_user_can('view', $listing_id)): ?><input type="button" value="<?php _ex('View', 'templates', 'WPBDM'); ?>" class="button view-listing" onclick="window.location.href = '<?php the_permalink(); ?>' " /><?php endif; ?>-->
<!--<?php if (wpbdp_user_can('edit', $listing_id)): ?><form action="<?php echo wpbdp_get_page_link('editlisting', $listing_id); ?>" method="POST"><input type="submit" name="" value="<?php _ex('Edit', 'templates', 'WPBDM'); ?>" class="edit-listing" /></form><?php endif; ?>-->
<!--<?php if (wpbdp_user_can('delete', $listing_id)): ?><form action="<?php echo wpbdp_get_page_link('deletelisting', $listing_id); ?>" method="POST"><input type="submit" name="" value="<?php _ex('Delete', 'templates', 'WPBDM'); ?>" class="delete-listing" data-confirmation-message="<?php _ex('Are you sure you wish to delete this listing?', 'templates', 'WPBDM'); ?>" /></form><?php endif; ?>-->
<?php endif; ?>
</div>
