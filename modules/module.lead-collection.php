<?php
	
add_filter('lp_js_hook_submit_form_pre','lp_lead_collection_js');

function lp_lead_collection_js()
{	
	$current_page = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$post_id = lp_url_to_postid($current_page);
	(isset($_SERVER['HTTP_REFERER'])) ? $referrer = $_SERVER['HTTP_REFERER'] : $referrer ='direct access';	
	(isset($_SERVER['REMOTE_ADDR'])) ? $ip_address = $_SERVER['REMOTE_ADDR'] : $ip_address = '0.0.0.0.0';
	
	?>	
	var email = jQuery(".lp-email-value input").val();
	var firstname = jQuery(".lp-first-name-value input").val();
	var lastname = jQuery(".lp-last-name-value input").val();

	//alert('1');
	if (!email)
	{
		 jQuery("#lp_container_form input[type=text]").each(function() {
			if (this.value)
			{
				if (jQuery(this).attr("name").toLowerCase().indexOf('email')>-1) {
					email = this.value;
				}
				else if(jQuery(this).attr("name").toLowerCase().indexOf('name')>-1&&!firstname) {
					 firstname = this.value;
				}
				else if (jQuery(this).attr("name").toLowerCase().indexOf('name')>-1) {
					 lastname = this.value;
				}
			}
		});
	}
	else
	{		
		if (!lastname&&jQuery("input").eq(1).val().indexOf("@") === -1)
		{
			lastname = jQuery("input").eq(1).val();
		}
	}
	
	if (!email)
	{
		jQuery("#lp_container_form input[type=text]").each(function() {
			if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('email')>-1) 
			{
				email = this.value;
			}
			else if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('name')>-1&&!firstname) {
				firstname = this.value;
			}
			else if (jQuery(this).closest('li').children('label').html().toLowerCase().indexOf('name')>-1) {
				lastname = this.value;
			}
		});
	}
	
	
	if (!lastname&&firstname)
	{
		var parts = firstname.split(" ");
		firstname = parts[0];
		lastname = parts[1];
	}
	
	jQuery.ajax({
		type: 'POST',
		url: '<?php echo admin_url('admin-ajax.php') ?>',
		data: {
			action: 'lp_store_lead',
			emailTo: email, 
			first_name: firstname, 
			last_name: lastname,
			lp_id: '<?php echo $post_id; ?>'
		},
		success: function(user_id){
				 //alert(user_id);
				 //jQuery('.lp-form').unbind('submit').submit();
			   },
		error: function(MLHttpRequest, textStatus, errorThrown){
				//alert(MLHttpRequest+' '+errorThrown+' '+textStatus);
				//die();
			}

	});
	<?php
}

if (!post_type_exists('wp-lead'))
{
	add_action('init', 'lp_wpleads_register');
	function lp_wpleads_register() {
		//echo $slug;exit;
		$labels = array(
			'name' => _x('Leads', 'post type general name'),
			'singular_name' => _x('Lead', 'post type singular name'),
			'add_new' => _x('Add New', 'Lead'),
			'add_new_item' => __('Add New Lead'),
			'edit_item' => __('Edit Lead'),
			'new_item' => __('New Leads'),
			'view_item' => __('View Leads'),
			'search_items' => __('Search Leads'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			//'show_ui' => true,
			'show_ui' => false,
			'query_var' => true,
			//'menu_icon' => WPL_URL . '/images/leads.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('custom-fields','thumbnail')
		  );

		register_post_type( 'wp-lead' , $args );
		//flush_rewrite_rules( false );

	}
}


?>