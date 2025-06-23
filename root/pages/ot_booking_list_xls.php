
<html>
<head>
<title>OT Register</title>

</head>
<body>
<div class="container">
	<?php
	include'../../includes/connection.php';
echo $brand;
	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}

	function convert_time($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$d_id=$_GET['d_id'];
	$p_id=$_GET['p_id'];
	$g_id=$_GET['g_id'];
	$s_id=$_GET['s_id'];
	$as_id=$_GET['as_id'];
	$a_id=$_GET['a_id'];
	$pd_id=$_GET['pd_id'];
	$pr_id=$_GET['pr_id'];
	$o_typ=$_GET['o_typ'];
	$a_typ=$_GET['a_typ'];
	$rf_id=$_GET['rf_id'];
	//$user=$_GET['user'];
	
	$filename ="ot_register_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	?>
	
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
		</tr>
		<?php
		$j++;
		}
		?>
	</table>
</div>
</body>
</html>
