<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date("Y-m-d");
$time=date("H:i:s");

//print_r($_POST);
$type=$_POST['type'];

if($type==1)
{
	$uhid=$_POST['uhid'];
	$opd_id=$_POST['opd_id'];
	$ipd_id=$_POST['ipd_id'];
	$batch_no=$_POST['batch_no'];
	$dept_id=$_POST['dept_id'];
	$result=$_POST['result'];
	$user=$_POST['user'];
	
	if(mysqli_query($link, "DELETE FROM `patient_flagged_details` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch_no' AND `dept_id`='$dept_id'"))
	{
		mysqli_query($link,"INSERT INTO `patient_flagged_records`(`patient_id`, `opd_id`, `ipd_id`, `batch_no`, `dept_id`, `cause`, `cause_user`, `remarks`, `remarks_user`, `time`, `date`, `flag`) VALUES ('$uhid','$opd_id','$ipd_id','$batch_no','$dept_id','','$user','$result','$user','$time','$date','0')");
	}
}

if($type==2)
{
	//print_r($_POST);
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$user=$_POST['user'];
	
	$qry="SELECT DISTINCT `patient_id`,`opd_id` FROM `patient_flagged_records` WHERE `date` BETWEEN '$date1' AND '$date2'";
	//echo $qry;
	?>
	<table class="table table-condensed table-report">
		<tr>
			<th>#</th>
			<th>Patient No</th>
			<th>Patient Name</th>
			<th>Flagged</th>
			<th>Cause</th>
			<th>Remarks</th>
			<th>Flagged By</th>
			<th>Un-Flagg</th>
			<th>Remarks</th>
			<th>Un-Flag By</th>
			<th>Status</th>
		</tr>
	<?php
	$j=1;
	$q	=mysqli_query($link, $qry);
	while($r=mysqli_fetch_array($q))
	{
		$pat=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `patient_info` WHERE `patient_id`='$r[patient_id]'"));
		$flag=mysqli_fetch_array(mysqli_query($link,"SELECT `cause`,`cause_user`,`remarks`,`time`,`date` FROM `patient_flagged_records` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `flag`='1' LIMIT 1"));
		$flagEmp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$flag[cause_user]'"));
		$unflag=mysqli_fetch_array(mysqli_query($link,"SELECT `remarks`,`remarks_user`,`time`,`date` FROM `patient_flagged_records` WHERE `patient_id`='$r[patient_id]' AND `opd_id`='$r[opd_id]' AND `flag`='0' LIMIT 1"));
		if($unflag)
		{
			$status="Un-Flag";
			$tdClass="green";
			$unflagEmp=mysqli_fetch_array(mysqli_query($link,"SELECT `name` FROM `employee` WHERE `emp_id`='$unflag[remarks_user]'"));
		}
		else
		{
			$status="Flagged";
			$tdClass="red";
		}
		?>
		<tr>
			<td><?php echo $j;?></td>
			<td><?php echo $r['patient_id'];?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php echo $flag['date']." ".$flag['time'];?></td>
			<td><?php echo $flag['cause'];?></td>
			<td><?php echo $flag['remarks'];?></td>
			<td><?php echo $flagEmp['name'];?></td>
			<td><?php echo $unflag['date']." ".$unflag['time'];?></td>
			<td><?php echo $unflag['remarks'];?></td>
			<td><?php echo $unflagEmp['name'];?></td>
			<td class="<?php echo $tdClass;?>"><?php echo $status;?></td>
		</tr>
		<?php
		$j++;
	}
	?>
	</table>
	<?php
}


mysqli_close($link);
?>