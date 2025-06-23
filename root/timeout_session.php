<?php
session_start();

include('../includes/connection.php');

$date=date("Y-m-d");
$time=date("H:i:s");

$emp_id=$_POST["user"];

if($emp_id)
{
	$ip_addr=$_SERVER["REMOTE_ADDR"];
	
	mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','no activity','$date','$time','$ip_addr') ");
	session_destroy();
	echo "1";
}

?>
