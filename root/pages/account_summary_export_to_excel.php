
<html>
<head>
<title>Detail Acount</title>

<style>
input[type="text"]
{
	border:none;
}
body {
	padding: 10px;
	font-size:13px; font-family:Arial, Helvetica, sans-serif; line-height: 18px;
}
.line td{border-top:1px dotted}
@media print{
 .noprint{
	 display:none;
 }
}
.bline td{border-bottom:1px solid;}
*{font-size:13px;}
</style>
</head>
<body>
<div class="container">
	<?php
	include'../../includes/connection.php';

	function convert_date($date)
	{
		$timestamp = strtotime($date); 
		$new_date = date('d M Y', $timestamp);
		return $new_date;
	}

	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	$encounter=$_GET['encounter'];
	$encounter_val=$_GET['encounter'];
	$pay_mode=$_GET['pay_mode'];
	$user_entry=$_GET['user_entry'];
	
	$filename ="account_summary_".$date1."_to_".$date2.".xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	
	$q="SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' ";
	if($encounter!='0')
	{
		$q.=" AND `type`='$encounter'";
	}
	if($user_entry!='0')
	{
		$q.=" AND `user`='$user_entry'";
	}
	$q.=" ORDER BY `slno`";
	
	//echo $q;
	
	$uhid_opdid_qry=mysqli_query($link, $q);
	?>
	<p style="margin-top: 2%;"><b>Account Summary Report from:</b> <?php echo convert_date($date1)." <b>to</b> ".convert_date($date2); ?></p>
	<table class="table table-bordered table-condensed table-report">
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
	
	$encounter_val_str="";
	if($encounter_val>0)
	{
		$encounter_val_str=" AND b.`type`='$encounter_val' ";
	}
	$user_str="";
	if($user_entry>0)
	{
		$user_str=" AND a.`user`='$user_entry' ";
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
	
	if($encounter_val=='0' || $pat_typ_encounter=='3')
	{
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
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
	
	echo "<tr><th colspan='10'>Balance Received</th></tr>";
	
	if($encounter_val=='0' || $pat_typ_encounter=='1')
	{
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
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
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str ");
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
	if($encounter_val=='0' || $pat_typ_encounter=='3')
	{
		$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' $encounter_val_str $user_str");
		while($adv_bal=mysqli_fetch_array($adv_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=0;
			
			$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
			$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
			
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
			<td colspan="2"></td>
			<td colspan=""><span class="text-right"><strong>Total:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bill),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_dis),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid),2);?> </strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_bal),2);?> </strong></span></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"></td>
			<td colspan=""><span class="text-right"><strong>Expense:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"></td>
			<td><span class="text-right"><strong>Net Amount:</strong></span></td>
			<td><span class=""><strong><?php echo $rupees_symbol.number_format(($tot_paid-$tot_expense),2);?> </strong></span></td>
			<td colspan="5">&nbsp;</td>
		</tr>
	</table>
</div>
</body>
</html>
