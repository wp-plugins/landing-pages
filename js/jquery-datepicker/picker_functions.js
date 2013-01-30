jQuery(document).ready(function ($) {

$(function() {
			$('#time-picker').timepicker({ 'timeFormat': 'H:i' });
		  });

var current_val = jQuery(".new-date").val();
var ret = current_val.split(" ");
var current_date = ret[0];
var current_time = ret[1];
jQuery("#date-picker").val(current_date);
jQuery("#time-picker").val(current_time);

jQuery('#date-picker, #time-picker').live('change', function () {
    var date_chosen = jQuery("#date-picker").val();
	var time_chosen = jQuery("#time-picker").val();
	var total_time = date_chosen + " " + time_chosen;
	jQuery(".new-date").val(total_time);

    });

});