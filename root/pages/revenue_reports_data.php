<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];
$branch_id=$_POST['branch_id'];
$group_id=$_POST['group_id'];

$type=$_POST['type'];

if($type=="group_wise")
{
	$str="SELECT `group_id`, `group_name` FROM `charge_group_master` WHERE (`group_id` IN(101) OR `group_id` IN(SELECT DISTINCT a.`group_id` FROM `ipd_pat_service_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`branch_id`='$branch_id' AND a.`date` BETWEEN '$date1' AND '$date2')) ORDER BY `group_id` ASC";
	
	//echo $str;
	
	$group_qry=mysqli_query($link, $str);
?>
	<button class="btn btn-excel print_btn" style="float:right;" onclick="excel_page('<?php echo $type; ?>','<?php echo $branch_id; ?>','<?php echo $group_id; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"><i class="icon-file"></i> Excel</button>
	
	<button class="btn btn-print print_btn" style="float:right;" onclick="print_page('<?php echo $type; ?>','<?php echo $branch_id; ?>','<?php echo $group_id; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"><i class="icon-print"></i> Print</button>
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
<?php
}

if($type=="serive_wise")
{
?>
	<button class="btn btn-excel print_btn" style="float:right;" onclick="excel_page('<?php echo $type; ?>','<?php echo $branch_id; ?>','<?php echo $group_id; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"><i class="icon-file"></i> Excel</button>
	
	<button class="btn btn-print print_btn" style="float:right;" onclick="print_page('<?php echo $type; ?>','<?php echo $branch_id; ?>','<?php echo $group_id; ?>','<?php echo $date1; ?>','<?php echo $date2; ?>')"><i class="icon-print"></i> Print</button>
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
<?php
}
?>
