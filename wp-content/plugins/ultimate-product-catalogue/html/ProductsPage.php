<div id="col-right">
<div class="col-wrap">

<!-- Display a list of the products which have already been created -->
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<?php 
			if (isset($_GET['Page']) and $_GET['DisplayPage'] == "Products") {$Page = $_GET['Page'];}
			else {$Page = 1;}
			
			$Sql = "SELECT * FROM $items_table_name ";
				if (isset($_POST['ItemName'])) {$Sql .= "WHERE Item_Name LIKE '%" . $_POST['ItemName'] . "%' ";}
				if (isset($_GET['OrderBy']) and $_GET['DisplayPage'] == "Products") {$Sql .= "ORDER BY " . $_GET['OrderBy'] . " " . $_GET['Order'] . " ";}
				else {$Sql .= "ORDER BY Item_Date_Created ";}
				$Sql .= "LIMIT " . ($Page - 1)*20 . ",20";
				$myrows = $wpdb->get_results($Sql);
				$TotalProducts = $wpdb->get_results("SELECT Item_ID FROM $items_table_name");
				$num_rows = $wpdb->num_rows; 
				$Number_of_Pages = ceil($num_rows/20);
				echo $Number_Of_Pages;
				$Current_Page_With_Order_By = "admin.php?page=UPCP-options&DisplayPage=Products";
				if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}?>

<form action="admin.php?page=UPCP-options&DisplayPage=Products" method="post"> 
<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">Search Products:</label>
	<input type="search" id="post-search-input" name="ItemName" value="">
	<input type="submit" name="" id="search-submit" class="button" value="Search Products">
</p>  
</form>

<form action="admin.php?page=UPCP-options&Action=UPCP_MassDeleteProducts&DisplayPage=Products" method="post">  
<div class="tablenav top">
		<div class="alignleft actions">
				<select name='action'>
  					<option value='-1' selected='selected'><?php _e("Bulk Actions", 'UPCP') ?></option>
						<option value='delete'><?php _e("Delete", 'UPCP') ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'UPCP') ?>"  />
				<a class='confirm button-secondary action' href='admin.php?page=UPCP-options&Action=UPCP_DeleteAllProducts&DisplayPage=Products'>Delete All Products</a>
		</div>
		<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
				<span class="displaying-num"><?php echo $wpdb->num_rows; ?> <?php _e("items", 'UPCP') ?></span>
				<span class='pagination-links'>
						<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
						<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
						<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'UPCP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
						<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
						<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
				</span>
		</div>
</div>

<table class="wp-list-table widefat fixed tags sorttable" cellspacing="0">
		<thead>
				<tr>
						<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
								<input type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name sortable desc'  style="">
										<?php if ($_GET['OrderBy'] == "Item_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Name&Order=ASC'>";} ?>
											  <span><?php _e("Name", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='description' class='manage-column column-description sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Description" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Description&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Description&Order=ASC'>";} ?>
											  <span><?php _e("Description", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='requirements' class='manage-column column-requirements sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Price" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Price&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Price&Order=ASC'>";} ?>
											  <span><?php _e("Price", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='users' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Category_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Category_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Category_Name&Order=ASC'>";} ?>
											  <span><?php _e("Category", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='enabled' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "SubCategory_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=SubCategory_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=SubCategory_Name&Order=ASC'>";} ?>
											  <span><?php _e("Sub-Category", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='enabled' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Views" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Views&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Views&Order=ASC'>";} ?>
											  <span><?php _e("# of Views", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
				</tr>
		</thead>

		<tfoot>
				<tr>
						<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
								<input type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name sortable desc'  style="">
										<?php if ($_GET['OrderBy'] == "Item_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Name&Order=ASC'>";} ?>
											  <span><?php _e("Name", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='description' class='manage-column column-description sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Description" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Description&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Description&Order=ASC'>";} ?>
											  <span><?php _e("Description", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='requirements' class='manage-column column-requirements sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Price" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Price&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Price&Order=ASC'>";} ?>
											  <span><?php _e("Price", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='users' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Category_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Category_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Category_Name&Order=ASC'>";} ?>
											  <span><?php _e("Category", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='enabled' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "SubCategory_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=SubCategory_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=SubCategory_Name&Order=ASC'>";} ?>
											  <span><?php _e("Sub-Category", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='enabled' class='manage-column column-users sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Item_Views" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Views&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Products&OrderBy=Item_Views&Order=ASC'>";} ?>
											  <span><?php _e("# of Views", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
				</tr>
		</tfoot>

	<tbody id="the-list" class='list:tag'>
		
		 <?php
				if ($myrows) { 
	  			  foreach ($myrows as $Item) {
								echo "<tr id='Item" . $Item->Item_ID ."'>";
								echo "<th scope='row' class='check-column'>";
								echo "<input type='checkbox' name='Products_Bulk[]' value='" . $Item->Item_ID ."' />";
								echo "</th>";
								echo "<td class='name column-name'>";
								echo "<strong>";
								echo "<a class='row-title' href='admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Item->Item_ID ."' title='Edit " . $Item->Item_Name . "'>" . $Item->Item_Name . "</a></strong>";
								echo "<br />";
								echo "<div class='row-actions'>";
								/*echo "<span class='edit'>";
								echo "<a href='admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Item->Item_ID ."'>Edit</a>";
		 						echo " | </span>";*/
								echo "<span class='delete'>";
								echo "<a class='delete-tag' href='admin.php?page=UPCP-options&Action=UPCP_DeleteProduct&DisplayPage=Products&Item_ID=" . $Item->Item_ID ."'>" . __("Delete", 'UPCP') . "</a>";
		 						echo "</span>";
								echo "</div>";
								echo "<div class='hidden' id='inline_" . $Item->Item_ID ."'>";
								echo "<div class='name'>" . $Item->Item_Name . "</div>";
								echo "</div>";
								echo "</td>";
								echo "<td class='description column-description'>" . strip_tags(substr($Item->Item_Description, 0, 60));
								if (strlen($Item->Item_Description) > 60) {echo "...";}
								echo "</td>";
								echo "<td class='description column-price'>" . $Item->Item_Price . "</td>";
								echo "<td class='users column-category'>" . $Item->Category_Name . "</td>";
								echo "<td class='users column-subcategory'>" . $Item->SubCategory_Name . "</td>";
								echo "<td class='users column-item-views'>" . $Item->Item_Views . "</td>";
								echo "</tr>";
						}
				}
		?>

	</tbody>
</table>

<div class="tablenav bottom">
		<div class="alignleft actions">
				<select name='action'>
  					<option value='-1' selected='selected'><?php _e("Bulk Actions", 'UPCP') ?></option>
						<option value='delete'><?php _e("Delete", 'UPCP') ?></option>
				</select>
				<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'UPCP') ?>"  />
		</div>
		<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
				<span class="displaying-num"><?php echo $wpdb->num_rows; ?> <?php _e("items", 'UPCP') ?></span>
				<span class='pagination-links'>
						<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
						<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
						<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'UPCP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
						<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
						<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
				</span>
		</div>
		<br class="clear" />
</div>
</form>

<br class="clear" />
</div>
</div> <!-- /col-right -->


<!-- Form to upload a list of new products from a spreadsheet -->
<div id="col-left">
<div class="col-wrap">

<div class="form-wrap">
<h2><?php _e("Add New Products", 'UPCP') ?></h2>
<!-- Form to create a new product -->
<h3><?php _e("Add Product Manually", 'UPCP') ?></h3>
<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_AddProduct&DisplayPage=Product" class="validate" enctype="multipart/form-data">
<input type="hidden" name="action" value="Add_Product" />
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>
<div class="form-field form-required">
	<label for="Item_Name"><?php _e("Name", 'UPCP') ?></label>
	<input name="Item_Name" id="Item_Name" type="text" value="" size="60" />
	<p><?php _e("The name of the product your users will see.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Slug"><?php _e("Slug", 'UPCP') ?></label>
	<input name="Item_Slug" id="Item_Slug" type="text" value="" size="60" />
	<p><?php _e("The slug for your product if you use pretty permalinks.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Image"><?php _e("Image", 'UPCP') ?></label>
	<input id="Item_Image" type="text" size="36" name="Item_Image" value="http://" /> 
  <input id="Item_Image_button" class="button" type="button" value="Upload Image" />
	<p><?php _e("The image that will be displayed in association with this product.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Price"><?php _e("Price", 'UPCP') ?></label>
	<input name="Item_Price" id="Item_Price" type="text" value="" size="60" />
	<p><?php _e("What does this product cost?", 'UPCP') ?></p>
</div>
<div class="form-field-small-buttons">
	<label for="Item_Description"><?php _e("Description", 'UPCP') ?></label>
	<?php $settings = array( //'wpautotop' => false,
												 	 'textarea_rows' => 6);																						
				wp_editor("", "Item_Description", $settings); ?>
	<p><?php _e("The description of the product. What is it and what makes it worth getting?", 'UPCP') ?></p>
</div>
<div>
		<label for="Item_Link"><?php _e("Product Link", 'UPCP') ?></label>
		<input name="Item_Link" id="Item_Link" type="text" value="<?php echo $Product->Item_Link;?>" size="60" />
		<p><?php _e("A link that will replace the default product page. Useful if you participate in affiliate programs.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Display_Status"><?php _e("Display Status", 'UPCP') ?></label>
		<label title='Show'><input type='radio' class ='upcp-radio-input' name='Item_Display_Status' value='Show' checked='checked'/> <span>Show</span></label><br />
	<label title='Hide'><input type='radio' class ='upcp-radio-input' name='Item_Display_Status' value='Hide' /> <span>Hide</span></label><br />
	<p><?php _e("Should this item be displayed if it's added to a catalogue?", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Category"><?php _e("Category:", 'UPCP') ?></label>
	<select name="Category_ID" id="Item_Category" onchange="UpdateSubCats();">
	<option value=""></option>
	<?php $Categories = $wpdb->get_results("SELECT * FROM $categories_table_name"); ?>
	<?php foreach ($Categories as $Category) {
						echo "<option value='" . $Category->Category_ID . "' ";
						if ($Category->Category_ID == $Product->Category_ID) {echo "selected='selected'";}
						echo " >" . $Category->Category_Name . "</option>";
				} ?>
	</select>
	<p><?php _e("What category is this product in? Categories help to organize your product catalogues and help your customers to find what they're looking for.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_SubCategory"><?php _e("Sub-Category:", 'UPCP') ?></label>
	<select name="SubCategory_ID" id="Item_SubCategory">
	<option value=""></option>
	<?php if ($Product->Category_ID != "") {
					  $SubCategories = $wpdb->get_results("SELECT * FROM $subcategories_table_name WHERE Category_ID=" . $Product->Category_ID . " ORDER BY SubCategory_Name");
						foreach ($SubCategories as $SubCategory) {
								echo "<option value='" . $SubCategory->SubCategory_ID . "' ";
								if ($SubCategory->SubCategory_ID == $Product->SubCategory_ID) {echo "selected='selected'";}
								echo " >" . $SubCategory->SubCategory_Name . "</option>";
						} 
				} ?>
	</select>
	<p><?php _e("What sub-category is this product in? Sub-categories help to organize your product catalogues and help your customers to find what they're looking for.", 'UPCP') ?></p>
</div>
<div class="form-field">
	<label for="Item_Tags"><?php _e("Tags:", 'UPCP') ?></label>
	<?php $Tagged_Items = $wpdb->get_results("SELECT Tag_ID FROM $tagged_items_table_name WHERE Item_ID='" . $Product->Item_ID ."'"); ?>
	<?php $Tags = $wpdb->get_results("SELECT * FROM $tags_table_name"); ?>
	<?php foreach ($Tags as $Tag) { ?>
			<input type="checkbox" class='upcp-tag-input' name="Tags[]" value="<?php echo $Tag->Tag_ID; ?>" id="Tag-<?php echo $Tag->Tag_Name; ?>" <?php if (in_array($Tag->Tag_ID, $Tagged_Items)) {echo " checked";} ?>>
			<?php echo $Tag->Tag_Name; ?></br>
	<?php } ?>
	<p><?php _e("What tags should this product have? Tags help to describe the attributes of a product.", 'UPCP') ?></p>
</div>

<?php if ($Related_Products != "Manual") {$RelatedDisabled = "disabled";} ?>
<div class="form-field">
	<label for="Item_Related_Products"><?php _e("Related Products", 'UPCP') ?></label>
	<label title='Product ID'></label><input type='text' name='Item_Related_Products_1' value='' <?php echo $RelatedDisabled; ?>/><br />
	<label title='Product ID'></label><input type='text' name='Item_Related_Products_2' value='' <?php echo $RelatedDisabled; ?>/><br />
	<label title='Product ID'></label><input type='text' name='Item_Related_Products_3' value='' <?php echo $RelatedDisabled; ?>/><br />
	<label title='Product ID'></label><input type='text' name='Item_Related_Products_4' value='' <?php echo $RelatedDisabled; ?>/><br />
	<label title='Product ID'></label><input type='text' name='Item_Related_Products_5' value='' <?php echo $RelatedDisabled; ?>/><br />
	<p><?php _e("What products are related to this one? (premium feature, input product IDs)", 'UPCP') ?></p>
</div>

<?php if ($Next_Previous != "Manual") {$NextPreviousDisabled = "disabled";} ?>
<div class="form-field">
	<label for="Item_Related_Products"><?php _e("Next/Previous Products", 'UPCP') ?></label>
	<label title='Product ID'>Next Product ID:</label><input type='text' name='Item_Next_Product' value='' <?php echo $NextPreviousDisabled; ?>/><br />
	<label title='Product ID'>Previous Product ID:</label><input type='text' name='Item_Previous_Product' value='' <?php echo $NextPreviousDisabled; ?>/><br />
	<p><?php _e("What products should be listed as the next/previous products? (premium feature, input product IDs)", 'UPCP') ?></p>
</div>

<?php
$Sql = "SELECT * FROM $fields_table_name ";
$Fields = $wpdb->get_results($Sql);
$Value = "";
foreach ($Fields as $Field) {
		$ReturnString .= "<div class='form-field'><label for='" . $Field->Field_Name . "'>" . $Field->Field_Name . ":</label>";
		if ($Field->Field_Type == "text" or $Field->Field_Type == "mediumint") {
			  $ReturnString .= "<input name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-text-input' type='text' value='" . $Value . "' />";
		}
		elseif ($Field->Field_Type == "textarea") {
				$ReturnString .= "<textarea name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-textarea'>" . $Value . "</textarea>";
		} 
		elseif ($Field->Field_Type == "select") { 
				$Options = explode(",", $Field->Field_Values);
				$ReturnString .= "<select name='" . $Field->Field_Name . "' id='upcp-input-" . $Field->Field_ID . "' class='upcp-select'>";
				foreach ($Options as $Option) {
						$ReturnString .= "<option value='" . $Option . "' ";
						if (trim($Option) == trim($Value)) {$ReturnString .= "selected='selected'";}
						$ReturnString .= ">" . $Option . "</option>";
				}
				$ReturnString .= "</select>";
		} 
		elseif ($Field->Field_Type == "radio") {
				$Counter = 0;
				$Options = explode(",", $Field->Field_Values);
				foreach ($Options as $Option) {
						if ($Counter != 0) {$ReturnString .= "<label class='radio'></label>";}
						$ReturnString .= "<input type='radio' name='" . $Field->Field_Name . "' value='" . $Option . "' class='upcp-radio' ";
						if (trim($Option) == trim($Value)) {$ReturnString .= "checked";}
						$ReturnString .= ">" . $Option;
						$Counter++;
				}
		} 
		elseif ($Field->Field_Type == "checkbox") {
  			$Counter = 0;
				$Options = explode(",", $Field->Field_Values);
				$Values = explode(",", $Value);
				foreach ($Options as $Option) {
						if ($Counter != 0) {$ReturnString .= "<label class='radio'></label>";}
						$ReturnString .= "<input type='checkbox' name='" . $Field->Field_Name . "[]' value='" . $Option . "' class='upcp-checkbox' ";
						if (in_array($Option, $Values)) {$ReturnString .= "checked";}
						$ReturnString .= ">" . $Option . "</br>";
						$Counter++;
				}
		}
		elseif ($Field->Field_Type == "file") {
				$ReturnString .= "<input name='" . $Field->Field_Name . "' class='upcp-file-input' type='file' value='' />";
		}
		elseif ($Field->Field_Type == "date") {
				$ReturnString .= "<input name='" . $Field->Field_Name . "' class='upcp-date-input' type='date' value='' />";
		} 
		elseif ($Field->Field_Type == "datetime") {
				$ReturnString .= "<input name='" . $Field->Field_Name . "' class='upcp-datetime-input' type='datetime-local' value='' />";
  	}
		$ReturnString .= " </div>";
}
echo $ReturnString;

?>

<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Add New Product', 'UPCP') ?>"  /></p></form>

<h3><?php _e("Add Products from Spreadsheet", 'UPCP') ?></h3>
<form id="addtag" method="post" action="admin.php?page=UPCP-options&Action=UPCP_AddProductSpreadsheet&DisplayPage=Product" class="validate" enctype="multipart/form-data">
<div class="form-field form-required">
		<label for="Products_Spreadsheet"><?php _e("Spreadhseet Containing Products", 'UPCP') ?></label>
		<input name="Products_Spreadsheet" id="Products_Spreadsheet" type="file" value=""/>
		<p><?php _e("The spreadsheet containing all of the products you wish to add. Make sure that the column title names are the same as the field names for products (ex: Name, Price, etc.), and that any categories and sub-categories are written exactly the same as they are online.", 'UPCP') ?></p>
</div>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Add New Products', 'UPCP') ?>"  /></p>
</form>
</div>

<br class="clear" />

<h3><?php _e("Export Products to Spreadsheet", 'UPCP') ?></h3>
<?php if ($Full_Version == "Yes") { ?>
	<div class="wrap">

		<form method="post" action="admin.php?page=UPCP-options&Action=UPCP_ExportToExcel">
			<p><?php _e("Downloads all products currently in the database to Excel", 'UPCP') ?></p>
			<p class="submit"><input type="submit" name="Export_Submit" id="submit" class="button button-primary" value="Export to Excel"  /></p>
		</form>
	</div>
<?php } else { ?>
	<div class="Explanation-Div">
		<h2><?php _e("Full Version Required!", 'UPCP') ?></h2>
		<div class="upcp-full-version-explanation">
			<?php _e("The full version of the Ultimate Product Catalogue Plugin is required to export products to Excel.", "UPCP");?><a href="http://www.etoilewebdesign.com/ultimate-product-catalogue-plugin/"><?php _e(" Please upgrade to unlock this feature!", 'UPCP'); ?></a>
		</div>
	</div>
<?php } ?>

<div class='clear'></div>
</div>
</div><!-- /col-left -->		