<?php
include("../../includes/connection.php");
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
	<select id="dist" onkeyup="tab(this.id,event)">
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
				//$company_detaill=mysqli_fetch_array(mysqli_query($link, " SELECT `city` FROM `company_name` "));
				$company_detaill["city"]="Kamrup";
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
	mysqli_query($link,"INSERT INTO `ipd_bed_details_temp`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`) VALUES ('$usr','0','$w_id','$b_id')");
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

if($type=="day_care_pat_insert")
{
	$typ=$_POST['typ'];
	$ptype=$_POST['ptype'];
	$credit=$_POST['credit'];
	$crno=$_POST['crno'];
	$name_title=$_POST['name_title'];
	$pat_name=mysqli_real_escape_string($link, $_POST['pat_name']);
	$pat_name=trim($name_title." ".$pat_name);
	$dob=$_POST['dob'];
	$age=$_POST['age'];
	$age_type=$_POST['age_type'];
	$sex=$_POST['sex'];
	$phone=$_POST['phone'];
	$r_doc=$_POST['r_doc'];
	$g_name=mysqli_real_escape_string($link, $_POST['g_name']);
	$g_ph=$_POST['g_ph'];
	$address=mysqli_real_escape_string($link, $_POST['address']);
	$pin=$_POST['pin'];
	$police=mysqli_real_escape_string($link, $_POST['police']);
	$state=$_POST['state'];
	$dist=$_POST['dist'];
	$city=mysqli_real_escape_string($link, $_POST['city']);
	$at_doc=$_POST['at_doc'];
	$ad_doc=$_POST['ad_doc'];
	$r_doc=$_POST['r_doc'];
	$user=$_POST['usr'];
	
	$source_id=$_POST['patient_type'];
	$g_relation=$_POST['g_relation'];
	$marital_status=$_POST['marital_status'];
	
	$entry_date=$_POST['entry_date'];
	$entry_time=$_POST['entry_time'];
	
	$hguide_id=$_POST['hguide_id'];
	
	$sel_center="C100";
	
	$fileno=$blood_group=$email="";
	
	if($typ=="save_opd_pat_info")
	{
		$new_patient_id=$_POST["uhid"];
		
		$date=$entry_date;
		$time=$entry_time;
		
		//--------------------------------- uhid
		
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
			//~ //$uhid="0";
			
			$patient_reg_type=$pat_type;
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
			//$uhid="0";
		}
		
		$already_admitted="No";
		
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
					mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$uhid_serial','$pat_name','$g_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$r_doc','','$user','$ptype','$blood_group','$date','$time') ");
					mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`) VALUES ('$new_patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno')");
					
					mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`) VALUES ('$new_patient_id','$marital_status','$g_relation','$source_id') ");
					
					mysqli_query($link," INSERT INTO `pat_health_guide`(`patient_id`, `hguide_id`, `user`, `date`, `time`) VALUES ('$new_patient_id','$hguide_id','$user','$date','$time') ");
				}
				
				$serial=0;
				$dis_day=date("d");
				$dis_month=date("m");
				$dis_year=date("Y");
				$dis_year_sm=date("y");
				$c_m_y=$dis_year."-".$dis_month."-".$dis_day;
				$opd_serial_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid` WHERE `date` like '$c_m_y%' AND `type`='3' "));
				$opd_serial_num=$opd_serial_qry["tot"];
				
				$opd_serial_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`ipd_serial`) as tot FROM `uhid_and_opdid_cancel` WHERE `date` like '$c_m_y%' AND `type`='3' "));
				$opd_serial_cancel_num=$opd_serial_qry_cancel["tot"];
				
				$pat_serial_num=$opd_serial_num+$opd_serial_cancel_num;
				
				if($pat_serial_num==0)
				{
					$opd_serial=$serial+1;
				}else
				{
					$opd_serial=$serial+$pat_serial_num+1;
				}
				
				//$ipd_serial_no = str_pad($opd_serial,3,"0",STR_PAD_LEFT);
				
				//~ $vid=$opd_id=generate_pin($user);
				//~ $n=0;
				//~ while($n==0)
				//~ {
					//~ $check_double_entry_opdid=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
					//~ if($check_double_entry_opdid>0)
					//~ {
						//~ $vid=$opd_id=generate_pin($user);
						//~ $n=0;
					//~ }else
					//~ {
						//~ $n++;
					//~ }
				//~ }
				
				$opd_idds=100;
				
				$date_str=explode("-", $date);
				$dis_year=$date_str[0];
				$dis_month=$date_str[1];
				$dis_year_sm=convert_date_only_sm_year($date);
				
				$c_m_y=$dis_year."-".$dis_month;
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
					$opd_idd=$opd_idds+$pat_tot_num+1;
				}
				$vid=$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				
				$check_double_entry_opdid=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_id' "));
				if($check_double_entry_opdid!=0)
				{
					mysqli_query($link, " INSERT INTO `uhid_and_opdid` (`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('0', '0', '$date', '$time', '0', '0', '0', '0', '0') ");
					
					$opd_idds=100;
					$date_str=explode("-", $date);
					$dis_year=$date_str[0];
					$dis_month=$date_str[1];
					$dis_year_sm=convert_date_only_sm_year($date);
					
					$c_m_y=$dis_year."-".$dis_month;
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
						$opd_idd=$opd_idds+$pat_tot_num+1;
					}
					$vid=$opd_id=$opd_idd."/".$dis_month.$dis_year_sm;
				}
				
				mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('$new_patient_id','$vid','$date','$time','$user','5','$ipd_serial_no','$r_doc','$sel_center') ");
				
				mysqli_query($link,"INSERT INTO `ipd_pat_doc_details`(`patient_id`, `ipd_id`, `attend_doc`, `admit_doc`) VALUES ('$new_patient_id','$vid','$at_doc','$ad_doc')");
				
				echo $new_patient_id."@".$vid."@0";
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
		
		//~ $pa_ref_doc_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' "));
		//~ if($pa_ref_doc_num==1)
		//~ {
			//~ mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`refbydoctorid`='$r_doc',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		//~ }else
		//~ {
			//~ mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		//~ }
		
		mysqli_query($link," UPDATE `patient_info` SET  `name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		
		$info_rel_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `patient_info_rel` WHERE `patient_id`='$patient_id' "));
		if($info_rel_num==0)
		{
			mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`) VALUES ('$patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno')");
		}else
		{
			mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno' WHERE `patient_id`='$patient_id' ");
		}
		
		$pat_other=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_other_info` WHERE `patient_id`='$patient_id' "));
		if($pat_other)
		{
			mysqli_query($link," UPDATE `patient_other_info` SET `marital_status`='$marital_status',`relation`='$g_relation',`source_id`='$source_id' WHERE `patient_id`='$patient_id'");
		}else
		{
			mysqli_query($link," INSERT INTO `patient_other_info`(`patient_id`, `marital_status`, `relation`, `source_id`) VALUES ('$patient_id','$marital_status','$g_relation','$source_id') ");
		}
		
		mysqli_query($link," UPDATE `ipd_pat_doc_details` SET `attend_doc`='$at_doc',`admit_doc`='$ad_doc' WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_val' ");
		
		mysqli_query($link," UPDATE `pat_health_guide` SET `hguide_id`='$hguide_id' WHERE `patient_id`='$patient_id' ");
		
		mysqli_query($link, " UPDATE `uhid_and_opdid` SET `refbydoctorid`='$r_doc' WHERE `patient_id`='$uhid' and `opd_id`='$ipd_val' ");
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
