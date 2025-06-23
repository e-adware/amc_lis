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
$val=$_GET['val'];
$branch_id=$_GET['branch_id'];

$dept_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `test_category_master` WHERE `category_id`='$val' "));
if($dept_info)
{
	$dept=$dept_info["name"];
}

$str=" SELECT distinct a.`patient_id`,a.`opd_id`,a.`ipd_id`,a.`date` FROM `patient_test_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and a.`date` between '$date1' and '$date2' and a.`testid`in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='$val' ) and b.`branch_id`='$branch_id' order by a.`slno` ASC ";

$pat_reg_qry=mysqli_query($link, $str);

$filename ="catwise_test_report_of_".$dept."_from_".$date1."_to_".$date2.".xls";
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
			<th colspan="6">Category Wise Test Report of <?php echo $dept; ?> from <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></th>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>UHID</th>
				<th>Bill No</th>
				<th>Patient Name</th>
				<th>Amount</th>
			</tr>
		<?php
			$n=1;
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));
				
				$pat_test_qry=mysqli_query($link, " SELECT `testname` FROM `testmaster` WHERE `category_id`='$val' and `testid` in ( SELECT `testid` FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `ipd_id`='$pat_reg[ipd_id]' ) ");
				
				$all_test="";
				while($pat_test=mysqli_fetch_array($pat_test_qry))
				{
					$all_test.=$pat_test["testname"]." , ";
				}
				if( $pat_reg["opd_id"])
				{
					$pin= $pat_reg["opd_id"];
				}
				if( $pat_reg["ipd_id"])
				{
					$pin= $pat_reg["ipd_id"];
				}
				$pat_test_amount=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`test_rate`) as tot_amt FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' and `opd_id`='$pat_reg[opd_id]' and `ipd_id`='$pat_reg[ipd_id]'  and `testid`in ( SELECT `testid` FROM `testmaster` WHERE `category_id`='$val' )"));
				$test_tot_amt=$pat_test_amount['tot_amt'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_reg["date"]; ?></td>
				<td><?php echo $pat_reg["patient_id"]; ?></td>
				<td><?php echo $pin; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $test_tot_amt; ?>.00</td>
			</tr>
			<tr>
				<th colspan="3"><span class="text-right">Tests: </span></th>
				<td colspan="3"><i><?php echo $all_test; ?></i></td>
			</tr>
	<?php
		$n++;
		}
	?>
		</table>
	</div>
</body>
</html>
