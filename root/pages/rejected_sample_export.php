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

$filename ="rejected_sample_report_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Rejected Sample Report</title>
</head>
<body>
	<table class="table table-condensed table-bordered">
		<tr>
			<th colspan="7">Rejected Sample Report From <?php echo date("d-m-y", strtotime($date1));?> To <?php echo date("d-m-y", strtotime($date2));?></th>
		</tr>
		<tr>
			<th>#</th>
			<th>Hospital No</th>
			<th>Patient Name</th>
			<th>Test Name</th>
			<th>Sample Status</th>
			<th>Date Time</th>
			<th>User</th>
		</tr>
		<?php
		$j=1;
		//$qry="SELECT a.*,b.`date`,b.`time`,b.`testid`,b.`user` FROM `phlebo_sample_status` a, `phlebo_sample` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`vaccu`=b.`vaccu` AND a.`status_id`>'1' AND b.`date` BETWEEN '$date1' AND '$date2'";
		$qry="SELECT a.*,b.`date`,b.`time`,b.`testid`,b.`user` FROM `phlebo_sample_status` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`batch_no`=b.`batch_no` AND a.`status_id`>'1' AND b.`date` BETWEEN '$date1' AND '$date2'";
		$q=mysqli_query($link, $qry);
		while($r=mysqli_fetch_array($q))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
			$test=mysqli_fetch_array(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$r[testid]'"));
			$stat=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `phlebo_sample_status_master` WHERE `status_id`='$r[status_id]'"));
			$emp=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]'"));
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['patient_id'];?></td>
			<td><?php echo $pat_info['name'];?></td>
			<td><?php echo $test['testname'];?></td>
			<td><?php echo $stat['name'];?></td>
			<td><?php echo date("d-m-y", strtotime($r['date']))." ".date("h:i A", strtotime($r['time']));?></td>
			<td><?php echo $emp['name'];?></td>
		</tr>
		<?php
		$j++;
		}
		?>
	  </table>
</body>
</html>
