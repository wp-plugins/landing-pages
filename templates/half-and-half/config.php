<?php
/**
* WordPress Landing Page Config File
* Template Name:  Half and Half Template
*
* @package  WordPress Landing Pages
* @author 	David Wells
* @version 	1.0
* @since    1.0
*/

lp_global_config(); // The lp_global_config function is for global code added by 3rd party extensions

//gets template directory name to use as identifier - do not edit - include in all template files
$key = lp_get_parent_directory(dirname(__FILE__)); 

//adds template data to global array for use with landing page plugin - edit theme category and description only. 

//EDIT - START - defines template information - helps categorizae template and provides additional popup information
$lp_data[$key]['category'] = "miscellaneous"; 
// Add Landing Page to a specific category. 
$lp_data[$key]['description'] = "This template illustrates capabilities of this plugin's templating system.."; 
// Add description visible to the user
$lp_data[$key]['features'][] = lp_list_feature("Demo Link","../wp-content/plugins/landing-pages/templates/demo/demo.html"); 
// Add a live demo link to illustration the page functionality to the user
$lp_data[$key]['features'][] = lp_list_feature("Study this template to learn about Landing Page Plugin's templating system and to assist in building new templates."); 
// Description of the landing page visible to the user.


//DO NOT EDIT - adds template to template selection dropdown 
$lp_data[$key]['value'] = $key; //do not edit this
$lp_data[$key]['label'] = ucwords(str_replace('-',' ',$key)); //do not edit this


//************************************************
// Add User Options to Your Landing Page Template
//************************************************

// Add a radio button option to your theme's options panel.	
$options = array('1' => 'on','0'=>'off');
$lp_data[$key]['options'][] = 
	lp_add_option($key,"radio","display-social","1","Display Social Media Share Buttons","Toggle social sharing on and off", $options);

// Add a dropdown toggle to the landing page options panel	
$options = array('right'=>'Conversion Area on right','left'=>'Conversion Area on left');
$lp_data[$key]['options'][] = 
	lp_add_option($key,"dropdown","sidebar","right","Page Layout","Align Conversion/Form Area to the left or the right", $options);	

// Add a media uploader field to your landing page options	
$lp_data[$key]['options'][] = 
	lp_add_option($key,"media","logo","/wp-content/plugins/landing-pages/templates/svtle/assets/images/inbound-logo.png","Logo Image","Upload Your Logo (300x110px)", $options=null);

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","content-color","ffffff","Content Background Color","Use this setting to change the template's main content area color", $options=null);

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","content-text-color","000000","Content Text Color","Use this setting to change the content text color", $options=null);	

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","sidebar-color","EE6E4C","Conversion Area Background Color","Use this setting to change the template's sidebar color", $options=null);

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","submit-button-color","38A6F0","Submit Button Background Color","Use this setting to change the template's submit button color.", $options=null);

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","sidebar-text-color","ffffff","Conversion Area Text Color","Use this setting to change the sidebar text color", $options=null);		
