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
if($type=="view_district_pat_list")
{
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$district=$_POST['district'];
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

	<span class="text-right" id="excel_btn_hide"><a class="btn btn-info" href="pages/district_pat_list_excel.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>&district=<?php echo $district;?>"><i class="icon-file icon-large"></i> Excel</a></span>
	
	<button type="button" id="print_div" class="btn btn-info" onclick="print_page('<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $district;?>')" style="float:right;"><i class="icon-print icon-large"></i> Print</button>
	<p style="margin-top: 2%;"><b>District Wise Patient List from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
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
	<?php
}

if($type=="")
{
	
}
?>
