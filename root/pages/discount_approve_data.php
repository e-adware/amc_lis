<?php
include("../../includes/connection.php");
//require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date("H:i:s");
if($_POST["type"]=="approve_discount")
{
	$patient_id=$_POST["patient_id"];
	$pin=$_POST["pin"];
	$dis_amt=$_POST["dis_amt"];
	$user=$_POST["user"];
	
	$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
	if($pat_typ["type"]==1)
	{
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount`,`dis_amt`,`advance`,`balance` FROM `consult_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
		
		if($con_pat_pay_detail["tot_amount"]==$con_pat_pay_detail["advance"])
		{
			$paid=$con_pat_pay_detail["tot_amount"]-$dis_amt;
			$balance=0;
		}else
		{
			$paid=$con_pat_pay_detail["advance"];
			$balance=$con_pat_pay_detail["tot_amount"]-$dis_amt-$con_pat_pay_detail["advance"];
		}
		
		mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `dis_amt`='$dis_amt',`advance`='$paid',`balance`='$balance' WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' ");
		mysqli_query($link, " UPDATE `consult_payment_detail` SET `amount`='$paid' WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' AND `typeofpayment`='A' ");
		
		mysqli_query($link, " UPDATE `discount_approve` SET `dis_amount`='$dis_amt',`approve_by`='$user',`date`='$date',`time`='$time' WHERE `patient_id`='$patient_id' AND `pin`='$pin' ");
	}
	if($pat_typ["type"]==2)
	{
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT `tot_amount`,`dis_amt`,`advance`,`balance` FROM `invest_patient_payment_details` WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' "));
		
		if($con_pat_pay_detail["tot_amount"]==$con_pat_pay_detail["advance"])
		{
			$paid=$con_pat_pay_detail["tot_amount"]-$dis_amt;
			$balance=0;
		}else
		{
			$paid=$con_pat_pay_detail["advance"];
			$balance=$con_pat_pay_detail["tot_amount"]-$dis_amt-$con_pat_pay_detail["advance"];
		}
		
		mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `dis_amt`='$dis_amt',`advance`='$paid',`balance`='$balance' WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' ");
		mysqli_query($link, " UPDATE `invest_payment_detail` SET `amount`='$paid' WHERE `patient_id`='$patient_id' AND `opd_id`='$pin' AND `typeofpayment`='A' ");
		
		mysqli_query($link, " UPDATE `discount_approve` SET `approve_by`='$user',`date`='$date',`time`='$time' WHERE `patient_id`='$patient_id' AND `pin`='$pin' ");
	}
}

?>
