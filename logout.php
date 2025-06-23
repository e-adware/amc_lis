<?php
session_start();
include('includes/connection.php');

$date=date("Y-m-d");
$time=date("H:i:s");

$emp_id=base64_decode($_GET['Piuoi87yL8jhjUyl']);

if($emp_id)
{
	$ip_addr=$_SERVER["REMOTE_ADDR"];
	
	mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','normal','$date','$time','$ip_addr') ");
	
	session_destroy();
	
	setcookie("91E03M03P6I1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6C1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6PSS", "", time()-(60*60*24*7),"/");
	
	echo "<script>document.location='./'</script>";
}
?>
