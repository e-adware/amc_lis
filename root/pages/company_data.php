<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

if($_POST["type"]=="save_company_name")
{
	$branch_id=$_POST['branch_id'];
	$client_id=$_POST['client_id'];
	$name=mysqli_real_escape_string($link, $_POST['name']);
	$bed=$_POST['bed'];
	$addr=mysqli_real_escape_string($link, $_POST['addr']);
	$city=mysqli_real_escape_string($link, $_POST['city']);
	$pin=mysqli_real_escape_string($link, $_POST['pin']);
	$state=mysqli_real_escape_string($link, $_POST['state']);
	$ph1=mysqli_real_escape_string($link, $_POST['ph1']);
	$ph2=mysqli_real_escape_string($link, $_POST['ph2']);
	$ph3=mysqli_real_escape_string($link, $_POST['ph3']);
	$email=mysqli_real_escape_string($link, $_POST['email']);
	$web=mysqli_real_escape_string($link, $_POST['web']);
	$cer=$_POST['cer'];
	$gst=$_POST['gst'];
	$trade_licence=$_POST['trade_licence'];
	$narcotics=$_POST['narcotics'];
	$bmw=$_POST['bmw'];
	$spirit=$_POST['spirit'];
	$mtp=$_POST['mtp'];
	$fire=$_POST['fire'];
	$pharmacy=$_POST['pharmacy'];
	$i_date=$_POST['i_date'];
	$usr=$_POST['usr'];
	
	mysqli_query($link,"DELETE FROM `company_documents` WHERE `branch_id`='$branch_id'");
	
	//mysqli_query($link,"INSERT INTO `company_name`(`client_id`, `name`, `no_of_bed`, `address`, `phone1`, `phone2`, `phone3`, `email`, `website`, `pincode`, `city`, `state`) VALUES ('$client_id','$name','$bed','$addr','$ph1','$ph2','$ph3','$email','$web','$pin','$city','$state')");
	
	//mysqli_query($link,"INSERT INTO `company_documents`(`branch_id`, `cer`, `gst`, `trade_licence`, `narcotics`, `bmw`, `spirit`, `mtp`, `fire`, `pharmacy`, `lab`, `radiology`) VALUES ('$branch_id','$cer','$gst','$trade_licence','$narcotics','$bmw','$spirit','$mtp','$fire','$pharmacy','$lab','$radio')");
	
	mysqli_query($link," UPDATE `company_name` SET `client_id`='$client_id',`name`='$name',`no_of_bed`='$bed',`address`='$addr',`phone1`='$ph1',`phone2`='$ph2',`phone3`='$ph3',`email`='$email',`website`='$web',`pincode`='$pin',`city`='$city',`state`='$state',`i_date`='$i_date' WHERE `branch_id`='$branch_id' ");
	
	echo "Saved";
}

if($_POST["type"]=="save_reg_fees")
{
	$branch_id=$_POST['branch_id'];
	$opd_fee=$_POST['opd_fee'];
	$opd_emer_fee=$_POST['opd_emer_fee'];
	$opd_val=$_POST['opd_val'];
	$ipd_fee=$_POST['ipd_fee'];
	$ipd_val=$_POST['ipd_val'];
	$vaccu=$_POST['vaccu'];
	$uhidnum=$_POST['uhidnum'];
	$pinnum=$_POST['pinnum'];
	$usr=$_POST['usr'];
	mysqli_query($link,"DELETE FROM `opd_registration_fees` WHERE `branch_id`='$branch_id'");
	mysqli_query($link,"DELETE FROM `ipd_registration_fees` WHERE `branch_id`='$branch_id'");
	
	mysqli_query($link,"INSERT INTO `opd_registration_fees`(`branch_id`, `regd_fee`, `emerg_fee`, `validity`, `user`) VALUES ('$branch_id',$opd_fee','$opd_emer_fee','$opd_val','$usr')");
	
	mysqli_query($link,"INSERT INTO `ipd_registration_fees`(`branch_id`, `regd_fee`, `validity`, `user`) VALUES ('$branch_id','$ipd_fee','$ipd_val','$usr')");
	
	mysqli_query($link,"UPDATE `company_name` SET `vaccu_charge`='$vaccu',`uhid_start`='$uhidnum',`pin_start`='$pinnum' WHERE `branch_id`='$branch_id'");
	
	echo "Saved";
}


if($_POST["type"]=="save_company_fees")
{
	$branch_id=$_POST['branch_id'];
	$fees_id=$_POST['id'];
	$user=$_POST['user'];
	$amount_validity=mysqli_real_escape_string($link, $_POST['val']);
	
	mysqli_query($link, "UPDATE `company_fees` SET `amount_validity`='$amount_validity',`user`='$user' WHERE `fees_id`='$fees_id' AND `branch_id`='$branch_id'");
	
}

?>
