<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");
$date=date("Y-m-d"); // important
$date1=date("Y-m-d");
$time=date("H:i:s");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

if($_POST["type"]=="save_pat_info")
{
	$name_title=$_POST["name_title"];
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	$pat_name=trim($name_title." ".$pat_name);
	$grdn_name=mysqli_real_escape_string($link, $_POST["grdn_name"]);
	$dob=$_POST["dob"];
	$age=$_POST["age"];
	$age_type=$_POST["age_type"];
	$sex=$_POST["sex"];
	$phone=$_POST["phone"];
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	$email=$_POST["email"];
	$ref_doc_id=$_POST["ref_doc_id"];
	$user=$_POST["user"];
	$payment_mode=$_POST["payment_mode"];
	$blood_group=$_POST["blood_group"];
	$category=$_POST["category"];
	$sel_center=$_POST["sel_center"];
	
	$source_id=$_POST['patient_type'];
	$g_relation=0;
	$marital_status=0;
	
	$hguide_id=$_POST['hguide_id'];
	if($ref_doc_id==0)
	{
		$ref_doc_id="101";
	}
	//$regd_fee=$_POST["regd_fee"];
	
	$entry_date=$_POST['entry_date'];
	$entry_time=$_POST['entry_time'];
	
	//~ $start=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid_start`,`pin_start` FROM `company_name`"));
	//~ $uhid_start=$start['uhid_start'];
	
	$patient_id=100;
	
	$date_str=explode("-", $date);
	$dis_year=$date_str[0];
	$dis_month=$date_str[1];
	$dis_year_sm=convert_date_only_sm_year($date);
	
	$c_m_y=$dis_year."-".$dis_month;
	$pat_tot_num_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`patient_id`) as tot FROM `patient_info` WHERE `date` like '$c_m_y%' "));
	$pat_tot_num=$pat_tot_num_qry["tot"];
	
	if($pat_tot_num==0)
	{
		$patient_id=$patient_id+1;
	}else
	{
		$patient_id=$patient_id+$pat_tot_num+1;
	}
	$new_patient_id=$patient_id.$dis_month.$dis_year_sm.$user;
	$new_patient_id=trim($new_patient_id);
	
	//~ $pat_uhid_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`uhid`) as uhid_tot FROM `patient_info` WHERE `date` like '$dis_year-%'"));
	//~ $pat_uhid_num=$pat_uhid_qry["uhid_tot"];
	//~ $uhid=$uhid_start+$pat_uhid_num+1;
	//~ $uhid=trim($uhid.$dis_year_sm);
	
	// Serial UHID
	$uhid_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_serial_generator` "));
	if(!$uhid_data)
	{
		//mysqli_query($link, " TRUNCATE TABLE `uhid_serial_generator` ");
	}

	mysqli_query($link, " INSERT INTO `uhid_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$user','$date','$time') ");
	
	$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `uhid_serial_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
	
	$uhid_serial=$last_slno["slno"];
	
	//$new_patient_id=generate_uhid($user);
	
	$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
	if($check_double_entry==0)
	{
		if($category=="lab_reg")
		{
			$date=$entry_date;
			$time=$entry_time;
		
			$payment_mode=$blood_group="";
			if(mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$pat_name','$grdn_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$ref_doc_id','$sel_center','$user','$payment_mode','$blood_group','$date','$time') "))
			{
				mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id') ");
				
				//mysqli_query($link," INSERT INTO `pat_health_guide`(`patient_id`, `hguide_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$hguide_id','$user','$date','$time') ");
				
				//echo $new_patient_id;
			}
		}else
		{		
			if(mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$pat_name','$grdn_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$ref_doc_id','','$user','$payment_mode','$blood_group','$date','$time') "))
			{
				mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id') ");
				
				//mysqli_query($link," INSERT INTO `pat_health_guide`(`patient_id`, `hguide_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$hguide_id','$user','$date','$time') ");
				
				//mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`, `regd_fee`, `date`, `time`) VALUES ('$new_patient_id','$regd_fee','$date','$time') ");
				
				//echo $new_patient_id;
			}
		}
		echo "0@@".$new_patient_id;
	}else
	{
		mysqli_query($link," INSERT INTO `patient_info` (`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('0', '0', '', '', '', '', '', '', '', '', '', '0', '', '0', '', '', '$date', '$time') ");
		
		echo "4@@0"; // Already exists
	}
}
if($_POST["type"]=="update_pat_info")
{
	$name_title=$_POST["name_title"];
	$pat_name=mysqli_real_escape_string($link, $_POST["pat_name"]);
	$pat_name=trim($name_title." ".$pat_name);
	$grdn_name=mysqli_real_escape_string($link, $_POST["grdn_name"]);
	$dob=$_POST["dob"];
	$age=$_POST["age"];
	$age_type=$_POST["age_type"];
	$sex=$_POST["sex"];
	$phone=$_POST["phone"];
	$address=mysqli_real_escape_string($link, $_POST["address"]);
	$email=$_POST["email"];
	$ref_doc_id=$_POST["ref_doc_id"];
	$user=$_POST["user"];
	$payment_mode=$_POST["payment_mode"];
	$blood_group=$_POST["blood_group"];
	$category=$_POST["category"];
	$sel_center=$_POST["sel_center"];
	
	$source_id=$_POST['patient_type'];
	$g_relation=0;
	$marital_status=0;
	
	$patient_id=$_POST["patient_id"];
	$user=$_POST["user"];
	if($ref_doc_id==0)
	{
		$ref_doc_id="101"; // Self
	}
	$hguide_id=$_POST['hguide_id'];
	
	// Edit Counter
	$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='' AND `type`='11' "));
	$edit_counter_num=$edit_counter["cntr"];
	$counter_num=$edit_counter_num+1;
	
	// edit counter record
	mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','','$date','$time','$user','11','$counter_num') ");
	
	$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
	if($pat_info)
	{
		mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$pat_info[name]','$pat_info[gd_name]','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$pat_info[address]','$pat_info[email]','$pat_info[refbydoctorid]','$pat_info[center_no]','$pat_info[user]','$pat_info[payment_mode]','$pat_info[blood_group]','$pat_info[date]','$pat_info[time]','$counter_num') ");
	}
	
	
	if(mysqli_query($link, " UPDATE `patient_info` SET `name`='$pat_name',`gd_name`='$grdn_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`refbydoctorid`='$ref_doc_id',`center_no`='$center_no',`user`='$user' WHERE `patient_id`='$patient_id' "))
	{
		$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$patient_id' "));
		if($pat_other)
		{
			mysqli_query($link," INSERT INTO `patient_other_info_edit`(`patient_id`, `marital_status`, `relation`, `source_id`, `counter`) VALUES ('$pat_other[patient_id]','$pat_other[marital_status]','$pat_other[relation]','$pat_other[source_id]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_other_info` SET `source_id`='$source_id' WHERE `patient_id`='$patient_id'");
		}else
		{
			mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`) VALUES ('$patient_id','$marital_status','$g_relation','$source_id') ");
		}
		
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
		if($pat_info_rel)
		{
			mysqli_query($link," INSERT INTO `patient_info_rel_edit`(`patient_id`, `credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `counter`) VALUES ('$pat_info_rel[patient_id]','$pat_info_rel[credit]','$pat_info_rel[gd_phone]','$pat_info_rel[crno]','$pat_info_rel[pin]','$pat_info_rel[police]','$pat_info_rel[state]','$pat_info_rel[district]','$pat_info_rel[city]','$pat_info_rel[file_no]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno',`post_office`='$post_office' WHERE `patient_id`='$patient_id' ");
		}else
		{
			mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`) VALUES ('$patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office')");
		}
		
		//mysqli_query($link," UPDATE `pat_health_guide` SET `hguide_id`='$hguide_id' WHERE `patient_id`='$patient_id' ");
		
		echo "0@@".$patient_id;
	}
}

if($_POST["type"]=="revisit_pat_info")
{
	$patient_id=$_POST["patient_id"];
	//~ $user=$_POST["user"];
	//~ $sel_center=$_POST["sel_center"];
	//~ $ref_doc_id=$_POST["ref_doc_id"];
	
	//~ mysqli_query($link," INSERT INTO `pat_visit_record`(`patient_id`, `refbydoctorid`, `center_no`, `user`, `date`, `time`) VALUES ('$patient_id','$ref_doc_id','$sel_center','$user','$date','$time') ");
	
	echo "0@@".$patient_id;
	
}

if($_POST["type"]=="average_time_per_patient")
{
	$con_doc_id=$_POST["con_doc_id"];
	$average_time=$_POST["average_time"];
	$user=$_POST["user"];
	
	$con_doc_val=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `con_doc_available_time` WHERE `consultantdoctorid`='$con_doc_id' "));
	if($con_doc_val==0)
	{
		if(mysqli_query($link, " INSERT INTO `con_doc_available_time`(`consultantdoctorid`, `average_time`, `sunday`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `date`, `time`, `user`) VALUES ('$con_doc_id','$average_time','','','','','','','','$date','$time','$user') "))
		{
			echo "Saved";
		}
	}else
	{
		if(mysqli_query($link, " UPDATE `con_doc_available_time` SET `average_time`='$average_time',`user`='$user' WHERE `consultantdoctorid`='$con_doc_id' "))
		{
			echo "Saved";
		}
	}
}
if($_POST["type"]=="save_con_doc_time_range")
{
	$con_doc_id=$_POST["con_doc_id"];
	$this_day=$_POST["this_day"];
	$str=$_POST["str"];
	$user=$_POST["user"];
	
	$week = array("","sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"); 
	$day=$week[$this_day];
	
	mysqli_query($link, " UPDATE `con_doc_available_time` SET `$day`='$str',`user`='$user' WHERE `consultantdoctorid`='$con_doc_id' ");
	
	echo $this_day;
}
if($_POST["type"]=="save_pat_appointment")
{
	$mode=$_POST["mode"];
	$ref_doc_id=$_POST["ref_doc_id"];
	$dept_id=$_POST["dept_id"];
	$con_doc_id=$_POST["con_doc_id"];
	$appoint_date=$_POST["appoint_date"];
	$visit_fee=$_POST["visit_fee"];
	$regd_fee=$_POST["regd_fee"];
	$total=trim($_POST["total"]);
	$dis_per=$_POST["dis_per"];
	$dis_amnt=trim($_POST["dis_amnt"]);
	
	$dis_reason=mysqli_real_escape_string($link, $_POST["dis_reason"]);
	$advance=trim($_POST["advance"]);
	$bal_reason=mysqli_real_escape_string($link, $_POST["bal_reason"]);
	
	$balance=trim($_POST["balance"]);
	$pay_mode=$_POST["pay_mode"];
	$pat_emergency=$_POST["pat_emergency"];
	$emergency_fee=$_POST["emergency_fee"];
	
	$emergency_check=$_POST["emergency_check"];
	$pat_emergency=$emergency_check;
	
	$cross_consult=$_POST["cross_consult"];
	$cross_consult_fee=$_POST["cross_consult_fee"];
	
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["cheque_ref_no"]);
	
	$appoint_day=convert_date_to_day_num($appoint_date);
	
	$uhid=$_POST["uhid"];
	$user=$_POST["user"];
	
	$balance=$total-$advance-$dis_amnt;
	
	$refund_amount=0;
	$refund_reason="";
	
	$sel_center=$_POST['sel_center'];
	$doctor_session=$_POST['doctor_session'];
	$visit_type_id=$_POST['visit_type_id'];
	$card_id=$_POST['card_id'];
	
	$hguide_id=101;
	$branch_id=1;
	
	$card_no="";
	$card_details="";
	
	$emergency_fee=0;
	$pat_emergency=0;
	$cross_consult=0;
	$hguide_id=101; // Health Guide
	
	$blood_group="";
	$credit="";
	$fileno="";
	$esi_ip_no="";
	
	if(!$ptype){ $ptype=0; }
	if(!$ref_doc_id){ $ref_doc_id=0; }
	if(!$crno){ $crno=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$income_id){ $income_id=0; }
	if(!$dept_id){ $dept_id=0; }
	if(!$con_doc_id){ $con_doc_id=0; }
	if(!$doctor_session){ $doctor_session=0; }
	if(!$visit_type_id){ $visit_type_id=0; }
	if(!$visit_fee){ $visit_fee=0; }
	if(!$regd_fee){ $regd_fee=0; }
	if(!$total){ $total=0; }
	if(!$advance){ $advance=0; }
	if(!$dis_amnt){ $dis_amnt=0; }
	
	$dis_per=round($dis_amnt/$total,2);
	
	if($sel_center!='C100')
	{
		$center=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$sel_center' "));
		//$dis_reason=$center["centrename"];
	}
	
	$visit_type_val=1;
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$visit_type_val'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	if($mode=="save")
	{
		$date=$appoint_date;
		
		$alrdy_appoint_num=mysqli_num_rows(mysqli_query($link, " SELECT `appointment_no` FROM `appointment_book` WHERE `patient_id`='$uhid' and `consultantdoctorid`='$con_doc_id' and `appointment_date`='$appoint_date' "));
		$alrdy_appoint_num=0;
		if($alrdy_appoint_num==0)
		{
			$opd_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opd_serial_generator` "));
			if(!$opd_data)
			{
				//mysqli_query($link, " TRUNCATE TABLE `opd_serial_generator` ");
			}

			mysqli_query($link, " INSERT INTO `opd_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$uhid','$user','$date','$time') ");
			
			$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `opd_serial_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$opd_serial=$last_slno["slno"];
			
			// OPD ID Generate
			$opd_idds=100;
			
			$date_str=explode("-", $date);
			$dis_year=$date_str[0];
			$dis_month=$date_str[1];
			$dis_year_sm=convert_date_only_sm_year($date);
			
			$c_m_y=$dis_year."-".$dis_month;
			
			$current_month=date("Y-m");
			if($c_m_y<$current_month)
			{
				$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
				$opd_id_num=$opd_id_qry["tot"];
				
				$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
				$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
				
				$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
				
				if($pat_tot_num==0)
				{
					$opd_idd=$opd_idds+1;
				}else
				{
					$opd_idd=$opd_idds+$pat_tot_num+100;
				}
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}else
			{
				$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
				if(!$c_data)
				{
					mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
				}

				mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','1','$user','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$last_slno=$last_slno["slno"];
				
				//mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
				
				$opd_idd=$opd_idds+$last_slno;
				$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}
			
			// uhid_and_opdid
			//~ $check_double_entry_opdid=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
			//~ if($check_double_entry_opdid==0)
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid','$opd_id','$date','$time','$user','$visit_type_val','$opd_serial','$ref_doc_id','$sel_center','$hguide_id','$branch_id') "))
			{
				
				$appnt_qry=mysqli_fetch_array(mysqli_query($link, " SELECT max(`appointment_no`) as mx FROM `appointment_book` WHERE `consultantdoctorid`='$con_doc_id' and `appointment_date`='$appoint_date' and doctor_session='$doctor_session' "));
				$appnt_num=$appnt_qry["mx"];
				if($appnt_num==0)
				{
					$appoint_no=1;
				}else
				{
					$appoint_no=$appnt_num+1;
				}
				
				// appointment_book
				$check_double_entry_appointment=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_appointment==0)
				{
					mysqli_query($link, " INSERT INTO `appointment_book`(`patient_id`, `opd_id`, `dept_id`, `consultantdoctorid`, `appointment_date`, `appointment_day`, `appointment_no`, `user`, `date`, `time`, `emergency`, `visit_fee`, `doctor_session`) VALUES ('$uhid','$opd_id','$dept_id','$con_doc_id','$appoint_date','$appoint_day','$appoint_no','$user','$date','$time','$pat_emergency','$visit_fee','$doctor_session') ");
				}
				// Cross Consultation
				if($cross_consult>0)
				{
					$check_double_entry_cross_consult=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
					if($check_double_entry_cross_consult==0)
					{
						mysqli_query($link, " INSERT INTO `cross_consultation`(`patient_id`, `opd_id`, `amount`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$cross_consult_fee','$user','$date','$time') ");
					}
				}
				
				// consult_patient_payment_details
				$check_double_entry_pat_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_pat_pay_detail==0)
				{
					if($regd_fee>0)
					{
						// Regd Fee Record
						mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`,`opd_id`, `regd_fee`, `date`, `time`) VALUES ('$uhid','$opd_id','$regd_fee','$date','$time') ");
					}
					if($emergency_fee>0)
					{
						// Regd Fee Record
						mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`,`opd_id`, `regd_fee`, `date`, `time`) VALUES ('$uhid','$opd_id','$emergency_fee','$date','$time') ");
					}
					
					mysqli_query($link, " INSERT INTO `consult_patient_payment_details`(`patient_id`, `opd_id`, `visit_fee`, `regd_fee`, `emergency_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$visit_fee','$regd_fee','$emergency_fee','$total','$dis_per','$dis_amnt','$dis_reason','$advance','0','0','$balance','$bal_reason','$date','$time','$user') ");
					
				}
				
				// consult_payment_detail
				
				$check_double_entry_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_pay_detail==0)
				{
					if($pay_mode=="Credit")
					{
						$advance=0;
						$balance=$total-$advance-$dis_amnt;
					}
					if($advance==0)
					{
						$pay_mode="Credit";
						if($total==0)
						{
							$pay_mode="Cash";
						}
					}
					
					if($advance>0 && $balance>0)
					{
						if($advance>0)
						{
							$bill_no=generate_bill_no($bill_name,1);
							$balance_now=0;
							
							mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance_now','','$dis_amnt','$dis_reason','$refund_amount','$refund_reason','$cheque_ref_no','$user','$time','$date') ");
							
							$dis_amnt=0;
							$dis_reason="";
							$cheque_ref_no="";
						}
						if($balance>0)
						{
							$bill_no=generate_bill_no($bill_name,1);
							$advance_now=0;
							$pay_mode="Credit";
							
							mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance_now','$balance','$bal_reason','$dis_amnt','$dis_reason','$refund_amount','$refund_reason','$cheque_ref_no','$user','$time','$date') ");
						}
					}
					else
					{
						$bill_no=generate_bill_no($bill_name,1);
							
						mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance','$bal_reason','$dis_amnt','$dis_reason','$refund_amount','$refund_reason','$cheque_ref_no','$user','$time','$date') ");
					}
				}
				
				// Check double entry
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cash' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Card' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Credit' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cheque' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='NEFT' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='RTGS' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='UPI' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				if($dis_amnt>0)
				{
					// Discount Approve
					mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$uhid','$opd_id','$total','$dis_amnt','$dis_reason','$user','0','$date','$time') ");
				}
				
				mysqli_query($link, " INSERT INTO `patient_visit_type_details`(`patient_id`, `opd_id`, `p_type_id`, `visit_type_id`,`date`,`time`) VALUES ('$uhid','$opd_id','1','$visit_type_id','$date','$time') ");
				
				
				if($card_id>0)
				{
					mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$uhid','$opd_id','$card_id','$card_no','$card_details') ");
				}
				
				echo "Saved@@".$opd_id;
			}else
			{
				//mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','1','$user','$date','$time') ");
				
				//mysqli_query($link, " INSERT INTO `uhid_and_opdid` (`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('0', '0', '$date', '$time', '0', '0', '0', '0', '0') ");
				
				echo "4"; // already exists
			}
		}else
		{
			echo "2";
		}
	}else if($mode=="update")
	{
		$opd_id=$_POST["opd_id"];
		
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='1' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// appointment_book_edit
			$check_double_entry_appointment=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `appointment_book_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `counter`='$counter_num' "));
			if($check_double_entry_appointment==0)
			{
				$doc_consult=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		
				mysqli_query($link, "  INSERT INTO `appointment_book_edit`(`patient_id`, `opd_id`, `consultantdoctorid`, `appointment_date`, `appointment_day`, `appointment_no`, `user`, `date`, `time`, `emergency`, `visit_fee`, `doctor_session`, `counter`) VALUES ('$doc_consult[patient_id]','$doc_consult[opd_id]','$doc_consult[consultantdoctorid]','$doc_consult[appointment_date]','$doc_consult[appointment_day]','$doc_consult[appointment_no]','$doc_consult[user]','$doc_consult[date]','$doc_consult[time]','$doc_consult[emergency]','$doc_consult[visit_fee]','$doc_consult[doctor_session]','$counter_num') ");
			}
			
			// consult_patient_payment_details_edit
			$check_double_entry_pat_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `counter`='$counter_num' "));
			if($check_double_entry_pat_pay_detail==0)
			{
				$con_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				
				mysqli_query($link, " INSERT INTO `consult_patient_payment_details_edit`(`patient_id`, `opd_id`, `visit_fee`, `regd_fee`, `emergency_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user`, `counter`) VALUES ('$con_pat_pay_detail[patient_id]','$con_pat_pay_detail[opd_id]','$con_pat_pay_detail[visit_fee]','$con_pat_pay_detail[regd_fee]','$con_pat_pay_detail[emergency_fee]','$con_pat_pay_detail[tot_amount]','$con_pat_pay_detail[dis_per]','$con_pat_pay_detail[dis_amt]','$con_pat_pay_detail[dis_reason]','$con_pat_pay_detail[advance]','$con_pat_pay_detail[refund_amount]','$con_pat_pay_detail[tax_amount]','$con_pat_pay_detail[balance]','$con_pat_pay_detail[bal_reason]','$con_pat_pay_detail[date]','$con_pat_pay_detail[time]','$con_pat_pay_detail[user]','$counter_num') ");
			}
			// consult_payment_detail_edit
			$check_double_entry_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `consult_payment_detail_edit` WHERE `patient_id`='$uhid' and `typeofpayment`='A' and `opd_id`='$opd_id' and `counter`='$counter_num' "));
			if($check_double_entry_pay_detail==0)
			{
				$con_pay_detail_qry=mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				while($con_pay_detail=mysqli_fetch_array($con_pay_detail_qry))
				{
					mysqli_query($link, " INSERT INTO `consult_payment_detail_edit`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`, `counter`) VALUES ('$con_pay_detail[patient_id]','$con_pay_detail[opd_id]','$con_pay_detail[bill_no]','$con_pay_detail[payment_mode]','$con_pay_detail[typeofpayment]','$con_pay_detail[amount]','$con_pay_detail[balance]','$con_pay_detail[balance_reason]','$con_pay_detail[discount]','$con_pay_detail[discount_reason]','$con_pay_detail[refund]','$con_pay_detail[refund_reason]','$con_pay_detail[cheque_ref_no]','$con_pay_detail[user]','$con_pay_detail[time]','$con_pay_detail[date]','$counter_num') ");
				}
			}
			// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$opd_id','$date','$time','$user','1','$counter_num') ");
			
			$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			
			mysqli_query($link, " INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `counter`) VALUES ('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','$pat_reg[type]','$pat_reg[ipd_serial]','$pat_reg[refbydoctorid]','$pat_reg[center_no]','$pat_reg[hguide_id]','$pat_reg[branch_id]','$counter_num') ");
			
			// Update
			mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$ref_doc_id', `center_no`='$sel_center' , `hguide_id`='$hguide_id' , `branch_id`='$branch_id' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			
			// Regd Fee Record
			$check_regd_fees=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_regd_fee` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			if($check_regd_fees)
			{
				mysqli_query($link, " UPDATE `pat_regd_fee` SET `regd_fee`='$regd_fee' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}else
			{
				if($regd_fee>0)
				{
					mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`,`opd_id`, `regd_fee`, `date`, `time`) VALUES ('$uhid','$opd_id','$regd_fee','$date','$time') ");
				}
				if($emergency_fee>0)
				{
					// Regd Fee Record
					mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`,`opd_id`, `regd_fee`, `date`, `time`) VALUES ('$uhid','$opd_id','$emergency_fee','$date','$time') ");
				}
			}
			
			//Cross Consultation
			if($cross_consult>0)
			{
				$check_double_entry_cross_consult=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `cross_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_cross_consult==0)
				{
					$date=$appoint_date;
					mysqli_query($link, " INSERT INTO `cross_consultation`(`patient_id`, `opd_id`, `amount`, `user`, `date`, `time`) VALUES ('$uhid','$opd_id','$cross_consult_fee','$user','$date','$time') ");
				}else
				{
					mysqli_query($link, " UPDATE `cross_consultation` SET `amount`='$cross_consult_fee' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				}
			}else
			{
				mysqli_query($link, " DELETE FROM `cross_consultation` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}
			
			mysqli_query($link, " UPDATE `consult_patient_payment_details` SET `visit_fee`='$visit_fee',`regd_fee`='$regd_fee',`emergency_fee`='$emergency_fee',`tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			
			mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='B' ");
			mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='R' ");
			
			///////////
			if($pay_mode=="Credit")
			{
				$advance=0;
				$balance=$total-$advance-$dis_amnt;
			}
			if($advance==0)
			{
				$pay_mode="Credit";
				if($total==0)
				{
					$pay_mode="Cash";
				}
			}
			
			if($advance==0)
			{
				mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='Credit' ");
			}
			
			if($balance==0)
			{
				mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='Credit' ");
			}
			
			if($advance>0 && $balance>0)
			{
				if($advance>0)
				{
					$balance_now=0;
					
					$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
					
					if($inv_pay_detail_chk)
					{
						mysqli_query($link, " UPDATE `consult_payment_detail` SET `amount`='$advance', `balance`='$balance_now', `discount`='$dis_amnt', `refund`='$refund_amount', `cheque_ref_no`='$cheque_ref_no' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' ");
					}
					else
					{
						mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='Credit' and `payment_mode`!='$pay_mode' ");
						
						$bill_no=generate_bill_no($bill_name,1);
						
						mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance_now','','$dis_amnt','$refund_amount','$refund_reason','$cheque_ref_no','$user','$pat_reg[time]','$pat_reg[date]') ");
					}
					$dis_amnt=0;
					$cheque_ref_no="";
				}
				if($balance>0)
				{
					$advance_now=0;
					$pay_mode="Credit";
					
					$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
					if($inv_pay_detail_chk)
					{
						mysqli_query($link, " UPDATE `consult_payment_detail` SET `amount`='$advance_now', `balance`='$balance', `balance_reason`='$bal_reason', `discount`='$dis_amnt', `refund`='$refund_amount', `cheque_ref_no`='$cheque_ref_no' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' ");
					}
					else
					{
						$bill_no=generate_bill_no($bill_name,1);
									
						mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance_now','$balance','$bal_reason','$dis_amnt','$refund_amount','$refund_reason','$cheque_ref_no','$user','$pat_reg[time]','$pat_reg[date]') ");
					}
				}
			}
			else
			{
				mysqli_query($link, " DELETE FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='$pay_mode' ");
				
				$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
					
				if($inv_pay_detail_chk)
				{
					mysqli_query($link, " UPDATE `consult_payment_detail` SET `amount`='$advance', `balance`='$balance', `balance_reason`='$bal_reason', `discount`='$dis_amnt', `refund`='$refund_amount', `cheque_ref_no`='$cheque_ref_no' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' ");
				}
				else
				{
					$bill_no=generate_bill_no($bill_name,1);
					
					mysqli_query($link, " INSERT INTO `consult_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance','$bal_reason','$dis_amnt','$refund_amount','$refund_reason','$cheque_ref_no','$user','$pat_reg[time]','$pat_reg[date]') ");
				}
			}
			
			// Check double entry
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cash' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Card' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Credit' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cheque' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='NEFT' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='RTGS' ");
			$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

			if($cash_adv_pay_num>1)
			{
				$h=1;
				while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
				{
					if($h>1)
					{
						$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
						if(!$check_pay_mode_change)
						{
							mysqli_query($link," DELETE FROM `consult_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
						}
					}
					$h++;
				}
			}
			
			//mysqli_query($link, " UPDATE `consult_payment_detail` SET `amount`='$advance', `payment_mode`='$pay_mode' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='A' ");
			
			$check_appmnt=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
			
			$appnt_qry=mysqli_fetch_array(mysqli_query($link, " SELECT max(`appointment_no`) as mx FROM `appointment_book` WHERE `consultantdoctorid`='$con_doc_id' and `appointment_date`='$appoint_date' and doctor_session='$doctor_session' "));
			$appnt_num=$appnt_qry["mx"];
			if($appnt_num==0)
			{
				$appoint_no=1;
			}else
			{
				$appoint_no=$appnt_num+1;
			}
			
			if($con_doc_id==$check_appmnt["consultantdoctorid"] && $appoint_date==$check_appmnt["appointment_date"])
			{
				if($doctor_session!=$check_appmnt["doctor_session"])
				{
					mysqli_query($link, " UPDATE `appointment_book` SET `appointment_no`='$appoint_no',`emergency`='$pat_emergency',`visit_fee`='$visit_fee',`doctor_session`='$doctor_session' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				}else
				{
					mysqli_query($link, " UPDATE `appointment_book` SET `emergency`='$pat_emergency',`visit_fee`='$visit_fee' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				}
			}else
			{
				mysqli_query($link, " UPDATE `appointment_book` SET `consultantdoctorid`='$con_doc_id',`appointment_date`='$appoint_date',`appointment_day`='$appoint_day',`appointment_no`='$appoint_no',`emergency`='$pat_emergency',`visit_fee`='$visit_fee',`doctor_session`='$doctor_session' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}
		
		if($dis_amnt>0)
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ");
			//$dis_apprv_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' "));
			//if($dis_apprv_num==0)
			
				// Discount Approve
				mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$uhid','$opd_id','$total','$dis_amnt','$dis_reason','$user','0','$date','$time') ");
			
		}else
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ");
		}
		
		mysqli_query($link, " UPDATE `patient_visit_type_details` SET `visit_type_id`='$visit_type_id' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		
		$check_card_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_card_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		if($check_card_entry)
		{
			if($card_id>0)
			{
				mysqli_query($link, " UPDATE `patient_card_details` SET `card_id`='$card_id',`card_no`='$card_no',`card_details`='$card_details' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}else
			{
				mysqli_query($link, " DELETE FROM `patient_card_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}
		}else
		{
			if($card_id>0)
			{
				mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$uhid','$opd_id','$card_id','$card_no','$card_details') ");
			}
		}
		
		echo "Updated@@".$opd_id;
	}
	
}
if($_POST["type"]=="cancel_con_doc_appointment")
{
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$con_doc_id=$_POST["con_doc_id"];
	$ap_date=$_POST["ap_date"];
	mysqli_query($link, " DELETE FROM `appointment_book` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
}

if($_POST["type"]=="save_pat_test")
{
	$mode=$_POST["mode"];
	$regd_fee=$_POST["regd_fee"];
	$total=trim($_POST["total"]);
	$dis_per=$_POST["dis_per"];
	$dis_amnt=trim($_POST["dis_amnt"]);
	$dis_reason=mysqli_real_escape_string($link, $_POST["dis_reason"]);
	$advance=trim($_POST["advance"]);
	$bal_reason=mysqli_real_escape_string($link, $_POST["bal_reason"]);
	$balance=trim($_POST["balance"]);
	$pay_mode=$_POST["pay_mode"];
	
	$ref_doc_id=$_POST["ref_doc_id"];
	$sel_center=$_POST["sel_center"];
	
	$cheque_ref_no=mysqli_real_escape_string($link, $_POST["cheque_ref_no"]);
	$card_id=$_POST['card_id'];
	$cat=$_POST['cat'];
	
	$hguide_id=101;
	$branch_id=1;
	
	$card_no="";
	$card_details="";
	
	$uhid=$_POST["uhid"];
	$opd_id=$_POST["opd_id"];
	$user=trim($_POST["user"]);
	
	$test_all=$_POST["test_all"];
	$extr=$_POST["ex_all"];
	
	if(!$total){ $total=0; }
	if(!$advance){ $advance=0; }
	if(!$dis_amnt){ $dis_amnt=0; }
	
	$balance=$total-$advance-$dis_amnt;
	
	$refund_amount=0;
	
	$dis_per=round(($dis_amnt*100)/$total,2);
	
	if($sel_center!='C100')
	{
		$center=mysqli_fetch_array(mysqli_query($link, " SELECT `centrename` FROM `centremaster` WHERE `centreno`='$sel_center' "));
		$dis_reason=$center["centrename"];
	}
	
	$date=date("Y-m-d"); // important
	
	$catt=explode("@", $cat);
	$reg_category=$catt[0];
	$reg_dept=$catt[1];
	if($reg_category==1)
	{
		$visit_type_val=2;
	}
	else
	{
		if($reg_dept==128)
		{
			$visit_type_val=10; // USG
		}
		else if($reg_dept==40)
		{
			$visit_type_val=11; // XRAY
		}
		else if($reg_dept==131)
		{
			$visit_type_val=12; // ECG
		}
		else if($reg_dept==121)
		{
			$visit_type_val=13; // ENDOSCOPY
		}
		else if($reg_dept==126)
		{
			$visit_type_val=16; // CT SCAN
		}
	}
	
	$pat_typ_text=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_type_master` WHERE `p_type_id`='$visit_type_val'"));
	$bill_name=$pat_typ_text["bill_name"];
	
	if($mode=="Save")
	{
		if($test_all!="")
		{
			$old_opd_id=0;
			if($opd_id=="0000")
			{
				$lab_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `lab_serial_generator` "));
				if(!$lab_data)
				{
					//mysqli_query($link, " TRUNCATE TABLE `lab_serial_generator` ");
				}

				mysqli_query($link, " INSERT INTO `lab_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$uhid','$user','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `lab_serial_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$lab_serial=$last_slno["slno"];
				
				// OPD ID Generate
				$opd_idds=100;
				
				$date_str=explode("-", $date);
				$dis_year=$date_str[0];
				$dis_month=$date_str[1];
				$dis_year_sm=convert_date_only_sm_year($date);
				
				$c_m_y=$dis_year."-".$dis_month;
				
				$current_month=date("Y-m");
				if($c_m_y<$current_month)
				{
					$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
					$opd_id_num=$opd_id_qry["tot"];
					
					$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
					$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
					
					$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
					
					if($pat_tot_num==0)
					{
						$opd_idd=$opd_idds+1;
					}else
					{
						$opd_idd=$opd_idds+$pat_tot_num+100;
					}
					$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}else
				{
					$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
					if(!$c_data)
					{
						mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
					}

					mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','2','$user','$date','$time') ");
					
					$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
					
					$last_slno=$last_slno["slno"];
					
					//mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
					
					$opd_idd=$opd_idds+$last_slno;
					$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}
			}else
			{
				$old_opd_id=$opd_id;
				$opd_id="";
				
				$serial=0;
				$dis_day=date("d");
				$dis_month=date("m");
				$dis_year=date("Y");
				$dis_year_sm=date("y");
				$c_m_y=$dis_year."-".$dis_month."-".$dis_day;
				$opd_serial_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
				$opd_serial_num=$opd_serial_qry["tot"];
				
				$opd_serial_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
				$opd_serial_cancel_num=$opd_serial_qry_cancel["tot"];
				
				$pat_serial_num=$opd_serial_num+$opd_serial_cancel_num;
				
				if($pat_serial_num==0)
				{
					$opd_serial=$serial+1;
				}else
				{
					$opd_serial=$serial+$pat_serial_num+1;
				}
				$opd_serial=0;
				
				//$opd_serial=generate_serial_lab($opd_serial);
				
				// OPD ID Generate
				$opd_idds=100;
				
				$date_str=explode("-", $date);
				$dis_year=$date_str[0];
				$dis_month=$date_str[1];
				$dis_year_sm=convert_date_only_sm_year($date);
				
				$c_m_y=$dis_year."-".$dis_month;
				
				$current_month=date("Y-m");
				if($c_m_y<$current_month)
				{
					$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' "));
					$opd_id_num=$opd_id_qry["tot"];
					
					$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' "));
					$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
					
					$pat_tot_num=$opd_id_num+$opd_id_cancel_num;
					
					if($pat_tot_num==0)
					{
						$opd_idd=$opd_idds+1;
					}else
					{
						$opd_idd=$opd_idds+$pat_tot_num+100;
					}
					$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}else
				{
					$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
					if(!$c_data)
					{
						mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
					}

					mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','2','$user','$date','$time') ");
					
					$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$uhid' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
					
					$last_slno=$last_slno["slno"];
					
					//mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
					
					$opd_idd=$opd_idds+$last_slno;
					$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}
			}
			
			if(mysqli_query($link, " INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$uhid','$opd_id','$date','$time','$user','$visit_type_val','$lab_serial','$ref_doc_id','$sel_center','$hguide_id','$branch_id') "))
			{
				$check_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `opdid_link_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$old_opd_id' "));
				
				if($check_entry)
				{
					// Delete
					mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$old_opd_id' ");
					
					mysqli_query($link," UPDATE `opdid_link_opdid` SET `new_opd_id`='$opd_id' WHERE `patient_id`='$uhid' AND `opd_id`='$old_opd_id' ");
				}
				
				$test_all=explode("@",$test_all);
				foreach($test_all as $test)
				{
					if($test)
					{
						$test=explode("-",$test);
						$test_id=$test[0];
						$test_rate=$test[1];
						
						$test_discount=round((($test_rate*$dis_per)/100),2);
						
						$check_double_entry_test_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `testid`='$test_id' "));
						if($check_double_entry_test_detail==0)
						{
							// Delete
							mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `testid`='$test_id' ");
							// Sample ID
							$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
							// Insert
							mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd_id','','1','$test_id','$smpl[SampleId]','$test_rate','$test_discount','$date','$time','$user','2') ");
							// Add On Test
							$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
							while($s_t=mysqli_fetch_array($sub_tst))
							{
								$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
								mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd_id','','1','$s_t[sub_testid]','$samp_sb[SampleId]','0','0','$date','$time','$user','2') ");
							}
						}
					}
				}
				// Vaccu
				mysqli_query($link, " DELETE FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
				$ext=explode("@",$extr);
				foreach($ext as $ex)
				{
					if($ex)
					{
						$ex=explode("-",$ex);
						mysqli_query($link, " INSERT INTO `patient_vaccu_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date`) VALUES ('$uhid','$opd_id','','1','$ex[0]','$ex[1]','$time','$date') ");	
						
					}	
				}
				
				// invest_patient_payment_details
				$check_double_entry_pat_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_pat_pay_detail==0)
				{
					// Regd Fee Record
					if($regd_fee>0)
					{
						mysqli_query($link, " INSERT INTO `pat_regd_fee`(`patient_id`,`opd_id`, `regd_fee`, `date`, `time`) VALUES ('$uhid','$opd_id','$regd_fee','$date','$time') ");
					}
					
					mysqli_query($link, " INSERT INTO `invest_patient_payment_details`(`patient_id`, `opd_id`,`regd_fee`,`tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user`) VALUES ('$uhid','$opd_id','$regd_fee','$total','$dis_per','$dis_amnt','$dis_reason','$advance','0','0','$balance','$bal_reason','$date','$time','$user') ");
					
				}
				// invest_payment_detail
				$check_double_entry_pay_detail=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				if($check_double_entry_pay_detail==0)
				{
					if($pay_mode=="Credit")
					{
						$advance=0;
						$balance=$total-$advance-$dis_amnt;
					}
					if($advance==0)
					{
						$pay_mode="Credit";
						if($total==0)
						{
							$pay_mode="Cash";
						}
					}
					
					if($advance>0 && $balance>0)
					{
						if($advance>0)
						{
							$bill_no=generate_bill_no($bill_name,2);
							$balance_now=0;
							$bal_reason_now="";
							
							mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance_now','$bal_reason_now','$dis_amnt','$dis_reason','$refund_amount','','$cheque_ref_no','$user','$time','$date') ");
						}
						if($balance>0)
						{
							$bill_no=generate_bill_no($bill_name,2);
							$advance_now=0;
							$pay_mode="Credit";
							$cheque_ref_no="";
							$dis_amnt=0;
							
							mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance_now','$balance','$bal_reason','$dis_amnt','$dis_reason','$refund_amount','','$cheque_ref_no','$user','$time','$date') ");
						}
					}
					else
					{
						$bill_no=generate_bill_no($bill_name,2);
							
						mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance','$bal_reason','$dis_amnt','$dis_reason','$refund_amount','','$cheque_ref_no','$user','$time','$date') ");
					}
				}
				
				// Check double entry
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cash' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Card' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Credit' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cheque' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='NEFT' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='RTGS' ");
				$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

				if($cash_adv_pay_num>1)
				{
					$h=1;
					while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
					{
						if($h>1)
						{
							$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
							if(!$check_pay_mode_change)
							{
								mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
							}
						}
						$h++;
					}
				}
				
				if($dis_amnt>0)
				{
					// Discount Approve
					mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$uhid','$opd_id','$total','$dis_amnt','$dis_reason','$user','0','$date','$time') ");
				}
				
				if($card_id>0)
				{
					mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$uhid','$opd_id','$card_id','$card_no','$card_details') ");
				}
				
				echo "Saved@".$opd_id."@".$uhid;	 // success
			}else
			{
				//mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$uhid','$visit_type_val','$user','$date','$time') ");
				
				//mysqli_query($link, " INSERT INTO `uhid_and_opdid` (`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('0', '0', '$date', '$time', '0', '0', '0', '0', '0') ");
				
				echo "Error@0@0"; // already exists
			}
		}
	}else if($mode=="Update")
	{
		$pat_reg=mysqli_fetch_array(mysqli_query($link, "  SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		
		// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `type`='2' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$uhid','$opd_id','$date','$time','$user','2','$counter_num') ");
			
			// Test Entry
			$test_qry=mysqli_query($link, "  SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			//mysqli_query($link, " DELETE FROM `patient_test_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' AND `counter`='$counter_num' ");
			while($test_val=mysqli_fetch_array($test_qry))
			{
				mysqli_query($link, "  INSERT INTO `patient_test_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type` ,`counter`) VALUES ('$test_val[patient_id]','$test_val[opd_id]','$test_val[ipd_id]','$test_val[batch_no]','$test_val[testid]','$test_val[sample_id]','$test_val[test_rate]','$test_val[test_discount]','$test_val[date]','$test_val[time]','$test_val[user]','$test_val[type]','$counter_num') ");
			}
			// Vaccu Entry
			$vaccu_qry=mysqli_query($link, "  SELECT * FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			//mysqli_query($link, " DELETE FROM `patient_vaccu_details_edit` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' AND `counter`='$counter_num' ");
			while($vaccu=mysqli_fetch_array($vaccu_qry))
			{
				mysqli_query($link, " INSERT INTO `patient_vaccu_details_edit`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date` ,`counter`) VALUES ('$vaccu[patient_id]','$vaccu[opd_id]','$vaccu[ipd_id]','$vaccu[batch_no]','$vaccu[vaccu_id]','$vaccu[rate]','$vaccu[time]','$vaccu[date]','$counter_num') ");
			}
			// Payment
				// invest_patient_payment_details
				$inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
				mysqli_query($link, "  INSERT INTO `invest_patient_payment_details_edit`(`patient_id`, `opd_id`, `regd_fee`, `tot_amount`, `dis_per`, `dis_amt`, `dis_reason`, `advance`, `refund_amount`, `tax_amount`, `balance`, `bal_reason`, `date`, `time`, `user` ,`counter`) VALUES ('$inv_pat_pay_detail[patient_id]','$inv_pat_pay_detail[opd_id]','$inv_pat_pay_detail[regd_fee]','$inv_pat_pay_detail[tot_amount]','$inv_pat_pay_detail[dis_per]','$inv_pat_pay_detail[dis_amt]','$inv_pat_pay_detail[dis_reason]','$inv_pat_pay_detail[advance]','$inv_pat_pay_detail[balance]','$inv_pat_pay_detail[refund_amount]','$inv_pat_pay_detail[tax_amount]','$inv_pat_pay_detail[bal_reason]','$inv_pat_pay_detail[date]','$inv_pat_pay_detail[time]','$inv_pat_pay_detail[user]','$counter_num') ");
				// invest_payment_detail
				$inv_pay_detail_qry=mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
				while($inv_pay_detail=mysqli_fetch_array($inv_pay_detail_qry))
				{
					mysqli_query($link, " INSERT INTO `invest_payment_detail_edit`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date` ,`counter`) VALUES ('$inv_pay_detail[patient_id]','$inv_pay_detail[opd_id]','$inv_pay_detail[bill_no]','$inv_pay_detail[payment_mode]','$inv_pay_detail[typeofpayment]','$inv_pay_detail[amount]','$inv_pay_detail[balance]','$inv_pay_detail[balance_reason]','$inv_pay_detail[discount]','$inv_pay_detail[discount_reason]','$inv_pay_detail[refund]','$inv_pay_detail[refund_reason]','$inv_pay_detail[cheque_ref_no]','$inv_pay_detail[user]','$inv_pay_detail[time]','$inv_pay_detail[date]','$counter_num') ");
				}
		if($dis_amnt>0)
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ");
			//$dis_apprv_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' "));
			//if($dis_apprv_num==0)
			
				// Discount Approve
				mysqli_query($link, " INSERT INTO `discount_approve`(`patient_id`, `pin`, `bill_amount`, `dis_amount`, `reason`, `user`, `approve_by`, `date`, `time`) VALUES ('$uhid','$opd_id','$total','$dis_amnt','$dis_reason','$user','0','$date','$time') ");
			
		}else
		{
			mysqli_query($link, " DELETE FROM `discount_approve` WHERE `patient_id`='$uhid' AND `pin`='$opd_id' ");
		}
			
		// Delete
		mysqli_query($link, " DELETE FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		$test_all=explode("@",$test_all);
		foreach($test_all as $test)
		{
			if($test)
			{
				$test=explode("-",$test);
				$test_id=$test[0];
				$test_rate=$test[1];
				
				$test_discount=round((($test_rate*$dis_per)/100),2);
				
				// Sample ID
				$smpl=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$test_id' "));
				// Insert
				mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd_id','','1','$test_id','$smpl[SampleId]','$test_rate','$test_discount','$pat_reg[date]','$pat_reg[time]','$user','2') ");
				// Add On Test
				$sub_tst=mysqli_query($link, " SELECT * FROM `testmaster_sub` WHERE `testid`='$test_id' ");
				while($s_t=mysqli_fetch_array($sub_tst))
				{
					$samp_sb=mysqli_fetch_array(mysqli_query($link, " SELECT distinct `SampleId` FROM `TestSample` WHERE `TestId`='$s_t[sub_testid]'"));
					mysqli_query($link, " INSERT INTO `patient_test_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `testid`, `sample_id`, `test_rate`, `test_discount`, `date`, `time`, `user`, `type`) VALUES ('$uhid','$opd_id','','1','$s_t[sub_testid]','$samp_sb[SampleId]','0','0','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','2') ");
				}
			}
		}
		// Vaccu
		mysqli_query($link, " DELETE FROM `patient_vaccu_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' ");
		$ext=explode("@",$extr);
		foreach($ext as $ex)
		{
			if($ex)
			{
				$ex=explode("-",$ex);
				mysqli_query($link, " INSERT INTO `patient_vaccu_details`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `vaccu_id`, `rate`, `time`, `date`) VALUES ('$uhid','$opd_id','','1','$ex[0]','$ex[1]','$pat_reg[time]','$pat_reg[date]') ");	
				
			}	
		}
		
		//~ $inv_pat_pay_detail=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		//~ $balance=$total-$inv_pat_pay_detail["advance"];
		
		//mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `tot_amount`='$total',`balance`='$balance',`user`='$user' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		mysqli_query($link, " UPDATE `invest_patient_payment_details` SET `regd_fee`='$regd_fee',`tot_amount`='$total',`dis_per`='$dis_per',`dis_amt`='$dis_amnt',`dis_reason`='$dis_reason',`advance`='$advance',`balance`='$balance',`bal_reason`='$bal_reason' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `typeofpayment`='B' ");
		
		if($pay_mode=="Credit")
		{
			$advance=0;
			$balance=$total-$advance-$dis_amnt;
		}
		if($advance==0)
		{
			$pay_mode="Credit";
			if($total==0)
			{
				$pay_mode="Cash";
			}
		}
		
		if($advance==0)
		{
			mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='Credit' ");
		}
		
		if($balance==0)
		{
			mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='Credit' ");
		}
		
		if($advance>0 && $balance>0)
		{
			if($advance>0)
			{
				$balance_now=0;
				$bal_reason_now="";
				
				$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
				
				if($inv_pay_detail_chk)
				{
					mysqli_query($link, " UPDATE `invest_payment_detail` SET `amount`='$advance', `balance`='$balance_now', `discount`='$dis_amnt', `discount_reason`='$dis_reason', `cheque_ref_no`='$cheque_ref_no' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' ");
				}
				else
				{
					mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='Credit' and `payment_mode`!='$pay_mode' ");
					
					$bill_no=generate_bill_no($bill_name,2);
					
					mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance_now','$bal_reason_now','$dis_amnt','$dis_reason','$refund_amount','','$cheque_ref_no','$pat_reg[user]','$pat_reg[time]','$pat_reg[date]') ");
				}
			}
			if($balance>0)
			{
				$advance_now=0;
				$pay_mode="Credit";
				
				$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
				if($inv_pay_detail_chk)
				{
					mysqli_query($link, " UPDATE `invest_payment_detail` SET `amount`='$advance_now', `balance`='$balance', `balance_reason`='$bal_reason' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "); // no discount and refund and reference_no
				}
				else
				{
					$bill_no=generate_bill_no($bill_name,2);
								
					mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance_now','$balance','$bal_reason','0','','0','','','$pat_reg[user]','$pat_reg[time]','$pat_reg[date]') ");
				}
			}
		}
		else
		{
			mysqli_query($link, " DELETE FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`!='$pay_mode' ");
			
			$inv_pay_detail_chk=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' "));
				
			if($inv_pay_detail_chk)
			{
				mysqli_query($link, " UPDATE `invest_payment_detail` SET `amount`='$advance', `balance`='$balance', `balance_reason`='$bal_reason', `discount`='$dis_amnt', `discount_reason`='$dis_reason', `cheque_ref_no`='$cheque_ref_no' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' and `payment_mode`='$pay_mode' ");
			}
			else
			{
				$bill_no=generate_bill_no($bill_name,2);
				
				mysqli_query($link, " INSERT INTO `invest_payment_detail`(`patient_id`, `opd_id`, `bill_no`, `payment_mode`, `typeofpayment`, `amount`, `balance`, `balance_reason`, `discount`, `discount_reason`, `refund`, `refund_reason`, `cheque_ref_no`, `user`, `time`, `date`) VALUES ('$uhid','$opd_id','$bill_no','$pay_mode','A','$advance','$balance','$bal_reason','$dis_amnt','$dis_reason','$refund_amount','','$cheque_ref_no','$pat_reg[user]','$pat_reg[time]','$pat_reg[date]') ");
			}
		}
		
		// Check double entry
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cash' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Card' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Credit' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='Cheque' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='NEFT' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		$cash_adv_pay_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `typeofpayment`='A' AND `payment_mode`='RTGS' ");
		$cash_adv_pay_num=mysqli_num_rows($cash_adv_pay_qry);

		if($cash_adv_pay_num>1)
		{
			$h=1;
			while($cash_adv_pay_val=mysqli_fetch_array($cash_adv_pay_qry))
			{
				if($h>1)
				{
					$check_pay_mode_change=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `payment_mode_change` WHERE `patient_id`='$uhid' and `ipd_id`='$opd_id' "));
					if(!$check_pay_mode_change)
					{
						mysqli_query($link," DELETE FROM `invest_payment_detail` WHERE `slno`='$cash_adv_pay_val[slno]' ");
					}
				}
				$h++;
			}
		}
		
		
		mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$ref_doc_id', `center_no`='$sel_center', `hguide_id`='$hguide_id', `branch_id`='$branch_id' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
		
		$check_card_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_card_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));
		if($check_card_entry)
		{
			if($card_id>0)
			{
				mysqli_query($link, " UPDATE `patient_card_details` SET `card_id`='$card_id',`card_no`='$card_no',`card_details`='$card_details' WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}else
			{
				mysqli_query($link, " DELETE FROM `patient_card_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' ");
			}
		}else
		{
			if($card_id>0)
			{
				mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$uhid','$opd_id','$card_id','$card_no','$card_details') ");
			}
		}
		
		echo "Updated@".$opd_id."@".$uhid;
		
	}
}

if($_POST["type"]=="labdoctor")
{
	$cat=$_POST['cat'];
	$docid=$_POST['docid'];
	$seq=$_POST['seq'];
	$paswrd=$_POST['paswrd'];
	$user=$_POST['user'];
		
	 $qr=mysqli_query($link, "select id from lab_doctor where id='$docid'");
	 $qr1=mysqli_num_rows($qr);
	if($qr1==0)
	{
		mysqli_query($link," INSERT INTO `lab_doctor`(`id`, `sequence`, `category`, `name`, `desig`, `qual`, `phn`, `password`, `result_approve`, `status`, `dept_id`, `regd_no`, `sign_name`) VALUES ('$docid','$seq','$cat','$name','$desig','$qlfction','$phn','$paswrd','0','0','0','','') ");
		
		//mysqli_query($link, " INSERT INTO `employee`(`emp_id`, `name`, `phone` `Qualification`, `password`, `levelid`, `user`) VALUES('$docid','$name','$phn','$qlfction','$paswrd','$cat','$user')");
	}
	else
	{
	  mysqli_query($link, "update lab_doctor set sequence='$seq', category='$cat',password='$paswrd' where id='$docid'");
	  
	}
}
if($_POST["type"]=="vaccumaster")
{
	$smpid=$_POST['smpid'];
	$smpname=$_POST['smpname'];
	$rate=$_POST['rate'];
	
	$qexectve=mysqli_fetch_array(mysqli_query($link, "select * from vaccu_master where id='$smpid'"));
	if($qexectve)
	{
	   mysqli_query($link, "update vaccu_master set type='$smpname',rate='$rate' where id='$smpid'");
	}
	else
	{
	   mysqli_query($link, "insert into vaccu_master values('$smpid','$smpname','$rate')");
	}
}
if($_POST["type"]=="samplemastr")
{
	$smpid=$_POST['smpid'];
	$smpname=$_POST['smpname'];
	$qexectve=mysqli_fetch_array(mysqli_query($link, "select * from Sample where ID='$smpid'"));
	if($qexectve)
	{
		mysqli_query($link, "update Sample set Name='$smpname' where ID='$smpid'");
	}
	else
	{
		mysqli_query($link, "insert into Sample values('$smpid','$smpname')");
	}
}
if($_POST["type"]=="testmethod")
{
	$docid=$_POST['docid'];
	$docname=$_POST['docname'];	
	$qexectve=mysqli_fetch_array(mysqli_query($link, "select * from test_methods where id='$docid'"));
	if($qexectve)
	{
	   mysqli_query($link, "update test_methods set name='$docname' where id='$docid'");
	}
	else
	{
	   mysqli_query($link, "insert into test_methods values('$docid','$docname')");
	}
}
if($_POST["type"]=="resultoption")
{
	$opid=$_POST['opid'];
	$opname=$_POST['opname'];
	$qr=mysqli_query($link, "select id from  ResultOption where id='$opid'");
	$qr1=mysqli_num_rows($qr);
	if($qr1==0)
	{
		mysqli_query($link, "insert into  ResultOption values('$opid','$opname')");
	}
	else
	{
	  mysqli_query($link, "update ResultOption set name='$opname' where id='$opid'");	
	}
}
if($_POST["type"]=="option")
{
	$opid=$_POST['opid'];
	$opname=$_POST['opname'];		
	$opname=str_replace("'","''",$opname);
	$qr=mysqli_query($link, "select id from  Options where id='$opid'");
	$qr1=mysqli_num_rows($qr);
	if($qr1==0)
	{
		mysqli_query($link, "insert into  Options values('$opid','$opname')");
	}
	else
	{
	  mysqli_query($link, "update Options set name='$opname' where id='$opid'");	
	}
}
if($_POST["type"]=="save_regd_validity")
{
	$regd_fee=$_POST['regd_fee'];
	$valid=$_POST['valid'];
	$user=$_POST['user'];
	
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `registration_fees` "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `registration_fees`(`regd_fee`, `validity`, `user`) VALUES ('$regd_fee','$valid','$user') ");
	}else
	{
		mysqli_query($link, " UPDATE `registration_fees` SET `regd_fee`='$regd_fee',`validity`='$valid',`user`='$user' ");
	}
}





// After accuRate

if($_POST["type"]=="cleaning_item")
{
	$item_id=$_POST['item_id'];
	$item_name=mysqli_real_escape_string($link, $_POST['item_name']);
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `cleaning_item_master` WHERE `item_id`='$item_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `cleaning_item_master`(`item_id`, `item_name`) VALUES ('$item_id','$item_name') ");
	}else
	{
		mysqli_query($link, " UPDATE `cleaning_item_master` SET `item_name`='$item_name' WHERE `item_id`='$item_id' ");
	}
	
}
if($_POST["type"]=="cleaning_material")
{
	$item_id=$_POST['item_id'];
	$item_name=mysqli_real_escape_string($link, $_POST['item_name']);
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `cleaning_material_master` WHERE `item_mat_id`='$item_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `cleaning_material_master`(`item_mat_id`, `item_mat_name`) VALUES ('$item_id','$item_name') ");
	}else
	{
		mysqli_query($link, " UPDATE `cleaning_material_master` SET `item_mat_name`='$item_name' WHERE `item_mat_id`='$item_mat_id' ");
	}
	
}
if($_POST["type"]=="cleaning_area")
{
	$materials=$_POST['materials'];
	$item_id=$_POST['item_id'];

	if($item_id>0)
	{
		mysqli_query($link, " DELETE FROM `cleaning_area` WHERE `item_id`='$item_id' ");
		
		$materials=explode("$",$materials);
		foreach($materials as $m)
		{
			if($m)
			{
				$qq=explode("@",$m);
				mysqli_query($link, "INSERT INTO `cleaning_area`(`item_id`, `item_mat_id`, `frequency`) VALUES ('$item_id','$qq[0]','$qq[1]')");
			}
		}
	}
}
if($_POST["type"]=="cleaning_area_master")
{
	$area_name=mysqli_real_escape_string($link, $_POST['area_name']);
	mysqli_query($link, " INSERT INTO `cleaning_area_master`(`area_name`) VALUES ('$area_name') ");
}
if($_POST["type"]=="cleaning_area_master_update")
{
	$area_id=$_POST['area_id'];
	$area_name=mysqli_real_escape_string($link, $_POST['area_name']);
	mysqli_query($link, " UPDATE `cleaning_area_master` SET `area_name`='$area_name' WHERE `area_id`='$area_id' ");
}
if($_POST["type"]=="cleaning_area_data_save")
{
	$area_id=$_POST['area_id'];
	$item_id=$_POST['item_id'];
	$item_mat_id=$_POST['item_mat_id'];
	$frequency=$_POST['frequency'];
	
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `cleaning_area` WHERE `area_id`='$area_id' and `item_id`='$item_id' and `item_mat_id`='$item_mat_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `cleaning_area`(`area_id`, `item_id`, `item_mat_id`, `frequency`) VALUES ('$area_id','$item_id','$item_mat_id','$frequency') ");
		echo "11";
	}else
	{
		echo "22";
	}
}

if($_POST["type"]=="charge_group")
{
	$group_id=$_POST['group_id'];
	$group_name=mysqli_real_escape_string($link, $_POST['group_name']);
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `charge_group_master` WHERE `group_id`='$group_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `charge_group_master`(`group_id`, `group_name`) VALUES ('$group_id','$group_name') ");
	}else
	{
		mysqli_query($link, " UPDATE `charge_group_master` SET `group_name`='$group_name' WHERE `group_id`='$group_id' ");
	}
	
}
if($_POST["type"]=="charges")
{
	$charge_id=$_POST['charge_id'];
	$charge_name=mysqli_real_escape_string($link, $_POST['charge_name']);
	$group_id=$_POST['group_id'];
	$amount=$_POST['amount'];
	$user=$_POST['user'];
	$doc_link=$_POST['doc_link'];
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `charge_type`, `amount`, `user`, `doc_link`) VALUES ('$charge_id','$charge_name','$group_id','0','$amount','$user','$doc_link') ");
	}else
	{
		mysqli_query($link, " UPDATE `charge_master` SET `charge_name`='$charge_name',`group_id`='$group_id',`amount`='$amount',`user`='$user',`doc_link`='$doc_link' WHERE `charge_id`='$charge_id' ");
	}
	
	/*$charge_id=$_POST['charge_id'];
	$charge_name=$_POST['charge_name'];
	$charge_name= str_replace("'", "''", "$charge_name");
	$group_id=$_POST['group_id'];
	$charge_type=$_POST['charge_type'];
	$amount=$_POST['amount'];
	$user=$_POST['user'];
	$q_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `charge_master` WHERE `charge_id`='$charge_id' "));
	if($q_num==0)
	{
		mysqli_query($link, " INSERT INTO `charge_master`(`charge_id`, `charge_name`, `group_id`, `charge_type`, `amount`, `user`) VALUES ('$charge_id','$charge_name','$group_id','$charge_type','$amount','$user') ");
	}else
	{
		mysqli_query($link, " UPDATE `charge_master` SET `charge_name`='$charge_name',`group_id`='$group_id',`charge_type`='$charge_type',`amount`='$amount',`user`='$user' WHERE `charge_id`='$charge_id' ");
	}*/
	
}
if($_POST["type"]=="cntermaster")
{
	$cid=$_POST['cid'];
	 $lgnid=$_POST['lgnid'];
	 $cname=$_POST['cname'];
	 $address=$_POST['address'];
	 $phone=$_POST['phone'];
	 $email=$_POST['email'];
	 $testdiscnt=$_POST['testdiscnt'];
	 $crdtlmt=$_POST['crdtlmt'];
	  
	 $dpatho=$_POST['dpatho'];
	 $dultra=$_POST['dultra'];
	 $dxray=$_POST['dxray'];
	 $dcardio=$_POST['dcardio'];
	 $dspl=$_POST['dspl'];
	 
	 $cpatho=$_POST['cpatho'];
	 $cultra=$_POST['cultra'];
	 $cxray=$_POST['cxray'];
	 $ccardio=$_POST['ccardio'];
	 $cspl=$_POST['cspl'];
	 $credit=$_POST['credit'];
	 
	 $vccucharg=$_POST['vccucharg'];
	 $vonline=$_POST['vonline'];
	 $vntrqrd=$_POST['vntrqrd'];
	 $shortnm=$_POST['shortnm'];
	 $vdsnt=$_POST['vdsnt'];
	 $vinsurance=$_POST['vinsurance'];
	 $backup=$_POST['backup'];
	 
	$qr=mysqli_fetch_array(mysqli_query($link, "select centreno from centremaster where centreno='$cid'"));
	if($qr)
	{	
		
		mysqli_query($link, "update centremaster set centrename='$cname',add1='$address',phoneno='$phone',onLine='$vonline',e_mail='$email',credit_limit='$crdtlmt',c_discount='$testdiscnt',vacu_charge='$vccucharg',d_patho='$dpatho',d_ultra='$dultra',d_xray='$dxray',d_cardio='$dcardio',d_spl='$dspl',not_required='$vntrqrd',c_patho='$cpatho',c_ultra='$cultra',c_xray='$cxray',c_cardio='$ccardio',c_spl='$cspl',short_name='$shortnm',allow_credit='$credit',insurance='$vinsurance',backup='$backup' where centreno='$cid'");	
	}
	else
	{
		mysqli_query($link, "insert into centremaster(centreno,centrename,add1,phoneno,onLine,e_mail,credit_limit,c_discount,vacu_charge,d_patho,d_ultra,d_xray,d_cardio,d_spl,not_required,c_patho,c_ultra,c_xray,c_cardio,c_spl,short_name,loginid,allow_credit,insurance,backup) values('$cid','$cname','$address','$phone','$vonline','$email','$crdtlmt','$testdiscnt','$vccucharg','$dpatho','$dultra','$dxray','$dcardio','$dspl','$vntrqrd','$cpatho','$cultra','$cxray','$ccardio','$cspl','$shortnm','0','$credit','$vinsurance','$backup')");
	}
}
if($_POST["type"]=="centertest")  /// for Center Rate
{
	$cid=$_POST['mkid'];
	$testid=$_POST['testid'];
	$rate=$_POST['rate'];
	
	 mysqli_query($link, "delete from testmaster_rate where centreno='$cid' and testid='$testid'");
	 mysqli_query($link, "insert into testmaster_rate values('$testid','$rate','$cid')");
	  
}
if($_POST["type"]=="save_daily_expense")
{
	$dat=$_POST['dat'];
	$details=$_POST['details'];
	$desc=$_POST['desc'];
	$amount=$_POST['amount'];
	$user=$_POST['user'];
	
	mysqli_query($link, " INSERT INTO `expense_detail`(`details`, `description`, `amount`, `date`, `user`) VALUES ('$details','$desc','$amount','$dat','$user') ");
	
}
if($_POST["type"]=="opd_room")
{
	$cid=$_POST['cid'];
	$cname=$_POST['cname'];
	$cname= str_replace("'", "''", "$cname");
	$qr=mysqli_fetch_array(mysqli_query($link, "select room_id from opd_doctor_room where room_id='$cid'"));
	if($qr)
	{	
		mysqli_query($link, " UPDATE `opd_doctor_room` SET `room_name`='$cname' WHERE `room_id`='$cid' ");	
	}
	else
	{
		mysqli_query($link, " INSERT INTO `opd_doctor_room`(`room_id`, `room_name`) VALUES ('$cid','$cname') ");
	}
}
if($_POST["type"]=="ipd_discharge")
{
	$cid=$_POST['cid'];
	$cname=$_POST['cname'];
	$cname= str_replace("'", "''", "$cname");
	$qr=mysqli_fetch_array(mysqli_query($link, "select discharge_id from discharge_master where discharge_id='$cid'"));
	if($qr)
	{	
		mysqli_query($link, " UPDATE `discharge_master` SET `discharge_name`='$cname' WHERE `discharge_id`='$cid' ");	
	}
	else
	{
		mysqli_query($link, " INSERT INTO `discharge_master`(`discharge_id`, `discharge_name`) VALUES ('$cid','$cname') ");
	}
}
?>
