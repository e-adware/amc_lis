<?php
include("includes/connection.php");

$date = date("Y-m-d");
$time = date("H:i:s");

$qry = mysqli_query($link, "SELECT `Name`,`Sex`,`password`,`levelid`,`branch_id` FROM `Employee_old` WHERE `Name`!='' ORDER BY `Name` ASC");
while ($data = mysqli_fetch_array($qry)) {
	$name = mysqli_real_escape_string($link, $data["Name"]);

	$password = md5($data["password"]);

	if ($data["levelid"] == "A005") {
		$levelid = 1;
	}

	if ($data["levelid"] == "A004") {
		$levelid = 31;
	}
	if ($data["levelid"] == "A015") {
		$levelid = 10;
	}
	if ($data["levelid"] == "A011") {
		$levelid = 13;
	}
	if ($data["levelid"] == "A009") {
		$levelid = 2;
	}
	if ($data["levelid"] == "A013") {
		$levelid = 8;
	}
	if ($data["levelid"] == "A010") {
		$levelid = 7;
	}
	if ($data["levelid"] == "A007") {
		$levelid = 19;
	}
	if ($data["levelid"] == "A014") {
		$levelid = 25;
	}
	if ($data["levelid"] == "A003") {
		$levelid = 28;
	}
	if ($data["levelid"] == "A017") {
		$levelid = 24;
	}
	if ($data["levelid"] == "A016") {
		$levelid = 3;
	}

	$branch_id = $data["branch_id"];

	if (!$branch_id) {
		$branch_id = 1;
	}

	$centreno_name = $data["name"];

	$centre_info = mysqli_fetch_array(mysqli_query($link, "SELECT `centreno` FROM `centremaster` WHERE `centreno`='$centreno_name'"));
	if ($centre_info) {
		$centreno = $centre_info["centreno"];
	} else {
		if ($branch_id == 1) {
			$centreno = "C100";
		}
		if ($branch_id == 2) {
			$centreno = "C286";
		}
	}

	$str = "INSERT INTO `employee`(`branch_id`, `emp_code`, `name`, `sex`, `dob`, `phone`, `email`, `address`, `password`, `levelid`, `emp_type`, `edit_info`, `edit_payment`, `cancel_pat`, `discount_permission`, `status`, `user`, `api_key`, `centreno`, `amount`) VALUES ('$branch_id',NULL,'$name','Male','0000-00-00','','','','$password','$levelid','1','0','0','0','0','0','101','','$centreno','0')";

	//echo $str."<br>";
	//UPDATE `employee` SET `emp_code`=concat('LCD',`emp_id`,'100'); 
	//INSERT INTO `employee` (`emp_id`, `branch_id`, `emp_code`, `name`, `sex`, `dob`, `phone`, `email`, `address`, `password`, `levelid`, `emp_type`, `edit_info`, `edit_payment`, `cancel_pat`, `discount_permission`, `status`, `user`, `api_key`, `centreno`, `amount`) VALUES ('1', '1', NULL, 'DEVELOPER', 'Male', '0000-00-00', '', '', '', 'b9356b3dc4426d3985efecf84473eab8', '1', '1', '1', '1', '1', '1', '0', '0', '', 'C100', '0');

	mysqli_query($link, $str);
}

if ($_POST) {
	$pass = $_POST["pass"];
	if ($pass == "poiu!1") {
		mysqli_query($link, "TRUNCATE TABLE `qc_baseline`");
		mysqli_query($link, "TRUNCATE TABLE `qc_lot_master`");
		mysqli_query($link, "TRUNCATE TABLE `qc_mapping`");
		mysqli_query($link, "TRUNCATE TABLE `qc_master`");
		mysqli_query($link, "TRUNCATE TABLE `qc_results`");
		mysqli_query($link, "TRUNCATE TABLE `qc_testmaster`");

		mysqli_query($link, " TRUNCATE TABLE `pat_attend_doc_change_record` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_centre_change_record` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_ot_resources` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_ot_schedule` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_ot_schedule_template` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_discharge_summary` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_discharge_summary_baby` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_discharge_summary_obs` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_discharge_summary_template` ");

		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_discharge_summary` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_discharge_summary_nicu` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_asa_grade` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_ari` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_bd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_cd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_drug` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_dtkg` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_ed` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_intubation` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_nd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_rd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_women` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_plan` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_eye_external_exam` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_fractometer` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_history` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_lamp_exam` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_prescribe_power` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_present_power` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_eye_visual` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_antenatal_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_clinic_advice_note` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_clinic_revisit_advice` ");

		mysqli_query($link, " TRUNCATE TABLE `opd_clinic_investigation` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_clinic_medication` ");

		mysqli_query($link, " TRUNCATE TABLE `pathology_repeat_param_details` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_repeat` ");
		mysqli_query($link, " TRUNCATE TABLE `test_sample_result_repeat` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_report_delivery_details` ");

		mysqli_query($link, " TRUNCATE TABLE `employee_record` ");

		mysqli_query($link, " TRUNCATE TABLE `bminusper` ");
		mysqli_query($link, " TRUNCATE TABLE `test_details_data` ");
		mysqli_query($link, " TRUNCATE TABLE `test_details_data_replace` ");

		mysqli_query($link, " TRUNCATE TABLE `data_user_record` ");
		mysqli_query($link, " TRUNCATE TABLE `doctor_approval_record` ");

		mysqli_query($link, " TRUNCATE TABLE `report_sms_email` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_centre_test` ");
		mysqli_query($link, " TRUNCATE TABLE `expensedetail` ");

		mysqli_query($link, " TRUNCATE TABLE `user_pay_settlement_master` ");
		mysqli_query($link, " TRUNCATE TABLE `user_pay_settlement_details` ");
		mysqli_query($link, " TRUNCATE TABLE `pathology_report_print` ");
		mysqli_query($link, " TRUNCATE TABLE `test_sample_result` ");

		mysqli_query($link, " TRUNCATE TABLE `whatsapp_optin_numbers` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_refer_details` ");

		mysqli_query($link, " TRUNCATE TABLE `advance_booking` ");
		mysqli_query($link, " TRUNCATE TABLE `advance_booking_activity` ");
		mysqli_query($link, " TRUNCATE TABLE `test_advance_booking` ");
		mysqli_query($link, " TRUNCATE TABLE `test_advance_booking_activity` ");
		mysqli_query($link, " TRUNCATE TABLE `test_advance_booking_details` ");

		mysqli_query($link, " TRUNCATE TABLE `payment_settlement_doc` ");
		mysqli_query($link, " TRUNCATE TABLE `daily_account_close_new` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_refer_details` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_patient_refer_details` ");

		mysqli_query($link, " TRUNCATE TABLE `lab_sample_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_death_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_staying_time` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_card_details` ");
		mysqli_query($link, " TRUNCATE TABLE `payment_detail_all` ");
		mysqli_query($link, " TRUNCATE TABLE `payment_detail_all_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `payment_detail_all_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `payment_detail_all_cancel` ");

		mysqli_query($link, " TRUNCATE TABLE `ipd_discharge_balance_pat` ");
		mysqli_query($link, " TRUNCATE TABLE `payment_mode_change` ");

		mysqli_query($link, " TRUNCATE TABLE `refund_request` ");
		mysqli_query($link, " TRUNCATE TABLE `refund_request_details` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_refund` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_refund_details` ");

		mysqli_query($link, " TRUNCATE TABLE `baby_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `emergency_patient_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_discharge_balance_pat` ");
		mysqli_query($link, " TRUNCATE TABLE `phlebo_sample_note` ");
		mysqli_query($link, " TRUNCATE TABLE `test_sample_result` ");

		mysqli_query($link, " TRUNCATE TABLE `request_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_visit_type_details` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_expense_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `appointment_book` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_other_info` ");
		mysqli_query($link, " TRUNCATE TABLE `uhid_and_opdid` ");
		mysqli_query($link, " TRUNCATE TABLE `uhid_and_opdid_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `uhid_and_opdid_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pin_double_check` ");
		mysqli_query($link, " TRUNCATE TABLE `request_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_test_details` ");
		mysqli_query($link, " TRUNCATE TABLE `medicine_check` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_patient_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_payment_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_total_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_test_details` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_regd_fee` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_payment_refund_details` ");
		mysqli_query($link, " TRUNCATE TABLE `phlebo_sample` ");
		mysqli_query($link, " TRUNCATE TABLE `lab_sample_receive` ");
		mysqli_query($link, " TRUNCATE TABLE `sample_note` ");
		mysqli_query($link, " TRUNCATE TABLE `sample_note_record` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_test_summary` ");
		mysqli_query($link, " TRUNCATE TABLE `widalresult` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_update` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_note` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_rad` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_card` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_vaccu_details` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_rel` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_rel_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `expense_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_confidential` ");
		mysqli_query($link, " TRUNCATE TABLE `advance_book_link` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_adv` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_rel_adv` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_test_details_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_test_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_vaccu_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_cancel_reason` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_rad_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `testresults_card_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `widalresult_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_payment_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_detail_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `appointment_book_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_patient_payment_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_payment_detail_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `medicine_check_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_complaints_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_diagnosis_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_disposition_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_examination_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_vital_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_dischage_type` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_reg_fees` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_reg_fees_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_final_discharge` ");
		//mysqli_query($link, " TRUNCATE TABLE `` ");


		mysqli_query($link, " TRUNCATE TABLE `bill_ph_sell_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_challan_receipt_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_challan_receipt_master` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_credit_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_return` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_return_master` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_stock_entry` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_order_details` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_order_details_temp` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_order_master` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_receipt_details` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_receipt_master` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_purchase_receipt_temp ` "); //
		mysqli_query($link, " TRUNCATE TABLE `ph_sell_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_sell_details_temp` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_sell_master` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_sell_master_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_stock_master` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_stock_process` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_supplier_master` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_complaints` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_consultation` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_diagnosis` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_disposition` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_examination` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_vital` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_info` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_patient_payment_details` ");

		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_relation` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_doc_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_doc_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_diagnosis` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_bed_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_bed_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_bed_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_bed_details_temp` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_advance_payment_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_advance_payment_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_advance_payment_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_given` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_vital` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_ip_consultation` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_bed_alloc_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_bed_alloc_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_complaints` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_post_discharge` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_equipment` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_consumable` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_examination` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_history_collection` ");
		mysqli_query($link, " TRUNCATE TABLE `bill_patient_test_details` ");
		mysqli_query($link, " TRUNCATE TABLE `bill_ipd_ip_consultation` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_delivery_det` "); //devilery details
		mysqli_query($link, " TRUNCATE TABLE `bill_ipd_bed_details` ");
		mysqli_query($link, " TRUNCATE TABLE `bill_ipd_pat_equipment` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_insurance_det` "); // patient insurance details
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_discharge_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_details_update` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_details_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `bill_ipd_pat_consumable` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_sur_consumable` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_payment_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_schedule` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_surgery_record` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_advance_payment_details_temp` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_doc_transfer` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_doc_transfer_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `cash_deposit` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_medicine_detail` ");

		mysqli_query($link, " TRUNCATE TABLE `cancel_request` ");
		mysqli_query($link, " TRUNCATE TABLE `cancel_request_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `delete_cancel_request` ");
		mysqli_query($link, " TRUNCATE TABLE `approve_cancel_request` ");



		mysqli_query($link, " TRUNCATE TABLE `blood_screwing_details` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_request` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_receipt` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_issue` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_donor_rejected` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_donor_reg` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_donor_inventory` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_crossmatch` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_component_stock` ");
		mysqli_query($link, " TRUNCATE TABLE `blood_component_expired` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_book` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_surgery_record_resource` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_notes` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_post_surgery` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_pre_anaesthesia` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_pac_status` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_resource` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_pat_service_details` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_link_test_service` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_process` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_room_leaved` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_clinical_resourse` ");
		//mysqli_query($link, " TRUNCATE TABLE `` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_dischage_type` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_final_discharge` ");
		mysqli_query($link, " TRUNCATE TABLE `cancel_payment` ");
		mysqli_query($link, " TRUNCATE TABLE `edit_counter` ");
		mysqli_query($link, " TRUNCATE TABLE `appointment_book_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_patient_payment_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `consult_payment_detail_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_vaccu_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_test_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_payment_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_detail_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `dosage_master` ");
		mysqli_query($link, " TRUNCATE TABLE `doctor_service_done` ");
		mysqli_query($link, " TRUNCATE TABLE `doctor_service_done_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_final` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_medicine_indent` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_service_delete` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_significant_investigation` ");
		mysqli_query($link, " TRUNCATE TABLE `link_test_service` ");
		mysqli_query($link, " TRUNCATE TABLE `login_activity` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_health_guide` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_health_guide_adv` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_ref_doc` ");
		mysqli_query($link, " TRUNCATE TABLE `pat_visit_record` ");
		mysqli_query($link, " TRUNCATE TABLE `discharge_request` ");
		mysqli_query($link, " TRUNCATE TABLE `cross_consultation` ");
		mysqli_query($link, " TRUNCATE TABLE `daily_account_close` ");
		mysqli_query($link, " TRUNCATE TABLE `discharge_cancel_record` ");
		mysqli_query($link, " TRUNCATE TABLE `discount_approve` ");
		mysqli_query($link, " TRUNCATE TABLE `discount_approve_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_pat_admit_reason` ");
		mysqli_query($link, " TRUNCATE TABLE `login_activity` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_free` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_free_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_refund` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_refund_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_refund_details` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_payment_refund_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_test_ref_doc` ");
		mysqli_query($link, " TRUNCATE TABLE `opdid_link_opdid` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_discount_reason` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_discount_reason_cancel` ");

		mysqli_query($link, " TRUNCATE TABLE `ipd_rmo_notes` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_case_sheet_LL` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_case_sheet_GL` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_case_sheet_CF` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_case_sheet_AB` ");

		mysqli_query($link, " TRUNCATE TABLE `casualty_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `ipd_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `lab_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `opd_free_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `uhid_serial_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_id_generator` ");

		mysqli_query($link, " TRUNCATE TABLE `serial_generator_1` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_2` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_3` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_4` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_5` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_6` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_7` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_8` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_9` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_10` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_11` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_12` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_13` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_14` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_15` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_16` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_17` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_18` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_19` ");
		mysqli_query($link, " TRUNCATE TABLE `serial_generator_20` ");

		mysqli_query($link, " INSERT INTO `patient_id_generator` (`slno`, `user`, `date`, `time`, `ip_addr`) VALUES ('100', '0', '0000-00-00', '00:00:00', '') ");

		mysqli_query($link, " TRUNCATE TABLE `approve_details` ");
		mysqli_query($link, " TRUNCATE TABLE `daily_account_close_pharmacy` ");
		mysqli_query($link, " TRUNCATE TABLE `date_master` ");
		mysqli_query($link, " TRUNCATE TABLE `emergency_patient_details` ");
		mysqli_query($link, " TRUNCATE TABLE `doctor_payment` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_book_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_pat_service_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_pat_service_details_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_resource_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_schedule_cancel` ");
		mysqli_query($link, " TRUNCATE TABLE `ot_schedule_update` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_cabin` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_info_rel_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_other_info_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_sell_details_edit` ");
		mysqli_query($link, " TRUNCATE TABLE `remark` ");
		mysqli_query($link, " TRUNCATE TABLE `testreport_print` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_process` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_return_supplier_master` ");
		mysqli_query($link, " TRUNCATE TABLE `ph_item_return_supplier_details` ");

		mysqli_query($link, " TRUNCATE TABLE `inv_main_stock_received_master` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_main_stock_received_detail` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_supplier_transaction` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_substore_issue_master` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_substore_issue_details` ");

		mysqli_query($link, " TRUNCATE TABLE `inv_item_process` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_maincurrent_stock` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_mainstock_details` ");

		mysqli_query($link, " TRUNCATE TABLE `inv_substorestock_master` ");
		mysqli_query($link, " TRUNCATE TABLE `inv_substorestock_details` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_info_app` ");
		mysqli_query($link, " TRUNCATE TABLE `invest_patient_payment_details_app` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_test_details_app` ");
		mysqli_query($link, " TRUNCATE TABLE `testmaster_app` ");

		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_nd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_rd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_cd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_ari` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_women` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_ed` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_bd` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_dtkg` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_intubation` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_details_drug` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_asa_grade` ");
		mysqli_query($link, " TRUNCATE TABLE `patient_pac_plan` ");





		if ($_POST["client"] == 2) {

			// Enable it when new client installation
			mysqli_query($link, " TRUNCATE TABLE `marketing_master` ");

			mysqli_query($link, " TRUNCATE TABLE `testmaster_rate` ");
			mysqli_query($link, " TRUNCATE TABLE `service_rate` ");
			mysqli_query($link, " TRUNCATE TABLE `ot_cabin_rate` ");
			mysqli_query($link, " TRUNCATE TABLE `opd_doc_rate` ");

			mysqli_query($link, " TRUNCATE TABLE `centre_test_discount_setup` ");

			mysqli_query($link, " TRUNCATE TABLE `dal_com_setup` ");

			mysqli_query($link, " TRUNCATE TABLE `menu_access_detail_user` ");

			mysqli_query($link, " TRUNCATE TABLE `ot_resource_link` ");

			mysqli_query($link, " TRUNCATE TABLE `consultant_doctor_master` ");
			mysqli_query($link, " TRUNCATE TABLE `consultant_doctor_alloc` ");
			mysqli_query($link, " TRUNCATE TABLE `lab_doctor` ");

			mysqli_query($link, " TRUNCATE TABLE `employee` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_alloc` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_advance` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_casual_leave_total` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_doc` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_extra_leave` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_family` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_medical_leave_total` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_official` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_overtime` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_personal` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_pfesi` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_remarks` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_salary` ");
			mysqli_query($link, " TRUNCATE TABLE `employee_salary_mode` ");
			mysqli_query($link, " TRUNCATE TABLE `emp_image` ");

			mysqli_query($link, " INSERT INTO `employee` (`emp_id`, `branch_id`, `emp_code`, `name`, `sex`, `dob`, `phone`, `email`, `address`, `password`, `levelid`, `emp_type`, `edit_info`, `edit_payment`, `cancel_pat`, `discount_permission`, `status`, `user`, `api_key`, `centreno`, `amount`) VALUES (101, 1, 'HIS101101', 'Administrator', 'Male', '0000-00-00', '', '', '', '9680a4e979c55c10957dcbaee66c7c56', 1, 1, 1, 1, 1, 0, 0, 99, '', 'C100', '0.00'), (102, 1, 'HIS102101', 'DEVELOPER', 'Male', '0000-00-00', '', '', '', '9680a4e979c55c10957dcbaee66c7c56', 1, 1, 0, 1, 1, 1, 0, 102, '', 'C100', '0.00') ");

			mysqli_query($link, " TRUNCATE TABLE `super_health_guide` ");
			mysqli_query($link, " INSERT INTO `super_health_guide` (`sguide_id`, `name`, `address`, `phone`, `email`, `branch_id`) VALUES (101, 'HOSPITAL', '', '', '', 1) ");

			mysqli_query($link, " TRUNCATE TABLE `health_guide` ");
			mysqli_query($link, " INSERT INTO `health_guide` (`hguide_id`, `name`, `address`, `phone`, `email`, `sguide_id`, `status`, `branch_id`) VALUES (101, 'SELF', '', '', '', 101, 0, 1) ");

			//~ mysqli_query($link, " TRUNCATE TABLE `refbydoctor_master` ");
			//~ mysqli_query($link, " INSERT INTO `refbydoctor_master` (`refbydoctorid`, `ref_name`, `qualification`, `address`, `phone`, `email`, `consultantdoctorid`, `emp_id`, `branch_id`, `user`, `date`, `time`) VALUES (101, 'SELF', '', '', NULL, '', 0, 0, 1,101,'$date','$time') ");

			mysqli_query($link, " TRUNCATE TABLE `cashier_access` ");
			mysqli_query($link, " INSERT INTO `cashier_access` (`emp_id`, `opd_cashier`, `lab_cashier`, `ipd_cashier`, `pharmacy_cashier`, `bloodbank_cashier`, `casuality_cashier`, `user`) VALUES ('101', '1', '1', '1', '1', '1', '1', '101'), ('102', 1, 1, 1, 1, 1, 1, 101) ");

			mysqli_query($link, "INSERT INTO `patient_info` (`slno`, `patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES (NULL, '100', '', 'OTHER', '', '', '', '', '', '', '', '', '', '0', '', '0', '', '', '0', '', '', '', '', '4', '55', '', '', '', '', '0', '0', '2023-07-28', '')");
		}

		echo "Database cleared";
	} else {
		echo "Incorrect Password !";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Clear Database</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>

<body>
	<form method="POST" action="">
		<input type="password" name="pass" placeholder="password" autofocus>
		<br>
		<b>Client Install</b>
		<label><input type="radio" name="client" value="1" checked> No</label>
		<label><input type="radio" name="client" value="2"> Yes</label>
		<br>
		<br>
		<button type="submit">Clear DB</button>
	</form>
</body>

</html>