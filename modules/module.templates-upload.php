<?php

function lp_templates_upload()
{
	//echo 2;exit;
	
	lp_templates_upload_execute();

	lp_display_upload();
	lp_templates_search();
	
}

function lp_display_upload()
{
?>
	<div class="wrap templates_upload">
		<div class="icon32" id="icon-plugins"><br></div><h2>Install Templates</h2>
		
		<ul class="subsubsub">
			<li class="plugin-install-dashboard"><a href="#search" id='menu_search'>Search</a> |</li>
			<li class="plugin-install-upload"><a class="current" href="#upload" id='menu_upload'>Upload</a> </li>
		</ul>
	
		<br class="clear">
			<h4>Install a template by uploading it in .zip format</h4>
			<p class="install-help">If you have a template in a .zip format, you may install it by uploading it here.</p>
			<form action="" class="wp-upload-form" enctype="multipart/form-data" method="post">
				<input type="hidden" value="<?php echo wp_create_nonce('lp-nonce'); ?>" name="lp_wpnonce" id="_wpnonce">
				<input type="hidden" value="/wp-admin/plugin-install.php?tab=upload" name="_wp_http_referer">
				<label for="pluginzip" class="screen-reader-text">Template zip file</label>
				<input type="file" name="templatezip" id="templatezip">
				<input type="submit" value="Install Now" class="button" id="install-template-submit" name="install-template-submit" disabled="">	
			</form>
	</div>
<?php
}

function lp_templates_search()
{
	//echo 2; exit;
	?>
	
	<div class="wrap templates_search" style='display:none'>
		<div class="icon32" id="icon-plugins"><br></div><h2>Search Templates</h2>

		<ul class="subsubsub">
				<li class="plugin-install-dashboard"><a href="#search" id='menu_search'>Search</a> |</li>
				<li class="plugin-install-upload"><a class="current" href="#upload" id='menu_upload'>Upload</a> </li>
		</ul>
		
		<br class="clear">
			<p class="install-help">Search the Inboundnow marketplace for free and premium templates.</p>
			<form action="edit.php?post_type=landing-page&page=lp_store" method="POST" id="">
				<input type="search" autofocus="autofocus" value="" name="search">
				<label for="plugin-search-input" class="screen-reader-text">Search Templates</label>
				<input type="submit" value="Search Templates" class="button" id="plugin-search-input" name="plugin-search-input">	
			</form>
	</div>
	
	<?php
}

function lp_templates_upload_execute()
{
	// verify nonce
	//print_r($_POST);
	//print_r($_FILES);
	if ($_FILES)
	{		
		//echo $_FILES['templatezip']["tmp_name"];exit;
		if (!wp_verify_nonce($_POST["lp_wpnonce"], 'lp-nonce'))
		{
			return NULL;
		}
		
		include_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		
		$zip = new PclZip( $_FILES['templatezip']["tmp_name"]);
		if ($zip->extract(PCLZIP_OPT_PATH, LANDINGPAGES_PATH.'templates/') == 0) 
		{
			die("There was a problem. Please try again!");
		} else 
		{
			unlink( $_FILES['templatezip']["tmp_name"]);
			echo '<div class="updated"><p>Template uploaded successfully!</div>';
		}
	}
}

function lp_templates_update()
{
	echo 'hello Update!';
}


?>