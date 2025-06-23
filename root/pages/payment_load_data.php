<?php
session_start();
include("../../includes/connection.php");
require('../../includes/global.function.php');

$c_user=trim($_SESSION['emp_id']);
$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="payment_mode_change")
{
	$val=$_POST["val"];
	
	$p_mode_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_master` WHERE `p_mode_name`='$val' "));
	
	echo $p_mode_info["ref_field"]."@#@".$p_mode_info["operation"];
}

?>
