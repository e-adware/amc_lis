<?php
session_start();
include("../../includes/connection.php");

$c_user=trim($_SESSION['emp_id']);

$u_level=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid` FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$u_level["levelid"];

$date1=$_POST['date1'];
$date2=$_POST['date2'];

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

if($_POST["type"]=="summary_account_detail")
{
	$encounter=$_POST['encounter'];
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	if($encounter==0)
	{
		echo "<h5>Select Department</h5>";
	}
	if($encounter_pay_type==1)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Visit Fees</th>
					<th>Regd Fees</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
<?php
		$n=1;
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=$tot_visit_fee=$tot_regd_fee=$tot_refund_amount=0;
		
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ORDER BY b.`slno` ");
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=$visit_fee=$regd_fee=$refund_amount=0;
			
			$paid=$con_bal['amount'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			//$balance=$con_pat_pay['balance'];
			$balance=$bill_amt-$paid-$discount;
			$visit_fee=$con_pat_pay['visit_fee'];
			$regd_fee=$con_pat_pay['regd_fee'];
			
			// Check Refund
			$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			if($con_pat_refund)
			{
				//~ $paid+=$con_pat_refund["refund_amount"];
				$refund_amount=$con_pat_refund["refund_amount"];
				$bill_amt+=$refund_amount;
			}
			// Check Free
			//~ $con_pat_free=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			//~ if($con_pat_free)
			//~ {
				//~ $paid+=$con_pat_free["free_amount"];
			//~ }
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter_name=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($visit_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($regd_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($refund_amount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<!--<td><?php echo $quser["name"]; ?></td>-->
				<!--<td><?php echo $encounter_name; ?></td>-->
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill+=$bill_amt;
			$tot_visit_fee+=$visit_fee;
			$tot_regd_fee+=$regd_fee;
			$tot_dis+=$discount;
			$tot_refund_amount+=$refund_amount;
			$tot_paid+=$paid;
			$tot_bal+=$balance;
		}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol.number_format($tot_bill,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_visit_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_regd_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_dis,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_refund_amount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal,2); ?></td>
				<td></td>
			</tr>
<?php
		
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ");
		$con_bal_num=mysqli_num_rows($con_bal_qry);
		$zz=0;
		if($con_bal_num>0)
		{
			echo "<tr><th colspan='11'>Balance Received</th></tr>";
			$zz=1;
			$n=1;
			$tot_bal_bill_amt=$tot_bal_discount=$tot_bal_paid=$tot_bal_balance=$tot_bal_visit_fee=$tot_bal_regd_fee=$tot_bal_refund_amount=0;
			while($con_bal=mysqli_fetch_array($con_bal_qry))
			{
				$bal_pat_show=0;
				$bal_bill_amt=$bal_discount=$bal_paid=$bal_balance=$bal_visit_fee=$bal_regd_fee=$bal_refund_amount=0;
				
				$bal_paid=$con_bal['amount'];
				
				$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
				
				$bal_bill_amt=$con_pat_pay['tot_amount'];
				$bal_discount=$con_pat_pay['dis_amt'];
				//$balance=$con_pat_pay['balance'];
				$bal_balance=0;
				$bal_visit_fee=$con_pat_pay['visit_fee'];
				$bal_regd_fee=$con_pat_pay['regd_fee'];
				
				// Check Refund
				$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
				
				if($con_pat_refund)
				{
					$bal_refund_amount=$con_pat_refund["refund_amount"];
				}
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $con_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_visit_fee,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_regd_fee,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_refund_amount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_balance,2); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($con_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bal_bill_amt+=$bal_bill_amt;
				$tot_bal_visit_fee+=$bal_visit_fee;
				$tot_bal_regd_fee+=$bal_regd_fee;
				$tot_bal_discount+=$bal_discount;
				$tot_bal_refund_amount+=$bal_refund_amount;
				$tot_bal_paid+=$bal_paid;
				$tot_bal_balance+=$bal_balance;
				
			}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol.number_format($tot_bal_bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_visit_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_regd_fee,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_refund_amount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_balance,2); ?></td>
				<td></td>
			</tr>
<?php
		}
			// Total Balance Received between dates
			$con_bal_rev=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_bal_recv` FROM `consult_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' AND a.`typeofpayment`='B' "));
			$con_bal_rev_amount=$con_bal_rev["tot_bal_recv"];
?>
			<tr>
				<th colspan="3"><span class="text-right">Grand Total</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_bill,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_visit_fee,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_regd_fee,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_dis,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_refund_amount,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_paid+$tot_bal_paid,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_bal-$con_bal_rev_amount,2); ?></th>
				<th></th>
			</tr>
		</table>
<?php
	}
	if($encounter_pay_type==2)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Discount</th>
					<th>Refund</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
<?php
		$n=1;
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=$tot_visit_fee=$tot_regd_fee=$tot_refund_amount=0;
		
		$con_bal_qry=mysqli_query($link, " SELECT * FROM `invest_patient_payment_details` WHERE `date` BETWEEN '$date1' AND '$date2' AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `type`='$encounter') ");
		
		//~ $con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' GROUP BY a.`patient_id`,a.`opd_id` ORDER BY b.`slno` ");
		
		while($con_bal=mysqli_fetch_array($con_bal_qry))
		{
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=$visit_fee=$regd_fee=$refund_amount=0;
			
			//~ $paid=$con_bal['amount'];
			
			$inv_pay=mysqli_fetch_array(mysqli_query($link, "SELECT ifnull(SUM(`amount`),0) AS `tot_inv_adv` FROM `invest_payment_detail` WHERE patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' AND `date` between '$date1' AND '$date2'"));
			
			$paid=$inv_pay['tot_inv_adv'];
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$discount=$con_pat_pay['dis_amt'];
			//$balance=$con_pat_pay['balance'];
			$balance=$bill_amt-$paid-$discount;
			
			// Check Refund
			$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			if($con_pat_refund)
			{
				//~ $paid+=$con_pat_refund["refund_amount"];
				$refund_amount=$con_pat_refund["refund_amount"];
				
				$bill_amt+=$refund_amount;
			}
			// Check Free
			//~ $con_pat_free=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_free where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
			
			//~ if($con_pat_free)
			//~ {
				//~ $paid+=$con_pat_free["free_amount"];
			//~ }
			
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
			
			$uhid_id=$pat_info["patient_id"];
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
			$encounter_name=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $con_bal["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($refund_amount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
				<!--<td><?php echo $quser["name"]; ?></td>-->
				<!--<td><?php echo $encounter_name; ?></td>-->
				<td><?php echo convert_date($con_bal["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill+=$bill_amt;
			$tot_dis+=$discount;
			$tot_refund_amount+=$refund_amount;
			$tot_paid+=$paid;
			$tot_bal+=$balance;
		}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol.number_format($tot_bill,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_dis,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_refund_amount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_paid,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal,2); ?></td>
				<td></td>
			</tr>
<?php
		
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ");
		$con_bal_num=mysqli_num_rows($con_bal_qry);
		$zz=0;
		if($con_bal_num>0)
		{
			echo "<tr><th colspan='11'>Balance Received</th></tr>";
			$zz=1;
			$n=1;
			$tot_bal_bill_amt=$tot_bal_discount=$tot_bal_paid=$tot_bal_balance=$tot_bal_visit_fee=$tot_bal_regd_fee=$tot_bal_refund_amount=0;
			while($con_bal=mysqli_fetch_array($con_bal_qry))
			{
				$bal_pat_show=0;
				$bal_bill_amt=$bal_discount=$bal_paid=$bal_balance=$bal_visit_fee=$bal_regd_fee=$bal_refund_amount=0;
				
				$bal_paid=$con_bal['amount'];
				
				$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
				
				$bal_bill_amt=$con_pat_pay['tot_amount'];
				$bal_discount=$con_pat_pay['dis_amt'];
				//$balance=$con_pat_pay['balance'];
				$bal_balance=0;
				
				// Check Refund
				$con_pat_refund=mysqli_fetch_array(mysqli_query($link, "select * from invest_payment_refund where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]' "));
				
				if($con_pat_refund)
				{
					$bal_refund_amount=$con_pat_refund["refund_amount"];
				}
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $con_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_refund_amount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($bal_balance,2); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($con_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bal_bill_amt+=$bal_bill_amt;
				$tot_bal_discount+=$bal_discount;
				$tot_bal_refund_amount+=$bal_refund_amount;
				$tot_bal_paid+=$bal_paid;
				$tot_bal_balance+=$bal_balance;
				
			}
?>
			<tr>
				<th colspan="6"><span class="text-right">Total</span></th>
				<!--<td><?php echo $rupees_symbol.number_format($tot_bal_bill_amt,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_discount,2); ?></td>
				<td><?php echo $rupees_symbol.number_format($tot_bal_refund_amount,2); ?></td>-->
				<td><?php echo $rupees_symbol.number_format($tot_bal_paid,2); ?></td>
				<!--<td><?php echo $rupees_symbol.number_format($tot_bal_balance,2); ?></td>-->
				<td></td>
				<td></td>
			</tr>
<?php
		}
			// Total Balance Received between dates
			$con_bal_rev=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `tot_bal_recv` FROM `invest_payment_detail` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' AND a.`typeofpayment`='B' "));
			$con_bal_rev_amount=$con_bal_rev["tot_bal_recv"];
			$con_bal_rev_amount=0;
?>
			<tr>
				<th colspan="3"><span class="text-right">Grand Total</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_bill,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_dis,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_refund_amount,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_paid+$tot_bal_paid,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_bal-$con_bal_rev_amount,2); ?></th>
				<th></th>
			</tr>
		</table>
<?php
	}
	if($encounter!=3 && $encounter_pay_type==3)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amout</th>
					<th>Discount</th>
					<th>Paid</th>
					<th>Balance</th>
					<!--<th>User</th>-->
					<!--<th>Encounter</th>-->
					<th>Date</th>
				</tr>
			</thead>
<?php
			$n=1;
			$tot_bill_amt=$tot_discount=$tot_paid=$tot_balance=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' ORDER BY b.`slno` ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$paid=$balance=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$bill_amt=$tot_serv['sum_tot_amt'];
				
				$tot_amountt_pay=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS sum_paid FROM `ipd_advance_payment_details` WHERE `slno`='$adv_bal[slno]'"));
				$paid=$tot_amountt_pay['sum_paid'];
				
				$tot_amountt=mysqli_fetch_array(mysqli_query($link, " SELECT sum(`discount`) as sum_dis, sum(`amount`) as sum_paid, sum(`balance`) as sum_bal FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$discount=$tot_amountt['sum_dis'];
				$paidd=$tot_amountt['sum_paid'];
				$balance=($bill_amt-$discount-$paidd);
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($paid,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill_amt+=$bill_amt;
				$tot_discount+=$discount;
				$tot_paid+=$paid;
				$tot_balance+=$balance;
			}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_bill_amt,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_discount,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_paid,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_balance,2); ?></th>
				<th></th>
			</tr>
		</table>
<?php
	}
	if($encounter==3)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date($date1)." to ".convert_date($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th colspan="4">Patient Name</th>
					<th colspan="2">Advance Paid</th>
					<th>Date</th>
				</tr>
			</thead>
				<tr>
					<th colspan="9">Advance Payment</th>
				</tr>
<?php
			$n=1;
			$tot_advance_paid=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`pay_type`='Advance' AND b.`type`='$encounter' ORDER BY b.`slno` ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$advance_paid=0;
				
				$advance_paid=$adv_bal["amount"];
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["ipd_id"]; ?></td>
					<td colspan="4"><?php echo $pat_info["name"]; ?></td>
					<td colspan="2"><?php echo $rupees_symbol.number_format($advance_paid,2); ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_advance_paid+=$advance_paid;
			}
?>
			<tr>
				<th colspan="6"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_advance_paid,2); ?></th>
				<th colspan="2"></th>
			</tr>
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amount</th>
					<th>Discount Amount</th>
					<th>Final Payment</th>
					<th>Previous Payment</th>
					<th>Balance</th>
					<th>Date</th>
				</tr>
			</thead>
				<tr>
					<th colspan="9">Final Payment</th>
				</tr>
<?php
			$n=1;
			$tot_bill_amt=$tot_discount=$tot_final_pay=$tot_balance=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `ipd_advance_payment_details` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`ipd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`pay_type`='Final' AND b.`type`='$encounter' ORDER BY b.`slno` ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$bill_amt=$discount=$final_pay=$balance=$prev_pay=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$tot_serv_ot=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `sum_tot_amt` FROM `ot_pat_service_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]'"));
				
				$bill_amt=$tot_serv['sum_tot_amt']+$tot_serv_ot['sum_tot_amt'];
				
				$discount=$adv_bal['discount'];
				
				$final_pay=$adv_bal['amount'];
				
				$tot_adv=mysqli_fetch_array(mysqli_query($link, " SELECT SUM(`amount`) AS `adv_pay` FROM `ipd_advance_payment_details` WHERE `patient_id`='$adv_bal[patient_id]' AND `ipd_id`='$adv_bal[ipd_id]' AND `pay_type`='Advance'"));
				
				$prev_pay=$tot_adv["adv_pay"];
				
				$balance=($bill_amt-$discount-$final_pay-$prev_pay);
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["ipd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.number_format($bill_amt,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($discount,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($final_pay,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($prev_pay,2); ?></td>
					<td><?php echo $rupees_symbol.number_format($balance,2); ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill_amt+=$bill_amt;
				$tot_discount+=$discount;
				$tot_final_pay+=$final_pay;
				$tot_balance+=$balance;
			}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_bill_amt,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_discount,2); ?></th>
				<th><?php echo $rupees_symbol.number_format($tot_final_pay,2); ?></th>
				<th></th>
				<th><?php echo $rupees_symbol.number_format($tot_balance,2); ?></th>
				<th></th>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Net Amount Received</span></th>
				<th><?php echo $rupees_symbol.number_format($tot_final_pay+$tot_advance_paid,2); ?></th>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
<?php
	}
}

?>
