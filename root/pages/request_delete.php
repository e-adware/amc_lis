<?php
include("../../includes/connection.php");

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="request_delete")
{
	$uhid=$_POST['pat_uhid'];
	$pin=$_POST['pin'];
	$user=$_POST['user'];
	$val=$_POST['val'];
	$remark=mysqli_real_escape_string($link, $_POST["reason"]);
	
	if($val==1)
	{
		mysqli_query($link," INSERT INTO `request_delete`(`patient_id`, `opd_id`, `remark`, `date`, `time`, `user`) VALUES ('$uhid','$pin','$remark','$date','$time','$user') ");
	}
	if($val==0)
	{
		mysqli_query($link," DELETE FROM `request_delete` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' ");
	}
	
}

?>
