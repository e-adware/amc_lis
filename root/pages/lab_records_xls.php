<?php
include('../../includes/connection.php');

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}

$date1=$_GET['date1'];
$date2=$_GET['date2'];

$filename ="lab_records_xls_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>	<p style="margin-top: 2%;" id="print_div">
	
	<b>Laboratory Record from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?>
	
</p>
<table class="table table-condensed table-bordered">
	<tr>
		<th colspan="2">Total Patient</th>
<?php
		$dept_qry=mysqli_query($link, " SELECT DISTINCT `type_id` FROM `testmaster` WHERE `testid` IN(SELECT DISTINCT `testid` FROM `patient_test_details`) AND `category_id`=1 ");
		$dept_num=mysqli_num_rows($dept_qry);
		while($dept=mysqli_fetch_array($dept_qry))
		{
			$dept_name=mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `test_department` WHERE `id`='$dept[type_id]'"));
			
			echo "<th colspan='2'>$dept_name[name]</th>";
		}
?>
		<th colspan="2">Total Test</th>
	</tr>
	<tr>
<?php
	$xx=0;
	while($xx<=$dept_num)
	{
		echo "<th>OPD</th><th>IPD</th>";
		$xx++;
	}
?>
		<th>OPD</th>
		<th>IPD</th>
	</tr>
<?php

// OPD Patient
$opd_pat_str="SELECT COUNT(DISTINCT `opd_id`) AS `tot_opd_pat` FROM `patient_test_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `ipd_id`=''  AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1')";

$opd_pat_val=mysqli_fetch_array(mysqli_query($link, $opd_pat_str));
$opd_pat_num=$opd_pat_val["tot_opd_pat"];

// IPD Patient
$ipd_pat_str="SELECT COUNT(DISTINCT `ipd_id`) AS `tot_ipd_pat` FROM `patient_test_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id`=''  AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1')";

$ipd_pat_val=mysqli_fetch_array(mysqli_query($link, $ipd_pat_str));
$ipd_pat_num=$ipd_pat_val["tot_ipd_pat"];

?>
	<tr>
		<td><?php echo $opd_pat_num; ?></td>
		<td><?php echo $ipd_pat_num; ?></td>
<?php
	$dept_qry=mysqli_query($link, " SELECT DISTINCT `type_id` FROM `testmaster` WHERE `testid` IN(SELECT DISTINCT `testid` FROM `patient_test_details`) AND `category_id`=1 ");
	while($dept=mysqli_fetch_array($dept_qry))
	{
		// OPD
		$test_str_opd="SELECT COUNT(`testid`) AS `tot_test` FROM `patient_test_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept[type_id]') AND `ipd_id`=''";

		$opd_test=mysqli_fetch_array(mysqli_query($link, $test_str_opd));
		$opd_test_num=$opd_test["tot_test"];
		
		// IPD
		$test_str_ipd="SELECT COUNT(`testid`) AS `tot_test` FROM `patient_test_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='$dept[type_id]') AND `opd_id`=''";

		$ipd_test=mysqli_fetch_array(mysqli_query($link, $test_str_ipd));
		$ipd_test_num=$ipd_test["tot_test"];
		
		echo "<td class='opd_test'>$opd_test_num</td><td class='ipd_test'>$ipd_test_num</td>";
		
		$total_opd_test_num+=$opd_test_num;
		$total_ipd_test_num+=$ipd_test_num;
		
	}
?>
		<td class="opd_test_total" onmouseover="mouse_over('opd')" onmouseout="mouse_out('opd')"><?php echo $total_opd_test_num; ?></td>
		<td  class="ipd_test_total" onmouseover="mouse_over('ipd')" onmouseout="mouse_out('ipd')"><?php echo $total_ipd_test_num; ?></td>
	</tr>
</table>
