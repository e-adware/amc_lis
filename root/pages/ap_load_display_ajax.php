<?php
session_start();
include'../../includes/connection.php';

$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
$userid=$qsl['emp_id'];

$type=$_POST['type'];

if($type=="phcategory")  //For indent order
{
	$phid=$_POST['phid'];
	$qrm=mysqli_query($GLOBALS["___mysqli_ston"], "select * from ph_category_master where ph_cate_id='$phid' ");
	$qrm1=mysqli_fetch_array($qrm);
	
	$val=$phid.'@'.$qrm1[ph_cate_name];
	echo $val;
	
}



/////////////////////////////////
elseif($type=="ap_test_id") //for ph ipd credit
{
	$orderid=$_POST['orderid'];
		
	$val=$orderid.'#'.$orderid;
	echo $val;
}


?>
