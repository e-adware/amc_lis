<?php
include("../../includes/connection.php");

$date1=$_POST['date1'];
$date2=$_POST['date2'];

// important
$date11=$_POST['date1'];
$date22=$_POST['date2'];

// Date format convert
function convert_date($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('d M Y', $timestamp);
	return $new_date;
}

if($_POST["type"]=="account_summary")
{
	// Close account
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$date2' "));	
	if($check_close_account_today)
	{
		$con_max_slno_less=$check_close_account_today['con_slno'];
		$con_max_slno_str_less=" AND `slno`<=$con_max_slno_less ";
		
		$inv_max_slno_less=$check_close_account_today['inv_slno'];
		$inv_max_slno_str_less=" AND `slno`<=$inv_max_slno_less ";
		
		$ipd_max_slno_less=$check_close_account_today['ipd_slno'];
		$ipd_max_slno_str_less=" AND `slno`<=$ipd_max_slno_less ";
	}
	else
	{
		$con_max_slno_str_less="";
		$inv_max_slno_str_less="";
		$ipd_max_slno_str_less="";
	}
	
	$last_date=date('Y-m-d', strtotime($date1. ' - 1 days'));
	$check_close_account_today=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `daily_account_close` WHERE `close_date`='$last_date' "));	
	if($check_close_account_today)
	{
		$date1=date('Y-m-d', strtotime($date1. ' - 1 days'));
		
		$con_max_slno_grtr=$check_close_account_today['con_slno'];
		$con_max_slno_str_grtr=" AND `slno`>$con_max_slno_grtr ";
		
		$inv_max_slno_grtr=$check_close_account_today['inv_slno'];
		$inv_max_slno_str_grtr=" AND `slno`>$inv_max_slno_grtr ";
		
		$ipd_max_slno_grtr=$check_close_account_today['ipd_slno'];
		$ipd_max_slno_str_grtr=" AND `slno`>$ipd_max_slno_grtr ";
	}
	else
	{
		$con_max_slno_str_grtr="";
		$inv_max_slno_str_grtr="";
		$ipd_max_slno_str_grtr="";
	}
	
	// OPD
	$i=1;
	$con_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `consult_payment_detail` WHERE `date` between '$date1' and '$date2' $con_max_slno_str_less $con_max_slno_str_grtr ORDER BY `slno`");
	while($con_pay=mysqli_fetch_array($con_pay_qry))
	{
		$opd_pin[$i]=$con_pay["opd_id"];
		
		if($i==1)
		{
			$vbllfrom=$con_pay['opd_id'];
		}
		$vbllto=$con_pay['opd_id'];
		if($vbllfrom!=$vbllto)
		{
		  $vbllto=$con_pay['opd_id'];
		}
		
		$i++;
	}
	sort($opd_pin);
	//print_r($opd_pin);
	$opd_first_amt=$opd_revisit_amt=$opd_discount_amt=0;
	foreach($opd_pin as $opd_pin)
	{
		if($opd_pin)
		{
			$opd_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$opd_pin' "));
			
			$opd_visit_type_check=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$opd_uhid[patient_id]' AND `slno`<$opd_uhid[slno] "));
			if($opd_visit_type_check)
			{
				// Re-visit
				$opd_amt=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `consult_payment_detail` WHERE `patient_id`='$opd_uhid[patient_id]' AND `opd_id`='$opd_pin' AND `date` between '$date1' and '$date2' "));
				$opd_revisit_amt+=$opd_amt["amount"];
				
				$opd_dis=mysqli_fetch_array(mysqli_query($link, " SELECT `dis_amt` FROM `consult_patient_payment_details` WHERE `patient_id`='$opd_uhid[patient_id]' AND `opd_id`='$opd_pin' AND `date` between '$date1' and '$date2' "));
				$opd_discount_amt+=$opd_dis["dis_amt"];
				
			}else
			{
				// First Visit
				$opd_amt=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `consult_payment_detail` WHERE `patient_id`='$opd_uhid[patient_id]' AND `opd_id`='$opd_pin' AND `date` between '$date1' and '$date2' "));
				$opd_first_amt+=$opd_amt["amount"];
				
				$opd_dis=mysqli_fetch_array(mysqli_query($link, " SELECT `dis_amt` FROM `consult_patient_payment_details` WHERE `patient_id`='$opd_uhid[patient_id]' AND `opd_id`='$opd_pin' AND `date` between '$date1' and '$date2' "));
				$opd_discount_amt+=$opd_dis["dis_amt"];
			}
		}
	}
	
	// LAB
	$i=1;
	$inv_pay_qry=mysqli_query($link, " SELECT distinct `opd_id` FROM `invest_payment_detail` WHERE `date` between '$date1' and '$date2' $inv_max_slno_str_less $inv_max_slno_str_grtr ORDER BY `slno`");
	while($inv_pay=mysqli_fetch_array($inv_pay_qry))
	{
		$lab_pin[$i]=$inv_pay["opd_id"];
		$i++;
	}
	sort($lab_pin);
	//print_r($lab_pin);
	$lab_opd_amt=$lab_ipd_amt=$lab_discount_amt=0;
	foreach($lab_pin as $lab_pin)
	{
		if($lab_pin)
		{
			$lab_uhid=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `opd_id`='$lab_pin' "));
			
			if($lab_uhid["type"]==2)
			{
				$lab_amt=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `invest_payment_detail` WHERE `patient_id`='$lab_uhid[patient_id]' AND `opd_id`='$lab_pin' AND `date` between '$date1' and '$date2' "));
				$lab_opd_amt+=$lab_amt["amount"];
				
				$lab_dis=mysqli_fetch_array(mysqli_query($link, " SELECT `dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$lab_uhid[patient_id]' AND `opd_id`='$lab_pin' AND `date` between '$date1' and '$date2' "));
				$lab_discount_amt+=$lab_dis["dis_amt"];
			}
			if($lab_uhid["type"]==5)
			{
				$lab_amt=mysqli_fetch_array(mysqli_query($link, " SELECT `amount` FROM `invest_payment_detail` WHERE `patient_id`='$lab_uhid[patient_id]' AND `opd_id`='$lab_pin' AND `date` between '$date1' and '$date2' "));
				$lab_ipd_amt+=$lab_amt["amount"];
				
				$lab_dis=mysqli_fetch_array(mysqli_query($link, " SELECT `dis_amt` FROM `invest_patient_payment_details` WHERE `patient_id`='$lab_uhid[patient_id]' AND `opd_id`='$lab_pin' AND `date` between '$date1' and '$date2' "));
				$lab_discount_amt+=$lab_dis["dis_amt"];
			}
		}
	}
	
	// IPD
	$ipd_amt=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS tot_amt,SUM(`discount`) AS tot_dis FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr AND `ipd_id` IN (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='3') ORDER BY `slno` "));
	$ipd_tot_amt+=$ipd_amt["tot_amt"];
	$ipd_discount_amt+=$ipd_amt["tot_dis"];
	
	// Casualty
	$casual_amt=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS tot_amt,SUM(`discount`) AS tot_dis FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr AND `ipd_id` IN (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='4') ORDER BY `slno` "));
	$casual_tot_amt+=$casual_amt["tot_amt"];
	$casual_discount_amt+=$casual_amt["tot_dis"];
	
	// Radiology
	$baby_amt=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS tot_amt,SUM(`discount`) AS tot_dis FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr AND `ipd_id` IN (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='8') ORDER BY `slno` "));
	$baby_tot_amt+=$baby_amt["tot_amt"];
	$baby_discount_amt+=$baby_amt["tot_dis"];
	
	//~ $i=1;
	//~ $radio_qry=mysqli_query($link, " SELECT distinct `ipd_id` FROM `ipd_advance_payment_details` WHERE `date` between '$date1' and '$date2' $ipd_max_slno_str_less $ipd_max_slno_str_grtr AND `ipd_id` IN (SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='8') ORDER BY `slno`");
	//~ while($radio_val=mysqli_fetch_array($radio_qry))
	//~ {
		//~ $radio_pin[$i]=$radio_val["ipd_id"];
		//~ $i++;
	//~ }
	//~ sort($radio_pin);
	//print_r($radio_pin);
	
?>
	<p style="margin-top: 2%;"><b>Account Summary from:</b> <?php echo convert_date($date11)." <b>to</b> ".convert_date($date22); ?>
		<button type="button" class="btn btn-info text-right" onclick="print_page('account_summary','<?php echo $date11;?>','<?php echo $date22;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
	</p>
	<table class="table table-condensed table-bordered">
		<!--<tr>
			<th colspan="3">OPD Registration <span style="float:right">From PIN No: <?php echo $vbllfrom.' --'.$vbllto;?> </span></th>
		</tr>-->
		<tr>
			<td>New Visit</td>
			<td>Total Rs. <span><?php echo number_format($opd_first_amt,2); ?></span></td>
			<th rowspan="3">Total: <span><?php echo number_format($opd_first_amt+$opd_revisit_amt,2); ?></span></th>
		</tr>
		<tr>
			<td>Re-Visit</td>
			<td>Total Rs. <span><?php echo number_format($opd_revisit_amt,2); ?></span></td>
		</tr>
		<tr>
			<td>Discount</td>
			<td>Total Rs. <span><?php echo number_format($opd_discount_amt,2); ?></span></td>
		</tr>
		<tr>
			<th colspan="3">LAB</th>
		</tr>
		<tr>
			<td>OPD LAB</td>
			<td>Total Rs. <span><?php echo number_format($lab_opd_amt,2); ?></span></td>
			<th rowspan="3">Total: <span><?php echo number_format($lab_opd_amt+$lab_ipd_amt,2); ?></span></th>
		</tr>
		<tr>
			<td>IPD LAB</td>
			<td>Total Rs. <span><?php echo number_format($lab_ipd_amt,2); ?></span></td>
		</tr>
		<tr>
			<td>Discount</td>
			<td>Total Rs. <span><?php echo number_format($lab_discount_amt,2); ?></span></td>
		</tr>
		<tr>
			<th colspan="3">IPD</th>
		</tr>
		<tr>
			<td>IPD</td>
			<td>Total Rs. <span><?php echo number_format($ipd_tot_amt,2); ?></span></td>
			<th rowspan="2">Total: <span><?php echo number_format($ipd_tot_amt,2); ?></span></th>
		</tr>
		<tr>
			<td>Discount</td>
			<td>Total Rs. <span><?php echo number_format($ipd_discount_amt,2); ?></span></td>
		</tr>
		<tr>
			<th colspan="3">Casualty</th>
		</tr>
		<tr>
			<td>Casualty</td>
			<td>Total Rs. <span><?php echo number_format($casual_tot_amt,2); ?></span></td>
			<th rowspan="2">Total: <span><?php echo number_format($casual_tot_amt,2); ?></span></th>
		</tr>
		<tr>
			<td>Discount</td>
			<td>Total Rs. <span><?php echo number_format($casual_discount_amt,2); ?></span></td>
		</tr>
		<tr>
			<th colspan="3">Baby</th>
		</tr>
		<tr>
			<td>Baby</td>
			<td>Total Rs. <span><?php echo number_format($baby_tot_amt,2); ?></span></td>
			<th rowspan="2">Total: <span><?php echo number_format($baby_tot_amt,2); ?></span></th>
		</tr>
		<tr>
			<td>Discount</td>
			<td>Total Rs. <span><?php echo number_format($baby_discount_amt,2); ?></span></td>
		</tr>
	</table>
<?php
}

?>
