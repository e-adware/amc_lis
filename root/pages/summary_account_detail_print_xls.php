<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user=trim($_SESSION['emp_id']);

$emp_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));
$u_level=$emp_info["levelid"];

$date1=$_GET['date1'];
$date2=$_GET['date2'];
$encounter=$_GET['encounter'];
$branch_id=$_GET['branch_id'];

if(!$branch_id)
{
	$branch_id=$emp_info["branch_id"];
}

$branch_str=" AND `opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_a=" AND a.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";
$branch_str_b=" AND b.`opd_id` IN(SELECT `opd_id` FROM `uhid_and_opdid` WHERE `branch_id`='$branch_id')";

$filename ="summary_account_report_".$date1."_to_".$date2.".xls";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);

?>
<?php
	
	$user_str="";
	$user_str_a="";
	$user_str_b="";
	if($user_entry>0)
	{
		$user_str=" AND `user`='$user_entry'";
		$user_str_a=" AND a.`user`='$user_entry'";
		$user_str_b=" AND b.`user`='$user_entry'";
		
		$user_info=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$user_entry' "));
		$user_name=$user_info["name"];
	}
	else
	{
		$user_name="All";
	}
	
	$encounter_pay_type=0;
	if($encounter>0)
	{
		$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT `type` FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$encounter_pay_type=$pat_typ_text["type"];
	}
	
	if($encounter_pay_type==1)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1)." to ".convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right" id="det_excel" onclick="export_excel()"><i class="icon-file icon-large"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
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
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=$tot_visit_fee=$tot_regd_fee=$tot_refund_amount=$tot_tax_amount=0;
		
		$pat_reg_qry=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $branch_str $user_str ORDER BY `slno` ASC ");
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));
			
			$uhid_id     =$pat_info["patient_id"];
			$patient_id  =$pat_reg["patient_id"];
			$opd_id      =$pat_reg["opd_id"];
			
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=$visit_fee=$regd_fee=$refund_amount=$tax_amount=0;
			
			$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$pat_reg[patient_id]' and opd_id='$pat_reg[opd_id]'"));
			
			$bill_amt=$con_pat_pay['tot_amount'];
			$visit_fee=$con_pat_pay['visit_fee'];
			$regd_fee=$con_pat_pay['regd_fee'];
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));
			
			$paid           =$check_paid["paid"];
			$discount       =$check_paid["discount"];
			//$refund_amount  =$check_paid["refund"];
			$tax_amount     =$check_paid["tax"];
			
			$check_refund=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
			$refund_amount_other  =$check_refund["refund"];
			
			if($refund_amount_other>0)
			{
				$bill_amt+=$refund_amount_other;
			}
			
			$settle_amount=$paid+$discount+$tax_amount-$refund_amount;
			
			$balance=$bill_amt-$settle_amount;
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
			$encounter_name=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_reg["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($bill_amt); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($visit_fee); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($regd_fee); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($discount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($paid); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($balance); ?></td>
				<!--<td><?php echo $quser["name"]; ?></td>-->
				<!--<td><?php echo $encounter_name; ?></td>-->
				<td><?php echo convert_date($pat_reg["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill+=$bill_amt;
			$tot_visit_fee+=$visit_fee;
			$tot_regd_fee+=$regd_fee;
			$tot_dis+=$discount;
			$tot_refund_amount+=$refund_amount;
			$tot_tax_amount+=$tax_amount;
			$tot_paid+=$paid;
			$tot_bal+=$balance;
		}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bill); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_visit_fee); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_regd_fee); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_paid); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal); ?></td>
				<td></td>
			</tr>
<?php
		
		$con_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
		$con_bal_num=mysqli_num_rows($con_bal_qry);
		$zz=0;
		if($con_bal_num>0)
		{
			echo "<tr><th colspan='11'>Balance Received</th></tr>";
			$zz=1;
			$n=1;
			$tot_bal_bill_amt=$tot_bal_discount=$tot_bal_paid=$tot_bal_balance=$tot_bal_visit_fee=$tot_bal_regd_fee=$tot_bal_refund_amount=$tot_bal_tax_amount=0;
			while($con_bal=mysqli_fetch_array($con_bal_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$con_bal[patient_id]'"));
				
				$uhid_id     =$pat_info["patient_id"];
				$patient_id  =$con_bal["patient_id"];
				$opd_id      =$con_bal["opd_id"];
				
				$bal_pat_show=0;
				$bal_bill_amt=$bal_discount=$bal_paid=$bal_balance=$bal_visit_fee=$bal_regd_fee=$bal_refund_amount=$bal_tax_amount=0;
				
				$bal_paid=$con_bal['amount'];
				
				$con_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from consult_patient_payment_details where patient_id='$con_bal[patient_id]' and opd_id='$con_bal[opd_id]'"));
				
				$bal_bill_amt=$con_pat_pay['tot_amount'];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$con_bal[pay_id]' $user_str_a "));
				
				$bal_paid           =$check_paid["paid"];
				$bal_discount       =$check_paid["discount"];
				$bal_refund_amount  =$check_paid["refund"];
				$bal_tax_amount     =$check_paid["tax"];
				
				$bal_visit_fee=$con_pat_pay['visit_fee'];
				$bal_regd_fee=$con_pat_pay['regd_fee'];
				
				if($bal_discount<0)
				{
					$bal_discount=0;
				}
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$con_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$con_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $con_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_bill_amt); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_visit_fee); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_regd_fee); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_paid); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($con_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//~ $tot_bal_bill_amt+=$bal_bill_amt;
				//~ $tot_bal_visit_fee+=$bal_visit_fee;
				//~ $tot_bal_regd_fee+=$bal_regd_fee;
				$tot_bal_discount+=$bal_discount;
				$tot_dis+=$bal_discount;
				$tot_bal_refund_amount+=$bal_refund_amount;
				$tot_refund_amount+=$bal_refund_amount;
				$tot_bal_tax_amount+=$bal_tax_amount;
				$tot_bal_paid+=$bal_paid;
				//~ $tot_bal_balance+=$bal_balance;
				
			}
?>
			<tr>
				<th colspan="6"><span class="text-right">Total</span></th>
				<!--<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_bill_amt); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_visit_fee); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_regd_fee); ?></td>-->
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_discount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_paid); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_balance); ?></td>
				<td></td>
			</tr>
<?php
		}
?>
			<tr>
				<th colspan="6"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bill); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_visit_fee); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_regd_fee); ?></th>-->
				<th><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
<?php
		if($tot_refund_amount>0)
		{
?>
			<tr>
				<th colspan="8" style="text-align:right;">Net Received Amount</th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid-$tot_refund_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
<?php
		}
?>
		</table>
<?php
	}
	if($encounter_pay_type==2)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1)." to ".convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
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
		$tot_bill=$tot_dis=$tot_paid=$tot_bal=$tot_refund_amount=$tot_tax_amount=0;
		
		$pat_reg_qry=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $user_str $branch_str ORDER BY `slno` ASC ");
		while($pat_reg=mysqli_fetch_array($pat_reg_qry))
		{
			$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));
			
			$uhid_id     =$pat_info["patient_id"];
			$patient_id  =$pat_reg["patient_id"];
			$opd_id      =$pat_reg["opd_id"];
			
			$pat_show=0;
			$bill_amt=$discount=$paid=$balance=$refund_amount=$tax_amount=0;
			
			$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$pat_reg[patient_id]' and opd_id='$pat_reg[opd_id]'"));
			
			$bill_amt=$inv_pat_pay['tot_amount'];
			
			$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));
			
			$paid           =$check_paid["paid"];
			$discount       =$check_paid["discount"];
			$refund_amount  =$check_paid["refund"];
			$tax_amount     =$check_paid["tax"];
			
			$check_refund=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
			$refund_amount_other  =$check_refund["refund"];
			
			if($refund_amount_other>0)
			{
				$bill_amt+=$refund_amount_other;
			}
			
			$settle_amount=$paid+$discount+$tax_amount-$refund_amount;
			
			$balance=$bill_amt-$settle_amount;
			
			$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
			$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
			$encounter_name=$pat_typ_text['p_type'];
		?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $pat_reg["opd_id"]; ?></td>
				<td><?php echo $pat_info["name"]; ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($bill_amt); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($discount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($paid); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($balance); ?></td>
				<!--<td><?php echo $quser["name"]; ?></td>-->
				<!--<td><?php echo $encounter_name; ?></td>-->
				<td><?php echo convert_date($pat_reg["date"]); ?></td>
			</tr>
		<?php
			$n++;
			
			$tot_bill+=$bill_amt;
			$tot_dis+=$discount;
			$tot_refund_amount+=$refund_amount;
			$tot_tax_amount+=$rax_amount;
			$tot_paid+=$paid;
			$tot_bal+=$balance;
		}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bill); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_paid); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal); ?></td>
				<td></td>
			</tr>
<?php
		
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
		$inv_bal_num=mysqli_num_rows($inv_bal_qry);
		$zz=0;
		if($inv_bal_num>0)
		{
			echo "<tr><th colspan='11'>Balance Received</th></tr>";
			$zz=1;
			$n=1;
			$tot_bal_bill_amt=$tot_bal_discount=$tot_bal_paid=$tot_bal_balance=$tot_bal_visit_fee=$tot_bal_regd_fee=$tot_bal_refund_amount=0;
			while($inv_bal=mysqli_fetch_array($inv_bal_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
				
				$uhid_id    =$pat_info["patient_id"];
				$patient_id  =$inv_bal["patient_id"];
				$opd_id      =$inv_bal["opd_id"];
				
				$bal_pat_show=0;
				$bal_bill_amt=$bal_discount=$bal_paid=$bal_balance=$bal_refund_amount=$bal_tax_amount=0;
				
				$bal_paid=$inv_bal['amount'];
				
				$inv_pat_pay=mysqli_fetch_array(mysqli_query($link, "select * from invest_patient_payment_details where patient_id='$inv_bal[patient_id]' and opd_id='$inv_bal[opd_id]'"));
				
				$bal_bill_amt=$inv_pat_pay['tot_amount'];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$inv_bal[pay_id]' $user_str_a "));
				
				$bal_paid           =$check_paid["paid"];
				$bal_discount       =$check_paid["discount"];
				$bal_refund_amount  =$check_paid["refund"];
				$bal_tax_amount     =$check_paid["tax"];
				
				if($bal_discount<0)
				{
					$bal_discount=0;
				}
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $inv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_bill_amt); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_paid); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($inv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//$tot_bal_bill_amt+=$bal_bill_amt;
				$tot_bal_discount+=$bal_discount;
				$tot_dis+=$bal_discount;
				$tot_bal_refund_amount+=$bal_refund_amount;
				$tot_refund_amount+=$bal_refund_amount;
				$tot_bal_paid+=$bal_paid;
				//$tot_bal_balance+=$bal_balance;
				
			}
?>
			<tr>
				<th colspan="4"><span class="text-right">Total</span></th>
				<!--<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_bill_amt); ?></td>-->
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_discount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_paid); ?></td>
				<!--<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_balance); ?></td>-->
				<td></td>
				<td></td>
			</tr>
<?php
		}
?>
			<tr>
				<th colspan="4"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bill); ?></th>-->
				<th><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
<?php
		if($tot_refund_amount>0)
		{
?>
			<tr>
				<th colspan="6" style="text-align:right;">Net Received Amount</th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid-$tot_refund_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
<?php
		}
?>
		</table>
<?php
	}
	if($encounter!=3 && $encounter_pay_type==3)
	{
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1)." to ".convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
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
			$tot_bill_amt=$tot_discount=$tot_paid=$tot_refund_amount=$tot_tax_amount=$tot_balance=0;
			$pat_reg_qry=mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$date1' AND '$date2' AND `type`='$encounter' $user_str $branch_str ORDER BY `slno` ASC ");
			while($pat_reg=mysqli_fetch_array($pat_reg_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$pat_reg[patient_id]'"));
				
				$uhid_id     =$pat_info["patient_id"];
				$patient_id  =$pat_reg["patient_id"];
				$opd_id      =$pat_reg["opd_id"];
				
				$pat_show=0;
				$bill_amt=$discount=$paid=$refund_amount=$tax_amount=$balance=0;
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$pat_reg[patient_id]' AND `ipd_id`='$pat_reg[opd_id]'"));
				
				$bill_amt=$tot_serv['sum_tot_amt'];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' $user_str_a "));
				
				$paid           =$check_paid["paid"];
				$discount       =$check_paid["discount"];
				$refund_amount  =$check_paid["refund"];
				$tax_amount     =$check_paid["tax"];
				
				$check_refund=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`refund_amount`),0) AS `refund` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' "));
				$refund_amount_other  =$check_refund["refund"];
				
				if($refund_amount_other>0)
				{
					$bill_amt+=$refund_amount_other;
				}
				
				$settle_amount=$paid+$discount+$tax_amount-$refund_amount;
				
				$balance=$bill_amt-$settle_amount;
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_reg["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($refund_amount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($paid); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($pat_reg["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill_amt+=$bill_amt;
				$tot_dis+=$discount;
				$tot_refund_amount+=$refund_amount;
				$tot_tax_amount+=$tax_amount;
				$tot_paid+=$paid;
				$tot_balance+=$balance;
			}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_bill_amt); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_balance); ?></th>
				<th></th>
			</tr>
<?php
		$inv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`date` between '$date1' AND '$date2' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
		$inv_bal_num=mysqli_num_rows($inv_bal_qry);
		$zz=0;
		if($inv_bal_num>0)
		{
			echo "<tr><th colspan='11'>Balance Received</th></tr>";
			$zz=1;
			$n=1;
			$tot_bal_bill_amt=$tot_bal_discount=$tot_bal_paid=$tot_bal_balance=$tot_bal_visit_fee=$tot_bal_regd_fee=$tot_bal_refund_amount=0;
			while($inv_bal=mysqli_fetch_array($inv_bal_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$inv_bal[patient_id]'"));
				
				$uhid_id    =$pat_info["patient_id"];
				$patient_id  =$inv_bal["patient_id"];
				$opd_id      =$inv_bal["opd_id"];
				
				$bal_pat_show=0;
				$bal_bill_amt=$bal_discount=$bal_paid=$bal_balance=$bal_refund_amount=$bal_tax_amount=0;
				
				$bal_paid=$inv_bal['amount'];
				
				$tot_serv=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `sum_tot_amt` FROM `ipd_pat_service_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$opd_id'"));
				
				$bal_bill_amt=$tot_serv['sum_tot_amt'];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(a.`amount`),0) AS `paid`, ifnull(SUM(a.`discount_amount`),0) AS `discount`, ifnull(SUM(a.`refund_amount`),0) AS `refund`, ifnull(SUM(a.`tax_amount`),0) AS `tax` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date`!=b.`date` AND a.`patient_id`='$patient_id' and a.`opd_id`='$opd_id' AND a.`date` between '$date1' AND '$date2' AND a.`pay_id`='$inv_bal[pay_id]' $user_str_a "));
				
				$bal_paid           =$check_paid["paid"];
				$bal_discount       =$check_paid["discount"];
				$bal_refund_amount  =$check_paid["refund"];
				$bal_tax_amount     =$check_paid["tax"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$inv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$inv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $inv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_bill_amt); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_refund_amount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_paid); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_balance); ?></td>
					<!--<td><?php echo $quser["name"]; ?></td>-->
					<!--<td><?php echo $encounter_name; ?></td>-->
					<td><?php echo convert_date($inv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				//$tot_bal_bill_amt+=$bal_bill_amt;
				$tot_bal_discount+=$bal_discount;
				$tot_dis+=$bal_discount;
				$tot_bal_refund_amount+=$bal_refund_amount;
				$tot_refund_amount+=$bal_refund_amount;
				$tot_bal_paid+=$bal_paid;
				//$tot_bal_balance+=$bal_balance;
				
			}
?>
			<tr>
				<th colspan="4"><span class="text-right">Total</span></th>
				<!--<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_bill_amt); ?></td>-->
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_discount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_refund_amount); ?></td>
				<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_paid); ?></td>
				<!--<td><?php echo $rupees_symbol.indian_currency_format($tot_bal_balance); ?></td>-->
				<td></td>
				<td></td>
			</tr>
<?php
		}
?>
			<tr>
				<th colspan="4"><span class="text-right">Grand Total</span></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bill_amt); ?></th>-->
				<th><?php echo $rupees_symbol.indian_currency_format($tot_dis); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid); ?></th>
				<!--<th><?php echo $rupees_symbol.indian_currency_format($tot_bal); ?></th>-->
				<th></th>
				<th></th>
			</tr>
<?php
		if($tot_refund_amount>0)
		{
?>
			<tr>
				<th colspan="6" style="text-align:right;">Net Received Amount</th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_paid+$tot_bal_paid-$tot_refund_amount); ?></th>
				<th></th>
				<th></th>
			</tr>
<?php
		}
?>
		</table>
<?php
	}
	if($encounter==3)
	{
		$grand_tot_paid=$grand_tot_discount=0;
		
		$pat_typ=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$encounter' "));
		$pat_typ_encounter=$pat_typ["p_type"];
?>
		<p id="print_div" style="margin-top: 2%;">
			<b>Detail Account Report from:</b> <?php echo convert_date_g($date1)." to ".convert_date_g($date2); ?>
			<br>
			<b>Department: <?php echo $pat_typ_encounter; ?></b>
			<button class="btn btn-info btn-mini text-right" id="det_excel" onclick="export_excel()"><i class="icon-file"></i> Excel</button>
			<button type="button" class="btn btn-info btn-mini text-right" onclick="print_page('summary_account_detail','<?php echo $date1;?>','<?php echo $date2;?>','<?php echo $encounter;?>','<?php echo $branch_id;?>')" style="margin-right: 1%;"><i class="icon-print icon-large"></i> Print</button>
		</p>
		<table class="table table-bordered table-condensed">
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th colspan="4">Patient Name</th>
					<th>Advance Paid</th>
					<th></th>
					<th></th>
					<th>Date</th>
				</tr>
			</thead>
				<tr>
					<th colspan="10">Advance Payment</th>
				</tr>
<?php
			$n=1;
			$tot_advance_paid=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`='Advance' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ASC ");
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
					<td><?php echo $adv_bal["opd_id"]; ?></td>
					<td colspan="4"><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($advance_paid); ?></td>
					<td></td>
					<td></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_advance_paid+=$advance_paid;
				$grand_tot_paid+=$advance_paid;
			}
?>
			<tr>
				<th colspan="6"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_advance_paid); ?></th>
				<th colspan="3"></th>
			</tr>
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th>Patient Name</th>
					<th>Bill Amount</th>
					<th>Previous Payment</th>
					<th>Discount Amount</th>
					<th>Final Paid</th>
					<th>Balance</th>
					<th>Refund</th>
					<th>Date</th>
				</tr>
			</thead>
				<tr>
					<th colspan="10">Final Payment</th>
				</tr>
<?php
			$n=1;
			$tot_bill_amt=$tot_discount=$tot_final_pay=$tot_refund_amount=$tot_tax_amount=$tot_balance=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`='Final' AND a.`payment_mode`!='Credit' AND b.`type`='$encounter' $user_str_a $branch_str_b ORDER BY a.`pay_id` ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$patient_id=$adv_bal["patient_id"];
				$ipd=$opd_id=$adv_bal["opd_id"];
				
				$pat_show=0;
				$bill_amt=$discount=$final_pay=$refund_amount=$tax_amount=$balance=$prev_pay=0;
				
				$baby_serv_tot=0;
				$baby_ot_total=0;
				$delivery_qry=mysqli_query($link," SELECT * FROM `ipd_pat_delivery_det` WHERE patient_id='$patient_id' and ipd_id='$ipd' ");
				while($delivery_check=mysqli_fetch_array($delivery_qry))
				{
					$baby_tot_serv=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_serv_tot+=$baby_tot_serv["tots"];
					
					// OT Charge Baby
					$baby_ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$delivery_check[baby_uhid]' and ipd_id='$delivery_check[baby_ipd_id]' "));
					$baby_ot_total+=$baby_ot_tot_val["g_tot"];
					
				}
				
				$no_of_days_val=mysqli_fetch_array(mysqli_query($link,"select * from ipd_pat_service_details where patient_id='$patient_id' and ipd_id='$ipd' and `group_id`='141' "));
				$no_of_days=$no_of_days_val["ser_quantity"];
				
				$tot_serv1=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$patient_id' and ipd_id='$ipd' and group_id='141' "));
				$tot_serv_amt1=$tot_serv1["tots"];
				//$tot_serv_amt1=$tot_serv1["tots"]*$no_of_days;
				
				$tot_serv2=mysqli_fetch_array(mysqli_query($link," SELECT ifnull(sum(`amount`),0) as tots FROM `ipd_pat_service_details` WHERE patient_id='$patient_id' and ipd_id='$ipd' and group_id!='141' "));
				$tot_serv_amt2=$tot_serv2["tots"];
				
				// OT Charge
				$ot_tot_val=mysqli_fetch_array(mysqli_query($link,"select ifnull(sum(amount),0) as g_tot from ot_pat_service_details where patient_id='$patient_id' and ipd_id='$ipd' "));
				$ot_total=$ot_tot_val["g_tot"];
				
				// Total
				$bill_amt=$tot_serv_amt1+$tot_serv_amt2+$baby_serv_tot+$ot_total+$baby_ot_total;
				
				$discount=$adv_bal['discount_amount'];
				$tax_amount=$adv_bal['tax_amount'];
				$refund_amount=$adv_bal['refund_amount'];
				$final_pay=$adv_bal['amount'];
				
				$check_refund=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`refund_amount`),0) AS `refund` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd' AND `payment_type`='Refund' $user_str "));
				$refund_amount+=$check_refund['refund'];
				
				$check_paid=mysqli_fetch_array(mysqli_query($link, " SELECT ifnull(SUM(`amount`),0) AS `paid`, ifnull(SUM(`discount_amount`),0) AS `discount`, ifnull(SUM(`refund_amount`),0) AS `refund`, ifnull(SUM(`tax_amount`),0) AS `tax` FROM `payment_detail_all` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd' AND `payment_type`='Advance' $user_str "));
				
				$prev_pay      =$check_paid["paid"];
				
				$balance=($bill_amt-$discount-$tax_amount-$final_pay-$prev_pay+$refund_amount);
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["opd_id"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bill_amt); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($prev_pay); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($final_pay); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($balance); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($refund_amount); ?></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_bill_amt+=$bill_amt;
				$tot_discount+=$discount;
				$grand_tot_discount+=$discount;
				$tot_final_pay+=$final_pay;
				$grand_tot_paid+=$final_pay;
				$tot_refund_amount+=$refund_amount;
				$tot_balance+=$balance;
			}
?>
			<tr>
				<th colspan="3"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_bill_amt); ?></th>
				<th></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_discount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_final_pay); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_balance); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_refund_amount); ?></th>
				<th></th>
			</tr>
			
			<thead class="table_header_fix">
				<tr>
					<th>#</th>
					<th>Bill No</th>
					<th colspan="3">Patient Name</th>
					<th>Discount Amount</th>
					<th>Balance Paid</th>
					<th></th>
					<th></th>
					<th>Date</th>
				</tr>
			</thead>
				<tr>
					<th colspan="10">Balance Payment</th>
				</tr>
<?php
			$n=1;
			$tot_balance_paid=$tot_bal_discount=0;
			$adv_bal_qry=mysqli_query($link, " SELECT a.*, b.`type` FROM `payment_detail_all` a, `uhid_and_opdid` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`date` between '$date1' AND '$date2' AND a.`payment_type`!='Advance' AND b.`type`='$encounter' $user_str_a ORDER BY a.`pay_id` ASC ");
			while($adv_bal=mysqli_fetch_array($adv_bal_qry))
			{
				$pat_show=0;
				$advance_paid=0;
				$bal_discount=0;
				
				$balance_paid=$adv_bal["amount"];
				
				if($adv_bal["discount_amount"]>0)
				{
					$bal_discount=$adv_bal["discount_amount"];
				}
				
				$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$adv_bal[patient_id]'"));
				
				$uhid_id=$pat_info["patient_id"];
				
				$quser=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$adv_bal[user]' "));
				$pat_typ_text=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$adv_bal[type]' "));
				$encounter_name=$pat_typ_text['p_type'];
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $adv_bal["opd_id"]; ?></td>
					<td colspan="3"><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($bal_discount); ?></td>
					<td><?php echo $rupees_symbol.indian_currency_format($balance_paid); ?></td>
					<td></td>
					<td></td>
					<td><?php echo convert_date($adv_bal["date"]); ?></td>
				</tr>
			<?php
				$n++;
				
				$tot_balance_paid+=$balance_paid;
				$grand_tot_paid+=$balance_paid;
				$tot_bal_discount+=$bal_discount;
				$grand_tot_discount+=$bal_discount;
			}
?>
			<tr>
				<th colspan="5"><span class="text-right">Total</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_discount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($tot_balance_paid); ?></th>
				<th colspan="3"></th>
			</tr>
			<tr>
				<th colspan="5"><span class="text-right">Grand Total Amount</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($grand_tot_discount); ?></th>
				<th><?php echo $rupees_symbol.indian_currency_format($grand_tot_paid); ?></th>
				<th colspan="3"></th>
			</tr>
			
			<tr>
				<th colspan="6"><span class="text-right">Net Received Amount</span></th>
				<th><?php echo $rupees_symbol.indian_currency_format($grand_tot_paid-$tot_refund_amount); ?></th>
				<th colspan="3"></th>
			</tr>
		</table>
<?php
	}
?>
