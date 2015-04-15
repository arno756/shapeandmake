<?php
/* The file contains all of the functions which make changes to the UPCP tables */

/* Adds a single new category to the UPCP database */
function Add_UPCP_Category($Category_Name, $Category_Description = "") {
		global $wpdb;
		global $categories_table_name;
		
		$wpdb->insert($categories_table_name,
				array( 'Category_Name' => $Category_Name,
							 'Category_Description' => $Category_Description,
							 'Category_Item_Count' => 0)
		);
		$update = __("Category has been successfully created.", 'UPCP');
		return $update;
}

/* Edits a single category with a given ID in the UPCP database */
function Edit_UPCP_Category($Category_ID, $Category_Name, $Category_Description = "") {
		global $wpdb;
		global $categories_table_name;
		
		$wpdb->update(
						$categories_table_name,
						array( 'Category_Name' => $Category_Name,
									 'Category_Description' => $Category_Description),
						array( 'Category_ID' => $Category_ID)
		);
		$update = __("Category has been successfully edited.", 'UPCP');
		return $update;
}

/* Deletes a single category with a given ID in the UPCP database */
function Delete_UPCP_Category($Cat) {
		global $wpdb;
		global $categories_table_name;
		global $items_table_name;
		
		$wpdb->delete(
						$categories_table_name,
						array('Category_ID' => $Cat)
					);
					
		$Products = $wpdb->get_results("SELECT Item_ID FROM $items_table_name WHERE Category_ID='" . $Cat . "'");
		foreach ($Products as $Product) {
				$wpdb->update(
								$items_table_name,
								array(
										'Category_ID' => 0,
										'Category_Name' => ''),
								array( 'Item_ID' => $Product->Item_ID)
				);
		}

		$update = __("Category has been successfully deleted.", 'UPCP');
		return $update;
}

/* Adds a single new sub-category to the UPCP database */
function Add_UPCP_SubCategory($SubCategory_Name, $Category_ID, $SubCategory_Description) {
		global $wpdb;
		global $subcategories_table_name;
		global $categories_table_name;
		
		if ($Category_ID != "") {$Category = $wpdb->get_row("SELECT * FROM $categories_table_name WHERE Category_ID =" . $Category_ID); $Category_Name = $Category->Category_Name;}
		
		$wpdb->insert($subcategories_table_name,
				array( 'SubCategory_Name' => $SubCategory_Name,
							 'Category_ID' => $Category_ID,
							 'Category_Name' => $Category_Name,
							 'SubCategory_Description' => $SubCategory_Description,
							 'SubCategory_Item_Count' => 0)
		);
		$update = __("Sub-Category has been successfully created.", 'UPCP');
		return $update;
}

/* Edits a single sub-category with a given ID in the UPCP database */
function Edit_UPCP_SubCategory($SubCategory_ID, $SubCategory_Name, $Category_ID, $SubCategory_Description) {
		global $wpdb;
		global $subcategories_table_name;
		global $categories_table_name;
		global $items_table_name;
		
		if ($Category_ID != "") {$Category = $wpdb->get_row("SELECT * FROM $categories_table_name WHERE Category_ID =" . $Category_ID); $Category_Name = $Category->Category_Name;}
		
		$wpdb->update(
						$subcategories_table_name,
						array( 'SubCategory_Name' => $SubCategory_Name,
									 'Category_ID' => $Category_ID,
									 'Category_Name' => $Category_Name,
							 		 'SubCategory_Description' => $SubCategory_Description),
						array( 'SubCategory_ID' => $SubCategory_ID)
		);
		$wpdb->update(
						$items_table_name,
						array( 'SubCategory_Name' => $SubCategory_Name,
									 'Category_ID' => $Category_ID,
									 'Category_Name' => $Category_Name),
						array( 'SubCategory_ID' => $SubCategory_ID)
		);
		$update = __("Sub-Category has been successfully edited.", 'UPCP');
		return $update;
}

/* Deletes a single sub-category with a given ID in the UPCP database */
function Delete_UPCP_SubCategory($Sub_ID) {
		global $wpdb;
		global $subcategories_table_name;
		global $items_table_name;
		
		$wpdb->delete(
						$subcategories_table_name,
						array('SubCategory_ID' => $Sub_ID)
					);
					
		$Products = $wpdb->get_results("SELECT Item_ID FROM $items_table_name WHERE SubCategory_ID='" . $Sub_ID . "'");
		foreach ($Products as $Product) {
				$wpdb->update(
								$items_table_name,
								array(
										'SubCategory_ID' => 0,
										'SubCategory_Name' => ''),
								array('Item_ID' => $Product->Item_ID)
				);
		}

		$update = __("Sub-Category has been successfully deleted.", 'UPCP');
		return $update;
}

/* Adds a single new tag to the UPCP database */
function Add_UPCP_Tag($Tag_Name, $Tag_Description) {
		global $wpdb;
		global $tags_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}
		$wpdb->insert($tags_table_name,
				array( 'Tag_Name' => $Tag_Name,
							 'Tag_Description' => $Tag_Description,
							 'Tag_Item_Count' => 0)
		);
		$update = __("Tag has been successfully created.", 'UPCP');
		return $update;
}

/* Edits a single tag with a given ID in the UPCP database */
function Edit_UPCP_Tag($Tag_ID, $Tag_Name, $Tag_Description) {
		global $wpdb;
		global $tags_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}		
		$wpdb->update(
						$tags_table_name,
						array( 'Tag_Name' => $Tag_Name,
							 		 'Tag_Description' => $Tag_Description),
						array( 'Tag_ID' => $Tag_ID)
		);
		$update = __("Tag has been successfully edited.", 'UPCP');
		return $update;
}

/* Deletes a single tag with a given ID in the UPCP database, and then eliminates 
*  all of the occurrences of that tag from the tagged items table.  */
function Delete_UPCP_Tag($Tag_ID) {
		global $wpdb;
		global $tags_table_name;
		global $tagged_items_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}		
		$wpdb->delete(
						$tags_table_name,
						array('Tag_ID' => $Tag_ID)
					);
					
		$wpdb->delete(
						$tagged_items_table_name,
						array('Tag_ID' => $Tag_ID)
					);

		$update = __("Tag has been successfully deleted.", 'UPCP');
		return $update;
}

/* Deletes a single tagged item with a given ID in the UPCP database */
function Delete_Products_Tags() {
		global $wpdb;
		global $tagged_items_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}					
		$wpdb->delete(
						$tagged_items_table_name,
						array('Tagged_Item_ID' => $_GET['Tagged_Item_ID'])
					);

		$update = __("Tag has been successfully deleted from product.", 'UPCP');
		return $update;
}

/* Adds a single new custom field to the UPCP database */
function Add_UPCP_Custom_Field($Field_Name, $Field_Slug, $Field_Type, $Field_Description, $Field_Values, $Field_Displays, $Field_Searchable) {
		global $wpdb;
		global $fields_table_name;
		$Date = date("Y-m-d H:i:s");
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}		
		$wpdb->insert($fields_table_name,
				array( 'Field_Name' => $Field_Name,
							 'Field_Slug' => $Field_Slug,
							 'Field_Type' => $Field_Type,
							 'Field_Description' => $Field_Description,
							 'Field_Values' => $Field_Values,
							 'Field_Values' => $Field_Values,
							 'Field_Displays' => $Field_Displays,
							 'Field_Searchable' => $Field_Searchable,
							 'Field_Date_Created' => $Date)
		);
		$update = __("Field has been successfully created.", 'UPCP');
		return $update;
}

/* Edits a single custom field with a given ID in the UPCP database */
function  Edit_UPCP_Custom_Field($Field_ID, $Field_Name, $Field_Slug, $Field_Type, $Field_Description, $Field_Values, $Field_Displays, $Field_Searchable) {
		global $wpdb;
		global $fields_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}		
		$wpdb->update(
						$fields_table_name,
						array( 'Field_Name' => $Field_Name,
									 'Field_Slug' => $Field_Slug,
									 'Field_Type' => $Field_Type,
							 		 'Field_Description' => $Field_Description,
									 'Field_Values' => $Field_Values,
									 'Field_Displays' => $Field_Displays,
									 'Field_Searchable' => $Field_Searchable),
						array( 'Field_ID' => $Field_ID)
		);
		$update = __("Field has been successfully edited.", 'UPCP');
		return $update;
}

/* Deletes a single tag with a given ID in the UPCP database, and then eliminates 
*  all of the occurrences of that tag from the tagged items table.  */
function Delete_UPCP_Custom_Field($Field_ID) {
		global $wpdb;
		global $fields_table_name;
		global $Full_Version;
		
		if ($Full_Version != "Yes") {exit();}		
		$wpdb->delete(
						$fields_table_name,
						array('Field_ID' => $Field_ID)
					);
					

		$update = __("Field has been successfully deleted.", 'UPCP');
		return $update;
}

/* Adds a single new catalogue to the UPCP database */
function Add_UPCP_Catalogue($Catalogue_Name, $Catalogue_Description) {
		global $wpdb;
		global $catalogues_table_name;
		
		$wpdb->insert($catalogues_table_name,
				array( 'Catalogue_Name' => $Catalogue_Name,
							 'Catalogue_Description' => $Catalogue_Description,
							 'Catalogue_Item_Count' => 0)
		);
		$update = __("Catalogue has been successfully created.", 'UPCP');
		return $update;
}

/* Edits a single catalogue with a given ID in the UPCP database */
function Edit_UPCP_Catalogue($Catalogue_ID, $Catalogue_Name, $Catalogue_Description, $Catalogue_Layout_Format = "", $Catalogue_Custom_CSS = "") {
		global $wpdb;
		global $catalogues_table_name;
		
		$wpdb->update(
						$catalogues_table_name,
						array( 'Catalogue_Name' => $Catalogue_Name,
							 		 'Catalogue_Description' => $Catalogue_Description,
									 'Catalogue_Layout_Format' => $Catalogue_Layout_Format,
									 'Catalogue_Custom_CSS' => $Catalogue_Custom_CSS),
						array( 'Catalogue_ID' => $Catalogue_ID)
		);
		$update = __("Catalogue has been successfully edited.", 'UPCP');
		return $update;
}

/* Adds one or multiple new products to to a single catalogue in the UPCP database */
function Add_Products_Catalogue() {
		global $wpdb;
		global $catalogues_table_name;
		global $catalogue_items_table_name;
		
		$Catalogue_ID = $_GET['Catalogue_ID'];
		foreach ($_POST['products'] as $Item_ID) {
				$MaxPos = $wpdb->get_var($wpdb->prepare("SELECT MAX(Position) FROM $catalogue_items_table_name WHERE Catalogue_ID='%d'", $Catalogue_ID));
				$Position = $MaxPos + 1;
				$wpdb->insert($catalogue_items_table_name,
				array( 'Catalogue_ID' => $Catalogue_ID,
							 'Item_ID' => $Item_ID,
							 'Position' => $Position)
				);
		}
		
		Update_Catalogue_Item_Count($Catalogue_ID);
		
		$update = __("Products have been successfully added to the catalogue.", 'UPCP');
		return $update;
}
/* Adds one or multiple new categories to to a single catalogue in the UPCP database */
function Add_Categories_Catalogue() {
		global $wpdb;
		global $catalogues_table_name;
		global $catalogue_items_table_name;
		
		$Catalogue_ID = $_GET['Catalogue_ID'];
		foreach ($_POST['categories'] as $Category_ID) {
				$MaxPos = $wpdb->get_var($wpdb->prepare("SELECT MAX(Position) FROM $catalogue_items_table_name WHERE Catalogue_ID='%d'", $Catalogue_ID));
				$Position = $MaxPos + 1;
				$wpdb->insert($catalogue_items_table_name,
				array( 'Catalogue_ID' => $Catalogue_ID,
							 'Category_ID' => $Category_ID,
							 'Position' => $Position)
				);
		}
		
		Update_Catalogue_Item_Count($Catalogue_ID);
		
		$update = __("Categories have been successfully added to the catalogue.", 'UPCP');
		return $update;
}

function Update_Catalogue_Item_Count($Catalogue_ID) {
		global $wpdb;
		global $catalogues_table_name;
		global $catalogue_items_table_name;
		global $subcategories_table_name;
		global $categories_table_name;
		global $items_table_name;
		
		$Individual_Products = $wpdb->get_results($wpdb->prepare("SELECT Catalogue_Item_ID FROM $catalogue_items_table_name WHERE Catalogue_ID='%d' AND Item_ID!='NULL' AND Item_ID!='0'", $Catalogue_ID));
		$Total_Products = $wpdb->num_rows;
		
		$Categories = $wpdb->get_results($wpdb->prepare("SELECT Category_ID FROM $catalogue_items_table_name WHERE Catalogue_ID='%d' AND Category_ID!='NULL' AND Category_ID!='0'", $Catalogue_ID));
		foreach ($Categories as $Category) {
				$Individual_Products = $wpdb->get_results("SELECT Item_ID FROM $items_table_name WHERE Category_ID='" . $Category->Category_ID . "'");
				$Total_Products += $wpdb->num_rows;
		}
		
		$SubCategories = $wpdb->get_results($wpdb->prepare("SELECT SubCategory_ID FROM $catalogue_items_table_name WHERE Catalogue_ID='%d' AND SubCategory_ID!='NULL' AND SubCategory_ID!='0'", $Catalogue_ID));
		foreach ($SubCategories as $SubCategory) {
				$Individual_Products = $wpdb->get_results("SELECT Item_ID FROM $items_table_name WHERE SubCategory_ID='" . $SubCategory->SubCategory_ID . "'");
				$Total_Products += $wpdb->num_rows;
		}
		
		$Result = $wpdb->query($wpdb->prepare("UPDATE $catalogues_table_name SET Catalogue_Item_Count='" . $Total_Products . "' WHERE Catalogue_ID='%d'", $Catalogue_ID));
}

/* Deletes a single catalogue with a given ID in the UPCP database */
function Delete_UPCP_Catalogue($Catalogue_ID) {
		global $wpdb;
		global $catalogues_table_name;
		global $catalogue_items_table_name;
		
		$wpdb->delete(
						$catalogues_table_name,
						array('Catalogue_ID' => $Catalogue_ID)
					);
		$wpdb->delete(
				$catalogue_items_table_name,
				array('Catalogue_ID' => $Catalogue_ID)
		);

		$update = __("Catalogue has been successfully deleted.", 'UPCP');
		return $update;
}

/* Deletes a single product with a given ID from a catalogue in the UPCP database */
function Delete_Products_Catalogue() {
		global $wpdb;
		global $catalogues_table_name;
		global $catalogue_items_table_name;
		
		$Catalogue_ID = $wpdb->get_var($wpdb->prepare("SELECT Catalogue_ID FROM $catalogue_items_table_name WHERE Catalogue_Item_ID='%d'", $_GET['Catalogue_Item_ID']));	
					
		$wpdb->delete(
						$catalogue_items_table_name,
						array('Catalogue_Item_ID' => $_GET['Catalogue_Item_ID'])
					);
			
		Update_Catalogue_Item_Count($Catalogue_ID);

		$update = __("Product has been successfully deleted from catalogue.", 'UPCP');
		return $update;
}

/* Adds a single new product inputted via the form on the left-hand side of the
*  products' page to the UPCP database */
function Add_UPCP_Product($Item_Name, $Item_Slug, $Item_Photo_URL, $Item_Description, $Item_Price, $Item_Link, $Item_Display_Status = "", $Category_ID = "", $Global_Item_ID = "", $Item_Special_Attr = "", $SubCategory_ID = "", $Tags = array(), $Related_Products = "", $Next_Previous = "") {
		global $wpdb;
		global $items_table_name;
		global $categories_table_name;
		global $subcategories_table_name;
		global $tagged_items_table_name;
		global $tags_table_name;
		global $fields_table_name;
		global $fields_meta_table_name;
		global $Full_Version;
		
		$Prod_Count = $wpdb->get_var("SELECT COUNT(*) FROM " . $items_table_name);
		
		
		if (Prod_Count >= 100 and $Full_Version != "Yes") {
			  $update = __("Maximum number of products (100) has been reached for free version. Upgrade to the premium version to continue.", 'UPCP');
				return $update;
		}

		//Find the category and sub-category names, since only the ID's are passed in via the form
		if ($Category_ID != "") {$Category = $wpdb->get_row("SELECT * FROM $categories_table_name WHERE Category_ID =" . $Category_ID); $Category_Name = $Category->Category_Name;}
		if ($SubCategory_ID != "") {$SubCategory = $wpdb->get_row("SELECT * FROM $subcategories_table_name WHERE SubCategory_ID =" . $SubCategory_ID); $SubCategory_Name = $SubCategory->SubCategory_Name;}
		$Today = date("Y-m-d H:i:s"); 
		
		$wpdb->insert($items_table_name,
				array(
						'Item_Name' => $Item_Name,
						'Item_Slug' => $Item_Slug,
						'Item_Description' => $Item_Description,
						'Item_Price' => $Item_Price,
						'Item_Link' => $Item_Link,
						'Item_Photo_URL' => $Item_Photo_URL,
						'Item_Display_Status' => $Item_Display_Status,
						'Category_ID' => $Category_ID,
						'Category_Name' => $Category_Name,
						'Global_Item_ID' => $Global_Item_ID,
						'Item_Special_Attr' => $Item_Special_Attr,
						'SubCategory_ID' => $SubCategory_ID,
						'SubCategory_Name' => $SubCategory_Name,
						'Item_Related_Products' => $Related_Products,
						'Item_Next_Previous' => $Next_Previous,
						'Item_Date_Created' => $Today)
		);
		
		// Increase the Item_Count column for the category and sub-category tables in the database
		if ($Category_ID != "") {$wpdb->query("UPDATE $categories_table_name SET Category_Item_Count=Category_Item_Count + 1 WHERE Category_ID =" . $Category_ID);}
		if ($SubCategory_ID != "") {$wpdb->query("UPDATE $subcategories_table_name SET SubCategory_Item_Count=SubCategory_Item_Count + 1 WHERE SubCategory_ID =" . $SubCategory_ID);}
		
		// Create the tagged item in the tagged items table for each "tag" checkbox
		// and update the Item_Count column in the tags table in the database
		$Item_ID = $wpdb->insert_id;
		foreach ($Tags as $Tag) {
				$wpdb->insert($tagged_items_table_name,
								array('Tag_ID' => $Tag,
											'Item_ID' => $Item_ID)
				);
				$wpdb->query("UPDATE $tags_table_name SET Tag_Item_Count=Tag_Item_Count + 1 WHERE Tag_ID =" . $Tag);
		}
		
		//Add the custom fields to the meta table
		$Fields = $wpdb->get_results("SELECT Field_ID, Field_Type, Field_Name, Field_Values FROM $fields_table_name");
		if (is_array($Fields)) {
			  foreach ($Fields as $Field) {
						$FieldName = str_replace(" ", "_", $Field->Field_Name);
						if (isset($_POST[$FieldName]) or isset($_FILES[$FieldName])) {
							  // If it's a file, pass back to Prepare_Data_For_Insertion.php to upload the file and get the name
								if ($Field->Field_Type == "file") {
										$File_Upload_Return = UPCP_Handle_File_Upload($FieldName);
										if ($File_Upload_Return['Success'] == "No") {return $File_Upload_Return['Data'];}
										elseif ($File_Upload_Return['Success'] == "N/A") {$NoFile = "Yes";}
										else {$Value = $File_Upload_Return['Data'];}
								}
								elseif ($Field->Field_Type == "checkbox") {
										foreach ($_POST[$FieldName] as $SingleValue) {$Value .= trim($SingleValue) . ",";}
										$Value = substr($Value, 0, strlen($Value)-1);
								}
								else {
									  $Value = trim($_POST[$FieldName]);
										$Options = explode(",", $Field->Field_Values);
										if (sizeOf($Options) > 0 and $Options[0] != "") {
									  	  array_walk($Options, create_function('&$val', '$val = trim($val);'));
												$InArray = in_array($Value, $Options);
										}
								}
								if (!isset($InArray) or $InArray) {
									  if ($NoFile != "Yes") {
											  $wpdb->insert($fields_meta_table_name,
															array( 'Field_ID' => $Field->Field_ID,
																		 'Item_ID' => $Item_ID,
																		 'Meta_Value' => $Value)
												);
										}
								}
								elseif ($InArray == false) {$CustomFieldError = __(" One or more custom field values were incorrect.", 'UPCP');}
								unset($Value);
								unset($InArray);
								unset($NoFile);
								unset($CombinedValue);
								unset($FieldName);
						}
				}
		}
		
		UPCP_Create_XML_Sitemap();
		
		$update = __("Product has been successfully created." . $CustomFieldError, 'UPCP');
		return $update;
}

/* Edits a single product in the UPCP database */
function Edit_UPCP_Product($Item_ID, $Item_Name, $Item_Slug, $Item_Photo_URL, $Item_Description, $Item_Price, $Item_Link, $Item_Display_Status = "", $Category_ID = "", $Global_Item_ID = "", $Item_Special_Attr = "", $SubCategory_ID = "", $Tags = array(), $Related_Products = "", $Next_Previous = "") {
		global $wpdb;
		global $items_table_name;
		global $categories_table_name;
		global $subcategories_table_name;	
		global $tagged_items_table_name;
		global $tags_table_name;
		global $fields_table_name;
		global $fields_meta_table_name;
		
		// Delete the tagged item in the tagged items table for the given Item_ID
		// and update the Item_Count column in the tags table in the database		
		$Tagged_Items = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Item_ID ."'");
		foreach ($Tagged_Items as $Tagged_Item) {
				$wpdb->query("UPDATE $tags_table_name SET Tag_Item_Count=Tag_Item_Count - 1 WHERE Tag_ID =" . $Tagged_Item->Tag_ID);
		}
		$wpdb->delete(
				$tagged_items_table_name,
				array('Item_ID' => $Item_ID)
		);
		
		// Delete the custom field values for the given Item_ID
		$wpdb->delete(
				$fields_meta_table_name,
				array('Item_ID' => $Item_ID)
		);
		
		// Decrease the Item_Count column for the category and sub-category tables in the database
		$Current_Product = $wpdb->get_row("SELECT Category_ID, SubCategory_ID FROM $items_table_name WHERE Item_ID='" . $Item_ID ."'");
		if ($Current_Product->Category_ID != 0) {$wpdb->query("UPDATE $categories_table_name SET Category_Item_Count=Category_Item_Count - 1 WHERE Category_ID =" . $Current_Product->Category_ID);}
		if ($Current_Product->SubCategory_ID != 0) {$wpdb->query("UPDATE $subcategories_table_name SET SubCategory_Item_Count=SubCategory_Item_Count - 1 WHERE SubCategory_ID =" . $Current_Product->SubCategory_ID);}
		
		//Find the category and sub-category names, since only the ID's are passed in via the form
		if ($Category_ID != "") {$Category = $wpdb->get_row("SELECT * FROM $categories_table_name WHERE Category_ID =" . $Category_ID); $Category_Name = $Category->Category_Name;}
		if ($SubCategory_ID != "") {$SubCategory = $wpdb->get_row("SELECT * FROM $subcategories_table_name WHERE SubCategory_ID =" . $SubCategory_ID); $SubCategory_Name = $SubCategory->SubCategory_Name;}
		
		$wpdb->update(
						$items_table_name,
						array(
						'Item_Name' => $Item_Name,
						'Item_Slug' => $Item_Slug,
						'Item_Description' => $Item_Description,
						'Item_Price' => $Item_Price,
						'Item_Link' => $Item_Link,
						'Item_Photo_URL' => $Item_Photo_URL,
						'Item_Display_Status' => $Item_Display_Status,
						'Category_ID' => $Category_ID,
						'Category_Name' => $Category_Name,
						'Global_Item_ID' => $Global_Item_ID,
						'Item_Special_Attr' => $Item_Special_Attr,
						'SubCategory_ID' => $SubCategory_ID,
						'SubCategory_Name' => $SubCategory_Name,
						'Item_Related_Products' => $Related_Products,
						'Item_Next_Previous' => $Next_Previous),
						array( 'Item_ID' => $Item_ID)
		);
		
		// Create the tagged item in the tagged items table for each "tag" checkbox
		// and update the Item_Count column in the tags table in the database
		foreach ($Tags as $Tag) {
				$wpdb->insert($tagged_items_table_name,
								array('Tag_ID' => $Tag,
											'Item_ID' => $Item_ID)
				);
				$wpdb->query("UPDATE $tags_table_name SET Tag_Item_Count=Tag_Item_Count + 1 WHERE Tag_ID =" . $Tag);
		}
		
		//Add the custom fields to the meta table
		$Fields = $wpdb->get_results("SELECT Field_ID, Field_Name, Field_Values, Field_Type FROM $fields_table_name");
		if (is_array($Fields)) {
			  foreach ($Fields as $Field) {
						$FieldName = str_replace(" ", "_", $Field->Field_Name);
						if (isset($_POST[$FieldName]) or isset($_FILES[$FieldName])) {
							  // If it's a file, pass back to Prepare_Data_For_Insertion.php to upload the file and get the name
								if ($Field->Field_Type == "file") {
										$File_Upload_Return = UPCP_Handle_File_Upload($FieldName);
										if ($File_Upload_Return['Success'] == "No") {return $File_Upload_Return['Data'];}
										elseif ($File_Upload_Return['Success'] == "N/A") {$NoFile = "Yes";}
										else {$Value = $File_Upload_Return['Data'];}
								}
								elseif ($Field->Field_Type == "checkbox") {
										foreach ($_POST[$FieldName] as $SingleValue) {$Value .= trim($SingleValue) . ",";}
										$Value = substr($Value, 0, strlen($Value)-1);
								}
								else {
									  $Value = trim($_POST[$FieldName]);
										$Options = explode(",", $Field->Field_Values);
										if (sizeOf($Options) > 0 and $Options[0] != "") {
									  	  array_walk($Options, create_function('&$val', '$val = trim($val);'));
												$InArray = in_array($Value, $Options);
										}
								}
								if (!isset($InArray) or $InArray) {
									  if ($NoFile != "Yes") {
											  $wpdb->insert($fields_meta_table_name,
															array( 'Field_ID' => $Field->Field_ID,
																		 'Item_ID' => $Item_ID,
																		 'Meta_Value' => $Value)
												);
										}
								}
								elseif ($InArray == false) {$CustomFieldError = __(" One or more custom field values were incorrect.", 'UPCP');}
								unset($Value);
								unset($InArray);
								unset($NoFile);
								unset($CombinedValue);
								unset($FieldName);
						}
				}
		}
		
		// Increase the Item_Count column for the category and sub-category tables in the database
		if ($Category_ID != "") {$wpdb->query("UPDATE $categories_table_name SET Category_Item_Count=Category_Item_Count + 1 WHERE Category_ID =" . $Category_ID);}
		if ($SubCategory_ID != "") {$wpdb->query("UPDATE $subcategories_table_name SET SubCategory_Item_Count=SubCategory_Item_Count + 1 WHERE SubCategory_ID =" . $SubCategory_ID);}
		
		UPCP_Create_XML_Sitemap();
		
		$update = __("Product has been successfully edited." . $CustomFieldError, 'UPCP');
		return $update;
}

/* Adds multiple new products inputted via a spreadsheet uploaded to the top form 
*  on the left-hand side of the products' page to the UPCP database */
function Add_UPCP_Products_From_Spreadsheet($Excel_File_Name) {
		global $wpdb;
		global $items_table_name;
		global $categories_table_name;
		global $subcategories_table_name;
		global $tags_table_name;
		global $tagged_items_table_name;	
		global $fields_table_name;
		global $fields_meta_table_name;
		global $catalogue_items_table_name;
		global $Full_Version;
		
		$Excel_URL = '../wp-content/plugins/ultimate-product-catalogue/product-sheets/' . $Excel_File_Name;
		
		// Uses the PHPExcel class to simplify the file parsing process
		include_once('../wp-content/plugins/ultimate-product-catalogue/PHPExcel/Classes/PHPExcel.php');
		
		// Build the workbook object out of the uploaded spredsheet
		$inputFileType = PHPExcel_IOFactory::identify($Excel_URL);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objWorkBook = $objReader->load($Excel_URL);
		
		// Create a worksheet object out of the product sheet in the workbook
		$sheet = $objWorkBook->getActiveSheet();
		
		//List of fields that can be accepted via upload
		$Allowed_Fields = array ("Name" => "Item_Name", "Slug" => "Item_Slug", "Description" => "Item_Description", "Price" => "Item_Price", "Image" => "Item_Photo_URL", "Link" => "Item_Link", "Category" => "Category_Name", "Sub-Category" => "SubCategory_Name", "Tags" => "Tags_Names_String", "Catalogue ID" => "Catalogue_ID");
		$Custom_Fields_From_DB = $wpdb->get_results("SELECT Field_ID, Field_Name, Field_Values, Field_Type FROM $fields_table_name");
		if (is_array($Custom_Fields_From_DB)) {
			  foreach ($Custom_Fields_From_DB as $Custom_Field_From_DB) {
						$Allowable_Custom_Fields[$Custom_Field_From_DB->Field_Name] = $Custom_Field_From_DB->Field_Name;
						$Field_IDs[$Custom_Field_From_DB->Field_Name] = $Custom_Field_From_DB->Field_ID;
				}
		}
		
		// Get column names
		$highestColumn = $sheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);	
		for ($column = 0; $column < $highestColumnIndex; $column++) {
				$Titles[$column] = trim($sheet->getCellByColumnAndRow($column, 1)->getValue());
		}

		// Make sure all columns are acceptable based on the acceptable fields above
		foreach ($Titles as $key => $Title) {
				if ($Title != "" and !array_key_exists($Title, $Allowed_Fields) and !array_key_exists($Title, $Allowable_Custom_Fields)) {
					  $Error = __("You have a column which is not recognized: ", 'UPCP') . $Title . __(". <br>Please make sure that the column names match the product field labels exactly.", 'UPCP');
						$user_update = array("Message_Type" => "Error", "Message" => $Error);
						return $user_update;
				}
				if ($Title == "") {
					  $Error = __("You have a blank column that has been edited.<br>Please delete that column and re-upload your spreadsheet.", 'UPCP');
						$user_update = array("Message_Type" => "Error", "Message" => $Error);
						return $user_update;
				}
				if (is_array($Allowable_Custom_Fields)) {
					  if (array_key_exists($Title, $Allowable_Custom_Fields)) {
					  	  $Custom_Fields[$key] = $Title;
								unset($Titles[$key]);
						}
				}
		}
		if (!is_array($Custom_Fields)) {$Custom_Fields = array();}
		
		// Put the spreadsheet data into a multi-dimensional array to facilitate processing
		$highestRow = $sheet->getHighestRow();
		for ($row = 2; $row <= $highestRow; $row++) {
				for ($column = 0; $column < $highestColumnIndex; $column++) {
						$Data[$row][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
				}
		}
		
		$Prod_Count = $wpdb->get_var("SELECT COUNT(*) FROM " . $items_table_name);

		$New_Product_Count = $Prod_Count + sizeOf($Data);
		
		if ($New_Product_Count > 100 and $Full_Version != "Yes") {
			  $Error = __("Maximum number of products (100) for the free version would be exceeded with spreadhseet products. Upgrade to the premium version to continue.", 'UPCP');
				$user_update = array("Message_Type" => "Error", "Message" => $Error);
				return $user_update;
		}

		// Create an array of the categories currently in the UPCP database, 
		// with Category_Name as the key and Category_ID as the value
		$Categories_From_DB = $wpdb->get_results("SELECT * FROM $categories_table_name");
		foreach ($Categories_From_DB as $Category) {
				$Categories[$Category->Category_Name] = $Category->Category_ID;
		}
		
		// Create an array of the sub-categories currently in the UPCP database, 
		// with SubCategory_Name as the key and SubCategory_ID as the value
		$SubCategories_From_DB = $wpdb->get_results("SELECT * FROM $subcategories_table_name");
		foreach ($SubCategories_From_DB as $SubCategory) {
				$SubCategories[$SubCategory->SubCategory_Name] = $SubCategory->SubCategory_ID;
		}
		
		// Create an array of the tags currently in the UPCP database, 
		// with Tag_Name as the key and Tag_ID as the value
		$Tags_From_DB = $wpdb->get_results("SELECT * FROM $tags_table_name");
		foreach ($Tags_From_DB as $Tag) {
				$Tags[$Tag->Tag_Name] = $Tag->Tag_ID;
		}

		// Creates an array of the field names which are going to be inserted into the database
		// and then turns that array into a string so that it can be used in the query
		for ($column = 0; $column < $highestColumnIndex; $column++) {
				if ($Allowed_Fields[$Titles[$column]] != "Tags_Names_String" and $Allowed_Fields[$Titles[$column]] != "Catalogue_ID" and !array_key_exists($column, $Custom_Fields)) {$Fields[] = $Allowed_Fields[$Titles[$column]];}
				if ($Allowed_Fields[$Titles[$column]] == "Category_Name") {$Category_Column = $column; $Fields[] = "Category_ID";}
				if ($Allowed_Fields[$Titles[$column]] == "SubCategory_Name") {$SubCategory_Column = $column; $Fields[] = "SubCategory_ID";}
				if ($Allowed_Fields[$Titles[$column]] == "Tags_Names_String") {$Tags_Column = $column;}
				if ($Allowed_Fields[$Titles[$column]] == "Catalogue_ID") {$Cat_ID_Column = $column;}
		}
		$FieldsString = implode(",", $Fields);
		
		$ShowStatus = "Show";
		$Today = date("Y-m-d H:i:s"); 
		$wpdb->show_errors();
		// Create the query to insert the products one at a time into the database and then run it
		foreach ($Data as $Product) {
				
				// Create an array of the values that are being inserted for each product,
				// add in the values for Category_ID and SubCategory_ID, and increment 
				// the category and sub-category counts when neccessary
				foreach ($Product as $Col_Index => $Value) {
						if ((!isset($Tags_Column) or $Tags_Column != $Col_Index) and (!isset($Cat_ID_Column) or $Cat_ID_Column != $Col_Index) and !array_key_exists($Col_Index, $Custom_Fields)) {$Values[] = esc_sql($Value);}
						if (isset($Category_Column) and $Category_Column == $Col_Index) {
							$Values[] = $Categories[$Value];
							$wpdb->query("UPDATE $categories_table_name SET Category_Item_Count=Category_Item_Count+1 WHERE Category_ID='" . $Categories[$Value] . "'");
						}
						if (isset($SubCategory_Column) and $SubCategory_Column == $Col_Index) {
							$Values[] = $SubCategories[$Value];
							$wpdb->query("UPDATE $subcategories_table_name SET SubCategory_Item_Count=SubCategory_Item_Count+1 WHERE SubCategory_ID='" . $SubCategories[$Value] . "'");
						}
						if (isset($Tags_Column) and $Tags_Column == $Col_Index) {
							$Tags_Names_Array = explode(",", esc_sql($Value));
						}
						if (array_key_exists($Col_Index, $Custom_Fields)) {
							$Custom_Fields_To_Insert[$Custom_Fields[$Col_Index]] = $Value;
						}
						if (isset($Cat_ID_Column) and $Cat_ID_Column == $Col_Index and $Value != "") {
							$Cat_ID = $Value;
						}
				}
				
				$ValuesString = implode("','", $Values);
				$wpdb->query(
					$wpdb->prepare("INSERT INTO $items_table_name (" . $FieldsString . ", Item_Display_Status, Item_Date_Created) VALUES ('" . $ValuesString . "','%s', '%s')", $ShowStatus, $Today)
				);

				$Item_ID = $wpdb->insert_id;
				if (is_array($Tags_Names_Array)) {
					foreach ($Tags_Names_Array as $Tag_Name) {
						$Trimmed_Name = trim($Tag_Name);
						$Tag_ID = $Tags[$Trimmed_Name];
						$wpdb->query($wpdb->prepare("INSERT INTO $tagged_items_table_name (Tag_ID, Item_ID) VALUES (%d, %d)", $Tag_ID, $Item_ID));
						$wpdb->query($wpdb->prepare("UPDATE $tags_table_name SET Tag_Item_Count=Tag_Item_Count WHERE Tag_ID=%d", $Tag_ID));
					}
				}
				
				if (isset($Cat_ID)) {
					  $wpdb->query(
								$wpdb->prepare("INSERT INTO $catalogue_items_table_name (Catalogue_ID, Item_ID) VALUES ('%d','%d')", $Cat_ID, $Item_ID)
						);
				}
				
				if (is_array($Custom_Fields_To_Insert)) {
						foreach ($Custom_Fields_To_Insert as $Field => $Value) {
								$Trimmed_Field = trim($Field);
								$Field_ID = $Field_IDs[$Trimmed_Field];
								$wpdb->query($wpdb->prepare("INSERT INTO $fields_meta_table_name (Field_ID, Item_ID, Meta_Value) VALUES (%d, %d, %s)", $Field_ID, $Item_ID, $Value));
						}
				}

				unset($Values);
				unset($Item_ID);
				unset($ValuesString);
				unset($Tags_Name_Array);
				unset($Custom_Fields_To_Insert);
				unset($Cat_ID);
		}
		
		UPCP_Create_XML_Sitemap();
		
		return __("Products added successfully.", 'UPCP');
}

/* Deletes a single product with a given ID from the UPCP database */
function Delete_UPCP_Product($Item_ID) {
		global $wpdb;
		global $items_table_name;
		global $item_images_table_name;
		global $categories_table_name;
		global $subcategories_table_name;
		global $tags_table_name;
		global $tagged_items_table_name;
        global $catalogue_items_table_name;
		
		// Delete the tagged item in the tagged items table for the given Item_ID
		// and update the Item_Count column in the tags table in the database		
		$Tagged_Items = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Item_ID ."'");
		foreach ($Tagged_Items as $Tagged_Item) {
				$wpdb->query("UPDATE $tags_table_name SET Tag_Item_Count=Tag_Item_Count - 1 WHERE Tag_ID =" . $Tagged_Item->Tag_ID);
		}
		$wpdb->delete(
				$tagged_items_table_name,
				array('Item_ID' => $Item_ID)
		);

        $wpdb->delete(
            $catalogue_items_table_name,
            array('Item_ID' => $Item_ID)
        );
		
		// Decrease the Item_Count column for the category and sub-category tables in the database
		$Current_Product = $wpdb->get_row("SELECT Category_ID, SubCategory_ID FROM $items_table_name WHERE Item_ID='" . $Item_ID ."'");
		if ($Current_Product->Category_ID != 0) {$wpdb->query("UPDATE $categories_table_name SET Category_Item_Count=Category_Item_Count - 1 WHERE Category_ID =" . $Current_Product->Category_ID);}
		if ($Current_Product->SubCategory_ID != 0) {$wpdb->query("UPDATE $subcategories_table_name SET SubCategory_Item_Count=SubCategory_Item_Count - 1 WHERE SubCategory_ID =" . $Current_Product->SubCategory_ID);}
		
		$wpdb->delete(
						$items_table_name,
						array('Item_ID' => $Item_ID)
					);
		
		$wpdb->delete(
						$item_images_table_name,
						array('Item_ID' => $Item_ID)
					);
		
		UPCP_Create_XML_Sitemap();
		
		$update = __("Product has been successfully deleted.", 'UPCP');
		return $update;
}

/* Adds a new product image to for a specified product to the UPCP database */
function Add_Product_Image($Item_ID, $Image_URL, $Image_Description = "") {
		global $wpdb;
		global $item_images_table_name;
		
		$wpdb->query(
							$wpdb->prepare("INSERT INTO $item_images_table_name (Item_ID, Item_Image_URL, Item_Image_Description) VALUES (%d, %s, %s)", $Item_ID, $Image_URL, $Item_Image_Description)
				);
		
		$update = __("Image has been successfully added to the product.", 'UPCP');
		return $update;
}

/* Deletes a single image with a given Item_Image_ID from the UPCP database */
function Delete_Product_Image() {
		global $wpdb;
		global $item_images_table_name;
		
		$wpdb->delete(
						$item_images_table_name,
						array('Item_Image_ID' => $_GET['Item_Image_ID'])
					);

		$update = __("Image has been successfully removed.", 'UPCP');
		return $update;
}

/* Updates the main plugin options in the WordPress database */
function Update_UPCP_Options() {
	global $Full_Version;
	$InstallVersion = get_option("UPCP_First_Install_Version");
	
	update_option('UPCP_Color_Scheme', $_POST['color_scheme']);
	update_option('UPCP_Product_Links', $_POST['product_links']);
	update_option('UPCP_Tag_Logic', $_POST['tag_logic']);
	update_option('UPCP_Filter_Type', $_POST['filter_type']);
	update_option("UPCP_Read_More", $_POST['read_more']);
	update_option("UPCP_Desc_Chars", $_POST['desc_count']);
	update_option("UPCP_Sidebar_Order", $_POST['sidebar_order']);
	update_option("UPCP_Product_Search", $_POST['product_search']);
	$DetailsImageLink = Prepare_Details_Image();
	update_option("UPCP_Details_Image", $DetailsImageLink);
	update_option("UPCP_Single_Page_Price", $_POST['single_page_price']);
	update_option("UPCP_Case_Insensitive_Search", $_POST['case_insensitive_search']);
	update_option("UPCP_Apply_Contents_Filter", $_POST['contents_filter']);
	update_option("UPCP_Maintain_Filtering", $_POST['maintain_filtering']);
	
	if ($InstallVersion <= 2.0 or $Full_Version == "Yes") {update_option("UPCP_Pretty_Links", $_POST['pretty_links']);}
	if ($Full_Version == "Yes") {update_option("UPCP_XML_Sitemap_URL", $_POST['xml_sitemap_url']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Filter_Title", $_POST['filter_title']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Custom_Product_Page", $_POST['custom_product_page']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Products_Per_Page", $_POST['products_per_page']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Pagination_Location", $_POST['pagination_location']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Product_Sort", $_POST['product_sort']);}
	if ($Full_Version == "Yes") {update_option("UPCP_CF_Conversion", $_POST['cf_converion']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Related_Products", $_POST['related_products']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Next_Previous", $_POST['next_previous']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Access_Role", $_POST['access_role']);}
	if ($Full_Version == "Yes") {update_option("UPCP_PP_Grid_Width", $_POST['pp_grid_width']);}
	if ($Full_Version == "Yes") {update_option("UPCP_PP_Grid_Height", $_POST['pp_grid_height']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Top_Bottom_Padding", $_POST['pp_top_bottom_padding']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Left_Right_Padding", $_POST['pp_left_right_padding']);}
	
	if ($Full_Version == "Yes") {update_option("UPCP_Categories_Label", $_POST['categories_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_SubCategories_Label", $_POST['subcategories_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Tags_Label", $_POST['tags_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Custom_Fields_Label", $_POST['custom_fields_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Details_Label", $_POST['details_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Sort_By_Label", $_POST['sort_by_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Product_Name_Search_Label", $_POST['product_name_search_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Product_Name_Text_Label", $_POST['product_name_text_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Back_To_Catalogue_Label", $_POST['back_to_catalogue']);}
	if ($Full_Version == "Yes") {update_option("UPCP_No_Results_Found_Label", $_POST['no_results_found_label']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Products_Pagination_Label", $_POST['products_pagination_label']);}

	if ($Full_Version == "Yes") {update_option("UPCP_Catalogue_Style", $_POST['catalogue_style']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Sidebar_Style", $_POST['sidebar_style']);}
	if ($Full_Version == "Yes") {update_option("UPCP_Pagination_Style", $_POST['pagination_style']);}
	
	if ($_POST['Pretty_Links'] == "Yes") {
		 update_option("UPCP_Update_RR_Rules", "Yes");
	}
	
	UPCP_Create_XML_Sitemap();
	
	$update = __("Options have been succesfully updated.", 'UPCP');
	return $update;
}

function Restore_Default_PP_Layout() {
		$Product_Page = '[{"element_type":"Product Description<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"description","element_id":"","col":3,"row":9,"size_x":5,"size_y":4},{"element_type":"Back Link<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"back","element_id":"","col":1,"row":1,"size_x":2,"size_y":1},{"element_type":"Additional Images<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"additional_images","element_id":"","col":1,"row":2,"size_x":2,"size_y":9},{"element_type":"Main Image<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"main_image","element_id":"","col":3,"row":3,"size_x":4,"size_y":6},{"element_type":"Permalink<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"product_link","element_id":"","col":6,"row":2,"size_x":1,"size_y":1},{"element_type":"Product Name<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"product_name","element_id":"","col":3,"row":2,"size_x":3,"size_y":1},{"element_type":"Blank<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"blank","element_id":"","col":7,"row":2,"size_x":1,"size_y":7},{"element_type":"Blank<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"blank","element_id":"","col":3,"row":1,"size_x":5,"size_y":1}]';
		
		update_option("UPCP_Product_Page_Serialized", $Product_Page);
		update_option("UPCP_PP_Grid_Width", 90);
		update_option("UPCP_PP_Grid_Height", 35);
		update_option("UPCP_Top_Bottom_Padding", 10);
		update_option("UPCP_Left_Right_Padding", 10);
}

function Restore_Default_PP_Layout_Mobile() {
		$Product_Page = '[{"element_type":"Product Description<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"description","element_id":"","col":3,"row":9,"size_x":5,"size_y":4},{"element_type":"Back Link<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"back","element_id":"","col":1,"row":1,"size_x":2,"size_y":1},{"element_type":"Additional Images<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"additional_images","element_id":"","col":1,"row":2,"size_x":2,"size_y":9},{"element_type":"Main Image<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"main_image","element_id":"","col":3,"row":3,"size_x":4,"size_y":6},{"element_type":"Permalink<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"product_link","element_id":"","col":6,"row":2,"size_x":1,"size_y":1},{"element_type":"Product Name<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"product_name","element_id":"","col":3,"row":2,"size_x":3,"size_y":1},{"element_type":"Blank<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"blank","element_id":"","col":7,"row":2,"size_x":1,"size_y":7},{"element_type":"Blank<div class=\"gs-delete-handle\" onclick=\"remove_element(this);\"></div><span class=\"gs-resize-handle gs-resize-handle-both\"></span>","element_class":"blank","element_id":"","col":3,"row":1,"size_x":5,"size_y":1}]';
		
		update_option("UPCP_Product_Page_Serialized_Mobile", $Product_Page);
}
?>