<?php
session_start();

include('../includes/connection.php');

$emp_id=mysqli_real_escape_string($link, $_POST["user"]);
//$emp_id=mysqli_real_escape_string($link, $_SESSION["emp_id"]);

$last_login=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `login_activity` WHERE `emp_id`='$emp_id' ORDER BY `slno` DESC limit 0,1 "));

if($last_login['status']=='1')
{
	$_SESSION['emp_id']=$emp_id;

	$emp_info=mysqli_fetch_array(mysqli_query($link, "select * from employee where emp_id='$emp_id' "));

	$cookie_name = "91E03M03P6I1D";
	$cookie_value = $emp_info["emp_id"];
	setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day

	$cookie_name = "91E03M03P6C1D";
	$cookie_value = $emp_info["emp_code"];
	setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day

	$cookie_name = "91E03M03P6PSS";
	$cookie_value = $emp_info["password"];
	setcookie($cookie_name, $cookie_value, time() + (86400 * 365 * 10), "/"); // 86400 = 1 day
}
else
{
	$ip_addr=$_SERVER["REMOTE_ADDR"];
	
	if($emp_id)
	{
		mysqli_query($link, " INSERT INTO `login_activity`(`emp_id`, `status`, `remark`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','other','$date','$time','$ip_addr') ");
	}
	
	session_destroy();
	
	setcookie("91E03M03P6I1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6C1D", "", time()-(60*60*24*7),"/");
	setcookie("91E03M03P6PSS", "", time()-(60*60*24*7),"/");
}

?>
