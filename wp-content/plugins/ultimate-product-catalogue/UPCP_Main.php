<?php
/*
Plugin Name: Ultimate Product Catalogue Plugin
Plugin URI: http://www.EtoileWebDesign.com/ultimate-product-catalogue/
Description: Plugin to create a customizable product catalogue for businesses, restaurants, real estate agents, etc.
Author: Etoile Web Design
Author URI: http://www.EtoileWebDesign.com/
Terms and Conditions: http://www.etoilewebdesign.com/plugin-terms-and-conditions/
Text Domain: UPCP
Version: 3.0.16
*/

global $UPCP_db_version;
global $categories_table_name, $subcategories_table_name, $items_table_name, $item_images_table_name, $catalogues_table_name, $catalogue_items_table_name, $tagged_items_table_name, $tags_table_name, $fields_table_name, $fields_meta_table_name;
global $wpdb;
global $upcp_message;
global $Full_Version;
$categories_table_name = $wpdb->prefix . "UPCP_Categories";
$subcategories_table_name = $wpdb->prefix . "UPCP_SubCategories";
$items_table_name = $wpdb->prefix . "UPCP_Items";
$item_images_table_name = $wpdb->prefix . "UPCP_Item_Images";
$catalogues_table_name = $wpdb->prefix . "UPCP_Catalogues";
$catalogue_items_table_name = $wpdb->prefix . "UPCP_Catalogue_Items";
$tags_table_name = $wpdb->prefix . "UPCP_Tags";
$tagged_items_table_name = $wpdb->prefix . "UPCP_Tagged_Items";
$fields_table_name = $wpdb->prefix . "UPCP_Custom_Fields";
$fields_meta_table_name = $wpdb->prefix . "UPCP_Fields_Meta";
$UPCP_db_version = "3.0.10";

define( 'UPCP_CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'UPCP_CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/*define('WP_DEBUG', true);
$wpdb->show_errors();*/

/* When plugin is activated */
register_activation_hook(__FILE__,'Install_UPCP_DB');
register_activation_hook(__FILE__,'Initial_UPCP_Data');
register_activation_hook(__FILE__,'Initial_UPCP_Options');

/* When plugin is deactivation*/
register_deactivation_hook( __FILE__, 'Remove_UPCP' );

/* Creates the admin menu for the contests plugin */
if ( is_admin() ){
	add_action('admin_menu', 'UPCP_Plugin_Menu');
	add_action('admin_head', 'UPCP_Admin_Options');
	add_action('admin_init', 'Add_UPCP_Scripts');
	add_action('widgets_init', 'Update_UPCP_Content');
	add_action('admin_notices', 'UPCP_Error_Notices');
}

function Remove_UPCP() {
  	/* Deletes the database field */
	delete_option('UPCP_db_version');
}

/* Admin Page setup */
function UPCP_Plugin_Menu() {
	$Access_Role = get_option("UPCP_Access_Role");

	if ($Access_Role == "") {$Access_Role = "administrator";}
	add_menu_page('Ultimate Product Catalogue Plugin', 'Product Catalogue', $Access_Role, 'UPCP-options', 'UPCP_Output_Options',null , '50.5');
}

/* Add localization support */
function UPCP_localization_setup() {
	load_plugin_textdomain('UPCP', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}
add_action('after_setup_theme', 'UPCP_localization_setup');

// Add settings link on plugin page
function UPCP_plugin_settings_link($links) { 
	$settings_link = '<a href="admin.php?page=UPCP-options">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'UPCP_plugin_settings_link' );

/* Put in the pretty permalinks filter */
add_filter( 'query_vars', 'add_query_vars_filter' );

function Add_UPCP_Scripts() {
	if (isset($_GET['page']) && $_GET['page'] == 'UPCP-options') {
		$url_one = plugins_url("ultimate-product-catalogue/js/Admin.js");
		$url_two = plugins_url("ultimate-product-catalogue/js/sorttable.js");
		$url_three = plugins_url("ultimate-product-catalogue/js/get_sub_cats.js");
		$url_four = plugins_url("ultimate-product-catalogue/js/wp_upcp_uploader.js");
		$url_five = plugins_url("ultimate-product-catalogue/js/bootstrap.min.js");
		$url_six = plugins_url("ultimate-product-catalogue/js/jquery.confirm.min.js");
		$url_seven = plugins_url("ultimate-product-catalogue/js/product-page-builder.js");
		$url_eight = plugins_url("ultimate-product-catalogue/js/jquery.gridster.js");
		wp_enqueue_script('PageSwitch', $url_one, array('jquery'));
		wp_enqueue_script('sorttable', $url_two, array('jquery'));
		wp_enqueue_script('UpdateSubCats', $url_three, array('jquery'));
		wp_enqueue_script('wp_upcp_uploader', $url_four, array('jquery'));
		wp_enqueue_script('Bootstrap', $url_five, array('jquery'));
		wp_enqueue_script('Confirm', $url_six, array('jquery'));
		wp_enqueue_script('Page-Builder', $url_seven, array('jquery'), '1.0', true);
		wp_enqueue_script('Gridster', $url_eight, array('jquery'), '1.0', true);
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('update-catalogue-order', plugin_dir_url(__FILE__) . '/js/update-catalogue-order.js');
		wp_enqueue_media();
	}
}

add_action( 'wp_enqueue_scripts', 'UPCP_Add_Stylesheet' );
function UPCP_Add_Stylesheet() {
    global $Full_Version;
    
    $Catalogue_Style = get_option("UPCP_Catalogue_Style");
	$Sidebar_Style = get_option("UPCP_Sidebar_Style");
	$Pagination_Style = get_option("UPCP_Pagination_Style");

    wp_register_style( 'catalogue-style', plugins_url('css/catalogue-style.css', __FILE__) );
    wp_enqueue_style( 'catalogue-style' );
	if (is_rtl()) {
		wp_register_style( 'upcp-rtl-style', plugins_url('css/rtl-style.css', __FILE__) );
    	wp_enqueue_style( 'upcp-rtl-style' );
	}
	if ($Full_Version == "Yes") {
		wp_register_style( 'upcp-gridster', plugins_url("ultimate-product-catalogue/css/jquery.gridster.css"));
    	wp_enqueue_style( 'upcp-gridster' );
		//wp_enqueue_style( 'wp-admin' );
	}

	if ($Catalogue_Style != "None") {wp_register_style('upcp-addtl-stylesheet', UPCP_CD_PLUGIN_URL . "css/addtl/" . $Catalogue_Style . ".css"); wp_enqueue_style('upcp-addtl-stylesheet');}
	if ($Sidebar_Style != "None") {wp_register_style('upcp-sidebar', UPCP_CD_PLUGIN_URL . "css/addtl/" . $Sidebar_Style . ".css"); wp_enqueue_style('upcp-sidebar');}
	if ($Pagination_Style != "None") {wp_register_style('upcp-pagination', UPCP_CD_PLUGIN_URL . "css/addtl/" . $Pagination_Style . ".css"); wp_enqueue_style('upcp-pagination');}
}

add_action( 'wp_enqueue_scripts', 'Add_UPCP_FrontEnd_Scripts' );
function Add_UPCP_FrontEnd_Scripts() {
	wp_enqueue_script('upcpjquery', plugins_url( '/js/upcp-jquery-functions.js' , __FILE__ ), array( 'jquery' ));
	wp_enqueue_script('upcp-page-builder', plugins_url( '/js/product-page-display.js' , __FILE__ ), array( 'jquery' ), '1.0', true);
	wp_enqueue_script('gridster', plugins_url("/js/jquery.gridster.js", __FILE__ ), array( 'jquery' ), '1.0', true);
}

function UPCP_Admin_Options() {
	//$url = plugins_url("ultimate-product-catalogue/css/Admin.css");
	//echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	wp_enqueue_style( 'upcp-admin', plugins_url("ultimate-product-catalogue/css/Admin.css"));
	wp_enqueue_style( 'upcp-gridster', plugins_url("ultimate-product-catalogue/css/jquery.gridster.css"));
    //wp_enqueue_style( 'bootstrap', plugins_url("ultimate-product-catalogue/css/bootstrap.min.css"));
}

add_action('activated_plugin','save_error');
function save_error(){
    update_option('plugin_error',  ob_get_contents());
}

$Full_Version = get_option("UPCP_Full_Version");

if (isset($_POST['Upgrade_To_Full'])) {
	add_action('admin_init', 'Upgrade_To_Full');
}

include "Functions/Initial_Data.php";
include "Functions/Initial_Options.php";
include "Functions/UPCP_Output_Options.php";
include "Functions/Update_Admin_Databases.php";
include "Functions/Install_UPCP.php";
include "Functions/Error_Notices.php";
include "Functions/Update_UPCP_Content.php";
include "Functions/Prepare_Data_For_Insertion.php";
include "Functions/Process_Ajax.php";
include "Functions/Shortcodes.php";
include "Functions/Full_Upgrade.php";
include "Functions/Version_Upgrade.php";
include "Functions/Rewrite_Rules.php";
include "Functions/Update_Tables.php";
include "Functions/FrontEndAjaxUrl.php";
include "Functions/UPCP_Create_XML_Sitemap.php";
include "Functions/UPCP_Export_To_Excel.php";
include "Functions/UPCP_Widget.php";

// Updates the UPCP database when required
if (get_option('UPCP_DB_Version') != $UPCP_db_version) {
	UpdateTables();
	update_option('UPCP_DB_Version', $UPCP_db_version);
}

$rules = get_option('rewrite_rules');
$PrettyLinks = get_option("UPCP_Pretty_Links");
if (!isset($rules['"(.?.+?)/([^&]+)/?$"']) and $PrettyLinks == "Yes") {
	add_filter( 'query_vars', 'add_query_vars_filter' );
	add_filter('init', 'UPCP_Rewrite_Rules');
	update_option("UPCP_Update_RR_Rules", "No");
}

?>