<?php
session_start();
$c_user=trim($_SESSION['emp_id']);

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

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$head_id=$_GET['head_id'];
$encounter=$_GET['encounter'];
$ward=$_GET['ward'];
$branch_id=$_GET['branch_id'];

$ward_str="";
if($ward)
{
	$ward_str=" AND b.`wardName`='$ward'";
}

$head=mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));
	
if($encounter==3)
{
	$str=" SELECT DISTINCT a.`opd_id`,a.`ipd_id` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`ipd_id`=b.`opd_id` and `testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' ) AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`ipd_id` IN( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter' ) and b.`branch_id`='$branch_id' $ward_str ORDER BY b.`date`";
}
else
{
	$str=" SELECT DISTINCT a.`opd_id`,a.`ipd_id` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' ) AND a.`date` BETWEEN '$date1' AND '$date2' AND a.`ipd_id`='' and b.`branch_id`='$branch_id' $ward_str ORDER BY b.`date`"; // AND `opd_id` IN( SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter' )
}

$filename ="patientwise_report_of_".$dept."_from_".$date1."_to_".$date2.".xls";
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<html>
<head>
<title>Reports</title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	font-size:13px; font-family:Arial, Helvetica, sans-serif; line-height: 18px;
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
*{font-size:13px;}
</style>
</head>
<body>
	<div class="container">
		<table class="table table-hover">
			<tr>
				<th colspan="9">
					<b>Patient Wise Test Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
					<br>
					<b>Department Name: <?php echo $head["type_name"]; ?></b>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Hospital No</th>
				<th>Patient Name</th>
				<th>Ward</th>
				<th>Test Name</th>
				<th>Collection Time</th>
				<th>Reporting Time</th>
				<th>Status</th>
			</tr>
		<?php
			$n=1;
			$grand_tot=0;
			//$qry=mysqli_query($link, " SELECT DISTINCT `opd_id`,`ipd_id` FROM `patient_test_details` WHERE `testid` in ( SELECT `testid` FROM `testmaster` WHERE `type_id`='$head_id' )  AND `date` BETWEEN '$date1' AND '$date2' AND `opd_id` in ( SELECT `opd_id` FROM `uhid_and_opdid` $encounter_str ) ");
			$qry=mysqli_query($link, $str);
			while($dis_ipd=mysqli_fetch_array($qry))
			{
				if($dis_ipd["opd_id"])
				{
					$pin=$dis_ipd["opd_id"];
				}
				if($dis_ipd["ipd_id"])
				{
					$pin=$dis_ipd["ipd_id"];
				}
				
				$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$pin' "));
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$ref_doc=mysqli_fetch_array(mysqli_query($link, " SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));
				
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$Encounter=$pat_typ_text['p_type'];
				
				$all_test="";
				$tot_test=0;
				$z=1;
				$test_qry=mysqli_query($link, " SELECT a.`test_rate`,b.`testid`, b.`testname` FROM `patient_test_details` a, `testmaster` b WHERE a.`patient_id`='$pat_reg[patient_id]' AND a.`opd_id`='$dis_ipd[opd_id]' AND a.`ipd_id`='$dis_ipd[ipd_id]' AND b.`type_id`='$head_id' AND a.`testid`=b.`testid` ");
				while($test=mysqli_fetch_array($test_qry))
				{
					$collectionTime="";
					$collTime=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date` FROM `phlebo_sample` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$dis_ipd[opd_id]' AND `testid`='$test[testid]'"));
					if($collTime)
					{
						$collectionTime=date("d-m-y", strtotime($collTime["date"]))." ".date("h:i A", strtotime($collTime["time"]));
					}
					$reportingTime="";
					$reportStatus="pending";
					$repTime=mysqli_fetch_array(mysqli_query($link,"SELECT `time`,`date` FROM `testresults` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$dis_ipd[opd_id]' AND `testid`='$test[testid]' AND `result`!='' ORDER BY `slno` DESC LIMIT 1"));
					if($repTime)
					{
						$reportingTime=date("d-m-y", strtotime($repTime["date"]))." ".date("h:i A", strtotime($repTime["time"]));
						$reportStatus="Reported";
					}
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo date("d-m-Y", strtotime($pat_reg["date"])); ?></td>
					<td><?php echo $pat_info['patient_id']; ?></td>
					<td><?php echo $pat_info['name']; ?></td>
					<td><?php echo $pat_reg["wardName"]; ?></td>
					<td><?php echo $test["testname"]; ?></td>
					<td><?php echo $collectionTime;?></td>
					<td><?php echo $reportingTime;?></td>
					<td><?php echo $reportStatus;?></td>
				</tr>
			<?php
				$n++;
				}
			}
		?>
		</table>
	</div>
</body>
</html>
