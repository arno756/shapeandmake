
<!-- The details of a specific product for editing, based on the product ID -->
<?php if ($_GET['Selected'] == "Product") { ?>
	
	<?php $Product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $items_table_name WHERE Item_ID ='%d'", $_GET['Item_ID'])); ?>
	
	<div class="OptionTab ActiveTab" id="EditProduct">
		<div class="form-wrap ItemDetail">
			<a href="admin.php?page=UPCP-options&DisplayPage=Products" class="NoUnderline">&#171; <?php _e("Back", 'UPCP') ?></a>
			<h3>Edit  <?php echo $Product->Item_Name . " (ID:" . $Product->Item_ID . " )"; ?></h3>
			<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_EditProduct&DisplayPage=Products" class="validate" enctype="multipart/form-data">
			<input type="hidden" name="action" value="Edit_Product" />
			<input type="hidden" name="Item_ID" value="<?php echo $Product->Item_ID; ?>" />
			<?php wp_nonce_field(); ?>
			<?php wp_referer_field(); ?>
			<table class="form-table">
			<tr>
				<th><label for="Item_Name"><?php _e("Name", 'UPCP') ?></label></th>
				<td><input name="Item_Name" id="Item_Name" type="text" value="<?php echo $Product->Item_Name;?>" size="60" />
				<p><?php _e("The name of the product your users will see.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Slug"><?php _e("Slug", 'UPCP') ?></label></th>
				<td><input name="Item_Slug" id="Item_Slug" type="text" value="<?php echo $Product->Item_Slug;?>" size="60" />
				<p><?php _e("The slug for your product if you use pretty permalinks.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Image"><?php _e("Image", 'UPCP') ?></label></th>
				<td><input id="Item_Image" type="text" size="36" name="Item_Image" value="<?php echo $Product->Item_Photo_URL;?>" /> 
				<input id="Item_Image_button" class="button" type="button" value="Upload Image" />
				<p><?php _e("The main image that will be displayed in association with this product. Current Image:", 'UPCP') ?><br/><img class="PreviewImage" src="<?php echo $Product->Item_Photo_URL;?>" /></p></td>
			</tr>
			<tr>
				<th><label for="Item_Price"><?php _e("Price", 'UPCP') ?></label></th>
				<td><input name="Item_Price" id="Item_Price" type="text" value="<?php echo $Product->Item_Price;?>" size="60" />
				<p><?php _e("The price of the product.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Description"><?php _e("Description", 'UPCP') ?></label></th>
				<td><?php 
					$settings = array( //'wpautotop' => false,
									'textarea_rows' => 6);																						
					wp_editor($Product->Item_Description, "Item_Description", $settings); ?>
				<p><?php _e("The description of the product. What is it and what makes it worth getting?", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Link"><?php _e("Product Link", 'UPCP') ?></label></th>
				<td><input name="Item_Link" id="Item_Link" type="text" value="<?php echo $Product->Item_Link;?>" size="60" />
				<p><?php _e("A link that will replace the default product page. Useful if you participate in affiliate programs.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Display_Status"><?php _e("Display Status", 'UPCP') ?></label></th>
				<td><label title='Show'><input type='radio' name='Item_Display_Status' value='Show' <?php if($Product->Item_Display_Status == "Show" or $Product->Item_Display_Status == "") {echo "checked='checked'";} ?>/> <span>Show</span></label><br />
				<label title='Hide'><input type='radio' name='Item_Display_Status' value='Hide' <?php if($Product->Item_Display_Status == "Hide") {echo "checked='checked'";} ?>/> <span>Hide</span></label><br />
				<p><?php _e("Should this item be displayed if it's added to a catalogue?", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Category"><?php _e("Category:", 'UPCP') ?></label></th>
				<td><select name="Category_ID" id="Item_Category" onchange="UpdateSubCats();">
					<option value=""></option>
						<?php $Categories = $wpdb->get_results("SELECT * FROM $categories_table_name"); ?>
						<?php foreach ($Categories as $Category) {
						 	echo "<option value='" . $Category->Category_ID . "' ";
							if ($Category->Category_ID == $Product->Category_ID) {echo "selected='selected'";}
							echo " >" . $Category->Category_Name . "</option>";
						} ?>
					</select>
				<p><?php _e("What category is this product in? Categories help to organize your product catalogues and help your customers to find what they're looking for.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_SubCategory"><?php _e("Sub-Category:", 'UPCP') ?></label></th>
				<td><select name="SubCategory_ID" id="Item_SubCategory">
						<option value=""></option>
						<?php $SubCategories = $wpdb->get_results("SELECT * FROM $subcategories_table_name WHERE Category_ID=" . $Product->Category_ID . " ORDER BY SubCategory_Name"); ?>
						<?php foreach ($SubCategories as $SubCategory) {
						 			echo "<option value='" . $SubCategory->SubCategory_ID . "' ";
									if ($SubCategory->SubCategory_ID == $Product->SubCategory_ID) {echo "selected='selected'";}
									echo " >" . $SubCategory->SubCategory_Name . "</option>";
							} ?>
					</select>
				<p><?php _e("What sub-category is this product in? Sub-categories help to organize your product catalogues and help your customers to find what they're looking for.", 'UPCP') ?></p></td>
			</tr>
			<tr>
				<th><label for="Item_Tags"><?php _e("Tags:", 'UPCP') ?></label></th>
				<td>
				<?php $Tagged_Items = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Product->Item_ID ."'");?>
				<?php $Tags = $wpdb->get_results("SELECT * FROM $tags_table_name"); ?>
				<?php foreach ($Tags as $Tag) {
						$Is_Tagged = false;
						foreach ($Tagged_Items as $Tagged_Item) {if ($Tagged_Item->Tag_ID == $Tag->Tag_ID) {$Is_Tagged = true;}}?>
						<input type="checkbox" class='upcp-tag-input' name="Tags[]" value="<?php echo $Tag->Tag_ID; ?>" id="Tag-<?php echo $Tag->Tag_Name; ?>" <?php if ($Is_Tagged) {echo " checked";} ?>>
						<?php echo $Tag->Tag_Name; ?></br>
				<?php } ?>
				<p><?php _e("What tags should this product have? Tags help to describe the attributes of a product.", 'UPCP') ?></p></td>
			</tr>
			
			<?php if ($Related_Products != "Manual") {$RelatedDisabled = "disabled";} ?>
			<?php $Related_Products_Array = explode(",", $Product->Item_Related_Products); ?>
			<tr>
				<th><label for="Item_Related_Products"><?php _e("Related Products", 'UPCP') ?></label></th>
				<td>
				<label title='Product ID'></label><input type='text' name='Item_Related_Products_1' value="<?php echo $Related_Products_Array['0']; ?>" <?php echo $RelatedDisabled; ?>/><br />
				<label title='Product ID'></label><input type='text' name='Item_Related_Products_2' value="<?php echo $Related_Products_Array['1']; ?>" <?php echo $RelatedDisabled; ?>/><br />
				<label title='Product ID'></label><input type='text' name='Item_Related_Products_3' value="<?php echo $Related_Products_Array['2']; ?>" <?php echo $RelatedDisabled; ?>/><br />
				<label title='Product ID'></label><input type='text' name='Item_Related_Products_4' value="<?php echo $Related_Products_Array['3']; ?>" <?php echo $RelatedDisabled; ?>/><br />
				<label title='Product ID'></label><input type='text' name='Item_Related_Products_5' value="<?php echo $Related_Products_Array['4']; ?>" <?php echo $RelatedDisabled; ?>/><br />
				<p><?php _e("What products are related to this one if set to manual related products? (premium feature, input product IDs)", 'UPCP') ?></p>
				</td>
			</tr>
			
			<?php if ($Next_Previous != "Manual") {$NextPreviousDisabled = "disabled";} ?>
			<tr>
				<th><label for="Item_Related_Products"><?php _e("Next/Previous Products", 'UPCP') ?></label></th>
				<td>
				<label title='Product ID'>Next Product ID:</label><input type='text' name='Item_Next_Product' value="<?php echo substr($Product->Item_Next_Previous, 0, strpos($Product->Item_Next_Previous, ',')); ?>" <?php echo $NextPreviousDisabled; ?>/><br />
				<label title='Product ID'>Previous Product ID:</label><input type='text' name='Item_Previous_Product' value="<?php echo substr($Product->Item_Next_Previous, strpos($Product->Item_Next_Previous, ',')+1); ?>" <?php echo $NextPreviousDisabled; ?>/><br />
				<p><?php _e("What products should be listed as the next/previous products? (premium feature, input product IDs)", 'UPCP') ?></p>
				</td>
			</tr>
			
			<?php
			
			$Sql = "SELECT * FROM $fields_table_name ";
			$Fields = $wpdb->get_results($Sql);
			$MetaValues = $wpdb->get_results($wpdb->prepare("SELECT Field_ID, Meta_Value FROM $fields_meta_table_name WHERE Item_ID=%d", $_GET['Item_ID']));
			foreach ($Fields as $Field) {
				$Value = "";
				if (is_array($MetaValues)) {
					foreach ($MetaValues as $Meta) {
						if ($Field->Field_ID == $Meta->Field_ID) {$Value = $Meta->Meta_Value;}
					}
				}
				$ReturnString .= "<tr><th><label for='" . $Field->Field_Name . "'>" . $Field->Field_Name . ":</label></th>";
				if ($Field->Field_Type == "text" or $Field->Field_Type == "mediumint") {
		  		  $ReturnString .= "<td><input name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-text-input' type='text' value='" . $Value . "' /></td>";
				}
				elseif ($Field->Field_Type == "textarea") {
					$ReturnString .= "<td><textarea name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-textarea'>" . $Value . "</textarea></td>";
				} 
				elseif ($Field->Field_Type == "select") { 
					$Options = explode(",", $Field->Field_Values);
					$ReturnString .= "<td><select name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-select'>";
 					foreach ($Options as $Option) {
						$ReturnString .= "<option value='" . $Option . "' ";
						if (trim($Option) == trim($Value)) {$ReturnString .= "selected='selected'";}
						$ReturnString .= ">" . $Option . "</option>";
					}
					$ReturnString .= "</select></td>";
				} 
				elseif ($Field->Field_Type == "radio") {
					$Counter = 0;
					$Options = explode(",", $Field->Field_Values);
					$ReturnString .= "<td>";
					foreach ($Options as $Option) {
						if ($Counter != 0) {$ReturnString .= "<label class='radio'></label>";}
						$ReturnString .= "<input type='radio' name='" . $Field->Field_Name . "' value='" . $Option . "' class='upcp-radio' ";
						if (trim($Option) == trim($Value)) {$ReturnString .= "checked";}
						$ReturnString .= ">" . $Option;
						$Counter++;
					} 
					$ReturnString .= "</td>";
				} 
				elseif ($Field->Field_Type == "checkbox") {
					$Counter = 0;
					$Options = explode(",", $Field->Field_Values);
					$Values = explode(",", $Value);
					$ReturnString .= "<td>";
					foreach ($Options as $Option) {
						if ($Counter != 0) {$ReturnString .= "<label class='radio'></label>";}
						$ReturnString .= "<input type='checkbox' name='" . $Field->Field_Name . "[]' value='" . $Option . "' class='upcp-checkbox' ";
						if (in_array($Option, $Values)) {$ReturnString .= "checked";}
						$ReturnString .= ">" . $Option . "</br>";
						$Counter++;
					}
					$ReturnString .= "</td>";
				}
				elseif ($Field->Field_Type == "file") {
					$ReturnString .= "<td><input name='" . $Field->Field_Name . "' class='upcp-file-input' type='file' value='" . $Value . "' /></td>";
				}
				elseif ($Field->Field_Type == "date") {
					$ReturnString .= "<td><input name='" . $Field->Field_Name . "' class='upcp-date-input' type='date' value='" . $Value . "' /></td>";
				} 
				elseif ($Field->Field_Type == "datetime") {
					$ReturnString .= "<td><input name='" . $Field->Field_Name . "' class='upcp-datetime-input' type='datetime-local' value='" . $Value . "' /></td>";
				}
			}
			echo $ReturnString;
			?>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"  /></p>
			</form>
		</div>
	
		<!-- A form to add additional images for a product, so that they can be viewed in the FancyBox popup -->
		<?php if ($Full_Version == "Yes") { ?>
		<div class="form-wrap ItemImages">
			<h3><?php _e("Add Product Images", 'UPCP') ?></h3>
			<form id="add-image" method="post" action="admin.php?page=UPCP-options&Action=UPCP_AddProductImage&DisplayPage=Products" class="validate" enctype="multipart/form-data">
				<input type="hidden" name="action" value="Add_Product_Image" />
				<input type="hidden" name="Item_ID" value="<?php echo $Product->Item_ID; ?>" />
				<?php wp_nonce_field(); ?>
				<?php wp_referer_field(); ?>
				<table class="form-table">
					<tr>
						<th><label for="Item_Image"><?php _e("Image", 'UPCP') ?></label></th>
						<td><input id="Item_Image_Addt" type="text" size="36" name="Item_Image[]" value="http://" /> 
						<input id="Item_Image_Addt_button" class="button" type="button" value="<?php _e('Upload Image', 'UPCP');?>" />
						<p><?php _e("The secondaries images that will be displayed.", 'UPCP') ?></p></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Add Image"  /></p>
			</form>
			<div class='item-image-preview'>
				<p><?php _e("Current Images:", 'UPCP') ?><p/>
					<?php $Images = $wpdb->get_results("SELECT * FROM $item_images_table_name WHERE Item_ID='" . $Product->Item_ID . "'"); 
					foreach ($Images as $Image) { ?>
						<div class="item-image">
							<img class="PreviewImage" src="<?php echo $Image->Item_Image_URL;?>" />
							<a href="admin.php?page=UPCP-options&Action=UPCP_DeleteProductImage&DisplayPage=Products&Item_Image_ID=<?php echo $Image->Item_Image_ID; ?>"><?php _e("Delete", 'UPCP') ?></a>
						</div>
					<?php } ?>
			</div>
		</div>
		<?php } else { ?>
			<div class="Explanation-Div">
				<h2><?php _e("Full Version Required!", 'UPCP') ?></h2>
				<div class="upcp-full-version-explanation">
					<?php _e("The full version of the Ultimate Product Catalogue Plugin is required to additional product images.", "UPCP");?><a href="http://www.etoilewebdesign.com/ultimate-product-catalogue-plugin/"><?php _e(" Please upgrade to unlock this page!", 'UPCP'); ?></a>
				</div>
			</div>
		<?php } ?>
	</div>

<!-- The details of a specific category for editing, based on the product ID -->		
<?php } elseif ($_GET['Selected'] == "Category") { ?>
		
		<?php $Category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $categories_table_name WHERE Category_ID ='%d'", $_GET['Category_ID'])); ?>
		
		<div class="OptionTab ActiveTab" id="EditCategory">
				
				<div id="col-right">
				<div class="col-wrap">
				<div id="add-page" class="postbox metabox-holder" >
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Products in Category", 'UPCP') ?></span></h3>
				<div class="inside">
				<div id="posttype-page" class="posttypediv">

				<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
				<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php $Products = $wpdb->get_results($wpdb->prepare("SELECT Item_ID, Item_Name FROM $items_table_name WHERE Category_ID='%d'", $_GET['Category_ID']));
							foreach ($Products as $Product) {
									echo "<li><label class='menu-item-title'><a href='admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Product->Item_ID . "'>" . $Product->Item_Name . "</a></label></li>";
							}
				?>
				</ul>
				</div><!-- /.tabs-panel -->
				</div><!-- /.posttypediv -->
				</div>
				</div>
				</div>
				</div><!-- col-right -->
				
				<div id="col-left">
				<div class="col-wrap">
				<div class="form-wrap CategoryDetail">
						<a href="admin.php?page=UPCP-options&DisplayPage=Categories" class="NoUnderline">&#171; <?php _e("Back", 'UPCP') ?></a>
						<h3>Edit <?php echo $Category->Category_Name;echo" (ID:";echo $Category->Category_ID;echo " )";?></h3>
						<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_EditCategory&DisplayPage=Categories" class="validate" enctype="multipart/form-data">
						<input type="hidden" name="action" value="Edit_Category" />
						<input type="hidden" name="Category_ID" value="<?php echo $Category->Category_ID; ?>" />
						<?php wp_nonce_field(); ?>
						<?php wp_referer_field(); ?>
						<div class="form-field">
								<label for="Category_Name"><?php _e("Name", 'UPCP') ?></label>
								<input name="Category_Name" id="Category_Name" type="text" value="<?php echo $Category->Category_Name;?>" size="60" />
								<p><?php _e("The name of the category your users will see and search for.", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Category_Description"><?php _e("Description", 'UPCP') ?></label>
								<textarea name="Category_Description" id="Category_Description" rows="5" cols="40"><?php echo $Category->Category_Description;?></textarea>
								<p><?php _e("The description of the category. What products are included in this?", 'UPCP') ?></p>
						</div>

						<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'UPCP') ?>" /></p>
						</form>
				</div>
				</div>
				</div>
			
		</div>

<!-- Catalogues are complicated to edit, so we pass that off to a different page in the HTML folder -->		
<?php } elseif ($_GET['Selected'] == "Catalogue") { ?>
		
		<?php include "CatalogueDetails.php"; ?>

<!-- The details of a specific sub-category for editing, based on the product ID -->		
<?php } elseif ($_GET['Selected'] == "SubCategory") { ?>
		
		<?php $SubCategory = $wpdb->get_row($wpdb->prepare("SELECT * FROM $subcategories_table_name WHERE SubCategory_ID ='%d'", $_GET['SubCategory_ID'])); ?>
		
		<div class="OptionTab ActiveTab" id="EditSubCategory">
				
				<div id="col-right">
				<div class="col-wrap">
				<div id="add-page" class="postbox metabox-holder" >
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Products in Sub-Category", 'UPCP') ?></span></h3>
				<div class="inside">
				<div id="posttype-page" class="posttypediv">

				<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
				<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php $Products = $wpdb->get_results($wpdb->prepare("SELECT Item_ID, Item_Name FROM $items_table_name WHERE SubCategory_ID='%d'", $_GET['SubCategory_ID']));
							foreach ($Products as $Product) {
									echo "<li><label class='menu-item-title'><a href='admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Product->Item_ID . "'>" . $Product->Item_Name . "</a></label></li>";
							}
				?>
				</ul>
				</div><!-- /.tabs-panel -->
				</div><!-- /.posttypediv -->
				</div>
				</div>
				</div>
				</div><!-- col-right -->
				
				<div id="col-left">
				<div class="col-wrap">
				<div class="form-wrap SubCategoryDetail">
						<a href="admin.php?page=UPCP-options&DisplayPage=SubCategories" class="NoUnderline">&#171; <?php _e("Back", 'UPCP')?></a>
						<h3>Edit  <?php echo $SubCategory->SubCategory_Name; echo "( ID: "; echo $SubCategory->SubCategory_ID; echo " )"; ?></h3>
						<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_EditSubCategory&DisplayPage=SubCategories" class="validate" enctype="multipart/form-data">
						<input type="hidden" name="action" value="Edit_SubCategory" />
						<input type="hidden" name="SubCategory_ID" value="<?php echo $SubCategory->SubCategory_ID; ?>" />
						<?php wp_nonce_field(); ?>
						<?php wp_referer_field(); ?>
						<div class='form-field'>
								<label for="SubCategory_Name"><?php _e("Name", 'UPCP') ?></label>
								<input name="SubCategory_Name" id="SubCategory_Name" type="text" value="<?php echo $SubCategory->SubCategory_Name;?>" size="60" />
								<p><?php _e("The name of the sub-category your users will see and search for.", 'UPCP') ?></p>
						</div>
						<div class='form-field'>
								<label for="Item_Category"><?php _e("Category:", 'UPCP') ?></label>
								<select name="Category_ID" id="Item_Category">
												<option value=""></option>
												<?php $Categories = $wpdb->get_results("SELECT * FROM $categories_table_name"); ?>
												<?php foreach ($Categories as $Category) {
												echo "<option value='" . $Category->Category_ID . "' ";
												if ($Category->Category_ID == $SubCategory->Category_ID) {echo "selected='selected'";}
												echo " >" . $Category->Category_Name . "</option>";
												} ?>
										</select>
										<p><?php _e("What category is this product in? Categories help to organize your product catalogues and help your customers to find what they're looking for.", 'UPCP') ?></p>
						</div>
						<div class='form-field'>
								<label for="SubCategory_Description"><?php _e("Description", 'UPCP') ?></label>
								<textarea name="SubCategory_Description" id="SubCategory_Description" rows="5" cols="40"><?php echo $SubCategory->SubCategory_Description;?></textarea>
								<p><?php _e("The description of the sub-category. What products are included in this?", 'UPCP') ?></p>
						</div>

						<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'UPCP') ?>"  /></p>
						</form>
				</div>
				</div>
				</div>
		</div>
		
<!-- The details of a specific tag for editing, based on the product ID -->		
<?php } elseif ($_GET['Selected'] == "Tag") { ?>
		
		<?php $Tag = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tags_table_name WHERE Tag_ID ='%d'", $_GET['Tag_ID'])); ?>
		
		<div class="OptionTab ActiveTab" id="EditTag">
				
				<div id="col-right">
				<div class="col-wrap">
				<div id="add-page" class="postbox metabox-holder" >
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Products in Tag", 'UPCP') ?></span></h3>
				<div class="inside">
				<div id="posttype-page" class="posttypediv">

				<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
				<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php $Tagged_Items = $wpdb->get_results($wpdb->prepare("SELECT Item_ID FROM $tagged_items_table_name WHERE Tag_ID='%d'", $_GET['Tag_ID']));
							foreach ($Tagged_Items as $Tagged_Item) {
									$Product = $wpdb->get_row("SELECT Item_ID, Item_Name FROM $items_table_name WHERE Item_ID=" . $Tagged_Item->Item_ID);
									echo "<li><label class='menu-item-title'><a href='admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Product->Item_ID . "'>" . $Product->Item_Name . "</a></label></li>";
							}
				?>
				</ul>
				</div><!-- /.tabs-panel -->
				</div><!-- /.posttypediv -->
				</div>
				</div>
				</div>
				</div><!-- col-right -->
				
				<div id="col-left">
				<div class="col-wrap">
				<div class="form-wrap TagDetail">
						<a href="admin.php?page=UPCP-options&DisplayPage=Tags" class="NoUnderline">&#171; <?php _e("Back", 'UPCP') ?></a>
						<h3>Edit  <?php echo $Tag->Tag_Name; echo"( ID:"; echo $Tag->Tag_ID; echo" )";?></h3>
						<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_EditTag&DisplayPage=Tags" class="validate" enctype="multipart/form-data">
						<input type="hidden" name="action" value="Edit_Tag" />
						<input type="hidden" name="Tag_ID" value="<?php echo $Tag->Tag_ID; ?>" />
						<?php wp_nonce_field(); ?>
						<?php wp_referer_field(); ?>
						<div class='form-field'>
								<label for="Tag_Name"><?php _e("Name", 'UPCP') ?></label>
								<input name="Tag_Name" id="Tag_Name" type="text" value="<?php echo $Tag->Tag_Name;?>" size="60" />
								<p><?php _e("The name of the tag your users will see and search for.", 'UPCP') ?></p>
						</div>
						<div class='form-field'>
								<label for="Tag_Description"><?php _e("Description", 'UPCP') ?></label>
								<textarea name="Tag_Description" id="Tag_Description" rows="5" cols="40"><?php echo $Tag->Tag_Description;?></textarea>
								<p><?php _e("The description of the tag. What products are included in this?", 'UPCP') ?></p>
						</div>

						<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'UPCP') ?>"  /></p>
						</form>
				</div>
				</div>
				</div>
		</div>
		
<?php } elseif ($_GET['Selected'] == "CustomField") { ?>
		
		<?php $Field = $wpdb->get_row($wpdb->prepare("SELECT * FROM $fields_table_name WHERE Field_ID ='%d'", $_GET['Field_ID'])); ?>
		
		<div class="OptionTab ActiveTab" id="EditCustomField">
				
				<div id="col-left">
				<div class="col-wrap">
				<div class="form-wrap TagDetail">
						<a href="admin.php?page=UPCP-options&DisplayPage=Tags" class="NoUnderline">&#171; <?php _e("Back", 'UPCP') ?></a>
						<h3>Edit <?php echo $Field->Field_Name; echo "( ID: "; echo $Field->Field_ID; echo" )"; ?></h3>
						<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_EditCustomField&DisplayPage=CustomFields" class="validate" enctype="multipart/form-data">
						<input type="hidden" name="action" value="Edit_Custom_Field" />
						<input type="hidden" name="Field_ID" value="<?php echo $Field->Field_ID; ?>" />
						<?php wp_nonce_field(); ?>
						<?php wp_referer_field(); ?>
						<div class="form-field form-required">
								<label for="Field_Name"><?php _e("Name", 'UPCP') ?></label>
								<input name="Field_Name" id="Field_Name" type="text" value="<?php echo $Field->Field_Name;?>" size="60" />
								<p><?php _e("The name of the field you will see.", 'UPCP') ?></p>
						</div>
						<div class="form-field form-required">
								<label for="Field_Slug"><?php _e("Slug", 'UPCP') ?></label>
								<input name="Field_Slug" id="Field_Slug" type="text" value="<?php echo $Field->Field_Slug;?>" size="60" />
								<p><?php _e("An all-lowercase name that will be used to insert the field.", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Field_Type"><?php _e("Type", 'UPCP') ?></label>
								<select name="Field_Type" id="Field_Type">
										<option value='text' <?php if ($Field->Field_Type == "text") {echo "selected=selected";} ?>><?php _e("Short Text", 'UPCP') ?></option>
										<option value='mediumint' <?php if ($Field->Field_Type == "mediumint") {echo "selected=selected";} ?>><?php _e("Integer", 'UPCP') ?></option>
										<option value='select' <?php if ($Field->Field_Type == "select") {echo "selected=selected";} ?>><?php _e("Select Box", 'UPCP') ?></option>
										<option value='radio' <?php if ($Field->Field_Type == "radio") {echo "selected=selected";} ?>><?php _e("Radio Button", 'UPCP') ?></option>
										<option value='checkbox' <?php if ($Field->Field_Type == "checkbox") {echo "selected=selected";} ?>><?php _e("Checkbox", 'UPCP') ?></option>
										<option value='textarea' <?php if ($Field->Field_Type == "textarea") {echo "selected=selected";} ?>><?php _e("Text Area", 'UPCP') ?></option>
										<option value='file' <?php if ($Field->Field_Type == "file") {echo "selected=selected";} ?>><?php _e("File", 'UPCP') ?></option>
										<option value='date' <?php if ($Field->Field_Type == "date") {echo "selected=selected";} ?>><?php _e("Date", 'UPCP') ?></option>
										<option value='datetime' <?php if ($Field->Field_Type == "datetime") {echo "selected=selected";} ?>><?php _e("Date/Time", 'UPCP') ?></option>
								</select>
								<p><?php _e("The input method for the field and type of data that the field will hold.", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Field_Description"><?php _e("Description", 'UPCP') ?></label>
								<textarea name="Field_Description" id="Field_Description" rows="2" cols="40"><?php echo $Field->Field_Description;?></textarea>
								<p><?php _e("The description of the field, which you will see as the instruction for the field.", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Field_Values"><?php _e("Input Values", 'UPCP') ?></label>
								<input name="Field_Values" id="Field_Values" type="text" value="<?php echo $Field->Field_Values;?>"  size="60" />
								<p><?php _e("A comma-separated list of acceptable input values for this field. These values will be the options for select, checkbox, and radio inputs. All values will be accepted if left blank.", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Field_Displays"><?php _e("Display?", 'UPCP') ?></label>
								<select name="Field_Displays" id="Field_Displays">
										<option value='none' <?php if ($Field->Field_Displays == "none") {echo "selected=selected";} ?>><?php _e("None", 'UPCP') ?></option>
										<option value='thumbs' <?php if ($Field->Field_Displays == "thumbs") {echo "selected=selected";} ?>><?php _e("Thumbnail View", 'UPCP') ?></option>
										<option value='details' <?php if ($Field->Field_Displays == "details") {echo "selected=selected";} ?>><?php _e("Details View", 'UPCP') ?></option>
										<option value='both' <?php if ($Field->Field_Displays == "both") {echo "selected=selected";} ?>><?php _e("Both", 'UPCP') ?></option>
								</select>
								<p><?php _e("Should this field be displayed in any of the main catalogue pages?", 'UPCP') ?></p>
						</div>
						<div class="form-field">
								<label for="Field_Searchable"><?php _e("Searchable?", 'UPCP') ?></label>
								<select name="Field_Searchable" id="Field_Searchable">
										<option value='No' <?php if ($Field->Field_Searchable == "No") {echo "selected=selected";} ?>><?php _e("No", 'UPCP') ?></option>
										<option value='Yes' <?php if ($Field->Field_Searchable == "Yes") {echo "selected=selected";} ?>><?php _e("Yes", 'UPCP') ?></option>
								</select>
								<p><?php _e("Should this field be searchable in your catalogues?", 'UPCP') ?></p>
						</div>

						<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes', 'UPCP') ?>"  /></p>
						</form>
				</div>
				</div>
				</div>
		</div>
		
<?php } ?>	