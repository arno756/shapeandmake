<?php
/* Creates the admin page, and fills it in based on whether the user is looking at
*  the overview page or an individual item is being edited */
function UPCP_Output_Options() {
		global $wpdb, $error, $Full_Version;
		global $categories_table_name, $subcategories_table_name, $items_table_name, $item_images_table_name, $catalogues_table_name, $catalogue_items_table_name, $tagged_items_table_name, $tags_table_name, $fields_table_name, $fields_meta_table_name;
		
		$Related_Products = get_option("UPCP_Related_Products");
		$Next_Previous = get_option("UPCP_Next_Previous");

		if (isset($_GET['DisplayPage'])) {
			  $Display_Page = $_GET['DisplayPage'];
		}
		include ABSPATH . 'wp-content/plugins/ultimate-product-catalogue/html/AdminHeader.php';
		if ($_GET['Action'] == "UPCP_Item_Details" or 
			$_GET['Action'] == "UPCP_Category_Details" or 
			$_GET['Action'] == "UPCP_SubCategory_Details" or 
			$_GET['Action'] == "UPCP_Catalogue_Details" or 
			$_GET['Action'] == "UPCP_Tag_Details" or 
			$_GET['Action'] == "UPCP_Field_Details") {
				include ABSPATH . 'wp-content/plugins/ultimate-product-catalogue/html/ItemDetails.php';
		}
		else {include ABSPATH . 'wp-content/plugins/ultimate-product-catalogue/html/MainScreen.php';}
		include ABSPATH . 'wp-content/plugins/ultimate-product-catalogue/html/AdminFooter.php';
}
?>