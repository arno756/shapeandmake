<?php
/**
 * Cross-sell and Upsell
 *
 * @package Marketify
 */

/**
 * Cross Sell/Up Sell
 *
 * @since Marketify 1.0
 */
add_filter( 'edd_csau_show_excerpt', '__return_false' );
add_filter( 'edd_csau_show_price', '__return_false' );