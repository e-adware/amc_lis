<?php

$add_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));

$st_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `state` WHERE `state_id`='$add_info[state]' "));

$dist_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `district` WHERE `district_id`='$add_info[district]' "));

$address="";
//~ if($pat_info["gd_name"])
//~ {
	//~ $address="C/O: ".$pat_info["gd_name"].", ";
//~ }
if($add_info["city"])
{
	$address.=$add_info["city"].", ";
}
if($pat_info["address"])
{
	$address.=$pat_info["address"].", ";
}
if($add_info["police"])
{
	$address.="P.S- ".$add_info["police"].", ";
}
if($dist_info["name"])
{
	//$address.=" &nbsp; District- ".$dist_info["name"].", ";
}
if($st_info["name"])
{
	//$address.=" &nbsp; State- ".$st_info["name"].", ";
}
//~ if($add_info["pin"])
//~ {
	//~ $address.="PIN-".$add_info["pin"];
//~ }

//if($pat_info["dob"]!=""){ $age=age_calculator($pat_info["dob"]); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

if($pat_info["dob"]!=""){ $age=age_calculator_date($pat_info["dob"],$reg_date); }else{ $age=$pat_info["age"]." ".$pat_info["age_type"]; }

?>
<!--<br/>-->
<table class="table table-no-top-border">
	<tr>
		<th>UHID</th>
		<th style="font-size: 15px;">: <?php echo $uhid; ?></th>
		<th>Appointment Date</th>
		<th style="font-size: 15px;">: <?php echo date("d-M-Y",strtotime($dt_tm["appointment_date"])); ?></th>
		<th>Reg Date</th>
		<td>: <?php echo date("d-M-Y",strtotime($dt_tm["date"])); ?> <?php echo convert_time($dt_tm["time"]); ?></td>
	</tr>
	<tr>
		<th>Name</th>
		<td>: <?php echo $pat_info["name"]; ?></td>
		<th>Age / Sex</th>
		<td>: <?php echo $age; ?> / <?php echo $pat_info["sex"]; ?></td>
		<th>Phone</th>
		<td>: <?php echo $pat_info["phone"]; ?></td>
	</tr>
	<tr>
		<th>Ref By</th>
		<td>: <?php echo $ref_doc["ref_name"]; ?></td>
		<th>Address</th>
		<td colspan="3">: <?php echo $address; ?></td>
	</tr>
<?php
	if($booking_info["booking_no"]>0)
	{
?>
	<tr>
		<th>Appointment No</th>
		<td colspan="5">: <?php echo $booking_info["booking_no"]; ?></td>
	</tr>
<?php
	}
?>
</table>
