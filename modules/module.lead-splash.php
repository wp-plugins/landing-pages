<?php
//define('WP_DEBUG',true); 
require_once('../../../../wp-admin/admin.php');
$matches = array();
preg_match('/wp-admin/', $_SERVER['HTTP_REFERER'], $matches, null, 0);

$lead_id = $_GET['lead_id'];
$wplead_data = get_post_custom($lead_id);

?>
<div id='lead-details-container'>
<table>
	<tr>
		<td>
			Customer Name:
		</td>
		<td>
			<?php echo $wplead_data['wpleads_first_name'][0]; ?> <?php echo $wplead_data['wpleads_last_name'][0]; ?> 
		</td>
	</tr>
	<tr>
		<td>
			Email Address:
		</td>
		<td>
			<?php echo $wplead_data['wpleads_email_address'][0]; ?>
		</td>
	</tr>
	<tr>
		<td>
			IP Address:
		</td>
		<td>
			<?php echo $wplead_data['wpleads_ip_address'][0]; ?>
		</td>
	</tr>
	<tr>
		<td>
			City:
		</td>
		<td>
			<?php echo $wplead_data['wpleads_city'][0]; ?>
		</td>
	</tr>
	<tr>
		<td>
			State:
		</td>
		<td>
			<?php echo $wplead_data['wpleads_region_name'][0]; ?>
		</td>
	</tr>	
</table>