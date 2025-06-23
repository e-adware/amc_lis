<?php
session_start();
include('includes/connection.php');

$id=$_POST['user_id'];
$pword=$_POST['pword'];

$data=mysqli_query($GLOBALS["___mysqli_ston"], "select * from employee where emp_id='$id' and password='$pword' ");
$data1=mysqli_num_rows($data);

if($data1>0)
{
	$_SESSION['emp_id']=$id;
	echo "1";
}
else
{
	echo "2";
}
?>
