<?phpadd_action('admin_init','lp_register_admin_ajax');function lp_register_admin_ajax() {	//clear stats button	wp_enqueue_script( 'lp-admin-clear-stats-ajax-request', LANDINGPAGES_URLPATH . 'js/ajax.clearstats.js', array( 'jquery' ) );	wp_localize_script( 'lp-admin-clear-stats-ajax-request', 'ajaxadmin', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'wp_landing_page_clear_nonce' => wp_create_nonce('wp-landing-page-clear-nonce') ) );	//pause and play lander buttons	wp_enqueue_script( 'lp-admin-split-test-ajax-request', LANDINGPAGES_URLPATH . 'js/ajax.split-testing.js', array( 'jquery' ) );	wp_localize_script( 'lp-admin-split-test-ajax-request', 'lp_st_ajax', array( 'ajaxurl' => admin_url('admin-ajax.php') ));}/** * Adds ajax for split testing */add_action('wp_ajax_lp_pause_lander', 'lp_pause_lander_callback');add_action('wp_ajax_nopriv_lp_pause_lander', 'lp_pause_lander_callback');function lp_pause_lander_callback(){	$group_id = $_POST['group_id'];	$lp_id = $_POST['lp_id'];	$content = get_post_field('post_content', $group_id);	$data = json_decode($content,true);	$data[$lp_id]['status']='paused';	//print_r($data);	$data = json_encode($data);				$post = array(		'ID' =>  $group_id,						'post_content' => $data	);		return wp_update_post($post);	die();}add_action('wp_ajax_lp_play_lander', 'lp_play_lander_callback');add_action('wp_ajax_nopriv_lp_play_lander', 'lp_play_lander_callback');function lp_play_lander_callback(){	$group_id = $_POST['group_id'];	$lp_id = $_POST['lp_id'];	$content = get_post_field('post_content', $group_id);	$data = json_decode($content,true);	$data[$lp_id]['status']='active';	$data = json_encode($data);				$post = array(		'ID' =>  $group_id,						'post_content' => $data	);		return wp_update_post($post);	die();}/** * Adds Ajax for Clear Stats button */	add_action( 'wp_ajax_nopriv_lp_clear_stats_action', 'lp_clear_stats_action' );	add_action( 'wp_ajax_lp_clear_stats_action', 'lp_clear_stats_action' );	function lp_clear_stats_action()	{			global $wpdb;			$newrules = "0";		$post_id = mysql_real_escape_string($_POST['page_id']);		$variations = get_post_meta($post_id, 'lp-ab-variations', true);		if ($variations)		{			$variations = explode(",", $variations);			foreach ($variations as $vid) 			{			add_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules );			add_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules );			}		}		header('HTTP/1.1 200 OK');	}// Clear Single Stats	add_action( 'wp_ajax_nopriv_lp_clear_stats_single', 'lp_clear_stats_single' );	add_action( 'wp_ajax_lp_clear_stats_single', 'lp_clear_stats_single' );	function lp_clear_stats_single()	{			global $wpdb;		$newrules = "0";		$post_id = mysql_real_escape_string($_POST['page_id']);		$vid = $_POST['variation'];		add_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules );	add_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules );			header('HTTP/1.1 200 OK');	}	/** * Adds ajax for split testing clone button */add_action('wp_ajax_lp_st_clone', 'lp_st_clone_callback');add_action('wp_ajax_nopriv_lp_st_clone', 'lp_st_clone_callback');function lp_st_clone_callback(){	$lp_id = $_POST['lp_id'];	$clone_id = lp_duplicate_post_create_duplicate($lp_id);	lp_clone_lp_groups($lp_id,$clone_id);	echo $clone_id;	die();}/** * Adds ajax to record impressions/conversions */add_action('wp_footer','lp_register_ajax');function lp_register_ajax() {	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."/";	$current_url = trim(str_replace('//','/',"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."/"));	global $post;	// if leads on add tracking to all pages	if (@function_exists('wpleads_check_active'))	{		require_once(LANDINGPAGES_PATH . 'js/ajax.tracking.js.php');	}	else if ($post->post_type=='landing-page')	{			require_once(LANDINGPAGES_PATH . 'js/ajax.tracking.js.php');	}	// embed the javascript file that makes the AJAX request	//wp_enqueue_script( 'lp-ajax-request', LANDINGPAGES_URLPATH . 'js/ajax.tracking.js.php', array( 'jquery' ) );	//wp_localize_script( 'lp-ajax-request', 'myajax', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'current_url' =>  $current_url, 'standardize_form' =>  $standardize_form ));}add_action('wp_ajax_lp_record_conversion', 'lp_record_conversion_callback');add_action('wp_ajax_nopriv_lp_record_conversion', 'lp_record_conversion_callback');function lp_record_conversion_callback() {	global $wpdb; // this is how you get access to the database	global $user_ID; 	$global_record_admin_actions = get_option( 'lp_global_record_admin_actions', '' );	$role = get_userdata( $user_ID );	//if ($role->user_level==10)	//echo "hello";die();		$current_url = trim($_POST['current_url']);		$parts = explode('lp-variation-id=',$current_url);	if (count($parts)>1)	{		$variation_id = $parts[1];		$variation_id = str_replace('/','',$variation_id);	}	else	{		$variation_id = 0; 	}		$page_id = lp_url_to_postid( $current_url );		if (!lp_determine_spider())	{			lp_set_conversions($page_id);	}		do_action('lp_record_conversion',$page_id, $variation_id);		print $page_id;	die(); // this is required to return a proper result	}add_action('wp_ajax_lp_record_impression', 'lp_record_impression_callback');add_action('wp_ajax_nopriv_lp_record_impression', 'lp_record_impression_callback');function lp_record_impression_callback() {	global $wpdb; // this is how you get access to the database	global $user_ID; 	$global_record_admin_actions = get_option( 'lp_global_record_admin_actions', '' );	$role = get_userdata( $user_ID );	$current_url = trim($_POST['current_url']);		$parts = explode('lp-variation-id=',$current_url);	if (count($parts)>1)	{		$variation_id = $parts[1];		$variation_id = str_replace('/','',$variation_id);	}	else	{		$variation_id = 0; 	}		$page_id = lp_url_to_postid( $current_url );		if (!lp_determine_spider())	{			//print $page_id;		lp_set_page_views($page_id);				}		do_action('lp_record_impression',$page_id,$variation_id);		print $page_id;	die(); 	}add_action('wp_ajax_lp_store_lead', 'lp_store_lead_callback');add_action('wp_ajax_nopriv_lp_store_lead', 'lp_store_lead_callback');function lp_store_lead_callback() {			if (isset( $_POST['emailTo'])&&!empty( $_POST['emailTo'])&&strstr($_POST['emailTo'],'@'))	{		// Grab form values		$title = $_POST['emailTo'];		$content =	$_POST['first_name'];		$wp_lead_uid = $_POST['wp_lead_uid'];		$raw_post_values_json = $_POST['raw_post_values_json'];			//echo 'here';		global $user_ID, $wpdb;		$wordpress_date_time = $timezone_format = _x('Y-m-d G:i:s T', 'timezone date format');		$wordpress_date_time =  date_i18n($timezone_format);				(isset(	$_POST['first_name'] )) ? $first_name = $_POST['first_name'] : $first_name = "";		(isset(	$_POST['last_name'] )) ? $last_name = $_POST['last_name'] : $last_name = "";		(isset(	$_SERVER['REMOTE_ADDR'] )) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = "undefined";		(isset(	$_POST['nature'] )) ? $nature = $_POST['nature'] : $nature = 0;		(isset(	$_POST['wp_lead_uid'] )) ? $wp_lead_id = $_POST['wp_lead_uid'] : $wp_lead_id = "null";		(isset(	$_POST['lp_id'] )) ? $lp_id = $_POST['lp_id'] : $lp_id = 0;		(isset(	$_POST['lp_v'] )) ? $lp_variation = $_POST['lp_v'] : $lp_variation = 0;				do_action('lp_store_lead_pre');				$query = $wpdb->prepare(			'SELECT ID FROM ' . $wpdb->posts . '			WHERE post_title = %s			AND post_type = \'wp-lead\'',			$_POST['emailTo']		);		$wpdb->query( $query );		if ( $wpdb->num_rows ) {			// If lead exists add data/append data to it			$post_ID = $wpdb->get_var( $query );			//echo "here";			//echo $post_ID;			$meta = get_post_meta( $post_ID, 'times', TRUE );						$meta++;						if ($lp_id)			{				$conversion_data = get_post_meta( $post_ID, 'wpleads_conversion_data', TRUE );				$conversion_data = json_decode($conversion_data,true);				$conversion_data[$meta]['id'] = $lp_id;				$conversion_data[$meta]['variation'] = $lp_variation;				$conversion_data[$meta]['datetime'] = $wordpress_date_time;				$conversion_data = json_encode($conversion_data);			}						update_post_meta( $post_ID, 'times', $meta );			update_post_meta( $post_ID, 'wpleads_email_address', $title );						if (!empty($user_ID))				update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );							if (!empty($first_name))				update_post_meta( $post_ID, 'wpleads_first_name', $first_name );			if (!empty($last_name))				update_post_meta( $post_ID, 'wpleads_last_name', $last_name );			if (!empty($wp_lead_id))				update_post_meta( $post_ID, 'wp_leads_uid', $wp_lead_id );							update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );			update_post_meta( $post_ID, 'wpleads_conversion_data', $conversion_data );			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );						do_action('wpleads_after_conversion_lead_update',$post_ID);				} else { 			// If lead doesn't exist create it			$post = array(				'post_title'		=> $title, 				 //'post_content'		=> $json,				'post_status'		=> 'publish',				'post_type'		=> 'wp-lead',				'post_author'		=> 1			);						//$post = add_filter('lp_leads_post_vars',$post);						if ($lp_id)			{							$conversion_data[1]['id'] = $lp_id;				$conversion_data[1]['variation'] = $lp_variation;				$conversion_data[1]['datetime'] = $wordpress_date_time;				$conversion_data[1]['first_time'] = 1;									$conversion_data = json_encode($conversion_data);			}						$post_ID = wp_insert_post($post);			update_post_meta( $post_ID, 'times', 1 );			update_post_meta( $post_ID, 'wpleads_wordpress_user_id', $user_ID );			update_post_meta( $post_ID, 'wpleads_email_address', $title );			update_post_meta( $post_ID, 'wpleads_first_name', $first_name);			update_post_meta( $post_ID, 'wpleads_last_name', $last_name);			update_post_meta( $post_ID, 'wpleads_ip_address', $ip_address );			update_post_meta( $post_ID, 'wp_leads_uid', $wp_lead_id );			update_post_meta( $post_ID, 'wpleads_conversion_data', $conversion_data );			update_post_meta( $post_ID, 'wpleads_landing_page_'.$lp_id, 1 );						$geo_array = unserialize(lp_remote_connect('http://www.geoplugin.net/php.gp?ip='.$ip_address));									(isset($geo_array['geoplugin_areaCode'])) ? update_post_meta( $post_ID, 'wpleads_areaCode', $geo_array['geoplugin_areaCode'] ) : null;			(isset($geo_array['geoplugin_city'])) ? update_post_meta( $post_ID, 'wpleads_city', $geo_array['geoplugin_city'] ) : null;			(isset($geo_array['geoplugin_regionName'])) ? update_post_meta( $post_ID, 'wpleads_region_name', $geo_array['geoplugin_regionName'] ) : null;			(isset($geo_array['geoplugin_regionCode'])) ? update_post_meta( $post_ID, 'wpleads_region_code', $geo_array['geoplugin_regionCode'] ) : null;			(isset($geo_array['geoplugin_countryName'])) ? update_post_meta( $post_ID, 'wpleads_country_name', $geo_array['geoplugin_countryName'] ) : null;			(isset($geo_array['geoplugin_countryCode'])) ? update_post_meta( $post_ID, 'wpleads_country_code', $geo_array['geoplugin_countryCode'] ) : null;			(isset($geo_array['geoplugin_latitude'])) ? update_post_meta( $post_ID, 'wpleads_latitude', $geo_array['geoplugin_latitude'] ) : null;			(isset($geo_array['geoplugin_longitude'])) ? update_post_meta( $post_ID, 'wpleads_longitude', $geo_array['geoplugin_longitude'] ) : null;			(isset($geo_array['geoplugin_currencyCode'])) ? update_post_meta( $post_ID, 'wpleads_currency_code', $geo_array['geoplugin_currencyCode'] ) : null;			(isset($geo_array['geoplugin_currencySymbol_UTF8'])) ? update_post_meta( $post_ID, 'wpleads_currency_symbol', $geo_array['geoplugin_currencySymbol_UTF8'] ) : null;						do_action('wpleads_after_conversion_lead_insert',$post_ID);				}		$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');		$wordpress_date_time =  date_i18n($timezone_format);		$data['lp_id'] = $lp_id;		$data['lead_id'] = $post_ID;		$data['first_name'] = $first_name;		$data['last_name'] = $last_name;		$data['email'] = $title;		$data['wp_lead_uid'] = $wp_lead_id;		$data['raw_post_values_json'] = $raw_post_values_json;				do_action('lp_store_lead_post', $data );				echo $post_ID;		die();	}}/** * Add ajax for post meta save options */add_action( 'wp_ajax_nopriv_wp_landing_page_meta_save', 'wp_landing_page_meta_save' );add_action( 'wp_ajax_wp_landing_page_meta_save', 'wp_landing_page_meta_save' );function wp_landing_page_meta_save(){	global $wpdb;	if ( !wp_verify_nonce( $_POST['nonce'], "wp-landing-page-meta-nonce")) {		 exit("Wrong nonce");	}	$new_meta_val = $_POST['new_meta_val'];	$meta_id = $_POST['meta_id'];	$post_id = mysql_real_escape_string($_POST['page_id']);	if ($meta_id === "main_title") 	{		$my_post = array();		$my_post['ID'] = $post_id;		$my_post['post_title'] = $new_meta_val;		// Update the post into the database		wp_update_post( $my_post );	}	if ($meta_id === "the_content") 	{		$title_save = get_post_meta($post_id, "lp-main-headline", true); // fix content from removing title		$my_post = array();		$my_post['ID'] = $post_id;		$my_post['post_content'] = $new_meta_val;		// Update the post into the database		wp_update_post( $my_post );		add_post_meta( $post_id, "lp-main-headline", $title_save, true ) or update_post_meta( $post_id, "lp-main-headline", $title_save ); // fix main headline removal	} 	else 	{	  add_post_meta( $post_id, $meta_id, $new_meta_val, true ) or update_post_meta( $post_id, $meta_id, $new_meta_val );	}		header('HTTP/1.1 200 OK');}