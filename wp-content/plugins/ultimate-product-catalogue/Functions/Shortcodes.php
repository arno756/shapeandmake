<?php 
/* The function that creates the HTML on the front-end, based on the parameters
* supplied in the product-catalog shortcode */
function Insert_Product_Catalog($atts) {
	// Include the required global variables, and create a few new ones
	global $wpdb, $categories_table_name, $subcategories_table_name, $tags_table_name, $tagged_items_table_name, $catalogues_table_name, $catalogue_items_table_name, $items_table_name, $fields_meta_table_name;
	global $ReturnString, $ProdCats, $ProdSubCats, $ProdTags, $ProdCustomFields, $ProdCatString, $ProdSubCatString, $ProdTagString, $ProdCustomFieldsString, $Catalogue_ID, $Catalogue_Layout_Format, $Catalogue_Sidebar, $Full_Version;

	$ReturnString = "";
	$Filter = get_option("UPCP_Filter_Type");
	$Color = get_option("UPCP_Color_Scheme");
	$Links = get_option("UPCP_Product_Links");
	$Detail_Image = get_option("UPCP_Details_Image");
	$Pretty_Links = get_option("UPCP_Pretty_Links");
	$Mobile_Style = get_option("UPCP_Mobile_SS");
	$Tag_Logic = get_option("UPCP_Tag_Logic");
	$Links = get_option("UPCP_Product_Links");
	$Pagination_Location = get_option("UPCP_Pagination_Location");
	$CaseInsensitiveSearch = get_option("UPCP_Case_Insensitive_Search");
	$Maintain_Filtering = get_option("UPCP_Maintain_Filtering");
	$Products_Per_Page = get_option("UPCP_Products_Per_Page");
	$ProductSearch = get_option("UPCP_Product_Search");

	$Products_Pagination_Label = get_option("UPCP_Products_Pagination_Label");
	$Product_Name_Search_Label = get_option("UPCP_Product_Name_Search_Label");
	$Product_Search_Text_Label = get_option("UPCP_Product_Name_Text_Label");
	$No_Results_Found_Label = get_option("UPCP_No_Results_Found_Label");
	if ($Products_Pagination_Label != "") {$Products_Pagination_Text = $Products_Pagination_Label;}
	else {$Products_Pagination_Text = __(' products', 'UPCP');}
	if ($Product_Name_Search_Label != "") {$SearchLabel = $Product_Name_Search_Label;}
	else {
		if ($ProductSearch == "namedesc" or $ProductSearch == "namedesccust") {$SearchLabel = __("Product Search:", 'UPCP');}
		else {$SearchLabel = __("Product Name:", 'UPCP');}
	}
	if ($prod_name != "") {$Product_Name_Text = $prod_name;}
	elseif ($Product_Search_Text_Label != "") {$Product_Name_Text = $Product_Search_Text_Label;
	}
	else {
		if ($ProductSearch == "namedesc" or $ProductSearch == "namedesccust") {$Product_Name_Text = __("Search...", 'UPCP');}
		else {$Product_Name_Text = __("Name...", 'UPCP');}
	}
		
	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
				"id" => "1",
				"excluded_layouts" => "None",
				"starting_layout" => "",
				"products_per_page" => "",
				"current_page" => 1,
				"sidebar" => "Yes",
				"only_inner" => "No",
				"ajax_reload" => "No",
				"ajax_url" => "",
				"request_count" => 0,
				"category" => "",
				"subcategory" => "",
				"tags" => "",
				"custom_fields" => "",
				"prod_name" => ""),
			$atts
		)
	);

	if ($ajax_reload != "No"){$ajax_reload = ucfirst(strtolower($ajax_reload));}
		
	// Select the catalogue information from the database 
	$Catalogue = $wpdb->get_row("SELECT * FROM $catalogues_table_name WHERE Catalogue_ID=" . $id);
	$CatalogueItems = $wpdb->get_results("SELECT * FROM $catalogue_items_table_name WHERE Catalogue_ID=" . $id . " ORDER BY Position");
		
	// Add any additional CSS in-line
	if ($Catalogue->Catalogue_Custom_CSS != "") {
		$HeaderBar .= "<style type='text/css'>";
		$HeaderBar .= $Catalogue->Catalogue_Custom_CSS;
		$HeaderBar .= "</style>";
	}
	if ($Detail_Image != "") {
		$HeaderBar .= "<style type='text/css'>";
		$HeaderBar .= ".upcp-thumb-details-link, .upcp-list-details-link, .upcp-detail-details-link {";
		$HeaderBar .= "background: url('" . $Detail_Image . "');";
		$HeaderBar .= "}";
		$HeaderBar .= "</style>";
	}

	$Top_JS .= "<script language='JavaScript' type='text/javascript'>";	
	if ($Maintain_Filtering == "Yes") {$Top_JS .= "var maintain_filtering = 'Yes';";}
	else {$Top_JS .= "var maintain_filtering = 'No';";}
	$Top_JS .= "</script>";

	$HeaderBar .= $Top_JS;

	if ($Links == "New") {$Target = "_blank";}
	else {$Target = "_self";}
	
	$HeaderBar .= "<form id='upcp-hidden-filtering-form' method='post' target='" . $Target . "'>";
	$HeaderBar .= "<input type='hidden' id='upcp-selected-categories' name='categories' value='" . $_POST['categories'] . "' />";
	$HeaderBar .= "<input type='hidden' id='upcp-selected-subcategories' name='subcategories' value='" . $_POST['subcategories'] . "' />";
	$HeaderBar .= "<input type='hidden' id='upcp-selected-tags' name='tags' value='" . $_POST['tags'] . "' />";
	$HeaderBar .= "<input type='hidden' id='upcp-selected-prod-name' name='prod_name' value='" . $_POST['prod_name'] . "' />";
	$HeaderBar .= "</form>";
		
	if (get_query_var('single_product') != "" or $_GET['SingleProduct'] != "") {
		$ReturnString .= $HeaderBar;
		$ReturnString .= SingleProductPage(); 
		return $ReturnString;
	}
						
	$Catalogue_ID = $id;
	$Catalogue_Sidebar = $sidebar;
	$Starting_Layout = ucfirst($starting_layout);
	if ($excluded_layouts != "None") {$Excluded_Layouts = explode(",", $excluded_layouts);}
	else {$Excluded_Layouts = array();}
		
	if (isset($_GET['categories'])) {$category = explode(",", $_GET['categories']);}
	elseif (isset($_POST['categories']) and $_POST['categories'] != "") {$category = explode(",", $_POST['categories']);}
	elseif ($category == "") {$category = array();}
	else {$category = explode(",", $category);}	
	if (isset($_GET['sub-categories'])) {$subcategory = explode(",", $_GET['sub-categories']);}	
	elseif (isset($_POST['subcategories']) and $_POST['subcategories'] != "") {$subcategory = explode(",", $_POST['subcategories']);}
	elseif ($subcategory == "") {$subcategory = array();}
	else {$subcategory = explode(",", $subcategory);}	
	if (isset($_GET['tags'])) {$tags = explode(",", $_GET['tags']);}
	elseif (isset($_POST['tags']) and $_POST['tags'] != "") {$tags = explode(",", $_POST['tags']);}	
	elseif ($tags == "") {$tags = array();}
	else {$tags = explode(",", $tags);}	

	if (isset($_POST['search_prod_name']) and $_POST['search_prod_name'] != "") {$search_prod_name = $_POST['prod_name'];}

	//Pagination early work
	if ($products_per_page == "") {$products_per_page = $Products_Per_Page;}
	if ($category != "" or $subcategory != "" or $tags != "" or $search_prod_name != "") {$Filtered = "Yes";}
	else {$Filtered = "No";}
	
		
	$ReturnString .= "<div class='upcp-Hide-Item' id='upcp-shortcode-atts'>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-catalogue-id'>" . $id . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-catalogue-sidebar'>" . $sidebar . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-starting-layout'>" . $starting_layout . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-current-layout'>" . $starting_layout . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-exclude-layouts'>" . $excluded_layouts . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-current-page'>" . $current_page . "</div>";
	$ReturnString .= "<div class='shortcode-attr' id='upcp-default-search-text'>" . $Product_Name_Text . "</div>";
	if ($ajax_reload == "Yes") {$ReturnString .= "<div class='shortcode-attr' id='upcp-base-url'>" . $ajax_url . "</div>";}
	else {
		$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		//if ($uri_parts[0] != "/") {$ReturnString .= "<div class='shortcode-attr' id='upcp-base-url'>" . $uri_parts[0] . "</div>";}
		if (strpos($uri_parts[1], "page_id") === false) {$ReturnString .= "<div class='shortcode-attr' id='upcp-base-url'>" . $uri_parts[0] . "</div>";}
		else {$ReturnString .= "<div class='shortcode-attr' id='upcp-base-url'>" . $uri_parts[0] . "?" . $uri_parts[1] . "</div>";}
		//else {$ReturnString .= "<div class='shortcode-attr' id='upcp-base-url'>/?" . $uri_parts[1] . "</div>";}
	}
	$ReturnString .= "</div>";
		
	if (sizeOf($Excluded_Layouts)>0) {for ($i=0; $i<sizeOf($Excluded_Layouts); $i++) {$ExcludedLayouts[$i] = ucfirst(trim($Excluded_Layouts[$i]));}}
	else {$ExcludedLayouts = array();}
		
	if ($Starting_Layout == "") {
		if (!in_array("Thumbnail", $Excluded_Layouts)) {$Starting_Layout = "Thumbnail";}
		elseif (!in_array("List", $Excluded_Layouts)) {$Starting_Layout = "List";}
		else {$Starting_Layout = "Detail";}
	}
				
	// Make sure that the layout is set
	if ($layout_format != "Thumbnail" and $layout_format != "List") {
		if ($Catalogue->Catalogue_Layout_Format != "") {$format = $Catalogue->Catalogue_Layout_Format;}
		else {$format = "Thumbnail";}
	}
	else {$format = $layout_format;}
		
	// Arrays to store what categories, sub-categories and tags are applied to the product in the catalogue
	$ProdCats = array();
	$ProdSubCats = array();
	$ProdTags = array();
	$ProdCustomFields = array();

	// If filtering for custom fields, build the field/value query string
	if ($custom_fields != "") {
		$Custom_Field_IDs = explode(",", $custom_fields);
		foreach ($Custom_Field_IDs as $Custom_Field_ID){
			$Field_ID = substr($Custom_Field_ID, 0, strpos($Custom_Field_ID, "=>"));
			$Field_Value = substr($Custom_Field_ID, strpos($Custom_Field_ID, "=>")+2);
			$Selected_Custom_Fields[$Field_ID][] = $Field_Value;
		}
		$Custom_Fields_Sql_String .= "(";
		foreach ($Selected_Custom_Fields as $Field_ID => $Selected_Custom_Field) {
			$Custom_Fields_Sql_String .= "(";
			$Custom_Fields_Sql_String .= "Field_ID='" . $Field_ID . "' AND (";
			foreach ($Selected_Custom_Field as $Value){
				$Custom_Fields_Sql_String .= "Meta_Value='" . $Value . "' OR ";
			}
			$Custom_Fields_Sql_String = substr($Custom_Fields_Sql_String, 0, -4);
			$Custom_Fields_Sql_String .= "))";
			$Custom_Fields_Sql_String .= " OR ";
		}
		$Custom_Fields_Sql_String = substr($Custom_Fields_Sql_String, 0, -4);
		$Custom_Fields_Sql_String .= ")";
		$Custom_Field_Count = sizeOf($Selected_Custom_Fields);
	}
		
	$ProdThumbString .=  "<div id='prod-cat-" . $id . "' class='prod-cat thumb-display ";
	if ($Starting_Layout != "Thumbnail") {$ProdThumbString .= "hidden-field";}
	$ProdThumbString .= "'>\n";
	$ProdThumbString .= "%upcp_pagination_placeholder_top%";
		
	$ProdListString .=  "<div id='prod-cat-" . $id . "' class='prod-cat list-display ";
	if ($Starting_Layout != "List") {$ProdListString .= "hidden-field";}
	$ProdListString .= "'>\n";
	$ProdListString .= "%upcp_pagination_placeholder_top%";
		
	$ProdDetailString .=  "<div id='prod-cat-" . $id . "' class='prod-cat detail-display ";
	if ($Starting_Layout != "Detail") {$ProdDetailString .= "hidden-field";} 
	$ProdDetailString .= "'>\n";
	$ProdDetailString .= "%upcp_pagination_placeholder_top%";
		
	$Product_Count = 0;
	foreach ($CatalogueItems as $CatalogueItem) {
				
		// If the item is a product, then simply call the AddProduct function to add it to the code
		if ($CatalogueItem->Item_ID != "" and $CatalogueItem->Item_ID != 0) {
			$Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_ID=" . $CatalogueItem->Item_ID);
			$ProdTagObj = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID=" . $CatalogueItem->Item_ID);
			if ($ajax_reload == "No") {
				$Prod_Custom_Fields = $wpdb->get_results("SELECT Field_ID, Meta_Value FROM $fields_meta_table_name WHERE Item_ID=" . $Product->Item_ID);
			}
						
			if ($Product->Item_Display_Status != "Hide") {
				if (sizeOf($category) == 0 or in_array($Product->Category_ID, $category)) {
					if (sizeOf($subcategory) == 0 or in_array($Product->SubCategory_ID, $subcategory)) {
						$ProdTag = ObjectToArray($ProdTagObj);
						$Tag_Check = CheckTags($tags, $ProdTag, $Tag_Logic);
						if ($Tag_Check == "Yes") {
							$Custom_Field_Check = Custom_Field_Check($Custom_Fields_Sql_String, $Custom_Field_Count, $Product->Item_ID);
							if ($Custom_Field_Check == "Yes") {
								$Name_Search_Match = SearchProductName($Product->Item_ID, $Product->Item_Name, $Product->Item_Description, $prod_name, $CaseInsensitiveSearch, $ProductSearch);
								if ($Name_Search_Match == "Yes") {
									$Pagination_Check = CheckPagination($Product_Count, $products_per_page, $current_page, $Filtered);
									if ($Pagination_Check == "OK") {
										$HeaderBar .= "<a id='hidden_FB_link-" . $Product->Item_ID . "' class='fancybox' href='#prod-cat-addt-details-" . $Product->Item_ID . "'></a>";
										if (!in_array("Thumbnail", $ExcludedLayouts)) {$ProdThumbString .= AddProduct("Thumbnail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
										if (!in_array("List", $ExcludedLayouts)) {$ProdListString .= AddProduct("List", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
										if (!in_array("Detail", $ExcludedLayouts)) {$ProdDetailString .= AddProduct("Detail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
									}
									$Product_Count++;
								}
			}}}}}
			if ($ajax_reload == "No") {FilterCount($Product, $ProdTagObj, $Prod_Custom_Fields);}
			unset($NameSearchMatch);
		}
				
		// If the item is a category, then add the appropriate extra HTML and call the AddProduct function
		// for each individual product in the category
		if ($CatalogueItem->Category_ID != "" and $CatalogueItem->Category_ID != 0) {
			if (sizeOf($category) == 0 or in_array($CatalogueItem->Category_ID, $category)) {					
				$CatProdCount = 0;
				$Category = $wpdb->get_row("SELECT Category_Name FROM $categories_table_name WHERE Category_ID=" . $CatalogueItem->Category_ID);
						
				$ProdThumbString .= "<div id='prod-cat-category-" . $CatalogueItem->Category_ID . "' class='prod-cat-category upcp-thumb-category'>\n";
				$ProdListString .= "<div id='prod-cat-category-" . $CatalogueItem->Category_ID . "' class='prod-cat-category upcp-list-category'>\n";
				$ProdDetailString .= "<div id='prod-cat-category-" . $CatalogueItem->Category_ID . "' class='prod-cat-category upcp-detail-category'>\n";
						
				$ProdThumbString .= "%Category_Label%";
				$ProdListString .= "%Category_Label%";
				$ProdDetailString .= "%Category_Label%";
						
				$CatThumbHead = "<div id='prod-cat-category-label-" . $CatalogueItem->Category_ID . "' class='prod-cat-category-label upcp-thumb-category-label'>" . $Category->Category_Name ."</div>\n";
				$CatListHead = "<div id='prod-cat-category-label-" . $CatalogueItem->Category_ID . "' class='prod-cat-category-label upcp-list-category-label'>" . $Category->Category_Name ."</div>\n";
				$CatDetailHead = "<div id='prod-cat-category-label-" . $CatalogueItem->Category_ID . "' class='prod-cat-category-label upcp-detail-category-label'>" . $Category->Category_Name ."</div>\n";
						
				$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Category_ID=" . $CatalogueItem->Category_ID);
						
				foreach ($Products as $Product) {
					$ProdTagObj = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID=" . $Product->Item_ID);
					if ($ajax_reload == "No") {
						$Prod_Custom_Fields = $wpdb->get_results("SELECT Field_ID, Meta_Value FROM $fields_meta_table_name WHERE Item_ID=" . $Product->Item_ID);
					}
								
					if ($Product->Item_Display_Status != "Hide") {
						if (sizeOf($category) == 0 or in_array($Product->Category_ID, $category)) {
							if (sizeOf($subcategory) == 0 or in_array($Product->SubCategory_ID, $subcategory)) {
								$ProdTag = ObjectToArray($ProdTagObj);
								$Tag_Check = CheckTags($tags, $ProdTag, $Tag_Logic);
								if ($Tag_Check == "Yes") {
									$Custom_Field_Check = Custom_Field_Check($Custom_Fields_Sql_String, $Custom_Field_Count, $Product->Item_ID);
									if ($Custom_Field_Check == "Yes") {
										$Name_Search_Match = SearchProductName($Product->Item_ID, $Product->Item_Name, $Product->Item_Description, $prod_name, $CaseInsensitiveSearch, $ProductSearch);
										if ($Name_Search_Match == "Yes") {
											$Pagination_Check = CheckPagination($Product_Count, $products_per_page, $current_page, $Filtered);
											if ($Pagination_Check == "OK") {
												$HeaderBar .= "<a id='hidden_FB_link-" . $Product->Item_ID . "' class='fancybox' href='#prod-cat-addt-details-" . $Product->Item_ID . "'></a>";
												if (!in_array("Thumbnail", $ExcludedLayouts)) {$ProdThumbString .= AddProduct("Thumbnail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
												if (!in_array("List", $ExcludedLayouts)) {$ProdListString .= AddProduct("List", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
												if (!in_array("Detail", $ExcludedLayouts)) {$ProdDetailString .= AddProduct("Detail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
												$CatProdCount++;
											}
											$Product_Count++;
										}
					}}}}}
					if ($ajax_reload == "No") {FilterCount($Product, $ProdTagObj, $Prod_Custom_Fields);}
					unset($NameSearchMatch);
				}
						
				if ($CatProdCount > 0) {
					$ProdThumbString =  str_replace("%Category_Label%", $CatThumbHead, $ProdThumbString);
					$ProdListString = str_replace("%Category_Label%", $CatListHead, $ProdListString);
					$ProdDetailString = str_replace("%Category_Label%", $CatDetailHead, $ProdDetailString);
				}
				else {
					$ProdThumbString = str_replace("%Category_Label%", "", $ProdThumbString);
					$ProdListString = str_replace("%Category_Label%", "", $ProdListString);
					$ProdDetailString = str_replace("%Category_Label%", "", $ProdDetailString);
				}
						
				$ProdThumbString .= "</div>";
				$ProdListString .= "</div>";
				$ProdDetailString .= "</div>";
		}}
				
		// If the item is a sub-category, then add the appropriate extra HTML and call the AddProduct function
		// for each individual product in the sub-category
		if ($CatalogueItem->SubCategory_ID != "" and $CatalogueItem->SubCategory_ID != 0) {
			if (sizeOf($subcategory) == 0 or in_array($CatalogueItem->SubCategory_ID, $subcategory)) {
				$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE SubCategory_ID=" . $CatalogueItem->SubCategory_ID);
						
				foreach ($Products as $Product) {
					$ProdTagObj = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID=" . $CatalogueItem->Item_ID);
					if ($ajax_reload == "No") {
						$Prod_Custom_Fields = $wpdb->get_results("SELECT Field_ID, Meta_Value FROM $fields_meta_table_name WHERE Item_ID=" . $Product->Item_ID);
					}
								
					if ($Product->Item_Display_Status != "Hide") {
						if (sizeOf($category) == 0 or in_array($Product->Category_ID, $category)) {
							if (sizeOf($subcategory) == 0 or in_array($Product->SubCategory_ID, $subcategory)) {
								$ProdTag = ObjectToArray($ProdTagObj);
								$Tag_Check = CheckTags($tags, $ProdTag, $Tag_Logic);
								if ($Tag_Check == "Yes") {
									$Custom_Field_Check = Custom_Field_Check($Custom_Fields_Sql_String, $Custom_Field_Count, $Product->Item_ID);
									if ($Custom_Field_Check == "Yes") {
										$Name_Search_Match = SearchProductName($Product->Item_ID, $Product->Item_Name, $Product->Item_Description, $prod_name, $CaseInsensitiveSearch, $ProductSearch);
										if ($Name_Search_Match == "Yes") {
											$Pagination_Check = CheckPagination($Product_Count, $products_per_page, $current_page, $Filtered);
											if ($Pagination_Check == "OK") {
												$HeaderBar .= "<a id='hidden_FB_link-" . $Product->Item_ID . "' class='fancybox' href='#prod-cat-addt-details-" . $Product->Item_ID . "'></a>";
												if (!in_array("Thumbnail", $ExcludedLayouts)) {$ProdThumbString .= AddProduct("Thumbnail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
												if (!in_array("List", $ExcludedLayouts)) {$ProdListString .= AddProduct("List", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
												if (!in_array("Detail", $ExcludedLayouts)) {$ProdDetailString .= AddProduct("Detail", $Product->Item_ID, $Product, $ProdTagObj, $ajax_reload, $ajax_url);}
											}	
											$Product_Count++;
										}
					}}}}}
					if ($ajax_reload == "No") {FilterCount($Product, $ProdTagObj, $Prod_Custom_Fields);}
					unset($NameSearchMatch);
				}
		}}
				
		//if ($Pagination_Check == "Over") {break;}
	}

	if ($Product_Count == 0) {
		$ProdThumbString .= $No_Results_Found_Label;
		$ProdListString .= $No_Results_Found_Label;
		$ProdDetailString .= $No_Results_Found_Label;
	}
				
	$ProdThumbString .= "<div class='upcp-clear'></div>\n";
	$ProdListString .= "<div class='upcp-clear'></div>\n";
	$ProdDetailString .= "<div class='upcp-clear'></div>\n";
		
	if ($Pagination_Location == "Bottom" or $Pagination_Location == "Both") {
		$ProdThumbString .= "%upcp_pagination_placeholder_bottom%";
		$ProdListString .= "%upcp_pagination_placeholder_bottom%";
		$ProdDetailString .= "%upcp_pagination_placeholder_bottom%";
				
		/*$ProdThumbString .= "<div class='upcp-clear'></div>\n";
		$ProdListString .= "<div class='upcp-clear'></div>\n";
		$ProdDetailString .= "<div class='upcp-clear'></div>\n";*/
	}
		
	$ProdThumbString .= "</div>\n";
	$ProdListString .= "</div>\n";
	$ProdDetailString .= "</div>\n";
		
	if (in_array("Thumbnail", $ExcludedLayouts)) {unset($ProdThumbString);}
	if (in_array("List", $ExcludedLayouts)) {unset($ProdListString);}
	if (in_array("Detail", $ExcludedLayouts)) {unset($ProdDetailString);}
		
	//Deal with creating the page counter, if pagination is neccessary
	if ($Filtered == "Yes") {$Total_Products = $Product_Count;}
	else {$Total_Products = $Catalogue->Catalogue_Item_Count;}
		
	if ($Total_Products > $products_per_page) {
		$Num_Pages = ceil($Total_Products / $products_per_page);
				
		$PrevPage = max($current_page - 1, 1);
		$NextPage = min($current_page + 1, $Num_Pages);
				
		$PaginationString .= "<div class='catalogue-nav'>";
		$PaginationString .= "<span class='displaying-num'>" . $Total_Products . $Products_Pagination_Text . "</span>";
		$PaginationString .= "<span class='pagination-links'>";
		$PaginationString .= "<a class='first-page' title='Go to the first page' href='#' onclick='UPCP_DisplayPage(\"1\")'>&#171;</a>";
		$PaginationString .= "<a class='prev-page' title='Go to the previous page' href='#' onclick='UPCP_DisplayPage(\"" . $PrevPage . "\")'>&#8249;</a>";
		$PaginationString .= "<span class='paging-input'>" . $current_page . __(' of ', 'UPCP') . "<span class='total-pages'>" . $Num_Pages . "</span></span>";
		$PaginationString .= "<a class='next-page' title='Go to the next page' href='#' onclick='UPCP_DisplayPage(\"" . $NextPage . "\")'>&#8250;</a>";
		$PaginationString .= "<a class='last-page' title='Go to the last page' href='#' onclick='UPCP_DisplayPage(\"" . $Num_Pages . "\")'>&#187;</a>";
		$PaginationString .= "</span>";
		$PaginationString .= "</div>";
				
		if ($current_page == 1) {$PaginationString = str_replace("first-page", "first-page disabled", $PaginationString);}
		if ($current_page == 1) {$PaginationString = str_replace("prev-page", "prev-page disabled", $PaginationString);}
		if ($current_page == $Num_Pages) {$PaginationString = str_replace("next-page", "next-page disabled", $PaginationString);}
		if ($current_page == $Num_Pages) {$PaginationString = str_replace("last-page", "last-page disabled", $PaginationString);}
		/*if ($current_page != 1) {$PaginationString .= "<a href='#' onclick='UPCP_DisplayPage(\"1\")>" . __('First', 'UPCP') . "</a>";}
		if ($current_page != 1) {$PaginationString .= "<a href='#' onclick='UPCP_DisplayPage(\"" . $current_page - 1 . "\")>" . __('Previous', 'UPCP') . "</a>";}
				
		$PaginationString .= "<span class='paging-input'>" . $current_page . __(' of ', 'UPCP') . "<span class='total-pages'>" . $Num_Pages . "</span></span>";
				
		if ($current_page != $Num_Pages) {$PaginationString .= "<a href='#' onclick='UPCP_DisplayPage(\"" . $current_page + 1 . "\")>" . __('Next', 'UPCP') . "</a>";}
		if ($current_page != $Num_Pages) {$PaginationString .= "<a href='#' onclick='UPCP_DisplayPage(\"" . $Num_Pages . "\")>" . __('Last', 'UPCP') . "</a>";}*/
	}		
	if ($Pagination_Location == "Bottom") {
		$ProdThumbString = str_replace("%upcp_pagination_placeholder_top%", "", $ProdThumbString);
		$ProdListString = str_replace("%upcp_pagination_placeholder_top%", "", $ProdListString);
		$ProdDetailString = str_replace("%upcp_pagination_placeholder_top%", "", $ProdDetailString);
	}
	if ($Pagination_Location == "Top") {
		$ProdThumbString = str_replace("%upcp_pagination_placeholder_bottom%", "", $ProdThumbString);
		$ProdListString = str_replace("%upcp_pagination_placeholder_bottom%", "", $ProdListString);
		$ProdDetailString = str_replace("%upcp_pagination_placeholder_bottom%", "", $ProdDetailString);
	}
				
	$ProdThumbString = str_replace("%upcp_pagination_placeholder_top%", $PaginationString, $ProdThumbString);
	$ProdListString = str_replace("%upcp_pagination_placeholder_top%", $PaginationString, $ProdListString);
	$ProdDetailString = str_replace("%upcp_pagination_placeholder_top%", $PaginationString, $ProdDetailString);
	$ProdThumbString = str_replace("%upcp_pagination_placeholder_bottom%", $PaginationString, $ProdThumbString);
	$ProdListString = str_replace("%upcp_pagination_placeholder_bottom%", $PaginationString, $ProdListString);
	$ProdDetailString = str_replace("%upcp_pagination_placeholder_bottom%", $PaginationString, $ProdDetailString);
		
	// Create string from the arrays, should use the implode function instead
	foreach ($ProdCats as $key=>$value) {$ProdCatString .= $key . ",";}
	$ProdCatString = trim($ProdCatString, " ,");
	foreach ($ProdSubCats as $key=>$value) {$ProdSubCatString .= $key . ",";}
	$ProdSubCatString = trim($ProdSubCatString, " ,");
	foreach ($ProdTags as $key=>$value) {$ProdTagString .= $key . ",";}
	$ProdTagString = trim($ProdTagString, " ,");
	foreach ($ProdCustomFields as $key=>$value) {$ProdCustomFieldsString .= $key . ",";}
	$ProdCustomFieldsString = trim($ProdCustomFieldsString, " ,");
		
	// If the sidebar is requested, add it
	if (($sidebar == "Yes" or $sidebar == "yes" or $sidebar == "YES") and $only_inner != "Yes") {
		$SidebarString = BuildSidebar($category, $subcategory, $tags, $search_prod_name);
	}
		
	if ($Mobile_Style == "Yes") {
		$MobileMenuString .= "<div id='prod-cat-mobile-menu' class='upcp-mobile-menu'>\n";
		$MobileMenuString .= "<div id='prod-cat-mobile-search'>\n";
		if ($Tag_Logic == "OR") {$MobileMenuString .= "<input type='text' id='upcp-mobile-search' class='jquery-prod-name-text mobile-search' name='Mobile_Search' value='" . __('Product Name', 'UPCP') . "...' onfocus='FieldFocus(this);' onblur='FieldBlur(this);' onkeyup='UPCP_Filer_Results_OR();'>\n";}
		else {$MobileMenuString .= "<input type='text' id='upcp-mobile-search' class='jquery-prod-name-text mobile-search' name='Mobile_Search' value='" . __('Product Name', 'UPCP') . "...' onfocus='FieldFocus(this);' onblur='FieldBlur(this);'  onkeyup='UPCP_Filer_Results();'>\n";}
		$MobileMenuString .= "</div>";
		$MobileMenuString .= "</div>";
	}
		
	$HeaderBar .= "<div class='prod-cat-header-div " . $Color . "-prod-cat-header-div'>";
	$HeaderBar .= "<div class='prod-cat-header-padding'></div>";
	$HeaderBar .= "<div id='starting-layout' class='hidden-field'>" . $Starting_Layout . "</div>";
	if (!in_array("Thumbnail", $ExcludedLayouts)) {
		$HeaderBar .= "<a href='#' onclick='ToggleView(\"Thumbnail\");return false;' title='Thumbnail'><div class='upcp-thumb-toggle-icon " . $Color . "-thumb-icon'></div></a>";
	}
	if (!in_array("List", $ExcludedLayouts)) {
		$HeaderBar .= "<a href='#' onclick='ToggleView(\"List\"); return false;' title='List'><div class='upcp-list-toggle-icon " . $Color . "-list-icon'></div></a>";
	}
	if (!in_array("Detail", $ExcludedLayouts)) {
		$HeaderBar .= "<a href='#' onclick='ToggleView(\"Detail\"); return false;' title='Detail'><div class='upcp-details-toggle-icon " . $Color . "-details-icon'></div></a>";
	}
	$HeaderBar .= "<div class='upcp-clear'></div>";
	$HeaderBar .= "</div>";
	
	$Bottom_JS .= "<script language='JavaScript' type='text/javascript'>";	
	if (isset($_GET['Product_ID'])) {$Bottom_JS .= "jQuery(window).load(OpenProduct('" . $_GET['Product_ID'] . "'));";}
	$Bottom_JS .= "</script>";
		
	if ($only_inner != "Yes") {$InnerString .= "<div class='prod-cat-inner'>";}
	$InnerString .= $ProdThumbString . "<div class='upcp-clear'></div>" . $ProdListString . "<div class='upcp-clear'></div>" . $ProdDetailString . "<div class='upcp-clear'></div>";
	if ($only_inner != "Yes") {$InnerString .= "</div>";}
		
	if ($only_inner == "Yes") {
		$ReturnArray['request_count'] = $request_count;
		$ReturnArray['message'] = $InnerString;
		return json_encode($ReturnArray);
	}
		
	$ReturnString .= "<div class='prod-cat-container'>";
	$ReturnString .= $HeaderBar;
	$ReturnString .= $MobileMenuString;
	$ReturnString .= $InnerString;
	$ReturnString .= $SidebarString;
	$ReturnString .= $Bottom_JS;
	$ReturnString .= "<div class='upcp-clear'></div></div>";
		
	return $ReturnString;
}
add_shortcode("product-catalogue", "Insert_Product_Catalog");

function Insert_Minimal_Products($atts) {
	global $wpdb, $items_table_name, $catalogue_items_table_name;

	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
				"catalogue_url" => "",
				"product_ids" => "",
				"catalogue_id" => "",
				"category_id" => "",
				"subcategory_id" => "",
				"product_count" => 3,
				"products_wide" => 3),
			$atts
		)
	);

	// If there's a product select, return that product
	if ((get_query_var('single_product') != "" or $_GET['SingleProduct'] != "") and $catalogue_url == "") {
		return do_shortcode("[product-catalogue]");
	}

	$ReturnString .= "<div class='upcp-minimal-catalogue upcp-minimal-width-" . $products_wide . "'>";
	if ($product_ids != "") {
		$Product_Array = explode(",", $product_ids);
		foreach ($Product_Array as $Product) {$Products_String .= $Product . ",";}
		$Products_String = substr($Products_String, 0, -1);
		$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Item_ID IN (" . $Products_String . ")");
	}
	elseif ($catalogue_id != "") {
		$Item_IDs = "'999999',";
		$Category_IDs = "'999999',";
		$SubCategory_IDs = "'999999',";
		$Catalogue_Items = $wpdb->get_results("SELECT * FROM $catalogue_items_table_name WHERE Catalogue_ID='" . $catalogue_id . "'");
		foreach ($Catalogue_Items as $Catalogue_Item) {
			if ($Catalogue_Item->Item_ID != 0) {$Item_IDs .= "'" . $Catalogue_Item->Item_ID . "',";}
			elseif ($Catalogue_Item->Category_ID != 0) {$Category_IDs .= "'" . $Catalogue_Item->Category_ID . "',";}
			elseif ($Catalogue_Item->SubCategory_ID != 0) {$SubCategory_IDs = "'" . $Catalogue_Item->SubCategory_ID . "',";}
		}
		$Item_IDs = substr($Item_IDs, 0, -1);
		$Category_IDs = substr($Category_IDs, 0, -1);
		$SubCategory_IDs = substr($SubCategory_IDs, 0, -1);
		$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Item_ID IN (" . $Item_IDs . ") OR Category_ID IN (" . $Category_IDs . ") OR SubCategory_ID IN (" . $SubCategory_IDs . ") ORDER BY rand() LIMIT " . $product_count);
	}
	elseif ($category_id != "") {
		$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE  Category_ID='" . $category_id . "' ORDER BY rand() LIMIT " . $product_count);
	}
	elseif ($subcategory_id != "") {
		$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE  SubCategory_ID='" . $subcategory_id . "' ORDER BY rand() LIMIT " . $product_count);
	}
	else {
		$Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Item_Date_Created!='0000-00-00 00:00:00' ORDER BY Item_Date_Created ASC LIMIT " . $product_count);
	}
	foreach ($Products as $Product) {
		$ReturnString .= "<div class='upcp-insert-product upcp-minimal-product-listing'>";
		$ReturnString .= Build_Minimal_Product_Listing($Product, $catalogue_url);
		$ReturnString .= "</div>";
	}
	
	$ReturnString .= "</div>";

	return $ReturnString;
}
add_shortcode("insert-products", "Insert_Minimal_Products");

/* Function to add the HTML for an individual product to the catalog */
function AddProduct($format, $Item_ID, $Product, $Tags, $AjaxReload = "No", $AjaxURL = "") {
	// Add the required global variables
	global $wpdb, $categories_table_name, $subcategories_table_name, $tags_table_name, $tagged_items_table_name, $catalogues_table_name, $catalogue_items_table_name, $items_table_name, $item_images_table_name;
	global $ProdCats, $ProdSubCats, $ProdTags, $ProdCustomFields, $ReturnString;
		
	$ReadMore = get_option("UPCP_Read_More");
	$Links = get_option("UPCP_Product_Links");
	$Pretty_Links = get_option("UPCP_Pretty_Links");
	$Detail_Desc_Chars = get_option("UPCP_Desc_Chars");
	$CF_Conversion = get_option("UPCP_CF_Conversion");

	$Details_Label = get_option("UPCP_Details_Label");
	if ($Details_Label != "") {$Details_Text = $Details_Label;}
	else {$Details_Text = __("Details", 'UPCP');}

	if ($Links == "New") {$NewWindow = true;}
	else {$NewWindow = false;}
		
	if ($CF_Conversion != "No") {$Description = ConvertCustomFields($Product->Item_Description);}
	else {$Description = $Product->Item_Description;}
	$Description = str_replace("[upcp-price]", $Product->Item_Price, $Description);
		
	//Select the product info, tags and images for the product
	$Item_Images = $wpdb->get_results("SELECT Item_Image_URL, Item_Image_ID FROM $item_images_table_name WHERE Item_ID=" . $Item_ID);
	$TagsString = "";
		
	if ($Product->Item_Photo_URL != "" and strlen($Product->Item_Photo_URL) > 7 and substr($Product->Item_Photo_URL, 0, 7) != "http://" and substr($Product->Item_Photo_URL, 0, 8) != "https://") {
		$PhotoCode = $Product->Item_Photo_URL;
		$PhotoCode = do_shortcode($PhotoCode);
	}
	elseif ($Product->Item_Photo_URL != "" and strlen($Product->Item_Photo_URL) > 7) {
		$PhotoURL = htmlspecialchars($Product->Item_Photo_URL, ENT_QUOTES);
		$PhotoCode = "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-thumb-" . $Product->Item_ID . "' class='prod-cat-thumb-image upcp-thumb-image'>";
	}
	else {
		$PhotoURL = plugins_url('ultimate-product-catalogue/images/No-Photo-Available.jpg');
		$PhotoCode = "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-thumb-" . $Product->Item_ID . "' class='prod-cat-thumb-image upcp-thumb-image'>";
	}
		
	//Create the tag string for filtering
	foreach ($Tags as $Tag) {$TagsString .= $Tag->Tag_ID . ", ";}
	$TagsString = trim($TagsString, " ,");
		
	// Check whether the FancyBox for WordPress plugin is activated
	$plugin = "fancybox-for-wordpress/fancybox.php";
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$FancyBox_Installed = is_plugin_active($plugin);
		
	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	$FB_Perm_URL = $uri_parts[0] . "?" . $uri_parts[1];
	if ($uri_parts[1] == "") {$FB_Perm_URL .= "Product_ID=" . $Product->Item_ID;}
	else {$FB_Perm_URL .= "&Product_ID=" . $Product->Item_ID;}
		
	if ($AjaxReload == "Yes") {$Base = $AjaxURL;}
	else {$Base = $uri_parts[0];}
		
	if ($Product->Item_Link != "") {$ItemLink = $Product->Item_Link;}
	elseif ($FancyBox_Installed) {$ItemLink = "#prod-cat-addt-details-" . $Product->Item_ID; $FancyBoxClass = true;}
	elseif ($Pretty_Links == "Yes") {$ItemLink = $Base . "product/" . $Product->Item_Slug . "/?" . $uri_parts[1];}
	else {$ItemLink = $Base . "?" . $uri_parts[1] . "&SingleProduct=" . $Product->Item_ID;}

	//Create the listing for the thumbnail layout display
	if ($format == "Thumbnail") {				
		$ProductString .= "<div id='prod-cat-item-" . $Product->Item_ID . "' class='prod-cat-item upcp-thumb-item'>\n";
		$ProductString .= "<div id='prod-cat-thumb-div-" . $Product->Item_ID . "' class='prod-cat-thumb-image-div upcp-thumb-image-div'>";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= $PhotoCode;
		$ProductString .= "</a>";	
		$ProductString .= "</div>\n";
		$ProductString .= "<div id='prod-cat-title-" . $Product->Item_ID . "' class='prod-cat-title upcp-thumb-title'>";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= " no-underline'";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>" . $Product->Item_Name . "</a>";
		$ProductString .= AddCustomFields($Product->Item_ID, "thumbs");
		$ProductString .= "</div>\n";
		$ProductString .= "<div id='prod-cat-price-" . $Product->Item_ID . "' class='prod-cat-price upcp-thumb-price'>" . $Product->Item_Price . "</div>\n";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= "<div id='prod-cat-details-link-" . $Product->Item_ID . "' class='prod-cat-details-link upcp-thumb-details-link'>" . $Details_Text . "</div>\n";
		$ProductString .= "</a>";
	}
	//Create the listing for the list layout display
	if ($format == "List") {				
		$ProductString .= "<div id='prod-cat-item-" . $Product->Item_ID . "' class='prod-cat-item upcp-list-item'>\n";
		$ProductString .= "<div id='prod-cat-title-" . $Product->Item_ID . "' class='prod-cat-title upcp-list-title' onclick='ToggleItem(" . $Product->Item_ID . ");'>" . $Product->Item_Name . "</div>\n";
		$ProductString .= "<div id='prod-cat-price-" . $Product->Item_ID . "' class='prod-cat-price upcp-list-price' onclick='ToggleItem(" . $Product->Item_ID . ");'>" . $Product->Item_Price . "</div>\n";
		$ProductString .= "<div id='prod-cat-details-" . $Product->Item_ID . "' class='prod-cat-details upcp-list-details hidden-field'>\n";
		$ProductString .= "<div id='prod-cat-thumb-div-" . $Product->Item_ID . "' class='prod-cat-thumb-image-div upcp-list-image-div'>";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= $PhotoCode;
		$ProductString .= "</a>";
		$ProductString .= "</div>\n";
		$ProductString .= "<div id='prod-cat-desc-" . $Product->Item_ID . "' class='prod-cat-desc upcp-list-desc'>" . $Description . "</div>\n";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= "<div id='prod-cat-details-link-" . $Product->Item_ID . "' class='prod-cat-details-link upcp-list-details-link'>" . __("Images", 'UPCP') . "</div>\n";
		$ProductString .= "</a>";
		$ProductString .= "</div>";
	}
	//Create the listing for the detail layout display
	if ($format == "Detail") {				
		$ProductString .= "<div id='prod-cat-item-" . $Product->Item_ID . "' class='prod-cat-item upcp-detail-item'>\n";
		$ProductString .= "<div id='prod-cat-detail-div-" . $Product->Item_ID . "' class='prod-cat-detail-image-div upcp-detail-image-div'>";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= $PhotoCode;
		$ProductString .= "</a>";	
		$ProductString .= "</div>\n";
		$ProductString .= "<div id='prod-cat-mid-div-" . $Product->Item_ID . "' class='prod-cat-mid-detail-div upcp-mid-detail-div'>";
		$ProductString .= "<div id='prod-cat-title-" . $Product->Item_ID . "' class='prod-cat-title upcp-detail-title'>" . $Product->Item_Name . "</div>\n";
		if ($ReadMore == "Yes") {$ProductString .= "<div id='prod-cat-desc-" . $Product->Item_ID . "' class='prod-cat-desc upcp-detail-desc'>" . strip_tags(substr($Description, 0, $Detail_Desc_Chars));}
		else {$ProductString .= "<div id='prod-cat-desc-" . $Product->Item_ID . "' class='prod-cat-desc upcp-detail-desc'>" . strip_tags($Description);}
		if ($ReadMore == "Yes") {
			if (strlen($Description) > $Detail_Desc_Chars) {
				$ProductString .= "... <a class='upcp-catalogue-link ";
				if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
				$ProductString .= "' ";
				if ($NewWindow) {$ProductString .= "target='_blank'";}
				$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>" . __("Read More", 'UPCP') . "</a>";
			}
		}
		$ProductString .= AddCustomFields($Product->Item_ID, "details");
		$ProductString .= "</div>\n";
		$ProductString .= "</div>";
		$ProductString .= "<div id='prod-cat-end-div-" . $Product->Item_ID . "' class='prod-cat-end-detail-div upcp-end-detail-div'>";
		$ProductString .= "<div id='prod-cat-price-" . $Product->Item_ID . "' class='prod-cat-price upcp-detail-price'>" . $Product->Item_Price . "</div>\n";
		$ProductString .= "<a class='upcp-catalogue-link ";
		if ($FancyBoxClass and !$NewWindow) {$ProductString .= "fancybox";}
		$ProductString .= "' ";
		if ($NewWindow) {$ProductString .= "target='_blank'";}
		$ProductString .= " href='" . $ItemLink . "' onclick='RecordView(" . $Product->Item_ID . ");'>";
		$ProductString .= "<div id='prod-cat-details-link-" . $Product->Item_ID . "' class='prod-cat-details-link upcp-detail-details-link'>" . $Details_Text . "</div>\n";
		$ProductString .= "</a>";
		$ProductString .= "</div>";
	}
		
	if ($FancyBox_Installed) {
		$ProductString .= "<div style='display:none;' id='upcp-fb-" . $Product->Item_ID . "'>";
		$ProductString .= "<div id='prod-cat-addt-details-" . $Product->Item_ID . "' class='prod-cat-addt-details'>";
		$ProductString .= "<div id='prod-cat-addt-details-thumbs-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-thumbs-div'>";
		$ProductString .= "<img src='" . $PhotoURL . "' id='prod-cat-addt-details-thumb-P". $Product->Item_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"0\");'>";
		foreach ($Item_Images as $Image) {$ProductString .= "<img src='" . htmlspecialchars($Image->Item_Image_URL, ENT_QUOTES) . "' id='prod-cat-addt-details-thumb-". $Image->Item_Image_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"" . $Image->Item_Image_ID . "\");'>";}
		$ProductString .= "</div>";
		$ProductString .= "<div id='prod-cat-addt-details-right-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-right-div'>";
		$ProductString .= "<h2 class='prod-cat-addt-details-title'><a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $FB_Perm_URL . "'>" . $Product->Item_Name . "<img class='upcp-product-url-icon' src='" . get_bloginfo('wpurl') . "/wp-content/plugins/ultimate-product-catalogue/images/insert_link.png' /></a></h2>";
		$ProductString .= "<div id='prod-cat-addt-details-main-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-main-div'>";
		$ProductString .= "<a class='upcp-no-pointer' onclick='return false'>";
		$ProductString .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-" . $Product->Item_ID . "' class='prod-cat-addt-details-main'>";
		$ProductString .= "</a>";
		$ProductString .= "</div>";
		$ProductString .= "<div class='upcp-clear'></div>";
		$ProductString .= "<div id='prod-cat-addt-details-desc-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-desc-div'>";
		$ProductString .= $Description; 
		$ProductString .= AddCustomFields($Product->Item_ID, "details");
		$ProductString .= "</div>";
		$ProductString .= "</div></div></div>";
		//$ProductString .= "</div>";
	}
		
	// Add hidden fields with the category, sub-category and tag ID's for each product		
	$ProductString .= "<div id='prod-cat-category-jquery-" . $Product->Item_ID . "' class='prod-cat-category-jquery jquery-hidden'> " . $Product->Category_ID . ",</div>\n";
	$ProductString .= "<div id='prod-cat-subcategory-jquery-" . $Product->Item_ID . "' class='prod-cat-subcategory-jquery jquery-hidden'> " . $Product->SubCategory_ID . ",</div>\n";
	$ProductString .= "<div id='prod-cat-tag-jquery-" . $Product->Item_ID . "' class='prod-cat-tag-jquery jquery-hidden'> " . $TagsString . ",</div>\n";
	$ProductString .= "<div id='prod-cat-title-jquery-" . $Product->Item_ID . "' class='prod-cat-title-jquery jquery-hidden'> " . $Product->Item_Name . ",</div>\n";
	$ProductString .= "<div class='upcp-clear'></div>\n";
	$ProductString .= "</div>\n";
				
	return $ProductString;
}

function SingleProductPage() {
	global $wpdb, $items_table_name, $item_images_table_name, $fields_table_name, $fields_meta_table_name, $tagged_items_table_name, $tags_table_name;
		
	$Pretty_Links = get_option("UPCP_Pretty_Links");
	$Filter_Title = get_option("UPCP_Filter_Title");
	$Single_Page_Price = get_option("UPCP_Single_Page_Price");
	$Custom_Product_Page = get_option("UPCP_Custom_Product_Page");
	$Related_Type = get_option("UPCP_Related_Products");
	$Next_Previous = get_option("UPCP_Next_Previous");
	$Product_Page_Serialized = get_option("UPCP_Product_Page_Serialized");
	$Mobile_Product_Page_Serialized = get_option("UPCP_Product_Page_Serialized_Mobile");
	$PP_Grid_Width = get_option("UPCP_PP_Grid_Width");
	$PP_Grid_Height = get_option("UPCP_PP_Grid_Height");
	$Top_Bottom_Padding = get_option("UPCP_Top_Bottom_Padding");
	$Left_Right_Padding = get_option("UPCP_Left_Right_Padding");
	$CF_Conversion = get_option("UPCP_CF_Conversion");

	$Back_To_Catalogue_Label = get_option("UPCP_Back_To_Catalogue_Label");
	if ($Back_To_Catalogue_Label != "") {$Back_To_Catalogue_Text = $Back_To_Catalogue_Label;}
	else {$Back_To_Catalogue_Text = __("Back to Catalogue", 'UPCP');}
		
	if ($Pretty_Links == "Yes") {$Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_Slug='" . trim(get_query_var('single_product'), "/? ") . "'");}
	else {$Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_ID='" . $_GET['SingleProduct'] . "'");}
	$Item_Images = $wpdb->get_results("SELECT Item_Image_URL, Item_Image_ID FROM $item_images_table_name WHERE Item_ID=" . $Product->Item_ID);
		
	$Links = get_option("UPCP_Product_Links");
	if ($CF_Conversion != "No") {$Description = ConvertCustomFields($Product->Item_Description);}
	else {$Description = $Product->Item_Description;}
	$Description = str_replace("[upcp-price]", $Product->Item_Price, $Description);
	$Description = do_shortcode($Description);
		
	//Edit the title if that option has been selected
	if ($Filter_Title == "Yes") {
		add_action( 'init', 'UPCP_Filter_Title', 20, $Product->Item_Name);
	}
		
	//Create the tag string for filtering
	$Tags = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID=" . $Product->Item_ID);
	if (is_array($Tags)) {
		foreach ($Tags as $Tag) {
			$TagInfo = $wpdb->get_row("SELECT Tag_Name FROM $tags_table_name WHERE Tag_ID=" . $Tag->Tag_ID);
			$TagsString .= $TagInfo->Tag_Name . ", ";
		}
	}
	$TagsString = trim($TagsString, " ,");
		
	if ($Product->Item_Photo_URL != "" and strlen($Product->Item_Photo_URL) > 7 and substr($Product->Item_Photo_URL, 0, 7) != "http://" and substr($Product->Item_Photo_URL, 0, 8) != "https://") {
		$PhotoCode = $Product->Item_Photo_URL;
		$PhotoCode = do_shortcode($PhotoCode);
	}
	elseif ($Product->Item_Photo_URL != "" and strlen($Product->Item_Photo_URL) > 7) {
		$PhotoURL = htmlspecialchars($Product->Item_Photo_URL, ENT_QUOTES);
		$PhotoCode .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-" . $Product->Item_ID . "' class='prod-cat-addt-details-main'>";
		$PhotoCodeMobile .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-mobile-" . $Product->Item_ID . "' class='prod-cat-addt-details-main'>";
	}
	else {
		$PhotoURL = plugins_url('ultimate-product-catalogue/images/No-Photo-Available.jpg');
		$PhotoCode .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-" . $Product->Item_ID . "' class='prod-cat-addt-details-main'>";
		$PhotoCodeMobile .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-mobile-" . $Product->Item_ID . "' class='prod-cat-addt-details-main'>";
	}
		
	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	$SP_Perm_URL = $uri_parts[0] . "?" . $uri_parts[1];
	$Return_URL = $uri_parts[0];
	if ($Pretty_Links == "Yes") {$Return_URL = substr($uri_parts[0], 0, strrpos($uri_parts[0], "/", -2)-8) . "/?" . $uri_parts[1];}
	elseif (strpos($uri_parts[1],  "page_id") !== false) {$Return_URL .= "?" . substr($uri_parts[1], 0, strpos($uri_parts[1], "&"));}

	if ($uri_parts[1] == "") {$SP_Perm_URL .= "Product_ID=" . $Product->Item_ID;}
	else {$SP_Perm_URL .= "&Product_ID=" . $Product->Item_ID;}
		
	if ($Custom_Product_Page == "No") {
		$ProductString .= "<div class='upcp-standard-product-page'>";
				
		$ProductString .= "<div class='prod-cat-back-link'>";
		$ProductString .= "<a class='upcp-catalogue-link' href='" . $Return_URL . "'>&#171; " . $Back_To_Catalogue_Text . "</a>";
		$ProductString .= "</div>";
		
		$ProductString .= "<div id='prod-cat-addt-details-" . $Product->Item_ID . "' class='prod-cat-addt-details'>";
		$ProductString .= "<div id='prod-cat-addt-details-thumbs-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-thumbs-div'>";
		if (isset($PhotoURL)) {$ProductString .= "<img src='" . $PhotoURL . "' id='prod-cat-addt-details-thumb-P". $Product->Item_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"0\");'>";}
		foreach ($Item_Images as $Image) {$ProductString .= "<img src='" . htmlspecialchars($Image->Item_Image_URL, ENT_QUOTES) . "' id='prod-cat-addt-details-thumb-". $Image->Item_Image_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"" . $Image->Item_Image_ID . "\");'>";}
		$ProductString .= "</div>";
		$ProductString .= "<div id='prod-cat-addt-details-right-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-right-div'>";
		$ProductString .= "<h2 class='prod-cat-addt-details-title'><a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $SP_Perm_URL . "'>" . $Product->Item_Name . "<img class='upcp-product-url-icon' src='" . get_bloginfo('wpurl') . "/wp-content/plugins/ultimate-product-catalogue/images/insert_link.png' /></a></h2>";
		if ($Single_Page_Price == "Yes") {$ProductString .= "<h3 class='prod-cat-addt-details-price'>" . $Product->Item_Price . "</h3>";}
		$ProductString .= "<div id='prod-cat-addt-details-main-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-main-div'>";
		$ProductString .= $PhotoCode;
		$ProductString .= "</div>";
		$ProductString .= "<div class='upcp-clear'></div>";
		$ProductString .= "<div id='prod-cat-addt-details-desc-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-desc-div'>";
		$ProductString .= $Description . "</div>";
		$ProductString .= "<div class='upcp-clear'></div>\n";
		if ($Related_Type == "Manual" or $Related_Type == "Auto") {$ProductString .= Get_Related_Products($Product, $Related_Type);}
		if ($Next_Previous == "Manual") {$ProductString .= Get_Next_Previous($Product, $Next_Previous);}
		$ProductString .= "</div>\n";
		$ProductString .= "</div>\n";
		$ProductString .= "</div>\n";
				
		$ProductString .= "</div>\n";
				
		$ProductString .= "<div class='upcp-standard-product-page-mobile'>";
				
		$ProductString .= "<div class='prod-cat-back-link'>";
		$ProductString .= "<a class='upcp-catalogue-link' href='" . $Return_URL . "'>&#171; " . $Back_To_Catalogue_Text . "</a>";
		$ProductString .= "</div>";
				
		$ProductString .= "<h2 class='prod-cat-addt-details-title'><a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $SP_Perm_URL . "'>" . $Product->Item_Name . "<img class='upcp-product-url-icon' src='" . get_bloginfo('wpurl') . "/wp-content/plugins/ultimate-product-catalogue/images/insert_link.png' /></a></h2>";
		if ($Single_Page_Price == "Yes") {$ProductString .= "<h3 class='prod-cat-addt-details-price'>" . $Product->Item_Price . "</h3>";}
		$ProductString .= $PhotoCodeMobile;
		$ProductString .= "<div class='upcp-clear'></div>";
		
		$ProductString .= "<div id='prod-cat-addt-details-" . $Product->Item_ID . "' class='prod-cat-addt-details'>";
		$ProductString .= "<div id='prod-cat-addt-details-thumbs-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-thumbs-div'>";
		if (isset($PhotoURL)) {$ProductString .= "<img src='" . $PhotoURL . "' id='prod-cat-addt-details-thumb-P". $Product->Item_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"0\");'>";}
		foreach ($Item_Images as $Image) {$ProductString .= "<img src='" . htmlspecialchars($Image->Item_Image_URL, ENT_QUOTES) . "' id='prod-cat-addt-details-thumb-". $Image->Item_Image_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"" . $Image->Item_Image_ID . "\");'>";}
		$ProductString .= "<div class='upcp-clear'></div>";
		$ProductString .= "</div>";

		$ProductString .= "<div id='prod-cat-addt-details-desc-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-desc-div'>";
		$ProductString .= $Description . "</div>";
		$ProductString .= "<div class='upcp-clear'></div>\n";
		$ProductString .= "</div>\n";
				
		$ProductString .= "</div>\n";
	}
	else {
		if ($Custom_Product_Page == "Large" or $Mobile_Product_Page_Serialized != "") {$ProductString .= "<div class='upcp-custom-large-product-page'>";}
				
		echo "<script language='JavaScript' type='text/javascript'>";
		echo "var pp_grid_width = " . $PP_Grid_Width . ";";
		echo "var pp_grid_height = " . $PP_Grid_Height . ";";
		echo "var pp_top_bottom_padding = " . $Top_Bottom_Padding . ";";
		echo "var pp_left_right_padding = " . $Left_Right_Padding . ";";
		echo "</script>";
				
		$Gridster = json_decode(stripslashes($Product_Page_Serialized));
		$ProductString .= "<div class='gridster'>";
		$ProductString .= "<ul>";
		$ProductString .= BuildGridster($Gridster, $Product, $Item_Images, $Description, $PhotoURL, $SP_Perm_URL, $Return_URL, $TagsString);
		$ProductString .= "</ul>";
		$ProductString .= "</div>";
		
		if ($Custom_Product_Page == "Large") {
			$ProductString .= "</div>";
						
			$ProductString .= "<div class='upcp-standard-product-page-mobile'>";
				
			$ProductString .= "<div class='prod-cat-back-link'>";
			$ProductString .= "<a class='upcp-catalogue-link' href='" . $Return_URL . "'>&#171; " . $Back_To_Catalogue_Text . "</a>";
			$ProductString .= "</div>";
				
			$ProductString .= "<h2 class='prod-cat-addt-details-title'><a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $SP_Perm_URL . "'>" . $Product->Item_Name . "<img class='upcp-product-url-icon' src='" . get_bloginfo('wpurl') . "/wp-content/plugins/ultimate-product-catalogue/images/insert_link.png' /></a></h2>";
			if ($Single_Page_Price == "Yes") {$ProductString .= "<h3 class='prod-cat-addt-details-price'>" . $Product->Item_Price . "</h3>";}
			$ProductString .= $PhotoCodeMobile;
			$ProductString .= "<div class='upcp-clear'></div>";
		
			$ProductString .= "<div id='prod-cat-addt-details-" . $Product->Item_ID . "' class='prod-cat-addt-details'>";
			$ProductString .= "<div id='prod-cat-addt-details-thumbs-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-thumbs-div'>";
			if (isset($PhotoURL)) {$ProductString .= "<img src='" . $PhotoURL . "' id='prod-cat-addt-details-thumb-P1-". $Product->Item_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"0\");'>";}
			foreach ($Item_Images as $Image) {$ProductString .= "<img src='" . htmlspecialchars($Image->Item_Image_URL, ENT_QUOTES) . "' id='prod-cat-addt-details-thumb-". $Image->Item_Image_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"" . $Image->Item_Image_ID . "\");'>";}
			$ProductString .= "<div class='upcp-clear'></div>";
			$ProductString .= "</div>";

			$ProductString .= "<div id='prod-cat-addt-details-desc-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-desc-div'>";
			$ProductString .= $Description . "</div>";
			$ProductString .= "<div class='upcp-clear'></div>\n";
			if ($Related_Type == "Manual" or $Related_Type == "Auto") {$ProductString .= Get_Related_Products($Product, $Related_Type);}
			if ($Next_Previous == "Manual") {$ProductString .= Get_Next_Previous($Product, $Next_Previous);}
			$ProductString .= "</div>\n";
				
			$ProductString .= "</div>\n";
		}
		elseif ($Mobile_Product_Page_Serialized != "") {
			$ProductString .= "</div>";
						
			$ProductString .= "<div class='upcp-standard-product-page-mobile'>";
						
			$Gridster = json_decode(stripslashes($Mobile_Product_Page_Serialized));
			$ProductString .= "<div class='gridster-mobile'>";
			$ProductString .= "<ul>";
			$ProductString .= BuildGridster($Gridster, $Product, $Item_Images, $Description, $PhotoURL, $SP_Perm_URL, $Return_URL, $TagsString);
			$ProductString .= "</ul>";
			$ProductString .= "</div>";
						
			$ProductString .= "</div>\n";
		}
	}
		
	return $ProductString;
}

function BuildSidebar($category, $subcategory, $tags, $prod_name) {
	global $wpdb, $Full_Version, $ProdCats, $ProdSubCats, $ProdTags, $ProdCustomFields, $ProdCatString, $ProdSubCatString, $ProdTagString, $ProdCustomFieldsString;
	global $categories_table_name, $subcategories_table_name, $tags_table_name, $fields_table_name;
		
	$Color = get_option("UPCP_Color_Scheme");
	$Tag_Logic = get_option("UPCP_Tag_Logic");
	$ProductSearch = get_option("UPCP_Product_Search");
	$Product_Sort = get_option("UPCP_Product_Sort");
	$Sidebar_Order = get_option("UPCP_Sidebar_Order");

	$Categories_Label = get_option("UPCP_Categories_Label");
	$SubCategories_Label = get_option("UPCP_SubCategories_Label");
	$Tags_Label = get_option("UPCP_Tags_Label");
	$Custom_Fields_Label = get_option("UPCP_Custom_Fields_Label");
	$Sort_By_Label = get_option("UPCP_Sort_By_Label");
	$Product_Name_Search_Label = get_option("UPCP_Product_Name_Search_Label");
	$Product_Search_Text_Label = get_option("UPCP_Product_Name_Text_Label");

	if ($Categories_Label != "") {$Categories_Text = $Categories_Label;}
	else {$Categories_Text = __("Categories:", 'UPCP');}
	if ($SubCategories_Label != "") {$SubCategories_Text = $SubCategories_Label;}
	else {$SubCategories_Text = __("Sub-Categories:", 'UPCP');}
	if ($Tags_Label != "") {$Tags_Text = $Tags_Label;}
	else {$Tags_Text = __("Tags:", 'UPCP');}
	if ($Custom_Fields_Label != "") {$Custom_Field_Text = $Custom_Fields_Label;}
	else {$Custom_Field_Text = __("Additional Options:", 'UPCP');}
	if ($Sort_By_Label != "") {$Sort_Text = $Sort_By_Label;}
	else {$Sort_Text = __('Sort By:', 'UPCP');}
	if ($Product_Name_Search_Label != "") {$SearchLabel = $Product_Name_Search_Label;}
	else {
		if ($ProductSearch == "namedesc" or $ProductSearch == "namedesccust") {$SearchLabel = __("Product Search:", 'UPCP');}
		else {$SearchLabel = __("Product Name:", 'UPCP');}
	}
	if ($prod_name != "") {$Product_Name_Text = $prod_name;}
	elseif ($Product_Search_Text_Label != "") {$Product_Name_Text = $Product_Search_Text_Label;
	}
	else {
		if ($ProductSearch == "namedesc" or $ProductSearch == "namedesccust") {$Product_Name_Text = __("Search...", 'UPCP');}
		else {$Product_Name_Text = __("Name...", 'UPCP');}
	}
	
	// Get the categories, sub-categories and tags that apply to the products in the catalog
	if ($ProdCatString != "") {$Categories = $wpdb->get_results("SELECT Category_ID, Category_Name FROM $categories_table_name WHERE Category_ID IN (" . $ProdCatString . ") ORDER BY Category_Name");}
	if ($ProdSubCatString != "") {$SubCategories = $wpdb->get_results("SELECT SubCategory_ID, SubCategory_Name, Category_ID FROM $subcategories_table_name WHERE SubCategory_ID IN (" . $ProdSubCatString . ") ORDER BY SubCategory_Name");}
	if ($ProdTagString != "") {$Tags = $wpdb->get_results("SELECT Tag_ID, Tag_Name FROM $tags_table_name WHERE Tag_ID IN (" . $ProdTagString . ") ORDER BY Tag_Date_Created");}
	else {$Tags = array();}
	if ($ProdCustomFieldsString != "") {$Custom_Fields = $wpdb->get_results("SELECT Field_ID, Field_Name FROM $fields_table_name WHERE Field_ID IN (" . $ProdCustomFieldsString . ") AND Field_Searchable='Yes' ORDER BY Field_Name");}
	else {$Custom_Fields = array();}

	$SidebarString .= "<div id='prod-cat-sidebar-" . $id . "' class='prod-cat-sidebar'>\n";
	//$SidebarString .= "<form action='#' name='Product_Catalog_Sidebar_Form'>\n";
	$SidebarString .= "<form onsubmit='return false;' name='Product_Catalog_Sidebar_Form'>\n";
				
	//Create the 'Sort By' select box
	if ($Full_Version == "Yes" and $Product_Sort != "None") {
		$SidebarString .= "<div id='prod-cat-sort-by' class='prod-cat-sort-by'>";
		$SidebarString .= $Sort_Text . "<br>";
		$SidebarString .= "<div class='styled-select styled-input'>";
		$SidebarString .= "<select name='upcp-sort-by' id='upcp-sort-by' onchange='UPCP_Sort_By();'>";
		$SidebarString .= "<option value=''></option>";
		if ($Product_Sort == "Price" or $Product_Sort == "Price_Name") {
			$SidebarString .= "<option value='price_asc'>" . __('Price (Ascending)',  'UPCP') . "</option>";
			$SidebarString .= "<option value='price_desc'>" . __('Price (Descending)',  'UPCP') . "</option>";
		}
		if ($Product_Sort == "Name" or $Product_Sort == "Price_Name") {
			$SidebarString .= "<option value='name_asc'>" . __('Name (Ascending)',  'UPCP') . "</option>";
			$SidebarString .= "<option value='name_desc'>" . __('Name (Descending)',  'UPCP') . "</option>";
		}
		$SidebarString .= "</select>";
		$SidebarString .= "</div>";
		$SidebarString .= "</div>";
	}
				
	// Create the text search box
	$SidebarString .= "<div id='prod-cat-text-search' class='prod-cat-text-search'>\n";
	$SidebarString .= $SearchLabel . "<br /><div class='styled-input'>";
	if ($Filter  == "Javascript" and $Tag_Logic == "OR") {$SidebarString .= "<input type='text' id='upcp-name-search' class='jquery-prod-name-text' name='Text_Search' value='" . $Product_Name_Text . "' onfocus='FieldFocus(this);' onblur='FieldBlur(this);' onkeyup='UPCP_Filer_Results_OR();'>\n";}
	elseif ($Filter  == "Javascript") {$SidebarString .= "<input type='text' id='upcp-name-search' class='jquery-prod-name-text' name='Text_Search' value='" . $Product_Name_Text . "' onfocus='FieldFocus(this);' onblur='FieldBlur(this);' onkeyup='UPCP_Filer_Results();'>\n";}
	else {$SidebarString .= "<input type='text' id='upcp-name-search' class='jquery-prod-name-text' name='Text_Search' value='" . $Product_Name_Text . "' onfocus='FieldFocus(this);' onblur='FieldBlur(this);' onkeyup='UPCP_DisplayPage(\"1\");'>\n";}
	$SidebarString .= "</div></div>\n";
				
	// Create the categories checkboxes
	if (sizeof($Categories) > 0) {
		foreach ($Categories as $key => $row) {
    		$ID[$key]  = $row->Category_ID;
    		$Name[$key] = $row->Category_Name;
		}
		array_multisort($Name, SORT_ASC, $ID, SORT_DESC, $Categories);
		unset($ID);
		unset($Name);
		$SidebarString .= "<div id='prod-cat-sidebar-category-div-" . $id . "' class='prod-cat-sidebar-category-div'>\n";
		$SidebarString .= "<div id='prod-cat-sidebar-category-title-" . $id . "' class='prod-cat-sidebar-category-title'><h3>" . $Categories_Text . "</h3></div>\n";
		foreach ($Categories as $Category) {
			$SidebarString .= "<div id='prod-cat-sidebar-category-" . $Category->Category_ID . "' class='prod-cat-sidebar-category";
			if (in_array($Category->Category_ID, $category)) {$SidebarString .= " highlightBlue";}
			$SidebarString .= "'>\n";
			if ($Filter  == "Javascript" and $Tag_Logic == "OR") {$SidebarString .= "<input type='checkbox' class='jquery-prod-cat-value' name='Category" . $Category->Category_ID . "' value='" . $Category->Category_ID . "' onclick='UPCP_Filer_Results_OR(); UPCPHighlight(this, \"" . $Color . "\");'>" . $Category->Category_Name . " (" . $ProdCats[$Category->Category_ID] . ")\n";}
			elseif ($Filter  == "Javascript") {$SidebarString .= "<input type='checkbox' class='jquery-prod-cat-value' name='Category" . $Category->Category_ID . "' value='" . $Category->Category_ID . "' onclick='UPCP_Filer_Results(); UPCPHighlight(this, \"" . $Color . "\");'>" . $Category->Category_Name . " (" . $ProdCats[$Category->Category_ID] . ")\n";}
			else {
				$SidebarString .= "<input type='checkbox' name='Category" . $Category->Category_ID . "' value='" . $Category->Category_ID . "' onclick='UPCP_DisplayPage(\"1\"); UPCPHighlight(this, \"" . $Color . "\");' class='jquery-prod-cat-value'";
				if (in_array($Category->Category_ID, $category)) {$SidebarString .= "checked=checked";}
				$SidebarString .= "> " . $Category->Category_Name . " (" . $ProdCats[$Category->Category_ID] . ")\n";
			}
			$SidebarString .= "</div>\n";
						
			if ($Sidebar_Order == "Hierarchical") {
				foreach ($SubCategories as $SubCategory) {
					if ($SubCategory->Category_ID == $Category->Category_ID) {
						$SidebarString .= "<div id='prod-cat-sidebar-subcategory-" . $SubCategory->SubCategory_ID . "' class='prod-cat-sidebar-subcategory upcp-margin-left-6 upcp-margin-top-minus-2";
						if (in_array($SubCategory->SubCategory_ID, $subcategory)) {$SidebarString .= " highlightBlue";}
						$SidebarString .= "'>\n";
						if ($Filter  == "Javascript" and $Tag_Logic == "OR") {$SidebarString .= "<input type='checkbox' class='jquery-prod-sub-cat-value' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_Filer_Results_OR(); UPCPHighlight(this, \"" . $Color . "\");'> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";}
						elseif ($Filter  == "Javascript") {$SidebarString .= "<input type='checkbox' class='jquery-prod-sub-cat-value' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_Filer_Results(); UPCPHighlight(this, \"" . $Color . "\");'> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";}
						else {
							$SidebarString .= "<input type='checkbox' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_DisplayPage(\"1\"); UPCPHighlight(this, \"" . $Color . "\");' class='jquery-prod-sub-cat-value'";
							if (in_array($SubCategory->SubCategory_ID, $subcategory)) {$SidebarString .= "checked=checked";}
							$SidebarString .= "> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";
						}
						$SidebarString .= "</div>\n";
					}
				}
			}
		}
		$SidebarString .= "</div>\n";
	}
				
	// Create the sub-categories checkboxes
	if (sizeof($SubCategories) > 0 and $Sidebar_Order != "Hierarchical") {
		foreach ($SubCategories as $key => $row) {
    		$ID[$key]  = $row->SubCategory_ID;
    		$Name[$key] = $row->SubCategory_Name;
		}
		array_multisort($Name, SORT_ASC, $ID, SORT_DESC, $SubCategories);
		unset($ID);
		unset($Name);
		$SidebarString .= "<div id='prod-cat-sidebar-subcategory-div-" . $id . "' class='prod-cat-sidebar-subcategory-div'>\n";
		$SidebarString .= "<div id='prod-cat-sidebar-subcategory-title-" . $id . "' class='prod-cat-sidebar-subcategory-title'><h3>" . $SubCategories_Text . "</h3></div>\n";
		foreach ($SubCategories as $SubCategory) {
			$SidebarString .= "<div id='prod-cat-sidebar-subcategory-" . $SubCategory->SubCategory_ID . "' class='prod-cat-sidebar-subcategory";
			if (in_array($SubCategory->SubCategory_ID, $subcategory)) {$SidebarString .= " highlightBlue";}
			$SidebarString .= "'>\n";
			if ($Filter  == "Javascript" and $Tag_Logic == "OR") {$SidebarString .= "<input type='checkbox' class='jquery-prod-sub-cat-value' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_Filer_Results_OR(); UPCPHighlight(this, \"" . $Color . "\");'> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";}
			elseif ($Filter  == "Javascript") {$SidebarString .= "<input type='checkbox' class='jquery-prod-sub-cat-value' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_Filer_Results(); UPCPHighlight(this, \"" . $Color . "\");'> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";}
			else {
				$SidebarString .= "<input type='checkbox' name='SubCategory[]' value='" . $SubCategory->SubCategory_ID . "'  onclick='UPCP_DisplayPage(\"1\"); UPCPHighlight(this, \"" . $Color . "\");' class='jquery-prod-sub-cat-value'";
				if (in_array($SubCategory->SubCategory_ID, $subcategory)) {$SidebarString .= "checked=checked";}
				$SidebarString .= "> " . $SubCategory->SubCategory_Name . " (" . $ProdSubCats[$SubCategory->SubCategory_ID] . ")\n";
			}
			$SidebarString .= "</div>\n";
		}
		$SidebarString .= "</div>\n";
	}
				
	// Create the tags checkboxes
	if (sizeof($Tags) > 0) {
		foreach ($Tags as $key => $row) {
    		$ID[$key]  = $row->Tag_ID;
    		$Name[$key] = $row->Tag_Name;
		}
		array_multisort($Name, SORT_ASC, $ID, SORT_DESC, $Tags);
		unset($ID);
		unset($Name);
		$SidebarString .= "<div id='prod-cat-sidebar-tag-div-" . $id . "' class='prod-cat-sidebar-tag-div'>\n";
		$SidebarString .= "<div id='prod-cat-sidebar-tag-title-" . $id . "' class='prod-cat-tag-sidebar-title'><h3>" . $Tags_Text . "</h3></div>\n";
		foreach ($Tags as $Tag) {
			$SidebarString .= "<div id='prod-cat-sidebar-tag-" . $Tag->Tag_ID . "' class='prod-cat-sidebar-tag";
			if (in_array($Tag->Tag_ID, $tags)) {$SidebarString .= " highlightBlue";}
			$SidebarString .= "'>\n";
			if ($Filter  == "Javascript" and $Tag_Logic == "OR") {$SidebarString .= "<input type='checkbox' class='jquery-prod-tag-value' name='Tag[]' value='" . $Tag->Tag_ID . "'  onclick='UPCP_Filer_Results_OR(); UPCPHighlight(this, \"" . $Color . "\");'>" . $Tag->Tag_Name . "\n";}
			elseif ($Filter  == "Javascript") {$SidebarString .= "<input type='checkbox' class='jquery-prod-tag-value' name='Tag[]' value='" . $Tag->Tag_ID . "'  onclick='UPCP_Filer_Results(); UPCPHighlight(this, \"" . $Color . "\");'> " . $Tag->Tag_Name . "\n";}
			else {
				$SidebarString .= "<input type='checkbox' name='Tag[]' value='" . $Tag->Tag_ID . "'  onclick='UPCP_DisplayPage(\"1\"); UPCPHighlight(this, \"" . $Color . "\");' class='jquery-prod-tag-value'";
				if (in_array($Tag->Tag_ID, $tags)) {$SidebarString .= "checked=checked";}
				$SidebarString .= "> " . $Tag->Tag_Name . "\n";
			}
			$SidebarString .= "</div>";
		}
		$SidebarString .= "</div>\n";
	}

	if (sizeOf($Custom_Fields) > 0) {
		$SidebarString .= "<div id='prod-cat-sidebar-cf-div-" . $id . "' class='prod-cat-sidebar-cf-div'>\n";
		$SidebarString .= "<div id='prod-cat-sidebar-cf-title-" . $id . "' class='prod-cat-cf-sidebar-title'><h3>" . $Custom_Field_Text . "</h3></div>\n";
		foreach ($Custom_Fields as $Custom_Field) {
			$SidebarString .= "<div id='prod-cat-sidebar-cf-" . $Custom_Field->Field_ID . "' class='prod-cat-sidebar-cf' data-cfid='" . $Custom_Field->Field_ID . "'>\n";
			$SidebarString .= "<div class='prod-cat-sidebar-cf-title'>" . $Custom_Field->Field_Name . "</div>";
			foreach ($ProdCustomFields[$Custom_Field->Field_ID]  as $Meta_Value => $Count) {
				$SidebarString .= "<div class='prod-cat-sidebar-cf-value-div'>";
				$SidebarString .= "<input type='checkbox' name='Custom_Field[]' value='" . $Meta_Value . "'  onclick='UPCP_DisplayPage(\"1\"); UPCPHighlight(this, \"" . $Color . "\");' class='jquery-prod-cf-value' /> " . $Meta_Value . " (" . $Count . ")";
				$SidebarString .= "</div>";
			}
			$SidebarString .= "</div>";
		}
		$SidebarString .= "</div>\n";
	}
				
	$SidebarString .= "</form>\n</div>\n";
		
	return $SidebarString;
}

function BuildGridster($Gridster, $Product, $Item_Images, $Description, $PhotoURL, $SP_Perm_URL, $Return_URL, $TagsString) {
	global $wpdb, $fields_meta_table_name, $fields_table_name;

	$Back_To_Catalogue_Label = get_option("UPCP_Back_To_Catalogue_Label");
	if ($Back_To_Catalogue_Label != "") {$Back_To_Catalogue_Text = $Back_To_Catalogue_Label;}
	else {$Back_To_Catalogue_Text = __("Back to Catalogue", 'UPCP');}
		
	if (is_array($Gridster)) {
		foreach ($Gridster as $Element) {
			switch ($Element->element_class) {
				case "additional_images":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-addt-images-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<div id='prod-cat-addt-details-thumbs-div-" . $Product->Item_ID . "' class='prod-cat-addt-details-thumbs-div'>";
					$ProductString .= "<img src='" . $PhotoURL . "' id='prod-cat-addt-details-thumb-P". $Product->Item_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"0\");'>";
					foreach ($Item_Images as $Image) {$ProductString .= "<img src='" . htmlspecialchars($Image->Item_Image_URL, ENT_QUOTES) . "' id='prod-cat-addt-details-thumb-". $Image->Item_Image_ID . "' class='prod-cat-addt-details-thumb' onclick='ZoomImage(\"" . $Product->Item_ID . "\", \"" . $Image->Item_Image_ID . "\");'>";}
					$ProductString .= "</div>";
					break;
				case "back":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-back-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<a class='upcp-catalogue-link' href='" . $Return_URL . "'>&#171; " . $Back_To_Catalogue_Text . "</a>";
					break;
				case "blank":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-blank-div gs-w' style='display: list-item; position:absolute;'>";
					break;
				case "category":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-cat-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= $Product->Category_Name;
					break;
				case "category_label":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-cat-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= __("Category:", 'UPCP') . " ";
					break;
				case "description":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-description-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= $Description;
					break;
				case "main_image":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-main-image-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<img src='" . $PhotoURL . "' alt='" . $Product->Item_Name . " Image' id='prod-cat-addt-details-main-" . $Product->Item_ID . "' class='prod-cat-addt-details-main' />";
					break;
				case "next_previous":
					$Next_Previous_Type = get_option("UPCP_Next_Previous");
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-prod-name-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= Get_Next_Previous($Product, $Next_Previous_Type);
				case "price":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-price-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<h3 class='prod-cat-addt-details-price'>" . $Product->Item_Price . "</h3>";
					break;
				case "price_label":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-price-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "Price: ";
					break;
				case "product_link":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-prod-link-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $SP_Perm_URL . "'><img class='upcp-product-url-icon' src='" . get_bloginfo('wpurl') . "/wp-content/plugins/ultimate-product-catalogue/images/insert_link.png' /></a>";
					break;
				case "product_name":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-prod-name-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= "<h2 class='prod-cat-addt-details-title upcp-cpp-title'><a class='no-underline' href='http://" . $_SERVER['HTTP_HOST'] . $SP_Perm_URL . "'>" . $Product->Item_Name . "</a></h2>";
					break;
				case "related_products":
					$Related_Type = get_option("UPCP_Related_Products");
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-prod-name-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= Get_Related_Products($Product, $Related_Type);
					break;
				case "subcategory":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-sub-cat-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= $Product->SubCategory_Name;
					break;
				case "subcategory_label":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-sub-cat-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= __("Sub-Category:", 'UPCP') . " ";
					break;
				case "tags":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-tags-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= $TagsString;
					break;
				case "tags_label":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-tags-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= __("Tags:", 'UPCP') . " ";
					break;
				case "text":
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-tags-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= do_shortcode($Element->element_id);
					break;
				case "custom_field":
					$Field_Value = $wpdb->get_row("SELECT Meta_Value FROM $fields_meta_table_name WHERE Field_ID='" . $Element->element_id ."' AND Item_ID='" . $Product->Item_ID . "'");
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-custom-field-div gs-w' style='display: list-item; position:absolute;'>";
					if ($Field->Field_Type == "file") {
						$upload_dir = wp_upload_dir();
						$ProductString .= "<a href='" . $upload_dir['baseurl'] . "/upcp-product-file-uploads/" . $Field_Value->Meta_Value . "' download>" . $Field_Value->Meta_Value . "</a>";
					}
					else {$ProductString .= $Field_Value->Meta_Value;}
					break;
				case "custom_label":
					$Field = $wpdb->get_row("SELECT Field_Name FROM $fields_table_name WHERE Field_ID='" . $Element->element_id ."'");
					$ProductString .= "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "' class='prod-page-div prod-page-front-end prod-page-custom-field-label-div gs-w' style='display: list-item; position:absolute;'>";
					$ProductString .= $Field->Field_Name . ": ";
					break;
			}
			$MaxCol = max($MaxCol, $Element->col);
			$ProductString .= "</li>";								
		}
	}
		
	return $ProductString;
}

function Get_Next_Previous($Product, $Next_Previous_Type = "Manual") {
	global $wpdb, $items_table_name;

	if ($Next_Previous_Type == "Manual") {
		$Next_Product_ID = substr($Product->Item_Next_Previous, 0, strpos($Product->Item_Next_Previous, ","));
		$Previous_Product_ID = substr($Product->Item_Next_Previous, strpos($Product->Item_Next_Previous, ",")+1);
	}

	$Next_Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_ID='" . $Next_Product_ID ."'");
	$Previous_Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_ID='" . $Previous_Product_ID ."'");

	$ReturnString .= "<div class='upcp-next-previous-products'>";
	$ReturnString .= "<div class='upcp-next-product upcp-minimal-product-listing'>";
	$ReturnString .= "<div class='upcp-next-product-title'>" . __("Next Product:", "UPCP") . "</div>";
	$ReturnString .= Build_Minimal_Product_Listing($Next_Product);
	$ReturnString .= "<div class='upcp-clear'></div>";
	$ReturnString .= "</div>";
	$ReturnString .= "<div class='upcp-previous-product upcp-minimal-product-listing'>";
	$ReturnString .= "<div class='upcp-previous-product-title'>" . __("Previous Product:", "UPCP") . "</div>";
	$ReturnString .= Build_Minimal_Product_Listing($Previous_Product);
	$ReturnString .= "<div class='upcp-clear'></div>";
	$ReturnString .= "</div>";
	$ReturnString .= "<div class='upcp-clear'></div>";
	$ReturnString .= "</div>";

	return $ReturnString;
}

function Get_Related_Products($Product, $Related_Type = "Auto") {
	global $wpdb, $items_table_name;

	if ($Related_Type == "Manual") {
		$Related_Products_IDs = explode(",", $Product->Item_Related_Products);
		foreach ($Related_Products_IDs as $Related_Product_ID) {$ID_String .= "'" . $Related_Product_ID . "',";}
		$ID_String = substr($ID_String, 0, -1);
		$Related_Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Item_ID IN (" . $ID_String . ")");
	}

	elseif ($Related_Type == "Auto") {
		$Ordered_Sub_Cat_Products = array();
		$Ordered_Cat_Products = array();

		$Sub_Category_Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE SubCategory_ID='" . $Product->SubCategory_ID . "' AND Item_ID!='" . $Product->Item_ID . "'", ARRAY_A);
		if ($wpdb->num_rows < 5) {
			$Category_Products = $wpdb->get_results("SELECT * FROM $items_table_name WHERE Category_ID='" . $Product->Category_ID . "' AND SubCategory_ID!='" . $Product->SubCategory_ID . "' AND Item_ID!='" . $Product->Item_ID . "'", ARRAY_A);
		}

		$Ordered_Sub_Cat_Products = Order_Products($Selected_Product, $Sub_Category_Products);
		if (isset($Category_Products)) {$Ordered_Cat_Products = Order_Products($Selected_Product, $Category_Products);}

		$Related_Products = $Ordered_Sub_Cat_Products + $Ordered_Cat_Products;
		$Related_Products = array_splice($Related_Products, 0, 5);
	}

	$ReturnString .= "<div class='upcp-related-products'>";
	$ReturnString .= "<div class='upcp-related-products-title'>" . __("Related Products:", "UPCP") . "</div>";
	$ReturnString .= "<div class='upcp-clear'></div>";
	foreach ($Related_Products as $Related_Product) {
		$ReturnString .= "<div class='upcp-related-product upcp-minimal-product-listing'>";
		$ReturnString .= Build_Minimal_Product_Listing($Related_Product);
		$ReturnString .= "<div class='upcp-clear'></div>";
		$ReturnString .= "</div>";
	}
	$RetunString .= "</div>";
	$RetunString .= "</div>";

	return $ReturnString;
}

function Order_Products($Product, $Related_Products_Array) {
	global $wpdb, $tagged_items_table_name;

	$Product_Tags = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Product['Item_ID'] . "'", ARRAY_A);

	foreach ($Related_Products_Array as $Related_Product) {
		$Related_Product_Tags = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Related_Product['Item_ID'] . "'", ARRAY_A);
		$Intersect = array_intersect($Product_Tags, $Related_Product_Tags);
		$Related_Product['Score'] = sizeOf($Intersect);
		unset($Related_Product_Tags);
	}

	usort($Related_Products_Array, 'Score_Sort');

	return $Related_Products_Array;
}

function Score_Sort($a, $b) {
	return $a['Score'] == $b['Score'] ? 0 : ($a['Score'] > $b['Score']) ? 1 : -1;
}

function Build_Minimal_Product_Listing($Product, $Catalogue_URL = "") {
	global $wpdb, $items_table_name;

	$Pretty_Links = get_option("UPCP_Pretty_Links");

	if (is_array($Product)) {$Product = $wpdb->get_row("SELECT * FROM $items_table_name WHERE Item_ID='" . $Product['Item_ID'] . "'");}

	if ($Product->Item_ID == "") {return;}

	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	if ($Catalogue_URL == "") {
		$Base = $uri_parts[0];
		$Base = substr($Base, 0, strpos($Base, "/product/")+1);
	}
	else {$Base = $Catalogue_URL;}
	if ($Product->Item_Link != "") {$ItemLink = $Product->Item_Link;}
	elseif ($Pretty_Links == "Yes") {$ItemLink = $Base . "product/" . $Product->Item_Slug . "/?" . $uri_parts[1];}
	elseif (strpos($uri_parts[1], "page_id") !== false) {$ItemLink = $Base . "?" . substr($uri_parts[1], 0, strpos($uri_parts[1], "&")) . "&SingleProduct=" . $Product->Item_ID;}
	else {$ItemLink = $Base . "?SingleProduct=" . $Product->Item_ID;}

	if ($Product->Item_Photo_URL != "") {$PhotoURL = $Product->Item_Photo_URL;}
	else {$PhotoURL = plugins_url('ultimate-product-catalogue/images/No-Photo-Available.jpg');}

	$ReturnString .= "<a class='upcp-minimal-link' href='" . $ItemLink . "'>";
	$ReturnString .= "<div class='upcp-minimal-img-div'>";
	$ReturnString .= "<img class='upcp-minimal-img' src='" . $PhotoURL . "' alt='Product Image' />";
	$ReturnString .= "</div>";
	$ReturnString .= "<div class='upcp-minimal-title'>" . $Product->Item_Name . "</div>";
	$ReturnString .= "<div class='upcp-minimal-price'>" . $Product->Item_Price . "</div>";
	$ReturnString .= "</a>";

	return $ReturnString;
}

function FilterCount($Product, $Tags, $Custom_Fields) {
	global $ProdCats, $ProdSubCats, $ProdTags, $ProdCustomFields;

	// Increment the arrays keeping count of the number of products in each 
	// category, sub-category and tag
	$ProdCats[$Product->Category_ID]++;
	$ProdSubCats[$Product->SubCategory_ID]++;
	foreach ($Tags as $Tag) {$ProdTags[$Tag->Tag_ID]++;}
	if (is_array($Custom_Fields)) {
		foreach ($Custom_Fields as $Custom_Field) {$ProdCustomFields[$Custom_Field->Field_ID][$Custom_Field->Meta_Value]++;}
	}
}

function SearchProductName($Item_ID, $ProductName, $ProductDescription, $SearchName, $CaseInsensitive, $SearchLocation) {
	global $wpdb;
	global $fields_meta_table_name;
		
	if ($CaseInsensitive == "Yes") {
		$ProductName = strtolower($ProductName);
		$ProductDescription = strtolower($ProductDescription);
		$SearchName = strtolower($SearchName);
	}
		
	if ($SearchName == ""){$NameSearchMatch = "Yes";}
	elseif (strpos($ProductName, $SearchName) !== false) {$NameSearchMatch = "Yes";}
	elseif (strpos($ProductDescription, $SearchName) !== false) {
		if ($SearchLocation == "namedesc" or $SearchLocation == "namedesccust") {
			$NameSearchMatch = "Yes";
		}
	}
		
	if ($NameSearchMatch != "Yes" and $SearchLocation == "namedesccust") {
		$SearchName =  "%" .  $SearchName . "%";
		$Metas = $wpdb->get_results($wpdb->prepare("SELECT Meta_Value FROM $fields_meta_table_name WHERE Item_ID='" . $Item_ID . "' and Meta_Value LIKE %s", $SearchName));
		if (sizeOf($Metas) > 0) {$NameSearchMatch = "Yes";}
	}
		
	return $NameSearchMatch;
}

function Custom_Field_Check($Custom_Fields_Sql_String, $Custom_Field_Count, $Product_ID) {
	global $wpdb, $fields_meta_table_name;

	if ($Custom_Fields_Sql_String == "") {return "Yes";}

	$Fields = $wpdb->get_results("SELECT Field_ID FROM $fields_meta_table_name WHERE " . $Custom_Fields_Sql_String . " AND Item_ID='" . $Product_ID . "'");
	if ($wpdb->num_rows == $Custom_Field_Count) {return "Yes";}
	else {return "No";} 
}

function CheckTags($tags, $ProdTag, $Tag_Logic) {
	if (sizeOf($tags) == 0) {return "Yes";}

	if ($Tag_Logic == "OR") {
		if (count(array_intersect($tags, $ProdTag)) > 0) {return "Yes";}
	}
	else {
		if (count(array_intersect($tags, $ProdTag)) == sizeOf($tags)) {return "Yes";}
	}
		
	return "No";
}

function CheckPagination($Product_Count, $products_per_page, $current_page, $Filtered = "No") {
	if ($products_per_page >= 1000000) {return "OK";}
	if ($Product_Count >= ($products_per_page * ($current_page - 1))) {
		if ($Product_Count < ($products_per_page * $current_page)) {
			return "OK";
		}
		else {
			return "Over";
		}
	}
		
	if ($Filtered == "Yes") {return "Filtered";}
	else {return "Under";}
}

function ConvertCustomFields($Description) {
	global $wpdb;
	global $fields_table_name, $fields_meta_table_name;
		
	$upload_dir = wp_upload_dir();
		
	$Fields = $wpdb->get_results("SELECT Field_ID, Field_Slug, Field_Type FROM $fields_table_name");
	$Metas = $wpdb->get_results("SELECT Field_ID, Meta_Value FROM $fields_meta_table_name");
		
	if (is_array($Fields)) {
		if (is_array($Metas)) {
			foreach ($Metas as $Meta) {
				$MetaArray[$Meta->Field_ID] = $Meta->Meta_Value;
			}
		}
		foreach ($Fields as $Field) {
			if ($Field->Field_Type == "file") {
				$LinkString = "<a href='" . $upload_dir['baseurl'] . "/upcp-product-file-uploads/" . $MetaArray[$Field->Field_ID] . "' download>" . $MetaArray[$Field->Field_ID] . "</a>";
				$Description = str_replace("[" . $Field->Field_Slug . "]" , $LinkString, $Description);
			}
			else {$Description = str_replace("[" . $Field->Field_Slug . "]" , $MetaArray[$Field->Field_ID], $Description);}
		}
	}
		
	return $Description;
}

function AddCustomFields($ProductID, $Layout) {
	global $wpdb;
	global $fields_table_name, $fields_meta_table_name;
		
	$upload_dir = wp_upload_dir();
		
	$Fields = $wpdb->get_results("SELECT Field_ID, Field_Name, Field_Type FROM $fields_table_name WHERE Field_Displays='" . $Layout . "' OR Field_Displays='both'");
	if (is_array($Fields)) {
		$CustomFieldString .= "<div class='upcp-prod-desc-custom-fields upcp-custom-field-" . $Layout . "'>";
		if ($Layout == "details") {$AddBreak = "<br />";}
		else {$AddBreak = "";}
		foreach ($Fields as $Field) {
			$Meta = $wpdb->get_row("SELECT Meta_Value FROM $fields_meta_table_name WHERE Field_ID='" . $Field->Field_ID . "' AND Item_ID='" . $ProductID . "'");
			if ($Field->Field_Type == "file") {
				$CustomFieldString .= $AddBreak . $Field->Field_Name . ": ";
				$CustomFieldString .= "<a href='" . $upload_dir['baseurl'] . "/upcp-product-file-uploads/" .$Meta->Meta_Value . "' download>" . $Meta->Meta_Value . "</a>";
			}
			else {$CustomFieldString .= $AddBreak . $Field->Field_Name . ": " . $Meta->Meta_Value;}
			$AddBreak = "<br />";
		}
		$CustomFieldString .= "</div>";
	}
		
	return $CustomFieldString;
}

function ObjectToArray($Obj) {
	$TagsArray = array();
	foreach ($Obj as $Tag) {
		$TagsArray[] = $Tag->Tag_ID;
	}
		
	return $TagsArray;
}

function UPCP_Filter_Title($ProductName) {
	echo $ProductName;
	add_filter('the_title', 'UPCP_Alter_Title', 20, $ProductName);
}

function UPCP_Alter_Title($Title, $ProductName) {
	$Title = $ProductName . " | " . $Title;
	return $Title;
}


 ?>