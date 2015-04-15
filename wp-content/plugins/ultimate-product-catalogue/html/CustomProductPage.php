<?php if ($Full_Version == "Yes") { 

$PP_Grid_Width = get_option("UPCP_PP_Grid_Width");
$PP_Grid_Height = get_option("UPCP_PP_Grid_Height");
$Top_Bottom_Padding = get_option("UPCP_Top_Bottom_Padding");
$Left_Right_Padding = get_option("UPCP_Left_Right_Padding");

echo "<script language='JavaScript' type='text/javascript'>";
echo "var pp_grid_width = " . $PP_Grid_Width . ";";
echo "var pp_grid_height = " . $PP_Grid_Height . ";";
echo "var pp_top_bottom_padding = " . $Top_Bottom_Padding . ";";
echo "var pp_left_right_padding = " . $Left_Right_Padding . ";";

if ($_GET['CPP_Mobile'] == "mobile") {echo "var grid_type = 'mobile';";}
else {echo "var grid_type = 'regular';";}
echo "</script>";
?>		
	<div id="side-sortables" class="metabox-holder ">
		<div id="cpp-message" class="postbox " >
			<div class="handlediv" title="Click to toggle"></div><h3 class='hndle'><span><?php _e("Feature Update", 'UPCP') ?></span></h3>
			<div class="inside">
					<?php _e("Some users have reported problems using the admin area functions of this feature with FireFox and IE browsers. No issues reported yet with Chrome, or with any browser on the visitor's side.", 'UPCP'); ?>
			</div>
		</div>
	</div>
	
	<!-- Create the form to edit the basic catalogue details -->
	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">
		<div id="side-sortables" class="meta-box-sortables">
		<div id='pp-select' class='pp-select'>
			<h3>Selected Layout:</h3>
			<select name='PP-type-select' id='PP-type-select' onchange='Reload_PP_Page()'>
					<option value='regular' <?php if ($_GET['CPP_Mobile'] == "regular") {echo "selected=selected";} ?>>Regular</option>
					<option value='mobile' <?php if ($_GET['CPP_Mobile'] == "mobile") {echo "selected=selected";} ?>>Mobile</option>
			</select>
		</div>
			
<!-- Create a box with a form that users can add products to the catalogue with -->
<div id="add-page" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Basic Elements", 'UPCP') ?></span></h3>
<div class="inside">
	<div id="posttype-page" class="posttypediv">

		<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
			<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php $BasicElements =  array( array('name' => "Additional Images",'class' => 'additional_images', 'id'=> '', 'x-size' => 2,'y-size' => 8),
							 					array('name' => "Back", 'class' => 'back', 'id'=> '','x-size' => 2,'y-size' => 1),
												array('name' => "Blank", 'class' => 'blank', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Category", 'class' => 'category', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Category Label", 'class' => 'category_label', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Description", 'class' => 'description', 'id'=> '','x-size' => 5,'y-size' => 4),
												array('name' => "Main Image",'class' => 'main_image', 'id'=> '','x-size' => 4,'y-size' => 6),
												array('name' => "Next/Previous", 'class' => 'next_previous', 'id'=> '','x-size' => 2,'y-size' => 3),
												array('name' => "Price", 'class' => 'price', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Price Label", 'class' => 'price_label', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Product Link", 'class' => 'product_link', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Product Name", 'class' => 'product_name', 'id'=> '','x-size' => 3,'y-size' => 1),
												array('name' => "Related Products", 'class' => 'related_products', 'id'=> '','x-size' => 5,'y-size' => 3),
												array('name' => "Sub-Category", 'class' => 'subcategory', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Sub-Category Label", 'class' => 'subcategory_label', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Tags", 'class' => 'tags', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Tags Label", 'class' => 'tags_label', 'id'=> '','x-size' => 1,'y-size' => 1),
												array('name' => "Text", 'class' => 'text', 'id'=> '','x-size' => 2,'y-size' => 2));
						foreach ($BasicElements as $Element) {
							echo "<li><a href='#' onclick='add_element(\"" . $Element['name'] . "\",\"" . $Element['class'] . "\",\"" . $Element['id'] . "\", " . $Element['x-size'] . ", " . $Element['y-size'] . "); return false;'>" . $Element['name'] . "</a></li>";
						}
				?>
			</ul>
		</div><!-- /.tabs-panel -->

	</div><!-- /.posttypediv -->
</div>

<!-- Create a box with a form that users can add categories to the catalogue with -->
<div id="add-page" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Custom Fields", 'UPCP') ?></span></h3>
<div class="inside">
	<div id="posttype-page" class="posttypediv">

		<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
			<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php $Fields = $wpdb->get_results("SELECT * FROM $fields_table_name ORDER BY Field_Name"); 
						foreach ($Fields as $Field) {
							echo "<li><a href='#' onclick='add_element(\"" . $Field->Field_Name . "\",\"custom_field\",\"" . $Field->Field_ID . "\", 1, 1); return false;'>" . $Field->Field_Name . "</a></li>";
							echo "<li><a href='#' onclick='add_element(\"" . $Field->Field_Name . " Label\",\"custom_label\",\"" . $Field->Field_ID . "\", 1, 1); return false;'>" . $Field->Field_Name . " Label</a></li>";
						}
				?>
			</ul>
		</div><!-- /.tabs-panel -->

	</div><!-- /.posttypediv -->
	</div>
</div>
</div>
</div>
<?php if ($_GET['CPP_Mobile'] == "mobile") { ?>
<button value='Save Grid' id='gridster-button-mobile'>Save Layout</button>
<a class='confirm-restore' href='admin.php?page=UPCP-options&Action=UPCP_RestoreDefaultPPLayoutMobile&DisplayPage=ProductPage'><button id='gridster-reset'>Restore Default</button></a>
<?php } else { ?>
<button value='Save Grid' id='gridster-button'>Save Layout</button>
<a class='confirm-restore' href='admin.php?page=UPCP-options&Action=UPCP_RestoreDefaultPPLayout&DisplayPage=ProductPage'><button id='gridster-reset-mobile'>Restore Default</button></a>
<?php } ?>
</div><!-- /#menu-settings-column -->
			
<!-- Show the products and categories currently in the catalogue, give the user the
     option of deleting them or switching the order around -->

  <div class="wrapper gridster">
      <ul>
        <?php if ($_GET['CPP_Mobile'] == "mobile") {$UPCP_Product_Page_Serialized = get_option("UPCP_Product_Page_Serialized_Mobile");}
				else {$UPCP_Product_Page_Serialized = get_option("UPCP_Product_Page_Serialized");}
				if (strpos($UPCP_Product_Page_Serialized, "class=\\\\") !== FALSE){$Gridster = json_decode(stripslashes($UPCP_Product_Page_Serialized));}
				else {$Gridster = json_decode($UPCP_Product_Page_Serialized);}
				if (is_array($Gridster)) {
					foreach ($Gridster as $Element) {
						echo "<li data-col='" . $Element->col . "' data-row='" . $Element->row . "' data-sizex='" . $Element->size_x . "' data-sizey='" . $Element->size_y . "'  data-elementclass='" . $Element->element_class . "' data-elementid='" . $Element->element_id . "' class='prod-page-div gs-w' style='display: list-item; position:absolute;'>";
						echo substr($Element->element_type, 0, strpos($Element->element_type, '<'));
						echo "<div class='gs-delete-handle' onclick='remove_element(this);'></div>";
						if ($Element->element_class == "text") {echo "<textarea onkeyup='UPCP_Page_Builder_UpdateID(this);' class='upcp-pb-textarea'>" . $Element->element_id . "</textarea>";}
						/*echo "<div>Col: " . $Element->col . "<br />";
						echo "Row: " . $Element->row . "<br />";
						echo "Size X: " . $Element->size_x . "<br />";
						echo "Size Y: " . $Element->size_y . "<br />";
						echo "Class: " . $Element->element_class . "<br />";
						echo "ID: " . $Element->element_id . "<br /></div>";*/
						echo "</li>";
					}
				}
		?>
      </ul>
  </div>
							

				</div>
				
<?php } else { ?>
<div class="Info-Div">
	<h2><?php _e("Full Version Required!", 'UPCP') ?></h2>
	<div class="upcp-full-version-explanation">
		<?php _e("The full version of the Ultimate Product Catalogue Plugin is required to use tags.", "UPCP");?><a href="http://www.etoilewebdesign.com/ultimate-product-catalogue-plugin/"><?php _e(" Please upgrade to unlock this page!", 'UPCP'); ?></a>
	</div>
</div>

<div id="side-sortables" class="metabox-holder ">
	<div id="cpp-message" class="postbox " >
		<div class="handlediv" title="Click to toggle"></div><h3 class='hndle'><span><?php _e("Feature Update", 'UPCP') ?></span></h3>
		<div class="inside">
			<?php _e("Some users have reported problems using this feature with FireFox and IE browsers. No issues reported yet with Chrome.", 'UPCP'); ?>
		</div>
	</div>
</div>
<?php } ?> 