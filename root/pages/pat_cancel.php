<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
$date=date("Y-m-d");
$time=date("H:i:s");
if($_POST["type"]=="pat_test_cancel_check")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$typ=$_POST["typ"];
	if($typ=="test")
	{
		echo $result_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
	}else if($typ=="doc")
	{
		$consult_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `pat_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		//$vital_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		$vital_num=0;
		echo ($consult_num+$vital_num);
	}
	
}
if($_POST["type"]=="pat_test_opd")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$typ=$_POST["typ"];
	$reason=$_POST["reason"];
	$user=$_POST["user"];
	
	if($typ=="test")
	{
		$uhid_opd_id=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='2' "));
		
		mysqli_query($link, " DELETE FROM `uhid_and_opdid_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='2'  ");
		mysqli_query($link, " INSERT INTO `uhid_and_opdid_cancel`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`,`refbydoctorid`,`center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid_opd_id[patient_id]','$uhid_opd_id[opd_id]','$uhid_opd_id[date]','$uhid_opd_id[time]','$uhid_opd_id[user]','2','$uhid_opd_id[ipd_serial]','$uhid_opd_id[refbydoctorid]','$uhid_opd_id[center_no]','$uhid_opd_id[hguide_id]','$uhid_opd_id[branch_id]') ");
		
		// Test Entry
		$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `patient_test_details_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		while($test_val=mysqli_fetch_array($test_qry))
		{
			mysqli_query($link, "  INSERT INTO `patient_test_details_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]') ");
		}
		// Vaccu Entry
		$vaccu_qry=mysqli_query($link, "  SELECT * FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($vaccu=mysqli_fetch_array($vaccu_qry))
		{
			mysqli_query($link, " INSERT INTO `patient_vaccu_details_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date`) VALUES ('$vaccu[patient_id]','$vaccu[opd_id]','$vaccu[ipd_id]','$vaccu[batch_no]','$vaccu[vaccu_id]','$vaccu[rate]','$vaccu[time]','$vaccu[date]') ");
		}
		// Test Result Pathology
		$test_patho_qry=mysqli_query($link, "  SELECT * FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_patho_num=mysqli_num_rows($test_patho_qry);
		if($test_patho_num>0)
		{
			while($test_patho=mysqli_fetch_array($test_patho_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$test_patho[patient_id]','$test_patho[opd_id]','$test_patho[ipd_id]','$test_patho[batch_no]','$test_patho[testid]','$test_patho[paramid]','$test_patho[sequence]','$test_patho[result]','$test_patho[time]','$test_patho[date]','$test_patho[doc]','$test_patho[tech]','$test_patho[main_tech]','$test_patho[for_doc]') ");
			}
		}
		// Test Result Radiology
		$test_radio_qry=mysqli_query($link, "  SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_radio_num=mysqli_num_rows($test_radio_qry);
		if($test_radio_num>0)
		{
			while($test_radio=mysqli_fetch_array($test_radio_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_rad_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `observ`, `doc`, `time`, `date`) VALUES ('$test_radio[patient_id]','$test_radio[opd_id]','$test_radio[ipd_id]','$test_radio[batch_no]','$test_radio[testid]','$test_radio[observ]','$test_radio[doc]','$test_radio[time]','$test_radio[date]') ");
			}
		}
		// Test Result Cardiology
		$test_card_qry=mysqli_query($link, "  SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_card_num=mysqli_num_rows($test_card_qry);
		if($test_card_num>0)
		{
			while($test_card=mysqli_fetch_array($test_card_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_card_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `observ`, `doc`, `time`, `date`) VALUES ('$test_card[patient_id]','$test_card[opd_id]','$test_card[ipd_id]','$test_card[batch_no]','$test_card[testid]','$test_card[observ]','$test_card[doc]','$test_card[time]','$test_card[date]') ");
			}
		}
		// Widal Test
		$widalresult_qry=mysqli_query($link, "  SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$widalresult_num=mysqli_num_rows($widalresult_qry);
		if($widalresult_num>0)
		{
			while($widalresult=mysqli_fetch_array($widalresult_qry))
			{
				mysqli_query($link, " INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`,  `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`) VALUES ('$widalresult[patient_id]','$widalresult[opd_id]','$widalresult[ipd_id]','$widalresult[batch_no]','$widalresult[slno]','$widalresult[F1]','$widalresult[F2]','$widalresult[F3]','$widalresult[F4]','$widalresult[F5]','$widalresult[F6]','$widalresult[DETAILS]','$widalresult[v_User]','$widalresult[main_tech]','$widalresult[doc]','$widalresult[for_doc]','$widalresult[time]','$widalresult[date]') ");
			}
		}
		// Payment
		$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			mysqli_query($link, "  INSERT INTO `invest_patient_payment_details_cancel`(`patient_id`, `opd_id`, `regd_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `balance`, `bal_reason`, `date`, `time`, `user`) VALUES ('$inv_pat_pay_detail[patient_id]','$inv_pat_pay_detail[opd_id]','$inv_pat_pay_detail[regd_fee]','$inv_pat_pay_detail[tot_amount]','$inv_pat_pay_detail[dis_per]','$inv_pat_pay_detail[dis_amt]','$inv_pat_pay_detail[dis_reason]','$inv_pat_pay_detail[advance]','$inv_pat_pay_detail[balance]','$inv_pat_pay_detail[bal_reason]','$inv_pat_pay_detail[date]','$inv_pat_pay_detail[time]','$inv_pat_pay_detail[user]') ");
		
		$inv_pay_detail_qry=mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($inv_pay_detail=mysqli_fetch_array($inv_pay_detail_qry))
		{
			mysqli_query($link, " INSERT INTO `invest_payment_detail_cancel`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `discount`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$inv_pay_detail[patient_id]','$inv_pay_detail[opd_id]','$inv_pay_detail[bill_no]','$inv_pay_detail[payment_mode]','$inv_pay_detail[typeofpayment]','$inv_pay_detail[amount]','$inv_pay_detail[balance]','$inv_pay_detail[discount]','$inv_pay_detail[refund]','$inv_pay_detail[refund_reason]','$inv_pay_detail[cheque_ref_no]','$inv_pay_detail[user]','$inv_pay_detail[time]','$inv_pay_detail[date]') ");
		}
		
		$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
		while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
		{
			mysqli_query($link, " INSERT INTO `payment_detail_all_cancel`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]') ");
		}
		
		$dis_apprv=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `discount_approve` WHERE `patient_id`='$uhid' and `pin`='$opd_id' "));
		if($dis_apprv)
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
			mysqli_query($link, " INSERT INTO `discount_approve_cancel`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$dis_apprv[patient_id]','$dis_apprv[pin]','$dis_apprv[bill_amount]','$dis_apprv[dis_amount]','$dis_apprv[reason]','$dis_apprv[user]','$dis_apprv[approve_by]','$dis_apprv[date]','$dis_apprv[time]') ");
		}
		mysqli_query($link, " DELETE FROM `discount_approve_cancel` WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
		
		// Reason
		mysqli_query($link, "  INSERT INTO `patient_cancel_reason`(`patient_id`, `opd_id`, `ipd_id`, `date`, `time`, `user`, `reason`, `type`) VALUES ('$uhid','$opd_id','','$date','$time','$user','$reason','2') ");
		
		// Delete
		
		mysqli_query($link, " DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='2' ");
		
		mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `patient_total_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_regd_fee` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `phlebo_sample` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_card` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_note` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_rad` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `widalresult` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testreport_print` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		// Edit
		
		mysqli_query($link, " DELETE FROM `edit_counter` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='2' ");
		mysqli_query($link, " DELETE FROM `invest_patient_payment_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `invest_payment_detail_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `patient_test_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `patient_vaccu_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		// Refund
		mysqli_query($link, " DELETE FROM `consult_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	}
	if($typ=="doc")
	{
		$uhid_opd_id=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='1' "));
		
		mysqli_query($link, " DELETE FROM `uhid_and_opdid_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='1' ");
		mysqli_query($link, " INSERT INTO `uhid_and_opdid_cancel`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`,`refbydoctorid`,`center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid_opd_id[patient_id]','$uhid_opd_id[opd_id]','$uhid_opd_id[date]','$uhid_opd_id[time]','$uhid_opd_id[user]','2','$uhid_opd_id[ipd_serial]','$uhid_opd_id[refbydoctorid]','$uhid_opd_id[center_no]','$uhid_opd_id[hguide_id]','$uhid_opd_id[branch_id]') ");
		
		$doc_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		
		mysqli_query($link, "  INSERT INTO `appointment_book_cancel`(`patient_id`, `opd_id`, `dept_id`, `consultantdoctorid`, `appointment_date`, `appointment_day`, `appointment_no`, `user`, `date`, `time`, `emergency`, `visit_fee`, `doctor_session`) VALUES ('$doc_consult[patient_id]','$doc_consult[opd_id]','$doc_consult[dept_id]','$doc_consult[consultantdoctorid]','$doc_consult[appointment_date]','$doc_consult[appointment_day]','$doc_consult[appointment_no]','$doc_consult[user]','$doc_consult[date]','$doc_consult[time]','$doc_consult[emergency]','$doc_consult[visit_fee]','$doc_consult[doctor_session]') ");
		
		// Test Entry
		$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($test_val=mysqli_fetch_array($test_qry))
		{
			mysqli_query($link, "  INSERT INTO `patient_test_details_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]') ");
		}
		// Vaccu Entry
		$vaccu_qry=mysqli_query($link, "  SELECT * FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($vaccu=mysqli_fetch_array($vaccu_qry))
		{
			mysqli_query($link, " INSERT INTO `patient_vaccu_details_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date`) VALUES ('$vaccu[patient_id]','$vaccu[opd_id]','$vaccu[ipd_id]','$vaccu[batch_no]','$vaccu[vaccu_id]','$vaccu[rate]','$vaccu[time]','$vaccu[date]') ");
		}
		// Test Result Pathology
		$test_patho_qry=mysqli_query($link, "  SELECT * FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_patho_num=mysqli_num_rows($test_patho_qry);
		if($test_patho_num>0)
		{
			while($test_patho=mysqli_fetch_array($test_patho_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `paramid`, `sequence`, `result`, `time`, `date`, `doc`, `tech`, `main_tech`, `for_doc`) VALUES ('$test_patho[patient_id]','$test_patho[opd_id]','$test_patho[ipd_id]','$test_patho[batch_no]','$test_patho[testid]','$test_patho[paramid]','$test_patho[sequence]','$test_patho[result]','$test_patho[time]','$test_patho[date]','$test_patho[doc]','$test_patho[tech]','$test_patho[main_tech]','$test_patho[for_doc]') ");
			}
		}
		// Test Result Radiology
		$test_radio_qry=mysqli_query($link, "  SELECT * FROM `testresults_rad` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_radio_num=mysqli_num_rows($test_radio_qry);
		if($test_radio_num>0)
		{
			while($test_radio=mysqli_fetch_array($test_radio_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_rad_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `observ`, `doc`, `time`, `date`) VALUES ('$test_radio[patient_id]','$test_radio[opd_id]','$test_radio[ipd_id]','$test_radio[batch_no]','$test_radio[testid]','$test_radio[observ]','$test_radio[doc]','$test_radio[time]','$test_radio[date]') ");
			}
		}
		// Test Result Cardiology
		$test_card_qry=mysqli_query($link, "  SELECT * FROM `testresults_card` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_card_num=mysqli_num_rows($test_card_qry);
		if($test_card_num>0)
		{
			while($test_card=mysqli_fetch_array($test_card_qry))
			{
				mysqli_query($link, "  INSERT INTO `testresults_card_cancel`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `observ`, `doc`, `time`, `date`) VALUES ('$test_card[patient_id]','$test_card[opd_id]','$test_card[ipd_id]','$test_card[batch_no]','$test_card[testid]','$test_card[observ]','$test_card[doc]','$test_card[time]','$test_card[date]') ");
			}
		}
		
		// Widal Test
		$widalresult_qry=mysqli_query($link, "  SELECT * FROM `widalresult` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$widalresult_num=mysqli_num_rows($widalresult_qry);
		if($widalresult_num>0)
		{
			while($widalresult=mysqli_fetch_array($widalresult_qry))
			{
				mysqli_query($link, " INSERT INTO `widalresult`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`,  `slno`, `F1`, `F2`, `F3`, `F4`, `F5`, `F6`, `DETAILS`, `v_User`, `main_tech`, `doc`, `for_doc`, `time`, `date`) VALUES ('$widalresult[patient_id]','$widalresult[opd_id]','$widalresult[ipd_id]','$widalresult[batch_no]','$widalresult[slno]','$widalresult[F1]','$widalresult[F2]','$widalresult[F3]','$widalresult[F4]','$widalresult[F5]','$widalresult[F6]','$widalresult[DETAILS]','$widalresult[v_User]','$widalresult[main_tech]','$widalresult[doc]','$widalresult[for_doc]','$widalresult[time]','$widalresult[date]') ");
			}
		}
		
		// Medicine Entry
		$medicine_qry=mysqli_query($link, "  SELECT * FROM `medicine_check` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($medicine_val=mysqli_fetch_array($medicine_qry))
		{
			mysqli_query($link, " INSERT INTO `medicine_check_cancel`(`patient_id`, `opd_id`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `duration`, `unit_days`, `total_days`, `instruction`, `sos`, `date`, `time`, `user`) VALUES ('$medicine_val[patient_id]','$medicine_val[opd_id]','$medicine_val[item_code]','$medicine_val[dosage]','$medicine_val[units]','$medicine_val[frequency]','$medicine_val[start_date]','$medicine_val[duration]','$medicine_val[unit_days]','$medicine_val[total_days]','$medicine_val[instruction]','$medicine_val[sos]','$medicine_val[date]','$medicine_val[time]','$medicine_val[user]') ");
		}
		
		// Patient Complaints Entry
		$pat_com_qry=mysqli_query($link, " SELECT * FROM `pat_complaints` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($pat_com=mysqli_fetch_array($pat_com_qry))
		{
			mysqli_query($link, " INSERT INTO `pat_complaints_cancel`(`patient_id`, `opd_id`, `comp_one`, `comp_two`, `comp_three`, `date`, `time`, `user`) VALUES ('$pat_com[patient_id]','$pat_com[opd_id]','$pat_com[comp_one]','$pat_com[comp_two]','$pat_com[comp_three]','$pat_com[date]','$pat_com[time]','$pat_com[user]') ");
		}
		// Patient Diagnosis Entry
		$pat_com_qry=mysqli_query($link, " SELECT * FROM `pat_diagnosis` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($pat_com=mysqli_fetch_array($pat_com_qry))
		{
			mysqli_query($link, " INSERT INTO `pat_diagnosis_cancel`(`patient_id`, `opd_id`, `diagnosis`, `order`, `certainity`, `date`, `time`, `user`) VALUES ('$pat_com[patient_id]','$pat_com[opd_id]','$pat_com[diagnosis]','$pat_com[order]','$pat_com[certainity]','$pat_com[date]','$pat_com[time]','$pat_com[user]') ");
		}
		// Patient Disposition Entry
		$pat_dis_qry=mysqli_query($link, " SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($pat_dis=mysqli_fetch_array($pat_dis_qry))
		{
			mysqli_query($link, " INSERT INTO `pat_disposition_cancel`(`patient_id`, `opd_id`, `disposition`, `date`, `time`, `user`) VALUES ('$pat_dis[patient_id]','$pat_dis[opd_id]','$pat_dis[disposition]','$pat_dis[date]','$pat_dis[time]','$pat_dis[user]') ");
		}
		// Patient Examination Entry
		$pat_exam_qry=mysqli_query($link, " SELECT * FROM `pat_examination` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($pat_exam=mysqli_fetch_array($pat_exam_qry))
		{
			mysqli_query($link, " INSERT INTO `pat_examination_cancel`(`patient_id`, `opd_id`, `history`, `examination`, `date`, `time`, `user`) VALUES ('$pat_exam[patient_id]','$pat_exam[opd_id]','$pat_exam[history]','$pat_exam[examination]','$pat_exam[date]','$pat_exam[time]','$pat_exam[user]') ");
		}
		// Patient Vital Entry
		$pat_vital_qry=mysqli_query($link, " SELECT * FROM `pat_vital` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($pat_vital=mysqli_fetch_array($pat_vital_qry))
		{
			mysqli_query($link, " INSERT INTO `pat_vital_cancel`(`patient_id`, `opd_id`, `weight`, `height`, `medium_circumference`, `BMI_1`, `BMI_2`, `spo2`, `pulse`, `head_circumference`, `PR`, `RR`, `temp`, `systolic`, `diastolic`, `note`, `date`, `time`, `user`) VALUES ('$pat_vital[patient_id]','$pat_vital[opd_id]','$pat_vital[weight]','$pat_vital[height]','$pat_vital[medium_circumference]','$pat_vital[BMI_1]','$pat_vital[BMI_2]','$pat_vital[spo2]','$pat_vital[pulse]','$pat_vital[head_circumference]','$pat_vital[PR]','$pat_vital[RR]','$pat_vital[temp]','$pat_vital[systolic]','$pat_vital[diastolic]','$pat_vital[note]','$pat_vital[date]','$pat_vital[time]','$pat_vital[user]') ");
		}
		
		// Payment
		$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			mysqli_query($link, "  INSERT INTO `consult_patient_payment_details_cancel`(`patient_id`, `opd_id`, `regd_fee`, `emergency_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user`) VALUES ('$con_pat_pay_detail[patient_id]','$con_pat_pay_detail[opd_id]','$con_pat_pay_detail[regd_fee]','$con_pat_pay_detail[emergency_fee]','$con_pat_pay_detail[tot_amount]','$con_pat_pay_detail[dis_per]','$con_pat_pay_detail[dis_amt]','$con_pat_pay_detail[dis_reason]','$con_pat_pay_detail[advance]','$con_pat_pay_detail[refund_amount]','$con_pat_pay_detail[tax_amount]','$con_pat_pay_detail[balance]','$con_pat_pay_detail[bal_reason]','$con_pat_pay_detail[date]','$con_pat_pay_detail[time]','$con_pat_pay_detail[user]') ");
		
		$con_pay_detail_qry=mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		while($con_pay_detail=mysqli_fetch_array($con_pay_detail_qry))
		{
			mysqli_query($link, " INSERT INTO `consult_payment_detail_cancel`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$con_pay_detail[patient_id]','$con_pay_detail[opd_id]','$con_pay_detail[bill_no]','$con_pay_detail[payment_mode]','$con_pay_detail[typeofpayment]','$con_pay_detail[amount]','$con_pay_detail[balance]','$con_pay_detail[balance_reason]','$con_pay_detail[discount]','$con_pay_detail[discount_reason]','$con_pay_detail[refund]','$con_pay_detail[refund_reason]','$con_pay_detail[cheque_ref_no]','$con_pay_detail[user]','$con_pay_detail[time]','$con_pay_detail[date]') ");
		}
		
		$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
		while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
		{
			mysqli_query($link, " INSERT INTO `payment_detail_all_cancel`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]') ");
		}
		
		$dis_apprv=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `discount_approve` WHERE `patient_id`='$uhid' and `pin`='$opd_id' "));
		if($dis_apprv)
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
			mysqli_query($link, " INSERT INTO `discount_approve_cancel`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$dis_apprv[patient_id]','$dis_apprv[pin]','$dis_apprv[bill_amount]','$dis_apprv[dis_amount]','$dis_apprv[reason]','$dis_apprv[user]','$dis_apprv[approve_by]','$dis_apprv[date]','$dis_apprv[time]') ");
		}
		mysqli_query($link, " DELETE FROM `discount_approve_cancel` WHERE `patient_id`='$uhid' and `pin`='$opd_id' ");
		
		// Reason
		mysqli_query($link, "  INSERT INTO `patient_cancel_reason`(`patient_id`, `opd_id`, `ipd_id`, `date`, `time`, `user`, `reason`, `type`) VALUES ('$uhid','$opd_id','','$date','$time','$user','$reason','1') ");
		
		// Delete
		
		mysqli_query($link, " DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='1' ");
		
		mysqli_query($link, " DELETE FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `patient_total_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_regd_fee` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `phlebo_sample` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_card` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_note` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testresults_rad` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `widalresult` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `testreport_print` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `medicine_check` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_complaints` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_diagnosis` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_disposition` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_examination` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		mysqli_query($link, " DELETE FROM `pat_vital` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		// Edit
		mysqli_query($link, " DELETE FROM `appointment_book_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `consult_patient_payment_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `consult_payment_detail_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		mysqli_query($link, " DELETE FROM `edit_counter` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `type`='1' ");
		//~ mysqli_query($link, " DELETE FROM `invest_patient_payment_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		//~ mysqli_query($link, " DELETE FROM `invest_payment_detail_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		//~ mysqli_query($link, " DELETE FROM `patient_test_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		//~ mysqli_query($link, " DELETE FROM `patient_vaccu_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'  ");
		
		// Refund
		mysqli_query($link, " DELETE FROM `consult_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	}
}
if($_POST["type"]=="casual_pat_cancel")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["ipd"];
	$reason=$_POST["reason"];
	$user=$_POST["user"];
	
	//$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
	
	//mysqli_query($link, " DELETE FROM `patient_info_cancel` WHERE `patient_id`='$uhid' ");
	//mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$pat_info[name]','$pat_info[gd_name]','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$pat_info[address]','$pat_info[email]','$pat_info[refbydoctorid]','$pat_info[center_no]','$pat_info[user]','$pat_info[payment_mode]','$pat_info[blood_group]','$pat_info[date]','$pat_info[time]')");
	
	//$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$uhid' "));
	
	//mysqli_query($link, " DELETE FROM `patient_info_rel_cancel` WHERE `patient_id`='$uhid' ");
	//mysqli_query($link, " INSERT INTO `patient_info_rel`(`patient_id`, `credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`) VALUES ('$pat_info[patient_id]','$pat_info[credit]','$pat_info[gd_phone]','$pat_info[crno]','$pat_info[pin]','$pat_info[police]','$pat_info[state]','$pat_info[district]','$pat_info[city]','$pat_info[file_no]') ");
	
	$uhid_opd_id=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
	
	mysqli_query($link, " DELETE FROM `uhid_and_opdid_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " INSERT INTO `uhid_and_opdid_cancel`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`,`refbydoctorid`,`center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid_opd_id[patient_id]','$uhid_opd_id[opd_id]','$uhid_opd_id[date]','$uhid_opd_id[time]','$uhid_opd_id[user]','2','$uhid_opd_id[ipd_serial]','$uhid_opd_id[refbydoctorid]','$uhid_opd_id[center_no]','$uhid_opd_id[hguide_id]','$uhid_opd_id[branch_id]') ");
	
	$ipd_pat_service_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_pat_service_num=mysqli_num_rows($ipd_pat_service_qry);
	if($ipd_pat_service_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_pat_service_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_pat_service=mysqli_fetch_array($ipd_pat_service_qry))
		{
			mysqli_query($link, " INSERT INTO `ipd_pat_service_details_cancel`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`) VALUES ('$uhid','$opd_id','$ipd_pat_service[group_id]','$ipd_pat_service[service_id]','$ipd_pat_service[service_text]','$ipd_pat_service[ser_quantity]','$ipd_pat_service[rate]','$ipd_pat_service[amount]','$ipd_pat_service[days]','$ipd_pat_service[user]','$ipd_pat_service[time]','$ipd_pat_service[date]','$ipd_pat_service[bed_id]','$ipd_pat_service[doc_id]','$ipd_pat_service[ref_id]') ");
		}
	}
	
	$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
	while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
	{
		mysqli_query($link, " INSERT INTO `payment_detail_all_cancel`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]') ");
	}
	
	$ipd_advance_payment_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_advance_payment_num=mysqli_num_rows($ipd_advance_payment_qry);
	if($ipd_advance_payment_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_advance_payment_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_advance_payment=mysqli_fetch_array($ipd_advance_payment_qry))
		{
			mysqli_query($link, " INSERT INTO `ipd_advance_payment_details_cancel`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$uhid','$opd_id','$ipd_advance_payment[bill_no]','$ipd_advance_payment[tot_amount]','$ipd_advance_payment[discount]','$ipd_advance_payment[amount]','$ipd_advance_payment[balance]','$ipd_advance_payment[refund]','$ipd_advance_payment[pay_type]','$ipd_advance_payment[pay_mode]','$ipd_advance_payment[time]','$ipd_advance_payment[date]','$ipd_advance_payment[user]') ");
		}
	}
	
	$ipd_reg_fee_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_reg_fee_num=mysqli_num_rows($ipd_reg_fee_qry);
	if($ipd_reg_fee_num>0)
	{
		$ipd_reg_fee=mysqli_fetch_array($ipd_reg_fee_qry);
		mysqli_query($link, " DELETE FROM `ipd_pat_reg_fees_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " INSERT INTO `ipd_pat_reg_fees_cancel`(`patient_id`, `ipd_id`, `bill_no`, `total`, `discount`, `paid`, `mode`, `date`, `time`, `user`) VALUES ('$ipd_reg_fee[patient_id]','$ipd_reg_fee[ipd_id]','$ipd_reg_fee[bill_no]','$ipd_reg_fee[total]','$ipd_reg_fee[discount]','$ipd_reg_fee[paid]','$ipd_reg_fee[mode]','$ipd_reg_fee[date]','$ipd_reg_fee[time]','$ipd_reg_fee[user]') ");
	}
	
	$doc_serv_qry=mysqli_query($link, " SELECT * FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$doc_serv_num=mysqli_num_rows($doc_serv_qry);
	if($doc_serv_num>0)
	{
		mysqli_query($link, " DELETE FROM `doctor_service_done_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($doc_serv=mysqli_fetch_array($doc_serv_qry))
		{
			mysqli_query($link," INSERT INTO `doctor_service_done_cancel`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`, `schedule_id`) VALUES ('$uhid','$opd_id','$doc_serv[service_id]','$doc_serv[consultantdoctorid]','$doc_serv[user]','$doc_serv[date]','$doc_serv[time]','$doc_serv[rel_slno]','$doc_serv[schedule_id]') ");
		}
	}
	// OT cancel---------------------------------------------------------------------------------------------------
	$ot_book=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	if($ot_book)
	{
	mysqli_query($link,"INSERT INTO `ot_book_cancel`(`patient_id`, `ipd_id`, `ot_area_id`, `procedure_id`, `consultantdoctorid`, `ot_date`, `scheduled`, `pac_status`, `date`, `time`, `user`, `schedule_id`, `ot_cabin_id`) VALUES ('$ot_book[patient_id]','$ot_book[ipd_id]','$ot_book[ot_area_id]','$ot_book[procedure_id]','$ot_book[consultantdoctorid]','$ot_book[ot_date]','$ot_book[scheduled]','$ot_book[pac_status]','$date','$time','$user','$ot_book[schedule_id]','$ot_book[ot_cabin_id]')");
	
	$ot_sch=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	mysqli_query($link,"INSERT INTO `ot_schedule_cancel`(`schedule_id`, `patient_id`, `ipd_id`, `ot_date`, `start_time`, `end_time`, `ot_no`, `ot_type`, `ot_dept_id`, `grade_id`, `anesthesia_id`, `diagnosis`, `remarks`, `requesting_doc`, `procedure_id`, `date`, `time`, `user`, `leaved`, `ot_cabin_id`) VALUES ('$ot_sch[schedule_id]','$ot_sch[patient_id]','$ot_sch[ipd_id]','$ot_sch[ot_date]','$ot_sch[start_time]','$ot_sch[end_time]','$ot_sch[ot_no]','$ot_sch[ot_type]','$ot_sch[ot_dept_id]','$ot_sch[grade_id]','$ot_sch[anesthesia_id]','$ot_sch[diagnosis]','$ot_sch[remarks]','$ot_sch[requesting_doc]','$ot_sch[procedure_id]','$date','$time','$user','$ot_sch[leaved]','$ot_sch[ot_cabin_id]')");
	
	$ot_shed=mysqli_fetch_array(mysqli_query($link,"SELECT `schedule_id` FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id'"));
	$shed=$ot_shed['schedule_id'];
	
	$ot_serv=mysqli_query($link, " SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	while($sv=mysqli_fetch_array($ot_serv))
	{
		mysqli_query($link,"INSERT INTO `ot_pat_service_details_cancel`(`slno`, `patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$sv[slno]','$sv[patient_id]','$sv[ipd_id]','$sv[schedule_id]','$sv[resourse_id]','$sv[emp_id]','$sv[ot_group_id]','$sv[ot_service_id]','$sv[service_text]','$sv[ser_quantity]','$sv[rate]','$sv[amount]','$sv[days]','$user','$time','$date','$sv[bed_id]')");
	}
	
	//~ $ot_doc_serv=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	//~ while($dsv=mysqli_fetch_array($ot_doc_serv))
	//~ {
		//~ mysqli_query($link,"INSERT INTO `doctor_service_done_cancel`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`, `schedule_id`) VALUES ('$dsv[patient_id]','$dsv[ipd_id]','$dsv[service_id]','$dsv[consultantdoctorid]','$user','$date','$time','$dsv[rel_slno]','$dsv[schedule_id]')");
	//~ }
	
	$ot_rs_qry=mysqli_query($link, " SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed'");
	while($ot_rs=mysqli_fetch_array($ot_rs_qry))
	{
		mysqli_query($link,"INSERT INTO `ot_resource_cancel`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$ot_rs[resourse_id]','$ot_rs[emp_id]')");
	}
	}
	
	//-------------------------------------------------------------------------------------------------------------
	
	// Reason
	mysqli_query($link, " DELETE FROM `patient_cancel_reason` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " INSERT INTO `patient_cancel_reason`(`patient_id`, `opd_id`, `ipd_id`, `date`, `time`, `user`, `reason`, `type`) VALUES ('$uhid','$opd_id','','$date','$time','$user','$reason','$uhid_opd_id[type]') ");
	
	// Delete
	
	mysqli_query($link, " DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `cancel_payment` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	// OT Delete
	if($ot_book)
	{
		mysqli_query($link, " DELETE FROM `ot_book` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		//mysqli_query($link, " DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_surgery_record` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		//mysqli_query($link, " DELETE FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_process` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_room_leaved` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_resource` WHERE `schedule_id`='$shed' ");
	}
	
	// Refund
		mysqli_query($link, " DELETE FROM `consult_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
}

if($_POST["type"]=="ipd_pat_cancel")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["ipd"];
	$reason=mysqli_real_escape_string($link, $_POST["reason"]);
	$user=$_POST["user"];
	
	$uhid_opd_id=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
	
	mysqli_query($link, " DELETE FROM `uhid_and_opdid_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " INSERT INTO `uhid_and_opdid_cancel`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`,`refbydoctorid`,`center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid_opd_id[patient_id]','$uhid_opd_id[opd_id]','$uhid_opd_id[date]','$uhid_opd_id[time]','$uhid_opd_id[user]','2','$uhid_opd_id[ipd_serial]','$uhid_opd_id[refbydoctorid]','$uhid_opd_id[center_no]','$uhid_opd_id[hguide_id]','$uhid_opd_id[branch_id]') ");
	
	$ipd_pat_service_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_pat_service_num=mysqli_num_rows($ipd_pat_service_qry);
	if($ipd_pat_service_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_pat_service_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_pat_service=mysqli_fetch_array($ipd_pat_service_qry))
		{
			mysqli_query($link, " INSERT INTO `ipd_pat_service_details_cancel`(`patient_id`, `ipd_id`, `group_id`, `service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`, `doc_id`, `ref_id`) VALUES ('$uhid','$opd_id','$ipd_pat_service[group_id]','$ipd_pat_service[service_id]','$ipd_pat_service[service_text]','$ipd_pat_service[ser_quantity]','$ipd_pat_service[rate]','$ipd_pat_service[amount]','$ipd_pat_service[days]','$ipd_pat_service[user]','$ipd_pat_service[time]','$ipd_pat_service[date]','$ipd_pat_service[bed_id]','$ipd_pat_service[doc_id]','$ipd_pat_service[ref_id]') ");
		}
	}
	
	$ipd_advance_payment_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_advance_payment_num=mysqli_num_rows($ipd_advance_payment_qry);
	if($ipd_advance_payment_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_advance_payment_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_advance_payment=mysqli_fetch_array($ipd_advance_payment_qry))
		{
			mysqli_query($link, " INSERT INTO `ipd_advance_payment_details_cancel`(`patient_id`, `ipd_id`, `bill_no`, `tot_amount`, `discount`, `amount`, `balance`, `refund`, `pay_type`, `pay_mode`, `time`, `date`, `user`) VALUES ('$uhid','$opd_id','$ipd_advance_payment[bill_no]','$ipd_advance_payment[tot_amount]','$ipd_advance_payment[discount]','$ipd_advance_payment[amount]','$ipd_advance_payment[balance]','$ipd_advance_payment[refund]','$ipd_advance_payment[pay_type]','$ipd_advance_payment[pay_mode]','$ipd_advance_payment[time]','$ipd_advance_payment[date]','$ipd_advance_payment[user]') ");
		}
	}
	
	$payment_detail_all_qry=mysqli_query($link, " SELECT * FROM `payment_detail_all` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
	while($payment_detail_all=mysqli_fetch_array($payment_detail_all_qry))
	{
		mysqli_query($link, " INSERT INTO `payment_detail_all_cancel`(`pay_id`, `patient_id`, `opd_id`, `transaction_no`, `bill_amount`, `already_paid`, `amount`, `discount_amount`, `discount_reason`, `refund_amount`, `refund_reason`, `tax_amount`, `tax_reason`, `balance_amount`, `balance_reason`, `payment_type`, `payment_mode`, `cheque_ref_no`, `user`, `date`, `time`, `encounter`) VALUES ('$payment_detail_all[pay_id]','$payment_detail_all[patient_id]','$payment_detail_all[opd_id]','$payment_detail_all[transaction_no]','$payment_detail_all[bill_amount]','$payment_detail_all[already_paid]','$payment_detail_all[amount]','$payment_detail_all[discount_amount]','$payment_detail_all[discount_reason]','$payment_detail_all[refund_amount]','$payment_detail_all[refund_reason]','$payment_detail_all[tax_amount]','$payment_detail_all[tax_reason]','$payment_detail_all[balance_amount]','$payment_detail_all[balance_reason]','$payment_detail_all[payment_type]','$payment_detail_all[payment_mode]','$payment_detail_all[cheque_ref_no]','$payment_detail_all[user]','$payment_detail_all[date]','$payment_detail_all[time]','$payment_detail_all[encounter]') ");
	}
	
	$ipd_reg_fee_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_reg_fee_num=mysqli_num_rows($ipd_reg_fee_qry);
	if($ipd_reg_fee_num>0)
	{
		$ipd_reg_fee=mysqli_fetch_array($ipd_reg_fee_qry);
		mysqli_query($link, " DELETE FROM `ipd_pat_reg_fees_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " INSERT INTO `ipd_pat_reg_fees_cancel`(`patient_id`, `ipd_id`, `bill_no`, `total`, `discount`, `paid`, `mode`, `date`, `time`, `user`) VALUES ('$ipd_reg_fee[patient_id]','$ipd_reg_fee[ipd_id]','$ipd_reg_fee[bill_no]','$ipd_reg_fee[total]','$ipd_reg_fee[discount]','$ipd_reg_fee[paid]','$ipd_reg_fee[mode]','$ipd_reg_fee[date]','$ipd_reg_fee[time]','$ipd_reg_fee[user]') ");
	}
	
	// Bed Details
	$bed_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	
	mysqli_query($link, " DELETE FROM `ipd_pat_bed_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " INSERT INTO `ipd_pat_bed_details_cancel`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bed_detail[ward_id]','$bed_detail[bed_id]','$bed_detail[user]','$bed_detail[time]','$bed_detail[date]') ");
	
	// IPD Pat Details
	$ipd_pat_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	if($ipd_pat_detail)
	{
		mysqli_query($link, " DELETE FROM `ipd_pat_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " INSERT INTO `ipd_pat_details_cancel`(`patient_id`, `ipd_id`, `user`, `time`, `date`) VALUES ('$ipd_pat_detail[patient_id]','$ipd_pat_detail[ipd_id]','$ipd_pat_detail[user]','$ipd_pat_detail[time]','$ipd_pat_detail[date]') ");
	}
	$ipd_bed_aloc_qry=mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_bed_aloc_num=mysqli_num_rows($ipd_bed_aloc_qry);
	if($ipd_bed_aloc_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_bed_alloc_details_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_bed_aloc=mysqli_fetch_array($ipd_bed_aloc_qry))
		{
			mysqli_query($link, " INSERT INTO `ipd_bed_alloc_details_cancel`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$uhid','$opd_id','$ipd_bed_aloc[ward_id]','$ipd_bed_aloc[bed_id]','$ipd_bed_aloc[alloc_type]','$ipd_bed_aloc[time]','$ipd_bed_aloc[date]','$ipd_bed_aloc[user]') ");
		}
	}
	$doc_serv_qry=mysqli_query($link, " SELECT * FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$doc_serv_num=mysqli_num_rows($doc_serv_qry);
	if($doc_serv_num>0)
	{
		mysqli_query($link, " DELETE FROM `doctor_service_done_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($doc_serv=mysqli_fetch_array($doc_serv_qry))
		{
			mysqli_query($link," INSERT INTO `doctor_service_done_cancel`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`, `schedule_id`) VALUES ('$uhid','$opd_id','$doc_serv[service_id]','$doc_serv[consultantdoctorid]','$doc_serv[user]','$doc_serv[date]','$doc_serv[time]','$doc_serv[rel_slno]','$doc_serv[schedule_id]') ");
		}
	}
	$ipd_pat_medi_qry=mysqli_query($link, " SELECT * FROM `ipd_pat_medicine_post_discharge` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	$ipd_pat_medi_num=mysqli_num_rows($ipd_pat_medi_qry);
	if($ipd_pat_medi_num>0)
	{
		mysqli_query($link, " DELETE FROM `ipd_pat_medicine_post_discharge_cancel` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($ipd_pat_medi=mysqli_fetch_array($ipd_pat_medi_qry))
		{
			mysqli_query($link," INSERT INTO `ipd_pat_medicine_post_discharge_cancel`(`patient_id`, `ipd_id`, `item_code`, `dosage`, `units`, `frequency`, `start_date`, `end_date`, `total_drugs`, `duration`, `unit_days`, `instruction`, `date`, `time`, `user`) VALUES ('$ipd_pat_medi[patient_id]','$ipd_pat_medi[ipd_id]','$ipd_pat_medi[item_code]','$ipd_pat_medi[dosage]','$ipd_pat_medi[units]','$ipd_pat_medi[frequency]','$ipd_pat_medi[start_date]','$ipd_pat_medi[end_date]','$ipd_pat_medi[total_drugs]','$ipd_pat_medi[duration]','$ipd_pat_medi[unit_days]','$ipd_pat_medi[instruction]','$ipd_pat_medi[date]','$ipd_pat_medi[time]','$ipd_pat_medi[user]') ");
		}
	}
	// OT cancel---------------------------------------------------------------------------------------------------
	$ot_book=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ot_book` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
	if($ot_book)
	{
		mysqli_query($link,"INSERT INTO `ot_book_cancel`(`patient_id`, `ipd_id`, `ot_area_id`, `procedure_id`, `consultantdoctorid`, `ot_date`, `scheduled`, `pac_status`, `date`, `time`, `user`, `schedule_id`, `ot_cabin_id`) VALUES ('$ot_book[patient_id]','$ot_book[ipd_id]','$ot_book[ot_area_id]','$ot_book[procedure_id]','$ot_book[consultantdoctorid]','$ot_book[ot_date]','$ot_book[scheduled]','$ot_book[pac_status]','$date','$time','$user','$ot_book[schedule_id]','$ot_book[ot_cabin_id]')");
	
		$ot_sch=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
		mysqli_query($link,"INSERT INTO `ot_schedule_cancel`(`schedule_id`, `patient_id`, `ipd_id`, `ot_date`, `start_time`, `end_time`, `ot_no`, `ot_type`, `ot_dept_id`, `grade_id`, `anesthesia_id`, `diagnosis`, `remarks`, `requesting_doc`, `procedure_id`, `date`, `time`, `user`, `leaved`, `ot_cabin_id`) VALUES ('$ot_sch[schedule_id]','$ot_sch[patient_id]','$ot_sch[ipd_id]','$ot_sch[ot_date]','$ot_sch[start_time]','$ot_sch[end_time]','$ot_sch[ot_no]','$ot_sch[ot_type]','$ot_sch[ot_dept_id]','$ot_sch[grade_id]','$ot_sch[anesthesia_id]','$ot_sch[diagnosis]','$ot_sch[remarks]','$ot_sch[requesting_doc]','$ot_sch[procedure_id]','$date','$time','$user','$ot_sch[leaved]','$ot_sch[ot_cabin_id]')");
	
		$ot_shed=mysqli_fetch_array(mysqli_query($link,"SELECT `schedule_id` FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id'"));
		$shed=$ot_shed['schedule_id'];
	
		$ot_serv=mysqli_query($link, " SELECT * FROM `ot_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		while($sv=mysqli_fetch_array($ot_serv))
		{
			mysqli_query($link,"INSERT INTO `ot_pat_service_details_cancel`(`slno`, `patient_id`, `ipd_id`, `schedule_id`, `resourse_id`, `emp_id`, `ot_group_id`, `ot_service_id`, `service_text`, `ser_quantity`, `rate`, `amount`, `days`, `user`, `time`, `date`, `bed_id`) VALUES ('$sv[slno]','$sv[patient_id]','$sv[ipd_id]','$sv[schedule_id]','$sv[resourse_id]','$sv[emp_id]','$sv[ot_group_id]','$sv[ot_service_id]','$sv[service_text]','$sv[ser_quantity]','$sv[rate]','$sv[amount]','$sv[days]','$user','$time','$date','$sv[bed_id]')");
		}
	
		//~ $ot_doc_serv=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
		//~ while($dsv=mysqli_fetch_array($ot_doc_serv))
		//~ {
			//~ mysqli_query($link,"INSERT INTO `doctor_service_done_cancel`(`patient_id`, `ipd_id`, `service_id`, `consultantdoctorid`, `user`, `date`, `time`, `rel_slno`, `schedule_id`) VALUES ('$dsv[patient_id]','$dsv[ipd_id]','$dsv[service_id]','$dsv[consultantdoctorid]','$user','$date','$time','$dsv[rel_slno]','$dsv[schedule_id]')");
		//~ }
			
		$ot_rs_qry=mysqli_query($link, " SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed'");
		while($ot_rs=mysqli_fetch_array($ot_rs_qry))
		{
			mysqli_query($link,"INSERT INTO `ot_resource_cancel`(`schedule_id`, `resourse_id`, `emp_id`) VALUES ('$shed','$ot_rs[resourse_id]','$ot_rs[emp_id]')");
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------
	
	
	// Reason
	mysqli_query($link, " DELETE FROM `patient_cancel_reason` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " INSERT INTO `patient_cancel_reason`(`patient_id`, `opd_id`, `ipd_id`, `date`, `time`, `user`, `reason`, `type`) VALUES ('$uhid','$opd_id','','$date','$time','$user','$reason','3') ");
	
	// Delete
	
	mysqli_query($link, " DELETE FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_advance_payment_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `cancel_payment` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `discharge_request` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	
	mysqli_query($link, " DELETE FROM `ipd_pat_bed_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_bed_alloc_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_reg_fees` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_medicine_post_discharge` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_discharge_balance_pat` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	mysqli_query($link, " DELETE FROM `ipd_pat_discharge_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
	// OT Delete
	if($ot_book)
	{
		mysqli_query($link, " DELETE FROM `ot_book` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_schedule` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		//mysqli_query($link, " DELETE FROM `ipd_pat_service_details` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_surgery_record` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		//mysqli_query($link, " DELETE FROM `doctor_service_done` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_process` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_room_leaved` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `ot_resource` WHERE `schedule_id`='$shed' ");
	}
	
	// Refund
		mysqli_query($link, " DELETE FROM `consult_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		mysqli_query($link, " DELETE FROM `invest_payment_refund_details_cancel` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
}

?>
