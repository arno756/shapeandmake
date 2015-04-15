<!-- Upgrade to pro link box -->
<?php if ($Full_Version != "Yes") { ?>
<div id="side-sortables" class="metabox-holder ">
<div id="upcp_pro" class="postbox " >
		<div class="handlediv" title="Click to toggle"></div><h3 class='hndle'><span><?php _e("Full Version", 'UPCP') ?></span></h3>
		<div class="inside">
				<ul><li><a href="http://www.etoilewebdesign.com/ultimate-product-catalogue-plugin/"><?php _e("Upgrade to the full version ", "UPCP"); ?></a><?php _e("to take advantage of all the available features of the Ultimate Product Catalogue for Wordpress!", 'UPCP'); ?></li>
				<div class="full-version-form-div">
						<form action="admin.php?page=UPCP-options" method="post">
								<div class="form-field form-required">
										<label for="Catalogue_Name"><?php _e("Product Key", 'UPCP') ?></label>
										<input name="Key" type="text" value="" size="40" />
								</div>							
								<input type="submit" name="Upgrade_To_Full" value="<?php _e('Upgrade', 'UPCP') ?>">
						</form>
				</div>
		</div>
</div>
</div>
<?php } ?>
<?php/* echo get_option('plugin_error');*/?>
<?php if (get_option("UPCP_Update_Flag") == "Yes" or get_option("UPCP_Install_Flag") == "Yes") {?>
					<div id="side-sortables" class="metabox-holder ">
							<div id="upcp_pro" class="postbox " >
									<div class="handlediv" title="Click to toggle"></div>
									<h3 class='hndle'><span><?php _e("Thank You!", 'UPCP') ?></span></h3>
							 		<div class="inside">
											<?php /* if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw'><?php _e("Subscribe to our YouTube channel ", "UPCP"); ?></a> <?php _e("for tutorial videos on this and our other plugins!", "UPCP");?> </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 2.4.21!", "UPCP"); ?><br> <a href='https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw'><?php _e("Subscribe to our YouTube channel ", "UPCP"); ?></a> <?php _e("for tutorial videos on this and our other plugins!", "UPCP");?> </li></ul><?php } */ ?>
											
											<?php /* if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?> </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 2.2.9!", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?> </li></ul><?php } */ ?>
											
											<?php  if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?>  </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.0.16!", "UPCP"); ?><br> <a href='http://wordpress.org/support/view/plugin-reviews/ultimate-product-catalogue'><?php _e("Please rate our plugin", "UPCP"); ?></a> <?php _e("if you find the Ultimate Product Catalogue Plugin useful!", "UPCP");?> </li></ul><?php }  ?>
											
											<?php /* if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?>  </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.0.9!", "UPCP"); ?><br> <a href='http://wordpress.org/plugins/order-tracking/'><?php _e("Try out order tracking plugin ", "UPCP"); ?></a> <?php _e("if you ship orders and find the Ultimate Product Catalogue Plugin useful!", "UPCP");?> </li></ul><?php } */ ?>

											<?php /* if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?>  </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 2.3.9!", "UPCP"); ?><br> <a href='http://wordpress.org/support/topic/error-hunt'><?php _e("Please let us know about any small display/functionality errors. ", "UPCP"); ?></a> <?php _e("We've noticed a couple, and would like to eliminate as many as possible.", "UPCP");?> </li></ul><?php } */ ?>
											
											<?php /* if (get_option("UPCP_Install_Flag") == "Yes") { ?><ul><li><?php _e("Thanks for installing the Ultimate Product Catalogue Plugin.", "UPCP"); ?><br> <a href='https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw'><?php _e("Check out our YouTube channel ", "UPCP"); ?></a> <?php _e("for tutorial videos on this and our other plugins!", "UPCP");?> </li></ul>
											<?php } elseif ($Full_Version == "Yes") { ?><ul><li><?php _e("Thanks for upgrading to version 2.6!", "UPCP"); ?><br> <a href='http://www.facebook.com/EtoileWebDesign'><?php _e("Follow us on Facebook", "UPCP"); ?></a> <?php _e("to suggest new features or hear about upcoming ones!", "UPCP");?> </li></ul>
											<?php } else { ?><ul><li><?php _e("Thanks for upgrading to version 3.0!", "UPCP"); ?><br> <?php _e("Love the plugin but don't need the premium version? Help us speed up product support and development by donating. Thanks for using the plugin!", "UPCP");?>
																	 <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
																	 <input type="hidden" name="cmd" value="_s-xclick">
																	 <input type="hidden" name="hosted_button_id" value="AQLMJFJ62GEFJ">
																	 <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
																	 <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
																	 </form>
																	 </li></ul>
											<?php } */ ?>

									</div>
							</div>
					</div>
		<?php 
		update_option('UPCP_Update_Flag', "No");
		update_option('UPCP_Install_Flag', "No");  
		} ?>

<!-- List of the catalogues which have already been created -->
<div id="col-right">
<div class="col-wrap">

<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<?php 
			if (isset($_GET['Page'])) {$Page = $_GET['Page'];}
			else {$Page = 1;}
			
			$Sql = "SELECT * FROM $catalogues_table_name ";
				if (isset($_GET['OrderBy']) and $_GET['DisplayPage'] == "Dashboard") {$Sql .= "ORDER BY " . $_GET['OrderBy'] . " " . $_GET['Order'] . " ";}
				else {$Sql .= "ORDER BY Catalogue_Name ";}
				$Sql .= "LIMIT " . ($Page - 1)*20 . ",20";
				$myrows = $wpdb->get_results($Sql);
				$TotalProducts = $wpdb->get_results("SELECT Catalogue_ID FROM $catalogues_table_name");
				$num_rows = $wpdb->num_rows; 
				$Number_of_Pages = ceil($wpdb->num_rows/20);
				$Current_Page_With_Order_By = "admin.php?page=UPCP-options&DisplayPage=Dashboard";
				if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}?>

<form action="admin.php?page=UPCP-options&Action=UPCP_MassDeleteCatalogues" method="post">    
<div class="tablenav top">
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
</div>

<table class="wp-list-table widefat fixed tags sorttable" cellspacing="0">
		<thead>
				<tr>
						<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
								<input type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name sortable desc'  style="">
										<?php if ($_GET['OrderBy'] == "Catalogue_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Name&Order=ASC'>";} ?>
											  <span><?php _e("Name", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='shortcode' class='manage-column column-shortcode'  style="">
											  <span><?php _e("Shortcode", 'UPCP') ?></span>
						</th>
						<th scope='col' id='requirements' class='manage-column column-requirements sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Catalogue_Item_Count" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Item_Count&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Item_Count&Order=ASC'>";} ?>
											  <span><?php _e("Products in Catalogue", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
				</tr>
		</thead>

		<tfoot>
				<tr>
						<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
								<input type="checkbox" /></th><th scope='col' id='name' class='manage-column column-name sortable desc'  style="">
										<?php if ($_GET['OrderBy'] == "Catalogue_Name" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Name&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Name&Order=ASC'>";} ?>
											  <span><?php _e("Name", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
						<th scope='col' id='shortcode' class='manage-column column-shortcode'  style="">
											  <span><?php _e("Shortcode", 'UPCP') ?></span>
						</th>
						<th scope='col' id='requirements' class='manage-column column-requirements sortable desc'  style="">
									  <?php if ($_GET['OrderBy'] == "Catalogue_Item_Count" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Item_Count&Order=DESC'>";}
										 			else {echo "<a href='admin.php?page=UPCP-options&DisplayPage=Catalogues&OrderBy=Catalogue_Item_Count&Order=ASC'>";} ?>
											  <span><?php _e("Products in Catalogue", 'UPCP') ?></span>
												<span class="sorting-indicator"></span>
										</a>
						</th>
				</tr>
		</tfoot>

	<tbody id="the-list" class='list:tag'>
		
		 <?php
				if ($myrows) { 
	  			  foreach ($myrows as $Catalogue) {
								echo "<tr id='Item" . $Catalogue->Catalogue_ID ."'>";
								echo "<th scope='row' class='check-column'>";
								echo "<input type='checkbox' name='Catalogues_Bulk[]' value='" . $Catalogue->Catalogue_ID ."' />";
								echo "</th>";
								echo "<td class='name column-name'>";
								echo "<strong>";
								echo "<a class='row-title' href='admin.php?page=UPCP-options&Action=UPCP_Catalogue_Details&Selected=Catalogue&Catalogue_ID=" . $Catalogue->Catalogue_ID ."' title='Edit " . $Catalogue->Catalogue_Name . "'>" . $Catalogue->Catalogue_Name . "</a></strong>";
								echo "<br />";
								echo "<div class='row-actions'>";
								echo "<span class='delete'>";
								echo "<a class='delete-tag' href='admin.php?page=UPCP-options&Action=UPCP_DeleteCatalogue&DisplayPage=Catalogues&Catalogue_ID=" . $Catalogue->Catalogue_ID ."'>" . __("Delete", 'UPCP') . "</a>";
		 						echo "</span>";
								echo "</div>";
								echo "<div class='hidden' id='inline_" . $Catalogue->Catalogue_ID ."'>";
								echo "<div class='name'>" . $Catalogue->Catalogue_Name . "</div>";
								echo "</div>";
								echo "</td>";
								echo "<td class='description column-description'>[product-catalogue id='" . $Catalogue->Catalogue_ID . "']</td>";
								echo "<td class='description column-items-count'>" . $Catalogue->Catalogue_Item_Count . "</td>";
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
</div>

<!-- A list of the products in the catalogue -->
<div id="col-left">
<div class="col-wrap">
	<div id="dashboard-products-column" class="metabox-holder">	
			<div id="side-sortables" class="meta-box-sortables">

<div id="add-page" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span><?php _e("Popular Products", 'UPCP') ?></span></h3>
<div class="inside">
	<div id="posttype-page" class="posttypediv">
		<ul id="posttype-page-tabs" class="posttype-tabs add-menu-item-tabs">
			<!--<li  class="tabs"><a class="nav-tab-link" href="/wp-admin/nav-menus.php?page-tab=most-recent#tabs-panel-posttype-page-most-recent">Most Recent</a></li>-->
			<li  class="tabs"><!--<a class="nav-tab-link" href="/wp-admin/nav-menus.php?page-tab=all#page-all">--><?php _e("View All", 'UPCP') ?><!--</a>--></li>
			<!--<li ><a class="nav-tab-link" href="/wp-admin/nav-menus.php?page-tab=search#tabs-panel-posttype-page-search">Search</a></li>-->
		</ul>

		<div id="tabs-panel-posttype-page-most-recent" class="tabs-panel tabs-panel-active">
			<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
				<?php //$Products = $wpdb->get_results("SELECT Item_ID, Item_Name FROM $items_table_name ORDER BY Item_Views DESC"); 
							$Products = $wpdb->get_results("SELECT Item_ID, Item_Name FROM $items_table_name"); 
							foreach ($Products as $Product) {
									echo "<li><label class='menu-item-title'><a href='/wp-admin/admin.php?page=UPCP-options&Action=UPCP_Item_Details&Selected=Product&Item_ID=" . $Product->Item_ID ."'> " . $Product->Item_Name . "</a></label></li>";
							}
				?>
			</ul>
		</div><!-- /.tabs-panel -->

		<div class="tabs-panel tabs-panel-inactive" id="tabs-panel-posttype-page-search">
						<!--<p class="quick-search-wrap">
				<input type="search" class="quick-search input-with-default-title" title="Search" value="" name="quick-search-posttype-page" />
				<img class="waiting" src="http://www.etoilewebdesign.com/wp-admin/images/wpspin_light.gif" alt="" />
				<input type="submit" name="submit" id="submit-quick-search-posttype-page" class="quick-search-submit button-secondary hide-if-js" value="Search"  />			</p>-->

			<ul id="page-search-checklist" class="list:page categorychecklist form-no-clear">
						</ul>
		</div><!-- /.tabs-panel -->

		<div id="page-all" class="tabs-panel tabs-panel-view-all tabs-panel-inactive">

		</div><!-- /.tabs-panel -->

		<p class="button-controls">
			<!--<span class="list-controls">
				<a href="/wp-admin/nav-menus.php?page-tab=all&#038;selectall=1#posttype-page" class="select-all">Select All</a>
			</span>-->

			<!--<span class="add-to-menu">
				<span class="spinner"></span>
				<input type="submit" class="button-secondary submit-add-to-menu" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-page" />
			</span>-->
		</p>

	</div><!-- /.posttypediv -->
	</div>
</div>

</div>
</div>
</div>
</div>
