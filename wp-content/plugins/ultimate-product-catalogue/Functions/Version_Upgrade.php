<?php 
function TransferOutBeforeUpgrade() {
		/*$to = plugins_url("/upcp_images_backup/"); 
		$from = plugins_url("/ultimate-product-catalogue/images/"); */
		$to = dirname(__FILE__)."/../../upcp_images_backup/"; 
		$from = dirname(__FILE__)."/../images/";
		CopyFile($from, $to); 
}

function TransferInAfterUpgrade() {
		/*$from = plugins_url("/ultimate-product-catalogue/images/"); 
		$to = plugins_url("/upcp_images_backup/"); */
		$from = dirname(__FILE__)."/../../upcp_images_backup/"; 
		$to = dirname(__FILE__)."/../images/";
		CopyFile($from, $to); 
		if (is_dir($from)) { 
			  RemoveDir($from);
		} 
}

function CopyFile($source, $dest) { 
// Check for symlinks 
if (is_link($source)) { return symlink(readlink($source), $dest); } 
// Simple copy for a file 
if (is_file($source)) { return copy($source, $dest); } 

// Make destination directory 
if (!is_dir($dest)) { mkdir($dest); } 
// Loop through the folder 
$dir = dir($source); while (false !== $entry = $dir->read()) { 
// Skip pointers 
if ($entry == '.' || $entry == '..') { continue; } 
// Deep copy directories 
CopyFile("$source/$entry", "$dest/$entry"); } 
// Clean up 
$dir->close(); 
return true;
}

function RemoveDir($dirname)
{
// Sanity check
if (!file_exists($dirname)) {
return false;
}

// Simple delete for a file
if (is_file($dirname)) {
return unlink($dirname);
}

// Loop through the folder
$dir = dir($dirname);
while (false !== $entry = $dir->read()) {
// Skip pointers
if ($entry == '.' || $entry == '..') { continue; } 

// Recurse
RemoveDir("$dirname/$entry");
}

// Clean up
$dir->close();
return rmdir($dirname);
}

function SetUpdateOption() {
		update_option('UPCP_Update_Flag', "Yes");
		update_option("UPCP_Mobile_SS", "No");
}

add_filter('upgrader_pre_install', 'SetUpdateOption', 10, 2);
add_filter('upgrader_pre_install', 'TransferOutBeforeUpgrade', 10, 2); 
add_filter('upgrader_post_install', 'TransferInAfterUpgrade', 10, 2);

 ?>