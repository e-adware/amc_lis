<?php
session_start();
include("../../includes/connection.php");
include("../../includes/global.function.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$typ=$_GET['typ'];
$date1=$_GET['date1'];
$date2=$_GET['date2'];
$group_id=$_GET['gid'];
$branch_id=$_GET['bid'];

$filename ="revenue_reports_group_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$str="SELECT `group_id`, `group_name` FROM `charge_group_master` WHERE (`group_id` IN(101) OR `group_id` IN(SELECT DISTINCT a.`group_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2')) ORDER BY `group_id` ASC";

$group_qry=mysqli_query($link, $str);

?>
	<table class="table table-bordered table-hover text-center">
		<thead class="table_header_fix">
			<tr>
				<th>Date</th>
<?php
			while($group_info=mysqli_fetch_array($group_qry))
			{
				echo "<th>$group_info[group_name]</th>";
			}
?>
				<th>Total</th>
				<th>Expense</th>
			</tr>
		</thead>
<?php
	$grand_expense=0;
	$each_serv_tot=array();
	$dates = getDatesFromRange($date1,$date2);
	foreach($dates as $date)
	{
		if($date)
		{
			$date_num=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `uhid_and_opdid` WHERE `date`='$date'"));
			$date_num+=mysqli_num_rows(mysqli_query($link, "SELECT `slno` FROM `ipd_pat_service_details` WHERE `date`='$date'"));
			if($date_num>0)
			{
				$day_total=0;
				
				$expense_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`Amount`) AS `exp_tot` FROM `expensedetail` WHERE `entry_date`='$date'"));
				$expense_amount=$expense_val["exp_tot"];
				
				$grand_expense+=$expense_amount;
?>
			<tbody>
				<tr>
					<td><?php echo date("d-m-Y",strtotime($date)); ?></td>
<?php
			$i=1;
			$group_qry=mysqli_query($link, $str);
			while($group_info=mysqli_fetch_array($group_qry))
			{
				$group_id=$group_info["group_id"];
				$group_amount=0;
				if($group_id==101) // OPD
				{
					$opd_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`tot_amount`) AS `opd_tot` FROM `consult_patient_payment_details` WHERE `date`='$date'"));
					$group_amount=$opd_val["opd_tot"];
				}
				else if($group_id==104) // Lab
				{
					$opd_lab_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `opd_lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=1 AND a.`date`='$date'"));
					$group_amount=$opd_lab_val["opd_lab_tot"];
					
				}
				else if($group_id==150) // Cardiology
				{
					$opd_lab_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `opd_lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=3 AND a.`date`='$date'"));
					$group_amount=$opd_lab_val["opd_lab_tot"];
				}
				else if($group_id==151) // Radiology
				{
					$opd_lab_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `opd_lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=2 AND a.`date`='$date'"));
					$group_amount=$opd_lab_val["opd_lab_tot"];
				}
				else 
				{
					$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`amount`) `serv_tot` FROM `ipd_pat_service_details` WHERE `group_id`='$group_id' AND `date`='$date'"));
					
					$group_amount=$serv_val["serv_tot"]+$opd_lab_amount;
				}
?>
				<td style="text-align:right;"><?php echo number_format($group_amount,2); ?></td>
<?php
				$day_total+=$group_amount;
				$each_serv_tot[$i]+=$group_amount;
				$i++;
			}
?>
					<th style="text-align:right;"><?php echo number_format($day_total,2); ?></th>
					<th style="text-align:right;"><?php echo number_format($expense_amount,2); ?></th>
				</tr>
			</tbody>
<?php
			}
		}
		
	}
?>
		<tr style="display:;">
			<th style="text-align:right;">Total</th>
<?php
		$grand_total=0;
		for($m=1;$m<$i;$m++)
		{
?>
			<th style="text-align:right;"><?php echo number_format($each_serv_tot[$m],2); ?></th>
<?php
			$grand_total+=$each_serv_tot[$m];
		}
?>
			<th style="text-align:right;"><?php echo number_format($grand_total,2); ?></th>
			<th style="text-align:right;"><?php echo number_format($grand_expense,2); ?></th>
		</tr>
	</table>
