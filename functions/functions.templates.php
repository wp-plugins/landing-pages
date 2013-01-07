<?php
// Easily add in horizontal social media buttons to a template
function lp_social_media(){
	wp_enqueue_script('sharrre', LANDINGPAGES_URLPATH . 'js/sharrre/jquery.sharrre-1.3.3.min.js');
 ?>
<style type="text/css">
   #lp-social-buttons{
	margin: 0 auto;
	text-align: center;}
.sharrre .button {
	width: 95px;
	display: inline-block;
	vertical-align: top;
	margin-top: 10px;
}
.linkedin {
    margin-right: -15px;}
</style>
<script type="text/javascript">
jQuery(document).ready(function () {
jQuery('#shareme').sharrre({
  share: {
    googlePlus: true,
    twitter: true,
    linkedin: true,
    pinterest: true,
    facebook: true
  },
  buttons: {
    googlePlus: {size: 'medium'},
    twitter: {count: 'horizontal'},
    linkedin: {counter: 'right'},
    pinterest: {media: 'http://placekitten.com/200/300', description: '<?php the_title();?>', layout: 'horizontal'},
    facebook: {layout: 'like_count', width: '50', colorscheme: 'dark' }
  },
  enableHover: false,
  enableCounter: false,
  enableTracking: true
});
 });
</script>
 <div id="lp-social-buttons">
  <div id="shareme" data-url="<?php the_permalink();?>" data-text="<?php the_title();?>"></div>
</div>
<?php } // end of social media helper function ?>
<?php
// Easily add in horizontal social media buttons to a template
function lp_social_media_vertical(){
  wp_enqueue_script('sharrre', LANDINGPAGES_URLPATH . 'js/sharrre/jquery.sharrre-1.3.3.min.js');
 ?>
<style type="text/css">
  #social-share-buttons iframe {
width:85px !important;
}
#social-share-buttons{
position: absolute;
top: 70px;
width: 70px;
margin-left: 990px;
padding: 10px;
overflow: hidden;
  }
 .sharrre .button{
    width:60px;
    padding: 4px;
  } 
.linkedin {
    margin-bottom: -20px;}
</style>
<script type="text/javascript">
jQuery(document).ready(function () {
jQuery('#shareme').sharrre({
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
    pinterest: {media: 'http://sharrre.com/img/example1.png', description: jQuery('#shareme').data('text'), layout: 'horizontal'}
  },
  enableHover: false,
  enableCounter: false,
  enableTracking: true
});
 });
</script>
<div id="social-share-buttons">
      <div id="shareme" data-url="<?php the_permalink(); ?>" data-text="<?php the_title();?>"></div>
    </div>
<?php } // end of social media helper function ?>