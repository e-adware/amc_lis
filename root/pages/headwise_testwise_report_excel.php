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
$testid=$_GET['testid'];
$branch_id=$_GET['branch_id'];

$test_str="";
if($testid)
{
	$test_str=" AND a.`testid`='$testid'";
}

$head=mysqli_fetch_array(mysqli_query($link, " select type_name from testmaster where type_id='$head_id' "));
	
//$encounter_str=" AND c.`type`='$encounter'";

if($encounter==3)
{
	$encounter_str=" AND a.opd_id='' AND a.ipd_id!=''";
}
else
{
	$encounter_str=" AND a.opd_id!='' AND a.ipd_id=''";
}

$filename ="headwise_testwise_report_of_".$dept."_from_".$date1."_to_".$date2.".xls";
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
				<th colspan="3">
					Test Wise Details from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
					<br>
					<b>Department Name: <?php echo $head["type_name"]; ?></b>
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>Test Name</th>
				<th>No. of Test</th>
			</tr>
		<?php
			
			$n=1;
			$total_test_num=0;
			$grand_total=0;
			
			//~ $qry=mysqli_query($link, " SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`type_id`='$head_id' $encounter_str ORDER BY b.`testname` ");
		
			$qry=mysqli_query($link, " SELECT DISTINCT a.`testid` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`date` BETWEEN '$date1' AND '$date2' AND b.`type_id`='$head_id' $encounter_str and c.`branch_id`='$branch_id' $test_str ORDER BY b.`testname` ");
		
			while($dist_testid=mysqli_fetch_array($qry))
			{
				$test_info=mysqli_fetch_array(mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `testid`='$dist_testid[testid]' "));
				
				$test_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `patient_test_details`  a, `uhid_and_opdid` c WHERE a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`testid`='$dist_testid[testid]' AND a.`date` BETWEEN '$date1' AND '$date2' $encounter_str and c.`branch_id`='$branch_id' "));
				
				$test_amount=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`test_rate`) AS `test_sum` FROM `patient_test_details` a, `uhid_and_opdid` c WHERE a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND a.`testid`='$dist_testid[testid]' AND a.`date` BETWEEN '$date1' AND '$date2' $encounter_str and c.`branch_id`='$branch_id' "));
			?>
				<tr>
					<td><?php echo $n;?></td>
					<td><?php echo $test_info['testname'];?></td>
					<td><?php echo $test_num;?></td>
				</tr>
			<?php
				$total_test_num+=$test_num;
				$grand_total+=$test_amount["test_sum"];
				$n++;
			}
		?>
		</table>
	</div>
</body>
</html>
