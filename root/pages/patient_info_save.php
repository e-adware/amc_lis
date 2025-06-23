<?php
	
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year =$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		if($month==0)
		{
			$day=$from->diff($to)->d;
			$age=$day;
			$age_type="Days";
		}else
		{
			$age=$month;
			$age_type="Months";
		}
	}
	else
	{
		$age=$year;
		$age_type="Years";
	}
	
	if(!$religion_id){ $religion_id=0; }
	if(!$marital_status){ $marital_status=0; }
	if(!$income_id){ $income_id=0; }
	if(!$state){ $state=0; }
	if(!$district){ $district=0; }
	if(!$file_create){ $file_create=0; }
	
	$dob=date("Y-m-d",strtotime($dob));
	
	$gd_name="";
	$g_relation="";
	$address="";
	$email="";
	$blood_group="";
	$occupation="";
	$gurdian_Occupation="";
	$education="";
	$gd_phone="";
	$pin="";
	$police="";
	$post_office="";
	$father_name="";
	$mother_name="";
	
	if($patient_id=="0")
	{
		if(mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `occupation`, `gurdian_Occupation`, `income_id`, `education`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `file_create`, `user`, `date`, `time`) VALUES (NULL,'$uhid','$pat_name_full','$gd_name','$g_relation','$sex','$dob','$age','$age_type','$phone','$address','$email','$religion_id','$blood_group','$marital_status','$occupation','$gurdian_Occupation','$income_id','$education','$gd_phone','$pin','$police','$state','$district','$city','$post_office','$father_name','$mother_name','$file_create','$user','$date','$time') "))
		{
			$last_row=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `patient_info` WHERE `name`='$pat_name_full' AND `phone`='$phone' AND `user`='$user' AND `date`='$date' AND `time`='$time' ORDER BY `slno` DESC LIMIT 0,1 "));
			
			$last_row_num=$last_row["slno"];
			
			$patient_reg_type=$p_type_id;
			include("patient_id_generator.php");
			
			$patient_id=$new_patient_id;
			$uhid_serial=$uhid_serial;
			
			mysqli_query($link," UPDATE `patient_info` SET `patient_id`='$patient_id' WHERE `slno`='$last_row_num' ");
			
			//mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$new_patient_id','$credit','$gd_phone','$crno','$pin','$police','$state','$district','$city','$fileno','$post_office','$father_name','$mother_name')");
			
			//mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
			
			$patient_id=$new_patient_id;
		}
		else
		{
			$patient_id=0;
		}
	}
	else if($opd_id!="0" || $opd_id!="")
	{
		$uhid=$patient_id;
		
		if($emp_access["edit_info"]==1 && $pat_reg_type==0)
		{
			// Edit Counter
			$edit_counter=mysqli_fetch_array(mysqli_query($link, " SELECT max(`counter`) as cntr FROM `edit_counter` WHERE `patient_id`='$patient_id' AND `opd_id`='' AND `type`='11' "));
			$edit_counter_num=$edit_counter["cntr"];
			$counter_num=$edit_counter_num+1;
			
			// edit counter record
			mysqli_query($link, " INSERT INTO `edit_counter`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `counter`) VALUES ('$patient_id','','$date','$time','$user','11','$counter_num') ");
			
			// Patient Info
			$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));
			if($pat_info)
			{
				$name_edit=mysqli_real_escape_string($link, $pat_info["name"]);
				$gd_name_edit=mysqli_real_escape_string($link, $pat_info["gd_name"]);
				$relation_edit=mysqli_real_escape_string($link, $pat_info["relation"]);
				$address_edit=mysqli_real_escape_string($link, $pat_info["address"]);
				
				//mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$name_edit','$gd_name_edit','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$address_edit','$pat_info[email]','$pat_info[refbydoctorid]','$pat_info[center_no]','$pat_info[user]','$pat_info[payment_mode]','$pat_info[blood_group]','$pat_info[date]','$pat_info[time]','$counter_num') ");
				
				mysqli_query($link," INSERT INTO `patient_info_edit`(`patient_id`, `uhid`, `name`, `gd_name`, `relation`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `religion_id`, `blood_group`, `marital_status`, `income_id`, `gd_phone`, `pin`, `police`, `state`, `district`, `city`, `post_office`, `father_name`, `mother_name`, `user`, `date`, `time`, `counter`) VALUES ('$pat_info[patient_id]','$pat_info[uhid]','$name_edit','$gd_name_edit','$relation_edit','$pat_info[sex]','$pat_info[dob]','$pat_info[age]','$pat_info[age_type]','$pat_info[phone]','$address_edit','$pat_info[email]','$pat_info[religion_id]','$pat_info[blood_group]','$pat_info[marital_status]','$pat_info[income_id]','$pat_info[gd_phone]','$pat_info[pin]','$pat_info[police]','$pat_info[state]','$pat_info[district]','$pat_info[city]','$pat_info[post_office]','$pat_info[father_name]','$pat_info[mother_name]','$pat_info[user]','$pat_info[date]','$pat_info[time]','$counter_num') ");
			}
			
			//mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name_full',`gd_name`='$gd_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`refbydoctorid`='$refbydoctorid',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
			
			mysqli_query($link," UPDATE `patient_info` SET `name`='$pat_name_full',`gd_name`='$gd_name',`relation`='$g_relation',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`email`='$email',`religion_id`='$religion_id',`blood_group`='$blood_group',`marital_status`='$marital_status',`occupation`='$occupation',`gurdian_Occupation`='$gurdian_Occupation',`income_id`='$income_id',`education`='$education',`gd_phone`='$gd_phone',`pin`='$pin',`police`='$police',`state`='$state',`district`='$district',`city`='$city',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name',`file_create`='$file_create' WHERE `patient_id`='$patient_id' ");
			
			//~ $pat_info_rel=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
			//~ if($pat_info_rel)
			//~ {
				//~ $file_no_edit=mysqli_real_escape_string($link, $pat_info_rel["file_no"]);
				//~ $post_office_edit=mysqli_real_escape_string($link, $pat_info_rel["post_office"]);
				
				//~ mysqli_query($link," INSERT INTO `patient_info_rel_edit`(`patient_id`, `credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`, `counter`) VALUES ('$pat_info_rel[patient_id]','$pat_info_rel[credit]','$pat_info_rel[gd_phone]','$pat_info_rel[crno]','$pat_info_rel[pin]','$pat_info_rel[police]','$pat_info_rel[state]','$pat_info_rel[district]','$pat_info_rel[city]','$file_no_edit','$post_office_edit','$pat_info_rel[father_name]','$pat_info_rel[mother_name]','$counter_num') ");
				
				//~ mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$gd_phone',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$district',`city`='$city',`file_no`='$fileno',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name' WHERE `patient_id`='$patient_id' ");
			//~ }else
			//~ {
				//~ mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$patient_id','$credit','$gd_phone','$crno','$pin','$police','$state','$district','$city','$fileno','$post_office','$father_name','$mother_name')");
			//~ }
			
			//~ $pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$patient_id' "));
			//~ if($pat_other)
			//~ {
				//~ mysqli_query($link," INSERT INTO `patient_other_info_edit`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`, `counter`) VALUES ('$pat_other[patient_id]','$pat_other[marital_status]','$pat_other[relation]','$pat_other[source_id]','$pat_other[esi_ip_no]','$pat_other[income_id]','$counter_num') ");
				
				//~ mysqli_query($link," UPDATE `patient_other_info` SET `marital_status`='$marital_status',`relation`='$g_relation',`source_id`='$source_id',`esi_ip_no`='$esi_ip_no',`income_id`='$income_id' WHERE `patient_id`='$patient_id'");
			//~ }else
			//~ {
				//~ mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
			//~ }
		}
	}

?>
