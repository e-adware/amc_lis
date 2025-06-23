<?php
session_start();
include("../../includes/connection.php");

// Function that gives days between including two dates
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	foreach($period as $date) { 
		$array[] = $date->format($format); 
	}

	return $array;
}


$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_POST['date1'];
$date2=$_POST['date2'];

$date1 = date("Y-m");

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}

$rupees_symbol="&#x20b9; ";

if($_POST["type"]=="1")
{
	$ipd_new_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' and '$date2' AND `type`='3' "));
	
	$ipd_discharge_pat_num=mysqli_num_rows(mysqli_query($link, " SELECT * FROM `ipd_pat_discharge_details` WHERE `date` between '$date1' and '$date2' "));
	
	$inpatient_number=0;
	$inpatient_str="";
	
	$dates = getDatesFromRange($date1,$date2);
	foreach($dates as $date)
	{
		if($date)
		{
			$ipd_admit_pat_qry=mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ");
			while($ipd_admit_pat=mysqli_fetch_array($ipd_admit_pat_qry))
			{
				if (strpos($inpatient_str, $ipd_admit_pat["ipd_id"]) !== false)
				{
					
				}
				else
				{
					$inpatient_str.=$ipd_admit_pat["ipd_id"]."@";
					
					$inpatient_number++;
				}
			}
		}
	}
	
	//echo $inpatient_str;
	
	$total_ipd_pat_num=$inpatient_number+$ipd_new_pat_num+$ipd_discharge_pat_num;
	//~ $total_ipd_pat_num=$ipd_new_pat_num+$ipd_discharge_pat_num;
	if($total_ipd_pat_num==0)
	{
		$total_ipd_pat_num=20;
	}
	
	echo $inpatient_number."@".$ipd_new_pat_num."@".$ipd_discharge_pat_num."@".$total_ipd_pat_num;
}

if($_POST["type"]=="2")
{
	$ipd_refund_amount=0;
	$ipd_discount_amount=0;
	$ipd_bill_amount=0;
	$ipd_paid_amount=0;
	$ipd_balance_amount=0;
	
	$ipd_pay_det=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`discount`),0) AS `tot_discount`, ifnull(SUM(a.`amount`),0) AS `tot_amount`, ifnull(SUM(a.`refund`),0) AS `tot_refund` FROM `ipd_advance_payment_details` a,`uhid_and_opdid` b  WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' "));
	
	$ipd_refund_amount=$ipd_pay_det["tot_refund"];
	$ipd_discount_amount=$ipd_pay_det["tot_discount"];
	$ipd_paid_amount=$ipd_pay_det["tot_amount"];
	
	$qry=" SELECT DISTINCT a.`patient_id`,a.`ipd_id` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND b.`type`='3' AND a.`date` between '$date1' and '$date2' AND a.`pay_type`='Final' ";
	
	$final_pat_qry=mysqli_query($link, $qry);
	while($final_pat=mysqli_fetch_array($final_pat_qry))
	{
		$uhid=$final_pat['patient_id'];
		$ipd=$final_pat['ipd_id'];
		
		//~ $pat_reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` WHERE patient_id='$uhid' and opd_id='$ipd' "));
		
		//~ $baby_serv_tot=0;
		//~ $baby_ot_total=0;
		//~ $delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
		//~ while($delivery_check=mysqli_fetch_array($delivery_qry))
		//~ {
			//~ $baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			//~ $baby_serv_tot+=$baby_tot_serv["tots"];
			
			//~ // OT Charge Baby
			//~ $baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
			//~ $baby_ot_total+=$baby_ot_tot_val["g_tot"];
			
		//~ }
		
		//~ $no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
		//~ $no_of_days=$no_of_days_val["ser_quantity"];
		
		//~ $tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
		//~ $tot_serv_amt1=$tot_serv1["tots"];
		//~ //$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
		
		//~ $tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
		//~ $tot_serv_amt2=$tot_serv2["tots"];
		
		//~ $ot_total=0;
		//~ if($pat_reg["type"]==3) // If Caualty or day care and has entry ot, skip ot
		//~ {
			//~ // OT Charge
			//~ $ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
			//~ $ot_total=$ot_tot_val["g_tot"];
		//~ }
		//~ // Total Amount
		//~ $tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
		
		//~ $ipd_bill_amount+=$tot_serv_amt;
		
		// Balance
		$pat_balance=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`bal_amount`),0) as `tot_bal` FROM `ipd_discharge_balance_pat` WHERE patient_id='$uhid' and ipd_id='$ipd' "));
		
		$ipd_balance_amount+=$pat_balance["tot_bal"];
	}
	
	echo $ipd_refund_amount."@".$ipd_discount_amount."@".$ipd_bill_amount."@".$ipd_paid_amount."@".$ipd_balance_amount;
}

if($_POST["type"]=="3")
{
	$opd_refund_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(a.refund_amount),0) as maxref_opd from invest_payment_refund a , uhid_and_opdid b where a.`date` between '$date1' and '$date2' and a.`patient_id`=b.`patient_id` and a.`opd_id`=b.`opd_id` and b.type='1' "));
	$opd_refund_amount=$opd_refund_val["maxref_opd"];
	
	$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill`, ifnull(SUM(`dis_amt`),0) AS `tot_dis`, ifnull(SUM(`advance`),0) AS `tot_adv`, ifnull(SUM(`balance`),0) AS `tot_bal` FROM `consult_patient_payment_details` WHERE `date` between '$date1' AND '$date2' "));
	$opd_bill_amount=$opd_val["tot_bill"];
	$opd_discount_amount=$opd_val["tot_dis"];
	$opd_paid_amount=$opd_val["tot_adv"];
	$opd_balance_amount=$opd_val["tot_bal"];
	
	echo $opd_refund_amount."@".$opd_discount_amount."@".$opd_paid_amount."@".$opd_balance_amount."@".$opd_bill_amount;
}

if($_POST["type"]=="4")
{
	$daycare_pat=0;
	$female_ward_pat=0;
	$male_ward_pat=0;
	$special_cabin_pat=0;
	$icu_pat=0;
	
	$daycare_pat=mysqli_num_rows(mysqli_query($link,"SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` between '$date1' AND '$date2' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='5')"));
	
	$inpatient_str="";
	$dates = getDatesFromRange($date1,$date2);
	foreach($dates as $date)
	{
		if($date)
		{
			$ipd_admit_pat_qry=mysqli_query($link, " SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `date`<='$date' AND `ipd_id` NOT IN(SELECT `ipd_id` FROM `ipd_pat_discharge_details` WHERE `date`<='$date') ");
			while($ipd_admit_pat=mysqli_fetch_array($ipd_admit_pat_qry))
			{
				if (strpos($inpatient_str, $ipd_admit_pat["ipd_id"]) !== false)
				{
				}
				else
				{
					$inpatient_str.=$ipd_admit_pat["ipd_id"]."@";
					
					$female_ward=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id`='$ipd_admit_pat[ipd_id]' AND `ward_id`='1' "));
					$female_ward_pat+=$female_ward;
					
					$male_ward=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id`='$ipd_admit_pat[ipd_id]' AND `ward_id`='2' "));
					$male_ward_pat+=$male_ward;
					
					$special_cabin=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id`='$ipd_admit_pat[ipd_id]' AND `ward_id`='3' "));
					$special_cabin_pat+=$special_cabin;
					
					$icu=mysqli_num_rows(mysqli_query($link," SELECT DISTINCT `ipd_id` FROM `ipd_bed_alloc_details` WHERE `ipd_id`='$ipd_admit_pat[ipd_id]' AND `ward_id`='4' "));
					$icu_pat+=$icu;
				}
			}
		}
	}
	
	
	echo $daycare_pat."@".$female_ward_pat."@".$male_ward_pat."@".$special_cabin_pat."@".$icu_pat;
	
	//~ echo "DAYCARE##".$daycare."@#@FEMALE WARD##".$female_ward."@#@MALE WARD##".$male_ward."@#@SPECIAL CABIN##".$special_cabin."@#@ICU##".$icu;
	//~ echo "DAYCARE##".$daycare;
}

if($_POST["type"]=="5")
{
	$last_12_month_val="";
	
	// Last 12 Months
	for ($i = 11; $i >= 0; $i--) 
	{
	   $months[] = date("Y-m", strtotime( $date1." -$i months"));
	}
	
	foreach($months AS $month)
	{
		if($month)
		{
			$month_value=0;
			
			$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` WHERE `date` LIKE '$month%' "));
			$month_value=$opd_val["tot_bill"];
			
			
			$month_name=date("F", strtotime($month));
			
			$last_12_month_val.=$month_name."@".$month_value."@#@";
		}
	}
	echo $last_12_month_val;
}

if($_POST["type"]=="6")
{
	$last_12_month_val="";
	
	// Last 12 Months
	for ($i = 11; $i >= 0; $i--) 
	{
	   $months[] = date("Y-m", strtotime( $date1." -$i months"));
	}
	
	foreach($months AS $month)
	{
		if($month)
		{
			$month_value=0;
			
			$paid_pat_qry=mysqli_query($link,"SELECT DISTINCT `patient_id`,`ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` LIKE '$month%' AND `ipd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='3')");
			while($paid_pat=mysqli_fetch_array($paid_pat_qry))
			{
				$uhid=$paid_pat['patient_id'];
				$ipd=$paid_pat['ipd_id'];
				
				$baby_serv_tot=0;
				$baby_ot_total=0;
				$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$uhid' and ipd_id='$ipd' ");
				while($delivery_check=mysqli_fetch_array($delivery_qry))
				{
					$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot+=$baby_tot_serv["tots"];
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total+=$baby_ot_tot_val["g_tot"];
					
				}
				
				$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' and `group_id`='141' "));
				$no_of_days=$no_of_days_val["ser_quantity"];
				
				$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id='141' "));
				$tot_serv_amt1=$tot_serv1["tots"];
				//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
				
				$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$uhid' and ipd_id='$ipd' and group_id!='141' "));
				$tot_serv_amt2=$tot_serv2["tots"];
				
				$ot_total=0;
				
				// OT Charge
				$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$uhid' and ipd_id='$ipd' "));
				$ot_total=$ot_tot_val["g_tot"];
				
				// Total Amount
				$tot_serv_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
				
				$month_value+=$tot_serv_amt;
				
			}
			
			$month_name=date("F", strtotime($month));
			
			$last_12_month_val.=$month_name."@".$month_value."@#@";
		}
	}
	echo $last_12_month_val;
}

if($_POST["type"]=="7")
{
	$last_12_month_val="";
	
	// Last 12 Months
	for ($i = 11; $i >= 0; $i--) 
	{
	   $months[] = date("Y-m", strtotime( $date1." -$i months"));
	}
	
	foreach($months AS $month)
	{
		if($month)
		{
			$month_value=0;
			
			$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` WHERE `date` LIKE '$month%' "));
			$month_value+=$opd_val["tot_bill"];
			
			$inv_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `invest_patient_payment_details` WHERE `date` LIKE '$month%' "));
			$month_value+=$inv_val["tot_bill"];
			
			// IPD
			$ipd_ser_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` LIKE '$month%') "));
			$ipd_ser_amount=$ipd_ser_val["ipd_ser"];
			
			$month_value+=$ipd_ser_amount;
			
			$ipd_ot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ot_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` LIKE '$month%') "));
			$ipd_ot_amount=$ipd_ot_val["ipd_ser"];
			
			$month_value+=$ipd_ot_amount;
			
			
			$ipd_baby_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT a.`baby_ipd_id` FROM `ipd_pat_delivery_det` a, `ipd_advance_payment_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND b.`pay_type`='Final' AND b.`date` LIKE '$month%') "));
			$ipd_baby_amount=$ipd_baby_val["ipd_ser"];
			
			$month_value+=$ipd_baby_amount;
			
			$month_name=date("F", strtotime($month));
			
			$last_12_month_val.=$month_name."@".$month_value."@#@";
		}
	}
	echo $last_12_month_val;
}

if($_POST["type"]=="8")
{
	
	$min=0;
	
	$max=5000000;
	
	$month=date("Y-m", strtotime($date1));
	$month_year_str=date("F Y", strtotime($date1));
		
		// Bill Amount Monthly
		$month_value=0;
				
		$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` WHERE `date` LIKE '$month%' "));
		$month_value+=$opd_val["tot_bill"];
		
		$inv_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `invest_patient_payment_details` WHERE `date` LIKE '$month%' "));
		$month_value+=$inv_val["tot_bill"];
		
		// IPD
		$ipd_ser_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` LIKE '$month%') "));
		$ipd_ser_amount=$ipd_ser_val["ipd_ser"];
		
		$month_value+=$ipd_ser_amount;
		
		$ipd_ot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ot_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` LIKE '$month%') "));
		$ipd_ot_amount=$ipd_ot_val["ipd_ser"];
		
		$month_value+=$ipd_ot_amount;
		
		
		$ipd_baby_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT a.`baby_ipd_id` FROM `ipd_pat_delivery_det` a, `ipd_advance_payment_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND b.`pay_type`='Final' AND b.`date` LIKE '$month%') "));
		$ipd_baby_amount=$ipd_baby_val["ipd_ser"];
		
		$month_value+=$ipd_baby_amount;
	
	echo $month_value."@#@".$min."@#@".$max."@#@".$month_year_str;
}


if($_POST["type"]=="9") // Weekly
{
	$last_12_week_val="";
	$last_12_week_str="";
	
	// Last 12 Weeks
	
	for ($i = 11; $i >= 0; $i--) 
	{
		$previous_week = strtotime("-$i week +1 day");

		$start_week = strtotime("last sunday midnight",$previous_week);
		$end_week = strtotime("next saturday",$start_week);

		$start_week = date("Y-m-d",$start_week);
		$end_week = date("Y-m-d",$end_week);

		$last_12_week_str.=$start_week.'#'.$end_week.'@';

	}
	
	$last_12_weeks=explode("@", $last_12_week_str);
	
	foreach($last_12_weeks AS $week)
	{
		if($week)
		{
			$week=explode("#", $week);
			$date1=$week[0];
			$date2=$week[1];
			
			$week_value=0;
			
			$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` WHERE `date` between '$date1' and '$date2' "));
			$week_value+=$opd_val["tot_bill"];
			
			$inv_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `invest_patient_payment_details` WHERE `date` between '$date1' and '$date2' "));
			$week_value+=$inv_val["tot_bill"];
			
			// IPD
			$ipd_ser_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` between '$date1' and '$date2') "));
			$ipd_ser_amount=$ipd_ser_val["ipd_ser"];
			
			$week_value+=$ipd_ser_amount;
			
			$ipd_ot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ot_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date` between '$date1' and '$date2') "));
			$ipd_ot_amount=$ipd_ot_val["ipd_ser"];
			
			$week_value+=$ipd_ot_amount;
			
			
			$ipd_baby_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT a.`baby_ipd_id` FROM `ipd_pat_delivery_det` a, `ipd_advance_payment_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND b.`pay_type`='Final' AND b.`date` between '$date1' and '$date2') "));
			$ipd_baby_amount=$ipd_baby_val["ipd_ser"];
			
			$week_value+=$ipd_baby_amount;
			
			$week_name=date("d-M", strtotime($date1))." ".date("d-M", strtotime($date2));
			
			$last_12_week_val.=$week_name."@".$week_value."@#@";
		}
	}
	echo $last_12_week_val;
}

if($_POST["type"]=="10") // Daily
{
	$last_12_day_val="";
	$last_12_day_str="";
	
	// Last 12 Days
	
	$date = date("Y-m-d");
	for ($i = 11; $i >= 0; $i--) 
	{
		$date1 = date( 'Y-m-d', strtotime( $date .  -$i.' day' ) );

		$last_12_day_str.=$date1.'@';

	}
	
	$last_12_days=explode("@", $last_12_day_str);
	
	foreach($last_12_days AS $date)
	{
		if($date)
		{
			
			$day_value=0;
			
			$opd_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `consult_patient_payment_details` WHERE `date`='$date' "));
			$week_value+=$opd_val["tot_bill"];
			
			$inv_val=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(SUM(`tot_amount`),0) AS `tot_bill` FROM `invest_patient_payment_details` WHERE `date`='$date' "));
			$day_value+=$inv_val["tot_bill"];
			
			// IPD
			$ipd_ser_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date`='$date') "));
			$ipd_ser_amount=$ipd_ser_val["ipd_ser"];
			
			$day_value+=$ipd_ser_amount;
			
			$ipd_ot_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ot_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT `ipd_id` FROM `ipd_advance_payment_details` WHERE `pay_type`='Final' AND `date`='$date') "));
			$ipd_ot_amount=$ipd_ot_val["ipd_ser"];
			
			$day_value+=$ipd_ot_amount;
			
			
			$ipd_baby_val=mysqli_fetch_array(mysqli_query($link," SELECT SUM(`amount`) AS `ipd_ser` FROM `ipd_pat_service_details` WHERE `ipd_id` IN(SELECT DISTINCT a.`baby_ipd_id` FROM `ipd_pat_delivery_det` a, `ipd_advance_payment_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`ipd_id` AND b.`pay_type`='Final' AND b.`date`='$date') "));
			$ipd_baby_amount=$ipd_baby_val["ipd_ser"];
			
			$day_value+=$ipd_baby_amount;
			
			$day_name=date("d-M", strtotime($date));
			
			$last_12_week_val.=$day_name."@".$day_value."@#@";
		}
	}
	echo $last_12_week_val;
}
?>
