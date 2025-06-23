<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	// Time format convert
	function convert_time($time)
	{
		$time = date("g:i A", strtotime($time));
		return $time;
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$encounter_val=$_GET['encounter'];
	$pay_mode=$_GET['pay_mode'];
	$user_entry=$_GET['user_entry'];
	
?>
<html>
<head>
	<title>Summary Account Report</title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
</head>
<body onafterprint="window.close();" onkeypress="close_window(event)">
	<div class="container-fluid">
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4>Summary Account Report</h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<table class="table table-bordered table-condensed table-report" style="font-size: 11px;">
			<tr>
				<th>#</th>
				<th>PIN</th>
				<th>Patient Name</th>
				<th>Bill Amout</th>
				<th>Discount</th>
				<th>Paid</th>
				<th>Balance</th>
				<th>User</th>
				<th>Encounter</th>
				<th>Date</th>
			</tr>
	<?php
		$n=1;
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=0;
		
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter_val' "));
		$pat_typ_encounter=$pat_typ["type"];
		
		$encounter_val_str_ipd=" AND `type`='3' ";
		if($encounter_val>0)
		{
			$encounter_val_str=" AND b.`type`='$encounter_val' ";
		}
		$user_str="";
		if($user_entry>0)
		{
			$user_str=" AND a.`user`='$user_entry' ";
			$user_str_ipd=" AND `user`='$user_entry' ";
		}
		
		if($encounter_val=='0' || $pat_typ_encounter=='1')
		{
			$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
			while($con_bal=mysqli_fetch_array($con_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$paid=$con_bal['amount'];
				
				$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
				
				$bill_amt=$con_pat_pay['tot_amount'];
				$discount=$con_pat_pay['dis_amt'];
				$balance=$con_pat_pay['balance'];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $con_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($con_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				$tot_bal=$tot_bal+$balance;
			}
		}
		if($encounter_val=='0' || $pat_typ_encounter=='2')
		{
			$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str");
			while($inv_bal=mysqli_fetch_array($inv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$paid=$inv_bal['amount'];
				
				$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
				
				$bill_amt=$inv_pat_pay['tot_amount'];
				$discount=$inv_pat_pay['dis_amt'];
				$balance=$inv_pat_pay['balance'];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $inv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($inv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				$tot_bal=$tot_bal+$balance;
			}
		}
		if($encounter_val=='0' || $encounter_val=='4' || $encounter_val=='5')
		{
			if($encounter_val=='0')
			{
				$encounter_val_str_other_ipd=" AND (b.`type`='4' OR b.`type`='5') "; // Casulty or Daycare
			}
			if($encounter_val==4)
			{
				$encounter_val_str_other_ipd=" AND b.`type`='4' "; // Casulty or Daycare
			}
			if($encounter_val==5)
			{
				$encounter_val_str_other_ipd=" AND b.`type`='5' "; // Casulty or Daycare
			}
			
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str_other_ipd $user_str ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				if($adv_bal["type"]==3)
				{
					$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				}
				
				// Baby's Charge
				$baby_serv_tot=0;
				$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
				if($delivery_check)
				{
					$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot=$baby_tot_serv["tots"];
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total=$baby_ot_tot_val["g_tot"];
					
					$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
					
				}
				
				$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
				
				$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
				$paid=$tot_amountt_pay['sum_paid'];
				
				$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$discount=$tot_amountt['sum_dis'];
				$paidd=$tot_amountt['sum_paid'];
				$balance=($bill_amt-$discount-$paidd);
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				$tot_bal=$tot_bal+$balance;
			}
		}
		
		// Balance Received
		
		//echo "<tr><th colspan='10'>Balance Received</th></tr>";
		
		if($encounter_val=='0' || $pat_typ_encounter=='1')
		{
			$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
			$con_bal_num=mysqli_num_rows($con_bal_qry);
			$zz=0;
			if($con_bal_num>0)
			{
				echo "<tr><th colspan='10'>Balance Received</th></tr>";
				$zz=1;
			}
			while($con_bal=mysqli_fetch_array($con_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$paid=$con_bal['amount'];
				
				$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
				
				$bill_amt=$con_pat_pay['tot_amount'];
				$discount=$con_pat_pay['dis_amt'];
				$balance=$con_pat_pay['balance'];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $con_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($con_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//~ $tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				//~ $tot_bal=$tot_bal+$balance;
			}
		}
		if($encounter_val=='0' || $pat_typ_encounter=='2')
		{
			$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
			
			if($zz==0)
			{
				$inv_bal_num=mysqli_num_rows($inv_bal_qry);
				if($inv_bal_num>0)
				{
					$zz=1;
					echo "<tr><th colspan='10'>Balance Received</th></tr>";
				}
			}
			
			while($inv_bal=mysqli_fetch_array($inv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$paid=$inv_bal['amount'];
				
				$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
				
				$bill_amt=$inv_pat_pay['tot_amount'];
				$discount=$inv_pat_pay['dis_amt'];
				$balance=$inv_pat_pay['balance'];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $inv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($inv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//~ $tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				//~ $tot_bal=$tot_bal+$balance;
			}
		}
		if($encounter_val=='0' || $encounter_val=='4' || $encounter_val=='5')
		{
			if($encounter_val=='0')
			{
				$encounter_val_str_other_ipd=" AND (b.`type`='4' OR b.`type`='5') "; // Casulty or Daycare
			}
			if($encounter_val==4)
			{
				$encounter_val_str_other_ipd=" AND b.`type`='4' "; // Casulty or Daycare
			}
			if($encounter_val==5)
			{
				$encounter_val_str_other_ipd=" AND b.`type`='5' "; // Casulty or Daycare
			}
			
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str_other_ipd $user_str");
			
			if($zz==0)
			{
				$adv_bal_num=mysqli_num_rows($adv_bal_qry);
				if($adv_bal_num>0)
				{
					$zz=1;
					echo "<tr><th colspan='10'>Balance Received</th></tr>";
				}
			}
			
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				if($adv_bal["type"]==3)
				{
					$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
					
				}
				
				// Baby's Charge
				$baby_serv_tot=0;
				$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' "));
				if($delivery_check)
				{
					$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot=$baby_tot_serv["tots"];
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total=$baby_ot_tot_val["g_tot"];
					
					$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
					
				}
				
				$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
				
				$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
				$paid=$tot_amountt_pay['sum_paid'];
				
				$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`tot_amount`) as sum_tot_amt,sum(`discount`) as sum_dis, sum(`amount`) sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$discount=$tot_amountt['sum_dis'];
				$paidd=$tot_amountt['sum_paid'];
				$balance=($bill_amt-$discount-$paidd);
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo $quser["name"]; ?></td>
					<td><?php echo $encounter; ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//~ $tot_bill=$tot_bill+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_paid=$tot_paid+$paid;
				//~ $tot_bal=$tot_bal+$balance;
			}
		}
		
	?>
			<tr>
				<td colspan="2"></td>
				<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
				<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
				<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
				<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid),2);?> </strong></span></td>
				<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bal),2);?> </strong></span></td>
				<td colspan="4">&nbsp;</td>
			</tr>
		</table>
	<?php
		if($encounter_val=='0' || $encounter_val=='3')
		{
	?>
		<table class="table table-bordered table-condensed table-report" style="font-size: 11px;">
			<tr>
				<th colspan="10">IPD Accounts</th>
			</tr>
			<tr>
				<th>#</th>
				<th>PIN</th>
				<th>Patient Name</th>
				<th>Bill Amount</th>
				<th>Discount</th>
				<th>Paid Amount</th>
				<th>Payment Type</th>
				<th>Balance</th>
				<th>Payment Time</th>
				<th>User</th>
			</tr>
	<?php
			
			$ipd_pat_reg_qry=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' $encounter_val_str_ipd $user_str_ipd ");
			while($ipd_pat_reg=mysqli_fetch_array($ipd_pat_reg_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
				
				$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
				
				// Baby's Charge
				$baby_serv_tot=0;
				$delivery_check=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' "));
				if($delivery_check)
				{
					$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT sum(`amount`) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot=$baby_tot_serv["tots"];
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select sum(amount) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total=$baby_ot_tot_val["g_tot"];
					
					$tot_baby_bill=$baby_serv_tot+$baby_ot_total;
					
				}
				
				$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt']+$tot_baby_bill;
				
				$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid, SUM(`discount`) AS sum_dis FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' AND `date` between '$date1' AND '$date2' $user_str_ipd "));
				$paid=$tot_amountt_pay['sum_paid'];
				$discount=$tot_amountt_pay['sum_dis'];
				$balance=$bill_amt-$paid-$discount;
				if($balance<0)
				{
					//$balance=0;
				}
				
				$ipd_payment_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` WHERE `patient_id`='$ipd_pat_reg[patient_id]' AND `ipd_id`='$ipd_pat_reg[opd_id]' AND `date` between '$date1' AND '$date2' $user_str_ipd ORDER BY `slno` ");
				$ipd_payment_num=mysqli_num_rows($ipd_payment_qry);
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$ipd_pat_reg[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $ipd_pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo number_format($bill_amt,2); ?></td>
					<td><?php echo number_format($discount,2); ?></td>
					<td><?php echo number_format($paid,2); ?></td>
					<td><?php echo ""; ?></td>
					<td><?php echo number_format($balance,2); ?></td>
					<td><?php echo convert_date($ipd_pat_reg["date"]); ?></td>
					<td></td>
				</tr>
			<?php
				if($ipd_payment_num>0)
				{
					while($ipd_payment=mysqli_fetch_array($ipd_payment_qry))
					{
						$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ipd_payment[user]' "));
				?>
				<tr>
					<td colspan="4"></td>
					<td><?php echo number_format($ipd_payment["discount"],2); ?></td>
					<td><?php echo number_format($ipd_payment["amount"],2); ?></td>
					<td><?php echo $ipd_payment["pay_type"]; ?></td>
					<td></td>
					<td><?php echo convert_date($ipd_payment["date"]); ?> <?php echo convert_time($ipd_payment["time"]); ?></td>
					<td><?php echo $quser["name"]; ?></td>
				</tr>
				<?php
					}
				}
				$n++;
				
				$tot_bill=$tot_bill+$bill_amt;
				$tot_bill_ipd=$tot_bill_ipd+$bill_amt;
				$tot_dis=$tot_dis+$discount;
				$tot_dis_ipd=$tot_dis_ipd+$discount;
				$tot_paid_ipd=$tot_paid_ipd+$paid;
				$tot_paid=$tot_paid+$paid;
				$tot_bal=$tot_bal+$balance;
				$tot_bal_ipd=$tot_bal_ipd+$balance;
			}
	?>
			<tr>
				<td colspan="2"></td>
				<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
				<td><span class=""><strong><?php echo number_format(($tot_bill_ipd),2);?> </strong></span></td>
				<td><span class=""><strong><?php echo number_format(($tot_dis_ipd),2);?> </strong></span></td>
				<td><span class=""><strong><?php echo number_format(($tot_paid_ipd),2);?> </strong></span></td>
				<td></td>
				<td><span class=""><strong><?php echo number_format(($tot_bal_ipd),2);?> </strong></span></td>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<th colspan="10">Balance Received</th>
			</tr>
	<?php
		
		$adv_bal_qry=mysqli_query($link, " SELECT * FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND b.`date`<'$date1' $encounter_val_str_ipd $user_str ");
		
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
	?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $adv_bal["ipd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td></td>
				<td><?php echo number_format($adv_bal["discount"],2); ?></td>
				<td><?php echo number_format($adv_bal["amount"],2); ?></td>
				<td><?php echo $adv_bal["pay_type"]; ?></td>
				<td></td>
				<td><?php echo convert_date($adv_bal["date"]); ?> <?php echo convert_time($adv_bal["time"]); ?></td>
				<td><?php echo $quser["name"]; ?></td>
			</tr>
	<?php
			$adv_bal_amount+=$adv_bal["amount"];
			$n++;
		}
		if($encounter_val=='0')
		{
			$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2'"));
		}else
		{
			$tot_expense_qry=mysqli_fetch_array(mysqli_query($link, "select sum(amount) as tot_exp from expense_detail where date between '$date1' and '$date2' AND `user`='$encounter_val' "));
		}
		$tot_expense=$tot_expense_qry["tot_exp"];
	?>
			<tr>
				<td colspan="3"></td>
				<td colspan="2"><span class="text-right"><strong>Balance Received:</strong></span></td>
				<td colspan="5"><span class=""><strong><?php echo $rupees_symbol.number_format(($adv_bal_amount),2);?> </strong></span></td>
			</tr>
			<!--<tr>
				<td colspan="3"></td>
				<td colspan="2"><span class="text-right"><strong>Total Amount:</strong></span></td>
				<td colspan="5"><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid+$adv_bal_amount),2);?> </strong></span></td>
			</tr>
			<tr>
				<td colspan="4"></td>
				<td colspan=""><span class="text-right"><strong>Expense:</strong></span></td>
				<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_expense),2);?> </strong></span></td>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td colspan="2"><span class="text-right"><strong>Net Amount:</strong></span></td>
				<td colspan="2"><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid+$adv_bal_amount-$tot_expense),2);?> </strong></span></td>
				<td colspan="5">&nbsp;</td>
			</tr>-->
		</table>
		<table class="table table-bordered table-condensed table-report" style="font-size: 11px;">
			<tr>
				<th colspan="5">Account Summary</th>
			</tr>
			<tr>
				<th>Bill Amount</th>
				<th>Discount Amount</th>
				<th>Paid Amount</th>
				<!--<th>Balance Receive</th>-->
				<th>Balance Amount</th>
			</tr>
			<tr>
				<th><?php echo number_format(($tot_bill),2);?></th>
				<th><?php echo number_format(($tot_dis),2);?></th>
				<th><?php echo number_format(($tot_paid),2);?></th>
				<!--<th><?php echo number_format(($adv_bal_amount),2);?></th>-->
				<th><?php echo number_format(($tot_bal),2);?></th>
			</tr>
			<tr>
				<td></td>
				<th><span class="text-right">Balance Received :</span></th>
				<th><?php echo number_format(($adv_bal_amount),2);?></th>
				<th colspan="2"></th>
			</tr>
			<tr>
				<td></td>
				<th><span class="text-right">Total Amount :</span></th>
				<th><?php echo number_format(($tot_paid+$adv_bal_amount),2);?></th>
				<th colspan="2"></th>
			</tr>
			<tr>
				<td></td>
				<th><span class="text-right">Expense :</span></th>
				<th><?php echo number_format(($tot_expense),2);?></th>
				<th colspan="2"></th>
			</tr>
			<tr>
				<td></td>
				<th><span class="text-right">Net Amount :</span></th>
				<th><?php echo number_format(($tot_paid+$adv_bal_amount-$tot_expense),2);?></th>
				<th colspan="2"></th>
			</tr>
		</table>
<?php
}
?>
	</div>
</body>
</html>
<script>
	//window.print();
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style type="text/css" media="print">
  @page { size: landscape; }
</style>
