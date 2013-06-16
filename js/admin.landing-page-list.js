jQuery(document).ready(function($) {
   // Code for landing page list view
   var cats = jQuery("#landing_page_category option").length;
	if ( cats === 0 ){
   	jQuery("#landing_page_category").hide();
   }

    jQuery('.lp-letter').each(function(){
        var draft = jQuery(this).text();
         if ( draft === "" ){
   		jQuery(this).parent().parent().hide();
   		}
    });

    jQuery(".lp-impress-num").each(function(){
 		var empty = jQuery(this).text();
 		 if ( empty === "" ){
   		jQuery(this).parent().text("  no stats yet");
   		}
    });
   /*jQuery(".lp-varation-stat-ul").each(function(){
 		var length = jQuery(this).find("li").length;
 		 if ( length < 3 ){
   		jQuery(this).find("li").first().css("padding-top", "18px");
   		}
    });
*/
      jQuery(".variation-winner-is").each(function(){
    var target = jQuery(this).text();
      jQuery("." + target).css("background-color", "#e2ffc9");
    });

    var hidestats = "<span id='hide-stats'>(Hide Stats)</span><span class='show-stats show-stats-top'>Show Stats</span>";
    jQuery("#stats").append(hidestats);

    jQuery("body").on('click', '#hide-stats', function () {
    	jQuery(".lp-varation-stat-ul").each(function(){
    		jQuery(this).hide();
    });
    	jQuery(".show-stats").show();
    	jQuery("#hide-stats").hide();
    });

    jQuery("body").on('click', '.show-stats-top', function () {
    	jQuery(".lp-varation-stat-ul").each(function(){
    		jQuery(this).show();
    });
    	jQuery(".show-stats").hide();
    	jQuery("#hide-stats").show();
    });

    jQuery("body").on('click', '.show-stats', function () {
    	jQuery(this).hide();
    	jQuery(this).parent().find(".lp-varation-stat-ul").show();
    });
    
 });
