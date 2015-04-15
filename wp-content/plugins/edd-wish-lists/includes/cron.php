<?php
/**
 * Cron events
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Delete posts function
 */
function edd_wl_delete_lists() {  
    $args = array(
        'post_type' => 'edd_wish_list',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'post_status' => array( 'publish', 'private' )
    );

    // get all lists that are 1 month old
    $args['date_query'][] = apply_filters( 'edd_wl_delete_lists_date_query', 
        array(
            'column'    => 'post_date_gmt',
            'after'     => '1 month ago',
        ) 
    );

    $posts = get_posts( $args );

    if ( $posts ) {
       foreach( $posts as $post ) {
           
           // only delete posts that have a post_author of 0
           if ( $post->post_author != 0 )
               continue;

           wp_delete_post( $post->ID );
       } 
    }
}
add_action( 'edd_weekly_scheduled_events', 'edd_wl_delete_lists' );