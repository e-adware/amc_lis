<?php
session_start();
include('includes/connection.php');

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="all_device_logout")
{
	$emp_id=$_POST['emp_id'];
	
	$ip_addr=$_SERVER["REMOTE_ADDR"];

	if(mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','other','$date','$time','$ip_addr') "))
	{
		echo "1"; //logged-out -->other
	}else
	{
		echo "2"; // error
	}
}
if($_POST["type"]=="destroy_session")
{
	session_destroy();
	
	setcookie("91E03M03P6I1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6C1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6PSS", "", time()-(60*60*24*7),"/");
	
}
?>
