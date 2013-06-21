<?php	// Add to edit screen admin view	add_action( 'wp_before_admin_bar_render', 'lp_customizer_admin_bar' );	function lp_customizer_admin_bar() {		global $post;		global $wp_admin_bar;		$screen = get_permalink(); 		if (isset($_GET['template-customize'])&&$_GET['template-customize']=='on') 		{ 			$menu_title = "Turn Off Editor";			$var_number = $_GET['lp-variation-id']; 			$link = apply_filters('lp_customizer_admin_bar_link', $screen.'?lp-variation-id='.$var_number.'');		} 		else 		{ 			$menu_title = "Launch Visual Editor"; 						if (preg_match("/lp-variation-id\=0/i", $screen)) {			  	$screen = get_permalink() . "?template-customize=on"; 			} 			else if (isset($_GET['lp-variation-id']))			{				$var_number = $_GET['lp-variation-id'];			  	$screen = get_permalink() . "?template-customize=on&lp-variation-id=$var_number"; 			}			else			{				$screen = get_permalink() . "?template-customize=on";			}						$link = apply_filters('lp_customizer_admin_bar_link', $screen);		}						if (!is_admin() && $post->post_type=='landing-page') 		{			$wp_admin_bar->add_menu( array(				'id' => 'launch-lp-front-end-customizer',				'title' => $menu_title,				'href' => $link			) );		}	}	// Add Link to landing page list	add_action( 'wp_before_admin_bar_render', 'lp_list_page_admin_bar' );	function lp_list_page_admin_bar() {		global $post;		global $wp_admin_bar;				if (!is_admin() && $post->post_type=='landing-page') 		{			$wp_admin_bar->add_menu( array(			'id' => 'lp-list-pages',			'title' => "View Landing Page List",			'href' => '/wp-admin/edit.php?post_type=landing-page'			) );		}	}	// Kill admin bar on visual editor preview window	if (isset($_GET['cache_bust']))	{		show_admin_bar( false );	}	// Admin Side Print out varaitions toggles for preview iframes	if (isset($_GET['iframe_window']))	{		add_action('admin_enqueue_scripts','lp_ab_previewer_enqueue');		function lp_ab_previewer_enqueue()		{			wp_enqueue_style('lp_ab_testing_customizer_css', LANDINGPAGES_URLPATH . 'css/customizer-ab-testing.css');		}				show_admin_bar( false );				add_action('wp_head', 'lp_preview_iframe');	    function lp_preview_iframe() 		{				$lp_variation = (isset($_GET['lp-variation-id'])) ? $_GET['lp-variation-id'] : '0';			$postid = $_GET['post_id'];						$variations = get_post_meta($postid,'lp-ab-variations', true);			$variations_array = explode(",", $variations);			$post_type_is = get_post_type($postid); ?>						<link rel="stylesheet" href="<?php echo LANDINGPAGES_URLPATH . 'css/customizer-ab-testing.css';?>" />			<style type="text/css">						#variation-list {				position: absolute;				top: 0px;				left:0px;				padding-left: 20px;			}			#variation-list h3 {				text-decoration: none;				border-bottom: none;			}			#variation-list div {				display: inline-block;			}			#current_variation_id, #current-post-id {				display: none !important;			}			<?php if ($post_type_is !== "landing-page") {			echo "#variation-list {display:none !important;}";			} ?>			</style>			<script type="text/javascript">				jQuery(document).ready(function($) {				var current_page = jQuery("#current_variation_id").text();						// reload the iframe preview page (for option toggles)					jQuery('.variation-lp').on('click', function (event) {						varaition_is = jQuery(this).attr("id");						var original_url = jQuery(parent.document).find("#TB_iframeContent").attr("src");						var current_id = jQuery("#current-post-id").text();						someURL = original_url;						splitURL = someURL.split('?'); 						someURL = splitURL[0];						new_url = someURL + "?lp-variation-id=" + varaition_is + "&iframe_window=on&post_id=" + current_id;						jQuery(parent.document).find("#TB_iframeContent").attr("src", new_url);					});				 });				</script>			<?php			if ($variations_array[0] === "")			{				echo '<div id="variation-list" class="no-abtests"><h3>No A/B Tests running for this page</h3>';			} 			else 			{				echo '<div id="variation-list"><h3>Variations:</h3>';				echo '<div id="current_variation_id">'.$lp_variation.'</div>';			}						foreach ($variations_array as $key => $val) 			{				$current_view = ($val == $lp_variation) ? 'current-variation-view' : '';				echo "<div class='variation-lp ".$current_view."' id=". $val . ">";				echo lp_ab_key_to_letter($val);			   				// echo $val; number				echo "</div>";			}			echo "<span id='current-post-id'>$postid</span>";						echo '</div>';		}	}	// NEED ADMIN CHECK HERE	// The loadtiny is specifically to load thing in the module.customizer-display.php iframe (not really working for whatever reason)	if (isset($_GET['page'])&&$_GET['page']=='lp-frontend-editor')	{		add_action('init','lp_customizer_enqueue');		add_action('wp_enqueue_scripts', 'lp_customizer_enqueue');		function lp_customizer_enqueue($hook)		{						wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));			wp_dequeue_script('jquery-cookie');			wp_enqueue_script('jquery-cookie', LANDINGPAGES_URLPATH . 'js/jquery.cookie.js');			wp_enqueue_style( 'wp-admin' );			wp_admin_css('thickbox');			add_thickbox(); 			wp_enqueue_style('lp-admin-css', LANDINGPAGES_URLPATH . 'css/admin-style.css');			wp_enqueue_script('lp-post-edit-ui', LANDINGPAGES_URLPATH . 'js/admin/admin.post-edit.js');			wp_enqueue_script('lp-frontend-editor-js', LANDINGPAGES_URLPATH . 'js/customizer.save.js');			// Ajax Localize			wp_localize_script( 'lp-post-edit-ui', 'lp_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_landing_page_meta_nonce' => wp_create_nonce('wp-landing-page-meta-nonce') ) );			wp_enqueue_script('lp-js-isotope', LANDINGPAGES_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0' );			wp_enqueue_style('lp-css-isotope', LANDINGPAGES_URLPATH . 'js/libraries/isotope/css/style.css');			//jpicker - color picker			wp_enqueue_script('jpicker', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');			wp_localize_script( 'jpicker', 'jpicker', array( 'thispath' => LANDINGPAGES_URLPATH.'js/libraries/jpicker/images/' ));			wp_enqueue_style('jpicker-css', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');			wp_enqueue_style('jpicker-css', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/css/jPicker.css');    			wp_enqueue_style('lp-customizer-frontend', LANDINGPAGES_URLPATH . 'css/customizer.frontend.css'); 			wp_dequeue_script('form-population');			wp_dequeue_script('funnel-tracking');			wp_enqueue_script('jquery-easing', LANDINGPAGES_URLPATH . 'js/jquery.easing.min.js');					}	}    function lp_customizer_show_metabox($post,$key)     {        global $lp_data;        //print_r($lp_data);exit;        $key = $key['args']['key'];                $lp_custom_fields = $lp_data[$key]['options'];        $lp_custom_fields = apply_filters('lp_show_metabox',$lp_custom_fields, $key);                lp_customizer_render_metabox($key,$lp_custom_fields,$post);    }        function lp_customizer_render_metabox($key,$custom_fields,$post)    {        // Use nonce for verification        echo "<input type='hidden' name='lp_{$key}_custom_fields_nonce' value='".wp_create_nonce('lp-nonce')."' />";        // Begin the field table and loop        echo '<div class="form-table" >';        //print_r($custom_fields);exit;        foreach ($custom_fields as $field) {            $raw_option_id = str_replace($key . "-", "", $field['id']);            $label_class = $raw_option_id . "-label";            // get value of this field if it exists for this post            $meta = get_post_meta($post->ID, $field['id'], true);            if ((!isset($meta)&&isset($field['default'])&&!is_numeric($meta))||isset($meta)&&empty($meta)&&isset($field['default'])&&!is_numeric($meta))            {                //echo $field['id'].":".$meta;                //echo "<br>";                $meta = $field['default'];            }                    echo '<div class="'.$field['id'].' '.$raw_option_id.' landing-page-option-row">                    <div class="landing-page-table-header '.$label_class.'"><label for="'.$field['id'].'">'.$field['label'].' <div class="lp_tooltip" title="'.$field['desc'].'"></div></label></div>                    <div class="landing-page-option-td"><a id="click-'.$field['id'].'" class="click-this" href="#'.$field['id'].'">anchor</a>';                    switch($field['type']) {                        // default content for the_content                        case 'default-content':                            echo '<span id="overwrite-content" class="button-secondary">Insert Default Content into main Content area</span><div style="display:none;"><textarea name="'.$field['id'].'" id="'.$field['id'].'" class="default-content" cols="106" rows="6" style="width: 75%; display:hidden;">'.$meta.'</textarea></div>';                            break;                        // text                        case 'colorpicker':                            if (!$meta)                            {                                $meta = $field['default'];                            }                            echo '<input type="text" class="jpicker" style="background-color:#'.$meta.'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" size="5" /><span class="button-primary new-save-lp-frontend" id="'.$field['id'].'" style="margin-left:10px; display:none;">Update</span>';                            break;                        case 'datepicker':                            echo '<div class="jquery-date-picker" id="date-picking">                                <span class="datepair" data-language="javascript">                                          Date: <input type="text" id="date-picker-'.$key.'" class="date start" /></span>                                        Time: <input id="time-picker-'.$key.'" type="text" class="time time-picker" />                                        <input type="hidden" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" class="new-date" value="" >                                                                        </div>';                                    break;                                              case 'text':                            echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" size="30" />';                            break;                        // textarea                        case 'textarea':                            echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" cols="106" rows="6" style="width: 75%;">'.$meta.'</textarea>';                            break;                        // wysiwyg                        case 'wysiwyg':                            echo '<div id="poststuff" class="wysiwyg-editor-area ">';                            wp_editor( $meta, $field['id'], $settings = array() );                              echo "</div>";                            break;                        // media                                            case 'media':                            //echo 1; exit;                            echo '<label for="upload_image">';                            echo '<input name="'.$field['id'].'"  id="'.$field['id'].'" type="text" size="36" name="upload_image" value="'.$meta.'" data-old="'.$meta.'" />';                            echo '<input class="upload_image_button" id="uploader_'.$field['id'].'" type="button" value="Upload Image" /><span class="uploader-save"></span>';                            //echo '<p class="description">'.$field['desc'].'</p>';                             break;                        // checkbox                        case 'checkbox':                            $i = 1;                            echo '<table class="lp_check_box_table" data-old="'.$meta.'">';                                                  if (!isset($meta)){$meta=array();}                            elseif (!is_array($meta)){                                $meta = array($meta);                            }                            foreach ($field['options'] as $value=>$label) {                                if ($i==5||$i==1)                                {                                    echo "<tr>";                                    $i=1;                                }                                    echo '<td><input type="checkbox" name="'.$field['id'].'[]" id="'.$field['id'].'" value="'.$value.'" ',in_array($value,$meta) ? ' checked="checked"' : '','/>';                                    echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label></td>';                                                 if ($i==4)                                {                                    echo "</tr>";                                }                                $i++;                            }                            echo "</table>";                           // echo '<div class="lp_tooltip tool_checkbox" title="'.$field['desc'].'"></div>';                        break;                        // radio                        case 'radio':                            foreach ($field['options'] as $value=>$label) {                                //echo $meta.":".$field['id'];                                //echo "<br>";                                echo '<input type="radio" name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" value="'.$value.'" ',$meta==$value ? ' checked="checked"' : '','/>';                                echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label> &nbsp;&nbsp;&nbsp;&nbsp;';                                                         }                          //  echo '<div class="lp_tooltip" title="'.$field['desc'].'"></div>';                        break;                        // select                        case 'dropdown':                            echo '<select name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" class="'.$raw_option_id.'">';                            foreach ($field['options'] as $value=>$label) {                                echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';                            }                            echo '</select>';                        break;                                            } //end switch            echo '</div></div>';        } // end foreach        echo '</div>'; // end table    }/* Admin Settings page Function */function lp_frontend_editor_screen() {	// show on screen else redirect to another page	if (isset($_GET['frontend-go'])&&$_GET['frontend-go']=='on')	{		$lp_id = $_GET['lp_id'];		$post_type_is = get_post_type($_GET['lp_id']);		$post = get_post($lp_id);		$admin_title = $post->post_title;		$main_headline = lp_main_headline($post,null,true);		$content = lp_content_area($post,null,true);		$lp_conversion_area = lp_conversion_area($post,null,true,false);		$letter = (isset($_GET['letter'])) ? '<span class="variation-letter-top">'.$_GET['letter'].'</span>' : '';		do_action('lp_frontend_editor_screen_pre',$post);		$lp_variation = (isset($_GET['lp-variation-id'])) ? $_GET['lp-variation-id'] : '0';		if ($post_type_is !== "landing-page") { 			echo "<style type='text/css'>.variation-letter-top {display:none;} #lp-top-box{height:0px;} h1 {margin-top:35px;}</style>";		}		?>		<div id="lp-top-box"><div id='lp-options-controls'>  <a style="float:right; margin-right:5px;" class="reload">Reload Preview</a>			<a style="float:right; margin-right:5px;" class="full-size-view">View fullsize</a>			<a style="float:right; margin-right:5px; display:none;" class="shrink-view">Shrink View</a>		 </div> </div>		<!-- The classes/id are important for jquery ajax to fire. don't change -->		<div id="lp-frontend-options-container" class="lp-options-customizer-area">			<h1><?php echo $letter;?><?php echo $admin_title;?></h1>			<div id="post_ID"><?php echo $lp_id;?></div>	  					<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="POST">				<div class="the-title landing-page-option-row">					<a id="click-the-title"  class="click-this" style="display:none;" href="#the-title">anchor</a>						<div class="landing-page-table-header logo-label">							<label for="the-title">Main Headline</label>						</div>						<div class="landing-page-option-td">						<?php if ($post_type_is === "landing-page") { 						lp_display_headline_input('lp-main-headline',$main_headline);						} else {							echo '<input type="text" name="main_title" id="main_title" value="'.$admin_title.'" data-old="'.$admin_title.'" size="30" />';						}						?>													</div>				</div>				<div class="the-content content-<?php echo $lp_variation; ?> landing-page-option-row">					<a id="click-the-content" class="click-this" style="display:none;" href="#the-content">anchor</a>						<div class="landing-page-table-header the-content-label">							<label for="the-content">							The Main Content Area							</label>						</div>					<div>						<div class="landing-page-option-td" id="the-content">							<?php 							lp_wp_editor( $content, 'wp_content', $settings = array('media_buttons' => TRUE, 'teeny' => FALSE) ); 							?>      						</div>					</div>				</div>					<?php if ($post_type_is === "landing-page") { ?>				<div class="lp-conversion-area landing-page-myeditor-<?php echo $lp_variation; ?>  landing-page-option-row">					<a id="click-lp-conversion-area"  class="click-this" style="display:none;" href="#lp-conversion-area">anchor</a>						<div class="landing-page-table-header lp-conversion-area-label">							<label for="lp-conversion-area">Form/Conversion Button Area</label>						</div>						<div>						<div class="landing-page-option-td" id='lp_container_form'>							<?php 							lp_wp_editor( $lp_conversion_area, 'lp-conversion-area', $settings = array() ); 														?>						</div>					</div>				</div>      				<?php }          								$template = get_post_meta($post->ID, 'lp-selected-template', true); 				$template = strtolower($template);  				$key = array();				$key['args']['key'] = $template;								lp_customizer_show_metabox($post,$key) ;								?>							<!-- Need form submit button here -->   			</form>					</div>		<?php 	} else {  		$url = site_url();		header("Location: " . $url . "/wp-admin/edit.php?post_type=landing-page&notice=edit-note");	}     }if (isset($_GET['notice'])&&$_GET['notice']=='edit-note'){	echo "<div style='font-size:28px; text-align:center; position:absolute; left:33%; top:59px;'>Head into the landing page and click on frontend editor button!</div>";}/* End Hidden Settings Page *//************ Main Page Window* This is the page window behind the frames***************//* Not working for some reason: function lp_customizer_preview_window($hook){        wp_register_script('lp-customizer-load-js', LANDINGPAGES_URLPATH . 'js/customizer.load.js', array('jquery'));        wp_enqueue_script('lp-customizer-load-js');}*/if (isset($_GET['template-customize'])&&$_GET['template-customize']=='on'){	add_filter('wp_head', 'lp_launch_customizer');}			// need filter to not load the actual page behind the frames. AKA kill the botton contentfunction lp_launch_customizer() {	//echo "here";exit;	global $post;		$page_id = $post->ID;	$permalink = get_permalink( $page_id );		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);	$lp_variation = (isset($_GET['lp-variation-id'])) ? $_GET['lp-variation-id'] : '0';		$params = '?cache_bust='.$randomString.'&live-preview-area='.$randomString;		$preview_link = $permalink.$params;	$preview_link = apply_filters('lp_customizer_preview_link',$preview_link);		$customizer_link = '/wp-admin/edit.php?post_type=landing-page&page=lp-frontend-editor&frontend-go=on&lp_id='.$page_id.'&loadlpdata=yes';	$customizer_link = apply_filters('lp_customizer_customizer_link',$customizer_link);	//echo $customizer_link;exit;	do_action('lp_launch_customizer_pre',$post);	?>		<style type="text/css">		#wpadminbar {			z-index: 99999999999 !important; 		}				#lp-live-preview #wpadminbar {			margin-top:0px;		}			.lp-load-overlay {			position: absolute;			z-index: 9999999999 !important; 			z-index: 999999;			background-color: #000;			opacity: 0;			background: -moz-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -webkit-gradient(radial,center center,0px,center center,100%,color-stop(0%,rgba(0,0,0,0.4)),color-stop(100%,rgba(0,0,0,0.9)));			background: -webkit-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -o-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -ms-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#66000000',endColorstr='#e6000000',GradientType=1);			-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";			filter: alpha(opacity=50);		}		</style>	<script type="text/javascript">	jQuery(document).ready(function($) {		jQuery("#wp-admin-bar-edit a").text("Main Edit Screen"); 				setTimeout(function() {		      jQuery(document).find("#lp-live-preview").contents().find("#wpadminbar").hide()				   jQuery(document).find("#lp-live-preview").contents().find("html").css("margin-bottom", "-28px");		}, 2000);			 });	</script>   	<?php 			echo '<div class="lp-load-overlay" style="top: 0;bottom: 0; left: 0;right: 0;position: fixed;opacity: .8; display:none;"></div><iframe id="lp_customizer_options" src="'.$customizer_link.'" style="width: 32%; height: 100%; position: fixed; left: 0px; z-index: 999999999; top: 26px;"></iframe>';	echo '<iframe id="lp-live-preview" src="'.$preview_link.'" style="width: 68%; height: 100%; position: fixed; right: 0px; top: 26px; z-index: 999999999; background-color: #eee;	//background-image: linear-gradient(45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194)), linear-gradient(-45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194));	//background-size:25px 25px; background-position: initial initial; background-repeat: initial initial;"></iframe>';	wp_footer();	exit;}/* Preview Iframe Window* This is the preview frame */if (isset($_GET['live-preview-area'])) {     show_admin_bar( false );	/* This filters cause caching issues with the live previews. We need a way to fix that. Find cache fix for wordpress filters		// Do these work? 	//define("QUICK_CACHE_ALLOWED", false); 	//define("DONOTCACHEPAGE", true); 	//define('DONOTCACHCEOBJECT', true); 	//define('DONOTCDN', true); 		// Function to wrap outputted meta in spans for front end editing	//add_filter( 'lp_get_value', 'lp_customizer_add_span_meta' , 10 , 4);	function lp_customizer_add_span_meta( $content , $post = null , $key=null, $id=null) 	{           		$id = apply_filters('lp_customizer_span_id',$id);		$exclude_list = "color|default|tile|repeat-x|repeat-y|left|right"; 		// need to exclude these matches only if exact match with no other content		// Need to exclude /images/img.jpg		// Need to find single strings with only a url to a .png,.jpg, .gif file and exclude		// Check for media upload type and ignore. Also ignore common setting words		//echo $key.':'.$id.":".$content;		//echo "<hr>";		//echo "<br>";		//<img alt="" src="/wp-content/uploads/landing-pages/templates/minimal-responsive/img/placeholder.jpg" /> matches the below preg match but we only want to match the string if its exactly /wp-content/uploads/landing-pages/templates/minimal-responsive/img/placeholder.jpg and nothing else 		if (!@preg_match('/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i', $content)&&!strstr($content,'/wp-content/') && !@preg_match('/^[a-f0-9]{1,}$/is', $content) && $content != "color") {			$content = "<span id='$key-$id' class='live-preview-area-box'>" . $content . "</span>";		}		return $content;	}			//add_filter( 'lp-main-headline', 'lp_customizer_add_span_title' ,99);	function lp_customizer_add_span_title( $content, $id ='title' ) 	{		$id = apply_filters('lp_customizer_span_id' , $id );		$content = "<span id='the-title' class='live-preview-area-box' >" . $content . "</span>";				return $content;	}	// Function to wrap outputted meta in spans for front end editing	//add_filter( 'the_content', 'lp_customizer_add_span_content' );	function lp_customizer_add_span_content( $content , $id = 'content' ) 	{		$id = apply_filters('lp_customizer_span_id', $id );		$content = "<span id='the-content' class='live-preview-area-box' >" . $content . "</span>"; 				return $content;	}	// Function to wrap outputted meta in spans for front end editing	//add_filter( 'lp_conversion_area', 'lp_customizer_add_span_conversion_area' );	function lp_customizer_add_span_conversion_area( $content , $id = 'lp-conversion-area' ) 	{		//echo "here";exit;		$id = apply_filters('lp_customizer_span_id', $id );		$content = "<span id='lp-conversion-area' class='live-preview-area-box' >" . $content . "</span>";				return $content;	}	*/}?>