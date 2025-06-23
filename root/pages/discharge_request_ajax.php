<?php
include("../../includes/connection.php");

$time=date('H:i:s');
$date=date("Y-m-d");

$type=$_POST['type'];

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

if($type=="search_patient_disc_req")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$name=$_POST['name'];
	$dat=$_POST['dat'];
	
	$prefix_det=mysqli_fetch_array(mysqli_query($link, " SELECT `p_type`,`type`,`prefix` FROM `patient_type_master` WHERE `p_type_id`='3' "));
	
	$q="SELECT * FROM `discharge_request` WHERE `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details`) ";
	if($uhid)
	{
		$q.=" AND `patient_id` like '$uhid%'";
	}
	if($ipd)
	{
		$q.=" AND `ipd_id` like '$ipd%'";
	}
	if($name)
	{
		$q=" AND `patient_id` IN (SELECT `patient_id` FROM `patient_info` WHERE `name` like '$name%')";
	}
	if($dat)
	{
		$q=" AND `date`='$dat'";
	}
	$q.=" ORDER BY `slno` DESC";
	//echo $q;
	$qry=mysqli_query($link, $q);
	?>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>UHID</th><th><?php echo $prefix_det["prefix"]; ?></th><th>Name</th><th>Sex</th><th>Age (DOB)</th><th>Ward</th><th>Bed No</th><th></th>
		</tr>
	<?php
	while($r=mysqli_fetch_array($qry))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `uhid`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		if($pat["dob"]!=""){ $age=age_calculator($pat["dob"])." (".$pat["dob"].")"; }else{ $age=$pat["age"]." ".$pat["age_type"]; }
		
		$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_bed_details where patient_id='$r[patient_id]' and ipd_id='$r[ipd_id]'"));
		if($bed_det['bed_id'])
		{
			$ward=mysqli_fetch_array(mysqli_query($link,"select * from ward_master where ward_id='$bed_det[ward_id]'"));
			$bed_det=mysqli_fetch_array(mysqli_query($link,"select * from bed_master where bed_id='$bed_det[bed_id]'"));
			
			$ward=$ward["name"];
			$bed=$bed_det["bed_no"];
		}else
		{
			$ward="Discharged";
			$bed="Discharged";
		}
		
		$cancel_request=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[ipd_id]' AND `type`='2' "));
		if($cancel_request)
		{
			$td_function_p="";
			$td_function_c="";
			
			$td_style="style='background-color: #ff000021'";
			
			$emp_info_del=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));
			
			$tr_title="title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
		}
		else
		{
			$td_function_p="onclick=\"redirect_ipd_dash('$r[patient_id]','$r[ipd_id]')\"";
			$td_function_c="onclick=\"cancel_disc('$r[patient_id]','$r[ipd_id]')\"";
			$td_style="style='cursor:pointer;'";
			$tr_title="";
		}
		
	?>
		<tr <?php echo $td_style." ".$tr_title; ?> >
			<td><?php echo $r['patient_id'];?></td>
			<td><?php echo $r['ipd_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $ward;?></td>
			<td><?php echo $bed;?></td>
			<td>
				<button class="btn btn-process" <?php echo $td_function_p; ?>><i class="icon-forward"></i> Process</button>
				<button class="btn btn-delete" <?php echo $td_function_c; ?>><i class="icon-remove"></i> Cancel Request</button>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	<?php
}

if($type=="cancel_patient_disc_req")
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	if(mysqli_query($link,"DELETE FROM `discharge_request` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"))
	{
		echo "Cancelled";
	}
	else
	{
		echo "Error";
	}
}

if($type=="")
{
	
}
?>
