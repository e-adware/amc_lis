<?php
session_start();
include('../../includes/connection.php');

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$user_entry=$_GET['user_entry'];


$filename ="daily_reports_xls".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$user_str="";
$user_str_a="";
$user_str_b="";
$emp_name="All User";
if($user_entry>0)
{
	$user_str=" AND `user`='$user_entry'";
	$user_str_a=" AND a.`user`='$user_entry'";
	$user_str_b=" AND b.`user`='$user_entry'";
	
	$user_name=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
	$emp_name=$user_name["name"];
}
?>
<table class="table table-hover table-condensed">
	<tr>
		<td colspan="3">
			<b>CASHIER ON DUTY : </b> <?php echo $emp_name; ?>
		</td>
	</tr>
<?php
	// OPD
	$opd_pat_new=$opd_pat_pfu=$opd_pat_ffu=0;
	$opd_visit_amount=$opd_reg_amount=$opd_disount_amount=$opd_free_amount_det=0;
	
	$pat_pay_det_opd_qry=mysqli_query($link," SELECT * FROM `consult_patient_payment_details` WHERE `date` between '$date1' AND '$date2' $user_str");
	while($pat_pay_det_opd=mysqli_fetch_array($pat_pay_det_opd_qry))
	{
		// Patient count
		if($pat_pay_det_opd["visit_fee"]>0)
		{
			$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='6' "));
			if($pat_paid_follow)
			{
				$opd_pat_pfu++;
			}else
			{
				$opd_pat_new++;
			}
		}
		else
		{
			$opd_pat_ffu++;
		}
		// Payment
		$opd_visit_amount+=$pat_pay_det_opd["visit_fee"];
		$opd_reg_amount+=$pat_pay_det_opd["regd_fee"]+$pat_pay_det_opd["emergency_fee"];
		
		$opd_pat_free=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `invest_payment_free` WHERE `opd_id`='$con_pay[opd_id]' "));
		if(!$opd_pat_free)
		{
			$opd_disount_amount+=$pat_pay_det_opd["dis_amt"];
			
			$all_total_disount_amount+=$pat_pay_det_opd["dis_amt"];
		}else
		{
			$opd_free_amount_det+=$opd_pat_free["free_amount"];
		}
	}
	
?>
	<tr>
		<th colspan="3">OPD Registration</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Patient</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo ($opd_pat_new+$opd_pat_pfu+$opd_pat_ffu); ?> (NP : <?php echo $opd_pat_new; ?>, PFU : <?php echo $opd_pat_pfu; ?>, FFU : <?php echo $opd_pat_ffu; ?>)</td>
		<td><?php echo number_format($opd_visit_amount,2); ?></td>
	</tr>
<?php
	// Dental
	$dental_pat_num=$dental_pat_new=$dental_pat_pfu=$dental_pat_ffu=0;
	$dental_amount=0;
	
	$dental_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='6' $user_str");
	while($dental_pat=mysqli_fetch_array($dental_pat_qry))
	{
		$dental_pat_ser=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_dental FROM `ipd_pat_service_details` WHERE `patient_id`='$dental_pat[patient_id]' AND `ipd_id`='$dental_pat[opd_id]' AND `group_id`='188' AND `service_id`='253' "));
		if($dental_pat_ser["tot_dental"]>0)
		{
			$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='11' "));
			if($pat_paid_follow)
			{
				$dental_pat_pfu++;
			}else
			{
				$dental_pat_new++;
			}
			
			$dental_pat_num++;
			
			$dental_amount+=$dental_pat_ser["tot_dental"];	
		}
		else
		{
			$dental_pat_ffu++;
			$dental_pat_num++;
		}
	}
?>
	<tr>
		<th colspan="3">Dental Registration</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Patient</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo ($dental_pat_new+$dental_pat_pfu+$dental_pat_ffu); ?> (NPDE : <?php echo $dental_pat_new; ?>, PFDE : <?php echo $dental_pat_pfu; ?>, FFDE : <?php echo $dental_pat_ffu; ?>)</td>
		<td><?php echo number_format($dental_amount,2); ?></td>
	</tr>
<?php
	// CASUALTY
	$casu_pat_new=0;
	$casu_pat_pfu=0;
	$casu_pat_ffu=0;
	$casu_amount=0;
	
	$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='4' $user_str");
	while($casu_pat=mysqli_fetch_array($casu_pat_qry))
	{
		$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='186' AND `service_id`='251' "));
		
		if($only_casu_qry["tot_casu"]>0)
		{
			$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='8' "));
			if($pat_paid_follow)
			{
				$casu_pat_pfu++;
			}else
			{
				$casu_pat_new++;
			}
			
			$only_casu_amount+=$only_casu_qry["tot_casu"];
			
		}
		else
		{
			$casu_pat_ffu++;
		}
	}
?>
	<tr>
		<th colspan="3">EMERGENCY ROOM Registration</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Patient</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo ($casu_pat_new+$casu_pat_pfu+$casu_pat_ffu); ?>
			(NPER : <?php echo $casu_pat_new; ?>, PFER : <?php echo $casu_pat_pfu; ?>, FFER : <?php echo $casu_pat_ffu; ?>)
		</td>
		<td><?php echo number_format($only_casu_amount,2); ?></td>
	</tr>
<?php
	// Dialysis
	$dialysis_pat_new=0;
	$dialysis_pat_pfu=0;
	$dialysis_pat_ffu=0;
	$dialysis_amount=0;
	
	$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='7' $user_str");
	while($casu_pat=mysqli_fetch_array($casu_pat_qry))
	{
		$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='187' AND `service_id`='252' "));
		
		if($only_casu_qry["tot_casu"]>0)
		{
			$pat_paid_follow=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `patient_visit_type_details` WHERE `patient_id`='$pat_pay_det_opd[patient_id]' AND `opd_id`='$pat_pay_det_opd[opd_id]' AND `visit_type_id`='16' "));
			if($pat_paid_follow)
			{
				$dialysis_pat_pfu++;
			}else
			{
				$dialysis_pat_new++;
			}
			
			$dialysis_amount+=$only_casu_qry["tot_casu"];
			
		}
		else
		{
			$dialysis_pat_ffu++;
		}
	}
	// IPD Dailysis
	$ipd_dialysis_pat=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_pat_service_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`service_id`='252' $user_str_a ORDER BY a.`slno`"));
?>
	<tr>
		<th colspan="3">Dialysis Registration</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Patient</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo ($dialysis_pat_new+$dialysis_pat_pfu+$dialysis_pat_ffu+$ipd_dialysis_pat); ?>
			(NPDI : <?php echo $dialysis_pat_new; ?>, PFDI : <?php echo $dialysis_pat_pfu; ?>, FFDI : <?php echo $dialysis_pat_ffu; ?>, IPD : <?php echo $ipd_dialysis_pat; ?>)
		</td>
		<td><?php echo number_format($dialysis_amount,2); ?></td>
	</tr>
<?php
	// Daycare
	$daycare_pat_new=0;
	$daycare_amount=0;
	
	$casu_pat_qry=mysqli_query($link,"SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='5' $user_str");
	while($casu_pat=mysqli_fetch_array($casu_pat_qry))
	{
		$only_casu_qry=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS tot_casu FROM `ipd_pat_service_details` WHERE `patient_id`='$casu_pat[patient_id]' AND `ipd_id`='$casu_pat[opd_id]' AND `group_id`='167' AND `service_id`='150' "));
		
		if($only_casu_qry["tot_casu"]>0)
		{
			$daycare_pat_new++;
			
			$daycare_amount+=$only_casu_qry["tot_casu"];
			
		}
	}
?>
	<tr>
		<th colspan="3">Daycare Registration</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Patient</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<?php echo $daycare_pat_new; ?>
		</td>
		<td><?php echo number_format($daycare_amount,2); ?></td>
	</tr>
<?php
	// Dental Procedure `group_id`='192'
	$dental_procedure_pat=$dental_procedure_num=$dental_procedure_amount=0;
	
	$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='192') $user_str_b ORDER BY a.`slno`");
	while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
	{
		$dental_procedure=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS tot_casu FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' AND `ipd_id`='$ipd_casual_pay[ipd_id]' AND `ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='192') "));
		
		if($dental_procedure["tot_casu"]>0)
		{
			$dental_procedure_pat++;
			
			$dental_procedure_amount+=$dental_procedure["tot_casu"];
			
			$dental_serv_num=mysqli_num_rows(mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `ipd_id`='$ipd_casual_pay[ipd_id]' AND `group_id`='192' "));
			$dental_procedure_num+=$dental_serv_num;
		}
	}
	
?>
	<tr>
		<th colspan="3">Dental Procedure</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No of Procedure</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $dental_procedure_pat; ?></td>
		<td><?php echo $dental_procedure_num; ?></td>
		<td><?php echo number_format($dental_procedure_amount,2); ?></td>
	</tr>
<?php
	// Daycare Services
	$day_group_id = array();
	array_push($day_group_id, 191, 192); // MISCELLANEOUS , DENTAL PROCEDURE
	$day_group_id = join(',',$day_group_id);
	
	$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` NOT IN($day_group_id)) $user_str_b ORDER BY a.`slno`");
	$ipd_casual_pay_num=mysqli_num_rows($ipd_casual_pay_qry);
	if($ipd_casual_pay_num>0)
	{
		echo "<tr><th colspan='3'>DAYCARE SERVICES</th></tr>";
		echo "<tr><td>Service Name</td><td>No</td><td>Amount</td></tr>";
		
		$charge_qry=mysqli_query($link, "SELECT `charge_id`, `charge_name` FROM `charge_master` WHERE `group_id` NOT IN($day_group_id) ORDER BY `charge_name`");
		while($charge_val=mysqli_fetch_array($charge_qry))
		{
			$each_service_num=0;
			$each_service_amount=0;
			$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id` NOT IN($day_group_id)) ORDER BY a.`slno`");
			while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
			{
				$service_qry=mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `ipd_id`='$ipd_casual_pay[ipd_id]' AND `service_id`='$charge_val[charge_id]' AND `rate`>0 ");
				//$service_num=mysqli_num_rows($service_qry);
				$service_num=$service_amount=0;
				while($service_val=mysqli_fetch_array($service_qry))
				{
					$service_num+=$service_val["ser_quantity"];
					$service_amount=$service_val["rate"]*$service_num;
				}
				
				$each_service_num+=$service_num;
				$each_service_amount+=$service_amount;
			}
			if($each_service_num>0)
			{
				echo "<tr><td>$charge_val[charge_name]</td><td>$each_service_num</td><td>$each_service_amount</td></tr>";
				$all_total_amount+=$each_service_amount;
			}
		}
	}
	// MISCELLANEOUS Services
	
	$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='191') $user_str_b ORDER BY a.`slno`");
	$ipd_casual_pay_num=mysqli_num_rows($ipd_casual_pay_qry);
	if($ipd_casual_pay_num>0)
	{
		echo "<tr><th colspan='3'>MISCELLANEOUS SERVICES</th></tr>";
		echo "<tr><td>Service Name</td><td>No</td><td>Amount</td></tr>";
		
		$charge_qry=mysqli_query($link, "SELECT `charge_id`, `charge_name` FROM `charge_master` WHERE `group_id`='191' ORDER BY `charge_name`");
		while($charge_val=mysqli_fetch_array($charge_qry))
		{
			$each_service_num=0;
			$each_service_amount=0;
			$ipd_casual_pay_qry=mysqli_query($link, " SELECT DISTINCT(a.`ipd_id`) FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='9' AND a.`date` between '$date1' and '$date2' AND a.`ipd_id` IN(SELECT `ipd_id` FROM `ipd_pat_service_details` WHERE `group_id`='191') ORDER BY a.`slno`");
			while($ipd_casual_pay=mysqli_fetch_array($ipd_casual_pay_qry))
			{
				$mis_service_qry=mysqli_query($link," SELECT * FROM `ipd_pat_service_details` WHERE `ipd_id`='$ipd_casual_pay[ipd_id]' AND `service_id`='$charge_val[charge_id]' AND `rate`>0 ");
				//$service_num=mysqli_num_rows($mis_service_qry);
				$service_num=$service_amount=0;
				while($service_val=mysqli_fetch_array($mis_service_qry))
				{
					$service_num+=$service_val["ser_quantity"];
					$service_amount=$service_val["rate"]*$service_num;
				}
				
				$each_service_num+=$service_num;
				$each_service_amount+=$service_amount;
			}
			if($each_service_num>0)
			{
				echo "<tr><td>$charge_val[charge_name]</td><td>$each_service_num</td><td>$each_service_amount</td></tr>";
				$all_total_amount+=$each_service_amount;
			}
		}
	}
	
?>
<?php
	// Laboratory OPD
	$opd_lab_pat=$opd_lab_path_amount=$opd_test_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='2' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a ");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$opd_lab_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$opd_test_num++;
				$opd_lab_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">LABORATORY</th>
	</tr>
	<tr>
		<th colspan="3">OPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $opd_lab_pat; ?></td>
		<td><?php echo $opd_test_num; ?></td>
		<td><?php echo number_format($opd_lab_path_amount,2); ?></td>
	</tr>
<?php
	// Laboratory IPD
	$ipd_lab_pat=$ipd_lab_path_amount=$ipd_test_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='1') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$ipd_lab_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$ipd_test_num++;
				$ipd_lab_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $ipd_lab_pat; ?></td>
		<td><?php echo $ipd_test_num; ?></td>
		<td><?php echo number_format($ipd_lab_path_amount,2); ?></td>
	</tr>
<?php
	// ECG OPD
	$opd_ecg_pat=$opd_ecg_path_amount=$opd_ecg_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND (a.`type`='2' OR a.`type`='10') AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$opd_ecg_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$opd_ecg_num++;
				$opd_ecg_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">ECG</th>
	</tr>
	<tr>
		<th colspan="3">OPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $opd_ecg_pat; ?></td>
		<td><?php echo $opd_ecg_num; ?></td>
		<td><?php echo number_format($opd_ecg_path_amount,2); ?></td>
	</tr>
<?php
	// ECG IPD
	$ipd_ecg_pat=$ipd_ecg_path_amount=$ipd_ecg_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `category_id`='3') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$ipd_ecg_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$ipd_ecg_num++;
				$ipd_ecg_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $ipd_ecg_pat; ?></td>
		<td><?php echo $ipd_ecg_num; ?></td>
		<td><?php echo number_format($ipd_ecg_path_amount,2); ?></td>
	</tr>
<?php
	// SONOGRAPHY OPD
	$opd_usg_pat=$opd_usg_path_amount=$opd_usg_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND (a.`type`='2' OR a.`type`='10') AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$opd_usg_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$opd_usg_num++;
				$opd_usg_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">SONOGRAPHY</th>
	</tr>
	<tr>
		<th colspan="3">OPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $opd_usg_pat; ?></td>
		<td><?php echo $opd_usg_num; ?></td>
		<td><?php echo number_format($opd_usg_path_amount,2); ?></td>
	</tr>
<?php
	// SONOGRAPHY IPD
	$ipd_usg_pat=$ipd_usg_path_amount=$ipd_usg_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='128') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$ipd_usg_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$ipd_usg_num++;
				$ipd_usg_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $ipd_usg_pat; ?></td>
		<td><?php echo $ipd_usg_num; ?></td>
		<td><?php echo number_format($ipd_usg_path_amount,2); ?></td>
	</tr>
<?php
	// X-RAY OPD
	$opd_xray_pat=$opd_xray_path_amount=$opd_xray_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND (a.`type`='2' OR a.`type`='10') AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$opd_xray_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$opd_xray_num++;
				$opd_xray_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">X-RAY</th>
	</tr>
	<tr>
		<th colspan="3">OPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $opd_xray_pat; ?></td>
		<td><?php echo $opd_xray_num; ?></td>
		<td><?php echo number_format($opd_xray_path_amount,2); ?></td>
	</tr>
<?php
	// X-RAY IPD
	$ipd_xray_pat=$ipd_xray_path_amount=$ipd_xray_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='40') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$ipd_xray_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$ipd_xray_num++;
				$ipd_xray_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $ipd_xray_pat; ?></td>
		<td><?php echo $ipd_xray_num; ?></td>
		<td><?php echo number_format($ipd_xray_path_amount,2); ?></td>
	</tr>
<?php
	// ENDOSCOPY OPD
	$opd_endos_pat=$opd_endos_path_amount=$opd_endos_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND (a.`type`='2' OR a.`type`='10') AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `opd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$opd_endos_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$opd_endos_num++;
				$opd_endos_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">ENDOSCOPY</th>
	</tr>
	<tr>
		<th colspan="3">OPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $opd_endos_pat; ?></td>
		<td><?php echo $opd_endos_num; ?></td>
		<td><?php echo number_format($opd_endos_path_amount,2); ?></td>
	</tr>
<?php
	// ENDOSCOPY IPD
	$ipd_endos_pat=$ipd_endos_path_amount=$ipd_endos_num=0;
	$lab_pat_qry=mysqli_query($link,"SELECT DISTINCT a.`patient_id`,a.`opd_id` FROM `uhid_and_opdid` a, `patient_test_details` b WHERE a.`patient_id`=b.`patient_id` AND (a.`opd_id`=b.`opd_id` OR a.`opd_id`=b.`ipd_id`) AND a.`type`='3' AND b.`date` BETWEEN '$date1' AND '$date2' $user_str_a");
	while($lab_pat=mysqli_fetch_array($lab_pat_qry))
	{
		$opd_test_qry=mysqli_query($link," SELECT `test_rate` FROM `patient_test_details` WHERE `patient_id`='$lab_pat[patient_id]' AND `ipd_id`='$lab_pat[opd_id]' AND `testid` IN(SELECT `testid` FROM `testmaster` WHERE `type_id`='121') AND `date` BETWEEN '$date1' AND '$date2' ");
		$opd_test_num_rows=mysqli_num_rows($opd_test_qry);
		if($opd_test_num_rows>0)
		{
			$ipd_endos_pat++;
			
			while($opd_test=mysqli_fetch_array($opd_test_qry))
			{
				$ipd_endos_num++;
				$ipd_endos_path_amount+=$opd_test["test_rate"];
			}
		}
	}
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td>No. of Patient</td>
		<td>No. of Test</td>
		<td>Amount</td>
	</tr>
	<tr>
		<td><?php echo $ipd_endos_pat; ?></td>
		<td><?php echo $ipd_endos_num; ?></td>
		<td><?php echo number_format($ipd_endos_path_amount,2); ?></td>
	</tr>
<?php
$ipd_new_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' and '$date2' AND `type`='3' $user_str "));

$ipd_discharge_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `date` between '$date1' and '$date2'  $user_str "));

$ipd_admit_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date1' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date1') "));

// IPD Payment
$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_adv` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Advance' $user_str_a"));
$ipd_advance_recv=$ipd_pay_det["tot_adv"];

$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_final` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Final' $user_str_a"));
$ipd_final_recv=$ipd_pay_det["tot_final"];

$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_bal` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Balance' $user_str_a"));
$ipd_balance_recv=$ipd_pay_det["tot_bal"];

$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund`),0) AS `tot_refund` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' "));
$ipd_refunded=$ipd_pay_det["tot_refund"];

$ipd_total_amount_recv=$ipd_advance_recv+$ipd_final_recv+$ipd_balance_recv-$ipd_refunded;
?>
	<tr>
		<th colspan="3">IPD</th>
	</tr>
	<tr>
		<td></td>
		<td>No. of Admission</td>
		<td><?php echo $ipd_new_pat_num; ?></td>
	</tr>
	<tr>
		<td></td>
		<td>No. of Discharge</td>
		<td><?php echo $ipd_discharge_pat_num; ?></td>
	</tr>
	<tr>
		<td></td>
		<td>No. of Admitted Patient</td>
		<td><?php echo $ipd_admit_pat_num; ?></td>
	</tr>
	<tr>
		<td></td>
		<td>Amount received</td>
		<td><?php echo number_format($ipd_total_amount_recv,2); ?></td>
	</tr>
</table>
