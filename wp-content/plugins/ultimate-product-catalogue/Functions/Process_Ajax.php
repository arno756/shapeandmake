<?php
/* Processes the ajax requests being put out in the admin area and the front-end
*  of the UPCP plugin */

// Updates the order of items in the catalogue after a user has dragged and dropped them
function Catalogue_Save_Order() {
		global $catalogue_items_table_name;
		$Path = ABSPATH . 'wp-load.php';
		include_once($Path);
		global $wpdb;
		
		foreach ($_POST['list-item'] as $Key=>$ID) {
				$Result = $wpdb->query("UPDATE $catalogue_items_table_name SET Position='" . $Key . "' WHERE Catalogue_Item_ID=" . $ID);
		}
		
}
add_action('wp_ajax_catalogue_update_order', 'Catalogue_Save_Order');

// Records the number of times a product has been viewed
function Record_Item_View() {
		global $items_table_name;
		$Path = ABSPATH . 'wp-load.php';
		include_once($Path);
		global $wpdb;
		
		$Item_ID = $_POST['Item_ID'];
		$Item = $wpdb->get_row("SELECT Item_Views FROM $items_table_name WHERE Item_ID=" . $Item_ID);
		if ($Item->Item_Views == "") {$wpdb->query("UPDATE $items_table_name SET Item_Views=1 WHERE Item_ID=" . $Item_ID);}
		else {$wpdb->query("UPDATE $items_table_name SET Item_Views=Item_Views+1 WHERE Item_ID=" . $Item_ID);}
}
add_action('wp_ajax_record_view', 'Record_Item_View');
add_action( 'wp_ajax_nopriv_record_view', 'Record_Item_View' );

// Records the number of times a product has been viewed
function UPCP_Filter_Catalogue() {
		$Path = ABSPATH . 'wp-load.php';
		include_once($Path);
		
		$id = $_POST['id'];
		$sidebar = $_POST['sidebar'];
		$start_layout = $_POST['start_layout'];
		$excluded_layouts = $_POST['excluded_layouts'];
		$current_page = $_POST['current_page'];
		$default_search_text = $_POST['default_search_text'];
		$ajax_url = $_POST['ajax_url'];
		$ajax_reload = $_POST['ajax_reload'];
		$request_count = $_POST['request_count'];
		
		if ($_POST['Prod_Name'] != $default_search_text) {$Prod_Name = $_POST['Prod_Name'];}
		else {$Prod_Name = "";}
		$Category = $_POST['Category'];
		$SubCategory = $_POST['SubCategory'];
		$Tags = $_POST['Tags'];
		$Custom_Fields = $_POST['Custom_Fields'];
		
		echo do_shortcode("[product-catalogue id='" . $id . "' only_inner='Yes' starting_layout='" . $start_layout . "' excluded_layouts='" . $exclude_layouts . "' current_page='" . $current_page . "' ajax_reload='" . $ajax_reload . "' ajax_url='" . $ajax_url . "' request_count='" . $request_count . "' category='" . $Category . "' subcategory='" . $SubCategory . "' tags='" . $Tags . "' custom_fields='" . $Custom_Fields . "' prod_name='" . $Prod_Name . "']");
}
add_action('wp_ajax_update_catalogue', 'UPCP_Filter_Catalogue');
add_action( 'wp_ajax_nopriv_update_catalogue', 'UPCP_Filter_Catalogue');

// Updates sub-categories drop-down box on the products pages, based on the product's category
function Get_UPCP_SubCategories() {
		global $subcategories_table_name;
		$Path = ABSPATH . 'wp-load.php';
		include_once($Path);
		global $wpdb;
		
		$SubCategories = $wpdb->get_results("SELECT SubCategory_ID, SubCategory_Name FROM $subcategories_table_name WHERE Category_ID=" . $_POST['CatID']);
		foreach ($SubCategories as $SubCategory) {$Response_Array[] = $SubCategory->SubCategory_ID; $Response_Array[] = $SubCategory->SubCategory_Name;}
		if (is_array($Response_Array)) {$Response = implode(",", $Response_Array);}
		else {$Response = "";}
		echo $Response;
}
add_action('wp_ajax_get_upcp_subcategories', 'Get_UPCP_SubCategories');

function Save_Serialized_Product_Page() {	
		if ($_POST['type'] == "mobile") {return update_option("UPCP_Product_Page_Serialized_Mobile", $_POST['serialized_product_page']);}
		else {return update_option("UPCP_Product_Page_Serialized", $_POST['serialized_product_page']);}
}
add_action('wp_ajax_save_serialized_product_page', 'Save_Serialized_Product_Page');
?>