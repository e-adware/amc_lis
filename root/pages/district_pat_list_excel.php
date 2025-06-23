<?php
session_start();
include'../../includes/connection.php';
$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];


$time=date('H:i:s');
$date=date("Y-m-d");

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$district=$_GET['district'];

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
// Date format convert
function convert_date($date)
{
	if($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

	$filename ="district_pat_list_xlx_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	
	if($district==0)
	{
		$q="SELECT DISTINCT `patient_id` FROM `patient_info_rel` WHERE `patient_id` IN (SELECT `patient_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2')";
	}
	else
	{
		$q="SELECT DISTINCT `patient_id` FROM `patient_info_rel` WHERE `district`='$district' AND `patient_id` IN (SELECT `patient_id` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$date1' AND '$date2')";
	}
	
	//echo $q;
	
	$qry=mysqli_query($link,$q);
?>
<html>
<head>
<title>District Wise Patient List</title>
</head>
<body>
	<table class="table table-condensed table-bordered">
		<tr>
			<th>#</th>
			<th>UHID</th>
			<th>Bill No</th>
			<th>Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Contact</th>
			<th>City / Village</th>
			<th>District</th>
			<th>State</th>
			<th>Date</th>
			<th>Time</th>
		</tr>
	<?php
		$n=1;
		while($r=mysqli_fetch_array($qry))
		{
			$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `patient_id`,`name`,`sex`,`dob`,`age`,`age_type`,`phone` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			
			$info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info_rel` WHERE `patient_id`='$r[patient_id]'"));
			
			$dist=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `district` WHERE `district_id`='$info[district]'"));
			
			$state=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `state` WHERE `state_id`='$info[state]'"));
			
			$vl=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$r[patient_id]' ORDER BY `slno` DESC"));
			
			//~ if($pat["dob"]!=""){ $age=age_calculator($pat["dob"])." (".$pat["dob"].")"; }else{ $age=$pat["age"]." ".$pat["age_type"]; }
			if($pat["dob"]!=""){ $age=age_calculator($pat["dob"]); }else{ $age=$pat["age"]." ".$pat["age_type"]; }
			
			$sex=$pat['sex'];
			
			//~ $addr="";
			//~ if($addr)
			//~ {
				//~ $addr.=", ".$info['city'];
			//~ }
			//~ else
			//~ {
				//~ $addr.=$info['city'];
			//~ }
			//~ if($addr)
			//~ {
				//~ $addr.=", ".$info['police'];
			//~ }
			//~ else
			//~ {
				//~ $addr.=$info['police'];
			//~ }
			//~ if($addr)
			//~ {
				//~ $addr.=", ".$dist['name'];
			//~ }
			//~ else
			//~ {
				//~ $addr.=$dist['name'];
			//~ }
			//~ if($addr)
			//~ {
				//~ $addr.=", ".$state['name'];
			//~ }
			//~ else
			//~ {
				//~ $addr.=$state['name'];
			//~ }
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $pat['patient_id'];?></td>
			<td><?php echo $vl['opd_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $age;?></td>
			<td><?php echo $sex;?></td>
			<td><?php echo $pat['phone'];?></td>
			<td><?php echo $info['city'];?></td>
			<td><?php echo $dist['name'];?></td>
			<td><?php echo $state['name'];?></td>
			<!--<td><?php echo $u['name'];?></td>-->
			<td><?php echo convert_date($vl['date']);?></td>
			<td><?php echo convert_time($vl['time']);?></td>
		</tr>
	<?php
			$n++;
		}
	?>
	</table>
</body>
</html>
