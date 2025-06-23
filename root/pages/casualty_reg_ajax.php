<?php
session_start();

$c_user=$_SESSION["emp_id"];

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");
$type=$_POST['type'];

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

if($type=="load_district")
{
	$state=$_POST['state'];
	$patient_id=$_POST['patient_id'];
	$q=mysqli_query($link,"SELECT * FROM `district` WHERE `state_id`='$state' ORDER BY `name`");
	?>
	<select class="span2" id="dist" onkeyup="tab(this.id,event)">
		<option value="0">Select</option>
		<?php
		while($r=mysqli_fetch_array($q))
		{
			if($patient_id>0)
			{
				$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT `district` FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
				if($pat_info_rel["district"]==$r['district_id']){ $sel_state="selected"; }else{ $sel_state=""; }
			}else
			{
				$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `city` FROM `company_name` "));
				//$company_detaill["city"]="Kamrup";
				if($company_detaill["city"]==$r['name']){ $sel_state="selected"; }else{ $sel_state=""; }
			}
		?>
		<option value="<?php echo $r['district_id']; ?>" <?php echo $sel_state; ?>><?php echo $r['name']; ?></option>
		<?php
		}
		?>
	</select>
	<?php
}

if($type=="casualty_pat_insert")
{
	$typ=$_POST['typ'];
	$p_type_id=$_POST['pat_type'];
	$ptype=$_POST['ptype'];
	if(!$ptype){ $ptype=0; }
	$credit=$_POST['credit'];
	$crno=$_POST['crno'];
	if(!$crno){ $crno=0; }
	$name_title=$_POST['name_title'];
	$pat_name=mysqli_real_escape_string($link, $_POST['pat_name']);
	$pat_name=trim($name_title." ".$pat_name);
	$dob=$_POST['dob'];
	$age=$_POST['age'];
	$age_type=$_POST['age_type'];
	$sex=$_POST['sex'];
	$phone=$_POST['phone'];
	$r_doc=$_POST['r_doc'];
	if(!$r_doc){ $r_doc=0; }
	$g_name=mysqli_real_escape_string($link, $_POST['g_name']);
	$g_ph=$_POST['g_ph'];
	$address=mysqli_real_escape_string($link, $_POST['address']);
	$pin=$_POST['pin'];
	$police=mysqli_real_escape_string($link, $_POST['police']);
	$state=$_POST['state'];
	if(!$state){ $state=0; }
	$dist=$_POST['dist'];
	if(!$dist){ $dist=0; }
	$city=mysqli_real_escape_string($link, $_POST['city']);
	
	$user=$_POST['usr'];
	
	$source_id=$_POST['patient_type'];
	if(!$source_id){ $source_id=0; }
	$g_relation=$_POST['g_relation'];
	$marital_status=$_POST['marital_status'];
	if(!$marital_status){ $marital_status=0; }
	$income_id=$_POST['income_id'];
	if(!$income_id){ $income_id=0; }
	
	$esi_ip_no="";
	$post_office=mysqli_real_escape_string($link, $_POST['post_office']);
	
	$visit_type_id=$_POST['visit_type_id'];
	
	$card_id=$_POST['card_id'];
	
	$card_no="";
	$card_details="";
	
	$r_doc=$_POST['r_doc'];
	$hguide_id=$_POST['hguide_id'];
	
	$branch_id=1;
	
	$hguide_id=$_POST['hguide_id'];
	if(!$hguide_id)
	{
		$hguide_id=101;
	}
	
	$age_str=age_calculator_save($dob);
	$age_str=explode(" ",$age_str);
	
	$age=$age_str[0];
	$age_type=$age_str[1];
	
	$sel_center="C100";
	$center_info=mysqli_fetch_array(mysqli_query($link, " SELECT `centreno` FROM `patient_source_master` WHERE `source_id`='$source_id' "));
	if($center_info)
	{
		$sel_center=$center_info["centreno"];
	}
	
	$e_doc_id=$_POST['e_doc_id'];
	
	$pat_visit_type=mysqli_real_escape_string($link, $_POST['pat_visit_type']);
	$police_detail_station=mysqli_real_escape_string($link, $_POST['police_detail_station']);
	$police_detail_officer=mysqli_real_escape_string($link, $_POST['police_detail_officer']);
	$police_detail_case_no=mysqli_real_escape_string($link, $_POST['police_detail_case_no']);
	$police_detail_date=$_POST['police_detail_date'];
	$police_detail_time=$_POST['police_detail_time'];
	$police_detail_desc=mysqli_real_escape_string($link, $_POST['police_detail_desc']);
	
	$father_name="";
	$mother_name="";
	
	$entry_date=$_POST['entry_date'];
	$entry_time=$_POST['entry_time'];
	
	if($typ=="save_opd_pat_info")
	{
		$date=$entry_date;
		$time=$entry_time;
		
		$fileno=$blood_group=$email="";
		
		$new_patient_id=$_POST["uhid"];
		
		//--------------------------------- uhid
		
		if($new_patient_id=="0")
		{
			$patient_reg_type=$p_type_id;
			include("patient_id_generator.php");
			
			$ipd_visit_check=0;
			$new_visit_pat=0;
			
		}else
		{
			$new_visit_pat=1;
			$ipd_visit_check=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$new_patient_id' AND `type`='$p_type_id' "));
			//~ $pat_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid` FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
			//~ $uhid_serial=trim($pat_uhid["uhid"]);
			
		}
		
		$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id'"));
		if($new_visit_pat==1)
		{
			$check_double_entry=0;
		}
		if($check_double_entry==0)
		{
			if($new_visit_pat==0)
			{
				$same_double_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `name`='$pat_name' AND `gd_name`='$g_name' AND `sex`='$sex' AND `dob`='$dob' AND `age`='$age' AND `age_type`='$age_type' AND `phone`='$phone' AND `user`='$user' AND `date`='$date' "));
				if(!$same_double_entry)
				{
					mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$pat_name','$g_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$r_doc','','$user','$ptype','$blood_group','$date','$time') ");
					
					mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$new_patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office','$father_name','$mother_name')");
					
					mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
				
					//mysqli_query($link," INSERT INTO `pat_health_guide`(`patient_id`, `hguide_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$hguide_id','$user','$date','$time') ");
					
					mysqli_query($link," INSERT INTO `pat_ref_doc`(`patient_id`, `pin`, `refbydoctorid`, `user`, `date`, `time`) VALUES ('$new_patient_id','$vid','$r_doc','$user','$date','$time') ");
					
				}else
				{
					$new_patient_id=$same_double_entry["patient_id"];
				}
			}
			
			// Serial Generate
			$casualty_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `casualty_serial_generator` "));
			if(!$casualty_data)
			{
				//mysqli_query($link, " TRUNCATE TABLE `casualty_serial_generator` ");
			}

			mysqli_query($link, " INSERT INTO `casualty_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$user','$date','$time') ");
			
			$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `casualty_serial_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$casualty_serial=$last_slno["slno"];
			
			// PIN Generate
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
				$vid=$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}else
			{
				$c_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pin_generator` WHERE `date` LIKE '$c_m_y%' "));
				if(!$c_data)
				{
					mysqli_query($link, " TRUNCATE TABLE `pin_generator` ");
				}

				mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$new_patient_id','$p_type_id','$user','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$last_slno=$last_slno["slno"];
				
				//mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
				
				$opd_idd=$opd_idds+$last_slno;
				$vid=$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
			}
			
			//~ $check_double_entry_same_time_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$new_patient_id' AND `date`='$date' AND `time`='$time' AND `user`='$user' "));
			//~ if($check_double_entry_same_time_num==0)
			//~ {
				//~ mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('$new_patient_id','$vid','$date','$time','$user','$p_type_id','$opd_serial','$r_doc','$sel_center') ");
			//~ }
			
			if(mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$new_patient_id','$vid','$date','$time','$user','$p_type_id','$casualty_serial','$r_doc','$sel_center','$hguide_id','$branch_id') "))
			{
				mysqli_query($link, " INSERT INTO `emergency_patient_details`(`patient_id`, `opd_id`, `consultantdoctorid`, `type_id`, `police_station`, `officer`, `case_no`, `case_date`, `case_time`, `description`, `date`, `time`, `user`) VALUES ('$new_patient_id','$opd_id','$e_doc_id','$pat_visit_type','$police_detail_station','$police_detail_officer','$police_detail_case_no','$police_detail_date','$police_detail_time','$police_detail_desc','$date','$time','$user') ");
				
				if($visit_type_id>0)
				{
					mysqli_query($link, " INSERT INTO `patient_visit_type_details`(`patient_id`, `opd_id`, `p_type_id`, `visit_type_id`,`date`,`time`) VALUES ('$new_patient_id','$opd_id','$p_type_id','$visit_type_id','$date','$time') ");
				}
				
				if($card_id>0)
				{
					mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$new_patient_id','$opd_id','$card_id','$card_no','$card_details') ");
				}
				
				echo $new_patient_id."@".$opd_id."@0@".$p_type_id;
			}else
			{
				echo "x@x@1@0"; // Already exists
			}
		}else
		{
			//mysqli_query($link," INSERT INTO `patient_info` (`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('0', '0', '', '', '', '', '', '', '', '', '', '0', '', '0', '', '', '$date', '$time') ");
			
			echo "x@x@1@0"; // Already exists
		}
	}
	if($typ=="update_opd_pat_info")
	{
		$patient_id=trim($_POST["patient_id"]);
		$ipd_val=trim($_POST["ipd_val"]);
		
		// Edit Counter
		$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT count(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_val' AND `type`='11' "));
		$edit_counter_num=$edit_counter["cntr"];
		$counter_num=$edit_counter_num+1;
		
		// edit counter record
		mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','$ipd_val','$date','$time','$user','11','$counter_num') ");
		
		$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
		if($pat_info)
		{
			mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$pat_info[name]','$pat_info[gd_name]','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$pat_info[address]','$pat_info[email]','$pat_info[refbydoctorid]','$pat_info[center_no]','$pat_info[user]','$pat_info[payment_mode]','$pat_info[blood_group]','$pat_info[date]','$pat_info[time]','$counter_num') ");
		}
		
		mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`refbydoctorid`='$r_doc',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
		if($pat_info_rel)
		{
			mysqli_query($link," INSERT INTO `patient_info_rel_edit`(`patient_id`, `credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`, `counter`) VALUES ('$pat_info_rel[patient_id]','$pat_info_rel[credit]','$pat_info_rel[gd_phone]','$pat_info_rel[crno]','$pat_info_rel[pin]','$pat_info_rel[police]','$pat_info_rel[state]','$pat_info_rel[district]','$pat_info_rel[city]','$pat_info_rel[file_no]','$pat_info_rel[post_office]','$pat_info_rel[father_name]','$pat_info_rel[mother_name]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name' WHERE `patient_id`='$patient_id' ");
		}else
		{
			mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office','$father_name','$mother_name')");
		}
		
		$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$patient_id' "));
		if($pat_other)
		{
			mysqli_query($link," INSERT INTO `patient_other_info_edit`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`, `counter`) VALUES ('$pat_other[patient_id]','$pat_other[marital_status]','$pat_other[relation]','$pat_other[source_id]','$pat_other[esi_ip_no]','$pat_other[income_id]','$counter_num') ");
			
			mysqli_query($link," UPDATE `patient_other_info` SET `marital_status`='$marital_status',`relation`='$g_relation',`source_id`='$source_id',`esi_ip_no`='$esi_ip_no',`income_id`='$income_id' WHERE `patient_id`='$patient_id'");
		}else
		{
			mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
		}
		
		$pat_emergency_det=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `emergency_patient_details` WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' "));
	
		mysqli_query($link, "  INSERT INTO `emergency_patient_details_edit`(`patient_id`, `opd_id`, `consultantdoctorid`, `type_id`, `police_station`, `officer`, `case_no`, `case_date`, `case_time`, `description`, `date`, `time`, `user`, `counter`) VALUES ('$pat_emergency_det[patient_id]','$pat_emergency_det[opd_id]','$pat_emergency_det[consultantdoctorid]','$pat_emergency_det[type_id]','$pat_emergency_det[police_station]','$pat_emergency_det[officer]','$pat_emergency_det[case_no]','$pat_emergency_det[case_date]','$pat_emergency_det[case_time]','$pat_emergency_det[description]','$pat_emergency_det[date]','$pat_emergency_det[time]','$pat_emergency_det[user]','$counter_num') ");
			
		mysqli_query($link," UPDATE `emergency_patient_details` SET `consultantdoctorid`='$e_doc_id',`type_id`='$pat_visit_type',`police_station`='$police_detail_station',`officer`='$police_detail_officer',`case_no`='$police_detail_case_no',`case_date`='$police_detail_date',`case_time`='$police_detail_time',`description`='$police_detail_desc',`date`='$date',`time`='$time',`user`='$user' WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' ");
		
		mysqli_query($link," UPDATE `pat_ref_doc` SET `refbydoctorid`='$r_doc',`user`='$user' WHERE `patient_id`='$patient_id' and `pin`='$ipd_val' ");
		
		//mysqli_query($link," UPDATE `pat_health_guide` SET `hguide_id`='$hguide_id' WHERE `patient_id`='$patient_id' ");
		
		$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' "));
		
		mysqli_query($link, " INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `counter`) VALUES ('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','$pat_reg[type]','$pat_reg[ipd_serial]','$pat_reg[refbydoctorid]','$pat_reg[center_no]','$pat_reg[hguide_id]','$pat_reg[branch_id]','$counter_num') ");
		
		mysqli_query($link," UPDATE `uhid_and_opdid` SET `refbydoctorid`='$r_doc', `type`='$p_type_id',`center_no`='$sel_center',`hguide_id`='$hguide_id',`branch_id`='$branch_id' WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_val' ");
		
		$pat_type_val=mysqli_fetch_array(mysqli_query($link," SELECT `type` FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_val' "));
		
		mysqli_query($link, " UPDATE `patient_visit_type_details` SET `p_type_id`='$p_type_id',`visit_type_id`='$visit_type_id' WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' ");
		
		$check_card_entry=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' "));
		if($check_card_entry)
		{
			if($card_id>0)
			{
				mysqli_query($link, " UPDATE `patient_card_details` SET `card_id`='$card_id',`card_no`='$card_no',`card_details`='$card_details' WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' ");
			}else
			{
				mysqli_query($link, " DELETE FROM `patient_card_details` WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' ");
			}
		}else
		{
			if($card_id>0)
			{
				mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$patient_id','$ipd_val','$card_id','$card_no','$card_details') ");
			}
		}
		
		echo $patient_id."@".$ipd_val."@0@".$pat_type_val["type"];
		
	}
}

if($type=="oo")
{
	
}

?>
