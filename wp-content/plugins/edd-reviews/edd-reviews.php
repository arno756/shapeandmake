<?php
/**
 * Plugin Name: Easy Digital Downloads - Product Reviews
 * Plugin URI: http://easydigitaldownloads.com/extension/reviews/
 * Description: A fully featured reviewing system for Easy Digital Downloads.
 * Author: Sunny Ratilal
 * Version: 1.3.9
 * Requires at least: 3.5
 * Tested up to: 4.0
 *
 * Text Domain: edd-reviews
 * Domain Path: languages
 *
 * Copyright 2014 Sunny Ratilal
 *
 * @package		EDD_Reviews
 * @category 	Core
 * @author		Sunny Ratilal
 * @version 	1.3.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Reviews' ) ) :

/**
 * EDD_Reviews Class
 *
 * @package	EDD_Reviews
 * @since	1.0
 * @version	1.2
 * @author 	Sunny Ratilal
 */
final class EDD_Reviews {
	/**
	 * EDD Reviews uses many variables, several of which can be filtered to
	 * customize the way it operates. Most of these variables are stored in a
	 * private array that gets updated with the help of PHP magic methods.
	 *
	 * @var array
	 * @see EDD_Reviews::setup_globals()
	 * @since 1.0
	 */
	private $data;

	/**
	 * Holds the instance
	 *
	 * Ensures that only one instance of EDD Reviews exists in memory at any one
	 * time and it also prevents needing to define globals all over the place.
	 *
	 * TL;DR This is a static property property that holds the singleton instance.
	 *
	 * @var object
	 * @static
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Boolean whether or not to use the singleton, comes in handy
	 * when doing testing
	 *
	 * @var bool
	 * @static
	 * @since 1.0
	 */
	public static $testing = false;

	/**
	 * Holds the version number
	 *
	 * @var string
	 * @since 1.0
	 */
	public $version = '1.3.9';

	/**
	 * Get the instance and store the class inside it. This plugin utilises
	 * the PHP singleton design pattern.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @access public
	 * @see edd_reviews();
	 * @uses EDD_Reviews::setup_globals() Setup the globals needed
	 * @uses EDD_Reviews::load_classes() Loads all the classes
	 * @uses EDD_Reviews::hooks() Setup hooks and actions
	 * @return object self::$instance Instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Reviews ) || self::$testing ) {
			self::$instance = new EDD_Reviews;
			self::$instance->setup_globals();
			self::$instance->load_classes();
			self::$instance->hooks();
			self::$instance->updater();
		}

		return self::$instance;
	}

	/**
	 * Constructor Function
	 *
	 * @since 1.0
	 * @access protected
	 * @see EDD_Reviews::init()
	 * @see EDD_Reviews::activation()
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'init', array( $this, 'init' ) );
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
	}

	/**
	 * Sets up the constants/globals used
	 *
	 * @since 1.0
	 * @static
	 * @access public
	 */
	private function setup_globals() {
		// File Path and URL Information
		$this->file          = __FILE__;
		$this->basename      = apply_filters( 'edd_reviews_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_url    = plugin_dir_url( __FILE__ );
		$this->plugin_path   = plugin_dir_path( __FILE__ );
		$this->lang_dir      = apply_filters( 'edd_reviews_lang_dir',         trailingslashit( $this->plugin_path . 'languages' ) );

		// Assets
		$this->assets_dir    = apply_filters( 'edd_reviews_assets_dir',       trailingslashit( $this->plugin_path . 'assets'    ) );
		$this->assets_url    = apply_filters( 'edd_reviews_assets_url',       trailingslashit( $this->plugin_url  . 'assets'    ) );

		// Classes
		$this->classes_dir   = apply_filters( 'edd_reviews_classes_dir',      trailingslashit( $this->plugin_path . 'classes'   ) );
		$this->classes_url   = apply_filters( 'edd_reviews_classes_url',      trailingslashit( $this->plugin_url  . 'classes'   ) );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-reviews' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'edd-reviews' ), '1.0' );
	}

	/**
	 * Magic method for checking if custom variables have been set
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Magic method for getting define_syslog_variables(oid)
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	/**
	 * Magic method for setting variables
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Magic method for unsetting variables
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __unset( $key ) {
		if ( isset( $this->data[ $key ] ) )
			unset( $this->data[ $key ] );
	}

	/**
	 * Magic method to prevent notices and errors from invalid method calls
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return void
	 */
	public function __call( $name = '', $args = array() ) {
		unset( $name, $args );
		return null;
	}

	/**
	 * Reset the instance of the class
	 *
	 * @since 1.0
	 * @access public
	 * @static
	 */
	public static function reset() {
		self::$instance = null;
	}

	/**
	 * Function fired on init
	 *
	 * This function is called on WordPress 'init'. It's triggered from the
	 * constructor function.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::load_plugin_textdomain()
	 * @uses EDD_Reviews::add_shortcodes()
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'edd_reviews_before_init' );

		$this->load_plugin_textdomain();

		$this->add_shortcodes();

		do_action( 'edd_reviews_after_init' );
	}

	/**
	 * Loads Classes
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function load_classes() {
		require $this->classes_dir . 'shortcodes/class-edd-reviews-shortcode-review.php';
		require $this->classes_dir . 'widgets/class-reviews-widget.php';
		require $this->classes_dir . 'widgets/class-featured-review-widget.php';
		require $this->classes_dir . 'widgets/class-per-product-reviews-widget.php';
	}

	/**
	 * Load Plugin Text Domain
	 *
	 * Looks for the plugin translation files in certain directories and loads
	 * them to allow the plugin to be localised
	 *
	 * @since 1.0
	 * @access public
	 * @return bool True on success, false on failure
	 */
	public function load_plugin_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale',  get_locale(), 'edd-reviews' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd-reviews', $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;

		if ( file_exists( $mofile_local ) ) {
			// Look in the /wp-content/plugins/edd-reviews/languages/ folder
			load_textdomain( 'edd-reviews', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'edd-reviews', false, $this->lang_dir );
		}

		return false;
	}

	/**
	 * Activation function fires when the plugin is activated.
	 *
	 * This function is fired when the activation hook is called by WordPress,
	 * it flushes the rewrite rules and disables the plugin if EDD isn't active
	 * and throws an error.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return void
	 */
	public function activation() {
		flush_rewrite_rules();

		if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
			if ( is_plugin_active( $this->basename ) ) {
				deactivate_plugins( $this->basename );
				unset( $_GET[ 'activate' ] );
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			}
		}
	}

	/**
	 * Adds all the shortcodes
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function add_shortcodes() {
		add_shortcode( 'review', array( 'EDD_Reviews_Shortcode_Review', 'render' ) );
	}

	/**
	 * Adds all the hooks/filters
	 *
	 * The plugin relies heavily on the use of hooks and filters and modifies
	 * default WordPress behaviour by the use of actions and filters which are
	 * provided by WordPress.
	 *
	 * Actions are provided to hook on this function, before the hooks and filters
	 * are added and after they are added. The class object is passed via the action.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function hooks() {
		do_action_ref_array( 'edd_reviews_before_setup_actions', array( &$this ) );

		/** Actions */
		add_action( 'comment_post',                            array( $this, 'save_review_meta'   ) );
		add_action( 'add_meta_boxes',                          array( $this, 'disable_trackbacks' ) );
		add_action( 'add_meta_boxes',                          array( $this, 'change_meta_boxes'  ) );
		add_action( 'wp_enqueue_scripts',                      array( $this, 'load_styles'        ) );
		add_action( 'wp_enqueue_scripts',                      array( $this, 'load_scripts'       ) );
		add_action( 'admin_enqueue_scripts',                   array( $this, 'admin_scripts'      ) );
		add_action( 'the_content',                             array( $this, 'microdata'          ) );
		add_action( 'admin_init',                              array( $this, 'activate_license'   ) );
		add_action( 'wp_before_admin_bar_render',              array( $this, 'admin_bar_menu'     ) );
		add_action( 'edd_reviews_review_display',              array( $this, 'render_review'      ), 10, 3 );
		add_action( 'widgets_init',                            array( $this, 'register_widgets'   ) );
		add_action( 'init',                                    array( $this, 'process_vote'       ) );
		add_action( 'wp_ajax_edd_reviews_process_vote',        array( $this, 'process_ajax_vote'  ) );
		add_action( 'wp_ajax_nopriv_edd_reviews_process_vote', array( $this, 'process_ajax_vote'  ) );
		add_action( 'wp_dashboard_setup',                      array( $this, 'dashboard_widgets'  ) );
		add_action( 'load-comment.php',                        array( $this, 'add_meta_boxes'     ) );
		add_action( 'edit_comment',                            array( $this, 'update_review_meta' ) );
		add_action( 'init',                                    array( $this, 'tinymce_button'     ) );
		add_action( 'init',                                    array( $this, 'process_mce_dialog' ) );
		add_action( 'comment_form_comments_closed',            array( $this, 'comments_closed'    ) );

		/** Filters */
		add_filter( 'preprocess_comment',                      array( $this, 'check_author'       ) );
		add_filter( 'edd_download_supports',                   array( $this, 'enable_comments'    ) );
		add_filter( 'preprocess_comment',                      array( $this, 'check_review_title' ) );
		add_filter( 'preprocess_comment',                      array( $this, 'check_rating'       ) );
		add_filter( 'edd_settings_styles',                     array( $this, 'styles_settings'    ) );
		add_filter( 'edd_settings_extensions',                 array( $this, 'misc_settings'      ) );
		add_filter( 'manage_edit-comments_columns',            array( $this, 'custom_columns'     ) );
		add_filter( 'manage_comments_custom_column',           array( $this, 'custom_column_data' ), 10, 2 );
		add_filter( 'comments_open',                           array( $this, 'open_all_comments'  ), 10, 2 );
		add_filter( 'edd_api_valid_query_modes',               array( $this, 'register_api_mode'  ) );
		add_filter( 'edd_api_output_data',                     array( $this, 'api_output'         ), 10, 3 );
		add_filter( 'query_vars',                              array( $this, 'query_vars'         ) );
		add_filter( 'plugin_row_meta',                         array( $this, 'plugin_links'       ), 10, 2 );
		add_filter( 'comment_text',                            array( $this, 'comment_rating'     ), 10, 2 );
		add_filter( 'comment_form_defaults',                   array( $this, 'reviews_form'       ) );
		add_filter( 'shopfront_comment_form',                  array( $this, 'reviews_form'       ) );
		add_filter( 'comments_open',                           array( $this, 'author_purchased'   ), 20, 1 );

		do_action_ref_array( 'edd_reviews_after_setup_actions', array( &$this ) );
	}

	/**
	 * Register Widgets
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function register_widgets() {
		register_widget( 'EDD_Reviews_Widget_Reviews' );
		register_widget( 'EDD_Reviews_Widget_Featured_Review' );
		register_widget( 'EDD_Reviews_Per_Product_Reviews_Widget' );
	}

	/**
	 * Get current commenter's name, email, and URL.
	 *
	 * @since 1.2
	 * @access public
	 * @return array Comment author, email, url respectively.
	 */
	public function get_current_reviewer() {
		$user = wp_get_current_user();

		$comment_author = $user->exists() ? $user->display_name : '';
		$comment_author_email = $user->user_email;

		return array( 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email );
	}

	/**
	 * First conditional to check whether the author has not purchased the
	 * download, and then to display the form based on the result.
	 *
	 * @since 1.3
	 * @access public
	 * @global $post
	 * @param string $status
	 * @return string $status
	 */
	public function author_purchased( $status ) {
		global $post;

		if ( 'download' != get_post_type() )
			return $status;

		return $this->maybe_restrict_form();
	}

	/**
	 * Message and form to display when the comments are closed
	 *
	 * @since 1.3
	 * @access public
	 * @return void
	 */
	public function comments_closed() {

		if( ! is_singular( 'download' ) )
			return;

		$output  = '<div id="respond" class="comment-respond">';
		$output .= '<div class="edd-reviews-must-log-in  comment-form" id="commentform">';
		$output .= '<p class="edd-reviews-not-logged-in">' . apply_filters( 'edd_reviews_user_logged_out_message', sprintf( __( 'You must log in and be a buyer of this %s to submit a review.' ), strtolower( edd_get_label_singular() ) ) ) . '</p>';

		if ( ! is_user_logged_in() )
			$output .= wp_login_form( array( 'echo' => false ) );

		$output .= '</div><!-- /.edd-reviews-must-log-in -->';
		$output .= '</div><!-- /#respond -->';

		echo apply_filters( 'edd_reviews_user_not_buyer', $output );
	}

	/**
	 * Reviews form
	 *
	 * This function is called by the reviews template and overrides the default
	 * comments form by replacing the fields in order for reviews to be placed.
	 *
	 * @since 1.0
	 * @global $post
	 * @access public
	 * @return void
	 */
	public function reviews_form( $args ) {
		global $post;

		if ( 'download' != get_post_type() )
			return $args;

		$commenter = wp_get_current_commenter();

		$form = array(
			'title_reply'          => apply_filters( 'edd_reviews_leave_a_review_text',  __( 'Leave a Review', 'edd-reviews' ) ),
			'title_reply_to'       => '',
			'must_log_in'          => $this->display_login_form(),
			'comment_notes_before' => $this->maybe_show_review_breakdown( $post->ID ),
			'comment_notes_after'  => '',
			'fields' => array(
				'author'           => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'edd-reviews' ) . '<span class="required">*</span></label>' .
						          	'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
				'email'            => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'edd-reviews' ) . '<span class="required">*</span></label>' .
						          	'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
			),
			'label_submit'         => __( 'Submit Review', 'edd-reviews' ),
			'logged_in_as'         => '',
			'comment_field'        => '',
			'cancel_reply_link'    => '',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'format'               => 'html5'
		);

		$form['comment_field'] = apply_filters( 'edd_reviews_review_form_template', '
			<p class="comment_form_review_title">
				<label for="edd_review_title">' . __( 'Review Title', 'edd-reviews' ) . '<span class="required">*</span></label>
				<input type="text" name="edd_review_title" id="edd_review_title" value="" size="30" aria-required="true" />
			</p>

			<p class="comment_form_rating">
				<label for="edd_rating">' . __( 'Rating', 'edd-reviews' ) . '<span class="required">*</span></label>

				' . apply_filters( 'edd_reviews_rating_box', '
				<span class="edd_reviews_rating_box">
					<span class="edd_star_rating"></span>
					<span class="edd_ratings">
						<a class="edd_rating" href="" data-rating="5"><span></span></a>
						<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="5"/>5&nbsp;</span>

						<a class="edd_rating" href="" data-rating="4"><span></span></a>
						<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="4"/>4&nbsp;</span>

						<a class="edd_rating" href="" data-rating="3"><span></span></a>
						<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="3"/>3&nbsp;</span>

						<a class="edd_rating" href="" data-rating="2"><span></span></a>
						<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="2"/>2&nbsp;</span>

						<a class="edd_rating" href="" data-rating="1"><span></span></a>
						<span class="edd_show_if_no_js"><input type="radio" name="edd_rating" id="edd_rating" value="1"/>1&nbsp;</span>
					</span>
				</span>' ) . '
			</p>

			<p class="comment-form-comment">
				<label for="comment">' . __( 'Review', 'edd-reviews' ) . '<span class="required">*</span></label>
				<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
			</p>

			<input type="hidden" id="edd_rating" name="edd_rating" />
			<input type="hidden" name="edd_review" value="true" />
		' );

		return wp_parse_args( $form, $args );
	}

	/**
	 * Checks if multiple reviews have been disabled and then verifies
	 * if the author has already posted a review for this download (product).
	 * This function queries the database for any reviews by taking the
	 * comment_post_ID and comment_author_email and if anything is returned, execution
	 * of the comment addition will fail with wp_die().
	 *
	 * @since 1.2
	 * @access public
	 * @param  array $commentdata All the comment data sent via $_POST
	 * @global array $edd_options Used to access the EDD Options
	 * @return object|bool Returns an instance of wp_die() or the comment data
	 */
	public function check_author( $commentdata ) {
		global $edd_options;

		if ( isset( $edd_options['edd_reviews_disable_multiple_reviews'] ) ) {
			$args = array(
				'author_email' => $commentdata['comment_author_email'],
				'post_id'      => $commentdata['comment_post_ID'],
				'meta_key'     => 'edd_review_title'
			);

			$comments = get_comments( $args );

			if ( $comments ) {
				wp_die(
					sprintf( __( 'You are only allowed to post one review for this %s. Multiple reviews have been disabled.', 'edd-reviews' ), strtolower( edd_get_label_singular() ) ),
					__( 'Multiple Reviews Not Allowed', 'edd-reviews' ),
					array( 'back_link' => true )
				);
			} else {
				return $commentdata;
			}
		} else {
			return $commentdata;
		}
	}

	/**
	 * Checks if a review title has been entered otherwise dies with an error
	 *
	 * @since 1.0
	 * @access public
	 * @param  array $commentdata All the comment data sent via $_POST
	 * @return array $commentdata All the comment data sent via $_POST
	 */
	public function check_review_title( $commentdata ) {
		if ( isset( $_POST['edd_review'] ) && empty( $_POST['edd_review_title'] ) ) {
			wp_die( sprintf( __( '%sERROR:%s You did not add a review title.', 'edd-reviews' ), '<strong>', '</strong>' ), __( 'Error', 'edd-reviews' ), array( 'back_link' => true ) );
		}

		return $commentdata;
	}

	/**
	 * Checks if a rating has been made otherwise dies with an error
	 *
	 * @since 1.0
	 * @access public
	 * @param  array $commentdata All the comment data sent via $_POST
	 * @return array $commentdata All the comment data sent via $_POST
	 */
	public function check_rating( $commentdata ) {
		if ( isset( $_POST['edd_review'] ) && ! isset( $_POST['edd_rating'] ) && is_int( $_POST['edd_rating'] )  && ( ! $_POST['edd_rating'] > 5 || $_POST['edd_rating'] < 0 ) ) {
			wp_die( sprintf( __( '%sERROR:%s You did not add a rating or the rating you supplied was not validated.', 'edd-reviews' ), '<strong>', '</strong>' ), __( 'Error', 'edd-reviews' ), array( 'back_link' => true ) );
		}

		return $commentdata;
	}

	/**
	 * Save the Review Meta Data
	 *
	 * @since 1.0
	 * @access public
	 * @param int $comment_id Comment ID
	 * @return void
	 */
	public function save_review_meta( $comment_id ) {

		$comment = get_comment( $comment_id );

		if( ! $comment ) {
			return; // Get out if not a valid comment
		}

		if( ! empty( $comment->comment_parent ) ) {
			return; // Get out if this is a comment reply
		}

		$_POST['edd_rating'] = ( ! empty( $_POST['edd_rating'] ) ) ? $_POST['edd_rating'] : '5';

		/** Check if a rating has been submitted */
		if ( isset( $_POST['edd_review'] ) && isset( $_POST['edd_rating'] ) && ! empty( $_POST['edd_review_title'] ) ) {
			$rating = wp_filter_nohtml_kses( $_POST['edd_rating'] );
			add_comment_meta( $comment_id, 'edd_rating', $rating );
		}

		/** Check if a review title has been submitted */
		if ( isset( $_POST['edd_review'] ) && isset( $_POST['edd_review_title'] ) && ! empty( $_POST['edd_review_title'] ) ) {
			$review_title = sanitize_text_field( wp_filter_nohtml_kses( esc_html( $_POST['edd_review_title'] ) ) );
			add_comment_meta( $comment_id, 'edd_review_title', $review_title );
		}
	}

	/**
	 * Microdata
	 *
	 * @since 1.0
	 * @access public
	 * @global object $post Used to access the post data
	 *
	 * @uses EDD_Reviews::average_rating()
	 *
	 * @param  string $content Content of the post
	 * @return string $content Content of the post with the microdata
	 */
	public function microdata( $content ) {
		global $post;

		// Bail if we're not on a download page
		if ( ! is_singular( 'download' ) )
			return $content;

		do_action( 'edd_reviews_microdata_before' );
		?>
		<div style="display:none" class="edd-review-microdata" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<span itemprop="ratingValue"><?php echo $this->average_rating(); ?></span>
			<span itemprop="reviewCount"><?php echo $this->count_reviews(); ?></span>
		</div>
		<?php
		do_action( 'edd_reviews_microdata_after' );
		return $content;
	}

	/**
	 * Get Average Rating
	 *
	 * @since 1.0
	 * @access public
	 * @global object Used to access the post data
	 *
	 * @param  bool $echo Whether to echo the result or return it
	 * @return string $average Returns the average rating
	 */
	public function average_rating( $echo = true ) {
		global $post;

		$reviews = get_comments( apply_filters( 'edd_reviews_average_rating_query_args', array(
			'post_id' => $post->ID
		) ) );

		$total         = 0;
		$total_ratings = 0;

		foreach ( $reviews as $review ) {

			$rating = get_comment_meta( $review->comment_ID, 'edd_rating', true );
			if ( $rating == '' ) {
				continue; // Skip comments that aren't reviews
			}
			$total++;
			$total_ratings += $rating;
		}

		if ( 0 == $total )
			$total = 1;
		$average = round( $total_ratings / $total, 1 );

		if ( $echo ) {
			echo $average;
		} else {
			return $average;
		}
	}

	/**
	 * Enable Reviews on all Downloads
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array $support What the Downloads post type supports (e.g. title)
	 * @return array Merged array with comments support enabled for downloads
	 */
	public function enable_comments( $supports ) {
		return array_merge( $supports, array( 'comments' ) );
	}

	/**
	 * Disable Trackbacks
	 *
	 * This function removes the Trackbacks meta box from the Add/Edit Download
	 * screen as it's not the sole purpose of this plugin and if any were to be
	 * made, they wouldn't render correctly as the plugin doesn't provide
	 * support for trackbacks.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function disable_trackbacks() {
		remove_meta_box( 'trackbacksdiv', 'download', 'normal' );
	}

	/**
	 * Edit Meta Boxes
	 *
	 * The Comments meta box on the Add/Edit Download screen is renamed here and
	 * the callback function for the comments meta box is also changed.
	 *
	 * @since 1.0
	 * @access public
	 * @global array Used to edit the admin meta boxes
	 * @return void
	 */
	public function change_meta_boxes() {
		global $wp_meta_boxes;

		/**
		 * The comment status box (whether comments are open or not) does not
		 * need to be displayed because a filter is used to force open all
		 * comments for the downloads post type
		 */
		unset( $wp_meta_boxes['download']['normal']['core']['commentstatusdiv'] );

		/** Titles */
		$wp_meta_boxes['download']['normal']['core']['commentsdiv']['title'] = __( 'Reviews', 'edd-reviews' );

		/** Callbacks */
		$wp_meta_boxes['download']['normal']['core']['commentsdiv']['callback'] = array( $this, 'post_comment_meta_box' );
		$wp_meta_boxes['download']['normal']['core']['commentsdiv']['args'] = array();
		$wp_meta_boxes['download']['normal']['core']['commentsdiv']['id'] = 'edd_reviews';
	}

	/**
	 * Override the default comment status meta box
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param object $post Post Object
	 * @return void
	 */
	public function post_comment_status_meta_box( $post ) {
		?>
		<input name="advanced_view" type="hidden" value="1" />
		<p class="meta-options">
			<label for="comment_status" class="selectit">
				<input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked( $post->comment_status, 'open' ); ?> /> <?php _e( 'Allow reviews.', 'edd-reviews' ) ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Override the default comments meta box on Add/Edit screen
	 *
	 * @since 1.0
	 * @access public
	 * @global object Used to query the database using the WordPress Database API
	 *
	 * @param object $post Current Post
	 * @return void
	 */
	public function post_comment_meta_box( $post ) {
		global $wpdb;

		wp_nonce_field( 'get-comments', 'add_comment_nonce', false );

		$total = get_comments( array( 'post_id' => $post->ID, 'number' => 1, 'count' => true ) );
		$wp_list_table = _get_list_table( 'WP_Post_Comments_List_Table' );
		$wp_list_table->display( true );

		if ( 1 > $total ) {
			echo '<p id="no-comments">' . apply_filters( 'edd_reviews_admin_no_reviews_text', __( 'No reviews yet.', 'edd-reviews' ) ) . '</p>';
		} else {
			$hidden = get_hidden_meta_boxes( get_current_screen() );
			if ( ! in_array( 'commentsdiv', $hidden ) ) {
				?>
				<script type="text/javascript">jQuery(document).ready(function(){commentsBox.get(<?php echo $total; ?>, 10);});</script>
				<?php
			}

			?>
			<p class="hide-if-no-js" id="show-comments"><a href="#commentstatusdiv" onclick="commentsBox.get(<?php echo $total; ?>);return false;"><?php _e('Show comments'); ?></a> <span class="spinner"></span></p>
			<?php
		}

		wp_comment_trashnotice();
	}

	/**
	 * Load Styles
	 *
	 * @since 1.0
	 * @access public
	 * @global array $edd_options Used to access the EDD Options
	 * @return void
	 */
	public function load_styles() {
		global $edd_options;

		wp_register_style( 'edd-reviews-admin', $this->assets_url . 'css/edd-reviews-admin.css', array( ), $this->version );

		if ( is_admin() )
			wp_enqueue_style( 'edd-reviews-admin' );

		if ( isset( $edd_options['edd_reviews_disable_css'] ) )
			return;

		wp_register_style( 'edd-reviews', $this->assets_url . 'css/edd-reviews.css', array( ), $this->version );
		wp_enqueue_style( 'edd-reviews' );
	}

	/**
	 * Load Scripts
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function load_scripts() {
		wp_register_script( 'edd-reviews-js', $this->assets_url . 'js/edd-reviews.js', array( 'jquery' ), $this->version );

		if ( is_singular( 'download' ) ) {
			wp_enqueue_script( 'edd-reviews-js' );
		}

		$edd_reviews_params = array(
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'edd_voting_nonce' => wp_create_nonce( 'edd_reviews_voting_nonce' ),
			'thank_you_msg'    => apply_filters( 'edd_reviews_thank_you_for_voting_message', __( 'Thank you for your feedback.', 'edd-reviews' ) )
		);

		wp_localize_script( 'edd-reviews-js', 'edd_reviews_params', apply_filters( 'edd_reviews_js_params', $edd_reviews_params ) );
	}

	/**
	 * Load Admin Scripts/Styles
	 *
	 * @since 1.0
	 * @access public
	 * @global object Used to access the current 'screen' that the user is
	 *  browsing
	 * @global object Used to access the post data
	 * @return void
	 */
	public function admin_scripts() {
		global $current_screen, $post;

		wp_register_style( 'edd-reviews-admin', $this->assets_url . 'css/edd-reviews-admin.css', array( ), $this->version );

		wp_enqueue_style( 'edd-reviews-admin' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Shows Review Meta on Comments List Table
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array $columns All the columns on the list table
	 * @return array $columns New columns with Review Title and Rating added
	 */
	function custom_columns( $columns ) {
		$columns['title']  = __( 'Review Title', 'edd-reviews' );
		$columns['rating'] = __( 'Rating', 'edd-reviews' );

		return $columns;
	}

	/**
	 * Display Custom Column Data
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param string $column Current column
	 * @param int $comment_ID Comment ID
	 * @return void
	 */
	public function custom_column_data( $column, $comment_ID ) {
		if ( 'title' == $column ) {
			if ( get_comment_meta( $comment_ID, 'edd_review_title', true ) )
				echo get_comment_meta( $comment_ID, 'edd_review_title', true );
			else
				echo '-';
		}

		if ( 'rating' == $column ) {
			if ( get_comment_meta( $comment_ID, 'edd_rating', true ) )
				echo get_comment_meta( $comment_ID, 'edd_rating', true ) . ' / 5';
			else
				echo '-';
		}
	}

	/**
	 * Open Comments for all Downloads
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param bool $open Whether the comments are open or not
	 * @param string $post_id Post ID
	 * @return bool $open Whether the comments are open or not
	 */
	public function open_all_comments( $open, $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( 'download' == $post_type )
			$open = true;

		return $open;
	}

	/**
	 * Register Extensions Settings
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array $settings Existing registered settings
	 * @param array New settings
	 * @return array Merged array with new settings added
	 */
	public function misc_settings( $settings ) {
		$new = array(
			array(
				'id'   => 'edd_review_settings',
				'name' => '<strong>' . __( 'Product Reviews', 'edd-reviews' ) . '</strong>',
				'desc' => '',
				'type' => 'header'
			),
			array(
				'id'   => 'edd_reviews_enable_breakdown',
				'name' => __( 'Enable review breakdown', 'edd-reviews' ),
				'desc' => __( 'This will show how many people have rated the download for each star rating.', 'edd-reviews' ),
				'type' => 'checkbox',
				'size' => 'regular'
			),
			array(
				'id'   => 'edd_reviews_disable_multiple_reviews',
				'name' => __( 'Disable multiple reviews by same author', 'edd-reviews' ),
				'desc' => __( 'This will disallow authors to post multiple reviews on the same download', 'edd-reviews' ),
				'type' => 'checkbox',
				'size' => 'regular'
			),
			array(
				'id'   => 'edd_reviews_only_allow_reviews_by_buyer',
				'name' => __( 'Only allow reviews by buyers', 'edd-reviews' ),
				'desc' => __( 'This will only allow people who have purchased your product to review it. It will require them to login.', 'edd-reviews' ),
				'type' => 'checkbox',
				'size' => 'regular'
			)
		);

		return array_merge( $settings, $new );
	}

	/**
	 * Register Misc Settings
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array $settings Existing registered settings
	 * @param array New settings
	 * @return array Merged array with new settings added
	 */
	public function styles_settings( $settings ) {
		$new = array(
			array(
				'id'   => 'edd_reviews_styling_options',
				'name' => '<strong>' . __( 'Reviews', 'edd-reviews' ) . '</strong>',
				'desc' => '',
				'type' => 'header'
			),
			array(
				'id'   => 'edd_reviews_disable_css',
				'name' => __( 'Disable EDD Reviews CSS', 'edd-reviews' ),
				'desc' => __( 'Check this to disable styling for the reviews provided by the EDD Reviews plugin', 'edd-reviews' ),
				'type' => 'checkbox',
				'size' => 'regular'
			)
		);

		return array_merge( $settings, $new );
	}

	/**
	 * Adds "View Reviews" Link to Admin Bar
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wp_admin_bar Used to add nodes to the WordPress Admin Bar
	 * @global object $post Used to access the post data
	 * @return void
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar, $post;

		if ( is_admin() && current_user_can( 'moderate_comments' ) ) {
			$current_screen = get_current_screen();

			if ( 'post' == $current_screen->base && 'add' != $current_screen->action && ( $post_type_object = get_post_type_object( $post->post_type ) ) && 'download' == $post->post_type && current_user_can( $post_type_object->cap->read_post, $post->ID ) && ( $post_type_object->public ) && ( $post_type_object->show_in_admin_bar ) && current_user_can( 'moderate_comments' ) ) {
				if ( wp_count_comments( $post->ID )->total_comments > 0 ) {
					$wp_admin_bar->add_node( array(
						'id' => 'edd-view-reviews',
						'title' => __( 'View Reviews', 'edd-reviews' ) . '<span class="edd-review-count-wrap"><span class="edd-review-count">' . wp_count_comments( $post->ID )->total_comments . '</span></span>',
						'href' => admin_url( 'edit-comments.php?p=' . $post->ID )
					) );
				}
			}
		} elseif ( is_singular( 'download' ) && current_user_can( 'moderate_comments' ) ) {
			if ( wp_count_comments( $post->ID )->total_comments > 0 ) {
				$wp_admin_bar->add_node( array(
					'id' => 'edd-view-reviews',
					'title' => __( 'View Reviews', 'edd-reviews' ) . '<span class="edd-review-count-wrap">&nbsp;<span class="edd-review-count">' . wp_count_comments( $post->ID )->total_comments . '</span></span>',
					'href' => admin_url( 'edit-comments.php?p=' . $post->ID )
				) );
			}
		}
	}

	/**
	 * Handles the displaying of any notices in the admin area
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function admin_notices() {
		echo '<div class="error"><p>' . sprintf( __( 'You must install %sEasy Digital Downloads%s for the Reviews Add-On to work.', 'edd-reviews' ), '<a href="http://easydigitaldownloads.com" title="Easy Digital Downloads">', '</a>' ) . '</p></div>';
	}

	/**
	 * Count the number of reviews from the database
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @global object $post Used to access the post data
	 * @return string $count Number of reviews
	 */
	public function count_reviews() {
		global $wpdb, $post;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(meta_value)
				FROM {$wpdb->commentmeta}
				LEFT JOIN {$wpdb->comments} ON {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID
				WHERE meta_key = 'edd_rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
				",
				$post->ID
			)
		);

		return $count;
	}

	/**
	 * Count the number of ratings from the database
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @global object $post Used to access the post data
	 * @return string $count Number of reviews
	 */
	public function count_ratings() {
		global $wpdb, $post;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT SUM(meta_value)
				FROM {$wpdb->commentmeta}
				LEFT JOIN {$wpdb->comments} ON {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID
				WHERE meta_key = 'edd_rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				",
				$post->ID
			)
		);

		return $count;
	}

	/**
	 * Gets the number of the reviews by a rating
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @param int $rating Rating (1 - 5)
	 * @return int $number Number of reviews
	 */
	public function get_review_count_by_rating( $rating ) {
		global $wpdb, $post;

		$rating = (int) $rating;

		if ( $rating < 1 && $rating > 5 )
			return;

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(meta_value)
				FROM {$wpdb->commentmeta}
				LEFT JOIN {$wpdb->comments} ON {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID
				WHERE meta_key = 'edd_rating'
				AND meta_value = {$rating}
				AND comment_approved = '1'
				AND meta_value > 0
				AND {$wpdb->comments}.comment_post_ID = %s
				",
				$post->ID
			)
		);

		return $count;
	}

	/**
	 * Build Reviews (comments) title
	 *
	 * @since 1.0
	 * @access public
	 * @global object $post Used to access the post data
	 *
	 * @uses EDD_Reviews::count_reviews()
	 *
	 * @param int $average Average ratings for reviews
	 * @return void
	 */
	public function reviews_title( $average = null ) {
		global $post;

		if ( $average ) :

		do_action( 'edd_reviews_title_before' );
		?>
		<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<span itemprop="ratingValue" style="display:none"><?php echo $average; ?></span>
			<h2 class="comments-title" id="comments-title"><?php echo sprintf( apply_filters( 'edd_reviews_reviews_title', __( '%s Reviews for %s', 'edd-reviews' ) ), '<span itemprop="reviewCount" class="edd-review-count">' . $this->count_reviews() . '</span>', get_the_title( $post->ID ) ); ?></h2>
		</div>
		<?php
		else :
		?>
		<h2 class="comments-title" id="comments-title"><?php apply_filters( 'edd_reviews_review_title_default',  _e( 'Reviews', 'edd-reviews' ) ); ?></h2>
		<?php
		do_action( 'edd_reviews_title_after' );
		endif;
	}

	/**
	 * Checks if the reviewer has purchased the download
	 *
	 * @since 1.0
	 * @access public
	 * @global object $post Used to access the post data
	 * @return bool Whether reviews has purchased download or not
	 */
	public function reviewer_has_purchased_download() {
		global $post;

		$user_email = wp_get_current_commenter();
		$user_email = $user_email['comment_author_email'];

		if ( edd_has_user_purchased( $user_email, $post->ID ) )
			return true;

		return false;
	}

	/**
	 * Add Classes to the Reviews
	 *
	 * @since 1.0
	 * @access public
	 * @param array $classes Comment classes
	 * @return array $classes Comment (reviews) classes with 'review' added
	 */
	public function review_classes( $classes ) {
		$classes[] = 'review';

		return $classes;
	}

	/**
	 * Get the HTML to display the ratings of comments
	 *
	 * @since 1.3
	 * @access public
	 * @return string $rating_html The HTML output generated
	 */
	public function get_comment_rating_output() {
		global $comment;

		if( ! empty( $comment->comment_parent ) )
			return;

		$rating = get_comment_meta( $comment->comment_ID, 'edd_rating', true );

		ob_start();
		?>
			<span itemprop="name" class="review-title-text"><?php echo get_comment_meta( $comment->comment_ID, 'edd_review_title', true ); ?></span>

			<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating">
				<div class="edd_reviews_rating_box" role="img" aria-label="<?php echo $rating . ' ' . __( 'stars', 'edd-reviews' ); ?>">
					<div class="edd_star_rating" style="width: <?php echo ( 19 * $rating ); ?>px"></div>
				</div>
				<div style="display:none" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
					<meta itemprop="worstRating" content="1" />
					<span itemprop="ratingValue"><?php echo $rating; ?></span>
					<span itemprop="bestRating">5</span>
				</div>
			</div>
		<?php
		$rating_html = ob_get_clean();

		return apply_filters( 'edd_reviews_ratings_html', $rating_html, $comment );
	}

	/**
	 * Get the HTML to display the 'helpful' output to the user
	 *
	 * @since 1.3
	 * @access public
	 * @global $comment
	 * @return string $output Generated HTML output
	 */
	public function get_comment_helpful_output() {
		global $comment;

		ob_start();
		?>

			<?php if ( ! $this->is_review_poster() ) : ?>

				<?php if ( ( isset( $_GET['edd_reviews_vote'] ) && $_GET['edd_reviews_vote'] == 'success' && isset( $_GET['edd_c'] ) && is_numeric( $_GET['edd_c'] ) && $_GET['edd_c'] == get_comment_ID() ) || EDD()->session->get( 'wordpress_edd_reviews_voted_' . get_comment_ID() ) ) : ?>

					<div class="edd-review-vote edd-yellowfade">
						<p style="margin:0;padding:0;"><?php echo apply_filters( 'edd_reviews_thank_you_for_voting_message', __( 'Thank you for your feedback.', 'edd-reviews' ) ); ?></p>
						<?php $this->voting_info(); ?>
					</div>

				<?php else: ?>

					<div class="edd-review-vote">
						<?php do_action( 'edd_reviews_voting_box_before' ); ?>
						<?php $this->voting_info(); ?>
						<p><?php echo apply_filters( 'edd_reviews_voting_intro_text', _e( 'Help other customers find the most helpful reviews', 'edd-reviews' ) ); ?></p>
						<p>
							<?php echo apply_filters( 'edd_reviews_review_helpful_text', __( 'Did you find this review helpful?', 'edd-reviews' ) ); ?>
							<span class="edd-reviews-voting-buttons">
								<a class="vote-yes" data-edd-reviews-comment-id="<?php echo get_comment_ID(); ?>" data-edd-reviews-vote="yes" rel="nofollow" href="<?php echo add_query_arg( array( 'edd_review_vote' => 'yes', 'edd_c' => get_comment_ID() ) ); ?>"><?php _e( 'Yes', 'edd-reviews' ); ?></a>&nbsp;<a class="vote-no" data-edd-reviews-comment-id="<?php echo get_comment_ID(); ?>" data-edd-reviews-vote="no" rel="nofollow" href="<?php echo add_query_arg( array( 'edd_review_vote' => 'no', 'edd_c' => get_comment_ID() ) ); ?>"><?php _e( 'No', 'edd-reviews' ); ?></a>
							</span>
						</p>
						<?php do_action( 'edd_reviews_voting_box_after' ); ?>
					</div>

				<?php endif; ?>

			<?php endif; ?>

		<?php
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Generate the output to display the comment rating
	 *
	 * @since 1.3
	 * @access public
	 * @global object $post
	 * @param  string $comment_text
	 * @param  array $comment
	 * @return string $output Generated HTML output
	 */
	public function comment_rating( $comment_text, $comment = null ) {
		global $post;

		// When submitting a comment as a guest don't try to append anything.
		if ( ! $comment )
			return $comment_text;

		if ( 'download' != get_post_type() )
			return $comment_text;

		$rating  = $this->get_comment_rating_output();
		$output  = $rating . $comment_text;
		$output .= $this->get_comment_helpful_output();

		return $output;
	}

	/**
	 * Conditional whether or not the current logged in user is the poster of
	 * the review being displayed. This function runs throughout the comment
	 * loop and is called for each comment.
	 *
	 * @since 1.2
	 * @access public
	 * @global object $GLOBALS['comment'] Current comment
	 * @return bool Whether or not the current comment in the loop is by the current user logged in
	 */
	public function is_review_poster() {
		$comment    = $GLOBALS['comment'];

		$user       = wp_get_current_user();
		$user_email = ( isset( $user->user_email ) ? $user->user_email : null );

		if ( $comment->comment_author_email == $user_email ) {
			return true;
		} else {
			return false;
		} // end if
	}

	/**
	 * Display Voting Info
	 *
	 * Example output: 2 of 8 people found this review helpful
	 *
	 * @since 1.0
	 * @access public
	 * @global object $GLOBALS['comment'] Current comment
	 * @return void
	 */
	public function voting_info() {
		$comment = $GLOBALS['comment'];

		$votes = array(
			'yes' => get_comment_meta( $comment->comment_ID, 'edd_review_vote_yes', true ),
			'no'  => get_comment_meta( $comment->comment_ID, 'edd_review_vote_no',  true ),
		);

		if ( ! empty( $votes['yes'] ) && $votes['yes'] > 1 ) {
			$total = $votes['yes'] + $votes['no'];

			echo '<p class="edd-review-voting-feedback">' . sprintf( __( '%s of %s people found this review helpful.', 'edd-reviews' ), $votes['yes'], $total ) . '</p>';
		}
	}

	/**
	 * Conditional whether or not to display review breakdown
	 *
	 * @since 1.0
	 * @access public
	 * @global array $edd_options Used to access the EDD Options
	 *
	 * @uses EDD_Reviews::review_breakdown()
	 *
	 * @return void
	 */
	public function maybe_show_review_breakdown( $post_id = 0 ) {
		global $edd_options;

		if ( isset( $edd_options['edd_reviews_enable_breakdown'] ) && comments_open( $post_id ) ) {
			$this->review_breakdown();
		}
	}

	/**
	 * Displays the login form
	 *
	 * @since 1.3
	 * @access public
	 * @global array $edd_options Used to access the EDD Options
	 * @return string $output Login form
	 */
	public function display_login_form() {
		global $edd_options;

		$output = '';

		$output .= '<div class="edd-reviews-must-log-in comment-form" id="commentform">';
		$output .= '<p class="edd-reviews-not-logged-in">' . apply_filters( 'edd_reviews_user_logged_out_message', sprintf( __( 'You must log in and be a buyer of this %s to submit a review.' ), strtolower( edd_get_label_singular() ) ) ) . '</p>';
		$output .= wp_login_form( array( 'echo' => false ) );
		$output .= '</div><!-- /.edd-reviews-must-log-in -->';

		return apply_filters( 'edd_reviews_login_form', $output );
	}

	/**
	 * Conditional whether or not the review submission form should remain
	 * restricted
	 *
	 * @since 1.2
	 * @access public
	 * @global object $post
	 * @return bool Whether the user has purchased the download
	 */
	public function maybe_restrict_form() {
		global $post, $edd_options;

		if ( ! isset( $edd_options[ 'edd_reviews_only_allow_reviews_by_buyer' ] ) )
			return true;

		$user = wp_get_current_user();
		$user_id = ( isset( $user->ID ) ? (int) $user->ID : 0 );

		return edd_has_user_purchased( $user_id, $post->ID );
	}

	/**
	 * Conditional whether the currently logged in user has purchased the product
	 * before
	 *
	 * @since 1.2
	 * @access public
	 * @global array $post Used to access the current post
	 * @return void
	 */
	public function is_user_buyer() {
		global $post;

		if ( edd_has_user_purchased( get_current_user_id(), $post->post_ID ) ) {
			return true;
		} else {
			return false;
		} // end if
	}

	/**
	 * Reviews Breakdown
	 *
	 * Shows a breakdown of all the reviews and the number of people that given
	 * each rating for each download
	 *
	 * Example: 8 people gave a 5 star rating; 10 people have a 2 star rating
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::display_total_reviews_count()
	 * @uses EDD_Reviews::display_review_counts()
	 *
	 * @return void
	 */
	public function review_breakdown() {
		echo '<div class="edd_reviews_breakdown">';
		$this->display_total_reviews_count();
		$this->display_review_counts();
		echo '</div><!-- /.edd_reviews_breakdown -->';
	}

	/**
	 * Displays the total reviews count
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::count_reviews()
	 *
	 * @return void
	 */
	public function display_total_reviews_count() {
		echo '<div class="edd-reviews-total-count">' . $this->count_reviews() . ' ' . _n( 'review', 'reviews', $this->count_reviews(), 'edd-reviews' ) . '</div>';
	}

	/**
	 * Displays reviews count for each rating by looping through 1 - 5
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::get_review_count_by_rating()
	 * @uses EDD_Reviews::count_reviews()
	 *
	 * @return void
	 */
	public function display_review_counts() {
		$output = '';

		for ( $i = 5; $i >= 1; $i-- ) {
			$number = $this->get_review_count_by_rating( $i );

			$all = $this->count_reviews();

			( $all == 0 ) ? $all = 1 : $all;

			$number_format = number_format( ( $number / $all ) * 100, 1 );

			if ( $number_format == '100.0' ) {
				$number_format = '100%';
			} else {
				$number_format .= 'px';
			}

			$output .= '<div class="edd-counter-container edd-counter-container-'. $i .'">';
			$output .= '<div class="edd-counter-label">'. $i . ' ' . _n( 'star', 'stars', $i, 'edd-reviews' ) . '</div>';
			$output .= '<div class="edd-counter-back"><span class="edd-counter-front" style="width: '. $number_format .'"></span></div>';
			$output .= '<div class="edd-review-count">'. $number .'</div>';
			$output .= '</div>';
		}

		echo $output;
	}

	/**
	 * Display an aggregate review score across all reviews.
	 *
	 * @since   1.3.7
	 * @access  public
	 * @return  void
	 */
	public function display_aggregate_rating() {
		$average = $this->average_rating( false );
		?>
		<div class="edd_reviews_aggregate_rating_display">
			<div class="edd_reviews_rating_box" role="img" aria-label="<?php echo $average . ' ' . __( 'stars', 'edd-reviews' ); ?>">
				<div class="edd_star_rating" style="width: <?php echo ( 19 * $average ); ?>px"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Process Vote from Review
	 *
	 * This function is called if a JavaScript isn't enabled
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function process_vote() {
		if ( isset( $_GET['edd_review_vote'] ) && isset( $_GET['edd_c'] ) && is_numeric( $_GET['edd_c'] ) ) {
			$this->add_comment_vote_meta( $_GET['edd_c'], $_GET['edd_review_vote'] );

			// Remove the query arguments to prevent multiple votes
			$url = remove_query_arg( array( 'edd_c', 'edd_review_vote' ) );
			wp_redirect( $url . '?edd_reviews_vote=success&edd_c=' . $_GET['edd_c'] .'#comment-' . intval( $_GET['edd_c'] ) );
			die();
		}
	}

	/**
	 * Add Comment Vote
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param int $comment_id Comment ID
	 * @param string $vote Whether the vote was yes or no
	 * @return void
	 */
	public function add_comment_vote_meta( $comment_id, $vote ) {
		if ( 'yes' == $vote ) {
			$value = get_comment_meta( $comment_id, 'edd_review_vote_yes', true );

			if ( ! empty( $value ) && ! $value > 0 ) {
				$number = 1;
				add_comment_meta( $comment_id, 'edd_review_vote_yes', $number );
			} else {
				$number = $value + 1;
				update_comment_meta( $comment_id, 'edd_review_vote_yes', $number );
			}
		} elseif ( 'no' == $vote ) {
			$value = get_comment_meta( $comment_id, 'edd_review_vote_no', true );

			if ( ! empty( $value ) && ! $value > 0 ) {
				$number = 1;
				add_comment_meta( $comment_id, 'edd_review_vote_yes', $number );
			} else {
				$number = $value + 1;
				update_comment_meta( $comment_id, 'edd_review_vote_no', $number );
			}

			add_comment_meta( $comment_id, 'edd_review_vote_no', $number );
		}
	}

	/**
	 * Checks whether an AJAX request has been sent
	 *
	 * @since 1.0
	 * @access public
	 * @return bool Whether or not AJAX $_GET header has been passed
	 */
	public function is_ajax_request() {
		return (bool) ( isset( $_POST['edd_reviews_ajax'] ) && ! empty( $_REQUEST['action'] ) );
	}

	/**
	 * Process Voting for the Reviews via AJAX
	 *
	 * Processes the voting button appended to the bottom of each review by adding
	 * or updating the comment meta via AJAX.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::is_ajax_request()
	 *
	 * @return mixed returns if AJAX check fails
	 */
	public function process_ajax_vote() {
		// Bail if an AJAX request isn't sent
		if ( ! $this->is_ajax_request() )
			return;

		check_ajax_referer( 'edd_reviews_voting_nonce', 'security', true );

		if ( ! isset( $_POST['review_vote'] ) )
			return;

		if ( isset( $_POST['review_vote'] ) && isset( $_POST['comment_id'] ) && is_numeric( $_POST['comment_id'] ) ) {
			$this->add_comment_vote_meta( $_POST['comment_id'], $_POST['review_vote'] );

			EDD()->session->set( 'wordpress_edd_reviews_voted_' . $_POST['comment_id'], 'yes' );

			echo 'success';
		} else {
			echo 'fail';
		}

		die();
	}

	/**
	 * Register Dashboard Widgets
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function dashboard_widgets() {
		if ( is_blog_admin() && current_user_can( 'moderate_comments' ) ) {
			$recent_reviews_title = apply_filters( 'edd_reviews_recent_reviews_dashboard_widget_title', __( 'Easy Digital Downloads Recent Reviews', 'edd-reviews' ) );
			wp_add_dashboard_widget(
				'edd_reviews_dashboard_recent_reviews',
				$recent_reviews_title,
				array( 'EDD_Reviews', 'render_dashboard_widget' )
			);
		}
	}

	/**
	 * Render the Dashboard Widget
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress Database API
	 * @return void
	 */
	public static function render_dashboard_widget() {
		global $wpdb;

		$reviews = $wpdb->get_results(
			"
			SELECT *, SUBSTRING( comment_content, 1, 100 ) AS excerpt
			FROM {$wpdb->comments}
			LEFT JOIN $wpdb->posts ON {$wpdb->comments}.comment_post_ID = {$wpdb->posts}.ID
			WHERE comment_approved = '1'
			AND post_password = ''
			AND post_type = 'download'
			AND comment_type = ''
			ORDER BY comment_date_gmt DESC
			LIMIT 5
			"
		);

		if ( $reviews ) {
			echo '<div id="edd-reviews-list">';

			foreach ( $reviews as $review ) {
				$output = '<div id="review-'. $review->ID .'">';

				$output .= ( get_option( 'show_avatars' ) ) ? get_avatar( $review->comment_author_email, 50 ) : '';

				$output .= '<div class="edd-dashboard-review-wrap">';

				$rating = get_comment_meta( $review->comment_ID, 'edd_rating', true );

				$output .= '<h4 class="meta">';
				$output .= '<a href="' . get_permalink( $review->ID ) . '#comment-' . absint( $review->comment_ID ) .'">' . esc_html__( get_comment_meta( $review->comment_ID, 'edd_review_title', true ) ) . '</a>';
				$output .= __( ' on ', 'edd-reviews' ) . '<a href="' . get_permalink( $review->ID ) . '">' . esc_html( $review->post_title ) . '</a>';
				$output .= '</h4>';
				$output .= '<div class="edd_reviews_rating_box"><div class="edd_star_rating" style="width: ' . 19 * $rating  . 'px"></div></div>';
				$output .= '<p>' . __( 'By', 'edd-reviews' ) . ' ' . esc_html( $review->comment_author ) . ', ' . get_comment_date( get_option( 'date_format()' ), $review->comment_ID ) . '</p>';
				$output .= '<blockquote>' . wp_kses_data( $review->excerpt ) . ' ...</blockquote></div>';
				$output .= '</div>';

				echo $output;
			}

			echo '</div>';
		} else {
			echo '<p>' . __( 'There are no reviews yet.', 'edd-reviews' ) . '</p>';
		}
	}

	/**
	 * Add the Meta Boxes
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes() {
		$comment_id = ! empty( $_GET['c'] ) ? absint( $_GET['c'] ) : 0;
		if ( $this->has_review_meta( $comment_id ) ) {
			add_meta_box( 'edd_reviews_review_meta_box', __( 'Review Information', 'edd-reviews' ), array( $this, 'review_meta_box' ), 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Checks if the commment passed has review metadata attached to it
	 *
	 * @since 1.0
	 * @access public
	 * @param object $comment Comment information
	 * @return void
	 */
	public function has_review_meta( $comment ) {
		if ( is_int( $comment ) ) {
			$meta = get_comment_meta( $comment, 'edd_review_title', true );
		} else if ( is_object( $comment ) ) {
			$meta = get_comment_meta( $comment->comment_ID, 'edd_review_title', true );
		}

		return ! empty( $meta );
	}

	/**
	 * Render the Review Meta Box
	 *
	 * Outputs the Review Information meta box on the Edit Comment screen. This
	 * meta box displays the review title and the star rating.  It also allows
	 * for it to be edited.
	 *
	 * @since 1.0
	 * @access public
	 * @param object $comment Comment information
	 * @return void
	 */
	public function review_meta_box( $comment ) {
		if ( $this->has_review_meta( $comment ) ) :
		?>
		<table class="form-table editcomment">
			<tbody>
				<tr valign="top">
					<td class="first"><?php _e( 'Review Title:', 'edd-reviews' ); ?></td>
					<td><input type="text" class="widefat" id="edd_reviews_review_title" name="edd_reviews_review_title" value="<?php echo get_comment_meta( $comment->comment_ID, 'edd_review_title', true ); ?>" /></td>
				</tr>
				<tr valign="top">
					<td class="first"><?php _e( 'Rating:', 'edd-reviews' ); ?></td>
					<td><input type="text" class="widefat" id="edd_reviews_rating" name="edd_reviews_rating" value="<?php echo get_comment_meta( $comment->comment_ID, 'edd_rating', true ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<?php
		endif;
	}

	/**
	 * Save the Meta Data from the Meta Box on the Edit Comment Screen
	 *
	 * @since 1.0
	 * @access public
	 * @param int $comment_id Comment ID
	 * @return void
	 */
	public function update_review_meta( $comment_id ) {
		if ( $this->has_review_meta( $comment_id ) ) {
			$review_title = sanitize_text_field( $_POST['edd_reviews_review_title'] );
			$rating       = intval( $_POST['edd_reviews_rating'] );

			if ( empty ( $review_title ) ) {
				wp_die( sprintf( __( '%sError%s: Please add a review title.', 'edd-reviews' ), '<strong>', '</strong>' ), __( 'Error', 'edd-reviews' ), array( 'back_link' => true ) );
			}

			if ( ! ( $rating > 0 && $rating <= 5 ) ) {
				wp_die( sprintf( __( '%sError%s: Please add a valid rating between 1 and 5.', 'edd-reviews' ), '<strong>', '</strong>' ), __( 'Error', 'edd-reviews' ), array( 'back_link' => true ) );
			}

			update_comment_meta( $comment_id, 'edd_review_title', $review_title );
			update_comment_meta( $comment_id, 'edd_rating',       $rating       );
		}
	}

	/**
	 * Register API Query Mode
	 *
	 * @since 1.0
	 * @access public
	 * @param array $modes Whitelisted query modes
	 * @return array $modes Updated list of query modes
	 */
	public function register_api_mode( $modes ) {
		$modes[] = 'reviews';
		return $modes;
	}

	/**
	 * Add 'review_id' Query Var into WordPress Whitelisted Query Vars
	 *
	 * @since 1.0
	 * @access public
	 * @param array $vars Array of WordPress allowed query vars
	 * @return array $vars Updated array of WordPress query vars to allow
	 *  Reviews to integrate with the EDD API
	 */
	public function query_vars( $vars ) {
		$vars[] = 'review_id';
		return $vars;
	}

	/**
	 * Processes the Data Outputted when an API Call for Reviews is Triggered
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @global object $wp_query Used to access the query vars
	 *
	 * @param array $data Array to hold the output
	 * @param array $query_mode Query mode (i.e. reviews)
	 * @param object $api_object EDD_API Object
	 *
	 * @return array $data All the data for when the API call for reviews is fired
	 */
	public function api_output( $data, $query_mode, $api_object ) {
		global $wpdb, $wp_query;

		// Bail if the query mode isn't reviews
		if ( 'reviews' !== $query_mode )
			return $data;

		// Get the review_id query var
		$review_id = isset( $wp_query->query_vars['review_id'] ) ? $wp_query->query_vars['review_id'] : null;

		if ( $review_id ) {
			// Get the review from the database
			$review = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT *
					FROM {$wpdb->comments}
					INNER JOIN {$wpdb->posts} ON {$wpdb->comments}.comment_post_ID = {$wpdb->posts}.ID
					WHERE comment_ID = '%d'
					LIMIT 1
					",
					$review_id
				)
			);

			if ( $review ) :
				$data['reviews']['id']             = $review[0]->comment_ID;
				$data['reviews']['title']          = get_comment_meta( $review[0]->comment_ID, 'edd_review_title', true );
				$data['reviews']['download_id']    = $review[0]->comment_post_ID;
				$data['reviews']['download_title'] = $review[0]->post_title;
				$data['reviews']['rating']         = get_comment_meta( $review[0]->comment_ID, 'edd_rating', true );
				$data['reviews']['author']         = $review[0]->comment_author;
				$data['reviews']['email']          = $review[0]->comment_author_email;
				$data['reviews']['IP']             = $review[0]->comment_author_IP;
				$data['reviews']['date']           = $review[0]->comment_date;
				$data['reviews']['date_gmt']       = $review[0]->comment_date_gmt;
				$data['reviews']['content']        = $review[0]->comment_content;
				$data['reviews']['status']         = $review[0]->comment_approved;
				$data['reviews']['user_id']        = $review[0]->user_id;
			else :
				$error['error'] = sprintf( __( 'Review %s not found!', 'edd-reviews' ), $review_id );
				return $error;
			endif;
		} else {
			// Get total reviews count from the database
			$total_reviews = $wpdb->get_var(
				$wpdb->prepare(
					"
					SELECT COUNT(meta_value)
					FROM {$wpdb->commentmeta}
					LEFT JOIN {$wpdb->comments} ON {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID
					WHERE meta_key = 'edd_rating'
					AND comment_approved = '1'
					AND meta_value > 0
					"
				)
			);

			/** Total Reviews */
			$data['reviews']['total'] = $total_reviews;

			/** Most Recent Review */
			$most_recent_review = get_comments( array( 'post_type' => 'download', 'number' => 1 ) );

			$data['reviews']['most_recent']['id']             = $most_recent_review[0]->comment_ID;
			$data['reviews']['most_recent']['title']          = get_comment_meta( $most_recent_review[0]->comment_ID, 'edd_review_title', true );
			$data['reviews']['most_recent']['download_id']    = $most_recent_review[0]->comment_post_ID;
			$data['reviews']['most_recent']['download_title'] = get_the_title( $most_recent_review[0]->comment_post_ID );
			$data['reviews']['most_recent']['rating']         = get_comment_meta( $most_recent_review[0]->comment_ID, 'edd_rating', true );
			$data['reviews']['most_recent']['author']         = $most_recent_review[0]->comment_author;
			$data['reviews']['most_recent']['email']          = $most_recent_review[0]->comment_author_email;
			$data['reviews']['most_recent']['IP']             = $most_recent_review[0]->comment_author_IP;
			$data['reviews']['most_recent']['date']           = $most_recent_review[0]->comment_date;
			$data['reviews']['most_recent']['date_gmt']       = $most_recent_review[0]->comment_date_gmt;
			$data['reviews']['most_recent']['content']        = $most_recent_review[0]->comment_content;
			$data['reviews']['most_recent']['status']         = $most_recent_review[0]->comment_approved;
			$data['reviews']['most_recent']['user_id']        = $most_recent_review[0]->user_id;
		}

		// Allow extensions to add to the data outpt
		$data = apply_filters( 'edd_reviews_api_output_data', $data );

		return $data;
	}

	/**
	 * Is the User Allowed to See TinyMCE?
	 *
	 * @since 1.0
	 * @access public
	 * @return bool Whether the user can see TinyMCE or not
	 */
	public function user_can_see_tinymce() {
		return ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) );
	}

	/**
	 * Add TinyMCE Button
	 *
	 * Adds a button to the TinyMCE editor to easily embed reviews into posts
	 * and pages
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_Reviews::user_can_see_tinymce()
	 *
	 * @return void
	 */
	public function tinymce_button() {
		if ( $this->user_can_see_tinymce() && 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_plugin'      ) );
			add_filter( 'mce_buttons',          array( $this, 'register_button' ) );
		}
	}

	/**
	 * Register TinyMCE Plugin
	 *
	 * @since 1.0
	 * @access public
	 * @param array $plugin_array Array of TinyMCE Plugins
	 * @return array $plugin_array Array of TinyMCE Plugins
	 */
	public function add_plugin( $plugin_array ) {
		$plugin_array['edd_reviews'] = $this->assets_url . 'js/edd-reviews-admin.js';
		return $plugin_array;
	}

	/**
	 * Register TinyMCE Button
	 *
	 * @since 1.0
	 * @access public
	 * @param array $buttons Array of TinyMCE Button
	 * @return array $buttons Array of TinyMCE Button
	 */
	public function register_button( $buttons ) {
		array_push( $buttons, '|', 'edd_reviews' );
		return $buttons;
	}

	/**
	 * Process the TinyMCE Modal Dialog
	 *
	 * @since 1.0
	 * @access public
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return void
	 */
	public function process_mce_dialog() {
		if ( is_user_logged_in() && isset( $_GET['edd_reviews_mce_dialog'] ) && 'true' == $_GET['edd_reviews_mce_dialog'] ) {
			global $wpdb;

			$reviews = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value, comment_id FROM {$wpdb->commentmeta} WHERE meta_key = %s", 'edd_review_title' ) );
			?>
			<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
			<head>
				<meta charset="utf-8" />
				<title><?php _e( 'Embed Review', 'edd-reviews' ); ?></title>
				<script type="text/javascript" src="<?php echo includes_url(); ?>/js/tinymce/tiny_mce_popup.js"></script>
				<script type="text/javascript">
				var edd_reviews_dialog = {
					local_ed: 'ed',

					init: function (ed) {
						edd_reviews_dialog.local_ed = ed;
						tinyMCEPopup.resizeToInnerSize();
					},

					insert: function insertButton(ed) {
						tinyMCEPopup.execCommand('mceRemoveNode', false, null);

						var elem     = document.getElementById('edd_reviews_shortcode_dialog'),
							selected = elem.options[elem.selectedIndex].value;
							output   = '';

						output = '[review id="' + selected + '"]';
						tinyMCEPopup.execCommand('mceReplaceContent', false, output);
						tinyMCEPopup.close();
					}
				}

				tinyMCEPopup.onInit.add(edd_reviews_dialog.init, edd_reviews_dialog);
				</script>
			</head>
			<body>
			<?php
			if ( $reviews ) {
				echo '<h2>' . __( 'Select a Review to Embed', 'edd-reviews' ) . '</h2>';
				echo '<p><select id="edd_reviews_shortcode_dialog" name="edd_reviews_shortcode_dialog">';

				foreach ( $reviews as $review ) {
					echo '<option value="' . $review->comment_id . '">' . esc_html( $review->meta_value ) . '</option>';
				}

				echo '</select></p>';

				echo '<p><a href="javascript:edd_reviews_dialog.insert(edd_reviews_dialog.local_ed)" id="insert" style="display: block; line-height: 24px;">' . __( 'Embed Review', 'edd-reviews' ) . '</a></p>';
			} else {
				echo '<h2>' . __( 'No Reviews Have Been Created Yet', 'edd-reviews' ) . '</h2>';
			}
			?>
			</body>
			</html>
			<?php
			die();
		}
	}

	/**
	 * Plugin Action Links
	 *
	 * This function adds a link to the plugin action links bar on the Plugins
	 * Administrati on page to for the API documentation and to Easy Digital Downloads
	 * Support Forum.
	 *
	 * @since 1.0
	 * @access public
	 * @param array $links Plugin Action Links
	 * @return array $links Plugin Action Links
	 */
	public function plugin_links( $links, $file ) {
		static $this_plugin;

		if ( ! $this_plugin ) {
			$this_plugin = $this->basename;
		}

		if ( $file == $this_plugin ) {
			$api_link = '<a href="' . $this->plugin_url . 'api-docs/index.html' . '">' . __( 'API Documentation', 'edd-reviews' ) . '</a>';
			array_unshift( $links , $api_link );
		}

		return $links;
	}

	/**
	 * Loads the Updater
	 *
	 * Instantiates the Software Licensing Plugin Updater and passes the plugin
	 * data to the class.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function updater() {
		if( class_exists( 'EDD_License' ) ) {
			$license = new EDD_License( $this->file, 'Reviews', $this->version, 'Sunny Ratilal', 'edd_reviews_licensing_license_key' );
		}
	}
}

/**
 * Loads a single instance of EDD Reviews
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $edd_reviews = edd_reviews(); ?>
 *
 * @since 1.0
 *
 * @see EDD_Reviews::get_instance()
 *
 * @return object Returns an instance of the EDD_Reviews class
 */
function edd_reviews() {
	return EDD_Reviews::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their
 * hooks and filters
 */
add_action( 'plugins_loaded', 'edd_reviews', apply_filters( 'edd_reviews_action_priority', 10 ) );

endif;
