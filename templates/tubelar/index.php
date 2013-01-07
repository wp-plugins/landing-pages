<?php 
/*****************************************/
// Template Title:  Tubelar
// Plugin: Landing Pages - Inboundnow.com
/*****************************************/

/* Declare Template Key */
$key = lp_get_parent_directory(dirname(__FILE__)); 
$path = LANDINGPAGES_URLPATH.'templates/'.$key.'/';
$url = plugins_url();
/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
lp_init();

/* Load $post data */
if (have_posts()) : while (have_posts()) : the_post();
	
	/* Pre-load meta data into variables */
	$form = lp_get_value($post, 'lp', 'form', true); //this value sourced from ../landing-pages.php
	$yt_video = lp_get_value($post, $key, 'yt-video');
	$logo = lp_get_value($post, $key, 'logo');  
	$sidebar = lp_get_value($post, $key, 'sidebar'); 
	$controls = lp_get_value($post, $key, 'controls');
	$boxcolor = lp_get_value($post, $key, 'box-color');
	$textcolor = lp_get_value($post, $key, 'text-color');
	$clear_bg_settings = lp_get_value($post, $key, 'clear-bg-settings');
	$social_display = lp_get_value($post, $key, 'display-social'); 
	$submit_button_color = lp_get_value($post, $key, 'submit-button-color'); 
	
// Convert Hex to RGB Value for submit button
function Hex_2_RGB($hex) {
        $hex = ereg_replace("#", "", $hex);
        $color = array();
 
        if(strlen($hex) == 3) {
            $color['r'] = hexdec(substr($hex, 0, 1) . $r);
            $color['g'] = hexdec(substr($hex, 1, 1) . $g);
            $color['b'] = hexdec(substr($hex, 2, 1) . $b);
        }
        else if(strlen($hex) == 6) {
            $color['r'] = hexdec(substr($hex, 0, 2));
            $color['g'] = hexdec(substr($hex, 2, 2));
            $color['b'] = hexdec(substr($hex, 4, 2));
        }
 
        return $color;
        
}
$RBG_array = Hex_2_RGB($submit_button_color);
$red = $RBG_array['r'];
$green = $RBG_array["g"];
$blue = $RBG_array["b"];
	// function to parse url and grab id
	function youtubeid($url) {
	        if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $match)) {
	                $match = $match[0];
	        }
	        return $match;
	}

	$videoid = youtubeid($yt_video);?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php wp_title(); ?></title>
		<?php /* Load all functions hooked to lp_head including global js and global css */
			wp_head(); // Load Regular WP Head
			lp_head(); // Load Custom Landing Page Specific Header Items
		?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
	<?php wp_enqueue_script('sharrre', LANDINGPAGES_URLPATH . 'js/sharrre/jquery.sharrre-1.3.3.min.js'); ?>
	<link href="<?php echo $path; ?>assets/css/screen.css" rel="stylesheet" type="text/css" /> 
	<style type="text/css">
	.black-65 {background: url('<?php echo $path; ?>assets/img/black-65-trans.png');}
	<?php if ($sidebar == "lp_left") { echo "#main {float: right;} #sidebar { width: 320px;}"; }?>
	<?php if ($textcolor != "") { echo "#wrapper {color: #$textcolor;}  #video-controls a {color: #$textcolor;} 
										input[type=\"text\"], input[type=\"email\"] {
						                border: 1px solid #$textcolor;
						            	opacity: 0.8;}"; } ?>	
	<?php if ($clear_bg_settings === "transparent"){
			if ($boxcolor != "") { echo ".black-50{background: url('".$path."image.php?hex=$boxcolor');}"; }
		} 	?>  
	<?php if ($clear_bg_settings === "solid"){
		echo ".black-50{background: #$boxcolor}";
	} ?>	 
	 <?php if ($submit_button_color != "") {
          echo"input[type='submit'] {
               background: -moz-linear-gradient(rgba($red,$green,$blue, 0.5), rgba($red,$green,$blue, 0.7));
               background: -ms-linear-gradient(rgba($red,$green,$blue, 0.5), rgba($red,$green,$blue, 0.7));
               background: -o-linear-gradient(rgba($red,$green,$blue, 0.5), rgba($red,$green,$blue, 0.7));
               background: -webkit-gradient(linear, 0 0, 0 100%, from(rgba($red,$green,$blue, 0.5)), to(rgba($red,$green,$blue, 0.7)));
               background: -webkit-linear-gradient(rgba($red,$green,$blue, 0.5), rgba($red,$green,$blue, 0.7));
               background: linear-gradient(rgba($red,$green,$blue, 0.5), rgba($red,$green,$blue, 0.7));
                border: 1px solid #000;}";
           }
        ?> 
	</style>
	<script type="text/javascript" charset="utf-8" src="<?php echo $path; ?>assets/js/jquery.tubular.1.0.js"></script>
    <script type="text/javascript">
		$('document').ready(function() {
			var options = { videoId: '<?php echo $videoid; ?>', start: 3 };
			$('#wrapper').tubular(options);
		});
    </script>

</head>
<body>
	<?php  if ($social_display === "1") { // Show Social Media Icons ?>
		<div id="social-share-buttons">
		  <div id="shareme" data-url="<?php the_permalink(); ?>" data-text="<?php the_title();?>"></div>
		</div>
	<?php } ?>

<div id="wrapper" class="clearfix">

	<div id="logo">

		<?php if ($logo != "") { ?>
		<img src="<?php echo $logo; ?>" alt="logo" id="logo" />
		<?php } else { ?>
		<img src="<?php echo $path; ?>assets/img/inbound-now-logo.png" alt="Inbound Now Logo" id="logo" /> 
		<?php } ?>	

	</div>	

	<div id="main">
		
		<div class="black-50">
			<h1><?php the_title(); ?> <?php echo $controls;?></h1>
			
			<?php the_content();?>

		</div>
		

	</div>

	<div id="sidebar">

		<div class="black-50">

			<?php lp_conversion_area(); /* Print out form content */ ?>

		</div>
	</div>
	<?php if ($controls === "1") { // Show video controls ?>
		<div id="controls">
			<p id="video-controls" class="black-50 control-margin"><a href="#" class="tubular-play">Play</a> | <a href="#" class="tubular-pause">Pause</a> <!-- Other Controls | <a href="#" class="tubular-volume-up">Volume Up</a> | <a href="#" class="tubular-volume-down">Volume Down</a> | <a href="#" class="tubular-mute">Mute</a>--></p>
		</div>	
	<?php } ?>


</div><!-- #wrapper -->
<?php 
endwhile; endif; 

lp_footer();
wp_footer();
?> 
<script type="text/javascript">
$(document).ready(function () {
$('#shareme').sharrre({
  share: {
    googlePlus: true,
    facebook: true,
    twitter: true,
    linkedin: true,
    pinterest: true
  },
  buttons: {
    googlePlus: {size: 'medium'},
    facebook: {layout: 'like_count', width: '45'},
    twitter: {count: 'horizontal'},
    linkedin: {counter: 'right'},
    pinterest: {media: 'http://sharrre.com/img/example1.png', description: $('#shareme').data('text'), layout: 'horizontal'}
  },
  enableHover: false,
  enableCounter: false,
  enableTracking: true
});
 });
</script>
</body>
</html>