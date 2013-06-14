<?php
//define('WP_DEBUG',true); 
require_once('../../../../wp-admin/admin.php');
$matches = array();
preg_match('/wp-admin/', $_SERVER['HTTP_REFERER'], $matches, null, 0);

$lp_post_id = $_GET['post_id'];

//wp_enqueue_style( 'global' );
wp_enqueue_style( 'colors' );
//wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'ie' );
wp_enqueue_style('lp-css-split-testing-splash', LANDINGPAGES_URLPATH . 'css/admin-split-testing-splash.css');

wp_enqueue_script('utils');
wp_enqueue_script('editor');
wp_enqueue_script('lp-js-split-testing-splash', LANDINGPAGES_URLPATH . 'js/admin.split-testing-splash.js');


do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');

if (isset($_GET['clone']))
{
	lp_split_testing_options_save() ;
	lp_split_testing_clone_popup_display() ;
}
else
{
	lp_split_testing_options_save() ;
	lp_split_testing_options_popup_display() ;
}

function lp_split_testing_options_save()
{
	global $post;
	$data = array();
	
	if ((isset($_POST['nature'])&&$_POST['nature']=='clone'))
	{
		$lp_id = $_POST['lp_post_id'];
		$group_ids = $_POST['lp_group_ids'];
		$clone_id = lp_duplicate_post_create_duplicate($lp_id);
		lp_clone_st_groups($lp_id,$clone_id,$group_ids);
		
		//display success and options
		$data['new_lpid'] = $clone_id;
		lp_display_success("Post cloned and added to group(s)!");
		lp_split_test_display_post_creation_options('copy',$data);
		exit;
	}
	else if (isset($_POST['nature'])&&$_POST['nature']=='update_groups')
	{		
		
		$lp_id = $_POST['lp_post_id'];
		$group_ids = $_POST['lp_group_ids'];
		
		if (isset($_POST['lp_group_ids']))
		{
			$args=array(
			  'post_type' => 'landing-page-group',
			  'post_satus'=>'publish'
			);
			
			$my_query = null;
			$my_query = new WP_Query($args);
			
			if( $my_query->have_posts() ) 
			{
				$i=1;				
				while ($my_query->have_posts()) : $my_query->the_post(); 
					$group_id = get_the_ID();
					$group_data = get_the_content();
					$group_data = json_decode($group_data,true);
					
					$lp_ids = array();
					foreach ($group_data as $key=>$value)
					{
						$lp_ids[] = $key;
					}

					if (in_array($lp_id,$lp_ids)&&!in_array($group_id,$group_ids))
					{
						unset($group_data[$lp_id]);
						//echo 1; exit;
						$this_data = json_encode($group_data);
						//print_r($this_data);
						$new_post = array(
							'ID' => $group_id,
							'post_title' => get_the_title(),
							'post_content' => $this_data,
							'post_status' => 'publish',
							'post_date' => date('Y-m-d H:i:s'),
							'post_author' => 1,
							'post_type' => 'landing-page-group'
						);	
						//print_r($new_post);
						$post_id = wp_update_post($new_post);
					}
					else if (!in_array($lp_id,$lp_ids)&&in_array($group_id,$group_ids))
					{
						//echo 2; exit;
						$group_data[$lp_id]['id'] = $lp_id;
						$group_data[$lp_id]['status'] = 'active';
						$this_data = json_encode($group_data);

						$new_post = array(
							'ID' => $group_id,
							'post_title' => get_the_title(),
							'post_content' => $this_data,
							'post_status' => 'publish',
							'post_date' => date('Y-m-d H:i:s'),
							'post_author' => 1,
							'post_type' => 'landing-page-group'
						);	
						//print_r($new_post);
						$post_id = wp_update_post($new_post);
					}
					
					$i++;
				endwhile;
			}	
		}
		
		//display success and options
		lp_display_success("Saved!");
		lp_split_test_display_post_creation_options('update_groups',$data);
		exit;
	
	}
	else if (isset($_POST['nature'])&&$_POST['nature']=='copy_create')
	{
		$data['group_name'] = $_POST['group_name'];
		$data['old_lpid'] = $_POST['lp_post_id'];
		
		$post = get_post($data['old_lpid']);

		//create copy of post
		$data['new_lpid'] = lp_duplicate_post_create_duplicate($post, 'publish' );

		$group_data[$data['old_lpid']]['id'] = $data['old_lpid'];
		$group_data[$data['old_lpid']]['status'] = 'active';
		$group_data[$data['new_lpid']]['id'] = $data['new_lpid'];
		$group_data[$data['new_lpid']]['status'] = 'active';
		$group_data = json_encode($group_data);
	
		//create new group with copy and landing page inserts
		$data['group_lp_ids'] =  $data['old_lpid'].",".$data['new_lpid'];
		$new_post = array(
			'post_title' => $data['group_name'],
			'post_content' => $group_data,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			'post_author' => 1,
			'post_type' => 'landing-page-group'
		);	
		
		$data['group_id'] = wp_insert_post($new_post);
		
		//display success and options
		lp_display_success("Created new post ".$data['new_lpid']."!");
		lp_split_test_display_post_creation_options('copy_create',$data);
		exit;
	
	}
	else if (isset($_POST['nature'])&&$_POST['nature']=='select_create')
	{
		$data['group_name'] = $_POST['group_name'];
		$data['old_lpid'] = $_POST['lp_post_id'];		
		$post = get_post($data['old_lpid']);
		
		//create new group with copy and landing page inserts
		$data['group_lp_ids'] =  $data['old_lpid'].",".implode(',',$_POST['group_lp_ids']);

		$group_data[$data['old_lpid']]['id'] = $data['old_lpid'];
		$group_data[$data['old_lpid']]['status'] = 'active';
		foreach ($_POST['group_lp_ids'] as $value)
		{
			$group_data[$value]['id'] = $value;
			$group_data[$value]['status'] = 'active';
		}
		$group_data = json_encode($group_data);
		
		$new_post = array(
			'post_title' => $data['group_name'],
			'post_content' => $group_data,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			'post_author' => 1,
			'post_type' => 'landing-page-group'
		);	
		
		$data['group_id'] = wp_insert_post($new_post);
		lp_display_success("Created new group: ".$data['group_name']."!");
		lp_split_test_display_post_creation_options('select_create',$data);
		exit;
	}
	else if (isset($_POST['nature'])&&$_POST['nature']=='blank_create')
	{
		$data['group_name'] = $_POST['group_name'];
		$data['old_lpid'] = $_POST['lp_post_id'];		
		$post = get_post($data['old_lpid']);
		
		//create new blank post
		$data['new_lpid'] = lp_duplicate_post_create_duplicate($post, null, null, $blank = true);
		
		//create new group with copy and landing page inserts
		$data['group_lp_ids'] =  $data['old_lpid'].",".$data['new_lpid'];

		$group_data[$data['old_lpid']]['id'] = $data['old_lpid'];
		$group_data[$data['old_lpid']]['status'] = 'active';
		$group_data[$data['new_lpid']]['id'] = $data['new_lpid'];
		$group_data[$data['new_lpid']]['status'] = 'active';

		$group_data = json_encode($group_data);
		
		$new_post = array(
			'post_title' => $data['group_name'],
			'post_content' => $group_data,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			'post_author' => 1,
			'post_type' => 'landing-page-group'
		);	
		
		$data['group_id'] = wp_insert_post($new_post);
		
		lp_display_success("Created new group: ".$data['group_name']."!");
		lp_display_success("Created new blank landing page!");
		lp_split_test_display_post_creation_options('blank_create',$data);
		exit;
	}
}

function lp_split_testing_options_popup_display() 
{
	global $lp_post_id;
	$post = get_post($lp_post_id); 
	//echo 1; exit;
	//print_r($post);exit;
	       echo '<div class="error"><p>';
        echo "<h3 style='font-weight:normal;'><strong>Please Note</strong> that this version 1 way of running Landing Page split tests will be phases out of the plugin soon.<br><br> Please use the <strong>new and improved A/B testing functionality</strong> directly in the landing page edit screen.";
        echo "</h3><h1><a href=\"#\" onClick=\"window.open('http://www.youtube.com/embed/KJ_EDJAvv9Y?autoplay=1','landing-page','width=640,height=480,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no')\">Watch Video Explanation</a></h1></p></div>";
	?>
	<div class="metabox-holder split-test-ui">
		<div class="meta-box-sortables ui-sortable">
			<h2 id="lp-st-tabs" class="nav-tab-wrapper">				
				<a href="#top#edit-group" id="edit-group" class="nav-tab nav-tab-active">Split Test Groups</a>				
				<a href="#top#create-new-group" id="create-new-group" class="nav-tab">Start New Split Test</a>
				<a href="/wp-content/plugins/landing-pages/modules/module.split-testing-splash.php?post_id=<?php echo $lp_post_id;?>&clone=1" id="clone-option" class="nav-tab">Clone Current Page</a>
			</h2>

			<div class="lp-st-div" id="edit-group-div">
			<h1>Add Current Page to existing Split Test Group</h1>
				<form action='' method='POST'>
				<input type='hidden' name='nature' value='update_groups'>
				<input type='hidden' name='lp_post_id' value='<?php echo $lp_post_id; ?>'>
				<div class='' style='text-align:center;'>
					<div class='postbox' style='text-align:left;padding:20px 20px 0px 20px;'>
						<div class="categorydiv" id="taxonomy-landing_page_category">
							<span class="manage-split" style='font-size:16px;'><strong>Manage which groups this landing page belongs to:</strong></span><br><br>


							<div class="tabs-panel split-page-list" id="landing_page_category-all">
								<ul id="landing_page_categorychecklist" class="list:landing_page_category categorychecklist form-no-clear">		
									<?php
										global $table_prefix;
										$query = "SELECT * FROM {$table_prefix}posts WHERE post_type='landing-page-group' ORDER BY ID DESC";
										$result = mysql_query($query);
										$i=0;
										while ($arr=mysql_fetch_array($result))
										{
											$group_id = $arr['ID'];
											$group_permalink = get_permalink($group_id);
											$group_name = $arr['post_title'];
											$group_link = $arr['guid'];
											$group_data = json_decode($arr['post_content'],true);				
											$data_keys = array_keys($group_data);
											?>
										
											<li id="landing_page-sp-group" class="popular-category">
												<label class="selectit">
													<input value="<?php echo $group_id; ?>" name="lp_group_ids[<?php echo $i; ?>]" id="" type="checkbox"  <?php if (in_array($lp_post_id,$data_keys)){ echo "checked"; } ?>><span class="split-list-label"><?php echo $group_name; ?></span><span class="lp_group_view">(<a href="<?php echo $group_link;?>" title="View A/B Split Group URL in New Tab" target="_blank">View</a>)</span>
														<ul class='split-test-page-list'>
															<li class="split_test_contains" title='$lid'><strong>Currently contains:</strong></li>
															<?php 		
															
															$i=0;
															foreach ($group_data as $key=>$data)
															{
																$this_title = get_the_title($key);
																if ($this_title)
																{
																	echo "<li class='lp_list_styled' title='$key'>$this_title</li>";
																	$b = $i+1;
																	if (array_key_exists($b,$data_keys))
																	{
																		//echo 1; exit;
																		echo "&nbsp;&nbsp;-&nbsp;&nbsp;";
																	}
																}
																$i++;
															}
															?>
														</ul>
															
												</label>
											</li>
											<?php
											$i++;
										}
									?>
								</ul>
							</div>			
						</div>	
							<div style='text-align:right;padding:5px;'><input type="submit" value="Save Placement" accesskey="p" tabindex="5" id="lp-submit-button" class="button-primary" name="Save Placement"></div>
					</div>
				
				</div>
				</form>
			</div>
			<div class="lp-st-div" id="create-new-group-div">
				<div id="start-new-split-group">
				<div class="start_group_creation">
					<h1>Create a new split testing group</h1>
					<center>
					<div id='copy-create'>
					
					<img class="lp_copy_toggle" src="/wp-content/plugins/landing-pages/css/images/clone-create.png">
					</div>
					<div class='lp_or_option'>- or -</div>
					<div id='group-then-create'>
					
					<img class="lp_grouping_toggle" src="/wp-content/plugins/landing-pages/css/images/select-pages.png">
					</div>
					<div class='lp_split_admin_link' style="padding-top:80px;">Alternatively, You can manage your split testing from the <a href="/wp-admin/edit.php?post_type=landing-page&page=lp_split_testing" target="_blank">split testing admin area.</a></div>
					<div id="blank-create">
					<h2>Option 3<span class="lp_group_view">Tiptip</span></h2>
					<img class="lp_blank_toggle" src="/wp-content/plugins/landing-pages/css/images/blank-page.png">
					</div>
				</center>
				</div>
				<div id='lp_st_ng_container_1' class="lp_copy_create_group">	
				<span class="lp_back_toggle">Back to Start New Split Test Options</span>				
					<div class='lp_copy_options'>
						<h1>Copy Page & Start New Group</h1>	
						<p>Copy the current landing page and create a brand new A/B Split test</p>
						<center>
					<form action="" method="POST">
					<input type='hidden' name='nature' value='copy_create'>
					<input type='hidden' name='split_test' value='1'>
					<input type='hidden' name='lp_post_id' value='<?php echo $lp_post_id; ?>'>
					<div style="float:left;">
					<input type="text" autocomplete="off" id="title" value="" tabindex="1" size="30" name="group_name" placeholder='Enter New Group Name Here' class="lp-input-group-name">
					</div>
					<div class='lp-submit-td'>
					<input type="submit" value="Create Copy & Create New Group"  id="lp-submit-button" class="button-primary" >	
					</div>

					</form>	
					</center>
				</div>
				</div>
			
				<div id='lp_st_ng_container_2' class="lp_new_page_create_group">
				<span class="lp_back_toggle">Back to Start New Split Test Options</span>					
					<div class="lp_copy_options">
						
						<p>Start a brand new split test group. This will create a new blank landing page and split test group associated with it.</p>
						<center>
					<form action="" method="POST"  >
					<input type='hidden' name='nature' value='blank_create'>
					<input type='hidden' name='split_test' value='1'>
					<input type='hidden' name='lp_post_id' value='<?php echo $lp_post_id; ?>'>
						<table>
							<tr>
								<td>
									<input type="text" autocomplete="off" id="title" value="" tabindex="1" size="30" name="group_name" placeholder='Enter New Group Name Here'class="lp-input-group-name">
								</td>
								<td  class='lp-submit-td'>
									<input type="submit" value="Create Blank LP & Create New Group"  id="lp-submit-button" class="button-primary" style='width:212px;'>
								</td>
							</tr>
						</table>
					</form>
					</center>
					</div>
				</div>
				
				<div id='lp_st_ng_container_3' class="lp_select_existing_pages_create_group">
				<span class="lp_back_toggle">Back to Start New Split Test Options</span>
					<div class="lp_copy_options">
					<h1>Select Existing Landing Pages & Create Group</h1>	
					<p>Select from the existing landing pages below (hold CMD key to select multiple pages) and create a brand new split test group.</p>		
					<center>
					<form action="" method="POST" >
					<input type='hidden' name='nature' value='select_create'>
					<input type='hidden' name='split_test' value='1'>
					<input type='hidden' name='lp_post_id' value='<?php echo $lp_post_id; ?>'>		
					<div id="group-lander-creation">
						<div id="list-of-landers">
									<?php
										lp_generate_drowndown('group_lp_ids[]', 'landing-page', $selected = 0);
									?>
							</div>
							<div id="lander-name-of-group">
									<input type="text" autocomplete="off" id="title" value="" tabindex="1" size="30" name="group_name" placeholder='Enter New Group Name Here'class="lp-input-group-name">
							</div>
							<div class='lp-submit-td'>
									<input type="submit" value="Create New Group from Selected Pages"  id="lp-submit-button" class="button-primary" style=''>
							</div>
						</div>
					
						
					</form>
					</center>
				</div>
				</div>	
				</div> <!-- end #start-new-split-group -->			
			</div>
		</div>
	</div>
<?php if (isset($_GET['start-group'])) { ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("#create-new-group").click();
    });
</script>
<?php } ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
     	if (jQuery('#edit-group-div #landing_page_categorychecklist li').length == 0) {
		jQuery("#edit-group-div .split-page-list").hide();
		jQuery("#create-new-group").click();
		jQuery(".manage-split").html('No Test Groups Found!<br><br><a href="#top#create-new-group" id="create-new-group">Start New Split Test</a>');
        jQuery("#edit-group-div input").hide();
}
    });
</script>		
	<?php
}	

function lp_split_test_display_post_creation_options($nature,$data)
{
	
	switch($nature) {
		case 'copy_create':
			//$edit_link_old =  get_edit_post_link( $data['old_lpid']);
			$edit_link_new = get_edit_post_link( $data['new_lpid']);
			$admin_url = get_admin_url();
			$edit_group_link = "{$admin_url}edit.php?post_type=landing-page&page=lp_split_testing&edit_group=1&group_name=".$data['group_name']."&group_id=".$data['group_id']."&landing_page_ids=".$data['group_lp_ids']."";
			?>
			<br><br>
			<center>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_link_new; ?>'" style='width:290px;'>Edit newly copied post.</button><br><br>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_group_link; ?>'"  style='width:290px;'>Edit newly created group.</button><br><br>
				<button class="button-secondary" onclick="self.parent.tb_remove();">Close this window and continue editing current page.</button><br><br>
			</center>
			<?php
			break;
		case 'copy':
			//$edit_link_old =  get_edit_post_link( $data['old_lpid']);
			$edit_link_new = get_edit_post_link( $data['new_lpid']);
			$admin_url = get_admin_url();
			?>
			<br><br>
			<center>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_link_new; ?>'" style='width:290px;'>Edit newly copied post.</button><br><br>
				<button class="button-secondary" onclick="self.parent.tb_remove();">Close this window and continue editing current page.</button><br><br>
			</center>
			<?php
			break;
		case 'select_create':
			//$edit_link_old =  get_edit_post_link( $data['old_lpid']);
			//$edit_link_new = get_edit_post_link( $data['new_lpid']);
			$admin_url = get_admin_url();
			$edit_group_link = "{$admin_url}edit.php?post_type=landing-page&page=lp_split_testing&edit_group=1&group_name=".$data['group_name']."&group_id=".$data['group_id']."&landing_page_ids=".$data['group_lp_ids']."";
			?>
			<br><br>
			<center>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_group_link; ?>'"  style='width:290px;'>Edit newly created group.</button><br><br>
				<button class="button-secondary" onclick="self.parent.tb_remove();">Close this window and continue editing current page.</button><br><br>
			</center>
			<?php
			break;
		case 'blank_create':
			//$edit_link_old =  get_edit_post_link( $data['old_lpid']);
			$edit_link_new = get_edit_post_link( $data['new_lpid']);
			$admin_url = get_admin_url();
			$edit_group_link = "{$admin_url}edit.php?post_type=landing-page&page=lp_split_testing&edit_group=1&group_name=".$data['group_name']."&group_id=".$data['group_id']."&landing_page_ids=".$data['group_lp_ids']."";
			?>
			<br><br>
			<center>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_link_new; ?>'" style='width:290px;'>Edit newly created post.</button><br><br>
				<button class="button-secondary" onclick="self.parent.location.href='<?php echo $edit_group_link; ?>'"  style='width:290px;'>Edit newly created group.</button><br><br>
				<button class="button-secondary" onclick="self.parent.tb_remove();" >Close this window and continue editing current page.</button><br><br>
			</center>
			<?php
			break;
		case 'update_groups':
			?>
			<br><br>
			<center>			
				<button class="button-secondary" onclick="self.parent.tb_remove();" >Close this window and continue editing current page.</button><br><br>
			</center>
			<?php
			break;
	}
}

function lp_split_testing_clone_popup_display() 
{
	global $lp_post_id;
	$post = get_post($lp_post_id); 
	//echo 1; exit;
	//print_r($post);exit;
	      echo '<div class="error"><p>';
        echo "<h3 style='font-weight:normal;'><strong>Please Note</strong> that this version 1 way of running Landing Page split tests will be phases out of the plugin soon.<br><br> Please use the <strong>new and improved A/B testing functionality</strong> directly in the landing page edit screen.";
        echo "</h3><h1><a href=\"#\" onClick=\"window.open('http://www.youtube.com/embed/KJ_EDJAvv9Y?autoplay=1','landing-page','width=640,height=480,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no')\">Watch Video Explanation</a></h1></p></div>";
	?>
	<div class="metabox-holder split-test-ui">
		<h2 id="lp-st-tabs" class="nav-tab-wrapper">				
				<a href="/wp-content/plugins/landing-pages/modules/module.split-testing-splash.php?post_id=<?php echo $lp_post_id;?>" id="edit-group" class="nav-tab">Split Test Groups</a>				
				<a href="/wp-content/plugins/landing-pages/modules/module.split-testing-splash.php?post_id=<?php echo $lp_post_id;?>&start-group=1" id="create-new-group" class="nav-tab ">Start New Split Test</a>
				<a href="" id="clone-option" class="nav-tab nav-tab-active">Clone Current Page</a>
			</h2>
		<div class="lp-st-div" id="edit-group-div">
			<form action='' method='POST'>	
			<input type='hidden' name='nature' value='clone'>
			<input type='hidden' name='lp_post_id' value='<?php echo $lp_post_id; ?>'>
			<div class='clone-post-options' style='text-align:center;'>
				<div class='postbox' style='text-align:left;padding:20px 20px 0px 20px;'>
					<div class="categorydiv" id="taxonomy-landing_page_category">
						<span class="clone_description" style='font-size:16px;'><strong>Clone Current Page</strong></span>
						<div class="tabs-panel split-page-list" id="landing_page_category-all">
							<ul id="landing_page_categorychecklist" class="list:landing_page_category categorychecklist form-no-clear">		
								<?php
									global $table_prefix;
									$query = "SELECT * FROM {$table_prefix}posts WHERE post_type='landing-page-group' ORDER BY ID DESC";
									$result = mysql_query($query);
									$i=0;
									while ($arr=mysql_fetch_array($result))
									{
										$group_id = $arr['ID'];
										$group_permalink = get_permalink($group_id);
										$group_name = $arr['post_title'];
										$group_link = $arr['guid'];
										$group_data = json_decode($arr['post_content'],true);				
										$data_keys = array_keys($group_data);
										?>
									
										<li id="landing_page-sp-group" class="popular-category">
											<label class="selectit">
												<input value="<?php echo $group_id; ?>" name="lp_group_ids[<?php echo $i; ?>]" id="" type="checkbox"  <?php if (in_array($lp_post_id,$data_keys)){ echo "checked"; } ?>><span class="split-list-label"><?php echo $group_name; ?></span><span class="lp_group_view">(<a href="<?php echo $group_link;?>" title="View A/B Split Group URL in New Tab" target="_blank">View</a>)</span>
													<ul class='split-test-page-list'>
														<li class="split_test_contains" title='$lid'><strong>Currently contains:</strong></li>
														<?php 		
														
														$i=0;
														foreach ($group_data as $key=>$data)
														{
															$this_title = get_the_title($key);
															if ($this_title)
															{
																echo "<li class='lp_list_styled' title='$key'>$this_title</li>";
																$b = $i+1;
																if (array_key_exists($b,$data_keys))
																{
																	//echo 1; exit;
																	echo "&nbsp;&nbsp;-&nbsp;&nbsp;";
																}
															}
															$i++;
														}
														?>
													</ul>
														
											</label>
										</li>
										<?php
										$i++;
									}
								?>
							</ul>
						</div>			
					</div>	
						<div style='text-align:left;padding:5px;'><input type="submit" value="Clone Page" accesskey="p" tabindex="5" id="lp-submit-button" class="button-primary" name="Clone Page"></div>
				</div>
			
			</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function () {
			if (jQuery('.clone-post-options #landing_page_categorychecklist li').length == 0) {
			jQuery(".clone-post-options .split-page-list").hide();
			jQuery(".clone_description").html("Click the Button to Clone this page");
	}
		});
	</script>		
		
	<?php
}	


?>