<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];
$branch_id=$_POST['branch_id'];
$doc_id=$_POST['doc_id'];
$type_id=$_POST['type_id'];

$type=$_POST['type'];

if($type=="doctor_wise")
{
	$dept_str="SELECT `id`,`name` FROM `test_department` WHERE `id` IN(SELECT DISTINCT c.`type_id` FROM `uhid_and_opdid` a, `testresults_rad` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`branch_id`='1' AND b.`doc`='$doc_id' AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`testid`=c.`testid`)";
	
	if($type_id)
	{
		$dept_str.=" AND `id`='$type_id'";
	}
	
	$dept_qry=mysqli_query($link, $dept_str);
	
	$test_num_str="SELECT b.`testid` FROM `uhid_and_opdid` a, `patient_test_details` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`branch_id`='1' AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`testid`=c.`testid` AND c.`category_id`='2'";
	if($type_id)
	{
		$test_num_str.=" AND c.`type_id`='$type_id'";
	}
	$test_num=mysqli_num_rows(mysqli_query($link, $test_num_str));
	
	$test_report_num_str="SELECT b.`testid` FROM `uhid_and_opdid` a, `patient_test_details` b, `testmaster` c, `testresults_rad` d WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`branch_id`='1' AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`testid`=c.`testid` AND c.`category_id`='2' AND b.`patient_id`=d.`patient_id` AND b.`ipd_id`=d.`ipd_id`";
	if($type_id)
	{
		$test_report_num_str.=" AND c.`type_id`='$type_id'";
	}
	$test_report_num=mysqli_num_rows(mysqli_query($link, $test_report_num_str));
?>
	<div class="print_btn">
		<b>Total test = <?php echo $test_num; ?></b><br>
		<b>Reported test = <?php echo $test_report_num; ?></b><br>
		<b>Pending test = <?php echo ($test_num-$test_report_num); ?></b>
		<button class="btn btn-print" style="float:right;" onclick="print_page('<?php echo $type; ?>','<?php echo $branch_id; ?>','<?php echo $doc_id; ?>','<?php echo $type_id; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"><i class="icon-print"></i> Print</button>
	</div>
	<table class="table table-bordered table-hover text-center">
		<thead class="table_header_fix">
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Bill No.</th>
				<th>Batch No.</th>
				<th>Test Name</th>
				<th>Ref. Doctor</th>
				<th>No. Of Test</th>
				<th style="text-align:center;">Rate</th>
			</tr>
		</thead>
		<tbody>
<?php
		while($dept_info=mysqli_fetch_array($dept_qry))
		{
			echo "<tr><th colspan='8'>$dept_info[name]</th></tr>";
			$type_id=$dept_info["id"];
			
			$str="SELECT a.`patient_id`,a.`opd_id`,a.`refbydoctorid`,a.`type`,b.`batch_no`,b.`testid`,b.`testname`, b.`date` AS `report_date` FROM `uhid_and_opdid` a, `testresults_rad` b, `testmaster` c WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`branch_id`='$branch_id' AND b.`doc`='$doc_id' AND b.`date` BETWEEN '$date1' AND '$date2' AND b.`testid`=c.`testid` ";
			
			$str.=" AND c.`type_id`='$type_id'";
			
			//echo $str;
			
			$pat_reg_qry=mysqli_query($link, $str);
			
			$n=1;
			$dept_test_no=$dept_test_amount=0;
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$refbydoctorid=$pat_reg["refbydoctorid"];
				
				$reg_type=mysqli_fetch_array(mysqli_query($link, "SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]'"));
				if($reg_type["type"]==3)
				{
					$refdoc=mysqli_fetch_array(mysqli_query($link, "SELECT `refbydoctorid` FROM `ipd_test_ref_doc` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[opd_id]' AND `batch_no`='$pat_reg[batch_no]'"));
					$refbydoctorid=$refdoc["refbydoctorid"];
				}
				$refdoc_info=mysqli_fetch_array(mysqli_query($link, "SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$refbydoctorid'"));
				
				$test_det=mysqli_fetch_array(mysqli_query($link, "SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$pat_reg[patient_id]' AND (`opd_id`='$pat_reg[opd_id]' OR `ipd_id`='$pat_reg[opd_id]') AND `batch_no`='$pat_reg[batch_no]' AND `testid`='$pat_reg[testid]'"));
	?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo date("d-M-Y",strtotime($pat_reg["report_date"])); ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_reg["batch_no"]; ?></td>
					<td><?php echo $pat_reg["testname"]; ?></td>
					<td><?php echo $refdoc_info["ref_name"]; ?></td>
					<td>1</td>
					<td style="text-align:right;"><?php echo number_format($test_det["test_rate"],2); ?></td>
				</tr>
	<?php
				$n++;
				$dept_test_no++;
				$dept_test_amount+=$test_det["test_rate"];
			}
?>
			<tr>
				<th colspan="6" style="text-align:right;">Total</th>
				<th><?php echo $dept_test_no; ?></th>
				<th style="text-align:right;"><?php echo number_format($dept_test_amount,2); ?></th>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
<?php
}

?>
