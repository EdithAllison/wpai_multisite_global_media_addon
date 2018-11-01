<?php
/*
Plugin Name: WP All Import - Multisite Global Media Add On
Description: Imports global media image IDs in WP All Import for WooCommerce Products. Requires Multisite Global Media & WooCommerce.  
Version: 1.0
Author: Edith Allison
* requires WP All Import http://www.wpallimport.com/
* requires Multisite Gobal Media https://github.com/bueltge/multisite-global-media 
*/

include "rapid-addon.php";

$multisite_global_media_addon = new RapidAddon('Multisite Global Media Add-On', 'multisite_global_media_addon');

$multisite_global_media_addon->add_field( 'mgma_images', 'Usage Images in Global Media Library (comma separated list of filenames)', 'textarea', null, 'Enter image filenames separated by comma. Only works for images present in the global media library. First image is set as featured image.', false, '');

$multisite_global_media_addon->add_field('mgma_site_id', 'Global Media Site ID (default 1)', 'text', null, '', false, '1');

$multisite_global_media_addon->set_import_function('multisite_global_media_addon_import');

if (function_exists('is_plugin_active')) {
	
	// only run this add-on if  Multisite Gobal Media & WooCommerce are active. 
	if ( is_plugin_active( "multisite-global-media/multisite-global-media.php" ) && is_plugin_active( "woocommerce/woocommerce.php" ) ) {
		
		$multisite_global_media_addon->run(
			array(
				"post_types" => array( "product" ) // only run for products 
			)
		);
		
	} 
}

function multisite_global_media_addon_import($post_id, $data, $import_options) {

	global $multisite_global_media_addon;
	
	$multisite_global_media_addon->log( '<strong>MULTISITE GLOBAL MEDIA ADD-ON:</strong>' );
	
	$images_array = explode(',', $data['mgma_images']);
	
	$global_site_id = (!empty ($data['mgma_site_id']) ) ? (int)$data['mgma_site_id'] : 1; 
	
	$i = 0; 
	$gallery_ids = array(); 
												
	foreach ( $images_array as $img ) {		
		
		$image_id = multisite_global_media_addon_get_image_id(trim($img),$global_site_id);
	
		$global_img = $data['mgma_site_id'] . '00000' . $image_id; 		  
			  
		if ($i == 0) {
			
			  update_post_meta($post_id, '_thumbnail_id', $global_img);			
			  update_post_meta($global_img, 'global_media_site_id', $global_site_id );  
			  $multisite_global_media_addon->log( 'Multisite Global Media : Product Image set as ' . $global_img );
			  
			  $i++;
			  
		} else {
			
			  $gallery_ids[] = $global_img;
			  
		}
	}
	
	$gallery_string = implode(',', $gallery_ids);
	$gallery_string = rtrim($gallery_string, ',');
	
	// Set product gallery images
	update_post_meta($post_id, '_product_image_gallery', (!empty($gallery_string)) ? $gallery_string : '');
	$multisite_global_media_addon->log( 'Multisite Global Media : Gallery Images set as ' . $gallery_string );
	
	update_post_meta($post_id, 'global_media_site_id', $global_site_id ); 
	$multisite_global_media_addon->log( 'Multisite Global Media : Global Site ID set as ' . $global_site_id );	

}

function multisite_global_media_addon_get_image_id($image_name, $global_site_id = 1) {
	
 global $multisite_global_media_addon;
 
 // we switch to the global media library 	
 switch_to_blog($global_site_id);  
 global $wpdb;
 
 $image_ID = ''; 
 
    // search attachment by attached file
    $attachment_metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND (meta_value = %s OR meta_value LIKE %s);", '_wp_attached_file', $image_name, "%/" . $image_name));
    
    if (!empty($attachment_metas)) {
        foreach ($attachment_metas as $attachment_meta) {
            $attch = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->posts . " WHERE ID = %d;", $attachment_meta->post_id));
            if (!empty($attch)) {
                $image_ID = $attachment_meta->post_id; 
                break;
            }
        }
    }
	 
	// we switch back to currenet site     
	restore_current_blog(); 
	    
	return $image_ID; 
    
}


