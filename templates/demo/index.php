<?php
/**
* Template Name:  Demo Template
*
* @package  WordPress Landing Pages
* @author   David Wells
* @link(homepage, http://www.inboundnow.com)
* @version  1.0
* @example link to example page
*/

/* Step 1: Declare Template Key. This will be automatically detected for you */
$key = lp_get_parent_directory(dirname(__FILE__));
$path = LANDINGPAGES_URLPATH.'templates/'.$key.'/'; // This defines the path to your template folder

/* Define Landing Pages's custom pre-load hook for 3rd party plugin integration */
lp_init();

/* Load Regular WordPress $post data and start the loop */
if (have_posts()) : while (have_posts()) : the_post();

/**
 * Step 2: Pre-load meta data into variables.
 * - These are defined in this templates config.php file 
 * - The config.php values create the metaboxes visible to the user.
 * - We define those meta-keys here to use them in the template.
 */
	$body_color = lp_get_value($post, $key, 'body-color'); 
	$navigation_display = lp_get_value($post, $key, 'display-navigation'); 
	$form_class = lp_get_value($post, $key, 'form-class');
	$sub_headline = lp_get_value($post, $key, 'sub-headline'); 
	$checkbox_example = lp_get_value($post, $key, 'checkbox-example');
	$textarea_content = lp_get_value($post, $key, 'textarea-content');
	$wysiwyg_content = lp_get_value($post, $key, 'wysiwyg-content');
	$media_example = lp_get_value($post, $key, 'media-example');

// alternatively you can use default wordpress 
// example: $body_color = get_post_meta($post->ID, 'demo-body-color', true);

/**
 * Step 3: Insert Your HTML, CSS, & JS below to create the page
 */
?>
<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<title><?php wp_title(); // Define page title ?></title>
	<meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
	<!-- Included CSS Files (Uncompressed) -->
	<!--
	<link rel="stylesheet" href="<?php echo $path; ?>assets/css/foundation.css">
	-->  
	<!-- Included CSS Files (Compressed) -->
	<link rel="stylesheet" href="<?php echo $path; ?>assets/css/foundation.min.css">
	<link rel="stylesheet" href="<?php echo $path; ?>assets/css/app.css">
	<script src="<?php echo $path; ?>assets/js/modernizr.foundation.js"></script>

	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<style type="text/css">
	.lp_right{ float:right; }
	.lp_left{ float:left; }
	aside {margin-bottom: 14px;
	margin-top: 14px;}
  li.menu-item-object-landing-page {display: inline;}
  .template-demo .menu-item {display: inline; padding: 10px;}
.custom_landing_page_nav  {margin-top:7px;} 
ul.menu {margin-left: .5em;
margin-right: .5em;}
	</style>


<?php 
      wp_head(); // Load Normal WordPress wp_head() function
      lp_head(); // Load Landing Pages's custom pre-load hook for 3rd party plugin integration
?>
</head>

<body <?php lp_body_class(); // Defines Custom Body Classes for Advanced User Customization
?> style="background: <?php echo $body_color; ?>;">

<?php /* Optionally display navigation */ 
      if ($navigation_display==1) { ?>
  <div class="row">
    <div class="three columns">
      <h1><img src="http://placehold.it/400x100&text=Logo" /></h1>
    </div>
    <div class="nine columns">
      <ul class="nav-bar right">
         <?php echo wp_nav_menu(); ?> 
        
      </ul>
    </div>
   
  </div>
<?php } // End Display Navigation Setting ?>

<!-- Start Title Area -->
<div class="row">
  <div class="twelve columns">
   <h1><?php lp_main_headline(); // Main Landing Page Headline ?></h1>
  </div>
</div>    
<!-- End Title Area -->

  <!-- Main Page Content and Sidebar -->

  <div class="row" style="background: whitesmoke; border: 1px solid #DDD;">

    <aside class="four columns <?php echo $form_class; ?>">

      <div id="form-area" class="panel">
        <?php lp_conversion_area(); /* Print out form content */ ?>
      </div>

    </aside>

    <!-- End Sidebar -->
    <!-- Main Blog Content -->
    <div class="eight columns" role="content">

      <article>

        <h3><?php echo $sub_headline;?></h3>
        <div class="row">
         <?php the_content(); // Display Main Landing Page Content ?>

      </article>
   

    </div>
    <!-- End Main Content -->
    
  </div>

  <!-- End Main Content and Sidebar -->


  <!-- Footer -->

  <footer class="row">
    <div class="twelve columns">
      <hr />
      <div class="row">
        <div class="six columns">
          <p>&copy; Copyright no one at all. Go to town.</p>
        </div>
        <div class="six columns">
          <ul class="link-list right">
            <li><a href="#">Link 1</a></li>
            <li><a href="#">Link 2</a></li>
            <li><a href="#">Link 3</a></li>
            <li><a href="#">Link 4</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <!-- End Footer -->

<!-- @@@@@@ Hidden Modal Box ---->
  <div id="exampleModal" class="reveal-modal">
    <h2>This is a modal.</h2>
    <p>
      Reveal makes these very easy to summon and dismiss. The close button is simple an anchor with a unicode 
      character icon and a class of <code>close-reveal-modal</code>. Clicking anywhere outside the modal will 
      also dismiss it.
    </p>
    <a class="close-reveal-modal">×</a>
  </div>


  <!-- Included JS Files (Compressed) -->
  <script src="<?php echo $path; ?>assets/js/jquery.js"></script>
  <script src="<?php echo $path; ?>assets/js/foundation.min.js"></script>
  
  <!-- Initialize JS Plugins -->
  <script src="<?php echo $path; ?>assets/js/app.js"></script>

<?php 
endwhile; endif; 
lp_footer(); // Load custom landing footer hook for 3rd party extensions
wp_footer(); // Load normal wordpress footer
?>  
</body>
</html>
