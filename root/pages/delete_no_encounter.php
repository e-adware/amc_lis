<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="delete_no_encounter")
{
	$uhid=$_POST['pat_uhid'];
	
	mysqli_query($link," UPDATE `patient_info` SET `patient_id`='0',`name`='',`gd_name`='',`sex`='',`dob`='',`age`='',`age_type`='',`phone`='',`address`='',`email`='',`refbydoctorid`='0',`center_no`='',`user`='0',`payment_mode`='',`blood_group`='' WHERE `patient_id`='$uhid' ");
	
}

?>
