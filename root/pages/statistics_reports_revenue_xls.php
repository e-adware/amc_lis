<?php
include('../../includes/connection.php');

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$date1=$_GET['date1'];
$date2=$_GET['date2'];

$filename ="statistics_reports_revenue_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

$today=date("Y-m-d");

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

function money_view($val)
{
	//$val=number_format($val,2);
	$val = money_format('%!i', $val);
	return $val;
}

$start    = new DateTime($date1);
$start->modify('first day of this month');
$end      = new DateTime($date2);
$end->modify('first day of next month');
$interval = DateInterval::createFromDateString('1 day');
$period   = new DatePeriod($start, $interval, $end);

?>
<table class="table table-condensed table-hover table-boredered">
	<thead class="table_header_fix">
		<tr>
			<th>DATE</th>
			<th>DAY</th>
			<th colspan="4">OPD Revenue</th>
			<th colspan="4">Emergency Room Revenue</th>
			<th colspan="4">Daycare Revenue</th>
			<th colspan="4">IPD Revenue</th>
			<th colspan="4">Pharmacy Revenue</th>
			<th colspan="4">LAB Revenue</th>
			<th colspan="4">USG Revenue</th>
			<th colspan="4">X-Ray Revenue</th>
			<th colspan="4">Endoscopy Revenue</th>
			<th colspan="4">ECG Revenue</th>
			<th colspan="4">Dialysis Revenue</th>
			<th colspan="4">Ambulance Revenue</th>
			<th colspan="4">Dental Procedure Revenue</th>
			<th colspan="4">Other Procedure Revenue</th>
			<th colspan="4">Miscelleneous Revenue</th>
			<th colspan="4">Total Revenue</th>
			<th>Grand Total</th>
		</tr>
		<tr>
			<td></td>
			<td></td>
		<?php
			$td=0;
			while($td<16)
			{
				echo "<td>Credit</td>";
				echo "<td>Cash</td>";
				echo "<td>Card</td>";
				echo "<th>Total</th>";
				$td++;
			}
		?>
			<td></td>
		</tr>
	</thead>
<?php
	$opd_cash_amount_week=$opd_card_amount_week=$opd_credit_amount_week=$opd_total_amount_week=0;
	$daycare_cash_amount_week=$daycare_card_amount_week=$daycare_credit_amount_week=$daycare_total_amount_week=0;
	$ipd_cash_amount_week=$ipd_card_amount_week=$ipd_credit_amount_week=$ipd_total_amount_week=0;
	$lab_cash_amount_week=$lab_card_amount_week=$lab_credit_amount_week=$lab_total_amount_week=0;
	$usg_cash_amount_week=$usg_card_amount_week=$usg_credit_amount_week=$usg_total_amount_week=0;
	$xray_cash_amount_week=$xray_card_amount_week=$xray_credit_amount_week=$xray_total_amount_week=0;
	$ecg_cash_amount_week=$ecg_card_amount_week=$ecg_credit_amount_week=$ecg_total_amount_week=0;
	$endo_cash_amount_week=$endo_card_amount_week=$endo_credit_amount_week=$endo_total_amount_week=0;
	$dialysis_cash_amount_week=$dialysis_card_amount_week=$dialysis_credit_amount_week=$dialysis_total_amount_week=0;
	$emergency__cash_amount_week=$emergency__card_amount_week=$emergency__credit_amount_week=$emergency__total_amount_week=0;
	$denpro_cash_amount_week=$denpro_card_amount_week=$denpro_credit_amount_week=$denpro_total_amount_week=0;
	$misc_cash_amount_week=$misc_card_amount_week=$misc_credit_amount_week=$misc_total_amount_week=0;
	$ambulance_cash_amount_week=$ambulance_card_amount_week=$ambulance_credit_amount_week=$ambulance_total_amount_week=0;
	$otherpro_cash_amount_week=$otherpro_card_amount_week=$otherpro_credit_amount_week=$otherpro_total_amount_week=0;
	$each_day_credit_week=$each_day_cash_week=$each_day_card_week=$each_day_total_week=0;
	
	foreach($period as $dt)
	{
		if($dt)
		{
			$date=$dt->format("Y-m-d");
			$day=date("D", strtotime($date));
			
			$last_day_month=date("Y-m-t", strtotime($date));
			
			$each_day_credit=0;
			$each_day_cash=0;
			$each_day_card=0;
			$each_day_total=0;
			
			// OPD
			$opd_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `consult_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='1' AND `date`='$date') "));
				$non_cash_refund_amount_opd_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `consult_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='1' AND `date`='$date') "));
				$non_cash_refund_amount_opd_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `consult_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='1' AND `date`!='$date') "));
				$non_cash_refund_amount_opd_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$opd_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' "));
				$opd_cash_amount=$opd_cash["tot"]-$non_cash_refund_amount_opd_same_all-$non_cash_refund_amount_opd_same_ref;
				
				$opd_total_amount+=$opd_cash_amount;
				$opd_cash_amount_week+=$opd_cash_amount;
				$each_day_cash+=$opd_cash_amount;
				$each_day_cash_week+=$opd_cash_amount;
			
				// Card
				$opd_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' "));
				$opd_card_amount=$opd_card["tot"]+$non_cash_refund_amount_opd_same_all+$non_cash_refund_amount_opd_same_reg;
				
				$opd_total_amount+=$opd_card_amount;
				$opd_card_amount_week+=$opd_card_amount;
				$each_day_card+=$opd_card_amount;
				$each_day_card_week+=$opd_card_amount;
				
				// Credit
				$opd_credit_amount=0;
				$opd_credit_qry=mysqli_query($link," SELECT * FROM `consult_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' ");
				while($opd_credit=mysqli_fetch_array($opd_credit_qry))
				{
					$opd_credit_amount+=$opd_credit["balance"];
					
					// Check Same Day Balance Receive
					$opd_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `consult_payment_detail` WHERE `patient_id`='$opd_credit[patient_id]' AND `opd_id`='$opd_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$opd_sameday_balance_amount=$opd_sameday_balance["tot"];
					
					$opd_credit_amount-=$opd_sameday_balance_amount;
				}
				
				$opd_total_amount+=$opd_credit_amount;
				$opd_credit_amount_week+=$opd_credit_amount;
				$each_day_credit+=$opd_credit_amount;
				$each_day_credit_week+=$opd_credit_amount;
			
			$each_day_total+=$opd_total_amount;
			$each_day_total_week+=$opd_total_amount;
			$opd_total_amount_week+=$opd_total_amount;
			
			// Daycare
			$pat_visit_type=5;
			$daycare_total_amount=0;
				// Cash
				$daycare_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$daycare_cash_amount=$daycare_cash["tot"]-$daycare_cash["refund"];
				
				$daycare_total_amount+=$daycare_cash_amount;
				$daycare_cash_amount_week+=$daycare_cash_amount;
				$each_day_cash+=$daycare_cash_amount;
				$each_day_cash_week+=$daycare_cash_amount;
				
				// Card
				$daycare_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$daycare_card_amount=$daycare_card["tot"];
				
				$daycare_total_amount+=$daycare_card_amount;
				$daycare_card_amount_week+=$daycare_card_amount;
				$each_day_card+=$daycare_card_amount;
				$each_day_card_week+=$daycare_card_amount;
				
				// Credit
				$daycare_credit_amount=0;
				$daycare_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($daycare_credit=mysqli_fetch_array($daycare_credit_qry))
				{
					$daycare_credit_amount+=$daycare_credit["balance"];
					
					// Check Same Day Balance Receive
					$daycare_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$daycare_credit[patient_id]' AND `ipd_id`='$daycare_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$daycare_sameday_balance_amount=$daycare_sameday_balance["tot"];
					
					$daycare_credit_amount-=$daycare_sameday_balance_amount;
				}
				$daycare_total_amount+=$daycare_credit_amount;
				$daycare_credit_amount_week+=$daycare_credit_amount;
				$each_day_credit+=$daycare_credit_amount;
				$each_day_credit_week+=$daycare_credit_amount;
				
			$each_day_total+=$daycare_total_amount;
			$each_day_total_week+=$daycare_total_amount;
			$daycare_total_amount_week+=$daycare_total_amount;
			
			// IPD
			$pat_visit_type=3;
			$ipd_total_amount=0;
				// Cash
				$ipd_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$ipd_cash_amount=$ipd_cash["tot"]-$ipd_cash["refund"];
				
				$ipd_total_amount+=$ipd_cash_amount;
				$ipd_cash_amount_week+=$ipd_cash_amount;
				$each_day_cash+=$ipd_cash_amount;
				$each_day_cash_week+=$ipd_cash_amount;
				
				// Card
				$ipd_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$ipd_card_amount=$ipd_card["tot"];
				
				$ipd_total_amount+=$ipd_card_amount;
				$ipd_card_amount_week+=$ipd_card_amount;
				$each_day_card+=$ipd_card_amount;
				$each_day_card_week+=$ipd_card_amount;
				
				// Credit
				$ipd_credit_amount=0;
				$ipd_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($ipd_credit=mysqli_fetch_array($ipd_credit_qry))
				{
					$ipd_credit_amount+=$ipd_credit["balance"];
					
					// Check Same Day Balance Receive
					$ipd_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_credit[patient_id]' AND `ipd_id`='$ipd_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$ipd_sameday_balance_amount=$ipd_sameday_balance["tot"];
					
					$ipd_credit_amount-=$ipd_sameday_balance_amount;
				}
				$ipd_total_amount+=$ipd_credit_amount;
				$ipd_credit_amount_week+=$ipd_credit_amount;
				$each_day_credit+=$ipd_credit_amount;
				$each_day_credit_week+=$ipd_credit_amount;
				
			$each_day_total+=$ipd_total_amount;
			$each_day_total_week+=$ipd_total_amount;
			$ipd_total_amount_week+=$ipd_total_amount;
			
			// Lab
			$pat_visit_type=2;
			$lab_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_lab_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_lab_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`!='$date') "));
				$non_cash_refund_amount_lab_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$lab_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$lab_cash_amount=$lab_cash["tot"]-$non_cash_refund_amount_lab_same_all-$non_cash_refund_amount_lab_same_ref;
				
				$lab_total_amount+=$lab_cash_amount;
				$lab_cash_amount_week+=$lab_cash_amount;
				$each_day_cash+=$lab_cash_amount;
				$each_day_cash_week+=$lab_cash_amount;
			
				// Card
				$lab_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$lab_card_amount=$lab_card["tot"]+$non_cash_refund_amount_lab_same_all+$non_cash_refund_amount_lab_same_reg;
				
				$lab_total_amount+=$lab_card_amount;
				$lab_card_amount_week+=$lab_card_amount;
				$each_day_card+=$lab_card_amount;
				$each_day_card_week+=$lab_card_amount;
				
				// Credit
				$lab_credit_amount=0;
				$lab_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($lab_credit=mysqli_fetch_array($lab_credit_qry))
				{
					$lab_credit_amount+=$lab_credit["balance"];
					
					// Check Same Day Balance Receive
					$lab_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$lab_credit[patient_id]' AND `opd_id`='$lab_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$lab_sameday_balance_amount=$lab_sameday_balance["tot"];
					
					$lab_credit_amount-=$lab_sameday_balance_amount;
				}
				
				$lab_total_amount+=$lab_credit_amount;
				$lab_credit_amount_week+=$lab_credit_amount;
				$each_day_credit+=$lab_credit_amount;
				$each_day_credit_week+=$lab_credit_amount;
			
			$each_day_total+=$lab_total_amount;
			$each_day_total_week+=$lab_total_amount;
			$lab_total_amount_week+=$lab_total_amount;
			
			// USG
			$pat_visit_type=10;
			$usg_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_usg_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_usg_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`!='$date') "));
				$non_cash_refund_amount_usg_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$usg_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$usg_cash_amount=$usg_cash["tot"]-$non_cash_refund_amount_usg_same_all-$non_cash_refund_amount_usg_same_ref;
				
				$usg_total_amount+=$usg_cash_amount;
				$usg_cash_amount_week+=$usg_cash_amount;
				$each_day_cash+=$usg_cash_amount;
				$each_day_cash_week+=$usg_cash_amount;
			
				// Card
				$usg_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$usg_card_amount=$usg_card["tot"]+$non_cash_refund_amount_usg_same_all+$non_cash_refund_amount_usg_same_reg;
				
				$usg_total_amount+=$usg_card_amount;
				$usg_card_amount_week+=$usg_card_amount;
				$each_day_card+=$usg_card_amount;
				$each_day_card_week+=$usg_card_amount;
				
				// Credit
				$usg_credit_amount=0;
				$usg_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($usg_credit=mysqli_fetch_array($usg_credit_qry))
				{
					$usg_credit_amount+=$usg_credit["balance"];
					
					// Check Same Day Balance Receive
					$usg_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$usg_credit[patient_id]' AND `opd_id`='$usg_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$usg_sameday_balance_amount=$usg_sameday_balance["tot"];
					
					$usg_credit_amount-=$usg_sameday_balance_amount;
				}
				
				$usg_total_amount+=$usg_credit_amount;
				$usg_credit_amount_week+=$usg_credit_amount;
				$each_day_credit+=$usg_credit_amount;
				$each_day_credit_week+=$usg_credit_amount;
			
			$each_day_total+=$usg_total_amount;
			$each_day_total_week+=$usg_total_amount;
			$usg_total_amount_week+=$usg_total_amount;
			
			// XRAY
			$pat_visit_type=11;
			$xray_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_xray_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_xray_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`!='$date') "));
				$non_cash_refund_amount_xray_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$xray_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$xray_cash_amount=$xray_cash["tot"]-$non_cash_refund_amount_xray_same_all-$non_cash_refund_amount_xray_same_ref;
				
				$xray_total_amount+=$xray_cash_amount;
				$xray_cash_amount_week+=$xray_cash_amount;
				$each_day_cash+=$xray_cash_amount;
				$each_day_cash_week+=$xray_cash_amount;
			
				// Card
				$xray_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$xray_card_amount=$xray_card["tot"]+$non_cash_refund_amount_xray_same_all+$non_cash_refund_amount_xray_same_reg;
				
				$xray_total_amount+=$xray_card_amount;
				$xray_card_amount_week+=$xray_card_amount;
				$each_day_card+=$xray_card_amount;
				$each_day_card_week+=$xray_card_amount;
				
				// Credit
				$xray_credit_amount=0;
				$xray_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($xray_credit=mysqli_fetch_array($xray_credit_qry))
				{
					$xray_credit_amount+=$xray_credit["balance"];
					
					// Check Same Day Balance Receive
					$xray_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$xray_credit[patient_id]' AND `opd_id`='$xray_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$xray_sameday_balance_amount=$xray_sameday_balance["tot"];
					
					$xray_credit_amount-=$xray_sameday_balance_amount;
				}
				
				$xray_total_amount+=$xray_credit_amount;
				$xray_credit_amount_week+=$xray_credit_amount;
				$each_day_credit+=$xray_credit_amount;
				$each_day_credit_week+=$xray_credit_amount;
			
			$each_day_total+=$xray_total_amount;
			$each_day_total_week+=$xray_total_amount;
			$xray_total_amount_week+=$xray_total_amount;
			
			// ECG
			$pat_visit_type=12;
			$ecg_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_ecg_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_ecg_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`!='$date') "));
				$non_cash_refund_amount_ecg_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$ecg_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$ecg_cash_amount=$ecg_cash["tot"]-$non_cash_refund_amount_ecg_same_all-$non_cash_refund_amount_ecg_same_ref;
				
				$ecg_total_amount+=$ecg_cash_amount;
				$ecg_cash_amount_week+=$ecg_cash_amount;
				$each_day_cash+=$ecg_cash_amount;
				$each_day_cash_week+=$ecg_cash_amount;
			
				// Card
				$ecg_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$ecg_card_amount=$ecg_card["tot"]+$non_cash_refund_amount_ecg_same_all+$non_cash_refund_amount_ecg_same_reg;
				
				$ecg_total_amount+=$ecg_card_amount;
				$ecg_card_amount_week+=$ecg_card_amount;
				$each_day_card+=$ecg_card_amount;
				$each_day_card_week+=$ecg_card_amount;
				
				// Credit
				$ecg_credit_amount=0;
				$ecg_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($ecg_credit=mysqli_fetch_array($ecg_credit_qry))
				{
					$ecg_credit_amount+=$ecg_credit["balance"];
					
					// Check Same Day Balance Receive
					$ecg_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$ecg_credit[patient_id]' AND `opd_id`='$ecg_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$ecg_sameday_balance_amount=$ecg_sameday_balance["tot"];
					
					$ecg_credit_amount-=$ecg_sameday_balance_amount;
				}
				
				$ecg_total_amount+=$ecg_credit_amount;
				$ecg_credit_amount_week+=$ecg_credit_amount;
				$each_day_credit+=$ecg_credit_amount;
				$each_day_credit_week+=$ecg_credit_amount;
			
			$each_day_total+=$ecg_total_amount;
			$each_day_total_week+=$ecg_total_amount;
			$ecg_total_amount_week+=$ecg_total_amount;
			
			// Endoscopy
			$pat_visit_type=13;
			$endo_total_amount=0;
				
				// Non Cash Refund
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_endo_same_all=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`!='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`='$date') "));
				$non_cash_refund_amount_endo_same_reg=$non_cash_refund["non_cash_refund"];
				
				$non_cash_refund=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(a.`refund_amount`),0) AS `non_cash_refund` FROM `invest_payment_refund` a, `invest_payment_detail` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND b.`payment_mode` IN(SELECT `p_mode_name` FROM `payment_mode_master` WHERE `ref_field`=0) AND a.`date`='$date' AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type' AND `date`!='$date') "));
				$non_cash_refund_amount_endo_same_ref=$non_cash_refund["non_cash_refund"];
				
				// Cash
				$endo_cash=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Cash' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$endo_cash_amount=$endo_cash["tot"]-$non_cash_refund_amount_endo_same_all-$non_cash_refund_amount_endo_same_ref;
				
				$endo_total_amount+=$endo_cash_amount;
				$endo_cash_amount_week+=$endo_cash_amount;
				$each_day_cash+=$endo_cash_amount;
				$each_day_cash_week+=$endo_cash_amount;
			
				// Card
				$endo_card=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `payment_mode`='Card' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') "));
				$endo_card_amount=$endo_card["tot"]+$non_cash_refund_amount_endo_same_all+$non_cash_refund_amount_endo_same_reg;
				
				$endo_total_amount+=$endo_card_amount;
				$endo_card_amount_week+=$endo_card_amount;
				$each_day_card+=$endo_card_amount;
				$each_day_card_week+=$endo_card_amount;
				
				// Credit
				$endo_credit_amount=0;
				$endo_credit_qry=mysqli_query($link," SELECT * FROM `invest_payment_detail` WHERE `payment_mode`='Credit' AND `date`='$date' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($endo_credit=mysqli_fetch_array($endo_credit_qry))
				{
					$endo_credit_amount+=$endo_credit["balance"];
					
					// Check Same Day Balance Receive
					$endo_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `invest_payment_detail` WHERE `patient_id`='$endo_credit[patient_id]' AND `opd_id`='$endo_credit[opd_id]' AND `typeofpayment`='B' AND `date`='$date' "));
					$endo_sameday_balance_amount=$endo_sameday_balance["tot"];
					
					$endo_credit_amount-=$endo_sameday_balance_amount;
				}
				
				$endo_total_amount+=$endo_credit_amount;
				$endo_credit_amount_week+=$endo_credit_amount;
				$each_day_credit+=$endo_credit_amount;
				$each_day_credit_week+=$endo_credit_amount;
			
			$each_day_total+=$endo_total_amount;
			$each_day_total_week+=$endo_total_amount;
			$endo_total_amount_week+=$endo_total_amount;
			
			// Dialysis
			$pat_visit_type=7;
			$dialysis_total_amount=0;
				// Cash
				$dialysis_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$dialysis_cash_amount=$dialysis_cash["tot"]-$dialysis_cash["refund"];
				
				$dialysis_total_amount+=$dialysis_cash_amount;
				$dialysis_cash_amount_week+=$dialysis_cash_amount;
				$each_day_cash+=$dialysis_cash_amount;
				$each_day_cash_week+=$dialysis_cash_amount;
				
				// Card
				$dialysis_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$dialysis_card_amount=$dialysis_card["tot"];
				
				$dialysis_total_amount+=$dialysis_card_amount;
				$dialysis_card_amount_week+=$dialysis_card_amount;
				$each_day_card+=$dialysis_card_amount;
				$each_day_card_week+=$dialysis_card_amount;
				
				// Credit
				$dialysis_credit_amount=0;
				$dialysis_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($dialysis_credit=mysqli_fetch_array($dialysis_credit_qry))
				{
					$dialysis_credit_amount+=$dialysis_credit["balance"];
					
					// Check Same Day Balance Receive
					$dialysis_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$dialysis_credit[patient_id]' AND `ipd_id`='$dialysis_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$dialysis_sameday_balance_amount=$dialysis_sameday_balance["tot"];
					
					$dialysis_credit_amount-=$dialysis_sameday_balance_amount;
				}
				$dialysis_total_amount+=$dialysis_credit_amount;
				$dialysis_credit_amount_week+=$dialysis_credit_amount;
				$each_day_credit+=$dialysis_credit_amount;
				$each_day_credit_week+=$dialysis_credit_amount;
				
			$each_day_total+=$dialysis_total_amount;
			$each_day_total_week+=$dialysis_total_amount;
			$dialysis_total_amount_week+=$dialysis_total_amount;
			
			// Emergency Room
			$pat_visit_type=4;
			$emergency_total_amount=0;
				// Cash
				$emergency_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$emergency_cash_amount=$emergency_cash["tot"]-$emergency_cash["refund"];
				
				$emergency_total_amount+=$emergency_cash_amount;
				$emergency_cash_amount_week+=$emergency_cash_amount;
				$each_day_cash+=$emergency_cash_amount;
				$each_day_cash_week+=$emergency_cash_amount;
				
				// Card
				$emergency_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$emergency_card_amount=$emergency_card["tot"];
				
				$emergency_total_amount+=$emergency_card_amount;
				$emergency_card_amount_week+=$emergency_card_amount;
				$each_day_card+=$emergency_card_amount;
				$each_day_card_week+=$emergency_card_amount;
				
				// Credit
				$emergency_credit_amount=0;
				$emergency_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($emergency_credit=mysqli_fetch_array($emergency_credit_qry))
				{
					$emergency_credit_amount+=$emergency_credit["balance"];
					
					// Check Same Day Balance Receive
					$emergency_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$emergency_credit[patient_id]' AND `ipd_id`='$emergency_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$emergency_sameday_balance_amount=$emergency_sameday_balance["tot"];
					
					$emergency_credit_amount-=$emergency_sameday_balance_amount;
				}
				$emergency_total_amount+=$emergency_credit_amount;
				$emergency_credit_amount_week+=$emergency_credit_amount;
				$each_day_credit+=$emergency_credit_amount;
				$each_day_credit_week+=$emergency_credit_amount;
				
			$each_day_total+=$emergency_total_amount;
			$each_day_total_week+=$emergency_total_amount;
			$emergency_total_amount_week+=$emergency_total_amount;
			
			// Dental Procedure
			$pat_visit_type=6;
			$denpro_total_amount=0;
				// Cash
				$denpro_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$denpro_cash_amount=$denpro_cash["tot"]-$denpro_cash["refund"];
				
				$denpro_total_amount+=$denpro_cash_amount;
				$denpro_cash_amount_week+=$denpro_cash_amount;
				$each_day_cash+=$denpro_cash_amount;
				$each_day_cash_week+=$denpro_cash_amount;
				
				// Card
				$denpro_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$denpro_card_amount=$denpro_card["tot"];
				
				$denpro_total_amount+=$denpro_card_amount;
				$denpro_card_amount_week+=$denpro_card_amount;
				$each_day_card+=$denpro_card_amount;
				$each_day_card_week+=$denpro_card_amount;
				
				// Credit
				$denpro_credit_amount=0;
				$denpro_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($denpro_credit=mysqli_fetch_array($denpro_credit_qry))
				{
					$denpro_credit_amount+=$denpro_credit["balance"];
					
					// Check Same Day Balance Receive
					$denpro_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$denpro_credit[patient_id]' AND `ipd_id`='$denpro_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$denpro_sameday_balance_amount=$denpro_sameday_balance["tot"];
					
					$denpro_credit_amount-=$denpro_sameday_balance_amount;
				}
				$denpro_total_amount+=$denpro_credit_amount;
				$denpro_credit_amount_week+=$denpro_credit_amount;
				$each_day_credit+=$denpro_credit_amount;
				$each_day_credit_week+=$denpro_credit_amount;
				
			$each_day_total+=$denpro_total_amount;
			$each_day_total_week+=$denpro_total_amount;
			$denpro_total_amount_week+=$denpro_total_amount;
			
			// MISCELLANEOUS
			$pat_visit_type=9;
			$misc_total_amount=0;
				// Cash
				$misc_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$misc_cash_amount=$misc_cash["tot"]-$misc_cash["refund"];
				
				$misc_total_amount+=$misc_cash_amount;
				$misc_cash_amount_week+=$misc_cash_amount;
				$each_day_cash+=$misc_cash_amount;
				$each_day_cash_week+=$misc_cash_amount;
				
				// Card
				$misc_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$misc_card_amount=$misc_card["tot"];
				
				$misc_total_amount+=$misc_card_amount;
				$misc_card_amount_week+=$misc_card_amount;
				$each_day_card+=$misc_card_amount;
				$each_day_card_week+=$misc_card_amount;
				
				// Credit
				$misc_credit_amount=0;
				$misc_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($misc_credit=mysqli_fetch_array($misc_credit_qry))
				{
					$misc_credit_amount+=$misc_credit["balance"];
					
					// Check Same Day Balance Receive
					$misc_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$misc_credit[patient_id]' AND `ipd_id`='$misc_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$misc_sameday_balance_amount=$misc_sameday_balance["tot"];
					
					$misc_credit_amount-=$misc_sameday_balance_amount;
				}
				$misc_total_amount+=$misc_credit_amount;
				$misc_credit_amount_week+=$misc_credit_amount;
				$each_day_credit+=$misc_credit_amount;
				$each_day_credit_week+=$misc_credit_amount;
				
			$each_day_total+=$misc_total_amount;
			$each_day_total_week+=$misc_total_amount;
			$misc_total_amount_week+=$misc_total_amount;
			
			// AMBULANCE
			$pat_visit_type=14;
			$ambulance_total_amount=0;
				// Cash
				$ambulance_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$ambulance_cash_amount=$ambulance_cash["tot"]-$ambulance_cash["refund"];
				
				$ambulance_total_amount+=$ambulance_cash_amount;
				$ambulance_cash_amount_week+=$ambulance_cash_amount;
				$each_day_cash+=$ambulance_cash_amount;
				$each_day_cash_week+=$ambulance_cash_amount;
				
				// Card
				$ambulance_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$ambulance_card_amount=$ambulance_card["tot"];
				
				$ambulance_total_amount+=$ambulance_card_amount;
				$ambulance_card_amount_week+=$ambulance_card_amount;
				$each_day_card+=$ambulance_card_amount;
				$each_day_card_week+=$ambulance_card_amount;
				
				// Credit
				$ambulance_credit_amount=0;
				$ambulance_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($ambulance_credit=mysqli_fetch_array($ambulance_credit_qry))
				{
					$ambulance_credit_amount+=$ambulance_credit["balance"];
					
					// Check Same Day Balance Receive
					$ambulance_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$ambulance_credit[patient_id]' AND `ipd_id`='$ambulance_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$ambulance_sameday_balance_amount=$ambulance_sameday_balance["tot"];
					
					$ambulance_credit_amount-=$ambulance_sameday_balance_amount;
				}
				$ambulance_total_amount+=$ambulance_credit_amount;
				$ambulance_credit_amount_week+=$ambulance_credit_amount;
				$each_day_credit+=$ambulance_credit_amount;
				$each_day_credit_week+=$ambulance_credit_amount;
				
			$each_day_total+=$ambulance_total_amount;
			$each_day_total_week+=$ambulance_total_amount;
			$ambulance_total_amount_week+=$ambulance_total_amount;
			
			// OTHER PROCEDURE
			$pat_visit_type=15;
			$otherpro_total_amount=0;
				// Cash
				$otherpro_cash=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot`, ifnull(SUM(`refund`),0) AS `refund` FROM `ipd_advance_payment_details` WHERE `pay_mode`='Cash' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$otherpro_cash_amount=$otherpro_cash["tot"]-$otherpro_cash["refund"];
				
				$otherpro_total_amount+=$otherpro_cash_amount;
				$otherpro_cash_amount_week+=$otherpro_cash_amount;
				$each_day_cash+=$otherpro_cash_amount;
				$each_day_cash_week+=$otherpro_cash_amount;
				
				// Card
				$otherpro_card=mysqli_fetch_array(mysqli_query($link,"SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE  `pay_mode`='Card' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type')"));
				$otherpro_card_amount=$otherpro_card["tot"];
				
				$otherpro_total_amount+=$otherpro_card_amount;
				$otherpro_card_amount_week+=$otherpro_card_amount;
				$each_day_card+=$otherpro_card_amount;
				$each_day_card_week+=$otherpro_card_amount;
				
				// Credit
				$otherpro_credit_amount=0;
				$otherpro_credit_qry=mysqli_query($link," SELECT * FROM `ipd_advance_payment_details` WHERE `pay_mode`='Credit' AND `date`='$date' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$pat_visit_type') ");
				while($otherpro_credit=mysqli_fetch_array($otherpro_credit_qry))
				{
					$otherpro_credit_amount+=$otherpro_credit["balance"];
					
					// Check Same Day Balance Receive
					$otherpro_sameday_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`amount`),0) AS `tot` FROM `ipd_advance_payment_details` WHERE `patient_id`='$otherpro_credit[patient_id]' AND `ipd_id`='$otherpro_credit[ipd_id]' AND `pay_type`='Balance' AND `date`='$date' "));
					$otherpro_sameday_balance_amount=$otherpro_sameday_balance["tot"];
					
					$otherpro_credit_amount-=$otherpro_sameday_balance_amount;
				}
				$otherpro_total_amount+=$otherpro_credit_amount;
				$otherpro_credit_amount_week+=$otherpro_credit_amount;
				$each_day_credit+=$otherpro_credit_amount;
				$each_day_credit_week+=$otherpro_credit_amount;
				
			$each_day_total+=$otherpro_total_amount;
			$each_day_total_week+=$otherpro_total_amount;
			$otherpro_total_amount_week+=$otherpro_total_amount;
			
?>
			<tr>
				<td><?php echo date("d/M", strtotime($date)); ?></td>
				<td><?php echo $day; ?></td>
				<td><?php echo money_view($opd_credit_amount); ?></td>
				<td><?php echo money_view($opd_cash_amount); ?></td>
				<td><?php echo money_view($opd_card_amount); ?></td>
				<th><?php echo money_view($opd_total_amount); ?></th>
				<td><?php echo money_view($emergency_credit_amount); ?></td>
				<td><?php echo money_view($emergency_cash_amount); ?></td>
				<td><?php echo money_view($emergency_card_amount); ?></td>
				<th><?php echo money_view($emergency_total_amount); ?></th>
				<td><?php echo money_view($daycare_credit_amount); ?></td>
				<td><?php echo money_view($daycare_cash_amount); ?></td>
				<td><?php echo money_view($daycare_card_amount); ?></td>
				<th><?php echo money_view($daycare_total_amount); ?></th>
				<td><?php echo money_view($ipd_credit_amount); ?></td>
				<td><?php echo money_view($ipd_cash_amount); ?></td>
				<td><?php echo money_view($ipd_card_amount); ?></td>
				<th><?php echo money_view($ipd_total_amount); ?></th>
				<td><?php echo money_view(0); ?></td>
				<td><?php echo money_view(0); ?></td>
				<td><?php echo money_view(0); ?></td>
				<th><?php echo money_view(0); ?></th>
				<td><?php echo money_view($lab_credit_amount); ?></td>
				<td><?php echo money_view($lab_cash_amount); ?></td>
				<td><?php echo money_view($lab_card_amount); ?></td>
				<th><?php echo money_view($lab_total_amount); ?></th>
				<td><?php echo money_view($usg_credit_amount); ?></td>
				<td><?php echo money_view($usg_cash_amount); ?></td>
				<td><?php echo money_view($usg_card_amount); ?></td>
				<th><?php echo money_view($usg_total_amount); ?></th>
				<td><?php echo money_view($xray_credit_amount); ?></td>
				<td><?php echo money_view($xray_cash_amount); ?></td>
				<td><?php echo money_view($xray_card_amount); ?></td>
				<th><?php echo money_view($xray_total_amount); ?></th>
				<td><?php echo money_view($endo_credit_amount); ?></td>
				<td><?php echo money_view($endo_cash_amount); ?></td>
				<td><?php echo money_view($endo_card_amount); ?></td>
				<th><?php echo money_view($endo_total_amount); ?></th>
				<td><?php echo money_view($ecg_credit_amount); ?></td>
				<td><?php echo money_view($ecg_cash_amount); ?></td>
				<td><?php echo money_view($ecg_card_amount); ?></td>
				<th><?php echo money_view($ecg_total_amount); ?></th>
				<td><?php echo money_view($dialysis_credit_amount); ?></td>
				<td><?php echo money_view($dialysis_cash_amount); ?></td>
				<td><?php echo money_view($dialysis_card_amount); ?></td>
				<th><?php echo money_view($dialysis_total_amount); ?></th>
				<td><?php echo money_view($ambulance_credit_amount); ?></td>
				<td><?php echo money_view($ambulance_cash_amount); ?></td>
				<td><?php echo money_view($ambulance_card_amount); ?></td>
				<th><?php echo money_view($ambulance_total_amount); ?></th>
				<td><?php echo money_view($denpro_credit_amount); ?></td>
				<td><?php echo money_view($denpro_cash_amount); ?></td>
				<td><?php echo money_view($denpro_card_amount); ?></td>
				<th><?php echo money_view($denpro_total_amount); ?></th>
				<td><?php echo money_view($otherpro_credit_amount); ?></td>
				<td><?php echo money_view($otherpro_cash_amount); ?></td>
				<td><?php echo money_view($otherpro_card_amount); ?></td>
				<th><?php echo money_view($otherpro_total_amount); ?></th>
				<td><?php echo money_view($misc_credit_amount); ?></td>
				<td><?php echo money_view($misc_cash_amount); ?></td>
				<td><?php echo money_view($misc_card_amount); ?></td>
				<th><?php echo money_view($misc_total_amount); ?></th>
				<td><?php echo money_view($each_day_credit); ?></td>
				<td><?php echo money_view($each_day_cash); ?></td>
				<td><?php echo money_view($each_day_card); ?></td>
				<th><?php echo money_view($each_day_total); ?></th>
			</tr>
<?php
			if($day=="Sun" || $last_day_month==$date)
			{
?>
			<tr>
				<th>Total</th>
				<th></th>
				<td><?php echo money_view($opd_credit_amount_week); ?></td>
				<td><?php echo money_view($opd_cash_amount_week); ?></td>
				<td><?php echo money_view($opd_card_amount_week); ?></td>
				<th><?php echo money_view($opd_total_amount_week); ?></th>
				<td><?php echo money_view($emergency_credit_amount_week); ?></td>
				<td><?php echo money_view($emergency_cash_amount_week); ?></td>
				<td><?php echo money_view($emergency_card_amount_week); ?></td>
				<th><?php echo money_view($emergency_total_amount_week); ?></th>
				<td><?php echo money_view($daycare_credit_amount_week); ?></td>
				<td><?php echo money_view($daycare_cash_amount_week); ?></td>
				<td><?php echo money_view($daycare_card_amount_week); ?></td>
				<th><?php echo money_view($daycare_total_amount_week); ?></th>
				<td><?php echo money_view($ipd_credit_amount_week); ?></td>
				<td><?php echo money_view($ipd_cash_amount_week); ?></td>
				<td><?php echo money_view($ipd_card_amount_week); ?></td>
				<th><?php echo money_view($ipd_total_amount_week); ?></th>
				<th><?php echo money_view(0); ?></th>
				<th><?php echo money_view(0); ?></th>
				<th><?php echo money_view(0); ?></th>
				<th><?php echo money_view(0); ?></th>
				<td><?php echo money_view($lab_credit_amount_week); ?></td>
				<td><?php echo money_view($lab_cash_amount_week); ?></td>
				<td><?php echo money_view($lab_card_amount_week); ?></td>
				<th><?php echo money_view($lab_total_amount_week); ?></th>
				<td><?php echo money_view($usg_credit_amount_week); ?></td>
				<td><?php echo money_view($usg_cash_amount_week); ?></td>
				<td><?php echo money_view($usg_card_amount_week); ?></td>
				<th><?php echo money_view($usg_total_amount_week); ?></th>
				<td><?php echo money_view($xray_credit_amount_week); ?></td>
				<td><?php echo money_view($xray_cash_amount_week); ?></td>
				<td><?php echo money_view($xray_card_amount_week); ?></td>
				<th><?php echo money_view($xray_total_amount_week); ?></th>
				<td><?php echo money_view($endo_credit_amount_week); ?></td>
				<td><?php echo money_view($endo_cash_amount_week); ?></td>
				<td><?php echo money_view($endo_card_amount_week); ?></td>
				<th><?php echo money_view($endo_total_amount_week); ?></th>
				<td><?php echo money_view($ecg_credit_amount_week); ?></td>
				<td><?php echo money_view($ecg_cash_amount_week); ?></td>
				<td><?php echo money_view($ecg_card_amount_week); ?></td>
				<th><?php echo money_view($ecg_total_amount_week); ?></th>
				<td><?php echo money_view($dialysis_credit_amount_week); ?></td>
				<td><?php echo money_view($dialysis_cash_amount_week); ?></td>
				<td><?php echo money_view($dialysis_card_amount_week); ?></td>
				<th><?php echo money_view($dialysis_total_amount_week); ?></th>
				<td><?php echo money_view($ambulance_credit_amount_week); ?></td>
				<td><?php echo money_view($ambulance_cash_amount_week); ?></td>
				<td><?php echo money_view($ambulance_card_amount_week); ?></td>
				<th><?php echo money_view($ambulance_total_amount_week); ?></th>
				<td><?php echo money_view($denpro_credit_amount_week); ?></td>
				<td><?php echo money_view($denpro_cash_amount_week); ?></td>
				<td><?php echo money_view($denpro_card_amount_week); ?></td>
				<th><?php echo money_view($denpro_total_amount_week); ?></th>
				<td><?php echo money_view($otherpro_credit_amount_week); ?></td>
				<td><?php echo money_view($otherpro_cash_amount_week); ?></td>
				<td><?php echo money_view($otherpro_card_amount_week); ?></td>
				<th><?php echo money_view($otherpro_total_amount_week); ?></th>
				<td><?php echo money_view($misc_credit_amount_week); ?></td>
				<td><?php echo money_view($misc_cash_amount_week); ?></td>
				<td><?php echo money_view($misc_card_amount_week); ?></td>
				<th><?php echo money_view($misc_total_amount_week); ?></th>
				<td><?php echo money_view($each_day_credit_week); ?></td>
				<td><?php echo money_view($each_day_cash_week); ?></td>
				<td><?php echo money_view($each_day_card_week); ?></td>
				<th><?php echo money_view($each_day_total_week); ?></th>
			</tr>
<?php
				$opd_cash_amount_week=$opd_card_amount_week=$opd_credit_amount_week=$opd_total_amount_week=0;
				$daycare_cash_amount_week=$daycare_card_amount_week=$daycare_credit_amount_week=$daycare_total_amount_week=0;
				$ipd_cash_amount_week=$ipd_card_amount_week=$ipd_credit_amount_week=$ipd_total_amount_week=0;
				$lab_cash_amount_week=$lab_card_amount_week=$lab_credit_amount_week=$lab_total_amount_week=0;
				$usg_cash_amount_week=$usg_card_amount_week=$usg_credit_amount_week=$usg_total_amount_week=0;
				$xray_cash_amount_week=$xray_card_amount_week=$xray_credit_amount_week=$xray_total_amount_week=0;
				$ecg_cash_amount_week=$ecg_card_amount_week=$ecg_credit_amount_week=$ecg_total_amount_week=0;
				$endo_cash_amount_week=$endo_card_amount_week=$endo_credit_amount_week=$endo_total_amount_week=0;
				$dialysis_cash_amount_week=$dialysis_card_amount_week=$dialysis_credit_amount_week=$dialysis_total_amount_week=0;
				$emergency__cash_amount_week=$emergency__card_amount_week=$emergency__credit_amount_week=$emergency__total_amount_week=0;
				$denpro_cash_amount_week=$denpro_card_amount_week=$denpro_credit_amount_week=$denpro_total_amount_week=0;
				$misc_cash_amount_week=$misc_card_amount_week=$misc_credit_amount_week=$misc_total_amount_week=0;
				$ambulance_cash_amount_week=$ambulance_card_amount_week=$ambulance_credit_amount_week=$ambulance_total_amount_week=0;
				$otherpro_cash_amount_week=$otherpro_card_amount_week=$otherpro_credit_amount_week=$otherpro_total_amount_week=0;
				$each_day_credit_week=$each_day_cash_week=$each_day_card_week=$each_day_total_week=0;
			}
			$i++;
		}
	}
?>
</table>

