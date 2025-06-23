<?php
include'../../includes/connection.php';

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

$date1=base64_decode($_GET['date1']);
$date2=base64_decode($_GET['date2']);

$filename ="tat_non_conformity_report_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>TAT Non Conformity Reports</title>
</head>
<body>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="8">TAT Non Conformity Reports From <?php echo date("d-m-y", strtotime($date1));?> To <?php echo date("d-m-y", strtotime($date2));?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Hospital No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
			<th>Reason for delay</th>
			<th>Corrective action</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
		<?php
		$qry="SELECT * FROM `tat_nc_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `delay`!='' AND `nc_action`!=''";
		$j=1;
		$q=mysqli_query($link, $qry);
		while($r=mysqli_fetch_array($q))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name`, `hosp_no` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$test=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
			$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $pat_info['hosp_no'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $test['testname'];?></td>
			<td class="text-justify"><?php echo $r['delay'];?></td>
			<td class="text-justify"><?php echo $r['nc_action'];?></td>
			<td><?php echo date("d-m-y", strtotime($r['date']))." ".date("h:i A", strtotime($r['time']));?></td>
			<td><?php echo $emp['name'];?></td>
		</tr>
		<?php
		$j++;
		}?>
	</table>
</body>
</html>
