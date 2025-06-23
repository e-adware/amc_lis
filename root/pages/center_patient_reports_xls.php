<?php
session_start();
include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$fdate=$date1=$_GET['date1'];
$tdate=$date2=$_GET['date2'];
$cid=$_GET['cid'];

$filename ="centre_patient_report_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

	if($cid)
	{
		$center_str="SELECT `centreno`, `centrename` FROM `centremaster`";
		$center_str.=" WHERE `centreno`='$cid'";
	}
	$center_str.=" ORDER BY `centrename` ASC";
	
	$center_qry=mysqli_query($link, $center_str);
	while($center_info=mysqli_fetch_array($center_qry))
	{
		echo "<b>Center Patient Reports</b><br>";
		echo "<b>Centre Name : ".$center_info['centrename']."</b><br>";
		
		$str="SELECT * FROM `uhid_and_opdid` WHERE `center_no`='$center_info[centreno]' AND `date` BETWEEN '$fdate' AND '$tdate' ";
	
		$qry=mysqli_query($link, $str);
?>
		<table class="table table-condensed table-bordered table-hover">
			<thead class="table_header_fix">
				<tr>
					<th style="width: 1%;">#</th>
					<th>Date</th>
					<th>UHID</th>
					<th>PIN</th>
					<th>Name</th>
					<th>Phone</th>
					<th>City</th>
					<th>Bill Amount</th>
					<th>Encounter</th>
				</tr>
			</thead>
		<?php
			$i=1;
			$total_bill_amount=0;
			while($reg_info=mysqli_fetch_array($qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info` WHERE `patient_id`='$reg_info[patient_id]'"));
				
				$pat_info_rel=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `patient_info_rel` WHERE `patient_id`='$reg_info[patient_id]'"));
				
				$rdoc=mysqli_fetch_array(mysqli_query($link,"SELECT `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$reg_info[refbydoctorid]'"));
				
				$pat_pay_detail=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
				
				$visit_type=mysqli_fetch_array(mysqli_query($link,"SELECT `p_type`,`type` FROM `patient_type_master` WHERE `p_type_id`='$reg_info[type]'"));
				
				$bill_amount="0.00";
				if($visit_type["type"]==1)
				{
					$bill=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`tot_amount`),0) AS `tot` FROM `consult_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					
					$bill_amount=$bill["tot"];
				}
				if($visit_type["type"]==2)
				{
					$bill=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`tot_amount`),0) AS `tot` FROM `invest_patient_payment_details` WHERE `patient_id`='$reg_info[patient_id]' AND `opd_id`='$reg_info[opd_id]'"));
					
					$bill_amount=$bill["tot"];
				}
				if($visit_type["type"]==3)
				{
					$bill=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_pat_service_details` WHERE `patient_id`='$reg_info[patient_id]' AND `ipd_id`='$reg_info[opd_id]'"));
					
					$bill_amount=$bill["tot"];
				}
				
				$total_bill_amount+=$bill_amount;
		?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo date("d-M-Y", strtotime($reg_info['date']));?></td>
					<td><?php echo $pat_info['patient_id'];?></td>
					<td><?php echo $reg_info['opd_id'];?></td>
					<td><?php echo $pat_info['name'];?></td>
					<td><?php echo $pat_info['phone'];?></td>
					<td><?php echo $pat_info_rel['city'];?></td>
					<td><?php echo $bill_amount; ?></td>
					<td><?php echo $visit_type['p_type'];?></td>
				</tr>
		<?php
				$i++;
			}
		?>
		</table>
<?php
	}

?>
