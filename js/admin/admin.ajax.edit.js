// Prep Data and Save
    var nonce_val = lp_post_edit_ui.wp_landing_page_meta_nonce; // NEED CORRECT NONCE
    jQuery(document).on('mousedown', '.new-save-lp', function () {
        var type_input = jQuery(this).parent().find("input").attr("type");
        var type_select = jQuery(this).parent().find("select");
        // var the_conversion_area_editor = jQuery(this).parent().parent().find('#lp-conversion-area_ifr').length;
        jQuery(this).parent().find(".lp-success-message").hide();
       // var the_content_editor = jQuery(this).parent().parent().find('#wp_content_ifr').length;
        var type_wysiwyg = jQuery(this).parent().parent().find('iframe').length;

        var type_textarea = jQuery(this).parent().find("textarea");
        if (typeof (type_input) != "undefined" && type_input !== null) {
            var type_of_field = type_input;
        } else if (typeof (type_wysiwyg) != "undefined" && type_wysiwyg !== null && type_wysiwyg === 1) {
            var type_of_field = 'wysiwyg';
        } else if (typeof (type_textarea) != "undefined" && type_textarea !== null) {
            var type_of_field = 'textarea';
        } else {
            (typeof (type_select) != "undefined" && type_select)
            var type_of_field = 'select';
        }
        // console.log(type_of_field); // type of input
        var new_value_meta_input = jQuery(this).parent().find("input").val();
        //console.log(new_value_meta_input); 
        var new_value_meta_select = jQuery(this).parent().find("select").val();
        var new_value_meta_textarea = jQuery(this).parent().find("textarea").val();
       // console.log(new_value_meta_select); 
        var new_value_meta_radio = jQuery(this).parent().find("input:checked").val();
        var new_value_meta_checkbox = jQuery(this).parent().find('input[type="checkbox"]:checked').val();
        var new_wysiwyg_meta = jQuery(this).parent().parent().find("iframe").contents().find("body").html();
        // prep data
        if (typeof (new_value_meta_input) != "undefined" && new_value_meta_input !== null && type_of_field == "text") {
            var meta_to_save = new_value_meta_input;
        } else if (typeof (new_value_meta_textarea) != "undefined" && new_value_meta_textarea !== null && type_of_field == "textarea") {
            var meta_to_save = new_value_meta_textarea;
        } else if (typeof (new_value_meta_select) != "undefined" && new_value_meta_select !== null) {
            var meta_to_save = new_value_meta_select;
        } else if (typeof (new_value_meta_radio) != "undefined" && new_value_meta_radio !== null && type_of_field == "radio") {
            var meta_to_save = new_value_meta_radio;
        } else if (typeof (new_value_meta_checkbox) != "undefined" && new_value_meta_checkbox !== null && type_of_field == "checkbox") {
            var meta_to_save = new_value_meta_checkbox;
        } else if (typeof (new_wysiwyg_meta) != "undefined" && new_wysiwyg_meta !== null && type_of_field == "wysiwyg") {
            var meta_to_save = new_wysiwyg_meta;
            //alert('here');  
        } else {
            var meta_to_save = "";
        }

        // if data exists save it
        // console.log(meta_to_save);

        var this_meta_id = jQuery(this).attr("id"); // From save button
        console.log(this_meta_id);
        var post_id = jQuery("#post_ID").text();
        console.log(post_id);
        console.log(meta_to_save);
            
        // Run Ajax
        jQuery.ajax({
            type: 'POST',
            url: lp_post_edit_ui.ajaxurl,
            context: this,
            data: {
                action: 'wp_landing_page_meta_save',
                meta_id: this_meta_id,
                new_meta_val: meta_to_save,
                page_id: post_id,
                nonce: nonce_val
            },

            success: function (data) {
                var self = this;

                //alert(data);
                // jQuery('.lp-form').unbind('submit').submit();
                //var worked = '<span class="success-message-map">Success! ' + this_meta_id + ' set to ' + meta_to_save + '</span>';
                var worked = '<span class="lp-success-message">Updated!</span>';
                var s_message = jQuery(self).parent();
                jQuery(worked).appendTo(s_message);
                jQuery(self).parent().find("lp-success-message").remove();
                jQuery(self).hide();
                jQuery('.reload').click();
                //alert("Changes Saved!");
            },

            error: function (MLHttpRequest, textStatus, errorThrown) {
                alert("Ajax not enabled");
            }
        });
        
        //reload_preview();    
        return false;    
            
    });