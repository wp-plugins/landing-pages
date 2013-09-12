<?php

if (is_admin())
{	
	//define main tabs and bind display functions
	if (isset($_GET['page'])&&($_GET['page']=='lp_global_settings'&&$_GET['page']=='lp_global_settings'))
	{
		add_action('admin_init','lp_global_settings_enqueue');
		function lp_global_settings_enqueue()
		{		
			wp_enqueue_style('lp-css-global-settings-here', LANDINGPAGES_URLPATH . 'css/admin-global-settings.css');			
		}
	}
	
	
	function lp_get_global_settings()
	{
		global $lp_global_settings;
		
		// Setup navigation and display elements
		$tab_slug = 'main';
		$lp_global_settings[$tab_slug]['label'] = 'Global Settings';	
		
		$lp_global_settings[$tab_slug]['settings'] = 
		array(	
			//ADD METABOX - SELECTED TEMPLATE	
			array(
				'id'  => 'landing-page-permalink-prefix',
				'label' => 'Default Landing Page Permalink Prefix',
				'description' => "Enter in the 'prefix' for landing page permalinks. eg: /prefix/pemalink-name",
				'type'  => 'text', 
				'default'  => 'go',
				'options' => null
			),
			array(
				'id'  => 'landing-page-prepopulate-forms',
				'label' => 'Enable Form Prepopulation',
				'description' => "With this setting enabled landing pages plugin will remember form input data that the user submits to a form and attempt to pre-populate all other forms with the same user's data when applicable.",
				'type'  => 'radio', 
				'default'  => '1',
				'options' => array('1'=>'on','0'=>'off')
			),
			array(
				'id'  => 'landing-page-disable-admin-tracking',
				'label' => 'Disable Admin Impression/Conversion Tracking',
				'description' => "With this setting enabled landing pages plugin will not record visits and conversions for administrative users that are logged in.",
				'type'  => 'radio', 
				'default'  => '0',
				'options' => array('1'=>'on','0'=>'off')
			),
			array(
				'id'  => 'landing-page-auto-format-forms',
				'label' => 'Enable Form Standardization',
				'description' => "With this setting enabled landing pages plugin will clean and standardize all input ids and classnames. Uncheck this setting to disable standardization.",
				'type'  => 'radio', 
				'default'  => '0',
				'options' => array('1'=>'on','0'=>'off')
			),
			array(
				'id'  => 'main-landing-page-auto-format-forms-retain-elements',
				'label' => 'Form Standardization Element Whitelist',
				'description' => "Form standardization strips the conversion area content of html elements. Add the elements you do not want to be stripped to this list.",
				'type'  => 'textarea', 
				'default'  => '<button><script><textarea><style><input><form><select><label><a><p><b><u><strong><i><img><strong><span><font><h1><h2><h3><center><blockquote><embed><object><small>',
				'options' => null 
			)
		);

		$lp_global_settings = apply_filters('lp_define_global_settings',$lp_global_settings);

		return $lp_global_settings;
	}	
	
	/* Provide backwards compatibility for older data array model */
	add_filter('lp_define_global_settings','lp_rebuild_old_global_settings_configurations_to_suit_new_convention', 99, 1);
	function lp_rebuild_old_global_settings_configurations_to_suit_new_convention($lp_global_settings)
	{
		//print_r($lp_global_settings);exit;
		foreach ($lp_global_settings as $parent_tab => $aa)
		{
			if (is_array($aa))
			{
				
				foreach ($aa as $k=>$aaa)
				{
					/* change 'options' key to 'settings' */
					if ($k=='options')
					{
						if (is_array($aaa))
						{
							foreach ($aaa as $kk => $aaaa)
							{
								$lp_global_settings[$parent_tab]['settings'][] = $aaaa;
							}							
						}						
						unset($lp_global_settings[$parent_tab][$k]);
					}
			
				}
			}
		}		
		return $lp_global_settings;
	}
	
	function lp_display_global_settings_js()
	{	
		if (isset($_GET['tab']))
		{
			$default_id = $_GET['tab'];
		}
		else
		{
			$default_id ='main';
		}
		?>
		<script type='text/javascript'>
			jQuery(document).ready(function() 
			{
				//jQuery('#<? echo $default_id; ?>').css('display','block');
				//jQuery('#<? echo $default_id; ?>').css('display','block');
				 setTimeout(function() {
	     			var getoption = document.URL.split('&option=')[1];
					var showoption = "#" + getoption;
					jQuery(showoption).click();
    			}, 100);

				jQuery('.lp-nav-tab').live('click', function() {
					var this_id = this.id.replace('tabs-','');
					//alert(this_id);
					jQuery('.lp-tab-display').css('display','none');
					jQuery('#'+this_id).css('display','block');
					jQuery('.lp-nav-tab').removeClass('nav-tab-special-active');
					jQuery('.lp-nav-tab').addClass('nav-tab-special-inactive');
					jQuery('#tabs-'+this_id).addClass('nav-tab-special-active');						
					jQuery('#id-open-tab').val(this_id);
				});
	
			});			
		</script>
		<?php
	}
	
	function lp_display_global_settings()
	{	
		global $wpdb;
		$lp_global_settings = lp_get_global_settings();
		
		//print_r($lp_global_settings);
		$active_tab = 'main'; 
		if (isset($_REQUEST['open-tab']))
		{
			$active_tab = $_REQUEST['open-tab'];
		}

		//echo $active_tab;exit;
		
		lp_display_global_settings_js();
		lp_save_global_settings();

		echo '<h2 class="nav-tab-wrapper">';		
	
		foreach ($lp_global_settings as $key => $data)
		{
			?>
			<a  id='tabs-<?php echo $key; ?>' class="lp-nav-tab nav-tab nav-tab-special<?php echo $active_tab == $key ? '-active' : '-inactive'; ?>"><?php echo $data['label']; ?></a> 
			<?php
		}
		echo "</h2><div class='lp-settings-tab-sidebar'><div class='lp-sidebar-settings'><h2 style='font-size:17px;'>Like the Plugin? Leave us a review</h2><center><a class='review-button' href='http://wordpress.org/support/view/plugin-reviews/landing-pages?rate=5#postform' target='_blank'>Leave a Review</a></center><small>Reviews help constantly improve the plugin & keep us motivated! <strong>Thank you for your support!</strong></small></div><div class='lp-sidebar-settings'><h2>Help keep the plugin up to date, awesome & free!</h2><form action='https://www.paypal.com/cgi-bin/webscr' method='post' target='_top'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='GKQ2BR3RKB3YQ'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'></form>
			<small>Spare some change? Buy us a coffee/beer.<strong> We appreciate your continued support.</strong></small></div><div class='lp-sidebar-settings'><h2 style='font-size:18px;'>Follow Updates on Facebook</h2><iframe src='//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Finboundnow&amp;width=234&amp;height=65&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=false&amp;appId=364256913591848' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:234px; height:65px;' allowTransparency='true'></iframe></div></div>";
		echo "<form action='edit.php?post_type=landing-page&page=lp_global_settings' method='POST'>
		<input type='hidden' name='nature' value='lp-global-settings-save'>
		<input type='hidden' name='open-tab' id='id-open-tab' value='{$active_tab}'>";
		
		foreach ($lp_global_settings as $key => $data)
		{
			lp_render_global_settings($key,$data['settings'], $active_tab);
		}
		
		echo '<div style="float:left;padding-left:9px;padding-top:20px;">
				<input type="submit" value="Save Settings" tabindex="5" id="lp-button-create-new-group-open" class="button-primary" >
			</div>';
		echo "</form>";
		?>
		<div id="lp-additional-resources" class="clear">
			<hr>
		<div id="more-templates">
			<center>
			<a href="http://www.inboundnow.com/landing-pages/downloads/category/templates/" target="_blank"><img src="<?php echo LANDINGPAGES_URLPATH;?>/images/templates-image.png"></a>
			
			</center>
		</div>
		<div id="more-addons">
			<center>
			<a href="http://www.inboundnow.com/landing-pages/downloads/category/add-ons/" target="_blank"><img src="<?php echo LANDINGPAGES_URLPATH;?>/images/add-on-image.png"></a>
		</center>
		</div>
		<div id="custom-templates">
			<center><a href="http://www.inboundnow.com/landing-pages/custom-wordpress-landing-page-setup/" target=="_blank"><img src="<?php echo LANDINGPAGES_URLPATH;?>/images/custom-setup-image.png"></a>
			</center>
		</div>
		</div>
		<div class="clear" id="php-sql-lp-version">
		 <h3>Installation Status</h3>
              <table class="form-table" id="lp-wordpress-site-status">

                <tr valign="top">
                   <th scope="row"><label>PHP Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo phpversion(); ?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare(phpversion(), '5.0.0', '>')){
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message"><?php _e("Gravity Forms requires PHP 5 or above.", "gravityforms"); ?></span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                   <th scope="row"><label>MySQL Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo $wpdb->db_version();?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare($wpdb->db_version(), '5.0.0', '>')){
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message"><?php _e("Gravity Forms requires MySQL 5 or above.", "gravityforms"); ?></span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                   <th scope="row"><label>WordPress Version</label></th>
                    <td class="installation_item_cell">
                        <strong><?php echo get_bloginfo("version"); ?></strong>
                    </td>
                    <td>
                        <?php
                            if(version_compare(get_bloginfo("version"), '3.3', '>')){
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/tick.png"/>
                                <?php
                            }
                            else{
                                ?>
                                <img src="<?php echo LANDINGPAGES_URLPATH;?>/images/cross.png"/>
                                <span class="installation_item_message">landing pages requires version X or higher</span>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                 <tr valign="top">
                   <th scope="row"><label>Landing Page Version</label></th>
                    <td class="installation_item_cell">
                        <strong>Version <?php echo landing_page_get_version();?></strong>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </div>
	<?php	
	}
	
	function lp_save_global_settings() 
	{
		
		$lp_global_settings = lp_get_global_settings();
		
		if (!isset($_POST['nature']))
			return;
	
		
		foreach ($lp_global_settings as $key=>$data)
		{	
			$tab_settings = $lp_global_settings[$key]['settings'];		

			// loop through fields and save the data
			foreach ($tab_settings as $field) 
			{
				$field_id = $key."-".$field['id'];
				$old = get_option($field_id);	
				(isset($_POST[$field_id]))? $new = $_POST[$field_id] : $new = null;
				
				
				if ((isset($new) && ($new !== $old ) )|| !isset($old) ) 
				{
					//echo $field_id;exit;
					$bool = update_option($field_id,$new);				
					if ($field_id=='main-landing-page-permalink-prefix')
					{
						//echo "here";
						global $wp_rewrite;
						$wp_rewrite->flush_rules();
					}
					if ($field['type']=='license-key')
					{						
						// retrieve the license from the database
						$license = trim( get_option( 'edd_sample_license_key' ) );
						
						// data to send in our API request
						$api_params = array( 
							'edd_action'=> 'activate_license', 
							'license' 	=> $new, 
							'item_name' =>  $field['slug'] // the name of our product in EDD
						);
						
						// Call the custom API.
						$response = wp_remote_get( add_query_arg( $api_params, LANDINGPAGES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

						// make sure the response came back okay
						if ( is_wp_error( $response ) )
							break;

						// decode the license data
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						
						//echo $license_data->license;
						//echo $option['slug'];exit;
						
						// $license_data->license will be either "active" or "inactive"						
						$license_status = update_option('lp_license_status-'.$field['slug'], $license_data->license);
					}
				} 
				elseif (!$new && $old) 
				{
					//echo "here: $key <br>";
					$bool = delete_option($field_id);
				}
				else
				{
					//print_r($field);
					if ($field['type']=='license-key'&& $new )
					{
					
						$license_status = get_option('lp_license_status-'.$field['slug']);
						
						if ($license_status=='valid' && $new == $old)
						{
							continue;
						}

						// retrieve the license from the database
						$license = trim( get_option( 'edd_sample_license_key' ) );
						
						// data to send in our API request
						$api_params = array( 
							'edd_action'=> 'activate_license', 
							'license' 	=> $new, 
							'item_name' =>  $field['slug'] // the name of our product in EDD
						);
						
						// Call the custom API.
						$response = wp_remote_get( add_query_arg( $api_params, LANDINGPAGES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
						//print_r($response);
						//echo "<br>";
						
						// make sure the response came back okay
						if ( is_wp_error( $response ) )
							break;

						// decode the license data
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						
						// $license_data->license will be either "active" or "inactive"						
						$license_status = update_option('lp_license_status-'.$field['slug'], $license_data->license);
					}
				}
				//exit;
				do_action('lp_save_global_settings',$field);
			} // end foreach		
			
		}
		//exit;
	}
	
		
	function lp_render_global_settings($key,$custom_fields,$active_tab)
	{

		//Check if active tab
		if ($key==$active_tab)
		{
			$display = 'block';
		}
		else
		{
			$display = 'none';
		}
		
		//echo $display;
		
		// Use nonce for verification
		echo "<input type='hidden' name='lp_{$key}_custom_fields_nonce' value='".wp_create_nonce('lp-nonce')."' />";

		// Begin the field table and loop
		echo '<table class="lp-tab-display" id="'.$key.'" style="display:'.$display.'">';
		//print_r($custom_fields);exit;
		foreach ($custom_fields as $field) {
			//echo $field['type'];exit; 
			// get value of this field if it exists for this post
			if (isset($field['default']))
			{
				$default = $field['default'];
			}
			else
			{
				$default = null;
			}
			
			$field_id = $key."-".$field['id'];
			$option = get_option($field_id, $default);
			
			// begin a table row with
			echo '<tr>
					<th class="lp-gs-th" valign="top" style="font-weight:300px;"><small>'.$field['label'].':</small></th>
					<td>';
					switch($field['type']) {
						// text
						case 'colorpicker':
							if (!$option)
							{
								$option = $field['default'];
							}
							echo '<input type="text" class="jpicker" name="'.$field_id.'" id="'.$field_id.'" value="'.$option.'" size="5" />
									<div class="lp_tooltip tool_color" title="'.$field['desc'].'"></div>';
							break;
						case 'datepicker':
							echo '<input id="datepicker-example2" class="Zebra_DatePicker_Icon" type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.$option.'" size="8" />
									<div class="lp_tooltip tool_date" title="'.$field['desc'].'"></div><p class="description">'.$field['desc'].'</p>';
							break;	
						case 'license-key':
							$license_status = lp_check_license_status($field);
							//echo $license_status;exit;
							echo '<input type="hidden" name="lp_license_status-'.$field['slug'].'" id="'.$field_id.'" value="'.$license_status.'" size="30" />
							<input type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.$option.'" size="30" />
									<div class="lp_tooltip tool_text" title="'.$field['desc'].'"></div>';
							
							if ($license_status=='valid')
							{
								echo '<div class="lp_license_status_valid">Valid</div>';
							}
							else
							{
								echo '<div class="lp_license_status_invalid">Invalid</div>';
							}						
							break;	
						case 'text':
							echo '<input type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.$option.'" size="30" />
									<div class="lp_tooltip tool_text" title="'.$field['desc'].'"></div>';
							break;
						// textarea
						case 'textarea':
							echo '<textarea name="'.$field_id.'" id="'.$field_id.'" cols="106" rows="6">'.$option.'</textarea>
									<div class="lp_tooltip tool_textarea" title="'.$field['desc'].'"></div>';
							break;
						// wysiwyg
						case 'wysiwyg':
							wp_editor( $option, $field_id, $settings = array() );
							echo	'<span class="description">'.$field['desc'].'</span><br><br>';							
							break;
						// media					
							case 'media':
							//echo 1; exit;
							echo '<label for="upload_image">';
							echo '<input name="'.$field_id.'"  id="'.$field_id.'" type="text" size="36" name="upload_image" value="'.$option.'" />';
							echo '<input class="upload_image_button" id="uploader_'.$field_id.'" type="button" value="Upload Image" />';
							echo '<br /><div class="lp_tooltip tool_media" title="'.$field['desc'].'"></div>'; 
							break;
						// checkbox
						case 'checkbox':
							$i = 1;
							echo "<table>";				
							if (!isset($option)){$option=array();}
							elseif (!is_array($option)){
								$option = array($option);
							}
							foreach ($field['options'] as $value=>$label) {
								if ($i==5||$i==1)
								{
									echo "<tr>";
									$i=1;
								}
									echo '<td><input type="checkbox" name="'.$field_id.'[]" id="'.$field_id.'" value="'.$value.'" ',in_array($value,$option) ? ' checked="checked"' : '','/>';
									echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label></td>';					
								if ($i==4)
								{
									echo "</tr>";
								}
								$i++;
							}
							echo "</table>";
							echo '<br><div class="lp_tooltip tool_checkbox" title="'.$field['desc'].'"></div>';
						break;
						// radio
						case 'radio':
							foreach ($field['options'] as $value=>$label) {
								//echo $meta.":".$field_id;
								//echo "<br>";
								echo '<input type="radio" name="'.$field_id.'" id="'.$field_id.'" value="'.$value.'" ',$option==$value ? ' checked="checked"' : '','/>';
								echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label> &nbsp;&nbsp;&nbsp;&nbsp;';								
							}
							echo '<div class="lp_tooltip tool_radio" title="'.$field['desc'].'"></div>';
						break;
						// select
						case 'dropdown':
							echo '<select name="'.$field_id.'" id="'.$field_id.'">';
							foreach ($field['options'] as $value=>$label) {
								echo '<option', $option == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
							}
							echo '</select><br /><div class="lp_tooltip tool_dropdown" title="'.$field['desc'].'"></div>';
						break;
						case 'html':
							//print_r($field);
							echo $option;
							echo '<br /><div class="lp_tooltip tool_dropdown" title="'.$field['desc'].'"></div>';
						break;
						


					} //end switch
			echo '</td></tr>';
		} // end foreach
		echo '</table>'; // end table
	}
}