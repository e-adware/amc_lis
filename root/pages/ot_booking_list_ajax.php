<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');
//date_default_timezone_set("Asia/Kolkata");
$date=date("Y-m-d");
$time=date("H:i:s");

//---------------------------------------------------------------------------------------------------//
function val_con($val)
{
	$nval=explode(".",$val);
	if(!$nval[1])
	{
		return $val.".00";	
	}
	else
	{
		if(!$nval[1][1])
		{
			return $val."0";	
		}
		else
		{
			return $val;	
		}
	}
}
//-------------------------------------------------------------------------------------------------//


if($_POST["type"]=="ot_booking_list")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$d_id=$_POST['d_id'];
	$p_id=$_POST['p_id'];
	$g_id=$_POST['g_id'];
	$s_id=$_POST['s_id'];
	$as_id=$_POST['as_id'];
	$a_id=$_POST['a_id'];
	$pd_id=$_POST['pd_id'];
	$pr_id=$_POST['pr_id'];
	$o_typ=$_POST['o_typ'];
	$a_typ=$_POST['a_typ'];
	$rf_id=$_POST['rf_id'];
	$user=$_POST['user'];
	
	?>
	<!--<span style="float:right;background:#FFFFFF;padding:2px;">
		<i class="icon-circle" style="color:#C4F2C4;"></i> Processing<br/>
		<i class="icon-circle" style="color:#FEDCDC;"></i> Leaved
	</span>-->
	<!--------------------------------------------------------------->
	<select id="d_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Departments</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT `ot_dept_id` FROM `ot_schedule` WHERE `ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$dep=mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$pid[ot_dept_id]'"));
		?>
		<option value="<?php echo $pid['ot_dept_id']?>" <?php if($d_id==$pid['ot_dept_id']){echo "selected='selected'";}?>><?php echo $dep['ot_dept_name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="p_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Procedures</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT `procedure_id` FROM `ot_schedule` WHERE `ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$proc=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_clinical_procedure` WHERE `procedure_id`='$pid[procedure_id]'"));
		?>
		<option value="<?php echo $pid['procedure_id']?>" <?php if($p_id==$pid['procedure_id']){echo "selected='selected'";}?>><?php echo $proc['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<!--<select id="g_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Grades</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT `grade_id` FROM `ot_schedule` WHERE `ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$grade=mysqli_fetch_array(mysqli_query($link, "SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$pid[grade_id]'"));
		?>
		<option value="<?php echo $pid['grade_id']?>" <?php if($g_id==$pid['grade_id']){echo "selected='selected'";}?>><?php echo $grade['grade_name']?></option>
		<?php
	}
	?>
	</select>-->
	<!--------------------------------------------------------------->
	<?php
	
	?>
	<select id="s_id" onchange="ot_booking_list(this.id)">
		<option value="0">All Surgeon</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT a.`emp_id` FROM `ot_resource` a, `ot_schedule` b WHERE a.`schedule_id`=b.`schedule_id` AND a.`resourse_id`='1357' AND a.`emp_id`>0 AND b.`ot_date` BETWEEN '$date1' AND '$date2'");
	//$sel_qry=mysqli_query($link," SELECT * FROM `ot_resource_link` WHERE `type_id` = 1357 ");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pid[emp_id]'"));
		?>
		<option value="<?php echo $pid['emp_id']?>" <?php if($s_id==$pid['emp_id']){echo "selected='selected'";}?>><?php echo $emp['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="as_id" onchange="ot_booking_list(this.id)">
		<option value="0">All Asst Surgeon</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT a.`emp_id` FROM `ot_resource` a, `ot_schedule` b WHERE a.`schedule_id`=b.`schedule_id` AND a.`resourse_id`='1358' AND a.`emp_id`>0 AND b.`ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pid[emp_id]'"));
		?>
		<option value="<?php echo $pid['emp_id']?>" <?php if($as_id==$pid['emp_id']){echo "selected='selected'";}?>><?php echo $emp['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="a_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Anaesthesist</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT a.`emp_id` FROM `ot_resource` a, `ot_schedule` b WHERE a.`schedule_id`=b.`schedule_id` AND a.`resourse_id`='1359' AND a.`emp_id`>0 AND b.`ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pid[emp_id]'"));
		?>
		<option value="<?php echo $pid['emp_id']?>" <?php if($a_id==$pid['emp_id']){echo "selected='selected'";}?>><?php echo $emp['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="pd_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Paediatrician</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT a.`emp_id` FROM `ot_resource` a, `ot_schedule` b WHERE a.`schedule_id`=b.`schedule_id` AND a.`resourse_id`='1374' AND a.`emp_id`>0 AND b.`ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pid[emp_id]'"));
		?>
		<option value="<?php echo $pid['emp_id']?>" <?php if($pd_id==$pid['emp_id']){echo "selected='selected'";}?>><?php echo $emp['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="pr_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Performing Doctor</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT DISTINCT a.`emp_id` FROM `ot_resource` a, `ot_schedule` b WHERE a.`schedule_id`=b.`schedule_id` AND a.`resourse_id`='1389' AND a.`emp_id`>0 AND b.`ot_date` BETWEEN '$date1' AND '$date2'");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$pid[emp_id]'"));
		?>
		<option value="<?php echo $pid['emp_id']?>" <?php if($pr_id==$pid['emp_id']){echo "selected='selected'";}?>><?php echo $emp['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="o_typ" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All OT Types</option>
		<option value="2" <?php if($o_typ=="2"){echo "selected='selected'";}?>>Major</option>
		<option value="1" <?php if($o_typ=="1"){echo "selected='selected'";}?>>Minor</option>
	</select>
	<!--------------------------------------------------------------->
	<select id="a_typ" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Anaesthsia</option>
	<?php
	$sel_qry=mysqli_query($link,"SELECT * FROM `ot_anesthesia_types`");
	while($pid=mysqli_fetch_array($sel_qry))
	{
		?>
		<option value="<?php echo $pid['anesthesia_id']?>" <?php if($a_typ==$pid['anesthesia_id']){echo "selected='selected'";}?>><?php echo $pid['name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<select id="rf_id" onchange="ot_booking_list(this.id)" style="width:auto;">
		<option value="0">All Refer Doctor</option>
	<?php
	$ref_qry=mysqli_query($link,"SELECT DISTINCT `requesting_doc` FROM `ot_schedule` WHERE `ot_date` BETWEEN '$date1' AND '$date2'");
	while($rfid=mysqli_fetch_array($ref_qry))
	{
		$doc=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$rfid[requesting_doc]'"));
		?>
		<option value="<?php echo $rfid['requesting_doc']?>" <?php if($rf_id==$rfid['requesting_doc']){echo "selected='selected'";}?>><?php echo $doc['Name']?></option>
		<?php
	}
	?>
	</select>
	<!--------------------------------------------------------------->
	<span style="float:right;">
		<button type="button" class="btn btn-primary" onclick="export_page()"><i class="icon-file icon-large"></i> Export</button>
	</span>
	<table class="table table-condensed table-bordered" style="font-size:12px;">
		<tr>
			<th>#</th>
			<th>Procedure</th>
			<th>OT Date</th>
			<!--<th>Grade</th>-->
			<th>IPD ID</th>
			<th>Name</th>
			<th>Type</th>
			<th>Surgeon</th>
			<th>Asst Surgeon</th>
			<th>Anaesthesist</th>
			<th>Paediatrician</th>
			<th>Performing Doctor</th>
			<th>Anaesthsia</th>
			<th>Refer Doctor</th>
			<!--<th>Time</th>-->
		</tr>
		<?php
		$cl_d_id="";
		$cl_p_id="";
		$cl_g_id="";
		$cl_s_id="";
		$cl_as_id="";
		$cl_a_id="";
		$cl_pd_id="";
		$cl_o_typ="";
		$cl_a_typ="";
		//$qry="SELECT * FROM `ot_schedule` WHERE `ot_date`='$date1'";
		$qry="SELECT * FROM `ot_schedule` WHERE `ot_date` BETWEEN '$date1' AND '$date2'";
		if($d_id)
		{
			$qry.=" AND `ot_dept_id`='$d_id'";
			$cl_d_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($p_id)
		{
			$qry.=" AND `procedure_id`='$p_id'";
			$cl_p_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($g_id)
		{
			$qry.=" AND `grade_id`='$g_id'";
			$cl_g_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($s_id)
		{
			$qry.=" AND `schedule_id` IN (SELECT DISTINCT `schedule_id` FROM `ot_resource` WHERE `resourse_id`='1357' AND `emp_id`='$s_id')";
			$cl_s_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($as_id)
		{
			$qry.=" AND `schedule_id` IN (SELECT DISTINCT `schedule_id` FROM `ot_resource` WHERE `resourse_id`='1358' AND `emp_id`='$as_id')";
			$cl_as_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($a_id)
		{
			$qry.=" AND `schedule_id` IN (SELECT DISTINCT `schedule_id` FROM `ot_resource` WHERE `resourse_id`='1359' AND `emp_id`='$a_id')";
			$cl_a_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($pd_id)
		{
			$qry.=" AND `schedule_id` IN (SELECT DISTINCT `schedule_id` FROM `ot_resource` WHERE `resourse_id`='1374' AND `emp_id`='$pd_id')";
			$cl_pd_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($pr_id)
		{
			$qry.=" AND `schedule_id` IN (SELECT DISTINCT `schedule_id` FROM `ot_resource` WHERE `resourse_id`='1389' AND `emp_id`='$pr_id')";
			$cl_pr_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($o_typ)
		{
			$qry.=" AND `ot_type`='$o_typ'";
			$cl_o_typ="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($a_typ)
		{
			$qry.=" AND `anesthesia_id`='$a_typ'";
			$cl_a_typ="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		if($rf_id)
		{
			$qry.=" AND `requesting_doc`='$rf_id'";
			$cl_rf_id="border-left:1px solid #FB1515;border-right:1px solid #FB1515;";
		}
		$qry.=" ORDER BY `ot_date`";
		//echo $qry;
		$j=1;
		$q=mysqli_query($link,$qry);
		while($r=mysqli_fetch_array($q))
		{
			$p=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			
			$ot_name=mysqli_fetch_array(mysqli_query($link, " SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$r[ot_no]'"));
			$proc=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_clinical_procedure` WHERE `procedure_id`='$r[procedure_id]'"));
			
			$grade=mysqli_fetch_array(mysqli_query($link, "SELECT `grade_name` FROM `ot_grade_master` WHERE `grade_id`='$r[grade_id]'"));
			$ans=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_anesthesia_types` WHERE `anesthesia_id`='$r[anesthesia_id]'"));
			//--------------------------------------------------------//
			$all_srg="";
			$srg_qry=mysqli_query($link, "SELECT `emp_id` FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]' AND `resourse_id`='1357'");
			while($surg=mysqli_fetch_array($srg_qry))
			{
				$surg_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$surg[emp_id]'"));
				if($all_srg)
				{
					$all_srg.="<br/>".$surg_name['name'];
				}
				else
				{
					$all_srg=$surg_name['name'];
				}
			}
			//--------------------------------------------------------//
			$all_a_srg="";
			$asrg_qry=mysqli_query($link, "SELECT `emp_id` FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]' AND `resourse_id`='1358'");
			while($asurg=mysqli_fetch_array($asrg_qry))
			{
				$asurg_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$asurg[emp_id]'"));
				if($all_a_srg)
				{
					$all_a_srg.="<br/>".$asurg_name['name'];
				}
				else
				{
					$all_a_srg=$asurg_name['name'];
				}
			}
			//--------------------------------------------------------//
			$all_ans="";
			$ans_qry=mysqli_query($link, "SELECT `emp_id` FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]' AND `resourse_id`='1359'");
			while($anas=mysqli_fetch_array($ans_qry))
			{
				$anas_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$anas[emp_id]'"));
				if($all_ans)
				{
					$all_ans.="<br/>".$anas_name['name'];
				}
				else
				{
					$all_ans=$anas_name['name'];
				}
			}
			//--------------------------------------------------------//
			$all_ped="";
			$pd_qry=mysqli_query($link, "SELECT `emp_id` FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]' AND `resourse_id`='1374'");
			while($ped=mysqli_fetch_array($pd_qry))
			{
				$ped_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$ped[emp_id]'"));
				if($all_ped)
				{
					$all_ped.="<br/>".$ped_name['name'];
				}
				else
				{
					$all_ped=$ped_name['name'];
				}
			}
			//--------------------------------------------------------//
			$all_pr="";
			$pr_qry=mysqli_query($link, "SELECT `emp_id` FROM `ot_resource` WHERE `schedule_id`='$r[schedule_id]' AND `resourse_id`='1389'");
			while($prd=mysqli_fetch_array($pr_qry))
			{
				$pr_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$prd[emp_id]'"));
				if($all_pr)
				{
					$all_pr.="<br/>".$pr_name['name'];
				}
				else
				{
					$all_pr=$pr_name['name'];
				}
			}
			//--------------------------------------------------------//
			if($r['ot_type']==1)
			{
				$ot_type="Minor";
			}
			if($r['ot_type']==2)
			{
				$ot_type="Major";
			}
			
			if($r['leaved']>0)
			{
				$leav_styl="background:#FBEBEB;"; // leaved ot room
			}
			else
			{
				$leav_styl="background:#EAF7EA;";
			}
			$leav_styl.="color:#444444;";
			
			$rf_doc=mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$r[requesting_doc]'"));
		?>
		<tr style="<?php echo $leav_styl;?>">
			<td style="<?php echo $cl_d_id;?>"><?php echo $j;?></td>
			<td style="<?php echo $cl_p_id;?>"><?php echo $proc['name'];?></td>
			<td><?php echo convert_date($r['ot_date']);?></td>
			<!--<td style="<?php echo $cl_g_id;?>"><?php echo $grade['grade_name'];?></td>-->
			<td><?php echo $r['ipd_id'];?></td>
			<td><?php echo $p['name'];?></td>
			<td style="<?php echo $cl_o_typ;?>"><?php echo $ot_type;?></td>
			<td style="<?php echo $cl_s_id;?>"><?php echo $all_srg;?></td>
			<td style="<?php echo $cl_as_id;?>"><?php echo $all_a_srg;?></td>
			<td style="<?php echo $cl_a_id;?>"><?php echo $all_ans;?></td>
			<td style="<?php echo $cl_pd_id;?>"><?php echo $all_ped;?></td>
			<td style="<?php echo $cl_pr_id;?>"><?php echo $all_pr;?></td>
			<td style="<?php echo $cl_a_typ;?>"><?php echo $ans['name'];?></td>
			<td style="<?php echo $cl_rf_id;?>"><?php echo $rf_doc['Name'];?></td>
			<!--<td><?php if($r['start_time']!="00:00:00"){echo convert_time($r['start_time']);}else{echo "-";}?></td>-->
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
	<?php
}

if($_POST["type"]=="ot_schedule_reason")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
}

?>
