<div id="wpbdp-map-<?php echo $settings['map_uid']; ?>" class="wpbdp-map <?php echo $settings['map_size']; ?>" style="<?php echo $settings['map_style_attr']; ?>">
</div>

<script type="text/javascript">
var map = new wpbdp.googlemaps.Map( 'wpbdp-map-<?php echo $settings['map_uid']; ?>',
                                    <?php echo json_encode( $settings ); ?> );
map.setLocations( <?php echo json_encode( $locations ); ?> );
map.render();
</script>
