<?php
session_start();
include("../../includes/connection.php");

$c_user=$_SESSION["emp_id"];
$ip_addr=$_SERVER["REMOTE_ADDR"];

$date=date("Y-m-d");
$time=date("H:i:s");

$menu=$_POST['menu'];
$level=$_POST['level'];
$emp_id=$_POST['emp_id'];

if($_POST["type"]=="level")
{
	if($level>0)
	{
		$chk=mysqli_num_rows(mysqli_query($link, "select * from menu_access_detail where levelid='$level'"));
		mysqli_query($link, "delete from menu_access_detail where levelid='$level'");
		
		$menus=explode("$",$menu);
		foreach($menus as $m)
		{
			if($m)
			{
			   mysqli_query($link, "INSERT INTO `menu_access_detail`(`levelid`, `par_id`) VALUES ('$level','$m')");
			}
		}
		mysqli_query($link, "INSERT INTO `access_record`(`emp_id`, `level_id`, `user`, `date`, `time`, `ip_addr`) VALUES ('0','$level','$c_user','$date','$time','$ip_addr')");
	}
}

if($_POST["type"]=="user")
{
	if($emp_id>0)
	{
		$chk=mysqli_num_rows(mysqli_query($link, "select * from menu_access_detail_user where emp_id='$emp_id'"));
		mysqli_query($link, "delete from menu_access_detail_user where emp_id='$emp_id'");
		
		$menus=explode("$",$menu);
		foreach($menus as $m)
		{
			if($m)
			{
			   mysqli_query($link, "INSERT INTO `menu_access_detail_user`(`emp_id`, `par_id`) VALUES ('$emp_id','$m')");
			}
		}
		mysqli_query($link, "INSERT INTO `access_record`(`emp_id`, `level_id`, `user`, `date`, `time`, `ip_addr`) VALUES ('$emp_id','0','$c_user','$date','$time','$ip_addr')");
	}
}
?>
