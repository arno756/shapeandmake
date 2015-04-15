<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class FES_Vendor_Shop {

	public function __construct() {

		$vendor_page = EDD_FES()->vendors->use_author_archives();

		if ( empty( $vendor_page ) ) { // use vendors page & filtered [downloads to display]
			add_action( 'pre_get_posts', array( $this, 'vendor_download_query' ) );
			add_action( 'the_content', array( $this, 'content' ), 10 );
			add_filter( 'init', array( $this, 'add_rewrite_rules' ),0 );
			add_action( 'query_vars', array( $this, 'query_vars' ), 0 );
			add_action( 'template_redirect', array( $this, 'vendor_archive_redirect' ) );
			add_filter( 'the_title',  array( $this, 'change_the_title' ) );
			add_filter( 'save_post', array( $this, 'vendor_page_updated' ), 10, 1 );
			add_action( 'admin_init', array( $this, 'after_vendor_page_update' ), 10 );
		} else { // use author archives to display
			add_action( 'init',  array( $this, 'change_wp_author_base' ));
			add_action('pre_get_posts',  array( $this, 'custom_download_archive'));
		}
	}

	public function content($content) {
		global $wp_query;

		$has_shortcode = false;
		if( function_exists( 'has_shortcode' ) ) {
			$has_shortcode = has_shortcode( $content, 'downloads' );
		}

		if ( isset( $wp_query->query_vars['vendor'] ) && ! $has_shortcode ) {
			return do_shortcode( '[downloads]' );
		} else {
			return $content;
		}

	}

	public function query_vars( $query_vars ) {
		$query_vars[] = 'vendor';
		return $query_vars;
	}

	public function add_rewrite_rules() {
		if ( !EDD_FES()->helper->get_option( 'fes-vendor-page', false ) ){
			return;
		}

		$page_id   = EDD_FES()->helper->get_option( 'fes-vendor-page', false );
		$page      = get_page( $page_id );
		$page_name = ! empty( $page->post_name ) ? $page->post_name : 'vendor/';
		$permalink = apply_filters( 'fes_adjust_vendor_url', untrailingslashit( $page_name ) );

		// Remove beginning slash
		if ( substr( $permalink, 0, 1 ) == '/' ) {
			$permalink = substr( $permalink, 1, strlen( $permalink ) );
		}

		//add_rewrite_rule( $permalink . '/([^/]+)', 'index.php?page_id=' . $page_id . '&vendor=$matches[1]', 'top' );

		add_rewrite_rule("{$page_name}/([^/]+)/page/?([2-9][0-9]*)", "index.php?page_id={$page_id}&vendor=\$matches[1]&paged=\$matches[2]", 'top');
		add_rewrite_rule("{$page_name}/([^/]+)", "index.php?page_id={$page_id}&vendor=\$matches[1]", 'top');
	}

	public function vendor_download_query( $query ) {
		global $wp_query, $post;


		if ( is_admin() ) {
			return;
		}

		if( ! is_object( $wp_query ) ) {
			return;
		}

		if ( isset( $wp_query->query_vars[ 'vendor' ] ) ) {
			add_filter( 'edd_downloads_query', array(
				$this,
				'set_shortcode'
			) );
		}
	}

	public function set_shortcode( $query ) {
		global $wp_query;

		if( ! is_object( $wp_query ) ) {
			return $query;
		}

		if( empty( $wp_query->query_vars[ 'vendor' ] ) ) {
			return $query;
		}

		$vendor_nicename   = $wp_query->query_vars[ 'vendor' ];
		$vendor            = get_user_by( 'slug', $vendor_nicename );
		if( $vendor ) {

			$query[ 'author' ] = $vendor->ID;
	
		}
		return $query;
	}

	public function change_the_title( $title ) {
		global $wp_query;
		if ( ! empty( $wp_query->query_vars['vendor'] ) && ! is_admin() && in_the_loop() && is_page( EDD_FES()->helper->get_option( 'fes-vendor-page', false ) ) ) {
			remove_filter( 'the_title',  array( $this, 'change_the_title' ) );
			$vendor_nicename = $wp_query->query_vars[ 'vendor' ];
			$vendor          = get_user_by( 'slug', $vendor_nicename );

			if( ! $vendor ) {
				return $title;
			}

			$custom = EDD_FES()->helper->get_user_meta( 'name_of_store', $vendor->ID );
			if ( strlen ( trim( $custom ) ) == 0 ){
				$vendor_name = EDD_FES()->vendors->get_vendor_constant_name( $plural = false, $uppercase = true ) . ' ' . $vendor->display_name;
				$title = sprintf( __('The Shop of %s','edd_fes'), $vendor_name );
				$title = apply_filters('fes_change_the_title', $title , $vendor->ID );
				return $title;
			} else {
				return trim( $custom );
			}
		}
		return $title;
	}

	function custom_download_archive($query) {
		if( is_admin() ) {
			return;
		}
		if ( $query->is_author ) {
			$query->set( 'post_type', array('download') );
			remove_action( 'pre_get_posts', array( $this, 'custom_download_archive' ) ); // run once!
		}
	}

	// Remove the 'author' base slug and change to value from settings panel
	function change_wp_author_base() {
		global $wp_rewrite;
		$author_slug = EDD_FES()->vendors->get_vendor_constant_name( $plural = false, $uppercase = false );
		$wp_rewrite->author_base = $author_slug;
	}

	public function vendor_archive_redirect() {
		if ( ! is_author() ) {
			return;
		}

		$author = get_queried_object();

		$enable_redirect = apply_filters( 'fes_vendor_archive_switch', true, $author );
		if (
			EDD_FES()->vendors->is_vendor( $author->data->ID ) &&
			EDD_FES()->vendors->vendor_is_vendor( $author->data->ID ) &&
			$enable_redirect
		) {
			$vendor_url = add_query_arg( 'vendor', $author->data->user_nicename, get_permalink( EDD_FES()->helper->get_option( 'fes-vendor-page', false ) ) );
			$vendor_url = apply_filters( 'fes_vendor_archive_url', $vendor_url, $author );
			wp_redirect( $vendor_url , 301 );
			exit;
		}
	}

	public function vendor_page_updated( $post_id ) {
		if ( !EDD_FES()->helper->get_option( 'fes-vendor-page', false ) ){
			return;
		}

		$page_id = (int)EDD_FES()->helper->get_option( 'fes-vendor-page', false );

		if ( $page_id !== $post_id ) {
			return;
		}

		$this->add_rewrite_rules();

		// Set an option so we know to flush the rewrites at the next admin_init
		add_option( 'fes_permalinks_updated', 1, 'no' );

		return $post_id;
	}

	public function after_vendor_page_update() {
		$fes_permalinks_updated = get_option( 'fes_permalinks_updated' );

		if ( empty( $edd_fes_permalinks_updated ) ) {
			return;
		}

		flush_rewrite_rules();
		delete_option( 'fes_permalinks_updated' );
	}
}
