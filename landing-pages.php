<?php
/*
Plugin Name: Landing Pages
Plugin URI: http://www.inboundnow.com/landing-pages/
Description: The first true all-in-one Landing Page solution for WordPress, including ongoing conversion metrics, a/b split testing, unlimited design options and so much more!
Version: 1.0.9.2
Author: David Wells, Hudson Atwell
Author URI: http://www.inboundnow.com/
*/
					
define('LANDINGPAGES_CURRENT_VERSION', '1.0.9.2' );
define('LANDINGPAGES_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('LANDINGPAGES_PATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define('LANDINGPAGES_PLUGIN_SLUG', 'landing-pages' );
define('LANDINGPAGES_STORE_URL', 'http://www.inboundnow.com/landing-pages/' ); 
$uploads = wp_upload_dir();
define('LANDINGPAGES_UPLOADS_PATH', $uploads['basedir'].'/landing-pages/templates/' ); 
define('LANDINGPAGES_UPLOADS_URLPATH', $uploads['baseurl'].'/landing-pages/templates/' ); 

/** 
 * Load Admin Core Files
 */
if (is_admin())
{
include_once('functions/functions.admin.php');
include_once('modules/module.global-settings.php');
include_once('modules/module.clone.php');
include_once('modules/module.extension-updater.php');
}
/**
 * load frontend-only and load global core files
 */
include_once('functions/functions.global.php');
include_once('modules/module.post-type.php');
include_once('modules/module.track.php');
include_once('modules/module.ajax-setup.php');
include_once('modules/module.utils.php');
include_once('modules/module.sidebar.php');
include_once('modules/module.widgets.php');
include_once('modules/module.cookies.php');
include_once('modules/module.lead-collection.php');
include_once('modules/module.ab-testing.php');
include_once('modules/module.alert.php');
include_once('functions/functions.templates.php'); 

/**
 * REGISTER ACTIVATION!
 */
register_activation_hook(__FILE__, 'landing_page_activate');

function landing_page_activate()
{

	add_option( 'lp_global_css', '', '', 'no' );
	add_option( 'lp_global_js', '', '', 'no' );
	add_option( 'lp_global_record_admin_actions', '1', '', 'no' );
	add_option( 'lp_global_lp_slug', 'go', '', 'no' );
	add_option( 'lp_split_testing_slug', 'group', '', 'no' );
	update_option( 'lp_activate_rewrite_check', '1');
	
	//enable lead management
	//include_once('modules/module.leads-activate.php');
	
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
	
}
	
/**
 * PREPARE LANDING PAGE TEMPLATES
 */
if (is_admin())
{
	//load current url in global variable
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	
	$template_paths = lp_get_core_template_paths();	
	
	//Now load all config.php files with their custom meta data
	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{		
			include_once(LANDINGPAGES_PATH."/templates/$name/config.php"); 	
		}		
	}
	
	$template_paths = lp_get_extended_template_paths();	
	$uploads = wp_upload_dir();
	$uploads_path = $uploads['basedir'];
	//print_r($template_paths);exit;
	$extended_templates_path = $uploads_path.'/landing-pages/templates/';
	
	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{			
			include_once($extended_templates_path."$name/config.php"); 	
		}		
	}
	
	$template_data = lp_get_template_data();
	if (isset($template_data))
	{
		$template_data_cats = lp_get_template_data_cats($template_data);
	}
	
	
	$template_paths = lp_get_core_template_paths();	
	//print_r($template_paths);
	
	//Now load all config.php files with their custom meta data
	if (count($template_paths)>0)
	{
		foreach ($template_paths as $name)
		{		
			include_once(LANDINGPAGES_PATH."/templates/$name/config.php"); 	
		}
		
		$template_data = lp_get_template_data();
		if (isset($template_data))
		{
			$template_data_cats = lp_get_template_data_cats($template_data);
		}
	}
	
	//Select Template
	//main headline metabox is defined in module-metaboxes.php
	$lp_data['lp']['options'][] = 	lp_add_option('lp',"radio","selected-template","default","Select Template","This option provides a placeholder for the selected template data", $options=null);
	
	//Set Main Headline
	//main headline metabox is defined in module-metaboxes.php
	$lp_data['lp']['options'][] =  lp_add_option('lp',"radio","main-headline","","Set Main Headline","Set Main Headline", $options=null);	

	
	add_action('add_meta_boxes', 'lp_display_meta_box_lp_conversion_area');

	/* ADD FORM WYSIWYG METABOX */
	//prepare primary meta box that allows user to select templates
	add_action('add_meta_boxes', 'add_custom_meta_box_select_templates');

	//include additional metaboxes
	include_once('modules/module.metaboxes.php');
}


/**
 * Hook function that will apply css, js, and record impressions
 */
add_action('wp_head','landing_pages_insert_custom_head');
function landing_pages_insert_custom_head() {
	global $post;
	
   if (isset($post)&&'landing-page'==$post->post_type) 
   {
		//$global_js =  htmlspecialchars_decode(get_option( 'lp_global_js', '' ));			
		$global_record_admin_actions = get_option( 'lp_global_record_admin_actions', '0' );
		
		$this_id = $post->ID;
		$custom_css = get_post_meta($this_id, 'lp-custom-css', true);
		$custom_js = get_post_meta($this_id, 'lp-custom-js', true);
		//echo $this_id;exit;

		//Print Cusom CSS
		if (!stristr($custom_css,'<style'))
		{
			echo '<style type="text/css" id="lp_css_custom">'.$custom_css.'</style>';	
		}
		else
		{
			echo $custom_css;
		}
		if (!stristr($custom_css,'<script'))
		{
			echo '<script type="text/javascript" id="lp_js_custom">jQuery(document).ready(function($) {
			'.$custom_js.' });</script>';
		}
		else
		{
			echo $custom_js;
		}

		if ($global_record_admin_actions==0&&current_user_can( 'manage_options' ))
		{
		}
		else
		{		

			if (!lp_determine_spider())
			{
				//lp_set_page_views(get_the_ID($this_id));
			}
		}
		  
		//rewind_posts();
		//wp_reset_query();
   }
}

add_filter('the_content','landing_pages_add_conversion_area');
add_filter('get_the_content','landing_pages_add_conversion_area');
function landing_pages_add_conversion_area($content)
{	
	if ('landing-page'==get_post_type())
	{
		global $post;
		
		$key = get_post_meta($post->ID, 'lp-selected-template', true);
		$key = apply_filters('lp_selected_template',$key); 
		
		if (strstr($key,'-slash-'))
		{
			$key = str_replace('-slash-','/',$key);
		}
		
		$my_theme =  wp_get_theme($key);
		//echo $key;	
		if ($my_theme->exists()||$key=='default')
		{
			
			$position = get_post_meta($post->ID, "{$key}-conversion-area-placement", true);
			$_SESSION['lp_conversion_area_position'] = $position;
			
			$conversion_area = do_shortcode(get_post_meta($post->ID, 'lp-conversion-area', true));
			$standardize_form = get_option( 'main-landing-page-auto-format-forms' , 1); // conditional to check for options
			if ($standardize_form) 
			{
				$wrapper_class = lp_discover_important_wrappers($conversion_area);
				$conversion_area = lp_rebuild_attributes($conversion_area);	
			}
			
			$conversion_area = "<div id='lp_container' class='$wrapper_class'>".$conversion_area."</div>";	
			
			//echo $template;exit;
			if ($position=='top')
			{
				$content = $conversion_area.$content;
			}
			else if ($position=='bottom')
			{
				$content = $content.$conversion_area;
			}
			else if ($position=='widget')
			{
				$content = $content;
			}
			else 
			{
				$conversion_area = str_replace("id='lp_inner'","id='lp_inner' class='lp_$position' style='float:$position'",$conversion_area);
				$content = $conversion_area.$content;
				
			}
		}
		
	}
	//echo $content;exit;
	return $content;
}


if (is_admin())
{
	include_once('modules/module.split-testing.php');
	include_once('modules/module.templates.php');
	include_once('modules/module.store.php');
	/**********************************************************/
	/******************CREATE SETTINGS SUBMENU*****************/

	add_action('admin_menu', 'lp_add_menu');
	
	function lp_add_menu()
	{
		//echo 1; exit;
		if (current_user_can('manage_options'))
		{

			add_submenu_page('edit.php?post_type=landing-page', 'URL Rotater', 'URL Rotater', 'manage_options', 'lp_split_testing','lp_split_testing_display');	
			
			add_submenu_page('edit.php?post_type=landing-page', 'Templates', 'Templates', 'manage_options', 'lp_manage_templates','lp_manage_templates',100);	
				
			add_submenu_page('edit.php?post_type=landing-page', 'Get Addons', 'Get Addons', 'manage_options', 'lp_store','lp_store_display',100);	
			
			add_submenu_page('edit.php?post_type=landing-page', 'Global Settings', 'Global Settings', 'manage_options', 'lp_global_settings','lp_display_global_settings');

			// Add settings page for frontend editor
    		add_submenu_page('edit.php?post_type=landing-page', __('Editor','Editor'), __('Editor','Editor'), 'manage_options', 'lp-frontend-editor', 'lp_frontend_editor_screen');
			
		}
	}
	
/**
 * PREPARE STAT CLEARNING FUNCTIONS
 */
function lp_clear_stats_group($landing_page_ids)
	{
		$this_array = explode(',',$landing_page_ids);
		
		foreach ($landing_page_ids as $lp_id)
		{
			update_post_meta($lp_id, 'lp_page_views_count', 0);
			update_post_meta($lp_id, 'lp_page_conversions_count', 0);
		}
	}
	
}

/**
 * MAKE SURE WE USE THE RIGHT TEMPLATE
 */
add_filter('single_template', 'lp_custom_template');

function lp_custom_template($single) {
    global $wp_query, $post, $query_string;
	$template = get_post_meta($post->ID, 'lp-selected-template', true);
	$template = apply_filters('lp_selected_template',$template); 
		
	
	if (isset($template))
	{
		//echo 2;exit;
		if ($post->post_type == "landing-page")
		{
			if (strstr($template,'-slash-'))
			{
				$template = str_replace('-slash-','/',$template);
			}
			
			$my_theme =  wp_get_theme($template);
			
			if ($my_theme->exists())
			{
				return "";
			}
			else if ($template!='default')
			{
				$template = str_replace('_','-',$template);
				//echo LANDINGPAGES_URLPATH.'templates/'.$template.'/index.php'; exit;
				if (file_exists(LANDINGPAGES_PATH.'templates/'.$template.'/index.php'))
				{
					//query_posts ($query_string . '&showposts=1');
					return LANDINGPAGES_PATH.'templates/'.$template.'/index.php';
				}
				else
				{			
					//query_posts ($query_string . '&showposts=1');
					return LANDINGPAGES_UPLOADS_PATH.$template.'/index.php';
				}
			}
		}
	}
    return $single;
}
/*
global $wpdb;
$data   =   array();
$wpdb->query("
  SELECT `meta_key`, `meta_value`
	FROM $wpdb->postmeta
	WHERE `post_id` = ".$_GET['post']."
");
foreach($wpdb->last_result as $k => $v){
	$data[$v->meta_key] =   $v->meta_value;
};
if (isset($_GET['post']))
{
	echo "<pre>";
	print_r( $data);
	echo "</pre>";
}
/**
 * LOAD ADDITIONAL MODULES
 */
include_once('modules/module.customizer.php');
?>