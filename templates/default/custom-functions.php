<?php // Custom Functions

// Add Colorpicker
$lp_data[$key]['options'][] = 
	lp_add_option($key,"colorpicker","content-background","000000","Content Background Color","Use this setting to change the content area's background color", $options=null);	

// Add a radio button option to your theme's options panel.	
$options = array('on' => 'on','off'=>'off');
$lp_data[$key]['options'][] = 
	lp_add_option($key,"radio","background-on","on","Show Transparent Background behind content?","Toggle this on to render the transparent background behind your content for better visability", $options);

// Textfield Example
// Add a text input field to the landing page options panel	
$lp_data[$key]['options'][] = 
	lp_add_option($key,"text","countdown-message","Countdown Until... Message","Countdown Until... Message","Insert the event you are counting down to", $options=null);