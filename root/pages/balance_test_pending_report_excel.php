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
$sel_test=$_GET['sel_test'];

if($sel_test=="all")
{
	$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' ");
}else
{
	$pat_test_qry=mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `date` between '$date1' AND '$date2' AND `testid`='$sel_test' ");
}

$filename ="balance_test_pending_report_from_".$date1."_to_".$date2.".xls";
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
				<th colspan="4">
					Pending Test from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Test Name</th>
				<!--<th>Sample</th>-->
				<!--<th>Date Time</th>-->
			</tr>
		<?php
			$n=1;
			while($pat_test=mysqli_fetch_array($pat_test_qry))
			{
				$pat_testresult_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testid` FROM `testresults` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
				$pat_testresult_rad_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testid` FROM `testresults_rad` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
				$pat_testresult_card_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testid` FROM `testresults_card` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
				$test_summary_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testid` FROM `patient_test_summary` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
				$pat_widalresult_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testid` FROM `widalresult` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
				$tot_num=$pat_testresult_num+$pat_testresult_rad_num+$pat_testresult_card_num+$test_summary_num+$pat_widalresult_num;
				if($tot_num==0)
				{
					if($pat_test["opd_id"]!='')
					{
						$v_id=$pat_test["opd_id"];
					}
					if($pat_test["ipd_id"]!='')
					{
						$v_id=$pat_test["ipd_id"];
					}
					$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_test[patient_id]' "));
					$testname=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `testname` FROM `testmaster` WHERE `testid`='$pat_test[testid]' "));
					if($pat_test["sample_id"]>0)
					{
						$phlebo_num=mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `patient_id` FROM `phlebo_sample` WHERE `patient_id`='$pat_test[patient_id]' AND `opd_id`='$pat_test[opd_id]' AND `ipd_id`='$pat_test[ipd_id]' AND `batch_no`='$pat_test[batch_no]' AND `testid`='$pat_test[testid]' "));
						if($phlebo_num==0)
						{
							$sample="Not Received";
						}else
						{
							$sample="Received";
						}
					}else
					{
						$sample="---";
					}
				?>
					<tr>
						<td><?php echo $n; ?></td>
						<td><?php echo $v_id; ?></td>
						<td><?php echo $pat_info["name"]; ?></td>
						<td><?php echo $testname["testname"]; ?></td>
						<!--<td><?php echo $sample; ?></td>-->
						<!--<td><?php echo convert_date($pat_test["date"])." ".$pat_test["time"]; ?></td>-->
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
