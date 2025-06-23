<?php
include('../../includes/connection.php');
include("../../includes/global.function.php");
include("../app/init.php");

$date=date('Y-m-d');
$time=date('H:i:s');


$type=$_POST['type'];
//print_r($_POST);

if($type==1)
{
	$fdt	=$_POST['fdate'];
	$tdt	=$_POST['tdate'];
	$fdate	=$_POST['fdate']."-01";
	$tdate	=$_POST['tdate']."-01";
	$tdate	=date("Y-m-t", strtotime($tdate));
	?>
	<span style="float:right;">
		<button type="button" class="btn btn-primary btn_act" onclick="report_print('<?php echo base64_encode($fdt);?>','<?php echo base64_encode($tdt);?>')">Print</button>
	</span>
	<table class="table table-condensed table-report">
		<tr>
			<th>Month</th>
			<th>Total Patient</th>
			<th>No of day(s)</th>
			<th>Per day (average)</th>
		</tr>
	<?php
	if (strtotime($fdate) > strtotime($tdate))
	{
		echo "<tr><td colspan='4'>Invalid date range: from-date is after to-date.</td></tr>";
	}
	else
	{
	
	$start = new DateTime($fdate);
	$end = new DateTime($tdate);
	// Extend to end of the last month
	$end->modify('last day of this month');

	$interval = new DateInterval('P1M');
	$period = new DatePeriod($start, $interval, $end);
	
	$months = [];

	foreach ($period as $dt) {
	$monthStart = $dt->format("Y-m-01");
	$monthEnd = $dt->format("Y-m-t");
	//$months[] = $dt->format("F Y"); // e.g., "March 2025"

	// Clamp to range boundaries
	if ($monthStart < $fdate) $monthStart = $fdate;
	if ($monthEnd > $tdate) $monthEnd = $tdate;
	
	$monthName = $dt->format("F"); // Full month name like "January"
	$daysInMonth = date("t", strtotime($dt->format("Y-m-d")));
	//echo "From: $monthStart To: $monthEnd - Total Days: $daysInMonth<br/>";
	
	$qry	="SELECT COUNT(DISTINCT `opd_id`) AS `counts` FROM `uhid_and_opdid` WHERE `date` BETWEEN '$monthStart' AND '$monthEnd'";
	$patCount=mysqli_fetch_array(mysqli_query($link, $qry));
	$totPatCount=$patCount['counts'];
	$perDayCount=($totPatCount/$daysInMonth);
	echo "<tr><td>$monthName</td><td>$totPatCount</td><td>$daysInMonth</td><td>".round($perDayCount)."</td></tr>";
	
	$months[]=round($perDayCount);
	}
	
	// Get last 3 months
	$last3 = array_slice($months, -3);
	$last3length=sizeof($last3);
	$avgMon3=0;
	foreach ($last3 as $month) {
		$avgMon3+=$month;
	}
	if($last3length>=3)
	{
	echo "<tr><td></td><td colspan='2' style='text-align:center;'>Average in last 3 months : ".round(($avgMon3/3))."</td><td></td></tr>";
	}
	
	}
	?>
	</table>
	<?php
}


?>