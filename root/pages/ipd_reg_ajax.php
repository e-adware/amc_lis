<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");
$date=date('Y-m-d'); // impotant
$time=date('H:i:s');
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
				//$company_detaill["city"]="Sivasagar";
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

if($type=="load_bed_details")
{
	$uhid=$_POST['usr'];
	?>
	<h3>Bed Details</h3>
	<?php
	$ward=mysqli_query($link,"select * from ward_master order by ward_id");
	while($w=mysqli_fetch_array($ward))
	{
		echo "<div class='ward'>";
		echo "<b>$w[name]</b> <br/>";
		
		
		$i=0;
		$beds=mysqli_query($link,"select distinct room_id,room_no from room_master where ward_id='$w[ward_id]' order by room_no");
		while($b=mysqli_fetch_array($beds))
		{
			echo "<div style='margin:10px 0px 0px 10px'>";
			echo "<b>Room No: $b[room_no]</b> <br/>";
			//$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]' order by bed_no");
			$room_det=mysqli_query($link,"select * from bed_master where ward_id='$w[ward_id]' and room_id='$b[room_id]' order by bed_no");
			while($rd=mysqli_fetch_array($room_det))
			{
				$style="width:50px;margin-left:10px;";
				$chk_bd=mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
				if(mysqli_num_rows($chk_bd)>0)
				{
					if(mysqli_num_rows(mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]' and patient_id='$uhid'"))>0)
					{
						$style.="background-color:#5bc0de";
						echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
					}
					else
					{
						$style.="background-color:#ff8a80";
						echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
					}
				}
				else if($rd[status]==1)
				{
					$style.="background-color:#ffbb33";
					echo "<span class='btn' style='$style' id='$rd[bed_id]' >$rd[bed_no]</span>";
				}
				else
				{
					$chk_bd_main=mysqli_query($link,"select * from ipd_pat_bed_details where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
					
					if(mysqli_num_rows($chk_bd_main)>0)
					{
						
						$chk_bd_ipd=mysqli_num_rows(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$uhid' and ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'"));
						if($chk_bd_ipd>0)
						{
							$style.="background-color:#5cb85c;font-weight:bold;text-decoration:underline";
							echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]')\">$rd[bed_no]</span>";
						}
						else
						{
							$style.="background-color:#5cb85c";
							echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
						}
						
					}
					else
					{
						echo "<span class='btn' style='$style' id='$b[bed_id]' onclick=\"bed_asign('$w[ward_id]','$rd[bed_id]','$w[name]','$rd[bed_no]')\">$rd[bed_no]</span>";
					}
				}
				
				
				
				if($i==10)
				{
					$i=0;
					echo "<br/>";
				}
				else
				{
					$i++;
				}
				
			}
			echo "</div>";
		}
		echo "<br/>";
		echo "</div> <hr/>";
	}
}

if($type=="ipd_bed_asign")
{
	$w_id=$_POST['w_id'];
	$b_id=$_POST['b_id'];
	$usr=$_POST['usr'];
	mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$usr'");
	mysqli_query($link,"INSERT INTO `ipd_bed_details_temp`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `date`) VALUES ('$usr','0','$w_id','$b_id','$date')");
}

if($type=="load_bed_stat")
{
	$user=$_POST['usr'];
	
	
	$al_bed=mysqli_query($link,"select * from bill_ipd_bed_details where patient_id='$uhid' and ipd_id='$ipd' and manip!='1' order by date_from asc");
	$chk_stat=mysqli_num_rows($al_bed);
	if($chk_stat>0)
	{
		?>
		<span id="tot_bed_cost"></span>
		
		<table class="table table-bordered table-condensed">
		<tr>
			<th>Ward No</th><th> Bed No</th> <th>Occupied On</th><th>Released On</th><th>Days</th><th>Cost</th><th></th>
		</tr>
		<?php
		$tot=0;
		while($al_b=mysqli_fetch_array($al_bed))
		{
			$bed_no=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$al_b[bed_id]'"));
			$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_no[ward_id]'"));
			echo "<tr><th>$ward[name]</th><th>$bed_no[bed_no]</th><th>".convert_date($al_b[date_from])." </th>";
			echo "<th>".convert_date($al_b[date_to])." </th>";
			
			
			$diff=abs(strtotime($al_b[date_from])-strtotime($al_b[date_to]));
			$diff=$diff/60/60/24;
			
			$tot=$tot+$al_b[tot_amount];
			echo "<th>$diff</th><th>$al_b[tot_amount]</th><th><button' class='btn btn-danger btn-mini' onclick='bed_edit($al_b[slno])'><i class=icon-edit></i> Edit</button></th></tr>";
			?>
				
				<!--
				<table class="table table-condensed">
					<tr>
						<th>Requested Bed</th>
					</tr>
					<tr>
						<th>
							<div id="bed_info">
								Ward No: <?php echo $al_b[ward_id];?> <br/>
								Bed No : <?php echo $bed_no[bed_no];?> 
							</div>
						</th>
					</tr>
					<tr>
						<th>
							<input type="button" class="btn btn-info" value="Request Different Bed" onclick="load_bed_details()"/>
							<input type="button" class="btn btn-info" value="Allocate Bed" onclick="allocate_bed()"/>
						</th>
					</tr>
				</table>
				-->
			<?php
		}
		?> 
		
		</table> 
		<!--<input type="button" class="btn btn-info" value="Bed Tranfer" onclick="load_bed_details()"/>-->
		<button class="btn btn-info" onclick="add_more_bed()"><i class="icon-plus"></i> Add More</button>
		<script>$("#tot_bed_cost").html("<b><i>Total Bed Cost: <?php echo $tot;?></i></b><br/><br/>");</script>
		<?php
	}
}

if($type=="ipd_pat_insert")
{
	$typ=$_POST['typ'];
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
	$at_doc=$_POST['at_doc'];
	$ad_doc=$_POST['ad_doc'];
	$r_doc=$_POST['r_doc'];
	$user=$_POST['usr'];
	
	$source_id=$_POST['patient_type'];
	if(!$source_id){ $source_id=0; }
	$g_relation=$_POST['g_relation'];
	$marital_status=$_POST['marital_status'];
	if(!$marital_status){ $marital_status=0; }
	$income_id=$_POST['income_id'];
	if(!$income_id){ $income_id=0; }
	
	$entry_date=$_POST['entry_date'];
	$entry_time=$_POST['entry_time'];
	
	$branch_id=1;
	
	$hguide_id=$_POST['hguide_id'];
	if(!$hguide_id)
	{
		$hguide_id=101;
	}
	
	$esi_ip_no="";
	$post_office=mysqli_real_escape_string($link, $_POST['post_office']);
	
	$card_id=$_POST['card_id'];
	
	$card_no="";
	$card_details="";
	
	//$sel_center="C100";
	
	$age_str=age_calculator_save($dob);
	$age_str=explode(" ",$age_str);
	
	$age=$age_str[0];
	$age_type=$age_str[1];
	
	// Patient Center
	$pat_source=mysqli_fetch_array(mysqli_query($link," SELECT `centreno` FROM `patient_source_master` WHERE `source_id`='$source_id' "));
	$sel_center=$pat_source["centreno"];
	
	$fileno=$blood_group=$email="";
	
	if($typ=="save_opd_pat_info")
	{
		$new_patient_id=$_POST["uhid"];
		
		$date=$entry_date;
		$time=$entry_time;
		
		//// date difference //////
		$date_str_less=$date.' '.$time;
		$date_str_now=date("Y-m-d").' '.date("H:i:s");

		$start_date = new DateTime($date_str_less);
		$since_start = $start_date->diff(new DateTime($date_str_now));

		$minutes = $since_start->days * 24 * 60;
		$minutes += $since_start->h * 60;
		$minutes += $since_start->i;
		if($minutes>86400) // not more than two months
		{
			$date=date('Y-m-d'); // impotant
			$time=date('H:i:s');
		}
		//// date difference //////
		
		if($new_patient_id=='0')
		{
			//~ $uhid_start=$start['uhid_start'];
			
			//~ $patient_id=100;
			
			//~ $date_str=explode("-", $date);
			//~ $dis_year=$date_str[0];
			//~ $dis_month=$date_str[1];
			//~ $dis_year_sm=convert_date_only_sm_year($date);
			
			//~ $c_m_y=$dis_year."-".$dis_month;
			//~ $pat_tot_num_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`patient_id`) as tot FROM `patient_info` WHERE `date` like '$c_m_y%' "));
			//~ $pat_tot_num=$pat_tot_num_qry["tot"];
			
			//~ if($pat_tot_num==0)
			//~ {
				//~ $patient_id=$patient_id+1;
			//~ }else
			//~ {
				//~ $patient_id=$patient_id+$pat_tot_num+1;
			//~ }
			
			//~ $new_patient_id=$patient_id.$dis_month.$dis_year_sm.$user;
			
			//~ $pat_uhid_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`uhid`) as uhid_tot FROM `patient_info` WHERE `date` like '$dis_year-%'"));
			//~ $pat_uhid_num=$pat_uhid_qry["uhid_tot"];
			//~ $uhid=$uhid_start+$pat_uhid_num+1;
			//~ $uhid=trim($uhid.$dis_year_sm);
			
			$patient_reg_type=3;
			include("patient_id_generator.php");
			
			//--------------------------------- uhid
			$ipd_visit_check=0;
			$new_visit_pat=0;
			
			//$new_patient_id=generate_uhid($user);
		}else
		{
			$new_visit_pat=1;
			$ipd_visit_check=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$new_patient_id' AND `type`='3' "));
			$pat_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid` FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
			$uhid_serial=trim($pat_uhid["uhid"]);
			//$uhid_serial="0";
		}
		if($ipd_visit_check>0)
		{
			$pat_bed_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_bed_details` WHERE `patient_id`='$new_patient_id' "));
			if($pat_bed_num>0)
			{
				$already_admitted="Yes";
			}else
			{
				$already_admitted="No";
			}
		}else
		{
			$already_admitted="No";
		}
		//--------------------------------- ipd
		//~ $opd_id=$start['pin_start'];
		//~ $opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` "));
		//~ $opd_id_num=$opd_id_qry["tot"];
		
		//~ $opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` "));
		//~ $opd_id_cancel_num=$opd_id_qry_cancel["tot"];
		
		//~ $vid=$opd_id+$opd_id_num+$opd_id_cancel_num+1;
		
		//--------------------------------- ipd
		if($already_admitted=="No")
		{
			$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
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
					}else
					{
						$new_patient_id=$same_double_entry["patient_id"];
					}
				}
				
				if($new_visit_pat==1)
				{
					mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$new_patient_id' ");
		
					$info_rel_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$new_patient_id' "));
					if($info_rel_num==0)
					{
						mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`, `post_office`, `father_name`, `mother_name`) VALUES ('$new_patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno','$post_office','$father_name','$mother_name')");
					}else
					{
						mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno',`post_office`='$post_office',`father_name`='$father_name',`mother_name`='$mother_name' WHERE `patient_id`='$new_patient_id' ");
					}
					
					$pat_other=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$new_patient_id' "));
					if($pat_other)
					{
						mysqli_query($link," UPDATE `patient_other_info` SET `marital_status`='$marital_status',`relation`='$g_relation',`source_id`='$source_id',`esi_ip_no`='$esi_ip_no',`income_id`='$income_id' WHERE `patient_id`='$new_patient_id'");
					}else
					{
						mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`, `esi_ip_no`, `income_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id','$esi_ip_no','$income_id') ");
					}
				}
				
				$ipd_data=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_serial_generator` "));
				if(!$ipd_data)
				{
					//mysqli_query($link, " TRUNCATE TABLE `ipd_serial_generator` ");
				}

				mysqli_query($link, " INSERT INTO `ipd_serial_generator`(`patient_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$user','$date','$time') ");
				
				$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `ipd_serial_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
				
				$ipd_serial=$last_slno["slno"];
				
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

					mysqli_query($link, " INSERT INTO `pin_generator`(`patient_id`, `type`, `user`, `date`, `time`) VALUES ('$new_patient_id','3','$user','$date','$time') ");
					
					$last_slno=mysqli_fetch_array(mysqli_query($link, " SELECT `slno` FROM `pin_generator` WHERE `patient_id`='$new_patient_id' AND `user`='$user' ORDER BY `slno` DESC LIMIT 0,1 "));
					
					$last_slno=$last_slno["slno"];
					
					//mysqli_query($link, " DELETE FROM `pin_generator` WHERE `slno`='$last_slno' ");
					
					$opd_idd=$opd_idds+$last_slno;
					$vid=$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}
				
				if(mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`) VALUES ('$new_patient_id','$opd_id','$date','$time','$user','3','$ipd_serial','$r_doc','$sel_center','$hguide_id','$branch_id') "))
				{
					$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'"));
				
					mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','1','$time','$date','$user')");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','$user','$time','$date')");
					
					mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_info`(`patient_id`, `ipd_id`, `occupation`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone_type`, `phone`, `email`, `insurance`) VALUES ('$new_patient_id','$vid','','','$address','','$city','$state','$pin','','','$phone','$email','')");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_relation`(`patient_id`, `ipd_id`, `person_type`, `name`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone`, `email`) VALUES ('$new_patient_id','$vid','','$g_name','','$address','','$city','$state','$pin','','$phone','$email')");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_details`(`patient_id`, `ipd_id`, `user`, `time`, `date`) VALUES ('$new_patient_id','$vid','$user','$time','$date')");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_doc_details`(`patient_id`, `ipd_id`, `attend_doc`, `admit_doc`) VALUES ('$new_patient_id','$vid','$at_doc','$ad_doc')");
					
					mysqli_query($link,"INSERT INTO `ipd_pat_doc_transfer`(`patient_id`, `ipd_id`, `attend_doc`, `status`, `date`, `time`, `user`) VALUES ('$new_patient_id','$vid','$at_doc','1','$date','$time','$user')");
					
					mysqli_query($link," DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$user' ");
					
					if($card_id>0)
					{
						mysqli_query($link, " INSERT INTO `patient_card_details`(`patient_id`, `opd_id`, `card_id`, `card_no`, `card_details`) VALUES ('$new_patient_id','$vid','$card_id','$card_no','$card_details') ");
					}
					
					echo $new_patient_id."@".$vid."@0";
				}else
				{
					echo "x@x@1"; // Already exists
				}
			}else
			{
				mysqli_query($link," INSERT INTO `patient_info` (`patient_id`, `uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('0', '0', '', '', '', '', '', '', '', '', '', '0', '', '0', '', '', '$date', '$time') ");
				
				echo "x@x@1"; // Already exists
			}
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
		
		mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		
		$pat_info_rel=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
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
		
		$pat_bed=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_doc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_val' "));
		
		mysqli_query($link, " INSERT INTO `ipd_pat_doc_details_edit`(`patient_id`, `ipd_id`, `attend_doc`, `admit_doc`, `counter`) VALUES ('$pat_bed[patient_id]','$pat_bed[ipd_id]','$pat_bed[attend_doc]','$pat_bed[admit_doc]','$counter_num') ");
		
		mysqli_query($link," UPDATE `ipd_pat_doc_details` SET `attend_doc`='$at_doc',`admit_doc`='$ad_doc' WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_val' ");
		
		$m_slno=mysqli_fetch_array(mysqli_query($link," SELECT MAX(`slno`) AS `max_slno` FROM `ipd_pat_doc_transfer` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_val' "));
		
		mysqli_query($link," UPDATE `ipd_pat_doc_transfer` SET `attend_doc`='$at_doc', `user`='$user' WHERE `slno`='$m_slno[max_slno]' ");
		
		mysqli_query($link," UPDATE `pat_ref_doc` SET `refbydoctorid`='$r_doc',`user`='$user' WHERE `patient_id`='$patient_id' and `pin`='$ipd_val' ");
		
		//mysqli_query($link," UPDATE `pat_health_guide` SET `hguide_id`='$hguide_id' WHERE `patient_id`='$patient_id' ");
		
		$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'"));
		if($b)
		{				
			//mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','1','$time','$date','$user')");
			
			//mysqli_query($link,"INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','$user','$time','$date')");
			
			//mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'");
		}
		
		$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' "));
		
		mysqli_query($link, " INSERT INTO `uhid_and_opdid_edit`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`, `hguide_id`, `branch_id`, `counter`) VALUES ('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[date]','$pat_reg[time]','$pat_reg[user]','$pat_reg[type]','$pat_reg[ipd_serial]','$pat_reg[refbydoctorid]','$pat_reg[center_no]','$pat_reg[hguide_id]','$pat_reg[branch_id]','$counter_num') ");
		
		mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$r_doc',`center_no`='$sel_center',`hguide_id`='$hguide_id',`branch_id`='$branch_id' WHERE `patient_id`='$patient_id' and `opd_id`='$ipd_val' ");
		
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
		
		echo $patient_id."@".$ipd_val."@0";
	}
}

if($type=="clear_temp_bed")
{
	mysqli_query($link, " TRUNCATE TABLE `ipd_bed_details_temp` ");
}
if($type=="oo")
{
	
}

?>
