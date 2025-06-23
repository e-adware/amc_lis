<?php
session_start();
if(!isset($_SESSION["emp_id"]))
{
	echo "1";
}else
{
	include('../includes/connection.php');
	$last_login=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `login_activity` WHERE `emp_id`='$_SESSION[emp_id]' ORDER BY `slno` DESC limit 0,1 "));
	if($last_login['status']=='0')
	{
		echo "1";
	}
}
?>
