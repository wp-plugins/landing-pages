<script type='text/javascript'>jQuery(document).ready(function($) {    /***record impressions***/	var impression_data = {		action: 'lp_record_impression',		current_url: '<?php echo trim(str_replace('//','/',"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."/")); ?>'	};	<?php if (isset($_GET['lp-variation-id']) && (!isset($_GET['template-customize']))) { ?>		var current = window.location.href; var cleanparams = current.split("?"); var clean_url = cleanparams[0]; history.replaceState({}, 'landing page', clean_url);		<?php } ?>		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', impression_data, function(response) {		//alert(response);		if (response!=false)		{			//alert('Got this from the server: ' + response + myajax.ajaxurl);			//form.submit();			return true;		}					});				/***look for form conversion elements and record action***/	var form = jQuery('.lp-form-track');	var lp_stop=0;	var conversion_data = {		action: 'lp_record_conversion',		current_url: '<?php echo trim(str_replace('//','/',"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."/")); ?>'	};		if (form.length>0)	{				form.submit(function(e) { 			form_id = jQuery(this).attr('id');			this_form = jQuery(this);			jQuery('#lp-submit-button').css('cursor', 'wait');			jQuery('input').css('cursor', 'wait');			jQuery('body').css('cursor', 'wait');						e.preventDefault();			var email_check = 1;			var submit_halt = 0;						<?php			do_action('lp_js_hook_submit_form_pre',null);			?>						if (<?php echo get_option( 'main-landing-page-auto-format-forms' , 1); ?>==1)			{									if (jQuery('.lp-email-value input').length>0 && jQuery('.lp-email-value input').val().indexOf('@') == -1)				{										email_check = 0;				}			}						if (email_check)			{								jQuery.ajax({					type: "POST",					url: '<?php echo admin_url('admin-ajax.php'); ?>',					data: conversion_data,					dataType: 'json',					timeout: 7000,					success: function (response) {						if (response!=false)						{													<?php							do_action('lp_js_hook_submit_form_success',null);							?>											if (submit_halt===0)							{								setTimeout(function(){ 									if (form_id)									{										jQuery('#'+form_id).unbind('submit');										jQuery('#'+form_id).submit();										jQuery('#'+form_id+':input[type=submit]').click();									}									else									{										this_form.unbind('submit');										this_form.submit();									}        								  }, 300 ); 							}						}						else						{					         if (form_id)							{								jQuery('#'+form_id).unbind('submit');								jQuery('#'+form_id).submit();								jQuery('#'+form_id+':input[type=submit]').click();							}							else							{								this_form.unbind('submit');								this_form.submit();							}						}					},					error: function(request, status, err) {												if(status == "timeout") {							this_form.unbind('submit');							this_form.submit();						}						else						{							this_form.unbind('submit');							this_form.submit();						}						//alert(status);					}				});							}			else			{				if (form_id)				{					jQuery('#'+form_id).unbind('submit');					jQuery('#'+form_id).submit();					jQuery('#'+form_id+' :input[type=submit]').click();				}				else				{					this_form.unbind('submit');					this_form.submit();				}			}						//jQuery('body').css('cursor', 'default');			//jQuery('input').css('cursor', 'default');			//jQuery('#lp-submit-button').css('cursor', 'default');		});			}	/***look for link conversion elements and record action***/	lp_stop=0;		var link = jQuery('.lp-track-link');				if (link.length>0)	{		$(document.body).on('click', link , function(){							<?php			do_action('lp_js_hook_click_link_pre',null);			?>						if (lp_stop == 0)			{				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', conversion_data, function(response) {										<?php					do_action('lp_js_hook_click_link_success',null);					?>										lp_stop=1;						link.click();					return false;				});									}			else			{				return true;			}						});	}		});</script>