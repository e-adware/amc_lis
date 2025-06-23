<?php
include("../../includes/connection.php");
require("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

$rupees_symbol="&#x20b9; ";
$type=$_POST['type'];

if($type==1)
{
	$id=$_POST['id'];
	
	$user=mysqli_real_escape_string($link, $_POST["user"]);
	
	$branch_id=mysqli_real_escape_string($link, $_POST["branch_id"]);
	$center_no=mysqli_real_escape_string($link, $_POST["center_no"]);
	
	$save_type=mysqli_real_escape_string($link, $_POST["save_type"]);
	$p_type_id=mysqli_real_escape_string($link, $_POST["p_type_id"]);
	
	$patient_id=mysqli_real_escape_string($link, $_POST["patient_id"]);
	$opd_id=mysqli_real_escape_string($link, $_POST["opd_id"]);
	
	$pat_reg_type=mysqli_real_escape_string($link, $_POST["pat_reg_type"]);
	
	$source_id=mysqli_real_escape_string($link, $_POST["patient_type"]);
	$name_title=mysqli_real_escape_string($link, $_POST["name_title"]);
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	
	$pat_name_full=trim($name_title." ".$pat_name);
	
	$sex=mysqli_real_escape_string($link, $_POST["sex"]);
	$dob=mysqli_real_escape_string($link, $_POST["dob"]);
	$phone=mysqli_real_escape_string($link, $_POST["phone"]);
	$marital_status=mysqli_real_escape_string($link, $_POST["marital_status"]);
	$email=mysqli_real_escape_string($link, $_POST["email"]);
	$father_name=mysqli_real_escape_string($link, $_POST["father_name"]);
	$mother_name=mysqli_real_escape_string($link, $_POST["mother_name"]);
	$gd_name=mysqli_real_escape_string($link, $_POST["gd_name"]);
	$g_relation=mysqli_real_escape_string($link, $_POST["g_relation"]);
	$gd_phone=mysqli_real_escape_string($link, $_POST["gd_phone"]);
	$income_id=mysqli_real_escape_string($link, $_POST["income_id"]);
	$state=mysqli_real_escape_string($link, $_POST["state"]);
	$district=mysqli_real_escape_string($link, $_POST["district"]);
	$city=mysqli_real_escape_string($link, $_POST["city"]);
	$police=mysqli_real_escape_string($link, $_POST["police"]);
	$post_office=mysqli_real_escape_string($link, $_POST["post_office"]);
	$pin=mysqli_real_escape_string($link, $_POST["pin"]);
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	
	$refbydoctorid=mysqli_real_escape_string($link, $_POST["refbydoctorid"]);
	$hguide_id=mysqli_real_escape_string($link, $_POST["hguide_id"]);
	
	$test_all=mysqli_real_escape_string($link, $_POST["test_all"]);
	
	$total=mysqli_real_escape_string($link, $_POST["total"]);
	$discount_amount=mysqli_real_escape_string($link, $_POST["now_discount"]);
	$discount_reason=mysqli_real_escape_string($link, $_POST["opd_now_discount_reason"]);
	$now_pay=mysqli_real_escape_string($link, $_POST["opd_now_pay"]);
	$payment_mode=mysqli_real_escape_string($link, $_POST["opd_now_payment_mode"]);
	$balance_reason=mysqli_real_escape_string($link, $_POST["now_balance_reason"]);
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["opd_now_cheque_ref_no"]);
	
	$sel_center=$center_no;
	include("patient_info_save.php");
	mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$patient_id',NULL,'$date','$time','$user','$p_type_id','','$refbydoctorid','$sel_center','$hguide_id','$branch_id') ");
	
	$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `date`='$date' AND `time`='$time' AND `user`='$user' AND `type`='$p_type_id' AND `refbydoctorid`='$refbydoctorid' AND `hguide_id`='$hguide_id' ORDER BY `slno` DESC LIMIT 0,1 "));

	$last_row_num=$last_row["slno"];
	
	$patient_reg_type=$p_type_id;
	include("opd_id_generator.php");
	
	if(mysqli_query($link," UPDATE `uhid_and_opdid` SET `opd_id`='$opd_id',`ipd_serial`='$opd_serial' WHERE `slno`='$last_row_num' "))
	{
		echo $opd_id;
	}
}

if($type==999)
{
	$id=$_POST['id'];
}
?>
