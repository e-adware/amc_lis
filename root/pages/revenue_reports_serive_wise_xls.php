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

$filename ="revenue_reports_serive_wise_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
	<table class="table table-bordered table-hover text-center">
		<thead class="table_header_fix">
			<tr>
				<th>Date</th>
<?php
			if($group_id==101)
			{
				echo "<th>OPD CHARGES</th>";
			}
			else if($group_id==104)
			{
				$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='1' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
				while($service=mysqli_fetch_array($service_qry))
				{
					echo "<th>$service[testname]</th>";
				}
			}
			else if($group_id==150)
			{
				$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='3' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
				while($service=mysqli_fetch_array($service_qry))
				{
					echo "<th>$service[testname]</th>";
				}
			}
			else if($group_id==151)
			{
				$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='2' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
				while($service=mysqli_fetch_array($service_qry))
				{
					echo "<th>$service[testname]</th>";
				}
			}
			else
			{
				$service_qry=mysqli_query($link, "SELECT DISTINCT a.`service_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id'AND a.`group_id`='$group_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
				while($service=mysqli_fetch_array($service_qry))
				{
					$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT `charge_name` FROM `charge_master` WHERE `charge_id`='$service[service_id]'"));
					if($serv_val)
					{
						$serv_name=$serv_val["charge_name"];
					}
					else
					{
						$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT `service_text` FROM `ipd_pat_service_details` WHERE `service_id`='$service[service_id]'"));
						$serv_name=$serv_val["service_text"];
					}
					echo "<th>$serv_name</th>";
				}
			}
			
?>
				<th>Total</th>
			</tr>
		</thead>
<?php
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
?>
			<tbody>
				<tr>
					<td><?php echo date("d-m-Y",strtotime($date)); ?></td>
<?php
				$i=1;
				if($group_id==101)
				{
					$opd_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(`tot_amount`) AS `opd_tot` FROM `consult_patient_payment_details` WHERE `date`='$date'"));
					$service_amount=$opd_val["opd_tot"];
					
					echo "<td style='text-align:right;'>".number_format($service_amount,2)."</td>";
					
					$day_total+=$service_amount;
					$each_serv_tot[$i]+=$service_amount;
					$i++;
				}
				else if($group_id==104)
				{
					$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='1' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
					while($service=mysqli_fetch_array($service_qry))
					{
						$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=1 AND a.`date`='$date' AND a.`testid`='$service[testid]'"));
						$service_amount=$serv_val["lab_tot"];
						
						echo "<td style='text-align:right;'>".number_format($service_amount,2)."</td>";
						
						$day_total+=$service_amount;
						$each_serv_tot[$i]+=$service_amount;
						$i++;
					}
				}
				else if($group_id==150)
				{
					$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='3' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
					while($service=mysqli_fetch_array($service_qry))
					{
						$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=3 AND a.`date`='$date' AND a.`testid`='$service[testid]'"));
						$service_amount=$serv_val["lab_tot"];
						
						echo "<td style='text-align:right;'>".number_format($service_amount,2)."</td>";
						
						$day_total+=$service_amount;
						$each_serv_tot[$i]+=$service_amount;
						$i++;
					}
				}
				else if($group_id==151)
				{
					$service_qry=mysqli_query($link, "SELECT DISTINCT a.`testid`,b.`testname` FROM `patient_test_details` a, `testmaster` b , `uhid_and_opdid` c WHERE a.`testid`=b.`testid` AND b.`category_id`='2' AND a.`patient_id`=c.`patient_id` AND (a.`opd_id`=c.`opd_id` OR a.`ipd_id`=c.`opd_id`) AND c.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
					while($service=mysqli_fetch_array($service_qry))
					{
						$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`test_rate`) AS `lab_tot` FROM `patient_test_details` a, `testmaster` b WHERE a.`testid`=b.`testid` AND b.`category_id`=2 AND a.`date`='$date' AND a.`testid`='$service[testid]'"));
						$service_amount=$serv_val["lab_tot"];
						
						echo "<td style='text-align:right;'>".number_format($service_amount,2)."</td>";
						
						$day_total+=$service_amount;
						$each_serv_tot[$i]+=$service_amount;
						$i++;
					}
				}
				else
				{
					$service_qry=mysqli_query($link, "SELECT DISTINCT a.`service_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id' AND a.`group_id`='$group_id' AND a.`date` BETWEEN '$date1' AND '$date2'");
					while($service=mysqli_fetch_array($service_qry))
					{
						//echo "SELECT SUM(a.`amount`) AS `lab_tot` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id' AND a.`group_id`='$group_id' AND a.`service_id`='$service[service_id]' AND a.`date`='$date'<br>";
						$serv_val=mysqli_fetch_array(mysqli_query($link, "SELECT SUM(a.`amount`) AS `lab_tot` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id' AND a.`group_id`='$group_id' AND a.`service_id`='$service[service_id]' AND a.`date`='$date'"));
						$service_amount=$serv_val["lab_tot"];
						
						echo "<td style='text-align:right;'>".number_format($service_amount,2)."</td>";
						
						$day_total+=$service_amount;
						$each_serv_tot[$i]+=$service_amount;
						$i++;
					}
				}
?>
				<th style="text-align:right;"><?php echo number_format($day_total,2); ?></th>
			</tr>
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
		</tr>
	</table>
