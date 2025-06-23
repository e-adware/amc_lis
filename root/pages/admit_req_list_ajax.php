<?php
include("../../includes/connection.php");

// Age Calculator
function age_calculator($dob)
{
	$from = new DateTime($dob);
	$to   = new DateTime('today');
	$year=$from->diff($to)->y;
	$month=$from->diff($to)->m;
	if($year==0)
	{
		//$month=$from->diff($to)->m;
		if($month==0)
		{
			$day=$from->diff($to)->d;
			return $day." Days";
		}else
		{
			return $month." Months";
		}
	}else
	{
		return $year.".".$month." Years";
	}
}

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$date=date('Y-m-d');
$time=date('H:i:s');
$type=$_POST['type'];


if($type=="load_bed_details")
{
	$uhid=$_POST['uhid'];
	?>
	<h3>Bed Details</h3>
	<?php
	$ward=mysqli_query($link,"select * from ward_master order by name");
	while($w=mysqli_fetch_array($ward))
	{
		echo "<div class='ward'>";
		echo "<b>$w[name]</b> <br/>";
		
		
		$i=1;
		$beds=mysqli_query($link,"select distinct room_id,room_no from room_master where ward_id='$w[ward_id]' order by room_no");
		while($b=mysqli_fetch_array($beds))
		{
			echo "<div style='margin:10px 0px 0px 10px'>";
			echo "<b>Room No: $b[room_no]</b> <br/>";
			$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]'");
			
			while($rd=mysqli_fetch_array($room_det))
			{
				$style="width:60px;margin-left:10px;";
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
							echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
						}
						else
						{
							$style.="background-color:#5cb85c";
							echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
						}
						
					}
					else
					{
						echo "<span class='btn' style='$style' id='$b[bed_id]' onclick=\"bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
					}
				}
				
				
				
				if($i==10)
				{
					$i=1;
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
		echo "<style>.btn{ margin-top:3px;margin-bottom:3px;}</style>";
	}
}

if($type=="ipd_bed_asign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$w_id=$_POST['w_id'];
	$b_id=$_POST['b_id'];
	$usr=$_POST['usr'];
	mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	mysqli_query($link,"INSERT INTO `ipd_bed_details_temp`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`) VALUES ('$uhid','$ipd','$w_id','$b_id')");
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
	$regtl=$_POST['regtl'];
	$regtl= str_replace("'", "''", "$regtl");
	$rank=$_POST['rank'];
	$rank= str_replace("'", "''", "$rank");
	$pat_name=$_POST['pat_name'];
	$pat_name= str_replace("'", "''", "$pat_name");
	$dob=$_POST['dob'];
	$age=$_POST['age'];
	$age_type=$_POST['age_type'];
	$sex=$_POST['sex'];
	$phone=$_POST['phone'];
	$unit=$_POST['unit'];
	$unit= str_replace("'", "''", "$unit");
	$comp=$_POST['comp'];
	$comp= str_replace("'", "''", "$comp");
	$user=$_POST['usr'];
	
	$fileno=$blood_group=$email="";
	
	if($typ=="save_opd_pat_info")
	{
		$new_patient_id=$_POST["uhid"];
		
		//--------------------------------- uhid
		$start=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid_start`,`pin_start` FROM `company_name`"));
		
		if($new_patient_id==0)
		{
			$uhid_start=$start['uhid_start'];
			
			$patient_id=100;
			
			$dis_month=date("m");
			$dis_year=date("Y");
			$dis_year_sm=date("y");
			$c_m_y=$dis_year."-".$dis_month;
			$pat_tot_num_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`patient_id`) as tot FROM `patient_info` WHERE `date` like '$c_m_y%' "));
			$pat_tot_num=$pat_tot_num_qry["tot"];
			if($pat_tot_num==0)
			{
				$patient_id=$patient_id+1;
				$uhid_db=1;
			}else
			{
				$patient_id=$patient_id+$pat_tot_num+1;
				$uhid_db=$pat_tot_num+1;
			}
			
			$uhid=$uhid_start+$uhid_db;
			
			$new_patient_id=$patient_id.$dis_month.$dis_year_sm.$user;
			$new_patient_id=trim($new_patient_id);
			$uhid=trim($uhid);
			//--------------------------------- uhid
			$ipd_visit_check=0;
			$new_visit_pat=0;
		}else
		{
			$new_visit_pat=1;
			$ipd_visit_check=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `type`='3' "));
			$pat_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT `uhid` FROM `patient_info` WHERE `patient_id`='$new_patient_id' "));
			$uhid=trim($pat_uhid["uhid"]);
		}
		if($ipd_visit_check>0)
		{
			$bed_alloc_num=mysqli_num_rows(mysqli_query($link, " SELECT distinct `ipd_id` FROM `ipd_bed_alloc_details` WHERE `patient_id`='$new_patient_id' "));
			$discharge_num=mysqli_num_rows(mysqli_query($link, " SELECT distinct `ipd_id` FROM `ipd_pat_discharge_details` WHERE `patient_id`='$new_patient_id' "));
			if($bed_alloc_num>$discharge_num)
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
		$opd_id=$start['pin_start'];
		$opd_id=$opd_id+100;
		$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` "));
		$opd_id_num=$opd_id_qry["tot"];
		
		$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` "));
		$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
		
		$vid=$opd_id+$opd_id_num+$opd_id_cancel_num+1;
		//--------------------------------- ipd
		if($already_admitted=="No")
		{
			$check_double_entry=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$new_patient_id' and `date`='$date' and `time`='$time' and `user`='$user' "));
			if($new_visit_pat==1)
			{
				$check_double_entry=0;
			}
			if($check_double_entry==0)
			{
				if($new_visit_pat==0)
				{
					mysqli_query($link, " INSERT INTO `patient_info`(`patient_id`,`uhid`, `name`, `gd_name`, `sex`, `dob`, `age`, `age_type`, `phone`, `address`, `email`, `refbydoctorid`, `center_no`, `rank`, `unit`, `company`,`user`, `payment_mode`, `blood_group`, `date`, `time`) VALUES ('$new_patient_id','$regtl','$pat_name','$g_name','$sex','$dob','$age','$age_type','$phone','$address','$email','$r_doc','','$rank','$unit','$comp','$user','$ptype','$blood_group','$date','$time') ");
					//mysqli_query($link,"INSERT INTO `patient_info_rel`(`patient_id`,`credit`, `gd_phone`, `crno`, `pin`, `police`, `state`, `district`, `city`, `file_no`) VALUES ('$new_patient_id','$credit','$g_ph','$crno','$pin','$police','$state','$dist','$city','$fileno')");
				}
				mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`) VALUES ('$new_patient_id','$vid','$date','$time','$user','3') ");
				
				$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'"));
				
				mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','1','$time','$date','$user')");
				
				mysqli_query($link,"INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$new_patient_id','$vid','$b[ward_id]','$b[bed_id]','$user','$time','$date')");
				
				mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$user'");
				
				mysqli_query($link,"INSERT INTO `ipd_pat_info`(`patient_id`, `ipd_id`, `occupation`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone_type`, `phone`, `email`, `insurance`) VALUES ('$new_patient_id','$vid','','','$address','','$city','$state','$pin','','','$phone','$email','')");
				
				//mysqli_query($link,"INSERT INTO `ipd_pat_relation`(`patient_id`, `ipd_id`, `person_type`, `name`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone`, `email`) VALUES ('$new_patient_id','$vid','','$g_name','','$address','','$city','$state','$pin','','$phone','$email')");
				
				mysqli_query($link,"INSERT INTO `ipd_pat_details`(`patient_id`, `ipd_id`, `user`, `time`, `date`) VALUES ('$new_patient_id','$vid','$user','$time','$date')");
				
				//mysqli_query($link,"INSERT INTO `ipd_pat_doc_details`(`patient_id`, `ipd_id`, `attend_doc`, `admit_doc`) VALUES ('$new_patient_id','$vid','$at_doc','$ad_doc')");
				
				echo $new_patient_id."@".$vid;
			}
			
		}else
		{
			echo "x@x";
		}
	}
	if($typ=="update_opd_pat_info")
	{
		$patient_id=trim($_POST["patient_id"]);
		$ipd_val=trim($_POST["ipd_val"]);
		
		mysqli_query($link," UPDATE `patient_info` SET  `uhid`='$regtl',`name`='$pat_name',`gd_name`='$g_name',`sex`='$sex',`dob`='$dob',`age`='$age',`age_type`='$age_type',`phone`='$phone',`address`='$address',`refbydoctorid`='$r_doc',`rank`='$rank',`unit`='$unit',`company`='$comp',`user`='$user',`payment_mode`='$ptype' WHERE `patient_id`='$patient_id' ");
		
		//mysqli_query($link," UPDATE `patient_info_rel` SET `credit`='$credit',`gd_phone`='$g_ph',`crno`='$crno',`pin`='$pin',`police`='$police',`state`='$state',`district`='$dist',`city`='$city',`file_no`='$fileno' WHERE `patient_id`='$patient_id' ");
		
		//mysqli_query($link," UPDATE `ipd_pat_doc_details` SET `attend_doc`='$at_doc',`admit_doc`='$ad_doc' WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_val' ");
		
		echo $patient_id."@".$ipd_val;
	}
}

if($type=="ipd_admit_patient")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$regtl=$_POST['regtl'];
	$crr_pin=$_POST['crr_pin'];
	$bed_id=$_POST['bed_id'];
	$usr=$_POST['usr'];
	/*
	$start=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid_start`,`pin_start` FROM `company_name`"));
	$opd_id=$start['pin_start'];
	$opd_id=$opd_id+100;
	$opd_id_qry=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid` "));
	$opd_id_num=$opd_id_qry["tot"];
	
	$opd_id_qry_cancel=mysqli_fetch_array(mysqli_query($link, " SELECT count(`opd_id`) as tot FROM `uhid_and_opdid_cancel` "));
	$opd_id_cancel_num=$opd_id_qry_cancel["tot"];
	
	$ipd=$opd_id+$opd_id_num+$opd_id_cancel_num+1;
	$ipd=trim($ipd);
	*/
	$doc=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_disposition` WHERE `patient_id`='$uhid' AND `adm_opd`='$ipd'"));

	$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
	
	mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$uhid','$ipd','$b[ward_id]','$b[bed_id]','1','$time','$date','$usr')");
	
	mysqli_query($link,"INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$uhid','$ipd','$b[ward_id]','$b[bed_id]','$usr','$time','$date')");
	
	mysqli_query($link,"UPDATE `ipd_pat_admit_details` SET `ipd_id`='$ipd' WHERE `patient_id`='$uhid' AND `ipd_id`='$doc[opd_id]'");
	
	mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	
	echo $uhid."@@".$ipd;
}


if($type=="load_patient_id")
{
	$regtl=$_POST['regtl'];
	$pat=mysqli_fetch_array(mysqli_query($link, " SELECT `patient_id` FROM `patient_info` WHERE `uhid`='$regtl'"));
	echo $pat['patient_id'];
}

if($_POST["type"]=="search_patient_list_ipd")
{
	$ward=0;
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	$usr=$_POST['usr'];
	$p_date=date('Y-m-d', strtotime('-10 days'));
	
	$qr="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `pat_disposition` a, `ipd_pat_bed_details` b WHERE a.`disposition`='1' AND a.`ref_opd`='' AND a.`date`>'$p_date' AND a.`patient_id`!=b.`patient_id` ORDER BY a.`date` DESC";
	
	if($uhid)
	{	
		$qr="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `pat_disposition` a, `ipd_pat_bed_details` b WHERE a.`disposition`='1' AND a.`ref_opd`='' AND a.`date`>'$p_date' AND a.`patient_id`!=b.`patient_id` AND a.`patient_id`='$uhid' ORDER BY a.`date` DESC";
	}
	if($ipd)
	{
		$qr="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `pat_disposition` a, `ipd_pat_bed_details` b WHERE a.`disposition`='1' AND a.`ref_opd`='' AND a.`date`>'$p_date' AND a.`patient_id`!=b.`patient_id` AND a.`opd_id`='$ipd' ORDER BY a.`date` DESC";
	}
	if($name)
	{
		$qr="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `pat_disposition` a, `ipd_pat_bed_details` b, `patient_info` c WHERE a.`disposition`='1' AND a.`ref_opd`='' AND a.`date`>'$p_date' AND a.`patient_id`!=b.`patient_id` AND a.`patient_id`=c.`patient_id` AND c.`name` like '%$name%' ORDER BY a.`date` DESC";
	}
	if($dat)
	{
		$qr="SELECT DISTINCT a.`patient_id`, a.`opd_id` FROM `pat_disposition` a, `ipd_pat_bed_details` b WHERE a.`disposition`='1' AND a.`ref_opd`='' AND a.`date`>'$p_date' AND a.`patient_id`!=b.`patient_id` AND a.`date`='$dat' ORDER BY a.`date` DESC";
	}
	//echo $qr;
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Name</th>
			<th>Sex</th>
			<th>Age (DOB)</th>
		</tr>
	<?php
		$qry=mysqli_query($link,$qr);
		while($rr=mysqli_fetch_array($qry))
		{
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$rr[patient_id]'"));
			if($p["dob"]!=""){ $age=age_calculator($p["dob"])." (".$p["dob"].")"; }else{ $age=$p["age"]." ".$p["age_type"]; }
		?>
			<tr onclick="redirect_page('<?php echo $rr['patient_id'];?>','<?php echo $rr['opd_id'];?>')" style="cursor:pointer;color:#D51111;">
				<td><?php echo $p['uhid'];?></td>
				<td><?php echo $rr['opd_id'];?></td>
				<td><?php echo $p['name'];?></td>
				<td><?php echo $p['sex'];?></td>
				<td><?php echo $age;?></td>
			</tr>
		<?php
		}
	?>
	</table>
	<?php
}

if($_POST["type"]=="nursing_bed_transfer")
{
	$uhid=$_POST['uhid'];
	$wrd=$_POST['wrd'];
	?>
	<center><h4>Bed Details</h4></center>
	
	<?php
	$uhid=$_POST[uhid];
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
			$room_det=mysqli_query($link,"select * from bed_master where room_id='$b[room_id]'");
			
			while($rd=mysqli_fetch_array($room_det))
			{
			
			$style="width:50px;margin-left:10px;";
			$chk_bd=mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]'");
			if(mysqli_num_rows($chk_bd)>0)
			{
				if(mysqli_num_rows(mysqli_query($link,"select * from ipd_bed_details_temp where ward_id='$w[ward_id]' and bed_id='$rd[bed_id]' and patient_id='$uhid'"))>0)
				{
					$style.="background-color:#5bc0de";
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
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
					$style.="background-color:#5cb85c";
					echo "<span class='btn' style='$style' id='$rd[bed_id]'>$rd[bed_no]</span>";
				}
				else
				{
					echo "<span class='btn' style='$style' id='$rd[bed_id]' onclick=\"nursing_bed_asign($w[ward_id],$rd[bed_id],'$w[name]',$rd[bed_no])\">$rd[bed_no]</span>";
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
		
		echo "</div> <hr/>";
	}
}

if($_POST["type"]=="nursing_bed_asign")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$w_id=$_POST['w_id'];
	$b_id=$_POST['b_id'];
	
	mysqli_query($link,"delete from ipd_bed_details_temp where patient_id='$uhid' and ipd_id='$ipd'");
	mysqli_query($link,"insert into ipd_bed_details_temp(patient_id,ipd_id,ward_id,bed_id) values('$uhid','$ipd','$w_id','$b_id')");
}

if($_POST["type"]=="load_bed_info")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$q=mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	$num=mysqli_num_rows($q);
	if($num>0)
	{
		$v=mysqli_fetch_array($q);
		$ward=$v['ward_id'];
		$bed=$v['bed_id'];
		$wname=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `ward_master` WHERE `ward_id`='$ward'"));
		$bname=mysqli_fetch_array(mysqli_query($link,"SELECT `bed_no` FROM `bed_master` WHERE `bed_id`='$bed'"));
		echo "<b>Selected</b><br/>Ward: ".$wname['name']."<br/>Bed No: ".$bname['bed_no']."<input type='hidden' id='ward_id' value=".$ward." /><input type='hidden' id='bed_id' value=".$bed." />";
	}
}

if($_POST["type"]=="bed_assign_ok")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$ward=$_POST['ward'];
	$bed=$_POST['bed'];
	$usr=$_POST['usr'];
	
	$pat=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$uhid'"));
	$doc=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `appointment_book` WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'"));
	$regtl=$pat['uhid'];
	$addoc_id=$doc['consultantdoctorid'];
	//--------------------------------- serial
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
	//--------------------------------- ipd
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
		$vid=trim($vid);
		
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
			$vid=trim($vid);
		}
		//-------------------------------------
		mysqli_query($link," INSERT INTO `uhid_and_opdid`(`patient_id`, `opd_id`, `date`, `time`, `user`, `type`, `ipd_serial`, `refbydoctorid`, `center_no`) VALUES ('$uhid','$vid','$date','$time','$usr','3','$opd_serial','$addoc_id','C100') ");
				
		$b=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
		
		mysqli_query($link,"INSERT INTO `ipd_bed_alloc_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `alloc_type`, `time`, `date`, `user`) VALUES ('$uhid','$vid','$b[ward_id]','$b[bed_id]','1','$time','$date','$usr')");
		
		mysqli_query($link,"INSERT INTO `ipd_pat_bed_details`(`patient_id`, `ipd_id`, `ward_id`, `bed_id`, `user`, `time`, `date`) VALUES ('$uhid','$vid','$b[ward_id]','$b[bed_id]','$usr','$time','$date')");
		
		mysqli_query($link,"DELETE FROM `ipd_bed_details_temp` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
		
		mysqli_query($link,"INSERT INTO `ipd_pat_info`(`patient_id`, `ipd_id`, `occupation`, `address_type`, `add_1`, `add_2`, `city`, `state`, `zip`, `country`, `phone_type`, `phone`, `email`, `insurance`) VALUES ('$uhid','$vid','','','$pat[address]','','','','','','','','','')");
		
		mysqli_query($link,"INSERT INTO `ipd_pat_details`(`patient_id`, `ipd_id`, `user`, `time`, `date`) VALUES ('$uhid','$vid','$usr','$time','$date')");
		
		mysqli_query($link,"UPDATE `pat_disposition` SET `ref_opd`='$vid' WHERE `patient_id`='$uhid' AND `opd_id`='$ipd'");
		
		mysqli_query($link,"INSERT INTO `ipd_pat_doc_details`(`patient_id`, `ipd_id`, `attend_doc`, `admit_doc`) VALUES ('$uhid','$vid','$addoc_id','$addoc_id')");
		
		
		echo $vid;
}

if($type=="oo")
{
	
}

?>
