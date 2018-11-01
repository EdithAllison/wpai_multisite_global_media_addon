# WPAI Multisite Global Media Addon
Imports global media image IDs in WP All Import for WooCommerce Products. Requires Multisite Global Media &amp; WooCommerce.  

This is an add-on for sites using:
- Multisite set up
- Global Media Library by https://github.com/bueltge/multisite-global-media 
- WooCommerce 
- WP All Import plugin http://www.wpallimport.com/

It allows the import of product images held in the global media library by filename. 

The CSV value should be a comma separated list in format "image-1.jpg,image-2.jpg, ..."  
The first image is set as featured image (product image)  
All other images are set as product gallery images

The plugin does NOT support posts, pages or anything else that isn't a product.  
The plugin does not upload any images, all images must already be present in the global media library. 

During upload you have 2 additional fields:
- Global Images (comma separate list of image filenames)
- Global Site ID (default = 1) 
