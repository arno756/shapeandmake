<div class="wpbdp-note"><p>
<?php _e( 'Set a field to "0" for Unlimited characters, otherwise enter a specific character limit to be enforced for the "Long Description" (content) field at that level.',
          'wpbdp-featured-levels' );
?></p></div>

<table class="wpbdp-restrictions-table">
    <thead>
        <tr>
            <th colspan="2" class="capability-name">
                <a href="#" class="toggle-link"><?php _e( 'Long Description field character limit', 'wpbdp-featured-levels' ); ?></a>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if ( $level_config ): ?>
        <tr>
            <td class="subheader">
                <?php _e( 'Listing Level', 'wpbdp-featured-levels' ); ?>
            </td>
            <td class="subheader">
                <?php _e( 'Limit', 'wpbdp-featured-levels' ); ?>
            </td>
        </tr>
        <?php foreach ( $level_config as $lc ): ?>
            <tr>
                <td><?php echo esc_html( $lc->level->name ); ?></td>
                <td>
                    <input type="text" name="levels[<?php echo $lc->level->id; ?>]" value="<?php echo intval( $lc->char_limit ); ?>" size="2" />
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ( $fees_config ): ?>
        <tr>
            <td class="subheader">
                <?php _e( 'Listing Fee', 'wpbdp-featured-levels' ); ?>
            </td>
            <td class="subheader">
                <?php _e( 'Limit', 'wpbdp-featured-levels' ); ?>
            </td>
        </tr>
        <?php foreach ( $fees_config as $fc ): ?>
            <tr>
                <td><?php echo esc_html( $fc->fee->label ); ?></td>
                <td>
                    <input type="text" name="fees[<?php echo $fc->fee->id; ?>]" value="<?php echo intval( $fc->char_limit ); ?>" size="2" />
                </td>
            </tr>    
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<table class="wpbdp-restrictions-table">
    <thead>
        <tr>
            <th class="capability-name">
                <a href="#" class="toggle-link"><?php _e( 'Misc. Settings', 'wpbdp-featured-levels' ); ?></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <label>
                    <input type="checkbox" name="nofollow_on_featured" value="1" <?php echo $nofollow_on_featured ? 'checked="checked"' : ''; ?> />
                    <?php _ex( 'Make featured listings links crawable by search engines (remove nofollow)?', 'wpbdp-featured-levels' ); ?>
                </label>
                <span class="description"><?php _ex( 'This setting overrides any field-specific configuration.', 'wpbdp-featured-levels' ); ?></span>
            </td>
        </tr>
    </tbody>
</table>