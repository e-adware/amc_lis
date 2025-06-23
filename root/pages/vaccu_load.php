<?php
include("../../includes/connection.php");

if($_POST["type"]=="vaccu_center")
{
	$sel_center=$_POST["sel_center"];
	
	$vaccu_charge_val=mysqli_fetch_array(mysqli_query($link, " SELECT `vaccu_charge` FROM `company_name` "));
	$vaccu_charge=$vaccu_charge_val["vaccu_charge"];
	if($vaccu_charge=='0')
	{
		$vaccu_charge_vl=mysqli_fetch_array(mysqli_query($link, " SELECT `vacu_charge` FROM `centremaster` WHERE `centreno`='$sel_center' "));
		$vaccu_charge=$vaccu_charge_vl["vacu_charge"];
	}
	
	$center_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename`,`c_discount` FROM `centremaster` WHERE `centreno`='$sel_center' "));
	
	$discount_reason="";
	if($center_info["c_discount"]>0)
	{
		$discount_reason=$center_info["centrename"];
	}
	
	echo $vaccu_charge."@@@".$center_info["c_discount"]."@@@".$discount_reason;
	
}


?>
