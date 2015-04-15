<?php
/**
 * Sharing
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Share via email button
 * @return string
 */
function edd_wl_share_via_email_link() {
	if ( ! edd_wl_sharing_is_enabled( 'email' ) )
		return;

	ob_start();
	?>
	<p>
		<a class="edd-wl-action edd-wl-button" href="#" data-backdrop="static" data-toggle="modal" data-target="#edd-wl-modal"><?php _e( 'Share via email', 'edd-wish-lists' ); ?></a>
	</p>
	<?php

	$html = ob_get_clean();
	return apply_filters( 'edd_wl_share_via_email_link', $html );
}

/**
 * Default Email Template Subject
 *
 * @since 1.0
 * @return string $default_email_body Body of the email
 */
function edd_wl_share_via_email_subject( $sender_name, $referrer ) {

	$subject = sprintf( __( '%s has suggested you look at this %s from %s', 'edd-wish-lists' ), $sender_name, edd_wl_get_label_singular( true ), get_bloginfo('name') );
	
	return apply_filters( 'edd_wl_share_via_email_subject', $subject, $sender_name, $referrer );

}

/**
 * Default Email Template Body
 *
 * @since 1.0
 * @return string $default_email_body Body of the email
 */
function edd_wl_share_via_email_message( $shortlink, $sender_name, $sender_email, $message, $referrer ) {
	// Email body
	$default_email_body = __( "Hi!", "edd-wish-lists" ) . "<br/><br/>";
	$default_email_body .= sprintf( __( "%s has suggested you look at this %s from %s:", "edd-wish-lists" ), $sender_name, edd_wl_get_label_singular( true ), get_bloginfo( 'name' ) ) . "<br/>";
	$default_email_body .= $shortlink . "<br/><br/>";

	if ( $message )
		$default_email_body .= $message . "<br/><br/>";
	
	$default_email_body .= sprintf( __( "Reply to %s by emailing %s", "edd-wish-lists" ), $sender_name, '<a href="mailto:' . $sender_email . '" title="' . $sender_email . '">' . $sender_email . '</a>' ) . "<br/><br/>";
	$default_email_body .= get_bloginfo('name') . "<br/>";
	$default_email_body .= '<a title="' . get_bloginfo( 'name' ) . '" href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'url' ) . '</a>';

	$default_email_body = apply_filters( 'edd_wl_share_via_email_message', $default_email_body, $shortlink, $sender_name, $message, $sender_email, $referrer );

	return $default_email_body;
}

/**
 * Check that each social network is enabled
 * @param  string  $network
 * @return boolean
 * @since  1.0
 */
function edd_wl_sharing_is_enabled( $network = '' ) {
	global $edd_options;

	$networks = edd_get_option( 'edd_wl_services', '' );

	// if network is passed as parameter
	if ( $network ) {
		switch ( $network ) {

			case 'twitter':
				return isset( $networks[$network] );
				break;

			case 'facebook':
				return isset( $networks[$network] );
				break;
				
			case 'googleplus':
				return isset( $networks[$network] );
				break;
				
			case 'linkedin':
				return isset( $networks[$network] );
				break;

			case 'email':
				return isset( $networks[$network] );
				break;			
			
		}
	}
	elseif ( $networks ) {
		return true;
	}

}

/**
 * Main share box that is displayed on the page
 * 
 * @param  string $id 		post/page/download ID
 * @param  string $title 	custom title
 * @param  string $message 	custom message
 * @param  string $tweet 	custom tweet message
 * @return void
 * @since  2.0
 */
function edd_wl_sharing_services() {
	global $edd_options;

	// get list ID
	$list_id = get_query_var( 'wl_view' );

	$sharing_layout = apply_filters( 'edd_wl_sharing_layout', 'vertical' );

	if ( 'vertical' == $sharing_layout ) {
		$twitter_layout    = 'data-count="vertical"';
		$facebook_layout   = 'data-layout="box_count"';
		$googleplus_layout = 'data-size="tall"';
		$linkedin_layout   = 'data-counter="top"';
	}
	elseif ( 'horizontal' == $sharing_layout ) {
		$twitter_layout    = 'data-count="horizontal"';
		$facebook_layout   = 'data-layout="button_count"';
		$googleplus_layout = 'data-size="medium"';
		$linkedin_layout   = 'data-counter="right"';
	}
	else {
		$twitter_layout    = '';
		$facebook_layout   = '';
		$googleplus_layout = '';
		$linkedin_layout   = '';
	}

	// twitter message
	$twitter_text = apply_filters( 'edd_wl_twitter_text', get_the_title( $list_id ) );
	
	// URL to share. Uses shortlink
	$share_url = apply_filters( 'edd_wl_share_url', wp_get_shortlink( get_query_var( 'wl_view' ) ) );

	// get services
	$services = edd_get_option( 'edd_wl_services', '' );

	// return if there are no services
	if ( empty( $services ) )
		return;
	
	ob_start();
	?>
	<div class="<?php echo apply_filters( 'edd_wl_share_classes', 'edd-wl-share' ); ?>">
		<?php do_action( 'edd_wl_before_share_box' ); ?>

		<?php if ( edd_wl_sharing_is_enabled( 'twitter' ) ) :
			$locale 				= apply_filters( 'edd_wl_twitter_locale', 'en' );
			$twitter_button_size 	= apply_filters( 'edd_wl_twitter_button_size', 'medium' );
		?>
		<div class="edd-wl-service twitter">
			<a href="https://twitter.com/share" data-width="100" data-lang="<?php echo $locale; ?>" class="twitter-share-button" <?php echo $twitter_layout; ?> data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo post_permalink( get_query_var( 'wl_view' ) ); ?>" data-url="<?php echo $share_url; ?>" data-text="<?php echo $twitter_text; ?>" data-related=""><?php _e( 'Share', 'edd-wish-lists' ); ?></a>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'facebook' ) ) :
			$data_share = apply_filters( 'edd_wl_facebook_share_button', 'false' );
			$layout 	= isset ( $sharing_layout ) ? 'data-layout="' . $sharing_layout . '"' : '';
		?>
		
		<div class="edd-wl-service facebook">
			<div class="fb-like" data-href="<?php echo post_permalink( get_query_var( 'wl_view' ) ); ?>" data-send="true" data-action="like" <?php echo $facebook_layout; ?> data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'googleplus' ) ) : 
			$google_button_annotation 		= apply_filters( 'edd_wl_googleplus_button_annotation', 'bubble' );
			$google_button_recommendations 	= apply_filters( 'edd_wl_googleplus_button_recommendations', 'false' );
		?>
		<div class="edd-wl-service googleplus">
			<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" <?php echo $googleplus_layout; ?> data-href="<?php echo $share_url; ?>"></div>
		</div>
		<?php endif; ?>

		<?php if ( edd_wl_sharing_is_enabled( 'linkedin' ) ) :
			$locale = apply_filters( 'edd_wl_linkedin_locale', 'en_US' );
		?>
		<div class="edd-wl-service linkedin">
		<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: <?php echo $locale; ?></script>
		<script type="IN/Share" <?php echo $linkedin_layout; ?> data-onSuccess="share" data-url="<?php echo $share_url; ?>"></script>
		</div>
		<?php endif; ?>

		<?php do_action( 'edd_wl_after_share_box' ); ?>
	</div>

<?php 
	$share_box = ob_get_clean();
	return apply_filters( 'edd_wl_share_box', $share_box );
}

/**
 * Set opengraph URL to use URL including query vars
 *
 * @since 1.1
 */
function edd_wl_add_og_url() {

	if ( get_query_var( 'wl_view' ) ) : 

		$post         = get_post( get_query_var( 'wl_view' ) );
		$post_content = $post ? $post->post_content : '';

	?>

<meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?>" />
<meta property="og:url" content="<?php echo trailingslashit( post_permalink( get_query_var( 'wl_view' ) ) ); ?>" />
<meta property="og:description" content="<?php echo esc_attr( $post_content ); ?>" />
<link rel="canonical" href="<?php echo trailingslashit( post_permalink( get_query_var( 'wl_view' ) ) ); ?>" />
<?php endif;

}
add_action( 'wp_head', 'edd_wl_add_og_url' );

/**
 * Removes WP canonical URL when on single wish list page
 *
 * @since 1.1
 */
function edd_wl_wp_head() {

	if ( get_query_var( 'wl_view' ) ) {
		// remove canonical tag
		remove_action( 'wp_head', 'rel_canonical' );	
	}

}
add_action( 'template_redirect', 'edd_wl_wp_head' );

/**
 * Remove opengraph URL if WPSEO is active and we're on single wish list page
 *
 * @since 1.1 
 */
function edd_wl_wpseo_opengraph_url( $url ) {

	if ( get_query_var( 'wl_view' ) ) {
		return '';
	}

	return $url;
	
}
add_filter( 'wpseo_opengraph_url', 'edd_wl_wpseo_opengraph_url' );

/**
 * Remove WPSEO page title when on single wish list page
 *
 * @since 1.1 
 */
function edd_wl_wpseo_title( $title ) {

	// add the title of the wish list to the title tag
	if ( get_query_var( 'wl_view' ) ) {
		return get_the_title( get_query_var( 'wl_view' ) ) . ' - ' . $title;
	}

	return $title;
	
}
add_filter( 'wpseo_title', 'edd_wl_wpseo_title' );

/**
 * Remove opengraph description if WPSEO is active and we're on single wish list page
 *
 * @since 1.1 
 */
function edd_wl_wpseo_opengraph_desc( $desc ) {

	if ( get_query_var( 'wl_view' ) ) {
		return '';
	}

	return $desc;
	
}
add_filter( 'wpseo_opengraph_desc', 'edd_wl_wpseo_opengraph_desc' );

/**
 * Remove opengraph title if WPSEO is active and we're on single wish list page
 *
 * @since 1.1 
 */
function edd_wl_wpseo_opengraph_title( $title ) {

	if ( get_query_var( 'wl_view' ) ) {
		return '';
	}

	return $title;
	
}
add_filter( 'wpseo_opengraph_title', 'edd_wl_wpseo_opengraph_title' );

/**
 * Remove canonical URL from WPSEO if active and we're on the single wish list page
 *
 * @since 1.1 
 */
function edd_wl_wpseo_canonical( $url ) {

	if ( get_query_var( 'wl_view' ) ) {
		return '';
	}

	return $url;
	
}
add_filter( 'wpseo_canonical', 'edd_wl_wpseo_canonical' );

/**
 * Jetpack shortlinks filter the standard WP shortlinks. This breaks the links on the single wish list pages. This resets the links back to how they should be.
 *
 * @since 1.1
 */
function edd_wl_jetpack_shortlinks( $shortlink ) {

	if ( get_query_var( 'wl_view' ) ) {
		return home_url( '?p=' . get_query_var( 'wl_view' ) );
	}

	return $shortlink;
}
add_filter( 'get_shortlink', 'edd_wl_jetpack_shortlinks', 100 );

/**
 * Print scripts
 *
 * @since 1.0
*/
function edd_wl_sharing_print_scripts() {
	global $edd_options;

	if ( ! ( edd_wl_is_view_page() ) )
		return;

	?>
	<script type="text/javascript">

	<?php 
	/**
	 * Twitter
	 *
	 * @since 1.0
	*/
	if ( edd_wl_sharing_is_enabled( 'twitter' ) ) : 
		?>
	  	window.twttr = (function (d,s,id) {
		  var t, js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
		  js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
		  return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
		}(document, "script", "twitter-wjs"));

		twttr.ready(function (twttr) {
		    twttr.events.bind('tweet', function (event) {
		        jQuery.event.trigger({
		            type: "listShared",
		            url: event.target.baseURI
		        });
		    });
		});

		<?php endif; ?>

		<?php 
		/**
		 * Google +
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'googleplus' ) ) : 
			$locale = apply_filters( 'edd_wl_googleplus_locale', 'en-US' );
		?>
			window.___gcfg = {
			  lang: '<?php echo $locale; ?>',
			  parsetags: 'onload'
			};

			(function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();

			function plusOned(obj) {
				console.log(obj);
				jQuery.event.trigger({
				    type: "listShared",
				    url: obj.href
				});
			}
		<?php endif; ?>

		<?php 
		/**
		 * LinkedIn
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'linkedin' ) ) : ?>
			function share(url) {
				console.log(url);
			 	jQuery.event.trigger({
		            type: "listShared",
		            url: url
		        });
			}
		
		<?php endif; ?>

		<?php
		/**
		 * Facebook
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled( 'facebook' ) ) : 
			// defaults to en_US if left blank
			$locale = apply_filters( 'edd_wl_facebook_locale', 'en_US' );
			?>

			(function(d, s, id) {
			     var js, fjs = d.getElementsByTagName(s)[0];
			     if (d.getElementById(id)) {return;}
			     js = d.createElement(s); js.id = id;
			     js.src = "//connect.facebook.net/<?php echo $locale; ?>/all.js";
			     fjs.parentNode.insertBefore(js, fjs);
			 }(document, 'script', 'facebook-jssdk'));

			window.fbAsyncInit = function() {
			    // init the FB JS SDK
			    FB.init({
			      status	: true,
			      cookie	: true,                               
			      xfbml		: true                              
			    });

			    FB.Event.subscribe('edge.create', function(href, widget) {
			        jQuery.event.trigger({
			            type: "listShared",
			            url: href
			        });     
			    });
			};
		<?php endif; ?>

		<?php 
		/**
		 * Listen for the productShared event
		 *
		 * @since 1.0
		*/
		if ( edd_wl_sharing_is_enabled() ) : ?>

		jQuery(document).ready(function ($) {

			jQuery(document).on( 'listShared', function(e) {

				if( e.url == window.location.href ) {

			    	var postData = {
			            action: 'share_list',
			            list_id: <?php echo get_the_ID(); ?>, 
			            nonce: edd_wl_scripts.ajax_nonce
			        };

			    	$.ajax({
			            type: "POST",
			            data: postData,
			            dataType: "json",
			            url: edd_wl_vars.ajaxurl,
			            success: function ( share_response ) {

		            	}
				        }).fail(function (data) {
				            console.log( data );
				        });
				}
			});
		});
	<?php endif; ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'edd_wl_sharing_print_scripts' );